<?php
include "../../../model/model.php";
?>


<style>
textarea.form-control {
    height: 120 !important;
}


</style>
<form id="itinerary_frm_save">

<div class="modal fade" id="itinerary_save_modal" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false">

  <div class="modal-dialog modal-lg" role="document" style="width:95% !important;">

    <div class="modal-content">

      <div class="modal-header">

        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

        <h4 class="modal-title" id="myModalLabel">Add Itinerary</h4>

      </div>

      <div class="modal-body">
        <div class="row">
          <div class="text-left col-md-3 col-sm-6">
            <select id="dest_ids"  name="dest_names" title="Select Destination" class="form-control" onchange="check_dest_validation(this.id)" style="width:100%"> 
              <option value="">*Destination</option>
              <?php
              $sq_query = mysqlQuery("select * from destination_master where status != 'Inactive'"); 
              while($row_dest = mysqli_fetch_assoc($sq_query)){ ?>
              <option value="<?php echo $row_dest['dest_id']; ?>"><?php echo $row_dest['dest_name']; ?></option>
              <?php } ?>
            </select>
          </div>
          <div class="col-sm-6 text-left">
            <button type="button" class="btn btn-info btn-sm ico_left pull-left" style="margin-right:10px" onclick="display_format_modal();"><i class="fa fa-download"></i>&nbsp;&nbsp;CSV Format</button>
            <div class="div-upload  mg_bt_20" id="div_upload_button" role="button" title="Import CSV" style="overflow:hidden;">
                  <div id="itinerary_csv_upload" class="upload-button1"><span  id="vendor_status1">CSV</span></div>
                  <span id="vendor_status"></span>
                  <ul id="files" ></ul>
                  <input type="file" id="itinerary_csv_file" accept=".csv,text/csv" style="position:absolute;left:0;top:0;width:100%;height:100%;opacity:0;cursor:pointer;z-index:10;">
                  <input type="hidden" id="txt_itinerary_csv_upload_dir" name="txt_itinerary_csv_upload_dir">
            </div>
            <button type="button" data-toggle="tooltip" class="btn btn-excel" title="Character limit for Special attraction is 85 characters, for Day-wise program is 2000 characters and for Overnight stay is 30 characters."><i class="fa fa-question-circle"></i></button> 
          </div>
          
        
          <div class="col-xs-3 text-right text_center_xs">
            <button type="button" class="btn btn-excel" title="Add Row" onclick="addRow('default_program_list','', 'itinerary')"><i class="fa fa-plus"></i></button>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12"> 
		        <!-- <button type="button" data-toggle="tooltip" class="btn btn-excel" title="Character limit for Special attraction is 85 characters, for Day-wise program is 2000 characters and for Overnight stay is 30 characters."><i class="fa fa-question-circle"></i></button> 
          <span style="color: red;" class="note" data-original-title="" title="">For saving daywise program keep checkbox selected!</span> -->
          </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-sm-6 col-xs-12 mg_bt_10">
            <table style="width:100%" id="default_program_list" name="default_program_list" class="table table-bordered table-hover table-striped no-marg pd_bt_51 mg_bt_0">
                <tbody>
                  <tr>
                    <td width="27px;" class="itinerary-row-check" style="padding-right: 10px !important; vertical-align: top; padding-top: 35px !important;">
                      <div class="itinerary-chk-wrap" style="height:40px; display:flex; align-items:center; justify-content:center;">
                        <input class="css-checkbox labelauty" id="chk_programd1" type="checkbox" checked style="display: none;">
                        <label for="chk_programd1" style="margin:0;"><span class="labelauty-unchecked-image"></span><span class="labelauty-checked-image"></span></label>
                      </div>
                    </td>
                    <td width="20px;"><input maxlength="15" value="1" type="text" name="username" placeholder="Sr. No." class="form-control" disabled="" style="margin-top:35px;"></td>
                    <td class="col-md-3 no-pad" style="padding-left: 5px !important;"><input type="text" id="special_attaraction" onchange="validate_spaces(this.id);validate_spattration(this.id);" name="special_attaraction" class="form-control" placeholder="*Special Attraction" title="Special Attraction" style="margin-top:35px;"></td>
                    <td class="col-md-5 no-pad" style="padding-left: 5px !important;max-width: 594px;overflow: hidden;position: relative;" ><textarea id="day_program" name="day_program" class="form-control day_program" rows="2" placeholder="*Day-wise Program" onchange="validate_spaces(this.id);validate_dayprogram(this.id);" title="Day-wise Program"   style="overflow:hidden;resize:none;height:900px;"  
    rows="1"></textarea></td>
                    <td class="col-md-2 no-pad" style="padding-left: 5px !important;"><input type="text" id="overnight_stay" name="overnight_stay" style="margin-top:35px;"  onchange="validate_spaces(this.id);validate_onstay(this.id);" class="form-control" placeholder="*Overnight Stay" title="Overnight Stay"></td>
                    <td class="col-md-2 no-pad" style="padding-left:5px !important; ">
                      <div style="display: flex; align-items: center; gap: 10px;">
                      <div style="margin-top:35px;">
                        <label for="day_image_1" class="btn btn-sm btn-success" 
                               style="margin-bottom: 5px; padding: 6px 12px; font-size: 12px; cursor: pointer; border-radius: 4px; border: none; background-color: #28a745; color: white; font-weight: 500;">
                            Upload Image
                        </label>
                        <input type="file" id="day_image_1" 
                               name="day_image_1" accept="image/*" 
                               onchange="previewDayImage(this, '1')" 
                               style="display: none;">
                        <div id="day_image_preview_1" style="display: none; margin-top: 5px;">
                            <div style="height:100px; max-height: 100px; overflow:hidden; position: relative; width: 100px; border: 2px solid #ddd; border-radius: 8px; background-color: #f8f9fa;">
                                <img id="preview_img_1" src="" alt="Preview" 
                                     style="width:100%; height:100%; object-fit: cover; border-radius: 6px;">
                                <button type="button" 
                                        onclick="removeDayImage('1', this)" 
                                        title="Remove Image" 
                                        style="position: absolute; top: 5px; right: 5px; width: 20px; height: 20px; border: none; border-radius: 50%; background-color: #dc3545; color: white; font-size: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                                    ×
                                </button>
                            </div>
                        </div>
                        <input type="hidden" id="itinerary_image_path_1" name="itinerary_image_path_1" />
                      </div>
                      <button type="button" data-toggle="tooltip" class="btn btn-excel" title="Image Size Should Be Less Than 100KB, Resolution : 900 X 900 and Format: Jpg/JEPG/Png"><i class="fa fa-question-circle " style="margin-top: 33px; display:flex; align-items: center; justify-content: center;"></i></button>
                      </div>
                    </td>
                  </tr>
                </tbody>
            </table>
            </div>
        </div>
        <div class="row mg_tp_10">
          <div class="col-xs-12 text-center">
                        <button class="btn btn-sm btn-success" type="button" id="btn_save"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;Save</button>
          </div>
        </div>
        <div id="itinerary_html"></div>
      </div> 

    </div>

  </div>

