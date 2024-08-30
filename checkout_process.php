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
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_CHECKOUT_PROCESS);
$template->set_filenames(array('email'=>'recruiter_order_template_template.htm','de_email'=>'de_recruiter_order_template_template.htm'));
include_once(FILENAME_BODY);
$demo_payment=false;
$complate_payment=false;
if(!check_login("recruiter"))
{
 $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
 tep_redirect(FILENAME_RECRUITER_LOGIN);
}
if(isset($_SESSION['product_id']))
{
 $product_id=(int)$_SESSION['product_id'];
}
else
{
 $product_id=(int)$_GET['product_id'];
}
if(!$row=getAnyTableWhereData(PLAN_TYPE_TABLE,'id="'.tep_db_input($product_id).'"','id,fee'))
{
 $messageStack->add_session(SORRY_PRODUCT_NOT_EXIST, 'error');
 tep_redirect(FILENAME_RECRUITER_CONTROL_PANEL);
}
///////////////
if($row['fee']<=0.0 && $_SESSION['payment']!='cc')
{
 tep_redirect(tep_href_link(FILENAME_DEMO_PAYMENT,'product_id='.$product_id));
}
////////////////////////
$checked_price=$currencies->format_without_symbol($row['fee'], ($row['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($row['currency']==DEFAULT_CURRENCY?$currencies->get_value($row['currency']):''));

//echo $checked_price;
//print_r($row);
$_SESSION['sess_gift']=false;
$gift_code=$_SESSION['gift_code'];
//echo $gift_code;
//die();
if(tep_not_null(MODULE_PAYMENT_INSTALLED) && (!isset($_SESSION['payment'])))
{
 if($gift_code!='')
 {
  if($row_gift=getAnyTableWhereData(GIFT_TABLE,'user ="recruiter"  and certificate_number="'.tep_db_input($gift_code).'"',"*"))
  {
   switch($row_gift['discount_type_id'])
   {
    case 3:
     if($row_gift['amount']<$checked_price)
     {
      $messageStack->add_session(ERROR_EXTRA_PAYMENT, 'error');
      tep_redirect(FILENAME_CHECKOUT_PAYMENT.'?product_id='.$product_id);
     }
     unset($_SESSION['payment']);
     $_SESSION['sess_gift']=true;
     $gift_id=$row_gift['gift_id'];
     $gift_amount=$row_gift['amount'];
     break;
   }
  }
 }
 else
	tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'product_id='.$product_id));
}
$payment=$_SESSION['payment'];

// load all enabled payment modules
include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'payment.php');
$payment_modules = new payment($payment);
if($gift_code!='')
{
 include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'gift.php');
 $gift = new gift();
 $obj_gift=$gift->check_status($gift_code);
}
include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'order.php');
$order = new order;
//print_r($order);die();
// load the before_process function from the payment modules
$payment_modules->before_process();

include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'order_total.php');
$order_total_modules = new order_total;
$order_totals = $order_total_modules->process();
$sql_data_array = array('recruiter_id' => $_SESSION['sess_recruiterid'],
			'product_id' => $order->products['id'],
			'recruiter_name' => $order->customer['firstname'] . ' ' . $order->customer['lastname'],
			'recruiter_email_address' => $order->customer['email_address'],
			'recruiter_company' => $order->customer['company'],
			'recruiter_street_address' => $order->customer['street_address'],
			'recruiter_city' => $order->customer['city'],
			'recruiter_state' => $order->customer['state'],
			'recruiter_country' => $order->customer['country'],
			'recruiter_zip' => $order->customer['zip'],
			'recruiter_telephone' => $order->customer['telephone'],
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
			'comments' => $_SESSION['comments'],
			'admin_comment' => $_SESSION['admin_comment']
			);

 if($order->products['fee']<=0)
 {
  $sql_data_array['orders_status']=3;
  $invoice_order_status=3;
  $demo_payment=true;
 }
 elseif($order->info['payment_method']=='PayPal' || $order-> info['payment_method']=='PayUMoney Checkout')
 {
  $sql_data_array['orders_status']=$order->info['order_status'];
  if($order->info['order_status']==3)
  $complate_payment=true;
 }
 elseif($order->info['payment_method']=='PayUMoney Checkout')
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
                        //print_r($sql_data_array);
                        //die();
