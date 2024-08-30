<?
include_once("include_files.php");
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_JOBSEEKER_CHECKOUT_CONFIRMATION);
$template->set_filenames(array('payment' => 'jobseeker_checkout_confirmation.htm','payment1' => 'jobseeker_checkout_confirmation1.htm'));
include_once(FILENAME_BODY);
$hidden_fields='';
if(!check_login("jobseeker"))
{
 $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
 tep_redirect(FILENAME_JOBSEEKER_LOGIN);
}
if(isset($_SESSION['product_id']))
{
 unset($_SESSION['product_id']);
}
$product_id=(int)$_GET['product_id'];
if(!$row=getAnyTableWhereData(JOBSEEKER_PLAN_TYPE_TABLE,'id="'.tep_db_input($product_id).'"','*'))
{
 $messageStack->add_session(SORRY_PRODUCT_NOT_EXIST, 'error');
 tep_redirect(FILENAME_JOBSEEKER_CONTROL_PANEL);
}
/*if($row['plan_type_name']=='Demo')
{
 tep_redirect(tep_href_link(FILENAME_DEMO_PAYMENT,'product_id='.$product_id));
}*/
$checked_price=$currencies->format_without_symbol($row['fee'], ($row['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($row['currency']==DEFAULT_CURRENCY?$currencies->get_value($row['currency']):''));
$product_name=tep_db_output($row['plan_type_name']);
//echo $checked_price;
//print_r($row);
//print_r($_POST);
$_SESSION['sess_gift']=false;
if(isset($_POST['payment'])) 
{
 $_SESSION['payment'] = $_POST['payment'];
 $payment=$_SESSION['payment'];
}
else if(isset($_POST['gift_code']))
{
 $gift_code=tep_db_prepare_input($_POST['gift_code']);
 if($row_gift=getAnyTableWhereData(GIFT_TABLE,' user ="jobseeker" and certificate_number="'.tep_db_input(tep_db_prepare_input($gift_code)).'"',"*"))
 {
  switch($row_gift['discount_type_id'])
  {
   case 3:
    if($row_gift['amount']<$checked_price)
    {
     $_SESSION['gift_code']=$gift_code;
     if($row_gift_used_1=getAnyTableWhereData(GIFT_USED_TABLE,"user_id='".$_SESSION['sess_jobseekerid']."' and gift_id='".tep_db_input($row_gift['gift_id'])."'","*",false))
      $messageStack->add_session(sprintf(ERROR_EXTRA_PAYMENT,($checked_price-$row_gift['amount']+$row_gift_used_1['gift_amount'])), 'error');
     else
      $messageStack->add_session(sprintf(ERROR_EXTRA_PAYMENT,($checked_price-$row_gift['amount'])), 'error');
     tep_redirect(FILENAME_JOBSEEKER_CHECKOUT_PAYMENT.'?product_id='.$product_id);
    }
    $_SESSION['sess_gift']=true;
    break;
  }
 }
}
else
{
 $messageStack->add_session(ERROR_PAYMENT_METHOD, 'error');
 tep_redirect(FILENAME_JOBSEEKER_CHECKOUT_PAYMENT.'?product_id='.$product_id);
}
/////////////////////////////////////////////////////////////
$product_name=tep_db_output($row['plan_type_name']);
//$product_fee=tep_db_output($currencies->format($row['fee'], ($row['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($row['currency']==DEFAULT_CURRENCY?$currencies->get_value($row['currency']):'')));
if(isset($_POST['comments'])) 
 $_SESSION['comments'] = tep_db_prepare_input($_POST['comments']);
else
 $_SESSION['comments']='N/A';

// load all enabled payment modules
include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'payment.php');
$payment_modules = new payment($payment);

unset($_SESSION['gift_code']);
$gift_code=tep_db_prepare_input($_POST['gift_code']);
$gift_error='';
if($gift_code!='')
{
 include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'gift.php');
 $gift = new gift();
 $obj_gift=$gift->check_status($gift_code,'jobseeker');
 if(!is_object($obj_gift))
 {
  $gift_error='<div class="alert alert-danger" >&nbsp;&nbsp;'.INFO_TEXT_SORRY_PROMOTION_CODE.'</div>';
 }
 else
 {
  if(!$row_gift_used=getAnyTableWhereData(GIFT_USED_TABLE." as gu, ".GIFT_TABLE." as g","gu.user_id='".$_SESSION['sess_jobseekerid']."' and g.certificate_number='".tep_db_input($gift_code)."' and gu.gift_id=g.gift_id and g.user='jobseeker'","gu.gift_id"))
  {
   $_SESSION['gift_code']=$gift_code;
  }
  else
  {
   if($row_gift_used['gift_amount']>$obj_gift->amount)
   {
    $gift_error='<div class="alert alert-danger" >&nbsp;&nbsp;'.INFO_TEXT_SORRY_ALREADY_CODE.'</div>';
   }
   else
   {
    $_SESSION['gift_code']=$gift_code;
   }
  }
 }
}
include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'jobseeker_order.php');
$order = new order;
//print_r($order);
if($order->info['total']<=0)
{
 unset($_SESSION['payment']);
 unset($payment);
}
$product_fee=$order->info['fee'];

