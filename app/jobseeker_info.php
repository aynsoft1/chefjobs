<?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik   #*****
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2013  #**********
***********************************************************
*/
include_once("../include_files.php");
if(check_login("jobseeker"))
{
	$fielsd='jl.jobseeker_id,jl.jobseeker_email_address,j.jobseeker_first_name,j.jobseeker_last_name,jobseeker_address1,jobseeker_address2,jobseeker_country_id,jobseeker_city,jobseeker_phone,jobseeker_mobile, if(jobseeker_state_id,z.zone_name,jobseeker_state) as j_state';
 if($row=getAnyTableWhereData(JOBSEEKER_LOGIN_TABLE.' as jl left outer join  '.JOBSEEKER_TABLE.' as j on (jl.jobseeker_id=j.jobseeker_id)  left  outer join '.ZONES_TABLE.' as z on (j.jobseeker_state_id =z.zone_id) '," jl.jobseeker_id ='".$_SESSION['sess_jobseekerid']."'",$fielsd))
 {
		$message ='<user>'."\n";
  $message .='<status>success</status>'."\n";
 	$message .='<id>'.$row['jobseeker_id'].'</id>'."\n";
		$message .='<email_address>'.$row['jobseeker_email_address'].'</email_address>'."\n";
		$message .='<first_name>'.tep_db_output($row['jobseeker_first_name']).'</first_name>'."\n";
		$message .='<last_name>'.tep_db_output($row['jobseeker_last_name']).'</last_name>'."\n";
		$message .='<address1>'.tep_db_output($row['jobseeker_address1']).'</address1>'."\n";
		$message .='<address2>'.tep_db_output($row['jobseeker_address2']).'</address2>'."\n";
		$message .='<country_id>'.tep_db_output($row['jobseeker_country_id']).'</country_id>'."\n";
		$message .='<state>'.tep_db_output($row['j_state']).'</state>'."\n";
		$message .='<city>'.tep_db_output($row['jobseeker_city']).'</city>'."\n";
		$message .='<phone>'.tep_db_output($row['jobseeker_phone']).'</phone>'."\n";
		$message .='<mobile>'.tep_db_output($row['jobseeker_mobile']).'</mobile>'."\n";
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