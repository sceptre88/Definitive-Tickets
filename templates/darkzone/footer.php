<?php
/*
	osConcert Seat Booking Sofware
	Copyright (c) 2007-2021 https://www.osconcert.com

Released under the GNU General Public License
*/

// Check to ensure this file is included in osConcert!
defined('_FEXEC') or die();

// Hide footer.php if not to show in maintenance mode
if (DOWN_FOR_MAINTENANCE_FOOTER_OFF =='false') 
{
?>
<?php
if(HEADER_BANNER=='yes'){ 
require(DIR_FS_CATALOG.DIR_WS_TEMPLATES.TEMPLATE_NAME. '/content/sponsors.php'); 
}
?> 
	<!-- ======= Footer ======= -->
  <footer id="footer" class="footer">

    <div class="footer-content">
      <div class="container">
        <div class="row">

          <div class="col-lg-3 col-md-6">
            <div class="footer-links">
            <h3><?php echo STORE_NAME; ?></h3>
			<img src="<?php echo DIR_WS_TEMPLATES . TEMPLATE_NAME; ?>/images/theatre2.png" alt="<?php echo HEADER_TEXT_HOME; ?>">
			<div class="footer-links">
			<ul>
              <li><strong><?php echo FOOTER_TEXT_EMAIL; ?></strong>&nbsp;&nbsp;<a href="mailto:<?php echo STORE_OWNER_EMAIL_ADDRESS; ?>"><?php echo STORE_OWNER_EMAIL_ADDRESS; ?></a></li>
			  <li><?php if (STORE_OWNER_TELEPHONE!=''){ ?>
	<i class="bi bi-phone d-flex align-items-center ms-4"><?php echo STORE_OWNER_TELEPHONE; ?></i><?php } ?></li>
			  </ul>
			</div>
            </div>
          </div>

          <div class="col-lg-3 col-md-6 footer-links">
            <!--<h4></h4>
            <ul>
              <li><i class="bi bi-chevron-right"></i> <?php //echo '<a href=""></a>';?></li>
              <li><i class="bi bi-chevron-right"></i> <?php //echo '<a href=""></a>';?></li>
			  <li><i class="bi bi-chevron-right"></i> <?php //echo '<a href=""></a>';?></li>
			  <li><i class="bi bi-chevron-right"></i> <?php //echo '<a href=""></a>';?></li>
			</ul>-->
          </div>

          <div class="col-lg-2 col-md-6 footer-links">
            <h4><?php echo TEXT_FOOTER_LINKS; ?></h4>
            <ul>
              <li><i class="bi bi-chevron-right"></i> <?php echo '<a href="'.tep_href_link(FILENAME_DEFAULT,'stcPath=2').'">'.BOX_INFORMATION_PRIVACY.'</a>';?></li>
              <li><i class="bi bi-chevron-right"></i> <?php echo '<a href="'.tep_href_link(FILENAME_DEFAULT,'stcPath=3').'">'.BOX_INFORMATION_CONDITIONS.'</a>';?></li>
			  <?php if(BOX_INFORMATION_CONTACT_LINK !=''){ ?>
              <li><i class="bi bi-chevron-right"></i> <?php echo '<a href="' .BOX_INFORMATION_CONTACT_LINK. '">'.BOX_INFORMATION_CONTACT.'</a>';?></li><?php } ?>
                <?php 
				if (PURCHASE_NO_ACCOUNT =='no'){
				echo '<li><i class="bi bi-chevron-right"></i> <a href="'.tep_href_link(FILENAME_ACCOUNT).'">'.HEADER_ACCOUNT.'</a></li>';
				}else{
				echo '';	
				}
				?>
			</ul>
          </div>

          <div class="col-lg-4 col-md-6 footer-newsletter" style="text-align:center;">
            <?php 
		    if(SHOW_NEWSLETTER_BUTTON=='Enable'){
			echo '<h3>';	
			echo TEXT_FOOTER_NEWSLETTER; //THIS IS LANDING PAGE VERSION
			echo '</h3>';
			echo '<a class="btn btn-get-started" href="'.NEWSLETTER_LANDING_PAGE.'">Subscribe</a>';
			echo '<p><a target="_blank" href="https://www.mailer-pro.com">by Mailer-PRO</a></p>';
			}
			else
			{
			echo OTHER_FOOTER_TEXT;
			//use from the language file english.php
			//echo OTHER_FOOTER_TEXT2;
			}
			?></div>
        </div>
      </div>
    </div>

    <div class="footer-legal text-center">
      <div class="container d-flex flex-column flex-lg-row justify-content-center justify-content-lg-between align-items-center">
        <div class="d-flex flex-column align-items-center align-items-lg-start">
          <div class="copyright">
            <?php echo "Copyright &copy; 2007-".date('Y'); ?> <a href="/"><?php echo STORE_NAME; ?></a>. <?php echo FOOTER_MESSAGE; ?><br />
	<a href="https://www.osconcert.com" target="_blank"><img src="<?php echo DIR_WS_TEMPLATES . TEMPLATE_NAME; ?>/images/osc1.png" alt="<?php echo STORE_NAME; ?>" title="<?php echo STORE_NAME; ?>" /></a> Powered by <a href="https://www.osconcert.com" target="_blank">osConcert</a>.
          </div>
        </div>
	<div class="social-links order-first order-lg-last mb-3 mb-lg-0">
	<?php if (TWITTER_ID!=''){ ?>
	<a target="_blank" href="https://twitter.com/<?php echo TWITTER_ID; ?>" class="twitter"><i class="bi bi-twitter"></i></a>
	<?php }?>
	<?php if (FACEBOOK_ID!=''){ ?>
	<a target="_blank" href="https://facebook.com/<?php echo FACEBOOK_ID; ?>" class="facebook"><i class="bi bi-facebook"></i></a>
	<?php }?>
	<?php if (INSTAGRAM_ID!=''){ ?>
	<a target="_blank" href="https://www.instagram.com/<?php echo INSTAGRAM_ID; ?>" class="instagram"><i class="bi bi-instagram"></i></a>
	<?php }?>
	<?php if (LINKEDIN_ID!=''){ ?>
	<a target="_blank" href="https://www.linkedin.com/in/<?php echo LINKEDIN_ID; ?>" class="linkedin"><i class="bi bi-linkedin"></i></a>
	<?php }?>
</div>
    </div>
    </div>
  </footer>
  <!-- End Footer -->
  
<?php 
} 
?>