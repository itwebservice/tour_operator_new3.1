<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    include "../../../../model/model.php";
    $dest_id = isset($_POST['dest_id']) ? $_POST['dest_id'] : '';
    $total_nights = isset($_POST['total_nights']) ? $_POST['total_nights'] : '';
    $current_package_id = isset($_POST['current_package_id']) ? $_POST['current_package_id'] : '';
    $quotation_id = isset($_POST['quotation_id']) ? $_POST['quotation_id'] : '';
    
    error_log("get_packages.php called - dest_id: " . $dest_id . ", total_nights: " . $total_nights . ", current_package_id: " . $current_package_id . ", quotation_id: " . $quotation_id);
    error_log("is_new_quotation: " . (empty($quotation_id) ? 'YES' : 'NO'));
} catch (Exception $e) {
    error_log("Error in get_packages.php: " . $e->getMessage());
    echo "Error: " . $e->getMessage();
    exit;
}
$count = 1;
$offset = 1;

// Helper function to check image existence in multiple locations
function findImageUrl($image_path, $is_new_quotation = false) {
    if (empty($image_path) || $image_path === 'NULL') {
        return '';
    }

    $image_path = trim($image_path);

    // Check if path already starts with http
    if (strpos($image_path, 'http') === 0) {
        return $image_path;
    }

    // Get project base URL
    $project_base_url = str_replace('/crm/', '/', BASE_URL);
    $project_base_url = rtrim($project_base_url, '/');

    $image_path_clean = ltrim($image_path, '/');

    // For new quotations, prioritize itinerary_images folder
    if ($is_new_quotation) {
        error_log("QUOTATION: Checking itinerary_images folder for new quotation - image: " . $image_path_clean);
        // Check itinerary_images folder first for new quotations
        $itinerary_images_path = "../../../../../uploads/itinerary_images/" . basename($image_path_clean);
        error_log("QUOTATION: Checking path: " . $itinerary_images_path);
        if (file_exists($itinerary_images_path)) {
            error_log("QUOTATION: Found image in itinerary_images folder (new quotation priority): " . $itinerary_images_path);
            $itinerary_images_url = $project_base_url . '/uploads/itinerary_images/' . basename($image_path_clean);
            return $itinerary_images_url;
        } else {
            error_log("QUOTATION: Image not found in itinerary_images folder: " . $itinerary_images_path);
        }
    }

    // Check original path first
    $original_url = $project_base_url . '/' . $image_path_clean;
    $original_file_path = "../../../" . $image_path_clean;

    if (file_exists($original_file_path)) {
        error_log("QUOTATION: Found image in original location: " . $original_file_path);
        return $original_url;
    }

    // Check quotation_images folder (full path as stored in database)
    if (strpos($image_path_clean, 'uploads/quotation_images/') === 0) {
        // For database paths like "uploads/quotation_images/filename.jpg", 
        // we need to check in crm/uploads/quotation_images/ folder
        $quotation_images_url = $project_base_url . '/crm/' . $image_path_clean;
        $quotation_images_file_path = "../../../../" . $image_path_clean;

        if (file_exists($quotation_images_file_path)) {
            error_log("QUOTATION: Found image in quotation_images folder (database path): " . $quotation_images_file_path);
            return $quotation_images_url;
        }
    }

    // Check quotation_images folder (with just filename, not full path)
    $quotation_images_path = "crm/uploads/quotation_images/" . basename($image_path_clean);
    $quotation_images_url = $project_base_url . '/' . $quotation_images_path;
    $quotation_images_file_path = "../../../../" . $quotation_images_path;

    if (file_exists($quotation_images_file_path)) {
        error_log("QUOTATION: Found image in quotation_images folder: " . $quotation_images_file_path);
        return $quotation_images_url;
    }

    // Check CRM uploads folder directly
    $crm_uploads_path = "../../../../crm/uploads/" . basename($image_path_clean);
    if (file_exists($crm_uploads_path)) {
        error_log("QUOTATION: Found image in CRM uploads folder: " . $crm_uploads_path);
        $crm_uploads_url = $project_base_url . '/crm/uploads/' . basename($image_path_clean);
        return $crm_uploads_url;
    }

    // Check CRM uploads quotation_images folder directly
    $crm_quotation_images_path = "../../../../crm/uploads/quotation_images/" . basename($image_path_clean);
    if (file_exists($crm_quotation_images_path)) {
        error_log("QUOTATION: Found image in CRM uploads quotation_images folder: " . $crm_quotation_images_path);
        $crm_quotation_images_url = $project_base_url . '/crm/uploads/quotation_images/' . basename($image_path_clean);
        return $crm_quotation_images_url;
    }

    // Check itinerary_images folder as fallback (for both new and existing quotations)
    $itinerary_images_path = "../../../../../uploads/itinerary_images/" . basename($image_path_clean);
    error_log("QUOTATION: Checking itinerary_images fallback path: " . $itinerary_images_path);
    if (file_exists($itinerary_images_path)) {
        error_log("QUOTATION: Found image in itinerary_images folder (fallback): " . $itinerary_images_path);
        $itinerary_images_url = $project_base_url . '/uploads/itinerary_images/' . basename($image_path_clean);
        return $itinerary_images_url;
    } else {
        error_log("QUOTATION: Image not found in itinerary_images fallback path: " . $itinerary_images_path);
    }

    error_log("QUOTATION: Image not found in any location: " . $image_path);
    return '';
}

// Always load all available packages for the destination and nights (both new and edit mode)
$query = "select * from custom_package_master where dest_id = '$dest_id' and status!='Inactive'";
if (!empty($total_nights) && $total_nights != '') {
    // Ensure we're comparing the same data type
    $query .= " and total_nights = " . intval($total_nights);
}

