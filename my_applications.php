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
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_JOBSEEKER_LIST_OF_APPLICATIONS);
$template->set_filenames(array('applications' => 'my_applications.htm'));
include_once(FILENAME_BODY);
if(!check_login("jobseeker"))
{
	$_SESSION['REDIRECT_URL']=$_SERVER['REQUEST_URI'];
 $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
 tep_redirect(FILENAME_JOBSEEKER_LOGIN);
}

$query_strings=$_GET['query_strings'];
if(tep_not_null($query_strings))
{
 $application_id=check_data($query_strings,"=","apply_id","apply_id");
 if($row=getAnyTableWhereData(APPLY_TABLE." as a left outer join ".JOB_TABLE." as j on (a.job_id=j.job_id)","a.job_id=j.job_id and a.id='".$application_id."' and a.jobseeker_id='".$_SESSION['sess_jobseekerid']."'","a.job_id,a.resume_name"))
 {
  //tep_db_query("update ".JOB_STATISTICS_TABLE." set applications=(applications-1) where job_id='".$row['job_id']."'");
  //@unlink(PATH_TO_MAIN_PHYSICAL_APPLY_RESUME.$row['resume_name']);
  //tep_db_query("delete from ".APPLY_TABLE." where id='".$application_id."'");
   tep_db_query("update ".APPLY_TABLE." set jobseeker_apply_status='inactive' where id='".$application_id."'");

  $messageStack->add_session(SUCCESS_APPLICATION_DELETED, 'error');
  tep_redirect(FILENAME_JOBSEEKER_LIST_OF_APPLICATIONS);
 }
 else //hacking attempt
 {
  $messageStack->add_session(ERROR_APPLICATION, 'error');
  tep_redirect(FILENAME_ERROR);
 }
}
$template->assign_vars(array('HEADING_TITLE'=>HEADING_TITLE));

$table_names=APPLY_TABLE.' as a left outer join  '.JOB_TABLE." as j on (a.job_id=j.job_id) left join ".APPLICATION_TABLE." as ap on (ap.jobseeker_apply_id =a.id) ";
$whereClause.="a.job_id=j.job_id and a.jobseeker_id='".$_SESSION['sess_jobseekerid']."' and jobseeker_apply_status='active'";
$field_names="a.id,a.inserted,j.job_id, j.recruiter_id, j.job_title,j.job_reference,j.re_adv,application_id,ap.id as application";
$query1 = "select count(a.id) as x1 from $table_names where $whereClause ";
//echo "<br>$query1";//exit;
$result1=tep_db_query($query1);
$tt_row=tep_db_fetch_array($result1);
$x1=$tt_row['x1'];
//echo $x1;
//////////////////
///only for sorting starts
include_once(PATH_TO_MAIN_PHYSICAL_CLASS.'sort_by_clause.php');
$sort_array=array("j.job_reference",'j.job_title','j.re_adv','a.inserted');
$obj_sort_by_clause=new sort_by_clause($sort_array,'a.inserted desc');
$order_by_clause=$obj_sort_by_clause->return_value;
//print_r($obj_sort_by_clause->return_sort_array['name']);
//print_r($obj_sort_by_clause->return_sort_array['image']);
$see_before_page_number_array=see_before_page_number123($sort_array,$field,'a.inserted',$order,'desc',$lower,'0',$higher,'20');
$lower=$see_before_page_number_array['lower'];
$higher=$see_before_page_number_array['higher'];
$field=$see_before_page_number_array['field'];
$order=$see_before_page_number_array['order'];
$hidden_fields.=tep_draw_hidden_field('sort');
$template->assign_vars(array('TABLE_HEADING_JOB_REFERENCE'=>"<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][0]."','".$lower."');\"><u>".TABLE_HEADING_JOB_REFERENCE.'</u>'.$obj_sort_by_clause->return_sort_array['image'][0]."</a>",
'TABLE_HEADING_JOB_TITLE'=>"<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][1]."','".$lower."');\"><u>".TABLE_HEADING_JOB_TITLE.'</u>'.$obj_sort_by_clause->return_sort_array['image'][1]."</a>",
'TABLE_HEADING_APPLICATION_ID'=>TABLE_HEADING_APPLICATION_ID,
'TABLE_HEADING_APPLIED_FOR'=>TABLE_HEADING_APPLIED_FOR,
'TABLE_HEADING_JOB_INSERTED'=>"<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][2]."','".$lower."');\"><u>".TABLE_HEADING_JOB_INSERTED.'</u>'.$obj_sort_by_clause->return_sort_array['image'][2]."</a>",
'TABLE_HEADING_APPLICATION_INSERTED'=>"<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][3]."','".$lower."');\"><u>".TABLE_HEADING_APPLICATION_INSERTED.'</u>'.$obj_sort_by_clause->return_sort_array['image'][3]."</a>",
'TABLE_HEADING_APPLICATION_REAPPLY'=>TABLE_HEADING_APPLICATION_REAPPLY,
'TABLE_HEADING_APPLICATION_DELETE'=>TABLE_HEADING_APPLICATION_DELETE,
'JOB_SEARCH_LEFT'   => JOB_SEARCH_LEFT,
  'LEFT_HTML'=>LEFT_HTML_JOBSEEKER,

));
///only for sorting ends

