<?php

/*



  osCommerce, Open Source E-Commerce Solutions 

http://www.oscommerce.com 



Copyright (c) 2003 osCommerce 



Released under the GNU General Public License

*/



// Check to ensure this file is included in osConcert!

defined('_FEXEC') or die();



// Stop from parsing any further PHP code

// v2.3.3.1 now closes the session through a registered shutdown function

  function tep_exit() {

   exit();

  }

// Redirect to another page or site

  function tep_redirect($url) {

    if ( (strstr($url, "\n") != false) || (strstr($url, "\r") != false) ) { 

      tep_redirect(tep_href_link('index.php', '', 'NONSSL', false));

    }



    if ( ('ENABLE_SSL' == true) && (getenv('HTTPS') == 'on') ) { // We are loading an SSL page

      if (substr($url, 0, strlen(HTTP_SERVER . DIR_WS_HTTP_CATALOG)) == HTTP_SERVER . DIR_WS_HTTP_CATALOG) { // NONSSL url

        $url = HTTPS_SERVER . DIR_WS_HTTPS_CATALOG . substr($url, strlen(HTTP_SERVER . DIR_WS_HTTP_CATALOG)); // Change it to SSL

      }

    }



    if ( strpos($url, '&amp;') !== false ) {

      $url = str_replace('&amp;', '&', $url);

    }



    header('Location: ' . $url);



    tep_exit();

  }





////

// Parse the data used in the html tags to ensure the tags will not break

  function tep_parse_input_field_data($data, $parse) {

    return strtr(trim($data), $parse);

  }



  function tep_output_string($string, $translate = false, $protected = false) {

    if ($protected == true) {

      return htmlspecialchars($string);

    } else {

      if ($translate == false) {

        return tep_parse_input_field_data($string, array('"' => '&quot;'));

      } else {

        return tep_parse_input_field_data($string, $translate);

      }

    }

  }



  function tep_output_string_protected($string) {

    return tep_output_string($string, false, true);

  }



  function tep_sanitize_string($string) {

	$string = preg_replace('/ +/', ' ', trim($string));

    return preg_replace("/[<>]/", '_', $string);

  }



////

// Return a random row from a database query

  function tep_random_select($query) {

    $random_product = '';

    $random_query = tep_db_query($query);

    $num_rows = tep_db_num_rows($random_query);

    if ($num_rows > 0) {

      $random_row = tep_rand(0, ($num_rows - 1));

      tep_db_data_seek($random_query, $random_row);

      $random_product = tep_db_fetch_array($random_query);

    }



    return $random_product;

  }



////

// Return a product's name

