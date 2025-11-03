<style>
.style_text
{
	position: absolute;
    right: 15px;
    display: flex;
    gap: 15px;
    background: #f5f5f5;
    padding: 0px 14px;
    top: 0px;
}
#tab2 {
	max-height: 600px;
	overflow-y: auto;
	overflow-x: hidden;
}
</style>
<form id="frm_tab_itinerary">
	<div class="panel panel-default panel-body app_panel_style feildset-panel mg_tp_20">
		<legend>Tour Itinerary Details</legend>
		<div class="app_panel_content Filter-panel">
			<div class="row mg_bt_10">
				<div class="col-xs-12 text-right text_center_xs">
					<button type="button" class="btn btn-excel btn-sm" onClick="addRow('package_program_list')" title="Add row"><i class="fa fa-plus"></i></button>
					<button type="button" class="btn btn-pdf btn-sm" onClick="deleteRow('package_program_list');renumber_itinerary_rows_tab3();" title="Delete row"><i class="fa fa-trash"></i></button>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12 col-sm-6 col-xs-12 mg_bt_10">
					<div style="max-height: 400px; overflow-y: auto;">
					<table style="width:100%" id="package_program_list" name="package_program_list"
						class="table mg_bt_0 table-bordered">
						<tbody>
							<tr>
								<td width="27px;" style="padding-right: 10px !important;"><input
									class="css-checkbox mg_bt_10 labelauty" id="chk_program1" type="checkbox" checked style="display: none;"><label
									for="chk_program1" style="margin-top: 55px;"><span
										class="labelauty-unchecked-image"></span><span
										class="labelauty-checked-image"></span></label></td>
								<td width="50px;"><input maxlength="15" value="1" type="text" name="username" placeholder="Sr. No."  style="margin-top: 35px;" class="form-control" disabled=""></td>
								<td class="col-md-3 no-pad" style="padding-left: 5px !important;"><input type="text" id="special_attaraction1"  style="margin-top: 35px;" onchange="validate_spaces(this.id);" name="special_attaraction" class="form-control mg_bt_10" placeholder="Special Attraction" title="Special Attraction"></td>
								<td class='col-md-5 no-pad' style="padding-left: 5px !important;position: relative;"><textarea id="day_program1" name="day_program" style=" height:900px;" class="form-control mg_bt_10 day_program" placeholder="*Day Program" title="Day-wise Program" onchange="validate_spaces(this.id);" rows="3"></textarea><span class="style_text"><span class="style_text_b" data-wrapper="**" style="font-weight: bold; cursor: pointer;" title="Bold text">B</span><span class="style_text_u" data-wrapper="__" style="cursor: pointer;" title="Underline text"><u>U</u></span></span>
								</td>
								<td class="col-md-1/2 no-pad" style="padding-left: 5px !important;"><input type="text" id="overnight_stay1" style="margin-top: 35px;" name="overnight_stay" onchange="validate_spaces(this.id);" class="form-control mg_bt_10" placeholder="Overnight Stay" title="Overnight Stay"></td>
								<td class="col-md-1/2 no-pad" style="padding-left: 5px !important;"><select id="meal_plan1" title="meal plan" style="margin-top: 35px;" name="meal_plan" class="form-control mg_bt_10" data-original-title="Meal Plan">
										<?php get_mealplan_dropdown(); ?>
										</select></td>
								<td class='col-md-1 pad_8'><button type="button" class="btn btn-info btn-iti btn-sm itinerary-btn" style="margin-top: 35px; border:none;" data-row="1" id="itinerary1" title="Add Itinerary" onClick="add_itinerary(0,'special_attaraction1','day_program1','overnight_stay1','Day-1')"><i class="fa fa-plus"></i></button></td>
								<td style="display:none"><input type="text" name="package_id_n" value="" autocomplete="off" class="form-control" data-original-title="" title=""></td>
							</tr>
						</tbody>
					</table>
					</div>
				</div>
			</div>
		</div>
	</div>

	<br><br>
	<div class="row text-center">
		<div class="col-xs-12">
			<button type="button" class="btn btn-info btn-sm ico_left" onclick="switch_to_tab1()"><i class="fa fa-arrow-left"></i>&nbsp;&nbsp;Previous</button>
			&nbsp;&nbsp;
			<button class="btn btn-info btn-sm ico_right">Next&nbsp;&nbsp;<i class="fa fa-arrow-right"></i></button>
		</div>
	</div>
