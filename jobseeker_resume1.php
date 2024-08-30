<?php
include_once("include_files.php");
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_JOBSEEKER_RESUME1);
$template->set_filenames(array('resume_step1' => 'jobseeker_resume1.htm'));
include_once(FILENAME_BODY);
$jscript_file=PATH_TO_LANGUAGE.$language."/jscript/".'jobseeker_resume1.js';
if(!check_login("jobseeker"))
{
 $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
 tep_redirect(getPermalink(FILENAME_JOBSEEKER_LOGIN));
}
//print_r($_POST);die();
if(!isset($_POST['resume_id']))
$temp_resume_no=no_of_records(JOBSEEKER_RESUME1_TABLE,"jobseeker_id='".$_SESSION['sess_jobseekerid']."'");
if(($temp_resume_no+1)>MAX_NUM_OF_RESUMES)
{
 $messageStack->add_session(sprintf(ERROR_EXCEED_MAX_NO_RESUME,$temp_resume_no), 'error');
 tep_redirect(FILENAME_JOBSEEKER_LIST_OF_RESUMES);
}
//////////////
$action = (isset($_POST['action']) ? $_POST['action'] : '');

$resume_id=$_POST['resume_id'];
//$resume_id=1;//$_POST['resume_id'];

// add/edit
if(tep_not_null($action))
{
 switch($action)
 {
  case 'add':
  case 'edit':
   $resume_id                = $_POST['resume_id'];
   $resume_name              = $_POST['TR_resume_name'];
   $objective                = $_POST['objective'];
   if(tep_not_null($_POST['job_type']))
   {
    $job_type1=$_POST['job_type'];
    $job_type=implode(",",$job_type1);
   }
	if(tep_not_null($_POST['currency']))
		$currency                 = tep_db_prepare_input($_POST['currency']);
	else
		$currency=0;
   $expected_salary          = $_POST['expected_salary'];
   $expected_salary_per      = $_POST['expected_salary_per'];
   $target_job_titles        = $_POST['TR_target_job_titles'];
   $nationality              = (int)tep_db_prepare_input($_POST['TR_nationality']);

   if(tep_not_null($_POST['TR_industry_sector']))
   {
    $total_industry_sector=count($_POST['TR_industry_sector']);

    $industry_sector1=$_POST['TR_industry_sector'];
    $industry_sector=implode(",",$industry_sector1);
    $industry_sector=remove_child_job_category($industry_sector);
   }
   $relocate                 = $_POST['relocate'];
   $facebook_url = tep_db_prepare_input($_POST['facebook_url']);
   $instagram_url   = tep_db_prepare_input($_POST['google_url']); //google url is changed to instagram url
   $linkedin_url = tep_db_prepare_input($_POST['linkedin_url']);
   $twitter_url  = tep_db_prepare_input($_POST['twitter_url']);
   $error=false;
   if (strlen($resume_name) <=0)
   {
    $error = true;
    $messageStack->add(RESUME_NAME_ERROR,'position');
   }
   if (strlen($target_job_titles) <=0)
   {
    $error = true;
    $messageStack->add(TARGET_JOB_TITLES_ERROR,'error');
   }
   if ($nationality <=0)
   {
    $error = true;
    $messageStack->add(NATIONALITY_ERROR,'error');
   }

   if($row_title=getAnyTableWhereData(JOBSEEKER_RESUME1_TABLE,"jobseeker_id='".$_SESSION['sess_jobseekerid']."' and  resume_title='".$resume_name."' and  resume_id !='".$resume_id."' ","resume_id"))
   {
    $error=true;
    $messageStack->add(SAME_RESUME_NAME_ERROR,'error');
   }
   if($total_industry_sector > 5)
   {
    $error=true;
    $messageStack->add(EXCEED_INDUSTORY_SECTOR_ERROR,'error');
   }
   if($total_industry_sector <=0)
   {
    $error=true;
    $messageStack->add(INDUSTORY_SECTOR_ERROR,'error');
   }
			if(tep_not_null($facebook_url))
		 {
	   if(!preg_match('/^(http|https):\/\//i',$facebook_url))
    $facebook_url ='http://'.$facebook_url;
			}
			if(tep_not_null($instagram_url))
		 {
	   if(!preg_match('/^(http|https):\/\//i',$instagram_url))
    $instagram_url ='http://'.$instagram_url;
			}
			if(tep_not_null($linkedin_url))
		 {
	   if(!preg_match('/^(http|https):\/\//i',$linkedin_url))
    $linkedin_url ='http://'.$linkedin_url;
			}
			if(tep_not_null($twitter_url))
		 {
	   if(!preg_match('/^(http|https):\/\//i',$twitter_url))
    $twitter_url ='http://'.$twitter_url;
			}

 		if(!$error)
			{
				$sql_data_array=array('resume_title'             =>tep_db_prepare_input($resume_name),
                          'objective'                =>tep_db_prepare_input($objective),
                          'job_type_id'              =>tep_db_prepare_input($job_type),
                     	  'currency'                 =>tep_db_prepare_input($currency),
                          'expected_salary'          =>tep_db_prepare_input($expected_salary),
                          'expected_salary_per'      =>tep_db_prepare_input($expected_salary_per),
                          'target_job_titles'        =>tep_db_prepare_input($target_job_titles),
                          'jobseeker_nationality'    =>tep_db_prepare_input($nationality),
                          'job_category'             =>tep_db_prepare_input($industry_sector),
                          'relocate'                 =>tep_db_prepare_input($relocate),
                          'facebook_url'             => $facebook_url,
                          'google_url'               => $instagram_url,
                          'linkedin_url'             => $linkedin_url,
                          'twitter_url'              => $twitter_url,
                          );

    if($action=='edit')
				{
     ////////////////////////////////////////////////////
     $job_category2=explode(',',$industry_sector);
				 $sql_job_array=array('resume_id'=>$resume_id );
		  	for($i=0;$i<count($job_category2);$i++)
					{
					 if(!$job_row = getAnyTableWhereData(RESUME_JOB_CATEGORY_TABLE, "resume_id = '" . tep_db_input($resume_id) . "' and job_category_id='".$job_category2[$i]."'", "job_category_id"))
					 {
					 	$sql_job_array['job_category_id']=$job_category2[$i];
						 tep_db_perform(RESUME_JOB_CATEGORY_TABLE,$sql_job_array);
					 }
					}
					if(!tep_not_null($industry_sector))
					$industry_sector=0;
 				tep_db_query("delete from ".RESUME_JOB_CATEGORY_TABLE." where resume_id='".$resume_id."' and job_category_id not in(".$industry_sector.")");
     //////////////////////////////////////////////////////
     $sql_data_array['updated']='now()';
		   tep_db_perform(JOBSEEKER_RESUME1_TABLE, $sql_data_array, 'update', "jobseeker_id = '" . $_SESSION['sess_jobseekerid'] . "' and  resume_id ='".$resume_id ."'");
     $query_string = encode_string("resume_id@@@".$resume_id."@@@resume");
     $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
		  }
    elseif($action=='add')
    {
     $sql_data_array['inserted']='now()';
     $sql_data_array['availability_date']='now()';
		   $sql_data_array['jobseeker_id']=$_SESSION['sess_jobseekerid'];

    	tep_db_perform(JOBSEEKER_RESUME1_TABLE, $sql_data_array,'insert');
     $messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');
     $row1=getAnyTableWhereData(JOBSEEKER_RESUME1_TABLE,"jobseeker_id='".$_SESSION['sess_jobseekerid']."'","max(resume_id) as resume_id");
     $resume_id_new=$row1['resume_id'];
     $query_string=encode_string("resume_id@@@".$resume_id_new."@@@resume");
     ////////////////////////////////////////////////////
     $job_category2=explode(',',$industry_sector);
				 $sql_job_array=array('resume_id'=>$resume_id_new );
		  	for($i=0;$i<count($job_category2);$i++)
					{
					 if(!$job_row = getAnyTableWhereData(RESUME_JOB_CATEGORY_TABLE, "resume_id = '" . tep_db_input($resume_id) . "' and job_category_id='".$job_category2[$i]."'", "job_category_id"))
					 {
					 	$sql_job_array['job_category_id']=$job_category2[$i];
						 tep_db_perform(RESUME_JOB_CATEGORY_TABLE,$sql_job_array);
					 }
					}
     ////////////////////////////////////////////////////////////
			 }
    $sql_data_array1['updated']='now()';
				tep_db_perform(JOBSEEKER_LOGIN_TABLE, $sql_data_array1, 'update', "jobseeker_id = '" . $_SESSION['sess_jobseekerid'] . "'");
				tep_redirect(tep_href_link(FILENAME_JOBSEEKER_RESUME2."?query_string=".$query_string));
			}
			break;
 }
}
//////////////////////////////
if($_SESSION['sess_new_jobseeker']=='y')
{
	$add_save_button='<button class="btn btn-primary px-5" type="submit">'.TEXT_NEXT.'</button>';//tep_image_submit(PATH_TO_BUTTON.'button_next.gif', IMAGE_NEXT);
}
else
{
	$add_save_button='<button class="btn btn-primary px-5" type="submit">'.TEXT_NEXT.'</button>';//tep_image_submit(PATH_TO_BUTTON.'button_next.gif', IMAGE_NEXT);
}
if($error)
{
 $resume_name     = $resume_name;
 $industry_sector = $industry_sector;
 $job_type        = $job_type;
 if($_POST['action']=="edit")
 {
  $add_save_button     = '<button class="btn btn-primary me-2" type="submit">'.IMAGE_UPDATE.'</button>';//tep_image_submit(PATH_TO_BUTTON.'button_update.gif', IMAGE_UPDATE);
  $query_string=encode_string("resume_id@@@".$_POST['resume_id']."@@@resume");
  $add_next_button     = "<a  href='".tep_href_link(FILENAME_JOBSEEKER_RESUME2."?query_string=".$query_string)."' class='btn btn-outline-secondary mmt-15'>".tep_db_output(INFO_SKIP_THIS_STEP)."</a>";
  $registration_form=tep_draw_form('defineForm', FILENAME_JOBSEEKER_RESUME1, '', 'post', 'onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('resume_id',$_POST['resume_id']).tep_draw_hidden_field('action','edit');
 }
 else
 {
  $add_save_button='<button class="btn btn-primary px-5" type="submit">'.TEXT_NEXT.'</button>';//tep_image_submit(PATH_TO_BUTTON.'button_next.gif', IMAGE_NEXT);
  $add_next_button     = "";
  $registration_form=tep_draw_form('defineForm', FILENAME_JOBSEEKER_RESUME1, '', 'post', 'onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action','add');
 }
}
else
{
 $fields="resume_id, jobseeker_id, resume_title, objective,  job_type_id, currency, expected_salary, expected_salary_per, target_job_titles, job_category,  relocate,jobseeker_nationality, facebook_url,google_url,linkedin_url,twitter_url";//career_level_id, experience, education_level_id, target_job_title,
 if($row2=getAnyTableWhereData(JOBSEEKER_RESUME1_TABLE,"jobseeker_id='".$_SESSION['sess_jobseekerid']."' and resume_id ='".$resume_id."'",$fields))
 {
  $query_string             = encode_string("resume_id@@@".$row2['resume_id']."@@@resume");
  $add_save_button          = '<button class="btn btn-primary me-2" type="submit">'.IMAGE_UPDATE.'</button>';//tep_image_submit(PATH_TO_BUTTON.'button_update.gif', IMAGE_UPDATE);
  $add_next_button          = "<a class='btn btn-outline-secondary mmt-15' href='".tep_href_link(FILENAME_JOBSEEKER_RESUME2."?query_string=".$query_string)."' >".tep_db_output(INFO_SKIP_THIS_STEP)."</a>";
  $registration_form        =tep_draw_form('defineForm', FILENAME_JOBSEEKER_RESUME1, '', 'post', 'onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('resume_id',$row2['resume_id']).tep_draw_hidden_field('action','edit');
  $resume_id                = $row2['resume_id'];
  $resume_name              = $row2['resume_title'];
  $objective                = $row2['objective'];
  $job_type                 = $row2['job_type_id'];
  $currency                 = $row2['currency'];
  $expected_salary          = $row2['expected_salary'];
  $expected_salary_per      = $row2['expected_salary_per'];
  $target_job_titles        = $row2['target_job_titles'];
  $nationality              = $row2['jobseeker_nationality'];
  $industry_sector        = get_name_from_table(RESUME_JOB_CATEGORY_TABLE,'job_category_id','resume_id',tep_db_output($resume_id));
  $relocate                 = $row2['relocate'];
  $facebook_url             = $row2['facebook_url'];
  $instagram_url               = $row2['google_url'];
  $linkedin_url             = $row2['linkedin_url'];
  $twitter_url              = $row2['twitter_url'];
 }
 else
 {
  $row12=getAnyTableWhereData(JOBSEEKER_TABLE,"jobseeker_id='".$_SESSION['sess_jobseekerid']."'",'jobseeker_first_name ,jobseeker_last_name ');
  $add_save_button   = '<button class="btn btn-primary px-5" type="submit">'.TEXT_NEXT.'</button>';//tep_image_submit(PATH_TO_BUTTON.'button_next.gif', IMAGE_NEXT);
  $add_next_button   = '';
  $registration_form = tep_draw_form('defineForm', FILENAME_JOBSEEKER_RESUME1, '', 'post', 'onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action','add');
  $resume_id         = "";
  $resume_name       = INFO_TEXT_RESUME.":".$row12['jobseeker_first_name']." ".$row12['jobseeker_last_name'];
  $job_type          = "";
  $currency          ='';
  $nationality  =DEFAULT_COUNTRY_ID;
  $industry_sector  ='';
  $expected_salary_per  ="Hour";
 }
}
if(check_login("jobseeker"))
{
    $resume1='<div class="step ms-0 current"><a class="" href ="#">'.INFO_TEXT_LEFT_RESUME.'</a></div>';
		  $resume2='<div class="step"><a class="" href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME2.'?query_string='.$query_string).'" >'.INFO_TEXT_LEFT_EXPERIENCE.'</a></div>';
    $resume3='<div class="step"><a class="" href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME3.'?query_string='.$query_string).'" >'.INFO_TEXT_LEFT_EDUCATION.'</a></div>';
		  $resume4='<div class="step"><a class="" href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME4.'?query_string='.$query_string).'" >'.INFO_TEXT_LEFT_SKILLS.'</a></div>';
		  $resume5='<div class="step"><a class="" href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME5.'?query_string='.$query_string).'" >'.INFO_TEXT_LEFT_UPLOAD.'</a></div>';
				$resume6='<div class="step"><a class="" href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME6.'?query_string='.$query_string).'" >'.INFO_TEXT_LEFT_REFERENCE.'</a></div>';
		  $view_resume='<div class="step"><a class="" href ="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME.'?query_string='.$query_string).'" >'.INFO_TEXT_LEFT_VIEW_RESUME.'</a></div>';
//////////////Jobseeker resume left start//////
	define('JOBSEEKER_RESUME_LEFT','

	
 

  <div class="mb-3">
  <div class="row">
	<div class="">
	<div class="arrow-steps clearfix mx-auto">
    '.$resume1.'
    '.$resume2.'
   '.$resume6.'
  '.$resume3.'
   '.$resume4.'
   '.$resume5.'
   '.$view_resume.'
   </div>
   </div>
   </div>
   </div>


<!--<div class="card bg-secondary card-sidebar mb-1">
  <div class="card-header card-header-sidebar">
    <i class="fa fa-file-text icon-page-title" aria-hidden="true"></i> '.$resume1.'
  </div>
  <div class="card-body card-body-sidebar">
    <div><a href ="#resume_name">'.INFO_TEXT_RESUME_NAME.'</a></div>
	<div><a href ="#objective">'.INFO_TEXT_OBJECTIVE.'</a></div>
	<div><a href ="#target_job">'.INFO_TEXT_TARGET_JOB.'</a></div>
  </div>
</div>

<div class="card card-sidebar mb-1">
  <div class="card-header card-header-sidebar">
    <i class="fa fa-briefcase icon-page-title" aria-hidden="true"></i> '.$resume2.'
  </div>
  <div class="card-body card-body-sidebar">
    <div><a href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME2.'?query_string='.$query_string).'" >'.INFO_TEXT_TOTAL_WORK_EXP.'</a></div>
	<div><a href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME2.'?query_string='.$query_string).'" >'.INFO_TEXT_YOUR_WORK_EXPERIENCE.'</a></div>
  </div>
</div>

<div class="card card-sidebar mb-1">
  <div class="card-header card-header-sidebar">
    <i class="fa fa-bookmark icon-page-title" aria-hidden="true"></i> '.$resume6.'
  </div>
  <div class="card-body card-body-sidebar">
    <div><a href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME6.'?query_string='.$query_string).'#reference" >'.INFO_TEXT_LIST_OF_REFERENCES.'</a></div>
  </div>
</div>

<div class="card card-sidebar mb-1">
  <div class="card-header card-header-sidebar">
    <i class="fa fa-graduation-cap icon-page-title" aria-hidden="true"></i> '.$resume3.'
  </div>
  <div class="card-body card-body-sidebar">
    <div><a href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME3.'?query_string='.$query_string).'" >'.INFO_TEXT_EDUCATION_DETAILS.'</a></div>
  </div>
