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
include_once("../include_files.php");
include_once("../general_functions/password_funcs.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_ADMIN_FORGOT_PASSWORD);
$template->set_filenames(array('forgot_password' => 'forgot_password.htm'));
include_once(FILENAME_ADMIN_BODY);

if($_GET['action']=='send')
{
 $email_address = tep_db_prepare_input($_POST['TREF_email_address']);
 // Check if email exists
 if($row=getAnyTableWhereData(ADMIN_TABLE," admin_email_address='".tep_db_input($email_address) . "'",'admin_id,admin_name, admin_email_address'))
 {
  $reset_code=get_user_opt($email_address,'admin');
  $link=tep_href_link(FILENAME_RESET_PASSWORD,'reset_code='.$reset_code.'&web=1&action=reset_password&email_address='.$email_address);
  $paramete=array('user_name'=>$row['admin_name'],'email_address'=>$email_address,'opt'=>$reset_code,'reset_link'=>$link);
  send_reset_opt($paramete);
  tep_redirect(tep_href_link(FILENAME_RESET_PASSWORD,'web=1&email_address='.urlencode($email_address)));
 }
 else
 {
  $messageStack->add(sprintf(EMAIL_NOT_FOUND,tep_db_output($email_address)), 'error');
 }
}/*
 elseif($_GET['action']=='reset')
{
 $email_address = tep_db_prepare_input($_GET['TREF_email_address']);
 // Check if email exists
 if($row=getAnyTableWhereData(ADMIN_TABLE," admin_email_address='".tep_db_input($email_address) . "'",'admin_id,admin_name, admin_email_address'))
 {
  $makePassword = randomize();
  tep_mail($row['admin_name'] , $row['admin_email_address'], ADMIN_EMAIL_SUBJECT, nl2br(sprintf(ADMIN_EMAIL_TEXT, $row['admin_name'], '<a href="'.HOST_NAME . PATH_TO_ADMIN.'">'.HOST_NAME . PATH_TO_ADMIN.'</a>', $row['admin_name'], $makePassword, SITE_OWNER)), SITE_OWNER, ADMIN_EMAIL);
  tep_db_query("update " . ADMIN_TABLE . " set admin_password = '" . tep_encrypt_password($makePassword) . "' where admin_id = '" . $row['admin_id'] . "'");
  //echo $makePassword;//die();
  $messageStack->add_session(STRING_PASSWORD_SEND, 'success');
  tep_redirect(FILENAME_INDEX);
 }
 else
 {
  $messageStack->add(sprintf(EMAIL_NOT_FOUND,tep_db_output($email_address)), 'error');
 }
}*/
$template->assign_vars(array(
 'HEADING_TITLE'=>HEADING_TITLE,
 'HEADING_CONTENT'=>HEADING_CONTENT,
 'TEXT_INFO_EMAIL_ADDRESS'=>TEXT_INFO_EMAIL_ADDRESS,
 'logo_image'=>'<img src="'.HOST_NAME.PATH_TO_IMG.DEFAULT_SITE_LOGO.'" class="img-fluid">',
 'TEXT_INFO_EMAIL_ADDRESS1'=>tep_draw_input_field('TREF_email_address',$_POST['TREF_email_address'],'class="form-control form-control-sm" required autocomplete="email" placeholder="enter your email"'),
 'new_button'=>'<button type="button" class="btn btn-primary" onclick="location.href=\''.tep_href_link(PATH_TO_ADMIN).'\'">Cancel</button>
                <button type="submit" class="btn btn-primary">Submit</button>',
 'form'=>tep_draw_form('forgot_password',PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_FORGOT_PASSWORD,'action=send','post','onsubmit="return ValidateForm(this)"'),
 'update_message'=>$messageStack->output()));
$template->pparse('forgot_password');
?>