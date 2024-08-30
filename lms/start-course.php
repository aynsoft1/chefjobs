<?
include_once("../include_files.php");
include_once(PATH_TO_MAIN_LMS_PHYSICAL_LANGUAGE . $language . '/' . LMS_START_COURSE_FILENAME);
$template->set_filenames(array(
    'start_course' => 'jobseeker/start-course.htm',
  ));
include_once("../" . FILENAME_BODY);

// request parameters
$action = (isset($_GET['action']) ? $_GET['action'] : '');
$courseID = (isset($_GET['id']) ? tep_db_prepare_input($_GET['id']) : '');
$lessonID = (isset($_GET['_lesson']) ? tep_db_prepare_input($_GET['_lesson']) : '');
$date = date("Y-m-d H:i:s"); // current date

// check condition member is logged in or not if jobseeker is not logged in then redirect to login page otherwise to next request
if (!check_login('jobseeker') && tep_not_null($action)) {
    // decode course id
    $decodedCourseID=check_data($courseID,"==","course_id","enrolled");
    // $_SESSION['REDIRECT_URL'] = $_SERVER['REQUEST_URI'];
    $redirect_back_url = '/'.PATH_TO_MAIN.PATH_TO_LMS.LMS_LIST_COURSES_FILENAME;
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

    if (!$course_detail = getAnyTableWhereData(LMS_COURSE_TBL, "id='" . tep_db_input($decodedCourseID) . "'")) {
        $messageStack->add_session(MESSAGE_COURSE_NOT_FOUND_ERR, 'error');
        tep_redirect(LMS_LIST_COURSES_FILENAME);
    }

    $courseID = $course_detail['id'];
    $edit = true;
}


if ($_SERVER['REQUEST_METHOD'] == 'GET' AND $action == '_s' AND $courseID AND $jobseeker_id) {

    $courseIsEnrolled = is_course_enrolled($courseID, $jobseeker_id);
    if ($courseIsEnrolled['is_enrolled'] == 'no') {
        $messageStack->add_session(MESSAGE_COURSE_NOT_ENROLL_ERR, 'error');
        tep_redirect(LMS_MY_COURSES_FILENAME);
    }

    if ($lessonID) {
        $decodedLessonID = check_data($lessonID,"==","lesson_id","enrolled");
        $data = get_lesson_data($courseID, $decodedLessonID);
        course_lesson_data($data);
    }else{
        // go to course preview
        // course_detail_data($course_detail);

        // go to the course first lesson
        $first_lesson = get_section_first_lesson($courseID);
        if ($first_lesson) {
            course_lesson_data($first_lesson);
        }else{
            $messageStack->add_session('No section and lesson added currently', 'error');
            tep_redirect(tep_href_link(PATH_TO_LMS.LMS_MY_COURSES_FILENAME));
            // print_r('No section and lesson added currently');exit;
        }
    }

    $template->pparse('start_course');
}


function course_detail_data($course_detail)
{
    global $template;

    if ($course_detail['course_thumbnail']) {
        // $prevCourseThumb = tep_image(FILENAME_IMAGE . "?image_name=" . PATH_TO_UPLOAD_IMAGE . $course_detail['course_thumbnail'] . "", '', '', '', 'class="img-fluid rounded"');
        $prevCourseThumb = '<img src="'.tep_href_link(PATH_TO_UPLOAD_IMAGE . $course_detail['course_thumbnail']).'" class="img-fluid rounded" alt="'.$course_detail['course_thumbnail'].'" />';
    } else {
        $prevCourseThumb = defaultProfilePhotoUrl($course_detail['course_title'], false, 75);
    }

    if ($course_detail['course_overview_provider'] == 'youtube' AND $course_detail['course_overview_url']) {
        $videoURL = $course_detail['course_overview_url'];
        $embedURL = str_replace("watch?v=", "embed/", $videoURL);

        $overview_tag = '<iframe width="100%" height="375" src="'.$embedURL.'" title="'.$course_detail['course_title'].'" allowfullscreen></iframe>';

    } elseif ($course_detail['course_overview_provider'] == 'vimeo' AND $course_detail['course_overview_url']) {
        $videoURL = $course_detail['course_overview_url'];
        $embedURL = str_replace("vimeo.com", "player.vimeo.com/video", $videoURL);
        $overview_tag = '<iframe width="100%" height="375" src="'.$embedURL.'" title="'.$course_detail['course_title'].'"
                            frameborder="0" allow="fullscreen; picture-in-picture" allowfullscreen></iframe>';
    } else{
        $videoURL = $course_detail['course_overview_url'];
        $overview_tag = '<video width="320" height="240" controls> <source src="'.$videoURL.'" type="video/mp4"></video>';
    }

    $template->assign_vars(array(
        'COURSE_TITLE'         => $course_detail['course_title'],
        'COURSE_SUMMARY'       => $course_detail['course_summary'],
        'COURSE_CONTENT'       => '<h3 class="fw-bold mb-3">About This Course</h3>' . $course_detail['course_description'],
        'COURSE_OVERVIEW_VID'  => $overview_tag,
        'COURSE_THUMB'         => $prevCourseThumb,
        'LESSON_SECTION'       => load_course_lessons($course_detail['id']),

    ));
}


