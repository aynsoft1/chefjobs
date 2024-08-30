<?php
// Initial Setup
include_once("../include_files.php");
include_once(PATH_TO_MAIN_QUIZ_PHYSICAL_LANGUAGE . $language . '/' . FILENAME_RECRUITER_QUIZ_MESSAGES);
$template->set_filenames(
    array(
        'list_messages' => 'quiz_message/list_message.htm',
        'create_update_form' => 'quiz_message/form_message.htm',
    )
);
include_once("../" . FILENAME_BODY);

// check if recruiter is logged in  or not
if (!check_login("recruiter")) {
    $_SESSION['REDIRECT_URL'] = $_SERVER['REQUEST_URI'];
	$messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
	tep_redirect(tep_href_link(FILENAME_RECRUITER_LOGIN));
}
// job_seeker id get
if (check_login('recruiter')) {
    $recruiter_id   = $_SESSION['sess_recruiterid'];
    $user_type = 'recruiter';
}

// global Properties
$action = (isset($_GET['action']) ? $_GET['action'] : '');
$quiz_id = (isset($_GET['id']) ? tep_db_prepare_input($_GET['id']) : '');
$message_id = (isset($_GET['message_id']) ? tep_db_prepare_input($_GET['message_id']) : '');
$edit = false;
$error = false;
$currentDate = date("Y-m-d H:i:s"); // current date

/**
 * Check Condition if id is present in table or not
 */
if (tep_not_null($quiz_id)) {
    if (!$row_check_quiz_id = getAnyTableWhereData(QUIZ_TABLE, "id='" . tep_db_input($quiz_id) . "'")) {
        $messageStack->add_session(MESSAGE_QUIZ_ERROR, 'error');
        tep_redirect(FILENAME_RECRUITER_LIST_OF_QUIZ);
    }
    $quiz_id = $row_check_quiz_id['id'];
    $edit = true;
}

/**
 * find quiz_message
 */
if (tep_not_null($message_id)) {
    if (!$row_check_message_id = getAnyTableWhereData(QUIZ_MESSAGE_TABLE, "id='" . tep_db_input($message_id) . "'")) {
        $messageStack->add_session(MESSAGE_QUESTION_ERROR, 'error');
        tep_redirect(FILENAME_RECRUITER_LIST_OF_QUESTIONS);
    }
    $message_id = $row_check_message_id['id'];
    $edit = true;
}

function findQuizDataWithQuizId()
{
    global $error, $action, $row_check_quiz_id;
    if (!$error && ($action == 'edit' || $action == 'new' || $action == 'listMessage')) {
        $data = new objectInfo($row_check_quiz_id);
        return $data;
    }
    return false;
}

function findMessageWithId()
{
    global $error, $action, $row_check_message_id;
    if (!$error && $action == 'edit') {
        $data = new objectInfo($row_check_message_id);
        return $data;
    }
    return false;
}

// Default Values
$template->assign_vars(array(
    'quiz_menus' => '<a class="btn-text  mr-2" href="' . tep_href_link(FILENAME_QUIZ . '/' . FILENAME_RECRUITER_LIST_OF_QUIZ) . '" class="hm_color">Add Test</a>
                    <a class="btn-text  mr-2" href="' . tep_href_link(FILENAME_QUIZ . '/' . FILENAME_RECRUITER_LIST_OF_QUESTIONS) . '" class="hm_color">Test Questions</a>
                    <a class="btn-text  mr-2" href="' . tep_href_link(FILENAME_QUIZ . '/' . FILENAME_RECRUITER_QUIZ_REPORT) . '" class="hm_color">Test Report</a>
    ',
    'update_message' => $messageStack->output()
));

// get the starting form tag <form> not </form> -> this would be manually added in the htm file
function getFormTag($actionValue, $dataID = null)
{
    switch ($actionValue) {
        case 'new':
            return tep_draw_form('question formElement', PATH_TO_QUIZ . FILENAME_RECRUITER_QUIZ_MESSAGES, 'action=submitForm', 'post', 'id="formElement" enctype="multipart/form-data"');
            break;
        case 'edit':
            return tep_draw_form('question formElement', PATH_TO_QUIZ . FILENAME_RECRUITER_QUIZ_MESSAGES, 'message_id=' . $dataID . '&action=updateForm', 'post', 'id="formElement" enctype="multipart/form-data"');
            break;
    }
}

