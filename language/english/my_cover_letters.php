<?
/**********************************************************
**********# Name          : Shambhu Prasad Patnaik  #**********
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
**********************************************************/

$action = (isset($_GET['action']) ? $_GET['action'] : '');
if($action=='add_new')
 define('HEADING_TITLE', 'Add cover letter');
else if(tep_not_null($_GET['cID']))
{
 define('HEADING_TITLE', 'Edit cover letter');
}
else
 define('HEADING_TITLE', 'List of cover letters');
//////////////////////////
define('TABLE_HEADING_COVER_LETTER_NAME', 'Name');
define('TABLE_HEADING_COVER_LETTER_VALUE', 'Value');
define('TABLE_HEADING_INSERTED', 'Inserted');
define('TABLE_HEADING_UPDATED', 'Updated');
define('TABLE_HEADING_EDIT', 'Edit');
define('TABLE_HEADING_DELETE', 'Delete');
define('TABLE_HEADING_VIEW', 'View');
define('TABLE_HEADING_DUPLICATE', 'Duplicate');

define('INFO_TEXT_COVER_LETTER_NAME', 'Name : ');
define('INFO_TEXT_COVER_LETTER_NAME_ERROR', 'Please enter cover letter name.');

define('INFO_TEXT_COVER_LETTER_DESCRIPTION', 'Description : ');
define('INFO_TEXT_COVER_LETTER_DESCRIPTION_ERROR', 'Please enter cover letter description.');
define('SAME_COVER_LETTER_NAME_ERROR', 'Sorry, This name already exists.');

define('INFO_TEXT_MAX_COVERLETTER', 'Note : You can create up to %d cover letters.');
define('ERROR_EXCEED_MAX_NO_COVERLETTER','Error: Sorry, you have already created <b>%d</b> cover letters and this is the maximum number of cover letters that a jobseeker can created.');

define('MESSAGE_SUCCESS_SAVED','Success: Cover letter successfully Saved.');
define('MESSAGE_SUCCESS_UPDATED','Success: Cover letter successfully updated.');
define('MESSAGE_SUCCESS_DELETED','Success: Cover letter successfully deleted.');
define('MESSAGE_SUCCESS_DUPLICATED','Success: Cover letter successfully duplicated.');

define('MESSAGE_COVER_LETTER_ERROR','Sorry this cover letter doesnot exist. If problem persists please contact admin of the site.');
define('IMAGE_UPDATE','Update');
define('IMAGE_SAVE','Save');
define('IMAGE_CANCEL','Cancel');
define('INFO_TEXT_ADD_COVER_LETTER','Add cover letter');
?>