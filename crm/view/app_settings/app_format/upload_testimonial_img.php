<?php
include "../../../model/model.php";

$upload_dir = BASE_URL . 'uploads/testimonials/';
$root_dir = $_SERVER['DOCUMENT_ROOT'] . '/tour_operator_new3.1/crm/uploads/testimonials/';

if (!file_exists($root_dir)) {
  mkdir($root_dir, 0777, true);
}

if (!isset($_FILES['uploadfile'])) {
  echo "error--No file uploaded!";
  exit;
}

$file_name = $_FILES['uploadfile']['name'];
$tmp_name = $_FILES['uploadfile']['tmp_name'];
$ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

if (!in_array($ext, array('jpg', 'jpeg', 'png'))) {
  echo "error--Only JPG,JPEG,PNG files are allowed!";
  exit;
}

$new_name = 'testimonial_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
$target = $root_dir . $new_name;

if (move_uploaded_file($tmp_name, $target)) {
  echo $upload_dir . $new_name;
} else {
  echo "error--Image upload failed!";
}
