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

<?php
// Get quotation_id and package_id from URL parameters or form data
if (!isset($quotation_id) || empty($quotation_id)) {
    $quotation_id = isset($_GET['quotation_id']) ? $_GET['quotation_id'] : (isset($_POST['quotation_id']) ? $_POST['quotation_id'] : '');
}
if (!isset($package_id) || empty($package_id)) {
    $package_id = isset($_GET['package_id']) ? $_GET['package_id'] : (isset($_POST['package_id']) ? $_POST['package_id'] : '');
}

// If package_id is still empty, try to get it from the quotation data
if (empty($package_id) && !empty($quotation_id)) {
    $package_query = "SELECT package_id FROM package_tour_quotation_master WHERE quotation_id = '$quotation_id' LIMIT 1";
    $package_result = mysqlQuery($package_query);
    if (mysqli_num_rows($package_result) > 0) {
        $package_row = mysqli_fetch_assoc($package_result);
        $package_id = $package_row['package_id'];
        echo "<!-- DEBUG: Got package_id from quotation_master: $package_id -->";
    }
}

// If still empty, try to get it from package_quotation_program
if (empty($package_id) && !empty($quotation_id)) {
    $package_query = "SELECT package_id FROM package_quotation_program WHERE quotation_id = '$quotation_id' LIMIT 1";
    $package_result = mysqlQuery($package_query);
    if (mysqli_num_rows($package_result) > 0) {
        $package_row = mysqli_fetch_assoc($package_result);
        $package_id = $package_row['package_id'];
        echo "<!-- DEBUG: Got package_id from package_quotation_program: $package_id -->";
    }
}

// Debug information
echo "<!-- Debug Info: quotation_id = " . (isset($quotation_id) ? $quotation_id : 'NOT SET') . " -->";
echo "<!-- Debug Info: package_id = " . (isset($package_id) ? $package_id : 'NOT SET') . " -->";
if (isset($quotation_id) && !empty($quotation_id)) {
    $debug_query = "select * from package_quotation_program where quotation_id = '$quotation_id'";
    $debug_result = mysqlQuery($debug_query);
    $debug_count = mysqli_num_rows($debug_result);
    echo "<!-- Debug Info: Query result count = " . $debug_count . " -->";
}
?>



