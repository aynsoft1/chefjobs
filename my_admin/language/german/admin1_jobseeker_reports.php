<?
if($_POST['email']=='email')
{
 define('HEADING_TITLE', 'Send Email');
}
else if($_POST['action']=='search')
{
 define('HEADING_TITLE', 'List of Jobseekers');
}
else
 define('HEADING_TITLE', 'Search jobseekers');

define('INFO_TEXT_KEYWORD','Keyword : ');
define('INFO_TEXT_KEYWORD_EXAMPLE','<font size="1"><i>&nbsp;e.g. Sales Executive</i></font>');
define('INFO_TEXT_PLACEHOLDER_EXAMPLE','e.g. Sales Executive');
define('ENTRY_STATE_ERROR_SELECT', 'Please select a state from the States pull down menu.');
define('INFO_TEXT_KEYWORD_CRITERIA','<i>Choose your search criteria:</i>');
define('INFO_TEXT_KEYWORD_CRITERIA','<i>Choose your search criteria:</i>');
define('INFO_TEXT_KEYWORD_WORD1','any of the above words or phrases');
define('INFO_TEXT_KEYWORD_WORD2','exact match of words or phrases');
define('INFO_TEXT_START_DATE','From : ');
define('INFO_TEXT_END_DATE','To : ');
define('INFO_TEXT_FIRST_NAME','First name : ');
define('INFO_TEXT_LAST_NAME','Last name : ');
define('INFO_TEXT_EMAIL_ADDRESS','Email-address : ');
define('INFO_TEXT_COUNTRY','Country : ');
define('INFO_TEXT_STATE','State : ');
define('INFO_TEXT_CITY','City : ');
define('INFO_TEXT_INDUSTRY_SECTOR','Job category: ');
define('TABLE_HEADING_NAME','Name ');
define('TABLE_HEADING_EMAIL_ADDRESS','Email-address ');
define('TABLE_HEADING_INSERTED','Inserted ');
define('TABLE_HEADING_ACTION','Action ');

define('TEXT_INFO_EDIT_ACCOUNT_INTRO', 'Please make any necessary changes.');

define('TEXT_INFO_IP_ADDRESS', 'Last IP address');
define('TEXT_INFO_UPDATED', 'Last updated on');
define('TEXT_INFO_LAST_LOGIN', 'Last login on');
define('TEXT_INFO_NUMBER_OF_LOGON', 'No of logon');

// email sending 
define('TEXT_TO', 'To:');
define('TEXT_SUBJECT', 'Subject:');
define('MAIL_ATTACHMENT', 'Attachment :');
define('TEXT_FROM', 'From:');
define('TEXT_MESSAGE', 'Message:');

define('MESSAGE_SUCCESS_SENT', 'Success: Message successfully sent.');

define('IMAGE_EMAIL','E-mail');
define('IMAGE_SEND_MAIL', 'Send email');
define('IMAGE_PREVIEW_MAIL', 'Preview email');
define('IMAGE_BACK', 'Back');
define('IMAGE_CANCEL','Cancel');
define('IMAGE_PREVIEW','Preview');
define('IMAGE_EDIT','Edit');
define('IMAGE_SEARCH','Search');
define('IMAGE_EXCEL','create excel report');
?>