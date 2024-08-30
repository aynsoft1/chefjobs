<?php
/**********************************************************
**********# Name          : Shambhu Prasad Patnaik  #**********
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
**********************************************************/
include_once("include_files.php");
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_JOBSEEKER_CONTROL_PANEL);
$template->set_filenames(array('control_panel' => 'jobseeker_control_panel.htm'));
include_once(FILENAME_BODY);
if(!check_login("jobseeker"))
{
 $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
 tep_redirect(getPermalink(FILENAME_JOBSEEKER_LOGIN));
}
$no_of_applications=no_of_records(APPLY_TABLE.' as a, '.JOB_TABLE." as j","a.job_id=j.job_id and a.jobseeker_id='".$_SESSION['sess_jobseekerid']."' and jobseeker_apply_status='active'",'a.id');
$no_of_cover_letters=no_of_records(JOBSEEKER_LOGIN_TABLE . " as jl, ".COVER_LETTER_TABLE." as c","jl.jobseeker_id='".$_SESSION['sess_jobseekerid']."' and jl.jobseeker_id=c.jobseeker_id",'c.cover_letter_id');
$no_of_saved_searches=no_of_records(SEARCH_JOB_RESULT_TABLE . " as sr ","sr.jobseeker_id='".$_SESSION['sess_jobseekerid']."'",'sr.id');
$no_of_saved_jobs=no_of_records(SAVE_JOB_TABLE . " as s, ".JOB_TABLE." as j, ".RECRUITER_TABLE." as r, ".RECRUITER_LOGIN_TABLE." as rl","s.jobseeker_id='".$_SESSION['sess_jobseekerid']."' and s.job_id=j.job_id and j.recruiter_id=rl.recruiter_id and j.recruiter_id=r.recruiter_id and rl.recruiter_status='Yes'",'s.id');
$no_of_resumes=no_of_records(JOBSEEKER_RESUME1_TABLE . " as j1","j1.jobseeker_id='".$_SESSION['sess_jobseekerid']."'",'j1.jobseeker_id');
$no_of_unread_mail=no_of_records(APPLICANT_INTERACTION_TABLE." as ai left join ".APPLICATION_TABLE."  as a on (a.id=ai.application_id) ","a.jobseeker_id='".$_SESSION['sess_jobseekerid']."' and ai.receiver_mail_status='active'  and ai.user_see ='No' and  sender_user='recruiter' ",'ai.id');
$no_of_contact=no_of_records(USER_CONTACT_TABLE,"user_id='".$_SESSION['sess_jobseekerid']."' and user_type='jobseeker'",'id');

//*******coding for search status as availablenow***************//
$jobseeker_status=(tep_not_null($_POST['jobseeker_status'])?tep_db_prepare_input($_POST['jobseeker_status']):'');
$action=(tep_not_null($_POST['action'])?tep_db_prepare_input($_POST['action']):'');
//*********//

//////////////////////////////////DISPLAY No OF TIMES RESUME VIEWED ////////////////////////////////////////////////////
$query_view = "select * from ".JOBSEEKER_RESUME1_TABLE. " as jr1 left join ".RESUME_STATISTICS_TABLE." as rs on (jr1.resume_id=rs.resume_id) where jr1.jobseeker_id='".$_SESSION['sess_jobseekerid']."'";
//echo query;
$result_view=tep_db_query($query_view);
$total_view='0';
while($row_view = tep_db_fetch_array($result_view))
{
$total_view=$total_view + $row_view['viewed'];
}
//echo "total=".$total_view;
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$table_names=JOBSEEKER_RESUME1_TABLE." as jr1 ";
$whereClause.="jr1.jobseeker_id='".$_SESSION['sess_jobseekerid']."' order by jr1.inserted desc";
$field_names="jr1.resume_id,jr1.resume_title,jr1.inserted,jr1.updated,jr1.availability_date,jr1.search_status ";//;,sum(rs.viewed) as viewed";

