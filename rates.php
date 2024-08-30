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
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_RECRUITER_RATES);
$template->set_filenames(array('rates' => 'rates.htm'));
include_once(FILENAME_BODY);

$rates_array=array('p.job >0 and p.cv=0 and featured_job="No"','p.job =0 and p.cv >0 ','p.job >0 and p.cv>0 and  featured_job="No"','p.job >0 and p.cv=0 and featured_job="Yes"','p.job >0 and p.cv >0 and  featured_job="Yes" ');
for($j=0;$j<5;$j++)
{
 $z=$j+1;
 $table_names=PLAN_TYPE_TABLE." as p";
 $whereClause=$rates_array[$j];
 $field_names="*";
 $query = "select $field_names from $table_names where $whereClause ORDER BY priority";
 $result=tep_db_query($query);
 //echo "<br>$query";//exit;
 $x=tep_db_num_rows($result);
 //echo $x;exit;
 switch($z)
 {
  case 1 :if($x<=0)
   $template->assign_vars(array('INFO_TEXT_BLOCK1'=>'style="display:none"'));
   break;
  case 2 :if($x<=0)
   $template->assign_vars(array('INFO_TEXT_BLOCK2'=>'style="display:none"'));
   break;
  case 3 :if($x<=0)
   $template->assign_vars(array('INFO_TEXT_BLOCK3'=>'style="display:none"'));
   break;
  case 4 :if($x<=0)
   $template->assign_vars(array('INFO_TEXT_BLOCK4'=>'style="display:none"'));
   break;
  case 5 :if($x<=0)
   $template->assign_vars(array('INFO_TEXT_BLOCK5'=>'style="display:none"'));
   break;
 }
 if($x > 0)
 {
  $alternate=1;
  while($row = tep_db_fetch_array($result))
  {
   $unlimited_job=($row['job']=="2147483647"?true:false);
   $unlimited_cv=($row['cv']=="2147483647"?true:false);
   $unlimited_sms=($row['sms']=="2147483647"?true:false);
   $alternate++;
   if((int)$row['fee']<=0)
   {
    $buy='<a href="' . tep_href_link(FILENAME_DEMO_PAYMENT,'product_id='.$row['id']).'">'.INFO_TEXT_FREE_SERVICE.'</a>';
    $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" ';
   }
   else
   {
    $buy='<a href="' . tep_href_link(FILENAME_EMPLOYER_GIFT,'product_id='.$row['id']).'">'.TABLE_HEADING_PLAN_TYPE_BUY.'</a>';
    $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
   }

   $template->assign_block_vars('rates'.$z, array(
    'row_selected' => $row_selected,
    // 'radio' =>tep_draw_radio_field('product_id',$row['id'],'','','id="product_'.$row['id'].'" class="form-check-input"'),
    'rate_card_buy' => tep_draw_hidden_field('product_id',$row['id'],'id="product_'.$row['id'].'" class=""'),
    'name' => '<label class="form-check-label fw-bold m-0" for="product_'.$row['id'].'">'.tep_db_output($row[TEXT_LANGUAGE.'plan_type_name']).'</label>',
    'time' => tep_db_output($row['time_period']).'&nbsp;'.($row['time_period'] >1?tep_db_output($row['time_period1'])."s":tep_db_output($row['time_period1'])),
    'fee' => tep_db_output($currencies->format($row['fee'], ($row['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($row['currency']==DEFAULT_CURRENCY?$currencies->get_value($row['currency']):''))),
    'job' => ($unlimited_job?INFO_TEXT_UNLIMITED:tep_db_output($row['job'])),
    'cv' => ($unlimited_cv?INFO_TEXT_UNLIMITED:tep_db_output($row['cv'])),
    'sms' => ($unlimited_sms?INFO_TEXT_UNLIMITED:tep_db_output($row['sms'])),
    'buy' =>$buy,
    'buy_now' => tep_button_submit('btn btn-primary',''.BUY_NOW.''),
    ));
  }
 }
 tep_db_free_result($result);

}
$template->assign_vars(array(
 'HEADING_TITLE'=>HEADING_TITLE,
 'BUY_BUTTON'=>'<button class="btn btn-lg btn-primary">'.BUY_NOW.'</button>',
//  'order_form' => tep_draw_form('rates'.$row['id'], FILENAME_EMPLOYER_GIFT,'', 'get',' onSubmit="return check_submit(this);"'),
 'order_form' => tep_draw_form('rates'.$row['id'], FILENAME_EMPLOYER_GIFT,'', 'get',''),
 'TABLE_HEADING_PLAN_TYPE_NAME'=>TABLE_HEADING_PLAN_TYPE_NAME,
 'TABLE_HEADING_PLAN_TYPE_TIME_PERIOD'=>TABLE_HEADING_PLAN_TYPE_TIME_PERIOD,
 'TABLE_HEADING_PLAN_TYPE_FEE'=>TABLE_HEADING_PLAN_TYPE_FEE,
 'TABLE_HEADING_PLAN_TYPE_NO_OF_JOBS'=>TABLE_HEADING_PLAN_TYPE_NO_OF_JOBS,
 'TABLE_HEADING_PLAN_TYPE_NO_OF_CVS'=>TABLE_HEADING_PLAN_TYPE_NO_OF_CVS,
 'TABLE_HEADING_PLAN_TYPE_NO_OF_SMS'=>TABLE_HEADING_PLAN_TYPE_NO_OF_SMS,
 'TABLE_HEADING_PLAN_TYPE_BUY'=>TABLE_HEADING_PLAN_TYPE_BUY,
 'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
 'RIGHT_BOX_WIDTH'=>RIGHT_BOX_WIDTH1,
 'INFO_TEXT_QUOT1'=>INFO_TEXT_QUOT1,
 'INFO_TEXT_QUOT2'=>INFO_TEXT_QUOT2,
 'INFO_TEXT_POST_JOBS'=>INFO_TEXT_POST_JOBS,
 'INFO_TEXT_POST_F_JOBS'=>INFO_TEXT_F_POST_JOBS,
 'INFO_TEXT_SEARCH_RESUMES'=>INFO_TEXT_SEARCH_RESUMES,
 'INFO_TEXT_POST_SEARCH' =>INFO_TEXT_POST_SEARCH,
 'INFO_TEXT_F_POST_SEARCH' =>INFO_TEXT_F_POST_SEARCH,
 'INFO_TEXT_KEY_FEATURES'=>INFO_TEXT_KEY_FEATURES,
 'INFO_TEXT_POST_JOB_INSTANTLY'=>INFO_TEXT_POST_JOB_INSTANTLY,
 'INFO_TEXT_MANAGE_MONITOR' =>INFO_TEXT_MANAGE_MONITOR,
 'INFO_TEXT_TRACK_APPLICATION'=>INFO_TEXT_TRACK_APPLICATION,
 'INFO_TEXT_GET_RESUMES'=>INFO_TEXT_GET_RESUMES,
 'INFO_TEXT_SEARCH_QUALIFIED'=>INFO_TEXT_SEARCH_QUALIFIED,
 'INFO_TEXT_VIEW_SAVE_ORGANISATION'=>INFO_TEXT_VIEW_SAVE_ORGANISATION,
 'INFO_TEXT_GET_ACCESS_TO_RESUME'=>INFO_TEXT_GET_ACCESS_TO_RESUME,
 'INFO_TEXT_USE_APPLICANT_TRACKING'=>INFO_TEXT_USE_APPLICANT_TRACKING,
 'INFO_TEXT_FOR_OTHER_OPTIONS' =>INFO_TEXT_FOR_OTHER_OPTIONS,
 'INFO_TEXT_CONTACT_US'=>INFO_TEXT_CONTACT_US,
 'BUY_NOW'=>BUY_NOW,
 'LEFT_HTML'=>LEFT_HTML,
 'RIGHT_HTML'=>RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('rates');
?>