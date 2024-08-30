<?
include_once("../include_files.php");
include_once(PATH_TO_MAIN_QUIZ_PHYSICAL_LANGUAGE . $language . '/' . FILENAME_QUIZ_TOPICS);
$template->set_filenames(array(
    'quizTopic'     => 'quiz_topics.htm',
    'video_test'    => 'video.htm',
    'essay_test'    => 'essay_test.htm',
    'quizStartForm' => 'quiz_start.htm',
));
include_once("../" . FILENAME_BODY);

$action = (isset($_GET['action']) ? $_GET['action'] : '');
$test_id = (isset($_GET['t_id']) ? $_GET['t_id'] : '');
$assessment_id = (isset($_GET['a_id']) ? $_GET['a_id'] : '');
$test_type = (isset($_GET['q']) ? $_GET['q'] : '');
$user_id;
/**
 * check condition jobseeker is logged in or not
 * if jobseeker is not logged in then redirect to login page 
 * otherwise to next request
 */
if (!check_login('jobseeker') && tep_not_null($action)) {
    $_SESSION['REDIRECT_URL'] = $_SERVER['REQUEST_URI'];
    $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
    tep_redirect(tep_href_link(FILENAME_JOBSEEKER_LOGIN));
}

/**
 * get user/member id if logged in
 */
if (check_login('jobseeker')) {
    $user_id   = $_SESSION['sess_jobseekerid'];
    $user_type = 'jobseeker';
    //  print_r($user_id);
    //  die();
}

/**
 * for this type of domain/quiz/3/3-Minute-Depression-Test.html url check .htaccess file to register url
 * 
 * First check the id is returned or not
 */

if (tep_not_null($_GET['quiz_id'])) {
    $quiz_id = tep_db_prepare_input($_GET['quiz_id']);
}


$template->assign_vars(array(
    'test_menus' => '
                    <a class="btn btn-outline-secondary mr-2" href="' . tep_href_link(PATH_TO_QUIZ . FILENAME_JOBSEEKER_TESTS) . '" class="hm_color">
                        ' . MY_TESTS . '
                    </a>
                    <a class="btn btn-outline-secondary mr-2" href="' . tep_href_link(PATH_TO_QUIZ.FILENAME_JOBSEEKER_TESTS,'action=latest-report') . '" class="hm_color">
                        ' . LATEST_REPORT . '
                    </a>
                    <a class="btn btn-outline-secondary" href="' . tep_href_link(PATH_TO_QUIZ.FILENAME_QUIZ_CALENDAR) . '" class="hm_color">
                        ' . CALENDAR . '
                    </a>
    ',
    'update_message' => $messageStack->output()
));

/**
 * Check Condition if id is present in table or not
 */
if ($action == 'start_test' OR $action == 'quiz_start') {

    $interface_type = 0;

    if (($action == 'start_test') AND tep_not_null($test_id) AND tep_not_null($assessment_id)) {
        $decoded_test_id        = check_data($test_id,"==","test_id","test");
        $decoded_assessment_id  = check_data($assessment_id,"==","assessment_id","assessment");

        // check condition in assessment_quiz for assessment id and test id present
        if (!$isRowPresent = getAnyTableWhereData(ASSESSMENT_QUIZ_TABLE, "quiz_id = $decoded_test_id AND assessment_id = $decoded_assessment_id")) {
            $messageStack->add_session(MESSAGE_INVALID_ASSESSMENT_ERROR, 'error');
            tep_redirect(tep_href_link(PATH_TO_QUIZ . FILENAME_JOBSEEKER_TESTS));
        }

        // check condition for test is attempted by jobseeker or not => for assessment_id, jobseeker_id and test_id
        if ($isTestTaken = getAnyTableWhereData(QUIZ_RESULT_TABLE, "quiz_id = $decoded_test_id AND assessment_id = $decoded_assessment_id AND member_id = $user_id")) {
            $messageStack->add_session(MESSAGE_INVALID_TEST_TAKEN, 'error');
            tep_redirect(tep_href_link(PATH_TO_QUIZ . FILENAME_JOBSEEKER_TESTS));
        }

        $quiz_id = $decoded_test_id;
    }

    // is quizzes or test present or not 
    if ($quiz_id) {
        if (!$quizInfo = getAnyTableWhereData(QUIZ_TABLE, "id='" . tep_db_input($quiz_id) . "'")) {
            $messageStack->add_session(MESSAGE_INVALID_QUIZ_ERROR, 'error');
            tep_redirect(tep_href_link(PATH_TO_QUIZ . FILENAME_JOBSEEKER_TESTS));
        }
        $quiz_id = $quizInfo['id'];
        $interface_type = $quizInfo['interface_type'];
    }

}else {
    print_r('not found');
    die();
    $messageStack->add_session(MESSAGE_INVALID_QUIZ_ERROR, 'error');
    tep_redirect(tep_href_link(PATH_TO_QUIZ . FILENAME_INDEX));
}

