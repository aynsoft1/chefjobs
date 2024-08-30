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
define('HEADING_TITLE', 'Currencies');

define('TABLE_HEADING_CURRENCY_NAME', 'Currency');
define('TABLE_HEADING_CURRENCY_CODES', 'Code');
define('TABLE_HEADING_CURRENCY_VALUE', 'Value');
define('TABLE_HEADING_ACTION', 'Action');

define('TEXT_INFO_CURRENCY_TITLE', 'Title : ');
define('TEXT_INFO_CURRENCY_CODE', 'Code : ');
define('TEXT_INFO_CURRENCY_SYMBOL_LEFT', 'Symbol left : ');
define('TEXT_INFO_CURRENCY_SYMBOL_RIGHT', 'Symbol right : ');
define('TEXT_INFO_CURRENCY_DECIMAL_POINT', 'Decimal point : ');
define('TEXT_INFO_CURRENCY_THOUSANDS_POINT', 'Thousands point : ');
define('TEXT_INFO_CURRENCY_DECIMAL_PLACES', 'Decimal places : ');
define('TEXT_INFO_CURRENCY_LAST_UPDATED', 'Last updated : ');
define('TEXT_INFO_CURRENCY_VALUE', 'Value : ');
define('TEXT_INFO_CURRENCY_EXAMPLE', 'Example output : ');
define('TEXT_INFO_INSERT_INTRO', 'Please enter the new currency with its related data');
define('TEXT_INFO_DELETE_INTRO', 'Are you sure you want to delete this currency?');
define('TEXT_INFO_HEADING_NEW_CURRENCY', 'New currency');
define('TEXT_INFO_HEADING_EDIT_CURRENCY', 'Edit currency');
define('TEXT_INFO_HEADING_DELETE_CURRENCY', 'Delete currency');
define('TEXT_INFO_SET_AS_DEFAULT', 'Set as default (requires a manual update of currency values)');
define('TEXT_INFO_CURRENCY_UPDATED', 'The exchange rate for %s (%s) was updated successfully via %s.');

define('ERROR_REMOVE_DEFAULT_CURRENCY', 'Error: The default currency can not be removed. Please set another currency as default, and try again.');
define('ERROR_CURRENCY_INVALID', 'Error: The exchange rate for %s (%s) was not updated via %s. Is it a valid currency code?');
define('WARNING_PRIMARY_SERVER_FAILED', 'Warning: The primary exchange rate server (%s) failed for %s (%s) - trying the secondary exchange rate server.');

define('IMAGE_NEW_CURRENCY','Add new currency');
define('IMAGE_CANCEL','Cancel');
define('IMAGE_INSERT','Insert currency');
define('IMAGE_EDIT','Edit currency');
define('IMAGE_UPDATE','Update currency');
define('IMAGE_DELETE','Delete currency');
define('IMAGE_CONFIRM','Confirm delete currency');
define('IMAGE_UPDATE_CURRENCIES','Update currency');
define('TEXT_DISPLAY_NUMBER_OF_CURRENCY', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> currencies)');
?>