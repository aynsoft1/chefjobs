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
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_JOBSEEKER_CHECKOUT_PROCESS);
$template->set_filenames(array('jobseeker_email'=>'jobseeker_order_template_template.htm','fr_jobseeker_email'=>'fr_jobseeker_order_template_template.htm'));
include_once(FILENAME_BODY);
$demo_payment=false;
$complate_payment=false;
$r_order_id = tep_db_prepare_input($_POST['ORDER_ID']);
$r_account   = tep_db_prepare_input($_POST['ACCOUNT']);

if(!check_login("jobseeker"))
{
 	$messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
	tep_redirect(FILENAME_JOBSEEKER_LOGIN);
}
if(isset($_SESSION['product_id']))
{
	$product_id=(int)$_SESSION['product_id'];
}
else
{
	$product_id=(int)$_GET['product_id'];
}
if(!$row=getAnyTableWhereData(JOBSEEKER_PLAN_TYPE_TABLE,'id="'.tep_db_input($product_id).'"','id'))
{
	$messageStack->add_session(SORRY_PRODUCT_NOT_EXIST, 'error');
	tep_redirect(FILENAME_JOBSEEKER_CONTROL_PANEL);
}
///////////////
/*if($row['plan_type_name']=='Demo' && $_SESSION['payment']!='cc')
 {
 tep_redirect(tep_href_link(FILENAME_DEMO_PAYMENT,'product_id='.$product_id));
 }*/
////////////////////////
$checked_price=$currencies->format_without_symbol($row['fee'], ($row['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($row['currency']==DEFAULT_CURRENCY?$currencies->get_value($row['currency']):''));
$_SESSION['sess_gift']=false;
$gift_code=$_SESSION['gift_code'];

//echo $checked_price;
//print_r($row);
//die();
if(tep_not_null(MODULE_PAYMENT_INSTALLED) && (!isset($_SESSION['payment'])))
{

  tep_redirect(tep_href_link(FILENAME_JOBSEEKER_CHECKOUT_PAYMENT, 'product_id='.$product_id));
}
if($gift_code!='')
 {
  if($row_gift=getAnyTableWhereData(GIFT_TABLE,'user ="jobseeker"  and certificate_number="'.tep_db_input($gift_code).'"',"*"))
  {
    $gift_id=$row_gift['gift_id'];
  }
 }
$payment=$_SESSION['payment'];
// load all enabled payment modules
include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'payment.php');
$payment_modules = new payment($payment);

if($gift_code!='')
{
 include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'gift.php');
 $gift = new gift();
 $obj_gift=$gift->check_status($gift_code,'jobseeker');
}
include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'jobseeker_order.php');
$order = new order;
//print_r($order);die();
// load the before_process function from the payment modules
$payment_modules->before_process();

include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'order_total.php');
$order_total_modules = new order_total;
$order_totals = $order_total_modules->process();
$sql_data_array = array('jobseeker_id' => $_SESSION['sess_jobseekerid'],
						'product_id' => $order->products['id'],
						'jobseeker_name' => $order->customer['firstname'] . ' ' . $order->customer['lastname'],
						'jobseeker_email_address' => $order->customer['email_address'],
						'jobseeker_street_address' => $order->customer['street_address'],
						'jobseeker_city' => $order->customer['city'], 
						'jobseeker_state' => $order->customer['state'], 
						'jobseeker_country' => $order->customer['country'], 
						'jobseeker_zip' => $order->customer['zip'], 
						'jobseeker_telephone' => $order->customer['telephone'], 
						'billing_name' => $order->billing['firstname'] . ' ' . $order->customer['lastname'],
						'billing_company' => $order->billing['company'],
						'billing_street_address' => $order->billing['street_address'],
						'billing_city' => $order->billing['city'], 
						'billing_state' => $order->billing['state'], 
						'billing_country' => $order->billing['country'], 
						'billing_zip' => $order->billing['zip'], 
						'billing_telephone' => $order->billing['telephone'], 
						'payment_method' => $order->info['payment_method'], 
						'cc_type' => $order->info['cc_type'], 
						'cc_owner' => $order->info['cc_owner'], 
						'cc_number' => $order->info['cc_number'], 
						'cc_expires' => $order->info['cc_expires'], 
						'date_purchased' => 'now()', 
						'currency' => $order->info['currency'], 
						'currency_value' => $order->info['currency_value'],
						'comments' => $_SESSION['comments']);
