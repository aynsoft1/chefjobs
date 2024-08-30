<?
include_once("include_files.php");
include_once(FILENAME_BODY);
ini_set('display_errors','0');
if(!check_login("recruiter"))
{
 $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
 tep_redirect(FILENAME_RECRUITER_LOGIN);
}
$query_string=$_GET['query_string'];
$resume_id=check_data($query_string,"=","resume_id","resume_id");
if(!$row=getAnyTableWhereData(APPLY_TABLE." as a, ".JOB_TABLE." as j","a.id='".tep_db_input($resume_id)."' and a.job_id=j.job_id and j.recruiter_id='".$_SESSION['sess_recruiterid']."'","a.resume_name")) 
{ ///Hack attempt
 $messageStack->add_session(MESSAGE_RESUME_ERROR, 'error');
 tep_redirect(tep_href_link(FILENAME_ERROR));
}
header('Content-Type: application/force-download' );
header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Content-Disposition: attachment; filename="' . stripslashes(stripslashes(substr($row["resume_name"],14))) . '"');
readfile(PATH_TO_MAIN_PHYSICAL_APPLY_RESUME.$row["resume_name"]);
//header('Pragma: no-cache');
?>