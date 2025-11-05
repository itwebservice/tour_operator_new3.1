<?php
include "../../../../../model/model.php";

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get the data from POST request
$attraction_arr = isset($_POST['attraction_arr']) ? $_POST['attraction_arr'] : [];
$program_arr = isset($_POST['program_arr']) ? $_POST['program_arr'] : [];
$stay_arr = isset($_POST['stay_arr']) ? $_POST['stay_arr'] : [];
$meal_plan_arr = isset($_POST['meal_plan_arr']) ? $_POST['meal_plan_arr'] : [];
$day_image_arr = isset($_POST['day_image_arr']) ? $_POST['day_image_arr'] : [];
$package_p_id_arr = isset($_POST['package_p_id_arr']) ? $_POST['package_p_id_arr'] : [];
$package_id_arr = isset($_POST['package_id_arr']) ? $_POST['package_id_arr'] : [];
$quotation_id = isset($_POST['quotation_id']) ? $_POST['quotation_id'] : '';

// Check if we have any data to save
if (empty($attraction_arr) || empty($program_arr)) {
    echo "error--No itinerary data provided!";
    exit;
}

// Convert arrays if they are strings
if (is_string($attraction_arr)) {
    $attraction_arr = json_decode($attraction_arr, true);
}
if (is_string($program_arr)) {
    $program_arr = json_decode($program_arr, true);
}
if (is_string($stay_arr)) {
    $stay_arr = json_decode($stay_arr, true);
}
if (is_string($meal_plan_arr)) {
    $meal_plan_arr = json_decode($meal_plan_arr, true);
}
if (is_string($package_p_id_arr)) {
    $package_p_id_arr = json_decode($package_p_id_arr, true);
}
if (is_string($package_id_arr)) {
    $package_id_arr = json_decode($package_id_arr, true);
}

// If no quotation_id provided, create a temporary one
if (empty($quotation_id)) {
    $quotation_id = 'temp_' . time() . '_' . rand(1000, 9999);
}

// Also try to get quotation_id from session if available
session_start();
if (isset($_SESSION['current_quotation_id']) && !empty($_SESSION['current_quotation_id'])) {
    $quotation_id = $_SESSION['current_quotation_id'];
    error_log("DEBUG: Using quotation_id from session: " . $quotation_id);
}

// Log the data for debugging
error_log("DEBUG: save_itinerary_data.php called with:");
error_log("quotation_id: " . $quotation_id);
error_log("attraction_arr: " . print_r($attraction_arr, true));
error_log("program_arr: " . print_r($program_arr, true));
error_log("package_id_arr: " . print_r($package_id_arr, true));

// First, delete existing program data for this quotation
$delete_query = "DELETE FROM package_quotation_program WHERE quotation_id = '$quotation_id'";
$delete_result = mysqlQuery($delete_query);
error_log("DEBUG: Delete query result: " . ($delete_result ? "success" : "failed"));

// Insert new program data
$day_count = 0;
$inserted_count = 0;

