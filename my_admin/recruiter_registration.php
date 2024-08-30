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
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_ADMIN_RECRUITER_REGISTRATION);
$template->set_filenames(array('recruiter_registration' => 'recruiter_registration.htm',
                               'recruiter_registration_new_edit' => 'recruiter_registration1.htm',
								'preview' => 'preview_recruiter_registration.htm'));
include_once(FILENAME_ADMIN_BODY);
$action = (isset($_GET['action']) ? $_GET['action'] : '');
$edit=false;
$error      =false;

$hidden_fields='';
$recruiter_registration_id = (isset($_GET['recruiter_registration_id']) ? $_GET['recruiter_registration_id'] : '');
if(tep_not_null($recruiter_registration_id))
{
 $recruiter_registration_id=(int)$recruiter_registration_id;
 if(!$row_check=getAnyTableWhereData(RECRUITER_REGISTRATION_TABLE,"id='".tep_db_input($recruiter_registration_id)."'"))
 {
  $messageStack->add_session(ERROR_RECRUITER_REGISTRATION_NOT_EXIST, 'error');
  tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_RECRUITER_REGISTRATION));
 }
 $edit=true;
 $recruiter_registration_name=tep_db_prepare_input($row_check['recruiter_registration_name']);
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
			 $messageStack->add_session(ERROR_RECRUITER_REGISTRATION_NOT_DELETED, 'error');
   }
   else
   {
    tep_db_query("delete from " . RECRUITER_REGISTRATION_TABLE . " where id = '" . (int)$recruiter_registration_id . "'");
			 $messageStack->add_session(MESSAGE_SUCCESS_DELETED, 'success');
   }
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_RECRUITER_REGISTRATION, 'page=' . $_GET['page']));
  break;
  case 'insert':
  case 'save':
  case 'preview':
  case 'back':
   $recruiter_registration_name=tep_db_prepare_input($_POST['TR_recruiter_registration_name']);
   $priority = tep_db_prepare_input($_POST['IN_priority']);
   $default=tep_db_prepare_input($_POST['default']);
   $description = stripslashes($_POST['description2']);
   $de_description = stripslashes($_POST['de_description2']);

   $hidden_fields.=tep_draw_hidden_field('TR_recruiter_registration_name', $recruiter_registration_name);
   $hidden_fields.=tep_draw_hidden_field('IN_priority', $priority);
   $hidden_fields.=tep_draw_hidden_field('description2', $description);
   $hidden_fields.=tep_draw_hidden_field('de_description2', $de_description);
   $hidden_fields.=tep_draw_hidden_field('default', $default);

   $sql_data_array['recruiter_registration_name'] = $recruiter_registration_name;
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
				if($row_chek=getAnyTableWhereData(RECRUITER_REGISTRATION_TABLE,"recruiter_registration_name='".tep_db_input($recruiter_registration_name)."'",'id'))
				{
					$error      =true;
					$messageStack->add(MESSAGE_NAME_ERROR, 'error');
				}
				if(!$error)
				{
     if($default=='Yes')
     {
      tep_db_query('update '.RECRUITER_REGISTRATION_TABLE.' set alert_default="No"');
     }
     else if(!$row_check=getAnyTableWhereData(RECRUITER_REGISTRATION_TABLE,"alert_default='Yes'","id"))
     {
      $sql_data_array['alert_default'] = 'Yes';
     }
     tep_db_perform(RECRUITER_REGISTRATION_TABLE, $sql_data_array);
     $row_id_check=getAnyTableWhereData(RECRUITER_REGISTRATION_TABLE,"1 order by id desc limit 0,1","id");
     $id = $row_id_check['id'];
     /// create a template file  starts //
     $row_template=getAnyTableWhereData(RECRUITER_REGISTRATION_TABLE,'alert_default="Yes"');
     $handle = fopen(PATH_TO_MAIN_PHYSICAL.PATH_TO_TEMPLATE.'recruiter_registration_template.htm', "w");
     fwrite($handle, stripslashes($row_template['description']));
     fclose($handle);
     $handle1 = fopen(PATH_TO_MAIN_PHYSICAL.PATH_TO_TEMPLATE.'de_recruiter_registration_template.htm', "w");
     fwrite($handle1, stripslashes($row_template['de_description']));
     fclose($handle1);
     /// create a template file  ends //
  			$messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');
					tep_redirect(FILENAME_ADMIN1_ADMIN_RECRUITER_REGISTRATION);
				}
			}
			else if($action=='save' && $edit)
			{
				if($row_chek=getAnyTableWhereData(RECRUITER_REGISTRATION_TABLE,"recruiter_registration_name='".tep_db_input($recruiter_registration_name)."' and id!='$recruiter_registration_id'",'id'))
				{
					$error      =true;
					$messageStack->add(MESSAGE_NAME_ERROR, 'error');
				}
				if(!$error)
				{
     if($default!='Yes')
     {
      if($default_check=getAnyTableWhereData(RECRUITER_REGISTRATION_TABLE,"alert_default='Yes' and id='$recruiter_registration_id'",'id'))
      {
       $sql_data_array['alert_default'] = 'Yes';
      }
     }
     else
      tep_db_query('update '.RECRUITER_REGISTRATION_TABLE.' set alert_default="No"');

     tep_db_perform(RECRUITER_REGISTRATION_TABLE, $sql_data_array, 'update', "id = '" . $recruiter_registration_id . "'");
     /// create a template file  starts //
     $row_template=getAnyTableWhereData(RECRUITER_REGISTRATION_TABLE,'alert_default="Yes"');
     $handle = fopen(PATH_TO_MAIN_PHYSICAL.PATH_TO_TEMPLATE.'recruiter_registration_template.htm', "w");
     fwrite($handle, stripslashes($row_template['description']));
     fclose($handle);
     $handle1 = fopen(PATH_TO_MAIN_PHYSICAL.PATH_TO_TEMPLATE.'de_recruiter_registration_template.htm', "w");
     fwrite($handle1, stripslashes($row_template['de_description']));
     fclose($handle1);
     /// create a template file  ends //
  			$messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
					tep_redirect(FILENAME_ADMIN1_ADMIN_RECRUITER_REGISTRATION.'?page='.$_GET['page'].'&id='.$recruiter_registration_id);
				}
			}
  break;
 }
}
///////////// Middle Values
$recruiter_registration_query_raw="select id, recruiter_registration_name,priority,alert_default from " . RECRUITER_REGISTRATION_TABLE ." order by recruiter_registration_name";
$recruiter_registration_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $recruiter_registration_query_raw, $recruiter_registration_query_numrows);
$recruiter_registration_query = tep_db_query($recruiter_registration_query_raw);
if(tep_db_num_rows($recruiter_registration_query) > 0)
{
 $alternate=1;
 while ($recruiter_registration = tep_db_fetch_array($recruiter_registration_query))
 {
  if ((!isset($_GET['id']) || (isset($_GET['id']) && ($_GET['id'] == $recruiter_registration['id']))) && !isset($cInfo) && (substr($action, 0, 3) != 'new'))
  {
   $cInfo = new objectInfo($recruiter_registration);
  }
  if ( (isset($cInfo) && is_object($cInfo)) && ($recruiter_registration['id'] == $cInfo->id) )
  {
   $row_selected=' id="defaultSelected" class="table-secondary dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . FILENAME_ADMIN1_ADMIN_RECRUITER_REGISTRATION . '?page='.$_GET['page'].'&id=' . $cInfo->id . '&action=edit\'"';
  }
  else
  {
   $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . FILENAME_ADMIN1_ADMIN_RECRUITER_REGISTRATION . '?page='.$_GET['page'].'&id=' . $recruiter_registration['id'] . '\'"';
  }
  $alternate++;
  if ( (isset($cInfo) && is_object($cInfo)) && ($recruiter_registration['id'] == $cInfo->id) )
  {
   $action_image=tep_image(PATH_TO_IMAGE.'icon_arrow_right.gif',IMAGE_EDIT);
  }
  else
  {
   $action_image='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_RECRUITER_REGISTRATION, 'page='.$_GET['page'].'&id=' . $recruiter_registration['id']) . '">'.tep_image(PATH_TO_IMAGE.'icon_info.gif',IMAGE_INFO).'</a>';
  }
  $template->assign_block_vars('recruiter_registration', array( 'row_selected' => $row_selected,
   'action' => $action_image,
   'name' => tep_db_output($recruiter_registration['recruiter_registration_name']).($recruiter_registration['alert_default']=='Yes'?' ( '.TEXT_SET_DEFAULT.' )':''),
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
  $heading[] = array('text' => '<b>' . $cInfo->recruiter_registration_name . '</b>');
  $contents = array('form' => tep_draw_form('recruiter_registration_delete', PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_RECRUITER_REGISTRATION, 'page=' . $_GET['page'] . '&id=' . $nInfo->id . '&action=deleteconfirm'));
  $contents[] = array('text' => TEXT_DELETE_INTRO);
  $contents[] = array('text' => '<br><b>' . $cInfo->recruiter_registration_name . '</b>');
  $contents[] = array('align' => 'left', 'text' => '<br>
  <a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_RECRUITER_REGISTRATION, 'page=' . $_GET['page'] . '&recruiter_registration_id=' . $recruiter_registration_id.'&action=confirm_delete') . '">'.IMAGE_CONFIRM.'</a>
  <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_RECRUITER_REGISTRATION, 'page=' . $_GET['page'] . '&recruiter_registration_id=' . $recruiter_registration_id) . '">' . IMAGE_CANCEL . '</a>');
 break;
 default:
  if (isset($cInfo) && is_object($cInfo))
		{
   $heading[] = array('text' => '<b>'.TEXT_INFO_HEADING_RECRUITER_REGISTRATION.'</b>');
   $contents[] = array('text' => tep_db_output($cInfo->recruiter_registration_name).($cInfo->alert_default=='Yes'?' ( '.TEXT_SET_DEFAULT.' )':''));
   $contents[] = array('align' => 'left', 'text' => '<br>
   <a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_RECRUITER_REGISTRATION, 'page=' . $_GET['page'] .'&recruiter_registration_id=' . $cInfo->id . '&action=edit') . '">'.IMAGE_EDIT.'</a>
   <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_RECRUITER_REGISTRATION, 'page=' . $_GET['page'] .'&recruiter_registration_id=' . $cInfo->id . '&action=delete') . '">'.IMAGE_DELETE.'</a>');
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
 'TABLE_HEADING_RECRUITER_REGISTRATION_NAME'=>TABLE_HEADING_RECRUITER_REGISTRATION_NAME,
 'TABLE_HEADING_ACTION'=>TABLE_HEADING_ACTION,
 'INFO_TEXT_RECRUITER_REGISTRATION_NAME'=>INFO_TEXT_RECRUITER_REGISTRATION_NAME,
 'INFO_TEXT_RECRUITER_REGISTRATION_NAME1'=>tep_draw_input_field('TR_recruiter_registration_name', $recruiter_registration_name, 'class="form-control form-control-sm mb-2"', true ).'&nbsp;'.tep_draw_checkbox_field('default', 'Yes', false, $default, 'id="check_box_default"') . ' <label for="check_box_default">' . TEXT_SET_DEFAULT.'</label>',
 'INFO_TEXT_RECRUITER_REGISTRATION_PRIORITY'=>INFO_TEXT_RECRUITER_REGISTRATION_PRIORITY,
 'INFO_TEXT_RECRUITER_REGISTRATION_PRIORITY1'=>tep_draw_input_field('IN_priority', $priority, 'class="form-control form-control-sm"', true ),
 'INFO_TEXT_RECRUITER_REGISTRATION_DESCRIPTION'=>INFO_TEXT_RECRUITER_REGISTRATION_DESCRIPTION,
 'INFO_TEXT_RECRUITER_REGISTRATION_DESCRIPTION1'=>tep_draw_textarea_field('description2', 'soft', '', '10', $description, ' id="description2"', true, true),
 'INFO_TEXT_FR_RECRUITER_REGISTRATION_DESCRIPTION'=>INFO_TEXT_FR_RECRUITER_REGISTRATION_DESCRIPTION,
 'INFO_TEXT_FR_RECRUITER_REGISTRATION_DESCRIPTION1'=>tep_draw_textarea_field('de_description2', 'soft', '', '10', $de_description, ' id="de_description2"', true, true),
 'HOST_NAME'                       =>HOST_NAME,
 'count_rows'=>$recruiter_registration_split->display_count($recruiter_registration_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_RECRUITER_REGISTRATIONS),
 'no_of_pages'=>$recruiter_registration_split->display_links($recruiter_registration_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']),
 'new_button'=>'<a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_RECRUITER_REGISTRATION, 'page=' . $_GET['page'] .'&action=new') . '"><i class="fa fa-plus" aria-hidden="true"></i> '.IMAGE_NEW.'</a>',
 'buttons'=>tep_draw_submit_button_field('', IMAGE_PREVIEW,'class="btn btn-primary"'),
 'form'=>tep_draw_form('preview', PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_RECRUITER_REGISTRATION, 'action=preview'.($edit?'&recruiter_registration_id='.$recruiter_registration_id:''), 'post', 'onsubmit="return ValidateForm(this)"'),
 'HEADING_TITLE'=>HEADING_TITLE,
 'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
 'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
 'update_message'=>$messageStack->output()));
 if($action=='new' || $action=='edit' || $action=='back'  || $error)
 {
  $template->pparse('recruiter_registration_new_edit');
 }
 else if($action=='preview' || $action=='save' || $action=='insert')
 {
  $template->assign_vars(array(
   'INFO_TEXT_RECRUITER_REGISTRATION_NAME'=>INFO_TEXT_RECRUITER_REGISTRATION_NAME,
   'INFO_TEXT_RECRUITER_REGISTRATION_NAME1'=>tep_db_output($recruiter_registration_name).($default=='Yes'?'&nbsp; ( '.TEXT_SET_DEFAULT.' ) ':''),
   'INFO_TEXT_RECRUITER_REGISTRATION_PRIORITY'=>INFO_TEXT_RECRUITER_REGISTRATION_PRIORITY,
   'INFO_TEXT_RECRUITER_REGISTRATION_PRIORITY1'=>tep_db_output($priority),
   'INFO_TEXT_RECRUITER_REGISTRATION_DESCRIPTION'=>INFO_TEXT_RECRUITER_REGISTRATION_DESCRIPTION,
   'INFO_TEXT_RECRUITER_REGISTRATION_DESCRIPTION1'=>stripslashes($description),
   'INFO_TEXT_FR_RECRUITER_REGISTRATION_DESCRIPTION'=>INFO_TEXT_FR_RECRUITER_REGISTRATION_DESCRIPTION,
   'INFO_TEXT_FR_RECRUITER_REGISTRATION_DESCRIPTION1'=>stripslashes($de_description),
   'hidden_fields'=>$hidden_fields,
   'button1'=>($edit?tep_image_submit(PATH_TO_BUTTON.'button_save.gif', IMAGE_SAVE):tep_image_submit(PATH_TO_BUTTON.'button_insert.gif', IMAGE_INSERT)),
   'button2'=>'&nbsp;&nbsp;'.tep_image_submit(PATH_TO_BUTTON.'button_back.gif', IMAGE_BACK),
   'form1'=>tep_draw_form('back', PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_RECRUITER_REGISTRATION, ($edit?'action=save&recruiter_registration_id='.$recruiter_registration_id:'action=insert'), 'post', ''),
   'form2'=>tep_draw_form('insert', PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_RECRUITER_REGISTRATION, ($edit?'action=back&recruiter_registration_id='.$recruiter_registration_id:'action=back'), 'post', '')));
  $template->pparse('preview');
 }
 else
 {
  $template->pparse('recruiter_registration');
 }
?>