</div>

</form>

<script>
// Wait for jQuery to be available with fallback
function initializeItineraryTooltips() {
    $('#itinerary_save_modal [data-toggle="tooltip"]').each(function () {
        var $el = $(this);
        if ($el.data('bs.tooltip')) {
            $el.tooltip('destroy');
        }
        $el.tooltip({ placement: 'bottom', container: 'body' });
    });
    $('#itinerary_save_modal [data-toggle="tooltip"]').off('click.itinerary-tooltip').on('click.itinerary-tooltip', function () {
        $('.tooltip').remove();
    });
}

// Keep itinerary checkbox vertically centered with Sr. No. field
function alignItineraryRowCheckbox(row) {
    if (!row || !row.cells[0]) {
        return;
    }
    var cell = row.cells[0];
    var checkbox = cell.querySelector('input[type="checkbox"]');
    if (!checkbox) {
        return;
    }
    var checked = checkbox.checked;
    var id = checkbox.id || ('chk_programd' + (row.rowIndex + 1));
    cell.className = 'itinerary-row-check';
    cell.style.cssText = 'width:27px; padding-right:10px !important; vertical-align:top; padding-top:35px !important;';
    cell.innerHTML = '';

    var wrap = document.createElement('div');
    wrap.className = 'itinerary-chk-wrap';
    wrap.style.cssText = 'height:40px; display:flex; align-items:center; justify-content:center;';

    var input = document.createElement('input');
    input.type = 'checkbox';
    input.className = 'css-checkbox labelauty';
    input.id = id;
    input.name = id;
    input.checked = checked;
    input.style.display = 'none';

    var label = document.createElement('label');
    label.setAttribute('for', id);
    label.style.margin = '0';
    label.innerHTML = '<span class="labelauty-unchecked-image"></span><span class="labelauty-checked-image"></span>';

    wrap.appendChild(input);
    wrap.appendChild(label);
    cell.appendChild(wrap);

    if (typeof $ !== 'undefined' && typeof $.fn.labelauty === 'function') {
        $(input).labelauty({
            label: false,
            maximum_width: '20px'
        });
        $(input).next('label').css('margin', '0');
    }
}
window.alignItineraryRowCheckbox = alignItineraryRowCheckbox;

