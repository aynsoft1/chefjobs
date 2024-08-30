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
$heading[] = array('link'  =>FILENAME_ADMIN1_JOBALERTS.'?selected_box=job_alerts',
                   'text'  =>BOX_HEADING_JOBALERTS,
                   'default_row'=>(($_SESSION['selected_box'] == 'jobalerts') ?'1':''),
                   'text_image'=>'<ion-icon name="notifications-outline" style="color: #000000;margin: 0px 5px 0 10px;font-size: 22px;position: absolute;"></ion-icon>',
                  );

if ($_SESSION['selected_box'] == 'job_alerts')
{
 $blank_space='<i class="far fa-circle" style="margin: 3px 5px 3px 10px;font-size: 10px;color:#fff;"></i>';
 $content=tep_admin_files_boxes(FILENAME_ADMIN1_JOBALERTS, BOX_JOBALERTS);
 if(tep_not_null($content))
 {
	 $contents[] = array('text'=>$blank_space.$content);
 }
}
$box = new left_box;
$LEFT_HTML.=$box->menuBox($heading, $contents);
?>