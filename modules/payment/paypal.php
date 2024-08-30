<?
  class paypal {
    var $code, $title, $description, $enabled;

// class constructor
    function __construct() {
      global $order;

      $this->code = 'paypal';
      $this->title = MODULE_PAYMENT_PAYPAL_TEXT_TITLE;
      $this->description = MODULE_PAYMENT_PAYPAL_TEXT_DESCRIPTION;
      $this->sort_order = MODULE_PAYMENT_PAYPAL_SORT_ORDER;
      $this->enabled = ((MODULE_PAYMENT_PAYPAL_STATUS == 'True') ? true : false);

      if ((int)MODULE_PAYMENT_PAYPAL_ORDER_STATUS_ID > 0) {
        $this->order_status = MODULE_PAYMENT_PAYPAL_ORDER_STATUS_ID;
      }

      if (is_object($order)) $this->update_status();

      $this->form_action_url = 'https://www.paypal.com/cgi-bin/webscr';
    }

// class methods
    function update_status() {
      global $order;

      if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_PAYPAL_ZONE > 0) ) {
        $check_flag = false;
        $check_query = tep_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_PAYPAL_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
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
      return false;
    }

    function process_button() {
      global $order, $currencies, $currency, $product_id;

      if (MODULE_PAYMENT_PAYPAL_CURRENCY == 'Selected Currency') {
        $my_currency = $currency;
      } else {
        $my_currency = substr(MODULE_PAYMENT_PAYPAL_CURRENCY, 5);
      }
      if (!in_array($my_currency, array('CAD', 'EUR', 'GBP', 'JPY', 'USD','SGD'))) {
        $my_currency = DEFAULT_CURRENCY;
      }
						if(check_login("jobseeker"))
 					{
	 					$payment_hidden_fields=''.tep_draw_hidden_field('return', tep_href_link(FILENAME_JOBSEEKER_CHECKOUT_PROCESS, '', 'SSL')) .
                                 tep_draw_hidden_field('cancel_return', tep_href_link(FILENAME_JOBSEEKER_CHECKOUT_PAYMENT, '', 'SSL')).'';
					 }
					 else
					 {
							$payment_hidden_fields=''.tep_draw_hidden_field('return', tep_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL')) .
                                 tep_draw_hidden_field('cancel_return', tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL')).'';
					 }

      $process_button_string = tep_draw_hidden_field('cmd', '_xclick') .
                               tep_draw_hidden_field('business', MODULE_PAYMENT_PAYPAL_ID) .
                               tep_draw_hidden_field('item_name', SITE_TITLE) .
                               tep_draw_hidden_field('amount',(float) $currencies->format_without_symbol((float)$order->info['total'], ($my_currency!=DEFAULT_CURRENCY?true:false), DEFAULT_CURRENCY, ($my_currency==DEFAULT_CURRENCY?$currencies->get_value($my_currency):''))) .
                               tep_draw_hidden_field('shipping', 0.00) .
                               tep_draw_hidden_field('currency_code', $my_currency) .$payment_hidden_fields;

      return $process_button_string;
    }

    function before_process() {
      return false;
    }

    function after_process() {
      return false;
    }

    function output_error() {
      return false;
    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = tep_db_query("select configuration_value from " . CONFIGURATION_TABLE . " where configuration_name = 'MODULE_PAYMENT_PAYPAL_STATUS'");
        $this->_check = tep_db_num_rows($check_query);
      }
      return $this->_check;
    }

    function install() {
      tep_db_query("insert into " . CONFIGURATION_TABLE . " (configuration_title, configuration_name, configuration_value, configuration_description, configuration_group_id, priority, set_function, inserted) values ('Enable PayPal Module', 'MODULE_PAYMENT_PAYPAL_STATUS', 'True', 'Do you want to accept PayPal payments?', '9', '3', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into " . CONFIGURATION_TABLE . " (configuration_title, configuration_name, configuration_value, configuration_description, configuration_group_id, priority, inserted) values ('E-Mail Address', 'MODULE_PAYMENT_PAYPAL_ID', 'you@yourbusiness.com', 'The e-mail address to use for the PayPal service', '9', '4', now())");
      tep_db_query("insert into " . CONFIGURATION_TABLE . " (configuration_title, configuration_name, configuration_value, configuration_description, configuration_group_id, priority, set_function, inserted) values ('Transaction Currency', 'MODULE_PAYMENT_PAYPAL_CURRENCY', 'Selected Currency', 'The currency to use for credit card transactions', '9', '9', 'tep_cfg_select_option(array(\'Selected Currency\',\'Only USD\',\'Only SGD	\',\'Only CAD\',\'Only EUR\',\'Only GBP\',\'Only JPY\'), ', now())");
      tep_db_query("insert into " . CONFIGURATION_TABLE . " (configuration_title, configuration_name, configuration_value, configuration_description, configuration_group_id, priority, inserted) values ('Sort order of display.', 'MODULE_PAYMENT_PAYPAL_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '9', '0', now())");
      tep_db_query("insert into " . CONFIGURATION_TABLE . " (configuration_title, configuration_name, configuration_value, configuration_description, configuration_group_id, priority, use_function, set_function, inserted) values ('Payment Zone', 'MODULE_PAYMENT_PAYPAL_ZONE', '0', 'If a zone is selected, only enable this payment method for that zone.', '9', '2', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now())");
      tep_db_query("insert into " . CONFIGURATION_TABLE . " (configuration_title, configuration_name, configuration_value, configuration_description, configuration_group_id, priority, set_function, use_function, inserted) values ('Set Order Status', 'MODULE_PAYMENT_PAYPAL_ORDER_STATUS_ID', '0', 'Set the status of orders made with this payment module to this value', '9', '0', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now())");
    }

    function remove() {
      tep_db_query("delete from " . CONFIGURATION_TABLE . " where configuration_name in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_PAYMENT_PAYPAL_STATUS', 'MODULE_PAYMENT_PAYPAL_ID', 'MODULE_PAYMENT_PAYPAL_CURRENCY', 'MODULE_PAYMENT_PAYPAL_ZONE', 'MODULE_PAYMENT_PAYPAL_ORDER_STATUS_ID', 'MODULE_PAYMENT_PAYPAL_SORT_ORDER');
    }
  }
?>
