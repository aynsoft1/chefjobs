 <?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik  #**********
**********# Company       : Aynsoft               #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
***********************************************************
*/
include_once("include_files.php");
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_JOBSEEKER_VIDEO);
$template->set_filenames(array('jobseeker_video' => 'jobseeker_video.htm'));
include_once(FILENAME_BODY);
if(!check_login("jobseeker"))
{
 $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
 tep_redirect(FILENAME_JOBSEEKER_LOGIN);
}

$whereClause="jobseeker_id='".$_SESSION['sess_jobseekerid']."'";
if($check=getAnyTableWhereData(JOBSEEKER_RESUME1_TABLE,$whereClause,"jobseeker_video"))
  {
  $jobseeker_video_link=$check['jobseeker_video'];
  $photo_arr=(explode("watch?v=",(basename($jobseeker_video_link))));
  $photo       = 'http://img.youtube.com/vi/'.trim($photo_arr[1]).'/2.jpg';
  $video="<img style='border:2  solid #a0a0a0;' src='".$photo."' alt='' >";

  }

 $template->assign_vars(array(
 'HEADING_TITLE'=> HEADING_TITLE,
 'video_image'=>$video,
 'update_message'=>$messageStack->output()));
 $template->pparse('jobseeker_video');
?>