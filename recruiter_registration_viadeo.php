<?
/*
***********************************************************
***********************************************************
**********#	Name				      : Shambhu Prasad Patnaik#********
**********#	Company			    : Aynsoft	Pvt. Ltd.   #**********
**********#	Copyright (c) www.aynsoft.com 2004	#***********
***********************************************************
***********************************************************
*/
include_once("include_files.php");
include_once("general_functions/password_funcs.php");
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_RECRUITER_REG_VIADEO);
$template->set_filenames(array('registration' => 'recruiter_registration_viadeo.htm','email'=>'recruiter_registration_template.htm','de_email'=>'de_recruiter_registration_template.htm'));
$jscript_file=PATH_TO_LANGUAGE.$language."/jscript/".'recruiter_registation.js';
include_once(FILENAME_BODY);
include_once "class/viadeooauth.php";
//////
if(isset($_SESSION['sess_recruiteruserid']))
{
 $messageStack->add_session(ACCESS_DENIED, 'error');
 tep_redirect(FILENAME_RECRUITER_CONTROL_PANEL);
}
//////
//$state_error=false;
$action = (isset($_POST['action']) ? $_POST['action'] : '');
if(check_login('recruiter'))
{
 tep_redirect(FILENAME_RECRUITER_REGISTRATION);
}
if(MODULE_VIADEO_PLUGIN!='enable')
{
 unset($_SESSION['access_token']);
 tep_redirect(FILENAME_INDEX);
}
$viadeo_app_key  = MODULE_VIADEO_PLUGIN_APP_KEY;
$viadeo_app_secret = check_data1(MODULE_VIADEO_PLUGIN_APP_SECRET_KEY,'##@##','app','passw');
if($viadeo_app_secret==-1)
$viadeo_app_secret= '';
if($viadeo_app_key=='' || $viadeo_app_secret=='' || MODULE_VIADEO_PLUGIN!='enable')
{
 unset($_SESSION['access_token']);
 tep_redirect(FILENAME_INDEX);
}
$viadeo_id='';
if(isset($_SESSION['access_token']))
{
 $callback_url='';
 $access_token1=$_SESSION['access_token'];
 $callback_url='';
 $connection = new ViadeoOAuth($viadeo_app_key,$viadeo_app_secret,$callback_url);
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
  tep_redirect(FILENAME_INDEX);
 }
 $viadeo_id = $content['id'];
 if($row_linked=getAnyTableWhereData(VIADEO_USER_TABLE,"viadeo_id='".tep_db_input($viadeo_id)."'","user_type,user_id"))
 {
  tep_redirect(tep_href_link(FILENAME_VIADEO_APPLICATION,'request=login_info&user_type='.$row_linked['user_type']));
 }
 if(MODULE_VIADEO_PLUGIN_RECRUITER!='enable')
 {
  $_SESSION['viadeo_error']='through viadeo recruiter registration disable by admin.use normal way to register';
  unset($_SESSION['access_token']);
  tep_redirect(FILENAME_RECRUITER_REGISTRATION);
 }
 if(!tep_not_null($action))
 {
  $TR_first_name    = tep_db_prepare_input($content['first_name']);
  $TR_last_name     = tep_db_prepare_input($content['last_name']);
  $viadeo_location  = tep_db_prepare_input($content['location']);
  $city             = tep_db_prepare_input($viadeo_location['city']);
  $viadeo_country   = tep_db_prepare_input($viadeo_location['country']);
  $viadeo_country_c = tep_db_prepare_input($viadeo_location['country_code']);
  $TR_address_line1 = tep_db_prepare_input($viadeo_location['address']);
  $zip_code         = tep_db_prepare_input($viadeo_location['zipcode']);
  if($row_c=getAnyTableWhereData(COUNTRIES_TABLE,"country_code='".tep_db_input($viadeo_country_c)."'","id"))
  $TR_country=$row_c['id'];
 }
}
else
{
 tep_redirect(tep_href_link(FILENAME_VIADEO_APPLICATION,'user_type=recruiter'));
}
//print_r($_POST);
//////////////////////////////
// add/edit
if(tep_not_null($action))
{
 switch($action)
 {
  case 'new':
   $first_name         = tep_db_prepare_input($_POST['TR_first_name']);
   $last_name          = tep_db_prepare_input($_POST['TR_last_name']);
   $email_address      = tep_db_prepare_input($_POST['TREF_email_address']);
   $confirm_email_address=tep_db_prepare_input($_POST['TREF_confirm_email_address']);
   $position           = tep_db_prepare_input($_POST['TR_position']);
   $company_name        = tep_db_prepare_input($_POST['TR_company_name']);
   $address1           = tep_db_prepare_input($_POST['TR_address_line1']);
   $address2           = tep_db_prepare_input($_POST['address_line2']);
   $city               = tep_db_prepare_input($_POST['city']);
   $country            = (int)tep_db_prepare_input($_POST['TR_country']);

   if(isset($_POST['state']) and $_POST['state']!='')
   $state_value        =tep_db_prepare_input($_POST['state']);
   elseif(isset($_POST['state1']))
   $state_value        =tep_db_prepare_input($_POST['state1']);

   $zip_code           = tep_db_prepare_input($_POST['TR_zip_code']);
   $telephone          = tep_db_prepare_input($_POST['TR_telephone_number']);
   $fax                = tep_db_prepare_input($_POST['fax']);
   $url                = tep_db_prepare_input($_POST['url']);
   $newsletter         = tep_db_prepare_input($_POST['newsletter']);
   $error              = false;

   //Check
   if($row=getAnyTableWhereData(JOBSEEKER_LOGIN_TABLE,"jobseeker_email_address='".tep_db_input($email_address)."'","jobseeker_id"))
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
    if($row=getAnyTableWhereData(RECRUITER_LOGIN_TABLE,"recruiter_email_address='".tep_db_input($email_address)."'","recruiter_id"))
    {
     $error = true;
     $messageStack->add(EMAIL_ADDRESS_ERROR,'recruiter_account');
    }
   }
   if(!$error)
   {
    if (strlen($first_name) < MIN_FIRST_NAME_LENGTH)
    {
     $error = true;
     $messageStack->add(MIN_FIRST_NAME_ERROR,'recruiter_account');
    }
    if (strlen($last_name) < MIN_LAST_NAME_LENGTH)
    {
     $error = true;
     $messageStack->add(MIN_LAST_NAME_ERROR,'recruiter_account');
    }
    $error_email=false;
    if(tep_validate_email($email_address) == false)
    {
     $error_email=true;
     $error = true;
     $messageStack->add(EMAIL_ADDRESS_INVALID_ERROR,'recruiter_account');
    }
    if(tep_validate_email($confirm_email_address) == false)
    {
     $error_email=true;
     $error = true;
     $messageStack->add(CONFIRM_EMAIL_ADDRESS_INVALID_ERROR,'recruiter_account');
    }
    if(!$error_email)
    {
     if($email_address!=$confirm_email_address)
     {
      $error = true;
      $messageStack->add(EMAIL_ADDRESS_MATCH_ERROR,'recruiter_account');
     }
    }
    //////recruiter position check
    if (strlen($position) <= 0)
    {
     $error = true;
     $messageStack->add(POSITION_ERROR,'recruiter_account');
    }
    //////company name check
    if (strlen($company_name) < MIN_COMPANY_NAME_LENGTH)
    {
     $error = true;
     $messageStack->add(MIN_COMPANY_NAME_ERROR,'recruiter_account');
    }
    //////Address Line error check
    if (strlen($address1) < MIN_ADDRESS_LINE1_LENGTH)
    {
     $error = true;
     $messageStack->add(MIN_ADDRESS_LINE1_ERROR,'recruiter_account');
    }
    if(is_numeric($country) == false)
    {
     $error = true;
     $messageStack->add('create_account', ENTRY_COUNTRY_ERROR);
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
      // $error = true;
       //$messageStack->add(ENTRY_STATE_ERROR_SELECT,'recruiter_account');
      }
     }
     else
     {
      // $state_error=true;
       //$error = true;
      // $messageStack->add(ENTRY_STATE_ERROR_SELECT,'recruiter_account');
     }
    }
    else
    {
     if(tep_not_null($state_value))
     if($row11 = getAnyTableWhereData(ZONES_TABLE, "zone_country_id = '" . tep_db_input($country) . "'", "zone_country_id"))
     {
      $state_error=true;
      $error = true;
      $messageStack->add(ENTRY_STATE_ERROR_SELECT,'recruiter_account');
     }
     //if (strlen($state_value) <= 0)
     //{
      //$state_error=true;
      //$error = true;
      //$messageStack->add(ENTRY_STATE_ERROR,'recruiter_account');
     //}
    }
    ////// Zip code error check
    if (strlen($zip_code) <= 0)
    {
     $error = true;
     $messageStack->add(ZIP_CODE_ERROR,'recruiter_account');
    }
    //////Telephone error check
    if (strlen($telephone) <= 0)
    {
     $error = true;
     $messageStack->add(TELEPHONE_ERROR,'recruiter_account');
    }

   //////// logo upload starts //////
    ///*
	   if((strlen(tep_not_null($_FILES['my_photo']['size'])) <= 0))
    {
   	 if($action=='new')
     {
      $error = true;
      $messageStack->add(LOGO_UPLOAD_ERROR,'recruiter_account');
     }
	   }
    //*/
    //////// logo upload ends //////
   }
   if(!$error)
   {
    $logo='';
    if(tep_not_null($_FILES['my_photo']['name']))
    {
	    if((substr($_FILES['my_photo']['name'],-4)=='.gif') || (substr($_FILES['my_photo']['name'],-4)=='.jpg') || (substr($_FILES['my_photo']['name'],-4)=='.png') || (substr($_FILES['my_photo']['name'],-5)=='.jpeg'))
	    {
      if($obj_logo = new upload('my_photo', PATH_TO_MAIN_PHYSICAL_LOGO,'644',array('gif','jpg','png','jpeg')))
      {
       $logo=tep_db_input($obj_logo->filename);
      }
	     else
      {
       $error=true;
      }
	    }
	    else
     {
	     $error = true;
      $messageStack->add(LOGO_UPLOAD_TYPE_ERROR,'recruiter_account');
	    }
    }
   }
   if(!$error)
   {
     $sql_data_array=array('recruiter_first_name'  => $first_name,
                           'recruiter_last_name'   => $last_name,
                           'recruiter_position'    => $position,
                           'recruiter_company_name'=> $company_name,
                           'recruiter_address1'    => $address1,
                           'recruiter_address2'    => $address2,
                           'recruiter_city'        => $city,
                           'recruiter_country_id'  => $country,
                           'recruiter_zip'         => $zip_code,
                           'recruiter_telephone'   => $telephone,
                           'fax'                   => $fax,
                           'recruiter_url'         => ($url=='http://')?"":$url,
                           'recruiter_newsletter'  => $newsletter
                           );
    if($zone_id > 0)
    {
     $sql_data_array['recruiter_state']='null';
     $sql_data_array['recruiter_state_id']=$zone_id;
    }
    else
    {
     $sql_data_array['recruiter_state']=$state_value;
     $sql_data_array['recruiter_state_id']=0;
    }
    if($logo!='')
    {
     $sql_data_array['recruiter_logo']=$logo;
    }
    if(!check_login('recruiter'))
    {
     $password = randomize();
     $recruiter_password=tep_encrypt_password($password);
     $sql_data_array1=array('inserted'=>'now()',
                           'recruiter_email_address'=>$email_address,
                           'recruiter_password'=>$recruiter_password
                           );
     tep_db_perform(RECRUITER_LOGIN_TABLE, $sql_data_array1);
     $row=getAnyTableWhereData(RECRUITER_LOGIN_TABLE,"recruiter_email_address='".tep_db_input($email_address)."'","recruiter_id");
     $_SESSION['sess_recruiterid']=$row['recruiter_id'];
     $sql_data_array['recruiter_id']=$_SESSION['sess_recruiterid'];
     tep_db_perform(RECRUITER_TABLE, $sql_data_array);
     $sql_data_array2=array('viadeo_id'=>$viadeo_id,
                           'user_type'=>'recruiter',
                           'user_id'=>$_SESSION['sess_recruiterid'],
                           );
     tep_db_perform(VIADEO_USER_TABLE,$sql_data_array2);

 	   //$messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');
     $template->assign_vars(array(
      'recruiter_name'=>tep_db_output($first_name.' '.$last_name),
      'site_title'=>tep_db_output(SITE_TITLE),
      'user_name'=>tep_db_output($email_address),
      'password'=>tep_db_output($password),
      'admin_email'=>stripslashes(CONTACT_ADMIN),
      'logo'=>'<a href="'.tep_href_link("").'">'.tep_image(PATH_TO_IMG.DEFAULT_SITE_LOGO,tep_db_output(SITE_TITLE),'','','class="internal-logo" style="width: 150px;height: 40px;object-fit: contain;"').'</a>',

      ));
     $email_text=stripslashes($template->pparse1(TEXT_LANGUAGE.'email'));
     //echo $email_text;die();
     tep_mail($first_name.' '.$last_name , $email_address, NEW_RECRUITER_SUBJECT, $email_text, SITE_OWNER, ADMIN_EMAIL);

     $_SESSION['sess_recruiterlogin']='y';
     $_SESSION['sess_new_recruiter']='y';
     $_SESSION['sess_email_address']=$email_address;
     $_SESSION['sess_password']=$password;
     $_SESSION['sess_user_name']=$first_name." ".$last_name;
     unset($_SESSION['access_token']);
     tep_redirect(tep_href_link(FILENAME_RECRUITER_REGISTRATION_CONFIRM));
    }
   }
  break;
 }
}
//////////////////////////////
{
 $add_save_button=tep_image_submit(PATH_TO_BUTTON.'button_insert.gif', IMAGE_INSERT);
 $registration_form=tep_draw_form('registration', FILENAME_RECRUITER_REG_VIADEO, '', 'post', 'enctype="multipart/form-data" onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action','new');
}
if($error)
{
 $TR_first_name             = $first_name;
 $TR_last_name              = $last_name;
 $TREF_email_address        = $email_address;
 $TREF_confirm_email_address= $confirm_email_address;
 $TR_position        = $position;
 $TR_company_name    = $company_name;
 $TR_address_line1   = $address1;
 $address_line2      = $address2;
 $TR_country         = $country;
 $city               = $city;
 $state_value        = $state_value;
 $TR_zip_code        = $zip_code;
 $TR_telephone_number= $telephone;
 $fax                = $fax;
 $url                = $url;
 $newsletter         = $newsletter;
}
else
{
 $TREF_email_address= '';
 $TREF_confirm_email_address='';
 $TR_position       = '';
 $TR_company_name   = '';
 $address_line2     ='';
 $state_value       = "";
 $TR_zip_code       = "";
 $fax               = "";
 $url               = 'http://';
 $newsletter        = "Yes";
}
{
$design='
				<td width="86%"><div align="center">';
}

