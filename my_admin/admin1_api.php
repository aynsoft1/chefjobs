<?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2009  #**********
***********************************************************
*/
include_once("../include_files.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_API);
$template->set_filenames(array('api' => 'admin1_api.htm'));
include_once(FILENAME_ADMIN_BODY);
$template->assign_vars(array(
 'HEADING_TITLE'                   => HEADING_TITLE,
 'URL_JLOGIN'=>HOST_NAME.'app/jobseeker_login/',
 'URL_LOGOUT'=>HOST_NAME.'app/logout/',
 'URL_INFO'=>HOST_NAME.'app/jobseeker_info/',
 'URL_JREG'=>HOST_NAME.'app/jobseeker_reg/',
 'URL_SEARCH'=>HOST_NAME.'app/search/',
 'URL_RESUME'=>HOST_NAME.'app/jobseeker_resumes/',
 'URL_APPLY'=>HOST_NAME.'app/apply_job/',
 'URL_APPLIEDJOBS'=>HOST_NAME.'app/applyed_jobs/',
 'URL_JOB_COUNTRY'=>HOST_NAME.'app/job_countries/',
 'URL_RLOGIN'=>HOST_NAME.'app/recruiter_login/',
 'URL_RINFO'=>HOST_NAME.'app/recruiter_info/',
 'URL_ADDJOB'=>HOST_NAME.'app/add_job/',

 'update_message'=>$messageStack->output()));
$template->pparse('api');
?>