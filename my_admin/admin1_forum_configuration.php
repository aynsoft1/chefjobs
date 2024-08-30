<?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2011  #**********
***********************************************************
*/
include_once("../include_files.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_FORUM_CONFIGURATION);
$template->set_filenames(array('configure_forume' => 'admin1_forum_configuration.htm'));
include_once(FILENAME_ADMIN_BODY);

$forum_search_captcha    = MODULE_FORUM_SEARCH_CAPTCHA;
$min_keyword_length      = MODULE_FORUM_SEARCH_MIN_KEYWORD_LENGTH; 
$max_forum_search_result = MODULE_FORUM_SEARCH_MAX_RESULT_DISPLAY; 

//////////////
$action = (isset($_POST['action']) ? $_POST['action'] : '');
if (tep_not_null($action)) 
{
 switch ($action) 
	{
  case 'update':
   $forum_search_captcha    = tep_db_prepare_input($_POST['forum_search_captcha']);
   $min_keyword_length      = tep_db_prepare_input($_POST['IN_min_keyword_length']);
   $max_forum_search_result = tep_db_prepare_input($_POST['IN_max_forum_search_result']);


   $sql_data_array=array('configuration_value'=>$forum_search_captcha,'updated'=>'now()');
   tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_FORUM_SEARCH_CAPTCHA'");

   
   $sql_data_array=array('configuration_value'=>$min_keyword_length);
   tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_FORUM_SEARCH_MIN_KEYWORD_LENGTH'");

   $sql_data_array=array('configuration_value'=>$max_forum_search_result,'updated'=>'now()');
   tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_FORUM_SEARCH_MAX_RESULT_DISPLAY'");


   $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
   tep_redirect(FILENAME_ADMIN1_FORUM_CONFIGURATION);   
  break;
 }
}


$template->assign_vars(array(
 'HEADING_TITLE'=>HEADING_TITLE,
 'configure_form' => tep_draw_form('page',PATH_TO_ADMIN.FILENAME_ADMIN1_FORUM_CONFIGURATION,'', 'post','onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action','update'),
 'INFO_TEXT_MODULE_FORUM_SEARCH_CAPTCHA'   => INFO_TEXT_MODULE_FORUM_SEARCH_CAPTCHA,
 'INFO_TEXT_MODULE_FORUM_SEARCH_CAPTCHA1'  => tep_draw_radio_field("forum_search_captcha", 'enable',true,$forum_search_captcha,'id="forum_search_captcha_enable"').'&nbsp; <label for="forum_search_captcha_enable" >enable</label>&nbsp;'.tep_draw_radio_field("forum_search_captcha", 'disable',false,$forum_search_captcha,'id="forum_search_captcha_disable"').'&nbsp;<label for="forum_search_captcha_disable" >disable</label>',
 'INFO_TEXT_MODULE_FORUM_SEARCH_KAYWORD'   => INFO_TEXT_MODULE_FORUM_SEARCH_KAYWORD,
 'INFO_TEXT_MODULE_FORUM_SEARCH_KAYWORD1'  => tep_draw_input_field('IN_min_keyword_length',$min_keyword_length,'class="form-control form-control-sm"'),
 'INFO_TEXT_MODULE_FORUM_MAXIMUM_RESULT'   => INFO_TEXT_MODULE_FORUM_MAXIMUM_RESULT,
 'INFO_TEXT_MODULE_FORUM_MAXIMUM_RESULT1'  => tep_draw_input_field('IN_max_forum_search_result',$max_forum_search_result,'class="form-control form-control-sm"',true),

//  'button' => tep_image_submit(PATH_TO_BUTTON.'button_update.gif',IMAGE_UPDATE),
 'button' => tep_button_submit('btn btn-primary',IMAGE_UPDATE),
 'update_message'=>$messageStack->output()));
$template->pparse('configure_forume');
?>