<?php
include_once("../include_files.php");
include_once(PATH_TO_MAIN_QUIZ_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_RECRUITER_TEST_INVITE_CANDIDATE);
$template->set_filenames(array('invite_candidate' => 'invite_candidate_form.htm'));
include_once("../" . FILENAME_BODY);

// global Properties
$action = (isset($_POST['action']) ? $_POST['action'] : '');
$test_id = (isset($_GET['test_id']) ? tep_db_prepare_input($_GET['test_id']) : '');

// check if recruiter is logged in  or not
if (!check_login("recruiter")) {
    $_SESSION['REDIRECT_URL'] = $_SERVER['REQUEST_URI'];
	$messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
	tep_redirect(tep_href_link(FILENAME_RECRUITER_LOGIN));
}

// recruiter id, full_name and email get
if (check_login('recruiter')) {
    $recruiter_id   = $_SESSION['sess_recruiterid'];
    $user_type = 'recruiter';

    $data = get_recruiter_detail($recruiter_id);

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

// quiz id is present in quiz table or not
if (tep_not_null($test_id)) {
    if (!$row_check_quiz_id = getAnyTableWhereData(QUIZ_TABLE, "id='".tep_db_input($test_id)."' AND recruiter_id=$recruiter_id")) {
        $messageStack->add_session(MESSAGE_QUIZ_ERROR, 'error');
        tep_redirect(FILENAME_RECRUITER_LIST_OF_QUIZ);
    }
    $test_id = $row_check_quiz_id['id'];
    $test_title = $row_check_quiz_id['title'];
}
/**
 * get the email message content
 *
 * @param string $name
 * @param string $recruiter_name
 * @param string $test_title
 * @param string $site_title
 * @return string
 */
function email_content(string $candidate_name, string $recruiter_name, $test_id, string $test_title ,string $site_title)
{
    
    if ($test_id && $test_title) {
        $text = '<!DOCTYPE html><html lang="en"><body>';
        $text .= "<h5>Hi $candidate_name </h5></br></br>";
        $text .= "$recruiter_name from $site_title has invited you to take a assessment. </br></br>";
        $text .= "<a href='".tep_href_link(PATH_TO_QUIZ . $test_id . '/' . encode_forum($test_title) . '.html')."'>Take assessment</a>";
        $text .= "</body></html>";
        return $text;
    }

    return false;
}

// form submit
if ($action == 'send_invitation' && $test_id && $test_title) {
    $error =false;
    $from_email_name=tep_db_output(SITE_TITLE);
    $from_email_address=tep_db_output(EMAIL_FROM);

    $your_email_name=$recruiter_name;
    $your_email_address=$recruiter_email_address;

    $to_name = stripslashes($_POST['candidate_full_name']);
    $to_email_address = stripslashes($_POST['candidate_email']);

    $email_subject = "You've been invited to an assessment";
    $email_text=email_content($to_name, $recruiter_name, $test_id, $test_title, SITE_TITLE);

    if(!tep_not_null($to_name))
    {
     $error =true;
     $messageStack->add(YOUR_FRIEND_NAME_ERROR, 'error');
    }
    if(!tep_not_null($to_email_address))
    {
     $error =true;
     $messageStack->add(YOUR_FRIEND_EMAIL_ADDRESS_ERROR, 'error');
    }

    if(!$error)
    {
        tep_mail($to_name, $to_email_address, $email_subject, $email_text,SITE_OWNER,EMAIL_FROM);
        $messageStack->add_session(SUCCESS_EMAIL_SENT, 'success');
        tep_redirect(FILENAME_RECRUITER_LIST_OF_QUIZ);
    }
}

$template->assign_vars(array(
    'HEADING_TITLE'=>HEADING_TITLE,
    'INFO_TEXT_TO_NAME'          => INFO_TEXT_TO_NAME,
    'INFO_TEXT_TO_NAME1'         => tep_draw_input_field('candidate_full_name',$to_name,'size="40" class="form-control required"',false),
    'INFO_TEXT_TO_EMAIL_ADDRESS' => INFO_TEXT_TO_EMAIL_ADDRESS,
    'INFO_TEXT_TO_EMAIL_ADDRESS1'=> tep_draw_input_field('candidate_email', $to_email_address,'size="40" class="form-control required"',false),
   
    'INFO_TEXT_SUBJECT'=>INFO_TEXT_SUBJECT,
    'INFO_TEXT_SUBJECT1'=>tep_draw_input_field('TR_subject', $email_subject,'size="40" class="form-control required"',false),
    'INFO_TEXT_MESSAGE'=>INFO_TEXT_MESSAGE,
    'INFO_TEXT_MESSAGE1'=>tep_draw_textarea_field('TR_message', 'soft', '50', '8', $TR_message, 'class="form-control-postjob7 required"', '',false),
    // 'INFO_TEXT_SECURITY_CODE' => INFO_TEXT_SECURITY_CODE,
    // 'INFO_TEXT_SECURITY_CODE1'=> tep_draw_input_field('TR_security_code','','class="form-control required"',false),
    // 'INFO_TEXT_TYPE_CODE'     => INFO_TEXT_TYPE_CODE,     
    'form'=>tep_draw_form('send_invitation', PATH_TO_QUIZ.FILENAME_RECRUITER_TEST_INVITE_CANDIDATE,
                        'test_id='.$test_id,
                        'post', 
                        'onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action','send_invitation'),
   
    'button'=>tep_button_submit('btn btn-primary', IMAGE_SEND),
    'INFO_TEXT_JSCRIPT_FILE'  => '<script src="'.$jscript_file.'"></script>',
    'LEFT_HTML'=>LEFT_HTML,
    'RIGHT_HTML'=>RIGHT_HTML,
    'LEFT_HTML_JOBSEEKER'=>LEFT_HTML_JOBSEEKER,
    'update_message'=>$messageStack->output())
);

$template->pparse('invite_candidate');



?>