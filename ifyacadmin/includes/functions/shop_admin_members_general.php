<?php
/*

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce
  
  

  Released under the GNU General Public License
  
  Freeway eCommerce from ZacWare
  http://www.openfreeway.org

  Copyright 2007 ZacWare Pty. Ltd
*/
 // Check to ensure this file is included in osConcert!
defined('_FEXEC') or die(); 

function newsdesk_draw_file_field($name, $parameters = '', $required = false) {

$field = tep_draw_input_field($name, '', $parameters, $required, 'file');

return $field;

}

function newsdesk_output_generated_category_path($id, $id1=0) {

$calculated_category_path_string = '';
$last_category=array();
$sql_qu="select admin_groups_name from " . TABLE_ADMIN_GROUPS . " where admin_groups_id='".tep_db_input($id1) ."'";

$last_category_query = tep_db_query($sql_qu);
		$last_category = tep_db_fetch_array($last_category_query);
		
		$calculated_category_path_string=$last_category['admin_groups_name'];

return $calculated_category_path_string;

}


function newsdesk_get_category_tree($parent_id = '0', $spacing = '', $exclude = '', $category_tree_array = '', $include_itself = false) {

global $FSESSION;

if (!is_array($category_tree_array)) $category_tree_array = array();
//if ( (sizeof($category_tree_array) < 1) && ($exclude != '0') ) $category_tree_array[] = array('id' => '0', 'text' => TEXT_TOP);

if ($include_itself) {
	$category_query = tep_db_query("select admin_groups_id,admin_groups_name from " . TABLE_ADMIN_GROUPS ." order by  admin_groups_id");
	$category = tep_db_fetch_array($category_query);
	$category_tree_array[] = array('id' =>$category['admin_groups_id'], 'text' => $category['admin_groups_name']);
	}

$categories_query = tep_db_query("select admin_groups_id,admin_groups_name from " . TABLE_ADMIN_GROUPS ." order by  admin_groups_id");

while ($categories = tep_db_fetch_array($categories_query)) {
	if ($exclude != $categories['admin_groups_id']) $category_tree_array[] = array('id' => $categories['admin_groups_id'], 'text' =>$categories['admin_groups_name']);
		
}

return $category_tree_array;

}


// Count how many products exist in a category
// TABLES: products, products_to_categories, categories
function newsdesk_products_in_category_count($categories_id, $include_deactivated = false) {
// -------------------------------------------------------------------------------------------------------------------------------------------------------------
$products_count = 0;

if ($include_deactivated) {
	$products_query = tep_db_query("select count(*) as total from " . TABLE_ADMIN . " p, " . TABLE_ADMIN_GROUPS . " p2c where p.admin_groups_id = p2c.admin_groups_id and p.admin_groups_id = '" . tep_db_input($categories_id) . "'");
	} else {
	$products_query = tep_db_query("select count(*) as total from " . TABLE_ADMIN . " p, " . TABLE_ADMIN_GROUPS . " p2c where p.admin_groups_id = p2c.admin_groups_id and p.admin_groups_id = '" . tep_db_input($categories_id) . "'");
}

$products = tep_db_fetch_array($products_query);

$products_count += $products['total'];
$childs_query = tep_db_query("select ag.admin_groups_id from " . TABLE_ADMIN . " a,".TABLE_ADMIN_GROUPS." ag where a.admin_groups_id = '" . tep_db_input($categories_id) . "'");
//$childs_query = tep_db_query("select ag.admin_groups_id from " . TABLE_NEWSDESK_CATEGORIES . " where parent_id = '" . tep_db_input($categories_id) . "'");
if (tep_db_num_rows($childs_query)) {
	while ($childs = tep_db_fetch_array($childs_query)) {
		$products_count += newsdesk_products_in_category_count($childs['admin_groups_id'], $include_deactivated);
	}
}

return $products_count;

}