function api_for_get_list_of_question_with_their_answers($quiz_id) {

    $query = "SELECT questions.id, questions.question
                FROM questions
                WHERE questions.quiz_id = '$quiz_id'";

    $response = tep_db_query($query);

    $itemRecords = [];
    
    
    while ($qu_data = tep_db_fetch_array($response)) {
        $choices = get_list_of_choices($qu_data['id']);

        $question = [
            "id"       => $qu_data['id'],
            "question" => $qu_data['question'],
            "answers"  => $choices['data'],
            "answer_unique_key" => $choices['question_choice'],
            // "ans_ids"  => array_values($choices['ids']),
            // "choice"   => array_keys($choices['data']),
            // "points"   => array_values($choices['data']),
        ];

        array_push($itemRecords, $question);
    }

    return json_encode($itemRecords);
}

function get_list_of_choices($question_id) {
    $query = "SELECT question_choices.id, question_choices.choice, question_choices.point
                FROM question_choices
                WHERE question_choices.question_id = $question_id";

    $response = tep_db_query($query);

    // $result = tep_db_fetch_array($response);

    $answers = [];
    $points = [];
    $choiceids = [];

    while($result = tep_db_fetch_array($response)) {
        array_push($answers, $result['choice']);
        array_push($points, $result['point']);
        array_push($choiceids, $result['id']);
    }

    $finalData = [
        'data' => array_combine($answers, $points),
        'question_choice'  => array_combine($answers, $choiceids)
    ];

    return $finalData;
}

function get_list_of_candidate_questions($decoded_assessment_id, $user_id)
{
    global $template;
     // get the list of assign test
     $list_test_query = "SELECT q.id, q.title, IF(res.id, 'Yes', 'No') as quiz_taken
     FROM ".QUIZ_TABLE." as q
     JOIN ".ASSESSMENT_QUIZ_TABLE." as aq ON aq.quiz_id = q.id
     JOIN ".ASSESSMENT_TABLE." as ass ON ass.id = aq.assessment_id
     JOIN ".ASSESSMENT_JOBSEEKER_TABLE." as asj ON asj.assessment_id = ass.id
     LEFT JOIN ".QUIZ_RESULT_TABLE." as res ON (res.assessment_id = ass.id AND res.quiz_id = q.id)
     WHERE ass.id = $decoded_assessment_id AND asj.jobseeker_id = $user_id";

    $res = tep_db_query($list_test_query);

    if (tep_db_num_rows($res) > 0) {
        while ($candidate_list_test = tep_db_fetch_array($res)) {
            $template->assign_block_vars('candidate_list_test', array(
                'title' => $candidate_list_test['title'],
            ));
        }
        tep_db_free_result($res);
        return true;
    }

    return false;
}
/**
 * function for making the quiz option table
 *
 * @param [integer] $questionID
 * @param [string] $question
 */
