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
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_JOBSEEKER_PREVIEW_COVER_LETTER);
$template->set_filenames(array('cover_letter'=>'preview_cover_letter.htm'));
if(!check_login("jobseeker"))
{
 $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
 tep_redirect(FILENAME_JOBSEEKER_LOGIN);
}
$cID = (isset($_GET['cID']) ? $_GET['cID'] : '');
//////////////////
if(tep_not_null($cID))
{
 $cover_letter_id=(int)$_GET['cID'];
 if(!$row_check=getAnyTableWhereData(COVER_LETTER_TABLE,"cover_letter_id='".tep_db_input($cover_letter_id)."' and jobseeker_id='".$_SESSION['sess_jobseekerid']."'"))
 {
  $messageStack->add_session(MESSAGE_COVER_LETTER_ERROR, 'error');
  tep_redirect(FILENAME_JOBSEEKER_LIST_OF_COVER_LETTERS);
 }
}
$template->assign_vars(array(
 'HEADING_TITLE'=>HEADING_TITLE,
 'INFO_TEXT_CLOSE'=>INFO_TEXT_CLOSE,
 'INFO_TEXT_COVER_LETTER'=>stripslashes($row_check['cover_letter']),
 'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
 'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
 'update_message'=>$update_message));
$template->pparse('cover_letter');
?>