error_log("Loading package data for dest_id: " . $dest_id . ", total_nights: " . $total_nights . ", quotation_id: " . $quotation_id);

// Debug information
echo "<!-- Debug: dest_id = " . $dest_id . " -->";
echo "<!-- Debug: total_nights = " . $total_nights . " -->";
echo "<!-- Debug: quotation_id = " . $quotation_id . " -->";
echo "<!-- Debug: current_package_id = " . $current_package_id . " -->";
echo "<!-- Debug: Query = " . $query . " -->";
echo "<!-- Debug: Is update operation = " . (!empty($quotation_id) ? 'YES' : 'NO') . " -->";

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

/* Prevent content jumping during loading */
.package_content {
    min-height: 200px;
    transition: opacity 0.3s ease;
}

.package_content.loading {
    opacity: 0.7;
    pointer-events: none;
}

/* Smooth transitions for accordion */
.accordion_content {
    transition: all 0.3s ease;
}

/* Simple radio button stability fix */
.package_selector {
    position: relative;
    display: inline-block;
    margin-right: 10px;
}

.package_selector input[type="radio"] {
    margin-right: 5px;
    vertical-align: middle;
}

/* Upload button styling with left padding */
label[class*="upload-btn-"] {
    margin-left: 15px !important;
    padding-left: 38px !important;
}

/* For dynamically added rows */
.upload-btn-dynamic {
    margin-left: 15px !important;
    padding-left: 38px !important;
}

/* General upload button styling */
.btn-success[class*="upload-btn"] {
    margin-left: 15px !important;
    padding-left: 38px !important;
}

</style>

