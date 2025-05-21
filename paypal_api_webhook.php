<?php
/*
	osCommerce, Open Source E-Commerce Solutions 
	http://www.oscommerce.com 
	
	Copyright (c) 2003 osCommerce 
	
	 
	
	Freeway eCommerce 
	http://www.openfreeway.org
	Copyright (c) 2007 ZacWare
	
	osConcert, Online Seat Booking 
  	http://www.osconcert.com

  	Copyright (c) 2020 osConcert

	Released under the GNU General Public License
*/
/* also code from
.---------------------------------------------------------------------------.
|    Software: PayPal-PPP                                                   |
|     Version: 0.2beta                                                      |
|        Date: 2016-02-09                                                   |
| Description: handle paypal-plus webhooks                                  |
|     Contact: info@andreas-guder.de                                        |
| ------------------------------------------------------------------------- |
|      Author: Andreas Guder                                                |
|     Contact: info@andreas-guder.de                                        |
| Copyright (c) 2015, Andreas Guder.                                        |
| ------------------------------------------------------------------------- |
|     License:  GNU Public License V2.0                                     |
|               http://www.gnu.org/licenses/gpl-2.0.html                    |
'--------------------------------------------------------------------------ö'
*/

// Set flag that this is a parent file
define('_FEXEC', 1);

//are we having instant callback?

sleep(10);

require ('./includes/application_top.php');
include_once ('./includes/functions/sessions.php');
$postdata = file_get_contents("php://input");


if (empty($postdata))
{   error_log('Webhook: no content');
    header("HTTP/1.0 400 Bad Request");
    echo 'No Content';
    exit;
}
$body = json_decode($postdata);


if (!is_object($body))
{   error_log('Webhook: JSON-Object Required');
    header("HTTP/1.0 500 Internal Server Error");
    echo 'JSON-Object Required';
    exit;
}

if (empty($body->id))
{   error_log('TTP/1.0 510 IDs required');
    header("HTTP/1.0 510 IDs required");
    header("Content-Length: 0");
    header("Connection: Close");
    exit;
}


    if (!empty($body->id))
    {
        $parent_payment = $body->id;
		$order_id = $body->resource->transactions[0]->invoice_number;
        $note = $body->event_type;
		$sale_id = $body->resource->id;
		
    }else{
		header("HTTP/1.0 510 More IDs required");
		header("Content-Length: 0");
		header("Connection: Close");
    exit;		
	}
	
#############################################################################
#   only working on completed sales - note all others
#############################################################################	

