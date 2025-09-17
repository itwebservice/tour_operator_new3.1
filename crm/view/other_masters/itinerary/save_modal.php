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
            <div class="div-upload  mg_bt_20" id="div_upload_button">
                  <div id="itinerary_csv_upload" class="upload-button1"><span  id="vendor_status1">CSV</span></div>
                  <span id="vendor_status"></span>
                  <ul id="files" ></ul>
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
                    <td width="27px;" style="padding-right: 10px !important;"><input class="css-checkbox labelauty" id="chk_programd1" type="checkbox" checked style="display: none;"><label for="chk_programd1" style="margin-top:55px;"><span class="labelauty-unchecked-image"></span><span class="labelauty-checked-image"></span></label></td>
                    <td width="20px;"><input maxlength="15" value="1" type="text" name="username" placeholder="Sr. No." class="form-control" disabled="" style="margin-top:35px;"></td>
                    <td class="col-md-3 no-pad" style="padding-left: 5px !important;"><input type="text" id="special_attaraction" onchange="validate_spaces(this.id);validate_spattration(this.id);" name="special_attaraction" class="form-control" placeholder="*Special Attraction" title="Special Attraction" style="margin-top:35px;"></td>
                    <td class="col-md-5 no-pad" style="padding-left: 5px !important;max-width: 594px;overflow: hidden;position: relative;" ><textarea id="day_program" name="day_program" class="form-control day_program" rows="2" placeholder="*Day-wise Program" onchange="validate_spaces(this.id);validate_dayprogram(this.id);" title="Day-wise Program"   style="overflow:hidden;resize:none;height:900px;"  
    rows="1"></textarea><span class="style_text" style="position: absolute !important; right: 15px !important; display: flex !important; gap: 15px; background: #f5f5f5 !important; padding: 0px 14px !important; top: 0px !important;"><span class="style_text_b" data-wrapper="**" style="font-weight: bold; cursor: pointer;" title="Bold text">B</span><span class="style_text_u" data-wrapper="__" style="cursor: pointer;" title="Underline text"><u>U</u></span></span></td>
                    <td class="col-md-2 no-pad" style="padding-left: 5px !important;"><input type="text" id="overnight_stay" name="overnight_stay" style="margin-top:35px;"  onchange="validate_spaces(this.id);validate_onstay(this.id);" class="form-control" placeholder="*Overnight Stay" title="Overnight Stay"></td>
                    <td class="col-md-2 no-pad" style="padding-left:5px !important;">
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
                                        onclick="removeDayImage('1')" 
                                        title="Remove Image" 
                                        style="position: absolute; top: 5px; right: 5px; width: 20px; height: 20px; border: none; border-radius: 50%; background-color: #dc3545; color: white; font-size: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                                    ×
                                </button>
                            </div>
                        </div>
                        <input type="hidden" id="itinerary_image_path_1" name="itinerary_image_path_1" />
                      </div>
                    </td>
                  </tr>
                </tbody>
            </table>
            </div>
        </div>
        <div class="row mg_tp_10">
          <div class="col-xs-12 text-center">
            <button class="btn btn-sm btn-success" id="btn_save"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;Save</button>
          </div>
        </div>
        <div id="itinerary_html">
        <div>
      </div>      

    </div>

  </div>

</div>

</form>

<script>
// Wait for jQuery to be available with fallback
function initializeModal() {
    if (typeof $ !== 'undefined' && typeof $.fn.select2 !== 'undefined') {
$('#dest_ids').select2();
$('#itinerary_save_modal').modal('show');
    } else {
        // Fallback - try again after a short delay
        setTimeout(initializeModal, 100);
    }
}
initializeModal();



$(document).on("click", ".style_text_b, .style_text_u", function() {
        var wrapper = $(this).data("wrapper");

        var textarea = $(this).parents('.style_text').siblings('.day_program')[0];
        console.log(textarea);
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
        var text = textarea.value;
        var content = text.replace(/\*\*(.*?)\*\*/g, '<b>$1</b>');

        // Replace markdown-style underline (__text__) with <u> tags
        content = content.replace(/__(.*?)__/g, '<u>$1</u>');
        textarea.value = content;
        //console.log(content);    
    });

function display_format_modal(){
    var base_url = $('#base_url').val();
    window.location = base_url+"images/csv_format/itinerary.csv";
}

function check_dest_validation(dest_id){

  var dest_id = $('#'+dest_id).val();
  $.post('itinerary/check_dest_validation.php', {dest_id:dest_id}, function(data){
    if(data != ''){
      error_msg_alert(data);
    }
	});
}

