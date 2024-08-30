<?
/*
***********************************************************
***********************************************************
**********# Name          : Kamal Kumar Sahoo   #**********
**********# Company       : Aynsoft Pvt. Ltd.   #**********
**********# Date Created  : 07/09/2005          #**********
**********# Date Modified : 07/09/2005          #**********
**********# Copyright (c) www.aynsoft.com 2005  #**********
***********************************************************
***********************************************************
*/
include_once("../include_files.php");
$xx_mins_ago = (time() - LOGOUT_TIME);
// remove entries that have expired
tep_db_query("delete from " . WHOS_ONLINE_TABLE . " where time_last_click < '" . $xx_mins_ago . "'");
?>