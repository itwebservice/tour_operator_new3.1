<?php
include "../../../../model/model.php";

$dest_id = $_POST['dest_id'];
if (!$dest_id) {
    echo '';
    exit;
}

// Get destination name
$sq_dest = mysqli_fetch_assoc(mysqlQuery("SELECT dest_name FROM destination_master WHERE dest_id='$dest_id'"));
$dest_name = preg_replace('/\s+/', '', strtolower($sq_dest['dest_name']));
$prefix = substr($dest_name, 0, 3); // take up to 3 letters

// Find the highest package code with this prefix
$sq_package = mysqli_fetch_assoc(mysqlQuery("
    SELECT package_code FROM custom_package_master 
    WHERE package_code LIKE '{$prefix}%' 
    ORDER BY CAST(SUBSTRING(package_code, LENGTH('{$prefix}') + 1) AS UNSIGNED) DESC 
    LIMIT 1
"));

if (!empty($sq_package['package_code'])) {
    // Extract numeric suffix
    preg_match('/(\d{3})$/', $sq_package['package_code'], $matches);
    $last_number = isset($matches[1]) ? intval($matches[1]) : 0;
    $next_number = $last_number + 1;
} else {
    $next_number = 1;
}

// Generate next code
$next_code = $prefix . str_pad($next_number, 3, '0', STR_PAD_LEFT);
echo $next_code;
?>