<form id="frm_tab2_u">
    <div class="app_panel">

        <div class="container" style="width:100% !important;">

            <div class="row">
                <div class="col-md-6 col-sm-4 col-xs-12" style="margin-left:15px;">
                    <?php $sq_pacakge = mysqli_fetch_assoc(mysqlQuery("select * from custom_package_master where package_id='$sq_quotation[package_id]'")) ?>
                    <input type="text" value="<?= $sq_pacakge['package_name'] . ' (' . ($sq_pacakge['total_days'] + 1) . 'D/' . $sq_pacakge['total_nights'] . 'N )' ?>" readonly>
                    <input type="hidden" value="<?= $sq_pacakge['dest_id'] ?>" id='dest_name'>
                    <input type="hidden" value="<?= $package_id ?>" id='img_package_id'>
                    <input type='hidden' id='pckg_daywise_url' name='pckg_daywise_url' />
                </div>
                <div class="col-md-3 col-sm-4 col-xs-12 mg_bt_20">
                    <select name="nights_filter" id="nights_filter" title="Filter by Nights" 
                        onchange="filter_packages_by_nights()" style="width:100%">
                        <option value="">All Nights</option>
                        <?php
                        // Generate options for 1 to 30 nights
                        for($i = 1; $i <= 30; $i++) {
                            $selected = ($sq_quotation['total_days'] == $i) ? 'selected' : '';
                            echo "<option value='$i' $selected>$i Night" . ($i > 1 ? 's' : '') . "</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-sm-8 col-xs-12 no-pad">
                    <div class="col-md-12 app_accordion">
                        <div class="panel-group main_block" id="accordion" role="tablist" aria-multiselectable="true">
                            <div class="panel-body">
                                <div class="col-md-12 no-pad" id="div_list1">
                                    <div class="row mg_bt_10">
                                        <div class="col-xs-12 text-right text_center_xs">
                                            <button type="button" class="btn btn-excel btn-sm" onClick="addRow('dynamic_table_list_update','','itinerary')"><i class="fa fa-plus"></i></button>
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
                                                
                                                // First, let's check what quotation IDs exist in the table
                                                $debug_all = mysqlQuery("SELECT DISTINCT quotation_id FROM package_quotation_program ORDER BY quotation_id DESC LIMIT 10");
                                                echo "<!-- Debug: Recent quotation IDs in database: ";
                                                while($debug_row = mysqli_fetch_assoc($debug_all)) {
                                                    echo $debug_row['quotation_id'] . ", ";
                                                }
                                                echo " -->";
                                                
                                                // Get itinerary records for this quotation
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
                                                    <td style="width: 100px;"><input type="text" id="special_attaraction<?php echo $offset; ?>-u" onchange="validate_spaces(this.id);validate_spattration(this.id);" name="special_attaraction" class="form-control mg_bt_10" placeholder="Special Attraction" title="Special Attraction" style='width:220px;margin-top: 35px;'  value="<?php echo $row_program['attraction']; ?>"></td>
                                                    <!-- <td style="width: 100px;max-width: 594px;overflow: hidden;"><textarea id="day_program<?php echo $offset; ?>-u" name="day_program" class="form-control mg_bt_10" title="Day-wise Program" rows="3" placeholder="*Day-wise Program" onchange="validate_spaces(this.id);validate_dayprogram(this.id);" style='width:400px;' value="<?php echo $row_program['day_wise_program']; ?>"><?php echo $row_program['day_wise_program']; ?></textarea>
                                                    </td> -->
                                                    <td  class='col-md-5 no-pad' style="max-width:800px;overflow: hidden;position: relative;"><textarea id="day_program<?php echo $offset; ?>-u" name="day_program" class="form-control mg_bt_10 day_program"  style=" height:900px;" placeholder="*Day Program" title="Day-wise Program" onchange="validate_spaces(this.id);validate_dayprogram(this.id);" rows="3" value="<?php echo $row_program['day_wise_program']; ?>"><?php echo $row_program['day_wise_program']; ?></textarea><span class="style_text"><span class="style_text_b" data-wrapper="**" style="font-weight: bold; cursor: pointer;" title="Bold text">B</span><span class="style_text_u" data-wrapper="__" style="cursor: pointer;" title="Underline text"><u>U</u></span></span>
                                                    </td>
                                                    <td class='col-md-1/2 no-pad' style='width:150px;'><input type="text" id="overnight_stay<?php echo $offset; ?>-u" name="overnight_stay" onchange="validate_spaces(this.id);validate_onstay(this.id);" class="form-control mg_bt_10" placeholder="*Overnight Stay" title="Overnight Stay" value="<?php echo $row_program['stay']; ?>" style='width:200px;margin-top: 35px;'></td>
                                                    <td class='col-md-1/2 no-pad' style='width:150px;'><select id="meal_plan<?php echo $offset; ?>-u" title="Meal Plan" name="meal_plan" class="form-control mg_bt_10" style='width: 140px;margin-top: 35px;'>
                                                            <?php if ($row_program['meal_plan'] != '') { ?>
                                                                <option value="<?php echo $row_program['meal_plan']; ?>">
                                                                    <?php echo $row_program['meal_plan']; ?></option>
                                                            <?php } ?>
                                                            <?php get_mealplan_dropdown(); ?>
                                                        </select></td>
                                                    <td class='col-md-1 pad_8'><button type="button" class="btn btn-info btn-iti btn-sm" style="border:none;margin-top: 35px;" title="Add Itinerary" id="itinerary<?php echo $offset; ?>" onclick="add_itinerary('dest_name','special_attaraction<?php echo $offset; ?>-u','day_program<?php echo $offset; ?>-u','overnight_stay<?php echo $offset; ?>-u','Day-<?= $offset ?>')"><i class="fa fa-plus"></i></button>
                                                    </td>
                                                    <td class='col-md-1 pad_8' style="width: 120px;">
                                                        <div style="margin-top: 35px;">
                                                            <label for="day_image_<?php echo $offset; ?>" class="btn btn-sm btn-success" 
                                                                   style="margin-bottom: 5px; padding: 6px 12px; font-size: 12px; cursor: pointer; border-radius: 4px; border: none; background-color: #28a745; color: white; font-weight: 500;">
                                                                Upload Image
                                                            </label>
                                                            <input type="file" id="day_image_<?php echo $offset; ?>" 
                                                                   name="day_image_<?php echo $offset; ?>" accept="image/*" 
                                                                   onchange="previewDayImage(this, '<?php echo $offset; ?>')" 
                                                                   style="display: none;">
                                                        </div>
                                                        <div id="day_image_preview_<?php echo $offset; ?>" style="display: none; margin-top: 5px;">
                                                            <div style="height:100px; max-height: 100px; overflow:hidden; position: relative; width: 100px; border: 2px solid #ddd; border-radius: 8px; background-color: #f8f9fa;">
                                                                <img id="preview_img_<?php echo $offset; ?>" src="" alt="Preview" 
                                                                     style="width:100%; height:100%; object-fit: cover; border-radius: 6px;">
                                                                <button type="button" 
                                                                        onclick="removeDayImage('<?php echo $offset; ?>')" 
                                                                        title="Remove Image" 
                                                                        style="position: absolute; top: 5px; right: 5px; width: 20px; height: 20px; border: none; border-radius: 50%; background-color: #dc3545; color: white; font-size: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                                                                    ×
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <?php
                                                        // Load existing image for this day
                                                        echo "<!-- DEBUG: quotation_id = $quotation_id, package_id = $package_id, offset = $offset -->";
                                                        
                                                        // If quotation_id is not set, try to get it from the form
                                                        if (empty($quotation_id)) {
                                                            $quotation_id = isset($_GET['quotation_id']) ? $_GET['quotation_id'] : '';
                                                        }
                                                        if (empty($package_id)) {
                                                            $package_id = isset($_GET['package_id']) ? $_GET['package_id'] : '';
                                                        }
                                                        
                                                        // If package_id is still empty, get it from database
                                                        if (empty($package_id) && !empty($quotation_id)) {
                                                            $package_query = "SELECT package_id FROM package_tour_quotation_master WHERE quotation_id = '$quotation_id' LIMIT 1";
                                                            $package_result = mysqlQuery($package_query);
                                                            if (mysqli_num_rows($package_result) > 0) {
                                                                $package_row = mysqli_fetch_assoc($package_result);
                                                                $package_id = $package_row['package_id'];
                                                            }
                                                        }
                                                        
                                                        $existing_image_query = "SELECT image_url FROM package_tour_quotation_images WHERE quotation_id = '$quotation_id' AND package_id = '$package_id' AND image_url LIKE '%day_$offset_%'";
                                                        echo "<!-- DEBUG: Query = $existing_image_query -->";
                                                        $existing_image_result = mysqlQuery($existing_image_query);
                                                        $image_count = mysqli_num_rows($existing_image_result);
                                                        echo "<!-- DEBUG: Found $image_count images -->";
                                                        if ($image_count > 0) {
                                                            $existing_image = mysqli_fetch_assoc($existing_image_result);
                                                            $image_url = BASE_URL . $existing_image['image_url'];
                                                            echo "<!-- DEBUG: Image URL = $image_url -->";
                                                        ?>
                                                        <div id="saved_image_<?php echo $offset; ?>" style="margin-top: 5px;">
                                                            <div style="height:100px; max-height: 100px; overflow:hidden; position: relative; width: 100px; border: 2px solid #28a745; border-radius: 8px; background-color: #f8f9fa;">
                                                                <img src="<?php echo $image_url; ?>" alt="Saved Image" 
                                                                     style="width:100%; height:100%; object-fit: cover; border-radius: 6px;">
                                                                <button type="button" 
                                                                        onclick="removeSavedImage('<?php echo $offset; ?>', '<?php echo $existing_image['image_url']; ?>')" 
                                                                        title="Remove Image" 
                                                                        style="position: absolute; top: 5px; right: 5px; width: 20px; height: 20px; border: none; border-radius: 50%; background-color: #dc3545; color: white; font-size: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                                                                    ×
                                                                </button>
                                                            </div>
                                                            <div style="margin-top: 5px; text-align: center;">
                                                                <button type="button" onclick="replaceSavedImage('<?php echo $offset; ?>')" class="btn btn-sm btn-warning" style="padding: 4px 8px; font-size: 11px; border-radius: 4px;">Replace Image</button>
                                                            </div>
                                                        </div>
                                                        <script>
                                                        // Hide upload button when saved image exists
                                                        $(document).ready(function() {
                                                            $('#saved_image_<?php echo $offset; ?>').show();
                                                            $('label[for="day_image_<?php echo $offset; ?>"]').hide();
                                                        });
                                                        </script>
                                                        <?php } ?>
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
                                                    echo '<td class="col-md-1 pad_8"><button type="button" class="btn btn-info btn-iti btn-sm" style="border:none;margin-top: 35px;" title="Add Itinerary" id="itinerary1" onclick="add_itinerary(\'dest_name\',\'special_attaraction1-u\',\'day_program1-u\',\'overnight_stay1-u\',\'Day-1\')"><i class="fa fa-plus"></i></button></td>';
                                                    echo '<td class="col-md-1 pad_8" style="width: 120px;">';
                                                    echo '<div style="margin-top: 35px;">';
                                                    echo '<label for="day_image_1" class="btn btn-sm btn-success" style="margin-bottom: 5px; padding: 6px 12px; font-size: 12px; cursor: pointer; border-radius: 4px; border: none; background-color: #28a745; color: white; font-weight: 500;">Upload Image</label>';
                                                    echo '<input type="file" id="day_image_1" name="day_image_1" accept="image/*" onchange="previewDayImage(this, \'1\')" style="display: none;">';
                                                    echo '</div>';
                                                    echo '<div id="day_image_preview_1" style="display: none; margin-top: 5px;">';
                                                    echo '<div style="height:100px; max-height: 100px; overflow:hidden; position: relative; width: 100px; border: 2px solid #ddd; border-radius: 8px; background-color: #f8f9fa;">';
                                                    echo '<img id="preview_img_1" src="" alt="Preview" style="width:100%; height:100%; object-fit: cover; border-radius: 6px;">';
                                                    echo '<button type="button" onclick="removeDayImage(\'1\')" title="Remove Image" style="position: absolute; top: 5px; right: 5px; width: 20px; height: 20px; border: none; border-radius: 50%; background-color: #dc3545; color: white; font-size: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">×</button>';
                                                    echo '</div>';
                                                    echo '</div>';
                                                    
                                                    // Load existing image for day 1
                                                    echo "<!-- DEBUG: Day 1 - quotation_id = $quotation_id, package_id = $package_id -->";
                                                    
                                                    // If quotation_id is not set, try to get it from the form
                                                    if (empty($quotation_id)) {
                                                        $quotation_id = isset($_GET['quotation_id']) ? $_GET['quotation_id'] : '';
                                                    }
                                                    if (empty($package_id)) {
                                                        $package_id = isset($_GET['package_id']) ? $_GET['package_id'] : '';
                                                    }
                                                    
                                                    // If package_id is still empty, get it from database
                                                    if (empty($package_id) && !empty($quotation_id)) {
                                                        $package_query = "SELECT package_id FROM package_tour_quotation_master WHERE quotation_id = '$quotation_id' LIMIT 1";
                                                        $package_result = mysqlQuery($package_query);
                                                        if (mysqli_num_rows($package_result) > 0) {
                                                            $package_row = mysqli_fetch_assoc($package_result);
                                                            $package_id = $package_row['package_id'];
                                                        }
                                                    }
                                                    
                                                    $existing_image_query = "SELECT image_url FROM package_tour_quotation_images WHERE quotation_id = '$quotation_id' AND package_id = '$package_id' AND image_url LIKE '%day_1_%'";
                                                    echo "<!-- DEBUG: Day 1 Query = $existing_image_query -->";
                                                    $existing_image_result = mysqlQuery($existing_image_query);
                                                    $day1_image_count = mysqli_num_rows($existing_image_result);
                                                    echo "<!-- DEBUG: Day 1 Found $day1_image_count images -->";
                                                    if (mysqli_num_rows($existing_image_result) > 0) {
                                                        $existing_image = mysqli_fetch_assoc($existing_image_result);
                                                        $image_url = BASE_URL . $existing_image['image_url'];
                                                        echo '<div id="saved_image_1" style="margin-top: 5px;">';
                                                        echo '<div style="height:100px; max-height: 100px; overflow:hidden; position: relative; width: 100px; border: 2px solid #28a745; border-radius: 8px; background-color: #f8f9fa;">';
                                                        echo '<img src="' . $image_url . '" alt="Saved Image" style="width:100%; height:100%; object-fit: cover; border-radius: 6px;">';
                                                        echo '<button type="button" onclick="removeSavedImage(\'1\', \'' . $existing_image['image_url'] . '\')" title="Remove Image" style="position: absolute; top: 5px; right: 5px; width: 20px; height: 20px; border: none; border-radius: 50%; background-color: #dc3545; color: white; font-size: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">×</button>';
                                                        echo '</div>';
                                                        echo '</div>';
                                                        echo '<script>';
                                                        echo '$(document).ready(function() {';
                                                        echo '$("#saved_image_1").show();';
                                                        echo '$("label[for=\\"day_image_1\\"]").hide();';
                                                        echo '});';
                                                        echo '</script>';
                                                    }
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
                                            <textarea class="feature_editor form-control" id="inclusions1" name="inclusions1" placeholder="Inclusions" title="Inclusions" rows="4"><?php echo $sq_quotation['inclusions']; ?></textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <h3 class="editor_title">Exclusions</h3>
                                            <textarea class="feature_editor form-control" id="exclusions1" name="exclusions1" placeholder="Exclusions" title="Exclusions" rows="4"><?php echo $sq_quotation['exclusions']; ?></textarea>
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
                    <button class="btn btn-info btn-sm ico_right">Next&nbsp;&nbsp;<i class="fa fa-arrow-right"></i></button>
                </div>
            </div>
</form>
<script>

// Function to get package ID for a specific offset
function getPackageIdForOffset(offset) {
    // Try to find package ID from the current row
    var packageIdInput = $('input[name="package_id_n"]').first();
    var packageId = packageIdInput.val() || '1'; // Default to 1 if not found
    
    console.log("DEBUG: Getting package ID for offset", offset, ":", packageId);
    return packageId;
}

// Day image preview functions for update
function previewDayImage(input, offset) {
    console.log("previewDayImage called with offset:", offset, "file:", input.files[0]);
    
    if (input.files && input.files[0]) {
        var file = input.files[0];
        
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
            $('#preview_img_' + offset).attr('src', e.target.result);
            $('#day_image_preview_' + offset).show();
            
            // Hide the upload button when image is uploaded
            $('label[for="day_image_' + offset + '"]').hide();
            
            // Show the upload button in preview area
            $('#upload_btn_' + offset).show();
            
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
            
            // Update button text to indicate it will be uploaded when quotation is updated
            $('#upload_btn_' + offset).text('Will Upload on Update');
            
            console.log("DEBUG: Stored image for offset " + offset + " in edit mode:", file.name, "Package ID:", packageId);
            console.log("DEBUG: Full stored object:", window.quotationImages[offset]);
        }
        reader.onerror = function() {
            console.error("FileReader error");
            alert('Error reading file');
        }
        reader.readAsDataURL(file);
    }
}

