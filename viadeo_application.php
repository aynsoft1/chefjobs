<?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft Pvt. Ltd.   #**********
**********# Copyright (c) www.aynsoft.com 2012  #**********
***********************************************************
*/
include_once("include_files.php");
include_once "class/viadeooauth.php";
//ini_set('error_reporting',E_ALL ^ E_NOTICE);
//ini_set('display_errors','1');

$viadeo_app_key  = MODULE_VIADEO_PLUGIN_APP_KEY; 
$viadeo_app_secret = check_data1(MODULE_VIADEO_PLUGIN_APP_SECRET_KEY,'##@##','app','passw');
$calback_url=tep_href_link(FILENAME_VIADEO_APPLICATION);

if($viadeo_app_secret==-1)
$viadeo_app_secret= '';
if($viadeo_app_key=='' || $viadeo_app_secret=='' || MODULE_VIADEO_PLUGIN!='enable')
tep_redirect(FILENAME_INDEX);
if (isset($_GET['error']))
{
 @session_unset();
 @session_destroy();
 tep_redirect(FILENAME_INDEX);    
}
$request = tep_db_prepare_input($_GET['request']);
$state   = tep_db_prepare_input($_GET['state']);
if(tep_not_null($state))
{
 $state1   = explode('_',$state,2);
 $request=$state1[1];
}
switch($request)
{
 case 'jobseeker':
 case 'recruiter':
   if (isset($_GET['code']))
   {
    if($state!=$_SESSION['state'] || $state=='')
    {
     unset($_SESSION['state']);
     die('Invalid Request');
    }
    $code       = tep_db_prepare_input($_GET['code']);
    $connection = new ViadeoOAuth($viadeo_app_key,$viadeo_app_secret,$calback_url);
    $connection ->authenticate($code);
    $_SESSION['access_token'] = $connection->getAccessToken();
    unset($_SESSION['state']);
    if($request=='recruiter')
     tep_redirect(FILENAME_VIADEO_APPLICATION."?request=recruiter_info");
    elseif($request=='admin')
     tep_redirect(FILENAME_VIADEO_APPLICATION."?request=admin_info");
    else
     tep_redirect(FILENAME_VIADEO_APPLICATION."?request=jobseeker_info");
   }
  break;
 case 'recruiter_info':
 case 'jobseeker_info':
   if(!isset($_SESSION['access_token']))
   {
    if($request=='recruiter_info')
     tep_redirect(FILENAME_VIADEO_APPLICATION."?user_type=recruiter");
    else
     tep_redirect(FILENAME_VIADEO_APPLICATION); 
   }
   $access_token1=$_SESSION['access_token'];
   $connection = new  ViadeoOAuth($viadeo_app_key,$viadeo_app_secret,$calback_url);
   $connection->setAccessToken($_SESSION['access_token']);
   $token=$connection->accessToken['access_token'];
   $content = $connection->get('https://api.viadeo.com/me?user_detail=partial&access_token='.$token);
			if(isset($content['error']))
	  {
    @session_unset();
    @session_destroy();
    tep_redirect(FILENAME_INDEX);
			}
			if(!tep_not_null($content['id']))
   {
    @session_unset();
    @session_destroy();
    die('Error : Unable to access Your information.');
   }
   $viadeo_id = $content['id'];
			if($viadeo_info=getAnyTableWhereData(VIADEO_USER_TABLE,"viadeo_id='".tep_db_input($viadeo_id)."'","user_type,user_id"))
	  {
    $user_type = $viadeo_info['user_type'];
    $user_id   = $viadeo_info['user_id'];
    if($user_type=='jobseeker')
			 {
     if(MODULE_VIADEO_PLUGIN_JOBSEEKER!='enable')
     {
      $messageStack->add_session('through Viadeo jobseeker login disable by admin.use normal way to login','error');
      tep_redirect(FILENAME_JOBSEEKER_LOGIN);
     }
     $row_info=getAnyTableWhereData(JOBSEEKER_LOGIN_TABLE," jobseeker_id='".$user_id."'",'jobseeker_id,jobseeker_status,ip_address,number_of_logon');
     if($row_info['jobseeker_status']=='Yes')
     {
      $ip_address=$_SERVER['REMOTE_ADDR'];
      $last_ip_address=tep_db_prepare_input($row_info['ip_address']);
      $number_of_logon=$row_info['number_of_logon']+1;
      $sql_data_array = array('last_login_time' => 'now()',
                           'ip_address' => $ip_address,
                           'last_ip_address' => $last_ip_address,
                           'number_of_logon' => $number_of_logon);
      tep_db_perform(JOBSEEKER_LOGIN_TABLE, $sql_data_array, 'update', "jobseeker_id = '" . $row_info['jobseeker_id'] . "'");
      $language=$_SESSION['language'];
      $language_id=$_SESSION['languages_id'];
      @session_unset($_SESSION);
      @session_destroy($_SESSION);
      $_SESSION['sess_jobseekerlogin']="y";
      $_SESSION['sess_jobseekerid']=$row_info["jobseeker_id"];
      $_SESSION['language']=$language;
      $_SESSION['languages_id']=$language_id;
					 tep_redirect(FILENAME_JOBSEEKER_CONTROL_PANEL);
     }
     elseif($row_info['jobseeker_id'])
     {
      $messageStack->add_session('Your account is blocked.', 'error');
      tep_redirect(FILENAME_JOBSEEKER_LOGIN);
     } 
				}
				else
				{
					if(MODULE_VIADEO_PLUGIN_RECRUITER!='enable')
     {
      $messageStack->add_session('through Viadeo recruiter login disable by admin.use normal way to login','error');
      tep_redirect(FILENAME_RECRUITER_LOGIN);
     }
     if($row_info=getAnyTableWhereData(RECRUITER_LOGIN_TABLE," recruiter_id='".$user_id."'",'recruiter_id,recruiter_status,ip_address,number_of_logon'))
					{
      if($row_info['recruiter_status']=='Yes')
      {
       $ip_address=$_SERVER['REMOTE_ADDR'];
       $last_ip_address=tep_db_prepare_input($row_info['ip_address']);
       $number_of_logon=$row_info['number_of_logon']+1;
       $sql_data_array = array('last_login_time' => 'now()',
                           'ip_address' => $ip_address,
                           'last_ip_address' => $last_ip_address,
                           'number_of_logon' => $number_of_logon);
       tep_db_perform(RECRUITER_LOGIN_TABLE, $sql_data_array, 'update', "recruiter_id = '" . $row_info['recruiter_id'] . "'");
       $language=$_SESSION['language'];
       $language_id=$_SESSION['languages_id'];
       @session_unset($_SESSION);
       @session_destroy($_SESSION);
       $_SESSION['sess_recruiterlogin']="y";
       $_SESSION['sess_recruiterid']=$row_info["recruiter_id"];
       $_SESSION['language']=$language;
       $_SESSION['languages_id']=$language_id;
       tep_redirect(FILENAME_RECRUITER_CONTROL_PANEL);
      }
      elseif($row_info['recruiter_id'])
      {
       $messageStack->add_session('Your account is blocked.', 'error');
       tep_redirect(FILENAME_RECRUITER_LOGIN);
      }
					}					
				}
			}  
   if($request=='recruiter_info')
   tep_redirect(FILENAME_RECRUITER_REG_VIADEO);
   else
   tep_redirect(FILENAME_JOBSEEKER_REG_VIADEO);
  break;
 default:
  $user_type=tep_db_prepare_input($_GET['user_type']);
  $connection = new  ViadeoOAuth($viadeo_app_key,$viadeo_app_secret,$calback_url);
  $state=md5(uniqid(rand(), TRUE));
  if($user_type=='recruiter') 
   $state=$state.'_recruiter';
  else
   $state=$state.'_jobseeker';
  $_SESSION['state']=$state;
  $connection->state=$state;
  $url=$connection->createAuthUrl();
  tep_redirect($url);  
}
?>