<?
  class ot_total {
    var $title, $output;

    function __construct() {
      $this->code = 'ot_total';
      $this->title = MODULE_ORDER_TOTAL_TOTAL_TITLE;
      $this->description = MODULE_ORDER_TOTAL_TOTAL_DESCRIPTION;
      $this->enabled = ((MODULE_ORDER_TOTAL_TOTAL_STATUS == 'true') ? true : false);
      $this->sort_order = MODULE_ORDER_TOTAL_TOTAL_SORT_ORDER;

      $this->output = array();
    }

    function process() {
      global $order, $currencies;

      $this->output[] = array('title' => $this->title . ':',
                              'text' => '<b>' . $order->info['total_symbol'] . '</b>',
                              'value' => str_replace(",","",$order->info['total']));
    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = tep_db_query("select configuration_value from " . CONFIGURATION_TABLE . " where configuration_name = 'MODULE_ORDER_TOTAL_TOTAL_STATUS'");
        $this->_check = tep_db_num_rows($check_query);
      }

      return $this->_check;
    }

    function keys() {
      return array('MODULE_ORDER_TOTAL_TOTAL_STATUS', 'MODULE_ORDER_TOTAL_TOTAL_SORT_ORDER');
    }

    function install() {
      tep_db_query("insert into " . CONFIGURATION_TABLE . " (configuration_title, configuration_name, configuration_value, configuration_description, configuration_group_id, priority, set_function, inserted) values ('Display Total', 'MODULE_ORDER_TOTAL_TOTAL_STATUS', 'true', 'Do you want to display the total order value?', '9', '1','tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
      tep_db_query("insert into " . CONFIGURATION_TABLE . " (configuration_title, configuration_name, configuration_value, configuration_description, configuration_group_id, priority, inserted) values ('Sort Order', 'MODULE_ORDER_TOTAL_TOTAL_SORT_ORDER', '4', 'Sort order of display.', '9', '2', now())");
    }

    function remove() {
      tep_db_query("delete from " . CONFIGURATION_TABLE . " where configuration_name in ('" . implode("', '", $this->keys()) . "')");
    }
  }
?>
