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
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_THEME_EDITOR);
$template->set_filenames(array('themes' =>'admin1_theme_editor.htm','themes1' =>'admin1_theme_editor1.htm'));
include_once(FILENAME_ADMIN_BODY);
$action = (isset($_POST['action']) ? $_POST['action'] : '');

if(isset($_POST['theme_name']))
 $theme_name = tep_db_prepare_input($_POST['theme_name']);
else
 $theme_name = tep_db_prepare_input($_GET['theme_name']);

if(isset($_POST['curr_dir']))
 $curr_dir =tep_db_prepare_input($_POST['curr_dir']);
else
$curr_dir =tep_db_prepare_input($_GET['curr_dir']);
$error =false;
//print_r($_POST);
if($action!="")
{
 switch ($action)
	{
  case 'create_folder':

    if(tep_not_null($curr_dir) &&  is_dir(PATH_TO_MAIN_PHYSICAL_THEMES.$theme_name.'/'.$curr_dir))
    {
     $curr_directory = PATH_TO_MAIN_PHYSICAL_THEMES.$theme_name.$curr_dir;
    }
    else
    {
     $curr_directory=PATH_TO_MAIN_PHYSICAL_THEMES.$theme_name;
    }

    if (!is_writeable($curr_directory))
    {
     $error =true;
     $messageStack->add_session(sprintf(ERROR_DIRECTORY_NOT_WRITEABLE,$curr_directory), 'error');
    }
    $folder_name= tep_db_prepare_input($_POST['TR_folder_name']);
    if($folder_name=='')
    {
     $error =true;
     $messageStack->add_session(ERROR_INVALID_DIRECTORY, 'error');
    }
    if(file_exists($curr_directory.'/'.$folder_name))
    {
     $error =true;
     $messageStack->add_session(ERROR_INVALID_DIRECTORY, 'error');
    }
    if(!$error)
    {
     mkdir($curr_directory.'/'.$folder_name,0777);
    }
    tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_THEME_EDITOR,'curr_dir=' . urlencode($curr_dir).'&theme_name=' . urlencode($theme_name)));
  break;
    case 'upload':

    if(tep_not_null($curr_dir) &&  is_dir(PATH_TO_MAIN_PHYSICAL_THEMES.$theme_name.'/'.$curr_dir))
    {
     $curr_directory = PATH_TO_MAIN_PHYSICAL_THEMES.$theme_name.$curr_dir;
    }
    else
    {
     $curr_directory=PATH_TO_MAIN_PHYSICAL_THEMES.$theme_name;
    }
    if (!is_writeable($curr_directory))
    {
     $error =true;
     $messageStack->add(sprintf(ERROR_DIRECTORY_NOT_WRITEABLE,$curr_directory), 'error');
    }
    if(!$error)
    {
     if (tep_not_null($_FILES['TR_file_name']['name']))
     {
      $obj_file=new upload('TR_file_name',$curr_directory);
      $upload_file_name=tep_db_input($obj_file->filename);
      copy($curr_directory.'/'.$upload_file_name,$curr_directory.'/'.substr($upload_file_name,14));
      unlink($curr_directory.'/'.$upload_file_name);
      tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_THEME_EDITOR,'curr_dir=' . urlencode($curr_dir).'&theme_name=' . urlencode($theme_name)));
     }
    }
  break;
  case 'save':

    $file_name     = tep_db_prepare_input($_POST['file']);
    $file_contents = ($_POST['file_contents']);
    if(tep_not_null($curr_dir) &&  is_dir(PATH_TO_MAIN_PHYSICAL_THEMES.$theme_name.'/'.$curr_dir))
    {
     $curr_directory = PATH_TO_MAIN_PHYSICAL_THEMES.$theme_name.$curr_dir;
    }
    else
    {
     $curr_directory=PATH_TO_MAIN_PHYSICAL_THEMES.$theme_name;
    }
    if (!is_writeable($curr_directory.'/'.$file_name))
    {
     $error =true;
     $messageStack->add(sprintf(ERROR_FILE_NOT_WRITEABLE,$curr_directory.'/'.$file_name), 'error');
    }
    if(!$error)
    {
     if ($fp = fopen($curr_directory.'/'.$file_name, 'w+'))
     {
	  tep_site_magic_quotes();
      fputs($fp,stripslashes(str_replace(array('<!--','-->'),array("\n".'<!--','-->'."\n"),addslashes($file_contents))));
      fclose($fp);
      $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
      tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_THEME_EDITOR,'curr_dir=' . urlencode($curr_dir).'&theme_name=' . urlencode($theme_name)));
     }
    }
    else
     $action='edit';
  break;
  case 'download':
    $file_name     = tep_db_prepare_input($_POST['file']);
    if(tep_not_null($curr_dir) &&  is_dir(PATH_TO_MAIN_PHYSICAL_THEMES.$theme_name.'/'.$curr_dir))
    {
     $curr_directory = PATH_TO_MAIN_PHYSICAL_THEMES.$theme_name.$curr_dir;
    }
    else
    {
     $curr_directory=PATH_TO_MAIN_PHYSICAL_THEMES.$theme_name;
    }
    if (!file_exists($curr_directory.'/'.$file_name))
    {
     $error =true;
     $messageStack->add(sprintf(ERROR_FILE_NOT_EXIST,$curr_directory.'/'.$file_name), 'error');
    }
    if(!$error)
    {
     header('Content-type: application/x-octet-stream');
     header('Content-disposition: attachment; filename='.$file_name);
     readfile($curr_directory.'/'.$file_name);
     exit;
    }
    else
     $action='';
   break;
  case 'delete':

    $file_name     = tep_db_prepare_input($_POST['file']);
    $file_contents = ($_POST['file_contents']);
    if(tep_not_null($curr_dir) &&  is_dir(PATH_TO_MAIN_PHYSICAL_THEMES.$theme_name.'/'.$curr_dir))
    {
     $curr_directory = PATH_TO_MAIN_PHYSICAL_THEMES.$theme_name.$curr_dir;
    }
    else
    {
     $curr_directory=PATH_TO_MAIN_PHYSICAL_THEMES.$theme_name;
    }
    if (!is_writeable($curr_directory.'/'.$file_name))
    {
     $error =true;
     $messageStack->add(sprintf(ERROR_FILE_NOT_REMOVEABLE,$curr_directory.'/'.$file_name), 'error');
    }
    if(!$error)
    {
     @unlink($curr_directory.'/'.$file_name);
     $messageStack->add_session(MESSAGE_SUCCESS_DELETED, 'success');
     tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_THEME_EDITOR,'curr_dir=' . urlencode($curr_dir).'&theme_name=' . urlencode($theme_name)));
    }
    else
    $action='';
  break;
 }
}
$themes_array =get_themes();
$themes        =  $themes_array['theme'];
$broken_themes =  $themes_array['broken'];
$theme_list=array();
$total_broken_themes=count($broken_themes);
$total_themes=count($themes);
$theme_directory=(array_keys($themes));
$broken_theme_directory=(array_keys($broken_themes));
$default_theme=MODULE_THEME_DEFAULT_THEME;

