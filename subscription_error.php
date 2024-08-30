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
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_SUBSCRIPTION_ERROR);
$template->set_filenames(array('subscription_error' => 'subscription_error.htm'));
include_once(FILENAME_BODY);
if($messageStack->size <= 0)
{
 tep_redirect(tep_href_link(FILENAME_RECRUITER_RATES));
}

$template->assign_vars(array(
 'HEADING_TITLE'=>HEADING_TITLE,
 'button'=>'&nbsp;<br><br><a href="javascript:history.back();">' . tep_image_button(PATH_TO_BUTTON.'button_back.gif', IMAGE_BACK) . '</a><br>&nbsp;',
 'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
 'RIGHT_BOX_WIDTH'=>RIGHT_BOX_WIDTH1,
 'LEFT_HTML'=>LEFT_HTML,
 'RIGHT_HTML'=>RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('subscription_error');
?>