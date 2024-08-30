<?
include_once("../include_files.php");
include_once(PATH_TO_MAIN_LMS_PHYSICAL_LANGUAGE . $language . '/' . LMS_LIST_COURSES_FILENAME);
$template->set_filenames(array(
  'course_homepage' => 'jobseeker/frontend-list-course.htm',
  'course_preview'  => 'jobseeker/course-preview.htm',
));
include_once("../" . FILENAME_BODY);

// request parameters
$action = (isset($_GET['action']) ? $_GET['action'] : '');
$courseID = (isset($_GET['id']) ? tep_db_prepare_input($_GET['id']) : '');
$searchKeyword = (isset($_GET['q']) ? $_GET['q'] : null);
$searchByCatID = (isset($_GET['c']) ? $_GET['c'] : null);
$date = date("Y-m-d H:i:s"); // current date

// pagination variable
$perPage = 12;
if (isset($_GET['page']) AND $_GET['page'] != 0) {
    $page = (int)$_GET['page'];
} else {
    $page = 1;
}
$startAt = $perPage * ($page - 1);


// get user/jobseeker id if logged in
if (check_login('jobseeker')) {
    $jobseeker_id   = $_SESSION['sess_jobseekerid'];
    $user_type = 'jobseeker';
    $jobseeker_name = $_SESSION['sess_jobseekername'];
}





// default currency symbol
$row_cur=getAnyTableWhereData(CURRENCY_TABLE,"code ='".DEFAULT_CURRENCY."'",'symbol_left,symbol_right');
$sym_left=(tep_not_null($row_cur['symbol_left'])?$row_cur['symbol_left'].' ':'');
$sym_rt=(tep_not_null($row_cur['symbol_right'])?' '.$row_cur['symbol_right']:'');

// if id request available then check the course available or not
if (tep_not_null($courseID)) {
    $left_joins = "LEFT JOIN recruiter ON course.recruiter_id = recruiter.recruiter_id
                    LEFT JOIN recruiter_login AS rl ON rl.recruiter_id = course.recruiter_id
                    LEFT JOIN lms_lessons AS lesson ON lesson.lms_course_id = course.id
                    LEFT JOIN  recruiter_users AS ru ON ru.id = course.instructor_id 
                    LEFT JOIN lms_categories AS course_category ON course_category.id = course.lms_category_id";
    
    if (!$course_detail = getAnyTableWhereData(LMS_COURSE_TBL . " AS course $left_joins",
                            "course.id='" . tep_db_input($courseID) . "' AND course.course_is_active = '1' GROUP BY lesson.lms_course_id",
                            "course.*, recruiter.recruiter_company_name, recruiter.recruiter_logo, COUNT(lesson.lms_course_id) AS total_lesson, course_category.category_name, rl.recruiter_email_address, ru.name AS sub_instructor_name"
                        )) {
        $messageStack->add_session(MESSAGE_LMS_COURSE_FIND_ERROR, 'error');
        tep_redirect(LMS_LIST_COURSES_FILENAME);
    }
    $courseID = $course_detail['id'];
    $edit = true;
}


// default value pass
$template->assign_vars(array(
    'update_message' => $messageStack->output(),
 'LEFT_HTML'=>(check_login("recruiter")?LEFT_HTML:(check_login("jobseeker")?LEFT_HTML_JOBSEEKER:'')),

    'menus' => '
        <a 
            class="btn btn-secondary mr-2 float-right" 
            href="' . tep_href_link(PATH_TO_LMS . LMS_LIST_COURSES_FILENAME) . '">
            '.ALL_COURSES.'
        </a>
    ',

    'back_btn' => '
            <a 
                class="btn-link mr-2 float-right" 
                href="' . tep_href_link(PATH_TO_LMS . LMS_LIST_COURSES_FILENAME) . '">
                Back
            </a>
        ',

));



