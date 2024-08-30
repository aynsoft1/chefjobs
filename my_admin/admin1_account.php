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
include_once("../general_functions/password_funcs.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_ACCOUNT);
$template->set_filenames(array('account1' => 'admin1_account1.htm','account2' => 'admin1_account2.htm'));
include_once(FILENAME_ADMIN_BODY);

$action = (isset($_GET['action']) ? $_GET['action'] : '');

if (tep_not_null($action))
{
 switch ($action)
 {
  case 'check_password':
   $check_pass_query = tep_db_query("select admin_password as confirm_password from " . ADMIN_TABLE . " where admin_id = '" . $_POST['id_info'] . "'");
   $check_pass = tep_db_fetch_array($check_pass_query);
   // Check that password is good
   if (!tep_validate_password($_POST['password_confirmation'], $check_pass['confirm_password']))
   {
    tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ACCOUNT, 'action=check_account&error=password'));
   }
   else
   {
    //$confirm = 'confirm_account';
    $_SESSION['confirm_account']='y';
    tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ACCOUNT, 'action=edit_process'));
   }
  break;
  case 'save_account':
   $admin_id = tep_db_prepare_input($_POST['id_info']);
   $admin_email_address = tep_db_prepare_input($_POST['admin_email_address']);
   $stored_email = array();
   $stored_adminname=array();
   $hiddenPassword = '-hidden-';
   $check_email_query = tep_db_query("select admin_name, admin_email_address from " . ADMIN_TABLE . " where admin_id <> " . $admin_id . "");
   while ($check_email = tep_db_fetch_array($check_email_query))
   {
    $stored_email[] = $check_email['admin_email_address'];
    $stored_adminname[] = $check_email['admin_name'];
   }
   if (in_array($_POST['TREF_email_address'], $stored_email))
   {
    tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ACCOUNT, 'action=edit_process&error=email'));
   }
   else if (in_array($_POST['TR_login_name'], $stored_adminname))
   {
    tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ACCOUNT, 'action=edit_process&error=adminname'));
   }
   else
   {
    $password=tep_db_prepare_input($_POST['TR_password']);
    $admin_password=tep_encrypt_password($password);
    $sql_data_array = array('admin_name' => tep_db_prepare_input($_POST['TR_login_name']),
                            'admin_firstname' => tep_db_prepare_input($_POST['TR_firstname']),
                            'admin_lastname' => tep_db_prepare_input($_POST['TR_lastname']),
                            'admin_email_address' => tep_db_prepare_input($_POST['TREF_email_address']),
                            'admin_password' => $admin_password,
                            'updated' => 'now()');
    tep_db_perform(ADMIN_TABLE, $sql_data_array, 'update', "admin_id = '" . tep_db_input($admin_id) . "'");
    tep_mail($_POST['TR_firstname'] . ' ' . $_POST['TR_lastname'], $_POST['TREF_email_address'], ADMIN_EMAIL_SUBJECT, nl2br(sprintf(ADMIN_EMAIL_TEXT, $_POST['TR_firstname'], '<a href="'.HOST_NAME . PATH_TO_ADMIN.'">'.HOST_NAME . PATH_TO_ADMIN.'</a>', $_POST['TR_login_name'], $_POST['TR_password'], SITE_OWNER)), SITE_OWNER, ADMIN_EMAIL);
    tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ACCOUNT, 'mID=' . $admin_id));
   }
  break;
 }
}
/////
if($action == 'edit_process')
{
 $form=tep_draw_form('account', PATH_TO_ADMIN.FILENAME_ADMIN1_ACCOUNT, 'action=save_account', 'post', 'enctype="multipart/form-data" onsubmit="return ValidateForm(this)"');
}
elseif ($action == 'check_account')
{
 $form= tep_draw_form('account', PATH_TO_ADMIN.FILENAME_ADMIN1_ACCOUNT, 'action=check_password', 'post', 'enctype="multipart/form-data"');
}
else
{
 $form= tep_draw_form('account', PATH_TO_ADMIN.FILENAME_ADMIN1_ACCOUNT, 'action=check_account', 'post', 'enctype="multipart/form-data"');
}

