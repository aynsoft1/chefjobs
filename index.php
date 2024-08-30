<?
include_once("include_files.php");
include_once(FILENAME_BODY);
include_once(PATH_TO_THEMES.MODULE_THEME_DEFAULT_THEME."/home.php");
$template->set_filenames(array('index' => 'text.htm'));
$template->assign_vars(array(
 'update_message'=>$messageStack->output()));
$template->pparse('index');

?>