$totalpage=ceil($x1/$higher);
$query = "select $field_names from $table_names where $whereClause ORDER BY ".$order_by_clause." limit $lower,$higher ";
$result=tep_db_query($query);
//echo "<br>$query";//exit;
$x=tep_db_num_rows($result);
//echo $x;exit;
$pno= ceil($lower+$higher)/($higher);
if($x > 0 && $x1 > 0)
{
 $alternate=1;
 while($row = tep_db_fetch_array($result))
 {
  $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
  $alternate++;
  $ide=$row["job_id"];
  $company_applied_row=getAnyTableWhereData(RECRUITER_TABLE,"recruiter_id='".$row['recruiter_id']."'","recruiter_company_name");
  $company_app=$company_applied_row['recruiter_company_name'];
  $title_format=encode_category($row['job_title']);
  $query_string=encode_string("job_id=".$ide."=job_id");
  $delete_string=encode_string("apply_id=".$row['id']."=apply_id");
  $template->assign_block_vars('applications', array('row_selected'=>$row_selected,
   'reference' => tep_db_output($row['job_reference']),
   'title' => '<a href="'.tep_href_link($ide.'/'.$title_format.'.html').'">'.tep_db_output($row['job_title']).'</a>',
   'application_id' =>tep_db_output($row['application_id']),
   //'application_status' =>tep_db_output(get_current_round_name($row['application'])),
   'inserted' => tep_date_short($row['re_adv']),
   'applied_for'=>$company_app,
   'application_inserted' => tep_date_short($row['inserted']),
   'application_reapply' => '<a class="" href="'.tep_href_link(FILENAME_APPLY_NOW,'query_string='.$query_string).'">'.TABLE_HEADING_APPLICATION_REAPPLY.'</a>',
			'application_delete'  => '<a class="text-danger" href="#" onclick=\'encode_delete1("'.FILENAME_JOBSEEKER_LIST_OF_APPLICATIONS.'","'.$delete_string.'")\';return false;">'.TABLE_HEADING_APPLICATION_DELETE.'</a>',
   ));
  $lower = $lower + 1;
 }
 see_page_number();
 $plural=($x1=="1")?INFO_TEXT_APPLICATION:INFO_TEXT_APPLICATIONS;
 $template->assign_vars(array('total'=>" <b>$x1</b> ".$plural." ."));
}
else
{
 $hidden_fields.=tep_draw_hidden_field('lower');
 $template->assign_vars(array('total'=>tep_db_output(SITE_TITLE)."  ".INFO_TEXT_HAS_NOT_FOUND));
}
tep_db_free_result($result);
tep_db_free_result($result1);
$template->assign_vars(array('RIGHT_HTML' => RIGHT_HTML,
 'hidden_fields' => $hidden_fields,
 'update_message' => $messageStack->output()));
$template->pparse('applications');
?>