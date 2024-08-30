<?
include_once("../include_files.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_ADMIN_SKILL_TAGS);
$template->set_filenames(array('skill_tag' => 'admin1_skill_tags.htm'));
include_once(FILENAME_ADMIN_BODY);

$action = (isset($_GET['action']) ? $_GET['action'] : '');
$skill_status1=tep_db_prepare_input($_GET['skill_status']);

if ($action!="")
{
 switch ($action)
	{
  case 'confirm_delete':
   $id = tep_db_prepare_input($_GET['id']);
   tep_db_query("delete from " . SKILL_TAGS_TABLE . " where id = '" . (int)$id . "'");
			$messageStack->add_session(MESSAGE_SUCCESS_DELETED, 'success');
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_SKILL_TAGS, 'page=' . $_GET['page']));
  case 'tag_active':
  case 'tag_inactive':
   $id = tep_db_prepare_input($_GET['id']);
   tep_db_query("update ".SKILL_TAGS_TABLE ." set status='".($action=='tag_active'?'active':'inactive')."' where id='".$id."'");
   $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_SKILL_TAGS,tep_get_all_get_params(array('action','selected_box'))));
  case 'insert':
  case 'save':
   $skill_tag_key=tep_db_prepare_input($_POST['TR_skill_tag_key']);
   $skill_tag_key = preg_replace("'[\s]+'", " ", $skill_tag_key);
   $status = tep_db_prepare_input($_POST['status']);
   $sql_data_array['tag'] = $skill_tag_key;
   $sql_data_array['status'] = $status;
   $error=false;
   if($action=='insert')
   {
	if($row_chek=getAnyTableWhereData(SKILL_TAGS_TABLE,"tag='".tep_db_input($skill_tag_key)."'",'id'))
	{
	 $messageStack->add(MESSAGE_NAME_ERROR, 'error');
	}
	else
	{
     tep_db_perform(SKILL_TAGS_TABLE, $sql_data_array);
	 $messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');
	 tep_redirect(FILENAME_ADMIN1_ADMIN_SKILL_TAGS);
	}
   }
   else
   {
    $id=(int)$_GET['id'];
				if($row_chek=getAnyTableWhereData(SKILL_TAGS_TABLE,"tag='".tep_db_input($skill_tag_key)."' and id!='$id'",'id'))
				{
					$messageStack->add(MESSAGE_NAME_ERROR, 'error');
				}
				else
				{
     tep_db_perform(SKILL_TAGS_TABLE, $sql_data_array, 'update', "id = '" . (int)$id . "'");
  			$messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
					tep_redirect(FILENAME_ADMIN1_ADMIN_SKILL_TAGS.'?page='.$_GET['page'].'&id='.$id);
				}
			}
  break;
 }
}
///////////// Middle Values
$sort_array=array("tag","status");
include_once(PATH_TO_MAIN_PHYSICAL_CLASS.'sort_by_clause.php');
$obj_sort_by_clause=new sort_by_clause($sort_array,'id desc');
$order_by_clause=$obj_sort_by_clause->return_value;

