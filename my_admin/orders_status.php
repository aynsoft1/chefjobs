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
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_ADMIN_ORDERS_STATUS);
$template->set_filenames(array('order_status' => 'orders_status.htm'));
include_once(FILENAME_ADMIN_BODY);

$action = (isset($_GET['action']) ? $_GET['action'] : '');

  $action = (isset($_GET['action']) ? $_GET['action'] : '');

  if (tep_not_null($action)) {
    switch ($action) {
      case 'insert':
      case 'save':
        if (isset($_GET['oID'])) $orders_status_id = tep_db_prepare_input($_GET['oID']);

        $languages = tep_get_languages();
        for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
          $orders_status_name_array = $_POST['orders_status_name'];
          $language_id = $languages[$i]['id'];

          $sql_data_array = array('orders_status_name' => tep_db_prepare_input($orders_status_name_array[$language_id]));

          if ($action == 'insert') {
            if (empty($orders_status_id)) {
              $next_id_query = tep_db_query("select max(orders_status_id) as orders_status_id from " . ORDER_STATUS_TABLE . "");
              $next_id = tep_db_fetch_array($next_id_query);
              $orders_status_id = $next_id['orders_status_id'] + 1;
            }

            $insert_sql_data = array('orders_status_id' => $orders_status_id,
                                     'language_id' => $language_id);

            $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

            tep_db_perform(ORDER_STATUS_TABLE, $sql_data_array);
          } elseif ($action == 'save') {
            tep_db_perform(ORDER_STATUS_TABLE, $sql_data_array, 'update', "orders_status_id = '" . (int)$orders_status_id . "' and language_id = '" . (int)$language_id . "'");
          }
        }

        if (isset($_POST['default']) && ($_POST['default'] == 'on')) {
          tep_db_query("update " . CONFIGURATION_TABLE . " set configuration_value = '" . tep_db_input($orders_status_id) . "' where configuration_name = 'DEFAULT_ORDERS_STATUS_ID'");
        }

        tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $orders_status_id));
        break;
      case 'deleteconfirm':
        $oID = tep_db_prepare_input($_GET['oID']);

        $orders_status_query = tep_db_query("select configuration_value from " . CONFIGURATION_TABLE . " where configuration_name = 'DEFAULT_ORDERS_STATUS_ID'");
        $orders_status = tep_db_fetch_array($orders_status_query);

        if ($orders_status['configuration_value'] == $oID) {
          tep_db_query("update " . CONFIGURATION_TABLE . " set configuration_value = '' where configuration_name = 'DEFAULT_ORDERS_STATUS_ID'");
        }

        tep_db_query("delete from " . ORDER_STATUS_TABLE . " where orders_status_id = '" . tep_db_input($oID) . "'");

        tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_ORDERS_STATUS, 'page=' . $_GET['page']));
        break;
      case 'delete':
        $oID = tep_db_prepare_input($_GET['oID']);

        $status_query = tep_db_query("select count(*) as count from " . ORDER_TABLE . " where orders_status = '" . (int)$oID . "'");
        $status = tep_db_fetch_array($status_query);

        $remove_status = true;
        if ($oID == DEFAULT_ORDERS_STATUS_ID) {
          $remove_status = false;
          $messageStack->add(ERROR_REMOVE_DEFAULT_ORDER_STATUS, 'error');
        } elseif ($status['count'] > 0) {
          $remove_status = false;
          $messageStack->add(ERROR_STATUS_USED_IN_ORDERS, 'error');
        } else {
          $history_query = tep_db_query("select count(*) as count from " . ORDER_TABLE . " where orders_status = '" . (int)$oID . "'");
          $history = tep_db_fetch_array($history_query);
          if ($history['count'] > 0) {
            $remove_status = false;
            $messageStack->add(ERROR_STATUS_USED_IN_HISTORY, 'error');
          }
        }
        break;
    }
  }

  $orders_status_query_raw = "select orders_status_id, orders_status_name from " . ORDER_STATUS_TABLE . " where language_id = '" . (int)$languages_id . "' order by orders_status_id";
  $orders_status_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $orders_status_query_raw, $orders_status_query_numrows);
  $orders_status_query = tep_db_query($orders_status_query_raw);
  if(tep_db_num_rows($orders_status_query) > 0)
  {
   $alternate=1;
   while ($orders_status = tep_db_fetch_array($orders_status_query))
   {
    if ((!isset($_GET['oID']) || (isset($_GET['oID']) && ($_GET['oID'] == $orders_status['orders_status_id']))) && !isset($oInfo) && (substr($action, 0, 3) != 'new'))
    {
     $oInfo = new objectInfo($orders_status);
    }
    if (isset($oInfo) && is_object($oInfo) && ($orders_status['orders_status_id'] == $oInfo->orders_status_id))
    {
     $row_selected=' id="defaultSelected" class="table-secondary dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_status_id . '&action=edit') . '\'"';
    }
    else
    {
     $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $orders_status['orders_status_id']) . '\'"';
    }
    $alternate++;

    if (DEFAULT_ORDERS_STATUS_ID == $orders_status['orders_status_id'])
    {
     $name='<b>' . tep_db_output($orders_status['orders_status_name']) . ' (' . TEXT_DEFAULT . ')</b>';
    }
    else
    {
     $name=tep_db_output($orders_status['orders_status_name']);
    }
    if (isset($oInfo) && is_object($oInfo) && ($orders_status['orders_status_id'] == $oInfo->orders_status_id))
    {
     $action_image= tep_image(PATH_TO_IMAGE.'icon_arrow_right.gif', '');
    }
    else
    {
     $action_image= '<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $orders_status['orders_status_id']) . '">' . tep_image(PATH_TO_IMAGE.'icon_info.gif', IMAGE_ICON_INFO) . '</a>';
    }
    $template->assign_block_vars('order_status', array( 'row_selected' => $row_selected,
    'name' => $name,
    'action' => $action_image,
    'row_selected' => $row_selected
    ));
 }
}
$new_button='';
  if (empty($action)) {
                  $new_button.= '<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_ORDERS_STATUS, 'page=' . $_GET['page'] . '&action=new') . '">' . tep_image_button(PATH_TO_BUTTON.'button_insert.gif', IMAGE_INSERT) . '</a>';
  }

  $heading = array();
  $contents = array();

  switch ($action) {
    case 'new':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_ORDERS_STATUS . '</b>');

      $contents = array('form' => tep_draw_form('status', PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_ORDERS_STATUS, 'page=' . $_GET['page'] . '&action=insert'));
      $contents[] = array('text' => TEXT_INFO_INSERT_INTRO);

      $orders_status_inputs_string = '';
      $languages = tep_get_languages();
      for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
        $orders_status_inputs_string .= '<br>' . tep_image(PATH_TO_LANGUAGE . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('orders_status_name[' . $languages[$i]['id'] . ']');
      }

      $contents[] = array('text' => '<br>' . TEXT_INFO_ORDERS_STATUS_NAME . $orders_status_inputs_string);
      $contents[] = array('text' => '<br>' . tep_draw_checkbox_field('default') . ' ' . TEXT_SET_DEFAULT);
      $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit(PATH_TO_BUTTON.'button_insert.gif', IMAGE_INSERT) . ' <a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_ORDERS_STATUS, 'page=' . $_GET['page']) . '">' . tep_image_button(PATH_TO_BUTTON.'button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;
    case 'edit':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_ORDERS_STATUS . '</b>');

      $contents = array('form' => tep_draw_form('status', PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_status_id  . '&action=save'));
      $contents[] = array('text' => TEXT_INFO_EDIT_INTRO);

      $orders_status_inputs_string = '';
      $languages = tep_get_languages();
      for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
        $orders_status_inputs_string .= '<br>' . tep_image(PATH_TO_LANGUAGE . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('orders_status_name[' . $languages[$i]['id'] . ']', tep_get_orders_status_name($oInfo->orders_status_id, $languages[$i]['id']));
      }

      $contents[] = array('text' => '<br>' . TEXT_INFO_ORDERS_STATUS_NAME . $orders_status_inputs_string);
      if (DEFAULT_ORDERS_STATUS_ID != $oInfo->orders_status_id) $contents[] = array('text' => '<br>' . tep_draw_checkbox_field('default') . ' ' . TEXT_SET_DEFAULT);
      $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit(PATH_TO_BUTTON.'button_update.gif', IMAGE_UPDATE) . ' <a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_status_id) . '">' . tep_image_button(PATH_TO_BUTTON.'button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;
    case 'delete':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_ORDERS_STATUS . '</b>');

      $contents = array('form' => tep_draw_form('status', PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_status_id  . '&action=deleteconfirm'));
      $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
      $contents[] = array('text' => '<br><b>' . $oInfo->orders_status_name . '</b>');
      if ($remove_status) $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit(PATH_TO_BUTTON.'button_delete.gif', IMAGE_DELETE) . ' <a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_status_id) . '">' . tep_image_button(PATH_TO_BUTTON.'button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;
    default:
      if (isset($oInfo) && is_object($oInfo)) {
        $heading[] = array('text' => '<b>' . $oInfo->orders_status_name . '</b>');

        $contents[] = array('align' => 'center', 'text' => '<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_status_id . '&action=edit') . '">' . tep_image_button(PATH_TO_BUTTON.'button_edit.gif', IMAGE_EDIT) . '</a> <a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_status_id . '&action=delete') . '">' . tep_image_button(PATH_TO_BUTTON.'button_delete.gif', IMAGE_DELETE) . '</a>');

        $orders_status_inputs_string = '';
        $languages = tep_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
          $orders_status_inputs_string .= '<br>' . tep_image(PATH_TO_LANGUAGE . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_get_orders_status_name($oInfo->orders_status_id, $languages[$i]['id']);
        }

        $contents[] = array('text' => $orders_status_inputs_string);
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
 'TABLE_HEADING_ORDERS_STATUS'=>TABLE_HEADING_ORDERS_STATUS,
 'TABLE_HEADING_ACTION'=>TABLE_HEADING_ACTION,
 'count_rows'=>$orders_status_split->display_count($orders_status_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_ORDERS_STATUS),
 'no_of_pages'=>$orders_status_split->display_links($orders_status_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']),
 'new_button'=>$new_button,
 'HEADING_TITLE'=>HEADING_TITLE,
 'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
 'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('order_status');
?>