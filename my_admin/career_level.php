<?
/*
***********************************************************
***********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
***********************************************************
***********************************************************
*/
include_once("../include_files.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_ADMIN_CAREER_LEVEL);
$template->set_filenames(array('career_level' => 'career_level.htm'));
include_once(FILENAME_ADMIN_BODY);

$action = (isset($_GET['action']) ? $_GET['action'] : '');

if ($action!="") 
{
 switch ($action) 
	{
  case 'confirm_delete':
   $id = tep_db_prepare_input($_GET['id']);
   tep_db_query("delete from " . CAREER_LEVEL_TABLE . " where id = '" . (int)$id . "'");
			$messageStack->add_session(MESSAGE_SUCCESS_DELETED, 'success');
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_CAREER_LEVEL, 'page=' . $_GET['page']));
  break;
  case 'insert':
  case 'save':
   $career_level_name=tep_db_prepare_input($_POST['TR_career_level_name']);
 		$de_career_level_name=tep_db_prepare_input($_POST['TR_de_career_level_name']);
   $priority = tep_db_prepare_input($_POST['IN_priority']);
   $sql_data_array['career_level_name'] = $career_level_name;
			$sql_data_array['de_career_level_name'] = $de_career_level_name;
   $sql_data_array['priority'] = $priority;

			if($action=='insert')
			{
				if($row_chek=getAnyTableWhereData(CAREER_LEVEL_TABLE," career_level_name='".tep_db_input($career_level_name)."'",'id'))
				{
					$messageStack->add(MESSAGE_NAME_ERROR, 'error');
				}
				else if($row_chek=getAnyTableWhereData(CAREER_LEVEL_TABLE," de_career_level_name='".tep_db_input($de_career_level_name)."'",'id'))
				{
					$messageStack->add(MESSAGE_FR_NAME_ERROR, 'error');
				}
				else
				{
     tep_db_perform(CAREER_LEVEL_TABLE, $sql_data_array);
     $row_id_check=getAnyTableWhereData(CAREER_LEVEL_TABLE,"1 order by id desc limit 0,1","id");
     $id = $row_id_check['id'];
  			$messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');
					tep_redirect(FILENAME_ADMIN1_ADMIN_CAREER_LEVEL);
				}
			}
			else
			{
    $id=(int)$_GET['id'];
				if($row_chek=getAnyTableWhereData(CAREER_LEVEL_TABLE," career_level_name='".tep_db_input($career_level_name)."' and id!='$id'",'id'))
				{
					$messageStack->add(MESSAGE_NAME_ERROR, 'error');
					$action='edit';
				}
				else if($row_chek=getAnyTableWhereData(CAREER_LEVEL_TABLE," de_career_level_name='".tep_db_input($de_career_level_name)."' and id!='$id'",'id'))
				{
					$messageStack->add(MESSAGE_FR_NAME_ERROR, 'error');
					$action='edit';
				}
				else
				{
     tep_db_perform(CAREER_LEVEL_TABLE, $sql_data_array, 'update', "id = '" . (int)$id . "'");
  			$messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
					tep_redirect(FILENAME_ADMIN1_ADMIN_CAREER_LEVEL.'?page='.$_GET['page'].'&id='.$id);
				}
			}
  break;
 }
}
///////////// Middle Values 
$career_level_query_raw="select id, career_level_name,de_career_level_name,priority from " . CAREER_LEVEL_TABLE ." order by priority";
$career_level_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $career_level_query_raw, $career_level_query_numrows);
$career_level_query = tep_db_query($career_level_query_raw);
if(tep_db_num_rows($career_level_query) > 0)
{
 $alternate=1;
 while ($career_level = tep_db_fetch_array($career_level_query)) 
 {
  if ((!isset($_GET['id']) || (isset($_GET['id']) && ($_GET['id'] == $career_level['id']))) && !isset($cInfo) && (substr($action, 0, 3) != 'new')) 
  {
   $cInfo = new objectInfo($career_level);
  }
  if ( (isset($cInfo) && is_object($cInfo)) && ($career_level['id'] == $cInfo->id) ) 
  {
   $row_selected='id="defaultSelected" class="table-secondary dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . FILENAME_ADMIN1_ADMIN_CAREER_LEVEL . '?page='.$_GET['page'].'&id=' . $cInfo->id . '&action=edit\'"';
  } 
  else 
  {
   $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . FILENAME_ADMIN1_ADMIN_CAREER_LEVEL . '?page='.$_GET['page'].'&id=' . $career_level['id'] . '\'"';
  }
  $alternate++;
  if ( (isset($cInfo) && is_object($cInfo)) && ($career_level['id'] == $cInfo->id) ) 
  { 
   $action_image=tep_image(PATH_TO_IMAGE.'icon_arrow_right.gif',IMAGE_EDIT); 
  } 
  else 
  { 
   $action_image='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_CAREER_LEVEL, 'page='.$_GET['page'].'&id=' . $career_level['id']) . '">'.tep_image(PATH_TO_IMAGE.'icon_info.gif',IMAGE_INFO).'</a>'; 
  }
  $template->assign_block_vars('career_level', array( 'row_selected' => $row_selected,
   'action' => $action_image,
   'name' => tep_db_output($career_level['career_level_name']),
   'de_name' => tep_db_output($career_level['de_career_level_name']),
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
    '.TEXT_INFO_HEADING_CAREER_LEVEL.'</div>
    </div>');
  $contents = array('form' => tep_draw_form('career_level', PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_CAREER_LEVEL, 'action=insert','post',' onsubmit="return ValidateForm(this)"'));

    $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
    <div class="mb-1 text-danger">'.TEXT_INFO_NEW_INTRO.'</div>
    <div class="form-group">
    <label>'.TEXT_INFO_CAREER_LEVEL_NAME.'</label>
    '.tep_draw_input_field('TR_career_level_name', $_POST['TR_career_level_name'], 'class="form-control form-control-sm"' ).'
    </div>
    <div class="form-group">
    <label>'.TEXT_INFO_FR_CAREER_LEVEL_NAME.'</label>
    '.tep_draw_input_field('TR_de_career_level_name', $_POST['TR_de_career_level_name'], 'class="form-control form-control-sm"' ).'
    </div>
    <div class="form-group">
    <label>'.TEXT_INFO_CAREER_LEVEL_PRIORITY.'</label>
    '.tep_draw_input_field('IN_priority', $_POST['IN_priority'], 'class="form-control form-control-sm"' ).'
    </div>
    '.tep_draw_submit_button_field('',IMAGE_INSERT,'class="btn btn-primary"').'
    <a class="btn btn-secondary" href="' .tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_CAREER_LEVEL) . '">' 
    . IMAGE_CANCEL . '</a>
    </div>');
  break;
 case 'edit':
  $value_field=tep_draw_input_field('TR_career_level_name', $cInfo->career_level_name, '' );
  $heading[] = array('text' => '<div class="list-group">
  <div class="font-weight-bold text-primary">
  '.TEXT_INFO_CAREER_LEVEL_NAME.'</div>
  </div>');
  $contents = array('form' => tep_draw_form('career_level', PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_CAREER_LEVEL, 'id=' . $cInfo->id.'&page='.$_GET['page'].'&action=save','post',' onsubmit="return ValidateForm(this)"'));
  
  $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
  <div class="mb-1 text-danger">'.TEXT_INFO_EDIT_INTRO.'</div>
  <div class="form-group">
  <label>'.TEXT_INFO_CAREER_LEVEL_NAME.'</label>
  '.tep_draw_input_field('TR_career_level_name', $cInfo->career_level_name, 'class="form-control form-control-sm"' ).'
  </div>
  <div class="form-group">
  <label>'.TEXT_INFO_FR_CAREER_LEVEL_NAME.'</label>
  '.tep_draw_input_field('TR_de_career_level_name', $cInfo->de_career_level_name, 'class="form-control form-control-sm"' ).'
  </div>
  <div class="form-group">
  <label>'.TEXT_INFO_CAREER_LEVEL_PRIORITY.'</label>
  '.tep_draw_input_field('IN_priority', $cInfo->priority, 'class="form-control form-control-sm"' ).'
  </div>
  '.tep_draw_submit_button_field('',IMAGE_UPDATE,'class="btn btn-primary"').'
  <a class="btn btn-secondary" href="' .tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_CAREER_LEVEL, 'gid=' . $_GET['gid'] . '&id=' . $cInfo->id ) . '">' 
  . IMAGE_CANCEL . '</a>
  </div>');
  
  break;
 case 'delete':
  $heading[] = array('text' => '<div class="list-group">
  <div class="font-weight-bold">
  '.$cInfo->career_level_name.'</div>
  </div>');
  $contents = array('form' => tep_draw_form('career_level_delete', PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_CAREER_LEVEL, 'page=' . $_GET['page'] . '&id=' . $cInfo->id . '&action=deleteconfirm'));
  $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
  <div class="mb-1 font-weight-bold">'.TEXT_DELETE_INTRO.'
  <p>'.$cInfo->career_level_name.'</p></div>
  <a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_CAREER_LEVEL, 'page=' . $_GET['page'] . '&id=' . $_GET['id'].'&action=confirm_delete') . '">' 
  . IMAGE_CONFIRM . '</a>
  <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_CAREER_LEVEL, 'page=' . $_GET['page'] . '&id=' . $_GET['id']) . '">' 
  . IMAGE_CANCEL . '</a>
  </div>');
 break;
 default:
  if (isset($cInfo) && is_object($cInfo)) 
		{
  //  $heading[] = array('text' => '<b>'.TEXT_INFO_HEADING_CAREER_LEVEL.'</b>');
  //  $contents[] = array('text' => tep_db_output($cInfo->career_level_name));
  //  $contents[] = array('align' => 'left', 'text' => '<br><a href="' . . '">'.tep_image_button(PATH_TO_BUTTON.'button_edit.gif',IMAGE_EDIT).'</a>&nbsp;<a href="' .  . '">'.tep_image_button(PATH_TO_BUTTON.'button_delete.gif',IMAGE_DELETE).'</a>');
  //  $contents[] = array('text' => '<br>'.TEXT_INFO_ACTION);

   $heading[] = array('text' => '<div class="list-group"><div class="font-weight-bold text-primary">'.TEXT_INFO_HEADING_CAREER_LEVEL.'</div></div>');
   $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
   <div class="mb-1 text-danger">'.tep_db_output($cInfo->career_level_name).'<strong class="d-block">'.TEXT_INFO_ACTION.'</strong></div>
   <a class="btn btn-primary" href="' .  tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_CAREER_LEVEL, 'page=' . $_GET['page'] .'&id=' . $cInfo->id . '&action=edit') . '">'
   .IMAGE_EDIT.'</a>
   <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_CAREER_LEVEL, 'page=' . $_GET['page'] .'&id=' . $cInfo->id . '&action=delete') . '">'
   .IMAGE_DELETE.'</a>
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
 'TABLE_HEADING_FR_CAREER_LEVEL'=>TABLE_HEADING_FR_CAREER_LEVEL,
 'TABLE_HEADING_CAREER_LEVEL'=>TABLE_HEADING_CAREER_LEVEL,
 'TABLE_HEADING_ACTION'=>TABLE_HEADING_ACTION,
 'count_rows'=>$career_level_split->display_count($career_level_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_CAREER_LEVEL),
 'no_of_pages'=>$career_level_split->display_links($career_level_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']),
 'new_button'=>'<a class="btn btn-primary float-right" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_CAREER_LEVEL, 'page=' . $_GET['page'] .'&action=new') . '"><i class="bi bi-plus-lg me-2"></i>'.IMAGE_NEW.'</a>',
 'HEADING_TITLE'=>HEADING_TITLE,
 'RIGHT_BOX_WIDTH'=>RIGHT_BOX_WIDTH,
 'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('career_level');
?>