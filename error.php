<?
include_once("include_files.php");
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ERROR);

if($messageStack->size <= 0)
{
 tep_redirect(FILENAME_INDEX);
}
$template->set_filenames(array('error' => 'error.htm'));
include_once(FILENAME_BODY);
$template->assign_vars(array(
 'HEADING_TITLE'=>HEADING_TITLE,
 'button'=>'<a href="javascript:history.back();"><button class="btn btn-primary" >Back</button></a>',
 'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
 'RIGHT_BOX_WIDTH'=>RIGHT_BOX_WIDTH1,
 'LEFT_HTML'=>LEFT_HTML,
 'RIGHT_HTML'=>RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('error');
?>