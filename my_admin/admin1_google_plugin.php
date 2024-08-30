<?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2012  #**********
***********************************************************
*/
include_once("../include_files.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_GOOGLE_PLUGIN);
$template->set_filenames(array('google_plugin' => 'admin1_google_plugin.htm'));
include_once(FILENAME_ADMIN_BODY);
$google_app_key  = MODULE_GOOGLE_PLUGIN_APP_KEY; 
$google_app_secret = check_data1(MODULE_GOOGLE_PLUGIN_APP_SECRET_KEY,'##@##','app','passw');
if($google_app_secret==-1)
$google_app_secret= '';

//////////////
$action = (isset($_POST['action']) ? $_POST['action'] : '');
if (tep_not_null($action)) 
{
 switch ($action) 
	{
  case 'update':
   $google_submitter_status = tep_db_prepare_input($_POST['google_plugin_status']);
   $google_app_key     = tep_db_prepare_input($_POST['google_app_key']);
   $google_app_secret  = tep_db_prepare_input($_POST['google_app_secret']);
   $j_module_status      = tep_db_prepare_input($_POST['j_module_status']);
   $r_module_status      = tep_db_prepare_input($_POST['r_module_status']);
   
   $sql_data_array=array('configuration_value'=>$google_submitter_status,'updated'=>'now()');
   tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_GOOGLE_PLUGIN'");

   $sql_data_array=array('configuration_value'=>$j_module_status,'updated'=>'now()');
   tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_GOOGLE_PLUGIN_JOBSEEKER'");

   $sql_data_array=array('configuration_value'=>$r_module_status,'updated'=>'now()');
   tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_GOOGLE_PLUGIN_RECRUITER'");

   $sql_data_array=array('configuration_value'=>$google_app_key,'updated'=>'now()');
   tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_GOOGLE_PLUGIN_APP_KEY'");

   $sql_data_array=array('configuration_value'=>encode_string('app##@##'.$google_app_secret.'##@##passw'),'updated'=>'now()');
   tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_GOOGLE_PLUGIN_APP_SECRET_KEY'");
   ///////////////////////
   $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
   tep_redirect(FILENAME_ADMIN1_GOOGLE_PLUGIN);   
  break;
 }
}

$google_submitter_status = MODULE_GOOGLE_PLUGIN;
$j_module_status           = MODULE_GOOGLE_PLUGIN_JOBSEEKER;
$r_module_status           = MODULE_GOOGLE_PLUGIN_RECRUITER;
$google_submitter_status1= 'ENABLED';
if(MODULE_GOOGLE_PLUGIN!='enable')
{
 $google_submitter_status = 'disable';
 $google_submitter_status1= 'DISABLED';
}

$google_status_array=array();
$google_status_array[]=array('id'=>'enable','text'=>'Enabled');
$google_status_array[]=array('id'=>'disable','text'=>'Disabled');

$redirect_url=tep_href_link(FILENAME_GOOGLE_APPLICATION);
$url=parse_url($redirect_url);
if(preg_match('/^www\./i', $url['host'], $match))
$redirect_url.="<br>".$url['scheme'].'://'.substr($url['host'],4).$url['path'];
elseif(!preg_match('/^[1-9]/i', $url['host'], $match))
$redirect_url.="<br>".$url['scheme'].'://www.'.$url['host'].$url['path'];

$template->assign_vars(array(
 'HEADING_TITLE'=>HEADING_TITLE,
 'google_PLUGIN_STATUS'=>$google_submitter_status1,
 'google_form' => tep_draw_form('google_form',PATH_TO_ADMIN.FILENAME_ADMIN1_GOOGLE_PLUGIN,'', 'post','onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action','update'),
 'INFO_TEXT_GOOGLE_REDIRECT_URL'  => $redirect_url,
 'INFO_TEXT_GOOGLE_JAVASCRIPT_URL'=> HOST_NAME_MAIN,
 'INFO_TEXT_GOOGLE_PLUGIN'        => INFO_TEXT_GOOGLE_PLUGIN,
 'INFO_TEXT_GOOGLE_PLUGIN1'       => tep_draw_pull_down_menu('google_plugin_status', $google_status_array, $google_submitter_status,'class="form-control form-control-sm"'),
 'INFO_TEXT_GOOGLE_PLUGIN_KEY'    => INFO_TEXT_GOOGLE_PLUGIN_KEY,
 'INFO_TEXT_GOOGLE_PLUGIN_KEY1'   => tep_draw_input_field('google_app_key',$google_app_key,'class="form-control form-control-sm"'),
 'INFO_TEXT_GOOGLE_PLUGIN_SECRET' => INFO_TEXT_GOOGLE_PLUGIN_SECRET,
 'INFO_TEXT_GOOGLE_PLUGIN_SECRET1'=> tep_draw_input_field('google_app_secret',$google_app_secret,'class="form-control form-control-sm"'),
 'TEXT_INFO_J_MODULE_STATUS'     => TEXT_INFO_J_MODULE_STATUS, 
 'TEXT_INFO_J_MODULE_STATUS1'    => tep_draw_radio_field("j_module_status", 'enable',true,$j_module_status,'id="status_active_j"').'&nbsp; <label for="status_active_j" >Active </label>&nbsp;'.tep_draw_radio_field("j_module_status", 'disable',false,$j_module_status,'id="status_inactive_j"').'&nbsp;<label for="status_inactive_j" >Inactive</label>', 
 'TEXT_INFO_R_MODULE_STATUS'     => TEXT_INFO_R_MODULE_STATUS, 
 'TEXT_INFO_R_MODULE_STATUS1'    => tep_draw_radio_field("r_module_status", 'enable',true,$r_module_status,'id="status_active_r"').'&nbsp; <label for="status_active_r" >Active </label>&nbsp;'.tep_draw_radio_field("r_module_status", 'disable',false,$r_module_status,'id="status_inactive_r"').'&nbsp;<label for="status_inactive_r" >Inactive</label>', 

 'button' => tep_draw_submit_button_field('',IMAGE_UPDATE,'class="btn btn-primary"'),
 'update_message'=>$messageStack->output()));
$template->pparse('google_plugin');
?>