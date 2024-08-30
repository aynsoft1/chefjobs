<?
/*
************************************************************
************************************************************
**********#	Name				      : Kamal Kumar Sahoo		 #***********
**********#	Company			    : Aynsoft	Pvt. Ltd.   #***********
**********#	Date Created	 : 03/02/2005   					  #***********
**********#	Date Modified	: 03/02/2005     	    #***********
**********#	Copyright (c) www.aynsoft.com 2004	 #***********
************************************************************
************************************************************
*/
include_once("include_files.php");
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_JOBSEEKER_CHECKOUT_CONFIRMATION);
$template->set_filenames(array('payment' => 'jobseeker_demo_payment.htm'));
include_once(FILENAME_BODY);
if(!check_login("jobseeker"))
{
 $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
 tep_redirect(FILENAME_JOBSEEKER_LOGIN);
}
$product_id=(int)$_GET['product_id'];
if(!$row=getAnyTableWhereData(JOBSEEKER_PLAN_TYPE_TABLE,'id="'.tep_db_input($product_id).'"','*'))
{
 $messageStack->add_session(SORRY_PRODUCT_NOT_EXIST, 'error');
 tep_redirect(FILENAME_JOBSEEKER_CONTROL_PANEL);
}
$product_name=tep_db_output($row['plan_type_name']);
$product_fee=tep_db_output($currencies->format($row['fee'], ($row['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($row['currency']==DEFAULT_CURRENCY?$currencies->get_value($row['currency']):'')));
$payment=$_SESSION['payment']='cc';
// load all enabled payment modules
include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'payment.php');

$payment_modules = new payment($payment);
include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'jobseeker_order.php');
$order = new order;
//print_r($order);
$payment_modules->update_status();

include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'order_total.php');
$order_total_modules = new order_total;
$form_action_url = tep_href_link(FILENAME_JOBSEEKER_CHECKOUT_PROCESS, 'product_id='.$product_id, 'SSL');
$form='<form name="checkout_confirmation" action="'.$form_action_url.'" method="post">';
$hidden_fields=tep_draw_hidden_field('cc_owner', $order->billing['firstname'].' '.$order->billing['lastname']).
               tep_draw_hidden_field('cc_expires', date("my",mktime(0,0,0,date("m")+3,date("d"),date("Y")))) .
               tep_draw_hidden_field('cc_type', 'Visa') .
               tep_draw_hidden_field('cc_number', '4111111111111111');
///*
//*/
$template->assign_vars(array(
 'HEADING_TITLE'=>HEADING_TITLE,
 'form'=>$form,
 'button'=>tep_image_submit(PATH_TO_BUTTON.'button_confirm_order.gif', IMAGE_BUTTON_CONFIRM_ORDER),
 'hidden_fields'=>$hidden_fields,
 'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
 'RIGHT_BOX_WIDTH'=>RIGHT_BOX_WIDTH1,
 'LEFT_HTML'=>LEFT_HTML,
 'RIGHT_HTML'=>RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('payment');
?>