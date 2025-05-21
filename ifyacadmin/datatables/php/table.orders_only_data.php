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

// The following statement can be removed after the first run (i.e. the database
// table has been created). It is a good idea to do this to help improve
// performance.


// Build our Editor instance and process the data coming from _POST
Editor::inst( $db, 'orders', 'orders_id' )
	->fields(
		Field::inst( 'orders_id' ),
		Field::inst( 'date_purchased' )
		->getFormatter( Format::dateSqlToFormat( 'd-m-Y' ) )
		->setFormatter( Format::dateFormatToSql( 'd-m-Y' ) ),
		Field::inst( 'customers_name' ),
		Field::inst( 'customers_company' ),
		Field::inst( 'customers_street_address' ),
		Field::inst( 'customers_city' ),
		Field::inst( 'customers_postcode' ),
		Field::inst( 'customers_state' ),
		Field::inst( 'customers_country' ),
		Field::inst( 'customers_telephone' ),
		Field::inst( 'customers_email_address' )

	)
	->process( $_POST )
	->json();
