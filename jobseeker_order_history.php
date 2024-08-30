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
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_JOBSEEKER_ACCOUNT_HISTORY_INFO);
$template->set_filenames(array('order' => 'jobseeker_order_history.htm','invoice' => 'jobseeker_invoice.htm'));
include_once(FILENAME_BODY);

if(!check_login("jobseeker"))
{
 $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
 tep_redirect(FILENAME_JOBSEEKER_LOGIN);
}
$action = (isset($_GET['action']) ? $_GET['action'] : '');
$_GET['page'] = ((int)$_GET['page'] > 0 ? (int)$_GET['page'] : '1');
$order_id = (isset($_GET['order_id']) ? $_GET['order_id'] : '');
if(tep_not_null($order_id))
{
 $order_id=(int)tep_db_prepare_input($order_id);
 if(!$row_order_check=getAnyTableWhereData(JOBSEEKER_ORDER_TABLE,"orders_id='".tep_db_input($order_id)."' and jobseeker_id='".$_SESSION['sess_jobseekerid']."'","orders_id,admin_comment"))
 {
  $messageStack->add_session(MESSAGE_ORDER_ERROR, 'error');
  tep_redirect(FILENAME_JOBSEEKER_ACCOUNT_HISTORY_INFO);
 }
 $admin_comment=$row_order_check['admin_comment'];
 include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'jobseeker_order.php');
 $order = new order($order_id);
 $customer_address=$order->customer['company']."<br>".
                   $order->customer['name']."<br>".
                   $order->customer['street_address']."<br>".
                   (tep_not_null($order->customer['city'])?$order->customer['city']."<br>":'').
                   $order->customer['state']."<br>".
                   $order->customer['country']."<br>".
                   $order->customer['zip']."<br>Phone #: ".
                   $order->customer['telephone']."<br>E-mail-address : <a href='mailto:".$order->customer['email_address']."'>".
                   $order->customer['email_address'].'</a>';
                   $billing_address=$order->billing['company']."<br>".
                   $order->billing['name']."<br>".
                   $order->billing['street_address']."<br>".
                   (tep_not_null($order->billing['city'])?$order->billing['city']."<br>":'').
                   $order->billing['state']."<br>".
                   $order->billing['country']."<br>".
                   $order->billing['zip']."<br>Phone #: ".
                   $order->billing['telephone'];

 $credit_card_string='';
 if (tep_not_null($order->info['cc_type']) || tep_not_null($order->info['cc_owner']) || tep_not_null($order->info['cc_number']))
 {
  $credit_card_string.='
     <br>
      <table border="0" width="100%" cellspacing="1" cellpadding="3" class="infoBox">
       <tr class="infoBoxContent">
        <td valign="top" width="50%">
         <table border="0" cellspacing="3" cellpadding="0">
          <tr>
            <td class="label">'.ENTRY_CREDIT_CARD_TYPE.'</td>
            <td class="small">'.$order->info['cc_type'].'</td>
          </tr>
          <tr>
            <td class="label">'.ENTRY_CREDIT_CARD_OWNER.'</td>
            <td class="small">'.$order->info['cc_owner'].'</td>
          </tr>
          <tr>
            <td class="label">'.ENTRY_CREDIT_CARD_NUMBER.'</td>
            <td class="small">'.substr($order->info['cc_number'], 0, 4) . str_repeat('X', (strlen($order->info['cc_number']) - 8)) . substr($order->info['cc_number'], -4).'</td>
          </tr>
          <tr>
            <td class="label">'.ENTRY_CREDIT_CARD_EXPIRES.'</td>
            <td class="small">'.$order->info['cc_expires'].'</td>
          </tr>
         </table>
        </td>
       </tr>
      </table><br>';
 }
 $product_name=tep_db_output($order->products['plan_type_name']);
 $product_fee=tep_db_output($currencies->format($order->products['fee'], ($order->products['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($order->products['currency']==DEFAULT_CURRENCY?$currencies->get_value($order->products['currency']):'')));
 $order_total_text='';
 for ($i = 0, $n = sizeof($order->totals); $i < $n; $i++)
 {
  $order_total_text.='
          <tr>
           <td valign="top" class="label">'.$order->totals[$i]['title'].'</td>
           <td valign="top" class="small">'.$order->totals[$i]['text'].'</td>
          </tr>'."\n";
 }
	$template->assign_vars(array(
		'HEADING_TITLE'=>HEADING_TITLE,
		'HEADING_TITLE1'=>'<a href="' . tep_href_link(FILENAME_JOBSEEKER_ACCOUNT_HISTORY_INFO). '"><button class="btn btn-primary" >Back</button></a>',
		'ENTRY_CUSTOMER'=>ENTRY_CUSTOMER,
		'ENTRY_CUSTOMER1'=>$customer_address,
		'ENTRY_BILLING_ADDRESS'=>ENTRY_BILLING_ADDRESS,
		'ENTRY_BILLING_ADDRESS1'=>$billing_address,
		'ENTRY_PAYMENT_METHOD'=>ENTRY_PAYMENT_METHOD,
		'ENTRY_PAYMENT_METHOD1'=>$order->info['payment_method'],
		'credit_card_string'=>$credit_card_string,
		'TABLE_HEADING_PRODUCTS'=>TABLE_HEADING_PRODUCTS,
		'TABLE_HEADING_PRODUCTS1'=>$product_name,
		'TABLE_HEADING_TOTAL_PRICE'=>TABLE_HEADING_TOTAL_PRICE,
		'TABLE_HEADING_TOTAL_PRICE1'=>$product_fee,
		'order_total_text'=>$order_total_text,
		'TABLE_HEADING_DATE_ADDED'=>TABLE_HEADING_DATE_ADDED,
		'TABLE_HEADING_DATE_ADDED1'=>tep_date_short($order->info['date_purchased']),
		'TABLE_HEADING_STATUS'=>TABLE_HEADING_STATUS,
		'TABLE_HEADING_STATUS1'=>$order->info['orders_status'],
		'TABLE_HEADING_COMMENTS'=>TABLE_HEADING_COMMENTS,
		'TABLE_HEADING_COMMENTS1'=>nl2br(tep_db_output($admin_comment)).'&nbsp;',
		'TABLE_HEADING_MY_COMMENTS'=>TABLE_HEADING_MY_COMMENTS,
		'TABLE_HEADING_MY_COMMENTS1'=>nl2br(tep_db_output($order->info['comments'])).'&nbsp;',
		'form'=>tep_draw_form('status', PATH_TO_ADMIN.FILENAME_ADMIN1_JOBSEEKER_ORDERS, tep_get_all_get_params(array('action')) . 'action=update_order'),
		'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
		'LEFT_HTML'=>LEFT_HTML_JOBSEEKER,
		'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
    'PRINT_INVOICE' => tep_link_button_Name(
			tep_href_link(FILENAME_ADMIN1_PDF_ORDERS, 'action=invoice&inv_id='.$order_id.'&q=jobseeker'),
			'btn btn-outline-secondary ml-2 mr-2 float-right',
			'Print',
			'target="_new"'
		),
		'update_message'=>$messageStack->output()));
	$template->pparse('invoice');
}
else
{
 //////////////////
 ///only for sorting starts
 $sort_array=array('oh.plan_type_name','oh.inserted');
 include_once(PATH_TO_MAIN_PHYSICAL_CLASS.'sort_by_clause.php');
 $obj_sort_by_clause=new sort_by_clause($sort_array,'oh.inserted desc');
 $order_by_clause=$obj_sort_by_clause->return_value;
 //print_r($obj_sort_by_clause->return_sort_array['name']);
 //print_r($obj_sort_by_clause->return_sort_array['image']);
 ///only for sorting ends
 define('MAX_DISPLAY_LIST_OF_ORDERS','20');
  $db_order_query_raw = "select oh.*,o.orders_date_finished, os.orders_status_name , ah.start_date , ah.end_date from ".JOBSEEKER_ACCOUNT_HISTORY_TABLE." as ah right join " . JOBSEEKER_ORDER_HISTORY_TABLE . " as oh on ( ah.order_id=oh.order_id ), ".JOBSEEKER_ORDER_TABLE." as o right join ".ORDER_STATUS_TABLE." as os on (o.orders_status=os.orders_status_id and os.language_id='".(int)$languages_id."')  where oh.order_id=o.orders_id and o.jobseeker_id='".$_SESSION['sess_jobseekerid']."' order by ".$order_by_clause;
//  $db_order_query_raw = "select oh.*,o.orders_date_finished, os.orders_status_name from " . JOBSEEKER_ORDER_HISTORY_TABLE . " as oh, ".JOBSEEKER_ORDER_TABLE." as o right join ".ORDER_STATUS_TABLE." as os on (o.orders_status=os.orders_status_id)  where oh.order_id=o.orders_id and o.jobseeker_id='".$_SESSION['sess_jobseekerid']."' order by ".$order_by_clause;
  //echo $db_order_query_raw;
  $db_order_query = tep_db_query($db_order_query_raw);
  $db_order_num_row = tep_db_num_rows($db_order_query);
  $db_order_split = new splitPageResults($_GET['page'], MAX_DISPLAY_LIST_OF_ORDERS, $db_order_query_raw, $db_order_query_numrows);
  if($db_order_num_row > 0)
  {
   $alternate=1;
   while ($order = tep_db_fetch_array($db_order_query))
   {
				$row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_JOBSEEKER_ACCOUNT_HISTORY_INFO,'order_id='.$order['order_id']). '\'"';
    $template->assign_block_vars('orders', array( 'row_selected' => $row_selected,
     'plan_type_name' => tep_db_output($order['plan_type_name']),
     'price' => tep_db_output($currencies->format($order['total_price'], ($order['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($order['currency']==DEFAULT_CURRENCY?$currencies->get_value($order['currency']):''))),
     'time' => tep_db_output($order['time_period']).'&nbsp;'.($order['time_period'] >1?tep_db_output($order['time_period1'])."s":tep_db_output($order['time_period1'])),
     //'time' => '1 Month',
     'status' => tep_db_output($order['orders_status_name']),
     'purchase' => tep_date_short(tep_db_output($order['inserted'])),
    //'last_updated' => tep_date_long(tep_db_output($order['orders_date_finished'])),
		   'inserted' => (tep_not_null($order['start_date'])?tep_date_short(tep_db_output($order['start_date'])):'Wait For Admin Approval'),
     'last_updated' => (tep_not_null($order['end_date'])?tep_date_short(tep_db_output($order['end_date'])):'Wait For Admin Approval'),

     ));
    $alternate++;
   }
  }
  /////
// $RIGHT_HTML="";
 //$RIGHT_BOX_WIDTH=0;
 /////

  $template->assign_vars(array(
   'HEADING_TITLE'=>HEADING_TITLE,
   'TABLE_HEADING_PLAN_TYPE_NAME'=>"<a href='".tep_href_link(FILENAME_JOBSEEKER_ACCOUNT_HISTORY_INFO, tep_get_all_get_params(array('sort','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][0]."' class='white'>".TABLE_HEADING_PLAN_TYPE_NAME.$obj_sort_by_clause->return_sort_array['image'][0]."</a>",
   'TABLE_HEADING_PRICE'=>TABLE_HEADING_PRICE,
   'TABLE_HEADING_PLAN_TYPE_TIME_PERIOD'=>TABLE_HEADING_PLAN_TYPE_TIME_PERIOD,
   'TABLE_HEADING_STATUS'=>TABLE_HEADING_STATUS,
			'TABLE_HEADING_PURCHASED' =>TABLE_HEADING_PURCHASED,
   'TABLE_HEADING_INSERTED'=>"<a href='".tep_href_link(FILENAME_JOBSEEKER_ACCOUNT_HISTORY_INFO, tep_get_all_get_params(array('sort','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][1]."' class='white'>".TABLE_HEADING_INSERTED.$obj_sort_by_clause->return_sort_array['image'][1]."</a>",
   'TABLE_HEADING_LAST_UPDATED'=>TABLE_HEADING_LAST_UPDATED,
   'count_rows'=>$db_order_split->display_count($db_order_query_numrows, MAX_DISPLAY_LIST_OF_ORDERS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_ORDERS),
   'no_of_pages'=>$db_order_split->display_links($db_order_query_numrows, MAX_DISPLAY_LIST_OF_ORDERS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'],tep_get_all_get_params(array('page','jobID','action'))),
   'new_button'=>'',
   'hidden_fields'=>$hidden_fields,
   'new_button'=>'',
   'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
   'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
   'LEFT_HTML'=>LEFT_HTML_JOBSEEKER,
   'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
   'update_message'=>$messageStack->output()));
  $template->pparse('order');
}
?>