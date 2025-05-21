
/*
 * Editor client script for DB table language_text
 * Created by http://editor.datatables.net/generator
 */

(function($){
	

	

$(document).ready(function() {
	var editor = new $.fn.dataTable.Editor( {
		ajax: 'datatables/php/table.orders_formdata.php',
		table: '#formdata',
		fields: [
			{
				"label": "Order ID:",
				"name": "orders_id"
			},
			{
				"label": "Passenger:",
				"name": "passenger"
			},
						{
				"label": "Category:",
				"name": "category"
			},
			{
				"label": "Fistname:",
				"name": "firstname"
			},
			{
				"label": "Surname:",
				"name": "surname"
			},
			{
				"label": "Cabin Number:",
				"name": "cabin_number"
			},
			{
				"label": "Sex:",
				"name": "sex"
			},
			{
				"label": "Date of Birth:",
				"name": "dob"
			},
			{
				"label": "Nation CD",
				"name": "nation_cd"
			},
			{
				"label": "Passport Number:",
				"name": "passport_number"
			},
			{
				"label": "Passport (Date of Issue):",
				"name": "passport_doi"
			},
			{
				"label": "Date Valid:",
				"name": "date_valid"
			},
			{
				"label": "National ID:",
				"name": "national_id"
			},
			{
				"label": "Seating Preference:",
				"name": "seating_pref"
			},
			{
				"label": "Place of birth:",
				"name": "place_of_birth"
			},
			{
				"label": "Passport Country of Issue:",
				"name": "passport_country_issue"
			},
			{
				"label": "Emergency Phone:",
				"name": "emerg_phone"
			},
			{
				"label": "Emergency Name:",
				"name": "emerg_name"
			},
			{
				"label": "Bed Size:",
				"name": "bed_size"
			}

		]
	} );
	

	

	var table = $('#formdata').DataTable( {
		dom: 'Bfrtip',
		ajax: 'datatables/php/table.orders_formdata.php',
		columns: [
			{
				"data": "orders_id"
			},
			{
				"data": "passenger",
				"visible": true
			},
			{
				"data": "category"
			},
			{
				"data": "firstname"
			},
			{
				"data": "surname"
			},
			{
				"data": "cabin_number"
			},
			{
				"data": "sex",
				"visible": false
				
			},
			{
				"data": "dob",
				"visible": false
			},
			{
				"data": "nation_cd",
				"visible": false
			},
			{
				"data": "passport_number",
				"visible": false
			},
			{
				"data": "passport_doi",
				"visible": false
			},
			{
				"data": "date_valid",
				"visible": false
			},
			{
				"data": "national_id",
				"visible": false
			},
			{
				"data": "seating_pref",
				"visible": false
			},
			{
				"data": "place_of_birth",
				"visible": false
			},
			{
				"data": "passport_country_issue",
				"visible": false
			},
			{
				"data": "emerg_phone",
				"visible": false
			},
			{
				"data": "emerg_name",
				"visible": false
			},
			{
				"data": "bed_size",
				"visible": false
			}
		],
		select: true,
		lengthChange: false,

		buttons: [
			//{ extend: 'create', editor: editor },
			{ extend: 'edit',   editor: editor },
			//{ extend: 'remove', editor: editor },
			 'pageLength',
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
		
		
		
		
	} );
	
	
} );



}(jQuery));

