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
$template->set_filenames(array('expired_job_renewal_success' => 'expired_job_renewal_template.htm','expired_job_renewal_failed' => 'expired_job_renewal_template1.htm'));

//$today=date("Y-m-d H:i:s");
//$tomorrow=date("Y-m-d H:i:s",mktime(date("H"),date("i"), date("s"), date("m")  , date("d")+1, date("Y")));
$today=date("Y-m-d 00:00:00");

$tableNames=JOB_TABLE." as j left outer join  ".RECRUITER_LOGIN_TABLE." as rl on (j.recruiter_id=rl.recruiter_id) left outer join  ".RECRUITER_TABLE." as r on (j.recruiter_id=r.recruiter_id)";
$whereClauses=" j.re_adv <= '".$today."' and j.expired < '".$today."'   and j.deleted is NULL and  job_auto_renew >0 and rl.recruiter_status='Yes' and job_status='Yes' ";
$fieldNames="j.job_id,j.job_title,j.job_reference,j.expired,concat(r.recruiter_first_name,' ',r.recruiter_last_name) as name,rl.recruiter_email_address as email,j.recruiter_id,j.job_auto_renew";
$query = "select $fieldNames from $tableNames where $whereClauses";
$result=tep_db_query($query);
//echo "<br>$query";exit;
$x=tep_db_num_rows($result);
//echo "<br>$x";//exit;
$now=date("Y-m-d");

while($row=tep_db_fetch_array($result))
{
 $from_email_name=tep_db_output(SITE_OWNER);
 $from_email_address=tep_db_output(ADMIN_EMAIL);
 $to_name=tep_db_output($row['name']);
 $to_email_address=tep_db_output($row['email']);
 //$to_email_address1='sp_patnaik2003@yahoo.co.in';
 $email_subject= tep_db_output(SITE_TITLE." job auto renewal");
 $job_id         =  $row['job_id'];
 $seo_name         =  tep_db_output(encode_category($row['job_title']));
 $recruiter_id   =  $row['recruiter_id'];
 $job_auto_renew =  $row['job_auto_renew'];

 include_once(PATH_TO_MAIN_PHYSICAL_CLASS . 'recruiter_accounts.php');
 $obj_account=new recruiter_accounts($recruiter_id);
 $total_job=$obj_account->allocated_amount['job'];
 $total_enjoyed=$obj_account->enjoyed_amount['job'];
 $job_renewal =false;
 if($total_job > $total_enjoyed || $total_job=="Unlimited")
 {
  if($row1=getAnyTableWhereData(RECRUITER_ACCOUNT_HISTORY_TABLE," recruiter_id='".tep_db_input($recruiter_id)."' and plan_for ='job_post' and start_date <= '$now' and end_date >='$now'","id,job_enjoyed"))
  {
   $deleted='null';
   $expired=date("Y-m-d H:i:s",mktime(23,59, 59, date("m")  , date("d")+$job_auto_renew, date("Y")));
   $sql_data_array1=array('deleted'=>$deleted,'re_adv'=>$today,'expired'=>$expired);
   tep_db_perform(JOB_TABLE, $sql_data_array1, 'update', "job_id = '" . $job_id . "'");
   $sql_data_array=array("job_id"=>$job_id,"re_advertised_from"=>$today,"re_advertised_to"=>$expired);
   tep_db_perform(JOB_POINT_HISTORY_TABLE, $sql_data_array);

   $sql_data_array=array('job_enjoyed'=>1+$row1['job_enjoyed']);
   tep_db_perform(RECRUITER_ACCOUNT_HISTORY_TABLE, $sql_data_array, 'update', "id = '" . $row1['id'] . "'");
    $job_renewal =true;
   }
 }
 if($job_renewal)
 {// successs auto renewal
   $email_subject.= ' successfully done';

   $template->assign_vars(array(
    'logo'=>'<a  href="'.tep_href_link("").'">'.tep_image(PATH_TO_IMG.DEFAULT_SITE_LOGO,tep_db_output(SITE_TITLE),'','',' width="90%"').'</a>',
    'recruiter_name'=>$to_name,
    'job_title'=>'<a href="'.getPermalink('job',array('ide'=>$job_id,'seo_name'=>$seo_name)) .'" class="job_search_title" target="_blank">'.tep_db_output($row['job_title']).'<a>',
    'expired_date'=>tep_date_long(tep_db_output($expired)),
    'site_title'      => tep_db_output(SITE_TITLE),
	'site_link'       => '<a href="'.tep_href_link("").'">'.tep_db_output(SITE_TITLE).'</a>',

    ));
     $email_text=stripslashes($template->pparse1('expired_job_renewal_success'));
    //echo "From Name : ".$from_email_name.'<br>'. "From Email address : ".$from_email_address."<br>"."To Name : ".$to_name.'<br>'. "To email_address : ".$to_email_address.'<br>'. $email_subject.'<br>'. $email_text."<hr>";
    tep_mail($to_name, $to_email_address, $email_subject, $email_text, $from_email_name, $from_email_address);
  }
 else
 {// faild auto renewal
   $email_subject.= ' Failed';
  
   $template->assign_vars(array(
    'logo'=>'<a  href="'.tep_href_link("").'">'.tep_image(PATH_TO_IMG.DEFAULT_SITE_LOGO,tep_db_output(SITE_TITLE),'','',' width="90%"').'</a>',
    'recruiter_name'=>$to_name,
    'job_title'=>'<a href="'.tep_href_link(FILENAME_RECRUITER_VIEW_JOB,'jobID='.$job_id) .'"   target="_blank">'.tep_db_output($row['job_title']).'<a>',
     'site_title'      => tep_db_output(SITE_TITLE),
	'site_link'       => '<a href="'.tep_href_link("").'">'.tep_db_output(SITE_TITLE).'</a>',

    ));
     $email_text=stripslashes($template->pparse1('expired_job_renewal_failed'));
    //echo "From Name : ".$from_email_name.'<br>'. "From Email address : ".$from_email_address."<br>"."To Name : ".$to_name.'<br>'. "To email_address : ".$to_email_address.'<br>'. $email_subject.'<br>'. $email_text."<hr>";
    tep_mail($to_name, $to_email_address, $email_subject, $email_text, $from_email_name, $from_email_address);
 
 }
 //print_r($obj_account);
 }
tep_db_free_result($result);
?>