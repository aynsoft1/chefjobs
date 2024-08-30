<?
/**********************************************************
**********# Name          : Shambhu Prasad Patnaik  #**********
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
**********************************************************/


define('HEADING_TITLE','Register - Job Seeker');
if(check_login("jobseeker"))
	define('INFO_TEXT_CREATE_ACCOUNT','Edit Profile');
else
	define('INFO_TEXT_CREATE_ACCOUNT','Create Your Free Account');

define('INFO_TEXT_JOIN','join using');
define('SECTION_ACCOUNT_DETAILS','Account Details');
define('SECTION_CONTACT_DETAILS','Personal Details');
define('INFO_TEXT_UPLOAD_RESUME','Upload resume:');
define('INFO_TEXT_UPLOAD_RESUME_HELP','Upload: doc, txt, pdf format');
define('SECTION_ACCOUNT_PRIVACY','Privacy setting');
define('INFO_TEXT_PRIVACY_HIDE_ALL','Hide my contact information from all employers.');
define('INFO_TEXT_PRIVACY_HIDE_CONTACT','Show my contact information to the employers to whom I have applied.');
define('INFO_TEXT_PRIVACY_HIDE_NOTHING','Show my contact information to all employers.');
define('INFO_TEXT_PRIVACY_HIDE_RESUME','Private � I don�t want employers to find my resume.');
define('INFO_TEXT_ALREADY_MEMBER','Already a Member?');


define('SECTION_PASSWORD','Your Password');
define('SECTION_ACCOUNT_RESUME_NAME','Resume name');

define('INFO_TEXT_PRIVACY','Privacy :');
define('INFO_TEXT_RESUME_SEARCHEABLE','My Resume is searchable :');
define('PRIVACY_ERROR','Please select privacy.');

define('MIN_FIRST_NAME_ERROR','First Name must contain a minimum of ' . MIN_FIRST_NAME_LENGTH . ' characters.');
define('MIN_LAST_NAME_ERROR','Last Name must contain a minimum of ' . MIN_LAST_NAME_LENGTH . ' characters.');
define('EMAIL_ADDRESS_ERROR','Email-Address already exists.');
define('EMAIL_ADDRESS_INVALID_ERROR','Please enter valid Email Address.');
define('CONFIRM_EMAIL_ADDRESS_INVALID_ERROR','Your confirm Email-Address is not valid.');
define('EMAIL_ADDRESS_MATCH_ERROR','Your Email-address & confirm Email-Address does not match.');
define('MIN_PASSWORD_ERROR','Your Password must contain a minimum of ' . MIN_PASSWORD_LENGTH . ' characters.');
define('MIN_CONFIRM_PASSWORD_ERROR','Your Confirm Password must contain a minimum of ' . MIN_PASSWORD_LENGTH . ' characters.');
define('PASSWORD_MATCH_ERROR','Your password & confirm password does not match.');
define('MIN_ADDRESS_LINE1_ERROR','Address must contain a minimum of ' . MIN_ADDRESS_LINE1_LENGTH . ' characters.');
define('ENTRY_COUNTRY_ERROR', 'Please select Country from the Countries pull down menu.');

define('PLEASE_SELECT','Please select...');
define('ENTRY_STATE_ERROR_SELECT', 'Please select State from the States pull down menu.');
define('ENTRY_STATE_ERROR', 'You must include your state or province');
define('MIN_CITY_ERROR','City must contain a minimum of ' . MIN_CITY_LENGTH . ' characters.');
define('MIN_ZIP_ERROR', 'Zip code must contain a minimum of ' . MIN_ZIP_LENGTH . ' characters.');
define('ENTRY_HOME_PHONE_ERROR', 'Please enter Primary Phone Number.');

define('INFO_TEXT_NEWS_LETTER','newsletter?');
define('INFO_TEXT_AGREEMENT','By continuing, you acknowledge that you accept our <a href="'.FILENAME_TERMS.'">Terms & Conditions</a> and <a href="'.FILENAME_PRIVACY.'">Privacy Policy</a> ');


define('NEW_JOBSEEKER_SUBJECT','Thank you for registering on '.SITE_TITLE);
define('NEW_JOBSEEKER_EMAIL_TEXT','Dear <b>%s</b>,'."\n\n".'Thank you for registering on '.SITE_TITLE."\n\n".'Your username: <b>%s</b>'."\n\n".'Your password: xxxxx'."\n\n".'You can access our site by this username/password.'. "\n\n" .'Thanks!' . "\n" . '%s ( Admin )'."\n\n" . 'This is an automated response, please do not reply!');

define('MESSAGE_SUCCESS_UPDATED','Account successfully updated.');
define('MESSAGE_SUCCESS_INSERTED','Account successfully inserted.');

define('NEW_RECRUITER_SUBJECT','Success registration at '.SITE_TITLE);

define('CAPTCHA_ERROR','Captcha Error');

define('IMAGE_INSERT','Insert');
define('IMAGE_UPDATE','Update');
define('IMAGE_NEXT','Next >>');
define('INFO_TEXT_NEW_JOBSEEKER_REGISTER','New Jobseeker Register from jobsite_demo');
define('INFO_TEXT_JOBSEEKER_NAME','Jobseeker name');
define('INFO_TEXT_JOBSEEKER_EMAIL','jobseeker email');
define('INFO_TEXT_YES','Yes');
define('INFO_TEXT_NO','No');
define('INFO_TEXT_SUBSCRIBE','Newsletter');
define('INFO_TEXT_PLEASE_SELECT_COUNTRY','Please select Country');

/*placeholders*/
define('INFO_P_PASSWORD','Password');
define('INFO_P_EMAIL_ADDRESS','Email Address');
define('INFO_P_FNAME','First name');
define('INFO_P_LNAME','Last name');
define('INFO_P_FULL_ADD','Full address');
define('INFO_P_ZIP','Zip');
define('INFO_P_CITY','City/town');
define('INFO_P_PHONE','Contact No');
define('INFO_P_SUBSCRIBE','Newsletter');
define('INFO_P_ALREADY_MEMBER','Already a Member?');
define('INFO_P_NATIONALITY','Nationality');
define('INFO_P_SIGN_IN','Sign in');
define('INFO_P_SIGN_UP','Sign Up');
define('INFO_P_MOBILE','Mobile');
define('INFO_P_JOIN_USING','or');
define('INFO_P_STATE','State');


define('WITH_FACEBOOK','Continue with Facebook');
define('WITH_GOOGLE','Continue with Google');
define('WITH_LINKEDIN','Continue with Linkedin');
define('WITH_TWITTER','Continue with Twitter');


?>