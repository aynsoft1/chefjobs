<?php
// Initial Setup
include_once("../include_files.php");
include_once(PATH_TO_MAIN_QUIZ_PHYSICAL_LANGUAGE . $language . '/' . FILENAME_RECRUITER_LIST_OF_QUIZ);
$template->set_filenames(
    array(
        'all_test' => 'quiz/all_test.htm',
        'create_update_form' => 'quiz/store_test.htm',
        'preview' => 'quiz/view_test.htm',
        'select_test_form' => 'quiz/select_test.htm',
    )
);
include_once("../" . FILENAME_BODY);

// global Properties
$action = (isset($_GET['action']) ? $_GET['action'] : '');
$jobId = (isset($_GET['jobID']) ? $_GET['jobID'] : '');
$quiz_id = (isset($_GET['id']) ? tep_db_prepare_input($_GET['id']) : '');
$edit = false;
$error = false;
$currentDate = date("Y-m-d H:i:s"); // current date
// default assessment type mcq set
// at current we use only mcq other enums are: 'admin_test_library','mcq','essay','video'
$assessmentType = (isset($_GET['type']) ? $_GET['type'] : 'mcq');
$typeArray  = ['mcq', 'video', 'essay'];

// input fields
$testTitle  = $_POST['title'];
$testTimer  = $_POST['timer'];
$quesName   = $_POST['question'];
$quesChoice = $_POST['question_choice'];
$quesPoint  = $_POST['points'];


// check if recruiter is logged in  or not
if (!check_login("recruiter")) {
    $_SESSION['REDIRECT_URL'] = $_SERVER['REQUEST_URI'];
    $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
    tep_redirect(tep_href_link(FILENAME_RECRUITER_LOGIN));
} else {
    $recruiter_id   = $_SESSION['sess_recruiterid'];
    $user_type = 'recruiter';
}

// perform insert data in quizzes table
function add_test(array $test_data)
{
    $link = tep_db_connect();

    tep_db_perform(QUIZ_TABLE, $test_data);

    $id = mysqli_insert_id($link);

    // return latest id of stored value
    return $id;
}

function add_message(array $data)
{
    $link = tep_db_connect();

    tep_db_perform(QUIZ_MESSAGE_TABLE, $data);

    $id = mysqli_insert_id($link);

    // return latest id of stored value
    return $id;
}

function add_question(array $data)
{
    $link = tep_db_connect();

    tep_db_perform(QUES_TABLE, $data);

    $id = mysqli_insert_id($link);

    // return latest id of stored value
    return $id;
}

function add_choices(array $data)
{
    $link = tep_db_connect();

    tep_db_perform(QUES_CHOICE_TABLE, $data);

    $id = mysqli_insert_id($link);

    // return latest id of stored value
    return $id;
}


// update test
function update_test(array $test_data, $testId)
{
    $data = tep_db_perform(QUIZ_TABLE, $test_data, 'update', "id='" . $testId . "'");

    return $data;
}

// update question
function update_question(array $data, $quesId)
{
    $data = tep_db_perform(QUES_TABLE, $data, 'update', "id='" . $quesId . "'");
    
    return $data;
}

function update_msg(array $data, $msgId)
{
    $data = tep_db_perform(QUIZ_MESSAGE_TABLE, $data, 'update', "id='" . $msgId . "'");
    
    return $data;
}

// perform delete quiz action
function delete_test(int $test_id, int $recruiter_id)
{
    $data = tep_db_query("delete from " . QUIZ_TABLE . " where id='$test_id, int $recruiter_id' AND recruiter_id = '$recruiter_id'");
    return $data;
}

// get the starting form tag <form> not </form> -> this would be manually added in the htm file
function getFormTag($actionValue, $quizId = null)
{
    global $assessmentType;

    switch ($actionValue) {
        case 'new':
            return tep_draw_form('quiz', PATH_TO_QUIZ . FILENAME_RECRUITER_LIST_OF_QUIZ, 'action=submit_test&type='.$assessmentType, 'post', ' enctype="multipart/form-data"');
            break;
        case 'edit':
            return tep_draw_form('article', PATH_TO_QUIZ . FILENAME_RECRUITER_LIST_OF_QUIZ, 'id=' . $quizId . '&action=update_test&type='.$assessmentType, 'post', 'enctype="multipart/form-data"');
            break;
    }
}

