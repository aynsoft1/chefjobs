<?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2009  #**********
***********************************************************
*/
include_once("../include_files.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_MODULE);
$template->set_filenames(array('module_configuration' => 'admin1_module.htm'));
include_once(FILENAME_ADMIN_BODY);


//////////////
$action = (isset($_GET['action']) ? $_GET['action'] : '');

if (tep_not_null($action))
{
 switch ($action)
	{
  case 'save':
   $sql_data_array = array();
   $configuration_name = tep_db_prepare_input($_POST['TR_configuration_name']);
   $configuration_title = tep_db_prepare_input($_POST['TR_configuration_title']);
   $configuration_value = tep_db_prepare_input($_POST['TR_configuration_value']);
   $configuration_description = tep_db_prepare_input($_POST['configuration_description']);
   $priority = tep_db_prepare_input($_POST['IN_configuration_priority']);
  // $sql_data_array['configuration_name'] = $configuration_name;
  // $sql_data_array['configuration_title'] = $configuration_title;
   //$sql_data_array['configuration_description'] = $configuration_description;
   $sql_data_array['priority'] = $priority;
   ///////////////////////////////////
   $id=(int)tep_db_input($_GET['id']);
   $error = false;
   if($row_chek=getAnyTableWhereData(CONFIGURATION_TABLE,"configuration_name='".tep_db_input($configuration_name)."' and configuration_title='".tep_db_input($configuration_title)."' and id!='$id'",'id'))
   {
    $error = true;
    $messageStack->add(MESSAGE_TITLE_NAME_ERROR, 'error');
   }
   else if($row_chek=getAnyTableWhereData(CONFIGURATION_TABLE,"configuration_name='$configuration_name' and id!='$id'",'id'))
   {
    $error = true;
    $messageStack->add(MESSAGE_NAME_ERROR, 'error');
   }
   if($configuration_name =='DEFAULT_SITE_LOGO')
   {
    if(tep_not_null($_FILES['site_logo']['name']))
    {
     if($obj_logo = new upload('site_logo',PATH_TO_MAIN_PHYSICAL.PATH_TO_IMG,'644',array('gif','jpg','png','jpeg')))
					{
					 $site_logo=tep_db_input($obj_logo->filename);
      $file_ext=substr($site_logo,-3);
      $new_file_name='logo.'.$file_ext;
      if(file_exists(PATH_TO_MAIN_PHYSICAL.PATH_TO_IMG.DEFAULT_SITE_LOGO)&& tep_not_null(DEFAULT_SITE_LOGO))
      {
       @unlink(PATH_TO_MAIN_PHYSICAL.PATH_TO_IMG.DEFAULT_SITE_LOGO);
      }
      @copy(PATH_TO_MAIN_PHYSICAL.PATH_TO_IMG.$site_logo,PATH_TO_MAIN_PHYSICAL.PATH_TO_IMG.$new_file_name);
      @unlink(PATH_TO_MAIN_PHYSICAL.PATH_TO_IMG.$site_logo);
      $sql_data_array['configuration_value'] = $new_file_name;
				 }
					else
					{
						$error=true;
  	 	 $messageStack->add(UPLOAD_ERROR, 'error');
					}
    }
   }
 //////////////ADDED CODE FOR FAVICON  /////////////////////////////////////////////////////
   elseif($configuration_name =='DEFAULT_SITE_FAVICON')
   {
    if(tep_not_null($_FILES['site_favicon']['name']))
    {
     if($obj_logo = new upload('site_favicon',PATH_TO_MAIN_PHYSICAL.PATH_TO_IMG,'644',array('ico')))
					{
					 $site_favicon=tep_db_input($obj_logo->filename);
      $file_ext2=substr($site_favicon,-3);
      $new_file_name2='favicon.'.$file_ext2;
      if(file_exists(PATH_TO_MAIN_PHYSICAL.PATH_TO_IMG.DEFAULT_SITE_FAVICON)&& tep_not_null(DEFAULT_SITE_FAVICON))
      {
       @unlink(PATH_TO_MAIN_PHYSICAL.PATH_TO_IMG.DEFAULT_SITE_FAVICON);
      }
      @copy(PATH_TO_MAIN_PHYSICAL.PATH_TO_IMG.$site_favicon,PATH_TO_MAIN_PHYSICAL.PATH_TO_IMG.$new_file_name2);
      @unlink(PATH_TO_MAIN_PHYSICAL.PATH_TO_IMG.$site_favicon);
      $sql_data_array['configuration_value'] = $new_file_name2;
				 }
					else
					{
						$error=true;
  	 	 $messageStack->add(UPLOAD_ERROR, 'error');
					}
    }
   }
///////////////////////////////////////////////////////////////////////////////////////////////////////
  else
    $sql_data_array['configuration_value'] = $configuration_value;


   if(!$error)
   {
    /////// check if it is screeners ////
    $sql_data_array['updated'] = 'now()';
    tep_db_perform(CONFIGURATION_TABLE, $sql_data_array,'update', "id = '" . (int)$id . "'");
    $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
    tep_redirect(FILENAME_ADMIN1_MODULE. '?id=' . $id);
   }
   else
    $action='edit';
  break;
 }
}
///////////// Middle Values
$module_array=array();
$module_array[]=array('name'=>'Site Logo ','field'=>"'DEFAULT_SITE_LOGO'");
$module_array[]=array('name'=>'Site Favicon ','field'=>"'DEFAULT_SITE_FAVICON'");
$module_array[]=array('name'=>'Google Analytices','field'=>"'MOGULE_GOOGLE_ANALYTICES','MOGULE_GOOGLE_ANALYTICES_UA_ID'");
$module_array[]=array('name'=>'Google Map Key','field'=>"'MODULE_GOOGLE_MAP_KEY'");
$module_array[]=array('name'=>'Slider Heading','field'=>"'SLIDER_HEADING1','SLIDER_HEADING2','SLIDER_HEADING3','SLIDER_HEADING4'");
$module_array[]=array('name'=>'Slider Text','field'=>"'SLIDER_TEXT1','SLIDER_TEXT2','SLIDER_TEXT3','SLIDER_TEXT4'");
if(file_exists(PATH_TO_MAIN_PHYSICAL_THEMES.MODULE_THEME_DEFAULT_THEME.'/theme_configuration.php'))
{
 include_once(PATH_TO_MAIN_PHYSICAL_THEMES.MODULE_THEME_DEFAULT_THEME.'/theme_configuration.php');
 $class_name='theme_'.MODULE_THEME_DEFAULT_THEME;
 if(class_exists($class_name))
 {
  $obj=new $class_name;
  if(method_exists($obj,'keys'))
  {
   if(is_array($obj->keys()))
   $module_array[]=array('name'=>'Theme Configuration','field'=>"'".implode("' ,'",$obj->keys())."'");
  }
 }
}
$total_module = count($module_array);
for($i=0;$i<$total_module;$i++)
{
 $configuration_query = tep_db_query("select * from " . CONFIGURATION_TABLE . " where configuration_name in  (".($module_array[$i]['field']).") order by priority");
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
    $row_selected=' id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . FILENAME_ADMIN1_MODULE . '?id=' . $cInfo->id . '&action=edit\'"';
   }
   else
   {
    $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . FILENAME_ADMIN1_MODULE . '?id=' . $configuration['id'] . '\'"';
   }
   if ( (isset($cInfo) && is_object($cInfo)) && ($configuration['id'] == $cInfo->id) )
   {
    $action_image=tep_image(PATH_TO_IMAGE.'icon_arrow_right.gif',IMAGE_EDIT);
   }
   else
   {
    $action_image='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_MODULE, 'id=' . $configuration['id']) . '">'.tep_image(PATH_TO_IMAGE.'icon_info.gif',IMAGE_INFO).'</a>';
   }

   $module_name='';
   if($alternate==1)
   $module_name='<tr> <td colspan="4" style="background-color:#fff;color:0073aa;font-size: 15px!important;font-weight: 600;">'.tep_db_output($module_array[$i]['name']).'</td></tr>';

   $template->assign_block_vars('configuration', array( 'row_selected' => $row_selected,
     'action'      => $action_image,
     'title'       => tep_db_output($configuration['configuration_title']),
     'description' => tep_db_output($configuration['configuration_description']),
     'value'       => tep_db_output($configuration['configuration_value']),//(($configuration['configuration_name']=='DEFAULT_SITE_LOGO')?tep_db_output($configuration['configuration_value']):tep_db_output($configuration['configuration_value'])),
     'module_name' => $module_name,
    ));
    $alternate++;

  }
 }
}

