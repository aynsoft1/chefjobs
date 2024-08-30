<?
include_once("include_files.php");
include_once(FILENAME_BODY);
ini_set('display_errors','0');
//print_r($_GET);
if(check_login("recruiter"))
{
 $query_string=$_GET['query_string'];
 $resume_id=check_data($query_string,"@@@","resume_id","resume");
 if(!$resume_check=getAnyTableWhereData(JOBSEEKER_RESUME1_TABLE,"resume_id='".$resume_id."'","resume_id"))
 {
  $messageStack->add_session(MESSAGE_RESUME_ERROR, 'error');
  tep_redirect(FILENAME_RECRUITER_LIST_OF_APPLICATIONS);
 }
}
else
{
 if(!check_login("jobseeker"))
 {
  $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
  tep_redirect(FILENAME_JOBSEEKER_LOGIN);
 }
 //$jobseeker_id=$_SESSION['sess_jobseekerid'];
 $resume_id =check_data($_GET['query_string'],"@@@","resume_id","resume");
 if(!$check1=getAnyTableWhereData(JOBSEEKER_RESUME1_TABLE,"resume_id='".$resume_id."' "))
 {
  $messageStack->add_session(MESSAGE_RESUME_NOT_EXIST,'error');
  tep_redirect(tep_href_link(FILENAME_JOBSEEKER_LIST_OF_RESUMES));
 }
}
$fields="jobseeker_resume";
$table_name=JOBSEEKER_RESUME1_TABLE;
$whereClause=" resume_id='".$resume_id."'";
//$row=getAnyTableWhereData($table_name,$whereClause,$fields);
//print_r($row);
if($row=getAnyTableWhereData($table_name,$whereClause,$fields))
{//print_r($row);
 header('Content-Type: application/force-download' );
 header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
 header('Content-Disposition: attachment; filename="'. stripslashes(stripslashes(substr($row["jobseeker_resume"],14))) . '"');
 $resume_directory=get_file_directory($row['jobseeker_resume'],6);
 readfile(PATH_TO_MAIN_PHYSICAL_RESUME.$resume_directory.'/'.$row["jobseeker_resume"]);
 //header('Pragma: no-cache');

}
else
{
 header("location:./");
 exit;
}
?>