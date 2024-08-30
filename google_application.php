<?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft Pvt. Ltd.   #**********
**********# Copyright (c) www.aynsoft.com 2012  #**********
***********************************************************
*/
include_once("include_files.php");
include_once "class/googleoauth2.php";
//ini_set('error_reporting',E_ALL ^ E_NOTICE);
//ini_set('display_errors','1');
$google_app_key  = MODULE_GOOGLE_PLUGIN_APP_KEY; 
$google_app_secret = check_data1(MODULE_GOOGLE_PLUGIN_APP_SECRET_KEY,'##@##','app','passw');
$calback_url=tep_href_link(FILENAME_GOOGLE_APPLICATION);
if($google_app_secret==-1)
$google_app_secret= '';
if($google_app_key=='' || $google_app_secret=='' || MODULE_GOOGLE_PLUGIN!='enable')
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
    $connection = new GoogleOAuth2($google_app_key,$google_app_secret,$calback_url);
    $connection ->authenticate($code);
    $_SESSION['access_token'] = $connection->getAccessToken();
    if($request=='recruiter')
     tep_redirect(FILENAME_GOOGLE_APPLICATION."?request=recruiter_info");
    else
     tep_redirect(FILENAME_GOOGLE_APPLICATION."?request=jobseeker_info");
   }
  break;
 case 'recruiter_info':
 case 'jobseeker_info':
   if(!isset($_SESSION['access_token']))
   {
    if($request=='recruiter_info')
     tep_redirect(FILENAME_GOOGLE_APPLICATION."?user_type=recruiter");
    else
     tep_redirect(FILENAME_GOOGLE_APPLICATION); 
   }
   $access_token1=$_SESSION['access_token'];
   $connection = new  GoogleOAuth2($google_app_key,$google_app_secret,$calback_url);
   $connection->setAccessToken($_SESSION['access_token']);
   $token=$connection->accessToken['access_token'];
   $content = $connection->get('https://www.googleapis.com/oauth2/v1/userinfo?access_token='.$token);
   if(!tep_not_null($content['email']))
   {
    @session_unset();
    @session_destroy();
    die('Error : Unable to access Your information.');
   }
   $user_email = filter_var($content['email'], FILTER_SANITIZE_EMAIL);
   if($row_info=getAnyTableWhereData(JOBSEEKER_LOGIN_TABLE," jobseeker_email_address='".tep_db_input($user_email)."'",'jobseeker_id,jobseeker_status,ip_address,number_of_logon'))
   {
    if(MODULE_GOOGLE_PLUGIN_JOBSEEKER!='enable')
    {
     $_SESSION['REDIRECT_URL']=FILENAME_JOBSEEKER_LOGIN;
     $_SESSION['google_error']='through Google jobseeker login disable by admin.use normal way to login';
     tep_redirect(FILENAME_GOOGLE_APPLICATION1);
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
     $_SESSION['sess_jobseekerlogin']="y";
     $_SESSION['sess_jobseekerid']=$row_info["jobseeker_id"];
     $_SESSION['language']=$language;
     $_SESSION['languages_id']=$language_id;
     $_SESSION['REDIRECT_URL']=FILENAME_JOBSEEKER_CONTROL_PANEL;
     tep_redirect(FILENAME_GOOGLE_APPLICATION1);
    }
    elseif($row_info['jobseeker_id'])
    {
     $_SESSION['google_error']='Your account is blocked.';
     $_SESSION['REDIRECT_URL']=FILENAME_JOBSEEKER_LOGIN;
     tep_redirect(FILENAME_GOOGLE_APPLICATION1);
    }
   }
   elseif($row_info=getAnyTableWhereData(RECRUITER_LOGIN_TABLE," recruiter_email_address='".tep_db_input($user_email)."'",'recruiter_id,recruiter_status,ip_address,number_of_logon'))
   {///recruiter
    if(MODULE_LINKEDIN_PLUGIN_RECRUITER!='enable')
    {
     $_SESSION['REDIRECT_URL']=FILENAME_RECRUITER_LOGIN;
     $_SESSION['google_error']='through Google recruiter login disable by admin.use normal way to login';
     tep_redirect(FILENAME_GOOGLE_APPLICATION1);
    }     
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
     $_SESSION['sess_recruiterlogin']="y";
     $_SESSION['sess_recruiterid']=$row_info["recruiter_id"];
     $_SESSION['language']=$language;
     $_SESSION['languages_id']=$language_id;
     $_SESSION['REDIRECT_URL']=FILENAME_RECRUITER_CONTROL_PANEL;
     tep_redirect(FILENAME_GOOGLE_APPLICATION1);
    }
    elseif($row_info['recruiter_id'])
    {
     $messageStack->add_session('Your account is blocked.', 'error');
     $_SESSION['REDIRECT_URL']=FILENAME_RECRUITER_LOGIN;
     tep_redirect(FILENAME_GOOGLE_APPLICATION1);
    }
   }
   elseif($row_info=getAnyTableWhereData(RECRUITER_USERS_TABLE," email_address='".tep_db_input($user_email)."'",'id,recruiter_id,status,ip_address,number_of_logon'))
   {///recruiter user 
    if(MODULE_LINKEDIN_PLUGIN_RECRUITER!='enable')
    {
     $_SESSION['REDIRECT_URL']=FILENAME_RECRUITER_LOGIN;
     $_SESSION['google_error']='through Google recruiter login disable by admin.use normal way to login';
     tep_redirect(FILENAME_GOOGLE_APPLICATION1);
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
     $_SESSION['sess_recruiterlogin']="y";
     $_SESSION['sess_recruiterid']=$row_info["recruiter_id"];
     $_SESSION['sess_recruiteruserid']=$row_info["id"];
     $_SESSION['language']=$language;
     $_SESSION['languages_id']=$language_id;
     $_SESSION['REDIRECT_URL']=FILENAME_RECRUITER_CONTROL_PANEL;
     tep_redirect(FILENAME_GOOGLE_APPLICATION1);
    }
    elseif($row_info['id'])
    {
     $messageStack->add_session('Your account is blocked.', 'error');
     $_SESSION['REDIRECT_URL']=FILENAME_RECRUITER_LOGIN;
     tep_redirect(FILENAME_GOOGLE_APPLICATION1);
    }
   }
   else if($row=getAnyTableWhereData(ADMIN_TABLE,"admin_email_address='".tep_db_input($user_email)."'","admin_id"))
   {
    $messageStack->add_session('through Google Admin login disable .', 'error');
    $_SESSION['REDIRECT_URL']=PATH_TO_ADMIN.FILENAME_INDEX;
    tep_redirect(FILENAME_GOOGLE_APPLICATION1);
   }
   if($request=='recruiter_info')
   tep_redirect(FILENAME_RECRUITER_REG_GOOGLE);
   else
   tep_redirect(FILENAME_JOBSEEKER_REG_GOOGLE);
  break;
 default:
  $user_type=tep_db_prepare_input($_GET['user_type']);
  $connection = new  GoogleOAuth2($google_app_key,$google_app_secret,$calback_url);
  $scope='https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/plus.me';
  $state=md5(uniqid(rand(), TRUE));
  if($user_type=='recruiter') 
   $state=$state.'_recruiter';
  else
   $state=$state.'_jobseeker';
  $_SESSION['state']=$state;
  $connection->state=$state;
  $url=$connection->createAuthUrl($scope);
  tep_redirect($url);  
}
?>