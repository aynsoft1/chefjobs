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
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_JOBSEEKER_FORGOT_PASSWORD);
$template->set_filenames(array('password' => 'jobseeker_forgot_password.htm'));
include_once(FILENAME_BODY);
$jscript_file=PATH_TO_LANGUAGE.$language."/jscript/".'forgot_password.js';

if($_POST['action']=='send')
{
 $jobseeker_email_address=tep_db_prepare_input($_POST['TREF_email_address']);
	$whereClause="jl.jobseeker_email_address='".tep_db_input($jobseeker_email_address)."' and jl.jobseeker_status='Yes' and jl.jobseeker_id=j.jobseeker_id";
 $fields='jl.jobseeker_id,concat(j.jobseeker_first_name," ",j.jobseeker_last_name) as full_name,jl.jobseeker_email_address';
 // Check if email exists
 if($row=getAnyTableWhereData(JOBSEEKER_LOGIN_TABLE.' as jl, '.JOBSEEKER_TABLE.' as j',$whereClause,$fields))
 {
  $reset_code=get_user_opt($jobseeker_email_address);
  $link=tep_href_link(FILENAME_RESET_PASSWORD,'reset_code='.$reset_code.'&web=1&action=reset_password&email_address='.$jobseeker_email_address);
  $paramete=array('user_name'=>$row['full_name'],'email_address'=>$jobseeker_email_address,'opt'=>$reset_code,'reset_link'=>$link);
  send_reset_opt($paramete);
  tep_redirect(tep_href_link(FILENAME_RESET_PASSWORD,'web=1&email_address='.urlencode($jobseeker_email_address)));
 }
  else
 {
  $messageStack->add(sprintf(EMAIL_NOT_FOUND,tep_db_output($jobseeker_email_address)), 'error');
 }
}
$template->assign_vars(array(
 'HEADING_TITLE'=>HEADING_TITLE,
 'HEADING_CONTENT'=>HEADING_CONTENT,
 'TREF_email_address'=>tep_draw_input_field('TREF_email_address',$jobseeker_email_address, 'class="form-control mb-2" placeholder="'.EMAIL_ADDRESS.'"'),
 'buttons'=>tep_button_submit('btn btn-primary w-100 mt-3',''.IMAGE_CONFIRM.''),
 //'buttons'=>'<a href="'.tep_href_link(FILENAME_INDEX).'">'.tep_image_button(PATH_TO_BUTTON.'button_cancel.gif','Cancel').'</a>&nbsp;'.tep_image_submit(PATH_TO_BUTTON.'button_confirm.gif','Confirm'),
 'form'=>tep_draw_form('forgot_password',FILENAME_JOBSEEKER_FORGOT_PASSWORD,'','post','onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action','send'),
 'INFO_TEXT_JSCRIPT_FILE'  => '<script src="'.$jscript_file.'"></script>',
 'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
 'RIGHT_BOX_WIDTH'=>RIGHT_BOX_WIDTH1,
 'LEFT_HTML'=>LEFT_HTML,
 'RIGHT_HTML'=>RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('password');
?>