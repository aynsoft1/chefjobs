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
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_JOBALERTS);
$template->set_filenames(array('job_alert' => 'admin1_job_alerts.htm'));
include_once(FILENAME_ADMIN_BODY);

$action = (isset($_GET['action']) ? $_GET['action'] : '');

if ($action!="")
{
 switch ($action)
	{
  case 'confirm_delete':
   $id = tep_db_prepare_input($_GET['id']);
   tep_db_query("delete from " . JOB_ALERT_DIRECT_TABLE . " where id = '" . (int)$id . "'");
			$messageStack->add_session(MESSAGE_SUCCESS_DELETED, 'success');
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_JOBALERTS, 'page=' . $_GET['page']));
  break;
  case 'insert':
  case 'save':
   $job_alert_email=tep_db_prepare_input($_POST['TR_job_alert_email']);
   $addedon = tep_db_prepare_input($_POST['addedon']);
   $sql_data_array['email_id'] = $job_alert_email;
   $sql_data_array['inserted'] = $addedon;

			if($action=='insert')
			{
				if($row_chek=getAnyTableWhereData(JOB_ALERT_DIRECT_TABLE,"email_id='".tep_db_input($job_alert_email)."'",'id'))
				{
					$messageStack->add(MESSAGE_NAME_ERROR, 'error');
				}
				else
				{
     tep_db_perform(JOB_ALERT_DIRECT_TABLE, $sql_data_array);
     $row_id_check=getAnyTableWhereData(JOB_ALERT_DIRECT_TABLE,"1 order by id desc limit 0,1","id");
     $id = $row_id_check['id'];
  			$messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');
					tep_redirect(FILENAME_ADMIN1_JOBALERTS);
				}
			}
			else
			{
    $id=(int)$_GET['id'];
				if($row_chek=getAnyTableWhereData(JOB_ALERT_DIRECT_TABLE,"email_id='".tep_db_input($job_alert_email)."' and id!='$id'",'id'))
				{
					$messageStack->add(MESSAGE_NAME_ERROR, 'error');
				}
				else
				{
     tep_db_perform(JOB_ALERT_DIRECT_TABLE, $sql_data_array, 'update', "id = '" . (int)$id . "'");
  			$messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
					tep_redirect(FILENAME_ADMIN1_JOBALERTS.'?page='.$_GET['page'].'&id='.$id);
				}
			}
  break;
 }
}
///////////// Middle Values ////
$job_alert_query_raw="select id, email_id, inserted from " . JOB_ALERT_DIRECT_TABLE ." order by inserted";
$job_alert_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $job_alert_query_raw, $job_alert_query_numrows);
$job_alert_query = tep_db_query($job_alert_query_raw);
if(tep_db_num_rows($job_alert_query) > 0)
{
 $alternate=1;
 while ($job_alert = tep_db_fetch_array($job_alert_query))
 {
  if ((!isset($_GET['id']) || (isset($_GET['id']) && ($_GET['id'] == $job_alert['id']))) && !isset($jaInfo) && (substr($action, 0, 3) != 'new'))
  {
   $jaInfo = new objectInfo($job_alert);
  }
  if ( (isset($jaInfo) && is_object($jaInfo)) && ($job_alert['id'] == $jaInfo->id) )
  {
   $row_selected=' id="defaultSelected" class="table-secondary dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . FILENAME_ADMIN1_JOBALERTS . '?page='.$_GET['page'].'&id=' . $jaInfo->id . '&action=edit\'"';
  }
  else
  {
   $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . FILENAME_ADMIN1_JOBALERTS . '?page='.$_GET['page'].'&id=' . $job_alert['id'] . '\'"';
  }
  $alternate++;
  if ( (isset($jaInfo) && is_object($jaInfo)) && ($job_alert['id'] == $jaInfo->id) )
  {
   $action_image=tep_image(PATH_TO_IMAGE.'icon_arrow_right.gif',IMAGE_EDIT);
  }
  else
  {
   $action_image='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_JOBALERTS, 'page='.$_GET['page'].'&id=' . $job_alert['id']) . '">'.tep_image(PATH_TO_IMAGE.'icon_info.gif',IMAGE_INFO).'</a>';
  }
  $template->assign_block_vars('job_alert', array( 'row_selected' => $row_selected,
   'action' => $action_image,
   'email' => tep_db_output($job_alert['email_id']),
   ));
 }
}

