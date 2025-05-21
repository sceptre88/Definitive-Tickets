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
  if (!isset($process)) $process = false;
?>
<div class="container">
    <div class="row">
        <div class="col-md-6">
            <?php
            if (ACCOUNT_GENDER == 'true') {
                if (isset($gender)) {
                    $male = ($gender == 'm') ? true : false;
                    $female = ($gender == 'f') ? true : false;
                } else {
                    $male = ($addr['gender'] === 'm') ? true : false;
                    $female = ($addr['gender'] === 'f') ? true : false;
                }
            ?>
                <div class="row mb-3">
                    <div class="col-6">
                        <div class="main"><?php echo ENTRY_GENDER; ?></div>
                    </div>
                    <div class="col-6">
                        <div class="main">
                            <?php
                            echo tep_draw_radio_field('gender', 'm', $male) . '&nbsp;&nbsp;' . MALE . '&nbsp;&nbsp;' . tep_draw_radio_field('gender', 'f', $female) . '&nbsp;&nbsp;' . FEMALE . '&nbsp;' . (tep_not_null(ENTRY_GENDER_TEXT) ? '<span class="inputRequirement">' . ENTRY_GENDER_TEXT . '</span>' : '');
                            ?>
                        </div>
                    </div>
                </div>
<?php
            }
            ?>
                <div class="row mb-3">
                    <div class="col-6">
                        <div class="main"><?php echo ENTRY_FIRST_NAME . '&nbsp;' . (tep_not_null(ENTRY_FIRST_NAME_TEXT) ? '<span class="inputRequirement">' . ENTRY_FIRST_NAME_TEXT . '</span>' : ''); ?></div>
                    </div>
                    <div class="col-6">
                        <div class="main"><?php echo tep_draw_input_field('firstname', $addr['firstname']); ?></div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-6">
                        <div class="main"><?php echo ENTRY_LAST_NAME . '&nbsp;' . (tep_not_null(ENTRY_LAST_NAME_TEXT) ? '<span class="inputRequirement">' . ENTRY_LAST_NAME_TEXT . '</span>' : ''); ?></div>
                    </div>
                    <div class="col-6">
                        <div class="main"><?php echo tep_draw_input_field('lastname', $addr['lastname']); ?></div>
                    </div>
                </div>

                <?php
                if (ACCOUNT_CUSTOMER_EMAIL == 'true') {
                ?>
                    <div class="row mb-3">
                        <div class="col-6">
                            <div class="main"><?php echo ENTRY_CUSTOMER_EMAIL . '&nbsp;' . (tep_not_null(ENTRY_CUSTOMER_EMAIL_TEXT) ? '<span class="inputRequirement">' . ENTRY_CUSTOMER_EMAIL_TEXT . '</span>' : ''); ?></div>
                        </div>
                        <div class="col-6">
                            <div class="main"><?php echo tep_draw_input_field('customer_email', $addr['customer_email']); ?></div>
                        </div>
                    </div>
                <?php
                }
                ?>

            <?php
            if (ACCOUNT_COMPANY == 'true') {
            ?>
                <div class="row mb-3">
                    <div class="col-6">
                        <div class="main"><?php echo ENTRY_COMPANY . '&nbsp;' . (tep_not_null(ENTRY_COMPANY_TEXT) ? '<span class="inputRequirement">' . ENTRY_COMPANY_TEXT . '</span>' : ''); ?></div>
                    </div>
                    <div class="col-6">
                        <div class="main"><?php echo tep_draw_input_field('company', $addr['company']); ?></div>
                    </div>
                </div>
            <?php
            }
            ?>
            <?php
            if (ACCOUNT_ADDRESS == 'true') {
            ?>
                <div class="row mb-3">
                    <div class="col-6">
                        <div class="main"><?php echo ENTRY_STREET_ADDRESS . '&nbsp;' . (tep_not_null(ENTRY_STREET_ADDRESS_TEXT) ? '<span class="inputRequirement">' . ENTRY_STREET_ADDRESS_TEXT . '</span>' : ''); ?></div>
                    </div>
                    <div class="col-6">
                        <div class="main"><?php echo tep_draw_input_field('street_address', $addr['street_address']); ?></div>
                    </div>
                </div>
            <?php
            }
            ?>
            <?php
            if (ACCOUNT_SUBURB == 'true') {
            ?>
                <div class="row mb-3">
                    <div class="col-6">
                        <div class="main"><?php echo ENTRY_SUBURB . '&nbsp;' . (tep_not_null(ENTRY_SUBURB_TEXT) ? '<span class="inputRequirement">' . ENTRY_SUBURB_TEXT . '</span>' : ''); ?></div>
                    </div>
                    <div class="col-6">
                        <div class="main"><?php echo tep_draw_input_field('suburb', $addr['suburb']); ?></div>
                    </div>
                </div>
            <?php
            }
            ?>
            <?php
            if (ACCOUNT_CITY == 'true') {
            ?>
                <div class="row mb-3">
                    <div class="col-6">
                        <div class="main"><?php echo ENTRY_CITY . '&nbsp;' . (tep_not_null(ENTRY_CITY_TEXT) ? '<span class="inputRequirement">' . ENTRY_CITY_TEXT . '</span>' : ''); ?></div>
                    </div>
                    <div class="col-6">
                        <div class="main"><?php echo tep_draw_input_field('city', $addr['city']); ?></div>
                    </div>
                </div>
            <?php
            }
            ?>
            <?php
            if (ACCOUNT_POST_CODE == 'true') {
            ?>
                <div class="row mb-3">
                    <div class="col-6">
                        <div class="main"><?php echo ENTRY_POST_CODE . '&nbsp;' . (tep_not_null(ENTRY_POST_CODE_TEXT) ? '<span class="inputRequirement">' . ENTRY_POST_CODE_TEXT . '</span>' : ''); ?></div>
                    </div>
                    <div class="col-6">
                        <div class="main"><?php echo tep_draw_input_field('postcode', $addr['postcode']); ?></div>
                    </div>
                </div>
            <?php
            }
            ?>
            <?php
            if (ACCOUNT_TELEPHONE == 'true') {
            ?>
                <div class="row mb-3">
                    <div class="col-6">
                        <div class="main"><?php echo ENTRY_TELEPHONE_NUMBER . '&nbsp;' . (tep_not_null(ENTRY_TELEPHONE_NUMBER_TEXT) ? '<span class="inputRequirement">' . ENTRY_TELEPHONE_NUMBER_TEXT . '</span>' : ''); ?></div>
                    </div>
                    <div class="col-6">
                        <div class="main"><?php echo tep_draw_input_field('customer_phone', $addr['customer_phone']); ?></div>
                    </div>
                </div>
            <?php
            }
            ?>
            <div class="row mb-3">
                <div class="col-4">
                    <div class="main"><?php echo ENTRY_COUNTRY . '&nbsp;' . (tep_not_null(ENTRY_COUNTRY_TEXT) ? '<span class="inputRequirement">' . ENTRY_COUNTRY_TEXT . '</span>' : ''); ?></div>
                </div>
                <div class="col-8">
                    <div class="main" nowrap>
                        <?php echo tep_get_country_list('country', $addr['country_id'], 'id="country" onchange="javascript:show_state();" style="width:180px"'); ?>
                    </div>
                </div>
            </div>
            <?php
            if (ACCOUNT_STATE == 'true') {
            ?>
                <div class="row mb-3">
                    <div class="col-6">
                        <div class="main"><?php echo ENTRY_STATE . '<span class="inputRequirement">' . ENTRY_COUNTRY_TEXT . '</span>'; ?></div>
                    </div>
                    <div class="col-6">
                        <div class="main">
                            <?php
                            echo tep_draw_input_field('state1', tep_get_zone_name($addr['country_id'], $addr['zone_id'], $addr['state']), 'id="state1"');
                            echo tep_draw_pull_down_menu('state', array(), '' . $addr['state'] . '', 'id="state" style="display:none; width:180px"');
                            echo $addr['state'];
                            ?>
                        </div>
                    </div>
                </div>
            <?php
            }
            ?>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
  // we call the function to populate the state field
  show_state();
});
</script>