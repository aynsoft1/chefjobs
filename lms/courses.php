<?php
include_once("../include_files.php");
include_once(PATH_TO_MAIN_LMS_PHYSICAL_LANGUAGE . $language . '/' . LMS_COURSES_FILENAME);
$template->set_filenames(array(
  'list_course'         => 'recruiter/list-course.htm',
  'create_edit_form'    => 'recruiter/course-form.htm',
  'course_view'         => 'recruiter/view-course.htm',
));
include_once("../" . FILENAME_BODY);

// request parameters
$action = (isset($_GET['action']) ? $_GET['action'] : '');
$courseID = (isset($_GET['id']) ? tep_db_prepare_input($_GET['id']) : '');
$edit = false;
$error = false;
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

// form inputs
$input_course_title                  = $_POST['course_title'];
$input_course_summary                = $_POST['course_summary'];
$input_course_description            = $_POST['course_description'];
$input_lms_category_id               = $_POST['lms_category_id'];
$input_course_duration               = $_POST['course_duration'];
$input_requirement                   = $_POST['requirement'];
$input_course_price_check            = $_POST['course_price'];
$input_course_price                  = $_POST['price'];
$input_course_overview_provider      = $_POST['course_overview_provider'];
$input_course_overview_url           = $_POST['course_overview_url'];
$input_course_thumbnail              = $_POST['course_thumbnail'];
$input_course_instructor_ids         = $_POST['instructors'];





// check if recruiter is logged in  or not
if (!check_login("recruiter")) {
    $_SESSION['REDIRECT_URL'] = $_SERVER['REQUEST_URI'];
    $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
    tep_redirect(tep_href_link(FILENAME_RECRUITER_LOGIN));
} else {
    $recruiter_id   = $_SESSION['sess_recruiterid'];
    $user_type = 'recruiter';
    // $recruiterData = get_recruiter_detail($recruiter_id);
}


// if id request available then check the course available or not
if (tep_not_null($courseID)) {
    if (!$course_detail = getAnyTableWhereData(LMS_COURSE_TBL, "id='" . tep_db_input($courseID) . "' AND recruiter_id=$recruiter_id")) {
        $messageStack->add_session(MESSAGE_LMS_COURSE_FIND_ERROR, 'error');
        tep_redirect(LMS_COURSES_FILENAME);
    }
    $courseID = $course_detail['id'];
    $edit = true;
}





// default value pass
$template->assign_vars(array(
    'update_message' => $messageStack->output(),
    
    'new_button' => '
        <a 
            class="btn btn-sm btn-primary me-3 m-border" 
            href="' . tep_href_link(PATH_TO_LMS . LMS_COURSES_FILENAME, 'action=new') . '">
            <i class="bi bi-plus-lg me-2"></i> '.ADD_NEW_COURSE.'
        </a>
    ',

    'menus' => '
        <a 
            class="btn btn-outline-primary btn-outline-primary-custom me-3 m-border" 
            href="' . tep_href_link(PATH_TO_LMS . LMS_COURSES_FILENAME) . '">
            '.ALL_COURSES.'
        </a>
    ',

    'back_btn' => '
            <a 
                class="btn btn-outline-primary me-3 m-border" 
                href="' . tep_href_link(PATH_TO_LMS . LMS_COURSES_FILENAME) . '">
                Back
            </a>
        ',
));



// store course
if (tep_not_null($action) && $action == 'new' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    // validation
    if (strlen($input_course_title) <= 0) {
        $error = true;
        $error_title = 'course title is required';
    }
    
    if (strlen($input_course_summary) <= 0) {
        $error = true;
        $error_summary = 'course summary is required';
    }

    if (strlen($input_course_description) <= 0) {
        $error = true;
        $error_description = 'course descrption is required';
    }

    if ($input_lms_category_id <= 0) {
        $eror = true;
        $error_category = 'course category is required';
    }

    // create course
    $course_data = [
        'course_title'             => $input_course_title,
        'recruiter_id'             => $recruiter_id,
        'instructor_id'            => $input_course_instructor_ids,
        'lms_category_id'          => $input_lms_category_id,
        'course_summary'           => $input_course_summary,
        'course_description'       => $input_course_description,
        'requirement'              => $input_requirement,
        'price'                    => $input_course_price,
        'course_duration'          => $input_course_duration,
        'course_overview_provider' => $input_course_overview_provider,
        'course_overview_url'      => $input_course_overview_url,
        'created_at'               => $date,
        'updated_at'               => $date,
    ];

    if (!$error) {

        //////// file upload Attachment starts //////
        if (tep_not_null($_FILES['course_thumbnail']['name'])) {
            if ($obj_resume = new upload('course_thumbnail', PATH_TO_MAIN_PHYSICAL_TEMP, '644', array('jpg', 'gif', 'png'))) {
                $file_name = tep_db_input($obj_resume->filename);
                if (tep_not_null($file_name)) {
                    if (is_file(PATH_TO_MAIN_PHYSICAL_TEMP . $file_name)) {
                        $target_file_name = PATH_TO_MAIN_PHYSICAL_LMS_THUMB_IMAGE . $file_name;
                        copy(PATH_TO_MAIN_PHYSICAL_TEMP . $file_name, $target_file_name);
                        @unlink(PATH_TO_MAIN_PHYSICAL_TEMP . $file_name);
                        chmod($target_file_name, 0644);
                        $course_data['course_thumbnail'] = $file_name;
                    }
                }
            }
        }

        $courseStoreId = store_course(null, $course_data);
        $messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');
        return tep_redirect(LMS_COURSES_FILENAME.'?action=course-view&id='.$courseStoreId);
    }
}