if(tep_not_null($skill_status1))
{
 if($skill_status1=='active')
 {
  $skill_tag_query_raw="select id,tag, status from " . SKILL_TAGS_TABLE ." where status='active' order by ".$order_by_clause;
 }
 elseif($skill_status1=='inactive')
 {
  $skill_tag_query_raw="select id,tag, status from " . SKILL_TAGS_TABLE ." where status='inactive'  order by ".$order_by_clause;
 }
}
else
$skill_tag_query_raw="select id,tag, status from " . SKILL_TAGS_TABLE ." order by ".$order_by_clause;
$skill_tag_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $skill_tag_query_raw, $skill_tag_query_numrows);
$skill_tag_query = tep_db_query($skill_tag_query_raw);
if(tep_db_num_rows($skill_tag_query) > 0)
{
 $alternate=1;
 while ($skill_tag = tep_db_fetch_array($skill_tag_query))
 {
  if ((!isset($_GET['id']) || (isset($_GET['id']) && ($_GET['id'] == $skill_tag['id']))) && !isset($cInfo) && (substr($action, 0, 3) != 'new'))
  {
   $cInfo = new objectInfo($skill_tag);
  }
  if ( (isset($cInfo) && is_object($cInfo)) && ($skill_tag['id'] == $cInfo->id) )
  {
   $row_selected=' id="defaultSelected" class="table-secondary dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . FILENAME_ADMIN1_ADMIN_SKILL_TAGS . '?page='.$_GET['page'].'&id=' . $cInfo->id . '&action=edit\'"';
  }
  else
  {
   $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . FILENAME_ADMIN1_ADMIN_SKILL_TAGS . '?page='.$_GET['page'].'&id=' . $skill_tag['id'] . '\'"';
  }
  $alternate++;
  if ( (isset($cInfo) && is_object($cInfo)) && ($skill_tag['id'] == $cInfo->id) )
  {
   $action_image=tep_image(PATH_TO_IMAGE.'icon_arrow_right.gif',IMAGE_EDIT);
  }
  else
  {
   $action_image='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_SKILL_TAGS, 'page='.$_GET['page'].'&id=' . $skill_tag['id']) . '">'.tep_image(PATH_TO_IMAGE.'icon_info.gif',IMAGE_INFO).'</a>';
  }

  if ($skill_tag['status'] == 'active')
  {
   $status='<a href="'.tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_SKILL_TAGS, tep_get_all_get_params(array('id','action','selected_box'))).'&id='.$skill_tag['id'].'&action=tag_inactive' . '">' . tep_image(PATH_TO_IMAGE.'icon_status_red_light.gif', STATUS_TAG_INACTIVATE, 28, 22) . '</a>' . tep_image(PATH_TO_IMAGE.'icon_status_green.gif', STATUS_TAG_ACTIVE, 28, 22);
  }
  else
  {
   $status=tep_image(PATH_TO_IMAGE.'icon_status_red.gif',STATUS_TAG_INACTIVE, 28, 22) . '<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_SKILL_TAGS, tep_get_all_get_params(array('id','action','selected_box'))).'&id='.$skill_tag['id'].'&action=tag_active' . '">' . tep_image(PATH_TO_IMAGE.'icon_status_green_light.gif', STATUS_TAG_ACTIVATE, 28, 22) . '</a>';
  }

  $template->assign_block_vars('skill_tag', array( 'row_selected' => $row_selected,
   'action' => $action_image,
   'name' => tep_db_output($skill_tag['tag']),
   'status' => $status,
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
  $heading[] = array('text' => '<div class="text-primary font-weight-bold mb-1">'.TEXT_INFO_HEADING_SKILL_TAG.'</div>');
  $contents = array('form' => tep_draw_form('skill_tag', PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_SKILL_TAGS, 'action=insert','post',' onsubmit="return ValidateForm(this)"'));
  $contents[] = array('text' =>'<div class="mb-1 text-danger">' . TEXT_INFO_NEW_INTRO. '</div>');
  $contents[] = array('text' => '<br>'.TEXT_INFO_SKILL_TAG_NAME.'<br>'.tep_draw_input_field('TR_skill_tag_key', $_POST['TR_skill_tag_key'], 'class="form-control form-control-sm"' ));
  $contents[] = array('text' => '<br>'.TEXT_INFO_STATUS.'<br>'.tep_draw_radio_field("status", 'active',true,$status).'&nbsp; Active &nbsp;'.tep_draw_radio_field("status", 'inactive',false,$status).'&nbsp;Inactive');
  $contents[] = array('align' => 'left', 'text' => '<br>
  
  '.tep_draw_submit_button_field('', IMAGE_INSERT,'class="btn btn-primary"').'
  <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_SKILL_TAGS).'">'.IMAGE_CANCEL.'</a>');
  break;
 case 'edit':
  $value_field=tep_draw_input_field('TR_skill_tag_key', $cInfo->tag, '' );
  $heading[] = array('text' => '<div class="text-primary font-weight-bold mb-1">'.TEXT_INFO_HEADING_SKILL_TAG.'</div>');
  $contents = array('form' => tep_draw_form('skill_tag', PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_SKILL_TAGS, 'id=' . $cInfo->id.'&page='.$_GET['page'].'&action=save','post',' onsubmit="return ValidateForm(this)"'));
  $contents[] = array('text' =>'<div class="mb-1 text-danger">' .TEXT_INFO_EDIT_INTRO. '</div>');
  $contents[] = array('text' => '<br>'.TEXT_INFO_SKILL_TAG_NAME.'<br>'.tep_draw_input_field('TR_skill_tag_key', $cInfo->tag, 'class="form-control form-control-sm"' ));
  $contents[] = array('text' => '<br>'.TEXT_INFO_STATUS.'<br>'.tep_draw_radio_field("status", 'active', false,isset($_POST['status'])?$status:$cInfo->status).'&nbsp; Active &nbsp;'.tep_draw_radio_field("status", 'inactive',false,isset($_POST['status'])?$status:$cInfo->status).'&nbsp; Inactive');
  $contents[] = array('align' => 'left', 'text' => '<br>

  '.tep_draw_submit_button_field('', IMAGE_UPDATE,'class="btn btn-primary"').'
  <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_SKILL_TAGS, 'gid=' . $_GET['gid'] . '&id=' . $cInfo->id ). '">'.IMAGE_CANCEL.'</a>');
  break;
 case 'delete':
  $heading[] = array('text' => '<div class="text-primary font-weight-bold mb-1">' . $cInfo->tag . '</div>');
  $contents = array('form' => tep_draw_form('skill_tag_delete', PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_SKILL_TAGS, 'page=' . $_GET['page'] . '&id=' . $nInfo->id . '&action=deleteconfirm'));
  $contents[] = array('text' =>'<div class="mb-1 text-danger">' . TEXT_DELETE_INTRO. '</div>');
  $contents[] = array('text' => '<br><b>' . $cInfo->tag . '</b>');
  $contents[] = array('align' => 'left', 'text' => '
  <table>
   <tr>
   <td>       
   <a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_SKILL_TAGS, 'page=' . $_GET['page'] . '&id=' . $_GET['id'].'&action=confirm_delete') . '">'
   .IMAGE_CONFIRM.'</a>
   </td>
   <td>
   <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_SKILL_TAGS, 'page=' . $_GET['page'] . '&id=' . $_GET['id']) . '">' 
   . IMAGE_CANCEL . '</a>
   </td>
   </tr>
   </table>
  ');
 break;
 default:
  if (isset($cInfo) && is_object($cInfo))
		{
   $heading[] = array('text' => '<div class="text-primary font-weight-bold mb-1">'.TEXT_INFO_HEADING_SKILL_TAG.'</div>');
   $contents[] = array('text' =>'<div class="mb-1 text-danger">' . tep_db_output($cInfo->tag) . '</div>');
   $contents[] = array('align' => 'left', 'text' => '
   <table>
   <tr>
   <td>       
   <a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_SKILL_TAGS, 'page=' . $_GET['page'] .'&id=' . $cInfo->id . '&action=edit') . '">'
   .IMAGE_EDIT.'</a>
   </td>
   <td>
   <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_SKILL_TAGS, 'page=' . $_GET['page'] .'&id=' . $cInfo->id . '&action=delete') . '">'
   .IMAGE_DELETE.'</a>
   </td>
   </tr>
   </table>
   ');
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
$skill_status_array=array();
$skill_status_array[]=array('id'=>'','text'=>'All');
$skill_status_array[]=array('id'=>'active','text'=>'active');
$skill_status_array[]=array('id'=>'inactive','text'=>'inactive');

