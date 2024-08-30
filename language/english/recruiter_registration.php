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
if(check_login("recruiter"))
	define('HEADING_TITLE','Edit Profile');
else
	define('HEADING_TITLE','Sign up');

define('MIN_FIRST_NAME_ERROR','Your First Name must contain a minimum of ' . MIN_FIRST_NAME_LENGTH . ' characters.');

define('MIN_LAST_NAME_ERROR','Your Last Name must contain a minimum of ' . MIN_LAST_NAME_LENGTH . ' characters.');

define('SECTION_CONTACT_DETAILS','Your Contact Details');
define('EMAIL_ADDRESS_ERROR','Your Email-Address already exists.');
define('EMAIL_ADDRESS_INVALID_ERROR','Your Email-Address is not valid.');

define('CONFIRM_EMAIL_ADDRESS_INVALID_ERROR','Your confirm Email-Address is not valid.');

define('EMAIL_ADDRESS_MATCH_ERROR','Your Email-address & confirm Email-Address does not match.');

define('SECTION_PASSWORD_DETAILS','Your Password');
define('INFO_TEXT_PASSWORD','Password :');
define('MIN_PASSWORD_ERROR','Your Password must contain a minimum of ' . MIN_PASSWORD_LENGTH . ' characters.');

define('INFO_TEXT_CONFIRM_PASSWORD','Confirm Password :');
define('MIN_CONFIRM_PASSWORD_ERROR','Your Confirm Password must contain a minimum of ' . MIN_PASSWORD_LENGTH . ' characters.');

define('PASSWORD_MATCH_ERROR','Your password & confirm password does not match.');

##################################################
define('SECTION_COMPANY','Company Information');

define('POSITION_ERROR','Please enter your position.');
//define('MIN_POSITION_ERROR','Your  title must contain a minimum of ' . MIN_POSITION_LENGTH . ' characters.');

define('MIN_COMPANY_NAME_ERROR','Your Company Name must contain a minimum of ' . MIN_COMPANY_NAME_LENGTH . ' characters.');

define('MIN_ADDRESS_LINE1_ERROR','Your address line1 must contain a minimum of ' . MIN_ADDRESS_LINE1_LENGTH . ' characters.');

define('MIN_ADDRESS2_ERROR','');

define('ENTRY_COUNTRY_ERROR', 'You must select a country from the Countries pull down menu.');

define('ENTRY_STATE_ERROR_SELECT', 'Please select a state from the States pull down menu.');
define('ENTRY_STATE_ERROR', 'You must include your state or province');

define('ZIP_CODE_ERROR','Please enter Zip code.');

define('TELEPHONE_ERROR','Please enter Telephone number.');

define('INFO_TEXT_PHOTO','Company Logo');


define('INFO_TEXT_AGREEMENT','<br>Note : When you click on the following button, it means that you have agreed to our <a href="'.FILENAME_TERMS.'" target="terms">Terms & Conditions </a>and <a href="'.FILENAME_PRIVACY.'" target="privacy">Privacy Policy</a>');

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
define('CAPTCHA_ERROR','Captcha Error');
define('INFO_PERSONAL_DETAILS','Personal Details');
define('INFO_COMPANY_DETAILS','Company Details');
define('INFO_UPLOAD_GIF','Upload: gif, jpg, jpeg, png format');
define('INFO_TEXT_NEWSLETTER','newsletter?');
define('INFO_TEXT_CONTINUE','By continuing, you acknowledge that you accept our');
define('INFO_AND','and');
define('INFO_PREVIEW','Preview');
define('INFO_JOIN_USING','or');
define('INFO_SUBSCRIBE','Newsletter');
define('INFO_ALREADY_MEMBER','Already a Member?');
define('INFO_SIGN_IN','Sign in');
define('INFO_SIGN_UP','Sign up');

/*placeholders*/
define('INFO_P_PASSWORD','Password');
define('INFO_P_EMAIL_ADDRESS','Email Address');
define('INFO_P_FNAME','First name');
define('INFO_P_LNAME','Last name');
define('INFO_P_POSITION','Your Position');
define('INFO_P_ZIP','Zip');
define('INFO_P_CITY','City/town');
define('INFO_P_WEB_ADD','Website URL i.e. http://www.aynsoft.com');
define('INFO_P_TERMS',' Terms & Conditions ');
define('INFO_P_PRIVACY',' Privacy Policy ');
define('INFO_P_COMPANY','Company Name');
define('INFO_P_JOIN_USING','or');
define('INFO_P_COUNTRY','Please select a countries...');
define('INFO_P_FULL_ADD','Full address');
define('INFO_P_TEL_NO','Telephone Number');
define('INFO_P_STATE','State');
define('INFO_TEXT_COMPANY_PROFILE','Company Profile');
define('INFO_P_COMPANY_PRO','Company');


define('WITH_FACEBOOK','Continue with Facebook');
define('WITH_GOOGLE','Continue with Google');
define('WITH_LINKEDIN','Continue with Linkedin');
define('WITH_TWITTER','Continue with Twitter');
?>
