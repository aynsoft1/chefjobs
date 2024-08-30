<?
/**********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft Pvt. Ltd.   #**********
**********# Copyright (c) www.aynsoft.com 2005  #**********
**********************************************************/

function tep_update_whos_online() 
{
 $user_id=0;
 $user="Anonymous";
 if(!check_login('admin'))
 {
  if(check_login('recruiter'))
  {
   $user_id=$_SESSION['sess_recruiterid'];
   $user='Recruiter';
  }
  else if(check_login('jobseeker'))
  {
   $user_id=$_SESSION['sess_jobseekerid'];
   $user='Jobseeker';
  }
 }
 else if(check_login('admin'))
 {
  $user_id=$_SESSION['sess_adminid'];
  $user='Administrator';
 }
 $session_id = session_id();
 $ip_address = getenv('REMOTE_ADDR');
 $last_page_url = getenv('REQUEST_URI');
 $current_time = time();
 $xx_mins_ago = ($current_time - LOGOUT_TIME);

 // remove entries that have expired

 if ($row = getAnyTableWhereData(WHOS_ONLINE_TABLE, "session_id = '" . tep_db_input($session_id) ."'","ip_address, time_last_click,session_id")) 
 {
  //echo $row['time_last_click'] ." : ". $xx_mins_ago;
  if(($row['ip_address']!=$ip_address) && (CHECK_IP_ADDRESS=='true'))
  {
   //tep_mail(SITE_OWNER, ADMIN_EMAIL, "Login attempt", "Site name : Login by the IP-Address: ".$ip_address, SITE_OWNER, ADMIN_EMAIL);
   session_unset();
   // Finally, destroy the session.
   session_destroy();   
   session_start();
   session_regenerate_id();
   $session_id = session_id();

   tep_redirect(FILENAME_INDEX);
   //die('You are trying to hack. Strong action will be taken against you.');
  }
  else if($row['time_last_click'] < $xx_mins_ago)
  {
   session_unset();
   // Finally, destroy the session.
   session_destroy();   
   session_start();
   session_regenerate_id();
   $session_id = session_id();
   //tep_db_query("delete from " . WHOS_ONLINE_TABLE . " where time_last_click < '" . $xx_mins_ago . "'");
   tep_redirect(FILENAME_INDEX);
  }
  else
  {
   tep_db_query("update " . WHOS_ONLINE_TABLE . " set user_id = '" . (int)$user_id . "', user = '" . tep_db_input($user) . "', ip_address = '" . tep_db_input($ip_address) . "', time_last_click = '" . tep_db_input($current_time) . "', last_page_url = '" . tep_db_input($last_page_url) . "' where session_id = '" . tep_db_input($session_id) . "'");
  }
 } 
 else 
 {
  tep_db_query("insert into " . WHOS_ONLINE_TABLE . " (user_id, user, session_id, ip_address, time_entry, time_last_click, last_page_url) values ('" . (int)$user_id . "', '" . tep_db_input($user) . "', '" . tep_db_input($session_id) . "', '" . tep_db_input($ip_address) . "', '" . tep_db_input($current_time) . "', '" . tep_db_input($current_time) . "', '" . tep_db_input($last_page_url) . "')");
 }
}
?>