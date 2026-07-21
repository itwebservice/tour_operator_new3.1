<div id="div_modal_save"></div>
<div id="location_list_div"></div>

<script src="js/location.js"></script>
<script>
function save_modal()
{
	
	$('#bt_save_loc').button('loading');
	$.post('locations/location_save_modal.php', {}, function(data){
		$('#bt_save_loc').button('reset');
		$('#div_modal_save').html(data);
	});
}

</script>
<script src="js/location.js"></script>