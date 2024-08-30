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
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_FILES);
$template->set_filenames(array('file' => 'admin1_files.htm','file1' => 'admin1_files1.htm'));
include_once(FILENAME_ADMIN_BODY);

$action = (isset($_GET['action']) ? $_GET['action'] : '');

if(tep_not_null($action)) 
{
 switch ($action) 
 {
  case 'box_store':       
   $sql_data_array = array('admin_files_name' => tep_db_prepare_input($_GET['box']),
                           'admin_files_is_boxes' => '1');
   tep_db_perform(ADMIN_FILES_TABLE, $sql_data_array);
   $row_id_check=getAnyTableWhereData(ADMIN_FILES_TABLE,"admin_files_name='".tep_db_input($_GET['box'])."'","admin_files_id");
   $admin_boxes_id = $row_id_check['admin_files_id'];
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FILES, 'cID=' . $admin_boxes_id));
  break;
  case 'box_remove':
   // NOTE: ALSO DELETE FILES STORED IN REMOVED BOX //
  //die('Don\'t remove');
   $admin_boxes_id = tep_db_prepare_input($_GET['cID']);
   tep_db_query("delete from " . ADMIN_FILES_TABLE . " where admin_files_id = '" . $admin_boxes_id . "' or admin_files_to_boxes = '" . $admin_boxes_id . "'");
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FILES));
  break;
  case 'file_store':
   $sql_data_array = array('admin_files_name' => tep_db_prepare_input($_POST['admin_files_name']),
                           'admin_files_to_boxes' => tep_db_prepare_input($_POST['admin_files_to_boxes']));
   tep_db_perform(ADMIN_FILES_TABLE, $sql_data_array);
   $row_id_check=getAnyTableWhereData(ADMIN_FILES_TABLE,"admin_files_name='".tep_db_input($_POST['admin_files_name'])."'","admin_files_id");
   $admin_files_id = $row_id_check['admin_files_id'];
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FILES, 'cPath=' . $_GET['cPath'] . '&fID=' . $admin_files_id));
  break;
  case 'file_remove':
   $admin_files_id = tep_db_prepare_input($_POST['admin_files_id']);      
   tep_db_query("delete from " . ADMIN_FILES_TABLE . " where admin_files_id = '" . $admin_files_id . "'");
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FILES, 'cPath=' . $_GET['cPath']));
  break;
 }
}
if ($_GET['fID'] || $_GET['cPath']) 
{
 //echo $current_box_query_raw = "select admin_files_name as admin_box_name from " . ADMIN_FILES_TABLE . " where admin_files_id = " . $_GET['cPath'] . " ";
 $current_box_query = tep_db_query("select admin_files_name as admin_box_name from " . ADMIN_FILES_TABLE . " where admin_files_id = " . $_GET['cPath']);
 $current_box = tep_db_fetch_array($current_box_query); 

 $db_file_query_raw = "select * from " . ADMIN_FILES_TABLE . " where admin_files_to_boxes = " . $_GET['cPath'] . " order by admin_files_name";
 $db_file_query = tep_db_query($db_file_query_raw);
 $file_count = 0;

 $db_file_num_row = tep_db_num_rows($db_file_query);
 if($db_file_num_row > 0)
 {
  $alternate=1;
  while ($files = tep_db_fetch_array($db_file_query)) 
  {
   $file_count++;
   if (((!$_GET['fID']) || ($_GET['fID'] == $files['admin_files_id'])) && (!$fInfo) ) 
   {
    $fInfo = new objectInfo($files);
   }
   if ( (is_object($fInfo)) && ($files['admin_files_id'] == $fInfo->admin_files_id) ) 
   {
				$row_selected=' class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FILES, 'cPath=' . $_GET['cPath'] . '&fID=' . $files['admin_files_id'] . '&action=edit_file') . '\'"';
   } 
   else 
   {
				$row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FILES, 'cPath=' . $_GET['cPath'] . '&fID=' . $files['admin_files_id']) . '\'"';
   }
   $alternate++;
   if ( (is_object($fInfo)) && ($files['admin_files_id'] == $fInfo->admin_files_id) ) 
   { 
    $action_image=tep_image(PATH_TO_IMAGE.'icon_arrow_right.gif'); 
   } 
   else 
   { 
    $action_image='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FILES, 'cPath=' . $_GET['cPath'] . '&fID=' . $files['admin_files_id']) . '">' . tep_image(PATH_TO_IMAGE.'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; 
   }
   $template->assign_block_vars('file', array( 'row_selected' => $row_selected,
    'action' => $action_image,
    'name' => tep_db_output($files['admin_files_name']),
    ));
  }
 }
}
else
{
 $installed_boxes_query = tep_db_query("select admin_files_name as admin_boxes_name from " . ADMIN_FILES_TABLE . " where admin_files_is_boxes = 1 order by admin_files_name");
 $installed_boxes = array();
 while($db_boxes = tep_db_fetch_array($installed_boxes_query)) 
 {
  $installed_boxes[] = $db_boxes['admin_boxes_name'];
 }
 $none = 0;
 $boxes = array();
 $dir = dir(PATH_TO_MAIN_PHYSICAL_BOX);

 while ($boxes_file = $dir->read()) 
 {
  if ( (substr("$boxes_file", 0,6) == 'admin_') && (substr("$boxes_file", -4) == '.php') && !(in_array($boxes_file, $installed_boxes)))
  {
   $boxes[] = array('admin_boxes_name' => $boxes_file,
                    'admin_boxes_id' => 'b' . $none);
  } 
  elseif ( (substr("$boxes_file", 0,6) == 'admin_') && (substr("$boxes_file", -4) == '.php') && (in_array($boxes_file, $installed_boxes))) 
  {
   $db_boxes_id_query = tep_db_query("select admin_files_id as admin_boxes_id from " . ADMIN_FILES_TABLE . " where admin_files_is_boxes = 1 and admin_files_name = '" . $boxes_file . "'");
   $db_boxes_id = tep_db_fetch_array($db_boxes_id_query);
   $boxes[] = array('admin_boxes_name' => $boxes_file,
                    'admin_boxes_id' => $db_boxes_id['admin_boxes_id']);
  }
  $none++;
 }
 $dir->close();
 sort($boxes);
 reset ($boxes);
 $boxnum = sizeof($boxes);
 $i = 0;
 if($boxnum > 0)
 {
  $alternate=1;
  while ($i < $boxnum) 
  {
   if (((!$_GET['cID']) || ($_GET['none'] == $boxes[$i]['admin_boxes_id']) || ($_GET['cID'] == $boxes[$i]['admin_boxes_id'])) && (!$cInfo) ) 
   {
    $cInfo = new objectInfo($boxes[$i]);
   }
   if ( (is_object($cInfo)) && ($boxes[$i]['admin_boxes_id'] == $cInfo->admin_boxes_id) ) 
   {
    if ( substr("$cInfo->admin_boxes_id", 0,1) == 'b') 
    {
     $row_selected=' id="defaultSelected" class="table-secondary dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FILES, 'cID=' . $boxes[$i]['admin_boxes_id']) . '\'"';
    } 
    else 
    {
     $row_selected=' id="defaultSelected" class="table-secondary dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FILES, 'cPath=' . $boxes[$i]['admin_boxes_id'] . '&action=store_file') . '\'"';
    }
   } 
   else 
   {
     $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FILES, 'cID=' . $boxes[$i]['admin_boxes_id']) . '\'"';
   }   
   $alternate++;
   if ( (is_object($cInfo)) && ($_GET['cID'] == $boxes[$i]['admin_boxes_id'])) 
   {
    if (substr($boxes[$i]['admin_boxes_id'], 0,1) == 'b') 
    {
     $status=tep_image(PATH_TO_IMAGE.'icon_status_red.gif', STATUS_BOX_NOT_INSTALLED, 30, 20) . '&nbsp;<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FILES, 'cID=' . $boxes[$i]['admin_boxes_id'] . '&box=' . $boxes[$i]['admin_boxes_name'] . '&action=box_store') . '">' . tep_image(PATH_TO_IMAGE.'icon_status_green_light.gif', STATUS_BOX_INSTALL, 30, 20) . '</a>';
    } 
    else 
    {
     $status='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FILES, 'cID=' . $_GET['cID'] . '&action=box_remove') . '">' . tep_image(PATH_TO_IMAGE.'icon_status_red_light.gif', STATUS_BOX_REMOVE, 30, 20) . '</a>&nbsp;' . tep_image(PATH_TO_IMAGE.'icon_status_green.gif', STATUS_BOX_INSTALLED, 30, 20);
    }
   } 
   else 
   {
    if (substr($boxes[$i]['admin_boxes_id'], 0,1) == 'b') 
    {
     $status=tep_image(PATH_TO_IMAGE.'icon_status_red.gif', '', 30, 20) . '&nbsp;' . tep_image(PATH_TO_IMAGE.'icon_status_green_light.gif', '', 30, 20) ;
    } 
    else 
    {
     $status=tep_image(PATH_TO_IMAGE.'icon_status_red_light.gif', '', 30, 20) . '&nbsp;' . tep_image(PATH_TO_IMAGE.'icon_status_green.gif', '', 30, 20);
    }
   }
   if ( (is_object($cInfo)) && ($boxes[$i]['admin_boxes_id'] == $cInfo->admin_boxes_id) ) 
   { 
    $action_image=tep_image(PATH_TO_IMAGE.'icon_arrow_right.gif'); 
   } 
   else 
   { 
    $action_image='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FILES, 'cID=' . $boxes[$i]['admin_boxes_id']) . '">' . tep_image(PATH_TO_IMAGE.'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; 
   }
   $template->assign_block_vars('file1', array( 'row_selected' => $row_selected,
    'action' => $action_image,
    'name' => tep_image(PATH_TO_IMAGE.'folder.gif', ICON_FOLDER) . ' <b>' . ucfirst(substr_replace(substr_replace($boxes[$i]['admin_boxes_name'], '' , 0,6),'',-4)) . '</b>',
    'status' => $status,
    ));
   $i++;
  }
 }
}
//////////////
$ADMIN_RIGHT_HTML="";
$heading = array();
$contents = array();
switch ($_GET['action']) 
{  
 case 'store_file': 
  $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_FILE . '</b>');
  $file_query = tep_db_query("select admin_files_name from " . ADMIN_FILES_TABLE . " where admin_files_is_boxes = '0' ");
  while ($fetch_files = tep_db_fetch_array($file_query)) 
  {
   $files_array[] = $fetch_files['admin_files_name'];        
  }
  $file_dir = array();
  $dir = dir(PATH_TO_MAIN_PHYSICAL.PATH_TO_ADMIN);
  while ($file = $dir->read()) 
  {
   if ((substr("$file", -4) == '.php') && $file != FILENAME_INDEX && $file != FILENAME_ADMIN_BODY && $file != FILENAME_ADMIN_FOOTER && $file != FILENAME_ADMIN_LEFT && $file != FILENAME_ADMIN_RIGHT && $file != FILENAME_ADMIN_HEADER && $file != FILENAME_ADMIN1_ADMIN_FORGOT_PASSWORD && $file != FILENAME_ADMIN1_ACCOUNT && $file != FILENAME_LOGOUT && $file != FILENAME_ADMIN1_CONTROL_PANEL && $file != FILENAME_ADMIN1_ADMIN_FORBIDDEN && $file != FILENAME_ADMIN1_ADMIN_ERROR ) 
   {
    $file_dir[] = $file;
   }
  }
  $result = $file_dir;      
  //print_r($file_dir);
  //echo "<br><br>";
  //print_r($files_array);
  if (sizeof($files_array) > 0) 
  {
   $result = array_values (array_diff($file_dir, $files_array));
  }
  sort ($result);
  reset ($result);
  //while (list ($key, $val) = each ($result)) 
  foreach($result as $key=> $val)
  {
   $show[] = array('id' => $val,
                   'text' => $val);
  }
  $contents = array('form' => tep_draw_form('store_file', PATH_TO_ADMIN.FILENAME_ADMIN1_FILES, 'cPath=' . $_GET['cPath'] . '&fID=' . $files['admin_files_id'] . '&action=file_store', 'post', 'enctype="multipart/form-data"')); 
  $contents[] = array('text' => '<b>' . TEXT_INFO_NEW_FILE_BOX .  ucfirst(substr_replace(substr_replace($current_box['admin_box_name'],'',0,6), '', -4)) . '</b>');
  $contents[] = array('text' => TEXT_INFO_NEW_FILE_INTRO );
  $contents[] = array('align' => 'left', 'text' => '<br>' . tep_draw_pull_down_menu('admin_files_name', $show, $show, 'class="form-control form-control-sm"')); 
  $contents[] = array('text' => tep_draw_hidden_field('admin_files_to_boxes', $_GET['cPath']));
  $contents[] = array('align' => 'left', 'text' => '<br>' . tep_button_submit('btn btn-primary',IMAGE_SAVE) . ' <a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FILES, 'cPath=' . $_GET['cPath']) . '">' . tep_button_submit('btn btn-secondary',IMAGE_CANCEL) . '</a>');    
 break;
 case 'remove_file': 
  $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_FILE . '</b>');
  $contents = array('form' => tep_draw_form('remove_file', PATH_TO_ADMIN.FILENAME_ADMIN1_FILES, 'action=file_remove&cPath=' . $_GET['cPath'] . '&fID=' . $files['admin_files_id'], 'post', 'enctype="multipart/form-data"')); 
  $contents[] = array('text' => tep_draw_hidden_field('admin_files_id', $_GET['fID']));
  $contents[] = array('text' =>  sprintf(TEXT_INFO_DELETE_FILE_INTRO, $fInfo->admin_files_name, ucfirst(substr_replace ($current_box['admin_box_name'], '', -4))) );    
  $contents[] = array('align' => 'left', 'text' => '<br>' . tep_image_submit(PATH_TO_BUTTON.'button_confirm.gif', IMAGE_CONFIRM) . ' <a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FILES, 'cPath=' . $_GET['cPath'] . '&fID=' . $_GET['fID']) . '">' . tep_button_submit('btn btn-secondary',IMAGE_CANCEL) . '</a>');    
 break;
 default:
  if (is_object($cInfo)) 
  {
   $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DEFAULT_BOXES . ucfirst(substr_replace(substr_replace($cInfo->admin_boxes_name,'',0,6), '', -4)) . '</b>');
   if ( substr($cInfo->admin_boxes_id, 0,1) == 'b') 
   {
    $contents[] = array('text' => '<b>' .  ucfirst(substr_replace($cInfo->admin_boxes_name, '', -4)) . ' ' . TEXT_INFO_DEFAULT_BOXES_NOT_INSTALLED . '</b><br>&nbsp;');
    $contents[] = array('text' => TEXT_INFO_DEFAULT_BOXES_INTRO);
   } 
   else 
   {
    $contents = array('form' => tep_draw_form('newfile', PATH_TO_ADMIN.FILENAME_ADMIN1_FILES, 'cPath=' . $cInfo->admin_boxes_id . '&action=store_file', 'post')); 
    $contents[] = array('align' => 'left', 'text' => tep_button_submit('btn btn-primary',IMAGE_INSERT_FILE) );
    $contents[] = array('text' => tep_draw_hidden_field('this_category', $cInfo->admin_boxes_id));
    $contents[] = array('text' => '<br>' . TEXT_INFO_DEFAULT_BOXES_INTRO);
   }
   $contents[] = array('text' => '<br>');
  }
  if (is_object($fInfo)) 
  {
   $heading[] = array('text' => '<b>' . TEXT_INFO_NEW_FILE_BOX .  ucfirst(substr_replace(substr_replace($current_box['admin_box_name'],'',0,6), '', -4)) . '</b>');
   $contents[] = array('align' => 'left', 'text' => '<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FILES, 'cPath=' . $_GET['cPath'] . '&action=store_file') . '">' . tep_image_button(PATH_TO_BUTTON.'button_admin_files.gif', IMAGE_INSERT_FILE) . '</a> <a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FILES, 'cPath=' . $_GET['cPath'] . '&fID=' . $fInfo->admin_files_id . '&action=remove_file') . '">' . tep_image_button(PATH_TO_BUTTON.'button_admin_remove.gif', IMAGE_DELETE) . '</a>');
   $contents[] = array('text' => '<br>' . TEXT_INFO_DEFAULT_FILE_INTRO . ucfirst(substr_replace(substr_replace($current_box['admin_box_name'],'',0,6), '', -4)));
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
////////////////////
if ($_GET['fID'] || $_GET['cPath']) 
{
 $template->assign_vars(array(
  'HEADING_TITLE'=>HEADING_TITLE,
  'TABLE_HEADING_FILENAME'=>TABLE_HEADING_FILENAME,
  'TABLE_HEADING_ACTION'=>TABLE_HEADING_ACTION,
  'count_rows'=>TEXT_COUNT_FILES . $file_count,
  'new_button'=>'<a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FILES, 'cID=' . $_GET['cPath']) . '">' .IMAGE_BACK . '</a>
  <a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_FILES, 'cPath=' . $_GET['cPath'] . '&action=store_file') . '">' .IMAGE_INSERT_FILE . '</a>',
  'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
  'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
  'update_message'=>$messageStack->output()));
 $template->pparse('file');
}
else
{
 $template->assign_vars(array(
  'HEADING_TITLE'=>HEADING_TITLE,
  'TABLE_HEADING_BOXES'=>TABLE_HEADING_BOXES,
  'TABLE_HEADING_STATUS'=>TABLE_HEADING_STATUS,
  'TABLE_HEADING_ACTION'=>TABLE_HEADING_ACTION,
  'count_rows'=>TEXT_COUNT_FILES . $boxnum,
  'new_button'=>'',
  'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
  'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
  'update_message'=>$messageStack->output()));
 $template->pparse('file1');
}
?>