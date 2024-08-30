<?
include_once("include_files.php");
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_TERMS);
$template->set_filenames(array('terms' => 'terms.htm'));
include_once(FILENAME_BODY);
$template->assign_vars(array(
 'HEADING_TITLE'=>HEADING_TITLE,
 'PARAGRAPH'=>PARAGRAPH,


 'INFO_TEXT_CONTENT'=>INFO_TEXT_CONTENT,
 'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
 'RIGHT_BOX_WIDTH'=>RIGHT_BOX_WIDTH1,
 'LEFT_HTML'=>LEFT_HTML,
 'RIGHT_HTML'=>RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('terms');
?>