for ($i = 0; $i < count($attraction_arr); $i++) {
    // Skip empty entries
    if (empty($attraction_arr[$i]) || empty($program_arr[$i]) || empty($stay_arr[$i])) {
        error_log("DEBUG: Skipping empty entry at index $i");
        continue;
    }
    
    $day_count++;
    
    // Use addslashes instead of mysqli_real_escape_string since $con might not be available
    $attraction = addslashes($attraction_arr[$i]);
    $program = addslashes($program_arr[$i]);
    $stay = addslashes($stay_arr[$i]);
    $meal_plan = addslashes($meal_plan_arr[$i]);
    $day_image = isset($day_image_arr[$i]) ? addslashes($day_image_arr[$i]) : '';
    $package_id = addslashes($package_p_id_arr[$i]);
    
    // Ensure package_id is not empty
    if (empty($package_id)) {
        $package_id = '1'; // Default package ID
    }
    
    // Handle image path consistency - ensure ALL images are in quotation_images folder
    if (!empty($day_image) && trim($day_image) !== '' && trim($day_image) !== 'NULL') {
        // Check if this is an original package image from itinerary_images
        if (strpos($day_image, 'uploads/itinerary_images/') === 0) {
            // This is an original package image, copy it to quotation_images folder
            $source_path = "../../../" . $day_image;
            $filename = basename($day_image);
            $new_filename = "quotation_" . $quotation_id . "_day_" . $day_count . "_" . $filename;
            $destination_path = "../../../uploads/quotation_images/" . $new_filename;

            // Create quotation_images directory if it doesn't exist
            $quotation_images_dir = "../../../uploads/quotation_images/";
            if (!file_exists($quotation_images_dir)) {
                if (!mkdir($quotation_images_dir, 0777, true)) {
                    error_log("ERROR: Failed to create quotation_images directory");
                }
            }

            // Copy the file if source exists
            if (file_exists($source_path)) {
                if (copy($source_path, $destination_path)) {
                    $day_image = "uploads/quotation_images/" . $new_filename;
                    error_log("SUCCESS: Copied original image from $source_path to $destination_path");
                } else {
                    error_log("ERROR: Failed to copy original image from $source_path to $destination_path");
                    // Keep original path if copy failed
                }
            } else {
                error_log("WARNING: Original image not found: $source_path");
                // Keep original path if source doesn't exist
            }
        }
        // Also check for images that might be in crm/uploads/itinerary_images/
        elseif (strpos($day_image, 'crm/uploads/itinerary_images/') === 0) {
            // This is an image from crm/uploads/itinerary_images/, copy it to quotation_images folder
            $source_path = "../../../" . $day_image;
            $filename = basename($day_image);
            $new_filename = "quotation_" . $quotation_id . "_day_" . $day_count . "_" . $filename;
            $destination_path = "../../../uploads/quotation_images/" . $new_filename;

            // Create quotation_images directory if it doesn't exist
            $quotation_images_dir = "../../../uploads/quotation_images/";
            if (!file_exists($quotation_images_dir)) {
                if (!mkdir($quotation_images_dir, 0777, true)) {
                    error_log("ERROR: Failed to create quotation_images directory");
                }
            }

            // Copy the file if source exists
            if (file_exists($source_path)) {
                if (copy($source_path, $destination_path)) {
                    $day_image = "uploads/quotation_images/" . $new_filename;
                    error_log("SUCCESS: Copied CRM image from $source_path to $destination_path");
                } else {
                    error_log("ERROR: Failed to copy CRM image from $source_path to $destination_path");
                    // Keep original path if copy failed
                }
            } else {
                error_log("WARNING: CRM image not found: $source_path");
                // Keep original path if source doesn't exist
            }
        }
        // If it's already in quotation_images folder, keep it as is
        // If it's a new image path from upload, keep it as is
    }
    $insert_query = "INSERT INTO package_quotation_program (quotation_id, package_id, attraction, day_wise_program, stay, meal_plan, day_image, day_count) 
                     VALUES ('$quotation_id', '$package_id', '$attraction', '$program', '$stay', '$meal_plan', '$day_image', '$day_count')";
    
    error_log("DEBUG: Inserting program data - Query: " . $insert_query);
    
    $result = mysqlQuery($insert_query);
    if (!$result) {
        error_log("ERROR: Failed to insert program data. Query: " . $insert_query);
        error_log("ERROR: MySQL Error: " . mysqli_error($con));
        echo "error--Program data not saved! Query failed.";
        exit;
    } else {
        $inserted_count++;
        error_log("DEBUG: Successfully inserted program data for day $day_count");
    }
}

error_log("DEBUG: Total inserted records: $inserted_count");

echo "success--Itinerary data saved successfully for quotation ID: " . $quotation_id;
?>
