<?php
/*
   

	Freeway eCommerce
	http://www.openfreeway.org
	Copyright (c) 2007 ZacWare

	Released under the GNU General Public License 
*/

// Check to ensure this file is included in osConcert!
defined('_FEXEC') or die();


  class card_payment {
    var $code, $title, $description, $enabled;

// class constructor
	function __construct() {
		global $order;
		
		$this->code = 'card_payment';
		$name = "Card Payment";
		$image = "";
		$path = "";

		if(MODULE_PAYMENT_CARD_PAYMENT_DISPLAY_NAME != "MODULE_PAYMENT_CARD_PAYMENT_DISPLAY_NAME")$name = MODULE_PAYMENT_CARD_PAYMENT_DISPLAY_NAME;
		if(MODULE_PAYMENT_CARD_PAYMENT_IMAGE != "MODULE_PAYMENT_CARD_PAYMENT_IMAGE")$image = MODULE_PAYMENT_CARD_PAYMENT_IMAGE;
		if($image != "" && file_exists($path . DIR_WS_IMAGES . $image))
		{
			$image = '<img class="img-fluid img-mod" src="' . HTTP_SERVER . DIR_WS_HTTP_CATALOG . DIR_WS_IMAGES . $image . '" height="33">';
		}else
		{
			$image_array = array('.png','.jpg','.jpeg','.gif');
			$image_check = true;
			for($i=0;$i<sizeof($image_array);$i++)
			{
				if($image_check && $image != "" && file_exists($path . DIR_WS_IMAGES . $image . $image_array[$i])){
					$image = '<img class="img-fluid img-mod" src="' . HTTP_SERVER . DIR_WS_HTTP_CATALOG . DIR_WS_IMAGES . $image . $image_array[$i] . '" width="122" height="33">';
					$image_check = false;
				}
			}
			if($image_check)$image = $path;
		}
		define('MODULE_PAYMENT_CARD_PAYMENT_TEXT_TITLE', '<span class="modname">' . $name . '</span>&nbsp;&nbsp;' . $image);
		
		
		//define('MODULE_PAYMENT_CARD_PAYMENT_TEXT_TITLE', '<div class="container-fluid"><div class="row"><div class="col-md-12"><div class="row"><div class="col-md-3">'.$name . '</div><div class="col-md-9">' . $image.'</div></div></div>');
		define('MODULE_PAYMENT_CARD_PAYMENT_TEXT_TEXT_TITLE', $name);
		$this->title = MODULE_PAYMENT_CARD_PAYMENT_TEXT_TITLE;
		$this->text_title = MODULE_PAYMENT_CARD_PAYMENT_TEXT_TEXT_TITLE;
		$this->description = MODULE_PAYMENT_CARD_PAYMENT_TEXT_DESCRIPTION;
		$this->sort_order = MODULE_PAYMENT_CARD_PAYMENT_SORT_ORDER;
		$this->enabled = ((MODULE_PAYMENT_CARD_PAYMENT_STATUS == 'True') ? true : false);
		$this->barred=false;
		if ((int)MODULE_PAYMENT_CARD_PAYMENT_ORDER_STATUS_ID > 0) {
			$this->order_status = MODULE_PAYMENT_CARD_PAYMENT_ORDER_STATUS_ID;
		}
		
		if (is_object($order)) $this->update_status();
	}

// class methods
    function update_status() {
      global $order;
	  
	  tep_check_module_status($this,MODULE_PAYMENT_CARD_PAYMENT_ZONE,trim(MODULE_PAYMENT_CARD_PAYMENT_EXCEPT_ZONE),trim(MODULE_PAYMENT_CARD_PAYMENT_EXCEPT_COUNTRY));
	  $this->barred=tep_check_payment_barred(trim(MODULE_PAYMENT_CARD_PAYMENT_EXCEPT_COUNTRY));
    }

    function javascript_validation() 
	{
      return false;
    }

    function selection() {
      return array('id' => $this->code,
	  				'barred'=>$this->barred,
                   'module' => $this->title);
    }

   function pre_confirmation_check() {
	
	  		if(($_SESSION['customer_country_id']==999) && (DIRECT_CHECKOUT=='true')){
		
		 tep_redirect(tep_href_link(FILENAME_CHECKOUT_PROCESS_FREE, '', 'SSL'));
			}
        return false;
    }
    function confirmation() {
      return false;
    }

    function process_button() {
      return false;
    }

    function before_process() {
		//double check here for a BoxOffice user
		//country_id should == 999
	global $FSESSION;
	
	if ($FSESSION->getobject('customer_country_id') != 999){
		$FSESSION->remove('payment');
		tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . urlencode(ERROR_NO_PAYMENT_MODULE_SELECTED), 'SSL'));
		exit();
	}
		// extra check - look for the country id based on customer id
		$check_customer_query = tep_db_query("select   customers_default_address_id from " . TABLE_CUSTOMERS . " where customers_id = '" . $FSESSION->get("customer_id") . "' ");
        $check_customer = tep_db_fetch_array($check_customer_query);
		
		$check_country_query = tep_db_query("select entry_country_id, entry_zone_id from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . $FSESSION->get("customer_id") . "' and address_book_id = '" . (int)$check_customer['customers_default_address_id'] . "'");
        $check_country = tep_db_fetch_array($check_country_query);
		
		if ($check_country['entry_country_id'] != 999){
		  $FSESSION->remove('payment');
		  tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . urlencode(ERROR_NO_PAYMENT_MODULE_SELECTED), 'SSL'));
		exit();
	}
		

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
        $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_CARD_PAYMENT_STATUS'");
        $this->_check = tep_db_num_rows($check_query);
      }
      return $this->_check;
    }

    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Card Payment module', 'MODULE_PAYMENT_CARD_PAYMENT_STATUS', 'True', 'Do you want to accept Card Payment payments?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Payment Zone', 'MODULE_PAYMENT_CARD_PAYMENT_ZONE', '4', 'If a zone is selected, only enable this payment method for that zone.', '6', '2', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('Card Payment Exclude these Countries', 'MODULE_PAYMENT_CARD_PAYMENT_EXCEPT_COUNTRY', '', 'If countries are selected, disable this payment method for that countries.', '6', '3', 'tep_cfg_pull_down_zone_except_countries(MODULE_PAYMENT_CARD_PAYMENT_ZONE,', 'tep_get_zone_except_country', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('Card Payment Exclude these Zones', 'MODULE_PAYMENT_CARD_PAYMENT_EXCEPT_ZONE', '', 'If a zone is selected, disable this payment method for that zone.', '6', '4', 'tep_cfg_pull_down_zone_classes(','tep_get_zone_class_title', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort order of display.', 'MODULE_PAYMENT_CARD_PAYMENT_SORT_ORDER', '4', 'Sort order of Card Payment display. Lowest is displayed first.', '6', '5', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('Card Payment Set Order Status', 'MODULE_PAYMENT_CARD_PAYMENT_ORDER_STATUS_ID', '3', 'Set the status of orders made with this payment module to this value', '6', '6', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now())");
	  tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Display Name', 'MODULE_PAYMENT_CARD_PAYMENT_DISPLAY_NAME', 'BO Card Payment', 'Set the Display name to payment module', '6', '7', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Image', 'MODULE_PAYMENT_CARD_PAYMENT_IMAGE', 'card_payment.png', 'Set the Image of payment module', '6', '8', 'tep_cfg_file_field(', now())");
   }

    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_PAYMENT_CARD_PAYMENT_STATUS', 'MODULE_PAYMENT_CARD_PAYMENT_ZONE', 'MODULE_PAYMENT_CARD_PAYMENT_EXCEPT_ZONE', 'MODULE_PAYMENT_CARD_PAYMENT_EXCEPT_COUNTRY', 'MODULE_PAYMENT_CARD_PAYMENT_ORDER_STATUS_ID', 'MODULE_PAYMENT_CARD_PAYMENT_SORT_ORDER','MODULE_PAYMENT_CARD_PAYMENT_DISPLAY_NAME','MODULE_PAYMENT_CARD_PAYMENT_IMAGE');
    }
  }
?>