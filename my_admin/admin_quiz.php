<?php
// Initial Setup
include_once("../include_files.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE . $language . '/' . FILENAME_ADMIN1_LIST_OF_QUIZ);
$template->set_filenames(
    array(
        'all_quiz' => 'quiz/admin_all_quiz.htm',
        'create_update_form' => 'quiz/admin_store_quiz.htm',
        'preview' => 'quiz/admin_view_quiz.htm',
        'video_list' => 'quiz/video-list.htm',
        'essay_list' => 'quiz/essay-list.htm',
    )
);
include_once(FILENAME_ADMIN_BODY);

// global Properties
$action = (isset($_GET['action']) ? $_GET['action'] : '');
$quiz_id = (isset($_GET['id']) ? tep_db_prepare_input($_GET['id']) : '');
$edit = false;
$error = false;
$currentDate = date("Y-m-d H:i:s"); // current date
$errorTitle;
$errorDescription;
$errorTimer;

$perPage = 10;

if (isset($_GET['page']) AND $_GET['page'] != 0) {
    $page = (int)$_GET['page'];
} else {
    $page = 1;
}
$startAt = $perPage * ($page - 1);

/**
 * Check Condition if id is present in table or not
 */
if (tep_not_null($quiz_id)) {
    if (!$row_check_quiz_id = getAnyTableWhereData(QUIZ_TABLE, "id='" . tep_db_input($quiz_id) . "'")) {
        $messageStack->add_session(MESSAGE_QUIZ_ERROR, 'error');
        tep_redirect(FILENAME_ADMIN1_LIST_OF_QUIZ);
    }
    $quiz_id = $row_check_quiz_id['id'];
    $edit = true;
}


// Object return: Quiz row return with Id
function findDataWithQuizId()
{
    global $error, $action, $row_check_quiz_id;
    if (!$error && $action == 'edit') {
        $data = new objectInfo($row_check_quiz_id);
        return $data;
    }
    return false;
}

// Object return: Quiz row return with Id
function viewDataWithQuizId()
{
    global $error, $action, $row_check_quiz_id;
    if (!$error && $action == 'preview') {
        $data = new objectInfo($row_check_quiz_id);
        return $data;
    }
    return false;
}

function getQuizDataWithQuizId(int $id)
{
    $data = getAnyTableWhereData(QUIZ_TABLE, "id='" . $id . "'");
    return $data;
}

// perform insert data in quizzes table
function storeForm(array $sql_data)
{
    $data = tep_db_perform(QUIZ_TABLE, $sql_data);
    return $data;
}

// perform update the data in quizzes table
function updateForm(array $sql_data)
{
    global $quiz_id;
    $data = tep_db_perform(QUIZ_TABLE, $sql_data, 'update', "id='" . $quiz_id . "'");
    return $data;
}

// perform delete quiz action
function deleteQuiz(int $id)
{
    $data = tep_db_query("delete from " . QUIZ_TABLE . " where id='" . tep_db_input($id) . "'");
    return $data;
}

/**
 * title, description should be pass
 * And for actionValue vlaue must be new or edit don't put any other because based on these this will redirect to page
 * @param [string] $title
 * @param [string] $description
 * @param [string] $actionValue
 *
 */
function validationFormCheck(string $title, string $description, string $timer, string $actionValue)
{
    global $action, $error, $errorDescription, $errorTitle, $errorTimer;
    if (strlen($title) <= 0) {
        $error = true;
        $action = $actionValue;
        $errorTitle = true;
    }
    if (!is_numeric($timer) || ($timer <= 0)) {
        $error = true;
        $action = $actionValue;
        $errorTimer = true;
    }
}

// Submit / Update / Delete functionality if action is not null
if (tep_not_null($action)) {
    $quiz_title         = tep_db_prepare_input($_POST['title']);
    $test_category_id   = tep_db_prepare_input($_POST['test_category_id']);
    $timer         = tep_db_prepare_input($_POST['timer']);
    $quiz_description   = stripslashes($_POST['description']);

    switch ($action) {
        case 'submitForm':
            validationFormCheck($quiz_title, $quiz_description, $timer, 'new');
            if (!$error) {
                $store_data_array = array(
                    'title'            => $quiz_title,
                    'test_category_id' => $test_category_id,
                    'description'      => $quiz_description,
                    'timer'            => $timer,
                    'save_as_template' => 1,
                    'created_at'       => $currentDate,
                    'updated_at'       => $currentDate,
                );

                //////// file upload Attachment starts //////
                if (tep_not_null($_FILES['picture']['name'])) {
                    if ($obj_resume = new upload('picture', PATH_TO_MAIN_PHYSICAL_TEMP, '644', array('jpg', 'gif', 'png'))) {
                        $quiz_picture_name = tep_db_input($obj_resume->filename);
                        if (tep_not_null($quiz_picture_name)) {
                            if (is_file(PATH_TO_MAIN_PHYSICAL_TEMP . $quiz_picture_name)) {
                                $target_file_name = PATH_TO_MAIN_PHYSICAL_FORUM_IMAGE . $quiz_picture_name;
                                copy(PATH_TO_MAIN_PHYSICAL_TEMP . $quiz_picture_name, $target_file_name);
                                @unlink(PATH_TO_MAIN_PHYSICAL_TEMP . $quiz_picture_name);
                                chmod($target_file_name, 0644);
                                $store_data_array['picture'] = $quiz_picture_name;
                            }
                        }
                    }
                }
                //////// file upload ends //////

                storeForm($store_data_array);
                $messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');
                return tep_redirect(FILENAME_ADMIN1_LIST_OF_QUIZ);
            }
            break;
        case 'updateForm':
            // Validation check
            validationFormCheck($quiz_title, $quiz_description, $timer, 'edit');
            if (!$error) {
                $update_data_array = array(
                    'title'            => $quiz_title,
                    'test_category_id' => $test_category_id,
                    'description'      => $quiz_description,
                    'timer'            => $timer,
                    'updated_at'       => $currentDate,
                );
                //////// file upload Attachment starts //////
                if (tep_not_null($_FILES['picture']['name'])) {
                    if ($obj_resume = new upload('picture', PATH_TO_MAIN_PHYSICAL_TEMP, '644', array('jpg', 'gif', 'png'))) {
                        $quiz_picture_name = tep_db_input($obj_resume->filename);
                        if (tep_not_null($quiz_picture_name)) {
                            if (is_file(PATH_TO_MAIN_PHYSICAL_TEMP . $quiz_picture_name)) {
                                $target_file_name = PATH_TO_MAIN_PHYSICAL_FORUM_IMAGE . $quiz_picture_name;
                                copy(PATH_TO_MAIN_PHYSICAL_TEMP . $quiz_picture_name, $target_file_name);
                                @unlink(PATH_TO_MAIN_PHYSICAL_TEMP . $quiz_picture_name);
                                chmod($target_file_name, 0644);
                                $update_data_array['picture'] = $quiz_picture_name;
                                if ($edit && tep_not_null(getQuizDataWithQuizId($quiz_id)['picture'])) {
                                    $old_photo = getQuizDataWithQuizId($quiz_id)['picture'];
                                    if (is_file(PATH_TO_MAIN_PHYSICAL_FORUM_IMAGE . $old_photo))
                                        @unlink(PATH_TO_MAIN_PHYSICAL_FORUM_IMAGE . $old_photo);
                                }
                            }
                        }
                    }
                }
                //////// file upload ends //////
                updateForm($update_data_array);
                $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
                return tep_redirect(FILENAME_ADMIN1_LIST_OF_QUIZ);
            }
            break;
        case 'confirm_delete':
            deleteQuiz($quiz_id);
            $messageStack->add_session(MESSAGE_SUCCESS_DELETED, 'success');
            tep_redirect(FILENAME_ADMIN1_LIST_OF_QUIZ);
            break;
        case 'test_active':
        case 'test_inactive':
            tep_db_query("update " . QUIZ_TABLE . " set isActive='" . ($action == 'test_active' ? '1' : '0') . "' where id='" . $quiz_id . "'");
            $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
            return tep_redirect(FILENAME_ADMIN1_LIST_OF_QUIZ);
            break;
        case 'test_save_as_template':
        case 'test_not_save_as_template':
            tep_db_query("update " . QUIZ_TABLE . " set save_as_template='" . ($action == 'test_save_as_template' ? '1' : '0') . "' where id='" . $quiz_id . "'");
            $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
            return tep_redirect(FILENAME_ADMIN1_LIST_OF_QUIZ);
            break;
    }
}

function interface_toggle_link_btn(int $test_id, string $action_name, int $value)
{
    $onclickEvent = "event.preventDefault();if(confirm('Are you sure!')){document.getElementById('form-interface-$test_id').submit()}";

    if ($value == 1) {
        $toggle_on_off = '
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-toggle-on" viewBox="0 0 16 16">
                                <path d="M5 3a5 5 0 0 0 0 10h6a5 5 0 0 0 0-10H5zm6 9a4 4 0 1 1 0-8 4 4 0 0 1 0 8z"/>
                            </svg>
        ';
    } else {
        $toggle_on_off =  '
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-toggle-off" viewBox="0 0 16 16">
                                <path d="M11 4a4 4 0 0 1 0 8H8a4.992 4.992 0 0 0 2-4 4.992 4.992 0 0 0-2-4h3zm-6 8a4 4 0 1 1 0-8 4 4 0 0 1 0 8zM0 8a5 5 0 0 0 5 5h6a5 5 0 0 0 0-10H5a5 5 0 0 0-5 5z"/>
                            </svg>
        ';
    }

    $linkBtn = ' <a class="dropdown-item" href="#" onclick="'.$onclickEvent.'">'
                    . $toggle_on_off . '
                </a>
                <form style="display:none" 
                    method="post" 
                    id="form-interface-'.$test_id.'"
                    action="' . tep_href_link(PATH_TO_ADMIN . FILENAME_ADMIN1_LIST_OF_QUIZ, 'id=' . $test_id . '&action='.$action_name) . '">
                    <input name="_method" type="hidden" value="put" />
                </form>';

    return $linkBtn;
}

// create action button Edit and Delete
function getAction($quizId)
{
    $button = '
    <div class="btn-group">
        <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Action
        </button>
        <div class="dropdown-menu">
            <a class="dropdown-item" href="' . tep_href_link(PATH_TO_ADMIN . FILENAME_ADMIN1_LIST_OF_QUIZ, 'id=' . $quizId . '&action=edit') . '">
                ' . EDIT_TEXT . '
            </a>
            <a class="dropdown-item" href="' . tep_href_link(PATH_TO_ADMIN . FILENAME_ADMIN1_LIST_OF_QUIZ, 'id=' . $quizId . '&action=preview') . '">
                ' . VIEW_TEXT . '
            </a>
            <a class="dropdown-item" href="' . tep_href_link(PATH_TO_ADMIN . FILENAME_ADMIN1_LIST_OF_QUIZ, 'id=' . $quizId . '&action=confirm_delete') . '">'
        . DELETE_TEXT . '
            </a>
        </div>
    </div>
    ';
    return $button;
}

// quiz message link button
function getMessageLink($quizId)
{
    $button = '
        <a class="" href="' . tep_href_link(PATH_TO_ADMIN . FILENAME_QUIZ_MESSAGES, 'id=' . $quizId . '&action=listMessage') . '">
            ' . ADD_REPORT . '
        </a>    
    ';
    return $button;
}

// get the starting form tag <form> not </form> -> this would be manually added in the htm file
function getFormTag($actionValue, $quizId = null)
{
    switch ($actionValue) {
        case 'new':
            return tep_draw_form('quiz', PATH_TO_ADMIN . FILENAME_ADMIN1_LIST_OF_QUIZ, 'action=submitForm', 'post', ' enctype="multipart/form-data"');
            break;
        case 'edit':
            return tep_draw_form('article', PATH_TO_ADMIN . FILENAME_ADMIN1_LIST_OF_QUIZ, 'id=' . $quizId . '&action=updateForm', 'post', 'enctype="multipart/form-data"');
            break;
    }
}

function on_off_toggle_link_btn(int $test_id, string $action_name, int $value)
{
    if ($value == 1) {
        $toggle_on_off = '
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-toggle-on" viewBox="0 0 16 16">
                                <path d="M5 3a5 5 0 0 0 0 10h6a5 5 0 0 0 0-10H5zm6 9a4 4 0 1 1 0-8 4 4 0 0 1 0 8z"/>
                            </svg>
        ';
    } else {
        $toggle_on_off =  '
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-toggle-off" viewBox="0 0 16 16">
                                <path d="M11 4a4 4 0 0 1 0 8H8a4.992 4.992 0 0 0 2-4 4.992 4.992 0 0 0-2-4h3zm-6 8a4 4 0 1 1 0-8 4 4 0 0 1 0 8zM0 8a5 5 0 0 0 5 5h6a5 5 0 0 0 0-10H5a5 5 0 0 0-5 5z"/>
                            </svg>
        ';
    }

    return '<a href="' . tep_href_link(PATH_TO_ADMIN . FILENAME_ADMIN1_LIST_OF_QUIZ, 'id=' . $test_id . '&action='.$action_name) . '">' . $toggle_on_off . '</a>';
}

function category_lists(int $selected_category_id = null)
{
    $test_query = "SELECT * FROM " . TEST_CATEGORY_TABLE . " as category 
                            WHERE category.is_active = '1' 
                            ORDER BY category.created_at DESC";

    $test_result = tep_db_query($test_query);

    $options = '<option value="0">Select Category</option>';

    if (tep_db_num_rows($test_result) > 0) {
        while ($data = tep_db_fetch_array($test_result)) {
            $selected = ($selected_category_id == $data['id']) ? 'selected' : null ;

            $options .= '<option value="' . $data['id'] . '" '.$selected.'>
                                ' . $data['name'] . '
                        </option>';
        }
        tep_db_free_result($test_result);
    }

    $select_job = '
            <select name="test_category_id" id="category_id" class="form-control" required>
                ' . $options . '
            </select>      
            ';

    return $select_job;
}

function get_pagination_for_videos() {
    global $perPage, $page;

    $countRow = "SELECT COUNT(*) AS total FROM quiz_videos";

    $result = tep_db_query($countRow);

    if (tep_db_num_rows($result) > 0) {
        $total_row = tep_db_fetch_array($result)['total'];
    }
    
    $total_page = ceil($total_row / $perPage);

    $prevURL = ($page <= 1) ? '#' : tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_QUIZ, 'page='.($page - 1).'&action=video-list');
    $prevDisabled = ($page <= 1) ? 'disabled' : '';
    
    $nextURL = ($page >= $total_page) ? '#' : tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_QUIZ, 'page='.($page + 1).'&action=video-list');
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

