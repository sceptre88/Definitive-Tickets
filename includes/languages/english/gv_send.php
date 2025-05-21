<?php // Check to ensure this file is included in osConcert!
defined('_FEXEC') or die();

define('GV_FAQ','Gift Voucher FAQ');
define('HEADING_TITLE', 'Send gift voucher');
define('NAVBAR_TITLE', 'Send gift voucher');
define('EMAIL_SUBJECT', 'Request from ' . STORE_NAME);
define('HEADING_TEXT','<br>Please enter below the details of the gift voucher you would like to send. For more information, please see our <a href="' . tep_href_link('FILENAME_GV_FAQ','','NONSSL ').'">'.GV_FAQ.'.</a><br>');
define('ENTRY_NAME', 'Recipient\'s name:');
define('ENTRY_EMAIL', 'Email address of recipient:');
define('ENTRY_MESSAGE', 'Message to recipient:');
define('ENTRY_AMOUNT', 'Amount of vouchers:');
define('ERROR_ENTRY_AMOUNT_CHECK', '&nbsp;&nbsp;<span class="errorText">Invalid amount</span>');
define('ERROR_ENTRY_EMAIL_ADDRESS_CHECK', '&nbsp;&nbsp;<span class="errorText">Invalid email address</span>');
define('MAIN_MESSAGE', 'You have decided to post a voucher worth %s to %s whose email address is %s<br><br>The text accompanying the email will be like this<br><br >Dear %s<br><br>
                         You have been sent a %s coupon by %s');

define('PERSONAL_MESSAGE', '%s says');
define('TEXT_SUCCESS', 'Congratulations, your voucher was sent successfully');


define('EMAIL_SEPARATOR', '----------------------------- ---------------------------------');
define('EMAIL_GV_TEXT_HEADER', 'Congratulations, you have received a voucher worth %s');
define('EMAIL_GV_TEXT_SUBJECT', 'A gift from %s');
define('EMAIL_GV_FROM', 'This coupon was sent to you by %s');
define('EMAIL_GV_MESSAGE', 'With a message: ');
define('EMAIL_GV_SEND_TO', 'Hi, %s');
define('EMAIL_GV_REDEEM', 'To redeem this voucher, please click on the link below. Please write down the code:%s. In case you have problems.');
define('EMAIL_GV_LINK', 'Please click to redeem ');
define('EMAIL_GV_VISIT', ' or visit ');
define('EMAIL_GV_ENTER', ' and enter code ');
define('EMAIL_GV_FIXED_FOOTER', 'If you have trouble redeeming it, click the automated link above, ' . "\n" .
                                 'You can also enter the voucher code during checkout.' . "\n\n");
define('EMAIL_GV_SHOP_FOOTER', '');
?>