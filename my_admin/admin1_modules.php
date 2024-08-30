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
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_ADMIN_MODULES);
$template->set_filenames(array('modules' => 'admin1_modules.htm'));
include_once(FILENAME_ADMIN_BODY);

$action = (isset($_GET['action']) ? $_GET['action'] : '');
$_GET['page'] = ((int)$_GET['page'] > 0 ? (int)$_GET['page'] : '1');
$set = (isset($_GET['set']) ? $_GET['set'] : '');

unset($mInfo); //required


if (tep_not_null($set)) 
{
 switch ($set) 
 {
  case 'shipping':
   $module_type = 'shipping';
   $module_directory = PATH_TO_MAIN_PHYSICAL_MODULE . 'shipping/';
   $module_key = 'MODULE_SHIPPING_INSTALLED';
   define('HEADING_TITLE', HEADING_TITLE_MODULES_SHIPPING);
  break;
  case 'ordertotal':
   $module_type = 'order_total';
   $module_directory = PATH_TO_MAIN_PHYSICAL_MODULE . 'order_total/';
   $module_key = 'MODULE_ORDER_TOTAL_INSTALLED';
   define('HEADING_TITLE', HEADING_TITLE_MODULES_ORDER_TOTAL);
  break;
  case 'payment':
  default:
   $module_type = 'payment';
   $module_directory = PATH_TO_MAIN_PHYSICAL_MODULE . 'payment/';
   $module_key = 'MODULE_PAYMENT_INSTALLED';
   define('HEADING_TITLE', HEADING_TITLE_MODULES_PAYMENT);
  break;
 }
}
if (tep_not_null($action)) 
{
 switch ($action) 
 {
  case 'save':
   foreach($_POST['configuration']  as $key => $value)
   {
    tep_db_query("update " . CONFIGURATION_TABLE . " set configuration_value = '" . $value . "' where configuration_name = '" . $key . "'");
   }
   foreach($_POST['TR_configuration_value']  as $key => $value)	   
   {
    tep_db_query("update " . CONFIGURATION_TABLE . " set configuration_value = '" . $value . "' where configuration_name = '" . $key . "'");
   }
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_MODULES, 'set=' . $set . '&module=' . $_GET['module']));
  break;
  case 'install':
  case 'remove':
   $file_extension = substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], '.'));
   $class = basename($_GET['module']);
   if (file_exists($module_directory . $class . $file_extension)) 
   {
    include($module_directory . $class . $file_extension);
    $module = new $class;
    if ($action == 'install') 
    {
     $module->install();
    } 
    elseif ($action == 'remove') 
    {
     $module->remove();
    }
   }
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_MODULES, 'set=' . $set . '&module=' . $class));
  break;
 }
}

$file_extension = substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], '.'));
$directory_array = array();
if ($dir = dir($module_directory)) 
{
 while ($file = $dir->read()) 
 {
  if (!is_dir($module_directory . $file)) 
  {
   if (substr($file, strrpos($file, '.')) == $file_extension) 
   {
    $directory_array[] = $file;
   }
  }
 }
 sort($directory_array);
 $dir->close();
}

