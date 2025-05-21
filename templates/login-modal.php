<?php 

// Check to ensure this file is included in osConcert!
defined('_FEXEC') or die();
?>
	<!-- Login Modal -->
	<div class="modal" id="LoginModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
         <p><?php 
		 if(CLOSE_LOGIN=='yes'){
		 echo "Viewing Only"; 
		 }else{
		 echo PLEASE_LOGIN;
		 }
		 ?></p>
      </div>
    </div>
  </div>
</div>