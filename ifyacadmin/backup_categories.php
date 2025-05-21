<?php

/*

osCommerce, Open Source E-Commerce Solutions 
http://www.oscommerce.com 

Copyright (c) 2003 osCommerce 

Released under the GNU General Public License

UPDATED 30 May 2021 added abilty to make blocked seats active
*/
define( '_FEXEC', 1 );

  require('includes/application_top.php');
  
 //Missing language id for some reason
 $languages_id = $_SESSION['languages_id'];
 
 //add this function - could be popped into includes/functions/general but for simplicity here it is
 
   function tep_get_products_number($product_id, $language_id = 0) 
   {
    global $FSESSION;
	
	if($language_id<=0) 
	$language_id=$FSESSION->languages_id;
    $product_query = tep_db_query("select products_number from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . (int)$product_id . "' and language_id = '" . (int)$language_id . "'");
    $product = tep_db_fetch_array($product_query);

    return $product['products_number'];
  }
  require('includes/functions/categories_description.php');
  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();
  $action = (isset($_GET['action']) ? $_GET['action'] : '');
  $server_date = getServerDate(true);	

	if (tep_not_null($action)) 
	{
		switch ($action) 
		{
		  case 'setflag':
			if ( ($_GET['flag'] == '0') || ($_GET['flag'] == '1') || ($_GET['flag'] == '8')|| ($_GET['flag'] == '3')) 
			{
			  if (isset($_GET['pID'])) 
			  {
				tep_set_product_status($_GET['pID'], $_GET['flag']);
			  }
			}

			tep_redirect(tep_href_link(FILENAME_BACKUP_CATEGORIES, 'cPath=' . $_GET['cPath'] . '&pID=' . $_GET['pID']));
			break;
			
		

			$languages = tep_get_languages();
			for ($i=0, $n=sizeof($languages); $i<$n; $i++) 
			{
			  $categories_name_array = $_POST['categories_name'];

			  $language_id = $languages[$i]['id'];

			  $sql_data_array = array('categories_name' => tep_db_prepare_input($categories_name_array[$language_id]));


				if (ALLOW_CATEGORY_DESCRIPTIONS == 'true') 
				{
				  $sql_data_array = array('categories_name' => tep_db_prepare_input($_POST['categories_name'][$language_id]),
										  'categories_heading_title' => tep_db_prepare_input($_POST['categories_heading_title'][$language_id]),
										  'categories_description' => tep_db_prepare_input($_POST['categories_description'][$language_id]));
				}

			}

		}

}
	
//$go_back_to=$REQUEST_URI;
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/menu.js"></script>
<script language="javascript" src="includes/general.js"></script>
<script language="JavaScript" src="includes/date-picker.js"></script>

<?php
	require(DIR_WS_INCLUDES . 'tweak/' . HTML_EDITOR . '.php');
	textEditorLoadJS();
?>
<?php
include(DIR_WS_INCLUDES . 'javascript/' . 'webmakers_added_js.php')
?>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onLoad="SetFocus();">
<div id="spiffycalendar" class="text"></div>
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<!--Add osConcert message-->
            <div class="osconcert_message">
            <?php echo OSCONCERT_MESSAGE_AEC; ?>
            </div><br>
			<div class="osconcert_message">
            <?php echo OSCONCERT_MESSAGE_PD; ?>
            </div>
            <!--EOF osConcert message-->
	<!-- body_text //-->
	     <table border="0" width="100%" cellspacing="0" cellpadding="2">
	<?php   //----- new_category / edit_category (when ALLOW_CATEGORY_DESCRIPTIONS is 'true') -----
  if ($_GET['action'] == 'new_category_ACD' || $_GET['action'] == 'edit_category_ACD') 
  {
    if ( ($_GET['cID']) && (!$_POST) ) 
	{
      $categories_query = tep_db_query("select c.categories_id, cd.categories_name,c.categories_status, cd.categories_heading_title, cd.categories_description, c.categories_image, c.parent_id, c.sort_order, c.date_added, c.last_modified from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = '" . $_GET['cID'] . "' and c.categories_id = cd.categories_id and cd.language_id = '" . $languages_id . "' order by c.sort_order, cd.categories_id");
      $category = tep_db_fetch_array($categories_query);

      $cInfo = new objectInfo($category);
    } elseif ($_POST) 
	{
      $cInfo = new objectInfo($_POST);
      $categories_name = $_POST['categories_name'];
	  $categories_status = $_POST['categories_status'];
      $categories_heading_title = $_POST['categories_heading_title'];
      $categories_description = $_POST['categories_description'];
      $categories_url = $_POST['categories_url'];
    } else 
	{
      $cInfo = new objectInfo(array());
    }

    $languages = tep_get_languages();

    $text_new_or_edit = ($_GET['action']=='new_category_ACD') ? TEXT_INFO_HEADING_NEW_CATEGORY : TEXT_INFO_HEADING_EDIT_CATEGORY;
?>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo sprintf($text_new_or_edit, tep_output_generated_category_path($current_category_id)); ?></td>
            <td class="pageHeading" align="right"></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr><?php echo tep_draw_form('new_category', FILENAME_BACKUP_CATEGORIES, 'cPath=' . $cPath . '&cID=' . $_GET['cID'] . '&action=new_category_preview', 'post', 'enctype="multipart/form-data"'); ?>
        <td><table border="0" cellspacing="0" cellpadding="2">
<?php
    for ($i=0; $i<sizeof($languages); $i++) 
	{
?>
          <tr>
            <td class="main"><?php if ($i == 0) echo TEXT_EDIT_CATEGORIES_NAME; ?></td>
            <td class="main"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('categories_name[' . $languages[$i]['id'] . ']', (($categories_name[$languages[$i]['id']]) ? stripslashes($categories_name[$languages[$i]['id']]) : tep_get_category_name($cInfo->categories_id, $languages[$i]['id']))); ?></td>
          </tr>
<?php
    }
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
<?php 
    for ($i=0; $i<sizeof($languages); $i++) 
	{
?>
          <tr>
            <td class="main"><?php if ($i == 0) echo TEXT_EDIT_CATEGORIES_HEADING_TITLE; ?></td>
            <td class="main"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('categories_heading_title[' . $languages[$i]['id'] . ']', (($categories_name[$languages[$i]['id']]) ? stripslashes($categories_name[$languages[$i]['id']]) : tep_get_category_heading_title($cInfo->categories_id, $languages[$i]['id']))); ?></td>
          </tr>
<?php
    }
?>


          <tr>
            <td class="main">Category Status (1=enabled)(0=disabled)</td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('categories_status', $cInfo->categories_status, 'size="2"'); ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td class="main" align="right"><?php echo tep_draw_hidden_field('categories_date_added', (($cInfo->date_added) ? $cInfo->date_added : getServerDate())) . tep_draw_hidden_field('parent_id', $cInfo->parent_id) . tep_image_submit('button_preview.gif', IMAGE_PREVIEW) . '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_BACKUP_CATEGORIES, 'cPath=' . $cPath . '&cID=' . $_GET['cID']) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>'; ?></td>
      </form></tr>
		<?php

		//----- new_category_preview (active when ALLOW_CATEGORY_DESCRIPTIONS is 'true') -----
	}//action
	elseif ($_GET['action'] == 'new_category_preview') 
	{
		if ($_POST) 
		{
		  $cInfo = new objectInfo($_POST);
		  $categories_name = $_POST['categories_name'];
		  $categories_heading_title = $_POST['categories_heading_title'];
		  $categories_description = $_POST['categories_description'];

	// copy image only if modified
			$categories_image = new upload('categories_image');
			$categories_image->set_destination(DIR_FS_CATALOG_IMAGES);
			if ($categories_image->parse() && $categories_image->save()) {
			  $categories_image_name = $categories_image->filename;
			} else {
			$categories_image_name = $_POST['categories_previous_image'];
		  }

		} else 
		{
		  $category_query = tep_db_query("select c.categories_id, cd.language_id, cd.categories_name, c.categories_status,cd.categories_heading_title, cd.categories_description, c.categories_image, c.sort_order, c.date_added, c.last_modified from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = cd.categories_id and c.categories_id = '" . $_GET['cID'] . "'");
		  $category = tep_db_fetch_array($category_query);

		  $cInfo = new objectInfo($category);
		  $categories_image_name = $cInfo->categories_image;
		}

    $form_action = ($_GET['cID']) ? 'update_category' : 'insert_category';
    echo tep_draw_form($form_action, FILENAME_BACKUP_CATEGORIES, 'cPath=' . $cPath . '&cID=' . $_GET['cID'] . '&action=' . $form_action, 'post', 'enctype="multipart/form-data"');

    $languages = tep_get_languages();
    for ($i=0; $i<sizeof($languages); $i++) 
	{
      if ($_GET['read'] == 'only') 
	  {
        $cInfo->categories_name = tep_get_category_name($cInfo->categories_id, $languages[$i]['id']);
        $cInfo->categories_heading_title = tep_get_category_heading_title($cInfo->categories_id, $languages[$i]['id']);
        $cInfo->categories_description = tep_get_category_description($cInfo->categories_id, $languages[$i]['id']);
      } else 
	  {
        $cInfo->categories_name = tep_db_prepare_input($categories_name[$languages[$i]['id']]);
        $cInfo->categories_heading_title = tep_db_prepare_input($categories_heading_title[$languages[$i]['id']]);
        $cInfo->categories_description = tep_db_prepare_input($categories_description[$languages[$i]['id']]);
      }

    }
    if ($_GET['read'] == 'only') 
	{
      if ($_GET['origin']) {
        $pos_params = strpos($_GET['origin'], '?', 0);
        if ($pos_params != false) {
          $back_url = substr($_GET['origin'], 0, $pos_params);
          $back_url_params = substr($_GET['origin'], $pos_params + 1);
        } else 
		{
          $back_url = $_GET['origin'];
          $back_url_params = '';
        }
      } else 
	  {
        $back_url = FILENAME_BACKUP_CATEGORIES;
        $back_url_params = 'cPath=' . $cPath . '&cID=' . $cInfo->categories_id;
      }
?>
      <tr>
        <td align="right"><?php echo '<a href="' . tep_href_link($back_url, $back_url_params, 'NONSSL') . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a>'; ?></td>
      </tr>
<?php
    } else 
	{
?>
      <tr>
        <td align="right" class="smallText">
<?php
/* Re-Post all POST'ed variables */
      reset($_POST);
		foreach($_POST as $key => $value)  
	  {
        if (!is_array($_POST[$key])) {
          echo tep_draw_hidden_field($key, htmlspecialchars(stripslashes($value)));
        }
      }
      $languages = tep_get_languages();
      for ($i=0; $i<sizeof($languages); $i++) 
	  {
        echo tep_draw_hidden_field('categories_name[' . $languages[$i]['id'] . ']', htmlspecialchars(stripslashes($categories_name[$languages[$i]['id']])));
        echo tep_draw_hidden_field('categories_heading_title[' . $languages[$i]['id'] . ']', htmlspecialchars(stripslashes($categories_heading_title[$languages[$i]['id']])));
        echo tep_draw_hidden_field('categories_description[' . $languages[$i]['id'] . ']', htmlspecialchars(stripslashes($categories_description[$languages[$i]['id']])));
      }
      echo tep_draw_hidden_field('X_categories_image', stripslashes($categories_image_name));
      echo tep_draw_hidden_field('categories_image', stripslashes($categories_image_name));

      echo tep_image_submit('button_back.gif', IMAGE_BACK, 'name="edit"') . '&nbsp;&nbsp;';

      if ($_GET['cID']) 
	  {
        echo tep_image_submit('button_update.gif', IMAGE_UPDATE);
      } else 
	  {
        echo tep_image_submit('button_insert.gif', IMAGE_INSERT);
      }
      echo '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_BACKUP_CATEGORIES, 'cPath=' . $cPath . '&cID=' . $_GET['cID']) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>';
?></td>
      </form></tr>
<?php
    }


  } 
  
  elseif ($action == 'new_product') 
  {
    $parameters = array('products_name' => '',
                       'products_description' => '',
					    'products_number' => '',
                       'products_url' => '',
                       'products_id' => '',
                       'products_quantity' => '',
					   'master_quantity' => '',
					   'product_type' => '',
					   'is_attributes' =>'',
                       'products_model' => '',
					   'color_code' => '',
                       'products_image_1' => '',
                       'products_price' => '',
                       'products_weight' => '',
					   'products_sort_order' =>'',
                       'products_date_added' => '',
                       'products_last_modified' => '',
                       //'products_date_available' => '',
                       'products_status' => '',
                       'products_tax_class_id' => '',
                       'manufacturers_id' => '');

    $pInfo = new objectInfo($parameters);

    if (isset($_GET['pID']) && empty($_POST)) 
	{
      $product_query = tep_db_query("select pd.products_name, pd.products_description, pd.products_number, p.product_type,p.color_code, pd.products_url, p.products_id, p.products_quantity,p.master_quantity, p.is_attributes,p.products_model,
	  								 p.products_image_1,p.products_price, p.products_weight,p.products_sort_order, p.products_date_added, p.products_last_modified, date_format(p.products_date_available, '%Y-%m-%d') as products_date_available, p.products_status, p.products_tax_class_id, p.manufacturers_id, p.product_type from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = '" . (int)$_GET['pID'] . "' and p.product_type='P' and p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "'");
      $product = tep_db_fetch_array($product_query);
      $pInfo->objectInfo($product);
    } elseif (tep_not_null($_POST)) 
	{
      $pInfo->objectInfo($_POST);
      $products_name = $_POST['products_name'];
      $products_description = $_POST['products_description'];
	   $products_number = $_POST['products_number'];
      $products_url = $_POST['products_url'];
    }


    # get selected categories
    $categories_query_selected = tep_db_query("select categories_id from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . $_GET['pID'] . "'");
    $categories_array_selected = array(array('id' => ''));
    while ($categories = tep_db_fetch_array($categories_query_selected)) 
	{
      $categories_array_selected[] = array('id' => $categories['categories_id']);
    }

    $categories_array = array(array('id' => '', 'text' => TEXT_NONE));
    #PR Algozone: Categories list displays only for one language (Deafault is English)
    $language_id = 1;
    $categories_array = tep_get_category_tree(); // added by R Calder

    $languages = tep_get_languages();

    if (!isset($pInfo->products_status)) $pInfo->products_status = '1';
    switch ($pInfo->products_status) {
      case '0': $in_status = false; $out_status = true; break;
      case '1':
      default: $in_status = true; $out_status = false;
    }

	 
	 echo tep_draw_form('new_product', FILENAME_BACKUP_CATEGORIES, 'cPath=' . $cPath . '&pID=' . $_GET['pID'] . '&action=new_product_preview', 'post', ' onSubmit="return ValidateForm();" enctype="multipart/form-data"'); 
	
	?>
    <table border="0" width="100%" cellspacing="0" cellpadding="2">

      <tr>
        <td class="pageHeading"><?php echo sprintf(TEXT_NEW_PRODUCT, tep_output_generated_category_path($current_category_id)); ?></td>
      </tr>
      <tr>
        <td><table border="0" cellspacing="0" cellpadding="2">
         <tr>
            <td class="main"><?php echo TEXT_PRODUCTS_STATUS; ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_radio_field('products_status', '1', $in_status) . '&nbsp;' . TEXT_PRODUCT_AVAILABLE . '&nbsp;' . tep_draw_radio_field('products_status', '0', $out_status) . '&nbsp;' . TEXT_PRODUCT_NOT_AVAILABLE; ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr><?php $_array=array('d','m','Y');  $replace_array=array('DD','MM','YYYY'); 	$date_format=str_replace($_array,$replace_array,EVENTS_DATE_FORMAT);?>
            <td class="main"><?php //echo TEXT_PRODUCTS_DATE_AVAILABLE; ?><!--<strong>SHOW CATEGORY</strong>--></td>
            <td class="main" <?php if ($product->product_type=="P"){ 
									echo "style=display:none";
									}
									?>>
			<?php  //echo '&nbsp;' . tep_draw_pull_down_menu('manufacturers_id', $categories_array, $pInfo->manufacturers_id);
			
			
			//echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field("products_date_available",format_date($pInfo->products_date_available),"size=10",false,'text',false);?>
			               <!-- <a href="javascript:show_calendar('new_product.products_date_available',null,null,'<?php echo $date_format;?>');"
							onmouseover="window.status='Date Picker';return true;"
							onmouseout="window.status='';return true;"><img border="none" src="images/icon_calendar.gif"/>  
							</a>-->
							</td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
<?php
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
?>
          <tr>
            <td class="main"><?php if ($i == 0) echo TEXT_PRODUCTS_NAME; ?></td>
			<td class="main"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('products_name[' . $languages[$i]['id'] . ']', (isset($products_name[$languages[$i]['id']]) ? stripslashes($products_name[$languages[$i]['id']]) : tep_get_products_name($pInfo->products_id, $languages[$i]['id']))); ?></td>
          </tr>
<?php
    }
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr bgcolor="#ebebff">
            <td class="main"><?php echo TEXT_PRODUCTS_TAX_CLASS; ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_pull_down_menu('products_tax_class_id', $tax_class_array, $pInfo->products_tax_class_id, 'onchange="updateGross()"'); ?></td>
          </tr>
          <tr bgcolor="#ebebff">
            <td class="main"><?php echo TEXT_PRODUCTS_PRICE_NET; ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('products_price', $pInfo->products_price, 'onKeyUp="updateGross()"'); ?></td>
          </tr>
          <tr bgcolor="#ebebff">
            <td class="main"><?php echo TEXT_PRODUCTS_PRICE_GROSS; ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('products_price_gross', $pInfo->products_price, 'OnKeyUp="updateNet()"'); ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
<script language="javascript"><!--
updateGross();
//--></script>
		
<?php
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) 
	{
?>
          <tr>
            <td class="main" valign="top"><?php if ($i == 0) //echo TEXT_PRODUCTS_DESCRIPTION; ?></td>
            <td><table border="0" cellspacing="0" cellpadding="0" style="display:none">
              <tr>
                <td class="main" valign="top"><?php //echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;</td>
                <td class="main"><?php //echo tep_draw_textarea_field('products_description[' . $languages[$i]['id'] . ']', 'soft', '80', '24', (isset($products_description[$languages[$i]['id']]) ? stripslashes($products_description[$languages[$i]['id']]) : tep_get_products_description($pInfo->products_id, $languages[$i]['id'])),'id="products_description[' . $languages[$i]['id'] . ']"'); ?></td>
              </tr>
            </table></td>
          </tr>
          
           <tr>
            <td class="main">Seat Number (Seat cell link):</td>
			<td class="main"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('products_number[' . $languages[$i]['id'] . ']', (isset($products_number[$languages[$i]['id']]) ? stripslashes($products_number[$languages[$i]['id']]) : tep_get_products_number($pInfo->products_id, $languages[$i]['id']))); ?></td>
          </tr>
<?php
    }
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_PRODUCTS_QUANTITY; ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('products_quantity', $pInfo->products_quantity); ?></td>
          </tr>
          <tr>
            <td class="main">Master <?php echo TEXT_PRODUCTS_QUANTITY; ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('master_quantity', $pInfo->master_quantity); ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_PRODUCTS_MODEL; ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('products_model', $pInfo->products_model); ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
		  <?php 
		  for ($icnt=1;$icnt<=1;$icnt++)
		  { 
		  			$var_name_1="products_image_" . $icnt;
					//$var_name_2="products_title_" . $icnt;
					if (isset($_POST[$var_name_1])) $image_name=$_POST[$var_name_1];
					else $image_name=$pInfo->$var_name_1;
		  ?>
		  <tr>
		  	<td colspan="2" valign="top">
			<table border="0" cellpadding="0" cellspacing="0" style="display:none">
				  <tr>
					<td class="main" valign="top"><?php echo sprintf(TEXT_PRODUCTS_IMAGE,$icnt); ?></td>
					<td class="main" valign="top"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_file_field('products_image_' . $icnt) . '<br>' . tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . $image_name . tep_draw_hidden_field('products_previous_image_' .$icnt, $image_name); ?></td>
				  </tr>
			</table>
			</td>
		</tr>
		  <?php  } ?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
<?php
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) 
	{
?>
          <tr>
            <td class="main">Color Banding:<?php //if ($i == 0) echo TEXT_COLOR_CODE . ; ?></td>
            <td class="main"><?php
			
			 
			echo tep_draw_input_field('color_code', $pInfo->color_code);
			//echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('products_url[' . $languages[$i]['id'] . ']', (isset($products_url[$languages[$i]['id']]) ? stripslashes($products_url[$languages[$i]['id']]) : tep_get_products_url($pInfo->products_id, $languages[$i]['id']))); ?></td>
          </tr>
<?php
    }
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_PRODUCTS_WEIGHT; ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('products_weight', $pInfo->products_weight); ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
     <!--     <tr>
            <td class="main"><?php echo TEXT_PRODUCTS_SORT_ORDER; ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('products_sort_order', $pInfo->products_sort_order); ?></td>
          </tr> 
		  <tr>
			<td style="display:none" colspan="2" class="main"><?php //echo tep_draw_checkbox_field("is_attributes",'Y',($pInfo->is_attributes=='Y'?true:false)) .'&nbsp;'. TEXT_IS_ATTRIBUTES;?></td>
		</tr>	
        </table></td>
      </tr>-->
       