$my_account_query = tep_db_query ("select a.admin_id, a.admin_name, a.admin_firstname, a.admin_lastname, a.admin_email_address, a.inserted, a.updated, a.last_login_time, a.number_of_logon, g.admin_groups_name from " . ADMIN_TABLE . " a, " . ADMIN_GROUPS_TABLE . " g where a.admin_id= '" . $_SESSION['sess_adminid'] . "' and a.admin_groups_id=g.admin_groups_id");
$myAccount = tep_db_fetch_array($my_account_query);
if ( ($action == 'edit_process') && ($_SESSION['confirm_account']=='y') )
{
}
else
{
 unset($_SESSION['confirm_account']);
}
if ($action=='edit_process')
{
 $buttons='<a  href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ACCOUNT) . '">' . tep_button('Back','class="btn btn-secondary"') . '</a> ';
 if($_SESSION['confirm_account']=='y')
 {
  $buttons.=tep_draw_submit_button_field('','Save','class="btn btn-primary"');//tep_image_submit(PATH_TO_BUTTON.'button_save.gif', IMAGE_SAVE);
 }
}
elseif ($action == 'check_account')
{
 $buttons= '&nbsp;';
}
else
{
 $buttons=tep_draw_submit_button_field('','Edit','class="btn btn-primary margins"');//tep_image_submit(PATH_TO_BUTTON.'button_edit.gif', IMAGE_EDIT);
}
/////
$ADMIN_RIGHT_HTML="";
$heading = array();
$contents = array();
  switch ($action) {
    case 'edit_process':
      $heading[] = array('text' => '<b>&nbsp;' . TEXT_INFO_HEADING_DEFAULT . '</b>');

      $contents[] = array('text' => TEXT_INFO_INTRO_EDIT_PROCESS . tep_draw_hidden_field('id_info', $myAccount['admin_id']));
      //$contents[] = array('align' => 'left', 'text' => '<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ACCOUNT) . '">' . tep_image('button_back.gif', IMAGE_BACK) . '</a> ' . tep_image_submit('button_confirm.gif', IMAGE_CONFIRM, 'onClick="validateForm();return document.returnValue"') . '<br>&nbsp');
      break;
    case 'check_account':
      $heading[] = array('text' => '<b>&nbsp;' . TEXT_INFO_HEADING_CONFIRM_PASSWORD . '</b>');

      $contents[] = array('text' => '&nbsp;' . TEXT_INFO_INTRO_CONFIRM_PASSWORD . tep_draw_hidden_field('id_info', $myAccount['admin_id']));
      if ($_GET['error']) {
        $contents[] = array('text' => '&nbsp;' . TEXT_INFO_INTRO_CONFIRM_PASSWORD_ERROR);
      }
      $contents[] = array('align' => 'left', 'text' => tep_draw_password_field('password_confirmation'));
      $contents[] = array('align' => 'left', 'text' => '<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ACCOUNT) . '">' . tep_button('Back','class="mt-2 btn btn-secondary"') . '</a> ' . tep_draw_submit_button_field('','Confirm','class="mt-2 btn btn-primary margins"') . '<br>&nbsp');
      break;
    default:
      $heading[] = array('text' => '<b>&nbsp;' . TEXT_INFO_HEADING_DEFAULT . '</b>');

      $contents[] = array('text' => TEXT_INFO_INTRO_DEFAULT);
      $contents[] = array('align' => 'left', 'text' => tep_draw_submit_button_field('','Edit','class="btn btn-primary"') . '<br>&nbsp');
      if ($myAccount['admin_email_address'] == 'admin@localhost') {
        $contents[] = array('text' => sprintf(TEXT_INFO_INTRO_DEFAULT_FIRST, $myAccount['admin_firstname']) . '<br>&nbsp');
      } elseif (($myAccount['updated'] == '0000-00-00 00:00:00') || ($myAccount['number_of_logon'] <= 1) ) {
        $contents[] = array('text' => sprintf(TEXT_INFO_INTRO_DEFAULT_FIRST_TIME, $myAccount['admin_firstname']) . '<br>&nbsp');
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
if ( ($action == 'edit_process') && ($_SESSION['confirm_account']=='y') )
{
 $template->assign_vars(array(
  'HEADING_TITLE'=>HEADING_TITLE,
  'TABLE_HEADING_ACCOUNT'=>TABLE_HEADING_ACCOUNT,
  'form'=>$form,
  'TEXT_INFO_LOGINNAME'=>TEXT_INFO_LOGINNAME,
  'TEXT_INFO_LOGINNAME1'=>tep_draw_input_field('TR_login_name', $myAccount['admin_name'],'',true).(($_GET['error']=='adminname')?'&nbsp;'.TEXTINFO_ERROR_ADMINNAME:''),
  'TEXT_INFO_FIRSTNAME'=>TEXT_INFO_FIRSTNAME,
  'TEXT_INFO_FIRSTNAME1'=>tep_draw_input_field('TR_firstname', $myAccount['admin_firstname'],'',true),
  'TEXT_INFO_LASTNAME'=>TEXT_INFO_LASTNAME,
  'TEXT_INFO_LASTNAME1'=>tep_draw_input_field('TR_lastname', $myAccount['admin_lastname'],'',true),
  'TEXT_INFO_EMAIL'=>TEXT_INFO_EMAIL,
  'TEXT_INFO_EMAIL1'=>tep_draw_input_field('TREF_email_address', $myAccount['admin_email_address'],'',true).(($_GET['error']=='email')?'&nbsp;'.TEXT_INFO_ERROR_EMAIL:''),
  'TEXT_INFO_PASSWORD'=>TEXT_INFO_PASSWORD,
  'TEXT_INFO_PASSWORD1'=>tep_draw_password_field('TR_password','',true),
  'TEXT_INFO_PASSWORD_CONFIRM'=>TEXT_INFO_PASSWORD_CONFIRM,
  'TEXT_INFO_PASSWORD_CONFIRM1'=>tep_draw_password_field('TR_confirm_password','',true),
  'buttons'=>$buttons,
  'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
  'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
  'update_message'=>$messageStack->output()));
 $template->pparse('account2');
}
else
{
 $template->assign_vars(array(
  'HEADING_TITLE'=>HEADING_TITLE,
  'TABLE_HEADING_ACCOUNT'=>TABLE_HEADING_ACCOUNT,
  'form'=>$form,
  'TEXT_INFO_LOGINNAME'=>TEXT_INFO_LOGINNAME,
  'TEXT_INFO_LOGINNAME1'=>tep_db_output($myAccount['admin_name']),
  'TEXT_INFO_FULLNAME'=>TEXT_INFO_FULLNAME,
  'TEXT_INFO_FULLNAME1'=>tep_db_output($myAccount['admin_firstname'] . ' ' . $myAccount['admin_lastname']),
  'TEXT_INFO_EMAIL'=>TEXT_INFO_EMAIL,
  'TEXT_INFO_EMAIL1'=>tep_db_output($myAccount['admin_email_address']),
  'TEXT_INFO_PASSWORD'=>TEXT_INFO_PASSWORD,
  'TEXT_INFO_PASSWORD_HIDDEN'=>TEXT_INFO_PASSWORD_HIDDEN,
  'TEXT_INFO_GROUP'=>TEXT_INFO_GROUP,
  'TEXT_INFO_GROUP1'=>tep_db_output($myAccount['admin_groups_name']),
  'TEXT_INFO_CREATED'=>TEXT_INFO_CREATED,
  'TEXT_INFO_CREATED1'=>tep_db_output($myAccount['inserted']),
  'TEXT_INFO_LOGNUM'=>TEXT_INFO_LOGNUM,
  'TEXT_INFO_LOGNUM1'=>tep_db_output($myAccount['number_of_logon']),
  'TEXT_INFO_LOGDATE'=>TEXT_INFO_LOGDATE,
  'TEXT_INFO_LOGDATE1'=>tep_db_output($myAccount['last_login_time']),
  'modified'=>tep_db_output(TEXT_INFO_MODIFIED . $myAccount['updated']),
  'buttons'=>$buttons,
  'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
  'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
  'update_message'=>$messageStack->output()));
 $template->pparse('account1');
}
?>