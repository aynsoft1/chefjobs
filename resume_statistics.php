<?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik   #**********
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
***********************************************************
*/
include_once("include_files.php");
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_RESUME_STATISTICS);
$template->set_filenames(array('statistics' => 'resume_statistics.htm'));
include_once(FILENAME_BODY);
if(!check_login("jobseeker"))
{
 $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
 tep_redirect(FILENAME_LOGIN);
}
############### RESUME STATISTICS LISTING ###############
$whereClause="resume_id in (select resume_id from ".JOBSEEKER_RESUME1_TABLE." where jobseeker_id=".$_SESSION['sess_jobseekerid'].")";
$query1 = "select * from ".SAVE_RESUME_TABLE." where $whereClause ";
//echo "<br>$query1";//exit;
$result1=tep_db_query($query1);
$tt_row=tep_db_fetch_array($result1);
///only for sorting starts
include_once(PATH_TO_MAIN_PHYSICAL_CLASS.'sort_by_clause.php');
$sort_array=array("inserted");
$obj_sort_by_clause=new sort_by_clause($sort_array,'inserted desc');
$order_by_clause=$obj_sort_by_clause->return_value;
$see_before_page_number_array=see_before_page_number($sort_array,$field,'inserted ',$order,'desc',$lower,'0',$higher,'20');
$lower=$see_before_page_number_array['lower'];
$higher=$see_before_page_number_array['higher'];
$field=$see_before_page_number_array['field'];
$order=$see_before_page_number_array['order'];
$hidden_fields.=tep_draw_hidden_field('sort',$sort);
//$template->assign_vars(array('INFO_TEXT_JOBSEEKER_NAME'=>"<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][0]."','".$lower."');\"><u>".INFO_TEXT_JOBSEEKER_NAME.'</u>'.$obj_sort_by_clause->return_sort_array['image'][0]."</a>",
  //                            'INFO_TEXT_JOBSEEKER_EMAIL_ADDRESS'=>"<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][1]."','".$lower."');\"><u>".INFO_TEXT_JOBSEEKER_EMAIL_ADDRESS.'</u>'.$obj_sort_by_clause->return_sort_array['image'][1]."</a>"
//));
///only for sorting ends
$totalpage=ceil($x1/$higher);
$whereClause1="resume_id in (select resume_id from ".JOBSEEKER_RESUME1_TABLE." where jobseeker_id=".$_SESSION['sess_jobseekerid'].")";
$query = "select * from ".SAVE_RESUME_TABLE." where $whereClause1 ORDER BY $field $order limit $lower,$higher ";
$result=tep_db_query($query);
//echo "<br>$query";//exit;
$x=tep_db_num_rows($result);
//echo $x;exit;
$pno= ceil($lower+$higher)/($higher);
if($x > 0)
{
 $alternate=1;
 while($row = tep_db_fetch_array($result))
 {
  $ide=$row["id"];
		$resume_name=getAnyTableWhereData(JOBSEEKER_RESUME1_TABLE,"resume_id=".$row['resume_id']."","resume_title");
		$company_name=getAnyTableWhereData(RECRUITER_TABLE,"recruiter_id=".$row['recruiter_id']."","recruiter_company_name");
  $row_selected=' class="dataTableRow'.($alternate%2==1?'2':'1').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" ';
  $template->assign_block_vars('statistics', array(
   'resume_name'  => tep_db_output($resume_name['resume_title']),
   'company_name' => tep_db_output($company_name['recruiter_company_name']),
   'viewed_on'    => tep_date_short($row['inserted']),
   'row_selected'=>$row_selected,
   ));
  $alternate++;
  $lower = $lower + 1;
 }
 $plural=($x1=="1")?HEADING_TITLE:INFO_TEXT_STATISTICS;
 $template->assign_vars(array('total'=>"  <font color='red'><b>$x</b></font> ".$plural));
}
else
{
 $template->assign_vars(array('total'=>INFO_TEXT_NO_STATISTICS_AVAILABLE));
}
see_page_number();
tep_db_free_result($result1);
############### RESUME STATISTICS LISTING ###############
$template->assign_vars(array(
 'HEADING_TITLE'=>HEADING_TITLE,
 'TABLE_HEADING_RESUME_NAME'=>TABLE_HEADING_RESUME_NAME,
 'TABLE_HEADING_INSERTED'=>TABLE_HEADING_INSERTED,
 'TABLE_HEADING_COMPANY_NAME'=>TABLE_HEADING_COMPANY_NAME,
 'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
 'RIGHT_BOX_WIDTH'=>RIGHT_BOX_WIDTH1,
 'LEFT_HTML'=>LEFT_HTML_JOBSEEKER,
 'RIGHT_HTML'=>RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('statistics');

?>