itinerary_csv_upload();
function itinerary_csv_upload()
{   
    var type="id_proof";
    var btnUpload=$('#itinerary_csv_upload');
    var status=$('#vendor_status');
    new AjaxUpload(btnUpload, {
      action: 'itinerary/upload_itinerary_csv_file.php',
      name: 'uploadfile',
      onSubmit: function(file, ext){

        if(!confirm('Do you want to import this file?')){
          return false;
        }
        if (! (ext && /^(csv)$/.test(ext))){ 
          error_msg_alert('Only CSV files are allowed');
          return false;
        }
        status.text('Uploading...');
      },
      onComplete: function(file, response){
        status.text('');
        if(response==="error"){          
          alert("File is not uploaded.");           
        } else{
          document.getElementById("txt_itinerary_csv_upload_dir").value = response;
          itinerary_form_csv_save();
        }
      }
    });
}
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
            var itinerary_arr = JSON.parse($('#itinerary_arr').val());

            if(itinerary_arr.length == 0){
              error_msg_alert('Improper itinerary details entered!');
              return false;
            }
            else{

              for(var i=0; i<itinerary_arr.length; i++){

                  var row = table.rows[i];
                  itinerary_arr[i]['spa'] = itinerary_arr[i]['spa'].replace(/\\/g, '');
                  itinerary_arr[i]['dwp'] = itinerary_arr[i]['dwp'].replace(/\\/g, '');
                  itinerary_arr[i]['os'] = itinerary_arr[i]['os'].replace(/\\/g, '');
                  row.cells[2].childNodes[0].value = itinerary_arr[i]['spa'];
                  var dwpTextarea = row.cells[3].querySelector('textarea');
                  if(dwpTextarea){ dwpTextarea.value = itinerary_arr[i]['dwp']; }
                  row.cells[4].childNodes[0].value = itinerary_arr[i]['os'];
                  if(i!=itinerary_arr.length-1){
                      if(table.rows[i+1]==undefined){
                          addRow('default_program_list','', 'itinerary');
                      }
                  }
              }
            }
        }
    });
}
// Simple click handler instead of complex validation
$(document).ready(function() {
    console.log("Document ready - initializing itinerary save");
    
    // Handle save button click
    $(document).on('click', '#btn_save', function(e) {
        e.preventDefault();
        console.log("Save button clicked");
        
        // Get destination ID
        var dest_id = $('#dest_ids').val();
        console.log("Destination ID:", dest_id);
        
        if(!dest_id || dest_id == '') {
            error_msg_alert("Please select a destination!");
            return false;
        }
        
        // Get table data
      var table = document.getElementById("default_program_list");
      var rowCount = table.rows.length;
        console.log("Table rows:", rowCount);
        
        // Check if at least one row is selected
      var count = 0;
      for(var i=0; i<rowCount; i++){
          var row = table.rows[i];
          if(row.cells[0].childNodes[0].checked){
              count++;
          }
      }
        
      if(parseInt(count) == 0){
          error_msg_alert("Please select atleast one day itinerary!");
          return false;
      }
        
        // Collect data from selected rows
        var sp_arr = [];
        var dwp_arr = [];
        var os_arr = [];
        var img_arr = [];
        
        for(var i=0; i<rowCount; i++){
        var row = table.rows[i];
        if(row.cells[0].childNodes[0].checked){
          var sp = row.cells[2].childNodes[0].value;
                var dwpElem = row.cells[3].querySelector('textarea');
                var dwp = dwpElem ? dwpElem.value : '';
          var os = row.cells[4].childNodes[0].value;
                
                // Get the actual row ID from the image input to match with stored images
                var imgInput = row.querySelector('input[id^="day_image_"]');
                var actualRowId = '';
                if (imgInput) {
                    actualRowId = imgInput.id.replace('day_image_', '');
                }
                
                // Image path - check both stored images and hidden input
                var img = '';
                if (window.itineraryImages && window.itineraryImages[actualRowId]) {
                    // If image is stored but not uploaded yet, upload it now
                    var imageData = window.itineraryImages[actualRowId];
                    if (imageData.file && !imageData.uploaded) {
                        // Upload the image immediately
                        var formData = new FormData();
                        formData.append('uploadfile', imageData.file);
                        
                        $.ajax({
                            url: 'itinerary/upload_itinerary_image.php',
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            async: false, // Make it synchronous for data collection
                            success: function(response) {
                                try {
                                    var msg = response.split('--');
                                    if (msg[0] !== "error" && !/<\/?(html|body|h1|p|address|hr)/i.test(response)) {
                                        img = response;
                                        window.itineraryImages[actualRowId].uploaded = true;
                                        window.itineraryImages[actualRowId].image_url = response;
                                    }
                                } catch(e) {
                                    console.log('Upload parse error:', e);
                                }
                            },
                            error: function(xhr, status, error) {
                                console.log("Upload error for row", actualRowId, ":", error);
                            }
                        });
                    } else if (imageData.image_url) {
                        img = imageData.image_url;
                    }
                } else {
                    // Fallback to hidden input
                    var hiddenImgInput = row.querySelector('input[id^="itinerary_image_path_"]');
                    img = hiddenImgInput ? hiddenImgInput.value : '';
                }
                console.log("Row " + i + " (ID: " + actualRowId + ") image path:", img);

                console.log("Row " + i + " data:", {sp: sp, dwp: dwp, os: os});
                
                if(sp == ""){
                    error_msg_alert('Special attraction is mandatory in row ' + (i+1));
              return false;
          }
                if(dwp == ""){
                    error_msg_alert('Daywise program is mandatory in row ' + (i+1));
              return false;
          }
                if(os == ""){
                    error_msg_alert('Overnight stay is mandatory in row ' + (i+1));
              return false;
          }
                
          sp_arr.push(sp);
          dwp_arr.push(dwp);
          os_arr.push(os);
                img_arr.push(img);
            }
        }
        
        console.log("Final data to send:", {
            dest_id: dest_id,
            sp_arr: sp_arr,
            dwp_arr: dwp_arr,
            os_arr: os_arr,
            img_arr: img_arr
        });
        
        // Show loading state
      $('#btn_save').button('loading');
        
        // Send AJAX request
      $.ajax({
            type: 'post',
            url: base_url() + 'controller/other_masters/itinerary/itinerary_save.php',
            data: { 
                dest_id: dest_id, 
                sp_arr: sp_arr, 
                dwp_arr: dwp_arr, 
                os_arr: os_arr,
                img_arr: img_arr
            },
            success: function(result) {
                console.log("Server response:", result);
                $('#btn_save').button('reset');
                
          var msg = result.split('--');
                if(msg[0] != "error"){
            $('#itinerary_save_modal').modal('hide');
            msg_alert(result);
            list_reflect();
                } else {
                    error_msg_alert(msg[1]);
                }
            },
            error: function(xhr, status, error) {
                console.log("AJAX Error:", error);
                console.log("Status:", status);
                console.log("Response:", xhr.responseText);
            $('#btn_save').button('reset');
                error_msg_alert("Error saving itinerary: " + error);
            }
        });
    });

    // Function to fix all existing rows
    window.fixAllItineraryRows = function() {
        var table = document.getElementById("default_program_list");
        if (table) {
            console.log("Fixing all existing itinerary rows, total rows:", table.rows.length);
            
            // Process ALL rows, not just the last one
            for (var i = 1; i < table.rows.length; i++) { // Skip header row (index 0)
                var row = table.rows[i];
                var rowId = i; // Row 1 gets ID 1, Row 2 gets ID 2, etc.
                
                console.log("Fixing row", i, "with", row.cells.length, "cells");
                
                if (row && row.cells[5]) {
                    // Check if this row already has the correct image upload structure
                    var hasCorrectStructure = row.cells[5].querySelector('input[id^="day_image_"]');
                    
                    if (!hasCorrectStructure) {
                        console.log("Adding image upload structure to row", rowId);
                        console.log("Row cells[5] current content:", row.cells[5].innerHTML.substring(0, 100));
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
                                                onclick="removeDayImage('${rowId}')" 
                                                title="Remove Image" 
                                                style="position: absolute; top: 5px; right: 5px; width: 20px; height: 20px; border: none; border-radius: 50%; background-color: #dc3545; color: white; font-size: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                                            ×
                                        </button>
                                    </div>
                                </div>
                                <input type="hidden" id="itinerary_image_path_${rowId}" name="itinerary_image_path_${rowId}" />
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
                
                // Reinitialize labelauty for the checkbox in cell 0
                if (row.cells[0]) {
                    var checkbox = row.cells[0].querySelector('input[type="checkbox"]');
                    if (checkbox) {
                        // Remove any existing labelauty classes and reinitialize
                        $(checkbox).removeClass('labelauty').next('label').remove();
                        $(checkbox).addClass('css-checkbox labelauty');
                        
                        // Create new label
                        var label = document.createElement('label');
                        label.setAttribute('for', checkbox.id);
                        label.style.marginTop = '55px';
                        label.innerHTML = '<span class="labelauty-unchecked-image"></span><span class="labelauty-checked-image"></span>';
                        
                        // Insert label after checkbox
                        checkbox.parentNode.insertBefore(label, checkbox.nextSibling);
                        
                        // Reinitialize labelauty
                        $(checkbox).labelauty({
                            checked_label: "Yes",
                            unchecked_label: "No"
                        });
                    }
                }
            }
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
                
                // Process ALL rows, not just the last one
                for (var i = 1; i < table.rows.length; i++) { // Skip header row (index 0)
                    var row = table.rows[i];
                    var rowId = i; // Row 1 gets ID 1, Row 2 gets ID 2, etc.
                    
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
                                                    onclick="removeDayImage('${rowId}')" 
                                                    title="Remove Image" 
                                                    style="position: absolute; top: 5px; right: 5px; width: 20px; height: 20px; border: none; border-radius: 50%; background-color: #dc3545; color: white; font-size: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                                                ×
                                            </button>
                                        </div>
                                    </div>
                                    <input type="hidden" id="itinerary_image_path_${rowId}" name="itinerary_image_path_${rowId}" />
                                </div>
                            `;
                        } else {
                            console.log("Row", rowId, "already has correct image upload structure");
                        }
                    }
                    
                    // Reinitialize labelauty for the checkbox in cell 0
                    if (row.cells[0]) {
                        var checkbox = row.cells[0].querySelector('input[type="checkbox"]');
                        if (checkbox) {
                            // Remove any existing labelauty classes and reinitialize
                            $(checkbox).removeClass('labelauty').next('label').remove();
                            $(checkbox).addClass('css-checkbox labelauty');
                            
                            // Create new label
                            var label = document.createElement('label');
                            label.setAttribute('for', checkbox.id);
                            label.style.marginTop = '55px';
                            label.innerHTML = '<span class="labelauty-unchecked-image"></span><span class="labelauty-checked-image"></span>';
                            
                            // Insert label after checkbox
                            checkbox.parentNode.insertBefore(label, checkbox.nextSibling);
                            
                            console.log("Reinitialized labelauty for checkbox", checkbox.id);
                        }
                    }
                }
            }, 300);
        }
    };
});

        // Preview day image function (like quotation form)
        window.previewDayImage = function(input, rowIndex) {
            console.log("Preview triggered for row:", rowIndex);
            console.log("Input element:", input);
            console.log("Input ID:", input.id);
            console.log("Expected preview elements - img:", '#preview_img_' + rowIndex, "div:", '#day_image_preview_' + rowIndex);
            
            var file = input.files[0];
            if (!file) {
                console.log("No file selected");
                return;
            }
            
            console.log("File selected:", file.name, "Type:", file.type);
            
            // Validate file type
            var allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                error_msg_alert('Only JPG, JPEG, PNG, WEBP files are allowed');
                input.value = ''; // Clear the input
                return;
            }
            
            // Check if preview elements exist
            var previewImg = $('#preview_img_' + rowIndex);
            var previewDiv = $('#day_image_preview_' + rowIndex);
            console.log("Preview elements found - img:", previewImg.length, "div:", previewDiv.length);
            
            // Show preview immediately
            var reader = new FileReader();
            reader.onload = function(e) {
                console.log("FileReader loaded, setting preview for row:", rowIndex);
                previewImg.attr('src', e.target.result);
                previewDiv.show();
                // Hide the upload button after image selection
                $('#day_image_' + rowIndex).parent().find('label').hide();
                console.log("Preview set and shown for row:", rowIndex);
            };
            reader.readAsDataURL(file);
            
            // Store file for later upload (like quotation form)
            if (!window.itineraryImages) {
                window.itineraryImages = {};
            }
            window.itineraryImages[rowIndex] = {
                file: file,
                uploaded: false
            };
            
            console.log("Image previewed and stored for row:", rowIndex);
            console.log("Stored images object:", window.itineraryImages);
        }

        // Remove day image function
        window.removeDayImage = function(rowIndex) {
            console.log("Removing image for row:", rowIndex);
            
            // Clear file input
            $('#day_image_' + rowIndex).val('');
            
            // Hide preview
            $('#day_image_preview_' + rowIndex).hide();
            
            // Show the upload button again
            $('#day_image_' + rowIndex).parent().find('label').show();
            
            // Clear hidden path
            $('#itinerary_image_path_' + rowIndex).val('');
            
            // Clear stored file
            if (window.itineraryImages && window.itineraryImages[rowIndex]) {
                delete window.itineraryImages[rowIndex];
            }
            
            console.log("Image removed for row:", rowIndex);
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

    // Initialize the static row counter
    window.itineraryRowIdCounter = 2; // Start from 2 since row 1 is default

    // Fix all rows when the page loads
    $(document).ready(function() {
        setTimeout(function() {
            if (typeof window.fixAllItineraryRows === 'function') {
                console.log("Fixing all existing rows on page load...");
                window.fixAllItineraryRows();
                
                // Debug all row IDs after fixing
                setTimeout(function() {
                    if (typeof window.debugAllRowIds === 'function') {
                        window.debugAllRowIds();
                    }
                }, 500);
            }
        }, 1000);
    });

</script>
<script src="<?= BASE_URL ?>js/ajaxupload.3.5.js"></script>
<script src="<?= BASE_URL ?>js/app/footer_scripts.js"></script>