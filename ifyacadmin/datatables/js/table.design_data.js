
(function($){
	
// Use a global for the submit and return data rendering in the examples.
// Don't do this outside of the Editor examples!
var editor;

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
	editor = new $.fn.dataTable.Editor( {
		ajax: 'datatables/php/table.design_data.php',
		table: '#products',
		fields: [
			{
				"label": "Products ID:",
				"name": "products.products_id",
				"type": "readonly"
			},
			{
				"label": "NumberText:",
				"name": "products_description.products_number"
			},
			{
				"label": "Name:",
				"name": "products_description.products_name"
			},	
			{
				"label": "X:",
				"name": "products.products_x"
			},
			{
				"label": "Y:",
				"name": "products.products_y"
			},
			{
				"label": "Width:",
				"name": "products.products_w"
			},
			{
				"label": "Height:",
				"name": "products.products_h"
			},
			{
				"label": "Status:",
				"name": "products.products_status"
			},
			{
				"label": "Rotate:",
				"name": "products.products_r"	
			},
			{
				"label": "Scale:",
				"name": "products.products_sx"
			},
			{
				"label": "Scale:",
				"name": "products.products_sy"
			},
			{
				"label": "ColorCode:",
				"name": "products.color_code"
			},
			{
				"label": "Section ID:",
				"name": "products.section_id",
				"type": "readonly"
			},
			{
				"label": "Type:",
				"name": "products.product_type",
				"type": "readonly"
			}

		]
	} );
	
//Upload Editor - triggered from the import button. Used only for uploading a file to the browser
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
	
	
		$('#products').DataTable( {
		dom: "Bfrtip",
		ajax: {
			url: "datatables/php/table.design_data.php",
			type: 'POST'
		},
		"pageLength": 100,
		columns: [
			{ data: "products.products_id",
				"visible": true },
			{ data: "products_description.products_number",
				"visible": true },
			{ data: "products_description.products_name",
				"visible": true },
			{ data: "products.products_x",
				"visible": true },
			{ data: "products.products_y",
				"visible": true },
			{ data: "products.products_w",
				"visible": true },
			{ data: "products.products_h",
				"visible": true },
			{ data: "products.products_status",
				"visible": true },
			{ data: "products.products_r",
				"visible": true },
			{ data: "products.products_sx",
				"visible": true },
			{ data: "products.products_sy",
				"visible": true },
			{ data: "products.color_code",
				"visible": true },
			{ data: "products.section_id",
				"visible": true },
			{ data: "products.product_type",
				"visible": true }
		],
		select: true,
		// lengthChange: false,
		// buttons: [
			// { extend: "create", editor: editor },
			// { extend: "edit",   editor: editor },
			// { extend: "remove", editor: editor }
		// ]
		
		buttons: [
			//{ extend: 'create', editor: editor },
			{ extend: 'edit',   editor: editor },
			//{ extend: 'remove', editor: editor },
			'pageLength',
			//Cannot Be Used For Linked Tables Aug 2019
			// {
				// extend: 'csv',
				// text: 'Export CSV',
				// className: 'btn-space',
				// exportOptions: {
					// orthogonal: null
				// }
			// },
			// {
				// text: 'Import CSV',
				// action: function () {
					// uploadEditor.create( {
						// title: 'CSV file import'
					// } );
				// }
			// },
			//EOF Cannot Be Used For Linked Tables Aug 2019
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

//EXPORT BUTTONS
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
//EOF EXPORT BUTTONS
		
	} );

		
		
		

	} );
	

}(jQuery));

