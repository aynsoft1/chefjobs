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
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_JOBSEEKER_LIST_OF_SAVED_JOBS);
$template->set_filenames(array('saved_job'=>'my_saved_jobs.htm'));
include_once(FILENAME_BODY);

if(!check_login("jobseeker"))
{
 $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
 tep_redirect(FILENAME_JOBSEEKER_LOGIN);
}
$_GET['page'] = ((int)$_GET['page'] > 0 ? (int)$_GET['page'] : '1');

$action = (isset($_GET['action']) ? $_GET['action'] : '');
if(tep_not_null($action))
{
 switch($action)
 {
  case 'delete':
   $id = (isset($_GET['sID']) ? (int)$_GET['sID'] : '');
   if(!$row_check=getAnyTableWhereData(SAVE_JOB_TABLE,"id='".tep_db_input($id)."' and jobseeker_id='".$_SESSION['sess_jobseekerid']."'"))
   {
    $messageStack->add_session(MESSAGE_JOB_ERROR, 'error');
    tep_redirect(FILENAME_JOBSEEKER_LIST_OF_COVER_LETTERS);
   }
   tep_db_query("delete from ".SAVE_JOB_TABLE." where id='".$id."'");
   $messageStack->add_session(MESSAGE_SUCCESS_DELETED, 'success');
   tep_redirect(FILENAME_JOBSEEKER_LIST_OF_SAVED_JOBS);
 }
}

//////////////////////////////
///only for sorting starts
$sort_array=array('j.job_title','r.recruiter_company_name','j.re_adv','j.expired');
include_once(PATH_TO_MAIN_PHYSICAL_CLASS.'sort_by_clause.php');
$obj_sort_by_clause=new sort_by_clause($sort_array,'j.re_adv desc');
$order_by_clause=$obj_sort_by_clause->return_value;
///only for sorting ends
define('MAX_DISPLAY_LIST_OF_SAVED_JOBS',20);
$db_saved_job_query_raw = "select s.id,s.job_id,j.job_title,j.re_adv,j.expired,r.recruiter_company_name,r.recruiter_featured,j.job_source from " . SAVE_JOB_TABLE . " as s, ".JOB_TABLE." as j, ".RECRUITER_TABLE." as r, ".RECRUITER_LOGIN_TABLE." as rl where s.jobseeker_id='".$_SESSION['sess_jobseekerid']."' and s.job_id=j.job_id and j.recruiter_id=rl.recruiter_id and j.recruiter_id=r.recruiter_id and rl.recruiter_status='Yes' order by ".$order_by_clause;
//echo $db_saved_job_query_raw;
$db_saved_job_split = new splitPageResults($_GET['page'], MAX_DISPLAY_LIST_OF_SAVED_JOBS, $db_saved_job_query_raw, $db_saved_job_query_numrows);
$db_saved_job_query = tep_db_query($db_saved_job_query_raw);
$db_saved_job_num_row = tep_db_num_rows($db_saved_job_query);
if($db_saved_job_num_row > 0)
{
 $alternate=1;
 while ($saved_job = tep_db_fetch_array($db_saved_job_query)) 
 {
  $ide=$saved_job['id'];
  $job_id=$saved_job['job_id'];
  $title_format=encode_category($saved_job['job_title']);
  $query_string=encode_string("job_id=".$job_id."=job_id");
  $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
  $company_name=tep_db_output($saved_job['recruiter_company_name']);
  if($saved_job['recruiter_featured']=='Yes')
  {
   $company_name='<a href="'.tep_href_link(FILENAME_JOBSEEKER_COMPANY_DETAILS,'query_string='.$query_string).'">'.$company_name.'</a>';
  }
  $template->assign_block_vars('job', array( 'row_selected' => $row_selected,
   'job_title' => '<a href="'.getPermalink('job',array('ide'=>$job_id,'seo_name'=>$title_format)) .'">'.tep_db_output($saved_job['job_title']).'</u></a>',
   're_adv' => tep_date_short(tep_db_output($saved_job['re_adv'])),
   'company_name' => $company_name,
   'expired' => tep_date_short(tep_db_output($saved_job['expired'])),
   'delete' => '<a class="text-danger" href="'.tep_href_link(FILENAME_JOBSEEKER_LIST_OF_SAVED_JOBS,'sID='.$ide.'&action=delete').'">'.TABLE_HEADING_DELETE.'</a>',
   'apply' => ($saved_job['job_source']!='jobsite')?'':'<a class="" href="'.tep_href_link(FILENAME_APPLY_NOW,'query_string='.$query_string).'">Apply Now</a>',
   ));
  $alternate++;
 }
}
/////
$RIGHT_HTML="";
$RIGHT_BOX_WIDTH=RIGHT_BOX_WIDTH1;
/////
$template->assign_vars(array(
 'HEADING_TITLE'=>HEADING_TITLE,
 'TABLE_HEADING_JOB_TITLE'=>"<a href='".tep_href_link(FILENAME_JOBSEEKER_LIST_OF_SAVED_JOBS, tep_get_all_get_params(array('sort','cID','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][0]."' class='white'>".TABLE_HEADING_JOB_TITLE.$obj_sort_by_clause->return_sort_array['image'][0]."</a>",
 'TABLE_HEADING_COMPANY_NAME'=>"<a href='".tep_href_link(FILENAME_JOBSEEKER_LIST_OF_SAVED_JOBS, tep_get_all_get_params(array('sort','cID','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][1]."' class='white'>".TABLE_HEADING_COMPANY_NAME.$obj_sort_by_clause->return_sort_array['image'][1]."</a>",
 'TABLE_HEADING_ADVERTISED'=>"<a href='".tep_href_link(FILENAME_JOBSEEKER_LIST_OF_SAVED_JOBS, tep_get_all_get_params(array('sort','cID','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][2]."' class='white'>".TABLE_HEADING_ADVERTISED.$obj_sort_by_clause->return_sort_array['image'][2]."</a>",
 'TABLE_HEADING_EXPIRED'=>"<a href='".tep_href_link(FILENAME_JOBSEEKER_LIST_OF_SAVED_JOBS, tep_get_all_get_params(array('sort','cID','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][3]."' class='white'>".TABLE_HEADING_EXPIRED.$obj_sort_by_clause->return_sort_array['image'][3]."</a>",
 'TABLE_HEADING_DELETE'=>TABLE_HEADING_DELETE,
 'TABLE_HEADING_APPLY'=>TABLE_HEADING_APPLY,
 'count_rows'=>$db_saved_job_split->display_count($db_saved_job_query_numrows, MAX_DISPLAY_LIST_OF_SAVED_JOBS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_SAVED_JOBS),
 'no_of_pages'=>$db_saved_job_split->display_links($db_saved_job_query_numrows, MAX_DISPLAY_LIST_OF_SAVED_JOBS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'],tep_get_all_get_params(array('page','cID','action'))),
 'hidden_fields'=>$hidden_fields,
 'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
 'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
 'LEFT_HTML'=>'',
'JOB_SEARCH_LEFT'=>JOB_SEARCH_LEFT,
  'LEFT_HTML'=>LEFT_HTML_JOBSEEKER,
 'RIGHT_HTML'=>RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('saved_job');
?>