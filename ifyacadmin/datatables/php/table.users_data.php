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
Editor::inst( $db, 'users', 'id' )
	->fields(
		Field::inst( 'id' )->set( false ),
		Field::inst( 'first_name' )
            ->validator( Validate::notEmpty( ValidateOptions::inst()
                ->message( 'A first name is required' ) 
            ) ),
        Field::inst( 'last_name' )
            ->validator( Validate::notEmpty( ValidateOptions::inst()
                ->message( 'A last name is required' )  
            ) ),
		Field::inst( 'email' )
            ->validator( Validate::email( ValidateOptions::inst()
                ->message( 'Please enter an e-mail address' )   
            ) ),
		Field::inst( 'password' )
		->setFormatter( function($val, $data, $opts) {
			return password_hash($val, PASSWORD_DEFAULT, ['cost' => 14]);
		})
		->getFormatter( function ( $val, $data, $opts ) { return null;}),
		Field::inst( 'created' )

	)
	->on( 'preEdit', function ( $e, $id, $values ) {
		if ( $values['password'] === '' ) {
			$e->field( 'password' )->set( false );
		}
		} ) 
	->process( $_POST )
	->json();
