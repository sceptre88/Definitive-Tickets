<?php 
defined('_FEXEC') or die();

if($i>1){$item =ITEMS;}else{$item =ITEM;}
define('MODULE_NAVBAR_SHOPPING_CART_CONTENTS', '<i class="bi-cart2"></i><span id="total_head_seats" class="d-inline d-sm-none d-md-inline"> %1$s ' . $item . '</span>');
define('MODULE_NAVBAR_SHOPPING_CART_HAS_CONTENTS', '<span id="total_seats">%s ' . $item . ' %s</span>');
define('MODULE_NAVBAR_SHOPPING_CART_CHECKOUT', '<i class="bi-arrow-90deg-right"></i> ' . HEADER_TITLE_CHECKOUT . '');
define('MODULE_NAVBAR_SHOPPING_CART_PRODUCT', '<div class="indiv_ticket ticket_id_%s"><a class="dropdown-item" href="' . tep_href_link('product_info.php', 'products_id=%s') . '"><span class= "cnt">%s</span>&nbsp;x&nbsp;<span class="pn">%s</span><span class="pp" style="display:none">%s</span></a><span class="top_category_name" style="display:none">%s</span></div>');
?>
		<li class="dropdown">
		<a class="nav-link dropdown-toggle" href="shopping_cart.php" id="navDropdownCart" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		<?php echo sprintf(MODULE_NAVBAR_SHOPPING_CART_CONTENTS, $cart->count_contents_gt()); ?>
		</a>
		<ul>
		<li>
        <?php 
		echo '<a class="dropdown-item" href="' . tep_href_link('shopping_cart.php') . '">' . sprintf(MODULE_NAVBAR_SHOPPING_CART_HAS_CONTENTS, $cart->count_contents_gt(), $currencies->format($cart->show_total())) . '</a>';
		
		echo '<div class="dropdown-divider"></div>' . PHP_EOL;    
		echo '<div class="ticket_list">' . PHP_EOL; 
		if ($cart->count_contents() > 0) 
		{
		$products = $cart->get_products();//var_dump($products);
		foreach ($products as $k => $v) 
		{
			if ($v['sku'] == 6){
				$v['quantity'] = 1;
			}
		//2021 get price incl tax
		$tax_rate=tep_get_tax_rate($v["tax_class_id"]);
        $products_price=tep_add_tax(tep_get_plain_products_price($v['final_price']), $tax_rate);		
		//echo sprintf(MODULE_NAVBAR_SHOPPING_CART_PRODUCT,$v['id'], $v['id'],$v['quantity'], $v['name'], $v['final_price'], $v['top_category_name']);
		echo sprintf(MODULE_NAVBAR_SHOPPING_CART_PRODUCT,$v['id'], $v['id'],$v['quantity'], $v['name'], $products_price, $v['top_category_name']);
		}
		}
		echo '</div>' . PHP_EOL; 
		echo '<div class="dropdown-divider"></div>' . PHP_EOL;   
		echo '<a class="dropdown-item" href="' . tep_href_link('checkout_shipping.php', '', 'SSL') . '">' . MODULE_NAVBAR_SHOPPING_CART_CHECKOUT . '</a>' . PHP_EOL;
		?>
		</li>
		</ul>
		<div id="ajax_status"></div>
		</li>