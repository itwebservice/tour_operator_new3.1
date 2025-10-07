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
function findImageUrl($image_path, $is_new_quotation = false)
{
    if (empty($image_path) || $image_path === 'NULL') {
        return '';
    }

    $image_path = trim($image_path);

    // Check if path already starts with http
    if (strpos($image_path, 'http') === 0) {
        // URL encode the path to handle spaces and special characters
        $encoded_path = str_replace(' ', '%20', $image_path);
        return $encoded_path;
    }

    // Get project base URL
    $project_base_url = str_replace('/crm/', '/', BASE_URL);
    $project_base_url = rtrim($project_base_url, '/');

    $image_path_clean = ltrim($image_path, '/');

    // For both new and existing quotations, check multiple locations for package images
    if ($is_new_quotation) {
        error_log("QUOTATION: Checking multiple locations for quotation - image: " . $image_path_clean);

        // First, try the original path as stored in the database
        $original_file_path = "../../../" . $image_path_clean;
        if (file_exists($original_file_path)) {
            error_log("QUOTATION: Found image in original location: " . $original_file_path);
            $original_url = $project_base_url . '/' . $image_path_clean;
            return $original_url;
        }

        // Check if it's a relative path and try different base locations
        if (strpos($image_path_clean, 'uploads/') === 0) {
            // Try project root uploads folder
            $project_uploads_path = "../../../" . $image_path_clean;
            if (file_exists($project_uploads_path)) {
                error_log("QUOTATION: Found image in project uploads: " . $project_uploads_path);
                $original_url = $project_base_url . '/' . $image_path_clean;
                return $original_url;
            }
        }

        // Check itinerary_images folder
        $itinerary_images_path = "../../../../../uploads/itinerary_images/" . basename($image_path_clean);
        error_log("QUOTATION: Checking itinerary_images path: " . $itinerary_images_path);
        if (file_exists($itinerary_images_path)) {
            error_log("QUOTATION: Found image in itinerary_images folder (new quotation): " . $itinerary_images_path);
            $itinerary_images_url = $project_base_url . '/uploads/itinerary_images/' . basename($image_path_clean);
            return $itinerary_images_url;
        }
    }

    // Check original path first
    $original_url = $project_base_url . '/' . $image_path_clean;
    $original_file_path = "../../../" . $image_path_clean;

    if (file_exists($original_file_path)) {
        error_log("QUOTATION: Found image in original location: " . $original_file_path);
        return $original_url;
    }

    // Additional check for existing quotations - try different path variations
    $alternative_paths = [
        "../../../../" . $image_path_clean,
        "../../../../../" . $image_path_clean,
        "../../../../../../" . $image_path_clean
    ];

    foreach ($alternative_paths as $alt_path) {
        if (file_exists($alt_path)) {
            error_log("QUOTATION: Found image in alternative path: " . $alt_path);
            // Construct URL based on the found path
            $relative_path = str_replace(['../../../../', '../../../../../', '../../../../../../'], '', $alt_path);
            return $project_base_url . '/' . $relative_path;
        }
    }

    // Try different path variations for package images
    $path_variations = [
        "../../../" . $image_path_clean,
        "../../../../" . $image_path_clean,
        "../../../../../" . $image_path_clean,
        "../../../uploads/" . basename($image_path_clean),
        "../../../../uploads/" . basename($image_path_clean),
        "../../../../../uploads/" . basename($image_path_clean)
    ];

    foreach ($path_variations as $test_path) {
        if (file_exists($test_path)) {
            error_log("QUOTATION: Found image in variation: " . $test_path);
            // Construct appropriate URL based on path
            if (strpos($test_path, 'uploads/') !== false) {
                $url_path = str_replace(['../../../../', '../../../', '../../../../../'], '', $test_path);
                return $project_base_url . '/' . $url_path;
            }
            return $original_url;
        }
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
    .style_text {
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
        /* min-height: 200px; */
        transition: opacity 0.3s ease;
        margin-bottom: 10px;
    }

    .package_content.loading {
        opacity: 0.7;
        pointer-events: none;
    }

    /* Smooth transitions for accordion */
    .accordion_content {
        transition: all 0.3s ease;
        margin-bottom: 15px;
    }

    /* Fix package list spacing */
    .panel-group {
        margin-bottom: 0;
    }

    .panel {
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .panel:last-child {
        margin-bottom: 0;
    }

    /* Fix accordion content spacing */
    .accordion_content.package_content {
        margin-bottom: 10px;
    }

    /* Ensure proper spacing between packages */
    .package_selector+.accordion_content {
        margin-top: 5px;
    }

    /* Fix table spacing issues */
    .table-responsive {
        margin-bottom: 0;
    }

    .table {
        margin-bottom: 0;
    }

    .table>tbody>tr>td {
        padding: 8px;
        vertical-align: middle;
    }

    /* Fix panel body spacing */
    .panel-body {
        padding: 15px;
    }

    /* Remove excessive margins from main_block */
    .main_block {
        margin-bottom: 0;
    }

    /* Fix accordion spacing */
    #accordion .panel {
        margin-bottom: 15px;
    }

    #accordion .panel:last-child {
        margin-bottom: 0;
    }

    /* Fix textarea height issues */
    .day_program {
        height: 80px !important;
        min-height: 80px;
        max-height: 120px;
        resize: vertical;
    }

    /* Ensure consistent row heights */
    .table>tbody>tr {
        height: auto;
        min-height: 100px;
    }

    /* Fix excessive margins in form controls */
    .mg_bt_10 {
        margin-bottom: 10px !important;
    }

    /* Fix table cell padding */
    .table>tbody>tr>td {
        padding: 8px 12px;
        vertical-align: top;
    }

    /* Ensure proper spacing in dynamic rows */
    #dynamic_table_list_p_<?= $row_tours['package_id'] ?>tbody tr {
        margin-bottom: 5px;
    }

    /* Force all textareas to have consistent height */
    textarea.day_program {
        height: 80px !important;
        min-height: 80px !important;
        max-height: 120px !important;
    }

    /* Remove any excessive spacing from table rows */
    table tbody tr {
        height: auto !important;
        min-height: 100px !important;
        max-height: 150px !important;
    }

    /* Fix any remaining height issues */
    .panel-body {
        padding: 10px 15px !important;
    }

    /* Ensure table cells don't have excessive height */
    .table>tbody>tr>td {
        height: auto !important;
        max-height: 150px !important;
        overflow: hidden;
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

    /* ENHANCED Image preview styling - Hidden by default */
    div[id^="day_image_preview_"] {
        display: none !important;
        margin-top: 5px !important;
        position: relative !important;
        z-index: 1000 !important;
        width: 100px !important;
        height: 100px !important;
    }

    /* Force show when image is present */
    div[id^="day_image_preview_"][style*="display: block"],
    div[id^="day_image_preview_"]:not([style*="display: none"]) {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    /* Force preview container visibility */
    .image-zoom-container {
        display: block !important;
        width: 100px !important;
        height: 100px !important;
        position: relative !important;
        border: 2px solid #ddd !important;
        border-radius: 8px !important;
        background-color: #f8f9fa !important;
        overflow: hidden !important;
        z-index: 1001 !important;
    }

    /* Force preview image visibility */
    img[id^="preview_img_"] {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
        border-radius: 6px !important;
        display: block !important;
        position: relative !important;
        z-index: 1002 !important;
    }

    /* Force remove button visibility and styling */
    button[onclick*="removeDayImage"] {
        display: flex !important;
        visibility: visible !important;
        opacity: 1 !important;
        position: absolute !important;
        top: 2px !important;
        right: 2px !important;
        z-index: 1003 !important;
        width: 20px !important;
        height: 20px !important;
        border-radius: 50% !important;
        background-color: rgba(255, 0, 0, 0.8) !important;
        color: white !important;
        border: none !important;
        cursor: pointer !important;
        font-size: 12px !important;
        font-weight: bold !important;
        align-items: center !important;
        justify-content: center !important;
        line-height: 1 !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    button[onclick*="removeDayImage"]:hover {
        background-color: rgba(255, 0, 0, 1) !important;
        transform: scale(1.1) !important;
    }

    /* Hide upload button when image is uploaded */
    label[for^="day_image_"] {
        display: inline-block !important;
    }

    /* Force hide upload button and instruction div when preview is shown - USING UNIQUE IDS */
    div[id^="day_image_preview_"][style*="display: block"] ~ div[id^="upload_btn_container_"],
    div[id^="day_image_preview_"]:not([style*="display: none"]) ~ div[id^="upload_btn_container_"],
    div[id^="day_image_preview_"][style*="display: block"] ~ .image-requirements,
    div[id^="day_image_preview_"][style*="display: block"] + .image-requirements,
    div[id^="day_image_preview_"]:not([style*="display: none"]) ~ .image-requirements,
    div[id^="day_image_preview_"]:not([style*="display: none"]) + .image-requirements {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
    }

    /* Additional aggressive hiding for upload buttons when previews exist - USING UNIQUE IDS */
    div[id^="day_image_preview_"]:not([style*="display: none"]) ~ div[id^="upload_btn_container_"],
    div[id^="day_image_preview_"]:not([style*="display: none"]) + div[id^="upload_btn_container_"],
    div[id^="day_image_preview_"]:not([style*="display: none"]) ~ div div[id^="upload_btn_container_"],
    div[id^="day_image_preview_"]:not([style*="display: none"]) + div div[id^="upload_btn_container_"] {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
    }

    /* Hide upload buttons by class when previews are visible - REMOVED (using unique IDs now) */

    /* NUCLEAR OPTION: Hide upload button containers when previews are visible - USING UNIQUE IDS */
    div[id^="day_image_preview_"]:not([style*="display: none"]) ~ div[id^="upload_btn_container_"],
    div[id^="day_image_preview_"]:not([style*="display: none"]) + div[id^="upload_btn_container_"],
    div[id^="day_image_preview_"]:not([style*="display: none"]) ~ div div[id^="upload_btn_container_"],
    div[id^="day_image_preview_"]:not([style*="display: none"]) + div div[id^="upload_btn_container_"] {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        position: absolute !important;
        left: -9999px !important;
        width: 0 !important;
        height: 0 !important;
        overflow: hidden !important;
    }

    /* Prevent fluctuation by stabilizing initial states */
    div[id^="day_image_preview_"] {
        transition: none !important;
        animation: none !important;
    }

    label[for^="day_image_"] {
        transition: none !important;
        animation: none !important;
    }

    /* REMOVED: This CSS rule was conflicting with JavaScript hiding */

    /* REMOVED: This CSS rule was also conflicting with JavaScript hiding */
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
                                                        value="<?php echo $row_program['attraction']; ?>" style='width:220px;margin-top: 35px;'>
                                                </td>
                                                <td class='col-md-6 pad_8' style="max-width: 594px;overflow: hidden;position: relative;"><textarea id="day_program<?php echo $offset1; ?>-u" name="day_program" class="form-control mg_bt_10 day_program" placeholder="*Day-wise Program" title="Day-wise Program" style="height:80px;" onchange="validate_spaces(this.id);validate_dayprogram(this.id);" rows="3" value="<?php echo $row_program['day_wise_program']; ?>"><?php echo $row_program['day_wise_program']; ?></textarea><span class="style_text"><span class="style_text_b" data-wrapper="**" style="font-weight: bold; cursor: pointer;" title="Bold text">B</span><span class="style_text_u" data-wrapper="__" style="cursor: pointer;" title="Underline text"><u>U</u></span></span>
                                                </td>
                                                <td style="width: 100px;"><input type="text"
                                                        id="overnight_stay<?php echo $offset1; ?>-u" name="overnight_stay"
                                                        onchange="validate_spaces(this.id);validate_onstay(this.id);"
                                                        class="form-control mg_bt_10" placeholder="*Overnight Stay"
                                                        title="Overnight Stay" value="<?php echo $row_program['stay']; ?>"
                                                        style='width:170px;margin-top: 35px;'></td>
                                                <td><select id="meal_plan<?php echo $offset1; ?>-u" title="Meal Plan"
                                                        name="meal_plan" class="form-control mg_bt_10" style='width: 140px;margin-top: 35px;'>
                                                        <option value="">Select Meal Plan</option>
                                                        <?php get_mealplan_dropdown(); ?>
                                                    </select></td>
                                                <td class='col-md-1 pad_8'><button type="button" class="btn btn-info btn-iti btn-sm" style="border:none;margin-top: 35px;" title="Add Itinerary" id="itinerary<?php echo $offset1; ?>" onclick="add_itinerary('dest_name','special_attaraction<?php echo $offset1; ?>-u','day_program<?php echo $offset1; ?>-u','overnight_stay<?php echo $offset1; ?>-u','Day-<?= $offset1 ?>')"><i class="fa fa-plus"></i></button>
                                                </td>
                                              <?php
    $package_id = $row_program['package_id'] ?? 'pkg0'; // or however you have it stored
    $offset_id = $package_id . '_' . $current_offset;
    $image_path = trim($row_program['day_image'] ?? '');
    $has_image = ($image_path !== '' && strtolower($image_path) !== 'null');

    $final_url = '';
    if ($has_image) {
        $final_url = findImageUrl($image_path, true);
        if (empty($final_url)) {
            $has_image = false;
        }
    }
?>
<td class="col-md-1 pad_8" style="width: 120px;">

    <!-- Upload button -->
           <div id="upload_btn_container_<?php echo $offset_id; ?>"
                style="margin-top: 35px; display: <?php echo $has_image ? 'none' : 'flex'; ?>; align-items: center; justify-content: center; height: 100%;">
        <label for="day_image_<?php echo $offset_id; ?>"
               class="btn btn-sm btn-success"
               style="margin-bottom: 5px; padding: 6px 12px; font-size: 12px; border-radius: 4px;">
            <i class="fa fa-image"></i> Upload Image
        </label>

        <input type="file" 
               id="day_image_<?php echo $offset_id; ?>"
               name="day_image_<?php echo $offset_id; ?>"
               accept="image/*"
               onchange="
               console.log('File selected for <?php echo $offset_id; ?>');
               var uniqueId = '<?php echo $offset_id; ?>';
               var file = this.files[0];
               if (file) {
                   var reader = new FileReader();
                   reader.onload = function(e) {
                       // Show preview
                       $('#preview_img_' + uniqueId).attr('src', e.target.result).show();
                       $('#day_image_preview_' + uniqueId).show().css('display', 'block !important');
                       
                       // Hide upload button - SIMPLE DIRECT APPROACH
                       var element = document.getElementById('upload_btn_container_' + uniqueId);
                       if (element) {
                           element.style.display = 'none';
                           element.style.visibility = 'hidden';
                           element.style.opacity = '0';
                           console.log('Upload button hidden for ' + uniqueId);
                       }
                   };
                   reader.readAsDataURL(file);
               }
               "
               style="display:none;">
    </div>

    <!-- Image preview -->
    <div id="day_image_preview_<?php echo $offset_id; ?>" 
         style="margin-top:5px; display: <?php echo $has_image ? 'block' : 'none'; ?>;">
        <div class="image-zoom-container" 
             style="height:100px; width:100px; overflow:hidden; border:2px solid #ddd; border-radius:8px; position:relative;">
            <img id="preview_img_<?php echo $offset_id; ?>" 
                 src="<?php echo $has_image ? htmlspecialchars($final_url) : ''; ?>"
                 alt="Preview"
                 style="width:100%; height:100%; object-fit:cover; border-radius:6px; <?php echo $has_image ? '' : 'display:none;'; ?>"
                 onerror="
                    console.log('Image failed for <?php echo $offset_id; ?>');
                    this.style.display='none';
                    var previewDiv = document.getElementById('day_image_preview_<?php echo $offset_id; ?>');
                    var uploadContainer = document.getElementById('upload_btn_container_<?php echo $offset_id; ?>');
                    if (previewDiv) previewDiv.style.display='none';
                    if (uploadContainer) uploadContainer.style.display='flex';
                 ">
            <button type="button"
                    onclick="removeDayImage('<?php echo $package_id; ?>', '<?php echo $current_offset; ?>')"
                    title="Remove Image"
                    style="position:absolute; top:5px; right:5px; background-color:#dc3545; color:#fff; border:none; border-radius:50%; width:20px; height:20px; display:<?php echo $has_image ? 'flex' : 'none'; ?>; align-items:center; justify-content:center;">
                ×
            </button>
        </div>
    </div>

    <input type="hidden" 
           id="existing_image_path_<?php echo $offset_id; ?>" 
           name="existing_image_path_<?php echo $offset_id; ?>" 
           value="<?php echo htmlspecialchars($image_path); ?>">
</td>

                                                <td class="hidden"><input type="hidden" name="package_id_n" value=""></td>
                                            </tr>
                                            <?php
                                        } else {
                                            // Show existing program entries
                                            while ($row_program = mysqli_fetch_assoc($sq_program)) {
                                                $offset1++;
                                                $current_offset = $offset1; // Use consistent offset for this row
                                                $package_id = $row_tours['package_id'] ?? 'pkg0';
                                                $offset_id = $package_id . '_' . $current_offset;
                                                $image_path = trim($row_program['day_image'] ?? '');
                                                $has_image = ($image_path !== '' && strtolower($image_path) !== 'null');
                                                
                                                $final_url = '';
                                                if ($has_image) {
                                                    $final_url = findImageUrl($image_path, true);
                                                    if (empty($final_url)) {
                                                        $has_image = false;
                                                    }
                                                }
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
                                                            value="<?php echo $row_program['attraction']; ?>" style='width:220px;margin-top: 35px;'>
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
                                                    <td class='col-md-6 pad_8' style="max-width: 594px;overflow: hidden;position: relative;"><textarea id="day_program<?php echo $current_offset; ?>-u" name="day_program" class="form-control mg_bt_10 day_program" placeholder="*Day-wise Program" title="Day-wise Program" style="height:80px;" onchange="validate_spaces(this.id);validate_dayprogram(this.id);" rows="3" value="<?php echo $row_program['day_wise_program']; ?>"><?php echo $row_program['day_wise_program']; ?></textarea><span class="style_text"><span class="style_text_b" data-wrapper="**" style="font-weight: bold; cursor: pointer;" title="Bold text">B</span><span class="style_text_u" data-wrapper="__" style="cursor: pointer;" title="Underline text"><u>U</u></span></span>
                                                    </td>
                                                    <td style="width: 100px;"><input type="text"
                                                            id="overnight_stay<?php echo $current_offset; ?>-u" name="overnight_stay"
                                                            onchange="validate_spaces(this.id);validate_onstay(this.id);"
                                                            class="form-control mg_bt_10" placeholder="*Overnight Stay"
                                                            title="Overnight Stay" value="<?php echo $row_program['stay']; ?>"
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
                                                         <!-- Upload button container -->



                                                            <div id="upload_btn_container_<?php echo $offset_id; ?>" 
                                                                 style="margin-top: 35px; display: <?php echo $has_image ? 'none' : 'flex'; ?>; align-items: center; justify-content: center; height: 100%;">
                                                            <label for="day_image_<?php echo $offset_id; ?>"
                                                                   class="btn btn-sm btn-success"
                                                                   style="margin-bottom: 5px; font-size: 12px; cursor: pointer; border-radius: 4px; border: none; background-color: #28a745; color: white; font-weight: 500;">
                                                                <i class="fa fa-image"></i> Upload Image
                                                            </label>
                                                            
                                                            <input type="file" 
                                                                   id="day_image_<?php echo $offset_id; ?>"
                                                                   name="day_image_<?php echo $offset_id; ?>"
                                                                   accept="image/*"
                                                                   onchange="
                                                                   console.log('File selected for <?php echo $offset_id; ?>');
                                                                   var uniqueId = '<?php echo $offset_id; ?>';
                                                                   var file = this.files[0];
                                                                   if (file) {
                                                                       var reader = new FileReader();
                                                                       reader.onload = function(e) {
                                                                           // Show preview
                                                                           $('#preview_img_' + uniqueId).attr('src', e.target.result).show();
                                                                           $('#day_image_preview_' + uniqueId).show().css('display', 'block !important');
                                                                           
                                                                           // Hide upload button - SIMPLE DIRECT APPROACH
                                                                           var element = document.getElementById('upload_btn_container_' + uniqueId);
                                                                           if (element) {
                                                                               element.style.display = 'none';
                                                                               element.style.visibility = 'hidden';
                                                                               element.style.opacity = '0';
                                                                               console.log('Upload button hidden for ' + uniqueId);
                                                                           }
                                                                       };
                                                                       reader.readAsDataURL(file);
                                                                   }
                                                                   "
                                                                   style="display: none;">
                                                        </div>
                                                        <!-- Image preview container -->
                                                        <div id="day_image_preview_<?php echo $offset_id; ?>" 
                                                             style="margin-top:5px; display: <?php echo $has_image ? 'block' : 'none'; ?>;">
                                                            <div class="image-zoom-container" 
                                                                 style="height:100px; width:100px; overflow:hidden; border:2px solid #ddd; border-radius:8px; position:relative;">
                                                                <img id="preview_img_<?php echo $offset_id; ?>" 
                                                                     src="<?php echo $has_image ? htmlspecialchars($final_url) : ''; ?>"
                                                                     alt="Preview"
                                                                     style="width:100%; height:100%; object-fit:cover; border-radius:6px; <?php echo $has_image ? '' : 'display:none;'; ?>"
                                                                     onerror="
                                                                        console.log('Image failed for <?php echo $offset_id; ?>');
                                                                        this.style.display='none';
                                                                        var previewDiv = document.getElementById('day_image_preview_<?php echo $offset_id; ?>');
                                                                        var uploadContainer = document.getElementById('upload_btn_container_<?php echo $offset_id; ?>');
                                                                        if (previewDiv) previewDiv.style.display='none';
                                                                        if (uploadContainer) uploadContainer.style.display='flex';
                                                                     ">
                                                                <button type="button"
                                                                        onclick="removeDayImage('<?php echo $package_id; ?>', '<?php echo $current_offset; ?>')"
                                                                        title="Remove Image"
                                                                        style="position:absolute; top:5px; right:5px; background-color:#dc3545; color:#fff; border:none; border-radius:50%; width:20px; height:20px; display:<?php echo $has_image ? 'flex' : 'none'; ?>; align-items:center; justify-content:center;">
                                                                    ×
                                                                </button>
                                                            </div>
                                                        </div>
                                                        
                                                        <input type="hidden" 
                                                               id="existing_image_path_<?php echo $offset_id; ?>" 
                                                               name="existing_image_path_<?php echo $offset_id; ?>" 
                                                               value="<?php echo htmlspecialchars($image_path); ?>">
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
    // function previewDayImage(input, offset) {
    //     console.log("previewDayImage called with offset:", offset);

    //     if (input.files && input.files[0]) {
    //         var file = input.files[0];

    //         // Validate file type
    //         var allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    //         var fileType = file.type.toLowerCase();

    //         if (!allowedTypes.includes(fileType)) {
    //             alert('Please select a valid image file (JPEG, PNG, GIF, or WebP)');
    //             input.value = '';
    //             return;
    //         }

    //         // Validate file size (max 5MB)
    //         var maxSize = 5 * 1024 * 1024; // 5MB
    //         if (file.size > maxSize) {
    //             alert('File size too large. Maximum size is 5MB.');
    //             input.value = '';
    //             return;
    //         }

    //         var reader = new FileReader();
    //         reader.onload = function(e) {
    //             console.log("QUOTATION: Showing preview for offset:", offset);

    //             // Set image source
    //             $('#preview_img_' + offset).attr('src', e.target.result);

    //             // Simple show/hide logic
    //             $('#day_image_preview_' + offset).show();
    //             $('label[for="day_image_' + offset + '"]').hide();

    //             // Hide the upload button container when image is uploaded
    //             $('label[for="day_image_' + offset + '"]').parent().hide();
    //             $('label[for="day_image_' + offset + '"]').parent().css('display', 'none !important');
    //             $('label[for="day_image_' + offset + '"]').parent().css('visibility', 'hidden !important');
    //             $('label[for="day_image_' + offset + '"]').parent().css('opacity', '0 !important');

    //             // Hide the instruction div
    //             $('.image-requirements').hide();
    //             $('.image-requirements').css('display', 'none !important');
    //             $('.image-requirements').css('visibility', 'hidden !important');
    //             $('.image-requirements').css('opacity', '0 !important');

    //             // Hide the requirements tooltip
    //             $('.image-requirements-tooltip').hide();
    //             $('.image-requirements-tooltip').css('display', 'none !important');
    //             $('.image-requirements-tooltip').css('visibility', 'hidden !important');
    //             $('.image-requirements-tooltip').css('opacity', '0 !important');

    //             console.log("QUOTATION: Hidden upload button for offset:", offset);
    //             console.log("QUOTATION: Upload button visible after hide:", $('label[for="day_image_' + offset + '"]').is(':visible'));

    //             // Additional aggressive hiding for dynamic rows
    //             setTimeout(function() {
    //                 $('label[for="day_image_' + offset + '"]').hide();
    //                 $('label[for="day_image_' + offset + '"]').css('display', 'none !important');
    //                 $('label[for="day_image_' + offset + '"]').css('visibility', 'hidden !important');
    //                 $('label[for="day_image_' + offset + '"]').css('opacity', '0 !important');
    //                 $('label[for="day_image_' + offset + '"]').attr('style', 'display: none !important; visibility: hidden !important; opacity: 0 !important;');

    //                 console.log("QUOTATION: Aggressive hide applied for offset:", offset);
    //                 console.log("QUOTATION: Upload button visible after aggressive hide:", $('label[for="day_image_' + offset + '"]').is(':visible'));
    //             }, 50);

    //             console.log("QUOTATION: Preview displayed successfully for offset:", offset);

    //             // Store image data for later upload
    //             if (!window.quotationImages) {
    //                 window.quotationImages = {};
    //             }

    //             // Get package ID for this row
    //             var packageId = getPackageIdForOffset(offset);

    //             // Store image by offset for later upload (when quotation is saved)
    //             window.quotationImages[offset] = {
    //                 file: file,
    //                 offset: offset,
    //                 package_id: packageId,
    //                 day_number: offset,
    //                 preview_url: e.target.result,
    //                 uploaded: false
    //             };

    //             console.log("DEBUG: Stored image for offset " + offset + ":", file.name, "Package ID:", packageId);
    //             console.log("DEBUG: Full stored object:", window.quotationImages[offset]);
    //             console.log("DEBUG: Total stored images:", Object.keys(window.quotationImages).length);
    //         }
    //         reader.onerror = function() {
    //             console.error("FileReader error");
    //             alert('Error reading file');
    //         }
    //         reader.readAsDataURL(file);
    //     } else {
    //         console.log("No file selected");
    //     }
    // }

    // function removeDayImage(offset) {

    //     // Clear file input
    //     $('#day_image_' + offset).val('');

    //     // Hide preview and clear image
    //     $('#day_image_preview_' + offset).hide();
    //     $('#preview_img_' + offset).attr('src', '').hide();

    //     // Show upload button again
    //     var uploadLabel = $('label[for="day_image_' + offset + '"]');
    //     if (uploadLabel.length) {
    //         uploadLabel.show().css({
    //             'display': 'inline-block',
    //             'cursor': 'pointer',
    //             'margin-top': '5px'
    //         });
    //     } else {
    //         // If label somehow got removed, recreate it
    //         var input = $('#day_image_' + offset);
    //         if (input.length === 0) {
    //             // Recreate file input if needed
    //             var newFileInput = $('<input type="file" id="day_image_' + offset + '" name="day_image_' + offset + '" accept="image/*" onchange="previewDayImage(this, \'' + offset + '\')" style="display:none;">');
    //             $('#day_image_preview_' + offset).after(newFileInput);
    //             input = newFileInput;
    //         }

    //         var newLabel = $('<label for="day_image_' + offset + '" class="btn btn-sm btn-success" style="margin-top:5px;"><i class="fa fa-image"></i> Upload Image3</label>');
    //         input.after(newLabel);
    //     }

    //     // Clear stored image data if using global object
    //     if (window.itineraryImages && window.itineraryImages[offset]) {
    //         delete window.itineraryImages[offset];
    //     }

    //     console.log("QUOTATION: Image removed successfully and upload button visible for offset:", offset);
    // }


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

        // Force show existing image previews after page load
        setTimeout(function() {
            console.log('QUOTATION: Checking for existing image previews...');
            $('[id^="day_image_preview_"]').each(function() {
                var previewDiv = $(this);
                var img = previewDiv.find('img[id^="preview_img_"]');
                if (img.length > 0 && img.attr('src') && img.attr('src') !== '') {
                    console.log('QUOTATION: Found image with src:', img.attr('src'));
                    previewDiv.css({
                        'display': 'block !important',
                        'visibility': 'visible !important',
                        'opacity': '1 !important'
                    });
                    previewDiv.find('button[onclick*="removeDayImage"]').css({
                        'display': 'flex !important',
                        'visibility': 'visible !important',
                        'opacity': '1 !important'
                    });

                    // Get the offset from the preview div ID
                    var offset = previewDiv.attr('id').replace('day_image_preview_', '');
                    console.log('QUOTATION: Hiding upload button for offset:', offset);

                    // Hide the specific upload button for this offset - Multiple approaches
                    var uploadButton = $('label[for="day_image_' + offset + '"]');
                    var uploadButtonParent = uploadButton.parent();

                    // Method 1: Direct button hiding
                    uploadButton.hide();
                    uploadButton.css({
                        'display': 'none !important',
                        'visibility': 'hidden !important',
                        'opacity': '0 !important'
                    });

                    // Method 2: Hide parent container
                    uploadButtonParent.hide();
                    uploadButtonParent.css({
                        'display': 'none !important',
                        'visibility': 'hidden !important',
                        'opacity': '0 !important'
                    });

                    // Method 3: Target by class
                    $('.upload-btn-' + offset).hide();
                    $('.upload-btn-' + offset).css({
                        'display': 'none !important',
                        'visibility': 'hidden !important',
                        'opacity': '0 !important'
                    });

                    console.log('QUOTATION: Upload button elements found:', uploadButton.length, 'Parent:', uploadButtonParent.length, 'Class:', $('.upload-btn-' + offset).length);

                    // Hide instruction divs
                    $('.image-requirements').css({
                        'display': 'none !important',
                        'visibility': 'hidden !important',
                        'opacity': '0 !important'
                    });
                    console.log('QUOTATION: Forced preview visibility for:', previewDiv.attr('id'));
                }
            });

            // Special handling for first row (offset 1)
            var firstRowPreview = $('#day_image_preview_1');
            var firstRowImg = $('#preview_img_1');
            if (firstRowImg.length > 0 && firstRowImg.attr('src') && firstRowImg.attr('src') !== '') {
                console.log('QUOTATION: Special handling for first row with image:', firstRowImg.attr('src'));
                firstRowPreview.css({
                    'display': 'block !important',
                    'visibility': 'visible !important',
                    'opacity': '1 !important'
                });
                firstRowPreview.show();

                // Hide first row upload button
                $('label[for="day_image_1"]').css({
                    'display': 'none !important',
                    'visibility': 'hidden !important',
                    'opacity': '0 !important'
                });

                // Show first row remove button
                firstRowPreview.find('button[onclick*="removeDayImage"]').css({
                    'display': 'flex !important',
                    'visibility': 'visible !important',
                    'opacity': '1 !important'
                });

                console.log('QUOTATION: First row preview forced to show');
            } else {
                console.log('QUOTATION: First row has no image or empty src - showing upload button');
                // Show upload button for first row when no image
                $('label[for="day_image_1"]').css({
                    'display': 'block !important',
                    'visibility': 'visible !important',
                    'opacity': '1 !important'
                });
                $('label[for="day_image_1"]').show();

                // Completely hide preview div for first row when no image
                firstRowPreview.css({
                    'display': 'none !important',
                    'visibility': 'hidden !important',
                    'opacity': '0 !important',
                    'position': 'absolute !important',
                    'left': '-9999px !important',
                    'width': '0 !important',
                    'height': '0 !important',
                    'overflow': 'hidden !important'
                });
                firstRowPreview.hide();

                // Hide remove button
                firstRowPreview.find('button[onclick*="removeDayImage"]').css({
                    'display': 'none !important',
                    'visibility': 'hidden !important',
                    'opacity': '0 !important'
                });
            }
        }, 1000);

        // Single final check to prevent fluctuation
        setTimeout(function() {
            console.log('QUOTATION: Final state verification...');
            $('[id^="day_image_preview_"]').each(function() {
                var previewDiv = $(this);
                var offset = previewDiv.attr('id').replace('day_image_preview_', '');
                var img = previewDiv.find('img');

                if (img.attr('src') && img.attr('src') !== '') {
                    // Has image - hide upload button
                    $('label[for="day_image_' + offset + '"]').css('display', 'none !important');
                } else {
                    // No image - show upload button, hide preview
                    $('label[for="day_image_' + offset + '"]').css('display', 'block !important');
                    previewDiv.css('display', 'none !important');
                }
            });
        }, 2000);

        // Immediate fix on page load
        setTimeout(function() {
            console.log('QUOTATION: Immediate fix on page load...');
            $('[id^="day_image_preview_"]').each(function() {
                var previewDiv = $(this);
                var offset = previewDiv.attr('id').replace('day_image_preview_', '');
                var img = previewDiv.find('img');
                var uploadButton = $('label[for="day_image_' + offset + '"]');
                var uploadButtonParent = uploadButton.parent();

                // Check if image actually exists and has valid src
                var hasImage = img.attr('src') && img.attr('src') !== '' && img.attr('src') !== 'undefined';
                console.log('QUOTATION: Row', offset, 'hasImage:', hasImage, 'src:', img.attr('src'));

                if (hasImage) {
                    // Has image - show preview, hide upload button
                    console.log('QUOTATION: Row', offset, '- showing preview, hiding button');
                    previewDiv.show();
                    previewDiv.css({
                        'display': 'block !important',
                        'visibility': 'visible !important',
                        'opacity': '1 !important'
                    });

                    // Aggressively hide upload button
                    uploadButton.hide();
                    uploadButton.css({
                        'display': 'none !important',
                        'visibility': 'hidden !important',
                        'opacity': '0 !important',
                        'position': 'absolute !important',
                        'left': '-9999px !important'
                    });

                    // Aggressively hide upload button parent
                    uploadButtonParent.css({
                        'display': 'none !important',
                        'visibility': 'hidden !important',
                        'opacity': '0 !important',
                        'position': 'absolute !important',
                        'left': '-9999px !important'
                    });
                } else {
                    // No image - show upload button, hide preview
                    console.log('QUOTATION: Row', offset, '- showing button, hiding preview');
                    previewDiv.hide();
                    previewDiv.css({
                        'display': 'none !important',
                        'visibility': 'hidden !important',
                        'opacity': '0 !important',
                        'position': 'absolute !important',
                        'left': '-9999px !important'
                    });

                    // Show upload button
                    uploadButtonParent.removeAttr('style');
                    uploadButton.removeAttr('style');

                    uploadButton.show();
                    uploadButton.css({
                        'display': 'block !important',
                        'visibility': 'visible !important',
                        'opacity': '1 !important',
                        'position': 'static !important',
                        'left': 'auto !important'
                    });

                    uploadButtonParent.css({
                        'display': 'flex !important',
                        'visibility': 'visible !important',
                        'opacity': '1 !important',
                        'margin-top': '35px',
                        'align-items': 'center',
                        'justify-content': 'center',
                        'height': '100%',
                        'position': 'static !important',
                        'left': 'auto !important'
                    });
                }
            });
        }, 100);

        // Nuclear option - completely remove upload buttons when images exist
        setTimeout(function() {
            console.log('QUOTATION: Nuclear option - removing upload buttons for rows with images...');
            $('[id^="day_image_preview_"]').each(function() {
                var previewDiv = $(this);
                var offset = previewDiv.attr('id').replace('day_image_preview_', '');
                var img = previewDiv.find('img');
                var uploadButton = $('label[for="day_image_' + offset + '"]');
                var uploadButtonParent = uploadButton.parent();

                // Check if image actually exists and has valid src
                var hasImage = img.attr('src') && img.attr('src') !== '' && img.attr('src') !== 'undefined';

                if (hasImage) {
                    console.log('QUOTATION: Nuclear option for row', offset, '- removing upload button completely');
                    // Completely remove the upload button and its parent
                    uploadButtonParent.remove();
                }
            });
        }, 3000); // Run after 3 seconds

        // Debug function to check first row specifically
        window.debugFirstRow = function() {
            console.log('QUOTATION: Debugging first row...');
            var firstRowPreview = $('#day_image_preview_1');
            var firstRowImg = $('#preview_img_1');
            var firstRowUpload = $('label[for="day_image_1"]');

            console.log('QUOTATION: First row preview div:', firstRowPreview.length, firstRowPreview.is(':visible'));
            console.log('QUOTATION: First row image src:', firstRowImg.attr('src'));
            console.log('QUOTATION: First row upload button:', firstRowUpload.length, firstRowUpload.is(':visible'));
            console.log('QUOTATION: First row preview div style:', firstRowPreview.attr('style'));
            console.log('QUOTATION: First row image onload/onerror:', firstRowImg.attr('onload'), firstRowImg.attr('onerror'));
        };

        // Comprehensive debug function for all rows
        window.debugAllRows = function() {
            console.log('QUOTATION: === COMPREHENSIVE DEBUG ===');
            $('[id^="day_image_preview_"]').each(function() {
                var previewDiv = $(this);
                var offset = previewDiv.attr('id').replace('day_image_preview_', '');
                var img = previewDiv.find('img');
                var uploadBtn = $('label[for="day_image_' + offset + '"]');

                console.log('QUOTATION: Row ' + offset + ' - Preview visible:', previewDiv.is(':visible'));
                console.log('QUOTATION: Row ' + offset + ' - Image src:', img.attr('src'));
                console.log('QUOTATION: Row ' + offset + ' - Upload button visible:', uploadBtn.is(':visible'));
                console.log('QUOTATION: Row ' + offset + ' - Preview style:', previewDiv.attr('style'));
                console.log('QUOTATION: Row ' + offset + ' - Upload button style:', uploadBtn.attr('style'));

                // Test image loading
                if (img.attr('src') && img.attr('src') !== '') {
                    var testImg = new Image();
                    testImg.onload = function() {
                        console.log('QUOTATION: Row ' + offset + ' - Image loads successfully');
                    };
                    testImg.onerror = function() {
                        console.log('QUOTATION: Row ' + offset + ' - Image failed to load');
                    };
                    testImg.src = img.attr('src');
                }
            });
        };

        // Force show all images function
        window.forceShowAllImages = function() {
            console.log('QUOTATION: Force showing all images...');
            $('[id^="day_image_preview_"]').each(function() {
                var previewDiv = $(this);
                var offset = previewDiv.attr('id').replace('day_image_preview_', '');
                var img = previewDiv.find('img');

                if (img.attr('src') && img.attr('src') !== '') {
                    console.log('QUOTATION: Force showing image for offset:', offset);

                    // Force show preview
                    previewDiv.css({
                        'display': 'block !important',
                        'visibility': 'visible !important',
                        'opacity': '1 !important',
                        'position': 'static !important',
                        'left': 'auto !important',
                        'width': '100px !important',
                        'height': '100px !important',
                        'overflow': 'visible !important'
                    });
                    previewDiv.show();

                    // Force hide upload button
                    $('label[for="day_image_' + offset + '"]').css({
                        'display': 'none !important',
                        'visibility': 'hidden !important',
                        'opacity': '0 !important'
                    });

                    // Force show remove button
                    previewDiv.find('button[onclick*="removeDayImage"]').css({
                        'display': 'flex !important',
                        'visibility': 'visible !important',
                        'opacity': '1 !important'
                    });
                } else {
                    console.log('QUOTATION: No image for offset:', offset, '- showing upload button');

                    // Force show upload button
                    $('label[for="day_image_' + offset + '"]').css({
                        'display': 'block !important',
                        'visibility': 'visible !important',
                        'opacity': '1 !important'
                    });

                    // Force hide preview
                    previewDiv.css({
                        'display': 'none !important',
                        'visibility': 'hidden !important',
                        'opacity': '0 !important'
                    });
                }
            });
        };

        // Run debug after page load
        setTimeout(function() {
            window.debugFirstRow();
            window.debugAllRows();
            window.forceShowAllImages();

            // Force check first row specifically
            var firstRowImg = $('#preview_img_1');
            if (firstRowImg.length > 0 && firstRowImg.attr('src') && firstRowImg.attr('src') !== '') {
                console.log('QUOTATION: First row has image, forcing preview to show');

                // Test if image actually loads
                var testImg = new Image();
                testImg.onload = function() {
                    console.log('QUOTATION: First row image confirmed to load');
                    var firstRowPreview = $('#day_image_preview_1');
                    firstRowPreview.css({
                        'display': 'block !important',
                        'visibility': 'visible !important',
                        'opacity': '1 !important'
                    });
                    firstRowPreview.show();

                    // Hide upload button
                    $('label[for="day_image_1"]').css({
                        'display': 'none !important',
                        'visibility': 'hidden !important',
                        'opacity': '0 !important'
                    });

                    // Show remove button
                    firstRowPreview.find('button[onclick*="removeDayImage"]').css({
                        'display': 'flex !important',
                        'visibility': 'visible !important',
                        'opacity': '1 !important'
                    });
                };
                testImg.onerror = function() {
                    console.log('QUOTATION: First row image failed to load, showing upload button');
                    // Image failed to load, show upload button
                    $('label[for="day_image_1"]').css({
                        'display': 'block !important',
                        'visibility': 'visible !important',
                        'opacity': '1 !important'
                    });
                    $('#day_image_preview_1').css({
                        'display': 'none !important',
                        'visibility': 'hidden !important',
                        'opacity': '0 !important'
                    });
                };
                testImg.src = firstRowImg.attr('src');
            }
        }, 2000);

        // Add global test functions for debugging
        window.testImageSystem = function() {
            console.log('=== TESTING IMAGE SYSTEM ===');
            console.log('Available functions:');
            console.log('- debugFirstRow()');
            console.log('- debugAllRows()');
            console.log('- forceShowAllImages()');
            console.log('- testImageSystem()');

            // Run all debug functions
            window.debugFirstRow();
            window.debugAllRows();
            window.forceShowAllImages();
        };

        // Test remove function
        window.testRemoveImage = function(offset) {
            console.log('QUOTATION: Testing remove image for offset:', offset);
            removeDayImage(offset);
        };

        // Debug function to check parent div styles
        window.debugParentDiv = function(offset) {
            var uploadButton = $('label[for="day_image_' + offset + '"]');
            var uploadButtonParent = uploadButton.parent();
            console.log('QUOTATION: Parent div for offset', offset, ':');
            console.log('- Parent element:', uploadButtonParent[0]);
            console.log('- Parent style:', uploadButtonParent.attr('style'));
            console.log('- Parent display:', uploadButtonParent.css('display'));
            console.log('- Parent visibility:', uploadButtonParent.css('visibility'));
            console.log('- Upload button visible:', uploadButton.is(':visible'));
        };

        // Force remove display:none function
        window.forceRemoveDisplayNone = function(offset) {
            console.log('QUOTATION: Force removing display:none for offset:', offset);
            var uploadButton = $('label[for="day_image_' + offset + '"]');
            var uploadButtonParent = uploadButton.parent();

            // Get current style
            var currentStyle = uploadButtonParent.attr('style') || '';
            console.log('QUOTATION: Current parent style:', currentStyle);

            // Remove display:none completely
            var newStyle = currentStyle.replace(/display\s*:\s*none[^;]*;?/gi, '');
            newStyle = newStyle.replace(/;\s*;/g, ';'); // Clean up double semicolons
            newStyle = newStyle.replace(/^;|;$/g, ''); // Remove leading/trailing semicolons

            console.log('QUOTATION: New parent style:', newStyle);

            // Set new style
            uploadButtonParent.attr('style', newStyle);

            // Force show
            uploadButtonParent.css('display', 'flex !important');
            uploadButton.show();

            console.log('QUOTATION: Final parent style:', uploadButtonParent.attr('style'));
            console.log('QUOTATION: Upload button visible:', uploadButton.is(':visible'));
        };

        // Fix all upload buttons function with proper logic
        window.fixAllUploadButtons = function() {
            console.log('QUOTATION: Fixing all upload buttons with proper logic...');
            $('[id^="day_image_preview_"]').each(function() {
                var previewDiv = $(this);
                var offset = previewDiv.attr('id').replace('day_image_preview_', '');
                var img = previewDiv.find('img');
                var uploadButton = $('label[for="day_image_' + offset + '"]');
                var uploadButtonParent = uploadButton.parent();

                // Check if image actually exists and has valid src
                var hasImage = img.attr('src') && img.attr('src') !== '' && img.attr('src') !== 'undefined';

                if (hasImage) {
                    // Has image - show preview, hide upload button
                    console.log('QUOTATION: Row', offset, 'has image - showing preview, hiding button');
                    previewDiv.show();
                    previewDiv.css({
                        'display': 'block !important',
                        'visibility': 'visible !important',
                        'opacity': '1 !important'
                    });

                    uploadButton.hide();
                    uploadButton.css({
                        'display': 'none !important',
                        'visibility': 'hidden !important',
                        'opacity': '0 !important'
                    });

                    uploadButtonParent.css({
                        'display': 'none !important',
                        'visibility': 'hidden !important',
                        'opacity': '0 !important'
                    });
                } else {
                    // No image - show upload button, hide preview
                    console.log('QUOTATION: Row', offset, 'no image - showing button, hiding preview');
                    previewDiv.hide();
                    previewDiv.css({
                        'display': 'none !important',
                        'visibility': 'hidden !important',
                        'opacity': '0 !important'
                    });

                    uploadButtonParent.removeAttr('style');
                    uploadButton.removeAttr('style');

                    uploadButton.show();
                    uploadButton.css({
                        'display': 'block !important',
                        'visibility': 'visible !important',
                        'opacity': '1 !important'
                    });

                    uploadButtonParent.css({
                        'display': 'flex !important',
                        'visibility': 'visible !important',
                        'opacity': '1 !important',
                        'margin-top': '35px',
                        'align-items': 'center',
                        'justify-content': 'center',
                        'height': '100%'
                    });
                }
            });
            console.log('QUOTATION: All rows fixed with proper logic!');
        };

        // Quick fix function for immediate use
        window.quickFix = function() {
            console.log('QUOTATION: Quick fix - hiding all upload buttons when images exist...');
            $('[id^="day_image_preview_"]').each(function() {
                var previewDiv = $(this);
                var offset = previewDiv.attr('id').replace('day_image_preview_', '');
                var img = previewDiv.find('img');
                var uploadButton = $('label[for="day_image_' + offset + '"]');
                var uploadButtonParent = uploadButton.parent();

                if (img.attr('src') && img.attr('src') !== '') {
                    // Has image - hide upload button completely
                    uploadButton.hide();
                    uploadButton.css({
                        'display': 'none !important',
                        'visibility': 'hidden !important',
                        'opacity': '0 !important',
                        'position': 'absolute !important',
                        'left': '-9999px !important'
                    });
                    uploadButtonParent.css({
                        'display': 'none !important',
                        'visibility': 'hidden !important',
                        'opacity': '0 !important',
                        'position': 'absolute !important',
                        'left': '-9999px !important'
                    });
                    console.log('QUOTATION: Hidden upload button for row', offset);
                }
            });
        };

        // Make functions globally available
        window.debugFirstRow = window.debugFirstRow;
        window.debugAllRows = window.debugAllRows;
        window.forceShowAllImages = window.forceShowAllImages;
        window.testImageSystem = window.testImageSystem;
        window.testRemoveImage = window.testRemoveImage;
        window.debugParentDiv = window.debugParentDiv;
        window.forceRemoveDisplayNone = window.forceRemoveDisplayNone;
        window.fixAllUploadButtons = window.fixAllUploadButtons;
        window.quickFix = window.quickFix;
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
        var uniqueId = package_id + '_' + offset;

        console.log("QUOTATION: Adding new row with unique offset:", offset, "for package:", package_id, "uniqueId:", uniqueId);

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
            <textarea id="day_program${offset}-u" name="day_program" class="form-control mg_bt_10 day_program" placeholder="Day-wise Program" title="Day-wise Program" onchange="validate_spaces(this.id);validate_dayprogram(this.id);" rows="3" style="height:80px;"></textarea>
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
            <!-- Upload button container -->
            <div id="upload_btn_container_${uniqueId}" style="margin-top: 35px; display: flex; align-items: center; justify-content: center; height: 100%;">
                <label for="day_image_${uniqueId}" class="btn btn-sm btn-success" style="margin-bottom: 5px; font-size: 12px; cursor: pointer; border-radius: 4px; border: none; background-color: #28a745; color: white; font-weight: 500;">
                    <i class="fa fa-image"></i> Upload Image
                </label>
                
                <input type="file" id="day_image_${uniqueId}" name="day_image_${uniqueId}" accept="image/*" onchange="
                console.log('File selected for ${uniqueId}');
                var file = this.files[0];
                if (file) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        // Show preview
                        $('#preview_img_${uniqueId}').attr('src', e.target.result).show();
                        $('#day_image_preview_${uniqueId}').show().css('display', 'block !important');
                        
                        // Hide upload button - SIMPLE DIRECT APPROACH
                        var element = document.getElementById('upload_btn_container_${uniqueId}');
                        if (element) {
                            element.style.display = 'none';
                            element.style.visibility = 'hidden';
                            element.style.opacity = '0';
                            console.log('Upload button hidden for ${uniqueId}');
                        }
                    };
                    reader.readAsDataURL(file);
                }
                " style="display: none;">
            </div>
             
            <!-- Image preview container -->
            <div id="day_image_preview_${uniqueId}" style="display: none; margin-top: 5px;">
                <div style="height:100px; max-height: 100px; overflow:hidden; position: relative; width: 100px; border: 2px solid #ddd; border-radius: 8px; background-color: #f8f9fa;">
                    <img id="preview_img_${uniqueId}" src="" alt="Preview" style="width:100%; height:100%; object-fit: cover; border-radius: 6px;"
                         onerror="
                            console.log('Image failed for ${uniqueId}');
                            this.style.display='none';
                            var previewDiv = document.getElementById('day_image_preview_${uniqueId}');
                            var uploadContainer = document.getElementById('upload_btn_container_${uniqueId}');
                            if (previewDiv) previewDiv.style.display='none';
                            if (uploadContainer) uploadContainer.style.display='flex';
                         ">
                    <button type="button" 
                            onclick="removeDayImage('${package_id}', '${offset}')" 
                            title="Remove Image" 
                            style="position: absolute; top: 5px; right: 5px; width: 20px; height: 20px; border: none; border-radius: 50%; background-color: #dc3545; color: white; font-size: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                        ×
                    </button>
                </div>
            </div>
            <input type="hidden" id="existing_image_path_${uniqueId}" name="existing_image_path_${uniqueId}" value="" />
        </td>
        <td style="width: 100px;">
            <input style="display:none" type="text" name="package_id_n" value="${package_id}">
        </td>
    `;

        // Apply CSS and event handlers to the new row
        setTimeout(function() {
            // Ensure the preview div is hidden by default
            $('#day_image_preview_' + uniqueId).hide();
            $('#day_image_preview_' + uniqueId).css('display', 'none !important');

            // Ensure the upload button container is visible by default
            $('#upload_btn_container_' + uniqueId).show();
            $('#upload_btn_container_' + uniqueId).css('display', 'flex !important');

            // Ensure the requirements tooltip is visible by default
            $('.image-requirements-tooltip').show();
            $('.image-requirements-tooltip').css('display', 'inline-block !important');

            console.log("QUOTATION: Applied styling to new row with offset:", offset);
        }, 100);
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

    // Make function globally available - Updated to handle both old and new calling patterns
    window.removeDayImage = function(packageIdOrOffset, offset) {
        // Handle both old (single parameter) and new (two parameters) calling patterns
        if (arguments.length === 1) {
            // Old calling pattern: removeDayImage(offset)
            console.log("QUOTATION: REMOVING image for offset (old pattern):", packageIdOrOffset);
            var offset = packageIdOrOffset;
            var uniqueId = offset; // For old system, use offset as uniqueId
        } else {
            // New calling pattern: removeDayImage(packageId, offset)
            console.log("QUOTATION: REMOVING image for packageId:", packageIdOrOffset, "offset:", offset);
            var uniqueId = packageIdOrOffset + "_" + offset;
        }

        // Prevent multiple calls
        if (window.removingImage && window.removingImage[uniqueId]) {
            console.log("QUOTATION: Already removing image for uniqueId:", uniqueId);
            return;
        }

        if (!window.removingImage) {
            window.removingImage = {};
        }
        window.removingImage[uniqueId] = true;

        // Get elements using uniqueId
        var fileInput = $('#day_image_' + uniqueId);
        var previewDiv = $('#day_image_preview_' + uniqueId);
        var previewImg = $('#preview_img_' + uniqueId);
        var uploadLabel = $('label[for="day_image_' + uniqueId + '"]');
        var uploadContainer = $('#upload_btn_container_' + uniqueId);

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
        $('#existing_image_path_' + uniqueId).val('');

        // Clear stored file
        if (window.quotationImages && window.quotationImages[uniqueId]) {
            delete window.quotationImages[uniqueId];
        }

        // Update the has_image state to false
        console.log("QUOTATION: Setting has_image to false for uniqueId:", uniqueId);

        // AGGRESSIVELY hide preview div and clear image
        previewDiv.hide();
        previewDiv.css({
            'display': 'none !important',
            'visibility': 'hidden !important',
            'opacity': '0 !important',
            'position': 'absolute !important',
            'left': '-9999px !important',
            'width': '0 !important',
            'height': '0 !important',
            'overflow': 'hidden !important'
        });
        previewDiv.attr('style', 'display: none !important; visibility: hidden !important; opacity: 0 !important; position: absolute !important; left: -9999px !important; width: 0 !important; height: 0 !important; overflow: hidden !important;');

        // Clear the image src completely
        previewImg.attr('src', '');
        previewImg.removeAttr('src');
        previewImg.hide();
        previewImg.css('display', 'none !important');

        // NUCLEAR OPTION - Force show BOTH container and label
        console.log("QUOTATION: NUCLEAR OPTION - Showing both container and label for uniqueId:", uniqueId);
        
        // Force show upload container
        if (uploadContainer.length > 0) {
            uploadContainer.show();
            uploadContainer.css({
                'display': 'flex !important',
                'visibility': 'visible !important',
                'opacity': '1 !important',
                'position': 'relative !important',
                'left': 'auto !important',
                'top': 'auto !important',
                'width': 'auto !important',
                'height': 'auto !important',
                'overflow': 'visible !important',
                'z-index': '9999 !important'
            });
            uploadContainer.attr('style', 'display: flex !important; visibility: visible !important; opacity: 1 !important; position: relative !important; left: auto !important; top: auto !important; width: auto !important; height: auto !important; overflow: visible !important; z-index: 9999 !important;');
            console.log("QUOTATION: Upload container shown for uniqueId:", uniqueId);
        }
        
        // Force show upload label (fallback)
        if (uploadLabel.length > 0) {
            uploadLabel.show();
            uploadLabel.css({
                'display': 'inline-block !important',
                'visibility': 'visible !important',
                'opacity': '1 !important',
                'position': 'relative !important',
                'left': 'auto !important',
                'top': 'auto !important',
                'width': 'auto !important',
                'height': 'auto !important',
                'overflow': 'visible !important',
                'z-index': '9999 !important'
            });
            uploadLabel.attr('style', 'display: inline-block !important; visibility: visible !important; opacity: 1 !important; position: relative !important; left: auto !important; top: auto !important; width: auto !important; height: auto !important; overflow: visible !important; z-index: 9999 !important;');
            console.log("QUOTATION: Upload label shown for uniqueId:", uniqueId);
        }
        
        // If still nothing visible, create a new button
        if ((uploadContainer.length === 0 || !uploadContainer.is(':visible')) && 
            (uploadLabel.length === 0 || !uploadLabel.is(':visible'))) {
            console.log("QUOTATION: Creating emergency upload button for uniqueId:", uniqueId);
            
            // Find the parent cell
            var parentCell = $('td:has(div[id="day_image_preview_' + uniqueId + '"])');
            if (parentCell.length > 0) {
                // Create emergency upload button
                var emergencyButton = $('<div style="display: flex !important; visibility: visible !important; opacity: 1 !important; position: relative !important; z-index: 9999 !important; margin-top: 35px; align-items: center; justify-content: center; height: 100%;"><label for="day_image_' + uniqueId + '" class="btn btn-sm btn-success" style="margin-bottom: 5px;font-size: 12px; cursor: pointer; border-radius: 4px; border: none; background-color: #28a745; color: white; font-weight: 500;"><i class="fa fa-image"></i> Upload Image</label></div>');
                parentCell.append(emergencyButton);
                console.log("QUOTATION: Emergency button created for uniqueId:", uniqueId);
            }
        }

        // Show the requirements tooltip when upload button is shown
        $('.image-requirements-tooltip').show();

        // Reset the file input's change event handler
        fileInput.off('change').on('change', function() {
            console.log("QUOTATION: File input change event triggered for uniqueId:", uniqueId);
            if (this.files && this.files.length > 0) {
                if (typeof window.previewDayImage === 'function') {
                    // For old system, we need to extract package ID from uniqueId or use a default
                    if (uniqueId.includes('_')) {
                        var parts = uniqueId.split('_');
                        window.previewDayImage(this, parts[0], parts[1]);
                    } else {
                        // Fallback for old system
                        window.previewDayImage(this, 'pkg0', uniqueId);
                    }
                } else {
                    console.error('QUOTATION: previewDayImage function not available in fallback');
                }
            }
        });

        // Clean up after a short delay
        setTimeout(function() {
            previewDiv.hide();
            previewDiv.css('display', 'none');

            // Force show the upload button container with maximum CSS override
            if (uploadContainer.length > 0) {
                uploadContainer.show();
                uploadContainer.attr('style', 'display: flex !important; visibility: visible !important; opacity: 1 !important; position: relative !important; left: auto !important; width: auto !important; height: auto !important; overflow: visible !important;');
                uploadContainer.css({
                    'display': 'flex !important',
                    'visibility': 'visible !important',
                    'opacity': '1 !important',
                    'position': 'relative !important',
                    'left': 'auto !important',
                    'width': 'auto !important',
                    'height': 'auto !important',
                    'overflow': 'visible !important'
                });
                console.log("QUOTATION: Upload container shown for uniqueId:", uniqueId);
            } else {
                // Fallback for old system with maximum CSS override
                uploadLabel.show();
                uploadLabel.attr('style', 'display: inline-block !important; visibility: visible !important; opacity: 1 !important; position: relative !important; left: auto !important; width: auto !important; height: auto !important; overflow: visible !important;');
                uploadLabel.css({
                    'display': 'inline-block !important',
                    'visibility': 'visible !important',
                    'opacity': '1 !important',
                    'position': 'relative !important',
                    'left': 'auto !important',
                    'width': 'auto !important',
                    'height': 'auto !important',
                    'overflow': 'visible !important'
                });
                console.log("QUOTATION: Upload label shown (fallback) for uniqueId:", uniqueId);
            }

            $('.image-requirements-tooltip').show();
            delete window.removingImage[uniqueId];
            console.log("QUOTATION: Image removal completed for uniqueId:", uniqueId);
            
            // Check visibility based on which system we're using
            var isVisible = false;
            if (uploadContainer.length > 0) {
                isVisible = uploadContainer.is(':visible');
                console.log("QUOTATION: Upload container visible after cleanup:", isVisible);
            } else {
                isVisible = uploadLabel.is(':visible');
                console.log("QUOTATION: Upload label visible after cleanup:", isVisible);
            }

            // If upload button is still not visible, try to show it using the new system
            if (!isVisible) {
                console.log("QUOTATION: Upload button not visible after cleanup, trying to show using new system...");
                if (uploadContainer.length > 0) {
                    uploadContainer.show();
                    uploadContainer.attr('style', 'display: flex !important; visibility: visible !important; opacity: 1 !important; position: relative !important; left: auto !important; width: auto !important; height: auto !important; overflow: visible !important;');
                    uploadContainer.css('display', 'flex !important');
                    console.log("QUOTATION: Force showed upload container");
                } else {
                    uploadLabel.show();
                    uploadLabel.attr('style', 'display: inline-block !important; visibility: visible !important; opacity: 1 !important; position: relative !important; left: auto !important; width: auto !important; height: auto !important; overflow: visible !important;');
                    uploadLabel.css('display', 'inline-block !important');
                    console.log("QUOTATION: Force showed upload label");
                }
            }
        }, 100);

        console.log("QUOTATION: Image removed for uniqueId:", uniqueId);
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
        var text = textarea.value;
        var content = text.replace(/\*\*(.*?)\*\*/g, '<b>$1</b>');

        // Replace markdown-style underline (__text__) with <u> tags
        content = content.replace(/__(.*?)__/g, '<u>$1</u>');
        textarea.value = content;
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


            // Only process if there are actually files selected
            if (this.files && this.files.length > 0) {

                // Small delay to ensure DOM is ready
                setTimeout(function() {
                    if (typeof window.previewDayImage === 'function') {
                        window.previewDayImage(this, offset);
                    } else {
                        console.error('QUOTATION: previewDayImage function not available in event listener');
                    }
                }.bind(this), 10);
            } else {}
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
                <div style="margin-top: 35px; display: flex; align-items: center; justify-content: center; height: 100%; visibility: visible !important; opacity: 1 !important;">
                    <label for="day_image_${offsetNum}" class="btn btn-sm btn-success upload-btn-${offsetNum}" 
                           style="margin-bottom: 5px;  font-size: 12px; cursor: pointer; border-radius: 4px; border: none; background-color: #28a745; color: white; font-weight: 500; display: inline-block !important; visibility: visible !important; opacity: 1 !important;">
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

                // Force show the upload button and ensure it's visible
                setTimeout(function() {
                    console.log("QUOTATION: Starting force visibility for offset:", offsetNum);

                    var uploadLabel = $('label[for="day_image_' + offsetNum + '"]');
                    var uploadContainer = uploadLabel.parent();
                    var fileInput = $('#day_image_' + offsetNum);

                    console.log("QUOTATION: Elements found - label:", uploadLabel.length, "container:", uploadContainer.length, "input:", fileInput.length);
                    console.log("QUOTATION: Container HTML:", uploadContainer.html());

                    if (uploadLabel.length === 0) {
                        console.error("QUOTATION: Upload label not found, trying to recreate...");
                        // Try to find the container and recreate the label
                        var container = $('#day_image_' + offsetNum).parent();
                        if (container.length > 0) {
                            container.html(`
                                <div style="margin-top: 35px; display: flex; align-items: center; justify-content: center; height: 100%; visibility: visible !important; opacity: 1 !important;">
                                    <label for="day_image_${offsetNum}" class="btn btn-sm btn-success upload-btn-${offsetNum}" 
                                           style="margin-bottom: 5px; font-size: 12px; cursor: pointer; border-radius: 4px; border: none; background-color: #28a745; color: white; font-weight: 500; display: inline-block !important; visibility: visible !important; opacity: 1 !important;">
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

                            // Update references after recreation
                            uploadLabel = $('label[for="day_image_' + offsetNum + '"]');
                            uploadContainer = uploadLabel.parent();
                            fileInput = $('#day_image_' + offsetNum);

                            console.log("QUOTATION: After recreation - label:", uploadLabel.length, "container:", uploadContainer.length, "input:", fileInput.length);
                        }
                    }

                    // Force show the container and label
                    if (uploadContainer.length > 0) {
                        uploadContainer.css({
                            'display': 'flex !important',
                            'visibility': 'visible !important',
                            'opacity': '1 !important'
                        });
                    }

                    if (uploadLabel.length > 0) {
                        uploadLabel.css({
                            'display': 'inline-block !important',
                            'visibility': 'visible !important',
                            'opacity': '1 !important'
                        });
                    }

                    console.log("QUOTATION: Forced upload button visibility for offset:", offsetNum);
                    console.log("QUOTATION: Upload button visible after force:", uploadLabel.is(':visible'));
                    console.log("QUOTATION: Upload button computed style display:", uploadLabel.css('display'));

                    // If still not visible, the new system should handle this
                    if (!uploadLabel.is(':visible') || uploadLabel.length === 0) {
                        console.log("QUOTATION: Upload button still not visible, new unique ID system should handle this");
                        // The new system should handle this automatically
                    }
                }, 100);

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

    // Function to force create and show upload button
    // DISABLED: Old function using offset system - now using unique ID system
    window.forceCreateUploadButton_OLD = function(offset) {
        console.log("QUOTATION: Force creating upload button for offset:", offset);

        // Try multiple methods to find the container
        var container = null;

        // Method 1: Look for existing file input
        var existingInput = $('#day_image_' + offset);
        if (existingInput.length > 0) {
            container = existingInput.closest('td');
            console.log("QUOTATION: Found container via existing input:", container.length);
        }

        // Method 2: Look for preview div
        if (!container || container.length === 0) {
            var previewDiv = $('#day_image_preview_' + offset);
            if (previewDiv.length > 0) {
                container = previewDiv.closest('td');
                console.log("QUOTATION: Found container via preview div:", container.length);
            }
        }

        // Method 3: Look for table row with specific offset pattern
        if (!container || container.length === 0) {
            container = $('tr').filter(function() {
                return $(this).find('input[name*="day_image_' + offset + '"]').length > 0 ||
                    $(this).find('label[for*="day_image_' + offset + '"]').length > 0 ||
                    $(this).find('[id*="day_image_' + offset + '"]').length > 0;
            }).find('td.col-md-1.pad_8').last();
            console.log("QUOTATION: Found container via row search:", container.length);
        }

        // Method 4: Look for any td with the right class that might contain our elements
        if (!container || container.length === 0) {
            container = $('td.col-md-1.pad_8').filter(function() {
                var $td = $(this);
                return $td.attr('style') && $td.attr('style').includes('width: 120px');
            }).eq(offset - 1); // Try to match by position
            console.log("QUOTATION: Found container via class and style:", container.length);
        }

        // Method 5: Look for the specific table structure pattern
        if (!container || container.length === 0) {
            // Look for table rows that contain day inputs and find the image column
            var dayRows = $('tr').filter(function() {
                return $(this).find('input[name*="day_' + offset + '"]').length > 0 ||
                    $(this).find('input[value*="day ' + offset + '"]').length > 0;
            });

            if (dayRows.length > 0) {
                // Find the last td in the row (which should be the image column)
                container = dayRows.find('td').last();
                console.log("QUOTATION: Found container via day row pattern:", container.length);
            }
        }

        if (!container || container.length === 0) {
            console.error("QUOTATION: Container not found for offset:", offset);
            console.log("QUOTATION: Available tds with col-md-1 pad_8:", $('td.col-md-1.pad_8').length);
            console.log("QUOTATION: Available file inputs:", $('input[id^="day_image_"]').length);
            console.log("QUOTATION: Available preview divs:", $('[id^="day_image_preview_"]').length);
            return false;
        }

        // Clear the container completely
        container.empty();

        // Create fresh HTML structure
        container.html(`
            <div style="margin-top: 35px; display: flex; align-items: center; justify-content: center; height: 100%; visibility: visible !important; opacity: 1 !important;">
                <label for="day_image_${offset}" class="btn btn-sm btn-success upload-btn-${offset}" 
                       style="margin-bottom: 5px;  font-size: 12px; cursor: pointer; border-radius: 4px; border: none; background-color: #28a745; color: white; font-weight: 500; display: inline-block !important; visibility: visible !important; opacity: 1 !important;">
                    <i class="fa fa-image"></i> Upload Image
                </label>
                <input type="file" id="day_image_${offset}" 
                       name="day_image_${offset}" accept="image/*" 
                       onchange="console.log('QUOTATION: File input changed for offset ${offset}'); if(typeof window.previewDayImage === 'function') { window.previewDayImage(this, '${offset}'); } else { console.error('QUOTATION: previewDayImage function not available'); }" 
                       style="display: none;">
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
            </div>
            <input type="hidden" id="existing_image_path_${offset}" name="existing_image_path_${offset}" value="" />
        `);

        // Verify the button was created and is visible
        setTimeout(function() {
            var uploadLabel = $('label[for="day_image_' + offset + '"]');
            console.log("QUOTATION: Force created upload button - found:", uploadLabel.length);
            console.log("QUOTATION: Force created upload button - visible:", uploadLabel.is(':visible'));
            console.log("QUOTATION: Force created upload button - display:", uploadLabel.css('display'));
        }, 50);

        return true;
    };

    // Test function to manually create upload button for testing - DISABLED (old system)
    window.testCreateUploadButton_OLD = function(offset) {
        console.log("QUOTATION: Testing upload button creation for offset:", offset);
        console.log("QUOTATION: This function is disabled - use the new unique ID system instead");
        return false;
    };

    // Debug function to analyze table structure
    window.debugTableStructure = function() {
        console.log("QUOTATION: Analyzing table structure...");
        console.log("QUOTATION: Total table rows:", $('tr').length);
        console.log("QUOTATION: Total tds with col-md-1 pad_8:", $('td.col-md-1.pad_8').length);
        console.log("QUOTATION: Total file inputs:", $('input[id^="day_image_"]').length);
        console.log("QUOTATION: Total preview divs:", $('[id^="day_image_preview_"]').length);

        // Log each row structure
        $('tr').each(function(index) {
            var $row = $(this);
            var dayInputs = $row.find('input[name*="day_"]');
            var imageInputs = $row.find('input[id^="day_image_"]');
            if (dayInputs.length > 0 || imageInputs.length > 0) {
                console.log("QUOTATION: Row", index, "- Day inputs:", dayInputs.length, "Image inputs:", imageInputs.length);
                console.log("QUOTATION: Row", index, "- TDs:", $row.find('td').length);
            }
        });
    };

    // Simple test functions to debug image preview
    window.testImagePreview = function(offset) {
        console.log("QUOTATION: Testing image preview for offset:", offset);

        // Check if elements exist
        var previewImg = $('#preview_img_' + offset);
        var previewDiv = $('#day_image_preview_' + offset);
        var uploadLabel = $('label[for="day_image_' + offset + '"]');

        console.log("QUOTATION: Elements found - img:", previewImg.length, "div:", previewDiv.length, "label:", uploadLabel.length);

        if (previewImg.length === 0) {
            console.error("QUOTATION: Preview image element not found for offset:", offset);
            return false;
        }

        if (previewDiv.length === 0) {
            console.error("QUOTATION: Preview div element not found for offset:", offset);
            return false;
        }

        // Test showing preview
        previewDiv.show();
        previewDiv.css('display', 'block');
        previewDiv.attr('style', 'display: block !important; margin-top: 5px;');

        // Test hiding upload button
        uploadLabel.hide();
        uploadLabel.css('display', 'none');

        console.log("QUOTATION: Test completed for offset:", offset);
        return true;
    };

    // Force show all preview divs
    window.showAllPreviews = function() {
        console.log("QUOTATION: Showing all preview divs");
        $('[id^="day_image_preview_"]').each(function() {
            var offset = $(this).attr('id').replace('day_image_preview_', '');
            console.log("QUOTATION: Showing preview for offset:", offset);
            $(this).show();
            $(this).css('display', 'block');
            $(this).attr('style', 'display: block !important; margin-top: 5px;');
        });
    };

    // Enhanced force show function with maximum visibility
    // DISABLED: Old function using offset system - now using unique ID system
    window.forceShowPreview_OLD = function(offset) {
        console.log("QUOTATION: Force showing preview for offset:", offset);

        var previewDiv = $('#day_image_preview_' + offset);
        var previewImg = $('#preview_img_' + offset);
        var uploadLabel = $('label[for="day_image_' + offset + '"]');
        var removeButton = previewDiv.find('button[onclick*="removeDayImage"]');

        // Force show preview with maximum CSS overrides
        previewDiv.show();
        previewDiv.css({
            'display': 'block !important',
            'visibility': 'visible !important',
            'opacity': '1 !important',
            'position': 'relative !important',
            'z-index': '1000 !important',
            'width': '100px !important',
            'height': '100px !important',
            'margin-top': '5px !important'
        });
        previewDiv.attr('style', 'display: block !important; visibility: visible !important; opacity: 1 !important; position: relative !important; z-index: 1000 !important; width: 100px !important; height: 100px !important; margin-top: 5px !important;');

        // Force show image
        previewImg.css({
            'display': 'block !important',
            'visibility': 'visible !important',
            'opacity': '1 !important',
            'width': '100% !important',
            'height': '100% !important'
        });

        // Force show remove button
        removeButton.css({
            'display': 'flex !important',
            'visibility': 'visible !important',
            'opacity': '1 !important',
            'position': 'absolute !important',
            'top': '2px !important',
            'right': '2px !important',
            'z-index': '1003 !important'
        });
        removeButton.show();

        // Hide upload button and instruction div
        uploadLabel.hide();
        uploadLabel.css('display', 'none !important');

        // Hide instruction div
        $('.image-requirements').hide();
        $('.image-requirements').css('display', 'none !important');
        $('.image-requirements').css('visibility', 'hidden !important');
        $('.image-requirements').css('opacity', '0 !important');

        console.log("QUOTATION: Force show completed for offset:", offset);
        console.log("QUOTATION: Preview div visible:", previewDiv.is(':visible'));
        console.log("QUOTATION: Remove button visible:", removeButton.is(':visible'));
        console.log("QUOTATION: Remove button count:", removeButton.length);

        return true;
    };

    // Debug function to check remove button visibility
    window.checkRemoveButton = function(offset) {
        console.log("QUOTATION: Checking remove button for offset:", offset);
        var removeButton = $('#day_image_preview_' + offset).find('button[onclick*="removeDayImage"]');
        console.log("QUOTATION: Remove button found:", removeButton.length);
        console.log("QUOTATION: Remove button visible:", removeButton.is(':visible'));
        console.log("QUOTATION: Remove button display style:", removeButton.css('display'));
        console.log("QUOTATION: Remove button computed style:", removeButton[0] ? window.getComputedStyle(removeButton[0]).display : 'N/A');
        return removeButton;
    };

    // Debug function to check upload button visibility
    window.checkUploadButton = function(offset) {
        console.log("QUOTATION: Checking upload button for offset:", offset);
        var uploadButton = $('label[for="day_image_' + offset + '"]');
        var uploadContainer = uploadButton.parent();
        var previewDiv = $('#day_image_preview_' + offset);
        console.log("QUOTATION: Upload button found:", uploadButton.length);
        console.log("QUOTATION: Upload container found:", uploadContainer.length);
        console.log("QUOTATION: Upload container visible:", uploadContainer.is(':visible'));
        console.log("QUOTATION: Upload container display style:", uploadContainer.css('display'));
        console.log("QUOTATION: Preview div visible:", previewDiv.is(':visible'));
        console.log("QUOTATION: Preview div display style:", previewDiv.css('display'));

        // If preview is visible, upload container should be hidden
        if (previewDiv.is(':visible')) {
            console.log("QUOTATION: Preview is visible, hiding upload container");
            uploadContainer.hide();
            uploadContainer.css('display', 'none !important');
        } else {
            console.log("QUOTATION: Preview is hidden, showing upload container");
            uploadContainer.show();
            uploadContainer.css('display', 'block !important');
        }

        return uploadContainer;
    };

    // Function to fix all dynamically added rows
    // DISABLED: Old function using offset system - now using unique ID system
    window.fixDynamicRows_OLD = function() {
        console.log("QUOTATION: Fixing all dynamic rows");

        // Find all dynamically added rows (those with high offset numbers)
        $('[id^="day_image_preview_"]').each(function() {
            var offset = $(this).attr('id').replace('day_image_preview_', '');
            var previewDiv = $(this);
            var uploadButton = $('label[for="day_image_' + offset + '"]');
            var requirementsTooltip = $('.image-requirements-tooltip');

            // If preview has an image, hide upload button
            var hasImage = previewDiv.find('img').attr('src') && previewDiv.find('img').attr('src') !== '';

            if (hasImage) {
                console.log("QUOTATION: Row", offset, "has image, hiding upload button");
                previewDiv.show();
                previewDiv.css('display', 'block !important');
                uploadButton.hide();
                uploadButton.css('display', 'none !important');
                uploadButton.css('visibility', 'hidden !important');
                uploadButton.css('opacity', '0 !important');
                uploadButton.attr('style', 'display: none !important; visibility: hidden !important; opacity: 0 !important;');
                requirementsTooltip.hide();
                requirementsTooltip.css('display', 'none !important');
            } else {
                console.log("QUOTATION: Row", offset, "has no image, showing upload button");
                previewDiv.hide();
                previewDiv.css('display', 'none !important');
                uploadButton.show();
                uploadButton.css('display', 'inline-block !important');
                uploadButton.css('visibility', 'visible !important');
                uploadButton.css('opacity', '1 !important');
                requirementsTooltip.show();
                requirementsTooltip.css('display', 'inline-block !important');
            }
        });

        console.log("QUOTATION: Fixed all dynamic rows");
    };

    // Force hide upload button for specific offset
    window.forceHideUploadButton = function(offset) {
        console.log("QUOTATION: Force hiding upload button for offset:", offset);

        var uploadButton = $('label[for="day_image_' + offset + '"]');
        console.log("QUOTATION: Upload button found:", uploadButton.length);
        console.log("QUOTATION: Upload button before hide:", uploadButton.is(':visible'));

        // Multiple approaches to hide the button
        uploadButton.hide();
        uploadButton.css('display', 'none !important');
        uploadButton.css('visibility', 'hidden !important');
        uploadButton.css('opacity', '0 !important');
        uploadButton.attr('style', 'display: none !important; visibility: hidden !important; opacity: 0 !important;');

        // Also hide the parent container if it exists
        uploadButton.parent().hide();
        uploadButton.parent().css('display', 'none !important');

        console.log("QUOTATION: Upload button after force hide:", uploadButton.is(':visible'));
        console.log("QUOTATION: Upload button computed style:", uploadButton[0] ? window.getComputedStyle(uploadButton[0]).display : 'N/A');

        return uploadButton;
    };

    // Test if previewDayImage function exists
    window.testPreviewFunction = function() {
        console.log("QUOTATION: Testing previewDayImage function");
        console.log("QUOTATION: previewDayImage exists:", typeof previewDayImage);
        console.log("QUOTATION: window.previewDayImage exists:", typeof window.previewDayImage);

        // Test with a simple call
        if (typeof previewDayImage === 'function') {
            console.log("QUOTATION: previewDayImage function is available");
            return true;
        } else {
            console.error("QUOTATION: previewDayImage function not found");
            return false;
        }
    };









    function previewDayImage(input, packageId, offset) {
    console.log("QUOTATION: previewDayImage called with packageId:", packageId, "offset:", offset);
    const uniqueId = packageId + "_" + offset;
    console.log("QUOTATION: Generated uniqueId:", uniqueId);

    if (input.files && input.files[0]) {
        console.log("QUOTATION: File selected, starting file reader");
        const file = input.files[0];
        const reader = new FileReader();

        reader.onload = function (e) {
            console.log("QUOTATION: Image loaded for uniqueId:", uniqueId);
            
            // Show preview
            $('#preview_img_' + uniqueId).attr('src', e.target.result).show();
            $('#day_image_preview_' + uniqueId).show().css({
                'display': 'block !important',
                'visibility': 'visible !important',
                'opacity': '1 !important',
                'position': 'relative !important',
                'left': 'auto !important',
                'width': 'auto !important',
                'height': 'auto !important',
                'overflow': 'visible !important'
            });
            
            // AGGRESSIVE APPROACH - Use the working method
            setTimeout(function() {
                console.log("QUOTATION: Using aggressive hide for uniqueId:", uniqueId);
                
                var element = document.getElementById('upload_btn_container_' + uniqueId);

                console.log(element,'helllooo')
                if (element) {
                    // Remove the element completely and recreate it hidden
                    var parent = element.parentNode;
                    var hiddenElement = element.cloneNode(true);
                    hiddenElement.style.cssText = 'display: none !important; visibility: hidden !important; opacity: 0 !important; position: absolute !important; left: -9999px !important; width: 0 !important; height: 0 !important; overflow: hidden !important;';
                    hiddenElement.setAttribute('style', 'display: none !important; visibility: hidden !important; opacity: 0 !important; position: absolute !important; left: -9999px !important; width: 0 !important; height: 0 !important; overflow: hidden !important;');
                    
                    parent.replaceChild(hiddenElement, element);
                    
                    console.log("QUOTATION: Aggressive hide completed for uniqueId:", uniqueId);
                } else {
                    console.log("QUOTATION: ERROR - Upload container element not found for uniqueId:", uniqueId);
                }
            }, 50);

            if (!window.quotationImages) window.quotationImages = {};
            window.quotationImages[uniqueId] = { file, package_id: packageId, offset, preview_url: e.target.result };
        };

        reader.readAsDataURL(file);
    }
}