if ($action == 'course-preview' && $courseID) {

    get_course_detail($course_detail);

    $template->pparse('course_preview');
} else {
    $isCourse = get_lms_courses($startAt, $perPage, $searchKeyword, $searchByCatID);

    if ($searchKeyword) {
        $search_heading = $searchKeyword;
    } elseif ($searchByCatID) {
        $search_heading = $searchByCatID;
    } else {
        $search_heading = '<a href="'.tep_href_link(PATH_TO_LMS.LMS_LIST_COURSES_FILENAME).'">'.ALL_COURSES.'</a>';
    }


    $template->assign_vars(array(
        'HEADING_TITLE'         => HEADING_TITLE . ' <span class="badge text-bg-primary">'.paginate_lms_course()['totalData'].'</span>',
        'GET_UNLIMITED'         => GET_UNLIMITED,
        'LEARN_IMPROVE'         => LEARN_IMPROVE,
        'FIND_THE_BEST'         => FIND_THE_BEST,
        'WHAT_DOU_WANT'         => WHAT_DOU_WANT,


        'COURSE_NOT_FOUND'      => ($isCourse) ? null : '<tr><td colspan="10" class="text-center">'.COURSE_NOT_FOUND.'</td></tr>',
        'PAGINATION_LINK'       => paginate_lms_course()['pagination'],
        'category_dropdown'     => category_select_dropdown(),
        'top_categories'        => list_of_top_categories(),
        'SEARCH_HEADING'        => $search_heading,
 'LEFT_HTML'=>(check_login("recruiter")?LEFT_HTML:(check_login("jobseeker")?LEFT_HTML_JOBSEEKER:'')),

    ));
    $template->pparse('course_homepage');
}








// ///////////////////////////////////////////////Functions//////////////////////////////////////////////////

