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
$heading[] = array('text'  => BOX_ORDERS,
                   'link'  =>FILENAME_ADMIN1_RECRUITER_ORDERS.'?selected_box=orders',
                   'default_row'=>(($_SESSION['selected_box'] == 'orders') ?'1':''),
                   'text_image'=>'<ion-icon name="card-outline" style="color: #000000;margin: 0px 5px 0 10px;font-size: 22px;position: absolute;"></ion-icon>',
                   );

if ($_SESSION['selected_box'] == 'orders')
{
 $blank_space='<i class="far fa-circle" style="margin: 3px 5px 3px 10px;font-size: 10px;color:#fff;"></i>';
 $content=tep_admin_files_boxes(FILENAME_ADMIN1_JOBSEEKER_ORDERS, BOX_JOBSEEKER_ORDERS);
 if(tep_not_null($content))
 {
	 $contents[] = array('text'=>$blank_space.$content);
 }
 $content=tep_admin_files_boxes(FILENAME_ADMIN1_RECRUITER_ORDERS, BOX_RECRUITER_ORDERS);
 if(tep_not_null($content))
 {
	 $contents[] = array('text'=>$blank_space.$content);
 }
 $content=tep_admin_files_boxes(FILENAME_ADMIN1_RECRUITER_INVOICES, BOX_RECRUITER_INVOICES);
 if(tep_not_null($content))
 {
	 $contents[] = array('text'=>$blank_space.$content);
 }

}
$box = new left_box;
$LEFT_HTML.=$box->menuBox($heading, $contents);
?>