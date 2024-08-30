<?
include_once("../include_files.php");
if($messageStack->size <= 0)
{
 tep_redirect(FILENAME_INDEX);
}
$template->set_filenames(array('error' => 'error.htm'));
include_once(FILENAME_ADMIN_BODY);
$template->assign_vars(array(
 'HEADING_TITLE'=>"Error !!!",
 'update_message'=>$messageStack->output()));
$template->pparse('error');
?>