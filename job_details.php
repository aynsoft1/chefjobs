<?php
/*
***********************************************************
**********# Name          : SHAMBHU PRASAD PATNAIK   #**********
**********# Company       : Aynsoft                 #**********
**********# Copyright (c) www.aynsoft.com 2004     #**********
***********************************************************
*/

include_once("include_files.php");
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_JOB_DETAILS);
$template->set_filenames(array('job_details' => 'job_details.htm','indeed_job_details' => 'indeed_job_details.htm','job_details1' => 'job_details1.htm'));
include_once(FILENAME_BODY);
/*if(!check_login("jobseeker"))
{
 $_SESSION['REDIRECT_URL']=$_SERVER['REQUEST_URI'];
 $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
 tep_redirect(FILENAME_JOBSEEKER_LOGIN);
}*/

$job_id=tep_db_prepare_input($_GET['query_string']);
if(isset($_GET['query_string1']))
{
 $query_string1=tep_db_prepare_input($_GET['query_string1']);
 $job_id=check_data($query_string1,"=","job_id","job_id");
}

////////////////////////////////////////////
$directoryPath = __DIR__;
if ($handle = opendir($directoryPath)) {
  // Loop through the directory
  while (false !== ($entry = readdir($handle))) {
      // Check if the entry is a directory and starts with 'temp_extract_job_'
      if ($entry !== '.' && $entry !== '..' && is_dir($directoryPath . '/' . $entry) && strpos($entry, 'temp_extract_job_') === 0) {
          // Construct the full path to the directory
          $dirPath = $directoryPath . '/' . $entry;
          
          // Delete the directory and its contents
          deleteDirectory($dirPath);
      }
  }
  closedir($handle);
}
////////////////////////////////////////////
$query_string=encode_string("job_id=".$job_id."=job_id");
//$job_id=check_data($query_string,"=","job_id","job_id");
$now=date('Y-m-d H:i:s');
$table_names=JOB_TABLE." as j left outer join ".JOB_STATISTICS_TABLE." as js on (j.job_id=js.job_id) left outer join  ".RECRUITER_TABLE." as r on (j.recruiter_id=r.recruiter_id ) left outer join  ".RECRUITER_LOGIN_TABLE." as rl on (rl.recruiter_id=r.recruiter_id ) left outer join ".INDEED_JOB_TABLE." as dd on (j.job_id=dd.job_id) left outer join ".ZIP_RECRUITER_JOB_TABLE." as zz on (j.job_id=zz.job_id) left outer join ".USAJOBS_JOB_TABLE." as uj   on (j.job_id=uj.job_id) ";
$where_clause=" j.recruiter_id=r.recruiter_id and r.recruiter_id=rl.recruiter_id and rl.recruiter_status='Yes' and j.job_id='".tep_db_input($job_id)."' and j.expired >='$now' and j.re_adv <='$now' and j.job_status='Yes' and ( j.deleted is NULL or j.deleted='0000-00-00 00:00:00')";
$field_names="r.recruiter_company_name,r.recruiter_featured,r.recruiter_logo,r.recruiter_url,j.job_title,j.job_reference,j.min_experience,j.max_experience,j.job_location,j.job_state,j.uploaded_file,r.recruiter_applywithoutlogin, j.job_state_id, j.job_country_id, re_adv,expired, js.viewed, j.job_salary, j.job_allowance,j.job_industry_sector,j.job_type,j.min_experience,j.max_experience,j.job_description,dd.indeed_id,dd.indeed_url,j.post_url,j.url,job_short_description,j.latitude,j.longitude,j.job_skills,zz.zr_id,zz.zr_url,uj.usajobs_id,usajobs_url";//
if(!$row=getAnyTableWhereData($table_names,$where_clause,$field_names))
{ ///Hack attempt
 $messageStack->add_session(ERROR_JOB_NOT_EXIST, 'error');
 tep_redirect(tep_href_link(FILENAME_ERROR));
}
$title_format=encode_category($row['job_title']);

/*****************************************************************/
   $TR_jb_nl_name=tep_db_prepare_input($_POST['TR_jb_nl_name']);
   $TREF_jb_nl_email=tep_db_prepare_input($_POST['TREF_jb_nl_email']);
   $country=tep_db_prepare_input($_POST['country1']);

/****************************************************************/