<?php
/////////////////////////////////////////////////////////////////////
// BOF: WebMakers.com Added: Draw Attribute Tables
?>
<?php
    // $rows = 0;
    // $options_query = tep_db_query("select products_options_id, products_options_name from " . TABLE_PRODUCTS_OPTIONS . " where language_id = '" . $languages_id . "' order by products_options_sort_order");
	
    // while ($options = tep_db_fetch_array($options_query)) { 
	?>
		
	<?php   
	// $values_query = tep_db_query("select pov.products_options_values_id, pov.products_options_values_name from " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov, " . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " p2p where pov.products_options_values_id = p2p.products_options_values_id and p2p.products_options_id = '" . $options['products_options_id'] . "' and pov.language_id = '" . $languages_id . "'");
      // $header = false;
      // while ($values = tep_db_fetch_array($values_query)) {
        // $rows ++;
        // if (!$header) {
          // $header = true;
?>
          <tr valign="top">
<td><table border="0" cellpadding="1" cellspacing="1">
              <tr>
            	<td colspan="15"><?php if ($options['products_options_name']=='Size')  echo tep_black_line(); ?></td>
         	 </tr>
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"width="250" align="left"><?php echo $options['products_options_name']; ?></td>
                <td class="dataTableHeadingContent" width="50" align="center"><?php echo 'Prefix'; ?></td>
                <td class="dataTableHeadingContent" width="70" align="center"><?php echo 'Price'; ?></td>
                <td class="dataTableHeadingContent" width="70" align="center"><?php echo 'Sort Order'; ?></td>
                <td class="dataTableHeadingContent" width="20">&nbsp;</td>
                <td class="dataTableHeadingContent" width="70" align="center"><?php echo 'One Time Charge'; ?></td>
                <td class="dataTableHeadingContent" width="70" align="center"><?php echo 'Weight Prefix'; ?></td>
                <td class="dataTableHeadingContent" width="70" align="center"><?php echo 'Weight'; ?></td>
		        <!--<td class="dataTableHeadingContent" width="70" align="center"><?php //echo 'Quantity'; ?></td>-->
                <td class="dataTableHeadingContent" width="70" align="center"><?php echo 'Units'; ?></td>
                <td class="dataTableHeadingContent" width="70" align="center"><?php echo 'Units Price'; ?></td>
              </tr>
            
              <tr class="<?php echo (floor($rows/2) == ($rows/2) ? 'attributes-even' : 'attributes-odd'); ?>">
                <td class="smallText"><?php echo tep_draw_checkbox_field('option'. '['.$rows . ']', $attributes['products_attributes_id'], $attributes['products_attributes_id'],'','onClick=javascript:change(); ') . '&nbsp;' . $values['products_options_values_name']; ?>&nbsp;</td>
                <td class="smallText" width="50" align="center"><?php echo tep_draw_input_field('prefix' .'['. $rows . ']', $attributes['price_prefix'], 'size="2"'); ?></td>
                <td class="smallText" width="70" align="center"><?php echo tep_draw_input_field('price' .'['.$rows . ']', $attributes['options_values_price'], 'size="7"'); ?></td>
                <td class="smallText" width="70" align="center"><?php echo tep_draw_input_field('products_options_sort_order' .'[ '.$rows . ']', $attributes['products_options_sort_order'], 'size="7"'); ?></td>
                <td class="smallText" width="20">&nbsp;</td>
                <td class="smallText" width="70" align="center"><?php echo tep_draw_input_field('product_attributes_one_time' .'[' .$rows . ']', $attributes['product_attributes_one_time'], 'size="2"'); ?></td>
                <td class="smallText" width="70" align="center"><?php echo tep_draw_input_field('products_attributes_weight_prefix' . '['.$rows . ']', $attributes['products_attributes_weight_prefix'], 'size="2"'); ?></td>
                <td class="smallText" width="70" align="center"><?php echo tep_draw_input_field('products_attributes_weight' .'['. $rows . ']', $attributes['products_attributes_weight'], 'size="7"'); ?></td>
              <!--  <td class="smallText" width="70" align="center"><?php //echo tep_draw_input_field('products_attributes_quantity[' . $rows . ']', $attributes['products_attributes_quantity'], 'size="7"'); ?><?PHP //$qcheck = $qcheck + $attributes['products_attributes_quantity']; ?></td>-->
			    <td class="smallText" width="70" align="center"><?php echo tep_draw_input_field('products_attributes_units' .'['. $rows . ']', $attributes['products_attributes_units'], 'size="7"'); ?></td>
                <td class="smallText" width="70" align="center"><?php echo tep_draw_input_field('products_attributes_units_price' .'['. $rows . ']', $attributes['products_attributes_units_price'], 'size="7"'); ?></td>
              </tr>
			  <tr>
                <td colspan="15"><?PHP if ($values['products_options_values_name']=="Not Required") echo tep_black_line(); ?></td>
             </tr>
