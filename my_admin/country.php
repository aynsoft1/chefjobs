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
include_once("../include_files.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_ADMIN_COUNTRY);
$template->set_filenames(array('country' => 'country.htm'));
include_once(FILENAME_ADMIN_BODY);

$action = (isset($_GET['action']) ? $_GET['action'] : '');

if ($action!="") 
{
 switch ($action) 
	{
  case 'confirm_delete':
   $id = tep_db_prepare_input($_GET['id']);
   tep_db_query("delete from " . COUNTRIES_TABLE . " where id = '" . (int)$id . "'");
			$messageStack->add_session(MESSAGE_SUCCESS_DELETED, 'success');
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_COUNTRY, 'page=' . $_GET['page']));
  break;
  case 'insert':
  case 'save':
   $continent_name = tep_db_prepare_input($_POST['TR_continent_name']);
   $country_name   = tep_db_prepare_input($_POST['TR_country_name']);
			$de_country_name= tep_db_prepare_input($_POST['TR_de_country_name']);
			$country_code   = tep_db_prepare_input($_POST['country_code']);
   $priority = tep_db_prepare_input($_POST['IN_priority']);
   $sql_data_array['continent_id'] = $continent_name;
   $sql_data_array['country_name'] = $country_name;
			$sql_data_array['de_country_name'] = $de_country_name;
   $sql_data_array['country_code'] = $country_code;
   $sql_data_array['priority'] = $priority;

			if($action=='insert')
			{
				if($row_chek=getAnyTableWhereData(COUNTRIES_TABLE,"country_name='".tep_db_input($country_name)."'",'id'))
				{
					$messageStack->add(MESSAGE_NAME_ERROR, 'error');
				}
				else if($row_chek=getAnyTableWhereData(COUNTRIES_TABLE,"de_country_name='".tep_db_input($de_country_name)."'",'id'))
				{
					$messageStack->add(MESSAGE_FR_NAME_ERROR, 'error');
				}
				else
				{
     tep_db_perform(COUNTRIES_TABLE, $sql_data_array);
     $row_id_check=getAnyTableWhereData(COUNTRIES_TABLE,"1 order by id desc limit 0,1","id");
     $id = $row_id_check['id'];
  			$messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');
					tep_redirect(FILENAME_ADMIN1_ADMIN_COUNTRY);
				}
			}
			else
			{
    $id=(int)$_GET['id'];
				if($row_chek=getAnyTableWhereData(COUNTRIES_TABLE,"country_name='".tep_db_input($country_name)."' and id!='$id'",'id'))
				{
					$messageStack->add(MESSAGE_NAME_ERROR, 'error');
					$action='edit';
				}
				else if($row_chek=getAnyTableWhereData(COUNTRIES_TABLE,"de_country_name='".tep_db_input($de_country_name)."' and id!='$id'",'id'))
				{
					$messageStack->add(MESSAGE_FR_NAME_ERROR, 'error');
					$action='edit';
				}
				else
				{
     tep_db_perform(COUNTRIES_TABLE, $sql_data_array, 'update', "id = '" . (int)$id . "'");
  			$messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
					tep_redirect(FILENAME_ADMIN1_ADMIN_COUNTRY.'?page='.$_GET['page'].'&id='.$id);
				}
			}
  break;
 }
}
///////////// Middle Values 
$country_query_raw="select c.id, c.continent_id, cont.continent_name, c.country_name, c.de_country_name,c.country_code,c.priority from " . COUNTRIES_TABLE ." as c left join ".CONTINENT_TABLE." as cont on (c.continent_id=cont.id) order by c.country_name";
$country_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $country_query_raw, $country_query_numrows);
$country_query = tep_db_query($country_query_raw);
if(tep_db_num_rows($country_query) > 0)
{
 $alternate=1;
 while ($country = tep_db_fetch_array($country_query)) 
 {
  if ((!isset($_GET['id']) || (isset($_GET['id']) && ($_GET['id'] == $country['id']))) && !isset($cInfo) && (substr($action, 0, 3) != 'new')) 
  {
   $cInfo = new objectInfo($country);
  }
  if ( (isset($cInfo) && is_object($cInfo)) && ($country['id'] == $cInfo->id) ) 
  {
   $row_selected=' id="defaultSelected" class="table-secondary dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . FILENAME_ADMIN1_ADMIN_COUNTRY . '?page='.$_GET['page'].'&id=' . $cInfo->id . '&action=edit\'"';
  } 
  else 
  {
   $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . FILENAME_ADMIN1_ADMIN_COUNTRY . '?page='.$_GET['page'].'&id=' . $country['id'] . '\'"';
  }
  $alternate++;
  if ( (isset($cInfo) && is_object($cInfo)) && ($country['id'] == $cInfo->id) ) 
  { 
   $action_image=tep_image(PATH_TO_IMAGE.'icon_arrow_right.gif',IMAGE_EDIT); 
  } 
  else 
  { 
   $action_image='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_COUNTRY, 'page='.$_GET['page'].'&id=' . $country['id']) . '">'.tep_image(PATH_TO_IMAGE.'icon_info.gif',IMAGE_INFO).'</a>'; 
  }
  $template->assign_block_vars('country', array( 'row_selected' => $row_selected,
   'action' => $action_image,
   'country_name' => tep_db_output($country['country_name']),
   'de_country_name' => tep_db_output($country['de_country_name']),
   'continent_name' => tep_db_output($country['continent_name']),
   'row_selected' => $row_selected
   ));
 }
}
//// for right side
$ADMIN_RIGHT_HTML="";