//$experience_string=calculate_experience($row['min_experience'],$row['max_experience']);
$action = (isset($_GET['action']) ? $_GET['action'] : '');
//$action = (isset($action_match['1']) ? $action_match['1'] : '');
switch($action)
{
 case "save":
  if(!check_login("jobseeker"))
  {
   $_SESSION['REDIRECT_URL']=$_SERVER['REQUEST_URI'];
   $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
   tep_redirect(tep_href_link(FILENAME_JOBSEEKER_LOGIN));
  }
  $sql_data_array=array('jobseeker_id'=>$_SESSION['sess_jobseekerid'],
                         'job_id'=>$job_id
                        );
  if($row_check=getAnyTableWhereData(SAVE_JOB_TABLE,"jobseeker_id='".$_SESSION['sess_jobseekerid']."' and job_id='".$job_id."'"))
  {
   if($_POST['output']=='ajax')
   die("<div style='color:green;'>Already Saved</div>");
   $messageStack->add_session(ERROR_JOB_ALREADY_SAVED, 'error');
  }
  else
  {
   tep_db_perform(SAVE_JOB_TABLE, $sql_data_array);
   if($_POST['output']=='ajax')
   die("<div style='color:green;'>Job Successfully Saved</div>");
   $messageStack->add_session(SUCCESS_JOB_SAVED, 'success');
  }
  tep_redirect(getPermalink('job',array('ide'=>$job_id,'seo_name'=>$title_format)));
  break;
}


$company_logo=$row['recruiter_logo'];
if(tep_not_null($company_logo) && is_file(PATH_TO_MAIN_PHYSICAL.PATH_TO_LOGO.$company_logo))
{
 /*if(tep_not_null($row['recruiter_url']))
 {
  $photo=tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_LOGO.$company_logo.'&size=200','','','','class="img-thumbnail" width="125"');
  $company_logo='<a href="'.$row['recruiter_url'].'" target="new_site">'.$photo.'</a>';
 }
 else
 {
*/
  $photo=tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_LOGO.$company_logo.'&size=200','','','','class="img-thumbnail resume--result-profile-img"');
  $company_logo='<a href="'.tep_href_link(FILENAME_JOBSEEKER_COMPANY_DETAILS,'query_string='.$query_string).'" title="'.INFO_TEXT_VIEW_COMPANY_PROFILE.'">'.$photo.'</a>';
// }
// $company_logo.='<br><br><a href="#" onclick="'.js_popup(PATH_TO_LOGO.$row['recruiter_logo'],SITE_TITLE).'"><span class="footer"> [ '.INFO_TEXT_CLICK_HERE_ENLARGE.'</a> ]</span></a>&nbsp;&nbsp;';
}
else
{
  $photo=tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_IMG."nologo.jpg".'&size=200','','','','class="employer-logo"');
  $company_logo='<a href="'.tep_href_link(FILENAME_JOBSEEKER_COMPANY_PROFILE).'">'.$photo.'</a>';
}

$recruiter_company_name=tep_db_output($row['recruiter_company_name']);
$recruiter_company_name1=tep_db_output($row['recruiter_company_name']);
if($row['recruiter_featured']=='Yes')
{
 $recruiter_company_name='<a href="'.tep_href_link(FILENAME_JOBSEEKER_COMPANY_DETAILS,'query_string='.$query_string).'">'.$recruiter_company_name.'</a>';
}
if(tep_date_long(tep_db_output($row['updated']))=='')
$last_modified=tep_date_long(tep_db_output($row['re_adv']));
else
$last_modified=tep_date_long(tep_db_output($row['updated']));
if($row['post_url']=='Yes')
{
 $post_url=trim($row['url']);
 if(substr($post_url,0,4)!='http')
 $post_url='http://'.$post_url;
	$button_apply='<input type="button" name="apply" value="Apply Now" class="btn btn-primary" onclick="location.href=\''.$post_url.'\'">';
}
elseif(check_login("jobseeker"))
{
 if($applicant_id=getAnytableWhereData(APPLICATION_TABLE,"jobseeker_id='".$_SESSION['sess_jobseekerid']."' and job_id='".$job_id."' order by inserted desc limit 0,1",'id'))
//	$button_apply='<td>'.tep_image_button(PATH_TO_BUTTON.'re_apply.gif',"Re-Apply Now",'onclick="location.href=\''.tep_href_link(FILENAME_APPLY_NOW,'query_string='.$query_string).'\'"').'</td>';
	$button_apply='<span class="btn btn-sm btn-success"><i class="bi bi-check-lg"></i> Already Applied</span>';
  else
	$button_apply='<input type="button" name="apply" value="Apply Now" class="btn btn-lg btn-primary" onclick="location.href=\''.tep_href_link(FILENAME_APPLY_NOW,'query_string='.$query_string).'\'">';
}
else
$button_apply='<input type="button" name="apply" value="'.LOGIN_TO_APPLY.'" class="btn btn-primary mb-2 me-2 m-btn-block" onclick="location.href=\''.tep_href_link(FILENAME_APPLY_NOW,'query_string='.$query_string).'\'">
						<input type="button" name="apply" value="'.REGISTER_AND_APPLY.'" class="btn btn-outline-secondary mb-2 m-btn-block me-2" onclick="location.href=\''.tep_href_link(FILENAME_JOBSEEKER_REGISTER1,'job='.$query_string).'\'">'.($row['recruiter_applywithoutlogin']=='Yes'?
