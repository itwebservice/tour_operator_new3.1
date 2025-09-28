<style>
    .style_text {
        position: absolute;
        right: 15px;
        display: flex;
        gap: 15px;
        background: #f5f5f5;
        padding: 0px 14px;
        top: 0px;
    }
    
    /* Upload button styling with left padding */
    label[class*="upload-btn-"] {
        margin-left: 15px !important;
        padding-left: 15px !important;
    }

    /* For dynamically added rows */
    .upload-btn-dynamic {
        margin-left: 15px !important;
        padding-left: 15px !important;
    }

    /* General upload button styling */
    .btn-success[class*="upload-btn"] {
        margin-left: 15px !important;
        padding-left: 15px !important;
    }

    /* Update section specific upload button styling */
    .btn-success[style*="background-color: #28a745"] {
        margin-left: 15px !important;
        padding-left: 15px !important;
    }
</style>

<?php

// Helper function to check image existence in multiple locations
function findImageUrl($image_path, $is_new_quotation = false)
{
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
        // Check itinerary_images folder first for new quotations
        $itinerary_images_path = "../../../../../uploads/itinerary_images/" . basename($image_path_clean);
        if (file_exists($itinerary_images_path)) {
            error_log("TAB2: Found image in itinerary_images folder (new quotation priority): " . $itinerary_images_path);
            $itinerary_images_url = $project_base_url . '/uploads/itinerary_images/' . basename($image_path_clean);
            return $itinerary_images_url;
        }
    }

    // Check original path first
    $original_url = $project_base_url . '/' . $image_path_clean;
    $original_file_path = "../../../" . $image_path_clean;

    if (file_exists($original_file_path)) {
        error_log("TAB2: Found image in original location: " . $original_file_path);
        return $original_url;
    }

    // Check quotation_images folder (full path as stored in database)
    if (strpos($image_path_clean, 'uploads/quotation_images/') === 0) {
        // For database paths like "uploads/quotation_images/filename.jpg", 
        // we need to check in crm/uploads/quotation_images/ folder
        $quotation_images_url = $project_base_url . '/crm/' . $image_path_clean;
        $quotation_images_file_path = "../../../../" . $image_path_clean;

        if (file_exists($quotation_images_file_path)) {
            error_log("TAB2: Found image in quotation_images folder (database path): " . $quotation_images_file_path);
            return $quotation_images_url;
        }
    }

    // Check quotation_images folder (with just filename, not full path)
    $quotation_images_path = "crm/uploads/quotation_images/" . basename($image_path_clean);
    $quotation_images_url = $project_base_url . '/' . $quotation_images_path;
    $quotation_images_file_path = "../../../../" . $quotation_images_path;

    if (file_exists($quotation_images_file_path)) {
        error_log("TAB2: Found image in quotation_images folder: " . $quotation_images_file_path);
        return $quotation_images_url;
    }

    // Check CRM uploads folder directly
    $crm_uploads_path = "../../../../crm/uploads/" . basename($image_path_clean);
    if (file_exists($crm_uploads_path)) {
        error_log("TAB2: Found image in CRM uploads folder: " . $crm_uploads_path);
        $crm_uploads_url = $project_base_url . '/crm/uploads/' . basename($image_path_clean);
        return $crm_uploads_url;
    }

    // Check CRM uploads quotation_images folder directly
    $crm_quotation_images_path = "../../../../crm/uploads/quotation_images/" . basename($image_path_clean);
    if (file_exists($crm_quotation_images_path)) {
        error_log("TAB2: Found image in CRM uploads quotation_images folder: " . $crm_quotation_images_path);
        $crm_quotation_images_url = $project_base_url . '/crm/uploads/quotation_images/' . basename($image_path_clean);
        return $crm_quotation_images_url;
    }

    // Check itinerary_images folder as fallback (for both new and existing quotations)
    $itinerary_images_path = "../../../../../uploads/itinerary_images/" . basename($image_path_clean);
    if (file_exists($itinerary_images_path)) {
        error_log("TAB2: Found image in itinerary_images folder (fallback): " . $itinerary_images_path);
        $itinerary_images_url = $project_base_url . '/uploads/itinerary_images/' . basename($image_path_clean);
        return $itinerary_images_url;
    }

    error_log("TAB2: Image not found in any location: " . $image_path);
    return '';
}

?>


