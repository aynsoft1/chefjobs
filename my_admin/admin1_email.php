<?
/*
***********************************************************
***********************************************************
**********# Name          : Kamal Kumar Sahoo   #**********
**********# Company       : Aynsoft             #**********
**********# Date Created  : 23/07/05            #**********
**********# Date Modified : 23/07/05            #**********
**********# Copyright (c) www.aynsoft.com 2005  #**********
***********************************************************
***********************************************************
*/
include_once("../include_files.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_EMAIL);
$template->set_filenames(array('email' => 'admin1_email.htm','preview_email'=>'admin1_preview_email.htm'));
include_once(FILENAME_ADMIN_BODY);

$action = (isset($_GET['action']) ? $_GET['action'] : '');

if(isset($_POST['send_mail_x']))
{
 $action='send_mail';
}
if($_POST['action']=='back')
{
 $action='back';
}
if(tep_not_null($action))
{
 switch($action)
	{
  case 'preview':
  case 'back':
  case 'send_mail':
   $TREF_to=tep_db_prepare_input($_POST['TREF_to']);
   $TREF_from=tep_db_prepare_input($_POST['TREF_from']);
   $TR_subject=tep_db_prepare_input($_POST['TR_subject']);
   $TR_message=stripslashes($_POST['TR_message']);
   break;
 }
}

$hidden_fields="";
if($action=="send_mail")
{
 //Let's build a message object using the email class
 $mimemessage = new email(array('X-Mailer: travel.com'));
 // add the message to the object
	$text = strip_tags($TR_message);
 if (EMAIL_USE_HTML == 'true')
 {
  $mimemessage->add_html($TR_message);
 }
 else
 {
  $mimemessage->add_text($text);
 }
 $mimemessage->build_message();
 $mimemessage->send(' ', $TREF_to, '', $TREF_from, $TR_subject);
 $messageStack->add_session(sprintf(NOTICE_EMAIL_SENT_TO,$TREF_to), 'success');
 tep_redirect(FILENAME_ADMIN1_EMAIL);
}
else if($action=="preview")
{
 $hidden_fields.=tep_draw_hidden_field('action', '');
 $hidden_fields.=tep_draw_hidden_field('TREF_to', $TREF_to);
 $hidden_fields.=tep_draw_hidden_field('TREF_from', $TREF_from);
 $hidden_fields.=tep_draw_hidden_field('TR_subject', $TR_subject);
 $hidden_fields.=tep_draw_hidden_field('TR_message', $TR_message);
 $template->assign_vars(array(
  'HEADING_TITLE'=>HEADING_TITLE,
  'TEXT_TO'=>TEXT_TO,
  'TEXT_TO1'=>tep_db_output($TREF_to),
  'TEXT_FROM'=>TEXT_FROM,
  'TEXT_FROM1'=>tep_db_output($TREF_from),
  'TEXT_SUBJECT'=>TEXT_SUBJECT,
  'TEXT_SUBJECT1'=>tep_db_output($TR_subject),
  'TEXT_MESSAGE'=>TEXT_MESSAGE,
  'TEXT_MESSAGE1'=>stripslashes($TR_message),
  'buttons'=>'<a href="#" onclick="javascript: submitform();">'.tep_button('Back','class="btn btn-secondary"').'</a>&nbsp;&nbsp;'.tep_draw_submit_button_field('','Send Mail','class="btn btn-primary"'),//tep_image_submit(PATH_TO_BUTTON.'button_send_mail.gif', IMAGE_SEND_MAIL, 'name="send_mail"'),
  'form'=>tep_draw_form('preview_mail', PATH_TO_ADMIN.FILENAME_ADMIN1_EMAIL, '', 'post', 'onsubmit="return ValidateForm(this)"'),
  'hidden_fields'=>$hidden_fields,
  'RIGHT_BOX_WIDTH'=>RIGHT_BOX_WIDTH,
  'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
  'update_message'=>$messageStack->output()));
}
else
{
 $TREF_from=EMAIL_FROM;
 $template->assign_vars(array(
  'HEADING_TITLE'=>HEADING_TITLE,
  'TEXT_TO'=>TEXT_TO,
  'TEXT_TO1'=>tep_draw_input_field('TREF_to', $TREF_to, 'size="35" class="form-control form-control-sm"', true ),
  'TEXT_FROM'=>TEXT_FROM,
  'TEXT_FROM1'=>tep_draw_input_field('TREF_from', $TREF_from, 'size="35" class="form-control form-control-sm"', true ),
  'TEXT_SUBJECT'=>TEXT_SUBJECT,
  'TEXT_SUBJECT1'=>tep_draw_input_field('TR_subject', $TR_subject, 'size="35" class="form-control form-control-sm"', true ),
  'TEXT_MESSAGE'=>TEXT_MESSAGE,
  'TEXT_MESSAGE1'=>tep_draw_textarea_field('TR_message', 'soft', '80%', '10', $TR_message, 'class="form-control form-control-sm"', true, true),
  'buttons'=>tep_draw_submit_button_field('','Preview','class="btn btn-primary"'),//tep_image_submit(PATH_TO_BUTTON.'button_preview.gif', IMAGE_PREVIEW_MAIL),
  'form'=>tep_draw_form('newmember', PATH_TO_ADMIN.FILENAME_ADMIN1_EMAIL, 'action=preview', 'post', 'onsubmit="return ValidateForm(this)"'),
  'RIGHT_BOX_WIDTH'=>RIGHT_BOX_WIDTH,
  'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
  'update_message'=>$messageStack->output()));
}
if($action=="preview")
{
 $template->pparse('preview_email');
}
else
{
 $template->pparse('email');
}
?>