// TABLES: products

  function tep_get_products_name($product_id, $language = '') {

    global $FSESSION;



    if (empty($language)) $language = $FSESSION->languages_id;



    $product_query = tep_db_query("select products_name from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . (int)$product_id . "' and language_id = '" . (int)$language . "'");

    $product = tep_db_fetch_array($product_query);



    return $product['products_name'];

  }



////

// Return a product's special price (returns nothing if there is no offer)

// TABLES: products



  function tep_get_customers_groups_id() {

    global $FSESSION;

    $customers_groups_query = tep_db_query("select customers_groups_id from " . TABLE_CUSTOMERS . " where customers_id =  '" . (int)$FSESSION->customer_id . "'");

    $customers_groups_id = tep_db_fetch_array($customers_groups_query);

    return $customers_groups_id['customers_groups_id'];

  }

  

  function tep_get_products_special_price($product_id,$sale_id=0,$cart_item_id='') {

  	global $FSESSION,$cart;

    $product_query = tep_db_query("select products_price, products_model,products_price_break from " . TABLE_PRODUCTS . " where products_id = '" . (int)$product_id . "'");

    if (tep_db_num_rows($product_query)) {

      $product = tep_db_fetch_array($product_query);



	  $product_price = $product['products_price'];

	  if ($product["products_price_break"]=="Y") return false;

    } else {

	  return false;

    }



    $customer_groups_id = tep_get_customers_groups_id();

	$specials_query = tep_db_query("select specials_new_products_price from " . TABLE_SPECIALS . " where products_id = '" . (int)$product_id . "' and status = '1' and customers_id = '" . (int)$FSESSION->customer_id . "' and customers_groups_id = '0'");

	if (!tep_db_num_rows($specials_query)) {

	  $specials_query = tep_db_query("select specials_new_products_price from " . TABLE_SPECIALS . " where products_id = '" . (int)$product_id . "' and status = '1' and customers_groups_id = '" . (int)$customer_groups_id . "' and customers_id = '0'");

	  if (!tep_db_num_rows($specials_query)) {

	    $specials_query = tep_db_query("select specials_new_products_price from " . TABLE_SPECIALS . " where products_id = '" . (int)$product_id . "' and status = '1' and customers_groups_id = '0' and customers_id = '0'");

	  }

	}

    //$specials_query = tep_db_query("select specials_new_products_price from " . TABLE_SPECIALS . " where products_id = '" . $product_id . "' and status");





	

	$specials_query = tep_db_query("select specials_new_products_price,customers_id,customers_groups_id from " . TABLE_SPECIALS . " where products_id = '" . (int)$product_id . "' and status = '1'");



    if (tep_db_num_rows($specials_query)) {

      $special = tep_db_fetch_array($specials_query);

	  	if ($FSESSION->customer_id>0 && $FSESSION->customer_id==$special["customers_id"]){

		  $special_price = $special['specials_new_products_price'];

		} else if ($customer_groups_id>0 && $customer_groups_id==$special["customers_groups_id"]){

		  $special_price = $special['specials_new_products_price'];

		} else if ($special["customers_id"]<=0 && $special["customers_groups_id"]<=0){

		  $special_price = $special['specials_new_products_price'];

		} else {

			$special_price=false;

		}

    } else {

	  $special_price = false;

    }

	

    if(substr($product['products_model'], 0, 4) == 'GIFT') 

	{    //Never apply a salededuction to Ian Wilson's Giftvouchers

      return $special_price;

    }



    $product_to_categories_query = tep_db_query("select categories_id from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . (int)$product_id . "'");

    $product_to_categories = tep_db_fetch_array($product_to_categories_query);

    $category = $product_to_categories['categories_id'];



	$option_where='';

	if ($sale_id>0) 

		$option_where=" sale_id=" . $sale_id . " and ";

	else $option_where=" sale_discount_type='S' and ";



	

	$sDate=getServerDate(true);

    $sale_query = tep_db_query("select sale_specials_condition, sale_deduction_value, sale_deduction_type,sale_id,sale_discount_type from " . TABLE_SALEMAKER_SALES . " where " . $option_where . " ((sale_categories_all='' and sale_products_selected='') or sale_categories_all like '%," . tep_db_input($category) . ",%' or sale_products_selected like '%," . (int)$product_id .",%') and sale_status = '1' and (sale_date_start <= '" . $sDate. "' or sale_date_start = '0000-00-00') and (sale_date_end >= '" . $sDate . "' or sale_date_end = '0000-00-00')");



    if (tep_db_num_rows($sale_query)) {

      $sale = tep_db_fetch_array($sale_query);

    } else {

	  return $special_price;

    }



    if (!$special_price) {

      $tmp_special_price = $product_price;

    } else {

      $tmp_special_price = $special_price;

    }

	if($cart_item_id!='' && $sale['sale_discount_type']=='S')

		$cart->contents[$cart_item_id]['salemaker_id']=$sale['sale_id'];

    switch ($sale['sale_deduction_type']) {

      case 0:

        $sale_product_price = $product_price - $sale['sale_deduction_value'];

        $sale_special_price = $tmp_special_price - $sale['sale_deduction_value'];

        break;

      case 1:

        $sale_product_price = $product_price - (($product_price * $sale['sale_deduction_value']) / 100);

        $sale_special_price = $tmp_special_price - (($tmp_special_price * $sale['sale_deduction_value']) / 100);

        break;

      case 2:

        $sale_product_price = $sale['sale_deduction_value'];

        $sale_special_price = $sale['sale_deduction_value'];

        break;

      default:

        return $special_price;

    }



    if ($sale_product_price < 0) {

      $sale_product_price = 0;

    }



    if ($sale_special_price < 0) {

      $sale_special_price = 0;

    }



    if (!$special_price) {

      return number_format($sale_product_price, 4, '.', '');

	} else {

      switch($sale['sale_specials_condition']){

        case 0:

          return number_format($sale_product_price, 4, '.', '');

          break;

        case 1:

          return number_format($special_price, 4, '.', '');

          break;

        case 2:

          return number_format($sale_special_price, 4, '.', '');

          break;

        default:

          return number_format($special_price, 4, '.', '');

      }

    }

  }

  function tep_get_salemaker_price($special_price,$product_price,$sale){

    if (!$special_price) {

      $tmp_special_price = $product_price;

    } else {

      $tmp_special_price = $special_price;

    }



    switch ($sale['sale_deduction_type']) {

      case 0:

        $sale_product_price = $product_price - $sale['sale_deduction_value'];

        $sale_special_price = $tmp_special_price - $sale['sale_deduction_value'];

        break;

      case 1:

        $sale_product_price = $product_price - (($product_price * $sale['sale_deduction_value']) / 100);

        $sale_special_price = $tmp_special_price - (($tmp_special_price * $sale['sale_deduction_value']) / 100);

        break;

      case 2:

        $sale_product_price = $sale['sale_deduction_value'];

        $sale_special_price = $sale['sale_deduction_value'];

        break;

      default:

        return $special_price;

    }



    if ($sale_product_price < 0) {

      $sale_product_price = 0;

    }



    if ($sale_special_price < 0) {

      $sale_special_price = 0;

    }



    if (!$special_price) {

      return number_format($sale_product_price, 4, '.', '');

	} else {

      switch($sale['sale_specials_condition']){

        case 0:

          return number_format($sale_product_price, 4, '.', '');

          break;

        case 1:

          return number_format($special_price, 4, '.', '');

          break;

        case 2:

          return number_format($sale_special_price, 4, '.', '');

          break;

        default:

          return number_format($special_price, 4, '.', '');

      }

    }

  }



  function tep_get_products_special_price_only($products_id){

  	global $FSESSION;

	$customer_id=$FSESSION->get('customer_id','int',0);

	

    $customer_groups_id = tep_get_customers_groups_id($customer_id);

	$specials_query = tep_db_query("select specials_new_products_price,customers_id,customers_groups_id from " . TABLE_SPECIALS . " where products_id = '" . (int)$products_id . "' and status = '1'");



		if (tep_db_num_rows($specials_query)) {

		  $special = tep_db_fetch_array($specials_query);

			if ($customer_id>0 && $customer_id==$special["customers_id"]){

			  $special_price = $special['specials_new_products_price'];

			} else if ($customer_groups_id>0 && $customer_groups_id==$special["customers_groups_id"]){

			  $special_price = $special['specials_new_products_price'];

			} else if ($special["customers_id"]<=0 && $special["customers_groups_id"]<=0){

			  $special_price = $special['specials_new_products_price'];

			} else {

				$special_price=false;

			}

		} else {

		  $special_price = false;

		}

		return $special_price;

	}

////

// Return a product's stock

// TABLES: products

  function tep_get_products_stock($products_id) {

    $products_id = tep_get_prid($products_id);

    $stock_query = tep_db_query("select products_quantity from " . TABLE_PRODUCTS . " where products_id = '" . (int)$products_id . "'");

    $stock_values = tep_db_fetch_array($stock_query);



    return $stock_values['products_quantity'];

  }



////

// Check if the required stock is available

// If insufficent stock is available return an out of stock message

  function tep_check_stock($products_id, $products_quantity) {

    $stock_left = tep_get_products_stock($products_id) - $products_quantity;

    $out_of_stock = '';



    if ($stock_left < 0) {

      $out_of_stock = '<span class="markProductOutOfStock">' . STOCK_MARK_PRODUCT_OUT_OF_STOCK . '</span>';

    }



    return $out_of_stock;

  }

  //added Aug 2012 sakwoya@sakwoya.co.uk (Graeme Tyson)

    function tep_check_stock_ga($products_id, $products_quantity) {

	$stock_av=tep_get_products_stock($products_id);

    $stock_left = $stock_av - $products_quantity;

    $out_of_stock_ga = '';



    if ($stock_left < 0) {

      $out_of_stock_ga = '<span class="markProductOutOfStock">' .STOCK_MARK_PRODUCT_OUT_OF_STOCK_GA .' '. $stock_av.OUT_OF_STOCK_CANT_CHECKOUT_GA_TRAILER.'</span>';

    }



    return $out_of_stock_ga;

  }



////

// Break a word in a string if it is longer than a specified length ($len)

  function tep_break_string($string, $len, $break_char = '-') {

    $l = 0;

    $output = '';

    for ($i=0, $n=strlen($string); $i<$n; $i++) {

      $char = substr($string, $i, 1);

      if ($char != ' ') {

        $l++;

      } else {

        $l = 0;

      }

      if ($l > $len) {

        $l = 1;

        $output .= $break_char;

      }

      $output .= $char;

    }



    return $output;

  }



////

// Return all HTTP GET variables, except those passed as a parameter

  function tep_get_all_get_params($exclude_array = '') {

    global $FGET,$FSESSION;



	if (!is_array($exclude_array)) $exclude_array = array();

	$exclude_array[]=$FSESSION->NAME;

	$exclude_array[]='error';

	$exclude_array[]='x';

	$exclude_array[]='y';

	$exclude_array[]='option';

	$exclude_array[]='openfile';

	$exclude_array[]='component';

    $get_url = '';

    if (is_array($FGET) && (sizeof($FGET) > 0)) {

      reset($FGET);

		foreach($FGET as $key => $value)

		{  

        if ( (strlen($value) > 0) && ($key != $FSESSION->NAME) && ($key != 'error') && (!in_array($key, $exclude_array)) && ($key != 'x') && ($key != 'y') ) {

          $get_url .= $key . '=' . rawurlencode(stripslashes($value)) . '&';

        }

      }

    }



    return $get_url;

  }



////

// Returns an array with countries

// TABLES: countries

  function tep_get_countries($countries_id = '', $with_iso_codes = false) {

    $countries_array = array();

    if (tep_not_null($countries_id)) {

      if ($with_iso_codes == true) {

        $countries = tep_db_query("select countries_name, countries_iso_code_2, countries_iso_code_3 from " . TABLE_COUNTRIES . " where countries_id = '" . (int)$countries_id . "' order by countries_name");

        $countries_values = tep_db_fetch_array($countries);

        $countries_array = array('countries_name' => $countries_values['countries_name'],

                                 'countries_iso_code_2' => $countries_values['countries_iso_code_2'],

                                 'countries_iso_code_3' => $countries_values['countries_iso_code_3']);

      } else {

        $countries = tep_db_query("select countries_name from " . TABLE_COUNTRIES . " where countries_id = '" . (int)$countries_id . "'");

        $countries_values = tep_db_fetch_array($countries);

        $countries_array = array('countries_name' => $countries_values['countries_name']);

      }

    } else {

      $countries = tep_db_query("select countries_id, countries_name from " . TABLE_COUNTRIES . " where countries_id<999 order by countries_name");

      while ($countries_values = tep_db_fetch_array($countries)) {

        $countries_array[] = array('countries_id' => $countries_values['countries_id'],

                                   'countries_name' => $countries_values['countries_name']);

      }

    }



    return $countries_array;

  }



////

// Alias function to tep_get_countries, which also returns the countries iso codes

  function tep_get_countries_with_iso_codes($countries_id) {

    return tep_get_countries($countries_id, true);

  }



////

// Generate a path to categories

  function tep_get_path($current_category_id = '') {

    global $cPath_array;



    if (tep_not_null($current_category_id)) {

		//PHP8 -->		

		if(!is_array($cPath_array)){

		$cPath_array=array();

		}

		//PHP8 -->

      $cp_size = sizeof($cPath_array);//PHP8 -->

      if ($cp_size == 0) {

        $cPath_new = $current_category_id;

      } else {

        $cPath_new = '';

        $last_category_query = tep_db_query("select parent_id from " . TABLE_CATEGORIES . " where categories_id = '" . (int)$cPath_array[($cp_size-1)] . "'");

        $last_category = tep_db_fetch_array($last_category_query);



        $current_category_query = tep_db_query("select parent_id from " . TABLE_CATEGORIES . " where categories_id = '" . (int)$current_category_id . "'");

        $current_category = tep_db_fetch_array($current_category_query);



        if ($last_category['parent_id'] == $current_category['parent_id']) {

          for ($i=0; $i<($cp_size-1); $i++) {

            $cPath_new .= '_' . $cPath_array[$i];

          }

        } else {

          for ($i=0; $i<$cp_size; $i++) {

            $cPath_new .= '_' . $cPath_array[$i];

          }

        }

        $cPath_new .= '_' . $current_category_id;



        if (substr($cPath_new, 0, 1) == '_') {

          $cPath_new = substr($cPath_new, 1);

        }

     }

    } else {      

      $cPath_new = implode('_', $cPath_array);

    }

    return 'cPath=' . $cPath_new;

  }



////

// Returns the clients browser

  function tep_browser_detect($component) {

    return stristr($_SERVER['HTTP_USER_AGENT'], $component);

  }



////

// Alias function to tep_get_countries()

  function tep_get_country_name($country_id) {

    $country_array = tep_get_countries($country_id);



    return $country_array['countries_name'];

  }



////

// Returns the zone (State/Province) name

// TABLES: zones

  function tep_get_zone_name($country_id, $zone_id, $default_zone) {

    $zone_query = tep_db_query("select zone_name from " . TABLE_ZONES . " where zone_country_id = '" . (int)$country_id . "' and zone_id = '" . (int)$zone_id . "'");

    if (tep_db_num_rows($zone_query)) {

      $zone = tep_db_fetch_array($zone_query);

      return $zone['zone_name'];

    } else {

      return $default_zone;

    }

  }



////

// Returns the zone (State/Province) code

// TABLES: zones

  function tep_get_zone_code($country_id, $zone_id, $default_zone) {

    $zone_query = tep_db_query("select zone_code from " . TABLE_ZONES . " where zone_country_id = '" . (int)$country_id . "' and zone_id = '" . (int)$zone_id . "'");

    if (tep_db_num_rows($zone_query)) {

      $zone = tep_db_fetch_array($zone_query);

      return $zone['zone_code'];

    } else {

      return $default_zone;

    }

  }

  





////

// Wrapper function for round()

  function tep_round($number, $precision) {

    if (strpos($number, '.') && (strlen(substr($number, strpos($number, '.')+1)) > $precision)) {

      $number = substr($number, 0, strpos($number, '.') + 1 + $precision + 1);



      if (substr($number, -1) >= 5) {

        if ($precision > 1) {

          $number = substr($number, 0, -1) + ('0.' . str_repeat(0, $precision-1) . '1');

        } elseif ($precision == 1) {

          $number = substr($number, 0, -1) + 0.1;

        } else {

          $number = substr($number, 0, -1) + 1;

        }

      } else {

        $number = substr($number, 0, -1);

      }

    }



    return $number;

  }



////

// Returns the tax rate for a zone / class

// TABLES: tax_rates, zones_to_geo_zones

  function tep_get_tax_rate($class_id, $country_id = -1, $zone_id = -1) {

    global $FSESSION;



    if ( ($country_id == -1) && ($zone_id == -1) ) {

      if (!$FSESSION->is_registered('customer_id')) {

        $country_id = STORE_COUNTRY;

        $zone_id = STORE_ZONE;

      } else {

        $country_id = $FSESSION->customer_country_id;

        $zone_id = $FSESSION->customer_zone_id;

      }

    }



    $tax_query = tep_db_query("select sum(tax_rate) as tax_rate from " . TABLE_TAX_RATES . " tr left join " . TABLE_ZONES_TO_GEO_ZONES . " za on (tr.tax_zone_id = za.geo_zone_id) left join " . TABLE_GEO_ZONES . " tz on (tz.geo_zone_id = tr.tax_zone_id) where (za.zone_country_id is null or za.zone_country_id = '0' or za.zone_country_id = '" . (int)$country_id . "') and (za.zone_id is null or za.zone_id = '0' or za.zone_id = '" . (int)$zone_id . "') and tr.tax_class_id = '" . (int)$class_id . "' group by tr.tax_priority");

    if (tep_db_num_rows($tax_query)) {

      $tax_multiplier = 1.0;

      while ($tax = tep_db_fetch_array($tax_query)) {

        $tax_multiplier *= 1.0 + ($tax['tax_rate'] / 100);

      }

      return ($tax_multiplier - 1.0) * 100;

    } else {

      return 0;

    }

  }



////

// Return the tax description for a zone / class

// TABLES: tax_rates;

  function tep_get_tax_description($class_id, $country_id, $zone_id) {

    $tax_query = tep_db_query("select tax_description from " . TABLE_TAX_RATES . " tr left join " . TABLE_ZONES_TO_GEO_ZONES . " za on (tr.tax_zone_id = za.geo_zone_id) left join " . TABLE_GEO_ZONES . " tz on (tz.geo_zone_id = tr.tax_zone_id) where (za.zone_country_id is null or za.zone_country_id = '0' or za.zone_country_id = '" . (int)$country_id . "') and (za.zone_id is null or za.zone_id = '0' or za.zone_id = '" . (int)$zone_id . "') and tr.tax_class_id = '" . (int)$class_id . "' order by tr.tax_priority");

    if (tep_db_num_rows($tax_query)) {

      $tax_description = '';

      while ($tax = tep_db_fetch_array($tax_query)) {

        $tax_description .= $tax['tax_description'] . ' + ';

      }

      $tax_description = substr($tax_description, 0, -3);



      return $tax_description;

    } else {

      return TEXT_UNKNOWN_TAX_RATE;

    }

  }



////

// Add tax to a products price

  function tep_add_tax($price, $tax,$deposit=100) {

    global $currencies;

    if ( (DISPLAY_PRICE_WITH_TAX == 'true') && ($tax > 0) ) {

      return tep_round($price, $currencies->currencies[DEFAULT_CURRENCY]['decimal_places']) + tep_calculate_tax($price, $tax);

    } else {

      return tep_round($price, $currencies->currencies[DEFAULT_CURRENCY]['decimal_places']);

    }

  }



// Calculates Tax rounding the result

  function tep_calculate_tax($price, $tax) {

    global $currencies;



    return tep_round($price * $tax / 100, $currencies->currencies[DEFAULT_CURRENCY]['decimal_places']);

  }



////

// Return the number of products in a category

// TABLES: products, products_to_categories, categories

  function tep_count_products_in_category($category_id, $include_inactive = false) {

    $products_count = 0;

    if ($include_inactive == true) {

      $products_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where p.products_id = p2c.products_id and p2c.categories_id = '" . (int)$category_id . "'");

    } else {

      $products_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where p.products_id = p2c.products_id and p.products_status = '1' and p2c.categories_id = '" . (int)$category_id . "'");

    }

    $products = tep_db_fetch_array($products_query);

    $products_count += $products['total'];



    $child_categories_query = tep_db_query("select categories_id from " . TABLE_CATEGORIES . " where parent_id = '" . (int)$category_id . "'");

    if (tep_db_num_rows($child_categories_query)) {

      while ($child_categories = tep_db_fetch_array($child_categories_query)) {

        $products_count += tep_count_products_in_category($child_categories['categories_id'], $include_inactive);

      }

    }



    return $products_count;

  }



////

// Return true if the category has subcategories

// TABLES: categories

  function tep_has_category_subcategories($category_id) {

    $child_category_query = tep_db_query("select count(*) as count from " . TABLE_CATEGORIES . " where parent_id = '" . (int)$category_id . "'");

    $child_category = tep_db_fetch_array($child_category_query);



    if ($child_category['count'] > 0) {

      return true;

    } else {

      return false;

    }

  }



////

// Returns the address_format_id for the given country

// TABLES: countries;

  function tep_get_address_format_id($country_id) {

    $address_format_query = tep_db_query("select address_format_id as format_id from " . TABLE_COUNTRIES . " where countries_id = '" . (int)$country_id . "'");

    if (tep_db_num_rows($address_format_query)) {

      $address_format = tep_db_fetch_array($address_format_query);

      return $address_format['format_id'];

    } else {

      return '1';

    }

  }

  

// Return a formatted address

// TABLES: address_format

  function tep_address_format($address_format_id, $address, $html, $boln, $eoln) {

    $address_format_query = tep_db_query("select address_format as format from " . TABLE_ADDRESS_FORMAT . " where address_format_id = '" . (int)$address_format_id . "'");

    $address_format = tep_db_fetch_array($address_format_query);



    $company = tep_output_string_protected($address['company']);

    if (isset($address['firstname']) && tep_not_null($address['firstname'])) {

      $firstname = tep_output_string_protected($address['firstname']);

      $lastname = tep_output_string_protected($address['lastname']);

    } elseif (isset($address['name']) && tep_not_null($address['name'])) {

      $firstname = tep_output_string_protected($address['name']);

      $lastname = '';

    } else {

      $firstname = '';

      $lastname = '';

    }

	$customer_email = tep_output_string_protected($address['customer_email']);

	$customer_phone = tep_output_string_protected($address['customer_phone']);

	//$billing_phone = tep_output_string_protected($address['billing_phone']);

    $street = tep_output_string_protected($address['street_address']);

    $suburb = tep_output_string_protected($address['suburb']);

    $city = tep_output_string_protected($address['city']);

    $state = tep_output_string_protected($address['state']);

    if (isset($address['country_id']) && tep_not_null($address['country_id'])) {

      $country = tep_get_country_name($address['country_id']);



      if (isset($address['zone_id']) && tep_not_null($address['zone_id'])) {

        $state = tep_get_zone_name($address['country_id'], $address['zone_id'], $state);

      }

    } elseif (isset($address['country']) && tep_not_null($address['country'])) {

      //$country = tep_output_string_protected($address['country']['title']);

	  $country = tep_output_string_protected($address['country']); 

    } else {

      $country = '';

    }

    $postcode = tep_output_string_protected($address['postcode']);

    $zip = $postcode;



    if ($html) {

// HTML Mode

      $HR = '<hr>';

      $hr = '<hr>';

      if ( ($boln == '') && ($eoln == "\n") ) { // Values not specified, use rational defaults

        $CR = '<br>';

        $cr = '<br>';

        $eoln = $cr;

      } else { // Use values supplied

        $CR = $eoln . $boln;

        $cr = $CR;

      }

    } else {

// Text Mode

      $CR = $eoln;

      $cr = $CR;

      $HR = '----------------------------------------';

      $hr = '----------------------------------------';

    }



    $statecomma = '';

    $streets = $street;

    if ($suburb != '') $streets = $street . $cr . $suburb;

    if ($state != '') $statecomma = $state . ', ';



    $fmt = $address_format['format'];

    eval("\$address = \"$fmt\";");



    if ( (ACCOUNT_COMPANY == 'true') && (tep_not_null($company)) ) {

      $address = $company . $cr . $address;

    }



    return $address;

  }



  ////

// Return a formatted address

// TABLES: customers, address_book

  function tep_address_label($customers_id, $address_id = 1, $html = false, $boln = '', $eoln = "\n") {

   	 /*$cust=tep_db_query("select customers_default_address_id from customers where customers_id='". (int)$customers_id  ."'");

	 $custo=tep_db_fetch_array($cust);

	  // to set the selected address to display

	$address_id=$custo['customers_default_address_id'];*/



    $address_query = tep_db_query("select entry_firstname as firstname, entry_lastname as lastname, entry_company as company, entry_customer_email as customer_email, entry_customer_phone as customer_phone, entry_street_address as street_address, entry_suburb as suburb, entry_city as city, entry_postcode as postcode, entry_state as state, entry_zone_id as zone_id, entry_country_id as country_id from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . (int)$customers_id . "' and address_book_id = '" . (int)$address_id . "'");

    $address = tep_db_fetch_array($address_query);



    $format_id = tep_get_address_format_id($address['country_id']);



    return tep_address_format($format_id, $address, $html, $boln, $eoln);

  }



  function tep_row_number_format($number) {

    if ( ($number < 10) && (substr($number, 0, 1) != '0') ) $number = '0' . $number;



    return $number;

  }



  function tep_get_categories($categories_array = '', $parent_id = '0', $indent = '') {

    global $FSESSION;



    if (!is_array($categories_array)) $categories_array = array();



    $categories_query = tep_db_query("select c.categories_id, cd.categories_name from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where parent_id = '" . (int)$parent_id . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$FSESSION->languages_id . "' order by sort_order, cd.categories_name");

    while ($categories = tep_db_fetch_array($categories_query)) {

      $categories_array[] = array('id' => $categories['categories_id'],

                                  'text' => $indent . $categories['categories_name']);



      if ($categories['categories_id'] != $parent_id) {

        $categories_array = tep_get_categories($categories_array, $categories['categories_id'], $indent . '&nbsp;&nbsp;');

      }

    }



    return $categories_array;

  }



  function tep_get_manufacturers($manufacturers_array = '') {

    if (!is_array($manufacturers_array)) $manufacturers_array = array();



    $manufacturers_query = tep_db_query("select manufacturers_id, manufacturers_name from " . TABLE_MANUFACTURERS . " order by manufacturers_name");

    while ($manufacturers = tep_db_fetch_array($manufacturers_query)) {

      $manufacturers_array[] = array('id' => $manufacturers['manufacturers_id'], 'text' => $manufacturers['manufacturers_name']);

    }



    return $manufacturers_array;

  }



////

// Return all subcategory IDs

// TABLES: categories

  function tep_get_subcategories(&$subcategories_array, $parent_id = 0) {

    $subcategories_query = tep_db_query("select categories_id from " . TABLE_CATEGORIES . " where parent_id = '" . (int)$parent_id . "'");

    while ($subcategories = tep_db_fetch_array($subcategories_query)) {

      $subcategories_array[sizeof($subcategories_array)] = $subcategories['categories_id'];

      if ($subcategories['categories_id'] != $parent_id) {

        tep_get_subcategories($subcategories_array, $subcategories['categories_id']);

      }

    }

  }



// Output a raw date string in the selected locale date format

// $raw_date needs to be in this format: YYYY-MM-DD HH:MM:SS

  function tep_date_long($raw_date) {

    if ( ($raw_date == '0000-00-00 00:00:00') || ($raw_date == '') ) return false;



    $year = (int)substr($raw_date, 0, 4);

    $month = (int)substr($raw_date, 5, 2);

    $day = (int)substr($raw_date, 8, 2);

    $hour = (int)substr($raw_date, 11, 2);

    $minute = (int)substr($raw_date, 14, 2);

    $second = (int)substr($raw_date, 17, 2);



    return strftime(DATE_FORMAT_LONG, mktime($hour,$minute,$second,$month,$day,$year));

  }



////

// Output a raw date string in the selected locale date format

// $raw_date needs to be in this format: YYYY-MM-DD HH:MM:SS

// NOTE: Includes a workaround for dates before 01/01/1970 that fail on windows servers

  function tep_date_short($raw_date) {

    if ( ($raw_date == '0000-00-00 00:00:00') || empty($raw_date) ) return false;



    $year = substr($raw_date, 0, 4);

    $month = (int)substr($raw_date, 5, 2);

    $day = (int)substr($raw_date, 8, 2);

    $hour = (int)substr($raw_date, 11, 2);

    $minute = (int)substr($raw_date, 14, 2);

    $second = (int)substr($raw_date, 17, 2);

	if (@date('Y', mktime($hour, $minute, $second, $month, $day, $year)) == $year) {

      return date(DATE_FORMAT, mktime($hour, $minute, $second, $month, $day, $year));

    } else {

      return preg_replace('/2037$/', $year, date(DATE_FORMAT, mktime($hour, $minute, $second, $month, $day, 2037)));

    }

  }







////

// Recursively go through the categories and retreive all parent categories IDs

// TABLES: categories

  function tep_get_parent_categories(&$categories, $categories_id) {

    $parent_categories_query = tep_db_query("select parent_id from " . TABLE_CATEGORIES . " where categories_id = '" . (int)$categories_id . "'");

    while ($parent_categories = tep_db_fetch_array($parent_categories_query)) {

      if ($parent_categories['parent_id'] == 0) return true;

      $categories[sizeof($categories)] = $parent_categories['parent_id'];

      if ($parent_categories['parent_id'] != $categories_id) {

        tep_get_parent_categories($categories, $parent_categories['parent_id']);

      }

    }

  }



////

// Construct a category path to the product

// TABLES: products_to_categories

  function tep_get_product_path($products_id) {

    $cPath = '';	

	//echo "select p2c.categories_id from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where p.products_id = '" . (int)$products_id . "' and p.products_status = '1' and p.products_id = p2c.products_id limit 1";



    $category_query = tep_db_query("select p2c.categories_id from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where p.products_id = '" . (int)$products_id . "' and p.products_status = '1' and p.products_id = p2c.products_id limit 1");

    if (tep_db_num_rows($category_query)) {

      $category = tep_db_fetch_array($category_query);



      $categories = array();

      tep_get_parent_categories($categories, $category['categories_id']);



      $categories = array_reverse($categories);



      $cPath = implode('_', $categories);



      if (tep_not_null($cPath)) $cPath .= '_';

      $cPath .= $category['categories_id'];

    }



    return $cPath;

  }



////

// Return a product ID with attributes

  function tep_get_uprid($prid, $params) {

    if (is_numeric($prid)) {

      $uprid = (int)$prid;



      if (is_array($params) && (sizeof($params) > 0)) {

        $attributes_check = true;

        $attributes_ids = '';



        foreach($params as $option => $value) {

          if (is_numeric($option) && is_numeric($value)) {

            $attributes_ids .= '{' . (int)$option . '}' . (int)$value;

          } else {

            $attributes_check = false;

            break;

          }

        }



        if ($attributes_check == true) {

          $uprid .= $attributes_ids;

        }

      }

    } else {

      $uprid = tep_get_prid($prid);



      if (is_numeric($uprid)) {

        if (strpos($prid, '{') !== false) {

          $attributes_check = true;

          $attributes_ids = '';



// strpos()+1 to remove up to and including the first { which would create an empty array element in explode()

        $attributes = explode('{', substr($prid, strpos($prid, '{')+1));

 

        for ($i=0, $n=sizeof($attributes); $i<$n; $i++) {

          $pair = explode('}', $attributes[$i]);

 

          if (is_numeric($pair[0]) && is_numeric($pair[1])) {

            $attributes_ids .= '{' . (int)$pair[0] . '}' . (int)$pair[1];

          } else {

            $attributes_check = false;

            break;

          }

        }

 

        if ($attributes_check == true) {

          $uprid .= $attributes_ids;

        }

      }

    } else {

      return false;

    }

  }

 

  return $uprid;

}







////

// Return a product ID from a product ID with attributes

  function tep_get_prid($uprid) {

    $pieces = explode('{', $uprid);



    if (is_numeric($pieces[0])) {

      return (int)$pieces[0];

    } else {

      return false;

  }

}







////

// Return a customer greeting

  function tep_customer_greeting() {

    global $FSESSION;

    if ($FSESSION->is_registered('customer_first_name') && $FSESSION->is_registered('customer_id')) {

      $greeting_string = sprintf(TEXT_GREETING_PERSONAL, tep_output_string_protected($FSESSION->customer_first_name), tep_href_link(FILENAME_PRODUCTS_NEW));

    } else {

      $greeting_string = sprintf(TEXT_GREETING_GUEST, tep_href_link(FILENAME_LOGIN, '', 'SSL'), tep_href_link(FILENAME_CREATE_ACCOUNT, '', 'SSL'));

    }

    return $greeting_string;

    

  }





////

//! Send email (text/html) using MIME

// This is the central mail function. The SMTP Server should be configured

// correct in php.ini

// Parameters:

// $to_name           The name of the recipient, e.g. "Jan Wildeboer"

// $to_email_address  The eMail address of the recipient,

//                    e.g. jan.wildeboer@gmx.de

// $email_subject     The subject of the eMail

// $email_text        The text of the eMail, may contain HTML entities

// $from_email_name   The name of the sender, e.g. Shop Administration

// $from_email_adress The eMail address of the sender,

//                    e.g. info@mytepshop.com



  function tep_mail($to_name, $to_email_address, $email_subject, $email_text, $from_email_name, $from_email_address) {

    if (SEND_EMAILS != 'true') return false;

 

    // Instantiate a new mail object

    $message = new email();



    // Build the text version

    $text = strip_tags($email_text);

    if (EMAIL_USE_HTML == 'true') {

      $message->add_html($email_text, $text);

    } else {

      $message->add_text($text);

    }

    // Send message

	//echo $text;

    $message->build_message();

    $message->send($to_name, $to_email_address, $from_email_name, $from_email_address, $email_subject);

  }



////

// Check if product has attributes

  // function tep_has_product_attributes($products_id) {

    // $attributes_query = tep_db_query("select count(*) as count from " . TABLE_PRODUCTS_ATTRIBUTES . " where products_id = '" . (int)$products_id . "'");

    // $attributes = tep_db_fetch_array($attributes_query);



    // if ($attributes['count'] > 0) {

      // return true;

    // } else {

      // return false;

    // }

  // }



////

// Get the number of times a word/character is present in a string

  function tep_word_count($string, $needle) {

    $temp_array = preg_split('/'.$needle.'/', $string);



    return sizeof($temp_array);

  }



  function tep_count_modules($modules = '') {

    $count = 0;



    if (empty($modules)) return $count;



    $modules_array = explode(';', $modules);



    for ($i=0, $n=sizeof($modules_array); $i<$n; $i++) {

      $class = substr($modules_array[$i], 0, strrpos($modules_array[$i], '.'));



      if (isset($GLOBALS[$class]) && is_object($GLOBALS[$class])) {

        if ($GLOBALS[$class]->enabled) {

          $count++;

        }

      }

    }



    return $count;

  }



  function tep_count_payment_modules() {

    return tep_count_modules(MODULE_PAYMENT_INSTALLED);

  }



  function tep_count_shipping_modules() {

    return tep_count_modules(MODULE_SHIPPING_INSTALLED);

  }



    function tep_create_random_value($length, $type = 'mixed') {

    if ( ($type != 'mixed') && ($type != 'chars') && ($type != 'digits')) $type = 'mixed';



    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    $digits = '0123456789';



    $base = '';



    if ( ($type == 'mixed') || ($type == 'chars') ) {

      $base .= $chars;

    }



    if ( ($type == 'mixed') || ($type == 'digits') ) {

      $base .= $digits;

    }



    $value = '';



    if (!class_exists('PasswordHash')) {

      include('includes/classes/passwordhash.php');

    }



    $hasher = new PasswordHash(10, true);



    do {

      $random = base64_encode($hasher->get_random_bytes($length));



      for ($i = 0, $n = strlen($random); $i < $n; $i++) {

        $char = substr($random, $i, 1);



        if ( strpos($base, $char) !== false ) {

          $value .= $char;

        }

      }

    } while ( strlen($value) < $length );



    if ( strlen($value) > $length ) {

      $value = substr($value, 0, $length);

    }



    return $value;

  }



  function tep_array_to_string($array, $exclude = '', $equals = '=', $separator = '&') {

    if (!is_array($exclude)) $exclude = array();

//<!--PHP8 -->

    $get_string = '';

	if (is_countable($array) && count($array)>0){

	//if (count($array) > 0) {

    //if (sizeof($array) > 0) {

      foreach($array as $key => $value) {

        if ( (!in_array($key, $exclude)) && ($key != 'x') && ($key != 'y') ) {

          $get_string .= $key . $equals . $value . $separator;

        }

      }

      $remove_chars = strlen($separator);

      $get_string = substr($get_string, 0, -$remove_chars);

    }



    return $get_string;

  }



  function tep_not_null($value) {

    if (is_array($value)) {

      if (sizeof($value) > 0) {

        return true;

      } else {

        return false;

      }

    } else {

      if (($value != '') && (strtolower($value) != 'null') && (strlen(trim($value)) > 0)) {

        return true;

      } else {

        return false;

      }

    }

  }



////

// Output the tax percentage with optional padded decimals

  function tep_display_tax_value($value, $padding = TAX_DECIMAL_PLACES) {

    if (strpos($value, '.')) {

      $loop = true;

      while ($loop) {

        if (substr($value, -1) == '0') {

          $value = substr($value, 0, -1);

        } else {

          $loop = false;

          if (substr($value, -1) == '.') {

            $value = substr($value, 0, -1);

          }

        }

      }

    }



    if ($padding > 0) {

      if ($decimal_pos = strpos($value, '.')) {

        $decimals = strlen(substr($value, ($decimal_pos+1)));

        for ($i=$decimals; $i<$padding; $i++) {

          $value .= '0';

        }

      } else {

        $value .= '.';

        for ($i=0; $i<$padding; $i++) {

          $value .= '0';

        }

      }

    }



    return $value;

  }



////

    function tep_currency_exists($code) {

    $code = tep_db_prepare_input($code);



    $currency_query = tep_db_query("select code from currencies where code = '" . tep_db_input($code) . "' limit 1");

    if (tep_db_num_rows($currency_query)) {

      $currency = tep_db_fetch_array($currency_query);

      return $currency['code'];

    } else {

      return false;

    }

  }



  function tep_string_to_int($string) {

    return (int)$string;

  }



////

// Parse and secure the cPath parameter values

  function tep_parse_category_path($cPath) {

// make sure the category IDs are integers

    $cPath_array = array_map('tep_string_to_int', explode('_', $cPath));



// make sure no duplicate category IDs exist which could lock the server in a loop

    $tmp_array = array();

    $n = sizeof($cPath_array);

    for ($i=0; $i<$n; $i++) {

      if (!in_array($cPath_array[$i], $tmp_array)) {

        $tmp_array[] = $cPath_array[$i];

      }

    }



    return $tmp_array;

  }



////

// Return a random value

  function tep_rand($min = null, $max = null) {

    static $seeded;



    if (!isset($seeded)) {

      mt_srand((double)microtime()*1000000);

      $seeded = true;

    }



    if (isset($min) && isset($max)) {

      if ($min >= $max) {

        return $min;

      } else {

        return mt_rand($min, $max);

      }

    } else {

      return mt_rand();

    }

  }



  function tep_setcookie($name, $value = '', $expire = 0, $path = '/', $domain = '', $secure = 0) {

    setcookie($name, $value, $expire, $path, (tep_not_null($domain) ? $domain : ''), $secure);

  }



  

  function tep_validate_ip_address($ip_address) {

    return filter_var($ip_address, FILTER_VALIDATE_IP, array('flags' => FILTER_FLAG_IPV4));

  }



  function tep_get_ip_address() {

    global $_SERVER;



    $ip_address = null;

    $ip_addresses = array();



    if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {

      foreach ( array_reverse(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])) as $x_ip ) {

        $x_ip = trim($x_ip);



        if (tep_validate_ip_address($x_ip)) {

          $ip_addresses[] = $x_ip;

        }

      }

    }



    if (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP'])) {

      $ip_addresses[] = $_SERVER['HTTP_CLIENT_IP'];

    }



    if (isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && !empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])) {

      $ip_addresses[] = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];

    }



    if (isset($_SERVER['HTTP_PROXY_USER']) && !empty($_SERVER['HTTP_PROXY_USER'])) {

      $ip_addresses[] = $_SERVER['HTTP_PROXY_USER'];

    }



    $ip_addresses[] = $_SERVER['REMOTE_ADDR'];



    foreach ( $ip_addresses as $ip ) {

      if (!empty($ip) && tep_validate_ip_address($ip)) {

        $ip_address = $ip;

        break;

      }

    }



    return $ip_address;

  }



  function tep_count_customer_orders($id = '', $check_session = true) {

    global $FSESSION;



    if (is_numeric($id) == false) {

      if ($FSESSION->is_registered('customer_id')) {

        $id = $FSESSION->customer_id;

      } else {

        return 0;

      }

    }



    if ($check_session == true) {

      if ( ($FSESSION->is_registered('customer_id') == false) || ($id != $FSESSION->customer_id) ) {

        return 0;

      }

    }



    $orders_check_query = tep_db_query("select count(*) as total from " . TABLE_ORDERS . " where customers_id = '" . (int)$id . "'");

    $orders_check = tep_db_fetch_array($orders_check_query);



    return $orders_check['total'];

  }



  function tep_count_customer_address_book_entries($id = '', $check_session = true) {

    global $FSESSION;



    if (is_numeric($id) == false) {

      if ($FSESSION->is_registered('customer_id')) {

        $id = $FSESSION->customer_id;

      } else {

        return 0;

      }

    }



    if ($check_session == true) {

      if ( ($FSESSION->is_registered('customer_id') == false) || ($id != $FSESSION->customer_id) ) {

        return 0;

      }

    }



    $addresses_query = tep_db_query("select count(*) as total from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . (int)$id . "'");

    $addresses = tep_db_fetch_array($addresses_query);



    return $addresses['total'];

  }



// nl2br() prior PHP 4.2.0 did not convert linefeeds on all OSs (it only converted \n)

// nl2br() prior PHP 4.2.0 did not convert linefeeds on all OSs (it only converted \n)

  function tep_convert_linefeeds($from, $to, $string) {

    return str_replace($from, $to, $string);

  }



  function tep_get_configuration_key_value($lookup) {

    $configuration_query_raw= tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key='" . tep_db_input($lookup) . "'");

    $configuration_query= tep_db_fetch_array($configuration_query_raw);

    $lookup_value= $configuration_query['configuration_value'];

    /*if ( !($lookup_value) ) {

      $lookup_value='<font color="FF0000">' . $lookup . '</font>';

    }*/

    return $lookup_value;

  }

////

//CLR 030228 Add function tep_decode_specialchars

// Decode string encoded with htmlspecialchars()

  function tep_decode_specialchars($string){

    $string=str_replace('&gt;', '>', $string);

    $string=str_replace('&lt;', '<', $string);

    $string=str_replace('&#039;', "'", $string);

    $string=str_replace('&quot;', "\"", $string);

    $string=str_replace('&amp;', '&', $string);



    return $string;

  }



// saved from old code

  function tep_output_warning($warning) {

    new errorBox(array(array('text' => tep_image(DIR_WS_ICONS . 'warning.gif', ICON_WARNING) . ' ' . $warning)));

  }

function tep_check_is_blocked_customer(){

	global $FSESSION;

	$group_sql = "select customers_id from " . TABLE_CUSTOMERS . " c where customers_id=" . (int)$FSESSION->customer_id . " and c.is_blocked='Y'";

	$group_query = tep_db_query($group_sql);

	$is_data = tep_db_fetch_array($group_query);

	if($is_data['customers_id'] != '') return true;

	//if (tep_db_num_rows($group_query)>0) return true;

	else return false;

}

function tep_check_is_suspended_customer(){

	global $FSESSION;

	$group_sql = "select customers_id from " . TABLE_CUSTOMERS . " c where customers_id=" . (int)$FSESSION->customer_id . 

					" and ((suspend_from!='0000-00-00' and suspend_from<=curdate() and (resume_from='0000-00-00' or resume_from>curdate()))

					or (resume_from!='0000-00-00' && resume_from>curdate()))";

	$group_query = tep_db_query($group_sql);

	$is_data = tep_db_fetch_array($group_query);

	if($is_data['customers_id'] != '') return true;

	//if (tep_db_num_rows($group_query)>0) return true;

	else return false;

}



