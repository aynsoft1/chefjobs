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
define('HEADING_TITLE', 'Database backup manager');

define('TABLE_HEADING_TITLE', 'Title');
define('TABLE_HEADING_FILE_DATE', 'Date');
define('TABLE_HEADING_FILE_SIZE', 'Size');
define('TABLE_HEADING_ACTION', 'Action');

define('TEXT_INFO_HEADING_NEW_BACKUP', 'New backup');
define('TEXT_INFO_HEADING_RESTORE_LOCAL', 'Restore local');
define('TEXT_INFO_NEW_BACKUP', 'Do not interrupt the backup process which might take a couple of minutes.');
define('TEXT_INFO_UNPACK', '<br><br>(after unpacking the file from the archive)');
define('TEXT_INFO_RESTORE', 'Do not interrupt the restoration process.<br><br>The larger the backup, the longer this process takes!<br><br>If possible, use the mysql client.<br><br>For example:<br><br><b>mysql -h' . DB_SERVER . ' -u' . DB_SERVER_USERNAME . ' -p ' . DB_DATABASE . ' < %s </b> %s');
define('TEXT_INFO_RESTORE_LOCAL', 'Do not interrupt the restoration process.<br><br>The larger the backup, the longer this process takes!');
define('TEXT_INFO_RESTORE_LOCAL_RAW_FILE', 'The file uploaded must be a raw sql (text) file.');
define('TEXT_INFO_DATE', 'Date : ');
define('TEXT_INFO_SIZE', 'Size : ');
define('TEXT_INFO_COMPRESSION', 'Compression : ');
define('TEXT_INFO_USE_GZIP', 'Use GZIP');
define('TEXT_INFO_USE_ZIP', 'Use ZIP');
define('TEXT_INFO_USE_NO_COMPRESSION', 'No Compression (Pure SQL)');
define('TEXT_INFO_DOWNLOAD_ONLY', 'Download only (do not store server side)');
define('TEXT_INFO_BEST_THROUGH_HTTPS', 'Best through a HTTPS connection');
define('TEXT_DELETE_INTRO', 'Are you sure you want to delete this backup?');
define('TEXT_NO_EXTENSION', 'None');
define('TEXT_BACKUP_DIRECTORY', 'Backup directory:');
define('TEXT_LAST_RESTORATION', 'Last restoration:');
define('TEXT_FORGET', '(<u>forget</u>)');

define('ERROR_BACKUP_DIRECTORY_DOES_NOT_EXIST', 'Error: backup directory does not exist.');
define('ERROR_BACKUP_DIRECTORY_NOT_WRITEABLE', 'Error: backup directory is not writeable.');
define('ERROR_DOWNLOAD_LINK_NOT_ACCEPTABLE', 'Error: download link not acceptable.');

define('SUCCESS_LAST_RESTORE_CLEARED', 'Success: the last restoration date has been cleared.');
define('SUCCESS_DATABASE_SAVED', 'Success: the database has been saved.');
define('SUCCESS_DATABASE_RESTORED', 'Success: the database has been restored.');
define('SUCCESS_BACKUP_DELETED', 'Success: the backup has been removed.');

define('IMAGE_CANCEL', 'Cancel');
define('IMAGE_BACKUP', 'Backup');
define('IMAGE_DOWNLOAD', 'Download');
define('IMAGE_RESTORE', 'Restore');
define('IMAGE_RESTORE_LOCAL', 'Restore local');
define('IMAGE_DELETE','Delete');
define('IMAGE_CONFIRM','Confirm delete');
?>