function initializeModal() {
    window.itineraryImages = {};
    if (typeof $ !== 'undefined' && typeof $.fn.select2 !== 'undefined') {
$('#dest_ids').select2();
$('#itinerary_save_modal').modal('show');
initializeItineraryTooltips();
var firstTable = document.getElementById('default_program_list');
if (firstTable) {
    for (var r = 0; r < firstTable.rows.length; r++) {
        alignItineraryRowCheckbox(firstTable.rows[r]);
    }
}
    } else {
        // Fallback - try again after a short delay
        setTimeout(initializeModal, 100);
    }
}
initializeModal();



function display_format_modal(){
    var base_url = $('#base_url').val();
    var url = base_url + 'images/csv_format/itinerary.csv';
    var a = document.createElement('a');
    a.href = url;
    a.download = 'itinerary.csv';
    a.target = '_blank';
    a.rel = 'noopener';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}

function check_dest_validation(dest_id){

  var dest_id = $('#'+dest_id).val();
  $.post('itinerary/check_dest_validation.php', {dest_id:dest_id}, function(data){
    if(data != ''){
      error_msg_alert(data);
    }
	});
}

function itineraryMasterRowChecked(row){
    if(!row || !row.cells[0]){
        return false;
    }
    var cb = row.cells[0].querySelector('input[type="checkbox"]');
    return !!(cb && cb.checked);
}

function itineraryMasterCellValue(row, cellIndex, selector){
    if(!row || !row.cells[cellIndex]){
        return '';
    }
    var el = row.cells[cellIndex].querySelector(selector);
    return el ? el.value : '';
}

function itinerary_csv_upload(){
    var $file = $('#itinerary_csv_file');
    var $status = $('#vendor_status1');
    if(!$file.length){
        return;
    }
    $('body > input[type="file"][name="uploadfile"]').remove();
    $file.off('change.itineraryCsv').on('change.itineraryCsv', function(){
        var file = this.files && this.files[0] ? this.files[0] : null;
        if(!file){
            return;
        }
        var ext = (file.name.split('.').pop() || '').toLowerCase();
        if(ext !== 'csv'){
            error_msg_alert('Only CSV files are allowed');
            this.value = '';
            return;
        }
        if(!confirm('Do you want to import this file?')){
            this.value = '';
            return;
        }
        var formData = new FormData();
        formData.append('uploadfile', file);
        this.value = '';
        var prevLabel = $status.text() || 'CSV';
        $status.text('Uploading...');
        var uploadUrl = ($('#base_url').val() || '') + 'view/other_masters/itinerary/upload_itinerary_csv_file.php';
        $.ajax({
            url: uploadUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response){
                $status.text(prevLabel);
                response = String(response || '').replace(/<[^>]*>/g, '').replace(/&nbsp;/g, ' ').trim();
                if(response === 'error' || response === ''){
                    error_msg_alert('File is not uploaded.');
                    return;
                }
                document.getElementById('txt_itinerary_csv_upload_dir').value = response;
                itinerary_form_csv_save();
            },
            error: function(){
                $status.text(prevLabel);
                error_msg_alert('File is not uploaded.');
            }
        });
    });
}
itinerary_csv_upload();
$('#itinerary_save_modal').on('shown.bs.modal', function(){
    itinerary_csv_upload();
});

