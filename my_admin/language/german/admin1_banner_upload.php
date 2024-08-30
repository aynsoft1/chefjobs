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

 define('HEADING_TITLE','Upload Banner');



define('INFO_TEXT_BANNER','Banner : ');

define('INFO_TEXT_BANNER_TITLE','Banner Title: ');
define('INFO_TEXT_BANNER_HREF','Banner Link/ HREF : ');
define('INFO_TEXT_BANNER_ALT','Banner ALT Text : ');
define('INFO_TEXT_BANNER_TYPE','Banner Type: ');
define('INFO_TEXT_BANNER_SCRIPT','Banner Script : ');
define('INFO_TEXT_BANNER_LOCATION','Banner Location :');
define('INFO_TEXT_COMPANY_NAME','Name: ');
define('INFO_TEXT_COMPANY_CONTACT','Contact No: ');
define('INFO_TEXT_COMPANY_EMAIL','Email: ');
define('INFO_TEXT_COMPANY_COMMENTS','Comments: ');
define('INFO_TEXT_BANNER_DURATION','Banner Duration:');
define('INFO_TEXT_BANNER_COSTING','Costing: ');
define('INFO_TEXT_ADD_BANNER','add banner');

define('INFO_TEXT_EDIT_BANNER','edit banner');



define('TABLE_HEADING_BANNER','Banner');
define('TABLE_HEADING_BANNER_COMPANYNAME','Company');
define('TABLE_HEADING_BANNER_DURATION','Duration');
define('TABLE_HEADING_BANNER_DATE','Date of Insertion');
define('TABLE_HEADING_VIEW','View');

define('TABLE_HEADING_ACTION','Action');



define('TEXT_INFO_HEADING_NEW_BANNER','New Banner');



define('MESSAGE_SUCCESS_DELETED','Success: File name successfully deleted.');

define('MESSAGE_SUCCESS_INSERTED','Success: File name successfully inserted.');

define('MESSAGE_SUCCESS_UPDATED','Success: File name successfully updated.');



define('MESSAGE_NAME_ERROR','Error : Sorry, this banner title already exists.');

define('MESSAGE_ERROR_NOT_EXIST','Error : Sorry, this file name does not exist.');

define('MESSAGE_FROM_DATE_ERROR','From date is not in required format.');
define('MESSAGE_TO_DATE_ERROR','To date is not in required format.');
define('MESSAGE_DATE_ERROR','from date does not grater than to date');


define('TEXT_DELETE_INTRO', 'Are you sure you want to delete this file name?');

define('TEXT_INFO_UPLOAD_INTRO','Please upload new banner.');
define('TEXT_COMPANY_INFO','Company Information');


define('IMAGE_NEW','New');

define('IMAGE_UPLOAD','Upload');

define('IMAGE_UPDATE','Update');

define('IMAGE_EDIT','Edit');

define('IMAGE_DELETE','Delete');

define('IMAGE_CANCEL','Cancel');

define('IMAGE_CONFIRM','Cofirm');

define('TEXT_DISPLAY_NUMBER_OF_BANNERS', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> banners)');

define('UPLOAD_ERROR','please Upload Image.');
define('SCRIPT_ERROR','Please enter a script.');
define('ALT_ERROR','Please enter Alt.');
define('HREF_ERROR','Please enter Href.');
define('BANNER_TYPE_ERROR','Please enter banner Type.');


?>