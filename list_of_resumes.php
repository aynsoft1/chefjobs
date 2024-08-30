<?
/*
***********************************************************
**********# Name          : Shambhu Patnaik #**********
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
***********************************************************
*/
include_once("include_files.php");
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_RECRUITER_SAVE_RESUME);
$template->set_filenames(array('resumes' => 'list_of_resumes.htm'));
include_once(FILENAME_BODY);
if(!check_login("recruiter"))
{
 $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
 tep_redirect(FILENAME_RECRUITER_LOGIN);
}
$rate_it = (isset($_POST['rate_it']) ? $_POST['rate_it'] : '0');
$_GET['page'] = ((int)$_GET['page'] > 0 ? (int)$_GET['page'] : '1');
//print_r($row);
////////////////////////////////////////////////////
if($_GET['data_delete']=="ResultDelete")
{

	$s_id=explode(",",$_GET['s_id']);
	for($i=0;$i<count($s_id);$i++)
 tep_db_query("delete from ".SAVE_RESUME_TABLE." where id='".$s_id[$i]."' and recruiter_id='".$_SESSION['sess_recruiterid']."'");
 $messageStack->add_session(MESSAGE_SUCCESS_DELETED,'success');
 tep_redirect(FILENAME_RECRUITER_SAVE_RESUME);
}
////////////////////////////////////////////////

