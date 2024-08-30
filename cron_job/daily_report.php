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
$template->set_filenames(array('daily_report' => 'daily_report_template.htm'));

$today=date("Y-m-d H:i:s",mktime(0,0, 0, date("m")  , date("d"), date("Y")));
$tomorrow=date("Y-m-d H:i:s",mktime(0,0, 0, date("m")  , date("d")+1, date("Y")));
$yesterday=date("Y-m-d H:i:s",mktime(0,0, 0, date("m")  , date("d")-1, date("Y")));

$total_resumes=no_of_records(JOBSEEKER_RESUME1_TABLE . " as jr1","jr1.inserted >= '".$yesterday."' and  jr1.inserted < '".$today."' ",'resume_id');

$jobseeker_query_raw = "select concat(j.jobseeker_first_name,' ',j.jobseeker_last_name) as jobseeker_name,jl.jobseeker_email_address ,concat(case when if(j.jobseeker_state_id,z.zone_name,j.jobseeker_state)='' then '' else  concat(if(j.jobseeker_state_id,z.zone_name,j.jobseeker_state),',') end,if(c.country_name!='',c.country_name,'')) as address from " . JOBSEEKER_LOGIN_TABLE . " as jl left outer join  " . JOBSEEKER_TABLE . " as j  on (j.jobseeker_id=jl.jobseeker_id ) left outer join ".COUNTRIES_TABLE."  as c on (j.jobseeker_country_id=c.id) left outer join ".ZONES_TABLE."  as z on  (j.jobseeker_state_id=z.zone_id or z.zone_id is NULL) where  jl.inserted >= '".$yesterday."' and  jl.inserted < '".$today."'";
$result=tep_db_query($jobseeker_query_raw );
$total_jobseekers=tep_db_num_rows($result);
$alternate=0;
while($row=tep_db_fetch_array($result))
{
  $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'"';
	 $template->assign_block_vars('jobseekers',array('row_selected' => $row_selected,
                               		                'name'         => tep_db_output(ucfirst($row['jobseeker_name'])),
                                	                'email_address'=> tep_db_output($row['jobseeker_email_address']),
                                	                'address'=> tep_db_output($row['address']),
                                                ));
   $alternate++;
}
tep_db_free_result($result);
////////////////////////////////////////////////////////
$recruiter_query_raw = "select  concat(r.recruiter_first_name,' ',r.recruiter_last_name) as recruiter_name,r.recruiter_company_name,rl.recruiter_email_address ,concat(case when if(r.recruiter_state_id,z.zone_name,r.recruiter_state)='' then '' else  concat(if(r.recruiter_state_id,z.zone_name,r.recruiter_state),',') end,if(c.country_name!='',c.country_name,'')) as address from " . RECRUITER_LOGIN_TABLE . " as rl left outer join  " . RECRUITER_TABLE . " as r  on (r.recruiter_id=rl.recruiter_id ) left outer join ".COUNTRIES_TABLE."  as c on (r.recruiter_country_id=c.id) left outer join ".ZONES_TABLE."  as z on  (r.recruiter_state_id=z.zone_id or z.zone_id is NULL)  where  rl.inserted >= '".$yesterday."' and  rl.inserted < '".$today."'";
$result=tep_db_query($recruiter_query_raw );
$total_recruiters=tep_db_num_rows($result);
$alternate=0;
while($row=tep_db_fetch_array($result))
{
 $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'"';
 $template->assign_block_vars('recruiters',array('row_selected' => $row_selected,
                               		                'name'         => tep_db_output(ucfirst($row['recruiter_name'])),
                                	                'email_address'=> tep_db_output($row['recruiter_email_address']),
                                	                'company'=> tep_db_output($row['recruiter_company_name']),
                                	                'address'=> tep_db_output($row['address']),
                                                ));
 $alternate++;
}
tep_db_free_result($result);
////////////////////////////////////////////////////////
$now=date('Y-m-d');
$prev_day=date("Y-m-d",mktime(0,0,0, date("m"), date("d")-1, date("Y")));
$job_query_raw = "select  j.job_id,j.job_title,r.recruiter_company_name,concat(case when if(j.job_state_id,z.zone_name,j.job_state)='' then '' else  concat(if(j.job_state_id,z.zone_name,j.job_state),',') end,if(c.country_name!='',c.country_name,'')) as location from " . JOB_TABLE . " as j left outer join   " . RECRUITER_LOGIN_TABLE . " as rl  on (j.recruiter_id=rl.recruiter_id) left outer join  " . RECRUITER_TABLE . " as r  on (r.recruiter_id=rl.recruiter_id ) left outer join ".COUNTRIES_TABLE."  as c on (j.job_country_id=c.id) left outer join ".ZONES_TABLE."  as z on  (j.job_state_id=z.zone_id or z.zone_id is NULL) where  rl.recruiter_status='Yes' and j.expired >='$now' and j.re_adv >='$prev_day' and j.re_adv <= '$now' and j.job_status='Yes' and ( j.deleted is NULL or j.deleted='0000-00-00')"  ;
$result=tep_db_query($job_query_raw );
$total_jobs=tep_db_num_rows($result);
$alternate=0;
while($row=tep_db_fetch_array($result))
{
	$title_format=encode_category($row['job_title']);
 $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'"';
 $query_string=encode_string("job_id=".$row['job_id']."=job_id");
	$template->assign_block_vars('jobs',array('row_selected' => $row_selected,
                               		                'title'   => "<a href=".getPermalink('job',array('ide'=>$row['job_id'],'seo_name'=>$title_format))." target='_blank'>".tep_db_output($row['job_title'])."</a>",
                                	                'company' => tep_db_output($row['recruiter_company_name']),
                                	                'location'=> tep_db_output($row['location']),
                                                ));
   $alternate++;
}
tep_db_free_result($result);

