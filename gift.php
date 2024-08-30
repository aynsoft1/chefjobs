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
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_EMPLOYER_GIFT);
$template->set_filenames(array('gift' => 'gift.htm'));
include_once(FILENAME_BODY);
$product_id=(int)$_GET['product_id'];
include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'gift.php');
$gift = new gift();
$obj=$gift->check_status('','recruiter');
 if(count($gift->gift_text_array)>0 && $obj)
{
 $gift_string=$gift->gift_text_array['text'].'<br>'.$gift->gift_text_array['field'];
}
else
{
 tep_redirect(FILENAME_CHECKOUT_PAYMENT.'?product_id='.$product_id);
}
/*
if(isset($_POST['no_of_jobs']) && tep_not_null($_POST['no_of_jobs']))
{
 $_SESSION['sess_no_of_jobs']=$_POST['no_of_jobs'][$product_id];
}
else if(!isset($_SESSION['sess_no_of_jobs']))
{
 $messageStack->add_session(SORRY_PRODUCT_NOT_EXIST, 'error');
 tep_redirect(FILENAME_EMPLOYER_RATES);
}*/
$row=getAnyTableWhereData(PLAN_TYPE_TABLE,'id="'.tep_db_input($product_id).'"','*');
$amount=tep_db_output(number_format($row['fee'],2,'.',''));
$amount=$currencies->format($amount, ($row['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($row['currency']==DEFAULT_CURRENCY?$currencies->get_value($row['currency']):''));
$button='<a href="'.tep_href_link(FILENAME_RECRUITER_RATES).'" class="btn btn-outline-secondary mx-2">'.IMAGE_BUTTON_CHANGE_ORDER.'</a>';
// $button.=tep_image_submit(PATH_TO_BUTTON.'button_next.gif', IMAGE_BUTTON_NEXT);
$button.=tep_button_submit('btn btn-primary', 'Next');
$template->assign_vars(array(
 'HEADING_TITLE'=>HEADING_TITLE,
 'gift_string'=>$gift_string,
 'amount_due'=>sprintf(INFO_TEXT_AMOUNT,$amount),
 'form'=>tep_draw_form('gift', FILENAME_CHECKOUT_PAYMENT, 'product_id='.$product_id, 'post'),
 'button'=>$button,
 'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
 'RIGHT_BOX_WIDTH'=>RIGHT_BOX_WIDTH1,
 'LEFT_HTML'=>LEFT_HTML,
 'RIGHT_HTML'=>RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('gift');
?>