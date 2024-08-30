<?php
// Initial Setup
include_once("../include_files.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE . $language . '/' . FILENAME_ADMIN1_LIST_OF_QUESTIONS);
$template->set_filenames(
    array(
        'all_quiz_page' => 'questions/admin-all-quiz-page.htm',
        'all_question' => 'questions/admin-all-question.htm',
        'create_update_form' => 'questions/admin-store-question.htm',
        'preview' => 'questions/admin-view-question.htm'
    )
);
include_once(FILENAME_ADMIN_BODY);

// global Properties
$action = (isset($_GET['action']) ? $_GET['action'] : '');
$question_id = (isset($_GET['id']) ? tep_db_prepare_input($_GET['id']) : '');
$quiz_id = (isset($_GET['quiz_id']) ? tep_db_prepare_input($_GET['quiz_id']) : '');
$edit = false;
$error = false;
$currentDate = date("Y-m-d H:i:s"); // current date
$errorQuestion;

/**
 *
 * Default Condition End
 * 
 * 
 *  
 */
// Check Condition if id is present in quiz table or not
if (tep_not_null($quiz_id)) {
    if (!$row_check_quiz_id = getAnyTableWhereData(QUIZ_TABLE, "id='" . tep_db_input($quiz_id) . "'")) {
        $messageStack->add_session(MESSAGE_QUIZ_ERROR, 'error');
        tep_redirect(FILENAME_ADMIN1_LIST_OF_QUESTIONS);
    }
    $quiz_id = $row_check_quiz_id['id'];
}

// Check Condition if id is present in question table or not
if (tep_not_null($question_id)) {
    if (!$row_check_question_id = getAnyTableWhereData(QUES_TABLE. " left join question_choices as choice on choice.question_id = questions.id ", "questions.id='" . tep_db_input($question_id) . "' group by choice.question_id", 'questions.*, count(choice.question_id) as total_choice')) {
        $messageStack->add_session(MESSAGE_QUESTION_ERROR, 'error');
        tep_redirect(FILENAME_ADMIN1_LIST_OF_QUESTIONS);
    }
    $question_id = $row_check_question_id['id'];
    $edit = true;
}

// Default Values Pass
$template->assign_vars(array(
    'update_message' => $messageStack->output(),
    'question_menus' => '
                    <a 
                        class="btn-link mr-2 float-right" 
                        href="' . tep_href_link(PATH_TO_ADMIN . FILENAME_ADMIN1_LIST_OF_QUESTIONS) . '">
                        ' . ADMIN_QUESTION . '
                    </a>
                    <a 
                        class="btn-link mr-2 float-right" 
                        href="' . tep_href_link(PATH_TO_ADMIN . FILENAME_ADMIN1_LIST_OF_QUESTIONS,'filter=employer_question') . '">
                        ' . EMP_QUESTION . '
                    </a>
                    <a 
                        class="btn-link mr-2 float-right" 
                        href="' . tep_href_link(PATH_TO_ADMIN . FILENAME_ADMIN1_LIST_OF_QUIZ) . '">
                        ' . TEST_LIB . '
                    </a>
    ',
));
/**
 * 
 * 
 * Default Condition End
 * 
 * 
 * 
 */




/**
 *
 * 
 * 
 *  Function Start
 * 
 * 
 * 
 * 
 */

// create action button Edit and Delete for question
function getQuestionAction(int $quesId, int $quiz_id)
{
    $button = '
    <div class="btn-group">
        <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Action
        </button>
        <div class="dropdown-menu">
            <a class="dropdown-item" href="' . tep_href_link(PATH_TO_ADMIN . FILENAME_ADMIN1_LIST_OF_QUESTIONS, 'quiz_id=' . $quiz_id . '&id=' . $quesId . '&action=edit') . '">
                ' . EDIT_TEXT . '
            </a>
            <a class="dropdown-item" href="' . tep_href_link(PATH_TO_ADMIN . FILENAME_ADMIN1_LIST_OF_QUESTIONS, 'quiz_id=' . $quiz_id . '&id=' . $quesId . '&action=confirm_delete') . '">'
        . DELETE_TEXT . '
            </a>
        </div>
    </div>
    ';
    return $button;
}

/**
 * count quiz has total questions
 *
 * @param [integer] $quiz_id
 */
function countQuizHasQuestions($quiz_id)
{
    $query = "SELECT COUNT(*) as totalQuestion FROM " . QUES_TABLE . " WHERE quiz_id = $quiz_id";
    $data = tep_db_query($query);
    if ($data) {
        $countData = tep_db_fetch_array($data);
        return $countData['totalQuestion'];
    }
    return '';
}

// crate add action button for question
function getAddQuestionAction($quiz_id)
{
    $button = '
        <a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN . FILENAME_ADMIN1_LIST_OF_QUESTIONS, 'quiz_id=' . $quiz_id . '&action=allQuestion') . '">
            ' . ADD_QUESTION . '
        </a>
    ';
    return $button;
}

