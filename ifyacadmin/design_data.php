<?php
	
	/*
		
		https://www.osconcert.com
		
		
		
		Released under the GNU General Public License
		
		Freeway eCommerce from ZacWare
		http://www.openfreeway.org
		
		Copyright 2007 ZacWare Pty. Ltd
	*/
	// Set flag that this is a parent file
	define( '_FEXEC', 1 );
	require('includes/application_top.php');
	define('HEADING_TITLE', 'Design Data');

?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>

	<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
	
	<link rel="stylesheet" type="text/css" href="datatables/css/jquery.dataTables.min.css">
	<link rel="stylesheet" type="text/css" href="datatables/css/editor.dataTables.min.css">
	<link rel="stylesheet" type="text/css" href="datatables/css/select.dataTables.min.css">
	<link rel="stylesheet" type="text/css" href="datatables/css/dataTables.dateTime.min.css">
	<link rel="stylesheet" type="text/css" href="datatables/css/buttons.dataTables.min.css">
	<link rel="stylesheet" type="text/css" href="datatables/css/colReorder.dataTables.min.css">
	<link rel="stylesheet" type="text/css" href="datatables/css/shCore.css">
	<link rel="stylesheet" type="text/css" href="datatables/css/demo.css">
	<style type="text/css" class="init">
	
	a.buttons-collection {
		margin-left: 1em;
	}

	</style>

	<script type="text/javascript" language="javascript" src="datatables/js/jquery-3.5.1.js"></script>
	<script type="text/javascript" language="javascript" src="datatables/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" language="javascript" src="datatables/js/dataTables.editor.min.js"></script>
	<script type="text/javascript" language="javascript" src="datatables/js/dataTables.buttons.min.js"></script>
	<script type="text/javascript" language="javascript" src="datatables/js/dataTables.select.min.js"></script>
	<script type="text/javascript" language="javascript" src="datatables/js/dataTables.select.min.js"></script>
	<script type="text/javascript" language="javascript" src="datatables/js/dataTables.dateTime.min.js"></script>
	<script type="text/javascript" language="javascript" src="datatables/js/dataTables.colReorder.min.js"></script>
	<script type="text/javascript" language="javascript" src="datatables/js/vfs_fonts.js"></script>
	<script type="text/javascript" language="javascript" src="datatables/js/buttons.html5.min.js"></script>
	<script type="text/javascript" charset="utf-8" src="datatables/js/table.design_data.js"></script>

<style type="text/css" class="init">
	
	a.buttons-collection {
		margin-left: 1em;
	}
		button.btn-space {
		margin-left: 1em;
	}

	</style>
	</head>
<body  class="design_data" marginwidth="0" marginheight="0" topmargin="0"  leftmargin="0" bgcolor="#FFFFFF">

<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->
		<?php
		if(SHOW_OSCONCERT_HELP=='yes')
		{
		?>
		<div class="osconcert_message"><?php echo DESIGN_DATA; ?></div>
		<?php
		}
		?>
<!-- body //-->
	<div class="container-fluid" style="padding:10px">
		<?php
		if(SHOW_ADMIN_DATATOOLS=='yes')
		{
		?>
        <div>
		<a class="btn btn-primary" href="design_data.php"><h3>Design Mode Data</h3></a>
		<a class="btn btn-primary" href="categories_data.php"><h3>Categories Data</h3></a>
		<a class="btn btn-primary" href="products_data.php"><h3>Products Data</h3></a>
		<a class="btn btn-primary" href="products_only_data.php"><h3>Products TABLE Data</h3></a>
		<a class="btn btn-primary" href="orders_only_data.php"><h3>Orders TABLE Data</h3></a>
		</div><br>
		<?php
		}
		?>

			<table cellpadding="0" cellspacing="0" border="0" class="display" id="products" width="100%">
				<thead>
					<tr>
						<th>Product ID</th>
						<th>Number</th>
						<th>Name</th>
						<th>X</th>
						<th>Y</th>
						<th>Width</th>
						<th>Height</th>
						<th>Status</th>
						<th>Rotate</th>
						<th>Scale X</th>
						<th>Scale Y</th>
						<th>Color Code</th>
						<th>Section ID</th>
						<th>Type</th>	
					
					</tr>
				</thead>
			</table>

			</div>
<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->

</body>
</html> 
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
