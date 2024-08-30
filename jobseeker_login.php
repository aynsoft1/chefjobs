<?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik   #*****
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
***********************************************************
*/
include_once("include_files.php");
include_once("general_functions/password_funcs.php");
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_JOBSEEKER_LOGIN);
include_once(FILENAME_BODY);
$jscript_file=tep_href_link(PATH_TO_LANGUAGE.$language."/jscript/".'jobseeker_login.js');
$template->set_filenames(array('jobseeker_login' => 'jobseeker_login.htm'));
//// Jobseeker auto login starts //////
$checked1=false;
$encoded_login1=$_COOKIE["autologin1"];
//echo $encoded_login1;
if(tep_not_null($encoded_login1))
{
 $checked1=true;
 $explode_array=explode("|",decode_string($encoded_login1));
 $TREF_email_address1=$explode_array[0];
 $TR_password1=$explode_array[1];
}
else
{
$TREF_email_address1="demo@aynsoft.com";
 $TR_password1='jobseeker';
}
//// Jobseeker auto login ends //////

$action = (isset($_POST['action']) ? $_POST['action'] : '');

if ($action=='check')
{
 $jobseeker_email_address=tep_db_prepare_input($_POST['TREF_email_address']);
	$jobseeker_password=$_POST['TR_password'];
	$whereClause="jl.jobseeker_email_address='".tep_db_input($jobseeker_email_address)."' and jl.jobseeker_status='Yes' and jl.jobseeker_id=j.jobseeker_id";
 $fields='jl.jobseeker_id,concat(j.jobseeker_first_name," ",j.jobseeker_last_name) as name,jl.jobseeker_email_address,jl.jobseeker_password,jl.ip_address,jl.number_of_logon';
	if($row=getAnyTableWhereData(JOBSEEKER_LOGIN_TABLE.' as jl, '.JOBSEEKER_TABLE.' as j',$whereClause,$fields,false))
	{
  if(!tep_validate_password($jobseeker_password, $row['jobseeker_password']))
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
   tep_db_perform(JOBSEEKER_LOGIN_TABLE, $sql_data_array, 'update', "jobseeker_id = '" . $row['jobseeker_id'] . "'");
   $language=$_SESSION['language'];
			$language_id=$_SESSION['languages_id'];
   @session_unset($_SESSION);
   @session_destroy($_SESSION);

   /////////for Cookie ///
   @SetCookie("autologin1", "", 0);
   @SetCookie("autologin2", "", 0);
   if(isset($_POST['auto_login1']))//start if4
   {
    //set login to expire in 1 day
    srand((double) microtime() * 1000000);
    $encoded_login=encode_string($jobseeker_email_address."|");
    @SetCookie("autologin1", $encoded_login, time() + ( 24 * 3600 * 365 ));
   }
   ////////
   $_SESSION['sess_jobseekername']=tep_db_output($row['name']);
   $_SESSION['sess_jobseekerlogin']="y";
   $_SESSION['sess_jobseekerid']=$row["jobseeker_id"];
			$_SESSION['language']=$language;
			$_SESSION['languages_id']=$language_id;
   if(tep_not_null($redirect_url))
			{
 	  tep_redirect($redirect_url);
			}
			else
			{
	   tep_redirect(tep_href_link(FILENAME_JOBSEEKER_CONTROL_PANEL));
			}
  }
	}
	else
	{
  $messageStack->add(SORRY_LOGIN_MATCH, 'error');
	}
}
$social_login_button='';
if(!check_login("jobseeker"))
{
 if(MODULE_FACEBOOK_PLUGIN=='enable' && MODULE_FACEBOOK_PLUGIN_JOBSEEKER=='enable')
 $social_login_button.=' <a href="'.FILENAME_FACEBOOK_APPLICATION.'" title="Sign in with Facebook"><img src="'.tep_href_link('img/facebook.png').'" alt="Facebook"></a>';

 if(MODULE_GOOGLE_PLUGIN=='enable' && MODULE_GOOGLE_PLUGIN_JOBSEEKER=='enable')
 $social_login_button.=' <a href="'.FILENAME_GOOGLE_APPLICATION.'" title="Sign in with Google"><img src="'.tep_href_link('img/google.png').'" alt="Google"></a>';

 if(MODULE_LINKEDIN_PLUGIN=='enable' && MODULE_LINKEDIN_PLUGIN_JOBSEEKER=='enable')
 $social_login_button.=' <a href="'.FILENAME_LINKEDIN_APPLICATION.'" title="Sign in with Linkedin"><img src="'.tep_href_link('img/linkedin.png').'" alt="Linkedin"></a>';

 if(MODULE_TWITTER_PLUGIN_JOBSEEKER=='enable' && MODULE_TWITTER_SUBMITTER_OAUTH_CONSUMER_KEY!='')
 $social_login_button.=' <a href="'.FILENAME_TWITTER_APPLICATION.'" title="Sign in with Twitter"><img src="'.tep_href_link('img/twitter.png').'" alt="Twitter"></a>';
}
$template->assign_vars(array(
 'INFO_TEXT_JSCRIPT_FILE'  => $jscript_file,
	'INFO_TEXT_SOCIAL_LOGIN_BUTTON'=>$social_login_button,
 'HEADING_TITLE'=>HEADING_TITLE,
 'INFO_TEXT_EMAIL_ADDRESS'=>INFO_TEXT_EMAIL_ADDRESS,
 'INFO_TEXT_EMAIL_ADDRESS1'=>tep_draw_input_field('TREF_email_address', $TREF_email_address1,'size="35" class="jobseeker_2" ',true),
 'INFO_TEXT_PASSWORD'=>INFO_TEXT_PASSWORD,
 'INFO_TEXT_PASSWORD1'=>tep_draw_password_field('TR_password', $TR_password1,true, 'size="35" maxlength="15" class="jobseeker_2" '),
 'button'=>tep_image_submit(PATH_TO_BUTTON.'login.gif', IMAGE_CONFIRM),
 'NEW_USER'=>'new user ?&nbsp;<a href="' . tep_href_link(FILENAME_JOBSEEKER_REGISTER1).'"><b>'.INFO_TEXT_CLICK_HERE.'</b></a>',
 'NEW_USER_REGISTER_NOW'=>'<span class="style28">'.INFO_TEXT_NEW_USER.'</span> ?&nbsp;<a href="' . tep_href_link(FILENAME_JOBSEEKER_REGISTER1).'"><span class="style17">'.INFO_TEXT_REGISTER_NOW.'<span class="style17"></a>',
 'REGISTER_NOW'=>'<a href="' . tep_href_link(FILENAME_JOBSEEKER_REGISTER1).'">'.tep_image_button(PATH_TO_BUTTON."register_now.gif", REGISTER_NOW).'</a>',
 'FORGOT_PASSWORD'=>'<a href="'.tep_href_link(FILENAME_JOBSEEKER_FORGOT_PASSWORD).'" class="style15">'.INFO_TEXT_FORGOT_PASSWORD.'</a>',
 'AUTO_LOGIN1'=>tep_draw_checkbox_field('auto_login1','on', $checked1,'','id="auto_login1"').'<label for="auto_login1" class="home_verdana">'.AUTO_LOGIN.'<label>',
 'form'=>tep_draw_form('login', FILENAME_JOBSEEKER_LOGIN,'','post', 'onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action','check'),
 'INFO_TEXT_ALREADY_MEMBER'=>INFO_TEXT_ALREADY_MEMBER,
 'INFO_TEXT_LOGIN' =>INFO_TEXT_LOGIN,
 'INFO_TEXT_1'=>INFO_TEXT_1,
 'INFO_TEXT_2'=>INFO_TEXT_2,
 'INFO_TEXT_3'=>INFO_TEXT_3,
 'INFO_TEXT_4'=>INFO_TEXT_4,
 'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
 'RIGHT_BOX_WIDTH'=>RIGHT_BOX_WIDTH1,
 'LEFT_HTML'=>LEFT_HTML,
 'RIGHT_HTML'=>RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('jobseeker_login');
?>