function get_pagination_for_essay() {
    global $perPage, $page;

    $countRow = "SELECT COUNT(*) AS total FROM quiz_essay_answers";

    $result = tep_db_query($countRow);

    if (tep_db_num_rows($result) > 0) {
        $total_row = tep_db_fetch_array($result)['total'];
    }
    
    $total_page = ceil($total_row / $perPage);

    $prevURL = ($page <= 1) ? '#' : tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_QUIZ, 'page='.($page - 1).'&action=essay-list');
    $prevDisabled = ($page <= 1) ? 'disabled' : '';
    
    $nextURL = ($page >= $total_page) ? '#' : tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_QUIZ, 'page='.($page + 1).'&action=essay-list');
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

function get_list_of_videos(int $offset, int $perPage)
{
    global $template;

    // get the list of assign test
    $list_test_query = "SELECT video.*, 
                            assessments.title AS assessment, 
                            test.title AS test, 
                            job.job_title, 
                            recruiter.recruiter_company_name AS company, 
                            CONCAT(jobseeker.jobseeker_first_name, ' ', jobseeker.jobseeker_last_name) AS jobseeker_name, 
                            CONCAT(recruiter.recruiter_first_name, ' ', recruiter.recruiter_last_name) AS recruiter_name,
                            recruiter.recruiter_company_name AS company,
                            questions.question
                        FROM quiz_videos AS video
                        INNER JOIN assessments ON assessments.id = video.assessment_id
                        INNER JOIN quizzes AS test ON test.id = video.quiz_id
                        INNER JOIN jobs AS job ON job.job_id = assessments.job_id
                        INNER JOIN recruiter ON assessments.creator_id = recruiter.recruiter_id
                        INNER JOIN jobseeker ON jobseeker.jobseeker_id = video.jobseeker_id
                        INNER JOIN questions ON questions.id = video.question_id
                        ORDER BY video.created_at ASC
                        LIMIT $offset, $perPage";

    $res = tep_db_query($list_test_query);

    if (tep_db_num_rows($res) > 0) {
        while ($data = tep_db_fetch_array($res)) {
            $video_path = tep_href_link($data['file_path']);
            $encode_vid_id = encode_string("video_id==".$data['id']."==video");
            $template->assign_block_vars('video_lib', array(
                'name'       => '<a href="'.tep_href_link(PATH_TO_QUIZ.FILENAME_RECRUITER_ASSESSMENT,'action=video&v_id='.$encode_vid_id).'">
                                    <video controls width="125"><source src="'.$video_path.'" type="video/webm"></video>
                                </a>',
                'file_path'  => $data['file_path'],
                'created_at' => tep_date_short($data['created_at']),
                'assessment' => $data['assessment'],
                'seeker_name' => $data['jobseeker_name'],
                'recruiter_name' => $data['recruiter_name'],
                'test'       => $data['test'],
                'job_title'  => $data['job_title'],
                'company'  => $data['company'],
                'question'    => $data['question'],
            ));
        }
        tep_db_free_result($res);
        return true;
    }

    return false;
}

