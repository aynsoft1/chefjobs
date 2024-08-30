<?
/*
***********************************************************
***********************************************************
**********# Name          : Kamal Kumar Sahoo   #**********
**********# Company       : Aynsoft             #**********
**********# Date Created  : 11/02/04            #**********
**********# Date Modified : 11/02/04            #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
***********************************************************
***********************************************************
*/
include_once("include_files.php");
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_RECRUITER_LOGIN);
include_once(FILENAME_BODY);
$template->set_filenames(array('login' => 'recruiter_login.htm'));
$jscript_file=tep_href_link(PATH_TO_LANGUAGE.$language."/jscript/".'recruiter_login.js');
//// Recruiter auto login starts //////
$checked2=false;
$encoded_login2=$_COOKIE["autologin2"];
//echo $encoded_login2;
if(tep_not_null($encoded_login2))
{
 $checked2=true;
 $explode_array=explode("|",decode_string($encoded_login2));
 $TREF_email_address2=$explode_array[0];
 $TR_password2=$explode_array[1];
}
else
{
 $TREF_email_address2="";
 $TR_password2='';
}
//// Recruiter auto login ends //////

$action = (isset($_POST['action']) ? $_POST['action'] : '');

if ($action=='check')
{
 $recruiter_email_address=tep_db_prepare_input($_POST['TREF_email_address']);
	$recruiter_password=$_POST['TR_password'];
	$whereClause="rl.recruiter_email_address='".tep_db_input($recruiter_email_address)."' and rl.recruiter_status='Yes' and rl.recruiter_id=r.recruiter_id";
 $fields='rl.recruiter_id,concat(r.recruiter_first_name," ",r.recruiter_last_name) as name,rl.recruiter_email_address,rl.recruiter_password,rl.ip_address,rl.number_of_logon';
	if($row=getAnyTableWhereData(RECRUITER_LOGIN_TABLE.' as rl, '.RECRUITER_TABLE.' as r',$whereClause,$fields))
	{
  if(!tep_validate_password($recruiter_password, $row['recruiter_password']))
  {
   $messageStack->add(SORRY_LOGIN_MATCH, 'error');
  }
  else
  {
			$redirect_url=(tep_not_null($_SESSION['REDIRECT_URL'])?HOST_NAME_MAIN.$_SESSION['REDIRECT_URL']:'');
   $ip_address=$_SERVER['REMOTE_ADDR'];
   $last_ip_address=tep_db_prepare_input($row['ip_address']);
   $number_of_logon=$row['number_of_logon']+1;
   $sql_data_array = array('last_login_time' => 'now()',
                           'ip_address' => $ip_address,
                           'last_ip_address' => $last_ip_address,
                           'number_of_logon' => $number_of_logon);
			$language=$_SESSION['language'];
			$language_id=$_SESSION['languages_id'];
			//print_r($_SESSION);die();
   tep_db_perform(RECRUITER_LOGIN_TABLE, $sql_data_array, 'update', "recruiter_id = '" . $row['recruiter_id'] . "'");
   @session_unset($_SESSION);
   @session_destroy($_SESSION);

   /////////for Cookie ///
   @SetCookie("autologin1", "", 0);
   @SetCookie("autologin2", "", 0);
   if(isset($_POST['auto_login2']))//start if4
   {
    //set login to expire in 1 day
    srand((double) microtime() * 1000000);
    $encoded_login=encode_string($recruiter_email_address."|");
    @SetCookie("autologin2", $encoded_login, time() + ( 24 * 3600 * 365 ));
   }
   ////////
   $_SESSION['sess_recruiterlogin']="y";
   $_SESSION['sess_recruiterid']=$row["recruiter_id"];
   $_SESSION['language']=$language;
			$_SESSION['languages_id']=$language_id;
			if(tep_not_null($redirect_url))
			{
 	  tep_redirect($redirect_url);
			}
			else
			{
	   tep_redirect(tep_href_link(FILENAME_RECRUITER_CONTROL_PANEL));
			}
  }
	}
 else if($row=getAnyTableWhereData(RECRUITER_USERS_TABLE,"email_address='".tep_db_input($recruiter_email_address)."' and status='Yes'","id,recruiter_id,name,email_address,password,ip_address,number_of_logon"))
	{
  if(!tep_validate_password($recruiter_password, $row['password']))
  {
   $messageStack->add(SORRY_LOGIN_MATCH, 'error');
  }
  else
  {
   $ip_address=$_SERVER['REMOTE_ADDR'];
   $last_ip_address=tep_db_prepare_input($row['ip_address']);
   $number_of_logon=$row['number_of_logon']+1;
   $sql_data_array = array('last_login_time' => 'now()',
                           'ip_address' => $ip_address,
                           'last_ip_address' => $last_ip_address,
                           'number_of_logon' => $number_of_logon);
			tep_db_perform(RECRUITER_USERS_TABLE, $sql_data_array, 'update', "id = '" . $row['id'] . "'");
			$language=$_SESSION['language'];
			$language_id=$_SESSION['languages_id'];
   @session_unset($_SESSION);
   @session_destroy($_SESSION);

   /////////for Cookie ///
   @SetCookie("autologin1", "", 0);
   @SetCookie("autologin2", "", 0);
   if(isset($_POST['auto_login2']))//start if4
   {
    //set login to expire in 1 day
    srand((double) microtime() * 1000000);
    $encoded_login=encode_string($recruiter_email_address."|");
    @SetCookie("autologin2", $encoded_login, time() + ( 24 * 3600 * 365 ));
   }
   ////////
   $_SESSION['sess_recruiterlogin']="y";
   $_SESSION['sess_recruiterid']=$row["recruiter_id"];
   $_SESSION['sess_recruiteruserid']=$row["id"];
		 $_SESSION['language']=$language;
			$_SESSION['languages_id']=$language_id;
   tep_redirect(tep_href_link(FILENAME_RECRUITER_CONTROL_PANEL));
  }
 }
	else
	{
  $messageStack->add(SORRY_LOGIN_MATCH, 'error');
	}
}
$social_login_button='';
if(!check_login("recruiter"))
{
 if(MODULE_FACEBOOK_PLUGIN=='enable' && MODULE_FACEBOOK_PLUGIN_RECRUITER=='enable')
 $social_login_button.=' <a href="'.FILENAME_FACEBOOK_APPLICATION.'?user_type=recruiter" title="Sign in with Facebook"><img src="'.tep_href_link('img/facebook.png').'" alt="Facebook"></a>';
 if(MODULE_GOOGLE_PLUGIN=='enable' && MODULE_GOOGLE_PLUGIN_RECRUITER=='enable')
 $social_login_button.=' <a href="'.FILENAME_GOOGLE_APPLICATION.'?user_type=recruiter" title="Sign in with Google"><img src="'.tep_href_link('img/google.png').'" alt="Google"></a>';
 if(MODULE_LINKEDIN_PLUGIN=='enable' && MODULE_LINKEDIN_PLUGIN_RECRUITER=='enable')
 $social_login_button.=' <a href="'.FILENAME_LINKEDIN_APPLICATION.'?user_type=recruiter" title="Sign in with Linkedin"><img src="'.tep_href_link('img/linkedin.png').'" alt="Linkedin"></a>';
 if( MODULE_TWITTER_PLUGIN_RECRUITER=='enable' && MODULE_TWITTER_SUBMITTER_OAUTH_CONSUMER_KEY!='')
 $social_login_button.=' <a href="'.FILENAME_TWITTER_APPLICATION.'?user_type=recruiter" title="Sign in with Twitter"><img src="'.tep_href_link('img/twitter.png').'" alt="Twitter"></a>';
}
$template->assign_vars(array(
 'INFO_TEXT_JSCRIPT_FILE'  => $jscript_file,
	'FACEBOOK_LOGIN_BUTTON'  =>$facebook_log,
 'FACEBOOK_LOGIN_BUTTON_SCRIPT'        => $facebook_login_button,
	'INFO_TEXT_SOCIAL_LOGIN_BUTTON'=>$social_login_button,
	'HEADING_TITLE'=>HEADING_TITLE,
 'INFO_TEXT_EMAIL_ADDRESS'=>INFO_TEXT_EMAIL_ADDRESS,
 'INFO_TEXT_EMAIL_ADDRESS1'=>tep_draw_input_field('TREF_email_address', $TREF_email_address2,'size="35" class="jobseeker_2" maxlength="50" onfocus="document.login.TREF_email_address.value=\'\'"',true),
 'INFO_TEXT_PASSWORD'=>INFO_TEXT_PASSWORD,
 'INFO_TEXT_PASSWORD1'=>tep_draw_password_field('TR_password', $TR_password2,true, 'size="35" class="jobseeker_2" maxlength="15" onfocus="document.login.TR_password.value=\'\'"'),
 'button'=>tep_image_submit(PATH_TO_BUTTON.'button_confirm.gif', IMAGE_CONFIRM),
 'NEW_USER'=>INFO_TEXT_NEW_USER.'&nbsp;<a href="' . tep_href_link(FILENAME_RECRUITER_REGISTRATION).'" class="home_verdana_bold">'.INFO_TEXT_CLICK_HERE.'</a>',
 'NEW_USER_REGISTER_NOW'=>'<span class="style15">'.INFO_TEXT_NEW_USER.'</span><span class="jobseeker_1"> &nbsp;</span><a href="' . tep_href_link(FILENAME_RECRUITER_REGISTRATION).'" class="style17">'.INFO_TEXT_REGISTER_NOW.'</a>',
 'REGISTER_NOW'=>'<a href="' . tep_href_link(FILENAME_RECRUITER_REGISTRATION).'">'.tep_image_button(PATH_TO_BUTTON."register_now.gif", REGISTER_NOW).'</a>',
 'FORGOT_PASSWORD'=>'<a href="' . tep_href_link(FILENAME_RECRUITER_FORGOT_PASSWORD).'" class="home_verdana_bold">'.INFO_TEXT_FORGOT_PASSWORD.'</a>',
 'AUTO_LOGIN1'=>tep_draw_checkbox_field('auto_login2','on', $checked2,'','id="auto_login2"').'<label for="auto_login2" class="home_verdana">'.AUTO_LOGIN.'<label>',
 'form'=>tep_draw_form('login', FILENAME_RECRUITER_LOGIN,'','post', 'onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action','check'),
 'INFO_TEXT_ALREADY_MEMBER'=>INFO_TEXT_ALREADY_MEMBER,
 'INFO_TEXT_LOGIN'=>INFO_TEXT_LOGIN,
 'INFO_TEXT_QUOT1'=>INFO_TEXT_QUOT1,
 'INFO_TEXT_QUOT2'=>INFO_TEXT_QUOT2,
 'INFO_TEXT_QUOT3'=> INFO_TEXT_QUOT3,
 'INFO_TEXT_QUOT4'=>INFO_TEXT_QUOT4,
 'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
 'RIGHT_BOX_WIDTH'=>RIGHT_BOX_WIDTH1,
 'LEFT_HTML'=>LEFT_HTML,
 'RIGHT_HTML'=>RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('login');
?>