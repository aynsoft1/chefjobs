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
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_ADMIN_JOBSEEKER_LANGUAGE);
$template->set_filenames(array('language_level' => 'jobseeker_language.htm'));
include_once(FILENAME_ADMIN_BODY);

$action = (isset($_GET['action']) ? $_GET['action'] : '');

if ($action!="") 
{
 switch ($action) 
	{
  case 'confirm_delete':
   $languages_id = tep_db_prepare_input($_GET['languages_id']);
   tep_db_query("delete from " . JOBSEEKER_LANGUAGE_TABLE . " where languages_id = '" . (int)$languages_id . "'");
			$messageStack->add_session(MESSAGE_SUCCESS_DELETED, 'success');
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_JOBSEEKER_LANGUAGE, 'page=' . $_GET['page']));
  break;
  case 'insert':
  case 'save':
   $language_name=tep_db_prepare_input($_POST['TR_language_name']);
   $de_language_name=tep_db_prepare_input($_POST['TR_de_language_name']);
   $priority = tep_db_prepare_input($_POST['IN_priority']);
   $sql_data_array['name'] = $language_name;
			$sql_data_array['de_name'] = $de_language_name;
   $sql_data_array['priority'] = $priority;

			if($action=='insert')
			{
				if($row_chek=getAnyTableWhereData(JOBSEEKER_LANGUAGE_TABLE," name='".tep_db_input($language_name)."'",'languages_id'))
				{
					$messageStack->add(MESSAGE_NAME_ERROR, 'error');
				}
				else if($row_chek=getAnyTableWhereData(JOBSEEKER_LANGUAGE_TABLE," de_name='".tep_db_input($de_language_name)."'",'languages_id'))
				{
					$messageStack->add(MESSAGE_FR_NAME_ERROR, 'error');
				}
				else
				{
     tep_db_perform(JOBSEEKER_LANGUAGE_TABLE, $sql_data_array);
     $row_id_check=getAnyTableWhereData(JOBSEEKER_LANGUAGE_TABLE,"1 order by languages_id desc limit 0,1","languages_id");
     $languages_id = $row_id_check['languages_id'];
  			$messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');
					tep_redirect(FILENAME_ADMIN1_ADMIN_JOBSEEKER_LANGUAGE);
				}
			}
			else
			{
    $languages_id=(int)$_GET['languages_id'];
				if($row_chek=getAnyTableWhereData(JOBSEEKER_LANGUAGE_TABLE," name='".tep_db_input($language_name)."' and languages_id!='$languages_id'",'languages_id'))
				{
					$messageStack->add(MESSAGE_NAME_ERROR, 'error');
					$action='edit';
				}
				else if($row_chek=getAnyTableWhereData(JOBSEEKER_LANGUAGE_TABLE," de_name='".tep_db_input($de_language_name)."' and languages_id!='$languages_id'",'languages_id'))
				{
					$messageStack->add(MESSAGE_FR_NAME_ERROR, 'error');
					$action='edit';
				}
				else
				{
     tep_db_perform(JOBSEEKER_LANGUAGE_TABLE, $sql_data_array, 'update', "languages_id = '" . (int)$languages_id . "'");
  			$messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
					tep_redirect(FILENAME_ADMIN1_ADMIN_JOBSEEKER_LANGUAGE.'?page='.$_GET['page'].'&languages_id='.$languages_id);
				}
			}
  break;
 }
}
///////////// Middle Values 
$language_level_query_raw="select languages_id, name,de_name,priority from " . JOBSEEKER_LANGUAGE_TABLE ." order by priority";
$language_level_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $language_level_query_raw, $language_level_query_numrows);
$language_level_query = tep_db_query($language_level_query_raw);
if(tep_db_num_rows($language_level_query) > 0)
{
 $alternate=1;
 while ($language_level = tep_db_fetch_array($language_level_query)) 
 {
  if ((!isset($_GET['languages_id']) || (isset($_GET['languages_id']) && ($_GET['languages_id'] == $language_level['languages_id']))) && !isset($cInfo) && (substr($action, 0, 3) != 'new')) 
  {
   $cInfo = new objectInfo($language_level);
  }
  if ( (isset($cInfo) && is_object($cInfo)) && ($language_level['languages_id'] == $cInfo->languages_id) ) 
  {
   $row_selected='languages_id="defaultSelected" class="table-secondary dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . FILENAME_ADMIN1_ADMIN_JOBSEEKER_LANGUAGE . '?page='.$_GET['page'].'&languages_id=' . $cInfo->languages_id . '&action=edit\'"';
  } 
  else 
  {
   $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . FILENAME_ADMIN1_ADMIN_JOBSEEKER_LANGUAGE . '?page='.$_GET['page'].'&languages_id=' . $language_level['languages_id'] . '\'"';
  }
  $alternate++;
  if ( (isset($cInfo) && is_object($cInfo)) && ($language_level['languages_id'] == $cInfo->languages_id) ) 
  { 
   $action_image=tep_image(PATH_TO_IMAGE.'icon_arrow_right.gif',IMAGE_EDIT); 
  } 
  else 
  { 
   $action_image='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_JOBSEEKER_LANGUAGE, 'page='.$_GET['page'].'&languages_id=' . $language_level['languages_id']) . '">'.tep_image(PATH_TO_IMAGE.'icon_info.gif',IMAGE_INFO).'</a>'; 
  }
  $template->assign_block_vars('language_level', array( 'row_selected' => $row_selected,
   'action' => $action_image,
   'name' => tep_db_output($language_level['name']),
   'de_name' => tep_db_output($language_level['de_name']),
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
    '.TEXT_INFO_HEADING_LANGUAGE.'</div>
    </div>');
  $contents = array('form' => tep_draw_form('language_level', PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_JOBSEEKER_LANGUAGE, 'action=insert','post',' onsubmit="return ValidateForm(this)"'));

    $contents = array('form' => tep_draw_form('job_category', PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_JOB_CATEGORY, 'action=insert','post',' onsubmit="return ValidateForm(this)"'));
    $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
    <div class="mb-1 text-danger">'.TEXT_INFO_NEW_INTRO.'</div>
    <div class="form-group">
    <label>'.TEXT_INFO_LANGUAGE_NAME.'</label>
    '.tep_draw_input_field('TR_language_name', $_POST['TR_language_name'], 'class="form-control form-control-sm"' ).'
    </div>
    <div class="form-group">
    <label>'.TEXT_INFO_FR_LANGUAGE_NAME.'</label>
    '.tep_draw_input_field('TR_de_language_name', $_POST['TR_de_language_name'], 'class="form-control form-control-sm"' ).'
    </div>
    <div class="form-group">
    <label>'.TEXT_INFO_LANGUAGE_PRIORITY.'</label>
    '.tep_draw_input_field('IN_priority', $_POST['IN_priority'], 'class="form-control form-control-sm"' ).'
    </div>
    '.tep_draw_submit_button_field('',IMAGE_INSERT,'class="btn btn-primary"').'
    <a class="btn btn-secondary" href="' .tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_JOBSEEKER_LANGUAGE) . '">' 
    . IMAGE_CANCEL . '</a>
    </div>');
  break;
 case 'edit':
  $value_field=tep_draw_input_field('TR_language_name', $cInfo->name, '' );
  $heading[] = array('text' => '<div class="list-group">
    <div class="font-weight-bold text-primary">
    '.TEXT_INFO_LANGUAGE_NAME.'</div>
    </div>');
  $contents = array('form' => tep_draw_form('language_level', PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_JOBSEEKER_LANGUAGE, 'languages_id=' . $cInfo->languages_id.'&page='.$_GET['page'].'&action=save','post',' onsubmit="return ValidateForm(this)"'));
  $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
  <div class="mb-1 text-danger">'.TEXT_INFO_EDIT_INTRO.'</div>
  <div class="form-group">
  <label>'.TEXT_INFO_LANGUAGE_NAME.'</label>
  '.tep_draw_input_field('TR_language_name', $cInfo->name, 'class="form-control form-control-sm"' ).'
  </div>
  <div class="form-group">
  <label>'.TEXT_INFO_FR_LANGUAGE_NAME.'</label>
  '.tep_draw_input_field('TR_de_language_name', $cInfo->de_name, 'class="form-control form-control-sm"' ).'
  </div>
  <div class="form-group">
  <label>'.TEXT_INFO_LANGUAGE_PRIORITY.'</label>
  '.tep_draw_input_field('IN_priority', $cInfo->priority, 'class="form-control form-control-sm"' ).'
  </div>
  '.tep_draw_submit_button_field('',IMAGE_UPDATE,'class="btn btn-primary"').'
  <a class="btn btn-secondary" href="' .tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_JOBSEEKER_LANGUAGE, 'gid=' . $_GET['gid'] . '&languages_id=' . $cInfo->languages_id ) . '">' 
  . IMAGE_CANCEL . '</a>
  </div>');
  break;
 case 'delete':
  $heading[] = array('text' => '<div class="list-group">
  <div class="font-weight-bold">
  '.$cInfo->name.'</div>
  </div>');
  $contents = array('form' => tep_draw_form('language_level_delete', PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_JOBSEEKER_LANGUAGE, 'page=' . $_GET['page'] . '&languages_id=' . $cInfo->languages_id . '&action=deleteconfirm'));
  $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
  <div class="mb-1 font-weight-bold">'.TEXT_DELETE_INTRO.'
  <p>'.$cInfo->name.'</p></div>
  <a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_JOBSEEKER_LANGUAGE, 'page=' . $_GET['page'] . '&languages_id=' . $_GET['languages_id'].'&action=confirm_delete') . '">' 
  . IMAGE_CONFIRM . '</a>
  <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_JOBSEEKER_LANGUAGE, 'page=' . $_GET['page'] . '&languages_id=' . $_GET['languages_id']) . '">' 
  . IMAGE_CANCEL . '</a>
  </div>');
 break;
 default:
  if (isset($cInfo) && is_object($cInfo)) 
		{

  $heading[] = array('text' => '<div class="list-group"><div class="font-weight-bold  text-primary">'.TEXT_INFO_HEADING_LANGUAGE.'</div></div>');
  $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
  <div class="mb-1 text-danger">'.tep_db_output($cInfo->name).'<strong class="d-block">'.TEXT_INFO_ACTION.'</strong></div>
  <a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_JOBSEEKER_LANGUAGE, 'page=' . $_GET['page'] .'&languages_id=' . $cInfo->languages_id . '&action=edit') . '">'
  .IMAGE_EDIT.'</a>
  <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_JOBSEEKER_LANGUAGE, 'page=' . $_GET['page'] .'&languages_id=' . $cInfo->languages_id . '&action=delete') . '">'
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
 'TABLE_HEADING_FR_LANGUAGE_NAME'=>TABLE_HEADING_FR_LANGUAGE_NAME,
 'TABLE_HEADING_LANGUAGE_NAME'=>TABLE_HEADING_LANGUAGE_NAME,
 'TABLE_HEADING_ACTION'=>TABLE_HEADING_ACTION,
 'count_rows'=>$language_level_split->display_count($language_level_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_JOBSEEKER_LANGUAGE),
 'no_of_pages'=>$language_level_split->display_links($language_level_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']),
 'new_button'=>'<a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_JOBSEEKER_LANGUAGE, 'page=' . $_GET['page'] .'&action=new') . '"><i class="bi bi-plus-lg me-2"></i>'.IMAGE_NEW.'</a>',
 'HEADING_TITLE'=>HEADING_TITLE,
 'RIGHT_BOX_WIDTH'=>RIGHT_BOX_WIDTH,
 'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('language_level');
?>