<?php
      //}
      if ($header) {
?>			 
            </table></td>
<?php
      }
    //}
?>
          </tr>
		  <tr>
				<td ><?php echo tep_draw_separator('pixel_trans.gif', '24', '15');?></td>
		  </tr>
		  <tr>
		  	<td>
			<table style="display:none" cellpadding="5" cellspacing="0" border="1" width="50%" bgcolor="#ebebff">
			<tr>
				<td style="display:none" class="main" width="35%"><?php //echo tep_draw_pull_down_menu('attribute_list',$attribute_array,'',' size=10 style="width:100%" onkeyup="javascript:do_action(\'select\');" onkeydown="javascript:do_action(\'select\');" onClick="javascript:do_action(\'select\');"');
					//echo tep_draw_hidden_field('attributes_array');
				?></td>
			
				<td style="display:none" class="main" valign="middle" width="10%" align="center">&nbsp;<?php echo '<a href="javascript:do_action(\'add\');">' . tep_image_button('button_add.gif',IMAGE_ADD_ATTRIBUTE) . '</a><br><br><a href="javascript:do_action(\'edit\')">' . tep_image_button('button_update.gif',IMAGE_EDIT_ATTRIBUTE) . '<a><br><br><a href="javascript:do_action(\'delete\')">' . tep_image_button('button_delete.gif',IMAGE_DELETE_ATTRIBUTE) . '<a>';?>&nbsp;</td>
			</tr>
			<tr>
				<td colspan="2">
					<table style="display:none" cellpadding="2" cellspacing="2" border="0" >
		  <?php   
		  if(tep_db_num_rows($options_query)>0){
			 tep_db_data_seek($options_query,0);
			  while($options=tep_db_fetch_array($options_query)){?>
						<tr id="<?php echo 'options'.$options['products_options_id'];?>">
							<td class="main"><?php echo $options['products_options_name'];?></td>
							<td class="main"><?php echo tep_draw_pull_down_menu('option_values'.$options['products_options_id'],array(),'','  id="option_values'.$options['products_options_id'].'"');?></td>
							<td>&nbsp;</td>
						</tr>
	<?php	  } 
		}?>
	</table>
	</td></tr>
					<tr>
						<td colspan="3" class="main"><?php echo TEXT_QUANTITY.'&nbsp;'.tep_draw_input_field('quantity','','','',' size 10');?></td>
					</tr>	
			</table>
		</td>
	</tr>
