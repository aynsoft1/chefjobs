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

if($action=='add_banner' || $action=='edit_banner')
 define('HEADING_TITLE','Banner Management');
else
 define('HEADING_TITLE','Banner Edition');
define('INFO_TEXT_BANNER_TITLE','Banner Title : ');
define('INFO_TEXT_BANNER_SRC','Banner SRC : ');
define('INFO_TEXT_BANNER_SIZE','Banner Size(width x Height) : ');
define('INFO_TEXT_BANNER_HREF','Banner Link/ HREF : ');

define('INFO_TEXT_BANNER_ALT','Banner ALT Text : ');

define('INFO_TEXT_BANNER_LOCATION','Banner Location :');
define('INFO_TEXT_BANNER_DURATION','Banner Duration :');
define('INFO_TEXT_BANNER_COSTING','Costing :');

define('INFO_TEXT_HITS','Hits : ');
define('INFO_TEXT_CLICKS','Clicks : ');
define('INFO_TEXT_STATUS','Status : ');

define('INFO_TEXT_COMPANY_NAME','Name : ');
define('INFO_TEXT_COMPANY_CONTACT','Contact No : ');
define('INFO_TEXT_COMPANY_EMAIL','Email : ');
define('INFO_TEXT_COMPANY_COMMENTS','Comments : ');

define('INFO_TEXT_ADD_BANNER','add banner');
define('INFO_TEXT_EDIT_BANNER','edit banner');

define('TABLE_HEADING_BANNER_NAME','Banner');
define('TABLE_HEADING_BANNER_COMPANYNAME','Company');
define('TABLE_HEADING_BANNER_DURATION','Duration');
define('TABLE_HEADING_BANNER_DATE','Date of Insertion');
define('TABLE_HEADING_STATUS','Status');
define('INFO_TEXT_BANNER_TYPE','Banner Type: ');
define('INFO_TEXT_BANNER_SCRIPT','Banner Script : ');
define('INFO_TEXT_BANNER','Banner Image: ');
define('TABLE_HEADING_ACTION', 'Action');

define('TEXT_INFO_HEADING_NEW_BANNER','New Banner');

define('MESSAGE_SUCCESS_DELETED','Success: Banner successfully deleted.');
define('MESSAGE_SUCCESS_RESTORED','Success: Banner successfully restored.');
define('MESSAGE_SUCCESS_INSERTED','Success: Banner successfully inserted.');
define('MESSAGE_SUCCESS_UPDATED','Success: Banner successfully updated.');
define('MESSAGE_NAME_ERROR','Error : Sorry, this banner already exists.');
define('MESSAGE_ERROR_NOT_EXIST','Error : Sorry, this banner does not exist.');
define('TEXT_DELETE_INTRO', 'Are you sure you want to delete this banner?');
define('TEXT_INFO_NEW_INTRO','Please enter the new banner title with its related data.');

define('MESSAGE_FROM_DATE_ERROR','From date is not in required format.');
define('MESSAGE_TO_DATE_ERROR','To date is not in required format.');
define('MESSAGE_DATE_ERROR','from date does not grater than to date');


define('IMAGE_NEW','New');
define('IMAGE_INSERT','Insert');
define('IMAGE_UPDATE','Update');
define('IMAGE_EDIT','Edit');
define('IMAGE_DELETE','Delete');
define('IMAGE_CANCEL','Cancel');
define('IMAGE_PURGE','Permanently delete');
define('IMAGE_CONFIRM','Cofirm');
define('IMAGE_RESTORE','Restore');
define('TEXT_PURGE_INTRO','Are you sure you want to delete banner permanantly.');
define('TEXT_DISPLAY_NUMBER_OF_BANNERS', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> banners)');

define('UPLOAD_ERROR','please Upload a file.');
define('SCRIPT_ERROR','Please enter a script.');
define('ALT_ERROR','Please enter Alt.');
define('HREF_ERROR','Please enter Href.');
define('BANNER_TYPE_ERROR','Please enter banner Type.');

?>