function removeDayImage(packageId, offset) {
    const uniqueId = packageId + "_" + offset;

    $('#day_image_' + uniqueId).val('');
    $('#preview_img_' + uniqueId).attr('src', '').hide();
    $('#day_image_preview_' + uniqueId).hide();
    $('#upload_btn_container_' + uniqueId).show();
    $('#existing_image_path_' + uniqueId).val('');

    if (window.quotationImages && window.quotationImages[uniqueId]) {
        delete window.quotationImages[uniqueId];
    }
}

// Emergency function to force show all upload buttons
window.forceShowAllUploadButtons = function() {
    console.log("QUOTATION: Emergency - Force showing all upload buttons");
    
    // Count elements found
    var containers = $('div[id^="upload_btn_container_"]');
    var labels = $('label[for^="day_image_"]');
    console.log("QUOTATION: Found", containers.length, "upload containers and", labels.length, "upload labels");
    
    // NUCLEAR OPTION: Remove all CSS classes that might be hiding elements
    $('div[id^="upload_btn_container_"], label[for^="day_image_"]').removeClass();
    
    // Show all upload containers with maximum force
    containers.each(function(index) {
        var $this = $(this);
        console.log("QUOTATION: Processing container", index, "ID:", this.id);
        
        // Remove any inline styles that might hide it
        this.removeAttribute('style');
        
        // Force show with maximum CSS override
        $this.show();
        $this.css({
            'display': 'flex !important',
            'visibility': 'visible !important',
            'opacity': '1 !important',
            'position': 'relative !important',
            'left': 'auto !important',
            'top': 'auto !important',
            'width': 'auto !important',
            'height': 'auto !important',
            'overflow': 'visible !important',
            'z-index': '9999 !important',
            'background': 'transparent !important',
            'border': 'none !important',
            'margin': '0 !important',
            'padding': '0 !important'
        });
        
        // Also set inline style as backup
        this.style.cssText = 'display: flex !important; visibility: visible !important; opacity: 1 !important; position: relative !important; left: auto !important; top: auto !important; width: auto !important; height: auto !important; overflow: visible !important; z-index: 9999 !important; background: transparent !important; border: none !important; margin: 0 !important; padding: 0 !important;';
        
        console.log("QUOTATION: Container", index, "forced visible");
    });
    
    // Show all upload labels with maximum force
    labels.each(function(index) {
        var $this = $(this);
        console.log("QUOTATION: Processing label", index, "for:", this.getAttribute('for'));
        
        // Remove any inline styles that might hide it
        this.removeAttribute('style');
        
        // Force show with maximum CSS override
        $this.show();
        $this.css({
            'display': 'inline-block !important',
            'visibility': 'visible !important',
            'opacity': '1 !important',
            'position': 'relative !important',
            'left': 'auto !important',
            'top': 'auto !important',
            'width': 'auto !important',
            'height': 'auto !important',
            'overflow': 'visible !important',
            'z-index': '9999 !important',
            'background': '#28a745 !important',
            'color': 'white !important',
            'border': 'none !important',
            'border-radius': '4px !important',
            'padding': '6px 12px !important',
            'margin': '0 !important',
            'cursor': 'pointer !important'
        });
        
        // Also set inline style as backup
        this.style.cssText = 'display: inline-block !important; visibility: visible !important; opacity: 1 !important; position: relative !important; left: auto !important; top: auto !important; width: auto !important; height: auto !important; overflow: visible !important; z-index: 9999 !important; background: #28a745 !important; color: white !important; border: none !important; border-radius: 4px !important; padding: 6px 12px !important; margin: 0 !important; cursor: pointer !important;';
        
        console.log("QUOTATION: Label", index, "forced visible");
    });
    
    console.log("QUOTATION: Emergency force show completed");
};

