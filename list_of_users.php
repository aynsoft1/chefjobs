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
include_once("include_files.php");
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_RECRUITER_LIST_OF_USERS);
$template->set_filenames(array('users' => 'list_of_users.htm','password' => 'list_of_users1.htm'));
$jscript_file=PATH_TO_LANGUAGE.$language."/jscript/".'list_of_users.js';
include_once(FILENAME_BODY);

if(!check_login("recruiter"))
{
 $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
 tep_redirect(FILENAME_RECRUITER_LOGIN);
}
/////////////////////////////////
if(isset($_SESSION['sess_recruiteruserid']))
{
 $messageStack->add_session(ACCESS_DENIED, 'error');
 tep_redirect(FILENAME_RECRUITER_CONTROL_PANEL);
}
////////////////////////////////
$edit=false;
if(isset($_GET['userID']))
{
 $user_id=(int)tep_db_prepare_input($_GET['userID']);
 $whereClause="id='".tep_db_input($user_id)."' and recruiter_id='".$_SESSION['sess_recruiterid']."'";
 if($row=getAnyTableWhereData(RECRUITER_USERS_TABLE,$whereClause,'name,email_address'))
 {
  $full_name=$row['name'];
  $email_address=$row['email_address'];
  $confirm_email_address=$email_address;
  $edit=true;
 }
 else //hacking attempt
 {
  $messageStack->add_session(MESSAGE_ERROR_USER, 'error');
  tep_redirect(FILENAME_RECRUITER_LIST_OF_USERS);
 }
}
//////////////////
$password_data="";
$action = (isset($_POST['action']) ? $_POST['action'] : '');
$action1 = (isset($_GET['action1']) ? $_GET['action1'] : '');
if(tep_not_null($action1))
{
 switch($action1)
 {
  case 'user_active':
  case 'user_inactive':
   tep_db_query("update ".RECRUITER_USERS_TABLE." set status='".($action1=='user_active'?'Yes':'No')."' where id='".tep_db_input($user_id)."'");
   $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
   tep_redirect(tep_href_link(FILENAME_RECRUITER_LIST_OF_USERS,tep_get_all_get_params(array('action1','userID'))));
 }
}

