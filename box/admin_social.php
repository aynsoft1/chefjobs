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
$heading[] = array('text'  =>BOX_HEADING_SOCIAL,
                   'link'  => FILENAME_ADMIN1_LINKEDIN_PLUGIN.'?selected_box=social',
                   'default_row'=>(($_SESSION['selected_box'] == 'social') ?'1':''),
                   'text_image'=>'<ion-icon name="share-social-outline" style="color: #000000;margin: 0px 5px 0 10px;font-size: 22px;position: absolute;"></ion-icon>',
                  );
if ($_SESSION['selected_box'] == 'social')
{
 $blank_space='<i class="far fa-circle" style="margin: 3px 5px 3px 10px;font-size: 10px;color:#fff;"></i>';
  $content=tep_admin_files_boxes(FILENAME_ADMIN1_LINKEDIN_PLUGIN, BOX_SETTING_LINKEDIN_PLUGIN);
 if(tep_not_null($content))
 {
	 $contents[] = array('text'=>$blank_space.$content);
 }
 $content=tep_admin_files_boxes(FILENAME_ADMIN1_GOOGLE_PLUGIN, BOX_SETTING_GOOGLE_PLUGIN);
 if(tep_not_null($content))
 {
	 $contents[] = array('text'=>$blank_space.$content);
 }
 $content=tep_admin_files_boxes(FILENAME_ADMIN1_FACEBOOK_PLUGIN, BOX_SETTING_FACEBOOK_PLUGIN);
 if(tep_not_null($content))
 {
	 $contents[] = array('text'=>$blank_space.$content);
 }
 	$content=tep_admin_files_boxes(FILENAME_ADMIN1_TWITTER_TOOLS,BOX_SEO_TWITTER_SUBMITTER);
 if(tep_not_null($content))
 {
	 $contents[] = array('text'=>$blank_space.$content);
 }
 	$content=tep_admin_files_boxes(FILENAME_ADMIN1_SOCIAL_FOOTER_LINKS,BOX_SOCIAL_FOOTER_LINKS);
 if(tep_not_null($content))
 {
	 $contents[] = array('text'=>$blank_space.$content);
 }

}
$box = new left_box;
$LEFT_HTML.=$box->menuBox($heading, $contents);
?>