// Debug function to check current state
window.debugUploadButtons = function() {
    console.log("QUOTATION: === DEBUGGING UPLOAD BUTTONS ===");
    
    // Check containers
    $('div[id^="upload_btn_container_"]').each(function(index) {
        var $this = $(this);
        console.log("Container", index, ":", {
            id: this.id,
            display: $this.css('display'),
            visible: $this.is(':visible'),
            style: this.getAttribute('style'),
            parent: this.parentElement ? this.parentElement.tagName : 'none'
        });
    });
    
    // Check labels
    $('label[for^="day_image_"]').each(function(index) {
        var $this = $(this);
        console.log("Label", index, ":", {
            id: this.id,
            for: this.getAttribute('for'),
            display: $this.css('display'),
            visible: $this.is(':visible'),
            style: this.getAttribute('style'),
            parent: this.parentElement ? this.parentElement.tagName : 'none'
        });
    });
    
    console.log("QUOTATION: === END DEBUG ===");
};

// Function to check CSS rules that might be hiding elements
window.checkCSSRules = function() {
    console.log("QUOTATION: === CHECKING CSS RULES ===");
    
    var containers = $('div[id^="upload_btn_container_"]');
    var labels = $('label[for^="day_image_"]');
    
    if (containers.length > 0) {
        var container = containers.first();
        var computedStyle = window.getComputedStyle(container[0]);
        console.log("QUOTATION: First container computed styles:", {
            display: computedStyle.display,
            visibility: computedStyle.visibility,
            opacity: computedStyle.opacity,
            position: computedStyle.position,
            left: computedStyle.left,
            top: computedStyle.top,
            width: computedStyle.width,
            height: computedStyle.height,
            overflow: computedStyle.overflow,
            zIndex: computedStyle.zIndex
        });
    }
    
    if (labels.length > 0) {
        var label = labels.first();
        var computedStyle = window.getComputedStyle(label[0]);
        console.log("QUOTATION: First label computed styles:", {
            display: computedStyle.display,
            visibility: computedStyle.visibility,
            opacity: computedStyle.opacity,
            position: computedStyle.position,
            left: computedStyle.left,
            top: computedStyle.top,
            width: computedStyle.width,
            height: computedStyle.height,
            overflow: computedStyle.overflow,
            zIndex: computedStyle.zIndex
        });
    }
    
    console.log("QUOTATION: === END CSS CHECK ===");
};

