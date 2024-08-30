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
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_JOBSEEKER_ORDERS);
$template->set_filenames(array('order' => 'admin1_jobseeker_orders.htm','order1' => 'admin1_jobseeker_orders1.htm','email' => 'jobseeker_ord_status_update_tmpl.htm'));
include_once(FILENAME_ADMIN_BODY);

// check if jobseeker exists or not ///
$whereClause="";
if(isset($_GET['jID']))
{
 if($row_check_jobseeker=getAnyTableWhereData(JOBSEEKER_TABLE,"jobseeker_id='".tep_db_input($_GET['jID'])."'","jobseeker_id"))
 {
  $whereClause.=" o.jobseeker_id='".tep_db_input($_GET['jID'])."'";
 }
}

$whereClause=($whereClause!=""?$whereClause." and":"");

$orders_statuses = array();
$orders_status_array = array();
$orders_status_query = tep_db_query("select orders_status_id, orders_status_name from " . ORDER_STATUS_TABLE . " where language_id = '" . (int)$languages_id . "'");
while ($orders_status = tep_db_fetch_array($orders_status_query)) 
{
	$orders_statuses[] = array('id' => $orders_status['orders_status_id'],
																												'text' => $orders_status['orders_status_name']);
	$orders_status_array[$orders_status['orders_status_id']] = $orders_status['orders_status_name'];
}
$action = (isset($_GET['action']) ? $_GET['action'] : '');

