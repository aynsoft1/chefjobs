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
if ($dir = dir(PATH_TO_MAIN_PHYSICAL_BACKUP)) 
{
 while ($file = $dir->read()) 
 {
  if (!is_dir(PATH_TO_MAIN_PHYSICAL_BACKUP . $file)) 
  {
   $date =date('Y-m-d',mktime(0, 0, 0, date("m"),date("d")-7,date("Y")));
   $file_name= $file;
   $file_date= date("Y-m-d",filectime(PATH_TO_MAIN_PHYSICAL_BACKUP.$file));
   if($date > $file_date && $file_name!='index.php')
   {
    if(is_file(PATH_TO_MAIN_PHYSICAL_BACKUP . $file) &&  $file_name!='')
     @unlink(PATH_TO_MAIN_PHYSICAL_BACKUP . $file);
     $from_email_address=tep_db_output(ADMIN_EMAIL);
     tep_mail('Admin', $from_email_address, 'Prune 7 day old database backup ','delete : '.$file, SITE_OWNER, $from_email_address);
   }
  }
 }
}
$dir->close();
?>