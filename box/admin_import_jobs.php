<?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2009  #**********
***********************************************************
*/
$heading = array();
$contents = array();
$heading[] = array('text'  =>BOX_HEADING_IMPORT_JOBS,
                   'link'  =>FILENAME_ADMIN1_INDEED_FEED_IMPORT.'?selected_box=import_jobs',
                   'default_row'=>(($_SESSION['selected_box'] == 'import_jobs') ?'1':''),
                   'text_image'=>'<ion-icon name="archive-outline" style="color: #000000;margin: 0px 5px 0 10px;font-size: 22px;position: absolute;"></ion-icon>',
                   );
if ($_SESSION['selected_box'] == 'import_jobs')
{
 $blank_space='<i class="far fa-circle" style="margin: 3px 5px 3px 10px;font-size: 10px;color:#fff;"></i>';
 $content=tep_admin_files_boxes(FILENAME_ADMIN1_INDEED_FEED_IMPORT, BOX_IMPORT_JOBS_INDEED);
 if(tep_not_null($content))
 {
	 $contents[] = array('text'=>$blank_space.$content);
 }
 $content=tep_admin_files_boxes(FILENAME_ADMIN1_ZIP_RECRUITER_FEED_IMPORT, BOX_IMPORT_JOBS_ZIP_RECRUITER);
 if(tep_not_null($content))
 {
	 $contents[] = array('text'=>$blank_space.$content);
 }
 $content=tep_admin_files_boxes(FILENAME_ADMIN1_USAJOBS_FEED_IMPORT, BOX_IMPORT_JOBS_USAJOBS);
 if(tep_not_null($content))
 {
	 $contents[] = array('text'=>$blank_space.$content);
 }
 $content=tep_admin_files_boxes(FILENAME_ADMIN1_IMPORT_JOBS, BOX_IMPORT_CSV_JOBS);
 if(tep_not_null($content))
 {
	 $contents[] = array('text'=>$blank_space.$content);
 }
 $content=tep_admin_files_boxes(FILENAME_ADMIN1_IMPORT_XML_JOBS, BOX_IMPORT_XML_JOBS);
 if(tep_not_null($content))
 {
	 $contents[] = array('text'=>$blank_space.$content);
 }
}
$box = new left_box;
$LEFT_HTML.=$box->menuBox($heading, $contents);

?>