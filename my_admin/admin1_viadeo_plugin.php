<?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2012  #**********
***********************************************************
*/
include_once("../include_files.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_VIADEO_PLUGIN);
$template->set_filenames(array('viadeo_plugin' => 'admin1_viadeo_plugin.htm'));
include_once(FILENAME_ADMIN_BODY);
$viadeo_app_key  = MODULE_VIADEO_PLUGIN_APP_KEY; 
$viadeo_app_secret = check_data1(MODULE_VIADEO_PLUGIN_APP_SECRET_KEY,'##@##','app','passw');
if($viadeo_app_secret==-1)
$viadeo_app_secret= '';

//////////////
$action = (isset($_POST['action']) ? $_POST['action'] : '');
if (tep_not_null($action)) 
{
 switch ($action) 
	{
  case 'test_authentication':
   $viadeo_app_key        = tep_db_prepare_input($_POST['viadeo_app_key']);
   $viadeo_app_secret     = tep_db_prepare_input($_POST['viadeo_app_secret']);
   $viadeo_submitter_status    = tep_db_prepare_input($_POST['viadeo_plugin_status']);
   ini_set('max_execution_time','0');
   include_once("../class/viadeooauth.php");
   $viadeo_obj = new ViadeoOAuth($viadeo_app_key,$viadeo_app_secret,'');
   if($viadeo_obj ->chechAuthorization())
   {
    $messageStack->add(MESSAGE_SUCCESS_AUTHENTICATION, 'success');
   }
   else
    $messageStack->add(MESSAGE_ERROR_AUTHENTICATION, 'error'); 
  break;
  case 'update':
   $viadeo_submitter_status = tep_db_prepare_input($_POST['viadeo_plugin_status']);
   $viadeo_app_key     = tep_db_prepare_input($_POST['viadeo_app_key']);
   $viadeo_app_secret  = tep_db_prepare_input($_POST['viadeo_app_secret']);
   $j_module_status      = tep_db_prepare_input($_POST['j_module_status']);
   $r_module_status      = tep_db_prepare_input($_POST['r_module_status']);
   
   $sql_data_array=array('configuration_value'=>$viadeo_submitter_status,'updated'=>'now()');
   tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_VIADEO_PLUGIN'");

   $sql_data_array=array('configuration_value'=>$j_module_status,'updated'=>'now()');
   tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_VIADEO_PLUGIN_JOBSEEKER'");

   $sql_data_array=array('configuration_value'=>$r_module_status,'updated'=>'now()');
   tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_VIADEO_PLUGIN_RECRUITER'");

   $sql_data_array=array('configuration_value'=>$viadeo_app_key,'updated'=>'now()');
   tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_VIADEO_PLUGIN_APP_KEY'");

   $sql_data_array=array('configuration_value'=>encode_string('app##@##'.$viadeo_app_secret.'##@##passw'),'updated'=>'now()');
   tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_VIADEO_PLUGIN_APP_SECRET_KEY'");
   ///////////////////////
 

   $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
   tep_redirect(FILENAME_ADMIN1_VIADEO_PLUGIN);   
  break;
 }
}

$viadeo_submitter_status = MODULE_VIADEO_PLUGIN;
$j_module_status           = MODULE_VIADEO_PLUGIN_JOBSEEKER;
$r_module_status           = MODULE_VIADEO_PLUGIN_RECRUITER;
$viadeo_submitter_status1= 'ENABLED';
if(MODULE_VIADEO_PLUGIN!='enable')
{
 $viadeo_submitter_status = 'disable';
 $viadeo_submitter_status1= 'DISABLED';
}

$viadeo_status_array=array();
$viadeo_status_array[]=array('id'=>'enable','text'=>'Enabled');
$viadeo_status_array[]=array('id'=>'disable','text'=>'Disabled');

$javascript_url=HOST_NAME_MAIN;//tep_href_link('');
$url=parse_url($javascript_url);
if(preg_match('/^www\./i', $url['host'], $match))
$javascript_url.=' , '.$url['scheme'].'://'.substr($url['host'],4).$url['path'];
elseif(!preg_match('/^[1-9]/i', $url['host'], $match))
$javascript_url.=' , '.$url['scheme'].'://www.'.$url['host'].$url['path'];

$template->assign_vars(array(
 'HEADING_TITLE'=>HEADING_TITLE,
 'VIADEO_PLUGIN_STATUS'=>$viadeo_submitter_status1,
 'viadeo_form' => tep_draw_form('viadeo_form',PATH_TO_ADMIN.FILENAME_ADMIN1_VIADEO_PLUGIN,'', 'post','onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action','update'),
 'INFO_TEXT_VIADEO_INTEGRATION_URL' => tep_href_link(''),
 'INFO_TEXT_VIADEO_R_INTEGRATION_URL' => tep_href_link(''),
 'INFO_TEXT_VIADEO_PLUGIN'     => INFO_TEXT_VIADEO_PLUGIN,
 'INFO_TEXT_VIADEO_PLUGIN1'    => tep_draw_pull_down_menu('viadeo_plugin_status', $viadeo_status_array, $viadeo_submitter_status,''),
 'INFO_TEXT_VIADEO_PLUGIN_KEY' => INFO_TEXT_VIADEO_PLUGIN_KEY,
 'INFO_TEXT_VIADEO_PLUGIN_KEY1'=> tep_draw_input_field('viadeo_app_key',$viadeo_app_key,'size="40"'),
 'INFO_TEXT_VIADEO_PLUGIN_SECRET' => INFO_TEXT_VIADEO_PLUGIN_SECRET,
 'INFO_TEXT_VIADEO_PLUGIN_SECRET1'=> tep_draw_input_field('viadeo_app_secret',$viadeo_app_secret,'size="60"'),
 'TEXT_INFO_J_MODULE_STATUS'     => TEXT_INFO_J_MODULE_STATUS, 
 'TEXT_INFO_J_MODULE_STATUS1'    => tep_draw_radio_field("j_module_status", 'enable',true,$j_module_status,'id="status_active_j"').'&nbsp; <label for="status_active_j" >Active </label>&nbsp;'.tep_draw_radio_field("j_module_status", 'disable',false,$j_module_status,'id="status_inactive_j"').'&nbsp;<label for="status_inactive_j" >Inactive</label>', 
 'TEXT_INFO_R_MODULE_STATUS'     => TEXT_INFO_R_MODULE_STATUS, 
 'TEXT_INFO_R_MODULE_STATUS1'    => tep_draw_radio_field("r_module_status", 'enable',true,$r_module_status,'id="status_active_r"').'&nbsp; <label for="status_active_r" >Active </label>&nbsp;'.tep_draw_radio_field("r_module_status", 'disable',false,$r_module_status,'id="status_inactive_r"').'&nbsp;<label for="status_inactive_r" >Inactive</label>', 

 'button' => tep_image_submit(PATH_TO_BUTTON.'button_update.gif',IMAGE_UPDATE),
 'update_message'=>$messageStack->output()));
$template->pparse('viadeo_plugin');
?>