if($total_jobseekers>0)
{
 $add_jobseeker_content='<tr>
                           <td class="heading"><hr class="heading"></td>
                          </tr>
                          <tr>
                           <td class="heading" align="center">New Jobseeker List</td>
                          </tr>';
 $add_jobseeker_content1='<tr bgcolor="#ededed">
                           <td><b>Name<b></td>
                           <td><b>E-Mail Address<b></td>
                           <td><b>Location<b></td>
                           </tr>';

}
if($total_recruiters>0)
{
 $add_recruiter_content='<tr>
                           <td class="heading"><hr class="heading"></td>
                          </tr>
                          <tr>
                           <td class="heading" align="center">New Recruiter List</td>
                          </tr>';
 $add_recruiter_content1='<tr bgcolor="#ededed">
                           <td><b>Name<b></td>
                           <td><b>E-Mail Address<b></td>
                           <td><b>Company<b></td>
                           <td><b>Location<b></td>
                          </tr>';

}
if($total_jobs>0)
{
 $add_job_content='<tr>
                    <td class="heading"><hr class="heading"></td>
                   </tr>
                   <tr>
                    <td class="heading" align="center">New Job List</td>
                   </tr>';
 $add_job_content1='<tr bgcolor="#ededed">
                     <td><b>Title<b></td>
                     <td><b>Company<b></td>
                     <td><b>Location<b></td>
                    </tr>';
}
$report_date=date("d-M-Y",mktime(0,0, 0, date("m")  , date("d")-1, date("Y")));
$total_forum_post       = no_of_records(FORUM_TOPICS_TABLE. " as t","t.inserted >= '".$yesterday."' and  t.inserted < '".$today."' ",'id');
$total_forum_post_reply = no_of_records(TOPIC_REPLY_TABLE. " as t","t.inserted >= '".$yesterday."' and  t.inserted < '".$today."' ",'id');

$template->assign_vars(array(
 'HEADING_TITLE'=>'Report '.$report_date,
 'HEADING_TITLE1'=>'Global Report '.$report_date,
 'INFO_TEXT_TOTAL_JOBSEEKERS'  => $total_jobseekers,
 'INFO_TEXT_TOTAL_RECRUITERS'  => $total_recruiters,
 'INFO_TEXT_TOTAL_JOBS'        => $total_jobs,
 'INFO_TEXT_TOTAL_RESUMES'     => $total_resumes,
 'INFO_TEXT_TOTAL_FORUM_POST'  => $total_forum_post,
 'INFO_TEXT_TOTAL_FORUM_POST_REPLY' => $total_forum_post_reply,
 'INFO_TEXT_JOBSEEKER_CONTENT' => $add_jobseeker_content,
 'INFO_TEXT_JOBSEEKER_CONTENT1'=> $add_jobseeker_content1,
 'INFO_TEXT_RECRUITER_CONTENT' => $add_recruiter_content,
 'INFO_TEXT_RECRUITER_CONTENT1'=> $add_recruiter_content1,
 'INFO_TEXT_JOB_CONTENT'       => $add_job_content,
 'INFO_TEXT_JOB_CONTENT1'      => $add_job_content1,
));
$email_text=stripslashes($template->pparse1('daily_report'));
tep_mail(SITE_OWNER, ADMIN_EMAIL, 'Daily Report '.$_SERVER["HTTP_HOST"], $email_text, SITE_OWNER, ADMIN_EMAIL);
tep_mail(SITE_OWNER, 'shambhu@ejobsitesoftware.com', 'Daily Report '.$_SERVER["HTTP_HOST"], $email_text, SITE_OWNER, ADMIN_EMAIL);
?>