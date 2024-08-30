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
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_BACKUP);
$template->set_filenames(array('backup' => 'admin1_backup.htm'));
include_once(FILENAME_ADMIN_BODY);

///////////////////////////////////
$action = (isset($_GET['action']) ? $_GET['action'] : '');

if (tep_not_null($action))
{
 switch ($action)
	{
  case 'forget':
  tep_db_query("delete from " . CONFIGURATION_TABLE . " where configuration_name = 'DB_LAST_RESTORE'");
		$messageStack->add_session(SUCCESS_LAST_RESTORE_CLEARED, 'success');
		tep_redirect(FILENAME_ADMIN1_BACKUP);
		break;
		case 'backupnow':
		set_time_limit(0);
		$backup_file = 'db_' . DB_DATABASE . '-' . date('YmdHis') . '.sql';
		$fp = fopen(PATH_TO_MAIN_PHYSICAL_BACKUP . $backup_file, 'w');
		$schema = '# Database backup' . "\n" .
		'# ' . HOST_NAME."\n" .
		'#' . "\n" .
		'# Database Backup For ' . SITE_OWNER . "\n" .
		'# Copyright (c) ' . date('Y') . ' ' . SITE_OWNER . "\n" .
		'#' . "\n" .
		'# Database: ' . DB_DATABASE . "\n" .
		'# Database Server: ' . DB_SERVER . "\n" .
		'#' . "\n" .
		'# Backup Date: ' . date("d/m/Y H:i:s") . "\n\n";
		fputs($fp, $schema);
		$tables_query = tep_db_query('show tables');
		while ($tables = tep_db_fetch_array($tables_query))
		{ 
			//list(,$table) = each($tables); // old code
    		foreach ($tables as $key => $table)
			$schema = "\n".'drop table if exists ' . $table . ';' . "\n" .
													'create table ' . $table . ' (' . "\n";
			$table_list = array();
			$fields_query = tep_db_query("show fields from " . $table);
			while ($fields = tep_db_fetch_array($fields_query))
			{
				$table_list[] = $fields['Field'];
				$schema .= '  ' . $fields['Field'] . ' ' . $fields['Type'];
				if (strlen($fields['Default']) > 0)
					$schema .= ' default \'' . $fields['Default'] . '\'';
				if ($fields['Null'] != 'YES')
					$schema .= ' not null';
				if (isset($fields['Extra']))
					$schema .= ' ' . $fields['Extra'];
				$schema .= ',' . "\n";
			}
   tep_db_free_result($fields_query);
			$schema = preg_replace("/,\n$/", '', $schema);
			// add the keys
			$index = array();
			$keys_query = tep_db_query("show keys from " . $table);
			while ($keys = tep_db_fetch_array($keys_query))
			{
				$kname = $keys['Key_name'];
				if (!isset($index[$kname]))
				{
					$index[$kname] = array('unique' => !$keys['Non_unique'],
																												'columns' => array());
				}
				$index[$kname]['columns'][] = $keys['Column_name'];
			}
   tep_db_free_result($keys_query); 
			//while (list($kname, $info) = each($index))  /// old code
             foreach ($index as $kname => $info)         // new add code

			{
				$schema .= ',' . "\n";
				$columns = implode( ', ',$info['columns']);
				if ($kname == 'PRIMARY')
				{
					$schema .= '  PRIMARY KEY (' . $columns . ')';
				}
				elseif ($info['unique'])
				{
					$schema .= '  UNIQUE ' . $kname . ' (' . $columns . ')';
				}
				else
				{
					$schema .= '  KEY ' . $kname . ' (' . $columns . ')';
				}
			}
			$schema .= "\n" . ')TYPE=MyISAM;' . "\n\n";
			fputs($fp, $schema);
				// dump the data
   $query_backup_count =(int) no_of_records($table,' 1',"*");
   $query_backup_count1=500;
   if($query_backup_count>$query_backup_count1)
    $query_backup_count =ceil($query_backup_count/$query_backup_count1);
   else
    $query_backup_count=1;
   $x1=0;
   $lower_limit=0;
   $upper_limit=$query_backup_count1;
   for($c=0;$c<$query_backup_count;$c++)
   {
    $rows_query = tep_db_query("select " . implode(',', $table_list) . " from " . $table."  limit $lower_limit ,$upper_limit");
    $lower_limit=$lower_limit+$query_backup_count1;
    while ($rows = tep_db_fetch_array($rows_query))
    {
     $schema = 'insert into ' . $table . ' (' . implode(', ', $table_list) . ') values (';
     reset($table_list);
     // while (list(,$i) = each($table_list)) // old code
	 foreach ($table_list as $key => $i)// new code 
     {
      if (!isset($rows[$i]))
      {
       $schema .= 'NULL, ';
      }
      elseif (tep_not_null($rows[$i]))
      {
       $row = addslashes($rows[$i]);
       $row = preg_replace("/\n#/", "\n".'\#', $row);
       $schema .= '\'' . $row . '\', ';
      }
      else
      {
       $schema .= '\'\', ';
      }
     }
     $schema = preg_replace('/, $/', '', $schema) . ');' . "\n";
     fputs($fp, $schema);
    }
    tep_db_free_result($rows_query);
   }
   /////////////////////////////////////////////////////////////////////////////////
		}
  tep_db_free_result($tables_query);
		fclose($fp);
		if (isset($_POST['download']) && ($_POST['download'] == 'yes'))
		{
			switch ($_POST['compress'])
			{
				case 'gzip':
				exec(LOCAL_EXE_GZIP . ' ' . PATH_TO_MAIN_PHYSICAL_BACKUP . $backup_file);
				$backup_file .= '.gz';
				break;
				case 'zip':
				exec(LOCAL_EXE_ZIP . ' -j ' . PATH_TO_MAIN_PHYSICAL_BACKUP . $backup_file . '.zip ' . PATH_TO_MAIN_PHYSICAL_BACKUP . $backup_file);
				unlink(PATH_TO_MAIN_PHYSICAL_BACKUP . $backup_file);
				$backup_file .= '.zip';
			}
			header('Content-type: application/x-octet-stream');
			header('Content-disposition: attachment; filename=' . $backup_file);
			readfile(PATH_TO_MAIN_PHYSICAL_BACKUP . $backup_file);
			unlink(PATH_TO_MAIN_PHYSICAL_BACKUP . $backup_file);
			exit;
		}
		else
		{
			switch ($_POST['compress'])
			{
				case 'gzip':
				exec(LOCAL_EXE_GZIP . ' ' . PATH_TO_MAIN_PHYSICAL_BACKUP . $backup_file);
				break;
				case 'zip':
				exec(LOCAL_EXE_ZIP . ' -j ' . PATH_TO_MAIN_PHYSICAL_BACKUP . $backup_file . '.zip ' . PATH_TO_MAIN_PHYSICAL_BACKUP . $backup_file);
				unlink(PATH_TO_MAIN_PHYSICAL_BACKUP . $backup_file);
			}
			$messageStack->add_session(SUCCESS_DATABASE_SAVED, 'success');
		}
		tep_redirect(FILENAME_ADMIN1_BACKUP);
		break;
		case 'restorenow':
		case 'restorelocalnow':
		set_time_limit(0);
		if ($action == 'restorenow')
		{
			$read_from = $_GET['file'];
			if (is_file(PATH_TO_MAIN_PHYSICAL_BACKUP . $_GET['file']))
			{
				$restore_file = PATH_TO_MAIN_PHYSICAL_BACKUP . $_GET['file'];
				$extension = substr($_GET['file'], -3);
				if ( ($extension == 'sql') || ($extension == '.gz') || ($extension == 'zip') )
				{
					switch ($extension)
					{
						case 'sql':
						$restore_from = $restore_file;
						$remove_raw = false;
						break;
						case '.gz':
						$restore_from = substr($restore_file, 0, -3);
						exec(LOCAL_EXE_GUNZIP . ' ' . $restore_file . ' -c > ' . $restore_from);
						$remove_raw = true;
						break;
						case 'zip':
						$restore_from = substr($restore_file, 0, -4);
						exec(LOCAL_EXE_UNZIP . ' ' . $restore_file . ' -d ' . PATH_TO_MAIN_PHYSICAL_BACKUP);
						$remove_raw = true;
					}
					if (isset($restore_from) && is_file($restore_from))
					{
						$fd = fopen($restore_from, 'rb');
						$restore_query = fread($fd, filesize($restore_from));
						fclose($fd);
					}
				}
			}
		}
		elseif ($action == 'restorelocalnow')
		{
			$sql_file = new upload('sql_file');
			if ($sql_file->parse() == true)
			{
				$restore_query = fread(fopen($sql_file->tmp_filename, 'r'), filesize($sql_file->tmp_filename));
				$read_from = $sql_file->filename;
			}
		}
		if (isset($restore_query))
		{
			$sql_array = array();
			$sql_length = strlen($restore_query);
			$pos = strpos($restore_query, ';');
			for ($i=$pos; $i<$sql_length; $i++)
			{
				if ($restore_query[0] == '#')
				{
					$restore_query = ltrim(substr($restore_query, strpos($restore_query, "\n")));
					$sql_length = strlen($restore_query);
					$i = strpos($restore_query, ';')-1;
					continue;
				}
				if ($restore_query[($i+1)] == "\n")
				{
					for ($j=($i+2); $j<$sql_length; $j++)
					{
						if (trim($restore_query[$j]) != '')
						{
							$next = substr($restore_query, $j, 6);
							if ($next[0] == '#')
							{
								// find out where the break position is so we can remove this line (#comment line)
								for ($k=$j; $k<$sql_length; $k++)
								{
									if ($restore_query[$k] == "\n")
										break;
								}
								$query = substr($restore_query, 0, $i+1);
								$restore_query = substr($restore_query, $k);
								// join the query before the comment appeared, with the rest of the dump
								$restore_query = $query . $restore_query;
								$sql_length = strlen($restore_query);
								$i = strpos($restore_query, ';')-1;
								continue 2;
							}
							break;
						}
					}
					if ($next == '')
					{
						// get the last insert query
						$next = 'insert';
					}
					if ( (preg_match('/create/i', $next)) || (preg_match('/insert/i', $next)) || (preg_match('/drop t/i', $next)) )
					{
						$next = '';
						$sql_array[] = substr($restore_query, 0, $i);
						$restore_query = ltrim(substr($restore_query, $i+1));
						$sql_length = strlen($restore_query);
						$i = strpos($restore_query, ';')-1;
					}
				}
			}
			//tep_db_query("drop table if exists ".ADMIN_TABLE.','.ADMIN_PERMISSION_TABLE.','.CONFIGURATION_GROUP_TABLE.','.CONFIGURATION_TABLE.','.COUNTRIES_TABLE);
			for ($i=0, $n=sizeof($sql_array); $i<$n; $i++)
			{
				tep_db_query($sql_array[$i]);
			}
			tep_db_query("delete from " . CONFIGURATION_TABLE . " where configuration_name = 'DB_LAST_RESTORE'");
			tep_db_query("insert into " . CONFIGURATION_TABLE . " values ('', '6', 'now()', '','Last Database Restore', 'DB_LAST_RESTORE', 'Last database restore file', '0', '', '', '')");
			if (isset($remove_raw) && ($remove_raw == true))
			{
				unlink($restore_from);
			}
			$messageStack->add_session(SUCCESS_DATABASE_RESTORED, 'success');
		}
		tep_redirect(FILENAME_ADMIN1_BACKUP);
		//exit;
		break;
		case 'download':
		$extension = substr($_GET['file'], -3);
		if ( ($extension == 'zip') || ($extension == '.gz') || ($extension == 'sql') )
		{
			if ($fp = fopen(PATH_TO_MAIN_PHYSICAL_BACKUP . $_GET['file'], 'rb'))
			{
				$buffer = fread($fp, filesize(PATH_TO_MAIN_PHYSICAL_BACKUP . $_GET['file']));
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
			header("location:".FILENAME_ADMIN1_BACKUP);
			exit;
		}
		@unlink(PATH_TO_MAIN_PHYSICAL_BACKUP .  $_GET['file']);
		$messageStack->add_session(SUCCESS_BACKUP_DELETED, 'success');
		header("location:".FILENAME_ADMIN1_BACKUP);
		exit;
		break;
	}
}
/////////////////////////////////
// check if the backup directory exists
$dir_ok = false;
if (is_dir(PATH_TO_MAIN_PHYSICAL_BACKUP))
{
 if (is_writeable(PATH_TO_MAIN_PHYSICAL_BACKUP))
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
 $dir = dir(PATH_TO_MAIN_PHYSICAL_BACKUP);
 $contents = array();
 while ($file = $dir->read())
	{
  if (!is_dir(PATH_TO_MAIN_PHYSICAL_BACKUP . $file) && $file!='index.php')
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
			if ((!isset($_GET['file']) || (isset($_GET['file']) && ($_GET['file'] == $entry))) && !isset($buInfo) && ($action != 'backup') && ($action != 'restorelocal'))
			{
				$file_array['file'] = $entry;
				$file_array['date'] = date('dS M, Y', filemtime(PATH_TO_MAIN_PHYSICAL_BACKUP . $entry));
				$file_array['size'] = number_format(filesize(PATH_TO_MAIN_PHYSICAL_BACKUP . $entry)) . ' bytes';
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
				$onclick_link = '?file=' . $buInfo->file . '&action=restore';
			}
			else
			{
				$row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
				$onclick_link = '?file=' . $entry;
			}
   $alternate++;
			if (isset($buInfo) && is_object($buInfo) && ($entry == $buInfo->file))
			{
				$action_image=tep_image(PATH_TO_IMAGE.'icon_arrow_right.gif',IMAGE_RESTORE);
			}
			else
			{
				$action_image='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_BACKUP, 'file=' . $entry) . '">'.tep_image(PATH_TO_IMAGE.'icon_info.gif',IMAGE_INFO).'</a>';
			}
   $template->assign_block_vars('backup', array( 'row_selected' => $row_selected,
    'column_selected' => 'onclick="document.location.href=\''.FILENAME_ADMIN1_BACKUP.$onclick_link.'\'"',
    'title' => '<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_BACKUP,'action=download&file=' . $entry) . '">'.tep_image(PATH_TO_IMAGE.'file_download.gif',IMAGE_DOWNLOAD).'</a>&nbsp;' . $entry,
    'date' => date("d/m/y H:i:s", filemtime(PATH_TO_MAIN_PHYSICAL_BACKUP . $entry)),
    'size' => number_format(filesize(PATH_TO_MAIN_PHYSICAL_BACKUP . $entry))." bytes",
    'action' => $action_image,
    'name' => tep_db_output($user_category['user_category_name']),
    ));
		}
		$dir->close();
	}
}
$backup_buttons='';
if ( ($action != 'backup') && (isset($dir)) )
 $backup_buttons='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_BACKUP, 'action=backup') . '">'.tep_button('Backup','class="btn btn-primary"').'</a>';
