<?php
include "../../../model/model.php";
?>
<div class="row text-right mg_tp_20 mg_bt_10">
	<div class="col-md-12">
		<button class="btn btn-info btn-sm ico_left" onclick="save_modal()" id="btn_save_modal"><i class="fa fa-plus"></i>&nbsp;&nbsp;Airline</button>
	</div>
</div>
<div class="app_panel_content Filter-panel">
	<div class="row">
		<div class="text-left col-md-3 col-sm-6">
			<select id="airline_status" name="airline_status" title="Select Status" class="form-control" onchange="list_reflect()" style="width:100%">
				<option value="">All</option>
				<option value="Active">Active</option>
				<option value="Inactive">Inactive</option>
			</select>
		</div>
	</div>
</div>

<div id="div_modal"></div>
<div id="div_list" class="loader_parent">
	<div class="row mg_tp_20"> <div class="col-md-12 no-pad"> <div class="table-responsive">
		<table id="airline_table" class="table table-hover" style="margin: 20px 0 !important;">
		</table>
	</div>
</div>
<script>
function save_modal()
{
	$('#btn_save_modal').button('loading');
	$.post('airlines/save_modal.php', {}, function(data){
		$('#btn_save_modal').button('reset');
		$('#div_modal').html(data);
	});
}
var columns = [
		{ title: "S_NO" },
		{ title: "Airline_Name" },
		{ title: "Code" },
		{ title: "Status" },
		{ title: "Actions" }
	];
function list_reflect(){
	$('#div_list').append('<div class="loader"></div>');
	var status = $('#airline_status').val();
	$.post('airlines/list_reflect.php', { status : status }, function(data){
		setTimeout(() => {
			pagination_load(data,columns,true, false,20, 'airline_table');
			$('.loader').remove();
		}, 1000);
	});
}list_reflect();
function update_modal(airline_id)
{
	$('#airline_update-'+airline_id).button('loading');
	$('#airline_update-'+airline_id).prop('disabled',true);
	$.post('airlines/update_modal.php', { airline_id : airline_id }, function(data){
		$('#div_modal').html(data);
		$('#airline_update-'+airline_id).button('reset');
		$('#airline_update-'+airline_id).prop('disabled',false);
	});
}
</script>