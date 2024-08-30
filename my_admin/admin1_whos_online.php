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
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_WHOS_ONLINE);
$template->set_filenames(array('online' => 'admin1_whos_online.htm'));
include_once(FILENAME_ADMIN_BODY);

$xx_mins_ago = (time() - LOGOUT_TIME);
$action = (isset($_GET['action']) ? $_GET['action'] : '');

if (tep_not_null($action)) 
{
 switch ($action) 
	{
  case 'delete':
  // remove entries that have expired
  tep_db_query("delete from " . WHOS_ONLINE_TABLE . " where time_last_click < '" . $xx_mins_ago . "'");
		$messageStack->add_session(SUCCESS_DELETED, 'success');
		tep_redirect(FILENAME_ADMIN1_WHOS_ONLINE);
 }
}

$whos_online_query_raw="select * from " . WHOS_ONLINE_TABLE ." order by time_entry desc";
$whos_online_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $whos_online_query_raw, $whos_online_query_numrows);
$whos_online_query = tep_db_query($whos_online_query_raw);
if(tep_db_num_rows($whos_online_query) > 0)
{
 $alternate=1;
 while ($whos_online = tep_db_fetch_array($whos_online_query)) 
 {
  $time_online = (time() - $whos_online['time_entry']);
  if ((!isset($_GET['info']) || (isset($_GET['info']) && ($_GET['info'] == $whos_online['session_id']))) && !isset($info)) 
  {
   $info = $whos_online['session_id'];
  }
  if ($whos_online['session_id'] == $info) 
  {
   $row_selected=' id="defaultSelected" class="table-secondary dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)"';
  } 
  else 
  {
   $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_WHOS_ONLINE, tep_get_all_get_params(array('info', 'action')) . 'info=' . $whos_online['session_id'], 'NONSSL') . '\'"';
  }
  $alternate++;
  switch($whos_online['user'])
  {
   case "Administrator":
    $row=getAnyTableWhereData(ADMIN_TABLE,"admin_id='".$_SESSION['sess_adminid']."'","concat(admin_firstname,' ',admin_lastname) as full_name");
    $full_name=stripslashes($row['full_name']);
    break;
   case "Jobseeker":
    $row=getAnyTableWhereData(JOBSEEKER_TABLE,"jobseeker_id='".$whos_online['user_id']."'","concat(jobseeker_first_name,' ',jobseeker_last_name) as full_name");
    $full_name=stripslashes($row['full_name']);
    break;
   case "Recruiter":
    $row=getAnyTableWhereData(RECRUITER_TABLE,"recruiter_id='".$whos_online['user_id']."'","concat(recruiter_first_name,' ',recruiter_last_name) as full_name");
    $full_name=stripslashes($row['full_name']);
    break;
   case "Anonymous":
    $full_name="";
    break;
  }
  if (preg_match('/^(.*)' . session_name() . '=[a-f,0-9]+[&]*(.*)/i', $whos_online['last_page_url'], $array)) 
  { 
   $last_url=tep_db_output($array[1] . $array[2]);
  }
  else
  {
   $last_url=tep_db_output($whos_online['last_page_url']);
  }
  $template->assign_block_vars('online', array( 'row_selected' => $row_selected,
     'online' => tep_db_output(gmdate('H:i:s', $time_online)),
     'name' => tep_db_output($full_name),
     'ip_address' => tep_db_output($whos_online['ip_address']),
     'entry_time' => tep_db_output(date('d/m/Y H:i:s', $whos_online['time_entry'])),
     'last_click' => tep_db_output(date('d/m/Y H:i:s', $whos_online['time_last_click'])),
     'last_url' => $last_url,
     'user' => tep_db_output($whos_online['user'])
     ));
 }
}
$ADMIN_RIGHT_HTML='';
$template->assign_vars(array(
 'TABLE_HEADING_ONLINE'=>TABLE_HEADING_ONLINE,
 'TABLE_HEADING_USER'=>TABLE_HEADING_USER,
 'TABLE_HEADING_FULL_NAME'=>TABLE_HEADING_FULL_NAME,
 'TABLE_HEADING_IP_ADDRESS'=>TABLE_HEADING_IP_ADDRESS,
 'TABLE_HEADING_ENTRY_TIME'=>TABLE_HEADING_ENTRY_TIME,
 'TABLE_HEADING_LAST_CLICK'=>TABLE_HEADING_LAST_CLICK,
 'TABLE_HEADING_LAST_PAGE_URL'=>TABLE_HEADING_LAST_PAGE_URL,
 'TEXT_DELETE_IDLE_USERS'=>'<a href="'.tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_WHOS_ONLINE,'action=delete').'">'.TEXT_DELETE_IDLE_USERS.'</a>',
 'count_rows'=>$whos_online_split->display_count($whos_online_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_ONLINE_USERS),
 'no_of_pages'=>$whos_online_split->display_links($whos_online_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']),
 'HEADING_TITLE'=>HEADING_TITLE,
 'RIGHT_BOX_WIDTH'=>0,
 'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('online');
?>