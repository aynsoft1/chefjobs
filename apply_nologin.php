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
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_APPLY_NOLOGIN);
$template->set_filenames(array('apply_nologin' => 'apply_nologin.htm'));
include_once(FILENAME_BODY);
 include_once "class/reCaptcha.php";

//$jscript_file=PATH_TO_LANGUAGE.$language."/jscript/".'jobseeker_registration.js';
$query_string=$_GET['query_string'];

if(!tep_not_null($query_string))
{
 $messageStack->add_session(ERROR_JOB_NOT_EXIST, 'error');
 tep_redirect(tep_href_link(FILENAME_ERROR));
}
$job_id=check_data($query_string,"=","job_id","job_id");

$now=date('Y-m-d H:i:s');
//$table_names=JOB_TABLE." as j, ".RECRUITER_LOGIN_TABLE." as rl,".RECRUITER_TABLE." as r";
//$where_clause=" j.recruiter_id=r.recruiter_id and rl.recruiter_id=r.recruiter_id and j.job_id='".$job_id."'";//and j.job_source='jobsite'
//$field_names="j.recruiter_user_id,j.job_reference,j.job_title,rl.recruiter_email_address,concat(r.recruiter_first_name,' ',r.recruiter_last_name) as r_full_name,j.display_id,j.post_url,j.url,r.recruiter_id";

if($row=getAnyTableWhereData(JOB_TABLE,"job_id='".$job_id."'",'recruiter_id,job_title,job_location,job_state,job_state_id,job_country_id'))
{
 $recruiter_id=$row['recruiter_id'];
 $title_format=$row['job_title'];
$marker_location=(tep_not_null($row['job_location'])?tep_db_output($row['job_location']).', ':'').(($row['job_state_id'] > 0)?get_name_from_table(ZONES_TABLE,TEXT_LANGUAGE.'zone_name','zone_id',tep_db_output($row['job_state_id'])).", ":((tep_db_output($row['job_state']!='')?tep_db_output($row['job_state']).", ":''))).get_name_from_table(COUNTRIES_TABLE,'country_name','id',tep_db_output($row['job_country_id']));

$rowrec=getAnyTableWhereData(RECRUITER_TABLE,"recruiter_id='".$recruiter_id."'",'recruiter_company_name,recruiter_logo');
 $company=$rowrec['recruiter_company_name'];
$company_logo=$rowrec['recruiter_logo'];
	if(tep_not_null($company_logo) && is_file(PATH_TO_MAIN_PHYSICAL.PATH_TO_LOGO.$company_logo))
	{
		$photo=tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_LOGO.$company_logo.'&size=200','','','','class="mini-profile-img img-fluid img-thumbnail mr-3"');
	  $company_logo=$photo;
	}
	else
	{
	  $photo=tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_IMG."nologo.jpg".'&size=200','','','','class="mini-profile-img img-fluid img-thumbnail mr-3"');
	  $company_logo='<a href="'.$row['recruiter_url'].'" target="new_site">'.$photo.'</a>';
	}
}

$action = (isset($_POST['action']) ? $_POST['action'] : '');
$error=false;
$resume_name='';
//echo "action=".$action;die;
/*****************************/
 $g_captcha =true;
 $reCaptcha=new reCaptcha();
