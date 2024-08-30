<?
/*
***********************************************************
***********************************************************
**********# Name          : Shambhu Prasad Patnaik  #******
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
***********************************************************
***********************************************************
*/
include_once("../include_files.php");
include_once("../general_functions/password_funcs.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_MEMBERS);
$template->set_filenames(array('member' => 'admin1_members.htm',
                               'member1' => 'admin1_members1.htm',
                               'member2' => 'admin1_members2.htm'));
include_once(FILENAME_ADMIN_BODY);

$action = (isset($_GET['action']) ? $_GET['action'] : '');

if(tep_not_null($action)) 
{
 switch ($action) 
 {
  case 'member_new':
   $email_address=tep_db_prepare_input($_POST['TREF_email_address']);
   $admin_name=tep_db_prepare_input($_POST['TR_login_name']);
   $password=tep_db_prepare_input($_POST['TR_password']);
   $error= false;
   if (strlen($password) < MIN_PASSWORD_LENGTH && tep_not_null($password ))
   {
    $error= true;
    tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_MEMBERS, 'page=' . $_GET['page'] . '&mID=' . $_GET['mID'] . '&error=min-password-length&action=new_member'));
   }
   if($row=getAnyTableWhereData(RECRUITER_LOGIN_TABLE,"recruiter_email_address='".tep_db_input($email_address)."'","recruiter_id") ||
      $row=getAnyTableWhereData(RECRUITER_USERS_TABLE,"email_address='".tep_db_input($email_address)."'","id") ||
      $row=getAnyTableWhereData(ADMIN_TABLE,"admin_email_address='".tep_db_input($email_address)."'","admin_id") ||
      $row=getAnyTableWhereData(JOBSEEKER_LOGIN_TABLE,"jobseeker_email_address='".tep_db_input($email_address)."'","jobseeker_id"))
   {
    tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_MEMBERS, 'page=' . $_GET['page'] . '&mID=' . $_GET['mID'] . '&error=email&action=new_member'));
   }
   elseif($row=getAnyTableWhereData(ADMIN_TABLE,"admin_name='".tep_db_input($admin_name)."' ","admin_id"))
   {
    tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_MEMBERS, 'page=' . $_GET['page'] . '&mID=' . $_GET['mID'] . '&error=adminname&action=new_member'));
   }
   if(!$error)
   {
    if(tep_not_null($password))
    $makePassword = $password;
	else
    $makePassword = randomize();
    $hiddenPassword=$makePassword;
    $sql_data_array = array('admin_groups_id' => tep_db_prepare_input($_POST['TR_group_level']),
                            'admin_name' => tep_db_prepare_input($_POST['TR_login_name' ]),
                            'admin_firstname' => tep_db_prepare_input($_POST['TR_firstname']),
                            'admin_lastname' => tep_db_prepare_input($_POST['TR_lastname']),
                            'admin_email_address' => tep_db_prepare_input($_POST['TREF_email_address']),
                            'admin_password' => tep_encrypt_password($makePassword),
                            'inserted' => 'now()');
    tep_db_perform(ADMIN_TABLE, $sql_data_array);
    $row_id_check=getAnyTableWhereData(ADMIN_TABLE,"admin_name='".tep_db_input($_POST['TR_login_name'])."'","admin_id");
    $admin_id = $row_id_check['admin_id'];
    tep_mail($_POST['TR_firstname'] . ' ' . $_POST['TR_lastname'], $_POST['TREF_email_address'], ADMIN_EMAIL_SUBJECT, nl2br(sprintf(ADMIN_ADD_EMAIL_TEXT, $_POST['TR_firstname'], '<a href="'.HOST_NAME . PATH_TO_ADMIN.'">'.HOST_NAME . PATH_TO_ADMIN.'</a>', $_POST['TR_login_name'], $hiddenPassword, SITE_OWNER)), SITE_OWNER, ADMIN_EMAIL);
    tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_MEMBERS, 'page=' . $_GET['page'] . '&mID=' . $admin_id));
   }
  break;
  case 'member_edit':
   $admin_id = tep_db_prepare_input($_POST['admin_id']);
   $hiddenPassword = '-hidden-';
   $email_address=tep_db_prepare_input($_POST['TREF_email_address']);
   $admin_name=tep_db_prepare_input($_POST['TR_login_name']);
   if($row=getAnyTableWhereData(RECRUITER_LOGIN_TABLE,"recruiter_email_address='".tep_db_input($email_address)."'","recruiter_id") ||
      $row=getAnyTableWhereData(RECRUITER_USERS_TABLE,"email_address='".tep_db_input($email_address)."'","id") ||
      $row=getAnyTableWhereData(ADMIN_TABLE,"admin_email_address='".tep_db_input($email_address)."' and admin_id<>$admin_id","admin_id") ||
      $row=getAnyTableWhereData(JOBSEEKER_LOGIN_TABLE,"jobseeker_email_address='".tep_db_input($email_address)."'","jobseeker_id"))
   {
    tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_MEMBERS, 'page=' . $_GET['page'] . '&mID=' . $_GET['mID'] . '&error=email&action=edit_member'));
   }
   if($row=getAnyTableWhereData(ADMIN_TABLE,"admin_name='".tep_db_input($admin_name)."' and admin_id<>'".$admin_id."'","admin_id"))
   {
    tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_MEMBERS, 'page=' . $_GET['page'] . '&mID=' . $_GET['mID'] . '&error=adminname&action=edit_member'));
   }
   else 
   {
    $row1=getAnyTableWhereData(ADMIN_TABLE,"admin_id ='".$admin_id."'","admin_name");
    $sql_data_array = array('admin_groups_id' => tep_db_prepare_input($_POST['TR_group_level']),
                            'admin_name' => tep_db_prepare_input($_POST['TR_login_name']),
                            'admin_firstname' => tep_db_prepare_input($_POST['TR_firstname']),
                            'admin_lastname' => tep_db_prepare_input($_POST['TR_lastname']),
                            'admin_email_address' => tep_db_prepare_input($_POST['TREF_email_address']),
                            'updated' => 'now()');
    tep_db_perform(ADMIN_TABLE, $sql_data_array, 'update', 'admin_id = \'' . $admin_id . '\'');
    tep_mail($_POST['TR_firstname'] . ' ' . $_POST['TR_lastname'], $_POST['TREF_email_address'], ADMIN_EMAIL_SUBJECT, nl2br(sprintf(ADMIN_EMAIL_EDIT_TEXT, $_POST['TR_firstname'],'<a href="'.HOST_NAME . PATH_TO_ADMIN.'">'.HOST_NAME . PATH_TO_ADMIN.'</a>', $_POST['TR_login_name'],SITE_OWNER)), SITE_OWNER, ADMIN_EMAIL);
    tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_MEMBERS, 'page=' . $_GET['page'] . '&mID=' . $admin_id));
   }
  break;
  case 'member_delete':
   $admin_id = tep_db_prepare_input($_POST['admin_id']);
   $row1=getAnyTableWhereData(ADMIN_TABLE,"admin_id='".tep_db_input($admin_id)."'","admin_name");
   tep_db_query("delete from " . ADMIN_TABLE . " where admin_id = '" . $admin_id . "'");
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_MEMBERS, 'page=' . $_GET['page']));
  break;
  case 'group_define':
   $selected_checkbox = $_POST['groups_to_boxes'];
   $define_files_query = tep_db_query("select admin_files_id from " . ADMIN_FILES_TABLE . " order by admin_files_id");
   while ($define_files = tep_db_fetch_array($define_files_query)) 
   {
    $admin_files_id = $define_files['admin_files_id'];
    if (@in_array ($admin_files_id, $selected_checkbox)) 
    {
     $sql_data_array = array('admin_groups_id' => tep_db_prepare_input($_POST['checked_' . $admin_files_id]));
     //$set_group_id = $_POST['checked_' . $admin_files_id];
    }
    else 
    {
     $sql_data_array = array('admin_groups_id' => tep_db_prepare_input($_POST['unchecked_' . $admin_files_id]));
     //$set_group_id = $_POST['unchecked_' . $admin_files_id];
    }
    tep_db_perform(ADMIN_FILES_TABLE, $sql_data_array, 'update', 'admin_files_id = \'' . $admin_files_id . '\'');
   }
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_MEMBERS, 'gID=' . $_POST['admin_groups_id']));
  break;
  case 'group_delete':
   $set_groups_id = tep_db_prepare_input($_POST['set_groups_id']);
   tep_db_query("delete from " . ADMIN_GROUPS_TABLE . " where admin_groups_id = '" . $_GET['gID'] . "'");
   tep_db_query("alter table " . ADMIN_FILES_TABLE . " change admin_groups_id admin_groups_id set( " . $set_groups_id . " ) NOT NULL DEFAULT '1' ");
   tep_db_query("delete from " . ADMIN_TABLE . " where admin_groups_id = '" . $_GET['gID'] . "'");
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_MEMBERS, 'gID=groups'));
  break;        
  case 'group_edit':
   $admin_groups_name = ucwords(strtolower(tep_db_prepare_input($_POST['TR_group_level'])));
   $name_replace = preg_replace ("/ /", "%", $admin_groups_name);
   if (!tep_not_null($admin_groups_name) ) 
   {
    tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_MEMBERS, 'gID=' . $_GET[gID] . '&gName=false&action=edit_group'));
   } 
   else 
   {
    $check_groups_name_query = tep_db_query("select admin_groups_name as group_name_edit from " . ADMIN_GROUPS_TABLE . " where admin_groups_id <> " . $_GET['gID'] . " and admin_groups_name like '%" . $name_replace . "%'");
    $check_duplicate = tep_db_num_rows($check_groups_name_query);
    if ($check_duplicate > 0)
    {
     tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_MEMBERS, 'gID=' . $_GET['gID'] . '&gName=used&action=edit_group'));
    } 
    else 
    {
     $admin_groups_id = $_GET['gID'];
     tep_db_query("update " . ADMIN_GROUPS_TABLE . " set admin_groups_name = '" . $admin_groups_name . "' where admin_groups_id = '" . $admin_groups_id . "'");
     tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_MEMBERS, 'gID=' . $admin_groups_id));
    }
   }
  break;              
  case 'group_new':
   $admin_groups_name = ucwords(strtolower(tep_db_prepare_input($_POST['TR_group_name'])));
   $name_replace = preg_replace ("/ /", "%", $admin_groups_name);
   if (($admin_groups_name == '' || NULL)) 
   {
    tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_MEMBERS, 'gID=' . $_GET['gID'] . '&gName=false&action=new_group'));
   } 
   else 
   {
    $check_groups_name_query = tep_db_query("select admin_groups_name as group_name_new from " . ADMIN_GROUPS_TABLE . " where admin_groups_name like '%" . $name_replace . "%'");
    $check_duplicate = tep_db_num_rows($check_groups_name_query);
    if ($check_duplicate > 0)
    {
     tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_MEMBERS, 'gID=' . $_GET['gID'] . '&gName=used&action=new_group'));
    } 
    else 
    {
     $sql_data_array = array('admin_groups_name' => $admin_groups_name);
     tep_db_perform(ADMIN_GROUPS_TABLE, $sql_data_array);
     $row_id_check=getAnyTableWhereData(ADMIN_GROUPS_TABLE,"admin_groups_name='".tep_db_input($admin_groups_name)."'","admin_groups_id");
     $admin_groups_id = $row_id_check['admin_groups_id'];
     $set_groups_id = tep_db_prepare_input($_POST['set_groups_id']);
     $add_group_id = $set_groups_id . ',\'' . $admin_groups_id . '\'';
     tep_db_query("alter table " . ADMIN_FILES_TABLE . " change admin_groups_id admin_groups_id set( " . $add_group_id . ") NOT NULL DEFAULT '1' ");
     tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_MEMBERS, 'gID=' . $admin_groups_id));
    }
   }
  break;        
 }
}
//////////////////
if($_GET['gPath']) 
{
 $group_name_query = tep_db_query("select admin_groups_name from " . ADMIN_GROUPS_TABLE . " where admin_groups_id = " . $_GET['gPath']);
 $group_name = tep_db_fetch_array($group_name_query);
 if ($_GET['gPath'] == 1) 
 {
  $form= tep_draw_form('defineForm', PATH_TO_ADMIN.FILENAME_ADMIN1_MEMBERS, 'gID=' . $_GET['gPath']);
 } 
 elseif ($_GET['gPath'] != 1) 
 {
  $form= tep_draw_form('defineForm', PATH_TO_ADMIN.FILENAME_ADMIN1_MEMBERS, 'gID=' . $_GET['gPath'] . '&action=group_define', 'post', '');
  $form.= tep_draw_hidden_field('admin_groups_id', $_GET['gPath']); 
 }
 $db_boxes_query = tep_db_query("select admin_files_id as admin_boxes_id, admin_files_name as admin_boxes_name, admin_groups_id as boxes_group_id from " . ADMIN_FILES_TABLE . " where admin_files_is_boxes = '1' order by admin_files_name");
 while ($group_boxes = tep_db_fetch_array($db_boxes_query)) 
 {
  $group_boxes_files_query = tep_db_query("select admin_files_id, admin_files_name, admin_groups_id from " . ADMIN_FILES_TABLE . " where admin_files_is_boxes = '0' and admin_files_to_boxes = '" . $group_boxes['admin_boxes_id'] . "' order by admin_files_name");
  $selectedGroups = $group_boxes['boxes_group_id'];
  $groupsArray = explode(",", $selectedGroups);
  if (in_array($_GET['gPath'], $groupsArray)) 
  {     
   $del_boxes = array($_GET['gPath']);
   $result = array_diff ($groupsArray, $del_boxes);
   sort($result);
   $checkedBox = $selectedGroups;
   $uncheckedBox = implode (",",$result);
   $checked = true;
  } 
  else 
  {
   $add_boxes = array($_GET['gPath']);
   $result = array_merge ($add_boxes, $groupsArray);
   sort($result);
   $checkedBox = implode (",",$result);
   $uncheckedBox = $selectedGroups;
   $checked = false;
  }    
    //$group_boxes_files_query = tep_db_query("select admin_files_id, admin_files_name, admin_groups_id from " . ADMIN_FILES_TABLE . " where admin_files_is_boxes = '0' and admin_files_to_boxes = '" . $group_boxes['admin_boxes_id'] . "' order by admin_files_name");
  $file_names='';
  while($group_boxes_files = tep_db_fetch_array($group_boxes_files_query)) 
  {
   $selectedGroups = $group_boxes_files['admin_groups_id'];
   $groupsArray = explode(",", $selectedGroups);
   if (in_array($_GET['gPath'], $groupsArray)) 
   {     
    $del_boxes = array($_GET['gPath']);
    $result = array_diff ($groupsArray, $del_boxes);
    sort($result);
    $checkedBox = $selectedGroups;
    $uncheckedBox = implode (",", $result);
    $checked = true;
   } 
   else 
   {
    $add_boxes = array($_GET['gPath']);
    $result = array_merge ($add_boxes, $groupsArray);
    sort($result);
    $checkedBox = implode (",", $result);
    $uncheckedBox = $selectedGroups;
    $checked = false;
   }
   $file_names.='<table border="0" cellspacing="0" cellpadding="0">'.
   '             <tr>'.
   '              <td valign="top" class="dataTableContent">'.tep_draw_checkbox_field('groups_to_boxes[]', $group_boxes_files['admin_files_id'], $checked, '', 'id="subgroups_' . $group_boxes['admin_boxes_id'] . '" onClick="checkSub(this)"').'</td>'.
   '              <td class="dataTableContent">'.$group_boxes_files['admin_files_name'] . ' ' . tep_draw_hidden_field('checked_' . $group_boxes_files['admin_files_id'], $checkedBox) . tep_draw_hidden_field('unchecked_' . $group_boxes_files['admin_files_id'], $uncheckedBox).'</td>'.
   '             </tr>'.
   '            </table>';
  }
  $template->assign_block_vars('member2', array( 'file_names' => $file_names,
   'check_box' => tep_draw_checkbox_field('groups_to_boxes[]', $group_boxes['admin_boxes_id'], $checked, '', 'id="groups_' . $group_boxes['admin_boxes_id'] . '" onClick="checkGroups(this)"'),
   'check_box_value' => ucfirst(substr_replace(substr_replace($group_boxes['admin_boxes_name'],'',0,6), '', -4)). ' ' . tep_draw_hidden_field('checked_' . $group_boxes['admin_boxes_id'], $checkedBox) . tep_draw_hidden_field('unchecked_' . $group_boxes['admin_boxes_id'], $uncheckedBox),
   ));
 }
 if($_GET['gPath'] != 1) 
 {
  $buttons='<a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_MEMBERS, 'gID=' . $_GET['gPath']) . '">' .IMAGE_CANCEL . '</a> ' . tep_button('Save','class="btn btn-primary"'); 
 } 
 else 
 { 
  $buttons=tep_button('Back','class="btn btn-secondary"'); 
 }
} 
elseif ($_GET['gID']) 
{
 $db_groups_query = tep_db_query("select * from " . ADMIN_GROUPS_TABLE.(($_SESSION['sess_admin']!='Yes')?" where  admin_groups_id not in (1) ":"" ). " order by admin_groups_id");
 $add_groups_prepare = '\'0\'' ;
 $del_groups_prepare = '\'0\'' ;
 if($_SESSION['sess_admin']!='Yes')
 {
  $add_groups_prepare .= ',\'1\'' ;
  $del_groups_prepare .= ',\'1\'' ;
 }

 $count_groups = 0;
 $db_groups_num_row=tep_db_num_rows($db_groups_query);
 if($db_groups_num_row > 0)
 {
  $alternate=1;
  while ($groups = tep_db_fetch_array($db_groups_query)) 
  {
   $add_groups_prepare .= ',\'' . $groups['admin_groups_id'] . '\'' ;
   if (((!$_GET['gID']) || ($_GET['gID'] == $groups['admin_groups_id']) || ($_GET['gID'] == 'groups')) && (!$gInfo) ) 
   {
    $gInfo = new objectInfo($groups);
   }
   if ( (is_object($gInfo)) && ($groups['admin_groups_id'] == $gInfo->admin_groups_id) ) 
   {
    $row_selected=' id="defaultSelected" class="table-secondary dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_MEMBERS, 'gID=' . $groups['admin_groups_id'] . '&action=edit_group') . '\'"';
   } 
   else 
   {
    $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_MEMBERS, 'gID=' . $groups['admin_groups_id']) . '\'"';
    $del_groups_prepare .= ',\'' . $groups['admin_groups_id'] . '\'' ;
   }
   if ( (is_object($gInfo)) && ($groups['admin_groups_id'] == $gInfo->admin_groups_id) ) 
   { 
    $action_image=tep_image(PATH_TO_IMAGE.'icon_arrow_right.gif'); 
   } 
   else 
   { 
    $action_image='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_MEMBERS, 'gID=' . $groups['admin_groups_id']) . '">' . tep_image(PATH_TO_IMAGE.'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; 
   }
   $alternate++;
   $template->assign_block_vars('member1', array( 'row_selected' => $row_selected,
    'action' => $action_image,
    'name' => tep_db_output($groups['admin_groups_name']),
    ));
   $count_groups++;
  }
 }
} 
else 
{
 $db_admin_query_raw = "select * from " . ADMIN_TABLE.(($_SESSION['sess_admin']!='Yes')?" where  is_admin !='Yes' ":"")." order by admin_firstname";
 $db_admin_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $db_admin_query_raw, $db_admin_query_numrows);
 $db_admin_query = tep_db_query($db_admin_query_raw);
 $db_admin_num_row = tep_db_num_rows($db_admin_query);
 if($db_admin_num_row > 0)
 {
  $alternate=1;
  while ($admin = tep_db_fetch_array($db_admin_query)) 
  {
   $admin_group_query = tep_db_query("select admin_groups_name from " . ADMIN_GROUPS_TABLE . " where admin_groups_id = '" . $admin['admin_groups_id'] . "'");
   $admin_group = tep_db_fetch_array ($admin_group_query);
   if (((!$_GET['mID']) || ($_GET['mID'] == $admin['admin_id'])) && (!$mInfo) ) 
   {
    $mInfo_array = array_merge($admin, $admin_group);
    $mInfo = new objectInfo($mInfo_array);
   }
   if ( (is_object($mInfo)) && ($admin['admin_id'] == $mInfo->admin_id) ) 
			{
				$row_selected=' id="defaultSelected" class="table-secondary dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_MEMBERS, 'page=' . $_GET['page'] . '&mID=' . $admin['admin_id'] . '&action=edit_member') . '\'"';
			}
			else
			{
				$row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_MEMBERS, 'page=' . $_GET['page'] . '&mID=' . $admin['admin_id']) . '\'"';
			}
			if ( (is_object($mInfo)) && ($admin['admin_id'] == $mInfo->admin_id) ) 
			{ 
				$action_image=tep_image(PATH_TO_IMAGE.'icon_arrow_right.gif'); 
			} 
			else 
			{ 
				$action_image='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_MEMBERS, 'page=' . $_GET['page'] . '&mID=' . $admin['admin_id']) . '">' . tep_image(PATH_TO_IMAGE.'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; 
			}
   $alternate++;
   $template->assign_block_vars('member', array( 'row_selected' => $row_selected,
    'action' => $action_image,
    'name' => tep_db_output($admin['admin_name']),
    'email' => tep_db_output($admin['admin_email_address']),
    'group' => tep_db_output($admin_group['admin_groups_name']),
    'log_num' => tep_db_output($admin['number_of_logon']),
    ));
		}
	}
}
/////
$ADMIN_RIGHT_HTML="";

