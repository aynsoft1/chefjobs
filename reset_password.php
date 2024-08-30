<?
/*
***********************************************************
**********# Name          : Shambhu Prasad Patnaik  #******
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2017  #**********
***********************************************************
*/
include_once("include_files.php");
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_RESET_PASSWORD);
$template->set_filenames(array('reset_password' => 'reset_password.htm','reset_password1' => 'reset_password1.htm','change_password' => 'reset_password2.htm'));
include_once(FILENAME_BODY);
$jscript_file=PATH_TO_LANGUAGE.$language."/jscript/".'reset_password.js';

if(isset($_POST['action']))
$action    = tep_db_prepare_input($_POST['action']);
elseif(isset($_GET['action']))
$action    = tep_db_prepare_input($_GET['action']);
else
$action    = '';
$web=0;
if(isset($_GET['web']))
$web    = tep_db_prepare_input($_GET['web']);


if(isset($_POST['email_address']))
$email_address    = tep_db_prepare_input($_POST['email_address']);
elseif(isset($_GET['email_address']))
$email_address    = tep_db_prepare_input($_GET['email_address']);
else
$email_address    = '';

if(isset($_POST['TR_reset_code']))
$reset_code    = tep_db_prepare_input($_POST['TR_reset_code']);
elseif(isset($_GET['reset_code']))
$reset_code    = tep_db_prepare_input($_GET['reset_code']);
else
$reset_code    ='';
$error=false;
$is_verified =false;
if($action=='reset_password')
{
  if($email_address=='')
  {
   $messageStack->add_session(ERROR_INVALID_EMAIL_ADDRESS, 'error');
   tep_redirect(FILENAME_RESET_PASSWORD.'?action=msg');
  }
  if($reset_code=='')
  {
   $error=true;
   $messageStack->add(ERROR_INVALID_RESET_CODE, 'error');
  }
  if(tep_validate_email($email_address) == false)
  {
   $messageStack->add_session(ERROR_INVALID_EMAIL_ADDRESS, 'error');
   tep_redirect(FILENAME_RESET_PASSWORD.'?action=msg');
  } 
  if(!$error)
  {
	$is_verified =check_user_opt($email_address,$reset_code);
	if($is_verified)
	{
     
	}
	else
	{
      $messageStack->add(ERROR_INVALID_RESET_CODE, 'error');
	}
  }  
}
if($action=='msg')
{
 $template->assign_vars(array(
  'HEADING_TITLE'=>HEADING_TITLE,
  'HEADING_CONTENT'=>HEADING_CONTENT,
  'INFO_TEXT_OPT'  => INFO_TEXT_OPT, 
  'INFO_TEXT_OPT1'=>tep_draw_input_field('TR_reset_code',$reset_code, 'class="form-control"'),
  'buttons'=>tep_button_submit('btn btn-block btn-primary','Confirm'),
  'form'=>tep_draw_form('reset',FILENAME_RESET_PASSWORD,'web='.$web,'post','onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action','reset_password'),
  'INFO_TEXT_JSCRIPT_FILE'  => '<script src="'.$jscript_file.'"></script>',
  'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
  'RIGHT_BOX_WIDTH'=>RIGHT_BOX_WIDTH1,
  'LEFT_HTML'=>LEFT_HTML,
  'RIGHT_HTML'=>RIGHT_HTML,
  'update_message'=>$messageStack->output()));
 $template->pparse('reset_password1');
}
else
{
  if($email_address=='')
  {
   $messageStack->add_session(ERROR_INVALID_EMAIL_ADDRESS, 'error');
   tep_redirect(FILENAME_RESET_PASSWORD.'?action=msg');
  }

  if($is_verified)
  {
   if(isset($_POST['TR_new_password']))
    {
     $new_password    = tep_db_prepare_input($_POST['TR_new_password']);
     if(strlen($new_password)<5)
	 {
	  $error = true;
	  $messageStack->add(MIN_PASSWORD_ERROR, 'error');
	 }
	 if(strlen($new_password)>15)
	 {
      $error = true;
      $messageStack->add(MAX_PASSWORD_ERROR, 'error');
     }
     if(preg_match("#\s#",$new_password,$match))
	 {
      $error = true;
      $messageStack->add(SPACE_PASSWORD_ERROR, 'error');
     }
	 if(!$error)
	 {
      switch($is_verified)
	  {
		case 'jobseeker':
		 if($row=getAnyTableWhereData(JOBSEEKER_LOGIN_TABLE.' as jl  left outer join '.JOBSEEKER_TABLE.' as j on (j.jobseeker_id=jl.jobseeker_id)',"jl.jobseeker_email_address='".tep_db_input($email_address)."' and jl.jobseeker_status='Yes'",'jl.jobseeker_id,jl.jobseeker_email_address,j.jobseeker_first_name'))
		 {
	      $sql_data_array = array('jobseeker_password' => tep_encrypt_password($new_password));
		  tep_db_perform(JOBSEEKER_LOGIN_TABLE, $sql_data_array, 'update', "jobseeker_id = '" . $row['jobseeker_id'] . "'");

          password_acknowledgement_mail($row['jobseeker_first_name'],$row['jobseeker_email_address']);
	      $messageStack->add_session(PASS_CHANGE_SUCCESSFULLY, 'success');
		  tep_redirect(tep_href_link(FILENAME_JOBSEEKER_LOGIN));
		 }
		 break;
		case 'recruiter':
		 if($row=getAnyTableWhereData(RECRUITER_LOGIN_TABLE.' as rl  left outer join '.RECRUITER_TABLE.' as r on (r.recruiter_id=rl.recruiter_id)',"rl.recruiter_email_address='".tep_db_input($email_address)."' and rl.recruiter_status='Yes'",'rl.recruiter_id,rl.recruiter_email_address,concat(r.recruiter_first_name," ",r.recruiter_last_name) as full_name'))
		 {
	      $sql_data_array = array('recruiter_password' => tep_encrypt_password($new_password));
		  tep_db_perform(RECRUITER_LOGIN_TABLE, $sql_data_array, 'update', "recruiter_id = '" . $row['recruiter_id'] . "'");

          password_acknowledgement_mail($row['full_name'],$row['recruiter_email_address']);
	      $messageStack->add_session(PASS_CHANGE_SUCCESSFULLY, 'success');
		  tep_redirect(tep_href_link(FILENAME_RECRUITER_LOGIN));
		 }
		 elseif($row=getAnyTableWhereData(RECRUITER_USERS_TABLE,"email_address ='".tep_db_input($email_address)."' and status='Yes'",'id,name,email_address'))
		 {
	      $sql_data_array = array('password' => tep_encrypt_password($new_password));
		  tep_db_perform(RECRUITER_LOGIN_TABLE, $sql_data_array, 'update', "id = '" . $row['id'] . "'");
          password_acknowledgement_mail($row['name'],$row['email_address']);
	      $messageStack->add_session(PASS_CHANGE_SUCCESSFULLY, 'success');
		  tep_redirect(tep_href_link(FILENAME_RECRUITER_LOGIN));
		 }	
		 break;
		case 'admin':
		 if($row=getAnyTableWhereData(ADMIN_TABLE,"admin_email_address='".tep_db_input($email_address)."'",'admin_id,admin_name, admin_email_address'))
		 {
	      $sql_data_array = array('admin_password' => tep_encrypt_password($new_password));
		  tep_db_perform(ADMIN_TABLE, $sql_data_array, 'update', "admin_id = '" . $row['admin_id'] . "'");
          password_acknowledgement_mail($row['admin_name'],$row['admin_email_address']);
		  $messageStack->add_session(PASS_CHANGE_SUCCESSFULLY, 'success');
          tep_redirect(tep_href_link(PATH_TO_ADMIN));
		 }
		 break;
	  }
	 }
    }
     $template->assign_vars(array(
    'HEADING_TITLE'=>HEADING_TITLE,
    'HEADING_RESET_PASS'=>HEADING_RESET_PASS,
    'INFO_TEXT_NEW_PASSWORD'  => INFO_TEXT_NEW_PASSWORD, 
    'INFO_TEXT_NEW_PASSWORD1'=>tep_draw_password_field('TR_new_password','').tep_draw_hidden_field('TR_reset_code',$reset_code),
    'buttons'=>tep_button_submit('btn btn-block btn-primary','Confirm'),
    'form'=>tep_draw_form('reset',FILENAME_RESET_PASSWORD,'web='.$web.'&email_address='.($email_address),'post','onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action','reset_password'),
    'INFO_TEXT_JSCRIPT_FILE'  => '<script src="'.$jscript_file.'"></script>',
    'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
    'RIGHT_BOX_WIDTH'=>RIGHT_BOX_WIDTH1,
    'LEFT_HTML'=>LEFT_HTML,
    'RIGHT_HTML'=>RIGHT_HTML,
    'update_message'=>$messageStack->output()));
   $template->pparse('change_password');
  }
  else
  {
   $template->assign_vars(array(
    'HEADING_TITLE'=>HEADING_TITLE,
    'HEADING_CONTENT'=>HEADING_CONTENT,
    'INFO_TEXT_OPT'  => INFO_TEXT_OPT, 
    'INFO_TEXT_OPT1'=>tep_draw_input_field('TR_reset_code',$reset_code, 'class="form-control"'),
    'buttons'=>tep_button_submit('btn btn btn-block btn-primary','Confirm'),
    'form'=>tep_draw_form('reset',FILENAME_RESET_PASSWORD,'web='.$web.'&email_address='.($email_address),'post','onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action','reset_password'),
    'INFO_TEXT_JSCRIPT_FILE'  => '<script src="'.$jscript_file.'"></script>',
    'LEFT_BOX_WIDTH'=>LEFT_BOX_WIDTH1,
    'RIGHT_BOX_WIDTH'=>RIGHT_BOX_WIDTH1,
    'LEFT_HTML'=>LEFT_HTML,
    'RIGHT_HTML'=>RIGHT_HTML,
    'update_message'=>$messageStack->output()));
   $template->pparse('reset_password');
  }
}
?>