function getAction($quizId, $type,  $job_id)
{
    $onclickEvent = "event.preventDefault();if(confirm('Are you sure!')){document.getElementById('form-delete-$quizId').submit()}";

    $button = '
    <div class="btn-group">
        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            Action
        </button>
        <div class="dropdown-menu">
            <a class="dropdown-item" href="' . tep_href_link(PATH_TO_QUIZ . FILENAME_RECRUITER_LIST_OF_QUIZ, 'id=' . $quizId . '&action=edit&type='.$type) . '">
                ' . EDIT_TEXT . '
            </a>
            <a class="dropdown-item" href="' . tep_href_link(PATH_TO_QUIZ . FILENAME_RECRUITER_LIST_OF_QUIZ, 'id=' . $quizId . '&action=preview') . '">
                ' . VIEW_TEXT . '
            </a>
            <a class="dropdown-item" href="#" onclick="'.$onclickEvent.'">'
                . DELETE_TEXT . '
            </a>
            <form style="display:none" 
                method="post" 
                id="form-delete-'.$quizId.'"
                action="' . tep_href_link(PATH_TO_QUIZ . FILENAME_RECRUITER_LIST_OF_QUIZ, 'id=' . $quizId . '&action=confirm_delete') . '">
                <input name="_method" type="hidden" value="delete" />
            </form>
        </div>
    </div>
    ';
    return $button;
}

