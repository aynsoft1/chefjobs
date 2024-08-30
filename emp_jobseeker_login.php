<?php

include_once("include_files.php");
include_once("general_functions/password_funcs.php");
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_EMP_JOBSEEKER_LOGIN);
include_once(FILENAME_BODY);
$jscript_file=PATH_TO_LANGUAGE.$language."/jscript/".'login.js';
// $template->set_filenames(array('login' => 'login.htm'));

$action = (isset($_POST['action']) ? $_POST['action'] : '');
$u=(isset($_GET['u'])?$_GET['u']:0);

$authPassed = '';

// get input fields value
$email_address=tep_db_prepare_input($_POST['TREF_email_address1']);
$input_password=$_POST['TR_password1'];

$checked1=false;
$checked2=false;
$encoded_login1=$_COOKIE["autologin1"];
$encoded_login2=$_COOKIE["autologin2"];

if(tep_not_null($encoded_login1)) {
    $checked1=true;
    $explode_array=explode("|",decode_string($encoded_login1));
    $TREF_email_address1=$explode_array[0];
    $TR_password1=$explode_array[1];
} else {
    $TREF_email_address1="";
    $TR_password1='';
}

 if(tep_not_null($encoded_login2)) {
    $checked2=true;
    $explode_array=explode("|",decode_string($encoded_login2));
    $TREF_email_address2=$explode_array[0];
    $TR_password2=$explode_array[1];
 } else {
    $TREF_email_address2="";
    $TR_password2='';
 }
// Jobseeker auto login ends

function job_seeker_login($email_address = null, $password = null)
{
    global $messageStack, $authPassed;
    $jobseeker_login_table = "SELECT rl.recruiter_id as id, 
                                rl.recruiter_email_address as email, 
                                rl.recruiter_password as password,
                                rl.recruiter_status as status,
                                CONCAT(r.recruiter_first_name,' ',r.recruiter_last_name) as name, 
                                CONCAT('recruiter') AS 'login_type' 
                                FROM recruiter_login as rl
                                JOIN recruiter as r ON rl.recruiter_id = r.recruiter_id
                                WHERE rl.recruiter_status='Yes'";

    $recruiter_login_table = "SELECT jl.jobseeker_id as id, 
                                jl.jobseeker_email_address as email, 
                                jl.jobseeker_password as password, 
                                jl.jobseeker_status as status,
                                CONCAT(j.jobseeker_first_name,' ',j.jobseeker_last_name) as name, 
                                CONCAT('jobseeker') AS 'login_type' 
                                FROM jobseeker_login as jl
                                JOIN jobseeker as j ON jl.jobseeker_id = j.jobseeker_id
                                WHERE jl.jobseeker_status='Yes' ";  
    
    $where_clause = "login_table.email='".tep_db_input($email_address)."' and login_table.status='Yes'";
    
    $union_both_table = "SELECT * FROM($jobseeker_login_table UNION $recruiter_login_table) AS login_table WHERE $where_clause";
    
    $query_result = tep_db_query($union_both_table);

    if(tep_db_num_rows($query_result) > 0)
	{
        $row = tep_db_fetch_array($query_result);     
        if(!tep_validate_password($password, $row['password']))
        {
            if ($row['login_type'] == 'jobseeker') {
                $messageStack->add_session(SORRY_LOGIN_MATCH, 'error');
                tep_redirect(FILENAME_JOBSEEKER_LOGIN);
            } else {
                $messageStack->add_session(SORRY_LOGIN_MATCH, 'error');
                tep_redirect(FILENAME_RECRUITER_LOGIN);
            }
        }
        else
        {
                $authPassed = true;
                $redirect_url=(tep_not_null($_SESSION['REDIRECT_URL'])?HOST_NAME_MAIN.$_SESSION['REDIRECT_URL']:'');
                $ip_address=$_SERVER['REMOTE_ADDR'];
                $last_ip_address=tep_db_prepare_input($row['ip_address']);
                $number_of_logon=$row['number_of_logon']+1;
                $sql_data_array = array('last_login_time' => 'now()',
                                        'ip_address' => $ip_address,
                                        'last_ip_address' => $last_ip_address,
                                        'number_of_logon' => $number_of_logon);
                perform_login_setup($row, $sql_data_array, $email_address, $redirect_url);
            }
    } else {
        $messageStack->add_session(SORRY_LOGIN_MATCH, 'error');
        tep_redirect(FILENAME_JOBSEEKER_LOGIN);
	}
}


function perform_login_setup($row, $sql_data_array, $email_address, $redirect_url)
{
    $language = $_SESSION['language'];
    $language_id = $_SESSION['languages_id'];

    if ($row['login_type'] == 'jobseeker') {
        tep_db_perform(JOBSEEKER_LOGIN_TABLE, $sql_data_array, 'update', "jobseeker_id = '" . $row['id'] . "'");
        
        // Clear session variables and destroy the session
        session_unset();  // This clears all session variables
        session_destroy(); // This destroys the session entirely

        // Clear cookies
        @SetCookie("autologin1", "", 0);
        @SetCookie("autologin2", "", 0);

        if (isset($_POST['auto_login1'])) {
            // Set login to expire in 1 day
            srand((double) microtime() * 1000000);
            $encoded_login = encode_string($email_address . "|");
            @SetCookie("autologin1", $encoded_login, time() + (24 * 3600 * 365));
        }

        // Reinitialize session and set new session variables
        session_start();
        $_SESSION['sess_jobseekername'] = tep_db_output($row['name']);
        $_SESSION['sess_jobseekerlogin'] = "y";
        $_SESSION['sess_jobseekerid'] = $row["id"];
        $_SESSION['language'] = $language;
        $_SESSION['languages_id'] = $language_id;

        // Redirect
        if (tep_not_null($redirect_url)) {
            tep_redirect($redirect_url);
        } else {
            tep_redirect(FILENAME_JOBSEEKER_CONTROL_PANEL);
        }
    }

    if ($row['login_type'] == 'recruiter') {
        tep_db_perform(RECRUITER_LOGIN_TABLE, $sql_data_array, 'update', "recruiter_id = '" . $row['recruiter_id'] . "'");
        
        // Clear session variables and destroy the session
        session_unset();  // This clears all session variables
        session_destroy(); // This destroys the session entirely

        // Clear cookies
        @SetCookie("autologin1", "", 0);
        @SetCookie("autologin2", "", 0);

        if (isset($_POST['auto_login1'])) {
            // Set login to expire in 1 day
            srand((double) microtime() * 1000000);
            $encoded_login = encode_string($email_address . "|");
            @SetCookie("autologin2", $encoded_login, time() + (24 * 3600 * 365));
        }

        // Reinitialize session and set new session variables
        session_start();
        $_SESSION['sess_recruiterlogin'] = "y";
        $_SESSION['sess_recruiterid'] = $row["recruiter_id"];
        $_SESSION['language'] = $language;
        $_SESSION['languages_id'] = $language_id;

        // Redirect
        if (tep_not_null($redirect_url)) {
            tep_redirect($redirect_url);
        } else {
            tep_redirect(FILENAME_RECRUITER_CONTROL_PANEL);
        }
    }
}


if ($action == 'check') {
    job_seeker_login($email_address, $input_password);
}
?>