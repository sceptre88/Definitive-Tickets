<?php
define( '_FEXEC', 1 );
/*
 * Editor server script for DB table language_text
 * Created by http://editor.datatables.net/generator
 */

// DataTables PHP library and database connection
include( "../lib/DataTables.php" );


// Alias Editor classes so they are easy to use
use
	DataTables\Editor,
	DataTables\Editor\Field,
	DataTables\Editor\Format,
	DataTables\Editor\Mjoin,
	DataTables\Editor\Options,
	DataTables\Editor\Upload,
	DataTables\Editor\Validate,
	DataTables\Editor\ValidateOptions;
	
Editor::inst( $db, 'orders_barcode', 'barcode_id' )
	->fields(
		Field::inst( 'showtime' ),
		Field::inst( 'products_name' ),
		Field::inst( 'barcode' ),
		Field::inst( 'scanned' )
    )

	->process( $_POST )
	->json();
