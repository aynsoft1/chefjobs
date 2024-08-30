<?php
/*
************************************************************
**********#	Name				      : Shambhu Prasad Patnaik #********
**********#	Company			    : Aynsoft	Pvt. Ltd.   #***********
**********#	Copyright (c) www.aynsoft.com 2004	 #***********
************************************************************
*/
include_once("include_files.php");
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_JOBSEEKER_VIEW_RESUME);
$template->set_filenames(array('view_resume' => 'view_resume.htm', //resume which display from my resume section
'view_resume1' => 'view_resume1.htm',//prinout
'view_resume2' => 'view_resume2.htm',//  download Resume
'view_resume3' => 'view_resume3.htm',//tell to friend
'view_resume4' => 'view_resume4.htm',//contact to me
'book_mark'    => 'view_resume5.htm',//bookmarks
'view_resume6' => 'view_resume6.htm'//general user
));

include_once(FILENAME_BODY);
$jscript_file=PATH_TO_LANGUAGE.$language."/jscript/".'view_resume.js';

//print_r($_SERVER['HTTP_REFERER']);
//print_r($_SESSION);die();
//die();
$present_company='';
$present_job_title='';
$action=(isset($_GET['action'])?$_GET['action']:'');
$action1 = (isset($_POST['action1']) ? $_POST['action1'] : '');
$show_detail=false;
$from_application=false;
$adminedit=false;
if(check_login('admin'))
{
$adminedit=true;
}

if ($action == 'create-add-comment') {
  $notes = (isset($_POST['private_notes']) ? $_POST['private_notes'] : '');
  $commentResumeId = (isset($_POST['resume_id']) ? $_POST['resume_id'] : '');
  
  $currentUrlSearchId = encode_string("application_id=".$commentResumeId."=application_id");;

  $sql_data_array1 = [
    'private_notes' => $notes,
    'resume_id' => $commentResumeId,
    'recruiter_id' => $_SESSION['sess_recruiterid']
  ];

  // Your code to handle the received data goes here
  if ($row_rating1 = getAnyTableWhereData(JOBSEEKER_RATING_TABLE, " resume_id='" . $commentResumeId . "'", 'rating_id')) {
    tep_db_perform(JOBSEEKER_RATING_TABLE, $sql_data_array1, 'update', "rating_id='" . $row_rating1['rating_id'] . "'");
    $messageStack->add_session('comment updated successfully', 'success');
  } else {
    $messageStack->add_session('comment added successfully', 'success');
    tep_db_perform(JOBSEEKER_RATING_TABLE, $sql_data_array1);
  }

  tep_redirect(FILENAME_JOBSEEKER_VIEW_RESUME.'?query_string='.$currentUrlSearchId);

  return true;
}

#################################################################
if(tep_not_null($_GET['query_string2']))
{
if(!check_login("recruiter"))
{
if(tep_not_null($query_string2))
$_SESSION['REDIRECT_URL']=$_SERVER['REQUEST_URI'];
$messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
tep_redirect(tep_href_link(FILENAME_RECRUITER_LOGIN));
}
}
if(isset($_GET['query_string4']))
{
$resume_id =check_data($_GET['query_string4'],"==","view_resume","search");
$query_string4=encode_string("view_resume==".$resume_id."==search");
$hidden=MESSAGE_JOBSEEKER_PRIVACY;
}
elseif(isset($_GET['query_string6']))
{
$resume_id =check_data($_GET['query_string6'],"==","view_resume_general","search_general");
$query_string6=encode_string("view_resume_general==".$resume_id."==search_general");
$hidden=MESSAGE_JOBSEEKER_PRIVACY;
}
elseif(check_login('recruiter') || ($adminedit==true && !check_login('jobseeker') ))
{
if(isset($_GET['query_string']))///Apply Resume
{
$resume_id =check_data($_GET['query_string'],"=","application_id","application_id");
$from_application=true;
}
else if(isset($_GET['query_string1']))///Resume Search\\\\\\
$resume_id =check_data($_GET['query_string1'],"==","search_id","search");
else if(isset($_GET['query_string2']))//Email Alert\
$resume_id =check_data($_GET['query_string2'],"=","resume_id","resume_id");
$query_string1=encode_string("search_id==".$resume_id."==search");
//if($action=='download')
//////// $show_detail=true;
$hidden=MESSAGE_JOBSEEKER_PRIVACY;
}
else if(check_login("jobseeker"))
{
$show_detail=true;
if(isset($_POST['resume_id']))
$resume_id= $_POST['resume_id'];
else if(isset($_GET['resume']))
$resume_id =check_data($_GET['resume'],"@@@","resume","resume");
else if(isset($_GET['query_string']))
$resume_id =check_data($_GET['query_string'],"@@@","resume_id","resume");
$query_string=encode_string("resume@@@".$resume_id."@@@resume");
$jobseeker_id = $_SESSION['sess_jobseekerid'];
}
else
{
$_SESSION['REDIRECT_URL']=$_SERVER['REQUEST_URI'];
$messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
tep_redirect(tep_href_link(FILENAME_LOGIN));
}
if($check_resume=getAnyTableWhereData(JOBSEEKER_RESUME1_TABLE," resume_id='".$resume_id."'",'jobseeker_id,resume_id'))
{
$resume_id    = $check_resume['resume_id'];
$jobseeker_id = $check_resume['jobseeker_id'];
}
else
{
$messageStack->add_session(MESSAGE_RESUME_ERROR, 'error');
tep_redirect(FILENAME_ERROR);
}

//////////////////////////////////////////
$add_button='';
$referer =explode('?',$_SERVER['HTTP_REFERER']);
$rateBtn = '<a class="btn btn-sm btn-outline-secondary btn-outline-secondary-resume btn-outline-secondary btn-outline-secondary-resume-top-bar align-items-center" 
                 
                data-bs-toggle="collapse" 
                href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
              <i class="bi bi-star me-1" title="Rate this resume"></i>'.TEXT_RATE.'
            </a>';
if(check_login("jobseeker"))
{
$add_button.='<a class="btn btn-outline-secondary btn-outline-secondary-resume btn-outline-secondary btn-outline-secondary-resume-top-bar m-none" href="'.$_SERVER['HTTP_REFERER'].'"><i class="bi bi-arrow-left" title="Back"></i></a>';
$add_button.='<a class="btn btn-outline-secondary btn-outline-secondary-resume btn-outline-secondary btn-outline-secondary-resume-top-bar m-none" href="#" onclick="popUp(\''.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,tep_get_all_get_params().'action=print&resume='.$query_string).'\')"><i class="bi bi-printer me-1" title="Print Resume"></i>'.TEXT_PRINT.'</a>';

}
elseif(check_login("recruiter") && (($referer[0]!=HOST_NAME.FILENAME_RECRUITER_SEARCH_RESUME) && $referer[0]!=''))
{
$add_button.='<a class="btn btn-outline-secondary btn-outline-secondary-resume btn-outline-secondary btn-outline-secondary-resume-top-bar m-none" href="'.tep_href_link(FILENAME_RECRUITER_APPLICANT_TRACKING).'"><i class="bi bi-arrow-left" title="Back"></i></a>';
$add_button.='<a class="btn btn-outline-secondary btn-outline-secondary-resume btn-outline-secondary btn-outline-secondary-resume-top-bar m-none" href="#" onclick="popUp(\''.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,tep_get_all_get_params().'action=print').'\')"><i class="bi bi-printer me-1" title="Print Resume"></i>'.TEXT_PRINT.'</a>';
 $add_button.='<a class="btn btn-outline-secondary btn-outline-secondary-resume btn-outline-secondary btn-outline-secondary-resume-top-bar m-none" href="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,tep_get_all_get_params().'action=download').'" ><i class="bi bi-download me-1" title="Download this resume"></i>'.TEXT_DOWNLOAD.'</a>';
$add_button.='<a class="btn btn-outline-secondary btn-outline-secondary-resume btn-outline-secondary btn-outline-secondary-resume-top-bar" href="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,tep_get_all_get_params().'action=send_to_friend').'"><i class="bi bi-share me-1" title="Share this resume"></i>'.TEXT_SHARE.'</a>';
// $add_button.='<a class="btn btn-outline-secondary btn-outline-secondary-resume btn-outline-secondary btn-outline-secondary-resume-top-bar" href="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,tep_get_all_get_params().'action=contact').'"><i class="bi bi-envelope me-1" title="Contact job seeker"></i>Contact</a>';
$add_button.='<a class="btn btn-outline-secondary btn-outline-secondary-resume btn-outline-secondary btn-outline-secondary-resume-top-bar" 
                    href="'.tep_href_link(FILENAME_EMPLOYER_INTERACTION,"query_type=contact&js=".$jobseeker_id).'">
                    <i class="bi bi-envelope me-1" title="Contact job seeker"></i>'.TEXT_CONTACT.'</a>';
 $add_button.=tep_draw_form('save_form', FILENAME_JOBSEEKER_VIEW_RESUME, tep_get_all_get_params(), 'post', 'class="btn-group"').tep_draw_hidden_field('action1','save_resume').'<button type="submit" class="btn btn-outline-secondary btn-outline-secondary-resume btn-outline-secondary btn-outline-secondary-resume-top-bar m-btlr m-bblr m-radius-none"><i class="bi bi-save me-1" title="Save this resume"></i>'.TEXT_SAVE.'</button></form>';
$add_button.='<a class="btn btn-outline-secondary btn-outline-secondary-resume btn-outline-secondary btn-outline-secondary-resume-top-bar" href="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,tep_get_all_get_params().'action=book_mark').'"><i class="bi bi-box-arrow-up-right me-1" title="Connect with job"></i>'.TEXT_CONNECT.'</a>';
$add_button.=$rateBtn;
}
elseif(check_login("recruiter"))
{
$add_button.=(($_SERVER['HTTP_REFERER']==FILENAME_RECRUITER_SEARCH_RESUME)?tep_draw_form('search_resume', FILENAME_RECRUITER_SEARCH_RESUME,'','post').tep_draw_hidden_field('action','search').'<button type="submit" class="btn btn-outline-secondary btn-outline-secondary-resume btn-outline-secondary btn-outline-secondary-resume-top-bar m-none"><i class="bi bi-arrow-left" title="Back"></i></button></form>':'<a class="btn btn-outline-secondary btn-outline-secondary-resume btn-outline-secondary btn-outline-secondary-resume-top-bar m-none" href="javascript:history.back();"><i class="bi bi-arrow-left" title="Back"></i></a>');
$add_button.='<a class="btn btn-outline-secondary btn-outline-secondary-resume btn-outline-secondary btn-outline-secondary-resume-top-bar m-none" href="#" onclick="popUp(\''.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,tep_get_all_get_params().'action=print').'\')"><i class="bi bi-printer me-1" title="Print Resume"></i>'.TEXT_PRINT.'</a>';
$add_button.='<a class="btn btn-outline-secondary btn-outline-secondary-resume btn-outline-secondary btn-outline-secondary-resume-top-bar" href="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,tep_get_all_get_params().'action=download').'"><i class="bi bi-download me-1" title="Download this resume"></i>'.TEXT_DOWNLOAD.'
</a>';
$add_button.='<a class="btn btn-outline-secondary btn-outline-secondary-resume btn-outline-secondary btn-outline-secondary-resume-top-bar" href=" '.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,tep_get_all_get_params().'action=send_to_friend').'"><i class="bi bi-share me-1" title="Share this resume"></i>'.TEXT_SHARE.'</a>';
$add_button.='<a class="btn btn-outline-secondary btn-outline-secondary-resume btn-outline-secondary btn-outline-secondary-resume-top-bar" href="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,tep_get_all_get_params().'action=contact').'"><i class="bi bi-envelope me-1" title="Contact job seeker"></i>'.TEXT_CONTACT.'</a>';
 $add_button.=tep_draw_form('save_form', FILENAME_JOBSEEKER_VIEW_RESUME, tep_get_all_get_params(), 'post', 'class="btn-group"').tep_draw_hidden_field('action1','save_resume').'<button type="submit" class="btn btn-outline-secondary btn-outline-secondary-resume btn-outline-secondary btn-outline-secondary-resume-top-bar m-btlr m-bblr"><i class="bi bi-save me-1" title="Save this resume"></i>'.TEXT_SAVE.'</button></form>';
