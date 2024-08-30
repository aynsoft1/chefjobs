<?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2015  #**********
***********************************************************
*/
include_once("../include_files.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_G_CAPTCHA_PLUGIN);
$template->set_filenames(array('g_recaptcha' => 'admin1_g_recaptcha.htm'));
include_once(FILENAME_ADMIN_BODY);
$g_recaptcha_key  = MODULE_G_RECAPTCHA_PLUGIN_KEY; 

$g_recaptcha_secret = check_data1(MODULE_G_CAPTCHA_PLUGIN_SECRET_KEY,'##@##','gapp','passw');
if($g_recaptcha_secret==-1)
$g_recaptcha_secret= '';
//$calback_url =  tep_href_link(FILENAME_G_CAPTCHA_APPLICATION);

//////////////
$action = (isset($_POST['action']) ? $_POST['action'] : '');


if (tep_not_null($action)) 
{
 switch ($action) 
 {
  case 'update':
   $g_recaptcha_status = tep_db_prepare_input($_POST['g_recaptcha_status']);
   $g_recaptcha_key     = tep_db_prepare_input($_POST['g_recaptcha_key']);
   $g_recaptcha_secret  = tep_db_prepare_input($_POST['g_recaptcha_secret']);
   $j_module_status      = tep_db_prepare_input($_POST['j_module_status']);
   $r_module_status      = tep_db_prepare_input($_POST['r_module_status']);
   $mj_module_status    = tep_db_prepare_input($_POST['mj_module_status']);
   $mr_module_status    = tep_db_prepare_input($_POST['mr_module_status']);
   
   

   $sql_data_array=array('configuration_value'=>$g_recaptcha_status,'updated'=>'now()');
   tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_G_CAPTCHA_PLUGIN'");

   $sql_data_array=array('configuration_value'=>$j_module_status,'updated'=>'now()');
   tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_G_CAPTCHA_WEB_R_JOBSEEKER'");

   $sql_data_array=array('configuration_value'=>$r_module_status,'updated'=>'now()');
   tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_G_CAPTCHA_WEB_R_RECRUITER'");

   $sql_data_array=array('configuration_value'=>$mj_module_status,'updated'=>'now()');
   tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_G_CAPTCHA_MOB_R_JOBSEEKER'");

   $sql_data_array=array('configuration_value'=>$mr_module_status,'updated'=>'now()');
   tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_G_CAPTCHA_MOB_R_RECRUITER'");


   $sql_data_array=array('configuration_value'=>$g_recaptcha_key,'updated'=>'now()');
   tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_G_RECAPTCHA_PLUGIN_KEY'");

   $sql_data_array=array('configuration_value'=>encode_string('gapp##@##'.$g_recaptcha_secret.'##@##passw'),'updated'=>'now()');
   tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_G_CAPTCHA_PLUGIN_SECRET_KEY'");
   ///////////////////////
   $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
   tep_redirect(FILENAME_ADMIN1_G_CAPTCHA_PLUGIN);   
  break;
 }
}

$g_recaptcha_status = MODULE_G_CAPTCHA_PLUGIN;
$j_module_status    = MODULE_G_CAPTCHA_WEB_R_JOBSEEKER;
$r_module_status    = MODULE_G_CAPTCHA_WEB_R_RECRUITER;
$mj_module_status   = MODULE_G_CAPTCHA_MOB_R_JOBSEEKER;
$mr_module_status   = MODULE_G_CAPTCHA_MOB_R_RECRUITER;
$g_recaptcha_status1= 'ENABLED';
if(MODULE_G_CAPTCHA_PLUGIN!='enable')
{
 $g_recaptcha_status = 'disable';
 $g_recaptcha_status1= 'DISABLED';
}

$gcaptcha_status_array=array();
$gcaptcha_status_array[]=array('id'=>'enable','text'=>'Enabled');
$gcaptcha_status_array[]=array('id'=>'disable','text'=>'Disabled');


$template->assign_vars(array(
 'HEADING_TITLE'=>HEADING_TITLE,
 'G_CAPTCHA_PLUGIN_STATUS'=>$g_recaptcha_status1,
 'gcaptcha_form' => tep_draw_form('gcaptcha__form',PATH_TO_ADMIN.FILENAME_ADMIN1_G_CAPTCHA_PLUGIN,'', 'post','onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action','update'),
 'INFO_TEXT_G_CAPTCHA_PLUGIN'        => INFO_TEXT_G_CAPTCHA_PLUGIN,
 'INFO_TEXT_G_CAPTCHA_PLUGIN1'       => tep_draw_pull_down_menu('g_recaptcha_status', $gcaptcha_status_array, $g_recaptcha_status,'class="form-control form-control-sm"'),
 'INFO_TEXT_G_CAPTCHA_PLUGIN_KEY'    => INFO_TEXT_G_CAPTCHA_PLUGIN_KEY,
 'INFO_TEXT_G_CAPTCHA_PLUGIN_KEY1'   => tep_draw_input_field('g_recaptcha_key',$g_recaptcha_key,'class="form-control form-control-sm"'),
 'INFO_TEXT_G_CAPTCHA_PLUGIN_SECRET' => INFO_TEXT_G_CAPTCHA_PLUGIN_SECRET,
 'INFO_TEXT_G_CAPTCHA_PLUGIN_SECRET1'=> tep_draw_input_field('g_recaptcha_secret',$g_recaptcha_secret,'class="form-control form-control-sm"'),
 'TEXT_INFO_J_MODULE_STATUS'     => TEXT_INFO_J_MODULE_STATUS, 
 'TEXT_INFO_J_MODULE_STATUS1'    => tep_draw_radio_field("j_module_status", 'enable',true,$j_module_status,'id="status_active_j"').'&nbsp; <label for="status_active_j" >Active </label>&nbsp;'.tep_draw_radio_field("j_module_status", 'disable',false,$j_module_status,'id="status_inactive_j"').'&nbsp;<label for="status_inactive_j" >Inactive</label>', 
 'TEXT_INFO_R_MODULE_STATUS'     => TEXT_INFO_R_MODULE_STATUS, 
 'TEXT_INFO_R_MODULE_STATUS1'    => tep_draw_radio_field("r_module_status", 'enable',true,$r_module_status,'id="status_active_r"').'&nbsp; <label for="status_active_r" >Active </label>&nbsp;'.tep_draw_radio_field("r_module_status", 'disable',false,$r_module_status,'id="status_inactive_r"').'&nbsp;<label for="status_inactive_r" >Inactive</label>', 
 'TEXT_INFO_MJ_MODULE_STATUS'   => TEXT_INFO_MJ_MODULE_STATUS, 
 'TEXT_INFO_MJ_MODULE_STATUS1'  => tep_draw_radio_field("mj_module_status", 'enable',true,$mj_module_status,'id="status_active_job"').'&nbsp; <label for="status_active_job" >Active </label>&nbsp;'.tep_draw_radio_field("mj_module_status", 'disable',false,$mj_module_status,'id="status_inactive_job"').'&nbsp;<label for="status_inactive_job" >Inactive</label>', 
 'TEXT_INFO_MR_MODULE_STATUS'   => TEXT_INFO_MR_MODULE_STATUS, 
 'TEXT_INFO_MR_MODULE_STATUS1'  => tep_draw_radio_field("mr_module_status", 'enable',true,$mr_module_status,'id="status_active_mr"').'&nbsp; <label for="status_active_mr" >Active </label>&nbsp;'.tep_draw_radio_field("mr_module_status", 'disable',false,$mr_module_status,'id="status_inactive_mr"').'&nbsp;<label for="status_inactive_mr" >Inactive</label>', 
//  'button' => tep_image_submit(PATH_TO_BUTTON.'button_update.gif',IMAGE_UPDATE),
 'button'    => tep_draw_submit_button_field('',IMAGE_UPDATE,'class="btn btn-primary"'),
 'update_message'=>$messageStack->output()));
$template->pparse('g_recaptcha');
?>