'<input type="button" name="apply" value="Apply without login" class="btn btn-outline-secondary mb-2 ml-2 m-btn-block" onclick="location.href=\''.tep_href_link(FILENAME_APPLY_NOLOGIN,'query_string='.$query_string).'\'">':'');



$job_category_ids=get_name_from_table(JOB_JOB_CATEGORY_TABLE,'job_category_id','job_id',tep_db_output($job_id));

if(tep_not_null($row['indeed_id']))
{
 $template->assign_vars(array(
                               //'INFO_TEXT_JOB_DETAILS1'=>'<div id="outteriframe"><iframe src="'.$row['indeed_url'].'" scrolling="no" id="inneriframe"></iframe></div>',
                               'INFO_TEXT_JOB_DETAILS1'=>nl2br(stripslashes($row['job_short_description'])).'<div align="right" ><a href="'.$row['indeed_url'].'" target="_blank" class="red">More >></a></div><br>',
								'indeedlogo'=>'<img src='.HOST_NAME.PATH_TO_IMG.'nologo.jpg height="120">',
                                'button'=>'<span id="job_detail_back"><a href="javascript:history.back();">'.INFO_TEXT_BACK.'</a>&nbsp;|&nbsp;</span><a href="'.getPermalink('job',array('ide'=>$job_id,'seo_name'=>$title_format,'other'=>'action=save')).'">'.INFO_TEXT_SAVE.'</a>&nbsp;|&nbsp;<a href="'.tep_href_link(FILENAME_TELL_TO_FRIEND,'query_string='.$query_string).'">'.INFO_TEXT_TELL_TO_FRIEND.'</a>',
                              ));
}
elseif(tep_not_null($row['zr_id']))
{
$button_apply='<input type="button" name="apply" value="Apply Now" class="btn btn-primary" onclick="location.href=\''.$row['zr_url'].'\'">';

 $template->assign_vars(array(
                               //'INFO_TEXT_JOB_DETAILS1'=>'<div id="outteriframe"><iframe src="'.$row['indeed_url'].'" scrolling="no" id="inneriframe"></iframe></div>',
                               'INFO_TEXT_JOB_DETAILS1'=>nl2br(stripslashes($row['job_description'])).'<div align="right" ><a href="'.$row['zr_url'].'" target="_blank" class="red">More >></a></div><br>',
                               'button'=>'<span id="job_detail_back"><a href="javascript:history.back();">'.INFO_TEXT_BACK.'</a>&nbsp;|&nbsp;</span><a href="'.getPermalink('job',array('ide'=>$job_id,'seo_name'=>$title_format,'other'=>'action=save')).'">'.INFO_TEXT_SAVE.'</a>&nbsp;|&nbsp;<a href="'.tep_href_link(FILENAME_TELL_TO_FRIEND,'query_string='.$query_string).'">'.INFO_TEXT_TELL_TO_FRIEND.'</a>',
                              ));
}
elseif(tep_not_null($row['usajobs_id']))
{
$button_apply='<input type="button" name="apply" value="Apply Now" class="btn btn-primary" onclick="location.href=\''.$row['usajobs_url'].'\'">';

 $template->assign_vars(array(
                               //'INFO_TEXT_JOB_DETAILS1'=>'<div id="outteriframe"><iframe src="'.$row['indeed_url'].'" scrolling="no" id="inneriframe"></iframe></div>',
                               'INFO_TEXT_JOB_DETAILS1'=>nl2br(stripslashes($row['job_description'])).'<div align="right" ><a href="'.$row['usajobs_url'].'" target="_blank" class="red">More >></a></div><br>',
                               'button'=>'<span id="job_detail_back"><a href="javascript:history.back();">'.INFO_TEXT_BACK.'</a>&nbsp;|&nbsp;</span><a href="'.getPermalink('job',array('ide'=>$job_id,'seo_name'=>$title_format,'other'=>'action=save')).'">'.INFO_TEXT_SAVE.'</a>&nbsp;|&nbsp;<a href="'.tep_href_link(FILENAME_TELL_TO_FRIEND,'query_string='.$query_string).'">'.INFO_TEXT_TELL_TO_FRIEND.'</a>',
                              ));
}
else
{
 $template->assign_vars(array(
   'INFO_TEXT_JOB_DETAILS1'=>nl2br(stripslashes($row['job_description'])),
   'button'=>'<a class="btn" href="'.getPermalink('job',array('ide'=>$job_id,'seo_name'=>$title_format,'other'=>'action=save')).'"><svg xmlns="http://www.w3.org/2000/svg" title="Save this job" width="16" height="16" fill="currentColor" class="bi bi-save" viewBox="0 0 16 16">
    <path d="M2 1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H9.5a1 1 0 0 0-1 1v7.293l2.646-2.647a.5.5 0 0 1 .708.708l-3.5 3.5a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L7.5 9.293V2a2 2 0 0 1 2-2H14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h2.5a.5.5 0 0 1 0 1H2z"/>
  </svg></a>

   <a class="btn" href="'.tep_href_link(FILENAME_TELL_TO_FRIEND,'query_string='.$query_string).'"><i class="bi bi-people"></i></a>

   <a class="btn" href="#" onclick="popUp(\''. getPermalink('job',array('ide'=>$job_id,'seo_name'=>$title_format,'other'=>'action=print')) .'\')"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer" viewBox="0 0 16 16">
    <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
    <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/>
  </svg></a>',
 ));
}

