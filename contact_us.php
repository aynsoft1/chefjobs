<?php
include_once("include_files.php");
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_CONTACT_US);
$template->set_filenames(array('contact_us' => 'contact_us.htm'));
$jscript_file=tep_href_link(PATH_TO_LANGUAGE.$language."/jscript/".'contact_us.js');
include_once(FILENAME_BODY);
$action = (isset($_POST['action']) ? $_POST['action'] : '');
// search
/////////////////////////////////////////////////////
 include_once "class/reCaptcha.php";
 $g_captcha =true;
 $reCaptcha=new reCaptcha();

////////////////////////////////////////////////////////
if($action=="send")
{
 $from_email_name    = tep_db_prepare_input($_POST['TR_full_name']);
 $from_email_address = tep_db_prepare_input($_POST['TREF_email_address']);
 $to_name            = SITE_OWNER;
 $to_email_address   = ADMIN_EMAIL;
 $email_subject      = tep_db_prepare_input($_POST['TR_subject']);
 $email_text         = tep_db_prepare_input($_POST['TR_message']);
// $security_code1     = tep_db_prepare_input($_POST['TR_security_code']);

 $error =false;
 if(!tep_not_null($from_email_name))
 {
  $error =true;
  $messageStack->add(YOUR_NAME_ERROR, 'error');
 }
 if(!tep_not_null($from_email_address))
 {
  $error =true;
  $messageStack->add(YOUR_EMAIL_ADDRESS_ERROR, 'error');
 }
 if(!tep_not_null($email_subject))
 {
  $error =true;
  $messageStack->add(EMAIL_SUBJECT_ERROR, 'error');
 }
 if(!tep_not_null($email_text))
 {
  $error =true;
  $messageStack->add(EMAIL_MESSAGE_ERROR, 'error');
 }
/* if($_SESSION['security_code'] != $security_code1 || $security_code1==''  ||  (strtolower($_SERVER['HTTP_REFERER'])!=strtolower(tep_href_link(FILENAME_CONTACT_US))))
 {
  $error =true;
  $messageStack->add(ERROR_SECURITY_CODE, 'error');
 }
*/
 
 if((strtolower($_SERVER['HTTP_REFERER'])!=strtolower(getPermalink(FILENAME_CONTACT_US))))
 {
  $error =true;
  $messageStack->add(ERROR_SECURITY_CODE, 'error');
 }
/////////////////////////////////////////////
if(MODULE_G_CAPTCHA_PLUGIN=='enable')
if(!$reCaptcha->reCaptchaVerify())
      {
       $error = true;
       $messageStack->add(CAPTCHA_ERROR,'jobseeker_account');
	  }


////////////////////////////////////////////////

 if(!$error)
 {
		//unset($_SESSION['security_code']);
  tep_mail($to_name, $to_email_address, $email_subject, nl2br($email_text), $from_email_name, $from_email_address);
  ////
	 $sql_data_array=array( 'user_name'=>$from_email_name,
                         'user_email_address'=>$from_email_address,
                         'msg_subject'=>$email_subject,
                         'user_message'=>$email_text,
                         'msg_status'=>'active',
                         'inserted'=>'now()',
                        );
   tep_db_perform(ADMIN_MESSAGE_TABLE, $sql_data_array);
  /////
  $messageStack->add_session(SUCCESS_EMAIL_SENT, 'success');
  tep_redirect(getPermalink(FILENAME_CONTACT_US));
 }
}
$gogle_captcha='';
if(MODULE_G_CAPTCHA_PLUGIN=='enable')

 $gogle_captcha=''.$reCaptcha->reCaptchaGetCaptcha().'';
$template->assign_vars(array(
 'FIRST_PARAGRAPH'         => FIRST_PARAGRAPH,
 'HEADING_TITLE'           => HEADING_TITLE,
'google_captcha'=>$gogle_captcha,

 'INFO_TEXT_FULL_NAME'     => INFO_TEXT_FULL_NAME,
 'INFO_TEXT_FULL_NAME1'    => tep_draw_input_field('TR_full_name',$from_email_name, 'size="45" class="form-control required"'),
 'INFO_TEXT_EMAIL_ADDRESS' =>INFO_TEXT_EMAIL_ADDRESS,
 'INFO_TEXT_EMAIL_ADDRESS1'=>tep_draw_input_field('TREF_email_address',$from_email_address, 'size="45" class="form-control required"'),
 'INFO_TEXT_SUBJECT'       =>INFO_TEXT_SUBJECT,
 'INFO_TEXT_SUBJECT1'      =>tep_draw_input_field('TR_subject',$email_subject, 'size="45" class="form-control required"'),
 'INFO_TEXT_MESSAGE'       =>INFO_TEXT_MESSAGE,
 'INFO_TEXT_MESSAGE1'      =>tep_draw_textarea_field('TR_message', 'soft', '40%', '8', $email_text, 'class="form-control required h-50"', true),
 'INFO_TEXT_SECURITY_CODE' => INFO_TEXT_SECURITY_CODE,
 'INFO_TEXT_SECURITY_CODE1'=> tep_draw_input_field('TR_security_code','','class="form-control"',true),
 'INFO_TEXT_TYPE_CODE'     => INFO_TEXT_TYPE_CODE,
 'form'                    =>tep_draw_form('send', FILENAME_CONTACT_US,'','post', 'onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action','send'),
 //'button'                  =>tep_image_submit(PATH_TO_BUTTON.'button_send.gif', IMAGE_SEND),
 'button'=>tep_button_submit('btn btn-primary m-btn-full',''.IMAGE_SEND.''),
 'INFO_TEXT_JSCRIPT_FILE'  =>$jscript_file,
 'LEFT_BOX_WIDTH'          =>LEFT_BOX_WIDTH1,
 'RIGHT_BOX_WIDTH'         =>RIGHT_BOX_WIDTH1,
 'LEFT_HTML'               =>LEFT_HTML,
 'RIGHT_HTML'              =>RIGHT_HTML,
 'update_message'          =>$messageStack->output()));
$template->pparse('contact_us');
?>