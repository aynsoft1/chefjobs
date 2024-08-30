<?
/*
***********************************************************
**********# Name          : Shambhu Prrasad Patnaik   #**********
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
***********************************************************
*/
include_once("include_files.php");
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_UNSUBSCRIBE);
$action = (isset($_GET['action']) ? $_GET['action'] : '');
//die();
//if ($action=='unsubscribe') 
{
 $user_type=tep_db_prepare_input($_GET['user_type']);
 $query_string=tep_db_prepare_input($_GET['email']);
 $email_address=check_data1($query_string,"###","email","unsubscribe");
 switch($user_type)
 {
  case'jobseeker':
   $whereClause="  jl.jobseeker_email_address='".tep_db_input($email_address)."'";
   if($row=getAnyTableWhereData(JOBSEEKER_LOGIN_TABLE.' as jl left outer join  '.JOBSEEKER_TABLE.' as j on (jl.jobseeker_id =j.jobseeker_id  )',$whereClause,'jl.jobseeker_id,j.jobseeker_newsletter'))
   {
    if($row['jobseeker_newsletter']=='Yes') 
    {
     tep_db_query('update '.JOBSEEKER_TABLE ." set jobseeker_newsletter='No' where jobseeker_id = '" .tep_db_input($row['jobseeker_id']). "'");
     $messageStack->add_session(EMAIL_UNSUBSCRIBE_SUCCESS, 'success');
     tep_redirect(tep_href_link(FILENAME_JOBSEEKER_LOGIN));
    }
    else
    {
     $messageStack->add_session(ALL_READY_EMAIL_UNSUBSCRIBE, 'error');
     tep_redirect(tep_href_link(FILENAME_JOBSEEKER_LOGIN));
    }
   }
   else
   {
    $messageStack->add_session(SORRY_NO_EMAIL_ADDRESS_EXIST, 'error');
    tep_redirect(tep_href_link(FILENAME_JOBSEEKER_LOGIN));
   }
    break;
  case'recruiter':
   $whereClause=" rl.recruiter_email_address='".tep_db_input($email_address)."'";
   if($row=getAnyTableWhereData(RECRUITER_LOGIN_TABLE.' as rl left outer join  '.RECRUITER_TABLE.' as r on (rl.recruiter_id =r.recruiter_id )',$whereClause,'rl.recruiter_id,r.recruiter_newsletter'))
   {
    if($row['recruiter_newsletter']=='Yes') 
    {
     tep_db_query('update '.RECRUITER_TABLE ." set recruiter_newsletter='No' where recruiter_id = '" .tep_db_input($row['recruiter_id']). "'");
     $messageStack->add_session(EMAIL_UNSUBSCRIBE_SUCCESS, 'success');
     tep_redirect(tep_href_link(FILENAME_RECRUITER_LOGIN));
    }
    else
    {
     $messageStack->add_session(ALL_READY_EMAIL_UNSUBSCRIBE, 'error');
     tep_redirect(tep_href_link(FILENAME_RECRUITER_LOGIN));
    }
   }
   else
   {
    $messageStack->add_session(SORRY_NO_EMAIL_ADDRESS_EXIST, 'error');
    tep_redirect(tep_href_link(FILENAME_RECRUITER_LOGIN));
   }
   break;
  }  
 }
?>