function itinerary_form_csv_save(){
  
    var itinerary_csv_dir = document.getElementById("txt_itinerary_csv_upload_dir").value;
    var base_url = $('#base_url').val();
    $.ajax({
        type:'post',
        url: base_url+'controller/other_masters/itinerary/csv_save.php',
        data:{itinerary_csv_dir : itinerary_csv_dir },
        success:function(result){
            var table = document.getElementById("default_program_list");
            $('#itinerary_html').html(result);
            var itinerary_arr = [];
            try {
                var raw = $('#itinerary_arr').val();
                itinerary_arr = raw ? JSON.parse(raw) : [];
            } catch (e) {
                itinerary_arr = [];
            }
            if(!Array.isArray(itinerary_arr)){
                itinerary_arr = [];
            }

            if(itinerary_arr.length == 0){
              error_msg_alert('Improper itinerary details entered!');
              return false;
            }
            else{
              for(var r = 1; r < itinerary_arr.length; r++){
                  if(table.rows[r] === undefined){
                      addRow('default_program_list','', 'itinerary');
                  }
              }

              for(var i=0; i<itinerary_arr.length; i++){

                  var row = table.rows[i];
                  if(!row){ continue; }
                  var spaVal = (itinerary_arr[i]['spa'] || '').replace(/\\/g, '');
                  var dwpVal = (itinerary_arr[i]['dwp'] || '').replace(/\\/g, '');
                  var osVal = (itinerary_arr[i]['os'] || '').replace(/\\/g, '');
                  var spaInput = row.cells[2] ? row.cells[2].querySelector('input') : null;
                  var dwpTextarea = row.cells[3] ? row.cells[3].querySelector('textarea') : null;
                  var osInput = row.cells[4] ? row.cells[4].querySelector('input') : null;
                  if(spaInput){ spaInput.value = spaVal; }
                  if(dwpTextarea){ dwpTextarea.value = dwpVal; }
                  if(osInput){ osInput.value = osVal; }
                  var cb = row.cells[0] ? row.cells[0].querySelector('input[type="checkbox"]') : null;
                  if(cb){ cb.checked = true; }
                  if(typeof alignItineraryRowCheckbox === 'function'){
                      alignItineraryRowCheckbox(row);
                  }
              }
              initializeItineraryTooltips();
            }
        }
    });
}
// Simple click handler instead of complex validation
$(document).ready(function() {
    console.log("Document ready - initializing itinerary save");
    
    // Handle save button click
    $(document).off('click.itineraryMasterSave', '#btn_save').on('click.itineraryMasterSave', '#btn_save', function(e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        if (!$(this).closest('#itinerary_save_modal').length) {
            return false;
        }

        var dest_id = $('#dest_ids').val();
        if(!dest_id || dest_id == '') {
            error_msg_alert("Please select a destination!");
            return false;
        }

        var $btn = $(this);
        $btn.button('loading');
        itineraryWhenImagesReady(function() {
            var table = document.getElementById("default_program_list");
            if (!table) {
                $btn.button('reset');
                return false;
            }
            var rowCount = table.rows.length;
            var count = 0;
            for (var i = 0; i < rowCount; i++) {
                if (itineraryMasterRowChecked(table.rows[i])) {
                    count++;
                }
            }
            if (parseInt(count) === 0) {
                $btn.button('reset');
                error_msg_alert("Please select atleast one day itinerary!");
                return false;
            }

            var sp_arr = [];
            var dwp_arr = [];
            var os_arr = [];
            var img_arr = [];

            for (var i = 0; i < rowCount; i++) {
                var row = table.rows[i];
                if (!itineraryMasterRowChecked(row)) {
                    continue;
                }
                var sp = itineraryMasterCellValue(row, 2, 'input');
                var dwp = itineraryMasterCellValue(row, 3, 'textarea');
                var os = itineraryMasterCellValue(row, 4, 'input');
                var img = itineraryCollectRowImagePath(row);

                if (sp == "") {
                    $btn.button('reset');
                    error_msg_alert('Special attraction is mandatory in row ' + (i + 1));
                    return false;
                }
                if (dwp == "") {
                    $btn.button('reset');
                    error_msg_alert('Daywise program is mandatory in row ' + (i + 1));
                    return false;
                }
                if (os == "") {
                    $btn.button('reset');
                    error_msg_alert('Overnight stay is mandatory in row ' + (i + 1));
                    return false;
                }

                sp_arr.push(sp);
                dwp_arr.push(dwp);
                os_arr.push(os);
                img_arr.push(img || '');
            }

            $.ajax({
                type: 'post',
                url: base_url() + 'controller/other_masters/itinerary/itinerary_save.php',
                data: {
                    dest_id: dest_id,
                    sp_arr: sp_arr,
                    dwp_arr: dwp_arr,
                    os_arr: os_arr,
                    img_arr: JSON.stringify(img_arr)
                },
                success: function(result) {
                    $btn.button('reset');
                    var msg = result.split('--');
                    if (msg[0] != "error") {
                        $('#itinerary_save_modal').modal('hide');
                        msg_alert(result);
                        list_reflect();
                    } else {
                        error_msg_alert(msg[1]);
                    }
                },
                error: function(xhr, status, error) {
                    $btn.button('reset');
                    error_msg_alert("Error saving itinerary: " + error);
                }
            });
        });
    });

    // Function to fix all existing rows
    window.fixAllItineraryRows = function() {
        var table = document.getElementById("default_program_list");
        if (table) {
            console.log("Fixing all existing itinerary rows, total rows:", table.rows.length);
            
            // Process ALL rows, not just the last one
            for (var i = 0; i < table.rows.length; i++) {
                var row = table.rows[i];
                var rowId = i + 1; // Row 0 gets ID 1, Row 1 gets ID 2, etc.
                
                console.log("Fixing row", i, "with", row.cells.length, "cells");
                
                if (row && row.cells[5]) {
                    // Check if this row already has the correct image upload structure
                    var hasCorrectStructure = row.cells[5].querySelector('input[id^="day_image_"]');
                    
                    if (!hasCorrectStructure) {
                        console.log("Adding image upload structure to row", rowId);
                        console.log("Row cells[5] current content:", row.cells[5].innerHTML.substring(0, 100));
                        row.cells[5].innerHTML = `
                            <div style="margin-top:35px; display: flex; align-items: center; gap: 10px;">
                                <label for="day_image_${rowId}" class="btn btn-sm btn-success" 
                                       style="margin-bottom: 5px; padding: 6px 12px; font-size: 12px; cursor: pointer; border-radius: 4px; border: none; background-color: #28a745; color: white; font-weight: 500;">
                                    Upload Image
                                </label>
                                <input type="file" id="day_image_${rowId}" 
                                       name="day_image_${rowId}" accept="image/*" 
                                       onchange="previewDayImage(this, '${rowId}')" 
                                       style="display: none;">
                                <div id="day_image_preview_${rowId}" style="display: none; margin-top: 5px;">
                                    <div style="height:100px; max-height: 100px; overflow:hidden; position: relative; width: 100px; border: 2px solid #ddd; border-radius: 8px; background-color: #f8f9fa;">
                                        <img id="preview_img_${rowId}" src="" alt="Preview" 
                                             style="width:100%; height:100%; object-fit: cover; border-radius: 6px;">
                                        <button type="button" 
                                                onclick="removeDayImage('${rowId}', this)" 
                                                title="Remove Image" 
                                                style="position: absolute; top: 5px; right: 5px; width: 20px; height: 20px; border: none; border-radius: 50%; background-color: #dc3545; color: white; font-size: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                                            ×
                                        </button>
                                    </div>
                                </div>
                                <input type="hidden" id="itinerary_image_path_${rowId}" name="itinerary_image_path_${rowId}" />
                                <button type="button" data-toggle="tooltip" class="btn btn-excel" title="Image Size Should Be Less Than 100KB, Resolution : 900 X 900 and Format: Jpg/JEPG/Png"><i class="fa fa-question-circle " style="display:flex; align-items: center; justify-content: center;"></i></button>
                            </div>
                            `;
                            console.log("Row", rowId, "new content:", row.cells[5].innerHTML.substring(0, 200));
                            
                            // Verify the actual input element after creation
                            var newInput = row.cells[5].querySelector('input[type="file"]');
                            if (newInput) {
                                console.log("Row", rowId, "created input ID:", newInput.id, "onchange:", newInput.getAttribute('onchange'));
                            }
                        } else {
                            console.log("Row", rowId, "already has correct image upload structure");
                            console.log("Row", rowId, "existing content:", row.cells[5].innerHTML.substring(0, 200));
                            
                            // Check the existing input element
                            var existingInput = row.cells[5].querySelector('input[type="file"]');
                            if (existingInput) {
                                console.log("Row", rowId, "existing input ID:", existingInput.id, "onchange:", existingInput.getAttribute('onchange'));
                            }
                        }
                }
                
                // Keep checkbox aligned with Sr. No.
                alignItineraryRowCheckbox(row);
            }
            initializeItineraryTooltips();
        }
    };

    // Override the addRow function to ensure image upload works
    window.originalAddRow = window.addRow;
    window.addRow = function(tableID, quot_table, itinerary) {
        console.log("Custom addRow called for", tableID, "itinerary:", itinerary);
        
        // Call original addRow
        if (window.originalAddRow) {
            window.originalAddRow(tableID, quot_table, itinerary);
        }
        
        // If it's itinerary table, force add image upload to ALL rows and reinitialize labelauty
        if (tableID === "default_program_list" && itinerary === "itinerary") {
            setTimeout(function() {
                var table = document.getElementById(tableID);
                console.log("Processing ALL rows for itinerary table, total rows:", table.rows.length);
                console.log("Table structure:");
                for (var j = 0; j < table.rows.length; j++) {
                    console.log("  Row", j, ":", j === 0 ? "(HEADER)" : "(DATA ROW " + j + ")");
                }
                
                // Process ALL data rows
                for (var i = 0; i < table.rows.length; i++) {
                    var row = table.rows[i];
                    var rowId = i + 1;
                    
                    console.log("Processing row", i, "with", row.cells.length, "cells");
                    console.log("Row ID will be:", rowId);
                    
                    if (row && row.cells[5]) {
                        // Check if this row already has the correct image upload structure
                        var hasCorrectStructure = row.cells[5].querySelector('input[id^="day_image_"]');
                        
                        if (!hasCorrectStructure) {
                            console.log("Adding image upload structure to row", rowId);
                            row.cells[5].innerHTML = `
                                <div style="margin-top:35px;">
                                    <label for="day_image_${rowId}" class="btn btn-sm btn-success" 
                                           style="margin-bottom: 5px; padding: 6px 12px; font-size: 12px; cursor: pointer; border-radius: 4px; border: none; background-color: #28a745; color: white; font-weight: 500;">
                                        Upload Image
                                    </label>
                                    <input type="file" id="day_image_${rowId}" 
                                           name="day_image_${rowId}" accept="image/*" 
                                           onchange="previewDayImage(this, '${rowId}')" 
                                           style="display: none;">
                                    <div id="day_image_preview_${rowId}" style="display: none; margin-top: 5px;">
                                        <div style="height:100px; max-height: 100px; overflow:hidden; position: relative; width: 100px; border: 2px solid #ddd; border-radius: 8px; background-color: #f8f9fa;">
                                            <img id="preview_img_${rowId}" src="" alt="Preview" 
                                                 style="width:100%; height:100%; object-fit: cover; border-radius: 6px;">
                                            <button type="button" 
                                                    onclick="removeDayImage('${rowId}', this)" 
                                                    title="Remove Image" 
                                                    style="position: absolute; top: 5px; right: 5px; width: 20px; height: 20px; border: none; border-radius: 50%; background-color: #dc3545; color: white; font-size: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                                                ×
                                            </button>
                                        </div>
                                    </div>
                                    <input type="hidden" id="itinerary_image_path_${rowId}" name="itinerary_image_path_${rowId}" />
                                    <button type="button" data-toggle="tooltip" class="btn btn-excel" title="Image Size Should Be Less Than 100KB, Resolution : 900 X 900 and Format: Jpg/JEPG/Png"><i class="fa fa-question-circle " style="display:flex; align-items: center; justify-content: center;"></i></button>
                                </div>
                            `;
                        } else {
                            console.log("Row", rowId, "already has correct image upload structure");
                        }
                    }
                    
                    alignItineraryRowCheckbox(row);
                }
                initializeItineraryTooltips();
            }, 300);
        }
    };
});