</div>

<div class="card card-sidebar mb-1">
  <div class="card-header card-header-sidebar">
    <i class="fa fa-user icon-page-title" aria-hidden="true"></i> '.$resume4.'
  </div>
  <div class="card-body card-body-sidebar">
    <div><a href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME4.'?query_string='.$query_string).'" >'.INFO_TEXT_YOUR_SKILLS.'</a></div>
	<div><a href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME4.'?query_string='.$query_string).'" >'.INFO_TEXT_LANGUAGES.'</a></div>
  </div>
</div>

<div class="card card-sidebar mb-1">
  <div class="card-header card-header-sidebar">
    <i class="fa fa-upload icon-page-title" aria-hidden="true"></i> '.$resume5.'
  </div>
  <div class="card-body card-body-sidebar">
    <div><a href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME5.'?query_string='.$query_string).'" >'.INFO_TEXT_RESUME.'</a></div>
  </div>
</div>

<div class="card card-sidebar">
  <div class="card-header card-header-sidebar">
    <i class="fa fa-eye icon-page-title" aria-hidden="true"></i> '.$view_resume.'
  </div>
  <div class="card-body card-body-sidebar">
    <div><a href ="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME.'?query_string='.$query_string).'#profile" >'.INFO_TEXT_PERSONAL_PROFILE.'</a></div>
	<div><a href ="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME.'?query_string='.$query_string).'#work_experience" >'.INFO_TEXT_EXPERIENCE.'</a></div>
	<div><a href ="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME.'?query_string='.$query_string).'#target_job" >'.INFO_TEXT_TARGET_JOB.'</a></div>
  </div>
