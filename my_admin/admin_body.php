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
if(basename($_SERVER['PHP_SELF'])!= FILENAME_INDEX && basename($_SERVER['PHP_SELF']) != FILENAME_ADMIN1_ADMIN_FORGOT_PASSWORD) 
{ 
 tep_admin_check_login(); 
}
include_once(FILENAME_ADMIN_HEADER);
include_once(FILENAME_ADMIN_LEFT);
include_once(FILENAME_ADMIN_FOOTER);
$template->assign_vars(array('ADMIN_HEADER_HTML' => $ADMIN_HEADER_HTML,
 'LEFT_HTML' => $LEFT_HTML,
 'LEFT_BOX_WIDTH' => LEFT_BOX_WIDTH,
 'RIGHT_BOX_WIDTH' => RIGHT_BOX_WIDTH,
 'ADMIN_FOOTER_HTML' => $ADMIN_FOOTER_HTML
 ));
?>