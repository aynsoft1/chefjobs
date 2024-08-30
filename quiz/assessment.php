<?php
// Initial Setup
include_once("../include_files.php");
include_once(PATH_TO_MAIN_QUIZ_PHYSICAL_LANGUAGE . $language . '/' . FILENAME_RECRUITER_ASSESSMENT);
$template->set_filenames(
    array(
        'list_assessment'               => 'assessment/list-assessment.htm',
        'create_update_form'            => 'assessment/assessment-form.htm',
        'view_assessment_invite_page'   => 'assessment/view-assessment.htm',
        'list_video_lib'                => 'assessment/list-video-lib.htm',
        'view_video'                    => 'assessment/view-video.htm',
        'invite_list'                   => 'assessment/invite-list.htm',
    )
);
include_once("../" . FILENAME_BODY);

// global Properties
$action = (isset($_GET['action']) ? $_GET['action'] : '');
$assessment_id = (isset($_GET['id']) ? tep_db_prepare_input($_GET['id']) : '');
$edit = false;
$error = false;

$currentDate = date("Y-m-d H:i:s"); // current date
$expire_date = date("Y-m-d H:i:s", strtotime(' + 2 months')); // assessment expired date

$errorTitle;
$errorCandidateName;
$errorCandidateEmail;

/*
|--------------------------------------------------------------------------
| Global condition check like constructor
|--------------------------------------------------------------------------
| recruiter login request check
|
| if assessment id rquest available then check is assessment available 
| OR not
|
| default params pass to html pages
*/

// check if recruiter is logged in  or not
if (!check_login("recruiter")) {
    $_SESSION['REDIRECT_URL'] = $_SERVER['REQUEST_URI'];
    $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
    tep_redirect(tep_href_link(FILENAME_RECRUITER_LOGIN));
} else {
    $recruiter_id   = $_SESSION['sess_recruiterid'];
    $user_type = 'recruiter';
    $recruiterData = get_recruiter_detail($recruiter_id);

    $recruiter_name=tep_db_output($data['full_name']);
	$recruiter_email_address=tep_db_output($data['recruiter_email_address']);
}

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


// if id request available then check the assessment available or not
if (tep_not_null($assessment_id)) {
    $join_query = " LEFT JOIN jobs ON jobs.job_id = assessments.job_id ";
    if (!$assessments = getAnyTableWhereData(ASSESSMENT_TABLE . $join_query, "id='" . tep_db_input($assessment_id) . "' AND creator_id=$recruiter_id", "assessments.*, jobs.job_title")) {
        $messageStack->add_session(MESSAGE_ASSESSMENT_ERROR, 'error');
        tep_redirect(FILENAME_RECRUITER_ASSESSMENT);
    }
    $assessment_id = $assessments['id'];
    $edit = true;
}

// pass default values
$template->assign_vars(array(
    'quiz_menus' => '
                    <a class="me-2 m-none" href="' . tep_href_link(FILENAME_QUIZ . '/' . FILENAME_RECRUITER_ASSESSMENT) . '" class="hm_color">
                        ' . LIST_ASSESSMENT . '
                    </a>
                    <a class="me-2 m-none" href="' . tep_href_link(FILENAME_QUIZ . '/' . FILENAME_RECRUITER_QUIZ_REPORT, 'report=latestReport') . '" class="hm_color">
                        ' . MY_CANDIDATE . '
                    </a>
                    <a class="me-2 m-none" href="' . tep_href_link(FILENAME_QUIZ . '/' . FILENAME_RECRUITER_LIST_OF_QUIZ) . '" class="hm_color">
                        ' . MY_CUSTOM_TESTS . '
                    </a>
                    <a class="me-2 m-none" href="' . tep_href_link(FILENAME_QUIZ . '/' . FILENAME_RECRUITER_ASSESSMENT, 'action=video') . '" class="hm_color">
                        ' . TEST_VIDEOS . '
                    </a>
                    <a class="me-4 m-none" href="' . tep_href_link(FILENAME_QUIZ . '/' . FILENAME_RECRUITER_ASSESSMENT, 'action=invitation-list') . '" class="hm_color">
                        ' . INVITATION . '
                    </a>
    ',

    'API_URL_TEST_STORE' => tep_href_link(PATH_TO_QUIZ.FILENAME_RECRUITER_ASSESSMENT,'action=test-api'),

    'ASSESSMENT_HOMEPAGE'  => tep_href_link(PATH_TO_QUIZ.FILENAME_RECRUITER_ASSESSMENT,'action=test-api'),

    'quiz_menus_1' => '<a class="btn btn-sm btn-primary mr-2" href="' . tep_href_link(FILENAME_QUIZ . '/' . FILENAME_RECRUITER_ASSESSMENT, 'action=new') . '" class="hm_color">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-lg me-1" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2Z"/>
                            </svg> ' . CREATE_ASSESSMENT . '
                        </a>',

    'update_message' => $messageStack->output()
));



/*
|--------------------------------------------------------------------------
| Custom function for performing an actions
|--------------------------------------------------------------------------
|
|
*/

/**
 * generate the form tag for form
 *
 * @param string $actionValue
 * @param integer|null $a_ID
 * @return void
 */
function getFormTag(string $actionValue, int $a_ID = null)
{
    if ($actionValue == 'edit') {
        return tep_draw_form('assessment_form', PATH_TO_QUIZ . FILENAME_RECRUITER_ASSESSMENT, 'id=' . $a_ID . '&action=update_assessment', 'post', ' id="assessment_form" enctype="multipart/form-data"');
    }
    
    return tep_draw_form("assessment_form", PATH_TO_QUIZ . FILENAME_RECRUITER_ASSESSMENT, "action=submit_assessment", "post", " id='assessment_form' enctype='multipart/form-data'");
}

/**
 * get the list of recruiter active jobs
 *
 * @param integer $id
 * @return void
 */
function get_list_of_employer_active_job(int $id, int $selected_job_id = null)
{
    global $template;
    $today = date("Y-m-d H:i:s");

    $test_query = "SELECT * FROM " . JOB_TABLE . " as jobs 
                    WHERE jobs.recruiter_id = $id
                    AND jobs.re_adv <= '" . $today . "' 
                    AND jobs.expired >= '" . $today . "' 
                    AND jobs.deleted IS NULL
                    ORDER BY jobs.inserted";

    $test_result = tep_db_query($test_query);

    $options = '<option value="0">Select Job</option>';

    if (tep_db_num_rows($test_result) > 0) {
        while ($data = tep_db_fetch_array($test_result)) {
            $selected = ($selected_job_id == $data['job_id']) ? 'selected' : null ;

            $options .= '<option value="' . $data['job_id'] . '" '.$selected.'>
                                ' . $data['job_title'] . '
                        </option>';
        }
        tep_db_free_result($test_result);
    }

    $select_job = '
            <select name="template_job_id" id="template_job_id" class="form-control" required>
                ' . $options . '
            </select>      
            ';

    return $select_job;
}

