<?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft Pvt. Ltd.   #**********
**********# Copyright (c) www.aynsoft.com 2012  #**********
***********************************************************
*/
include_once("include_files.php");
include_once "class/facebookoauth.php";
ini_set('error_reporting',E_ALL ^ E_NOTICE);
ini_set('display_errors','1');
$facebook_app_key  = MODULE_FACEBOOK_PLUGIN_APP_KEY; 
$facebook_app_secret = check_data1(MODULE_FACEBOOK_PLUGIN_APP_SECRET_KEY,'##@##','app','passw');
$calback_url=tep_href_link(FILENAME_FACEBOOK_APPLICATION);
if($facebook_app_secret==-1)
$facebook_app_secret= '';
if($facebook_app_key=='' || $facebook_app_secret=='' || MODULE_FACEBOOK_PLUGIN!='enable')
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
 case 'admin_info':
   if(!isset($_SESSION['access_token']))
     tep_redirect(FILENAME_INDEX); 
   $access_token1=$_SESSION['access_token'];
   $connection = new  FacebookOAuth($facebook_app_key,$facebook_app_secret,$calback_url);
   $connection->setAccessToken($_SESSION['access_token']);
   $token=$connection->accessToken['access_token'];
   $content = $connection->get('https://graph.facebook.com/me?fields=id&&access_token='.urlencode($_SESSION['access_token']));
			$facebook_page_id  = explode(':',MODULE_FACEBOOK_PLUGIN_SUBMITTER_ID); 
   $page_id = $facebook_page_id[1];
			$page_found =false;
			if($page_id==$content['id'])
	  {
 			$page_found =$content['id'];
			}
			else
	  {
    $content = $connection->get('https://graph.facebook.com/me/accounts?fields=id&limit=100&&access_token='.$token);
				foreach($content['data'] as $c =>$d)
				{
			  if($d['id']==$page_id)
					{
   			$page_found =$d['id'];
						break;
					}
				}
			}
			if($page_found)
    tep_redirect('https://www.facebook.com/logout.php?next='.urlencode(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FACEBOOK_PLUGIN,'&access_token='.$token.'&page_found='.$page_found)).'&access_token='.$token);
			else
    tep_redirect('https://www.facebook.com/logout.php?next='.urlencode(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FACEBOOK_PLUGIN,'page_found=faild')).'&access_token='.$token);
			//print_r($content);die();
		break;
 case 'jobseeker':
 case 'recruiter':
 case 'admin':
   if (isset($_GET['code']))
   {
    if($state!=$_SESSION['state'] || $state=='')
    {
     unset($_SESSION['state']);
     die('Invalid Request');
    }
    $code       = tep_db_prepare_input($_GET['code']);
    $connection = new FacebookOAuth($facebook_app_key,$facebook_app_secret,$calback_url);
    $connection ->authenticate($code);
    $_SESSION['access_token'] = $connection->getAccessToken();
    unset($_SESSION['state']);
    if($request=='recruiter')
     tep_redirect(FILENAME_FACEBOOK_APPLICATION."?request=recruiter_info");
    elseif($request=='admin')
     tep_redirect(FILENAME_FACEBOOK_APPLICATION."?request=admin_info");
    else
     tep_redirect(FILENAME_FACEBOOK_APPLICATION."?request=jobseeker_info");
   }
  break;
 case 'recruiter_info':
 case 'jobseeker_info':
   if(!isset($_SESSION['access_token']))
   {
    if($request=='recruiter_info')
     tep_redirect(FILENAME_FACEBOOK_APPLICATION."?user_type=recruiter");
    else
     tep_redirect(FILENAME_FACEBOOK_APPLICATION); 
   }
   $access_token1=$_SESSION['access_token'];
   $connection = new  FacebookOAuth($facebook_app_key,$facebook_app_secret,$calback_url);
   $connection->setAccessToken($_SESSION['access_token']);
   $token=$connection->accessToken['access_token'];
   $content = $connection->get('https://graph.facebook.com/me?fields=id,email&access_token='.$token);
   if(!tep_not_null($content['email']))
   {
    @session_unset();
    @session_destroy();
    die('Error : Unable to access Your information.');
   }
   $user_email = filter_var($content['email'], FILTER_SANITIZE_EMAIL);
   if($row_info=getAnyTableWhereData(JOBSEEKER_LOGIN_TABLE," jobseeker_email_address='".tep_db_input($user_email)."'",'jobseeker_id,jobseeker_status,ip_address,number_of_logon'))
   {
    if(MODULE_FACEBOOK_PLUGIN_JOBSEEKER!='enable')
    {
     $messageStack->add_session('through Facebook jobseeker login disable by admin.use normal way to login','error');
     tep_redirect('https://www.facebook.com/logout.php?next='.urlencode(tep_href_link(FILENAME_JOBSEEKER_LOGIN)).'&access_token='.$token);
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
     @session_unset($_SESSION);
     @session_destroy($_SESSION);
     $_SESSION['sess_jobseekerlogin']="y";
     $_SESSION['sess_jobseekerid']=$row_info["jobseeker_id"];
     $_SESSION['language']=$language;
     $_SESSION['languages_id']=$language_id;
     tep_redirect('https://www.facebook.com/logout.php?next='.urlencode(tep_href_link(FILENAME_JOBSEEKER_CONTROL_PANEL)).'&access_token='.$token);
    }
    elseif($row_info['jobseeker_id'])
    {
     $messageStack->add_session('Your account is blocked.', 'error');
     tep_redirect('https://www.facebook.com/logout.php?next='.urlencode(tep_href_link(FILENAME_JOBSEEKER_LOGIN)).'&access_token='.$token);
    }
   }
   elseif($row_info=getAnyTableWhereData(RECRUITER_LOGIN_TABLE," recruiter_email_address='".tep_db_input($user_email)."'",'recruiter_id,recruiter_status,ip_address,number_of_logon'))
   {///recruiter
    if(MODULE_LINKEDIN_PLUGIN_RECRUITER!='enable')
    {
     $messageStack->add_session('through Facebook recruiter login disable by admin.use normal way to login','error');
     tep_redirect('https://www.facebook.com/logout.php?next='.urlencode(tep_href_link(FILENAME_RECRUITER_LOGIN)).'&access_token='.$token);
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
     @session_unset($_SESSION);
     @session_destroy($_SESSION);
     $_SESSION['sess_recruiterlogin']="y";
     $_SESSION['sess_recruiterid']=$row_info["recruiter_id"];
     $_SESSION['language']=$language;
     $_SESSION['languages_id']=$language_id;
     tep_redirect('https://www.facebook.com/logout.php?next='.urlencode(tep_href_link(FILENAME_RECRUITER_CONTROL_PANEL)).'&access_token='.$token);
    }
    elseif($row_info['recruiter_id'])
    {
     $messageStack->add_session('Your account is blocked.', 'error');
     tep_redirect('https://www.facebook.com/logout.php?next='.urlencode(tep_href_link(FILENAME_RECRUITER_LOGIN)).'&access_token='.$token);
    }
   }
   elseif($row_info=getAnyTableWhereData(RECRUITER_USERS_TABLE," email_address='".tep_db_input($user_email)."'",'id,recruiter_id,status,ip_address,number_of_logon'))
   {///recruiter user 
    if(MODULE_LINKEDIN_PLUGIN_RECRUITER!='enable')
    {
     $_SESSION['REDIRECT_URL']=FILENAME_RECRUITER_LOGIN;
     $messageStack->add_session('through Facebook recruiter login disable by admin.use normal way to login','error');
     tep_redirect('https://www.facebook.com/logout.php?next='.urlencode(tep_href_link(FILENAME_RECRUITER_LOGIN)).'&access_token='.$token);
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
     @session_unset($_SESSION);
     @session_destroy($_SESSION);
     $_SESSION['sess_recruiterlogin']="y";
     $_SESSION['sess_recruiterid']=$row_info["recruiter_id"];
     $_SESSION['sess_recruiteruserid']=$row_info["id"];
     $_SESSION['language']=$language;
     $_SESSION['languages_id']=$language_id;
     tep_redirect('https://www.facebook.com/logout.php?next='.urlencode(tep_href_link(FILENAME_RECRUITER_CONTROL_PANEL)).'&access_token='.$token);
    }
    elseif($row_info['id'])
    {
     $messageStack->add_session('Your account is blocked.', 'error');
     tep_redirect('https://www.facebook.com/logout.php?next='.urlencode(tep_href_link(FILENAME_RECRUITER_LOGIN)).'&access_token='.$token);
    }
   }
   else if($row=getAnyTableWhereData(ADMIN_TABLE,"admin_email_address='".tep_db_input($user_email)."'","admin_id"))
   {
    $messageStack->add_session('through Facebook Admin login disable .', 'error');
    tep_redirect('https://www.facebook.com/logout.php?next='.urlencode(tep_href_link(PATH_TO_ADMIN.FILENAME_INDEX)).'&access_token='.$token);
   }
   if($request=='recruiter_info')
   tep_redirect(FILENAME_RECRUITER_REG_FACEBOOK);
   else
   tep_redirect(FILENAME_JOBSEEKER_REG_FACEBOOK);
  break;
 default:
  $user_type=tep_db_prepare_input($_GET['user_type']);
  $connection = new  FacebookOAuth($facebook_app_key,$facebook_app_secret,$calback_url);
  $scope='email,user_location';
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