foreach($theme_directory as $theme)
{
 $rows=$themes[$theme];
  if($rows['feature']=='mobile-theme')
   continue;
 $theme_list[]=array('id'=>$theme,'text'=>$rows['name']);
}
foreach($broken_theme_directory as $theme)
{
 $rows=$broken_themes[$theme];
 $theme_list[]=array('id'=>$theme,'text'=>$rows['name']);
}
if(!tep_not_null($theme_name))
{
 $theme_name=MODULE_THEME_DEFAULT_THEME;
}
if(!is_dir(PATH_TO_MAIN_PHYSICAL_THEMES.$theme_name))
{
 $theme_name=MODULE_THEME_DEFAULT_THEME;
}
if(tep_not_null($theme_name))
{
 if(isset($themes[$theme_name]))
 {
  $theme_detail=$themes[$theme_name];
  $theme_detail['theme_dir']=$theme_name;
 }
 else
 {
  $theme_detail=$broken_themes[$theme_name];
  $theme_detail['theme_dir']=$theme_name;
 }
}
////////////////////////////////
$hidden_fields  = '';


if(tep_not_null($curr_dir) && $theme_detail['theme_dir']!=$curr_dir && is_dir(PATH_TO_MAIN_PHYSICAL_THEMES.$theme_detail['theme_dir'].'/'.$curr_dir))
{
 $hidden_fields  = tep_draw_hidden_field('curr_dir',$curr_dir);
 $curr_directory = PATH_TO_MAIN_PHYSICAL_THEMES.$theme_detail['theme_dir'].$curr_dir;
}
else
{
 $curr_dir='';
 $hidden_fields  = tep_draw_hidden_field('curr_dir','');
 $curr_directory=PATH_TO_MAIN_PHYSICAL_THEMES.$theme_detail['theme_dir'];
}
$directory_view='default';
$file_editable =false;

