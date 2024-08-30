<?php
include_once("../include_files.php");
include_once(PATH_TO_MAIN_QUIZ_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_TEST_TAKER_INVITE_LINK);
// $template->set_filenames(array('invite_candidate' => 'invite_candidate_form.htm'));
include_once("../" . FILENAME_BODY);

if ($_SESSION['language'] == "spanish") {
    $language = 'es';
} else {
    $language = 'en';
}

$action = (isset($_GET['action']) ? $_GET['action'] : '');
$uuid = (isset($_GET['uuid']) ? $_GET['uuid'] : '');

// jobseeker login required get jobseeker email also
if (!check_login('jobseeker') && tep_not_null($action)) {
    $_SESSION['REDIRECT_URL'] = $_SERVER['REQUEST_URI'];
    $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
    tep_redirect(tep_href_link(FILENAME_JOBSEEKER_LOGIN));
} else {
    $jobseeker_id   = $_SESSION['sess_jobseekerid'];
    $jobseeker_type = 'jobseeker';
}

// find jobseeker details
function get_jobseeker_details($id)
{
    $seeker = false;

    $table_name = JOBSEEKER_LOGIN_TABLE;

    $seeker_query = "SELECT j.jobseeker_id, j.jobseeker_email_address FROM $table_name as j WHERE j.jobseeker_id = $id";

    $result = tep_db_query($seeker_query);

    if (tep_db_num_rows($result) > 0) {
        $seeker = tep_db_fetch_array($result);
    }

    return $seeker;

}

// find invite mail detail
function get_invite_mail_data($uuid)
{
    $data = false;

    $table_name = ASSESSMENT_INVITEMAIL_TABLE;

    $data_query = "SELECT * FROM $table_name WHERE uuid = '$uuid'";

    $result = tep_db_query($data_query);

    if (tep_db_num_rows($result) > 0) {
        $data = tep_db_fetch_array($result);
    }

    return $data;
}


// assign assessment to the jobseeker
function assign_assessment_to_the_jobseeker_and_update_accepted_column_to_the_invitemail($assessmentID, $inviteMailID, $jobseekerID)
{
    $currentDate = date("Y-m-d H:i:s"); 

    // first check the assessment_invitemail accepted column is 0 or 1
    // if value is 1 for inviteMailID then return false with message already invited otherwise store data

    $table_name = ASSESSMENT_INVITEMAIL_TABLE;
    $query_data = "SELECT a.id, a.uuid, a.email_to, a.assessment_id, a.accepted FROM $table_name as a WHERE id = $inviteMailID";
    $result     = tep_db_query($query_data);
    $data = tep_db_fetch_array($result);

    if ($data['accepted'] != 1) {
        $storeData = [
            'assessment_id' => $assessmentID,
            'jobseeker_id'  => $jobseekerID,
            'created_at'    => $currentDate,
            'updated_at'    => $currentDate,
        ];
        // store data to assessment_jobseeker table
        tep_db_perform(ASSESSMENT_JOBSEEKER_TABLE, $storeData);

        // updated accespted column
        $updateCol = [
            'accepted'      => 1,
            'updated_at'    => $currentDate,
        ];
        
        tep_db_perform(ASSESSMENT_INVITEMAIL_TABLE, $updateCol, 'update', "id='" . $inviteMailID . "'");

        return true;
    }

    return false;
}

/*
    |--------------------------------------------------------------------------
    | Main Logic
    |--------------------------------------------------------------------------
    |
    | if invitation link is clicked. Then check the condition
    | First condition is action should be takeinvitation
    | second get the invited mail data
    | And match the invited mail with jobseeker login mail
    | if it is correct then assign assessment to the jobseeker otherwise false or 404
    |
*/

if ($action == "takeinvitation" AND $uuid) {
    $mail_tbl = get_invite_mail_data($uuid);
    $jobseeker_tbl = get_jobseeker_details($jobseeker_id);

    // check if uuid match with tbl uuid
    // now if jobseeker mail and invited mail matched then store a value to db

    if ($mail_tbl AND ($mail_tbl['uuid'] == $uuid) AND ($mail_tbl['email_to'] == $jobseeker_tbl['jobseeker_email_address'])) {
        $response = assign_assessment_to_the_jobseeker_and_update_accepted_column_to_the_invitemail($mail_tbl['assessment_id'], $mail_tbl['id'], $jobseeker_id);
            
        if ($response) {
            $messageStack->add_session('New assessment has been assigned to you', 'success');
            tep_redirect(tep_href_link(PATH_TO_QUIZ.FILENAME_JOBSEEKER_TEST_REPORT));
        } else {
            $messageStack->add_session('The link is already used', 'error');
            tep_redirect(tep_href_link(PATH_TO_QUIZ.FILENAME_JOBSEEKER_TEST_REPORT));
        }
    } else {
        $messageStack->add_session('Unauthorized Link', 'error');
        tep_redirect(tep_href_link(PATH_TO_QUIZ.FILENAME_JOBSEEKER_TEST_REPORT));
    }
} else {
    $messageStack->add_session('Something went wrong', 'error');
    tep_redirect(tep_href_link(PATH_TO_QUIZ.FILENAME_JOBSEEKER_TEST_REPORT));
}
?>