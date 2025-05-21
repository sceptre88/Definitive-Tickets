
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
		ajax: 'datatables/php/table.categories_data.php',
		table: '#categories',
		fields: [
			{
				"label": "Categories ID:",
				"name": "categories.categories_id",
				"type": "readonly"
			},
			{
				"label": "Quantity:",
				"name": "categories.categories_quantity"
			},	
			{
				"label": "Date ID:",
				"name": "categories.date_id"
			},
			{
				"label": "ColorCode:",
				"name": "categories.color_code"
			},
			{
				"label": "Image:",
				"name": "categories.categories_image"
			},
			{
				"label": "Status:",
				"name": "categories.categories_status"
			},
			{
				"label": "Quantity Remaining:",
				"name": "categories.categories_quantity_remaining",
				"type": "readonly"
			},
			{
				"label": "GA:",
				"name": "categories.categories_GA"
			},
			{
				"label": "Section ID:",
				"name": "categories.section_id",
				"type": "readonly"
			},
			{
				"label": "Date:",
				"name": "categories_description.concert_date",
				"type": "readonly"
			},
			{
				"label": "Name:",
				"name": "categories_description.categories_name"
			},
			{
				"label": "Time:",
				"name": "categories_description.concert_time"
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
	
		$('#categories').DataTable( {
		dom: "Bfrtip",
		ajax: {
			url: "datatables/php/table.categories_data.php",
			type: 'POST'
		},
		"pageLength": 100,
		columns: [
			{ data: "categories.categories_id",
				"visible": true },
			{ data: "categories.categories_quantity",
				"visible": true },
			{ data: "categories.date_id",
				"visible": true },
			{ data: "categories.color_code",
				"visible": true },
			{ data: "categories.categories_image",
				"visible": true },
			{ data: "categories.categories_status",
				"visible": true },
			{ data: "categories.categories_quantity_remaining",
				"visible": true },
			{ data: "categories.categories_GA",
				"visible": true },
			{ data: "categories.section_id",
				"visible": true },
			{ data: "categories_description.concert_date",
				"visible": true },
			{ data: "categories_description.categories_name",
				"visible": true },
			{ data: "categories_description.concert_time",
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