function get_list_of_tests($of_emp = null)
{
    // if you want to get list test for emp only then pass the of_emp id otherwise admin test library populated
    if ($of_emp) {
        $test_query = "SELECT * FROM " . QUIZ_TABLE . " as quiz 
                        WHERE quiz.isActive = '1' 
                        AND quiz.save_as_template = 0 
                        AND quiz.recruiter_id = $of_emp 
                        ORDER BY quiz.created_at DESC";
    } else {
        $test_query = "SELECT * FROM " . QUIZ_TABLE . " as quiz 
                        WHERE quiz.isActive = '1' 
                        AND quiz.save_as_template = 1 
                        AND quiz.recruiter_id IS NULL 
                        ORDER BY quiz.created_at DESC";
    }

    $test_result = tep_db_query($test_query);

    $card = '';

    if (tep_db_num_rows($test_result) > 0) {
        while ($data = tep_db_fetch_array($test_result)) {

            $short_title = (strlen($data['title']) > 18) ? substr($data['title'],0,18).'...' : $data['title'];
            $short_desc = (strlen($data['description']) > 40) ? substr($data['description'], 0 , 40).'...' : strlen($data['description']);

            $title = '<div class="card-header border-bottom" style="border-top-left-radius: 0.8rem;border-top-right-radius: 0.8rem;border-bottom-left-radius: 0px;border-bottom-right-radius: 0px;font-weight:500;padding: 7px 17px;">'.$short_title.'</div>';
            
            $description = ($of_emp) ? '' : '<div class="card-body card-body-custom align-items-center d-flex">'.$short_desc.'</div>';

            if ($of_emp) {
                $inputField = '<input class="form-check-input" type="checkbox" name="custom_tests[]" 
                            id="custom_tests-'.$data['id'].'" value="'.$data['id'].'" data-title="'.$data['title'].'" data-duration="'.$data['timer'].' min" data-id="'.$data['id'].'" />
                        <label for="custom_tests-'.$data['id'].'" class="pe-3 me-2">Select Test</label>';
            } else {
                $inputField = '<input class="form-check-input" type="checkbox" name="tests[]" 
                            id="tests-'.$data['id'].'" value="'.$data['id'].'" data-title="'.$data['title'].'" data-duration="'.$data['timer'].' min" data-id="'.$data['id'].'" />
                            <label for="tests-'.$data['id'].'" class="ms-2 fw-bold">Select Test</label>';
            }

            // custom_tests
            $card .= '
                <div class="col-3 mb-3">
                    <div class="card card-custom" id="card-'.$data['id'].'">
                        '.$title.$description.'
                        <div class="card-footer card-footer-custom">
                            '.$inputField.'
                        </div>
                    </div>
                </div>
                ';
        }
        tep_db_free_result($test_result);
    }

    return $card;
}

function store_assessment_with_pivot_table(array $assessmentData, array $quizzesId)
{
    $link = tep_db_connect();

    $data = tep_db_perform(ASSESSMENT_TABLE, $assessmentData);

    $assessmentID = mysqli_insert_id($link);

    if ($assessmentID) {
        foreach ($quizzesId as $q_ID) {
            $store_choices = array(
                'quiz_id'        => $q_ID,
                'assessment_id'  => $assessmentID,
            );
            tep_db_perform(ASSESSMENT_QUIZ_TABLE, $store_choices);
        }
    }

    return true;
}

function delete_assessment_quiz_row_where_assessment_id_is($id)
{
    tep_db_query("delete from " . ASSESSMENT_QUIZ_TABLE . " where assessment_id='" . tep_db_input($id) . "'");
    
    return true;
}

function update_assessment(int $assessmentID, array $assessmentData, array $quizzesId)
{
    $q1 = tep_db_perform(ASSESSMENT_TABLE, $assessmentData, 'update', "id='" . $assessmentID . "'");

    if ($q1) {
        // delete the old assessment_quiz data based on assessement_id
        $q2 = delete_assessment_quiz_row_where_assessment_id_is($assessmentID);
        // insert new selected quizzes id
        if ($q2) {
            foreach ($quizzesId as $q_ID) {
                $store_choices = array(
                    'quiz_id'        => $q_ID,
                    'assessment_id'  => $assessmentID,
                );
                tep_db_perform(ASSESSMENT_QUIZ_TABLE, $store_choices);
            }
        }
    }

    return true;
}

function delete_assessment(int $id)
{
    $data = tep_db_query("delete from " . ASSESSMENT_TABLE . " where id='" . tep_db_input($id) . "'");
    
    return $data;
}

/**
 * send mail and store sent mail to db
 * 
 * while sending mail to same user for same assessment update the counter otherwise create new one
 * 
 * @param array $data
 */
function store_update_value_to_invite_mail_db($assessment_id, array $data)
{
    global $currentDate;
    $site_title = SITE_TITLE;
    
    // fetch data
    $email = $data['email_to'];
    $query = "SELECT * FROM ". ASSESSMENT_INVITEMAIL_TABLE. " WHERE email_to = '$email' AND assessment_id = $assessment_id ";

    $test_result = tep_db_query($query);

    // update invite mail table if invitation already sent otherwise create new invite 
    if (tep_db_num_rows($test_result) > 0){
        $res = tep_db_fetch_array($test_result);
        $counter = $res['counter'] + 1;

        // if row found use row uuid
        $uuid_of_mail = $res['uuid'];
        $assessmentLink = tep_href_link(PATH_TO_QUIZ.FILENAME_TEST_TAKER_INVITE_LINK,'action=takeinvitation&uuid='.$uuid_of_mail);
        
        $eContent = email_data($data['recruiter_name'], $data['candidate_name'], $assessmentLink, $site_title, $data);
        $email_subject = $eContent['subject'];
        $email_text = $eContent['message'];

        $updatedData = [
            'subject'     => $email_subject,
            'message'     => $email_text,
            'counter'     => $counter,
            'updated_at'  => $currentDate,
        ];

        // print_r($email_text);
        // exit;
        // send mail
        tep_mail($data['candidate_name'], $data['email_to'], $email_subject, $email_text, SITE_OWNER, EMAIL_FROM);

        // update data
        tep_db_perform(ASSESSMENT_INVITEMAIL_TABLE, $updatedData, 'update', "id='" . $res['id'] . "'");
        

    } else {
        $link = tep_db_connect();

        $uuid_of_mail = uniqid('assessment-'.$assessment_id, true);
        $assessmentLink = tep_href_link(PATH_TO_QUIZ.FILENAME_TEST_TAKER_INVITE_LINK,'action=takeinvitation&uuid='.$uuid_of_mail);

        $eContent = email_data($data['recruiter_name'], $data['candidate_name'], $assessmentLink, $site_title, $data);
        $email_subject = $eContent['subject'];
        $email_text = $eContent['message'];

        $newData = [
            'uuid'              => $uuid_of_mail,
            'subject'           => $email_subject,
            'message'           => $email_text,
            'recruiter_id'      => $data['recruiter_id'],
            'candidate_name'    => $data['candidate_name'],
            'email_to'          => $data['email_to'],
            'assessment_id'     => $assessment_id,
            'created_at'        => $currentDate,
            'updated_at'        => $currentDate,
        ];
        // print_r($email_text);
        // exit;
        // send mail
        tep_mail($data['candidate_name'], $data['email_to'], $email_subject, $email_text, SITE_OWNER, EMAIL_FROM);

        // store data
        tep_db_perform(ASSESSMENT_INVITEMAIL_TABLE, $newData);
    
        $assessmentID = mysqli_insert_id($link);
    }


    return true;
}