function tep_get_product_price_for_order($products_id,$products_price){

  global $FSESSION;

	$query = tep_db_query("select g.customers_groups_discount from " . TABLE_CUSTOMERS_GROUPS . " g inner join  " . TABLE_CUSTOMERS  . " c on g.customers_groups_id = c.customers_groups_id and c.customers_id = '" . (int)$FSESSION->customer_id . "'");

	$query_result = tep_db_fetch_array($query);

	$customers_groups_discount = $query_result['customers_groups_discount'];

	$query = tep_db_query("select customers_discount from " . TABLE_CUSTOMERS . " where customers_id =  '" . (int)$FSESSION->customer_id . "'");

	$query_result = tep_db_fetch_array($query);

	$customer_discount = $query_result['customers_discount'];

	$customer_discount = $customer_discount + $customers_groups_discount;

	if ($customer_discount >= 0) {

	 $products_price = $products_price + $products_price * abs($customer_discount) / 100;

	} else {

	 $products_price = $products_price - $products_price * abs($customer_discount) / 100;

	}

	if ($special_price = tep_get_products_special_price($products_id)) $products_price = $special_price;

	return $products_price;

}



		//little function to check for ticket amounts and redirect to cart - stops hack attempts to purchase more than ticket limit

		function tep_check_ticket_limit($cust_id,$redirect)

		{

			if (is_numeric('CUSTOMER_TICKET_LIMIT') && ('CUSTOMER_TICKET_LIMIT' > 0)) { 

			

				$basket_query_raw ="select cb.products_id, p.products_model, cb.customers_basket_quantity from ".TABLE_CUSTOMERS_BASKET." cb, ".TABLE_PRODUCTS." p WHERE cb.customers_id = '".(int)$cust_id."' and p.products_id = cb.products_id";

			 $basket_query = tep_db_query($basket_query_raw);

			 $show_total=array();

			 while($result=tep_db_fetch_array($basket_query))  {

			 

			 if(isset($show_total[$result['products_model']])){

			 $show_total[$result['products_model']] = $show_total[$result['products_model']]+$result['customers_basket_quantity'];

			 }else{

			 

			 $show_total[$result['products_model']] = $result['customers_basket_quantity'];

			 }

			 }

			 //OK now go get the stuff from the orders table

			 

			 $customers_query_raw = "select  c.customers_firstname, sum(op.products_quantity ) as ordersum, op.products_model from " . TABLE_CUSTOMERS . " c, " . TABLE_ORDERS_PRODUCTS . " op, " . TABLE_ORDERS . " o where c.customers_id = o.customers_id and o.orders_id = op.orders_id and c.customers_id='" . (int)$cust_id . "' group by op.products_model order by op.products_model";

			//echo $customers_query_raw;

 			 $customers_query = tep_db_query($customers_query_raw);



		while ($customers = tep_db_fetch_array($customers_query))  {

  		

			 

			 if(isset($show_total[$customers['products_model']])){

			 $show_total[$customers['products_model']] = $show_total[$customers['products_model']]+$customers['ordersum'];

			 }else{

			 

			 $show_total[$customers['products_model']] = $customers['ordersum'];

			 }

			 }

			 

			 //we now have an array of shows and tickets bought/in cart

			 $oversubscribed=0;//could use false/true

			 foreach($show_total as $key => $value){

			 		if ($value <= CUSTOMER_TICKET_LIMIT){//it's OK so drop from the array

						unset($show_total[$key]);}

						else{//OTT so increment the flag

						$oversubscribed++;

						}

					 }

			 // check the flag

			 	if($oversubscribed>0){// send to cart page with a session holding the array

					$_SESSION['oversubscribed']=$show_total;

					//stop redirect if on shopping cart page so we can call the function from there

					      if ( $redirect ==1){			

					

					tep_redirect(tep_href_link(FILENAME_SHOPPING_CART));

				}

				}

			 }

			 }

			 

  