// add/edit
if(tep_not_null($action))
{
 switch($action)
 {
  case 'check':
   $old_password=tep_db_prepare_input($_POST['TR_old_password']);
   $password=tep_db_prepare_input($_POST['TR_new_password']);
   $whereClause="id='".$user_id."' and recruiter_id='".$_SESSION['sess_recruiterid']."'";
   $fields='password';
   if($row=getAnyTableWhereData(RECRUITER_USERS_TABLE,$whereClause,$fields))
   {
    if(!tep_validate_password($old_password, $row['password']))
    {
     $messageStack->add(SORRY_OLD_PASSWORD_MATCH, 'error');
    }
    else
    {
     $t_password=tep_encrypt_password($password);
     $sql_data_array = array('password' => $t_password);
     tep_db_perform(RECRUITER_USERS_TABLE, $sql_data_array, 'update', "id = '" . $user_id . "'");
     //$sql_data_array_cal['cal_passwd']=$t_password;
     //$row_cal=getAnyTableWhereData(RECRUITER_USERS_TABLE,"id='".$user_id."' and recruiter_id='".$_SESSION['sess_recruiterid']."'","email_address");
     //tep_db_perform('webcal_user', $sql_data_array_cal, 'update', "cal_login = '" . tep_db_input($row_cal['email_address']) . "'");
     $messageStack->add_session(SUCCESS_PASSWORD_CHANGE, 'success');
     tep_redirect(FILENAME_RECRUITER_LIST_OF_USERS);
    }
   }
   break;
  case 'new':
  case 'update':
   $full_name=tep_db_prepare_input($_POST['TR_full_name']);
   $email_address=tep_db_prepare_input($_POST['TREF_email_address']);
   $confirm_email_address=tep_db_prepare_input($_POST['TREF_confirm_email_address']);
   $password=tep_db_prepare_input($_POST['TR_password']);
   $confirm_password=tep_db_prepare_input($_POST['TR_confirm_password']);
   $error=false;

   //Check
   if($row=getAnyTableWhereData(JOBSEEKER_LOGIN_TABLE,"jobseeker_email_address='".tep_db_input($email_address)."'","jobseeker_id"))
   {
    $error = true;
    $messageStack->add(EMAIL_ADDRESS_ERROR,'user_account');
   }
   else if($row=getAnyTableWhereData(ADMIN_TABLE,"admin_email_address='".tep_db_input($email_address)."'","admin_id"))
   {
    $error = true;
    $messageStack->add(EMAIL_ADDRESS_ERROR,'user_account');
   }
   elseif($row=getAnyTableWhereData(RECRUITER_LOGIN_TABLE,"recruiter_email_address='".tep_db_input($email_address)."'","recruiter_id"))
   {
    $error = true;
    $messageStack->add(EMAIL_ADDRESS_ERROR,'user_account');
   }
   else
   {
    if($edit)
    {
     if($row=getAnyTableWhereData(RECRUITER_USERS_TABLE,"email_address='".tep_db_input($email_address)."' and recruiter_id='".$_SESSION['sess_recruiterid']."' and id!='".tep_db_input($user_id)."'","id"))
     {
      $error = true;
      $messageStack->add(EMAIL_ADDRESS_ERROR,'user_account');
     }
    }
    else
    {
     if($row=getAnyTableWhereData(RECRUITER_USERS_TABLE,"email_address='".tep_db_input($email_address)."' and recruiter_id='".$_SESSION['sess_recruiterid']."'","id"))
     {
      $error = true;
      $messageStack->add(EMAIL_ADDRESS_ERROR,'user_account');
     }
    }
   }
   if(!$error)
   {
    $error_email=false;
    if(tep_validate_email($email_address) == false)
    {
     $error_email=true;
     $error = true;
     $messageStack->add(EMAIL_ADDRESS_INVALID_ERROR,'user_account');
    }
    if(tep_validate_email($confirm_email_address) == false)
    {
     $error_email=true;
     $error = true;
     $messageStack->add(CONFIRM_EMAIL_ADDRESS_INVALID_ERROR,'user_account');
    }
    if(!$error_email)
    {
     if($email_address!=$confirm_email_address)
     {
      $error = true;
      $messageStack->add(EMAIL_ADDRESS_MATCH_ERROR,'user_account');
     }
    }
    //// password check
    if(!$edit)
    {
     $error_password=false;
     if (strlen($password) < MIN_PASSWORD_LENGTH)
     {
      $error_password=true;
      $error = true;
      $messageStack->add(MIN_PASSWORD_ERROR,'user_account');
     }
     if (strlen($password) < MIN_PASSWORD_LENGTH)
     {
      $error_password=true;
      $error = true;
      $messageStack->add(MIN_CONFIRM_PASSWORD_ERROR,'user_account');
     }
     if(!$error_password)
     {
      if($password!=$confirm_password)
      {
       $error = true;
       $messageStack->add(PASSWORD_MATCH_ERROR,'user_account');
      }
     }
    }
   }
   if(!$error)
   {
    $sql_data_array=array('recruiter_id'=>$_SESSION['sess_recruiterid'],
                          'name'=>$full_name,
                          'email_address'=>$email_address
                          );
    /*
    $sql_data_array_cal=array('cal_login'=>$email_address,
                              'cal_firstname'=>$full_name,
                              'cal_lastname'=>'',
                              'cal_is_admin'=>'N',
                              'cal_email'=>$email_address,
                              );
    */
    if($edit)
    {
     $sql_data_array['updated']='now()';
     //$row_cal=getAnyTableWhereData(RECRUITER_USERS_TABLE,"recruiter_id='".$_SESSION['sess_recruiterid']."'","email_address");
     tep_db_perform(RECRUITER_USERS_TABLE, $sql_data_array, 'update', "id='".$user_id."' and recruiter_id = '" . $_SESSION['sess_recruiterid'] . "'");
     //tep_db_perform('webcal_user', $sql_data_array_cal, 'update', "cal_login = '" . tep_db_input($row_cal['email_address']) . "'");
 	   $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
    }
    else
    {
     $sql_data_array['inserted']='now()';
     $t_password=tep_encrypt_password($password);
     $sql_data_array['password']=$t_password;
     tep_db_perform(RECRUITER_USERS_TABLE, $sql_data_array);
     //$sql_data_array_cal['cal_passwd']=$t_password;
     //tep_db_perform('webcal_user', $sql_data_array_cal);
 	   $messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');
    }
    tep_redirect(tep_href_link(FILENAME_RECRUITER_LIST_OF_USERS));
   }
 }
}