$installed_modules = array();
$alternate=1;
for ($i=0, $n=sizeof($directory_array); $i<$n; $i++) 
{
 $file = $directory_array[$i];
 include(PATH_TO_MAIN_PHYSICAL_LANGUAGE_MODULE . $module_type . '/' . $file);
 include($module_directory . $file);
 $class = substr($file, 0, strrpos($file, '.'));
 if (tep_class_exists($class)) 
 {
  $module = new $class;
  if ($module->check() > 0) 
  {
   if ($module->sort_order > 0) 
   {
    $installed_modules[$module->sort_order] = $file;
   } 
   else 
   {
    $installed_modules[] = $file;
   }
  }
  if ((!isset($_GET['module']) || (isset($_GET['module']) && ($_GET['module'] == $class))) && !isset($mInfo)) 
  {
   $module_info = array('code' => $module->code,
                        'title' => $module->title,
                        'description' => $module->description,
                        'status' => $module->check());
   $module_keys = $module->keys();
   $keys_extra = array();
   for ($j=0, $k=sizeof($module_keys); $j<$k; $j++) 
   {
    $key_value_query = tep_db_query("select configuration_title, configuration_value, configuration_description, use_function, set_function from " . CONFIGURATION_TABLE . " where configuration_name = '" . $module_keys[$j] . "'");
    $key_value = tep_db_fetch_array($key_value_query);
    $keys_extra[$module_keys[$j]]['title'] = $key_value['configuration_title'];
    $keys_extra[$module_keys[$j]]['value'] = $key_value['configuration_value'];
    $keys_extra[$module_keys[$j]]['description'] = $key_value['configuration_description'];
    $keys_extra[$module_keys[$j]]['use_function'] = $key_value['use_function'];
    $keys_extra[$module_keys[$j]]['set_function'] = $key_value['set_function'];
   }
   $module_info['keys'] = $keys_extra;
   $mInfo = new objectInfo($module_info);
  }
  if (isset($mInfo) && is_object($mInfo) && ($class == $mInfo->code) ) 
  {
   if ($module->check() > 0) 
   {
    $row_selected=' id="defaultSelected" class="table-secondary dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_MODULES, 'set=' . $set . '&module=' . $class . '&action=edit') . '\'"';
   } 
   else 
   {
    $row_selected=' id="defaultSelected" class="table-secondary dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"' ;
   }
  } 
  else 
  {
   $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_MODULES, 'set=' . $set . '&module=' . $class) . '\'"';
  }
  $alternate++;
  if (isset($mInfo) && is_object($mInfo) && ($class == $mInfo->code) ) 
  { 
   $action_image=tep_image(PATH_TO_IMAGE.'icon_arrow_right.gif'); 
  } 
  else 
  { 
   $action_image='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_MODULES, 'set=' . $set . '&module=' . $class) . '">' . tep_image(PATH_TO_IMAGE.'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; 
  }
  $template->assign_block_vars('modules', array( 'row_selected' => $row_selected,
   'action' => $action_image,
   'title' => tep_db_output($module->title),
   'sort_order' => (is_numeric($module->sort_order)?$module->sort_order:''),
   ));
 }
}
ksort($installed_modules);
$check_query = tep_db_query("select configuration_value from " . CONFIGURATION_TABLE . " where configuration_name = '" . $module_key . "'");
if (tep_db_num_rows($check_query)) 
{
 $check = tep_db_fetch_array($check_query);
 if ($check['configuration_value'] != implode(';',$installed_modules)) 
 {
  tep_db_query("update " . CONFIGURATION_TABLE . " set configuration_value = '" . implode(';',$installed_modules) . "', updated = now() where configuration_name = '" . $module_key . "'");
 }
} 
else 
{
 tep_db_query("insert into " . CONFIGURATION_TABLE . " (configuration_title, configuration_name, configuration_value, configuration_description, configuration_group_id, priority, inserted) values ('Installed Modules', '" . $module_key . "', '" . implode( ';',$installed_modules) . "', 'This is automatically updated. No need to edit.', '9', '0', now())");
}
$TEXT_MODULE_DIRECTORY=TEXT_MODULE_DIRECTORY . ' ' . $module_directory;

//////////////////////////////
$ADMIN_RIGHT_HTML="";
$heading = array();
$contents = array();