$add_button.='<a class="btn btn-outline-secondary btn-outline-secondary-resume btn-outline-secondary btn-outline-secondary-resume-top-bar" href="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,tep_get_all_get_params().'action=book_mark').'"><i class="bi bi-box-arrow-up-right me-1" title="Connect with job"></i>'.TEXT_CONNECT.'</a>';
$add_button.=$rateBtn;
}
elseif(check_login("admin") && (($referer[0]!=HOST_NAME.FILENAME_RECRUITER_SEARCH_RESUME) && $referer[0]!=''))
{
$add_button.='<a class="btn btn-outline-secondary btn-outline-secondary-resume btn-outline-secondary btn-outline-secondary-resume-top-bar m-none" href="javascript:history.back();"><i class="bi bi-arrow-left" title="Back"></i>
</a>';
$add_button.='<a class="btn btn-outline-secondary btn-outline-secondary-resume btn-outline-secondary btn-outline-secondary-resume-top-bar m-none" href="#" onclick="popUp(\''.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,tep_get_all_get_params().'action=print').'\')"><i class="bi bi-printer me-1" title="Print Resume"></i>'.TEXT_PRINT.'</a>';
}
else
{
$add_button.='<a class="btn btn-outline-secondary btn-outline-secondary-resume btn-outline-secondary btn-outline-secondary-resume-top-bar m-none" href="#" onclick="popUp(\''.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,tep_get_all_get_params(array('action','query_string4')).'action=print&query_string4='.$query_string4).'\')"><i class="bi bi-printer me-1" title="Print Resume"></i>'.TEXT_PRINT.'</a>';
}
$add_button.='';
////////////// add statistics to this resume starts///////
if((!tep_not_null($action)) && (!tep_not_null($action1)))
{
if(check_login("recruiter"))
{
if($row_check=getAnyTableWhereData(RESUME_STATISTICS_TABLE,"resume_id='".tep_db_input($resume_id)."' and recruiter_id='".$_SESSION['sess_recruiterid']."'"))
{
  $sql_data_array=array('resume_id'=>$resume_id,
  'viewed'=>($row_check['viewed']+1),
  'recruiter_id'=>$_SESSION['sess_recruiterid']
);
tep_db_perform(RESUME_STATISTICS_TABLE, $sql_data_array, 'update', "resume_id='".$resume_id."' and recruiter_id='".$_SESSION['sess_recruiterid']."'");
}
else
{
$sql_data_array=array('resume_id'=>$resume_id,
'viewed'=>1,
'recruiter_id'=>$_SESSION['sess_recruiterid']
);
tep_db_perform(RESUME_STATISTICS_TABLE, $sql_data_array);
}
//////////////////////////////////////
$today     =date('Y-m-d');
if($row_check1=getAnyTableWhereData(RESUME_STATISTICS_DAY_TABLE," resume_id='".tep_db_input($resume_id)."' and  date='".tep_db_input($today)."'")  )
   {
	  $sql_data_array=array('date'=>$today,
	  'clicked'=>($row_check1['clicked']+1),
	  'resume_id'=>$resume_id);
	  
	 tep_db_perform(RESUME_STATISTICS_DAY_TABLE, $sql_data_array, 'update', "resume_id='".tep_db_input($resume_id)."' and date='".tep_db_input($today)."'  ");
   }
	else
	{
	  $sql_data_array=array('date'=>$today,
	  'viewed'=>1,
	  'clicked'=>1,
	  'resume_id'=>$resume_id);
	  

  	 tep_db_perform(RESUME_STATISTICS_DAY_TABLE, $sql_data_array);
   }
   /////////////////////////////////////////
}
}
////////////// add statistics to this resume ends///////
###############################################################
$table_name   = JOBSEEKER_LOGIN_TABLE." as jl left outer join  ".JOBSEEKER_TABLE."  as j  on (jl.jobseeker_id=j.jobseeker_id) left outer join ".JOBSEEKER_RESUME1_TABLE." as jr  on (j.jobseeker_id=jr.jobseeker_id) left outer join ".JOB_CATEGORY_TABLE." as jc on (jr.job_category=jc.id)";
$fields= "jl.jobseeker_email_address,jobseeker_first_name,jobseeker_middle_name,jobseeker_last_name,jr.jobseeker_nationality,j.jobseeker_address1,j.jobseeker_address2,j.jobseeker_country_id,j.jobseeker_state,j.jobseeker_state_id,j.jobseeker_city,j.jobseeker_zip,j.phone_code,j.phone_country,j.jobseeker_phone,j.jobseeker_mobile,j.jobseeker_work_phone,jr.objective ,jr.job_type_id ,jr.expected_salary, jr.currency ,jr.expected_salary_per ,jr.target_job_titles ,jr.job_category, jc.category_name, jr.relocate ,jr.jobseeker_resume,jr.jobseeker_resume_text,jr.jobseeker_photo,j.jobseeker_privacy, jr.facebook_url, jr.google_url, jr.linkedin_url, jr.twitter_url";
$row=getAnyTableWhereData($table_name," jr.jobseeker_id='".$jobseeker_id."' and jr.resume_id='".$resume_id."'",$fields);
if(isset($_GET['query_string6']))
{
$show_detail=false;
}
elseif(check_login('recruiter'))
{
if($from_application)
{
$show_detail=(($row['jobseeker_privacy']==2 || $row['jobseeker_privacy']==3)?true:false);
}
else
{
$show_detail=(($row['jobseeker_privacy']==3)?true:false);
}
}
elseif(isset($_GET['query_string4']))
{
$show_detail=(($row['jobseeker_privacy']==3)?true:false);
}
//print_r($row);
///////////////////////  RETING //////////////////////////////////////////////////////
// add/edit
if(tep_not_null($action1))
{
if(!check_login('recruiter') && ($adminedit==false))
{
$_SESSION['REDIRECT_URL']=$_SERVER['REQUEST_URI'];
$messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
tep_redirect(tep_href_link(FILENAME_LOGIN));
}
switch($action1)
{
case 'rate_it':
  if(check_login('admin'))
  {
	$adminedit=true;
	$sql_data_array=array('resume_id'=>$resume_id,
	'point'=>tep_db_prepare_input($_POST['rate_it']),
	'admin_rate'=>'Y',
  );
  if($row_rating=getAnyTableWhereData(JOBSEEKER_RATING_TABLE," resume_id='".$resume_id."' and  admin_rate ='Y'",'rating_id'))
  {
	tep_db_perform(JOBSEEKER_RATING_TABLE, $sql_data_array, 'update',"rating_id='".$row_rating['rating_id']."'");
  }
  else
  {
	tep_db_perform(JOBSEEKER_RATING_TABLE, $sql_data_array);
  }
  $messageStack->add_session(MESSAGE_SUCCESS_RATED, 'success');
  tep_redirect(tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,tep_get_all_get_params()));
}
if(check_login('recruiter') && $adminedit==false)
{
  $sql_data_array=array('resume_id'=>$resume_id,
  'recruiter_id'=>$_SESSION['sess_recruiterid'],
  'admin_rate'=>'N',
  'point'=>tep_db_prepare_input($_POST['rate_it']),
  'private_notes'=>tep_db_prepare_input($_POST['private_notes']),
);
if($row_rating=getAnyTableWhereData(JOBSEEKER_RATING_TABLE,"recruiter_id='".$_SESSION['sess_recruiterid']."' and resume_id='".$resume_id."'",'rating_id'))
{
  tep_db_perform(JOBSEEKER_RATING_TABLE, $sql_data_array, 'update',"rating_id='".$row_rating['rating_id']."'");
}
else
{
  tep_db_perform(JOBSEEKER_RATING_TABLE, $sql_data_array);
}
$messageStack->add_session(MESSAGE_SUCCESS_RATED, 'success');
}
tep_redirect(tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,tep_get_all_get_params()));
break;
case 'bookmark_to_job':
$job_id =(int)tep_db_prepare_input($_POST['job_id']);
if(!$check_row=getAnyTableWhereData(JOB_TABLE," job_id='".$job_id ."' and recruiter_id='".$_SESSION['sess_recruiterid']."'",'job_id'))
{
$messageStack->add_session(MESSAGE_ERROR_JOB_NOT_EXIST, 'error');
tep_redirect(tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,tep_get_all_get_params()));
}
if($row=getAnyTableWhereData(APPLICATION_TABLE,"resume_id='".$resume_id."' and jobseeker_id='".$jobseeker_id."' and  job_id='".$job_id."' ","application_id"))
{
$messageStack->add_session(MESSAGE_ERROR_ALREADY_BOOKMARKED, 'error');
tep_redirect(tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,tep_get_all_get_params()));
}
$sql_data_array=array('resume_id'     => $resume_id,
'jobseeker_id'  => $jobseeker_id,
'job_id'        => $job_id,
'source'        => 'search_resume',
'inserted'      => 'now()',
);
tep_db_perform(APPLICATION_TABLE, $sql_data_array);
/////////////////////////////////////////////////////////
if($check_row=getAnytableWhereData(JOB_STATISTICS_TABLE,"job_id='".$job_id."'",'applications'))
{
$sql_data_array=array('job_id'=>$job_id,
'applications'=>($check_row['applications']+1)
);
tep_db_perform(JOB_STATISTICS_TABLE, $sql_data_array, 'update', "job_id='".$job_id."'");
}
else
{
$sql_data_array=array('job_id'=>$job_id,
'applications'=>1
);
tep_db_perform(JOB_STATISTICS_TABLE, $sql_data_array);
}
/////////////////////////////////////////////////////////
if($applicant_id=getAnytableWhereData(APPLICATION_TABLE,"jobseeker_id='".$jobseeker_id."' and job_id='".$job_id."' order by inserted desc limit 0,1",'id,job_id'))
{
$row_round =getAnyTableWhereData(SELECTION_ROUND_TABLE," 1 order by value limit 0,1",'*');
$sql_data_array1=array('application_id'=>$applicant_id['id'],
'cur_status'=>1,
'process_round '=>$row_round['id'],
'inserted'=>'now()',
);
//tep_db_perform(APPLICANT_STATUS_TABLE, $sql_data_array1);
$sql_data_array=array('application_id'=>get_job_enquiry_code($applicant_id['job_id']).'-'.($applicant_id['id']+1000));
tep_db_perform(APPLICATION_TABLE, $sql_data_array, 'update', "id = '" .$applicant_id['id']."'");
}
$messageStack->add_session(MESSAGE_SUCCESS_BOOKMARK, 'success');
tep_redirect(tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,tep_get_all_get_params()));
break;
case 'save_resume':
$sql_data_array=array('resume_id'=>$resume_id,
'recruiter_id'=>$_SESSION['sess_recruiterid'],
'inserted'=>'now()',
);
if(!$row_rating=getAnyTableWhereData(SAVE_RESUME_TABLE,"recruiter_id='".$_SESSION['sess_recruiterid']."' and resume_id='".$resume_id."'",'resume_id'))
tep_db_perform(SAVE_RESUME_TABLE, $sql_data_array);
$messageStack->add_session(MESSAGE_SUCCESS_SAVED, 'success');
tep_redirect(tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,tep_get_all_get_params()));
break;
case 'send':
$query_string4=encode_string("view_resume==".$resume_id."==search");
$recruiter_email   = getAnyTableWhereData(RECRUITER_LOGIN_TABLE," recruiter_id='".$_SESSION['sess_recruiterid']."'",'recruiter_email_address');
$to_name=tep_db_output($_POST['TR_your_friend_full_name']);
$to_email_address=tep_db_output($_POST['TREF_your_friend_email_address']);
$from_email_name=tep_db_output($_POST['TR_your_full_name']);
$from_email_address=tep_db_output($recruiter_email['recruiter_email_address']);
$email_text='<div style="font: normal 12px/17px Verdana, Arial, Helvetica, sans-serif;">'.INFO_TEXT_HI.' <b>'.$to_name.',</b>';
$email_text.='<br>&nbsp;'.INFO_TEXT_YOUR_FRIEND.' <b>'.$from_email_name.'</b> '.INFO_TEXT_HAS_SENT;
$email_text.='<br>&nbsp;'.INFO_TEXT_EMAIL_ADDRESS_IS.' <b>'.$from_email_address.' </b>.';
$email_text.='<br>&nbsp;'.INFO_TEXT_MESSAGE_HIS_HER.'<hr>';
$email_text.='<br>'.INFO_TEXT_RESUME_LINK.'<a style="color:blue;" href="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,'query_string4='.$query_string4).'">'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,'query_string4='.$query_string4).'</a><br><br>';
$email_text.=nl2br(stripslashes($_POST['TR_message']));
$email_text.='</div>';
$TR_message=(stripslashes($_POST['TR_message']));
$subject=tep_db_output($_POST['TR_subject']);
$error =false;
if(!tep_not_null($from_email_name))
{
$error =true;
$messageStack->add(YOUR_NAME_ERROR, 'error');
}
if(!tep_not_null($to_name))
{
$error =true;
$messageStack->add(YOUR_FRIEND_NAME_ERROR, 'error');
}
if(!tep_not_null($to_email_address))
{
$error =true;
$messageStack->add(YOUR_FRIEND_EMAIL_ERROR, 'error');
}
if(!tep_not_null($subject))
{
$error =true;
$messageStack->add(EMAIL_SUBJECT_ERROR, 'error');
}
if(!tep_not_null($TR_message))
{
$error =true;
$messageStack->add(EMAIL_MESSAGE_ERROR, 'error');
}

