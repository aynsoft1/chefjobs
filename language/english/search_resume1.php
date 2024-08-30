<?
/**********************************************************
**********# Name          : Shambhu Prasad Patnaik  #******
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
**********************************************************/


if($_POST['action']=='search')
{
 define('HEADING_TITLE', 'List of Resumes');
}
else
 define('HEADING_TITLE', 'Search Resumes');
 define('BTN_SEARCH', 'Search');



define('INFO_TEXT_RESUME_TYPE','Resume Type : ');
define('INFO_TEXT_KEYWORD','Keyword : ');
define('INFO_TEXT_KEYWORD_EXAMPLE','<font size="1"><i>&nbsp;e.g. Sales Executive</i></font>');
define('INFO_TEXT_KEYWORD_CRITERIA','<i>Choose your search criteria:</i>');
define('INFO_TEXT_KEYWORD_WORD1','any of these words');
define('INFO_TEXT_KEYWORD_WORD2','all of these words');

define('INFO_TEXT_MINIMUM_RATING','Minimum Rating : ');
define('INFO_TEXT_TO','To');
define('INFO_TEXT_MAXIMUM_RATING','Maximum Rating : ');

define('INFO_TEXT_FIRST_NAME','First name : ');
define('INFO_TEXT_LAST_NAME','Last name : ');
define('INFO_TEXT_EMAIL_ADDRESS','Email-address : ');
define('INFO_TEXT_COUNTRY','Country : ');
define('INFO_TEXT_STATE','State : ');
define('INFO_TEXT_CITY','City : ');
define('INFO_TEXT_ZIP','Zip : ');
define('INFO_TEXT_JOB_CATEGORY','Job category : ');
define('INFO_TEXT_EXPERIENCE','Experience : ');



define('TABLE_HEADING_RESUMES','Resumes');

define('TABLE_HEADING_TARGET_JOB','Target Job');
define('TABLE_HEADING_CATEGORIES','Job Categories');
define('TABLE_HEADING_CITY','City');
define('TABLE_HEADING_RESUME','Resume');
define('TABLE_HEADING_RATING','Rating');
define('TABLE_HEADING_AVAILABILITY', 'Available?');


define('STATUS_NOT_AVAILABLE', 'Not Available');
define('STATUS_AVAILABLE', 'Available');



define('IMAGE_SEARCH','Search');
define('IMAGE_SAVE','Save search');
define('IMAGE_CANCEL','Cancel');
define('IMAGE_BACK','Back');
define('INFO_TEXT_VIEW','view');
define('INFO_TEXT_HAS_MATCHED','has matched');
define('INFO_TEXT_TO_YOUR_SEARCH','to your search criteria.');
define('INFO_TEXT_RATED_ADMIN',' Rated by admin ');
define('INFO_TEXT_HAS_NOT_MATCHED','has not matched any resume to your search criteria.');
define('INFO_TEXT_IF_YOUR_SEARCH_RESULT','If your search results do not yield qualified candidates that suit your needs - please search in surrounding states or nationwide - many candidates are willing to move if you show interest in them.');
define('INFO_TEXT_ALL','All..');
define('INFO_TEXT_ALL_COUNTRIES','All countries');
define('INFO_TEXT_ALL_JOB_CATEGORIES','All Job Categories');
define('INFO_TEXT_ANY_EXPERIENCE','Any experience');
define('INFO_TEXT_NATIONALITY','Nationality');

?>