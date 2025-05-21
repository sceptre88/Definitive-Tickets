<?php
/*
osConcert Visual Seat Reservation Copyright (c) 2009-2024 https://www.osconcert.com
Bootstrap v5.3 2023
*/
defined('_FEXEC') or die();
define('DIR_WS_TEMPLATE_IMAGES', 'templates/darkzone/images/');
$template_file_name=$content;$con_page = $content;
require(DIR_WS_INCLUDES.'meta_tags.php');
require(DIR_WS_CLASSES.'seatplan.php');
$sp = new seatplan; 
require_once(DIR_WS_CLASSES.'ajax_cart.php');
$ajaxCart = new ajaxCart;
$template_query = tep_db_query("select include_column_left,include_column_right from " . TABLE_TEMPLATE . "  WHERE template_name='darkzone'");
while ($template_values = tep_db_fetch_array($template_query)) 
	{
		if (($template_values['include_column_left']=='yes')or($template_values['include_column_right']=='yes'))
		{
			$has_column='left_column/';
		}
	}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" <?php echo HTML_PARAMS; ?>>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET;?>">
	<meta name="robots" content="nofollow">
	<base href="<?php echo (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER).DIR_WS_CATALOG;?>">
	<title><?php echo STORE_NAME; ?> | <?php echo META_TAG_TITLE; ?></title>
	<meta name="description" content="<?php echo META_TAG_DESCRIPTION;?>">
	<meta name="keywords" content="<?php echo META_TAG_KEYWORDS;?>">
	<meta property="fb:app_id" content="<?php echo FB_APP_ID; ?>">
	<meta property="og:site_name" content="<?php echo FB_SITE_NAME; ?>">
	<meta property="og:url" content="<?php echo FB_URL; ?>">
	<meta property="og:type" content="<?php echo FB_TYPE; ?>">
	<meta property="og:locale" content="<?php echo FB_LOCALE; ?>">
	<meta property="og:title" content="<?php echo FB_TITLE; ?>">
	<meta property="og:description" content="<?php echo FB_DESCRIPTION; ?>">
	<meta property="og:image" content="<?php echo FB_IMAGE; ?>">
	<meta name="viewport" content="width=device-width">
	<meta content="width=device-width, initial-scale=1.0" name="viewport">
	<?php if(file_exists(DIR_WS_INCLUDES. 'header_tags.php')) require(DIR_WS_INCLUDES. 'header_tags.php');?>
	<!-- Favicons -->
	<link rel="icon" type="image/ico" href="favicon.ico">
	<!-- Libraries CSS Files -->
	
	  <!-- Vendor CSS Files -->
  <link href="<?php echo DIR_WS_TEMPLATES . TEMPLATE_NAME; ?>/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?php echo DIR_WS_TEMPLATES . TEMPLATE_NAME; ?>/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="<?php echo DIR_WS_TEMPLATES . TEMPLATE_NAME; ?>/assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="<?php echo DIR_WS_TEMPLATES . TEMPLATE_NAME; ?>/assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="<?php echo DIR_WS_TEMPLATES . TEMPLATE_NAME; ?>/assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

	<link href="<?php echo DIR_WS_TEMPLATES . TEMPLATE_NAME; ?>/assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">

	<link href="<?php echo DIR_WS_TEMPLATES . TEMPLATE_NAME; ?>/assets/vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet">
	<?php
	if (SHOW_LANGUAGES_IN_HEADER == 'yes') 
		{
	echo '<link href="templates/darkzone/flag-icon.css" rel="stylesheet">';
		}
		?>
	<!-- osConcert CSS Files -->
	<link href="<?php	
	$css='seatplan'.$manufacturers_id; //manufacturers_id is Design ID ==5
	//$css="seatplan_lc"; 
	echo DIR_WS_TEMPLATES . TEMPLATE_NAME.'/assets/css/'.$has_column.$css.'.css';?>" rel="stylesheet">
	<link href="<?php echo DIR_WS_TEMPLATES . TEMPLATE_NAME; ?>/assets/css/jquery-ui.css" rel="stylesheet">
	<!-- Template Stylesheet Files -->
	<link href="<?php echo DIR_WS_TEMPLATES . TEMPLATE_NAME; ?>/assets/css/variables.css" rel="stylesheet">
	<link href="<?php echo DIR_WS_TEMPLATES . TEMPLATE_NAME; ?>/assets/css/main.css" rel="stylesheet"> 
	<link href="<?php echo DIR_WS_TEMPLATES . TEMPLATE_NAME; ?>/assets/css/custom.css" rel="stylesheet"> 
	<link href="<?php echo DIR_WS_TEMPLATES.TEMPLATE_NAME.'/assets/css/themes/variables-'.TEMPLATE_COLOR.'.css';?>" rel="stylesheet">
	<style>.main-content {padding-top: <?php echo HEADER_HEIGHT+20; ?>px;}.header .logo img{max-height: <?php 
	echo HEADER_HEIGHT-20; ?>px;}</style>
	<?php 
	define('LOG_PAGE','<a href="%s"><span>%s</span></a>');
	if($javascript) require(DIR_WS_JAVASCRIPT.$javascript); if(($FSESSION->is_registered('customer_first_name') && $FSESSION->is_registered('customer_id'))){ $user = tep_output_string_protected($FSESSION->customer_first_name); $url = tep_href_link(FILENAME_ACCOUNT,'','SSL'); define('USER', '<a href="%1$s">%2$s</a>'); $clink = sprintf(USER,$url,$user);define('USERLINK',$clink); $account = sprintf(WELCOME_CUSTOMER,tep_href_link(FILENAME_ACCOUNT, '', 'SSL')); $greet = sprintf(TEXT_GREETING_PERSONAL,USERLINK,'',''); $log_page = sprintf(LOG_PAGE,tep_href_link(FILENAME_LOGOFF),TEXT_LOGOFF);}else{ $greet = sprintf(WELCOME_GUEST, tep_href_link(FILENAME_LOGIN, '', 'SSL')); $log_page = sprintf(LOG_PAGE,tep_href_link(FILENAME_LOGIN.'','','SSL'),TEXT_LOGIN);} ?>
	<?php if(COOKIEHUB_KEY !=''){ ?>
	<script src="https://cookiehub.net/c2/<?php echo COOKIEHUB_KEY; ?>.js"></script>
	<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", function(event) {
	var cpm = {};
	window.cookiehub.load(cpm);
	});
	</script><?php } ?>