// Function to manually fix a specific image removal
window.fixImageRemoval = function(packageId, offset) {
    var uniqueId = packageId + "_" + offset;
    console.log("QUOTATION: Manually fixing image removal for uniqueId:", uniqueId);
    
    // Get elements
    var previewDiv = $('#day_image_preview_' + uniqueId);
    var previewImg = $('#preview_img_' + uniqueId);
    var uploadContainer = $('#upload_btn_container_' + uniqueId);
    var uploadLabel = $('label[for="day_image_' + uniqueId + '"]');
    
    console.log("QUOTATION: Found elements - previewDiv:", previewDiv.length, "previewImg:", previewImg.length, "uploadContainer:", uploadContainer.length, "uploadLabel:", uploadLabel.length);
    
    // AGGRESSIVELY hide preview
    previewDiv.hide();
    previewDiv.css({
        'display': 'none !important',
        'visibility': 'hidden !important',
        'opacity': '0 !important',
        'position': 'absolute !important',
        'left': '-9999px !important',
        'width': '0 !important',
        'height': '0 !important',
        'overflow': 'hidden !important'
    });
    previewDiv.attr('style', 'display: none !important; visibility: hidden !important; opacity: 0 !important; position: absolute !important; left: -9999px !important; width: 0 !important; height: 0 !important; overflow: hidden !important;');
    
    // Clear image
    previewImg.attr('src', '');
    previewImg.removeAttr('src');
    previewImg.hide();
    previewImg.css('display', 'none !important');
    
    // FORCE show upload button
    if (uploadContainer.length > 0) {
        uploadContainer.show();
        uploadContainer.css({
            'display': 'flex !important',
            'visibility': 'visible !important',
            'opacity': '1 !important',
            'position': 'relative !important',
            'left': 'auto !important',
            'top': 'auto !important',
            'width': 'auto !important',
            'height': 'auto !important',
            'overflow': 'visible !important',
            'z-index': '9999 !important'
        });
        uploadContainer.attr('style', 'display: flex !important; visibility: visible !important; opacity: 1 !important; position: relative !important; left: auto !important; top: auto !important; width: auto !important; height: auto !important; overflow: visible !important; z-index: 9999 !important;');
        console.log("QUOTATION: Upload container shown for uniqueId:", uniqueId);
    } else if (uploadLabel.length > 0) {
        uploadLabel.show();
        uploadLabel.css({
            'display': 'inline-block !important',
            'visibility': 'visible !important',
            'opacity': '1 !important',
            'position': 'relative !important',
            'left': 'auto !important',
            'top': 'auto !important',
            'width': 'auto !important',
            'height': 'auto !important',
            'overflow': 'visible !important',
            'z-index': '9999 !important'
        });
        uploadLabel.attr('style', 'display: inline-block !important; visibility: visible !important; opacity: 1 !important; position: relative !important; left: auto !important; top: auto !important; width: auto !important; height: auto !important; overflow: visible !important; z-index: 9999 !important;');
        console.log("QUOTATION: Upload label shown for uniqueId:", uniqueId);
    } else {
        console.log("QUOTATION: No upload elements found for uniqueId:", uniqueId);
    }
    
    console.log("QUOTATION: Manual fix completed for uniqueId:", uniqueId);
};