// //default mail handling function with type,merge_details,send_address details

	// function tep_send_default_email($type,$merge_details,$send_details,$filename=''){

		// $details=array();

		// $details['type']=$type;

		

		// $details['table']=TABLE_EMAIL_MESSAGES;

		// tep_get_template($details);      	  // get template using type and table   

		// if ($details['html_text']=="") {	

			// if($type!='PRD') return;

			// $fp=@fopen(DIR_FS_CATALOG . DIR_WS_TEMPLATES . TEMPLATE_NAME . "/images/product_email_template.html","r");

			// if (!$fp) return;

			// $details['html_text']=fread($fp,filesize(DIR_FS_CATALOG . DIR_WS_TEMPLATES . TEMPLATE_NAME . "/images/product_email_template.html"));

			// if ($details['html_text']=="") return false;

			// $details['text']=strip_tags($details['html_text']);

			// $details['format']="B";

		// }

		// tep_replace_template($details,$merge_details); // replace template content with merge details

		// tep_strip_html($details); // strip tags;

		// //send mails to given addresses

		// for ($icnt=0;$icnt<sizeof($send_details);$icnt++){

			// $details['to_name']=$send_details[$icnt]['to_name'];

			// $details['to_email']=$send_details[$icnt]['to_email'];

			// $details['from_name']=$send_details[$icnt]['from_name'];

			// $details['from_email']=$send_details[$icnt]['from_email'];

			// tep_send_email($details,true,$filename); // send email

		// }

		

	// }