if(!$error)
{
tep_mail($to_name , $to_email_address, $subject, $email_text, SITE_OWNER, ADMIN_EMAIL);
$messageStack->add_session(MESSAGE_SUCCESS_SEND_LINK, 'success');
tep_redirect(tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,tep_get_all_get_params(array('action'))));
}
else
$action ='send_to_friend';

//$email_text;die();
break;
case 'send1':
$to_name           = tep_db_output($row['jobseeker_first_name'].' '.$row['jobseeker_middle_name'].' '.$row['jobseeker_last_name']);
$to_email_address  = tep_db_output($row['jobseeker_email_address']);
if($company_name   = getAnyTableWhereData(RECRUITER_TABLE.' as r left outer join '.RECRUITER_LOGIN_TABLE.' as rl on (rl.recruiter_id=r.recruiter_id)'," r.recruiter_id='".$_SESSION['sess_recruiterid']."'",'recruiter_company_name,recruiter_email_address'))
$email_text        = tep_db_output(sprintf(INFO_TEXT_DEFALUT,$to_name,$company_name['recruiter_company_name'],$company_name['recruiter_email_address']));
$email_text       .= tep_db_output(nl2br($_POST['TR_message']));
$subject=tep_db_output($_POST['TR_subject']);
tep_mail($to_name , $to_email_address, $subject, $email_text,SITE_OWNER,ADMIN_EMAIL);
$messageStack->add_session(SUCCESS_EMAIL_SENT, 'success');
tep_redirect(tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,tep_get_all_get_params().""));
break;
}
}
if(check_login('admin'))
{
$adminedit=true;
$row_rating=getAnyTableWhereData(JOBSEEKER_RATING_TABLE," resume_id='".$resume_id."' and admin_rate='Y'",'point');
$rate_it_array=array();
for($i=1;$i<=5;$i++)
{
$rate_it_array[]=array("id"=>$i,"text"=>$i);
}
$rate_it_string='';
$rate_it_string.=INFO_TEXT_CURRENT_RATE_IT.'';
$rate_it_string.=tep_draw_pull_down_menu('rate_it', $rate_it_array, tep_not_null($row_rating['point'])?$row_rating['point']:'3', 'class="form-select" style="max-width: 500px;margin: 0 15px;"', false);
$rate_it_string.='';
$rate_it_string.=''.tep_draw_submit_button_field('','Rate this resume','class="btn btn-primary"').'';

}
if(check_login('recruiter') && $adminedit==false)
{
$row_rating=getAnyTableWhereData(JOBSEEKER_RATING_TABLE," recruiter_id='".$_SESSION['sess_recruiterid']."' and resume_id='".$resume_id."'",'point,private_notes');
$rate_it_array=array();
for($i=1;$i<=5;$i++)
{
$rate_it_array[]=array("id"=>$i,"text"=>$i);
}
$rate_it_string.='<div class="form-group row" id="rate_id_div"><label class="col-md-2 text-right">'.INFO_TEXT_CURRENT_RATE_IT.':</label>';
$rate_it_string.='<div class="col-md-10">'.tep_draw_pull_down_menu('rate_it', $rate_it_array, tep_not_null($row_rating['point'])?$row_rating['point']:'3', '', false).'</div></div>';
$rate_it_string.='';
$rate_it_string.='<div class="form-group row" id="rate_id_div"><label class="col-md-2 text-right">'.INFO_TEXT_PRIVATE_NOTES.':</label>';
$rate_it_string.='<div class="col-md-10">'.tep_draw_textarea_field('private_notes', 'soft', '60', '4', tep_not_null($row_rating['private_notes'])?$row_rating['private_notes']:'', '', '',false).'</div></div>';
$rate_it_string.=''.(check_login("recruiter")?tep_draw_submit_button_field('','Add','class="btn btn-primary mt-1 float-right mb-3"'):'').'';
}
$add_sec_header='';
$add_sec_header1='
';
$add_sec_footer='
';

$add_sec_footer1='';

///////////////////////////////////// Attachment /////////////////////////////////////
$attachment_query="select * from ".JOBSEEKER_RESUME1_TABLE." where resume_id='".$resume_id."' ";
$attachment_result = tep_db_query($attachment_query);
$rows=tep_db_num_rows($attachment_result);
$attachment='';
$r_no=1;
while ($row1= tep_db_fetch_array($attachment_result))
{

$resume_directory=get_file_directory($row1['jobseeker_resume'],6);
if(is_file(PATH_TO_MAIN_PHYSICAL_RESUME.$resume_directory.'/'.stripslashes($row1['jobseeker_resume'])))
{
$resume='';
$query_string3 = encode_string("resume_id@@@".$row1['resume_id']."@@@resume");
$resume="<a href='".tep_href_link(FILENAME_JOBSEEKER_RESUME_DOWNLOAD,(tep_not_null($resume_id)?'query_string='.$query_string3:''))."'>".stripslashes(stripslashes(substr($row1['jobseeker_resume'],14)))."</a>";

$attachment1=$resume;
}
$r_no++;
}
if($attachment1!='')
{
$SECTION_DOCUMENT_UPLOAD=$add_sec_header.SECTION_DOCUMENT_UPLOAD.$attachment1.$add_sec_footer;
$SECTION_DOCUMENT_UPLOAD.=$attachment."</table></td></tr></table> ";
}
///////////////////////////////////// Attachment /////////////////////////////////////
///////////////////////////////////// target_job ////////////////////////////////////////////
$target_category=get_category_name_with_parent(get_name_from_table(RESUME_JOB_CATEGORY_TABLE,'job_category_id','resume_id',tep_db_output($resume_id)));
$target_job.='
<div class="row">
<div class="col-md-12">
<div>
<div class="cname"><span class="me-3" style="width: 210px;display: inline-block;">'.INFO_TEXT_TARGET_JOB_TITLES.'</span>:<span class="ms-3 location">'.tep_db_output($row['target_job_titles']).' </span></div>
</div>
<div>
<div class="cname mt-2"><span class="me-3" style="width: 210px;display: inline-block;">'.INFO_TEXT_JOB_TYPE.'</span>:<span class="ms-3 location">'.(tep_not_null($row['job_type_id'])?get_name_from_table(JOB_TYPE_TABLE,TEXT_LANGUAGE.'type_name', 'id',$row['job_type_id']):INFO_TEXT_ANY_TYPE).'</span></div>
</div>
<div>
<div class="cname mt-2"><span class="me-3" style="width: 210px;display: inline-block;">'.INFO_TEXT_INDUSTRY.'</span>:<span class="ms-3 location">'.tep_db_output($target_category).'</span></div>
</div>
</div>
<div class="col-md-12">
<div>
<div class="cname mt-2"><span class="me-3" style="width: 210px;display: inline-block;">'.INFO_TEXT_DESIRED_SALARY.'</span>:<span class="ms-3 location">'.(tep_not_null($row['expected_salary'])? get_name_from_table(CURRENCY_TABLE,'code', 'currencies_id',$row['currency']).' '.tep_db_output($row['expected_salary'].'/'.$row['expected_salary_per']):"--").'</span></div>
</div>';
if($row['relocate']!='')
$target_job.='
<div>
<div class="cname mt-2"><span class="me-3" style="width: 210px;display: inline-block;">'.INFO_TEXT_RELOCATE.'</span>:<span class="ms-3 location">'.tep_db_output($row['relocate']).'</span></div>
</div>
';
if($attachment1!='')
$target_job.='
<div>
<div class="cname mt-2"><span class="me-3" style="width: 210px;display: inline-block;">Attached Resume</span>:<span class="ms-3 location cv-attach">'.$attachment1.'</span></div>
</div>
</div>
</div>
';
if($target_job!='')
{
$SECTION_TARGET_JOB='<div class="table-responsive-sm"><table class="table table-sm border-bottom">';
$SECTION_TARGET_JOB.=$target_job."</table></div>";
if($row['job_type_id']=='' && $row['expected_salary']=='' && $row['expected_salary_per']=='' && $row['target_job_titles']=='' && $target_category=='')
$SECTION_TARGET_JOB='';
}
///////////////////////////////////// target_job ////////////////////////////////////////////
///////////////////////social urls/////////////////////
$social_url='';
if($row['facebook_url']!='')
$social_url.='
<div class="mb-3">
<div><a class="cname" href="'.$row['facebook_url'].'"><i class="bi bi-facebook me-2" style="color:#3b5998;"></i>'.tep_db_output($row['facebook_url']).'</a></div>
</div>';
if($row['google_url']!='')
$social_url.='
<div class="mb-3">
<div><a class="cname" href="'.$row['google_url'].'"><i class="bi bi-google me-2" style="color:#0F9D58;"></i>'.tep_db_output($row['google_url']).'</a></div>
</div>';
if($row['linkedin_url']!='')
$social_url.='
<div class="mb-3">
<div><a class="cname" href="'.$row['linkedin_url'].'"><i class="bi bi-linkedin me-2" style="color:#0A66C2;"></i>'.tep_db_output($row['linkedin_url']).'</a></div>
</div>';
if($row['twitter_url']!='')
$social_url.='
<div class="mb-3">
<div><a class="cname" href="'.$row['twitter_url'].'"><i class="bi bi-twitter me-2" style="color:#00acee;"></i>'.tep_db_output($row['twitter_url']).'</a></div>
</div>
';