$heading = array();
$contents = array();
switch ($action) 
{  
 case 'new_member': 
 $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW . '</b>');
 $contents = array('form' => tep_draw_form('newmember', PATH_TO_ADMIN.FILENAME_ADMIN1_MEMBERS, 'action=member_new&page=' . $_GET['page'] . '&mID=' . $_GET['mID'], 'post', 'onsubmit="return ValidateForm(this)"')); 
 if ($_GET['error']=='adminname') 
 {
  $contents[] = array('text' => TEXT_INFO_ERROR_ADMINNAME); 
 }
 else if ($_GET['error']=='email') 
 {
  $contents[] = array('text' => TEXT_INFO_ERROR_EMAIL); 
 }
 $contents[] = array('text' => '<br>&nbsp;' . TEXT_INFO_LOGINNAME . '<br>' . tep_draw_input_field('TR_login_name', '', 'class="form-control form-control-sm mt-1"')); 
 $contents[] = array('text' => '<br>&nbsp;' . TEXT_INFO_FIRSTNAME . '<br>' . tep_draw_input_field('TR_firstname', '', 'class="form-control form-control-sm mt-1"')); 
 $contents[] = array('text' => '<br>&nbsp;' . TEXT_INFO_LASTNAME . '<br>' . tep_draw_input_field('TR_lastname', '', 'class="form-control form-control-sm mt-1"'));
 $contents[] = array('text' => '<br>&nbsp;' . TEXT_INFO_EMAIL . '<br>' . tep_draw_input_field('TREF_email_address', '', 'class="form-control form-control-sm mt-1"')); 
 $contents[] = array('text' => '<br>&nbsp;' . TEXT_INFO_PASSWORD . '<br>' . tep_draw_password_field('TR_password', '', true,' minlength="5" maxlength="15" class="form-control form-control-sm mt-1"')); 
 $groups_query = tep_db_query("select admin_groups_id, admin_groups_name from " . ADMIN_GROUPS_TABLE.(($_SESSION['sess_admin']!='Yes')?" where  admin_groups_id not in (1) ":""));
 while ($groups = tep_db_fetch_array($groups_query)) 
 {
  $groups_array[] = array('id' => $groups['admin_groups_id'],
                          'text' => $groups['admin_groups_name']);
 }
 $contents[] = array('text' => '<br>&nbsp;' . TEXT_INFO_GROUP . '<br>' . tep_draw_pull_down_menu('TR_group_level', $groups_array, '', 'class="form-control form-control-sm mt-1"')); 
 $contents[] = array('align' => 'left', 'text' => '<br>'
 
 . tep_draw_submit_button_field('', IMAGE_INSERT,'class="btn btn-primary"') . '
 <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_MEMBERS, 'page=' . $_GET['page'] . '&mID=' . $_GET['mID']) . '">' .IMAGE_CANCEL . '</a>');    
 break;
 case 'edit_member': 
 $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW . '</b>');
 $contents = array('form' => tep_draw_form('newmember', PATH_TO_ADMIN.FILENAME_ADMIN1_MEMBERS, 'action=member_edit&page=' . $_GET['page'] . '&mID=' . $_GET['mID'], 'post', ' onsubmit="return ValidateForm(this)"')); 
 if ($_GET['error']=='adminname') 
 {
  $contents[] = array('text' => TEXT_INFO_ERROR_ADMINNAME); 
 }
 else if ($_GET['error']=='email') 
 {
  $contents[] = array('text' => TEXT_INFO_ERROR_EMAIL); 
 }
 $contents[] = array('text' => tep_draw_hidden_field('admin_id', $mInfo->admin_id)); 
 $contents[] = array('text' => '<br>&nbsp;' . TEXT_INFO_LOGINNAME . '<br>' . tep_draw_input_field('TR_login_name', $mInfo->admin_name, 'class="form-control form-control-sm mt-1"')); 
 $contents[] = array('text' => '<br>&nbsp;' . TEXT_INFO_FIRSTNAME . '<br>' . tep_draw_input_field('TR_firstname', $mInfo->admin_firstname, 'class="form-control form-control-sm mt-1"')); 
 $contents[] = array('text' => '<br>&nbsp;' . TEXT_INFO_LASTNAME . '<br>' . tep_draw_input_field('TR_lastname', $mInfo->admin_lastname, 'class="form-control form-control-sm mt-1"'));
 $contents[] = array('text' => '<br>&nbsp;' . TEXT_INFO_EMAIL . '<br>' . tep_draw_input_field('TREF_email_address', $mInfo->admin_email_address, 'class="form-control form-control-sm mt-1"')); 
 if ($mInfo->admin_id == 1) 
 {      
  $contents[] = array('text' => tep_draw_hidden_field('TR_group_level', $mInfo->admin_groups_id));
 } 
 else 
 {
  $groups_array = array();
  $groups_query = tep_db_query("select admin_groups_id, admin_groups_name from " . ADMIN_GROUPS_TABLE.(($_SESSION['sess_admin']!='Yes')?" where  admin_groups_id not in (1) ":""));
  while ($groups = tep_db_fetch_array($groups_query)) 
  {
   $groups_array[] = array('id' => $groups['admin_groups_id'],
                           'text' => $groups['admin_groups_name']);
  }
  $contents[] = array('text' => '<br>&nbsp;' . TEXT_INFO_GROUP . '<br>&nbsp;' . tep_draw_pull_down_menu('TR_group_level', $groups_array, $mInfo->admin_groups_id)); 
 }
 $contents[] = array('align' => 'left', 'text' => '<br>'
 
 . tep_draw_submit_button_field('', IMAGE_UPDATE,'class="btn btn-primary"') . '
 <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_MEMBERS, 'page=' . $_GET['page'] . '&mID=' . $_GET['mID']) . '">' .IMAGE_CANCEL . '</a>');    
 break;
 case 'del_member': 
 $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE . '</b>');
 if ($mInfo->admin_id == 1 || $mInfo->admin_email_address == ADMIN_EMAIL) 
 {
  $contents[] = array('align' => 'left', 'text' => '<br><a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_MEMBERS, 'page=' . $_GET['page'] . '&mID=' . $mInfo->admin_id) . '">' . IMAGE_BACK . '</a><br>&nbsp;');
 } 
 else 
 {
  $contents = array('form' => tep_draw_form('edit', PATH_TO_ADMIN.FILENAME_ADMIN1_MEMBERS, 'action=member_delete&page=' . $_GET['page'] . '&mID=' . $admin['admin_id'], 'post', '')); 
  $contents[] = array('text' => tep_draw_hidden_field('admin_id', $mInfo->admin_id));
  $contents[] = array('align' => 'left', 'text' =>  sprintf(TEXT_INFO_DELETE_INTRO, $mInfo->admin_firstname . ' ' . $mInfo->admin_lastname));    
  $contents[] = array('align' => 'left', 'text' => '<br>' . tep_button('Delete','class="btn btn-primary"') . ' <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_MEMBERS, 'page=' . $_GET['page'] . '&mID=' . $_GET['mID']) . '">' . IMAGE_CANCEL . '</a>');    
 }
 break;
 case 'new_group':
 $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_GROUPS . '</b>');
 $contents = array('form' => tep_draw_form('new_group', PATH_TO_ADMIN.FILENAME_ADMIN1_MEMBERS, 'action=group_new&gID=' . $gInfo->admin_groups_id, 'post', ' onsubmit="return ValidateForm(this)"')); 
 if ($_GET['gName'] == 'false') 
 {
  $contents[] = array('text' => TEXT_INFO_GROUPS_NAME_FALSE . '<br>&nbsp;');
 } 
 elseif ($_GET['gName'] == 'used') 
 {
  $contents[] = array('text' => TEXT_INFO_GROUPS_NAME_USED . '<br>&nbsp;');
 }
 $contents[] = array('text' => tep_draw_hidden_field('set_groups_id', substr($add_groups_prepare, 4)) );
 $contents[] = array('text' => TEXT_INFO_GROUPS_NAME . '<br>');      
 $contents[] = array('align' => 'left', 'text' => tep_draw_input_field('TR_group_name', '' , 'class="form-control form-control-sm"'));      
 $contents[] = array('align' => 'left', 'text' => '<br>
 <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_MEMBERS, 'gID=' . $gInfo->admin_groups_id) . '">' .IMAGE_CANCEL . '</a> '
  . tep_draw_submit_button_field('', IMAGE_NEXT,'class="btn btn-primary"') );    
 break;
 case 'edit_group': 
 $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_GROUP . '</b>');
 $contents = array('form' => tep_draw_form('edit_group', PATH_TO_ADMIN.FILENAME_ADMIN1_MEMBERS, 'action=group_edit&gID=' . $_GET['gID'], 'post', ' onsubmit="return ValidateForm(this)"')); 
 if ($_GET['gName'] == 'false') 
 {
  $contents[] = array('text' => TEXT_INFO_GROUPS_NAME_FALSE . '<br>&nbsp;');
 } 
 elseif ($_GET['gName'] == 'used') 
 {
  $contents[] = array('text' => TEXT_INFO_GROUPS_NAME_USED . '<br>&nbsp;');
 }      
 $contents[] = array('align' => 'left', 'text' => TEXT_INFO_EDIT_GROUP_INTRO . '<br>&nbsp;<br>' . tep_draw_input_field('TR_group_level', $gInfo->admin_groups_name, 'class="form-control form-control-sm"')); 
 $contents[] = array('align' => 'left', 'text' => '<br>'
 . tep_draw_submit_button_field('', IMAGE_SAVE,'class="btn btn-primary"') . '
 <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_MEMBERS, 'gID=' . $gInfo->admin_groups_id) . '">' .IMAGE_CANCEL . '</a>');    
 break;
 case 'del_group': 
 $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_GROUPS . '</b>');
 $contents = array('form' => tep_draw_form('delete_group', PATH_TO_ADMIN.FILENAME_ADMIN1_MEMBERS, 'action=group_delete&gID=' . $gInfo->admin_groups_id, 'post', '')); 
 if ($gInfo->admin_groups_id == 1) 
 {
  $contents[] = array('align' => 'left', 'text' => sprintf(TEXT_INFO_DELETE_GROUPS_INTRO_NOT, $gInfo->admin_groups_name));
  $contents[] = array('align' => 'left', 'text' => '<br>
  <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_MEMBERS, 'gID=' . $_GET['gID']) . '">' .IMAGE_BACK . '</a><br>');
 } 
 else 
 {
  $contents[] = array('text' => tep_draw_hidden_field('set_groups_id', substr($del_groups_prepare, 4)) );
  $contents[] = array('align' => 'left', 'text' => sprintf(TEXT_INFO_DELETE_GROUPS_INTRO, $gInfo->admin_groups_name));    
  $contents[] = array('align' => 'left', 'text' => '<br>'
   . tep_draw_submit_button_field('', IMAGE_DELETE,'class="btn btn-primary"') . '
   <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_MEMBERS, 'gID=' . $_GET['gID']) . '">' .IMAGE_CANCEL . '</a><br>');    
 }
 break;
 case 'define_group':      
 $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DEFINE . '</b>');
 $contents[] = array('text' => sprintf(TEXT_INFO_DEFINE_INTRO, $group_name['admin_groups_name']));
 if ($_GET['gPath'] == 1) 
 {
  $contents[] = array('align' => 'left', 'text' => '<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_MEMBERS, 'gID=' . $_GET['gPath']) . '">' . tep_button('Back','class="btn btn-secondary"') . '</a><br>');      
 }
 break;
 default:
 if (is_object($mInfo)) 
 {
  $heading[] = array('text' => '<b>' . tep_db_output($mInfo->admin_name) . '</b>');
  $contents[] = array('align' => 'left', 'text' => '<a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_MEMBERS, 'page=' . $_GET['page'] . '&mID=' . $mInfo->admin_id . '&action=edit_member') . '">' .IMAGE_EDIT . '</a>
  <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_MEMBERS, 'page=' . $_GET['page'] . '&mID=' . $mInfo->admin_id . '&action=del_member') . '">' . IMAGE_DELETE . '</a><br>&nbsp;');
  $contents[] = array('text' => '<b>' . TEXT_INFO_LOGINNAME . '</b><br>&nbsp;&nbsp;' . $mInfo->admin_name );
  $contents[] = array('text' => '<b>' . TEXT_INFO_FULLNAME . '</b><br>&nbsp;&nbsp;' . $mInfo->admin_firstname . ' ' . $mInfo->admin_lastname);
  $contents[] = array('text' => '<b>' . TEXT_INFO_EMAIL . '</b><br>&nbsp;&nbsp;' . $mInfo->admin_email_address);
  $contents[] = array('text' => '<b>' . TEXT_INFO_GROUP . '</b>&nbsp;&nbsp;' . $mInfo->admin_groups_name);
  $contents[] = array('text' => '<b>' . TEXT_INFO_CREATED . '</b><br>&nbsp;&nbsp;' . $mInfo->inserted);
  $contents[] = array('text' => '<b>' . TEXT_INFO_MODIFIED . '</b><br>&nbsp;&nbsp;' . $mInfo->updated);
  $contents[] = array('text' => '<b>' . TEXT_INFO_LOGDATE . '</b><br>&nbsp;&nbsp;' . $mInfo->last_login_time);
  $contents[] = array('text' => '<b>' . TEXT_INFO_LOGNUM . '</b>' . $mInfo->number_of_logon);
  $contents[] = array('text' => '<br>');
 } 
 elseif (is_object($gInfo)) 
 {
  $heading[] = array('text' => '<div class="mb-1 font-weight-bold">' . tep_db_output($gInfo->admin_groups_name) . '</div>');
  $contents[] = array('align' => 'left', 'text' => '
  <a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_MEMBERS, 'gPath=' . $gInfo->admin_groups_id . '&action=define_group') . '">' .IMAGE_FILE_PERMISSION . '</a>
  <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_MEMBERS, 'gID=' . $gInfo->admin_groups_id . '&action=edit_group') . '">' .IMAGE_EDIT . '</a>
  <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_MEMBERS, 'gID=' . $gInfo->admin_groups_id . '&action=del_group') . '">' .IMAGE_DELETE . '</a>');
  $contents[] = array('text' => '<br>' . TEXT_INFO_DEFAULT_GROUPS_INTRO . '<br>&nbsp');
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
/////
if($_GET['gPath']) 
{
 $template->assign_vars(array(
  'HEADING_TITLE'=>HEADING_TITLE,
  'form'=>$form,
  'TABLE_HEADING_GROUPS_DEFINE'=>TABLE_HEADING_GROUPS_DEFINE,
  'buttons'=>$buttons,
  'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
  'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
  'update_message'=>$messageStack->output()));
 $template->pparse('member2');
}
else if($_GET['gID'])
{
 $template->assign_vars(array(
  'HEADING_TITLE'=>HEADING_TITLE,
  'TABLE_HEADING_GROUPS_NAME'=>TABLE_HEADING_GROUPS_NAME,
  'TABLE_HEADING_ACTION'=>TABLE_HEADING_ACTION,
  'count_rows'=>TEXT_COUNT_GROUPS.$count_groups,
  'new_button'=>'<a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_MEMBERS) . '">' .IMAGE_BACK . '</a>
  <a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_MEMBERS, 'gID=' . $gInfo->admin_groups_id . '&action=new_group') . '">' .IMAGE_NEW_GROUP . '</a>',
  'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
  'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
  'update_message'=>$messageStack->output()));
 $template->pparse('member1');
}
else
{
 $template->assign_vars(array(
  'HEADING_TITLE'=>HEADING_TITLE,
  'TABLE_HEADING_NAME'=>TABLE_HEADING_NAME,
  'TABLE_HEADING_EMAIL'=>TABLE_HEADING_EMAIL,
  'TABLE_HEADING_GROUPS'=>TABLE_HEADING_GROUPS,
  'TABLE_HEADING_LOG_NUM'=>TABLE_HEADING_LOG_NUM,
  'TABLE_HEADING_ACTION'=>TABLE_HEADING_ACTION,
  'count_rows'=>$db_admin_split->display_count($db_admin_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_MEMBERS),
  'no_of_pages'=>$db_admin_split->display_links($db_admin_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']),
  'new_button'=>'<a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_MEMBERS, 'gID=groups') . '">' . IMAGE_GROUPS . '</a>
  <a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_MEMBERS, 'page=' . $_GET['page'] . '&mID=' . $mInfo->admin_id . '&action=new_member') . '"><i class="bi bi-plus-lg me-2"></i>' . IMAGE_NEW_MEMBER . '</a>',
  'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
  'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
  'update_message'=>$messageStack->output()));
 $template->pparse('member');
}
?>