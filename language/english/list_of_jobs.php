<?
/**********************************************************
**********# Name          : Shambhu Prasad Patnaik  #**********
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
**********************************************************/


if($_GET['action']=='add_screener' || $_GET['action']=='edit_screener')
{
 define('HEADING_TITLE', 'Add/Edit Screener');
}
else
{
 define('HEADING_TITLE', '%s jobs');
}
//////////////////////////
define('HEADING_TITLE_READV', 'Re-advertise Job');
define('INFO_TEXT_READVERTISE', 'Re-advertise from : ');
define('INFO_TEXT_ADVERTISE_WEEKS','How many weeks would you like to advertise this vacancy? :');
////////////
define('TEXT_INFO_EDIT_JOB_INTRO', 'Please select job for make action.');
define('TEXT_DELETE_INTRO', 'Do you want to delete this job?');
define('TEXT_SCREENER_DELETE_INTRO', 'Do you want to delete this screener?');
if($_GET['j_status']=='deleted')
 define('TEXT_DELETE_WARNING', '<font color="red"><b>Warning:</b></font> With this job all the data of this job will also be deleted.');
else
 define('TEXT_DELETE_WARNING', '<font color="red"><b>Warning: </b></font>Job will not be physically deleted from the database. Simply it will go to the <b>deleted jobs</b> category.');

define('TEXT_DELETE_SCREENER_WARNING', '<font color="red"><b>Warning: </b></font>Screener will be physically deleted from the database.');

define('TEXT_INFO_NEW_JOB_INTRO', 'No job information is added.');
define('TEXT_INFO_JOB_INSERTED', 'Job Added on:');
define('TEXT_INFO_JOB_UPDADED', 'Job Modified on:');
define('TEXT_INFO_FULLNAME', 'Name:');
define('TEXT_INFO_EMAIL', 'Email-address:');
define('TEXT_INFO_JOB_STARTS', 'Job Starts on:');
define('TEXT_INFO_JOB_ENDS', 'Job Ends on:');
define('TEXT_INFO_JOB_JOB_STATUS', 'Job Status:');
define('TEXT_INFO_JOB_NO_OF_JOBS', 'Max. No of Jobs:');
define('TEXT_INFO_JOB_CV_STATUS', 'CV Status:');
define('TEXT_INFO_JOB_NO_OF_CVS', 'Max. No of Days to search CV:');



define('TABLE_HEADING_REFERENCE', 'Reference');
define('TABLE_HEADING_TITLE', 'Title');
define('TABLE_HEADING_INSERTED', 'Added');
define('TABLE_HEADING_EXPIRED', 'Expiring On');
define('TABLE_HEADING_STATUS', 'Status');
define('TABLE_HEADING_VIEWED', 'Viewed');
define('TABLE_HEADING_CLICKED', 'Clicked');
define('TABLE_HEADING_APPLICATIONS', 'Application');
define('TABLE_HEADING_ACTION', 'Action');

define('STATUS_JOB_INACTIVE', 'Inactive');
define('STATUS_JOB_INACTIVATE', 'Inactivate?');

define('STATUS_JOB_ACTIVE', 'Active');
define('STATUS_JOB_ACTIVATE', 'Activate?');


define('MESSAGE_SUCCESS_DELETED','Success: Job successfully deleted.');
define('MESSAGE_SUCCESS_UPDATED','Success: Job successfully updated.');

define('MESSAGE_UNSUCCESS_SCREENER_DELETED','error: Due to some problem screener is not deleted.');
define('MESSAGE_SUCCESS_SCREENER_DELETED','Success: Screener successfully deleted.');
define('MESSAGE_SUCCESS_JOB_UNDELETED','Success: Job is successfully Re-added.');

define('MESSAGE_SUCCESS_SCREENER_INSERTED','Success: Screener successfully inserted.');
define('MESSAGE_SUCCESS_SCREENER_UPDATED','Success: Screener successfully updated.');
define('MESSAGE_JOB_SUCCESS_READVERTISED','Success: Job successfully re-advertised.');
define('MESSAGE_JOB_UNSUCCESS_READVERTISED','Error: Due to some reason job cannot be re-advertised.');
define('MESSAGE_JOB_UNSUCCESS_READVERTISED1','Error: You have remaining %s job point\'s. please reduce your vacancy weeks');
define('MESSAGE_JOB_UNSUCCESS_READVERTISED2','Error: You have remaining %s job point. please contact  admin');

define('MESSAGE_JOB_ERROR','Sorry this job doesnot exist or cannot be re-advertised, if exists then wait untill expired. If problem persists please contact admin of the site.');

define('MESSAGE_SUCCESS_STATUS_UPDATED','Job successfully updated.');


define('INFO_TEXT_RESUME_WEIGHT','CV Weight');

define('IMAGE_NEW','Add New Job');
define('IMAGE_BACK','Back');
define('IMAGE_NEXT','Next');
define('IMAGE_CANCEL','Cancel');
define('IMAGE_INSERT','Insert');
define('IMAGE_EDIT','Edit');
define('IMAGE_UPDATE','Update');
define('IMAGE_DELETE','Delete');
define('IMAGE_CONFIRM','Confirm Delete');
define('IMAGE_PREVIEW','Preview Job');
define('IMAGE_UPDATE','Update');
define('IMAGE_EDIT_JOB','Edit');
define('IMAGE_DELETE_JOB','Delete');
define('IMAGE_UNDELETE_JOB','Un-delete job');
define('IMAGE_READVERTISE','Re-advertise job');
define('IMAGE_APPLICATIONS','Applications');
define('IMAGE_ADD_SCREENER','Add screener');
define('IMAGE_EDIT_SCREENER','Edit screener');
define('IMAGE_DELETE_SCREENER','Delete screener');
define('IMAGE_REPORT','Reports');
define('IMAGE_SELECTED_APPLICATIONS','Selected');

define('ERROR_QUESTION','You have to fill-up Question no. <b>%s</b> first.');
define('IMAGE_VIEW_JOB','View');
define('INFO_TEXT_QUESTION','Question-');
define('INFO_TEXT_ACTIVE_JOBS','Active Jobs');
define('INFO_TEXT_EXPIRED_JOBS','Expired Jobs');
define('INFO_TEXT_DELETED_JOBS','Deleted Jobs');
define('INFO_TEXT_OTHER_JOBS','Other Jobs');
define('INFO_TEXT_SPECIFY_VACANCY_PERIOD','Specify vacancy period...');
define('INFO_TEXT_ONE_WEEK','One week');
define('INFO_TEXT_TWO_WEEKS','Two weeks');
define('INFO_TEXT_THREE_WEEKS','Three weeks');
define('INFO_TEXT_ONE_MONTH','One month');
define('INFO_TEXT_YES','Yes');
define('INFO_TEXT_NO','No');
define('INFO_TEXT_SCREENER_QUESTION','You can add as few or up to ten screener questions to this posting. When job seekers apply to this posting, they are presented with these questions as part of the application proccess. Adding screener questions can assist you in pre-qualifing and rating candidates.<br>
             <em>This is an optional feature and is not required to post this position. </em>');
define('INFO_TEXT_ADD_UPTO_FIVE','Add up to five open ended questions.<br>
             Open ended questions allow for the job seeker to answer in a text box, using his or her own words. An example of a possible open ended question is &quot;What are your strengths and weaknesses?&quot; ');
?>