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
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_SITE_BACKUP);
$template->set_filenames(array('backup' => 'admin1_site_backup.htm'));
include_once(FILENAME_ADMIN_BODY);

///////////////////////////////////
$action = (isset($_GET['action']) ? $_GET['action'] : '');

if (tep_not_null($action)) 
{
 switch ($action) 
	{
		case 'backupnow':
   set_time_limit(0);
   $backup_file = 'bk_'.date('YmdHis').".tar.gz";
   if (isset($_POST['download']) && ($_POST['download'] == 'yes')) 
   {
    exec("tar -zcvpf ".PATH_TO_MAIN_PHYSICAL_SITE_BACKUP.$backup_file." --directory ".PATH_TO_MAIN_PHYSICAL."  --exclude=site_backup .");
    header('Content-type: application/x-octet-stream');
    header('Content-disposition: attachment; filename=' . $backup_file);
    readfile(PATH_TO_MAIN_PHYSICAL_SITE_BACKUP . $backup_file);
    unlink(PATH_TO_MAIN_PHYSICAL_SITE_BACKUP . $backup_file);
    exit;
   } 
   else 
   {
    exec("tar -zcvpf ".PATH_TO_MAIN_PHYSICAL_SITE_BACKUP.$backup_file." --directory ".PATH_TO_MAIN_PHYSICAL."  --exclude=site_backup .");
    $messageStack->add_session(SUCCESS_SITE_BACKUP_SAVED, 'success');
    tep_redirect(FILENAME_ADMIN1_SITE_BACKUP);
   }
		break;
		case 'download':
		 $extension = substr($_GET['file'], -3);
		 if ( ($extension == 'zip') || ($extension == '.gz')) 
		 {
			 if ($fp = fopen(PATH_TO_MAIN_PHYSICAL_SITE_BACKUP . $_GET['file'], 'rb')) 
			 {
				 $buffer = fread($fp, filesize(PATH_TO_MAIN_PHYSICAL_SITE_BACKUP . $_GET['file']));
				 fclose($fp);
				 header('Content-type: application/x-octet-stream');
				 header('Content-disposition: attachment; filename=' . $_GET['file']);
				 echo $buffer;
				 exit;
			 }
		 } 
		 else 
		 {
			 $messageStack->add(ERROR_DOWNLOAD_LINK_NOT_ACCEPTABLE, 'error');
		 }
		break;
		case 'deleteconfirm':
   if (strstr($_GET['file'], '..')) 
   {
    header("location:".FILENAME_ADMIN1_SITE_BACKUP);
    exit;
   }
   @unlink(PATH_TO_MAIN_PHYSICAL_SITE_BACKUP .  $_GET['file']);
   $messageStack->add_session(SUCCESS_BACKUP_DELETED, 'success');
   header("location:".FILENAME_ADMIN1_SITE_BACKUP);
   exit;
		break;
	}
}
/////////////////////////////////
// check if the backup directory exists
$dir_ok = false;
if (is_dir(PATH_TO_MAIN_PHYSICAL_SITE_BACKUP)) 
{
 if (is_writeable(PATH_TO_MAIN_PHYSICAL_SITE_BACKUP)) 
	{
  $dir_ok = true;
 } 
	else 
	{
  $messageStack->add(ERROR_BACKUP_DIRECTORY_NOT_WRITEABLE, 'error');
 }
} 
else 
{
 $messageStack->add(ERROR_BACKUP_DIRECTORY_DOES_NOT_EXIST, 'error');
}
//////////////
if ($dir_ok == true) 
{
 $dir = dir(PATH_TO_MAIN_PHYSICAL_SITE_BACKUP);
 $contents = array();
 while ($file = $dir->read()) 
	{
  if (!is_dir(PATH_TO_MAIN_PHYSICAL_SITE_BACKUP . $file) && $file!='index.php') 
		{
   $contents[] = $file;
  }
	}
	sort($contents);
	$n=sizeof($contents);
	if($n > 0)
	{
  $alternate=1;
		for ($i=0; $i<$n; $i++) 
		{
			$entry = $contents[$i];
			$check = 0;
			if ((!isset($_GET['file']) || (isset($_GET['file']) && ($_GET['file'] == $entry))) && !isset($buInfo) && ($action != 'backup')) 
			{
				$file_array['file'] = $entry;
				$file_array['date'] = date('dS M, Y', filemtime(PATH_TO_MAIN_PHYSICAL_SITE_BACKUP . $entry));
				$file_array['size'] = number_format(filesize(PATH_TO_MAIN_PHYSICAL_SITE_BACKUP . $entry)) . ' bytes';
				switch (substr($entry, -3)) 
				{
					case 'zip': $file_array['compression'] = 'ZIP'; break;
					case '.gz': $file_array['compression'] = 'GZIP'; break;
					default: $file_array['compression'] = "None"; break;
				}
				$buInfo = new objectInfo($file_array);
			}
			if (isset($buInfo) && is_object($buInfo) && ($entry == $buInfo->file)) 
			{
				$row_selected=' id="defaultSelected" class="table-secondary dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
				$onclick_link = '';
			} 
			else 
			{
				$row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
				$onclick_link = '?file=' . $entry;
			}
   $alternate++;
			if (isset($buInfo) && is_object($buInfo) && ($entry == $buInfo->file)) 
			{ 
				$action_image=tep_image(PATH_TO_IMAGE.'icon_arrow_right.gif',''); 
			}
			else
			{ 
				$action_image='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_SITE_BACKUP, 'file=' . $entry) . '">'.tep_image(PATH_TO_IMAGE.'icon_info.gif',IMAGE_INFO).'</a>'; 
			}
   $template->assign_block_vars('backup', array( 'row_selected' => $row_selected,
    'column_selected' => 'onclick="document.location.href=\''.FILENAME_ADMIN1_SITE_BACKUP.$onclick_link.'\'"',
    'title' => '<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_SITE_BACKUP,'action=download&file=' . $entry) . '">'.tep_image(PATH_TO_IMAGE.'file_download.gif',IMAGE_DOWNLOAD).'</a>&nbsp;' . $entry,
    'date' => date("d/m/y H:i:s", filemtime(PATH_TO_MAIN_PHYSICAL_SITE_BACKUP . $entry)),
    'size' => number_format(filesize(PATH_TO_MAIN_PHYSICAL_SITE_BACKUP . $entry))." bytes",
    'action' => $action_image,
    'name' => tep_db_output($user_category['user_category_name']),
    ));
		}
		$dir->close();
	}
}
$backup_buttons='';
if ( ($action != 'backup') && (isset($dir)) ) 
 $backup_buttons='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_SITE_BACKUP, 'action=backup') . '">'.tep_button_submit('btn btn-primary',IMAGE_BACKUP).'</a>'; 