function get_list_of_essay(int $offset, int $perPage)
{
    global $template;

    // get the list of assign test
    $list_test_query = "SELECT quiz_essay_answers.*, 
                            assessments.title AS assessment, 
                            test.title AS test, 
                            job.job_title, 
                            recruiter.recruiter_company_name AS company, 
                            CONCAT(jobseeker.jobseeker_first_name, ' ', jobseeker.jobseeker_last_name) AS jobseeker_name, 
                            CONCAT(recruiter.recruiter_first_name, ' ', recruiter.recruiter_last_name) AS recruiter_name,
                            recruiter.recruiter_company_name AS company,
                            questions.question
                        FROM quiz_essay_answers
                        INNER JOIN assessments ON assessments.id = quiz_essay_answers.assessment_id
                        INNER JOIN quizzes AS test ON test.id = quiz_essay_answers.quiz_id
                        INNER JOIN jobs AS job ON job.job_id = assessments.job_id
                        INNER JOIN recruiter ON assessments.creator_id = recruiter.recruiter_id
                        INNER JOIN jobseeker ON jobseeker.jobseeker_id = quiz_essay_answers.jobseeker_id
                        INNER JOIN questions ON questions.id = quiz_essay_answers.question_id
                        ORDER BY quiz_essay_answers.created_at ASC
                        LIMIT $offset, $perPage";

    $res = tep_db_query($list_test_query);

    if (tep_db_num_rows($res) > 0) {
        while ($data = tep_db_fetch_array($res)) {
            $template->assign_block_vars('essay_lib', array(
                'name'       => $data['answer'],
                'created_at' => tep_date_short($data['created_at']),
                'assessment' => $data['assessment'],
                'seeker_name' => $data['jobseeker_name'],
                'recruiter_name' => $data['recruiter_name'],
                'test'       => $data['test'],
                'job_title'  => $data['job_title'],
                'company'  => $data['company'],
                'question'    => $data['question'],
            ));
        }
        tep_db_free_result($res);
        return true;
    }

    return false;
}





























