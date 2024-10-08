<?php
/*
***********************************************************
**********# Name          : Shamhu Prasad Patnaik   #**********
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
***********************************************************
*/
include_once("include_files.php");
ini_set('max_execution_time','0');
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_RECRUITER_MAILS);
$template->set_filenames(array('email'  =>'recruiter_mails.htm','email_message'  =>'recruiter_mails1.htm','send_mail' => 'recruiter_mail_reply.htm'));
include_once(FILENAME_BODY);
if(!check_login("recruiter"))
{
 $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
 tep_redirect(FILENAME_RECRUITER_LOGIN);
}
$action         = (isset($_POST['action1']) ? $_POST['action1'] : '');
//print_r($_POST);//die();
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
     if($row_check=getAnyTableWhereData(ADMIN_EMPLOYER_MAILS_TABLE." as em "," em.receiver_id ='".$_SESSION['sess_recruiterid']."' and  em.id ='".(int)$mail_id[$i]."'","em.id"))
      tep_db_query("update ".ADMIN_EMPLOYER_MAILS_TABLE." set receiver_mail_status='inactive' where id='".$mail_id[$i]."'");
    }
    $messageStack->add_session(MESSAGE_SUCCESS_DELETED,'success');
    tep_redirect(tep_href_link(FILENAME_RECRUITER_MAILS,$page_string. tep_get_all_get_params(array('lower','higher'))));
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
     if($row_check=getAnyTableWhereData(ADMIN_EMPLOYER_MAILS_TABLE." as em "," em.receiver_id ='".$_SESSION['sess_recruiterid']."' and  em.id ='".(int)$mail_id[$i]."'","em.id"))
      tep_db_query("update ".ADMIN_EMPLOYER_MAILS_TABLE." set receiver_mail_status='deleted' where id='".$mail_id[$i]."'");
    }
    $messageStack->add_session(MESSAGE_SUCCESS_RESTORE,'success');
    tep_redirect(tep_href_link(FILENAME_RECRUITER_MAILS,$page_string. tep_get_all_get_params(array('lower','higher'))));
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
     if($row_check=getAnyTableWhereData(ADMIN_EMPLOYER_MAILS_TABLE." as em "," em.receiver_id ='".$_SESSION['sess_recruiterid']."' and  em.id ='".(int)$mail_id[$i]."'","em.id"))
      tep_db_query("update ".ADMIN_EMPLOYER_MAILS_TABLE." set receiver_mail_status='active' where id='".$mail_id[$i]."'");
    }
    $messageStack->add_session(MESSAGE_SUCCESS_DELETED,'success');
    tep_redirect(tep_href_link(FILENAME_RECRUITER_MAILS,$page_string. tep_get_all_get_params(array('lower','higher'))));
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
     if($row_check=getAnyTableWhereData(ADMIN_EMPLOYER_MAILS_TABLE." as em "," em.receiver_id ='".$_SESSION['sess_recruiterid']."' and  em.id ='".(int)$mail_id[$i]."'","em.id"))
      tep_db_query("update ".ADMIN_EMPLOYER_MAILS_TABLE." set receiver_mark='Yes' where id='".$mail_id[$i]."'");
    }
    $messageStack->add_session(MESSAGE_SUCCESS_MARKED,'success');
    tep_redirect(tep_href_link(FILENAME_RECRUITER_MAILS,$page_string. tep_get_all_get_params(array('lower','higher'))));
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
     if($row_check=getAnyTableWhereData(ADMIN_EMPLOYER_MAILS_TABLE." as em "," em.receiver_id ='".$_SESSION['sess_recruiterid']."' and  em.id ='".(int)$mail_id[$i]."'","em.id"))
      tep_db_query("update ".ADMIN_EMPLOYER_MAILS_TABLE." set receiver_mark='No' where id='".$mail_id[$i]."'");
    }
    $messageStack->add_session(MESSAGE_SUCCESS_UNMARKED,'success');
    tep_redirect(tep_href_link(FILENAME_RECRUITER_MAILS,$page_string. tep_get_all_get_params(array('lower','higher'))));
    break;
		case 'confirm_send':
		 $mail=getAnyTableWhereData(ADMIN_EMPLOYER_MAILS_TABLE,"id='".$_POST['id']."'");
			$recruiter_email=getAnyTableWhereData(RECRUITER_LOGIN_TABLE,"recruiter_id='".tep_db_input($mail['receiver_id'])."'");
 		$recruiter_name=getAnyTableWhereData(RECRUITER_TABLE,"recruiter_id='".tep_db_input($mail['receiver_id'])."'");
			/*echo $recruiter_name['recruiter_first_name'].'&nbsp;'.$recruiter_name['recruiter_last_name'].'<br/>';
			echo $email_address['recruiter_email_address'].'<br/>';
			echo $_POST['TR_subject'].'<br/>';
			echo $_POST['TR_message'].'<br/>';die();*/
			$sql_data_array=array('sender_id'  => $mail['receiver_id'],
				                     'receiver_id'=> '',
                         'subject'    => $_POST['TR_subject'],
                         'message'    => $_POST['TR_message'],
                         'inserted'   => 'now()',
                        );
   tep_mail(SITE_OWNER,ADMIN_EMAIL , $_POST['TR_subject'], $_POST['TR_message'], $recruiter_name['recruiter_first_name'].'&nbsp;'.$recruiter_name['recruiter_last_name'] , $recruiter_email['recruiter_email_address']);
			tep_db_perform(ADMIN_EMPLOYER_MAILS_TABLE,$sql_data_array);
	  $messageStack->add_session(MESSAGE_SUCCESS_SENT, 'success');
   tep_redirect(tep_href_link(FILENAME_RECRUITER_MAILS));
	  break;
 }
}
if(isset($_GET['query_string1'])  && $_GET['query_string1']!='')
{
 $mail_id = check_data($_GET['query_string1'],"=+=","mail","mail_id");
}
if(tep_not_null($mail_id))
if(!$row_check=getAnyTableWhereData(ADMIN_EMPLOYER_MAILS_TABLE." as em "," em.receiver_id ='".$_SESSION['sess_recruiterid']."' and  em.id ='".(int)$mail_id."'","em.subject,em.attachment_file,em.message"))
{
 $messageStack->add_session(ERROR_MAIL_NOT_EXIST, 'error');
 tep_redirect(FILENAME_RECRUITER_MAILS);
}
if(($_GET['action'] == 'send_mail') && (tep_not_null($_GET['query_string1'])))
{
	$mail=getAnyTableWhereData(ADMIN_EMPLOYER_MAILS_TABLE,"id='".$mail_id."'");
	$recruiter_mail=getAnyTableWhereData(RECRUITER_LOGIN_TABLE,"recruiter_id='".$mail['receiver_id']."'");
 $template->assign_vars(array(
  'HEADING_TITLE1'       => HEADING_TITLE1,
		'HEADING_TO_EMAIL'     => HEADING_TO_EMAIL,
		'HEADING_TO_EMAIL1'    => ADMIN_EMAIL,
  'INFO_TEXT_FROM_EMAIL' => INFO_TEXT_FROM_EMAIL,
		'INFO_TEXT_FROM_EMAIL1'=> $recruiter_mail['recruiter_email_address'],
		'INFO_TEXT_SUBJECT'    => INFO_TEXT_SUBJECT,
  'INFO_TEXT_SUBJECT1'   => tep_draw_input_field('TR_subject', '', 'size="45"', true ),
		'INFO_MAIL_ATTACHMENT' => INFO_MAIL_ATTACHMENT,
  'INFO_MAIL_ATTACHMENT1'=> tep_draw_file_field('attachment', false),
  'INFO_TEXT_MESSAGE'    => INFO_TEXT_MESSAGE,
		'JOB_SEARCH_LEFT'      => JOB_SEARCH_LEFT,
	 'LEFT_HTML'=>LEFT_HTML,
  'INFO_TEXT_MESSAGE1'   => tep_draw_textarea_field('TR_message', 'soft', '60%', '10', '', '', true, true),
		'form'                 => tep_draw_form('send', FILENAME_RECRUITER_MAILS,'','post', 'onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('id',$mail_id).tep_draw_hidden_field('action1','confirm_send'),
		'button'               => tep_image_submit(PATH_TO_BUTTON.'button_send.gif', IMAGE_SEND).'&nbsp;&nbsp;<a href="'.tep_href_link(FILENAME_RECRUITER_MAILS).'">'.tep_image_button(PATH_TO_BUTTON.'button_back.gif',IMAGE_BACK).'</a>',
  'update_message'=>$messageStack->output()));
 $template->pparse('send_mail');
}
if(($mail_id>0) && !tep_not_null($_GET['action']))
{
 /*$file_directory=get_file_directory($row_check['attachment_file']);
 if($row_check['attachment_file']!='' && is_file(PATH_TO_MAIN_PHYSICAL_RECRUITER_EMAIL_ATTACHMENT.$file_directory.'/'.$row_check['attachment_file']))
 $attachment_file_name=$row_check['attachment_file'];
 $query_string5=encode_string("mail_attachment@#^#@".$attachment_file_name."@#^#@attachment");*/
 tep_db_query("update ".ADMIN_EMPLOYER_MAILS_TABLE." set receiver_see='Yes' where id='".$mail_id."'");

  $template->assign_vars(array(
  'HEADING_TITLE'     => HEADING_TITLE,
  'INFO_TEXT_PLEASE_CHECK_ONE'=>INFO_TEXT_PLEASE_CHECK_ONE,
  'INFO_TEXT_SUBJECT' => INFO_TEXT_SUBJECT,
  'INFO_TEXT_SUBJECT1'=> tep_db_output($row_check['subject']),//.(($attachment_file_name!='')?'<span class="small"><a href="'.tep_href_link(FILENAME_ATTACHMENT_DOWNLOAD,"query_string1=".$query_string5).'"  title="'.tep_db_output(substr($attachment_file_name,14)).'">'.tep_image_button('img/attachment.gif',IMAGE_ATTACHMENT.' :'.tep_db_output(substr($attachment_file_name,14))).' :'.tep_db_output(substr($attachment_file_name,14)).'</a></span>':''),
  'INFO_TEXT_MESSAGE' => INFO_TEXT_MESSAGE,
  'INFO_TEXT_MESSAGE1'=> stripslashes($row_check['message']),
		'INFO_TEXT_REPLY'   => "<a href='".tep_href_link(FILENAME_RECRUITER_MAILS,"query_string1=".$_GET['query_string1'])."&action=send_mail'>".tep_image_button(PATH_TO_BUTTON.'button_reply.gif', IMAGE_REPLY)."</a>",
//		'form'              => tep_draw_form('send', FILENAME_RECRUITER_MAILS,'query_string1='.$_GET['query_string1']','post', 'onsubmit="return ValidateForm(this)"'),
  'INFO_TEXT_BACK'    => '<a href="#" onclick="javascript:history.back()">'.tep_image_button(PATH_TO_BUTTON.'button_back.gif', IMAGE_BACK).'</a>&nbsp;&nbsp;',
  'RIGHT_BOX_WIDTH'   => RIGHT_BOX_WIDTH1,
  'LEFT_HTML'         => LEFT_HTML,
		'JOB_SEARCH_LEFT'   => JOB_SEARCH_LEFT,
  'RIGHT_HTML'        => RIGHT_HTML,
  'update_message'=>$messageStack->output()));
 $template->pparse('email_message');

}
if((!$mail_id) && !tep_not_null($_GET['action']))
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
 $view_link="";
 if($mail_status=='inactive')
  $show_mail_status='Trash';
 else
  $show_mail_status='Inbox';

 if($show_mail_status=='Inbox')
  $view_link1 ="  <a href='#'  onclick='view_data(\"all\",\"inactive\")'>".INFO_TEXT_VIEW_IN_TRASH."</a>";
 else
  $view_link1 ="  <a href='#'  onclick='view_data(\"all\",\"active\")'>".INFO_TEXT_VIEW_IN_INBOX."</a>";

 if($view=='unread')
 {
  $view_link =INFO_TEXT_VIEW_IN."  ".$show_mail_status." : <a href='#' onclick='view_data(\"all\",\"".$mail_status."\")'>".INFO_TEXT_VIEW_ALL."</a>";
  $view_link .=" | ".INFO_TEXT_UNREAD;
  $view_link .=" | <a href='#' onclick='view_data(\"marked\",\"".$mail_status."\")'>".INFO_TEXT_VIEW_MARKED."</a>";
 }
 else if($view=='marked')
 {
  $view_link =INFO_TEXT_VIEW_IN." ".$show_mail_status.": <a href='#' onclick='view_data(\"all\",\"".$mail_status."\")'>".INFO_TEXT_VIEW_ALL."</a>";
  $view_link .=" | <a href='#' onclick='view_data(\"unread\",\"".$mail_status."\")'>".INFO_TEXT_UNREAD."</a>";
  $view_link .=" | ".INFO_TEXT_VIEW_MARKED;
 }
 else
 {
  $view_link =INFO_TEXT_VIEW_IN." ".$show_mail_status." : ".INFO_TEXT_VIEW_ALL;
  $view_link .=" | <a href='#' onclick='view_data(\"unread\",\"".$mail_status."\")'>".INFO_TEXT_UNREAD."</a>";
  $view_link .=" | <a href='#' onclick='view_data(\"marked\",\"".$mail_status."\")'>".INFO_TEXT_VIEW_MARKED."</a>";
 }
 $table_names=ADMIN_EMPLOYER_MAILS_TABLE." as em left join ".RECRUITER_TABLE."  as r on (em.receiver_id =r.recruiter_id) ";
 $whereClause="em.sender_id=0 and em.receiver_id='".$_SESSION['sess_recruiterid']."'";

 if($mail_status=='active')
  $whereClause.=" and em.receiver_mail_status='active'";
 elseif($mail_status=='inactive')
  $whereClause.=" and em.receiver_mail_status='inactive'";
 else
  $whereClause.=" and em.receiver_mail_status='active'";

 if($view=='unread')
  $whereClause.=" and em.receiver_see ='No'";
 elseif($view=='marked')
  $whereClause.=" and em.receiver_mark ='Yes'";

 $field_names="em.id,em.subject,em.inserted,em.receiver_see,em.receiver_mark,r.recruiter_company_name";
 $query1 = "select count(em.id) as x1 from $table_names where $whereClause ";
 $result1=tep_db_query($query1);
 $tt_row=tep_db_fetch_array($result1);
 $x1=$tt_row['x1'];
 //echo $x1;
 //////////////////
 ///only for sorting starts

 include_once(PATH_TO_MAIN_PHYSICAL_CLASS.'sort_by_clause.php');
 $sort_array=array("r.recruiter_company_name",'em.application_id','em.inserted');
 $obj_sort_by_clause=new sort_by_clause($sort_array,'em.inserted desc');
 $order_by_clause=$obj_sort_by_clause->return_value;
 //print_r($obj_sort_by_clause->return_sort_array['name']);
 //print_r($obj_sort_by_clause->return_sort_array['image']);
 $see_before_page_number_array=see_before_page_number1($sort_array,$field,'em.inserted',$order,'desc',$lower,'0',$higher,'20');
 $lower=$see_before_page_number_array['lower'];
 $higher=$see_before_page_number_array['higher'];
 $field=$see_before_page_number_array['field'];
 $order=$see_before_page_number_array['order'];
 $hidden_fields.=tep_draw_hidden_field('sort',$sort);
 $hidden_fields.=tep_draw_hidden_field('view',$view);
 $hidden_fields.=tep_draw_hidden_field('mail_status',$mail_status);
 $hidden_fields.=tep_draw_hidden_field('action1','');
 $template->assign_vars(array('TABLE_HEADING_MAIL_SENDER'=>"<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][0]."','".$lower."');\"><u>".TABLE_HEADING_MAIL_SENDER.'</u>'.$obj_sort_by_clause->return_sort_array['image'][0]."</a>",
                              'TABLE_HEADING_JOB_TITLE'=>"<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][1]."','".$lower."');\"><u>".TABLE_HEADING_JOB_TITLE.'</u>'.$obj_sort_by_clause->return_sort_array['image'][1]."</a>",
                              'TABLE_HEADING_MAIL_INSERTED'=>"<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][2]."','".$lower."');\"><u>".TABLE_HEADING_MAIL_INSERTED.'</u>'.$obj_sort_by_clause->return_sort_array['image'][2]."</a>"));
 ///only for sorting ends

 $totalpage=ceil($x1/$higher);
 $query = "select $field_names from $table_names where $whereClause ORDER BY ". $order_by_clause ." limit $lower,$higher ";
 $result=tep_db_query($query);
 $x=tep_db_num_rows($result);
 ///////////////
 // $query= "select  ai.id,a.application_id,ai.subject,ai.attachment_file,ai.inserted,ai.email_address,ai.receiver_see from ".ADMIN_EMPLOYER_MAILS_TABLE." as ai left join ".APPLICATION_TABLE."  as a on (a.id=ai.application_id) where a.jobseeker_id ='".$_SESSION['sess_jobseekerid']."' order by ai.inserted desc  ";
 // $query_result = tep_db_query($query);
 $pno= ceil($lower+$higher)/($higher);
 if($x > 0 && $x1 > 0)
 {
  $alternate=1;
  while ($recruiter_mail = tep_db_fetch_array($result))
  {
   /*$attachment_file_name='';
   $file_directory=get_file_directory($recruiter_mail['attachment_file']);
   if($recruiter_mail['attachment_file']!='' && is_file(PATH_TO_MAIN_PHYSICAL_RECRUITER_EMAIL_ATTACHMENT.$file_directory.'/'.$recruiter_mail['attachment_file']))
   $attachment_file_name=$recruiter_mail['attachment_file'];*/
   $row_selected=' class="dataTableRow'.(($recruiter_mail['receiver_see']=='No')?'3':($alternate%2==1?'1':'2')).'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
   $query_string1=encode_string("mail=+=".$recruiter_mail['id']."=+=mail_id");
			$template->assign_block_vars('recruiter_mail',array( 'row_selected' => $row_selected,
   'check_box'  => tep_draw_checkbox_field('mail_no[]',$recruiter_mail['id']).(($recruiter_mail['receiver_mark']=='Yes')?tep_image_button('image/mark.gif',"Marked"):""),
   'sender'     => 'Admin',
   'subject'    => "<a href='".tep_href_link(FILENAME_RECRUITER_MAILS,"query_string1=".$query_string1)."'>".tep_db_output($recruiter_mail['subject'])."</a>",
   'inserted'   => ($recruiter_mail['inserted']=='0000-00-00')?'-':tep_db_output(formate_date($recruiter_mail['inserted'])),
   ));
    $alternate++;
    $lower = $lower + 1;
  }
   $plural=($x1=="1")?INFO_TEXT_MAIL:INFO_TEXT_MAILS;
   $template->assign_vars(array('total'=>SITE_TITLE.' '.INFO_TEXT_HAS_FOUND." $x1 ".$plural." ."));
  $check_link='<a href="#" onclick="checkall()">'.INFO_TEXT_CHECK_ALL.'</a> / <a href="#" onclick="uncheckall()">'.INFO_TEXT_UNCHECK_ALL.'</a>';
  if($show_mail_status=='Inbox')
  {
   $check_link1='<b>'.INFO_TEXT_WITH_SELECTED.'</b>
              <select name="select_action"  onchange="select_action2();">
                 <option value="" selected="selected">'.INFO_TEXT_WITH_SELECTED1.'</option>
                 <option value="marked" >'.INFO_TEXT_VIEW_MARKED.'</option>
                 <option value="unmarked" >'.INFO_TEXT_CLEAR_MARK.'</option>
                 <option value="delete" >'.INFO_TEXT_DELETE.'</option>
              </select>';
  }
  else
  {
   $check_link1='<b>'.INFO_TEXT_WITH_SELECTED.'</b>
              <select name="select_action"  onchange="select_action2();">
                 <option value="" selected="selected">'.INFO_TEXT_WITH_SELECTED1.'</option>
                 <option value="marked" >'.INFO_TEXT_VIEW_MARKED.'</option>
                 <option value="unmarked" >'.INFO_TEXT_CLEAR_MARK.'</option>
                 <option value="restore" >'.INFO_TEXT_RESTORE.'</option>
                 <option value="complete_delete" >'.INFO_TEXT_DELETE.'</option>
              </select>';
  }
 }
 else
 {
    $template->assign_vars(array('total'=>SITE_TITLE.' '.INFO_TEXT_HAS_NOT_FOUND." <br><br>&nbsp;&nbsp;&nbsp;"));
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
  'TABLE_HEADING_MAIL_SUBJECT'=>TABLE_HEADING_MAIL_SUBJECT,
  'INFO_TEXT_PLEASE_CHECK_ONE'=>INFO_TEXT_PLEASE_CHECK_ONE,
  'form'=>tep_draw_form('page', FILENAME_RECRUITER_MAILS,'','post', 'onsubmit="return ValidateForm(this)"'),
  'view_link'   => $view_link,
  'view_link1'  => $view_link1,
  'check_link'  => $check_link,
  'check_link1' => $check_link1,
  'lower_value' => $lower_value,
  'higher_value'=> $higher_value,
  'mail_status'=>  $status_value,
  'RIGHT_BOX_WIDTH'   => $RIGHT_BOX_WIDTH,
  'LEFT_HTML'         => LEFT_HTML,
		'JOB_SEARCH_LEFT'   => JOB_SEARCH_LEFT,
  'RIGHT_HTML'        => RIGHT_HTML,
  'update_message'=>$messageStack->output()));
 $template->pparse('email');
}
?>