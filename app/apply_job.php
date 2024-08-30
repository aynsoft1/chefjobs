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
 $job_id       = tep_db_prepare_input($_POST['job_id']);
 $resume_id    = tep_db_prepare_input($_POST['resume_id']);
	$error =false;
	$errorMsg=array();
 if(!$row_resume=getAnyTableWhereData(JOBSEEKER_RESUME1_TABLE.' as jr1  left outer join '.JOBSEEKER_LOGIN_TABLE.' as jl on (jr1.jobseeker_id =jl.jobseeker_id ) left outer join '.JOBSEEKER_TABLE.' as j on (jl.jobseeker_id =j.jobseeker_id )',"jr1.jobseeker_id='".$jobseeker_id."' and  jr1.resume_id ='".$resume_id."' ","jr1.resume_id,jl.jobseeker_email_address,j.jobseeker_first_name,j.jobseeker_middle_name,j.jobseeker_last_name"))
	{
 	$error =true;
  $errorMsg [] ='invalid resume';
	}
	$jobseeker_name  = $row_resume['jobseeker_first_name'].' '.$row_resume['jobseeker_last_name'];
	$jobseeker_email = $row_resume['jobseeker_email_address'];
 if(!$error)
	{
		$now=date('Y-m-d H:i:s');
  $where_clause=" j.job_id='".$job_id."' and j.expired >='$now' and j.re_adv <='$now' and j.job_status='Yes' and ( j.deleted is NULL or j.deleted='0000-00-00 00:00:00') and  rl.recruiter_status='Yes'";
  $fielsd='j.job_id,j.job_title,j.job_reference,j.display_id,j.url,j.post_url,rl.recruiter_email_address,r.recruiter_first_name,r.recruiter_last_name,ru.name,ru.email_address,ru.status as recruiter_user_status ';
  if(!$row_rec=getAnyTableWhereData(JOB_TABLE.' as j left outer join  '.RECRUITER_LOGIN_TABLE.' as rl on (rl.recruiter_id=j.recruiter_id) left outer join  '.RECRUITER_TABLE.' as r on (rl.recruiter_id=r.recruiter_id)  left outer join  '.RECRUITER_USERS_TABLE.' as ru on (j.recruiter_user_id =ru.id)  ',$where_clause,$fielsd))
		{
  	$error =true;
   $errorMsg [] ='invalid job';
		}
		if(!$error)
		{
   $post_url = $row_rec['post_url'];
   if($post_url=='Yes')
			{
  	 $error =true;
    $errorMsg [] ='not allowed';
		 }

   if(!$error)
			{
    $display_id = $row_rec['display_id'];
			 $job_title  = $row_rec['job_reference'].' - '.$row_rec['job_title']; 
    $recruiter_user_status = $row_rec['recruiter_user_status'];


    if($recruiter_user_status=='Yes')
			 {
				 $recruiter_name  = $row_rec['name'];
				 $recruiter_email = $row_rec['email_address'];
			 }
			 else
			 {
				 $recruiter_name  = $row_rec['recruiter_first_name'] .' '.$row_rec['recruiter_last_name'];
				 $recruiter_email = $row_rec['recruiter_email_address'];
			 }


 		 $sql_data_array=array('inserted'=>'now()',
 																								'job_id'=>$job_id,
																									'resume_id'=>$resume_id,
																									'jobseeker_id'=>$jobseeker_id,
																								);
 		 tep_db_perform(APPLY_TABLE, $sql_data_array);
  	 tep_db_perform(APPLICATION_TABLE, $sql_data_array);
    if($applicant_id=getAnytableWhereData(APPLICATION_TABLE,"jobseeker_id='".$jobseeker_id."' and job_id='".$job_id."' order by inserted desc limit 0,1",'id'))
    {
     $sql_data_array2=array('application_id'=>$display_id.'-'.($applicant_id['id']+1000));
     $row_apply=getAnytableWhereData(APPLY_TABLE,"jobseeker_id='".$jobseeker_id."' and job_id ='".$job_id."' order by inserted desc limit 0,1",'id');
     $sql_data_array2['jobseeker_apply_id']=$row_apply['id'];
     tep_db_perform(APPLICATION_TABLE, $sql_data_array2, 'update', "id = '" .$applicant_id['id']."'");
				 if($job_sta_row=getAnytableWhereData(JOB_STATISTICS_TABLE,"job_id='".$job_id."'",'applications'))
				 {
      tep_db_query('update '.JOB_STATISTICS_TABLE ." set applications =applications+1,clicked=clicked+1,viewed=viewed+1 where job_id = '" . $job_id . "'");
				 }
				 else
				 {
			   $sql_statis_array=array('job_id'=>$job_id,
                        'clicked'=>1,
                        'applications'=>1,
                        'viewed'=>1,
                        );
      tep_db_perform(JOB_STATISTICS_TABLE, $sql_data_array);
				 }
				 ///
 			 $template1 = new Template(PATH_TO_MAIN_PHYSICAL.PATH_TO_TEMPLATE);
     $template1->set_filenames(array('email'=>'application_send_template.htm'));

				 $query_string=encode_string("application_id=".$resume_id."=application_id");

     $application_link=sprintf('To view my CV, Please click the following link.%s','<a href="'.tep_href_link(FILENAME_JOBSEEKER_VIEW_RESUME,'query_string='.$query_string).'" target="_blank"><u><b>View Resume</b></u></a><br>&nbsp;');
     $subject = tep_db_output('application -'.$job_title);
	    $template1->assign_vars(array(
     'logo'=>'<a href="'.tep_href_link("").'">'.tep_image(PATH_TO_IMG.DEFAULT_SITE_LOGO,tep_db_output(SITE_TITLE),'','','border="0"').'</a>',
     'recruiter_name'=>tep_db_output($recruiter_name),
     'jobseeker_name'=>tep_db_output($jobseeker_name),
     'jobseeker_email_address'=>tep_db_output($jobseeker_email),
     'application_link'=>$application_link,
     'site_title'=>tep_db_output(SITE_TITLE),
     'admin_email'=>stripslashes(CONTACT_ADMIN),
     ));
     $email_text=stripslashes($template1->pparse1('email'));
					//echo $email_text;die();
     tep_mail($recruiter_name , $recruiter_email,$subject, $email_text, SITE_OWNER,EMAIL_FROM);
			  tep_redirect(tep_href_link('app/success.php'));
				////
		  }
		 }
		}
	}
	if($error)
	{
		$message='<error>'."\n"; 	
  $message .='<status>error</status>'."\n";
		foreach($errorMsg as $msg)
		{
   $message .='<message>'.$msg.'</message>'."\n";
		}
		$message.='</error>'; 	
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