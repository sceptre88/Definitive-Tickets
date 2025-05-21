<?php

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
Editor::inst( $db, 'brands', 'id' )
	->fields(
		Field::inst( 'id' )->set( false ),
		Field::inst( 'status' ),
		Field::inst( 'link' ),
		Field::inst( 'brand_name' )
            ->validator( Validate::notEmpty( ValidateOptions::inst()
                ->message( 'A Brand Name is required' ) 
            ) ),
        Field::inst( 'brand_description' ),
		Field::inst( 'brand_image' )
            ->validator( Validate::notEmpty( ValidateOptions::inst()
                ->message( 'A Brand Image is required' ) 
            ) ),
		Field::inst( 'created' )
	)

	->process( $_POST )
	->json();
	
	
//->validator( Validate::notEmpty( ValidateOptions::inst()
               // ->message( 'A title is required' )  
           // ) )