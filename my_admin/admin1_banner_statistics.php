<?
/*
***********************************************************
***********************************************************
**********# Name          : Hema Chawla         #**********
**********# Company       : Aynsoft             #**********
**********# Date Created  : 23/07/05            #**********
**********# Date Modified : 23/07/05            #**********
**********# Copyright (c) www.aynsoft.com 2005  #**********
***********************************************************
***********************************************************
*/

include_once("../include_files.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_BANNER_STATISTICS);
$template->set_filenames(array('bannerstats' => 'admin1_banner_statistics.htm'));
include_once(FILENAME_ADMIN_BODY);

$action = (isset($_GET['action']) ? $_GET['action'] : '');
$_GET['b_status'] = (in_array($_GET['b_status'],array('active','expired','deleted','other')) ? $_GET['b_status'] : 'active');
$today=date("Y-m-d",mktime(date("m"), date("d"), date("Y")));
switch($_GET['b_status'])
{
 case 'active':
  $whereClause=" (duration_unlimited='T' and deleted='F')|| (duration_unlimited='F' && deleted='F' && banner_duration_from <= '".$today."' and banner_duration_to >= '".$today."')";
  break;
 case 'expired':
  $whereClause=" duration_unlimited='F' && banner_duration_from < '".$today."' && banner_duration_to < '".$today."' and deleted='F'";
  break;
 case 'deleted':
  $whereClause=" deleted = 'T'";
  break;
 case 'other':
  $whereClause=" duration_unlimited='F' && banner_duration_from > '".$today."' and deleted='F'";
  break;
}


$different_banners='<a href="'.tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_BANNER_STATISTICS, 'page=1&'.tep_get_all_get_params(array('action','page','bID','b_status','sort','selected_box'))).'&b_status=active">Active Banners</a>';
$different_banners.='&nbsp;|&nbsp;<a href="'.tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_BANNER_STATISTICS, 'page=1&'.tep_get_all_get_params(array('action','page','bID','b_status','sort','selected_box'))).'&b_status=expired">Expired Banners</a>';
$different_banners.='&nbsp;|&nbsp;<a href="'.tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_BANNER_STATISTICS, 'page=1&'.tep_get_all_get_params(array('action','page','bID','b_status','sort','selected_box'))).'&b_status=deleted">Deleted Banners</a>';
$different_banners.='&nbsp;|&nbsp;<a href="'.tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_BANNER_STATISTICS, 'page=1&'.tep_get_all_get_params(array('action','page','bID','b_status','sort','selected_box'))).'&b_status=other">Other Banners</a>';


$sort_array=array("bn_loc.banner_location_name","bn.title","bn.banner_date");
include_once(PATH_TO_MAIN_PHYSICAL_CLASS.'sort_by_clause.php');
$obj_sort_by_clause=new sort_by_clause($sort_array,'bn_loc.banner_location_name asc, bn.title asc, bn.banner_date desc');
$order_by_clause=$obj_sort_by_clause->return_value;
///////////// Middle Values 
$banner_query_raw="select * from " . BANNER_TABLE ." as bn LEFT JOIN ".BANNER_LOCATION_TABLE ." as bn_loc on (bn.location=bn_loc.banner_location_id) where ".$whereClause." order by ".$order_by_clause;
$banner_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $banner_query_raw, $banner_query_numrows);
$banner_query = tep_db_query($banner_query_raw);
if(tep_db_num_rows($banner_query) > 0)
{
 $alternate=1;
 $pre_banner_location_name="";
 $banner_location_name="";
 while ($banner = tep_db_fetch_array($banner_query)) 
 {
  if ((!isset($_GET['bID']) || (isset($_GET['bID']) && ($_GET['bID'] == $banner['id']))) && !isset($bInfo) && (substr($action, 0, 3) != 'new')) 
  {
   $bInfo = new objectInfo($banner);
  }
  $alternate++;
  if ( (isset($bInfo) && is_object($bInfo)) && ($banner['id'] == $bInfo->id) ) 
  { 
   $action_image=tep_image(PATH_TO_IMAGE.'icon_arrow_right.gif',IMAGE_EDIT); 
  } 
  else 
  { 
   $action_image='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_BANNER_MANAGEMENT, 'page='.$_GET['page'].'&bID=' . $banner['id']) . '">'.tep_image(PATH_TO_IMAGE.'icon_info.gif',IMAGE_INFO).'</a>'; 
  }
  if ( (isset($bInfo) && is_object($bInfo)) && ($banner['id'] == $bInfo->id) ) 
  {
   $row_selected=' id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
  } 
  else 
  {
   $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
  }

		$banner_location_name=$banner['banner_location_name'];
		$location="";	
  if($pre_banner_location_name !=$banner_location_name  || $pre_banner_location_name=='')
  $location='<tr><td valign="top" class="dataTableContentbold" colspan="4">'.tep_db_output($banner['banner_location_name']).'</td></tr>';
  $template->assign_block_vars('banner', array( 'row_selected' => $row_selected,
   'title' => tep_db_output($banner['title']),
   'name' => tep_db_output($banner['src']),
   'adviews'=>tep_db_output($banner['adviews']),
   'adclicks'=>tep_db_output($banner['adclicks']),
   'location'=>$location,
   'ctr'=>($banner['adviews']==0 || $banner['adclicks']==0?'0':tep_db_output(sprintf('%.2f',($banner['adclicks']/$banner['adviews'])*100))),
   ));
			$pre_banner_location_name=$banner_location_name;

 }
}

//// for right side
/*$ADMIN_RIGHT_HTML="";
$heading = array();
$contents = array();
////

if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) 

{

 $box = new right_box;

 $ADMIN_RIGHT_HTML.= $box->infoBox($heading, $contents);

	$RIGHT_BOX_WIDTH=RIGHT_BOX_WIDTH;

}

else

{

	$RIGHT_BOX_WIDTH='0';

}
*/
/////

$template->assign_vars(array(
 'TABLE_HEADING_BANNER_NAME'=>TABLE_HEADING_BANNER_NAME,
 'TABLE_HEADING_ADVIEWS'=>TABLE_HEADING_ADVIEWS,
 'TABLE_HEADING_ADCLICKS'=>TABLE_HEADING_ADCLICKS,
 'TABLE_HEADING_CTR'=>TABLE_HEADING_CTR,
 'TABLE_HEADING_STATUS'=>TABLE_HEADING_STATUS,
 'TABLE_HEADING_ACTION'=>TABLE_HEADING_ACTION,
 'count_rows'=>$banner_split->display_count($banner_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_BANNERS),
 'no_of_pages'=>$banner_split->display_links($banner_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']),
 'new_button'=>$new_button,
// 'button'=>'<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_BANNER_MANAGEMENT, 'action=new_banner') . '">'.tep_image_button(PATH_TO_BUTTON.'button_new.gif',IMAGE_NEW).'</a>&nbsp;&nbsp;',
 'form'=>tep_draw_form('add_edit', PATH_TO_ADMIN.FILENAME_ADMIN1_BANNER_MANAGEMENT,'ID=' . $id . '&action=insert','post', 'onsubmit="return ValidateForm(this)"'),
 'HEADING_TITLE'=>HEADING_TITLE,
 'MIDDLE_STRING'=>$middle_string,
 'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
 'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
	'different_banners'=>$different_banners,
 'update_message'=>$messageStack->output()));
 $template->pparse('bannerstats');
?>