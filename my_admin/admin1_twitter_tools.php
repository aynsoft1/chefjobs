<?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2009  #**********
***********************************************************
*/
include_once("../include_files.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_TWITTER_TOOLS);
$template->set_filenames(array('twitter_tools' => 'admin1_twitter_tools.htm'));
include_once(FILENAME_ADMIN_BODY);
$twitter_user_id       = MODULE_TWITTER_SUBMITTER_USER_ID;
$twitter_consumer_key  = MODULE_TWITTER_SUBMITTER_OAUTH_CONSUMER_KEY; 
$twitter_access_token  = MODULE_TWITTER_SUBMITTER_OAUTH_TOKEN; 
$twitter_consumer_secret = check_data1(MODULE_TWITTER_SUBMITTER_APP_CONSUMER_SECRET,'##@##','consumer','passw');
if($twitter_consumer_secret==-1)
$twitter_consumer_secret= '';

$twitter_access_token_secret= check_data1(MODULE_TWITTER_SUBMITTER_OAUTH_TOKEN_SECRET,'##@##','token','secret');
if($twitter_access_token_secret==-1)
$twitter_access_token_secret='';

$bitly_user_id         = MODULE_BITLY_USER_ID;
$bitly_api_key         = check_data1(MODULE_BITLY_API_KEY,'##@##','api','key');
if($bitly_api_key==-1)
$bitly_api_key         = '';
//////////////
$action = (isset($_POST['action']) ? $_POST['action'] : '');
if (tep_not_null($action)) 
{
 switch ($action) 
	{
  case 'test_authentication':
   $twitter_user_id             = tep_db_prepare_input($_POST['twitter_user_id']);
   $twitter_consumer_key        = tep_db_prepare_input($_POST['twitter_consumer_key']);
   $twitter_consumer_secret     = tep_db_prepare_input($_POST['twitter_consumer_secret']);
   $twitter_access_token        = tep_db_prepare_input($_POST['twitter_access_token']);
   $twitter_access_token_secret = tep_db_prepare_input($_POST['twitter_access_token_secret']);
   $twitter_submitter_status    = tep_db_prepare_input($_POST['twitter_submitter_status']);
   ini_set('max_execution_time','0');
   include_once("../class/twitter.php");
   $twitter_obj = new twitter;
   if($twitter_obj ->twitter_check_authentication($twitter_consumer_key,$twitter_consumer_secret,$twitter_access_token,$twitter_access_token_secret))
   {
    $messageStack->add(MESSAGE_SUCCESS_AUTHENTICATION, 'success');
   }
   else
    $messageStack->add($twitter_obj->message, 'error'); 
  break;
  case 'update':
   $twitter_submitter_status = tep_db_prepare_input($_POST['twitter_submitter_status']);
   $twitter_user_id          = tep_db_prepare_input($_POST['twitter_user_id']);
   $twitter_consumer_key     = tep_db_prepare_input($_POST['twitter_consumer_key']);
   $twitter_consumer_secret  = tep_db_prepare_input($_POST['twitter_consumer_secret']);
   $twitter_access_token     = tep_db_prepare_input($_POST['twitter_access_token']);
   $twitter_access_token_secret   = tep_db_prepare_input($_POST['twitter_access_token_secret']);   
   $bitly_status           = tep_db_prepare_input($_POST['bitly_status']);
   $bitly_user_id          = tep_db_prepare_input($_POST['bitly_user_id']);
   $bitly_api_key          = tep_db_prepare_input($_POST['bitly_api_key']);
   $j_module_status        = tep_db_prepare_input($_POST['j_module_status']);
   $r_module_status         = tep_db_prepare_input($_POST['r_module_status']);

   ini_set('max_execution_time','0');
   include_once("../class/twitter.php");
   $twitter_obj = new twitter;
   if($twitter_obj ->twitter_check_authentication($twitter_consumer_key,$twitter_consumer_secret,$twitter_access_token,$twitter_access_token_secret))
   {
    $sql_data_array=array('configuration_value'=>$twitter_obj->message,'updated'=>'now()');
    tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_TWITTER_SUBMITTER_USER_ID'");
   }
   else
   {
    $sql_data_array=array('configuration_value'=>'','updated'=>'now()');
    tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_TWITTER_SUBMITTER_USER_ID'");
   }
   
   $sql_data_array=array('configuration_value'=>$twitter_submitter_status,'updated'=>'now()');
   tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_TWITTER_SUBMITTER'");

   $sql_data_array=array('configuration_value'=>$j_module_status,'updated'=>'now()');
   tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_TWITTER_PLUGIN_JOBSEEKER'");

   $sql_data_array=array('configuration_value'=>$r_module_status,'updated'=>'now()');
   tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_TWITTER_PLUGIN_RECRUITER'");


   $sql_data_array=array('configuration_value'=>$twitter_consumer_key,'updated'=>'now()');
   tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_TWITTER_SUBMITTER_OAUTH_CONSUMER_KEY'");

   $sql_data_array=array('configuration_value'=>encode_string('consumer##@##'.$twitter_consumer_secret.'##@##passw'),'updated'=>'now()');
   tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_TWITTER_SUBMITTER_APP_CONSUMER_SECRET'");

   $sql_data_array=array('configuration_value'=>$twitter_access_token,'updated'=>'now()');
   tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_TWITTER_SUBMITTER_OAUTH_TOKEN'");

   $sql_data_array=array('configuration_value'=>encode_string('token##@##'.$twitter_access_token_secret.'##@##secret'),'updated'=>'now()');
   tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_TWITTER_SUBMITTER_OAUTH_TOKEN_SECRET'");

   ///////////////////////
   $sql_data_array=array('configuration_value'=>$bitly_status,'updated'=>'now()');
   tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_BITLY_STATUS'");

   $sql_data_array=array('configuration_value'=>$bitly_user_id,'updated'=>'now()');
   tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_BITLY_USER_ID'");
   $sql_data_array=array('configuration_value'=>encode_string('api##@##'.$bitly_api_key.'##@##key'),'updated'=>'now()');
   tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_BITLY_API_KEY'");

   $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
   tep_redirect(FILENAME_ADMIN1_TWITTER_TOOLS);   
  break;
 }
}

$twitter_submitter_status = MODULE_TWITTER_SUBMITTER;
$twitter_submitter_status1= 'ENABLED';
if(MODULE_TWITTER_SUBMITTER!='enable')
{
 $twitter_submitter_status = 'disable';
 $twitter_submitter_status1= 'DISABLED';
}
$j_module_status           = MODULE_TWITTER_PLUGIN_JOBSEEKER;
$r_module_status           = MODULE_TWITTER_PLUGIN_RECRUITER;
$bitly_module_status = MODULE_BITLY_STATUS;

$twitter_status_array=array();
$twitter_status_array[]=array('id'=>'enable','text'=>'Enabled');
$twitter_status_array[]=array('id'=>'disable','text'=>'Disabled');

$template->assign_vars(array(
 'HEADING_TITLE'=>HEADING_TITLE,
 'TWITTER_SUBMITTER_STATUS'=>$twitter_submitter_status1,
 'twitter_form' => tep_draw_form('twitter_form',PATH_TO_ADMIN.FILENAME_ADMIN1_TWITTER_TOOLS,'', 'post','onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action','update'),
 'INFO_TEXT_TWITTER_CALLBACK_URL' => tep_href_link(''),
 'INFO_TEXT_TWITTER_SUBMITTER'    => INFO_TEXT_TWITTER_SUBMITTER,
 'INFO_TEXT_TWITTER_SUBMITTER1'   => tep_draw_pull_down_menu('twitter_submitter_status', $twitter_status_array, $twitter_submitter_status,'class="form-control form-control-sm"'),
 'INFO_TEXT_TWITTER_SUBMITTER_ID' => INFO_TEXT_TWITTER_SUBMITTER_ID,
 'INFO_TEXT_TWITTER_SUBMITTER_ID1'=> tep_db_output($twitter_user_id),

 'INFO_TEXT_TWITTER_CONSUMER_KEY' => INFO_TEXT_TWITTER_CONSUMER_KEY,
 'INFO_TEXT_TWITTER_CONSUMER_KEY1'=> tep_draw_input_field('twitter_consumer_key',$twitter_consumer_key,'class="form-control form-control-sm"'),
 'INFO_TEXT_TWITTER_CONSUMER_SECRET' => INFO_TEXT_TWITTER_CONSUMER_SECRET,
 'INFO_TEXT_TWITTER_CONSUMER_SECRET1'=> tep_draw_input_field('twitter_consumer_secret',$twitter_consumer_secret,'class="form-control form-control-sm"'),
 'INFO_TEXT_TWITTER_ACCESS_TOKEN' => INFO_TEXT_TWITTER_ACCESS_TOKEN,
 'INFO_TEXT_TWITTER_ACCESS_TOKEN1'=> tep_draw_input_field('twitter_access_token',$twitter_access_token,'class="form-control form-control-sm"'),
 'INFO_TEXT_TWITTER_TOKEN_SECRET' => INFO_TEXT_TWITTER_TOKEN_SECRET,
 'INFO_TEXT_TWITTER_TOKEN_SECRET1'=> tep_draw_input_field('twitter_access_token_secret',$twitter_access_token_secret,'class="form-control form-control-sm"'),

 'TEXT_INFO_J_MODULE_STATUS'     => TEXT_INFO_J_MODULE_STATUS, 
 'TEXT_INFO_J_MODULE_STATUS1'    => tep_draw_radio_field("j_module_status", 'enable',true,$j_module_status,'id="status_active_j"').'&nbsp; <label for="status_active_j" >Active </label>&nbsp;'.tep_draw_radio_field("j_module_status", 'disable',false,$j_module_status,'id="status_inactive_j"').'&nbsp;<label for="status_inactive_j" >Inactive</label>', 
 'TEXT_INFO_R_MODULE_STATUS'     => TEXT_INFO_R_MODULE_STATUS, 
 'TEXT_INFO_R_MODULE_STATUS1'    => tep_draw_radio_field("r_module_status", 'enable',true,$r_module_status,'id="status_active_r"').'&nbsp; <label for="status_active_r" >Active </label>&nbsp;'.tep_draw_radio_field("r_module_status", 'disable',false,$r_module_status,'id="status_inactive_r"').'&nbsp;<label for="status_inactive_r" >Inactive</label>', 


 'INFO_TEXT_BITLY_STATUS'         => INFO_TEXT_BITLY_STATUS,
 'INFO_TEXT_BITLY_STATUS1'        => tep_draw_pull_down_menu('bitly_status', $twitter_status_array, $bitly_module_status,'class="form-control form-control-sm"'),
 'INFO_TEXT_BITLY_USER_ID'        => INFO_TEXT_BITLY_USER_ID,
 'INFO_TEXT_BITLY_USER_ID1'       => tep_draw_input_field('bitly_user_id',$bitly_user_id,'class="form-control form-control-sm"'),
 'INFO_TEXT_BITLY_API_KEY'        => INFO_TEXT_BITLY_API_KEY,
 'INFO_TEXT_BITLY_API_KEY1'       => tep_draw_input_field('bitly_api_key',$bitly_api_key,'class="form-control form-control-sm"'),
 'authentication_link' => '<a  href="#" onclick="test_authentication();" class="blue">'.IMAGE_TEST_AUTHENTICATION.'</a>',
 'button' => tep_draw_submit_button_field('',IMAGE_UPDATE,'class="btn btn-primary"'),
 'update_message'=>$messageStack->output()));
$template->pparse('twitter_tools');
?>