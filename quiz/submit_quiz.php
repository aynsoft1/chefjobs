<?php
include_once("../include_files.php");
include_once(PATH_TO_MAIN_QUIZ_PHYSICAL_LANGUAGE . $language . '/' . FILENAME_SUBMIT_QUIZ);
$template->set_filenames(array(
    'resultPage' => 'quiz_result.htm',
));
include_once("../" . FILENAME_BODY);

if ($_SESSION['language'] == "spanish") {
    $language = 'es';
} else {
    $language = 'en';
}

$action = (isset($_GET['action']) ? $_GET['action'] : '');
$submitAction = (isset($_POST['submitted']) ? isset($_POST['submitted']) : '');
$optionsSelected = $_POST['quizChecked'];
$totalPoints = 0;
$message = '';
$currentDate = date("Y-m-d H:i:s");
$quiz_id = (isset($_POST['quiz_id']) ? tep_db_prepare_input($_POST['quiz_id']) : '');
$assessment_id = (isset($_POST['assessment_id']) ? tep_db_prepare_input($_POST['assessment_id']) : '');
$resultId = (isset($_GET['resultId']) ? $_GET['resultId'] : '');

$ip_address = getenv("REMOTE_ADDR");
$is_same_window = (isset($_POST['same_window']) ? tep_db_prepare_input($_POST['same_window']) : null);

if ($_SERVER['SERVER_NAME'] != 'localhost') {
    // ip2long => Converts a string containing an (IPv4) Internet Protocol dotted address into a long integer
    $client_ip = ip2long($ip_address);
} else {
    $client_ip = null;
}


// after quiz submit need get quiz id
$quizzes_id = (isset($_GET['quizzes_id']) ? tep_db_prepare_input($_GET['quizzes_id']) : '');
$user_id;

/**
 * check condition member is logged in or not
 * if jobseeker is not logged in then redirect to login page 
 * otherwise to next request
 */
if (!check_login('jobseeker') && tep_not_null($action)) {
    $_SESSION['REDIRECT_URL'] = $_SERVER['REQUEST_URI'];
    $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
    tep_redirect(tep_href_link(FILENAME_JOBSEEKER_LOGIN));
}

/**
 * get user/jobseeker id if logged in
 */
if (check_login('jobseeker')) {
    $user_id   = $_SESSION['sess_jobseekerid'];
    $user_type = 'jobseeker';
    $jobseeker_name = $_SESSION['sess_jobseekername'];
}

/**
 * Check Condition if id is present in table or not
 */
if (tep_not_null($quiz_id)) {
    if (!$row_check_quiz_id = getAnyTableWhereData(QUIZ_TABLE, "id='" . tep_db_input($quiz_id) . "'")) {
        $messageStack->add_session(MESSAGE_QUIZ_ERROR, 'error');
        tep_redirect(tep_href_link(PATH_TO_QUIZ . FILENAME_INDEX));
    }
    $quiz_id = $row_check_quiz_id['id'];
}

if (tep_not_null($quizzes_id)) {
    if (!$row_check_quiz_id = getAnyTableWhereData(QUIZ_TABLE, "id='" . tep_db_input($quizzes_id) . "'")) {
        $messageStack->add_session(MESSAGE_QUIZ_ERROR, 'error');
        tep_redirect(tep_href_link(PATH_TO_QUIZ . FILENAME_INDEX));
    }
    $quiz_id = $row_check_quiz_id['id'];
}

/**
 * check if result id is not null in results table
 */
if (tep_not_null($resultId)) {
    if (!$resultInfo = getAnyTableWhereData(QUIZ_RESULT_TABLE, "id='" . tep_db_input($resultId) . "'")) {
        $messageStack->add_session(MESSAGE_INVALID_QUIZ_ERROR, 'error');
        tep_redirect(tep_href_link(PATH_TO_QUIZ . FILENAME_INDEX));
    }
    $resultId = $resultInfo['id'];
}


/**
 * view result points where result is 
 */

function showResult()
{
    global $action, $resultInfo;
    if ($action == 'result') {
        $data = new objectInfo($resultInfo);
        return $data;
    }
    return false;
}

function storeDataInResultTable($sql_data)
{
    $link = tep_db_connect();

    $data = tep_db_perform(QUIZ_RESULT_TABLE, $sql_data);

    $insertedId = mysqli_insert_id($link);
    
    return $insertedId;
}

function store_answer_in_essay_table($arr_data)
{
    $link = tep_db_connect();

    $data = tep_db_perform(QUES_ESSAY_TABLE, $arr_data);

    $insertedId = mysqli_insert_id($link);
    
    return $insertedId;
}

/**
 * get total number of marks
 * quiz id needed
 * 
 * @param [int] $id
 */
