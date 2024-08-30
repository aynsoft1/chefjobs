<?
/*
***********************************************************
***********************************************************
**********#	Name				      : Shambhu Prasad Patnaik  		#***********
**********#	Company			    : Aynsoft							     #***********
**********#	Copyright (c) www.aynsoft.com 2004	#***********
***********************************************************
***********************************************************
*/
define('HEADING_TITLE','Employer  Registration');

define('INFO_TEXT_FIRST_NAME','First Name :');
define('MIN_FIRST_NAME_ERROR','Your First Name must contain a minimum of ' . MIN_FIRST_NAME_LENGTH . ' characters.');

define('INFO_TEXT_LAST_NAME','Last Name :');
define('MIN_LAST_NAME_ERROR','Your Last Name must contain a minimum of ' . MIN_LAST_NAME_LENGTH . ' characters.');

define('SECTION_CONTACT_DETAILS','Your Contact Details');
define('INFO_TEXT_EMAIL_ADDRESS','E-Mail Address :');
define('EMAIL_ADDRESS_ERROR','Your Email-Address already exists.');
define('EMAIL_ADDRESS_INVALID_ERROR','Your Email-Address is not valid.');

define('INFO_TEXT_CONFIRM_EMAIL_ADDRESS','Confirm E-Mail Address :');
define('CONFIRM_EMAIL_ADDRESS_INVALID_ERROR','Your confirm Email-Address is not valid.');

define('EMAIL_ADDRESS_MATCH_ERROR','Your Email-address & confirm Email-Address does not match.');


##################################################
define('SECTION_COMPANY','Company Information');

define('INFO_TEXT_POSITION','Your Position :');
define('POSITION_ERROR','Please enter your position.');
define('MIN_POSITION_ERROR','Your  title must contain a minimum of ' . MIN_POSITION_LENGTH . ' characters.');

define('INFO_TEXT_COMPANY_NAME','Company Name :');
define('MIN_COMPANY_NAME_ERROR','Your Company Name must contain a minimum of ' . MIN_COMPANY_NAME_LENGTH . ' characters.');

define('INFO_TEXT_ADDRESS1','Address line1 :');
define('MIN_ADDRESS_LINE1_ERROR','Your address line1 must contain a minimum of ' . MIN_ADDRESS_LINE1_LENGTH . ' characters.');

define('INFO_TEXT_ADDRESS2','Address line2 :');
define('MIN_ADDRESS2_ERROR','');

define('INFO_TEXT_COUNTRY','Country :');
define('ENTRY_COUNTRY_ERROR', 'You must select a country from the Countries pull down menu.');

define('INFO_TEXT_CITY','City :');

define('INFO_TEXT_STATE','State / Province :');
define('ENTRY_STATE_ERROR_SELECT', 'Please select a state from the States pull down menu.');
define('ENTRY_STATE_ERROR', 'You must include your state or province');

define('INFO_TEXT_ZIP_CODE','Zip Code :');
define('ZIP_CODE_ERROR','Please enter Zip code.');

define('INFO_TEXT_TELEPHONE','Telephone number :');
define('TELEPHONE_ERROR','Please enter Telephone number.');
define('INFO_TEXT_FAX','Fax :');

define('INFO_TEXT_PHOTO','Logo : ');

define('INFO_TEXT_URL','Url : ');

define('INFO_TEXT_NEWS_LETTER','Newsletter : ');
define('INFO_TEXT_AGREEMENT','<br><b>Note : </b>When you click on the following <b>button</b>, it means that you have agreed to our <a href="'.FILENAME_TERMS.'" target="terms">Terms & Conditions </a>and <a href="'.FILENAME_PRIVACY.'" target="terms">Privacy Policy</a>');

define('MESSAGE_SUCCESS_UPDATED','Account successfully updated.');
define('MESSAGE_SUCCESS_INSERTED','Account successfully inserted.');

define('NEW_RECRUITER_SUBJECT','Thank you for registering on '.SITE_TITLE);

define('IMAGE_INSERT','Insert');
define('IMAGE_UPDATE','Update');
define('INFO_TEXT_NEW_USER_REGISTRATION_DEMO','New Recruiter Register from jobsite_demo');
define('INFO_TEXT_RECRUITER_NAME','Recruiter name : ');
define('INFO_TEXT_RECRUITER_MAIL','Recruiter email : ');
define('LOGO_UPLOAD_ERROR','Please upload a logo of your company');
define('LOGO_UPLOAD_TYPE_ERROR','Please upload a gif,jpeg,png format');

?>