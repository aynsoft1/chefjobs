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
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_ADMIN_FORBIDDEN);
$template->set_filenames(array('forbidden' => 'forbidden.htm'));
include_once(FILENAME_ADMIN_BODY);

$template->assign_vars(array(
 'buttons'=>'<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_INDEX) . '">' . tep_image_button(PATH_TO_BUTTON.'button_back.gif', IMAGE_BACK) . '</a>',
 'HEADING_TITLE'=>HEADING_TITLE,
 'NAVBAR_TITLE'=>NAVBAR_TITLE,
 'TEXT_MAIN'=>TEXT_MAIN,
 'RIGHT_BOX_WIDTH'=>0,
 'ADMIN_RIGHT_HTML'=>'',
 'update_message'=>$messageStack->output()));
$template->pparse('forbidden');
?>