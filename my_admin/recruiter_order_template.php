<?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik#********
**********# Company       : Aynsoft               #********
**********# Copyright (c) www.aynsoft.com 2004  #**********
***********************************************************
*/
include_once("../include_files.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_ADMIN_RECRUITER_ORDER_TEMPLATE);
$template->set_filenames(array('recruiter_order_template' => 'recruiter_order_template.htm',
                               'recruiter_order_template_new_edit' => 'recruiter_order_template1.htm',
								'preview' => 'preview_rec_order_template.htm'));
include_once(FILENAME_ADMIN_BODY);

$action = (isset($_GET['action']) ? $_GET['action'] : '');

$edit=false;
$error      =false;

$hidden_fields='';
$order_template_id = (isset($_GET['order_template_id']) ? $_GET['order_template_id'] : '');
if(tep_not_null($order_template_id))
{
 $order_template_id=(int)$order_template_id;
 if(!$row_check=getAnyTableWhereData(RECRUITER_ORDER_TMPL_TABLE,"id='".tep_db_input($order_template_id)."'"))
 {
  $messageStack->add_session(ERROR_ORDER_TEMPLATE_NOT_EXIST, 'error');
  tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_RECRUITER_ORDER_TEMPLATE));
 }
 $edit=true;
 $order_template_name=tep_db_prepare_input($row_check['order_template_name']);
 $priority=tep_db_prepare_input($row_check['priority']);
 $default=tep_db_prepare_input($row_check['alert_default']);
 $description=stripslashes($row_check['description']);
 $de_description=stripslashes($row_check['de_description']);
}
if ($action!="")
{
 switch ($action)
	{
  case 'confirm_delete':
   if($default=='Yes')
   {
			 $messageStack->add_session(ERROR_RECRUITER_ORDER_TEMPLATE_NOT_DELETED, 'error');
   }
   else
   {
    tep_db_query("delete from " . RECRUITER_ORDER_TMPL_TABLE . " where id = '" . (int)$order_template_id . "'");
			 $messageStack->add_session(MESSAGE_SUCCESS_DELETED, 'success');
   }
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_RECRUITER_ORDER_TEMPLATE, 'page=' . $_GET['page']));
  break;
  case 'insert':
  case 'save':
  case 'preview':
  case 'back':
   $order_template_name=tep_db_prepare_input($_POST['TR_recruiter_order_template_name']);
   $priority = tep_db_prepare_input($_POST['IN_priority']);
   $default=tep_db_prepare_input($_POST['default']);
   $description = stripslashes($_POST['description2']);
   $de_description = stripslashes($_POST['de_description2']);

   $hidden_fields.=tep_draw_hidden_field('TR_recruiter_order_template_name', $order_template_name);
   $hidden_fields.=tep_draw_hidden_field('IN_priority', $priority);
   $hidden_fields.=tep_draw_hidden_field('description2', $description);
   $hidden_fields.=tep_draw_hidden_field('de_description2', $de_description);
   $hidden_fields.=tep_draw_hidden_field('default', $default);

   $sql_data_array['order_template_name'] = $order_template_name;
   $sql_data_array['priority'] = $priority;
   $sql_data_array['alert_default'] = $default;
   $sql_data_array['description'] = $description;
   $sql_data_array['de_description'] = $de_description;
			if(strlen($description)<=0)
			{
			 $error      =true;
    $messageStack->add(MESSAGE_DESCRIPTION_ERROR, 'error');
			}
			if(strlen($de_description)<=0)
			{
			 $error      =true;
    $messageStack->add(MESSAGE_FR_DESCRIPTION_ERROR, 'error');
			}
			if($action=='insert')
			{
				if($row_chek=getAnyTableWhereData(RECRUITER_ORDER_TMPL_TABLE,"order_template_name='".tep_db_input($order_template_name)."'",'id'))
				{
					$error      =true;
     $messageStack->add(MESSAGE_NAME_ERROR, 'error');
				}
				if(!$error)
				{
     if($default=='Yes')
     {
      tep_db_query('update '.RECRUITER_ORDER_TMPL_TABLE.' set alert_default="No"');
     }
     else if(!$row_check=getAnyTableWhereData(RECRUITER_ORDER_TMPL_TABLE,"alert_default='Yes'","id"))
     {
      $sql_data_array['alert_default'] = 'Yes';
     }
     tep_db_perform(RECRUITER_ORDER_TMPL_TABLE, $sql_data_array);
     $row_id_check=getAnyTableWhereData(RECRUITER_ORDER_TMPL_TABLE,"1 order by id desc limit 0,1","id");
     $id = $row_id_check['id'];
     /// create a template file  starts //
     $row_template=getAnyTableWhereData(RECRUITER_ORDER_TMPL_TABLE,'alert_default="Yes"');
     $handle = fopen(PATH_TO_MAIN_PHYSICAL.PATH_TO_TEMPLATE.'recruiter_order_template_template.htm', "w");
     fwrite($handle, stripslashes($row_template['description']));
     fclose($handle);
     $handle1 = fopen(PATH_TO_MAIN_PHYSICAL.PATH_TO_TEMPLATE.'de_recruiter_order_template_template.htm', "w");
     fwrite($handle1, stripslashes($row_template['de_description']));
     fclose($handle1);
     /// create a template file  ends //
  			$messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');
					tep_redirect(FILENAME_ADMIN1_ADMIN_RECRUITER_ORDER_TEMPLATE);
				}
			}
			else if($action=='save' && $edit)
			{
				if($row_chek=getAnyTableWhereData(RECRUITER_ORDER_TMPL_TABLE,"order_template_name='".tep_db_input($order_template_name)."' and id!='$order_template_id'",'id'))
				{
				 $error      =true;
    	$messageStack->add(MESSAGE_NAME_ERROR, 'error');
				}
				if(!$error)
				{
     if($default!='Yes')
     {
      if($default_check=getAnyTableWhereData(RECRUITER_ORDER_TMPL_TABLE,"alert_default='Yes' and id='$order_template_id'",'id'))
      {
       $sql_data_array['alert_default'] = 'Yes';
      }
     }
     else
     tep_db_query('update '.RECRUITER_ORDER_TMPL_TABLE.' set alert_default="No"');
     tep_db_perform(RECRUITER_ORDER_TMPL_TABLE, $sql_data_array, 'update', "id = '" . $order_template_id . "'");
     /// create a template file  starts //
     $row_template=getAnyTableWhereData(RECRUITER_ORDER_TMPL_TABLE,'alert_default="Yes"');
     $handle = fopen(PATH_TO_MAIN_PHYSICAL.PATH_TO_TEMPLATE.'recruiter_order_template_template.htm', "w");
     fwrite($handle, stripslashes($row_template['description']));
     fclose($handle);
     $handle1 = fopen(PATH_TO_MAIN_PHYSICAL.PATH_TO_TEMPLATE.'de_recruiter_order_template_template.htm', "w");
     fwrite($handle1, stripslashes($row_template['de_description']));
     fclose($handle1);

					/// create a template file  ends //
  			$messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
					tep_redirect(FILENAME_ADMIN1_ADMIN_RECRUITER_ORDER_TEMPLATE.'?page='.$_GET['page'].'&id='.$order_template_id);
				}
			}
  break;
 }
}
///////////// Middle Values
$recruiter_order_template_query_raw="select id, order_template_name,priority,alert_default from " . RECRUITER_ORDER_TMPL_TABLE ." order by order_template_name";
$recruiter_order_template_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $recruiter_order_template_query_raw, $recruiter_order_template_query_numrows);
$recruiter_order_template_query = tep_db_query($recruiter_order_template_query_raw);
if(tep_db_num_rows($recruiter_order_template_query) > 0)
{
 $alternate=1;
 while ($recruiter_order_template = tep_db_fetch_array($recruiter_order_template_query))
 {
  if ((!isset($_GET['id']) || (isset($_GET['id']) && ($_GET['id'] == $recruiter_order_template['id']))) && !isset($cInfo) && (substr($action, 0, 3) != 'new'))
  {
   $cInfo = new objectInfo($recruiter_order_template);
  }
  if ( (isset($cInfo) && is_object($cInfo)) && ($recruiter_order_template['id'] == $cInfo->id) )
  {
   $row_selected=' id="defaultSelected" class="table-secondary dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . FILENAME_ADMIN1_ADMIN_RECRUITER_ORDER_TEMPLATE . '?page='.$_GET['page'].'&id=' . $cInfo->id . '&action=edit\'"';
  }
  else
  {
   $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . FILENAME_ADMIN1_ADMIN_RECRUITER_ORDER_TEMPLATE . '?page='.$_GET['page'].'&id=' . $recruiter_order_template['id'] . '\'"';
  }
  $alternate++;
  if ( (isset($cInfo) && is_object($cInfo)) && ($recruiter_order_template['id'] == $cInfo->id) )
  {
   $action_image=tep_image(PATH_TO_IMAGE.'icon_arrow_right.gif',IMAGE_EDIT);
  }
  else
  {
   $action_image='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_RECRUITER_ORDER_TEMPLATE, 'page='.$_GET['page'].'&id=' . $recruiter_order_template['id']) . '">'.tep_image(PATH_TO_IMAGE.'icon_info.gif',IMAGE_INFO).'</a>';
  }
  $template->assign_block_vars('recruiter_order_template', array( 'row_selected' => $row_selected,
   'action' => $action_image,
   'name' => tep_db_output($recruiter_order_template['order_template_name']).($recruiter_order_template['alert_default']=='Yes'?' ( '.TEXT_SET_DEFAULT.' )':''),
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
 case 'delete':
  $heading[] = array('text' => '<b>' . $cInfo->order_template_name . '</b>');
  $contents = array('form' => tep_draw_form('recruiter_order_template_delete', PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_RECRUITER_ORDER_TEMPLATE, 'page=' . $_GET['page'] . '&id=' . $nInfo->id . '&action=deleteconfirm'));
  $contents[] = array('text' => TEXT_DELETE_INTRO);
  $contents[] = array('text' => '<br><b>' . $cInfo->order_template_name . '</b>');
  $contents[] = array('align' => 'left', 'text' => '<br>
  <a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_RECRUITER_ORDER_TEMPLATE, 'page=' . $_GET['page'] . '&order_template_id=' . $order_template_id.'&action=confirm_delete') . '">'.IMAGE_CONFIRM.'</a>
  <a class="btn btn-secondary"" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_RECRUITER_ORDER_TEMPLATE, 'page=' . $_GET['page'] . '&order_template_id=' . $order_template_id) . '">' . IMAGE_CANCEL . '</a>');
 break;
 default:
  if (isset($cInfo) && is_object($cInfo))
		{
   $heading[] = array('text' => '<b>'.TEXT_INFO_HEADING_RECRUITER_ORDER_TEMPLATE.'</b>');
   $contents[] = array('text' => tep_db_output($cInfo->order_template_name).($cInfo->alert_default=='Yes'?' ( '.TEXT_SET_DEFAULT.' )':''));
   $contents[] = array('align' => 'left', 'text' => '<br>
   <a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_RECRUITER_ORDER_TEMPLATE, 'page=' . $_GET['page'] .'&order_template_id=' . $cInfo->id . '&action=edit') . '">'.IMAGE_EDIT.'</a>
   <a class="btn btn-secondary"" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_RECRUITER_ORDER_TEMPLATE, 'page=' . $_GET['page'] .'&order_template_id=' . $cInfo->id . '&action=delete') . '">'.IMAGE_DELETE.'</a>');
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
	$RIGHT_BOX_WIDTH=0;
}
/////

$template->assign_vars(array(
 'TABLE_HEADING_ORDER_TEMPLATE_NAME'=>TABLE_HEADING_ORDER_TEMPLATE_NAME,
 'TABLE_HEADING_ACTION'=>TABLE_HEADING_ACTION,
 'INFO_TEXT_ORDER_TEMPLATE_NAME'=>INFO_TEXT_ORDER_TEMPLATE_NAME,
 'INFO_TEXT_ORDER_TEMPLATE_NAME1'=>tep_draw_input_field('TR_recruiter_order_template_name', $order_template_name, 'class="form-control form-control-sm mb-2"', true ).'&nbsp;'.tep_draw_checkbox_field('default', 'Yes', false, $default, 'id="check_box_default"') . ' <label for="check_box_default">' . TEXT_SET_DEFAULT.'</label>',
 'INFO_TEXT_RECRUITER_ORDER_TEMPLATE_PRIORITY'=>INFO_TEXT_RECRUITER_ORDER_TEMPLATE_PRIORITY,
 'INFO_TEXT_RECRUITER_ORDER_TEMPLATE_PRIORITY1'=>tep_draw_input_field('IN_priority', $priority, 'class="form-control form-control-sm"', true ),
 'INFO_TEXT_RECRUITER_ORDER_TEMPLATE_DESCRIPTION'=>INFO_TEXT_RECRUITER_ORDER_TEMPLATE_DESCRIPTION,
 'INFO_TEXT_RECRUITER_ORDER_TEMPLATE_DESCRIPTION1'=>tep_draw_textarea_field('description2', 'soft', '', '10', $description, ' id="description2"', true, true),
 'INFO_TEXT_FR_RECRUITER_ORDER_TEMPLATE_DESCRIPTION'=>INFO_TEXT_FR_RECRUITER_ORDER_TEMPLATE_DESCRIPTION,
 'INFO_TEXT_FR_RECRUITER_ORDER_TEMPLATE_DESCRIPTION1'=>tep_draw_textarea_field('de_description2', 'soft', '', '10', $de_description, '  id="de_description2"', true, true),
 'HOST_NAME'                       =>HOST_NAME,
 'count_rows'=>$recruiter_order_template_split->display_count($recruiter_order_template_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_RECRUITER_ORDER_TEMPLATES),
 'no_of_pages'=>$recruiter_order_template_split->display_links($recruiter_order_template_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']),
 'new_button'=>'<a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_RECRUITER_ORDER_TEMPLATE, 'page=' . $_GET['page'] .'&action=new') . '"><i class="fa fa-plus" aria-hidden="true"></i> '.IMAGE_NEW.'</a>',
 'buttons'=>tep_draw_submit_button_field('', IMAGE_PREVIEW,'class="btn btn-primary"'),
 'form'=>tep_draw_form('preview', PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_RECRUITER_ORDER_TEMPLATE, 'action=preview'.($edit?'&order_template_id='.$order_template_id:''), 'post', 'onsubmit="return ValidateForm(this)"'),
 'HEADING_TITLE'=>HEADING_TITLE,
 'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
 'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
 'update_message'=>$messageStack->output()));
 if($action=='new' || $action=='edit' || $action=='back'  || $error)
 {
  $template->pparse('recruiter_order_template_new_edit');
 }
 else if($action=='preview' || $action=='save' || $action=='insert')
 {
  $template->assign_vars(array(
   'INFO_TEXT_ORDER_TEMPLATE_NAME'=>INFO_TEXT_ORDER_TEMPLATE_NAME,
   'INFO_TEXT_ORDER_TEMPLATE_NAME1'=>tep_db_output($order_template_name).($default=='Yes'?'&nbsp; ( '.TEXT_SET_DEFAULT.' ) ':''),
   'INFO_TEXT_RECRUITER_ORDER_TEMPLATE_PRIORITY'=>INFO_TEXT_RECRUITER_ORDER_TEMPLATE_PRIORITY,
   'INFO_TEXT_RECRUITER_ORDER_TEMPLATE_PRIORITY1'=>tep_db_output($priority),
   'INFO_TEXT_RECRUITER_ORDER_TEMPLATE_DESCRIPTION'=>INFO_TEXT_RECRUITER_ORDER_TEMPLATE_DESCRIPTION,
   'INFO_TEXT_RECRUITER_ORDER_TEMPLATE_DESCRIPTION1'=>stripslashes($description),
   'INFO_TEXT_FR_RECRUITER_ORDER_TEMPLATE_DESCRIPTION'=>INFO_TEXT_FR_RECRUITER_ORDER_TEMPLATE_DESCRIPTION,
   'INFO_TEXT_FR_RECRUITER_ORDER_TEMPLATE_DESCRIPTION1'=>stripslashes($de_description),
   'hidden_fields'=>$hidden_fields,
   'button1'=>($edit?tep_image_submit(PATH_TO_BUTTON.'button_save.gif', IMAGE_SAVE):tep_image_submit(PATH_TO_BUTTON.'button_insert.gif', IMAGE_INSERT)),
   'button2'=>'&nbsp;&nbsp;'.tep_image_submit(PATH_TO_BUTTON.'button_back.gif', IMAGE_BACK),
   'form1'=>tep_draw_form('back', PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_RECRUITER_ORDER_TEMPLATE, ($edit?'action=save&order_template_id='.$order_template_id:'action=insert'), 'post', ''),
   'form2'=>tep_draw_form('insert', PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_RECRUITER_ORDER_TEMPLATE, ($edit?'action=back&order_template_id='.$order_template_id:'action=back'), 'post', '')));
  $template->pparse('preview');
 }
 else
 {
  $template->pparse('recruiter_order_template');
 }
?>