</div>-->

	<td width="19%">

	   <!--<table width="100%"  border="0" cellspacing="0" cellpadding="0" id="main-jobs-table">
					<tr>
						<td valign="top" bgcolor="#FFFFFF">
							<table width="100%"  border="0" cellspacing="1" cellpadding="0">
								<tr>
									<td class="jobseeker-resume-left-bar">
										<table  border="0" width="100%" align="left" cellpadding="0" cellspacing="0">
											<tr valign="middle">
												<td valign="middle" width="5"><i class="fa fa-file-text active-icon" aria-hidden="true"></i></td>
												<td class="active-text">'.$resume1.'</td>
												<td align="right" width="15"><i class="fa fa-arrow-right active-arrow" aria-hidden="true"></i></td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td>
										<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" id="jobs-table2">

											<tr>
												<td><div class="resume-left-sub-text"><i class="fa fa-caret-right" aria-hidden="true"></i> <a href ="#resume_name">'.INFO_TEXT_RESUME_NAME.'</a></div></td>
											</tr>

											<tr>
												<td><div class="resume-left-sub-text"><i class="fa fa-caret-right" aria-hidden="true"></i> <a href ="#objective">'.INFO_TEXT_OBJECTIVE.'</a></div></td>
											</tr>

											<tr>
												<td><div class="resume-left-sub-text"><i class="fa fa-caret-right" aria-hidden="true"></i> <a href ="#target_job">'.INFO_TEXT_TARGET_JOB.'</a></div></td>
											</tr>

											<tr>
												<td class="jobseeker-resume-left-bar">
												 <table  border="0" width="100%" align="left" cellpadding="0" cellspacing="0">
														<tr valign="middle">

															<td valign="middle" width="5"><i class="fa fa-briefcase" aria-hidden="true"></i></td>
															<td class="inactive-text">'.$resume2.'</td>															<td align="right" width="15"></td>

														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td><div class="resume-left-sub-text"><i class="fa fa-caret-right" aria-hidden="true"></i> <a href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME2.'?query_string='.$query_string).'" >'.INFO_TEXT_TOTAL_WORK_EXP.'</a></div></td>
											</tr>

											<tr>
												<td><div class="resume-left-sub-text"><i class="fa fa-caret-right" aria-hidden="true"></i> <a href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME2.'?query_string='.$query_string).'" >'.INFO_TEXT_YOUR_WORK_EXPERIENCE.'</a></div></td>
											</tr>



											<tr>
												<td height="22" bgcolor="#F3F3F3" class="style3">
													<table  border="0" width="100%" align="left" cellpadding="0" cellspacing="0">
														<tr valign="middle">															<td valign="middle" width="5"><i class="fa fa-bookmark" aria-hidden="true"></i></td>
															<td class="inactive-text">'.$resume6.'</td>															<td align="right" width="15"></td>

														</tr>
													</table>
												</td>
											</tr>

											<tr>
												<td><div class="resume-left-sub-text"><i class="fa fa-caret-right" aria-hidden="true"></i> <a href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME6.'?query_string='.$query_string).'#reference" >'.INFO_TEXT_LIST_OF_REFERENCES.'</a></div></td>
											</tr>



											<tr>
												<td height="22" bgcolor="#F3F3F3" class="style3">
													<table  border="0" width="100%" align="left" cellpadding="0" cellspacing="0">
														<tr valign="middle">
															<td><img src="img/spacer.gif" width="3" height="5"></td>
															<td valign="bottom" width="5"><i class="fa fa-graduation-cap" aria-hidden="true"></i></td>
															<td class="inactive-text">'.$resume3.'</td>
															<td align="right" width="15"><img src="img/spacer.gif" width="3" height="5"></td>
														</tr>
													</table>
												</td>
											</tr>

											<tr>
												<td><div class="resume-left-sub-text"><i class="fa fa-caret-right" aria-hidden="true"></i> <a href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME3.'?query_string='.$query_string).'" >'.INFO_TEXT_EDUCATION_DETAILS.'</a></div></td>
											</tr>

											<tr>
												<td>&nbsp;</td>
											</tr>

											<tr>
												<td height="22" bgcolor="#F3F3F3" class="style3">
													<table  border="0" width="100%" align="left" cellpadding="0" cellspacing="0">
														<tr valign="middle">
															<td valign="bottom" width="5"><i class="fa fa-user" aria-hidden="true"></i></td>
															<td class="inactive-text">'.$resume4.'</td>
															<td align="right" width="15"><img src="img/spacer.gif" width="3" height="5"></td>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td background="img/bgr_8.gif"><img src="img/trans_002.gif" width="1" height="1"></td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td><div class="resume-left-sub-text"><i class="fa fa-caret-right" aria-hidden="true"></i> <a href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME4.'?query_string='.$query_string).'" >'.INFO_TEXT_YOUR_SKILLS.'</a></div></td>
								</tr>

								<tr>
									<td><div class="resume-left-sub-text"><i class="fa fa-caret-right" aria-hidden="true"></i> <a href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME4.'?query_string='.$query_string).'" >'.INFO_TEXT_LANGUAGES.'</a></div></td>
								</tr>



								<tr>
									<td height="22" bgcolor="#F3F3F3" class="style3">
										<table  border="0" width="100%" align="left" cellpadding="0" cellspacing="0">
											<tr valign="middle">

												<td valign="bottom" width="5"><i class="fa fa-upload" aria-hidden="true"></i></td>
												<td class="inactive-text">'.$resume5.'</td>
												<td align="right" width="15"><img src="img/spacer.gif" width="3" height="5"></td>
											</tr>
										</table>
									</td>
								</tr>

								<tr>
									<td><div class="resume-left-sub-text"><i class="fa fa-caret-right" aria-hidden="true"></i> <a href ="'.tep_href_link(FILENAME_JOBSEEKER_RESUME5.'?query_string='.$query_string).'" >'.INFO_TEXT_RESUME.'</a></div></td>
								</tr>



								<tr>
									<td height="22" bgcolor="#F3F3F3" class="style3">
										<table  border="0" width="100%" align="left" cellpadding="0" cellspacing="0">
											<tr valign="middle">
												<td valign="bottom" width="5"><i class="fa fa-eye" aria-hidden="true"></i></td>
												<td class="inactive-text">'.$view_resume.'</td>
												<td align="right" width="15"><img src="img/spacer.gif" width="3" height="5"></td>
											</tr>
										</table>
									</td>
								</tr>

								<tr>
									<td><div class="resume-left-sub-text"><i class="fa fa-caret-right" aria-hidden="true"></i> <a href ="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME.'?query_string='.$query_string).'#profile" >'.INFO_TEXT_PERSONAL_PROFILE.'</a></div></td>
								</tr>

								<tr>
									<td><div class="resume-left-sub-text"><i class="fa fa-caret-right" aria-hidden="true"></i> <a href ="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME.'?query_string='.$query_string).'#work_experience" >'.INFO_TEXT_EXPERIENCE.'</a></div></td>
								</tr>

								<tr>
									<td><div class="resume-left-sub-text"><i class="fa fa-caret-right" aria-hidden="true"></i> <a href ="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME.'?query_string='.$query_string).'#target_job" >'.INFO_TEXT_TARGET_JOB.'</a></div></td>
								</tr>

							</table>
			  	</td>
			 	</tr>
				</table>-->
			</td>
			<!--<td width="1%"><img src="img/spacer.gif" width="10" height="5"></td>-->');