</form>

<div id="div_itinerary_modal"></div>

<script>
function switch_to_tab1(){ $('a[href="#tab1"]').tab('show'); }

$(document).on("click", ".style_text_b, .style_text_u", function() {
    var wrapper = $(this).data("wrapper");
    
    // Get the textarea element
    var textarea = $(this).parents('.style_text').siblings('.day_program')[0];
    
    // Ensure textarea exists and selectionStart/selectionEnd are supported
        var start = textarea.selectionStart;
        var end = textarea.selectionEnd;

        // Get the selected text
        var selectedText = textarea.value.substring(start, end);

        // Wrap the selected text with the wrapper (e.g., ** for bold, __ for underline)
        var wrappedText = wrapper + selectedText + wrapper;

        // Insert the wrapped text back into the textarea
        textarea.value = textarea.value.substring(0, start) + wrappedText + textarea.value.substring(end);

        // Adjust the cursor position after wrapping
        textarea.selectionStart = start;
        textarea.selectionEnd = end + wrapper.length * 2;
		var text=textarea.value;
		 var content = text.replace(/\*\*(.*?)\*\*/g, '<b>$1</b>');

		// Replace markdown-style underline (__text__) with <u> tags
		content = content.replace(/__(.*?)__/g, '<u>$1</u>');
		textarea.value =content;
});

// Update newly added row's button IDs
function updateLatestRowIds() {
	var table = document.getElementById('package_program_list');
	if(table) {
		var rowCount = table.rows.length;
		var lastRow = table.rows[rowCount - 1];
		
		// Update input IDs
		if(lastRow.cells[2]) lastRow.cells[2].childNodes[0].id = 'special_attaraction' + rowCount;
		if(lastRow.cells[3]) lastRow.cells[3].childNodes[0].id = 'day_program' + rowCount;
		if(lastRow.cells[4]) lastRow.cells[4].childNodes[0].id = 'overnight_stay' + rowCount;
		if(lastRow.cells[5]) lastRow.cells[5].childNodes[0].id = 'meal_plan' + rowCount;
		
		// Update button onclick
		if(lastRow.cells[6]) {
			var btn = lastRow.cells[6].getElementsByTagName('button')[0];
			if(btn) {
				btn.id = 'itinerary' + rowCount;
				btn.setAttribute('onclick', "add_itinerary(0,'special_attaraction" + rowCount + "','day_program" + rowCount + "','overnight_stay" + rowCount + "','Day-" + rowCount + "')");
			}
		}
		
		// Update serial number
		if(lastRow.cells[1]) lastRow.cells[1].childNodes[0].value = rowCount;
	}
}

// Intercept clicks on Add Row button
$(document).on('click', '.btn-excel', function() {
	var btnText = $(this).attr('onClick');
	if(btnText && btnText.includes('package_program_list')) {
		setTimeout(function() {
			updateLatestRowIds();
		}, 100);
	}
});

// Function to renumber all itinerary rows after deletion
function renumber_itinerary_rows_tab3() {
	var table = document.getElementById('package_program_list');
	if(table) {
		for(var i = 0; i < table.rows.length; i++) {
			if(table.rows[i].cells[1]) {
				var serialInput = table.rows[i].cells[1].querySelector('input');
				if(serialInput) {
					serialInput.value = i + 1;
				}
			}
		}
	}
}

$('#frm_tab_itinerary').validate({
	rules:{
	},
	submitHandler:function(form){
		$('a[href="#tab3"]').tab('show');
		return false;
	}
});
</script>

