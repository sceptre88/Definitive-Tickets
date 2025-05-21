<?php
/*
  $Id: ot_coupon.php,v 1.1.1.1 2003/09/18 19:04:58 wilt Exp $
  $Id: ot_coupon.php,v 1.1.1.1 2003/09/18 19:04:58 wilt Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
  
  Freeway eCommerce from ZacWare
  http://www.openfreeway.org

Copyright 2007 ZacWare Pty. Ltd
*/
 // Check to ensure this file is included in osConcert!
defined('_FEXEC') or die(); 

  class ot_coupon {
    var $title, $output;

    function __construct() {

      $this->code = 'ot_coupon';
      $this->header = MODULE_ORDER_TOTAL_COUPON_HEADER;
      $this->title = MODULE_ORDER_TOTAL_COUPON_TITLE;
      $this->description = MODULE_ORDER_TOTAL_COUPON_DESCRIPTION;
      $this->user_prompt = '';
      $this->enabled = MODULE_ORDER_TOTAL_COUPON_STATUS;
      $this->sort_order = MODULE_ORDER_TOTAL_COUPON_SORT_ORDER;
      $this->include_shipping = MODULE_ORDER_TOTAL_COUPON_INC_SHIPPING;
      $this->include_tax = MODULE_ORDER_TOTAL_COUPON_INC_TAX;
      $this->calculate_tax = MODULE_ORDER_TOTAL_COUPON_CALC_TAX;
      $this->tax_class  = MODULE_ORDER_TOTAL_COUPON_TAX_CLASS;
      $this->credit_class = true;
      $this->output = array();
	  $this->uses_per_user = 0;
	  $this->expire_date = '';
	  $this->output_value = 0;
	  $this->coupon_code = '';
    }

  function process() {
    global $PHP_SELF, $order, $currencies;
    $order_total=$this->get_order_total();
    $od_amount = $this->calculate_credit($order_total);
    $this->deduction = $od_amount;
    if ($this->calculate_tax != 'none') {
      $tod_amount = $this->calculate_tax_deduction($order_total, $this->deduction, $this->calculate_tax);
    }
    if ($od_amount > 0) {
      $order->info['total'] = $order->info['total'] - $od_amount;
	        $order->info['nettotal'] = $order->info['nettotal'] - $od_amount;

	  if($order->info['total']<0) $order->info['total']=0;
      $this->output[] = array('title' => $this->title . ':' . $this->coupon_code .':',
                     'text' => '<b>' . $currencies->format($od_amount) . '</b>',
                     'value' => $od_amount);
    }
  }

  function selection_test() {
    return false;
  }


  function pre_confirmation_check($order_total) {
    global $customer_id;
    return $this->calculate_credit($order_total);
    }

  function use_credit_amount() {
    return $output_string;
  }


    function credit_selection() {
      global $customer_id, $currencies, $language;
      $selection_string = '';
        $selection_string .= '<tr>' . "\n";
        $selection_string .= '  <td width="10"></td>';
        $selection_string .= '  <td class="main">' . "\n";
		$theme_query=tep_db_query("select configuration_value from configuration where configuration_title='Default theme'");
  		$theme_value=tep_db_fetch_array($theme_query);
  		$theme=$theme_value['configuration_value'];       
		$image_submit =	tep_image_button('button_redeem.gif',IMAGE_REDEEM_VOUCHER,'name="submit_redeem" style="cursor:pointer;cursor:hand;" onClick="javascript:submitFunction();"');
		$selection_string .= TEXT_ENTER_COUPON_CODE . tep_draw_input_field('gv_redeem_code') . '</td>';
        $selection_string .= '  <td align="right">' . $image_submit . '</td>';
        $selection_string .= '  <td width="10"></td>';
        $selection_string .= '</tr>' . "\n";
    return $selection_string;
    }


	function collect_posts() {
		global  $currencies, $oID, $order, $coupon_size, $coupon_error,$FREQUEST,$FSESSION;
		$coupon_error = "";
		$cc_id=$FSESSION->cc_id;
		$coupon=$FREQUEST->postvalue('coupon');
		$gv_redeem_code=$FREQUEST->postvalue('gv_redeem_code');
		if ($coupon!='' && $gv_redeem_code) {
			// get some info from the coupon table
			$coupon_sql = "select coupon_id, coupon_amount, coupon_type, coupon_minimum_order,
							uses_per_coupon, uses_per_user, restrict_to_products,
							restrict_to_categories,restrict_to_events from " . TABLE_COUPONS . "
							where coupon_code='".tep_db_input($gv_redeem_code)."'
							and coupon_active='Y'";
			// echo $coupon_sql;
	  $coupon_query=tep_db_query($coupon_sql);
      $coupon_result=tep_db_fetch_array($coupon_query);
	  $coupon_size = tep_db_num_rows($coupon_query);
        if (tep_db_num_rows($coupon_query)==0) {
			if($coupon_error==""){
				$coupon_error = ERROR_NO_INVALID_REDEEM_COUPON;
			}
        }
      if ($coupon_result['coupon_type'] != 'G') {
	  	$now_date = getServerDate();
		$date_sql = "select coupon_start_date from " . TABLE_COUPONS . "
                                where coupon_start_date <= now() and
                                coupon_code='".tep_db_input($gv_redeem_code)."'";
								//echo $date_sql;
        $date_query=tep_db_query($date_sql);
								
        //error for start date								
        if (tep_db_num_rows($date_query)==0) {
			if($coupon_error==""){
				$coupon_error = ERROR_INVALID_STARTDATE_COUPON;
			}
        }
		
		$date_sql = "select coupon_expire_date from " . TABLE_COUPONS . "
                                where coupon_expire_date >= now() and
                                coupon_code='".tep_db_input($gv_redeem_code)."'";
								//echo $date_sql;
        $date_query=tep_db_query($date_sql);
			
	   //error for end date							
        if (tep_db_num_rows($date_query)==0) {
			if($coupon_error==""){
				$coupon_error = ERROR_INVALID_FINISDATE_COUPON;
			}
        }
		else if(tep_db_num_rows($date_query)){
        	$date_res=tep_db_fetch_array($date_query);
        	$this->expire_date=format_date(date('Y-m-d',strtotime($date_res['coupon_expire_date'])));
        }
		//coupon order count
		$coupon_order_sql = "select coupon_id from " . TABLE_COUPON_REDEEM_TRACK . "
                             where coupon_id = '" . $coupon_result['coupon_id']."' and order_id='" . tep_db_input($oID) . "'";
						//echo $coupon_order_sql;				  
		$coupon_order_count = tep_db_query($coupon_order_sql);
        
		
		if (tep_db_num_rows($coupon_order_count) > 0) {
			if($coupon_error==""){
				$coupon_error = ERROR_ALREADY_ORDER_USES_COUPON;
			}
        }

        $coupon_count_sql = "select coupon_id from " . TABLE_COUPON_REDEEM_TRACK . "
                                          where coupon_id = '" . (int)$coupon_result['coupon_id']."'";
							//echo $coupon_count_sql;
		$coupon_count = tep_db_query($coupon_count_sql);
		
		//error for user per coupon								  
		if (tep_db_num_rows($coupon_count)>=$coupon_result['uses_per_coupon'] && $coupon_result['uses_per_coupon'] > 0) {
			if($coupon_error==""){
				$coupon_error = ERROR_INVALID_USES_COUPON . $coupon_result['uses_per_coupon'] . TIMES;
			}
        }
		
		$coupon_count_customer_sql = "select coupon_id from " . TABLE_COUPON_REDEEM_TRACK . "
                                                   where coupon_id = '" . (int)$coupon_result['coupon_id']."' and
                                                   customer_id = '" . (int)$FSESSION->customer_id . "'";										  
									//echo $coupon_count_customer_sql;
        $coupon_count_customer = tep_db_query($coupon_count_customer_sql);
       
		// error for user per user
        if (tep_db_num_rows($coupon_count_customer)>=$coupon_result['uses_per_user'] && $coupon_result['uses_per_user'] > 0) {
			if($coupon_error==""){
				$coupon_error = ERROR_INVALID_USES_USER_COUPON;
			}
        }
        if($coupon_result['uses_per_user']){
          $this->uses_per_user=($coupon_result['uses_per_user']-tep_db_num_rows($coupon_count_customer));
		  if($this->uses_per_user<=0) $this->uses_per_user='Nil';
        }else {
			if($this->uses_per_user<0) $this->uses_per_user=0;
        }
		$this->coupon_code = $coupon_result['coupon_id'];
		$this->output_value = $coupon_result['coupon_amount'];
        if ($coupon_result['coupon_type']=='S') {
          $coupon_amount = $order->info['shipping_cost'];
        } else {
          $coupon_amount = $currencies->format($coupon_result['coupon_amount']) . ' ';
		  
        }
        if ($coupon_result['coupon_type']=='P') $coupon_amount = $coupon_result['coupon_amount'] . '% ';
        if ($coupon_result['coupon_minimum_order']>0) $coupon_amount .= TEXT_ON_ORDERS .  $coupon_result['coupon_minimum_order'];
        $FSESSION->set('cc_id',$coupon_result['coupon_id']);
      }
	  if ($FREQUEST->postvalue('submit_redeem_coupon_x') && !$gv_redeem_code){
			if($coupon_error==""){
				$coupon_error = ERROR_NO_REDEEM_CODE;
			}
	  }
    }else if($coupon!='' && $gv_redeem_code=="") {
			if($coupon_error==""){
				$coupon_error = ERROR_NO_INVALID_REDEEM_COUPON;
			}
	}else{
		$FSESSION->remove('cc_id');
	} 
	//error for coupon not applied items 
		/*if($this->calculate_credit(1)==0){
			if($coupon_error==""){
				$coupon_error = ERROR_INVALID_ITEM;
			}
		}*/
		//error for order total > minimum order
		//echos($order->products);
		 if($order->info['subtotal'] < $coupon_result['coupon_minimum_order']){
			if($coupon_error==""){
				$coupon_error = ERROR_LOW_ORDERTOTAL;
			}
	  }
	
	    // error for coupon amount > order total
		if($coupon_result['coupon_amount'] > $order->info['total'] ){
			if(!$FSESSION->is_registered('coupon_exist')){
			//	tep_session_register('coupon_exist');
				if($coupon_error==""){
					$coupon_error = ERROR_LESSTHAN_COUPON_PRICE;
				}
			}  
		}
		
		
		// error for coupon amount equal to order total
		if($coupon_result['coupon_amount']==$order->info['total']){
			if($coupon_error==""){
				$coupon_error = ERROR_COUPON_PRICE_TOTAL;
			}
		}
  }

  
  
  function calculate_credit($amount) {
	global $customer_id, $order, $FSESSION;
	$cc_id = $FSESSION->cc_id;
	$discount = 0;

	if (!$cc_id) return $discount;

	$coupon_query = tep_db_query("select coupon_code from " . TABLE_COUPONS . " where coupon_id = '" . (int)$cc_id . "'");
	if (tep_db_num_rows($coupon_query)<=0) return $discount;

	$coupon_result = tep_db_fetch_array($coupon_query);
	$this->coupon_code = $coupon_result['coupon_code'];
	$coupon_get = tep_db_query("select coupon_amount, coupon_minimum_order, restrict_to_products, restrict_to_categories, coupon_type from " . TABLE_COUPONS ." where coupon_code = '". tep_db_input($coupon_result['coupon_code']) . "'");
	$get_result = tep_db_fetch_array($coupon_get);
	$c_deduct = $get_result['coupon_amount'];
	// if coupon type S convert so that shipping amount is subtracted	
	if ($get_result['coupon_type']=='S') $c_deduct = $order->info['shipping_cost'];
	if ($get_result['coupon_type']=='P') $c_deduct = substr($get_result['coupon_amount'],0,-1);
   
	// check if order_total > coupon minimum order
	if ($get_result['coupon_minimum_order'] > $this->get_order_total(true)) return $discount;
	
	if ($get_result['coupon_type']!='P'){
		$discount=$c_deduct;
	} else {
		$discount=$amount*$c_deduct/100;
	}
	return $discount;
  }

  function calculate_tax_deduction($amount, $od_amount, $method) {
    global $customer_id, $order, $cart, $FSESSION;
    $cc_id = $FSESSION->cc_id;
    $coupon_query = tep_db_query("select coupon_code from " . TABLE_COUPONS . " where coupon_id = '" . (int)$cc_id . "'");
    if (tep_db_num_rows($coupon_query) !=0 ) {
      $coupon_result = tep_db_fetch_array($coupon_query);
      $coupon_get = tep_db_query("select coupon_amount, coupon_minimum_order, restrict_to_products, restrict_to_categories, coupon_type from " . TABLE_COUPONS . " where coupon_code = '". tep_db_input($coupon_result['coupon_code']) . "'");
      $get_result = tep_db_fetch_array($coupon_get);
      if ($get_result['coupon_type'] != 'S') 
	  {
		  if ($get_result['restrict_to_products'] || $get_result['restrict_to_categories'] || $get_result['restrict_to_events']) 
		  {
			// What to do here.
			// Loop through all products and build a list of all product_ids, price, tax class
			// at the same time create total net amount.
			// then
			// for percentage discounts. simply reduce tax group per product by discount percentage
			// or
			// for fixed payment amount
			// calculate ratio based on total net
			// for each product reduce tax group per product by ratio amount.
			$products = $order->products;
			for ($i=0; $i<sizeof($products); $i++) {
			  $t_prid = tep_get_prid($products[$i]['id']);
			
				  $cc_query = tep_db_query("select products_tax_class_id from " . TABLE_PRODUCTS . " where products_id = '" . (int)$t_prid . "'");
			  
          if ($valid_product) {
            $valid_array[] = array('product_id' => $t_prid,
                                 'products_price' => $products[$i]['final_price'] * $products[$i]['quantity'],
                                 'products_tax_class' => $cc_result['products_tax_class_id']);
            $total_price += $products[$i]['final_price'] * $products[$i]['quantity'];
          }
        }
        if ($valid_product) {
        if ($get_result['coupon_type'] == 'P') {
          $ratio = $get_result['coupon_amount']/100;
        } else {
			if($total_price!=0)
          $ratio = $od_amount / $total_price;
        }
        if ($get_result['coupon_type'] == 'S') $ratio = 1;
          if ($method=='Credit Note') {
            $tax_rate = tep_get_tax_rate($this->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
            $tax_desc = tep_get_tax_description($this->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
            if ($get_result['coupon_type'] == 'P') {
              $tod_amount = $od_amount / (100 + $tax_rate)* $tax_rate;
            } else {
              $tod_amount = $order->info['tax_groups'][$tax_desc] * $od_amount/100;
            }
            $order->info['tax_groups'][$tax_desc] -= $tod_amount;
            $order->info['total'] -= $tod_amount;
          } else {
            for ($p=0; $p<sizeof($valid_array); $p++) {
              $tax_rate = tep_get_tax_rate($valid_array[$p]['products_tax_class'], $order->delivery['country']['id'], $order->delivery['zone_id']);
              $tax_desc = tep_get_tax_description($valid_array[$p]['products_tax_class'], $order->delivery['country']['id'], $order->delivery['zone_id']);
              if ($tax_rate > 0) {
                $tod_amount[$tax_desc] += ($valid_array[$p]['products_price'] * $tax_rate)/100 * $ratio;
                $order->info['tax_groups'][$tax_desc] -= ($valid_array[$p]['products_price'] * $tax_rate)/100 * $ratio;
                $order->info['total'] -= ($valid_array[$p]['products_price'] * $tax_rate)/100 * $ratio;
              }
            }
          }
        }
      } else 
	  {
        if ($get_result['coupon_type'] =='F') { 
          $tod_amount = 0;
          if ($method=='Credit Note') {
            $tax_rate = tep_get_tax_rate($this->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
            $tax_desc = tep_get_tax_description($this->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
            $tod_amount = $od_amount / (100 + $tax_rate)* $tax_rate;
            $order->info['tax_groups'][$tax_desc] -= $tod_amount;
          } else { 
            $ratio1 = $od_amount/$amount;
			if(is_array($order->info['tax_groups'])){
           reset($order->info['tax_groups']);
		foreach($order->info['tax_groups'] as $key => $value)
		{
              $tax_rate = tep_get_tax_rate_from_desc($key);
              $net = $tax_rate * $order->info['tax_groups'][$key];
              if ($net>0) {
                $god_amount = $order->info['tax_groups'][$key] * $ratio1;
                $tod_amount += $god_amount;
                $order->info['tax_groups'][$key] = $order->info['tax_groups'][$key] - $god_amount;
              }
            }
			}
          }
          $order->info['total'] -= $tod_amount;
        }
        if ($get_result['coupon_type'] =='P') {
          $tod_amount=0;
          if ($method=='Credit Note') {
            $tax_desc = tep_get_tax_description($this->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
            $tod_amount = $order->info['tax_groups'][$tax_desc] * $od_amount/100;
            $order->info['tax_groups'][$tax_desc] -= $tod_amount;
          } else {

		foreach($order->info['tax_groups'] as $key => $value)
		{
              $god_amount=0;
              $tax_rate = tep_get_tax_rate_from_desc($key);
              $net = $tax_rate * $order->info['tax_groups'][$key];
              if ($net>0) {
                $god_amount = $order->info['tax_groups'][$key] * $get_result['coupon_amount']/100;
                $tod_amount += $god_amount;
                $order->info['tax_groups'][$key] = $order->info['tax_groups'][$key] - $god_amount;
              }
            }
          }
          $order->info['tax'] -= $tod_amount;
        }
      }
    }
    }
    return $tod_amount;
  }

function update_credit_account($i) {
  return false;
 }

 function apply_credit() {
  // global $insert_id, $customer_id, $REMOTE_ADDR;
  global $oID, $customer_id, $REMOTE_ADDR, $FSESSION;
   if($customer_id=="")$customer_id='0';
   $cc_id = $FSESSION->cc_id;
   if ($this->deduction !=0) {
    // tep_db_query("insert into " . TABLE_COUPON_REDEEM_TRACK . " (coupon_id, redeem_date, redeem_ip, customer_id, order_id) values ('" . $cc_id . "', now(), '" . $REMOTE_ADDR . "', '" . $customer_id . "', '" . $insert_id . "')");
	 tep_db_query("insert into " . TABLE_COUPON_REDEEM_TRACK . " (coupon_id, redeem_date, redeem_ip, customer_id, order_id) values ('" . $cc_id . "', now(), '" . $REMOTE_ADDR . "', '" . $customer_id . "', '" . $oID . "')");
   }
   $FSESSION->remove('cc_id');
 }

  function get_order_total() {
    global  $order, $cart, $customer_id, $oID,$FSESSION;
    $cc_id = $FSESSION->cc_id;
    $order_total = $order->info['total'];
  
// Check if gift voucher is in cart and adjust total
    //$products = $cart->get_products();
	$products=$order->products;//print_r($order->products);
    for ($i=0; $i<sizeof($products); $i++) {
      $t_prid = tep_get_prid($products[$i]['id']);
      $gv_query = tep_db_query("select products_price, products_tax_class_id, products_model from " . TABLE_PRODUCTS . " where products_id = '" . (int)$t_prid . "'");
      $gv_result = tep_db_fetch_array($gv_query);
      if (ereg('^GIFT', addslashes($gv_result['products_model']))) {
        //$qty = $cart->get_quantity($t_prid);
		$qty=$this->get_product_quantity($t_prid);
        $products_tax = tep_get_tax_rate($gv_result['products_tax_class_id']);
        if ($this->include_tax =='false') {
           $gv_amount = $gv_result['products_price'] * $qty;
        } else {
          $gv_amount = ($gv_result['products_price'] + tep_calculate_tax($gv_result['products_price'],$products_tax)) * $qty;
        }
        $order_total=$order_total - $gv_amount;
      }
    }
    if ($this->include_tax == 'false') $order_total=$order_total-$order->info['tax'];
    if ($this->include_shipping == 'false') $order_total=$order_total-$order->info['shipping_cost'];
  
  $coupon_query=tep_db_query("select coupon_code  from " . TABLE_COUPONS . " where coupon_id='".(int)$cc_id."'");
		if (tep_db_num_rows($coupon_query) !=0) {
			$coupon_result=tep_db_fetch_array($coupon_query);

			$coupon_get_sql = "select restrict_to_products,restrict_to_categories from " . TABLE_COUPONS . " where coupon_code='".tep_db_input($coupon_result['coupon_code'])."' and (restrict_to_products!='' or restrict_to_categories!='' or restrict_to_event_categories!='' or restrict_to_events!='' or restrict_to_sub_categories!='' or restrict_to_subscription!='' or restrict_to_service_categories!='' or restrict_to_service!='')";
			//echos($coupon_get_sql);
			$coupon_get=tep_db_query($coupon_get_sql);
			$get_result=tep_db_fetch_array($coupon_get);
			$in_cat = true;//print_r($get_result);
			if ($get_result['restrict_to_categories']) {
				$cat_ids = preg_split("/[,]/", $get_result['restrict_to_categories']);
				for ($i = 0; $i <count($cat_ids); $i++) {
					for ($j=0; $j<count($products); $j++) {
						if ($products[$j]['products_type']!='P') continue;
						if(strpos(",".$get_result['restrict_to_products'].",",",".$products[$j]['id'].",")!==false) continue;
						$cat_query = tep_db_query("select products_id from products_to_categories where products_id = '" . (int)$products[$j]['id'] . "' and categories_id = '" . (int)$cat_ids[$i] . "'");
						if (tep_db_num_rows($cat_query) !=0) {
							$total_price += $this->get_product_price($products[$j]['id'],$products[$j]['qty'],$include_tax);
						//	$order->products[$j]['coupon_apply']=1;
						}
					}
				}
			}
			if ($get_result['restrict_to_products']) {
				$pr_ids = preg_split("/[,]/", $get_result['restrict_to_products']);
				for ($i = 0; $i < count($pr_ids); $i++) {
					for ($j = 0; $j<count($products); $j++) {
						if ($products[$j]['products_type']!='P') continue;
						if ($products[$j]['id'] == $pr_ids[$i]) {
							$total_price += $this->get_product_price($products[$j]['id'],$products[$j]['qty'],$include_tax);
						//	$order->products[$j]['coupon_apply']=1;
						}
					}
				}
			}

	
			if ($this->include_shipping == 'true') $total_price += $order->info['shipping_cost'];
			if(tep_db_num_rows($coupon_get)>0) {
				$order_total = $total_price; 
			 }
		}
		return $order_total;
  }

function get_product_price($product_id,$qty,$include_tax) 
{
    global $cart, $order;
    $products_id = tep_get_prid($product_id);
     $product_query = tep_db_query("select products_id, products_price, products_tax_class_id, products_weight from " . TABLE_PRODUCTS . " where products_id='" . tep_db_input($product_id) . "'");
    if ($product = tep_db_fetch_array($product_query)) 
	{
      $prid = $product['products_id'];

      $products_tax = tep_get_tax_rate($product['products_tax_class_id']);
      $products_price = $cart->get_product_price($prid,$product['products_price']);
      if ($this->include_tax == 'true' && $include_tax) {
        $total_price += ($products_price + tep_calculate_tax($products_price, $products_tax)) * $qty;
      } else {
        $total_price += $products_price * $qty;
      }
	  
    //if ($this->include_shipping == 'true') $total_price += $order->info['shipping_cost'];
	return $total_price;
}

function get_product_quantity($product_id)
{
	global $order;
    $products_array =$order->products;
	for ($ii = 1; $ii<=sizeof($products_array); $ii++) {
       if (tep_get_prid($products_array[$ii-1]['id']) == $product_id) {
            return $products_array[$ii-1]['qty'];
         }
     }
}

    function check() {
      if (!isset($this->check)) {
        $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_ORDER_TOTAL_COUPON_STATUS'");
        $this->check = tep_db_num_rows($check_query);
      }

      return $this->check;
    }

    function keys() {
      return array('MODULE_ORDER_TOTAL_COUPON_STATUS', 'MODULE_ORDER_TOTAL_COUPON_SORT_ORDER', 'MODULE_ORDER_TOTAL_COUPON_INC_SHIPPING', 'MODULE_ORDER_TOTAL_COUPON_INC_TAX', 'MODULE_ORDER_TOTAL_COUPON_CALC_TAX', 'MODULE_ORDER_TOTAL_COUPON_TAX_CLASS');
    }

    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Display Total', 'MODULE_ORDER_TOTAL_COUPON_STATUS', 'true', 'Do you want to display the Discount Coupon value?', '6', '1','tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_ORDER_TOTAL_COUPON_SORT_ORDER', '9', 'Sort order of display.', '6', '2', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function ,date_added) values ('Include Shipping', 'MODULE_ORDER_TOTAL_COUPON_INC_SHIPPING', 'true', 'Include Shipping in calculation', '6', '5', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function ,date_added) values ('Include Tax', 'MODULE_ORDER_TOTAL_COUPON_INC_TAX', 'true', 'Include Tax in calculation.', '6', '6','tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function ,date_added) values ('Re-calculate Tax', 'MODULE_ORDER_TOTAL_COUPON_CALC_TAX', 'None', 'Re-Calculate Tax', '6', '7','tep_cfg_select_option(array(\'None\', \'Standard\', \'Credit Note\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Tax Class', 'MODULE_ORDER_TOTAL_COUPON_TAX_CLASS', '0', 'Use the following tax class when treating Discount Coupon as Credit Note.', '6', '0', 'tep_get_tax_class_title', 'tep_cfg_pull_down_tax_classes(', now())");
    }

    function remove() {
      $keys = '';
      $keys_array = $this->keys();
      for ($i=0; $i<sizeof($keys_array); $i++) {
        $keys .= "'" . $keys_array[$i] . "',";
      }
      $keys = substr($keys, 0, -1);

      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in (" . $keys . ")");
    }
  }
?>