/*
$marker_location=(tep_not_null($row['job_location'])
                  ?tep_db_output($row['job_location']).', '
                  :'').(($row['job_state_id'] > 0)
                  ?get_name_from_table(ZONES_TABLE,TEXT_LANGUAGE.'zone_name','zone_id',tep_db_output($row['job_state_id'])).", "
                  :((tep_db_output($row['job_state']!='')
                  ?tep_db_output($row['job_state']).", "
                  :''))).get_name_from_table(COUNTRIES_TABLE,'country_name','id',tep_db_output($row['job_country_id']));*/
$marker_location = (($row['job_state_id'] > 0)
                  ?get_name_from_table(ZONES_TABLE,TEXT_LANGUAGE.'zone_name','zone_id',tep_db_output($row['job_state_id'])).", "
                  :((tep_db_output($row['job_state']!='')
                  ?tep_db_output($row['job_state']).", "
                  :''))).get_name_from_table(COUNTRIES_TABLE,'country_name','id',tep_db_output($row['job_country_id']));
$map_java_script='';
$show_map='';
   if($row['latitude']!='' && $row['longitude']!='')
   {

    $map_java_script='<script language="JavaScript">'."\n".'<!-- '."\n".'function initialize()
	 {
      var mapDiv = document.getElementById(\'map-canvas\');
      mapDiv.style.height="300px";
	  map = new google.maps.Map(mapDiv, {center: new google.maps.LatLng('.$row['latitude'].', '.$row['longitude'].'),zoom:7,mapTypeId: google.maps.MapTypeId.ROADMAP});
	  infoWindow = new google.maps.InfoWindow();
	  google.maps.event.addListenerOnce(map, \'tilesloaded\', addMarkers);
	  }'."\n";

	 $map_java_script.='setMarkers('.$row['latitude'].','.$row['longitude'].',\''.($marker_location).'\',\'map_content\')'."\n";
     $map_java_script.='//-->'."\n".'</script>'."\n";
     $show_map='<div id="map_content" style="display:none;"><b>'.$marker_location.'</b><ul><li>'.tep_db_output($row['job_title']).'</li></ul></b></div>';
$show_map.='<SCRIPT LANGUAGE="JavaScript">
<!--
initialize();
//-->
</SCRIPT>';
   }

/*----------------------------------------------------------------*/
		$job_skills = $row['job_skills'];
		$final_skills='';
		if(tep_not_null($row['job_skills']))
		{
		 $job_skills1=explode(",",$job_skills);
		 $count_job_skills=count($job_skills1);
			 for($i=0;$i<$count_job_skills;$i++)
			 {
				//$final_skills.='<span class="skills-small">'.$job_skills1[$i].'</span>';
				$final_skills.=getSkillTagLink ($job_skills1[$i],' Jobs');
			 }
		}
		else
			$final_skills='Not Mentioned';
$job_viewed=$row['viewed'];

$uploaded_file=tep_db_query("select uploaded_file from jobs where job_id=$job_id");
$uploaded_row=tep_db_fetch_array($uploaded_file);
if(!empty($uploaded_row['uploaded_file'])){
  $fileName = $row['uploaded_file'];
  $fileNameCmps = explode(".", $fileName);
  $fileExtension = $fileNameCmps[1];
  $uploadFileDir = 'post_job_doc/';
  $dest_path = $uploadFileDir . $fileName;
}
// echo $fileName ?  $fileName : 'No available';

////*** curency display coding ***********/
$row_cur=getAnyTableWhereData(CURRENCY_TABLE,"code ='".DEFAULT_CURRENCY."'",'symbol_left,symbol_right');
$sym_left=(tep_not_null($row_cur['symbol_left'])?$row_cur['symbol_left'].' ':'');
$sym_rt=(tep_not_null($row_cur['symbol_right'])?' '.$row_cur['symbol_right']:'');
//////**********currency display ***************************/

$jobPostedRelativeDate = new Relative_Date($row['re_adv']);

$template->assign_vars(array(
 'HEADING_TITLE'=>HEADING_TITLE,
 'INFO_TEXT_PAGE_HEADING_TITLE'=>'<title>'.SITE_TITLE.' - '.HEADING_TITLE.' - '.tep_db_output($row['job_title']).'</title>',
 //'button'=>'<a href="javascript:history.back();">'.tep_image_button(PATH_TO_BUTTON.'button_back.gif', IMAGE_BACK).'</a>&nbsp;&nbsp;<a href="'.tep_href_link(FILENAME_APPLY_NOW,'query_string='.$query_string).'">'.tep_image_button(PATH_TO_BUTTON.'button_apply_now.gif', IMAGE_APPLY_NOW).'</a>&nbsp;&nbsp;<a href="'.tep_href_link(FILENAME_JOB_DETAILS,'query_string='.$query_string.'&action=save').'">'.tep_image_button(PATH_TO_BUTTON.'button_save.gif', IMAGE_SAVE).'</a>&nbsp;&nbsp;<a href="'.tep_href_link(FILENAME_TELL_TO_FRIEND,'query_string='.$query_string).'">'.tep_image_button(PATH_TO_BUTTON.'button_tell_to_friend.gif', IMAGE_TELL_TO_FRIEND).'</a>&nbsp;&nbsp;<a href="#" onclick="popUp(\''.tep_href_link(FILENAME_JOB_DETAILS,'query_string='.$query_string.'&action=print').'\')">'.tep_image_button(PATH_TO_BUTTON.'button_print.gif', IMAGE_PRINT).'</a>',
// 'button'=>'<a href="javascript:history.back();">'.INFO_TEXT_BACK.'</a>&nbsp;|&nbsp;<a href="'.tep_href_link($job_id.'/'.$title_format.'-action-save.html').'">'.INFO_TEXT_SAVE.'</a>&nbsp;|&nbsp;<a href="'.tep_href_link(FILENAME_TELL_TO_FRIEND,'query_string='.$query_string).'">'.INFO_TEXT_TELL_TO_FRIEND.'</a>&nbsp;|&nbsp;<a href="#" onclick="popUp(\''.tep_href_link($job_id.'/'.$title_format.'-action-print.html').'\')">'.INFO_TEXT_PRINT.'</a>',
 'INFO_TEXT_APPLY_NOW'=>INFO_TEXT_APPLY_NOW,
 'button_apply'=>$button_apply,
  'INFO_TEXT_NAME1'    => tep_draw_input_field('jb_nl_name', $jb_nl_name ,'class="form-control" placehlder"Enter Name"',false),
  'INFO_TEXT_EMAIL1'   => tep_draw_input_field('jb_nl_email', $jb_nl_email ,'class="form-control" placehlder"Enter Email Address"',false),
 // 'INFO_TEXT_MESSAGE1' => tep_draw_textarea('jb_nl_msg', $jb_nl_msg ,'class="form-control-withoutlogin" placehlder"Enter Name"',false),
'form_nologin'=>tep_draw_form('apply_nologin', FILENAME_JOB_DETAILS,'','post'),

 //'button_apply'=>'<a href="'.tep_href_link(FILENAME_APPLY_NOW,'query_string='.$query_string).'">'.tep_image_button(PATH_TO_BUTTON.'button_apply_now_new.gif', IMAGE_APPLY_NOW).'</a>',
 'company_logo'=>$company_logo,

  'INFO_TEXT_JOB_VIEWED'=>$job_viewed,
  'job_search_form'=>tep_draw_form('search_job', FILENAME_JOB_SEARCH,'','post').tep_draw_hidden_field('action','search'),
  'keyword_search'=>tep_draw_input_field('keyword','','placeholder="e.g. Sales Executive" type="text" class="form-control mb-2"',false),
  'location_search'=>LIST_TABLE(COUNTRIES_TABLE,TEXT_LANGUAGE."country_name","priority","name='country' class='form-control mb-2'","All Locations","",DEFAULT_COUNTRY_ID),
  'search_button'=>'<input type="submit" name="login2" id="login2" value="search now" class="btn btn-primary btn-block mb-2" />',
  'advance_search'=>'<a href="'.getPermalink(FILENAME_JOB_SEARCH).'">Advanced search</a>',
   'jobs_by_category'=>'<a href="'.getPermalink(FILENAME_JOB_SEARCH_BY_INDUSTRY).'" title="Jobs by Category">'.INFO_TEXT_L_BY_CATEGORY.'</a>',
   'jobs_by_companies'=> '<a href="'.getPermalink(FILENAME_JOBSEEKER_COMPANY_PROFILE).'" title="'.INFO_TEXT_L_BY_COMPANY.'">'.INFO_TEXT_L_BY_COMPANY.'</a>',//.(($no_of_companies>0)?"(".$no_of_companies.")":''),
  'jobs_by_location'=>'<a href="'.getPermalink(FILENAME_JOB_SEARCH_BY_LOCATION).'"  title="'.INFO_TEXT_L_BY_LOCATION.'">'.INFO_TEXT_L_BY_LOCATION.'</a>',
   'jobs_by_map'=> (GOOGLE_MAP=='true'?'<div><i class="fa fa-angle-right" aria-hidden="true"></i> <a href="'.tep_href_link(FILENAME_JOB_BY_MAP).'" title="'.INFO_TEXT_L_BY_MAP.'">'.INFO_TEXT_L_BY_MAP.'</a></div>':''),
   'jobs_by_skill'=> '<a href="'.tep_href_link(FILENAME_JOB_SEARCH_BY_SKILL).'" title="'.INFO_TEXT_L_BY_SKILL.'">'.INFO_TEXT_L_BY_SKILL.'</a>',
   'one_week'=> tep_draw_form('week1_form', FILENAME_JOB_SEARCH,'','post').tep_draw_hidden_field('action','search').tep_draw_hidden_field('job_post_day','7').'<a href="#" onclick="document.week1_form.submit()"  title="'.INFO_TEXT_LAST_ONE_WEEK.'">'.INFO_TEXT_LAST_ONE_WEEK.'</a></form>',
   'two_week'=> tep_draw_form('week2_form', FILENAME_JOB_SEARCH,'','post').tep_draw_hidden_field('action','search').tep_draw_hidden_field('job_post_day','14').'<a href="#" onclick="document.week2_form.submit()"  title="'.INFO_TEXT_LAST_TWO_WEEKS.'">'.INFO_TEXT_LAST_TWO_WEEKS.'</a></form>',
   'three_week'=> tep_draw_form('week3_form', FILENAME_JOB_SEARCH,'','post').tep_draw_hidden_field('action','search').tep_draw_hidden_field('job_post_day','21').'<a href="#" onclick="document.week3_form.submit()"  title="'.INFO_TEXT_LAST_THREE_WEEKS.'">'.INFO_TEXT_LAST_THREE_WEEKS.'</a></form>',
   'one_month'=> tep_draw_form('week4_form', FILENAME_JOB_SEARCH,'','post').tep_draw_hidden_field('action','search').tep_draw_hidden_field('job_post_day','30').'<a href="#" onclick="document.week4_form.submit()"  title="'.INFO_TEXT_LAST_ONE_MONTH.'">'.INFO_TEXT_LAST_ONE_MONTH.'</a></form>',

 'INFO_TEXT_JOB_TITLE'=>INFO_TEXT_JOB_TITLE,
 'UPLOADED_DOC'=>UPLOADED_DOC,
 'INFO_TEXT_JOB_TITLE1'=>tep_db_output($row['job_title']),
 'INFO_TEXT_COMPANY_NAME'=>INFO_TEXT_COMPANY_NAME,
 'INFO_TEXT_COMPANY_NAME1'=>'<a class="text-blue" href="'.tep_href_link(FILENAME_JOBSEEKER_COMPANY_DETAILS,'query_string='.$query_string).'" title="'.INFO_TEXT_VIEW_COMPANY_PROFILE.'">'.$recruiter_company_name.'</a>',
 'INFO_TEXT_JOB_REFERENCE'=>(($row['job_reference']=='')?'':INFO_TEXT_JOB_REFERENCE),
 'INFO_TEXT_JOB_REFERENCE1'=>(($row['job_reference']=='')?'':tep_db_output($row['job_reference'])),
 'INFO_TEXT_JOB_REF_DOT'=>(($row['job_reference']=='')?'':':'),
 'INFO_TEXT_JOB_LOCATION'=>INFO_TEXT_JOB_LOCATION,
 'INFO_TEXT_JOB_LOCATION1'=>$marker_location,//(tep_not_null($row['job_location'])?tep_db_output($row['job_location']).', ':'').(($row['job_state_id'] > 0)?get_name_from_table(ZONES_TABLE,TEXT_LANGUAGE.'zone_name','zone_id',tep_db_output($row['job_state_id'])).", ":((tep_db_output($row['job_state']!='')?tep_db_output($row['job_state']).", ":''))).get_name_from_table(COUNTRIES_TABLE,'country_name','id',tep_db_output($row['job_country_id'])),
 'INFO_TEXT_JOB_POSTED'=>INFO_TEXT_JOB_POSTED,
//  'INFO_TEXT_JOB_POSTED1'=>tep_date_short(tep_db_output($row['re_adv'])),
 'INFO_TEXT_JOB_POSTED1'=> $jobPostedRelativeDate->relative_formatted_date,
 'INFO_TEXT_JOB_MODIFIED'=>INFO_TEXT_JOB_MODIFIED,
 'INFO_TEXT_JOB_MODIFIED1'=>$last_modified,
 'INFO_TEXT_JOB_APPLY_BEFORE'=>INFO_TEXT_JOB_APPLY_BEFORE,
 'INFO_TEXT_JOB_APPLY_BEFORE1'=>tep_date_long(tep_db_output($row['expired'])),
 'INFO_TEXT_SALARY'=>INFO_TEXT_SALARY,//((tep_not_null($row['job_salary']))?INFO_TEXT_SALARY:''),
 'INFO_TEXT_SALARY_DOT'=>((tep_not_null($row['job_salary']))?':':''),
 'INFO_TEXT_SALARY1'=>((tep_not_null($row['job_salary']))?$sym_left.tep_db_output($row['job_salary']).$sym_rt:'Negotiable'),
 'INFO_TEXT_SKILLS'  => INFO_TEXT_SKILLS,
 'INFO_TEXT_SKILLS1'  => $final_skills,
'PREVIEW_UPLOADED_DOCS' => 
    (!empty($fileName) ? 
        (
            $fileExtension === 'pdf' ? '<iframe src="/' . $dest_path . '" frameborder="0"></iframe>' :
            ($fileExtension === 'html' ? '<iframe src="/' . $dest_path . '" frameborder="0"></iframe>' :
            ($fileExtension === 'zip' ? displayZipContents($dest_path) :
            '<p>Unsupported file type: ' . htmlspecialchars($fileExtension) . '</p>')
            )
        ) : 
        '<p><span class="label">Uploaded File:</span> None</p>'
            ),

 'INFO_TEXT_ALLOWANCE'=>INFO_TEXT_ALLOWANCE,
 'INFO_TEXT_ALLOWANCE1'=>tep_db_output($row['job_allowance']),
 'INFO_TEXT_JOB_TYPE'=>((tep_not_null($row['job_type']))?INFO_TEXT_JOB_TYPE:''),
 'INFO_TEXT_JOB_TYPE_DOT'=>((tep_not_null($row['job_type']))?':':''),
 'INFO_TEXT_JOB_TYPE1'=>((tep_not_null($row['job_type']))?($row['job_type']==0)?'Any Job Types':get_name_from_table(JOB_TYPE_TABLE,TEXT_LANGUAGE.'type_name', 'id', tep_db_output($row['job_type'])):''),
 'INFO_TEXT_EXPERIENCE'=>INFO_TEXT_EXPERIENCE,
 'INFO_TEXT_EXPERIENCE1'=>calculate_experience(tep_db_output($row['min_experience']),tep_db_output($row['max_experience'])),
 'INFO_JOB_CATEGORY'=>INFO_JOB_CATEGORY,
 'INFO_JOB_CATEGORY1'=>((tep_db_output($job_category_ids)!='0' && $job_category_ids!='')?get_name_from_table(JOB_CATEGORY_TABLE,TEXT_LANGUAGE.'category_name', 'id', tep_db_output($job_category_ids)):' All job category'),

 'INFO_TEXT_JOB_DETAILS'=>INFO_TEXT_JOB_DETAILS,
 'INFO_TEXT_SHORT_DESCRIPTION'=>INFO_TEXT_SHORT_DESCRIPTION,
 'INFO_TEXT_SHORT_DESCRIPTION1'=>nl2br(stripslashes($row['job_short_description'])),
// 'view_company_profile'=>tep_draw_form('search',FILENAME_JOBSEEKER_COMPANY_PROFILE,'', 'post').tep_draw_hidden_field('action','search').tep_draw_hidden_field('recruiter_id',tep_db_get_field(JOB_TABLE,'recruiter_id','job_id='.$job_id)).'<a href="#" onclick="submit()" title="View company profile">View company profile</a></form>',
 'view_company_profile'=>'<a href="'.tep_href_link(FILENAME_JOBSEEKER_COMPANY_DETAILS,'query_string='.$query_string).'" title="'.INFO_TEXT_VIEW_COMPANY_PROFILE.'" class="style27"><u>'.INFO_TEXT_VIEW_COMPANY_PROFILE.'</u></a>',
 'view_company_jobs'=>''.tep_draw_form('search',FILENAME_JOB_SEARCH,'', 'post').tep_draw_hidden_field('action','search').tep_draw_hidden_field('company',$recruiter_company_name1).'<a href="#" onclick="document.search.submit()" title="'.INFO_TEXT_VIEW_ALL_JOBS_OF_COMPANY.'" class="style27" ><u>'.INFO_TEXT_VIEW_ALL_JOBS_OF_COMPANY.'</u></a></form>',
//  'INFO_TEXT_SIMILAR_JOBS'=>tep_draw_form('search_smililar_job',FILENAME_JOB_SEARCH,'', 'post').tep_draw_hidden_field('action','search').tep_draw_hidden_field('country',$row['job_country_id']).tep_draw_hidden_field('job_category[]',(tep_db_output($job_category_ids)!='0' && $job_category_ids!='')?$job_category_ids:'0').'<a class="addMe" href="#" onclick="document.search_smililar_job.submit()" title="'.INFO_TEXT_VIEW_SIMILAR_JOBS.'" ><span class=""> '.INFO_TEXT_SIMILAR_JOBS.'</span></a></form>',
 'INFO_TEXT_SIMILAR_JOBS'=>tep_draw_form('search_smililar_job',FILENAME_JOB_SEARCH,'', 'post').tep_draw_hidden_field('action','search').tep_draw_hidden_field('job_category[]',(tep_db_output($job_category_ids)!='0' && $job_category_ids!='')?$job_category_ids:'0').'<a class="addMe" href="#" onclick="document.search_smililar_job.submit()" title="'.INFO_TEXT_VIEW_SIMILAR_JOBS.'" ><span class=""> '.INFO_TEXT_SIMILAR_JOBS.'</span></a></form>',
 'MAP_JAVA_SCRIPT_LINK' => '<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false'.((MODULE_GOOGLE_MAP_KEY!='')?'&key='.MODULE_GOOGLE_MAP_KEY:'').'"></script>',
 'MAP_JAVA_SCRIPT' => $map_java_script,
 'SHOW_MAP' => (GOOGLE_MAP=='true'?$show_map:''),
 'DISPLAY_MAP_CANVAS'=>(GOOGLE_MAP=='true'?'<div id="map-canvas"></div>':''),
 'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
 'RIGHT_BOX_WIDTH'=>RIGHT_BOX_WIDTH1,
 'LEFT_HTML'=>LEFT_HTML,
 'RIGHT_HTML'=>RIGHT_HTML,
 'update_message'=>$messageStack->output()));
if($action=='print')
{
 $template->pparse('job_details1');
}
else
{
 if($check_row=getAnytableWhereData(JOB_STATISTICS_TABLE,"job_id='".$job_id."'",'job_id,clicked'))
 {
  $sql_data_array=array('job_id'=>$job_id,
                        'clicked'=>($check_row['clicked']+1)
                        );
  tep_db_perform(JOB_STATISTICS_TABLE, $sql_data_array, 'update', "job_id='".$job_id."'");
 }
 else
 {
  $sql_data_array=array('job_id'=>$job_id,
                        'clicked'=>1
                        );
  tep_db_perform(JOB_STATISTICS_TABLE, $sql_data_array);
 }
 $curr_date =date('Y-m-d');
 if($check_row=getAnytableWhereData(JOB_STATISTICS_DAY_TABLE,"job_id='".$job_id."'  and  date='".tep_db_input($curr_date)."' ",'job_id,clicked'))
 {
  $sql_data_array=array('job_id'=>$job_id,
                        'clicked'=>($check_row['clicked']+1)
                        );
  tep_db_perform(JOB_STATISTICS_DAY_TABLE, $sql_data_array, 'update', "job_id='".$job_id."'  and  date='".tep_db_input($curr_date)."'");
 }
 else
 {
  $sql_data_array=array('job_id'=>$job_id,
                        'clicked'=>1,
						'viewed'=>1,
	                    'date'=>$curr_date
                        );
  tep_db_perform(JOB_STATISTICS_DAY_TABLE, $sql_data_array);
 }
 if(tep_not_null($row['indeed_id']) || tep_not_null($row['zr_id']) || tep_not_null($row['usajobs_id'])  )
  $template->pparse('indeed_job_details');
 else
 $template->pparse('job_details');
}
?>