/**
 * if action is preview show detail and add data in global vaiable
 * if not then fetch all data
 */
if ($action == 'employer_test') {
    $article_query_raw = "SELECT quiz.*, recruiter.recruiter_company_name AS company, COUNT(questions.quiz_id) AS total_ques
                            FROM quizzes AS quiz
                            INNER JOIN recruiter ON recruiter.recruiter_id = quiz.recruiter_id
                            LEFT JOIN questions ON questions.quiz_id = quiz.id
                            WHERE quiz.recruiter_id IS NOT NULL
                            GROUP BY questions.quiz_id
                            ORDER BY quiz.created_at DESC";

    $article_query = tep_db_query($article_query_raw);
    if (tep_db_num_rows($article_query) > 0) {
        while ($article = tep_db_fetch_array($article_query)) {
            $alternate++;
            $template->assign_block_vars('quizs', array(
                'id' => tep_db_output($article['id']),
                'title' => '<a href="' . tep_href_link(PATH_TO_QUIZ . $article['id'] . '/' . encode_forum($article['title']) . '.html') . '" target="_blank" rel="noreferrer">
                                ' . tep_db_output($article['title']) .
                    '</a>',
                'created_at' => tep_date_short($article['created_at']),
                'company' => $article['company'],
                'messages_link' => getMessageLink(tep_db_output($article['id'])),
                
                'save_as_template' => '',
                
                'total_question' => tep_db_output($article['total_ques']),

                'active_inactive' => ($article['isActive'] == 1)
                    ? on_off_toggle_link_btn($article['id'], 'test_inactive', $article['isActive'])
                    : on_off_toggle_link_btn($article['id'], 'test_active', $article['isActive']),
                
                'action' => getAction(tep_db_output($article['id'])),
            ));
        }
        tep_db_free_result($article_query);
    }
} else {
    // Fetch All Quizzes
    // $article_query_raw = "SELECT * FROM " . QUIZ_TABLE . " as quiz WHERE recruiter_id IS NULL ORDER BY quiz.created_at DESC";
    $article_query_raw = "SELECT quizzes.*, COUNT(questions.quiz_id) AS total_ques
                            FROM quizzes
                            LEFT JOIN questions ON questions.quiz_id = quizzes.id
                            WHERE quizzes.recruiter_id IS NULL 
                            GROUP BY questions.quiz_id
                            ORDER BY quizzes.created_at DESC";

    $article_query = tep_db_query($article_query_raw);
    if (tep_db_num_rows($article_query) > 0) {
        while ($article = tep_db_fetch_array($article_query)) {
            $alternate++;
            $template->assign_block_vars('quizs', array(
                'id' => tep_db_output($article['id']),
                // 'title' => '<a href="' . tep_href_link(PATH_TO_QUIZ . $article['id'] . '/' . encode_forum($article['title']) . '.html') . '" target="_blank" rel="noreferrer">
                //                 ' . tep_db_output($article['title']) .
                //     '</a>',
                'title' => '<a href="' . tep_href_link(PATH_TO_ADMIN. FILENAME_ADMIN1_LIST_OF_QUIZ . '?id='.$article['id'].'&action=preview') . '" ">
                                ' . tep_db_output($article['title']) .
                    '</a>',
                
                'add_question' => '<a class="" href="' . tep_href_link(PATH_TO_ADMIN . FILENAME_ADMIN1_LIST_OF_QUESTIONS) . '?quiz_id='.$article['id'].'&action=new">
                                    add question
                                </a>',
                
                'total_question' => '<a href="'.tep_href_link(PATH_TO_ADMIN . FILENAME_ADMIN1_LIST_OF_QUESTIONS, 'quiz_id=' . tep_db_output($article['id']) . '&action=allQuestion').'">
                                        '.tep_db_output($article['total_ques']).'                    
                                    </a>',

                'created_at' => tep_date_short($article['created_at']),
                
                'messages_link' => getMessageLink(tep_db_output($article['id'])),
                
                'save_as_template' => ($article['save_as_template'] == 1)
                    ? on_off_toggle_link_btn($article['id'], 'test_not_save_as_template', $article['save_as_template'])
                    : on_off_toggle_link_btn($article['id'], 'test_save_as_template', $article['save_as_template']),
                
                'active_inactive' => ($article['isActive'] == 1)
                    ? on_off_toggle_link_btn($article['id'], 'test_inactive', $article['isActive'])
                    : on_off_toggle_link_btn($article['id'], 'test_active', $article['isActive']),
                
                'action' => getAction(tep_db_output($article['id'])),

                'interface' => ($article['interface_type'] == 1)
                    ? interface_toggle_link_btn($article['id'], 'interface_false', $article['interface_type'])
                    : interface_toggle_link_btn($article['id'], 'interface_true', $article['interface_type']),
            ));
        }
        tep_db_free_result($article_query);
    }
}