if($action =='edit' )
{
 $file_name = tep_db_prepare_input($_POST['file']);
 if(file_exists($curr_directory.'/'.$file_name))
 {
  $file_ext =strtolower(substr($file_name,strrpos($file_name,'.')+1));
  $file_edit = true;
  switch($file_ext)
  {
   case 'gif':
   case 'jpg':
   case 'bmp':
   case 'png':
   $file_content=tep_image(substr($curr_directory.'/'.$file_name,strlen(PATH_TO_MAIN_PHYSICAL)));
    break;
   default:
   $file_edit = true;
   $file_writeable = true;
   $add_script='';
   $css_file=tep_href_link(substr($curr_directory.'/stylesheet.css',strlen(PATH_TO_MAIN_PHYSICAL)));
   if($file_ext=='htm' || $file_ext=='html')
   {
    $add_script='<script language="javascript" type="text/javascript" src="../TinyMCE/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
    <script language="javascript" type="text/javascript">
     tinyMCE.init({
      mode : "exact",
      elements : "file_contents",
      content_css : "'.$css_file.'",
      theme : "advanced",
      theme_advanced_toolbar_location : "top",
      verify_html : "false",
      verify_css_classes : "false",
      plugins : "table,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,zoom,flash,ibrowser",
      theme_advanced_buttons1_add : "fontselect,fontsizeselect,forecolor",
      theme_advanced_buttons2_add_before: "cut,copy,paste,separator",
      theme_advanced_buttons2_add : "separator,insertdate,inserttime,preview,zoom",
      theme_advanced_buttons3_add : "tablecontrols,emotions,iespell,flash,advhr,ibrowser",
      document_base_url : "'.HOST_NAME.'",
      relative_urls : "false",
      remove_script_host : "false",
      plugin_insertdate_dateFormat : "%Y-%m-%d",
      plugin_insertdate_timeFormat : "%H:%M:%S",
      valid_elements : "*[*]",
      extended_valid_elements : "*[*]",
      auto_reset_designmode : "true",
      trim_span_elements : "false",
      apply_source_formatting :"true",
      convert_urls : "false"

     });
    </script>';
   }
   if (!is_writeable($curr_directory.'/'.$file_name))
   {
    $file_writeable = false;
    $messageStack->add(sprintf(ERROR_FILE_NOT_WRITEABLE,$curr_directory.'/'.$file_name), 'error');
   }
   if(!$error )
   {
    if ($file_array = file($curr_directory.'/'.$file_name))
    {
     $file_contents = htmlspecialchars(implode('',$file_array));
    }
   }

   $file_content=$add_script.'<div class="form-group">'
                                .tep_draw_textarea_field('file_contents', 'soft', '120', '25', $file_contents, 'class="form-control form-control-sm"', (($file_writeable) ? '' : 'readonly')).
                              '</div>';
   if ($file_writeable == true)
   $file_content.='<div class="form-group">'.tep_button_submit('btn btn-primary mb-4 float-right',IMAGE_SAVE).'</div>';

   break;
  }
}

}