function tep_send_default_email($type, $merge_details, $send_details, $filename = '') {

    $details = array();

    $details['type'] = $type;



    // Assuming TABLE_EMAIL_MESSAGES is the table where email templates are stored

    $details['table'] = TABLE_EMAIL_MESSAGES;

    tep_get_template($details); // get template using type and table



    // If the template is still not found or empty, return or handle accordingly

    if ($details['html_text'] === "") {

        if ($type !== 'PRD') return false; // Or handle the case where the template is not found differently



        // Handle the case where the template is not found in the database

        // Optionally, log or trigger some default behavior

        return false;

    }



    tep_replace_template($details, $merge_details); // replace template content with merge details

    tep_strip_html($details); // strip tags;



    // send mails to given addresses

    for ($icnt = 0; $icnt < sizeof($send_details); $icnt++) {

        $details['to_name'] = $send_details[$icnt]['to_name'];

        $details['to_email'] = $send_details[$icnt]['to_email'];

        $details['from_name'] = $send_details[$icnt]['from_name'];

        $details['from_email'] = $send_details[$icnt]['from_email'];

        tep_send_email($details, true, $filename); // send email

    }

}



	

	// format date according to date setting in configuration

  function format_date($raw_date,$simple=false){

    if ( ($raw_date == '0000-00-00 00:00:00') || ($raw_date == '0000-00-00') || ($raw_date == '') || ($raw_date <= 0)) return false;

	$format=EVENTS_DATE_FORMAT;

	if ($simple) $format=strtolower($format);

    $year = substr($raw_date, 0, 4);

    $month = (int)substr($raw_date, 5, 2);

    $day = (int)substr($raw_date, 8, 2);

    $hour = (int)substr($raw_date, 11, 2);

    $minute = (int)substr($raw_date, 14, 2);

    $second = (int)substr($raw_date, 17, 2);

	if (@date('Y', mktime($hour, $minute, $second, $month, $day, $year)) == $year) {

      return date($format, mktime($hour, $minute, $second, $month, $day, $year));

    } else {

      return preg_replace('/2037$/', $year, date($format, mktime($hour, $minute, $second, $month, $day, 2037)));

    }



  }

	

	// Replace the template content

	function tep_replace_template(&$details,&$replace_array){

		reset($replace_array);

		foreach($replace_array as $key => $value) {

			$details['html_text']=preg_replace("#%%" . $key . "%%#i",preg_escape_back($value). "",$details['html_text']);

		}

		$details['html_text']=preg_replace("/%%current_url%%/i",HTTP_SERVER . DIR_WS_CATALOG . DIR_WS_TEMPLATES . TEMPLATE_NAME . "/",$details['html_text']);

		//or add order number to subject line

		//$details['subject']=preg_replace("#%%" . $key . "%%#i",$value . "",$details['subject']);

	}

	

	function preg_escape_back($string) {

    // Replace $ with \$ 

    $string = preg_replace('#(\\$|\\\\)#', '\\\\$1', $string);

    return $string;

		} 

	// common function to send email

	// edited to handle pdf attachment

	function tep_send_email(&$details,$default=false,$filename){

		$result=false;

		if ($default==false && (int)EMAIL_ACTIVATE!=1) return $result;

		 $message = new email(array('X-Mailer: osConcert'));

			

		 if ($details['format'] != 'T') {

			 $message->add_html($details['html_text'], $details['text']);

		 } else {

			 $message->add_text($details['text']);

		 }

	     	  

		//new code pdf send

		//Feb 2020 add in smtp attachment

		if(DISPLAY_PDF_DELIVERED_ONLY=='true' && isset($filename) && $filename !==''){ 

			  $message->add_attachment($message->get_file($filename),'eticket.pdf','application/pdf');

			  //smtp

			  $message->add_smtp_attachment($filename,'eticket.pdf');

			  }

		  

		 $message->build_message();

		  if(strpos($details['to_email'],',')!==false) {

                 $to_email=array();

                 $to_email=explode(",",$details['to_email']);

                 $details['to_email']=$to_email;

             }

			 

		

		 $result=$message->send($details['to_name'], $details['to_email'], $details['from_name'], $details['from_email'], $details['subject']);

		 unset($message);

		 return $result;

	}

	// function to fetch template content from database table

	function tep_get_template(array &$details){

		$details['html_text']='';

		$details['text']='';

		$add_option="";

		$mail_data_query=tep_db_query("SELECT * from " . $details["table"] . " where message_type='" . tep_db_input($details["type"]) . "'" . $add_option);

		if (tep_db_num_rows($mail_data_query)>0){

			$mail_data_result=tep_db_fetch_array($mail_data_query);

			//get message content

			$details['format']=$mail_data_result['message_format'];

			$details['html_text']=$mail_data_result['message_text'];

			$details['subject']=$mail_data_result['message_subject'];

			//or to add the order number to the subject line

			//if($mail_data_result['message_type'] == 'PRD'){

//                $details['subject']=$mail_data_result['message_subject'].' - Order Number: %%Order_Number%%';

//            }

//            else{

//                $details['subject']=$mail_data_result['message_subject'];

//            }

		}

	}

	// strip the html tags

	function tep_strip_html(&$details){

		$details['text']=strip_tags($details['html_text'],'<br>');

		$details['text']=str_replace(array('<br />','<br>','<BR>','<BR />','<br/>','<BR/>'),chr(13). chr(10),$details['text']);

	}



	// get rounded amount according to rounding factor in configuration

 function tep_get_rounded_amount($amount){

 	$result=0;

	$round_digit=0;

	$decimal=0;

 	$round_type=EVENTS_ORDER_AMOUNT_ROUND;

	$decimal=floor((($amount-floor($amount))*100)+0.5);

	switch($round_type){

		case "0.01":

			$result=$amount;

			break;

		case "0.05":

			$round_digit=$decimal%10;

			if ($round_digit>7) {

				$decimal=(floor($decimal/10)+1)*10;

			} else if ($round_digit>2) {

				$decimal=(floor($decimal/10)*10)+5;

			} else {

				$decimal=floor($decimal/10)*10;			

			}

			$result=floor($amount)+$decimal/100;

			break;

		case "0.1":

			$decimal=(floor($decimal/10))*10;

			$result=floor($amount)+$decimal/100;

			break;

		case "0":

		default:

			if ($decimal>=50)

				$result=floor($amount)+1;

			else

				$result=floor($amount);

	}

	return $result;

 }

 

 // format may be in format dd mm yyyy in any combinations but length must be equal to this

 function tep_check_date_raw($date,$format,$sep_char="/-/"){

 	$date_details=array('y'=>0,'m'=>0,'d'=>0);

	$split_arr=preg_split($sep_char,$date);

	$split_format=preg_split($sep_char,$format);

	if (!(sizeof($split_arr)==sizeof($split_format) && sizeof($split_arr)>0 && sizeof($split_arr)<=3)) return false;

	

	for ($icnt=0;$icnt<sizeof($split_arr);$icnt++){

		if (isset($date_details[strtolower($split_format[$icnt])]))

			$date_details[strtolower($split_format[$icnt])]=(int)$split_arr[$icnt];

	}

	return checkdate($date_details['m'],$date_details['d'],$date_details['y']);

 }

 

 function tep_convert_date_raw($date,$format=EVENTS_DATE_FORMAT,$sep_char="/-/"){

 	$date_details=array('y'=>0,'m'=>0,'d'=>0);

	$split_arr=preg_split($sep_char,$date);

	$split_format=preg_split($sep_char,$format);

	if (!(sizeof($split_arr)==sizeof($split_format) && sizeof($split_arr)>0 && sizeof($split_arr)<=3)) return "";

	

	for ($icnt=0;$icnt<sizeof($split_arr);$icnt++){

		if (isset($date_details[strtolower($split_format[$icnt])]))

			$date_details[strtolower($split_format[$icnt])]=(int)$split_arr[$icnt];

	}

	return $date_details['y'] . '-' . $date_details['m'] . '-' . $date_details['d'];

 }

  

 

 // get the current server date considering date offset settings

 function getServerDate($date=true){

	//$offset=(float)EVENTS_SERVER_DATE_OFFSET;

	$offset = 0;

	if($offset>0){

		if(strpos($offset,'.')>0){

			$cur_offset_time = mktime(date('H')+abs($offset),date('i')+30,date('s'),date('m'),date('d'),date('y'));

			if($date)

				return date('Y-m-d',$cur_offset_time);

			else

				return $cur_offset_time;

		}else{

			$cur_offset_time = mktime(date('H')+abs($offset),date('i'),date('s'),date('m'),date('d'),date('y'));

			if($date)

				return date('Y-m-d',$cur_offset_time);

			else

				return $cur_offset_time;

		}

	}else{

		if(strpos($offset,'.')>0){

			$cur_offset_time = mktime(date('H')-abs($offset)+1,date('i')-30,date('s'),date('m'),date('d'),date('y'));

			if($date)

				return date('Y-m-d',$cur_offset_time);

			else

				return $cur_offset_time;

		}else{

			$cur_offset_time = mktime(date('H')-abs($offset),date('i'),date('s'),date('m'),date('d'),date('y'));

			if($date)

				return date('Y-m-d',$cur_offset_time);

			else

				return $cur_offset_time;

		}

	}

 }

 

   ////rmh referral

// Returns an array with sources

// TABLES: sources

  function tep_get_sources($sources_id = '') {

    $sources_array = array();

    if (tep_not_null($sources_id)) {

        $sources = tep_db_query("select sources_name from " . TABLE_SOURCES . " where sources_id = '" . (int)$sources_id . "'");

        $sources_values = tep_db_fetch_array($sources);

        $sources_array = array('sources_name' => $sources_values['sources_name']);

    } else {

      $sources = tep_db_query("select sources_id, sources_name from " . TABLE_SOURCES . " order by sources_name");

      while ($sources_values = tep_db_fetch_array($sources)) {

        $sources_array[] = array('sources_id' => $sources_values['sources_id'],

                                   'sources_name' => $sources_values['sources_name']);

      }

    }

    return $sources_array;

  }

////rmh referral

// Creates a pull-down list of countries

  function tep_get_source_list($name, $selected = '', $parameters = '') {

    $sources_array = array(array('id' => '', 'text' => PULL_DOWN_DEFAULT));

    $sources = tep_get_sources();

    for ($i=0, $n=sizeof($sources); $i<$n; $i++) {

      	$sources_array[] = array('id' => $sources[$i]['sources_id'], 'text' => $sources[$i]['sources_name']);

    }

	if (DISPLAY_REFERRAL_OTHER=='true'){

		$sources_array[] = array('id' => '9999', 'text' => TEXT_REFERRAL_OTHER);

	}

    return tep_draw_pull_down_menu($name, $sources_array, $selected, $parameters);

  }

  

  function tep_get_customer_options($type) {

    $options_array = array();

	$options_array[] = array('id' => '', 'text' => PULL_DOWN_DEFAULT);

    $options = tep_db_query("select options_id, options_name from " . TABLE_CUSTOMER_OPTIONS . " where options_type='". tep_db_input($type)  ."' order by options_name");

      while ($options_values = tep_db_fetch_array($options)) {

        $options_array[] = array('id' => $options_values['options_id'],

                                   'text' => $options_values['options_name']);

      }

    return $options_array;

  }

  function tep_draw_hidden($name, $value = '', $parameters = '') {

    $field = '<input type="hidden" name="' . tep_output_string($name) . '"';

      $field .= ' value="' . tep_output_string($value) . '"';

    if (tep_not_null($parameters)) $field .= ' ' . $parameters;

    $field .= '>';

    return $field;

  }

  function tep_get_all_input_params($exclude_array = '') {

    global $FGET,$FSESSION;

    if (!is_array($exclude_array)) $exclude_array = array();

    $get_url = '';

    if (is_array($FGET) && (sizeof($FGET) > 0)) {

		

      reset($FGET);

		foreach($FGET as $key => $value) 

		{		

        if ( (strlen($value) > 0) && ($key != $FSESSION->name) && (!in_array($key, $exclude_array)) && ($key != 'x') && ($key != 'y') ) {

          $get_url .= $key . '=' . urlencode(stripslashes($value)) . '&';

        }

      }

    }

    return $get_url;

  }

  