function actionBtn(int $id)
{
    $button = '
    <div class="btn-group">
        <button type="button" class="btn btn-dropdown dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            Action
        </button>
        <div class="dropdown-menu">
            <a class="dropdown-item" href="' . tep_href_link(PATH_TO_QUIZ . FILENAME_RECRUITER_ASSESSMENT, 'id=' . $id .'&action=edit') . '" name="editBtn" id="editBtn-'.$id.'">
                ' . EDIT_TEXT . '
            </a>
            <a class="dropdown-item" href="' . tep_href_link(PATH_TO_QUIZ . FILENAME_RECRUITER_ASSESSMENT, 'id=' . $id .'&action=view') . '" name="viewBtn" id="viewBtn-'.$id.'">
                ' . VIEW_TEXT . '
            </a>
            <a class="dropdown-item" href="' . tep_href_link(PATH_TO_QUIZ . FILENAME_RECRUITER_ASSESSMENT, '&action=confirm_delete&id='.$id) . '" 
                name="deleteBtn" 
                id="deleteBtn-'.$id.'">
                ' . DELETE_TEXT . '
            </a>
        </div>
    </div>
    ';
    return $button;
}

function email_data($senderName, $receiverName, $link, $site_title, $dataArray = null)
{
    // fetch invite template
    $query = "SELECT template.subject, template.message, template.mail_type 
                FROM assessment_email_templates AS template
            WHERE template.mail_type = 'invite' AND template.is_active = '1' LIMIT 0, 1";

    $res = tep_db_query($query);

    $data = tep_db_fetch_array($res);

    if ($data) {
        $email_subject  = $data['subject'];
        $text           = $data['message'];
        $searchedArray = ['{CANDIDATE_NAME}','{RECRUITER_NAME}','{SITE_TITLE}','{LINK_ASSESSMENT}', '{ASSESSMENT_NAME}', '{JOB_NAME}', '{START_DATE}', '{END_DATE}'];
        $replacedValue  = [
            '{CANDIDATE_NAME}'  => $receiverName,
            '{RECRUITER_NAME}'  => $senderName,
            '{SITE_TITLE}'      => $site_title,
            '{LINK_ASSESSMENT}' => "<a href='".$link."'>Take assessment</a>",
            '{ASSESSMENT_NAME}' => $dataArray['assessment_name'],
            '{JOB_NAME}'        => $dataArray['assessment_job'],
            '{START_DATE}'      => $dataArray['created_at'],
            '{END_DATE}'        => $dataArray['expired_at'],
        ];
        $email_text  = str_replace($searchedArray, $replacedValue, $text);

        $emailData = [
            'subject'   => $email_subject,
            'message'   => $email_text,
        ];
    } else {
        $email_body = '<!DOCTYPE html><html lang="en"><body>';
        $email_body .= "<h5>Hi $receiverName </h5></br></br>";
        $email_body .= "$senderName from $site_title has invited you to take a assessment. </br></br>";
        $email_body .= "<a href='".$link."'>Take assessment</a>";
        $email_body .= "</body></html>";

        $emailData = [
            'subject'   => "You've been invited to an assessment",
            'message'   => $email_body,
        ];
    }

    return $emailData;
}

function get_list_of_videos($id)
{
    global $template;

    // get the list of assign test
    $list_test_query = "SELECT video.*, 
                            assessments.title AS assessment, 
                            test.title AS test, 
                            job.job_title, 
                            recruiter.recruiter_company_name AS company, 
                            CONCAT(jobseeker.jobseeker_first_name, ' ', jobseeker.jobseeker_last_name) AS jobseeker_name, 
                            questions.question,
                            recruiter.recruiter_company_name AS company
                        FROM quiz_videos AS video
                        INNER JOIN assessments ON assessments.id = video.assessment_id
                        INNER JOIN quizzes AS test ON test.id = video.quiz_id
                        INNER JOIN jobs AS job ON job.job_id = assessments.job_id
                        INNER JOIN recruiter ON assessments.creator_id = recruiter.recruiter_id
                        INNER JOIN jobseeker ON jobseeker.jobseeker_id = video.jobseeker_id
                        INNER JOIN questions ON questions.id = video.question_id
                        WHERE recruiter.recruiter_id = $id";

    $res = tep_db_query($list_test_query);

    if (tep_db_num_rows($res) > 0) {
        while ($data = tep_db_fetch_array($res)) {
            $video_path = tep_href_link($data['file_path']);
            
            $encode_vid_id = encode_string("video_id==".$data['id']."==video");

            $template->assign_block_vars('video_lib', array(
                // 'name'       => $data['file_name'],
                'name'       => '<a href="'.tep_href_link(PATH_TO_QUIZ.FILENAME_RECRUITER_ASSESSMENT,'action=video&v_id='.$encode_vid_id).'">
                                    <video controls width="125"><source src="'.$video_path.'" type="video/webm"></video>
                                </a>',
                'file_path'  => $data['file_path'],
                'created_at' => tep_date_short($data['created_at']),
                'assessment' => $data['assessment'],
                'jobseeker_name' => $data['jobseeker_name'],
                'company' => $data['company'],
                'test'       => $data['test'],
                'job_title'  => $data['job_title'],
                'question'    => $data['question'],
            ));
        }
        tep_db_free_result($res);
        return true;
    }

    return false;
}


