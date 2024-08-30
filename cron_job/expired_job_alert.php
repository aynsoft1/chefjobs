<?
/*
************************************************************
************************************************************
**********#	Name				      : Shambhu Prasad Patnaik #********
**********#	Company			    : Aynsoft	Pvt. Ltd.   #***********
**********#	Copyright (c) www.aynsoft.com 2004	 #***********
************************************************************
************************************************************
*/
ini_set('max_execution_time','0');
include_once("../include_files.php");
$template->set_filenames(array('expired_job_alert_template' => 'expired_job_alert_template.htm'));

//$today=date("Y-m-d H:i:s");
//$tomorrow=date("Y-m-d H:i:s",mktime(date("H"),date("i"), date("s"), date("m")  , date("d")+1, date("Y")));
$today=date("Y-m-d 00:00:00");
$tomorrow=date("Y-m-d H:i:s",mktime(23,59, 59, date("m")  , date("d")+1, date("Y")));

$tableNames=JOB_TABLE." as j, ".RECRUITER_LOGIN_TABLE." as rl, ".RECRUITER_TABLE." as r";
$whereClauses="j.recruiter_id=rl.recruiter_id and j.recruiter_id=r.recruiter_id and j.re_adv <= '".$today."' and j.expired >= '".$today."' and j.expired <= '".$tomorrow."' and j.deleted is NULL";
$fieldNames="j.job_id,j.job_title,j.job_reference,j.expired,concat(r.recruiter_first_name,' ',r.recruiter_last_name) as name,rl.recruiter_email_address as email";
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
 $email_subject= tep_db_output(SITE_TITLE." job expiration alert");
 $template->assign_vars(array(
    'logo'=>'<table><tr><td><a href="'.tep_href_link("").'">'.tep_image(PATH_TO_IMG.DEFAULT_SITE_LOGO,tep_db_output(SITE_TITLE)).'</a> </td> </tr></table><br>',
    'recruiter_name'=>$to_name,
    'job_title'=>tep_db_output($row['job_title']),
    'job_reference'=>tep_db_output($row['job_reference']),
    'expired_date'=>tep_date_long(tep_db_output($row['expired'])),
    're_advertise_link'=>"<a href='".tep_href_link(FILENAME_RECRUITER_LIST_OF_JOBS,'j_status=expired&jobID='.$row['job_id'])."&action=readv_job'>Click here to re-advertise</a>",
		  'site_title'      => tep_db_output(SITE_TITLE),
				'site_link'       => '<a href="'.tep_href_link("").'">'.tep_db_output(SITE_TITLE).'</a>',

    ));
 $email_text=stripslashes($template->pparse1('expired_job_alert_template'));

 //echo "From Name : ".$from_email_name.'<br>'. "From Email address : ".$from_email_address."<br>"."To Name : ".$to_name.'<br>'. "To email_address : ".$to_email_address.'<br>'. $email_subject.'<br>'. $email_text."<hr>";
tep_mail($to_name, $to_email_address, $email_subject, $email_text, $from_email_name, $from_email_address);
}
tep_db_free_result($result);
?>