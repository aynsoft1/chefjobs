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
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_COUPONS);
$template->set_filenames(array('coupons' => 'coupon_management.htm'));
include_once(FILENAME_ADMIN_BODY);

$action = (isset($_GET['action']) ? $_GET['action'] : '');
unset($cInfo); //required

$now=date('Y-m-d H:i:s');
$gift_id = tep_db_prepare_input($_GET['gift_id']);

if ($action!="")
{
 switch ($action)
	{
  case 'confirm_delete':
   tep_db_query("delete from " . GIFT_TABLE . " where gift_id = '" . (int)$gift_id . "'");
   $messageStack->add_session(MESSAGE_SUCCESS_DELETED, 'success');
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_COUPONS, 'page=' . $_GET['page']));
  break;
  case 'insert':
  case 'save':
	$gift_reason=tep_db_prepare_input($_POST['TR_gift_reason']);
    $discount_type_id=tep_db_prepare_input($_POST['TR_discount_type_id']);
	$amount = tep_db_prepare_input($_POST['IN_amount']);
   $start_date = tep_db_prepare_input($_POST['start_date']);
   $start_month = tep_db_prepare_input($_POST['start_month']);
   $start_year = tep_db_prepare_input($_POST['start_year']);
   $end_date  = tep_db_prepare_input($_POST['end_date']);
   $end_month = tep_db_prepare_input($_POST['end_month']);
   $end_year = tep_db_prepare_input($_POST['end_year']);

   $coupon_start_date = $start_year."-".$start_month."-".$start_date;
   $coupon_end_date = $end_year."-".$end_month."-".$end_date;

    if(!@checkdate($start_month,$start_date,$start_year))
    {
     $error = true;
     $messageStack->add(MESSAGE_FROM_DATE_ERROR,'error');
    }
    if(!@checkdate($end_month,$end_date,$end_year))
    {
     $error = true;
     $messageStack->add(MESSAGE_TO_DATE_ERROR,'error');
    }
    if($coupon_start_date > $coupon_end_date)
    {
     $error = true;
     $messageStack->add(MESSAGE_DATE_ERROR,'error');
    }

	$sql_data_array['gift_reason'] = $gift_reason;
	$sql_data_array['amount'] = $amount;
	$sql_data_array['start_date'] = $coupon_start_date;
	$sql_data_array['expired'] = $coupon_end_date;
	$sql_data_array['discount_type_id'] = $discount_type_id;
	$sql_data_array['gift_status'] = 'Yes';
	$sql_data_array['inserted'] = $now;


			if($action=='insert')
			{
					$sql_data_array['user'] = tep_db_prepare_input($_POST['user_type']);

				/*if($row_chek=getAnyTableWhereData(GIFT_TABLE,"certificate_number='".tep_db_input($certificate_number)."'",'gift_id'))
				{
					$messageStack->add(MESSAGE_NAME_ERROR, 'error');
				}
				else
				{
					 tep_db_perform(GIFT_TABLE, $sql_data_array);
					 $row_id_check=getAnyTableWhereData(GIFT_TABLE,"1 order by gift_id desc limit 0,1","gift_id");
					 $gift_id = $row_id_check['gift_id'];
					$messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');
					tep_redirect(FILENAME_ADMIN1_COUPONS);
				}
*/
     tep_db_perform(GIFT_TABLE, $sql_data_array);
     $row_id_check=getAnyTableWhereData(GIFT_TABLE,"1 order by gift_id desc limit 0,1","gift_id");
     $gift_id = $row_id_check['gift_id'];
/***************************  generate discount code  begin ************************/
    if($discount_type_id=='3')
		$discount_code= "DK3241".rand(100,300).$gift_id;
	else
		$discount_code= "PER203".rand(100,300).$gift_id;

/***************************  generate discount code  end ************************/
    tep_db_query('update '.GIFT_TABLE ." set certificate_number='".$discount_code."' where gift_id = '".(int)$gift_id . "'");

  			$messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');
					tep_redirect(FILENAME_ADMIN1_COUPONS);

			}
			else
			{
    $gift_id=(int)$_GET['gift_id'];
				if($row_chek=getAnyTableWhereData(GIFT_TABLE,"certificate_number='".tep_db_input($certificate_number)."' and gift_id!='$gift_id'",'gift_id'))
				{
					$messageStack->add(MESSAGE_NAME_ERROR, 'error');
				}
				else
				{
     tep_db_perform(GIFT_TABLE, $sql_data_array, 'update', "gift_id = '" . (int)$gift_id . "'");
  			$messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
					tep_redirect(FILENAME_ADMIN1_COUPONS.'?page='.$_GET['page'].'&gift_id='.$gift_id);
				}
			}
  break;
  case 'gift_active':
  case 'gift_inactive':
