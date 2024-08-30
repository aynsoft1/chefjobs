<?
/*
***********************************************************
**********# Name          : SHAMBHU PRASAD PATNAIK   #**********
**********# Company       : Aynsoft                 #**********
**********# Copyright (c) www.aynsoft.com 2004     #**********
***********************************************************
*/

include_once("../include_files.php");
include_once('facebook_body.php');
$template->set_filenames(array('job_details' => 'job_details.htm','job_details1' => 'job_details1.htm'));
$job_id=tep_db_prepare_input($_GET['job_id']);
$now=date('Y-m-d H:i:s');
$table_names=JOB_TABLE.' as j left outer join  '.RECRUITER_TABLE.' as r on (j.recruiter_id=r.recruiter_id ) left outer join  '.RECRUITER_LOGIN_TABLE.' as rl on (rl.recruiter_id=r.recruiter_id ) left outer join '.ZONES_TABLE.' as z on (j.job_state_id=z.zone_id or z.zone_id is NULL) left outer join '.COUNTRIES_TABLE.' as c on (j.job_country_id =c.id)';
$where_clause=" j.recruiter_id=r.recruiter_id and r.recruiter_id=rl.recruiter_id and rl.recruiter_status='Yes' and j.job_id='".tep_db_input($job_id)."' and j.expired >='$now' and j.re_adv <='$now' and j.job_status='Yes' and ( j.deleted is NULL or j.deleted='0000-00-00 00:00:00')";
$field_names="j.job_id, j.job_title,j.job_reference,j.re_adv, j.job_short_description,j.job_description,j.job_salary,j.job_type,j.expired,j.job_source,concat(case when j.job_location='' then '' else concat(j.job_location,', ') end, if(j.job_state_id,z.zone_name,j.job_state)) as location ,c.country_name,r.recruiter_company_name,r.recruiter_logo";//
if(!$row=getAnyTableWhereData($table_names,$where_clause,$field_names)) 
{ 
 if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest')
 {
		die('Error : Sorry this job does not exist or deleted from our database.');
	}
	else
 {
		$messageStack->add_session('Error : Sorry this job does not exist or deleted from our database.', 'error');
  tep_redirect(tep_href_link(FILENAME_ERROR));
	}
}

if($check_row=getAnytableWhereData(JOB_STATISTICS_TABLE,"job_id='".$job_id."'",'job_id,clicked'))
{
 $sql_data_array=array('job_id'=>$job_id,'clicked'=>($check_row['clicked']+1));
 tep_db_perform(JOB_STATISTICS_TABLE, $sql_data_array, 'update', "job_id='".$job_id."'");
}
else
{
 $sql_data_array=array('job_id'=>$job_id,'clicked'=>1);
 tep_db_perform(JOB_STATISTICS_TABLE, $sql_data_array);
}
$ide=$row["job_id"];
$recruiter_logo='';
$company_logo=$row['recruiter_logo'];
$title_format=encode_category($row['job_title']);
$query_string=encode_string("job_id=".$ide."=job_id");

if(tep_not_null($company_logo) && is_file(PATH_TO_MAIN_PHYSICAL.PATH_TO_LOGO.$company_logo))
$recruiter_logo=tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_LOGO.$company_logo."&size=120");

$job_salary =tep_db_output($row['job_salary']);
$job_type  = (($row['job_type']!='' && $row['job_type']!='0')?get_name_from_table(JOB_TYPE_TABLE,'type_name', 'id',$row['job_type']):'');
$job_category_ids=get_name_from_table(JOB_JOB_CATEGORY_TABLE,'job_category_id','job_id',$ide);
$job_category  = (($job_category_ids!='' && $job_category_ids!='0')?get_name_from_table(JOB_CATEGORY_TABLE,'category_name', 'id',$job_category_ids):'');
$job_source   = $row['job_source'];
if($job_source=='jobsite')
$job_description = nl2br(stripslashes($row['job_description']));
else
$job_description = tep_db_output($row['job_short_description']);

$job_apply_url = '<a href="'.tep_href_link(FILENAME_APPLY_NOW,'query_string='.$query_string).'" class="job_apply" target="_blank">Apply Now</a>';

$template->assign_vars(array(
 'job_title' =>tep_db_output($row['job_title']),
 'job_logo' =>$recruiter_logo,
 'job_company' =>tep_db_output($row['recruiter_company_name']),
 'job_location' =>tep_db_output($row['location'].' '.$row['country_name']),
 'job_url' =>tep_href_link($ide.'/'.$title_format.'.html'),
 'job_category' =>$job_category,
 'job_salary_class' =>($job_salary=='')?'element_hide':'',
 'job_salary' =>$job_salary,
 'job_type_class' =>($job_type=='')?'element_hide':'',
 'job_type' =>$job_type,
 'job_posted' =>tep_date_long($row['re_adv']),
 'job_expired' =>tep_date_long($row['expired']),
 'job_description' =>$job_description,
 'job_apply' =>$job_apply_url,
 
 ));
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest')
{
 echo $template->pparse1('job_details1');die();
}
else
{
 $template->pparse('job_details');
}
?>