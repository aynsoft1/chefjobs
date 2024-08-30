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
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_IPADDRESS_BLOCK);
$template->set_filenames(array('ipaddress' => 'block_ipaddress.htm'));
include_once(FILENAME_ADMIN_BODY);

$action = (isset($_GET['action']) ? $_GET['action'] : '');

if ($action!="") 
{
 switch ($action) 
	{
  case 'confirm_delete':
   $id = tep_db_prepare_input($_GET['id']);
   tep_db_query("delete from " . IPADDRESS_BLOCK_TABLE . " where id = '" . (int)$id . "'");
			$messageStack->add_session(MESSAGE_SUCCESS_DELETED, 'success');
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_IPADDRESS_BLOCK, 'page=' . $_GET['page']));
  break;
  case 'insert':
  case 'save':
   $ipaddress_from=tep_db_prepare_input($_POST['TR_ipaddress_from']);
   $ipaddress_to=tep_db_prepare_input($_POST['ipaddress_to']);
   $priority = tep_db_prepare_input($_POST['IN_priority']);
   $sql_data_array['ip_address1'] = $ipaddress_from;
   $sql_data_array['ip_address2'] = $ipaddress_to;
   $sql_data_array['priority'] = $priority;

			if($action=='insert')
			{
				if($row_chek=getAnyTableWhereData(IPADDRESS_BLOCK_TABLE,"ip_address1='".tep_db_input($ipaddress_from)."'",'id'))
				{
					$messageStack->add(MESSAGE_NAME_ERROR, 'error');
				}
				else
				{
     tep_db_perform(IPADDRESS_BLOCK_TABLE, $sql_data_array);
     $row_id_check=getAnyTableWhereData(IPADDRESS_BLOCK_TABLE,"1 order by id desc limit 0,1","id");
     $id = $row_id_check['id'];
  			$messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');
					tep_redirect(FILENAME_ADMIN1_IPADDRESS_BLOCK);
				}
			}
			else
			{
    $id=(int)$_GET['id'];
				if($row_chek=getAnyTableWhereData(IPADDRESS_BLOCK_TABLE,"ip_address1='".tep_db_input($ipaddress_from)."' and id!='$id'",'id'))
				{
					$messageStack->add(MESSAGE_NAME_ERROR, 'error');
				}
				else
				{
     tep_db_perform(IPADDRESS_BLOCK_TABLE, $sql_data_array, 'update', "id = '" . (int)$id . "'");
  			$messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
					tep_redirect(FILENAME_ADMIN1_IPADDRESS_BLOCK.'?page='.$_GET['page'].'&id='.$id);
				}
			}
  break;
 }
}
///////////// Middle Values 
$ipaddress_query_raw="select id, ip_address1, ip_address2, priority from " . IPADDRESS_BLOCK_TABLE ." order by priority";
$ipaddress_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $ipaddress_query_raw, $ipaddress_query_numrows);
$ipaddress_query = tep_db_query($ipaddress_query_raw);
if(tep_db_num_rows($ipaddress_query) > 0)
{
 $alternate=1;
 while ($ipaddress = tep_db_fetch_array($ipaddress_query)) 
 {
  if ((!isset($_GET['id']) || (isset($_GET['id']) && ($_GET['id'] == $ipaddress['id']))) && !isset($iInfo) && (substr($action, 0, 3) != 'new')) 
  {
   $iInfo = new objectInfo($ipaddress);
  }
  if ( (isset($iInfo) && is_object($iInfo)) && ($ipaddress['id'] == $iInfo->id) ) 
  {
   $row_selected=' id="defaultSelected" class="table-secondary dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . FILENAME_ADMIN1_IPADDRESS_BLOCK . '?page='.$_GET['page'].'&id=' . $iInfo->id . '&action=edit\'"';
  } 
  else 
  {
   $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . FILENAME_ADMIN1_IPADDRESS_BLOCK . '?page='.$_GET['page'].'&id=' . $ipaddress['id'] . '\'"';
  }
  $alternate++;
  if ( (isset($iInfo) && is_object($iInfo)) && ($ipaddress['id'] == $iInfo->id) ) 
  { 
   $action_image=tep_image(PATH_TO_IMAGE.'icon_arrow_right.gif',IMAGE_EDIT); 
  } 
  else 
  { 
   $action_image='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_IPADDRESS_BLOCK, 'page='.$_GET['page'].'&id=' . $ipaddress['id']) . '">'.tep_image(PATH_TO_IMAGE.'icon_info.gif',IMAGE_INFO).'</a>'; 
  }
  $template->assign_block_vars('ipaddress', array( 'row_selected' => $row_selected,
   'action' => $action_image,
   'ipaddress1' => tep_db_output($ipaddress['ip_address1']),
   'ipaddress2' => tep_db_output($ipaddress['ip_address2']),
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
		$heading[] = array('text' => '<div class="text-primary font-weight-bold mb-1">'.TEXT_INFO_HEADING_IPADDRESS.'</div>');
  $contents = array('form' => tep_draw_form('ipaddress', PATH_TO_ADMIN.FILENAME_ADMIN1_IPADDRESS_BLOCK, 'action=insert','post',' onsubmit="return ValidateForm(this)"'));
		$contents[] = array('text' => '<div class="mb-1 text-danger">' .TEXT_INFO_NEW_INTRO. '</div>');
		$contents[] = array('text' => '<br>'.TEXT_INFO_IPADDRESS_FROM.'<br>'.tep_draw_input_field('TR_ipaddress_from', $_POST['TR_ipaddress_from'], 'class="form-control form-control-sm"' ));
		$contents[] = array('text' => '<br>'.TEXT_INFO_IPADDRESS_TO.'<br>'.tep_draw_input_field('ipaddress_to', $_POST['ipaddress_to'], 'class="form-control form-control-sm"' ));
		$contents[] = array('text' => '<br>'.TEXT_INFO_IPADDRESS_PRIORITY.'<br>'.tep_draw_input_field('IN_priority', $_POST['IN_priority'], 'size="5" class="form-control form-control-sm"' ));
		$contents[] = array('align' => 'left', 'text' => '<br>




    '.tep_draw_submit_button_field('', IMAGE_INSERT,'class="btn btn-primary"').'
    <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_IPADDRESS_BLOCK).'">'.IMAGE_CANCEL.'</a>');
  break;
 case 'edit':
  $value_field=tep_draw_input_field('TR_ipaddress_name', $iInfo->ipaddress_name, '' );
  $heading[] = array('text' => '<div class="text-primary font-weight-bold mb-1">'.TEXT_INFO_HEADING_IPADDRESS.'</div>');
  $contents = array('form' => tep_draw_form('ipaddress', PATH_TO_ADMIN.FILENAME_ADMIN1_IPADDRESS_BLOCK, 'id=' . $iInfo->id.'&page='.$_GET['page'].'&action=save','post',' onsubmit="return ValidateForm(this)"'));
		$contents[] = array('text' => '<div class="mb-1 text-danger">' .TEXT_INFO_EDIT_INTRO. '</div>'); 
		$contents[] = array('text' => '<br>'.TEXT_INFO_IPADDRESS_FROM.'<br>'.tep_draw_input_field('TR_ipaddress_from', $iInfo->ip_address1, 'class="form-control form-control-sm"' ));
		$contents[] = array('text' => '<br>'.TEXT_INFO_IPADDRESS_TO.'<br>'.tep_draw_input_field('ipaddress_to', $iInfo->ip_address2, 'class="form-control form-control-sm"' ));
		$contents[] = array('text' => '<br>'.TEXT_INFO_IPADDRESS_PRIORITY.'<br>'.tep_draw_input_field('IN_priority', $iInfo->priority, 'class="form-control form-control-sm"' ));
    $contents[] = array('align' => 'left', 'text' => '<br>
    
    '.tep_draw_submit_button_field('', IMAGE_UPDATE,'class="btn btn-primary"').'
  <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_IPADDRESS_BLOCK, 'gid=' . $_GET['gid'] . '&id=' . $iInfo->id ). '">'.IMAGE_CANCEL.'</a>');
  break;
 case 'delete':
  $heading[] = array('text' => '<div class="text-primary font-weight-bold mb-1">' . $iInfo->ipaddress_name . '</div>');
  $contents = array('form' => tep_draw_form('ipaddress_delete', PATH_TO_ADMIN.FILENAME_ADMIN1_IPADDRESS_BLOCK, 'page=' . $_GET['page'] . '&id=' . $iInfo->id . '&action=deleteconfirm'));
  $contents[] = array('text' => '<div class="mb-1 text-danger">' .TEXT_DELETE_INTRO. '</div>');
  $contents[] = array('text' => '<br><b>' . $iInfo->ipaddress_name . '</b>');
  $contents[] = array('align' => 'left', 'text' => '<br>
  <a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_IPADDRESS_BLOCK, 'page=' . $_GET['page'] . '&id=' . $_GET['id'].'&action=confirm_delete') . '">'.IMAGE_CONFIRM.'</a>
  <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_IPADDRESS_BLOCK, 'page=' . $_GET['page'] . '&id=' . $_GET['id']) . '">' . IMAGE_CANCEL . '</a>');
 break;
 default:
  if (isset($iInfo) && is_object($iInfo)) 
		{
   $heading[] = array('text' => '<b>'.TEXT_INFO_HEADING_IPADDRESS.'</b>');
   $contents[] = array('text' => tep_db_output($iInfo->ip_address1).(tep_not_null($iInfo->ip_address2)?' - '.$iInfo->ip_address2:''));
   $contents[] = array('align' => 'left', 'text' => '<br><a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_IPADDRESS_BLOCK, 'page=' . $_GET['page'] .'&id=' . $iInfo->id . '&action=edit') . '">'.tep_image_button(PATH_TO_BUTTON.'button_edit.gif',IMAGE_EDIT).'</a>
   <a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_IPADDRESS_BLOCK, 'page=' . $_GET['page'] .'&id=' . $iInfo->id . '&action=delete') . '">'.tep_image_button(PATH_TO_BUTTON.'button_delete.gif',IMAGE_DELETE).'</a>');
   $contents[] = array('text' => '<br>'.TEXT_INFO_ACTION);
  }
  break;
}
if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) 
{
 $box = new right_box;
 $ADMIN_RIGHT_HTML.= $box->infoBox($heading, $contents);
	$RIGHT_BOX_WIDTH=215;
}
else
{
	$RIGHT_BOX_WIDTH='0';
}
/////
$template->assign_vars(array(
 'TABLE_HEADING_IPADDRESS_FROM'=>TABLE_HEADING_IPADDRESS_FROM,
 'TABLE_HEADING_IPADDRESS_TO'=>TABLE_HEADING_IPADDRESS_TO,
 'TABLE_HEADING_ACTION'=>TABLE_HEADING_ACTION,
 'count_rows'=>$ipaddress_split->display_count($ipaddress_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_IP_ADDRESS_ENTRIES),
 'no_of_pages'=>$ipaddress_split->display_links($ipaddress_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']),
 'new_button'=>'<a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_IPADDRESS_BLOCK, 'page=' . $_GET['page'] .'&action=new') . '"><i class="fa fa-plus fa-admin-icons" aria-hidden="true"></i> '.IMAGE_NEW.'</a>',
 'HEADING_TITLE'=>HEADING_TITLE,
 'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
 'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('ipaddress');
?>