function tep_payment_installed($module){

		$modules=MODULE_PAYMENT_INSTALLED;

		if ($modules!=""){

			$modules_array=preg_split("/;/",$modules);

			if (array_search($module,$modules_array)===false){

				return false;

			}

			else {

				return true;

			}

		}

	}

	

	function tep_product_small_image($image,$title,$parameters=""){

		

		if($image==''){

		$image='pixel_trans.gif';	

		}

		

		if (file_exists(DIR_FS_CATALOG . DIR_WS_IMAGES . "small/" . $image)){

			//return tep_image(DIR_WS_IMAGES . "small/" . $image,$title,'','',$parameters);

			//give it width

			return tep_image(DIR_WS_IMAGES . "small/" . $image,$title,SMALL_IMAGE_WIDTH,'',$parameters);

		}

		else if (file_exists(DIR_FS_CATALOG . DIR_WS_IMAGES . "big/" . $image)){

			return tep_image(DIR_WS_IMAGES . "big/" . $image,$title,SMALL_IMAGE_WIDTH,'',$parameters);

		}

		else {

			return tep_image(DIR_WS_IMAGES . $image,$title,SMALL_IMAGE_WIDTH,'',$parameters);

		}

	}

	

	

	

	function tep_product_email_image($image,$title,$parameters=""){

		

		if($image==''){

		$image='pixel_trans.gif';	

		}

		

		//file_put_contents('mail.txt',DIR_FS_CATALOG . DIR_WS_IMAGES . "small/" . $image);

		if (file_exists(DIR_FS_CATALOG . DIR_WS_IMAGES . "small/" . $image)){

			return '<img src="' . HTTP_SERVER . DIR_WS_HTTP_CATALOG . DIR_WS_IMAGES . "small/" . $image . '" width=50 height=50 ' . $parameters . '>';

		}

		else if (file_exists(DIR_FS_CATALOG . DIR_WS_IMAGES . "big/" . $image)){

			return '<img src="' . HTTP_SERVER . DIR_WS_HTTP_CATALOG . DIR_WS_IMAGES . "big/" . $image . '" width=50 height=50 ' . $parameters . '>';

		}

		else if(file_exists(HTTP_SERVER . DIR_WS_HTTP_CATALOG . DIR_WS_IMAGES . $image))  {

			return '<img src="' . HTTP_SERVER . DIR_WS_HTTP_CATALOG . DIR_WS_IMAGES . $image . '" width=50 height=50 ' . $parameters . '>';

		}

		else {

			return tep_image(DIR_WS_IMAGES . 'pixel_trans.gif','', '50','50');

		}

	}

	//using for featured

	function tep_product_custom_image($image,$title,$parameters="",$width='',$height='',$calculate=true){

		$filename=$image;

		if (file_exists(DIR_FS_CATALOG . DIR_WS_IMAGES . "big/" . $image)){

			$filename="big/" . $image;

		} else if (file_exists(DIR_FS_CATALOG . DIR_WS_IMAGES . "big/" . $image)){

			$filename="big/" . $image;

		}

		if ($calculate){

		  if ($image_size = @getimagesize(DIR_FS_CATALOG . DIR_WS_IMAGES . $filename)) 

		  {

			  if ($image_size[0]>$width){

				  $ratio = $width / $image_size[0];

				  $height = intval($image_size[1] * $ratio);

			  } elseif ($image_size[1]>$height) {

				  $ratio = $height / $image_size[1];

				  $width = intval($image_size[0] * $ratio);

			  } else {

				  $width = $image_size[0];

				  $height = $image_size[1];

			  }

		  }

		}

		return '<img class="img-fluid" src="' . HTTP_SERVER . DIR_WS_HTTP_CATALOG . DIR_WS_IMAGES . $filename . '"  ' . $parameters . '>';

	} 









	

	function tep_get_unit_name(){

		if (!defined("SHOP_WEIGHT_UNIT")) return TEXT_KG;

		if (!defined("TEXT_" . SHOP_WEIGHT_UNIT)) return TEXT_KG;

		return constant("TEXT_" . SHOP_WEIGHT_UNIT);

	}

	function tep_get_display_weight($weight){

		return $weight . '&nbsp;' . tep_get_unit_name();

	}		

	  function tep_get_customer_survey_options($type,$other_option=false) {

    $options_array = array();

	$options_array[] = array('id' => '', 'text' => PULL_DOWN_DEFAULT);

    $options = tep_db_query("select options_id, options_name from " . TABLE_CUSTOMER_OPTIONS . " where options_type='". tep_db_input($type)  ."' order by options_name");

      while ($options_values = tep_db_fetch_array($options)) {

        $options_array[] = array('id' => $options_values['options_id'],

                                   'text' => $options_values['options_name']);

      }

	 if($other_option) 	$options_array[] = array('id'=>'9999', 'text' => TEXT_REFERRAL_OTHER); 

    return $options_array;

  }

  $nwords = array(    "Zero", "One", "Two", "Three", "Four", "Five", "Six", "Seven",

                     "Eight", "Nine", "Ten", "Eleven", "Twelve", "Thirteen",

                     "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eighteen",

                     "Nineteen", "Twenty", 30 => "Thirty", 40 => "Forty",

                     50 => "Fifty", 60 => "Sixty", 70 => "Seventy", 80 => "Eighty",

                     90 => "Ninety" );



	function tep_check_module_status(&$module,$payment_zone,$except_zone_list,$except_country_list){

		global $order, $FSESSION;

	  //Box Office - look for the session

	  if ($_SESSION['BoxOffice']== 999){

	  $the_zone_id = 999;}else{

      //$the_zone_id = (int)$order->billing['country']['id'];}

      $the_zone_id = isset($order->billing['country']['id']) ? $order->billing['country']['id'] : 0;

      }

	  //Box Office end

	  if ( ($module->enabled == true) && (($except_zone_list!='' && substr($except_zone_list,0,1)!='M') || ($except_country_list!='' && substr($except_country_list,0,1)!='M'))) {

	  	$add_option='';

		if ($except_zone_list!='' && substr($except_zone_list,0,1)!='M') $add_option.=" geo_zone_id in(" . tep_db_input($except_zone_list) . ") ";

		if ($except_country_list!='' && substr($except_country_list,0,1)!='M') {

			if ($add_option!='') $add_option.=' or ';

			$add_option.=" zone_country_id in(" . tep_db_input($except_country_list) . ") ";

		}

		if ($add_option!=""){

			$check_query=tep_db_query("SELECT zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where  (" . $add_option. ") and zone_country_id='" . $the_zone_id . "' and (zone_id is null or zone_id = '0' or zone_id ='" . (int)$order->billing['zone_id'] ."');");

			if (tep_db_num_rows($check_query)>0){

				$module->enabled=false;

			}

		}

	  }

      if ( ($module->enabled == true) && ($payment_zone!='') ) {

        $check_flag = false;

		$check_sql = "select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id in(" . tep_db_input($payment_zone) . ") and zone_country_id = '" . $the_zone_id . "' order by zone_id";

        $check_query = tep_db_query($check_sql);

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

          $module->enabled = false;

        }

      }

	}

	

	function tep_check_shipping_module_status(&$module,$payment_zone,$except_zone_list,$except_country_list){

		global $order;

	  if ( ($module->enabled == true) && (($except_zone_list!='' && substr($except_zone_list,0,1)!='M') || ($except_country_list!='' && substr($except_country_list,0,1)!='M'))) {

	  	$add_option='';

		if ($except_zone_list!='' && substr($except_zone_list,0,1)!='M') $add_option.=" geo_zone_id in(" . tep_db_input($except_zone_list) . ") ";

		if ($except_country_list!='' && substr($except_country_list,0,1)!='M') {

			if ($add_option!='') $add_option.=' or ';

			$add_option.=" zone_country_id in(" . tep_db_input($except_country_list) . ") ";

		}

		$check_query=tep_db_query("SELECT zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where  (" . $add_option. ") and zone_country_id='" . (int)$order->delivery['country']['id'] . "' and (zone_id is null or zone_id = '0' or zone_id ='" . (int)$order->delivery['zone_id'] ."');");

		if (tep_db_num_rows($check_query)>0){

			$module->enabled=false;

		}

	  }

      if ( ($module->enabled == true) && ($payment_zone!='') ) {

        $check_flag = false;

		$securepay_zone=trim($payment_zone)!=""?$payment_zone:'0';

        $check_query = tep_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id in(" . tep_db_input($payment_zone) . ") and zone_country_id = '" . (int)$order->delivery['country']['id'] . "' order by zone_id");

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

          $module->enabled = false;

        }

      }

	}

	

	function replace_content(&$replaced_content,&$template_details){

		$ttotal_cols=1;

		foreach($template_details as $key => $value) {

			switch(substr($key,0,strpos($key,'_'))){

				case "VALUE":

					$replaced_content=str_replace("{{" . $key . "}}",$value,$replaced_content);

					break;

				case "SECTION":

					if ($value!=1){

						$replaced_content=preg_replace("/\{\{" . $key ."_START\}\}((.|\n)*)\{\{" . $key . "_END\}\}/","",$replaced_content);

					} else {

						$replaced_content=str_replace(array("{{" .$key . "_START}}","{{" .$key . "_END}}"),"",$replaced_content);

					}

					break;

				case "REPEAT":

					$start_pos=strpos($replaced_content,"{{" . $key . "_START}}")+strlen("{{" . $key ."_START}}");

					$end_pos=strpos($replaced_content,"{{" . $key . "_END}}");

					$repeat_content=substr($replaced_content,$start_pos,$end_pos-$start_pos);

					$merged_total_content='<table width="100%">' . "\n";

					$col=0;

					$row=0;

					for ($icnt=0,$n=count($value);$icnt<$n;$icnt++){

						$merged_content='';

						if ($col==0 || $ttotal_cols==1) $merged_content.='<tr>';

						$merged_content.='<td valign="top"' . ($row==0?$tcols_width:''). '>' . $repeat_content;

						reset($value[$icnt]);

						foreach ( array_keys($value[$icnt]) as $itemkey )

						{

							$merged_content=str_replace("{{" . $key . "_" . $itemkey . "}}",$value[$icnt][$itemkey],$merged_content);

						}

						$col++;

						if ($col==$ttotal_cols) {

							$col=0;

							$row++;

						}

						$merged_total_content.="\n" . $merged_content;

					}

					$merged_total_content.="</table>";

					$replaced_content=substr($replaced_content,0,$start_pos) . $merged_total_content . substr($replaced_content,$end_pos);

					$replaced_content=str_replace(array("{{" .$key . "_START}}","{{" .$key . "_END}}"),"",$replaced_content);

					break;

			}

		}

	}

	

		function tep_check_password_strength($pwd)

		{

            $tot_average        = 0.0; 

            $pwdav_len            = 0.0;                 

            $pwdav_caps        = 0.0;                 

            $pwdav_nums        = 0.0;                                         

            $pwdav_small        = 0.0; 

            $pwdav_puncts        = 0.0;                 

            $total_char_used = 0; 

            if (strlen($pwd)>0) 

            { 

                $p_limit = 5; 

                $pwd_len = strlen($pwd); 

                $nums_cnt = 0; 

                for($i=0;$i<$pwd_len;$i++) 

                { 

                    if (is_numeric($pwd[$i])) 

                        $nums_cnt++; 

                } 

                if ($nums_cnt>0) 

                    $total_char_used += 10; 

					

                $small_cnt = 0; 

                for($i=0;$i<$pwd_len;$i++) 

                { 

                    if (ctype_lower($pwd[$i])) 

                    { 

                        $small_cnt++; 

                    } 

                } 

                if ($small_cnt>0) 

                { 

                    $total_char_used += 26; 

                } 

                $caps_cnt = 0; 

                for($i=0;$i<$pwd_len;$i++) 

                { 

                    if (ctype_upper($pwd[$i])) 

                    { 

                        $caps_cnt++; 

                    } 

                } 

                if ($caps_cnt>0) 

                { 

                    $total_char_used += 26; 

                } 

                $puncts_cnt = 0; 

                for($i=0;$i<$pwd_len;$i++) 

                { 

                    if (ctype_punct($pwd[$i])) 

                    { 

                        $puncts_cnt++; 

                    } 

                } 

                if ($puncts_cnt>0) 

                { 

                    $total_char_used += 31; 

                } 

                // calculation   

				$len_min=ENTRY_PASSWORD_MIN_LENGTH;

				$len_max=16;                                      

                if (($pwd_len>$len_min) and ($pwd_len<$len_max)) 

                { 

                    $pwdav_len += (100 / $p_limit); 

                }                                                         

                // caps 

                $tot_average += $pwdav_len; 

                if (20 <= (($caps_cnt * 100) / $pwd_len)) 

                { 

                    $pwdav_caps += (100 / $p_limit); 

                } 

                else 

                { 

                    $pwdav_caps += ($caps_cnt > 0) ? ((100 / $p_limit) - 10) :  0; 

                } 

                $tot_average += $pwdav_caps; 

                // numbers 

                if (10 <= (($nums_cnt * 100) / $pwd_len)) 

                { 

                    $pwdav_nums += (100 / $p_limit); 

                } 

                else 

                { 

                    $pwdav_nums += ($nums_cnt > 0) ? ((100 / $p_limit) - 10) :  0; 

                } 

                $tot_average += $pwdav_nums; 

                // small 

                if (30 <= (($small_cnt * 100) / $pwd_len)) 

                { 

                    $pwdav_small += (100 / $p_limit); 

                } 

                else 

                { 

                    $pwdav_small += ($small_cnt > 0) ? ((100 / $p_limit) - 10) :  0; 

                } 

                $tot_average += $pwdav_small; 

                // symbols 

                if (10 <= (($puncts_cnt * 100) / $pwd_len)) 

                { 

                    $pwdav_puncts += (100 / $p_limit); 

                } 

                else 

                { 

                    $pwdav_puncts += ($puncts_cnt > 0) ? ((100 / $p_limit) - 10) :  0; 

                } 

                 

                $tot_average += $pwdav_puncts;             

                $charSet = $total_char_used; 

            } 

            $tot_average=round($tot_average, 0); 

		//	echo $tot_average;

			if($tot_average<=40)

				return false;

			else

				return true;

				

		}

	

	

	// products tree structure

	