// interface of test ui change
if ($action AND ($_SERVER['REQUEST_METHOD'] == 'POST') AND ($_POST['_method'] == 'put')) {
    if (in_array($action, ["interface_true", "interface_false"])) {
        $intVal = ($action == 'interface_true') ? true : false;
        tep_db_query("update " . QUIZ_TABLE . " set interface_type='".$intVal."' where id='" . $quiz_id . "' AND recruiter_id IS NULL");
        $messageStack->add_session(INTERFACE_UPDATED, 'success');
        tep_redirect(FILENAME_ADMIN1_LIST_OF_QUIZ);
    } else {
        $messageStack->add_session(MESSAGE_TYPE_ERROR, 'error');
        tep_redirect(FILENAME_ADMIN1_LIST_OF_QUIZ);
    }
}



// Default Values
$template->assign_vars(array(
    // 'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
    'update_message' => $messageStack->output(),
    
    'new_button' => '<a 
            class="btn btn-primary float-left" 
            href="' . tep_href_link(PATH_TO_ADMIN . FILENAME_ADMIN1_LIST_OF_QUIZ, 'action=new') . '">
            <i class="fa fa-plus" aria-hidden="true"></i> ' . ADD_TEXT . '
        </a>
    ',

    'back_btn' => '<a 
            class="btn btn-outline-secondary float-right" 
            href="' . tep_href_link(PATH_TO_ADMIN . FILENAME_ADMIN1_LIST_OF_QUIZ) . '">
            ' . BACK_BTN . '
        </a>
    ',

    'test_menus' => '

        <a 
            class="btn-link mr-2 float-right" 
            href="' . tep_href_link(PATH_TO_QUIZ) . '">
            View Online
        </a>

        <a 
            class="btn-link mr-2 float-right" 
            href="' . tep_href_link(PATH_TO_ADMIN . FILENAME_ADMIN1_LIST_OF_QUIZ) . '">
            ' . ADMIN_TEST . '
        </a>
        <a 
            class="btn-link mr-2 float-right" 
            href="' . tep_href_link(PATH_TO_ADMIN . FILENAME_ADMIN1_LIST_OF_QUESTIONS) . '">
            ' . ADD_NEW_QUESTION . '
        </a>
        
        <a 
            class="btn-link mr-2 float-right" 
            href="' . tep_href_link(PATH_TO_ADMIN . FILENAME_ADMIN1_TEST_CATEGORY) . '">
            ' . TEST_CATEGORY . '
        </a>
    ',
));


