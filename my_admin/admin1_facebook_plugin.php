<?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2012  #**********
***********************************************************
*/
include_once("../include_files.php");
include_once "../class/facebook_post.php";
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_FACEBOOK_PLUGIN);
$template->set_filenames(array('facebook_plugin' => 'admin1_facebook_plugin.htm'));
include_once(FILENAME_ADMIN_BODY);
$facebook_app_key  = MODULE_FACEBOOK_PLUGIN_APP_KEY; 
$facebook_page     = MODULE_FACEBOOK_PLUGIN_SUBMITTER_URL; 
$facebook_page_url = MODULE_FACEBOOK_PLUGIN_SUBMITTER_URL; 
$facebook_page_id  = MODULE_FACEBOOK_PLUGIN_SUBMITTER_ID; 
$facebook_app_secret = check_data1(MODULE_FACEBOOK_PLUGIN_APP_SECRET_KEY,'##@##','app','passw');
$facebook_app_auth = check_data1(MODULE_FACEBOOK_PLUGIN_APP_AUTH,'####','app','key');
if($facebook_app_secret==-1)
$facebook_app_secret= '';
$calback_url =  tep_href_link(FILENAME_FACEBOOK_APPLICATION);

//////////////
$action = (isset($_POST['action']) ? $_POST['action'] : '');

$facebook_response   = tep_db_prepare_input($_GET['page_found']);

