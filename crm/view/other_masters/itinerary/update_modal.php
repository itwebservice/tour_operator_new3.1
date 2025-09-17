<?php
include "../../../model/model.php";
$dest_id = $_POST['dest_id'];
?>

<style>
textarea.form-control {
    height: 120 !important;
}

</style>
<form id="itinerary_frm_update">

<div class="modal fade" id="itinerary_update_modal" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false" >

  <div class="modal-dialog modal-lg" role="document" style="width:95% !important;">

    <div class="modal-content">

      <div class="modal-header">

        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

        <h4 class="modal-title" id="myModalLabel">Update Itinerary</h4>

      </div>

      <div class="modal-body">
        <div class="row">
          <div class="text-left col-md-3 col-sm-6">
            <select id="dest_ids1"  name="dest_names1" title="Select Destination" class="form-control" onchange="check_dest_validation(this.id)" style="width:100%" disabled> 
              <?php
              $row_dest = mysqli_fetch_assoc(mysqlQuery("select * from destination_master where dest_id = '$dest_id'"));
              ?>
              <option value="<?php echo $row_dest['dest_id']; ?>"><?php echo $row_dest['dest_name']; ?></option>
            </select>
          </div>
<div class="text-left col-md-3 col-sm-6">
          <button type="button" data-toggle="tooltip" class="btn btn-excel" title="Character limit for Special attraction is 85 characters, for Day-wise program is 2000 characters and for Overnight stay is 30 characters."><i class="fa fa-question-circle"></i></button> 
