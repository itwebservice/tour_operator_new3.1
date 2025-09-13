<?php
// Show all errors
error_reporting(E_ALL);

// Display errors on the page (useful for development)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
include "../../model/model.php";
include "../../model/custom_packages/package_master.php"; 

// Debug: Log the POST data
error_log("Package Master Update - POST Data: " . print_r($_POST, true));

$package_id1 = $_POST['package_id'];
$package_code = $_POST['package_code'];
$package_name = $_POST['package_name'];
$seo_slug = $_POST['seo_slug'];
$tour_theme = $_POST['tour_theme'];
$total_days = $_POST['total_days'];
$total_nights = $_POST['total_nights'];
$adult_cost = $_POST['adult_cost'];
$child_cost = $_POST['child_cost'];
$infant_cost = $_POST['infant_cost'];
$child_with = $_POST['child_with'];
$child_without = $_POST['child_without'];
$extra_bed = $_POST['extra_bed'];
$inclusions = $_POST['inclusions'];
$exclusions = $_POST['exclusions'];
$note = $_POST['note'];
$dest_image = $_POST['dest_image'];
$entry_id_arr = isset($_POST['entry_id_arr']) ? $_POST['entry_id_arr'] : [];
$hotel_entry_id_arr = isset($_POST['hotel_entry_id_arr']) ? $_POST['hotel_entry_id_arr'] : [];
$checked_programe_arr = isset($_POST['checked_programe_arr']) ? $_POST['checked_programe_arr'] : [];

$day_program_arr = isset($_POST['day_program_arr']) ? $_POST['day_program_arr'] : [];
$special_attaraction_arr = isset($_POST['special_attaraction_arr']) ? $_POST['special_attaraction_arr'] : [];
$overnight_stay_arr = isset($_POST['overnight_stay_arr']) ? $_POST['overnight_stay_arr'] : [];
$meal_plan_arr = isset($_POST['meal_plan_arr']) ? $_POST['meal_plan_arr'] : [];

// Debug logging for package update
error_log("Package update debug - package_id: " . $package_id1);
error_log("Package update debug - checked_programe_arr count: " . count($checked_programe_arr));
error_log("Package update debug - day_program_arr count: " . count($day_program_arr));
error_log("Package update debug - special_attaraction_arr count: " . count($special_attaraction_arr));
error_log("Package update debug - entry_id_arr count: " . count($entry_id_arr));
if(!empty($day_program_arr)) {
    error_log("Package update debug - first day program: " . $day_program_arr[0]);
}
if(!empty($checked_programe_arr)) {
    error_log("Package update debug - first checked program: " . $checked_programe_arr[0]);
}

$city_name_arr = isset($_POST['city_name_arr']) ? $_POST['city_name_arr'] : [];
$hotel_name_arr = isset($_POST['hotel_name_arr']) ? $_POST['hotel_name_arr'] : [];
$hotel_type_arr = isset($_POST['hotel_type_arr']) ? $_POST['hotel_type_arr'] : [];
$total_days_arr = isset($_POST['total_days_arr']) ? $_POST['total_days_arr'] : [];
$hotel_check_arr = isset($_POST['hotel_check_arr']) ? $_POST['hotel_check_arr'] : [];
$status = $_POST['status'];

$vehicle_name_arr = isset($_POST['vehicle_name_arr']) ? $_POST['vehicle_name_arr']: [];
$vehicle_check_arr = isset($_POST['vehicle_check_arr']) ? $_POST['vehicle_check_arr'] : [];
$drop_arr = isset($_POST['drop_arr']) ? $_POST['drop_arr'] : [];
$drop_type_arr = isset($_POST['drop_type_arr']) ? $_POST['drop_type_arr'] : [];
$pickup_arr = isset($_POST['pickup_arr']) ? $_POST['pickup_arr'] : [];
$pickup_type_arr = isset($_POST['pickup_type_arr']) ? $_POST['pickup_type_arr'] : [];
$tr_entry_arr = isset($_POST['tr_entry_arr']) ? $_POST['tr_entry_arr'] : [];

$currency_id = $_POST['currency_id'];
$taxation_type = isset($_POST['taxation_type']) ? $_POST['taxation_type'] : '';
$taxation_id = isset($_POST['taxation_id']) ? $_POST['taxation_id'] : '';
$service_tax = isset($_POST['service_tax']) ? $_POST['service_tax'] : '';
$transport_id = isset($_POST['transport_id']) ? $_POST['transport_id'] : '';

try {
    $package_master1 = new custom_package();

    $package_master1->package_master_update($package_id1,$package_code,$package_name,$total_days,$total_nights,$inclusions,$exclusions, $status ,$city_name_arr, $hotel_name_arr,$hotel_type_arr,$total_days_arr,$hotel_check_arr,$vehicle_name_arr,$vehicle_check_arr,$drop_arr,$drop_type_arr,$pickup_arr,$pickup_type_arr,$tr_entry_arr,$checked_programe_arr, $day_program_arr,$special_attaraction_arr,$overnight_stay_arr,$meal_plan_arr,$entry_id_arr,$hotel_entry_id_arr,$adult_cost,$child_cost,$infant_cost,$child_with,$child_without,$extra_bed,$currency_id,$note,$dest_image,$seo_slug,$tour_theme);
} catch (Exception $e) {
    error_log("Package Master Update Error: " . $e->getMessage());
    echo "error--" . $e->getMessage();
}
?>