// Function to initialize upload button visibility based on actual image loading
window.initializeUploadButtonVisibility = function() {
    console.log("QUOTATION: Initializing upload button visibility...");
    
    // Check all image preview containers
    $('div[id^="day_image_preview_"]').each(function() {
        var $previewDiv = $(this);
        var uniqueId = this.id.replace('day_image_preview_', '');
        var $uploadContainer = $('#upload_btn_container_' + uniqueId);
        var $previewImg = $('#preview_img_' + uniqueId);
        
        console.log("QUOTATION: Checking preview for uniqueId:", uniqueId);
        
        // Check if image actually has a valid src and is visible
        var hasValidImage = false;
        if ($previewImg.length > 0) {
            var imgSrc = $previewImg.attr('src');
            if (imgSrc && imgSrc !== '' && imgSrc !== 'null' && imgSrc !== 'undefined') {
                // Check if image is actually visible (not hidden by CSS)
                var isVisible = $previewDiv.is(':visible') && $previewImg.is(':visible');
                hasValidImage = isVisible;
                console.log("QUOTATION: Image src:", imgSrc, "visible:", isVisible);
            }
        }
        
        // Set visibility based on actual image state
        if (hasValidImage) {
            $previewDiv.show();
            $uploadContainer.hide();
            console.log("QUOTATION: Showing preview, hiding upload for uniqueId:", uniqueId);
        } else {
            $previewDiv.hide();
            $uploadContainer.show();
            console.log("QUOTATION: Hiding preview, showing upload for uniqueId:", uniqueId);
        }
    });
    
    console.log("QUOTATION: Upload button visibility initialization completed");
};