if($order->products['fee']<=0)
{
	$sql_data_array['orders_status']=3;
	$invoice_order_status=3;
	$demo_payment=true;
}
elseif($order->info['payment_method']=='PayPal')
{
	$sql_data_array['orders_status']=$order->info['order_status'];
	if($order->info['order_status']==3)
	$complate_payment=true;
}
else
{
	$sql_data_array['orders_status']=$order->info['order_status'];
	$invoice_order_status=$order->info['order_status'];
}
tep_db_perform(JOBSEEKER_ORDER_TABLE, $sql_data_array);
$row_id_check=getAnyTableWhereData(JOBSEEKER_ORDER_TABLE,"jobseeker_email_address='".tep_db_input($order->customer['email_address'])."' order by orders_id desc limit 0,1","orders_id");
$insert_id = $row_id_check['orders_id'];
for ($i=0, $n=sizeof($order_totals); $i<$n; $i++)
{
	$sql_data_array = array('orders_id' => $insert_id,
	                       'title' => $order_totals[$i]['title'],
						   'text' => $order_totals[$i]['text'],
						   'value' => $order_totals[$i]['value'], 
						   'class' => $order_totals[$i]['code'], 
						   'sort_order' => $order_totals[$i]['sort_order']);
	tep_db_perform(JOBSEEKER_ORDER_TOTAL_TABLE, $sql_data_array);
}

$sql_data_array = array('order_id' => $insert_id,
                        'inserted' => 'now()',
                        'plan_type_name' => $order->products['plan_type_name'],
                        'time_period' => $order->products['time1'],
                        'time_period1' => $order->products['time2'],
                        'fee' => $order->products['fee'],
                        'total_price' => $order->products['total_price'],
                        'currency' => $order->products['currency']);
