<?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik   #**********
**********# Company       : Aynsoft                #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
***********************************************************
*/
include_once("../include_files.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_JOBSEEKER_PRINT);
$template->set_filenames(array('jobseekers' => 'admin1_print_jobseekers.htm',
                               'print_fields' => 'admin1_print_jobseekers1.htm',
                               'print' => 'admin1_print_jobseekers2.htm'));
include_once(FILENAME_ADMIN_BODY);
//print_r($_GET);
$action = (isset($_GET['action']) ? $_GET['action'] : '');
$print = (isset($_GET['print']) ? $_GET['print'] : '');
if($print=='print'  || $print=='excel')
{
 //$print=true;
}
else
{
 $print=false;
}
$hidden_fields='';
switch ($action)
{
 case 'print':
  $action=tep_db_prepare_input($_GET['action']);
  $hidden_fields.=tep_draw_hidden_field('action',$action);
  //$hidden_fields.=tep_draw_hidden_field('print');
  ///only for sorting starts
  $sort_array=array("name","jl.jobseeker_email_address","contact_no","address");
  include_once(PATH_TO_MAIN_PHYSICAL_CLASS.'sort_by_clause.php');
  $obj_sort_by_clause=new sort_by_clause($sort_array,'jl.inserted desc');
  $order_by_clause=$obj_sort_by_clause->return_value;
  //echo $order_by_clause;
  //print_r($obj_sort_by_clause->return_sort_array['name']);
  //print_r($obj_sort_by_clause->return_sort_array['image']);
   $see_before_page_number_array=see_before_page_number($sort_array,$field,'jl.inserted',$order,'desc',$lower,'0',$higher,'20');
   $lower=$see_before_page_number_array['lower'];
   $higher=$see_before_page_number_array['higher'];
   $field=$see_before_page_number_array['field'];
   $order=$see_before_page_number_array['order'];
   $hidden_fields.=tep_draw_hidden_field('sort',$sort);

  ///only for sorting ends
  $fields=array();
  $header_row='';
  $name=$_GET['name'];
  $email=$_GET['email_address'];
  $address=$_GET['address'];
  $contact_no=$_GET['contact_no'];
  $csv_file = array();
  if(tep_not_null($name))
  {
   $hidden_fields.=tep_draw_hidden_field('name',$name);
   if($print)
    $header_row.='<td><b><u>'.TABLE_HEADING_NAME.'</u></td>';
   else
    $header_row.='<td class="dataTableHeadingContent">'."<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][0]."','".$lower."');\"><u>".TABLE_HEADING_NAME.'</u>'.$obj_sort_by_clause->return_sort_array['image'][0]."</a>".'</td>';
   $fields[]="concat(j.jobseeker_first_name,' ',j.jobseeker_last_name) as name ";
   $csv_file[0][] = TABLE_HEADING_NAME;
  }
  if(tep_not_null($email))
  {
   $hidden_fields.=tep_draw_hidden_field('email_address',$email);
   if($print)
    $header_row.='<td><b><u>'.TABLE_HEADING_EMAIL.'</u></td>';
   else
    $header_row.='<td class="dataTableHeadingContent">'."<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][1]."','".$lower."');\"><u>".TABLE_HEADING_EMAIL.'</u>'.$obj_sort_by_clause->return_sort_array['image'][1]."</a>" .'</td>';
   $fields[]="jl.jobseeker_email_address as email";
   $csv_file[0][] =TABLE_HEADING_EMAIL;
  }
  if(tep_not_null($address))
  {
   $hidden_fields.=tep_draw_hidden_field('address',$address);
   if($print)
    $header_row.='<td><b><u>'.TABLE_HEADING_ADDRESS.'</u></td>';
   else
    $header_row.='<td class="dataTableHeadingContent">'.TABLE_HEADING_ADDRESS.'</td>';
   $fields[]="j.jobseeker_address1, j.jobseeker_address2, j.jobseeker_zip, j.jobseeker_city, j.jobseeker_state_id, j.jobseeker_state, j.jobseeker_country_id";
   $csv_file[0][] =TABLE_HEADING_ADDRESS;
  }
  if(tep_not_null($contact_no))
  {
   $hidden_fields.=tep_draw_hidden_field('contact_no',$contact_no);
   if($print)
    $header_row.='<td><b><u>'.TABLE_HEADING_TELEPHONE.'</u></td>';
   else
    $header_row.='<td class="dataTableHeadingContent">'."<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][2]."','".$lower."');\"><u>".TABLE_HEADING_TELEPHONE.'</u>'.$obj_sort_by_clause->return_sort_array['image'][2]."</a>" .'</td>';
   $fields[]="concat(j.jobseeker_phone,' ',j.jobseeker_mobile,' ',j.jobseeker_work_phone) as contact_no";
   $csv_file[0][] =TABLE_HEADING_TELEPHONE;
  }
  if(tep_not_null($fields))
   $fields=implode(", ",$fields);
  else
  {
   $messageStack->add_session(MESSAGE_ATLEAST_ONE_CHECKED, 'error');
   tep_redirect(FILENAME_ADMIN1_JOBSEEKER_PRINT);
  }
  $hidden_fields.=tep_draw_hidden_field('page',$_GET['page']);

  $db_jobseeker_query_raw = "select $fields from " . JOBSEEKER_LOGIN_TABLE . " as jl, " . JOBSEEKER_TABLE . " as j where jl.jobseeker_id=j.jobseeker_id order by ".$order_by_clause;
  $db_jobseeker_query_raw;
  $db_jobseeker_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $db_jobseeker_query_raw, $db_jobseeker_query_numrows);
  $db_jobseeker_query = tep_db_query($db_jobseeker_query_raw);
  $db_jobseeker_num_row = tep_db_num_rows($db_jobseeker_query);
  if($db_jobseeker_num_row > 0)
  {
   $alternate=1;
   $header_value='';
   while ($jobseeker = tep_db_fetch_array($db_jobseeker_query))
   {
    if($print)
     $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'"';
    else
     $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
	if($print=='excel')
	{
	  if(tep_not_null($name))
	  $csv_file[$alternate] [] = tep_db_output($jobseeker['name']);
	  if(tep_not_null($email))
	  $csv_file[$alternate] [] = tep_db_output($jobseeker['email']);
	  if(tep_not_null($address))
	  $csv_file[$alternate] [] = tep_db_output($jobseeker['jobseeker_address1'].(tep_not_null($jobseeker['jobseeker_address2'])?', '.$jobseeker['jobseeker_address2']:'').(tep_not_null($jobseeker['jobseeker_city'])?', '.$jobseeker['jobseeker_city']:'').(tep_not_null($jobseeker['jobseeker_zip'])?', '.$jobseeker['jobseeker_zip']:'').($jobseeker['jobseeker_state_id'] > 0?', '.get_name_from_table(ZONES_TABLE,'zone_name','zone_id',$jobseeker['jobseeker_state_id']):(tep_not_null($jobseeker['jobseeker_state'])?', '.$jobseeker['jobseeker_state']:'')).(tep_not_null($jobseeker['jobseeker_country_id'])?', '.get_name_from_table(COUNTRIES_TABLE,'country_name','id',$jobseeker['jobseeker_country_id']):''));
	  if(tep_not_null($contact_no))
	  $csv_file[$alternate] [] = tep_db_output($jobseeker['contact_no']);

	}
	else
    {
    $header_value.='<tr'.$row_selected.'>'.
      (tep_not_null($name)?'<td valign="top" class="dataTableContent">'.tep_db_output($jobseeker['name']).'</td>':'').
      (tep_not_null($email)?'<td valign="top" class="dataTableContent">'.tep_db_output($jobseeker['email']).'</td>':'').
      (tep_not_null($address)?'<td valign="top" class="dataTableContent">'.tep_db_output($jobseeker['jobseeker_address1'].(tep_not_null($jobseeker['jobseeker_address2'])?', '.$jobseeker['jobseeker_address2']:'').(tep_not_null($jobseeker['jobseeker_city'])?', '.$jobseeker['jobseeker_city']:'').(tep_not_null($jobseeker['jobseeker_zip'])?', '.$jobseeker['jobseeker_zip']:'').($jobseeker['jobseeker_state_id'] > 0?', '.get_name_from_table(ZONES_TABLE,'zone_name','zone_id',$jobseeker['jobseeker_state_id']):(tep_not_null($jobseeker['jobseeker_state'])?', '.$jobseeker['jobseeker_state']:'')).(tep_not_null($jobseeker['jobseeker_country_id'])?', '.get_name_from_table(COUNTRIES_TABLE,'country_name','id',$jobseeker['jobseeker_country_id']):'')).'</td>':'').
      (tep_not_null($contact_no)?'<td valign="top" class="dataTableContent">'.tep_db_output($jobseeker['contact_no']).'</td>':'').
     '</tr>'."\n";
	 }
	 $alternate++;

   }
  }
  see_page_number();
  if($print=='excel')
  {
	$file_name=PATH_TO_MAIN_PHYSICAL_TEMP.date("YmdHis").'jobseeker_.csv';
    $fp = fopen($file_name, 'w');
	foreach($csv_file as $data)
    fputcsv($fp,$data);
    fclose($fp);
	header('Content-type: application/x-octet-stream');
    header('Content-disposition: attachment; filename=jobseeker_report_'.date("Y-m-d-hi").'.csv' );
    readfile($file_name);
    unlink($file_name);
	exit();
	//die();
  }

  $template->assign_vars(array(
   'hidden_fields' => $hidden_fields,
   'HEADER_ROW'=>$header_row,
   'HEADER_VALUE'=>$header_value,
   //'count_rows'=>sprintf(TEXT_DISPLAY_NUMBER_OF_JOBSEEKERS,($db_jobseeker_num_row>0?1:0),$db_jobseeker_num_row,$db_jobseeker_num_row),
   'count_rows'=>$db_jobseeker_split->display_count($db_jobseeker_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_JOBSEEKERS),
   'no_of_pages'=>$db_jobseeker_split->display_links($db_jobseeker_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'],"action=print&".tep_get_all_get_params(array('page','jID','action','selected_box'))),
   'new_button'=>'
   
    <a href="'.tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_JOBSEEKER_PRINT,tep_get_all_get_params(array('print')).'print=excel').'"  >'.tep_button('Download Excel','class="btn btn-success"').'</a>
   
   
   <a class="btn btn-primary" href="'.tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_JOBSEEKER_PRINT,tep_get_all_get_params(array('print')).'print=print').'" target="_print">'.IMAGE_PRINT.'</a>
   <a class="btn btn-secondary" href="'.tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_JOBSEEKER_PRINT).'">'.IMAGE_CANCEL.'</a>',
   'HEADING_TITLE'=>HEADING_TITLE,
   'update_message'=>$messageStack->output()));
  if($print)
  {
   $template->pparse('print');
  }
  else
  {
   $template->pparse('jobseekers');
  }
 break;
 default :
  $print_fields1=tep_draw_checkbox_field('name','','','','id="name"').' <label for="name"> ' .TABLE_HEADING_NAME.'</label><br>';
  $print_fields1.=tep_draw_checkbox_field('email_address','','','','id="email_address"').' <label for="email_address"> ' .TABLE_HEADING_EMAIL.'</label><br>';
  $print_fields1.=tep_draw_checkbox_field('address','','','','id="address"').' <label for="address"> ' .TABLE_HEADING_ADDRESS.'</label><br>';

  $print_fields2=tep_draw_checkbox_field('contact_no','','','','id="contact_no"').' <label for="contact_no"> ' .TABLE_HEADING_TELEPHONE.'</label><br>';

  $form=tep_draw_form('page', PATH_TO_ADMIN.FILENAME_ADMIN1_JOBSEEKER_PRINT, '','get', '').tep_draw_hidden_field('action', 'print');

  $template->assign_vars(array(
   'new_button'=>tep_draw_submit_button_field('',IMAGE_PRINT,'class="btn btn-primary"'),
   'HEADING_TITLE'=>HEADING_TITLE,
   'print_fields1'=>$print_fields1,
   'print_fields2'=>$print_fields2,
   'print_fields3'=>$print_fields3,
   'form'=>$form,
   'update_message'=>$messageStack->output()));
  $template->pparse('print_fields');

  break;
}
/////
?>