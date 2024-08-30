<?
if($_POST['action']=='search')
{
 define('HEADING_TITLE', 'List of jobs');
}
else
 define('HEADING_TITLE', 'Search Jobs by Company');

define('INFO_TEXT_COMPANY_NAME','Company name : ');

define('TABLE_HEADING_JOB_TITLE', 'Job title');
define('TABLE_HEADING_COMPANY_NAME', 'Company name');
define('TABLE_HEADING_JOB_CATEGORY', 'Job category');
define('TABLE_HEADING_ADVERTISED', 'Post Date');
define('TABLE_HEADING_EXPIRED', 'Expired on');
define('TABLE_HEADING_APPLY', 'Apply');

define('MESSAGE_JOB_ERROR','Sorry this job doesnot exist. If problem persists please contact admin of the site.');
define('ERROR_NO_COMPANIES_EXISTS','Error : Sorry, there are no companies.');

define('IMAGE_RATE','Rate');
define('IMAGE_BACK','Back');
define('INFO_TEXT_APPLY_NOW','apply&nbsp;now');
define('INFO_TEXT_JOB','job');
define('INFO_TEXT_JOBS','jobs');
define('INFO_TEXT_HAS_MATCHED',' has matched');
define('INFO_TEXT_TO_YOUR_SEARCH_CRITERIA','to your search criteria.');
define('INFO_TEXT_HAS_NOT_MATCHED','has not matched any jobs to your search criteria.');
define('INFO_TEXT_HAVE','has');
define('INFO_TEXT_COMPANY_IN_DIRECTORY','companies in company directory.');
define('INFO_TEXT_NO_COMPANY_DIRECTORY','No Company in company directory.');

//*********************************************************************//
define('INFO_TEXT_CURRENT_RATING','Current rating  : ');
define('INFO_TEXT_CURRENT_RATE_IT','Rate it  ');
define('INFO_TEXT_CURRENT_RATE_STRING','');
define('SECTION_RATE_COMPANY','Rate Company');
define('INFO_TEXT_NOT_RATED','Not Rated');
define('MESSAGE_SUCCESS_RATED','Recruiter successfully rated.');
define('MESSAGE_SUCCESS_FOLLOW','Success: You will receive regular updates from the Company.');
?>