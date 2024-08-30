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
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_RECRUITER_INVOICES);
$template->set_filenames(array('invoice' => 'admin1_invoices.htm','invoice1' => 'admin1_invoices1.htm','email' => 'recruiter_invoice_status_update_tmpl.htm','print_invoice'=>'admin1_invoices_print.htm'));
include_once(FILENAME_ADMIN_BODY);

// check if recruiter exists or not ///
$whereClause="";
if(isset($_GET['rID']))
{
 if($row_check_recruiter=getAnyTableWhereData(RECRUITER_TABLE,"recruiter_id='".tep_db_input($_GET['rID'])."'","recruiter_id"))
 {
  $whereClause.=" o.recruiter_id='".tep_db_input($_GET['rID'])."'";
 }
}

$whereClause=($whereClause!=""?$whereClause." and":"");

$invoices_statuses = array();
$invoices_status_array = array();
$invoices_status_query = tep_db_query("select orders_status_id, orders_status_name from " . ORDER_STATUS_TABLE . " where language_id = '" . (int)$languages_id . "'");
while ($invoices_status = tep_db_fetch_array($invoices_status_query))
{
	$invoices_statuses[] = array('id' => $invoices_status['orders_status_id'],
																												'text' => $invoices_status['orders_status_name']);
	$invoices_status_array[$invoices_status['orders_status_id']] = $invoices_status['orders_status_name'];
}
$action = (isset($_GET['action']) ? $_GET['action'] : '');

