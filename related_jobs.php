<?
/*
***********************************************************
**********# Name       : SHAMBHU PRASAD PATNAIK #**********
**********# Company    : Aynsoft                #**********
**********# Copyright (c) www.aynsoft.com 2013  #**********
***********************************************************
*/
include_once("include_files.php");
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_RELATED_JOBS);
$template->set_filenames(array('related_jobs' => 'related_jobs.htm'));
include_once(FILENAME_BODY);

$job_id=tep_db_prepare_input($_GET['job']);
$related_search='';
$whereClause1=$whereClause2=$whereClause3='';
if($job_id)
{
 $where_clause11 ="job_id ='".tep_db_input($job_id)."'";
 $job_category_ids=get_name_from_table(JOB_JOB_CATEGORY_TABLE,'job_category_id','job_id',tep_db_output($job_id));

 if($row_c=getAnyTableWhereData(JOB_TABLE,$where_clause11,'job_location,job_state,job_state_id,recruiter_id,job_country_id')) 
	{
		$s_location   = $row_c['job_location'];
		$s_job_state     = $row_c['job_state'];
		$s_job_state_id = $row_c['job_state_id'];
		$s_job_state_id = $row_c['job_state_id'];
		$s_recruiter_id = $row_c['recruiter_id'];
		$s_job_country_id = $row_c['job_country_id'];
	 $whereClause1.=" j.job_id  !=  ".tep_db_input($job_id)." ";//
	 $whereClause2.=" j.job_id  !=  ".tep_db_input($job_id)." ";//
  if(tep_not_null($s_location))
		{
		 $whereClause1=(tep_not_null($whereClause1)?$whereClause1.' and ':'');
		 $whereClause2=(tep_not_null($whereClause2)?$whereClause2.' and ':'');
		 $whereClause1.=" j.job_location like '%".tep_db_input($s_location)."%' ";//
		 $whereClause2.=" j.job_location like '%".tep_db_input($s_location)."%' ";//
  }
  if($s_job_state_id>0)
		{
		 $whereClause1=(tep_not_null($whereClause1)?$whereClause1.' and ':'');
		 $whereClause2=(tep_not_null($whereClause2)?$whereClause2.' or ':'');
		 $whereClause1.=" j.job_state_id = '".tep_db_input($s_job_state_id)."' ";//
		 $whereClause2.=" j.job_state_id = '".tep_db_input($s_job_state_id)."' ";//
		}
		if(tep_not_null($s_job_state))
		{
		 $whereClause1=(tep_not_null($whereClause1)?$whereClause1.' and ':'');
		 $whereClause2=(tep_not_null($whereClause2)?$whereClause2.' or ':'');
		 $whereClause1.=" j.job_state like '%".tep_db_input($s_job_state)."%' ";//
		 $whereClause2.=" j.job_state like '%".tep_db_input($s_job_state)."%' ";//
  }	
		if(tep_not_null($s_job_country_id))
		{
		 $whereClause1=(tep_not_null($whereClause1)?$whereClause1.' and ':'');
		 $whereClause2=(tep_not_null($whereClause2)?$whereClause2.' or ':'');
		 $whereClause1.=" j.job_country_id = '".tep_db_input($s_job_country_id)."' ";//
		 $whereClause2.=" j.job_country_id = '".tep_db_input($s_job_country_id)."' ";//
  }
	}
}
//print_r($row_c);die("ok");
$now=date('Y-m-d H:i:s');
$table_names=JOB_TABLE." as j left outer join ".RECRUITER_LOGIN_TABLE.' as rl on (j.recruiter_id=rl.recruiter_id ) left outer join '.RECRUITER_TABLE.' as r on (r.recruiter_id=rl.recruiter_id ) left join '.ZONES_TABLE.' as z on(j.job_state_id=z.zone_id or z.zone_id is NULL) left outer join '.COUNTRIES_TABLE.' as c on (j.job_country_id =c.id) left outer join '.JOB_TYPE_TABLE.' as jt on (j.job_type =jt.id)';
$whereClause=" rl.recruiter_status='Yes'  and j.expired >='$now' and j.re_adv <='$now' and j.job_status='Yes' and ( j.deleted is NULL or j.deleted='0000-00-00 00:00:00') ";//
$field_names="c.country_name,j.job_id,j.job_salary,j.job_title,concat(case when j.job_location='' then '' else concat(j.job_location,', ') end, if(j.job_state_id,z.zone_name,j.job_state)) as job_location,r.recruiter_company_name,j.min_experience,j.job_type,jt.type_name,j.max_experience,r.recruiter_logo,r.recruiter_url,j.job_short_description,j.expired,j.job_featured,j.url,j.post_url,j.job_skills";
//echo $whereClause2;die();
if(tep_not_null($whereClause1))
{
 if(tep_not_null($job_category_ids))
 {
   $whereClause_job_category=" select distinct (j.job_id) from ".JOB_TABLE."  as j  left join ".JOB_JOB_CATEGORY_TABLE." as jc on(j.job_id=jc.job_id ) where j.expired >='$now' and j.re_adv <='$now' and j.job_status='Yes' and ( j.deleted is NULL or j.deleted='0000-00-00 00:00:00') and jc.job_category_id in (".$job_category_ids.")"; 
  $query = "select $field_names from $table_names where $whereClause  and  ($whereClause1) and (j.job_id  !=  ".tep_db_input($job_id).") and job_id in  ( $whereClause_job_category) order by j.inserted desc limit 0,10 ";
 }
 else
 {
  $query = "select $field_names from $table_names where $whereClause  and  ($whereClause1) and (j.job_id  !=  ".tep_db_input($job_id).")  order by j.inserted desc limit 0,10 ";
 }
 $result=tep_db_query($query);
 $x=tep_db_num_rows($result);
 if(!$x)
	{
  $query = "select $field_names from $table_names where $whereClause  and  ($whereClause1) and (j.job_id  !=  ".tep_db_input($job_id).")order by j.inserted desc limit 0,10 ";
  $result=tep_db_query($query);
  $x=tep_db_num_rows($result);
  if(!$x)
	 {
	  $query = "select $field_names from $table_names where $whereClause  and ($whereClause2) and (j.job_id  !=  ".tep_db_input($job_id).") order by j.inserted desc limit 0,10 ";
   $result=tep_db_query($query);
   $x=tep_db_num_rows($result);
  }
 }
}
if(!$x)
{
	if(tep_not_null($job_category_ids))
	{
  $whereClause_job_category=" select distinct (j.job_id) from ".JOB_TABLE."  as j  left join ".JOB_JOB_CATEGORY_TABLE." as jc on(j.job_id=jc.job_id ) where j.expired >='$now' and j.re_adv <='$now' and j.job_status='Yes' and ( j.deleted is NULL or j.deleted='0000-00-00 00:00:00') and jc.job_category_id in (".$job_category_ids.")"; 

  $whereClause_job_category=' and job_id in  ('.$whereClause_job_category.')';
		$query = "select $field_names from $table_names where $whereClause $whereClause_job_category  order by j.inserted desc limit 0,10 ";
  $result=tep_db_query($query);
  $x=tep_db_num_rows($result);
	}
 if(!$x)
	{
  $query = "select $field_names from $table_names where $whereClause  order by j.inserted desc limit 0,10 ";
  $result=tep_db_query($query);
  $x=tep_db_num_rows($result);
	}
}
//var_dump($query);
//echo $x;exit;
//die("ok");
$i=1;
while($row = tep_db_fetch_array($result))
{
 $ide=$row["job_id"];
 $title_format=encode_category($row['job_title']);
 $query_string=encode_string("job_id=".$ide."=job_id");
 $name_short=	$row['job_title'];
 $title='<a href="'.getPermalink('job',array('ide'=>$ide,'seo_name'=>$title_format)).'"  title="'.tep_db_output($row['job_title']).'"target="_blank">'.tep_db_output($name_short).'</a>';
 $location=tep_db_output($row['job_location']);
	$company_logo=$row['recruiter_logo'];
 if(tep_not_null($company_logo) && is_file(PATH_TO_MAIN_PHYSICAL.PATH_TO_LOGO.$company_logo))
 {
		if(tep_not_null($row['recruiter_url']))
  {
   $recruiter_url=trim($row['recruiter_url']);
   if(substr($recruiter_url,0,4)!='http')
   $recruiter_url='http://'.$recruiter_url;
   $photo=tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_LOGO.$company_logo."&size=200");
   $company_logo='<a href="'.$recruiter_url.'" target="new_site">'.$photo.'</a>';
  }
  else
  {
   $photo=tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_LOGO.$company_logo."&size=220");
   $company_logo=$photo;
  }
	}
	else
  $company_logo='';
	$email_job    ='<a href="'.tep_href_link(FILENAME_TELL_TO_FRIEND,'query_string='.$query_string).'" target="_blank">'.INFO_TEXT_EMAIL_THIS_JOB.'</a>';

	$apply_job    ='';
	if($row['post_url']=='Yes')
	{
	 $post_url=trim($row['url']);
  if(substr($post_url,0,4)!='http')
  $post_url='http://'.$post_url;
  $apply_job    .='<a class="fw-bold text-primary" href="'.$post_url.'" target="_blank">'.INFO_TEXT_APPLY_TO_THIS_JOB.' <i class="bi bi-box-arrow-in-up-right ms-2"></i></a>';
 }
	else
	$apply_job    .='<a class="fw-bold text-primary" href="'.tep_href_link(FILENAME_APPLY_NOW,'query_string='.$query_string).'"  target="_blank">'.INFO_TEXT_APPLY_TO_THIS_JOB.' <i class="bi bi-box-arrow-in-up-right ms-2"></i></a>';

	if($row['job_featured']=='Yes')
	$row_class = 'related_jobs_box2';
	else
	{
	 $row_class='related_jobs_box'.($i%2==1?'':'1');
		$i++;
	}
	//$job_skill_1= getSkillTagValueForSearch($row['job_skills']);
	$job_skill_1=getSkillTagLink ($row['job_skills'],' Jobs');

 $template->assign_block_vars('related_jobs', array(
							  'row_class' 		=> $row_class, 
                              'job_title'     	=> $title,
							  'job_type'		=> tep_db_output($row['type_name']),
                              'job_company'   	=> tep_db_output($row['recruiter_company_name']),
                              'job_location'  	=> $location.' '.$row['country_name'],
		                      'job_experience'	=> calculate_experience(tep_db_output($row['min_experience']),tep_db_output($row['max_experience'])),
							  'salary' =>(tep_not_null($row['job_salary'])?$sym_left.tep_db_output($row['job_salary']).$sym_rt:'Negotiable'),
							  'salary_class' =>(tep_not_null($row['job_salary']))?'':'result_hide',
							  'job_skill' =>(tep_not_null($row['job_skills']))? $job_skill_1:'',
							  'skill_class' =>(tep_not_null($row['job_skills']))?'':'result_hide',
		                    //   'summary' 		=> nl2br(tep_db_output(strip_tags($row['job_short_description']))),
							  'summary' => nl2br(tep_db_output(strip_tags(substr($row['job_short_description'],0,70).'. . .'))),
							  'apply_before' 	=> tep_date_long($row['expired']),
							  'posted_on' => tep_date_short($row['inserted']),
							  'logo'          	=> $company_logo, 
 		                      'email_job' 		=> $email_job,
	 	                      'apply_job' 		=> $apply_job,
							  
                           ));
}
tep_db_free_result($result);

$template->assign_vars(array(
 'HEADING_TITLE'    => HEADING_TITLE,
 'LEFT_BOX_WIDTH'   => LEFT_BOX_WIDTH1,
 'RIGHT_BOX_WIDTH'  => RIGHT_BOX_WIDTH1,
 'LEFT_HTML'        => LEFT_HTML,
 'RIGHT_HTML'       => RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('related_jobs');
?>