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
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_CHECKOUT_PAYMENT);
$template->set_filenames(array('payment' => 'checkout_payment.htm'));
include_once(FILENAME_BODY);
//print_r($_SESSION);
if(!check_login("recruiter"))
{
 $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
 tep_redirect(getPermalink(FILENAME_RECRUITER_LOGIN));
}
unset($_SESSION['product_id']);
$product_id=(int)$_GET['product_id'];
if(!$row=getAnyTableWhereData(PLAN_TYPE_TABLE,'id="'.tep_db_input($product_id).'"','*'))
{
 $messageStack->add_session(SORRY_PRODUCT_NOT_EXIST, 'error');
 tep_redirect(FILENAME_RECRUITER_CONTROL_PANEL);
}
if($row['fee']==0.0)
{
 tep_redirect(tep_href_link(FILENAME_DEMO_PAYMENT,'product_id='.$product_id));
}
$product_name=tep_db_output($row[TEXT_LANGUAGE.'plan_type_name']);
$checked_price=$currencies->format_without_symbol($row['fee'], ($row['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($row['currency']==DEFAULT_CURRENCY?$currencies->get_value($row['currency']):''));
$hidden_fields='';
unset($_SESSION['gift_code']);
$gift_code=tep_db_prepare_input($_POST['gift_code']);
$gift_error='';
if($gift_code!='')
{
 include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'gift.php');
 $gift = new gift();
 $obj_gift=$gift->check_status($gift_code);
 if(!is_object($obj_gift))
 {
  $gift_error='<tr>
                <td class="infoBoxNoticeContents" height="20">&nbsp;&nbsp;'.INFO_TEXT_PROMOTION_CODE.'</td>
               </tr>';
 }
 else
 {
  if(!$row_gift_used=getAnyTableWhereData(GIFT_USED_TABLE." as gu, ".GIFT_TABLE." as g","gu.user_id='".$_SESSION['sess_recruiterid']."' and g.certificate_number='".tep_db_input($gift_code)."' and gu.gift_id=g.gift_id and g.user='recruiter'","gu.gift_id"))
  {
   $hidden_fields.=tep_draw_hidden_field('gift_code',$gift_code);
  }
  else
  {
   if($row_gift_used['gift_amount']>$obj_gift->amount)
   {
    $gift_error='<tr>
                  <td class="infoBoxNoticeContents" height="20">&nbsp;&nbsp;'.INFO_TEXT_SORRY_ALREADY_CODE.'</td>
                 </tr>';
   }
   else
   {
    $_SESSION['gift_code']=$gift_code;
   }
  }
 }
}
$product_name=tep_db_output($row[TEXT_LANGUAGE.'plan_type_name']);
////*** curency display coding ***********/
$row_cur=getAnyTableWhereData(CURRENCY_TABLE,"code ='".DEFAULT_CURRENCY."'",'symbol_left,symbol_right');
$sym_left=(tep_not_null($row_cur['symbol_left'])?$row_cur['symbol_left'].'':'');
$sym_rt=(tep_not_null($row_cur['symbol_right'])?' '.$row_cur['symbol_right']:'');
//////**********currency display ***************************/

$amount_due=sprintf(INFO_TEXT_AMOUNT_DUE,'0.00');
$_SESSION['sess_gift']=false;
if($row_gift=getAnyTableWhereData(GIFT_TABLE,'user="recruiter" and  certificate_number="'.tep_db_input(tep_db_prepare_input($_POST['gift_code'])).'"',"*"))
{
 switch($row_gift['discount_type_id'])
 {
  case 3:
   if($row_gift['amount']<$checked_price)
   {
    $_SESSION['gift_code']=$_POST['gift_code'];
    if($row_gift_used_1=getAnyTableWhereData(GIFT_USED_TABLE,"user_id='".$_SESSION['sess_recruiterid']."' and gift_id='".tep_db_input($row_gift['gift_id'])."'","*",false))
    {
     $messageStack->add(sprintf(ERROR_EXTRA_PAYMENT,($checked_price-$row_gift['amount']+$row_gift_used_1['gift_amount'])), 'success');
     $amount_due=sprintf(INFO_TEXT_AMOUNT_DUE,($checked_price-$row_gift['amount']+$row_gift_used_1['gift_amount']));
    }
    else
    {
     $messageStack->add(sprintf(ERROR_EXTRA_PAYMENT,($checked_price-$row_gift['amount'])), 'success');
     $amount_due=sprintf(INFO_TEXT_AMOUNT_DUE,($checked_price-$row_gift['amount']));
    }
    //tep_redirect(FILENAME_CHECKOUT_PAYMENT.'?product_id='.$product_id);
   }
   $_SESSION['sess_gift']=true;
   break;
 }
}
if(!$_SESSION['sess_gift'])
{
 $messageStack->add(sprintf(ERROR_EXTRA_PAYMENT1,$checked_price), 'success');
 $amount_due=sprintf(INFO_TEXT_AMOUNT_DUE,$currencies->get_symbol_left(DEFAULT_CURRENCY)." ".$checked_price);
}


include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'order.php');
$order = new order;
$product_fee=$order->info['fee'];
$amount_due=$order->info['total'];;
////
include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'order_total.php');
$order_total_modules = new order_total;
if(MODULE_ORDER_TOTAL_INSTALLED)
{
 $order_total_modules->process();
 $order_total_string=$order_total_modules->output();
}
////////
////////////////////////////////// load all enabled payment modules//////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'payment.php');
$payment_modules = new payment;
$javascript_validation=$payment_modules->javascript_validation();
if(isset($_GET['payment_error']) && is_object(${$_GET['payment_error']}) && ($error = ${$_GET['payment_error']}->get_error()))
{
 $payment_error_title=stripslashes($error['title']);
 $payment_error_value=stripslashes($error['error']);
 $update_message='
            <table border="0" width="100%" cellspacing="1" cellpadding="3" class="infoBoxNotice">
             <tr class="infoBoxNoticeContents">
              <td valign="top" class="label">'.$payment_error_title.'</td>
             </tr>
             <tr class="infoBoxNoticeContents">
              <td valign="top" class="small">'.$payment_error_value.'</td>
             </tr>
            </table>';
}
$billing_address="<h5 class='style16'>".$order->customer['company']."</h5>".
                 "<span class='style22'>".$order->customer['firstname'].' '.$order->customer['lastname']."</span><br>".
                 "<span class='style22'>".$order->customer['street_address']."<br>".
                 (tep_not_null($order->customer['city'])?$order->customer['city'].",&nbsp;":'').
                 $order->customer['state'].",&nbsp;".
                 $order->customer['country'].",&nbsp;".
                 $order->customer['zip']."<br>".INFO_TEXT_PHONE." #: ".
                 $order->customer['telephone']."</span>";