// Simple initialization - let PHP handle the initial state
$(document).ready(function() {
    console.log("QUOTATION: Page loaded - PHP handles initial state");
});

// Emergency function to fix the specific issue with package 3, offset 1
window.fixSpecificIssue = function() {
    console.log("QUOTATION: Fixing specific issue for package 3, offset 1");
    
    var uniqueId = '3_1';
    var $uploadContainer = $('#upload_btn_container_' + uniqueId);
    var $previewDiv = $('#day_image_preview_' + uniqueId);
    var $previewImg = $('#preview_img_' + uniqueId);
    
    console.log("QUOTATION: Elements found - container:", $uploadContainer.length, "preview:", $previewDiv.length, "img:", $previewImg.length);
    
    // Force hide preview
    $previewDiv.hide();
    $previewDiv.css({
        'display': 'none !important',
        'visibility': 'hidden !important',
        'opacity': '0 !important'
    });
    
    // Force show upload container
    $uploadContainer.show();
    $uploadContainer.css({
        'display': 'flex !important',
        'visibility': 'visible !important',
        'opacity': '1 !important',
        'position': 'relative !important',
        'left': 'auto !important',
        'top': 'auto !important',
        'width': 'auto !important',
        'height': 'auto !important',
        'overflow': 'visible !important',
        'z-index': '9999 !important'
    });
    $uploadContainer.attr('style', 'display: flex !important; visibility: visible !important; opacity: 1 !important; position: relative !important; left: auto !important; top: auto !important; width: auto !important; height: auto !important; overflow: visible !important; z-index: 9999 !important;');
    
    console.log("QUOTATION: Specific fix completed for uniqueId:", uniqueId);
    console.log("QUOTATION: Upload container visible:", $uploadContainer.is(':visible'));
};

