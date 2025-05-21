var editor; // use a global for the submit and return data rendering
 
$(document).ready(function() {
    editor = new $.fn.dataTable.Editor( {
   ajax: 'datatables/php/table.brands_data.php',
   table: "#brands",
			fields: [
			{
			label:"Approved:",
			name:"status",
			type:"checkbox",
			separator:"|",
			options:[
			{ label: '', value: 1 }
			]
			},
			{
				"label": "Website:",
				"name": "link"
			},
			{
				"label": "Brand Name:",
				"name": "brand_name"
			},
			{
				"label": "Brand Description:",
				"name": "brand_description"
			},
			{
				"label": "Brand Image:",
				"name": "brand_image"
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
	
var table = $('#brands').DataTable( {
   lengthChange: false,
   ajax: 'datatables/php/table.brands_data.php',
   columns: [
			{ data: "id",
				"visible": true },
			{ data: "status",
				"visible": true },
			{ data: "link",
				"visible": true },
			{ data: "brand_name",
				"visible": true },
			{ data: "brand_description",
				"visible": true },
			{ data: "brand_image",
				"visible": true },
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