<?php 
include_once('../../../model/model.php');
?>

<input type="hidden" name="branch_count" id="branch_count">
 <div id="div_branch_save_modal"></div>
 <div id="branch_list_div" class="main_block"></div>

<script>
$('#location_id_filter').select2();
function branche_save_modal()
{
	$('#branche_save_modal').prop('disabled',true);
  check_package_type('<?= $setup_package ?>','branch');
  var branch_count = $('#branch_count').val();
  if(<?= $setup_package ?> == '1' || <?= $setup_package ?> == '2'){
  		if(branch_count == '0'){
	      $('#branche_save_modal').button('loading');
  			$.post('branches/branches_save_modal.php', {}, function(data){
          $('#branche_save_modal').prop('disabled',false);
          $('#branche_save_modal').button('reset');
		      $('#div_branch_save_modal').html(data);
		    });
  		}
  		else {
  			$('#package_permission').removeClass('hidden');   
  		}
  }
  else{
	  $('#branche_save_modal').button('loading');
  	$.post('branches/branches_save_modal.php', {}, function(data){
      $('#div_branch_save_modal').html(data);
      $('#branche_save_modal').prop('disabled',false);
      $('#branche_save_modal').button('reset');
    });
  }
}
</script>

<script src="js/branch.js"></script>