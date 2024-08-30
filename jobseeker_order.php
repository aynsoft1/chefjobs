<?
/**********************************************************
**********# Name          : Naveen Kumar Swami  #**********
**********# Company       : Aynsoft Pvt. Ltd.   #**********
**********# Copyright (c) www.aynsoft.com 2005  #**********
**********************************************************/
class order 
{
 var $info, $totals, $products, $customer, $delivery, $content_type;
 function order($order_id = '') 
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
  $order_query = tep_db_query("select jobseeker_id, product_id, jobseeker_name, jobseeker_street_address, jobseeker_zip, jobseeker_city, jobseeker_state, jobseeker_country, jobseeker_telephone, jobseeker_email_address, billing_name, billing_street_address,  billing_zip, billing_city, billing_state, billing_country, billing_telephone, payment_method, cc_type, cc_owner, cc_number, cc_expires, currency, currency_value, date_purchased, orders_status, last_modified, comments from " . JOBSEEKER_ORDER_TABLE . " where orders_id = '" . (int)$order_id . "'");
  $order = tep_db_fetch_array($order_query);
  $totals_query = tep_db_query("select title, text from " . JOBSEEKER_ORDER_TOTAL_TABLE . " where orders_id = '" . (int)$order_id . "' order by sort_order");
  while ($totals = tep_db_fetch_array($totals_query)) 
  {
   $this->totals[] = array('title' => $totals['title'],
                           'text' => $totals['text']);
  }
  $order_total_query = tep_db_query("select text from " . JOBSEEKER_ORDER_TOTAL_TABLE . " where orders_id = '" . (int)$order_id . "' and class = 'ot_total'");
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

  $this->customer = array('id' => $order['jobseeker_id'],
                          'name' => $order['jobseeker_name'],
                          'street_address' => $order['jobseeker_street_address'],
                          'postcode' => $order['jobseeker_postcode'],
                          'city' => $order['jobseeker_city'],
                          'state' => $order['jobseeker_state'],
                          'country' => $order['jobseeker_country'],
                          'zip' => $order['jobseeker_zip'],
                          'telephone' => $order['jobseeker_telephone'],
                          'email_address' => $order['jobseeker_email_address']);

  $this->billing = array('name' => $order['billing_name'],
                         'street_address' => $order['billing_street_address'],
                         'zip' => $order['billing_zip'],
                         'city' => $order['billing_city'],
                         'state' => $order['billing_state'],
                         'country' => $order['billing_country'],
                         'telephone' => $order['billing_telephone']);