</head>
<body id="body" data-bs-theme="light">
<?php 
echo '<div id="warning-container" style="position: relative; top: '.HEADER_HEIGHT.'px;">';
require(DIR_WS_INCLUDES . 'warnings.php');
echo '</div>';
 ?>	
<?php require(DIR_FS_CATALOG.DIR_WS_TEMPLATES.TEMPLATE_NAME. '/header.php'); ?>
<?php 
	//<!-- errors -->
	if ($FREQUEST->getvalue('error_message')) 
	{ 
	?>
	<div class="alert-warning" style="text-align:center"></div>
	<?php 
	} 
	//<!-- end errors -->
	?>
	
<main id="main">
<?php require(DIR_FS_CATALOG.DIR_WS_TEMPLATES.TEMPLATE_NAME. '/main.php'); ?>
</main>

<?php
if(SHOW_FOOTER=='yes'){ 
require(DIR_FS_CATALOG.DIR_WS_TEMPLATES.TEMPLATE_NAME. '/footer.php'); 
}else{
require(DIR_FS_CATALOG.DIR_WS_TEMPLATES.TEMPLATE_NAME. '/def-footer.php'); 
}
?>  
<?php if(COOKIEHUB_KEY ==''){ ?>
	<!-- START Bootstrap-Cookie-Alert -->
	<div class="alert text-center cookiealert" role="alert">
		<?php echo TEXT_COOKIES ?>
		<button type="button" class="btn btn-primary btn-sm acceptcookies" aria-label="<?php echo TEXT_CLOSE ?>">
		<?php echo TEXT_UNDERSTAND ?>
		</button>
	</div>
	<!-- END Bootstrap-Cookie-Alert -->
<?php } ?>
	<!--<div id="preloader"></div>-->
	<a href="<?php echo $_SERVER['REQUEST_URI'];?>#body" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
	 
	<!-- Vendor JS Files -->
	<script src="<?php echo DIR_WS_TEMPLATES . TEMPLATE_NAME; ?>/assets/vendor/jquery/jquery.min.js"></script>
	<script src="<?php echo DIR_WS_TEMPLATES . TEMPLATE_NAME; ?>/assets/vendor/aos/aos.js"></script>
	<script src="<?php echo DIR_WS_TEMPLATES . TEMPLATE_NAME; ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
	<script>
	var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
	var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
	  return new bootstrap.Tooltip(tooltipTriggerEl)
	})
	</script>
	<script src="<?php echo DIR_WS_TEMPLATES . TEMPLATE_NAME; ?>/assets/vendor/jquery_ui/jquery-ui.min.js"></script>
	<script src="<?php echo DIR_WS_TEMPLATES . TEMPLATE_NAME; ?>/assets/vendor/glightbox/js/glightbox.min.js"></script>
	<script src="<?php echo DIR_WS_TEMPLATES . TEMPLATE_NAME; ?>/assets/vendor/swiper/swiper-bundle.min.js"></script>
	<script defer src="<?php echo DIR_WS_TEMPLATES . TEMPLATE_NAME; ?>/assets/js/cookiealert.min.js"></script>
	<script defer src="<?php echo DIR_WS_TEMPLATES . TEMPLATE_NAME; ?>/assets/js/easytooltip.min.js"></script>
	<script src="<?php echo DIR_WS_TEMPLATES . TEMPLATE_NAME; ?>/assets/js/main.js"></script>
	<?php
	if (!empty(CLICKY_KEY)) {
		echo '<script async data-id="' . CLICKY_KEY . '" src="//static.getclicky.com/js"></script>';
	}
	?>		
	<?php ################# server load
	if(SEATPLAN_LOGIN_ENFORCED=='true' && !$FSESSION->is_registered('customer_id'))
	{
	?>
	<?php if(file_exists(DIR_WS_TEMPLATES.'login-modal.php')) require(DIR_WS_TEMPLATES.'login-modal.php');?>
	<script>
	$(document).ready(function(){
		$(".seatplan").click(function(){
			$("html, body").animate({ scrollTop: 0 }, "slow");
			$("#LoginModal").modal("show");
		});
	});
	</script>
	<?php 
	}
	{
	$js='';/* checking for the age of the cart in order to initialize the timeout */ if($_SESSION['customer_id']) { $cart_age = $sp->tep_getCartAge($_SESSION['customer_id']); }else { $cart_age = $sp->tep_getTempCartAge(); }/* check if the cart isn't already expired yet */ if($cart_age > (int)SEATPLAN_TIMEOUT) { if($_SESSION['customer_id']){ $sp->tep_clearCart($_SESSION['customer_id'],$cPath); }else{/* if not logged in */ $sp->tep_clearTempCart($cPath); } }else {/* pass the remaining seconds as a jQuery variable */ if($cart_age != -1){ $remaining = ((int)SEATPLAN_TIMEOUT)-$cart_age; $timeout = 'true';}else{ $remaining=(int)SEATPLAN_TIMEOUT; $timeout = 'false';} if(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) { $baseurl = HTTPS_SERVER.DIR_WS_HTTPS_CATALOG; }else{$baseurl = HTTP_SERVER.DIR_WS_HTTP_CATALOG;} $js.='<script>$.cPath='.(($cPath)? $cPath:0).';$.cht="'.$sp->cat_name($cPath).'";$.refresh='.SEATPLAN_REFRESH.';$.lifetime='.SEATPLAN_TIMEOUT.';$.remaining='.$remaining.';$.timeout='.$timeout.';$.baseurl="'.$baseurl.'";$.thank="'.SP_NOTHANKYOU.'";</script>'."\n"; } $sym_left = $currencies->currencies[$currency=$FSESSION->currency]['symbol_left']; $sym_right = $currencies->currencies[$currency=$FSESSION->currency]['symbol_right']; $dec_point = $currencies->currencies[$currency=$FSESSION->currency]['decimal_point']; $thou_point = $currencies->currencies[$currency=$FSESSION->currency]['thousands_point']; $js.='<script>var lng={"expiry":"'.SP_EXPIRY.'","expired":"'.SP_EXPIRED.'","cleared":"'.SP_CLEARED.'","tooslow":"'.SP_TOOSLOW.'","toomany":"'.SP_TOOMANY.'","discount":"'.SP_DISCOUNT.'","seat":"'.ITEM.'","seats":"'.ITEMS.'","thou_point":"'.$thou_point.'","dec_point":"'.$dec_point.'","sym_left":"'.$sym_left.'","sym_right":"'.$sym_right.'"};</script>'."\n"; if($FSESSION->is_registered('customer_id')){$spt='user.min';}else{if(SEATPLAN_LOGIN_ENFORCED=='true'){$spt='public';}else{$spt='user.min';}} 
	if($_SESSION['customer_country_id']==999 && isset($_SESSION['box_office_refund'] )){$spt='refund.min';} 
	if($_SESSION['customer_country_id']==999 && isset($_SESSION['draggable'] )){$spt='public';} 
	$js.='<script src="'.DIR_WS_TEMPLATES.TEMPLATE_NAME.'/assets/js/seatplan_'.$spt.'.js"></script>'."\n"; echo $js;}
	?>
	<?php if(file_exists(DIR_WS_TEMPLATES.'discount-modal.php')) require(DIR_WS_TEMPLATES.'discount-modal.php');?>