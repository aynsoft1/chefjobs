<?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik   #*****
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2013  #**********
***********************************************************
*/
include_once("../include_files.php");
include_once("../general_functions/app_functions.php");

$access_key  = tep_db_prepare_input($_POST['access_key']);

if($jobseeker_id =get_access_user($access_key))
{
	$fielsd='jl.jobseeker_id,jl.jobseeker_email_address,j.jobseeker_first_name,j.jobseeker_last_name,jobseeker_address1,jobseeker_address2,jobseeker_country_id,jobseeker_city,jobseeker_phone,jobseeker_mobile, if(jobseeker_state_id,z.zone_name,jobseeker_state) as j_state';
 $message ='<results>'."\n";
 $message .='<status>success</status>'."\n";
 $query = "select r.resume_id,r.resume_title from ".JOBSEEKER_RESUME1_TABLE." as r where r.jobseeker_id ='".tep_db_input($jobseeker_id)."' order by r.inserted asc";
 $result=tep_db_query($query);
 $x=tep_db_num_rows($result);
 $resumes=array();
	if($x>0)
	{
 	$message .='<resumes>'."\n";
 	while($row_r = tep_db_fetch_array($result))
		{
  	$message .='<resume>'."\n";
  	$message .='<r_id>'.$row_r['resume_id'].'</r_id>'."\n";
  	$message .='<r_title>'.tep_db_output($row_r['resume_title']).'</r_title>'."\n";
  	$message .='</resume>'."\n";
 	}
 	$message .='</resumes>'."\n";
	}
	tep_db_free_result($result); 
	$message .='</results>'."\n";
	header('Content-Type: text/xml'); 
	echo $message;
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