$dir = dir($curr_directory);
while (false !==($file = $dir->read()))
{
 if ( ($file != '.') && ($file != 'CVS') && ( ($file != '..') || ($curr_directory != PATH_TO_MAIN_PHYSICAL_THEMES.$theme_detail['theme_dir'])) )
 {
  $file_size = number_format(filesize($curr_directory . '/' . $file)) . ' bytes';
  $permissions = tep_get_file_permissions(fileperms($curr_directory . '/' . $file));
  if ($showuser)
  {
   $user = @posix_getpwuid(fileowner($curr_directory . '/' . $file));
   $group = @posix_getgrgid(filegroup($curr_directory . '/' . $file));
  }
  else
  {
   $user = $group = array();
  }

  $contents[] = array('name' => $file,
                       'is_dir' => is_dir($curr_directory . '/' . $file),
                       'last_modified' => strftime(DATE_TIME_FORMAT, filemtime($curr_directory . '/' . $file)),
                       'size' => $file_size,
                       'permissions' => $permissions,
                       'user' => $user['name'],
                       'group' => $group['name']);
  }
 }
 ///////////////////
 function tep_cmp($a, $b)
 {
  return strcmp( ($a['is_dir'] ? 'D' : 'F') . $a['name'], ($b['is_dir'] ? 'D' : 'F') . $b['name']);
 }
 usort($contents, 'tep_cmp');

 if($action =='edit')
 {
  $alternate=1;
  $default_theme_directory=(PATH_TO_MAIN_PHYSICAL_THEMES.$theme_detail['theme_dir']);
  $directory_path=substr($curr_directory,strlen($default_theme_directory));

  for ($i=0, $n=sizeof($contents); $i<$n; $i++)
  {
   if ($contents[$i]['is_dir'])
   {
    if ($contents[$i]['name'] == '..')
    {
     $icon = '<a  href="#" onclick="view_list(\''.substr($directory_path,0,strrpos($directory_path,'/')).'\');">'.tep_image(PATH_TO_IMAGE.'folder.gif', ICON_FOLDER).'</a>';
     $file_link ='<a  href="#" onclick="view_list(\''.substr($directory_path,0,strrpos($directory_path,'/')).'\');" style="color:#00a0d2">';
    }
    else
    {
     $icon = '<a  href="#" onclick="view_list(\''.$directory_path.((substr($directory_path,-1)=='/')?'':'/').$contents[$i]['name'].'\');" >'.tep_image(PATH_TO_IMAGE.'folder.gif',$contents[$i]['name'].' '.ICON_FOLDER).'</a>';
     $file_link ='<a  href="#" onclick="view_list(\''.$directory_path.((substr($directory_path,-1)=='/')?'':'/').$contents[$i]['name'].'\');" style="color:#00a0d2">';
    }
   }
   else
   {
    $icon = '<a  href="#" onclick="set_action(\''.$contents[$i]['name'].'\',\'edit\');">'.tep_image(PATH_TO_IMAGE.'file.gif',$contents[$i]['name'].' '. ICON_FILE).'</a>';
    $file_link ='<a  href="#" onclick="set_action(\''.$contents[$i]['name'].'\',\'edit\');" style="color:#00a0d2">';
   }

   if($default_theme_directory==$curr_directory)
   {
    switch($contents[$i]['name'])
    {
     case 'text.htm':
      $name='Home Page Template';
      break;
     case 'header.php':
      $name='Home Page Header';
      break;
     case 'header_middle.php':
      $name='Page Header Middle';
      break;
     case 'footer.php':
      $name='Home Page Footer';
      break;
     case 'info.txt':
      $name='Theme Information';
      break;
     case 'theme_configuration.php':
      $name='Theme Configration';
      break;
     case 'stylesheet.css':
      $name='Stylesheet';
      break;
     default:
     $name=$contents[$i]['name'];
    }
   }
   else
    $name=$contents[$i]['name'];
   $template->assign_block_vars('file_list',array(
                                'icon'=>$icon.' '.$file_link.$name.'</a>',
                               ));
  }
 }
 else
 {
  $default_theme_directory=(PATH_TO_MAIN_PHYSICAL_THEMES.$theme_detail['theme_dir']);
  $directory_path=substr($curr_directory,strlen($default_theme_directory));
  for ($i=0, $n=sizeof($contents); $i<$n; $i++)
  {
   if ($contents[$i]['is_dir'])
   {
    if ($contents[$i]['name'] == '..')
    {
     $icon      = '<a  href="#" onclick="view_list(\''.substr($directory_path,0,strrpos($directory_path,'/')).'\');">'.tep_image(PATH_TO_IMAGE.'previous_level.gif', ICON_PREVIOUS_LEVEL).'</a>';
     $file_link = '<a  href="#" onclick="view_list(\''.substr($directory_path,0,strrpos($directory_path,'/')).'\');" style="color:#00a0d2">';
     $delete    = '';
     $edit      = '';
    }
    else
    {
     $icon      = '<a  href="#" onclick="view_list(\''.$directory_path.((substr($directory_path,-1)=='/')?'':'/').$contents[$i]['name'].'\');" >'.tep_image(PATH_TO_IMAGE.'folder.gif',$contents[$i]['name'].' '.ICON_FOLDER).'</a>';
     $file_link = '<a  href="#" onclick="view_list(\''.$directory_path.((substr($directory_path,-1)=='/')?'':'/').$contents[$i]['name'].'\');" style="color:#00a0d2">';
     $delete    = '';
     $edit      = '';
    }
   }
   else
   {
    $icon      = '<a  href="#" onclick="set_action(\''.$contents[$i]['name'].'\',\'download\');">'.tep_image(PATH_TO_IMAGE.'file_download.gif',$contents[$i]['name'].' '. ICON_FILE_DOWNLOAD).'</a>';
    $file_link = '<a  href="#" onclick="set_action(\''.$contents[$i]['name'].'\',\'edit\');" style="color:#00a0d2">';
    $delete    = '<a  href="#" onclick="set_action(\''.$contents[$i]['name'].'\',\'delete\');">'.tep_image(PATH_TO_IMAGE.'delete.gif',$contents[$i]['name'].' '. IMAGE_DELETE).'</a>';
    $edit      = '<a  href="#" onclick="set_action(\''.$contents[$i]['name'].'\',\'edit\');">'.tep_image(PATH_TO_IMAGE.'file.gif',$contents[$i]['name'].' '. ICON_FILE).'</a>';
   }

   if($default_theme_directory==$curr_directory)
   {
    switch($contents[$i]['name'])
    {
     case 'text.htm':
      $name='Home Page Template';
      break;
     case 'header.php':
      $name='Page Header';
      break;
     case 'header_middle.php':
      $name='Page Header Middle';
      break;
     case 'footer.php':
      $name='Page Footer';
      break;
     case 'info.txt':
      $name='Theme Information';
      break;
     case 'theme_configuration.php':
      $name='Theme Configration';
      break;
     case 'stylesheet.css':
      $name='Stylesheet';
      break;
     default:
     $name=$contents[$i]['name'];
    }
   }
   else
    $name=$contents[$i]['name'];
   $row_selected =' class="dataTableRow'.($i%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
   $template->assign_block_vars('file_manager',array(
                                'row_selected' => $row_selected,
                                'name'         =>  $icon.' '.$file_link.$name.'</a>',
                                'size'         => ($contents[$i]['is_dir'] ? '&nbsp;' : $contents[$i]['size']),
                                'permissions'  => $contents[$i]['permissions'],
                                'user'         => $contents[$i]['user'],
                                'group'        => $contents[$i]['group'],
                                'last_modified'=> $contents[$i]['last_modified'],
                                'delete'       => $delete,
                                'edit'         => $edit,
                               ));
  }
 }
////////////////

$template->assign_vars(array(
 'HEADING_TITLE'                => HEADING_TITLE,
 'INFO_TEXT_THEME_LIST'         => tep_draw_pull_down_menu('theme_name', $theme_list, $theme_name,''),
 'INFO_TEXT_THEME_DIRECTORY'    => INFO_TEXT_THEME_DIRECTORY,
 'INFO_TEXT_THEME_DIRECTORY1'   => tep_db_output($theme_detail['theme_dir'].(($curr_dir=='')?'/':''.$curr_dir.'/')),
 'INFO_TEXT_THEME_NAME'         => tep_db_output($theme_detail['name']),
 'INFO_TEXT_THEME_SCREENSHOT'   => tep_not_null(!$theme_detail['screenshot'])?'':tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_THEMES."/".$theme_name.'/'.$theme_detail['screenshot']."&size=200"),
 'theme_form'                   => tep_draw_form('thems', PATH_TO_ADMIN.FILENAME_ADMIN1_THEME_EDITOR,'','post',' onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('theme_name',$theme_name).tep_draw_hidden_field('action','').tep_draw_hidden_field('file','').$hidden_fields,
 'upload_form'                  => tep_draw_form('upload', PATH_TO_ADMIN.FILENAME_ADMIN1_THEME_EDITOR,'','post',' onsubmit="return ValidateForm(this)" enctype="multipart/form-data"').tep_draw_hidden_field('theme_name',$theme_name).tep_draw_hidden_field('action','upload').$hidden_fields,
 'folder_form'                  => tep_draw_form('upload', PATH_TO_ADMIN.FILENAME_ADMIN1_THEME_EDITOR,'','post',' onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('theme_name',$theme_name).tep_draw_hidden_field('action','create_folder').$hidden_fields,

 'INFO_TEXT_UPLOAD_FILE_NAME'   => INFO_TEXT_UPLOAD_FILE_NAME,
 'INFO_TEXT_UPLOAD_FILE_NAME1'  => tep_draw_file_field('TR_file_name',true),
 'INFO_TEXT_FOLDER'             => INFO_TEXT_FOLDER,
 'INFO_TEXT_FOLDER1'            => tep_draw_input_field('TR_folder_name','','class="form-control form-control-sm"'),

'UPLOAD_BUTTON'                => tep_button_submit('btn btn-primary',IMAGE_UPLOAD).'
                                  <a class="btn btn-secondary" href="#" onclick="show_content(\'\');">'.IMAGE_CANCEL.'</a>',

'FOLDER_SAVE_BUTTON'           => tep_button_submit('btn btn-primary', IMAGE_SAVE).'
                                  <a class="btn btn-secondary" href="#" onclick="show_content(\'\');">'.IMAGE_CANCEL.'</a>',

'theme_editor_form'            => tep_draw_form('theme_editor_form', PATH_TO_ADMIN.FILENAME_ADMIN1_THEME_EDITOR,'','post',' onsubmit="return ValidateForm(this)"'),
'INFO_TEXT_NEW_BUTTON'         => '<a class="btn btn-primary" href="#upload_file" onclick="show_content(\'upload_file\')">'.IMAGE_UPLOAD . '</a>
                                    <a class="btn btn-secondary" href="#add_folder" onclick="show_content(\'add_folder\')">'. IMAGE_NEW_FOLDER . '</a>',
));

/////////////////////

if($action=='edit')
{
 $template->assign_vars(array(
  'HEADING_TITLE'                => HEADING_TITLE,
  'INFO_TEXT_THEME_LIST'         => tep_draw_pull_down_menu('theme_name', $theme_list, $theme_name,''),
  'INFO_TEXT_THEME_DIRECTORY'    => INFO_TEXT_THEME_DIRECTORY,
  'INFO_TEXT_THEME_DIRECTORY1'   => tep_db_output($theme_detail['theme_dir'].(($curr_dir=='')?'/':''.$curr_dir.'/')),
  'INFO_TEXT_THEME_NAME'         => tep_db_output($theme_detail['name']),
  'INFO_TEXT_THEME_SCREENSHOT'   => tep_not_null(!$theme_detail['screenshot'])?'':tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_THEMES."/".$theme_name.'/'.$theme_detail['screenshot']."&size=200",'','','','class="card-img-top"'),
  'edit_form'                    => tep_draw_form('edit', PATH_TO_ADMIN.FILENAME_ADMIN1_THEME_EDITOR,'','post',' onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('theme_name',$theme_name).tep_draw_hidden_field('action','save').tep_draw_hidden_field('file',$file_name).$hidden_fields,
  'INFO_TEXT_FILE_NAME'          => INFO_TEXT_FILE_NAME,
  'INFO_TEXT_FILE_NAME1'         => tep_db_output($file_name),
  'INFO_TEXT_FILE_CONTENT'       => $file_content,

  'TABLE_HEADING_FILENAME'       => TABLE_HEADING_FILENAME,
  'TABLE_HEADING_SIZE'           => TABLE_HEADING_SIZE,
  'TABLE_HEADING_PERMISSIONS'    => TABLE_HEADING_PERMISSIONS,
  'TABLE_HEADING_USER'           => TABLE_HEADING_USER,
  'TABLE_HEADING_GROUP'          => TABLE_HEADING_GROUP,
  'TABLE_HEADING_LAST_MODIFIED'  => TABLE_HEADING_LAST_MODIFIED,
  'TABLE_HEADING_LAST_MODIFIED'  => TABLE_HEADING_LAST_MODIFIED,

  'button'       => tep_image_submit(PATH_TO_BUTTON.'button_save.gif',IMAGE_SAVE),
  'update_message'=>$messageStack->output()));
 $template->pparse('themes');
}
else
{
 $template->assign_vars(array(
  'HEADING_TITLE'                => HEADING_TITLE,
  'INFO_TEXT_THEME_LIST'         => tep_draw_pull_down_menu('theme_name', $theme_list, $theme_name,'class="form-control form-control-sm"'),
  'INFO_TEXT_THEME_DIRECTORY'    => INFO_TEXT_THEME_DIRECTORY,
  'INFO_TEXT_THEME_DIRECTORY1'   => tep_db_output($theme_detail['theme_dir'].(($curr_dir=='')?'/':''.$curr_dir.'/')),
  'INFO_TEXT_THEME_NAME'         => tep_db_output($theme_detail['name']),
  'INFO_TEXT_THEME_SCREENSHOT'   => tep_not_null(!$theme_detail['screenshot'])?'':tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_THEMES."/".$theme_name.'/'.$theme_detail['screenshot']."&size=200",'','','','class="card-img-top"'),

  'TABLE_HEADING_FILENAME'       => TABLE_HEADING_FILENAME,
  'TABLE_HEADING_SIZE'           => TABLE_HEADING_SIZE,
  'TABLE_HEADING_PERMISSIONS'    => TABLE_HEADING_PERMISSIONS,
  'TABLE_HEADING_USER'           => TABLE_HEADING_USER,
  'TABLE_HEADING_GROUP'          => TABLE_HEADING_GROUP,
  'TABLE_HEADING_LAST_MODIFIED'  => TABLE_HEADING_LAST_MODIFIED,
  'TABLE_HEADING_LAST_MODIFIED'  => TABLE_HEADING_LAST_MODIFIED,
  'update_message'=>$messageStack->output()));
 $template->pparse('themes1');
}
?>