$selection = $payment_modules->selection();
$radio_buttons = 0;
if(isset($_SESSION['payment']))
	$payment=$_SESSION['payment'];
if(sizeof($selection)<=0)
{
 $messageStack->add_session(SORRY_PAYMENT_METHOD, 'error');
 tep_redirect(FILENAME_RECRUITER_CONTROL_PANEL);
}
$n=sizeof($selection);
if($order->products['fee']==0.0)
{
 for ($i=0,$n=sizeof($selection); $i<$n; $i++)
 {
  if($selection[$i]['id']=='cc')
  {
   $n=$i+1;
   break;
  }
 }
}
else
{
 $i=0;
 $n=sizeof($selection);
}
for ($i=0; $i<$n; $i++)
{
 if ( ($selection[$i]['id'] == $payment) || ($n == 1) )
 {
  $row_selected=' id="defaultSelected" class="moduleRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="selectRowEffect(this, ' . $radio_buttons . ')"';
 }
 else
 {
  $row_selected=' class="moduleRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="selectRowEffect(this, ' . $radio_buttons . ')"';
 }
 if(sizeof($selection) > 1)
 {
		if($i==0)
  $radio_1=tep_draw_radio_field('payment', $selection[$i]['id'],true,$payment);
		else
  $radio_1=tep_draw_radio_field('payment', $selection[$i]['id'],false,$payment);
 }
 else
 {
  $radio_1=tep_draw_hidden_field('payment', $selection[$i]['id']);
 }
 $error_1='';
 $payment_field_name_value='';
 if (isset($selection[$i]['error']))
 {
  $error_1=$selection[$i]['error'];
 }
 elseif (isset($selection[$i]['fields']) && is_array($selection[$i]['fields']))
 {
  for ($j=0, $n2=sizeof($selection[$i]['fields']); $j<$n2; $j++)
  {
   $payment_field_name_value.='
                <tr>
                 <td valign="top" class="dataTableContent">'.$selection[$i]['fields'][$j]['title'].'</td>
                 <td valign="top" class="dataTableContent">'.$selection[$i]['fields'][$j]['field'].'</td>
                </tr>'."\n";
  }
 }
 $template->assign_block_vars('payment', array( 'row_selected' => $row_selected,
  //'module_name' => $selection[$i]['module'],
  'module_name' =>'<u>'.$selection[$i]['module'].'</u> '.(($selection[$i]['module']=='PayPal')?'  &nbsp;&nbsp;<img src="img/paypal.gif" alt="PayPal" > <img src="img/visa.gif" alt="Visa"> <img src="img/mc.gif" alt="Master Card"> <img src="img/discover.gif" alt="Discover"> <img src="img/amex.gif" alt="American Express">':'').(($selection[$i]['module']=='Authorize.net')?'  &nbsp;&nbsp;<img src="img/authrize.jpg" alt="Credit Card">':''),
  'radio_1' => $radio_1,
  'error_1' => $error_1,
  'payment_field_name_value' => $payment_field_name_value,
  ));
 $radio_buttons++;
}
$template->assign_vars(array(
 'HEADING_TITLE'=>HEADING_TITLE,
 'javascript_validation'=>$javascript_validation,
 'TABLE_HEADING_BILLING_ADDRESS'=>TABLE_HEADING_BILLING_ADDRESS,
 'TABLE_HEADING_PLAN_TYPE_TIME_PERIOD'=>TABLE_HEADING_PLAN_TYPE_TIME_PERIOD,
 'TABLE_HEADING_PAYMENT_METHOD'=>TABLE_HEADING_PAYMENT_METHOD,
 'TABLE_HEADING_PAYMENT_METHOD1'=>TEXT_SELECT_PAYMENT_METHOD,
 'TITLE_BILLING_ADDRESS'=>TITLE_BILLING_ADDRESS,
 'TITLE_BILLING_ADDRESS1'=>$billing_address,
 'order_total_string'=>$order_total_string,
 'product_name'=>$product_name,
 'product_fee'=>$product_fee,
 'INFO_TEXT_BILLING_INFORMATION'=>INFO_TEXT_BILLING_INFORMATION,
 'INFO_TEXT_PRODUCT' =>INFO_TEXT_PRODUCT,
 'form'=>tep_draw_form('checkout_payment', FILENAME_CHECKOUT_CONFIRMATION, 'product_id='.$product_id, 'post', 'onsubmit="return check_form();"'),
 'TABLE_HEADING_COMMENTS'=>TABLE_HEADING_COMMENTS,
 'TABLE_HEADING_COMMENTS1'=>tep_draw_textarea_field('comments', 'soft', '40', '5', '', 'class="form-control"'),
 'TITLE_CONTINUE_CHECKOUT_PROCEDURE'=>TITLE_CONTINUE_CHECKOUT_PROCEDURE.' '.TEXT_CONTINUE_CHECKOUT_PROCEDURE,
 'gift_error'=>$gift_error,
 'hidden_fields'=>$hidden_fields,
 'INFO_TEXT_AMOUNT_DUE'=>"<span>Final Amount : </span>".$sym_left.$amount_due.$sym_rt,
 'button'=>tep_draw_submit_button_field('','Confirm & Pay Now','class="btn btn-primary"'),
 'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
 'RIGHT_BOX_WIDTH'=>RIGHT_BOX_WIDTH1,
 'LEFT_HTML'=>LEFT_HTML,
 'RIGHT_HTML'=>RIGHT_HTML,
 'update_message'=>$update_message));
$template->pparse('payment');
?>