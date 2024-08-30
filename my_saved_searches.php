<?
include_once("include_files.php");
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_JOBSEEKER_LIST_OF_SAVED_SEARCHES);
$template->set_filenames(array('saved_search' => 'my_saved_searches.htm'));
include_once(FILENAME_BODY);
$template->assign_vars(array('HEADING_TITLE'=>HEADING_TITLE));
if(!check_login("jobseeker"))
{
 $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
 tep_redirect(FILENAME_JOBSEEKER_LOGIN);
}
$sID = (isset($_GET['sID']) ? $_GET['sID'] : '');
if(tep_not_null($sID))
{
 if(!$row_check=getAnyTableWhereData(SEARCH_JOB_RESULT_TABLE,"jobseeker_id='".$_SESSION['sess_jobseekerid']."' and id='".tep_db_input($sID)."'",'id'))
 {
  $messageStack->add_session(MESSAGE_ERROR_SAVED_SERCH_NOT_EXIST,'error');
  tep_redirect(FILENAME_JOBSEEKER_LIST_OF_SAVED_SEARCHES);
 }
 if($_GET['action']=='delete')
 {
  tep_db_query("delete from ".SEARCH_JOB_RESULT_TABLE." where id='".$sID."'");
  $messageStack->add_session(MESSAGE_SUCCESS_DELETE,'success');
  tep_redirect(FILENAME_JOBSEEKER_LIST_OF_SAVED_SEARCHES);
 }
}
 $lower=(int)tep_db_prepare_input($_POST['lower']);
 $higher=(int)tep_db_prepare_input($_POST['higher']);

