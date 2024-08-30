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
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_TELL_TO_FRIEND);
$template->set_filenames(array('tell_to_friend' => 'tell_to_friend.htm'));
include_once(FILENAME_BODY);
$jscript_file=PATH_TO_LANGUAGE.$language."/jscript/".'tell_to_friend.js';

if(check_login("jobseeker"))
{
	$row=getAnyTableWhereData(JOBSEEKER_LOGIN_TABLE." as jl,".JOBSEEKER_TABLE." as j","jl.jobseeker_id=j.jobseeker_id and j.jobseeker_id='".$_SESSION['sess_jobseekerid']."'","concat(j.jobseeker_first_name,' ',j.jobseeker_last_name) as full_name,jl.jobseeker_email_address");
	$TR_your_full_name=tep_db_output($row['full_name']);
	$TREF_your_email_address=tep_db_output($row['jobseeker_email_address']);
}
else
{
	$TR_your_full_name='';
	$TREF_your_email_address='';
}
$query_string=$_GET['query_string'];

$job_id=check_data($query_string,"=","job_id","job_id");
$now=date('Y-m-d H:i:s');
$table_names=JOB_TABLE;
$where_clause=" job_id='".$job_id."' and expired >='$now' and re_adv <='$now' and job_status='Yes' and ( deleted is NULL or deleted='0000-00-00 00:00:00')";
$field_names="job_title";
if(!$row=getAnyTableWhereData($table_names,$where_clause,$field_names)) 
{ ///Hack attempt
 $messageStack->add_session(ERROR_JOB_NOT_EXIST, 'error');
 tep_redirect(tep_href_link(FILENAME_ERROR));
}