function course_lesson_data($data)
{
    global $template;

    if ($data['video_url']) {
        $videoURL = $data['video_url'];
        $embedURL = str_replace("watch?v=", "embed/", $videoURL);

        $overview_tag = '<iframe width="100%" height="375" src="'.$embedURL.'" title="'.$data['lesson_name'].'" allowfullscreen></iframe>';
    } else {
        $overview_tag = null;
    }

    $template->assign_vars(array(
        'COURSE_TITLE'         => $data['lesson_name'],
        'COURSE_SUMMARY'       => $data['lesson_summary'],
        'COURSE_CONTENT'       => '<h3 class="fw-bold mb-3"></h3>'.$data['lesson_text'],
        'COURSE_OVERVIEW_VID'  => $overview_tag,
        'LESSON_SECTION'       => load_course_lessons($data['lms_course_id']),
    ));
}


function load_course_lessons($course_id) {
    $cardDiv = '<div class="accordion" id="accordionSectionLessonExample">';

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

                    // encode lesson id
                    $encode_lesson_id = encode_string("lesson_id==".$lesson['id']."==enrolled");
                    $encodeCourseID =encode_string("course_id==".$course_id."==enrolled");

                    $lessons .= '<div class="mb-4">
                                    <div class="lession-title"><i class="bi bi-play-circle"></i>
                                        <a href="'.tep_href_link(PATH_TO_LMS.LMS_START_COURSE_FILENAME,'action=_s&id='.$encodeCourseID.'&_lesson='.$encode_lesson_id).'">'.$lesson['lesson_name'].'</a>
                                    </div>
                                    <!-- <div class="lession-duration"><i class="bi bi-clock"></i> 12 Minutes</div> -->
                                </div>';
                }
            }

            $cardDiv .= '<div class="accordion-item">
                            <div class="accordion-header" id="heading-'.$key.'">
                                <button class="accordion-button py-2 collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapse-'.$key.'" aria-expanded="false" aria-controls="collapse-'.$key.'">
                                    <!-- <div class="fw-bold" style="font-size:12px;color:#d11818;">Section '.$key.'</div> -->
                                    <div class="section-title">'.$row_data['section_name'].'</div>
                                </button>
                            </div>
                            <div id="collapse-'.$key.'" class="accordion-collapse collapse" aria-labelledby="heading-'.$key.'" data-bs-parent="#accordionSectionLessonExample">
                                <div class="accordion-body">
                                    '.$lessons.'
                                </div>
                            </div>
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

function get_section_first_lesson($course_id)
{
    // first get the course first section id
    $db_section_query = "SELECT section.id AS section_id FROM lms_sections AS section WHERE section.lms_course_id = $course_id LIMIT 1";
    $query_section = tep_db_query($db_section_query);

    if (tep_db_num_rows($query_section) > 0) {
        $section_data = tep_db_fetch_array($query_section);

        // get the lesson data
        $where_clause = "WHERE lesson.lms_section_id = ".$section_data['section_id']." AND lesson.lms_course_id = $course_id";
        $db_raw_query = "SELECT lesson.* FROM " . LMS_LESSON_TBL . " AS lesson $where_clause ";
        $query = tep_db_query($db_raw_query);
        if (tep_db_num_rows($query) > 0) {
            $row_data = tep_db_fetch_array($query);
        } else {
            $row_data = false;
        }
    }

    return $row_data;
}


function get_lesson_data($course_id, $lesson_id)
{
    $where_clause = "WHERE lesson.id =$lesson_id AND lesson.lms_course_id = $course_id";

    $db_raw_query = "SELECT lesson.* FROM " . LMS_LESSON_TBL . " AS lesson $where_clause ";

    $query = tep_db_query($db_raw_query);

    if (tep_db_num_rows($query) > 0) {
        $row_data = tep_db_fetch_array($query);
    } else {
        $row_data = false;
    }

    return $row_data;
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