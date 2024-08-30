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

 define('HEADING_TITLE','List of Banners');



define('INFO_TEXT_BANNER_TITLE','Banner Title : ');

define('INFO_TEXT_BANNER_SRC','Banner SRC : ');

define('INFO_TEXT_BANNER_HREF','Banner Link/ HREF : ');

define('INFO_TEXT_BANNER_ALT','Banner ALT Text : ');

define('INFO_TEXT_BANNER_LOCATION','Banner Location :');


define('INFO_TEXT_ADVIEWS','AdViews : ');

define('INFO_TEXT_ADCLICKS','AdClicks : ');

define('INFO_TEXT_STATUS','Status : ');

define('INFO_TEXT_ORDER','Order : ');



define('INFO_TEXT_ADD_BANNER','add banner');

define('INFO_TEXT_EDIT_BANNER','edit banner');



define('TABLE_HEADING_BANNER_NAME','Banner');

define('TABLE_HEADING_ADVIEWS','AdViews');

define('TABLE_HEADING_ADCLICKS','AdClicks');
define('TABLE_HEADING_CTR','*CTR');

define('TABLE_HEADING_STATUS','Status');

define('TABLE_HEADING_ACTION', 'Action');



define('TEXT_INFO_HEADING_NEW_BANNER','New Banner');



define('MESSAGE_SUCCESS_DELETED','Success: File name successfully deleted.');

define('MESSAGE_SUCCESS_INSERTED','Success: File name successfully inserted.');

define('MESSAGE_SUCCESS_UPDATED','Success: File name successfully updated.');



define('MESSAGE_NAME_ERROR','Error : Sorry, this file name already exists.');

define('MESSAGE_ERROR_NOT_EXIST','Error : Sorry, this file name does not exist.');



define('TEXT_DELETE_INTRO', 'Are you sure you want to delete this file name?');

define('TEXT_INFO_NEW_INTRO','Please enter the new banner title with its related data.');



define('IMAGE_NEW','Add new file');

define('IMAGE_INSERT','Insert');

define('IMAGE_UPDATE','Update');

define('IMAGE_EDIT','Edit');

define('IMAGE_DELETE','Delete');

define('IMAGE_CANCEL','Cancel');

define('IMAGE_CONFIRM','Cofirm delete');

define('TEXT_DISPLAY_NUMBER_OF_BANNERS', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> banners)');

?>