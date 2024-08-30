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
include_once("../include_files.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_RECRUITER_PRINT);
$template->set_filenames(array('recruiters' => 'admin1_print_recruiters.htm',
                               'print_fields' => 'admin1_print_recruiters1.htm',
                               'print' => 'admin1_print_recruiters2.htm'));
include_once(FILENAME_ADMIN_BODY);
//print_r($_GET);
$action = (isset($_GET['action']) ? $_GET['action'] : '');
$print = (isset($_GET['print']) ? $_GET['print'] : '');
if($print=='print'  || $print=='excel')
{
// $print=true;
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
  $hidden_fields.=tep_draw_hidden_field('print');
  ///only for sorting starts
  $sort_array=array("name","rl.recruiter_email_address","r.recruiter_company_name","r.recruiter_telephone","r.recruiter_position");
  include_once(PATH_TO_MAIN_PHYSICAL_CLASS.'sort_by_clause.php');
  $obj_sort_by_clause=new sort_by_clause($sort_array,'rl.inserted desc');
  $order_by_clause=$obj_sort_by_clause->return_value;
  //echo $order_by_clause;
  //print_r($obj_sort_by_clause->return_sort_array['name']);
  //print_r($obj_sort_by_clause->return_sort_array['image']);
   $see_before_page_number_array=see_before_page_number($sort_array,$field,'rl.inserted',$order,'desc',$lower,'0',$higher,'');
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
  $company=$_GET['company'];
  $telephone=$_GET['telephone'];
  $position=$_GET['position'];
  $csv_file = array();
  if(tep_not_null($name))
  {
   $hidden_fields.=tep_draw_hidden_field('name',$name);
   if($print)
    $header_row.='<td><b><u>'.TABLE_HEADING_NAME.'</u></td>';
   else
    $header_row.='<td class="dataTableHeadingContent">'."<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][0]."','".$lower."');\"><u>".TABLE_HEADING_NAME.'</u>'.$obj_sort_by_clause->return_sort_array['image'][0]."</a>".'</td>';
   $fields[]="concat(r.recruiter_first_name,' ',r.recruiter_last_name) as name";
   $csv_file[0][] =TABLE_HEADING_NAME;
  }
  if(tep_not_null($email))
  {
   $hidden_fields.=tep_draw_hidden_field('email_address',$email);
   if($print)
    $header_row.='<td><b><u>'.TABLE_HEADING_EMAIL.'</u></td>';
   else
    $header_row.='<td class="dataTableHeadingContent">'."<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][1]."','".$lower."');\"><u>".TABLE_HEADING_EMAIL.'</u>'.$obj_sort_by_clause->return_sort_array['image'][1]."</a>" .'</td>';
   $fields[]="rl.recruiter_email_address as email";
   $csv_file[0][] =TABLE_HEADING_EMAIL;
  }
  if(tep_not_null($position))
  {
   $hidden_fields.=tep_draw_hidden_field('position',$position);
   if($print)
    $header_row.='<td><b><u>'.TABLE_HEADING_POSITION.'</u></td>';
   else
    $header_row.='<td class="dataTableHeadingContent">'."<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][4]."','".$lower."');\"><u>".TABLE_HEADING_POSITION.'</u>'.$obj_sort_by_clause->return_sort_array['image'][4]."</a>" .'</td>';
   $fields[]="r.recruiter_position as position";
   $csv_file[0][] =TABLE_HEADING_POSITION;
  }
  if(tep_not_null($company))
  {
   $hidden_fields.=tep_draw_hidden_field('company',$company);
   if($print)
    $header_row.='<td><b><u>'.TABLE_HEADING_COMPANY.'</u></td>';
   else
    $header_row.='<td class="dataTableHeadingContent">'."<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][2]."','".$lower."');\"><u>".TABLE_HEADING_COMPANY.'</u>'.$obj_sort_by_clause->return_sort_array['image'][2]."</a>" .'</td>';
   $fields[]="r.recruiter_company_name as company";
   $csv_file[0][] =TABLE_HEADING_COMPANY;
  }
  if(tep_not_null($address))
  {
   $hidden_fields.=tep_draw_hidden_field('address',$address);
   if($print)
    $header_row.='<td><b><u>'.TABLE_HEADING_ADDRESS.'</u></td>';
   else
    $header_row.='<td class="dataTableHeadingContent">'.TABLE_HEADING_ADDRESS.'</td>';
   $fields[]="r.recruiter_address1, r.recruiter_address2, r.recruiter_zip, r.recruiter_state_id, r.recruiter_state, r.recruiter_country_id";
   $csv_file[0][] =TABLE_HEADING_ADDRESS;
  }
  if(tep_not_null($telephone))
  {
   $hidden_fields.=tep_draw_hidden_field('telephone',$telephone);
   if($print)
    $header_row.='<td><b><u>'.TABLE_HEADING_TELEPHONE.'</u></td>';
   else
    $header_row.='<td class="dataTableHeadingContent">'."<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][3]."','".$lower."');\"><u>".TABLE_HEADING_TELEPHONE.'</u>'.$obj_sort_by_clause->return_sort_array['image'][3]."</a>" .'</td>';
   $fields[]="r.recruiter_telephone as telephone";
   $csv_file[0][] =TABLE_HEADING_TELEPHONE;
  }
  if(tep_not_null($fields))
   $fields=implode(", ",$fields);
  else
  {
   $messageStack->add_session(MESSAGE_ATLEAST_ONE_CHECKED, 'error');
   tep_redirect(FILENAME_ADMIN1_RECRUITER_PRINT);
  }

  $db_recruiter_query_raw = "select $fields from " . RECRUITER_LOGIN_TABLE . " as rl, " . RECRUITER_TABLE . " as r where rl.recruiter_id=r.recruiter_id order by ".$order_by_clause;
  //echo $db_recruiter_query_raw;
  $db_recruiter_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $db_recruiter_query_raw, $db_recruiter_query_numrows);
  $db_recruiter_query = tep_db_query($db_recruiter_query_raw);
  $db_recruiter_num_row = tep_db_num_rows($db_recruiter_query);
  if($db_recruiter_num_row > 0)
  {
   $alternate=1;
   $header_value='';
   while ($recruiter = tep_db_fetch_array($db_recruiter_query))
   {
    if($print)
     $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'"';
    else
     $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
	if($print=='excel')
	{
	  if(tep_not_null($name))
   	  $csv_file[$alternate] [] = tep_db_output($recruiter['name']);
	  if(tep_not_null($email))
   	  $csv_file[$alternate] [] = tep_db_output($recruiter['email']);
	  if(tep_not_null($position))
   	  $csv_file[$alternate] [] = tep_db_output($recruiter['position']);
	  if(tep_not_null($company))
   	  $csv_file[$alternate] [] = tep_db_output($recruiter['company']);
	  if(tep_not_null($address))
   	  $csv_file[$alternate] [] = tep_db_output($recruiter['recruiter_address1'].(tep_not_null($recruiter['recruiter_address2'])?', '.$recruiter['recruiter_address2']:'').(tep_not_null($recruiter['recruiter_zip'])?', '.$recruiter['recruiter_zip']:'').($recruiter['recruiter_state_id'] > 0?', '.get_name_from_table(ZONES_TABLE,'zone_name','zone_id',$recruiter['recruiter_state_id']):(tep_not_null($recruiter['recruiter_state'])?', '.$recruiter['recruiter_state']:'')).(tep_not_null($recruiter['recruiter_country_id'])?', '.get_name_from_table(COUNTRIES_TABLE,'country_name','id',$recruiter['recruiter_country_id']):''));
	  if(tep_not_null($telephone))
   	  $csv_file[$alternate] [] = tep_db_output($recruiter['telephone']);
    }
	else
	{
	 $header_value.='<tr'.$row_selected.'>'.
      (tep_not_null($name)?'<td valign="top" class="dataTableContent">'.tep_db_output($recruiter['name']).'</td>':'').
      (tep_not_null($email)?'<td valign="top" class="dataTableContent">'.tep_db_output($recruiter['email']).'</td>':'').
      (tep_not_null($position)?'<td valign="top" class="dataTableContent">'.tep_db_output($recruiter['position']).'</td>':'').
      (tep_not_null($company)?'<td valign="top" class="dataTableContent">'.tep_db_output($recruiter['company']).'</td>':'').
      (tep_not_null($address)?'<td valign="top" class="dataTableContent">'.tep_db_output($recruiter['recruiter_address1'].(tep_not_null($recruiter['recruiter_address2'])?', '.$recruiter['recruiter_address2']:'').(tep_not_null($recruiter['recruiter_zip'])?', '.$recruiter['recruiter_zip']:'').($recruiter['recruiter_state_id'] > 0?', '.get_name_from_table(ZONES_TABLE,'zone_name','zone_id',$recruiter['recruiter_state_id']):(tep_not_null($recruiter['recruiter_state'])?', '.$recruiter['recruiter_state']:'')).(tep_not_null($recruiter['recruiter_country_id'])?', '.get_name_from_table(COUNTRIES_TABLE,'country_name','id',$recruiter['recruiter_country_id']):'')).'</td>':'').
      (tep_not_null($telephone)?'<td valign="top" class="dataTableContent">'.tep_db_output($recruiter['telephone']).'</td>':'').
     '</tr>'."\n";

	}
    $alternate++;
   }
  }
  see_page_number();
   if($print=='excel')
  {
	$file_name=PATH_TO_MAIN_PHYSICAL_TEMP.date("YmdHis").'recruiter_report.csv';
    $fp = fopen($file_name, 'w');
	foreach($csv_file as $data)
    fputcsv($fp,$data);
    fclose($fp);
	header('Content-type: application/x-octet-stream');
    header('Content-disposition: attachment; filename=recruiter_report'.date("Y-m-d-hi").'.csv' );
    readfile($file_name);
    unlink($file_name);
	exit();
	//die();
  }

  $template->assign_vars(array(
   'hidden_fields' => $hidden_fields,
   'HEADER_ROW'=>$header_row,
   'HEADER_VALUE'=>$header_value,
//   'count_rows'=>sprintf(TEXT_DISPLAY_NUMBER_OF_RECRUITERS,($db_recruiter_num_row>0?1:0),$db_recruiter_num_row,$db_recruiter_num_row),//$db_jobseeker_split->display_count($db_jobseeker_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_JOBSEEKERS),
   'no_of_pages'=>'',
   'count_rows'=>$db_recruiter_split->display_count($db_recruiter_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_RECRUITERS),
   'no_of_pages'=>$db_recruiter_split->display_links($db_recruiter_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'],tep_get_all_get_params(array('page','jID','action','selected_box'))."&action=print"),
   'new_button'=>'<a class="btn btn-success" href="'.tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_PRINT,tep_get_all_get_params(array('print')).'print=excel').'">Download Excel</a>
   <a class="btn btn-primary" href="'.tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_PRINT,tep_get_all_get_params(array('print')).'print=print').'" target="_print">'.IMAGE_PRINT.'</a>
   <a class="btn btn-secondary" href="'.tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_PRINT).'">'.IMAGE_CANCEL.'</a>',
   'HEADING_TITLE'=>HEADING_TITLE,
   'update_message'=>$messageStack->output()));
  if($print)
  {
   $template->pparse('print');
  }
  else
  {
   $template->pparse('recruiters');
  }
 break;
 default :
  $print_fields1=tep_draw_checkbox_field('name','','','','id="name"').' <label for="name">'.TABLE_HEADING_NAME.'</label><br>';
  $print_fields1.=tep_draw_checkbox_field('email_address','','','','id="email_address"').' <label for="email_address">'.TABLE_HEADING_EMAIL.'</label><br>';
  $print_fields1.=tep_draw_checkbox_field('address','','','','id="address"').' <label for="address">'.TABLE_HEADING_ADDRESS.'</label><br>';

  $print_fields2=tep_draw_checkbox_field('position','','','','id="position"').' <label for="position">'.TABLE_HEADING_POSITION.'</label><br>';
  $print_fields2.=tep_draw_checkbox_field('company','','','','id="company"').' <label for="company">'.TABLE_HEADING_COMPANY.'</label><br>';
  $print_fields2.=tep_draw_checkbox_field('telephone','','','','id="telephone"').' <label for="telephone">'.TABLE_HEADING_TELEPHONE.'</label><br>';

  $form=tep_draw_form('page', PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_PRINT, '','get', '').tep_draw_hidden_field('action', 'print');

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