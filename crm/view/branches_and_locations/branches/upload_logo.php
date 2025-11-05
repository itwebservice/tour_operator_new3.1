<?php

$year = date("Y");
$month = date("M");
$day = date("d");
$timestamp = date('U');

function check_dir($current_dir, $type)
{	 	
	$new_dir = $current_dir."/".$type;
	if(!is_dir($new_dir))
	{
		if(!mkdir($new_dir, 0777, true)){
			error_log("Failed to create directory: " . $new_dir);
			return false;
		}
	}	
	$current_dir = $new_dir."/";
	return $current_dir;	
}

// Check if uploadfileLOGO is set
if(!isset($_FILES['uploadfileLOGO']) || $_FILES['uploadfileLOGO']['error'] == UPLOAD_ERR_NO_FILE){
	echo "error--No file uploaded";
	exit;
}

$current_dir = '../../../uploads/';

// Check if main uploads directory exists, if not create it
if(!is_dir($current_dir)){
	mkdir($current_dir, 0777, true);
}

$current_dir = check_dir($current_dir, 'branch_logo');
if($current_dir === false){
	echo "error--Failed to create branch_logo directory";
	exit;
}

$current_dir = check_dir($current_dir, $year);
if($current_dir === false){
	echo "error--Failed to create year directory";
	exit;
}

$current_dir = check_dir($current_dir, $month);
if($current_dir === false){
	echo "error--Failed to create month directory";
	exit;
}

$current_dir = check_dir($current_dir, $day);
if($current_dir === false){
	echo "error--Failed to create day directory";
	exit;
}

$current_dir = check_dir($current_dir, $timestamp);
if($current_dir === false){
	echo "error--Failed to create timestamp directory";
	exit;
}

$file_name = str_replace(' ','_',basename($_FILES['uploadfileLOGO']['name']));
$file = $current_dir.$file_name; 

// Maximum size 100KB
if($_FILES['uploadfileLOGO']['size'] > 100000){
	echo "error1";
	exit;
}

if(!move_uploaded_file($_FILES['uploadfileLOGO']['tmp_name'], $file)) { 
	echo "error--Failed to move uploaded file";
	exit;
}

echo $file;

?>

