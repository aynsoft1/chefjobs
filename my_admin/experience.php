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
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_ADMIN_EXPERIENCE);
$template->set_filenames(array('experience' => 'experience.htm'));
include_once(FILENAME_ADMIN_BODY);

$action = (isset($_GET['action']) ? $_GET['action'] : '');

if ($action!="") 
{
 switch ($action) 
	{
  case 'confirm_delete':
   $id = tep_db_prepare_input($_GET['id']);
   tep_db_query("delete from " . EXPERIENCE_TABLE . " where id = '" . (int)$id . "'");
   set_experience_weight();
			$messageStack->add_session(MESSAGE_SUCCESS_DELETED, 'success');
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_EXPERIENCE, 'page=' . $_GET['page']));
  break;
  case 'insert':
  case 'save':
   $min_experience=tep_db_prepare_input($_POST['TR_min_experience']);
   $max_experience=tep_db_prepare_input($_POST['TR_max_experience']);
   $priority = tep_db_prepare_input($_POST['IN_priority']);
   $sql_data_array['min_experience'] = $min_experience;
   $sql_data_array['max_experience'] = $max_experience;
   $sql_data_array['priority'] = $priority;

			if($action=='insert')
			{
				if($row_chek=getAnyTableWhereData(EXPERIENCE_TABLE,"min_experience='".tep_db_input($min_experience)."'",'id'))
				{
					$messageStack->add(MESSAGE_NAME_ERROR, 'error');
				}
				else
				{
     tep_db_perform(EXPERIENCE_TABLE, $sql_data_array);
     set_experience_weight();
     $row_id_check=getAnyTableWhereData(EXPERIENCE_TABLE,"1 order by id desc limit 0,1","id");
     $id = $row_id_check['id'];
  			$messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');
					tep_redirect(FILENAME_ADMIN1_ADMIN_EXPERIENCE);
				}
			}
			else
			{
    $id=(int)$_GET['id'];
				if($row_chek=getAnyTableWhereData(EXPERIENCE_TABLE,"min_experience='".tep_db_input($min_experience)."' and id!='$id'",'id'))
				{
					$messageStack->add(MESSAGE_NAME_ERROR, 'error');
				}
				else
				{
     tep_db_perform(EXPERIENCE_TABLE, $sql_data_array, 'update', "id = '" . (int)$id . "'");
     set_experience_weight();
  			$messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
					tep_redirect(FILENAME_ADMIN1_ADMIN_EXPERIENCE.'?page='.$_GET['page'].'&id='.$id);
				}
			}
  break;
 }
}
function set_experience_weight()
{
 $query  = "select id from ".EXPERIENCE_TABLE."  order by min_experience asc ";
 $query_result =tep_db_query($query);
 $weight=1;
 if(tep_db_num_rows($query_result)>0)
 {
  while($row1=tep_db_fetch_array($query_result))
  {
   tep_db_query("update ".EXPERIENCE_TABLE." set experience_weight='".$weight."' where id ='".$row1['id']."'");
   $weight++;
  }
 }
 tep_db_free_result($query_result);
}
///////////// Middle Values 
$experience_query_raw="select id, min_experience, max_experience, priority from " . EXPERIENCE_TABLE ." order by priority";
$experience_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $experience_query_raw, $experience_query_numrows);
$experience_query = tep_db_query($experience_query_raw);
if(tep_db_num_rows($experience_query) > 0)
{
 $alternate=1;
 while ($experience = tep_db_fetch_array($experience_query)) 
 {
  if ((!isset($_GET['id']) || (isset($_GET['id']) && ($_GET['id'] == $experience['id']))) && !isset($cInfo) && (substr($action, 0, 3) != 'new')) 
  {
   $cInfo = new objectInfo($experience);
  }
  if ( (isset($cInfo) && is_object($cInfo)) && ($experience['id'] == $cInfo->id) ) 
  {
   $row_selected=' id="defaultSelected" class="table-secondary dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . FILENAME_ADMIN1_ADMIN_EXPERIENCE . '?page='.$_GET['page'].'&id=' . $cInfo->id . '&action=edit\'"';
  } 
  else 
  {
   $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . FILENAME_ADMIN1_ADMIN_EXPERIENCE . '?page='.$_GET['page'].'&id=' . $experience['id'] . '\'"';
  }
  $alternate++;
  if ( (isset($cInfo) && is_object($cInfo)) && ($experience['id'] == $cInfo->id) ) 
  { 
   $action_image=tep_image(PATH_TO_IMAGE.'icon_arrow_right.gif',IMAGE_EDIT); 
  } 
  else 
  { 
   $action_image='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_EXPERIENCE, 'page='.$_GET['page'].'&id=' . $experience['id']) . '">'.tep_image(PATH_TO_IMAGE.'icon_info.gif',IMAGE_INFO).'</a>'; 
  }
  $minage=tep_db_output($experience['min_experience']);
  $minage=($minage >=12?(($minage/12) > 1?($minage/12).' years':($minage/12)." year"):($minage>1?$minage.' months':$minage.' month'));
  $maxage=tep_db_output($experience['max_experience']);
  if($minage*12==(int)$maxage)
   $maxage=' Plus'; 
  else
   $maxage=($maxage >=12?(($maxage/12) > 1?($maxage/12).' years':($maxage/12)." year"):($maxage>1?$maxage.' months':$maxage.' month'));

  $template->assign_block_vars('experience', array( 'row_selected' => $row_selected,
   'action' => $action_image,
   'name' => $minage.' - '.$maxage,
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
  '.TEXT_INFO_HEADING_EXPERIENCE.'</div>
  </div>');
  $contents = array('form' => tep_draw_form('experience', PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_EXPERIENCE, 'action=insert','post',' onsubmit="return ValidateForm(this)"'));
  
  $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
  <div class="mb-1 text-danger">'.TEXT_INFO_NEW_INTRO.'</div>
  <div class="form-group">
  <label>'.TEXT_INFO_MIN_AGE.'</label>
  '.tep_draw_input_field('TR_min_experience', $_POST['TR_min_experience'], 'class="form-control form-control-sm"' ).'
  </div>
  <div class="form-group">
  <label>'.TEXT_INFO_MAX_AGE.'</label>
  '.tep_draw_input_field('TR_max_experience', $_POST['TR_max_experience'], 'class="form-control form-control-sm"' ).'
  </div>
  <div class="form-group">
  <label>'.TEXT_INFO_EXPERIENCE_PRIORITY.'</label>
  '.tep_draw_input_field('IN_priority', $_POST['IN_priority'], 'class="form-control form-control-sm"' ).'
  </div>
  '.tep_draw_submit_button_field('',IMAGE_INSERT,'class="btn btn-primary"').'
  <a class="btn btn-secondary" href="' .tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_EXPERIENCE) . '">' 
  . IMAGE_CANCEL . '</a>
  </div>');
  
    break;
 case 'edit':
  $value_field=tep_draw_input_field('TR_min_experience', $cInfo->min_experience, '' );
  $heading[] = array('text' => '<div class="list-group">
  <div class="font-weight-bold text-primary">
  '.TEXT_INFO_HEADING_EXPERIENCE.'</div>
  </div>');
  $contents = array('form' => tep_draw_form('experience', PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_EXPERIENCE, 'id=' . $cInfo->id.'&page='.$_GET['page'].'&action=save','post',' onsubmit="return ValidateForm(this)"'));

  $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
  <div class="mb-1 text-danger">'.TEXT_INFO_EDIT_INTRO.'</div>
  <div class="form-group">
  <label>'.TEXT_INFO_MIN_AGE.'</label>
  '.tep_draw_input_field('TR_min_experience', $cInfo->min_experience, 'class="form-control form-control-sm"' ).'
  </div>
  <div class="form-group">
  <label>'.TEXT_INFO_MAX_AGE.'</label>
  '.tep_draw_input_field('TR_max_experience', $cInfo->max_experience, 'class="form-control form-control-sm"' ).'
  </div>
  <div class="form-group">
  <label>'.TEXT_INFO_EXPERIENCE_PRIORITY.'</label>
  '.tep_draw_input_field('IN_priority', $cInfo->priority, 'class="form-control form-control-sm"' ).'
  </div>
  '.tep_draw_submit_button_field('',IMAGE_UPDATE,'class="btn btn-primary"').'
  <a class="btn btn-secondary" href="' .tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_EXPERIENCE, 'gid=' . $_GET['gid'] . '&id=' . $cInfo->id ) . '">' 
  . IMAGE_CANCEL . '</a>
  </div>');
  
  break;
 case 'delete':
   $minage=tep_db_output($cInfo->min_experience);
   $minage=($minage >=12?(($minage/12) > 1?($minage/12).' years':($minage/12)." year"):($minage>1?$minage.' months':$minage.' month'));
   $maxage=tep_db_output($cInfo->max_experience);
   if($minage*12==(int)$maxage)
    $maxage=' Plus'; 
   else
    $maxage=($maxage >=12?(($maxage/12) > 1?($maxage/12).' years':($maxage/12)." year"):($maxage>1?$maxage.' months':$maxage.' month'));
  $heading[] = array('text' => '<div class="list-group"><div class="font-weight-bold">'.$minage.' - '.$maxage.'</div></div>');
  $contents = array('form' => tep_draw_form('experience_delete', PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_EXPERIENCE, 'page=' . $_GET['page'] . '&id=' . $nInfo->id . '&action=deleteconfirm'));
  $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
  <div class="mb-1">'.$minage.' - '.$maxage.'<strong class="d-block">'.TEXT_DELETE_INTRO.'</strong></div>
  <a class="btn btn-primary" href="' .  tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_EXPERIENCE, 'page=' . $_GET['page'] . '&id=' . $_GET['id'].'&action=confirm_delete') . '">'
  .IMAGE_CONFIRM.'</a>
  <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_EXPERIENCE, 'page=' . $_GET['page'] . '&id=' . $_GET['id']) . '">'
  .IMAGE_CANCEL.'</a>
  </div>');
 break;
 default:
  if (isset($cInfo) && is_object($cInfo)) 
		{
   $minage=tep_db_output($cInfo->min_experience);
   $minage=($minage >=12?(($minage/12) > 1?($minage/12).' years':($minage/12)." year"):($minage>1?$minage.' months':$minage.' month'));
   $maxage=tep_db_output($cInfo->max_experience);
   if($minage*12==(int)$maxage)
    $maxage=' Plus'; 
   else
    $maxage=($maxage >=12?(($maxage/12) > 1?($maxage/12).' years':($maxage/12)." year"):($maxage>1?$maxage.' months':$maxage.' month'));
   $heading[] = array('text' => '<div class="list-group"><div class="font-weight-bold text-primary">'.TEXT_INFO_HEADING_EXPERIENCE.'</div></div>');
   $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
   <div class="mb-1 text-danger">'.$minage.' - '.$maxage.'<strong class="d-block">'.TEXT_INFO_ACTION.'</strong></div>
   <a class="btn btn-primary" href="' .  tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_EXPERIENCE, 'page=' . $_GET['page'] .'&id=' . $cInfo->id . '&action=edit') . '">'
   .IMAGE_EDIT.'</a>
   <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_EXPERIENCE, 'page=' . $_GET['page'] .'&id=' . $cInfo->id . '&action=delete') . '">'
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
 'TABLE_HEADING_EXPERIENCE_NAME'=>TABLE_HEADING_EXPERIENCE_NAME,
 'TABLE_HEADING_ACTION'=>TABLE_HEADING_ACTION,
 'count_rows'=>$experience_split->display_count($experience_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_EXPERIENCES),
 'no_of_pages'=>$experience_split->display_links($experience_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']),
 'new_button'=>'<a class="btn btn-primary float-right" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_EXPERIENCE, 'page=' . $_GET['page'] .'&action=new') . '"><i class="bi bi-plus-lg me-2"></i>'.IMAGE_NEW.'</a>',
 'HEADING_TITLE'=>HEADING_TITLE,
 'RIGHT_BOX_WIDTH'=>RIGHT_BOX_WIDTH,
 'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('experience');
?>