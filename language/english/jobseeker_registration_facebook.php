<?
/**********************************************************
**********# Name          : Shambhu Prasad Patnaik  #**********
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
**********************************************************/


define('HEADING_TITLE','Job Seeker Registration');

define('REQUIRED_INFO','* Required information');
define('SECTION_ACCOUNT_DETAILS','Account Details');
define('SECTION_ACCOUNT_PRIVACY','Privacy Information');
define('SECTION_CONTACT_DETAILS','Personal Details');
define('SECTION_ACCOUNT_RESUME_NAME','Resume name');

define('INFO_TEXT_PRIVACY','Privacy :');
define('INFO_TEXT_RESUME_SEARCHEABLE','My Resume is searchable :');
define('PRIVACY_ERROR','Please select privacy.');
define('INFO_TEXT_PRIVACY_HIDE_ALL','Hide my contact information from all employers.');
define('INFO_TEXT_PRIVACY_HIDE_CONTACT','Show my contact information to the employers to whom I have applied.');
define('INFO_TEXT_PRIVACY_HIDE_NOTHING','Show my contact information to all employers.');
define('INFO_TEXT_PRIVACY_HIDE_RESUME','Private – I don’t want employers to find my resume.');

define('INFO_TEXT_FIRST_NAME','First Name :');
define('MIN_FIRST_NAME_ERROR','First Name must contain a minimum of ' . MIN_FIRST_NAME_LENGTH . ' characters.');

define('INFO_TEXT_MIDDLE_NAME','Middle Name :');

define('INFO_TEXT_LAST_NAME','Last Name :');
define('MIN_LAST_NAME_ERROR','Last Name must contain a minimum of ' . MIN_LAST_NAME_LENGTH . ' characters.');

define('INFO_TEXT_EMAIL_ADDRESS','E-Mail Address :');
define('EMAIL_ADDRESS_INVALID_ERROR','Please enter valid Email Address.');


define('INFO_TEXT_ADDRESS1','Home Address1 :');
define('MIN_ADDRESS_LINE1_ERROR','Address must contain a minimum of ' . MIN_ADDRESS_LINE1_LENGTH . ' characters.');

define('INFO_TEXT_ADDRESS2','Home Address2 :');

define('INFO_TEXT_NATIONALITY', 'Nationality :');
define('ENTRY_NATIONALITY_ERROR', 'Please select Nationality.');


define('INFO_TEXT_COUNTRY','Country :');
define('ENTRY_COUNTRY_ERROR', 'Please select Country.');

define('PLEASE_SELECT','Please select...');

define('INFO_TEXT_STATE','State/Province :');
define('ENTRY_STATE_ERROR_SELECT', 'Please select state from the States pull down menu.');
define('ENTRY_STATE_ERROR', 'Please enter state or province');

define('INFO_TEXT_CITY','City/Town :');
define('MIN_CITY_ERROR','City must contain a minimum of ' . MIN_CITY_LENGTH . ' characters.');

define('INFO_TEXT_ZIP','Zip Code :');
define('MIN_ZIP_ERROR', 'Zip code must contain a minimum of ' . MIN_ZIP_LENGTH . ' characters.');

define('INFO_TEXT_HOME_PHONE','Phone Number : ');
define('ENTRY_HOME_PHONE_ERROR', 'Please enter Primary Phone Number.');
define('INFO_TEXT_MOBILE','Mobile/Cell : ');

define('INFO_TEXT_NEWS_LETTER','Newsletter : ');

define('INFO_TEXT_AGREEMENT','<br><b>Note : </b>When you click on the following <b>button</b>, it means that you have agreed to our <a href="'.FILENAME_TERMS.'" target="terms">Terms & Conditions </a>and <a href="'.FILENAME_PRIVACY.'" target="terms">Privacy Policy</a>');

define('NEW_JOBSEEKER_SUBJECT','Thank you for registering on '.SITE_TITLE);
define('NEW_JOBSEEKER_EMAIL_TEXT','Dear <b>%s</b>,'."\n\n".'Thank you for registering on '.SITE_TITLE."\n\n".'Your username: <b>%s</b>'."\n\n".'Your password: <b>%s</b>'."\n\n".'You can access our site by this username/password.'. "\n\n" .'Thanks!' . "\n" . '%s ( Admin )'."\n\n" . 'This is an automated response, please do not reply!');

define('MESSAGE_SUCCESS_UPDATED','Account successfully updated.');
define('MESSAGE_SUCCESS_INSERTED','Account successfully inserted.');

define('NEW_RECRUITER_SUBJECT','Success registration at '.SITE_TITLE);

define('IMAGE_INSERT','Insert');
define('IMAGE_UPDATE','Update');
define('IMAGE_NEXT','Next >>');
define('INFO_TEXT_NEW_JOBSEEKER_REGISTER','New Jobseeker Register from jobsite_demo');
define('INFO_TEXT_JOBSEEKER_NAME','Jobseeker name');
define('INFO_TEXT_JOBSEEKER_EMAIL','jobseeker email');
define('INFO_TEXT_YES','Yes');
define('INFO_TEXT_NO','No');
define('INFO_TEXT_SUBSCRIBE','Subscribe');
define('INFO_TEXT_PLEASE_SELECT_COUNTRY','Please select Country');
?>