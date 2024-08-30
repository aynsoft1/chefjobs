<?
/**********************************************************
**********# Name          : Shambhu Prasad Patnaik  #**********
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
**********************************************************/


if($_POST['action']=='search')
{
 define('HEADING_TITLE','Search Results');
 define('INFO_TEXT_JOB_CATEGORY','Job Category :');

}
else
{
 define('HEADING_TITLE','Create Job Alert Agent');
 define('INFO_TEXT_JOB_CATEGORY','Job Category :');
}

define('INFO_TEXT_KEYWORD','Keyword : ');
define('INFO_TEXT_KEYWORD_EXAMPLE','<font size="1"><i>&nbsp;e.g. Sales Executive</i></font>');
define('INFO_TEXT_KEYWORD_CRITERIA','Choose your search criteria :');
define('INFO_TEXT_KEYWORD_WORD1','any of these words');
define('INFO_TEXT_KEYWORD_WORD2','all of these words');

define('INFO_TEXT_LOCATION','Location');
define('INFO_TEXT_JOB_CATEGORY_TEXT','What job category would you like to work in ?');
define('INFO_TEXT_EXPERIENCE','Experience');
define('INFO_TEXT_JOB_POSTED','	Show me jobs posted within : ');
define('INFO_TEXT_DEFAULT_JOB_POST_DAY','All');


define('INFO_TEXT_COMPANY','Company ');
define('INFO_TEXT_LOCATION_NAME','Location');
define('INFO_TEXT_POSTED_ON','Posted On :');
define('INFO_TEXT_SALARY','Salary');
define('INFO_TEXT_SALARY_DOT',':');
define('INFO_TEXT_APPLY_BEFORE','Apply Before :');

define('INFO_TEXT_TITLE_NAME','Agent name: ');
define('INFO_TEXT_ALERT_TEXT','Save Search As Job Alert');

define('INFO_TEXT_APPLY_NOW','Apply Now ! ');
define('INFO_TEXT_APPLY_NOW1','Apply to multiple jobs by selecting jobs of your choice.');

define('ENTRY_STATE_ERROR_SELECT', 'Please select a state from the States pull down menu.');



define('INFO_TEXT_COUNTRY','What country would you like to work in? : ');
define('INFO_TEXT_STATE','What state would you like to work in? :');

define('MESSAGE_SUCCESS_INSERTED','Success: Your saved search successfully inserted.');
define('MESSAGE_SUCCESS_UPDATED','Success: Your saved search successfully updated.');

define('MESSAGE_ERROR_SAVED_SERCH_NOT_EXIST','Error: Sorry, this saved search does not exist.');
define('MESSAGE_ERROR_SAVED_SERCH_ALREADY_EXIST','Error: Sorry, this saved search name already exists.');

define('INFO_TEXT_JOB_TYPE','Job type :');

define('IMAGE_SEARCH','Search');
define('IMAGE_SAVE','Save search');
define('IMAGE_CANCEL','Cancel');
define('IMAGE_BACK','Back');
define('IMAGE_APPLY','Apply now');
define('INFO_TEXT_CLICK_HERE_SEE_DETAILS','click here to see details');
define('INFO_TEXT_HAS_MATCHED','has matched');
define('INFO_TEXT_TO_YOUR_SEARCH_CRITERIA','to your search criteria.');
define('INFO_TEXT_JOB','Job');
define('INFO_TEXT_JOBS','Jobs');
define('INFO_TEXT_HAS_NOT_MATCHED','has not matched any Job to your search criteria.');
define('INFO_TEXT_ALL_JOB_CATEGORY','All Job categories...');
define('INFO_TEXT_ALL_COUNTRIES','All countries');
define('INFO_TEXT_REFINE_SEARCH','Refine Search');
define('INFO_TEXT_JOB_ALERT_CRITERIA','Get Job Alerts');
define('INFO_TEXT_DAILY','Daily');
define('INFO_TEXT_WEEKLY','Weekly');
define('INFO_TEXT_MONTHLY','Monthly');
define('INFO_TEXT_SEARCH_US_ZIP','US Zip');
define('INFO_TEXT_ZIP_CODE','Zip Code');
define('INFO_TEXT_RADIUS','Radius');
define('INFO_TEXT_SEARCH_COUNTRY_STATE','Country/State');
define('INFO_TEXT_EMAIL_THIS_JOB','Email This Job');
define('MORE_DETAILS','More Details');
define('INFO_TEXT_APPLY_TO_THIS_JOB','Apply to this Job');
define('ENTER_AGENT_NAME_ERROR','Please enter Agent name');

define('INFO_P_ANY_SAL','Any Salary');
define('INFO_TEXT_JOB_SALARY','Salary');
?>