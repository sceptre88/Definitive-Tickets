<?php
/*
  $Id: mainpage.php,v 2.0 2003/06/13

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
		
		
		// if the content for the mainpage is empty try to give the details of current mainpage category
		$content=@file_get_contents(DIR_WS_TEMPLATES . TEMPLATE_NAME . "/content/" . $FSESSION->language . '/' . FILENAME_DEFINE_MAINPAGE);
		if ($content=="")
		{

			if ('SHOW_MAIN_FEATURED_PRODUCTS'=='true')
			{
			include('DIR_WS_MODULES . FILENAME_NEW_PRODUCTS');
			 }

		}
			//HERE is the template content file page
			echo '<div class="mainpage">';
			echo $content;
			echo '</div>';
		unset($content);
?>