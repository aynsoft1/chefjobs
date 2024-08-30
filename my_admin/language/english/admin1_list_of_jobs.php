<?
define('HEADING_TITLE', '%s jobs');

define('TEXT_INFO_EDIT_JOB_INTRO', 'Please make any necessary changes.');
define('TEXT_DELETE_INTRO', 'Do you want to delete this job?');
if($_GET['j_status']=='deleted')
 define('TEXT_DELETE_WARNING', '<font color="red"><b>Warning:</b></font> With this job all the data of this job will also be deleted.');
else
 define('TEXT_DELETE_WARNING', '<font color="red"><b>Warning: </b></font>Job will not be physically deleted from the database. Simply it will go to the <b>deleted jobs</b> category.');

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

define('TABLE_HEADING_COMPANY_NAME', 'Company Name');
define('TABLE_HEADING_TITLE', 'Job Title');
define('TABLE_HEADING_INSERTED', 'Inserted');
define('TABLE_HEADING_STATUS', 'Status');
define('TABLE_HEADING_JOB_FEATURED', 'Featured');
define('TABLE_HEADING_VIEWED', 'Viewed');
define('TABLE_HEADING_CLICKED', 'Clicked');
define('TABLE_HEADING_APPLICATIONS', 'Application');
define('TABLE_HEADING_ACTION', 'Action');

define('STATUS_JOB_INACTIVE', 'Inactive');
define('STATUS_JOB_INACTIVATE', 'Inactivate?');

define('STATUS_JOB_ACTIVE', 'Active');
define('STATUS_JOB_ACTIVATE', 'Activate?');

define('STATUS_JOB_NOT_FEATURE', 'Not featured');
define('STATUS_JOB_NOT_FEATURED', 'Not featured?');

define('STATUS_JOB_FEATURE', 'Featured');
define('STATUS_JOB_FEATURED', 'Featured?');

define('MESSAGE_SUCCESS_DELETED','Success: Job successfully deleted.');

define('MESSAGE_JOB_ERROR','Sorry no recruiter exists.');

define('MESSAGE_SUCCESS_STATUS_UPDATED','Job successfully updated.');



define('IMAGE_NEW','Add New Job');
define('IMAGE_BACK','Back');
define('IMAGE_NEXT','Next');
define('IMAGE_CANCEL','Cancel');
define('IMAGE_INSERT','Insert');
define('IMAGE_EDIT','Edit Job');
define('IMAGE_UPDATE','Update');
define('IMAGE_DELETE','Delete Job');
define('IMAGE_CONFIRM','Confirm Delete');
define('IMAGE_PREVIEW','Preview Job');

?>