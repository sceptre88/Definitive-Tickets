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
	
Editor::inst( $db, 'categories_description', 'categories_id' )
	->fields(
		Field::inst( 'categories.categories_id' ),
		Field::inst( 'categories.categories_quantity' ),
		Field::inst( 'categories.date_id' ),
		Field::inst( 'categories.color_code' ),
		Field::inst( 'categories.categories_image' ),
		Field::inst( 'categories.categories_status' ),
		Field::inst( 'categories.categories_quantity_remaining' ),
		Field::inst( 'categories.categories_GA' ),
		Field::inst( 'categories.section_id' ),
		Field::inst( 'categories_description.concert_date' ),
        Field::inst( 'categories_description.categories_id' ),
        Field::inst( 'categories_description.categories_name' ),
        Field::inst( 'categories_description.concert_time' )
        // Field::inst( 'categories.categories_model' )
			//->getFormatter( Format::dateSqlToFormat('Y-m-d H:i' ) ),
        // Field::inst( 'categories.categories_status' )
			// ->setFormatter( function ( $val, $data, $opts ) {
                // return ! $val ? 0 : 1;
			// } )
    )
    ->leftJoin( 'categories', 'categories.categories_id', '=', 'categories_description.categories_id' )
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
// Editor::inst( $db, 'categories', 'categories_id' )
	// ->fields(
		// Field::inst( 'categories_id' ),
		// Field::inst( 'categories_quantity' ),
		// Field::inst( 'categories_model' ),
		// Field::inst( 'color_code' ),
		// Field::inst( 'categories_price' ),
		// Field::inst( 'categories_status' ),
		// Field::inst( 'categories_ordered' ),
		// Field::inst( 'restrict_to_groups' ),
		// Field::inst( 'section_id' ),
		// Field::inst( 'product_type' )

	// )
	// ->process( $_POST )
	// ->json();
