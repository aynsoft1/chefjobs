<?
/*
***********************************************************
**********# Name          : Shambhu Patnaik #**********
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
***********************************************************
*/
include_once("include_files.php");
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_RECRUITER_LIST_OF_UNREGISTERED_RESUMES);
$template->set_filenames(array('unreg_resumes' => 'list_of_unreg_resumes.htm'));
include_once(FILENAME_BODY);
if(!check_login("recruiter"))
{
 $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
 tep_redirect(FILENAME_RECRUITER_LOGIN);
}
$_GET['page'] = ((int)$_GET['page'] > 0 ? (int)$_GET['page'] : '1');
//print_r($row);

///*****check whether apply without login is true or not*/
	if($row_check_login=getAnyTableWhereData(RECRUITER_TABLE ,"recruiter_id='".$_SESSION['sess_recruiterid']."'","recruiter_applywithoutlogin"))
		$direct_login=($row_check_login['recruiter_applywithoutlogin']=='Yes'?'Yes':'No');
///*****check whether apply without login is true or not*/

////////////////////////////////////////////////////
if($_GET['data_delete']=="ResultDelete")
{

	$applicantnl_id=explode(",",$_GET['applicantnl_id']);
	for($i=0;$i<count($applicantnl_id);$i++)
 tep_db_query("delete from ".APPLICANT_NOLOGIN_TABLE." where applicantnl_id='".$applicantnl_id[$i]."' and recruiter_id='".$_SESSION['sess_recruiterid']."'");
 $messageStack->add_session(MESSAGE_SUCCESS_DELETED,'success');
 tep_redirect(FILENAME_RECRUITER_LIST_OF_UNREGISTERED_RESUMES);
}
///////******************************/////////////////////////////////////////

 //////////////////
 ///only for sorting starts
 $sort_array=array("j.job_title,anl.applicantnl_name","anl.applicantnl_email","anl.inserted");
 include_once(PATH_TO_MAIN_PHYSICAL_CLASS.'sort_by_clause.php');
 $obj_sort_by_clause=new sort_by_clause($sort_array,'anl.inserted desc');
 $order_by_clause=$obj_sort_by_clause->return_value;
 //print_r($obj_sort_by_clause->return_sort_array['name']);
 //print_r($obj_sort_by_clause->return_sort_array['image']);
 ///only for sorting ends
$field_namesnl="j.job_title,anl.applicantnl_id, anl.job_id,anl.recruiter_id, anl.applicantnl_name, anl.applicantnl_email, anl.applicantnl_msg, anl.applicantnl_resume, anl.inserted ";
$table_namesnl=APPLICANT_NOLOGIN_TABLE." as anl left join  ".JOB_TABLE." as j on  (j.job_id=anl.job_id)";
$whereClausenl="j.recruiter_id =".$_SESSION['sess_recruiterid']."";

 $db_jobfair_query_raw = "select $field_namesnl from $table_namesnl where $whereClausenl ORDER BY ".$order_by_clause;
// echo $db_jobfair_query_raw;
 $db_jobfair_split = new splitPageResults($_GET['page'], '10', $db_jobfair_query_raw, $db_jobfair_query_numrows);
 $db_jobfair_query = tep_db_query($db_jobfair_query_raw);
 $db_jobfair_num_row = tep_db_num_rows($db_jobfair_query);
 if($db_jobfair_num_row > 0)
 {
  $alternate=1;
  while ($row = tep_db_fetch_array($db_jobfair_query))
  {
	$ide=$row['applicantnl_id'];
/*************************************************/
$app_resume=$row['applicantnl_resume'];
$applicant_resume='';
if(tep_not_null($app_resume))
{
 if(is_file(PATH_TO_MAIN_PHYSICAL_APPLY_NOLOGIN_RESUME.$app_resume))
 {
  $applicant_resume="<a href='".tep_href_link(PATH_TO_APPLY_RESUME_NOLOGIN.$row['applicantnl_resume'])."'>Download</a>";
}
else
	$applicant_resume='';
}
/*****************************************************/
   $alternate++;
     $template->assign_block_vars('applicantnl_result', array(
   'job_title'     => tep_db_output($row['job_title']),
   'name'          => tep_db_output($row['applicantnl_name']),
   'email_address' =>tep_db_output($row['applicantnl_email']),
   'inserted'      => tep_date_veryshort($row['inserted']),
   'view'          => $applicant_resume,
   'delete'        => "<a href='#'  onClick=goRemove('".FILENAME_RECRUITER_LIST_OF_UNREGISTERED_RESUMES."','applicantnl_id','ResultDelete','$ide');return false;>".tep_db_output("Delete")."</a>",
   'row_selected'=>$row_selected,
   ));
  }
 }

 $template->assign_vars(array(
  'HEADING_TITLE'=>HEADING_TITLE,

'TABLE_HEADING_JOB_TITLE'=>"<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][0]."','".$lower."');\"><u>".TABLE_HEADING_JOB_TITLE.'</u>'.$obj_sort_by_clause->return_sort_array['image'][0]."</a>",
                             'TABLE_HEADING_APPLICANTNL_NAME'=>"<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][1]."','".$lower."');\"><u>".TABLE_HEADING_APPLICANTNL_NAME.'</u>'.$obj_sort_by_clause->return_sort_array['image'][1]."</a>",
                             'TABLE_HEADING_APPLICANTNL_EMAIL_ADDRESS'=>"<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][2]."','".$lower."');\"><u>".TABLE_HEADING_APPLICANTNL_EMAIL_ADDRESS.'</u>'.$obj_sort_by_clause->return_sort_array['image'][2]."</a>",
                             'TABLE_HEADING_INSERTED'=>"<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][3]."','".$lower."');\"><u>".TABLE_HEADING_INSERTED.'</u>'.$obj_sort_by_clause->return_sort_array['image'][3]."</a>",
 'TABLE_HEADING_RESUME_VIEW'=>TABLE_HEADING_RESUME_VIEW,
'TABLE_HEADING_RESUME_DELETE'=>TABLE_HEADING_RESUME_DELETE,
 'count_rows'=>$db_jobfair_split->display_count($db_jobfair_query_numrows, MAX_DISPLAY_LIST_OF_JOBS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_JOBS),
  'no_of_pages'=>$db_jobfair_split->display_links($db_jobfair_query_numrows, MAX_DISPLAY_LIST_OF_JOBS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'],tep_get_all_get_params(array('page','jobID','action'))),
  'new_button'=>'',
 'INFO_TEXT_LIST_OF_APPLICATIONS'=>($direct_login=='Yes'?tep_draw_form('search_applicant',FILENAME_RECRUITER_SEARCH_APPLICANT,'','post').tep_draw_hidden_field('action1','search').'<button class="btn btn-text text-dark border" type="submit">'.INFO_TEXT_LIST_OF_APPLICATIONS.'</button></form>':''),

  'hidden_fields'=>$hidden_fields,
  'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
  'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
  'LEFT_HTML'=>LEFT_HTML,
  'RIGHT_HTML'=>'',
  'update_message'=>$messageStack->output()));
 $template->pparse('unreg_resumes');
?>