<?
include_once("../include_files.php");
include_once(PATH_TO_MAIN_LMS_PHYSICAL_LANGUAGE . $language . '/' . LMS_MY_COURSES_FILENAME);
$template->set_filenames(array(
  'my_course' => 'jobseeker/my-courses.htm',
));
include_once("../" . FILENAME_BODY);

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
$sym_left=(tep_not_null($row_cur['symbol_left'])?$row_cur['symbol_left'].'':'');
$sym_rt=(tep_not_null($row_cur['symbol_right'])?' '.$row_cur['symbol_right']:'');





// check condition member is logged in or not if jobseeker is not logged in then redirect to login page otherwise to next request
if (!check_login('jobseeker')) {
    $_SESSION['REDIRECT_URL'] = $_SERVER['REQUEST_URI'];
    $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
    tep_redirect(getPermalink(FILENAME_JOBSEEKER_LOGIN));
}

// get user/jobseeker id if logged in
if (check_login('jobseeker')) {
    $jobseeker_id   = $_SESSION['sess_jobseekerid'];
    $user_type      = 'jobseeker';
    $jobseeker_name = $_SESSION['sess_jobseekername'];
}

// if id request available then check the course available or not
if (tep_not_null($courseID)) {

    $decodedCourseID=check_data($courseID,"==","course_id","enrolled");

    if (!$course_detail = getAnyTableWhereData(LMS_COURSE_TBL, "id='" . tep_db_input($decodedCourseID) . "'")) {
        $messageStack->add_session(MESSAGE_COURSE_NOT_FOUND_ERR, 'error');
        tep_redirect(LMS_LIST_COURSES_FILENAME);
    }

    $courseID = $course_detail['id'];
    $edit = true;
}


// default value pass
$template->assign_vars(array(
    'update_message' => $messageStack->output(),

    'menus' => '
        <a 
            class="btn btn-link mr-2 float-right" 
            href="' . tep_href_link(PATH_TO_LMS . LMS_LIST_COURSES_FILENAME) . '">
            Courses
        </a>
    ',
));


if ($action == 'all-courses') {
    
} else {
    $isCourse = get_my_courses($startAt, $perPage);

    $template->assign_vars(array(
        'HEADING_TITLE'         => '<h1>'.HEADING_TITLE . '</h1> <span class="badge small bg-secondary ms-2">'.paginate_lms_enrolled_course()['totalData'].'</span>',
        'COURSE_NOT_FOUND'      => ($isCourse) ? null : '<tr><td colspan="10" class="text-center">course not found</td></tr>',
        'PAGINATION_LINK'       => paginate_lms_enrolled_course()['pagination'],
		 'LEFT_HTML'=>(check_login("recruiter")?LEFT_HTML:(check_login("jobseeker")?LEFT_HTML_JOBSEEKER:'')),

        
    ));
    $template->pparse('my_course');
}


