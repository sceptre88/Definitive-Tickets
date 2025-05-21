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
<!-- Copyright section of the footer-->
  <footer id="footer">
    <div class="footer-top">
      <div class="container">
        <div class="row">

          <div class="col-lg-3 col-md-6">
            <div class="footer-info">
              <h3><?php echo TEXT_FOOTER_NAME; ?></h3>
              <p>
                <?php echo TEXT_FOOTER_DETAILS; ?>
              </p>
              <div class="social-links mt-3">
			<?php if (TWITTER_ID!=''){ ?>
			<a target="_blank" href="https://twitter.com/<?php echo TWITTER_ID; ?>"><i class="bi bi-twitter"></i></a>
			<?php } ?>
			<?php if (FACEBOOK_ID!=''){ ?>
			<a target="_blank" href="https://www.facebook.com/<?php echo FACEBOOK_ID; ?>"><i class="bi bi-facebook"></i></a>
			<?php } ?>
			<?php if (INSTAGRAM_ID!=''){ ?>
			<a target="_blank" href="https://instagram.com/<?php echo INSTAGRAM_ID; ?>"><i class="bi bi-instagram"></i></a>
			<?php } ?>
			<?php if (LINKEDIN_ID!=''){ ?>
			<a target="_blank" href="https://ph.linkedin.com/in/<?php echo LINKEDIN_ID; ?>"><i class="bi bi-linkedin"></i></a>
			<?php } ?>
            </div>
            </div>
          </div>

          <div class="col-lg-2 col-md-6 footer-links">
            <h4><?php echo TEXT_FOOTER_LINKS; ?></h4>
           <ul>
			  <?php if(BOX_INFORMATION_CONTACT_LINK !=''){ 
	echo '<li><i class="fa fa-angle-right"></i><a href="' .BOX_INFORMATION_CONTACT_LINK. '">'.BOX_INFORMATION_CONTACT.'</a></li>';
	}
	?>
             <?php 
			 if (DISPLAY_COLUMN_LEFT == 'no') 
			 {
			require(DIR_WS_TEMPLATES . TEMPLATE_NAME . '/boxes/footer_information.php');
			 }
			?>
			<li><i class="fa fa-angle-right"></i> <a href="account.php"><?php echo HEADER_ACCOUNT; ?></a></li>
            </ul>
          </div>

          <div class="col-lg-3 col-md-6 footer-links">
            <h4><?php echo TEXT_FOOTER_SERVICES; ?></h4>
            <ul>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Web Design</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Web Development</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Product Management</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Marketing</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Graphic Design</a></li>
            </ul>
          </div>

          <div class="col-lg-4 col-md-6 footer-newsletter">
            <h4><?php echo TEXT_FOOTER_NEWSLETTER; ?></h4>
            <p><?php echo TEXT_FOOTER_NEWSLETTER_DESC; ?></p>
            <form action="" method="post">
              <input type="email" name="email"><input type="submit" value="Subscribe">
            </form>

          </div>

        </div>
      </div>
    </div>

    <div class="container">
      <div class="copyright">
	<?php echo "Copyright &copy; 2007-".date('Y'); ?> <a href="/"><?php echo STORE_NAME; ?></a>. <?php echo FOOTER_MESSAGE; ?><br />
	<a href="https://www.osconcert.com" target="_blank"><img src="<?php echo DIR_WS_TEMPLATES . TEMPLATE_NAME; ?>/images/osc1.png" alt="<?php echo STORE_NAME; ?>" title="<?php echo STORE_NAME; ?>" /></a> Powered by <a href="https://www.osconcert.com" target="_blank">osConcert</a>.
      </div>
      
    </div>
  </footer><!-- End Footer -->
<?php 
} 
?>