function find_video_by_id($recruiter_id, $id)
{
    global $template;

    // get the list of assign test
    $list_test_query = "SELECT video.*, 
                            assessments.title AS assessment, 
                            test.title AS test, 
                            job.job_title, 
                            recruiter.recruiter_company_name AS company, 
                            CONCAT(jobseeker.jobseeker_first_name, ' ', jobseeker.jobseeker_last_name) AS jobseeker_name, 
                            questions.question, jl.jobseeker_email_address AS candidate_email, jobseeker.jobseeker_mobile,
                            result.id AS message_id, result.message, result.ip_address
                        FROM quiz_videos AS video
                        INNER JOIN assessments ON assessments.id = video.assessment_id
                        INNER JOIN quizzes AS test ON test.id = video.quiz_id
                        INNER JOIN jobs AS job ON job.job_id = assessments.job_id
                        INNER JOIN recruiter ON assessments.creator_id = recruiter.recruiter_id
                        INNER JOIN jobseeker ON jobseeker.jobseeker_id = video.jobseeker_id
                        INNER JOIN questions ON questions.id = video.question_id
                        INNER JOIN jobseeker_login AS jl ON jl.jobseeker_id = jobseeker.jobseeker_id AND video.jobseeker_id = jl.jobseeker_id
                        INNER JOIN results AS result ON result.quiz_id = test.id AND assessments.id = result.assessment_id
                        WHERE recruiter.recruiter_id = $recruiter_id AND video.id = $id";

    $res = tep_db_query($list_test_query);

    if (tep_db_num_rows($res) > 0) {
        $data = tep_db_fetch_array($res);

        return $data;
    }

    return false;
}

function get_ip_location($ip) {

    $response = file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip);

    $json_response = json_decode($response);

    return $json_response;
}

function get_jobseeker_resume_id($jobseeker_id)
{
    $query = "SELECT jobseeker_id, resume_id FROM ". JOBSEEKER_RESUME1_TABLE ." WHERE jobseeker_id=$jobseeker_id LIMIT 1";

    $result = tep_db_query($query);

    $data = tep_db_fetch_array($result);

    if (tep_db_num_rows($result) > 0) {
        return $data['resume_id'];
    }

    return false;
}



function recuriter_assessment_list($recruiter_id)
{
    global $template;
    // data fetch query apply
    $raw_query = "SELECT assessment.*, job.job_title, COUNT(aj.assessment_id) AS no_of_candidate, COUNT(aq.assessment_id) AS no_of_test  
                    FROM " . ASSESSMENT_TABLE . " as assessment
                    LEFT JOIN jobs as job ON assessment.job_id = job.job_id
                    LEFT JOIN assessment_jobseeker as aj ON assessment.id = aj.assessment_id
                    LEFT JOIN assessment_quiz as aq ON aq.assessment_id = assessment.id
                    WHERE assessment.is_active = '1' AND assessment.creator_id = $recruiter_id
                    GROUP BY aj.assessment_id, assessment.id, aq.assessment_id 
                    ORDER BY assessment.created_at DESC";

    $assessment_data = tep_db_query($raw_query);

    if (tep_db_num_rows($assessment_data) > 0) {
        while ($assmnt = tep_db_fetch_array($assessment_data)) {
            $template->assign_block_vars('assessment', array(
            'id' => tep_db_output($assmnt['id']),
            'title' => '<a class="text-dark fw-bold" href="' . tep_href_link(PATH_TO_QUIZ . FILENAME_RECRUITER_ASSESSMENT, 'id=' . $assmnt['id'] .'&action=view') . '">
                        ' . tep_db_output($assmnt['title']) .
            '</a>',
            'no_of_candidate' => tep_db_output($assmnt['no_of_candidate']),
            'no_of_test' => tep_db_output($assmnt['no_of_test']),
            'job_title' => tep_db_output($assmnt['job_title']),
            'created_at' => tep_date_short($assmnt['created_at']),
            'action' => actionBtn(tep_db_output($assmnt['id'])),
            'invite_link' => '<a class="btn btn-success btn-success-sm" href="' . tep_href_link(PATH_TO_QUIZ . FILENAME_RECRUITER_ASSESSMENT, 'id='.tep_db_output($assmnt['id']).'&action=view') . '"><i class="bi bi-person"></i> '.TB_HEAD_INVITE.'</a>',
            ));
        }
        tep_db_free_result($assessment_data);
    }
}

function get_recruiter_report($recruiter_id) {
    $total_assessment   = no_of_records(ASSESSMENT_TABLE, "is_active = '1' AND creator_id = $recruiter_id");
    $total_tests        = no_of_records(QUIZ_TABLE, "recruiter_id = $recruiter_id AND isActive = '1'");
    $total_questions    = no_of_records(QUES_TABLE . ' INNER JOIN quizzes ON quizzes.id = questions.quiz_id', "quizzes.recruiter_id = $recruiter_id AND questions.isActive = 1");

    $total_candidates   = no_of_records(QUIZ_RESULT_TABLE . " INNER JOIN quizzes ON quizzes.id = results.quiz_id INNER JOIN assessments ON assessments.id = results. assessment_id", " assessments.creator_id = $recruiter_id");
    $total_videos       = no_of_records(TEST_VIDEO_TABLE . " AS video INNER JOIN quizzes AS test ON test.id = video.quiz_id", " test.recruiter_id = $recruiter_id");

    $reportData = [
        ['title' => 'Assessments', 'total' => $total_assessment, 'class' => 'bg-primarya','iconClass' => 'bi bi-file-earmark-text icon-style1'], 
        ['title' => 'Tests', 'total' => $total_tests, 'class' => 'bg-primarya','iconClass' => 'bi bi-book icon-style2'],
        ['title' => 'Questions', 'total' => $total_questions, 'class' => 'bg-primarya','iconClass' => 'bi bi-question-lg icon-style3'],
        ['title' => 'Candidates', 'total' => $total_candidates, 'class' => 'bg-primarya','iconClass' => 'bi bi-people icon-style4'],
        ['title' => 'Videos', 'total' => $total_videos, 'class' => 'bg-primarya','iconClass' => 'bi bi-play-circle icon-style5'],
    ];

    return $reportData;
}

function invitation_send_by_recruiter($recruiter_id) {
    global $template;

    $raw_query = "SELECT ai.*, assessments.title AS assessment_name  
                FROM `assessment_invitemails` AS ai
                INNER JOIN assessments ON assessments.id = ai.assessment_id
                WHERE ai.recruiter_id = $recruiter_id ORDER BY ai.id DESC";

    $res = tep_db_query($raw_query);

    if (tep_db_num_rows($res) > 0) {
    while ($data = tep_db_fetch_array($res)) {
        $status = ($data['accepted'] == '1') ? '<span class="text-success">Accepted</span>' : '<span class="text-danger">Pending</span>';

        $template->assign_block_vars('invitations', array(
            'email_to'  => $data['email_to'],
            'status'    => $status,
            'assessment_name'=> $data['assessment_name'],
            'date'      => tep_date_short($data['created_at']),
        ));
    }
        tep_db_free_result($res);
    }
}








