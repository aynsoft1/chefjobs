<?
/*
***********************************************************
***********************************************************
**********# Name          : Kamal Kumar Sahoo   #**********
**********# Company       : Aynsoft             #**********
**********# Date Created  : 23/07/05            #**********
**********# Date Modified : 23/07/05            #**********
**********# Copyright (c) www.aynsoft.com 2005  #**********
***********************************************************
***********************************************************
*/
include_once("../include_files.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_ADMIN_LANGUAGES);
$template->set_filenames(array('languages' => 'languages.htm'));
include_once(FILENAME_ADMIN_BODY);

$action = (isset($_GET['action']) ? $_GET['action'] : '');

if (tep_not_null($action)) 
{
 switch ($action) 
 {
  case 'insert':
   $name = tep_db_prepare_input($_POST['name']);
   $code = tep_db_prepare_input($_POST['code']);
   $image = tep_db_prepare_input($_POST['image']);
   $directory = tep_db_prepare_input($_POST['directory']);
   $sort_order = tep_db_prepare_input($_POST['sort_order']);

   tep_db_query("insert into " . LANGUAGE_TABLE . " (name, code, image, directory, sort_order) values ('" . tep_db_input($name) . "', '" . tep_db_input($code) . "', '" . tep_db_input($image) . "', '" . tep_db_input($directory) . "', '" . tep_db_input($sort_order) . "')");
   $row_id_check=getAnyTableWhereData(LANGUAGE_TABLE,"1 order by id desc limit 0,1","languages_id");
   $insert_id = $row_id_check['languages_id'];

   // create additional orders_status records
   $orders_status_query = tep_db_query("select orders_status_id, orders_status_name from " . ORDER_STATUS_TABLE . " where language_id = '" . (int)$languages_id . "'");
   while ($orders_status = tep_db_fetch_array($orders_status_query)) 
   {
    tep_db_query("insert into " . ORDER_STATUS_TABLE . " (orders_status_id, language_id, orders_status_name) values ('" . (int)$orders_status['orders_status_id'] . "', '" . (int)$insert_id . "', '" . tep_db_input($orders_status['orders_status_name']) . "')");
   }

   if (isset($_POST['default']) && ($_POST['default'] == 'on')) 
   {
    tep_db_query("update " . CONFIGURATION_TABLE. " set configuration_value = '" . tep_db_input($code) . "' where configuration_name = 'DEFAULT_LANGUAGE'");
   }

   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_LANGUAGES, (isset($_GET['page']) ? 'page=' . $_GET['page'] . '&' : '') . 'lID=' . $insert_id));
  break;
  case 'save':
   $lID = tep_db_prepare_input($_GET['lID']);
   $name = tep_db_prepare_input($_POST['name']);
   $code = tep_db_prepare_input($_POST['code']);
   $image = tep_db_prepare_input($_POST['image']);
   $directory = tep_db_prepare_input($_POST['directory']);
   $sort_order = tep_db_prepare_input($_POST['sort_order']);

   tep_db_query("update " . LANGUAGE_TABLE . " set name = '" . tep_db_input($name) . "', code = '" . tep_db_input($code) . "', image = '" . tep_db_input($image) . "', directory = '" . tep_db_input($directory) . "', sort_order = '" . tep_db_input($sort_order) . "' where languages_id = '" . (int)$lID . "'");

   if ($_POST['default'] == 'on') 
   {
    tep_db_query("update " . CONFIGURATION_TABLE. " set configuration_value = '" . tep_db_input($code) . "' where configuration_name = 'DEFAULT_LANGUAGE'");
   }

   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $_GET['lID']));
  break;
  case 'deleteconfirm':
   $lID = tep_db_prepare_input($_GET['lID']);

   $lng_query = tep_db_query("select languages_id from " . LANGUAGE_TABLE . " where code = '" . DEFAULT_CURRENCY . "'");
   $lng = tep_db_fetch_array($lng_query);
   if ($lng['languages_id'] == $lID) 
   {
    tep_db_query("update " . CONFIGURATION_TABLE. " set configuration_value = '' where configuration_name = 'DEFAULT_CURRENCY'");
   }

   tep_db_query("delete from " . ORDER_STATUS_TABLE . " where language_id = '" . (int)$lID . "'");
   tep_db_query("delete from " . LANGUAGE_TABLE . " where languages_id = '" . (int)$lID . "'");

   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_LANGUAGES, 'page=' . $_GET['page']));
  break;
  case 'delete':
   $lID = tep_db_prepare_input($_GET['lID']);

   $lng_query = tep_db_query("select code from " . LANGUAGE_TABLE . " where languages_id = '" . (int)$lID . "'");
   $lng = tep_db_fetch_array($lng_query);

   $remove_language = true;
   if ($lng['code'] == DEFAULT_LANGUAGE) 
   {
    $remove_language = false;
    $messageStack->add(ERROR_REMOVE_DEFAULT_LANGUAGE, 'error');
   }
  break;
 }
}
$languages_query_raw = "select languages_id, name, code, image, directory, sort_order from " . LANGUAGE_TABLE . " order by sort_order";
$languages_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $languages_query_raw, $languages_query_numrows);
$languages_query = tep_db_query($languages_query_raw);
if(tep_db_num_rows($languages_query) > 0)
{
 $alternate=1;
 while ($languages = tep_db_fetch_array($languages_query)) 
 {
  if ((!isset($_GET['lID']) || (isset($_GET['lID']) && ($_GET['lID'] == $languages['languages_id']))) && !isset($lInfo) && (substr($action, 0, 3) != 'new')) 
  {
   $lInfo = new objectInfo($languages);
  }
  if (isset($lInfo) && is_object($lInfo) && ($languages['languages_id'] == $lInfo->languages_id) ) 
  {
   $row_selected=' id="defaultSelected" class="table-secondary dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $lInfo->languages_id . '&action=edit') . '\'"';
  } 
  else 
  {
   $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $languages['languages_id']) . '\'"';
  }
  $alternate++;
  if (DEFAULT_LANGUAGE == $languages['code']) 
  {
   $name='<b>' . tep_db_output($languages['name']) . ' (' . TEXT_DEFAULT . ')</b>';
  } 
  else 
  {
   $name=tep_db_output($languages['name']);
  }
  if (isset($lInfo) && is_object($lInfo) && ($languages['languages_id'] == $lInfo->languages_id)) 
  { 
   $action_image=tep_image(PATH_TO_IMAGE.'icon_arrow_right.gif'); 
  } 
  else 
  { 
   $action_image= '<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $languages['languages_id']) . '">' . tep_image(PATH_TO_IMAGE.'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; 
  } 
  $template->assign_block_vars('language', array( 'row_selected' => $row_selected,
  'name' => $name,
  'code' => tep_db_output($languages['code']),
  'action' => $action_image,
  'row_selected' => $row_selected
  ));
 }
}
$new_button='';
if(empty($action)) 
{
 $new_button.='<a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $lInfo->languages_id . '&action=new') . '"><i class="bi bi-plus-lg me-2"></i>' . IMAGE_NEW . '</a>';
}