// Render to htm files based on condition
if ($action == 'new' || $action == 'edit') {

    if (tep_not_null(findDataWithQuizId()->picture) && is_file(PATH_TO_MAIN_PHYSICAL_FORUM_IMAGE . findDataWithQuizId()->picture)) {
        $artcle_photo1 = "&nbsp;&nbsp;[&nbsp;&nbsp;<a href='#' onclick=\"javascript:popupimage('" . HOST_NAME . PATH_TO_MAIN_PHYSICAL_FORUM_IMAGE . findDataWithQuizId()->picture . "','')\" class='label'>Preview</a>&nbsp;&nbsp;]";
    }

    // Go to create or edit form
    $template->assign_vars(array(
        'HEADING_TITLE' => ($action == 'edit') ? EDIT_TEXT : ADD_TEXT,
        'TITLE_LABEL' => TITLE_LABEL,
        'TIMER' => 'Timer',
        'TEST_CAT' => 'Test Category',
        'IS_CATEGORY_ERROR' => ($error) ? '<span class="text-danger">' . 'Select category' . '</span>' : '',
        'categoires' => ($action == 'edit') ? category_lists(findDataWithQuizId()->test_category_id) : category_lists(),
        'DESCRIPTION_LABEL' => DESCRIPTION_LABEL,
        'ID' => $quiz_id,
        'form' => ($action == 'new') ? getFormTag($action) : getFormTag($action, $quiz_id),
        'INPUT_TITLE' => tep_draw_input_field('title', findDataWithQuizId()->title, 'class="form-control" id="title" required', '', 'text'),
        'INPUT_TIMER' => tep_draw_input_field('timer', findDataWithQuizId()->timer, 'class="form-control" id="timer" placeholder="Enter time limit for test in minute" required', '', 'number'),
        'TEXTAREA_DESCRIPTION' => tep_draw_textarea_field('description', 'soft', '30', '10', stripslashes(findDataWithQuizId()->description), 'class="form-control" id="description"', '', true),
        'BUTTON' => ($action == 'edit') ? tep_button_submit('btn btn-primary float-right', UPDATE_BUTTON) : tep_button_submit('btn btn-primary float-right', SUBMIT_BUTTON),
        'IS_TITLE_ERROR' => ($errorTitle) ? '<span class="text-danger">' . TITLE_REQUIRED . '</span>' : '',
        'IS_DESCRIPTION_ERROR' => ($errorDescription) ? '<span class="text-danger">' . DESCRIPTION_REQUIRED . '</span>' : '',
        'IS_TIMER_ERROR' => ($errorTimer) ? '<span class="text-danger">Required and only a numeric value required</span>' : '',
        'INFO_TEXT_QUIZ_PHOTO'  => INFO_TEXT_QUIZ_PHOTO,
        'INFO_TEXT_QUIZ_PHOTO1' => tep_draw_file_field("picture") . $artcle_photo1,
    ));
    $template->pparse('create_update_form');
} elseif ($action == 'preview') {
    // find data with id and his details
    $template->assign_vars(array(
        'HEADING_TITLE' => viewDataWithQuizId()->title,
        'DESCRIPTION_LABEL' => DESCRIPTION_LABEL,
        'BACK_BUTTON' => '
            <a class="btn btn-link mt-2 float-right" href="' . tep_href_link(PATH_TO_ADMIN . FILENAME_ADMIN1_LIST_OF_QUIZ) . '">
                <i class="fa fa-arrow-circle-left" aria-hidden="true"></i>' . BACK_BUTTON . '
            </a>',
        'add_question_link_btn' => '
            <a class="btn btn-link mt-2 float-right" href="' . tep_href_link(PATH_TO_ADMIN . FILENAME_ADMIN1_LIST_OF_QUESTIONS) . '?quiz_id='.viewDataWithQuizId()->id.'&action=new">
                Add question
            </a>',
        'DESCRIPTION' => viewDataWithQuizId()->description,
        'new_button' => '
            <a 
                class="btn btn-primary" 
                href="' . tep_href_link(PATH_TO_ADMIN . FILENAME_ADMIN1_LIST_OF_QUIZ, 'action=new') . '">
                <i class="fa fa-plus" aria-hidden="true"></i> ' . ADD_TEXT . '
            </a>
        ',

    ));
    $template->pparse('preview');
}elseif ($action == 'video-list') {
    get_list_of_videos($startAt, $perPage);

    $template->assign_vars(array(
        'VIDEO_TITLE'               => VIDEO_TITLE . ' <span class="badge badge-info">'.get_pagination_for_videos()['totalData'].'</span>',
        'TB_HEAD_VIDEO'             => TB_HEAD_VIDEO,
        'TB_HEAD_TEST_NAME'         => TB_HEAD_TEST_NAME,
        'TB_HEAD_ASSESSMENT_NAME'   => TB_HEAD_ASSESSMENT_NAME,
        'TH_COMPANY_NAME'           => TH_COMPANY_NAME,
        'TB_HEAD_DATE'              => TB_HEAD_DATE_1,
        'TB_HEAD_JOB'               => TB_HEAD_JOB,
        'TB_HEAD_QUESTION'          => TB_HEAD_QUESTION,
        'NOT_FOUND_DATA'            => $no_table_data_found,
        'TB_HEAD_SEEKER'            => TB_HEAD_SEEKER,
        'TB_HEAD_RECRUITER'         => TB_HEAD_RECRUITER,
        'PAGINATION_LINK'           => get_pagination_for_videos()['pagination'],
    ));

    $template->pparse('video_list');
} elseif ($action == 'essay-list') {
    get_list_of_essay($startAt, $perPage);

    $template->assign_vars(array(
        'ESSAY_TITLE'               => ESSAY_TITLE . ' <span class="badge badge-info">'.get_pagination_for_essay()['totalData'].'</span>',
        'TB_HEAD_ESSAY'             => TB_HEAD_ESSAY,
        'TB_HEAD_TEST_NAME'         => TB_HEAD_TEST_NAME,
        'TB_HEAD_ASSESSMENT_NAME'   => TB_HEAD_ASSESSMENT_NAME,
        'TH_COMPANY_NAME'           => TH_COMPANY_NAME,
        'TB_HEAD_DATE'              => TB_HEAD_DATE_1,
        'TB_HEAD_JOB'               => TB_HEAD_JOB,
        'TB_HEAD_QUESTION'          => TB_HEAD_QUESTION,
        'NOT_FOUND_DATA'            => $no_table_data_found,
        'TB_HEAD_SEEKER'            => TB_HEAD_SEEKER,
        'TB_HEAD_RECRUITER'         => TB_HEAD_RECRUITER,
        'PAGINATION_LINK'           => get_pagination_for_essay()['pagination'],
    ));

    $template->pparse('essay_list');
} else {
    // List of Quiz page return
    $template->assign_vars(array(
        'HEADING_TITLE' => ($action == 'employer_test') ? EMPLOYER_TEST : TEST_LIBRARY,
        'TABLE_HEADING_TITLE' => TABLE_HEADING_TITLE,
        'TABLE_HEADING_DESCRIPTION' => TABLE_HEADING_DESCRIPTION,
        'TABLE_HEADING_DATE_ADDED' => TABLE_HEADING_DATE_ADDED,
        'ADD_REPORT_MESSAGES' => ADD_REPORT_MESSAGES,
        'TABLE_HEADING_ACTION' => TABLE_HEADING_ACTION,
        'INTERFACE_UPDATE' => INTERFACE_UPDATE,
        'TABLE_HEADING_COMPANY' => ($action == 'employer_test') ? TABLE_HEADING_COMPANY : '',
        'TH_ADD_QUESTION' => ($action == 'employer_test') ? '' : TH_ADD_QUESTION,
        'TH_TOTAL_QUESTION' => TH_TOTAL_QUESTION,
        'CHECKBOX_SAVE_AS_TEMPLATE' => ($action == 'employer_test') ? '' : CHECKBOX_SAVE_AS_TEMPLATE,
        'CHECKBOX_ACTIVE_INACTIVE' => CHECKBOX_ACTIVE_INACTIVE,
    ));
    $template->pparse('all_quiz');
}
