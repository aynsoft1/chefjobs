<?
/*
***********************************************************
***********************************************************
**********# Name          : Kamal Kumar Sahoo   #**********
**********# Company       : Aynsoft             #**********
**********# Date Created  : 23/07/05            #**********
**********# Date Modified : 23/07/05            #**********
**********# Copyright (c) www.aynsoft.com 2005  #**********
***********************************************************
***********************************************************
*/
include_once("../include_files.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_FILE_MANAGER);
$template->set_filenames(array('file_manager' => 'admin1_file_manager.htm','file_manager1' => 'admin1_file_manager1.htm'));
include_once(FILENAME_ADMIN_BODY);

if (!isset($_SESSION['current_path'])) 
{
 $_SESSION['current_path'] = PATH_TO_MAIN_PHYSICAL;
}

if (isset($_GET['goto'])) 
{
 $_SESSION['current_path'] = $_GET['goto'];
 tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FILE_MANAGER));
}

if (strstr($_SESSION['current_path'], '..')) 
 $_SESSION['current_path'] = PATH_TO_MAIN_PHYSICAL;

if (!is_dir($_SESSION['current_path'])) 
 $_SESSION['current_path'] = PATH_TO_MAIN_PHYSICAL;

if (!preg_match('|^' . PATH_TO_MAIN_PHYSICAL.'|', $_SESSION['current_path'])) 
 $_SESSION['current_path'] = PATH_TO_MAIN_PHYSICAL;

$action = (isset($_GET['action']) ? $_GET['action'] : '');

