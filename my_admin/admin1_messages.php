<?
/*
***********************************************************
**********# Name          : Shamhu Prasad Patnaik #********
**********# Company       : Aynsoft             #**********
**********# Copyright (c) www.aynsoft.com 2004  #**********
***********************************************************
*/
include_once("../include_files.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_MESSAGES);
$template->set_filenames(array('email'  =>'admin1_messages.htm','email_message'  =>'admin1_messages1.htm','send_mail' => 'admin1_messages2.htm'));
include_once(FILENAME_ADMIN_BODY);
$action   = (isset($_POST['action1']) ? $_POST['action1'] : '');

//print_r($_POST);
//print_r($_GET);die();
$error = false;
if(tep_not_null($action))
{
 switch($action)
 {
  case 'delete':
    $mail_id = tep_db_prepare_input($_POST['mail_no']);
    $lower   =  tep_db_prepare_input($_POST['lower']);
    $higher  = tep_db_prepare_input($_POST['higher']);
    $sort  = tep_db_prepare_input($_POST['sort']);
    $mail_status=tep_db_prepare_input($_POST['mail_status']);
    $page_string='';
    if($lower >0)
    $page_string='lower='.$lower;

				if($higher >0)
     $page_string.=(($page_string=='')?'':'&').'higher='.$higher;

    if(tep_not_null($sort))
     $page_string.=(($page_string=='')?'':'&').'sort='.$sort;

    if($mail_status=='deleted')
     $page_string.=(($page_string=='')?'':'&').'mail_status=deleted';
    for($i=0;$i<count($mail_id);$i++)
    {
     if($row_check=getAnyTableWhereData(ADMIN_MESSAGE_TABLE." as em ","em.id ='".tep_db_input($mail_id[$i])."'","em.id"))
     tep_db_query("update ".ADMIN_MESSAGE_TABLE." set msg_status='deleted' where id='".$mail_id[$i]."'");
    }
    $messageStack->add_session(MESSAGE_SUCCESS_DELETED,'success');
    tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_MESSAGES,$page_string. tep_get_all_get_params(array('lower','higher'))));
    break;
  case 'complete_delete':
    $mail_id= tep_db_prepare_input($_POST['mail_no']);
    $lower=  tep_db_prepare_input($_POST['lower']);
    $higher= tep_db_prepare_input($_POST['higher']);
    $mail_status=tep_db_prepare_input($_POST['mail_status']);
    $sort  = tep_db_prepare_input($_POST['sort']);
    $page_string='';
    if($lower >0)
    $page_string='lower='.$lower;
    if($higher >0)
     $page_string.=(($page_string=='')?'':'&').'higher='.$higher;

				if(tep_not_null($sort))
     $page_string.=(($page_string=='')?'':'&').'sort='.$sort;

    if($mail_status=='deleted')
     $page_string.=(($page_string=='')?'':'&').'mail_status=deleted';
    for($i=0;$i<count($mail_id);$i++)
    {
     if($row_check=getAnyTableWhereData(ADMIN_MESSAGE_TABLE." as em "," em.id ='".tep_db_input($mail_id[$i])."'","em.id"))
      tep_db_query("delete from ".ADMIN_MESSAGE_TABLE."  where id='".$mail_id[$i]."'");
    }
    $messageStack->add_session(MESSAGE_SUCCESS_RESTORE,'success');
    tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_MESSAGES,$page_string. tep_get_all_get_params(array('lower','higher'))));
    break;
  case 'restore':
    $mail_id= tep_db_prepare_input($_POST['mail_no']);
    $lower  = tep_db_prepare_input($_POST['lower']);
    $higher = tep_db_prepare_input($_POST['higher']);
    $mail_status=tep_db_prepare_input($_POST['mail_status']);
    $sort  = tep_db_prepare_input($_POST['sort']);
    $page_string='';
    if($lower >0)
    $page_string='lower='.$lower;
    if($higher >0)
     $page_string.=(($page_string=='')?'':'&').'higher='.$higher;

				if(tep_not_null($sort))
     $page_string.=(($page_string=='')?'':'&').'sort='.$sort;

    if($mail_status=='deleted')
     $page_string.=(($page_string=='')?'':'&').'mail_status=deleted';
    for($i=0;$i<count($mail_id);$i++)
    {
     if($row_check=getAnyTableWhereData(ADMIN_MESSAGE_TABLE." as em "," em.id ='".tep_db_input($mail_id[$i])."'","em.id"))
      tep_db_query("update ".ADMIN_MESSAGE_TABLE." set msg_status='active' where id='".$mail_id[$i]."'");
    }
    $messageStack->add_session(MESSAGE_SUCCESS_DELETED,'success');
    tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_MESSAGES,$page_string. tep_get_all_get_params(array('lower','higher'))));
    break;
  case 'marked':
    $mail_id= tep_db_prepare_input($_POST['mail_no']);
    $lower  = tep_db_prepare_input($_POST['lower']);
    $higher = tep_db_prepare_input($_POST['higher']);
    $mail_status=tep_db_prepare_input($_POST['mail_status']);
    $sort  = tep_db_prepare_input($_POST['sort']);
    $page_string='';
    if($lower >0)
    $page_string='lower='.$lower;
    if($higher >0)
     $page_string.=(($page_string=='')?'':'&').'higher='.$higher;
    if($mail_status=='deleted')
     $page_string.=(($page_string=='')?'':'&').'mail_status=deleted';

				if(tep_not_null($sort))
     $page_string.=(($page_string=='')?'':'&').'sort='.$sort;

    for($i=0;$i<count($mail_id);$i++)
    {
     if($row_check=getAnyTableWhereData(ADMIN_MESSAGE_TABLE." as m "," m.id ='".tep_db_input($mail_id[$i])."'","m.id"))
      tep_db_query("update ".ADMIN_MESSAGE_TABLE." set msg_mark='yes' where id='".$mail_id[$i]."'");
    }
    $messageStack->add_session(MESSAGE_SUCCESS_MARKED,'success');
    tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_MESSAGES,$page_string. tep_get_all_get_params(array('lower','higher'))));
    break;
  case 'unmarked':
    $mail_id= tep_db_prepare_input($_POST['mail_no']);
    $lower   =  tep_db_prepare_input($_POST['lower']);
    $higher  =  tep_db_prepare_input($_POST['higher']);
    $mail_status=tep_db_prepare_input($_POST['mail_status']);
    $sort  = tep_db_prepare_input($_POST['sort']);
    $page_string='';
    if($lower >0)
    $page_string='lower='.$lower;
    if($higher >0)
     $page_string.=(($page_string=='')?'':'&').'higher='.$higher;
    if($mail_status=='deleted')
     $page_string.=(($page_string=='')?'':'&').'mail_status=deleted';

				if(tep_not_null($sort))
     $page_string.=(($page_string=='')?'':'&').'sort='.$sort;

    for($i=0;$i<count($mail_id);$i++)
    {
     if($row_check=getAnyTableWhereData(ADMIN_MESSAGE_TABLE." as em "," em.id ='".tep_db_input($mail_id[$i])."'","em.id"))
      tep_db_query("update ".ADMIN_MESSAGE_TABLE." set msg_mark='no' where id='".$mail_id[$i]."'");
    }
    $messageStack->add_session(MESSAGE_SUCCESS_UNMARKED,'success');
    tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_MESSAGES,$page_string. tep_get_all_get_params(array('lower','higher'))));
    break;
		case 'confirm_send':
   $mail_id      =  tep_db_prepare_input($_POST['id']);
   $user_name    =  tep_db_prepare_input($_POST['TR_user_name']);
   $user_email_address    =  tep_db_prepare_input($_POST['to_email_address']);
   $your_name    =  tep_db_prepare_input($_POST['TR_your_name']);
   $msg_subject  =  tep_db_prepare_input($_POST['TR_subject']);
   $message      =  tep_db_prepare_input($_POST['TR_message']);
   $query_string1=encode_string("mail=+=".$mail_id."=+=mail_id");

		 if(!tep_not_null($user_name))
   {
    $error =true;
    $messageStack->add(USER_NAME_ERROR, 'error');
   }
		 if(!tep_not_null($your_name))
   {
    $error =true;
    $messageStack->add(YOUR_NAME_ERROR, 'error');
   }
		 if(!tep_not_null($msg_subject))
   {
    $error =true;
    $messageStack->add(MAIL_SUBJECT_ERROR, 'error');
   }
		 if(!tep_not_null($message))
   {
    $error =true;
    $messageStack->add(MAIL_MESSAGE_ERROR, 'error');
   }
   //////////////
			if(!$error)
		 {
		  tep_mail($user_name, $user_email_address, $msg_subject, nl2br($message), $your_name,EMAIL_FROM);
	   $messageStack->add_session(MESSAGE_SUCCESS_SENT, 'success');
    tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_MESSAGES,'msg=success&query_string1='.$query_string1));
		 }
			else
				$action ='send_mail';

	  break;
 }
}
if(isset($_GET['query_string1'])  && $_GET['query_string1']!='')
{
 $query_string1=  tep_db_prepare_input($_GET['query_string1']);
 $mail_id = check_data($query_string1,"=+=","mail","mail_id");
}
if(tep_not_null($mail_id) && $mail_id>0)
{
 if(!$row_check=getAnyTableWhereData(ADMIN_MESSAGE_TABLE." as m "," m.id ='".tep_db_input($mail_id)."'","*"))
 {
  $messageStack->add_session(ERROR_MAIL_NOT_EXIST, 'error');
  tep_redirect(PATH_TO_ADMIN.FILENAME_ADMIN1_MESSAGES);
 }
	if(isset($_GET['action']))
 $action=  tep_db_prepare_input($_GET['action']);
	if($action =='send_mail')
	{
  if(!$error)
		{
   $user_name          = $row_check['user_name'];
   $user_email_address = $row_check['user_email_address'];
   $msg_subject        = 'Re : '.$row_check['msg_subject'];
			$your_name          = SITE_OWNER;
			$message ='';
		}
  $template->assign_vars(array(
  'HEADING_TITLE1'       => HEADING_TITLE1,
  'INFO_TEXT_USER_NAME'  => INFO_TEXT_USER_NAME,
  'INFO_TEXT_USER_NAME1' => tep_draw_input_field('TR_user_name', $user_name, 'size="45" class="form-control form-control-sm"', true ),
		'HEADING_TO_EMAIL'     => HEADING_TO_EMAIL,
		'HEADING_TO_EMAIL1'    => tep_db_output($user_email_address),
  'INFO_TEXT_FROM_EMAIL' => INFO_TEXT_FROM_EMAIL,
		'INFO_TEXT_FROM_EMAIL1'=> EMAIL_FROM,
  'INFO_TEXT_YOUR_NAME'  => INFO_TEXT_YOUR_NAME,
  'INFO_TEXT_YOUR_NAME1' => tep_draw_input_field('TR_your_name', $your_name, 'size="45" class="form-control form-control-sm"', true ),
		'INFO_TEXT_SUBJECT'    => INFO_TEXT_SUBJECT,
  'INFO_TEXT_SUBJECT1'   => tep_draw_input_field('TR_subject', $msg_subject, 'size="45" class="form-control form-control-sm"', true),
  'INFO_TEXT_MESSAGE'    => INFO_TEXT_MESSAGE,
	//	'JOB_SEARCH_LEFT'      => JOB_SEARCH_LEFT,
  'INFO_TEXT_MESSAGE1'   => tep_draw_textarea_field('TR_message', 'soft', '60%', '10', $message, 'class="form-control form-control-sm"', true, true),
		'form'                 => tep_draw_form('send', PATH_TO_ADMIN.FILENAME_ADMIN1_MESSAGES,'','post', 'onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('to_email_address',$user_email_address).tep_draw_hidden_field('id',$mail_id).tep_draw_hidden_field('action1','confirm_send'),
		'button'               => tep_draw_submit_button_field('','Send','class="btn btn-primary"').'&nbsp;&nbsp;<a href="'.tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_MESSAGES,'query_string1='.$query_string1).'">'.tep_button('Back','class="btn btn-secondary"').'</a>',
  'update_message'=>$messageStack->output()));
 $template->pparse('send_mail');


	}
	else
	{
		if(!$error)
		{
			if(!isset($_GET['msg']))
   tep_db_query("update ".ADMIN_MESSAGE_TABLE." set msg_seen='yes' where id='".tep_db_input($mail_id)."'");
   $user_name          = $row_check['user_name'];
   $user_email_address = $row_check['user_email_address'];
   $msg_subject        = $row_check['msg_subject'];
   $user_message       = $row_check['user_message'];
		}
  $template->assign_vars(array(
  'HEADING_TITLE'    => HEADING_TITLE,
  'INFO_TEXT_USER_NAME'  => INFO_TEXT_USER_NAME,
  'INFO_TEXT_USER_NAME1' => tep_db_output($user_name),
  'INFO_TEXT_USER_EMAIL' => INFO_TEXT_USER_EMAIL,
  'INFO_TEXT_USER_EMAIL1'=> tep_db_output($user_email_address),
  'INFO_TEXT_SUBJECT'    => INFO_TEXT_SUBJECT,
  'INFO_TEXT_SUBJECT1'   => tep_db_output($msg_subject),
  'INFO_TEXT_MESSAGE'    => INFO_TEXT_MESSAGE,
  'INFO_TEXT_MESSAGE1'   =>nl2br(tep_db_output($user_message)),
		'INFO_TEXT_REPLY'   => "<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_MESSAGES,"query_string1=".$query_string1)."&action=send_mail'>".tep_button('Reply','class="btn btn-primary"')."</a>",
  'INFO_TEXT_BACK'=>'<a href="#" onclick="javascript:window.close();">'.tep_button('Close','class="btn btn-secondary"').'</a>',
  'RIGHT_BOX_WIDTH'   => RIGHT_BOX_WIDTH1,
//		'JOB_SEARCH_LEFT'   => JOB_SEARCH_LEFT,
  //'RIGHT_HTML'        => RIGHT_HTML,
  'update_message'=>$messageStack->output()));
  $template->pparse('email_message');

	}
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

 if(isset($_GET['sort']) && $_GET['sort']!='' && !isset($_POST['sort']))
  $sort=tep_db_prepare_input($_GET['sort']);
 else
  $sort=tep_db_prepare_input($_POST['sort']);

 $view_link="";
 if($mail_status=='deleted')
  $show_mail_status='Trash';
 else
  $show_mail_status='Inbox';

 if($show_mail_status=='Inbox')
  $view_link1 ="  <a href='#' class='red' onclick='view_data(\"all\",\"deleted\")'>View in Trash</a>";
 else
  $view_link1 ="  <a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_MESSAGES)."' class='red'>View in Inbox</a>";

 if($view=='unread')
 {
  $view_link =" View in ".$show_mail_status." : <a href='#' onclick='view_data(\"all\",\"".$mail_status."\")'>all</a>";
  $view_link .=" | Unread";
  $view_link .=" | <a href='#' onclick='view_data(\"marked\",\"".$mail_status."\")'>Marked</a>";
 }
 else if($view=='marked')
 {
  $view_link ="View in ".$show_mail_status.": <a href='#' onclick='view_data(\"all\",\"".$mail_status."\")'>all</a>";
  $view_link .=" | <a href='#' onclick='view_data(\"unread\",\"".$mail_status."\")'>Unread</a>";
  $view_link .=" | Marked";
 }
 else
 {
  $view_link ="View in ".$show_mail_status." : All";
  $view_link .=" | <a href='#' onclick='view_data(\"unread\",\"".$mail_status."\")'>Unread</a>";
  $view_link .=" | <a href='#' onclick='view_data(\"marked\",\"".$mail_status."\")'>Marked</a>";
 }
 $table_names=ADMIN_MESSAGE_TABLE." as m ";
 $whereClause="";

 if($mail_status=='active')
  $whereClause.="  m.msg_status='active'";
 elseif($mail_status=='deleted')
  $whereClause.=" m.msg_status='deleted'";
 else
  $whereClause.=" m.msg_status='active'";

 if($view=='unread')
  $whereClause.=" and m.msg_seen ='no'";
 elseif($view=='marked')
  $whereClause.=" and m.msg_seen='yes'";

 $field_names="m.id,m.msg_subject,m.inserted,m.msg_seen,m.msg_mark,m.user_name,m.user_email_address";
 $query1 = "select count(m.id) as x1 from $table_names where $whereClause ";
 $result1=tep_db_query($query1);
 $tt_row=tep_db_fetch_array($result1);
 $x1=$tt_row['x1'];
 //echo $x1;
 //////////////////
 ///only for sorting starts

 include_once(PATH_TO_MAIN_PHYSICAL_CLASS.'sort_by_clause.php');
 $sort_array=array("m.user_name",'m.user_email_address','m.inserted');
 $obj_sort_by_clause=new sort_by_clause($sort_array,'m.inserted desc');
 $order_by_clause=$obj_sort_by_clause->return_value;
 //print_r($obj_sort_by_clause->return_sort_array['name']);
 //print_r($obj_sort_by_clause->return_sort_array['image']);
 $see_before_page_number_array=see_before_page_number1($sort_array,$field,'m.inserted',$order,'desc',$lower,'0',$higher,'20');
 $lower=$see_before_page_number_array['lower'];
 $higher=$see_before_page_number_array['higher'];
 $field=$see_before_page_number_array['field'];
 $order=$see_before_page_number_array['order'];
 $hidden_fields.=tep_draw_hidden_field('sort',$sort);
 $hidden_fields.=tep_draw_hidden_field('view',$view);
 $hidden_fields.=tep_draw_hidden_field('mail_status',$mail_status);
 $hidden_fields.=tep_draw_hidden_field('action1','');
 $template->assign_vars(array('TABLE_HEADING_USER_NAME'=>"<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][0]."','".$lower."');\"><u>".TABLE_HEADING_USER_NAME.'</u>'.$obj_sort_by_clause->return_sort_array['image'][0]."</a>",
                              'TABLE_HEADING_MAIL_ADDRESS' =>"<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][1]."','".$lower."');\"><u>".TABLE_HEADING_MAIL_ADDRESS.'</u>'.$obj_sort_by_clause->return_sort_array['image'][1]."</a>",
                              'TABLE_HEADING_MAIL_INSERTED'=>"<a href='#' class='white' onclick=\"submit_thispage('".$obj_sort_by_clause->return_sort_array['name'][2]."','".$lower."');\"><u>".TABLE_HEADING_MAIL_INSERTED.'</u>'.$obj_sort_by_clause->return_sort_array['image'][2]."</a>"));
 ///only for sorting ends

 $totalpage=ceil($x1/$higher);
 $query = "select $field_names from $table_names where $whereClause ORDER BY ". $order_by_clause ." limit $lower,$higher ";
 $result=tep_db_query($query);
 $x=tep_db_num_rows($result);
 ///////////////
 $pno= ceil($lower+$higher)/($higher);
 if($x > 0 && $x1 > 0)
 {
  $alternate=1;
  while ($row_mail = tep_db_fetch_array($result))
  {
   $row_selected=' class="dataTableRow'.(($row_mail['msg_seen']=='no')?'5':($alternate%2==1?'1':'2')).'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
   $query_string1=encode_string("mail=+=".$row_mail['id']."=+=mail_id");
   $template->assign_block_vars('user_mail',array(
				 'row_selected' => $row_selected,
     'check_box' => tep_draw_checkbox_field('mail_no[]',$row_mail['id']).(($row_mail['msg_mark']=='yes')?tep_image_button('image/mark.gif',"Marked"):""),
     'user_name'    => tep_db_output($row_mail['user_name']),
     'email_address' =>tep_db_output($row_mail['user_email_address']),
     'subject'   => "<a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_MESSAGES,"query_string1=".$query_string1)."' target='_blank'>".tep_db_output($row_mail['msg_subject'])."</a>",
     'inserted'  => ($row_mail['inserted']=='0000-00-00 00:00:00')?'-':tep_db_output(formate_date1($row_mail['inserted'])),
     ));
    $alternate++;
    $lower = $lower + 1;
  }
   $plural=($x1=="1")?"Mail ":"Mails";
   $template->assign_vars(array('total'=>SITE_TITLE." has found <font color='red'><b>$x1</b></font> ".$plural." ."));
  $check_link='<a href="#" onclick="checkall()">Check All</a> / <a href="#" onclick="uncheckall()">Uncheck All</a>';
  if($show_mail_status=='Inbox')
  {
   $check_link1='<b>With Selected</b>
              <select name="select_action"  onchange="select_action2();">
                 <option value="" selected="selected">With selected:</option>
                 <option value="marked" >Marked</option>
                 <option value="unmarked" >Clear Mark</option>
                 <option value="delete" >Delete</option>
              </select>';
  }
  else
  {
   $check_link1='<b>With Selected</b>
              <select name="select_action"  onchange="select_action2();">
                 <option value="" selected="selected">With selected:</option>
                 <option value="marked" >Marked</option>
                 <option value="unmarked" >Clear Mark</option>
                 <option value="restore" >Restore</option>
                 <option value="complete_delete" >Delete</option>
              </select>';
  }
 }
 else
 {
    $template->assign_vars(array('total'=>SITE_TITLE." has not found any mail.<br><br>&nbsp;&nbsp;&nbsp;"));
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
  'form'=>tep_draw_form('page', PATH_TO_ADMIN.FILENAME_ADMIN1_MESSAGES,'','post', 'onsubmit="return ValidateForm(this)"'),
  'view_link'   => $view_link,
  'view_link1'  => $view_link1,
  'check_link'  => $check_link,
  'check_link1' => $check_link1,
  'lower_value' => $lower_value,
  'higher_value'=> $higher_value,
  'mail_status'=>  $status_value,
  'RIGHT_BOX_WIDTH'   => $RIGHT_BOX_WIDTH,
	//	'JOB_SEARCH_LEFT'   => JOB_SEARCH_LEFT,
  //'RIGHT_HTML'        => RIGHT_HTML,
  'update_message'=>$messageStack->output()));
 $template->pparse('email');
}
?>