// update course
if (tep_not_null($action) && $action == 'edit' && $courseID && $_SERVER['REQUEST_METHOD'] == 'POST' && ($_POST['_method'] == 'put')) {
    // validation
    if (strlen($input_course_title) <= 0) {
        $error = true;
        $error_title = 'course title is required';
    }
    
    if (strlen($input_course_summary) <= 0) {
        $error = true;
        $error_summary = 'course summary is required';
    }

    if (strlen($input_course_description) <= 0) {
        $error = true;
        $error_description = 'course descrption is required';
    }

    if ($input_lms_category_id <= 0) {
        $eror = true;
        $error_category = 'course category is required';
    }

    // create course
    $course_data = [
        'course_title'             => $input_course_title,
        'recruiter_id'             => $recruiter_id,
        'instructor_id'            => $input_course_instructor_ids,
        'lms_category_id'          => $input_lms_category_id,
        'course_summary'           => $input_course_summary,
        'course_description'       => $input_course_description,
        'requirement'              => $input_requirement,
        'price'                    => $input_course_price,
        'course_duration'          => $input_course_duration,
        'course_overview_provider' => $input_course_overview_provider,
        'course_overview_url'      => $input_course_overview_url,
        'updated_at'               => $date,
    ];

    if (!$error) {

        //////// file upload Attachment starts //////
        if (tep_not_null($_FILES['course_thumbnail']['name'])) {
            if ($obj_resume = new upload('course_thumbnail', PATH_TO_MAIN_PHYSICAL_TEMP, '644', array('jpg', 'gif', 'png'))) {
                $file_name = tep_db_input($obj_resume->filename);
                if (tep_not_null($file_name)) {
                    if (is_file(PATH_TO_MAIN_PHYSICAL_TEMP . $file_name)) {
                        $target_file_name = PATH_TO_MAIN_PHYSICAL_LMS_THUMB_IMAGE . $file_name;
                        copy(PATH_TO_MAIN_PHYSICAL_TEMP . $file_name, $target_file_name);
                        @unlink(PATH_TO_MAIN_PHYSICAL_TEMP . $file_name);
                        chmod($target_file_name, 0644);
                        $course_data['course_thumbnail'] = $file_name;
                        if ($edit && tep_not_null($course_detail['course_thumbnail'])) {
                            $old_photo = $course_detail['course_thumbnail'];
                            if (is_file(PATH_TO_MAIN_PHYSICAL_LMS_THUMB_IMAGE . $old_photo))
                                @unlink(PATH_TO_MAIN_PHYSICAL_LMS_THUMB_IMAGE . $old_photo);
                        }
                    }
                }
            }
        }

        update_course($courseID, $course_data);
        $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
        return tep_redirect(LMS_COURSES_FILENAME.'?action=course-view&id='.$courseID);
    }
}

// delete course
if (tep_not_null($action) && $action == 'confirm_delete' && $courseID && ($_SERVER['REQUEST_METHOD'] == 'POST') && ($_POST['_method'] == 'delete')) {
    tep_db_query("delete from " . LMS_COURSE_TBL . " where id='" . tep_db_input($courseID) . "' AND recruiter_id = $recruiter_id");
    $messageStack->add_session(MESSAGE_SUCCESS_DELETED, 'success');
    tep_redirect(LMS_COURSES_FILENAME);
}