function get_course_detail($course_detail)
{
    global $template;

    $prevCourseDuration = ($course_detail['course_duration']) ? '<i class="bi bi-clock text-success me-2"></i>'.minute_to_hours_convert($course_detail['course_duration']).' Hours' : '';

    if ($course_detail['course_thumbnail']) {
        // $prevCourseThumb = tep_image(FILENAME_IMAGE . "?image_name=" . PATH_TO_UPLOAD_IMAGE . $course_detail['course_thumbnail'] . "", '', '', '', 'class="img-fluid rounded"');
        $prevCourseThumb = '<img src="'.tep_href_link(PATH_TO_UPLOAD_IMAGE . $course_detail['course_thumbnail']).'" 
                                class="img-fluid rounded" data-bs-toggle="modal" data-bs-target="#videoThumb" 
                                alt="'.$course_detail['course_thumbnail'].'" />';
    } else {
        $prevCourseThumb = defaultProfilePhotoUrl($course_detail['course_title'], false, 75);
    }

    if ($course_detail['recruiter_logo']) {
        $company_logo = '<img src="'.tep_href_link(PATH_TO_LOGO . $course_detail['recruiter_logo']).'" alt="'.$course_detail['recruiter_company_name'].'" class="instructor-img-sm me-2" />';
    }else{
        $company_logo = defaultProfilePhotoUrl($course_detail['recruiter_company_name'], true, 35);
    }

    if ($course_detail['course_overview_provider'] == 'youtube' AND $course_detail['course_overview_url']) {
        $videoURL = $course_detail['course_overview_url'];
        $embedURL = str_replace("watch?v=", "embed/", $videoURL);
        
        $overview_tag = '<iframe width="560" height="315" src="'.$embedURL.'" title="'.$course_detail['course_title'].'" allowfullscreen></iframe>';

    } elseif ($course_detail['course_overview_provider'] == 'vimeo' AND $course_detail['course_overview_url']) {
        $videoURL = $course_detail['course_overview_url'];
        $embedURL = str_replace("vimeo.com", "player.vimeo.com/video", $videoURL);
        $overview_tag = '<iframe width="560" height="315" src="'.$embedURL.'" title="'.$course_detail['course_title'].'" 
                            frameborder="0" allow="fullscreen; picture-in-picture" allowfullscreen></iframe>';
    } else{
        // $videoURL = $course_detail['course_overview_url'];
        // $overview_tag = '<video width="320" height="240" controls> <source src="'.$videoURL.'" type="video/mp4"></video>';
        $overview_tag = '';
    }

    $requirement = course_requirements($course_detail['requirement']);

    $query_string1=encode_string("recruiter_email=".$course_detail['recruiter_email_address']."=mail");

    $companyLink = '<a href="'.tep_href_link(FILENAME_JOBSEEKER_COMPANY_DETAILS,'query_string1='.$query_string1).'" target="_new" rel="noopener noreferrer">
                        '.$course_detail['recruiter_company_name'].
                    '</a>';

    if ($course_detail['sub_instructor_name']) {
        $subInstructorDiv = '<div class="col-md-8">
                                <div class="d-flex align-items-center mb-5">
                                    <div>'.defaultProfilePhotoUrl($course_detail['sub_instructor_name'], true, 50).'</div>
                                    <div class="">
                                        <div class="fw-bold" style="font-size: 16px;">'.$course_detail['sub_instructor_name'].'</div>
                                    </div>
                                </div>
                            </div>';
    } else {
        $subInstructorDiv = '';
    }

    $companyDiv = '<div class="col-md-4">
                        <div class="fw-bold" style="font-size: 16px;">Offered By:</div>
                        <div class="d-flex align-items-center mb-5">
                            <div>
                                '.$company_logo.'
                            </div>
                            <div class="">
                                <div>'.$companyLink.'</div>
                            </div>
                        </div>
                    </div>';

    $total_enrollments  = no_of_records(LMS_COURSE_ENROLL_TBL, 'lms_course_id = '.$course_detail['id']);

// default currency symbol
$row_cur=getAnyTableWhereData(CURRENCY_TABLE,"code ='".DEFAULT_CURRENCY."'",'symbol_left,symbol_right');
$sym_left=(tep_not_null($row_cur['symbol_left'])?$row_cur['symbol_left'].' ':'');
$sym_rt=(tep_not_null($row_cur['symbol_right'])?' '.$row_cur['symbol_right']:'');

    $template->assign_vars(array(
        'COURSE_TITLE'         => $course_detail['course_title'],
        'COURSE_PRICE'         => $sym_left.$course_detail['price'].$sym_rt,
        'COURSE_SUMMARY'       => $course_detail['course_summary'],
        'COURSE_CONTENT'       => $course_detail['course_description'],
        'COURSE_OVERVIEW_VID'  => $overview_tag,
        'COURSE_DURATION'      => $prevCourseDuration,
        'COURSE_ENROLLED'      => $total_enrollments,
        'COURSE_THUMB'         => $prevCourseThumb,
        'COURSE_TOTAL_LESSON'  => $course_detail['total_lesson'],
        'COURSE_TOTAL_LESSON'  => $course_detail['total_lesson'],
        'COURSE_REQUIREMENT'   => ($requirement) ? '<h3>Requirements: </h3>'. $requirement: null,
        'COMPANY_LOGO'         => $company_logo,
        'COMPANY_NAME'         => $companyLink,

        // 'sub_instructor_logo'  => $course_detail['sub_instructor_name'] ? defaultProfilePhotoUrl($course_detail['sub_instructor_name'], true, 50) : '',
        // 'sub_instructor_name'  => $course_detail['sub_instructor_name'] ? 'By '.$course_detail['sub_instructor_name']: '',

        'SUB_INSTRUCTOR_DIV'   => $subInstructorDiv,
        'RECRUITER_COMPANY_DIV'=> $companyDiv,

        'ENROLLED_COURSE'      => enroll_course_button_form($course_detail['id']),
        'COURSE_BREADCRUMB'    => '<span class="me-2"><a href="'.tep_href_link(PATH_TO_LMS.LMS_LIST_COURSES_FILENAME).'">Courses</a></span> <span class="me-2">&gt;</span> 
                                <span><a class="text-dark" href="'.tep_href_link(PATH_TO_LMS.LMS_LIST_COURSES_FILENAME,'c='.$course_detail['category_name']).'">'.$course_detail['category_name'].'</a></span>',
        'LESSON_SECTION'       => load_course_lessons($course_detail['id']),
 'LEFT_HTML'=>(check_login("recruiter")?LEFT_HTML:(check_login("jobseeker")?LEFT_HTML_JOBSEEKER:'')),

    
    ));
}