if (tep_not_null($action)) 
{
 switch ($action) 
 {
  case 'reset':
   unset($_SESSION['current_path']);
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FILE_MANAGER));
  break;
  case 'deleteconfirm':
   if (strstr($_GET['info'], '..')) 
    tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FILE_MANAGER));
   tep_remove($_SESSION['current_path'] . '/' . $_GET['info']);
   if (!$tep_remove_error) 
    tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FILE_MANAGER));
  break;
  case 'insert':
   if (mkdir($_SESSION['current_path'] . '/' . $_POST['folder_name'], 0777)) 
   {
    tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FILE_MANAGER, 'info=' . urlencode($_POST['folder_name'])));
   }
   break;
   case 'save':
    if ($fp = fopen($_SESSION['current_path'] . '/' . $_POST['filename'], 'w+')) 
    {
	 tep_site_magic_quotes();
     fputs($fp, stripslashes(addslashes($_POST['file_contents'])));
     fclose($fp);
     tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FILE_MANAGER, 'info=' . urlencode($_POST['filename'])));
    }
   break;
   case 'processuploads':
    for ($i=1; $i<6; $i++) 
    {
     if (tep_not_null($_FILES['file_'.$i]['name'])) 
     {
      $obj_file=new upload('file_' . $i, $_SESSION['current_path']);
      $file_name=tep_db_input($obj_file->filename);
      copy($_SESSION['current_path'].$file_name,$_SESSION['current_path'].substr($file_name,14));
      @unlink($_SESSION['current_path'].$file_name);
     }
    }
    tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FILE_MANAGER));
   break;
   case 'download':
    header('Content-type: application/x-octet-stream');
    header('Content-disposition: attachment; filename=' . urldecode($_GET['filename']));
    readfile($_SESSION['current_path'] . '/' . urldecode($_GET['filename']));
    exit;
  break;
  case 'upload':
  case 'new_folder':
  case 'new_file':
   $directory_writeable = true;
   if (!is_writeable($_SESSION['current_path'])) 
   {
    $directory_writeable = false;
    $messageStack->add(sprintf(ERROR_DIRECTORY_NOT_WRITEABLE, $_SESSION['current_path']), 'error');
   }
  break;
  case 'edit':
   if (strstr($_GET['info'], '..')) 
    tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FILE_MANAGER));
   $file_writeable = true;
   if (!is_writeable($_SESSION['current_path'] . '/' . $_GET['info'])) 
   {
    $file_writeable = false;
    $messageStack->add(sprintf(ERROR_FILE_NOT_WRITEABLE, $_SESSION['current_path'] . $_GET['info']), 'error');
   }
  break;
  case 'delete':
   if (strstr($_GET['info'], '..')) 
    tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FILE_MANAGER));
  break;
 }
}
$in_directory = substr(substr(PATH_TO_MAIN_PHYSICAL, strrpos(PATH_TO_MAIN_PHYSICAL, '/')), 1);
$current_path_array = explode('/', $_SESSION['current_path']);
$document_root_array = explode('/', PATH_TO_MAIN_PHYSICAL);
$goto_array = array(array('id' => PATH_TO_MAIN_PHYSICAL, 'text' => $in_directory));
for ($i=0, $n=sizeof($current_path_array); $i<$n; $i++) 
{
 if ((isset($document_root_array[$i]) && ($current_path_array[$i] != $document_root_array[$i])) || !isset($document_root_array[$i])) 
 {
  $goto_array[] = array('id' => implode('/', array_slice($current_path_array, 0, $i+1)), 'text' => $current_path_array[$i]);
 }
}
////////////////// middle starts ///////////
if ( (($action == 'new_file') && ($directory_writeable == true)) || ($action == 'edit') ) 
{
 if (isset($_GET['info']) && strstr($_GET['info'], '..')) 
  tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FILE_MANAGER));
 if (!isset($file_writeable)) 
  $file_writeable = true;
 $file_contents = '';
 if ($action == 'new_file') 
 {
  $filename_input_field = tep_draw_input_field('filename');
 } 
 elseif ($action == 'edit') 
 {
  if ($file_array = file($_SESSION['current_path'] . '/' . $_GET['info'])) 
  {
   $file_contents = htmlspecialchars(implode('', $file_array));
  }
  $filename_input_field = $_GET['info'] . tep_draw_hidden_field('filename', $_GET['info']);
 }
 $new_button="";
 if ($file_writeable == true) 



  $new_button=tep_draw_submit_button_field('', IMAGE_SAVE,'class="btn btn-primary"') . '

  <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FILE_MANAGER, (isset($_GET['info']) ? 'info=' . urlencode($_GET['info']) : '')) . '">' . IMAGE_CANCEL . '</a>';
} 
else 
{
 $showuser = (function_exists('posix_getpwuid') ? true : false);
 $contents = array();
 $dir = dir($_SESSION['current_path']);
 while ($file = $dir->read()) 
 {
  if ( ($file != '.') && ($file != 'CVS') && ( ($file != '..') || ($_SESSION['current_path'] != PATH_TO_MAIN_PHYSICAL) ) ) 
  {
   $file_size = number_format(filesize($_SESSION['current_path'] . '/' . $file)) . ' bytes';
   $permissions = tep_get_file_permissions(fileperms($_SESSION['current_path'] . '/' . $file));
   if ($showuser) 
   {
    $user = @posix_getpwuid(fileowner($_SESSION['current_path'] . '/' . $file));
    $group = @posix_getgrgid(filegroup($_SESSION['current_path'] . '/' . $file));
   } 
   else 
   {
    $user = $group = array();
   }
   $contents[] = array('name' => $file,
                       'is_dir' => is_dir($_SESSION['current_path'] . '/' . $file),
                       'last_modified' => strftime(DATE_TIME_FORMAT, filemtime($_SESSION['current_path'] . '/' . $file)),
                       'size' => $file_size,
                       'permissions' => $permissions,
                       'user' => $user['name'],
                       'group' => $group['name']);
  }
 }
 function tep_cmp($a, $b) 
 {
  return strcmp( ($a['is_dir'] ? 'D' : 'F') . $a['name'], ($b['is_dir'] ? 'D' : 'F') . $b['name']);
 }
 usort($contents, 'tep_cmp');
 $alternate=1;
 for ($i=0, $n=sizeof($contents); $i<$n; $i++) 
 {
  if ((!isset($_GET['info']) || (isset($_GET['info']) && ($_GET['info'] == $contents[$i]['name']))) && !isset($fInfo) && ($action != 'upload') && ($action != 'new_folder')) 
  {
   $fInfo = new objectInfo($contents[$i]);
  }
  if ($contents[$i]['name'] == '..') 
  {
   $goto_link = substr($_SESSION['current_path'], 1, strrpos($_SESSION['current_path'], '/'));
  } 
  else 
  {
   $goto_link = (substr($_SESSION['current_path'],-1)=='/'?$_SESSION['current_path'] .$contents[$i]['name']:$_SESSION['current_path']. '/'. $contents[$i]['name']);
  }
  if (isset($fInfo) && is_object($fInfo) && ($contents[$i]['name'] == $fInfo->name)) 
  {
   if ($fInfo->is_dir) 
   {
    $row_selected=' id="defaultSelected" class="table-secondary dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
    $onclick_link = 'goto=' . $goto_link;
   } 
   else 
   {
    $row_selected=' id="defaultSelected" class="table-secondary dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
    $onclick_link = 'info=' . urlencode($fInfo->name) . '&action=edit';
   }
  } 
  else 
  {
   $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
   $onclick_link = 'info=' . urlencode($contents[$i]['name']);
  }
  $alternate++;
  if ($contents[$i]['is_dir']) 
  {
   if ($contents[$i]['name'] == '..') 
   {
    $icon = tep_image(PATH_TO_IMAGE.'previous_level.gif', ICON_PREVIOUS_LEVEL);
   } 
   else 
   {
    $icon = (isset($fInfo) && is_object($fInfo) && ($contents[$i]['name'] == $fInfo->name) ? tep_image(PATH_TO_IMAGE.'current_folder.gif', ICON_CURRENT_FOLDER) : tep_image(PATH_TO_IMAGE.'folder.gif', ICON_FOLDER));
   }
   $link = tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FILE_MANAGER, 'goto=' . $goto_link);
  } 
  else 
  {
   $icon = tep_image(PATH_TO_IMAGE.'file_download.gif', ICON_FILE_DOWNLOAD);
   $link = tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FILE_MANAGER, 'action=download&filename=' . urlencode($contents[$i]['name']));
  }
  $delete_string="";
  if ($contents[$i]['name'] != '..')
  {
   $delete_string='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FILE_MANAGER, 'info=' . urlencode($contents[$i]['name']) . '&action=delete') . '">' . tep_image(PATH_TO_IMAGE.'delete.gif', ICON_DELETE) . '</a>&nbsp;';
  }
  if (isset($fInfo) && is_object($fInfo) && ($fInfo->name == $contents[$i]['name'])) 
  { 
   $delete_string.=tep_image(PATH_TO_IMAGE.'icon_arrow_right.gif');
  } 
  else 
  { 
   $delete_string.='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FILE_MANAGER, 'info=' . urlencode($contents[$i]['name'])) . '">' . tep_image(PATH_TO_IMAGE.'icon_info.gif', IMAGE_ICON_INFO) . '</a>';
  }
  $template->assign_block_vars('file_manager', array( 'row_selected' => $row_selected,
   'action' => $action_image,
   'TABLE_HEADING_ONCLICK' => 'onclick="document.location.href=\''.tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FILE_MANAGER, $onclick_link).'\'"',
   'TABLE_HEADING_FILENAME1' => '<a href="' . $link . '">' . $icon . '</a>&nbsp;' . $contents[$i]['name'],
   'TABLE_HEADING_SIZE1' => ($contents[$i]['is_dir'] ? '&nbsp;' : $contents[$i]['size']),
   'TABLE_HEADING_PERMISSIONS1' => $contents[$i]['permissions'],
   'TABLE_HEADING_USER1' => $contents[$i]['user'],
   'TABLE_HEADING_GROUP1' => $contents[$i]['group'],
   'TABLE_HEADING_LAST_MODIFIED1' => $contents[$i]['last_modified'],
   'TABLE_HEADING_ACTION1' => $delete_string,
   ));
 }
 $new_button='<a class="btn btn-secondary mr-1" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FILE_MANAGER, 'action=reset') . '">' .IMAGE_RESET . '</a>';
 $new_button.='<a class="btn btn-primary mr-1 float-right" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FILE_MANAGER, (isset($_GET['info']) ? 'info=' . urlencode($_GET['info']) . '&' : '') . 'action=upload') . '"><i class="fa fa-upload" aria-hidden="true"></i> ' .IMAGE_UPLOAD . '</a>
 <a class="btn btn-primary mr-1 float-right" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FILE_MANAGER, (isset($_GET['info']) ? 'info=' . urlencode($_GET['info']) . '&' : '') . 'action=new_file') . '"><i class="fa fa-plus" aria-hidden="true"></i> '. IMAGE_NEW_FILE .'</a>
 <a class="btn btn-primary mr-1 float-right" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FILE_MANAGER, (isset($_GET['info']) ? 'info=' . urlencode($_GET['info']) . '&' : '') . 'action=new_folder') . '"><i class="fa fa-plus" aria-hidden="true"></i> '.IMAGE_NEW_FOLDER .'</a>';;
}
//////////
//// right starts
$ADMIN_RIGHT_HTML="";
$heading = array();
$contents = array();
switch ($action) 
{
 case 'delete':
  $heading[] = array('text' => '<div class="text-primary font-weight-bold mb-1">' . $fInfo->name . '</div>');
  $contents = array('form' => tep_draw_form('file', PATH_TO_ADMIN.FILENAME_ADMIN1_FILE_MANAGER, 'info=' . urlencode($fInfo->name) . '&action=deleteconfirm'));
  $contents[] = array('text' => '<div class="mb-1 text-danger">' .TEXT_DELETE_INTRO. '</div>');
  $contents[] = array('text' => '<br><b>' . $fInfo->name . '</b>');
  $contents[] = array('align' => 'left', 'text' => '<br>'



  . tep_draw_submit_button_field('', IMAGE_DELETE,'class="btn btn-primary"') . '
  <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FILE_MANAGER, (tep_not_null($fInfo->name) ? 'info=' . urlencode($fInfo->name) : '')) . '">' .IMAGE_CANCEL . '</a>');
 break;
 case 'new_folder':
  $heading[] = array('text' => '<div class="text-primary font-weight-bold mb-1">' . TEXT_NEW_FOLDER . '</div>');
  $contents = array('form' => tep_draw_form('folder', PATH_TO_ADMIN.FILENAME_ADMIN1_FILE_MANAGER, 'action=insert'));
  $contents[] = array('text' => '<div class="mb-1 text-danger">' .TEXT_NEW_FOLDER_INTRO. '</div>');
  $contents[] = array('text' => '<br>' . TEXT_FILE_NAME . '<br>' . tep_draw_input_field('folder_name','' ,'class="form-control form-control-sm"'));
  $contents[] = array('align' => 'left', 'text' => '<br>'


  
  . (($directory_writeable == true) ? tep_draw_submit_button_field('', IMAGE_SAVE,'class="btn btn-primary"') : '') . '
  <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FILE_MANAGER, (isset($_GET['info']) ? 'info=' . urlencode($_GET['info']) : '')) . '">' .IMAGE_CANCEL . '</a>');
 break;
 case 'upload':
  $heading[] = array('text' => '<div class="text-primary font-weight-bold mb-1">' . TEXT_INFO_HEADING_UPLOAD . '</div>');
  $contents = array('form' => tep_draw_form('file', PATH_TO_ADMIN.FILENAME_ADMIN1_FILE_MANAGER, 'action=processuploads', 'post', 'enctype="multipart/form-data"'));
  $contents[] = array('text' => '<div class="mb-1 text-danger">' .TEXT_UPLOAD_INTRO. '</div>');
  $file_upload = '';
  for ($i=1; $i<6; $i++) 
   $file_upload .= tep_draw_file_field('file_' . $i) . '<br><br>';
  $contents[] = array('text' => '<br>' . $file_upload);
  $contents[] = array('align' => 'left', 'text' => '<br>'

  
  . (($directory_writeable == true) ? tep_draw_submit_button_field(PATH_TO_BUTTON.'', IMAGE_UPLOAD, 'class="btn btn-primary"') : '') . '
  <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FILE_MANAGER, (isset($_GET['info']) ? 'info=' . urlencode($_GET['info']) : '')) . '">' .IMAGE_CANCEL. '</a>');
 break;
 default:
  if (isset($fInfo) && is_object($fInfo)) 
  {
   $heading[] = array('text' => '<div class="text-primary font-weight-bold mb-1">' . $fInfo->name . '</div>');
   if (!$fInfo->is_dir) 
    $contents[] = array('align' => 'left', 'text' => '<a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FILE_MANAGER, 'info=' . urlencode($fInfo->name) . '&action=edit') . '">' .IMAGE_EDIT . '</a>');
   $contents[] = array('text' => '<div class="mb-1 text-danger">' . TEXT_FILE_NAME .  $fInfo->name .'</div>');
   if (!$fInfo->is_dir) 
    $contents[] = array('text' => '<br>' . TEXT_FILE_SIZE . ' <b>' . $fInfo->size . '</b>');
   $contents[] = array('text' => '<br>' . TEXT_LAST_MODIFIED . ' ' . $fInfo->last_modified);
  }
 }
 ////
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

