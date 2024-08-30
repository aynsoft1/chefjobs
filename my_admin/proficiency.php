<?
/*
***********************************************************
***********************************************************
**********# Name          : Shambhu Prasad Patnaik   #**********
**********# Company       : Aynsoft              #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
***********************************************************
***********************************************************
*/
include_once("../include_files.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_ADMIN_PROFICIENCY);
$template->set_filenames(array('proficiency' => 'proficiency.htm'));
include_once(FILENAME_ADMIN_BODY);

$action = (isset($_GET['action']) ? $_GET['action'] : '');

if ($action!="") 
{
 switch ($action) 
	{
  case 'confirm_delete':
   $id = tep_db_prepare_input($_GET['id']);
   tep_db_query("delete from " . LANGUAGE_PROFICIENCY_TABLE . " where id = '" . (int)$id . "'");
			$messageStack->add_session(MESSAGE_SUCCESS_DELETED, 'success');
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_PROFICIENCY, 'page=' . $_GET['page']));
  break;
  case 'insert':
  case 'save':
   $proficiency=tep_db_prepare_input($_POST['TR_Proficiency']);
 		$de_proficiency=tep_db_prepare_input($_POST['TR_de_Proficiency']);
   $priority = tep_db_prepare_input($_POST['IN_priority']);
   $sql_data_array['language_proficiency'] = $proficiency;
			$sql_data_array['de_language_proficiency'] = $de_proficiency;
   //$sql_data_array['priority'] = $priority;

			if($action=='insert')
			{
				if($row_chek=getAnyTableWhereData(LANGUAGE_PROFICIENCY_TABLE," language_proficiency='".tep_db_input($proficiency)."'",'id'))
				{
					$messageStack->add(MESSAGE_NAME_ERROR, 'error');
				}
				elseif($row_chek=getAnyTableWhereData(LANGUAGE_PROFICIENCY_TABLE," de_language_proficiency='".tep_db_input($de_proficiency)."'",'id'))
				{
					$messageStack->add(MESSAGE_FR_NAME_ERROR, 'error');
				}
				else
				{
     tep_db_perform(LANGUAGE_PROFICIENCY_TABLE, $sql_data_array);
     $row_id_check=getAnyTableWhereData(LANGUAGE_PROFICIENCY_TABLE,"1 order by id desc limit 0,1","id");
     $id = $row_id_check['id'];
  			$messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');
					tep_redirect(FILENAME_ADMIN1_ADMIN_PROFICIENCY);
				}
			}
			else
			{
    $id=(int)$_GET['id'];
				if($row_chek=getAnyTableWhereData(LANGUAGE_PROFICIENCY_TABLE,"language_proficiency='".tep_db_input($proficiency)."' and id!='$id'",'id'))
				{
					$messageStack->add(MESSAGE_NAME_ERROR, 'error');
					$action='edit';
				}
				else if($row_chek=getAnyTableWhereData(LANGUAGE_PROFICIENCY_TABLE,"de_language_proficiency='".tep_db_input($de_proficiency)."' and id!='$id'",'id'))
				{
					$messageStack->add(MESSAGE_FR_NAME_ERROR, 'error');
					$action='edit';
				}
				else
				{
     tep_db_perform(LANGUAGE_PROFICIENCY_TABLE, $sql_data_array, 'update', "id = '" . (int)$id . "'");
  			$messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
					tep_redirect(FILENAME_ADMIN1_ADMIN_PROFICIENCY.'?page='.$_GET['page'].'&id='.$id);
				}
			}
  break;
 }
}
///////////// Middle Values 
$proficiency_query_raw="select id, language_proficiency,de_language_proficiency from " . LANGUAGE_PROFICIENCY_TABLE ." order by language_proficiency";
$proficiency_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $proficiency_query_raw, $proficiency_query_numrows);
$proficiency_query = tep_db_query($proficiency_query_raw);
if(tep_db_num_rows($proficiency_query) > 0)
{
 $alternate=1;
 while ($row_proficiency = tep_db_fetch_array($proficiency_query)) 
 {
  if ((!isset($_GET['id']) || (isset($_GET['id']) && ($_GET['id'] == $row_proficiency['id']))) && !isset($cInfo) && (substr($action, 0, 3) != 'new')) 
  {
   $cInfo = new objectInfo($row_proficiency);
  }
  if ( (isset($cInfo) && is_object($cInfo)) && ($row_proficiency['id'] == $cInfo->id) ) 
  {
   $row_selected=' id="defaultSelected" class="table-secondary dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . FILENAME_ADMIN1_ADMIN_PROFICIENCY . '?page='.$_GET['page'].'&id=' . $cInfo->id . '&action=edit\'"';
  } 
  else 
  {
   $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . FILENAME_ADMIN1_ADMIN_PROFICIENCY . '?page='.$_GET['page'].'&id=' . $row_proficiency['id'] . '\'"';
  }
  $alternate++;
  if ( (isset($cInfo) && is_object($cInfo)) && ($row_proficiency['id'] == $cInfo->id) ) 
  { 
   $action_image=tep_image(PATH_TO_IMAGE.'icon_arrow_right.gif',IMAGE_EDIT); 
  } 
  else 
  { 
   $action_image='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_PROFICIENCY, 'page='.$_GET['page'].'&id=' . $row_proficiency['id']) . '">'.tep_image(PATH_TO_IMAGE.'icon_info.gif',IMAGE_INFO).'</a>'; 
  }
  $template->assign_block_vars('proficiency', array( 'row_selected' => $row_selected,
   'action' => $action_image,
   'name' => tep_db_output($row_proficiency['language_proficiency']),
   'de_name' => tep_db_output($row_proficiency['de_language_proficiency']),
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
  '.TEXT_INFO_HEADING_PROFICIENCY.'</div>
  </div>');
  $contents = array('form' => tep_draw_form('proficiency', PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_PROFICIENCY, 'action=insert','post',' onsubmit="return ValidateForm(this)"'));
  //$contents[] = array('text' => '<br>'.TEXT_INFO_PROFICIENCY_PRIORITY.'<br>'.tep_draw_input_field('IN_priority', $_POST['IN_priority'], '' ));

  $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
  <div class="mb-1 text-danger">'.TEXT_INFO_NEW_INTRO.'</div>
  <div class="form-group">
  <label>'.TEXT_INFO_PROFICIENCY_NAME.'</label>
  '.tep_draw_input_field('TR_Proficiency', $_POST['TR_Proficiency'], 'class="form-control form-control-sm"' ).'
  </div>
  <div class="form-group">
  <label>'.TEXT_INFO_FR_PROFICIENCY_NAME.'</label>
  '.tep_draw_input_field('TR_de_Proficiency', $_POST['TR_de_Proficiency'], 'class="form-control form-control-sm"' ).'
  </div>
  '.tep_draw_submit_button_field('',IMAGE_INSERT,'class="btn btn-primary"').'
  <a class="btn btn-secondary" href="' .tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_PROFICIENCY) . '">' 
  . IMAGE_CANCEL . '</a>
  </div>');
    break;
 case 'edit':
  $value_field=tep_draw_input_field('TR_Proficiency', $cInfo->language_proficiency, '' );
  $heading[] = array('text' => '<div class="list-group">
  <div class="font-weight-bold text-primary">
  '.TEXT_INFO_HEADING_PROFICIENCY.'</div>
  </div>');
  $contents = array('form' => tep_draw_form('proficiency', PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_PROFICIENCY, 'id=' . $cInfo->id.'&page='.$_GET['page'].'&action=save','post',' onsubmit="return ValidateForm(this)"'));
		//$contents[] = array('text' => '<br>'.TEXT_INFO_PROFICIENCY_PRIORITY.'<br>'.tep_draw_input_field('IN_priority', $cInfo->priority, '' ));
  $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
  <div class="mb-1 text-danger">'.TEXT_INFO_EDIT_INTRO.'</div>
  <div class="form-group">
  <label>'.TEXT_INFO_PROFICIENCY_NAME.'</label>
  '.tep_draw_input_field('TR_Proficiency', $cInfo->language_proficiency, 'class="form-control form-control-sm"' ).'
  </div>
  <div class="form-group">
  <label>'.TEXT_INFO_FR_PROFICIENCY_NAME.'</label>
  '.tep_draw_input_field('TR_de_Proficiency', $cInfo->de_language_proficiency,'class="form-control form-control-sm"').'
  </div>
  '.tep_draw_submit_button_field('',IMAGE_UPDATE,'class="btn btn-primary"').'
  <a class="btn btn-secondary" href="' .tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_PROFICIENCY, 'gid=' . $_GET['gid'] . '&id=' . $cInfo->id ) . '">' 
  . IMAGE_CANCEL . '</a>
  </div>');
  break;
 case 'delete':
  $heading[] = array('text' => '<div class="list-group">
  <div class="font-weight-bold">
  '.$cInfo->language_proficiency.'</div>
  </div>');
  $contents = array('form' => tep_draw_form('proficiency_delete', PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_PROFICIENCY, 'page=' . $_GET['page'] . '&id=' . $nInfo->id . '&action=deleteconfirm'));
  $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
  <div class="mb-1 font-weight-bold">'.TEXT_DELETE_INTRO.'
  <p>'.$cInfo->language_proficiency.'</p></div>
  <a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_PROFICIENCY, 'page=' . $_GET['page'] . '&id=' . $_GET['id'].'&action=confirm_delete') . '">' 
  . IMAGE_CONFIRM . '</a>
  <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_PROFICIENCY, 'page=' . $_GET['page'] . '&id=' . $_GET['id']) . '">' 
  . IMAGE_CANCEL . '</a>
  </div>');
 break;
 default:
  if (isset($cInfo) && is_object($cInfo)) 
		{
   $heading[] = array('text' => '<div class="list-group"><div class="font-weight-bold text-primary">'.TEXT_INFO_HEADING_PROFICIENCY.'</div></div>');
   $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
   <div class="mb-1 text-danger">'.tep_db_output($cInfo->language_proficiency).'<strong class="d-block">'.TEXT_INFO_ACTION.'</strong></div>
   <a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_PROFICIENCY, 'page=' . $_GET['page'] .'&id=' . $cInfo->id . '&action=edit') . '">'
   .IMAGE_EDIT.'</a>
   <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_PROFICIENCY, 'page=' . $_GET['page'] .'&id=' . $cInfo->id . '&action=delete') . '">'
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
 'TABLE_HEADING_PROFICIENCY_NAME'=>TABLE_HEADING_PROFICIENCY_NAME,
 'TABLE_HEADING_FR_PROFICIENCY_NAME'=>TABLE_HEADING_FR_PROFICIENCY_NAME,
 'TABLE_HEADING_ACTION'=>TABLE_HEADING_ACTION,
 'count_rows'=>$proficiency_split->display_count($proficiency_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PROFICIENCY),
 'no_of_pages'=>$proficiency_split->display_links($proficiency_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']),
 'new_button'=>'<a class="btn btn-primary float-right" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_PROFICIENCY, 'page=' . $_GET['page'] .'&action=new') . '"><i class="bi bi-plus-lg me-2"></i>'.IMAGE_NEW.'</a>',
 'HEADING_TITLE'=>HEADING_TITLE,
 'RIGHT_BOX_WIDTH'=>RIGHT_BOX_WIDTH,
 'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('proficiency');
?>