// Simple test function to check current state
window.testCurrentState = function() {
    console.log("QUOTATION: === TESTING CURRENT STATE ===");
    
    var uniqueId = '3_1';
    var $uploadContainer = $('#upload_btn_container_' + uniqueId);
    var $previewDiv = $('#day_image_preview_' + uniqueId);
    var $previewImg = $('#preview_img_' + uniqueId);
    
    console.log("QUOTATION: Elements found:");
    console.log("- Upload container:", $uploadContainer.length, "visible:", $uploadContainer.is(':visible'));
    console.log("- Preview div:", $previewDiv.length, "visible:", $previewDiv.is(':visible'));
    console.log("- Preview img:", $previewImg.length, "visible:", $previewImg.is(':visible'));
    
    if ($previewImg.length > 0) {
        console.log("- Image src:", $previewImg.attr('src'));
    }
    
    console.log("QUOTATION: === END TEST ===");
};

// EMERGENCY FUNCTION - This will definitely work
window.emergencyFix = function() {
    console.log("QUOTATION: EMERGENCY FIX - Creating upload buttons everywhere");
    
    // Find all cells that have image previews but no visible upload buttons
    $('td:has(div[id^="day_image_preview_"])').each(function() {
        var $cell = $(this);
        var $previewDiv = $cell.find('div[id^="day_image_preview_"]');
        var $uploadContainer = $cell.find('div[id^="upload_btn_container_"]');
        var $uploadLabel = $cell.find('label[for^="day_image_"]');
        
        // Check if preview is visible
        var previewVisible = $previewDiv.is(':visible');
        var uploadVisible = $uploadContainer.is(':visible') || $uploadLabel.is(':visible');
        
        console.log("QUOTATION: Cell check - preview visible:", previewVisible, "upload visible:", uploadVisible);
        
        // If preview is not visible but upload is also not visible, create emergency button
        if (!previewVisible && !uploadVisible) {
            var uniqueId = $previewDiv.attr('id').replace('day_image_preview_', '');
            console.log("QUOTATION: Creating emergency button for uniqueId:", uniqueId);
            
            // Create emergency upload button
            var emergencyButton = $('<div style="display: flex !important; visibility: visible !important; opacity: 1 !important; position: relative !important; z-index: 9999 !important; margin-top: 35px; align-items: center; justify-content: center; height: 100%;"><label for="day_image_' + uniqueId + '" class="btn btn-sm btn-success" style="margin-bottom: 5px;font-size: 12px; cursor: pointer; border-radius: 4px; border: none; background-color: #28a745; color: white; font-weight: 500;"><i class="fa fa-image"></i> Upload Image</label></div>');
            $cell.append(emergencyButton);
        }
    });
    
    console.log("QUOTATION: Emergency fix completed");
};