if ($action == 'new' || $action == 'edit') {

    $template->assign_vars(array(
        'HEADING_TITLE'                 => ($action == 'edit') ? EDIT_NEW_COURSE : ADD_NEW_COURSE,

        'form'                          => ($action == 'new') ? getFormTag($action) : getFormTag($action, $courseID),
        'form_method'                   => ($action == 'edit') ? tep_draw_hidden_field('_method', 'put') : '',
        'BTN_NEXT' => BTN_NEXT,
        'BTN_PREVIOUS' => BTN_PREVIOUS,
        'BTN_SUBMIT' => BTN_SUBMIT,
        'COURSE_TITLE'                  => COURSE_TITLE,
        'COURSE_SUMMARY'                => COURSE_SUMMARY,
        'COURSE_DESCRIPTION'            => COURSE_DESCRIPTION,
        'COURSE_CATEGORY'               => COURSE_CATEGORY,
        'COURSE_DURATION'               => COURSE_DURATION,
        'COURSE_REQUIREMENT'            => COURSE_REQUIREMENT,
        'COURSE_PRICE_CHECK'            => COURSE_PRICE_CHECK,
        'COURSE_PRICE'                  => COURSE_PRICE,
        'COURSE_OVERVIEW_PROVIDER'      => COURSE_OVERVIEW_PROVIDER,
        'COURSE_OVERVIEW_URL'           => COURSE_OVERVIEW_URL,
        'COURSE_THUMBNAIL'              => COURSE_THUMBNAIL,
        'COURSE_INSTRUCTOR'             => COURSE_INSTRUCTOR,
        'COURSE_INSTRUCTOR_PROFILE'     => COURSE_INSTRUCTOR_PROFILE,
        'COURSE_INSTRUCTOR_DESIGNATION' => COURSE_INSTRUCTOR_DESIGNATION,
        'COURSES' => COURSES,
        'ENROLLED' => ENROLLED,
        'SALES' => SALES,


        'BASIC' => BASIC,
        'MEDIA' => MEDIA,
        'INSTRUCTOR' => INSTRUCTOR,
        
        'ADD_NEW_INSTRUCTOR'            => '<a href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_USERS).'" class="btn btn-link" target="_new" rel="noopener noreferrer">Add New Subuser</a>',

        'error_title'                   => $error_title,
        'error_summary'                 => $error_summary,
        'error_description'             => $error_description,
        'error_category'                => $error_category,


        'course_input_tag'              => tep_draw_input_field('course_title', $course_detail['course_title'] ,'id="course_title" class="form-control"'),
        'course_summary_tag'            => '<textarea name="course_summary" id="course_summary" class="form-control" cols="30" rows="3">'.$course_detail['course_summary'].'</textarea>',
        'course_description_tag'        => '<textarea name="course_description" id="course_description" class="form-control" cols="30" rows="5">'.$course_detail['course_description'].'</textarea>',
        'lms_category'                  => load_lms_category($course_detail['lms_category_id']),
        'course_duration_tag'           => tep_draw_input_field('course_duration',$course_detail['course_duration'], 'id="course_duration" class="form-control" placeholder="in minutes"','','number'),
        'course_requirement_tag'        => '<textarea name="requirement" id="requirement" class="form-control" cols="30" rows="2" >'.$course_detail['requirement'].'</textarea>',
        'course_price_checkbox_tag'     => tep_draw_input_field('course_price', '' ,'id="course_price" class="form-check-input"', '', 'checkbox'),
        'course_price_tag'              => tep_draw_input_field('price', $course_detail['price'] ,'id="price" class="form-control" placeholder="'.$sym_left.$sym_rt.'"'),

        'course_overview_provider_tag' => load_lms_course_provider($course_detail['course_overview_provider']),
        'course_overview_url'          => tep_draw_input_field('course_overview_url', $course_detail['course_overview_url'] ,'id="course_overview_url" class="form-control"'),

        'instructor_box'               => load_lms_course_instructor($recruiter_id, $course_detail['instructor_id']),

        'course_thumbnail'             => tep_draw_input_field('course_thumbnail', '' ,'id="course_thumbnail" class="form-control"', '', 'file'),

        'BUTTON'                => ($action == 'edit') 
                                    ? tep_button_submit('btn btn-primary float-right', UPDATE_BUTTON) 
                                    : tep_button_submit('btn btn-primary float-right', SUBMIT_BUTTON),

        'tiny_mce_script_url'   => tep_href_link('tinymce6/tinymce.min.js'),
    ));

    $template->pparse('create_edit_form');
}elseif ($action == 'course-view' && $courseID) {
    
    $template->assign_vars(array(
        'COURSE_NAME'           => $course_detail['course_title'],
        'COURSE_ID'             => $course_detail['id'],
        
        'api_url_section'       => tep_href_link(PATH_TO_LMS.LMS_COURSES_FILENAME,'action=api-section-store'),
        'api_url_section_update'=> tep_href_link(PATH_TO_LMS.LMS_COURSES_FILENAME,'action=api-section-update'),
        'api-section-delete'    => tep_href_link(PATH_TO_LMS.LMS_COURSES_FILENAME,'action=api-section-delete'),
        
        'api_url_lesson'        => tep_href_link(PATH_TO_LMS.LMS_COURSES_FILENAME,'action=api-lesson-store'),
        'api_url_lesson_update' => tep_href_link(PATH_TO_LMS.LMS_COURSES_FILENAME,'action=api-lesson-update'),
        'api_url_lesson_view'   => tep_href_link(PATH_TO_LMS.LMS_COURSES_FILENAME,'action=view-lesson'),
        'api-lesson-delete'    => tep_href_link(PATH_TO_LMS.LMS_COURSES_FILENAME,'action=api-lesson-delete'),

        'load_section_url'      => tep_href_link(PATH_TO_LMS.LMS_COURSES_FILENAME,'action=api-section-store&id='.$courseID),
        
        'course_description'    => $course_detail['course_description'],
        'tiny_mce_script_url'   => tep_href_link('tinymce6/tinymce.min.js'),
    ));
    $template->pparse('course_view');
}elseif ($action == 'api-section-store') {
    $api_errors = [];
    $api_data = [];
    $sec_name = $_POST['section_title'];
    $lms_course_id = $_POST['course_id'];

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (strlen($sec_name) <= 0) {
            $api_errors['section_title'] = 'Title is required.';
        }
        if (!empty($api_errors)) {
            $api_data['success'] = false;
            $api_data['errors'] = $api_errors;
        } else {
            $newD = [
                'section_name' => $sec_name,
                'lms_course_id' => $lms_course_id,
                'recruiter_id'  => $recruiter_id,
                'created_at'    => $date,
                'updated_at'    => $date,
            ];

            tep_db_perform(LMS_SECTION_TBL, $newD);

            $api_data['success'] = true;
            $api_data['message'] = 'Successfully created!';
            $api_data['data']    = $newD;
        }
        echo json_encode($api_data);
    } elseif ($_SERVER['REQUEST_METHOD'] == 'GET' AND $_GET['section-view']=='api-view-section' AND $_GET['sec-id']) {
        load_lms_sections($recruiter_id, null, true ,$_GET['sec-id']);
    } elseif ($_SERVER['REQUEST_METHOD'] == 'GET' AND $courseID) {
        load_lms_sections($recruiter_id, $courseID, false, null);
    }
}elseif($action == 'api-section-update' AND $_GET['section_id']){
    $sec_name = $_POST['section_title'];
    $lms_course_id = $_POST['course_id'];
    $secID = $_GET['section_id'];
    $api_data = [];
    $api_errors = [];

    if ($_SERVER['REQUEST_METHOD'] == 'POST' AND $_GET['section_id']) {
        if (strlen($sec_name) <= 0) {
            $api_errors['section_title'] = 'Title is required.';
        }

        if (!$secID) {
            $api_errors['section_id'] = 'section id is requird.';
        }

        if (!empty($api_errors)) {
            $api_data['success'] = false;
            $api_data['errors'] = $api_errors;
        } else {
            
            $updatedData = [
                'section_name'  => $sec_name,
                'lms_course_id' => $lms_course_id,
                'recruiter_id'  => $recruiter_id,
                'updated_at'    => $date,
            ];

            tep_db_perform(LMS_SECTION_TBL, $updatedData, 'update', "id='" . $secID . "'");

            $api_data['success'] = true;
            $api_data['message'] = 'Updated successfully';
            $api_data['data']    = $updatedData;
        }
        echo json_encode($api_data);
    }

}elseif($action == 'api-section-delete' AND $_GET['section_id']){
    $secID = $_GET['section_id'];
    $api_data = [];
    if (tep_not_null($secID)) {
        tep_db_query("delete from " . LMS_SECTION_TBL . " where id='" . tep_db_input($secID) . "' AND recruiter_id = $recruiter_id");
        $api_data['success'] = true;
        $api_data['message'] = 'Record delete successfully!';
        echo json_encode($api_data);
    } else {
        $api_data['success'] = false;
        $api_data['message'] = 'Something went wrong with section delete';
        echo json_encode($api_data);
    }
}elseif ($action =='api-lesson-store') {
    $api_errors = [];
    $api_data = [];

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $lesson_title       = $_POST['lesson_title'];
        $lesson_section_id  = $_POST['lms_section_id'];
        $lesson_text        = $_POST['lesson_text'];
        $lesson_summary     = $_POST['lesson_summary'];
        $lesson_video_url   = $_POST['video_url'];
        $lesson_course_id   = $_POST['lms_course_id'];

        if (strlen($lesson_title) <= 0) {
            $api_errors['lesson_title'] = 'Title is required.';
        }
        
        if (strlen($lesson_text) <= 0) {
            $api_errors['lesson_text'] = 'Text is required.';
        }

        if ($lesson_section_id <= 0) {
            $api_errors['lms_section_id'] = 'Section id is required';
        }

        if ($lesson_course_id <= 0) {
            $api_errors['lms_course_id'] = 'Course id is required';
        }


        if (!empty($api_errors)) {
            $api_data['success'] = false;
            $api_data['errors'] = $api_errors;
        } else {
            
            $newD = [
                'lesson_name'       => $lesson_title,
                'lms_section_id '   => $lesson_section_id,
                'lms_course_id '    => $lesson_course_id,
                'recruiter_id'      => $recruiter_id,
                'lesson_summary'    => $lesson_summary,
                'lesson_text'       => $lesson_text,
                'video_url'         => $lesson_video_url,
                'created_at'        => $date,
                'updated_at'        => $date,
            ];

            tep_db_perform(LMS_LESSON_TBL, $newD);

            $api_data['success'] = true;
            $api_data['message'] = 'lesson added successfully';
            $api_data['data']    = $newD;
        }
        
        echo json_encode($api_data);
    } else {
        echo json_encode($_SERVER['REQUEST_METHOD'].' method is not allowed');
    }
}elseif($action == 'api-lesson-update' AND $_GET['lesson_id']){
    $api_errors = [];
    $api_data = [];

    if ($_SERVER['REQUEST_METHOD'] == 'POST' AND $_GET['lesson_id']) {
        $lessonID = $_GET['lesson_id'];

        $lesson_title       = $_POST['lesson_title'];
        $lesson_section_id  = $_POST['lms_section_id'];
        $lesson_text        = $_POST['lesson_text'];
        $lesson_summary     = $_POST['lesson_summary'];
        $lesson_video_url   = $_POST['video_url'];
        $lesson_course_id   = $_POST['lms_course_id'];

        if (strlen($lesson_title) <= 0) {
            $api_errors['lesson_title'] = 'Title is required.';
        }
        
        if (strlen($lesson_text) <= 0) {
            $api_errors['lesson_text'] = 'Text is required.';
        }

        if ($lesson_section_id <= 0) {
            $api_errors['lms_section_id'] = 'Section id is required';
        }

        if ($lesson_course_id <= 0) {
            $api_errors['lms_course_id'] = 'Course id is required';
        }

        if (!$lessonID) {
            $api_errors['lesson_id'] = 'lesson id is requird.';
        }


        if (!empty($api_errors)) {
            $api_data['success'] = false;
            $api_data['errors'] = $api_errors;
        } else {
            
            $updatedData = [
                'lesson_name'       => $lesson_title,
                'lms_section_id '   => $lesson_section_id,
                'lms_course_id '    => $lesson_course_id,
                'recruiter_id'      => $recruiter_id,
                'lesson_summary'    => $lesson_summary,
                'lesson_text'       => $lesson_text,
                'video_url'         => $lesson_video_url,
                'created_at'        => $date,
                'updated_at'        => $date,
            ];

            tep_db_perform(LMS_LESSON_TBL, $updatedData, 'update', "id='" . $lessonID . "'");

            $api_data['success'] = true;
            $api_data['message'] = 'lesson updated successfully';
            $api_data['data']    = $updatedData;
        }
        
        echo json_encode($api_data);
    }
}elseif ($_SERVER['REQUEST_METHOD'] == 'GET' AND $action == 'view-lesson' AND $_GET['lesson_id']) {
    find_lesson_for_id($recruiter_id, $_GET['lesson_id']);
}elseif($action == 'api-lesson-delete' AND $_GET['lesson_id']){
    $lessonID = $_GET['lesson_id'];
    $api_data = [];

    if (tep_not_null($lessonID)) {
        tep_db_query("delete from " . LMS_LESSON_TBL . " where id='" . tep_db_input($lessonID) . "' AND recruiter_id = $recruiter_id");
        $api_data['success'] = true;
        $api_data['message'] = 'Lesson deleted successfully';
        echo json_encode($api_data);
    } else {
        $api_data['success'] = false;
        $api_data['message'] = 'Something went wrong with lesson delete';
        echo json_encode($api_data);
    }
}else {
    $isCourse = get_recruiter_lms_courses($startAt, $perPage);
    $reportBox = get_recruiter_report_box($recruiter_id);
    $template->assign_vars(array(
        'HEADING_TITLE'         => '<h1>'.HEADING_TITLE . '<span class="badge small bg-secondary ms-2">'.paginate_lms_course()['totalData'].'</span></h1>',

        'TH_TITLE'              => TH_TITLE,
        'TH_DATE'               => TH_DATE,
        'TH_ACTION'             => TH_ACTION,
        'TH_CATEGORY'           => TH_CATEGORY,
        'TH_LESSON_SECTION'     => TH_LESSON_SECTION,
        'TH_ENROLLED_STUDENT'   => TH_ENROLLED_STUDENT,
        'TH_STATUS'             => TH_STATUS,
        'TH_PRICE'              => TH_PRICE,

        'TOTAL_COURSES'         => $reportBox['total_courses'],
        'TOTAL_ENROLLS'         => $reportBox['total_enrollments'],
        'TOTAL_SALES'           => $reportBox['total_sales'],

        'COURSE_NOT_FOUND'      => ($isCourse) ? null : '<tr><td colspan="10" class="text-center">course not found</td></tr>',
        'PAGINATION_LINK'       => paginate_lms_course()['pagination'],
    ));
    $template->pparse('list_course');
}