$title_format=encode_category($row['job_title']);
$action = (isset($_POST['action']) ? $_POST['action'] : '');
// search
if($action=="send")
{
 $error =false;
 $from_email_name=tep_db_output(SITE_TITLE);
 $from_email_address=tep_db_output(EMAIL_FROM);

 $your_email_name=stripslashes($_POST['TR_your_full_name']);
 $your_email_address=stripslashes($_POST['TREF_your_email_address']);

 $to_name=stripslashes($_POST['TR_your_friend_full_name']);
 $to_email_address=stripslashes($_POST['TREF_your_friend_email_address']);

 $email_text='<div style="font: normal 12px/17px Verdana, Arial, Helvetica, sans-serif;">'.INFO_TEXT_HI.' <b>'.$to_name.',</b>';
 $email_text.='<br>&nbsp;'.INFO_TEXT_YOUR_FRIEND.' <b>'.$your_email_name.'</b> '.INFO_TEXT_HAS_SENT;
 $email_text.='<br>&nbsp; '.INFO_TEXT_EMAIL_ADDRESS.' <b>'.$your_email_address.' </b>.';
 $email_text.='<br>&nbsp; '.INFO_TEXT_MESSAGE.'<hr>';
 $TR_message =(stripslashes($_POST['TR_message']));
 $email_text.=nl2br(stripslashes($_POST['TR_message']));
 $email_text.='</div>';
 $email_subject=stripslashes($_POST['TR_subject']);
 $security_code1=stripslashes($_POST['TR_security_code']);

 if(!tep_not_null($your_email_name))
 {
  $error =true;
  $messageStack->add(YOUR_NAME_ERROR, 'error');
 }
 if(!tep_not_null($your_email_address))
 {
  $error =true;
  $messageStack->add(YOUR_EMAIL_ADDRESS_ERROR, 'error');
 }
 if(!tep_not_null($to_name))
 {
  $error =true;
  $messageStack->add(YOUR_FRIEND_NAME_ERROR, 'error');
 }
 if(!tep_not_null($to_email_address))
 {
  $error =true;
  $messageStack->add(YOUR_FRIEND_EMAIL_ADDRESS_ERROR, 'error');
 }
 if(!tep_not_null($email_subject))
 {
  $error =true;
  $messageStack->add(EMAIL_SUBJECT_ERROR, 'error');
 }
 if(!tep_not_null($TR_message))
 {
  $error =true;
  $messageStack->add(EMAIL_MESSAGE_ERROR, 'error');
 }
 if($_SESSION['security_code'] != $security_code1 || $security_code1==''  ||  (strtolower($_SERVER['HTTP_REFERER'])!=strtolower(tep_href_link(FILENAME_TELL_TO_FRIEND,'query_string='.$query_string)))) 
 {
  $error =true;
  $messageStack->add(ERROR_SECURITY_CODE, 'error');
 }

 if(!$error)
 {
		unset($_SESSION['security_code']);
  //echo $email_text;die();
  tep_mail($to_name, $to_email_address, $email_subject, $email_text,SITE_OWNER,EMAIL_FROM);
  $messageStack->add_session(SUCCESS_EMAIL_SENT, 'success');
  tep_redirect(getPermalink('job',array('ide'=>$job_id,'seo_name'=>$title_format)));
 }
}
else
{
 $TR_message="\n\n\n\n\n\n\n\n\n".INFO_TEXT_CHECK_LINK."\n ".INFO_TEXT_JOB_TITLE." <a href=".getPermalink('job',array('ide'=>$job_id,'seo_name'=>$title_format))." target='_job_detail'>".tep_db_output($row['job_title'])."</a>";
}
$template->assign_vars(array(
 'HEADING_TITLE'=>HEADING_TITLE,
 'INFO_TEXT_FROM_NAME'=>INFO_TEXT_FROM_NAME,
 'INFO_TEXT_FROM_NAME1'=>tep_draw_input_field('TR_your_full_name', $your_email_name,'size="40" class="form-control required"',false),
 'INFO_TEXT_FROM_EMAIL_ADDRESS'=>INFO_TEXT_FROM_EMAIL_ADDRESS,
 'INFO_TEXT_FROM_EMAIL_ADDRESS1'=>tep_draw_input_field('TREF_your_email_address', $your_email_address,'size="40" class="form-control required"',false),

 'INFO_TEXT_TO_NAME'          => INFO_TEXT_TO_NAME,
 'INFO_TEXT_TO_NAME1'         => tep_draw_input_field('TR_your_friend_full_name',$to_name,'size="40" class="form-control required"',false),
 'INFO_TEXT_TO_EMAIL_ADDRESS' => INFO_TEXT_TO_EMAIL_ADDRESS,
 'INFO_TEXT_TO_EMAIL_ADDRESS1'=> tep_draw_input_field('TREF_your_friend_email_address', $to_email_address,'size="40" class="form-control required"',false),

 'INFO_TEXT_SUBJECT'=>INFO_TEXT_SUBJECT,
 'INFO_TEXT_SUBJECT1'=>tep_draw_input_field('TR_subject', $email_subject,'size="40" class="form-control required"',false),
 'INFO_TEXT_MESSAGE'=>INFO_TEXT_MESSAGE,
 'INFO_TEXT_MESSAGE1'=>tep_draw_textarea_field('TR_message', 'soft', '50', '8', $TR_message, 'class="form-control required"', '',false),
 'INFO_TEXT_SECURITY_CODE' => INFO_TEXT_SECURITY_CODE,
 'INFO_TEXT_SECURITY_CODE1'=> tep_draw_input_field('TR_security_code','','class="form-control required"',false),
 'INFO_TEXT_TYPE_CODE'     => INFO_TEXT_TYPE_CODE,     
 'form'=>tep_draw_form('send', FILENAME_TELL_TO_FRIEND,'query_string='.$query_string,'post', 'onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action','send'),

 'button'=>tep_button_submit('btn btn-primary', IMAGE_SEND),
 'INFO_TEXT_JSCRIPT_FILE'  => '<script src="'.$jscript_file.'"></script>',
 'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
 'RIGHT_BOX_WIDTH'=>RIGHT_BOX_WIDTH1,
 'LEFT_HTML'=>LEFT_HTML,
 'RIGHT_HTML'=>RIGHT_HTML,
	'LEFT_HTML_JOBSEEKER'=>LEFT_HTML_JOBSEEKER,
 'update_message'=>$messageStack->output()));
$template->pparse('tell_to_friend');
?>