if($facebook_response=='faild')
{
 $messageStack->add_session(ERROR_PAGE_AUTH_FACEBOOK_URL, 'error'); 
 tep_redirect(FILENAME_ADMIN1_FACEBOOK_PLUGIN);   
}
elseif(isset($facebook_response))
{
	if(!isset($_SESSION['access_token']))
	{
  tep_redirect(FILENAME_ADMIN1_FACEBOOK_PLUGIN);   
	}
 $access_token=$_SESSION['access_token'];
 //unset($_SESSION['access_token']);
	$access_token1= json_decode($access_token,true);
	$new_token = array();
	$new_token['access_token']= $access_token1['access_token'];
 if(isset($access_token1['expires']))
	{
 	$new_token['expired']= date('Y-m-d',time()+$access_token1['expires']);
	}
	$access_token1= json_encode($new_token);
	$sql_data_array=array('configuration_value'=>encode_string('app####'.$access_token1.'####key'),'updated'=>'now()');
 tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_FACEBOOK_PLUGIN_APP_AUTH'");   
	$messageStack->add_session(MESSAGE_SUCCESS_AUTHENTICATION, 'success'); 
 tep_redirect(FILENAME_ADMIN1_FACEBOOK_PLUGIN);   
}
if (tep_not_null($action)) 
{
 switch ($action) 
 {
  case 'page_authentication':
   $facebook_app_key     = tep_db_prepare_input($_POST['facebook_app_key']);
   $facebook_app_secret  = tep_db_prepare_input($_POST['facebook_app_secret']);
   $facebook_page        = tep_db_prepare_input($_POST['facebook_page']);
   $page_id='';
   $error =false;
   include_once "../class/facebook.php";
   if($page_id=='')
   {
    $error = true;
    $messageStack->add(ERROR_INVALID_FACEBOOK_URL, 'error'); 
   } 

   if($facebook_page_url!=$facebook_page && $facebook_page !='')
   {
    $connection = new FacebookOAuth($facebook_app_key,$facebook_app_secret,$calback_url);
    $scope='manage_pages,publish_pages,publish_actions';
    $state=md5(uniqid(rand(), TRUE));
    $state=$state.'_admin';
    $_SESSION['state']=$state;
    $_SESSION['facebook_page']=$facebook_page;
    $connection->state=$state;
    $url=$connection->createAuthUrl($scope);
    tep_redirect($url);  
	}
     /*
     $info = new Facebook($facebook_page);
     if($facebook_info=$info->getFacebookInfo())
	 {
	  if(isset($facebook_info['category']))
	  {
		$page_id = 'page:'.$facebook_info['id'];
	  }
	  else
	  $page_id = 'profile:'.$facebook_info['id'];
	 }
	
    
	 if($facebook_page_id!=$page_id)
	 {
  	  $sql_data_array=array('configuration_value'=>$page_id,'updated'=>'now()');
      tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_FACEBOOK_PLUGIN_SUBMITTER_ID'");
  	  $sql_data_array=array('configuration_value'=>encode_string('app####'.''.'####key'),'updated'=>'now()');
      tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_FACEBOOK_PLUGIN_APP_AUTH'");   
	  $sql_data_array=array('configuration_value'=>$facebook_page,'updated'=>'now()');
      tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_FACEBOOK_PLUGIN_SUBMITTER_URL'");
	 }
     if(!$error)
	 {
      $connection = new FacebookOAuth($facebook_app_key,$facebook_app_secret,$calback_url);
				if(isset($facebook_info['category']))
    $scope='manage_pages,publish_stream';
				else
    $scope='publish_stream';
    $state=md5(uniqid(rand(), TRUE));
    $state=$state.'_admin';
		  $_SESSION['state']=$state;

			 $connection->state=$state;
    $url=$connection->createAuthUrl($scope);
    tep_redirect($url);  
			} */
   
   
   break; 
  case 'test_authentication':
   $facebook_app_key     = tep_db_prepare_input($_POST['facebook_app_key']);
   $facebook_app_secret  = tep_db_prepare_input($_POST['facebook_app_secret']);
   $connection = new FacebookOAuth($facebook_app_key,$facebook_app_secret,'');
   if(!$connection ->checkAppKey())
   {
    $messageStack->add($connection ->errorMessage, 'error'); 
   }
   else
    $messageStack->add(MESSAGE_SUCCESS_AUTHENTICATION, 'success');
   break; 
  case 'update':
   $facebook_plugin_status = tep_db_prepare_input($_POST['facebook_plugin_status']);
   $facebook_app_key     = tep_db_prepare_input($_POST['facebook_app_key']);
   $facebook_app_secret  = tep_db_prepare_input($_POST['facebook_app_secret']);
   $j_module_status      = tep_db_prepare_input($_POST['j_module_status']);
   $r_module_status      = tep_db_prepare_input($_POST['r_module_status']);
   $job_module_status    = tep_db_prepare_input($_POST['job_module_status']);
   $facebook_page        = tep_db_prepare_input($_POST['facebook_page']);
   $page_id='';
   if($facebook_page!='')
   {
    include_once "../class/facebook.php";
    $info = new Facebook($facebook_page);
	$facebook_app_auth;
	$access_token = json_decode($facebook_app_auth,true);
	if($facebook_info=$info->getFacebookInfo($facebook_page,$access_token['access_token']))
	{
     /*
	 if(isset($facebook_info['category']))
	 {
	  $page_id = 'page:'.$facebook_info['id'];
	 }
	 else
	 $page_id = 'profile:'.$facebook_info['id'];
	 */
     $page_id = $facebook_info['id'];
	 if($facebook_page_id!=$page_id)
	 {
  	  $sql_data_array=array('configuration_value'=>$page_id,'updated'=>'now()');
      tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_FACEBOOK_PLUGIN_SUBMITTER_ID'");
  	  $sql_data_array=array('configuration_value'=>encode_string('app####'.''.'####key'),'updated'=>'now()');
      tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_FACEBOOK_PLUGIN_APP_AUTH'");   
	  }
	 }
	}
	if($page_id=='')
	{
	 $sql_data_array=array('configuration_value'=>'','updated'=>'now()');
     tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_FACEBOOK_PLUGIN_SUBMITTER_ID'");
	 $sql_data_array=array('configuration_value'=>encode_string('app####'.''.'####key'),'updated'=>'now()');
     tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_FACEBOOK_PLUGIN_APP_AUTH'");   
 	 if($facebook_page!='')
     $messageStack->add_session(ERROR_INVALID_FACEBOOK_URL, 'error'); 

	 $sql_data_array=array('configuration_value'=>'','updated'=>'now()');
     tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_FACEBOOK_PLUGIN_SUBMITTER_URL'");
	}
	else
	{
 	 $sql_data_array=array('configuration_value'=>$facebook_page,'updated'=>'now()');
     tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_FACEBOOK_PLUGIN_SUBMITTER_URL'");
	}
   
   $sql_data_array=array('configuration_value'=>$facebook_plugin_status,'updated'=>'now()');
   tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_FACEBOOK_PLUGIN'");

   $sql_data_array=array('configuration_value'=>$j_module_status,'updated'=>'now()');
   tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_FACEBOOK_PLUGIN_JOBSEEKER'");

   $sql_data_array=array('configuration_value'=>$r_module_status,'updated'=>'now()');
   tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_FACEBOOK_PLUGIN_RECRUITER'");

   $sql_data_array=array('configuration_value'=>$job_module_status,'updated'=>'now()');
   tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_FACEBOOK_PLUGIN_JOB_SUBMITTER'");


   $sql_data_array=array('configuration_value'=>$facebook_app_key,'updated'=>'now()');
   tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_FACEBOOK_PLUGIN_APP_KEY'");

   $sql_data_array=array('configuration_value'=>encode_string('app##@##'.$facebook_app_secret.'##@##passw'),'updated'=>'now()');
   tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "configuration_name= 'MODULE_FACEBOOK_PLUGIN_APP_SECRET_KEY'");
   ///////////////////////
   $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
   tep_redirect(FILENAME_ADMIN1_FACEBOOK_PLUGIN);   
  break;
 }
}

$facebook_plugin_status = MODULE_FACEBOOK_PLUGIN;
$j_module_status           = MODULE_FACEBOOK_PLUGIN_JOBSEEKER;
$r_module_status           = MODULE_FACEBOOK_PLUGIN_RECRUITER;
$job_module_status         = MODULE_FACEBOOK_PLUGIN_JOB_SUBMITTER;
$facebook_plugin_status1= 'ENABLED';
if(MODULE_FACEBOOK_PLUGIN!='enable')
{
 $facebook_plugin_status = 'disable';
 $facebook_plugin_status1= 'DISABLED';
}