// Object return: Question row return with Id
function findDataWithQuestionId()
{
    global $error, $action, $row_check_question_id;
    if (!$error && ($action == 'edit' || $action == 'preview')) {
        $data = new objectInfo($row_check_question_id);
        return $data;
    }
    return false;
}

/**
 * return array
 *
 * @return array
 */
function getAllQuestionChoiceBasedOnQuestionId()
{
    global $error, $action, $question_id;
    $arrayVal = [];

    if (!$error && $action == 'updateForm') {
        $ques_choice_query_raw = "SELECT * FROM " . QUES_CHOICE_TABLE . " as choice WHERE choice.question_id = " . $question_id . " ORDER BY choice.created_at ASC";
        $choice_query = tep_db_query($ques_choice_query_raw);
        // $allRow = mysqli_fetch_all($choice_query, MYSQLI_ASSOC);

        foreach ($choice_query as $value) {
            $arrayVal[] .= $value['id'];
        }

        // print_r($arrayVal);
        // die();

        return $arrayVal;
    }
    return false;
}



function viewQuizDataWithQuizId()
{
    global $error, $action, $row_check_quiz_id;
    if (!$error) {
        $data = new objectInfo($row_check_quiz_id);
        return $data;
    }
    return false;
}

// get the starting form tag <form> not </form> -> this would be manually added in the htm file
function getFormTag($actionValue, $dataID = null)
{
    switch ($actionValue) {
        case 'new':
            return tep_draw_form('question formElement', PATH_TO_ADMIN . FILENAME_ADMIN1_LIST_OF_QUESTIONS, 'action=submitForm', 'post', 'id="formElement" enctype="multipart/form-data"');
            break;
        case 'edit':
            return tep_draw_form('question formElement', PATH_TO_ADMIN . FILENAME_ADMIN1_LIST_OF_QUESTIONS, 'id=' . $dataID . '&action=updateForm', 'post', 'id="formElement" enctype="multipart/form-data"');
            break;
    }
}
// perform insert data in quizzes table
function storeForm(array $sql_data)
{
    global $currentDate;
    tep_db_perform(QUES_TABLE, $sql_data);

    $current_created_row_id = getAnyTableWhereData(QUES_TABLE, " created_at='" . tep_db_input($currentDate) . "' and question='" . tep_db_input($sql_data['question']) . "' order by  created_at desc", 'id');

    return $current_created_row_id; // array returned
}

// perform update the data in quizzes table
function updateForm(array $sql_data)
{
    global $question_id;
    $data = tep_db_perform(QUES_TABLE, $sql_data, 'update', "id='" . $question_id . "'");
    return $data;
}