if($messageStack->size('recruiter_account') > 0)
 $update_message=$messageStack->output('recruiter_account');
else
 $update_message=$messageStack->output();

$template->assign_vars(array(
 'HEADING_TITLE'=>HEADING_TITLE,
 'SECTION_ACCOUNT_DETAILS'=>SECTION_ACCOUNT_DETAILS,
 'SECTION_CONTACT_DETAILS'=>SECTION_CONTACT_DETAILS,
 'SECTION_COMPANY'=>SECTION_COMPANY,
 'REQUIRED_INFO'=>REQUIRED_INFO,
 'add_save_button'=>$add_save_button,
 'registration_form'=>$registration_form,
 'INFO_TEXT_FIRST_NAME'=>INFO_TEXT_FIRST_NAME,
 'INFO_TEXT_FIRST_NAME1'=>tep_draw_input_field('TR_first_name', $TR_first_name,'size="50"',true),
 'INFO_TEXT_LAST_NAME'=>INFO_TEXT_LAST_NAME,
 'INFO_TEXT_LAST_NAME1'=>tep_draw_input_field('TR_last_name', $TR_last_name,'size="50"',true),
 'INFO_TEXT_EMAIL_ADDRESS'=>INFO_TEXT_EMAIL_ADDRESS,
 'INFO_TEXT_EMAIL_ADDRESS1'=>tep_draw_input_field('TREF_email_address', $TREF_email_address,'size="50" maxlength="50"',true),
 'INFO_TEXT_CONFIRM_EMAIL_ADDRESS'=>INFO_TEXT_CONFIRM_EMAIL_ADDRESS,
 'INFO_TEXT_CONFIRM_EMAIL_ADDRESS1'=>tep_draw_input_field('TREF_confirm_email_address', $TREF_confirm_email_address,'size="50" maxlength="50"',true),
 //////// Company Detail //////////////////////////////
 'INFO_TEXT_POSITION'=>INFO_TEXT_POSITION,
 'INFO_TEXT_POSITION1'=>tep_draw_input_field('TR_position', $TR_position,'size="50"',true),
 'INFO_TEXT_COMPANY_NAME'=>INFO_TEXT_COMPANY_NAME,
 'INFO_TEXT_COMPANY_NAME1'=>tep_draw_input_field('TR_company_name', $TR_company_name,'size="50"',true),
 'INFO_TEXT_ADDRESS1'=>INFO_TEXT_ADDRESS1,
 'INFO_TEXT_ADDRESS11'=>tep_draw_input_field('TR_address_line1', $TR_address_line1,'size="50"',true),
 'INFO_TEXT_ADDRESS2'=>INFO_TEXT_ADDRESS2,
 'INFO_TEXT_ADDRESS21'=>tep_draw_input_field('address_line2', $address_line2,'size="50"',false),
 'INFO_TEXT_CITY'=>INFO_TEXT_CITY,
 'INFO_TEXT_CITY1'=>tep_draw_input_field('city', $city,'size="50"'),
 'INFO_TEXT_COUNTRY'=>INFO_TEXT_COUNTRY,
 'INFO_TEXT_COUNTRY1'=>tep_get_country_list('TR_country',$TR_country)." <span class='inputRequirement'>*</span>",
 'INFO_TEXT_STATE'=>INFO_TEXT_STATE,
 'INFO_TEXT_STATE1'=>LIST_SET_DATA(ZONES_TABLE,"",'zone_name','zone_id',"zone_name",'name="state"',"state",'',$state_value)." ".tep_draw_input_field('state1',is_numeric($state_value)?'': $state_value,'size="25"',false),
 'INFO_TEXT_ZIP_CODE'=>INFO_TEXT_ZIP_CODE,
 'INFO_TEXT_ZIP_CODE1'=>tep_draw_input_field('TR_zip_code', $TR_zip_code,'size="50"',true),
 'INFO_TEXT_TELEPHONE'=>INFO_TEXT_TELEPHONE,
 'INFO_TEXT_TELEPHONE1'=>tep_draw_input_field('TR_telephone_number', $TR_telephone_number,'size="50"',true),
 'INFO_TEXT_FAX'=>INFO_TEXT_FAX,
 'INFO_TEXT_FAX1'=>tep_draw_input_field('fax', $fax,'size="50"'),
 'INFO_TEXT_PHOTO'=>INFO_TEXT_PHOTO,
 'INFO_TEXT_PHOTO1'=>tep_draw_file_field("my_photo").$logo." <span class='inputRequirement'>*</span>",
 'INFO_TEXT_URL'=>INFO_TEXT_URL,
 'INFO_TEXT_URL1'=>tep_draw_input_field('url', $url,'size="50"',false),
 'INFO_TEXT_NEWS_LETTER'=>INFO_TEXT_NEWS_LETTER,
 'INFO_TEXT_AGREEMENT'     => INFO_TEXT_AGREEMENT,

 'INFO_TEXT_NEWS_LETTER1'=>tep_draw_checkbox_field('newsletter', 'Yes', '',$newsletter,'id="checkbox_news"')."&nbsp;<span class='small'><label for='checkbox_news' class='label'>Subscribe</label></div>",
 'SCRIPT'                => country_state($c_name='TR_country',$c_d_value='Please select a countries...',$s_name='state',$s_d_value='state','zone_id',$state_value),
 'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
 'INFO_TEXT_JSCRIPT_FILE'  =>$jscript_file,
	'design'        =>$design,
 'RIGHT_BOX_WIDTH'=>RIGHT_BOX_WIDTH1,
 'LEFT_HTML'=>LEFT_HTML,
 'RIGHT_HTML'=>RIGHT_HTML,
 'update_message'=>$update_message));
$template->pparse('registration');
?>