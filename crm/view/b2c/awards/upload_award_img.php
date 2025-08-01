<?php
$year = date("Y");
$month = date("M");
$day = date("d");
$timestamp = date('U');

function check_dir($current_dir, $type)
{   
    if(!is_dir($current_dir."/".$type))
    {
        mkdir($current_dir."/".$type);       
    }   
    $current_dir = $current_dir."/".$type."/";
    return $current_dir; 
}

$current_dir = '../../../uploads/';
$current_dir = check_dir($current_dir ,'testimonials');
$current_dir = check_dir($current_dir , $year);
$current_dir = check_dir($current_dir , $month);
$current_dir = check_dir($current_dir , $day);
$current_dir = check_dir($current_dir , $timestamp);

$file = $current_dir . basename($_FILES['uploadfile']['name']);

// Get image dimensions
$image_info = getimagesize($_FILES['uploadfile']['tmp_name']);
if ($image_info === false) {
    echo "error--File is not a valid image.";
    exit;
}

// Check if image is exactly 200x200 pixels
list($width, $height) = $image_info;
if ($width != 200 || $height != 200) {
    echo "error--Image dimensions must be 200x200 pixels.";
    exit;
}

// Check file size
if ($_FILES['uploadfile']['size'] < 100000) {
    if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], $file)) { 
        echo $file; 
    } else {
        echo "error--File is not uploaded.";
    }
} else {
    echo "error--File Size Limit Exceeded.";
}
?>