function removeDayImage(offset) {
    $('#day_image_' + offset).val('');
    $('#day_image_preview_' + offset).hide();
    $('#preview_img_' + offset).attr('src', '');
    
    // Hide the upload button in preview area
    $('#upload_btn_' + offset).hide();
    
    // Clear any stored image data
    if (window.quotationImages && window.quotationImages[offset]) {
        delete window.quotationImages[offset];
    }
    
    // Show the upload button again when image is removed
    $('label[for="day_image_' + offset + '"]').show();
    
    console.log("Removed image for offset:", offset);
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
		var text=textarea.value;
		 var content = text.replace(/\*\*(.*?)\*\*/g, '<b>$1</b>');

		// Replace markdown-style underline (__text__) with <u> tags
		content = content.replace(/__(.*?)__/g, '<u>$1</u>');
		textarea.value =content;
		//console.log(content);    
});



    function switch_to_tab1() {

        $('#tab2_head').removeClass('active');
        $('#tab1_head').addClass('active');
        $('.bk_tab').removeClass('active');
        $('#tab1').addClass('active');
        $('html, body').animate({
            scrollTop: $('.bk_tab_head').offset().top
        }, 200);
    }


    $('#frm_tab2_u').validate({
        rules: {},

        submitHandler: function(form) {
            var base_url = $('#base_url').val();
            var checked_programe_arr = new Array();
            var attraction_arr = new Array();
            var program_arr = new Array();
            var stay_arr = new Array();
            var meal_plan_arr = new Array();
            var package_p_id_arr = new Array();
            var day_count_arr = new Array();
            var count = 0;

            var table = document.getElementById("dynamic_table_list_update");
            var rowCount = table.rows.length;
        for (var i = 0; i < rowCount; i++) {
            var row = table.rows[i];
            var checked_programe = row.cells[0].childNodes[0].checked;
            var attraction = row.cells[2].childNodes[0].value;
            var program = row.cells[3].childNodes[0].value;
            var stay = row.cells[4].childNodes[0].value;
            var meal_plan = row.cells[5].childNodes[0].value;
            
            // Debug: Log cell count and package_id1 value
            console.log("Row " + i + " has " + row.cells.length + " cells");
            
            // Try to find the hidden input field with package_id_n
            var package_id1 = '';
            for (var j = 0; j < row.cells.length; j++) {
                if (row.cells[j].querySelector('input[name="package_id_n"]')) {
                    package_id1 = row.cells[j].querySelector('input[name="package_id_n"]').value;
                    console.log("Found package_id_n in cell " + j + " with value: " + package_id1);
                    break;
                }
            }
            
            if (!package_id1) {
                console.log("No package_id_n found, trying cell 8: ", row.cells[8] ? row.cells[8].childNodes[0].value : "Cell 8 not found");
                package_id1 = row.cells[8] ? row.cells[8].childNodes[0].value : '';
            }
                if (checked_programe) {
                    count++;
                    if (program == "") {
                        error_msg_alert('Daywise program is mandatory in row' + (i + 1));
                        return false;
                    }

                    var flag1 = validate_spattration(row.cells[2].childNodes[0].id);
                    var flag2 = validate_dayprogram(row.cells[3].childNodes[0].id);
                    var flag3 = validate_onstay(row.cells[4].childNodes[0].id);
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
            }
            day_count_arr.push(count);
            
            // Debug: Log the arrays being sent
            console.log("package_p_id_arr:", package_p_id_arr);
            console.log("checked_programe_arr:", checked_programe_arr);

            var dest_id = $('#dest_name').val();
            var package_id = $('#img_package_id').val();
            var package_id_arr = [package_id];

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
                    console.log(result.responseText);
                }
            });

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
                    console.log(result.responseText);
                }
            });

            $('#tab2_head').addClass('done');
            $('#tab_daywise_head').addClass('active');
            $('.bk_tab').removeClass('active');
            $('#tab_daywise').addClass('active');
            $('html, body').animate({
                scrollTop: $('.bk_tab_head').offset().top
            }, 200);
    }
});

// Initialize nights filter when tab2 loads for update
$(document).ready(function() {
    var selected_nights = sessionStorage.getItem('selected_nights');
    if (selected_nights) {
        $('#nights_filter').val(selected_nights);
    }
});

// Function to load packages with both destination and nights filter for update
function load_packages_with_filter() {
    var dest_id = $('#dest_name').val();
    var total_nights = $('#nights_filter').val() || sessionStorage.getItem('selected_nights');
    
    if (dest_id) {
        // Update sessionStorage with current nights selection
        if (total_nights) {
            sessionStorage.setItem('selected_nights', total_nights);
        }
        
        // Call the package loading function with nights parameter
        package_dynamic_reflect_with_nights('dest_name', total_nights);
    }
}

// Function to filter packages by nights in tab2 for update
function filter_packages_by_nights() {
    var total_nights = $('#nights_filter').val();
    
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
        total_nights: total_nights
    };

    $.ajax({
        type: 'post',
        url: base_url + 'view/package_booking/quotation/inc/get_packages.php',
        data: ajax_data,
        success: function (result) {
            $('#package_name_div').html(result);
        },
        error: function (result) {
            console.log('Package loading error:', result.responseText);
        }
    });
}
</script>
<?= end_panel(); ?>