<div class="col-md-12 app_accordion">
    <?php if (!empty($total_nights)) { ?>
    <div class="alert alert-info">
        <strong>Showing packages for <?= $total_nights ?> night<?= $total_nights > 1 ? 's' : '' ?></strong>
    </div>
    <?php } ?>
    
    <!-- Loading indicator -->
    <div id="package_loading_indicator" style="display: none; text-align: center; padding: 20px;">
        <i class="fa fa-spinner fa-spin"></i> Loading packages...
    </div>
    
    <div class="panel-group main_block" id="accordion" role="tablist" aria-multiselectable="true">
        <?php
        $table_count = 0;
        $package_found = false;
        while ($row_tours = mysqli_fetch_assoc($sq_tours)) {
            $package_found = true;
        ?>
        <div class="package_selector">
            <input type="radio" value="<?php echo $row_tours['package_id']; ?>"
                id="<?php echo $row_tours['package_id']; ?>" name="custom_package" 
                <?php echo ($current_package_id && $row_tours['package_id'] == $current_package_id) ? 'checked' : ''; ?> />
            <!-- Debug: Package ID = <?php echo $row_tours['package_id']; ?>, Current = <?php echo $current_package_id; ?>, Checked = <?php echo ($current_package_id && $row_tours['package_id'] == $current_package_id) ? 'YES' : 'NO'; ?> -->
        </div>
        <div class="accordion_content package_content mg_bt_10">
            <div class="panel panel-default main_block">
                <div class="panel-heading main_block" role="tab" id="heading_<?= $count ?>">
                    <div class="Normal <?php echo ($current_package_id && $row_tours['package_id'] == $current_package_id) ? '' : 'collapsed'; ?> main_block" role="button" data-toggle="collapse"
                        data-parent="#accordion" href="#collapse_<?= $count; ?>" aria-expanded="<?php echo ($current_package_id && $row_tours['package_id'] == $current_package_id) ? 'true' : 'false'; ?>"
                        aria-controls="collapse_<?= $count; ?>" id="collapsed_<?= $count ?>">
                        <div class="col-md-12"><span><em style="margin-left: 15px;"><?php echo $row_tours['package_name'] . ' (' . $row_tours['total_days'] . 'D/' . $row_tours['total_nights'] . 'N )' ?></em></span>
                        </div>
                    </div>
                </div>
                <div id="collapse_<?= $count ?>" class="panel-collapse collapse <?php echo ($current_package_id && $row_tours['package_id'] == $current_package_id) ? 'in' : ''; ?> main_block" role="tabpanel"
                    aria-labelledby="heading_<?= $count ?>" aria-expanded="<?php echo ($current_package_id && $row_tours['package_id'] == $current_package_id) ? 'true' : 'false'; ?>">
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
                    
                    // Load itinerary data from the correct table based on operation type
                    if (!empty($quotation_id)) {
                        // For update operations, load from package_quotation_program
                        $sq_program = mysqlQuery("select * from package_quotation_program where package_id='$row_tours[package_id]' and quotation_id='$quotation_id' order by day_count");
                        error_log("Loading itinerary from package_quotation_program for package_id: " . $row_tours['package_id'] . ", quotation_id: " . $quotation_id);
                        echo "<!-- Debug: Loading from package_quotation_program for quotation_id: " . $quotation_id . " -->";
                    } else {
                        // For new quotations, load from custom_package_program
                        $sq_program = mysqlQuery("select * from custom_package_program where package_id='$row_tours[package_id]'");
                        error_log("Loading itinerary from custom_package_program for package_id: " . $row_tours['package_id']);
                        echo "<!-- Debug: Loading from custom_package_program for new quotation -->";
                    }
                    
                    $program_count = mysqli_num_rows($sq_program);
                    echo "<!-- Debug: Program count: " . $program_count . " -->";
                    
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
                                    <label for="day_image_<?php echo $offset1; ?>" class="btn btn-sm btn-success upload-btn-<?php echo $offset1; ?>" 
                                           style="margin-bottom: 5px; padding: 6px 12px; font-size: 12px; cursor: pointer; border-radius: 4px; border: none; background-color: #28a745; color: white; font-weight: 500; <?= (!empty($row_program['day_image']) && trim($row_program['day_image']) !== '' && trim($row_program['day_image']) !== 'NULL') ? 'display:none;' : '' ?>">
                                        <i class="fa fa-image"></i> Upload Image
                                    </label>
                                    <input type="file" id="day_image_<?php echo $offset1; ?>" 
                                           name="day_image_<?php echo $offset1; ?>" accept="image/*" 
                                           onchange="previewDayImage(this, '<?php echo $offset1; ?>')" 
                                           style="display: none;">
                                    <div class="image-requirements-tooltip" style="font-size: 10px; color: white; margin-top: 3px; line-height: 1.2; background-color: #000; padding:8px; border-radius: 3px; max-width: 147px; <?= (!empty($row_program['day_image']) && trim($row_program['day_image']) !== '' && trim($row_program['day_image']) !== 'NULL') ? 'display:none;' : '' ?>">
                                        Image Size Should Be Less Than<br>
                                        100KB, Resolution : 900 X 900<br>
                                        and Format: Jpg/JPEG/Png
                                    </div>
                                </div>
                                <div id="day_image_preview_<?php echo $offset1; ?>" style="<?= (!empty($row_program['day_image']) && trim($row_program['day_image']) !== '' && trim($row_program['day_image']) !== 'NULL') ? 'display:block;' : 'display:none;' ?> margin-top: 5px;">
                                    <div class="image-zoom-container" style="height:100px; max-height: 100px; overflow:hidden; position: relative; width: 100px; border: 2px solid #ddd; border-radius: 8px; background-color: #f8f9fa;">
                                        <img id="preview_img_<?php echo $offset1; ?>" src="<?php
                                            if (!empty($row_program['day_image'])) {
                                                $image_path = trim($row_program['day_image']);
                                                error_log("QUOTATION: Image path from DB: " . $image_path);

                                                if ($image_path && $image_path !== '' && $image_path !== 'NULL') {
                                                    $final_url = findImageUrl($image_path, empty($quotation_id));
                                                    if (!empty($final_url)) {
                                                        error_log("QUOTATION: Using found image URL: " . $final_url);
                                                        echo $final_url;
                                                    } else {
                                                        error_log("QUOTATION: No image found for path: " . $image_path);
                                                        echo '';
                                                    }
                                                } else {
                                                    echo '';
                                                }
                                            } else {
                                                echo '';
                                            }
                                        ?>" alt="Preview" 
                                             style="width:100%; height:100%; object-fit: cover; border-radius: 6px;"
                                             onerror="console.log('QUOTATION: Existing image failed to load:', this.src); this.style.display='none'; if(this.parentElement && this.parentElement.parentElement) { this.parentElement.parentElement.style.display='none'; } if(this.parentElement && this.parentElement.parentElement && this.parentElement.parentElement.parentElement) { var label = this.parentElement.parentElement.parentElement.querySelector('label'); if(label) { label.style.display='block'; } } if(this.parentElement) { var removeBtn = this.parentElement.querySelector('button[onclick*=removeDayImage]'); if(removeBtn) { removeBtn.style.display='none'; } }"
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
                                    <label for="day_image_<?php echo $current_offset; ?>" class="btn btn-sm btn-success upload-btn-<?php echo $current_offset; ?>" 
                                           style="margin-bottom: 5px; padding: 6px 12px; font-size: 12px; cursor: pointer; border-radius: 4px; border: none; background-color: #28a745; color: white; font-weight: 500; <?= (!empty($row_program['day_image']) && trim($row_program['day_image']) !== '' && trim($row_program['day_image']) !== 'NULL') ? 'display:none;' : '' ?>">
                                        <i class="fa fa-image"></i> Upload Image
                                    </label>
                                    <input type="file" id="day_image_<?php echo $current_offset; ?>" 
                                           name="day_image_<?php echo $current_offset; ?>" accept="image/*" 
                                           onchange="previewDayImage(this, '<?php echo $current_offset; ?>')" 
                                           onclick="console.log('File input clicked for offset: <?php echo $current_offset; ?>')"
                                           style="display: none;">
                                </div>
                                <div id="day_image_preview_<?php echo $current_offset; ?>" style="<?= (!empty($row_program['day_image']) && trim($row_program['day_image']) !== '' && trim($row_program['day_image']) !== 'NULL') ? 'display:block;' : 'display:none;' ?> margin-top: 5px;">
                                    <div class="image-zoom-container" style="height:100px; max-height: 100px; overflow:hidden; position: relative; width: 100px; border: 2px solid #ddd; border-radius: 8px; background-color: #f8f9fa;">
                                        <img id="preview_img_<?php echo $current_offset; ?>" src="<?php
                                            if (!empty($row_program['day_image'])) {
                                                $image_path = trim($row_program['day_image']);
                                                error_log("QUOTATION: Image path from DB for offset " . $current_offset . ": " . $image_path);

                                                if ($image_path && $image_path !== '' && $image_path !== 'NULL') {
                                                    $final_url = findImageUrl($image_path, empty($quotation_id));
                                                    if (!empty($final_url)) {
                                                        error_log("QUOTATION: Using found image URL for offset " . $current_offset . ": " . $final_url);
                                                        echo $final_url;
                                                    } else {
                                                        error_log("QUOTATION: No image found for path (offset " . $current_offset . "): " . $image_path);
                                                        echo '';
                                                    }
                                                } else {
                                                    echo '';
                                                }
                                            } else {
                                                echo '';
                                            }
                                        ?>" alt="Preview" 
                                             style="width:100%; height:100%; object-fit: cover; border-radius: 6px;"
                                             onerror="console.log('QUOTATION: Existing image failed to load for offset <?php echo $current_offset; ?>:', this.src); this.style.display='none'; if(this.parentElement && this.parentElement.parentElement) { this.parentElement.parentElement.style.display='none'; } if(this.parentElement && this.parentElement.parentElement && this.parentElement.parentElement.parentElement) { var label = this.parentElement.parentElement.parentElement.querySelector('label'); if(label) { label.style.display='block'; } } if(this.parentElement) { var removeBtn = this.parentElement.querySelector('button[onclick*=removeDayImage]'); if(removeBtn) { removeBtn.style.display='none'; } } setTimeout(function(){ $('#day_image_preview_<?php echo $current_offset; ?>').hide(); $('label[for=\'day_image_<?php echo $current_offset; ?>\']').show(); }, 100);"
                                             onload="console.log('QUOTATION: Image loaded successfully for offset <?php echo $current_offset; ?>:', this.src);">
                                        <button type="button" 
                                                onclick="removeDayImage('<?php echo $current_offset; ?>'); return false;" 
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
                <label for="day_image_${offset}" class="btn btn-sm btn-success upload-btn-dynamic" style="margin-bottom: 5px; padding: 6px 12px; font-size: 12px; cursor: pointer; border-radius: 4px; border: none; background-color: #28a745; color: white; font-weight: 500;">
                <i class="fa fa-image"></i>    Upload Image
                </label>
                <input type="file" id="day_image_${offset}" name="day_image_${offset}" accept="image/*" onchange="previewDayImage(this, '${offset}')" style="display: none;">
                <div class="image-requirements-tooltip" style="font-size: 10px; color: white; margin-top: 3px; line-height: 1.2; background-color: #000; padding: 4px 6px; border-radius: 3px; display: inline-block; max-width: 120px;">
                    Image Size Should Be Less Than<br>
                    100KB, Resolution : 900 X 900<br>
                    and Format: Jpg/JPEG/Png
                </div>
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

// Day image preview functions for update (copied from update_modal.php)
// Make function globally available
window.previewDayImage = function(input, offset) {
    console.log("QUOTATION: Preview triggered for offset:", offset);
    console.log("QUOTATION: Input element:", input);
    console.log("QUOTATION: Input files:", input.files);
    console.log("QUOTATION: Input files length:", input.files ? input.files.length : 'no files property');
    console.log("QUOTATION: Input file[0]:", input.files ? input.files[0] : 'no files');
    
    var file = input.files[0];
    if (!file) {
        console.log("QUOTATION: No file selected - returning early");
        return;
    }
    
    console.log("QUOTATION: File selected:", file);
    console.log("QUOTATION: File name:", file.name);
    console.log("QUOTATION: File type:", file.type);
    console.log("QUOTATION: File size:", file.size);
    
    // Validate file type
    var allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
    if (!allowedTypes.includes(file.type)) {
        error_msg_alert('Only JPG, JPEG, PNG, WEBP files are allowed');
        input.value = ''; // Clear the input
        return;
    }
    
    // Validate file size (max 5MB)
    if (file.size > 5 * 1024 * 1024) {
        error_msg_alert('File size must be less than 5MB');
        input.value = '';
        return;
    }
    
    console.log("QUOTATION: File validation passed, showing preview for offset:", offset);
    
    var reader = new FileReader();
    reader.onload = function(e) {
        console.log("QUOTATION: FileReader loaded, setting image src for offset:", offset);
        console.log("QUOTATION: FileReader result:", e.target.result);
        var previewImg = $('#preview_img_' + offset);
        var previewDiv = $('#day_image_preview_' + offset);
        var uploadLabel = $('.upload-btn-' + offset);
        
        console.log("QUOTATION: Preview elements found - img:", previewImg.length, "div:", previewDiv.length, "label:", uploadLabel.length);
        
        if (previewImg.length === 0) {
            console.error("QUOTATION: Preview image element not found for offset:", offset);
            return;
        }
        
        if (previewDiv.length === 0) {
            console.error("QUOTATION: Preview div element not found for offset:", offset);
            console.error("QUOTATION: Looking for element with ID: day_image_preview_" + offset);
            console.error("QUOTATION: Available preview divs:", $('[id^="day_image_preview_"]').length);
            return;
        }
        
        console.log("QUOTATION: Preview div found for offset:", offset);
        console.log("QUOTATION: Preview div HTML:", previewDiv[0].outerHTML);
        
        // Set the image source
        previewImg.attr('src', e.target.result);
        
        // Force show preview div with multiple methods
        previewDiv.show();
        previewDiv.css('display', 'block !important');
        previewDiv.attr('style', 'display: block !important;');
        previewDiv.removeClass('hidden');
        
        // Force set dimensions to ensure visibility
        previewDiv.css('width', '100px');
        previewDiv.css('height', '100px');
        previewDiv.css('min-width', '100px');
        previewDiv.css('min-height', '100px');
        previewDiv.css('visibility', 'visible');
        previewDiv.css('opacity', '1');
        
        // CRITICAL: Make sure parent elements are also visible
        var parent = previewDiv.parent();
        while (parent.length > 0 && !parent.is('body')) {
            if (!parent.is(':visible')) {
                console.log("QUOTATION: Making parent visible:", parent[0].tagName, parent.attr('id'), parent.attr('class'));
                parent.show();
                parent.css('display', 'block !important');
                parent.css('visibility', 'visible');
                parent.css('opacity', '1');
            }
            parent = parent.parent();
        }
        
        // Show the remove button when image is selected
        previewDiv.find('button[onclick*="removeDayImage"]').css('display', 'flex');
        
        // Force hide the upload button after image selection
        uploadLabel.hide();
        uploadLabel.css('display', 'none !important');
        uploadLabel.attr('style', 'display: none !important;');
        
        // Hide the requirements tooltip when image is selected
        $('.image-requirements-tooltip').hide();
        
        console.log("QUOTATION: Preview displayed successfully for offset:", offset);
        console.log("QUOTATION: Preview div visible:", previewDiv.is(':visible'));
        console.log("QUOTATION: Preview div display style:", previewDiv.css('display'));
        console.log("QUOTATION: Preview div computed style:", previewDiv[0] ? window.getComputedStyle(previewDiv[0]).display : 'N/A');
        console.log("QUOTATION: Preview div width:", previewDiv.width());
        console.log("QUOTATION: Preview div height:", previewDiv.height());
        console.log("QUOTATION: Preview div offset:", previewDiv.offset());
        console.log("QUOTATION: Preview div position:", previewDiv.position());
        console.log("QUOTATION: Preview div parent visible:", previewDiv.parent().is(':visible'));
        console.log("QUOTATION: Upload button hidden:", !uploadLabel.is(':visible'));
        console.log("QUOTATION: Upload button display style:", uploadLabel.css('display'));
        
        // Fallback: Force show preview if it's not visible
        setTimeout(function() {
            if (!previewDiv.is(':visible')) {
                console.log("QUOTATION: Fallback - forcing preview to show for offset:", offset);
                previewDiv.show();
                previewDiv.css('display', 'block !important');
                previewDiv.attr('style', 'display: block !important;');
                previewDiv.css('width', '100px');
                previewDiv.css('height', '100px');
                previewDiv.css('min-width', '100px');
                previewDiv.css('min-height', '100px');
                previewDiv.css('visibility', 'visible');
                previewDiv.css('opacity', '1');
                
                // Make sure parent elements are also visible
                var parent = previewDiv.parent();
                while (parent.length > 0 && !parent.is('body')) {
                    if (!parent.is(':visible')) {
                        console.log("QUOTATION: Fallback - Making parent visible:", parent[0].tagName);
                        parent.show();
                        parent.css('display', 'block !important');
                        parent.css('visibility', 'visible');
                        parent.css('opacity', '1');
                    }
                    parent = parent.parent();
                }
                
                uploadLabel.hide();
                uploadLabel.css('display', 'none !important');
                uploadLabel.attr('style', 'display: none !important;');
            }
        }, 50);
        
        // Additional fallback with longer delay
        setTimeout(function() {
            if (!previewDiv.is(':visible')) {
                console.log("QUOTATION: Second fallback - forcing preview to show for offset:", offset);
                previewDiv.show();
                previewDiv.css('display', 'block !important');
                previewDiv.attr('style', 'display: block !important;');
                previewDiv.css('width', '100px');
                previewDiv.css('height', '100px');
                previewDiv.css('min-width', '100px');
                previewDiv.css('min-height', '100px');
                previewDiv.css('visibility', 'visible');
                previewDiv.css('opacity', '1');
                
                // Make sure parent elements are also visible
                var parent = previewDiv.parent();
                while (parent.length > 0 && !parent.is('body')) {
                    if (!parent.is(':visible')) {
                        console.log("QUOTATION: Second fallback - Making parent visible:", parent[0].tagName);
                        parent.show();
                        parent.css('display', 'block !important');
                        parent.css('visibility', 'visible');
                        parent.css('opacity', '1');
                    }
                    parent = parent.parent();
                }
                
                uploadLabel.hide();
                uploadLabel.css('display', 'none !important');
                uploadLabel.attr('style', 'display: none !important;');
            }
        }, 200);
    };
    
    reader.onerror = function(e) {
        console.error("QUOTATION: FileReader error for offset:", offset, e);
        error_msg_alert('Error reading the selected file');
    };
    
    reader.readAsDataURL(file);
    
    // Store file for later upload with the correct offset key
    if (!window.quotationImages) {
        window.quotationImages = {};
    }
    // Check if there's an existing image for this offset (replacement)
    var existingImage = $('#existing_image_path_' + offset).val();
    var isReplacement = existingImage && existingImage.trim() !== '';
    
    window.quotationImages[offset] = {
        file: file,
        uploaded: false,
        offset: offset,
        package_id: getPackageIdForOffset(offset),
        day_number: offset,
        is_replacement: isReplacement,
        existing_image_url: existingImage
    };
    
    console.log("QUOTATION: Image replacement status for offset", offset, ":", isReplacement, "existing:", existingImage);
    
    console.log("QUOTATION: Image stored in window.quotationImages[" + offset + "]");
    console.log("QUOTATION: Image details:", {
        name: file.name,
        size: file.size,
        type: file.type,
        offset: offset,
        package_id: window.quotationImages[offset].package_id
    });
    console.log("QUOTATION: Current quotationImages object:", window.quotationImages);
    
    // Debug: Check if images are being collected properly
    if (typeof collectStoredImages === 'function') {
        var collectedImages = collectStoredImages();
        console.log("QUOTATION: Collected images for upload:", collectedImages.length);
    }
    
    // Add a periodic check to refresh images that have been uploaded
    setInterval(function() {
        if (window.quotationImages) {
            for (var offset in window.quotationImages) {
                var imageData = window.quotationImages[offset];
                if (imageData.uploaded && imageData.image_url) {
                    // Check if the current image src matches the uploaded URL
                    var currentSrc = $('#preview_img_' + offset).attr('src');
                    var expectedSrc = imageData.image_url;
                    
                    if (currentSrc && !currentSrc.includes(expectedSrc)) {
                        console.log("QUOTATION: Image mismatch detected for offset", offset, "current:", currentSrc, "expected:", expectedSrc);
                        window.refreshImageAfterUpload(offset, imageData.image_url);
                    }
                }
            }
        }
    }, 2000); // Check every 2 seconds
    
    // Add a global function to refresh image after upload
    window.refreshImageAfterUpload = function(offset, newImageUrl) {
        console.log("QUOTATION: Refreshing image for offset", offset, "with URL:", newImageUrl);
        var previewImg = $('#preview_img_' + offset);
        var previewDiv = $('#day_image_preview_' + offset);
        
        if (previewImg.length && previewDiv.length) {
            // Add cache-busting parameter to ensure new image loads
            var cacheBuster = '?t=' + new Date().getTime();
            var imageUrl = newImageUrl + cacheBuster;
            
            // Update the image source
            previewImg.attr('src', imageUrl);
            
            // Update the existing image path (without cache buster)
            $('#existing_image_path_' + offset).val(newImageUrl);
            
            // Ensure preview is visible
            previewDiv.show();
            previewDiv.css('display', 'block');
            
            // Hide upload button
            $('.upload-btn-' + offset).hide();
            
            console.log("QUOTATION: Image refreshed successfully for offset", offset, "with URL:", imageUrl);
        } else {
            console.log("QUOTATION: Preview elements not found for offset", offset);
            console.log("QUOTATION: previewImg length:", previewImg.length, "previewDiv length:", previewDiv.length);
        }
    };
    
    // Add a function to force refresh all images after upload
    window.refreshAllImagesAfterUpload = function() {
        console.log("QUOTATION: Refreshing all images after upload");
        if (window.quotationImages) {
            for (var offset in window.quotationImages) {
                var imageData = window.quotationImages[offset];
                if (imageData.uploaded && imageData.image_url) {
                    console.log("QUOTATION: Refreshing uploaded image for offset", offset);
                    window.refreshImageAfterUpload(offset, imageData.image_url);
                }
            }
        }
    };
}

// Make function globally available
window.removeDayImage = function(offset) {
    console.log("QUOTATION: REMOVING image for offset:", offset);
    
    // Prevent multiple calls
    if (window.removingImage && window.removingImage[offset]) {
        console.log("QUOTATION: Already removing image for offset:", offset);
        return;
    }
    
    if (!window.removingImage) {
        window.removingImage = {};
    }
    window.removingImage[offset] = true;
    
    // Get elements
    var fileInput = $('#day_image_' + offset);
    var previewDiv = $('#day_image_preview_' + offset);
    var previewImg = $('#preview_img_' + offset);
    var uploadLabel = $('.upload-btn-' + offset);
    
    // Clear file input completely
    fileInput.val('');
    fileInput.prop('value', '');
    if (fileInput[0]) {
        fileInput[0].value = '';
    }
    
    // Clear the image src completely
    previewImg.attr('src', '');
    previewImg.removeAttr('src');
    if (previewImg[0]) {
        previewImg[0].src = '';
    }
    
    // Clear existing image path
    $('#existing_image_path_' + offset).val('');
    
    // Clear stored file
    if (window.quotationImages && window.quotationImages[offset]) {
        delete window.quotationImages[offset];
    }
    
    // Hide preview div and show upload button
    previewDiv.hide();
    previewDiv.css('display', 'none');
    uploadLabel.show();
    uploadLabel.css('display', 'block');
    
    // Show the requirements tooltip when upload button is shown
    $('.image-requirements-tooltip').show();
    
    // Reset the file input's change event handler
    fileInput.off('change').on('change', function() {
        console.log("QUOTATION: File input change event triggered for offset:", offset);
        if (this.files && this.files.length > 0) {
            if (typeof window.previewDayImage === 'function') {
                window.previewDayImage(this, offset);
            } else {
                console.error('QUOTATION: previewDayImage function not available in fallback');
            }
        }
    });
    
    // Clean up after a short delay
    setTimeout(function() {
        previewDiv.hide();
        previewDiv.css('display', 'none');
        uploadLabel.show();
        uploadLabel.css('display', 'block');
        $('.image-requirements-tooltip').show();
        delete window.removingImage[offset];
        console.log("QUOTATION: Image removal completed for offset:", offset);
    }, 100);
    
    console.log("QUOTATION: Image removed for offset:", offset);
}

// Function to get package ID for a specific offset
function getPackageIdForOffset(offset) {
    // Try to find package ID from the current row
    var packageIdInput = $('input[name="package_id_n"]').first();
    var packageId = packageIdInput.val() || '1'; // Default to 1 if not found
    
    console.log("DEBUG: Getting package ID for offset", offset, ":", packageId);
    return packageId;
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

// Function to process selected itinerary image after modal closes
function processSelectedItineraryImageQuotation() {
    console.log("QUOTATION: processSelectedItineraryImageQuotation called");
    console.log("QUOTATION: window.selectedItineraryImage =", window.selectedItineraryImage);
    
    if (window.selectedItineraryImage) {
        var dayId = window.selectedItineraryImage.dayId;
        var img = window.selectedItineraryImage.img;
        
        console.log("QUOTATION: Processing selected itinerary image for day:", dayId, "img:", img);
        
        // Set the image path in hidden input
        $('#existing_image_path_' + dayId).val(img);
        console.log("QUOTATION: Set hidden input value for day", dayId);
        
        // Show image preview if image exists
        if (img && img !== '' && img !== 'NULL') {
            var imageUrl = img;
            
            // Check if path already starts with http
            if (img.indexOf('http') !== 0) {
                // For package images, use project root URL
                var project_base_url = $('#base_url').val().replace('/crm/', '/');
                project_base_url = project_base_url.replace(/\/$/, '');
                var image_path = img.replace(/^\//, '');
                imageUrl = project_base_url + '/' + image_path;
            }
            
            console.log("QUOTATION: Final image URL:", imageUrl);
            
            // Update the image preview
            var previewImg = $('#preview_img_' + dayId);
            var previewDiv = $('#day_image_preview_' + dayId);
            
            if (previewImg.length && previewDiv.length) {
                previewImg.attr('src', imageUrl);
                previewDiv.show();
                
                // Show the remove button
                previewDiv.find('button[onclick*="removeDayImage"]').show();
                
                // Hide the upload button
                $('#day_image_' + dayId).parent().find('label').hide();
                
                console.log("QUOTATION: Image preview updated for day", dayId);
            } else {
                console.log("QUOTATION: Preview elements not found for day", dayId);
                console.log("QUOTATION: Looking for preview_img_" + dayId + " and day_image_preview_" + dayId);
            }
        } else {
            console.log("QUOTATION: No valid image to process for day", dayId);
        }
        
        // Clear the stored data
        window.selectedItineraryImage = null;
        console.log("QUOTATION: Image processing completed and data cleared");
    } else {
        console.log("QUOTATION: No selectedItineraryImage data found");
    }
}

// Listen for modal close event and process selected image
$(document).ready(function() {
    console.log("QUOTATION: Setting up modal event listeners");
    
    // Multiple event listeners to ensure we catch the modal close
    $(document).on('hidden.bs.modal', '#itinerary_detail_modal', function() {
        console.log("QUOTATION: Modal closed (hidden.bs.modal), processing selected image");
        console.log("QUOTATION: window.selectedItineraryImage =", window.selectedItineraryImage);
        setTimeout(function() {
            processSelectedItineraryImageQuotation();
        }, 100);
    });
    
    $(document).on('hide.bs.modal', '#itinerary_detail_modal', function() {
        console.log("QUOTATION: Modal closing (hide.bs.modal), processing selected image");
        console.log("QUOTATION: window.selectedItineraryImage =", window.selectedItineraryImage);
        setTimeout(function() {
            processSelectedItineraryImageQuotation();
        }, 200);
    });
    
    // Also check periodically if image data is available
    setInterval(function() {
        if (window.selectedItineraryImage) {
            console.log("QUOTATION: Periodic check found selectedItineraryImage, processing...");
            processSelectedItineraryImageQuotation();
        }
    }, 1000);
    
    // Add click event listener for image upload buttons (copied from update_modal.php)
    $(document).on('click', 'label[class*="upload-btn-"]', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var forAttr = $(this).attr('for');
        var offset = forAttr.replace('day_image_', '');
        console.log('QUOTATION: Upload button clicked for offset:', offset);
        console.log('QUOTATION: Upload button for attribute:', forAttr);
        
        // Check if file input exists
        var fileInput = $('#day_image_' + offset);
        console.log('QUOTATION: File input exists:', fileInput.length > 0);
        console.log('QUOTATION: File input element:', fileInput[0]);
        
        // Clear the file input first to ensure change event fires
        fileInput.val('');
        
        // Use a more robust method to trigger file input click
        console.log('QUOTATION: About to click file input');
        
        // Method 4: Test if label is properly connected
        console.log('QUOTATION: Testing label connection - file input ID:', fileInput.attr('id'));
        console.log('QUOTATION: Testing label connection - label for attribute:', forAttr);
        console.log('QUOTATION: Testing label connection - match:', fileInput.attr('id') === forAttr);
        
        // Keep the upload button visible and make it automatically trigger the file input
        console.log('QUOTATION: Making upload button automatically trigger file input');
        
        // Keep the upload button visible but make it trigger the file input
        var uploadButton = $('label[for="day_image_' + offset + '"]');
        
        // Make sure the file input is hidden initially
        fileInput.css('display', 'none');
        
        // When upload button is clicked, automatically trigger the file input
        uploadButton.off('click.auto').on('click.auto', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            console.log('QUOTATION: Upload button clicked, automatically triggering file input');
            
            // Try multiple methods to trigger the file input
            try {
                fileInput[0].click();
                console.log('QUOTATION: File input click triggered');
            } catch (e) {
                console.log('QUOTATION: File input click failed:', e);
            }
            
            // Also try jQuery trigger
            try {
                fileInput.trigger('click');
                console.log('QUOTATION: File input jQuery trigger attempted');
            } catch (e) {
                console.log('QUOTATION: File input jQuery trigger failed:', e);
            }
            
            // Also try mouse event
            try {
                var clickEvent = new MouseEvent('click', {
                    view: window,
                    bubbles: true,
                    cancelable: true
                });
                fileInput[0].dispatchEvent(clickEvent);
                console.log('QUOTATION: File input mouse event triggered');
            } catch (e) {
                console.log('QUOTATION: File input mouse event failed:', e);
            }
        });
        
        console.log('QUOTATION: Upload button configured to auto-trigger file input');
        
        // Check if click was successful
        setTimeout(function() {
            console.log('QUOTATION: File input after click - files:', fileInput[0].files);
            console.log('QUOTATION: File input after click - length:', fileInput[0].files.length);
        }, 200);
    });
    
    // Add change event listener for file inputs (copied from update_modal.php)
    $(document).on('change', 'input[id^="day_image_"]', function() {
        var offset = $(this).attr('id').replace('day_image_', '');
        console.log('QUOTATION: File input changed for offset:', offset);
        console.log('QUOTATION: File input files:', this.files);
        console.log('QUOTATION: File input element:', this);
        console.log('QUOTATION: File input value:', this.value);
        console.log('QUOTATION: File input files length:', this.files ? this.files.length : 'no files property');
        console.log('QUOTATION: File input files[0]:', this.files ? this.files[0] : 'no files');
        
        // Only process if there are actually files selected
        if (this.files && this.files.length > 0) {
            console.log('QUOTATION: Files selected, calling previewDayImage');
            console.log('QUOTATION: previewDayImage function available:', typeof window.previewDayImage);
            // Small delay to ensure DOM is ready
            setTimeout(function() {
                if (typeof window.previewDayImage === 'function') {
                    window.previewDayImage(this, offset);
                } else {
                    console.error('QUOTATION: previewDayImage function not available in event listener');
                }
            }.bind(this), 10);
        } else {
            console.log('QUOTATION: No files selected in file input - ignoring change event');
        }
    });
    
    // Also add direct onclick handlers to the file inputs
    $(document).on('click', 'input[id^="day_image_"]', function() {
        var offset = $(this).attr('id').replace('day_image_', '');
        console.log('QUOTATION: File input clicked for offset:', offset);
    });
    
    // Add click event listener for remove buttons
    $(document).on('click', 'button[onclick*="removeDayImage"]', function(e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        var onclickAttr = $(this).attr('onclick');
        var offset = onclickAttr.match(/removeDayImage\('(\d+)'\)/);
        if (offset && offset[1]) {
            console.log('QUOTATION: Remove button clicked for offset:', offset[1]);
            // Force immediate removal - NUCLEAR OPTION
            var offsetNum = offset[1];
            console.log('QUOTATION: NUCLEAR REMOVAL for offset:', offsetNum);
            
            // Remove the entire preview div
            $('#day_image_preview_' + offsetNum).remove();
            
            // Clear file input
            $('#day_image_' + offsetNum).val('');
            $('#existing_image_path_' + offsetNum).val('');
            
            // Create a fresh upload button container with preview div
            var container = $('#day_image_' + offsetNum).parent();
            
            // Check if previewDayImage function is available
            console.log("QUOTATION: Checking if previewDayImage is available:", typeof window.previewDayImage);
            console.log("QUOTATION: Checking if previewDayImage is available (direct):", typeof previewDayImage);
            
            container.html(`
                <div style="margin-top: 35px;">
                    <label for="day_image_${offsetNum}" class="btn btn-sm btn-success upload-btn-${offsetNum}" 
                           style="margin-bottom: 5px; padding: 6px 12px; font-size: 12px; cursor: pointer; border-radius: 4px; border: none; background-color: #28a745; color: white; font-weight: 500; display: block !important;">
                        <i class="fa fa-image"></i> Upload Image
                    </label>
                    <input type="file" id="day_image_${offsetNum}" 
                           name="day_image_${offsetNum}" accept="image/*" 
                           onchange="console.log('QUOTATION: File input changed for offset ${offsetNum}'); if(typeof window.previewDayImage === 'function') { window.previewDayImage(this, '${offsetNum}'); } else { console.error('QUOTATION: previewDayImage function not available'); }" 
                           style="display: none;">
                </div>
                <div id="day_image_preview_${offsetNum}" style="display: none; margin-top: 5px;">
                    <div style="height:100px; max-height: 100px; overflow:hidden; position: relative; width: 100px; border: 2px solid #ddd; border-radius: 8px; background-color: #f8f9fa;">
                        <img id="preview_img_${offsetNum}" src="" alt="Preview" style="width:100%; height:100%; object-fit: cover; border-radius: 6px;">
                        <button type="button" 
                                onclick="removeDayImage('${offsetNum}')" 
                                title="Remove Image" 
                                style="position: absolute; top: 5px; right: 5px; width: 20px; height: 20px; border: none; border-radius: 50%; background-color: #dc3545; color: white; font-size: 12px; cursor: pointer; display: none; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                            ×
                        </button>
                    </div>
                </div>
                <input type="hidden" id="existing_image_path_${offsetNum}" name="existing_image_path_${offsetNum}" value="" />
            `);
            
            console.log("QUOTATION: Fresh upload button created and shown for offset:", offsetNum);
            
            // Add direct event listener to the new file input
            var newFileInput = $('#day_image_' + offsetNum);
            console.log("QUOTATION: New file input found:", newFileInput.length > 0);
            
            if (newFileInput.length > 0) {
                newFileInput.off('change').on('change', function() {
                    console.log('QUOTATION: DIRECT File input changed for offset:', offsetNum);
                    console.log('QUOTATION: DIRECT File input files:', this.files);
                    console.log('QUOTATION: DIRECT File input files length:', this.files ? this.files.length : 'no files property');
                    console.log('QUOTATION: DIRECT File input files[0]:', this.files ? this.files[0] : 'no files');
                    console.log('QUOTATION: DIRECT File input value:', this.value);
                    
                    if (this.files && this.files.length > 0) {
                        console.log('QUOTATION: DIRECT Files selected, calling previewDayImage');
                        console.log('QUOTATION: DIRECT previewDayImage function available:', typeof window.previewDayImage);
                        if (typeof window.previewDayImage === 'function') {
                            console.log('QUOTATION: DIRECT Calling previewDayImage with file:', this.files[0]);
                            window.previewDayImage(this, offsetNum);
                        } else {
                            console.error('QUOTATION: DIRECT previewDayImage function not available');
                        }
                    } else {
                        console.log('QUOTATION: DIRECT No files selected or files.length is 0');
                    }
                });
                console.log("QUOTATION: Direct event listener attached to new file input");
            }
            
            // Clear stored data
            if (window.quotationImages && window.quotationImages[offsetNum]) {
                delete window.quotationImages[offsetNum];
            }
            
            console.log('QUOTATION: NUCLEAR REMOVAL completed for offset:', offsetNum);
        }
        return false;
    });
});


</script>
<script src="<?= BASE_URL ?>js/app/footer_scripts.js"></script>