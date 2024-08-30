<?
/**********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft Pvt. Ltd.   #**********
**********# Copyright (c) www.aynsoft.com 2005  #**********
**********************************************************/
define("CONF_ORDER_TAX",10);
define("CONF_ORDER_TAX_CASE",1);
class order 
{
 var $info, $totals, $products, $customer, $delivery, $content_type;
 function __construct($order_id = '') 
 {
  $this->info = array();
  $this->totals = array();
  $this->products = array();
  $this->customer = array();
  $this->delivery = array();
  if (tep_not_null($order_id)) 
  {
   $this->query($order_id);
  } 
  else 
  {
   $this->cart();
  }
 }

 function query($order_id) 
 {
  global $languages_id;
  $order_id = tep_db_prepare_input($order_id);
  $order_query = tep_db_query("select recruiter_id, product_id, recruiter_name, recruiter_company, recruiter_street_address, recruiter_zip, recruiter_city, recruiter_state, recruiter_country, recruiter_telephone, recruiter_email_address, billing_name, billing_company, billing_street_address,  billing_zip, billing_city, billing_state, billing_country, billing_telephone, payment_method, cc_type, cc_owner, cc_number, cc_expires, currency, currency_value, date_purchased, orders_status, last_modified, comments from " . ORDER_TABLE . " where orders_id = '" . (int)$order_id . "'");
  $order = tep_db_fetch_array($order_query);
  $totals_query = tep_db_query("select title, text from " . ORDER_TOTAL_TABLE . " where orders_id = '" . (int)$order_id . "' order by sort_order");
  while ($totals = tep_db_fetch_array($totals_query)) 
  {
   $this->totals[] = array('title' => $totals['title'],
                           'text' => $totals['text']);
  }
  $order_total_query = tep_db_query("select text from " . ORDER_TOTAL_TABLE . " where orders_id = '" . (int)$order_id . "' and class = 'ot_total'");
  $order_total = tep_db_fetch_array($order_total_query);

  $order_status_query = tep_db_query("select orders_status_name from " . ORDER_STATUS_TABLE . " where orders_status_id = '" . $order['orders_status'] . "' and language_id = '" . (int)$languages_id . "'");
  $order_status = tep_db_fetch_array($order_status_query);
  $this->info = array('currency' => $order['currency'],
                      'currency_value' => $order['currency_value'],
                      'payment_method' => $order['payment_method'],
                      'cc_type' => $order['cc_type'],
                      'cc_owner' => $order['cc_owner'],
                      'cc_number' => $order['cc_number'],
                      'cc_expires' => $order['cc_expires'],
                      'date_purchased' => $order['date_purchased'],
                      'orders_status' => $order_status['orders_status_name'],
                      'last_modified' => $order['last_modified'],
                      'total' => strip_tags($order_total['text']),
                      'comments' => $order['comments']);

  $this->customer = array('id' => $order['recruiter_id'],
                          'name' => $order['recruiter_name'],
                          'company' => $order['recruiter_company'],
                          'street_address' => $order['recruiter_street_address'],
                          'postcode' => $order['recruiter_postcode'],
                          'city' => $order['recruiter_city'],
                          'state' => $order['recruiter_state'],
                          'country' => $order['recruiter_country'],
                          'zip' => $order['recruiter_zip'],
                          'telephone' => $order['recruiter_telephone'],
                          'email_address' => $order['recruiter_email_address']);

  $this->billing = array('name' => $order['billing_name'],
                         'company' => $order['billing_company'],
                         'street_address' => $order['billing_street_address'],
                         'zip' => $order['billing_zip'],
                         'city' => $order['billing_city'],
                         'state' => $order['billing_state'],
                         'country' => $order['billing_country'],
                         'telephone' => $order['billing_telephone']);

  $order_history_query = tep_db_query("select * from " . ORDER_HISTORY_TABLE . " where order_id = '" .(int)$order_id."'");
  $order_history = tep_db_fetch_array($order_history_query);
  $this->products = array('plan_type_name' => $order_history['plan_type_name'],
                          'time_period' => $order_history['time_period'],
                          'time_period1' => $order_history['time_period1'],
                          'fee' => $order_history['fee'],
                          'currency' => $order_history['currency'],
                          'job' => $order_history['job'],
                          'cv' => $order_history['cv'],
                          'sms' => $order_history['sms'],
                          'featured_job' => $order_history['featured_job']
                      			);
 }

