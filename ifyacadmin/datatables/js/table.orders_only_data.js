
(function($){
	
// Use a global for the submit and return data rendering in the examples.
// Don't do this outside of the Editor examples!
//var editor;


// Display an Editor form that allows the user to pick the CSV data to apply to each column
function selectColumns ( editor, csv, header ) {
	var selectEditor = new $.fn.dataTable.Editor();
	var fields = editor.order();

	for ( var i=0 ; i<fields.length ; i++ ) {
		var field = editor.field( fields[i] );

		selectEditor.add( {
			label: field.label(),
			name: field.name(),
			type: 'select',
			options: header,
			def: header[i]
		} );
	}

	selectEditor.create({
		title: 'Map CSV fields',
		buttons: 'Import '+csv.length+' records',
		message: 'Select the CSV column you want to use the data from for each field.'
	});

	selectEditor.on('submitComplete', function (e, json, data, action) {
		// Use the host Editor instance to show a multi-row create form allowing the user to submit the data.
		editor.create( csv.length, {
			title: 'Confirm import',
			buttons: 'Submit',
			message: 'Click the <i>Submit</i> button to confirm the import of '+csv.length+' rows of data. Optionally, override the value for a field to set a common value by clicking on the field below.'
		} );

		for ( var i=0 ; i<fields.length ; i++ ) {
			var field = editor.field( fields[i] );
			var mapped = data[ field.name() ];

			for ( var j=0 ; j<csv.length ; j++ ) {
				field.multiSet( j, csv[j][mapped] );
			}
		}
	} );
}




$(document).ready(function() {
	// Regular editor for the table
		editor = new $.fn.dataTable.Editor( {
		ajax: 'datatables/php/table.orders_only_data.php',
		
		table: '#orders',
		fields: [
			{
				"label": "ID:",
				"name": "orders_id",
				"type": "readonly"
			},
			{
				"label": "Date:",
				"name": "date_purchased",
				"type": "readonly"
			},
			{
				"label": "Name:",
				"name": "customers_name",
				"type": "readonly"
			},
			
			{
				"label": "Company:",
				"name": "customers_company",
				"type": "readonly"
			},
						{
				"label": "Address:",
				"name": "customers_street_address",
				"type": "readonly"
			},
			{
				"label": "City:",
				"name": "customers_city",
				"type": "readonly"
			},
			{
				"label": "Zip:",
				"name": "Customers_postcode",
				"type": "readonly"
				
			},
			{
				"label": "State:",
				"name": "customers_state",
				"type": "readonly"
			},
			{
				"label": "Country:",
				"name": "customers_country",
				"type": "readonly"
			},
			{
				"label": "Phone:",
				"name": "customers_telephone",
				"type": "readonly"
			},
			{
				"label": "Email",
				"name": "customers_email",
				"type": "readonly"
			}

		]
	} );
	
// Upload Editor - triggered from the import button. Used only for uploading a file to the browser
	var uploadEditor = new $.fn.dataTable.Editor( {
		fields: [ {
			label: 'CSV file:',
			name: 'csv',
			type: 'upload',
			ajax: function ( files ) {
				// Ajax override of the upload so we can handle the file locally. Here we use Papa
				// to parse the CSV.
				Papa.parse(files[0], {
					header: true,
					skipEmptyLines: true,
					complete: function (results) {
						if ( results.errors.length ) {
							console.log( results );
							uploadEditor.field('csv').error( 'CSV parsing error: '+ results.errors[0].message );
						}
						else {
							uploadEditor.close();
							selectColumns( editor, results.data, results.meta.fields );
						}
					}
				});
			}
		} ]
	} );
	


  

	

	var table = $('#orders').DataTable( {
		
		dom: 'Bfrtip',
		ajax: 'datatables/php/table.orders_only_data.php',
		//"pageLength": 100,
		//"orderCellsTop": true,
		//"fixedHeader": true,

		columns: [
			{
				"data": "orders_id",
				"visible": true
			},
			{
				"data": "date_purchased",
				"visible": true
			},
			{
				"data": "customers_name",
				"visible": true
			},
			{
				"data": "customers_company",
				"visible": false
			},
			{
				"data": "customers_street_address",
				"visible": true
			},
			{
				"data": "customers_city",
				"visible": true
			},
			{
				"data": "customers_postcode",
				"visible": true
			},
			{
				"data": "customers_state",
				"visible": true
				
			},
			{
				"data": "customers_country",
				"visible": true
			},
			{
				"data": "customers_telephone",
				"visible": true
			},
			{
				"data": "customers_email_address",
				"visible": true
			}
		],
		select: true,
		lengthChange: false,
		
		buttons: [
			//{ extend: 'create', editor: editor },
			//{ extend: 'edit',   editor: editor },
			//{ extend: 'remove', editor: editor },
			'pageLength',
			{
				extend: 'csv',
				text: 'Export CSV',
				className: 'btn-space',
				exportOptions: {
					orthogonal: null
				}
			},
			// {
				// text: 'Import CSV',
				// action: function () {
					// uploadEditor.create( {
						// title: 'CSV file import'
					// } );
				// }
			// },
			{
            	extend: 'selectAll',
				className: 'btn-space'
			},
            'selectNone',
			{
				extend: 'collection',
				text: 'Export',
				buttons: [
					'copy',
					'excel',
					'csv',
					'pdf',
					'print'
				]
			 }
		]

		// buttons: [
			// //{ extend: 'create', editor: editor },
			// { extend: 'edit',   editor: editor },
			// //{ extend: 'remove', editor: editor },
			 // 'pageLength',
			// {
				// extend: 'collection',
				// text: 'Export',
				// buttons: [
					// 'copy',
					// 'excel',
					// 'csv',
					// 'pdf',
					// 'print'
				// ]
			// }
		// ]

		

	} );
	
	//DOES NOT WORK PERFECTLY
	// $(document).ready(function() {
		
    // // Setup - add a text input to each footer cell
    // $('#orders thead tr').clone(true).appendTo( '#orders thead' );
    // $('#orders thead tr:eq(1) th').each( function (i) {
        // var title = $(this).text();
        // $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
 
        // $( 'input', this ).on( 'keyup change', function () {
            // if ( table.column(i).search() !== this.value ) {
                // table
                    // .column(i)
                    // .search( this.value )
                    // .draw();
            // }
        // } );
    // } );
	
// } );
	
	
} );



}(jQuery));