$field_names="s.id,jl.jobseeker_email_address,concat(j.jobseeker_first_name,' ',j.jobseeker_last_name) as jobseeker_name,j.jobseeker_privacy,s.inserted,s.resume_id,rr.point ";
$table_names=SAVE_RESUME_TABLE." as s left join  ".RECRUITER_TABLE." as r on  (r.recruiter_id=s.recruiter_id) left join  ".JOBSEEKER_RESUME1_TABLE." as jr1 on  (s.resume_id=jr1.resume_id) left outer join ". JOBSEEKER_TABLE." as j on (jr1.jobseeker_id= j.jobseeker_id) left outer join  ".JOBSEEKER_LOGIN_TABLE." as jl on  (jl.jobseeker_id=j.jobseeker_id) left join ".JOBSEEKER_RATING_TABLE." as rr on (s.resume_id = rr.resume_id  && s.recruiter_id = rr.recruiter_id)";
$whereClause="s.recruiter_id =".$_SESSION['sess_recruiterid']."";
if($rate_it > 0)
$whereClause.=" and rr.point ='".$rate_it."'";
$query1 = "select count(s.id) as x1 from $table_names where $whereClause ";
//echo "<br>$query1";//exit;
$result1=tep_db_query($query1);
$tt_row=tep_db_fetch_array($result1);
$x1=$tt_row['x1'];
//echo $x1;//exit;
//////////////////
///only for sorting starts
include_once(PATH_TO_MAIN_PHYSICAL_CLASS.'sort_by_clause.php');
$sort_array=array("jobseeker_name","jl.jobseeker_email_address","rr.point ","s.inserted");
$obj_sort_by_clause=new sort_by_clause($sort_array,'s.inserted  desc,rr.point asc ');
$order_by_clause=$obj_sort_by_clause->return_value;
$see_before_page_number_array=see_before_page_number($sort_array,$field,'s.inserted  desc, rr.point  asc ',$order,'asc',$lower,'0',$higher,'30');
$lower=$see_before_page_number_array['lower'];
$higher=$see_before_page_number_array['higher'];
$field=$see_before_page_number_array['field'];
$order=$see_before_page_number_array['order'];
$hidden_fields.=tep_draw_hidden_field('sort',$sort);
$template->assign_vars(array('TABLE_HEADING_JOBSEEKER_NAME'=>"<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][0]."','".$lower."');\"><u>".TABLE_HEADING_JOBSEEKER_NAME.'</u>'.$obj_sort_by_clause->return_sort_array['image'][0]."</a>",
                             'TABLE_HEADING_JOBSEEKER_EMAIL_ADDRESS'=>"<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][1]."','".$lower."');\"><u>".TABLE_HEADING_JOBSEEKER_EMAIL_ADDRESS.'</u>'.$obj_sort_by_clause->return_sort_array['image'][1]."</a>",
                             'TABLE_HEADING_RESUME_RATING'=>"<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][2]."','".$lower."');\"><u>".TABLE_HEADING_RESUME_RATING.'</u>'.$obj_sort_by_clause->return_sort_array['image'][2]."</a>",
                             'TABLE_HEADING_INSERTED'=>"<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][3]."','".$lower."');\"><u>".TABLE_HEADING_INSERTED.'</u>'.$obj_sort_by_clause->return_sort_array['image'][3]."</a>"
));
///only for sorting ends
$totalpage=ceil($x1/$higher);
$query = "select $field_names from $table_names where $whereClause ORDER BY ".$order_by_clause." limit $lower,$higher";
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
  $ide=$row["id"];
  $query_string1=encode_string("search_id==".$row["resume_id"]."==search");
  $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
  // $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,'query_string1='.$query_string1) . '\'"';
  $now=date("Y-m-d");
  $template->assign_block_vars('search_resume_result', array( 
   'name'          => tep_db_output($row['jobseeker_name']),
   'email_address' => ($row['jobseeker_privacy']==3?tep_db_output($row['jobseeker_email_address']):'*****'),//tep_db_output($row['jobseeker_email_address']),
   'rating'        => ($row['point']>0)?tep_db_output($row['point']):'not rated',
   'inserted'      => tep_date_veryshort($row['inserted']),
   'view'          => '<a   href="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,'query_string1='.$query_string1).'"><u>'.TABLE_HEADING_RESUME_VIEW.'</u></a>',
   'delete'        => "<a href='#'  onClick=goRemove('".FILENAME_RECRUITER_SAVE_RESUME."','s_id','ResultDelete','$ide');return false;>".tep_db_output("Delete")."</a>",
   'row_selected'=>$row_selected,
   ));
  $alternate++;
  $lower = $lower + 1;
 }
 $plural=($x1=="1")?INFO_TEXT_RESUME:INFO_TEXT_RESUMES;
 $template->assign_vars(array('total'=>tep_db_output(SITE_TITLE).' '.INFO_TEXT_HAS_SAVED." <b>$x1</b> ".$plural.' '.INFO_TEXT_TO_YOUR_SEARCH_CRITERIA ));
}
else
{
 $template->assign_vars(array('total'=>tep_db_output(SITE_TITLE).' '.INFO_TEXT_HAS_NOT_SAVED));
}
see_page_number();
tep_db_free_result($result1);
////////////////////////////////////////////////////////// 
$rate_it_array=array();
$rate_it_array[]=array("id"=>0,"text"=>INFO_TEXT_ALL);
for($i=1;$i<=10;$i++)
{
  $rate_it_array[]=array("id"=>$i,"text"=>$i);
}
$rate_it_string=tep_draw_pull_down_menu('rate_it', $rate_it_array, $rate_it, ' onchange="document.page.submit()" class="form-select form-select-sm"', false);

$template->assign_vars(array(
'HEADING_TITLE'=>HEADING_TITLE,
'Rating'=>INFO_TEXT_RATING,
'TABLE_HEADING_RESUME_VIEW'=>TABLE_HEADING_RESUME_VIEW,
'TABLE_HEADING_RESUME_DELETE'=>TABLE_HEADING_RESUME_DELETE,
'new_button'=>'',
'rate_it_string'=>$rate_it_string,
'hidden_fields'=>$hidden_fields,
'new_button'=>'',
'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
'RIGHT_BOX_WIDTH'=>RIGHT_BOX_WIDTH1,
'LEFT_HTML'=>LEFT_HTML,
'RIGHT_HTML'=>RIGHT_HTML,
'update_message'=>$messageStack->output()));
 $template->pparse('resumes');
?>