tep_db_perform(JOBSEEKER_ORDER_HISTORY_TABLE, $sql_data_array);
$products_ordered = $order->products['plan_type_name'] .' ( '.$currencies->format($order->products['fee'], ($order->products['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($order->products['currency']==DEFAULT_CURRENCY?$currencies->get_value($order->products['currency']):'')).')';
///mail to recruiter
// lets start with the email confirmation
//$email_order = SITE_TITLE . "\n" .
//               EMAIL_SEPARATOR . "\n" .
//               EMAIL_TEXT_ORDER_NUMBER . ' ' . $insert_id . "\n" .
//               EMAIL_TEXT_INVOICE_URL . ' ' . tep_href_link(FILENAME_RECRUITER_ACCOUNT_HISTORY_INFO, 'order_id=' . $insert_id, 'SSL', false) . "\n" .
//               EMAIL_TEXT_DATE_ORDERED . ' ' . strftime(DATE_FORMAT_LONG) . "\n\n";
//if ($order->info['comments'])
//{
//$email_order .= tep_db_output($order->info['comments']) . "\n\n";
//}
//$email_order .= EMAIL_TEXT_PRODUCTS . "\n" .
//                EMAIL_SEPARATOR . "\n" .
//                $products_ordered . "\n" .
//                $products_user . "\n" .
//                EMAIL_SEPARATOR . "\n";

for ($i=0, $n=sizeof($order_totals); $i<$n; $i++) {
	//$email_order .= strip_tags($order_totals[$i]['title']) . ' ' . strip_tags($order_totals[$i]['text']) . "\n";
	$order_detail .= strip_tags($order_totals[$i]['title']) . ' ' . strip_tags($order_totals[$i]['text']) . "\n";
}
$billing_address=$order->customer['company']."<br>".
$order->customer['firstname']."<br>".
$order->customer['lastname']."<br>".
$order->customer['street_address']."<br>".
$order->customer['state']."<br>".
$order->customer['zip']."<br>Phone #: ".
$order->customer['telephone'];

//$email_order .= "\n" . EMAIL_TEXT_BILLING_ADDRESS . "\n" .
//                EMAIL_SEPARATOR . "\n" .
//                $billing_address . "\n\n";
if (is_object($$payment)) {
	//$email_order .= EMAIL_TEXT_PAYMENT_METHOD . "\n" .
	//                EMAIL_SEPARATOR . "\n";
	$payment_method_detail='';
	$payment_class = $$payment;
	//$email_order .= $payment_class->title . "\n\n";
	$payment_method_detail=$payment_class->title . "\n\n";
	if ($payment_class->email_footer) {
		$payment_method_detail .= $payment_class->email_footer . "\n\n";
		//$email_order .= $payment_class->email_footer . "\n\n";
	}
}
$plan_price=$currencies->format($order->products['fee'], ($order->products['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($order->products['currency']==DEFAULT_CURRENCY?$currencies->get_value($order->products['currency']):''));
$data_array=array('order_id'=>$insert_id,
                    'invoice_date'=>date('jS,M Y'),
                    'jobseeker_name'=>tep_db_output($order->customer['firstname'] . ' ' . $order->customer['lastname']),
                    'company_name'=>tep_db_output($order->customer['company']),
                    'address'=>tep_db_output($order->customer['address']),
                    'state'=>tep_db_output($order->customer['state']),
                    'zip_code'=>tep_db_output($order->customer['zip']),
                    'country'=>tep_db_output($order->customer['country']),
                    'telephone'=>tep_db_output($order->customer['telephone']),
                    'fax'=>tep_db_output($order->customer['fax']),
                    'plan_type'=>tep_db_output($order->products['plan_type_name']),
                    'time_period'=>str_replace('&nbsp;',' ',$order->products['time_period']),
                    'plan_price'=>$plan_price
);
//include_once('pdftest.php');
//$message = new email();
//if($order->products['plan_type_name']!='Demo')
//{
//$destination=PATH_TO_MAIN_PHYSICAL.PATH_TO_PDF_FILES.$file_name;
//$handle = fopen($destination, "r");
//$contents = fread($handle, filesize($destination));
//fclose($handle);
//$file_name=explode("_",$file_name);
//$message->add_attachment($contents, substr($file_name[1],14));
//}
$invoice_status=getAnyTableWhereData(ORDER_STATUS_TABLE," orders_status_id='".tep_db_input($invoice_order_status)."' and language_id = '" . (int)$languages_id . "'","orders_status_name");
$template->assign_vars(array(
      'site_title'=>tep_db_output(SITE_TITLE),
      'order_no'=>$insert_id,
      'order_invoice'=>'<a href="'.tep_href_link(FILENAME_JOBSEEKER_ACCOUNT_HISTORY_INFO, 'order_id=' . $insert_id, 'SSL', false).' target="_blank">'.tep_href_link(FILENAME_JOBSEEKER_ACCOUNT_HISTORY_INFO, 'order_id=' . $insert_id, 'SSL', false).'</a>',
      'order_date'=>strftime(DATE_FORMAT_LONG),
      'order_status'=>($invoice_order_status>0)?$invoice_status['orders_status_name']:'',
      'user_comment'=>tep_db_output($order->info['comments']),
      'product_detail'=>tep_db_output($products_ordered),
      'billing_address'=>nl2br($billing_address),
      'order_detail'=>nl2br($order_detail),
      'payment_method_detail'=>nl2br($payment_method_detail),
      'logo'=>'<a href="'.tep_href_link("").'">'.tep_image(PATH_TO_IMAGE.'logo.jpg',tep_db_output(SITE_TITLE)).'</a>',
));
$email_order=stripslashes($template->pparse1(TEXT_LANGUAGE.'jobseeker_email'));
$email_order =(nl2br('<font face="Verdana" size="2">'.$email_order.'</font>'));
//$message->add_html(nl2br('<font face="Verdana" size="2">'.$email_order.'</font>'));
//$message->build_message();
//$message->send($order->customer['firstname'] . ' ' . $order->customer['lastname'], $order->customer['email_address'], SITE_TITLE, ADMIN_EMAIL, EMAIL_TEXT_SUBJECT);
//$message->send(tep_db_output(SITE_TITLE), ADMIN_EMAIL, SITE_TITLE, ADMIN_EMAIL, EMAIL_TEXT_SUBJECT);
//$message->send($order->customer['firstname'] . ' ' . $order->customer['lastname'], $order->customer['email_address'], SITE_TITLE, ADMIN_EMAIL,EMAIL_TEXT_SUBJECT);
//$message->send('', 'kamal@erecruitmentsoftware.com', '', 'kamal@erecruitmentsoftware.com', EMAIL_TEXT_SUBJECT);
// load the after_process function from the payment modules
tep_mail($order->customer['firstname'] . ' ' . $order->customer['lastname'],$order->customer['email_address'],EMAIL_TEXT_SUBJECT,$email_order,ADMIN_EMAIL, SITE_TITLE) ;
$payment_modules->after_process();
  if($gift_code!='')
  {
   if($order->products['fee']< $order->products['discount_amount'])
   {
     $discount_amount =  $order->products['fee'];	   
   }
   else
   {
     $discount_amount =  $order->products['discount_amount'];
   }
   if($row=getAnyTableWhereData(GIFT_USED_TABLE,"user_id='".$_SESSION['sess_jobseekerid']."' and gift_id='".$gift_id."'"))
   {
    tep_db_perform(GIFT_USED_TABLE, array('user_id'=>$_SESSION['sess_jobseekerid'],
                                          'gift_id'=>$gift_id,
                                          'gift_amount'=>$row['gift_amount']+$discount_amount,
                                          'updated'=>'now()'),'update',"user_id='".$_SESSION['sess_jobseekerid']."' and gift_id='".$gift_id."'");
   }
   else
   {
    tep_db_perform(GIFT_USED_TABLE, array('user_id'=>$_SESSION['sess_jobseekerid'],
                                          'gift_id'=>$gift_id,
                                          'gift_amount'=>$discount_amount,
                                          'inserted'=>'now()',
    ));
   }
  }
// unregister session variables used during checkout
unset($_SESSION['gift_code']);
unset($_SESSION['billto']);
unset($_SESSION['payment']);
unset($_SESSION['comments']);
unset($_SESSION['product_id']);
if($demo_payment)
{
	$messageStack->add_session(TEXT_DEMO_SUCCESS, 'success');
	$jobseeker_id=$_SESSION['sess_jobseekerid'];
	$order_id=$insert_id;
	$check_status_query = tep_db_query("select product_id, jobseeker_id, jobseeker_name, jobseeker_email_address, orders_status, date_purchased from " . JOBSEEKER_ORDER_TABLE . " where orders_id = '" . (int)$order_id . "'");
	$check_status = tep_db_fetch_array($check_status_query);
	$product_id=$check_status['product_id'];
	$product_row = getAnyTableWhereData(JOBSEEKER_ORDER_HISTORY_TABLE,"order_id='".$order_id."'");
	$jobseeker_id=$check_status['jobseeker_id'];
	$sql_data_array=array('orders_date_finished'=>'now()');
	tep_db_perform(JOBSEEKER_ORDER_TABLE, $sql_data_array, 'update', "jobseeker_id='".$jobseeker_id."'");
	if($row_check=getAnyTableWhereData(JOBSEEKER_ACCOUNT_HISTORY_TABLE,"jobseeker_id='".$jobseeker_id."' order by end_date desc limit 0,1","id,end_date"))
	{
		$start_date=$row_check['end_date'];
		$end_date=calculate_end_date($product_row['time_period'],$product_row['time_period1'],$start_date);
		$start_date=date("Y-m-d",mktime(0,0,0,substr($start_date,5,2),(substr($start_date,8,2)+1),substr($start_date,0,4)));
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
                       'end_date'=>$end_date,);
	tep_db_perform(JOBSEEKER_ACCOUNT_HISTORY_TABLE, $sql_data_array);
}
elseif($complate_payment && $order->info['payment_method']=='PayPal')
{
	$messageStack->add_session(TEXT_PAYPAL_SUCCESS, 'success');
	$jobseeker_id=$_SESSION['sess_jobseekerid'];
	$order_id=$insert_id;
	$check_status_query = tep_db_query("select product_id, jobseeker_id, jobseeker_name, jobseeker_email_address, orders_status, date_purchased from " . JOBSEEKER_ORDER_TABLE . " where orders_id = '" . (int)$order_id . "'");
	$check_status = tep_db_fetch_array($check_status_query);
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
else
{
	$messageStack->add_session(TEXT_SUCCESS, 'success');
}


$messageStack->add_session(JOBSEEKER_SUCCESS_PAYMENT, 'success');
tep_redirect(tep_href_link(FILENAME_JOBSEEKER_CONTROL_PANEL));
?>