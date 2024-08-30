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
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_RECRUITER_LIST_OF_JOBFAIRS);
$template->set_filenames(array('jobfairs' => 'list_of_jobfairs.htm'));
include_once(FILENAME_BODY);

if(!check_login("recruiter"))
{
 $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
 tep_redirect(FILENAME_RECRUITER_LOGIN);
}
$recruiter_id=$_SESSION['sess_recruiterid'];

$action = (isset($_GET['action']) ? $_GET['action'] : '');
$_GET['page'] = ((int)$_GET['page'] > 0 ? (int)$_GET['page'] : '1');
unset($jfInfo);

 $whereClause="";

 $today=date("Y-m-d H:i:s");

switch ($action)
{
case 'apply_jobfair':
	$now=date('Y-m-d H:i:s');
	$sql_data_array=array('recruiter_id'=>$recruiter_id,
					 'jobfair_id'=>$_GET['jobfairID'],
					 'approved'=>'No',
					 'inserted'=>'now()',
					 );
tep_db_perform(RECRUITER_JOBFAIR_TABLE, $sql_data_array);
   $messageStack->add_session(MESSAGE_MAIL_SEND, 'success');
tep_redirect(tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBFAIRS));
break;
}

 //////////////////
 ///only for sorting starts
 $sort_array=array("jf.jobfair_title","jf.jobfair_begindate","jf.jobfair_enddate","jf.jobfair_status");
 include_once(PATH_TO_MAIN_PHYSICAL_CLASS.'sort_by_clause.php');
 $obj_sort_by_clause=new sort_by_clause($sort_array,'jf.inserted desc');
 $order_by_clause=$obj_sort_by_clause->return_value;
 //print_r($obj_sort_by_clause->return_sort_array['name']);
 //print_r($obj_sort_by_clause->return_sort_array['image']);
 ///only for sorting ends

 $db_jobfair_query_raw = "select * from " . JOBFAIR_TABLE ." as jf where jf.jobfair_enddate >'$today' and jf.jobfair_status='Yes' order by ".$order_by_clause;
 //echo $db_jobfair_query_raw;
 $db_jobfair_split = new splitPageResults($_GET['page'], '10', $db_jobfair_query_raw, $db_jobfair_query_numrows);
 $db_jobfair_query = tep_db_query($db_jobfair_query_raw);
 $db_jobfair_num_row = tep_db_num_rows($db_jobfair_query);

 if($db_jobfair_num_row > 0)
 {
  $alternate=1;
  while ($jobfair = tep_db_fetch_array($db_jobfair_query))
  {
   if ( (!isset($_GET['jobfairID']) || (isset($_GET['jobfairID']) && ($_GET['jobfairID'] == $jobfair['id']))) && !isset($jfInfo) && (substr($action, 0, 3) != 'new'))
   {
    $jfInfo = new objectInfo($jobfair);
   }
   if ( (isset($jfInfo) && is_object($jfInfo)) && ($jobfair['id'] == $jfInfo->id) )
   {
    $action_image=tep_image(PATH_TO_IMAGE.'icon_arrow_right.gif',IMAGE_EDIT);
    $row_selected=' class="table-secondary dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
   }
   else
   {
    $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBFAIRS, tep_get_all_get_params(array('jobfairID','action'))).'&jobfairID='.$jobfair['id'] . '\'"';
    $action_image='<a href="' . tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBFAIRS, tep_get_all_get_params(array('jobfairID','action'))).'&jobfairID='.$jobfair['id']. '">'.tep_image(PATH_TO_IMAGE.'icon_info.gif',IMAGE_INFO).'</a>';
   }
/*****create link*/
   $ide      = $jobfair["id"];
   $jobfair_seo_name = $jobfair["jobfair_seo_name"].'-jobfair';
	$jobfair_url=tep_href_link(get_display_link($ide,$jobfair_seo_name));
