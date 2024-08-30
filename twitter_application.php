<?php
include_once("include_files.php");
include_once "class/twitteroauth.php";
$request=tep_db_prepare_input($_GET['request']);
$twitter_app_key    =  MODULE_TWITTER_SUBMITTER_OAUTH_CONSUMER_KEY; 
$twitter_app_secret = check_data1(MODULE_TWITTER_SUBMITTER_APP_CONSUMER_SECRET,'##@##','consumer','passw');

switch($request)
{
 case 'login_info':
  $user_type=tep_db_prepare_input($_GET['user_type']);
  if (isset($_REQUEST['oauth_verifier']))
  {
   if (isset($_REQUEST['oauth_token']) && $_SESSION['oauth_token'] !== $_REQUEST['oauth_token']) 
   {
    unset($_SESSION['oauth_token']);
    unset($_SESSION['oauth_token_secret']);
    if($user_type=='recruiter')
    tep_redirect(FILENAME_TWITTER_APPLICATION.'?user_type=recruiter'); 
    else
    tep_redirect(FILENAME_TWITTER_APPLICATION); 
   }
   $connection = new TwitterOAuth($twitter_app_key, $twitter_app_secret, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
   /* Request access tokens from twitter */
   $access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);
   $_SESSION['access_token'] = $access_token;
   unset($_SESSION['oauth_token']);
   unset($_SESSION['oauth_token_secret']);
   if (200 != $connection->http_code) 
   {
    unset($_SESSION['access_token']);
    if($user_type=='recruiter')
    tep_redirect(FILENAME_TWITTER_APPLICATION.'?user_type=recruiter'); 
    else
    tep_redirect(FILENAME_TWITTER_APPLICATION); 
   }
  }
  if(isset($_GET['denied']))
  {
   @session_unset();
   @session_destroy();
   tep_redirect(FILENAME_INDEX);    
  }
  if(isset($_SESSION['access_token']))
  {
   $access_token = $_SESSION['access_token'];
   $connection = new TwitterOAuth($twitter_app_key, $twitter_app_secret, $access_token['oauth_token'], $access_token['oauth_token_secret']);
   $content = $connection->get('account/verify_credentials');
   $twitter_id=$content->id;
   if($user_type=='recruiter') 
   {
    if(MODULE_TWITTER_PLUGIN_RECRUITER!='enable')
    {
     unset($_SESSION['access_token']);
     $messageStack->add_session('through twitter recruiter login disable by admin.use normal way to login','error');
     tep_redirect(FILENAME_RECRUITER_LOGIN);
    }
   }
   elseif(MODULE_TWITTER_PLUGIN_JOBSEEKER!='enable')
   {
    unset($_SESSION['access_token']);
    $messageStack->add_session('through twitter jobseeker login disable by admin.use normal way to login','error');
    tep_redirect(FILENAME_JOBSEEKER_LOGIN);
   }
   if($twitter_info=getAnyTableWhereData(TWITTER_USER_TABLE,"twitter_id='".tep_db_input($twitter_id)."'","user_type,user_id"))
   {
    $user_type = $twitter_info['user_type'];
    $user_id   = $twitter_info['user_id'];
    if($twitter_info['user_type']=='jobseeker')
    {
     if(MODULE_TWITTER_PLUGIN_JOBSEEKER!='enable')
     {
      unset($_SESSION['access_token']);
      $messageStack->add_session('through twitter jobseeker login disable by admin.use normal way to login', 'error');
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
      unset($_SESSION['access_token']);
      $messageStack->add_session('Your account is blocked.', 'error');
      tep_redirect(FILENAME_JOBSEEKER_LOGIN);
     }
    }
    else
    {///recruiter
     if(MODULE_TWITTER_PLUGIN_RECRUITER!='enable')
     {
      unset($_SESSION['access_token']);
      $messageStack->add_session('through twitter recruiter login disable by admin.use normal way to login','error');
      tep_redirect(FILENAME_RECRUITER_LOGIN);
     }
     $row_info=getAnyTableWhereData(RECRUITER_LOGIN_TABLE," recruiter_id='".$user_id."' ",'recruiter_id,recruiter_status,ip_address,number_of_logon');
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
      unset($_SESSION['access_token']);
      $messageStack->add_session('Your account is blocked.', 'error');
      tep_redirect(FILENAME_RECRUITER_LOGIN);
     }
    }
   }
   if($user_type=='recruiter') 
   tep_redirect(FILENAME_RECRUITER_REG_TWITTER);
   else
   tep_redirect(FILENAME_JOBSEEKER_REG_TWITTER);
  }
  else
  {
   if($user_type=='recruiter') 
   tep_redirect(FILENAME_TWITTER_APPLICATION.'?user_type=recruiter');
   else
   tep_redirect(FILENAME_TWITTER_APPLICATION);
  }
  break;
 default:
 $user_type=tep_db_prepare_input($_GET['user_type']);
  if($user_type=='recruiter') 
 {
  if(MODULE_TWITTER_PLUGIN_RECRUITER!='enable')
  {
   $messageStack->add_session('through twitter recruiter login disable by admin.use normal way to register','error');
   tep_redirect(FILENAME_RECRUITER_LOGIN);
  }
  $call_back_url=tep_href_link(FILENAME_TWITTER_APPLICATION,'request=login_info&user_type=recruiter');
 }
 else
 {
  if(MODULE_TWITTER_PLUGIN_JOBSEEKER!='enable')
  {
   $messageStack->add_session('through twitter jobseeker login disable by admin.use normal way to register','error');
   tep_redirect(FILENAME_JOBSEEKER_LOGIN);
  }
  $call_back_url=tep_href_link(FILENAME_TWITTER_APPLICATION,'request=login_info');
 }
 $connection = new TwitterOAuth($twitter_app_key,$twitter_app_secret);
 $request_token = $connection->getRequestToken($call_back_url);
 $_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
 $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
 $connection->http_code;
 switch ($connection->http_code) 
 {
  case 200:
    /* Build authorize URL and redirect user to Twitter. */
    $url = $connection->getAuthorizeURL($token,false);
    tep_redirect($url);
    break;
  default:
    /* Show notification if something went wrong. */
    echo 'Could not connect to Twitter. Refresh the page or try again later.';
 }
}
?>