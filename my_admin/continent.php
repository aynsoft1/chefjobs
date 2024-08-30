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
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_ADMIN_CONTINENT);
$template->set_filenames(array('continent' => 'continent.htm'));
include_once(FILENAME_ADMIN_BODY);

$action = (isset($_GET['action']) ? $_GET['action'] : '');

if ($action!="") 
{
 switch ($action) 
	{
  case 'confirm_delete':
   $id = tep_db_prepare_input($_GET['id']);
   tep_db_query("delete from " . CONTINENT_TABLE . " where id = '" . (int)$id . "'");
			$messageStack->add_session(MESSAGE_SUCCESS_DELETED, 'success');
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_CONTINENT, 'page=' . $_GET['page']));
  break;
  case 'insert':
  case 'save':
   $continent_name=tep_db_prepare_input($_POST['TR_continent_name']);
   $priority = tep_db_prepare_input($_POST['IN_priority']);
   $sql_data_array['continent_name'] = $continent_name;
   $sql_data_array['priority'] = $priority;

			if($action=='insert')
			{
				if($row_chek=getAnyTableWhereData(CONTINENT_TABLE,"continent_name='".tep_db_input($continent_name)."'",'id'))
				{
					$messageStack->add(MESSAGE_NAME_ERROR, 'error');
				}
				else
				{
     tep_db_perform(CONTINENT_TABLE, $sql_data_array);
     $row_id_check=getAnyTableWhereData(CONTINENT_TABLE,"1 order by id desc limit 0,1","id");
     $id = $row_id_check['id'];
  			$messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');
					tep_redirect(FILENAME_ADMIN1_ADMIN_CONTINENT);
				}
			}
			else
			{
    $id=(int)$_GET['id'];
				if($row_chek=getAnyTableWhereData(CONTINENT_TABLE,"continent_name='".tep_db_input($continent_name)."' and id!='$id'",'id'))
				{
					$messageStack->add(MESSAGE_NAME_ERROR, 'error');
				}
				else
				{
     tep_db_perform(CONTINENT_TABLE, $sql_data_array, 'update', "id = '" . (int)$id . "'");
  			$messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
					tep_redirect(FILENAME_ADMIN1_ADMIN_CONTINENT.'?page='.$_GET['page'].'&id='.$id);
				}
			}
  break;
 }
}
///////////// Middle Values 
$continent_query_raw="select id, continent_name,priority from " . CONTINENT_TABLE ." order by priority";
$continent_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $continent_query_raw, $continent_query_numrows);
$continent_query = tep_db_query($continent_query_raw);
if(tep_db_num_rows($continent_query) > 0)
{
 $alternate=1;
 while ($continent = tep_db_fetch_array($continent_query)) 
 {
  if ((!isset($_GET['id']) || (isset($_GET['id']) && ($_GET['id'] == $continent['id']))) && !isset($cInfo) && (substr($action, 0, 3) != 'new')) 
  {
   $cInfo = new objectInfo($continent);
  }
  if ( (isset($cInfo) && is_object($cInfo)) && ($continent['id'] == $cInfo->id) ) 
  {
   $row_selected=' id="defaultSelected" class="table-secondary dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . FILENAME_ADMIN1_ADMIN_CONTINENT . '?page='.$_GET['page'].'&id=' . $cInfo->id . '&action=edit\'"';
  } 
  else 
  {
   $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . FILENAME_ADMIN1_ADMIN_CONTINENT . '?page='.$_GET['page'].'&id=' . $continent['id'] . '\'"';
  }
  $alternate++;
  if ( (isset($cInfo) && is_object($cInfo)) && ($continent['id'] == $cInfo->id) ) 
  { 
   $action_image=tep_image(PATH_TO_IMAGE.'icon_arrow_right.gif',IMAGE_EDIT); 
  } 
  else 
  { 
   $action_image='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_CONTINENT, 'page='.$_GET['page'].'&id=' . $continent['id']) . '">'.tep_image(PATH_TO_IMAGE.'icon_info.gif',IMAGE_INFO).'</a>'; 
  }
  $template->assign_block_vars('continent', array( 'row_selected' => $row_selected,
   'action' => $action_image,
   'name' => tep_db_output($continent['continent_name']),
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
  '.TEXT_INFO_HEADING_CONTINENT.'</div>
  </div>');
  $contents = array('form' => tep_draw_form('continent', PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_CONTINENT, 'action=insert','post',' onsubmit="return ValidateForm(this)"'));
  $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
  <div class="mb-1 text-danger">'.TEXT_INFO_NEW_INTRO.'</div>
  <div class="form-group">
  <label>'.TEXT_INFO_CONTINENT_NAME.'</label>
  '.tep_draw_input_field('TR_continent_name', $_POST['TR_continent_name'], 'class="form-control form-control-sm"' ).'
  </div>
  <div class="form-group">
  <label>'.TEXT_INFO_CONTINENT_PRIORITY.'</label>
  '.tep_draw_input_field('IN_priority', $_POST['IN_priority'], 'class="form-control form-control-sm"' ).'
  </div>
  '.tep_draw_submit_button_field('',IMAGE_INSERT,'class="btn btn-primary"').'
  <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_CONTINENT) . '">' 
  . IMAGE_CANCEL . '</a>
  </div>');
  
    break;
 case 'edit':
  $value_field=tep_draw_input_field('TR_continent_name', $cInfo->continent_name, '' );
  $heading[] = array('text' => '<div class="list-group">
  <div class="font-weight-bold text-primary">
  '.TEXT_INFO_HEADING_CONTINENT.'</div>
  </div>');
  $contents = array('form' => tep_draw_form('continent', PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_CONTINENT, 'id=' . $cInfo->id.'&page='.$_GET['page'].'&action=save','post',' onsubmit="return ValidateForm(this)"'));
  
  $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
  <div class="mb-1 text-danger">'.TEXT_INFO_EDIT_INTRO.'</div>
  <div class="form-group">
  <label>'.TEXT_INFO_CONTINENT_NAME.'</label>
  '.tep_draw_input_field('TR_continent_name', $cInfo->continent_name, 'class="form-control form-control-sm"' ).'
  </div>
  <div class="form-group">
  <label>'.TEXT_INFO_CONTINENT_PRIORITY.'</label>
  '.tep_draw_input_field('IN_priority', $cInfo->priority, 'class="form-control form-control-sm"' ).'
  </div>
  '.tep_draw_submit_button_field('',IMAGE_UPDATE,'class="btn btn-primary"').'
  <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_CONTINENT, 'gid=' . $_GET['gid'] . '&id=' . $cInfo->id ) . '">' 
  . IMAGE_CANCEL . '</a>
  </div>');
  
  break;
 case 'delete':
  $heading[] = array('text' => '<div class="list-group">
  <div class="font-weight-bold">
  '.$cInfo->continent_name.'</div>
  </div>');
  $contents = array('form' => tep_draw_form('continent_delete', PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_CONTINENT, 'page=' . $_GET['page'] . '&id=' . $nInfo->id . '&action=deleteconfirm'));
  $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
  <div class="mb-1 font-weight-bold">'.TEXT_DELETE_INTRO.'</div>
  <a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_CONTINENT, 'page=' . $_GET['page'] . '&id=' . $_GET['id'].'&action=confirm_delete') . '">'
  .IMAGE_CONFIRM.'</a>
  <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_CONTINENT, 'page=' . $_GET['page'] . '&id=' . $_GET['id']) . '">' 
  . IMAGE_CANCEL . '</a>
  </div>');
 break;
 default:
  if (isset($cInfo) && is_object($cInfo)) 
		{
   $heading[] = array('text' => '<div class="list-group"><div class="font-weight-bold text-primary">'.TEXT_INFO_HEADING_CONTINENT.'</div></div>');
   $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
   <div class="mb-1 text-danger">'.tep_db_output($cInfo->continent_name).'</div>
   <a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_CONTINENT, 'page=' . $_GET['page'] .'&id=' . $cInfo->id . '&action=edit') . '">'
   .IMAGE_EDIT.'</a>
   <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_CONTINENT, 'page=' . $_GET['page'] .'&id=' . $cInfo->id . '&action=delete') . '">'
   .IMAGE_DELETE.'</a>
   <div class="mt-1">'.TEXT_INFO_ACTION.'</div>
   </div>');
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
 'TABLE_HEADING_CONTINENT_NAME'=>TABLE_HEADING_CONTINENT_NAME,
 'TABLE_HEADING_ACTION'=>TABLE_HEADING_ACTION,
 'count_rows'=>$continent_split->display_count($continent_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_CONTINENT),
 'no_of_pages'=>$continent_split->display_links($continent_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']),
 'new_button'=>'<a class="btn btn-primary float-right" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_CONTINENT, 'page=' . $_GET['page'] .'&action=new') . '"><i class="bi bi-plus-lg me-2"></i>'.IMAGE_NEW.'</a>',
 'HEADING_TITLE'=>HEADING_TITLE,
 'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
 'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('continent');
?>