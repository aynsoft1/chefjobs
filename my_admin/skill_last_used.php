<?
include_once("../include_files.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_ADMIN_SKILL_LAST_USED);
$template->set_filenames(array('skill_last_used' => 'skill_last_used.htm'));
include_once(FILENAME_ADMIN_BODY);

$action = (isset($_GET['action']) ? $_GET['action'] : '');

if ($action!="") 
{
 switch ($action) 
	{
  case 'confirm_delete':
   $id = tep_db_prepare_input($_GET['id']);
   tep_db_query("delete from " . SKILL_LAST_USED_TABLE . " where id = '" . (int)$id . "'");
			$messageStack->add_session(MESSAGE_SUCCESS_DELETED, 'success');
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_SKILL_LAST_USED, 'page=' . $_GET['page']));
  break;
  case 'insert':
  case 'save':
   $skill_last_used=tep_db_prepare_input($_POST['TR_skill_last_used']);
   $priority = tep_db_prepare_input($_POST['IN_priority']);
   $sql_data_array['skill_last_used'] = $skill_last_used;
   $sql_data_array['priority'] = $priority;

			if($action=='insert')
			{
				if($row_chek=getAnyTableWhereData(SKILL_LAST_USED_TABLE,"skill_last_used='".tep_db_input($skill_last_used)."'",'id'))
				{
					$messageStack->add(MESSAGE_NAME_ERROR, 'error');
				}
				else
				{
     tep_db_perform(SKILL_LAST_USED_TABLE, $sql_data_array);
  			$messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');
					tep_redirect(FILENAME_ADMIN1_ADMIN_SKILL_LAST_USED);
				}
			}
			else
			{
    $id=(int)$_GET['id'];
				if($row_chek=getAnyTableWhereData(SKILL_LAST_USED_TABLE,"skill_last_used='".tep_db_input($skill_last_used)."' and id!='$id'",'id'))
				{
					$messageStack->add(MESSAGE_NAME_ERROR, 'error');
				}
				else
				{
     tep_db_perform(SKILL_LAST_USED_TABLE, $sql_data_array, 'update', "id = '" . (int)$id . "'");
  			$messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
					tep_redirect(FILENAME_ADMIN1_ADMIN_SKILL_LAST_USED.'?page='.$_GET['page'].'&id='.$id);
				}
			}
  break;
 }
}
///////////// Middle Values 
$education_level_query_raw="select id, skill_last_used,priority from " . SKILL_LAST_USED_TABLE ." order by priority";
$education_level_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $education_level_query_raw, $education_level_query_numrows);
$education_level_query = tep_db_query($education_level_query_raw);
if(tep_db_num_rows($education_level_query) > 0)
{
 $alternate=1;
 while ($skill_last_used = tep_db_fetch_array($education_level_query)) 
 {
  if ((!isset($_GET['id']) || (isset($_GET['id']) && ($_GET['id'] == $skill_last_used['id']))) && !isset($cInfo) && (substr($action, 0, 3) != 'new')) 
  {
   $cInfo = new objectInfo($skill_last_used);
  }
  if ( (isset($cInfo) && is_object($cInfo)) && ($skill_last_used['id'] == $cInfo->id) ) 
  {
   $row_selected=' id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . FILENAME_ADMIN1_ADMIN_SKILL_LAST_USED . '?page='.$_GET['page'].'&id=' . $cInfo->id . '&action=edit\'"';
  } 
  else 
  {
   $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . FILENAME_ADMIN1_ADMIN_SKILL_LAST_USED . '?page='.$_GET['page'].'&id=' . $skill_last_used['id'] . '\'"';
  }
  $alternate++;
  if ( (isset($cInfo) && is_object($cInfo)) && ($skill_last_used['id'] == $cInfo->id) ) 
  { 
   $action_image=tep_image(PATH_TO_IMAGE.'icon_arrow_right.gif',IMAGE_EDIT); 
  } 
  else 
  { 
   $action_image='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_SKILL_LAST_USED, 'page='.$_GET['page'].'&id=' . $skill_last_used['id']) . '">'.tep_image(PATH_TO_IMAGE.'icon_info.gif',IMAGE_INFO).'</a>'; 
  }
  $template->assign_block_vars('skill_last_used', array( 'row_selected' => $row_selected,
   'action' => $action_image,
   'name' => tep_db_output($skill_last_used['skill_last_used']),
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
		$heading[] = array('text' => '<b>'.TEXT_INFO_HEADING_EDUCATION_LEVEL.'</b>');
  $contents = array('form' => tep_draw_form('skill_last_used', PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_SKILL_LAST_USED, 'action=insert','post',' onsubmit="return ValidateForm(this)"'));
		$contents[] = array('text' => TEXT_INFO_NEW_INTRO);
		$contents[] = array('text' => '<br>'.TEXT_INFO_EDUCATION_LEVEL_NAME.'<br>'.tep_draw_input_field('TR_skill_last_used', $_POST['TR_skill_last_used'], '' ));
		$contents[] = array('text' => '<br>'.TEXT_INFO_EDUCATION_LEVEL_PRIORITY.'<br>'.tep_draw_input_field('IN_priority', $_POST['IN_priority'], '' ));
		$contents[] = array('align' => 'center', 'text' => '<br>'.tep_image_submit(PATH_TO_BUTTON.'button_insert.gif', IMAGE_INSERT).'&nbsp;<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_SKILL_LAST_USED).'">'.tep_image_button(PATH_TO_BUTTON.'button_cancel.gif', IMAGE_CANCEL).'</a>');
  break;
 case 'edit':
  $value_field=tep_draw_input_field('TR_skill_last_used', $cInfo->skill_last_used, '' );
  $heading[] = array('text' => '<b>'.TEXT_INFO_HEADING_EDUCATION_LEVEL.'</b>');
  $contents = array('form' => tep_draw_form('skill_last_used', PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_SKILL_LAST_USED, 'id=' . $cInfo->id.'&page='.$_GET['page'].'&action=save','post',' onsubmit="return ValidateForm(this)"'));
		$contents[] = array('text' => TEXT_INFO_EDIT_INTRO);
		$contents[] = array('text' => '<br>'.TEXT_INFO_EDUCATION_LEVEL_NAME.'<br>'.tep_draw_input_field('TR_skill_last_used', $cInfo->skill_last_used, '' ));
		$contents[] = array('text' => '<br>'.TEXT_INFO_EDUCATION_LEVEL_PRIORITY.'<br>'.tep_draw_input_field('IN_priority', $cInfo->priority, '' ));
  $contents[] = array('align' => 'center', 'text' => '<br>'.tep_image_submit(PATH_TO_BUTTON.'button_update.gif',IMAGE_UPDATE).'&nbsp;<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_SKILL_LAST_USED, 'gid=' . $_GET['gid'] . '&id=' . $cInfo->id ). '">'.tep_image_button(PATH_TO_BUTTON.'button_cancel.gif',IMAGE_CANCEL).'</a>');
  break;
 case 'delete':
  $heading[] = array('text' => '<b>' . $cInfo->skill_last_used . '</b>');
  $contents = array('form' => tep_draw_form('skill_last_used_delete', PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_SKILL_LAST_USED, 'page=' . $_GET['page'] . '&id=' . $nInfo->id . '&action=deleteconfirm'));
  $contents[] = array('text' => TEXT_DELETE_INTRO);
  $contents[] = array('text' => '<br><b>' . $cInfo->skill_last_used . '</b>');
  $contents[] = array('align' => 'center', 'text' => '<br><a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_SKILL_LAST_USED, 'page=' . $_GET['page'] . '&id=' . $_GET['id'].'&action=confirm_delete') . '">'.tep_image_button(PATH_TO_BUTTON.'button_confirm.gif', IMAGE_CONFIRM).'</a>&nbsp;<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_SKILL_LAST_USED, 'page=' . $_GET['page'] . '&id=' . $_GET['id']) . '">' . tep_image_button(PATH_TO_BUTTON.'button_cancel.gif', IMAGE_CANCEL) . '</a>');
 break;
 default:
  if (isset($cInfo) && is_object($cInfo)) 
		{
   $heading[] = array('text' => '<b>'.TEXT_INFO_HEADING_EDUCATION_LEVEL.'</b>');
   $contents[] = array('text' => tep_db_output($cInfo->skill_last_used));
   $contents[] = array('align' => 'center', 'text' => '<br><a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_SKILL_LAST_USED, 'page=' . $_GET['page'] .'&id=' . $cInfo->id . '&action=edit') . '">'.tep_image_button(PATH_TO_BUTTON.'button_edit.gif',IMAGE_EDIT).'</a>&nbsp;<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_SKILL_LAST_USED, 'page=' . $_GET['page'] .'&id=' . $cInfo->id . '&action=delete') . '">'.tep_image_button(PATH_TO_BUTTON.'button_delete.gif',IMAGE_DELETE).'</a>');
   $contents[] = array('text' => '<br>'.TEXT_INFO_ACTION);
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
 'TABLE_HEADING_EDUCATION_LEVEL_NAME'=>TABLE_HEADING_EDUCATION_LEVEL_NAME,
 'TABLE_HEADING_ACTION'=>TABLE_HEADING_ACTION,
 'count_rows'=>$education_level_split->display_count($education_level_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_EDUCATION_LEVEL),
 'no_of_pages'=>$education_level_split->display_links($education_level_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']),
 'new_button'=>'<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_SKILL_LAST_USED, 'page=' . $_GET['page'] .'&action=new') . '">'.tep_image_button(PATH_TO_BUTTON.'button_new.gif',IMAGE_NEW).'</a>&nbsp;&nbsp;',
 'HEADING_TITLE'=>HEADING_TITLE,
 'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
 'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('skill_last_used');
?>