function totalQuizMarks($id)
{
    $getMaxValue = [];

    // get total question first
    $questionTableQuery = "SELECT * FROM " . QUES_TABLE . " as ques WHERE ques.quiz_id = $id";

    $totalQuestions = tep_db_query($questionTableQuery);
    $ques = tep_db_num_rows($totalQuestions);

    foreach ($totalQuestions as $question) {
        $id = $question['id'];
        // for each question get their choice point which have max point
        $choiceTableQuery = "SELECT MAX(point) as point FROM " . QUES_CHOICE_TABLE . " WHERE question_id = $id";
        $result = tep_db_query($choiceTableQuery);
        $pointValue = tep_db_fetch_array($result);
        array_push($getMaxValue, $pointValue['point']);
    }

    $maxValue = max($getMaxValue);

    // get total points
    $outOfTotal = $ques * $maxValue;

    return $outOfTotal;
}

function resultMessage($resultValue, $quiz_id, $test_type = null)
{
    global $language;

    if (showResult()->message != null) {
        $resultAnalysis = showResult()->message;
    } else {
        $resultAnalysis = '';
    }

    if ($test_type == 'essay') {
        $pointsEn = null;
        $pointsSp = null;
    } else {
        $pointsEn = '<p>Your score was ' . $resultValue . ' out of a possible ' . totalQuizMarks($quiz_id) . '</p><p>' . $resultAnalysis . '</p>';
        $pointsSp = '<p>Tu puntaje fue ' . $resultValue . ' de una ' . totalQuizMarks($quiz_id) . ' posible</p><p>' . $resultAnalysis . '</p>';
    }

    $messageEnglish = '<h5>Thank You for taking the test.</h5>'.$pointsEn;

    $messageSpanish = '<h5>Gracias por tomar la prueba.</h5>'.$pointsSp;

    if ($language == 'es') {
        return $messageSpanish;
    } else {
        return $messageEnglish;
    }
}

/**
 * if jobseeker submit the test then send mail to employer
 *
 */
function send_mail_for_test_completion($resultId, $site_title, $jobseeker_name)
{
    // fetch invite template
    $query = "SELECT template.subject, template.message, template.mail_type 
                FROM assessment_email_templates AS template
            WHERE template.mail_type = 'complete' AND template.is_active = '1' LIMIT 0, 1";
    $res = tep_db_query($query);
    $data = tep_db_fetch_array($res);

    // fetch detail candiate name, email, rescuriter name, email, testname, and assessment name
    $resultQuery = "SELECT results.id, results.quiz_id, results.member_id, results.assessment_id,
                        quizzes.title AS test_name, assessments.title AS assessment_name,
                        CONCAT(recruiter.recruiter_first_name, ' ', recruiter.recruiter_last_name) AS recruiter_name,
                        recruiter_login.recruiter_email_address AS recruiter_email,
                        jobseeker_login.jobseeker_email_address
                    FROM results
                    INNER JOIN quizzes ON quizzes.id = results.quiz_id
                    INNER JOIN assessments ON assessments.id = results.assessment_id
                    INNER JOIN recruiter ON recruiter.recruiter_id = assessments.creator_id
                    INNER JOIN recruiter_login ON recruiter_login.recruiter_id = recruiter.recruiter_id
                    INNER JOIN jobseeker_login ON jobseeker_login.jobseeker_id = results.member_id
                    WHERE results.id = $resultId";

    $res1 = tep_db_query($resultQuery);
    $resultData = tep_db_fetch_array($res1);

    if ($data) {
        $email_subject  = $data['subject'];
        $text           = $data['message'];

        //NOTE: do not change the order or searchedArray and replacedValue
        $searchedArray = ['{CANDIDATE_NAME}','{CANDIDATE_EMAIL}','{RECRUITER_NAME}','{RECRUITER_EMAIL}','{TEST_NAME}','{ASSESSMENT_NAME}','SITE_TITLE'];
        $replacedValue  = [
            '{CANDIDATE_NAME}'  => $jobseeker_name,
            '{CANDIDATE_EMAIL}' => $resultData['jobseeker_email_address'],
            '{RECRUITER_NAME}'  => $resultData['recruiter_name'],
            '{RECRUITER_EMAIL}' => $resultData['recruiter_email'],
            '{TEST_NAME}'       => $resultData['test_name'],
            '{ASSESSMENT_NAME}' => $resultData['assessment_name'],
            'SITE_TITLE'        => $site_title,
        ];
        
        $email_text  = str_replace($searchedArray, $replacedValue, $text);

        $emailData = [
            'subject'   => $email_subject,
            'message'   => $email_text,
            'to_name'   => $resultData['recruiter_name'],
            'to_email'  => $resultData['recruiter_email'],
        ];
    } else {
        $email_body = '<!DOCTYPE html><html lang="en"><body>';
        $email_body .= "<h5>Hi ".$resultData['recruiter_name']." </h5> $jobseeker_name has done the ".$resultData['test_name']."</br></br>";
        $email_body .= "</body></html>";

        $emailData = [
            'subject'   => "Test done",
            'message'   => $email_body,
        ];
    }
    // sendmail
    tep_mail($emailData['to_name'], $emailData['to_email'], $emailData['subject'], $emailData['message'],SITE_OWNER,EMAIL_FROM);

    return $emailData;
}






