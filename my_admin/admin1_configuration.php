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
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_CONFIGURATION);
$template->set_filenames(array('configuration' => 'admin1_configuration.htm'));
include_once(FILENAME_ADMIN_BODY);

$gid=(int)tep_db_input($_GET['gid']);
if($row=getAnyTableWhereData(CONFIGURATION_GROUP_TABLE,"id='".$gid."'","configuration_group_title"))
{
 $configuration_group_title=tep_db_output($row['configuration_group_title']);
}
else
{
 tep_redirect(FILENAME_ADMIN1_CONFIGURATION. '?gid=1');
}

//////////////
$action = (isset($_GET['action']) ? $_GET['action'] : '');

if (tep_not_null($action))
{
 switch ($action)
	{
  case 'insert':
  case 'save':
   $sql_data_array = array();

   $configuration_group_id=tep_db_prepare_input($gid);
   $configuration_name = tep_db_prepare_input($_POST['TR_configuration_name']);
   $configuration_title = tep_db_prepare_input($_POST['TR_configuration_title']);
   $configuration_value = tep_db_prepare_input($_POST['TR_configuration_value']);
   $configuration_description = tep_db_prepare_input($_POST['configuration_description']);
   $priority = tep_db_prepare_input($_POST['IN_configuration_priority']);

   $sql_data_array['configuration_group_id'] = $configuration_group_id;
  // $sql_data_array['configuration_name'] = $configuration_name;
  // $sql_data_array['configuration_title'] = $configuration_title;
   $sql_data_array['configuration_value'] = $configuration_value;
  // $sql_data_array['configuration_description'] = $configuration_description;
   $sql_data_array['priority'] = $priority;

   if($action=='insert')
			 {
			  if($row_chek=getAnyTableWhereData(CONFIGURATION_TABLE,"configuration_name='".tep_db_input($configuration_name)."' and configuration_title='".tep_db_input($configuration_title)."'",'id'))
				{
	    $messageStack->add(MESSAGE_TITLE_NAME_ERROR, 'error');
				}
				else if($row_chek=getAnyTableWhereData(CONFIGURATION_TABLE,"configuration_name='".tep_db_input($configuration_name)."'",'id'))
				{
	    $messageStack->add(MESSAGE_NAME_ERROR, 'error');
				}
			else
				{
     $sql_data_array['inserted'] = 'now()';
					tep_db_perform(CONFIGURATION_TABLE, $sql_data_array);
     $row=getAnyTableWhereData(CONFIGURATION_TABLE,"1 order by id desc limit 0,1","id");
     $id=$row['id'];
	    $messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');
     tep_redirect(FILENAME_ADMIN1_CONFIGURATION. '?gid=' . $gid.'&id='.$id);
				}
			}
			else
			{
              $id=(int)tep_db_input($_GET['id']);
				if($row_chek=getAnyTableWhereData(CONFIGURATION_TABLE,"configuration_name='".tep_db_input($configuration_name)."' and configuration_title='".tep_db_input($configuration_title)."' and id!='$id'",'id'))
				{
	    $messageStack->add(MESSAGE_TITLE_NAME_ERROR, 'error');
				}
				else if($row_chek=getAnyTableWhereData(CONFIGURATION_TABLE,"configuration_name='$configuration_name' and id!='$id'",'id'))
				{
	    $messageStack->add(MESSAGE_NAME_ERROR, 'error');
				}
				else
				{
					if($configuration_name =='MOBILE_SITE_LOGO')
					{
      unset($sql_data_array['configuration_value']);
						if(tep_not_null($_FILES['mobile_site_logo']['name']))
						{
							if($obj_logo = new upload('mobile_site_logo',PATH_TO_MAIN_PHYSICAL_MOBILE.'img/','644',array('gif','jpg','png')))
							{
								$mobile_site_logo=tep_db_input($obj_logo->filename);
								$file_ext=substr($mobile_site_logo,-3);
								$new_file_name='logo.'.$file_ext;
								if(file_exists(PATH_TO_MAIN_PHYSICAL_MOBILE.'img/'.MOBILE_SITE_LOGO)&& tep_not_null(MOBILE_SITE_LOGO))
								{
									@unlink(PATH_TO_MAIN_PHYSICAL_MOBILE.'img/'.MOBILE_SITE_LOGO);
								}
								@copy(PATH_TO_MAIN_PHYSICAL_MOBILE.'img/'.$mobile_site_logo,PATH_TO_MAIN_PHYSICAL_MOBILE.'img/'.$new_file_name);
								@unlink(PATH_TO_MAIN_PHYSICAL_MOBILE.'img/'.$mobile_site_logo);
								$sql_data_array['configuration_value'] = $new_file_name;
							}
							else
							{
								$error=true;
								$messageStack->add(UPLOAD_ERROR, 'error');
							}
						}
					}
					elseif($configuration_name =='EMAIL_SMTP_PASSWORD')
					{
                     $encode_string=encode_string("mail=".$configuration_value."=pass");
                     $sql_data_array['configuration_value'] = $encode_string;
					}

     /////// check if it is screeners ////
     elseif($configuration_name=='NO_OF_SCREENERS')
     {
      if($configuration_value >0)
      {
       $row_chek=getAnyTableWhereData(CONFIGURATION_TABLE,"configuration_name='".tep_db_input($configuration_name)."'",'configuration_value');
       $total=$row_chek['configuration_value'];
       $difference=$configuration_value-$total;
       //echo $difference;
       $fields_query = tep_db_query("show fields from " .SCREENER_TABLE);
       while ($fields = tep_db_fetch_array($fields_query))
       {
        $field_name=$fields['Field'];
       }
       $field_name=substr($field_name,1);
       $query="alter table ".SCREENER_TABLE;
       if($difference >0)
       {
        for($i=1;$i<=$difference;$i++)
         $query.=" add q".($field_name+$i)." varchar( 255 ),";
        $query=substr($query,0,-1).";";
        tep_db_query($query);
       }
       else if($difference < 0)
       {
        for($i=$configuration_value;$i<(int)$field_name;$i++)
         $query.=" drop q".($i+1).",";
        $query=substr($query,0,-1).";";
        tep_db_query($query);
       }
       //echo $query; die();
       $sql_data_array['updated'] = 'now()';

       tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "id = '" . (int)$id . "'");
       $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
       tep_redirect(FILENAME_ADMIN1_CONFIGURATION. '?gid=' . $gid . '&id=' . $id);
      }
      else
      {
       $messageStack->add_session(MESSAGE_UNSUCCESS_SCREENER_UPDATED, 'error');
       tep_redirect(FILENAME_ADMIN1_CONFIGURATION. '?gid=' . $gid . '&id=' . $id);
      }
     }
     $sql_data_array['updated'] = 'now()';
					tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "id = '" . (int)$id . "'");
	    $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
     tep_redirect(FILENAME_ADMIN1_CONFIGURATION. '?gid=' . $gid . '&id=' . $id);
				}
			}
  break;
 }
}
///////////// Middle Values