function itineraryImageUploadUrl() {
    return (($('#base_url').val() || '')) + 'view/other_masters/itinerary/upload_itinerary_image.php';
}

function itineraryParseImageUploadResponse(response) {
    response = String(response || '').replace(/<[^>]*>/g, ' ').replace(/&nbsp;/g, ' ').replace(/\s+/g, ' ').trim();
    if (!response || /^error/i.test(response)) {
        return '';
    }
    var match = response.match(/uploads\/itinerary_images\/[A-Za-z0-9._-]+/i);
    if (match) {
        return match[0];
    }
    if (response.indexOf('uploads/') === 0 && response.indexOf(' ') === -1) {
        return response;
    }
    return '';
}

function itineraryTableRoot() {
    return document.getElementById('default_program_list');
}

function itineraryRowFromControl(el) {
    if (!el) {
        return null;
    }
    var $row = $(el).closest('tr');
    return $row.length ? $row[0] : null;
}

function itineraryFindInRow(row, selector) {
    return row ? row.querySelector(selector) : null;
}

function itineraryRowHidden(row) {
    return itineraryFindInRow(row, 'input[name^="itinerary_image_path_"]');
}

function itinerarySetRowImagePathOnRow(row, path) {
    if (!row) {
        return;
    }
    var hidden = itineraryRowHidden(row);
    if (hidden) {
        hidden.value = path || '';
    }
    if (path) {
        row.setAttribute('data-itinerary-image', path);
    } else {
        row.removeAttribute('data-itinerary-image');
    }
}