//// for right side
$ADMIN_RIGHT_HTML="";

$heading = array();
$contents = array();
switch ($action)
{
 case 'new':
 case 'insert':
 case 'save':
		$heading[] = array('text' => '<b>'.TEXT_INFO_HEADING_JOB_ALERT.'</b>');
  $contents = array('form' => tep_draw_form('job_alert', PATH_TO_ADMIN.FILENAME_ADMIN1_JOBALERTS, 'action=insert','post',' onsubmit="return ValidateForm(this)"'));
		$contents[] = array('text' => TEXT_INFO_NEW_INTRO);
		$contents[] = array('text' => '<br>'.TEXT_INFO_JOB_ALERT_EMAIL.'<br>'.tep_draw_input_field('TR_job_alert_email', $_POST['TR_job_alert_email'], '' ));
		$contents[] = array('align' => 'left', 'text' => '<br>'.tep_image_submit(PATH_TO_BUTTON.'button_insert.gif', IMAGE_INSERT).'&nbsp;<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_JOBALERTS).'">'.tep_image_button(PATH_TO_BUTTON.'button_cancel.gif', IMAGE_CANCEL).'</a>');
  break;
 case 'delete':
  $heading[] = array('text' => '<div class="list-group"><div class="font-weight-bold">' . $jaInfo->email_id . '
                      <div class="h6">'.TEXT_DELETE_INTRO.'</div>                      
                      </div></div>');

  $contents = array('form' => tep_draw_form('user_category_delete', PATH_TO_ADMIN.FILENAME_ADMIN1_JOBALERTS, 'page=' . $_GET['page'] . '&id=' . $nInfo->id . '&action=deleteconfirm'));
 
  $contents[] = array('align' => 'left', 'text' => '
  <div class="py-2">   
  <div class="text-justify font-weight-bold mb-1">'.$jaInfo->email_id.'</div> 
  <a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_JOBALERTS, 'page=' . $_GET['page'] . '&id=' . $_GET['id'].'&action=confirm_delete') . '">Confirm</a>
      
  <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_JOBALERTS, 'page=' . $_GET['page'] . '&id=' . $_GET['id']) . '">Cancel</a>
 
  </div>');
 break;
 default:
  if (isset($jaInfo) && is_object($jaInfo))
		{
   $heading[] = array('text' => '<div class="list-group"><div class="font-weight-bold">
                              '.TEXT_INFO_HEADING_JOB_ALERT.'
                              <div class="h6">'.tep_db_output($jaInfo->email_id).'</div>
                              </div></div>');
  //  $contents[] = array('text' => tep_db_output($jaInfo->email_id));
   $contents[] = array('align' => 'left', 'text' => '
              <div class="py-2">
              <a class="btn btn-danger" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_JOBALERTS, 'page=' . $_GET['page'] .'&id=' . $jaInfo->id . '&action=delete') . '">
              Delete
              </a>
              <div>'.TEXT_INFO_ACTION.'</div>
              </div>');
  //  $contents[] = array('text' => '<br>'.TEXT_INFO_ACTION);
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
/////
$template->assign_vars(array(
 'TABLE_HEADING_JOB_ALERT_EMAIL'=>TABLE_HEADING_JOB_ALERT_EMAIL,
 'TABLE_HEADING_ACTION'=>TABLE_HEADING_ACTION,
 'count_rows'=>$job_alert_split->display_count($job_alert_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_JOB_ALERTS),
 'no_of_pages'=>$job_alert_split->display_links($job_alert_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']),
 //'new_button'=>'<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_JOBALERTS, 'page=' . $_GET['page'] .'&action=new') . '">'.tep_image_button(PATH_TO_BUTTON.'button_new.gif',IMAGE_NEW).'</a>&nbsp;&nbsp;',
 'HEADING_TITLE'=>HEADING_TITLE,
 'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
 'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('job_alert');
?>