$resume_query_raw="select $field_names from $table_names where $whereClause";
$resume_query = tep_db_query($resume_query_raw);
$resume_query_numrows=tep_db_num_rows($resume_query);
//$available_status='';
if($resume_query_numrows > 0)
{
 while ($resume = tep_db_fetch_array($resume_query))
 {
if($resume_query_numrows > 0)
$jobseeker_status=(tep_not_null($resume['availability_date'])?'Yes':'No');

  /*if(tep_not_null($resume['availability_date']))
  {
   $available_status='<a href="' . tep_href_link(FILENAME_JOBSEEKER_CONTROL_PANEL, 'action=available_inactive') . '">' . tep_image(PATH_TO_IMAGE.'icon_status_red_light.gif', STATUS_NOT_AVAILABLE, 30, 17) . '</a>&nbsp;' . tep_image(PATH_TO_IMAGE.'icon_status_green.gif', STATUS_AVAILABLE, 30, 17);
  }
  else
  {
   $available_status=tep_image(PATH_TO_IMAGE.'icon_status_red.gif', STATUS_NOT_AVAILABLITY, 30, 17) . '&nbsp;<a href="' . tep_href_link(FILENAME_JOBSEEKER_CONTROL_PANEL, 'action=available_active') . '">' . tep_image(PATH_TO_IMAGE.'icon_status_green_light.gif', STATUS_AVAILABLITY, 30, 17) . '</a>';
   break;
  }
*/
 }
}

/*$action=((isset($_GET['action']) && ($_GET['action']=='available_active' || $_GET['action']=='available_inactive'))?$_GET['action']:'');
{
 $action = $_GET['action'] ;
}
*/
if(tep_not_null($action))
{
 switch($action)
 {
  case 'available_active':
  case 'available_inactive':
   if($action=='available_active')
   {
    tep_db_query("update ".JOBSEEKER_RESUME1_TABLE." set availability_date=now() where jobseeker_id='".$_SESSION['sess_jobseekerid']."'");
	tep_db_query("update ".JOBSEEKER_TABLE." set jobseeker_cv_searchable='Yes' where jobseeker_id='".$_SESSION['sess_jobseekerid']."'");
    $messageStack->add_session(MESSAGE_SUCCESS_UPDATED_AVAILABLE, 'success');
   }
   else
   {
    tep_db_query("update ".JOBSEEKER_RESUME1_TABLE." set availability_date=NULL where jobseeker_id='".$_SESSION['sess_jobseekerid']."'");
    tep_db_query("update ".JOBSEEKER_TABLE." set jobseeker_cv_searchable='No' where jobseeker_id='".$_SESSION['sess_jobseekerid']."'");
    $messageStack->add_session(MESSAGE_SUCCESS_UPDATED_NOT_AVAILABLE, 'success');
   }
   tep_redirect(FILENAME_JOBSEEKER_CONTROL_PANEL);
  break;
 }
}
$row_contact=getAnyTableWhereData(JOBSEEKER_LOGIN_TABLE." as jl left join  ".JOBSEEKER_TABLE." as j on (jl.jobseeker_id=j.jobseeker_id) left join  ".COUNTRIES_TABLE." as c on (j.jobseeker_country_id=c.id) left join ".ZONES_TABLE." as z on(j.jobseeker_state_id=z.zone_id or z.zone_id is NULL)"," jl.jobseeker_id ='".$_SESSION['sess_jobseekerid']."'","j.jobseeker_first_name,j.jobseeker_last_name,j.jobseeker_address1,j.jobseeker_address2,c.".TEXT_LANGUAGE."country_name,if(j.jobseeker_state_id,z.".TEXT_LANGUAGE."zone_name,j.jobseeker_state) as location,j.jobseeker_city,j.jobseeker_zip,j.jobseeker_phone,j.jobseeker_mobile,jl.jobseeker_email_address");
$resume_photo_check=getAnyTableWhereData(JOBSEEKER_RESUME1_TABLE,"jobseeker_id='".$_SESSION['sess_jobseekerid']."' and jobseeker_photo!='' ","jobseeker_photo,resume_id");
$photo='';
if(tep_not_null($resume_photo_check['jobseeker_photo']) && is_file(PATH_TO_MAIN_PHYSICAL_PHOTO.$resume_photo_check['jobseeker_photo']))
{
 $photo = tep_image(FILENAME_IMAGE.'?image_name='.PATH_TO_PHOTO.$resume_photo_check['jobseeker_photo'].'','','');
 $query_string=encode_string("resume_id@@@".$resume_photo_check['resume_id']."@@@resume");
 $photo='<div class="no-pic mx-auto mb-3">'.$photo.'</div>
 <a href="'.tep_href_link(FILENAME_JOBSEEKER_RESUME5,'query_string='.$query_string).'" class="small d-block m-edit-pic"><span class="text-blue font-13">'.INFO_TEXT_EDIT_PHOTO.'</span></a>';
}
else
{
 if($resume_photo_check1=getAnyTableWhereData(JOBSEEKER_RESUME1_TABLE,"jobseeker_id='".$_SESSION['sess_jobseekerid']."' order by resume_id desc","resume_id"))
 {
  $query_string=encode_string("resume_id@@@".$resume_photo_check1['resume_id']."@@@resume");
  $photo='<div>
            <!-- <img src="image/no_pic.gif" class=""> -->
            '.defaultProfilePhotoUrl($row_contact['jobseeker_first_name'].' '.$row_contact['jobseeker_last_name'],false,112,'class="no-pic" id=""').'
        </div>
  <div class="dashboard-pic text-center"><a href="'.tep_href_link(FILENAME_JOBSEEKER_RESUME5,'query_string='.$query_string).'" class="mt-2 small d-block" style="color:#0a66c2!important;">'.INFO_TEXT_ADD_PHOTO.'</a></div>';
 }
 else
 {
  $photo='<td  bgcolor="#ffffff" height="19" valign="center" width="12%" align="left">
            <!-- <img src="image/no_pic.gif" class="dashboardlogo"><br> -->
            '.defaultProfilePhotoUrl($row_contact['jobseeker_first_name'].' '.$row_contact['jobseeker_last_name'],false,112,'class="no-pic" id=""').'
            <a href="'.tep_href_link(FILENAME_JOBSEEKER_RESUME1).'" class="small text-muted d-block">'.INFO_TEXT_ADD_PHOTO.'</a>
            </td>';
 }
}
$jobseeker_name= tep_db_output($row_contact['jobseeker_first_name']).' '.tep_db_output($row_contact['jobseeker_last_name']);

