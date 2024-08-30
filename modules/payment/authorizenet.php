<?
  class authorizenet {
    var $code, $title, $description, $enabled;

// class constructor
    function __construct() {
      global $order;

      $this->code = 'authorizenet';
      $this->title = MODULE_PAYMENT_AUTHORIZENET_TEXT_TITLE;
      $this->description = MODULE_PAYMENT_AUTHORIZENET_TEXT_DESCRIPTION;
      $this->enabled = ((MODULE_PAYMENT_AUTHORIZENET_STATUS == 'True') ? true : false);
      $this->sort_order = MODULE_PAYMENT_AUTHORIZENET_SORT_ORDER;

      if ((int)MODULE_PAYMENT_AUTHORIZENET_ORDER_STATUS_ID > 0) {
        $this->order_status = MODULE_PAYMENT_AUTHORIZENET_ORDER_STATUS_ID;
      }

      if (is_object($order)) $this->update_status();
      switch (MODULE_PAYMENT_AUTHORIZENET_TRANSACTION_SERVER) {
        case 'Live':
          $this->form_action_url = 'https://secure.authorize.net/gateway/transact.dll';
          break;

        default:
          $this->form_action_url = 'https://test.authorize.net/gateway/transact.dll';
          break;
      }


    }

// Authorize.net utility functions
// DISCLAIMER:
//     This code is distributed in the hope that it will be useful, but without any warranty; 
//     without even the implied warranty of merchantability or fitness for a particular purpose.

// Main Interfaces:
//
// function InsertFP ($loginid, $txnkey, $amount, $sequence) - Insert HTML form elements required for SIM
// function CalculateFP ($loginid, $txnkey, $amount, $sequence, $tstamp) - Returns Fingerprint.

// compute HMAC-MD5
// Uses PHP mhash extension. Pl sure to enable the extension
// function hmac ($key, $data) {
//   return (bin2hex (mhash(MHASH_MD5, $data, $key)));
//}

// Thanks is lance from http://www.php.net/manual/en/function.mhash.php
//lance_rushing at hot* spamfree *mail dot com
//27-Nov-2002 09:36 
// 
//Want to Create a md5 HMAC, but don't have hmash installed?
//
//Use this:

function hmac ($key, $data)
{
   // RFC 2104 HMAC implementation for php.
   // Creates an md5 HMAC.
   // Eliminates the need to install mhash to compute a HMAC
   // Hacked by Lance Rushing

   $b = 64; // byte length for md5
   if (strlen($key) > $b) {
       $key = pack("H*",md5($key));
   }
   $key  = str_pad($key, $b, chr(0x00));
   $ipad = str_pad('', $b, chr(0x36));
   $opad = str_pad('', $b, chr(0x5c));
   $k_ipad = $key ^ $ipad ;
   $k_opad = $key ^ $opad;

   return md5($k_opad  . pack("H*",md5($k_ipad . $data)));
}
// end code from lance (resume authorize.net code)

// Calculate and return fingerprint
// Use when you need control on the HTML output
function CalculateFP ($loginid, $txnkey, $amount, $sequence, $tstamp, $currency = "") {
  return ($this->hmac ($txnkey, $loginid . "^" . $sequence . "^" . $tstamp . "^" . $amount . "^" . $currency));
}

// Inserts the hidden variables in the HTML FORM required for SIM
// Invokes hmac function to calculate fingerprint.

function InsertFP ($loginid, $txnkey, $amount, $sequence, $currency = "") {
  $tstamp = time ();
  $fingerprint = $this->hmac ($txnkey, $loginid . "^" . $sequence . "^" . $tstamp . "^" . $amount . "^" . $currency);

  $str = tep_draw_hidden_field('x_fp_sequence', $sequence) .
         tep_draw_hidden_field('x_fp_timestamp', $tstamp) .
         tep_draw_hidden_field('x_fp_hash', $fingerprint);

  return $str;
}
// end authorize.net code

// class methods
    function update_status() {
      global $order;

      if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_AUTHORIZENET_ZONE > 0) ) {
        $check_flag = false;
        $check_query = tep_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_AUTHORIZENET_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
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
      global $order;

      $today = getdate(); 
      for ($i=$today['year']; $i < $today['year']+10; $i++) {
        $expires_year[] = array('id' => strftime('%y',mktime(0,0,0,1,1,$i)), 'text' => strftime('%Y',mktime(0,0,0,1,1,$i)));
      }
      $selection = array('id' => $this->code,
                         'module' => $this->title);

      return $selection;
    }
    function pre_confirmation_check() {
      return false;
    }


    function confirmation() {
      return false;
    }

    function process_button() {
      global $HTTP_SERVER_VARS, $order;
      $sequence = rand(1, 1000);
      $process_button_string = tep_draw_hidden_field('x_login', MODULE_PAYMENT_AUTHORIZENET_LOGIN) .
                               tep_draw_hidden_field('x_Amount', number_format($order->info['total'],2)) .
                               tep_draw_hidden_field('x_currency_code',substr($order->info['currency'],0,3)) .
                               tep_draw_hidden_field('x_relay_response', 'TRUE') .
                               //tep_draw_hidden_field('x_relay_url', tep_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL', false)) .
                               tep_draw_hidden_field('x_relay_url', tep_href_link('authorizenet_check.php', '', 'SSL', false)) .
                               tep_draw_hidden_field('x_method','CC') .
                               tep_draw_hidden_field('x_type', ((MODULE_PAYMENT_AUTHORIZENET_TRANSACTION_METHOD == 'Capture') ? 'CAPTURE_ONLY' : ((MODULE_PAYMENT_AUTHORIZENET_TRANSACTION_METHOD == 'Authorization')?'AUTH_ONLY':'AUTH_CAPTURE'))) .
                               tep_draw_hidden_field('x_version', '3.0') .
                               tep_draw_hidden_field('x_show_form', 'PAYMENT_FORM').
                               tep_draw_hidden_field('x_email_customer', ((MODULE_PAYMENT_AUTHORIZENET_EMAIL_CUSTOMER == 'True') ? 'TRUE': 'FALSE')) .
                               tep_draw_hidden_field('x_first_name', $order->billing['firstname']) .
                               tep_draw_hidden_field('x_last_name', $order->billing['lastname']) .
                               tep_draw_hidden_field('x_company', substr($order->billing['company'], 0, 50)) .
                               // tep_draw_hidden_field('x_address', $order->billing['street_address']) .
                               //tep_draw_hidden_field('x_city', $order->billing['city']) .
                               // tep_draw_hidden_field('x_state', $order->billing['state']) .
                               //tep_draw_hidden_field('x_zip', $order->billing['postcode']) .
                               tep_draw_hidden_field('x_country', $order->billing['country']) .
                               //tep_draw_hidden_field('x_phone', $order->customer['telephone']) .
                               //tep_draw_hidden_field('x_email', $order->customer['email_address']) .
                               tep_draw_hidden_field('x_customer_ip',tep_get_ip_address()) .
                               $this->InsertFP(MODULE_PAYMENT_AUTHORIZENET_LOGIN, MODULE_PAYMENT_AUTHORIZENET_TXNKEY, number_format($order->info['total'], 2), $sequence,substr($order->info['currency'],0,3));
                               if (MODULE_PAYMENT_AUTHORIZENET_TESTMODE == 'Test') 
                               $process_button_string .= tep_draw_hidden_field('x_test_request', 'TRUE');
                               //$process_button_string .= tep_draw_hidden_field(tep_session_name(), tep_session_id());
                               return $process_button_string;
    }

    function before_process() {
      return false;
//      global $HTTP_POST_VARS;
      if ($_POST['x_response_code'] == '1') return;
      if ($_POST['x_response_code'] == '2') {
        tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . urlencode(MODULE_PAYMENT_AUTHORIZENET_TEXT_DECLINED_MESSAGE), 'SSL', true, false));
      }
      // Code 3 is an error - but anything else is an error too (IMHO)
      tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . urlencode(MODULE_PAYMENT_AUTHORIZENET_TEXT_ERROR_MESSAGE), 'SSL', true, false));
    }
	 function before_process1() {
//      global $HTTP_POST_VARS;
      if ($_POST['x_response_code'] == '1') 
      return true;
      return false;	 	 
    }

    function after_process() {
      return false;
    }

    function get_error() {
      global $HTTP_GET_VARS;

      $error = array('title' => MODULE_PAYMENT_AUTHORIZENET_TEXT_ERROR,
                     'error' => stripslashes(urldecode($HTTP_GET_VARS['error'])));

      return $error;
    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = tep_db_query("select configuration_value from " . CONFIGURATION_TABLE . " where configuration_name = 'MODULE_PAYMENT_AUTHORIZENET_STATUS'");
        $this->_check = tep_db_num_rows($check_query);
      }
      return $this->_check;
    }

    function install() {
      tep_db_query("insert into " . CONFIGURATION_TABLE . " (configuration_title, configuration_name, configuration_value, configuration_description, configuration_group_id, priority, set_function, inserted) values ('Enable Authorize.net Module', 'MODULE_PAYMENT_AUTHORIZENET_STATUS', 'True', 'Do you want to accept Authorize.net payments?', '9', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into " . CONFIGURATION_TABLE . " (configuration_title, configuration_name, configuration_value, configuration_description, configuration_group_id, priority, inserted) values ('Login Username', 'MODULE_PAYMENT_AUTHORIZENET_LOGIN', 'testing', 'The login username used for the Authorize.net service', '9', '0', now())");
      tep_db_query("insert into " . CONFIGURATION_TABLE . " (configuration_title, configuration_name, configuration_value, configuration_description, configuration_group_id, priority, inserted) values ('Transaction Key', 'MODULE_PAYMENT_AUTHORIZENET_TXNKEY', 'Test', 'Transaction Key used for encrypting TP data', '9', '0', now())");
      tep_db_query("insert into " . CONFIGURATION_TABLE . " (configuration_title, configuration_name, configuration_value, configuration_description, configuration_group_id, priority, set_function, inserted) values ('Transaction Server', 'MODULE_PAYMENT_AUTHORIZENET_TRANSACTION_SERVER', 'Live', 'Perform transactions on the live or test server. The test server should only be used by developers with Authorize.net test accounts.', '9', '0', 'tep_cfg_select_option(array(\'Live\', \'Test\'), ', now())");
      tep_db_query("insert into " . CONFIGURATION_TABLE . " (configuration_title, configuration_name, configuration_value, configuration_description, configuration_group_id, priority, set_function, inserted) values ('Transaction Mode','MODULE_PAYMENT_AUTHORIZENET_TESTMODE', 'Test', 'Transaction mode used for processing orders', '9', '0', 'tep_cfg_select_option(array(\'Test\', \'Production\'), ', now())");
     // tep_db_query("insert into " . CONFIGURATION_TABLE . " (configuration_title, configuration_name, configuration_value, configuration_description, configuration_group_id, priority, set_function, inserted) values ('Transaction Method','MODULE_PAYMENT_AUTHORIZENET_METHOD', 'Credit Card', 'Transaction method used for processing orders', '9', '0', 'tep_cfg_select_option(array(\'Credit Card\', \'eCheck\'), ', now())");
      tep_db_query("insert into " . CONFIGURATION_TABLE . " (configuration_title, configuration_name, configuration_value, configuration_description, configuration_group_id, priority, set_function, inserted) values ('Transaction Method', 'MODULE_PAYMENT_AUTHORIZENET_TRANSACTION_METHOD', 'Authorization', 'The processing method to use for each transaction.', '9', '0', 'tep_cfg_select_option(array(\'Authorization\', \'Capture\', \'Authorization and Capture\'), ', now())");
      tep_db_query("insert into " . CONFIGURATION_TABLE . " (configuration_title, configuration_name, configuration_value, configuration_description, configuration_group_id, priority, set_function, inserted) values ('Customer Notifications', 'MODULE_PAYMENT_AUTHORIZENET_EMAIL_CUSTOMER', 'False', 'Should Authorize.Net e-mail a receipt to the customer?', '9', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into " . CONFIGURATION_TABLE . " (configuration_title, configuration_name, configuration_value, configuration_description, configuration_group_id, priority, inserted) values ('Sort order of display.', 'MODULE_PAYMENT_AUTHORIZENET_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '9', '0', now())");
      tep_db_query("insert into " . CONFIGURATION_TABLE . " (configuration_title, configuration_name, configuration_value, configuration_description, configuration_group_id, priority, use_function, set_function, inserted) values ('Payment Zone', 'MODULE_PAYMENT_AUTHORIZENET_ZONE', '0', 'If a zone is selected, only enable this payment method for that zone.', '9', '2', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now())");
      tep_db_query("insert into " . CONFIGURATION_TABLE . " (configuration_title, configuration_name, configuration_value, configuration_description, configuration_group_id, priority, set_function, use_function, inserted) values ('Set Order Status', 'MODULE_PAYMENT_AUTHORIZENET_ORDER_STATUS_ID', '0', 'Set the status of orders made with this payment module to this value', '9', '0', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now())");
    }

    function remove() {
      tep_db_query("delete from " . CONFIGURATION_TABLE . " where configuration_name in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_PAYMENT_AUTHORIZENET_STATUS', 'MODULE_PAYMENT_AUTHORIZENET_LOGIN', 'MODULE_PAYMENT_AUTHORIZENET_TXNKEY','MODULE_PAYMENT_AUTHORIZENET_TRANSACTION_SERVER','MODULE_PAYMENT_AUTHORIZENET_TESTMODE', 'MODULE_PAYMENT_AUTHORIZENET_TRANSACTION_METHOD', 'MODULE_PAYMENT_AUTHORIZENET_EMAIL_CUSTOMER', 'MODULE_PAYMENT_AUTHORIZENET_ZONE', 'MODULE_PAYMENT_AUTHORIZENET_ORDER_STATUS_ID', 'MODULE_PAYMENT_AUTHORIZENET_SORT_ORDER');
    }
  }
?>