</div>
          <div class="col-xs-9 text-right text_center_xs">
              <button type="button" class="btn btn-excel btn-sm" title="Add row" onClick="addRow('default_program_list', '', 'itinerary')"><i class="fa fa-plus"></i></button>
          </div>
        </div>
        <div class="row mg_tp_10">
          <div class="col-sm-12">
		        
            <!-- <span style="color: red;" class="note" data-original-title="" title="">For saving daywise program keep checkbox selected!</span> -->
          </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-sm-6 col-xs-12 mg_bt_10">
            <table style="width:100%" id="default_program_list" name="default_program_list" class="table mg_bt_0 table-bordered">
                <tbody>
                  <?php
                  $count = 0;
                  $sq_itinerary = mysqlQuery("select * from itinerary_master where dest_id='$dest_id'");
                  while($row_itinerary = mysqli_fetch_assoc($sq_itinerary)){
                    $count++;
                    ?>
                    <tr>
                      <td width="27px;" style="padding-right: 10px !important;"><input class="css-checkbox labelauty" id="chk_programd<?=$count?>" type="checkbox" checked style="display: none;"><label for="chk_programd1<?=$count?>" style="margin-top:55px;"><span class="labelauty-unchecked-image"></span><span class="labelauty-checked-image"></span></label></td>
                      <td width="20px;"><input maxlength="15" value="<?=$count?>" type="text" name="username" placeholder="Sr. No." class="form-control" disabled="" style="margin-top:35px;"></td>
                      <td class="col-md-3 no-pad" style="padding-left: 5px !important;"><input type="text" id="special_attaraction<?=$count?>-u" onchange="validate_spaces(this.id);validate_spattration(this.id);" name="special_attaraction" style="margin-top:35px;" class="form-control" placeholder="*Special Attraction" title="Special Attraction" value="<?=$row_itinerary['special_attraction']?>"></td>
                      <td class="col-md-5 no-pad" style="padding-left: 5px !important;max-width: 594px;overflow: hidden;position: relative;"><textarea id="day_program<?=$count?>-u" name="day_program" class="form-control day_program" rows="3" placeholder="*Day-wise Program" onchange="validate_spaces(this.id);validate_dayprogram(this.id);" title="Day-wise Program"  style="overflow:hidden;resize:none;height:900px;"  
    rows="1"><?=$row_itinerary['daywise_program']?></textarea><span class="style_text" style="position: absolute !important; right: 15px !important; display: flex !important; gap: 15px; background: #f5f5f5 !important; padding: 0px 14px !important; top: 0px !important;"><span class="style_text_b" data-wrapper="**" style="font-weight: bold; cursor: pointer;" title="Bold text">B</span><span class="style_text_u" data-wrapper="__" style="cursor: pointer;" title="Underline text"><u>U</u></span></span></td>
                      <td class="col-md-2 no-pad" style="padding-left: 5px !important;"><input type="text" id="overnight_stay<?=$count?>-u" name="overnight_stay" onchange="validate_spaces(this.id);validate_onstay(this.id);"  style="margin-top:35px;"class="form-control" placeholder="*Overnight Stay" title="Overnight Stay" value="<?=$row_itinerary['overnight_stay']?>"></td>
                      <td class="col-md-2 no-pad" style="padding-left:5px !important;">
                        <!-- Debug: Image path = <?= $row_itinerary['itinerary_image'] ?? 'NULL' ?> -->
                        <!-- Debug: BASE_URL = <?= BASE_URL ?> -->
                        <!-- Debug: Project URL = <?= str_replace('/crm/', '/', BASE_URL) ?> -->
                        <div style="margin-top:35px;">
                          <label for="day_image_<?=$count?>" class="btn btn-sm btn-success" 
                                 style="margin-bottom: 5px; padding: 6px 12px; font-size: 12px; cursor: pointer; border-radius: 4px; border: none; background-color: #28a745; color: white; font-weight: 500; <?= (!empty($row_itinerary['itinerary_image']) && trim($row_itinerary['itinerary_image']) !== '' && trim($row_itinerary['itinerary_image']) !== 'NULL') ? 'display:none;' : '' ?>">
                              Upload Image
                          </label>
                          <input type="file" id="day_image_<?=$count?>" 
                                 name="day_image_<?=$count?>" accept="image/*" 
                                 onchange="previewDayImage(this, '<?=$count?>')" 
                                 style="display: none;">
                          <div id="day_image_preview_<?=$count?>" style="<?= (!empty($row_itinerary['itinerary_image']) && trim($row_itinerary['itinerary_image']) !== '' && trim($row_itinerary['itinerary_image']) !== 'NULL') ? 'display:block;' : 'display:none;' ?> margin-top: 5px;">
                              <div style="height:100px; max-height: 100px; overflow:hidden; position: relative; width: 100px; border: 2px solid #ddd; border-radius: 8px; background-color: #f8f9fa;">
                                  <img id="preview_img_<?=$count?>" src="<?php 
                                        if (!empty($row_itinerary['itinerary_image'])) {
                                            $image_path = trim($row_itinerary['itinerary_image']);
                                            // Debug the actual path
                                            error_log("Image path from DB: " . $image_path);
                                            
                                            // Check if path is valid and not empty
                                            if ($image_path && $image_path !== '' && $image_path !== 'NULL') {
                                                // Check if path already starts with http
                                                if (strpos($image_path, 'http') === 0) {
                                                    echo $image_path;
                                                } else {
                                                    // For itinerary images, use project root URL instead of CRM BASE_URL
                                                    // BASE_URL is http://localhost/itoursdemo/crm/ but images are in http://localhost/itoursdemo/uploads/
                                                    $project_base_url = str_replace('/crm/', '/', BASE_URL);
                                                    $project_base_url = rtrim($project_base_url, '/');
                                                    $image_path = ltrim($image_path, '/');
                                                    $final_url = $project_base_url . '/' . $image_path;
                                                    error_log("Final image URL: " . $final_url);
                                                    echo $final_url;
                                                }
                                            } else {
                                                // Empty or invalid path, don't output anything
                                                echo '';
                                            }
                                        } else {
                                            echo '';
                                        }
                                    ?>" alt="Preview" 
                                       style="width:100%; height:100%; object-fit: cover; border-radius: 6px;"
                                       onerror="console.log('UPDATE MODAL: Existing image failed to load:', this.src); this.style.display='none'; this.parentElement.parentElement.style.display='none'; this.parentElement.parentElement.parentElement.querySelector('label').style.display='block'; this.parentElement.querySelector('button[onclick*=removeDayImage]').style.display='none';"
                                       onload="console.log('Image loaded successfully:', this.src);">
                                  <button type="button" 
                                          onclick="removeDayImage('<?=$count?>')" 
                                          title="Remove Image" 
                                          style="position: absolute; top: 5px; right: 5px; width: 20px; height: 20px; border: none; border-radius: 50%; background-color: #dc3545; color: white; font-size: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2); <?= (empty($row_itinerary['itinerary_image']) || trim($row_itinerary['itinerary_image']) === '' || trim($row_itinerary['itinerary_image']) === 'NULL') ? 'display:none;' : '' ?>">
                                      ×
                                  </button>
                              </div>
                          </div>
                          <input type="hidden" id="itinerary_image_path_<?=$count?>" name="itinerary_image_path_<?=$count?>" value="<?= $row_itinerary['itinerary_image'] ?? '' ?>" />
                        </div>
                      </td>
                      <td class="hidden"><input type="text" id="entry_id" name="entry_id" class="form-control" value="<?=$row_itinerary['entry_id']?>"></td>
                    </tr>
                    <?php 
                  } ?>
                </tbody>
            </table>
            </div>
        </div>
          <div class="row mg_tp_10">
            <div class="col-xs-12 text-center">
              <button class="btn btn-sm btn-success" id="btn_update"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;Update</button>
            </div>
          </div>



      </div>      

    </div>

  </div>

