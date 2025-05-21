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
	
Editor::inst( $db, 'products_description', 'products_id' )
	->fields(
		Field::inst( 'products.products_id' ),
		Field::inst( 'products.products_quantity' ),
		Field::inst( 'products.products_model' ),
		Field::inst( 'products.color_code' ),
		Field::inst( 'products.products_price' ),
		Field::inst( 'products.products_status' ),
		Field::inst( 'products.products_sku' ),
		Field::inst( 'products.products_ordered' ),
		Field::inst( 'products.restrict_to_groups' ),
		Field::inst( 'products.section_id' ),
		Field::inst( 'products.product_type' ),
        Field::inst( 'products_description.products_id' ),
        Field::inst( 'products_description.products_name' )
        // Field::inst( 'products.products_model' )
			//->getFormatter( Format::dateSqlToFormat('Y-m-d H:i' ) ),
        // Field::inst( 'products.products_status' )
			// ->setFormatter( function ( $val, $data, $opts ) {
                // return ! $val ? 0 : 1;
			// } )
    )
    ->leftJoin( 'products', 'products.products_id', '=', 'products_description.products_id' )
    ->process($_POST)
    ->json();

// Alias Editor classes so they are easy to use
// use
	// DataTables\Editor,
	// DataTables\Editor\Field,
	// DataTables\Editor\Format,
	// DataTables\Editor\Mjoin,
	// DataTables\Editor\Options,
	// DataTables\Editor\Upload,
	// DataTables\Editor\Validate,
	// DataTables\Editor\ValidateOptions;

// // The following statement can be removed after the first run (i.e. the database
// // table has been created). It is a good idea to do this to help improve
// // performance.


// // Build our Editor instance and process the data coming from _POST
// Editor::inst( $db, 'products', 'products_id' )
	// ->fields(
		// Field::inst( 'products_id' ),
		// Field::inst( 'products_quantity' ),
		// Field::inst( 'products_model' ),
		// Field::inst( 'color_code' ),
		// Field::inst( 'products_price' ),
		// Field::inst( 'products_status' ),
		// Field::inst( 'products_ordered' ),
		// Field::inst( 'restrict_to_groups' ),
		// Field::inst( 'section_id' ),
		// Field::inst( 'product_type' )

	// )
	// ->process( $_POST )
	// ->json();
