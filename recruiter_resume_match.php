<?
/*
***********************************************************
***********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
***********************************************************
***********************************************************
*/
include_once("include_files.php");
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_RECRUITER_RESUME_MATCH);
include_once(FILENAME_BODY);
include_once("general_functions/weight_function.php");
$template->set_filenames(array('resume_match' => 'recruiter_resume_match.htm'));
if(!check_login("recruiter"))
{
 $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
 tep_redirect(FILENAME_RECRUITER_LOGIN);
}
$job_id         =(int) (isset($_GET['jobID']) ? $_GET['jobID'] : '');
$application_id =tep_db_prepare_input($_GET['application_id']);
if(!$row_check=getAnyTableWhereData(JOB_TABLE ," job_id='".$job_id."'  and recruiter_id='".$_SESSION['sess_recruiterid']."'","job_id"))
{
 $messageStack->add_session(ERROR_JOB_NOT_EXIST, 'error');
 tep_redirect(FILENAME_RECRUITER_LIST_OF_JOBS);
}

$row_check_resume=getAnyTableWhereData(APPLICATION_TABLE . " as a left outer join  ".JOBSEEKER_TABLE." as j on(a.jobseeker_id=j.jobseeker_id) left outer join  ".JOBSEEKER_RESUME1_TABLE." as r on (a.resume_id=r.resume_id)","a.application_id='".tep_db_input($application_id)."' and a.job_id='".tep_db_input($job_id)."' ","a.id,j.jobseeker_first_name,j.jobseeker_last_name,r.resume_id");
if((int)$row_check_resume['resume_id']<=0)
{
 $messageStack->add_session(ERROR_RESUME_NOT_EXIST, 'error');
 //tep_redirect(FILENAME_RECRUITER_LIST_OF_APPLICATIONS,'jobID='.$job_id);
}

if($rows=getAnyTableWhereData(RESUME_WEIGHT_TABLE ," job_id='".$job_id."'"))
{
}
else
$rows=getAnyTableWhereData(RESUME_WEIGHT_TABLE ," job_id='0'");
$match_row=(get_resume_weight($row_check_resume['resume_id'],$job_id,true));
//print_r($match_row);
$resume_location_weight=$resume_industry_weight=$resume_job_type_weight=$resume_experience_weight='--';

$job_location_weight=tep_db_output($rows['location']."%");
if($rows['location']>0)
$resume_location_weight=$match_row['location']."%";

$job_industry_weight=tep_db_output($rows['industry']."%");
if($rows['industry']>0)
$resume_industry_weight=$match_row['industry']."%";

$job_job_type_weight=tep_db_output($rows['job_type']."%");
if($rows['job_type']>0)
$resume_job_type_weight=$match_row['job_type']."%";

$job_experience_weight=tep_db_output($rows['experience']."%");
if($rows['experience']>0)
$resume_experience_weight=$match_row['experience']."%";

$total=$match_row['total']."%";

$template->assign_vars(array(
 'HEADING_TITLE'=>HEADING_TITLE,
 'INFO_TEXT_JOBSEEKER_NAME'   => tep_db_output(ucfirst($row_check_resume['jobseeker_first_name'].' '.$row_check_resume['jobseeker_last_name'])),
 'INFO_TEXT_JOBSEEKER_APP_ID' => INFO_TEXT_JOBSEEKER_APP_ID,
 'INFO_TEXT_JOBSEEKER_APP_ID1'=> tep_db_output($application_id),
 'INFO_TEXT_FIELDS'           => INFO_TEXT_FIELDS,
 'INFO_TEXT_JOB_WEIGHTS'      => INFO_TEXT_JOB_WEIGHTS,
 'INFO_TEXT_RESUME_WEIGHTS'   => INFO_TEXT_RESUME_WEIGHTS,

 'INFO_TEXT_LOCATION'         => INFO_TEXT_LOCATION,
 'INFO_TEXT_JOB_LOCATION1'    => $job_location_weight,
 'INFO_TEXT_RESUME_LOCATION1' => $resume_location_weight,

 'INFO_TEXT_INDUSTRY'         => INFO_TEXT_INDUSTRY,
 'INFO_TEXT_JOB_INDUSTRY1'    => $job_industry_weight,
 'INFO_TEXT_RESUME_INDUSTRY1' => $resume_industry_weight,

 'INFO_TEXT_EXPERIENCE'       => INFO_TEXT_EXPERIENCE,
 'INFO_TEXT_JOB_EXPERIENCE'   => $job_experience_weight,
 'INFO_TEXT_RESUME_EXPERIENCE'=>$resume_experience_weight,

 'INFO_TEXT_JOB_TYPE'         => INFO_TEXT_JOB_TYPE,
 'INFO_TEXT_JOB_JOB_TYPE1'    => $job_job_type_weight,
 'INFO_TEXT_RESUME_JOB_TYPE1' => $resume_job_type_weight,

 'INFO_TEXT_TOTAL'            => INFO_TEXT_TOTAL,
 'INFO_TEXT_TOTAL1'           => $total,
 
 'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
 'RIGHT_BOX_WIDTH'=>RIGHT_BOX_WIDTH1,
 'LEFT_HTML'=>LEFT_HTML,
 'RIGHT_HTML'=>RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('resume_match');
?>