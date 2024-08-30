<?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik#********
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
***********************************************************
*/
//session_cache_limiter('private_no_expire');
include_once("include_files.php");
//ini_set('max_execution_time','0');
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_JOBFAIR_JOBS);
$template->set_filenames(array('jobfair_jobs_result' => 'jobfair_jobs_result.htm'));
include_once(FILENAME_BODY);

$query_string=tep_db_prepare_input($_GET['query_string']);
$recruiter_id=check_data($query_string,"=","recruiter_id","recruiter_id");
$jobfair_id=tep_db_prepare_input($_GET['jfid']);
//print_r($_POST);

//////////////////
///only for sorting starts
//include_once(PATH_TO_MAIN_PHYSICAL_CLASS.'sort_by_clause.php');

$template->assign_vars(array('TABLE_HEADING_JOB_TITLE'=>TABLE_HEADING_JOB_TITLE,
						'TABLE_HEADING_ADVERTISED'=>TABLE_HEADING_ADVERTISED,
						'TABLE_HEADING_EXPIRED'=>TABLE_HEADING_EXPIRED
));
///only for sorting ends
$_GET['page'] = ((int)$_GET['page'] > 0 ? (int)$_GET['page'] : '1');
$now=date("Y-m-d");//date("Y-m-d H:i:s");
$db_job_query_raw = "select j.job_id,j.job_title, j.add_jobfair, j.re_adv, j.expired  from ".JOB_JOBFAIR_TABLE." as jf left outer join  ".JOB_TABLE." as j on (jf.job_id =j.job_id) where jf.recruiter_id='".$recruiter_id."' and jf.jobfair_id='".$jobfair_id."' and j.expired >='$now' and j.re_adv <='$now' and j.job_status='Yes' and j.add_jobfair='Yes' and ( deleted is NULL or deleted='0000-00-00 00:00:00') ORDER BY j.re_adv desc";

//echo $db_job_query_raw;
$db_job_split = new splitPageResults($_GET['page'], MAX_DISPLAY_LIST_OF_JOBS, $db_job_query_raw, $db_job_query_numrows);
$db_job_query = tep_db_query($db_job_query_raw);
$db_job_num_row = tep_db_num_rows($db_job_query);
if($db_job_num_row > 0)
{
$alternate=1;
while($row = tep_db_fetch_array($db_job_query))
{
$ide=$row["job_id"];
$title_format=encode_category($row['job_title']);
$query_string=encode_string("job_id=".$ide."=job_id");

$job_category_ids=get_name_from_table(JOB_JOB_CATEGORY_TABLE,'job_category_id','job_id',$ide);

$row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
$template->assign_block_vars('result', array( 'row_selected' => $row_selected,
'job_title' => '<a href="'.getPermalink('job',array('ide'=>$ide,'seo_name'=>$title_format)) .'" target="_blank">'.tep_db_output($row['job_title']).'</u></a>',
'job_category' => ((tep_db_output($job_category_ids)!='0' && $job_category_ids!='')?get_name_from_table(JOB_CATEGORY_TABLE,TEXT_LANGUAGE.'category_name', 'id', tep_db_output($job_category_ids)):'-'),
're_adv' => tep_date_veryshort(tep_db_output($row['re_adv'])),
'expired' => tep_date_veryshort(tep_db_output($row['expired'])),
'apply' => '<a class="text-success" href="'.tep_href_link(FILENAME_APPLY_NOW,'query_string='.$query_string).'">'.INFO_TEXT_APPLY_NOW.'</a>',
));
$alternate++;
$lower = $lower + 1;
}
$plural=($x1=="1")? INFO_TEXT_JOB:INFO_TEXT_JOBS;
/********** rectreive company details******************/
$row_comp=getAnyTableWhereData(RECRUITER_TABLE." as r","recruiter_id= '".$recruiter_id."'",'recruiter_company_name,recruiter_logo,recruiter_address1, recruiter_city,recruiter_state_id,recruiter_state, recruiter_country_id,recruiter_zip,recruiter_telephone');
   $recruiter_company_name=tep_db_output($row_comp['recruiter_company_name']);
   $header_title='<title>'.tep_db_output($row_comp['recruiter_company_name']).'</title>';
   $company_logo=$row_comp['recruiter_logo'];
   if(tep_not_null($company_logo) && is_file(PATH_TO_MAIN_PHYSICAL.PATH_TO_LOGO.$company_logo))
   {
	$photo=tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_LOGO.$company_logo.'','','','" class="img-fluid img-thumbnail rounded mini-profile-img" ');
     $company_logo1=$photo;
	}

$recruiter_state=(tep_not_null($row_comp['recruiter_state'])?$row_comp['recruiter_state']:get_name_from_table(ZONES_TABLE,'zone_name', 'zone_id',$row_comp['recruiter_state_id']));

$country_name=get_name_from_table(COUNTRIES_TABLE,'country_name', 'id',$row_comp['recruiter_country_id']);

 if($row_comp['recruiter_city']!='')
   $recuiter_address=$row_comp['recruiter_city'];
   $recuiter_address.=' '.$recruiter_state;
   $recuiter_address.=' '.$country_name;
   $recuiter_address =trim($recuiter_address);
   if($recuiter_address!='' && $row_comp['recruiter_zip']!='')
   $recuiter_address.="<br>".$row_comp['recruiter_zip'];
/***********************************************************/

$template->assign_vars(array('total'=>tep_db_output(SITE_TITLE).INFO_TEXT_HAS_MATCHED." <font color='red'><b>$x1</b></font> ".$plural. INFO_TEXT_TO_YOUR_SEARCH_CRITERIA));
}
else
{
$template->assign_vars(array('total'=>tep_db_output(SITE_TITLE)." ".INFO_TEXT_HAS_NOT_MATCHED." "));
}
see_page_number();
//tep_db_free_result($result);

$template->assign_vars(array(
'HEADING_TITLE'=>$recruiter_company_name,
'INFO_TEXT_HEADER_TITLE'=>$header_title,
'INFO_TEXT_RECRUITER_LOGO'=>$company_logo1,
'INFO_TEXT_RECRUITER_DESC'=>$company_description,
'INFO_TEXT_RECRUITER_ADDRESS'=>$recuiter_address,
'TABLE_HEADING_APPLY'=>TABLE_HEADING_APPLY,
'TABLE_HEADING_JOB_CATEGORY'=>TABLE_HEADING_JOB_CATEGORY,
'hidden_fields' => $hidden_fields,
'back_button' => tep_image_button(PATH_TO_BUTTON.'button_back.gif', IMAGE_BACK,'onclick="history.back();"'),
'INFO_TEXT_COMPANY_NAME' => INFO_TEXT_COMPANY_NAME,
'INFO_TEXT_COMPANY_NAME1' => $company_string,
'hidden_fields' => $hidden_fields,
 'count_rows'=>$db_job_split->display_count($db_job_query_numrows, MAX_DISPLAY_LIST_OF_JOBS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_JOBS),
 'no_of_pages'=>$db_job_split->display_links($db_job_query_numrows, MAX_DISPLAY_LIST_OF_JOBS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'],tep_get_all_get_params(array('page'))),


'RIGHT_BOX_WIDTH' => RIGHT_BOX_WIDTH1,
'RIGHT_HTML' => RIGHT_HTML,
'JOB_SEARCH_LEFT' => JOB_SEARCH_LEFT,
'update_message' => $messageStack->output(),
));
$template->pparse('jobfair_jobs_result');
?>