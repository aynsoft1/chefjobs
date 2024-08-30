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
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_ADMIN1_ADMIN_CURRENCIES);
$template->set_filenames(array('currencies' => 'currencies.htm'));
include_once(FILENAME_ADMIN_BODY);

$action = (isset($_GET['action']) ? $_GET['action'] : '');

if ($action!="")
{
 switch ($action)
	{
  case 'insert':
  case 'save':
   if (isset($_GET['cID'])) $currency_id = tep_db_prepare_input($_GET['cID']);
   $title = tep_db_prepare_input($_POST['title']);
   $code = tep_db_prepare_input($_POST['code']);
   $symbol_left = tep_db_prepare_input($_POST['symbol_left']);
   $symbol_right = tep_db_prepare_input($_POST['symbol_right']);
   $decimal_point = (tep_not_null($_POST['decimal_point'])?tep_db_prepare_input($_POST['decimal_point']):'.');
   $thousands_point = tep_db_prepare_input($_POST['thousands_point']);
   $decimal_places = (tep_not_null($_POST['decimal_places'])?tep_db_prepare_input($_POST['decimal_places']):'2');
   $value = tep_db_prepare_input($_POST['value']);
   $sql_data_array = array('title' => $title,
                           'code' => $code,
                           'symbol_left' => $symbol_left,
                           'symbol_right' => $symbol_right,
                           'decimal_point' => $decimal_point,
                           'thousands_point' => $thousands_point,
                           'decimal_places' => $decimal_places,
                           'value' => $value);

   if ($action == 'insert')
   {
    tep_db_perform(CURRENCY_TABLE, $sql_data_array);
    $row_id_check=getAnyTableWhereData(CURRENCY_TABLE,"1 order by currencies_id desc limit 0,1","currencies_id");
    $currency_id = $row_id_check['currencies_id'];
   }
   elseif ($action == 'save')
   {
    tep_db_perform(CURRENCY_TABLE, $sql_data_array, 'update', "currencies_id = '" . (int)$currency_id . "'");
   }
   if (isset($_POST['default']) && ($_POST['default'] == 'on'))
   {
    tep_db_query("update " . CONFIGURATION_TABLE . " set configuration_value = '" . tep_db_input($code) . "' where configuration_name = 'DEFAULT_CURRENCY'");
   }
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $currency_id));
  break;
  case 'deleteconfirm':
   $currencies_id = tep_db_prepare_input($_GET['cID']);

   $currency_query = tep_db_query("select currencies_id from " . CURRENCY_TABLE . " where code = '" . DEFAULT_CURRENCY . "'");
   $currency = tep_db_fetch_array($currency_query);

   if ($currency['currencies_id'] == $currencies_id)
   {
    tep_db_query("update " . CONFIGURATION_TABLE . " set configuration_value = '' where configuration_key = 'DEFAULT_CURRENCY'");
   }

   tep_db_query("delete from " . CURRENCY_TABLE . " where currencies_id = '" . (int)$currencies_id . "'");

   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_CURRENCIES, 'page=' . $_GET['page']));
  break;
  case 'update':
   $server_used = CURRENCY_SERVER_PRIMARY;

   $currency_query = tep_db_query("select currencies_id, code, title from " . CURRENCY_TABLE);
   while ($currency = tep_db_fetch_array($currency_query))
   {
    $quote_function = 'quote_' . CURRENCY_SERVER_PRIMARY . '_currency';
    $rate = $quote_function($currency['code']);
    if (empty($rate) && (tep_not_null(CURRENCY_SERVER_BACKUP)))
    {
     $messageStack->add_session(sprintf(WARNING_PRIMARY_SERVER_FAILED, CURRENCY_SERVER_PRIMARY, $currency['title'], $currency['code']), 'warning');
     $quote_function = 'quote_' . CURRENCY_SERVER_BACKUP . '_currency';
     $rate = $quote_function($currency['code']);
     $server_used = CURRENCY_SERVER_BACKUP;
    }
    if (tep_not_null($rate))
    {
     tep_db_query("update " . CURRENCY_TABLE . " set value = '" . $rate . "', last_updated = now() where currencies_id = '" . (int)$currency['currencies_id'] . "'");
     $messageStack->add_session(sprintf(TEXT_INFO_CURRENCY_UPDATED, $currency['title'], $currency['code'], $server_used), 'success');
    }
    else
    {
     $messageStack->add_session(sprintf(ERROR_CURRENCY_INVALID, $currency['title'], $currency['code'], $server_used), 'error');
    }
   }
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $_GET['cID']));
  break;
  case 'delete':
   $currencies_id = tep_db_prepare_input($_GET['cID']);
   $currency_query = tep_db_query("select code from " . CURRENCY_TABLE . " where currencies_id = '" . (int)$currencies_id . "'");
   $currency = tep_db_fetch_array($currency_query);
   $remove_currency = true;
   if ($currency['code'] == DEFAULT_CURRENCY)
   {
    $remove_currency = false;
    $messageStack->add(ERROR_REMOVE_DEFAULT_CURRENCY, 'error');
   }
  break;
 }
}
///////////// Middle Values
$currency_query_raw="select currencies_id, title, code, symbol_left, symbol_right, decimal_point, thousands_point, decimal_places, last_updated, value from " . CURRENCY_TABLE ." order by title";
$currency_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $currency_query_raw, $currency_query_numrows);
$currency_query = tep_db_query($currency_query_raw);
if(tep_db_num_rows($currency_query) > 0)
{
 $alternate=1;
 while ($currency = tep_db_fetch_array($currency_query))
 {
  if ((!isset($_GET['cID']) || (isset($_GET['cID']) && ($_GET['cID'] == $currency['currencies_id']))) && !isset($cInfo) && (substr($action, 0, 3) != 'new'))
  {
   $cInfo = new objectInfo($currency);
  }
  if (isset($cInfo) && is_object($cInfo) && ($currency['currencies_id'] == $cInfo->currencies_id) )
  {
   $row_selected=' id="defaultSelected" class="table-secondary dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->currencies_id . '&action=edit') . '\'"';
  }
  else
  {
   $row_selected=' class="dataTableRow'.($alternate%2==1?'1':'2').'" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $currency['currencies_id']) . '\'"';
  }
  $alternate++;
  if (DEFAULT_CURRENCY == $currency['code'])
  {
   $title='<b>' . tep_db_output($currency['title']) . ' (' . TEXT_DEFAULT . ')</b>';
  }
  else
  {
   $title=tep_db_output($currency['title']);
  }
  if (isset($cInfo) && is_object($cInfo) && ($currency['currencies_id'] == $cInfo->currencies_id) )
  {
   $action_image=tep_image(PATH_TO_IMAGE.'icon_arrow_right.gif',IMAGE_EDIT);
  }
  else
  {
   $action_image='<a href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_CURRENCIES, 'page='.$_GET['page'].'&cID=' . $currency['currencies_id']) . '">'.tep_image(PATH_TO_IMAGE.'icon_info.gif',IMAGE_INFO).'</a>';
  }
  $template->assign_block_vars('currency', array( 'row_selected' => $row_selected,
   'title' => $title,
   'code' => tep_db_output($currency['code']),
   'value' => tep_db_output(number_format($currency['value'], 8)),
   'action' => $action_image,
   'row_selected' => $row_selected
   ));
 }
}
$new_button='';
if(empty($action))
{
 if (CURRENCY_SERVER_PRIMARY)
 {
  $new_button.='<a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->currencies_id . '&action=update') . '">' . IMAGE_UPDATE_CURRENCIES . '</a>&nbsp;&nbsp;';
 }
 $new_button.='<a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->currencies_id . '&action=new') . '"><i class="bi bi-plus-lg me-2"></i>' .IMAGE_NEW_CURRENCY . '</a>';
}