$heading = array();
$contents = array();
switch ($action) 
{
 case 'new':
 case 'insert':
 case 'save':
    $heading[] = array('text' => '<div class="list-group">
    <div class="font-weight-bold text-primary">
    '.TEXT_INFO_HEADING_COUNTRY.'</div>
    </div>');
    $contents = array('form' => tep_draw_form('country', PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_COUNTRY, 'action=insert','post',' onsubmit="return ValidateForm(this)"'));

    $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
    <div class="mb-1 text-danger">'.TEXT_INFO_NEW_INTRO.'</div>
    <div class="form-group">
    <label>'.TEXT_INFO_CONTINENT_NAME.'</label>
    '.LIST_TABLE(CONTINENT_TABLE, 'continent_name', 'continent_name', 'name="TR_continent_name" class="form-control form-control-sm"', '', '' ,$_POST['TR_continent_name']).'
    </div>
    <div class="form-group">
    <label>'.TEXT_INFO_COUNTRY_NAME.'</label>
    '.tep_draw_input_field('TR_country_name', $_POST['TR_country_name'], 'class="form-control form-control-sm"' ).'
    </div>
    <div class="form-group">
    <label>'.TEXT_INFO_FR_COUNTRY_NAME.'</label>
    '.tep_draw_input_field('TR_de_country_name', $_POST['TR_de_country_name'], 'class="form-control form-control-sm"' ).'
    </div>
    <div class="form-group">
    <label>'.TEXT_INFO_COUNTRY_CODE.'</label>
    '.tep_draw_input_field('country_code', $_POST['country_code'], 'class="form-control form-control-sm"' ).'
    </div>
    <div class="form-group">
    <label>'.TEXT_INFO_CONTINENT_PRIORITY.'</label>
    '.tep_draw_input_field('IN_priority', $_POST['IN_priority'], 'class="form-control form-control-sm"' ).'
    </div>
    '.tep_draw_submit_button_field('',IMAGE_INSERT,'class="btn btn-primary"').'
    <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_COUNTRY) . '">' 
    . IMAGE_CANCEL . '</a>
    </div>');
    break;
 case 'edit':
  $value_field=tep_draw_input_field('TR_country_name', $cInfo->country_name, '' );
  $heading[] = array('text' => '<div class="list-group">
  <div class="font-weight-bold  text-primary">
  '.TEXT_INFO_HEADING_COUNTRY.'</div>
  </div>');
  $contents = array('form' => tep_draw_form('country', PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_COUNTRY, 'id=' . $cInfo->id.'&page='.$_GET['page'].'&action=save','post',' onsubmit="return ValidateForm(this)"'));
  $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
  <div class="mb-1 text-danger">'.TEXT_INFO_EDIT_INTRO.'</div>
  <div class="form-group">
  <label>'.TEXT_INFO_CONTINENT_NAME.'</label>
  '.LIST_TABLE(CONTINENT_TABLE, 'continent_name', 'continent_name', 'name="TR_continent_name" class="form-control form-control-sm"', '', '' ,$cInfo->continent_id).'
  </div>
  <div class="form-group">
  <label>'.TEXT_INFO_COUNTRY_NAME.'</label>
  '.tep_draw_input_field('TR_country_name', $cInfo->country_name, 'class="form-control form-control-sm"' ).'
  </div>
  <div class="form-group">
  <label>'.TEXT_INFO_FR_COUNTRY_NAME.'</label>
  '.tep_draw_input_field('TR_de_country_name', $cInfo->de_country_name, 'class="form-control form-control-sm"' ).'
  </div>
  <div class="form-group">
  <label>'.TEXT_INFO_COUNTRY_CODE.'</label>
  '.tep_draw_input_field('country_code', $cInfo->country_code, 'class="form-control form-control-sm"' ).'
  </div>
  <div class="form-group">
  <label>'.TEXT_INFO_CONTINENT_PRIORITY.'</label>
  '.tep_draw_input_field('IN_priority', $cInfo->priority, 'class="form-control form-control-sm"' ).'
  </div>
  '.tep_draw_submit_button_field('',IMAGE_UPDATE,'class="btn btn-primary"').'
  <a class="btn btn-secondary" href="' .tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_COUNTRY, 'gid=' . $_GET['gid'] . '&id=' . $cInfo->id ). '">' 
  . IMAGE_CANCEL . '</a>
  </div>');
  
  break;
 case 'delete':
  $heading[] = array('text' => '<div class="list-group">
  <div class="font-weight-bold">
  '.$cInfo->country_name.'</div>
  </div>');
  $contents = array('form' => tep_draw_form('country_delete', PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_COUNTRY, 'page=' . $_GET['page'] . '&id=' . $nInfo->id . '&action=deleteconfirm'));
  $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
  <div class="mb-1 font-weight-bold">'.TEXT_DELETE_INTRO.'</div>
  <a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_COUNTRY, 'page=' . $_GET['page'] . '&id=' . $_GET['id'].'&action=confirm_delete') . '">'
  .IMAGE_CONFIRM.'</a>
  <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_COUNTRY, 'page=' . $_GET['page'] . '&id=' . $_GET['id']) . '">' 
  . IMAGE_CANCEL . '</a>
  </div>');
 break;
 default:
  if (isset($cInfo) && is_object($cInfo)) 
		{
   $heading[] = array('text' => '<div class="list-group"><div class="font-weight-bold  text-primary">'.TEXT_INFO_HEADING_COUNTRY.'</div></div>');
   $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
   <div class="mb-1 text-danger">'.tep_db_output($cInfo->country_name).'</div>
   <a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_COUNTRY, 'page=' . $_GET['page'] .'&id=' . $cInfo->id . '&action=edit') . '">'
   .IMAGE_EDIT.'</a>
   <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_COUNTRY, 'page=' . $_GET['page'] .'&id=' . $cInfo->id . '&action=delete') . '">'
   .IMAGE_DELETE.'</a>
   <div class="mt-1">'.TEXT_INFO_ACTION.'</div>
   </div>');
  }
  break;
}
if((tep_not_null($heading)) && (tep_not_null($contents)) ) 
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
 'TABLE_HEADING_COUNTRY_NAME'=>TABLE_HEADING_COUNTRY_NAME,
	'TABLE_HEADING_FR_COUNTRY_NAME'=>TABLE_HEADING_FR_COUNTRY_NAME,
 'TABLE_HEADING_CONTINENT_NAME'=>TABLE_HEADING_CONTINENT_NAME,
 'TABLE_HEADING_ACTION'=>TABLE_HEADING_ACTION,
 'count_rows'=>$country_split->display_count($country_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_COUNTRIES),
 'no_of_pages'=>$country_split->display_links($country_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']),
 'new_button'=>'<a class="btn btn-primary float-right" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_COUNTRY, 'page=' . $_GET['page'] .'&action=new') . '"><i class="bi bi-plus-lg me-2"></i>'.IMAGE_NEW.'</a>',
 'HEADING_TITLE'=>HEADING_TITLE,
 'RIGHT_BOX_WIDTH'=>RIGHT_BOX_WIDTH,
 'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('country');
?>