<?php
// EOF: WebMakers.com Added: Draw Attribute Tables
/////////////////////////////////////////////////////////////////////
?>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
	  
        <td class="main" align="right"><?php 
		//preview 
		echo tep_image_submit('button_preview.gif', IMAGE_PREVIEW,'onclick = "javascript:do_quantity_check();" ');
		 ?><?php //echo tep_draw_hidden_field('products_date_added', (tep_not_null($pInfo->products_date_added) ? $pInfo->products_date_added : date('Y-m-d'))) .  '&nbsp;&nbsp;'. tep_image_submit('button_save.gif', IMAGE_SAVE) . '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_BACKUP_CATEGORIES, 'cPath=' . $cPath . (isset($_GET['pID']) ? '&pID=' . $_GET['pID'] : '')) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>'; 
		 
		 echo tep_draw_hidden_field('products_date_added', (tep_not_null($pInfo->products_date_added) ? $pInfo->products_date_added : getServerDate())) .   '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_BACKUP_CATEGORIES, 'cPath=' . $cPath . (isset($_GET['pID']) ? '&pID=' . $_GET['pID'] : '')) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>'; 
		 echo tep_draw_hidden_field('attribute_quantity',$qcheck);
		// echo tep_draw_hidden_field('attrib_ordered',$attributes['products_attributes_ordered']);
		 ?></td>
      </tr>
    </table>
	<script language="javascript">
		change();
		init_page();
		</script>
	</form>