if($social_url!='')
{
$SECTION_SOCIAL_URL='<div class="table-responsive-sm"><table class="table table-sm border-bottom">';
$SECTION_SOCIAL_URL.=$social_url."</table></div>";
if($row['facebook_url']=='' && $row['google_url']=='' && $row['linkedin_url']=='' && $row['twitter_url']=='')
	$SECTION_SOCIAL_URL='';
}
///////////////////////////////////////////////////////
////////////////////////// resume_video //////////////////////////

if($video_row=getAnyTableWhereData(JOBSEEKER_RESUME1_TABLE,"resume_id='".$resume_id."'","jobseeker_video"))
{
//	echo $video_row['jobseeker_video'];
if($video_row['jobseeker_video']!='')
{
	$jobseeker_video_link=$video_row['jobseeker_video'];
if (preg_match("/watch\?v=/i",$jobseeker_video_link))
{
  $photo_arr=(explode("watch?v=",(basename($jobseeker_video_link))));
  $photo_vd ='https://img.youtube.com/vi/'.trim($photo_arr[1]).'/2.jpg';
}
elseif (preg_match("#youtu.be/(.*)#i",$jobseeker_video_link,$mat))
$photo_vd ='https://img.youtube.com/vi/'.trim($mat[1]).'/2.jpg';
$vquery_string=encode_string("video_dispaly===".$resume_id."===videoid");
//$video ='<a href="#" onclick=\'popUp1("'.tep_href_link(FILENAME_DISPLAY_VIDEO,"query_string1=".$vquery_string).'")\' ><img width="436" height="273" src="'.$photo_vd.'" alt="" ></a>';

$video='<div class="resume-video"><iframe width="100%" height="550" class="video" src="'.tep_href_link(FILENAME_DISPLAY_VIDEO,"query_string1=".$vquery_string).'" scrolling="no" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen noscrollbars></iframe></div>';

}
}
if($video!='')
{
//$SECTION_DOCUMENT_VIDEO.='<section><iframe src="'.tep_href_link(FILENAME_DISPLAY_VIDEO,"query_string1=".$vquery_string).'" frameBorder="0" width="200" height="120" allowfullscreen ></iframe></section>';
$SECTION_DOCUMENT_VIDEO.='<div>
<div class="row mb-4">
    <div class="col-md-12">
      <div class="cardcard-custom">
        <div class="card-body px-4 vresume">
          <h3 class="resume-heading mb-3" style="font-size:20px;font-weight:bold;border-bottom: 2px solid #ccc;padding: 0 0 5px 0;border-bottom: 2px solid #ccc;padding: 0 0 5px 0;">'.SECTION_DOCUMENT_VIDEO.'</h3>
          '.$video.'
        </div>
      </div>
    </div>
</div>
    </div>
';
// $SECTION_DOCUMENT_VIDEO.=$video;
}
else
$SECTION_DOCUMENT_VIDEO='';
////////////////////////// resume_video //////////////////////////
///////////////////////////////////// objective_details ////////////////////////////////////////////
$total_experience='';
if($objective_row=getAnyTableWhereData(JOBSEEKER_RESUME1_TABLE,"resume_id='".$resume_id."'","objective,experience_year,experience_month"))
{
if($objective_row['objective']!='')
{
$objective='
<h3 class="resume-heading mb-3" style="font-size:20px;font-weight:bold;border-bottom: 2px solid #ccc;padding: 0 0 5px 0;border-bottom: 2px solid #ccc;padding: 0 0 5px 0;">'.INFO_TEXT_OBJECTIVE.'</h3>
<div class="m-0 p-0">'.tep_db_output($objective_row['objective']).'</div></div>
';
}
if($objective_row['experience_year']>0 || $objective_row['experience_month']>0)
{
$experience_string='';
if($objective_row['experience_year']>1)
$experience_string=$objective_row['experience_year'].' '.INFO_TEXT_YEARS;
elseif($objective_row['experience_year']>0)
$experience_string=$objective_row['experience_year'].' '.INFO_TEXT_YEAR;
if($objective_row['experience_month']>1)
$experience_string.=$objective_row['experience_month'].' '.INFO_TEXT_MONTHS;
elseif($objective_row['experience_month']>0)
$experience_string.=$objective_row['experience_month'].' '.INFO_TEXT_MONTH;

$total_experience='
<tr class="">
<th class="" colspan="2" width="40%">'.tep_db_output(INFO_TEXT_WORK_EXPERIENCE).'</th>
<td align="left" colspan="4">'.tep_db_output($experience_string).'</td>
</tr>
';
}

}
if($objective!='')
{
$SECTION_OBJECTIVE='<div class="row mb-3">
    <div class="col-md-12">
    <div class="cardcard-custom">
        <div class="cardbody">
'.$objective.'        </div>
    </div>
</div>
</div>
';
}
else
$SECTION_OBJECTIVE='';

///////////////////////////////////// end professional_details ////////////////////////////////////////////
///////////////////////    Work History  /////////////////////////////////////////////////////
$work_history_query="select * from ".JOBSEEKER_RESUME2_TABLE." where resume_id='".$resume_id."' order by start_year desc ,start_month desc";
$work_history_result = tep_db_query($work_history_query);
$rows=tep_db_num_rows($work_history_result);
$work_history='';
if($rows>0)
{
$work_history.='
<div class="row">
<!--<tr class="table-border-data">
<th class="resume-table-head">'.INFO_TEXT_COMPANY.'</th>
<th class="resume-table-head">'.INFO_TEXT_JOB_TITLE.'</th>
<th class="resume-table-head">'.INFO_TEXT_INDUSTRY.'</th>
<th class="resume-table-head">'.INFO_TEXT_LOCATION.'</th>
<th class="resume-table-head">'.INFO_TEXT_JOB_PERIOD.'</th>
<th class="resume-table-head">'.INFO_TEXT_RELATED_INFO.'</th>
</tr>-->
';
}
$r_no=1;
while ($row1= tep_db_fetch_array($work_history_result))
{
if($row1['start_month'] >0 and  $row1['start_year']>0  )
$start_date=formate_date($row1['start_year'].'-'.$row1['start_month'].'-1',"M Y");
else
$start_date='-';

if($row1['end_month']>0 and  $row1['end_year']>0  )
$end_date=formate_date($row1['end_year'].'-'.$row1['end_month'].'-1',"M Y");
elseif($row1['still_work']=='Yes'  )
{
	$end_date='still working ';
	$present_company=$row1['company'];
	$present_job_title=$row1['job_title'];
}
else
$end_date='-';
$description='';

if(tep_db_output($row1['state_id']) > 0)
$state_display=get_name_from_table(ZONES_TABLE,TEXT_LANGUAGE.'zone_name', 'zone_id',tep_db_output($row1['state_id']));
else
$state_display=tep_db_output($row1['state']);

$work_history.='

<div class="col-md-4">
	<div class="fw-bold mt-3">'.tep_db_output($row1['company']).'</div>
	<div><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person me-2" viewBox="0 0 16 16">
  <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
</svg>'.tep_db_output($row1['job_title']).'</div>
	<div class="small"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-briefcase me-2" viewBox="0 0 16 16">
  <path d="M6.5 1A1.5 1.5 0 0 0 5 2.5V3H1.5A1.5 1.5 0 0 0 0 4.5v8A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-8A1.5 1.5 0 0 0 14.5 3H11v-.5A1.5 1.5 0 0 0 9.5 1h-3zm0 1h3a.5.5 0 0 1 .5.5V3H6v-.5a.5.5 0 0 1 .5-.5zm1.886 6.914L15 7.151V12.5a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5V7.15l6.614 1.764a1.5 1.5 0 0 0 .772 0zM1.5 4h13a.5.5 0 0 1 .5.5v1.616L8.129 7.948a.5.5 0 0 1-.258 0L1 6.116V4.5a.5.5 0 0 1 .5-.5z"/>
</svg>'.get_name_from_table(JOB_CATEGORY_TABLE,TEXT_LANGUAGE.'category_name', 'id',tep_db_output($row1['company_industry'])).'</div>
	<div class="small"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo-alt me-2" viewBox="0 0 16 16">
  <path d="M12.166 8.94c-.524 1.062-1.234 2.12-1.96 3.07A31.493 31.493 0 0 1 8 14.58a31.481 31.481 0 0 1-2.206-2.57c-.726-.95-1.436-2.008-1.96-3.07C3.304 7.867 3 6.862 3 6a5 5 0 0 1 10 0c0 .862-.305 1.867-.834 2.94zM8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10z"/>
  <path d="M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm0 1a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
</svg>'.get_name_from_table(COUNTRIES_TABLE,TEXT_LANGUAGE.'country_name', 'id',tep_db_output($row1['country'])).', '.tep_db_output($row1['city']).'</div>
	<div class="small"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clock me-2" viewBox="0 0 16 16">
  <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/>
  <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z"/>
</svg>'.$start_date.'&nbsp;&nbsp;to&nbsp;&nbsp;'.$end_date.'</div>
	<div class="small">'.($row1['description']!=''?nl2br(tep_db_output($row1['description'])):'').'</div>
</div>

';

$r_no++;
}
tep_db_free_result($work_history_result);
if($work_history!='' || $total_experience!='')
{
$SECTION_WORK_HISTORY_DETAIL.='<div class="row mb-3">
    <div class="col-md-12">
    <div class="cardcard-custom">
        <div class="card-body px-4">
			<h3 class="resume-heading mb-3" style="font-size:20px;font-weight:bold;border-bottom: 2px solid #ccc;padding: 0 0 5px 0;">Work History</h3>

'.$total_experience.$work_history."</div>
</div>
    </div>
</div>
</div>
";
}
///////////////////////    end Work History  /////////////////////////////////////////////////////

