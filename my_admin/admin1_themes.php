<?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik #*******
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
***********************************************************
*/
include_once("../include_files.php");
include_once("../general_functions/theme_functions.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_THEMES);
$template->set_filenames(array('themes' => 'admin1_themes.htm'));
include_once(FILENAME_ADMIN_BODY);
$action = (isset($_POST['action']) ? $_POST['action'] : '');

if($action!="")
{
 switch ($action)
	{
  case 'set_default':
   $theme_name = tep_db_prepare_input($_POST['theme_name']);
   if(check_theme_info(PATH_TO_MAIN_PHYSICAL_THEMES.$theme_name))
   {
    $template_file = PATH_TO_MAIN_PHYSICAL_THEMES.$theme_name.'/text.htm';
    $handle = fopen($template_file, "r");
    $contents = fread($handle, filesize($template_file));
    fclose($handle);

    /// create a template file  starts //
    $handle = fopen(PATH_TO_MAIN_PHYSICAL.PATH_TO_TEMPLATE.'text.htm', "w");
    fwrite($handle,stripslashes($contents));
    fclose($handle);
    /// create a template file  ends //
    if(MODULE_THEME_DEFAULT_THEME!=$theme_name)
    {
     $old_theme_name=MODULE_THEME_DEFAULT_THEME;
     if(file_exists(PATH_TO_MAIN_PHYSICAL_THEMES.MODULE_THEME_DEFAULT_THEME.'/theme_configuration.php'))
     {
      include_once(PATH_TO_MAIN_PHYSICAL_THEMES.MODULE_THEME_DEFAULT_THEME.'/theme_configuration.php');
      $class_name='theme_'.MODULE_THEME_DEFAULT_THEME;
      if(class_exists($class_name))
      {
       $obj=new $class_name;
       if(method_exists($obj,'remove_theme'))
       $obj->remove_theme();
      }
     }
     if(file_exists(PATH_TO_MAIN_PHYSICAL_THEMES.$theme_name.'/theme_configuration.php'))
     {
      include_once(PATH_TO_MAIN_PHYSICAL_THEMES.$theme_name.'/theme_configuration.php');
      $class_name='theme_'.$theme_name;

      if(class_exists($class_name))
      {
       $obj = new $class_name;
       if(method_exists($obj,'install_theme'))
       $obj->install_theme();
      }
     }
     ////////////////////////////////
    }
    tep_db_query("update ".CONFIGURATION_TABLE." set configuration_value='".tep_db_input($theme_name)."' where configuration_name='MODULE_THEME_DEFAULT_THEME'");
    $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
    tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_THEMES,tep_get_all_get_params(array('action','selected_box'))));
   }
   else
   {
    $messageStack->add(INVALID_THEME, 'error');
   }
   break;

 }
}


 $themes_array =get_themes();
 $themes        =  $themes_array['theme'];
 $broken_themes =  $themes_array['broken'];

 $total_broken_themes=count($broken_themes);
 $theme_directory=(array_keys($themes));
 $broken_theme_directory=(array_keys($broken_themes));
 $default_theme=MODULE_THEME_DEFAULT_THEME;
 $i=0;
 $new_theme_array=array();
 $show_default_theme_array=array();

 foreach($theme_directory as $theme)
 {
  $rows=$themes[$theme];
  if($rows['feature']=='mobile-theme')
   continue;
 if($theme==$default_theme)
  $default_theme_id=1;
  else
  $default_theme_id=0;

  if($default_theme_id)
  $row_selected=' class="dataTableRow'.($i%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
  else
   $row_selected=' class="dataTableRow'.($i%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';

  $screenshot         = tep_not_null(!$rows['screenshot'])?'':'<a href="#" onclick="show_image(\''.tep_href_link(FILENAME_IMAGE."?image_name=".PATH_TO_THEMES."/".$theme.'/'.$rows['screenshot']."&size=800").'\')" class="shadow border" title="click me for large view">'.tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_THEMES."/".$theme.'/'.$rows['screenshot']."&size=250").'</a>';
  $screenshot1         = tep_not_null(!$rows['screenshot'])?'':'<a class="" onclick="show_image(\''.tep_href_link(FILENAME_IMAGE."?image_name=".PATH_TO_THEMES."/".$theme.'/'.$rows['screenshot']."&size=800").'\')" title="click me for large view">'.tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_THEMES."/".$theme.'/'.$rows['screenshot']."&size=300",'','','','id="default_main_screenshot" class="" style="border: 2px solid #34a853;border-radius: 7px;"').'</a>';
  $theme_scree_name   = tep_db_output($rows['name']);
  $theme_dir_name     = 'themes/'.tep_db_output($theme);
  $theme_description  = stripslashes($rows['description']);
  $theme_version      = tep_db_output($rows['version']);
  $theme_radio_button = tep_draw_radio_field('theme_name',$theme,'',$default_theme);
  if($default_theme_id)
  $show_default_theme_array= array('id'=>$theme,'default_theme'=>$default_theme_id,'row_selected' =>$row_selected,'screenshot' => $screenshot1,'theme_scree_name'=>$theme_scree_name,'theme_dir_name'=>$theme_dir_name,'theme_description'=>$theme_description,'theme_version'=>$theme_version,'theme_radio_button' =>$theme_radio_button);
  $new_theme_array[]       = array('id'=>$theme,'default_theme'=>$default_theme_id,'row_selected' =>$row_selected,'screenshot' => $screenshot,'theme_scree_name'=>$theme_scree_name,'theme_dir_name'=>$theme_dir_name,'theme_description'=>$theme_description,'theme_version'=>$theme_version,'theme_radio_button' =>$theme_radio_button);
  $i++;
 }
 sort($new_theme_array);
 $total_themes=count($new_theme_array);
 for($i=0;$i<$total_themes;$i++)
 {
  $template->assign_block_vars('themes', array( 'row_selected' => $new_theme_array[$i]['row_selected'],
  'screenshot' =>$new_theme_array[$i]['screenshot'],
  'directory' =>$new_theme_array[$i]['theme_dir_name'],
  'name' =>$new_theme_array[$i]['theme_scree_name'],
  'description' =>$new_theme_array[$i]['theme_description'],
  'version' =>$new_theme_array[$i]['theme_version'],
  'radio_button' =>$new_theme_array[$i]['theme_radio_button'],
  ));
 }
