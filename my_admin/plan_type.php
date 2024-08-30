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
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_ADMIN_PLAN_TYPE);
$template->set_filenames(array('plan_type' => 'plan_type.htm'));
include_once(FILENAME_ADMIN_BODY);
$action = (isset($_GET['action']) ? $_GET['action'] : '');

if ($action!="") 
{
 switch ($action) 
	{
  case 'confirm_delete':
   $id = tep_db_prepare_input($_GET['id']);
   tep_db_query("delete from " . PLAN_TYPE_TABLE . " where id = '" . (int)$id . "'");
			$messageStack->add_session(MESSAGE_SUCCESS_DELETED, 'success');
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_PLAN_TYPE, 'page=' . $_GET['page']));
  break;
  case 'insert':
  case 'save':
   $plan_type_name=tep_db_prepare_input($_POST['TR_plan_type_name']);
   $de_plan_type_name=tep_db_prepare_input($_POST['TR_de_plan_type_name']);
   $time_period=tep_db_prepare_input($_POST['IR_time_period']);
   $time_period1=tep_db_prepare_input($_POST['time_period1']);
   $fee=tep_db_prepare_input($_POST['MR_fee']);
   $featured_job=tep_db_prepare_input($_POST['featured_job']);
   $currency=tep_db_prepare_input(DEFAULT_CURRENCY);

   $priority = tep_db_prepare_input($_POST['IN_priority']);
   $sql_data_array = array('plan_type_name'    => $plan_type_name,
                       				'de_plan_type_name' => $de_plan_type_name,
                       				'time_period'       => $time_period,
                       				'time_period1'      => $time_period1,
                       				'fee'               => $fee,
                       				'currency'          => $currency,
                       				'priority'          => $priority,
                       				'featured_job'      => $featured_job,
				                   );
   $sql_data_array['job']=(tep_not_null($_POST['ch_no_of_jobs'])?'2147483647':tep_db_prepare_input($_POST['no_of_jobs']));
   $sql_data_array['cv']=(tep_not_null($_POST['ch_no_of_times'])?'2147483647':tep_db_prepare_input($_POST['no_of_days']));
   $sql_data_array['sms']=(tep_not_null($_POST['ch_no_of_sms'])?'2147483647':tep_db_prepare_input($_POST['no_of_sms']));

			if($action=='insert')
			{
				if($row_chek=getAnyTableWhereData(PLAN_TYPE_TABLE,"plan_type_name='".tep_db_input($plan_type_name)."'",'id'))
				{
					$messageStack->add(MESSAGE_NAME_ERROR, 'error');
				}
				else if($row_chek=getAnyTableWhereData(PLAN_TYPE_TABLE,"de_plan_type_name='".tep_db_input($de_plan_type_name)."'",'id'))
				{
					$messageStack->add(MESSAGE_FR_NAME_ERROR, 'error');
				}
				else
				{
     tep_db_perform(PLAN_TYPE_TABLE, $sql_data_array);
  			$messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');
					tep_redirect(FILENAME_ADMIN1_ADMIN_PLAN_TYPE);
				}
			}
			else
			{
    $id=(int)$_GET['id'];
				if($row_chek=getAnyTableWhereData(PLAN_TYPE_TABLE,"plan_type_name='".tep_db_input($plan_type_name)."' and id!='$id'",'id'))
				{
					$messageStack->add(MESSAGE_NAME_ERROR, 'error');
					$action='edit';
				}
				else if($row_chek=getAnyTableWhereData(PLAN_TYPE_TABLE,"de_plan_type_name='".tep_db_input($de_plan_type_name)."' and id!='$id'",'id'))
				{
					$messageStack->add(MESSAGE_FR_NAME_ERROR, 'error');
					$action='edit';
				}
				else
				{
     tep_db_perform(PLAN_TYPE_TABLE, $sql_data_array, 'update', "id = '" . (int)$id . "'");
  			$messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
					tep_redirect(FILENAME_ADMIN1_ADMIN_PLAN_TYPE.'?page='.$_GET['page'].'&id='.$id);
				}
			}
  break;
 }
}
///////////// Middle Values 
$plan_type_query_raw="select * from " . PLAN_TYPE_TABLE ." order by priority";
$plan_type_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $plan_type_query_raw, $plan_type_query_numrows);
$plan_type_query = tep_db_query($plan_type_query_raw);
if(tep_db_num_rows($plan_type_query) > 0)
{
 $alternate=1;
 while ($plan_type = tep_db_fetch_array($plan_type_query)) 
 {
  if ((!isset($_GET['id']) || (isset($_GET['id']) && ($_GET['id'] == $plan_type['id']))) && !isset($cInfo) && (substr($action, 0, 3) != 'new')) 
  {
   $cInfo = new objectInfo($plan_type);
  }
  if ( (isset($cInfo) && is_object($cInfo)) && ($plan_type['id'] == $cInfo->id) ) 
  {
   $row_selected=' id="defaultSelected" class="table-secondary dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . FILENAME_ADMIN1_ADMIN_PLAN_TYPE . '?page='.$_GET['page'].'&id=' . $cInfo->id . '&action=edit\'"';
  } 
  else 
  {
   $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . FILENAME_ADMIN1_ADMIN_PLAN_TYPE . '?page='.$_GET['page'].'&id=' . $plan_type['id'] . '\'"';
  }
  $alternate++;
  if ( (isset($cInfo) && is_object($cInfo)) && ($plan_type['id'] == $cInfo->id) ) 
  { 
   $action_image=tep_image(PATH_TO_IMAGE.'icon_arrow_right.gif',IMAGE_EDIT); 
  } 
  else 
  { 
   $action_image='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_PLAN_TYPE, 'page='.$_GET['page'].'&id=' . $plan_type['id']) . '">'.tep_image(PATH_TO_IMAGE.'icon_info.gif',IMAGE_INFO).'</a>'; 
  }
  $template->assign_block_vars('plan_type', array( 'row_selected' => $row_selected,
   'action' => $action_image,
   'name' => tep_db_output($plan_type['plan_type_name']),
   'de_name' => tep_db_output($plan_type['de_plan_type_name']),
   'time_period' => tep_db_output($plan_type['time_period']).'&nbsp;'.($plan_type['time_period']>1?tep_db_output($plan_type['time_period1'])."s":tep_db_output($plan_type['time_period1'])),
   'fee' => tep_db_output($currencies->format($plan_type['fee'], ($plan_type['currency']!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($plan_type['currency']==DEFAULT_CURRENCY?$currencies->get_value($plan_type['currency']):''))),
   'row_selected' => $row_selected
   ));
 }
}
//// for right side
$ADMIN_RIGHT_HTML="";
$m_y_array[]=array('id'=>'Month','text'=>'Month(s)');
$m_y_array[]=array('id'=>'Year','text'=>'Year(s)');
$heading = array();
$contents = array();
$unlimited_job=($cInfo->job=="2147483647"?true:false);
$unlimited_cv=($cInfo->cv=="2147483647"?true:false);
$unlimited_sms=($cInfo->sms=="2147483647"?true:false);
$featured_job_array   = array();
$featured_job_array[] = array('id'=>'No','text'=>'No');
$featured_job_array[] = array('id'=>'Yes','text'=>'Yes');

switch ($action) 
{
 case 'new':
 case 'insert':
 case 'save':
    $heading[] = array('text' => '<div class="list-group">
    <div class="font-weight-bold text-primary">
    '.TEXT_INFO_HEADING_PLAN_TYPE.'</div>
    </div>');
  $contents = array('form' => tep_draw_form('plan_type', PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_PLAN_TYPE, 'action=insert','post',' onsubmit="return ValidateForm(this)"'));
  //$contents[] = array('text' => '<b>&nbsp;'.TEXT_INFO_PLAN_TYPE_NO_OF_SMS.'</b><br>&nbsp;' . tep_draw_input_field('no_of_sms', '', 'size="5" maxlength="5"').'&nbsp;&nbsp;'.tep_draw_checkbox_field('ch_no_of_sms', '',($unlimited_sms?true:false),'','id="check_ch_no_of_sms" onclick="unlimited();"').'&nbsp;<label for="check_ch_no_of_sms">Unlimited</label>');
  
  $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
  <div class="mb-1 text-danger">'.TEXT_INFO_NEW_INTRO.'</div>
  <div class="form-group">
  <label>'.TEXT_INFO_PLAN_TYPE_NAME.'</label>
  '.tep_draw_input_field('TR_plan_type_name','', 'class="form-control form-control-sm"' ).'
  </div>
  <div class="form-group">
  <label>'.TEXT_INFO_FR_PLAN_TYPE_NAME.'</label>
  '.tep_draw_input_field('TR_de_plan_type_name','', 'class="form-control form-control-sm"' ).'
  </div>
  <div class="form-group">
  <label>'.TEXT_INFO_PLAN_TYPE_TIME.'</label>
  '.tep_draw_input_field('IR_time_period', '', 'class="form-control form-control-sm"' ).tep_draw_pull_down_menu('time_period1', $m_y_array, $time_period1, 'class="form-control form-control-sm"', false).'
  </div>
  <div class="form-group">
  <label>'.TEXT_INFO_PLAN_TYPE_FEE.' ('.$currencies->get_symbol_left(DEFAULT_CURRENCY).')</label>
  '.tep_draw_input_field('MR_fee','', 'class="form-control form-control-sm"' ).'
  </div>

  <div class="form-group">
  <label>'.TEXT_INFO_PLAN_TYPE_NO_OF_JOBS.'</label>
  '.tep_draw_input_field('no_of_jobs', '', 'class="form-control form-control-sm" maxlength="5"').tep_draw_checkbox_field('ch_no_of_jobs', '',($unlimited_job?true:false),'','id="check_ch_no_of_jobs" onclick="unlimited();"').'
  <label for="check_ch_no_of_jobs">Unlimited</label></div>

  <div class="form-group">
  <label>'.TEXT_INFO_PLAN_TYPE_FEATURED_JOBS.'</label>
  '.tep_draw_pull_down_menu('featured_job', $featured_job_array, $featured_job,'class="form-control form-control-sm"').'
  </div>

  <div class="form-group">
  <label>'.TEXT_INFO_PLAN_TYPE_NO_OF_CVS.'</label>
  '.tep_draw_input_field('no_of_days', '', 'class="form-control form-control-sm" maxlength="5"').'&nbsp;&nbsp;'.tep_draw_checkbox_field('ch_no_of_times', '',($unlimited_cv?true:false),'','id="check_ch_no_of_times" onclick="unlimited();"').'
  <label for="check_ch_no_of_times">Unlimited</label></div>
  
  <div class="form-group">
  <label>'.TEXT_INFO_PLAN_TYPE_PRIORITY.'</label>
  '.tep_draw_input_field('IN_priority', $_POST['IN_priority'], 'class="form-control form-control-sm"' ).'
  </div>

  '.tep_draw_submit_button_field('',IMAGE_INSERT,'class="btn btn-primary"').'
  <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_PLAN_TYPE).'">' 
  . IMAGE_CANCEL . '</a>
  </div>');
    break;
 case 'edit':
  $value_field=tep_draw_input_field('TR_plan_type_name', $cInfo->plan_type_name, '' );
  $heading[] = array('text' => '<div class="list-group">
  <div class="font-weight-bold text-primary">
  '.TEXT_INFO_HEADING_PLAN_TYPE.'</div>
  </div>');
  $contents = array('form' => tep_draw_form('plan_type', PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_PLAN_TYPE, 'id=' . $cInfo->id.'&page='.$_GET['page'].'&action=save','post',' onsubmit="return ValidateForm(this)"'));

  //$contents[] = array('text' => '<b>&nbsp;'.TEXT_INFO_PLAN_TYPE_NO_OF_SMS.'</b><br>&nbsp;' . tep_draw_input_field('no_of_sms', ($unlimited_sms?'':$cInfo->sms), 'size="5" maxlength="5"'.($unlimited_sms?' disabled':'')).'&nbsp;&nbsp;'.tep_draw_checkbox_field('ch_no_of_sms', '',($unlimited_sms?true:false),'','id="check_ch_no_of_sms" onclick="unlimited();"').'&nbsp;<label for="check_ch_no_of_sms">Unlimited</label>');
  $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
  <div class="mb-1 text-danger">'.TEXT_INFO_EDIT_INTRO.'</div>
  <div class="form-group">
  <label>'.TEXT_INFO_PLAN_TYPE_NAME.'</label>
  '.tep_draw_input_field('TR_plan_type_name', $cInfo->plan_type_name, 'class="form-control form-control-sm"' ).'
  </div>
  <div class="form-group">
  <label>'.TEXT_INFO_FR_PLAN_TYPE_NAME.'</label>
  '.tep_draw_input_field('TR_de_plan_type_name', $cInfo->de_plan_type_name, 'class="form-control form-control-sm"' ).'
  </div>
  <div class="form-group">
  <label>'.TEXT_INFO_PLAN_TYPE_TIME.'</label>
  '.tep_draw_input_field('IR_time_period', $cInfo->time_period, 'class="form-control form-control-sm"' ).'&nbsp;'.tep_draw_pull_down_menu('time_period1', $m_y_array, $cInfo->time_period1, 'class="form-control form-control-sm"', false).'
  </div>
  <div class="form-group">
  <label>'.TEXT_INFO_PLAN_TYPE_FEE.' ('.$currencies->get_symbol_left(DEFAULT_CURRENCY).')</label>
  '.tep_draw_input_field('MR_fee', $currencies->format_without_symbol($cInfo->fee, true, DEFAULT_CURRENCY, $currencies->get_value($cInfo->currency)), 'class="form-control form-control-sm"' ).'
  </div>

  <div class="form-group">
  <label>'.TEXT_INFO_PLAN_TYPE_NO_OF_JOBS.'</label>
  '.tep_draw_input_field('no_of_jobs', ($unlimited_job?'':$cInfo->job), 'class="form-control form-control-sm" maxlength="5"'.($unlimited_job?' disabled':'')).'&nbsp;&nbsp;'.tep_draw_checkbox_field('ch_no_of_jobs', '',($unlimited_job?true:false),'','id="check_ch_no_of_jobs" onclick="unlimited();"').'
  <label for="check_ch_no_of_jobs">Unlimited</label></div>

  <div class="form-group">
  <label>'.TEXT_INFO_PLAN_TYPE_FEATURED_JOBS.'</label>
  '.tep_draw_pull_down_menu('featured_job', $featured_job_array, $cInfo->featured_job,'class="form-control form-control-sm"').'
  </div>

  <div class="form-group">
  <label>'.TEXT_INFO_PLAN_TYPE_NO_OF_CVS.'</label>
  '.tep_draw_input_field('no_of_days', ($unlimited_cv?'':$cInfo->cv), 'class="form-control form-control-sm" maxlength="5"'.($unlimited_cv?' disabled':'')).' '.tep_draw_checkbox_field('ch_no_of_times', '',($unlimited_cv?true:false),'','id="check_ch_no_of_times" onclick="unlimited();"').'
  <label for="check_ch_no_of_times">Unlimited</label></div>
  
  <div class="form-group">
  <label>'.TEXT_INFO_PLAN_TYPE_PRIORITY.'</label>
  '.tep_draw_input_field('IN_priority', $cInfo->priority, 'class="form-control form-control-sm"' ).'
  </div>

  '.tep_draw_submit_button_field('',IMAGE_UPDATE,'class="btn btn-primary"').'
  <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_PLAN_TYPE, 'gid=' . $_GET['gid'] . '&id=' . $cInfo->id ).'">' 
  . IMAGE_CANCEL . '</a>
  </div>');
  
  
  break;
 case 'delete':
  $heading[] = array('text' => '<div class="list-group">
  <div class="font-weight-bold text-primary">
  '.$cInfo->plan_type_name.'</div>
  </div>');
  $contents = array('form' => tep_draw_form('plan_type_delete', PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_PLAN_TYPE, 'page=' . $_GET['page'] . '&id=' . $nInfo->id . '&action=deleteconfirm'));
  $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
  <div class="mb-1 text-danger">'.TEXT_DELETE_INTRO.'</div>
  <a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_PLAN_TYPE, 'page=' . $_GET['page'] . '&id=' . $_GET['id'].'&action=confirm_delete') . '">'
  .IMAGE_CONFIRM.'</a>
  <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_PLAN_TYPE, 'page=' . $_GET['page'] . '&id=' . $_GET['id']) . '">' 
  . IMAGE_CANCEL . '</a>
  </div>');
 break;
 default:
  if (isset($cInfo) && is_object($cInfo)) 
		{
   $heading[] = array('text' => '<div class="list-group"><div class="font-weight-bold text-primary">'.TEXT_INFO_HEADING_PLAN_TYPE.'</div></div>');
   $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
   <div class="mb-1 text-danger">'.tep_db_output($cInfo->plan_type_name).'</div>
   <a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_PLAN_TYPE, 'page=' . $_GET['page'] .'&id=' . $cInfo->id . '&action=edit') . '">'
   .IMAGE_EDIT.'</a>
   <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_PLAN_TYPE, 'page=' . $_GET['page'] .'&id=' . $cInfo->id . '&action=delete') . '">'
   .IMAGE_DELETE.'</a>
   <div class="mt-1">'.TEXT_INFO_ACTION.'</div>
   </div>');
   if($cInfo->job > 0)
   $contents[] = array('text' => '
                        <div class="py-2">                        
                        <div class="row">
                        <div class="col-12">
                        <label class="font-weight-bolder">
                        '.TEXT_INFO_PLAN_TYPE_TIME.'
                        </label>
                        <h5 class="mt-0">
                        '.tep_db_output($cInfo->time_period).'&nbsp;'.($cInfo->time_period >1?tep_db_output($cInfo->time_period1)."s":tep_db_output($cInfo->time_period1)).'
                        </h5>                       
                        </div>
                        <div class="col-12">
                        <label class="font-weight-bolder">
                        '.TEXT_INFO_PLAN_TYPE_FEE.'
                        </label>
                        <h5 class="mt-0">
                        '.tep_db_output($currencies->format($cInfo->fee, ($cInfo->currency!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($cInfo->currency==DEFAULT_CURRENCY?$currencies->get_value($cInfo->currency):''))).'
                        </h5>
                        </div>
                        <div class="col-12">
                        <label class="font-weight-bolder">
                        '.TEXT_INFO_PLAN_TYPE_NO_OF_JOBS.'
                        </label>
                        <h5 class="mt-0">
                        '.($unlimited_job?'Unlimited':tep_db_output($cInfo->job)).'
                        </h5>
                        </div>
                        <div class="col-12">
                        <label class="font-weight-bolder">
                        '.TEXT_INFO_PLAN_TYPE_FEATURED_JOBS.'
                        </label>
                        <h5 class="mt-0">
                        '.$cInfo->featured_job.'
                        </h5>
                        </div>
                        <div class="col-12">
                        <label class="font-weight-bolder">
                        '.TEXT_INFO_PLAN_TYPE_NO_OF_CVS.'
                        </label>
                        <h5 class="mt-0">
                        '.($unlimited_cv?'Unlimited':tep_db_output($cInfo->cv)).'
                        </h5>
                        </div>
                        </div>
                        </div>');
   //$contents[] = array('text' => '<br><b>'.TEXT_INFO_PLAN_TYPE_NO_OF_SMS.'</b><br>'.($unlimited_sms?'Unlimited':tep_db_output($cInfo->sms))."<br>&nbsp;");  
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
 'TABLE_HEADING_PLAN_TYPE_NAME'=>TABLE_HEADING_PLAN_TYPE_NAME,
 'TABLE_HEADING_FR_PLAN_TYPE_NAME'=>TABLE_HEADING_FR_PLAN_TYPE_NAME,
 'TABLE_HEADING_PLAN_TYPE_TIME_PERIOD'=>TABLE_HEADING_PLAN_TYPE_TIME_PERIOD,
 'TABLE_HEADING_PLAN_TYPE_FEE'=>TABLE_HEADING_PLAN_TYPE_FEE.' ('.$currencies->get_symbol_left(DEFAULT_CURRENCY).')',
 'TABLE_HEADING_ACTION'=>TABLE_HEADING_ACTION,
 'count_rows'=>$plan_type_split->display_count($plan_type_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PLAN_TYPE),
 'no_of_pages'=>$plan_type_split->display_links($plan_type_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']),
 'new_button'=>'<a class="btn btn-primary float-right" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_PLAN_TYPE, 'page=' . $_GET['page'] .'&action=new') . '"><i class="bi bi-plus-lg me-2"></i>'.IMAGE_NEW.'</a>',
 'HEADING_TITLE'=>HEADING_TITLE,
 'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
 'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('plan_type');
?>