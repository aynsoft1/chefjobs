<?
/*
***********************************************************
***********************************************************
**********# Name          : Kamal Kumar Sahoo   #**********
**********# Company       : Aynsoft             #**********
**********# Date Created  : 23/07/05            #**********
**********# Date Modified : 23/07/05            #**********
**********# Copyright (c) www.aynsoft.com 2005  #**********
***********************************************************
***********************************************************
*/
if ($_GET['gID']) 
{
 define('HEADING_TITLE', 'Admin groups');
} 
elseif ($_GET['gPath']) 
{
 define('HEADING_TITLE', 'Define groups');
} 
else 
{
 define('HEADING_TITLE', 'Admin members');
}

define('TEXT_INFO_HEADING_EDIT_GROUP', 'Admin group');
define('TEXT_INFO_EDIT_GROUP_INTRO', 'Please make any necessary changes.');



define('TEXT_COUNT_GROUPS', 'Groups : ');
define('TEXT_INFO_LOGINNAME', 'Login Name : ');

define('TABLE_HEADING_NAME', 'Login name');
define('TABLE_HEADING_EMAIL', 'E-mail address');
define('TABLE_HEADING_PASSWORD', 'Password');
define('TABLE_HEADING_CONFIRM', 'Confirm password');
define('TABLE_HEADING_GROUPS', 'Groups level');
define('TABLE_HEADING_CREATED', 'Account created');
define('TABLE_HEADING_MODIFIED', 'Account modified');
define('TABLE_HEADING_LOGDATE', 'Last access');
define('TABLE_HEADING_LOG_NUM', 'Log number');
define('TABLE_HEADING_ACTION', 'Action');

define('TABLE_HEADING_GROUPS_NAME', 'Group name');
define('TABLE_HEADING_GROUPS_DEFINE', 'Boxes and files selection');
define('TABLE_HEADING_GROUPS_GROUP', 'Level');
define('TABLE_HEADING_GROUPS_CATEGORIES', 'Categories permission');


define('TEXT_INFO_HEADING_DEFAULT', 'Admin member ');
define('TEXT_INFO_HEADING_DELETE', 'Delete permission ');
define('TEXT_INFO_HEADING_EDIT', 'Edit category / ');
define('TEXT_INFO_HEADING_NEW', 'New admin member ');

define('TEXT_INFO_DEFAULT_INTRO', 'Member group');
define('TEXT_INFO_DELETE_INTRO', 'Remove <nobr><b>%s</b></nobr> from <nobr>admin members?</nobr>');
define('TEXT_INFO_DELETE_INTRO_NOT', 'You can not delete <nobr>%s group!</nobr>');
define('TEXT_INFO_FULLNAME', 'Name : ');
define('TEXT_INFO_FIRSTNAME', 'First name : ');
define('TEXT_INFO_LASTNAME', 'Last name : ');
define('TEXT_INFO_EMAIL', 'E-mail address : ');
define('TEXT_INFO_PASSWORD', 'Password : ');
define('TEXT_INFO_CONFIRM', 'Confirm password : ');
define('TEXT_INFO_CREATED', 'Account created : ');
define('TEXT_INFO_MODIFIED', 'Account modified : ');
define('TEXT_INFO_LOGDATE', 'Last access : ');
define('TEXT_INFO_LOGNUM', 'Log number : ');
define('TEXT_INFO_GROUP', 'Group level : ');
define('TEXT_INFO_ERROR_EMAIL', '<font color="red">E-mail address has already been used! Please try again.</font>');
define('TEXT_INFO_ERROR_ADMINNAME', '<font color="red">Login name has already been used! Please try again.</font>');

define('JS_ALERT_FIRSTNAME', '- Required: First name \n');
define('JS_ALERT_LASTNAME', '- Required: Last name \n');
define('JS_ALERT_EMAIL', '- Required: E-mail address \n');
define('JS_ALERT_EMAIL_FORMAT', '- E-mail address format is invalid! \n');
define('JS_ALERT_EMAIL_USED', '- E-mail address has already been used! \n');
define('JS_ALERT_LEVEL', '- Required: Group member \n');