tep_db_perform(ORDER_TABLE, $sql_data_array);
$row_id_check=getAnyTableWhereData(ORDER_TABLE,"recruiter_email_address='".tep_db_input($order->customer['email_address'])."' order by orders_id desc limit 0,1","orders_id");
$insert_id = $row_id_check['orders_id'];
for ($i=0, $n=sizeof($order_totals); $i<$n; $i++)
{
		$sql_data_array = array('orders_id' => $insert_id,
		'title' => $order_totals[$i]['title'],
		'text' => $order_totals[$i]['text'],
		'value' => $order_totals[$i]['value'],
		'class' => $order_totals[$i]['code'],
		'sort_order' => $order_totals[$i]['sort_order']);
		tep_db_perform(ORDER_TOTAL_TABLE, $sql_data_array);
}

$sql_data_array = array('order_id' => $insert_id,
                        'inserted' => 'now()',
                        'plan_type_name' => $order->products['plan_type_name'],
                        'time_period' => $order->products['time1'],
                        'time_period1' => $order->products['time2'],
                        'fee' => $order->products['fee'],
                        'total_price' => $order->products['total_price'],
                        'currency' => $order->products['currency'],
                        'job' => $order->products['job1'],
                        'cv' => $order->products['cv1'],
                        'sms' => $order->products['sms1'],
                        'featured_job' => $order->products['featured_job']
                        );
  tep_db_perform(ORDER_HISTORY_TABLE, $sql_data_array);
  $products_ordered = $order->products['plan_type_name'] .' ( '.$currencies->format($order->products['fee'], ($order->products['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($order->products['currency']==DEFAULT_CURRENCY?$currencies->get_value($order->products['currency']):'')).')';
  for ($i=0, $n=sizeof($order_totals); $i<$n; $i++) {
    $order_detail .= strip_tags($order_totals[$i]['title']) . ' ' . strip_tags($order_totals[$i]['text']) . "\n";
  }
  $billing_address=$order->customer['company']."<br>".
                   $order->customer['firstname']."<br>".
                   $order->customer['lastname']."<br>".
                   $order->customer['street_address']."<br>".
                   $order->customer['state']."<br>".
                   $order->customer['zip']."<br>Phone #: ".
                   $order->customer['telephone'];

  if (is_object($$payment)) {
    $payment_method_detail='';
    $payment_class = $$payment;
    $payment_method_detail=$payment_class->title . "\n\n";
    if ($payment_class->email_footer) {
      $payment_method_detail .= $payment_class->email_footer . "\n\n";
    }
  }
  $plan_price=$currencies->format($order->products['fee'], ($order->products['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($order->products['currency']==DEFAULT_CURRENCY?$currencies->get_value($order->products['currency']):''));
  $data_array=array('order_id'=>$insert_id,
                    'invoice_date'=>date('jS,M Y'),
                    'recruiter_name'=>tep_db_output($order->customer['firstname'] . ' ' . $order->customer['lastname']),
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
  //$message = new email();
  $invoice_status=getAnyTableWhereData(ORDER_STATUS_TABLE," orders_status_id='".tep_db_input($invoice_order_status)."' and language_id = '" . (int)$languages_id . "'","orders_status_name");
  $template->assign_vars(array(
      'site_title'=>tep_db_output(SITE_TITLE),
      'order_no'=>$insert_id,
      'order_invoice'=>'<a href="'.tep_href_link(FILENAME_RECRUITER_ACCOUNT_HISTORY_INFO, 'order_id=' . $insert_id, 'SSL', false).' target="_blank">'.tep_href_link(FILENAME_RECRUITER_ACCOUNT_HISTORY_INFO, 'order_id=' . $insert_id, 'SSL', false).'</a>',
      'order_date'=>strftime(DATE_FORMAT_LONG),
      'order_status'=>($invoice_order_status>0)?$invoice_status['orders_status_name']:'',
      'user_comment'=>tep_db_output($order->info['comments']),
      'product_detail'=>tep_db_output($products_ordered),
      'billing_address'=>nl2br($billing_address),
      'order_detail'=>nl2br($order_detail),
      'payment_method_detail'=>nl2br($payment_method_detail),
      'logo'=>'<a href="'.tep_href_link("").'">'.tep_image(PATH_TO_IMG.DEFAULT_SITE_LOGO,tep_db_output(SITE_TITLE)).'</a>',
      ));
  $email_order=stripslashes($template->pparse1(TEXT_LANGUAGE.'email'));
  //$message->add_html($email_order);
  //$message->build_message();
  //$message->send($order->customer['firstname'] . ' ' . $order->customer['lastname'], $order->customer['email_address'], SITE_TITLE, ADMIN_EMAIL, EMAIL_TEXT_SUBJECT);
  //$message->send(tep_db_output(SITE_TITLE), ADMIN_EMAIL, SITE_TITLE, ADMIN_EMAIL, EMAIL_TEXT_SUBJECT);
  // load the after_process function from the payment modules
  tep_mail($order->customer['firstname'] . ' ' . $order->customer['lastname'],$order->customer['email_address'],EMAIL_TEXT_SUBJECT,$email_order,ADMIN_EMAIL, SITE_TITLE) ;
  //tep_mail(SITE_OWNER,ADMIN_EMAIL,EMAIL_TEXT_SUBJECT,$email_order,ADMIN_EMAIL, SITE_TITLE) ;

  $payment_modules->after_process();
  if($gift_code!='')
  {
   if($row=getAnyTableWhereData(GIFT_USED_TABLE,"user_id='".$_SESSION['sess_recruiterid']."' and gift_id='".$gift_id."'"))
   {
    tep_db_perform(GIFT_USED_TABLE, array('user_id'=>$_SESSION['sess_recruiterid'],
                                          'gift_id'=>$gift_id,
                                          'gift_amount'=>$row['gift_amount']+$order->products['job_posting_total_fee'],
                                          'updated'=>'now()'),'update',"recruiter_id='".$_SESSION['sess_recruiterid']."' and gift_id='".$gift_id."'");
   }
   else
   {
    tep_db_perform(GIFT_USED_TABLE, array('user_id'=>$_SESSION['sess_recruiterid'],
                                          'gift_id'=>$gift_id,
                                          'gift_amount'=>$order->products['job_posting_total_fee'],
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
 $recruiter_id=$_SESSION['sess_recruiterid'];
 $order_id=$insert_id;
 $check_status_query = tep_db_query("select product_id, recruiter_id, recruiter_name, recruiter_email_address, orders_status, date_purchased from " . ORDER_TABLE . " where orders_id = '" . (int)$order_id . "'");
 $check_status = tep_db_fetch_array($check_status_query);
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
  if($row_check=getAnyTableWhereData(RECRUITER_ACCOUNT_HISTORY_TABLE,"recruiter_id='".$recruiter_id."' and  plan_for='".$plan_history_array[$i]['plan_for']."' order by end_date desc limit 0,1","id,end_date"))
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
  $plan_type_name=$product_row[TEXT_LANGUAGE.'plan_type_name'];
  $sql_data_array=array('recruiter_id'=>$recruiter_id,
                        'order_id'=>$order_id,
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
elseif($complate_payment)
{
 $messageStack->add_session(TEXT_PAYPAL_SUCCESS, 'success');
 $recruiter_id=$_SESSION['sess_recruiterid'];
 $order_id=$insert_id;
 $check_status_query = tep_db_query("select product_id, recruiter_id, recruiter_name, recruiter_email_address, orders_status, date_purchased from " . ORDER_TABLE . " where orders_id = '" . (int)$order_id . "'");
 $check_status = tep_db_fetch_array($check_status_query);
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
 ////////////////////////////////////////////////////////
 for($i=0;$i<count($plan_history_array);$i++)
 {
  if($row_check=getAnyTableWhereData(RECRUITER_ACCOUNT_HISTORY_TABLE,"recruiter_id='".$recruiter_id."' and  plan_for='".$plan_history_array[$i]['plan_for']."' order by end_date desc limit 0,1","id,end_date,recruiter_job,recruiter_cv,job_enjoyed,cv_enjoyed"))
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
					case 'resume_search':
					 if($product_row['cv_enjoyed'] >= $product_row['recruiter_cv'])
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
                        'order_id'=>$order_id,
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
 ////////////////////////////////////////////
}
else
{
 $messageStack->add_session(TEXT_SUCCESS, 'success');
}
tep_redirect(tep_href_link(FILENAME_RECRUITER_CONTROL_PANEL));
?>