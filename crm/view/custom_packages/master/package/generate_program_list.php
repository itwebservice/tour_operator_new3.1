<?php
include '../../../../model/model.php';
?>
<div class="panel panel-default panel-body app_panel_style feildset-panel mg_tp_20">
    <legend>Tour Itinerary</legend>
    <table id="dynamic_table_list" style="width:100%" name="dynamic_table_list">
        <?php
		$total_days = $_POST['total_days'];
		error_log("GENERATE_PROGRAM_LIST: Creating " . $total_days . " itinerary rows");
		for ($i = 1; $i <= $total_days; $i++) {
		?>
        <tr>
            <td class='col-md-3 pad_8'><input type="text" id="special_attaraction<?php echo $i; ?>"
                    name="special_attaraction" class="form-control mg_bt_10" placeholder="*Special Attraction"
                    title="Special Attraction" onchange="validate_spaces(this.id);validate_spattration(this.id);" style="margin-top:15px;"
                    value="">
            </td>
            <td class='col-md-6 pad_8' style="max-width: 594px;overflow: hidden;position: relative;"><textarea 
    id="day_program<?php echo $i; ?>" 
    name="day_program" 
    class="form-control mg_bt_10 day_program" 
    placeholder="*Day<?php echo $i; ?> Program" 
    title="Day-wise Program" 
    onchange="validate_spaces(this.id);validate_dayprogram(this.id);" 
    
    style="overflow:hidden;resize:none;height:900px;"  
    rows="1"
></textarea>
<span class="style_text"><span class="style_text_b" data-wrapper="**" style="font-weight: bold; cursor: pointer;" title="Bold text">B</span><span class="style_text_u" data-wrapper="__" style="cursor: pointer;" title="Underline text"><u>U</u></span></span>
            </td>
			
            <td class='col-md-2 pad_8'><input type="text" id="overnight_stay<?php echo $i; ?>" name="overnight_stay"
                    class="form-control mg_bt_10" placeholder="*Overnight Stay"
                    onchange="validate_spaces(this.id);validate_onstay(this.id);" title="Overnight Stay" value="" style="margin-top:15px;">
            </td>
            <td class='col-md-1 pad_8'><select id="meal_plan<?php echo $i; ?>" title="Meal Plan" name="meal_plan"
                    class="form-control mg_bt_10" style="width:140px;margin-top:15px;">
                    <?php get_mealplan_dropdown(); ?>
            </td>
            <td class='col-md-1 pad_8'><button type="button" class="btn btn-excel btn-sm" title="Add Itinerary" style="margin-top:15px;"
                    id="itinerary<?php echo $i; ?>"
                    onclick="add_itinerary('dest_name_s','special_attaraction<?php echo $i; ?>','day_program<?php echo $i; ?>','overnight_stay<?php echo $i; ?>','Day-<?= $i ?>')"><i
                        class="fa fa-plus"></i></button>
            </td>
            <td class='col-md-1 pad_8' style="width: 120px;">
                <div style="margin-top: 15px;">
                    <label for="day_image_<?php echo $i; ?>" class="btn btn-sm btn-success" 
                           style="margin-bottom: 5px; padding: 6px 12px; font-size: 12px; cursor: pointer; border-radius: 4px; border: none; background-color: #28a745; color: white; font-weight: 500;">
                        <i class="fa fa-image"></i> Upload Image
                    </label>
                    <input type="file" id="day_image_<?php echo $i; ?>" 
                           name="day_image_<?php echo $i; ?>" accept="image/*" 
                           onchange="previewDayImageCreate(this, '<?php echo $i; ?>')" 
                           style="display: none;">
                </div>
                <div id="day_image_preview_<?php echo $i; ?>" style="display: none; margin-top: 5px;">
                    <div style="height:100px; max-height: 100px; overflow:hidden; position: relative; width: 100px; border: 2px solid #ddd; border-radius: 8px; background-color: #f8f9fa;">
                        <img id="preview_img_<?php echo $i; ?>" src="" alt="Preview" 
                             style="width:100%; height:100%; object-fit: cover; border-radius: 6px;">
                        <button type="button" 
                                onclick="removeDayImageCreate('<?php echo $i; ?>')" 
                                title="Remove Image" 
                                style="position: absolute; top: 5px; right: 5px; width: 20px; height: 20px; border: none; border-radius: 50%; background-color: #dc3545; color: white; font-size: 12px; cursor: pointer; display: none; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                            ×
                        </button>
                    </div>
                </div>
                <input type="hidden" id="day_image_path_<?php echo $i; ?>" name="day_image_path_<?php echo $i; ?>" value="" />
            </td>
        </tr>
        <?php } ?>
    </table>
</div>

