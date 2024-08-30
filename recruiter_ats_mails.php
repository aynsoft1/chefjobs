<?
/*
***********************************************************
**********# Name          : Shamhu Prasad Patnaik   #**********
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
***********************************************************
*/
include_once("include_files.php");
ini_set('max_execution_time','0');
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_RECRUITER_ATS_MAILS);
$template->set_filenames(array('email'  =>'recruiter_ats_mails.htm','email_message'  =>'recruiter_ats_mails1.htm','mail_reply'  =>'recruiter_ats_mails_reply.htm'));
include_once(FILENAME_BODY);
if(!check_login("recruiter"))
{
 $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
 tep_redirect(FILENAME_RECRUITER_LOGIN);
}
$action         = (isset($_POST['action1']) ? $_POST['action1'] : '');
//print_r($_POST);die();

if(tep_not_null($action))
{
 switch($action)
 {
  case 'delete':
    $mail_id=$_POST['mail_no'];
    $lower=(int)tep_db_prepare_input($_POST['lower']);
    $higher=(int)tep_db_prepare_input($_POST['higher']);
    $mail_status=tep_db_prepare_input($_POST['mail_status']);
    $page_string='';
    if($lower >0)
    $page_string='lower='.$lower;
    if($higher >0)
     $page_string.=(($page_string=='')?'':'&').'higher='.$higher;
    if($mail_status=='inactive')
     $page_string.=(($page_string=='')?'':'&').'mail_status=inactive';
    for($i=0;$i<count($mail_id);$i++)
    {
    if($row_check=getAnyTableWhereData(APPLICANT_INTERACTION_TABLE." as ai  left outer join ".APPLICATION_TABLE . " as a on (a.id=ai.application_id) left outer join ".JOB_TABLE . " as jb on (a.job_id=jb.job_id)  "," jb.recruiter_id ='".$_SESSION['sess_recruiterid']."' and  ai.id ='".$mail_id[$i]."'","ai.id,ai.sender_user"))
 	{
	  if($row_check['sender_user']=='recruiter')
	  tep_db_query("update ".APPLICANT_INTERACTION_TABLE." set sender_mail_status='inactive' where id='".$mail_id[$i]."'");
      else
	  tep_db_query("update ".APPLICANT_INTERACTION_TABLE." set receiver_mail_status='inactive' where id='".$mail_id[$i]."'");
	 }
    }
    $messageStack->add_session(MESSAGE_SUCCESS_DELETED,'success');
    tep_redirect(tep_href_link(FILENAME_RECRUITER_ATS_MAILS,$page_string. tep_get_all_get_params(array('lower','higher'))));
    break;
  case 'complete_delete':
    $mail_id=$_POST['mail_no'];
    $lower=(int)tep_db_prepare_input($_POST['lower']);
    $higher=(int)tep_db_prepare_input($_POST['higher']);
    $mail_status=tep_db_prepare_input($_POST['mail_status']);
    $page_string='';
    if($lower >0)
    $page_string='lower='.$lower;
    if($higher >0)
     $page_string.=(($page_string=='')?'':'&').'higher='.$higher;
    if($mail_status=='inactive')
     $page_string.=(($page_string=='')?'':'&').'mail_status=inactive';
    for($i=0;$i<count($mail_id);$i++)
    {
     if($row_check=getAnyTableWhereData(APPLICANT_INTERACTION_TABLE." as ai  left outer join ".APPLICATION_TABLE . " as a on (a.id=ai.application_id) left outer join ".JOB_TABLE . " as jb on (a.job_id=jb.job_id)  "," jb.recruiter_id ='".$_SESSION['sess_recruiterid']."' and  ai.id ='".$mail_id[$i]."'","ai.id,ai.sender_user,ai.receiver_mail_status,ai.sender_mail_status,ai.attachment_file"))
  	 {
	  if($row_check['sender_mail_status']=='deleted'  || $row_check['receiver_mail_status']=='deleted'  )
	  {
	   if(tep_not_null($row_check['attachment_file']))
	   {
	     $file_directory_name=get_file_directory($row_check['attachment_file']);
         if(is_file(PATH_TO_MAIN_PHYSICAL_RECRUITER_EMAIL_ATTACHMENT.$file_directory_name.'/'.$row_check['attachment_file']))
         {
          @unlink(PATH_TO_MAIN_PHYSICAL_RECRUITER_EMAIL_ATTACHMENT.$file_directory_name.'/'.$row_check['attachment_file']);
         }
       }
	   tep_db_query("delete from ".APPLICANT_INTERACTION_TABLE." where id='".$row_check['id']."'");
	  }
	  elseif($row_check['sender_user']=='recruiter')
      tep_db_query("update ".APPLICANT_INTERACTION_TABLE." set sender_mail_status='deleted' where id='".$mail_id[$i]."'");
	  else
      tep_db_query("update ".APPLICANT_INTERACTION_TABLE." set receiver_mail_status='deleted' where id='".$mail_id[$i]."'");
	 }
    }
    $messageStack->add_session(MESSAGE_SUCCESS_RESTORE,'success');
    tep_redirect(tep_href_link(FILENAME_RECRUITER_ATS_MAILS,$page_string. tep_get_all_get_params(array('lower','higher'))));
    break;
  case 'restore':
    $mail_id=$_POST['mail_no'];
    $lower=(int)tep_db_prepare_input($_POST['lower']);
    $higher=(int)tep_db_prepare_input($_POST['higher']);
    $mail_status=tep_db_prepare_input($_POST['mail_status']);
    $page_string='';
    if($lower >0)
    $page_string='lower='.$lower;
    if($higher >0)
     $page_string.=(($page_string=='')?'':'&').'higher='.$higher;
    if($mail_status=='inactive')
     $page_string.=(($page_string=='')?'':'&').'mail_status=inactive';
    for($i=0;$i<count($mail_id);$i++)
    {
     if($row_check=getAnyTableWhereData(APPLICANT_INTERACTION_TABLE." as ai  left outer join ".APPLICATION_TABLE . " as a on (a.id=ai.application_id) left outer join ".JOB_TABLE . " as jb on (a.job_id=jb.job_id)  "," jb.recruiter_id ='".$_SESSION['sess_recruiterid']."' and  ai.id ='".$mail_id[$i]."'","ai.id,ai.sender_user,ai.receiver_mail_status,ai.sender_mail_status,ai.attachment_file"))
 	 {
	  if($row_check['sender_user']=='recruiter')
       tep_db_query("update ".APPLICANT_INTERACTION_TABLE." set sender_mail_status='active' where id='".$mail_id[$i]."'");
	  else
       tep_db_query("update ".APPLICANT_INTERACTION_TABLE." set receiver_mail_status='active' where id='".$mail_id[$i]."'");
	 }
    }
    $messageStack->add_session(MESSAGE_SUCCESS_DELETED,'success');
    tep_redirect(tep_href_link(FILENAME_RECRUITER_ATS_MAILS,$page_string. tep_get_all_get_params(array('lower','higher'))));
    break;
  case 'marked':
    $mail_id=$_POST['mail_no'];
    $lower=(int)tep_db_prepare_input($_POST['lower']);
    $higher=(int)tep_db_prepare_input($_POST['higher']);
    $mail_status=tep_db_prepare_input($_POST['mail_status']);
    $page_string='';
    if($lower >0)
    $page_string='lower='.$lower;
    if($higher >0)
     $page_string.=(($page_string=='')?'':'&').'higher='.$higher;
    if($mail_status=='inactive')
     $page_string.=(($page_string=='')?'':'&').'mail_status=inactive';
    for($i=0;$i<count($mail_id);$i++)
    {
      if($row_check=getAnyTableWhereData(APPLICANT_INTERACTION_TABLE." as ai  left outer join ".APPLICATION_TABLE . " as a on (a.id=ai.application_id) left outer join ".JOB_TABLE . " as jb on (a.job_id=jb.job_id)  "," jb.recruiter_id ='".$_SESSION['sess_recruiterid']."' and  ai.id ='".$mail_id[$i]."'","ai.id"))
      tep_db_query("update ".APPLICANT_INTERACTION_TABLE." set recruiter_mark='Yes' where id='".$mail_id[$i]."'");
    }
    $messageStack->add_session(MESSAGE_SUCCESS_MARKED,'success');
    tep_redirect(tep_href_link(FILENAME_RECRUITER_ATS_MAILS,$page_string. tep_get_all_get_params(array('lower','higher'))));
    break;
  case 'unmarked':
    $mail_id=$_POST['mail_no'];
    $lower=(int)tep_db_prepare_input($_POST['lower']);
    $higher=(int)tep_db_prepare_input($_POST['higher']);
    $mail_status=tep_db_prepare_input($_POST['mail_status']);
    $page_string='';
    if($lower >0)
    $page_string='lower='.$lower;
    if($higher >0)
     $page_string.=(($page_string=='')?'':'&').'higher='.$higher;
    if($mail_status=='inactive')
     $page_string.=(($page_string=='')?'':'&').'mail_status=inactive';
    for($i=0;$i<count($mail_id);$i++)
    {
      if($row_check=getAnyTableWhereData(APPLICANT_INTERACTION_TABLE." as ai  left outer join ".APPLICATION_TABLE . " as a on (a.id=ai.application_id) left outer join ".JOB_TABLE . " as jb on (a.job_id=jb.job_id)  "," jb.recruiter_id ='".$_SESSION['sess_recruiterid']."' and  ai.id ='".$mail_id[$i]."'","ai.id"))
      tep_db_query("update ".APPLICANT_INTERACTION_TABLE." set recruiter_mark='No' where id='".$mail_id[$i]."'");
    }
    $messageStack->add_session(MESSAGE_SUCCESS_UNMARKED,'success');
    tep_redirect(tep_href_link(FILENAME_RECRUITER_ATS_MAILS,$page_string. tep_get_all_get_params(array('lower','higher'))));
    break;
 case 'send_mail':
    $TR_subject  = tep_db_prepare_input($_POST['TR_subject']);
    $email_text  = stripslashes($_POST['message1']);
	$query_string2= tep_db_prepare_input($_GET['query_string2']);
    $mail_id = check_data($query_string2,"=+=","mail","mail_id");
    if($row_check=getAnyTableWhereData(APPLICANT_INTERACTION_TABLE." as ai  left outer join ".APPLICATION_TABLE . " as a on (a.id=ai.application_id) left outer join ".JOB_TABLE . " as jb on (a.job_id=jb.job_id) left outer join ".JOBSEEKER_LOGIN_TABLE . " as jl on (a.jobseeker_id=jl.jobseeker_id) left outer join ".RECRUITER_TABLE . " as r on (jb.recruiter_id=r.recruiter_id)  "," jb.recruiter_id ='".$_SESSION['sess_recruiterid']."' and  ai.id ='".$mail_id."'","ai.sender_id,ai.application_id,jl.jobseeker_email_address,r.recruiter_company_name"))
 	{
	 $reciver_id     =  $row_check['sender_id'];
	 $reciver_email  =  $row_check['jobseeker_email_address'];
	 $company_name   =  $row_check['recruiter_company_name'];
	 $application_id =  $row_check['application_id'];
	 $sender_id      =  $_SESSION['sess_recruiterid'];
     if (strlen($TR_subject) <= 0)
     {
      $error = true;
      $messageStack->add(ENTRY_SUBJECT_ERROR,'error');
     }
     if (strlen($email_text) <= 0)
     {
      $error = true;
      $messageStack->add(ENTRY_MESSAGE_ERROR,'error');
     }
 	 $allow_file  =array('doc','docx','xls','xlsx','pdf','txt','jpg','gif','png','ppt','pptx');
     if(tep_not_null($_FILES['attachment']['name']) &&  !in_array(strtolower(substr($_FILES['attachment']['name'], strrpos($_FILES['attachment']['name'], '.')+1)),$allow_file))
	 {
      $error = true;
	  $messageStack->add(ERROR_FILETYPE_NOT_ALLOWED, 'error');
	 }
	 if(!$error)
	 {
 	  if(tep_not_null($_FILES['attachment']['name']))
	  {
	   if($obj_resume = new upload('attachment', PATH_TO_MAIN_PHYSICAL_TEMP,'644',$allow_file))
       {
        $attachment_file_name=tep_db_input($obj_resume->filename);
       }
       else
       {
        $error=true;
        $messageStack->add(ERROR_ATTACHMENT_FILE, 'error');
       }
	  }
      if(!$error)
	  {
	   $destination='';
	   if(tep_not_null($attachment_file_name))
	   {
	    if(is_file(PATH_TO_MAIN_PHYSICAL_TEMP.$attachment_file_name))
	    {
		 $file_directory=get_file_directory($attachment_file_name);
         if(check_directory(PATH_TO_RECRUITER_EMAIL_ATTACHMENT.$file_directory))
         {
          $target_file_name=PATH_TO_MAIN_PHYSICAL_RECRUITER_EMAIL_ATTACHMENT.$file_directory.'/'.$attachment_file_name;
          copy(PATH_TO_MAIN_PHYSICAL_TEMP.$attachment_file_name,$target_file_name);
          @unlink(PATH_TO_MAIN_PHYSICAL_TEMP.$attachment_file_name);
          chmod($target_file_name, 0644);
		  $destination=$target_file_name;
         }
	    }
	   }
	   /////////////////////
	        $sql_data_array=array('application_id'=>$application_id,
                            'subject'=>$TR_subject,
                            'sender_user'=>'recruiter',
                            'sender_id'=>$sender_id,
                            'message'=>$email_text,
                            'attachment_file'=>$attachment_file_name,
                            'inserted'=>'now()',
                           );
		   $text = strip_tags($email_text);
		   /*
		   if (SEND_EMAILS == 'true')
		   {
			$message = new email();
			if(tep_not_null($attachment_file_name))
			{
			  $file_directory=get_file_directory($attachment_file_name);
			  $destination=PATH_TO_MAIN_PHYSICAL_RECRUITER_EMAIL_ATTACHMENT.$file_directory.'/'.$_POST['attachment'];
			  $file_name = basename($destination);
			  $handle    = fopen($destination, "r");
			  $contents = fread($handle, filesize($destination));
			  fclose($handle);
	 		  $message->add_attachment($contents,substr($file_name,14));
			}
			if (EMAIL_USE_HTML == 'true')
			{
			 $message->add_html($email_text);
			}
			else
			{
			 $message->add_text($text);
			}
			// Send message
			 $message->build_message();
			 $message->send('', $reciver_email,tep_db_output($company_name),tep_db_output($company_name), $TR_subject);
		   }*/
		tep_new_mail('',$reciver_email, $TR_subject, $email_text,$company_name, $company_name,$destination,substr($attachment_file_name,14)) ;
        tep_db_perform(APPLICANT_INTERACTION_TABLE,$sql_data_array);
        $messageStack->add_session(REPLY_SUCCESS_SENT, 'success');
        tep_redirect(FILENAME_RECRUITER_ATS_MAILS);
	   //////////////////////
	  }
	 }
	}
	break;
 }
}
if(isset($_GET['query_string1'])  && $_GET['query_string1']!='')
{
 $query_string1= tep_db_prepare_input($_GET['query_string1']);
 $mail_id = check_data($query_string1,"=+=","mail","mail_id");
}
elseif(isset($_GET['query_string2'])  && $_GET['query_string2']!='')
{
 $query_string2= tep_db_prepare_input($_GET['query_string2']);
 $mail_id = check_data($query_string2,"=+=","mail","mail_id");
 if(!$error)
 {
  if($row_check=getAnyTableWhereData(APPLICANT_INTERACTION_TABLE." as ai  left outer join ".APPLICATION_TABLE . " as a on (a.id=ai.application_id) left outer join ".JOB_TABLE . " as jb on (a.job_id=jb.job_id)  "," jb.recruiter_id ='".$_SESSION['sess_recruiterid']."' and  ai.id ='".$mail_id."'","ai.subject"))
  {
    $TR_subject = "Re : ".$row_check['subject'];
	$message1 ='';
  }
 }
}
if(tep_not_null($mail_id))
if(!$row_check=getAnyTableWhereData(APPLICANT_INTERACTION_TABLE." as ai  left outer join ".APPLICATION_TABLE . " as a on (a.id=ai.application_id) left outer join ".JOB_TABLE . " as jb on (a.job_id=jb.job_id)  "," jb.recruiter_id ='".$_SESSION['sess_recruiterid']."' and  ai.id ='".$mail_id."'","ai.subject,ai.attachment_file,ai.message,ai.sender_user"))
{
 $messageStack->add_session(ERROR_MAIL_NOT_EXIST, 'error');
 tep_redirect(FILENAME_RECRUITER_ATS_MAILS);
}
if(isset($_GET['query_string2'])  && $_GET['query_string2']!='')
{

  $template->assign_vars(array(
  'HEADING_TITLE'    => HEADING_TITLE,
  'INFO_TEXT_SUBJECT'=> INFO_TEXT_SUBJECT,
  'INFO_MAIL_ATTACHMENT'=>INFO_MAIL_ATTACHMENT,
  'INFO_MAIL_ATTACHMENT1'=>tep_draw_file_field('attachment', false),
  'INFO_TEXT_SUBJECT1'=>tep_draw_input_field('TR_subject', $TR_subject, 'class="form-control"', true ),
  'INFO_TEXT_MESSAGE' => INFO_TEXT_MESSAGE,
  'INFO_TEXT_MESSAGE1'=>tep_draw_textarea_field('message1', 'soft', '75%', '10', $email_text, 'class="form-control"', true, true),
  'reply_form'        => tep_draw_form('reply_email', FILENAME_RECRUITER_ATS_MAILS, 'query_string2='.$query_string2, 'post', 'onsubmit="return ValidateForm(this)" enctype="multipart/form-data"').tep_draw_hidden_field('action1','send_mail'),
  'INFO_TEXT_BUTTON'  => '<a href="'.tep_href_link(FILENAME_RECRUITER_ATS_MAILS,"query_string1=".$query_string2).'">'.tep_draw_submit_button_field('back', IMAGE_BACK,'class="btn btn-outline-secondary"').'</a>&nbsp;&nbsp;'.tep_draw_submit_button_field('','Send','class="btn btn-primary"'),
  'RIGHT_BOX_WIDTH'   => RIGHT_BOX_WIDTH1,

'JOB_SEARCH_LEFT'   => JOB_SEARCH_LEFT,
 'LEFT_HTML'=>LEFT_HTML,
  'RIGHT_HTML'        => RIGHT_HTML,
  'update_message'=>$messageStack->output()));
 $template->pparse('mail_reply');


}
elseif($mail_id>0)
{
 $file_directory=get_file_directory($row_check['attachment_file']);
 if($row_check['attachment_file']!='' && is_file(PATH_TO_MAIN_PHYSICAL_RECRUITER_EMAIL_ATTACHMENT.$file_directory.'/'.$row_check['attachment_file']))
 $attachment_file_name=$row_check['attachment_file'];
 $query_string5=encode_string("mail_attachment@#^#@".$attachment_file_name."@#^#@attachment");
 if($row_check['sender_user']=='jobseeker')
 tep_db_query("update ".APPLICANT_INTERACTION_TABLE." set user_see='Yes' where id='".$mail_id."'");
  ///print_r($row_check);
  $template->assign_vars(array(
  'HEADING_TITLE'    => HEADING_TITLE,
  'INFO_TEXT_SUBJECT'=> INFO_TEXT_SUBJECT,
  'INFO_TEXT_SUBJECT1'=>tep_db_output($row_check['subject']).(($attachment_file_name!='')?'<span class="small"><a href="'.tep_href_link(FILENAME_ATTACHMENT_DOWNLOAD,"query_string1=".$query_string5).'"  title="'.tep_db_output(substr($attachment_file_name,14)).'">'.tep_image_button('img/attachment.gif',IMAGE_ATTACHMENT.' :'.tep_db_output(substr($attachment_file_name,14))).' :'.tep_db_output(substr($attachment_file_name,14)).'</a></span>':''),
  'INFO_TEXT_MESSAGE' => INFO_TEXT_MESSAGE,
  'INFO_TEXT_MESSAGE1'=>stripslashes($row_check['message']),
  //'INFO_TEXT_BACK'=>'<a href="#" onclick="javascript:history.back()"><button class="btn btn-primary" >Back</button>&nbsp;&nbsp;',
  'INFO_TEXT_BACK'=>'<a href="'.FILENAME_RECRUITER_ATS_MAILS.'"><button class="btn btn-outline-secondary me-2" ><i class="bi bi-arrow-left"></i></button>',
  'INFO_TEXT_REPLY'=>(($row_check['sender_user']=='recruiter')?'':'<a href="'.tep_href_link(FILENAME_RECRUITER_ATS_MAILS,'query_string2='.$query_string1).'" class="btn btn-primary">'.INFO_TEXT_REPLY.'</a>'),
  'RIGHT_BOX_WIDTH'   => RIGHT_BOX_WIDTH1,
  'LEFT_HTML'         => '',
   'LEFT_HTML'=>LEFT_HTML,
  'JOB_SEARCH_LEFT'   => JOB_SEARCH_LEFT,
  'RIGHT_HTML'        => RIGHT_HTML,
  'update_message'=>$messageStack->output()));
 $template->pparse('email_message');

}
else
{
 if(isset($_GET['view']) && $_GET['view']!='' && !isset($_POST['view']))
  $view=tep_db_prepare_input($_GET['view']);
 else
  $view=tep_db_prepare_input($_POST['view']);

 if(isset($_GET['mail_status']) && $_GET['mail_status']!='' && !isset($_POST['mail_status']))
  $mail_status=tep_db_prepare_input($_GET['mail_status']);
 else
  $mail_status=tep_db_prepare_input($_POST['mail_status']);

 if(isset($_GET['higher']) && $_GET['higher']!='' && !isset($_POST['higher']))
  $higher=(int)tep_db_prepare_input($_GET['higher']);
 else
  $higher=(int)tep_db_prepare_input($_POST['higher']);

 if(isset($_GET['lower']) && $_GET['lower']!='' && !isset($_POST['lower']))
  $lower=tep_db_prepare_input($_GET['lower']);
 else
  $lower=(int)tep_db_prepare_input($_POST['lower']);

 $field=tep_db_prepare_input($_POST['field']);
 $order=tep_db_prepare_input($_POST['order']);
 //print_r($_POST);
 $view_link="";
 if($mail_status=='inactive')
  $show_mail_status='Trash';
 elseif($mail_status=='send')
  $show_mail_status='Send';
 else
  $show_mail_status='Inbox';

 if($show_mail_status=='Inbox')
  $view_link1 =INFO_TEXT_VIEW_IN."<a class='text-blue mx-2' href='#' onclick='view_data(\"All\",\"Send\")'>".INFO_TEXT_VIEW_SEND."</a> | <a href='#' class='text-blue mx-2' onclick='view_data(\"All\",\"Inactive\")'>".INFO_TEXT_VIEW_TRASH."</a>  | <a class='text-blue ms-2' href='".tep_href_link(FILENAME_RECRUITER_APPLICANT_TRACKING)."'>Compose</a>";
 elseif($show_mail_status=='Send')
  $view_link1 =INFO_TEXT_VIEW_IN."<a class='text-blue' href='#' onclick='view_data(\"All\",\"Active\")'>".INFO_TEXT_VIEW_INBOX."</a> | <a href='#' class='text-blue mx-2' onclick='view_data(\"all\",\"Inactive\")'>".INFO_TEXT_VIEW_TRASH."</a>  | <a class='text-blue' href='".tep_href_link(FILENAME_RECRUITER_APPLICANT_TRACKING)."'>Compose</a>";
 else
  $view_link1 =INFO_TEXT_VIEW_IN. "<a class='text-blue' href='#' onclick='view_data(\"All\",\"Send\")'>".INFO_TEXT_VIEW_SEND."</a> | <a class='text-blue mx-2' href='#' onclick='view_data(\"All\",\"Active\")'>".INFO_TEXT_VIEW_INBOX."</a>  | <a class='text-blue' href='".tep_href_link(FILENAME_RECRUITER_APPLICANT_TRACKING)."'>Compose</a>";

 if($view=='unread')
 {
  $view_link =INFO_TEXT_VIEW_IN." ".$show_mail_status." : <a href='#' onclick='view_data(\"All\",\"".$mail_status."\")'>".INFO_TEXT_ALL."</a>";
  $view_link .=" |".INFO_TEXT_UNREAD;
  $view_link .=" | <a href='#' onclick='view_data(\"marked\",\"".$mail_status."\")'>".INFO_TEXT_MARKED."</a>";
 }
 else if($view=='marked')
 {
  if($show_mail_status=='Send')
  {
   $view_link =INFO_TEXT_VIEW_IN." ".$show_mail_status.": <a class='text-blue' href='#' onclick='view_data(\"All\",\"".$mail_status."\")'>".INFO_TEXT_SHOW_ALL."</a>";
  }
  else
  {
   $view_link =INFO_TEXT_VIEW_IN." ".$show_mail_status.": <a class='text-blue' href='#' onclick='view_data(\"All\",\"".$mail_status."\")'>".INFO_TEXT_ALL."</a>";
   $view_link .=" | <a class='text-blue mx-2' href='#' onclick='view_data(\"Unread\",\"".$mail_status."\")'>".INFO_TEXT_UNREAD."</a>";
   $view_link .=" | ".INFO_TEXT_MARKED;
  }
 }
 else
 {
  if($show_mail_status=='Send')
  {
   $view_link =INFO_TEXT_VIEW_IN." ".$show_mail_status." : ".INFO_TEXT_ALL;
   $view_link .=" | <a class='text-blue mx-2' href='#' onclick='view_data(\"Marked\",\"".$mail_status."\")'>".INFO_TEXT_MARKED."</a>";
  }
  else
  {
   $view_link =INFO_TEXT_VIEW_IN." ".$show_mail_status." : ".INFO_TEXT_ALL;
   $view_link .=" | <a class='text-blue mx-2' href='#' onclick='view_data(\"Unread\",\"".$mail_status."\")'>".INFO_TEXT_UNREAD."</a>";
   $view_link .=" | <a class='text-blue mx-2' href='#' onclick='view_data(\"Marked\",\"".$mail_status."\")'>".INFO_TEXT_MARKED."</a>";
  }
 }
 $table_names=APPLICANT_INTERACTION_TABLE." as ai left join ".APPLICATION_TABLE."  as a on (a.id=ai.application_id) left outer join ".JOBSEEKER_TABLE."  as j on (a.jobseeker_id=j.jobseeker_id) left join ".JOB_TABLE."  as jb on (a.job_id =jb.job_id) left join ".RECRUITER_TABLE."  as r on (jb.recruiter_id =r.recruiter_id) ";
 $whereClause=" jb.recruiter_id ='".$_SESSION['sess_recruiterid']."'";
 //print_r($_POST);
 if($mail_status=='active') //inbox
  $whereClause.=" and ai.receiver_mail_status='active' and sender_user='jobseeker' ";
 elseif($mail_status=='inactive')//Trash
  $whereClause.=" and ( ( sender_user='jobseeker' and ai.receiver_mail_status='inactive' )  or (ai.sender_mail_status='inactive' and sender_user='recruiter'))";
 elseif($mail_status=='send')//send box
  $whereClause.=" and ai.sender_mail_status='active' and sender_user='recruiter'";
 else
  $whereClause.=" and ai.receiver_mail_status='active' and sender_user='jobseeker'";

 if($view=='unread')
  $whereClause.=" and ai.user_see ='No'";
 elseif($view=='marked')
  $whereClause.=" and ai.recruiter_mark ='Yes'";

 $field_names="ai.id,a.application_id,ai.subject,ai.attachment_file,ai.inserted,ai.user_see,ai.recruiter_mark,if(j.jobseeker_privacy=2 || j.jobseeker_privacy=3,concat(j.jobseeker_first_name,' ',j.jobseeker_last_name),'*****') as jobseeker_name ,sender_user";
 $query1 = "select count(ai.id) as x1 from $table_names where $whereClause ";
 $result1=tep_db_query($query1);
 $tt_row=tep_db_fetch_array($result1);
 $x1=$tt_row['x1'];
 //echo $x1;
 //////////////////
 ///only for sorting starts

 include_once(PATH_TO_MAIN_PHYSICAL_CLASS.'sort_by_clause.php');
 $sort_array=array("r.recruiter_company_name",'ai.application_id','ai.inserted');
 $obj_sort_by_clause=new sort_by_clause($sort_array,'ai.inserted desc');
 $order_by_clause=$obj_sort_by_clause->return_value;
 //print_r($obj_sort_by_clause->return_sort_array['name']);
 //print_r($obj_sort_by_clause->return_sort_array['image']);
 $see_before_page_number_array=see_before_page_number1($sort_array,$field,'ai.inserted',$order,'desc',$lower,'0',$higher,'20');
 $lower=$see_before_page_number_array['lower'];
 $higher=$see_before_page_number_array['higher'];
 $field=$see_before_page_number_array['field'];
 $order=$see_before_page_number_array['order'];
 $hidden_fields.=tep_draw_hidden_field('sort',$sort);
 $hidden_fields.=tep_draw_hidden_field('view',$view);
 $hidden_fields.=tep_draw_hidden_field('mail_status',$mail_status);
 $hidden_fields.=tep_draw_hidden_field('action1','');
 $template->assign_vars(array('TABLE_HEADING_MAIL_SENDER'=>"<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][0]."','".$lower."');\"><u>".TABLE_HEADING_MAIL_SENDER.'</u>'.$obj_sort_by_clause->return_sort_array['image'][0]."</a>",
                              'TABLE_HEADING_APPLICATION_ID'=>"<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][1]."','".$lower."');\"><u>".TABLE_HEADING_APPLICATION_ID.'</u>'.$obj_sort_by_clause->return_sort_array['image'][1]."</a>",
                              'TABLE_HEADING_MAIL_INSERTED'=>"<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][2]."','".$lower."');\"><u>".TABLE_HEADING_MAIL_INSERTED.'</u>'.$obj_sort_by_clause->return_sort_array['image'][2]."</a>"));
 ///only for sorting ends

 $totalpage=ceil($x1/$higher);
 $query = "select $field_names from $table_names where $whereClause ORDER BY ". $order_by_clause ." limit $lower,$higher ";
 $result=tep_db_query($query);
 $x=tep_db_num_rows($result);
 ///////////////
 // $query= "select  ai.id,a.application_id,ai.subject,ai.attachment_file,ai.inserted,ai.email_address,ai.user_see from ".APPLICANT_INTERACTION_TABLE." as ai left join ".APPLICATION_TABLE."  as a on (a.id=ai.application_id) where a.jobseeker_id ='".$_SESSION['sess_jobseekerid']."' order by ai.inserted desc  ";
 // $query_result = tep_db_query($query);
 $pno= ceil($lower+$higher)/($higher);
 if($x > 0 && $x1 > 0)
 {
  $alternate=1;
  while ($jobseeker_mail = tep_db_fetch_array($result))
  {
   $attachment_file_name='';
   $file_directory=get_file_directory($jobseeker_mail['attachment_file']);
   if($jobseeker_mail['attachment_file']!='' && is_file(PATH_TO_MAIN_PHYSICAL_RECRUITER_EMAIL_ATTACHMENT.$file_directory.'/'.$jobseeker_mail['attachment_file']))
   $attachment_file_name=$jobseeker_mail['attachment_file'];
   $row_selected=' class="dataTableRow'.(($jobseeker_mail['user_see']=='No' && $show_mail_status!='Send')?'3':($alternate%2==1?'1':'2')).'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
   $query_string1=encode_string("mail=+=".$jobseeker_mail['id']."=+=mail_id");
  
   $template->assign_block_vars('jobseeker_mail',array( 'row_selected' => $row_selected,
   'check_box' => tep_draw_checkbox_field('mail_no[]',$jobseeker_mail['id']).(($jobseeker_mail['recruiter_mark']=='yes')?tep_image_button('image/mark.gif',"Marked"):""),
    'sender'        => (($jobseeker_mail['sender_user']=='recruiter')?'Me':"<a href='".tep_href_link(FILENAME_RECRUITER_ATS_MAILS,"query_string1=".$query_string1)."'>".tep_db_output($jobseeker_mail['jobseeker_name'])."</a>"),
    'application_id'=> "<a href='".tep_href_link(FILENAME_RECRUITER_LIST_OF_APPLICATIONS,"search_id=".$jobseeker_mail['application_id'])."' target='_blank'>".tep_db_output($jobseeker_mail['application_id'])."</a>",
    'subject'       => (($attachment_file_name!='')?' <span class="small">'.tep_image_button('img/attachment.gif',IMAGE_ATTACHMENT.' : '.tep_db_output(substr($attachment_file_name,14))).'</span>':'')." <a href='".tep_href_link(FILENAME_RECRUITER_ATS_MAILS,"query_string1=".$query_string1)."'>".tep_db_output($jobseeker_mail['subject'])."</a>",
    'inserted'      => ($jobseeker_mail['inserted']=='0000-00-00 00:00:00')?'-':tep_db_output(formate_date1($jobseeker_mail['inserted'])),
    ));
    $alternate++;
    $lower = $lower + 1;
  }
   $plural=($x1=="1")?INFO_TEXT_MAIL:INFO_TEXT_MAILS;
   $template->assign_vars(array('total'=>SITE_TITLE.' '.INFO_TEXT_HAS_FOUND." <b>$x1</b> ".$plural." ."));
  $check_link='<a href="#" onclick="checkall()">'.INFO_TEXT_CHECK_ALL.'</a> / <a href="#" onclick="uncheckall()">'.INFO_TEXT_UNCHECK_ALL.'</a>';
  if($show_mail_status=='Inbox')
  {
   $check_link1=INFO_TEXT_WITH_SELECTED.'
              <select name="select_action" class="form-select ms-2" onchange="select_action2();">
                 <option value="" selected="selected">'.INFO_TEXT_WITH_SELECTED.':</option>
                 <option value="marked" >'.INFO_TEXT_MARKED.'</option>
                 <option value="unmarked" >'.INFO_TEXT_CLEAR_MARK.'</option>
                 <option value="delete" >'.INFO_TEXT_DELETE.'</option>
              </select>';
  }
  elseif($show_mail_status=='Send')
  {
   $check_link1=INFO_TEXT_WITH_SELECTED.'
              <select name="select_action" class="form-select ms-2" onchange="select_action2();">
                 <option value="" selected="selected">'.INFO_TEXT_WITH_SELECTED.':</option>
                 <option value="marked" >'.INFO_TEXT_MARKED.'</option>
                 <option value="unmarked" >'.INFO_TEXT_CLEAR_MARK.'</option>
               </select>';
  }
  else
  {
   $check_link1=INFO_TEXT_WITH_SELECTED.'
              <select name="select_action" class="form-select ms-2" onchange="select_action2();">
                 <option value="" selected="selected">'.INFO_TEXT_WITH_SELECTED.':</option>
                 <option value="marked" >'.INFO_TEXT_MARKED.'</option>
                 <option value="unmarked" >'.INFO_TEXT_CLEAR_MARK.'</option>
                 <option value="restore" >'.INFO_TEXT_RESTORE.'</option>
                 <option value="complete_delete" >'.INFO_TEXT_DELETE.'</option>
              </select>';
  }
 }
 else
 {
    $template->assign_vars(array('total'=>SITE_TITLE.' '.INFO_TEXT_HAS_NOT_FOUND." .<br><br>&nbsp;&nbsp;&nbsp;"));
 }
 see_page_number();
 tep_db_free_result($result);
 tep_db_free_result($result1);
 $lower_value= ($_POST['lower']!='')?'document.page.lower.value='.$_POST['lower'].';':'';
 $higher_value= ($_POST['lower']!='')?'document.page.lower.value='.$_POST['lower'].';':'';
 $status_value= ($_POST['mail_status']!='')?'document.page.mail_status.value="'.$_POST['mail_status'].'";':'';

 if(!isset($_POST['lower']) &&  $_GET['lower'] >0 )
  $lower_value= ($_GET['lower']!='')?'document.page.lower.value='.(int)$_GET['lower'].';':'';
 if(!isset($_POST['higher']) &&  $_GET['higher'] >0 )
  $higher_value= ($_GET['higher']!='')?'document.page.higher.value='.(int)$_GET['higher'].';':'';


 $template->assign_vars(array(
  'hidden_fields' => $hidden_fields,
  'HEADING_TITLE'    => HEADING_TITLE,
  //'TABLE_HEADING_MAIL_SENDER'=>TABLE_HEADING_MAIL_SENDER,
  //'TABLE_HEADING_APPLICATION_ID'=>TABLE_HEADING_APPLICATION_ID,
  'TABLE_HEADING_MAIL_SUBJECT'=>TABLE_HEADING_MAIL_SUBJECT,
  //'TABLE_HEADING_MAIL_INSERTED'=>TABLE_HEADING_MAIL_INSERTED,
  'form'=>tep_draw_form('page', FILENAME_RECRUITER_ATS_MAILS,'','post', 'onsubmit="return ValidateForm(this)"'),
  'view_link'   => $view_link,
  'view_link1'  => $view_link1,
  'check_link'  => $check_link,
  'check_link1' => $check_link1,
  'lower_value' => $lower_value,
  'higher_value'=> $higher_value,
  'mail_status'=>  $status_value,
  'RIGHT_BOX_WIDTH'   => $RIGHT_BOX_WIDTH,
 'LEFT_HTML'=>LEFT_HTML,
		'JOB_SEARCH_LEFT'   => JOB_SEARCH_LEFT,
  'RIGHT_HTML'        => RIGHT_HTML,
  'update_message'=>$messageStack->output()));
 $template->pparse('email');
}
?>