/******************************/
if ($action=='apply')
{
	$jb_nl_name=tep_db_prepare_input($_POST['jb_nl_name']);
	$jb_nl_email=tep_db_prepare_input($_POST['jb_nl_email']);
	$jb_nl_msg=tep_db_prepare_input($_POST['jb_nl_msg']);
    $resume_name= tep_db_prepare_input($_POST['my_resumenl']);

	 if(strlen($jb_nl_name)<=0)
	 {
	  $error = true;
	  $messageStack->add(ENTER_NAME_ERROR, 'error');
	 }
	 if(strlen($jb_nl_email)<=0)
	 {
	  $error = true;
	  $messageStack->add(ENTER_EMAIL_ERROR, 'error');
	 }
if(MODULE_GOOGLE_PLUGIN=='enable')
 if(!$reCaptcha->reCaptchaVerify())
      {
       $error = true;
       $messageStack->add(CAPTCHA_ERROR_1,'Error');
	  }

//////// file upload resume starts //////
			///*
			if(tep_not_null($_FILES['my_resumenl']['name']))
			{//echo "enter".die;
				if($obj_resume = new upload('my_resumenl', PATH_TO_MAIN_PHYSICAL_APPLY_NOLOGIN_RESUME,'644',array('doc','pdf','txt','docx')))
				{
					$resume_name=tep_db_input($obj_resume->filename);
					$destination=PATH_TO_MAIN_PHYSICAL_APPLY_NOLOGIN_RESUME.$resume_name;
				}
				else
				{
			     $error = true;
		         $messageStack->add(INVALID_FILE_TYPE_ERROR, 'error');
		       //$messageStack->add_session(ERROR_RESUME_SEND, 'Error');
					//tep_redirect(tep_href_link($job_id.'/'.$title_format.'.html'));
				}
			}
			else
			{
	 	      $error = true;
		      $messageStack->add(INVALID_FILE_TYPE_ERROR, 'error');
			  //$messageStack->add_session(ERROR_RESUME_SEND, 'Error');
			  //tep_redirect(tep_href_link($job_id.'/'.$title_format.'.html'));
			}
			//*/
			//////// file upload ends //////

	 if(!$error)
	{
		$sql_data_array = array('applicantnl_name' => $jb_nl_name,
							'applicantnl_email'=>$jb_nl_email,
							'applicantnl_msg'=>$jb_nl_msg,
							'job_id'=>$job_id,
							'recruiter_id'=>$recruiter_id,
							'applicantnl_resume'=>$resume_name,
							'inserted'=>'now()');
		tep_db_perform(APPLICANT_NOLOGIN_TABLE, $sql_data_array);
		$messageStack->add_session(SUCCESS_RESUME_SEND, 'success');
tep_redirect(tep_href_link(FILENAME_JOB_SEARCH));
	    //tep_redirect(tep_href_link($job_id.'/'.encode_category($title_format).'.html'));
 	}

}
$gogle_captcha='<tr valign="top">
					<td style="padding-top:12px;">
					'.$reCaptcha->reCaptchaGetCaptcha().'
					</td>
				</tr>';
$template->assign_vars(array(
 'HEADING_TITLE'=>HEADING_TITLE,
 'form'=>tep_draw_form('application_nologin', FILENAME_APPLY_NOLOGIN, 'query_string='.$query_string, 'post', 'enctype="multipart/form-data"  onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action','apply'),
 'INFO_TEXT_NAME'=>INFO_TEXT_NAME,
 'INFO_TEXT_NAME1'=>tep_draw_input_field('jb_nl_name', $jb_nl_name ,'class="form-control"',false),
 'INFO_TEXT_EMAIL'=>INFO_TEXT_EMAIL,
 'INFO_TEXT_EMAIL1'=>tep_draw_input_field('jb_nl_email', $jb_nl_email ,'class="form-control"',false),
 'INFO_TEXT_MESSAGE'=>INFO_TEXT_MESSAGE.' :',
 'INFO_TEXT_MESSAGE1'=>tep_draw_textarea_field('jb_nl_msg', 'soft', '68', '4', $jb_nl_msg, 'class="form-control"', false),
 'INFO_TEXT_UPLOAD_RESUME'=>INFO_TEXT_UPLOAD_RESUME,
 'INFO_TEXT_UPLOAD_RESUME1'=>tep_draw_file_field("my_resumenl").'&nbsp;<br>'.INFO_TEXT_UPLOAD_RESUME_HELP,
 'button'=>'<button class="btn btn-primary" type="submit">Confirm</button>',
 'INFO_TEXT_JSCRIPT_FILE'  => $jscript_file,
 'job_title'=>$title_format,
 'company'=>$company,
 'company_logo'=>$company_logo,
 'job_location'=>$marker_location,
 'google_captcha'=>$gogle_captcha,
 'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
 'RIGHT_BOX_WIDTH'=>RIGHT_BOX_WIDTH1,
 'LEFT_HTML'=>LEFT_HTML,
 'JOB_SEARCH_LEFT'        => JOB_SEARCH_LEFT,
 'RIGHT_HTML'=>RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('apply_nologin');
?>