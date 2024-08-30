<?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2009  #**********
***********************************************************
*/
include_once("../include_files.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_CRON);
$template->set_filenames(array('cron' => 'admin1_cron.htm'));
include_once(FILENAME_ADMIN_BODY);
$template->assign_vars(array(
 'HEADING_TITLE'                   => HEADING_TITLE,
	'INFO_TEXT_DELETE_WHOISONLINE'    => HOST_NAME.PATH_TO_CRON_JOB.'who_is_online.php',
	'INFO_TEXT_JOB_CRON_URL'          => HOST_NAME.PATH_TO_CRON_JOB.'create_xml_file.php',
	'INFO_TEXT_ALL_JOB_CRON_URL'      => HOST_NAME.PATH_TO_CRON_JOB.'all_jobs.php',
	'INFO_TEXT_DAILY_REPORT_CRON_URL' => HOST_NAME.PATH_TO_CRON_JOB.'daily_report.php',
	'INFO_TEXT_EXPIRED_JOB_ALERT_CRON_URL'=> HOST_NAME.PATH_TO_CRON_JOB.'expired_job_alert.php',
	'INFO_TEXT_RECRUITER_ALERT_EXPIRE_CRON_URL'=>HOST_NAME.PATH_TO_CRON_JOB.'expired_recruiter_account_alert.php',
	'INFO_TEXT_FORUM_CRON_URL'=>HOST_NAME.PATH_TO_CRON_JOB.'forum.php',
	'INFO_TEXT_JOB_ALERT_CRON_URL'=>HOST_NAME.PATH_TO_CRON_JOB.'job_alert.php',
    'INFO_TEXT_JOB_ALERT_DIRECT_CRON_URL'=>HOST_NAME.PATH_TO_CRON_JOB.'job_alert_direct.php',
	'INFO_TEXT_REPLY_CRON_URL'=>HOST_NAME.PATH_TO_CRON_JOB.'reply.php',
	'INFO_TEXT_RESUME_ALERT_CRON_URL'=>HOST_NAME.PATH_TO_CRON_JOB.'resume_alert.php',
	'INFO_TEXT_TOPIC_CRON_URL'=>HOST_NAME.PATH_TO_CRON_JOB.'topics.php',
	'INFO_TEXT_DATABASE_BACKUP_CRON_URL'=>HOST_NAME.PATH_TO_CRON_JOB.'database_backup.php',
	'INFO_TEXT_PRUNE_DATABASE_CRON_URL'=>HOST_NAME.PATH_TO_CRON_JOB.'prune_database_backup.php',
	'INFO_TEXT_PAGE_RANK_CRON_URL'=>HOST_NAME.PATH_TO_CRON_JOB.'set_page_rank.php',
 'update_message'=>$messageStack->output()));
$template->pparse('cron');
?>