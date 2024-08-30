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
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_FEEDBACK);
$template->set_filenames(array('feedback' => 'feedback.htm'));
include_once(FILENAME_BODY);
$action = (isset($_POST['action']) ? $_POST['action'] : '');
// search
if($action=="send")
{
 $from_email_name=stripslashes($_POST['TR_full_name']);
 $from_email_address=stripslashes($_POST['TREF_email_address']);
 $to_name=SITE_OWNER;
 $to_email_address=ADMIN_EMAIL;
 $email_subject=stripslashes($_POST['TR_subject']);
 $email_text=stripslashes($_POST['TR_message']);
 tep_mail($to_name, $to_email_address, $email_subject, $email_text, $from_email_name, $from_email_address);
 $messageStack->add_session(SUCCESS_EMAIL_SENT, 'success');
 tep_redirect(FILENAME_FEEDBACK);
}
$template->assign_vars(array(
 'HEADING_TITLE'=>HEADING_TITLE,
 'INFO_TEXT_FULL_NAME'=>INFO_TEXT_FULL_NAME,
 'INFO_TEXT_FULL_NAME1'=>tep_draw_input_field('TR_full_name', '', 'size="45"', true ),
 'INFO_TEXT_EMAIL_ADDRESS'=>INFO_TEXT_EMAIL_ADDRESS,
 'INFO_TEXT_EMAIL_ADDRESS1'=>tep_draw_input_field('TREF_email_address', '', 'size="45"', true ),
 'INFO_TEXT_SUBJECT'=>INFO_TEXT_SUBJECT,
 'INFO_TEXT_SUBJECT1'=>tep_draw_input_field('TR_subject', '', 'size="45"', true ),
 'INFO_TEXT_MESSAGE'=>INFO_TEXT_MESSAGE,
 'INFO_TEXT_MESSAGE1'=>tep_draw_textarea_field('TR_message', 'soft', '70%', '10', '', '', true, true),
 'form'=>tep_draw_form('send', FILENAME_FEEDBACK,'','post', 'onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action','send'),
 'button'=>tep_image_submit(PATH_TO_BUTTON.'button_send.gif', IMAGE_SEND),
 'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
 'RIGHT_BOX_WIDTH'=>RIGHT_BOX_WIDTH1,
 'LEFT_HTML'=>LEFT_HTML,
 'RIGHT_HTML'=>RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('feedback');
?>