// Default parameter
$template->assign_vars(array(
    'test_menus' => '
                    <a class="btn btn-sm btn-outline-secondary mr-2" href="' . tep_href_link(PATH_TO_QUIZ . FILENAME_JOBSEEKER_TESTS) . '" class="hm_color">
                        ' . MY_TESTS . '
                    </a>
                    <a class="btn btn-sm btn-outline-secondary mr-2" href="' . tep_href_link(PATH_TO_QUIZ.FILENAME_JOBSEEKER_TESTS,'action=latest-report') . '" class="hm_color">
                        Test Taken
                    </a>
                    <a class="btn btn-sm btn-outline-secondary mr-2" href="' . tep_href_link(PATH_TO_QUIZ.FILENAME_QUIZ_CALENDAR) . '" class="hm_color">
                        ' . CALENDAR . '
                    </a>
                    <a class="btn btn-sm btn-outline-secondary mr-2" href="' . tep_href_link(PATH_TO_QUIZ.FILENAME_JOBSEEKER_TESTS,'action=video-library') . '" class="hm_color">
                        Videos
                    </a>
                    <a class="btn btn-sm btn-outline-secondary mr-2" href="' . tep_href_link(PATH_TO_QUIZ.FILENAME_JOBSEEKER_TESTS,'action=invitation-link') . '">
                        Invitation
                    </a>
    ',
    'update_message' => $messageStack->output()
));

if ($submitAction || $action == 'submitQuiz' || $action == 'submit_essay') {
    
    // while form submit calculate the result
    $message = '';
    if (!empty($optionsSelected)) {
        $totalPoints = array_sum($optionsSelected);
    } else {
        $totalPoints = 0;
    }

    $decoded_assessment_id  = check_data($assessment_id,"==","assessment_id","assessment");
    if ($action == 'submit_essay') {
        $answer      = $_POST['essay_answer'];
        $question_id = $_POST['q_id'];
        $type        = 'essay';
        // in essay case total points will be 0
        $totalPoints = 0;

        // check validation for essay
        if ((strlen($answer) <= 0) || (strlen($answer) > 1000)) {
            $encoded_testID = encode_string("test_id==".$quiz_id."==test");
            $redirect_url = tep_href_link(PATH_TO_QUIZ . FILENAME_QUIZ_TOPICS, 'action=start_test&q=essay&t_id=' . $encoded_testID .'&a_id='.$assessment_id);
            $messageStack->add_session('something went wrong, answer can not be empty and the word limit is 1000 words', 'error');

            tep_redirect($redirect_url);
        }

        store_answer_in_essay_table([
            'answer'        => $answer,
            'quiz_id'       => $quiz_id,
            'question_id'   => $question_id,
            'assessment_id' => $decoded_assessment_id,
            'jobseeker_id'  => $user_id,
            'created_at'    => $currentDate,
            'updated_at'    => $currentDate,
        ]);
    } else {
        $answer = null;
        $type   = null;
    }

    $store_data_array = array(
        'total_points' => $totalPoints,
        'out_of'       => ($action == 'submit_essay') ?  null : totalQuizMarks($quiz_id),
        'quiz_id'      => $quiz_id,
        'assessment_id'=> $decoded_assessment_id,
        'member_id'    => $user_id,
        'test_type'    => $type,
        'answer'       => $answer,
        'ip_address'   => $client_ip,
        'device_on_same_window' => $is_same_window,
        'created_at'   => $currentDate,
        'updated_at'   => $currentDate,
    );

    // get quiz quiz_messages
    $messageQuery = "SELECT * FROM " . QUIZ_MESSAGE_TABLE . " WHERE quiz_id = $quiz_id";
    $totalMessages = tep_db_query($messageQuery);
    $count = tep_db_num_rows($totalMessages);
    if ($count > 0) {
        foreach ($totalMessages as $msg) {
            if (($store_data_array['total_points'] >= $msg['min_value']) && ($store_data_array['total_points'] <= $msg['max_value'])) {
                $message = $msg['message'];
            }
        }
    }

    // append message to array if length is null then not add
    if (!(strlen($message) == '')) {
        $store_data_array['message'] = $message;
    }

    // print_r($store_data_array);
    // exit;
    // store data
    $resultDataId = storeDataInResultTable($store_data_array);

    // send mail
    $eContent = send_mail_for_test_completion($resultDataId, SITE_TITLE, $jobseeker_name);  

    $messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');

    return tep_redirect(FILENAME_SUBMIT_QUIZ . '?action=result&quizzes_id=' . $quiz_id . '&resultId=' . $resultDataId);

} elseif ($quizzes_id && $action && ($action == 'result') && $resultId) {
    $template->assign_vars(array(
        'HEADING_TITLE' => HEADING_TITLE,
        'RESULT' => ($language == 'es') ? 'Resultado' : 'RESULT',
        'RESULT_ASSESMENT_MESSAGE' => resultMessage(showResult()->total_points, $quizzes_id, $row_check_quiz_id['test_type']),
        'POINTS' => showResult()->total_points,
        'update_message' => $messageStack->output()
    ));
    $template->pparse('resultPage');
}else {
    print_r('error occurred');
    die();
    $messageStack->add_session(ERROR, 'error');
    tep_redirect(tep_href_link(PATH_TO_QUIZ . FILENAME_INDEX));
}