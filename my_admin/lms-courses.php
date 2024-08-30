<?php
// Initial Setup
include_once("../include_files.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE . $language . '/' . LMS_ADMIN_COURSES);
$template->set_filenames(
    array(
        'course_lists'   => 'lms/courses/list-course.htm',
        'course_report'  => 'lms/courses/course-report.htm',
    )
);

include_once(FILENAME_ADMIN_BODY);

// request parameters
$action = (isset($_GET['action']) ? $_GET['action'] : '');
$courseID = (isset($_GET['id']) ? tep_db_prepare_input($_GET['id']) : '');
$date = date("Y-m-d H:i:s"); // current date

// pagination variable
$perPage = 10;
if (isset($_GET['page']) AND $_GET['page'] != 0) {
    $page = (int)$_GET['page'];
} else {
    $page = 1;
}
$startAt = $perPage * ($page - 1);


// default currency symbol
$row_cur=getAnyTableWhereData(CURRENCY_TABLE,"code ='".DEFAULT_CURRENCY."'",'symbol_left,symbol_right');
$sym_left=(tep_not_null($row_cur['symbol_left'])?$row_cur['symbol_left'].' ':'');
$sym_rt=(tep_not_null($row_cur['symbol_right'])?' '.$row_cur['symbol_right']:'');


// if rowID not null check is data available
if (tep_not_null($courseID)) {
    if (!$course_detail = getAnyTableWhereData(LMS_COURSE_TBL, "id='" . tep_db_input($courseID) . "'")) {
        $messageStack->add_session(MESSAGE_LMS_COURSE_FIND_ERROR, 'error');
        tep_redirect(LMS_COURSES_FILENAME);
    }
    $courseID = $course_detail['id'];
    $edit = true;
}





// default value pass
$template->assign_vars(array(
    'update_message' => $messageStack->output(),
    
    'new_button' => ' ',

    'menus' => ' ',

    'back_btn' => '
            <a 
                class="btn-link mr-2 float-right" 
                href="' . tep_href_link(PATH_TO_ADMIN . LMS_ADMIN_COURSES) . '">
                Back
            </a>
        ',
));



if (($action == 'course_active' || $action == 'course_inactive') AND $courseID AND $_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($course_detail['course_is_active'] == 1) {
        $status = 0;
    } else {
        $status = 1;
    }

    $updateCourseStatus = [
        'course_is_active' => $status,
        'updated_at'       => $date,
    ];

    tep_db_perform(LMS_COURSE_TBL, $updateCourseStatus, 'update', "id='" . $courseID . "'");

    $messageStack->add_session(MESSAGE_STATUS_UPDATED, 'success');

    return tep_redirect(LMS_ADMIN_COURSES);
}



if ($action == 'report') {
    $data = reportBox();

    $template->assign_vars(array(
        'REPORT_BOX_TITLE'  => HEAD_REPORT,
        'TOTAL_COURSES'     => TOTAL_COURSES,
        'TOTAL_LESSONS'     => TOTAL_LESSONS,
        'TOTAL_ENROLLMENT'  => TOTAL_ENROLLMENT,
        'TOTAL_STUDENT'     => TOTAL_STUDENT,

        'total_courses'     => $data['total_courses'],
        'total_lessons'     => $data['total_lessons'],
        'total_enrollments' => $data['total_enrollments'],
        'total_students'    => $data['total_students'],
    ));

    $template->pparse('course_report');
} else {
    $isCourse = get_all_courses($startAt, $perPage);

    $template->assign_vars(array(
        'HEADING_TITLE'         => HEADING_TITLE . ' <span class="badge badge-info">'.pagination_lms_course()['totalData'].'</span>',

        'TH_TITLE'              => TH_TITLE,
        'TH_DATE'               => TH_DATE,
        'TH_ACTION'             => TH_ACTION,
        'TH_CATEGORY'           => TH_CATEGORY,
        'TH_LESSON_SECTION'     => TH_LESSON_SECTION,
        'TH_ENROLLED_STUDENT'   => TH_ENROLLED_STUDENT,
        'TH_STATUS'             => TH_STATUS,
        'TH_PRICE'              => TH_PRICE,
        'TH_RECRUITER'          => TH_RECRUITER,
        'COURSE_NOT_FOUND'      => ($isCourse) ? null : '<tr><td colspan="10" class="text-center">course not found</td></tr>',
        'PAGINATION_LINK'       => pagination_lms_course()['pagination'],
    ));

    $template->pparse('course_lists');
}



// ///////////////////////////////// functions //////////////////////////////////////////

// get all categories
function get_all_courses(int $offset, int $perPage)
{
    global $template, $sym_left, $sym_rt;

    $db_raw_query = "SELECT course.*, category.category_name, COUNT(e.lms_course_id) AS enrolled_student, 
                            CONCAT(recruiter.recruiter_first_name,' ',recruiter.recruiter_last_name) AS recruiter_name,
                            subuser.name AS instructor_name
                    FROM " . LMS_COURSE_TBL . " AS course
                    LEFT JOIN ".LMS_CATEGORY_TBL." AS category ON category.id = course.lms_category_id
                    LEFT JOIN ".RECRUITER_TABLE." ON recruiter.recruiter_id = course.recruiter_id
                    LEFT JOIN recruiter_users AS subuser ON subuser.id = course.instructor_id
                    LEFT JOIN ".LMS_COURSE_ENROLL_TBL." AS e ON e.lms_course_id = course.id
                    GROUP BY course.id, e.lms_course_id
                    ORDER BY course.id DESC
                    LIMIT $offset, $perPage";

    $query = tep_db_query($db_raw_query);

    if (tep_db_num_rows($query) > 0) {
        while ($row_data = tep_db_fetch_array($query)) {

            if ($row_data['course_thumbnail']) {
                $course_thumb = tep_image(FILENAME_IMAGE . "?image_name=" . PATH_TO_UPLOAD_IMAGE . $row_data['course_thumbnail'] . "&size=50", '', '', '', 'align="center" class="course-thumb rounded"');
            } else {
                $course_thumb = defaultProfilePhotoUrl($row_data['course_title'], false, 50);
            }

            if (tep_db_output($row_data['price']) == '0.00' OR $row_data['price'] == null) {
                $course_price = '<span class="badge badge-success">Free</span>';
            }else {
                $course_price = '<span class="badge badge-secondary">'.$sym_left.tep_db_output($row_data['price']).$sym_rt.'</span>';
            }

            $title = '<a class="" href="'.tep_href_link(PATH_TO_LMS.LMS_LIST_COURSES_FILENAME,'action=course-preview&id='.$row_data['id']).'">
                        '.tep_db_output($row_data['course_title']).'</a>';

            $template->assign_block_vars('lms_courses', array(
                'id'            => tep_db_output($row_data['id']),
                'slug'          => tep_db_output($row_data['slug']),
                'title'         => $title,
                'course_creator'=> $row_data['recruiter_name'],
                'instructor_name'=> tep_db_output($row_data['instructor_name']) ? $row_data['instructor_name'] : '',
                'category'      => tep_db_output($row_data['category_name']),
                'total_section' => count_records_for_course($row_data['id'])[0]['total'],
                'total_lessons' => count_records_for_course($row_data['id'])[1]['total'],
                'enrolled'      => tep_db_output($row_data['enrolled_student']),
                'price'         => $course_price,
                'status'        => course_active_inactive_btn($row_data['course_is_active'], $row_data['id']),
                'thumb'         => $course_thumb,
                'created_at'    => tep_date_short($row_data['created_at']),
            ));
        }
        tep_db_free_result($query);
        return true;
    }

    return false;
}


// course active inactive btn
function course_active_inactive_btn($courseStatus, $courseID)
{
    $onclickEvent = "event.preventDefault();if(confirm('Are you sure!')){document.getElementById('status-$courseID').submit()}";

    if ($courseStatus == 1) {
        $courseAction = 'course_inactive';
        $btnName = 'Active';
    }else {
        $courseAction = 'course_active';
        $btnName = 'Inactive';
    }

    $course_staus_btn_link = '<a class="" href="#" onclick="'.$onclickEvent.'">
                                    '.$btnName.'
                                </a>
                                <form style="display:none" 
                                    method="post" 
                                    id="status-'.$courseID.'"
                                    action="' . tep_href_link(PATH_TO_ADMIN . LMS_ADMIN_COURSES, 'action='.$courseAction . '&id='.$courseID) .'">
                                </form>
                                ';

    return $course_staus_btn_link;
}


// pagination for categories
function pagination_lms_course() {
    global $perPage, $page;

    $countRow = "SELECT COUNT(*) AS total FROM ". LMS_COURSE_TBL . "";

    $result = tep_db_query($countRow);

    if (tep_db_num_rows($result) > 0) {
        $total_row = tep_db_fetch_array($result)['total'];
    }
    
    $total_page = ceil($total_row / $perPage);

    $prevURL = ($page <= 1) ? '#' : tep_href_link(PATH_TO_ADMIN.LMS_ADMIN_COURSES, 'page='.($page - 1).'');
    $prevDisabled = ($page <= 1) ? 'disabled' : '';
    
    $nextURL = ($page >= $total_page) ? '#' : tep_href_link(PATH_TO_ADMIN.LMS_ADMIN_COURSES, 'page='.($page + 1).'');
    $nextDisabled = ($page >= $total_page) ? 'disabled' : '';

    $paginate_link = '<nav aria-label="Page navigation example">
        <ul class="pagination justify-content-center">
            <li class="page-item '.$prevDisabled.'">
                <a class="page-link" href="'.$prevURL.'">Previous</a>
            </li>
            <li class="page-item '.$nextDisabled.'">
                <a class="page-link" href="'.$nextURL.'">Next</a>
            </li>
        </ul>
    </nav>';

    return [
        'pagination' => ($total_row <= $perPage) ? '' : $paginate_link,
        'totalData'  => $total_row,
    ];
}

// count records for employer
function count_records_for_course($course_id)
{
    $total_sections = no_of_records(LMS_SECTION_TBL, "lms_course_id = $course_id");
    $total_lessons = no_of_records(LMS_LESSON_TBL, "lms_course_id = $course_id");

    $reportData = [
        ['title' => 'Sections', 'total' => $total_sections],
        ['title' => 'Lessons', 'total' => $total_lessons],
    ];

    return $reportData;
}

function reportBox() {
    $total_courses      = no_of_records(LMS_COURSE_TBL, "recruiter_id IS NOT NUll");
    $total_lessons      = no_of_records(LMS_LESSON_TBL, "recruiter_id IS NOT NUll");
    $total_enrollments  = no_of_records(LMS_COURSE_ENROLL_TBL, "lms_course_id IS NOT NUll");
    $total_students     = no_of_records(LMS_COURSE_ENROLL_TBL." AS student", "student.jobseeker_id IS NOT NULL","DISTINCT(student.jobseeker_id)");


    $data = [
                'total_courses'     => $total_courses,
                'total_lessons'     => $total_lessons,
                'total_enrollments' => $total_enrollments,
                'total_students'    => $total_students,
            ];

    return $data;
}
?>