$payment_modules->update_status();

if ( ( is_array($payment_modules->modules) && (sizeof($payment_modules->modules) > 1) && !is_object($$payment) ) || (is_object($$payment) && ($$payment->enabled == false)) ) 
{
 tep_redirect(tep_href_link(FILENAME_JOBSEEKER_CHECKOUT_PAYMENT, 'product_id='.$product_id.'&error_message=' . urlencode(ERROR_NO_PAYMENT_MODULE_SELECTED), 'SSL'));
}
if (is_array($payment_modules->modules)) 
{
 $payment_modules->pre_confirmation_check();
}
include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'order_total.php');
$order_total_modules = new order_total;

$billing_address=$order->billing['company']."<br>".
                 $order->billing['firstname'].' '.$order->billing['lastname']."<br>".
                 $order->billing['street_address']."<br>".
                 (tep_not_null($order->billing['city'])?$order->billing['city']."<br>":'').
                 $order->billing['state']."<br>".
                 $order->billing['country']."<br>".
                 $order->billing['zip']."<br>Phone #: ".
                 $order->billing['telephone'];
if(MODULE_ORDER_TOTAL_INSTALLED) 
{
 $order_total_modules->process();
	//print_r($order_total_modules->process());
 $order_total_string=$order_total_modules->output();
}
$payment_method=$GLOBALS[$payment]->title;

$payment_fields='';
If (is_array($payment_modules->modules)) 
{
 If ($confirmation = $payment_modules->confirmation()) 
 { 
  $payment_name=$confirmation['title'];
  if(isset($confirmation['fields']))
  for ($i=0, $n=sizeof($confirmation['fields']); $i<$n; $i++) 
  {
   $payment_fields.='
          <tr>
            <td class="small">'.$confirmation['fields'][$i]['title'].'</td>
            <td class="small">'.$confirmation['fields'][$i]['field'].'</td>
          </tr>';
  }
 }
}
//$payment_fields=($payment_fields==''?'N/R':$payment_fields);
$payment_fields=($payment_fields==''?'':$payment_fields);
if (isset($$payment->form_action_url)) 
{
 $_SESSION['product_id']=$product_id;
 $form_action_url = $$payment->form_action_url;
} 
else 
{
 $form_action_url = tep_href_link(FILENAME_JOBSEEKER_CHECKOUT_PROCESS, 'product_id='.$product_id, 'SSL');
}
$form='<form class="d-flex" name="checkout_confirmation" action="'.$form_action_url.'" method="post">'.($_SESSION['sess_gift']?$hidden_fields:'');
$form1='<form class="d-flex ms-auto" name="checkout_confirmation1" target="myNewWin" action="'.tep_href_link(FILENAME_JOBSEEKER_CHECKOUT_CONFIRMATION, 'product_id='.$product_id."&action1=print", 'SSL').'" method="post">'.($_SESSION['sess_gift']?$hidden_fields:'').tep_draw_hidden_field('payment',$payment).tep_draw_hidden_field('comments',$_POST['comments']);
if (is_array($payment_modules->modules)) 
{
 $process_buttons=$payment_modules->process_button();
}