//// for right side
$ADMIN_RIGHT_HTML="";

$heading = array();
$contents = array();
  switch ($action) {
    case 'new':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_CURRENCY . '</b>');

      $contents = array('form' => tep_draw_form('currencies', PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_CURRENCIES, 'page=' . $_GET['page'] . (isset($cInfo) ? '&cID=' . $cInfo->currencies_id : '') . '&action=insert'));
      $contents[] = array('text' => TEXT_INFO_INSERT_INTRO);
      $contents[] = array('text' => '<br>' . TEXT_INFO_CURRENCY_TITLE . '<br>' . tep_draw_input_field('title', '', 'class="form-control form-control-sm"'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_CURRENCY_CODE . '<br>' . tep_draw_input_field('code', '', 'class="form-control form-control-sm"'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_CURRENCY_SYMBOL_LEFT . '<br>' . tep_draw_input_field('symbol_left', '', 'class="form-control form-control-sm"'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_CURRENCY_SYMBOL_RIGHT . '<br>' . tep_draw_input_field('symbol_right', '', 'class="form-control form-control-sm"'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_CURRENCY_DECIMAL_POINT . '<br>' . tep_draw_input_field('decimal_point', '', 'class="form-control form-control-sm"'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_CURRENCY_THOUSANDS_POINT . '<br>' . tep_draw_input_field('thousands_point', '', 'class="form-control form-control-sm"'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_CURRENCY_DECIMAL_PLACES . '<br>' . tep_draw_input_field('decimal_places', '', 'class="form-control form-control-sm"'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_CURRENCY_VALUE . '<br>' . tep_draw_input_field('value', '', 'class="form-control form-control-sm"'));
      $contents[] = array('text' => '<br>' . tep_draw_checkbox_field('default') . ' ' . TEXT_INFO_SET_AS_DEFAULT);
      $contents[] = array('align' => 'left', 'text' => '<br>' . tep_draw_submit_button_field('', IMAGE_INSERT,'class="btn btn-primary"') . ' <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $_GET['cID']) . '">' .IMAGE_CANCEL . '</a>');
      break;
    case 'edit':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_CURRENCY . '</b>');

      $contents = array('form' => tep_draw_form('currencies', PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->currencies_id . '&action=save'));
      $contents[] = array('text' => TEXT_INFO_EDIT_INTRO);
      $contents[] = array('text' => '<br>' . TEXT_INFO_CURRENCY_TITLE . '<br>' . tep_draw_input_field('title', $cInfo->title, 'class="form-control form-control-sm"'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_CURRENCY_CODE . '<br>' . tep_draw_input_field('code', $cInfo->code, 'class="form-control form-control-sm"'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_CURRENCY_SYMBOL_LEFT . '<br>' . tep_draw_input_field('symbol_left', $cInfo->symbol_left, 'class="form-control form-control-sm"'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_CURRENCY_SYMBOL_RIGHT . '<br>' . tep_draw_input_field('symbol_right', $cInfo->symbol_right, 'class="form-control form-control-sm"'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_CURRENCY_DECIMAL_POINT . '<br>' . tep_draw_input_field('decimal_point', $cInfo->decimal_point, 'class="form-control form-control-sm"'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_CURRENCY_THOUSANDS_POINT . '<br>' . tep_draw_input_field('thousands_point', $cInfo->thousands_point, 'class="form-control form-control-sm"'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_CURRENCY_DECIMAL_PLACES . '<br>' . tep_draw_input_field('decimal_places', $cInfo->decimal_places, 'class="form-control form-control-sm"'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_CURRENCY_VALUE . '<br>' . tep_draw_input_field('value', $cInfo->value, 'class="form-control form-control-sm"'));
      if (DEFAULT_CURRENCY != $cInfo->code) $contents[] = array('text' => '<br>' . tep_draw_checkbox_field('default') . ' ' . TEXT_INFO_SET_AS_DEFAULT);
      $contents[] = array('align' => 'left', 'text' => '<br>' . tep_draw_submit_button_field('', IMAGE_UPDATE,'class="btn btn-primary"') . ' <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->currencies_id) . '">' .IMAGE_CANCEL . '</a>');
      break;
    case 'delete':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_CURRENCY . '</b>');

      $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
      $contents[] = array('text' => '<br><b>' . $cInfo->title . '</b>');
      $contents[] = array('align' => 'left', 'text' => '<br>' . (($remove_currency) ? '<a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->currencies_id . '&action=deleteconfirm') . '">' . IMAGE_DELETE . '</a>' : '') . ' <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->currencies_id) . '">' .IMAGE_CANCEL . '</a>');
      break;
    default:
      if (is_object($cInfo)) {
        $heading[] = array('text' => '<b>' . $cInfo->title . '</b>');

        $contents[] = array('align' => 'left', 'text' => '<a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->currencies_id . '&action=edit') . '">' .IMAGE_EDIT . '</a>
        <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->currencies_id . '&action=delete') . '">' . IMAGE_DELETE . '</a>');
        $contents[] = array('text' => '<br>' . TEXT_INFO_CURRENCY_TITLE . ' ' . $cInfo->title);
        $contents[] = array('text' => TEXT_INFO_CURRENCY_CODE . ' ' . $cInfo->code);
        $contents[] = array('text' => '<br>' . TEXT_INFO_CURRENCY_SYMBOL_LEFT . ' ' . $cInfo->symbol_left);
        $contents[] = array('text' => TEXT_INFO_CURRENCY_SYMBOL_RIGHT . ' ' . $cInfo->symbol_right);
        $contents[] = array('text' => '<br>' . TEXT_INFO_CURRENCY_DECIMAL_POINT . ' ' . $cInfo->decimal_point);
        $contents[] = array('text' => TEXT_INFO_CURRENCY_THOUSANDS_POINT . ' ' . $cInfo->thousands_point);
        $contents[] = array('text' => TEXT_INFO_CURRENCY_DECIMAL_PLACES . ' ' . $cInfo->decimal_places);
        $contents[] = array('text' => '<br>' . TEXT_INFO_CURRENCY_LAST_UPDATED . ' ' . tep_date_short($cInfo->last_updated));
        $contents[] = array('text' => TEXT_INFO_CURRENCY_VALUE . ' ' . number_format($cInfo->value, 8));
        $contents[] = array('text' => '<br>' . TEXT_INFO_CURRENCY_EXAMPLE . '<br>' . $currencies->format('30', false, DEFAULT_CURRENCY) . ' = ' . $currencies->format('30', true, $cInfo->code));
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
 'TABLE_HEADING_CURRENCY_NAME'=>TABLE_HEADING_CURRENCY_NAME,
 'TABLE_HEADING_CURRENCY_CODES'=>TABLE_HEADING_CURRENCY_CODES,
 'TABLE_HEADING_CURRENCY_VALUE'=>TABLE_HEADING_CURRENCY_VALUE,
 'TABLE_HEADING_ACTION'=>TABLE_HEADING_ACTION,
 'count_rows'=>$currency_split->display_count($currency_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_CURRENCY),
 'no_of_pages'=>$currency_split->display_links($currency_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']),
 'new_button'=>$new_button,
 'HEADING_TITLE'=>HEADING_TITLE,
 'RIGHT_BOX_WIDTH'=>$RIGHT_BOX_WIDTH,
 'ADMIN_RIGHT_HTML'=>$ADMIN_RIGHT_HTML,
 'update_message'=>$messageStack->output()));
$template->pparse('currencies');
?>