<?php

 // Check to ensure this file is included in osConcert!
defined('_FEXEC') or die(); 

//red,yellow,green,blue,fuchsia,thistle,orange,teal,salmon,palegreen,skyblue

define('TEXT_IMPORTANT','Provide products (seats) for reservation<br>Quantity=1 :: Status=1 :: Fix=1 (can be reset) Color code (red,yellow,green,blue,fuchsia,thistle,orange,teal,salmon,palegreen, skyblue)<br>Disable products: <br>Quantity=0 :: Status=0 (Reserved) <br>Status=3 (Hidden for social distancing) <br>Status=8 (Hidden) :: Fixed=0 (not reset)');

define('HEADING_TITLE', 'Products Data');
define('NO_DATA', 'No Data: Table "products" does not exist!');

?>
