<?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik   #*****
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2013  #**********
***********************************************************
*/
include_once("../include_files.php");
include_once("../general_functions/password_funcs.php");
include_once("../general_functions/app_functions.php");

$action = (isset($_POST['act']) ? $_POST['act'] : '');
if ($action=='register') 
{
 $email_address      = tep_db_prepare_input($_POST['email_address']);
	$jobseeker_password = tep_db_prepare_input($_POST['password']);
 $first_name         = tep_db_prepare_input($_POST['first_name']);
 $last_name          = tep_db_prepare_input($_POST['last_name']);
 $address1           = tep_db_prepare_input($_POST['address1']);
 $country            = tep_db_prepare_input($_POST['country']);
 $city               = tep_db_prepare_input($_POST['city']);
 $state              = tep_db_prepare_input($_POST['state']);
 $mobile             = tep_db_prepare_input($_POST['mobile']);
	$error =false;
	$reg =false;
	$errorMsg=array();
 if(tep_validate_email($email_address) == false)
	{
 	$error =true;
  $errorMsg [] ='email address';
	}
 if(!check_login('jobseeker'))
	{
	 if(!tep_not_null($jobseeker_password))
	 {
 	 $error =true;
   $errorMsg [] ='password';
	 }
  elseif(strlen($jobseeker_password) < MIN_PASSWORD_LENGTH) 
	 {
 	 $error =true;
   $errorMsg [] ='Password must contain a minimum of ' . MIN_PASSWORD_LENGTH . ' characters.';
	 }}
	if($row=getAnyTableWhereData(RECRUITER_LOGIN_TABLE,"recruiter_email_address='".tep_db_input($email_address)."'","recruiter_id"))
 {
  $error = true;
  $errorMsg [] ='Email Address already exists';
 }
 else if($row=getAnyTableWhereData(RECRUITER_USERS_TABLE,"email_address='".tep_db_input($email_address)."'","id"))
 {
  $error = true;
  $errorMsg [] ='Email Address already exists';
 }
 else if($row=getAnyTableWhereData(ADMIN_TABLE,"admin_email_address='".tep_db_input($email_address)."'","admin_id"))
 {
  $error = true;
  $errorMsg [] ='Email Address already exists';
 }
	elseif(check_login('jobseeker'))
	{
  if($row=getAnyTableWhereData(JOBSEEKER_LOGIN_TABLE,"jobseeker_email_address='".tep_db_input($email_address)."' and jobseeker_id!='".$_SESSION['sess_jobseekerid']."'","jobseeker_id"))
  {
   $error = true;
   $errorMsg [] ='Email Address already exists';
  }
 }
	elseif($row=getAnyTableWhereData(JOBSEEKER_LOGIN_TABLE,"jobseeker_email_address='".tep_db_input($email_address)."'","jobseeker_id"))
 {
 	$reg =true;
  $error = true;
  $errorMsg [] ='Email Address already exists';
 }
 if (strlen($first_name) < MIN_FIRST_NAME_LENGTH) 
 {
  $error = true;
  $errorMsg [] ='Name must contain a minimum of ' . MIN_FIRST_NAME_LENGTH . ' characters.';
 }
 if (strlen($address1) < MIN_ADDRESS_LINE1_LENGTH) 
	{
  $error = true;
  $errorMsg [] ='address  must contain a minimum of ' . MIN_ADDRESS_LINE1_LENGTH . ' characters.';
	}
 if(is_numeric($country) == false) 
 {
  $error = true;
  $errorMsg [] ='country';
	}
	if (strlen($city) < MIN_CITY_LENGTH) 
 {
  $error = true;
  $errorMsg [] ='city must contain a minimum of ' . MIN_CITY_LENGTH . ' characters.';
 }
 if(!$error)
	{
  $sql_data_array=array( 'jobseeker_first_name'=>$first_name,
                      			'jobseeker_last_name'=>$last_name,
                         'jobseeker_address1'=>$address1,
                         'jobseeker_country_id'=>$country,
                         'jobseeker_city'=>$city,
                         'jobseeker_mobile'=>$mobile,
                        );
 	if($check_state = getAnyTableWhereData(ZONES_TABLE, " zone_name  = '" . tep_db_input($state) . "'", "zone_id"))
  {
   $sql_data_array['jobseeker_state']=NULL;
   $sql_data_array['jobseeker_state_id']=$check_state['zone_id'];
	 }
  else
		{
			$sql_data_array['jobseeker_state']=$state;
   $sql_data_array['jobseeker_state_id']=0;
  }
  if(check_login('jobseeker'))
		{
   tep_db_query('update '.JOBSEEKER_LOGIN_TABLE ." set updated='".date("Y-m-d H:i:s")."', jobseeker_email_address='$email_address' where jobseeker_id = '" . $_SESSION['sess_jobseekerid'] . "'");
   tep_db_perform(JOBSEEKER_TABLE, $sql_data_array, 'update', "jobseeker_id = '" . $_SESSION['sess_jobseekerid'] . "'");
	  tep_redirect(tep_href_link('app/jobseeker_info1/'));
  }
		else
		{
   $sql_data_array1=array('inserted'=>'now()',
                           'jobseeker_email_address'=>$email_address,
                           'jobseeker_password'=>tep_encrypt_password($jobseeker_password)
                          );
   tep_db_perform(JOBSEEKER_LOGIN_TABLE, $sql_data_array1);
   $row=getAnyTableWhereData(JOBSEEKER_LOGIN_TABLE,"jobseeker_email_address='".tep_db_input($email_address)."'","jobseeker_id");
   $_SESSION['sess_jobseekerid']=$row['jobseeker_id'];
   $sql_data_array['jobseeker_id']=$_SESSION['sess_jobseekerid'];
   tep_db_perform(JOBSEEKER_TABLE, $sql_data_array);			
 		//////////email
			$template1 = new Template(PATH_TO_MAIN_PHYSICAL.PATH_TO_TEMPLATE);
   $template1->set_filenames(array('email'=>'jobseeker_registration_template.htm'));
   $template1->assign_vars(array(
      'jobseeker_name'=>tep_db_output($first_name.' '.$last_name),
      'site_title'=>tep_db_output(SITE_TITLE),
      'user_name'=>tep_db_output($email_address),
      'password'=>tep_db_output($jobseeker_password),
      'admin_email'=>stripslashes(CONTACT_ADMIN),
      'logo'=>'<a href="'.tep_href_link("").'">'.tep_image(PATH_TO_IMG.DEFAULT_SITE_LOGO,tep_db_output(SITE_TITLE)).'</a>',
      ));

   $email_text=stripslashes($template1->pparse1('email'));
   tep_mail($first_name.' '.$last_name , $email_address, 'Thank you for registering on '.SITE_TITLE, $email_text, SITE_OWNER,EMAIL_FROM);
   ////////////////

   $_SESSION['sess_jobseekerlogin']='y';
	  tep_redirect(tep_href_link('app/success.php'));
 	}	
	}
	if($error)
	{
		//$data =array();
  //$data['result']['status'] = 'error';
  //$data['result']['message'] = $errorMsg;
  //header('Content-Type: application/json'); 
  //$json = json_encode($data);
	 //echo $json; 
		$message=''; 	
		foreach($errorMsg as $msg)
  $message .=$msg.",\n";
  echo substr($message,0,-2);
	}
}
else
{
	echo 'Action';
	die();
	/*
	header('Content-Type: application/json'); 
	$data =array();
 $data['result']['status'] = 'error';
 $data['result']['message'] = 'Invalid Authentication';
 $json = json_encode($data);
	echo $json; 
	*/
}
?>