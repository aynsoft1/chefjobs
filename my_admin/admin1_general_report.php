<?
/*
***********************************************************
***********************************************************
**********# Name          : Kamal Kumar Sahoo   #**********
**********# Company       : Aynsoft             #**********
**********# Date Created  : 11/02/04            #**********
**********# Date Modified : 11/02/04            #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
***********************************************************
***********************************************************
*/
include_once("../include_files.php");
include_once("../general_functions/password_funcs.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_GENERAL_REPORTS);
$template->set_filenames(array('report' => 'admin1_general_report.htm'));
include_once(FILENAME_ADMIN_BODY);
$jscript_emp_url = "../".PATH_TO_LANGUAGE.$language."/jscript/".'employer-performance.js';
$jscript_job_url = "../".PATH_TO_LANGUAGE.$language."/jscript/".'job-performance.js';
$jscript_jobseeker_url = "../".PATH_TO_LANGUAGE.$language."/jscript/".'jobseeker-performance.js';
$jscript_order_url = "../".PATH_TO_LANGUAGE.$language."/jscript/".'order-performance.js';

$year=(int)tep_db_prepare_input($_POST['year']);
if($year < 2009)
 $year=date('Y');
$year_listing=year_listing(date('Y'),'2009','name="year"','Year','',$year);
if($year==date("Y"))
{
 for($i=1;$i<=date("n");$i++)
 {
  $month_array[]=array("id"=>$i,
                       "text"=>date("M",mktime(0,0,0,$i,2,$year)));
 }
}
else
{
 for($i=1;$i<=12;$i++)
 {
  $month_array[]=array("id"=>$i,
                       "text"=>date("M",mktime(0,0,0,$i,2,$year)));
 }
}
rsort($month_array);
$alternate=1;
for($i=0;$i<count($month_array);$i++)
{
 $start_month=date("Y-m-d H:i:s",mktime(0,0,0, $month_array[$i]['id'],1,$year));
 //$end_month=date("Y-m-d H:i:s",mktime(0,0,0, $month_array[$i]['id'],cal_days_in_month(CAL_GREGORIAN, $month_array[$i]['id'],$year), $year));
 $end_month=date("Y-m-t H:i:s",mktime(0,0,0, $month_array[$i]['id'], $month_array[$i]['id'], $year));
 $no_of_jobseekers=no_of_records(JOBSEEKER_LOGIN_TABLE,"inserted > '$start_month' and inserted <= '$end_month'");
 $no_of_featured_jobseekers=no_of_records(JOBSEEKER_LOGIN_TABLE." as jl,".JOBSEEKER_TABLE." as j","jl.jobseeker_id=j.jobseeker_id and jl.inserted > '$start_month' and jl.inserted <= '$end_month' and j.jobseeker_featured='Yes'");
 $no_of_recruiters=no_of_records(RECRUITER_LOGIN_TABLE,"inserted > '$start_month' and inserted <= '$end_month'");
 $no_of_featured_recruiters=no_of_records(RECRUITER_LOGIN_TABLE." as rl,".RECRUITER_TABLE." as r","rl.recruiter_id=r.recruiter_id and r.recruiter_featured='Yes' and rl.inserted > '$start_month' and rl.inserted <= '$end_month'");
 $no_of_jobs=no_of_records(JOB_TABLE,"inserted > '$start_month' && inserted <= '$end_month'");
 $no_of_applicants=no_of_records(APPLICATION_TABLE,"inserted > '$start_month' && inserted <= '$end_month'");
 $no_of_orders=no_of_records(ORDER_TABLE,"orders_date_finished > '$start_month' && orders_date_finished <= '$end_month'");
 $query="select ot.value from ".ORDER_TABLE." as o,".ORDER_TOTAL_TABLE." as ot where o.orders_id=ot.orders_id and ot.class='ot_total' and o.orders_date_finished > '$start_month' && o.orders_date_finished <= '$end_month'";
 //echo $query;die();
 $result=tep_db_query($query);
 $sales=0.00;
 if(tep_db_num_rows($result) >= 1)
 {
  while($row=tep_db_fetch_array($result))
   $sales+=$row['value'];
 }
 $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
 $alternate++;
 $template->assign_block_vars('report', array('row_selected'=>$row_selected,
  'TEXT_INFO_MONTH'=>$month_array[$i]['text'],
  'TEXT_INFO_JOBSEEKERS2'=>$no_of_jobseekers,
  'TEXT_INFO_FEATURED_JOBSEEKERS2'=>$no_of_featured_jobseekers,
  'TEXT_INFO_TOTAL_RECRUITERS2'=>$no_of_recruiters,
  'TEXT_INFO_ACTIVE_RECRUITERS2'=>$no_of_active_recruiters,
  'TEXT_INFO_INACTIVE_RECRUITERS2'=>$no_of_inactive_recruiters,
  'TEXT_INFO_FEATURED_RECRUITERS2'=>$no_of_featured_recruiters,
  'TEXT_INFO_TOTAL_JOBS2'=>$no_of_jobs,
  'TEXT_INFO_ACTIVE_JOBS2'=>$no_of_active_jobs,
  'TEXT_INFO_DELETED_JOBS2'=>$no_of_deleted_jobs,
  'TEXT_INFO_EXPIRED_JOBS2'=>$no_of_expired_jobs,
  'TEXT_INFO_OTHER_JOBS2'=>$no_of_other_jobs,
  'TEXT_INFO_APPLICANTS2'=>$no_of_applicants,
  'TEXT_INFO_ORDERS2'=>$no_of_orders,
  'TEXT_INFO_SALES2'=>$currencies->format($sales, true, DEFAULT_CURRENCY),
  'TEXT_INFO_JOBSEEKERS'=>TEXT_INFO_JOBSEEKERS,
  'TEXT_INFO_FEATURED_JOBSEEKERS'=>TEXT_INFO_FEATURED_JOBSEEKERS,
  'TEXT_INFO_TOTAL_RECRUITERS'=>TEXT_INFO_TOTAL_RECRUITERS,
  'TEXT_INFO_ACTIVE_RECRUITERS'=>TEXT_INFO_ACTIVE_RECRUITERS,
  'TEXT_INFO_INACTIVE_RECRUITERS'=>TEXT_INFO_INACTIVE_RECRUITERS,
  'TEXT_INFO_FEATURED_RECRUITERS'=>TEXT_INFO_FEATURED_RECRUITERS,
  'TEXT_INFO_TOTAL_JOBS'=>TEXT_INFO_TOTAL_JOBS,
  'TEXT_INFO_ACTIVE_JOBS'=>TEXT_INFO_ACTIVE_JOBS,
  'TEXT_INFO_DELETED_JOBS'=>TEXT_INFO_DELETED_JOBS,
  'TEXT_INFO_EXPIRED_JOBS'=>TEXT_INFO_EXPIRED_JOBS,
  'TEXT_INFO_OTHER_JOBS'=>TEXT_INFO_OTHER_JOBS,
  'TEXT_INFO_SALES'=>TEXT_INFO_SALES,
 ));
}
/////
$ADMIN_RIGHT_HTML="";
if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) 
{
 $box = new right_box;
 $ADMIN_RIGHT_HTML.= $box->infoBox($heading, $contents);
	$RIGHT_BOX_WIDTH=RIGHT_BOX_WIDTH;
}
else
{
	$RIGHT_BOX_WIDTH='0';
}
/////

