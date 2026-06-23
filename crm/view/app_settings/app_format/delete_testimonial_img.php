<?php
include "../../../model/model.php";

$image = isset($_POST['image']) ? $_POST['image'] : '';
$image = str_replace('\\', '/', $image);

$pos = strpos($image, 'uploads/testimonials/');
if ($pos === false) {
  exit;
}

$relative = substr($image, $pos);
$file = $_SERVER['DOCUMENT_ROOT'] . '/tour_operator_new3.1/crm/' . $relative;

if (file_exists($file)) {
  unlink($file);
}
