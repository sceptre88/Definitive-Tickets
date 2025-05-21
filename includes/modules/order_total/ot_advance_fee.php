<?php
/*
  osCommerce, Open Source E-Commerce Solutions 
http://www.oscommerce.com 

Copyright (c) 2003 osCommerce 

 

	Freeway eCommerce
	http://www.openfreeway.org
	Copyright (c) 2007 ZacWare

	Released under the GNU General Public License 
*/

// Check to ensure this file is included in osConcert!
defined('_FEXEC') or die();

define('MODULE_ORDER_TOTAL_ADVANCE_FEE_TITLE', '');
define('MODULE_ORDER_TOTAL_ADVANCE_FEE_DESCRIPTION', '');
define('MODULE_ORDER_TOTAL_ADVANCEFEE_STATUS', '');
define('MODULE_ORDER_TOTAL_ADVANCEFEE_SORT_ORDER', '');
define('MODULE_ORDER_TOTAL_ADVANCEFEE_TAX_CLASS', '');
define('MODULE_ORDER_TOTAL_ADVANCEFEE_SHOW_TICKET', '');
define('MODULE_ORDER_TOTAL_ADVANCEFEE_DETAIL', '');
define('MODULE_ORDER_TOTAL_ADVANCEFEE_DETAIL_TITLE', '');
define('MODULE_ORDER_TOTAL_ADVANCEFEE_DETAIL_VENUE', '');
define('MODULE_ORDER_TOTAL_ADVANCEFEE_DETAIL_DATE', '');
define('MODULE_ORDER_TOTAL_ADVANCEFEE_DETAIL_TIME', '');
define('MODULE_ORDER_TOTAL_ADVANCEFEE_DETAIL_PRICE', '');



  class ot_advance_fee {
    var $title, $output;

    function __construct() {
      $this->code = 'ot_advance_fee';
	  

      $this->title = MODULE_ORDER_TOTAL_ADVANCE_FEE_TITLE;
      $this->description = MODULE_ORDER_TOTAL_ADVANCE_FEE_DESCRIPTION;
      $this->enabled = ((MODULE_ORDER_TOTAL_ADVANCEFEE_STATUS == 'true') ? true : false);
      $this->sort_order = MODULE_ORDER_TOTAL_ADVANCEFEE_SORT_ORDER;

      $this->output = array();
    }

    function process() {
      global $order, $currencies, $cart;
	  require_once('includes/functions/categories_lookup.php');
	  
	      $tax = tep_get_tax_rate(MODULE_ORDER_TOTAL_ADVANCEFEE_TAX_CLASS, $order->delivery['country']['id'], $order->delivery['zone_id']);
          $tax_description = tep_get_tax_description(MODULE_ORDER_TOTAL_ADVANCEFEE_TAX_CLASS, $order->delivery['country']['id'], $order->delivery['zone_id']);
				
         //calculate fees for show only
		 $advancefee_categories = array();
   		 for ($i=0, $n=sizeof($order->products); $i<$n; $i++) 
			{   $cats=explode('_',(tep_get_product_path(tep_get_prid($order->products[$i]['id'])))); 
		        //if (MODULE_ORDER_TOTAL_ADVANCEFEE_SHOW_TICKET == "Ticket"){
					$qty = $order->products[$i]['qty'];
				//}else{
				//$qty = 1;}
				$top_cat_id = $cats[0];
				if (array_key_exists($top_cat_id, $advancefee_categories)){
				$advancefee_categories[$top_cat_id] = $advancefee_categories[$top_cat_id] + $qty;
				}else{
				$advancefee_categories [ $top_cat_id ] = $qty;
				}
			}
		 // $advancefee_categories_unique = array_unique($advancefee_categories);
		  $fee = 0;
		  foreach ($advancefee_categories as $key => $value){
			$the_fee_query= tep_db_query("select categories_advance_fee from " . TABLE_CATEGORIES . " where categories_id= '" . tep_db_input($key) . "'");
			$the_fee = tep_db_fetch_array($the_fee_query);		
			if (MODULE_ORDER_TOTAL_ADVANCEFEE_SHOW_TICKET == "Ticket"){
			 $fee_int = $the_fee['categories_advance_fee'] * $value;
		       }else{
			  $fee_int = $the_fee['categories_advance_fee'];	
			   }
			 $fee = $fee + $fee_int;	
        // show details in the order total listings

		if (MODULE_ORDER_TOTAL_ADVANCEFEE_DETAIL == "Yes" && $fee > 0 && basename($_SERVER['PHP_SELF']) == 'checkout_confirmation.php')
		{	require_once('includes/functions/categories_lookup.php');
			list($heading_name, $heading_venue,  $heading_date, $heading_time) = categories_lookup($key);
			$title_ext .= '<span class = "smallText">';
			if (MODULE_ORDER_TOTAL_ADVANCEFEE_DETAIL_TITLE == "Yes"){
			$title_ext .= $heading_name. ', ';}
			if (MODULE_ORDER_TOTAL_ADVANCEFEE_DETAIL_VENUE == "Yes"){
			$title_ext .= $heading_venue. ', ';}
			if (MODULE_ORDER_TOTAL_ADVANCEFEE_DETAIL_DATE == "Yes"){
			$title_ext .= $heading_date. ', ';}
			if (MODULE_ORDER_TOTAL_ADVANCEFEE_DETAIL_TIME == "Yes"){
			$title_ext .= $heading_time. ', ';}
			if (MODULE_ORDER_TOTAL_ADVANCEFEE_DETAIL_PRICE == "Yes"){
			$title_ext .= ' (' . $currencies->format($fee_int+ tep_calculate_tax($fee_int, $tax)) ;

			//remove extra space from format
			$title_ext = substr($title_ext, 0, -1);
			//add bracket
			$title_ext = $title_ext .'), ';
			}			
			//removing trailing comma
			$title_ext = substr($title_ext, 0, -2);
			$title_ext .= '</span><br>'; 
		}//end MODULE_ORDER_TOTAL_ADVANCEFEE_DETAIL
		  }
		  



          $order->info['tax'] += tep_calculate_tax($fee, $tax);
          $order->info['tax_groups']["$tax_description"] += tep_calculate_tax($fee, $tax);
          $order->info['total'] += $fee + tep_calculate_tax($fee, $tax);
		  $this->output[] = array('title' => $this->title . ':<br>'. $title_ext,
                                  'text' => $currencies->format(tep_add_tax($fee, $tax), true, $order->info['currency'], $order->info['currency_value']),
                                  'net_value' => $fee,
                                  'value' => tep_add_tax($fee, $tax));
        
    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_ORDER_TOTAL_ADVANCEFEE_STATUS'");
        $this->_check = tep_db_num_rows($check_query);
      }

      return $this->_check;
    }
    function collect_posts() {
	return false;
	}
   	function pre_confirmation_check() {
	return false;
	}
	function update_credit_account() {
	return false;
	}
	function apply_credit() {
	return false;
	}
    function keys() {
      return array('MODULE_ORDER_TOTAL_ADVANCEFEE_STATUS', 'MODULE_ORDER_TOTAL_ADVANCEFEE_SORT_ORDER',  'MODULE_ORDER_TOTAL_ADVANCEFEE_TAX_CLASS' , 'MODULE_ORDER_TOTAL_ADVANCEFEE_DETAIL', 'MODULE_ORDER_TOTAL_ADVANCEFEE_DETAIL_TITLE','MODULE_ORDER_TOTAL_ADVANCEFEE_DETAIL_VENUE', 'MODULE_ORDER_TOTAL_ADVANCEFEE_DETAIL_DATE', 'MODULE_ORDER_TOTAL_ADVANCEFEE_DETAIL_TIME', 'MODULE_ORDER_TOTAL_ADVANCEFEE_SHOW_TICKET', 'MODULE_ORDER_TOTAL_ADVANCEFEE_DETAIL_PRICE');
    }

    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Display Advance Fee', 'MODULE_ORDER_TOTAL_ADVANCEFEE_STATUS', 'true', 'Do you want to display the advance fee?', '6', '1','tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_ORDER_TOTAL_ADVANCEFEE_SORT_ORDER', '5', 'Sort order of display.', '6', '2', now())");
       tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Tax Class', 'MODULE_ORDER_TOTAL_ADVANCEFEE_TAX_CLASS', '0', 'Use the following tax class on the advance  fee.', '6', '7', 'tep_get_tax_class_title', 'tep_cfg_pull_down_tax_classes(', now())");
	   
	   // display
	   tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Detailed listings?', 'MODULE_ORDER_TOTAL_ADVANCEFEE_DETAIL', 'Yes', 'Do you want to display full details?', '6', '20','tep_cfg_select_option(array(\'Yes\', \'No\'), ', now())");
	   tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Details: include concert name?', 'MODULE_ORDER_TOTAL_ADVANCEFEE_DETAIL_TITLE', 'Yes', '', '6', '21','tep_cfg_select_option(array(\'Yes\', \'No\'), ', now())");	  
	   tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Details: include venue?', 'MODULE_ORDER_TOTAL_ADVANCEFEE_DETAIL_VENUE', 'Yes', '', '6', '22','tep_cfg_select_option(array(\'Yes\', \'No\'), ', now())");
	   tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Details: include date?', 'MODULE_ORDER_TOTAL_ADVANCEFEE_DETAIL_DATE', 'Yes', '', '6', '23','tep_cfg_select_option(array(\'Yes\', \'No\'), ', now())");
	  tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Details: include time?', 'MODULE_ORDER_TOTAL_ADVANCEFEE_DETAIL_TIME', 'Yes', '', '6', '24','tep_cfg_select_option(array(\'Yes\', \'No\'), ', now())");
	   tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Details: include fee?', 'MODULE_ORDER_TOTAL_ADVANCEFEE_DETAIL_PRICE', 'Yes', '', '6', '25','tep_cfg_select_option(array(\'Yes\', \'No\'), ', now())");
	    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Fee per show or per ticket?', 'MODULE_ORDER_TOTAL_ADVANCEFEE_SHOW_TICKET', 'Show', '', '6', '19','tep_cfg_select_option(array(\'Show\', \'Ticket\'), ', now())");
   
    }

    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }
  }
?>