if ($body->event_type == 'PAYMENT.SALE.COMPLETED' ){
#############################################################################
#   create token to callback to PayPal
#############################################################################

define('API_CREDENTIALS', MODULE_PAYMENT_PAYPAL_API_ID . ':' . MODULE_PAYMENT_PAYPAL_API_SECRET);

$data_url = trim(MODULE_PAYMENT_PAYPAL_API_ADMIN) . '/logs/data_live.json';
$ppurl = 'api.paypal.com';

if (MODULE_PAYMENT_PAYPAL_API_TEST_MODE == 'True')
{
    $data_url = trim(MODULE_PAYMENT_PAYPAL_API_ADMIN) . '/logs/data_test.json';
    $ppurl = 'api.sandbox.paypal.com';
}

include ('includes/classes/payment.php');
$payment = new payment('paypal_api');
$payment->after_process();
$paypal_api = new paypal_api;


if (empty($_SESSION['access_token']))
{
    # check for valid access token
    $ts_now = time();
    $jsonDATA = (array)json_decode(file_get_contents($data_url, true));
    if (!empty($jsonDATA))
    {
        $_SESSION['expiry'] = $jsonDATA['expiry'];
        $_SESSION['access_token'] = $jsonDATA['access_token'];
        $_SESSION['app_id'] = $jsonDATA['app_id'];
        $_SESSION['token_type'] = $jsonDATA['token_type'];
        $_SESSION['webprofilID'] = $jsonDATA['webprofilID'];
    }
    else
    {
        $jsonDATA['expiry'] = 0;
    }

    if ($ts_now > $jsonDATA['expiry'])
    {
        $url = 'https://' . $this->ppurl . '/v1/oauth2/token';
        $JSONrequest = 'grant_type=client_credentials';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_SSLCERT, $sslcertpath);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept: application/json'
        ));
        curl_setopt($ch, CURLOPT_USERPWD, API_CREDENTIALS);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $JSONrequest);
		
        $result = curl_exec($ch);
			if(curl_error($ch))	{
				error_log(curl_error($ch));
			}
        $resultGetAccessToken = json_decode($result, true);

        curl_close($ch);
        $_SESSION['expiry'] = time() + $resultGetAccessToken['expires_in'];
        $_SESSION['access_token'] = $resultGetAccessToken['access_token'];
        $_SESSION['app_id'] = $resultGetAccessToken['app_id'];
        $_SESSION['token_type'] = $resultGetAccessToken['token_type'];
        $jsonSTRING = '{ "expiry":"' . $resultGetAccessToken['expires_in'] . '" , "access_token":"' . $_SESSION['access_token'] . '" , "app_id":"' . $_SESSION['app_id'] . '","token_type":"' . $_SESSION['token_type'] . '","webprofilID":"' . $_SESSION['webprofilID'] . '"}';

        if (MODULE_PAYMENT_PAYPAL_API_ADMIN_USE == 'True')
        {
            file_put_contents($this->data_url, $jsonSTRING);
        }
    }
}
#############################################################################
#   now callback to PayPal
#############################################################################

    $url = 'https://' . $ppurl . '/v1/payments/sale/' . $sale_id;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $_SESSION['access_token']
    ));

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);    
    $result = curl_exec($ch);
    $resultExecutePayment = json_decode($result , true);
	
	//error_log (print_r($resultExecutePayment));
	
    if (curl_error($ch))
    {
        $resultExecutePayment = curl_error($ch);
		exit();
    }
    curl_close($ch);


    if (strtoupper($resultExecutePayment['state']) != 'COMPLETED')
    {
        //error_log("197 ".$resultExecutePayment['state'] );
		
		exit();
    }
    else 
    {

        $this_order_id = $resultExecutePayment['invoice_number'];
		require_once('includes/classes/order.php');
        $order = new order($this_order_id );
		error_log ($this_order_id." ".$note);
		
// change order status

				$order_status=tep_db_query("select orders_status from orders where orders_id='".$this_order_id."'");
				$order_result= tep_db_fetch_array($order_status);
				
				$status = MODULE_PAYMENT_PAYPAL_API_COMP_ORDER_STATUS_ID;
				$delivered ='yes';
							
				if ($order_result['orders_status']!= $status){//completed status not found 			
						//update the status						
					
					$status = MODULE_PAYMENT_PAYPAL_API_COMP_ORDER_STATUS_ID;
					$sql_data_array = array(
						'orders_status' => $status,
									);
					tep_db_perform(TABLE_ORDERS, $sql_data_array, "update", "orders_id='" . $this_order_id. "'");
					    //update status history
					$sql_data_array = array('orders_id' => $this_order_id,
					'orders_status_id' => $status,
					'date_added' => date('Y-m-d H:i:s',getServerDate(false)), 
					'customer_notified' => '0',
					'comments' => 'Webhook confirms payment captured.');

					tep_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);
					
					//better late than never - update the orders products table
					  tep_db_query("update orders_products set orders_products_status = ". $status . " where orders_id = '" . $this_order_id . "'");
					
					
		
				
					
//send order emails 
					//(1) run through the order products list and compile $ order_is printable
					$order_is_printable = 0;
					for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {								
		                 $order_is_printable = $order_is_printable + $order->products[$i]['is_printable'];
					}
					//(2) if printable setup ticket and generate filename
					$filename="";
							
					if($order_is_printable > 0 && EMAIL_PDF_DELIVERED_ONLY=='true' && E_TICKETS == 'true' && $status == E_TICKET_STATUS ){ 
					//if( EMAIL_PDF_DELIVERED_ONLY=='true' && E_TICKETS == 'true' && $status == E_TICKET_STATUS );{
					try
					  {
						  require_once('includes/functions/products_ticket.php');
						  $filename= (create_checkout_pdf($this_order_id));
						  }
						  
					 //catch exception
					   catch(Exception $e)
						  {
						   $delivered ='no';
						
						 }	
						 
						try {
						if(function_exists('update_season_queue')){
						update_season_queue((int)$this_order_id, $status );          
						}
						} catch (Exception $e) {
						unset($e);
						}
						 
					 //set ticket printed 
		                   //tep_db_query("update  " . TABLE_ORDERS . "  set ticket_printed='Y' where orders_id = (" . $this_order_id . ")");
						   
						     }
		
		      
			
				$email_sent=tep_db_query("select * from email_data where order_id='".$this_order_id."'");
				if (tep_db_num_rows($email_sent)>0) {
					 try{
				while($email_result= tep_db_fetch_array($email_sent)){			
					tep_send_default_email("PRD",unserialize(base64_decode($email_result['merge_data'])),unserialize(base64_decode($email_result['send_data'])),$filename);
				}
				}
				
				catch(Exception $e)
				{
					$delivered = "no";
				}
				
//if delivered then delete email from table
  if ($delivered == 'yes'){
	  tep_db_query("delete from email_data where order_id='".$this_order_id."'");
	  tep_db_query("update  " . TABLE_ORDERS . "  set ticket_printed='Y' where orders_id = (" . $this_order_id . ")");
	  
	  				//update status history
		$sql_data_array = array('orders_id' => $this_order_id,
		'orders_status_id' => $status,
		'date_added' => date('Y-m-d H:i:s',getServerDate(false)), 
		'customer_notified' => '1',
		'comments' => 'Order invoice and tickets emailed to customer.');

		tep_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);
  }
  }		
    
			
			
        }// end updating order as previously not 'delivered'
		else{
           //to update status history uncomment this - otherwise you may get a long list of comments if the webhook gets
		   //multiple hits
            $sql_data_array = array(
                'orders_id' => $this_order_id,
                'orders_status_id' => MODULE_PAYMENT_PAYPAL_API_COMP_ORDER_STATUS_ID,
                'date_added' => date('Y-m-d H:i:s', getServerDate(false)) ,
                'customer_notified' => 0,
                'comments' => 'Webhook confirms transaction completed.'
            );

            tep_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array); 
	}
	    header("HTTP/1.0 200 OK");
        header("Content-Length: 0");
        header("Connection: Close");
        exit();
		}
}// end completed message from paypal PAYMENT.SALE.COMPLETED
else{
	

				$order_status=tep_db_query("select orders_status from orders where orders_id='".$order_id."'");
				$order_result= tep_db_fetch_array($order_status);
				$status = $order_result['orders_status'];
		
									
				if ($status != MODULE_PAYMENT_PAYPAL_API_COMP_ORDER_STATUS_ID){
					//completed status  not found 	(do nothing if completed even though should not get here)								
					if( $note == "PAYMENTS.PAYMENT.CREATED" && $status == MODULE_PAYMENT_PAYPAL_API_ORDER_STATUS_ID){
						// payment authorised at PayPal but order in 'pending' mode 
						// change it to awaiting in case cron in user_error
						$status = MODULE_PAYMENT_PAYPAL_API_AWAITS_ORDER_STATUS_ID;
						$sql_data_array = array(
						   'orders_status' => $status,
									);
					tep_db_perform(TABLE_ORDERS, $sql_data_array, "update", "orders_id='" . $order_id. "'");
					
					//better late than never - update the orders products table
					  tep_db_query("update orders_products set orders_products_status = ". $status . " where orders_id = '" . $order_id . "'");
						
					
				    //update status history
					$sql_data_array = array('orders_id' => $order_id,
					'orders_status_id' => $status,
					'date_added' => date('Y-m-d H:i:s',getServerDate(false)), 
					'customer_notified' => '0',
					'comments' => 'Webhook status: '. $note);

					tep_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);
					}
				}
		error_log ($order_id." ".$note);
		header("HTTP/1.0 200 OK");
        header("Content-Length: 0");
        header("Connection: Close");
        exit();
	
	
				
	
}

    

########################### local function

    function table_exists($tablename, $database = false)
    {
        $CheckTable = tep_db_query("SHOW TABLES LIKE '" . $tablename . "'");
        if (tep_db_num_rows($CheckTable) > 0)
        {
            return true;
        }
        else
        {
            return false;
        }

    }
?>
