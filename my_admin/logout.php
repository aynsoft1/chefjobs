<?
/*
***********************************************************
***********************************************************
**********# Name          : Kamal Kumar Sahoo   #**********
**********# Company       : Aynsoft             #**********
**********# Date Created  : 11/02/04            #**********
**********# Date Modified : 11/02/04            #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
***********************************************************
***********************************************************
*/
include_once("../include_files.php");
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_LOGOUT);

$session_id = session_id();
@session_unset();
@session_destroy();

//go to main page
$messageStack->add_session(MESSAGE_LOGOUT, 'success');
tep_redirect(tep_href_link(PATH_TO_ADMIN));
?>