// ///////////////////////////////// functions //////////////////////////////////////////

// get all categories
function get_recruiter_lms_courses(int $offset, int $perPage)
{
    global $template, $recruiter_id, $sym_left, $sym_rt;

    $db_raw_query = "SELECT course.*, category.category_name, COUNT(e.lms_course_id) AS enrolled_student
                    FROM " . LMS_COURSE_TBL . " AS course
                    LEFT JOIN ".LMS_CATEGORY_TBL." AS category ON category.id = course.lms_category_id
                    LEFT JOIN ".LMS_COURSE_ENROLL_TBL." AS e ON e.lms_course_id = course.id
                    WHERE course.recruiter_id = $recruiter_id
                    GROUP BY course.id, e.lms_course_id
                    ORDER BY course.id DESC
                    LIMIT $offset, $perPage";

    $query = tep_db_query($db_raw_query);

    if (tep_db_num_rows($query) > 0) {
        while ($row_data = tep_db_fetch_array($query)) {

            if ($row_data['course_thumbnail']) {
                $course_thumb = tep_image(FILENAME_IMAGE . "?image_name=" . PATH_TO_UPLOAD_IMAGE . $row_data['course_thumbnail'] . "&size=0", '', '', '', 'align="center" class="course-thumb rounded"');
            } else {
                $course_thumb = defaultProfilePhotoUrl($row_data['course_title'], false, 55);
            }

            if (tep_db_output($row_data['price']) == '0.00' OR $row_data['price'] == null) {
                $course_price = '<span>Free</span>';
            }else {
                $course_price = '<span>'.$sym_left.tep_db_output($row_data['price']).$sym_rt.'</span>';
            }

            if (tep_db_output($row_data['course_is_active']) == 1) {
                $course_staus = '<span class="badge bg-green">Active</span>';
            }else {
                $course_staus = '<span class="badge bg-secondary">Inactive</span>';
            }

            $title = '<a class="h6" href="'.tep_href_link(PATH_TO_LMS.LMS_COURSES_FILENAME,'action=course-view&id='.$row_data['id']).'">'.tep_db_output($row_data['course_title']).'</a>';

            $template->assign_block_vars('lms_courses', array(
                'id'            => tep_db_output($row_data['id']),
                'slug'          => tep_db_output($row_data['slug']),
                'title'         => $title,
                'category'      => tep_db_output($row_data['category_name']),
                'total_section' => count_records_for_course($row_data['id'], $recruiter_id)[0]['total'],
                'total_lessons' => count_records_for_course($row_data['id'], $recruiter_id)[1]['total'],
                'enrolled'      => tep_db_output($row_data['enrolled_student']),
                'price'         => $course_price,
                'status'        => $course_staus,
                'thumb'         => $course_thumb,
                'created_at'    => tep_date_short($row_data['created_at']),
                'action'        => getAction(tep_db_output($row_data['id'])),
            ));
        }
        tep_db_free_result($query);
        return true;
    }

    return false;
}

