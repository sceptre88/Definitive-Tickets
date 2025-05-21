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
	define('HEADING_TITLE', 'Orders Barcode DATA');
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
    //$('#orders_barcode').append('<caption style="caption-side: bottom">A fictional company\'s staff table.</caption>');
 
    $('#orders_barcode').DataTable( {
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
<body  class="orders_barcode" marginwidth="0" marginheight="0" topmargin="0"  leftmargin="0" bgcolor="#FFFFFF">
		
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<?php
		if(SHOW_OSCONCERT_HELP=='yes')
		{
		?>
		<div class="osconcert_message"><?php echo BARCODE_DATA; ?></div>
		<?php
		}
		?>
<!-- header_eof //-->
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
				$barcode_query = tep_db_query("select showtime,products_name,barcode,scanned from orders_barcode ");
				?>
				<table id="orders_barcode" class="display" style="width:100%">
        <thead>
            <tr>
                <!-- <th>Barcode ID</th> 
				<th>Orders ID</th>
				<th>Products ID</th>-->
				<th>Show</th>
				<th>Seat</th>
				<th>Barcode</th>
				<!-- <th>Created</th> -->
				<th>Scanned</th>
				<!-- <th>Scanned Date</th> 
				<th>Location</th>-->
				<!--<th>Data</th> -->
            </tr>
        </thead>
        <tbody>
		<?php
		$num_barcodes = tep_db_num_rows($barcode_query);


	if ($num_barcodes > 0) 
	{
		while ($barcodes = tep_db_fetch_array($barcode_query)) 
		{
			$showtime=$barcodes['showtime'];
			$products_name=$barcodes['products_name'];
			$barcode=$barcodes['barcode'];
			$scanned=$barcodes['scanned'];

			$content .= '
			<tr>
                <td>'.$showtime.'</td>
                <td>'.$products_name.'</td>
                <td>'.$barcode.'</td>
                <td>'.$scanned.'</td>
            </tr>
			';
		}
		
	}
			
		echo $content;	
			?>
        </tbody>
        <tfoot>
            <tr>
                <!-- <th>Barcode ID</th> 
				<th>Orders ID</th>
				<th>Products ID</th>-->
				<th>Show Name</th>
				<th>Seat Name</th>
				<th>Barcode</th>
				<!-- <th>Created</th> -->
				<th>Scanned</th>
				<!-- <th>Scanned Date</th> 
				<th>Location</th>-->
				<!--<th>Data</th> -->
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


<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
