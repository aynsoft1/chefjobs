<?
include_once("include_files.php");
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_JOBSEEKER_REG_TWITTER);
$template->set_filenames(array('registration' => 'jobseeker_registration_twitter.htm','email'=>'jobseeker_registration_template.htm','fr_email'=>'fr_jobseeker_registration_template.htm'));
include_once(FILENAME_BODY);
$jscript_file=PATH_TO_LANGUAGE.$language."/jscript/".'jobseeker_registation.js';
include_once "class/twitteroauth.php";
//print_r($_POST);exit;
$state_error=false;
$action = (isset($_POST['action']) ? $_POST['action'] : '');

if(check_login('jobseeker') )
{
 tep_redirect(FILENAME_JOBSEEKER_REGISTER1);
}
if(MODULE_TWITTER_PLUGIN_JOBSEEKER!='enable')
{
 unset($_SESSION['access_token']);
 $messageStack->add_session('through twitter jobseeker registration disable by admin.use normal way to register',true);
 tep_redirect(FILENAME_JOBSEEKER_REGISTER1);
}

$twitter_app_key  =MODULE_TWITTER_SUBMITTER_OAUTH_CONSUMER_KEY;
$twitter_app_secret = check_data1(MODULE_TWITTER_SUBMITTER_APP_CONSUMER_SECRET,'##@##','consumer','passw');
if($twitter_app_secret==-1)
$twitter_app_secret='';
if($twitter_app_key=='' || $twitter_app_secret=='')
{
 unset($_SESSION['access_token']);
}
$twitter_id='';
if(isset($_SESSION['access_token']))
{
 if (empty($_SESSION['access_token']) || empty($_SESSION['access_token']['oauth_token']) || empty($_SESSION['access_token']['oauth_token_secret']))
 {
  unset($_SESSION['access_token']);
  tep_redirect(FILENAME_TWITTER_APPLICATION);
 }
 $access_token = $_SESSION['access_token'];
 $connection = new TwitterOAuth($twitter_app_key, $twitter_app_secret, $access_token['oauth_token'], $access_token['oauth_token_secret']);
 $content = $connection->get('account/verify_credentials');
 if(tep_not_null($content->error))
 {
  tep_redirect(FILENAME_TWITTER_APPLICATION);
 }
 $twitter_id=$content->id;
 if($row_twitter=getAnyTableWhereData(TWITTER_USER_TABLE,"twitter_id='".tep_db_input($twitter_id)."'","user_type,user_id"))
 {
  tep_redirect(tep_href_link(FILENAME_TWITTER_APPLICATION,'request=login_info&user_type='.$row_twitter['user_type']));
 }
 if(MODULE_TWITTER_PLUGIN_JOBSEEKER!='enable')
 {
  unset($_SESSION['access_token']);
  $messageStack->add_session('through twitter jobseeker registration disable by admin.use normal way to register',true);
  tep_redirect(FILENAME_JOBSEEKER_REGISTER1);
 }
 if(!tep_not_null($action))
 {
  $TR_first_name = tep_db_prepare_input($content->name);
  $TR_city       = tep_db_prepare_input($content->location);
 }
}
else
{
 tep_redirect(FILENAME_TWITTER_APPLICATION);
}
// add/edit
if(tep_not_null($action))
{
 switch($action)
 {
  case 'new':
   $privacy=tep_db_prepare_input($_POST['privacy']);
   $cv_searchable=tep_db_prepare_input($_POST['cv_searchable']);
   $first_name=tep_db_prepare_input($_POST['TR_first_name']);
   $middle_name=tep_db_prepare_input($_POST['middle_name']);
   $last_name=tep_db_prepare_input($_POST['TR_last_name']);
   $email_address=tep_db_prepare_input($_POST['TREF_email_address']);
   $confirm_email_address=tep_db_prepare_input($_POST['TREF_confirm_email_address']);
   $address1=tep_db_prepare_input($_POST['TR_address_line1']);
   $address2=tep_db_prepare_input($_POST['address_line2']);
   $nationality=tep_db_prepare_input($_POST['TR_nationality']);
   $country=(int)tep_db_prepare_input($_POST['TR_country']);

   if(isset($_POST['state']) && $_POST['state']!='')
   $state_value=tep_db_prepare_input($_POST['state']);
   elseif(isset($_POST['state1']))
   $state_value=tep_db_prepare_input($_POST['state1']);

   $city=tep_db_prepare_input($_POST['TR_city']);
   $zip=tep_db_prepare_input($_POST['zip']);
   $home_phone=tep_db_prepare_input($_POST['primary_phone']);
			//$mobile_no=tep_db_prepare_input($_POST['mobile_no']);
   $mobile=tep_db_prepare_input($_POST['mobile']);
   $newsletter=tep_db_prepare_input($_POST['newsletter']);
   $error=false;
   //////privacy check ////
   if (!in_array($privacy,array('1','2','3')))
   {
    $error = true;
    $messageStack->add(PRIVACY_ERROR,'jobseeker_account');
   }
   //Check
   if($row=getAnyTableWhereData(RECRUITER_LOGIN_TABLE,"recruiter_email_address='".tep_db_input($email_address)."'","recruiter_id"))
   {
    $error = true;
    $messageStack->add(EMAIL_ADDRESS_ERROR,'jobseeker_account');
   }
   else if($row=getAnyTableWhereData(RECRUITER_USERS_TABLE,"email_address='".tep_db_input($email_address)."'","id"))
   {
    $error = true;
    $messageStack->add(EMAIL_ADDRESS_ERROR,'jobseeker_account');
   }
   else if($row=getAnyTableWhereData(ADMIN_TABLE,"admin_email_address='".tep_db_input($email_address)."'","admin_id"))
   {
    $error = true;
    $messageStack->add(EMAIL_ADDRESS_ERROR,'jobseeker_account');
   }
   else
   {
    if($row=getAnyTableWhereData(JOBSEEKER_LOGIN_TABLE,"jobseeker_email_address='".tep_db_input($email_address)."'","jobseeker_id"))
    {
     $error = true;
     $messageStack->add(EMAIL_ADDRESS_ERROR,'jobseeker_account');
    }
   }
   if(!$error)
   {
    $error_email=false;
    if(tep_validate_email($email_address) == false)
    {
     $error_email=true;
     $error = true;
     $messageStack->add(EMAIL_ADDRESS_INVALID_ERROR,'jobseeker_account');
    }
    if(tep_validate_email($confirm_email_address) == false)
    {
     $error_email=true;
     $error = true;
     $messageStack->add(CONFIRM_EMAIL_ADDRESS_INVALID_ERROR,'jobseeker_account');
    }
    if(!$error_email)
    {
     if($email_address!=$confirm_email_address)
     {
      $error = true;
      $messageStack->add(EMAIL_ADDRESS_MATCH_ERROR,'jobseeker_account');
     }
    }
				/////////// check state //
    if(is_numeric($state_value))
    {
     $zone_id = 0;//echo $state_value;
     if($check_query = getAnyTableWhereData(ZONES_TABLE, "zone_country_id = '" . tep_db_input($country) . "'", "zone_country_id"))
     {
      $zone_query = tep_db_query("select distinct zone_id from " . ZONES_TABLE . " where zone_country_id = '" . tep_db_input($country) . "' and (zone_id ='" . tep_db_input($state_value) . "' )");
      if (tep_db_num_rows($zone_query) == 1)
      {
       $zone = tep_db_fetch_array($zone_query);
       $zone_id = $zone['zone_id'];
      }
      else
      {
       //$state_error=true;
       //$error = true;
       //$messageStack->add(ENTRY_STATE_ERROR_SELECT,'jobseeker_account');
      }
     }
     else
     {
      //$state_error=true;
      //$error = true;
      //$messageStack->add(ENTRY_STATE_ERROR_SELECT,'jobseeker_account');
     }
    }
    else
    {
     if(tep_not_null($state_value))
     if($row11 = getAnyTableWhereData(ZONES_TABLE, "zone_country_id = '" . tep_db_input($country) . "'", "zone_country_id"))
     {
      $state_error=true;
      $error = true;
      $messageStack->add(ENTRY_STATE_ERROR_SELECT,'jobseeker_account');
     }
     elseif (strlen($state_value) <= 0)
     {
      //$state_error=true;
      //$error = true;
      //$messageStack->add(ENTRY_STATE_ERROR,'jobseeker_account');
     }
    }
    /////////  /////////// end check state ///////////////////////
    if (strlen($first_name) < MIN_FIRST_NAME_LENGTH)
    {
     $error = true;
     $messageStack->add(MIN_FIRST_NAME_ERROR,'jobseeker_account');
    }
    if (strlen($last_name) < MIN_LAST_NAME_LENGTH)
    {
     $error = true;
     $messageStack->add(MIN_LAST_NAME_ERROR,'jobseeker_account');
    }
    if (strlen($address1) < MIN_ADDRESS_LINE1_LENGTH)
    {
     $error = true;
     $messageStack->add(MIN_ADDRESS_LINE1_ERROR,'jobseeker_account');
    }
    if(is_numeric($country) == false)
    {
     $error = true;
     $messageStack->add(ENTRY_COUNTRY_ERROR,'jobseeker_account');
    }
    if (strlen($city) < MIN_CITY_LENGTH)
    {
     $error = true;
     $messageStack->add(MIN_CITY_ERROR,'jobseeker_account');
    }
    if (strlen($home_phone) < 0)
    {
     $error = true;
     $messageStack->add(ENTRY_HOME_PHONE_ERROR,'jobseeker_account');
    }

   }
   if(!$error)
   {
     $sql_data_array=array('jobseeker_privacy'=>$privacy,
                           'jobseeker_cv_searchable'=>$cv_searchable,
                           'jobseeker_first_name'=>$first_name,
                           'jobseeker_middle_name'=>$middle_name,
                           'jobseeker_last_name'=>$last_name,
                           'jobseeker_address1'=>$address1,
                           'jobseeker_address2'=>$address2,
                           'jobseeker_country_id'=>$country,
                           'jobseeker_city'=>$city,
                           'jobseeker_zip'=>$zip,
                           'jobseeker_phone'=>$home_phone,
                           'jobseeker_mobile'=>$mobile,
                           'jobseeker_newsletter'=>$newsletter,
                           );
					if($zone_id > 0)
    {
     $sql_data_array['jobseeker_state']=NULL;
     $sql_data_array['jobseeker_state_id']=$zone_id;
    }
    else
    {
     $sql_data_array['jobseeker_state']=$state_value;
     $sql_data_array['jobseeker_state_id']=0;
    }
    if(!check_login('jobseeker'))
    {
     $password= randomize();
     $sql_data_array1=array('inserted'=>'now()',
                           'jobseeker_email_address'=>$email_address,
                           'jobseeker_password'=>tep_encrypt_password($password)
                           );
     tep_db_perform(JOBSEEKER_LOGIN_TABLE, $sql_data_array1);

     $row=getAnyTableWhereData(JOBSEEKER_LOGIN_TABLE,"jobseeker_email_address='".tep_db_input($email_address)."'","jobseeker_id");
     $_SESSION['sess_jobseekerid']=$row['jobseeker_id'];
     $sql_data_array['jobseeker_id']=$_SESSION['sess_jobseekerid'];
     tep_db_perform(JOBSEEKER_TABLE, $sql_data_array);
     $template->assign_vars(array(
      'jobseeker_name'=>tep_db_output($first_name.' '.$middle_name.' '.$last_name),
      'site_title'=>tep_db_output(SITE_TITLE),
      'user_name'=>tep_db_output($email_address),
      'password'=>tep_db_output($password),
      'admin_email'=>stripslashes(CONTACT_ADMIN),
      'logo'=>'<a href="'.tep_href_link("").'">'.tep_image(PATH_TO_IMG.DEFAULT_SITE_LOGO,tep_db_output(SITE_TITLE),'','','class="internal-logo" style="width: 150px;height: 40px;object-fit: contain;"').'</a>',
      ));
     $email_text=stripslashes($template->pparse1(TEXT_LANGUAGE.'email'));
     //////
     $sql_data_array2=array('twitter_id'=>$twitter_id,
                           'user_type'=>'jobseeker',
                           'user_id'=>$_SESSION['sess_jobseekerid'],
                           );
     tep_db_perform(TWITTER_USER_TABLE,$sql_data_array2);
     //echo $email_text;die();
     tep_mail($first_name.' '.$middle_name.' '.$last_name , $email_address, NEW_JOBSEEKER_SUBJECT, $email_text, SITE_OWNER, ADMIN_EMAIL);

     $_SESSION['sess_jobseekerlogin']='y';
     $_SESSION['sess_new_jobseeker']='y';
     $_SESSION['sess_user_name']=$first_name." ".$last_name;
     $_SESSION['sess_email_address']=$email_address;
     $_SESSION['sess_password']=$password;
     unset($_SESSION['access_token']);
     tep_redirect(tep_href_link(FILENAME_JOBSEEKER_REGISTRATION_CONFIRM));
   	}
   }
  break;
 }
}
//////////////////////////////
if($error)
{
 $privacy=$privacy;
 $cv_searchable=$cv_searchable;
 $TR_first_name=$first_name;
 $TR_last_name=$last_name;
 $middle_name=$middle_name;
 $TREF_email_address=$email_address;
 $TREF_confirm_email_address=$confirm_email_address;
 $TR_address_line1=$address1;
 $TR_nationality=$nationality;
 $address_line2=$address2;
 $TR_country=$country;
 $state_value=$state_value;
 $TR_city=$city;
 $zip=$zip;
 $primary_phone=$home_phone;
 $mobile=$mobile;
 $newsletter= $newsletter;
}
else
{
 $privacy='3';
 $cv_searchable="Yes";
 $middle_name='';
 $TREF_email_address='';
 $TREF_confirm_email_address='';
 $TR_nationality='';
 $address_line2='';
	$TR_country=DEFAULT_COUNTRY_ID;
 $state_value="";
 $zip="";
 $mobile="";
 $newsletter="Yes";
}
{
$design='
				<td width="86%"><div align="center">';
}
if($messageStack->size('jobseeker_account') > 0)
 $update_message=$messageStack->output('jobseeker_account');