$configuration_query = tep_db_query("select * from " . CONFIGURATION_TABLE . " where configuration_group_id = '".(int)$gid . "' order by priority");
if(tep_db_num_rows($configuration_query) > 0)
{
 $alternate=1;
 while ($configuration = tep_db_fetch_array($configuration_query))
 {
  if ((!isset($_GET['id']) || (isset($_GET['id']) && ($_GET['id'] == $configuration['id']))) && !isset($cInfo) && (substr($action, 0, 3) != 'new'))
  {
   $cInfo = new objectInfo($configuration);
  }
  if ( (isset($cInfo) && is_object($cInfo)) && ($configuration['id'] == $cInfo->id) )
  {
   $row_selected=' id="defaultSelected" class="table-secondary dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . FILENAME_ADMIN1_CONFIGURATION . '?gid=' . $gid . '&id=' . $cInfo->id . '&action=edit\'"';
  }
  else
  {
   $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . FILENAME_ADMIN1_CONFIGURATION . '?gid=' . $gid . '&id=' . $configuration['id'] . '\'"';
  }
  $alternate++;
  if ( (isset($cInfo) && is_object($cInfo)) && ($configuration['id'] == $cInfo->id) )
  {
   $action_image=tep_image(PATH_TO_IMAGE.'icon_arrow_right.gif',IMAGE_EDIT);
  }
  else
  {
   $action_image='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_CONFIGURATION, 'gid=' . $gid . '&id=' . $configuration['id']) . '">'.tep_image(PATH_TO_IMAGE.'icon_info.gif',IMAGE_INFO).'</a>';
  }
  $template->assign_block_vars('configuration', array( 'row_selected' => $row_selected,
   'action' => $action_image,
   'title' => tep_db_output($configuration['configuration_title']),
   'value' => tep_db_output($configuration['configuration_value']),
   ));
 }
}

