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
$action=$_GET['action'];
if($action=='add_title' || $action=='edit_title')
 define('HEADING_TITLE','Title');
else if($action=='add_metakeyword' || $action=='edit_metakeyword')
 define('HEADING_TITLE','Metatags');
else if($action=='add_both' || $action=='edit_both')
 define('HEADING_TITLE','Title & Metatags');
else
 define('HEADING_TITLE','List of files');

define('INFO_TEXT_FILE_NAME','File name : ');
define('INFO_TEXT_TITLE','Title : ');
define('INFO_TEXT_META_TAGS','Meta tags : ');

define('INFO_TEXT_FR_TITLE','Title (German): ');
define('INFO_TEXT_FR_META_TAGS','Meta tags (German): ');

define('INFO_TEXT_ADD_BOTH','add both title and metatags');
define('INFO_TEXT_EDIT_BOTH','edit both title and metatags');
define('INFO_TEXT_ADD_META_TAGS','add meta tags');
define('INFO_TEXT_EDIT_META_TAGS','edit meta tags');
define('INFO_TEXT_ADD_TITLE','add title');
define('INFO_TEXT_EDIT_TITLE','edit title');

define('TABLE_HEADING_TITLE_META_NAME','File name');
define('TABLE_HEADING_ACTION', 'Action');
define('TEXT_INFO_HEADING_FILE_NAME','File name');
define('TEXT_INFO_FILE_NAME','File name');

define('MESSAGE_SUCCESS_DELETED','Success: File name successfully deleted.');
define('MESSAGE_SUCCESS_INSERTED','Success: File name successfully inserted.');
define('MESSAGE_SUCCESS_UPDATED','Success: File name successfully updated.');

define('MESSAGE_NAME_ERROR','Error : Sorry, this file name already exists.');
define('MESSAGE_ERROR_NOT_EXIST','Error : Sorry, this file name does not exist.');

define('TEXT_DELETE_INTRO', 'Are you sure you want to delete this file name?');
define('TEXT_INFO_NEW_INTRO','Please enter the new file name with its related data.');

define('IMAGE_NEW','New');
define('IMAGE_INSERT','Insert');
define('IMAGE_UPDATE','Update');
define('IMAGE_EDIT','Edit');
define('IMAGE_DELETE','Delete');
define('IMAGE_CANCEL','Cancel');
define('IMAGE_CONFIRM','Cofirm delete');
define('TEXT_DISPLAY_NUMBER_OF_FILES', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> files)');
?>