tep_db_query("update ".GIFT_TABLE." set gift_status='".($action=='gift_active'?'Yes':'No')."' where gift_id='".$gift_id."'");
   $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_COUPONS,tep_get_all_get_params(array('action','selected_box'))));
   break;

 }
}
///////////// Middle Values ////
///only for sorting starts
$sort_array=array("g.start_date","g.expired","g.gift_status");
include_once(PATH_TO_MAIN_PHYSICAL_CLASS.'sort_by_clause.php');
$obj_sort_by_clause=new sort_by_clause($sort_array);
$order_by_clause=$obj_sort_by_clause->return_value;


//$coupon_query_raw="select g.gift_id, g.certificate_number, g.gift_reason, g.gift_status, g.start_date, g.expired, g.amount  from " . GIFT_TABLE . " as g left outer join " . GIFT_USED_TABLE . " as gu on (g.gift_id=gu.gift_id)";
$coupon_query_raw="select g.gift_id, g.certificate_number,g.discount_type_id, g.gift_reason, g.gift_status, g.start_date, g.expired, g.amount,g.user  from " . GIFT_TABLE . " as g  ";
$coupon_split = new splitPageResults($_GET['page'], '20', $coupon_query_raw, $coupon_query_numrows);
$coupon_query = tep_db_query($coupon_query_raw);
if(tep_db_num_rows($coupon_query) > 0)
{
 $alternate=1;
 $user_type ='';
 while ($coupon = tep_db_fetch_array($coupon_query))
 {
  if ((!isset($_GET['gift_id']) || (isset($_GET['gift_id']) && ($_GET['gift_id'] == $coupon['gift_id']))) && !isset($cInfo) && (substr($action, 0, 3) != 'new'))
  {
   $cInfo = new objectInfo($coupon);
  }
  if ( (isset($cInfo) && is_object($cInfo)) && ($coupon['gift_id'] == $cInfo->gift_id) )
  {
   $row_selected=' id="defaultSelected" class="table-secondary dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . FILENAME_ADMIN1_COUPONS . '?page='.$_GET['page'].'&gift_id=' . $cInfo->gift_id . '&action=edit\'"';
  }
  else
  {
   $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . FILENAME_ADMIN1_COUPONS . '?page='.$_GET['page'].'&gift_id=' . $coupon['gift_id'] . '\'"';
  }
  $alternate++;
  if ( (isset($cInfo) && is_object($cInfo)) && ($coupon['gift_id'] == $cInfo->gift_id) )
  {
   $action_image=tep_image(PATH_TO_IMAGE.'icon_arrow_right.gif',IMAGE_EDIT);
  }
  else
  {
   $action_image='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_COUPONS, 'page='.$_GET['page'].'&gift_id=' . $coupon['gift_id']) . '">'.tep_image(PATH_TO_IMAGE.'icon_info.gif',IMAGE_INFO).'</a>';
  }

   if ($coupon['gift_status'] == 'Yes')
   {
    $status='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_COUPONS, tep_get_all_get_params(array('gift_id','action','selected_box'))).'&gift_id=' . $coupon['gift_id'] . '&action=gift_inactive' . '">' . tep_image(PATH_TO_IMAGE.'icon_status_red_light.gif', STATUS_COUPON_INACTIVATE, 30, 20) . '</a>&nbsp;' . tep_image(PATH_TO_IMAGE.'icon_status_green.gif', STATUS_COUPON_ACTIVE, 30, 20);
   }
   else
   {
    $status=tep_image(PATH_TO_IMAGE.'icon_status_red.gif', STATUS_COUPON_INACTIVE, 30, 20) . '&nbsp;<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_COUPONS, tep_get_all_get_params(array('gift_id','action','selected_box'))).'&gift_id=' . $coupon['gift_id'] . '&action=gift_active' . '">' . tep_image(PATH_TO_IMAGE.'icon_status_green_light.gif', STATUS_COUPON_ACTIVATE, 30, 20) . '</a>';
   }
///////////////***count no of coupons used by recruiter   ***////////
$now=date('Y-m-d H:i:s');
 $ngift_query = tep_db_query("select  distinct user_id from " . GIFT_USED_TABLE . " where gift_id = '" . $coupon['gift_id'] . "'");
 $total_coupons= tep_db_num_rows($ngift_query);

//////////*********************************////
////*** curency display coding ***********/
$row_cur=getAnyTableWhereData(CURRENCY_TABLE,"code ='".DEFAULT_CURRENCY."'",'symbol_left,symbol_right');
$sym_left=(tep_not_null($row_cur['symbol_left'])?$row_cur['symbol_left'].' ':'');
$sym_rt=(tep_not_null($row_cur['symbol_right'])?' '.$row_cur['symbol_right']:'');
//////**********currency display ***************************/

if($user_type !=$coupon['user']) 
	$add_row='<tr class="table-success" ><th colspan="8" >'.tep_db_output($coupon['user']).'</th></tr>';
else
	$add_row='';
$user_type =$coupon['user'];
  $template->assign_block_vars('gift', array( 'row_selected' => $row_selected,
   'action'    => $action_image,
   'discount_code'=>tep_db_output($coupon['certificate_number']),
   'no_of_emp'=>$total_coupons,
   'reason'      => tep_db_output($coupon['gift_reason']),
   'start_date'     => tep_date_long($coupon['start_date']),
   'end_date'     => tep_date_long($coupon['expired']),
   'amount'     => ($coupon['discount_type_id']=='3'?$sym_left.tep_db_output($coupon['amount']).$sym_rt:tep_db_output($coupon['amount'])."%"),
   'status'    => $status,
	'add_row'  =>$add_row,
   ));
 }
}

