<html>

<head>
	<title>BarCode Scan</title>
	<meta http-equiv="pragma" content="no-cache">
	<meta http-equiv="expires" content="0">
	<meta content="width=device-width; initial-scale=1.0; maximum-scale=4.0; user-scalable=1;" name="viewport" >
	<STYLE TYPE="text/css">
		<!--
    	/* Text and background color for light mode */
    	body {
	        color: #333;
			background-image: url("logo-onwhite.png");
			background-repeat: no-repeat;
			height: 500px;
			background-position: 50% 0%;
			background-size: contain;
	    	}
		div {
			
		}
		/* Text and background color for dark mode */
		@media (prefers-color-scheme: dark) {
	    	body {
	        	color: #ddd;
	        	background-color: #222;
				background-image: url("logo-ondark.png");
				background-repeat: no-repeat;
				height: 500px;
				background-position: 50% 0%;
				background-size: contain;
	    	}
			div {
					
			}
		}
		-->
		.btn_scan {
	  		display: block;
	  		width: 100%;
	  		border: none;
	  		background-color: #1600DD;
	  		color: white;
	  		padding: 14px 28px;
	  		font-size: 50px;
			cursor: pointer;
	  		text-align: center;
	  		position: fixed;
	  		bottom: 0;
			right: 0;
			width: 100%;
			height: 30%;
		}

	</STYLE>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script type="text/javascript">
		<!-- called when a scan has been completed -->
			function onscan(bardata){
				/*alert( "Barcode result : " + bardata ); */
				jQuery.ajax({
					type: "POST",
					url: "https://ifyac.definitivetickets.com/scanner2.php?barcode=" + bardata,
					success: function(resultData) {$("div").html(resultData); }
				})
			}
		<!-- called when user from HTML page click on a scanner button, it will start the hardware/camera scanner on iOS -->
			function startscan(barfield){
    			window.location = "readbarcode://"+barfield;
			}

	</script>
</head>

<body>
	<div></div>
	<center>
		<button onclick="startscan('barcodefield1')" class="btn_scan">Scan Ticket</button>
	</center>
</body>
</html>