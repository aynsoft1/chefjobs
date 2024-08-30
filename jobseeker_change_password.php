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
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_JOBSEEKER_CHANGE_PASSWORD);
$template->set_filenames(array('password' => 'jobseeker_change_password.htm'));
include_once(FILENAME_BODY);
$jscript_file=PATH_TO_LANGUAGE.$language."/jscript/".'jobseeker_change_password.js';
if(!check_login("jobseeker"))
{
 $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
 tep_redirect(FILENAME_JOBSEEKER_LOGIN);
}
$action = (isset($_POST['action']) ? $_POST['action'] : '');

if ($action=='check')
{
	$jobseeker_old_password=tep_db_prepare_input($_POST['TR_old_password']);
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
  $whereClause="jobseeker_id='".$_SESSION['sess_jobseekerid']."'";
  $fields='jobseeker_id,jobseeker_password';
  if($row=getAnyTableWhereData(JOBSEEKER_LOGIN_TABLE,$whereClause,$fields))
  {
   if(!tep_validate_password($jobseeker_old_password, $row['jobseeker_password']))
   {
    $messageStack->add(SORRY_OLD_PASSWORD_MATCH, 'error');
   }
   else
   {
    $sql_data_array = array('jobseeker_password' => tep_encrypt_password($password));
    tep_db_perform(JOBSEEKER_LOGIN_TABLE, $sql_data_array, 'update', "jobseeker_id = '" . $row['jobseeker_id'] . "'");

	//code to logout after change password
    //$messageStack->add_session(SUCCESS_PASSWORD_CHANGE, 'success');
	@session_unset();
                // @session_destroy();

                $_SESSION['password_changed_success'] = SUCCESS_PASSWORD_CHANGE;
                $messageStack->add(SUCCESS_PASSWORD_CHANGE, 'success');
    tep_redirect(FILENAME_JOBSEEKER_LOGIN);
   }
  }
 }
}
$template->assign_vars(array(
 'HEADING_TITLE'=>HEADING_TITLE,
 'INFO_TEXT_OLD_PASSWORD'=>INFO_TEXT_OLD_PASSWORD,
 'INFO_TEXT_OLD_PASSWORD1'=>tep_draw_password_field('TR_old_password', '',false,'class="form-control required"'),
 'INFO_TEXT_NEW_PASSWORD'=>INFO_TEXT_NEW_PASSWORD,
 'INFO_TEXT_NEW_PASSWORD1'=>tep_draw_password_field('TR_new_password', '',false,'class="form-control required"'),
 'INFO_TEXT_CONFIRM_PASSWORD'=>INFO_TEXT_CONFIRM_PASSWORD,
 'INFO_TEXT_CONFIRM_PASSWORD1'=>tep_draw_password_field('TR_confirm_password', '',false,'class="form-control required"'),
 'button'=>'<button class="btn btn-primary" type="submit">Confirm</button>',//tep_image_submit(PATH_TO_BUTTON.'button_confirm.gif', IMAGE_CONFIRM),
 'form'=>tep_draw_form('change_password', FILENAME_JOBSEEKER_CHANGE_PASSWORD,'','post', 'onsubmit="return validate_change_password(this)"').tep_draw_hidden_field('action','check'),
 'INFO_TEXT_JSCRIPT_FILE'  => $jscript_file,
 'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
 'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
 'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
 'RIGHT_BOX_WIDTH'=>RIGHT_BOX_WIDTH1,
 'LEFT_HTML_JOBSEEKER'=>LEFT_HTML_JOBSEEKER,
  'LEFT_HTML'=>LEFT_HTML,
	'LEFT_HTML'=>LEFT_HTML_JOBSEEKER,
 'RIGHT_HTML'=>RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('password');
?>