$template->assign_vars(array(
 'HEADING_TITLE'=>HEADING_TITLE,
 'HEADING_TITLE1'=>HEADING_TITLE1,
 'HEADING_PRODUCTS'=>HEADING_PRODUCTS,
 'INFO_TEXT_LOGO'=>'<img src="'.PATH_TO_IMG.DEFAULT_SITE_LOGO.'" >',
 'HEADING_TITLE_LOGO'=>SITE_TITLE,
 'product_name'=>$product_name,
 'product_fee'=>$product_fee,
 'HEADING_BILLING_INFORMATION'=>HEADING_BILLING_INFORMATION,
 'HEADING_BILLING_ADDRESS'=>HEADING_BILLING_ADDRESS,
 'HEADING_BILLING_ADDRESS1'=>$billing_address,
 'form'=>$form,
 'form1'=>$form1,
 'button'=>'<a href="' . tep_href_link(FILENAME_JOBSEEKER_CHECKOUT_PAYMENT, 'product_id='.$product_id, 'SSL') . '" class="btn btn-outline-secondary">'
 .IMAGE_BUTTON_BACK.'</a>'.'&nbsp;&nbsp;'
//  .tep_image_submit(PATH_TO_BUTTON.'button_confirm_order.gif', IMAGE_BUTTON_CONFIRM_ORDER),
 .tep_button_submit('btn btn-primary', IMAGE_BUTTON_CONFIRM_ORDER),
 
//  'button_print'=>tep_image_button(PATH_TO_BUTTON.'button_print.gif',IMAGE_BUTTON_PRINT,'onClick="print_page();"'),
 'button_print'=>tep_button_submit('btn btn-outline-secondary','Print','onClick="print_page();"'),
 'order_total_string'=>$order_total_string,
 'HEADING_PAYMENT_METHOD'=>HEADING_PAYMENT_METHOD,
// 'HEADING_PAYMENT_METHOD1'=>$payment_method,
 'HEADING_PAYMENT_METHOD1'=>(($payment_method=='PayPal')?$payment_method.' '.INFO_TEXT_ACCEPT.' : <img src="img/visa.gif" alt="Visa"> <img src="img/mc.gif" alt="Master Card"> <img src="img/discover.gif" alt="Discover"> <img src="img/amex.gif" alt="American Express"> <img src="img/paypal.gif" alt="PayPal">':$payment_method),
 'HEADING_PAYMENT_INFORMATION'=>HEADING_PAYMENT_INFORMATION,
 'HEADING_PAYMENT_INFORMATION1'=>$payment_name,
 'HEADING_PAYMENT_INFORMATION2'=>$payment_fields,
 'HEADING_ORDER_COMMENTS'=>HEADING_ORDER_COMMENTS,
 'HEADING_ORDER_COMMENTS1'=>nl2br(tep_output_string_protected($order->info['comments'])) . tep_draw_hidden_field('comments', $order->info['comments']),
 'process_buttons'=>$process_buttons,
 'hidden_fields'=>$hidden_fields,
 'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
 'RIGHT_BOX_WIDTH'=>RIGHT_BOX_WIDTH1,
 'LEFT_HTML'=>LEFT_HTML,
 'RIGHT_HTML'=>RIGHT_HTML,
  'gift_error'=>$gift_error,

 'update_message'=>$messageStack->output()));
if($_GET['action1']=='print')
 $template->pparse('payment1');
else 
 $template->pparse('payment');
?>