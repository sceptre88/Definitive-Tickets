<?php

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


// Build our Editor instance and process the data coming from _POST
Editor::inst( $db, 'pages', 'id' )
	->fields(
		Field::inst( 'id' )->set( false ),
		Field::inst( 'status' ),
		Field::inst( 'page' )
            ->validator( Validate::notEmpty( ValidateOptions::inst()
                ->message( 'An seo friendly page name is required' ) 
            ) ),
			
        Field::inst( 'page_data' ),
		Field::inst( 'headline' ),
		Field::inst( 'hookline' ),
		Field::inst( 'description' ),
		Field::inst( 'affiliate_link' ),
		Field::inst( 'meta_title' ),
		Field::inst( 'meta_desc' ),
		Field::inst( 'meta_keys' ),
		Field::inst( 'created' ),
		Field::inst( 'image' ),
		Field::inst( 'sort_order' ),
		Field::inst( 'flag' )

	)
	->process( $_POST )
	->json();