// action btn function
function getAction($id)
{
    $onclickEvent = "event.preventDefault();if(confirm('Are you sure!')){document.getElementById('form-delete-$id').submit()}";

    $button = '
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        '.ACTION_DROPDOWN_BTN.'
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="' . tep_href_link(PATH_TO_LMS . LMS_LIST_COURSES_FILENAME, 'action=course-preview&id='.$id) . '" target="_new" rel="noopener noreferrer">
                                ' . VIEW_ON_FRONT_END . '
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item" href="' . tep_href_link(PATH_TO_LMS . LMS_COURSES_FILENAME, 'id=' . $id . '&action=edit') . '">
                                ' . EDIT_ACTION_BTN . '
                            </a>
                        </li>
                        
                        <!--
                        <li>
                            <a class="dropdown-item" href="' . tep_href_link(PATH_TO_LMS . LMS_COURSES_FILENAME, 'id=' . $id . '&action=course-view') . '">
                                ' . VIEW_ACTION_BTN . '
                            </a>
                        </li>
                        -->

                        <li>
                            <a class="dropdown-item" href="#" onclick="'.$onclickEvent.'">'
                                . DELETE_ACTION_BTN . '
                            </a>
                            <form style="display:none" 
                                method="post" 
                                id="form-delete-'.$id.'"
                                action="' . tep_href_link(PATH_TO_LMS . LMS_COURSES_FILENAME, 'id=' . $id . '&action=confirm_delete') . '">
                                <input name="_method" type="hidden" value="delete" />
                            </form>
                        </li>

                    </ul>
                </div>
    ';

    return $button;
}