if(!$edit)
{
 $password_data='
                  <div class="form-group row">
                  <label for="" class="col-sm-3 col-form-label">
                  '.INFO_TEXT_PASSWORD.'
                  </label>
                  <div class="col-sm-9">
                  '.tep_draw_password_field('TR_password', '',false,'class="form-control required"').'
                  </div>
                  </div>
                  <div class="form-group row">
                  <label for="" class="col-sm-3 col-form-label">
                  '.INFO_TEXT_CONFIRM_PASSWORD.'
                  </label>
                  <div class="col-sm-9">
                  '.tep_draw_password_field('TR_confirm_password', '',false,'class="form-control required"').'
                  </div>
                  </div>
                ';
 $user_form=tep_draw_form('user', FILENAME_RECRUITER_LIST_OF_USERS, '', 'post', 'onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action','new');
 $user_button=tep_draw_submit_button_field('',''.ADD_USER.'','class="btn btn-primary"');
}
else
{
 $user_form=tep_draw_form('user', FILENAME_RECRUITER_LIST_OF_USERS, tep_get_all_get_params(array('page')), 'post', 'onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action','update');
 $user_button=tep_draw_submit_button_field('','Update','class="btn btn-primary"');
}

///////////////////////
// $db_user_query_raw = "select id, name, email_address, status, inserted from " . RECRUITER_USERS_TABLE . " where recruiter_id='".$_SESSION['sess_recruiterid']."' order by name";

$db_user_query_raw = "select ru.id, ru.name, ru.email_address, ru.status, ru.inserted, count(j.job_id) as total_jobs 
                        from " . RECRUITER_USERS_TABLE . " as ru 
                        left join " . JOB_TABLE . " as j on ru.id = j.recruiter_user_id AND j.deleted IS NULL
                        WHERE ru.recruiter_id = '".$_SESSION['sess_recruiterid']."' 
                        GROUP BY ru.id, ru.name, ru.email_address, ru.status, ru.inserted 
                        ORDER BY ru.name";

// echo $db_user_query_raw;exit;

$db_user_query = tep_db_query($db_user_query_raw);
$db_user_num_row = tep_db_num_rows($db_user_query);
$db_user_split = new splitPageResults($_GET['page'], 20, $db_user_query_raw, $db_user_query_numrows);
if($db_user_num_row > 0)
{
 $alternate=1;
 while ($user = tep_db_fetch_array($db_user_query))
 {
$ide=$user['id'];
  $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
  if ($user['status'] == 'Yes')
  {
   $status='<a href="' . tep_href_link(FILENAME_RECRUITER_LIST_OF_USERS, tep_get_all_get_params(array('userID'))).'&userID=' . $user['id'] . '&action1=user_inactive' . '">' . tep_image(PATH_TO_IMAGE.'icon_status_red_light.gif', STATUS_USER_INACTIVATE, 30, 20) . '</a>' . tep_image(PATH_TO_IMAGE.'icon_status_green.gif', STATUS_USER_ACTIVE, 30, 20);
  }
  else
  {
   $status=tep_image(PATH_TO_IMAGE.'icon_status_red.gif', STATUS_USER_INACTIVE, 30, 20) . '<a href="' . tep_href_link(FILENAME_RECRUITER_LIST_OF_USERS, tep_get_all_get_params(array('action1','userID'))).'&userID=' . $user['id'] . '&action1=user_active' . '">' . tep_image(PATH_TO_IMAGE.'icon_status_green_light.gif', STATUS_USER_ACTIVATE, 30, 20) . '</a>';
  }
  $template->assign_block_vars('users', array( 'row_selected' => $row_selected,
   'name' =>"<a href='".tep_href_link(FILENAME_RECRUITER_LIST_OF_USERS,'userID='.$user['id'])."'>". tep_db_output($user['name'])."</a>",
   'email_address' => tep_db_output($user['email_address']),
   'no_of_jobs' => tep_db_output($user['total_jobs']),
   'inserted' => tep_date_short(tep_db_output($user['inserted'])),
	'delete'=>"<a href='".tep_href_link(FILENAME_RECRUITER_LIST_OF_USERS,'page='.$_GET['page'].'&userID='.$user['id'])."&action1=delete'>Delete</a>",
   'status' => $status,
   'change_password' =>"<a href='".tep_href_link(FILENAME_RECRUITER_LIST_OF_USERS,'page='.$_GET['page'].'&userID='.$user['id'])."&action1=change_password'>".INFO_CHANGE_PASSWORD."</a> <br> <a href='".tep_href_link(FILENAME_RECRUITER_LIST_OF_USERS,'page='.$_GET['page'].'&userID='.$user['id'])."&action1=delete'>".INFO_DELETE_USER."</a>",
   ));
  $alternate++;
 }
}
//////////////////////
if($messageStack->size('user_account') > 0)
 $update_message=$messageStack->output('user_account');