///////////////// TOP COMPANIES////////////////////////////////////////////
$now=date('Y-m-d H:i:s');
 $topcfields="count(j.job_id) as totaljobs, r.recruiter_company_name,r.recruiter_logo,r.recruiter_company_seo_name";
 $topctables=JOB_TABLE." as j,".RECRUITER_TABLE." as r,".RECRUITER_LOGIN_TABLE." as rl";
 $topcwhere="j.recruiter_id=rl.recruiter_id and rl.recruiter_id=r.recruiter_id and rl.recruiter_status='Yes'and j.expired >='$now' and j.re_adv <='$now' and j.job_status='Yes' and ( j.deleted is NULL or j.deleted='0000-00-00 00:00:00') ";//

 $topcquery = "select $topcfields from $topctables where $topcwhere GROUP BY j.recruiter_id ORDER BY count(*) desc limit 0,6";
 $topcresult=tep_db_query($topcquery);
//echo $topcquery;
 $x=tep_db_num_rows($topcresult);
//echo $x;//exit;
$topcount=1;
while($topcrow = tep_db_fetch_array($topcresult))
{

  /////logo
 $recruiter_logo='';
 $company_logo=$topcrow['recruiter_logo'];
 if(tep_not_null($company_logo) && is_file(PATH_TO_MAIN_PHYSICAL.PATH_TO_LOGO.$company_logo))
     $recruiter_logo=tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_LOGO.$company_logo."&size=120",'','','','class="featured-logo thumbnail img-responsive img-hover"');
else
     $recruiter_logo=defaultProfilePhotoUrl($topcrow['job_title'],false,55, 'class="featured-logo" ');

    $company_name="<a href='".getPermalink('company',array('seo_name'=>$topcrow["recruiter_company_seo_name"])) ."'  class='blue'><span  class='jobs-by-company-title'>".tep_db_output($topcrow['recruiter_company_name'])."</span></a> ";