// pagination for categories
function paginate_lms_course() {
    global $perPage, $page, $recruiter_id;

    $countRow = "SELECT COUNT(*) AS total FROM ". LMS_COURSE_TBL . " WHERE recruiter_id = $recruiter_id ";

    $result = tep_db_query($countRow);

    if (tep_db_num_rows($result) > 0) {
        $total_row = tep_db_fetch_array($result)['total'];
    }
    
    $total_page = ceil($total_row / $perPage);

    $prevURL = ($page <= 1) ? '#' : tep_href_link(PATH_TO_LMS.LMS_COURSES_FILENAME, 'page='.($page - 1).'');
    $prevDisabled = ($page <= 1) ? 'disabled' : '';
    
    $nextURL = ($page >= $total_page) ? '#' : tep_href_link(PATH_TO_LMS.LMS_COURSES_FILENAME, 'page='.($page + 1).'');
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

// form tag
function getFormTag($actionValue, $id = null)
{
    switch ($actionValue) {
        case 'new':
            // return tep_draw_form('lms_course', PATH_TO_LMS . LMS_COURSES_FILENAME, 'action=new', 'post', 'id="lms-course-submit" enctype="multipart/form-data"');
            return '<form name="lms_course" action="'.tep_href_link(PATH_TO_LMS . LMS_COURSES_FILENAME,'action=new').'" method="post" id="lms-course-submit" enctype="multipart/form-data">';
            break;
        case 'edit':
            // return tep_draw_form('lms_course', PATH_TO_LMS . LMS_COURSES_FILENAME, 'id=' . $id . '&action=edit', 'post', 'id="lms-course-submit" enctype="multipart/form-data"');
            return '<form name="lms_course" action="'.tep_href_link(PATH_TO_LMS . LMS_COURSES_FILENAME,'id='.$id.'&action=edit').'" method="post" id="lms-course-submit" enctype="multipart/form-data">';
            break;
    }
}

// get recruiter details
function get_recruiter_detail($recruiter_id)
{
    $query = "SELECT * FROM (
                                SELECT r.recruiter_id, CONCAT(r.recruiter_first_name,' ',r.recruiter_last_name) as full_name, rl.recruiter_email_address
                                FROM  recruiter as r INNER JOIN recruiter_login as rl
                                ON r.recruiter_id = rl.recruiter_id
                            ) AS custom_recruiter WHERE recruiter_id = $recruiter_id";

    $query_result = tep_db_query($query);

    $row = tep_db_fetch_array($query_result);

    return $row;
}

