<?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2012  #**********
***********************************************************
*///ini_set('error_reporting','0');
//ini_set('display_errors','0');
ini_set('error_reporting',E_ALL ^ E_NOTICE);
ini_set('display_errors','1');

include_once("../include_files.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_LINKEDIN_PLUGIN);
$template->set_filenames(array('linkedin_plugin' => 'admin1_linkedin_plugin.htm'));
include_once(FILENAME_ADMIN_BODY);
$linkedin_app_key  = MODULE_LINKEDIN_PLUGIN_APP_KEY; 
$linkedin_app_secret = check_data1(MODULE_LINKEDIN_PLUGIN_APP_SECRET_KEY,'##@##','app','passw');
if($linkedin_app_secret==-1)
$linkedin_app_secret= '';

//////////////
$action = (isset($_POST['action']) ? $_POST['action'] : '');
if (tep_not_null($action)) 
{
 switch ($action) 
	{
  case 'test_authentication':
   $linkedin_app_key        = tep_db_prepare_input($_POST['linkedin_app_key']);
   $linkedin_app_secret     = tep_db_prepare_input($_POST['linkedin_app_secret']);
   $linkedin_submitter_status    = tep_db_prepare_input($_POST['linkedin_plugin_status']);
   ini_set('max_execution_time','0');
   include_once("../class/linkedin.php");
   $callback_url=tep_href_link(FILENAME_LINKEDIN_APPLICATION);

   $linkedin_obj = new LinkedIn($linkedin_app_key,$linkedin_app_secret,$callback_url);
   if($linkedin_obj ->checkAppKey())
   {
    $messageStack->add(MESSAGE_SUCCESS_AUTHENTICATION, 'success');
   }
   else
    $messageStack->add(MESSAGE_ERROR_AUTHENTICATION, 'error'); 
  break;
  case 'update':
   $linkedin_submitter_status = tep_db_prepare_input($_POST['linkedin_plugin_status']);
   $linkedin_app_key     = tep_db_prepare_input($_POST['linkedin_app_key']);
   $linkedin_app_secret  = tep_db_prepare_input($_POST['linkedin_app_secret']);
   $j_module_status      = tep_db_prepare_input($_POST['j_module_status']);
   $r_module_status      = tep_db_prepare_input($_POST['r_module_status']);
   
   $sql_data_array=array('configuration_value'=>$linkedin_submitter_status,'updated'=>'now()');
   tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_LINKEDIN_PLUGIN'");

   $sql_data_array=array('configuration_value'=>$j_module_status,'updated'=>'now()');
   tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_LINKEDIN_PLUGIN_JOBSEEKER'");

   $sql_data_array=array('configuration_value'=>$r_module_status,'updated'=>'now()');
   tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_LINKEDIN_PLUGIN_RECRUITER'");

   $sql_data_array=array('configuration_value'=>$linkedin_app_key,'updated'=>'now()');
   tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_LINKEDIN_PLUGIN_APP_KEY'");

   $sql_data_array=array('configuration_value'=>encode_string('app##@##'.$linkedin_app_secret.'##@##passw'),'updated'=>'now()');
   tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_LINKEDIN_PLUGIN_APP_SECRET_KEY'");
   ///////////////////////
 

   $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
   tep_redirect(FILENAME_ADMIN1_LINKEDIN_PLUGIN);   
  break;
 }
}

$linkedin_submitter_status = MODULE_LINKEDIN_PLUGIN;
$j_module_status           = MODULE_LINKEDIN_PLUGIN_JOBSEEKER;
$r_module_status           = MODULE_LINKEDIN_PLUGIN_RECRUITER;
$linkedin_submitter_status1= 'ENABLED';
if(MODULE_LINKEDIN_PLUGIN!='enable')
{
 $linkedin_submitter_status = 'disable';
 $linkedin_submitter_status1= 'DISABLED';
}

$linkedin_status_array=array();
$linkedin_status_array[]=array('id'=>'enable','text'=>'Enabled');
$linkedin_status_array[]=array('id'=>'disable','text'=>'Disabled');

$domain_url=HOST_NAME_MAIN;//tep_href_link('');
$url=parse_url($domain_url);
if(preg_match('/^www\./i', $url['host'], $match))
$domain_url.=' , '.$url['scheme'].'://'.substr($url['host'],4).$url['path'];
elseif(!preg_match('/^[1-9]/i', $url['host'], $match))
$domain_url.=' , '.$url['scheme'].'://www.'.$url['host'].$url['path'];

$redirect_url=tep_href_link(FILENAME_LINKEDIN_APPLICATION);
$redirect_ur11 ='<li>'.$redirect_url.'</li>';

$url=parse_url($redirect_url);
if(preg_match('/^www\./i', $url['host'], $match))
$redirect_url=$url['scheme'].'://'.substr($url['host'],4).$url['path'];
elseif(!preg_match('/^[1-9]/i', $url['host'], $match))
$redirect_url=$url['scheme'].'://www.'.$url['host'].$url['path'];

$redirect_ur11.='<li>'.$redirect_url.'</li>';
$template->assign_vars(array(
 'HEADING_TITLE'=>HEADING_TITLE,
 'LINKEDIN_PLUGIN_STATUS'=>$linkedin_submitter_status1,
 'linkedin_form' => tep_draw_form('linkedin_form',PATH_TO_ADMIN.FILENAME_ADMIN1_LINKEDIN_PLUGIN,'', 'post','onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action','update'),
 'INFO_TEXT_LINKEDIN_REDIRECT_URL' => $redirect_ur11,
 'INFO_TEXT_LINKEDIN_DOMAIN_URL' => $domain_url,
 'INFO_TEXT_LINKEDIN_PLUGIN'     => INFO_TEXT_LINKEDIN_PLUGIN,
 'INFO_TEXT_LINKEDIN_PLUGIN1'    => tep_draw_pull_down_menu('linkedin_plugin_status', $linkedin_status_array, $linkedin_submitter_status,'class="form-control form-control-sm"'),
 'INFO_TEXT_LINKEDIN_PLUGIN_KEY' => INFO_TEXT_LINKEDIN_PLUGIN_KEY,
 'INFO_TEXT_LINKEDIN_PLUGIN_KEY1'=> tep_draw_input_field('linkedin_app_key',$linkedin_app_key,'class="form-control form-control-sm"'),
 'INFO_TEXT_LINKEDIN_PLUGIN_SECRET' => INFO_TEXT_LINKEDIN_PLUGIN_SECRET,
 'INFO_TEXT_LINKEDIN_PLUGIN_SECRET1'=> tep_draw_input_field('linkedin_app_secret',$linkedin_app_secret,'class="form-control form-control-sm"'),
 'TEXT_INFO_J_MODULE_STATUS'     => TEXT_INFO_J_MODULE_STATUS, 
 'TEXT_INFO_J_MODULE_STATUS1'    => tep_draw_radio_field("j_module_status", 'enable',true,$j_module_status,'id="status_active_j"').'&nbsp; <label for="status_active_j" >Active </label>&nbsp;'.tep_draw_radio_field("j_module_status", 'disable',false,$j_module_status,'id="status_inactive_j"').'&nbsp;<label for="status_inactive_j" >Inactive</label>', 
 'TEXT_INFO_R_MODULE_STATUS'     => TEXT_INFO_R_MODULE_STATUS, 
 'TEXT_INFO_R_MODULE_STATUS1'    => tep_draw_radio_field("r_module_status", 'enable',true,$r_module_status,'id="status_active_r"').'&nbsp; <label for="status_active_r" >Active </label>&nbsp;'.tep_draw_radio_field("r_module_status", 'disable',false,$r_module_status,'id="status_inactive_r"').'&nbsp;<label for="status_inactive_r" >Inactive</label>', 

// 'authentication_link' => '<a  href="#" onclick="test_authentication();" class="blue">'.IMAGE_TEST_AUTHENTICATION.'</a>',
 'button' =>  tep_draw_submit_button_field('',IMAGE_UPDATE,'class="btn btn-primary"'),
 'update_message'=>$messageStack->output()));
$template->pparse('linkedin_plugin');
?>