else
$update_message=$messageStack->output();
$template->assign_vars(array(
 'HEADING_TITLE'           => HEADING_TITLE,
 'SECTION_ACCOUNT_DETAILS' => SECTION_ACCOUNT_DETAILS,
 'SECTION_ACCOUNT_PRIVACY' => SECTION_ACCOUNT_PRIVACY,
 'SECTION_CONTACT_DETAILS' => SECTION_CONTACT_DETAILS,
 'REQUIRED_INFO'           => REQUIRED_INFO,
 'add_save_button'         => tep_image_submit(PATH_TO_BUTTON.'button_next.gif', IMAGE_NEXT),
 'registration_form'       => tep_draw_form('registration', FILENAME_JOBSEEKER_REG_TWITTER, '', 'post', 'onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action','new'),

 'INFO_TEXT_PRIVACY'       =>INFO_TEXT_PRIVACY,
 'INFO_TEXT_PRIVACY1'      =>tep_draw_radio_field('privacy', '1', '', $privacy, 'id="radio_privacy1"').'&nbsp;<label for="radio_privacy1" onMouseOver="this.style.color=\'#0000ff\'" onMouseOut="this.style.color=\'#000080\'">'.INFO_TEXT_PRIVACY_HIDE_ALL.'</label><br>'.tep_draw_radio_field('privacy', '2', '', $privacy, 'id="radio_privacy2"').'&nbsp;<label for="radio_privacy2" onMouseOver="this.style.color=\'#0000ff\'" onMouseOut="this.style.color=\'#000080\'">'.INFO_TEXT_PRIVACY_HIDE_CONTACT.'</label><br>'.tep_draw_radio_field('privacy', '3', '', $privacy, 'id="radio_privacy3"').'&nbsp;<label for="radio_privacy3" onMouseOver="this.style.color=\'#0000ff\'" onMouseOut="this.style.color=\'#000080\'">'.INFO_TEXT_PRIVACY_HIDE_NOTHING.'</label>',
 'INFO_TEXT_RESUME_SEARCHEABLE'=>INFO_TEXT_RESUME_SEARCHEABLE,
 'INFO_TEXT_RESUME_SEARCHEABLE1'=>tep_draw_radio_field('cv_searchable', 'Yes', '', $cv_searchable, 'id="radio_cv_searchable1"  ').'&nbsp;<label for="radio_cv_searchable1" onMouseOver="this.style.color=\'#0000aa\'" onMouseOut="this.style.color=\'#000000\'">'.INFO_TEXT_YES.'</label>'.tep_draw_radio_field('cv_searchable', 'No', '', $cv_searchable, 'id="radio_cv_searchable2" ').'&nbsp;<label for="radio_cv_searchable2" onMouseOver="this.style.color=\'#0000aa\'" onMouseOut="this.style.color=\'#000000\'">'.INFO_TEXT_NO.'</label>',

 'INFO_TEXT_FIRST_NAME'    => INFO_TEXT_FIRST_NAME,
 'INFO_TEXT_FIRST_NAME1'   => tep_draw_input_field('TR_first_name', $TR_first_name,'size="30"',true),
 'INFO_TEXT_MIDDLE_NAME'   => INFO_TEXT_MIDDLE_NAME,
 'INFO_TEXT_MIDDLE_NAME1'  => tep_draw_input_field('middle_name', $middle_name,'size="30"'),
 'INFO_TEXT_LAST_NAME'     => INFO_TEXT_LAST_NAME,
 'INFO_TEXT_LAST_NAME1'    => tep_draw_input_field('TR_last_name', $TR_last_name,'size="30"',true),
 'INFO_TEXT_HOME_PHONE'    => INFO_TEXT_HOME_PHONE,
 'INFO_TEXT_HOME_PHONE1'   => tep_draw_input_field('primary_phone', $primary_phone,'size="30"'),
 'INFO_TEXT_MOBILE'        => INFO_TEXT_MOBILE,
 'INFO_TEXT_MOBILE1'       => tep_draw_input_field('mobile', $mobile,'size="30"'),
// 'INFO_TEXT_MOBILE1'       => countty_code_phone_no('name="mobile_no"', 'please select ...', '0', $mobile_phone),

 'INFO_TEXT_EMAIL_ADDRESS' => INFO_TEXT_EMAIL_ADDRESS,
 'INFO_TEXT_EMAIL_ADDRESS1'=> tep_draw_input_field('TREF_email_address', $TREF_email_address,'size="30"',true),
 'INFO_TEXT_CONFIRM_EMAIL_ADDRESS' => INFO_TEXT_CONFIRM_EMAIL_ADDRESS,
 'INFO_TEXT_CONFIRM_EMAIL_ADDRESS1'=> tep_draw_input_field('TREF_confirm_email_address', $TREF_confirm_email_address,'size="30"',true),
 'INFO_TEXT_ADDRESS1'      => INFO_TEXT_ADDRESS1,
 'INFO_TEXT_ADDRESS11'     => tep_draw_input_field('TR_address_line1', $TR_address_line1,'size="30"',true),
 'INFO_TEXT_ADDRESS2'      => INFO_TEXT_ADDRESS2,
 'INFO_TEXT_ADDRESS21'     => tep_draw_input_field('address_line2', $address_line2,'size="30"',false),
 'INFO_TEXT_NATIONALITY'   => INFO_TEXT_NATIONALITY,
 'INFO_TEXT_NATIONALITY1'  => tep_draw_input_field('TR_nationality', $TR_nationality,'size="30"',true),
 'INFO_TEXT_COUNTRY'       => INFO_TEXT_COUNTRY,
 'INFO_TEXT_COUNTRY1'      => tep_get_country_list('TR_country',$TR_country)." <span class='inputRequirement'>*</span>",
 'INFO_TEXT_STATE'         => INFO_TEXT_STATE,
 'INFO_TEXT_STATE1'        => LIST_SET_DATA(ZONES_TABLE,"",'zone_name','zone_id',"zone_name",'name="state"',"state",'',$state_value)." ".tep_draw_input_field('state1', is_numeric($state_value)?'': $state_value,'size="25"',false),

 'INFO_TEXT_CITY'          => INFO_TEXT_CITY,
 'INFO_TEXT_CITY1'         => tep_draw_input_field('TR_city', $TR_city,'size="30"',true),
 'INFO_TEXT_ZIP'           => INFO_TEXT_ZIP,
 'INFO_TEXT_ZIP1'          => tep_draw_input_field('zip', $zip,'size="30"',false),
 'INFO_TEXT_NEWS_LETTER'   => INFO_TEXT_NEWS_LETTER,
 'INFO_TEXT_NEWS_LETTER1'  => tep_draw_checkbox_field('newsletter', 'Yes', '',$newsletter,'id="checkbox_news"')."&nbsp;<span class='small'><label for='checkbox_news'>".INFO_TEXT_SUBSCRIBE."</label></div>",
 'INFO_TEXT_AGREEMENT'     => INFO_TEXT_AGREEMENT,
 'design'                  => $design,
 'INFO_TEXT_JSCRIPT_FILE'  => $jscript_file,

 'COUNTRY_STATE_SCRIPT'    => country_state($c_name='TR_country',$c_d_value=INFO_TEXT_PLEASE_SELECT_COUNTRY.'...',$s_name='state',$s_d_value='state','zone_id',$state_value),
 'LEFT_BOX_WIDTH'          => LEFT_BOX_WIDTH1,
 'RIGHT_BOX_WIDTH'         => RIGHT_BOX_WIDTH1,
 'LEFT_HTML'               => LEFT_HTML,
 'LEFT_HTML_JOBSEEKER'     => LEFT_HTML_JOBSEEKER,
 'RIGHT_HTML'              => RIGHT_HTML,
 'update_message'=>$update_message));
$template->pparse('registration');
?>