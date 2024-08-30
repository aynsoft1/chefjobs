<?
include_once("../include_files.php");
include_once(PATH_TO_MAIN_LMS_PHYSICAL_LANGUAGE . $language . '/' . LMS_COURSE_ENROLL_FILENAME);
include_once("../" . FILENAME_BODY);

// request parameters
$action = (isset($_GET['action']) ? $_GET['action'] : '');
$courseID = (isset($_GET['id']) ? tep_db_prepare_input($_GET['id']) : '');
$date = date("Y-m-d H:i:s"); // current date

// check condition member is logged in or not if jobseeker is not logged in then redirect to login page otherwise to next request
if (!check_login('jobseeker') && tep_not_null($action)) {
    // decode course id
    $decodedCourseID=check_data($courseID,"==","course_id","enrolled");
    // $_SESSION['REDIRECT_URL'] = $_SERVER['REQUEST_URI'];
    $redirect_back_url = '/'.PATH_TO_MAIN.PATH_TO_LMS.LMS_LIST_COURSES_FILENAME.'?action=course-preview&id='.$decodedCourseID;
    $_SESSION['REDIRECT_URL'] = $redirect_back_url;
    $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
    tep_redirect(getPermalink(FILENAME_JOBSEEKER_LOGIN));
}

// get user/jobseeker id if logged in
if (check_login('jobseeker')) {
    $jobseeker_id   = $_SESSION['sess_jobseekerid'];
    $user_type = 'jobseeker';
    $jobseeker_name = $_SESSION['sess_jobseekername'];
}

// if id request available then check the course available or not
if (tep_not_null($courseID)) {
    $decodedCourseID=check_data($courseID,"==","course_id","enrolled");

    if (!$course_detail = getAnyTableWhereData(LMS_COURSE_TBL. ' AS course', "course.id='" . tep_db_input($decodedCourseID) . "' AND course.course_is_active = '1'")) {
        $messageStack->add_session(MESSAGE_COURSE_NOT_FOUND_ERR, 'error');
        tep_redirect(LMS_LIST_COURSES_FILENAME);
    }

    $courseID = $course_detail['id'];
    $edit = true;
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' AND $action == 'enrolled' AND $courseID AND $jobseeker_id) {

    // first check condition for course is enrolled or not
    // if course is enrolled then redirect back to my_courses
    
    $courseIsEnrolled = is_course_enrolled($course_id, $jobseeker_id);

    if ($courseIsEnrolled['is_enrolled'] == 'yes') {
        $messageStack->add_session(MESSAGE_COURSE_ENROLLED, 'error');
        tep_redirect(LMS_MY_COURSES_FILENAME);
    }

    if ($courseID AND $jobseeker_id) {
        $data = [
            'lms_course_id' => $courseID,
            'jobseeker_id'  => $jobseeker_id,
            'created_at'    => $date,
            'updated_at'    => $date,
        ];

        // store data in pivot table
        tep_db_perform(LMS_COURSE_ENROLL_TBL, $data);
        $messageStack->add_session(MESSAGE_SUCCESS_ENROLLED, 'success');

    }else{
        $messageStack->add_session(MESSAGE_ERROR_ENROLLED, 'error');
    }

    tep_redirect(LMS_MY_COURSES_FILENAME);
}


function is_course_enrolled($course_id, $jobseeker_id)
{
    // SELECT COUNT(*) AS total, CASE WHEN COUNT(*) = 1 THEN "yes" ELSE "no" END AS is_enrolled FROM lms_course_enrolled AS enroll 
    // WHERE enroll.lms_course_id = 2 AND enroll.jobseeker_id = 13613;

    $courseIsEnrolled = null;
    if ($course_id AND $jobseeker_id) {
        // if course is already enrolled then remove the enroll button and add start lesson button
        $field_column = "COUNT(*) AS total, CASE WHEN COUNT(*) = 1 THEN 'yes' ELSE 'no' END AS is_enrolled";
        $courseIsEnrolled = getAnyTableWhereData(LMS_COURSE_ENROLL_TBL." AS enroll", "enroll.lms_course_id = $course_id AND enroll.jobseeker_id = $jobseeker_id", $field_column);
        return $courseIsEnrolled;
    }

    return $courseIsEnrolled;
}