if ( (($action == 'new_file') && ($directory_writeable == true)) || ($action == 'edit') ) 
{
 $template->assign_vars(array(
  'HEADING_TITLE'=>HEADING_TITLE,
  'form'=>tep_draw_form('new_file', PATH_TO_ADMIN.FILENAME_ADMIN1_FILE_MANAGER, 'action=save'),
  'TEXT_FILE_NAME'=>TEXT_FILE_NAME,
  'TEXT_FILE_NAME1'=>$filename_input_field,
  'TEXT_FILE_CONTENTS'=>TEXT_FILE_CONTENTS,
  'TEXT_FILE_CONTENTS1'=>tep_draw_textarea_field('file_contents', 'soft', '90', '10', $file_contents, (($file_writeable) ? 'class="form-control form-control-sm"' : 'readonly')),
  'new_button'=>$new_button,
  'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
  'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
  'update_message'=>$messageStack->output()));
 $template->pparse('file_manager1');
}
else
{
 $template->assign_vars(array(
  'HEADING_TITLE'=>HEADING_TITLE,
  'form'=>tep_draw_form('goto', PATH_TO_ADMIN.FILENAME_ADMIN1_FILE_MANAGER, '', 'get'),
  'path'=>$_SESSION['current_path'],
  'directory_name'=>tep_draw_pull_down_menu('goto', $goto_array, $_SESSION['current_path'], 'onChange="this.form.submit();"'),
  'TABLE_HEADING_FILENAME'=>TABLE_HEADING_FILENAME,
  'TABLE_HEADING_SIZE'=>TABLE_HEADING_SIZE,
  'TABLE_HEADING_PERMISSIONS'=>TABLE_HEADING_PERMISSIONS,
  'TABLE_HEADING_USER'=>TABLE_HEADING_USER,
  'TABLE_HEADING_GROUP'=>TABLE_HEADING_GROUP,
  'TABLE_HEADING_LAST_MODIFIED'=>TABLE_HEADING_LAST_MODIFIED,
  'TABLE_HEADING_ACTION'=>TABLE_HEADING_ACTION,
  'new_button'=>$new_button,
  'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
  'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
  'update_message'=>$messageStack->output()));
 $template->pparse('file_manager');
}
?>