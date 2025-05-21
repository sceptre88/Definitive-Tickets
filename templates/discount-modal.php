<?php 

// Check to ensure this file is included in osConcert!
defined('_FEXEC') or die();
?>
<?php
if(DISCOUNT_POPUP=='true')
{
?>
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
<div style="background-color:transparent !important">
    <div class="vertical-alignment-helper">
        <div class="modal-dialog vertical-align-center">
            <div class="modal-content" id="ticket_discount">
                <div class="modal-body">
					<h5 class="modal-title" id="myModalLabel"><?php echo TEXT_DISCOUNT_AVAILABLE_POPUP;?></h5>
					<strong><span id="discount_show_name">&nbsp;</span>
					<span id="discount_products_name">&nbsp;</span></strong>
					<div class="clearfloat"></div>
					<ul id="discount" class="list-unstyled">
					<li></li>
					</ul>
					<div class="clearfloat"></div>
					<ul id="discount_choice_text_holder" class="list-unstyled">
					<li id="discount_choice_text"></li>
					</ul>
                </div>
            </div>
        </div>
    </div>
</div>
</div><?php } ?>