function itineraryResetFileInputOnRow(row) {
    var input = itineraryFindInRow(row, 'input[type="file"][id^="day_image_"]');
    if (!input || !input.parentNode) {
        return;
    }
    var neu = input.cloneNode(true);
    neu.value = '';
    input.parentNode.replaceChild(neu, input);
}

function itineraryShowDayImagePreviewOnRow(row, src) {
    if (!row) {
        return;
    }
    var previewImg = itineraryFindInRow(row, 'img[id^="preview_img_"]');
    var previewDiv = itineraryFindInRow(row, 'div[id^="day_image_preview_"]');
    var fileInput = itineraryFindInRow(row, 'input[type="file"][id^="day_image_"]');
    if (previewImg && src) {
        previewImg.src = src;
        previewImg.style.display = 'block';
    }
    if (previewDiv) {
        previewDiv.style.display = 'block';
        var btn = previewDiv.querySelector('button');
        if (btn) {
            btn.style.display = 'flex';
        }
    }
    if (fileInput) {
        var label = row.querySelector('label[for="' + fileInput.id + '"]');
        if (label) {
            label.style.display = 'none';
        }
    }
}

function itineraryHideDayImagePreviewOnRow(row) {
    if (!row) {
        return;
    }
    var previewImg = itineraryFindInRow(row, 'img[id^="preview_img_"]');
    var previewDiv = itineraryFindInRow(row, 'div[id^="day_image_preview_"]');
    var fileInput = itineraryFindInRow(row, 'input[type="file"][id^="day_image_"]');
    if (previewDiv) {
        previewDiv.style.display = 'none';
    }
    if (previewImg) {
        previewImg.src = '';
    }
    if (fileInput) {
        var label = row.querySelector('label[for="' + fileInput.id + '"]');
        if (label) {
            label.style.display = 'inline-block';
        }
    }
}