if ( ($action != 'restorelocal') && isset($dir) )
	$backup_buttons.='&nbsp;&nbsp;<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_BACKUP, 'action=restorelocal') . '">'.tep_button('Restore','class="btn btn-secondary"').'</a>';

if (defined('DB_LAST_RESTORE'))
{
 $last_restore=TEXT_LAST_RESTORATION . ' ' . DB_LAST_RESTORE . ' <a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_BACKUP, 'action=forget') . '">' . TEXT_FORGET . '</a>';
}
else
{
 $last_restore='';
}
//// for right side
$ADMIN_RIGHT_HTML="";

$heading = array();
$contents = array();

switch ($action)
{
 case 'backup':
  $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_BACKUP . '</b>');
  $contents = array('form' => tep_draw_form('backup', PATH_TO_ADMIN.FILENAME_ADMIN1_BACKUP, 'action=backupnow'));
  $contents[] = array('text' => TEXT_INFO_NEW_BACKUP);
  $contents[] = array('text' => '<br>' . tep_draw_radio_field('compress', 'no', true) . ' ' . TEXT_INFO_USE_NO_COMPRESSION);
  if (file_exists(LOCAL_EXE_GZIP))
   $contents[] = array('text' => '<br>' . tep_draw_radio_field('compress', 'gzip') . ' ' . TEXT_INFO_USE_GZIP);
  if (file_exists(LOCAL_EXE_ZIP))
   $contents[] = array('text' => tep_draw_radio_field('compress', 'zip') . ' ' . TEXT_INFO_USE_ZIP);
  if ($dir_ok == true)
  {
   $contents[] = array('text' => '<br>' . tep_draw_checkbox_field('download', 'yes') . ' ' . TEXT_INFO_DOWNLOAD_ONLY . '*<br><br>*' . TEXT_INFO_BEST_THROUGH_HTTPS);
  }
  else
  {
   $contents[] = array('text' => '<br>' . tep_draw_radio_field('download', 'yes', true) . ' ' . TEXT_INFO_DOWNLOAD_ONLY . '*<br><br>*' . TEXT_INFO_BEST_THROUGH_HTTPS);
  }
  $contents[] = array('align' => 'left', 'text' => '<br>' . tep_draw_submit_button_field('','Backup','class="btn btn-primary"') . '&nbsp;<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_BACKUP) . '">' . tep_button('Cancel','class="btn btn-primary"') . '</a>');
 break;
 case 'restore':
  $heading[] = array('text' => '<b>' . $buInfo->date . '</b>');
  $contents[] = array('text' => tep_break_string(sprintf(TEXT_INFO_RESTORE, PATH_TO_MAIN_PHYSICAL_BACKUP . (($buInfo->compression != TEXT_NO_EXTENSION) ? substr($buInfo->file, 0, strrpos($buInfo->file, '.')) : $buInfo->file), ($buInfo->compression != TEXT_NO_EXTENSION) ? TEXT_INFO_UNPACK : ''), 35, ' '));
  $contents[] = array('align' => 'left', 'text' => '<br><a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_BACKUP, 'file=' . $buInfo->file . '&action=restorenow') . '">' . tep_button('Restore','class="btn btn-primary"') . '</a>&nbsp;<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_BACKUP) . '">' . tep_button('Cancel','class="btn btn-primary"') . '</a>');
 break;
 case 'restorelocal':
  $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_RESTORE_LOCAL . '</b>');
  $contents = array('form' => tep_draw_form('restore', PATH_TO_ADMIN.FILENAME_ADMIN1_BACKUP, 'action=restorelocalnow', 'post', 'enctype="multipart/form-data"'));
  $contents[] = array('text' => TEXT_INFO_RESTORE_LOCAL . '<br><br>' . TEXT_INFO_BEST_THROUGH_HTTPS);
  $contents[] = array('text' => '<br>' . tep_draw_file_field('sql_file'));
  $contents[] = array('text' => TEXT_INFO_RESTORE_LOCAL_RAW_FILE);
  $contents[] = array('align' => 'left', 'text' => '<br>' . tep_draw_submit_button_field('','Restore','class="btn btn-primary"') . '&nbsp;<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_BACKUP) . '">' . tep_button('Cancel','class="btn btn-primary"') . '</a>');
 break;
 case 'delete':
  $heading[] = array('text' => '<b>' . $buInfo->date . '</b>');
  $contents = array('form' => tep_draw_form('delete', PATH_TO_ADMIN.FILENAME_ADMIN1_BACKUP, 'file=' . $buInfo->file . '&action=deleteconfirm'));
  $contents[] = array('text' => TEXT_DELETE_INTRO);
  $contents[] = array('text' => '<br><b>' . $buInfo->file . '</b>');
  $contents[] = array('align' => 'left', 'text' => '<br>' . tep_draw_submit_button_field('','Confirm','class="btn btn-primary"') . ' <a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_BACKUP) . '">' . tep_button('Cancel','class="btn btn-primary"') . '</a>');
 break;
 default:
  if (isset($buInfo) && is_object($buInfo))
  {
   $heading[] = array('text' => '<div class="text-primary font-weight-bold mb-2">' . $buInfo->date . '</div>');
   $contents[] = array('align' => 'left', 'text' => '<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_BACKUP, 'file=' . $buInfo->file . '&action=restore') . '">' . tep_button('Restore','class="btn btn-primary"') . '</a> <a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_BACKUP, 'file=' . $buInfo->file . '&action=delete') . '">' . tep_button('Delete','class="btn btn-secondary"') . '</a>');
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
 'backup_directory'=>TEXT_BACKUP_DIRECTORY.PATH_TO_MAIN_PHYSICAL_BACKUP,
 'backup_buttons'=>$backup_buttons,
 'TABLE_HEADING_TITLE'=>TABLE_HEADING_TITLE,
 'TABLE_HEADING_FILE_DATE'=>TABLE_HEADING_FILE_DATE,
 'TABLE_HEADING_FILE_SIZE'=>TABLE_HEADING_FILE_SIZE,
 'TABLE_HEADING_ACTION'=>TABLE_HEADING_ACTION,
 'last_restore'=>$last_restore,
 'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
 'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('backup');
?>