$heading = array();
$contents = array();

switch ($action) 
{
 case 'new':
  $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_LANGUAGE . '</b>');

  $contents = array('form' => tep_draw_form('languages', PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_LANGUAGES, 'action=insert'));
  $contents[] = array('text' => TEXT_INFO_INSERT_INTRO);
  $contents[] = array('text' => '<br>' . TEXT_INFO_LANGUAGE_NAME . '<br>' . tep_draw_input_field('name', '', 'class="form-control form-control-sm"'));
  $contents[] = array('text' => '<br>' . TEXT_INFO_LANGUAGE_CODE . '<br>' . tep_draw_input_field('code', '', 'class="form-control form-control-sm"'));
  $contents[] = array('text' => '<br>' . TEXT_INFO_LANGUAGE_IMAGE . '<br>' . tep_draw_input_field('image', 'icon.gif', 'class="form-control form-control-sm"'));
  $contents[] = array('text' => '<br>' . TEXT_INFO_LANGUAGE_DIRECTORY . '<br>' . tep_draw_input_field('directory', '', 'class="form-control form-control-sm"'));
  $contents[] = array('text' => '<br>' . TEXT_INFO_LANGUAGE_SORT_ORDER . '<br>' . tep_draw_input_field('sort_order', '', 'class="form-control form-control-sm"'));
  $contents[] = array('text' => '<br>' . tep_draw_checkbox_field('default') . ' ' . TEXT_SET_DEFAULT);
  $contents[] = array('align' => 'left', 'text' => '<br>' . tep_draw_submit_button_field('', IMAGE_INSERT,'class="btn btn-primary"') . ' <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $_GET['lID']) . '">' . IMAGE_CANCEL . '</a>');
 break;
 case 'edit':
  $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_LANGUAGE . '</b>');

  $contents = array('form' => tep_draw_form('languages', PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $lInfo->languages_id . '&action=save'));
  $contents[] = array('text' => TEXT_INFO_EDIT_INTRO);
  $contents[] = array('text' => '<br>' . TEXT_INFO_LANGUAGE_NAME . '<br>' . tep_draw_input_field('name', $lInfo->name, 'class="form-control form-control-sm"'));
  $contents[] = array('text' => '<br>' . TEXT_INFO_LANGUAGE_CODE . '<br>' . tep_draw_input_field('code', $lInfo->code, 'class="form-control form-control-sm"'));
  $contents[] = array('text' => '<br>' . TEXT_INFO_LANGUAGE_IMAGE . '<br>' . tep_draw_input_field('image', $lInfo->image, 'class="form-control form-control-sm"'));
  $contents[] = array('text' => '<br>' . TEXT_INFO_LANGUAGE_DIRECTORY . '<br>' . tep_draw_input_field('directory', $lInfo->directory, 'class="form-control form-control-sm"'));
  $contents[] = array('text' => '<br>' . TEXT_INFO_LANGUAGE_SORT_ORDER . '<br>' . tep_draw_input_field('sort_order', $lInfo->sort_order, 'class="form-control form-control-sm"'));
  if (DEFAULT_LANGUAGE != $lInfo->code) 
   $contents[] = array('text' => '<br>' . tep_draw_checkbox_field('default') . ' ' . TEXT_SET_DEFAULT);
  $contents[] = array('align' => 'left', 'text' => '<br>' . tep_draw_submit_button_field('', IMAGE_UPDATE,'class="btn btn-primary"') . ' <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $lInfo->languages_id) . '">' .IMAGE_CANCEL . '</a>');
 break;
 case 'delete':
  $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_LANGUAGE . '</b>');

  $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
  $contents[] = array('text' => '<br><b>' . $lInfo->name . '</b>');
  $contents[] = array('align' => 'left', 'text' => '<br>' . (($remove_language) ? '<a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $lInfo->languages_id . '&action=deleteconfirm') . '">' . tep_image_button(PATH_TO_BUTTON.'button_delete.gif', IMAGE_DELETE) . '</a>' : '') . ' <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $lInfo->languages_id) . '">' .IMAGE_CANCEL . '</a>');
 break;
 default:
  if (is_object($lInfo)) 
  {
   $heading[] = array('text' => '<b>' . $lInfo->name . '</b>');
   $contents[] = array('align' => 'left', 'text' => '<a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $lInfo->languages_id . '&action=edit') . '">' .IMAGE_EDIT . '</a> <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $lInfo->languages_id . '&action=delete') . '">' .IMAGE_DELETE . '</a>');
   $contents[] = array('text' => '<br>' . TEXT_INFO_LANGUAGE_NAME . ' ' . $lInfo->name);
   $contents[] = array('text' => TEXT_INFO_LANGUAGE_CODE . ' ' . $lInfo->code);
   $contents[] = array('text' => '<br>' . tep_image(PATH_TO_LANGUAGE.$lInfo->directory . '/images/' . $lInfo->image, $lInfo->name));
   $contents[] = array('text' => '<br>' . TEXT_INFO_LANGUAGE_DIRECTORY . '<br>' . str_replace("/","/ ",PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE). '<b>' . $lInfo->directory . '</b>');
   $contents[] = array('text' => '<br>' . TEXT_INFO_LANGUAGE_SORT_ORDER . ' ' . $lInfo->sort_order);
  }
 break;
}

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
/////
$template->assign_vars(array(
 'TABLE_HEADING_LANGUAGE_NAME'=>TABLE_HEADING_LANGUAGE_NAME,
 'TABLE_HEADING_LANGUAGE_CODE'=>TABLE_HEADING_LANGUAGE_CODE,
 'TABLE_HEADING_ACTION'=>TABLE_HEADING_ACTION,
 'count_rows'=>$languages_split->display_count($languages_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_LANGUAGES),
 'no_of_pages'=>$languages_split->display_links($languages_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']),
 'new_button'=>$new_button,
 'HEADING_TITLE'=>HEADING_TITLE,
 'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
 'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('languages');
?>