switch ($action) 
{
 case 'edit':
  $keys = '';
  reset($mInfo->keys);
  foreach($mInfo->keys  as $key => $value)
  {
   $keys .= '<b>' . $value['title'] . '</b><br>' . $value['description'] . '<br>';
   if ($value['set_function']) 
   {
    eval('$keys .= ' . $value['set_function'] . "'" . $value['value'] . "', '" . $key . "');");
   } 
   elseif($_GET['module']=='moneyorder' &&$key=='MODULE_PAYMENT_MONEYORDER_PAYTO' )
   {
    $keys .= tep_draw_textarea_field('configuration[' . $key . ']', true,30,3,$value['value']);
   }
   else 
   {
    $keys .= tep_draw_input_field('configuration[' . $key . ']', $value['value']);
   }
   $keys .= '<br><br>';
  }
  $keys = substr($keys, 0, strrpos($keys, '<br><br>'));
  $heading[] = array('text' => '<div class="list-group"><div class="font-weight-bold text-primary">'.$mInfo->title.'</div></div>');
  $contents = array('form' => tep_draw_form('modules', PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_MODULES, 'set=' . $set . '&module=' . $_GET['module'] . '&action=save'));

  // $contents[] = array('text' => $keys);
 
  $contents[] = array('align' => '', 'text' => '
  <div class="py-2">
  
  <div class="mt-2 mb-2">'.$keys.'</div>
  '.tep_draw_submit_button_field('',IMAGE_UPDATE,'class="btn btn-primary"').'
  <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_MODULES, 'set=' . $set . '&module=' . $_GET['module']). '">'.IMAGE_CANCEL.'</a>
  </div>
  </div>');
 
  break;
 default:
  $heading[] = array('text' => '<div class="list-group"><div class="font-weight-bold text-primary">'.$mInfo->title.'</div></div>');
  if ($mInfo->status == '1') 
  {
   $keys = '';
   reset($mInfo->keys);
   foreach($mInfo->keys  as $key => $value)
   {
    $keys .= '<b>' . $value['title'] . '</b><br>';
    if ($value['use_function']) 
    {
     $use_function = $value['use_function'];
     if (preg_match('/->/', $use_function)) 
     {
      $class_method = explode('->', $use_function);
      if (!is_object(${$class_method[0]})) 
      {
       include(PATH_TO_MAIN_PHYSICAL_CLASS . $class_method[0] . '.php');
       ${$class_method[0]} = new $class_method[0]();
      }
      $keys .= tep_call_function($class_method[1], $value['value'], ${$class_method[0]});
     } 
     else 
     {
      $keys .= tep_call_function($use_function, $value['value']);
     }
    } 
    else 
    {
     $keys .= $value['value'];
    }
    $keys .= '<br><br>';
   }
   $keys = substr($keys, 0, strrpos($keys, '<br><br>'));
  //  $contents[] = array('align' => 'left', 'text' => '<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_MODULES, 'set=' . $set . '&module=' . $mInfo->code . '&action=remove') . '">' . tep_image_button(PATH_TO_BUTTON.'button_module_remove.gif', IMAGE_MODULE_REMOVE) . '</a> <a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_MODULES, 'set=' . $set . (isset($_GET['module']) ? '&module=' . $_GET['module'] : '') . '&action=edit') . '">' . tep_image_button(PATH_TO_BUTTON.'button_edit.gif', IMAGE_EDIT) . '</a>');
  //  $contents[] = array('text' => '<br>' . $mInfo->description);
  //  $contents[] = array('text' => '<br>' . $keys);
   
   $contents[] = array('align' => '', 'text' => '
   <div class="py-2">
   <a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_MODULES, 'set=' . $set . '&module=' . $mInfo->code . '&action=remove') . '">'
   .IMAGE_MODULE_REMOVE.'</a>
   <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_MODULES, 'set=' . $set . (isset($_GET['module']) ? '&module=' . $_GET['module'] : '') . '&action=edit'). '">'
   .IMAGE_EDIT.'</a>
   <div class="mt-2">'.$mInfo->description.'</div>
   <div class="mt-2">'.$keys.'</div>
   </div>
   </div>');

  } 
  else 
  {
  //  $contents[] = array('align' => 'left', 'text' => '<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_MODULES, 'set=' . $set . '&module=' . $mInfo->code . '&action=install') . '">' . tep_image_button(PATH_TO_BUTTON.'button_module_install.gif', IMAGE_MODULE_INSTALL) . '</a>');
  //  $contents[] = array('text' => '<br>' . $mInfo->description);
   $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
   <div class="mb-2 text-danger">'.tep_db_output($mInfo->description).'</div>
   <a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_MODULES, 'set=' . $set . '&module=' . $mInfo->code . '&action=install') . '">'
   .IMAGE_MODULE_INSTALL.'</a>
   </div>
   </div>');
  }
 break;
}
if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) 
{
 $box = new right_box;
	$RIGHT_BOX_WIDTH='205';
 $ADMIN_RIGHT_HTML.= $box->infoBox($heading, $contents);
}
else
{
	$RIGHT_BOX_WIDTH='0';
}

$template->assign_vars(array(
 'TABLE_HEADING_MODULES'=>TABLE_HEADING_MODULES,
 'TABLE_HEADING_SORT_ORDER'=>TABLE_HEADING_SORT_ORDER,
 'TABLE_HEADING_ACTION'=>TABLE_HEADING_ACTION,
 'TEXT_MODULE_DIRECTORY'=>$TEXT_MODULE_DIRECTORY,
 'HEADING_TITLE'=>HEADING_TITLE,
 'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
 'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('modules');
?>