if (tep_not_null($action)) 
{
	switch ($action) 
	{
		case 'update_order':
			$oID = tep_db_prepare_input($_GET['oID']);
			$status = tep_db_prepare_input($_POST['status']);
			$comments = tep_db_prepare_input($_POST['comments']);
			$order_updated = false;
			$check_status_query = tep_db_query("select product_id, jobseeker_id, jobseeker_name, jobseeker_email_address, orders_status, date_purchased from " . JOBSEEKER_ORDER_TABLE . " where orders_id = '" . (int)$oID . "'");
			$check_status = tep_db_fetch_array($check_status_query);
			if($check_status['orders_status'] == "3")
             {
				$messageStack->add_session(WARNING_ORDER_ALREADY_COMPLETED, 'warning');
 			tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_JOBSEEKER_ORDERS, tep_get_all_get_params(array('action','selected_box'))));
			}
			if ( ($check_status['orders_status'] != $status) || tep_not_null($comments)) 
			{
				tep_db_query("update " . JOBSEEKER_ORDER_TABLE . " set orders_status = '" . tep_db_input($status) . "', last_modified = now(), admin_comment='".tep_db_input($comments)."' where orders_id = '" . (int)$oID . "'");
				$customer_notified = '0';
				if (isset($_POST['notify']) && ($_POST['notify'] == 'on')) 
				{
					$notify_comments = '';
					if (isset($_POST['notify_comments']) && ($_POST['notify_comments'] == 'on')) 
					{
						$notify_comments = sprintf(EMAIL_TEXT_COMMENTS_UPDATE, $comments) . "\n\n";
					}
					//$email = SITE_TITLE . "\n" . EMAIL_SEPARATOR . "\n" . EMAIL_TEXT_ORDER_NUMBER . ' ' . $oID . "\n" . EMAIL_TEXT_INVOICE_URL . ' ' . tep_href_link(FILENAME_RECRUITER_ACCOUNT_HISTORY_INFO, 'order_id=' . $oID, 'SSL') . "\n" . EMAIL_TEXT_DATE_ORDERED . ' ' . tep_date_long($check_status['date_purchased']) . "\n\n" . $notify_comments . sprintf(EMAIL_TEXT_STATUS_UPDATE, $orders_status_array[$status]);
      $template->assign_vars(array(
      'site_title'=>tep_db_output(SITE_TITLE),
      'order_no'=>$oID,
      'order_invoice'=>'<a href="'.tep_href_link(FILENAME_JOBSEEKER_ACCOUNT_HISTORY_INFO, 'order_id=' . $oID, 'SSL').' target="_blank">'.tep_href_link(FILENAME_JOBSEEKER_ACCOUNT_HISTORY_INFO, 'order_id=' . $oID, 'SSL').'</a>',
      'order_date'=>tep_date_long($check_status['date_purchased']),
      'order_status'=>$orders_status_array[$status],
      'admin_comment'=>nl2br($notify_comments),
      'logo'=>'<a href="'.tep_href_link("").'">'.tep_image(PATH_TO_IMAGE.'logo.jpg',tep_db_output(SITE_TITLE)).'</a>',
      ));
     $email=stripslashes($template->pparse1('email'));
					tep_mail($check_status['jobseeker_name'], $check_status['jobseeker_email_address'], EMAIL_TEXT_SUBJECT, nl2br($email), SITE_OWNER, ADMIN_EMAIL);
					tep_mail(SITE_OWNER, ADMIN_EMAIL, EMAIL_TEXT_SUBJECT, $email, SITE_OWNER, ADMIN_EMAIL);
					$customer_notified = '1';
				}
    ////////// If you delete order status then change must be needed ////
    /////////////////////////////////////////////////////////////////////
    if(($check_status['orders_status'] != $status ) && $status==3)
    {
     $order_id=(int)$oID;
     $product_id=$check_status['product_id'];
     $product_row = getAnyTableWhereData(JOBSEEKER_ORDER_HISTORY_TABLE,"order_id='".$order_id."'");
     $jobseeker_id=$check_status['jobseeker_id'];
     //////////////////////////////////////////////////////
     $sql_data_array=array('orders_date_finished'=>'now()');
     tep_db_perform(JOBSEEKER_ORDER_TABLE, $sql_data_array, 'update', "jobseeker_id='".$jobseeker_id."'");
     if($row_check=getAnyTableWhereData(JOBSEEKER_ACCOUNT_HISTORY_TABLE,"jobseeker_id='".$jobseeker_id."' order by end_date desc limit 0,1","id,end_date"))
     {
      $start_date=$row_check['end_date'];
      $start_date1=date('Y-m-d');
      if($start_date<$start_date1)
      {
       $start_date='now()';
       $end_date=calculate_end_date($product_row['time_period'],$product_row['time_period1']);
      }
      else
      {
       $end_date=calculate_end_date($product_row['time_period'],$product_row['time_period1'],$start_date);
       $start_date=date("Y-m-d",mktime(0,0,0,substr($start_date,5,2),(substr($start_date,8,2)+1),substr($start_date,0,4)));
      }
     }
     else
     {
      $start_date='now()';
      $end_date=calculate_end_date($product_row['time_period'],$product_row['time_period1']);
     }
     $plan_type_name=$product_row['plan_type_name'];
     $sql_data_array=array('jobseeker_id'=>$jobseeker_id,
                           'order_id'=>$order_id,
                           'inserted'=>'now()',
                           'plan_type_name'=>$plan_type_name,
                           'start_date'=>$start_date,
                           'end_date'=>$end_date);
     tep_db_perform(JOBSEEKER_ACCOUNT_HISTORY_TABLE, $sql_data_array);
    }
    ////////////////////////////////////////////
				$order_updated = true;
			}
			if ($order_updated == true) 
			{
				$messageStack->add_session(SUCCESS_ORDER_UPDATED, 'success');
			} 
			else 
			{
				$messageStack->add_session(WARNING_ORDER_NOT_UPDATED, 'warning');
			}
			tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_JOBSEEKER_ORDERS, tep_get_all_get_params(array('action','selected_box')) . 'action=edit'));
		break;
		case 'deleteconfirm':
			$oID = tep_db_prepare_input($_GET['oID']);
   tep_db_query("delete from " . JOBSEEKER_ORDER_TABLE . " where orders_id = '" . (int)$oID . "'");
   tep_db_query("delete from " . JOBSEEKER_ORDER_TOTAL_TABLE . " where orders_id = '" . (int)$oID . "'");
   tep_db_query("delete from " . JOBSEEKER_ORDER_HISTORY_TABLE . " where order_id = '" . (int)$oID . "'");
   tep_db_query("delete from " . JOBSEEKER_ACCOUNT_HISTORY_TABLE . " where order_id = '" . (int)$oID . "'");
			$messageStack->add_session(SUCCESS_ORDER_DELETED, 'success');
			tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_JOBSEEKER_ORDERS, tep_get_all_get_params(array('oID', 'action','selected_box'))));
		break;
	}
}
if (($action == 'edit') && isset($_GET['oID'])) 
{
	$oID = tep_db_prepare_input($_GET['oID']);
	$orders_query = tep_db_query("select orders_id, orders_status, admin_comment from " . JOBSEEKER_ORDER_TABLE . " where orders_id = '" . (int)$oID . "'");
	$order_exists = true;
 $row_comment=mysql_fetch_array($orders_query);
 $admin_comments=$row_comment['admin_comment'];
 $orders_status=$row_comment['orders_status'];
	if (!tep_db_num_rows($orders_query)) 
	{
		$order_exists = false;
		$messageStack->add(sprintf(ERROR_ORDER_DOES_NOT_EXIST, $oID), 'error');
	}
}
include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'jobseeker_order.php');
if (($action == 'edit') && ($order_exists == true)) 
{
 $order = new order($oID);
 $customer_address=$order->customer['name']."<br>".
                   $order->customer['street_address']."<br>".
                   (tep_not_null($order->customer['city'])?$order->customer['city']."<br>":'').
                   $order->customer['state']."<br>".
                   $order->customer['zip']."<br>".
                   $order->customer['country']."<br>Phone #: ".
                   $order->customer['telephone']."<br>E-mail-address : <a href='mailto:".$order->customer['email_address']."'>".
                   $order->customer['email_address'].'</a>';
 $billing_address= $order->billing['name']."<br>".
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
            <td class="small">'.$order->info['cc_number'].'</td>
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
		'HEADING_TITLE'=>'<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_JOBSEEKER_ORDERS, tep_get_all_get_params(array('action','selected_box'))) . '">' . tep_image_button(PATH_TO_BUTTON.'button_back.gif', IMAGE_BACK) . '</a>',
		'HEADING_TITLE1'=>$RIGHT_BOX_WIDTH,
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
		'TABLE_HEADING_COMMENTS1'=>nl2br(tep_db_output($order->info['comments'])).'&nbsp;',
		'TABLE_HEADING_COMMENTS2'=>tep_draw_textarea_field('comments', 'soft', '60', '5',$admin_comments),
		'form'=>tep_draw_form('status', PATH_TO_ADMIN.FILENAME_ADMIN1_JOBSEEKER_ORDERS, tep_get_all_get_params(array('action','selected_box')) . 'action=update_order'),
		'ENTRY_STATUS'=>ENTRY_STATUS,
		'ENTRY_STATUS1'=>tep_draw_pull_down_menu('status', $orders_statuses, $orders_status),
		'ENTRY_NOTIFY_CUSTOMER'=>ENTRY_NOTIFY_CUSTOMER,
		'ENTRY_NOTIFY_CUSTOMER1'=>tep_draw_checkbox_field('notify', '', true),
		'ENTRY_NOTIFY_COMMENTS'=>ENTRY_NOTIFY_COMMENTS,
		'ENTRY_NOTIFY_COMMENTS1'=>tep_draw_checkbox_field('notify_comments', '', true),
		'button'=>tep_image_submit(PATH_TO_BUTTON.'button_update.gif', IMAGE_UPDATE),
		'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
		'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
		'update_message'=>$messageStack->output()));
	$template->pparse('order1');
} 
else 
{
	$search_form=tep_draw_form('orders', PATH_TO_ADMIN.FILENAME_ADMIN1_JOBSEEKER_ORDERS, '', 'get');
	$search_text=HEADING_TITLE_SEARCH . ' ' . tep_draw_input_field('oID', '', 'size="12"') . tep_draw_hidden_field('action', 'edit');
	$order_form=tep_draw_form('status', PATH_TO_ADMIN.FILENAME_ADMIN1_JOBSEEKER_ORDERS, '', 'get');
	$order_text=' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.HEADING_TITLE_STATUS . ' ' . tep_draw_pull_down_menu('status', array_merge(array(array('id' => '', 'text' => TEXT_ALL_ORDERS)), $orders_statuses), $_GET['status'], 'onChange="this.form.submit();"');
	if (isset($_GET['cID'])) 
	{
		$cID = tep_db_prepare_input($_GET['cID']);
		$orders_query_raw = "select o.orders_id, o.jobseeker_name, o.jobseeker_id, o.payment_method, o.date_purchased, o.last_modified, o.currency, o.currency_value, s.orders_status_name, ot.text as order_total from " . JOBSEEKER_ORDER_TABLE . " o left join " . JOBSEEKER_ORDER_TOTAL_TABLE . " ot on (o.orders_id = ot.orders_id), " . ORDER_STATUS_TABLE . " s where o.jobseeker_id = '" . (int)$cID . "' and o.orders_status = s.orders_status_id and s.language_id = '" . (int)$languages_id . "' and ot.class = 'ot_total' order by orders_id DESC";
	} 
	elseif (isset($_GET['status']) &&  tep_not_null($_GET['status'])) 
	{
		$status = tep_db_prepare_input($_GET['status']);
		$orders_query_raw = "select o.orders_id, o.jobseeker_name, o.payment_method, o.date_purchased, o.last_modified, o.currency, o.currency_value, s.orders_status_name, ot.text as order_total from " . JOBSEEKER_ORDER_TABLE . " o left join " . JOBSEEKER_ORDER_TOTAL_TABLE . " ot on (o.orders_id = ot.orders_id), " . ORDER_STATUS_TABLE . " s where o.orders_status = s.orders_status_id and s.language_id = '" . (int)$languages_id . "' and s.orders_status_id = '" . (int)$status . "' and ot.class = 'ot_total' order by o.orders_id DESC";
	} 
	else 
	{
		$orders_query_raw = "select o.orders_id, o.jobseeker_name, o.payment_method, o.date_purchased, o.last_modified, o.currency, o.currency_value, s.orders_status_name, ot.text as order_total from " . JOBSEEKER_ORDER_TABLE . " o left join " . JOBSEEKER_ORDER_TOTAL_TABLE . " ot on (o.orders_id = ot.orders_id), " . ORDER_STATUS_TABLE . " s where $whereClause o.orders_status = s.orders_status_id and s.language_id = '" . (int)$languages_id . "' and ot.class = 'ot_total' order by o.orders_id DESC";
  //echo $orders_query_raw;
	}
	$orders_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $orders_query_raw, $orders_query_numrows);
	$orders_query = tep_db_query($orders_query_raw);
 $alternate=1;
	while ($orders = tep_db_fetch_array($orders_query)) 
	{
		if ((!isset($_GET['oID']) || (isset($_GET['oID']) && ($_GET['oID'] == $orders['orders_id']))) && !isset($oInfo)) 
		{
			$oInfo = new objectInfo($orders);
		}
		if (isset($oInfo) && is_object($oInfo) && ($orders['orders_id'] == $oInfo->orders_id)) 
		{
			$row_selected=' id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_JOBSEEKER_ORDERS, tep_get_all_get_params(array('oID', 'action','selected_box')) . 'oID=' . $oInfo->orders_id . '&action=edit') . '\'"';
   $action_image=tep_image(PATH_TO_IMAGE.'icon_arrow_right.gif',IMAGE_EDIT); 
		} 
		else 
		{
			$row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_JOBSEEKER_ORDERS, tep_get_all_get_params(array('oID','selected_box')) . 'oID=' . $orders['orders_id']) . '\'"';
   $action_image='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_JOBSEEKER_ORDERS, tep_get_all_get_params(array('oID','selected_box')) . 'oID=' . $orders['orders_id']) . '">'.tep_image(PATH_TO_IMAGE.'icon_info.gif',IMAGE_INFO).'</a>'; 
		}
  $alternate++;
  $template->assign_block_vars('order', array( 'row_selected' => $row_selected,
   'name' => '<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_JOBSEEKER_ORDERS, tep_get_all_get_params(array('oID', 'action','selected_box')) . 'oID=' . $orders['orders_id'] . '&action=edit') . '">' . tep_image(PATH_TO_IMAGE . 'preview.gif', 'Edit') . '</a>&nbsp;' . tep_db_output($orders['jobseeker_name']),
   'order_total' => strip_tags($orders['order_total']),
   'date_purchased' => tep_date_short($orders['date_purchased']),
   'status' => tep_db_output($orders['orders_status_name']),
   'action' => $action_image,
   ));
 	}