define('ADMIN_EMAIL_SUBJECT', 'New admin member');
define('ADMIN_ADD_EMAIL_TEXT', 'Hi %s,' . "\n\n" . 'You can access the admin panel with the following password. Once you access the admin, please change your password!' . "\n\n" . 'Website : %s' . "\n" . 'Username: %s' . "\n" . 'Password: %s' . "\n\n" . 'Thanks!' . "\n" . '%s' . "\n\n" . 'This is an automated response, please do not reply!'); 
define('ADMIN_EMAIL_EDIT_SUBJECT', 'Admin member profile edit');
define('ADMIN_EMAIL_EDIT_TEXT', 'Hi %s,' . "\n\n" . 'Your personal information has been updated by an administrator.' . "\n\n" . 'Website : %s' . "\n" . 'Username: %s' . "\n" .  "\n\n" . 'Thanks!' . "\n" . '%s' . "\n\n" . 'This is an automated response, please do not reply!'); 

define('TEXT_INFO_HEADING_DEFAULT_GROUPS', 'Admin group ');
define('TEXT_INFO_HEADING_DELETE_GROUPS', 'Delete group ');

define('TEXT_INFO_DEFAULT_GROUPS_INTRO', '<b>NOTE:</b><li><b>edit:</b> edit group name.</li><li><b>delete:</b> delete group.</li><li><b>new permission:</b> define group access.</li>');
define('TEXT_INFO_DELETE_GROUPS_INTRO', 'It\'s also will delete member of this group. Are you sure want to delete <nobr><b>%s</b> group?</nobr>');
define('TEXT_INFO_DELETE_GROUPS_INTRO_NOT', 'You can not delete this groups!');
define('TEXT_INFO_GROUPS_INTRO', 'Give an unique group name. Click next to submit.');

define('TEXT_INFO_HEADING_GROUPS', 'New group');
define('TEXT_INFO_GROUPS_NAME', ' <b>Group name : </b><br>Give an unique group name. Then, click next to submit.<br>');
define('TEXT_INFO_GROUPS_NAME_FALSE', '<font color="red"><b>ERROR:</b> Group name cannot be blank!</font>');
define('TEXT_INFO_GROUPS_NAME_USED', '<font color="red"><b>ERROR:</b> Group name has already been used!</font>');
define('TEXT_INFO_GROUPS_LEVEL', 'Group level : ');
define('TEXT_INFO_GROUPS_BOXES', '<b>Boxes Permission : </b><br>Give access to selected boxes.');
define('TEXT_INFO_GROUPS_BOXES_INCLUDE', 'Include files stored in: ');

define('TEXT_INFO_HEADING_DEFINE', 'Define Group');
if ($_GET['gPath'] == 1) 
{
  define('TEXT_INFO_DEFINE_INTRO', '<b>%s :</b><br>You can not change file permission for this group.<br><br>');
} 
else 
{
  define('TEXT_INFO_DEFINE_INTRO', '<b>%s :</b><br>Change permission for this group by selecting or unselecting boxes and files provided. Click <b>save</b> to save the changes.<br><br>');
}
define('TEXT_NONE','-None-');

define('IMAGE_NEW','New');
define('IMAGE_SAVE','Save');
define('IMAGE_BACK','Back');
define('IMAGE_NEXT','Next');
define('IMAGE_CANCEL','Cancel');
define('IMAGE_INSERT','Insert');
define('IMAGE_EDIT','Edit');
define('IMAGE_UPDATE','Update');
define('IMAGE_DELETE','Delete');
define('IMAGE_CONFIRM','Confirm delete');
define('IMAGE_NEW_MEMBER','New member');
define('IMAGE_GROUPS','Groups list');
define('IMAGE_NEW_GROUP','New group');
define('IMAGE_FILE_PERMISSION','File permission');
define('TEXT_DISPLAY_NUMBER_OF_MEMBERS', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> members)');
?>