<form id="frm_tab2_u">
    <input type="hidden" id="base_url" value="<?= BASE_URL ?>" />
    <input type="hidden" id="quotation_id" value="<?= $quotation_id ?>" />
    <div class="app_panel">

        <div class="container" style="width:100% !important;">

            <div class="row">
                <div class="col-md-3 col-sm-4 col-xs-12 mg_bt_20" id="package_div">
                    <select name="dest_name" id="dest_name" title="Select Destination"
                        onchange="load_packages_with_filter()" style="width:100%">
                        <option value="">*Select Destination</option>
                        <?php
                        $sq_query = mysqlQuery("select * from destination_master where status != 'Inactive'");
                        while ($row_dest = mysqli_fetch_assoc($sq_query)) {
                            $selected = ($sq_pacakge['dest_id'] == $row_dest['dest_id']) ? 'selected' : '';
                        ?>
                            <option value="<?php echo $row_dest['dest_id']; ?>" <?= $selected ?>><?php echo $row_dest['dest_name']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-3 col-sm-4 col-xs-12 mg_bt_20">
                    <select name="nights_filter" id="nights_filter" title="Filter by Nights"
                        onchange="filter_packages_by_nights()" style="width:100%">
                        <option value="">All Nights</option>
                        <?php
                        // Generate options for 1 to 30 nights
                        for ($i = 1; $i <= 30; $i++) {
                            $selected = ($sq_quotation['total_days'] == $i) ? 'selected' : '';
                            echo "<option value='$i' $selected>$i Night" . ($i > 1 ? 's' : '') . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-6 col-sm-4 col-xs-12 mg_bt_20 text-right">
                    <button type="button" data-toggle="tooltip" class="btn btn-excel" title="Note : The Package is not available for this Destination.Please create here."><i class="fa fa-question-circle"></i></button>

                    <a href="../../../../custom_packages/master/index.php" target='_blank' class="btn btn-sm" style="
    background: none;
    background: none;
    color: #fff;
    padding: 6px 14px;
    font-size: 16px;
    color: #007bff;text-decoration:none; display:inline-block;"><i class="fa fa-plus"></i>&nbsp;&nbsp;Package Tour</a>
                </div>
                <div class="col-md-12 col-sm-8 col-xs-12 no-pad" id="package_name_div">
                    <!-- Packages will be loaded here via AJAX -->
                    <div class="text-center" style="padding: 20px;">
                        <i class="fa fa-spinner fa-spin"></i> Loading packages...
                    </div>
                </div>

                <!-- Hidden fields for package data -->
                <?php
                $sq_pacakge = mysqli_fetch_assoc(mysqlQuery("select * from custom_package_master where package_id='$sq_quotation[package_id]'"));
                $sq_destination = mysqli_fetch_assoc(mysqlQuery("select * from destination_master where dest_id='{$sq_pacakge['dest_id']}'"));
                ?>
                <input type="hidden" value="<?= $sq_pacakge['dest_id'] ?>" id='dest_name_hidden'>
                <input type="hidden" value="<?= $sq_quotation['package_id'] ?>" id='img_package_id'>
                <input type='hidden' id='pckg_daywise_url' name='pckg_daywise_url' />
                <input type='hidden' id='quotation_id' name='quotation_id' value='<?= $quotation_id ?>' />
                <input type='hidden' id='base_url' name='base_url' value='<?= BASE_URL ?>' />
            </div>
            <div class="row">
                <div class="col-md-12 col-sm-8 col-xs-12 no-pad" id="package_name_div">
                    <!-- Packages will be loaded here via AJAX -->
                </div>
            </div>
            <div class="row" style="display: none;">
                <div class="col-md-12 col-sm-8 col-xs-12 no-pad">
                    <div class="col-md-12 app_accordion">
                        <div class="panel-group main_block" id="accordion" role="tablist" aria-multiselectable="true">
                            <div class="panel-body">
                                <div class="col-md-12 no-pad" id="div_list1">
                                    <div class="row mg_bt_10">
                                        <div class="col-xs-12 text-right text_center_xs">
                                            <button type="button" class="btn btn-excel btn-sm" onClick="addItineraryRow('<?php echo $package_id; ?>')"><i class="fa fa-plus"></i></button>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table style="width:100%" id="dynamic_table_list_update" name="dynamic_table_list_update" class="table table-bordered table-hover table-striped no-marg pd_bt_51 mg_bt_0">
                                            <h3 class="editor_title" style="width:100%;">Tour Itinerary</h3>
                                            <?php
                                            $offset = 0;
                                            // Debug: Check if quotation_id is available
                                            if (isset($quotation_id) && !empty($quotation_id)) {
                                                echo "<!-- Debug: Searching for quotation_id = '$quotation_id' -->";

                                                // Get itinerary records for this quotation with day_image
                                                $sq_program = mysqlQuery("select * from package_quotation_program where quotation_id = '$quotation_id' ORDER BY id");
                                                $program_count = mysqli_num_rows($sq_program);
                                                echo "<!-- Debug: Found $program_count records for quotation_id '$quotation_id' -->";

                                                if ($program_count > 0) {
                                                    while ($row_program = mysqli_fetch_assoc($sq_program)) {
                                                        $offset++;
                                            ?>
                                                        <tr>
                                                            <td style="width: 50px;"><input class="css-checkbox mg_bt_10" id="chk_program<?= $offset ?>" type="checkbox" checked><label class="css-label" style="margin-top: 55px;" for="chk_program<?= $offset ?>"> </label></td>
                                                            <td style="width: 50px;" class="hidden"><input maxlength="15" value="<?= $offset ?>" type="text" name="username" placeholder="Sr. No." class="form-control mg_bt_10" disabled />
                                                            </td>
                                                            <td style="width: 100px;"><input type="text" id="special_attaraction<?php echo $offset; ?>-u" onchange="validate_spaces(this.id);validate_spattration(this.id);" name="special_attaraction" class="form-control mg_bt_10" placeholder="Special Attraction" title="Special Attraction" style='width:220px;margin-top: 35px;' value="<?php echo $row_program['attraction']; ?>"></td>
                                                            <!-- <td style="width: 100px;max-width: 594px;overflow: hidden;"><textarea id="day_program<?php echo $offset; ?>-u" name="day_program" class="form-control mg_bt_10" title="Day-wise Program" rows="3" placeholder="*Day-wise Program" onchange="validate_spaces(this.id);validate_dayprogram(this.id);" style='width:400px;' value="<?php echo $row_program['day_wise_program']; ?>"><?php echo $row_program['day_wise_program']; ?></textarea>
                                                    </td> -->
                                                            <td class='col-md-5 no-pad' style="max-width:800px;overflow: hidden;position: relative;"><textarea id="day_program<?php echo $offset; ?>-u" name="day_program" class="form-control mg_bt_10 day_program" style=" height:900px;" placeholder="*Day Program" title="Day-wise Program" onchange="validate_spaces(this.id);validate_dayprogram(this.id);" rows="3" value="<?php echo $row_program['day_wise_program']; ?>"><?php echo $row_program['day_wise_program']; ?></textarea><span class="style_text"><span class="style_text_b" data-wrapper="**" style="font-weight: bold; cursor: pointer;" title="Bold text">B</span><span class="style_text_u" data-wrapper="__" style="cursor: pointer;" title="Underline text"><u>U</u></span></span>
                                                            </td>
                                                            <td class='col-md-1/2 no-pad' style='width:150px;'><input type="text" id="overnight_stay<?php echo $offset; ?>-u" name="overnight_stay" onchange="validate_spaces(this.id);validate_onstay(this.id);" class="form-control mg_bt_10" placeholder="*Overnight Stay" title="Overnight Stay" value="<?php echo $row_program['stay']; ?>" style='width:200px;margin-top: 35px;'></td>
                                                            <td class='col-md-1/2 no-pad' style='width:150px;'><select id="meal_plan<?php echo $offset; ?>-u" title="Meal Plan" name="meal_plan" class="form-control mg_bt_10" style='width: 140px;margin-top: 35px;'>
                                                                    <?php if ($row_program['meal_plan'] != '') { ?>
                                                                        <option value="<?php echo $row_program['meal_plan']; ?>">
                                                                            <?php echo $row_program['meal_plan']; ?></option>
                                                                    <?php } ?>
                                                                    <?php get_mealplan_dropdown(); ?>
                                                                </select></td>
                                                            <td class='col-md-1 pad_8'>
                                                                <button type="button" class="btn btn-info btn-iti btn-sm" style="border:none;margin-top: 35px;" title="Add Itinerary" id="itinerary<?php echo $offset; ?>" onclick="add_itinerary('dest_name','special_attaraction<?php echo $offset; ?>-u','day_program<?php echo $offset; ?>-u','overnight_stay<?php echo $offset; ?>-u','Day-<?= $offset ?>')"><i class="fa fa-plus"></i></button>
                                                                <button type="button" class="btn btn-danger btn-sm" style="border:none;margin-top: 35px; margin-left: 5px;" title="Delete Row" onclick="deleteItineraryRow(<?php echo $offset; ?>)"><i class="fa fa-trash"></i></button>
                                                            </td>
                                                            <td class='col-md-1 pad_8' style="width: 120px;">
                                                                <!-- Debug: Image path = <?= $row_program['day_image'] ?? 'NULL' ?> -->
                                                                <div style="margin-top: 35px;">
                                                                    <label for="day_image_<?php echo $offset; ?>" class="btn btn-sm btn-success upload-btn-<?php echo $offset; ?>"
                                                                        style="margin-bottom: 5px; padding: 6px 12px; font-size: 12px; cursor: pointer; border-radius: 4px; border: none; background-color: #28a745; color: white; font-weight: 500; <?= (!empty($row_program['day_image']) && trim($row_program['day_image']) !== '' && trim($row_program['day_image']) !== 'NULL') ? 'display:none;' : '' ?>;">
                                                                        <i class="fa fa-image"></i> Upload Image
                                                                    </label>
                                                                    <input type="file" id="day_image_<?php echo $offset; ?>"
                                                                        name="day_image_<?php echo $offset; ?>" accept="image/*"
                                                                        onchange="previewDayImage(this, '<?php echo $offset; ?>')"
                                                                        style="display: none;">
                                                                </div>
                                                                <div id="day_image_preview_<?php echo $offset; ?>" style="<?= (!empty($row_program['day_image']) && trim($row_program['day_image']) !== '' && trim($row_program['day_image']) !== 'NULL') ? 'display:block;' : 'display:none;' ?>; margin-top: 5px;">
                                                                    <div class="image-zoom-container" style="height:100px; max-height: 100px; overflow:hidden; position: relative; width: 100px; border: 2px solid #ddd; border-radius: 8px; background-color: #f8f9fa;">
                                                                        <img id="preview_img_<?php echo $offset; ?>" src="<?php
                                                                                                                            if (!empty($row_program['day_image'])) {
                                                                                                                                $image_path = trim($row_program['day_image']);
                                                                                                                                // Debug the actual path
                                                                                                                                error_log("QUOTATION UPDATE: Image path from DB for offset " . $offset . ": " . $image_path);

                                                                                                                                // Check if path is valid and not empty
                                                                                                                                if ($image_path && $image_path !== '' && $image_path !== 'NULL') {
                                                                                                                                    $final_url = findImageUrl($image_path, false); // tab2 is for updating existing quotations
                                                                                                                                    if (!empty($final_url)) {
                                                                                                                                        error_log("QUOTATION UPDATE: Using found image URL for offset " . $offset . ": " . $final_url);
                                                                                                                                        echo $final_url;
                                                                                                                                    } else {
                                                                                                                                        error_log("QUOTATION UPDATE: No image found for path (offset " . $offset . "): " . $image_path);
                                                                                                                                        echo '';
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
                                                                            onerror="console.log('QUOTATION UPDATE: Existing image failed to load for offset <?php echo $offset; ?>:', this.src); this.style.display='none'; this.parentElement.parentElement.style.display='none'; this.parentElement.parentElement.parentElement.querySelector('label').style.display='block'; this.parentElement.querySelector('button[onclick*=removeDayImage]').style.display='none';"
                                                                            onload="console.log('QUOTATION UPDATE: Image loaded successfully for offset <?php echo $offset; ?>:', this.src);">
                                                                        <button type="button"
                                                                            onclick="removeDayImage('<?php echo $offset; ?>')"
                                                                            title="Remove Image"
                                                                            style="position: absolute; top: 5px; right: 5px; width: 20px; height: 20px; border: none; border-radius: 50%; background-color: #dc3545; color: white; font-size: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2); <?= (empty($row_program['day_image']) || trim($row_program['day_image']) === '' || trim($row_program['day_image']) === 'NULL') ? 'display:none;' : '' ?>;">
                                                                            ×
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                                <input type="hidden" id="existing_image_path_<?php echo $offset; ?>" name="existing_image_path_<?php echo $offset; ?>" value="<?= $row_program['day_image'] ?? '' ?>" />
                                                            </td>
                                                            <td class="hidden"><input type="hidden" name="package_id_n" value="<?php echo $row_program['id']; ?>"></td>
                                                        </tr>
                                            <?php
                                                    }
                                                } else {
                                                    // Show empty form for adding itinerary when no data exists
                                                    echo '<tr>';
                                                    echo '<td style="width: 50px;"><input class="css-checkbox mg_bt_10" id="chk_program1" type="checkbox" checked><label class="css-label" style="margin-top: 55px;" for="chk_program1"> </label></td>';
                                                    echo '<td style="width: 50px;" class="hidden"><input maxlength="15" value="1" type="text" name="username" placeholder="Sr. No." class="form-control mg_bt_10" disabled /></td>';
                                                    echo '<td style="width: 100px;"><input type="text" id="special_attaraction1-u" onchange="validate_spaces(this.id);validate_spattration(this.id);" name="special_attaraction" class="form-control mg_bt_10" placeholder="Special Attraction" title="Special Attraction" style="width:220px;margin-top: 35px;"></td>';
                                                    echo '<td class="col-md-5 no-pad" style="max-width:800px;overflow: hidden;position: relative;"><textarea id="day_program1-u" name="day_program" class="form-control mg_bt_10 day_program" style="height:900px;" placeholder="*Day Program" title="Day-wise Program" onchange="validate_spaces(this.id);validate_dayprogram(this.id);" rows="3"></textarea><span class="style_text"><span class="style_text_b" data-wrapper="**" style="font-weight: bold; cursor: pointer;" title="Bold text">B</span><span class="style_text_u" data-wrapper="__" style="cursor: pointer;" title="Underline text"><u>U</u></span></span></td>';
                                                    echo '<td class="col-md-1/2 no-pad" style="width:150px;"><input type="text" id="overnight_stay1-u" name="overnight_stay" onchange="validate_spaces(this.id);validate_onstay(this.id);" class="form-control mg_bt_10" placeholder="*Overnight Stay" title="Overnight Stay" style="width:200px;margin-top: 35px;"></td>';
                                                    echo '<td class="col-md-1/2 no-pad" style="width:150px;"><select id="meal_plan1-u" title="Meal Plan" name="meal_plan" class="form-control mg_bt_10" style="width: 140px;margin-top: 35px;">';
                                                    echo '<option value="">Select Meal Plan</option>';
                                                    echo get_mealplan_dropdown();
                                                    echo '</select></td>';
                                                    echo '<td class="col-md-1 pad_8">';
                                                    echo '<button type="button" class="btn btn-info btn-iti btn-sm" style="border:none;margin-top: 35px;" title="Add Itinerary" id="itinerary1" onclick="add_itinerary(\'dest_name\',\'special_attaraction1-u\',\'day_program1-u\',\'overnight_stay1-u\',\'Day-1\')"><i class="fa fa-plus"></i></button>';
                                                    echo '<button type="button" class="btn btn-danger btn-sm" style="border:none;margin-top: 35px; margin-left: 5px;" title="Delete Row" onclick="deleteItineraryRow(1)"><i class="fa fa-trash"></i></button>';
                                                    echo '</td>';
                                                    echo '<td class="col-md-1 pad_8" style="width: 120px;">';
                                                    echo '<div style="margin-top: 35px;">';
                                                    echo '<label for="day_image_1" class="btn btn-sm btn-success upload-btn-1" style="margin-bottom: 5px; padding: 6px 12px; font-size: 12px; cursor: pointer; border-radius: 4px; border: none; background-color: #28a745; color: white; font-weight: 500;"><i class="fa fa-image"></i>Upload Image</label>';
                                                    echo '<input type="file" id="day_image_1" name="day_image_1" accept="image/*" onchange="previewDayImage(this, \'1\')" style="display: none;">';
                                                    echo '</div>';
                                                    echo '<div id="day_image_preview_1" style="display: none; margin-top: 5px;">';
                                                    echo '<div class="image-zoom-container" style="height:100px; max-height: 100px; overflow:hidden; position: relative; width: 100px; border: 2px solid #ddd; border-radius: 8px; background-color: #f8f9fa;">';
                                                    echo '<img id="preview_img_1" src="" alt="Preview" style="width:100%; height:100%; object-fit: cover; border-radius: 6px;">';
                                                    echo '<button type="button" onclick="removeDayImage(\'1\')" title="Remove Image" style="position: absolute; top: 5px; right: 5px; width: 20px; height: 20px; border: none; border-radius: 50%; background-color: #dc3545; color: white; font-size: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">×</button>';
                                                    echo '</div>';
                                                    echo '<div style="margin-top: 35px;">';
                                                    echo '<label for="day_image_1" class="btn btn-sm btn-success upload-btn-1" style="margin-bottom: 5px; padding: 6px 12px; font-size: 12px; cursor: pointer; border-radius: 4px; border: none; background-color: #28a745; color: white; font-weight: 500;"><i class="fa fa-image"></i> Upload Image</label>';
                                                    echo '<input type="file" id="day_image_1" name="day_image_1" accept="image/*" onchange="previewDayImage(this, \'1\')" style="display: none;">';
                                                    echo '</div>';
                                                    echo '<div id="day_image_preview_1" style="display: none; margin-top: 5px;">';
                                                    echo '<div class="image-zoom-container" style="height:100px; max-height: 100px; overflow:hidden; position: relative; width: 100px; border: 2px solid #ddd; border-radius: 8px; background-color: #f8f9fa;">';
                                                    echo '<img id="preview_img_1" src="" alt="Preview" style="width:100%; height:100%; object-fit: cover; border-radius: 6px;">';
                                                    echo '<button type="button" onclick="removeDayImage(\'1\')" title="Remove Image" style="position: absolute; top: 5px; right: 5px; width: 20px; height: 20px; border: none; border-radius: 50%; background-color: #dc3545; color: white; font-size: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">×</button>';
                                                    echo '</div>';
                                                    echo '</div>';
                                                    echo '<input type="hidden" id="existing_image_path_1" name="existing_image_path_1" value="" />';
                                                    echo '</td>';
                                                    echo '<td class="hidden"><input type="hidden" name="package_id_n" value=""></td>';
                                                    echo '</tr>';
                                                }
                                            } else {
                                                echo '<tr><td colspan="8" class="text-center">Quotation ID not found.</td></tr>';
                                            }
                                            ?>
                                        </table>
                                    </div>
                                    <div class="row mg_tp_20">
                                        <div class="col-md-6">
                                            <h3 class="editor_title">Inclusions</h3>
                                            <div style="background: #f0f0f0; padding: 5px; margin-bottom: 5px; font-size: 12px;">
                                                Debug: Data length: <?php echo strlen($sq_quotation['inclusions']); ?> |
                                                Preview: <?php echo substr(strip_tags($sq_quotation['inclusions']), 0, 50); ?>...
                                            </div>
                                            <textarea class="feature_editor form-control" id="inclusions1" name="inclusions1" placeholder="Inclusions" title="Inclusions" rows="4"><?php echo htmlspecialchars_decode($sq_quotation['inclusions']); ?></textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <h3 class="editor_title">Exclusions</h3>
                                            <div style="background: #f0f0f0; padding: 5px; margin-bottom: 5px; font-size: 12px;">
                                                Debug: Data length: <?php echo strlen($sq_quotation['exclusions']); ?> |
                                                Preview: <?php echo substr(strip_tags($sq_quotation['exclusions']), 0, 50); ?>...
                                            </div>
                                            <textarea class="feature_editor form-control" id="exclusions1" name="exclusions1" placeholder="Exclusions" title="Exclusions" rows="4"><?php echo htmlspecialchars_decode($sq_quotation['exclusions']); ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row text-center mg_tp_20">
                <div class="col-xs-12">
                    <button class="btn btn-info btn-sm ico_left" type="button" onclick="switch_to_tab1()"><i class="fa fa-arrow-left"></i>&nbsp;&nbsp Previous</button>
                    &nbsp;&nbsp;
                    <button class="btn btn-info btn-sm ico_right" type="button" onclick="submitTab2Form()">Next&nbsp;&nbsp;<i class="fa fa-arrow-right"></i></button>
                </div>
            </div>
</form>
<script>
    // Function to add new itinerary row (copied from create page)
    function addItineraryRow(package_id) {
        var table = document.getElementById('dynamic_table_list_update');
        if (!table) {
            console.error('Table not found: dynamic_table_list_update');
            return;
        }

        var rowCount = table.rows.length;
        var newRow = table.insertRow(rowCount);

        // Get the next offset number
        var offset = rowCount + 1;

        console.log("DEBUG: addItineraryRow called with package_id:", package_id);
        console.log("DEBUG: Current row count:", rowCount);
        console.log("DEBUG: New offset will be:", offset);

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
        <td class='col-md-5 no-pad' style="max-width:800px;overflow: hidden;position: relative;">
            <textarea id="day_program${offset}-u" name="day_program" class="form-control mg_bt_10 day_program" style="height:900px;" placeholder="*Day Program" title="Day-wise Program" onchange="validate_spaces(this.id);validate_dayprogram(this.id);" rows="3"></textarea>
            <span class="style_text">
                <span class="style_text_b" data-wrapper="**" style="font-weight: bold; cursor: pointer;" title="Bold text">B</span>
                <span class="style_text_u" data-wrapper="__" style="cursor: pointer;" title="Underline text"><u>U</u></span>
            </span>
        </td>
        <td class='col-md-1/2 no-pad' style='width:150px;'>
            <input type="text" id="overnight_stay${offset}-u" name="overnight_stay" onchange="validate_spaces(this.id);validate_onstay(this.id);" class="form-control mg_bt_10" placeholder="*Overnight Stay" title="Overnight Stay" style='width:200px;margin-top: 35px;'>
        </td>
        <td class='col-md-1/2 no-pad' style='width:150px;'>
            <select id="meal_plan${offset}-u" title="Meal Plan" name="meal_plan" class="form-control mg_bt_10" style='width: 140px;margin-top: 35px;'>
                <option value="">Select Meal Plan</option>
                <option value="Breakfast">Breakfast</option>
                <option value="Lunch">Lunch</option>
                <option value="Dinner">Dinner</option>
                <option value="All Meals">All Meals</option>
            </select>
        </td>
        <td class='col-md-1 pad_8'>
            <button type="button" class="btn btn-info btn-iti btn-sm" style="border:none;margin-top: 35px;" title="Add Itinerary" id="itinerary${offset}" onclick="add_itinerary('dest_name','special_attaraction${offset}-u','day_program${offset}-u','overnight_stay${offset}-u','Day-${offset}')">
                <i class="fa fa-plus"></i>
            </button>
            <button type="button" class="btn btn-danger btn-sm" style="border:none;margin-top: 35px; margin-left: 5px;" title="Delete Row" onclick="deleteItineraryRow(${offset})">
                <i class="fa fa-trash"></i>
            </button>
        </td>
        <td class='col-md-1 pad_8' style="width: 120px;">
            <div style="margin-top: 35px;">
                <label for="day_image_${offset}" class="btn btn-sm btn-success upload-btn-dynamic" style="margin-bottom: 5px; padding: 6px 12px; font-size: 12px; cursor: pointer; border-radius: 4px; border: none; background-color: #28a745; color: white; font-weight: 500;">
                   <i class="fa fa-image"></i> Upload Image
                </label>
                <input type="file" id="day_image_${offset}" name="day_image_${offset}" accept="image/*" onchange="previewDayImage(this, '${offset}')" style="display: none;">
            </div>
            <div id="day_image_preview_${offset}" style="display: none; margin-top: 5px;">
                <div class="image-zoom-container" style="height:100px; max-height: 100px; overflow:hidden; position: relative; width: 100px; border: 2px solid #ddd; border-radius: 8px; background-color: #f8f9fa;">
                    <img id="preview_img_${offset}" src="" alt="Preview" style="width:100%; height:100%; object-fit: cover; border-radius: 6px;">
                    <button type="button" 
                            onclick="removeDayImage('${offset}')" 
                            title="Remove Image" 
                            style="position: absolute; top: 5px; right: 5px; width: 20px; height: 20px; border: none; border-radius: 50%; background-color: #dc3545; color: white; font-size: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                        ×
                    </button>
                </div>
            </div>
        </td>
        <td class="hidden">
            <input type="hidden" name="package_id_n" value="${package_id}">
            <input type="hidden" id="existing_image_path_${offset}" name="existing_image_path_${offset}" value="" />
        </td>
    `;

        console.log("DEBUG: New itinerary row added with offset:", offset, "package_id:", package_id);
    }

    // Function to delete itinerary row
    function deleteItineraryRow(offset) {
        if (confirm('Are you sure you want to delete this itinerary row?')) {
            var table = document.getElementById('dynamic_table_list_update');
            if (!table) {
                console.error('Table not found: dynamic_table_list_update');
                return;
            }

            // Find the row with the matching offset
            var rows = table.rows;
            for (var i = 0; i < rows.length; i++) {
                var row = rows[i];
                var deleteButton = row.querySelector('button[onclick*="deleteItineraryRow(' + offset + ')"]');
                if (deleteButton) {
                    table.deleteRow(i);
                    console.log("DEBUG: Deleted itinerary row with offset:", offset);
                    break;
                }
            }
        }
    }

    // Function to get package ID for a specific offset
    function getPackageIdForOffset(offset) {
        // Use the package_id from PHP variables (already validated)
        var packageId = '<?php echo $package_id; ?>';

        // If package_id is still empty, try to get it from the hidden input
        if (!packageId || packageId === '') {
            var packageIdInput = $('input[name="package_id_n"]').first();
            packageId = packageIdInput.val();
        }

        // If still empty, use the img_package_id hidden input
        if (!packageId || packageId === '') {
            packageId = $('#img_package_id').val();
        }

        // Final fallback - this should not happen if package_id is properly set
        if (!packageId || packageId === '') {
            console.error("ERROR: No package_id found for offset", offset);
            packageId = '1'; // This should not be reached
        }

        console.log("DEBUG: Getting package ID for offset", offset, ":", packageId);
        return packageId;
    }

    // Day image preview functions for update
    function previewDayImage(input, offset) {
        console.log("previewDayImage called with offset:", offset, "file:", input.files[0]);

        if (input.files && input.files[0]) {
            var file = input.files[0];
            console.log("Selected file details:", file.name, file.size, file.type);

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
                console.log("FileReader loaded, showing preview for edit mode");

                // Set the image source
                $('#preview_img_' + offset).attr('src', e.target.result);

                // Show the preview div
                $('#day_image_preview_' + offset).show();

                // Hide the upload button when image is uploaded
                $('label[for="day_image_' + offset + '"]').hide();

                // Show the remove button
                $('#day_image_preview_' + offset).find('button[onclick*="removeDayImage"]').show();

                // Store image data for later upload (when quotation is updated)
                if (!window.quotationImages) {
                    window.quotationImages = {};
                }

                // Get package ID for this row
                var packageId = getPackageIdForOffset(offset);

                // Store image by offset for later upload (when quotation is updated)
                window.quotationImages[offset] = {
                    file: file,
                    offset: offset,
                    package_id: packageId,
                    day_number: offset,
                    preview_url: e.target.result,
                    uploaded: false
                };

                console.log("DEBUG: Stored image for offset " + offset + " in edit mode:", file.name, "Package ID:", packageId);
                console.log("DEBUG: Upload button hidden:", !$('label[for="day_image_' + offset + '"]').is(':visible'));
                console.log("DEBUG: Preview div shown:", $('#day_image_preview_' + offset).is(':visible'));
            }
            reader.onerror = function() {
                console.error("FileReader error");
                alert('Error reading file');
            }
            reader.readAsDataURL(file);
        } else {
            console.log("No file selected or file input is empty");
        }
    }

    function removeDayImage(offset) {
        console.log("removeDayImage called for offset:", offset);

        // Clear the file input
        $('#day_image_' + offset).val('');

        // Hide the preview div completely
        $('#day_image_preview_' + offset).hide();

        // Clear the image source
        $('#preview_img_' + offset).attr('src', '');

        // Clear the existing image path
        $('#existing_image_path_' + offset).val('');

        // Clear any stored image data
        if (window.quotationImages && window.quotationImages[offset]) {
            delete window.quotationImages[offset];
            console.log("Cleared stored image data for offset:", offset);
        }

        // Show ONLY the upload button (hide any preview elements)
        $('label[for="day_image_' + offset + '"]').show();

        // Hide any remove buttons
        $('#day_image_preview_' + offset).find('button[onclick*="removeDayImage"]').hide();

        // Reset the file input to ensure change event fires on next selection
        $('#day_image_' + offset).prop('value', '');

        console.log("Image removed and upload button shown for offset:", offset);
        console.log("Upload button visible:", $('label[for="day_image_' + offset + '"]').is(':visible'));
        console.log("Preview div hidden:", !$('#day_image_preview_' + offset).is(':visible'));
    }

    // Handle day image upload (deferred until quotation update)
    function uploadDayImage(offset) {
        console.log("uploadDayImage called with offset:", offset);

        // Check if image is already stored
        if (!window.quotationImages || !window.quotationImages[offset]) {
            alert('Please select an image first');
            return;
        }

        // Show message that image will be uploaded when quotation is updated
        $('#upload_status_' + offset).html('<span style="color: blue;">✓ Ready for upload on update</span>');
        $('#upload_btn_' + offset).prop('disabled', true).text('Ready to Upload');

        console.log("Image marked for upload when quotation is updated:", window.quotationImages[offset]);
    }

    function removeSavedImage(offset, imageUrl) {
        if (confirm('Are you sure you want to remove this image?')) {
            // AJAX call to delete image from server and database
            $.ajax({
                type: 'POST',
                url: '<?php echo BASE_URL; ?>controller/package_tour/quotation/delete_itinerary_image.php',
                data: {
                    quotation_id: '<?php echo $quotation_id; ?>',
                    package_id: '<?php echo $package_id; ?>',
                    day_number: offset,
                    image_url: imageUrl
                },
                success: function(response) {
                    if (response.success) {
                        $('#saved_image_' + offset).remove();
                        $('label[for="day_image_' + offset + '"]').show();
                    } else {
                        alert('Error removing image: ' + response.message);
                    }
                },
                error: function() {
                    alert('Error removing image. Please try again.');
                }
            });
        }
    }

    function replaceSavedImage(offset) {
        // Hide the saved image and show the file input
        $('#saved_image_' + offset).hide();
        $('label[for="day_image_' + offset + '"]').show();

        // Clear any existing preview
        $('#day_image_preview_' + offset).hide();

        // Clear the file input value to ensure change event fires
        $('#day_image_' + offset).val('');

        // Clear any stored image data
        if (window.quotationImages && window.quotationImages[offset]) {
            delete window.quotationImages[offset];
        }

        // Set a flag to indicate this is a replacement
        window.replacingImage = offset;

        console.log("Ready to replace image for offset:", offset);
    }


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
        var text = textarea.value;
        var content = text.replace(/\*\*(.*?)\*\*/g, '<b>$1</b>');

        // Replace markdown-style underline (__text__) with <u> tags
        content = content.replace(/__(.*?)__/g, '<u>$1</u>');
        textarea.value = content;
        //console.log(content);    
    });



    function switch_to_tab1() {
        // Reset user modification flag when going back to Tab1
        // This allows re-sync when returning to Tab2
        sessionStorage.removeItem('user_modified_nights');
        console.log('Reset user_modified_nights flag - returning to Tab1 (update)');

        $('#tab2_head').removeClass('active');
        $('#tab1_head').addClass('active');
        $('.bk_tab').removeClass('active');
        $('#tab1').addClass('active');
        $('html, body').animate({
            scrollTop: $('.bk_tab_head').offset().top
        }, 200);
    }


    // Function to submit tab2 form
    function submitTab2Form() {
        console.log("TAB2: submitTab2Form called");

        // Validate form first
        if (!$('#frm_tab2_u').valid()) {
            console.log("TAB2: Form validation failed");
            return false;
        }

        var base_url = $('#base_url').val();
        var checked_programe_arr = new Array();
        var attraction_arr = new Array();
        var program_arr = new Array();
        var stay_arr = new Array();
        var meal_plan_arr = new Array();
        var package_p_id_arr = new Array();
        var day_count_arr = new Array();
        var day_image_arr = new Array();
        var existing_image_path_arr = new Array();
        var count = 0;

        var table = document.getElementById("dynamic_table_list_update");
        var rowCount = table.rows.length;
        for (var i = 0; i < rowCount; i++) {
            var row = table.rows[i];

            // Skip header row if it exists
            if (i === 0 && row.cells.length < 5) {
                continue;
            }

            // Get data using more robust selectors instead of hardcoded cell positions
            var checked_programe = false;
            var attraction = '';
            var program = '';
            var stay = '';
            var meal_plan = '';

            // Find checkbox (checked_programe)
            var checkbox = row.querySelector('input[type="checkbox"]');
            if (checkbox) {
                checked_programe = checkbox.checked;
            }

            // Find special attraction input
            var attractionInput = row.querySelector('input[id*="special_attaraction"]');
            if (attractionInput) {
                attraction = attractionInput.value;
            }

            // Find day program textarea
            var programTextarea = row.querySelector('textarea[id*="day_program"]');
            if (programTextarea) {
                program = programTextarea.value;
            }

            // Find overnight stay input
            var stayInput = row.querySelector('input[id*="overnight_stay"]');
            if (stayInput) {
                stay = stayInput.value;
            }

            // Find meal plan select
            var mealPlanSelect = row.querySelector('select[id*="meal_plan"]');
            if (mealPlanSelect) {
                meal_plan = mealPlanSelect.value;
            }

            console.log("TAB2: Row", i, "data - attraction:", attraction, "program:", program, "stay:", stay, "meal_plan:", meal_plan, "checked:", checked_programe);

            // Get image data
            var day_image = '';
            var existing_image_path = '';

            // Look for image inputs in the row
            var imageInput = row.querySelector('input[id^="day_image_"]');
            var existingImageInput = row.querySelector('input[id^="existing_image_path_"]');

            if (existingImageInput && existingImageInput.value && existingImageInput.value !== '') {
                // Prioritize existing_image_path if it has a value (from modal selection)
                existing_image_path = existingImageInput.value;
                day_image = existingImageInput.value; // Use the same value for day_image
                console.log("TAB2: Using existing_image_path for day", i, ":", existing_image_path);
            } else if (imageInput) {
                day_image = imageInput.value;
                console.log("TAB2: Using day_image for day", i, ":", day_image);
            }

            if (existingImageInput) {
                existing_image_path = existingImageInput.value;
            }

            // Get package_id using robust selector
            var package_id1 = '';
            var packageIdInput = row.querySelector('input[name="package_id_n"]');
            if (packageIdInput) {
                package_id1 = packageIdInput.value;
                console.log("TAB2: Found package_id_n with value:", package_id1);
            } else {
                // Fallback to the global package_id
                package_id1 = '<?php echo $package_id; ?>';
                console.log("TAB2: Using global package_id:", package_id1);
            }
            if (checked_programe) {
                count++;
                if (program == "") {
                    error_msg_alert('Daywise program is mandatory in row' + (i + 1));
                    return false;
                }

                // Validate form elements if they exist
                var flag1 = true,
                    flag2 = true,
                    flag3 = true;

                // Check special attraction validation
                var attractionInput = row.querySelector('input[id*="special_attaraction"]');
                if (attractionInput && attractionInput.id) {
                    try {
                        flag1 = validate_spattration(attractionInput.id);
                    } catch (e) {
                        console.log('TAB2: validate_spattration error:', e);
                        flag1 = true; // Continue if validation fails
                    }
                }

                // Check day program validation
                var programTextarea = row.querySelector('textarea[id*="day_program"]');
                if (programTextarea && programTextarea.id) {
                    try {
                        flag2 = validate_dayprogram(programTextarea.id);
                    } catch (e) {
                        console.log('TAB2: validate_dayprogram error:', e);
                        flag2 = true; // Continue if validation fails
                    }
                }

                // Check overnight stay validation
                var stayInput = row.querySelector('input[id*="overnight_stay"]');
                if (stayInput && stayInput.id) {
                    try {
                        flag3 = validate_onstay(stayInput.id);
                    } catch (e) {
                        console.log('TAB2: validate_onstay error:', e);
                        flag3 = true; // Continue if validation fails
                    }
                }
                if (!flag1 || !flag2 || !flag3) {
                    return false;
                }

                if (attraction == "") {
                    error_msg_alert("Attraction Is Required At Row:" + (i + 1));
                    return false;
                }
                if (program == "") {
                    error_msg_alert("Program Is Required At Row:" + (i + 1));

                    return false;
                }
                if (stay == "") {
                    error_msg_alert("Overnighht Stay Is Required At Row:" + (i + 1));

                    return false;
                }
            }

            checked_programe_arr.push(checked_programe);
            attraction_arr.push(attraction);
            program_arr.push(program);
            stay_arr.push(stay);
            meal_plan_arr.push(meal_plan);
            package_p_id_arr.push(package_id1);
            day_count_arr.push(i + 1); // Use row index + 1 as day count
            day_image_arr.push(day_image);
            existing_image_path_arr.push(existing_image_path);
        }

        // Debug: Log the arrays being sent
        console.log("TAB2: Final data being sent:");
        console.log("package_p_id_arr:", package_p_id_arr);
        console.log("checked_programe_arr:", checked_programe_arr);
        console.log("day_count_arr:", day_count_arr);
        console.log("attraction_arr:", attraction_arr);
        console.log("program_arr:", program_arr);
        console.log("day_image_arr:", day_image_arr);
        console.log("existing_image_path_arr:", existing_image_path_arr);
        console.log("Total rows processed:", count);

        // Store form data in sessionStorage to prevent URL length issues
        var formData = {
            checked_programe_arr: checked_programe_arr,
            attraction_arr: attraction_arr,
            program_arr: program_arr,
            stay_arr: stay_arr,
            meal_plan_arr: meal_plan_arr,
            package_p_id_arr: package_p_id_arr,
            day_count_arr: day_count_arr,
            day_image_arr: day_image_arr,
            existing_image_path_arr: existing_image_path_arr,
            dest_id: $('#dest_name').val(),
            package_id: $('#img_package_id').val(),
            nights_filter: $('#nights_filter').val()
        };

        sessionStorage.setItem('tab2_form_data', JSON.stringify(formData));
        console.log("Form data stored in sessionStorage:", formData);

        var dest_id = $('#dest_name').val();
        var package_id = $('#img_package_id').val();
        var package_id_arr = [package_id];

        // Save the itinerary data to server first
        console.log('TAB2: Starting AJAX call to save itinerary data');
        console.log('TAB2: URL:', base_url + 'controller/package_tour/quotation/quotation_update.php');
        console.log('TAB2: Data being sent:', {
            quotation_id: '<?php echo $quotation_id; ?>',
            package_id: '<?php echo $package_id; ?>',
            checked_programe_arr: checked_programe_arr,
            attraction_arr: attraction_arr,
            program_arr: program_arr,
            stay_arr: stay_arr,
            meal_plan_arr: meal_plan_arr,
            package_p_id_arr: package_p_id_arr,
            day_count_arr: day_count_arr,
            day_image_arr: day_image_arr,
            existing_image_path_arr: existing_image_path_arr,
            dest_id: $('#dest_name').val(),
            package_id: $('#img_package_id').val(),
            nights_filter: $('#nights_filter').val(),
            action: 'save_itinerary_only'
        });

        $.ajax({
            url: base_url + 'controller/package_tour/quotation/quotation_update.php',
            type: 'post',
            beforeSend: function() {
                console.log('TAB2: AJAX call starting...');
            },
            data: {
                quotation_id: '<?php echo $quotation_id; ?>',
                package_id: '<?php echo $package_id; ?>',
                checked_programe_arr: checked_programe_arr,
                attraction_arr: attraction_arr,
                program_arr: program_arr,
                stay_arr: stay_arr,
                meal_plan_arr: meal_plan_arr,
                package_p_id_arr: package_p_id_arr,
                day_count_arr: day_count_arr,
                day_image_arr: day_image_arr,
                existing_image_path_arr: existing_image_path_arr,
                dest_id: $('#dest_name').val(),
                package_id: $('#img_package_id').val(),
                nights_filter: $('#nights_filter').val(),
                action: 'save_itinerary_only' // Flag to indicate this is just saving itinerary data
            },
            success: function(result) {
                console.log('TAB2: AJAX success - Itinerary data saved successfully:', result);

                // Parse JSON response if it's a string
                var response = result;
                if (typeof result === 'string') {
                    try {
                        response = JSON.parse(result);
                    } catch (e) {
                        console.log('TAB2: Response is not JSON, treating as plain text');
                    }
                }

                // Load daywise images after successful save
                $.ajax({
                    type: 'post',
                    url: '../../inc/get_packages_days.php',
                    data: {
                        dest_id: dest_id,
                        day_count_arr: day_count_arr,
                        package_id_arr: package_id_arr
                    },
                    success: function(result) {
                        $('#daywise_image_select').html(result);
                    },
                    error: function(result) {
                        console.log('Daywise image loading error:', result.responseText);
                    }
                });

                $('#tab2_head').addClass('done');
                $('#tab3_head').addClass('active');
                $('.bk_tab').removeClass('active');
                $('#tab3').addClass('active');
                $('html, body').animate({
                    scrollTop: $('.bk_tab_head').offset().top
                }, 200);
            },
            error: function(xhr, status, error) {
                console.error('TAB2: AJAX error - Error saving itinerary data:', error);
                console.error('TAB2: AJAX error - Status:', status);
                console.error('TAB2: AJAX error - Response:', xhr.responseText);
                console.error('TAB2: AJAX error - XHR object:', xhr);

                // Fallback: proceed to tab 3 even if save fails
                console.log('TAB2: Proceeding to tab 3 despite save error');
                $('#tab2_head').addClass('done');
                $('#tab3_head').addClass('active');
                $('.bk_tab').removeClass('active');
                $('#tab3').addClass('active');
                $('html, body').animate({
                    scrollTop: $('.bk_tab_head').offset().top
                }, 200);

                error_msg_alert('Error saving itinerary data, but proceeding to next tab. Please save again later.');
            },
            timeout: 10000 // 10 second timeout
        });
    }

    // Initialize form validation
    $('#frm_tab2_u').validate({
        rules: {},
        submitHandler: function(form) {
            // This will be handled by submitTab2Form() function
            return false;
        }
    });

    // Initialize nights filter and destination when tab2 loads for update
    $(document).ready(function() {
        // Function to sync nights filter and destination
        function syncNightsFilter(force_sync = false) {
            // Get total days from tab1 first
            var total_days = $('#total_days12').val();
            var selected_nights = sessionStorage.getItem('selected_nights');
            var destination_id = sessionStorage.getItem('selected_destination_id');
            var destination_name = sessionStorage.getItem('selected_destination_name');
            var current_nights_filter = $('#nights_filter').val();
            var user_modified_nights = sessionStorage.getItem('user_modified_nights');

            console.log('Tab2 ready (update) - total_days from tab1:', total_days);
            console.log('Tab2 ready (update) - selected_nights from sessionStorage:', selected_nights);
            console.log('Tab2 ready (update) - destination_id:', destination_id, 'destination_name:', destination_name);
            console.log('Tab2 ready (update) - current_nights_filter:', current_nights_filter, 'user_modified_nights:', user_modified_nights);

            // Use total_days from tab1 as primary source (only if user hasn't manually modified)
            if (total_days && total_days > 0 && (!user_modified_nights || force_sync)) {
                selected_nights = total_days;
                sessionStorage.setItem('selected_nights', selected_nights);
                console.log('Using total_days from tab1 (update):', selected_nights);
            } else if (selected_nights) {
                console.log('Using stored nights from sessionStorage (update):', selected_nights);
            }

            // Sync nights filter (only if user hasn't manually changed it or if force_sync is true)
            if (selected_nights && selected_nights > 0 && (!user_modified_nights || force_sync)) {
                $('#nights_filter').val(selected_nights);
                console.log('Initialized nights filter with value (update):', selected_nights);
            }

            // Sync destination
            if (destination_id && destination_name) {
                $('#dest_name').val(destination_id);
                $('#dest_name').trigger('change');
                console.log('Initialized destination with (update):', destination_name);
            }

            // Trigger package filtering if both destination and nights are available
            if ((destination_id || $('#dest_name').val()) && (selected_nights || $('#nights_filter').val())) {
                if (typeof load_packages_with_filter === 'function') {
                    load_packages_with_filter();
                }
            }
        }

        // Initial sync
        syncNightsFilter();

        // Test preselectPackage immediately
        console.log('=== TESTING PRESELECT PACKAGE ON PAGE LOAD ===');
        setTimeout(function() {
            preselectPackage();
        }, 2000);

        // Add click event listener for image upload buttons
        $(document).on('click', 'label[for^="day_image_"]', function() {
            var forAttr = $(this).attr('for');
            var offset = forAttr.replace('day_image_', '');
            console.log('Upload button clicked for offset:', offset);

            // Trigger the file input click
            $('#day_image_' + offset).click();
        });

        // Add change event listener for file inputs
        $(document).on('change', 'input[id^="day_image_"]', function() {
            var offset = $(this).attr('id').replace('day_image_', '');
            console.log('File input changed for offset:', offset);
            previewDayImage(this, offset);
        });

        // Initialize image states on page load
        function initializeImageStates() {
            $('input[id^="day_image_"]').each(function() {
                var offset = $(this).attr('id').replace('day_image_', '');
                var hasExistingImage = $('#existing_image_path_' + offset).val() && $('#existing_image_path_' + offset).val() !== '';
                var hasPreviewImage = $('#preview_img_' + offset).attr('src') && $('#preview_img_' + offset).attr('src') !== '';

                console.log('Initializing image state for offset:', offset, 'hasExisting:', hasExistingImage, 'hasPreview:', hasPreviewImage);

                if (hasExistingImage || hasPreviewImage) {
                    // Show preview, hide upload button
                    $('#day_image_preview_' + offset).show();
                    $('label[for="day_image_' + offset + '"]').hide();
                    $('#day_image_preview_' + offset).find('button[onclick*="removeDayImage"]').show();
                } else {
                    // Show upload button, hide preview
                    $('#day_image_preview_' + offset).hide();
                    $('label[for="day_image_' + offset + '"]').show();
                    $('#day_image_preview_' + offset).find('button[onclick*="removeDayImage"]').hide();
                }
            });
        }

        // Initialize image states after a short delay
        setTimeout(initializeImageStates, 1000);

        // Also sync when tab2 becomes visible (in case of dynamic loading)
        $(document).on('click', '#tab2_head', function() {
            // Check if user modification flag was reset (meaning user went back to Tab1)
            var user_modified_nights = sessionStorage.getItem('user_modified_nights');
            var force_sync = !user_modified_nights; // Force sync if flag was reset

            console.log('Tab2 clicked (update) - user_modified_nights:', user_modified_nights, 'force_sync:', force_sync);
            setTimeout(function() {
                syncNightsFilter(force_sync);
            }, 50);
        });

        // Reset user modification flag when clicking on Tab1 header
        $(document).on('click', '#tab1_head', function() {
            sessionStorage.removeItem('user_modified_nights');
            console.log('Reset user_modified_nights flag - clicked Tab1 header (update)');
        });

        // Fallback: Check every 500ms for total_days and destination changes
        setInterval(function() {
            var current_total_days = $('#total_days12').val();
            var current_nights_filter = $('#nights_filter').val();
            var current_destination_id = sessionStorage.getItem('selected_destination_id');
            var current_dest_name = $('#dest_name').val();
            var user_modified_nights = sessionStorage.getItem('user_modified_nights');

            var needs_sync = false;

            // Check if total_days changed (only if user hasn't manually modified nights)
            if (current_total_days && current_total_days > 0 && current_nights_filter != current_total_days && !user_modified_nights) {
                console.log('Detected total_days change, syncing nights filter (update):', current_total_days);
                needs_sync = true;
            }

            // Check if destination changed
            if (current_destination_id && current_dest_name != current_destination_id) {
                console.log('Detected destination change, syncing destination (update):', current_destination_id);
                needs_sync = true;
            }

            if (needs_sync) {
                syncNightsFilter();
            }
        }, 500);
    });

    // Function to load packages with both destination and nights filter for update
    function load_packages_with_filter() {
        var dest_id = $('#dest_name').val();
        var total_nights = $('#nights_filter').val() || sessionStorage.getItem('selected_nights');

        console.log('TAB2: load_packages_with_filter called - dest_id:', dest_id, 'total_nights:', total_nights);

        if (dest_id) {
            // Update sessionStorage with current nights selection
            if (total_nights) {
                sessionStorage.setItem('selected_nights', total_nights);
            }

            // Call the package loading function with nights parameter
            package_dynamic_reflect_with_nights('dest_name', total_nights);
        } else {
            console.log('TAB2: No destination selected, cannot load packages');
            // Show message to select destination first
            $('#package_name_div').html('<div class="alert alert-info text-center">Please select a destination first to view packages.</div>');
        }
    }

    // Function to pre-select the saved package (checkbox only)
    function preselectPackage() {
        var saved_package_id = $('#img_package_id').val();
        console.log('=== PRESELECT PACKAGE DEBUG ===');
        console.log('Saved package ID from img_package_id:', saved_package_id);
        console.log('Type of saved_package_id:', typeof saved_package_id);

        if (saved_package_id) {
            console.log('Pre-selecting package for update:', saved_package_id);

            // Try immediate selection first
            var immediate_radio = $('input[name="custom_package"][value="' + saved_package_id + '"]');
            console.log('Immediate radio button found:', immediate_radio.length);

            if (immediate_radio.length > 0) {
                immediate_radio.prop('checked', true).trigger('change');
                console.log('Package immediately checked:', saved_package_id);
                return;
            }

            // Wait for packages to load, then select the saved one
            setTimeout(function() {
                console.log('=== TIMEOUT DEBUG ===');
                console.log('Looking for package with ID:', saved_package_id);
                console.log('All available radio buttons:', $('input[name="custom_package"]').map(function() {
                    return this.value;
                }).get());
                console.log('All radio button elements:', $('input[name="custom_package"]'));

                // Try to find the radio button
                var package_radio = $('input[name="custom_package"][value="' + saved_package_id + '"]');
                console.log('Found radio button:', package_radio.length);
                console.log('Radio button element:', package_radio);

                if (package_radio.length > 0) {
                    package_radio.prop('checked', true);
                    console.log('Package radio button checked:', saved_package_id);

                    // Also trigger change event to ensure any listeners are notified
                    package_radio.trigger('change');
                } else {
                    console.log('Package not found in loaded packages:', saved_package_id);
                    console.log('Available packages:', $('input[name="custom_package"]').map(function() {
                        return this.value;
                    }).get());

                    // Try different selectors
                    var alt_radio = $('input[value="' + saved_package_id + '"]');
                    console.log('Alternative selector found:', alt_radio.length);

                    if (alt_radio.length > 0) {
                        alt_radio.prop('checked', true).trigger('change');
                        console.log('Package checked with alternative selector:', saved_package_id);
                    }
                }
            }, 1500);
        } else {
            console.log('No saved package ID found!');
            console.log('img_package_id element:', $('#img_package_id'));
            console.log('img_package_id value:', $('#img_package_id').val());
        }
    }

    // Function to filter packages by nights in tab2 for update
    function filter_packages_by_nights() {
        var total_nights = $('#nights_filter').val();

        // Mark that user has manually modified the nights filter
        sessionStorage.setItem('user_modified_nights', 'true');
        console.log('User manually changed nights filter to (update):', total_nights);

        if (total_nights) {
            sessionStorage.setItem('selected_nights', total_nights);
        } else {
            sessionStorage.removeItem('selected_nights');
        }

        // Reload packages with the new filter
        load_packages_with_filter();
    }

    // Function to load packages with explicit nights parameter for update
    function package_dynamic_reflect_with_nights(dest_name, total_nights) {
        var dest_id = $('#' + dest_name).val();
        var base_url = $('#base_url').val();

        // Ensure total_nights is not null or undefined
        if (!total_nights) {
            total_nights = '';
        }

        var ajax_data = {
            dest_id: dest_id,
            total_nights: total_nights,
            current_package_id: $('#img_package_id').val(), // Pass current package ID for pre-selection
            quotation_id: $('#quotation_id').val() // Pass quotation ID for loading quotation-specific data
        };

        // Debug: Log the data being sent
        console.log('AJAX Data being sent:', ajax_data);
        console.log('Quotation ID from input:', $('#quotation_id').val());
        console.log('Quotation ID length:', $('#quotation_id').val() ? $('#quotation_id').val().length : 'undefined');

        console.log('TAB2: Making AJAX call with data:', ajax_data);
        console.log('TAB2: AJAX URL:', base_url + 'view/package_booking/quotation/inc/get_packages.php?v=' + Date.now());

        $.ajax({
            type: 'post',
            url: base_url + 'view/package_booking/quotation/inc/get_packages.php?v=' + Date.now(),
            data: ajax_data,
            success: function(result) {
                console.log('=== PACKAGE LOADING SUCCESS ===');
                console.log('Result length:', result.length);
                console.log('Result preview:', result.substring(0, 200));
                $('#package_name_div').html(result);

                // Pre-select the saved package after loading
                console.log('Calling preselectPackage...');
                setTimeout(function() {
                    preselectPackage();
                }, 500); // Small delay to ensure DOM is ready
            },
            error: function(xhr, status, error) {
                console.log('Package loading error:', status, error);
                console.log('Response text:', xhr.responseText);
                $('#package_name_div').html('<div class="alert alert-danger">Error loading packages: ' + error + '</div>');
            }
        });
    }

    // Initialize tab2 for edit mode
    $(document).ready(function() {
        console.log("TAB2: Document ready - initializing edit mode");
        console.log("TAB2: jQuery version:", $.fn.jquery);
        console.log("TAB2: Base URL:", $('#base_url').val());

        // Initialize destination and nights from quotation data
        var dest_id = $('#dest_name_hidden').val();
        var total_nights = '<?= $sq_quotation['total_days'] ?>';

        console.log("TAB2: Initializing with dest_id:", dest_id, "total_nights:", total_nights);

        // Set the destination dropdown
        if (dest_id) {
            $('#dest_name').val(dest_id);
            console.log("TAB2: Set destination to:", dest_id);
        } else {
            console.log("TAB2: No destination ID found, checking dropdown options");
            // Fallback: try to find the destination by name
            var dest_name = '<?= $sq_destination['dest_name'] ?? '' ?>';
            if (dest_name) {
                $('#dest_name option').each(function() {
                    if ($(this).text().trim() === dest_name.trim()) {
                        $(this).prop('selected', true);
                        console.log("TAB2: Set destination by name:", dest_name);
                        return false;
                    }
                });
            }
        }

        // Set the nights filter
        if (total_nights) {
            $('#nights_filter').val(total_nights);
            sessionStorage.setItem('selected_nights', total_nights);
            console.log("TAB2: Set nights filter to:", total_nights);
        }

        // Load packages automatically on page load
        console.log("TAB2: Auto-loading packages for edit mode");
        console.log("TAB2: Destination value:", $('#dest_name').val());
        console.log("TAB2: Nights value:", $('#nights_filter').val());
        console.log("TAB2: Hidden dest_id:", $('#dest_name_hidden').val());

        // Small delay to ensure DOM is fully ready
        setTimeout(function() {
            console.log("TAB2: About to call load_packages_with_filter");
            load_packages_with_filter();
        }, 100);

        // Handle inclusions and exclusions WYSIWYG
        console.log("TAB2: Inclusions textarea exists:", $('#inclusions1').length > 0);
        console.log("TAB2: Exclusions textarea exists:", $('#exclusions1').length > 0);
        console.log("TAB2: Inclusions content length:", $('#inclusions1').val().length);
        console.log("TAB2: Exclusions content length:", $('#exclusions1').val().length);

        // Store the original content before any WYSIWYG initialization
        var inclusionsContent = $('#inclusions1').val();
        var exclusionsContent = $('#exclusions1').val();

        console.log("TAB2: Stored content - inclusions length:", inclusionsContent.length);
        console.log("TAB2: Stored content - exclusions length:", exclusionsContent.length);

        // Remove the feature_editor class temporarily to prevent global initialization
        $('#inclusions1, #exclusions1').removeClass('feature_editor');

        // Wait for global WYSIWYG initialization to complete, then initialize our fields
        setTimeout(function() {
            console.log("TAB2: Initializing WYSIWYG for inclusions and exclusions with preserved content");

            // Re-add the feature_editor class and initialize with preserved content
            $('#inclusions1, #exclusions1').addClass('feature_editor');

            if (typeof $().wysiwyg === 'function') {
                $('#inclusions1, #exclusions1').wysiwyg({
                    controls: 'bold,italic,|,undo,redo,image|h1,h2,h3,decreaseFontSize,highlight',
                    initialContent: ''
                });

                // Set the preserved content
                if (inclusionsContent) {
                    $('#inclusions1').wysiwyg('setContent', inclusionsContent);
                    console.log("TAB2: Set inclusions content in WYSIWYG");
                }
                if (exclusionsContent) {
                    $('#exclusions1').wysiwyg('setContent', exclusionsContent);
                    console.log("TAB2: Set exclusions content in WYSIWYG");
                }
            } else {
                console.log("TAB2: WYSIWYG function not available, restoring original content");
                $('#inclusions1').val(inclusionsContent);
                $('#exclusions1').val(exclusionsContent);
            }
        }, 2000); // Wait 2 seconds for global initialization
    });
</script>

<!-- Image Zoom Functionality -->
<style>
    .image-zoom-container {
        position: relative;
        display: inline-block;
        overflow: hidden;
        border-radius: 8px;
        cursor: zoom-in;
    }
    
    .image-zoom-container img {
        transition: transform 0.3s ease;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .image-zoom-container:hover img {
        transform: scale(1.5);
    }
    
    .image-zoom-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        z-index: 9999;
        display: none;
        justify-content: center;
        align-items: center;
        cursor: zoom-out;
    }
    
    .image-zoom-overlay img {
        max-width: 90%;
        max-height: 90%;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
    }
    
    .image-zoom-close {
        position: absolute;
        top: 20px;
        right: 30px;
        color: white;
        font-size: 30px;
        cursor: pointer;
        z-index: 10000;
    }
    
    .image-zoom-close:hover {
        color: #ff6b6b;
    }
</style>

<script>
    // Image zoom functionality for update mode
    function initImageZoomUpdate() {
        // Add zoom functionality to all existing images
        $('.image-zoom-container img').off('click').on('click', function(e) {
            e.preventDefault();
            showImageZoom($(this).attr('src'));
        });
    }
    
    function showImageZoom(imageSrc) {
        // Create overlay
        var overlay = $('<div class="image-zoom-overlay">' +
            '<span class="image-zoom-close">&times;</span>' +
            '<img src="' + imageSrc + '" alt="Zoomed Image">' +
            '</div>');
        
        $('body').append(overlay);
        overlay.fadeIn(300);
        
        // Close on click
        overlay.on('click', function(e) {
            if (e.target === this || $(e.target).hasClass('image-zoom-close')) {
                overlay.fadeOut(300, function() {
                    overlay.remove();
                });
            }
        });
        
        // Close on escape key
        $(document).on('keyup.imageZoom', function(e) {
            if (e.keyCode === 27) { // Escape key
                overlay.fadeOut(300, function() {
                    overlay.remove();
                });
                $(document).off('keyup.imageZoom');
            }
        });
    }
    
    // Initialize zoom when packages are loaded
    $(document).ready(function() {
        // Initialize zoom for existing images
        initImageZoomUpdate();
        
        // Re-initialize zoom when new content is loaded
        $(document).on('DOMNodeInserted', function() {
            setTimeout(function() {
                initImageZoomUpdate();
            }, 100);
        });
    });
    
    // Function to wrap images with zoom container
    function wrapImagesWithZoomUpdate() {
        $('img[id^="preview_img_"]').each(function() {
            if (!$(this).parent().hasClass('image-zoom-container')) {
                $(this).wrap('<div class="image-zoom-container"></div>');
            }
        });
    }
    
    // Call wrapImagesWithZoom when packages are loaded
    $(document).ajaxComplete(function() {
        setTimeout(function() {
            wrapImagesWithZoomUpdate();
            initImageZoomUpdate();
        }, 500);
    });
    
    // Also wrap images when new rows are added dynamically
    function addItineraryRow(package_id) {
        // Original addItineraryRow function code would go here
        // For now, just call the wrap function after adding
        setTimeout(function() {
            wrapImagesWithZoomUpdate();
            initImageZoomUpdate();
        }, 100);
    }
</script>
<?= end_panel(); ?>