</div>

</form>

<script>
$('#dest_ids1').select2();
$('#itinerary_update_modal').modal('show');



// data bold and underline


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

    // 

$('#itinerary_frm_update').validate({
    rules:{
           dest_names1 : { required : true }
    },
    submitHandler:function(form){

      var dest_id = $('#dest_ids1').val();
      var table = document.getElementById("default_program_list");
      var rowCount = table.rows.length;
      var sp_arr = [];
      var dwp_arr = [];
      var os_arr = [];
      var checked_arr = [];
      var entry_id_arr = [];
      var img_arr = [];
      //Atleast one row validation
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
			var checked_arr = new Array();
      var sp_arr = new Array();
      var dwp_arr = new Array();
      var os_arr = new Array();
			var entry_id_arr = new Array();
      for(var i=0; i<rowCount; i++){
        
        var row = table.rows[i];

        var status = row.cells[0].childNodes[0].checked;
        var sp = row.cells[2].childNodes[0].value;
        var dwp = row.cells[3].childNodes[0].value;
        var os = row.cells[4].childNodes[0].value;
				if(row.cells[6]){
					var entry_id = row.cells[6].childNodes[0].value;	
				}
				else{
					var entry_id = "";
				}
        if(row.cells[0].childNodes[0].checked){

          if(sp==""){
              error_msg_alert('Special attraction is mandatory in row'+(i+1));
              return false;
          }
          if(dwp==""){
              error_msg_alert('Daywise program is mandatory in row'+(i+1));
              return false;
          }
          if(os==""){
              error_msg_alert('Overnight stay is mandatory in row'+(i+1));
              return false;
          }
          var flag1 = validate_spattration(row.cells[2].childNodes[0].id);
          var flag2 = validate_dayprogram(row.cells[3].childNodes[0].id);
          var flag3 = validate_onstay(row.cells[4].childNodes[0].id);         
          if(!flag1 || !flag2 || !flag3){
              return false;
          }
        }
        checked_arr.push(status);
        sp_arr.push(sp);
        dwp_arr.push(dwp);
        os_arr.push(os);
        entry_id_arr.push(entry_id);
        
        // Get image path - check both stored images and hidden input
        var img = '';
        var rowIndex = i + 1; // Convert 0-based index to 1-based for matching PHP count
        
        console.log("Processing row", i, "with rowIndex", rowIndex);
        
        // First check if we have a new image in window.itineraryImages
        if (window.itineraryImages && window.itineraryImages[rowIndex]) {
            var imageData = window.itineraryImages[rowIndex];
            console.log("Found image data for rowIndex", rowIndex, imageData);
            
            if (imageData.file && !imageData.uploaded) {
                console.log("Uploading new image for rowIndex", rowIndex);
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
                                window.itineraryImages[rowIndex].uploaded = true;
                                window.itineraryImages[rowIndex].image_url = response;
                                console.log("Image uploaded successfully for rowIndex", rowIndex, ":", img);
                            } else {
                                console.log("Upload failed for rowIndex", rowIndex, ":", response);
                            }
                        } catch(e) {
                            console.log('Upload parse error for rowIndex', rowIndex, ':', e);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log("Upload error for rowIndex", rowIndex, ":", error);
                    }
                });
            } else if (imageData.image_url) {
                img = imageData.image_url;
                console.log("Using existing image URL for rowIndex", rowIndex, ":", img);
            }
        } else {
            // Fallback to hidden input - use the correct ID pattern
            var imgInput = row.querySelector('input[id="itinerary_image_path_' + rowIndex + '"]');
            img = imgInput ? imgInput.value : '';
            console.log("Using hidden input for rowIndex", rowIndex, ":", img);
        }
        
        console.log("Final image for row", i, ":", img);
        img_arr.push(img || '');
      }

      console.log("UPDATE MODAL: Final data being sent:");
      console.log("- dest_id:", dest_id);
      console.log("- sp_arr:", sp_arr);
      console.log("- dwp_arr:", dwp_arr);
      console.log("- os_arr:", os_arr);
      console.log("- checked_arr:", checked_arr);
      console.log("- entry_id_arr:", entry_id_arr);
      console.log("- img_arr:", img_arr);
      console.log("- window.itineraryImages:", window.itineraryImages);

      $('#btn_update').button('loading');
      $.ajax({
      type:'post',
      url:base_url()+'controller/other_masters/itinerary/itinerary_update.php',
      data:{ dest_id : dest_id, sp_arr : sp_arr, dwp_arr : dwp_arr, os_arr : os_arr,checked_arr:checked_arr,entry_id_arr:entry_id_arr, img_arr : img_arr},
      success:function(result){

          $('#btn_update').button('reset');
          var msg = result.split('--');
          if(msg[0]!="error"){
            $('#itinerary_update_modal').modal('hide');
            msg_alert(result);
            list_reflect();
          }
          else{
            error_msg_alert(msg[1]);
            $('#btn_update').button('reset');
          }
      }
      });
    }
});

