<?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik   #*****
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2013  #**********
***********************************************************
*/
include_once("../include_files.php");
include_once("../include_files.php");
include_once("../general_functions/app_functions.php");
if(isset($_SESSION['sess_access_key']))
{
 $access_key  = tep_db_prepare_input($_SESSION['sess_access_key']);
}
else
 $access_key  = tep_db_prepare_input($_POST['access_key']);

$userInfo = false;
if($recruiter_id =get_access_user($access_key,'recruiter'))
{
  if(isset($_SESSION['sess_access_key']))
 {
  unset($_SESSION['sess_access_key']);
 }
 $fielsd='rl.recruiter_id,rl.recruiter_email_address,r.recruiter_first_name,r.recruiter_last_name';
 if($row=getAnyTableWhereData(RECRUITER_LOGIN_TABLE.' as rl left outer join  '.RECRUITER_TABLE.' as r on (rl.recruiter_id=r.recruiter_id)   '," rl.recruiter_id ='".$recruiter_id."'",$fielsd))
  {
   $message ='<user>'."\n";
   $message .='<status>success</status>'."\n";
   $message .='<access_key>'.$access_key.'</access_key>'."\n";
   $message .='<first_name>'.tep_db_output($row['recruiter_first_name']).'</first_name>'."\n";
   $message .='<last_name>'.tep_db_output($row['recruiter_last_name']).'</last_name>'."\n";
   $message .='</user>';
   header('Content-Type: text/xml'); 
   echo $message; 
  }
}
else
{
 header('Content-Type: text/xml'); 
 $message='<error>'."\n";
 $message .='<status>error</status>'."\n";
 $message .='<message>Invalid Authentication</message>'."\n";
 $message.='</error>'; 	
 echo $message;
}
?>