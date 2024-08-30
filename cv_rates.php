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
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_JOBSEEKER_RATES);
$template->set_filenames(array('rates' => 'cv_rates.htm'));
include_once(FILENAME_BODY);

$table_names=JOBSEEKER_PLAN_TYPE_TABLE." as p";
$whereClause="1";
$field_names="*";
$query = "select $field_names from $table_names where $whereClause ORDER BY priority";
$result=tep_db_query($query);
//echo "<br>$query";//exit;
$x=tep_db_num_rows($result);
//echo $x;exit;
if($x > 0)
{
 $alternate=1;
 while($row = tep_db_fetch_array($result))
 {
  $alternate++;
  if($row['fee']==0.00)
  {
   $buy='<a href="' . tep_href_link(FILENAME_JOBSEEKER_DEMO_PAYMENT,'product_id='.$row['id']).'" >free service</a>';
   $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_JOBSEEKER_DEMO_PAYMENT,'product_id='.$row['id']). '\'"';
  }
  else
  {
   $buy='<a href="' . tep_href_link(FILENAME_JOBSEEKER_GIFT,'product_id='.$row['id']).'" class="btn btn-primary">Buy Now</a>';
   $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_JOBSEEKER_GIFT,'product_id='.$row['id']). '\'"';
  }
  $template->assign_block_vars('rates', array(
   'row_selected' => $row_selected,
   'name' => tep_db_output($row['plan_type_name']),
   'time' => tep_db_output($row['time_period']).'&nbsp;'.($row['time_period'] >1?tep_db_output($row['time_period1'])."s":tep_db_output($row['time_period1'])),
   'fee' => tep_db_output($currencies->format($row['fee'], ($row['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($row['currency']==DEFAULT_CURRENCY?$currencies->get_value($row['currency']):''))).($row['fee']>0?' + GST':''),
   'buy' =>$buy,
   ));
 }
}
$template->assign_vars(array(
 'HEADING_TITLE'=>HEADING_TITLE,
 'TABLE_HEADING_PLAN_TYPE_NAME'=>TABLE_HEADING_PLAN_TYPE_NAME,
 'TABLE_HEADING_PLAN_TYPE_TIME_PERIOD'=>TABLE_HEADING_PLAN_TYPE_TIME_PERIOD,
 'TABLE_HEADING_PLAN_TYPE_FEE'=>TABLE_HEADING_PLAN_TYPE_FEE,
 'TABLE_HEADING_PLAN_TYPE_NO_OF_CVS'=>TABLE_HEADING_PLAN_TYPE_NO_OF_CVS,
 'TABLE_HEADING_PLAN_TYPE_BUY'=>TABLE_HEADING_PLAN_TYPE_BUY,
 'HEADING_SUB_TITLE'=>HEADING_SUB_TITLE,

 'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
 'RIGHT_BOX_WIDTH'=>RIGHT_BOX_WIDTH1,
 'LEFT_HTML'=>LEFT_HTML,
  'LEFT_HTML'=>LEFT_HTML_JOBSEEKER,
 'RIGHT_HTML'=>RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('rates');
?>