////////////////
$whereClause="where jobseeker_id='".$_SESSION['sess_jobseekerid']."'";
$query1 = "select count(id ) as x1 from ".SEARCH_JOB_RESULT_TABLE." $whereClause";
$result1=tep_db_query($query1);
$tt_row=tep_db_fetch_array($result1);
$x1=$tt_row['x1'];//echo $query1;
//echo $x1;die();
///only for sorting starts
include_once(PATH_TO_MAIN_PHYSICAL_CLASS.'sort_by_clause.php');
$sort_array=array("title_name",'inserted');
$obj_sort_by_clause=new sort_by_clause($sort_array,'inserted desc');
$order_by_clause=$obj_sort_by_clause->return_value;
$see_before_page_number_array=see_before_page_number($sort_array,$field,'inserted',$order,'desc',$lower,'0',$higher,'3');
$lower=$see_before_page_number_array['lower'];
$higher=$see_before_page_number_array['higher'];
$field=$see_before_page_number_array['field'];
$order=$see_before_page_number_array['order'];
$hidden_fields.=tep_draw_hidden_field('sort',$sort);
$totalpage=ceil($x1/$higher);
$query = "select * from ".SEARCH_JOB_RESULT_TABLE." $whereClause ORDER BY $field $order limit $lower,$higher ";
$result=tep_db_query($query);//echo "<br>$query";//exit;
$x=tep_db_num_rows($result);//echo $x;exit;
$pno= ceil($lower+$higher)/($higher);
if($x > 0 && $x1 > 0)
{  
 //$t->set_block("var","row","rows");
 $alternate=1;
 $rowcount=1;
 while($row =  tep_db_fetch_array($result))
 {
  $ide=$row["id"];
  $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
  $alternate++;
  $hidden_fields1='';
  
  if(tep_not_null($row['keyword']))
  {
   $hidden_fields1.=tep_draw_hidden_field("keyword",$row['keyword']);
  }
  if(tep_not_null($row['location']))
  {
   $hidden_fields1.=tep_draw_hidden_field("location",$row['location']);
  }
  if(tep_not_null($row['word1']))
  {
   $hidden_fields1.=tep_draw_hidden_field("word1",$row['word1']);
  }
  if(tep_not_null($row['country']))
  {
   $hidden_fields1.=tep_draw_hidden_field("country",$row['country']);
  }
  if(tep_not_null($row['state']))
  {
   $hidden_fields1.=tep_draw_hidden_field("state",$row['state']);
  }
  if(tep_not_null($row['zip_code']))
  {
   $hidden_fields1.=tep_draw_hidden_field("zip_code",$row['zip_code']);
   $hidden_fields1.=tep_draw_hidden_field("radius",$row['radius']);
   $hidden_fields1.=tep_draw_hidden_field("search_zip_code",2);
  }
  if(tep_not_null($row['company']))
  {
   $hidden_fields1.=tep_draw_hidden_field("company",$row['company']);
  }
  if(tep_not_null($row['industry_sector']))
  {
   $industry_sector=explode(",",$row['industry_sector']);
   for($i=0;$i<count($industry_sector);$i++)
   {
    $hidden_fields1.=tep_draw_hidden_field("job_category[]",$industry_sector[$i]);
   }
  }
  if(tep_not_null($row['experience']))
  {
   $hidden_fields1.=tep_draw_hidden_field("experience",$row['experience']);
  }
  $run_form= tep_draw_form("search".$rowcount,FILENAME_JOB_SEARCH.'?sID='.$ide); 
  $edit_form= tep_draw_form("search_edit".$rowcount,FILENAME_JOB_SEARCH.'?sID='.$ide); 


  $template->assign_block_vars('list_job_save_result', array( 'row_selected'   => $row_selected,
                                                              'title'          => tep_db_output($row['title_name']),
                                                              'inserted'       => tep_date_short($row['inserted']),
                                                              'updated'        => tep_date_short($row['updated']),
                                                              'run'            => tep_draw_hidden_field('action','search').'<a href="#" onclick="document.search'.$rowcount.'.submit();">'. tep_db_output("Run").'</a>',
                                                              'edit'           => tep_draw_hidden_field('action','search_edit').'<a href="#" onclick="document.search_edit'.$rowcount.'.submit();">'.TABLE_HEADING_EDIT.'</a>',
                                                              'hidden_fields1' =>$hidden_fields1,
                                                              'run_form'       =>$run_form,
			                                                   'edit_form'      =>$edit_form,
                                                              'delete'         => "<a href='#' onClick=encode_delete('".FILENAME_JOBSEEKER_LIST_OF_SAVED_SEARCHES."?sID=".$ide."&action=delete');return false; >". tep_db_output("Delete")." </a>" ,
                                                              ));
  $lower = $lower + 1;
  $rowcount++;
 }
 see_page_number(); 
 $template->assign_vars(array('total'=>INFO_TEXT_YOU_HAVE." ".$x1 ." ".INFO_TEXT_SAVED_SEARCH));
}
else
{
 $template->assign_vars(array('total'=>INFO_TEXT_NO_SAVE_RESULT));
}
tep_db_free_result($result);
tep_db_free_result($result1);
$template->assign_vars(array(
 'HEADING_TITLE'=>HEADING_TITLE,
 'TABLE_HEADING_TITLE_NAME'=>TABLE_HEADING_TITLE_NAME,
 'TABLE_HEADING_INSERT_DATE'=>TABLE_HEADING_INSERT_DATE,
 'TABLE_HEADING_UPDATE_DATE'=>TABLE_HEADING_UPDATE_DATE,
 'TABLE_HEADING_RUN'=>TABLE_HEADING_RUN,
	'TABLE_HEADING_EDIT'=>TABLE_HEADING_EDIT,
	'TABLE_HEADING_DELETE'=>TABLE_HEADING_DELETE,
 'INFO_TEXT_GET_DAILY_JOB_ALERTS'=> INFO_TEXT_GET_DAILY_JOB_ALERTS,
 'INFO_TEXT_SAVE_SEARCH'  =>INFO_TEXT_SAVE_SEARCH,
 'INFO_TEXT_JOB_SEARCH_CRITERIA'=>INFO_TEXT_JOB_SEARCH_CRITERIA,
 'INFO_TEXT_JOB_ALERT' =>INFO_TEXT_JOB_ALERT,
 'INFO_TEXT_WILL_SEND_EMAIL_NOTIFICATION'=>INFO_TEXT_WILL_SEND_EMAIL_NOTIFICATION,
 'hidden_fields' =>$hidden_fields,
 'add_new'=>"<a class='btn btn-sm btn-primary mmt-15' href='".tep_href_link(FILENAME_JOB_ALERT_AGENT)."'><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-plus-lg' viewBox='0 0 16 16'>
 <path fill-rule='evenodd' d='M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2Z'/>
</svg> ".INFO_TEXT_CREATE_NEW_JOB_ALERT."</a>",
 'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
 'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
 'LEFT_HTML'=>'',
//'JOB_SEARCH_LEFT'=>JOB_SEARCH_LEFT,
'LEFT_HTML'=>LEFT_HTML_JOBSEEKER,
 'RIGHT_HTML'=>RIGHT_HTML,
 'update_message'=>$messageStack->output()));
 $template->pparse('saved_search');
?>