//////////////Jobseeker resume left ends//////
}
if($messageStack->size('job_category') > 0)
 $update_message=$messageStack->output('job_category');
else
 $update_message=$messageStack->output();

 $template->assign_vars(array(
 'HEADING_TITLE'=>HEADING_TITLE,
 'add_save_button'=>$add_save_button,
 'add_next_button'=>$add_next_button,
 'registration_form'=>$registration_form,
 'INFO_TEXT_RESUME_TEXT'=>$resume_text,
 'SECTION_ACCOUNT_RESUME_NAME'=>SECTION_ACCOUNT_RESUME_NAME,
 'SECTION_OBJECTIVE'=>SECTION_OBJECTIVE,
 'SECTION_TARGET_JOB'=>SECTION_TARGET_JOB,
	'SECTION_SOCIAL_ACCOUNT'=> SECTION_SOCIAL_ACCOUNT,
 //'SECTION_TARGET_JOB_LOCATIONS'=>SECTION_TARGET_JOB_LOCATIONS,
 'REQUIRED_INFO'=>REQUIRED_INFO,
 'INFO_TEXT_RESUME_NAME'=>INFO_TEXT_RESUME_NAME,
 'INFO_TEXT_RESUME_NAME1'=>tep_draw_input_field('TR_resume_name', $resume_name,'size="46" class="form-control required"',true),
 'INFO_TEXT_OBJECTIVE'=>INFO_TEXT_OBJECTIVE,
 'INFO_TEXT_OBJECTIVE1'=>tep_draw_textarea_field('objective', 'soft', '60', '5', stripslashes($objective), 'class="form-control h-100"', true, false),


 'INFO_TEXT_JOB_TYPE'=>INFO_TEXT_JOB_TYPE,
 'INFO_TEXT_JOB_TYPE1'=>JOB_TYPE(tep_db_output($job_type)),
 'INFO_TEXT_DESIRED_SALARY'=>INFO_TEXT_DESIRED_SALARY,

 'INFO_TEXT_DESIRED_SALARY1'=> "<div class='col-4'>"
 .LIST_SET_DATA(CURRENCY_TABLE,"",'code','currencies_id',"code",'class="form-select" name="currency" ',TEXT_PLEASE_SELECT,'',$currency)
 ."</div><div class='col-8'>".tep_draw_input_field('expected_salary', $expected_salary,'class="form-control" size="10"',false)
 .'</div>
 
 <div class="1form-check mt-2 1d-flex"><span class="me-3">'
 .tep_draw_radio_field('expected_salary_per', 'Year', '', $expected_salary_per, 'id="expected_salary_per1" class="form-check-input me-2"')
 .'<label class="form-check-label" for="expected_salary_per1">'.INFO_TEXT_RES_YEAR.'</label></span><span class="me-3">'
 .tep_draw_radio_field('expected_salary_per', 'Month', '', $expected_salary_per, 'id="expected_salary_per2" class="form-check-input me-2"')
 .'<label class="form-check-label" for="expected_salary_per2">'.INFO_TEXT_RES_MONTH.'</label></span><span class="me-3">'
 .tep_draw_radio_field('expected_salary_per', 'Hour', '', $expected_salary_per, 'id="expected_salary_per3" class="form-check-input me-2"')
 .'<label class="form-check-label" for="expected_salary_per3">'.INFO_TEXT_RES_HOUR.'</label></span>
 </div>',

 'INFO_TEXT_TARGET_JOB_TITLES'=>INFO_TEXT_TARGET_JOB_TITLES,

 'INFO_TEXT_TARGET_JOB_TITLES1'=>tep_draw_input_field('TR_target_job_titles', $target_job_titles,'class="form-control required" size="46"',true),

 'INFO_TEXT_NATIONALITY'=>INFO_TEXT_NATIONALITY,
 'INFO_TEXT_NATIONALITY1'=>LIST_SET_DATA(COUNTRIES_TABLE,"",TEXT_LANGUAGE.'country_name','id',TEXT_LANGUAGE."country_name","name='TR_nationality' class='form-select' " ,"",'',$nationality),

 'INFO_TEXT_INDUSTRY'=>INFO_TEXT_INDUSTRY,
 'INFO_TEXT_INDUSTRY1'=>get_drop_down_list(JOB_CATEGORY_TABLE,"name='TR_industry_sector[]'class='form-control required h-100' size='5' multiple","","0",$industry_sector),

 //'INFO_TEXT_TARGET_JOB_LOCATIONS'=>INFO_TEXT_TARGET_JOB_LOCATIONS,
 //'INFO_TEXT_TARGET_JOB_LOCATIONS1'=>LIST_SET_DATA(COUNTRIES_TABLE,"",TEXT_LANGUAGE.'country_name','id',TEXT_LANGUAGE."country_name","name='target_country[]' size='10' multiple" ,"",'',$target_country),
 'INFO_TEXT_RELOCATE'=>INFO_TEXT_RELOCATE,
 'INFO_TEXT_RELOCATE1'=>tep_draw_radio_field('relocate', 'Yes', true, $relocate, 'id="radio_relocate1" class="form-check-input me-1" ').'<label class="form-check-label me-3"  for="radio_relocate1">'.INFO_TEXT_YES.'</label>'.tep_draw_radio_field('relocate', 'No', '', $relocate, 'id="radio_relocate2" class="form-check-input me-1"').'<label class="form-check-label me-3" for="radio_relocate2">'.INFO_TEXT_NO.'</label>',

	'INFO_TEXT_FACEBOOK_URL'  => INFO_TEXT_FACEBOOK_URL,
	'INFO_TEXT_FACEBOOK_URL1' => tep_draw_input_field('facebook_url', $facebook_url,'size="60" class="form-control"'),
	'INFO_TEXT_INSTAGRAM_URL'    => INFO_TEXT_INSTAGRAM_URL,
	'INFO_TEXT_INSTAGRAM_URL1'   => tep_draw_input_field('google_url', $instagram_url,'size="60" class="form-control"'),
	'INFO_TEXT_LINKEDIN_URL'  => INFO_TEXT_LINKEDIN_URL,
	'INFO_TEXT_LINKEDIN_URL1' => tep_draw_input_field('linkedin_url', $linkedin_url,'size="60" class="form-control"'),
	'INFO_TEXT_TWITTER_URL'   => INFO_TEXT_TWITTER_URL,
	'INFO_TEXT_TWITTER_URL1'  => tep_draw_input_field('twitter_url', $twitter_url,'size="60" class="form-control"'),
 'INFO_TEXT_JSCRIPT_FILE'=>$jscript_file,
 'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
 'RIGHT_BOX_WIDTH'=>RIGHT_BOX_WIDTH1,
 'LEFT_HTML'=>LEFT_HTML,
  'LEFT_HTML'=>LEFT_HTML_JOBSEEKER,
//  'INFO_TEXT_JOBSEEKER_LEFT'=>((check_login("jobseeker") && tep_not_null($_POST['resume_id']))? JOBSEEKER_RESUME_LEFT:'<td width="19%">'.LEFT_HTML_JOBSEEKER.'</td>'),
 'JOBSEEKER_RESUME_LEFT'=>((check_login("jobseeker") && tep_not_null($_POST['resume_id'])) ? JOBSEEKER_RESUME_LEFT : null),
 'RIGHT_HTML'=>RIGHT_HTML,
 'update_message'=>$update_message));
$template->pparse('resume_step1');
?>