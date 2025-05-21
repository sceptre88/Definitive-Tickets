var editor; // use a global for the submit and return data rendering in the examples
 
$(document).ready(function() {
    editor = new $.fn.dataTable.Editor( {
        ajax: 'datatables/php/table.page_data.php',
        table: "#pages",
			fields: [
			{
			label:"Active:",
			name:"status",
			type:"checkbox",
			separator: "|",
			options:[
			{ label: '', value: 1 }
			]
			}, 
			{
				"label": "Page Name:",
				"name": "page"
			},	
			{
				"label": "Page Data:",
				"name": "page_data",
				"type":  "textarea"
				//"type": "tinymce",
				//"opts": {
				//skin : 'lightgray',
				// additional options if required...
			},
			{
				"label": "Headline:",
				"name": "headline"
			},
			{
				"label": "Hookline:",
				"name": "hookline"
			},
			{
				"label": "Description:",
				"name": "description",
				"type":  "textarea"
			},
			{
				"label": "Link:",
				"name": "affiliate_link"
			},
			{
				"label": "Meta Title:",
				"name": "meta_title"
			},
			{
				"label": "Meta Description:",
				"name": "meta_desc",
				"type":  "textarea"
			},
			{
				"label": "Meta Keywords:",
				"name": "meta_keys"
			},
			{
				"label": "Image:",
				"name": "image"
			},
			{
				"label": "Sort Order:",
				"name": "sort_order"
			},
			{
				"label": "Flag:",
				"name": "flag"
			}

		]
	} );

    	var table = $('#pages').DataTable( {
        lengthChange: false,
		    order: [[0, 'desc']],
        ajax: 'datatables/php/table.page_data.php',
        columns: [
			{ data: "id",
				"visible": true },
			{ data: "status",
				"visible": true },
			{ data: "page",
				"visible": true },
			{ data: "page_data",
				"visible": false },
			{ data: "headline",
				"visible": true },
			{ data: "hookline",
				"visible": false },
			{ data: "description",
				"visible": false },
			{ data: "affiliate_link",
				"visible": false },
			{ data: "meta_title",
				"visible": false },
			{ data: "meta_desc",
				"visible": false },
			{ data: "meta_keys",
				"visible": false },
			{ data: "created",
				"visible": true },
			{ data: "image",
				"visible": false },
			{ data: "sort_order",
				"visible": false },
			{ data: "flag",
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