/*
|--------------------------------------------------------------------------
| Database Related part Store/update/delete 
|--------------------------------------------------------------------------
| if request submit_assessment available then store
|
| if confirm_delete AND assessment id available then perform delete
*/


if ($action == 'confirm_delete' AND tep_not_null($assessment_id)) {
    delete_assessment($assessment_id);
    
    $messageStack->add_session(MESSAGE_SUCCESS_DELETED, 'success');
    
    return tep_redirect(FILENAME_RECRUITER_ASSESSMENT);
} 

// send invitation mail
if ($action == 'view' AND tep_not_null($assessment_id) AND $_SERVER['REQUEST_METHOD'] == 'POST')
{
    $to_name = stripslashes($_POST['candidate_full_name']);
    $to_email_address = stripslashes($_POST['candidate_email']);

    if (strlen($to_name) <= 0 OR strlen($to_email_address) <= 0) {
        $error = true;
        $errorCandidateName = true;
        $errorCandidateEmail = true;
    }

    if(!$error)
    {
        // store invite mail to database
        $dataArr = [
            'assessment_name'   => $assessments['title'],
            'assessment_job'    => $assessments['job_title'],
            'recruiter_id'      => $recruiter_id,
            'recruiter_name'    => $recruiter_name,
            'candidate_name'    => $to_name,
            'email_to'          => $to_email_address,
            'job_name'          => $assessments['job_title'],
            'created_at'        => tep_date_short($assessments['created_at']),
            'expired_at'        => tep_date_short($assessments['expired_at']),
        ];

        // print_r($dataArr);
        // exit;
        // store data and send mail
        store_update_value_to_invite_mail_db($assessment_id, $dataArr);

        $messageStack->add_session(SUCCESS_EMAIL_SENT, 'success');
        tep_redirect(FILENAME_RECRUITER_ASSESSMENT);
    }
}

if ($action == 'submit_message' AND $_SERVER['REQUEST_METHOD'] == 'POST') {
    $text_message = $_POST['message'];
    $m_id  = $_POST['m_id'];
    $vid_id = $_POST['vid_id'];

    $decoded_message_id = check_data($m_id,"==","m_id","message");

    if (empty($text_message)) {
        
        $messageStack->add_session(MESSAGE_UPDATED_ERROR, 'error');
        
        tep_redirect(FILENAME_RECRUITER_ASSESSMENT.'?action=video&v_id='.$vid_id);

    } else {
        $inputFields = [
            'message' => $text_message,
        ];

        // update message in result table
        tep_db_perform(QUIZ_RESULT_TABLE, $inputFields, 'update', "id='" . $decoded_message_id . "'");
        
        $messageStack->add_session(MESSAGE_UPDATED_SUCCESS, 'success');
        tep_redirect(FILENAME_RECRUITER_ASSESSMENT.'?action=video&v_id='.$vid_id);
    }
}














/*
|--------------------------------------------------------------------------
| Render To HTML Page based on conditions
|--------------------------------------------------------------------------
| 
|
*/

