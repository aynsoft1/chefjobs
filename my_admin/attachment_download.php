<?
include_once("../include_files.php");
ini_set('display_errors','0');
$n_id=$_GET['n_id'];
if(!$row=getAnyTableWhereData(NEWSLETTERS_TABLE." as n "," n.id='".tep_db_input($n_id)."' ","n.attachment_file")) 
{ ///Hack attempt
 $messageStack->add_session("ATTACHMENT ERROR", 'error');
 tep_redirect(tep_href_link(FILENAME_ADMIN1_ADMIN_ERROR));
}
header('Content-Type: application/force-download' );
header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Content-Disposition: attachment; filename="' . stripslashes(stripslashes(substr($row["attachment_file"],14))) . '"');
readfile(PATH_TO_MAIN_PHYSICAL_EMAIL_ATTACHMENT.$row["attachment_file"]);
//header('Pragma: no-cache');
?>