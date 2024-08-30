<?php
/*
***********************************************************
**********# Name          : SHAMBHU PRASAD PATNAIK   #*****
**********# Company       : Aynsoft                 #******
**********# Copyright (c) www.aynsoft.com 2017     #*******
***********************************************************
*/
include_once("include_files.php");
ini_set('max_execution_time','0');
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_JOB_SEARCH_BY_SKILL);
$template->set_filenames(array('job_skill'=>'job_search_by_skill1.htm','job_search_result'=>'job_search_by_skill.htm'));
include_once(FILENAME_BODY);
$jscript_file=tep_href_link(PATH_TO_LANGUAGE.$language."/jscript/".'jobs_search.js');
$preview_box_jscript_file=(PATH_TO_LANGUAGE.$language."/jscript/".'previewbox.js');
if(!isset($_GET['skill']))
{

 $field_names="id,tag";
 $whereClause=" where status='active' ";
 $query11 = "select $field_names from ".SKILL_TAGS_TABLE." $whereClause  order by tag  asc ";
 $result11=tep_db_query($query11);
 $i=0;
 while($row11 = tep_db_fetch_array($result11))
 {
  $ide=$row11["id"];
  $tag=getSkillTagLink ($row11['tag'],' Jobs');
  //$tag=getSkillTagValueForSearch($row11['tag']);
  if($i%3==0)
  {
   $template->assign_block_vars('job_skill1', array(
                                'job_skill_tag'=>$tag,
                               ));
  }
  elseif($i%3==1)
  {
   $template->assign_block_vars('job_skill2', array(
                                'job_skill_tag'=>$tag,
                               ));
  }
  else
  {
   $template->assign_block_vars('job_skill3', array(
                                'job_skill_tag'=>$tag,
                               ));
  }
  $i++;
 }
   tep_db_free_result($result11);

 $template->assign_vars(array(
  'HEADING_TITLE'          => HEADING_TITLE,
 ));
$template->assign_vars(array(
 'RIGHT_BOX_WIDTH' => RIGHT_BOX_WIDTH1,
 'RIGHT_HTML' => RIGHT_HTML,
 'LEFT_HTML'=>LEFT_HTML_JOBSEEKER,
 'update_message' => $messageStack->output()));
 $template->pparse('job_skill');

}
else
{
$job_skill   = tep_db_prepare_input($_GET['skill']);

$action      = 'search';
// search
if(tep_not_null($action))
{
 switch($action)
 {
  case 'search':
   $hidden_fields1='';
   $action=tep_db_prepare_input($_POST['action']);
   $hidden_fields.=tep_draw_hidden_field('action',$action);
   $field=tep_db_prepare_input($_POST['field']);
   $order=tep_db_prepare_input($_POST['order']);
   $lower=(int)tep_db_prepare_input($_POST['lower']);
   $higher=(int)tep_db_prepare_input($_POST['higher']);
   $whereClause='';
   if(tep_not_null($job_skill) )
   {
	$hidden_fields1.=tep_draw_hidden_field('skill',$job_skill);
	$whereClause=(tep_not_null($whereClause)?$whereClause.' and ( ':' ( ');
	$whereClause.=" j.job_skills = '".tep_db_input($job_skill)."'";
	$whereClause.=" or j.job_skills like '".tep_db_input($job_skill).",%'";
	$whereClause.=" or j.job_skills like '%,".tep_db_input($job_skill)."'";
	$whereClause.=" or j.job_skills like '%,".tep_db_input($job_skill).",%'";
	$whereClause.="  )";
   }
   // job_skill  ends ///
   $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
   ////
   $now=date('Y-m-d H:i:s');
   $table_names=JOB_TABLE." as j left outer join ".RECRUITER_LOGIN_TABLE.' as rl on (j.recruiter_id=rl.recruiter_id) left outer join '.RECRUITER_TABLE.' as r on (rl.recruiter_id=r.recruiter_id)  left outer join '.ZONES_TABLE.' as z on (j.job_state_id=z.zone_id or z.zone_id is NULL) left outer join '.COUNTRIES_TABLE.' as c on (j.job_country_id =c.id) left outer join '.JOB_TYPE_TABLE.' as jt on (j.job_type =jt.id)';
   $whereClause.="   rl.recruiter_status='Yes' and j.expired >='$now' and j.re_adv <='$now' and j.job_status='Yes' and ( j.deleted is NULL or j.deleted='0000-00-00 00:00:00')";
   $field_names="j.job_id, j.job_title, j.re_adv, j.job_short_description,  j.recruiter_id,j.min_experience,j.max_experience,j.job_salary,j.job_industry_sector,j.job_type,j.expired,j.recruiter_id,r.recruiter_company_name,r.recruiter_logo,j.job_source,j.post_url,j.url,j.job_featured,concat(case when j.job_location='' then '' else concat(j.job_location,', ') end, if(j.job_state_id,z.zone_name,j.job_state)) as location ,c.country_name,j.job_skills"; //j.job_state, j.job_state_id,j.job_country_id
   //$query1 = "select count(j.job_id) as x1 from $table_names where $whereClause ";

   //////////////////
			$query = "select $field_names from $table_names where $whereClause ORDER BY if(j.job_source ='jobsite',0,1)  asc, j.inserted desc, j.job_featured='Yes' desc";
			$starting=0;
			$recpage = MAX_DISPLAY_SEARCH_RESULTS;
			$obj = new pagination_class1($query,$starting,$recpage,$keyword,$location,$word1,$country,$state,$job_category,$experience,$job_post_day,$search_zip_code,$zip_code,$radius,0,$job_skill);

			$result = $obj->result;
			$x=tep_db_num_rows($result);
			$content='';
			$count=1;
			$count1=1;
   if(tep_db_num_rows($result)!=0)
   {
    while($row = tep_db_fetch_array($result))
    {
     $ide=$row["job_id"];
     $recruiter_logo='';
     $company_logo=$row['recruiter_logo'];
     $title_format=encode_category($row['job_title']);
     $query_string=encode_string("job_id=".$ide."=job_id");

					if(tep_not_null($company_logo) && is_file(PATH_TO_MAIN_PHYSICAL.PATH_TO_LOGO.$company_logo))
     $recruiter_logo=tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_LOGO.$company_logo."&size=120");

					$email_job    ='<a class="btn btn-sm btn-text border bg-white mr-3" href="'.tep_href_link(FILENAME_TELL_TO_FRIEND,'query_string='.$query_string).'" title="'.tep_db_output(INFO_TEXT_EMAIL_THIS_JOB).'" target="_blank"><i class="fa fa-envelope-o mr-1" aria-hidden="true"></i> '.INFO_TEXT_EMAIL_THIS_JOB.'</a>';
					$apply_job    ='<a class="btn btn-block btn-sm btn-primary" class="d-block" href="'.tep_href_link(FILENAME_APPLY_NOW,'query_string='.$query_string).'" title="'.tep_db_output(INFO_TEXT_APPLY_TO_THIS_JOB).'" target="_blank">'.INFO_TEXT_APPLY_TO_THIS_JOB.'</a>';
     if($row['job_featured']=='Yes')
					{
					 $row_selected='jobSearchRowFea';
					}
					else
					{
					 $row_selected='jobSearchRow1';
						$count++;
					}
					$job_skill1= getSkillTagLink($row['job_skills']);

////*** curency display coding ***********/
$row_cur=getAnyTableWhereData(CURRENCY_TABLE,"code ='".DEFAULT_CURRENCY."'",'symbol_left,symbol_right');
$sym_left=(tep_not_null($row_cur['symbol_left'])?$row_cur['symbol_left'].' ':'');
$sym_rt=(tep_not_null($row_cur['symbol_right'])?' '.$row_cur['symbol_right']:'');
//////**********currency display ***************************/

					$template->assign_block_vars('job_search_result', array(
                                  'row_selected' => $row_selected,
								  'jobId' => $row['job_id'],
                                  'check_box' => (($row['post_url']=='Yes'  )?'':'<input class="form-check-input" type="checkbox" name="apply_job" value="'.$query_string.'">'),
                                  'job_title' => '<a href="'.getPermalink('job',array('ide'=>$ide,'seo_name'=>$title_format)).'" class="job_search_title" target="_blank">'.tep_db_output($row['job_title']).'</a>',
 						          'company_name' =>tep_db_output($row['recruiter_company_name']),
 						          'location' =>tep_db_output($row['location'].' '.$row['country_name']),
                                  'experience' =>tep_db_output(calculate_experience($row['min_experience'],$row['max_experience'])),
                                  'salary' =>(tep_not_null($row['job_salary']))?$sym_left.tep_db_output($row['job_salary']).$sym_rt:'',
                                  'salary_class' =>(tep_not_null($row['job_salary']))?'':'result_hide',
                                  'job_skill' =>(tep_not_null($row['job_skills']))? $job_skill1:'',
                                  'skill_class' =>(tep_not_null($row['job_skills']))?'':'result_hide',
						          'description' => nl2br(tep_db_output(strip_tags($row['job_short_description']))),
                                  'apply_before' => tep_date_long($row['expired']),
                                  'logo'      => $recruiter_logo,
	 					          'email_job' => $email_job,
	 					          'apply_job' => $apply_job,

						                            ));




     /////////////////////////////////////////////////////////
     if($check_row=getAnytableWhereData(JOB_STATISTICS_TABLE,"job_id='".$ide."'",'viewed'))
     {
      $sql_data_array=array('job_id'=>$ide,
                            'viewed'=>($check_row['viewed']+1)
                            );
      tep_db_perform(JOB_STATISTICS_TABLE, $sql_data_array, 'update', "job_id='".$ide."'");
     }
     else
     {
      $sql_data_array=array('job_id'=>$ide,
                            'viewed'=>1
                            );
      tep_db_perform(JOB_STATISTICS_TABLE, $sql_data_array);
     }
     $curr_date =date('Y-m-d');
	 if($check_row=getAnytableWhereData(JOB_STATISTICS_DAY_TABLE,"job_id='".tep_db_input($ide)."' and  date='".tep_db_input($curr_date)."'",'viewed'))
     {
      $sql_data_array=array('job_id'=>$ide,
                            'viewed'=>($check_row['viewed']+1)
                            );
      tep_db_perform(JOB_STATISTICS_DAY_TABLE, $sql_data_array, 'update', "job_id='".tep_db_input($ide)."' and  date='".tep_db_input($curr_date)."'");
     }
     else
     {
      $sql_data_array=array('job_id'=>$ide,
		                    'date'=>$curr_date,
                            'viewed'=>1
                            );
      tep_db_perform(JOB_STATISTICS_DAY_TABLE, $sql_data_array);
     }

     /////////////////////////////////////////////////////////
    }
	$template->assign_vars(array('pages'=>$obj->anchors,'total_pages'=>$obj->total,'page_view'=>$obj->show_view));
    $plural=($x1=="1")?INFO_TEXT_JOB:INFO_TEXT_JOBS;
    $template->assign_vars(array('total'=>SITE_TITLE." ".INFO_TEXT_HAS_MATCHED." <font color='red'><b>$x1</b></font> ".$plural." ".INFO_TEXT_TO_YOUR_SEARCH_CRITERIA));
   }
   else
   {
    $template->assign_vars(array('content_hide'=>'result_hide','total'=>SITE_TITLE." ".INFO_TEXT_HAS_NOT_MATCHED." <br><br>&nbsp;&nbsp;&nbsp;"));
   }
  break;
 }
}

 $template->assign_vars(array( 'hidden_fields' => $hidden_fields,
  'HEADING_TITLE'          => sprintf(HEADING_TITLE,$job_skill),
  'hidden_fields1'          => $hidden_fields1,
  'form'                   => tep_draw_form('page', FILENAME_JOB_SEARCH_BY_SKILL,'','post'),
  'form1'                  => tep_draw_form('search1', FILENAME_JOB_SEARCH,'','post').tep_draw_hidden_field('action','search'),
  'button'                 => tep_image_submit(PATH_TO_BUTTON.'button_refine_search.gif', IMAGE_SEARCH),
 // 'INFO_TEXT_KEYWORD'      => INFO_TEXT_KEYWORD,
  'INFO_TEXT_KEYWORD1'     => tep_draw_input_field('keyword', $key1,'style="font-size: 12px;color: #626262; width:120;"',false),
  'INFO_TEXT_LOCATION'     => INFO_TEXT_LOCATION,
  'INFO_TEXT_LOCATION1'    => tep_draw_input_field('location', $loc1 ,'style="font-size: 12px;color: #626262; width:120;"',false),
  'INFO_TEXT_APPLY_NOW'    => (($x>0)?INFO_TEXT_APPLY_NOW:''),
  'INFO_TEXT_APPLY_NOW1'   => (($x>0)?INFO_TEXT_APPLY_NOW1:''),
  'INFO_TEXT_APPLY_ARROW'  => (($x>0)?tep_image('img/job_search_arrow.gif',''):''),

  // 'INFO_TEXT_APPLY_BUTTON' => (($x>0)?(check_login("jobseeker")?tep_image_button(PATH_TO_BUTTON.'button_apply_selectedjob.gif', IMAGE_APPLY,'onclick="ckeck_application(\'\');" style="cursor:pointer;"'):tep_image_button(PATH_TO_BUTTON.'button_registered_user.gif', IMAGE_APPLY,'onclick="ckeck_application(\'\');" style="cursor:pointer;"').' '.tep_image_button(PATH_TO_BUTTON.'button_new_user.gif', IMAGE_APPLY,'onclick="ckeck_application(\'new\');" style="cursor:pointer;"')):''),
  'INFO_TEXT_APPLY_BUTTON' => (($x>0)?(check_login("jobseeker")?'<a class="btn btn-outline-primary" onclick="ckeck_application(\'\');" role="button">Apply to Selected Jobs</a>':'<a class="btn btn-primary" onclick="ckeck_application(\'\');" role="button">Registered User</a> <a class="btn btn-outline-primary" onclick="ckeck_application(\'new\');" role="button">New User</a>'):''),


  'INFO_TEXT_LOCATION_NAME'=> INFO_TEXT_LOCATION_NAME,
  'INFO_TEXT_EXPERIENCE'   => INFO_TEXT_EXPERIENCE,
  'INFO_TEXT_SALARY'       => INFO_TEXT_SALARY,
  'INFO_TEXT_JOB_SKILL'    =>INFO_TEXT_JOB_SKILL,
  'INFO_TEXT_APPLY_BEFORE' => INFO_TEXT_APPLY_BEFORE,
  'JOB_SEARCH_LEFT'        => JOB_SEARCH_LEFT,
  'INFO_TEXT_COMPANY_NAME' => INFO_TEXT_COMPANY_NAME,
  'INFO_TEXT_JSCRIPT_FILE'  => $jscript_file,
  'PREVIEW_BOX_JSCRIPT_FILE' => $preview_box_jscript_file,
  'base_url'=> tep_href_link(),
  'MAP_JAVA_SCRIPT_LINK' => '<script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=false'.((MODULE_GOOGLE_MAP_KEY!='')?'&key='.MODULE_GOOGLE_MAP_KEY:'').'"></script>',
  'LEFT_HTML'=>LEFT_HTML_JOBSEEKER,

  ));

$template->assign_vars(array(
 'RIGHT_BOX_WIDTH' => RIGHT_BOX_WIDTH1,
 'RIGHT_HTML' => RIGHT_HTML,
 'LEFT_HTML'=>LEFT_HTML_JOBSEEKER,
 'update_message' => $messageStack->output()));
 $template->pparse('job_search_result');
}

?>