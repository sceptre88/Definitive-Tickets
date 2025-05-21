<?php
/*
	Freeway eCommerce
	http://www.openfreeway.org
	Copyright (c) 2007 ZacWare

	Released under the GNU General Public License 
*/	
// Check to ensure this file is included in osConcert!
defined('_FEXEC') or die();
	echo tep_draw_form('contact_us', tep_href_link(FILENAME_CONTACT_US, 'action=send')); ?>

    <div class="section-header">
	<h2><?php echo HEADING_CONTACT; ?></h2>
	</div>

<?php
  if ($messageStack->size('contact') > 0) 
  {
?>
   <div><?php echo $messageStack->output('contact'); ?></div>
<?php
  }

  if (($FREQUEST->getvalue('action') == 'success')) 
  {
?>
<section id="contact">
<div class="container">
	<div class="row">
		<div class="col-md-12">
		<?php echo TEXT_SUCCESS; ?>
		</div>
	</div>
</div>
</section>
</form>
<?php
  } else {
?>
<?php   // cms_contact starting

$address =str_replace("\n","<br>",STORE_NAME_ADDRESS);
$query = "select * from email_messages where message_type = 'HCF'";
$query = tep_db_query($query);
$result = tep_db_fetch_array($query);
$name = tep_draw_input_field('name','',' required placeholder="'.TEXT_NAME.'"').'<br>';
$mail = tep_draw_input_field('email', '',' required placeholder="'.TEXT_EMAIL.'"'). '<br>';
$enquiry = tep_draw_textarea_field('enquiry', 'soft', 0, 0,'',' required placeholder="'.TEXT_MESSAGE.'"');
//#######################
	if( defined('GOOGLE_CAPTCHA_PUBLIC_KEY_V3') && tep_not_null(GOOGLE_CAPTCHA_PUBLIC_KEY_V3))
	{
		$continue = tep_template_image_submit('', IMAGE_BUTTON_SEND,'google');
	}else{
		$continue = tep_template_image_submit('', IMAGE_BUTTON_SEND);
	}
########################
$firstname = '<b>'.ENTRY_NAME.'</b>';
$lastname = '<b>'.ENTRY_LASTNAME.'</b>';
$message = '<b>'.ENTRY_MESSAGE.'</b>';
$email = '<b>'.ENTRY_EMAIL.'</b>';

define("TEXT_TN1", '');
define("TEXT_TN2", '');
define("TEXT_TN3", 'x');
define("TEXT_BT1", '');
define("TEXT_BT2", '');
define("TEXT_TN4", '');
define("TEXT_TM", '');
define("TEXT_FN", '');
define("TEXT_LN", '');
define("TEXT_EM", '');
//$replace_array = array('%%Name_Box%%'=>$name,'%%Email_Box%%'=>$mail,'%%Message_Box%%'=>$enquiry,'%%Continue_Button%%'=>$continue,'%%Reset_Button%%'=>$reset);
$replace_array = array(TEXT_TN1=>$name,TEXT_TN2=>$mail,TEXT_TN3=>$enquiry,TEXT_BT1=>$continue,TEXT_BT2=>$reset,TEXT_TN4=>$subject,TEXT_TM=>$message,TEXT_FN=>$firstname,TEXT_LN=>$lastname,TEXT_EM=>$email);
$body='<body>';
$body1='</body>';
//$messages=html_entity_decode($result['message_text']);
if(strpos($messages,html_entity_decode($body),0)!==false)
{
	$pos1=strpos($messages,html_entity_decode($body),0)+6;
	$messages=substr($messages,$pos1);
	$pos2=strpos($messages,html_entity_decode($body1),$pos1);
	$messages=substr($messages,0,$pos2);
} else $messages=$result['message_text'];

foreach($replace_array as $key=>$value)
{ 
	$messages = str_replace('%%'.$key.'%%',$value,$messages);
}
?>   
<div class="main controls">
<?php echo $address; ?>
<!--<br>
<a href="mailto:<?php //echo STORE_OWNER_EMAIL_ADDRESS; ?>"><?php //echo STORE_OWNER_EMAIL_ADDRESS; 
### this section commented out as it exposes the store email address to spiders/bots

?>
</a>
-->
</div> 
<?php ########### recaptcha V2 ###########
if( defined('GOOGLE_CAPTCHA_PUBLIC_KEY_V3') && tep_not_null(GOOGLE_CAPTCHA_PUBLIC_KEY_V3)){
	// do nothing just prevents V2 captcha displaying';
}
?>
<br>    
<?php echo $messages; ?>
<br>

<?php
  }
  ##### the </form> tag below is getting ignored - there's some code 
  ##### adding a closing form tag most likely imbalanced <div> in the email template
?>
</form>
	<br>
	<script type="text/javascript">
		function reset_form(){
			document.contact_us.reset();
		}
	</script>
	<script>
   function onSubmit() { 
    
        grecaptcha.ready(function() {
			 document.contact_us.addEventListener('submit', (event) => {
			  event.preventDefault();
			});
			
			
          grecaptcha.execute('<?php echo GOOGLE_CAPTCHA_PUBLIC_KEY_V3;?>', {action: 'contact_us_submit'}).then(function(token) {
              // Add your logic to submit to your backend server here.
			    var newField = document.createElement('input');
				  newField.setAttribute('type','hidden');
				  newField.setAttribute('name','g-recaptcha-response');
				  newField.setAttribute('value',token);
				  document.contact_us.appendChild(newField); 
				  
			if (document.contact_us.checkValidity()) {
                document.contact_us.submit();     
            } else {
              document.contact_us.reportValidity();
            }
				 
			 
          });
        });
 
   }
    </script>
	
	<?php if(CONTACT_MAP_URL !='')
		//example New York https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d22864.11283411948!2d-73.96468908098944!3d40.630720240038435!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c24fa5d33f083b%3A0xc80b8f06e177fe62!2sNew+York%2C+NY%2C+USA!5e0!3m2!1sen!2sbg!4v1540447494452
	{ 
	?>
	<div class="container mb-4">
        <iframe src="<?php echo CONTACT_MAP_URL; ?>" width="100%" height="380" frameborder="0" style="border:0" allowfullscreen></iframe>
      </div>
	<?php }  ?>