function get_my_courses(int $offset, int $perPage)
{
    global $template, $sym_left, $sym_rt, $jobseeker_id;

    // SELECT 
    // FROM lms_course_enrolled AS my_course
    // INNER JOIN lms_courses ON lms_courses.id = my_course.lms_course_id
    // WHERE my_course.jobseeker_id = 13613;
    
    $fields = "my_course.id AS enroll_id, my_course.lms_course_id, my_course.jobseeker_id AS jobseeker_id, lms_courses.*";

    $db_raw_query = "SELECT $fields 
                    FROM " . LMS_COURSE_ENROLL_TBL . " AS my_course 
                    INNER JOIN ".LMS_COURSE_TBL." ON lms_courses.id = my_course.lms_course_id 
                    WHERE my_course.jobseeker_id = $jobseeker_id
                    ORDER BY my_course.id DESC
                    LIMIT $offset, $perPage";
    
    $query = tep_db_query($db_raw_query);
    
    if (tep_db_num_rows($query) > 0) {
        while ($row_data = tep_db_fetch_array($query)) {

            if ($row_data['course_thumbnail']) {
                // $course_thumb = tep_image(FILENAME_IMAGE . "?image_name=" . PATH_TO_UPLOAD_IMAGE . $row_data['course_thumbnail'] . "", '', '', '', 'class="card-img-top"');
                $course_thumb = '<img src="'.tep_href_link(PATH_TO_UPLOAD_IMAGE . $row_data['course_thumbnail']).'" class="card-img-top" alt="'.$row_data['course_thumbnail'].'" />';
            } else {
                $course_thumb = defaultProfilePhotoUrl($row_data['course_title'], false, 75);
            }

            $lms_course_id = tep_db_output($row_data['id']);

            // encode course id
            $encodeCourseID =encode_string("course_id==".$lms_course_id."==enrolled");
            $startCourseLink = tep_href_link(PATH_TO_LMS.LMS_START_COURSE_FILENAME,"action=_s&id=$encodeCourseID");

            // title
            $titleLimit = (strlen(tep_db_output($row_data['course_title'])) > 43) ? substr(tep_db_output($row_data['course_title']),0,43).'...' : tep_db_output($row_data['course_title']);
            $title = '<a href="'.$startCourseLink.'">'.$titleLimit.'</a>';

            // description
            if (tep_db_output($row_data['course_description'])) {
                $stripTag = strip_tags($row_data['course_description']);
                $summary = (strlen($stripTag) > 85) ? substr($stripTag,0,85).'...' : $stripTag;
            } else {
                $summary = '';
            }

            if (tep_db_output($row_data['price']) == '0.00' OR $row_data['price'] == null) {
                $course_price = '<h3 class="fw-bold">Free</h3>';
            }else {
                $course_price = '<h3 class="fw-bold">'.$sym_left.tep_db_output($row_data['price']).$sym_rt.'</h3>';
            }

            
            if ($row_data['course_duration']) {
                $course_duration = '<i class="bi bi-clock text-success me-2"></i>'.minute_to_hours_convert($row_data['course_duration']).' Hours';
            }else {
                $course_duration = '';
            }


            $template->assign_block_vars('my_courses', array(
                'id'                => $lms_course_id,
                'slug'              => tep_db_output($row_data['slug']),
                'title'             => $title,
                'summary'           => $summary,
                'thumb'             => $course_thumb,
                'price'             => $course_price,
                'course_duration'   => $course_duration,
                'requirement'       => tep_db_output($row_data['requirement']),
                'created_at'        => tep_date_short($row_data['created_at']),
                'start_course_btn'  => '<a href="'.$startCourseLink.'" class="btn btn-primary">'.START_COURSE_BTN_NAME.'</a>'
            ));
        }
        tep_db_free_result($query);
        return true;
    }

    return false;
}

function paginate_lms_enrolled_course() 
{
    global $perPage, $page, $jobseeker_id;

    $countRow = "SELECT COUNT(*) AS total FROM " . LMS_COURSE_ENROLL_TBL . " AS my_course 
                INNER JOIN ".LMS_COURSE_TBL." ON lms_courses.id = my_course.lms_course_id 
                WHERE my_course.jobseeker_id = $jobseeker_id";

    $result = tep_db_query($countRow);

    if (tep_db_num_rows($result) > 0) {
        $total_row = tep_db_fetch_array($result)['total'];
    }
    
    $total_page = ceil($total_row / $perPage);

    $prevURL = ($page <= 1) ? '#' : tep_href_link(PATH_TO_LMS.LMS_MY_COURSES_FILENAME, 'page='.($page - 1).'');
    $prevDisabled = ($page <= 1) ? 'disabled' : '';
    
    $nextURL = ($page >= $total_page) ? '#' : tep_href_link(PATH_TO_LMS.LMS_MY_COURSES_FILENAME, 'page='.($page + 1).'');
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


function minute_to_hours_convert($minutes)
{
    $hours = intdiv($minutes, 60).':'. ($minutes % 60);

    return $hours;
}


?>