function interface_toggle_link_btn(int $test_id, string $action_name, int $value)
{
    $onclickEvent = "event.preventDefault();if(confirm('Are you sure!')){document.getElementById('form-interface-$test_id').submit()}";

    if ($value == 1) {
        $toggle_on_off = '
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-toggle-on text-primary" viewBox="0 0 16 16">
                                <path d="M5 3a5 5 0 0 0 0 10h6a5 5 0 0 0 0-10H5zm6 9a4 4 0 1 1 0-8 4 4 0 0 1 0 8z"/>
                            </svg>
        ';
    } else {
        $toggle_on_off =  '
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-toggle-off text-secondary" viewBox="0 0 16 16">
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
                    action="' . tep_href_link(PATH_TO_QUIZ . FILENAME_RECRUITER_LIST_OF_QUIZ, 'id=' . $test_id . '&action='.$action_name) . '">
                    <input name="_method" type="hidden" value="put" />
                </form>';

    return $linkBtn;
}

function getMessageLink($quizId)
{
    $button = '
        <a class="btn btn-link" href="' . tep_href_link(PATH_TO_QUIZ . FILENAME_RECRUITER_QUIZ_MESSAGES, 'id=' . $quizId . '&action=listMessage') . '">
            ' . ADD_REPORT . '
        </a>    
    ';
    return $button;
}

function find_test_with_an_id_and_get($test_id, int $recruiter_id)
{
    global $template;

    $quiz_table = QUIZ_TABLE;
    $ques_table = QUES_TABLE;

    if ($test_id) {
        $test_query = "SELECT q.*, ques.id as ques_id, ques.question 
                    FROM $quiz_table as q
                    JOIN $ques_table as ques ON ques.quiz_id = q.id 
                    WHERE q.id = $test_id AND q.isActive = 1 AND q.recruiter_id = $recruiter_id";

        $result = tep_db_query($test_query);
        $data = tep_db_fetch_array($result);


        // get the test questions
        $ques_query = "SELECT questions.*, quizzes.title AS test_name, quizzes.created_at AS test_created 
                        FROM questions
                        INNER JOIN quizzes ON quizzes.id = questions.quiz_id
                        WHERE questions.quiz_id = $test_id AND quizzes.recruiter_id = $recruiter_id";
        
        $ques_res = tep_db_query($ques_query);

        if (tep_db_num_rows($ques_res) > 0) {
            while ($quesData = tep_db_fetch_array($ques_res)) {

                $template->assign_block_vars('questions', array(
                    'ques_name'  => $quesData['question'],
                    'ques_date' => tep_date_short($quesData['created_at']),
                ));
            }
            tep_db_free_result($ques_res);
        }

        return $data;
    }

    return false;
}

/**
 * This function used at update the question for quiz
 *
 */
function get_list_of_question_id_for_quiz($quiz_id)
{
    // get questions for quiz id
    $ques_query = "SELECT questions.id, questions.question
                    FROM questions
                    INNER JOIN quizzes ON quizzes.id = questions.quiz_id
                    WHERE questions.quiz_id = $quiz_id LIMIT 1";

    $ques_res = tep_db_query($ques_query);

    if (tep_db_num_rows($ques_res) > 0) {
        while ($res = tep_db_fetch_array($ques_res)) {
            $ids = $res['id']; 
        }

        return $ids;
    }

    return false;
}

function get_list_of_quiz_messages($quiz_id)
{
    // get questions for quiz id
    $msg_query = "SELECT msg.* FROM quiz_messages AS msg WHERE msg.quiz_id = $quiz_id";

    $msg_res = tep_db_query($msg_query);

    if (tep_db_num_rows($msg_res) > 0) {
        while ($res = tep_db_fetch_array($msg_res)) {
            $ids[] = $res['id']; 
        }

        return $ids;
    }

    return false;
}

function test_taken_by_users($quiz_id, $recruiter_id) {
    global $template;

    $query = "SELECT CONCAT(jobseeker.jobseeker_first_name, ' ', jobseeker.jobseeker_last_name) AS jobseeker_name, 
                    results.created_at AS date
                FROM results
                JOIN jobseeker ON jobseeker.jobseeker_id = results.member_id
                JOIN quizzes on quizzes.id = results.quiz_id
                WHERE quizzes.id = $quiz_id AND quizzes.recruiter_id = $recruiter_id
                ORDER BY results.created_at DESC";
    
    $res = tep_db_query($query);

    if (tep_db_num_rows($res) > 0) {
        while ($data = tep_db_fetch_array($res)) {
            $template->assign_block_vars('test_taken', array(
                'jobseeker'  => $data['jobseeker_name'],
                'submitted_date' => tep_date_short($data['date']),
            ));
        }
        tep_db_free_result($res);

        return true;
    }

    return false;
}








/*
|--------------------------------------------------------------------------
| Database Related part Store/update/delete 
|--------------------------------------------------------------------------
| if request submit_assessment available then store
|
| if confirm_delete AND assessment id available then perform delete
*/
if ($action == 'submit_test' AND $_SERVER['REQUEST_METHOD'] == 'POST' AND $recruiter_id AND $assessmentType) {

    // check first condition is type valid or not
    if (!in_array($assessmentType, $typeArray)) {
        $messageStack->add_session(MESSAGE_TYPE_ERROR, 'error');
        tep_redirect(FILENAME_RECRUITER_LIST_OF_QUIZ);
    }

    // add test on quizzes table
    $new_test_id = add_test([
        'title'         => $testTitle,
        'recruiter_id'  => $recruiter_id,
        'test_type'     => $assessmentType,
        'timer'         => $testTimer,
        'created_at'    => $currentDate,
        'updated_at'    => $currentDate,
    ]);

    // add question but test id needed
    if ($new_test_id) {
        $new_ques_id = add_question([
            'question'      => $quesName,
            'quiz_id'       => $new_test_id,
            'created_at'    => $currentDate,
            'updated_at'    => $currentDate,
        ]);

        // use this function when assessment type is mcq otherwise not
        if ($assessmentType == 'mcq') {
            // add choices but quesiton id needed
            if ($new_ques_id) {
                foreach ($quesChoice as $key => $choice) {
                    add_choices([
                        'choice'         => $choice,
                        'point'          => $quesPoint[$key],
                        'question_id'    => $new_ques_id,
                        'created_at'     => $currentDate,
                        'updated_at'     => $currentDate,
                    ]);
                }
            }

            // add message as well but test id needed
            for ($i=0; $i < 2; $i++) { 
                if ($i == 0) {
                    $min_val = 0;
                    $max_val = max($quesPoint) - 1;
                    $message_txt = 'wrong';
                }
                if ($i == 1) {
                    $min_val = max($quesPoint);
                    $max_val = max($quesPoint);
                    $message_txt = 'correct';
                }
    
                add_message([
                    'min_value'     => $min_val,
                    'max_value'     => $max_val,
                    'quiz_id'       => $new_test_id,
                    'message'       => $message_txt,
                    'created_at'    => $currentDate,
                    'updated_at'    => $currentDate,
                ]);
            }
        }

    }

    $messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');
    return tep_redirect(FILENAME_RECRUITER_LIST_OF_QUIZ);
} 

if ($action == 'update_test' AND $_SERVER['REQUEST_METHOD'] == 'POST' AND $recruiter_id AND $assessmentType) {
    // check first condition is type valid or not
    if (!in_array($assessmentType, $typeArray)) {
        $messageStack->add_session(MESSAGE_TYPE_ERROR, 'error');
        tep_redirect(FILENAME_RECRUITER_LIST_OF_QUIZ);
    }

    if ($quiz_id) {
        
        // question id
        $updated_ques_id = get_list_of_question_id_for_quiz($quiz_id);
        
        // update test
        update_test([
            'title'         => $testTitle,
            'recruiter_id'  => $recruiter_id,
            'test_type'     => $assessmentType,
            'timer'         => $testTimer,
            'updated_at'    => $currentDate,
        ], $quiz_id);

        // update question
        update_question([
            'question'      => $quesName,
            'quiz_id'       => $quiz_id,
            'updated_at'    => $currentDate,
        ], $updated_ques_id);

        if ($assessmentType == 'mcq') {

            // message ids
            $updated_msg_id = get_list_of_quiz_messages($quiz_id);
            // question choice ids
            $questionChoices = $_POST['questionChoiceID'];
            $choiceIds = array_filter($questionChoices);
            
            // update message
            if ($updated_msg_id) {
                foreach ($updated_msg_id as $key => $msg_id) {
                    if ($key == 0) {
                        $min_val = 0;
                        $max_val = max($quesPoint) - 1;
                        $message_txt = 'wrong';
                    }
                    if ($key == 1) {
                        $min_val = max($quesPoint);
                        $max_val = max($quesPoint);
                        $message_txt = 'correct';
                    }
        
                    update_msg([
                        'min_value'     => $min_val,
                        'max_value'     => $max_val,
                        'message'       => $message_txt,
                        'updated_at'    => $currentDate,
                    ], $msg_id);
                }
            } else {
                for ($i=0; $i < 2; $i++) { 
                    if ($i == 0) {
                        $min_val = 0;
                        $max_val = max($quesPoint) - 1;
                        $message_txt = 'wrong';
                    }
                    if ($i == 1) {
                        $min_val = max($quesPoint);
                        $max_val = max($quesPoint);
                        $message_txt = 'correct';
                    }
        
                    add_message([
                        'min_value'     => $min_val,
                        'max_value'     => $max_val,
                        'quiz_id'       => $quiz_id,
                        'message'       => $message_txt,
                        'created_at'    => $currentDate,
                        'updated_at'    => $currentDate,
                    ]);
                }
            }

            // delete and attach new choice
            foreach ($choiceIds as $key => $cids) {
                // delete old choice
                tep_db_query("delete from " . QUES_CHOICE_TABLE . " where id = $cids AND question_id = $updated_ques_id");
            }
            // attach new choice
            foreach ($quesChoice as $key => $new_choice) {
                add_choices([
                    'choice'         => $new_choice,
                    'point'          => $quesPoint[$key],
                    'question_id'    => $updated_ques_id,
                    'created_at'     => $currentDate,
                    'updated_at'     => $currentDate,
                ]);
            }
        }

        $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
        return tep_redirect(FILENAME_RECRUITER_LIST_OF_QUIZ);
    }
}


if ($action == 'confirm_delete' AND $recruiter_id AND ($_SERVER['REQUEST_METHOD'] == 'POST') AND ($_POST['_method'] == 'delete')) 
{
    delete_test($quiz_id, $recruiter_id);
    $messageStack->add_session(MESSAGE_SUCCESS_DELETED, 'success');
    tep_redirect(FILENAME_RECRUITER_LIST_OF_QUIZ);
}

if ($action AND $recruiter_id AND ($_SERVER['REQUEST_METHOD'] == 'POST') AND ($_POST['_method'] == 'put')) {
    if (in_array($action, ["interface_true", "interface_false"])) {
        $intVal = ($action == 'interface_true') ? true : false;
        tep_db_query("update " . QUIZ_TABLE . " set interface_type='".$intVal."' where id='" . $quiz_id . "' AND recruiter_id=$recruiter_id");
        $messageStack->add_session(INTERFACE_UPDATED, 'success');
        tep_redirect(FILENAME_RECRUITER_LIST_OF_QUIZ);
    } else {
        $messageStack->add_session(MESSAGE_TYPE_ERROR, 'error');
        tep_redirect(FILENAME_RECRUITER_LIST_OF_QUIZ);
    }
}
































/**
 * if action is preview show detail and add data in global vaiable
 * if not then fetch all data
 */
if ($action == 'add-template') {
    // $template_dropdown = get_list_of_test_template_dropdown();
    // $template_job_dropdown = get_list_of_employer_active_job($recruiter_id);
} else {
    // Fetch All Quizzes
    $raw_query = "SELECT * FROM " . QUIZ_TABLE . " as quiz 
                        WHERE quiz.isActive = '1' 
                        AND quiz.recruiter_id = $recruiter_id 
                        ORDER BY quiz.created_at DESC";

    $quiz_query = tep_db_query($raw_query);

    if (tep_db_num_rows($quiz_query) > 0) {
        while ($tests = tep_db_fetch_array($quiz_query)) {
            $alternate++;
            $template->assign_block_vars('quizs', array(
                'id' => tep_db_output($tests['id']),
                // 'title' => '<a href="' . tep_href_link(PATH_TO_QUIZ . $tests['id'] . '/' . encode_forum($tests['title']) . '.html') . '" target="_blank" rel="noreferrer">
                //                 ' . tep_db_output($tests['title']) .
                //     '</a>',
                'title' => '<a href="' . tep_href_link(PATH_TO_QUIZ . FILENAME_RECRUITER_LIST_OF_QUIZ, 'id=' . $tests['id'] . '&action=preview') . '">
                                ' . tep_db_output($tests['title']) .
                    '</a>',
                'job_title' => '',
                'created_at' => tep_date_short($tests['created_at']),
                'type'       => $tests['test_type'],
                'messages_link' => getMessageLink(tep_db_output($tests['id'])),
                'interface' => ($tests['interface_type'] == 1)
                    ? interface_toggle_link_btn($tests['id'], 'interface_false', $tests['interface_type'])
                    : interface_toggle_link_btn($tests['id'], 'interface_true', $tests['interface_type']),

                'action' => getAction(tep_db_output($tests['id']), $tests['test_type'],tep_db_output($tests['job_id'])),
            ));
        }
        tep_db_free_result($quiz_query);
    }
}

// Default Values
$template->assign_vars(array(
    'quiz_menus' => '
                    <a class="btn btn-sm btn-outline-primary me-2" href="' . tep_href_link(FILENAME_QUIZ . '/' . FILENAME_RECRUITER_ASSESSMENT) . '" class="hm_color">
                        ' . LIST_ASSESSMENT . '
                    </a>
                    <a class="btn btn-sm btn-outline-primary me-2" href="' . tep_href_link(FILENAME_QUIZ . '/' . FILENAME_RECRUITER_QUIZ_REPORT, 'report=latestReport') . '" class="hm_color">
                        ' . MY_CANDIDATE . '
                    </a>
                    <a class="btn btn-sm btn-outline-primary me-2" href="' . tep_href_link(FILENAME_QUIZ . '/' . FILENAME_RECRUITER_LIST_OF_QUIZ) . '" class="hm_color">
                        ' . MY_CUSTOM_TESTS . '
                    </a>
                    <a class="btn btn-sm btn-outline-primary me-2" href="' . tep_href_link(FILENAME_QUIZ . '/' . FILENAME_RECRUITER_ASSESSMENT, 'action=video') . '" class="hm_color">
                        ' . TEST_VIDEOS . '
                    </a>
                    <a class="btn btn-sm btn-outline-primary me-2" href="' . tep_href_link(FILENAME_QUIZ . '/' . FILENAME_RECRUITER_ASSESSMENT, 'action=invitation-list') . '" class="hm_color">
                        ' . INVITATION . '
                    </a>
    ',

    'quiz_menus_1' => '<a class="btn btn-sm btn-primary mr-2" href="' . tep_href_link(FILENAME_QUIZ . '/' . FILENAME_RECRUITER_LIST_OF_QUIZ, 'action=select-test') . '" class="hm_color">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-lg me-1" viewBox="0 0 16 16">
    <path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2Z"/>
  </svg> ' . CREATE_TEST . '
                        </a>
    ',

    'video_test_link' => tep_href_link(FILENAME_QUIZ . '/' . FILENAME_RECRUITER_LIST_OF_QUIZ, 'action=new&type=video'),
    'mcq_test_link'   => tep_href_link(FILENAME_QUIZ . '/' . FILENAME_RECRUITER_LIST_OF_QUIZ, 'action=new&type=mcq'),
    'essay_test_link'   => tep_href_link(FILENAME_QUIZ . '/' . FILENAME_RECRUITER_LIST_OF_QUIZ, 'action=new&type=essay'),

    'update_message' => $messageStack->output()
));



/**
 * if test type is mcq then this function will used for storing mcq test
 *
 */
function type_is_mcq($action, $quiz_id, $recruiter_id) {
    global $template;
    $i = 0;

    // if test found then get the question id for getting the choices
    $is_test_found = count(find_test_with_an_id_and_get($quiz_id, $recruiter_id));
    
    if (($action === 'edit') AND $is_test_found > 0) {
        $quesID = find_test_with_an_id_and_get($quiz_id, $recruiter_id)['ques_id'];
        
        $ques_choice_query_raw = "SELECT * FROM " . QUES_CHOICE_TABLE . " as choice WHERE choice.question_id = " . $quesID . " ORDER BY choice.created_at ASC";
        $choice_query = tep_db_query($ques_choice_query_raw);
        
        $choiceBox = '';
        
        if (tep_db_num_rows($choice_query) > 0) {
            while ($choices = tep_db_fetch_array($choice_query)) {
                $i++;
                $template->assign_block_vars('choices', array(
                    'ques_choice'           => tep_draw_input_field('question_choice[]', tep_db_output($choices['choice']), 'class="form-control mb-2" id="question-choice-' . $i . '" placeholder="Choice" autocomplete="off"', '', 'text'),
                    'ques_point'            => tep_draw_input_field('points[]', tep_db_output($choices['point']), 'class="form-control" id="points-' . $i . '" min="0" autocomplete="off" placeholder="Point"', '', 'number'),
                    'hidden_field_in_edit'  => tep_draw_hidden_field('questionChoiceID[]', tep_db_output($choices['id'])),
                    'choice_label'          => ($i == 0) ? '<label for="choice">'.LABEL_CHOICE.'</label>' : '',
                    'point_label'           => ($i == 0) ? '<label for="point">'.LABEL_POINT.'</label>' : '',
                ));
            }
            tep_db_free_result($choice_query);
        }

    } else {
        for ($i = 0; $i < 2; $i++) {
            $template->assign_block_vars('choices', array(
                'hidden_field_in_edit' => '',
                'ques_choice'       => tep_draw_input_field('question_choice[]', '', 'class="form-control mb-2" id="question-choice-' . $i . '" placeholder="Choice" autocomplete="off"', '', 'text'),
                'ques_point'        => tep_draw_input_field('points[]', '', 'placeholder="Point" class="form-control" id="points-' . $i . '" min="0" placeholder="Point" autocomplete="off"', '', 'number'),
                'choice_label'      => ($i == 0) ? '<label for="choice">'.LABEL_CHOICE.'</label>' : '',
                'point_label'       => ($i == 0) ? '<label for="point">'.LABEL_POINT.'</label>' : '',
            ));
        }
    }


    // Go to create or edit form
    $template->assign_vars(array(
        'HEADING_TITLE'     => ($action == 'edit') ? EDIT_MCQ_TEST : ADD_MCQ_TEST,
        'ASSESSMENT_TYPE'   => MCQ_TEST,
        'TEST_LABEL'        => LABEL_TITLE,
        'TIMER_LABEL'       => LABEL_TIMER,
        'QUESTION_LABEL'    => 'Question',
        'ANSWER_LABEL'      => 'Answers',
        'ADD_NEW_ANS_BTN'   => '<span class="btn btn-sm btn-outline-success me-3" id="add-answer"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-lg me-1" viewBox="0 0 16 16">
        <path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2Z"/>
      </svg></span>',
        'CHOICE_LABEL'      => 'Choice',
        'POINT_LABEL'       => 'Point',
        'COL_SIZE'          => 'col-7',
        'DIV_IMG_IN_VIDEO'  => '',
        'TEST_INPUT'        => tep_draw_input_field('title', ($is_test_found > 0) ? find_test_with_an_id_and_get($quiz_id, $recruiter_id)['title'] : '', 'class="form-control" id="title" required', '', 'text'),
        'TIMER_INPUT'       => tep_draw_input_field('timer', ($is_test_found > 0) ? find_test_with_an_id_and_get($quiz_id, $recruiter_id)['timer'] : '', 'class="form-control" id="timer" placeholder="Time to answer the question in minute" required', '', 'number'),
        
        'QUES_INPUT'        => tep_draw_textarea_field('question', 'soft', '30', '5', ($is_test_found > 0) ? find_test_with_an_id_and_get($quiz_id, $recruiter_id)['question'] : '', 'class="form-control" id="question" placeholder="For example: What attracts you most to our company?"', '', true),
        
        'form' => ($action == 'new') ? getFormTag($action) : getFormTag($action, $quiz_id),
        'BUTTON' => ($action == 'edit') 
                    ? tep_button_submit('btn btn-primary px-4', UPDATE_BUTTON) 
                    : tep_button_submit('btn btn-primary px-4', SUBMIT_BUTTON),
    ));
}

/**
 * if test type is video then this function will run
 *
 */
function type_is_video($action, $quiz_id, $recruiter_id) {
    global $template;

    $isTestVal = find_test_with_an_id_and_get($quiz_id, $recruiter_id);

    if ($isTestVal) {
        $is_test_found = find_test_with_an_id_and_get($quiz_id, $recruiter_id);
    }else{
        $is_test_found = 0;
    }

    $template->assign_vars(array(
        'HEADING_TITLE'     => ($action == 'edit') ? EDIT_VIDEO_TEST : ADD_VIDEO_TEST,
        'ASSESSMENT_TYPE'   => VIDEO_TEST,
        'TEST_LABEL'        => LABEL_TITLE,
        'TIMER_LABEL'       => LABEL_TIMER,
        'QUESTION_LABEL'    => 'Question',
        'COL_SIZE'          => 'col-md-6',
        'DIV_IMG_IN_VIDEO'  => '<div class="col-md-6 text-center">
                                    <img src="'.tep_href_link('img/video-tests.jpg').'" class="video-img mx-auto" alt="Video Interview">
                                </div>',
        'TEST_INPUT'        => tep_draw_input_field('title', ($is_test_found > 0) ? find_test_with_an_id_and_get($quiz_id, $recruiter_id)['title'] : '', 'class="form-control" id="title" required', '', 'text'),
        'TIMER_INPUT'       => tep_draw_input_field('timer', ($is_test_found > 0) ? find_test_with_an_id_and_get($quiz_id, $recruiter_id)['timer'] : '', 'class="form-control" id="timer" placeholder="Time to answer the question in minute" required', '', 'number'),
        
        'QUES_INPUT'        => tep_draw_textarea_field('question', 'soft', '30', '5', ($is_test_found > 0) ? find_test_with_an_id_and_get($quiz_id, $recruiter_id)['question'] : '', 'class="form-control" id="question" placeholder="For example: What attracts you most to our company?"', '', true),
        
        'form' => ($action == 'new') ? getFormTag($action) : getFormTag($action, $quiz_id),
        'BUTTON' => ($action == 'edit') ? tep_button_submit('btn btn-primary px-4', UPDATE_BUTTON) : tep_button_submit('btn btn-primary px-4', SUBMIT_BUTTON),
        

    ));
}

function type_is_essay($action, $quiz_id, $recruiter_id) {
    global $template;

    $is_test_found = count(find_test_with_an_id_and_get($quiz_id, $recruiter_id));

    $template->assign_vars(array(
        'HEADING_TITLE'     => ($action == 'edit') ? EDIT_ESSAY_TEST : ADD_ESSAY_TEST,
        'ASSESSMENT_TYPE'   => ESSAY_TEST,
        'TEST_LABEL'        => LABEL_TITLE,
        'TIMER_LABEL'       => LABEL_TIMER,
        'QUESTION_LABEL'    => 'Question',
        'COL_SIZE'          => 'col-md-6',
        'DIV_IMG_IN_VIDEO'  => '<div class="col-md-6 text-center">
                                    <img src="'.tep_href_link('img/video-tests.jpg').'" class="img-fluid video-img" alt="Essay Interview">
                                </div>',
        'TEST_INPUT'        => tep_draw_input_field('title', ($is_test_found > 0) ? find_test_with_an_id_and_get($quiz_id, $recruiter_id)['title'] : '', 'class="form-control" id="title" required', '', 'text'),
        'TIMER_INPUT'       => tep_draw_input_field('timer', ($is_test_found > 0) ? find_test_with_an_id_and_get($quiz_id, $recruiter_id)['timer'] : '', 'class="form-control" id="timer" placeholder="Time to answer the question in minute" required', '', 'number'),
        
        'QUES_INPUT'        => tep_draw_textarea_field('question', 'soft', '30', '5', ($is_test_found > 0) ? find_test_with_an_id_and_get($quiz_id, $recruiter_id)['question'] : '', 'class="form-control" id="question" placeholder="For example: What would be your priorities for the first 100 days in the job? please explain..."', '', true),
        
        'form' => ($action == 'new') ? getFormTag($action) : getFormTag($action, $quiz_id),
        'BUTTON' => ($action == 'edit') ? tep_button_submit('btn btn-primary btn-block', UPDATE_BUTTON) : tep_button_submit('btn btn-primary btn-block', SUBMIT_BUTTON),
        

    ));
}

// Render to htm files based on condition
if (($action == 'new' || $action == 'edit')) {

    if ($assessmentType == 'video') {
        type_is_video($action, $quiz_id, $recruiter_id);
    } elseif ($assessmentType == 'essay') {
        type_is_essay($action, $quiz_id, $recruiter_id);
    }
     else {
        type_is_mcq($action, $quiz_id, $recruiter_id);
    }

    $template->pparse('create_update_form');
} elseif ($action == 'preview' AND $quiz_id) {

    $testView = find_test_with_an_id_and_get($quiz_id, $recruiter_id);

    $test_taken = test_taken_by_users($quiz_id, $recruiter_id);

    // find data with id and his details
    $template->assign_vars(array(
        'HEADING_TITLE' => PREVIEW_HEADING,
        'DESCRIPTION_LABEL' => DESCRIPTION_LABEL,
        'QUESTION'  => QUESTION,
        'BACK_BUTTON' => '
            <a class="btn btn-link mt-2" href="' . tep_href_link(PATH_TO_QUIZ . FILENAME_RECRUITER_LIST_OF_QUIZ) . '">
                <i class="fa fa-arrow-circle-left" aria-hidden="true"></i>' . BACK_BUTTON . '
            </a>',
        'TEST_NAME' => TEST_NAME,
        'TEST_TITLE' => $testView['title'],
        'TEST_DURATION' => TEST_DURATION,
        'TEST_TIMER' => $testView['timer'] . ' min',
        'DATA_DESCRIPTION' => $testView['description'],
        'TEST_DATE' => tep_date_short($testView['created_at']),

        'TH_QUESTION'   => TH_QUESTION,
        'TH_TEST_CREATED'   => TH_TEST_CREATED,
        'TH_QUES_DATE'   => TH_QUES_DATE,
        'HEAD_TEST'   => HEAD_TEST,
        'HEAD_QUES'   => HEAD_QUES,
        'HEAD_TEST_TAKEN'   => HEAD_TEST_TAKEN,
        'TH_NAME'   => TH_NAME,
        'TH_DATE'   => TH_DATE,
        'NOT_FOUND' => ($test_taken) ? '' : '<tr><td colspan="2" class="text-center">No data found</td></tr>',
        'new_button' => '
            <a 
                class="btn-text  mr-2" 
                href="' . tep_href_link(PATH_TO_QUIZ . FILENAME_RECRUITER_LIST_OF_QUIZ, 'action=new') . '">
                <i class="fa fa-plus" aria-hidden="true"></i> ' . ADD_TEXT . '
            </a>
        ',

    ));
    $template->pparse('preview');
} elseif ($action == 'select-test') {
    $template->assign_vars(array(
        'HEADING_TITLE' => "Create Custom Question",
        'DESCRIPTION_LABEL' => DESCRIPTION_LABEL,
        'BACK_BUTTON' => '
            <a class="btn btn-link mt-2" href="' . tep_href_link(PATH_TO_QUIZ . FILENAME_RECRUITER_LIST_OF_QUIZ) . '">
                <i class="fa fa-arrow-circle-left" aria-hidden="true"></i>' . BACK_BUTTON . '
            </a>',

    ));
    $template->pparse('select_test_form');
} else {
    // List of Quiz page return
    $template->assign_vars(array(
        'HEADING_TITLE' => HEADING_TITLE,
        'TABLE_HEADING_TITLE' => TABLE_HEADING_TITLE,
        'TABLE_HEADING_DESCRIPTION' => TABLE_HEADING_DESCRIPTION,
        'TABLE_HEADING_DATE_ADDED' => TABLE_HEADING_DATE_ADDED,
        'ADD_REPORT_MESSAGES' => ADD_REPORT_MESSAGES,
        'TABLE_HEADING_JOB_TITLE' => TABLE_HEADING_JOB_TITLE,
        'TABLE_HEADING_ACTION' => TABLE_HEADING_ACTION,
        'INVITE_CANDIDATE'  => INVITE_CANDIDATE,
        'TABLE_HEADING_TEST_TYPE'   => TABLE_HEADING_TEST_TYPE,
        'new_button' => '
            <a 
                class="btn-text mr-2" 
                href="' . tep_href_link(PATH_TO_QUIZ . FILENAME_RECRUITER_LIST_OF_QUIZ, 'action=new') . '">
                <i class="fa fa-plus" aria-hidden="true"></i> ' . ADD_TEXT . '
            </a>
        ',

    ));
    $template->pparse('all_test');
}