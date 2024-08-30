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
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_TELL_FRIEND);
$template->set_filenames(array('tell_friend' => 'tell_friend.htm'));
include_once(FILENAME_BODY);
$jscript_file=PATH_TO_LANGUAGE.$language."/jscript/".'tell_friend.js';

$action = (isset($_POST['action']) ? $_POST['action'] : '');
// search
if($action=="send")
{
 $from_email_name=stripslashes($_POST['TR_your_full_name']);
 $from_email_address=stripslashes($_POST['TREF_your_email_address']);
 $to_name=stripslashes($_POST['TR_your_friend_full_name']);
 $to_email_address=stripslashes($_POST['TREF_your_friend_email_address']);
 $email_text='<div style="font: normal 12px/17px Verdana, Arial, Helvetica, sans-serif;">'.INFO_TEXT_HI.' <b>'.$to_name.',</b>';
 $email_text.='<br>&nbsp;'.INFO_TEXT_YOUR_FRIEND.' <b>'.$from_email_name.'</b> '.INFO_TEXT_HAS_SENT;
 $email_text.='<br>&nbsp; '.INFO_TEXT_EMAIL_ADDRESS.' <b>'.$from_email_address.' </b>.';
 $email_text.='<br>&nbsp; '.INFO_TEXT_MESSAGE.'<hr>';
 $email_text.=nl2br(stripslashes($_POST['TR_message']));
 $email_text.='</div>';

 $email_subject=stripslashes($_POST['TR_subject']);
 //$email_text=stripslashes($_POST['TR_message']);
 if(($_SESSION['security_code'] == tep_db_prepare_input($_POST['TR_security_code'])) && (!empty($_SESSION['security_code'])) && (strtolower($_SERVER['HTTP_REFERER'])==HOST_NAME.FILENAME_TELL_FRIEND)) 
 {
  tep_mail($to_name, $to_email_address, $email_subject, $email_text, SITE_TITLE, EMAIL_FROM);
  $messageStack->add_session(SUCCESS_EMAIL_SENT, 'success');
  tep_redirect(FILENAME_TELL_FRIEND);
 }
 else
 {
  $messageStack->add(ERROR_SECURITY_CODE, 'error');
 }
}
$template->assign_vars(array(
 'HEADING_TITLE'=>HEADING_TITLE,
 'INFO_TEXT_FROM_NAME'=>INFO_TEXT_FROM_NAME,
 'INFO_TEXT_FROM_NAME1'=>tep_draw_input_field('TR_your_full_name', $TR_your_full_name,'size="40"',true),
 'INFO_TEXT_FROM_EMAIL_ADDRESS'=>INFO_TEXT_FROM_EMAIL_ADDRESS,
 'INFO_TEXT_FROM_EMAIL_ADDRESS1'=>tep_draw_input_field('TREF_your_email_address', $TREF_your_email_address,'size="40"',true),
 'INFO_TEXT_TO_NAME'=>INFO_TEXT_TO_NAME,
 'INFO_TEXT_TO_NAME1'=>tep_draw_input_field('TR_your_friend_full_name', $TR_your_friend_full_name,'size="40"',true),
 'INFO_TEXT_TO_EMAIL_ADDRESS'=>INFO_TEXT_TO_EMAIL_ADDRESS,
 'INFO_TEXT_TO_EMAIL_ADDRESS1'=>tep_draw_input_field('TREF_your_friend_email_address', $TREF_your_friend_email_address,'size="40"',true),
 'INFO_TEXT_SUBJECT'=>INFO_TEXT_SUBJECT,
 'INFO_TEXT_SUBJECT1'=>tep_draw_input_field('TR_subject', $TR_subject,'size="40"',true),
 'INFO_TEXT_MESSAGE'=>INFO_TEXT_MESSAGE,
 'INFO_TEXT_MESSAGE1'=>tep_draw_textarea_field('TR_message', 'soft', '50', '12', $TR_message, '', '',true),
 'INFO_TEXT_SECURITY_CODE' => INFO_TEXT_SECURITY_CODE,
 'INFO_TEXT_SECURITY_CODE1'=> tep_draw_input_field('TR_security_code','','',true),
 'INFO_TEXT_TYPE_CODE'     => INFO_TEXT_TYPE_CODE,     
 'form'=>tep_draw_form('send', FILENAME_TELL_FRIEND,'','post', 'onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action','send'),
 'button'=>tep_image_submit(PATH_TO_BUTTON.'button_send.gif', IMAGE_SEND),
 'INFO_TEXT_TYPE_CODE'=>INFO_TEXT_TYPE_CODE,
 'INFO_TEXT_JSCRIPT_FILE'  => '<script src="'.$jscript_file.'"></script>',
 'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
 'RIGHT_BOX_WIDTH'=>RIGHT_BOX_WIDTH1,
 'LEFT_HTML'=>LEFT_HTML,
 'RIGHT_HTML'=>RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('tell_friend');
?>