// perform insert data in quiz_messages table
function storeForm(array $sql_data)
{
    $data = tep_db_perform(QUIZ_MESSAGE_TABLE, $sql_data);
    return $data;
}

// perform update the data in quiz_messages table
function updateForm(array $sql_data)
{
    global $message_id;
    $data = tep_db_perform(QUIZ_MESSAGE_TABLE, $sql_data, 'update', "id='" . $message_id . "'");
    return $data;
}

// perform delete quiz action
function deleteMessage(int $id)
{
    $data = tep_db_query("delete from " . QUIZ_MESSAGE_TABLE . " where id='" . tep_db_input($id) . "'");
    return $data;
}

function getAction(int $messageId, int $quizId)
{
    $button = '
    <div class="btn-group">
        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            Action
        </button>
        <div class="dropdown-menu">
            <a class="dropdown-item" href="' . tep_href_link(PATH_TO_QUIZ . FILENAME_RECRUITER_QUIZ_MESSAGES, 'id=' . $quizId . '&message_id='.$messageId.'&action=edit') . '" name="editBtn" id="editBtn-'.$messageId.'">
                ' . EDIT_TEXT . '
            </a>
            <a class="dropdown-item" href="' . tep_href_link(PATH_TO_QUIZ . FILENAME_RECRUITER_QUIZ_MESSAGES, 'message_id=' . $messageId . '&action=confirm_delete&id='.$quizId) . '" 
                name="deleteBtn" 
                id="deleteBtn-'.$messageId.'">
                ' . DELETE_TEXT . '
            </a>
        </div>
    </div>
    ';
    return $button;
}

// form store and update and delete case
if (tep_not_null($action)) {
    $quiz_minValue = tep_db_prepare_input($_POST['minValue']);
    $quiz_maxValue = tep_db_prepare_input($_POST['maxValue']);
    $input_quiz_id = tep_db_prepare_input($_POST['quiz_id']);
    $quiz_message  = stripslashes($_POST['message']);

    switch ($action) {
        case 'submitForm':
            // validationFormCheck($quiz_message, $quiz_minValue, $quiz_maxValue,  'new');
            if (!$error) {
                $store_data_array = array(
                    'min_value'   => $quiz_minValue,
                    'max_value'   => $quiz_maxValue,
                    'quiz_id'    => $input_quiz_id,
                    'message'    => $quiz_message,
                    'created_at' => $currentDate,
                    'updated_at' => $currentDate,
                );
                storeForm($store_data_array);
                $messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');
                return tep_redirect(FILENAME_RECRUITER_QUIZ_MESSAGES . '?id=' . $input_quiz_id . '&action=listMessage');
            }
            break;
        case 'updateForm':
            if (!$error) {
                $update_data_array = array(
                    'min_value'   => $quiz_minValue,
                    'max_value'   => $quiz_maxValue,
                    'message'    => $quiz_message,
                    'updated_at' => $currentDate,
                );
                updateForm($update_data_array);
                $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
                return tep_redirect(FILENAME_RECRUITER_QUIZ_MESSAGES . '?id=' . $input_quiz_id . '&action=listMessage');
            }
            break;
        case 'confirm_delete':
            deleteMessage($message_id);
            $messageStack->add_session(MESSAGE_SUCCESS_DELETED, 'success');
            tep_redirect(FILENAME_RECRUITER_QUIZ_MESSAGES . '?id=' . $quiz_id . '&action=listMessage');
            break;
    }
}