  $order_history_query = tep_db_query("select * from " . JOBSEEKER_ORDER_HISTORY_TABLE . " where order_id = '" .(int)$order_id."'");
  $order_history = tep_db_fetch_array($order_history_query);
  $this->products = array('plan_type_name' => $order_history['plan_type_name'],
                          'time_period' => $order_history['time_period'],
                          'time_period1' => $order_history['time_period1'],
                          'fee' => $order_history['fee'],
                          'currency' => $order_history['currency'],
                          'cv' => $order_history['cv']);
 }

 function cart() 
 {
  global $currency, $currencies, $shipping, $payment;
  global $discount_type_array;
  //print_r($_POST);die();
  $jobseeker_address_query = tep_db_query("select j.jobseeker_first_name, j.jobseeker_last_name, jl.jobseeker_email_address, j.jobseeker_address1, j.jobseeker_address2, j.jobseeker_country_id, j.jobseeker_state, j.jobseeker_state_id, j.jobseeker_city, j.jobseeker_zip, j.jobseeker_phone from " . JOBSEEKER_TABLE . " as j, " . JOBSEEKER_LOGIN_TABLE . " as jl where j.jobseeker_id=jl.jobseeker_id and jl.jobseeker_id='".$_SESSION['sess_jobseekerid']."'");
  $jobseeker_address = tep_db_fetch_array($jobseeker_address_query);
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
  $this->customer = array('firstname' => $jobseeker_address['jobseeker_first_name'],
                          'lastname' => $jobseeker_address['jobseeker_last_name'],
                          'email_address' => $jobseeker_address['jobseeker_email_address'],
                          'street_address' => $jobseeker_address['jobseeker_address1'].($jobseeker_address['jobseeker_address2']!=''?', ':'').$jobseeker_address['jobseeker_address2'],
                          'city' => $jobseeker_address['jobseeker_city'],
                          'state' => ($jobseeker_address['jobseeker_state_id']>0 ? get_name_from_table(ZONES_TABLE,'zone_name','zone_id',$jobseeker_address['jobseeker_state_id']) : $jobseeker_address['jobseeker_state']),
                          'country' => get_name_from_table(COUNTRIES_TABLE,'country_name','id',$jobseeker_address['jobseeker_country_id']),
                          'zip' => $jobseeker_address['jobseeker_zip'],
                          'fax' => $jobseeker_address['jobseeker_fax'],
                          'telephone' => $jobseeker_address['jobseeker_phone']);
  $this->billing = array('firstname' => $jobseeker_address['jobseeker_first_name'],
                          'lastname' => $jobseeker_address['jobseeker_last_name'],
                          'email_address' => $jobseeker_address['jobseeker_email_address'],
                          'street_address' => $jobseeker_address['jobseeker_address1'].$jobseeker_address['jobseeker_address2'],
                          'city' => $jobseeker_address['jobseeker_city'],
                          'state' => ($jobseeker_address['jobseeker_state_id']>0 ? get_name_from_table(ZONES_TABLE,'zone_name','zone_id',$jobseeker_address['jobseeker_state_id']) : $jobseeker_address['jobseeker_state']),
                          'country' => get_name_from_table(COUNTRIES_TABLE,'country_name','id',$jobseeker_address['jobseeker_country_id']),
                          'zip' => $jobseeker_address['jobseeker_zip'],
                          'telephone' => $jobseeker_address['jobseeker_phone']);
  $products = $this->get_products();
  $this->info['fee'] = $currencies->format($products['fee'], ($products['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($products['currency']==DEFAULT_CURRENCY?$currencies->get_value($products['currency']):''));
  $this->info['subtotal_symbol']=tep_db_output($currencies->format($products['job_posting_total_fee'], ($products['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($products['currency']==DEFAULT_CURRENCY?$currencies->get_value($products['currency']):'')));
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
   $discount_amount=($products['job_posting_total_fee']*$products['discount_percentage'])/100;
   //echo $discount_amount;
   $this->info['subtotal_symbol'].="<br>Discount ".$products['discount_percentage'].'%='.$currencies->format($discount_amount, ($products['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($products['currency']==DEFAULT_CURRENCY?$currencies->get_value($products['currency']):''));
   $this->info['subtotal'] = $currencies->format_without_symbol(($products['job_posting_total_fee']-$discount_amount), ($products['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($products['currency']==DEFAULT_CURRENCY?$currencies->get_value($products['currency']):''));
   $this->info['total_symbol'] = $currencies->format($this->info['subtotal'], ($products['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($products['currency']==DEFAULT_CURRENCY?$currencies->get_value($products['currency']):''));
   $this->info['total'] = $this->info['subtotal'];
  }
  else if($products['discount']>0)
  {
   $discount_amount=$products['discount'];
   //echo $discount_amount;
   $this->info['subtotal_symbol'].="<br>Discount=".$currencies->format(($products['job_posting_total_fee']>$discount_amount?$discount_amount:$products['job_posting_total_fee']), ($products['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($products['currency']==DEFAULT_CURRENCY?$currencies->get_value($products['currency']):''));
   $this->info['subtotal'] = $currencies->format_without_symbol(($products['job_posting_total_fee']>$discount_amount?$products['job_posting_total_fee']-$discount_amount:0), ($products['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($products['currency']==DEFAULT_CURRENCY?$currencies->get_value($products['currency']):''));
   $this->info['total_symbol'] = $currencies->format($this->info['subtotal'], ($products['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($products['currency']==DEFAULT_CURRENCY?$currencies->get_value($products['currency']):''));
   $this->info['total'] = $this->info['subtotal'];
  }
  else
  {
   $this->info['subtotal_symbol']=tep_db_output($currencies->format($products['job_posting_total_fee'], ($products['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($products['currency']==DEFAULT_CURRENCY?$currencies->get_value($products['currency']):'')));
   $this->info['subtotal'] = $currencies->format_without_symbol($products['job_posting_total_fee'], ($products['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($products['currency']==DEFAULT_CURRENCY?$currencies->get_value($products['currency']):''));
   $this->info['total_symbol'] = $currencies->format($products['job_posting_total_fee'], ($products['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($products['currency']==DEFAULT_CURRENCY?$currencies->get_value($products['currency']):''));
   $this->info['total'] = $this->info['subtotal'];
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
                          'sms1' => $products['sms1']);
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
   if(!$row=getAnyTableWhereData(JOBSEEKER_GIFT_USED_TABLE,"jobseeker_id='".$_SESSION['sess_jobseekerid']."' and gift_id='".tep_db_input($obj_gift->gift_id)."'","*",false))
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
  $row=getAnyTableWhereData(JOBSEEKER_PLAN_TYPE_TABLE,'id="'.tep_db_input($product_id).'"','*');
  $unlimited_cv=($row['cv']=="2147483647"?true:false);
  $products_array = array('id' => $product_id,
  'plan_type_name' => $row['plan_type_name'],
  'free_job' => $free_job,
  'discount_percentage' => $discount_percentage,
  'discount' => $discount,
  'time1' => $row['time_period'],
  'time2' => $row['time_period1'],
  'time_period' => tep_db_output($row['time_period']).'&nbsp;'.($row['time_period'] >1?tep_db_output($row['time_period1'])."s":tep_db_output($row['time_period1'])),
  'fee' => tep_db_output($row['fee']),
  'job_posting_total_fee' => tep_db_output($row['fee']),
  'currency' => tep_db_output($row['currency']),
  'cv' => ($unlimited_cv?'Unlimited':tep_db_output($row['cv'])),
  'cv1' => $row['cv']);
  return $products_array;
 }
}
?>
