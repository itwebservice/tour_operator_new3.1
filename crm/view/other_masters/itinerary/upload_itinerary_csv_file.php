<?php
$year = date("Y");
$month = date("M");
$day = date("d");
$timestamp = date('U');

function check_dir($current_dir, $type)
{
	if (!is_dir($current_dir."/".$type)) {
		mkdir($current_dir."/".$type);
	}
	return $current_dir."/".$type."/";
}

header('Content-Type: text/plain; charset=utf-8');

$current_dir = '../../../uploads/';
$current_dir = check_dir($current_dir , 'itinerary-csv');
$current_dir = check_dir($current_dir , $year);
$current_dir = check_dir($current_dir , $month);
$current_dir = check_dir($current_dir , $day);
$current_dir = check_dir($current_dir , $timestamp);

$original_name = isset($_FILES['uploadfile']['name']) ? $_FILES['uploadfile']['name'] : '';
$safe_name = preg_replace('/[^A-Za-z0-9._-]/', '_', basename($original_name));
if ($safe_name === '' || strtolower(pathinfo($safe_name, PATHINFO_EXTENSION)) !== 'csv') {
	echo "error";
	exit;
}

$filename = $current_dir . $safe_name;

if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], $filename)) {
	echo $filename;
} else {
	echo "error";
}
?>