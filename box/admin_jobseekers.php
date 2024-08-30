<?
/*
***********************************************************
***********************************************************
**********# Name          : Kamal Kumar Sahoo   #**********
**********# Company       : Aynsoft             #**********
**********# Date Created  : 11/02/04            #**********
**********# Date Modified : 11/02/04            #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
***********************************************************
***********************************************************
*/
$heading = array();
$contents = array();
$heading[] = array('link'  =>FILENAME_ADMIN1_JOBSEEKERS.'?selected_box=jobseekers',
                   'text'  =>BOX_HEADING_JOBSEEKERS,
                   'default_row'=>(($_SESSION['selected_box'] == 'jobseekers') ?'1':''),
                   'text_image'=>'<ion-icon name="people-circle-outline" style="color: #000000;margin: 0px 5px 0 10px;font-size: 22px;position: absolute;"></ion-icon>',
                  );

if ($_SESSION['selected_box'] == 'jobseekers')
{
 $blank_space='<i class="far fa-circle" style="margin: 3px 5px 3px 10px;font-size: 10px;color:#fff;"></i>';
 $content=tep_admin_files_boxes(FILENAME_ADMIN1_JOBSEEKERS, BOX_JOBSEEKER);
 if(tep_not_null($content))
 {
	 $contents[] = array('text'=>$blank_space.$content);
 }
 $content=tep_admin_files_boxes(FILENAME_ADMIN1_PAID_JOBSEEKERS, BOX_PAID_JOBSEEKERS);
 if(tep_not_null($content))
 {
	 $contents[] = array('text'=>$blank_space.$content);
 }
 $content=tep_admin_files_boxes(FILENAME_ADMIN1_IMPORT_JOBSEEKER, BOX_IMPORT_JOBSEEKERS);
 if(tep_not_null($content))
 {
	 $contents[] = array('text'=>$blank_space.$content);
 }
}
$box = new left_box;
$LEFT_HTML.=$box->menuBox($heading, $contents);
?>