//// for right side
$ADMIN_RIGHT_HTML="";
////*** curency display coding ***********/
$row_cur=getAnyTableWhereData(CURRENCY_TABLE,"code ='".DEFAULT_CURRENCY."'",'symbol_left,symbol_right');
$symbol=(tep_not_null($row_cur['symbol_left'])?$row_cur['symbol_left'].' ':$row_cur['symbol_right']);
/////********************************************/
$heading = array();
$contents = array();
switch ($action)
{
 case 'new':
 case 'insert':
 case 'save':
$discount_type_id='<div class="custom-control custom-radio">'.tep_draw_radio_field('TR_discount_type_id', '2', '', $discount_type_id, 'id="radio_discount_type_id1" class="custom-control-input"').'&nbsp;<label class="custom-control-label small" for="radio_discount_type_id1">Percentage</label></div>
<div class="custom-control custom-radio">'.tep_draw_radio_field('TR_discount_type_id', '3', '', $discount_type_id, 'id="radio_discount_type_id2" class="custom-control-input"').'&nbsp;<label class="custom-control-label  small" for="radio_discount_type_id2">Amount</label></div>';

$user_type_data='<div class="custom-control custom-radio">'.tep_draw_radio_field('user_type', 'reruiter', 'reruiter s', '', 'id="radio_user_type_id1" class="custom-control-input"').'&nbsp;<label class="custom-control-label small" for="radio_user_type_id1">Recruiter</label></div>
<div class="custom-control custom-radio">'.tep_draw_radio_field('user_type', 'jobseeker', '', '', 'id="radio_user_type_id2" class="custom-control-input"').'&nbsp;<label class="custom-control-label  small" for="radio_user_type_id2">Jobseeker</label></div>';

    $heading[] = array('text' => '<div class="list-group">
  <div class="font-weight-bold text-primary">
  '.TEXT_INFO_HEADING_ADD_COUPON.'</div>
  </div>');
  $contents = array('form' => tep_draw_form('coupon', PATH_TO_ADMIN.FILENAME_ADMIN1_COUPONS, 'action=insert','post',' onsubmit="return ValidateForm(this)"'));

  // $contents[] = array('text' => TEXT_INFO_NEW_INTRO);
		// $contents[] = array('text' => '<br>'.TEXT_INFO_COUPON_REASON.'<br>'.tep_draw_input_field('TR_gift_reason', $_POST['TR_gift_reason'], '' ));
		// $contents[] = array('text' => '<br>'.TEXT_INFO_COUPON_TYPE.'<br>'.$discount_type_id);
		// $contents[] = array('text' => '<br>'.TEXT_INFO_COUPON_AMOUNT.' ( '.$symbol.' ) / Percentage<br>'.tep_draw_input_field('IN_amount', $_POST['IN_amount'], '' ));
		// $contents[] = array('text' => '<br>'.TEXT_INFO_COUPON_START_DATE.'<br>'.datelisting(gmdate("Y-m-d"), 'name="start_date"', 'name="start_month"', 'name="start_year"', "2019", gmdate("Y")+2));
		// $contents[] = array('text' => '<br>'.TEXT_INFO_COUPON_END_DATE.'<br>'.datelisting(gmdate("d-m-Y",mktime(0,0,0,gmdate("m"),gmdate("d")+7,gmdate("Y"))), 'name="end_date"', 'name="end_month"', 'name="end_year"', "2019", gmdate("Y")+2));
		// $contents[] = array('align' => 'left', 'text' => '<br>'.tep_image_submit(PATH_TO_BUTTON.'button_insert.gif', IMAGE_INSERT).'&nbsp;<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_COUPONS).'">'.tep_image_button(PATH_TO_BUTTON.'button_cancel.gif', IMAGE_CANCEL).'</a>');

  $contents[] = array('align' => '', 'text' => '<div class="py-2">
  <div class="mb-1 text-danger">'.TEXT_INFO_NEW_INTRO.'</div>
  <div class="form-group">
  <label>'.TEXT_INFO_COUPON_REASON.'</label>
  '.tep_draw_input_field('TR_gift_reason', $_POST['TR_gift_reason'], 'class="form-control form-control-sm"' ).'
  </div>
<div class="form-group">
  <label>'.TEXT_INFO_USER_TYPE.'</label>
  '.$user_type_data.'
  </div>
  <div class="form-group">
  <label>'.TEXT_INFO_COUPON_TYPE.'</label>
  '.$discount_type_id.'
  </div>
  

  <div class="form-group">
  <label>'.TEXT_INFO_COUPON_AMOUNT.'( '.$symbol.' ) / Percentage</label>
  '.tep_draw_input_field('IN_amount', $_POST['IN_amount'], 'class="form-control form-control-sm"' ).'
  </div>

  <div class="form-group">
  <label>'.TEXT_INFO_COUPON_START_DATE.'</label>
  '.datelisting(gmdate("Y-m-d"), 'name="start_date" class="form-control form-control-sm"', 'name="start_month" class="form-control form-control-sm"', 'name="start_year" class="form-control form-control-sm"', "2019", gmdate("Y")+2).'
  </div>

  <div class="form-group">
  <label>'.TEXT_INFO_COUPON_END_DATE.'</label>
  '.datelisting(gmdate("d-m-Y",mktime(0,0,0,gmdate("m"),gmdate("d")+7,gmdate("Y"))), 'name="end_date" class="form-control form-control-sm"', 'name="end_month" class="form-control form-control-sm"', 'name="end_year" class="form-control form-control-sm"', "2019", gmdate("Y")+2).'
  </div>

  '.tep_draw_submit_button_field('',IMAGE_INSERT,'class="btn btn-primary"').'
  <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_COUPONS).'">'
  . IMAGE_CANCEL . '</a>
  </div>');

    break;
 case 'edit':
$discount_type_id='<div class="custom-control custom-radio">'.tep_draw_radio_field('TR_discount_type_id', '2', '', $cInfo->discount_type_id, 'id="radio_discount_type_id1" class="custom-control-input"').'&nbsp;<label class="custom-control-label small" for="radio_discount_type_id1">Percentage</label></div>
<div class="custom-control custom-radio">'.tep_draw_radio_field('TR_discount_type_id', '3', '', $cInfo->discount_type_id, 'id="radio_discount_type_id2" class="custom-control-input"').'&nbsp;<label class="custom-control-label  small" for="radio_discount_type_id2">Amount</label></div>';


  $value_field=tep_draw_input_field('TR_certificate_number', $cInfo->certificate_number, '' );
  $heading[] = array('text' => '<div class="list-group">
  <div class="font-weight-bold text-primary">
  '.TEXT_INFO_HEADING_EDIT_COUPON.'</div>
  </div>');
  $contents = array('form' => tep_draw_form('coupon', PATH_TO_ADMIN.FILENAME_ADMIN1_COUPONS, 'gift_id=' . $cInfo->gift_id.'&page='.$_GET['page'].'&action=save','post',' onsubmit="return ValidateForm(this)"'));

  // $contents[] = array('text' => TEXT_INFO_EDIT_INTRO);
		// $contents[] = array('text' => '<b>Discount Code : '.$cInfo->certificate_number.'</b>');
		// $contents[] = array('text' => '<br>'.TEXT_INFO_COUPON_TYPE.'<br>'.$discount_type_id);
		// $contents[] = array('text' => '<br>'.TEXT_INFO_COUPON_REASON.'<br>'.tep_draw_input_field('TR_gift_reason', $cInfo->gift_reason, '' ));
		// $contents[] = array('text' => '<br>'.TEXT_INFO_COUPON_AMOUNT.'<br> / Percentage'.tep_draw_input_field('IN_amount', $cInfo->amount, '' ));
		// $contents[] = array('text' => '<br>'.TEXT_INFO_COUPON_START_DATE.'<br>'.datelisting(@date("Y-m-d",mktime(0,0,0, substr($cInfo->start_date,5,2),substr($cInfo->start_date,8,2),substr($cInfo->start_date,0,4))), "name='start_date' ", "name='start_month' ", "name='start_year' ", "2019", date("Y")+1));
		// $contents[] = array('text' => '<br>'.TEXT_INFO_COUPON_END_DATE.'<br>'.datelisting(@date("Y-m-d",mktime(0,0,0, substr($cInfo->expired,5,2),substr($cInfo->expired,8,2),substr($cInfo->expired,0,4))), "name='end_date' ", "name='end_month' ", "name='end_year' ", "2019", date("Y")+1));
  // $contents[] = array('align' => 'left', 'text' => '<br>'.tep_image_submit(PATH_TO_BUTTON.'button_update.gif',IMAGE_UPDATE).'&nbsp;<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_COUPONS, 'gift_id=' . $_GET['gift_id'] . '&gift_id=' . $cInfo->gift_id ). '">'.tep_image_button(PATH_TO_BUTTON.'button_cancel.gif',IMAGE_CANCEL).'</a>');


  $contents[] = array('align' => '', 'text' => '<div class="py-2">
  <div class="mb-1 text-danger">'.TEXT_INFO_EDIT_INTRO.'</div>
  <div class="mb-1 font-weight-bold">Discount Code : '.$cInfo->certificate_number.'</div>
  <div class="form-group">
  <label>'.TEXT_INFO_COUPON_REASON.'</label>
  '.tep_draw_input_field('TR_gift_reason', $cInfo->gift_reason, 'class="form-control form-control-sm"' ).'
  </div>

   

  <div class="form-group">
  <label>'.TEXT_INFO_COUPON_AMOUNT.'( '.$symbol.' ) / Percentage</label>
  '.tep_draw_input_field('IN_amount', $cInfo->amount, 'class="form-control form-control-sm"' ).'
  </div>

  <div class="form-group">
  <label>'.TEXT_INFO_COUPON_START_DATE.'</label>
  '.datelisting(@date("Y-m-d",mktime(0,0,0, substr($cInfo->start_date,5,2),substr($cInfo->start_date,8,2),substr($cInfo->start_date,0,4))), "name='start_date' class='form-control form-control-sm'", "name='start_month' class='form-control form-control-sm'", "name='start_year' class='form-control form-control-sm'", "2019", date("Y")+1).'
  </div>

  <div class="form-group">
  <label>'.TEXT_INFO_COUPON_END_DATE.'</label>
  '.datelisting(@date("Y-m-d",mktime(0,0,0, substr($cInfo->expired,5,2),substr($cInfo->expired,8,2),substr($cInfo->expired,0,4))), "name='end_date' class='form-control form-control-sm'", "name='end_month' class='form-control form-control-sm'", "name='end_year' class='form-control form-control-sm'", "2019", date("Y")+1).'
  </div>

  '.tep_draw_submit_button_field('',IMAGE_UPDATE,'class="btn btn-primary"').'
  <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_COUPONS, 'gift_id=' . $_GET['gift_id'] . '&gift_id=' . $cInfo->gift_id ).'">'
  . IMAGE_CANCEL . '</a>
  </div>');

  break;
 case 'delete':
  $heading[] = array('text' => '<div class="list-group">
  <div class="font-weight-bold text-primary">
  '.$cInfo->certificate_number .'</div>
  </div>');
  $contents = array('form' => tep_draw_form('coupon_delete', PATH_TO_ADMIN.FILENAME_ADMIN1_COUPONS, 'page=' . $_GET['page'] . '&gift_id=' . $cInfo->gift_id . '&action=deleteconfirm'));

  $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
  <div class="mb-1 text-danger">'.TEXT_DELETE_INTRO.'</div>
  <a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_COUPONS, 'page=' . $_GET['page'] . '&gift_id=' . $_GET['gift_id'].'&action=confirm_delete') . '">'
  .IMAGE_CONFIRM.'</a>
  <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_COUPONS, 'page=' . $_GET['page'] . '&gift_id=' . $_GET['gift_id']) . '">'
  . IMAGE_CANCEL . '</a>
  </div>');
 break;
 default:
  if (isset($cInfo) && is_object($cInfo))
		{
  $heading[] = array('text' => '<div class="list-group">
  <div class="font-weight-bold text-primary">
  '.TEXT_INFO_HEADING_ADD_COUPON.'<p>'.tep_db_output($cInfo->certificate_number).'</p></div>
  </div>');

  //  $contents[] = array('text' => tep_db_output($cInfo->certificate_number));
  //  $contents[] = array('align' => 'left', 'text' => '<br><a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_COUPONS, 'page=' . $_GET['page'] .'&gift_id=' . $cInfo->gift_id . '&action=edit') . '">'.tep_image_button(PATH_TO_BUTTON.'button_edit.gif',IMAGE_EDIT).'</a>&nbsp;<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_COUPONS, 'page=' . $_GET['page'] .'&gift_id=' . $cInfo->gift_id . '&action=delete') . '">'.tep_image_button(PATH_TO_BUTTON.'button_delete.gif',IMAGE_DELETE).'</a>');
  //  $contents[] = array('text' => '<br>'.TEXT_INFO_ACTION);

  $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
  <a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_COUPONS, 'page=' . $_GET['page'] .'&gift_id=' . $cInfo->gift_id . '&action=edit') . '">'
  .IMAGE_EDIT.'</a>
  <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_COUPONS, 'page=' . $_GET['page'] .'&gift_id=' . $cInfo->gift_id . '&action=delete') . '">'
  .IMAGE_DELETE.'</a>
  <div class="mt-1"><h5>'.TEXT_INFO_ACTION.'</h5></div>
  </div>');
  }
  break;
}
if ( (tep_not_null($heading)) && (tep_not_null($contents)) )
{
 $box = new right_box;
 $ADMIN_RIGHT_HTML.= $box->infoBox($heading, $contents);
	$RIGHT_BOX_WIDTH=RIGHT_BOX_WIDTH;
}
else
{
	$RIGHT_BOX_WIDTH='0';
}
/////
$template->assign_vars(array(
'TABLE_HEADING_COUPON_REASON'=>TABLE_HEADING_COUPON_REASON,
'TABLE_HEADING_COUPON_USAGE'=>TABLE_HEADING_COUPON_USAGE,
 'TABLE_HEADING_COUPON_CODE'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_COUPONS, tep_get_all_get_params(array('sort','gID','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][0]."' class='white'>".TABLE_HEADING_COUPON_CODE.$obj_sort_by_clause->return_sort_array['image'][0]."</a>",
 'TABLE_HEADING_COUPON_START_DATE'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_COUPONS, tep_get_all_get_params(array('sort','gID','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][1]."' class='white'>".TABLE_HEADING_COUPON_START_DATE.$obj_sort_by_clause->return_sort_array['image'][1]."</a>",
 'TABLE_HEADING_COUPON_STATUS'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_COUPONS, tep_get_all_get_params(array('sort','gID','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][2]."' class='white'>".TABLE_HEADING_COUPON_STATUS.$obj_sort_by_clause->return_sort_array['image'][2]."</a>",
 'TABLE_HEADING_COUPON_END_DATE'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_COUPONS, tep_get_all_get_params(array('sort','gID','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][3]."' class='white'>".TABLE_HEADING_COUPON_END_DATE.$obj_sort_by_clause->return_sort_array['image'][3]."</a>",
 'TABLE_HEADING_COUPON_AMOUNT'=>TABLE_HEADING_COUPON_AMOUNT,
 'TABLE_HEADING_COUPON_EMP_NAME'=>TABLE_HEADING_COUPON_EMP_NAME,
 'TABLE_HEADING_ACTION'=>TABLE_HEADING_ACTION,
 'count_rows'=>$coupon_split->display_count($coupon_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_USER_CATEGORIES),
 'no_of_pages'=>$coupon_split->display_links($coupon_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']),
 'new_button'=>'<a class="btn btn-primary float-right" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_COUPONS, 'page=' . $_GET['page'] .'&action=new') . '"><i class="bi bi-plus-lg me-2"></i>'.IMAGE_NEW.'</a>',
 'HEADING_TITLE'=>HEADING_TITLE,
 'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
 'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('coupons');
?>