/////
$ADMIN_RIGHT_HTML="";
$heading = array();
$contents = array();

switch ($action) 
{
	case 'delete':
		$heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_ORDER . '</b>');
		$contents = array('form' => tep_draw_form('orders', PATH_TO_ADMIN.FILENAME_ADMIN1_JOBSEEKER_ORDERS, tep_get_all_get_params(array('oID', 'action','selected_box')) . 'oID=' . $oInfo->orders_id . '&action=deleteconfirm'));
		$contents[] = array('text' => TEXT_INFO_DELETE_INTRO . '<br><br><b>' . $cInfo->jobseeker_firstname . ' ' . $cInfo->jobseeker_lastname . '</b>');
		$contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit(PATH_TO_BUTTON.'button_delete.gif', IMAGE_DELETE) . ' <a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_JOBSEEKER_ORDERS, tep_get_all_get_params(array('oID', 'action','selected_box')) . 'oID=' . $oInfo->orders_id) . '">' . tep_image_button(PATH_TO_BUTTON.'button_cancel.gif', IMAGE_CANCEL) . '</a>');
	break;
	default:
		if (isset($oInfo) && is_object($oInfo)) 
		{
			$heading[] = array('text' => '<b>[' . $oInfo->orders_id . ']&nbsp;&nbsp;' . tep_date_short($oInfo->date_purchased) . '</b>');
			$contents[] = array('align' => 'center', 'text' => '<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_JOBSEEKER_ORDERS, tep_get_all_get_params(array('oID', 'action','selected_box')) . 'oID=' . $oInfo->orders_id . '&action=edit') . '">' . tep_image_button(PATH_TO_BUTTON.'button_edit.gif', IMAGE_EDIT) . '</a> <a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_JOBSEEKER_ORDERS, tep_get_all_get_params(array('oID', 'action','selected_box')) . 'oID=' . $oInfo->orders_id . '&action=delete') . '">' . tep_image_button(PATH_TO_BUTTON.'button_delete.gif', IMAGE_DELETE) . '</a>');
			//$contents[] = array('align' => 'center', 'text' => '<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_ORDERS_INVOICE, 'oID=' . $oInfo->orders_id) . '" TARGET="_blank">' . tep_image_button(PATH_TO_BUTTON.'button_invoice.gif', IMAGE_ORDERS_INVOICE) . '</a>');
			$contents[] = array('text' => '<br>' . TEXT_DATE_ORDER_CREATED . ' ' . tep_date_short($oInfo->date_purchased));
			if (tep_not_null($oInfo->last_modified)) 
				$contents[] = array('text' => TEXT_DATE_ORDER_LAST_MODIFIED . ' ' . tep_date_short($oInfo->last_modified));
			$contents[] = array('text' => '<br>' . TEXT_INFO_PAYMENT_METHOD . ' '  . $oInfo->payment_method);
		}
	break;
}

