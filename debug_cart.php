Debug file2.
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Try loading ONLY the shoppingCart class, NOT application_top.php
//require_once('includes/classes/shopping_cart.php');

$cart = new shoppingCart();

echo "<h1>Cart Debug</h1>";
echo "<p>Class loaded: " . get_class($cart) . "</p>";

if (method_exists($cart, 'reset_coupons')) {
    echo "<p><span style='color: green;'>Method reset_coupons() exists ✅</span></p>";
} else {
    echo "<p style='color: red;'>Method reset_coupons() does NOT exist ❌</span></p>";
}