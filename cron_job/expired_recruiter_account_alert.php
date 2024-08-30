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
$template->set_filenames(array('recruiter_account_alert_template' => 'recruiter_account_alert_template.htm'));

$today=date("Y-m-d H:i:s");
$tomorrow=date("Y-m-d H:i:s",mktime(date("H"),date("i"), date("s"), date("m")  , date("d")+1, date("Y")));

$tableNames=RECRUITER_ACCOUNT_HISTORY_TABLE." as rh left outer join  ".RECRUITER_LOGIN_TABLE." as rl on (rh.recruiter_id=rl.recruiter_id ) left outer join  ".RECRUITER_TABLE." as r on ( rl.recruiter_id=r.recruiter_id )";
$whereClauses="  DATE_SUB(end_date,interval 7 day) = curdate()";
//$whereClauses="(end_date -curdate() )=14 and plan_type_name='Demo'";
$fieldNames=" rl.recruiter_id, concat(r.recruiter_first_name,' ',r.recruiter_last_name) as name,rl.recruiter_email_address as email";
$query = "select $fieldNames from $tableNames where $whereClauses";
$result=tep_db_query($query);
//echo "<br>$query";//exit;
$x=tep_db_num_rows($result);
//echo "<br>$x";//exit;
while($row=tep_db_fetch_array($result))
{
 $from_email_name=tep_db_output(SITE_OWNER);
 $from_email_address=tep_db_output(ADMIN_EMAIL);
 $to_name=tep_db_output($row['name']);
 $to_email_address=tep_db_output($row['email']);
 $email_subject= tep_db_output(SITE_TITLE." account expiration alert!!!");
 
 $template->assign_vars(array(
    'logo'=>'<table><tr><td><a href="'.tep_href_link("").'">'.tep_image(PATH_TO_IMG.DEFAULT_SITE_LOGO,tep_db_output(SITE_TITLE)).'</a> </td> </tr></table><br>',
    'recruiter_name'=>$to_name,
    'expired_date'=>date("d/m/Y",mktime(0,0,0,date('m'),date('d')+7,date('Y'))),
	  	'click_here'      => '<a href="'.tep_href_link("").'"><u>Click here</u></a>',
 			'site_link'       => '<a href="'.tep_href_link("").'">'.tep_db_output(SITE_TITLE).'</a>',

    ));
 $email_text=stripslashes($template->pparse1('recruiter_account_alert_template'));
 tep_mail($to_name, $to_email_address, $email_subject, $email_text, $from_email_name, $from_email_address);
 tep_mail("ADMIN", ADMIN_EMAIL, $email_subject, $email_text, $from_email_name, $from_email_address);
 //tep_mail("shambhu@ejobsitesoftware.com", ADMIN_EMAIL, $email_subject, $email_text, $from_email_name, $from_email_address);
 $template = new Template(PATH_TO_TEMPLATE);
 $template->set_filenames(array('recruiter_account_alert_template' => 'recruiter_account_alert_template.htm'));
}
tep_db_free_result($result);
?>