if ( $topcrow['totaljobs'] > 0 && $topcrow['totaljobs']<=1 ) {
    $jobs_total = ''.$topcrow['totaljobs'].' Job';
   }elseif($topcrow['totaljobs'] > 1){
    $jobs_total = ''.$topcrow['totaljobs'].' Jobs';
   }else
    $jobs_total = '';


 $template->assign_block_vars('top_company_list', array(
						      'company_logo'	  => $recruiter_logo,
							  'company_name'   => $company_name,
							  'total_jobs' => $jobs_total,
                              ));
 $topcount++;
}

///////////////////// TOP COMPANIES END/////////////////////////////////////

//////////////////////// LATEST JOBS IN MIDDLE AREA ///////////////////
$table_names=JOB_TABLE." as j,".RECRUITER_LOGIN_TABLE.' as rl,'.RECRUITER_TABLE.' as r';
$whereClause="j.recruiter_id=rl.recruiter_id and rl.recruiter_id=r.recruiter_id and rl.recruiter_status='Yes'and j.expired >='$now' and j.re_adv <='$now' and j.job_status='Yes' and ( j.deleted is NULL or j.deleted='0000-00-00 00:00:00') ";//
$field_names="j.job_id, j.job_title, j.job_type, j.job_salary,j.job_featured, j.job_location,j.job_short_description,j.inserted,j.min_experience,j.max_experience, j.re_adv, r.recruiter_company_name,job_country_id,r.recruiter_logo";
$order_by_field_name = "j.inserted";
// $query = "select $field_names from $table_names where $whereClause order by rand() DESC limit 0,6" ;// " . (int) MODULE_THEME_JOBSITE12_MAX_LATEST_JOB;
$query = "select $field_names from $table_names where $whereClause order by $order_by_field_name DESC limit 0,3" ;// " . (int) MODULE_THEME_JOBSITE12_MAX_LATEST_JOB;

//echo "<br>$query";//exit;
$result=tep_db_query($query);
$x=tep_db_num_rows($result);
//echo $x;exit;
$count=1;
while($row = tep_db_fetch_array($result))
{
 $ide=$row["job_id"];
 $title_format=encode_category($row['job_title']);
 $query_string=encode_string("job_id=".$ide."=job_id");

  if(strlen($row['recruiter_company_name']) > 20)
  $company_name_short=	substr($row['recruiter_company_name'],0,15).'..';
 else
  $company_name_short=	substr($row['recruiter_company_name'],0,20);
	$company=$company_name_short;

  /////logo
 $recruiter_logo='';
 $company_logo=$row['recruiter_logo'];
 if(tep_not_null($company_logo) && is_file(PATH_TO_MAIN_PHYSICAL.PATH_TO_LOGO.$company_logo))
     $recruiter_logo=tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_LOGO.$company_logo."&size=120",'','','','class="featured-logo thumbnail img-responsive img-hover"');
else
     $recruiter_logo=defaultProfilePhotoUrl($row['job_title'],false,55, 'class="featured-logo" ');


///////////////
$description=(strlen($row['job_short_description'])>80?substr($row['job_short_description'],0,75).'..':$row['job_short_description']);
if(strlen($row['job_title']) > 30)
  $name_short=	substr($row['job_title'],0,25).'..';
 else
  $name_short=	substr($row['job_title'],0,30);
 $title=' <a class="job_search_title2" href="'.tep_href_link($ide.'/'.$title_format.'.html').'" target="_blank" class="job_search_title2">'.$name_short.'</a>';


$job_posted = tep_date_long(tep_db_output($row['re_adv']));
 $template->assign_block_vars('jobseeker_cpanel_jobs', array(
                              'title'     => $title,
                              'location'  => tep_db_output($row['job_location']) ? ''. tep_db_output($row['job_location']): '',
						       'logo'	    => $recruiter_logo,
                              'job_posted' => $job_posted,
							  'company'   =>$row['recruiter_company_name'],
                              ));
 $count++;
}
//////////////////// LATEST JOB ENDS ///////////////////////////////////////

