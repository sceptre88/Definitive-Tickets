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
	define('HEADING_TITLE', 'Products ONLY Data');

	tep_get_last_access_file();

?>
<!DOCTYPE html>
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">

	<script type="text/javascript" language="javascript" src="datatables/js/jquery-3.7.0.js"></script>
	<script type="text/javascript" language="javascript" src="datatables/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" language="javascript" src="datatables/js/dataTables.buttons.min.js"></script>
	<script type="text/javascript" language="javascript" src="datatables/js/jszip.min.js"></script>
	<script type="text/javascript" language="javascript" src="datatables/js/pdfmake.min.js"></script>
	<script type="text/javascript" language="javascript" src="datatables/js/vfs_fonts.js"></script>
	<script type="text/javascript" language="javascript" src="datatables/js/buttons.html5.min.js"></script>
	<script type="text/javascript" language="javascript" src="datatables/js/buttons.print.min.js"></script>
<link rel="stylesheet" type="text/css" href="datatables/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="datatables/css/buttons.dataTables.min.css">
<script>
  $(document).ready(function() {
    var printCounter = 0;
 
    // Append a caption to the table before the DataTables initialisation
    //$('#products_barcode').append('<caption style="caption-side: bottom">A fictional company\'s staff table.</caption>');
 
    $('#products').DataTable( {
        dom: 'Bfrtip',
        buttons: [
            'copy',
            {
                extend: 'excel',
                //messageTop: 'The information in this table is copyright to Sirius Cybernetics Corp.'
            },
            {
                extend: 'pdf',
                messageBottom: null
            },
            {
                extend: 'print',
                messageTop: function () {
                    printCounter++;
 
                    if ( printCounter === 1 ) {
                        return 'This is the first time you have printed this document.';
                    }
                    else {
                        return 'You have printed this document '+printCounter+' times';
                    }
                },
                messageBottom: null
            }
        ]
    } );
} );
</script>
<style type="text/css" class="init">
	
	a.buttons-collection {
		margin-left: 1em;
	}

	</style>
	</head>
<body  class="products_only_data" marginwidth="0" marginheight="0" topmargin="0"  leftmargin="0" bgcolor="#FFFFFF">


<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->
		<?php
		if(SHOW_OSCONCERT_HELP=='yes')
		{
		?>
		<div class="osconcert_message"><?php echo PRODUCTS_DATA; ?></div>
		<?php
		}
		?>
<!-- body //-->
	<br><br>
	<table border="0" width="100%" cellspacing="5" cellpadding="2">
        <tr>
          <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
        </tr>
        <tr>
          <td>
            <table border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr>
                <td class="main"></td>
              </tr>
              <tr>
                <td class="main">
				<?php
				$products_query = tep_db_query("select products_id,products_quantity,products_model,color_code,products_price,products_status,products_ordered,restrict_to_groups,section_id,product_type from products");
				?>
				<table id="products" class="display" style="width:100%">
        <thead>
            <tr>
				<th>ID</th>
						<th>Quantity</th>
						<th>Date ID</th>
						<th>Color Code</th>
						<th>Price</th>
						<th>Status</th>
						<th>Ordered</th>
						<th>Groups</th>
						<th>Section ID</th>
						<th>Type</th>
            </tr>
        </thead>
        <tbody>
		<?php
		$num_products = tep_db_num_rows($products_query);


	if ($num_products > 0) 
	{
		while ($products = tep_db_fetch_array($products_query)) 
		{
			$products_id=$products['products_id'];
			$products_quantity=$products['products_quantity'];
			$products_model=$products['products_model'];
			$color_code=$products['color_code'];
			$products_price=$products['products_price'];
			$products_status=$products['products_status'];
			$products_ordered=$products['products_ordered'];
			$restrict_to_groups=$products['restrict_to_groups'];
			$section_id=$products['section_id'];
			$product_type=$products['product_type'];

			$content .= '
			<tr>
                <td>'.$products_id.'</td>
				<td>'.$products_quantity.'</td>
				<td>'.$products_model.'</td>
				<td>'.$color_code.'</td>
				<td>'.$products_price.'</td>
				<td>'.$products_status.'</td>
				<td>'.$products_ordered.'</td>
				<td>'.$restrict_to_groups.'</td>
				<td>'.$section_id.'</td>
				<td>'.$product_type.'</td>


            </tr>
			';
		}
		
	}
			
		echo $content;	
			?>
        </tbody>
        <tfoot>
            <tr>
               <th>ID</th>
						<th>Quantity</th>
						<th>Date ID</th>
						<th>Color Code</th>
						<th>Price</th>
						<th>Status</th>
						<th>Ordered</th>
						<th>Groups</th>
						<th>Section ID</th>
						<th>Type</th>
            </tr>
        </tfoot>
    </table>
				
   </td>
   </tr>
   </table></td>
  </tr>
</table>
<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->

</body>
</html> 
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
