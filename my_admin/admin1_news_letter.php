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
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_NEWS_LETTER);
$template->set_filenames(array('news_letter' => 'admin1_news_letter.htm',
                               'new_news_letter' => 'admin1_news_letter1.htm',
                               'preview_news_letter' => 'admin1_previewnews_letter.htm',
                               'send_news_letter' => 'admin1_news_letter_send.htm',
                               'confirm_send_news_letter' => 'admin1_news_letter_send.htm'
                               ));
include_once(FILENAME_ADMIN_BODY);
//////////////////////////////////
$action = (isset($_GET['action']) ? $_GET['action'] : '');
if (tep_not_null($action))
{
 switch ($action)
 {
  case 'lock':
  case 'unlock':
   $id = tep_db_prepare_input($_GET['nID']);
   $status = (($action == 'lock') ? '1' : '0');
   tep_db_query("update " . NEWSLETTERS_TABLE . " set locked = '" . $status . "' where id = '" . (int)$id . "'");
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_NEWS_LETTER, 'page=' . $_GET['page'] . '&nID=' . $_GET['nID']));
  break;
  case 'insert':
  case 'update':
   if (isset($_POST['id']))
    $id = tep_db_prepare_input($_POST['id']);
   $newsletter_module = tep_db_prepare_input($_POST['TR_module']);
   $title = tep_db_prepare_input($_POST['TR_title']);
   $content = $_POST['TR_content'];
   $newsletter_error = false;
   if (empty($_POST['TR_title']))
   {
    $messageStack->add(ERROR_NEWSLETTER_TITLE, 'error');
    $newsletter_error = true;
   }
   if (empty($_POST['TR_module']))
   {
    $messageStack->add(ERROR_NEWSLETTER_MODULE, 'error');
    $newsletter_error = true;
   }
   if (empty($_POST['TR_content']))
   {
    $messageStack->add(ERROR_NEWSLETTER_CONTENT, 'error');
    $newsletter_error = true;
   }
   if($action=='update')
   {
    //if($row_check=getAnyTableWhereData(NEWSLETTERS_TABLE," id!='".$id."'  and title ='".tep_db_input($title)."'"))
    {
     //$newsletter_error = true;
     //$messageStack->add(EXIST_NEWSLETTER_TITLE_ERROR,'error');
    }
   }
   else
   {
    //if($row_check=getAnyTableWhereData(NEWSLETTERS_TABLE," title ='".tep_db_input($title)."'"))
    {
     //$newsletter_error  = true;
     //$messageStack->add(EXIST_NEWSLETTER_TITLE_ERROR,'error');
    }
   }
   ////////////////   Attachment ///////////////
   if(!$newsletter_error)
   {
    $destination='';
    //print_r($_POST);   exit;
    //////// file upload resume starts //////
    ///*
    if(tep_not_null($_FILES['attachment']['name']))
    {
     if($obj_resume = new upload('attachment', PATH_TO_MAIN_PHYSICAL_EMAIL_ATTACHMENT,'644',array('doc','pdf','txt','jpg','gif','png')))
     {
      if($row11=getAnyTableWhereData(NEWSLETTERS_TABLE." as n "," n.id='".tep_db_input($_POST['id'])."' "," n.attachment_file"))
      {
       $old_file_name=$row11['attachment_file'];
       if($old_file_name!='')
       if(is_file(PATH_TO_MAIN_PHYSICAL_EMAIL_ATTACHMENT.$old_file_name))
       @unlink(PATH_TO_MAIN_PHYSICAL_EMAIL_ATTACHMENT.$old_file_name);
      }
      $attachment_file_name=tep_db_input($obj_resume->filename);
      $destination=PATH_TO_MAIN_PHYSICAL_EMAIL_ATTACHMENT.$attachment_file_name;
     }
     else
     {
      $messageStack->add(ERROR_ATTACHMENT_FILE, 'error');
      $newsletter_error = true;
     }
    }
    //*/
    //////// file upload ends //////
   }
  ////////////////   Attachment ///////////////
   if ($newsletter_error == false)
   {
    $sql_data_array = array('title' => $title,
                            'content' => $content,
                            'module' => $newsletter_module);
    if(tep_not_null($attachment_file_name))
    $sql_data_array['attachment_file'] = $attachment_file_name;
    if ($action == 'insert')
    {
     $sql_data_array['date_added'] = 'now()';
     $sql_data_array['status'] = '0';
     $sql_data_array['locked'] = '0';
     tep_db_perform(NEWSLETTERS_TABLE, $sql_data_array);
     $row_id_check=getAnyTableWhereData(NEWSLETTERS_TABLE,"1 order by id desc limit 0,1","id");
     $id = $row_id_check['id'];
     $messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');
    }
    elseif ($action == 'update')
    {
     tep_db_perform(NEWSLETTERS_TABLE, $sql_data_array, 'update', "id = '" . (int)$id . "'");
     $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
    }
    tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_NEWS_LETTER, (isset($_GET['page']) ? 'page=' . $_GET['page'] . '&' : '') . 'nID=' . $id));
   }
   else
   {
    $action = 'new';
   }
  break;
  case 'deleteconfirm':
   $id = tep_db_prepare_input($_GET['nID']);
   if($row12=getAnyTableWhereData(NEWSLETTERS_TABLE." as n "," n.id='".tep_db_input($id)."' ","n.attachment_file"))
   {
    $old_file_name=$row12['attachment_file'];
    if($old_file_name!='')
    if(is_file(PATH_TO_MAIN_PHYSICAL_EMAIL_ATTACHMENT.$old_file_name))
    @unlink(PATH_TO_MAIN_PHYSICAL_EMAIL_ATTACHMENT.$old_file_name);
   }
   tep_db_query("delete from " . NEWSLETTERS_TABLE . " where id = '" . (int)$id . "'");
   $messageStack->add_session(MESSAGE_SUCCESS_DELETED, 'success');
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_NEWS_LETTER, 'page=' . $_GET['page']));
  break;
  case 'delete':
  case 'new':
   if (!isset($_GET['nID']))
    break;
  case 'send':
  case 'confirm_send':
   $id = tep_db_prepare_input($_GET['nID']);
   $check_query = tep_db_query("select locked from " . NEWSLETTERS_TABLE . " where id = '" . (int)$id . "'");
   $check = tep_db_fetch_array($check_query);
   if ($check['locked'] < 1)
   {
    switch ($action)
    {
     case 'delete': $error = ERROR_REMOVE_UNLOCKED_NEWSLETTER; break;
     case 'new': $error = ERROR_EDIT_UNLOCKED_NEWSLETTER; break;
     case 'send': $error = ERROR_SEND_UNLOCKED_NEWSLETTER; break;
     case 'confirm_send': $error = ERROR_SEND_UNLOCKED_NEWSLETTER; break;
    }
    $messageStack->add_session($error, 'error');
    tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_NEWS_LETTER, 'page=' . $_GET['page'] . '&nID=' . $_GET['nID']));
   }
  break;
 }
}
///////////////////////////////////
if ($action == 'new')
{
 $form_action = 'insert';
 $parameters = array('title' => '',
                     'content' => '',
                     'module' => '');
 $nInfo = new objectInfo($parameters);
 if (isset($_GET['nID']))
 {
  $form_action = 'update';
  $nID = tep_db_prepare_input($_GET['nID']);
  $newsletter_query = tep_db_query("select title, content, module ,attachment_file from " . NEWSLETTERS_TABLE . " where id = '" . (int)$nID . "'");
  $newsletter = tep_db_fetch_array($newsletter_query);
  //$nInfo->objectInfo($newsletter);
  $nInfo = new objectInfo($newsletter);

 }
 elseif ($_POST)
 {
  $nInfo->title=$_POST['TR_title'];
  $nInfo->content=$_POST['TR_content'];
  $nInfo->module=$_POST['TR_module'];
  //$nInfo->objectInfo($_POST);
 }
 //print_r($nInfo);
 $file_extension = substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], '.'));
 $directory_array = array();
 if ($dir = dir(PATH_TO_MAIN_PHYSICAL_NEWSLETTERS))
 {
  while ($file = $dir->read())
  {
   if (!is_dir(PATH_TO_MAIN_PHYSICAL_NEWSLETTERS . $file))
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
 for ($i=0, $n=sizeof($directory_array); $i<$n; $i++)
 {
		$actual_module_name=substr($directory_array[$i], 0, strrpos($directory_array[$i], '.'));
		switch($actual_module_name)
		{
			case 'all_jobseekers':
				$virtual_module_name="All Jobseekers";
				break;
			case 'all_recruiters':
				$virtual_module_name="All Recruiters";
				break;
				default:
				$virtual_module_name=$actual_module_name;
		}
  $modules_array[] = array('id' => $actual_module_name, 'text' => $virtual_module_name);
 }
 $_tep_draw_form=tep_draw_form('newsletter', PATH_TO_ADMIN.FILENAME_ADMIN1_NEWS_LETTER, (isset($_GET['page']) ? 'page=' . $_GET['page'] . '&' : '') . 'action=' . $form_action.'" onsubmit="return ValidateForm(this)" enctype="multipart/form-data"');
 if ($form_action == 'update')
  $_tep_draw_form.=tep_draw_hidden_field('id', $nID);
}
elseif ($action == 'preview')
{
 $nID = tep_db_prepare_input($_GET['nID']);
 $newsletter_query = tep_db_query("select title, content, module,attachment_file from " . NEWSLETTERS_TABLE . " where id = '" . (int)$nID . "'");
 $newsletter = tep_db_fetch_array($newsletter_query);
 $nInfo = new objectInfo($newsletter);
}
elseif ($action == 'send')
{
 $nID = tep_db_prepare_input($_GET['nID']);
 $newsletter_query = tep_db_query("select title, content, module,attachment_file from " . NEWSLETTERS_TABLE . " where id = '" . (int)$nID . "'");
 $newsletter = tep_db_fetch_array($newsletter_query);
 $nInfo = new objectInfo($newsletter);
 include(PATH_TO_MAIN_PHYSICAL_NEWSLETTERS. $nInfo->module . substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], '.')));
 $module_name = $nInfo->module;
 if(tep_not_null($nInfo->attachment_file))
 {
  if(is_file(PATH_TO_MAIN_PHYSICAL_EMAIL_ATTACHMENT.stripslashes($nInfo->attachment_file)))
  {
   $attachment_name=stripslashes(stripslashes(substr($nInfo->attachment_file,14)));
   $attachment_file=" attachment :".$attachment_name;
  }
  else
  $attachment_file='';
 }
 else
  $attachment_file="";

 $attachment=$attachment_file;
 $module = new $module_name($nInfo->title, stripslashes($nInfo->content),$attachment);
 $send_news_module=($module->show_choose_audience?$module->choose_audience():$module->confirm());
}
elseif ($action == 'confirm')
{
 $nID = tep_db_prepare_input($_GET['nID']);
 $newsletter_query = tep_db_query("select title, content,attachment_file, module from " . NEWSLETTERS_TABLE . " where id = '" . (int)$nID . "'");
 $newsletter = tep_db_fetch_array($newsletter_query);
 $nInfo = new objectInfo($newsletter);
 $module_name = $nInfo->module;
 //$module = new $module_name($nInfo->title, $nInfo->content);
}
elseif ($action == 'confirm_send')
{
 $nID = tep_db_prepare_input($_GET['nID']);
 $newsletter_query = tep_db_query("select id, title, content,module,attachment_file from " . NEWSLETTERS_TABLE . " where id = '" . (int)$nID . "'");
 $newsletter = tep_db_fetch_array($newsletter_query);
 $nInfo = new objectInfo($newsletter);
 include(PATH_TO_MAIN_PHYSICAL_NEWSLETTERS. $nInfo->module . substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], '.')));
 $module_name = $nInfo->module;
 if(tep_not_null($nInfo->attachment_file))
 {
  if(is_file(PATH_TO_MAIN_PHYSICAL_EMAIL_ATTACHMENT.stripslashes($nInfo->attachment_file)))
  {
   $attachment_file=PATH_TO_MAIN_PHYSICAL_EMAIL_ATTACHMENT.stripslashes($nInfo->attachment_file);
  }
  else
  $attachment_file='';
 }
 else
  $attachment_file="";

 $attachment=$attachment_file;
 $module = new $module_name($nInfo->title,$nInfo->content,$attachment);
 $send_news_module='
 <tr>
  <td>
   <table border="0" cellspacing="0" cellpadding="2">
    <tr>
     <td valign="middle">'.tep_button('Send','class="btn btn-secondary"').'</td>
     <td valign="middle"><b>'.TEXT_PLEASE_WAIT.'</b></td>
    </tr>
   </table>
  </td>
 </tr>';
 set_time_limit(0);
 //flush();
 $module->send($nInfo->id);
 if($_POST['test_mode']=='test')
 {
  $messageStack->add_session(MESSAGE_SUCCESS_SEND, 'success');
  tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_NEWS_LETTER, 'page=' . $_GET['page'] . '&action=send&nID=' . $_GET['nID']));
 }
 $send_news_module.='
  <tr>
    <td><font color="#ff0000"><b>'.TEXT_FINISHED_SENDING_EMAILS.'</b></font></td>
  </tr>
  <tr>
    <td><a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_NEWS_LETTER, 'page=' . $_GET['page'] . '&nID=' . $_GET['nID']) . '">' .IMAGE_BACK . '</a>'. '</td>
  </tr>';
}
if(!tep_not_null($action) || $action=="delete")
{
 //////////////////////////////////
 $newsletters_query_raw = "select id, title, length(content) as content_length, module, date_added, date_sent, status, locked from " . NEWSLETTERS_TABLE . " order by date_added desc";
 $newsletters_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $newsletters_query_raw, $newsletters_query_numrows);
 $newsletters_query = tep_db_query($newsletters_query_raw);
 if(tep_db_num_rows($newsletters_query) > 0)
 {
  $alternate=1;
  while ($newsletters = tep_db_fetch_array($newsletters_query))
  {
   if ((!isset($_GET['nID']) || (isset($_GET['nID']) && ($_GET['nID'] == $newsletters['id']))) && !isset($nInfo) && (substr($action, 0, 3) != 'new'))
   {
    $nInfo = new objectInfo($newsletters);
   }
   if (isset($nInfo) && is_object($nInfo) && ($newsletters['id'] == $nInfo->id) )
   {
    $row_selected=' id="defaultSelected" class="table-secondary dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_NEWS_LETTER, 'page=' . $_GET['page'] . '&nID=' . $nInfo->id . '&action=preview') . '\'"' . "\n";
   }
   else
   {
    $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_NEWS_LETTER, 'page=' . $_GET['page'] . '&nID=' . $newsletters['id']) . '\'"' . "\n";
   }
   $alternate++;
   if($newsletters['status'] == '1')
    $sent_image=tep_image(PATH_TO_IMAGE.'tick.gif', 'True');
   else
    $sent_image=tep_image(PATH_TO_IMAGE.'cross.gif', 'False');
   if($newsletters['locked'] > 0)
    $status_image=tep_image(PATH_TO_IMAGE.'locked.gif', IMAGE_LOCKED);
   else
    $status_image=tep_image(PATH_TO_IMAGE.'unlocked.gif', IMAGE_UNLOCKED);
   $action_image=(isset($nInfo) && is_object($nInfo) && ($newsletters['id'] == $nInfo->id) ? tep_image(PATH_TO_IMAGE.'icon_arrow_right.gif', IMAGE_PREVIEW) : '<a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_NEWS_LETTER, 'page=' . $_GET['page'] . '&nID=' . $newsletters['id']) . '">' . tep_image(PATH_TO_IMAGE.'icon_info.gif', IMAGE_INFO) . '</a>');
   $actual_module_name=tep_db_output($newsletters['module']);
   switch($actual_module_name)
   {
    case 'all_jobseekers':
     $virtual_module_name="All Jobseekers";
     break;
    case 'all_recruiters':
     $virtual_module_name="All Recruiters";
     break;
    default:
     $virtual_module_name=$actual_module_name;
   }

   $template->assign_block_vars('news_letter', array( 'row_selected' => $row_selected,
    'action' => $action_image,
    'newsletters' => '<a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_NEWS_LETTER, 'page=' . $_GET['page'] . '&nID=' . $newsletters['id'] . '&action=preview') . '">' . tep_image(PATH_TO_IMAGE.'preview.gif', IMAGE_PREVIEW) . '</a>&nbsp;' . $newsletters['title'],
    'size' => number_format($newsletters['content_length']) . ' bytes',
    'module' => $virtual_module_name,
    'sent' => $sent_image,
    'status' => $status_image
    ));
  }
 }
}
//// right ///
$heading = array();
$contents = array();
switch ($action)
{
 case 'delete':
  $heading[] = array('text' => '<div class="text-primary font-weight-bold mb-1">' . $nInfo->title . '</div>');
  $contents = array('form' => tep_draw_form('newsletters', PATH_TO_ADMIN.FILENAME_ADMIN1_NEWS_LETTER, 'page=' . $_GET['page'] . '&nID=' . $nInfo->id . '&action=deleteconfirm'));
  $contents[] = array('text' =>  '<div class="mb-1 text-danger">' .TEXT_INFO_DELETE_INTRO. '</div>');
  $contents[] = array('text' => '<br><b>' . $nInfo->title . '</b>');
  $contents[] = array('align' => 'left', 'text' => '<br>'

   . tep_draw_submit_button_field('', IMAGE_DELETE,'class="btn btn-primary"') . '
   <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_NEWS_LETTER, 'page=' . $_GET['page'] . '&nID=' . $_GET['nID']) . '">' .IMAGE_CANCEL . '</a>');
 break;
 default:
  if (is_object($nInfo))
  {
   $heading[] = array('text' => '<div class="text-primary font-weight-bold mb-2">' . $nInfo->title . '</DIV>');
   if ($nInfo->locked > 0)
   {
    $contents[] = array('align' => 'left', 'text' => '<a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_NEWS_LETTER, 'page=' . $_GET['page'] . '&nID=' . $nInfo->id . '&action=new') . '">' .IMAGE_EDIT . '</a>
    <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_NEWS_LETTER, 'page=' . $_GET['page'] . '&nID=' . $nInfo->id . '&action=delete') . '">' .IMAGE_DELETE . '</a>
    <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_NEWS_LETTER, 'page=' . $_GET['page'] . '&nID=' . $nInfo->id . '&action=preview') . '">' .IMAGE_PREVIEW . '</a>
    <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_NEWS_LETTER, 'page=' . $_GET['page'] . '&nID=' . $nInfo->id . '&action=send') . '">' .IMAGE_SEND . '</a>
    <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_NEWS_LETTER, 'page=' . $_GET['page'] . '&nID=' . $nInfo->id . '&action=unlock') . '">' .IMAGE_UNLOCK . '</a>');
   }
   else
   {
    $contents[] = array('align' => 'left', 'text' => '<a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_NEWS_LETTER, 'page=' . $_GET['page'] . '&nID=' . $nInfo->id . '&action=preview') . '">' .IMAGE_PREVIEW . '</a> <a class="btn btn-warning" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_NEWS_LETTER, 'page=' . $_GET['page'] . '&nID=' . $nInfo->id . '&action=lock') . '">' . IMAGE_LOCK . '</a>');
   }
   $contents[] = array('text' => '<br>Date Added :' . tep_date_long($nInfo->date_added));
   if ($nInfo->status == '1')
    $contents[] = array('text' => 'Date Sent: ' . tep_date_long($nInfo->date_sent));
  }
 break;
}

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