////
if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) 
{
 $box = new right_box;
	$RIGHT_BOX_WIDTH=RIGHT_BOX_WIDTH;
 $ADMIN_RIGHT_HTML.= $box->infoBox($heading, $contents);
}
else
{
	$RIGHT_BOX_WIDTH='0';
}
/////
	$template->assign_vars(array(
		'HEADING_TITLE'=>HEADING_TITLE,
		'search_form'=>$search_form,
		'search_text'=>$search_text,
		'order_form'=>$order_form,
		'order_text'=>$order_text,
		'TABLE_HEADING_CUSTOMERS'=>TABLE_HEADING_CUSTOMERS,
		'TABLE_HEADING_ORDER_TOTAL'=>TABLE_HEADING_ORDER_TOTAL,
		'TABLE_HEADING_DATE_PURCHASED'=>TABLE_HEADING_DATE_PURCHASED,
		'TABLE_HEADING_STATUS'=>TABLE_HEADING_STATUS,
		'TABLE_HEADING_ACTION'=>TABLE_HEADING_ACTION,
		'count_rows'=>$orders_split->display_count($orders_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_ORDERS),
		'no_of_pages'=>$orders_split->display_links($orders_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'oID', 'action','selected_box'))),
		'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
		'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
		'update_message'=>$messageStack->output()));
	$template->pparse('order');
}
?>