function tep_get_products_output_tree($parent_id = '0',&$content,$default,$product_category_id=0) {

    global $FSESSION,$selected_name,$login_groups_type;

    if (!is_array($products_category_tree_array)) $products_category_tree_array = array();

	// filte to particular instructor

	$product_where = '';

	if($product_category_id>0)$product_where = " and pc.categories_id='" . tep_db_input($product_category_id) . "'";

	// iterate with nested category

	$products_categories_sql = "select pc.categories_id, pcd.categories_name, pc.parent_id from " . TABLE_CATEGORIES . " pc, " . TABLE_CATEGORIES_DESCRIPTION . " pcd where pc.categories_id = pcd.categories_id and pcd.language_id = '" . (int)$FSESSION->languages_id . "' and pc.parent_id = '" . (int)$parent_id . "'" . $product_where . " order by pc.sort_order, pcd.categories_name";

	//echo $parent_id . ":" . $events_categories_sql;exit;

	$products_categories_query = tep_db_query($products_categories_sql);

	while ($products_categories = tep_db_fetch_array($products_categories_query)) {

		$content.= '<optgroup style="color:#0000FF" label="' . write_products_category_space($products_categories['categories_id']) . tep_output_string($products_categories['categories_name'], array('"' => '&quot;', '\'' => '&#039;', '<' => '&lt;', '>' => '&gt;')) . '">';

		// select the list of products of particular category

		$products_query=tep_db_query("select pd.products_name,pd.products_id from " . TABLE_PRODUCTS . ' p, ' . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS_TO_CATEGORIES . " pcd where pcd.categories_id='" . (int)$products_categories['categories_id'] . "' " .  " and pd.language_id = " . (int)$FSESSION->languages_id . PRODUCTS_CONDITION_STATUS. " and p.products_id=pcd.products_id and p.products_id=pd.products_id order by pd.products_name");

		while ($products=tep_db_fetch_array($products_query))

		{

			$content .= '<option style="color:#000000" value="' . tep_output_string($products['products_id']) . '"';

			if ($default == $products['products_id']){

				$selected_name=$events['products_name'];

				$content .= ' SELECTED';

			}

			$content .= '>' . tep_output_string($products['products_name'], array('"' => '&quot;', '\'' => '&#039;', '<' => '&lt;', '>' => '&gt;')) . '</option>';

		}    

		tep_get_products_output_tree($products_categories['categories_id'], $content,$default);

		$content.='</optgroup>';

	}

}

	function write_products_category_space($category_id){

	    global $FSESSION,$selected_name,$login_groups_type;

		$sql = "select parent_id,pc.categories_id,categories_name from " . TABLE_CATEGORIES . " pc," . TABLE_CATEGORIES_DESCRIPTION . " pcd where pc.categories_id=pcd.categories_id and pcd.language_id = '" . (int)$FSESSION->languages_id . "' and pc.categories_id=" . (int)$category_id;

		$query = tep_db_query($sql);

		$result = tep_db_fetch_array($query);

		if($result['parent_id']>0){

			return "&nbsp;&nbsp;" . write_products_category_space($result['parent_id']);

		}else{

			return "";

		}

	}





  function tep_info_image($image, $alt, $width = '', $height = '') {

    if (tep_not_null($image) && (file_exists(DIR_WS_IMAGES . $image)) ) {

      $image = tep_image(DIR_WS_IMAGES . $image, $alt, $width, $height);

    } else {

      $image = $alt;

    }

    return $image;

  }

 function check_restrick(){

   global $FSESSION,$FREQUEST,$customer_group_id;

    if(!$customer_group_id && $FSESSION->customer_id>0) $customer_group_id=get_customer_group_id();

    $check_restrict_customer="and ((restrict_to_customers='' OR ISNULL(restrict_to_customers)) AND (restrict_to_groups='' OR ISNULL(restrict_to_groups)))";     

    if($FSESSION->customer_id>0) $check_restrict_customer=substr($check_restrict_customer,0,-1)." OR (restrict_to_customers like '%". tep_db_input($FSESSION->customer_id) ."%' OR restrict_to_groups like '%". tep_db_input($customer_group_id) ."%'))";    

    $return=false;

    if($FREQUEST->getvalue('products_id')!='' && $FREQUEST->getvalue('products_id','int')>0){

		$check_product_query=tep_db_query("select products_id from ".TABLE_PRODUCTS." where products_id='".$FREQUEST->getvalue('products_id','int')."' ".$check_restrict_customer);		

		

		if(tep_db_num_rows($check_product_query)>0) $return=true;

    }

    return $return;

 }   

 function get_customer_group_id(){

 	global $FSESSION;

 	$customer_group_id="";

 	if($FSESSION->customer_id>0){

 		$customer_group_query=tep_db_query("select customers_groups_id from ".TABLE_CUSTOMERS." where customers_id='".(int)$FSESSION->customer_id."'"); 		

 		if(tep_db_num_rows($customer_group_query)>0) {

 			$customer_group=tep_db_fetch_array($customer_group_query);

 			$customer_group_id=$customer_group['customers_groups_id'];			

 		} 		

 	}

 	return $customer_group_id;

 }

 

 function tep_show_products_price($inital_price,$product_info,$select_quantity=true,$quantity_combo=false){

 	global $currencies,$products_price;

 	$output="<b>" . TEXT_PRICE . ": " .$products_price . "</b>";

 	if ($product_info["products_price_break"]!='Y') return $output;

	$breaks_query=tep_db_query("SELECT quantity,discount_per_item from " . TABLE_PRODUCTS_PRICE_BREAK . " where products_id=" .(int)$product_info["products_id"] . " order by quantity");

	

	if(tep_db_num_rows($breaks_query)<=0) return $output;

	$output="";

	$tax=tep_get_tax_rate($product_info['products_tax_class_id']);

	$select_script="";

	if ($select_quantity){

		$select_script="onMouseOver='javascript:rowOverEffect2(this);' onMouseOut='javascript:rowOutEffect2(this);' onClick='javascript:setQuantity(this,%s)'";

		$output.='<script>var prev;function setQuantity(object,quan){object.className="moduleRowSelected";document.getElementById("qty").value=quan;if(prev && prev!=object) prev.className="moduleRow";prev=object;load_stock_first('. $product_info["products_id"] .');}function rowOverEffect2(object) { if (object.className == "moduleRow") object.className = "moduleRowOver";}function rowOutEffect2(object) {if (object.className == "moduleRowOver") object.className = "moduleRow";}</script><input type="hidden" name="qty" value=1 id="qty">';

	}

	$output.='<table cellpadding="2" width="100%" id="tablePriceBreaks">

				<tr>

					<td class="smallText" align="right"><b>' . TEXT_QUANTITY  . '</b></td>

					<td class="smallText" align="right"><b>' . TEXT_PRICE . '</b></td>

				</tr>

				<tr class="moduleRow" ' . sprintf($select_script,1) . '>

					<td class="smallText" align="right"><b>1</b></td>

					<td class="smallText" align="right"><b>' .$currencies->format(tep_add_tax($product_info["products_price"], $tax)) . '</b></td>

				</tr>';

				

				

	while ($break_result = tep_db_fetch_array($breaks_query)) {

	    $quantity=$break_result['quantity'];

		$price=$currencies->format(tep_add_tax($product_info["products_price"]-$break_result['discount_per_item'], $tax) * $quantity);

		$output.='<tr class="moduleRow" ' . sprintf($select_script,$quantity) . '>

					<td class="smallText" align="right"><b>' . $quantity  . '</b></td>

					<td class="smallText" align="right"><b>' . $price . '</b></td>

				</tr>';

	}

	if ($quantity_combo){

		$price_breaks=tep_get_products_price_breaks($product_info["products_id"]);

		$output.='<tr height="10"><td></td></tr><tr><td class="main" colspan="2" align="right">' . TEXT_QUANTITY . ': ' . tep_draw_pull_down_menu('qty',$price_breaks,1) .'</td></tr>';

	}

	$output.='</table><script>setQuantity(document.getElementById("tablePriceBreaks").rows[1],1)</script>';

	

	return $output;

 }

 

 function tep_get_products_breaks($product_info,$tax_rate){

 	global $currencies;

	

	$breaks_query=tep_db_query("SELECT quantity,discount_per_item from " . TABLE_PRODUCTS_PRICE_BREAK . " where products_id=" .(int)$product_info["products_id"] . " order by quantity");

	if(tep_db_num_rows($breaks_query)<=0) return false;

	$GLOBALS["JS_DETAILS"]["PRODUCT_PRICE_BREAKS"]="{enabled:true,prices:{";

	$result=array();

	$icnt=0;

	

	//$result[]=array('QUANTITY'=>1,"PRICE"=>$currencies->format(tep_add_tax($product_info["products_price"],$tax_rate)));

	

	$GLOBALS["JS_START"].="setQuantity('','1');\n";

	//edited 2016 qpb

	//the 

	while ($break_result = tep_db_fetch_array($breaks_query)) {

		//$price=tep_add_tax($product_info["products_price"]-$break_result['discount_per_item'], $tax_rate) * $break_result['quantity'];

		$price=tep_add_tax($break_result['discount_per_item'], $tax_rate);

	    $result[]=array('QUANTITY'=>$break_result['quantity'],"PRICE"=>$currencies->format($price));

		$GLOBALS["JS_DETAILS"]["PRODUCT_PRICE_BREAKS"].=$break_result['quantity'] .":" . $price . ",";

				

		$icnt++;

	}

	$GLOBALS["JS_DETAILS"]["PRODUCT_PRICE_BREAKS"]=substr($GLOBALS["JS_DETAILS"]["PRODUCT_PRICE_BREAKS"],0,-1) . "}}";

	return $result;

 }

 function tep_get_products_price_breaks($products_id){

 	$price_breaks=false;

	$breaks_query=tep_db_query("SELECT quantity from " . TABLE_PRODUCTS_PRICE_BREAK . " where products_id=" . (int)$products_id . " order by quantity");

	if (tep_db_num_rows($breaks_query)>0){

		$price_breaks=array(array('id'=>1,'text'=>1));

		while($breaks_result=tep_db_fetch_array($breaks_query)){

			$price_breaks[]=array('id'=>$breaks_result["quantity"],'text'=>$breaks_result["quantity"]);

		}

	}

	return $price_breaks;

 }

 function tep_get_products_price_breaks_discount($products_id, $quantity_in_cart){

 	$discount=0;

	$breaks_query=tep_db_query("SELECT quantity, discount_per_item from " . TABLE_PRODUCTS_PRICE_BREAK . " where products_id=" . (int)$products_id . " order by quantity");

	if (tep_db_num_rows($breaks_query)>0){

	while($breaks_result=tep_db_fetch_array($breaks_query)){

        if ($quantity_in_cart >= $breaks_result['quantity']) {

          $discount = $breaks_result['discount_per_item'] * $quantity_in_cart;

        }

		}

	}

   return $discount;

 }

 

function tep_write_payment_response($order_id,$payment_response){

	tep_db_query("insert into ".TABLE_PAYMENT_RESPONSE." (order_id,payment_response) values('" . tep_db_input($order_id) . "','" . tep_db_input($payment_response) . "')");

}

function tep_update_payment_response_code($old_id,$order_id){

	tep_db_query("UPDATE ".TABLE_PAYMENT_RESPONSE." set order_id='" . tep_db_input($order_id) . "' where order_id='" . tep_db_input($old_id) . "'");

}



function tep_get_plain_products_price($products_price){

     global $FSESSION;

	  if (!$FSESSION->is_registered('customer_id')) {

		 $customer_discount = 0;

		 if (defined("GUEST_DISCOUNT")) $customer_discount=(int)GUEST_DISCOUNT;

		 if ($customer_discount >= 0) {

			$products_price = $products_price + $products_price * abs($customer_discount) / 100;

		 } else {

			$products_price = $products_price - $products_price * abs($customer_discount) / 100;

		 }

		 return $products_price;

		 

	  } elseif ($FSESSION->is_registered('customer_id')) {

		 $query = tep_db_query("select g.customers_groups_discount from " . TABLE_CUSTOMERS_GROUPS . " g inner join  " . TABLE_CUSTOMERS  . " c on g.customers_groups_id = c.customers_groups_id and c.customers_id = '" . (int)$FSESSION->customer_id . "'");

		 $query_result = tep_db_fetch_array($query);

		 $customers_groups_discount = $query_result['customers_groups_discount'];

		 $query = tep_db_query("select customers_discount from " . TABLE_CUSTOMERS . " where customers_id =  '" . (int)$FSESSION->customer_id . "'");

		 $query_result = tep_db_fetch_array($query);

		 $customer_discount = $query_result['customers_discount'];

		 $customer_discount = $customer_discount + $customers_groups_discount;

		 if ($customer_discount >= 0) {

			$products_price = $products_price + $products_price * abs($customer_discount) / 100;

		 } else {

			$products_price = $products_price - $products_price * abs($customer_discount) / 100;

		 }

		return $products_price;

	  }

}

// 

 function get_order_countryname($country_id) 

 {



    $country_name_query = tep_db_query("select * from " . TABLE_COUNTRIES . " where countries_id = '" . tep_db_input($country_id) . "'");

    if (!tep_db_num_rows($country_name_query)) 

	{

      return 0;

    }

    else {

      $country_name_row = tep_db_fetch_array($country_name_query);

      return $country_name_row['countries_name'];

    }

  }	

//osconcert fix php4 error

if (!function_exists('file_put_contents')) {

    function file_put_contents($filename, $data) {

        $f = @fopen($filename, 'w');

        if (!$f) {

            return false;

        } else {

            $bytes = fwrite($f, $data);

            fclose($f);

            return $bytes;

        }

    }

}

function tep_check_country_differ($payment=false){

	global $order,$IP_COUNTRY,$USER_BARRED;

	

	$USER_BARRED=false;

	if ((BLOCK_DIFFER_COUNTRY_IP!='Yes' && BLOCK_EXCLUDED_COUNTRIES!='Yes') || (BLOCK_DIFFER_COUNTRY_IP!='Yes' && !$payment)) return;

	$IP_COUNTRY=tep_get_ip_country();

	if (BLOCK_DIFFER_COUNTRY_IP!='Yes' || $IP_COUNTRY=="") return;

	if ($IP_COUNTRY!=$order->customer["country"]["iso_code_2"] && $IP_COUNTRY!=$order->customer["country"]["iso_code_3"] && strtolower($IP_COUNTRY)!=strtolower($order->customer["country"]["title"])){

		$USER_BARRED=true;

	}

}

function tep_get_ip_country(){

	if (isset($_SESSION["ip_country"])){

		return $_SESSION["ip_country"];

	}

	$ip=$_SERVER["REMOTE_ADDR"];

	$country="";

	if (defined("GEOIP_LICENSE_KEY") && GEOIP_LICENSE_KEY!=''){

		$query = "http://maxmind.com:8010/a?l=" . GEOIP_LICENSE_KEY . "&i=" . $ip;

		$url = parse_url($query);

		$host = $url["host"];

		$path = $url["path"] . "?" . $url["query"];

		$timeout = 1;

		$fp = @fsockopen ($host, 8010, $errno, $errstr, $timeout);

		if ($fp) {

		  @fputs ($fp, "GET $path HTTP/1.0\nHost: " . $host . "\n\n");

		  while (!feof($fp)) {

			$buf .= fgets($fp, 128);

		  }

		  $lines = split("\n", $buf);

		  $country = $lines[count($lines)-1];

		  fclose($fp);

		} 

		if (strlen($country)>3) $country="";

	} else {

		$result=@file_get_contents("http://www.ippages.com/simple/?ip=" . $ip . "&get=country");

		if (strpos($result,"-")!==false){

			$splt=preg_split("/-/",$result);

			$country=$splt[0];

		}

	}

	$_SESSION["ip_country"]=$country;

	return $country;

	

}

function tep_get_country_title($iso_code){

	$query=tep_db_query("SELECT countries_name from " . TABLE_COUNTRIES . " where countries_iso_code_2='" . tep_db_input( $iso_code). "' or countries_iso_code_3='"  . tep_db_input($iso_code) . "' or lower(countries_name)='" . tep_db_input((strtolower($iso_code))) . "'");

	$result=tep_db_fetch_array($query);

	return $result["countries_name"];

}

function tep_output_differ_check(){

	global $IP_COUNTRY,$USER_BARRED;

	if (!$USER_BARRED) return;

	echo '<tr>

			<td  class="messageStackWarning" style="padding:4px">

			<table cellpadding="2" width="100%">

				<tr>

					<td class="smallText"><img src="images/icons/error.gif" alt="Transaction barred"><b>' . TEXT_TRANSACTION_BARRED . '</b>

				</tr>

				<tr>

					<td class="smallText">' . sprintf(TEXT_TRANSACTION_BARRED_REASON,tep_get_country_title($IP_COUNTRY)) . '

					</td>

				</tr>

			</table>

			</td>

		</tr>

		 ';

}

