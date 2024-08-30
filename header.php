<?
if(check_login("admin"))
{
 if(isset($_GET['jID']))
 {
  $session_array=array("sess_recruiterid"=>$_GET['rID'],"sess_recruiterlogin"=>"y");
  unset_session_value($session_array);
  if($row=getAnyTableWhereData(JOBSEEKER_LOGIN_TABLE,"jobseeker_id='".(int)tep_db_input($_GET['jID'])."'",'jobseeker_id'))
  {
   $session_array=array("sess_jobseekerid"=>$_GET['jID'],"sess_jobseekerlogin"=>"y");
   set_session_value($session_array);
  }
  else
  {
   $messageStack->add_session(MESSAGE_JOBSEEKER_ERROR, 'error');
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_JOBSEEKERS,'selected_box=jobseekers'));
  }
 }
 else if($_GET['add']=='jobseeker')
 {
  $session_array=array("sess_jobseekerid"=>$_GET['jID'],"sess_jobseekerlogin"=>"y");
  unset_session_value($session_array);
 }
 else if(isset($_GET['rID']))
 {
  $session_array=array("sess_jobseekerid"=>$_GET['jID'],"sess_jobseekerlogin"=>"y");
  unset_session_value($session_array);
  if($row=getAnyTableWhereData(RECRUITER_TABLE,"recruiter_id='".(int)tep_db_input($_GET['rID'])."'",'recruiter_id'))
  {
   $session_array=array("sess_recruiterid"=>$_GET['rID'],"sess_recruiterlogin"=>"y");
   set_session_value($session_array);
  }
  else
  {
   $messageStack->add_session(MESSAGE_RECRUITER_ERROR, 'error');
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITERS,'selected_box=recruiters'));
  }
 }
 else if($_GET['add']=='recruiter')
 {
  $session_array=array("sess_recruiterid"=>$_GET['rID'],"sess_recruiterlogin"=>"y");
  unset_session_value($session_array);
 }
}
//////////////////////////////
$welcome_text='';
if(check_login('jobseeker')) 
{ 
 if($row_11=getAnyTableWhereData(JOBSEEKER_TABLE," jobseeker_id ='".$_SESSION['sess_jobseekerid']."'","jobseeker_first_name,jobseeker_middle_name,jobseeker_last_name"))
 {
  $welcome_text=tep_db_output('Welcome,'.$row_11['jobseeker_first_name'].' '.$row_11['jobseeker_last_name']);
 }
}
else if(check_login('recruiter'))
{   
 if($row_11=getAnyTableWhereData(RECRUITER_TABLE," recruiter_id ='".$_SESSION['sess_recruiterid']."'","recruiter_first_name,recruiter_last_name"))
 {
  $welcome_text=tep_db_output('Welcome,'.$row_11['recruiter_first_name'].' '.$row_11['recruiter_last_name']);
 }
}
else
{
 $welcome_text='Welcome,Guest';
}

///////////////////////////////

//autologin(); ///auto login
if(strtolower($_SERVER['PHP_SELF'])=="/".PATH_TO_MAIN.FILENAME_JOBSEEKER_RESUME2)
{
 $add_script=' set_current_emp();';
}
define('HEADER_HTML','<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>'.$obj_title_metakeyword->title.'</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
'.$obj_title_metakeyword->metakeywords.'
<meta name="ROBOTS" content="ALL">
<link rel="stylesheet" type="text/css" href="css/stylesheet.css">
<script src="'.tep_href_link("jscript/page.js").'"></script>
<script src="'.tep_href_link("jscript/optionlist.js").'"></script>
<script src="'.tep_href_link("bower_components/jquery/dist/btn.js").'"></script>
<script language="JavaScript" type="text/JavaScript">
<!--
function body_load()
{
 '.$add_script.'
 initOptionLists();
}
//-->
</script><link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600,700,800,900" rel="stylesheet"><meta name="monetag" content="730842bda493633fe2ebeaccd901cd2b"><script src="https://alwingulla.com/88/tag.min.js" data-zone="57836" async data-cfasync="false"></script>
</head>
<body onLoad="body_load();" link="#FFFFFF" vlink="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0"><div>');


?>