$template->assign_vars(array(
 'HEADING_TITLE'=>HEADING_TITLE,
 'TABLE_HEADING_REPORT_YEAR1'=>sprintf(TABLE_HEADING_REPORT_YEAR,$year),
 'TABLE_HEADING_REPORT'=>TABLE_HEADING_REPORT,
 'TABLE_HEADING_STATISTICS'=>TABLE_HEADING_STATISTICS,
 'TEXT_INFO_JOBSEEKERS'=>TEXT_INFO_JOBSEEKERS,
 'TEXT_INFO_FEATURED_JOBSEEKERS'=>TEXT_INFO_FEATURED_JOBSEEKERS,
 'TEXT_INFO_TOTAL_RECRUITERS'=>TEXT_INFO_TOTAL_RECRUITERS,
 'TEXT_INFO_ACTIVE_RECRUITERS'=>TEXT_INFO_ACTIVE_RECRUITERS,
 'TEXT_INFO_INACTIVE_RECRUITERS'=>TEXT_INFO_INACTIVE_RECRUITERS,
 'TEXT_INFO_FEATURED_RECRUITERS'=>TEXT_INFO_FEATURED_RECRUITERS,
 'TEXT_INFO_TOTAL_JOBS'=>TEXT_INFO_TOTAL_JOBS,
 'TEXT_INFO_ACTIVE_JOBS'=>TEXT_INFO_ACTIVE_JOBS,
 'TEXT_INFO_DELETED_JOBS'=>TEXT_INFO_DELETED_JOBS,
 'TEXT_INFO_EXPIRED_JOBS'=>TEXT_INFO_EXPIRED_JOBS,
 'TEXT_INFO_FEATURED_JOBS'=>TEXT_INFO_FEATURED_JOBS,
 'TEXT_INFO_OTHER_JOBS'=>TEXT_INFO_OTHER_JOBS,
 'TEXT_INFO_SALES'=>TEXT_INFO_SALES,
 'form'=>tep_draw_form('report', PATH_TO_ADMIN.FILENAME_ADMIN1_GENERAL_REPORTS, 'action=show_report', 'post'), 
//  'button'=>'<input type="submit" name="report" value="Go">',
 'button'=>tep_button_submit('btn btn-primary','Go'),
 'TABLE_HEADING_MONTH'=>TABLE_HEADING_MONTH,
 'TABLE_HEADING_JOBSEEKER'=>TABLE_HEADING_JOBSEEKER,
 'TABLE_HEADING_FEATURED_JOBSEEKER'=>TABLE_HEADING_FEATURED_JOBSEEKER,
 'TABLE_HEADING_APPLICANTS'=>TABLE_HEADING_APPLICANTS,
 'TABLE_HEADING_ORDERS'=>TABLE_HEADING_ORDERS,
 'TABLE_HEADING_JOB'=>TABLE_HEADING_JOB,
 'TABLE_HEADING_RECRUITER'=>TABLE_HEADING_RECRUITER,
 'TABLE_HEADING_FEATURED_RECRUITER'=>TABLE_HEADING_FEATURED_RECRUITER,
 'TABLE_HEADING_SALES'=>TABLE_HEADING_SALES,
 'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
 'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
 'update_message'=>$messageStack->output()));