function itineraryUploadImageFile(file) {
    var fd = new FormData();
    fd.append('uploadfile', file);
    return $.ajax({
        url: itineraryImageUploadUrl(),
        type: 'POST',
        data: fd,
        processData: false,
        contentType: false
    });
}

function itineraryCollectRowImagePath(row) {
    if (!row) {
        return '';
    }
    var hidden = itineraryRowHidden(row);
    var img = hidden ? String(hidden.value || '').trim() : '';
    if (!img) {
        img = String(row.getAttribute('data-itinerary-image') || '').trim();
    }
    return img;
}

function itineraryWhenImagesReady(callback) {
    var tries = 0;
    (function tick() {
        if (!window.itineraryPendingUploads || tries > 40) {
            callback();
            return;
        }
        tries++;
        setTimeout(tick, 250);
    })();
}

window.previewDayImage = function(input, rowIndex) {
    var row = itineraryRowFromControl(input);
    var file = input && input.files ? input.files[0] : null;
    if (!file || !row) {
        return;
    }
    var allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
    if (allowedTypes.indexOf(file.type) === -1) {
        error_msg_alert('Only JPG, JPEG, PNG, WEBP files are allowed');
        itineraryResetFileInputOnRow(row);
        return;
    }
    var reader = new FileReader();
    reader.onload = function(e) {
        itineraryShowDayImagePreviewOnRow(row, e.target.result);
    };
    reader.readAsDataURL(file);

    window.itineraryPendingUploads = (window.itineraryPendingUploads || 0) + 1;
    window.itineraryImageUploadSeq = window.itineraryImageUploadSeq || {};
    var rowKey = String(rowIndex || (input.id || '').replace('day_image_', '') || 'row');
    window.itineraryImageUploadSeq[rowKey] = (window.itineraryImageUploadSeq[rowKey] || 0) + 1;
    var uploadSeq = window.itineraryImageUploadSeq[rowKey];
    row.setAttribute('data-itinerary-upload-seq', uploadSeq);

    itineraryUploadImageFile(file).always(function() {
        window.itineraryPendingUploads = Math.max(0, (window.itineraryPendingUploads || 1) - 1);
    }).done(function(response) {
        if (String(row.getAttribute('data-itinerary-upload-seq') || '') !== String(uploadSeq)) {
            return;
        }
        var path = itineraryParseImageUploadResponse(response);
        if (!path) {
            error_msg_alert('Image upload failed. Please try again.');
            return;
        }
        itinerarySetRowImagePathOnRow(row, path);
    }).fail(function() {
        if (String(row.getAttribute('data-itinerary-upload-seq') || '') !== String(uploadSeq)) {
            return;
        }
        error_msg_alert('Image upload failed. Please try again.');
    });
};

