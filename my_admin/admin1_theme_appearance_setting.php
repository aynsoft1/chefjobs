<?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2011  #**********
***********************************************************
*/
include_once("../include_files.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_THEME_APPEARANCE_SETTING);
$template->set_filenames(array('themes' => 'admin1_theme_appearance_setting.htm'));
include_once(FILENAME_ADMIN_BODY);
$appearance=tep_db_prepare_input($_GET['appearance']);
if(function_exists('cms_theme_appearance_admin_init'))
$content=cms_theme_appearance_admin_init($appearance);
$template->assign_vars(array(
 'HEADING_TITLE'           => HEADING_TITLE,
 'INFO_TEXT_PAGE_CONTENT'  => $content,
 'update_message'=>$messageStack->output()));
$template->pparse('themes');
?>