// fetch quiz all messages
if (tep_not_null($quiz_id) && $action == 'listMessage') {
    // Fetch All Messages
    $message_query_raw = "SELECT * FROM " . QUIZ_MESSAGE_TABLE . " WHERE quiz_id = $quiz_id ORDER BY created_at DESC";
    $message_query = tep_db_query($message_query_raw);
    if (tep_db_num_rows($message_query) > 0) {
        while ($msg = tep_db_fetch_array($message_query)) {
            $alternate++;
            $template->assign_block_vars('quiz_msgs', array(
                'id' => tep_db_output($msg['id']),
                'message' => tep_db_output($msg['message']),
                'min_value' => tep_db_output($msg['min_value']),
                'max_value' => tep_db_output($msg['max_value']),
                'created_at' => tep_date_short($msg['created_at']),
                'action' => getAction(tep_db_output($msg['id']), $quiz_id),
            ));
        }
        tep_db_free_result($message_query);
    }
}


if (tep_not_null($quiz_id) && $action == 'listMessage') {
    $template->assign_vars(array(
        'HEADING_TITLE' => findQuizDataWithQuizId()->title.' '.HEADING_TITLE,
        'TABLE_HEADING_TITLE' => TABLE_HEADING_TITLE,
        'TABLE_HEADING_MIN' => MIN_VALUE,
        'TABLE_HEADING_MAX' => MAX_VALUE,
        'TABLE_HEADING_DATE_ADDED' => TABLE_HEADING_DATE_ADDED,
        'TABLE_HEADING_ACTION' => TABLE_HEADING_ACTION,
        'new_button' => '
            <a 
                class="btn btn-primary" 
                href="' . tep_href_link(PATH_TO_QUIZ . FILENAME_RECRUITER_QUIZ_MESSAGES, 'action=new&id=' . $quiz_id) . '">
                <i class="fa fa-plus" aria-hidden="true"></i> ' . ADD_TEXT . '
            </a>
        ',
    ));
    $template->pparse('list_messages');
} elseif (tep_not_null($quiz_id) && ($action == 'new' || $action == 'edit')) {
    $template->assign_vars(array(
        'HEADING_TITLE' => ($action == 'edit') ? EDIT_TEXT . ' ' . findQuizDataWithQuizId()->title : ADD_TEXT . ' ' . findQuizDataWithQuizId()->title,
        'ID' => $quiz_id,
        'form' => ($action == 'new') ? getFormTag($action) : getFormTag($action, $message_id),
        'hidden_quiz_id_input_field' => tep_draw_hidden_field('quiz_id', tep_db_output($quiz_id)),
        'TITLE_LABEL' => TITLE_LABEL,
        'MIN_LABEL' => MIN_LABEL,
        'MAX_LABEL' => MAX_LABEL,
        'QUESTION_INPUT' => tep_draw_textarea_field('message', 'soft', '30', '5', stripslashes(findMessageWithId()->message), 'class="form-control" id="message"', '', true),
        'MIN_INPUT' => tep_draw_input_field('minValue', findMessageWithId()->min_value, 'class="form-control" id="min" min="0" autocomplete="off"', '', 'number'),
        'MAX_INPUT' => tep_draw_input_field('maxValue', findMessageWithId()->max_value, 'class="form-control" id="max" min="0" autocomplete="off"', '', 'number'),
        // 'TITLE_ERROR' => ($errorQuestion) ? '<span class="text-danger">' . TITLE_ERROR . '</span>' : '',
        // 'MIN_INPUT_ERROR' => ($errorQuestion) ? '<span class="text-danger">' . POINT_INPUT_ERROR . '</span>' : '',
        // 'MAX_INPUT_ERROR' => ($errorQuestion) ? '<span class="text-danger">' . POINT_INPUT_ERROR . '</span>' : '',
        'TITLE_ERROR' => '<span id="messagecheck" style="color: red;">' . TITLE_ERROR . '</span>',
        'MIN_ERROR' => '<span id="mincheck" style="color: red;">' . MIN_ERROR . '</span>',
        'MAX_ERROR' => '<span id="maxcheck" style="color: red;">' . MAX_ERROR . '</span>',
        'BUTTON' => ($action == 'edit') ? tep_button_submit('btn btn-primary float-right', UPDATE_BUTTON, 'name="submitBtn" id="submitBtn"') : tep_button_submit('btn btn-primary float-right', SUBMIT_BUTTON, 'name="submitBtn" id="submitBtn"'),
    ));
    $template->pparse('create_update_form');
}