function get_lms_courses(int $offset, int $perPage, $searchKeyword = null, $searchByCategory = null)
{
    global $template, $sym_left, $sym_rt;
    
    $fields = "course.*, category.category_name";
    $tables = "" . LMS_COURSE_TBL . " AS course LEFT JOIN ".LMS_CATEGORY_TBL." AS category ON category.id = course.lms_category_id";

    if ($searchByCategory) {
        $db_raw_query = "SELECT $fields 
                        FROM $tables  
                        WHERE category.category_name LIKE '$searchByCategory' AND course.course_is_active = '1' 
                        ORDER BY course.id DESC LIMIT $offset, $perPage";
    }else {
        $db_raw_query = "SELECT $fields 
                        FROM $tables  
                        WHERE course.course_title LIKE '%$searchKeyword%' AND course.course_is_active = '1' 
                        ORDER BY course.id DESC LIMIT $offset, $perPage";
    }
    
    $query = tep_db_query($db_raw_query);
    
    if (tep_db_num_rows($query) > 0) {
        while ($row_data = tep_db_fetch_array($query)) {

            if ($row_data['course_thumbnail']) {
                // $course_thumb = tep_image(FILENAME_IMAGE . "?image_name=" . PATH_TO_UPLOAD_IMAGE . $row_data['course_thumbnail'] . "", '', '', '', 'class="card-img-top"');
                $course_thumb = '<img src="'.tep_href_link(PATH_TO_UPLOAD_IMAGE . $row_data['course_thumbnail']).'" class="card-img-top" alt="'.$row_data['course_thumbnail'].'" />';
            } else {
                $course_thumb = defaultProfilePhotoUrl($row_data['course_title'], false, 55, 'class="card-img-top"');
            }

            $lms_course_id = tep_db_output($row_data['id']);

            // title
            $titleLimit = (strlen(tep_db_output($row_data['course_title'])) > 40) ? substr(tep_db_output($row_data['course_title']),0,40).'...' : tep_db_output($row_data['course_title']);
            $title = '<a href="'.tep_href_link(PATH_TO_LMS.LMS_LIST_COURSES_FILENAME,'action=course-preview&id='.$lms_course_id).'">'.$titleLimit.'</a>';

            // description
            if (tep_db_output($row_data['course_description'])) {
                $stripTag = strip_tags($row_data['course_description']);
                $summary = (strlen($stripTag) > 55) ? substr($stripTag,0,55).'...' : $stripTag;
            } else {
                $summary = '';
            }

            if (tep_db_output($row_data['price']) == '0.00' OR $row_data['price'] == null) {
                $course_price = 'Free';
            }else {
                $course_price = ''.$sym_left.tep_db_output($row_data['price']).$sym_rt.'';
            }

            
            if ($row_data['course_duration']) {
                $course_duration = '<i class="bi bi-clock text-success me-2"></i>'.minute_to_hours_convert($row_data['course_duration']).' Hours';
            }else {
                $course_duration = '';
            }

            $template->assign_block_vars('lms_courses', array(
                'id'                => $lms_course_id,
                'slug'              => tep_db_output($row_data['slug']),
                'title'             => $title,
                'summary'           => $summary,
                'thumb'             => '<a href="'.tep_href_link(PATH_TO_LMS.LMS_LIST_COURSES_FILENAME,'action=course-preview&id='.$lms_course_id).'">'.$course_thumb.'</a>',
                'price'             => $course_price,
                'course_duration'   => $course_duration,
                'requirement'       => tep_db_output($row_data['requirement']),
                'created_at'        => tep_date_short($row_data['created_at']),
            ));
        }
        tep_db_free_result($query);
        return true;
    }

    return false;
}

