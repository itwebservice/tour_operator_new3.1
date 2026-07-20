<?php
include "../../../model/model.php";

$crm_root = dirname(__DIR__, 3);
$storage_dir = $crm_root . '/images/quotational_customer_testimonials/';

if (!file_exists($storage_dir)) {
    mkdir($storage_dir, 0777, true);
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

$new_name = 'quot_testimonial_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
$target = $storage_dir . $new_name;
$db_path = 'images/quotational_customer_testimonials/' . $new_name;

if (move_uploaded_file($tmp_name, $target)) {
    $testimonial_id = isset($_POST['testimonial_id']) ? (int) $_POST['testimonial_id'] : 0;
    if ($testimonial_id > 0) {
        $photo = mysqlREString($db_path);
        mysqlQuery("UPDATE quotation_testimonial SET photo='$photo' WHERE testimonial_id='$testimonial_id'");
    }
    echo $db_path;
} else {
    echo "error--Image upload failed!";
}
