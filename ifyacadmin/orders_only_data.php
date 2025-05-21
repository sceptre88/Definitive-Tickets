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
	define('HEADING_TITLE', 'Orders TABLE ONLY Data');

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
    //$('#orders_barcode').append('<caption style="caption-side: bottom">A fictional company\'s staff table.</caption>');
 
    $('#orders').DataTable( {
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
<body  class="orders_only_data" marginwidth="0" marginheight="0" topmargin="0"  leftmargin="0" bgcolor="#FFFFFF">


<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->
		<?php
		if(SHOW_OSCONCERT_HELP=='yes')
		{
		?>
		<div class="osconcert_message"><?php echo ORDERS_DATA; ?></div>
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
				$orders_query = tep_db_query("select orders_id,date_purchased,customers_name,customers_company,customers_street_address,customers_city,customers_postcode,customers_state,customers_country,customers_telephone,customers_email_address from orders");
				?>
				<table id="orders" class="display" style="width:100%">
        <thead>
            <tr>
				<th>ID</th>
						<th>Date</th>
						<th>Name</th>
						<th>Company</th>
						<th>Address</th>
						<th>City</th>
						<th>Zip</th>
						<th>State</th>
						<th>Country</th>
						<th>Phone</th>
						<th>Email</th>
            </tr>
        </thead>
        <tbody>
		<?php
		$num_orders = tep_db_num_rows($orders_query);


	if ($num_orders > 0) 
	{
		while ($orders = tep_db_fetch_array($orders_query)) 
		{
			$orders_id=$orders['orders_id'];
			$date_purchased=$orders['date_purchased'];
			$customers_name=$orders['customers_name'];
			$customers_company=$orders['customers_company'];
			$customers_street_address=$orders['customers_street_address'];
			$customers_city=$orders['customers_city'];
			$customers_postcode=$orders['customers_postcode'];
			$customers_state=$orders['customers_state'];
			$customers_country=$orders['customers_country'];
			$customers_telephone=$orders['customers_telephone'];
			$customers_email=$orders['customers_email_address'];


			$content .= '
			<tr>
                <td>'.$orders_id.'</td>
                <td>'.$date_purchased.'</td>
                <td>'.$customers_name.'</td>
				<td>'.$customers_company.'</td>
				<td>'.$customers_street_address.'</td>
				<td>'.$customers_city.'</td>
				<td>'.$customers_postcode.'</td>
				<td>'.$customers_state.'</td>
				<td>'.$customers_country.'</td>
				<td>'.$customers_telephone.'</td>
				<td>'.$customers_email.'</td>

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
						<th>Date</th>
						<th>Name</th>
						<th>Company</th>
						<th>Address</th>
						<th>City</th>
						<th>Zip</th>
						<th>State</th>
						<th>Country</th>
						<th>Phone</th>
						<th>Email</th>
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