if($action=='new')
{
 if(tep_not_null($nInfo->attachment_file))
 {
  if(is_file(PATH_TO_MAIN_PHYSICAL_EMAIL_ATTACHMENT.stripslashes($nInfo->attachment_file)))
  {
   $attachment_name=stripslashes(stripslashes(substr($nInfo->attachment_file,14)));
   $attachment_file=" <a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ATTACHMENT_DOWNLOAD,(tep_not_null($nID)?'n_id='.$nID:''))."'> Download </a>";
  }
  else
  $attachment_file='';
 }
 else
  $attachment_file="";

 $template->assign_vars(array(
  'HEADING_TITLE'=>HEADING_TITLE,
  'HOST_NAME'=>HOST_NAME,
  'TEXT_NEWSLETTER_MODULE' => TEXT_NEWSLETTER_MODULE,
  'TEXT_NEWSLETTER_TITLE'  => TEXT_NEWSLETTER_TITLE,
  'TEXT_NEWSLETTER_CONTENT'=> TEXT_NEWSLETTER_CONTENT,
  'INFO_TEXT_ATTACHMENT'   => TEXT_NEWSLETTER_ATTACHMENT,
  'INFO_TEXT_ATTACHMENT1'  => tep_draw_file_field('attachment', false).$attachment_file,

  'tep_draw_form'=>$_tep_draw_form,
  'news_module'=>tep_draw_pull_down_menu('TR_module', $modules_array,  $nInfo->module, 'class="form-control form-control-sm"'),
  'news_title'=>tep_draw_input_field('TR_title', $nInfo->title, 'class="form-control form-control-sm"', true ),
  'news_content'=>tep_draw_textarea_field('TR_content', 'soft', '50', '10', stripslashes($nInfo->content), 'class="form-control form-control-sm"', true),
  'news_letter_button1'=>(($form_action == 'insert') ? 

  tep_draw_submit_button_field('', IMAGE_SAVE,'class="btn btn-primary"') : tep_draw_submit_button_field('', IMAGE_UPDATE,'class="btn btn-primary"')). '

  <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_NEWS_LETTER, (isset($_GET['page']) ? 'page=' . $_GET['page'] . '&' : '') . (isset($_GET['nID']) ? 'nID=' . $_GET['nID'] : '')) . '">' .IMAGE_CANCEL . '</a>',
  'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
  'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
  'update_message'=>$messageStack->output()));
 $template->pparse('new_news_letter');
}
else if($action=='preview')
{
	$actual_module_name=tep_db_output($nInfo->module);
	switch($actual_module_name)
	{
		case 'all_jobseekers':
			$virtual_module_name="All Jobseekers";
			break;
		case 'all_recruiters':
			$virtual_module_name="All Recruiters";
			break;
		default:
			$virtual_module_name=$actual_module_name;
	}
 if(tep_not_null($nInfo->attachment_file))
 {
  if(is_file(PATH_TO_MAIN_PHYSICAL_EMAIL_ATTACHMENT.stripslashes($nInfo->attachment_file)))
  {
   $attachment_name=stripslashes(stripslashes(substr($nInfo->attachment_file,14)));
   $attachment_file=" <a href='".tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ATTACHMENT_DOWNLOAD,(tep_not_null($nID)?'n_id='.$nID:''))."'> Download </a>";
  }
  else
  $attachment_file='';
 }
 else
  $attachment_file="";

 $template->assign_vars(array(
  'HEADING_TITLE'=>HEADING_TITLE,
  'HOST_NAME'=>HOST_NAME,
  'TEXT_NEWSLETTER_MODULE'=>TEXT_NEWSLETTER_MODULE,
  'TEXT_NEWSLETTER_TITLE'=>TEXT_NEWSLETTER_TITLE,
  'TEXT_NEWSLETTER_CONTENT'=>TEXT_NEWSLETTER_CONTENT,
  'INFO_TEXT_ATTACHMENT'   => TEXT_NEWSLETTER_ATTACHMENT,
  'INFO_TEXT_ATTACHMENT1'  => $attachment_file,

  'preview_news_module'=>$virtual_module_name,
  'preview_news_title'=>$nInfo->title,
  'preview_news_content'=>stripslashes($nInfo->content),
  'preview_news_letter_button'=>'<a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_NEWS_LETTER, 'page=' . $_GET['page'] . '&nID=' . $_GET['nID']) . '">' . IMAGE_BACK . '</a>',
  'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
  'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
  'update_message'=>$messageStack->output()));
 $template->pparse('preview_news_letter');
}
else if($action=='send')
{
 $template->assign_vars(array(
  'HEADING_TITLE'=>HEADING_TITLE,
  'HOST_NAME'=>HOST_NAME,
  'send_news_module'=>$send_news_module,
  'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
  'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
  'update_message'=>$messageStack->output()));
 $template->pparse('send_news_letter');
}
else if($action=='confirm_send')
{
 $template->assign_vars(array(
  'HEADING_TITLE'=>HEADING_TITLE,
  'HOST_NAME'=>HOST_NAME,
  'send_news_module'=>$send_news_module,
  'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
  'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
  'update_message'=>$messageStack->output()));
// $template->pparse('send_news_letter');
}
else
{
 $template->assign_vars(array(
  'HOST_NAME'=>HOST_NAME,
  'TABLE_HEADING_NEWSLETTERS'=>TABLE_HEADING_NEWSLETTERS,
  'TABLE_HEADING_SIZE'=>TABLE_HEADING_SIZE,
  'TABLE_HEADING_MODULE'=>TABLE_HEADING_MODULE,
  'TABLE_HEADING_SENT'=>TABLE_HEADING_SENT,
  'TABLE_HEADING_STATUS'=>TABLE_HEADING_STATUS,
  'TABLE_HEADING_ACTION'=>TABLE_HEADING_ACTION,
  'count_rows'=>$newsletters_split->display_count($newsletters_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_NEWSLETTERS),
  'no_of_pages'=>$newsletters_split->display_links($newsletters_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']),
  'new_news_letter_button'=>'<a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_NEWS_LETTER, 'action=new') . '"><i class="bi bi-plus-lg me-2"></i>' .IMAGE_NEW . '</a>',
 'jobseeker_newsletter'=>'<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_NEWSLETTERS, 'newsletter_for=jobseeker') . '"><input type="button" class="btn btn-new" value="Jobseeker Newsletter"></a>',
 'recruiter_newsletter'=>'<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_LIST_OF_NEWSLETTERS, 'newsletter_for=recruiter') . '"><input type="button" class="btn btn-new" value="Recuiter Newsletter"></a>',

  'HEADING_TITLE'=>HEADING_TITLE,
  'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
  'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
  'update_message'=>$messageStack->output()));
 $template->pparse('news_letter');
}
?>