<?php
       if (HTML_AREA_WYSIWYG_DISABLE=='Enable') { 
		 for ($i = 0, $n = sizeof($languages); $i < $n; $i++) { ?>
		    <script>initEditor('products_description[<?php echo $languages[$i]['id']; ?>]');</script>
		<?php  } 
		} ?>
<?php
  } 
  
  elseif ($action == 'new_product_preview') 
  {
  
    if (tep_not_null($_POST)) {
      $pInfo = new objectInfo($_POST);
      $products_name = $_POST['products_name'];
      $products_description = $_POST['products_description'];
	  $products_number = $_POST['products_number'];
      $color_code = $_POST['color_code'];
    } else {
      $product_query = tep_db_query("select p.products_id, pd.language_id, p.product_type,p.color_code, pd.products_name, pd.products_number, pd.products_description, pd.products_url, p.products_quantity,p.is_attributes, p.products_model, p.products_price, p.products_weight, p.products_sort_order,p.products_date_added, p.products_last_modified, p.products_date_available, p.products_status, p.manufacturers_id,p.products_image_1  from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = pd.products_id and p.products_id = '" . (int)$_GET['pID'] . "'");
      $product = tep_db_fetch_array($product_query);

      $pInfo = new objectInfo($product);
	  $products_image_name=$pInfo->products_image_1;
    }

    $form_action = ($_GET['pID']) ? 'update_product' : 'insert_product';
	
    echo tep_draw_form($form_action, FILENAME_BACKUP_CATEGORIES, 'cPath=' . $cPath . (isset($_GET['pID']) ? '&pID=' . $_GET['pID'] : '') . '&action=' . $form_action, 'post', 'enctype="multipart/form-data"');

    $languages = tep_get_languages();
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
      if (isset($_GET['read']) && ($_GET['read'] == 'only')) {
        $pInfo->products_name = tep_get_products_name($pInfo->products_id, $languages[$i]['id']);
		 $pInfo->products_number = tep_get_products_number($pInfo->products_id, $languages[$i]['id']);
        $pInfo->products_description = tep_get_products_description($pInfo->products_id, $languages[$i]['id']);
        $pInfo->products_url = tep_get_products_url($pInfo->products_id, $languages[$i]['id']);
      } else {
        $pInfo->products_name = tep_db_prepare_input($products_name[$languages[$i]['id']]);
		$pInfo->products_number = tep_db_prepare_input($products_number[$languages[$i]['id']]);
        $pInfo->products_description = tep_db_prepare_input($products_description[$languages[$i]['id']]);
        $pInfo->products_url = tep_db_prepare_input($products_url[$languages[$i]['id']]);
      }
?>
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . $pInfo->products_name; ?></td>
            <td class="pageHeading" align="right"><?php echo $currencies->format($pInfo->products_price); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td class="main">
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td><?php echo $pInfo->products_description;
				
				?></td>
			</tr>
		</table>
		</td>
      </tr>
<?php 
      if ($pInfo->products_url) {
?>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td class="main"><?php //echo sprintf(TEXT_PRODUCT_MORE_INFORMATION, $pInfo->color_code); ?></td>
      </tr>
<?php
      }
?>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
<?php
     // if ($pInfo->products_date_available > getServerDate()) {
?>
      <tr>
        <td align="center" class="smallText"><?php //echo sprintf(TEXT_PRODUCT_DATE_AVAILABLE, tep_date_long($pInfo->products_date_available)); ?></td>
      </tr>
<?php
      //} else {
?>
      <tr>
        <td align="center" class="smallText">
		
		<?php //echo sprintf(TEXT_PRODUCT_DATE_ADDED, $pInfo->color_code); ?></td>
      </tr>
<?php
     // }
?>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
<?php
    }

    if (isset($_GET['read']) && ($_GET['read'] == 'only')) {
      if (isset($_GET['origin'])) {
        $pos_params = strpos($_GET['origin'], '?', 0);
        if ($pos_params != false) {
          $back_url = substr($_GET['origin'], 0, $pos_params);
          $back_url_params = substr($_GET['origin'], $pos_params + 1);
        } else {
          $back_url = $_GET['origin'];
          $back_url_params = '';
        }
      } else {
        $back_url = FILENAME_BACKUP_CATEGORIES;
        $back_url_params = 'cPath=' . $cPath . '&pID=' . $pInfo->products_id;
      }
?>
      <tr>
        <td align="right"><?php echo '<a href="' . tep_href_link($back_url, $back_url_params, 'NONSSL') . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a>'; ?></td>
      </tr>
<?php
    }
  } else 
  {
?>
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, 1); ?></td>
            <td align="right"><table border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr>
                <td class="smallText" align="right">
