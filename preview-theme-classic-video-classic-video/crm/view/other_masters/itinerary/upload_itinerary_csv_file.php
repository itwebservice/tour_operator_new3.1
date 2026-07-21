<?php
$year = date("Y");
$month = date("M");
$day = date("d");
$timestamp = date('U');

function check_dir($current_dir, $type)
{
    $next_dir = rtrim($current_dir, '/') . '/' . $type;
    if (!is_dir($next_dir)) {
        mkdir($next_dir, 0777, true);
    }
    return $next_dir;
}

// Always build path from this file location (not current working directory).
$base_upload_dir = dirname(__DIR__, 3) . '/uploads';
$current_dir = $base_upload_dir;
$current_dir = check_dir($current_dir, 'itinerary-csv');
$current_dir = check_dir($current_dir, $year);
$current_dir = check_dir($current_dir, $month);
$current_dir = check_dir($current_dir, $day);
$current_dir = check_dir($current_dir, $timestamp);

$original_name = basename($_FILES['uploadfile']['name']);
$safe_name = str_replace(' ', '_', $original_name);
$filename = rtrim($current_dir, '/') . '/' . $safe_name;

if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], $filename)) {
    // Return absolute path so csv_save can fopen reliably.
    echo $filename;
} else {
    echo "error";
}
?>