//// for right side
$ADMIN_RIGHT_HTML="";

$heading = array();
$contents = array();

switch ($action) 
{
 case 'backup':
  $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_BACKUP . '</b>');
  $contents = array('form' => tep_draw_form('backup', PATH_TO_ADMIN.FILENAME_ADMIN1_SITE_BACKUP, 'action=backupnow'));
  $contents[] = array('text' => TEXT_INFO_NEW_BACKUP);
  if ($dir_ok == true) 
  {
   $contents[] = array('text' => '<br>' . tep_draw_checkbox_field('download', 'yes') . ' ' . TEXT_INFO_DOWNLOAD_ONLY . '*<br><br>*' . TEXT_INFO_BEST_THROUGH_HTTPS);
  } 
  else 
  {
   $contents[] = array('text' => '<br>' . tep_draw_radio_field('download', 'yes', true) . ' ' . TEXT_INFO_DOWNLOAD_ONLY . '*<br><br>*' . TEXT_INFO_BEST_THROUGH_HTTPS);
  }
  $contents[] = array('align' => 'center', 'text' => '<br>' . tep_button_submit('btn btn-primary',IMAGE_BACKUP) . ' <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_SITE_BACKUP) . '">' . IMAGE_CANCEL . '</a>');
 break;
 case 'delete':
  $heading[] = array('text' => '<b>' . $buInfo->date . '</b>');
  $contents = array('form' => tep_draw_form('delete', PATH_TO_ADMIN.FILENAME_ADMIN1_SITE_BACKUP, 'file=' . $buInfo->file . '&action=deleteconfirm'));
  $contents[] = array('text' => TEXT_DELETE_INTRO);
  $contents[] = array('text' => '<br><b>' . $buInfo->file . '</b>');
  $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit(PATH_TO_BUTTON.'button_confirm.gif', IMAGE_CONFIRM) . ' <a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_SITE_BACKUP) . '">' . tep_image_button(PATH_TO_BUTTON.'button_cancel.gif', IMAGE_CANCEL) . '</a>');
 break;
 default:
  if (isset($buInfo) && is_object($buInfo)) 
  {
   $heading[] = array('text' => '<b>' . $buInfo->date . '</b>');
   $contents[] = array('align' => 'center', 'text' => '<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_SITE_BACKUP, 'file=' . $buInfo->file . '&action=delete') . '">' . tep_image_button(PATH_TO_BUTTON.'button_delete.gif', IMAGE_DELETE) . '</a>');
   $contents[] = array('text' => '<br>' . TEXT_INFO_DATE . ' ' . $buInfo->date);
   $contents[] = array('text' => TEXT_INFO_SIZE . ' ' . $buInfo->size);
   $contents[] = array('text' => '<br>' . TEXT_INFO_COMPRESSION . ' ' . $buInfo->compression);
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
 'HEADING_TITLE'=>HEADING_TITLE,
 'backup_directory'=>TEXT_BACKUP_DIRECTORY.PATH_TO_MAIN_PHYSICAL_SITE_BACKUP,
 'backup_buttons'=>$backup_buttons,
 'TABLE_HEADING_TITLE'=>TABLE_HEADING_TITLE,
 'TABLE_HEADING_FILE_DATE'=>TABLE_HEADING_FILE_DATE,
 'TABLE_HEADING_FILE_SIZE'=>TABLE_HEADING_FILE_SIZE,
 'TABLE_HEADING_ACTION'=>TABLE_HEADING_ACTION,
 'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
 'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('backup');
?>