///////////////////////////////////// reference_details //////////////////////////////////////////
$reference_query="select * from ".JOBSEEKER_RESUME6_TABLE." where resume_id='".$resume_id."' ";
$reference_result = tep_db_query($reference_query);
$rows=tep_db_num_rows($reference_result);
$reference='';
if($rows>0)
{
$reference='
<div class="row">
<!--<tr class="table-border-data">
<th class="resume-table-head">'.INFO_TEXT_REFERENCE_NAME.' </th>
<th class="resume-table-head">'.INFO_TEXT_COMPANY_NAME.'</th>
<th class="resume-table-head">'.INFO_TEXT_LOCATION.'</th>
<th class="resume-table-head">'.INFO_TEXT_POSITION_TITLE.'</th>
<th class="resume-table-head">'.INFO_TEXT_CONTACT_DETAILS.'</th>
<th class="resume-table-head">'.INFO_TEXT_RELATIONSHIP.'</th>
</tr>-->';
}
$r_no=1;
while ($row1= tep_db_fetch_array($reference_result))
{
$reference.='
<div class="col-md-6">
<div class="fw-bold mt-3"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person me-2" viewBox="0 0 16 16">
  <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
</svg>'.tep_db_output($row1['name']).'</div>
<div><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-building me-2" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M14.763.075A.5.5 0 0 1 15 .5v15a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5V14h-1v1.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V10a.5.5 0 0 1 .342-.474L6 7.64V4.5a.5.5 0 0 1 .276-.447l8-4a.5.5 0 0 1 .487.022zM6 8.694 1 10.36V15h5V8.694zM7 15h2v-1.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 .5.5V15h2V1.309l-7 3.5V15z"/>
  <path d="M2 11h1v1H2v-1zm2 0h1v1H4v-1zm-2 2h1v1H2v-1zm2 0h1v1H4v-1zm4-4h1v1H8V9zm2 0h1v1h-1V9zm-2 2h1v1H8v-1zm2 0h1v1h-1v-1zm2-2h1v1h-1V9zm0 2h1v1h-1v-1zM8 7h1v1H8V7zm2 0h1v1h-1V7zm2 0h1v1h-1V7zM8 5h1v1H8V5zm2 0h1v1h-1V5zm2 0h1v1h-1V5zm0-2h1v1h-1V3z"/>
</svg>'.tep_db_output($row1['company_name']).'</div>
<div><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo-alt me-2" viewBox="0 0 16 16">
  <path d="M12.166 8.94c-.524 1.062-1.234 2.12-1.96 3.07A31.493 31.493 0 0 1 8 14.58a31.481 31.481 0 0 1-2.206-2.57c-.726-.95-1.436-2.008-1.96-3.07C3.304 7.867 3 6.862 3 6a5 5 0 0 1 10 0c0 .862-.305 1.867-.834 2.94zM8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10z"/>
  <path d="M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm0 1a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
</svg>'.get_name_from_table(COUNTRIES_TABLE,TEXT_LANGUAGE.'country_name', 'id',tep_db_output($row1['country'])).'</div>
<div><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-briefcase me-2" viewBox="0 0 16 16">
  <path d="M6.5 1A1.5 1.5 0 0 0 5 2.5V3H1.5A1.5 1.5 0 0 0 0 4.5v8A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-8A1.5 1.5 0 0 0 14.5 3H11v-.5A1.5 1.5 0 0 0 9.5 1h-3zm0 1h3a.5.5 0 0 1 .5.5V3H6v-.5a.5.5 0 0 1 .5-.5zm1.886 6.914L15 7.151V12.5a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5V7.15l6.614 1.764a1.5 1.5 0 0 0 .772 0zM1.5 4h13a.5.5 0 0 1 .5.5v1.616L8.129 7.948a.5.5 0 0 1-.258 0L1 6.116V4.5a.5.5 0 0 1 .5-.5z"/>
</svg>'.tep_db_output($row1['position_title']).'</div>
<div><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-envelope me-1 me-2" viewBox="0 0 16 16">
  <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4Zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2Zm13 2.383-4.708 2.825L15 11.105V5.383Zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741ZM1 11.105l4.708-2.897L1 5.383v5.722Z"/>
</svg><a href="mailto:"'.tep_db_output($row1['email_address']).'">'.tep_db_output($row1['email_address']).'</a></div>
<div><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-telephone me-2" viewBox="0 0 16 16">
  <path d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.568 17.568 0 0 0 4.168 6.608 17.569 17.569 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.678.678 0 0 0-.58-.122l-2.19.547a1.745 1.745 0 0 1-1.657-.459L5.482 8.062a1.745 1.745 0 0 1-.46-1.657l.548-2.19a.678.678 0 0 0-.122-.58L3.654 1.328zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.678.678 0 0 0 .178.643l2.457 2.457a.678.678 0 0 0 .644.178l2.189-.547a1.745 1.745 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.634 18.634 0 0 1-7.01-4.42 18.634 18.634 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877L1.885.511z"/>
</svg>'.tep_db_output($row1['contact_no']).'</div>
<div><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-circle me-2" viewBox="0 0 16 16">
  <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
  <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
</svg>'.tep_db_output($row1['relationship']).'</div>
</div>
';
if($rows!=$r_no)
$reference.='';


$r_no++;
}
tep_db_free_result($reference_result);
if($reference!='')
{
$SECTION_REFERENCE_DETAILS='
<div class="row mb-4">
    <div class="col-md-12">
    <div class="cardcard-custom">
        <div class="card-body px-4"><h3 class="resume-heading" style="font-size:20px;font-weight:bold;border-bottom: 2px solid #ccc;padding: 0 0 5px 0;">'.SECTION_REFERENCE_DETAILS.'</h3>
';
$SECTION_REFERENCE_DETAILS.=$reference."        </div>
    </div>
</div>
</div></div>
";
}
///////////////////////////////////// end reference_details ////////////////////////////////////////////

/////////////////////////////////////EDUCATION_details///////////
$education_query="select * from ".JOBSEEKER_RESUME3_TABLE." where resume_id='".$resume_id."' ";
$education_result = tep_db_query($education_query);
$rows=tep_db_num_rows($education_result);
$education='';
$r_no=1;
if($rows>0)

$education.='<h3 class="resume-heading" style="font-size:20px;font-weight:bold;border-bottom: 2px solid #ccc;padding: 0 0 5px 0;">'.TEXT_EDUCATION.'</h3>
<!--<tr class="table-border-data">
<th class="resume-table-head">'.INFO_TEXT_DEGREE.'dd </th>
<th class="resume-table-head">'.INFO_TEXT_INSTITUTION_NAME.'</th>
<th class="resume-table-head">'.INFO_TEXT_COURSE_DURATION.'</th>
<th class="resume-table-head">'.INFO_TEXT_LOCATION.'</th>
<th class="resume-table-head">'.INFO_TEXT_RELATED_INFO.'</th>
</tr>-->
';

while ($row1= tep_db_fetch_array($education_result))
{
$start_date=$end_date='';
if($row1['start_year']>0 && $row1['start_month']>0)
$start_date  = formate_date(tep_db_output($row1['start_year']).'-'.tep_db_output($row1['start_month']).'-01'," M Y ");
if($row1['end_year']>0 && $row1['end_month']>0)
$end_date  = formate_date(tep_db_output($row1['end_year']).'-'.tep_db_output($row1['end_month']).'-01'," M Y ");

$education.='
<div class="mb-4">
	<div class="fw-bold mt-3">'.get_name_from_table(EDUCATION_LEVEL_TABLE,TEXT_LANGUAGE.'education_level_name', 'id',tep_db_output($row1['degree'])).(tep_not_null($row1['specialization'])?' ('.tep_db_output($row1['specialization']).')':'').'</div>
	<div><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-mortarboard me-2" viewBox="0 0 16 16">
  <path d="M8.211 2.047a.5.5 0 0 0-.422 0l-7.5 3.5a.5.5 0 0 0 .025.917l7.5 3a.5.5 0 0 0 .372 0L14 7.14V13a1 1 0 0 0-1 1v2h3v-2a1 1 0 0 0-1-1V6.739l.686-.275a.5.5 0 0 0 .025-.917l-7.5-3.5ZM8 8.46 1.758 5.965 8 3.052l6.242 2.913L8 8.46Z"/>
  <path d="M4.176 9.032a.5.5 0 0 0-.656.327l-.5 1.7a.5.5 0 0 0 .294.605l4.5 1.8a.5.5 0 0 0 .372 0l4.5-1.8a.5.5 0 0 0 .294-.605l-.5-1.7a.5.5 0 0 0-.656-.327L8 10.466 4.176 9.032Zm-.068 1.873.22-.748 3.496 1.311a.5.5 0 0 0 .352 0l3.496-1.311.22.748L8 12.46l-3.892-1.556Z"/>
</svg>'.(tep_not_null($row1['school'])?tep_db_output($row1['school']):'---').'</div>
	<div class="small"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo-alt me-2" viewBox="0 0 16 16">
  <path d="M12.166 8.94c-.524 1.062-1.234 2.12-1.96 3.07A31.493 31.493 0 0 1 8 14.58a31.481 31.481 0 0 1-2.206-2.57c-.726-.95-1.436-2.008-1.96-3.07C3.304 7.867 3 6.862 3 6a5 5 0 0 1 10 0c0 .862-.305 1.867-.834 2.94zM8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10z"/>
  <path d="M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm0 1a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
</svg>'.get_name_from_table(COUNTRIES_TABLE,TEXT_LANGUAGE.'country_name', 'id',tep_db_output($row1['country'])).','.tep_db_output($row1['city']).'</div>
	<div class="small"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clock me-2" viewBox="0 0 16 16">
  <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/>
  <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z"/>
</svg>'.$start_date.'-'.$end_date.'</div>
	<div class="small"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle me-2" viewBox="0 0 16 16">
  <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
  <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
</svg>'.tep_db_output($row1['related_info']).'</div>
</div>
';
/*if(tep_not_null($row1['related_info']))
$education.= '<tr>
<td  class="resume-table-head" valign="top" width="20%">'.INFO_TEXT_RELATED_INFO.' </td>
<td class="resume_content2" align="left" colspan="3">'.tep_db_output($row1['related_info']).'</td>
</tr>';
$r_no++;
*/
}
tep_db_free_result($education_result);
$education.='';
if($education!='')
{
$SECTION_EDUCATION_DETAILS='<div class="row">
    <div class="col-md-12">
    <div class="cardcard-custom">
        <div class="card-body px-4">'.$education.' </div>
    </div>
</div>
</div>';
}
///////////////////////////////////// end EDUCATION_details ////////////////////////////////////////////



///////////////////////////////////// CUT_PASTE RESUME Print/////////////////////////////////////
$cut_paste_query="select * from ".JOBSEEKER_RESUME1_TABLE." where resume_id='".$resume_id."' ";
$cut_paste_result = tep_db_query($cut_paste_query);
$rows=tep_db_num_rows($cut_paste_result);
$cut_paste_resume='';
$r_no=1;

while ($row1= tep_db_fetch_array($cut_paste_result))
{
$cv_text=stripslashes($row1['jobseeker_resume_text']);
if($row1['jobseeker_resume_text']!='')
{
$cut_paste_resume.='<h3 class="resume-heading" style="font-size:20px;font-weight:bold;margin-bottom:20px;border-bottom: 2px solid #ccc;padding: 0 0 5px 0;">'.PASTED_RESUME.'</h3>
<div class="table-responsive-sm">
'.$cv_text.'
';
}
$r_no++;
}
$cut_paste_resume.='</div>';
if($cut_paste_resume!='')
{
$SECTION_DOCUMENT_UPLOAD_PRINT.='<div class="row">
    <div class="col-md-12">
    <div class="cardcard-custom">
        <div class="card-body px-4">
'.$cut_paste_resume.'        </div>
    </div>
</div>
</div>
';
}
///////////////////////////////////// CUT_PASTE RESUME ///////////
///////////////////////////////////// skills_details///////////
$skills_query="select * from ".JOBSEEKER_RESUME4_TABLE." where resume_id='".$resume_id."' ";
$skills_result = tep_db_query($skills_query);
$rows=tep_db_num_rows($skills_result);
$skills='';
$r_no=1;
if($rows>0)
$skills.='<h3 class="resume-heading" style="font-size:20px;font-weight:bold;border-bottom: 2px solid #ccc;padding: 0 0 5px 0;">Skills</h3>
<div class="row">
<!--<tr class="table-border-data">
<th class="resume-table-head">'.INFO_TEXT_SKILL.' </th>
<th class="resume-table-head">'.INFO_TEXT_SKILL_LEVEL.'</th>
<th class="resume-table-head">'.INFO_TEXT_LAST_USED.'</th>
<th class="resume-table-head">'.INFO_TEXT_YEARS_OF_EXP.'</th>
</tr>-->
';

