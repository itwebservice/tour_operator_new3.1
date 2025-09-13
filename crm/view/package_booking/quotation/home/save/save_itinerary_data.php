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
    $package_id = addslashes($package_p_id_arr[$i]);
    
    // Ensure package_id is not empty
    if (empty($package_id)) {
        $package_id = '1'; // Default package ID
    }
    
    $insert_query = "INSERT INTO package_quotation_program (quotation_id, package_id, attraction, day_wise_program, stay, meal_plan, day_count) 
                     VALUES ('$quotation_id', '$package_id', '$attraction', '$program', '$stay', '$meal_plan', '$day_count')";
    
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