else
 $update_message=$messageStack->output();

$template->assign_vars(array(
 'HEADING_TITLE'=>HEADING_TITLE,
 'HEADING_TITLE1'=>HEADING_TITLE1,
//'TABLE_HEADING_DELETE'=>TABLE_HEADING_DELETE,
 'TABLE_HEADING_NAME'=>TABLE_HEADING_NAME,
 'TABLE_HEADING_EMAIL_ADDRESS'=>TABLE_HEADING_EMAIL_ADDRESS,
 'TABLE_HEADING_INSERTED'=>TABLE_HEADING_INSERTED,
 'TABLE_HEADING_NUMBER_OF_JOBS'=>TABLE_HEADING_NUMBER_OF_JOBS,
 'TABLE_HEADING_STATUS'=>TABLE_HEADING_STATUS,
 'TABLE_HEADING_CHANGE_PASSWORD'=>TABLE_HEADING_CHANGE_PASSWORD,
 'INFO_TEXT_JSCRIPT_FILE'  =>$jscript_file,
 'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
 'RIGHT_BOX_WIDTH'=>RIGHT_BOX_WIDTH1,
 'LEFT_HTML'=>LEFT_HTML,
 'RIGHT_HTML'=>RIGHT_HTML,
 'update_message'=>$update_message));
if($action1=='change_password')
{
 $template->assign_vars(array(
 'INFO_TEXT_OLD_PASSWORD'=>INFO_TEXT_OLD_PASSWORD,
 'INFO_TEXT_OLD_PASSWORD1'=>tep_draw_password_field('TR_old_password', '',true, 'class="form-control"'),
 'INFO_TEXT_NEW_PASSWORD'=>INFO_TEXT_NEW_PASSWORD,
 'INFO_TEXT_NEW_PASSWORD1'=>tep_draw_password_field('TR_new_password', '',true, 'class="form-control"'),
 'INFO_TEXT_CONFIRM_PASSWORD'=>INFO_TEXT_CONFIRM_PASSWORD,
 'INFO_TEXT_CONFIRM_PASSWORD1'=>tep_draw_password_field('TR_confirm_password', '',true, 'class="form-control"'),
 'button'=>tep_draw_submit_button_field('','Confirm','class="btn btn-primary"'),//tep_image_submit(PATH_TO_BUTTON.'button_confirm.gif', IMAGE_CONFIRM),
 'form'=>tep_draw_form('change_password', FILENAME_RECRUITER_LIST_OF_USERS,tep_get_all_get_params(),'post', 'onsubmit="return validate_change_password(this)"').tep_draw_hidden_field('action','check'),
 ));
 $template->pparse('password');
}
elseif($action1=='delete')
{
 tep_db_query("delete from ".RECRUITER_USERS_TABLE." where  id='".$ide."'");
     $messageStack->add_session(MESSAGE_SUCCESS_DELETED,'success');
     tep_redirect(FILENAME_RECRUITER_LIST_OF_USERS);
}
else
{
 $template->assign_vars(array(
  'INFO_TEXT_FULL_NAME'=>INFO_TEXT_FULL_NAME,
  'HEADING_TITLE1'     =>HEADING_TITLE1,
  'INFO_TEXT_FULL_NAME1'=>tep_draw_input_field('TR_full_name',$full_name,'size="40" class="form-control required"',false),
  'INFO_TEXT_EMAIL_ADDRESS'=>INFO_TEXT_EMAIL_ADDRESS,
  'INFO_TEXT_EMAIL_ADDRESS1'=>tep_draw_input_field('TREF_email_address', $email_address,'size="40" class="form-control required"',false),
  'INFO_TEXT_CONFIRM_EMAIL_ADDRESS'=>INFO_TEXT_CONFIRM_EMAIL_ADDRESS,
  'INFO_TEXT_CONFIRM_EMAIL_ADDRESS1'=>tep_draw_input_field('TREF_confirm_email_address', $confirm_email_address,'size="40" class="form-control required"',false),
  'password_data'=>$password_data,
  'INFO_TEXT_JSCRIPT_FILE'  =>$jscript_file,
  'user_form'=>$user_form,
  'user_button'=>$user_button
  ));
 $template->pparse('users');
}
?>