if($total_themes>0)
{
 $screenshot_array =get_site_theme_screen($show_default_theme_array['theme_dir_name']);
 $theme_id=$show_default_theme_array['id'];
 if(is_array($screenshot_array))
 {
  sort($screenshot_array);
 }
 if(count($screenshot_array)>1)
 {
  foreach($screenshot_array as $image)
  {
   $addtional_screenshots.=tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_THEMES."/".$theme_id.'/'.$image."&size=120",'click me for  large view','','','class="theme_screenshots_addtional" onclick="show_screenshot(\''.tep_href_link(FILENAME_IMAGE."?image_name=".PATH_TO_THEMES."/".$theme_id.'/'.$image."&size=350").'\');"').' ';
  }
 }
 $template->assign_vars(array(
   'CURRENT_THEME_SCREENSHOT1' => $addtional_screenshots,
   'CURRENT_THEME_SCREENSHOT' => $show_default_theme_array['screenshot'],
   'CURRENT_THEME_NAME'       => $show_default_theme_array['theme_scree_name'],
   'CURRENT_THEME_DIRECTORY'  => $show_default_theme_array['theme_dir_name'],
   'CURRENT_THEME_DESCRIPTION'=> $show_default_theme_array['theme_description'],
   'CURRENT_THEME_VERSION'    => $show_default_theme_array['theme_version'],
  ));
}

 $i=0;
 foreach($broken_theme_directory as $theme)
 {
  $rows=$broken_themes[$theme];
  $row_selected=' class="dataTableRow'.($i%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
  $template->assign_block_vars('broken_themes', array( 'row_selected' => $row_selected,
  'screenshot' =>tep_not_null(!$rows['screenshot'])?'':tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_THEMES."/".$theme.'/'.$rows['screenshot']."&size=250"),
  'directory' =>'themes/'.tep_db_output($theme),
  'name' => tep_db_output($rows['name']),
  'description' =>tep_db_output($rows['description']),
  'version' =>tep_db_output($rows['version']),
  'error' => nl2br(tep_db_output($rows['error'])),

  ));
  $i++;
 }
/////
$template->assign_vars(array(
 'HEADING_TITLE'                => HEADING_TITLE,
 'INFO_TEXT_BROKEN_THEMES'      => INFO_TEXT_BROKEN_THEMES,
 'TABLE_HEADING_THEME_NAME'     => TABLE_HEADING_THEME_NAME,
 'TABLE_HEADING_THEME_VERSION'  => TABLE_HEADING_THEME_VERSION,
 'TABLE_HEADING_THEME_DEFAULT'  => TABLE_HEADING_THEME_DEFAULT,
 'TABLE_HEADING_THEME_ERROR'    => TABLE_HEADING_THEME_ERROR,
 'INFO_TEXT_THEMES_STYLE'       => ($total_themes>0)?'':'style="display:none"',
 'INFO_TEXT_BROKEN_THEMES_STYLE'=> ($total_broken_themes>0)?'':'style="display:none"',
 'theme_form'   => tep_draw_form('thems', PATH_TO_ADMIN.FILENAME_ADMIN1_THEMES,'','post',' onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action','set_default'),
 'button'       => tep_draw_submit_button_field('','Save','class="btn btn-primary float-right"'),//tep_image_submit(PATH_TO_BUTTON.'button_save.gif',IMAGE_SAVE),
 'INFO_TEXT_LOGO' => tep_href_link(PATH_TO_IMG.DEFAULT_SITE_LOGO),
 'update_message'=>$messageStack->output()));
$template->pparse('themes');
?>