// -------------------------------------------------------------------------------------------------------------------------------------------------------------
// Count how many subcategories exist in a category
// TABLES: categories
function newsdesk_childs_in_category_count($categories_id) {
// -------------------------------------------------------------------------------------------------------------------------------------------------------------
$categories_count = 0;

//$categories_query = tep_db_query("select a.admin_groups_id from " . TABLE_ADMIN . " a,".TABLE_ADMIN_GROUPS." ag where a.admin_groups_id = '" . tep_db_input($categories_id) . "'");
$categories_query = tep_db_query("select count(admin_groups_id) as cate from " . TABLE_ADMIN . " where admin_groups_id = '" . tep_db_input($categories_id) . "'");
if($categories = tep_db_fetch_array($categories_query)) {
	
	$categories_count =$categories['cate'];
}
//echo $categories_counr;
//exit;

return $categories_count;

}


function newsdesk_remove_product($product_id) {
// -------------------------------------------------------------------------------------------------------------------------------------------------------------
tep_db_query("delete from " . TABLE_ADMIN . " where admin_id = '" . tep_db_input($product_id) . "'");


}


function newsdesk_not_null($value) {
if (is_array($value)) {
	if (sizeof($value) > 0) {
		return true;
	} else {
		return false;
	}
} else {
	if (($value != '') && ($value != 'NULL') && (strlen(trim($value)) > 0)) {
		return true;
	} else {
		return false;
	}
}

}

// -----------------------------------------------------------------------
// nl2br >> br2nl ... stripbreaks code found on php.net forum
// -----------------------------------------------------------------------
function stripbr($str) {
$str=eregi_replace('<BR[[:space:]]*/?[[:space:]]*>',"",$str);
return $str;
}
// -----------------------------------------------------------------------


// -----------------------------------------------------------------------
// upload file function (taken from loaded 5)
// -----------------------------------------------------------------------
function tep_get_uploaded_file($filename) {
if (isset($_FILES[$filename])) {
	$uploaded_file = array(
		'name' => $_FILES[$filename]['name'],
		'type' => $_FILES[$filename]['type'],
		'size' => $_FILES[$filename]['size'],
		'tmp_name' => $_FILES[$filename]['tmp_name']
	);
} elseif (isset($GLOBALS['HTTP_POST_FILES'][$filename])) {
	global $HTTP_POST_FILES;

	$uploaded_file = array(
	'name' => $HTTP_POST_FILES[$filename]['name'],
	'type' => $HTTP_POST_FILES[$filename]['type'],
	'size' => $HTTP_POST_FILES[$filename]['size'],
	'tmp_name' => $HTTP_POST_FILES[$filename]['tmp_name']
	);
} else {
	$uploaded_file = array(
		'name' => $GLOBALS[$filename . '_name'],
		'type' => $GLOBALS[$filename . '_type'],
		'size' => $GLOBALS[$filename . '_size'],
		'tmp_name' => $GLOBALS[$filename]
	);
}

	return $uploaded_file;
}
// -----------------------------------------------------------------------


// -----------------------------------------------------------------------
// return a local directory path (without trailing slash)
// -----------------------------------------------------------------------
function tep_get_local_path($path) {
	if (substr($path, -1) == '/') $path = substr($path, 0, -1);
	return $path;
}
// -----------------------------------------------------------------------


// -----------------------------------------------------------------------
// the $filename parameter is an array with the following elements:
// name, type, size, tmp_name
// -----------------------------------------------------------------------
function tep_copy_uploaded_file($filename, $target) {
	if (substr($target, -1) != '/') $target .= '/';
	$target .= $filename['name'];
	move_uploaded_file($filename['tmp_name'], $target);
}
// -----------------------------------------------------------------------


/*

	osCommerce, Open Source E-Commerce Solutions ---- http://www.oscommerce.com
	Copyright (c) 2002 osCommerce
	Released under the GNU General Public License

	IMPORTANT NOTE:

	This script is not part of the official osC distribution but an add-on contributed to the osC community.
	Please read the NOTE and INSTALL documents that are provided with this file for further information and installation notes.

	script name:	NewsDesk
	version:		1.4.5
	date:			2003-08-31
	author:			Carsten aka moyashi
	web site:		www..com

*/
?>
