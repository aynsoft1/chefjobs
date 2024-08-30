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
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_RECRUITER_CHANGE_PASSWORD);
$template->set_filenames(array('password' => 'recruiter_change_password.htm'));
$jscript_file=PATH_TO_LANGUAGE.$language."/jscript/".'recruiter_change_password.js';
include_once(FILENAME_BODY);
if(!check_login("recruiter"))
{
 $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
 tep_redirect(FILENAME_RECRUITER_LOGIN);
}
if(isset($_SESSION['sess_recruiteruserid']))
{
 $messageStack->add_session(ACCESS_DENIED, 'error');
 tep_redirect(FILENAME_RECRUITER_CONTROL_PANEL);
}
$action = (isset($_POST['action']) ? $_POST['action'] : '');
if ($action=='check')
{
	$recruiter_old_password=tep_db_prepare_input($_POST['TR_old_password']);
	$password=tep_db_prepare_input($_POST['TR_new_password']);
	$confirm_password=tep_db_prepare_input($_POST['TR_confirm_password']);
 $error = false;
 if($password!=$confirm_password)
 {
  $error = true;
  $messageStack->add(PASSWORD_MATCH_ERROR,'jobseeker_account');
 }
 if(strlen($password)<5)
 {
  $error = true;
  $messageStack->add(MIN_PASSWORD_ERROR, 'error');
 }
 if(strlen($password)>15)
 {
  $error = true;
  $messageStack->add(MAX_PASSWORD_ERROR, 'error');
 }
 if(!$error)
 {
  $whereClause="recruiter_id='".$_SESSION['sess_recruiterid']."'";
  $fields='recruiter_id,recruiter_password';
  if($row=getAnyTableWhereData(RECRUITER_LOGIN_TABLE,$whereClause,$fields))
  {
   if(!tep_validate_password($recruiter_old_password, $row['recruiter_password']))
   {
    $messageStack->add(SORRY_OLD_PASSWORD_MATCH, 'error');
   }
   else
   {
    $t_password=tep_encrypt_password($password);
    $sql_data_array = array('recruiter_password' => $t_password);
    tep_db_perform(RECRUITER_LOGIN_TABLE, $sql_data_array, 'update', "recruiter_id = '" . $row['recruiter_id'] . "'");
    $messageStack->add_session(SUCCESS_PASSWORD_CHANGE, 'success');
    tep_redirect(FILENAME_RECRUITER_CHANGE_PASSWORD);
   }
  }
 }
}
$template->assign_vars(array(
 'HEADING_TITLE'=>HEADING_TITLE,
 'INFO_TEXT_OLD_PASSWORD'=>INFO_TEXT_OLD_PASSWORD,
 'INFO_TEXT_OLD_PASSWORD1'=>tep_draw_password_field('TR_old_password', '',false, 'class="form-control required"'),
 'INFO_TEXT_NEW_PASSWORD'=>INFO_TEXT_NEW_PASSWORD,
 'INFO_TEXT_NEW_PASSWORD1'=>tep_draw_password_field('TR_new_password', '',false, 'class="form-control required"'),
 'INFO_TEXT_CONFIRM_PASSWORD'=>INFO_TEXT_CONFIRM_PASSWORD,
 'INFO_TEXT_CONFIRM_PASSWORD1'=>tep_draw_password_field('TR_confirm_password', '',false, 'class="form-control required"'),
 'button'=>tep_draw_submit_button_field('',''.IMAGE_CONFIRM.'','class="btn btn-primary"'),
 'form'=>tep_draw_form('change_password', FILENAME_RECRUITER_CHANGE_PASSWORD,'','post', 'onsubmit="return validate_change_password(this)"').tep_draw_hidden_field('action','check'),
 'INFO_TEXT_JSCRIPT_FILE'  =>$jscript_file,

 'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
 'RIGHT_BOX_WIDTH'=>RIGHT_BOX_WIDTH1,
 'LEFT_HTML'=>LEFT_HTML,
 'RIGHT_HTML'=>RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('password');
?>