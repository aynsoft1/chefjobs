<?
/*
************************************************************
**********#	Name				      : Shambhu Prasad Patnaik		 #***********
**********#	Company			    : Aynsoft	Pvt. Ltd.   #***********
**********#	Copyright (c) www.aynsoft.com 2004	 #***********
************************************************************
*/
ini_set('max_execution_time','0');
include_once("../include_files.php"); //expired_recruiter_account
//phpinfo();
$from_email_address=tep_db_output(ADMIN_EMAIL);
//tep_mail('shambhu', 'shambhu@ejobsitesoftware.com', 'cron test  ..',$_SERVER['SERVER_ADDR'], SITE_OWNER,$from_email_address);
tep_mail('shambhu', 'sp_patnaik2003@yahoo.co.in', 'cron test  ..',$_SERVER['REMOTE_ADDR'], SITE_OWNER,$from_email_address);
//mail('sp_patnaik2003@yahoo.co.in', 'cron test  ..','ok...',"from :shambhu@ejobsitesoftware.com");

?>
