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
$heading[] = array('text'  => BOX_HEADING_THEMES,
				'link'  => FILENAME_ADMIN1_THEMES."?selected_box=themes",
                   'default_row'=>(($_SESSION['selected_box'] == 'themes') ?'1':''),
                   'text_image'=>'<ion-icon name="color-palette-outline" style="color: #000000;margin: 0px 5px 0 10px;font-size: 22px;position: absolute;"></ion-icon>',
                   );

if ($_SESSION['selected_box'] == 'themes')
{
 $row_logo=getAnyTableWhereData(CONFIGURATION_TABLE,"configuration_name='DEFAULT_SITE_LOGO'","id");
 $blank_space='<i class="far fa-circle" style="margin: 3px 5px 3px 10px;font-size: 10px;color:#fff;"></i>';
 $content=tep_admin_files_boxes(FILENAME_ADMIN1_MODULE,BOX_THEMES_LOGO,'id='.$row_logo['id'].'&action=edit');
 if(tep_not_null($content))
 {
	 $contents[] = array('text'=>$blank_space.$content);
 }
 $blank_space='<i class="far fa-circle" style="margin: 3px 5px 3px 10px;font-size: 10px;color:#fff;"></i>';
 $content=tep_admin_files_boxes(FILENAME_ADMIN1_MENU_MANAGEMENT,BOX_THEMES_MENU);
 if(tep_not_null($content) )
 {
	 $contents[] = array('text'=>$blank_space.$content);
 }
 $blank_space='<i class="far fa-circle" style="margin: 3px 5px 3px 10px;font-size: 10px;color:#fff;"></i>';
 $content=tep_admin_files_boxes(FILENAME_ADMIN1_LIST_OF_SLIDERS,BOX_THEMES_SLIDER);
 if(tep_not_null($content))
 {
	 $contents[] = array('text'=>$blank_space.$content);
 }
 $blank_space='<i class="far fa-circle" style="margin: 3px 5px 3px 10px;font-size: 10px;color:#fff;"></i>';
 $content=tep_admin_files_boxes(FILENAME_ADMIN1_THEMES, BOX_THEMES_THEMES);
 if(tep_not_null($content))
 {
	 $contents[] = array('text'=>$blank_space.$content);
 }
 $content=tep_admin_files_boxes(FILENAME_ADMIN1_THEME_EDITOR, BOX_THEMES_THEME_EDITOR);
 if(tep_not_null($content))
 {
	 $contents[] = array('text'=>$blank_space.$content);
 }
 $content=tep_admin_files_boxes(FILENAME_ADMIN1_MODULE, BOX_THEMES_THEME_MODULE);
 if(tep_not_null($content))
 {
	 $contents[] = array('text'=>$blank_space.$content);
 }
}
$box = new left_box;
$LEFT_HTML.=$box->menuBox($heading, $contents);
?>