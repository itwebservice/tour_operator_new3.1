<?php
include "../../../model/model.php";
?>
<div class="row text-right mg_tp_20 mg_bt_10">
	<div class="col-md-12">
		<button class="btn btn-info btn-sm ico_left" onclick="save_modal()" id="btn_save_modal"><i class="fa fa-plus"></i>&nbsp;&nbsp;Room Category</button>
	</div>
</div>
<div class="app_panel_content Filter-panel">
	<div class="row">
		<div class="text-left col-md-3 col-sm-6">
			<select id="room_category_status" name="room_category_status" title="Select Status" class="form-control" onchange="list_reflect()" style="width:100%">
				<option value="Active">Active</option>
				<option value="Inactive">Inactive</option>
			</select>
		</div>
	</div>
</div>

<div id="div_modal"></div>
<div id="div_list"></div>
<script>
function save_modal()
{
	$('#btn_save_modal').button('loading');
	$.post('room_category/save_modal.php', {}, function(data){
		$('#btn_save_modal').button('reset');
		$('#div_modal').html(data);
	});
}
function list_reflect()
{
	var status = $('#room_category_status').val();
	$.post('room_category/list_reflect.php', { status : status }, function(data){
		$('#div_list').html(data);
	});
}
list_reflect();
function update_modal(entry_id)
{
	$('#roomc_update-'+entry_id).button('loading');
	$('#roomc_update-'+entry_id).prop('disabled',true);
	$.post('room_category/update_modal.php', { entry_id : entry_id }, function(data){
		$('#div_modal').html(data);
		$('#roomc_update-'+entry_id).button('reset');
		$('#roomc_update-'+entry_id).prop('disabled',false);
	});
}
</script>