 function cart() 
 {
  global $currency, $currencies, $shipping, $payment;
  global $discount_type_array;
  //print_r($_POST);die();
  $recruiter_address_query = tep_db_query("select r.recruiter_first_name, r.recruiter_last_name, rl.recruiter_email_address, r.recruiter_company_name, r.recruiter_address1, r.recruiter_address2, r.recruiter_country_id, r.recruiter_state, r.recruiter_state_id, r.recruiter_city, r.recruiter_zip, r.recruiter_telephone from " . RECRUITER_LOGIN_TABLE . " as rl, " . RECRUITER_TABLE . " as r where r.recruiter_id=rl.recruiter_id and r.recruiter_id='".$_SESSION['sess_recruiterid']."'");
  $recruiter_address = tep_db_fetch_array($recruiter_address_query);
  $this->info = array('order_status' => DEFAULT_ORDERS_STATUS_ID,
                      'currency' => DEFAULT_CURRENCY,
                      'currency_value' => $currencies->currencies[DEFAULT_CURRENCY]['value'],
                      'payment_method' => $payment,
                      'cc_type' => (isset($_POST['cc_type']) ? $_POST['cc_type'] : ''),
                      'cc_owner' => (isset($_POST['cc_owner']) ? $_POST['cc_owner'] : ''),
                      'cc_number' => (isset($_POST['cc_number']) ? $_POST['cc_number'] : ''),
                      'cc_expires' => (isset($_POST['cc_expires']) ? $_POST['cc_expires'] : ''),
                      'comments' => (isset($_POST['comments']) ? $_POST['comments'] : 'N/A'));

  if (isset($GLOBALS[$payment]) && is_object($GLOBALS[$payment])) 
  {
   $this->info['payment_method'] = $GLOBALS[$payment]->title;
   if ( isset($GLOBALS[$payment]->order_status) && is_numeric($GLOBALS[$payment]->order_status) && ($GLOBALS[$payment]->order_status > 0) ) 
   {
    $this->info['order_status'] = $GLOBALS[$payment]->order_status;
   }
  }
  $this->customer = array('firstname' => $recruiter_address['recruiter_first_name'],
                          'lastname' => $recruiter_address['recruiter_last_name'],
                          'email_address' => $recruiter_address['recruiter_email_address'],
                          'company' => $recruiter_address['recruiter_company_name'],
                          'street_address' => $recruiter_address['recruiter_address1'].($recruiter_address['recruiter_address2']!=''?', ':'').$recruiter_address['recruiter_address2'],
                          'city' => $recruiter_address['recruiter_city'],
                          'state' => ($recruiter_address['recruiter_state_id']>0 ? get_name_from_table(ZONES_TABLE,TEXT_LANGUAGE.'zone_name','zone_id',$recruiter_address['recruiter_state_id']) : $recruiter_address['recruiter_state']),
                          'country' => get_name_from_table(COUNTRIES_TABLE,TEXT_LANGUAGE.'country_name','id',$recruiter_address['recruiter_country_id']),
                          'zip' => $recruiter_address['recruiter_zip'],
                          'fax' => $recruiter_address['recruiter_fax'],
                          'telephone' => $recruiter_address['recruiter_telephone']);
  $this->billing = array('firstname' => $recruiter_address['recruiter_first_name'],
                          'lastname' => $recruiter_address['recruiter_last_name'],
                          'email_address' => $recruiter_address['recruiter_email_address'],
                          'company' => $recruiter_address['recruiter_company_name'],
                          'street_address' => $recruiter_address['recruiter_address1'].$recruiter_address['recruiter_address2'],
                          'city' => $recruiter_address['recruiter_city'],
                          'state' => ($recruiter_address['recruiter_state_id']>0 ? get_name_from_table(ZONES_TABLE,TEXT_LANGUAGE.'zone_name','zone_id',$recruiter_address['recruiter_state_id']) : $recruiter_address['recruiter_state']),
                          'country' => get_name_from_table(COUNTRIES_TABLE,TEXT_LANGUAGE.'country_name','id',$recruiter_address['recruiter_country_id']),
                          'zip' => $recruiter_address['recruiter_zip'],
                          'telephone' => $recruiter_address['recruiter_telephone']);
  $products = $this->get_products();
  $this->info['fee'] = $currencies->format($products['fee'], ($products['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($products['currency']==DEFAULT_CURRENCY?$currencies->get_value($products['currency']):''));
  $this->info['subtotal_symbol']=tep_db_output($currencies->format($products['job_posting_total_fee'], ($products['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($products['currency']==DEFAULT_CURRENCY?$currencies->get_value($products['currency']):'')));
  $discount_amount=0;
  if($products['free_job']>0)
  {
   for($i=0;$i<count($discount_type_array);$i++)
   {
    if(in_array(1,$discount_type_array[$i]))
    {
     $value=$discount_type_array[$i]['text'];
     break;
    }
   }
   $this->info['subtotal_symbol'].="<br>".$value;
   $this->info['subtotal'] = $currencies->format_without_symbol($products['job_posting_total_fee'], ($products['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($products['currency']==DEFAULT_CURRENCY?$currencies->get_value($products['currency']):''));
   $this->info['total_symbol'] = $currencies->format($this->info['subtotal'], ($products['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($products['currency']==DEFAULT_CURRENCY?$currencies->get_value($products['currency']):''));
   $this->info['total'] = $this->info['subtotal'];
  }
  else if($products['discount_percentage']>0)
  {
   if(CONF_ORDER_TAX>0)
   {
    if(CONF_ORDER_TAX_CASE==1)
	{
     $tax =($products['job_posting_total_fee']*CONF_ORDER_TAX)/100;
	 $this->info['subtotal_symbol'].="<br>Tax :".$tax;

     $final_total_fee  =$products['job_posting_total_fee']+$tax;
     $discount_amount=($final_total_fee *$products['discount_percentage'])/100;
     //echo $discount_amount;
     $this->info['subtotal_symbol'].="<br>Discount ".$products['discount_percentage'].'%='.$currencies->format($discount_amount, ($products['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($products['currency']==DEFAULT_CURRENCY?$currencies->get_value($products['currency']):''));
     $this->info['subtotal'] = $currencies->format_without_symbol(($final_total_fee -$discount_amount), ($products['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($products['currency']==DEFAULT_CURRENCY?$currencies->get_value($products['currency']):''));
     $this->info['total_symbol'] = $currencies->format($this->info['subtotal'], ($products['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($products['currency']==DEFAULT_CURRENCY?$currencies->get_value($products['currency']):''));
     $this->info['total'] = $this->info['subtotal'];
	}
	else
	{
	 $discount_amount=($products['job_posting_total_fee']*$products['discount_percentage'])/100;
     //echo $discount_amount;
	 $this->info['subtotal_symbol'].="<br>Discount ".$products['discount_percentage'].'%='.$currencies->format($discount_amount, ($products['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($products['currency']==DEFAULT_CURRENCY?$currencies->get_value($products['currency']):''));
	 
	 $final_total_fee  = $products['job_posting_total_fee']-$discount_amount;
     $tax = ($final_total_fee *CONF_ORDER_TAX)/100;
	 $this->info['subtotal_symbol'].="<br>Sub Total  :".$final_total_fee;
     $this->info['subtotal_symbol'].="<br>Tax  :".$tax;	 
     $this->info['subtotal'] = $currencies->format_without_symbol(($final_total_fee+$tax), ($products['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($products['currency']==DEFAULT_CURRENCY?$currencies->get_value($products['currency']):''));
     $this->info['total_symbol'] = $currencies->format($this->info['subtotal'], ($products['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($products['currency']==DEFAULT_CURRENCY?$currencies->get_value($products['currency']):''));
     $this->info['total'] = $this->info['subtotal'];
	}
   }
   else
   {
    $discount_amount=($products['job_posting_total_fee']*$products['discount_percentage'])/100;
    //echo $discount_amount;
    $this->info['subtotal_symbol'].="<br>Discount ".$products['discount_percentage'].'%='.$currencies->format($discount_amount, ($products['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($products['currency']==DEFAULT_CURRENCY?$currencies->get_value($products['currency']):''));
    $this->info['subtotal'] = $currencies->format_without_symbol(($products['job_posting_total_fee']-$discount_amount), ($products['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($products['currency']==DEFAULT_CURRENCY?$currencies->get_value($products['currency']):''));
    $this->info['total_symbol'] = $currencies->format($this->info['subtotal'], ($products['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($products['currency']==DEFAULT_CURRENCY?$currencies->get_value($products['currency']):''));
    $this->info['total'] = $this->info['subtotal'];
   }
  }
  else if($products['discount']>0)
  {
   $discount_amount=$products['discount'];
   if(CONF_ORDER_TAX>0)
   {
    if(CONF_ORDER_TAX_CASE==1)
	{
	 $tax =($products['job_posting_total_fee']*CONF_ORDER_TAX)/100;
	 $this->info['subtotal_symbol'].="<br>Tax :".$tax;
     $final_total_fee  =$products['job_posting_total_fee']+$tax;
     $this->info['subtotal_symbol'].="<br>Discount=".$currencies->format(($final_total_fee >$discount_amount?$discount_amount:$final_total_fee), ($products['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($products['currency']==DEFAULT_CURRENCY?$currencies->get_value($products['currency']):''));
     $this->info['subtotal'] = $currencies->format_without_symbol(($final_total_fee>$discount_amount?$final_total_fee-$discount_amount:0), ($products['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($products['currency']==DEFAULT_CURRENCY?$currencies->get_value($products['currency']):''));
     $this->info['total_symbol'] = $currencies->format($this->info['subtotal'], ($products['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($products['currency']==DEFAULT_CURRENCY?$currencies->get_value($products['currency']):''));
     $this->info['total'] = $this->info['subtotal'];
	}
	else
    {
	 $final_total_fee  =$products['job_posting_total_fee']-$discount_amount;
	 if($final_total_fee>0)
	 {
	  $tax =($final_total_fee*CONF_ORDER_TAX)/100;
	  $final_total_fee =$final_total_fee+$tax;
	 }
	 else
	 {
	  $tax=0;
	  $final_total_fee=0;
	 }
	 $this->info['subtotal_symbol'].="<br>Discount=".$currencies->format(($products['job_posting_total_fee']>$discount_amount?$discount_amount:$products['job_posting_total_fee']), ($products['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($products['currency']==DEFAULT_CURRENCY?$currencies->get_value($products['currency']):''));
	 $this->info['subtotal_symbol'].="<br>Tax :".$tax;    
	 $this->info['subtotal'] = $currencies->format_without_symbol($final_total_fee, ($products['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($products['currency']==DEFAULT_CURRENCY?$currencies->get_value($products['currency']):''));
     $this->info['total_symbol'] = $currencies->format($this->info['subtotal'], ($products['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($products['currency']==DEFAULT_CURRENCY?$currencies->get_value($products['currency']):''));
     $this->info['total'] = $this->info['subtotal'];
	}
   }
   else
   {
    $discount_amount=$products['discount'];
    //echo $discount_amount;
    $this->info['subtotal_symbol'].="<br>Discount=".$currencies->format(($products['job_posting_total_fee']>$discount_amount?$discount_amount:$products['job_posting_total_fee']), ($products['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($products['currency']==DEFAULT_CURRENCY?$currencies->get_value($products['currency']):''));
    $this->info['subtotal'] = $currencies->format_without_symbol(($products['job_posting_total_fee']>$discount_amount?$products['job_posting_total_fee']-$discount_amount:0), ($products['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($products['currency']==DEFAULT_CURRENCY?$currencies->get_value($products['currency']):''));
    $this->info['total_symbol'] = $currencies->format($this->info['subtotal'], ($products['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($products['currency']==DEFAULT_CURRENCY?$currencies->get_value($products['currency']):''));
    $this->info['total'] = $this->info['subtotal'];
   }
  }
  else
  {
    if(CONF_ORDER_TAX>0)
    {     
	  $tax =($products['job_posting_total_fee']*CONF_ORDER_TAX)/100;
      $final_total_fee  =$products['job_posting_total_fee'] +$tax;
      $this->info['subtotal_symbol'].="<br>Tax :".$tax;
      $this->info['subtotal_symbol'].="<br>------------------<br>".tep_db_output($currencies->format($final_total_fee, ($products['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($products['currency']==DEFAULT_CURRENCY?$currencies->get_value($products['currency']):'')));
   	  $this->info['subtotal'] = $currencies->format_without_symbol($final_total_fee, ($products['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($products['currency']==DEFAULT_CURRENCY?$currencies->get_value($products['currency']):''));
      $this->info['total_symbol'] = $currencies->format($final_total_fee, ($products['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($products['currency']==DEFAULT_CURRENCY?$currencies->get_value($products['currency']):''));
      $this->info['total'] = $this->info['subtotal'];	  
	}
	else
    {
     $this->info['subtotal_symbol']=tep_db_output($currencies->format($products['job_posting_total_fee'], ($products['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($products['currency']==DEFAULT_CURRENCY?$currencies->get_value($products['currency']):'')));
	 $this->info['subtotal'] = $currencies->format_without_symbol($products['job_posting_total_fee'], ($products['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($products['currency']==DEFAULT_CURRENCY?$currencies->get_value($products['currency']):''));
     $this->info['total_symbol'] = $currencies->format($products['job_posting_total_fee'], ($products['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($products['currency']==DEFAULT_CURRENCY?$currencies->get_value($products['currency']):''));
     $this->info['total'] = $this->info['subtotal'];
	}
  }
  /////////////
  //$this->info['subtotal_symbol']=tep_db_output($currencies->format($products['fee'], ($products['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($products['currency']==DEFAULT_CURRENCY?$currencies->get_value($row['currency']):'')));
  //$this->info['subtotal']=tep_db_output($currencies->format_without_symbol($products['fee'], ($products['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($products['currency']==DEFAULT_CURRENCY?$currencies->get_value($row['currency']):'')));
  //$this->info['total'] = $this->info['subtotal'];
  //$this->info['total_symbol'] = $this->info['subtotal_symbol'];
  $this->products = array('id' => $products['id'],
                          'plan_type_name' => $products['plan_type_name'],
                          'time1' => $products['time1'],
                          'time2' => $products['time2'],
                          'time_period' => $products['time_period'],
                          'fee' => $products['fee'],
                          'job_posting_total_fee' => $products['fee'],
                          'job1' => ($products['free_job']>0?($products['job1']+$products['free_job']):$products['job1']),
                          'currency' => $products['currency'],
                          'job' => $products['job'],
                          'cv' => $products['cv'],
                          'sms' => $products['sms'],
                          'total_price' => $this->info['total'],
                          //'job1' => $products['job1'],
                          'cv1' => $products['cv1'],
                          'sms1' => $products['sms1'],
                          'featured_job' => $products['featured_job'],
						  'discount_amount' => $discount_amount,

																											);
 }
 function get_products() 
 {
  global $product_id,$obj_gift;
  //print_r($obj_gift);
  $free_job=0;
  $discount_percentage=0;
  $discount=0;
  if(is_object($obj_gift))
  {
   if(!$row=getAnyTableWhereData(GIFT_USED_TABLE,"user_id='".$_SESSION['sess_recruiterid']."' and gift_id='".tep_db_input($obj_gift->gift_id)."'","*",false))
   {
    switch($obj_gift->discount_type_id)
    {
     case "1":
      $free_job=1;
      break;
     case "2":
      $discount_percentage=$obj_gift->amount;
      break;
     case "3":
      $discount=$obj_gift->amount;
      break;
    }
   }
   else
   {
    switch($obj_gift->discount_type_id)
    {
     case "3":
      $discount=$obj_gift->amount-$row['gift_amount'];
      break;
    }
   }
  }
  $products_array = array();
  $row=getAnyTableWhereData(PLAN_TYPE_TABLE,'id="'.tep_db_input($product_id).'"','*');
  $unlimited_job=($row['job']=="2147483647"?true:false);
  $unlimited_cv=($row['cv']=="2147483647"?true:false);
  $unlimited_sms=($row['sms']=="2147483647"?true:false);
  $products_array = array('id' => $product_id,
  'plan_type_name' => $row[TEXT_LANGUAGE.'plan_type_name'],
  'free_job' => $free_job,
  'discount_percentage' => $discount_percentage,
  'discount' => $discount,
  'time1' => $row['time_period'],
  'time2' => $row['time_period1'],
  'time_period' => tep_db_output($row['time_period']).'&nbsp;'.($row['time_period'] >1?tep_db_output($row['time_period1'])."s":tep_db_output($row['time_period1'])),
  'fee' => tep_db_output($row['fee']),
  'job_posting_total_fee' => tep_db_output($row['fee']),
  'currency' => tep_db_output($row['currency']),
  'job' => ($unlimited_job?'Unlimited':tep_db_output($row['job'])),
  'cv' => ($unlimited_cv?'Unlimited':tep_db_output($row['cv'])),
  'sms' => ($unlimited_sms?'Unlimited':tep_db_output($row['sms'])),
  'job1' => $row['job'],
  'cv1' => $row['cv'],
  'sms1' => $row['sms'],
  'featured_job' => $row['featured_job'],
			);
  return $products_array;
 }
}
?>