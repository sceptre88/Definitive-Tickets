<?php
/*
file includes/languages/english/modules/payment/square_checkout.php

 

  Some code copyright (c) 2003-2013 osCommerce Released under the GNU General Public License 
  Some code copyright 2014 osConcert. Released under the GPL Public Licence
*/
        // Check to ensure this file is included in Freeway!
        defined('_FEXEC') or die();
	
		define('DIR_WS_HTTP_CATALOG', '');
		define("MODULE_PAYMENT_SQUARE_CHECKOUT_IMAGE", '');
		define("MODULE_PAYMENT_SQUARE_CHECKOUT_TEXT_DESCRIPTION", '');
		define("MODULE_PAYMENT_SQUARE_CHECKOUT_SORT_ORDER", '');
		define("MODULE_PAYMENT_SQUARE_CHECKOUT_STATUS", '');
		define("MODULE_PAYMENT_SQUARE_CHECKOUT_TESTMODE", '');
		define("MODULE_PAYMENT_SQUARE_CHECKOUT_SECRET", '');
		define("MODULE_PAYMENT_SQUARE_CHECKOUT_ORDER_STATUS_ID", '');
	
		define('MODULE_PAYMENT_SQUARE_CHECKOUT_DISPLAY_NAME', 'Square Checkout');
		
		define('MODULE_PAYMENT_SQUARE_CHECKOUT_WAIT', 'Please wait whilst you are redirected payments.');
		
		define('MODULE_PAYMENT_SQUARE_CHECKOUT_DESC', 'Tickets from '.STORE_OWNER.'');
		
		define('MODULE_PAYMENT_SQUARE_CHECKOUT_DESC_TITLE', 'Tickets from '.STORE_OWNER.'');
		
		define('MODULE_PAYMENT_SQUARE_CHECKOUT_GENERIC_ERROR', 'There was a problem contacting the payment server. Please try again.');
		
		define ('MODULE_PAYMENT_SQUARE_CHECKOUT_ERROR_TITLE', 'Payments Error:');
		
		define('MODULE_PAYMENT_SQUARE_CHECKOUT_REDIRECT_ERROR', 'Redirection failed from store. Order cancelled.');
		
		define ('MODULE_PAYMENT_SQUARE_CHECKOUT_ERROR_CANX_LABEL', ':: ');
		
		define ('MODULE_PAYMENT_SQUARE_CHECKOUT_CANCEL_ERROR', 'Payment failed, code: ');
		
		define ('MODULE_PAYMENT_SQUARE_CHECKOUT_SUCCESS', 'Customer has returned after making successful payment');
		
		define ('MODULE_PAYMENT_SQUARE_CHECKOUT_UNCONFIRMED', 'Customer has returned payment not yet confirmed');
		
		define ('MODULE_PAYMENT_SQUARE_CHECKOUT_UNCONFIRMED_PROBLEM', 'Customer has been returned to cancel page with incorrect bank code. Please investigate.');
		
		define ('MODULE_PAYMENT_SQUARE_CHECKOUT_WEBHOOK_UNCONFIRMED_PROBLEM', 'Webhook reports problem. Please investigate.');
		
		define ('MODULE_PAYMENT_SQUARE_CHECKOUT_WEBHOOK_FAILED', 'Webhook reports failure. Code: ');		
		
		define ('MODULE_PAYMENT_SQUARE_CHECKOUT_METADATA', STORE_NAME . ' order number: ');
		
		define ('MODULE_PAYMENT_SQUARE_CHECKOUT_WEBHOOK_SUCCESS', 'Webhook notifies payment captured');
		
		define ('MODULE_PAYMENT_SQUARE_CHECKOUT_WEBHOOK_CERT_ERROR', 'Order cancelled. Webhook certficate error.');
		
		define ('MODULE_PAYMENT_SQUARE_CHECKOUT_CURRENCY_ERROR', 'Unable to create payment, bank is set to %s but store currency is %s.');
		
		define ('MODULE_PAYMENT_SQUARE_CHECKOUT_CONFIRM_ERROR', 'Unable to complete payment. Please try again');
		
		define ('MODULE_PAYMENT_SQUARE_CHECKOUT_CONFIRM_ERROR_TYPE', 'Unable to completete payment. Bank states: ');

?>