//print_r($cInfo);
//// for right side
$ADMIN_RIGHT_HTML="";

$heading = array();
$contents = array();
switch ($action)
{
 case 'new':
 case 'insert':
 case 'save':
		$heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_CONFIGURATION . '</b>');
		$contents = array('form' => tep_draw_form('configuration', PATH_TO_ADMIN.FILENAME_ADMIN1_CONFIGURATION,'gid=' . $gid . '&action=insert','post', 'onsubmit="return ValidateForm(this)" enctype="multipart/form-data"'));
		$contents[] = array('text' => $configuration_description);
		$contents[] = array('text' => '<br>'.TEXT_INFO_INSERT_INTRO);

		$contents[] = array('text' => '<div class="form-group"><label>' . TEXT_INFO_CONFIGURATION_TITLE . '</label>'.tep_draw_input_field('TR_configuration_title',$configuration_title,'class="form-control form-control-sm"').'</div>');
	//	$contents[] = array('text' => '<br>' . TEXT_INFO_CONFIGURATION_NAME . '<br>'.tep_draw_input_field('TR_configuration_name',$configuration_name,));
		$contents[] = array('text' => '<div class="form-group"><label>' . TEXT_INFO_CONFIGURATION_VALUE . '</label>'.tep_draw_input_field('TR_configuration_value',$configuration_value,'class="form-control form-select mb-3"').'</div>');
	//	$contents[] = array('text' => '<br>' . TEXT_INFO_CONFIGURATION_DESCRIPTION . '<br>'.tep_draw_input_field('configuration_description',$configuration_description));
		//$contents[] = array('text' => '<div class="form-group"><label>' . TEXT_INFO_CONFIGURATION_DESCRIPTION . '</label>'.$configuration_description);
		$contents[] = array('text' => '<div class="form-group"><label>' . TEXT_INFO_CONFIGURATION_PRIORITY . '</label>'.tep_draw_input_field('IN_configuration_priority',$configuration_priority,'class="form-control form-control-sm"').'</div>');
		
    
    $contents[] = array('align' => 'left', 'text' => '<div class="form-group">'.
    tep_button_submit('btn btn-primary mr-2',IMAGE_INSERT).'<a class="btn btn-secondary" href="' . FILENAME_ADMIN1_CONFIGURATION. '?gid='.$gid.'">'.IMAGE_CANCEL.'</a></div>');
  break;
 case 'edit':
  if ($cInfo->set_function)
  {
    eval('$value_field = ' . $cInfo->set_function . '"' . tep_db_output($cInfo->configuration_value) . '");');
  }
  else
  {
   if($cInfo->configuration_name=='MODULE_PAYMENT_MONEYORDER_PAYTO')
			{
    $value_field=tep_draw_textarea_field('TR_configuration_value', true,30,3,$cInfo->configuration_value, 'class="form-control form-control-sm"' );
			}
			else if($cInfo->configuration_name=='MOBILE_SITE_LOGO')
   {
    $value_field  = MOBILE_SITE_LOGO;
    $value_field .= '<br>'.tep_draw_file_field('mobile_site_logo').'<br>'.INFO_TEXT_UPLOAD_PHOTO;
    if(file_exists(PATH_TO_MAIN_PHYSICAL_MOBILE.'img/'.MOBILE_SITE_LOGO)&& tep_not_null(MOBILE_SITE_LOGO))
    $value_field .= '<br>'.tep_image(PATH_TO_MOBILE.'img/'.MOBILE_SITE_LOGO);
   }
   else if($cInfo->configuration_name=='EMAIL_SMTP_PASSWORD')
   {
    $value_field=tep_draw_password_field('TR_configuration_value','');
   }
   else
   $value_field=tep_draw_input_field('TR_configuration_value',$cInfo->configuration_value, 'class="form-control form-control-sm"');
  }
  $heading[] = array('text' => '<b>' . tep_db_output($cInfo->configuration_title) . '</b>');
  $contents = array('form' => tep_draw_form('configuration',PATH_TO_ADMIN.FILENAME_ADMIN1_CONFIGURATION, 'gid=' . $gid . '&id=' . $cInfo->id . '&action=save', 'post','onsubmit="return ValidateForm(this)" enctype="multipart/form-data"'));
		$contents[] = array('text' => '<span style="color:blue;">'.$configuration_description.'</span>');
		$contents[] = array('text' => '<br>'.TEXT_INFO_EDIT_INTRO);
		$contents[] = array('text' => '<br>' . TEXT_INFO_CONFIGURATION_TITLE . '<br>'.tep_draw_input_field('TR_configuration_title',$cInfo->configuration_title, 'class="form-control form-control-sm" disabled="disabled"', 'disabled'));
		$contents[] = array('text' => '<br>' . TEXT_INFO_CONFIGURATION_NAME . '<br>'.$cInfo->configuration_name.tep_draw_hidden_field('TR_configuration_name',$cInfo->configuration_name));
		$contents[] = array('text' => '<br>' . TEXT_INFO_CONFIGURATION_VALUE . '<br>'.$value_field);
		//$contents[] = array('text' => '<br>' . TEXT_INFO_CONFIGURATION_DESCRIPTION . '<br>'.tep_draw_input_field('configuration_description',$cInfo->configuration_description));
		//$contents[] = array('text' => '<br>' . TEXT_INFO_CONFIGURATION_DESCRIPTION . '<br>'.$cInfo->configuration_description);
		$contents[] = array('text' => '' . TEXT_INFO_CONFIGURATION_PRIORITY . ''.tep_draw_input_field('IN_configuration_priority',$cInfo->priority,'class="form-control form-control-sm mb-2"'));
		 $contents[] = array('align' => 'left', 'text' => '<div>'
     .tep_button_submit('btn btn-primary',IMAGE_UPDATE).'
     <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_CONFIGURATION, 'gid=' . $gid . '&id=' . $cInfo->id ). '">
     '.IMAGE_CANCEL.'</a></div>');
  break;
 default:
  if (isset($cInfo) && is_object($cInfo))
		{
   $heading[] = array('text' => '<b>' . tep_db_output($cInfo->configuration_title) . '</b>');
   $contents[] = array('align' => 'left', 'text' => '
   <a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_CONFIGURATION, 'gid=' . $gid . '&id=' . $cInfo->id . '&action=edit') . '">
   '.IMAGE_EDIT.'</a>');
   $contents[] = array('text' => '<br>' . tep_db_output($cInfo->configuration_description));
   $contents[] = array('text' => '<br>'.TEXT_INFO_DATE_ADDED. tep_date_long($cInfo->inserted));
   if (tep_not_null($cInfo->updated))
			 $contents[] = array('text' => '<br>' .TEXT_INFO_DATE_UPDATED. tep_date_long($cInfo->updated));
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
$template->assign_vars(array(
 //'HEADING_TITLE'=>HEADING_TITLE,
 'configuration_group_title'=>$configuration_group_title,
 'new_button'=>'<a class="btn btn-primary float-right" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_CONFIGURATION, 'gid=' . $gid . '&action=new') .'"><i class="bi bi-plus-lg me-2"></i>'. IMAGE_NEW.'</a>',
 'TABLE_HEADING_CONFIGURATION_TITLE'=>TABLE_HEADING_CONFIGURATION_TITLE,
 'TABLE_HEADING_CONFIGURATION_VALUE'=>TABLE_HEADING_CONFIGURATION_VALUE,
 'TABLE_HEADING_ACTION'=>TABLE_HEADING_ACTION,
 'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
 'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('configuration');
?>