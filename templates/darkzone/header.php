<?php 

defined('_FEXEC') or die();
$arrows = "&raquo;";

if (DOWN_FOR_MAINTENANCE == 'true')
	{
	$maintenance_on_at_time_raw = tep_db_query("select last_modified from " . TABLE_CONFIGURATION . " WHERE configuration_key = 'DOWN_FOR_MAINTENANCE'");
	$maintenance_on_at_time = tep_db_fetch_array($maintenance_on_at_time_raw);
	define('TEXT_DATE_TIME', $maintenance_on_at_time['last_modified']);
	}
	
	if(DOWN_FOR_MAINTENANCE_HEADER_OFF == 'false')
	{
		if (SHOW_TOP_BAR == 'yes') 
		{
		//require_once(DIR_WS_TEMPLATES.TEMPLATE_NAME.'/content/topbar.php');
		require_once(DIR_WS_TEMPLATES.TEMPLATE_NAME.'/content/header.php');
		} 

		if ((substr(basename($PHP_SELF), 0, 5) != 'index') or ($cPath>0) or ($stcPath>0))
		{ 
		?>
	<!-- ======= Header ======= -->
    <header id="header" class="header fixed-top" data-scrollto-offset="0" style="height:<?php 
	echo HEADER_HEIGHT; ?>px">
    <div class="container d-flex align-items-center justify-content-between">

    <?php if(TEXT_LOGO=='yes'){	?>
	<a href="index.php" class="logo d-flex align-items-center scrollto me-auto me-lg-0">
    <h1><?php echo TEXT_LOGO1; ?><span><?php echo TEXT_LOGO2; ?></span></h1>
    </a>
	<?php //use an image
	}else{ $url = '<a class="logo" href="index.php">' . tep_image(DIR_WS_TEMPLATES . DEFAULT_TEMPLATE . '/' . DIR_WS_IMAGES . COMPANY_LOGO, STORE_NAME,HEADING_IMAGE_WIDTH,HEADING_IMAGE_HEIGHT) . '</a>';///STORE_NAME, '200', '60') ////
	echo $url;
	} ?>
<?php
	if (SHOW_LANGUAGES_IN_HEADER == 'yes') 
		{
			//language selector
			if (!is_object($lng)) 
			{
			include(DIR_WS_CLASSES . 'language.php');
			$lng = new language;
			}
			
			if (getenv('HTTPS') == 'on') $connection = 'SSL';
			else $connection = 'NONSSL';
			
			$languages_string = '';
			reset($lng->catalog_languages);
			foreach($lng->catalog_languages as $key => $value)
			{
				$languages_string .= '
				<li><a class="badge-pill rounded-pill bg-light" 
				data-toggle="tooltip" 
				data-placement="bottom" 
				title="'. $value['name'] .'" 
				href="' . tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('language', 'currency')) . 'language=' . $key, $connection) . '">
				<i class="flag-icon flag-icon-' . $value['code'] . '"></i>
				</a></li>
				';
			}
			echo $languages_string;
		}
		?>
    <nav id="navbar" class="navbar">
    <ul>
	<?php
	if (SHOW_MAIN_FEATURED_CATEGORIES == 'true')
	{
	echo '<li><a class="nav-link events" href="' . tep_href_link(FILENAME_FEATURED_CATEGORIES) . '">' . HEADING_FEATURED_CATEGORIES . '</a></li>' . "\n";
	} 
	if (HIDE_SEARCH_EVENTS == 'no')
	{
	echo '<li><a class="nav-link" href="' . tep_href_link(FILENAME_SEARCH_EVENTS) . '">' . HEADER_TITLE_SEARCH . '</a></li>' . "\n";
	} 

	$looking_for = 'bor.php'; // is Box Office Reservations installed?
	
	if (defined('MODULE_PAYMENT_INSTALLED') && tep_not_null(MODULE_PAYMENT_INSTALLED) && $_SESSION['customer_country_id']==999) 
	{
	$modules_installed = explode(';', MODULE_PAYMENT_INSTALLED);
		if (in_array($looking_for, $modules_installed))
		{
		echo "<li><a class=\"nav-link\" href=\"" . tep_href_link('bor_listings.php') . "\">" . HEADER_VIEW_BOR . '</a></li>' . "\n";
		}
	}
	echo "<li><a class=\"nav-link\" href=\"" . tep_href_link(FILENAME_SHOPPING_CART) . "\">" . HEADER_TITLE_CART_CONTENTS . '</a></li>' . "\n"; 
	
	echo "<li class=\"menu-active\"><a class=\"nav-link\" href=\"" . tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL') . "\">" . HEADER_TITLE_CHECKOUT . '</a></li>' . "\n"; ?>
	<?php
	if (!isset($_COOKIE['customer_is_guest']))
	{ //PWA 

	echo "<li>".$log_page."</li>"; 
	} 
	?>
	<!-- header shopping cart -->
	<?php //let's find out if there is a left column shopping cart?	
	if (SHOW_CART_IN_HEADER == 'yes') 
	{
		if (!isset($_SESSION['box_office_refund'])) 
		{
			$shopping_cart_query = tep_db_query('select  infobox_display, infobox_file_name from ' . TABLE_INFOBOX_CONFIGURATION . ' where template_id = ' . tep_db_input(TEMPLATE_ID));

				while ($shopping_cart = tep_db_fetch_array($shopping_cart_query)) 
				{
					if (($shopping_cart['infobox_display'] == 'no')&&($shopping_cart['infobox_file_name'] == 'shopping_cart.php')) 
					{
					require_once (DIR_WS_TEMPLATES.TEMPLATE_NAME.'/shopping_cart_new.php');
					}
				}
		}
	}
	?>
	</ul>
    <i class="bi bi-list mobile-nav-toggle d-none"></i>
    </nav><!-- .navbar -->

    </div>
    </header><!-- End Header -->	
	<?php 
	}else{
	?>
	<!-- ======= Header ======= -->
    <header id="header" class="header fixed-top" data-scrollto-offset="0" style="height:<?php 
	echo HEADER_HEIGHT; ?>px">
    <div class="container d-flex align-items-center justify-content-between">

	<?php if(TEXT_LOGO=='yes'){	?>
	<a href="index.php" class="logo d-flex align-items-center scrollto me-auto me-lg-0">
    <h1><?php echo TEXT_LOGO1; ?><span><?php echo TEXT_LOGO2; ?></span></h1>
    </a>
	<?php //use an image
	}else{ $url = '<a class="logo" href="index.php">' . tep_image(DIR_WS_TEMPLATES . DEFAULT_TEMPLATE . '/' . DIR_WS_IMAGES . COMPANY_LOGO, STORE_NAME,HEADING_IMAGE_WIDTH,HEADING_IMAGE_HEIGHT) . '</a>';///STORE_NAME, '200', '60') ////
	echo $url;
	} ?>
<?php
	if (SHOW_LANGUAGES_IN_HEADER == 'yes') 
		{
			//language selector
			if (!is_object($lng)) 
			{
			include(DIR_WS_CLASSES . 'language.php');
			$lng = new language;
			}
			
			if (getenv('HTTPS') == 'on') $connection = 'SSL';
			else $connection = 'NONSSL';
			
			$languages_string = '';
			reset($lng->catalog_languages);
			foreach($lng->catalog_languages as $key => $value)
			{
				$languages_string .= '
				<a class="badge-pill rounded-pill bg-light" 
				data-toggle="tooltip" 
				data-placement="bottom" 
				title="'. $value['name'] .'" 
				href="' . tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('language', 'currency')) . 'language=' . $key, $connection) . '">
				<i class="flag-icon flag-icon-' . $value['code'] . '"></i>
				</a>
				';
			}
			echo $languages_string;
		}
		?>
    <nav id="navbar" class="navbar">
    <ul>
	<?php
	if (SHOW_MAIN_FEATURED_CATEGORIES == 'true')
	{
	echo '<li><a class="nav-link" href="' . tep_href_link(FILENAME_FEATURED_CATEGORIES) . '">' . HEADING_FEATURED_CATEGORIES . '</a></li>' . "\n";
	} 
	if (HIDE_SEARCH_EVENTS == 'no')
	{
	echo '<li><a class="nav-link" href="' . tep_href_link(FILENAME_SEARCH_EVENTS) . '">' . HEADER_TITLE_SEARCH . '</a></li>' . "\n";
	} 

	$looking_for = 'bor.php'; // is Box Office Reservations installed?
	
	if (defined('MODULE_PAYMENT_INSTALLED') && tep_not_null(MODULE_PAYMENT_INSTALLED) && $_SESSION['customer_country_id']==999) 
	{
	$modules_installed = explode(';', MODULE_PAYMENT_INSTALLED);
		if (in_array($looking_for, $modules_installed))
		{
		echo "<li><a class=\"nav-link\" href=\"" . tep_href_link('bor_listings.php') . "\">" . HEADER_VIEW_BOR . '</a></li>' . "\n";
		}
	}
	echo "<li><a class=\"nav-link\" href=\"" . tep_href_link(FILENAME_SHOPPING_CART) . "\">" . HEADER_TITLE_CART_CONTENTS . '</a></li>' . "\n"; ?>
	
	<?php
	echo "<li class=\"menu-active\"><a class=\"nav-link\" href=\"" . tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL') . "\">" . HEADER_TITLE_CHECKOUT . '</a></li>' . "\n"; 
	
	if (!isset($_COOKIE['customer_is_guest']))
	{ //PWA 

	echo "<li>".$log_page."</li>"; 
	} 
	?>
	<!-- header shopping cart -->
	<?php //let's find out if there is a left column shopping cart?	
	if (SHOW_CART_IN_HEADER == 'yes') 
	{
		if (!isset($_SESSION['box_office_refund'])) 
		{
			$shopping_cart_query = tep_db_query('select  infobox_display, infobox_file_name from ' . TABLE_INFOBOX_CONFIGURATION . ' where template_id = ' . tep_db_input(TEMPLATE_ID));

				while ($shopping_cart = tep_db_fetch_array($shopping_cart_query)) 
				{
					if (($shopping_cart['infobox_display'] == 'no')&&($shopping_cart['infobox_file_name'] == 'shopping_cart.php')) 
					{
					require_once (DIR_WS_TEMPLATES.TEMPLATE_NAME.'/shopping_cart_new.php');
					}
				}
		}
	}
	?>
	</ul>
        <i class="bi bi-list mobile-nav-toggle d-none"></i>
      </nav><!-- .navbar -->
    </div>
    </header><!-- End Header -->

	<?php
		if (SHOW_HEADER_PANE == 'yes') 
		{
		//require_once(DIR_WS_TEMPLATES.TEMPLATE_NAME.'/content/static-hero.php');
		require_once(DIR_FS_CATALOG.DIR_WS_TEMPLATES.TEMPLATE_NAME. '/content/fullscreen-hero.php');
		//require_once(DIR_FS_CATALOG.DIR_WS_TEMPLATES.TEMPLATE_NAME. '/content/carousel.php');
		//require_once(DIR_WS_TEMPLATES.TEMPLATE_NAME.'/content/header.php');
		} 
		else
		{
		//echo '<br class="clearfloat">';
		//require_once(DIR_WS_TEMPLATES.TEMPLATE_NAME.'/content/header.php');
		}
	}
} 
?>