// Image handling functions for edit modal
window.previewDayImage = function(input, rowIndex) {
    console.log("UPDATE MODAL: Preview triggered for row:", rowIndex);
    console.log("UPDATE MODAL: Input file:", input.files[0]);
    
    var file = input.files[0];
    if (!file) {
        console.log("UPDATE MODAL: No file selected");
        return;
    }
    
    // Validate file type
    var allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
    if (!allowedTypes.includes(file.type)) {
        error_msg_alert('Only JPG, JPEG, PNG, WEBP files are allowed');
        input.value = ''; // Clear the input
        return;
    }
    
    console.log("UPDATE MODAL: File validation passed, showing preview for row:", rowIndex);
    
    // Show preview immediately
    var reader = new FileReader();
    reader.onload = function(e) {
        console.log("UPDATE MODAL: FileReader loaded, setting image src for row:", rowIndex);
        var previewImg = $('#preview_img_' + rowIndex);
        var previewDiv = $('#day_image_preview_' + rowIndex);
        
        if (previewImg.length === 0) {
            console.error("UPDATE MODAL: Preview image element not found for row:", rowIndex);
            return;
        }
        
        if (previewDiv.length === 0) {
            console.error("UPDATE MODAL: Preview div element not found for row:", rowIndex);
            return;
        }
        
        // Set the image source and show preview
        previewImg.attr('src', e.target.result);
        previewDiv.show();
        
        // Show the remove button when image is selected
        previewDiv.find('button[onclick*="removeDayImage"]').show();
        
        // Hide the upload button after image selection
        $('#day_image_' + rowIndex).parent().find('label').hide();
        
        console.log("UPDATE MODAL: Preview displayed successfully for row:", rowIndex);
    };
    
    reader.onerror = function(e) {
        console.error("UPDATE MODAL: FileReader error for row:", rowIndex, e);
        error_msg_alert('Error reading the selected file');
    };
    
    reader.readAsDataURL(file);
    
    // Store file for later upload with the correct rowIndex key
    if (!window.itineraryImages) {
        window.itineraryImages = {};
    }
    window.itineraryImages[rowIndex] = {
        file: file,
        uploaded: false
    };
    
    console.log("UPDATE MODAL: Image stored in window.itineraryImages[" + rowIndex + "]");
    console.log("UPDATE MODAL: Current itineraryImages object:", window.itineraryImages);
}

