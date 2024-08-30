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
$heading[] = array('text'  =>BOX_REPORTS,
                   'link'  =>FILENAME_ADMIN1_GENERAL_REPORTS.'?selected_box=reports',
                   'default_row'=>(($_SESSION['selected_box'] == 'reports') ?'1':''),
                   'text_image'=>'<ion-icon name="bar-chart-outline" style="color: #000000;margin: 0px 5px 0 10px;font-size: 22px;position: absolute;"></ion-icon>',

                  );

$box = new left_box;
$LEFT_HTML.=$box->menuBox($heading, $contents);
?>