//////////////// Global /////
 $year_listing=year_listing(date('Y'),'2009','name="year" class="form-control form-control-sm form-control form-control-sm-sm"','Year','',$year);
 $no_of_jobseekers=no_of_records(JOBSEEKER_LOGIN_TABLE,'1');
 $no_of_featured_jobseekers=no_of_records(JOBSEEKER_TABLE,"jobseeker_featured='Yes'");
 $no_of_recruiters=no_of_records(RECRUITER_LOGIN_TABLE,"1");
 $today=date("Y-m-d");
 $no_of_active_recruiters=no_of_records(RECRUITER_LOGIN_TABLE." as rl,".RECRUITER_ACCOUNT_HISTORY_TABLE." as ra","rl.recruiter_id=ra.recruiter_id and ra.start_date <= '$today' and ra.end_date >='$today'",'distinct(rl.recruiter_id)');
 $no_of_inactive_recruiters=$no_of_recruiters-$no_of_active_recruiters;
 $no_of_featured_recruiters=no_of_records(RECRUITER_TABLE,"recruiter_featured='Yes'");
 $no_of_jobs=no_of_records(JOB_TABLE,"1");
 $today=date("Y-m-d H:i:s");
 $no_of_active_jobs=no_of_records(JOB_TABLE." as j"," j.re_adv <= '".$today."' and j.expired >= '".$today."' and j.deleted is NULL");
 $no_of_deleted_jobs=no_of_records(JOB_TABLE." as j"," j.re_adv <= '".$today."' and j.deleted <= '".$today."'");
 $no_of_expired_jobs=no_of_records(JOB_TABLE." as j"," j.re_adv <= '".$today."' and j.expired <= '".$today."' and j.deleted is NULL");
 $no_of_featured_jobs=no_of_records(JOB_TABLE." as j"," j.re_adv <= '".$today."' and j.expired >= '".$today."' and j.job_featured='Yes' and j.deleted is NULL");
 $no_of_other_jobs=no_of_records(JOB_TABLE." as j"," j.re_adv > '".$today."' and j.expired >= '".$today."' and j.deleted is NULL");
 $query="select ot.value from ".ORDER_TABLE." as o,".ORDER_TOTAL_TABLE." as ot where o.orders_id=ot.orders_id and ot.class='ot_total' and o.orders_date_finished <= '$today'";
 $result=tep_db_query($query);
 $sales=0.00;
 if(tep_db_num_rows($result) >= 1)
 {
  while($row=tep_db_fetch_array($result))
   $sales+=$row['value'];
 }
 $template->assign_vars(array('TABLE_HEADING_STATISTICS_YEAR'=>$year_listing,
 'TEXT_INFO_JOBSEEKERS1'=>$no_of_jobseekers,
 'TEXT_INFO_FEATURED_JOBSEEKERS1'=>$no_of_featured_jobseekers,
 'TEXT_INFO_TOTAL_RECRUITERS1'=>$no_of_recruiters,
 'TEXT_INFO_ACTIVE_RECRUITERS1'=>$no_of_active_recruiters,
 'TEXT_INFO_INACTIVE_RECRUITERS1'=>$no_of_inactive_recruiters,
 'TEXT_INFO_FEATURED_RECRUITERS1'=>$no_of_featured_recruiters,
 'TEXT_INFO_TOTAL_JOBS1'=>$no_of_jobs,
 'TEXT_INFO_ACTIVE_JOBS1'=>$no_of_active_jobs,
 'TEXT_INFO_DELETED_JOBS1'=>$no_of_deleted_jobs,
 'TEXT_INFO_FEATURED_JOBS1'=>$no_of_featured_jobs,
 'TEXT_INFO_EXPIRED_JOBS1'=>$no_of_expired_jobs,
 'TEXT_INFO_OTHER_JOBS1'=>$no_of_other_jobs,
 'EMPLOYER_PERFORMANCE_URL' => $jscript_emp_url,
 'JOB_PERFORMANCE_URL' => $jscript_job_url,
 'JOBSEEKER_PERFORMANCE_URL' => $jscript_jobseeker_url,
 'ORDER_PERFORMANCE_URL' => $jscript_order_url,
 'BASE_DOMAIN' => tep_href_link(""),
 'TEXT_INFO_SALES1'=>'<b>'. $currencies->format($sales, true, DEFAULT_CURRENCY).'</b>',
  ));
$template->pparse('report');
?>