if (tep_not_null($action))
{
	switch ($action)
	{
		case 'email_invoice':
				$iID = tep_db_prepare_input($_GET['iID']);
				$email=stripslashes($template->pparse1('email'));
				tep_mail($check_status['recruiter_name'], $check_status['recruiter_email_address'], EMAIL_TEXT_SUBJECT, ($email), SITE_OWNER, ADMIN_EMAIL);
				tep_mail(SITE_OWNER, ADMIN_EMAIL, EMAIL_TEXT_SUBJECT, $email, SITE_OWNER, ADMIN_EMAIL);
				$messageStack->add(MESSAGE_SENT, 'Success');
		break;

		case 'update_invoice':
			$iID = tep_db_prepare_input($_GET['iID']);
			$status = tep_db_prepare_input($_POST['status']);
			$comments = tep_db_prepare_input($_POST['comments']);
			$invoice_updated = false;
			$check_status_query = tep_db_query("select product_id, recruiter_id, recruiter_name, recruiter_email_address, orders_status, date_purchased from " . ORDER_TABLE . " where orders_id = '" . (int)$iID . "'");
			$check_status = tep_db_fetch_array($check_status_query);
   if($check_status['invoices_status'] == "3")
   {
				$messageStack->add_session(WARNING_INVOICE_ALREADY_COMPLETED, 'warning');
 			tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_INVOICES, tep_get_all_get_params(array('action','selected_box'))));
			}
			if ( ($check_status['invoices_status'] != $status) || tep_not_null($comments))
			{
				tep_db_query("update " . ORDER_TABLE . " set orders_status = '" . tep_db_input($status) . "', last_modified = now(), admin_comment='".tep_db_input($comments)."' where orders_id = '" . (int)$iID . "'");
				$customer_notified = '0';
				if (isset($_POST['notify']) && ($_POST['notify'] == 'on'))
				{
					$notify_comments = '';
					if (isset($_POST['notify_comments']) && ($_POST['notify_comments'] == 'on'))
					{
						$notify_comments = sprintf(EMAIL_TEXT_COMMENTS_UPDATE, $comments) . "\n\n";
					}
					//$email = SITE_TITLE . "\n" . EMAIL_SEPARATOR . "\n" . EMAIL_TEXT_ORDER_NUMBER . ' ' . $iID . "\n" . EMAIL_TEXT_INVOICE_URL . ' ' . tep_href_link(FILENAME_RECRUITER_ACCOUNT_HISTORY_INFO, 'order_id=' . $iID, 'SSL') . "\n" . EMAIL_TEXT_DATE_ORDERED . ' ' . tep_date_long($check_status['date_purchased']) . "\n\n" . $notify_comments . sprintf(EMAIL_TEXT_STATUS_UPDATE, $orders_status_array[$status]);
      $template->assign_vars(array(
      'site_title'=>tep_db_output(SITE_TITLE),
      'invoice_no'=>$iID,
      'invoice_invoice'=>'<a href="'.tep_href_link(FILENAME_RECRUITER_ACCOUNT_HISTORY_INFO, 'invoice_id=' . $iID, 'SSL').' target="_blank">'.tep_href_link(FILENAME_RECRUITER_ACCOUNT_HISTORY_INFO, 'invoice_id=' . $iID, 'SSL').'</a>',
      'invoice_date'=>tep_date_long($check_status['date_purchased']),
      'invoice_status'=>$orders_status_array[$status],
      'admin_comment'=>nl2br($notify_comments),
      'logo'=>'<a href="'.tep_href_link("").'">'.tep_image(PATH_TO_IMG.DEFAULT_SITE_LOGO,tep_db_output(SITE_TITLE)).'</a>',
      ));
     $email=stripslashes($template->pparse1('email'));
					tep_mail($check_status['recruiter_name'], $check_status['recruiter_email_address'], EMAIL_TEXT_SUBJECT, ($email), SITE_OWNER, ADMIN_EMAIL);
					tep_mail(SITE_OWNER, ADMIN_EMAIL, EMAIL_TEXT_SUBJECT, $email, SITE_OWNER, ADMIN_EMAIL);
					$customer_notified = '1';
				}
    ////////// If you delete invoice status then change must be needed ////
    /////////////////////////////////////////////////////////////////////
    if(($check_status['orders_status'] != $status ) && $status==3)
    {
     $invoice_id=(int)$iID;
     $product_id=$check_status['product_id'];
     $product_row = getAnyTableWhereData(ORDER_HISTORY_TABLE,"order_id='".$order_id."'");
     $recruiter_id=$check_status['recruiter_id'];
     $plan_history_array=array();
     //////////////////////////////////////////////////////
     if($product_row['job']==0)
     {
      $recruiter_job=0;
      $recruiter_job_status='No';
     }
     else
     {
      $recruiter_job_status='Yes';
      $recruiter_job=$product_row['job'];
      $plan_history_array[]=array('plan_for'=>'job_post');
     }
     //////////////////////////////////////////////////////
     if($product_row['cv']==0)
     {
      $recruiter_cv=0;
      $recruiter_cv_status='No';
     }
     else
     {
      $recruiter_cv_status='Yes';
      $recruiter_cv=$product_row['cv'];
      $plan_history_array[]=array('plan_for'=>'resume_search');
     }
     //////////////////////////////////////////////////////
     if($product_row['sms']==0)
     {
      $recruiter_sms=0;
      $recruiter_sms_status='No';
     }
     else
     {
      $recruiter_sms_status='Yes';
      $recruiter_sms=$product_row['sms'];
     }
     //////////////////////////////////////////////////////
     $sql_data_array=array('orders_date_finished'=>'now()');
     tep_db_perform(ORDER_TABLE, $sql_data_array, 'update', "recruiter_id='".$recruiter_id."'");

     for($i=0;$i<count($plan_history_array);$i++)
     {
      if($row_check=getAnyTableWhereData(RECRUITER_ACCOUNT_HISTORY_TABLE,"recruiter_id='".$recruiter_id."' and  plan_for='".$plan_history_array[$i]['plan_for']."'   order by end_date desc limit 0,1","id,end_date,recruiter_job,recruiter_cv,job_enjoyed,cv_enjoyed"))
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
      		switch($plan_history_array[$i]['plan_for'])
								{
									case 'job_post':
										if($product_row['job_enjoyed'] >= $product_row['recruiter_job'])
										{
										///expired old paln
											tep_db_query(" update ".RECRUITER_ACCOUNT_HISTORY_TABLE." set end_date='cur_date()', updated='now()' where id='".tep_db_input($row_check['id'])."' and recruiter_id='".tep_db_input($recruiter_id)."'");
											$start_date='now()';
											$end_date=calculate_end_date($product_row['time_period'],$product_row['time_period1']);
										}
										else
										{
											$end_date=calculate_end_date($product_row['time_period'],$product_row['time_period1'],$start_date);
											$start_date=date("Y-m-d",mktime(0,0,0,substr($start_date,5,2),(substr($start_date,8,2)+1),substr($start_date,0,4)));
										}
										break;
									case 'resume_search':
										if($product_row['cv_enjoyed'] >= $product_row['recruiter_cv'])
										{
										///expired old paln
											tep_db_query(" update ".RECRUITER_ACCOUNT_HISTORY_TABLE." set end_date=subdate(curdate(),1), updated=now()  where id='".tep_db_input($row_check['id'])."' and recruiter_id='".tep_db_input($recruiter_id)."'");
											$start_date='now()';
											$end_date=calculate_end_date($product_row['time_period'],$product_row['time_period1']);
										}
										else
										{
											$end_date=calculate_end_date($product_row['time_period'],$product_row['time_period1'],$start_date);
											$start_date=date("Y-m-d",mktime(0,0,0,substr($start_date,5,2),(substr($start_date,8,2)+1),substr($start_date,0,4)));
										}
										break;
								}
       }
      }
      else
      {
       $start_date='now()';
       $end_date=calculate_end_date($product_row['time_period'],$product_row['time_period1']);
      }
      $plan_type_name=$product_row['plan_type_name'];
      $sql_data_array=array('recruiter_id'=>$recruiter_id,
                            'order_id'=>$invoice_id,
                            'inserted'=>'now()',
                            'plan_type_name'=>$plan_type_name,
                            'plan_for'=>$plan_history_array[$i]['plan_for'],
                            'start_date'=>$start_date,
                            'end_date'=>$end_date,
                            'recruiter_job_status'=>$recruiter_job_status,
                            'recruiter_job'=>$recruiter_job,
                            'recruiter_cv_status'=>$recruiter_cv_status,
                            'recruiter_cv'=>$recruiter_cv,
                            'recruiter_sms_status'=>$recruiter_sms_status,
                            'recruiter_sms'=>$recruiter_sms,
                            'featured_job'=>$product_row['featured_job'],
                      );
      tep_db_perform(RECRUITER_ACCOUNT_HISTORY_TABLE, $sql_data_array);
     }
    }
    ////////////////////////////////////////////
				$invoice_updated = true;
			}
			if ($invoice_updated == true)
			{
				$messageStack->add_session(SUCCESS_INVOICE_UPDATED, 'success');
			}
			else
			{
				$messageStack->add_session(WARNING_INVOICE_NOT_UPDATED, 'warning');
			}
			tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_INVOICES, tep_get_all_get_params(array('action','selected_box')) . 'action=edit'));
		break;
		case 'deleteconfirm':
			$iID = tep_db_prepare_input($_GET['iID']);
   tep_db_query("delete from " . ORDER_TABLE . " where orders_id = '" . (int)$iID . "'");
   tep_db_query("delete from " . ORDER_TOTAL_TABLE . " where orders_id = '" . (int)$iID . "'");
   tep_db_query("delete from " . ORDER_HISTORY_TABLE . " where order_id = '" . (int)$iID . "'");
   tep_db_query("delete from " . RECRUITER_ACCOUNT_HISTORY_TABLE . " where order_id = '" . (int)$iID . "'");
			$messageStack->add_session(SUCCESS_INVOICE_DELETED, 'success');
			tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_INVOICES, tep_get_all_get_params(array('iID', 'action','selected_box'))));
		break;
	}
}
if (($action == 'edit' || $action == 'print') && isset($_GET['iID']))
{
	$iID = tep_db_prepare_input($_GET['iID']);
	$invoices_query = tep_db_query("select orders_id, orders_status, admin_comment from " . ORDER_TABLE . " where orders_id = '" . (int)$iID . "'");
	$invoice_exists = true;
 $row_comment=tep_db_fetch_array($invoices_query);
 $admin_comments=$row_comment['admin_comment'];
 $invoices_status=$row_comment['invoices_status'];
	if (!tep_db_num_rows($invoices_query))
	{
		$invoice_exists = false;
		$messageStack->add(sprintf(ERROR_INVOICE_DOES_NOT_EXIST, $iID), 'error');
	}
}
include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'order.php');
if (($action == 'edit' || $action == 'print') && ($invoice_exists == true))
{
 $invoice = new order($iID);
 $customer_address="<div class='inv-comp-name'>".$invoice->customer['company']."</div>".
                   //$invoice->customer['name']."<br>".
                   "<div>".$invoice->customer['street_address']."<br>".
                   (tep_not_null($invoice->customer['city'])?$invoice->customer['city']."</div>":'</div>').
                   "<div>".$invoice->customer['state']."</div>".
                   "<div>".$invoice->customer['zip']."</div>".
                   "<div>".$invoice->customer['country']."</div>".
                   "<div>Phone #: ".$invoice->customer['telephone']."</div>";
$customer_email_address=$invoice->customer['email_address'];
 $billing_address=$invoice->billing['company']."<br>".
                   $invoice->billing['name']."<br>".
                   $invoice->billing['street_address']."<br>".
                   (tep_not_null($invoice->billing['city'])?$invoice->billing['city']."<br>":'').
                   $invoice->billing['state']."<br>".
                   $invoice->billing['country']."<br>".
                   $invoice->billing['zip']."<br>Phone #: ".
                   $invoice->billing['telephone'];

 $credit_card_string='';
 if (tep_not_null($invoice->info['cc_type']) || tep_not_null($invoice->info['cc_owner']) || tep_not_null($invoice->info['cc_number']))
 {
  $credit_card_string.='
     <br>
      <table border="0" width="100%" cellspacing="1" cellpadding="3" class="infoBox">
       <tr class="infoBoxContent">
        <td valign="top" width="50%">
         <table border="0" cellspacing="3" cellpadding="0">
          <tr>
            <td class="label">'.ENTRY_CREDIT_CARD_TYPE.'</td>
            <td class="small">'.$invoice->info['cc_type'].'</td>
          </tr>
          <tr>
            <td class="label">'.ENTRY_CREDIT_CARD_OWNER.'</td>
            <td class="small">'.$invoice->info['cc_owner'].'</td>
          </tr>
          <tr>
            <td class="label">'.ENTRY_CREDIT_CARD_NUMBER.'</td>
            <td class="small">'.$invoice->info['cc_number'].'</td>
          </tr>
          <tr>
            <td class="label">'.ENTRY_CREDIT_CARD_EXPIRES.'</td>
            <td class="small">'.$invoice->info['cc_expires'].'</td>
          </tr>
         </table>
        </td>
       </tr>
      </table><br>';
  }
 $product_name=tep_db_output($invoice->products['plan_type_name']);
 $product_fee=tep_db_output($currencies->format($invoice->products['fee'], ($invoice->products['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($invoice->products['currency']==DEFAULT_CURRENCY?$currencies->get_value($invoice->products['currency']):'')));
 $invoice_total_text='';
 for ($i = 0, $n = sizeof($invoice->totals); $i < $n; $i++)
 {
  $invoice_total_text.='
          <tr>
           <td valign="top" class="label">'.$invoice->totals[$i]['title'].'</td>
           <td valign="top" class="small">'.$invoice->totals[$i]['text'].'</td>
          </tr>'."\n";
 }
	$template->assign_vars(array(
		'HEADING_TITLE'=>'<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_INVOICES, tep_get_all_get_params(array('action','selected_box'))) . '">' . tep_image_button(PATH_TO_BUTTON.'button_back.gif', IMAGE_BACK) . '</a><a href="#" onclick="popUp(\''.tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_INVOICES,tep_get_all_get_params(array('action','selected_box')) . 'action=print').'\')">'. tep_image_button(PATH_TO_BUTTON.'button_print.gif', 'Print') .'</a><a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_INVOICES, tep_get_all_get_params(array('iID', 'action','selected_box')) . 'iID=' . $iID . '&action=email_invoice') . '">'. tep_image_button(PATH_TO_BUTTON.'button_email.gif', 'Email') .'</a>',
		'HEADING_TITLE1'=>HEADING_TITLE1,
		'ENTRY_CUSTOMER'=>ENTRY_CUSTOMER,
		'ENTRY_CUSTOMER1'=>$customer_address,
		'ENTRY_BILLING_ADDRESS'=>ENTRY_BILLING_ADDRESS,
		'ENTRY_BILLING_ADDRESS1'=>$billing_address,
		'ENTRY_PAYMENT_METHOD'=>ENTRY_PAYMENT_METHOD,
		'ENTRY_PAYMENT_METHOD1'=>$invoice->info['payment_method'],
		'credit_card_string'=>$credit_card_string,
		'customer_email_address'=>$customer_email_address,
		'TABLE_HEADING_PRODUCTS'=>TABLE_HEADING_PRODUCTS,
		'TABLE_HEADING_PRODUCTS1'=>$product_name,
		'TABLE_HEADING_TOTAL_PRICE'=>TABLE_HEADING_TOTAL_PRICE,
		'TABLE_HEADING_TOTAL_PRICE1'=>$product_fee,
		'invoice_total_text'=>$invoice_total_text,
		'invoice_id'=>$iID,
		'plan_end_date'=>$end_date,
		'logo'=>'<a href="'.tep_href_link("").'">'.tep_image(PATH_TO_IMG.DEFAULT_SITE_LOGO,tep_db_output(SITE_TITLE),'','','class="invoice-logo"').'</a>',
		'TABLE_HEADING_DATE_ADDED'=>TABLE_HEADING_DATE_ADDED,
		'TABLE_HEADING_DATE_ADDED1'=>tep_date_short($invoice->info['date_purchased']),
		'TABLE_HEADING_STATUS'=>TABLE_HEADING_STATUS,
		'TABLE_HEADING_STATUS1'=>$invoice->info['orders_status'],
		'TABLE_HEADING_COMMENTS'=>TABLE_HEADING_COMMENTS,
		'TABLE_HEADING_COMMENTS1'=>nl2br(tep_db_output($invoice->info['comments'])).'&nbsp;',
		'TABLE_HEADING_COMMENTS2'=>tep_draw_textarea_field('comments', 'soft', '40', '5',$admin_comments),
		'form'=>tep_draw_form('status', PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_INVOICES, tep_get_all_get_params(array('action','selected_box')) . 'action=update_invoice'),
//		'print'=>'<a href="#" onclick="window.print();return false;"><i class="fa fa-print" style="font-size:16px"></i></a>',
		'ENTRY_STATUS'=>ENTRY_STATUS,
	//	'ENTRY_STATUS1'=>tep_draw_pull_down_menu('status', $orders_statuses, $orders_status),
		'ENTRY_NOTIFY_CUSTOMER'=>ENTRY_NOTIFY_CUSTOMER,
		'ENTRY_NOTIFY_CUSTOMER1'=>tep_draw_checkbox_field('notify', '', true),
		'ENTRY_NOTIFY_COMMENTS'=>ENTRY_NOTIFY_COMMENTS,
		'ENTRY_NOTIFY_COMMENTS1'=>tep_draw_checkbox_field('notify_comments', '', true),
		'button'=>tep_image_submit(PATH_TO_BUTTON.'button_update.gif', IMAGE_UPDATE),
		'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
		'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
		'update_message'=>$messageStack->output()));
	if($action == 'print')
	$template->pparse('print_invoice');
	else
	$template->pparse('invoice1');
}

else
{
	$search_form=tep_draw_form('invoices', PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_INVOICES, '', 'get');
	$search_text=HEADING_TITLE_SEARCH . ' ' . tep_draw_input_field('iID', '', 'size="12" class="ms-2 form-control form-control-sm"','') . tep_draw_hidden_field('action', 'edit');
	$invoice_form=tep_draw_form('status', PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_INVOICES, '', 'get');
	$invoice_text=' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.HEADING_TITLE_STATUS . ' ' . tep_draw_pull_down_menu('status', array_merge(array(array('id' => '', 'text' => TEXT_ALL_INVOICES)), $invoices_statuses), $_GET['status'], 'class="form-control form-control-sm form-select" style="width:115px;" onChange="this.form.submit();"');
	if (isset($_GET['cID']))
	{
		$cID = tep_db_prepare_input($_GET['cID']);
		$invoices_query_raw = "select o.orders_id, o.recruiter_company, o.recruiter_name, o.recruiter_email_address , o.recruiter_id, o.payment_method, o.date_purchased, o.last_modified, o.currency, o.currency_value, s.orders_status_name, ot.text as order_total from " . ORDER_TABLE . " o left join " . ORDER_TOTAL_TABLE . " ot on (o.orders_id = ot.orders_id), " . ORDER_STATUS_TABLE . " s where o.recruiter_id = '" . (int)$cID . "' and o.orders_status = s.orders_status_id and s.language_id = '" . (int)$languages_id . "' and ot.class = 'ot_total' order by orders_id DESC";
	}
	elseif (isset($_GET['status']) &&  tep_not_null($_GET['status']))
	{
		$status = tep_db_prepare_input($_GET['status']);
		$invoices_query_raw = "select o.orders_id, o.recruiter_company, o.recruiter_name, o.recruiter_email_address , o.payment_method, o.date_purchased, o.last_modified, o.currency, o.currency_value, s.orders_status_name, ot.text as order_total from " . ORDER_TABLE . " o left join " . ORDER_TOTAL_TABLE . " ot on (o.orders_id = ot.orders_id), " . ORDER_STATUS_TABLE . " s where o.orders_status = s.orders_status_id and s.language_id = '" . (int)$languages_id . "' and s.orders_status_id = '" . (int)$status . "' and ot.class = 'ot_total' order by o.orders_id DESC";
	}
	else
	{
		$invoices_query_raw = "select o.orders_id, o.recruiter_company, o.recruiter_name, o.recruiter_email_address , o.payment_method, o.date_purchased, o.last_modified, o.currency, o.currency_value, s.orders_status_name, ot.text as order_total from " . ORDER_TABLE . " o left join " . ORDER_TOTAL_TABLE . " ot on (o.orders_id = ot.orders_id), " . ORDER_STATUS_TABLE . " s where $whereClause o.orders_status = s.orders_status_id and s.language_id = '" . (int)$languages_id . "' and ot.class = 'ot_total' order by o.orders_id DESC";
  //echo $orders_query_raw;
	}
	$invoices_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $invoices_query_raw, $invoices_query_numrows);
	$invoices_query = tep_db_query($invoices_query_raw);
 $alternate=1;
	while ($invoices = tep_db_fetch_array($invoices_query))
	{
		if ((!isset($_GET['iID']) || (isset($_GET['iID']) && ($_GET['iID'] == $invoices['orders_id']))) && !isset($iInfo))
		{
			$iInfo = new objectInfo($invoices);
		}
		if (isset($iInfo) && is_object($iInfo) && ($invoices['orders_id'] == $iInfo->orders_id))
		{
			$row_selected=' id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_INVOICES, tep_get_all_get_params(array('iID', 'action','selected_box')) . 'iID=' . $iInfo->orders_id . '&action=edit') . '\'"';
   $action_image=tep_image(PATH_TO_IMAGE.'icon_arrow_right.gif',IMAGE_EDIT);
		}
		else
		{
			$row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_INVOICES, tep_get_all_get_params(array('iID','selected_box')) . 'iID=' . $invoices['orders_id']) . '\'"';
   $action_image='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_INVOICES, tep_get_all_get_params(array('iID','selected_box')) . 'iID=' . $invoices['orders_id']) . '">'.tep_image(PATH_TO_IMAGE.'icon_info.gif',IMAGE_INFO).'</a>';
		}
  $alternate++;
  $template->assign_block_vars('invoice', array( 'row_selected' => $row_selected,
	'invoice_no'=>$invoices['orders_id'],
//   'name' => '<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_INVOICES, tep_get_all_get_params(array('iID', 'action','selected_box')) . 'iID=' . $invoices['orders_id'] . '&action=edit') . '">' .  tep_db_output($invoices['recruiter_name']) . '</a>' ,
   'company_name' => '<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_INVOICES, tep_get_all_get_params(array('iID', 'action','selected_box')) . 'iID=' . $invoices['orders_id'] . '&action=edit') . '">' .  tep_db_output($invoices['recruiter_company']) . '</a>' ,
   'view' => '<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_INVOICES, tep_get_all_get_params(array('iID', 'action','selected_box')) . 'iID=' . $invoices['orders_id'] . '&action=edit') . '">' .tep_image(PATH_TO_IMAGE . 'preview.gif', 'Edit') . '</a>',
   'email' => '<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_INVOICES, tep_get_all_get_params(array('iID', 'action','selected_box')) . 'iID=' . $invoices['orders_id'] . '&action=email_invoice') . '"><i class="fa fa-envelope" style="font-size:16px"></i></a>',
   'email_address' => strip_tags($invoices['recruiter_email_address']),
   'invoice_total' => strip_tags($invoices['order_total']),
	'payment_method'=>tep_db_output($invoices['payment_method']),
   'date_purchased' => tep_date_short($invoices['date_purchased']),
   'status' => tep_db_output($invoices['orders_status_name']),
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
		$heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_INVOICE . '</b>');
		$contents = array('form' => tep_draw_form('invoices', PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_INVOICES, tep_get_all_get_params(array('iID', 'action','selected_box')) . 'iID=' . $iInfo->orders_id . '&action=deleteconfirm'));
		$contents[] = array('text' => TEXT_INFO_DELETE_INTRO . '<br><br><b>' . $cInfo->recruiter_firstname . ' ' . $cInfo->recruiter_lastname . '</b>');
		$contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit(PATH_TO_BUTTON.'button_delete.gif', IMAGE_DELETE) . ' <a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_INVOICES, tep_get_all_get_params(array('iID', 'action','selected_box')) . 'iID=' . $iInfo->orders_id) . '">' . tep_image_button(PATH_TO_BUTTON.'button_cancel.gif', IMAGE_CANCEL) . '</a>');
	break;
	default:
		if (isset($iInfo) && is_object($iInfo))
		{
			$heading[] = array('text' => '<b>[' . $iInfo->orders_id . ']&nbsp;&nbsp;' . tep_date_short($iInfo->date_purchased) . '</b>');
			$contents[] = array('align' => 'center', 'text' => '<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_INVOICES,tep_get_all_get_params(array('iID', 'action','selected_box')) . 'iID=' . $iInfo->orders_id . '&action=edit') . '">'. tep_draw_submit_button_field('view_invoice','View','class="btn btn-secondary"').'</a> <a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_INVOICES, tep_get_all_get_params(array('iID', 'action','selected_box')) . 'iID=' . $iInfo->orders_id . '&action=email_invoice') . '">'. tep_draw_submit_button_field('email_invoice','Email','class="btn btn-primary"'). '</a>');
			//$contents[] = array('align' => 'center', 'text' => '<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITER_ORDERS_INVOICE, 'iID=' . $iInfo->orders_id) . '" TARGET="_blank">' . tep_image_button(PATH_TO_BUTTON.'button_invoice.gif', IMAGE_ORDERS_INVOICE) . '</a>');
			$contents[] = array('text' => '<br>' . TEXT_DATE_INVOICE_CREATED . ' ' . tep_date_short($iInfo->date_purchased));
			if (tep_not_null($iInfo->last_modified))
				$contents[] = array('text' => TEXT_DATE_INVOICE_LAST_MODIFIED . ' ' . tep_date_short($iInfo->last_modified));
			$contents[] = array('text' => '<br>' . TEXT_INFO_PAYMENT_METHOD . ' '  . $iInfo->payment_method);
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
		'invoice_form'=>$invoice_form,
		'invoice_text'=>$invoice_text,
		'TABLE_HEADING_EMAIL'=>TABLE_HEADING_EMAIL,
		'TABLE_HEADING_INVOICE_NO'=>TABLE_HEADING_INVOICE_NO,
		'TABLE_HEADING_EMAIL_ADDRESS'=>TABLE_HEADING_EMAIL_ADDRESS,
		'TABLE_HEADING_COMPANY_NAME'=>TABLE_HEADING_COMPANY_NAME,
		'TABLE_HEADING_PAYMENT_METHOD'=>TABLE_HEADING_PAYMENT_METHOD,
		'TABLE_HEADING_CUSTOMERS'=>TABLE_HEADING_CUSTOMERS,
		'TABLE_HEADING_INVOICE_TOTAL'=>TABLE_HEADING_INVOICE_TOTAL,
		'TABLE_HEADING_DATE_PURCHASED'=>TABLE_HEADING_DATE_PURCHASED,
		'TABLE_HEADING_STATUS'=>TABLE_HEADING_STATUS,
		'TABLE_HEADING_VIEW'=>TABLE_HEADING_VIEW,
		'TABLE_HEADING_ACTION'=>TABLE_HEADING_ACTION,
		'count_rows'=>$invoices_split->display_count($invoices_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_INVOICES),
		'no_of_pages'=>$invoices_split->display_links($invoices_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'iID', 'action','selected_box'))),
		'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
		'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
		'update_message'=>$messageStack->output()));
	$template->pparse('invoice');
}
?>