function quizTemplate($questionID, $questionTitle)
{
    // SQL query for geting the question option
    $query = "SELECT * FROM " . QUES_CHOICE_TABLE . " as qc WHERE qc.question_id = $questionID AND qc.isActive = 1";
    $resultQuizChoice = tep_db_query($query);

    $optionDiv = '';

    // get the question options
    foreach ($resultQuizChoice as $key => $choices) {
        $optionDiv .= '
        <div class="col-sm-12">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="quizChecked[' . $choices['question_id'] . ']" id="questionChoice' . $choices['id'] . '" value="' . $choices['point'] . '">
                <label class="form-check-label" for="questionOptions-' . $choices['id'] . '">
                    ' . $choices['choice'] . '
                </label>
            </div>
        </div>
        ';
    }

    // merge question with his options
    $quizTemplate = '
                <fieldset class="form-group row">
                    <legend class="col-sm-12 float-sm-left pt-0">' . $questionTitle . '</legend>
                    ' . $optionDiv . '
                </fieldset>
                <input type="hidden" name="q_id" id="q_id" value="'.$questionID.'" />';

    return $quizTemplate;
}

/**
 * load quiz with their option wheh quiz inteface column is to be 0 (zero) or false
 *
 */
function loadQuiz($quiz_id) {
    $temp = '';

    $query = "SELECT * FROM " . QUES_TABLE . " as ques WHERE ques.quiz_id = $quiz_id";
        $result = tep_db_query($query);
        if (tep_db_num_rows($result) > 0) {
            while ($questionData = tep_db_fetch_array($result)) {
                $temp .= quizTemplate(tep_db_output($questionData['id']), tep_db_output($questionData['question']));  
            }
            $temp .= '<div class="col-sm-12 d-flex justify-content-center">
                        <div class="mt-2">
                            <button type="submit" name="submitted" id="testSubmit">'.SUBMIT.'</button>
                        </div>
                    </div>';
        }

    return $temp;
}

// script tag show when interface column is to be 1
function script_for_test($interface_type, $decoded_test_id) {
    $script = '';

    if ($interface_type == 1) {
        $script .= '<script>const quizData = '.api_for_get_list_of_question_with_their_answers($decoded_test_id).'</script>';
        $script .= '<script src="'.tep_href_link('jscript/online-test.js').'"></script>';
    }

    return $script;
}

// toggle the test ui based on interface column
function toggle_test_interface($interfaceType, $quiz_id) {
    $htmlElement = '';
    if ($interfaceType == 1) {
        $htmlElement .= '<h2 class="question_title" id="question">Question Text</h2>
                        <ul id="choices_lists"></ul>
                        <div class="d-flex justify-content-center">
                            <div class="mt-2">
                                <button id="testSubmit">'.SUBMIT.'</button>
                            </div>
                        </div>';
    } else {
        $htmlElement .= loadQuiz($quiz_id);
    }
    return $htmlElement;
}


























function questions_for_video_or_essay($quiz_id) {
    global $template;

    $query = "SELECT * FROM " . QUES_TABLE . " as ques WHERE ques.quiz_id = $quiz_id";
    
    $result = tep_db_query($query);

    if (tep_db_num_rows($result) > 0) {
        while ($questionData = tep_db_fetch_array($result)) {
            $questionLabel = $questionData['title'];
            $template->assign_block_vars('questions', array(
                'question_id'        => tep_db_output($questionData['id']),
                'quizTemplate'       => quizTemplate(tep_db_output($questionData['id']), tep_db_output($questionData['question'])),
            ));
        }
        tep_db_free_result($result);

        return true;
    }

    return false;
}


/* this switch case is not to be used now
    switch ($action) {
        case 'quiz_start':
        case 'start_test':
            $query = "SELECT * FROM " . QUES_TABLE . " as ques WHERE ques.quiz_id = $quiz_id";
            $result = tep_db_query($query);
            if (tep_db_num_rows($result) > 0) {
                while ($questionData = tep_db_fetch_array($result)) {
                    $questionLabel = $questionData['title'];
                    $template->assign_block_vars('questions', array(
                        'question_id' => tep_db_output($questionData['id']),
                        'quizTemplate' => quizTemplate(tep_db_output($questionData['id']), tep_db_output($questionData['question'])),
                    ));
                }
                tep_db_free_result($result);
            }
            break;
    }
*/

