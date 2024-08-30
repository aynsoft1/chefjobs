<?
  class moneyorder {
    var $code, $title, $description, $enabled;

// class constructor
    function __construct() {
      global $order;

      $this->code = 'moneyorder';
      $this->title = MODULE_PAYMENT_MONEYORDER_TEXT_TITLE;
      $this->description = MODULE_PAYMENT_MONEYORDER_TEXT_DESCRIPTION;
      $this->sort_order = MODULE_PAYMENT_MONEYORDER_SORT_ORDER;
      $this->enabled = ((MODULE_PAYMENT_MONEYORDER_STATUS == 'True') ? true : false);

      if ((int)MODULE_PAYMENT_MONEYORDER_ORDER_STATUS_ID > 0) {
        $this->order_status = MODULE_PAYMENT_MONEYORDER_ORDER_STATUS_ID;
      }

      if (is_object($order)) $this->update_status();
    
      $this->email_footer = MODULE_PAYMENT_MONEYORDER_TEXT_EMAIL_FOOTER;
    }

// class methods
    function update_status() {
      global $order;

      if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_MONEYORDER_ZONE > 0) ) {
        $check_flag = false;
        $check_query = tep_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_MONEYORDER_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
        while ($check = tep_db_fetch_array($check_query)) {
          if ($check['zone_id'] < 1) {
            $check_flag = true;
            break;
          } elseif ($check['zone_id'] == $order->billing['zone_id']) {
            $check_flag = true;
            break;
          }
        }

        if ($check_flag == false) {
          $this->enabled = false;
        }
      }
    }

    function javascript_validation() {
      return false;
    }

    function selection() {
      return array('id' => $this->code,
                   'module' => $this->title);
    }

    function pre_confirmation_check() {
      return false;
    }

    function confirmation() {
      return array('title' => MODULE_PAYMENT_MONEYORDER_TEXT_DESCRIPTION);
    }

    function process_button() {
      return false;
    }

    function before_process() {
      return false;
    }

    function after_process() {
      return false;
    }

    function get_error() {
      return false;
    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = tep_db_query("select configuration_value from " . CONFIGURATION_TABLE . " where configuration_name = 'MODULE_PAYMENT_MONEYORDER_STATUS'");
        $this->_check = tep_db_num_rows($check_query);
      }
      return $this->_check;
    }

    function install() {
      tep_db_query("insert into " . CONFIGURATION_TABLE . " (configuration_title, configuration_name, configuration_value, configuration_description, configuration_group_id, priority, set_function, inserted) values ('Enable Check/Money Order Module', 'MODULE_PAYMENT_MONEYORDER_STATUS', 'True', 'Do you want to accept Check/Money Order payments?', '9', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now());");
      tep_db_query("insert into " . CONFIGURATION_TABLE . " (configuration_title, configuration_name, configuration_value, configuration_description, configuration_group_id, priority, inserted) values ('Make Payable to:', 'MODULE_PAYMENT_MONEYORDER_PAYTO', '', 'Who should payments be made payable to?', '9', '1', now());");
      tep_db_query("insert into " . CONFIGURATION_TABLE . " (configuration_title, configuration_name, configuration_value, configuration_description, configuration_group_id, priority, inserted) values ('Sort order of display.', 'MODULE_PAYMENT_MONEYORDER_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '9', '0', now())");
      tep_db_query("insert into " . CONFIGURATION_TABLE . " (configuration_title, configuration_name, configuration_value, configuration_description, configuration_group_id, priority, use_function, set_function, inserted) values ('Payment Zone', 'MODULE_PAYMENT_MONEYORDER_ZONE', '0', 'If a zone is selected, only enable this payment method for that zone.', '9', '2', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now())");
      tep_db_query("insert into " . CONFIGURATION_TABLE . " (configuration_title, configuration_name, configuration_value, configuration_description, configuration_group_id, priority, set_function, use_function, inserted) values ('Set Order Status', 'MODULE_PAYMENT_MONEYORDER_ORDER_STATUS_ID', '0', 'Set the status of orders made with this payment module to this value', '9', '0', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now())");
    }

    function remove() {
      tep_db_query("delete from " . CONFIGURATION_TABLE . " where configuration_name in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_PAYMENT_MONEYORDER_STATUS', 'MODULE_PAYMENT_MONEYORDER_ZONE', 'MODULE_PAYMENT_MONEYORDER_ORDER_STATUS_ID', 'MODULE_PAYMENT_MONEYORDER_SORT_ORDER', 'MODULE_PAYMENT_MONEYORDER_PAYTO');
    }
  }
?>
