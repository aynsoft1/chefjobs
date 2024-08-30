<?
include_once("include_files.php");
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_JOBSEEKER_REGISTRATION_CONFIRM);
$template->set_filenames(array('confirm' => 'jobseeker_registration_confirm.htm'));
include_once(FILENAME_BODY);
if(!isset($_SESSION['sess_user_name']))
{
 tep_redirect(tep_href_link(FILENAME_INDEX));
}
$redirect_url=(tep_not_null($_SESSION['REDIRECT_URL'])?HOST_NAME_MAIN.$_SESSION['REDIRECT_URL']:'');
if(tep_not_null($redirect_url))
 $button='<a href="'.$redirect_url.'" class="btn btn-primary">'.IMAGE_BUTTON_NEXT.'</a>';
else
{
 if($row_resume=getAnyTableWhereData(JOBSEEKER_RESUME1_TABLE,"jobseeker_id='".$_SESSION['sess_jobseekerid']."' ","resume_id"))
 {
  $button=tep_draw_form('resume', FILENAME_JOBSEEKER_RESUME1, '', 'post', 'onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('resume_id',$row_resume['resume_id']);
		$button.=tep_button_submit('btn btn-primary',''.IMAGE_BUTTON_NEXT.'').'</form>';
 }
	else
 $button='<a href="'.tep_href_link(FILENAME_JOBSEEKER_RESUME1).'" class="btn btn-primary">'.IMAGE_BUTTON_NEXT.'</a>';
}

$template->assign_vars(array(
 'HEADING_TITLE'=>HEADING_TITLE,
// 'TEXT_ACCOUNT_CREATED'=>sprintf(TEXT_ACCOUNT_CREATED,tep_db_output($_SESSION['sess_user_name']),tep_db_output($_SESSION['sess_email_address']),tep_db_output($_SESSION['sess_password'])),
 'TEXT_ACCOUNT_CREATED'=>sprintf(TEXT_ACCOUNT_CREATED,tep_db_output($_SESSION['sess_user_name']),tep_db_output($_SESSION['sess_email_address']),'XXXXXXXXXX'),
 'button'=>$button,
 'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
 'RIGHT_BOX_WIDTH'=>RIGHT_BOX_WIDTH1,
 'LEFT_HTML'=>LEFT_HTML,
 'RIGHT_HTML'=>RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('confirm');
unset($_SESSION['sess_user_name']);
unset($_SESSION['sess_email_address']);
unset($_SESSION['sess_password']);
?>