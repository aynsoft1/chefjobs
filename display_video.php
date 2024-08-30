<?
/*
************************************************************
************************************************************
**********#	Name				      : Shambhu Prasad Patnaik#*********
**********#	Company			    : Aynsoft	Pvt. Ltd.   #***********
**********#	Copyright (c) www.aynsoft.com 2004	 #***********
************************************************************
************************************************************
*/
include_once("include_files.php");
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_DISPLAY_VIDEO);
$template->set_filenames(array('display_video' => 'display_video.htm'));
if(tep_not_null($_GET['query_string1']))
{
 $query_string1=tep_db_prepare_input($_GET['query_string1']);
 $resume_id= check_data($query_string1,"===","video_dispaly","videoid");
 if($checked=getAnyTableWhereData(JOBSEEKER_RESUME1_TABLE," resume_id='".$resume_id."'","jobseeker_video"))
 {
   include_once("class/video.php");
   $video_link=stripslashes($checked['jobseeker_video']);
   $search = array ("'[\s]+'");
   $replace = array ("");
   $video_link = preg_replace($search,$replace, $video_link);
   $VideoportalPlayer = new VideoportalPlayer;
   $content = $VideoportalPlayer->getYoutube('@'.$video_link);
   $template->assign_vars(array(
    'DISPLAY_VIDEO'=>$content,
	'VIDEO_TITLE'=>SITE_TITLE,
    'update_message'=>$messageStack->output()));
   $template->pparse('display_video');

 }
 else
 {
  die(VIDEO_NOT_EXIST_MESSAGE);
 }
}
else
{
 $messageStack->add_session(PROFILE_NOT_EXIST_MESSAGE, 'error');
 tep_redirect($_SERVER['HTTP_REFERER']);
}
?>