// get lms categories
function load_lms_category($value = null) {

    $db_raw_query = "SELECT * FROM " . LMS_CATEGORY_TBL . " AS category ORDER BY category.id DESC";

    $query = tep_db_query($db_raw_query);

    $select_box = '<select class="form-select" name="lms_category_id" id="lms_category_id" aria-label="course category"><option value="0">Select course category</option>';

    if (tep_db_num_rows($query) > 0) {
        while ($row_data = tep_db_fetch_array($query)) {
            $selected = ($value == $row_data['id']) ? 'selected' : '';
            $select_box .= '<option value="'.$row_data['id'].'" '.$selected.'>'.$row_data['category_name'].'</option>';
        }
        tep_db_free_result($query);
    }

    $select_box .= '</select>';
    
    return $select_box;
}

// get lms course provider
function load_lms_course_provider($value = null) {
    $select_box = '<select class="form-select" name="course_overview_provider" id="course_overview_provider" aria-label="course provider"><option value="0">Select provider</option>';
    
    $providers = ['youtube', 'vimeo'];

    foreach ($providers as $provider) {
        $selected = ($value == $provider) ? 'selected' : '';
        $select_box .= '<option value="'.$provider.'" '.$selected.'>'.$provider.'</option>';
    }

    $select_box .= '</select>';

    return $select_box;
}

// get lms course provider
function load_lms_course_instructor($recruiter_id, $selectedID = null) {

    $db_raw_query = "SELECT * FROM " . RECRUITER_USERS_TABLE . " AS instructor
                    WHERE instructor.recruiter_id = $recruiter_id AND instructor.status = 'Yes' 
                    ORDER BY instructor.id DESC";

    $query = tep_db_query($db_raw_query);

    $select_box = '<select class="form-select" name="instructors" id="instructors" aria-label="course provider"><option value="0">Select Instructor</option>';

    if (tep_db_num_rows($query) > 0) {
        while ($row_data = tep_db_fetch_array($query)) {
            $selected = ($selectedID == $row_data['id']) ? 'selected' : '';
            $select_box .= '<option value="'.$row_data['id'].'" '.$selected.'>'.$row_data['name'].'</option>';
        }
        tep_db_free_result($query);
    }

    $select_box .= '</select>';
    
    return $select_box;
}


function store_course(array $instructor_ids = null, array $course_arr_data) {
    // db connectivity check
    $link = tep_db_connect();

    // create course
    $data = tep_db_perform(LMS_COURSE_TBL, $course_arr_data);

    $course_id = mysqli_insert_id($link);

    // store course_id and instructor_id in pivot table course_instructor
    if ($instructor_ids AND $course_id) {
        print_r('instructor id found');
    }

    return $course_id;
}

function update_course($courseID, array $course_arr_data, array $instructor_ids = null)
{
    if ($courseID) {
        tep_db_perform(LMS_COURSE_TBL, $course_arr_data, 'update', "id='" . $courseID . "'");
    }
}