/********  check approval********/
$row_check=getAnyTableWhereData(RECRUITER_JOBFAIR_TABLE,"recruiter_id='".$_SESSION['sess_recruiterid']."' and jobfair_id='".$ide."'","approved");
/***** find no of jobs in this jobfair*****/
$jobfairs_query=tep_db_query("select distinct job_id from " . JOB_JOBFAIR_TABLE." where recruiter_id='".$_SESSION['sess_recruiterid']."' and jobfair_id='".$ide."'" );
$no_of_jobs= tep_db_num_rows($jobfairs_query);
/**********************/

   $alternate++;
   $template->assign_block_vars('jobfairs', array( 'row_selected' => $row_selected,
    'title' =>    '<a href="'.$jobfair_url.' " target="_blank">'.tep_db_output($jobfair['jobfair_title']).'</a>',
    'begindate' => tep_date_veryshort(tep_db_output($jobfair['jobfair_begindate'])),
    'enddate' => tep_date_veryshort(tep_db_output($jobfair['jobfair_enddate'])),
    'status' => ($row_check['approved']=='Yes'?'<span class="text-success small fw-bold">Approved</span>':($row_check['approved']=='No'?'<span class="text-warning small fw-bold">Pending</span>':'<span class="small fw-bold">Not Applied</span>')),
    'noofjobs' => '<a href="'.tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS,'jfID='.$ide).'">'.$no_of_jobs.'</a>',
    'action' => (!tep_not_null($row_check['approved'])?'<a href="' . tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBFAIRS, tep_get_all_get_params(array('jobfairID','action'))).'&jobfairID='.$ide.'&action=apply_jobfair' . '" class="btn btn-sm btn-primary-sm">'.TEXT_INFO_APPLY_NOW.'</a>':($row_check['approved']=='No'?'Applied':'Member')),//$action_image,
    'row_selected' => $row_selected
    ));
  }
 }

 $template->assign_vars(array(
  'HEADING_TITLE'=>HEADING_TITLE,

  //'TABLE_HEADING_REFERENCE'=>"<a href='".tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBFAIRS, tep_get_all_get_params(array('sort','jobID','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][0]."' class='white'>".TABLE_HEADING_REFERENCE.$obj_sort_by_clause->return_sort_array['image'][0]."</a>",
  'TABLE_HEADING_TITLE'=>"<a href='".tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBFAIRS, tep_get_all_get_params(array('sort','jobfairID','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][0]."' class='white'>".TABLE_HEADING_TITLE.$obj_sort_by_clause->return_sort_array['image'][0]."</a>",
  'TABLE_HEADING_INSERTED'=>"<a href='".tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBFAIRS, tep_get_all_get_params(array('sort','jobfairID','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][1]."' class='white'>".TABLE_HEADING_INSERTED.$obj_sort_by_clause->return_sort_array['image'][1]."</a>",
  'TABLE_HEADING_EXPIRED'=>"<a href='".tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBFAIRS, tep_get_all_get_params(array('sort','jobfairID','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][2]."' class='white'>".TABLE_HEADING_EXPIRED.$obj_sort_by_clause->return_sort_array['image'][2]."</a>",
  'TABLE_HEADING_STATUS'=>"<a href='".tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBFAIRS, tep_get_all_get_params(array('sort','jobfairID','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][3]."' class='white'>".TABLE_HEADING_STATUS.$obj_sort_by_clause->return_sort_array['image'][3]."</a>",
  'TABLE_HEADING_JOBS'=>TABLE_HEADING_JOBS,
  'TABLE_HEADING_ACTION'=>TABLE_HEADING_ACTION,
  'count_rows'=>$db_jobfair_split->display_count($db_jobfair_query_numrows, MAX_DISPLAY_LIST_OF_JOBS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_JOBS),
  'no_of_pages'=>$db_jobfair_split->display_links($db_jobfair_query_numrows, MAX_DISPLAY_LIST_OF_JOBS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'],tep_get_all_get_params(array('page','jobID','action'))),
  'new_button'=>'',
  'hidden_fields'=>$hidden_fields,
  'new_button'=>'',
  'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
  'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
  'LEFT_HTML'=>LEFT_HTML,
  'RIGHT_HTML'=>'',
  'update_message'=>$messageStack->output()));
 $template->pparse('jobfairs');

?>