// Specific function to fix package 2 issue
window.fixPackage2Issue = function() {
    console.log("QUOTATION: Fixing package 2 issue - hiding empty previews, showing upload buttons");
    
    // Find all preview divs that are showing but have no valid image
    $('div[id^="day_image_preview_"]').each(function() {
        var $previewDiv = $(this);
        var uniqueId = this.id.replace('day_image_preview_', '');
        var $previewImg = $previewDiv.find('img');
        var $uploadContainer = $('#upload_btn_container_' + uniqueId);
        
        console.log("QUOTATION: Checking preview for uniqueId:", uniqueId);
        
        // Check if there's a valid image
        var hasValidImage = false;
        if ($previewImg.length > 0) {
            var imgSrc = $previewImg.attr('src');
            console.log("QUOTATION: Image src:", imgSrc);
            
            if (imgSrc && imgSrc !== '' && imgSrc !== 'null' && imgSrc !== 'undefined') {
                // Check if the image has a valid source (not just empty or placeholder)
                var hasValidSrc = (imgSrc.indexOf('data:') === 0) || // Base64 image
                                 (imgSrc.indexOf('http') === 0) || // HTTP URL
                                 (imgSrc.indexOf('/') === 0) || // Relative path
                                 (imgSrc.indexOf('blob:') === 0); // Blob URL
                
                if (hasValidSrc) {
                    // Check if image is actually visible and not broken
                    var isVisible = $previewDiv.is(':visible') && $previewImg.is(':visible');
                    hasValidImage = isVisible;
                    console.log("QUOTATION: Image is valid and visible:", hasValidImage);
                } else {
                    console.log("QUOTATION: Invalid image src:", imgSrc);
                }
            }
        }
        
        if (!hasValidImage) {
            // No valid image - hide preview completely, show upload button
            console.log("QUOTATION: Hiding empty preview for uniqueId:", uniqueId);
            
            $previewDiv.hide();
            $previewDiv.css({
                'display': 'none !important',
                'visibility': 'hidden !important',
                'opacity': '0 !important',
                'position': 'absolute !important',
                'left': '-9999px !important',
                'width': '0 !important',
                'height': '0 !important',
                'overflow': 'hidden !important'
            });
            $previewDiv.attr('style', 'display: none !important; visibility: hidden !important; opacity: 0 !important; position: absolute !important; left: -9999px !important; width: 0 !important; height: 0 !important; overflow: hidden !important;');
            
            // Show upload button
            if ($uploadContainer.length > 0) {
                $uploadContainer.show();
                $uploadContainer.css({
                    'display': 'flex !important',
                    'visibility': 'visible !important',
                    'opacity': '1 !important'
                });
                console.log("QUOTATION: Showing upload container for uniqueId:", uniqueId);
            }
        } else {
            // Has valid image - show preview, hide upload
            console.log("QUOTATION: Showing preview for uniqueId:", uniqueId);
            $previewDiv.show();
            if ($uploadContainer.length > 0) {
                $uploadContainer.hide();
            }
        }
    });
    
    console.log("QUOTATION: Package 2 fix completed");
};

// Function to check all image states
window.checkAllImageStates = function() {
    console.log("QUOTATION: === CHECKING ALL IMAGE STATES ===");
    
    $('div[id^="day_image_preview_"]').each(function() {
        var $previewDiv = $(this);
        var uniqueId = this.id.replace('day_image_preview_', '');
        var $previewImg = $previewDiv.find('img');
        var $uploadContainer = $('#upload_btn_container_' + uniqueId);
        
        console.log("QUOTATION: === UNIQUE ID:", uniqueId, "===");
        console.log("- Preview div visible:", $previewDiv.is(':visible'));
        console.log("- Preview img found:", $previewImg.length);
        
        if ($previewImg.length > 0) {
            var imgSrc = $previewImg.attr('src');
            console.log("- Image src:", imgSrc);
            console.log("- Image visible:", $previewImg.is(':visible'));
            
            if (imgSrc && imgSrc !== '' && imgSrc !== 'null' && imgSrc !== 'undefined') {
                var hasValidSrc = (imgSrc.indexOf('data:') === 0) || 
                                 (imgSrc.indexOf('http') === 0) || 
                                 (imgSrc.indexOf('/') === 0) || 
                                 (imgSrc.indexOf('blob:') === 0);
                console.log("- Has valid src:", hasValidSrc);
            } else {
                console.log("- No valid src");
            }
        }
        
        console.log("- Upload container visible:", $uploadContainer.is(':visible'));
        console.log("---");
    });
    
    console.log("QUOTATION: === END IMAGE STATE CHECK ===");
};

// Simple function to show all upload buttons (for debugging)
window.showAllUploadButtons = function() {
    console.log("QUOTATION: Showing all upload buttons");
    
    $('div[id^="upload_btn_container_"]').show();
    $('div[id^="day_image_preview_"]').hide();
    
    console.log("QUOTATION: All upload buttons shown, all previews hidden");
};

// Function to force hide upload button for a specific uniqueId
window.forceHideUploadButton = function(uniqueId) {
    console.log("QUOTATION: Force hiding upload button for uniqueId:", uniqueId);
    
    var $uploadContainer = $('#upload_btn_container_' + uniqueId);
    $uploadContainer.hide();
    $uploadContainer.css({
        'display': 'none !important',
        'visibility': 'hidden !important',
        'opacity': '0 !important',
        'position': 'absolute !important',
        'left': '-9999px !important',
        'width': '0 !important',
        'height': '0 !important',
        'overflow': 'hidden !important'
    });
    $uploadContainer.attr('style', 'display: none !important; visibility: hidden !important; opacity: 0 !important; position: absolute !important; left: -9999px !important; width: 0 !important; height: 0 !important; overflow: hidden !important;');
    $uploadContainer.removeClass();
    
    console.log("QUOTATION: Upload button force hidden for uniqueId:", uniqueId);
};

// SIMPLE FUNCTION TO TEST - Call this after selecting an image
window.testImageUpload = function(uniqueId) {
    console.log("QUOTATION: Testing image upload for uniqueId:", uniqueId);
    
    // Hide upload button using direct DOM access
    var uploadElement = document.getElementById('upload_btn_container_' + uniqueId);
    if (uploadElement) {
        uploadElement.style.display = 'none';
        console.log("QUOTATION: Upload button hidden for uniqueId:", uniqueId);
        console.log("QUOTATION: Upload container style:", uploadElement.style.cssText);
    } else {
        console.log("QUOTATION: ERROR - Upload container not found for uniqueId:", uniqueId);
    }
    
    // Show preview
    var previewElement = document.getElementById('day_image_preview_' + uniqueId);
    if (previewElement) {
        previewElement.style.display = 'block';
        console.log("QUOTATION: Preview shown for uniqueId:", uniqueId);
    } else {
        console.log("QUOTATION: ERROR - Preview container not found for uniqueId:", uniqueId);
    }
    
    console.log("QUOTATION: Test completed");
};

// Function to test with the specific uniqueId you mentioned
window.testUpload42 = function() {
    testImageUpload('4_2');
};

// AGGRESSIVE HIDE FUNCTION - Use this if the normal approach doesn't work
window.aggressiveHideUpload = function(uniqueId) {
    console.log("QUOTATION: AGGRESSIVE HIDE for uniqueId:", uniqueId);
    
    var element = document.getElementById('upload_btn_container_' + uniqueId);
    if (element) {
        // Remove the element completely and recreate it hidden
        var parent = element.parentNode;
        var hiddenElement = element.cloneNode(true);
        hiddenElement.style.cssText = 'display: none !important; visibility: hidden !important; opacity: 0 !important; position: absolute !important; left: -9999px !important; width: 0 !important; height: 0 !important; overflow: hidden !important;';
        hiddenElement.setAttribute('style', 'display: none !important; visibility: hidden !important; opacity: 0 !important; position: absolute !important; left: -9999px !important; width: 0 !important; height: 0 !important; overflow: hidden !important;');
        
        parent.replaceChild(hiddenElement, element);
        
        console.log("QUOTATION: AGGRESSIVE HIDE completed for uniqueId:", uniqueId);
    } else {
        console.log("QUOTATION: Element not found for aggressive hide:", uniqueId);
    }
};

// TEST FUNCTION - Simulate the previewDayImage function
window.testPreviewDayImage = function(packageId, offset) {
    console.log("QUOTATION: TEST - Simulating previewDayImage");
    const uniqueId = packageId + "_" + offset;
    console.log("QUOTATION: TEST - Generated uniqueId:", uniqueId);
    
    // Show preview
    $('#preview_img_' + uniqueId).attr('src', 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAAEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQH/2wBDAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQH/wAARCAABAAEDASIAAhEBAxEB/8QAFQABAQAAAAAAAAAAAAAAAAAAAAv/xAAUEAEAAAAAAAAAAAAAAAAAAAAA/8QAFQEBAQAAAAAAAAAAAAAAAAAAAAX/xAAUEQEAAAAAAAAAAAAAAAAAAAAA/9oADAMBAAIRAxEAPwA/8A8A=').show();
    $('#day_image_preview_' + uniqueId).show().css({
        'display': 'block !important',
        'visibility': 'visible !important',
        'opacity': '1 !important',
        'position': 'relative !important',
        'left': 'auto !important',
        'width': 'auto !important',
        'height': 'auto !important',
        'overflow': 'visible !important'
    });
    
    // Hide upload button using aggressive approach
    setTimeout(function() {
        console.log("QUOTATION: TEST - Using aggressive hide for uniqueId:", uniqueId);
        
        var element = document.getElementById('upload_btn_container_' + uniqueId);
        if (element) {
            var parent = element.parentNode;
            var hiddenElement = element.cloneNode(true);
            hiddenElement.style.cssText = 'display: none !important; visibility: hidden !important; opacity: 0 !important; position: absolute !important; left: -9999px !important; width: 0 !important; height: 0 !important; overflow: hidden !important;';
            hiddenElement.setAttribute('style', 'display: none !important; visibility: hidden !important; opacity: 0 !important; position: absolute !important; left: -9999px !important; width: 0 !important; height: 0 !important; overflow: hidden !important;');
            
            parent.replaceChild(hiddenElement, element);
            
            console.log("QUOTATION: TEST - Aggressive hide completed for uniqueId:", uniqueId);
        } else {
            console.log("QUOTATION: TEST - Element not found for uniqueId:", uniqueId);
        }
    }, 50);
};

</script>
<script src="<?= BASE_URL ?>js/app/footer_scripts.js"></script>