<?php
    echo tep_draw_form('search', FILENAME_BACKUP_CATEGORIES, '', 'get');
    echo HEADING_TITLE_SEARCH . ' ' . tep_draw_input_field('search');
    echo '</form>';
?>
                </td>
              </tr>
              <tr>
                <td class="smallText" align="right">
<?php
    echo tep_draw_form('goto', FILENAME_BACKUP_CATEGORIES, '', 'get');
    echo HEADING_TITLE_GOTO . ' ' . tep_draw_pull_down_menu('cPath', tep_get_category_tree(), $current_category_id, 'onChange="this.form.submit();"');
    echo '</form>';
?>
                </td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top">
			
			<table border="0" width="50%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_CATEGORIES_PRODUCTS; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_STATUS; ?></td>
			
              </tr>
<?php
    $categories_count = 0;
    $rows = 0;
    if (isset($_GET['search'])) {
      $search = tep_db_prepare_input($_GET['search']);

      $categories_query = tep_db_query("select c.categories_id, cd.categories_name, cd.concert_date, c.categories_image, c.parent_id, c.sort_order, c.date_added, c.last_modified from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "' and cd.categories_name like '%" . tep_db_input($search) . "%' order by c.sort_order, cd.categories_id");
	  } else {
      $categories_query = tep_db_query("select c.categories_id, cd.categories_name, cd.concert_date, c.categories_image, c.parent_id, c.sort_order, c.date_added, c.last_modified from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.parent_id = '" . (int)$current_category_id . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "' order by cd.categories_id");
   }
   

    while ($categories = tep_db_fetch_array($categories_query)) 
	{
	
      $categories_count++;
      $rows++;

// Get parent_id for subcategories if search
      if (isset($_GET['search'])) $cPath= $categories['parent_id'];

      if ((!isset($_GET['cID']) && !isset($_GET['pID']) || (isset($_GET['cID']) && ($_GET['cID'] == $categories['categories_id']))) && !isset($cInfo) && (substr($action, 0, 3) != 'new')) {
        $category_childs = array('childs_count' => tep_childs_in_category_count($categories['categories_id']));
        $category_products = array('products_count' => tep_products_in_category_count($categories['categories_id']));

        $cInfo_array = array_merge($categories, $category_childs, $category_products);
        $cInfo = new objectInfo($cInfo_array);
      }

      if (isset($cInfo) && is_object($cInfo) && ($categories['categories_id'] == $cInfo->categories_id) ) {
        echo '              <tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_BACKUP_CATEGORIES, tep_get_path($categories['categories_id'])) . '\'">' . "\n";
      } else {
        echo '              <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_BACKUP_CATEGORIES, 'cPath=' . $cPath . '&cID=' . $categories['categories_id']) . '\'">' . "\n";
      }
?>
               
	   <td class="dataTableContent"><?php 
		echo '<a href="' . tep_href_link(FILENAME_BACKUP_CATEGORIES, tep_get_path($categories['categories_id'])) . '">' . tep_image(DIR_WS_ICONS . 'folder.gif', ICON_FOLDER) . '</a>
		&nbsp;<b>' . $categories['categories_name'] . ' : ' . $categories['concert_date'] . '</b>'; 
		?>
		</td>
		<td class="dataTableContent" align="center" colspan="2">&nbsp;</td>
		<td class="dataTableContent" align="right"><?php if (isset($cInfo) && is_object($cInfo) && ($categories['categories_id'] == $cInfo->categories_id) ) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . tep_href_link(FILENAME_BACKUP_CATEGORIES, 'cPath=' . $cPath . '&cID=' . $categories['categories_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
	  </tr>
<?php
    }

    $products_count = 0;
    if (isset($_GET['search'])) {
      $products_query = tep_db_query("select p.products_id, pd.products_name, pd.products_number, p.products_sku,  p.product_type, p.section_id, p.parent_id, p.products_quantity,p.is_attributes, p.products_price, p.products_ordered, p.products_date_added, p.products_last_modified, p.products_date_available, p.products_status, p.products_sort_order, p2c.categories_id,p.products_image_1 from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where p.products_id = pd.products_id and p.product_type='P' and pd.language_id = '" . (int)$languages_id . "' and p.products_id = p2c.products_id and pd.products_name like '%" . tep_db_input($search) . "%'  and p.products_sku>'0' order by p.products_sort_order, pd.products_id");
    } else {
      $products_query = tep_db_query("select p.products_id, pd.products_name, pd.products_number, p.products_sku, p.product_type, p.section_id, p.parent_id, p.products_quantity,p.is_attributes, p.products_price,p.products_ordered, p.products_date_added, p.products_last_modified, p.products_date_available, p.products_status, p.products_sort_order, p.products_image_1  from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where p.products_id = pd.products_id and p.product_type='P' and pd.language_id = '" . (int)$languages_id . "' and p.products_id = p2c.products_id and p2c.categories_id = '" . (int)$current_category_id . "' and p.products_sku>'0' order by p.products_sort_order, pd.products_id");
    }
    while ($products = tep_db_fetch_array($products_query)) 
	{
      $products_count++;
      $rows++;
	  
	  if($products['products_quantity']==0 && $products['products_status']==1)
	  {
		  $warning='red';
	  }else{
		  $warning='';
	  }
	  
	  if($products['products_ordered']>0 && $products['products_status']==1)
	  {
		  $warning='blue';
	  }else{
		  $warning='';
	  }
	  
?>
		<td class="dataTableContent">
		<?php echo '<a href="' . tep_href_link(FILENAME_BACKUP_CATEGORIES, 'cPath=' . $cPath . '&pID=' . $products['products_id'] . '&action=new_product_preview&read=only') . '">' . tep_image(DIR_WS_ICONS . 'preview.gif', ICON_PREVIEW) . '</a><span class="' . $warning . '"><b>&nbsp;' . $products['products_name']; ?>
		</span></b></td>
		<td class="dataTableContent" align="center">
<?php
      
		if ($products['products_sku'] == '1') 
		{
		//echo "&nbsp;&nbsp;RESET";
		$r=15;
		}
		else{
		$r=10; 
		}

		if ($products['products_status'] == '1') 
		{
		echo tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, $r, $r) 
		.'&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_BACKUP_CATEGORIES, 'action=setflag&flag=0&pID=' . $products['products_id'] . '&cPath=' . $cPath) . '">'
		. tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>'.
		'&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_BACKUP_CATEGORIES, 'action=setflag&flag=8&pID=' . $products['products_id'] . '&cPath=' . $cPath) . '">
		' . tep_image(DIR_WS_IMAGES . 'icon_status_hidden_light.gif', IMAGE_ICON_STATUS_HIDDEN_LIGHT, 10, 10) . '</a>'
		;

		} elseif  ($products['products_status'] == '0') 
		{
		echo '<a href="' . tep_href_link(FILENAME_BACKUP_CATEGORIES, 'action=setflag&flag=1&pID=' . $products['products_id'] . '&cPath=' . $cPath) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10)
		.'&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_BACKUP_CATEGORIES, 'action=setflag&flag=8&pID=' . $products['products_id'] . '&cPath=' . $cPath) . '">
		' . tep_image(DIR_WS_IMAGES . 'icon_status_hidden_light.gif', IMAGE_ICON_STATUS_HIDDEN_LIGHT, 10, 10) . '</a>';
		}
		elseif ($products['products_status'] == '3') 
		{
		echo '
		<a href="' . tep_href_link(FILENAME_BACKUP_CATEGORIES, 'action=setflag&flag=1&pID=' . $products['products_id'] . '&cPath=' . $cPath) . '">'
		. tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) 
		. '</a>';
		// <a href="' . tep_href_link(FILENAME_BACKUP_CATEGORIES, 'action=setflag&flag=0&pID=' . $products['products_id'] . '&cPath=' . $cPath) . '">'
		// . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;'
		// . tep_image(DIR_WS_IMAGES . 'icon_status_hidden.gif', IMAGE_ICON_STATUS_HIDDEN, 10, 10) ;
		}
		elseif ($products['products_status'] == '8') 
		{
		echo '
		<a href="' . tep_href_link(FILENAME_BACKUP_CATEGORIES, 'action=setflag&flag=1&pID=' . $products['products_id'] . '&cPath=' . $cPath) . '">'
		. tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) 
		. '</a>&nbsp;&nbsp;' .'
		<a href="' . tep_href_link(FILENAME_BACKUP_CATEGORIES, 'action=setflag&flag=0&pID=' . $products['products_id'] . '&cPath=' . $cPath) . '">'
		. tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;'
		. tep_image(DIR_WS_IMAGES . 'icon_status_hidden.gif', IMAGE_ICON_STATUS_HIDDEN, 10, 10) ;
		}

	?></td>
	<td style="display:none"></td>
	<td  style="display:none"></td>
    </tr>