if ($quiz_id && ($action === 'quiz_start')) {
    $template->assign_vars(array(
        'HEADING_TITLE' => HEADING_TITLE,
        'SUBMIT' => SUBMIT,
        'page_title' => $quizInfo['title'],
        'meta_title' => 'Assement Topic - ' . $quizInfo['title'] . ' - Assement - ' . SITE_TITLE . ' - ' . HOST_NAME,
        'meta_description' => 'Assement Topic - ' . $quizInfo['title'] . ', ' . strip_tags($quizInfo['description'], ' < > <a ">') . ' - Assement - ' . SITE_TITLE . ' - ' . HOST_NAME,
        'QUIZ_TITLE' => $quizInfo['title'],
        'QUIZ_TIMER' => $quizInfo['timer'],
        'hidden_quiz_input_id' => tep_draw_hidden_field('quiz_id', $quiz_id) . tep_draw_hidden_field('same_window', '', 'id="same_window"'),
        'form' => tep_draw_form('submitQuiz', PATH_TO_QUIZ . FILENAME_SUBMIT_QUIZ, 'action=submitQuiz', 'post', 'name="submitted" id="formSubmit" enctype="multipart/form-data"'),
        'SCRIPT_FOR_TEST'       => script_for_test($interface_type, $quiz_id),
        'toggle_test_interface' => toggle_test_interface($interface_type, $quiz_id),
        'update_message' => $messageStack->output()
    ));
    $template->pparse('quizStartForm');
}elseif ($action == 'start_test' && in_array($test_type, ['', 'mcq']) && tep_not_null($decoded_test_id) && tep_not_null($decoded_assessment_id)) {

    $template->assign_vars(array(
        'TEST_TITLE' => TEST_TITLE,
        'SUBMIT' => SUBMIT,
        'page_title' => $quizInfo['title'],
        'meta_title' => 'Assement Topic - ' . $quizInfo['title'] . ' - Assement - ' . SITE_TITLE . ' - ' . HOST_NAME,
        'meta_description' => 'Assement Topic - ' . $quizInfo['title'] . ', ' . strip_tags($quizInfo['description'], ' < > <a ">') . ' - Assement - ' . SITE_TITLE . ' - ' . HOST_NAME,
        'QUIZ_TITLE' => $quizInfo['title'],
        'QUIZ_TIMER' => $quizInfo['timer'],
        'hidden_quiz_input_id' => tep_draw_hidden_field('quiz_id', $decoded_test_id) . tep_draw_hidden_field('assessment_id', $assessment_id) . tep_draw_hidden_field('same_window', '', 'id="same_window"'),
        'form' => tep_draw_form('submitQuiz', PATH_TO_QUIZ . FILENAME_SUBMIT_QUIZ, 'action=submitQuiz', 'post', 'name="submitted" id="formSubmit" enctype="multipart/form-data"'),
        'assign_tests' => get_list_of_candidate_questions($decoded_assessment_id, $user_id),

        'SCRIPT_FOR_TEST'       => script_for_test($interface_type, $decoded_test_id),
        'toggle_test_interface' => toggle_test_interface($interface_type, $quiz_id),
        'update_message' => $messageStack->output()
    ));
    $template->pparse('quizStartForm');


} elseif(($action == 'start_test') && (in_array($test_type, ['video']))  && tep_not_null($decoded_test_id) && tep_not_null($decoded_assessment_id)) {

    questions_for_video_or_essay($decoded_test_id);

    $template->assign_vars(array(
        'TEST_TITLE' => VIDEO_TITLE,
        'SUBMIT' => SUBMIT,
        'page_title' => $quizInfo['title'],
        
        'meta_title' => 'Assement Topic - ' . $quizInfo['title'] . ' - Assement - ' . SITE_TITLE . ' - ' . HOST_NAME,
        'meta_description' => 'Assement Topic - ' . $quizInfo['title'] . ', ' . strip_tags($quizInfo['description'], ' < > <a ">') . ' - Assement - ' . SITE_TITLE . ' - ' . HOST_NAME,
        
        'QUIZ_TITLE' => $quizInfo['title'],
        'QUIZ_TIMER' => $quizInfo['timer'],
        
        'hidden_quiz_input_id' => tep_draw_hidden_field('quiz_id', $decoded_test_id) . tep_draw_hidden_field('assessment_id', $assessment_id) . tep_draw_hidden_field('same_window', '', 'id="same_window"'),
        'form' => tep_draw_form('submit_video', PATH_TO_QUIZ . FILENAME_SUBMIT_QUIZ, 'action=submit_video', 'post', 'name="submitted" id="formSubmit" enctype="multipart/form-data"'),
        
        'assign_tests' => get_list_of_candidate_questions($decoded_assessment_id, $user_id),
        
        'upload_api_url' => HOST_NAME . 'api/upload_recorded_video.php',
        'test_id'       => $decoded_test_id,
        'jobseeker_id'  => $user_id,
        'assessment_id'  => $decoded_assessment_id,
        'REDIRECT_TO_MY_TEST' => HOST_NAME . PATH_TO_QUIZ . FILENAME_JOBSEEKER_TESTS,

        'update_message' => $messageStack->output()
    ));
    $template->pparse('video_test');
} elseif (($action == 'start_test') && (in_array($test_type, ['essay']))  && tep_not_null($decoded_test_id) && tep_not_null($decoded_assessment_id)) {
    questions_for_video_or_essay($decoded_test_id);
    $template->assign_vars(array(
        'TEST_TITLE' => ESSAY_TITLE,
        'SUBMIT' => SUBMIT,
        'page_title' => $quizInfo['title'],
        
        'meta_title' => 'Assement Topic - ' . $quizInfo['title'] . ' - Assement - ' . SITE_TITLE . ' - ' . HOST_NAME,
        'meta_description' => 'Assement Topic - ' . $quizInfo['title'] . ', ' . strip_tags($quizInfo['description'], ' < > <a ">') . ' - Assement - ' . SITE_TITLE . ' - ' . HOST_NAME,
        
        'QUIZ_TITLE' => $quizInfo['title'],
        'QUIZ_TIMER' => $quizInfo['timer'],
        
        'hidden_quiz_input_id' => tep_draw_hidden_field('quiz_id', $decoded_test_id) . tep_draw_hidden_field('assessment_id', $assessment_id) . tep_draw_hidden_field('same_window', '', 'id="same_window"'),
        
        'form' => tep_draw_form('submit_essay', PATH_TO_QUIZ . FILENAME_SUBMIT_QUIZ, 'action=submit_essay', 'post', 'name="submitted" id="formSubmit" enctype="multipart/form-data"'),

        'REDIRECT_TO_MY_TEST' => HOST_NAME . PATH_TO_QUIZ . FILENAME_JOBSEEKER_TESTS,

        'update_message' => $messageStack->output()
    ));
    $template->pparse('essay_test');
} else {
    $quiz_header_link = '
    <a href="' . tep_href_link(PATH_TO_QUIZ) . '" class="forum_sub_heading">' . INFO_TEXT_HOME . '</a>
    &nbsp;&gt;&gt;&nbsp;
    <a href="' . tep_href_link(PATH_TO_QUIZ . $quizInfo['id'] . '/' . encode_forum($quizInfo['title']) . '.html') . '" class="forum_sub_heading">
    ' . tep_db_input($quizInfo['title']) . '
    </a>';

    $template->assign_vars(array(
        'HEADING_TITLE' => HEADING_TITLE,
        'page_title' => $quizInfo['title'],
        'meta_title' => 'Assement Topic - ' . $quizInfo['title'] . ' - Assement - ' . SITE_TITLE . ' - ' . HOST_NAME,
        'meta_description' => 'Assement Topic - ' . $quizInfo['title'] . ', ' . strip_tags($quizInfo['description'], ' < > <a ">') . ' - Assement - ' . SITE_TITLE . ' - ' . HOST_NAME,
        'QUIZ_TITLE' => $quizInfo['title'],
        'QUIZ_DESCRIPTION' => $quizInfo['description'],
        'new_button' => tep_link_button_Name(
            tep_href_link(PATH_TO_QUIZ . FILENAME_QUIZ_TOPICS, 'action=quiz_start&quiz_id=' . $quiz_id),
            'btn btn-primary ',
            TAKE_QUIZ,
            ''
        ),
        'INFO_TEXT_QUIZ_HEADER_LINK' => $quiz_header_link,
        'update_message' => $messageStack->output()
    ));
    $template->pparse('quizTopic');
}