// load sectoins
function load_lms_sections($recruiter_id, $course_id = null, $find_with_section_id = false, $section_id = null) {
    $output = array();

    // SELECT section.id AS section_id, section.section_name, section.lms_course_id, section.recruiter_id,
	// 	lesson.id AS lesson_id,lesson.lesson_name,lesson.lms_section_id
    // FROM lms_sections AS section
    // LEFT JOIN lms_lessons AS lesson ON lesson.lms_section_id = section.id
    // WHERE section.recruiter_id=27;

    if ($find_with_section_id) {
        $where_clause = "WHERE section.recruiter_id=$recruiter_id AND section.id = $section_id";
    } else {
        $where_clause = "WHERE section.recruiter_id=$recruiter_id AND section.lms_course_id = $course_id";
    }

    $db_raw_query = "SELECT section.* 
                    FROM " . LMS_SECTION_TBL . " AS section 
                    $where_clause
                    ORDER BY section.id ASC";

    $query = tep_db_query($db_raw_query);

    if (tep_db_num_rows($query) > 0) {
        while ($row_data = tep_db_fetch_array($query)) {

            if (count(load_section_lessons($recruiter_id, $row_data['id'])) > 0 AND !$find_with_section_id) {
                $lessonArray = load_section_lessons($recruiter_id, $row_data['id']);
            } else {
                $lessonArray = null;
            }

            $item = [
                'id'            => $row_data['id'],
                'section_name'  => $row_data['section_name'],
                'created_at'    => tep_date_short($row_data['created_at']),
                'lessons'       => $lessonArray,
            ];

            array_push($output, $item);
        }
        tep_db_free_result($query);
    }
 
    // set response code - 200 OK
    http_response_code(200);
    
    echo json_encode($output);
}

function load_section_lessons($recruiter_id, $section_id, $find_with_lesson_id = false, $lesson_id = null) {
    $output = array();

    if ($find_with_lesson_id) {
        $where_clause = "WHERE lesson.recruiter_id=$recruiter_id AND lesson.lms_section_id = $section_id AND lesson.id = $lesson_id";
    } else {
        $where_clause = "WHERE lesson.recruiter_id=$recruiter_id AND lesson.lms_section_id = $section_id";
    }

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

function find_lesson_for_id($recruiter_id, $lesson_id)
{
    $output = array();

    $where_clause = "WHERE lesson.recruiter_id=$recruiter_id AND lesson.id = $lesson_id";
    $db_raw_query = "SELECT lesson.* 
                    FROM " . LMS_LESSON_TBL . " AS lesson 
                    $where_clause 
                    ORDER BY lesson.id ASC";

    $query = tep_db_query($db_raw_query);

    if (tep_db_num_rows($query) > 0) {
        while ($row_data = tep_db_fetch_array($query)) {
            $item = [
                'id'            => $row_data['id'],
                'lesson_name'   => $row_data['lesson_name'],
                'lesson_summary'=> $row_data['lesson_summary'],
                'lesson_text'   => $row_data['lesson_text'],
                'video_url'     => $row_data['video_url'],
                'is_preview'    => $row_data['is_preview'],
                'lms_section_id'=> $row_data['lms_section_id'],
                'created_at'    => tep_date_short($row_data['created_at']),
            ];

            array_push($output, $item);
        }
        tep_db_free_result($query);
    }

    // set response code - 200 OK
    http_response_code(200);
    
    echo json_encode($output);
}
// count records for employer
function count_records_for_course($course_id, $recruiter_id)
{
    $total_sections = no_of_records(LMS_SECTION_TBL, "lms_course_id = $course_id AND recruiter_id = $recruiter_id");
    $total_lessons = no_of_records(LMS_LESSON_TBL, "lms_course_id = $course_id AND recruiter_id = $recruiter_id");

    $reportData = [
        ['title' => 'Sections', 'total' => $total_sections],
        ['title' => 'Lessons', 'total' => $total_lessons],
    ];

    return $reportData;
}

// report box
function get_recruiter_report_box($recruiter_id)
{
    global $sym_left, $sym_rt;
    $total_courses      = no_of_records(LMS_COURSE_TBL, "recruiter_id = $recruiter_id");
    $total_enrollments  = no_of_records(LMS_COURSE_ENROLL_TBL . " AS enrolled LEFT JOIN ".LMS_COURSE_TBL." ON lms_courses.id = enrolled.lms_course_id", "lms_courses.recruiter_id = $recruiter_id");
    $total_sales = getAnyTableWhereData(LMS_COURSE_ENROLL_TBL . " AS enrolled LEFT JOIN ".LMS_COURSE_TBL." ON lms_courses.id = enrolled.lms_course_id",
                                        "lms_courses.recruiter_id = $recruiter_id",
                                        "SUM(lms_courses.price) AS total"                
                                    );
                                    
    $data = [
                'total_courses'     => $total_courses,
                'total_enrollments' => $total_enrollments,
                'total_sales'       => $sym_left.ceil($total_sales['total']).$sym_rt,
            ];

    return $data;
}
?>