while ($row1= tep_db_fetch_array($skills_result))
{
$skills.='

<div class="col-md-4">
<div class="fw-bold mt-3"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-code-slash me-2" viewBox="0 0 16 16">
  <path d="M10.478 1.647a.5.5 0 1 0-.956-.294l-4 13a.5.5 0 0 0 .956.294l4-13zM4.854 4.146a.5.5 0 0 1 0 .708L1.707 8l3.147 3.146a.5.5 0 0 1-.708.708l-3.5-3.5a.5.5 0 0 1 0-.708l3.5-3.5a.5.5 0 0 1 .708 0zm6.292 0a.5.5 0 0 0 0 .708L14.293 8l-3.147 3.146a.5.5 0 0 0 .708.708l3.5-3.5a.5.5 0 0 0 0-.708l-3.5-3.5a.5.5 0 0 0-.708 0z"/>
</svg>'.tep_db_output($row1['skill']).'</div>
<div class="small"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-sliders me-2" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M11.5 2a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zM9.05 3a2.5 2.5 0 0 1 4.9 0H16v1h-2.05a2.5 2.5 0 0 1-4.9 0H0V3h9.05zM4.5 7a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zM2.05 8a2.5 2.5 0 0 1 4.9 0H16v1H6.95a2.5 2.5 0 0 1-4.9 0H0V8h2.05zm9.45 4a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zm-2.45 1a2.5 2.5 0 0 1 4.9 0H16v1h-2.05a2.5 2.5 0 0 1-4.9 0H0v-1h9.05z"/>
</svg>'.get_name_from_table(SKILL_LEVEL_TABLE,TEXT_LANGUAGE.'skill_name', 'id',tep_db_output($row1['skill_level'])).'</div>
<div class="small"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clock-history me-2" viewBox="0 0 16 16">
  <path d="M8.515 1.019A7 7 0 0 0 8 1V0a8 8 0 0 1 .589.022l-.074.997zm2.004.45a7.003 7.003 0 0 0-.985-.299l.219-.976c.383.086.76.2 1.126.342l-.36.933zm1.37.71a7.01 7.01 0 0 0-.439-.27l.493-.87a8.025 8.025 0 0 1 .979.654l-.615.789a6.996 6.996 0 0 0-.418-.302zm1.834 1.79a6.99 6.99 0 0 0-.653-.796l.724-.69c.27.285.52.59.747.91l-.818.576zm.744 1.352a7.08 7.08 0 0 0-.214-.468l.893-.45a7.976 7.976 0 0 1 .45 1.088l-.95.313a7.023 7.023 0 0 0-.179-.483zm.53 2.507a6.991 6.991 0 0 0-.1-1.025l.985-.17c.067.386.106.778.116 1.17l-1 .025zm-.131 1.538c.033-.17.06-.339.081-.51l.993.123a7.957 7.957 0 0 1-.23 1.155l-.964-.267c.046-.165.086-.332.12-.501zm-.952 2.379c.184-.29.346-.594.486-.908l.914.405c-.16.36-.345.706-.555 1.038l-.845-.535zm-.964 1.205c.122-.122.239-.248.35-.378l.758.653a8.073 8.073 0 0 1-.401.432l-.707-.707z"/>
  <path d="M8 1a7 7 0 1 0 4.95 11.95l.707.707A8.001 8.001 0 1 1 8 0v1z"/>
  <path d="M7.5 3a.5.5 0 0 1 .5.5v5.21l3.248 1.856a.5.5 0 0 1-.496.868l-3.5-2A.5.5 0 0 1 7 9V3.5a.5.5 0 0 1 .5-.5z"/>
</svg>'.get_name_from_table(SKILL_LAST_USED_TABLE,'skill_last_used', 'id',tep_db_output($row1['last_used'])).'</div>
<div class="small"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-briefcase me-2" viewBox="0 0 16 16">
  <path d="M6.5 1A1.5 1.5 0 0 0 5 2.5V3H1.5A1.5 1.5 0 0 0 0 4.5v8A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-8A1.5 1.5 0 0 0 14.5 3H11v-.5A1.5 1.5 0 0 0 9.5 1h-3zm0 1h3a.5.5 0 0 1 .5.5V3H6v-.5a.5.5 0 0 1 .5-.5zm1.886 6.914L15 7.151V12.5a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5V7.15l6.614 1.764a1.5 1.5 0 0 0 .772 0zM1.5 4h13a.5.5 0 0 1 .5.5v1.616L8.129 7.948a.5.5 0 0 1-.258 0L1 6.116V4.5a.5.5 0 0 1 .5-.5z"/>
</svg>'.tep_db_output($row1['years_of_exp']).' Yrs. Exp.</div>
</div>

';
$r_no++;
}
$skills.='';
tep_db_free_result($skills_result);
if($skills!='')
{
$SECTION_SKILLS='<div class="row mb-4">
    <div class="col-md-12">
    <div class="cardcard-custom">
        <div class="card-body px-4">'.$skills."</div>
    </div>
</div>
</div>

</div>";
}
///////////////////////////////////// end skills_details ////////////////////////////////////////////
///////////////////////////////////// language_details ////////////////////////////////////////////
$language_query="select * from ".JOBSEEKER_RESUME5_TABLE." where resume_id='".$resume_id."' ";
$language_result = tep_db_query($language_query);
$rows=tep_db_num_rows($language_result);
$language='';
$r_no=1;
if($rows>0)
{
$language='
<!--<tr  class="table-border-data">
<th class="resume-table-head">'.INFO_TEXT_LANGUAGE.'</th>
<th class="resume-table-head">'.INFO_TEXT_PROFICIENCY.'</th>
</tr>-->
';

while ($row1= tep_db_fetch_array($language_result))
{
$language.='
<div class="d-flex align-items-center mt-2">
<div class="fw-bold badge" style="width:110px;color: #000!important;background-color: #eee;padding: 9px 0;">'.get_name_from_table(JOBSEEKER_LANGUAGE_TABLE,'name', 'languages_id',tep_db_output($row1['language'])).'</div>
<div class=" text-center" style="width:60px;"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
</svg></div>
<div class="d-flex">'.get_name_from_table(LANGUAGE_PROFICIENCY_TABLE,TEXT_LANGUAGE.'language_proficiency', 'id',tep_db_output($row1['proficiency'])).'</div>
</div>

';
//	if($rows!=$r_no)
//$language.='<tr><td colspan="4"></td></tr>';
$r_no++;
}
}
tep_db_free_result($language_result);
if($language!='')
{
$SECTION_LANGUAGES= '
<div class="row mb-4">
    <div class="col-md-12">
    <div class="cardcard-custom">
        <div class="card-body px-4">

<h3 class="resume-heading mb-3" style="font-size:20px;font-weight:bold;border-bottom: 2px solid #ccc;padding: 0 0 5px 0;">'.SECTION_LANGUAGES.'</h3>
<div class="table-responsive-sm">
<table class="table table-sm">';
$SECTION_LANGUAGES.=$language."</table></div>
        </div>
    </div>
</div>
</div>";
}
///////////////////////////////////// end language_details ////////////////////////////////////////////

$query_string2=encode_string("resume_id".$resume_id."resume_id");
$cat_array=tep_get_categories(JOB_CATEGORY_TABLE);
array_unshift($cat_array,array("id"=>0,"text"=>"All Category"));

/*--------------------left side--------------------------*/
if(check_login("jobseeker"))
{
$search_left_bar='
<!--
<td><div class="search-applicant-box">
'.tep_draw_form('search_job', FILENAME_JOB_SEARCH,'','post').tep_draw_hidden_field('action','search').
tep_draw_input_field('keyword','','placeholder="e.g. Sales Executive" type="text" class="form-control mb-2"',false).
LIST_TABLE(COUNTRIES_TABLE,TEXT_LANGUAGE."country_name","priority","name='country' class='form-control mb-2'","All Locations","",DEFAULT_COUNTRY_ID).
tep_draw_pull_down_menu('job_category[]', $cat_array, '', 'class="form-control mb-2"').'
'.
experience_drop_down('name="experience" class="form-control mb-2"', 'Experience', '', $experience).'
<input type="submit" name="login2" value="search now" class="btn btn-primary btn-block mb-2" />
</form>
<center>
<a href="'.tep_href_link(FILENAME_JOB_SEARCH).'">Advanced search</a>
</center>
</div></td>
-->

'.tep_draw_form('search_job', FILENAME_JOB_SEARCH,'','post').tep_draw_hidden_field('action','search').'
<div class="form-group">
'.tep_draw_input_field('keyword','','placeholder="e.g. Sales Executive" type="text" class="form-control"',false).'
</div>
<div class="form-group">
'.LIST_TABLE(COUNTRIES_TABLE,TEXT_LANGUAGE."country_name","priority","name='country' class='form-control'","All Locations","",DEFAULT_COUNTRY_ID).'
</div>
<div class="form-group">
'.tep_draw_pull_down_menu('job_category[]', $cat_array, '', 'class="form-control"').'
</div>
<div class="form-group">
'.experience_drop_down('name="experience" class="form-control mb-2"', 'Experience', '', $experience).'
</div>
<input type="submit" name="login2" value="search now" class="btn btn-primary btn-block mb-2" />
</form>
<div class="form-group text-center">
<a href="'.tep_href_link(FILENAME_JOB_SEARCH).'">Advanced search</a>
</div>
';
$applicant_tracking='';
}
else
{
$search_left_bar='
'.tep_draw_form('search_resume', FILENAME_RECRUITER_SEARCH_RESUME,'','post').tep_draw_hidden_field('action','search').'
<div class="form-group">
'.tep_draw_input_field('keyword','','placeholder="e.g. Sales Executive" type="text" class="form-control"',false).'
</div>
<div class="form-group">
'.LIST_TABLE(COUNTRIES_TABLE,TEXT_LANGUAGE."country_name","priority","name='country' class='form-control'","All Locations","",DEFAULT_COUNTRY_ID).'
</div>
<div class="form-group">
'.tep_draw_pull_down_menu('industry_sector[]', $cat_array, '', 'class="form-control"').'
</div>
<div class="form-group">
'.experience_drop_down('name="experience" class="form-control mb-2"', 'Experience', '', $experience).'
</div>
<input type="submit" name="login2" value="search now" class="btn btn-primary btn-block mb-2" />
</form>
<div class="form-group text-center">
<a href="'.tep_href_link(FILENAME_JOB_SEARCH).'">Advanced search</a>
</div>';
$applicant_tracking='
<div class="card mt-3 m-none">
<div class="card-header">
Applicant Tracking
</div>
<div class="card-body px-4">
<div><i class="fa fa-angle-right" aria-hidden="true"></i> <a href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_APPLICATIONS).'">All Applicants</a></div>
<div><i class="fa fa-angle-right" aria-hidden="true"></i> <a href="'.tep_href_link(FILENAME_RECRUITER_APPLICATION_REPORT).'">Applicant Pipeline</a></div>
<div><i class="fa fa-angle-right" aria-hidden="true"></i> <a href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_APPLICATIONS).'">Search Applicants</a></div>
<div><i class="fa fa-angle-right" aria-hidden="true"></i> <a href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_SELECTD_APPLICANT).'">Selected Applicants</a></div>
</div>
</div>
';
}
/*--------------------------------------------------------------------*/
/*-----------------------------------------------------------phone display---------------------------------------------*/
$phoneSvg = '<i class="bi bi-phone me-2"></i>';

$all_phone_numbers='';
if(!$show_detail)
{
$all_phone_numbers=''.$hidden.'';
}
else
{
  $phone_code = ($row['phone_code']!='' ? $row['phone_code'].' ' : '');
  $all_phone_numbers .= ($row['jobseeker_phone'] != '' || $row['jobseeker_work_phone'] != '' || $row['jobseeker_mobile'] != '' ? '' : '');
  $all_phone_numbers .= ($row['jobseeker_mobile'] != '' ? '   ' . $phone_code  . tep_db_output($row['jobseeker_mobile']).', ' : '');
  $all_phone_numbers .= ($row['jobseeker_phone'] != '' ? '<i class="bi bi-telephone ms-3 me-2"></i>'.' '.tep_db_output($row['jobseeker_phone']) : '');
  $all_phone_numbers .= ($row['jobseeker_work_phone'] != '' ? ',   ' . tep_db_output($row['jobseeker_work_phone']) : '');
  $all_phone_numbers .= ($row['jobseeker_phone'] != '' || $row['jobseeker_work_phone'] != '' || $row['jobseeker_mobile'] != '' ? '</span>' : '');
}

$resume_views = getAnyTableWhereData(RESUME_STATISTICS_TABLE,"resume_id = '$resume_id'",'viewed');

/*-----------------------------------------------------------------------------------------------------------------------*/

