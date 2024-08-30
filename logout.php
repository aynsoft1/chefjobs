<?
/*
***********************************************************
***********************************************************
**********# Name          : Kamal Kumar Sahoo   #**********
**********# Company       : Aynsoft             #**********
**********# Date Created  : 11/02/04            #**********
**********# Date Modified : 11/02/04            #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
***********************************************************
***********************************************************
*/
include_once("include_files.php");
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_LOGOUT);

//// Forum logout starts /////
if(check_login('jobseeker'))
{
 $row=getAnyTableWhereData(JOBSEEKER_LOGIN_TABLE,"jobseeker_id='".$_SESSION['sess_jobseekerid']."'",'jobseeker_email_address');
 $email_address=$row['jobseeker_email_address'];
}
else if(check_login('recruiter'))
{
 if($row=getAnyTableWhereData(RECRUITER_USERS_TABLE,"id='".$_SESSION['sess_recruiteruserid']."'",'email_address'))
 {
  $email_address=$row['email_address'];
 }
 else if($row=getAnyTableWhereData(RECRUITER_LOGIN_TABLE,"recruiter_id='".$_SESSION['sess_recruiterid']."'",'recruiter_email_address'))
 {
  $email_address=$row['recruiter_email_address'];
 }
}
else if(check_login('admin'))
{
 $row=getAnyTableWhereData(ADMIN_TABLE,"admin_id='".$_SESSION['sess_adminid']."'",'admin_email_address');
 $email_address=$row['admin_email_address'];
}

/*///////// Logout to forum
include_once(PATH_TO_MAIN_PHYSICAL."phpbb_login.php");
// Then login the user to the forum
$phpbb = new PHPBB_Login();

$phpbb->logout(session_id(),tep_db_input($email_address));
//////////
///////// forum ends ////
*/
$session_id = session_id();
//tep_db_query("delete from " . WHOS_ONLINE_TABLE . " where session_id = '" . $session_id . "'");
@session_unset();
// Finally, destroy the session.
@session_destroy();


$messageStack->add_session(MESSAGE_LOGOUT, 'success');
tep_redirect(tep_href_link('index.php'));
?>