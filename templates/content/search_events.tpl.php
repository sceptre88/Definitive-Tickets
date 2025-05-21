<?php 
/*
	Freeway eCommerce
	http://www.openfreeway.org
	Copyright (c) 2007 ZacWare

	Released under the GNU General Public License 
*/	
// Check to ensure this file is included in osConcert!
defined('_FEXEC') or die();
?>
	<div class="section-header">
	<h2><?php echo HEADING_TITLE; ?></h2>
	</div>


<?php 

################### start of actual code that may be needed to transfer to infobox

#### if the language file has not been included then you may need to define some of the  
#### language files 

if (!defined('TEXT_NO_FEATURED_CATEGORIES_START_SEEN'))
{
define('TEXT_NO_FEATURED_CATEGORIES_START_SEEN', 'No start date given, please re-enter.');
}

?>
 
	<form id = "date_input" action="featured_categories_bydate.php">
      <div class="container">
	    <div class="row">
          <div class="col-md-4">
            <div>
			<label for = "dt1"><?php echo SEARCH_EVENTS_START_TEXT;?></label>
            <input type="date" class = "date_time" id="dt1" name ="date_start" required>
			<input type="hidden" class = "date_time_unix" id="dt1_unix" name ="date_start_unix">
            </div>
          </div>
          <div class="col-md-4">
            <div>
			<label for = "dt2"><?php echo SEARCH_EVENTS_END_TEXT;?></label>
            <input type="date" class = "date_time" id="dt2" name = "date_end">
			<input type="hidden" class = "date_time_unix" id="dt2_unix" name ="date_end_unix">
            </div>
          </div>
          <div class="col-md-4">
            <div>
             <input type="submit" class="btn btn-primary" value="<?php echo IMAGE_BUTTON_SEARCH; ?>" />
            </div>
          </div>
        </div>
		</div>
	</form>

 <script>

document.addEventListener("DOMContentLoaded", function(event) { 

$(document).ready(function(e){
	document.getElementById('dt1').valueAsDate = new Date();
	document.getElementById("dt1").onchange = function ()
		{
		  var input = document.getElementById("dt2");
		  input.min = this.value;
		}
	
    $('#date_input').on('submit',function(e){
		 e.preventDefault();
		 
		 var start_date = ($('#dt1').val());
		 var end_date = $('#dt2').val();
		 
		 var start_unix = (new Date(start_date).getTime())/1000; 
		 var end_unix = (new Date(end_date).getTime())/1000; 
		 

		 if(($.isNumeric(start_unix) != true) || (start_unix == 0)){ 
			alert ('<?php echo TEXT_NO_FEATURED_CATEGORIES_START_SEEN;?>');
			return;
		}
		 if(($.isNumeric(end_unix) != true) ){ 
			end_unix = 0;
		}
		 
         $('#dt1_unix').val(start_unix);
		 $('#dt2_unix').val(end_unix);
		 
		 this.submit();
    });
});
});
</script>
