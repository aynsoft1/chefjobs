<?php
  define('ENABLE_SSL', true);
  class stripe {
    var $code, $title, $description, $enabled;

    function __construct() {
      global $HTTP_GET_VARS, $order, $payment;

      $this->code = 'stripe';
      $this->title = MODULE_PAYMENT_STRIPE_TEXT_TITLE;
      $this->description = MODULE_PAYMENT_STRIPE_TEXT_DESCRIPTION;
      $this->sort_order = defined('MODULE_PAYMENT_STRIPE_SORT_ORDER') ? MODULE_PAYMENT_STRIPE_SORT_ORDER : 0;
      $this->enabled = defined('MODULE_PAYMENT_STRIPE_STATUS') && (MODULE_PAYMENT_STRIPE_STATUS == 'True') ? true : false;
      $this->order_status = defined('MODULE_PAYMENT_STRIPE_ORDER_STATUS_ID') && ((int)MODULE_PAYMENT_STRIPE_ORDER_STATUS_ID > 0) ? (int)MODULE_PAYMENT_STRIPE_ORDER_STATUS_ID : 0;
      $this->form_action_url = 'checkout_process.php';

      if ( defined('MODULE_PAYMENT_STRIPE_STATUS') ) {
        if ( MODULE_PAYMENT_STRIPE_TRANSACTION_SERVER == 'Test' ) {
          $this->title .= ' [Test]';
        }

      }

      if ( !function_exists('curl_init') ) {
        $this->description = '<div class="secWarning">' . MODULE_PAYMENT_STRIPE_ERROR_ADMIN_CURL . '</div>' . $this->description;

        $this->enabled = false;
      }

      if ( $this->enabled === true ) {
        if ( !tep_not_null(MODULE_PAYMENT_STRIPE_PUBLISHABLE_KEY) || !tep_not_null(MODULE_PAYMENT_STRIPE_SECRET_KEY) ) {
          $this->description = '<div class="secWarning">' . MODULE_PAYMENT_STRIPE_ERROR_ADMIN_CONFIGURATION . '</div>' . $this->description;

          $this->enabled = false;
        }
      }

      if ( $this->enabled === true ) {
        if ( isset($order) && is_object($order) ) {
          $this->update_status();
        }
      }
	 
      if ( defined('FILENAME_ADMIN1_ADMIN_MODULES') && (basename($_SERVER['PHP_SELF']) == FILENAME_ADMIN1_ADMIN_MODULES) && isset($_GET['action']) && ($_GET['action'] == 'install') && isset($_GET['subaction']) && ($_GET['subaction'] == 'conntest') ) {
        echo $this->getTestConnectionResult();
        exit;
      }
    }

    function update_status() {
      global $order;

      if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_STRIPE_ZONE > 0) ) {
        $check_flag = false;
        $check_query = tep_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_STRIPE_ZONE . "' and zone_country_id = '" . $order->delivery['country']['id'] . "' order by zone_id");
        while ($check = tep_db_fetch_array($check_query)) {
          if ($check['zone_id'] < 1) {
            $check_flag = true;
            break;
          } elseif ($check['zone_id'] == $order->delivery['zone_id']) {
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
      global $customer_id, $payment;

           return array('id' => $this->code,
                   'module' => $this->title);
    }

    function pre_confirmation_check() { 
      return false;
    }

    function confirmation() {      return array('title' => 'Make Payable To : '.SITE_TITLE);

		return false;	      
    }

    function process_button() {
      global $order, $currencies,  $product_id;
        $my_currency = DEFAULT_CURRENCY;
	    if(check_login("jobseeker"))
 		{
	 	 $action_url=tep_href_link(FILENAME_JOBSEEKER_CHECKOUT_PROCESS, '', 'SSL') ;
		}
		else
		{
		 $action_url=tep_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL') ;
		}   
		
		$process_button_string='<style>input[type="submit"]{display:none;}</style></form>
		<form  name="checkout_confirmation2" action="'.$action_url.'" method="post">
<script
    src="https://checkout.stripe.com/checkout.js" class="stripe-button"
    data-key="'.MODULE_PAYMENT_STRIPE_PUBLISHABLE_KEY.'"
    data-amount="'.	str_replace('.','',$currencies->format_without_symbol($order->info['total'])).'"
	data-currency="'.$my_currency.'"
    data-name="'.SITE_TITLE.'"
    data-description="'.$order->products['plan_type_name'].'"
	data-email="'.$order->billing['email_address'].'"
	data-label="Confirm Order"
    data-image="https://stripe.com/img/documentation/checkout/marketplace.png"
    data-locale="auto"
	data-allow-remember-me="false"
    >
  </script></form>';
		      return $process_button_string;

	 } 

    function before_process() {
		 global $order,$currencies,$messageStack;
        $my_currency = DEFAULT_CURRENCY;
		if(isset($_POST['stripeToken']))
		{
      	 $data = array('amount'      => str_replace('.','',$currencies->format_without_symbol($order->info['total'])),
			           'currency'    => $my_currency,
			           'source'      => $_POST['stripeToken'],
			           'description' => $order->products['plan_type_name']);
	 	 $ch = curl_init();
         curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '.MODULE_PAYMENT_STRIPE_SECRET_KEY));
         curl_setopt($ch, CURLOPT_URL, "https://api.stripe.com/v1/charges");
         curl_setopt($ch, CURLOPT_POST, true);
         curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
         $output = curl_exec($ch);
		 $info   = curl_getinfo($ch);
         $output = json_decode($output, true); 
		if($info['http_code']==200)
		{
         return false;
		}
        if(isset($output['error']))
	    {
	     $messageStack->add_session($output['error']['message'], 'error');     
	    }
	    if(check_login("jobseeker"))
         tep_redirect(tep_href_link(FILENAME_JOBSEEKER_CONTROL_PANEL));
		   else
            tep_redirect(tep_href_link(FILENAME_RECRUITER_CONTROL_PANEL));

		 die("ok");
		 curl_close($ch);
		}
		else
		{
	   	 if(check_login("jobseeker"))
          tep_redirect(tep_href_link(FILENAME_JOBSEEKER_CHECKOUT_PAYMENT, 'product_id='.$product_id, 'SSL',true,false));
	  	else
         tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'product_id='.$product_id, 'SSL',true,false));
		}
      return false;
    }

    function after_process() {
          return false;
    }
    

    function check() {
      if (!isset($this->_check)) {
        $check_query = tep_db_query("select configuration_value from " . CONFIGURATION_TABLE . " where configuration_name = 'MODULE_PAYMENT_STRIPE_STATUS'");
        $this->_check = tep_db_num_rows($check_query);
      }
      return $this->_check;
    }

    function install($parameter = null) {
      $params = $this->getParams();

      if (isset($parameter)) {
        if (isset($params[$parameter])) {
          $params = array($parameter => $params[$parameter]);
        } else {
          $params = array();
        }
      }

      foreach ($params as $key => $data) {
        $sql_data_array = array('configuration_title' => $data['title'],
                                'configuration_name' => $key,
                                'configuration_value' => (isset($data['value']) ? $data['value'] : ''),
                                'configuration_description' => $data['desc'],
                                'configuration_group_id' => '9',
                                'priority' => '0',
                                'inserted' => 'now()');

        if (isset($data['set_func'])) {
          $sql_data_array['set_function'] = $data['set_func'];
        }

        if (isset($data['use_func'])) {
          $sql_data_array['use_function'] = $data['use_func'];
        }

        tep_db_perform(CONFIGURATION_TABLE, $sql_data_array);
      }
    }

    function remove() {
      tep_db_query("delete from " . CONFIGURATION_TABLE . " where configuration_name in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      $keys = array_keys($this->getParams());

      if ($this->check()) {
        foreach ($keys as $key) {
          if (!defined($key)) {
            $this->install($key);
          }
        }
      }

      return $keys;
    }

    function getParams() {
      
      $status_id = MODULE_PAYMENT_STRIPE_TRANSACTION_ORDER_STATUS_ID;
      
      $params = array('MODULE_PAYMENT_STRIPE_STATUS' => array('title' => 'Enable Stripe Module',
                                                              'desc' => 'Do you want to accept Stripe payments?',
                                                              'value' => 'False',
                                                              'set_func' => 'tep_cfg_select_option(array(\'True\', \'False\'), '),
                      'MODULE_PAYMENT_STRIPE_PUBLISHABLE_KEY' => array('title' => 'Publishable API Key',
                                                                       'desc' => 'The Stripe account publishable API key to use.',
                                                                       'value' => ''),
                      'MODULE_PAYMENT_STRIPE_SECRET_KEY' => array('title' => 'Secret API Key',
                                                                  'desc' => 'The Stripe account secret API key to use with the publishable key.',
                                                                  'value' => ''),
                      'MODULE_PAYMENT_STRIPE_ORDER_STATUS_ID' => array('title' => 'Set Order Status',
                                                                       'desc' => 'Set the status of orders made with this payment module to this value',
                                                                       'value' => '0',
                                                                       'use_func' => 'tep_get_order_status_name',
                                                                       'set_func' => 'tep_cfg_pull_down_order_statuses('),
                       'MODULE_PAYMENT_STRIPE_TRANSACTION_SERVER' => array('title' => 'Transaction Server',
                                                                          'desc' => 'Perform transactions on the production server or on the testing server.',
                                                                          'value' => 'Live',
                                                                          'set_func' => 'tep_cfg_select_option(array(\'Live\', \'Test\'), '),
                      'MODULE_PAYMENT_STRIPE_SORT_ORDER' => array('title' => 'Sort order of display.',
                                                                  'desc' => 'Sort order of display. Lowest is displayed first.',
                                                                  'value' => '0'));

      return $params;
    }   
  }
?>
