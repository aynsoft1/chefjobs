<?
/**********************************************************
**********# Name          : Shambhu Prasad Patnaik  #**********
**********# Company       : Aynsoft Pvt. Ltd.   #**********
**********# Copyright (c) www.aynsoft.com 2005  #**********
**********************************************************/
class recruiter_account_cv 
{
 function __construct()
 {
  if(basename(strtolower($_SERVER['PHP_SELF']))==FILENAME_RECRUITER_SEARCH_RESUME)
  {
   if($_POST['action']!="search")
   {
    unset($_SESSION['sess_cvsearch']);
   }
   if(!isset($_SESSION['sess_cvsearch']))
   {
    if($_POST['action']=="search" )
    {
     $_SESSION['sess_cvsearch']='y';
     $now=date("Y-m-d");
     tep_db_query("update ".RECRUITER_ACCOUNT_HISTORY_TABLE." set cv_enjoyed=cv_enjoyed+1 where recruiter_id='".$_SESSION['sess_recruiterid']."' and  plan_for='resume_search' and start_date <= '$now' and end_date >='$now'");
    }
   }
  }
  else if((basename(strtolower($_SERVER['PHP_SELF']))!=FILENAME_JOBSEEKER_VIEW_RESUME) && (basename(strtolower($_SERVER['PHP_SELF']))!=FILENAME_JOBSEEKER_RESUME_DOWNLOAD))
  {
   unset($_SESSION['sess_cvsearch']);
  }
 }
}
?>