//print_r($cInfo);
//// for right side
$ADMIN_RIGHT_HTML="";

$heading = array();
$contents = array();
switch ($action)
{
 case 'edit':
  if ($cInfo->set_function)
  {
    eval('$value_field = ' . $cInfo->set_function . '"' . tep_db_output($cInfo->configuration_value) . '");');
  }
  else
  {
   if($cInfo->configuration_name=='DEFAULT_SITE_LOGO')
   {
    $value_field  = DEFAULT_SITE_LOGO;
    $value_field .= '<br>'.tep_draw_file_field('site_logo').'<br>'.INFO_TEXT_UPLOAD_PHOTO;
    if(file_exists(PATH_TO_MAIN_PHYSICAL.PATH_TO_IMG.DEFAULT_SITE_LOGO)&& tep_not_null(DEFAULT_SITE_LOGO))
    $value_field .= '<br><div class="admin-logo-preview-size">'.tep_image(PATH_TO_IMG.DEFAULT_SITE_LOGO). '</div>';
   }
///////////////////ADDED CODE/////////////////////////////////////////////////////////////
   elseif($cInfo->configuration_name=='DEFAULT_SITE_FAVICON')
   {
    $value_field  = DEFAULT_SITE_FAVICON;
    $value_field .= '<br>'.tep_draw_file_field('site_favicon').'<br>'.INFO_TEXT_UPLOAD_FAVICON;
    if(file_exists(PATH_TO_MAIN_PHYSICAL.PATH_TO_IMG.DEFAULT_SITE_FAVICON)&& tep_not_null(DEFAULT_SITE_FAVICON))
    $value_field .= '<br>'.tep_image(DEFAULT_SITE_FAVICON);
   }
/////////////////////////////////////////////////////////////////
   // ,tep_draw_textarea_field('TR_configuration_value', true,30,3,$cInfo->configuration_value);
   else
   $value_field=tep_draw_input_field('TR_configuration_value',$cInfo->configuration_value);
  }
  $heading[] = array('text' => '<b>' . tep_db_output($cInfo->configuration_title) . '</b>');
  $contents = array('form' => tep_draw_form('configuration',PATH_TO_ADMIN.FILENAME_ADMIN1_MODULE,'id=' . $cInfo->id . '&action=save', 'post','onsubmit="return ValidateForm(this)" enctype="multipart/form-data"'));
		$contents[] = array('text' => '<span style="color:blue;">'.$cInfo->configuration_description.'</span>');
		$contents[] = array('text' => TEXT_INFO_EDIT_INTRO);
		$contents[] = array('text' => '<br>' . TEXT_INFO_CONFIGURATION_TITLE . '<br>'.tep_draw_input_field('TR_configuration_title',$cInfo->configuration_title,' disabled'));
		$contents[] = array('text' => '<br>' . TEXT_INFO_CONFIGURATION_NAME . '<br>'.tep_db_output($cInfo->configuration_name).tep_draw_hidden_field('TR_configuration_name',$cInfo->configuration_name));
		$contents[] = array('text' => '<br>' . TEXT_INFO_CONFIGURATION_NAME . '<br>'.$cInfo->configuration_name.tep_draw_hidden_field('TR_configuration_name',$cInfo->configuration_name));
		$contents[] = array('text' => '<br>' . TEXT_INFO_CONFIGURATION_VALUE . '<br>'.$value_field);
		$contents[] = array('text' => '<br>' . TEXT_INFO_CONFIGURATION_PRIORITY . '<br>'.tep_draw_input_field('IN_configuration_priority',$cInfo->priority,'size="5" class="form-control form-control-sm mb-2"'));
  $contents[] = array('align' => 'left', 'text' => '
    <div>'
        // .tep_image_submit(PATH_TO_BUTTON.'button_update.gif',IMAGE_UPDATE).'&nbsp;
        .tep_button_submit('btn btn-primary',IMAGE_UPDATE).'
        <a class="btn btn-secondary" href="' 
        . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_MODULE, 'id=' . $cInfo->id ). '">'
        .IMAGE_CANCEL.'</a></div>');
  break;
 default:
  if (isset($cInfo) && is_object($cInfo))
		{
   $heading[] = array('text' => '<strong>' . tep_db_output($cInfo->configuration_title) . '</strong>');
   $contents[] = array('align' => 'left', 'text' => '<a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_MODULE, 'id=' . $cInfo->id . '&action=edit') . '">'
                      .IMAGE_EDIT.'</a>');
   $contents[] = array('text' => '<p>' . tep_db_output($cInfo->configuration_description).'</p>');
   $contents[] = array('text' => '<p>'.TEXT_INFO_DATE_ADDED. tep_date_long($cInfo->inserted).'</p>');
   if (tep_not_null($cInfo->updated))
			 $contents[] = array('text' => '<p>' .TEXT_INFO_DATE_UPDATED. tep_date_long($cInfo->updated).'</p>');
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
 'HEADING_TITLE'=>HEADING_TITLE,
 'TABLE_HEADING_CONFIGURATION_TITLE'=>TABLE_HEADING_CONFIGURATION_TITLE,
 'TABLE_HEADING_CONFIGURATION_DESCRIPTION'=>TABLE_HEADING_CONFIGURATION_DESCRIPTION,
 'TABLE_HEADING_CONFIGURATION_VALUE'=>TABLE_HEADING_CONFIGURATION_VALUE,
 'TABLE_HEADING_ACTION'=>TABLE_HEADING_ACTION,
 'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
 'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('module_configuration');
?>