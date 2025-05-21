<?php
/*

Released under the GNU General Public License
*/

// Check to ensure this file is included in osConcert!
defined('_FEXEC') or die();
?>
<section id="howto">
<div class="howto d-flex align-items-center">
<ul class="list-group list-group-horizontal">
<li class="list-group-item"><?php echo HOW_TO_RESERVE; ?></li>
<li class="list-group-item"><?php echo ONE; ?></li>
<li class="list-group-item"><?php echo TWO; ?></li>
<li class="list-group-item"><?php echo THREE; ?></li>
<?php
	if (PURCHASE_NO_ACCOUNT =='no'){
		?>
<li class="list-group-item"><?php echo FOUR; ?></li>
<?php } ?>
</ul></div></section>