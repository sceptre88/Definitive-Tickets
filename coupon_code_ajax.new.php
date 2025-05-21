<?php
/**
 * Coupon Code AJAX Handler
 */

define('_FEXEC', 1);
require('includes/application_top.php');
require_once(DIR_WS_CLASSES . 'shopping_cart.php'); // RBE.DS

function get_coupon_discount_display($coupon) {
    if ($coupon['coupon_type'] === 'F') {
        return "$" . number_format($coupon['coupon_amount'], 2);
    } elseif ($coupon['coupon_type'] === 'P') {
        return number_format($coupon['coupon_amount'], 0) . "%";
    }
    return "";
}

function is_coupon_valid($coupon) {
    $now = date('Y-m-d');
    if (!empty($coupon['coupon_start_date']) && $coupon['coupon_start_date'] > $now) return false;
    if (!empty($coupon['coupon_expire_date']) && $coupon['coupon_expire_date'] < $now) return false;
    return true;
}

function fetch_coupon($code) {
    $query = tep_db_query("
        SELECT coupon_id, coupon_code, coupon_amount, coupon_type, exclusive, coupon_start_date, coupon_expire_date 
        FROM " . TABLE_COUPONS . " 
        WHERE coupon_code = '" . tep_db_input($code) . "'
    ");
    return tep_db_fetch_array($query);
}

function render_applied_coupons($cart) {
    $output = '';
    foreach ($cart->get_coupons() as $c) {
        $coupon = fetch_coupon($c['code']);
        if (!$coupon) continue;
        $discount = get_coupon_discount_display($coupon);
        $output .= '<div id="valid_code_' . htmlspecialchars($coupon['coupon_code']) . '">';
        $output .= '<strong>' . htmlspecialchars($coupon['coupon_code']) . '</strong>: ' . $discount . ' discount ';
        $output .= '<a href="#" onclick="remove_coupon(\'' . htmlspecialchars($coupon['coupon_code']) . '\'); return false;">[Remove]</a>';
        $output .= '</div>';
    }
    return $output;
}

// Remove coupon
if (isset($_GET['x'])) {
    $code = tep_db_prepare_input($_GET['x']);
    $cart->remove_coupon($code);
    echo render_applied_coupons($cart);
    exit;
}

// Add coupon
if (isset($_GET['q'])) {
    $code = tep_db_prepare_input($_GET['q']);
    $coupon = fetch_coupon($code);

    if ($coupon) {
        if (!is_coupon_valid($coupon)) {
            echo '<div style="color:red">This coupon is not currently valid.</div>';
            exit;
        }

        $is_exclusive = (int)$coupon['exclusive'] === 1;
        $success = $cart->add_coupon($coupon['coupon_code'], $is_exclusive);

        if (!$success) {
            echo '<div style="color:red">This coupon could not be applied (possibly due to exclusivity rules).</div>';
            exit;
        }

        echo render_applied_coupons($cart);
    } else {
        echo '<div style="color:red">Invalid coupon code</div>';
    }
    exit;
}
?>