<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    include "../../../../model/model.php";
    $dest_id = isset($_POST['dest_id']) ? $_POST['dest_id'] : '';
    $total_nights = isset($_POST['total_nights']) ? $_POST['total_nights'] : '';
    
    error_log("get_packages.php called - dest_id: " . $dest_id . ", total_nights: " . $total_nights);
} catch (Exception $e) {
    error_log("Error in get_packages.php: " . $e->getMessage());
    echo "Error: " . $e->getMessage();
    exit;
}
$count = 1;
$offset = 1;

// Build query with nights filter
$query = "select * from custom_package_master where dest_id = '$dest_id' and status!='Inactive'";
if (!empty($total_nights) && $total_nights != '') {
    // Ensure we're comparing the same data type
    $query .= " and total_nights = " . intval($total_nights);
}

// Debug information
echo "<!-- Debug: dest_id = " . $dest_id . " -->";
echo "<!-- Debug: total_nights = " . $total_nights . " -->";
echo "<!-- Debug: Query = " . $query . " -->";

$sq_tours = mysqlQuery($query);
$result_count = mysqli_num_rows($sq_tours);
echo "<!-- Debug: Result count = " . $result_count . " -->";
?>


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

</style>

<div class="col-md-12 app_accordion">
    <?php if (!empty($total_nights)) { ?>
    <div class="alert alert-info">
        <strong>Showing packages for <?= $total_nights ?> night<?= $total_nights > 1 ? 's' : '' ?></strong>
    </div>
    <?php } ?>
    <div class="panel-group main_block" id="accordion" role="tablist" aria-multiselectable="true">
        <?php
        $table_count = 0;
        $package_found = false;
        while ($row_tours = mysqli_fetch_assoc($sq_tours)) {
            $package_found = true;
        ?>
        <div class="package_selector">
            <input type="radio" value="<?php echo $row_tours['package_id']; ?>"
                id="<?php echo $row_tours['package_id']; ?>" name="custom_package" />
        </div>
        <div class="accordion_content package_content mg_bt_10">
            <div class="panel panel-default main_block">
                <div class="panel-heading main_block" role="tab" id="heading_<?= $count ?>">
                    <div class="Normal collapsed main_block" role="button" data-toggle="collapse"
                        data-parent="#accordion" href="#collapse_<?= $count; ?>" aria-expanded="false"
                        aria-controls="collapse_<?= $count; ?>" id="collapsed_<?= $count ?>">
                        <div class="col-md-12"><span><em style="margin-left: 15px;"><?php echo $row_tours['package_name'] . ' (' . $row_tours['total_days'] . 'D/' . $row_tours['total_nights'] . 'N )' ?></em></span>
                        </div>
                    </div>
                </div>
                <div id="collapse_<?= $count ?>" class="panel-collapse collapse main_block" role="tabpanel"
                    aria-labelledby="heading_<?= $count ?>">
                    <div class="panel-body">
                        <div class="col-md-12 no-pad" id="div_list1">
                            <div class="row mg_bt_10">
                                <div class="col-xs-12 text-right text_center_xs">
                                    <button type="button" class="btn btn-excel btn-sm"
                                        onClick="addItineraryRow('<?= $row_tours['package_id'] ?>')"><i
                                            class="fa fa-plus"></i></button>
                                    <button type="button" class="btn btn-pdf btn-sm"
                                        onClick="deleteRow('dynamic_table_list_p_<?= $row_tours['package_id'] ?>')"><i
                                            class="fa fa-trash"></i></button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table style="width: 100%" id="dynamic_table_list_p_<?= $row_tours['package_id'] ?>"
                                    name="dynamic_table_list_p_<?= $row_tours['package_id'] ?>"
                                    class="table table-bordered table-hover table-striped no-marg pd_bt_51 mg_bt_0">
                                    <legend>Tour Itinerary</legend>
                                    <?php
                    $offset1 = 0;
                    $sq_program = mysqlQuery("select * from custom_package_program where package_id='$row_tours[package_id]'");
                    $program_count = mysqli_num_rows($sq_program);
                    
                    // If no program entries exist, show one empty row
                    if ($program_count == 0) {
                        $offset1 = 1;
                        $row_program = array(
                            'attraction' => '',
                            'day_wise_program' => '',
                            'stay' => '',
                            'meal_plan' => '',
                            'day_image' => '',
                            'day_count' => 1
                        );
                        ?>
                        <tr>
                            <td style="width: 50px;"><input class="css-checkbox mg_bt_10"
                                    id="chk_program<?= $offset1 ?>" type="checkbox" checked><label
                                    class="css-label" style="margin-top: 55px;" for="chk_program<?= $offset1 ?>"> <label></td>
                            <td style="width: 50px;" class="hidden"><input maxlength="15"
                                    value="<?= $offset1 ?>" type="text" name="username" style="margin-top: 35px;"
                                    placeholder="Sr. No." class="form-control mg_bt_10" disabled /></td>
                            <td style="width: 100px;"><input type="text"
                                    id="special_attaraction<?php echo $offset1; ?>-u"
                                    onchange="validate_spaces(this.id);validate_spattration(this.id);"
                                    name="special_attaraction" class="form-control mg_bt_10"
                                    placeholder="*Special Attraction" title="Special Attraction"
                                    value="<?php echo $row_program['attraction']; ?>" style='width:220px;margin-top: 35px;' >
                            </td>
                            <td class='col-md-6 pad_8' style="max-width: 594px;overflow: hidden;position: relative;"><textarea id="day_program<?php echo $offset1; ?>-u" name="day_program" class="form-control mg_bt_10 day_program" placeholder="*Day-wise Program" title="Day-wise Program"  style=" height:900px;" onchange="validate_spaces(this.id);validate_dayprogram(this.id);" rows="3" value="<?php echo $row_program['day_wise_program']; ?>"><?php echo $row_program['day_wise_program']; ?></textarea><span class="style_text"><span class="style_text_b" data-wrapper="**" style="font-weight: bold; cursor: pointer;" title="Bold text">B</span><span class="style_text_u" data-wrapper="__" style="cursor: pointer;" title="Underline text"><u>U</u></span></span>
                            </td>
                            <td style="width: 100px;"><input type="text"
                                    id="overnight_stay<?php echo $offset1; ?>-u" name="overnight_stay"
                                    onchange="validate_spaces(this.id);validate_onstay(this.id);"
                                    class="form-control mg_bt_10" placeholder="*Overnight Stay"
                                    title="Overnight Stay"  value="<?php echo $row_program['stay']; ?>"
                                    style='width:170px;margin-top: 35px;'></td>
                            <td><select id="meal_plan<?php echo $offset1; ?>-u" title="Meal Plan"
                                    name="meal_plan" class="form-control mg_bt_10" style='width: 140px;margin-top: 35px;'>
                                    <option value="">Select Meal Plan</option>
                                    <?php get_mealplan_dropdown(); ?>
                            </select></td>
                            <td class='col-md-1 pad_8'><button type="button" class="btn btn-info btn-iti btn-sm" style="border:none;margin-top: 35px;" title="Add Itinerary" id="itinerary<?php echo $offset1; ?>" onclick="add_itinerary('dest_name','special_attaraction<?php echo $offset1; ?>-u','day_program<?php echo $offset1; ?>-u','overnight_stay<?php echo $offset1; ?>-u','Day-<?= $offset1 ?>')"><i class="fa fa-plus"></i></button>
                            </td>
                            <td class='col-md-1 pad_8' style="width: 120px;">
                                <!-- Debug: Image path = <?= $row_program['day_image'] ?? 'NULL' ?> -->
                                <div style="margin-top: 35px;">
                                    <label for="day_image_<?php echo $offset1; ?>" class="btn btn-sm btn-success" 
                                           style="margin-bottom: 5px; padding: 6px 12px; font-size: 12px; cursor: pointer; border-radius: 4px; border: none; background-color: #28a745; color: white; font-weight: 500; <?= (!empty($row_program['day_image']) && trim($row_program['day_image']) !== '' && trim($row_program['day_image']) !== 'NULL') ? 'display:none;' : '' ?>">
                                        <i class="fa fa-image"></i> Upload Image
                                    </label>
                                    <input type="file" id="day_image_<?php echo $offset1; ?>" 
                                           name="day_image_<?php echo $offset1; ?>" accept="image/*" 
                                           onchange="previewDayImage(this, '<?php echo $offset1; ?>')" 
                                           style="display: none;">
                                </div>
                                <div id="day_image_preview_<?php echo $offset1; ?>" style="<?= (!empty($row_program['day_image']) && trim($row_program['day_image']) !== '' && trim($row_program['day_image']) !== 'NULL') ? 'display:block;' : 'display:none;' ?> margin-top: 5px;">
                                    <div style="height:100px; max-height: 100px; overflow:hidden; position: relative; width: 100px; border: 2px solid #ddd; border-radius: 8px; background-color: #f8f9fa;">
                                        <img id="preview_img_<?php echo $offset1; ?>" src="<?php 
                                            if (!empty($row_program['day_image'])) {
                                                $image_path = trim($row_program['day_image']);
                                                // Debug the actual path
                                                error_log("QUOTATION: Image path from DB: " . $image_path);
                                                
                                                // Check if path is valid and not empty
                                                if ($image_path && $image_path !== '' && $image_path !== 'NULL') {
                                                    // Check if path already starts with http
                                                    if (strpos($image_path, 'http') === 0) {
                                                        echo $image_path;
                                                    } else {
                                                        // For package images, use project root URL
                                                        $project_base_url = str_replace('/crm/', '/', BASE_URL);
                                                        $project_base_url = rtrim($project_base_url, '/');
                                                        $image_path = ltrim($image_path, '/');
                                                        $final_url = $project_base_url . '/' . $image_path;
                                                        error_log("QUOTATION: Final image URL: " . $final_url);
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
                                             onerror="console.log('QUOTATION: Existing image failed to load:', this.src); this.style.display='none'; this.parentElement.parentElement.style.display='none'; this.parentElement.parentElement.parentElement.querySelector('label').style.display='block'; this.parentElement.querySelector('button[onclick*=removeDayImage]').style.display='none';"
                                             onload="console.log('QUOTATION: Image loaded successfully:', this.src);">
                                        <button type="button" 
                                                onclick="removeDayImage('<?php echo $offset1; ?>')" 
                                                title="Remove Image" 
                                                style="position: absolute; top: 5px; right: 5px; width: 20px; height: 20px; border: none; border-radius: 50%; background-color: #dc3545; color: white; font-size: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2); <?= (empty($row_program['day_image']) || trim($row_program['day_image']) === '' || trim($row_program['day_image']) === 'NULL') ? 'display:none;' : '' ?>">
                                            ×
                                        </button>
                                    </div>
                                </div>
                                <input type="hidden" id="existing_image_path_<?php echo $offset1; ?>" name="existing_image_path_<?php echo $offset1; ?>" value="<?= $row_program['day_image'] ?? '' ?>" />
                            </td>
                            <td class="hidden"><input type="hidden" name="package_id_n" value=""></td>
                        </tr>
                        <?php
                    } else {
                        // Show existing program entries
                        while ($row_program = mysqli_fetch_assoc($sq_program)) {
                            $offset1++;
                            $current_offset = $offset1; // Use consistent offset for this row
                            ?>
                        <tr>
                            <td style="width: 50px;"><input class="css-checkbox mg_bt_10"
                                    id="chk_program<?= $current_offset ?>" type="checkbox" checked><label
                                    class="css-label" style="margin-top: 55px;" for="chk_program<?= $current_offset ?>"> <label></td>
                            <td style="width: 50px;" class="hidden"><input maxlength="15"
                                    value="<?= $current_offset ?>" type="text" name="username" style="margin-top: 35px;"
                                    placeholder="Sr. No." class="form-control mg_bt_10" disabled /></td>
                            <td style="width: 100px;"><input type="text"
                                    id="special_attaraction<?php echo $current_offset; ?>-u"
                                    onchange="validate_spaces(this.id);validate_spattration(this.id);"
                                    name="special_attaraction" class="form-control mg_bt_10"
                                    placeholder="*Special Attraction" title="Special Attraction"
                                    value="<?php echo $row_program['attraction']; ?>" style='width:220px;margin-top: 35px;' >
                            </td>
                            <!-- <td style="max-width: 594px;overflow: hidden;width:100px"><textarea
                                    id="day_program<?php echo $offset; ?>-u" name="day_program"
                                    class="form-control mg_bt_10" title="Day-wise Program" rows="3"
                                    placeholder="*Day-wise Program"
                                    onchange="validate_spaces(this.id);validate_dayprogram(this.id);"
                                    style='width:400px'
                                    value="<?php echo $row_program['day_wise_program']; ?>"><?php echo $row_program['day_wise_program']; ?></textarea>
                            </td> -->
                            <!-- <td  style="max-width: 594px;overflow: hidden;width:100px;"><textarea id="day_program<?php echo $offset; ?>-u" name="day_program" class="form-control mg_bt_10 day_program" placeholder="*Day-wise Program" title="Day-wise Program" onchange="validate_spaces(this.id);validate_dayprogram(this.id);" rows="3" style='width:400px'
                            value="<?php echo $row_program['day_wise_program']; ?>"><?php echo $row_program['day_wise_program']; ?></textarea><span class="style_text"><span class="style_text_b" data-wrapper="**" style="font-weight: bold; cursor: pointer;" title="Bold text">B</span><span class="style_text_u" data-wrapper="__" style="cursor: pointer;" title="Underline text"><u>U</u></span></span>
                            </td> -->
                            <td class='col-md-6 pad_8' style="max-width: 594px;overflow: hidden;position: relative;"><textarea id="day_program<?php echo $current_offset; ?>-u" name="day_program" class="form-control mg_bt_10 day_program" placeholder="*Day-wise Program" title="Day-wise Program"  style=" height:900px;" onchange="validate_spaces(this.id);validate_dayprogram(this.id);" rows="3" value="<?php echo $row_program['day_wise_program']; ?>"><?php echo $row_program['day_wise_program']; ?></textarea><span class="style_text"><span class="style_text_b" data-wrapper="**" style="font-weight: bold; cursor: pointer;" title="Bold text">B</span><span class="style_text_u" data-wrapper="__" style="cursor: pointer;" title="Underline text"><u>U</u></span></span>
                            </td>
                            <td style="width: 100px;"><input type="text"
                                    id="overnight_stay<?php echo $current_offset; ?>-u" name="overnight_stay"
                                    onchange="validate_spaces(this.id);validate_onstay(this.id);"
                                    class="form-control mg_bt_10" placeholder="*Overnight Stay"
                                    title="Overnight Stay"  value="<?php echo $row_program['stay']; ?>"
                                    style='width:170px;margin-top: 35px;'></td>
                            <td><select id="meal_plan<?php echo $current_offset; ?>-u" title="Meal Plan"
                                    name="meal_plan" class="form-control mg_bt_10" style='width: 140px;margin-top: 35px;'>
                                    <?php if ($row_program['meal_plan'] != '') { ?><option
                                        value="<?php echo $row_program['meal_plan']; ?>">
                                        <?php echo $row_program['meal_plan']; ?></option>
                                    <?php } ?>
                                    <?php get_mealplan_dropdown(); ?>
                                </select></td>
                            <td class='col-md-1 pad_8'><button type="button"
                                    class="btn btn-info btn-iti btn-sm" style="border:none;margin-top: 35px;"
                                    id="itinerary<?php echo $current_offset; ?>" title="Add Itinerary"
                                    onClick="add_itinerary('dest_name','special_attaraction<?php echo $current_offset; ?>-u','day_program<?php echo $current_offset; ?>-u','overnight_stay<?php echo $current_offset; ?>-u','Day-<?= $current_offset ?>')"><i
                                        class="fa fa-plus"></i></button>
                            </td>
                            <td class='col-md-1 pad_8' style="width: 120px;">
                                <!-- Debug: Image path = <?= $row_program['day_image'] ?? 'NULL' ?> -->
                                <div style="margin-top: 35px;">
                                    <label for="day_image_<?php echo $current_offset; ?>" class="btn btn-sm btn-success" 
                                           style="margin-bottom: 5px; padding: 6px 12px; font-size: 12px; cursor: pointer; border-radius: 4px; border: none; background-color: #28a745; color: white; font-weight: 500; <?= (!empty($row_program['day_image']) && trim($row_program['day_image']) !== '' && trim($row_program['day_image']) !== 'NULL') ? 'display:none;' : '' ?>">
                                        <i class="fa fa-image"></i> Upload Image
                                    </label>
                                    <input type="file" id="day_image_<?php echo $current_offset; ?>" 
                                           name="day_image_<?php echo $current_offset; ?>" accept="image/*" 
                                           onchange="previewDayImage(this, '<?php echo $current_offset; ?>')" 
                                           style="display: none;">
                                </div>
                                <div id="day_image_preview_<?php echo $current_offset; ?>" style="<?= (!empty($row_program['day_image']) && trim($row_program['day_image']) !== '' && trim($row_program['day_image']) !== 'NULL') ? 'display:block;' : 'display:none;' ?> margin-top: 5px;">
                                    <div style="height:100px; max-height: 100px; overflow:hidden; position: relative; width: 100px; border: 2px solid #ddd; border-radius: 8px; background-color: #f8f9fa;">
                                        <img id="preview_img_<?php echo $current_offset; ?>" src="<?php 
                                            if (!empty($row_program['day_image'])) {
                                                $image_path = trim($row_program['day_image']);
                                                // Debug the actual path
                                                error_log("QUOTATION: Image path from DB for offset " . $current_offset . ": " . $image_path);
                                                
                                                // Check if path is valid and not empty
                                                if ($image_path && $image_path !== '' && $image_path !== 'NULL') {
                                                    // Check if path already starts with http
                                                    if (strpos($image_path, 'http') === 0) {
                                                        echo $image_path;
                                                    } else {
                                                        // For package images, use project root URL
                                                        $project_base_url = str_replace('/crm/', '/', BASE_URL);
                                                        $project_base_url = rtrim($project_base_url, '/');
                                                        $image_path = ltrim($image_path, '/');
                                                        $final_url = $project_base_url . '/' . $image_path;
                                                        error_log("QUOTATION: Final image URL for offset " . $current_offset . ": " . $final_url);
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
                                             onerror="console.log('QUOTATION: Existing image failed to load for offset <?php echo $current_offset; ?>:', this.src); this.style.display='none'; this.parentElement.parentElement.style.display='none'; this.parentElement.parentElement.parentElement.querySelector('label').style.display='block'; this.parentElement.querySelector('button[onclick*=removeDayImage]').style.display='none';"
                                             onload="console.log('QUOTATION: Image loaded successfully for offset <?php echo $current_offset; ?>:', this.src);">
                                        <button type="button" 
                                                onclick="removeDayImage('<?php echo $current_offset; ?>')" 
                                                title="Remove Image" 
                                                style="position: absolute; top: 5px; right: 5px; width: 20px; height: 20px; border: none; border-radius: 50%; background-color: #dc3545; color: white; font-size: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2); <?= (empty($row_program['day_image']) || trim($row_program['day_image']) === '' || trim($row_program['day_image']) === 'NULL') ? 'display:none;' : '' ?>">
                                            ×
                                        </button>
                                    </div>
                                </div>
                                <input type="hidden" id="existing_image_path_<?php echo $current_offset; ?>" name="existing_image_path_<?php echo $current_offset; ?>" value="<?= $row_program['day_image'] ?? '' ?>" />
                            </td>
                            <td style="width: 100px;"><input style="display:none" type="text"
                                    name="package_id_n" value="<?php echo $row_tours['package_id']; ?>">
                            </td>
                        </tr>
                        <?php 
                        // $offset is not used anymore, using $current_offset instead
                        }
                    } ?>
                        </table>
                    </div>
                    <div class="row mg_tp_20">
                        <div class="col-md-6">
                            <legend>Inclusions</legend>
                        </div>
                        <div class="col-md-6">
                            <legend>Exclusions</legend>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <table style="width:100%" class="no-marg"
                                id="dynamic_table_incl<?= $row_tours['package_id'] ?>"
                                name="dynamic_table_incl<?= $row_tours['package_id'] ?>">
                                <tr>
                                    <td class="col-md-6"><textarea class="feature_editor"
                                            id="inclusions<?= $row_tours['package_id'] ?>" name="inclusions"
                                            placeholder="Inclusions" title="Inclusions"
                                            rows="4"><?php echo $row_tours['inclusions']; ?></textarea></td>
                                    <td class="col-md-6"><textarea class="feature_editor"
                                            id="exclusions<?= $row_tours['package_id'] ?>" name="exclusions"
                                            placeholder="Exclusions" title="Exclusions"
                                            rows="4"><?php echo $row_tours['exclusions']; ?></textarea></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$count++;
$table_count++;
$_SESSION['id'] = $row_tours['package_id'];
} 


// Show message if no packages found for selected nights
if (!$package_found && !empty($total_nights)) {
    echo '<div class="alert alert-info text-center">';
    echo '<h4>No packages found for ' . $total_nights . ' night' . ($total_nights > 1 ? 's' : '') . '</h4>';
    echo '<p>Please try selecting a different number of nights or create a new package for this duration.</p>';
    echo '</div>';
} elseif (!$package_found) {
    echo '<div class="alert alert-warning text-center">';
    echo '<h4>No packages available</h4>';
    echo '<p>Please select a destination and number of nights to see available packages.</p>';
    echo '</div>';
}
?>
    </div>
</div>
<script>
// Day image preview functions  
function previewDayImage(input, offset) {
    console.log("previewDayImage called with offset:", offset);
    console.log("Input element:", input);
    console.log("Files:", input.files);
    
    if (input.files && input.files[0]) {
        var file = input.files[0];
        console.log("Selected file:", file.name, file.size, file.type);
        
        // Validate file type
        var allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        var fileType = file.type.toLowerCase();
        
        if (!allowedTypes.includes(fileType)) {
            alert('Please select a valid image file (JPEG, PNG, GIF, or WebP)');
            input.value = '';
            return;
        }
        
        // Validate file size (max 5MB)
        var maxSize = 5 * 1024 * 1024; // 5MB
        if (file.size > maxSize) {
            alert('File size too large. Maximum size is 5MB.');
            input.value = '';
            return;
        }
        
        var reader = new FileReader();
        reader.onload = function(e) {
            console.log("QUOTATION: FileReader loaded, showing preview for offset:", offset);
            $('#preview_img_' + offset).attr('src', e.target.result);
            $('#day_image_preview_' + offset).show();
            
            // Show the remove button when image is selected
            $('#day_image_preview_' + offset).find('button[onclick*="removeDayImage"]').css('display', 'flex');
            
            // Hide the upload button when image is uploaded
            $('label[for="day_image_' + offset + '"]').hide();
            
            // Store image data for later upload
            if (!window.quotationImages) {
                window.quotationImages = {};
            }
            
            // Get package ID for this row
            var packageId = getPackageIdForOffset(offset);
            
            // Store image by offset for later upload (when quotation is saved)
            window.quotationImages[offset] = {
                file: file,
                offset: offset,
                package_id: packageId,
                day_number: offset,
                preview_url: e.target.result,
                uploaded: false
            };
            
            // Update button text to indicate it will be uploaded when quotation is saved
            $('#upload_btn_' + offset).text('Will Upload on Save');
            
            console.log("DEBUG: Stored image for offset " + offset + ":", file.name, "Package ID:", packageId);
            console.log("DEBUG: Full stored object:", window.quotationImages[offset]);
            console.log("DEBUG: Total stored images:", Object.keys(window.quotationImages).length);
        }
        reader.onerror = function() {
            console.error("FileReader error");
            alert('Error reading file');
        }
        reader.readAsDataURL(file);
    } else {
        console.log("No file selected");
    }
}

function removeDayImage(offset) {
    console.log("QUOTATION: Removing image for offset:", offset);
    
    // Clear file input
    $('#day_image_' + offset).val('');
    
    // Hide preview and remove button
    var previewDiv = $('#day_image_preview_' + offset);
    previewDiv.hide();
    previewDiv.find('button[onclick*="removeDayImage"]').hide();
    
    // Clear image source
    $('#preview_img_' + offset).attr('src', '');
    
    // Show upload button again
    $('label[for="day_image_' + offset + '"]').show();
    
    // Clear existing image path
    $('#existing_image_path_' + offset).val('');
    
    // Clear stored image data
    if (window.quotationImages && window.quotationImages[offset]) {
        delete window.quotationImages[offset];
        console.log("QUOTATION: Cleared stored image for offset:", offset);
    }
    
    console.log("QUOTATION: Image removed successfully for offset:", offset);
}

// Function to get package ID for a specific offset
function getPackageIdForOffset(offset) {
    // Try to find package ID from the current row
    var packageIdInput = $('input[name="package_id_n"]').first();
    var packageId = packageIdInput.val() || '1'; // Default to 1 if not found
    
    console.log("DEBUG: Getting package ID for offset", offset, ":", packageId);
    return packageId;
}

// Handle day image upload (deferred until quotation save)
function uploadDayImage(offset) {
    console.log("uploadDayImage called with offset:", offset);
    
    // Check if image is already stored
    if (!window.quotationImages || !window.quotationImages[offset]) {
        alert('Please select an image first');
        return;
    }
    
    // Show message that image will be uploaded when quotation is saved
    $('#upload_status_' + offset).html('<span style="color: blue;">✓ Ready for upload on save</span>');
    $('#upload_btn_' + offset).prop('disabled', true).text('Ready to Upload');
    
    console.log("Image marked for upload when quotation is saved:", window.quotationImages[offset]);
}

// Global counter for ensuring unique offsets across all packages
if (typeof window.quotationOffsetCounter === 'undefined') {
    window.quotationOffsetCounter = 1000; // Start from 1000 to avoid conflicts with existing rows
}

// Initialize counter based on existing rows when page loads
$(document).ready(function() {
    // Find the highest existing offset across all tables
    var maxExistingOffset = 0;
    $('table[id^="dynamic_table_list_p_"]').each(function() {
        $(this).find('input[name="username"]').each(function() {
            var currentOffset = parseInt($(this).val()) || 0;
            if (currentOffset > maxExistingOffset) {
                maxExistingOffset = currentOffset;
            }
        });
    });
    
    // Set counter to be higher than any existing offset
    if (maxExistingOffset > 0) {
        window.quotationOffsetCounter = Math.max(window.quotationOffsetCounter, maxExistingOffset + 100);
    }
    
    console.log("QUOTATION: Initialized offset counter to:", window.quotationOffsetCounter, "based on max existing:", maxExistingOffset);
});

// Add new itinerary row function
function addItineraryRow(package_id) {
    var table = document.getElementById('dynamic_table_list_p_' + package_id);
    if (!table) {
        console.error('Table not found: dynamic_table_list_p_' + package_id);
        return;
    }
    
    var rowCount = table.rows.length;
    var newRow = table.insertRow(rowCount);
    
    // Use global counter to ensure absolutely unique offset
    window.quotationOffsetCounter++;
    var offset = window.quotationOffsetCounter;
    
    console.log("QUOTATION: Adding new row with unique offset:", offset, "for package:", package_id);
    
    // Create the new row HTML
    newRow.innerHTML = `
        <td style="width: 50px;">
            <input class="css-checkbox mg_bt_10" id="chk_program${offset}" type="checkbox" checked>
            <label class="css-label" style="margin-top: 55px;" for="chk_program${offset}"></label>
        </td>
        <td style="width: 50px;" class="hidden">
            <input maxlength="15" value="${offset}" type="text" name="username" placeholder="Sr. No." class="form-control mg_bt_10" disabled />
        </td>
        <td style="width: 100px;">
            <input type="text" id="special_attaraction${offset}-u" onchange="validate_spaces(this.id);validate_spattration(this.id);" name="special_attaraction" class="form-control mg_bt_10" placeholder="Special Attraction" title="Special Attraction" style='width:220px;margin-top: 35px;'>
        </td>
        <td class='col-md-5 pad_8' style="max-width: 594px;overflow: hidden;position: relative;">
            <textarea id="day_program${offset}-u" name="day_program" class="form-control mg_bt_10 day_program" placeholder="Day-wise Program" title="Day-wise Program" onchange="validate_spaces(this.id);validate_dayprogram(this.id);" rows="3" style="height:900px;"></textarea>
            <span class="style_text">
                <span class="style_text_b" data-wrapper="**" style="font-weight: bold; cursor: pointer;" title="Bold text">B</span>
                <span class="style_text_u" data-wrapper="__" style="cursor: pointer;" title="Underline text"><u>U</u></span>
            </span>
        </td>
        <td style="width: 100px;">
            <input type="text" id="overnight_stay${offset}-u" name="overnight_stay" onchange="validate_spaces(this.id);validate_onstay(this.id);" class="form-control mg_bt_10" placeholder="Overnight Stay" title="Overnight Stay" style='width:170px;margin-top: 35px;'>
        </td>
        <td>
            <select id="meal_plan${offset}-u" title="Meal Plan" name="meal_plan" class="form-control mg_bt_10" style='width: 140px;margin-top: 35px;'>
                <option value="">Meal Plan</option>
                <option value="Breakfast">Breakfast</option>
                <option value="Lunch">Lunch</option>
                <option value="Dinner">Dinner</option>
                <option value="All Meals">All Meals</option>
            </select>
        </td>
        <td class='col-md-1 pad_8'>
            <button type="button" class="btn btn-info btn-iti btn-sm" style="border:none;margin-top: 35px;" title="Add Itinerary" id="itinerary${offset}" onClick="add_itinerary('dest_name','special_attaraction${offset}-u','day_program${offset}-u','overnight_stay${offset}-u','Day-${offset}')">
                <i class="fa fa-plus"></i>
            </button>
        </td>
        <td class='col-md-1 pad_8' style="width: 120px;">
            <div style="margin-top: 35px;">
                <label for="day_image_${offset}" class="btn btn-sm btn-success" style="margin-bottom: 5px; padding: 6px 12px; font-size: 12px; cursor: pointer; border-radius: 4px; border: none; background-color: #28a745; color: white; font-weight: 500;">
                    Upload Image
                </label>
                <input type="file" id="day_image_${offset}" name="day_image_${offset}" accept="image/*" onchange="previewDayImage(this, '${offset}')" style="display: none;">
            </div>
            <div id="day_image_preview_${offset}" style="display: none; margin-top: 5px;">
                <div style="height:100px; max-height: 100px; overflow:hidden; position: relative; width: 100px; border: 2px solid #ddd; border-radius: 8px; background-color: #f8f9fa;">
                    <img id="preview_img_${offset}" src="" alt="Preview" style="width:100%; height:100%; object-fit: cover; border-radius: 6px;">
                    <button type="button" 
                            onclick="removeDayImage('${offset}')" 
                            title="Remove Image" 
                            style="position: absolute; top: 5px; right: 5px; width: 20px; height: 20px; border: none; border-radius: 50%; background-color: #dc3545; color: white; font-size: 12px; cursor: pointer; display: none; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                        ×
                    </button>
                </div>
                <div style="margin-top: 5px; text-align: center;">
                    <button type="button" 
                            id="upload_btn_${offset}" 
                            onclick="uploadDayImage('${offset}')" 
                            class="btn btn-sm btn-primary" 
                            style="padding: 4px 8px; font-size: 11px; border-radius: 4px;">
                        Upload
                    </button>
                </div>
                <div id="upload_status_${offset}" style="margin-top: 2px; font-size: 10px;"></div>
            </div>
            <input type="hidden" id="existing_image_path_${offset}" name="existing_image_path_${offset}" value="" />
        </td>
        <td style="width: 100px;">
            <input style="display:none" type="text" name="package_id_n" value="${package_id}">
        </td>
    `;
}

$(document).on("click", ".style_text_b, .style_text_u", function() {
    var wrapper = $(this).data("wrapper");
    
    // Get the textarea element
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
		var text=textarea.value;
		 var content = text.replace(/\*\*(.*?)\*\*/g, '<b>$1</b>');

		// Replace markdown-style underline (__text__) with <u> tags
		content = content.replace(/__(.*?)__/g, '<u>$1</u>');
		textarea.value =content;
		//console.log(content);    
});


</script>
<script src="<?= BASE_URL ?>js/app/footer_scripts.js"></script>