if ($action == 'new') {
    // Go to create or edit form
    $template->assign_vars(array(
        'HEADING_TITLE'     => CREATE_FORM_HEADING,
        'form'              => getFormTag($action),
        'label_title'       => LABEL_TITLE,
        'label_template'    => LABEL_TEMPLATE,
        'input_title'       => tep_draw_input_field('title', '', 'class="form-control" id="title" required', '', 'text'),
        'dropdown_jobs'     => get_list_of_employer_active_job($recruiter_id),
        'dropdown_test_lib' => get_list_of_tests(),
        'custom_ques_lib'   => get_list_of_tests($recruiter_id),
        'STEP_1'            => STEP_1,
        'STEP_2'            => STEP_2,
        'STEP_3'            => STEP_3,
        'STEP_4'            => STEP_4,
        'add_job'           => '<a class="btn-ad" target="_blank" href="'.tep_href_link(FILENAME_RECRUITER_POST_JOB).'"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-lg me-1" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2Z"/>
                                </svg> Add job</a>',
        'add_custom_test'   => '<a class="btn btn-primary" href="'.tep_href_link(PATH_TO_QUIZ.FILENAME_RECRUITER_LIST_OF_QUIZ,'action=select-test').'">Add Your Own Questions</a>',
        'add_my_own_test'   => load_custom_modal_button(),
        'my_questions_url'  => tep_href_link(PATH_TO_QUIZ.FILENAME_RECRUITER_ASSESSMENT,'action=api-my-questions'),

        'API_URL_ASSMNT_STORE' => tep_href_link(PATH_TO_QUIZ.FILENAME_RECRUITER_ASSESSMENT,'action=submit_assessment'),
        'FORM_TYPE'            => 'POST',
    ));
    $template->pparse('create_update_form');
} elseif ($action == 'submit_assessment' AND $_SERVER['REQUEST_METHOD'] == 'POST' AND $recruiter_id) {
    $api_errors = [];
    $api_data = [];

    $assessmentTitle = $_POST['title'];
    $jobID = $_POST['template_job_id'];
    $templateIds = $_POST['tests'];
    $customTest = $_POST['custom_tests'];

    $store_assessment = array(
        'title'        => $assessmentTitle,
        'job_id'       => $jobID,
        'assigned_by'  => 'recruiter',
        'creator_id'   => $recruiter_id,
        'expired_at'   => $expire_date,
        'created_at'   => $currentDate,
        'updated_at'   => $currentDate,
    );

    if ((count($templateIds) > 0) AND (count($customTest) > 0)) {
        $merged_test_array = array_merge($templateIds, $customTest);
        $testIds = array_unique($merged_test_array);
    } else {
        if (count($customTest) > 0) {
            $testIds = $customTest;
        }
        
        if (count($templateIds) > 0) {
            $testIds = $templateIds;
        }
    }


    if ($testIds > 0) {
        store_assessment_with_pivot_table($store_assessment, $testIds);
        // $messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');
        $api_data['success'] = true;
        $api_data['message'] = 'assessment created!';
    } else {
        $api_data['success'] = false;
        $api_data['message'] = 'something went wrong';
        // $messageStack->add_session(MESSAGE_ASSESSMENT_ERROR_1, 'error');
    }
    
    // return tep_redirect(FILENAME_RECRUITER_ASSESSMENT);
    echo json_encode($api_data);
} elseif ($action == 'edit' AND tep_not_null($assessment_id)) {
    $template->assign_vars(array(
        'HEADING_TITLE'     => EDIT_FORM_HEADING,
        'form'              => getFormTag($action, $assessment_id),
        'label_title'       => LABEL_TITLE,
        'label_template'    => LABEL_TEMPLATE,
        'input_title'       => tep_draw_input_field('title', $assessments['title'], 'class="form-control" id="title" required', '', 'text'),
        'dropdown_jobs'     => get_list_of_employer_active_job($recruiter_id, $assessments['job_id']),
        'dropdown_test_lib' => get_list_of_tests(),
        'custom_ques_lib'   => get_list_of_tests($recruiter_id),
        'STEP_1'            => STEP_1,
        'STEP_2'            => STEP_2,
        'STEP_3'            => STEP_3,
        'STEP_4'            => STEP_4,
        'add_custom_test'   => '<a class="btn btn-primary" href="'.tep_href_link(PATH_TO_QUIZ.FILENAME_RECRUITER_LIST_OF_QUIZ,'action=select-test').'">Add Your Own Tests</a>',
        'add_my_own_test'   => load_custom_modal_button(),
        'my_questions_url'  => tep_href_link(PATH_TO_QUIZ.FILENAME_RECRUITER_ASSESSMENT,'action=api-my-questions'),

        'API_URL_ASSMNT_UPDATE' => tep_href_link(PATH_TO_QUIZ.FILENAME_RECRUITER_ASSESSMENT,"action=update_assessment&id=$assessment_id"),
        'FORM_TYPE'            => 'PUT',
    ));
    $template->pparse('create_update_form');
} elseif ($action == 'update_assessment' AND $_SERVER['REQUEST_METHOD'] == 'POST' AND $recruiter_id) {
    $api_errors = [];
    $api_data = [];

    $assessmentTitle = $_POST['title'];
    $jobID = $_POST['template_job_id'];
    $templateIds = $_POST['tests'];
    $customTest = $_POST['custom_tests'];

    $store_assessment = array(
        'title'        => $assessmentTitle,
        'job_id'       => $jobID,
        'assigned_by'  => 'recruiter',
        'creator_id'   => $recruiter_id,
        'created_at'   => $currentDate,
        'updated_at'   => $currentDate,
    );
    
    if ((count($templateIds) > 0) AND (count($customTest) > 0)) {
        $merged_test_array = array_merge($templateIds, $customTest);
        $testIds = array_unique($merged_test_array);
    } else {
        if (count($customTest) > 0) {
            $testIds = $customTest;
        }
        
        if (count($templateIds) > 0) {
            $testIds = $templateIds;
        }
    }

    if ($testIds > 0) {
        update_assessment($assessment_id, $store_assessment, $testIds);
        $api_data['success'] = true;
        $api_data['message'] = 'assessment updated!';
    } else {
        $api_data['success'] = false;
        $api_data['message'] = 'something went wrong';
    }
    // $messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');
    // return tep_redirect(FILENAME_RECRUITER_ASSESSMENT);
    echo json_encode($api_data);

} elseif ($action == 'view') {
    
    // fetch list of test for assessment
    $query = "SELECT q.id, q.title as quiz, q.timer, a.id, a.title as assessment
                FROM ".QUIZ_TABLE." as q
                JOIN assessment_quiz as aq ON aq.quiz_id = q.id
                JOIN assessments as a ON a.id = aq.assessment_id
                WHERE a.id = " . $assessments['id'] . " ORDER BY q.id DESC";

    $assess_test_query = tep_db_query($query);
        
    if (tep_db_num_rows($assess_test_query) > 0) {
        while ($test_lib = tep_db_fetch_array($assess_test_query)) {
            $alternate++;
            $template->assign_block_vars('library', array(
                'id' => tep_db_output($test_lib['id']),
                'title' => tep_db_output($test_lib['quiz']),
                'timer' => ' '. tep_db_output($test_lib['timer']). ' min',
            ));
        }
        tep_db_free_result($assess_test_query);
    }


    // fetch the invited candidates for an assessment
    $query_2 = "SELECT * FROM " . ASSESSMENT_INVITEMAIL_TABLE . "
                WHERE assessment_id = $assessment_id 
                AND recruiter_id = $recruiter_id ORDER BY id DESC";

    $test_query_2 = tep_db_query($query_2);
        
    if (tep_db_num_rows($test_query_2) > 0) {
        while ($invited_mails = tep_db_fetch_array($test_query_2)) {
            
            
            if ($invited_mails['accepted']) {
                $inviteStatus = 'accepted';
            } else {
                $inviteStatus = 'pending';
            }

            $invite_link = '<a class="text-dark" href="'.tep_href_link(PATH_TO_QUIZ.FILENAME_TEST_TAKER_INVITE_LINK,'action=takeinvitation&uuid='.$invited_mails['uuid']).'" target="_new" rel="noopener noreferrer">link</a>';
            
            $template->assign_block_vars('invited_candidates', array(
                'id' => tep_db_output($invited_mails['id']),                
                'name' => tep_db_output($invited_mails['candidate_name']),
                'email' => tep_db_output($invited_mails['email_to']),
                'link'  => $invite_link,
                'status' => $inviteStatus,
                'invited_on' => tep_date_short(tep_db_output($invited_mails['created_at'])),
            ));
        }
        tep_db_free_result($test_query_2);
    }



    $template->assign_vars(array(
        'HEADING_TITLE'              => strtolower($assessments['title']),
        'HEADING_INVITE_CANDIDATE'   => HEADING_INVITE_CANDIDATE,
        'HEADING_INCLUDED_TESTS'     => HEADING_INCLUDED_TESTS,
        
        'NAME'                       => NAME,
        'EMAIL'                      => EMAIL,
        'INVITED_ON'                 => INVITED_ON,
        'CANDIDATES'                 => CANDIDATES,
        'CANDIDATE_INVITATION_LINK'  => CANDIDATE_INVITATION_LINK,
        'CANDIDATE_INVITATION_STATUS'=> CANDIDATE_INVITATION_STATUS,
        
        'INFO_TEXT_TO_NAME'          => INFO_TEXT_TO_NAME,
        'INFO_TEXT_SUBJECT'          => INFO_TEXT_SUBJECT,
        'INFO_TEXT_TO_EMAIL_ADDRESS' => INFO_TEXT_TO_EMAIL_ADDRESS,
        'INFO_TEXT_MESSAGE'          => INFO_TEXT_MESSAGE,
    
        
        'TEST'                       => TEST,
        'DURATION'                   => DURATION,
        'INFO_TEXT_TO_NAME1'         => tep_draw_input_field('candidate_full_name',$to_name,'id="candidate-name" size="40" class="form-control required" placeholder="Full name"',false),
        'INFO_TEXT_TO_EMAIL_ADDRESS1'=> tep_draw_input_field('candidate_email', $to_email_address,'id="candidate-email" size="40" class="form-control required" placeholder="Email address"',false),
        'INFO_TEXT_SUBJECT1'         => tep_draw_input_field('TR_subject', $email_subject,'size="40" class="form-control required"',false),
        'INFO_TEXT_MESSAGE1'         => tep_draw_textarea_field('TR_message', 'soft', '50', '8', $TR_message, 'class="form-control-postjob7 required"', '',false),
        
        'ERROR_CANDIDATE_NAME'       => ($errorCandidateName) ? CANDIDATE_NAME_ERROR : '',
        'ERROR_CANDIDATE_EMAIL'      => ($errorCandidateEmail) ? CANDIDATE_EMAIL_ADDRESS_ERROR : '',
        
        'form'=>tep_draw_form('send_invitation', PATH_TO_QUIZ.FILENAME_RECRUITER_ASSESSMENT, 'id='.$assessment_id.'&action=view', 'post', 
                                'id="send_invitation"').tep_draw_hidden_field('action','send_invitation'),
        'button'=>tep_button_submit('btn btn-primary', BTN_SEND, 'id="invite-btn"'),
        // 'button'=> '<button type="button" id="invite-btn" class="btn btn-outline-primary float-right" role="button">'.BTN_SEND.'</button>',
    ));
    $template->pparse('view_assessment_invite_page');
} elseif ($action == 'api' AND $_GET['ids']) {
    $ids = $_GET['ids'];
    $data = [];

    $query = "SELECT quiz.id, quiz.title, quiz.timer FROM quizzes AS quiz WHERE quiz.id IN ( $ids )";
    $test_result = tep_db_query($query);

    if (tep_db_num_rows($test_result) > 0) {
        while ($res = tep_db_fetch_array($test_result)) {
            $data[] = $res;
        }
        
        echo json_encode($data);
    } else {
        return json_encode(false);
    }
 } elseif ($action == 'video') {
     if ($_GET['v_id']) {

        $decoded_vid_id = check_data($_GET['v_id'],"==","video_id","video");
        $data = find_video_by_id($recruiter_id, $decoded_vid_id);

        $resume_id = get_jobseeker_resume_id($data['jobseeker_id']);
        $query_string=encode_string("search_id==".$resume_id."==search");
        
        $encoded_message_id = encode_string("m_id==".$data['message_id']."==message");
        if ($resume_id) {
            $view_resume = '<a class="btn btn-outline-link" 
                                href="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,'query_string1='.$query_string).'" 
                                target="_new" rel="noopener noreferrer">'.VIEW_RESUME.'</a>';
        } else {
            $view_resume = null;
        }

        // long2ip => Converts an long integer address into a string in (IPv4) Internet standard dotted format
        $ip = long2ip($data['ip_address']);           
        $ipLocation = get_ip_location($ip);

        $template->assign_vars(array(
            'VIDEO_TITLE'       => VIDEO_TITLE,
            'CANDIDATE_NAME'    => $data['jobseeker_name'],
            'CANDIDATE_EMAIL'   => $data['candidate_email'],
            'VIDEO_QUESTION'    => $data['question'],
            'CANDIDATE_MOBILE'  => $data['jobseeker_mobile'],
            'IP_ADDRESS'        => 'IP Address: ' . $ip,
            'IP_LOCATON'        => 'Location: ' . $ipLocation->geoplugin_city,
            'VIEW_RESUME'       => $view_resume,
            'UPLOADED_DATE'     => tep_date_short($data['created_at']),
            'VIDEO_ANS'         => '<video controls width="100%"><source src="'.tep_href_link($data['file_path']).'" type="video/webm"></video>',
            'form'              => tep_draw_form("message_form", PATH_TO_QUIZ . FILENAME_RECRUITER_ASSESSMENT, "action=submit_message", 
                                                    "post", " id='message_form' 
                                                    enctype='multipart/form-data'").tep_draw_hidden_field('m_id', $encoded_message_id).tep_draw_hidden_field('vid_id', $_GET['v_id']),
            'MESSAGE_LABEL'     => MESSAGE_LABEL,
            'MESSAGE_SHORT_LABEL' => MESSAGE_SHORT_LABEL,
            'MESSAGE_INPUT'     => tep_draw_textarea_field('message', 'soft', '30', '4', $data['message'], 'class="form-control" id="message" required', '', true),
            'submit_btn'        => $data['message'] ? tep_button_submit('btn btn-outline-primary float-right mb-2', UPDATE_BTN) : tep_button_submit('btn btn-outline-primary float-right mb-2', SUBMIT_BTN),
        ));
        $template->pparse('view_video');
    } else {
        get_list_of_videos($recruiter_id);
        
        $template->assign_vars(array(
            'VIDEO_TITLE'               => VIDEO_TITLE,
            'TB_HEAD_VIDEO'             => TB_HEAD_VIDEO,
            'TB_HEAD_TEST_NAME'         => TB_HEAD_TEST_NAME,
            'TB_HEAD_ASSESSMENT_NAME'   => TB_HEAD_ASSESSMENT_NAME,
            'TB_HEAD_DATE'              => TB_HEAD_DATE_1,
            'TB_HEAD_JOB'               => TB_HEAD_JOB,
            'TB_HEAD_QUESTION'          => TB_HEAD_QUESTION,
            'TB_HEAD_COMPANY'           => TB_HEAD_COMPANY,
            'TB_HEAD_JOBSEEKER'         => TB_HEAD_JOBSEEKER,
            'NOT_FOUND_DATA'            => $no_table_data_found,
            'create_video'              => '<a class="btn btn-sm btn-primary float-right mr-2" 
                                                href="' . tep_href_link(FILENAME_QUIZ . '/' . FILENAME_RECRUITER_LIST_OF_QUIZ, 'action=new&type=video') . '"">
                                                ' . CREATE_VIDEO . '
                                            </a>',
        ));
        $template->pparse('list_video_lib');
    }

} elseif ($action == 'api-my-questions' AND $recruiter_id) {
    get_my_own_questions($recruiter_id);
} elseif ($action == 'test-api' AND $_SERVER['REQUEST_METHOD'] == 'POST' AND $recruiter_id) {
    $api_errors = [];
    $api_data = [];

    $assessmentType = (isset($_GET['type']) ? $_GET['type'] : 'null');
    $typeArray  = ['mcq', 'video', 'essay'];

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $testTitle  = $_POST['test_title'];
        $testTimer  = $_POST['timer'];
        $quesName   = $_POST['question'];
        $quesChoice = $_POST['question_choice'];
        $quesPoint  = $_POST['points'];

        if (strlen($testTitle) <= 0) {
            $api_errors['test_title'] = 'Title is required.';
        }

        if (strlen($testTimer) <= 0) {
            $api_errors['timer'] = 'Timer is required.';
        }

        if (strlen($quesName) <= 0) {
            $api_errors['question'] = 'Question is required.';
        }

        // check first condition is type valid or not
        if (!in_array($assessmentType, $typeArray)) {
            $api_errors['type'] = 'type not matched. it should be mcq, video or essay';
        }

        // if error then show error json otherwise sumbit the data
        if (!empty($api_errors)) {
            $api_data['success'] = false;
            $api_data['errors'] = $api_errors;
        } else {
            // start add test on quizzes table
            $new_test_id = add_test([
                'title'         => $testTitle,
                'recruiter_id'  => $recruiter_id,
                'test_type'     => $assessmentType,
                'timer'         => $testTimer,
                'created_at'    => $currentDate,
                'updated_at'    => $currentDate,
            ]); // end add test here

             //start add question but test id needed
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
            
            } // end add question here
            
            $api_data['success'] = true;
            $api_data['message'] = 'test added successfully';
        }

        echo json_encode($api_data);
    } else {
        echo json_encode($_SERVER['REQUEST_METHOD'].' method is not allowed');
    }
} elseif ($action == 'invitation-list') {

    invitation_send_by_recruiter($recruiter_id);

    // List of Quiz page return
    $template->assign_vars(array(
        'HEADING_TITLE' => INVITE_HEAD,
        'TH_INVITE_EMAIL' => TH_INVITE_EMAIL,
        'TH_INVITE_DATE'      => TH_INVITE_DATE,
        'TH_INVITE_STATUS'      => TH_INVITE_STATUS,
        'TH_INVITE_ASSESSMENT'      => TH_INVITE_ASSESSMENT,
    ));

    $template->pparse('invite_list');
} else {
 
    recuriter_assessment_list($recruiter_id);

    $reportData = get_recruiter_report($recruiter_id);

    if (count($reportData) > 0) {
        $i = 0;
        while ($i < count($reportData)) {
            $template->assign_block_vars('report_box', array(
                'title' => tep_db_output($reportData[$i]['title']),
                'total' => tep_db_output($reportData[$i]['total']),
                'class' => tep_db_output($reportData[$i]['class']),
                'iconClass' => tep_db_output($reportData[$i]['iconClass']),
            ));

            $i++;
        }
    }

    // List of Quiz page return
    $template->assign_vars(array(
        'HEADING_TITLE'             => HEADING_LIST,
        'JOB_TITLE'                 => JOB_TITLE,
        'TB_HEAD_TITLE'             => TB_HEAD_TITLE,
        'TB_HEAD_CANDIDATE'         => TB_HEAD_CANDIDATE,
        'TB_HEAD_DATE'              => TB_HEAD_DATE,
        'TB_HEAD_INVITE'            => TB_HEAD_INVITE,
        'TB_HEAD_ACTION'            => TB_HEAD_ACTION,
      //  'INVITE_CANDIDATE'          => INVITE_CANDIDATE,
        'NO_OF_TESTS'               => NO_OF_TESTS,

    ));
    $template->pparse('list_assessment');
}