function paginate_lms_course() {
    global $perPage, $page;

    $countRow = "SELECT COUNT(*) AS total FROM ". LMS_COURSE_TBL . " WHERE lms_courses.course_is_active = '1'";

    $result = tep_db_query($countRow);

    if (tep_db_num_rows($result) > 0) {
        $total_row = tep_db_fetch_array($result)['total'];
    }
    
    $total_page = ceil($total_row / $perPage);

    $prevURL = ($page <= 1) ? '#' : tep_href_link(PATH_TO_LMS.LMS_LIST_COURSES_FILENAME, 'page='.($page - 1).'');
    $prevDisabled = ($page <= 1) ? 'disabled' : '';
    
    $nextURL = ($page >= $total_page) ? '#' : tep_href_link(PATH_TO_LMS.LMS_LIST_COURSES_FILENAME, 'page='.($page + 1).'');
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


function load_course_lessons($course_id) {
    $cardDiv = '<div class="card card-custom mb-5"><div class="card-body"><h3 class="fw-bold mb-4">Course Syllabus </h3>';

    $where_clause = "WHERE section.lms_course_id = $course_id";
    $db_raw_query = "SELECT section.* 
                    FROM " . LMS_SECTION_TBL . " AS section 
                    $where_clause
                    ORDER BY section.id ASC";

    $query = tep_db_query($db_raw_query);

    if (tep_db_num_rows($query) > 0) {
        $key = 0;
        while ($row_data = tep_db_fetch_array($query)) {
            $key++;
            $lessons = '';
            if (count(load_section_lessons($row_data['id'], $course_id)) > 0) {
                $lessonArray = load_section_lessons($row_data['id'], $course_id);
                foreach ($lessonArray as $lesson) {
                    $lessons .= '<li>'.$lesson['lesson_name'].'</li>';
                }
            }
            
            $cardDiv .= '<div class="mb-4">
                            <p class="text-success fw-bold mb-2">'.$row_data['section_name'].'</p>
                            <!-- <h4>'.$row_data['section_name'].'</h4> -->
                            <ul style="font-size: 1rem;font-weight: 500;list-style-type:auto;margin: 0 0 0 21px;">'.$lessons.'</ul>
                        </div>';
        }
        tep_db_free_result($query);
    }
 
    $cardDiv .= "</div></div>";

    return $cardDiv;
}

function load_section_lessons($section_id, $course_id) {
    $output = array();

    
    $where_clause = "WHERE lesson.lms_section_id =$section_id AND lesson.lms_course_id = $course_id";
    $db_raw_query = "SELECT lesson.* 
                        FROM " . LMS_LESSON_TBL . " AS lesson 
                        $where_clause 
                        ORDER BY lesson.id ASC";

    $query = tep_db_query($db_raw_query);

    if (tep_db_num_rows($query) > 0) {
        while ($row_data = tep_db_fetch_array($query)) {
            $item = [
                'id'           => $row_data['id'],
                'lesson_name'  => $row_data['lesson_name'],
                'is_preview'   => $row_data['is_preview'],
                'lms_section_id'=> $row_data['lms_section_id'],
                'created_at'   => tep_date_short($row_data['created_at']),
            ];

            array_push($output, $item);
        }
        tep_db_free_result($query);
    }

    return $output;
}



function minute_to_hours_convert($minutes)
{
    $hours = intdiv($minutes, 60).':'. ($minutes % 60);

    return $hours;
}

function course_requirements($strValue, $separator = ',')
{
    if ($strValue) {
        $string_with_comma = $strValue;
        $requirementArr = explode($separator, $string_with_comma);

        $data = '';

        foreach ($requirementArr as $requirement) {
            $data .= '<span class="badge bg-secondary me-1">'.$requirement.'</span>';
        }

        $requirements = $data;
    }else {
        $requirements = null;
    }

    return $requirements;
}

function enroll_course_button_form($course_id) {
    global $jobseeker_id;

    // encode course id
    $encodeCourseID =encode_string("course_id==".$course_id."==enrolled");

    $courseIsEnrolled = is_course_enrolled($course_id, $jobseeker_id);
    if ($courseIsEnrolled['is_enrolled'] == 'yes') {
        $course_enrolled = '<a class="btn btn-lg py-2 btn-outline-primary btn-100" href="'.tep_href_link(PATH_TO_LMS.LMS_START_COURSE_FILENAME,"action=_s&id=$encodeCourseID").'">
                                <i class="bi bi-play-fill"></i> Start Lesson
                            </a>';
    } else {
        // $onclickEvent = "event.preventDefault();if(confirm('Are you sure!')){document.getElementById('form-enroll-$encodeCourseID').submit()}";
        $onclickEvent = "event.preventDefault();document.getElementById('form-enroll-$encodeCourseID').submit()";
        
        $course_enrolled = '<a class="btn btn-lg py-2 btn-outline-primary btn-100" href="#" onclick="'.$onclickEvent.'">
                                <i class="bi bi-play-fill"></i> Enroll Now
                            </a>
                            <form style="display:none" 
                                method="post" 
                                id="form-enroll-'.$encodeCourseID.'"
                                action="' . tep_href_link(PATH_TO_LMS.LMS_COURSE_ENROLL_FILENAME, 'action=enrolled&id=' . $encodeCourseID) . '">
                            </form>';
    }


    return $course_enrolled;
}

function is_course_enrolled($course_id, $jobseeker_id)
{
    $courseIsEnrolled = null;
    if ($course_id AND $jobseeker_id) {
        // if course is already enrolled then remove the enroll button and add start lesson button
        $field_column = "COUNT(*) AS total, CASE WHEN COUNT(*) = 1 THEN 'yes' ELSE 'no' END AS is_enrolled";
        $courseIsEnrolled = getAnyTableWhereData(LMS_COURSE_ENROLL_TBL." AS enroll", "enroll.lms_course_id = $course_id AND enroll.jobseeker_id = $jobseeker_id", $field_column);
        return $courseIsEnrolled;
    }

    return $courseIsEnrolled;
}

function category_select_dropdown()
{
    $all_course_url = "'".tep_href_link(PATH_TO_LMS.LMS_LIST_COURSES_FILENAME)."'"; 
    $selectDiv = '<form><select name="c" class="form-select form-select-sm" style="width:175px;" onchange="this.form.submit()"><option value="0">Select Course</option>';
    $selectDiv .= '<option value="" onclick="location.href = '.$all_course_url.'">All Course</option>';

    $data = get_list_of_category();

    if (count($data) > 0) {
        foreach ($data as $key => $row_data) {
            $selectDiv .= '<option value="'.$row_data['category_name'].'">'.$row_data['category_name'].'</option>';
        }
    }

    $selectDiv .= "</form></select>";

    return $selectDiv;
}

function list_of_top_categories()
{
    $all_course_url = "'".tep_href_link(PATH_TO_LMS.LMS_LIST_COURSES_FILENAME)."'"; 
    $divRow = '<div class="row row-cols-1 row-cols-md-4 g-4 text-center">';

    $data = get_list_of_category();

    if (count($data) > 0) {
        foreach ($data as $key => $row_data) {
            if ($row_data['category_img']) {
                $img = '<img class="py-3" src="'.tep_href_link(PATH_TO_UPLOAD_IMAGE . $row_data['category_img']).'" width="55" alt="category_img_'.$key.'">';
            }else{
                $img = defaultProfilePhotoUrl($row_data['category_name'], false, 75);
            }

            $divRow .= '<div class="col" id="cat-'.$key.'"><div class="card border-0 text-center lms-card">
                        '.$img.'
                    <h4 class="article_title"><a href="'.tep_href_link(PATH_TO_LMS.LMS_LIST_COURSES_FILENAME,'c='.$row_data['category_name']).'">'.$row_data['category_name'].'</h4></a>
			</div></div>';
        }
    }

    $divRow .= "</div>";

    return $divRow;
}

function get_list_of_category()
{
    $output = array();

    // SELECT 
    // FROM `lms_categories` AS category
    // INNER JOIN lms_courses AS course ON course.lms_category_id = category.id
    // WHERE category.category_is_active = '1' AND course.course_is_active = '1'
    // GROUP BY course.lms_category_id, category.id;

    $fields = "category.id, category.category_name, category.category_img, COUNT(course.lms_category_id) AS total_course";
    $tables = "lms_categories AS category INNER JOIN lms_courses AS course ON course.lms_category_id = category.id";

    $db_raw_query = "SELECT $fields FROM $tables 
                    WHERE category.category_is_active = '1' AND course.course_is_active = '1'
                    GROUP BY course.lms_category_id, category.id 
                    ORDER BY category.id DESC";

    $query = tep_db_query($db_raw_query);

    if (tep_db_num_rows($query) > 0) {
        $key = 0;
        while ($row_data = tep_db_fetch_array($query)) {
            $key++;
            $item = [
                'id'            => $row_data['id'],
                'category_name' => $row_data['category_name'],
                'category_img'  => $row_data['category_img'],
                'created_at'    => tep_date_short($row_data['created_at']),
            ];
            array_push($output, $item);
        }
        tep_db_free_result($query);
    }

    return $output;
}

?>