// perform delete quiz action
function deleteQuiz(int $id)
{
    $data = tep_db_query("delete from " . QUES_TABLE . " where id='" . tep_db_input($id) . "'");
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
function validationFormCheck(array $validateArray, string $actionValue, int $idOFQuiz)
{
    global $action, $error, $errorQuestion, $quiz_id;
    // if (strlen($validateArray['question']) <= 0) {
    // $error = true;
    // $quiz_id = $idOFQuiz;
    // $action = $actionValue;
    // $errorQuestion = true;
    // }
    foreach ($validateArray as $key => $value) {
        switch ($key) {
            case 'question': 
                if (strlen($value) <= 0) {//  if (strlen($value['question']) <= 0) {
                    $error = true;
                    $quiz_id = $idOFQuiz;
                    $action = $actionValue;
                    $errorQuestion = true;
                    // print_r('question required');
                    // die();
                }
                break;
                // case 'choices.*':
                // atleast one option required
                // if (strlen($validateArray['choices.*'][0]) <= 0) {
                //     $error = true;
                //     $quiz_id = $idOFQuiz;
                //     $action = $actionValue;
                //     $errorQuestion = true;
                // }
                // all option required
                // foreach ($validateArray['choices.*'] as $ck => $cv) {
                //     if (strlen($cv) <= 0) {
                //         $error = true;
                //         $quiz_id = $idOFQuiz;
                //         $action = $actionValue;
                //         $errorQuestion = true;
                //     }
                // }
                // break;

                // case 'points.*':
                // if (strlen($validateArray['points.*'][0]) <= 0) {
                //     $error = true;
                //     $quiz_id = $idOFQuiz;
                //     $action = $actionValue;
                //     $errorQuestion = true;
                // }
                //     foreach ($validateArray['points.*'] as $pk => $pv) {
                //         if (strlen($pv) <= 0) {
                //             $error = true;
                //             $quiz_id = $idOFQuiz;
                //             $action = $actionValue;
                //             $errorQuestion = true;
                //         }
                //     }
                // break;
        }
    }
}

/**
 * this function will return you to a boolean true or false
 *
 * @return boolean
 */
function isArrayMatched(array $arrayVal): bool
{
    global $action, $error;
    $newArray = [];
    if (!$error && $action == 'updateForm') {
        $questionChoicesArray = getAllQuestionChoiceBasedOnQuestionId();
        foreach ($questionChoicesArray as $choiceId) {
            $newArray[] =  $choiceId;
        }

        // print_r($newArray);
        // print_r('</br>');
        // print_r($arrayVal);
        // print_r('</br>');
        // print_r($newArray === $arrayVal);
        // die();
        
        return ($newArray === $arrayVal) ? true : false;
    }

    return false;
}

/**
 * will return the label name change as acording to your needs
 *
 * @param [string] $value
 * @return string
 */
function labelName(int $value, string $label_name = ''): string
{
    switch ($value) {
        case '0':
            return $label_name . '-1';
            break;
        case '1':
            return $label_name . '-2';
            break;
        case '2':
            return $label_name . '-3';
            break;
        case '3':
            return $label_name . '-4';
            break;

        default:
            return 'Label';
            break;
    }
}


function get_admin_question($request)
{
    global $template;

    if ($request == 'employer_question') {
        $fetch_all_quiz_query = "SELECT questions.*, quizzes.title, recruiter.recruiter_company_name AS company
                                    FROM questions
                                    INNER JOIN quizzes ON quizzes.id = questions.quiz_id
                                    LEFT JOIN recruiter ON recruiter.recruiter_id = quizzes.recruiter_id
                                    WHERE quizzes.recruiter_id IS NOT NULL AND quizzes.isActive = 1 AND questions.isActive = 1";
        

    } else {
        $fetch_all_quiz_query = "SELECT quiz.*, recruiter.recruiter_company_name AS company 
                                FROM " . QUIZ_TABLE . " as quiz 
                                LEFT JOIN recruiter ON recruiter.recruiter_id = quiz.recruiter_id 
                                WHERE quiz.isActive = '1' ORDER BY quiz.created_at DESC";
    }

    $get_all_quiz = tep_db_query($fetch_all_quiz_query);

    if (tep_db_num_rows($get_all_quiz) > 0) {
        while ($quiz = tep_db_fetch_array($get_all_quiz)) {
            $template->assign_block_vars('quizs', array(
                'id' => tep_db_output($quiz['id']),
                
                'question' => (isset($request)) ? $quiz['question'] : '',

                'title' => (!isset($request))
                            ? '<a href="' . tep_href_link(PATH_TO_ADMIN . FILENAME_ADMIN1_LIST_OF_QUESTIONS, 'quiz_id=' . tep_db_output($quiz['id']) . '&action=allQuestion') . '" target="_blank" rel="noreferrer">
                                ' . tep_db_output($quiz['title']) .
                            '</a>' 
                            : $quiz['title'],
                    
                'company' => $quiz['company'],

                'countQuestions' => (!isset($request)) ? '<a href="' . tep_href_link(PATH_TO_ADMIN . FILENAME_ADMIN1_LIST_OF_QUESTIONS, 'quiz_id=' . tep_db_output($quiz['id']) . '&action=allQuestion') . '">
                                        ' . countQuizHasQuestions(tep_db_output($quiz['id'])) .
                                    '</a>' : '',

                'created_at' => tep_date_short($quiz['created_at']),

                'action' =>  (!isset($request)) ? '<a class="btn btn-primary" 
                                href="' . tep_href_link(PATH_TO_ADMIN . FILENAME_ADMIN1_LIST_OF_QUESTIONS, 'quiz_id=' . tep_db_output($quiz['id']) . '&action=new') . '">
                                <i class="fa fa-plus" aria-hidden="true"></i> ' . ADD_TEXT . '
                            </a>' : '',
            ));
        }
        tep_db_free_result($get_all_quiz);
    }

    
}










/* Function End  */

/** 
 *  Form Sumbition Updation and Deletion Start if action is not null
 */

if (tep_not_null($action)) {
    $questionTitle = tep_db_prepare_input($_POST['question']);
    $question_quiz_id = $_POST['parentQuizId'];
    $choices = $_POST['question_choice']; // get all choices from form $_POST method: in store/update form it is required
    $points = $_POST['points'];
    $quesChoiceId = []; // get all choice Id from form $_POST method: in update form it is required  

    // filter the array select array which have value
	if(is_array($choices))
    $newChoices = array_filter($choices);
	else 
    $newChoices = array();
    if(is_array($newPoint))
		$newPoint = array_filter($points);
	else 
      $newPoint = array();
    switch ($action) {
        case 'submitForm':
            validationFormCheck([
                'question' => $questionTitle,
                // 'choices.*' => $choices,
                // 'points.*' => $points,
            ], 'new', $question_quiz_id);
            if (!$error) {
                $store_data_array = array(
                    'question'         => $questionTitle,
                    'quiz_id'          => $question_quiz_id,
                    'created_at'       => $currentDate,
                    'updated_at'       => $currentDate,
                );

                $data = storeForm($store_data_array);
                foreach ($newChoices as $key => $choice) {
                    // if (empty($newChoices[$key])) {
                    //     $isActive = 0;
                    // } else {
                    //     $isActive = 1;
                    // }
                    $store_choices = array(
                        'choice'         => $choice,
                        'point'         => $newPoint[$key],
                        'question_id'    => $data['id'],
                        // 'isActive'      => $isActive,
                        'created_at'     => $currentDate,
                        'updated_at'     => $currentDate,
                    );
                    tep_db_perform(QUES_CHOICE_TABLE, $store_choices);
                }
                $messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');
                return tep_redirect(FILENAME_ADMIN1_LIST_OF_QUESTIONS . '?quiz_id=' . $question_quiz_id . '&action=allQuestion');
            }
            break;
        case 'updateForm':
            validationFormCheck([
                'question' => $questionTitle,
            ], 'edit', $question_quiz_id);
            if (!$error) {
                $update_data_array = array(
                    'question'         => $questionTitle,
                    'updated_at'       => $currentDate,
                );//print_r($_POST);die();
                $quesChoiceId = $_POST['questionChoiceID'];

                // update question
                updateForm($update_data_array);

                // update choice based on condition
                if (($quesChoiceId) > 0) { //                if (count($quesChoiceId) > 0) {
                    if (isArrayMatched($quesChoiceId)) {
                        foreach ($newChoices as $key => $choice) {
                            $update_choices = array(
                                'choice'         => $choice,
                                'point'         => $newPoint[$key],
                                'question_id'    => $question_id,
                                'updated_at'     => $currentDate,
                            );
                            // tep_db_perform(QUES_CHOICE_TABLE, $update_choices, 'update', "id='" . $quesChoiceId[$key] . "'");

                            // delete old choice
                            tep_db_query("delete from " . QUES_CHOICE_TABLE . " where id='" . $quesChoiceId[$key] . "'");

                            // create new choice
                            tep_db_perform(QUES_CHOICE_TABLE, $update_choices);
                        }
                    } else {
                        $messageStack->add_session(MESSAGE_QUESTION_ERROR, 'error');
                        return tep_redirect(FILENAME_ADMIN1_LIST_OF_QUESTIONS . '?quiz_id=' . $question_quiz_id . '&action=allQuestion');
                    }
                } else {
                    foreach ($newChoices as $key => $choice) {
                        $update_choices = array(
                            'choice'         => $choice,
                            'point'         => $newPoint[$key],
                            'question_id'    => $question_id,
                            'updated_at'     => $currentDate,
                        );
                        // attach new choice
                        tep_db_perform(QUES_CHOICE_TABLE, $update_choices);
                    }
                }

                // return success message
                $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
                return tep_redirect(FILENAME_ADMIN1_LIST_OF_QUESTIONS . '?quiz_id=' . $question_quiz_id . '&action=allQuestion');
            }
            break;
        case 'confirm_delete':
            if (!$error) {
                // $softdelete_data_array = array(
                //     'deleted_at'       => $currentDate,
                // );
                // updateForm($softdelete_data_array);
                deleteQuiz($question_id);
                $messageStack->add_session(MESSAGE_SUCCESS_DELETED, 'success');
                tep_redirect(FILENAME_ADMIN1_LIST_OF_QUESTIONS . '?quiz_id=' . $quiz_id . '&action=allQuestion');
            }
            break;
    }
}
/**
 *  Form Sumbition Updation and Deletion End 
 */




/**
 * fetch all question based on ques Id
 */
if ($action === 'allQuestion' && tep_not_null($quiz_id)) {
    $fetch_all_questions = "SELECT ques.*, COUNT(choice.question_id) AS total_choices
                            FROM " . QUES_TABLE . " as ques 
                            LEFT JOIN ".QUES_CHOICE_TABLE." AS choice ON choice.question_id = ques.id
                            WHERE ques.isActive = '1' AND ques.deleted_at IS NULL AND ques.quiz_id = " . $quiz_id . " 
                            GROUP BY choice.question_id
                            ORDER BY ques.created_at DESC";
    $get_all_questions = tep_db_query($fetch_all_questions);
    if (tep_db_num_rows($get_all_questions) > 0) {
        while ($questions_table = tep_db_fetch_array($get_all_questions)) {
            $alternate++;
            $template->assign_block_vars('questions', array(
                'id' => tep_db_output($questions_table['id']),
                'question' => tep_db_output($questions_table['question']),
                'total_choices' => '<a href="'.tep_href_link(PATH_TO_ADMIN . FILENAME_ADMIN1_LIST_OF_QUESTIONS, 'quiz_id=' . $quiz_id . '&id=' . tep_db_output($questions_table['id']) . '&action=preview').'">'.tep_db_output($questions_table['total_choices']).'</a>',
                'created_at' => tep_date_short($questions_table['created_at']),
                'action' => getQuestionAction(tep_db_output($questions_table['id']), tep_db_output($questions_table['quiz_id'])),
            ));
        }
        tep_db_free_result($get_all_questions);
    }
}


/**
 * return to html files based on request actions
 */
if ($action == 'allQuestion' && tep_not_null($quiz_id)) {
    // Find question with quiz id
    $template->assign_vars(array(
        'HEADING_TITLE' => viewQuizDataWithQuizId()->title,
        'HEAD_QUESTION' => 'Question',
        'HEAD_CREATE_DATE' => 'Created_at',
        'HEAD_ACTION' => 'Action',
        'TOTAL_QUESTION' => countQuizHasQuestions($quiz_id),
        'new_button' => '
        <a 
            class="btn btn-primary" 
            href="' . tep_href_link(PATH_TO_ADMIN . FILENAME_ADMIN1_LIST_OF_QUESTIONS, 'quiz_id=' . $quiz_id . '&action=new') . '">
            <i class="fa fa-plus" aria-hidden="true"></i> ' . ADD_TEXT . '
        </a>
    ',
    ));
    $template->pparse('all_question');
} elseif ($action == 'preview' && tep_not_null($quiz_id) && tep_not_null($question_id)) {
    $query = "SELECT * FROM " . QUES_CHOICE_TABLE . " as choice WHERE choice.question_id = " . $question_id . " ORDER BY choice.created_at ASC";
    $choice_query = tep_db_query($query);
    if (tep_db_num_rows($choice_query) > 0) {
        while ($choice_row = tep_db_fetch_array($choice_query)) {
            $template->assign_block_vars('choices', array(
                'choice' => tep_db_output($choice_row['choice']),
                'point' => tep_db_output($choice_row['point']),
            ));
        }
        tep_db_free_result($choice_query);
    }

    $template->assign_vars(array(
        'QUESTION_TITLE' => findDataWithQuestionId()->question,
        'TH_CHOICE' => 'Choice',
        'TH_POINT' => 'Point',
        'TOTAL_CHOICES' => findDataWithQuestionId()->total_choice,
    ));
    $template->pparse('preview');
} elseif (tep_not_null($quiz_id) && ($action === 'new' || $action === 'edit')) {

    // Below if else condition is used for creating option and choice input box
    if ($action === 'edit') {
        $ques_choice_query_raw = "SELECT * FROM " . QUES_CHOICE_TABLE . " as choice WHERE choice.question_id = " . $question_id . " ORDER BY choice.created_at ASC";
        $choice_query = tep_db_query($ques_choice_query_raw);
        $choiceBox = '';
        if (tep_db_num_rows($choice_query) > 0) {
            $inc = -1;
            while ($ques_choice_table_row = tep_db_fetch_array($choice_query)) {
                $inc++;
                $template->assign_block_vars('choices', array(
                    'question_input_choice_box' => tep_draw_input_field('question_choice[]', tep_db_output($ques_choice_table_row['choice']), 'class="form-control" id="question_choice-' . $inc . '" autocomplete="off"', '', 'text'),
                    'question_input_point_box' => tep_draw_input_field('points[]', tep_db_output($ques_choice_table_row['point']), 'class="form-control" id="points" min="0" autocomplete="off"', '', 'number'),
                    'hidden_field_in_edit' => tep_draw_hidden_field('questionChoiceID[]', tep_db_output($ques_choice_table_row['id'])),
                    'option_label_name' => labelName($inc, 'Options'),
                    // 'point_label_name' => labelName($inc, 'Point'),
                    'point_label_name' => "Point",
                ));
            }
            tep_db_free_result($choice_query);
        }
    } else {
        for ($i = 0; $i < 1; $i++) {
            $template->assign_block_vars('choices', array(
                'question_input_choice_box' => tep_draw_input_field('question_choice[]', '', 'class="form-control" id="question-choice-' . $i . '" autocomplete="off"', '', 'text'),
                'question_input_point_box' => tep_draw_input_field('points[]', '', 'class="form-control" id="points-' . $i . '" min="0" autocomplete="off"', '', 'number'),
                'hidden_field_in_edit' => '',
                'option_label_name' => labelName($i, 'Options'),
                // 'point_label_name' => labelName($i, 'Point'),
                'point_label_name' => "Point",
            ));
        }
    }


    // Go to create or edit form
    $template->assign_vars(array(
        'HEADING_TITLE' => ($action == 'edit') ? EDIT_TEXT : ADD_TEXT,
        'ID' => $quiz_id,
        'form' => ($action == 'new') ? getFormTag($action) : getFormTag($action, $question_id),
        'TITLE_LABEL' => TITLE_LABEL,
        'QUESTION_INPUT' => tep_draw_input_field('question', findDataWithQuestionId()->question, 'class="form-control" id="question"', '', 'text'),
        'QUESTION_CHOICE' => 'Options',
        'QUESTION_POINT' => 'Point',
        'TITLE_ERROR' => ($errorQuestion) ? '<span class="text-danger">' . TITLE_ERROR . '</span>' : '',
        'CHOICE_INPUT_ERROR' => ($errorQuestion) ? '<span class="text-danger">' . CHOICE_INPUT_ERROR . '</span>' : '',
        'POINT_INPUT_ERROR' => ($errorQuestion) ? '<span class="text-danger">' . POINT_INPUT_ERROR . '</span>' : '',
        'HIDDEN_INPUT' => tep_draw_hidden_field('parentQuizId', $quiz_id, 'class="form-control" id="parentQuizId"'),
        'BUTTON' => ($action == 'edit') ? tep_button_submit('btn btn-primary float-right', UPDATE_BUTTON) : tep_button_submit('btn btn-primary float-right', SUBMIT_BUTTON),
    ));
    $template->pparse('create_update_form');
} else {
    get_admin_question($_GET['filter']);
    // List of Quiz page return
    $template->assign_vars(array(
        'HEADING_TITLE' => ($_GET['filter'] == 'employer_question') ? EMPLOYER_QUES : HEADING_TITLE,
        'HEAD_COMPANY' => TH_COMPANY,
        'TABLE_HEADING_TITLE' => TABLE_HEADING_TITLE,
        'TH_QUESTION' => ($_GET['filter'] == 'employer_question') ?  TH_QUESTION : '',
        'TABLE_HEADING_TOTAL_QUESTION' => ($_GET['filter'] == 'employer_question') ? '' : TABLE_HEADING_TOTAL_QUESTION,
        'TABLE_HEADING_DATE_ADDED' => TABLE_HEADING_DATE_ADDED,
        'TABLE_HEADING_ACTION' => ($_GET['filter'] == 'employer_question') ? '' : TABLE_HEADING_ACTION,
    ));
    $template->pparse('all_quiz_page');
}