function load_custom_modal_button()
{
    $button = '
    <div class="row d-none" id="test_link_menu">
    <div class="col-md-7 mx-auto mt-3">
    <div class="row row-cols-3 row-cols-md-3 g-4">
        <div class="col mp-0">
        <div class="card quiz-box" style="background:#FCD19C;border-color:#FCD19C;border-radius:0;text-align:center;">
            <div class="card-body mp-0 m-text-center">
                <a class="dropdown-item" id="videoModalBtn" data-test-type="video" style="cursor: pointer;">
                <div><i class="bi bi-play-circle" style="font-size: 24px;"></i></div>    
                <h5 class="m-0">Video Test</h5>
                </a>
            </div>
        </div>
        </div>

        <div class="col mp-0">
        <div class="card quiz-box" style="background:#AFF4C6;border-color:#AFF4C6;border-radius:0;text-align:center;">
            <div class="card-body mp-0 m-text-center">
            <a class="dropdown-item" id="mcqModalBtn" data-test-type="mcq" style="cursor: pointer;">
            <div><i class="bi bi-list-check" style="font-size: 24px;"></i></div>    
            <h5 class="m-0">MCQ Test</h5>
            </a>
            </div>
        </div>
        </div>

        <div class="col mp-0">
        <div class="card quiz-box" style="background:#E4CCFF;border-color:#E4CCFF;border-radius:0;text-align:center;">
            <div class="card-body mp-0 m-text-center">
            <a class="dropdown-item" id="essayModalBtn" data-test-type="essay" style="cursor: pointer;">
            <div><i class="bi bi-file-earmark-text" style="font-size: 24px;"></i></div>    
            <h5 class="m-0">Essay Test</h5>
            </a>
            </div>
        </div>
        </div>
        </div>
        </div>
        </div>
    ';

    return $button;
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

function get_my_own_questions($recruiter_id = null)
{
    // if you want to get list test for emp only then pass the of_emp id otherwise admin test library populated
    if ($recruiter_id) {
        $test_query = "SELECT * FROM " . QUIZ_TABLE . " as quiz 
                        WHERE quiz.isActive = '1' 
                        AND quiz.save_as_template = 0 
                        AND quiz.recruiter_id = $recruiter_id 
                        ORDER BY quiz.id DESC";
    } else {
        $test_query = "SELECT * FROM " . QUIZ_TABLE . " as quiz 
                        WHERE quiz.isActive = '1' 
                        AND quiz.save_as_template = 1 
                        AND quiz.recruiter_id IS NULL 
                        ORDER BY quiz.id DESC";
    }

    $test_result = tep_db_query($test_query);

    $output = [];

    if (tep_db_num_rows($test_result) > 0) {
        while ($data = tep_db_fetch_array($test_result)) {
            $short_title = (strlen($data['title']) > 18) ? substr($data['title'],0,18).'...' : $data['title'];
            $short_desc = (strlen($data['description']) > 40) ? substr($data['description'], 0 , 40).'...' : strlen($data['description']);
            $description = ($recruiter_id) ? '' : $short_desc;

            $item = [
                'id'            => $data['id'],
                'title'         => $short_title,
                'description'   => $description,
                'timer'         => $data['timer'],
                'recruiter_id'  => $recruiter_id,
            ];

            array_push($output, $item);
        }
        tep_db_free_result($test_result);
    }

    // set response code - 200 OK
    http_response_code(200);
    
    echo json_encode($output);
}