<script>
// Image preview functions for package tour creation
function previewDayImageCreate(input, offset) {
    console.log("PACKAGE CREATE: Preview triggered for day:", offset);
    console.log("PACKAGE CREATE: Input file:", input.files[0]);
    
    var file = input.files[0];
    if (!file) {
        console.log("PACKAGE CREATE: No file selected");
        return;
    }
    
    // Validate file type
    var allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
    if (!allowedTypes.includes(file.type)) {
        alert('Only JPG, JPEG, PNG, WEBP files are allowed');
        input.value = ''; // Clear the input
        return;
    }
    
    console.log("PACKAGE CREATE: File validation passed, showing preview for day:", offset);
    
    var reader = new FileReader();
    reader.onload = function(e) {
        console.log("PACKAGE CREATE: FileReader loaded, setting image src for day:", offset);
        var previewImg = $('#preview_img_' + offset);
        var previewDiv = $('#day_image_preview_' + offset);
        
        if (previewImg.length === 0) {
            console.error("PACKAGE CREATE: Preview image element not found for day:", offset);
            return;
        }
        
        if (previewDiv.length === 0) {
            console.error("PACKAGE CREATE: Preview div element not found for day:", offset);
            return;
        }
        
        // Set the image source and show preview
        previewImg.attr('src', e.target.result);
        previewDiv.show();
        
        // Show the remove button when image is selected
        previewDiv.find('button[onclick*="removeDayImageCreate"]').css('display', 'flex');
        
        // Hide the upload button after image selection
        $('#day_image_' + offset).parent().find('label').hide();
        
        console.log("PACKAGE CREATE: Preview displayed successfully for day:", offset);
    };
    
    reader.onerror = function(e) {
        console.error("PACKAGE CREATE: FileReader error for day:", offset, e);
        alert('Error reading the selected file');
    };
    
    reader.readAsDataURL(file);
    
    // Store file for later upload with the correct offset key
    if (!window.packageCreateImages) {
        window.packageCreateImages = {};
    }
    window.packageCreateImages[offset] = {
        file: file,
        uploaded: false
    };
    
    console.log("PACKAGE CREATE: Image stored in window.packageCreateImages[" + offset + "]");
    console.log("PACKAGE CREATE: Current packageCreateImages object:", window.packageCreateImages);
}

function removeDayImageCreate(offset) {
    console.log("PACKAGE CREATE: Removing image for day:", offset);
    
    // Clear file input
    $('#day_image_' + offset).val('');
    
    // Hide preview and remove button
    var previewDiv = $('#day_image_preview_' + offset);
    previewDiv.hide();
    previewDiv.find('button[onclick*="removeDayImageCreate"]').hide();
    
    // Clear the image src
    $('#preview_img_' + offset).attr('src', '');
    
    // Show the upload button again
    $('#day_image_' + offset).parent().find('label').show();
    
    // Clear hidden path
    $('#day_image_path_' + offset).val('');
    
    // Clear stored file
    if (window.packageCreateImages && window.packageCreateImages[offset]) {
        delete window.packageCreateImages[offset];
    }
    
    console.log("PACKAGE CREATE: Image removed successfully for day:", offset);
}

// Function to process selected itinerary image after modal closes
function processSelectedItineraryImage() {
    if (window.selectedItineraryImage) {
        var dayId = window.selectedItineraryImage.dayId;
        var img = window.selectedItineraryImage.img;
        
        console.log("PACKAGE CREATE: Processing selected itinerary image for day:", dayId, "img:", img);
        
        // Set the image path in hidden input
        $('#day_image_path_' + dayId).val(img);
        console.log("PACKAGE CREATE: Set hidden input value for day", dayId);
        
        // Show image preview if image exists
        if (img && img !== '' && img !== 'NULL') {
            var imageUrl = img;
            // Check if path already starts with http
            if (img.indexOf('http') !== 0) {
                // For itinerary images, construct the correct URL
                var currentUrl = window.location.href;
                var baseUrl = currentUrl.substring(0, currentUrl.indexOf('/crm/'));
                imageUrl = baseUrl + '/' + img.replace(/^\//, '');
            }
            
            console.log("PACKAGE CREATE: Final imageUrl:", imageUrl);
            
            // Update the preview image
            var previewImg = $('#preview_img_' + dayId);
            var previewDiv = $('#day_image_preview_' + dayId);
            var uploadLabel = $('#day_image_' + dayId).parent().find('label');
            
            console.log("PACKAGE CREATE: Preview elements found - img:", previewImg.length, "div:", previewDiv.length, "label:", uploadLabel.length);
            
            if (previewImg.length > 0) {
                previewImg.attr('src', imageUrl);
                console.log("PACKAGE CREATE: Set image src to:", imageUrl);
            }
            
            if (previewDiv.length > 0) {
                previewDiv.show();
                console.log("PACKAGE CREATE: Showed preview div");
            }
            
            if (uploadLabel.length > 0) {
                uploadLabel.hide();
                console.log("PACKAGE CREATE: Hid upload label");
            }
            
            // Show remove button
            var removeBtn = previewDiv.find('button[onclick*="removeDayImageCreate"]');
            if (removeBtn.length > 0) {
                removeBtn.css('display', 'flex');
                console.log("PACKAGE CREATE: Showed remove button");
            }
            
            console.log("PACKAGE CREATE: Successfully processed image for day", dayId, ":", img);
        } else {
            console.log("PACKAGE CREATE: No valid image to process for day", dayId);
        }
        
        // Clear the stored data
        window.selectedItineraryImage = null;
    }
}

// Listen for modal close event and process selected image
$(document).ready(function() {
    $(document).on('hidden.bs.modal', '#itinerary_detail_modal', function() {
        console.log("PACKAGE CREATE: Modal closed, processing selected image");
        setTimeout(function() {
            processSelectedItineraryImage();
        }, 100); // Small delay to ensure modal is fully closed
    });
});
</script>

<!-- <script>
function autoResize(el) {
    el.style.height = 'auto'; // reset previous height
    el.style.height = el.scrollHeight + 'px'; // set to new height based on content
}

// Optional: resize all day_program fields on load in case data is prefilled
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".day_program").forEach(function (el) {
        autoResize(el);
    });
});
</script> -->


<script>
    // oninput="autoResize(this)" 
// function autoResize(el) {
//     el.style.height = 'auto'; // reset previous height
//     el.style.height = el.scrollHeight + 'px'; // set to new height based on content
// }

// document.addEventListener("DOMContentLoaded", function () {
//     document.querySelectorAll(".day_program").forEach(function (el) {
//         autoResize(el);
//     });
// });

// $(document).on('shown.bs.modal', function () {
//     $('.day_program').each(function () {
//         autoResize(this);
//     });
// });
</script>

