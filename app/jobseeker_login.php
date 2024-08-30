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
if ($action=='check') 
{
 $email_address=tep_db_prepare_input($_POST['email_address']);
	$jobseeker_password=$_POST['password'];
	$error =false;
	$reg =false;
	$errorMsg=array();
 if(tep_validate_email($email_address) == false)
	{
 	$error =true;
  $errorMsg [] ='invalid email-address';
	}
	if(!tep_not_null($jobseeker_password))
	{
 	$error =true;
  $errorMsg [] ='invalid password';
	}
 elseif(strlen($jobseeker_password) < MIN_PASSWORD_LENGTH) 
	{
 	$error =true;
  $errorMsg [] =' password must contain a minimum of ' . MIN_PASSWORD_LENGTH . ' characters.';
	}
	if(!$error)
	{
 	$whereClause="jl.jobseeker_email_address='".tep_db_input($email_address)."' and jl.jobseeker_id=j.jobseeker_id";
  $fields='jl.jobseeker_id,concat(j.jobseeker_first_name," ",j.jobseeker_last_name) as name,jl.jobseeker_email_address,jl.jobseeker_password,jl.ip_address,jl.number_of_logon,jl.jobseeker_status';
	 if($row=getAnyTableWhereData(JOBSEEKER_LOGIN_TABLE.' as jl left outer join  '.JOBSEEKER_TABLE.' as j on (jl.jobseeker_id=j.jobseeker_id) ',$whereClause,$fields,false))
		{
			$reg =true;
   if($row['jobseeker_status']!='Yes')
			{
   	$error =true;
    $errorMsg [] ='Account Deactivated';
			}
   if(!tep_validate_password($jobseeker_password, $row['jobseeker_password'])) 
			{
				$error =true;
    $errorMsg [] ='Invalid Authentication';
			}
   else
			{
    $ip_address=tep_get_ip_address();
    $last_ip_address=tep_db_prepare_input($row['ip_address']);
    $number_of_logon=$row['number_of_logon']+1;
    $sql_data_array = array('last_login_time' => 'now()',
                           'ip_address' => $ip_address,
                           'last_ip_address' => $last_ip_address,
                           'number_of_logon' => $number_of_logon);
    tep_db_perform(JOBSEEKER_LOGIN_TABLE, $sql_data_array, 'update', "jobseeker_id = '" . $row['jobseeker_id'] . "'");
    $language=$_SESSION['language'];
	 		$language_id=$_SESSION['languages_id'];
    @session_unset();
    @session_destroy();
   session_start();
			 //$_SESSION['sess_jobseekerlogin']="y";
    //$_SESSION['sess_jobseekerid']=$row["jobseeker_id"];
			 $_SESSION['language']=$language;
			 $_SESSION['languages_id']=$language_id;
	   $_SESSION['sess_access_key']= get_access_key($row['jobseeker_id'],$ip_address);

	   tep_redirect(tep_href_link('app/jobseeker_info1/'));
   }
		}
		else
		{
  	$error =true;
   $errorMsg [] ='Invalid Authentication';
		}
	}
	if($error)
	{
 	if(!$reg)
		echo 'unregistered';
		else
		echo 'faild';
 	die();
	/*
		$message='<error>'."\n"; 	
		$message .='<status>error</status>'."\n";
  foreach($errorMsg as $msg)
		{
   $message .='<message>'.$msg.'</message>'."\n";
		}
		$message.='</error>'; 	
		header('Content-Type: text/xml'); 
	echo $message;
  //echo '<error>'."\n".'<message>invalid Action</message>'."\n".'</error>';
		*/
	}
}
else
{
	echo 'faild';
	die();
	/*header('Content-Type: text/xml'); 
 $message='<error>'."\n";
 $message .='<status>error</status>'."\n";
 $message .='<message>invalid Action</message>'."\n";
	$message.='</error>'; 	
	echo $message;
	*/
}
?>