$template->assign_vars(array(
 'TABLE_HEADING_SKILL_TAG_NAME'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_SKILL_TAGS, tep_get_all_get_params(array('sort','id','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][0]."' class='white'>".TABLE_HEADING_SKILL_TAG_NAME.$obj_sort_by_clause->return_sort_array['image'][0]."</a>",
 'TABLE_HEADING_SKILL_TAG_POINT'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_SKILL_TAGS, tep_get_all_get_params(array('sort','id','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][1]."' class='white'>".TABLE_HEADING_SKILL_TAG_POINT.$obj_sort_by_clause->return_sort_array['image'][1]."</a>",
 'TABLE_HEADING_SKILL_TAG_STATUS'=>"<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_SKILL_TAGS, tep_get_all_get_params(array('sort','id','selected_box','action')))."&sort=".$obj_sort_by_clause->return_sort_array['name'][2]."' class='white'>".TABLE_HEADING_SKILL_TAG_STATUS.$obj_sort_by_clause->return_sort_array['image'][2]."</a>",
 'TABLE_HEADING_ACTION'=>TABLE_HEADING_ACTION,
 'INFO_TEXT_STATUS'=>INFO_TEXT_STATUS,
 'INFO_TEXT_STATUS1'=>tep_draw_pull_down_menu('skill_status', $skill_status_array, $skill_status1,'onchange="document.disply.submit();" class="form-control form-control-sm form-select"'),
 'count_rows'=>$skill_tag_split->display_count($skill_tag_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_SKILL_TAG),
 'no_of_pages'=>$skill_tag_split->display_links($skill_tag_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'],tep_get_all_get_params(array('page','id','action'))),
 
 'new_button'=>'<a class="btn btn-primary float-right" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_SKILL_TAGS, 'page=' . $_GET['page'] .'&action=new') . '"><i class="bi bi-plus-lg me-2"></i>'.IMAGE_NEW.'</a>',


 
 'HEADING_TITLE'=>HEADING_TITLE,
 'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
 'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('skill_tag');
?>