window.removeDayImage = function(rowIndex, btn) {
    var row = itineraryRowFromControl(btn) || (function() {
        var table = itineraryTableRoot();
        var input = table ? table.querySelector('[id="day_image_' + rowIndex + '"]') : null;
        return itineraryRowFromControl(input);
    })();
    if (!row) {
        return;
    }
    window.itineraryImageUploadSeq = window.itineraryImageUploadSeq || {};
    var rowKey = String(rowIndex || '');
    window.itineraryImageUploadSeq[rowKey] = (window.itineraryImageUploadSeq[rowKey] || 0) + 1;
    row.setAttribute('data-itinerary-upload-seq', window.itineraryImageUploadSeq[rowKey]);
    itineraryHideDayImagePreviewOnRow(row);
    itinerarySetRowImagePathOnRow(row, '');
    itineraryResetFileInputOnRow(row);
};

if (typeof window.itineraryRowIdCounter === 'undefined') {
    window.itineraryRowIdCounter = 2;
}

    // Function to debug all row IDs
    window.debugAllRowIds = function() {
        var table = document.getElementById("default_program_list");
        if (table) {
            console.log("=== DEBUGGING ALL ROW IDS ===");
            console.log("Current itineraryRowIdCounter:", window.itineraryRowIdCounter);
            for (var i = 1; i < table.rows.length; i++) {
                var row = table.rows[i];
                var fileInput = row.cells[5] ? row.cells[5].querySelector('input[type="file"]') : null;
                if (fileInput) {
                    console.log("Row", i, "has input ID:", fileInput.id, "onchange:", fileInput.getAttribute('onchange'));
                } else {
                    console.log("Row", i, "has no file input");
                }
            }
            console.log("=== END DEBUG ===");
        }
    };

    // Do not rebuild row HTML on load; that wipes a first-row image if the user already selected one.

</script>
<script src="<?= BASE_URL ?>js/ajaxupload.3.5.js"></script>
<script src="<?= BASE_URL ?>js/app/footer_scripts.js"></script>