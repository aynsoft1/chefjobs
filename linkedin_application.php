<?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft Pvt. Ltd.   #**********
**********# Copyright (c) www.aynsoft.com 2012  #**********
***********************************************************
*/
include_once("include_files.php");
include_once "class/linkedin.php";
$request=tep_db_prepare_input($_GET['request']);
$callback_url=tep_href_link(FILENAME_LINKEDIN_APPLICATION);
//ini_set('error_reporting','0');
//ini_set('display_errors','0');
if(isset($_GET['error']) || isset($_GET['error_description']))
{
 tep_redirect(FILENAME_INDEX);
}
if(MODULE_LINKEDIN_PLUGIN!='enable')
{
 tep_redirect(FILENAME_INDEX);
 unset($_SESSION['oauth_verifier']);
}
$linkedin_app_key    = MODULE_LINKEDIN_PLUGIN_APP_KEY; 
$linkedin_app_secret = check_data1(MODULE_LINKEDIN_PLUGIN_APP_SECRET_KEY,'##@##','app','passw');
if($linkedin_app_secret==-1)
$linkedin_app_secret= '';
if($linkedin_app_key=='' || $linkedin_app_secret=='')
tep_redirect(FILENAME_INDEX);
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
   $user_type=tep_db_prepare_input($_GET['user_type']);
   if (isset($_GET['code']))
   {
     if($state!=$_SESSION['state'] || $state=='')
     {
      unset($_SESSION['state']);
      die('Invalid Request');
     }
    }
    $code       = tep_db_prepare_input($_GET['code']);

    $connection = new LinkedIn($linkedin_app_key,$linkedin_app_secret,$callback_url);
	  $scope='r_liteprofile,r_emailaddress';

    $connection ->authenticate($code);
    $_SESSION['access_token'] = $connection->getAccessToken();
    unset($_SESSION['state']);    
	if($user_type=='recruiter')
     tep_redirect(FILENAME_LINKEDIN_APPLICATION."?request=recruiter_info");
    else
     tep_redirect(FILENAME_LINKEDIN_APPLICATION."?request=jobseeker_info");
    break;
 case 'recruiter_info':
 case 'jobseeker_info':
   if(!isset($_SESSION['access_token']))
   {
	 if($request=='recruiter_info')
     tep_redirect(FILENAME_LINKEDIN_APPLICATION."?user_type=recruiter");
    else
     tep_redirect(FILENAME_LINKEDIN_APPLICATION);
   }
   $connection = new LinkedIn($linkedin_app_key,$linkedin_app_secret,'');
   $connection->setAccessToken($_SESSION['access_token']);
   $response=$connection->getProfileEmail();
   if(!tep_not_null($response))
   {
	@session_unset();
    @session_destroy();
    die('Error : Unable to access Your information.');
   }
   $user_email = $response;
   if($row_info=getAnyTableWhereData(JOBSEEKER_LOGIN_TABLE," jobseeker_email_address='".tep_db_input($user_email)."'",'jobseeker_id,jobseeker_status,ip_address,number_of_logon'))
   {
	 if(MODULE_LINKEDIN_PLUGIN_JOBSEEKER!='enable')
    {
	 $_SESSION['REDIRECT_URL']=FILENAME_JOBSEEKER_LOGIN;
     $_SESSION['linkedin_error']='through linkedin jobseeker login disable by admin.use normal way to login';
     tep_redirect(FILENAME_LINKEDIN_APPLICATION1);
    }
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
  	 @session_unset();
     @session_destroy();
     session_start();

     $_SESSION['linkedin_page']     = 'display';
     $_SESSION['sess_jobseekerlogin']="y";
     $_SESSION['sess_jobseekerid']=$row_info["jobseeker_id"];
     $_SESSION['language']=$language;
     $_SESSION['languages_id']=$language_id;
     $_SESSION['REDIRECT_URL']=FILENAME_JOBSEEKER_CONTROL_PANEL;
     tep_redirect(FILENAME_LINKEDIN_APPLICATION1);
    }
    elseif($row_info['jobseeker_id'])
    {
     $messageStack->add_session('Your account is blocked.', 'error');
      $_SESSION['REDIRECT_URL']=FILENAME_JOBSEEKER_LOGIN;
      tep_redirect(FILENAME_LINKEDIN_APPLICATION1);
    }
   } 
   elseif($row_info=getAnyTableWhereData(RECRUITER_LOGIN_TABLE," recruiter_email_address='".tep_db_input($user_email)."'",'recruiter_id,recruiter_status,ip_address,number_of_logon'))
   {///recruiter
	 if(MODULE_LINKEDIN_PLUGIN_RECRUITER!='enable')
     {
	  $_SESSION['REDIRECT_URL']=FILENAME_RECRUITER_LOGIN;
      $_SESSION['linkedin_error']='through linkedin recruiter login disable by admin.use normal way to login';
      tep_redirect(FILENAME_LINKEDIN_APPLICATION1);
     }
     $row_info=getAnyTableWhereData(RECRUITER_LOGIN_TABLE," recruiter_email_address='".$user_email."'",'recruiter_id,recruiter_status,ip_address,number_of_logon');
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
      @session_unset();
      @session_destroy();
      session_start();
      $_SESSION['linkedin_page']     = 'display';
      $_SESSION['sess_recruiterlogin']="y";
      $_SESSION['sess_recruiterid']=$row_info["recruiter_id"];
      $_SESSION['language']=$language;
      $_SESSION['languages_id']=$language_id;
      $_SESSION['REDIRECT_URL']=FILENAME_RECRUITER_CONTROL_PANEL;
      tep_redirect(FILENAME_LINKEDIN_APPLICATION1);
     }
     elseif($row_info['recruiter_id'])
     {
      $messageStack->add_session('Your account is blocked.', 'error');
      $_SESSION['REDIRECT_URL']=FILENAME_RECRUITER_LOGIN;
      tep_redirect(FILENAME_LINKEDIN_APPLICATION1);
     }
   }
   elseif($row_info=getAnyTableWhereData(RECRUITER_USERS_TABLE," email_address='".tep_db_input($user_email)."'",'id,recruiter_id,status,ip_address,number_of_logon'))
   {///recruiter user 
     if(MODULE_LINKEDIN_PLUGIN_RECRUITER!='enable')
     {
	  $_SESSION['REDIRECT_URL']=FILENAME_RECRUITER_LOGIN;
      $_SESSION['linkedin_error']='through linkedin recruiter login disable by admin.use normal way to login';
      tep_redirect(FILENAME_LINKEDIN_APPLICATION1);
     }
      if($row_info['status']=='Yes')
     {
      $ip_address=$_SERVER['REMOTE_ADDR'];
      $last_ip_address=tep_db_prepare_input($row_info['ip_address']);
      $number_of_logon=$row_info['number_of_logon']+1;
      $sql_data_array = array('last_login_time' => 'now()',
                            'ip_address' => $ip_address,
                            'last_ip_address' => $last_ip_address,
                            'number_of_logon' => $number_of_logon);
      tep_db_perform(RECRUITER_USERS_TABLE, $sql_data_array, 'update', "id = '" . $row_info['id'] . "'");
      $language=$_SESSION['language'];
      $language_id=$_SESSION['languages_id'];
   @session_unset();
   @session_destroy();
   session_start();
      $_SESSION['linkedin_page']     = 'display';
      $_SESSION['sess_recruiterlogin']="y";
      $_SESSION['sess_recruiterid']=$row_info["recruiter_id"];
      $_SESSION['language']=$language;
      $_SESSION['languages_id']=$language_id;
      $_SESSION['REDIRECT_URL']=FILENAME_RECRUITER_CONTROL_PANEL;
      tep_redirect(FILENAME_LINKEDIN_APPLICATION1);
     }
     elseif($row_info['recruiter_id'])
     {
      $messageStack->add_session('Your account is blocked.', 'error');
      $_SESSION['REDIRECT_URL']=FILENAME_RECRUITER_LOGIN;
      tep_redirect(FILENAME_LINKEDIN_APPLICATION1);
     }
   }
   $_SESSION['linkedin_page']     = 'display';
   if($request=='recruiter_info') 
   tep_redirect(FILENAME_RECRUITER_REG_LINKEDIN);
   else
   tep_redirect(FILENAME_JOBSEEKER_REG_LINKEDIN);
 break;
default:
 $user_type=tep_db_prepare_input($_GET['user_type']);
  if($user_type=='recruiter') 
  {
   if(MODULE_LINKEDIN_PLUGIN_RECRUITER!='enable')
   {
    $messageStack->add_session('through linkedin recruiter login disable by admin.use normal way to register','error');
    tep_redirect(FILENAME_RECRUITER_LOGIN);
   }
  }
  else
  {
   if(MODULE_LINKEDIN_PLUGIN_JOBSEEKER!='enable')
   {
    $messageStack->add_session('through linkedin jobseeker login disable by admin.use normal way to register','error');
    tep_redirect(FILENAME_JOBSEEKER_LOGIN);
   }
  }
  $connection = new LinkedIn($linkedin_app_key,$linkedin_app_secret,$callback_url);
  $scope='r_liteprofile,r_emailaddress';
  $state=md5(uniqid(rand(), TRUE));
  if($user_type=='recruiter') 
   $state=$state.'_recruiter';
  else
   $state=$state.'_jobseeker';
  $_SESSION['state']=$state;
  $connection->state=$state;
  $url=$connection->createAuthUrl($scope);
  tep_redirect($url);   //login 
}
?>