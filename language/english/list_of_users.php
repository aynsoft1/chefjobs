<?
/**********************************************************
**********# Name          : Shambhu Prasad Patnaik  #**********
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
**********************************************************/


if($_GET['action1']=='change_password')
 define('HEADING_TITLE', 'Change password');
else
 define('HEADING_TITLE', 'Add/Edit user');
define('HEADING_TITLE1', 'List of users');
//////////////////////////
define('INFO_TEXT_FULL_NAME', 'Full name : ');
define('FULL_NAME_ERROR','Your full name must contain atleast a character.');

define('INFO_TEXT_EMAIL_ADDRESS','E-Mail Address :');
define('EMAIL_ADDRESS_ERROR','Your Email-Address already exists.');
define('EMAIL_ADDRESS_INVALID_ERROR','Your Email-Address is not valid.');
define('ADD_USER','Add User');

define('INFO_TEXT_CONFIRM_EMAIL_ADDRESS','Confirm E-Mail Address :');
define('CONFIRM_EMAIL_ADDRESS_INVALID_ERROR','Your confirm Email-Address is not valid.');

define('EMAIL_ADDRESS_MATCH_ERROR','Your Email-address & confirm Email-Address does not match.');

define('INFO_TEXT_PASSWORD','Password :');
define('MIN_PASSWORD_ERROR','Your Password must contain a minimum of ' . MIN_PASSWORD_LENGTH . ' characters.');

define('INFO_TEXT_CONFIRM_PASSWORD','Confirm Password :');
define('MIN_CONFIRM_PASSWORD_ERROR','Your Confirm Password must contain a minimum of ' . MIN_PASSWORD_LENGTH . ' characters.');

define('PASSWORD_MATCH_ERROR','Your password & confirm password does not match.');
define('MESSAGE_ERROR_USER','Error : Sorry, this user does not exists.');

define('TABLE_HEADING_NAME','Full name');
define('TABLE_HEADING_EMAIL_ADDRESS','E-mail address');
define('TABLE_HEADING_INSERTED','Inserted');
define('TABLE_HEADING_NUMBER_OF_JOBS','Jobs');
define('TABLE_HEADING_STATUS','Status');
define('TABLE_HEADING_CHANGE_PASSWORD','Action');
define('INFO_CHANGE_PASSWORD','Change Password');
define('INFO_DELETE_USER','Delete User');
define('MESSAGE_SUCCESS_DELETED','Success: User successfully deleted');

define('STATUS_USER_INACTIVE','Inactive');
define('STATUS_USER_ACTIVATE','Activate ?');
define('STATUS_USER_INACTIVATE','Deactivate ?');
define('STATUS_USER_ACTIVE','Active');

define('INFO_TEXT_OLD_PASSWORD','Old Password :');
define('INFO_TEXT_NEW_PASSWORD','New Password :');
define('INFO_TEXT_CONFIRM_PASSWORD','Confirm Password :');

define('MESSAGE_SUCCESS_INSERTED','Success : User successfully inserted.');
define('MESSAGE_SUCCESS_UPDATED','Success : User successfully updated.');

define('IMAGE_NEW','Add new user');
define('IMAGE_UPDATE','Update user');
define('IMAGE_CONFIRM','Confirm');
?>