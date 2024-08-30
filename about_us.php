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
include_once("include_files.php");
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ABOUT_US);
$template->set_filenames(array('about_us' => 'about_us.htm'));
include_once(FILENAME_BODY);
$template->assign_vars(array(
 'HEADING_TITLE'    => HEADING_TITLE,
 'ABOUT_IMG'		=>'<img class="img-fluid" src="'.HOST_NAME.PATH_TO_IMG.'about_us.jpg" alt="About Us">',
 //'INFO_TEXT_MAIN'   => INFO_TEXT_MAIN,
 'LEFT_BOX_WIDTH'   => LEFT_BOX_WIDTH1,
 'RIGHT_BOX_WIDTH'  => RIGHT_BOX_WIDTH1,
 'LEFT_HTML'        => LEFT_HTML,
 'RIGHT_HTML'       => RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('about_us');
?>