<?php
    }

    $cPath_back = '';
    //PHP8
    if (is_array($cPath_array) && ($cPath_array) > 0) 
	{
      for ($i=0, $n=sizeof($cPath_array)-1; $i<$n; $i++) {
        if (empty($cPath_back)) {
          $cPath_back .= $cPath_array[$i];
        } else {
          $cPath_back .= '_' . $cPath_array[$i];
        }
      }
    }

    $cPath_back = (tep_not_null($cPath_back)) ? 'cPath=' . $cPath_back . '&' : '';
?>
              <tr>
                <td colspan="3">
				<table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText"><?php echo TEXT_CATEGORIES . '&nbsp;' . $categories_count . '<br>' . TEXT_PRODUCTS . '&nbsp;' . $products_count; ?></td>
                    <td align="right" class="smallText"><?php if (is_array($cPath_array) && sizeof($cPath_array) > 0) echo '<a href="' . tep_href_link(FILENAME_BACKUP_CATEGORIES, $cPath_back . 'cID=' . $current_category_id) . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a>&nbsp;'; if (!isset($_GET['search'])) 	
					 ?>&nbsp;</td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
<?php
    $heading = array();
    $contents = array();
?>
          </tr>
        </table></td>
      </tr>
    </table>
<?php
  }
?>

    </td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
