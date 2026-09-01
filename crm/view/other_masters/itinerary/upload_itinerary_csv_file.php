<?php
$year = date("Y");
$month = date("M");
$day = date("d");
$timestamp = date('U');

function itinerary_csv_check_dir($current_dir, $type)
{
	if (!is_dir($current_dir)) {
		@mkdir($current_dir, 0777, true);
	}
	$target = rtrim($current_dir, '/\\') . DIRECTORY_SEPARATOR . $type;
	if (!is_dir($target)) {
		@mkdir($target, 0777, true);
	}
	return $target . DIRECTORY_SEPARATOR;
}

header('Content-Type: text/plain; charset=utf-8');

if (!isset($_FILES['uploadfile']) || (int)$_FILES['uploadfile']['error'] !== UPLOAD_ERR_OK) {
	echo "error";
	exit;
}

$crm_root = realpath(__DIR__ . '/../../..');
if ($crm_root === false) {
	echo "error";
	exit;
}

$current_dir = $crm_root . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;
$current_dir = itinerary_csv_check_dir($current_dir, 'itinerary-csv');
$current_dir = itinerary_csv_check_dir($current_dir, $year);
$current_dir = itinerary_csv_check_dir($current_dir, $month);
$current_dir = itinerary_csv_check_dir($current_dir, $day);
$current_dir = itinerary_csv_check_dir($current_dir, $timestamp);

$original_name = isset($_FILES['uploadfile']['name']) ? $_FILES['uploadfile']['name'] : '';
$safe_name = preg_replace('/[^A-Za-z0-9._-]/', '_', basename($original_name));
if ($safe_name === '' || strtolower(pathinfo($safe_name, PATHINFO_EXTENSION)) !== 'csv') {
	echo "error";
	exit;
}

$filename = $current_dir . $safe_name;

if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], $filename)) {
	$normalized = str_replace('\\', '/', $filename);
	$pos = stripos($normalized, '/uploads/');
	if ($pos !== false) {
		echo 'uploads' . substr($normalized, $pos + strlen('/uploads'));
	} else {
		echo $normalized;
	}
} else {
	echo "error";
}
?>