$facebook_status_array=array();
$facebook_status_array[]=array('id'=>'enable','text'=>'Enabled');
$facebook_status_array[]=array('id'=>'disable','text'=>'Disabled');

$authentication_page='';
if($facebook_app_auth =='' && $facebook_page_id !='' && $facebook_app_key!='' && $facebook_app_secret!='')
{
 $authentication_page='<a  href="#" onclick="page_authentication();" class="link_blink">'.INFO_PAGE_AUTHENTICATION.'</a>';
}

$template->assign_vars(array(
 'HEADING_TITLE'=>HEADING_TITLE,
 'FACEBOOK_PLUGIN_STATUS'=>$facebook_plugin_status1,
 'facebook_form' => tep_draw_form('facebook_form',PATH_TO_ADMIN.FILENAME_ADMIN1_FACEBOOK_PLUGIN,'', 'post','onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action','update'),
 'INFO_TEXT_FACEBOOK_CANVAS_URL'  => tep_href_link(''),
 'INFO_TEXT_FACEBOOK_WEBSITE_URL' => tep_href_link(''),
 'INFO_TEXT_FACEBOOK_APP_DOMAIN'  => tep_db_output($_SERVER["HTTP_HOST"]),
 'INFO_TEXT_FACEBOOK_PLUGIN'        => INFO_TEXT_FACEBOOK_PLUGIN,
 'INFO_TEXT_FACEBOOK_PLUGIN1'       => tep_draw_pull_down_menu('facebook_plugin_status', $facebook_status_array, $facebook_plugin_status,'class="form-control form-control-sm"'),
 'INFO_TEXT_FACEBOOK_PLUGIN_KEY'    => INFO_TEXT_FACEBOOK_PLUGIN_KEY,
 'INFO_TEXT_FACEBOOK_PLUGIN_KEY1'   => tep_draw_input_field('facebook_app_key',$facebook_app_key,'class="form-control form-control-sm"'),
 'INFO_TEXT_FACEBOOK_PLUGIN_SECRET' => INFO_TEXT_FACEBOOK_PLUGIN_SECRET,
 'INFO_TEXT_FACEBOOK_PLUGIN_SECRET1'=> tep_draw_input_field('facebook_app_secret',$facebook_app_secret,'class="form-control form-control-sm"'),
 'TEXT_INFO_J_MODULE_STATUS'     => TEXT_INFO_J_MODULE_STATUS, 
 'TEXT_INFO_J_MODULE_STATUS1'    => tep_draw_radio_field("j_module_status", 'enable',true,$j_module_status,'id="status_active_j"').'&nbsp; <label for="status_active_j" >Active </label>&nbsp;'.tep_draw_radio_field("j_module_status", 'disable',false,$j_module_status,'id="status_inactive_j"').'&nbsp;<label for="status_inactive_j" >Inactive</label>', 
 'TEXT_INFO_R_MODULE_STATUS'     => TEXT_INFO_R_MODULE_STATUS, 
 'TEXT_INFO_R_MODULE_STATUS1'    => tep_draw_radio_field("r_module_status", 'enable',true,$r_module_status,'id="status_active_r"').'&nbsp; <label for="status_active_r" >Active </label>&nbsp;'.tep_draw_radio_field("r_module_status", 'disable',false,$r_module_status,'id="status_inactive_r"').'&nbsp;<label for="status_inactive_r" >Inactive</label>', 
 'TEXT_INFO_JOB_MODULE_STATUS'   => TEXT_INFO_JOB_MODULE_STATUS, 
 'TEXT_INFO_JOB_MODULE_STATUS1'  => tep_draw_radio_field("job_module_status", 'enable',true,$job_module_status,'id="status_active_job"').'&nbsp; <label for="status_active_job" >Active </label>&nbsp;'.tep_draw_radio_field("job_module_status", 'disable',false,$job_module_status,'id="status_inactive_job"').'&nbsp;<label for="status_inactive_job" >Inactive</label>', 
 'INFO_TEXT_FACEBOOK_PAGE'       => INFO_TEXT_FACEBOOK_PAGE,
 'INFO_TEXT_FACEBOOK_PAGE1'      => tep_draw_input_field('facebook_page',$facebook_page,'class="form-control form-control-sm" onchange="checkPageLink()"'),
 'INFO_TEXT_FACEBOOK_PAGE_DESC'  => INFO_TEXT_FACEBOOK_PAGE_DESC,
 'linkvalue'                     => $facebook_page,
 'authentication_page'           => $authentication_page,
 'authentication_link' => '<a  href="#" onclick="test_authentication();" class="blue">'.INFO_TEXT_AUTHENTICATION.'</a>',
 'button' => tep_draw_submit_button_field('',IMAGE_UPDATE,'class="btn btn-primary"'),
 'update_message'=>$messageStack->output()));
$template->pparse('facebook_plugin');
?>