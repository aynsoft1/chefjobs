<?
/*
***********************************************************
***********************************************************
**********# Name          : Shambhu Prasad Patnaik#********
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
***********************************************************
***********************************************************
*/
include_once("include_files.php");
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_RECRUITER_PRIVATE_NOTES);
$template->set_filenames(array('resume_private_notes' => 'resume_private_notes.htm'));
include_once(FILENAME_BODY);

if(!check_login("recruiter"))
{
	$_SESSION['REDIRECT_URL']=$_SERVER['REQUEST_URI'];
 $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
 tep_redirect(FILENAME_RECRUITER_LOGIN);
}

$query_string=$_GET['query_string'];
$resume_id=check_data($query_string,"==","search_id","search");

$row_notes=getAnyTableWhereData(JOBSEEKER_RATING_TABLE," recruiter_id='".$_SESSION['sess_recruiterid']."' and resume_id='".(int)$resume_id."'",'private_notes');
$private_notes=tep_db_output($row_notes['private_notes']);
if(!tep_not_null($private_notes))
$private_notes="Not Available";

$template->assign_vars(array(
 'HEADING_TITLE'    => HEADING_TITLE,
 'INFO_TEXT_NOTES'    =>$private_notes,
 'update_message'=>$messageStack->output()));
$template->pparse('resume_private_notes');
?>