function tep_check_payment_barred($exclude){

	global $IP_COUNTRY;

	if ($exclude=="" || substr($exclude,0,1)=='M') return false;

	if ($IP_COUNTRY=="") return false;

	if (BLOCK_EXCLUDED_COUNTRIES!='Yes') return false;

	

	$query=tep_db_query("SELECT countries_name from " . TABLE_COUNTRIES . " where countries_iso_code_2='$IP_COUNTRY' or countries_iso_code_3='$IP_COUNTRY' or lower(countries_name)='" . strtolower($IP_COUNTRY) . "' and countries_id in(" . $exclude . ")");

	if (tep_db_num_rows($query)>0) return true;

	return false;

}

 function get_discount_details($orders_id,$product_id){

 global $currencies;

 	$return='';

    $product_query = tep_db_query("select discount_type,discount_text,products_price,final_price,products_tax,products_quantity from " . TABLE_ORDERS_PRODUCTS . " where orders_id='" . (int)$orders_id . "' and products_id='" . (int)$product_id . "'" );

	

	$product_result=tep_db_fetch_array($product_query);

	if($product_result['discount_type']=='C' || $product_result['discount_type']=='S') {

		$product_to_categories_query = tep_db_query("select categories_id from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . (int)$product_id . "'");

		$product_to_categories = tep_db_fetch_array($product_to_categories_query);

		$category = $product_to_categories['categories_id'];

		$fp=$product_result["final_price"];

		$tax=$product_result["products_tax"];

		$qty=$product_result["products_quantity"];

		$price=$product_result['products_price'];

		$product_amount=tep_add_tax(($price * $product_result['products_quantity']),$tax);

		$final_amount=tep_add_tax(($fp*$product_result['products_quantity']),$tax);

		$total=($product_amount-$final_amount);

		

			$return .=TEXT_DISCOUNT_APPLIED . '&nbsp;&nbsp;'. $product_result['discount_text']  . '<br>';

			$return .=TEXT_ORIGINAL_AMOUNT . '&nbsp;&nbsp;'. $currencies->format($product_amount) . '<br>';

			$return .=TEXT_DISCOUNT . '&nbsp;&nbsp;'. $currencies->format($total);

				

	}

	return $return;

 	

 }

 

 function tep_get_post_vars($post_value,$name="",$filter_array=array()){

 	global $POST_HIDDEN;

 	if (count($post_value)<=0) return;

	reset($post_value);

	foreach($post_array as $name => $value)

	{

		if ($name=="" && in_array($key,$filter_array)) continue;

		if (is_array($post_value[$key])){

			tep_get_post_vars($post_value[$key],($name==''?$key:$name . "[" . $key . "]"));

		} else {

			$POST_HIDDEN.='<input type="hidden" name="' . ($name==''?$key:$name . "[" . $key . "]") . '" value="' . tep_output_string($post_value[$key]) . '">' . "\n";

		}

	}

 }

 

	

	 function kill_season_queue($order_id){

	  global $FSESSION;

	  tep_db_query("DELETE FROM `coupon_season_queue` where  `order_id` = '".$order_id."'");

		

		return false;

  }

	  

  function update_season_queue($order_id, $order_status)

  {

	  global $FSESSION;

	  //update the flag field to match the order status

	 // if(is_int($order_id) && is_int($order_status)){

		   tep_db_query("UPDATE `coupon_season_queue` set `flag` = '".$order_status."' where  `order_id` = '".$order_id."'");		  

	 // }

	  // now update  the entries where flag =3 	   

	   	if($order_status==3) {

			

		//how many season tickets to grant

		 $season_query = tep_db_query("select amount, customer_id from `coupon_season_queue`  where  `order_id` = '".$order_id."'");



		$season_count =tep_db_num_rows($season_query);

		if ($season_count > 0 ){

        $season_result = tep_db_fetch_array($season_query);

        $season_amount = $season_result['amount'];



		 

		 //is there any in the account already?



		

        $gv_query = tep_db_query("select amount from coupon_season_customer where customer_id = '" . $season_result['customer_id'] . "'");

		$gv_count =tep_db_num_rows($gv_query);

	if ($gv_count > 0 )

	{

        $gv_result = tep_db_fetch_array($gv_query);

        $gv_amount = $gv_result['amount'] + $season_amount;

        $gv_update = tep_db_query("update coupon_season_customer set amount = '" . $gv_amount . "' where customer_id = '" . $season_result['customer_id'] . "'");

	}

		 else

	{

		  	$sql_data_array = array(

							'customer_id' => $season_result['customer_id'], 

							'amount' => $season_amount							

							);

    		tep_db_perform('coupon_season_customer', $sql_data_array);

	}

		 	// now lets remove the queue entry

        	  tep_db_query("DELETE FROM `coupon_season_queue` where `customer_id`='".$season_result['customer_id'] ."' and `order_id` = '".$order_id."'");

			//and note the order

				$sql_data_array = array(

						'orders_id' => $order_id,

						'orders_status_id' =>  $order_status,

						'date_added' => date('Y-m-d H:i:s',getServerDate(false)),

						'customer_notified' => 0,

						'comments' => $season_amount . ' season tickets released',

						'user_added'=>"web"

						);

						

	tep_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);

		}

		}

  }  

  

   #############################################################

    #       Generate Unique Code                                #

    #############################################################

    

    function tep_generate_coupon_code(){

        global $order;

        

        $code_to_test = strtoupper(substr($order->customer['lastname'],0,1)  . substr($order->customer['firstname'],0,1));

        $random =  bin2hex(openssl_random_pseudo_bytes(4));

        $code_to_test .= str_replace('0', '', strtoupper($random));

        

        $coupon_details_raw = "select coupon_code from " . TABLE_COUPONS ." c where coupon_code =  '". $code_to_test . "' ";

		$coupon_details_query = tep_db_query($coupon_details_raw);

		if (tep_db_num_rows($coupon_details_query)>0){

		  tep_generate_coupon_code();

          }else{

        return $code_to_test;

        }

      }

  

  function tep_verify_coupons($order_id=''){

    

          $sql_extra = '';

          

          if (tep_not_null($order_id) && is_numeric($order_id)){

             $sql_extra = "o.orders_id = " . $order_id . " and ";

          }       

          

            $coupon_query=tep_db_query("select    c.uses_per_coupon, c.orders_id, c.coupon_id 

                                       from " . TABLE_COUPONS . " c, 

                                       orders o

                                       where 

                                       c.orders_id  = o.orders_id and

                                       o.orders_status = 3 and 

                                       ".$sql_extra."

                                       c.coupon_active='N' 

                                       ");

                                       

        $coupon=tep_db_fetch_array($coupon_query);

        while ($coupon=tep_db_fetch_array($coupon_query)){

	       	$coupon_count = tep_db_query("select coupon_id from " . TABLE_COUPON_REDEEM_TRACK . "

                                          where coupon_id = '" . tep_db_input($coupon['coupon_id'])."'");

                                             

        if (tep_db_num_rows($coupon_count) < $coupon['uses_per_coupon'] && $coupon['uses_per_coupon'] > 0 ) {

              tep_db_query("update " . TABLE_COUPONS . " set coupon_active = 'Y' where coupon_id = '" . $coupon['coupon_id'] . "'");

            }

        }

} 

  



function wannabuy()

{

	

	//wannabuy?

	

	//we must have the cart and currencies class objects available 

	//if they are not in the page where you call this class from then consider calling them in

	global $cart, $currencies, $languages_id, $FSESSION;

	

	// these should be shifted to a language file

	//define('WANNABUY_TEXT_WANT', 'Have you considered.....?');

	//define('WANNABUY_TEXT_ADD', 'Add to cart');

	

	// these are the product_ids of the programme/item that you want to promote

	// this could be shifted to an admin function - just so long as it passes over a string xxx,yyy,zzz

	

	// if($wannabuy_products_list==''){

		// $wannabuy_products_list=0;

	// }else{

		// $wannabuy_products_list = REMINDER_PRODUCTS;

	// }

	

	$wannabuy_products_list = empty(REMINDER_PRODUCTS) ? 0 : REMINDER_PRODUCTS;



		



		$wannabuy_query = tep_db_query("select p.products_id, pd.products_name, p.products_image_1,p.products_title_1,p.products_price, p.products_tax_class_id from " . TABLE_PRODUCTS . " p left join " . TABLE_PRODUCTS_DESCRIPTION . " pd on p.products_id = pd.products_id and pd.language_id = '" . $FSESSION->languages_id . "'   where p.products_status = '1' and p.products_quantity > '0' and p.products_id IN (".$wannabuy_products_list.")");

		

		 if (tep_db_num_rows($wannabuy_query))

		 {

			$display = 0;

			$wannabuy_products_display .= '<table border=1 class="table table-striped table-condensed">';

			$wannabuy_products_display .= '<th colspan = "4">'.WANNABUY_TEXT_WANT.'</th>';

			while($wannabuy_products = tep_db_fetch_array($wannabuy_query))

			{

			//print_r($wannabuy_products);

		    $name=$wannabuy_products['products_name'];

				if (!$cart->in_cart($wannabuy_products['products_id']))

				{

					$display++;

				

					$id=tep_db_input($wannabuy_products['products_id']);

					

					//require_once(DIR_WS_INCLUDES . 'functions/categories_lookup.php');

					//list($heading_name, $heading_venue,  $heading_date, $heading_time) = categories_lookup();

					$wannabuy_products_display .= '<tr id="w'.$wannabuy_products['products_id'].'">';

					

					$wannabuy_products_display .= '

					<td>

					  <form method="post" action = "'.tep_href_link(FILENAME_PRODUCT_INFO, 'products_id='.$wannabuy_products['products_id'].'&action=add_product').'">

					  <input type = "hidden" name = "qty" value = "1">

					  <input type = "hidden" name = "products_id" value = "'.$wannabuy_products['products_id'].'">

					  <input type = "hidden" name = "price" value = "'.$wannabuy_products['products_price'].'">

					  <div>

					  <input type="submit" class="btn btn-primary" value="'.WANNABUY_TEXT_ADD.'">

					  </div>

					  </span>

					  </form>

					   

					';

				

				$wannabuy_products_display .= '</td>';		

				

				//image + name

				$wannabuy_products_display.='

				<td class="heading-products-image" style="white-space: nowrap">

				

				<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $wannabuy_products['products_id']) . '">' . tep_product_small_image($wannabuy_products['products_image_1'], htmlspecialchars($wannabuy_products['products_title'])) . '</a>

				</td>' . '

				<td class="cart-text">

				<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $wannabuy_products['products_id']) . '">

				<strong>' . $name . '</strong></a>';

				$wannabuy_products_display.= '</td>';

				$wannabuy_products_display .= '  <td class="cart-text" align="right" valign="top"><strong>' . $currencies->display_price($wannabuy_products['products_price'], tep_get_tax_rate($wannabuy_products['tax_class_id']), 1) . '</strong></td>' . '</tr>';

			}

		}



		 }// end if tep_db_num

		$wannabuy_products_display .= '</td></tr></table>';

		

		if ($display > 0)

		{

		echo $wannabuy_products_display;

		}

}

 

  

  

  function tep_get_static_path($current_static_category_id = '') {

    global $stcPath_array;



    if (tep_not_null($current_static_category_id)) {

      $ecp_size = sizeof($stcPath_array);

      if ($ecp_size == 0) {

        $stcPath_new = $current_static_category_id;

      } else {

        $stcPath_new = '';

        $last_static_category_query = tep_db_query("select parent_id from " . TABLE_MAINPAGE . " where page_id = '" . (int)$stcPath_array[($sucp_size-1)] . "'");

        $last_static_category = tep_db_fetch_array($last_static_category_query);



        $current_static_category_query = tep_db_query("select parent_id from " . TABLE_MAINPAGE . " where page_id = '" . (int)$current_static_category_id . "'");

        $current_static_category = tep_db_fetch_array($current_static_category_query);



        if ($last_static_category['parent_id'] == $current_static_category['parent_id']) {

          for ($i=0; $i<($sucp_size-1); $i++) {

            $stcPath_new .= '_' . $stcPath_array[$i];

          }

        } else {

          for ($i=0; $i<$sucp_size; $i++) {

            $stcPath_new .= '_' . $stcPath_array[$i];

          }

        }

        $stcPath_new .= '_' . $current_static_category_id;



        if (substr($stcPath_new, 0, 1) == '_') {

          $stcPath_new = substr($stcPath_new, 1);

        }

      }

    } else {

      $stcPath_new = implode('_', $stcPath_array);

    }



    return 'stcPath=' . $stcPath_new;

  }





// Parse and secure the sucPath parameter values

  function tep_parse_static_category_path($stcPath) {

// make sure the category IDs are integers

    $stcPath_array = array_map('tep_string_to_int', explode('_', $stcPath));



// make sure no duplicate category IDs exist which could lock the server in a loop

    $tmp_array = array();

    $n = sizeof($stcPath_array);

    for ($i=0; $i<$n; $i++) {

      if (!in_array($stcPath_array[$i], $tmp_array)) {

        $tmp_array[] = $stcPath_array[$i];

      }

    }



    return $tmp_array;

  }





function tep_change_category_status_off($parent_id) 

{

   		//change the parent cat id

      		 tep_db_query("UPDATE " . TABLE_CATEGORIES . " set categories_status='0' where categories_id='".(int)$parent_id."'");

			

		      // $subcategories_array = array();

   			  // tep_get_subcategories($subcategories_array, $parent_id);

				  // for ($i=0, $n=sizeof($subcategories_array); $i<$n; $i++ ) 

				  // {

					 // tep_db_query("UPDATE " . TABLE_CATEGORIES . " set categories_status='0', date_expires='0000-00-00 00:00:00' where categories_id='".(int)$subcategories_array[$i]."'");

					  // change_product_status((int)$subcategories_array[$i],'1','9');

      // }				



      

    }

	//draggable

		function get_percentage($total, $number)

			{

			  if ( $total > 0 ) {

			   return round(($number * 100) / $total, 2);

			  } else {

				return 0;

			  }

			}

				
function tep_owd_costs($type='',$value=0){

		$cost_total = 0;

		if($type=='') return;

		

		if($type=="order" && defined('MODULE_FULFILLER_ONEWORLD_COST_PER_ORDER')){

			$cost_total = MODULE_FULFILLER_ONEWORLD_COST_PER_ORDER;

		

		} else if($type=="overweight" && defined('MODULE_FULFILLER_ONEWORLD_OVERWEIGHT_HANDLING_FEE')){

			if (defined("MODULE_FULFILLER_ONEWORLD_OVERWEIGHT_THRESHOLD") && MODULE_FULFILLER_ONEWORLD_OVERWEIGHT_THRESHOLD>0){

				if($value>MODULE_FULFILLER_ONEWORLD_OVERWEIGHT_THRESHOLD) {

					$cost_total = MODULE_FULFILLER_ONEWORLD_OVERWEIGHT_HANDLING_FEE;

				}

			}

		} else if($type=='quantity' && defined('MODULE_FULFILLER_ONEWORLD_COST_PER_ITEM')) {

			$cost_total = MODULE_FULFILLER_ONEWORLD_COST_PER_ITEM*$value;

		

		} else if($type=="packing" && defined('MODULE_FULFILLER_ONEWORLD_COST_PER_PACKING') && defined('SHIPPING_MAX_WEIGHT')) {

			$cost_total = MODULE_FULFILLER_ONEWORLD_COST_PER_PACKING*(ceil($value/SHIPPING_MAX_WEIGHT));

		}

		

		return $cost_total;

	}
?>