// Remove day image function
window.removeDayImage = function(rowIndex) {
    console.log("UPDATE MODAL: Removing image for row:", rowIndex);
    
    // Clear file input
    $('#day_image_' + rowIndex).val('');
    
    // Hide preview and remove button
    var previewDiv = $('#day_image_preview_' + rowIndex);
    previewDiv.hide();
    previewDiv.find('button[onclick*="removeDayImage"]').hide();
    
    // Clear the image src
    $('#preview_img_' + rowIndex).attr('src', '');
    
    // Show the upload button again
    $('#day_image_' + rowIndex).parent().find('label').show();
    
    // Clear hidden path
    $('#itinerary_image_path_' + rowIndex).val('');
    
    // Clear stored file
    if (window.itineraryImages && window.itineraryImages[rowIndex]) {
        delete window.itineraryImages[rowIndex];
    }
    
    console.log("UPDATE MODAL: Image removed successfully for row:", rowIndex);
}

// Function to check if image exists and handle accordingly
window.checkImageExists = function(img) {
    var xhr = new XMLHttpRequest();
    xhr.open('HEAD', img.src, true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 404 || xhr.status === 403) {
                console.log('Image not found on server:', img.src);
                img.style.display = 'none';
                img.parentElement.parentElement.style.display = 'none';
                var label = img.parentElement.parentElement.parentElement.querySelector('label');
                if (label) label.style.display = 'block';
            } else if (xhr.status === 200) {
                console.log('Image exists on server:', img.src);
            }
        }
    };
    xhr.send();
};

// Initialize the static row counter for edit modal (start from max existing row + 1)
$(document).ready(function() {
    var table = document.getElementById("default_program_list");
    if (table) {
        var maxRowId = 0;
        for (var i = 1; i < table.rows.length; i++) {
            var fileInput = table.rows[i].cells[5] ? table.rows[i].cells[5].querySelector('input[type="file"]') : null;
            if (fileInput && fileInput.id) {
                var rowId = parseInt(fileInput.id.replace('day_image_', ''));
                if (rowId > maxRowId) {
                    maxRowId = rowId;
                }
            }
        }
        window.itineraryRowIdCounter = maxRowId + 1;
        console.log("Edit modal: Set itineraryRowIdCounter to", window.itineraryRowIdCounter);
        
        // Check all existing images
        $('img[id^="preview_img_"]').each(function() {
            if (this.src && this.src !== '' && this.src !== window.location.href) {
                window.checkImageExists(this);
            }
        });
    }
});

</script>
<script src="<?= BASE_URL ?>js/app/footer_scripts.js"></script>
<script src="<?= BASE_URL ?>js/app/field_validation.js"></script>