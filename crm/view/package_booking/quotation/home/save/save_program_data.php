<?php
include "../../../../../model/model.php";

$quotation_id = $_POST['quotation_id'];
$attraction_arr = $_POST['attraction_arr'];
$program_arr = $_POST['program_arr'];
$stay_arr = $_POST['stay_arr'];
$meal_plan_arr = $_POST['meal_plan_arr'];
$package_p_id_arr = $_POST['package_p_id_arr'];
$day_count_arr = $_POST['day_count_arr'];

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
if (is_string($day_count_arr)) {
    $day_count_arr = json_decode($day_count_arr, true);
}

// First, delete existing program data for this quotation
$delete_query = "DELETE FROM package_quotation_program WHERE quotation_id = '$quotation_id'";
mysqlQuery($delete_query);

// Insert new program data
$day_count = 0;
for ($i = 0; $i < count($attraction_arr); $i++) {
    $day_count++;
    
    $attraction = mysqli_real_escape_string($con, $attraction_arr[$i]);
    $program = mysqli_real_escape_string($con, $program_arr[$i]);
    $stay = mysqli_real_escape_string($con, $stay_arr[$i]);
    $meal_plan = mysqli_real_escape_string($con, $meal_plan_arr[$i]);
    $package_id = mysqli_real_escape_string($con, $package_p_id_arr[$i]);
    
    $insert_query = "INSERT INTO package_quotation_program (quotation_id, package_id, attraction, day_wise_program, stay, meal_plan, day_count) 
                     VALUES ('$quotation_id', '$package_id', '$attraction', '$program', '$stay', '$meal_plan', '$day_count')";
    
    mysqlQuery($insert_query);
}

echo "Program data saved successfully for quotation ID: " . $quotation_id;
?>
