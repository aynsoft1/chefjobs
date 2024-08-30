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
include_once("../general_functions/password_funcs.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_INDEX);
$template->set_filenames(array('login' => 'login.htm'));
include_once(FILENAME_ADMIN_BODY);

if(check_login("admin"))
{
 tep_redirect(FILENAME_ADMIN1_CONTROL_PANEL);
}

if($_POST['action']=="check")
{
 $admin_name=tep_db_prepare_input($_POST['TR_User_Name']);
	$admin_password= tep_db_prepare_input($_POST['TR_Password']);
	$whereClause="admin_name ='".tep_db_input($admin_name)."'";
	if($row=getAnyTableWhereData(ADMIN_TABLE,$whereClause))
	{
  if(!tep_validate_password($admin_password, $row['admin_password']))
  {
   $messageStack->add(SORRY_ADMIN_LOGIN_MATCH, 'error');
  }
  else
  {
   $ip_address=$_SERVER['REMOTE_ADDR'];
   $last_ip_address=tep_db_prepare_input($row['ip_address']);
   $number_of_logon=$row['number_of_logon']+1;
   $sql_data_array = array('last_login_time' => 'now()',
                           'ip_address' => $ip_address,
                           'last_ip_address' => $last_ip_address,
                           'number_of_logon' => $number_of_logon);
   tep_db_perform(ADMIN_TABLE, $sql_data_array, 'update', "admin_id = '" . $row['admin_id'] . "'");
   @session_unset();
   @session_destroy();
   session_start();
   $_SESSION['sess_adminlogin']="y";
   $_SESSION['sess_adminid']=$row["admin_id"];
   $_SESSION['sess_admin']=$row["is_admin"];
   tep_redirect(FILENAME_ADMIN1_CONTROL_PANEL.'?selected_box=dashboard');
  }
	}
	else
	{
  $messageStack->add(SORRY_ADMIN_LOGIN_MATCH, 'error');
	}
}
//SetCookie ( "webcalendar_session", "", 0, $_SERVER["HTTP_HOST"].'/'.PATH_TO_MAIN.PATH_TO_CALENDER );
$template->assign_vars(array(
 'HEADING_TITLE'=>HEADING_TITLE,
 'TEXT_INFO_USER_NAME'=>TEXT_INFO_USER_NAME,
 'TEXT_INFO_USER_NAME1'=>tep_draw_input_field('TR_User_Name','','class="form-control form-control-sm"',false),
 'TEXT_INFO_PASSWORD'=>TEXT_INFO_PASSWORD,
 'TEXT_INFO_PASSWORD1'=>tep_draw_password_field('TR_Password','',false,'class="form-control form-control-sm"'),
  'LOGO'=>'<img class="logo-img" src="'.tep_href_link('img/'.DEFAULT_SITE_LOGO).'" width="150">',
 'form'=>tep_draw_form('login', PATH_TO_ADMIN.FILENAME_INDEX, 'action=check','post',' onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action','check'),
 'new_button'=>'<button type="submit" class="btn btn-primary btn-xl">Log In</button>',//tep_image_submit(PATH_TO_BUTTON.'button_confirm.gif', IMAGE_CONFIRM),
 'forgot_password'=>'<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_FORGOT_PASSWORD).'">Lost your password?</a>',
 'update_message'=>$messageStack->output()));
$template->pparse('login');
?>