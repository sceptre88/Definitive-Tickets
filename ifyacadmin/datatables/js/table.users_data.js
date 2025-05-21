var editor; // use a global for the submit and return data rendering
 
$(document).ready(function() {
    editor = new $.fn.dataTable.Editor( {
        ajax: 'datatables/php/table.users_data.php',
        table: "#users",
		fields: [
			
			{
				"label": "First Name:",
				"name": "first_name"
			},	
			{
				"label": "Last Name:",
				"name": "last_name"
			},
			{
				"label": "Email:",
				"name": "email"
			},
			{
				"label": "Password:",
				"name": "password"
			},
			{
                label: 'Created:',
                name: 'created',
                type: 'datetime',
                // def: function () { return new Date(); },
                // displayFormat: 'dddd D MMMM YYYY',
                // wireFormat: 'YYYY-MM-DD',
            }

		]
	} );
	
 
    	var table = $('#users').DataTable( {
        lengthChange: false,
        ajax: 'datatables/php/table.users_data.php',
        columns: [
			{ data: "id",
				"visible": true },
			 { data: null, render: function ( data, type, row ) {
                // Combine the first and last names into a single table field
                return data.first_name+' '+data.last_name;
            } },
			{ data: "email",
				"visible": true },
			{ data: "password",
				"visible": false },
			{ data: "created",
				"visible": true }
		],
        select: true,
		"pagingType": "simple",
		'paging': false,
		"searching": false,
		"info": false
    } );
 
    // Display the buttons
    new $.fn.dataTable.Buttons( table, [
        { extend: "create", editor: editor },
        { extend: "edit",   editor: editor },
        { extend: "remove", editor: editor }
    ] );
	
		
    table.buttons().container()
        .appendTo( $('.col-md-6:eq(0)', table.table().container() ) );
} );