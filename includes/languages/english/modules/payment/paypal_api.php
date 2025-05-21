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

	define('MODULE_PAYMENT_PAYPAL_API_DISPLAY_NAME', '');
	define('MODULE_PAYMENT_PAYPAL_API_TEXT_CONFIRMATION','You are about to be forwarded to the PayPal Secure Server to make payment:<br>You must return to the store to complete your order after agreeing payment.<br>If you wish to cancel your payment or change your order please use the cancel button provided at PayPal and wait to be returned to the store.<br><h5>Please do NOT use your browser back button.</h5>');
	define('MODULE_PAYMENT_PAYPAL_API_SORT_ORDER', '');
	define('MODULE_PAYMENT_PAYPAL_API_STATUS', '');
	define('MODULE_PAYMENT_PAYPAL_API_ORDER_STATUS_ID', '');
	define('MODULE_PAYMENT_PAYPAL_API_TEXT_DESCRIPTION', '');
	define('MODULE_PAYMENT_PAYPAL_API_NOTIFY_URL', '');
	define('MODULE_PAYMENT_PAYPAL_API_STATUS', '');
	define('MODULE_PAYMENT_PAYPAL_API_TEXT_DESCRIPTION', 'Paypal');
	define('MODULE_PAYMENT_PAYPAL_API_TEXT_CURL', 'cURL Enabled');
	define('MODULE_PAYMENT_PAYPAL_API_INFO', 'Paypal Message');
	define('MODULE_PAYMENT_PAYPAL_API_GENERAL_ERROR', 'Unexpected Error!...PLEASE TRY AGAIN');// [Click CONTINUE BUTTON]

	define('MODULE_PAYMENT_PAYPAL_API_POST_ERROR', 'Access token not seen post order');
	define('MODULE_PAYMENT_PAYPAL_API_GET_ERROR', 'Unable to obtain Approval URL at PayPal');
    define('MODULE_PAYMENT_PAYPAL_API_CANCEL', 'Payment cancelled at PayPal');
	define('MODULE_PAYMENT_PAYPAL_API_ERROR_TITLE', 'PayPal Payment Error');
	define('PROBLEM_ERROR', ' -problem');
	define('INFIELD_ERROR', 'in field');
	define('MODULE_PAYMENT_PAYPAL_API_CANX_FAILED','Your attempt to cancel payment has not succeeded.');
	define('MODULE_PAYMENT_PAYPAL_API_CANX','Cancelled by customer from PayPal');
	define('MODULE_PAYMENT_PAYPAL_API_CUSTOMER_RETURNS','Customer returns from PayPal:: Payment completed.');
	define('MODULE_PAYMENT_PAYPAL_API_TICKETS','Order invoice and tickets emailed to customer');
    define('MODULE_PAYMENT_PAYPAL_API_VAL_ERROR',' Please CHECK your Zip and City, State match...or PLEASE TRY AGAIN [Click CONTINUE BUTTON]');
	define('MODULE_PAYMENT_PAYPAL_API_TEXT_NO_ORDER','Unable to process your order. Please contact the store owner. ');
    define('MODULE_PAYMENT_PAYPAL_API_PRICE_DIFF','PayPal rounding adjustment ');
	define('MODULE_PAYMENT_PAYPAL_API_AWAITING', 'Customer returned after payment. Unable to confirm status.');
?>