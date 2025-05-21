<?php 
// Check if this file is included in osConcert
defined('_FEXEC') or die();

/*
    AJAX Multiuser Seat Reservations
    for osConcert, Online Seat Booking
    by Martin Zeitler, Germany
*/

// Define sign up style
$sign_up_style = ($cart->count_contents() > 0) ? 'style="padding-top:12px;display:true;z-index:100;"' : 'style="display:none;"';

?>
<ul class="list-group list-group-horizontal">
    <li class="list-group-item" style="border: none"><strong><?php echo TEXT_LEGEND; ?></strong></li>
    <?php echo $sp->tep_renderPricesBar($cPath, $category['categories_name']); ?>
</ul>

<ul class="list-group list-group-horizontal pull-right">
    <?php if($_SESSION['customer_id']): ?>
        <li class="list-group-item" style="border: none"><span class="plan_own cube"></span>&nbsp;<?php echo TEXT_YOUR_RESERVATIONS; ?></li>
        <li class="list-group-item" style="border: none"><span class="plan_reserved cube"></span>&nbsp;<?php echo TEXT_FOREIGN_RESERVATIONS; ?></li>
    <?php else: ?>
        <li class="list-group-item" style="border: none"><span class="plan_reserved cube"></span>&nbsp;<?php echo TEXT_RESERVED_SEATS; ?></li>
    <?php endif; ?>
    <li class="list-group-item" style="border: none"><span class="plan_incart cube"></span>&nbsp;<?php echo TEXT_IN_BASKET; ?></li>
    <li class="list-group-item" style="border: none"><span class="plan_locked cube"></span>&nbsp;<?php echo TEXT_OTHER_BASKETS; ?></li>
    <li class="list-group-item" style="border: none;width:60px;"><div id="indicator"></div></li>
</ul>

<br class="clearfloat">

<?php if($parent_id > 0): ?>
    <div style="text-align:center;display:true;"><h3><?php echo $category['categories_name']; ?></h3></div>
<?php endif; ?>

<?php if (isset($_SESSION['box_office_refund'])): ?>
    <?php require_once(DIR_WS_MODULES  . 'triple_boxes.php'); ?>
<?php else: ?>
    <?php if (BOX_WIDTH_LEFT == 'yes' && HOW_TO_GUIDE == 'true'): ?>
        <?php require_once(DIR_WS_TEMPLATES.TEMPLATE_NAME.'/content/howto.php'); ?>
    <?php endif; ?>
    <div id="btnCheckOut" class="fixed-bottom" <?php echo $sign_up_style; ?>>
        <h2>
            <a class="checkout" href="<?php echo tep_href_link('shopping_cart.php'); ?>">
                <?php echo sprintf(MODULE_NAVBAR_SHOPPING_CART_HAS_CONTENTS, $cart->count_contents_gt(), $currencies->format($cart->show_total())); ?>
            </a>
            <a href="<?php echo tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'); ?>">
                <?php echo tep_template_image_button_checkout('', IMAGE_BUTTON_CHECKOUT); ?>
            </a>
        </h2>
    </div>
<?php endif; ?>

<?php if(HAS_STAGE == 'true' && $plan_id < 5): ?>
    <div id="stage_label"><?php echo STAGE; ?></div>
<?php endif; ?>

<?php
// Check caching
if(SEAT_PLAN_CACHE == 'true') {
    $caching = true;
}

$expiry = 240 * 60; // Cache expiry: 240 minutes
$timeout = true;

// Handle different scenarios
if (isset($_SESSION['box_office_refund']) && $_SESSION['box_office_refund'] == 'yes') {
    if(DESIGN_MODE == 'yes') {
        echo $sp->tep_renderSeatplanCSS($cPath);
        echo $sp->tep_renderSeatplanRefundDesign($cPath);
    } else {
        echo $sp->tep_renderSeatplanRefund($cPath);
    }
} elseif (isset($_SESSION['box_office_reservation']) && $_SESSION['box_office_reservation'] == 'yes') {
    echo $sp->tep_renderSeatplanReservation($cPath);
} else {
    if ($caching) {
        $path = DIR_FS_CATALOG.'/cache/';
        $cache = $path.'section'.$cPath.'.html';

        if(!file_exists($path)) {
            mkdir($path, 0755);
        }

        if (file_exists($cache) && (time() - $expiry < filemtime($cache))) {
            echo "\n<!-- Cached on ".date('jS F Y @ H:i', filemtime($cache))." -->\n";
            include($cache);
        } else {
            if(file_exists($cache)) {
                unlink($cache);
            }

            ob_start();
            if ((DESIGN_MODE == 'yes') && ($manufacturers_id > 4 && $manufacturers_id < 9)) {
                echo $sp->tep_renderSeatplanCSS($cPath);
                echo $sp->tep_renderSeatplanDesign($cPath);
            } else {
                if ($plan_id < 5) {
                    echo $sp->tep_renderSeatplan($cPath);
                }
            }
            $fp = fopen($cache,'w');
            fwrite($fp,ob_get_contents());
            fclose($fp);
            ob_end_flush();
        }
    } else {
        echo "\n<!-- Rendered on ".date('jS F Y @ H:i', time())." -->\n";
        if ((DESIGN_MODE == 'yes') && ($manufacturers_id > 4 && $manufacturers_id < 9)) {
            echo $sp->tep_renderSeatplanCSS($cPath);
            echo $sp->tep_renderSeatplanDesign($cPath);
        } else {
            if ($plan_id < 5) {
                echo $sp->tep_renderSeatplan($cPath);
            }
        }
    }
}

// Display how to guide at bottom
if (BOX_WIDTH_LEFT == 'no' && HOW_TO_GUIDE == 'true') {
    require_once(DIR_WS_TEMPLATES.TEMPLATE_NAME.'/content/howto.php');
}

// Enforce login if necessary
if (SEATPLAN_LOGIN_ENFORCED == 'true' && !$FSESSION->is_registered('customer_id')) {
    require_once(DIR_WS_INCLUDES.'login_enforced.php');
}
//include(DIR_FS_CATALOG.DIR_WS_MODULES. '/double_boxes.php');
?>