// no of companies query start
$company_whereClause1=" select distinct(j.recruiter_id) as recruiter_id from ".JOB_TABLE."  as j  where j.expired >='$now' and j.re_adv <='$now' and j.job_status='Yes' and ( j.deleted is NULL or j.deleted='0000-00-00 00:00:00')";
$company_whereClause="where rl.recruiter_status='Yes' and r.recruiter_id in ($company_whereClause1)";
$company_query1 = "select count(r.recruiter_id ) as x1 from ".RECRUITER_TABLE." as r left join ".RECRUITER_LOGIN_TABLE." as rl on ( r.recruiter_id = rl.recruiter_id) ". $company_whereClause;
$company_result=tep_db_query($company_query1);
$tt_row=tep_db_fetch_array($company_result);
$no_of_companies = $tt_row['x1'];
// no of companies query end

$template->assign_vars(array(
 'HEADING_TITLE'=>HEADING_TITLE,
 'LEFT_HTML_JOBSEEKER'=>LEFT_HTML_JOBSEEKER,
 'INFO_TEXT_PHOTO'         => $photo,
	'JOBSEEKER_NAME'          => $jobseeker_name,
 'INFO_TEXT_BRIEFPROFILE'  => INFO_TEXT_BRIEFPROFILE,
 'INFO_TEXT_EMAIL_ADDRESS' => INFO_TEXT_EMAIL_ADDRESS,
 'INFO_TEXT_EMAIL_ADDRESS1'=> tep_db_output($row_contact['jobseeker_email_address']),
 'INFO_TEXT_ADDRESS'       => INFO_TEXT_ADDRESS,
 'INFO_TEXT_ADDRESS1'      => tep_db_output($row_contact['jobseeker_address1'].(tep_not_null($row_contact['jobseeker_address2'])?', '.$row_contact['jobseeker_address2']:'')).(tep_not_null($row_contact['jobseeker_city'])?', '.$row_contact['jobseeker_city']:'').(tep_not_null($row_contact['location'])?', '.$row_contact['location']:'').(tep_not_null($row_contact[TEXT_LANGUAGE.'country_name'])?', '.$row_contact[TEXT_LANGUAGE.'country_name']:''),
 'INFO_TEXT_PHONE'         => INFO_TEXT_PHONE,
 'INFO_TEXT_PHONE1'        => tep_db_output($row_contact['jobseeker_phone']),
 'INFO_TEXT_MOBILE'        => INFO_TEXT_MOBILE,
 'INFO_TEXT_MOBILE1'       => $row_contact["jobseeker_mobile"],
'INFO_TEXT_RESUME_VIEWED'=>INFO_TEXT_RESUME_VIEWED,
'TEXT_ADD_RESUMES'=>TEXT_ADD_RESUMES,
'TOP_COMPANIES'=>TOP_COMPANIES,
'RECOMMENDED_JOBS'=>RECOMMENDED_JOBS,


'TEXT_FEATURED_RESUME'=>TEXT_FEATURED_RESUME,


'INFO_TEXT_RESUME_VIEWED1'=>$total_view,
 'INFO_TEXT_RESUME_MANAGER'=> INFO_TEXT_RESUME_MANAGER,
 'INFO_TEXT_ADD_RESUMES'=>'<a href="'.tep_href_link(FILENAME_JOBSEEKER_RESUME1).'" class="style39">'.INFO_TEXT_ADD_RESUMES.'</a>',
 'INFO_TEXT_LIST_OF_RESUMES'=>'<a href="'.tep_href_link(FILENAME_JOBSEEKER_LIST_OF_RESUMES).'" class="style39">'.INFO_TEXT_LIST_OF_RESUMES.''.(($no_of_resumes>0)?"<span class='badge-round'>".$no_of_resumes."</span></a>":" "),
// 'INFO_TEXT_AVAILABILITY'=>'<a href="'.tep_href_link(FILENAME_JOBSEEKER_CONTROL_PANEL, 'action=available_active').'"  class="style39">'.INFO_TEXT_AVAILABILITY.'</a> '.$available_status,
'INFO_TEXT_AVAILABILITY'=>tep_draw_form('jstatus', FILENAME_JOBSEEKER_CONTROL_PANEL, '', 'post', '').($jobseeker_status=='Yes'?tep_draw_hidden_field('action','available_inactive'):tep_draw_hidden_field('action','available_active')).'
  <i class="fa fa-angle-right icon-page-title" aria-hidden="true">
</i> '.INFO_TEXT_AVAILABILITY.'<label for="checkbox_jsstatus" class="switch">'.tep_draw_checkbox_field('jobseeker_status','Yes','',$jobseeker_status,' class="inputdemo" id="checkbox_jsstatus" onchange="this.form.submit();"').'<span class="slider round"></span></label></form>',

 'INFO_TEXT_JOBS'          =>INFO_TEXT_JOBS,
 'INFO_TEXT_LIST_OF_SAVED_JOBS'=>'<a href="'.tep_href_link(FILENAME_JOBSEEKER_LIST_OF_SAVED_JOBS).'" class="style39">'.INFO_TEXT_LIST_OF_SAVED_JOBS.'</a>'.(($no_of_saved_jobs>0)?' ('.$no_of_saved_jobs.')':' '),
 'INFO_TEXT_LIST_OF_SAVED_JOBS1'=>$no_of_saved_jobs,
 'INFO_TEXT_LIST_OF_APPLICATIONS'=>'<a href="'.tep_href_link(FILENAME_JOBSEEKER_LIST_OF_APPLICATIONS).'" class="style39">'.INFO_TEXT_LIST_OF_APPLICATIONS.''.(($no_of_applications>0)?"<span class='badge-round'>".$no_of_applications."</span></a>":" "),
 'INFO_TEXT_MY_MAILS'=>'<a href="'.tep_href_link(FILENAME_JOBSEEKER_MAILS).'" class="style39">'.INFO_TEXT_MY_MAILS.'</a>'.(($no_of_unread_mail>0)?"(".$no_of_unread_mail.")":""),

'INFO_TEXT_SET_RESUME_STATUS'=>'<a class="style39" href="'.tep_href_link(FILENAME_JOBSEEKER_CONTROL_PANEL, 'action=available_active').'" title="'.INFO_TEXT_SET_STATUS_AS_AVAILALE_NOW.'">'.INFO_TEXT_SET_STATUS_AS_AVAILALE_NOW.'</a> '.$available_status.'',
 'INFO_TEXT_MY_ACCOUNT'=>INFO_TEXT_MY_ACCOUNT,
 'INFO_TEXT_EDIT_PERSONAL_DETAILS'=>'<a href="'.tep_href_link(FILENAME_JOBSEEKER_REGISTER1).'" class="style39">'.INFO_TEXT_EDIT_PERSONAL_DETAILS.'</a>',
 'INFO_TEXT_LIST_OF_COVER_LETTERS'=>'<a href="'.tep_href_link(FILENAME_JOBSEEKER_LIST_OF_COVER_LETTERS).'" class="style39">'.INFO_TEXT_LIST_OF_COVER_LETTERS.'</a>'.(($no_of_cover_letters>0)?" (".$no_of_cover_letters.") ":""),
 'INFO_TEXT_CHANGE_PASSWORD'=>'<a href="'.tep_href_link(FILENAME_JOBSEEKER_CHANGE_PASSWORD).'" class="style39">'.INFO_TEXT_CHANGE_PASSWORD.'</a>',
 'INFO_TEXT_NEWSLETTER'=>'<a href="'.tep_href_link(FILENAME_LIST_OF_NEWSLETTERS).'" class="style39">'.INFO_TEXT_NEWSLETTER.'</a>',
 'INFO_TEXT_CONTACT_LIST'=>'<a href="'.tep_href_link(FILENAME_RECRUITER_CONTACT_LIST).'" class="style39">'.INFO_TEXT_CONTACT_LIST.'</a>'.(($no_of_contact>0)?" (".$no_of_contact.") ":""),
 'INFO_TEXT_VIDEO_RESUME'=>'<a href="'.tep_href_link(FILENAME_JOBSEEKER_LIST_OF_RESUMES).'" class="style39">'.INFO_TEXT_VIDEO_RESUME.'</a>',
 'INFO_TEXT_LOGOUT'=>'<a href="'.tep_href_link(FILENAME_LOGOUT).'" >'.INFO_TEXT_LOGOUT.'</a>',

	'INFO_TEXT_JOIN_FORUM'    =>'<a href="'.tep_href_link(PATH_TO_FORUM).'" class="style39">'.INFO_TEXT_JOIN_FORUM.'</a>',

 'INFO_TEXT_HEADER_JOB_SEARCH'=>INFO_TEXT_HEADER_JOB_SEARCH,
 'INFO_TEXT_JOB_SEARCH'=>'<a href="'.tep_href_link(FILENAME_JOB_SEARCH).'" class="style39">'.INFO_TEXT_JOB_SEARCH.'</a>',
 'INFO_TEXT_SERCH_BY_LOCATION'=>'<a href="'.getPermalink(FILENAME_JOB_SEARCH_BY_LOCATION).'" class="style39">'.INFO_TEXT_SERCH_BY_LOCATION.'</a>',
 'INFO_TEXT_SEARCH_BY_SKILL'=>'<a class="style39" href="'.getPermalink(FILENAME_JOB_SEARCH_BY_SKILL).'" title="'.INFO_TEXT_L_BY_SKILL.'">'.INFO_TEXT_L_BY_SKILL.'</a>',

 'INFO_TEXT_SERCH_BY_CATEGORY'=>'<a href="'.getPermalink(FILENAME_JOB_SEARCH_BY_INDUSTRY).'" class="style39">'.INFO_TEXT_SERCH_BY_CATEGORY.'</a>',
 'INFO_TEXT_COMPANY_PROFILE'=>'<a href="'.getPermalink(FILENAME_JOBSEEKER_COMPANY_PROFILE).'" class="style39">'.INFO_TEXT_COMPANY_PROFILE.''.(($no_of_companies>0)?"<span class='badge-round'>".$no_of_companies."</span></a>":''),
 'INFO_TEXT_LIST_OF_SAVED_SEARCHES'=>'<a href="'.tep_href_link(FILENAME_JOBSEEKER_LIST_OF_SAVED_SEARCHES).'" class="style39">'.INFO_TEXT_LIST_OF_SAVED_SEARCHES.''.(($no_of_saved_searches>0)?"<span class='badge-round'>".$no_of_saved_searches."</span></a>":' '),
 'INFO_TEXT_JOB_ALERT_AGENT'=>'<a href="'.tep_href_link(FILENAME_JOBSEEKER_LIST_OF_SAVED_SEARCHES).'" class="style39">'.INFO_TEXT_JOB_ALERT_AGENT.''.(($no_of_saved_searches>0)?"<span class='badge-round'>".$no_of_saved_searches."</span></a>":''),
 'INFO_TEXT_RESUME_STATISTICS'=>'<a href="'.tep_href_link(FILENAME_RESUME_STATISTICS).'" class="style39">'.INFO_TEXT_RESUME_STATISTICS.'</a>',
 'INFO_TEXT_JOBSEEKER_ORDER_HISTORY'=>'<a href="'.tep_href_link(FILENAME_JOBSEEKER_ACCOUNT_HISTORY_INFO).'" class="style39">'.INFO_TEXT_JOBSEEKER_ORDER_HISTORY.'</a>',
	'INFO_TEXT_JOBS_BY_KEYWORD'=>'<a href="'.getPermalink(FILENAME_JOB_SEARCH).'" class="style39">'.INFO_TEXT_JOBS_BY_KEYWORD.'</a>',
'JOB_ALERT_BOX'=>(JOBSEEKER_MEMBERSHIP=='false'?'':'
						<div class="col-md-12">
                            <div class="">
                                <div class="">
                                    <div class="mb-2">
                                        '.GET_YOUR_RESUME_FEATURED.'
                                    </div>
                                    <a class="d-block" href="'.tep_href_link(FILENAME_JOBSEEKER_RATES).'"><i class="bi bi-cart"></i> '.TEXT_RATE_CARD.'</a>
                                </div>
                            </div>
                        </div>'),
'JOB_ALERT_BOX5'=>(JOBSEEKER_MEMBERSHIP=='false'?'':'<a class="" href="'.tep_href_link(FILENAME_JOBSEEKER_RATES).'"><i class="fa fa-shopping-cart" aria-hidden="true">
                                                </i> '.TEXT_RATE_CARD.'</a>'),
  'TOTAL_NUMBER_OF_RESUMES' => ($no_of_resumes>0) ? $no_of_resumes : '0',
  'TOTAL_APPLICATIONS' => ($no_of_applications>0) ? $no_of_applications : '0',
  'TOTAL_SAVED_JOBS' => ($no_of_saved_jobs>0) ? $no_of_saved_jobs : '0',
  'TOTAL_SAVE_SEARCHES' => ($no_of_saved_searches>0) ? $no_of_saved_searches : '0',
  'RESUME_CARD_VALUE' => '<a href="'.tep_href_link(FILENAME_JOBSEEKER_LIST_OF_RESUMES).'">'.TEXT_RESUMES.'</a>',
  'APPLICATION_CARD_VALUE' => '<a href="'.tep_href_link(FILENAME_JOBSEEKER_LIST_OF_APPLICATIONS).'">'.TEXT_APPLICATIONS.'</a>',
  'SAVED_JOB_CARD_VALUE' => '<a href="'.tep_href_link(FILENAME_JOBSEEKER_LIST_OF_SAVED_JOBS).'">'.INFO_TEXT_LIST_OF_SAVED_JOBS.'</a>',
  'SAVED_SEARCH_CARD_VALUE' => '<a href="'.tep_href_link(FILENAME_JOBSEEKER_LIST_OF_SAVED_SEARCHES).'">'.INFO_TEXT_LIST_OF_SAVED_SEARCHES.'</a>',
  'INFO_TEXT_DASHBOARD'=>'<a href="'.tep_href_link(FILENAME_JOBSEEKER_CONTROL_PANEL).'" class="accordion-button2 collapsed fw-bold drop-padd card-dashboard2">
					<i class="bi bi-speedometer2 me-2"></i> '.HEADING_TITLE.'
				</a>',

  'INFO_TEXT_ARTICLE'    =>'<a href="'.tep_href_link(FILENAME_ARTICLE).'" class="style39">'.TEXT_ARTICLES.'</a>',
  'INFO_TEXT_INTERVIEW'    =>'<a href="'.tep_href_link(FILENAME_JOBSEEKER_MAILS).'" class="style39">'.TEXT_INTERVIEW.'</a>',
   'INFO_TEXT_RATECARD'    =>'<a href="'.tep_href_link(FILENAME_JOBSEEKER_RATES).'" class="style39">'.TEXT_RATE_CARD.'</a>',

    'INFO_LMS'=>((LMS_SETTING=='True')?'<a href="'. tep_href_link(PATH_TO_LMS.LMS_MY_COURSES_FILENAME) . '">'.MY_COURSES.'</a>':''),

    'INFO_TEST'=>((TEST_SETTING=='True')?'<div class="pb-1"><a class="style39" href="'.tep_href_link(PATH_TO_QUIZ.FILENAME_JOBSEEKER_TEST_REPORT).'">'.MY_TESTS.'</a></div>':''),

	'SHOW_ALL_JOBS'=>tep_draw_form('all_jobs', FILENAME_JOB_SEARCH,'','post').tep_draw_hidden_field('action','search').'<div class="mt-3 mb-4 text-center"><a class="text-muted" href="#allJobs" onclick="document.all_jobs.submit()">'.TEXT_SHOW_ALL.'</a></div></form>',

	'MY_APPLICATIONS'=>'<a href="'.tep_href_link(FILENAME_JOBSEEKER_LIST_OF_APPLICATIONS).'" class="btn btn-outline-secondary">'.MY_APPLICATIONS.'</a>',

 'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
 'RIGHT_BOX_WIDTH'=>RIGHT_BOX_WIDTH1,
 'LEFT_HTML'=>LEFT_HTML,
 'RIGHT_HTML'=>RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('control_panel');