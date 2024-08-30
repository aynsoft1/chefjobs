<?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2009  #**********
***********************************************************
*/
include_once("../include_files.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_GOOGLE_ANALYTICS);
$template->set_filenames(array('google_analytics' => 'admin1_google_analytics.htm'));
include_once(FILENAME_ADMIN_BODY);

$google_analytics_ua_id=MOGULE_GOOGLE_ANALYTICES_UA_ID;

//////////////
$action = (isset($_POST['action']) ? $_POST['action'] : '');

if (tep_not_null($action)) 
{
 switch ($action) 
	{
  case 'update':
   $google_analytics_status = tep_db_prepare_input($_POST['google_analytics_status']);
   $google_analytics_ua_id  = tep_db_prepare_input($_POST['TR_google_analytics_ua_id']);
   
   if($google_analytics_ua_id=='')
   $google_analytics_ua_id='UA-XXXXX-X';
   $sql_data_array=array('configuration_value'=>$google_analytics_status,'updated'=>'now()');
   tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MOGULE_GOOGLE_ANALYTICES'");
   $sql_data_array=array('configuration_value'=>$google_analytics_ua_id,'updated'=>'now()');
   tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MOGULE_GOOGLE_ANALYTICES_UA_ID'");
   $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
   tep_redirect(FILENAME_ADMIN1_GOOGLE_ANALYTICS);   
  break;
 }
}
if(MOGULE_GOOGLE_ANALYTICES_UA_ID=='')
$google_analytics_analytics_ua_id = 'UA-XXXXX-X';

$google_analytics_status = MOGULE_GOOGLE_ANALYTICES;
$google_analytics_status1= 'ENABLED';
if(MOGULE_GOOGLE_ANALYTICES!='enable')
{
 $google_analytics_status = 'disable';
 $google_analytics_status1= 'DISABLED';
}

$google_analytics_array=array();
$google_analytics_array[]=array('id'=>'enable','text'=>'Enabled');
$google_analytics_array[]=array('id'=>'disable','text'=>'Disabled');

$template->assign_vars(array(
 'HEADING_TITLE'=>HEADING_TITLE,
 'GOOGLE_ANALYTICS_STATUS'=>$google_analytics_status1,
 'analytics_form' => tep_draw_form('google_analytics',PATH_TO_ADMIN.FILENAME_ADMIN1_GOOGLE_ANALYTICS,'', 'post','onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action','update'),
 'INFO_TEXT_GOOGLE_ANALYTICS'  => INFO_TEXT_GOOGLE_ANALYTICS,
 'INFO_TEXT_GOOGLE_ANALYTICS1' => tep_draw_pull_down_menu('google_analytics_status', $google_analytics_array, $google_analytics_status,'class="form-control form-control-sm"'),
 'INFO_TEXT_GOOGLE_ANALYTICS_UA_ID'=>INFO_TEXT_GOOGLE_ANALYTICS_UA_ID,
 'INFO_TEXT_GOOGLE_ANALYTICS_UA_ID1'=>tep_draw_input_field('TR_google_analytics_ua_id',$google_analytics_ua_id,'size="40" class="form-control form-control-sm"'),
 'button' => tep_draw_submit_button_field('',IMAGE_UPDATE,'class="btn btn-primary"'),

 'update_message'=>$messageStack->output()));
$template->pparse('google_analytics');
?>