if (check_login('recruiter')) {
	$dwnld_resume = '<a class="btn btn-outline-secondary btn-outline-secondary-resume btn-outline-secondary btn-outline-secondary-resume-top-bar" style="border-top-right-radius: 0px;border-bottom-right-radius: 0px;border-top-left-radius: 12px;border-bottom-left-radius: 12px;" href="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,tep_get_all_get_params().'action=download').'">
							<i class="bi bi-download me-1" title="Download this resume"></i>'.TEXT_DOWNLOAD.'
						</a>';

}

$add_comment_btn = '<a class="btn btn-sm btn-outline-secondary btn-outline-secondary-resume btn-outline-secondary btn-outline-secondary-resume-top-bar align-items-center" 
                                           
                      href="#comment"
                      onclick="addComment('.$resume_id.')" 
                      role="button"> <i class="bi bi-chat-left-text me-1" title="Rate this resume"></i>'.TEXT_COMMENT.'</a>';

$template->assign_vars(array(
'HEADING_TITLE'             => HEADING_TITLE,
// 'RATE_RESUME_BUTTON_FOR_REC'=>(check_login("jobseeker")?'':'<a class="btn btn-sm btn-outline-secondary btn-outline-secondary-resume btn-outline-secondary btn-outline-secondary-resume-top-bar d-flex align-items-center" style="font-size:14px!important;" data-bs-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample"><i class="bi bi-star me-1" title="Rate this resume"></i>'.TEXT_COMMENT.'</a>'),
'RATE_RESUME_BUTTON_FOR_REC'=>(check_login("jobseeker")?'':$add_comment_btn),
'INFO_TEXT_CURRENT_RATING'  =>(check_login('recruiter') || ($adminedit==true)?INFO_TEXT_CURRENT_RATING:'Not Rated'),

'DOWNLOAD_RESUME_BTN'	=> $dwnld_resume,
'RESUME_DOWNLOAD'=> (check_login("recruiter")?'<a class="btn download-btn me-3 mmr-0" href="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,tep_get_all_get_params().'action=download').'" >'.TEXT_DOWNLOAD.'</a>':''),
// 'RESUME_CONTACT'=>(check_login("recruiter")?'<a class="btn download-btn2" href="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,tep_get_all_get_params().'action=contact').'">Contact</a>':''),
'RESUME_CONTACT'=>(check_login("recruiter")
                    ?'<a class="btn download-btn2" 
                        href="'.tep_href_link(FILENAME_EMPLOYER_INTERACTION,"query_type=contact&js=".$jobseeker_id).'">'.TEXT_CONTACT.'</a>'
                    :''),

'SAVE_RESUME_BTN'	=> (check_login("recruiter")) ? tep_draw_form('save_form', FILENAME_JOBSEEKER_VIEW_RESUME, tep_get_all_get_params(), 'post',
						'class="btn-group"').tep_draw_hidden_field('action1','save_resume').'<button type="submit"
						class="btn btn-outline-secondary btn-outline-secondary-resume btn-outline-secondary btn-outline-secondary-resume-top-bar m-btlr m-bblr"><i class="bi bi-save me-1" title="Save this resume"></i>'.TEXT_SAVE.'</button></form>' : '',

							'INFO_TEXT_CURRENT_RATING1' =>(check_login('recruiter') || ($adminedit==true)?(tep_not_null($row_rating['point'])?number_format($row_rating['point'],1):INFO_TEXT_NOT_RATED):''),
'INFO_TEXT_CURRENT_RATE_IT' =>(check_login("recruiter") || ($adminedit==true)?INFO_TEXT_CURRENT_RATE_IT:''),
'INFO_TEXT_CURRENT_RATE_IT1'=>(check_login("recruiter") || ($adminedit==true)?$rate_it_string:''),
'rate_form'=>tep_draw_form('rate_form', FILENAME_JOBSEEKER_VIEW_RESUME, tep_get_all_get_params(), 'post', '').tep_draw_hidden_field('action1','rate_it'),
'comment_start'=>(check_login('recruiter') || ($adminedit==true)?'':'<!--'),
'comment_end'=>(check_login('recruiter') || ($adminedit==true)?'':'-->'),
'SECTION_RATE_RESUME'       => SECTION_RATE_RESUME,
'SECTION_PERSONAL_PROFILE'  => SECTION_PERSONAL_PROFILE,
'add_button'                => $add_button,
'INFO_TEXT_NAME'            => INFO_TEXT_NAME,
'INFO_TEXT_FULL_NAME1'      => tep_db_output($row['jobseeker_first_name'].' '.$row['jobseeker_middle_name'].' '.$row['jobseeker_last_name']),
'INFO_TEXT_TARGET_JOB'=>tep_db_output($row['target_job_titles']),
'INFO_TEXT_NATIONALITY'     => INFO_TEXT_NATIONALITY,
'INFO_TEXT_NATIONALITY1'	 => get_name_from_table(COUNTRIES_TABLE,TEXT_LANGUAGE.'country_name','id',$row['jobseeker_nationality']),
'INFO_TEXT_EMAIL_ADDRESS'   => INFO_TEXT_EMAIL_ADDRESS,
'INFO_TEXT_EMAIL_ADDRESS1'  => ((!$show_detail)?'<span class="small_red">'.$hidden.'</span>':'<a class="text-blue" href="mailto:'.tep_db_output($row['jobseeker_email_address']).'">'.tep_db_output($row['jobseeker_email_address']).'</a></span>'),
'INFO_TEXT_HOME_PHONE'      => INFO_TEXT_HOME_PHONE,
'INFO_TEXT_HOME_PHONE1'     => ((!$show_detail)?'<span class="small_red">'.$hidden.'</span>':tep_db_output($row['jobseeker_phone'])),
'INFO_TEXT_MOBILE'          => INFO_TEXT_MOBILE,
'INFO_TEXT_MOBILE1'         => ((!$show_detail)?'<span class="small_red">'.$hidden.'</span>':(($row['jobseeker_mobile']!='')? $row['jobseeker_mobile'].',  ':'')),
//'INFO_TEXT_WORK_PHONE'      => INFO_TEXT_WORK_PHONE,
'INFO_TEXT_WORK_PHONE1'     => ((!$show_detail)?'<span class="small_red">'.$hidden.'</span>':(($row['jobseeker_work_phone']!='')?tep_db_output($row['jobseeker_work_phone']):'')),
'INFO_TEXT_ALL_PHONE'       => ($all_phone_numbers == '--hidden--') ? '' : $phoneSvg . $all_phone_numbers,//fixPhoneNumber($all_phone_numbers),
'INFO_RESUME_VIEWS'       => ($resume_views) ? 'Resume views: '.$resume_views['viewed'] : '',
'INFO_TEXT_ADDRESS'         => INFO_TEXT_ADDRESS,
'INFO_TEXT_ADDRESS1'        => ((!$show_detail)?'<span class="small_red">'.$hidden.'</span>':tep_db_output($row['jobseeker_address1'].(tep_not_null($row['jobseeker_address2'])?', '.$row['jobseeker_address2']:'').(tep_not_null($row['jobseeker_city'])?', '.$row['jobseeker_city']:'').(tep_not_null($row['jobseeker_zip'])?', '.$row['jobseeker_zip']:'').($row['jobseeker_state_id'] > 0?', '.get_name_from_table(ZONES_TABLE,TEXT_LANGUAGE.'zone_name','zone_id',$row['jobseeker_state_id']):(tep_not_null($row['jobseeker_state'])?', '.$row['jobseeker_state']:'')).(tep_not_null($row['jobseeker_country_id'])?', '.get_name_from_table(COUNTRIES_TABLE,TEXT_LANGUAGE.'country_name','id',$row['jobseeker_country_id']):''))),
'INFO_TEXT_BASE_URL'        => '<base href="'.tep_href_LINK('').'"/>',
'INFO_PRESENT_COMPANY'=>(($present_company=='')?'':'<svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                    height="16" fill="currentColor" class="bi bi-person me-2"
                                                    viewBox="0 0 16 16">
                                                    <path
                                                        d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z" />
                                                </svg>Presently working at '.$present_company).($present_job_title==''?'':' as a '.$present_job_title),
'INFO_TEXT_CATEGORY1'=>$row['category_name'],

'INFO_TEXT_CATEGORY'        => INFO_TEXT_CATEGORY,
//'INFO_TEXT_GRADE'           =>INFO_TEXT_GRADE,
//'INFO_TEXT_SPECIALITY'      =>INFO_TEXT_SPECIALITY,
//'INFO_TEXT_SKILL_SETS'      =>INFO_TEXT_SKILL_SETS,
'INFO_TEXT_JOB_TYPE'        =>INFO_TEXT_JOB_TYPE,
'INFO_TEXT_RELOCATE'        =>INFO_TEXT_RELOCATE,
'INFO_TEXT_AVAILABILITY'    =>INFO_TEXT_AVAILABILITY,


'BASIC_INFO'    =>BASIC_INFO,
'SOCIAL_PROFILE'    =>SOCIAL_PROFILE,




'INFO_TEXT_DEGREE'          =>INFO_TEXT_DEGREE,
//'INFO_TEXT_UNIVERSITY'      =>INFO_TEXT_UNIVERSITY,
//'INFO_TEXT_DEGREE_OBT_DATE' =>INFO_TEXT_DEGREE_OBT_DATE,

//'INFO_TEXT_CERTIFICATE'     =>INFO_TEXT_CERTIFICATE,
//'INFO_TEXT_ISSUED_BY'       =>INFO_TEXT_ISSUED_BY,
//'INFO_TEXT_LICENSE_DATE_OBTAINED'=>INFO_TEXT_LICENSE_DATE_OBTAINED,
//'INFO_TEXT_EXPIRY_DATE'     =>INFO_TEXT_EXPIRY_DATE,

'INFO_TEXT_COUNTRY'         => INFO_TEXT_COUNTRY,
//'INFO_TEXT_BOARD'           => INFO_TEXT_BOARD,
//'INFO_TEXT_DATE_OBTAINED'   => INFO_TEXT_DATE_OBTAINED ,

//'INFO_TEXT_SOCIETY'         => INFO_TEXT_SOCIETY,
'INFO_TEXT_TYPE'            => INFO_TEXT_TYPE,
//'INFO_TEXT_DATE'            => INFO_TEXT_DATE,
'INFO_TEXT_PRINT_RESUME'    =>INFO_TEXT_PRINT_RESUME,

'SECTION_OBJECTIVE'         => $SECTION_OBJECTIVE,
'SECTION_WORK_HISTORY_DETAIL'=> $SECTION_WORK_HISTORY_DETAIL,
'SECTION_REFERENCE_DETAILS' => $SECTION_REFERENCE_DETAILS,
'SECTION_EDUCATION_DETAILS' => $SECTION_EDUCATION_DETAILS,
'SECTION_AFFILIATIONS'      => $SECTION_AFFILIATIONS,
'SECTION_SKILLS'            => $SECTION_SKILLS,
'SECTION_LANGUAGES'         => $SECTION_LANGUAGES,
'SECTION_REFERENCES'        => $SECTION_REFERENCES,
'SECTION_ADDITIONAL_INFO'   => $SECTION_ADDITIONAL_INFO,
'SECTION_SOCIAL_URL'		=> $SECTION_SOCIAL_URL,
'SECTION_TARGET_JOB'        => $SECTION_TARGET_JOB,
'SECTION_TARGET_LOCATIONS'  => $SECTION_TARGET_LOCATIONS,
'SECTION_DOCUMENT_VIDEO'    => $SECTION_DOCUMENT_VIDEO,
'SECTION_DOCUMENT_UPLOAD'   => $SECTION_DOCUMENT_UPLOAD,
'SECTION_DOCUMENT_UPLOAD_PRINT'=> $SECTION_DOCUMENT_UPLOAD_PRINT,
'SCREENER_ANSWER_RESULT' => screener_answer(check_login('recruiter'), $_GET['app_num']),
'SECTION_SOCUMENT_UPLOAD_PR'=> $SECTION_SOCUMENT_UPLOAD_PR,
'INFO_TEXT_RESUME_TEXT1'=>(tep_not_null($row['jobseeker_resume_text'])?nl2br(stripslashes($row['jobseeker_resume_text'])):'Not available.'),
// 'photo'=>(tep_not_null($row['jobseeker_photo']) && is_file(PATH_TO_MAIN_PHYSICAL_PHOTO.$row['jobseeker_photo'])?'<a href="#" onclick="'.js_popup(PATH_TO_PHOTO.$row['jobseeker_photo'],SITE_TITLE).'">'.tep_image(FILENAME_IMAGE."?size=80&image_name=".PATH_TO_PHOTO.$row['jobseeker_photo'],tep_db_output(SITE_TITLE),'','',' class="resume-pic"').'</a>':''),

// 'photo'=>(tep_not_null($row['jobseeker_photo']) && is_file(PATH_TO_MAIN_PHYSICAL_PHOTO.$row['jobseeker_photo'])
// ?tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_PHOTO.$row['jobseeker_photo'],tep_db_output(SITE_TITLE),'','',' class="img-thumbnail mr-3" width="150"')
// :'<img src='.HOST_NAME.'img/nopic.jpg class="img-thumbnail mr-3" width="150">'),

'photo'=>(tep_not_null($row['jobseeker_photo']) && is_file(PATH_TO_MAIN_PHYSICAL_PHOTO.$row['jobseeker_photo'])?tep_image(FILENAME_IMAGE.'?image_name='.PATH_TO_PHOTO.$row['jobseeker_photo'],'','','','class="resume-pic border"'):'<img src='.HOST_NAME.'img/nopic.jpg class="resume-pic">'),


'identification_id'=>(tep_not_null($row['jobseeker_identification_id']) && is_file(PATH_TO_MAIN_PHYSICAL_PHOTO.$row['jobseeker_identification_id'])?'<div style="float:left;text-align:center;padding:1;border:1px solid #ddd" ><a href="#" onclick="'.js_popup(PATH_TO_PHOTO.$row['jobseeker_identification_id'],SITE_TITLE).'">'.tep_image(FILENAME_IMAGE."?size=80&image_name=".PATH_TO_PHOTO.$row['jobseeker_identification_id'],tep_db_output(SITE_TITLE)).'</a><br> Identification ID</div><div style="float:left"> &nbsp;&nbsp;&nbsp;</div> ':''),
'DOWNLOAD_IMAGE'            =>(check_login('recruiter')?'<a class="m-none" href="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,'action=download&query_string1='.$query_string1).'"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-down" title="Download" viewBox="0 0 16 16">
<path fill-rule="evenodd" d="M3.5 10a.5.5 0 0 1-.5-.5v-8a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 .5.5v8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 0 0 1h2A1.5 1.5 0 0 0 14 9.5v-8A1.5 1.5 0 0 0 12.5 0h-9A1.5 1.5 0 0 0 2 1.5v8A1.5 1.5 0 0 0 3.5 11h2a.5.5 0 0 0 0-1h-2z"/>
<path fill-rule="evenodd" d="M7.646 15.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 14.293V5.5a.5.5 0 0 0-1 0v8.793l-2.146-2.147a.5.5 0 0 0-.708.708l3 3z"/>
</svg> '.INFO_TEXT_DOENLOAD_RESUME.'</a>':'&nbsp;'),

'SEARCH'=>(check_login("jobseeker")?'Search Job':'Search Resume'),
'SEARCH_LEFT_BAR'=>$search_left_bar,
'APPLICANT_TRACKING'=>$applicant_tracking,
'ADD_COMMENT_API_URL' => tep_href_link('api/add-comment.php', 'resumeId='),
'ADD_COMMENT_POST_URL' => tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,'action=create-add-comment'),
// 'LEFT_BOX_WIDTH'            => LEFT_BOX_WIDTH1,
// 'RIGHT_BOX_WIDTH'           => RIGHT_BOX_WIDTH1,
'LEFT_HTML'                 => '',
'RIGHT_HTML'                => RIGHT_HTML,
'update_message'            => $messageStack->output()));

if(isset($_GET['query_string6']))
{
$template->pparse('view_resume6');
}
elseif($action=='print')
{
$template->pparse('view_resume1');
}
elseif($action=='download')
{
$file_name=date("YmdHis").randomize(8)."resume.htm";
$handle = fopen(PATH_TO_MAIN_PHYSICAL_DOWNLOAD_RESUME.$file_name, "w");
$string=stripslashes($template->pparse1('view_resume2'));
//echo $string;die();
fwrite($handle, $string);
fclose($handle);
header('Content-Type: application/force-download' );
header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Content-Disposition: attachment; filename="'. substr($file_name,23) . '"');
readfile(PATH_TO_MAIN_PHYSICAL_DOWNLOAD_RESUME.$file_name);
unlink(PATH_TO_MAIN_PHYSICAL_DOWNLOAD_RESUME.$file_name);
}
elseif($action=='book_mark')
{
$today=date("Y-m-d H:i:s");
$template->assign_vars(array(
  'INFO_TEXT_JOB'=>INFO_TEXT_JOB,
  'INFO_TEXT_BOOKMARK_RESUME'=>INFO_TEXT_BOOKMARK_RESUME,
  'INFO_TEXT_JOB1'=>LIST_SET_DATA(JOB_TABLE,"where recruiter_id ='".$_SESSION['sess_recruiterid']."' and re_adv <= '".$today."' and expired >= '".$today."' and deleted is NULL  ",'job_title',"job_id","job_title",'name="job_id" class="form-select"'),
  'form'=>tep_draw_form('book_mark', FILENAME_JOBSEEKER_VIEW_RESUME,tep_get_all_get_params(array('action')),'post', 'onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action1','bookmark_to_job'),
  'button'=>tep_button_submit('btn btn-primary', 'Submit'),
  'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
  'RIGHT_BOX_WIDTH'=>RIGHT_BOX_WIDTH1,
  'LEFT_HTML'=>LEFT_HTML,
  'RIGHT_HTML'=>RIGHT_HTML,
  'update_message'=>$messageStack->output()));
  $template->pparse('book_mark');
}
else if($action=='send_to_friend')
{
  $row=getAnyTableWhereData(RECRUITER_LOGIN_TABLE.' as rl,'.RECRUITER_TABLE.' as r',"rl.recruiter_id='".$_SESSION['sess_recruiterid']."' and rl.recruiter_id=r.recruiter_id","concat(r.recruiter_first_name,' ',r.recruiter_last_name) as name, rl.recruiter_email_address as email_address");
  if(!isset($_POST['TR_your_full_name']))
  $from_email_name=$row['name'];
  $TREF_your_email_address=$row['email_address'];
  $template->assign_vars(array(
	'INFO_TEXT_FROM_NAME'=>INFO_TEXT_FROM_NAME,
	'INFO_TEXT_FROM_NAME1'=>tep_draw_input_field('TR_your_full_name', $from_email_name,'size="40" class="form-control"',true),
	'INFO_TEXT_FROM_EMAIL_ADDRESS'=>INFO_TEXT_FROM_EMAIL_ADDRESS,
	'INFO_TEXT_FROM_EMAIL_ADDRESS1'=>($TREF_your_email_address),
	'INFO_TEXT_TO_NAME'=>INFO_TEXT_TO_NAME,
	'INFO_TEXT_TO_NAME1'=>tep_draw_input_field('TR_your_friend_full_name', $to_name,'size="40" class="form-control"',true),
	'INFO_TEXT_TO_EMAIL_ADDRESS'=>INFO_TEXT_TO_EMAIL_ADDRESS,
	'INFO_TEXT_TO_EMAIL_ADDRESS1'=>tep_draw_input_field('TREF_your_friend_email_address', $to_email_address,'size="40" class="form-control"',true),
	'INFO_TEXT_SUBJECT'=>INFO_TEXT_SUBJECT,
	'INFO_TEXT_SUBJECT1'=>tep_draw_input_field('TR_subject', $subject,'size="40" class="form-control"',true),
	'INFO_TEXT_MESSAGE'=>INFO_TEXT_MESSAGE,
	'INFO_TEXT_MESSAGE1'=>tep_draw_textarea_field('TR_message', 'soft', '70', '12', $TR_message, '', '',false, 'class="form-control"'),
	'form'=>tep_draw_form('send', FILENAME_JOBSEEKER_VIEW_RESUME,tep_get_all_get_params(array('action')),'post', 'onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action1','send'),
	'button'=>tep_button_submit('btn btn-primary', IMAGE_SEND),
	'INFO_TEXT_JSCRIPT_FILE'  =>'<script src="'.$jscript_file.'"></script>' ,
	'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
	'RIGHT_BOX_WIDTH'=>RIGHT_BOX_WIDTH1,
	'LEFT_HTML'=>LEFT_HTML,
	'RIGHT_HTML'=>RIGHT_HTML,
	'update_message'=>$messageStack->output()));
	$template->pparse('view_resume3');
  }
  else if($action=='contact')
  {
	$template->assign_vars(array(
	  'INFO_TEXT_SUBJECT'=>INFO_TEXT_SUBJECT,
	  'INFO_TEXT_SUBJECT1'=>tep_draw_input_field('TR_subject', $TR_subject,'size="40" class="form-control required"',true),
	  'INFO_TEXT_MESSAGE'=>INFO_TEXT_MESSAGE,
	  'INFO_TEXT_MESSAGE1'=>tep_draw_textarea_field('TR_message', 'soft', '70', '12', '', '', '',false,'class="form-control required"'),
	  'form'=>tep_draw_form('send', FILENAME_JOBSEEKER_VIEW_RESUME,tep_get_all_get_params(array('action')),'post', 'onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action1','send1'),
	  'button'=>tep_button_submit('btn btn-primary', IMAGE_SEND),
	  'INFO_TEXT_JSCRIPT_FILE'  =>'<script src="'.$jscript_file.'"></script>' ,
	  // 'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
	  // 'RIGHT_BOX_WIDTH'=>RIGHT_BOX_WIDTH1,
	  'LEFT_HTML'=>LEFT_HTML,
	  'RIGHT_HTML'=>RIGHT_HTML,
	  'update_message'=>$messageStack->output()));
	  $template->pparse('view_resume4');
	}
	else
	{
	  $template->pparse('view_resume');
	}
  function screener_answer($is_recruiter, $application_id)
{
  $element = '';
  $ans_list = '';

  if ($is_recruiter && $application_id) {

    $query = "SELECT * FROM `application_screener` WHERE application_id = '$application_id'";

    $result = tep_db_query($query);

    if ($result && tep_db_num_rows($result) > 0) {
      while ($responseData = tep_db_fetch_array($result)) {
        $question = $responseData['application_ques'];
        $answer = $responseData['application_ans'];
        $ans_list .= "<div class='mt-2'><div><strong>Question:</strong> $question</div>";
        $ans_list .= "<div><strong>Answer:</strong> $answer</div></div>";
      }

      $element .= '<div class="row mb-4">
        <div class="col-md-12">
        <div class="cardcard-custom">
          <div class="card-body px-4">
            <h3 class="resume-heading" style="font-size:20px;font-weight:bold;margin-bottom:20px;border-bottom: 2px solid #ccc;padding: 0 0 5px 0;">Questions answered by candidate </h3>
            ' . $ans_list . '
          </div>
        </div>
        </div></div>';
    }

  }

  return $element;
}
	?>