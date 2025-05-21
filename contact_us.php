<?php
/*
	osCommerce, Open Source E-Commerce Solutions 
	http://www.oscommerce.com 
	
	Copyright (c) 2003 osCommerce 
	
	 
	
	Freeway eCommerce 
	http://www.openfreeway.org
	Copyright (c) 2007 ZacWare
	
	osConcert, Online Seat Booking 
  	https://www.osconcert.com

  	Copyright (c) 2020 osConcert

	Released under the GNU General Public License
*/

// Set flag that this is a parent file
	define( '_FEXEC', 1 );
	

  require('includes/application_top.php');

  require(DIR_WS_LANGUAGES . $FSESSION->language . '/' . FILENAME_CONTACT_US);

  $error = false;

  
  if ($FREQUEST->getvalue('action') == 'send') {
	  
    $name = tep_db_prepare_input($FREQUEST->postvalue('name'));
    $email_address = tep_db_prepare_input($FREQUEST->postvalue('email'));
    $enquiry = tep_db_prepare_input($FREQUEST->postvalue('enquiry'));
	 //IF V3 CAPTCHA IS ON
	 if( defined('GOOGLE_CAPTCHA_PUBLIC_KEY_V3') && tep_not_null(GOOGLE_CAPTCHA_PUBLIC_KEY_V3)){
		 
		 if(isset($_POST['g-recaptcha-response'])){
	
            $captcha=$_POST['g-recaptcha-response'];
		    $secretKey = GOOGLE_CAPTCHA_SECRET_KEY_V3;
			$ip = $_SERVER['REMOTE_ADDR'];

			  // post request to server
			  $url = 'https://www.google.com/recaptcha/api/siteverify';
			  $data = array('secret' => $secretKey, 'response' => $captcha);

			  $options = array(
				  'http' => array(
				  'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
				  'method'  => 'POST',
				  'content' => http_build_query($data)
				)
			  );
			  $context  = stream_context_create($options);
			  $response = file_get_contents($url, false, $context);
			  $responseKeys = json_decode($response,true);
			  header('Content-type: application/json');
			  if($responseKeys["success"]) {
				  //Boolean TRUE allow code to continue only if action is contact_us_submit
			    if ($responseKeys["score"] < GOOGLE_CAPTCHA_SCORE_CONTACT_US_V3){
					$error = true;
					$messageStack->add('contact', 'Google Captcha Score Failure');					
				}
				if ($responseKeys["action"] != 'contact_us_submit'){
					$error = true;
					$messageStack->add('contact', 'Google Captcha Action Failure');					
				}
			
			  } else {
				      $error = true;
					  $messageStack->add('contact', 'Google Captcha error');
				 }
		     }// end issett
			 else{
				      $error = true;
					  $messageStack->add('contact', 'Google Captcha token not seen');				 
			 }
	 }//end google captcha code - revert to original  
	  


    if (tep_validate_email($email_address) && $error == false) {
      tep_mail(STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, EMAIL_SUBJECT, $enquiry, $name, $email_address);
      tep_redirect(tep_href_link(FILENAME_CONTACT_US, 'action=success', 'SSL'));
    } else {
      $error = true;
      $messageStack->add('contact', ENTRY_EMAIL_ADDRESS_CHECK_ERROR);
	  header("Content-type: text/html");
    }
  
 // tep_redirect(tep_href_link(FILENAME_CONTACT_US, '', 'SSL'));
  }

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_CONTACT_US), 'SSL');

  $content = CONTENT_CONTACT_US;

  require(DIR_WS_TEMPLATES . TEMPLATE_NAME . '/' . TEMPLATENAME_MAIN_PAGE);

  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>