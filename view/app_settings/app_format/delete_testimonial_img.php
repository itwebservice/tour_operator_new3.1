<?php
include "../../../model/model.php";

$image = isset($_POST['image']) ? $_POST['image'] : '';
$image = str_replace('\\', '/', trim($image));

if ($image === '') {
    exit;
}

$crm_root = dirname(__DIR__, 3);
$file = '';

$known_paths = array(
    'images/quotational_customer_testimonials/',
    'images/testimonial/',
    'uploads/testimonials/',
);

foreach ($known_paths as $path_prefix) {
    if (strpos($image, $path_prefix) !== false) {
        $relative = substr($image, strpos($image, $path_prefix));
        $file = $crm_root . '/' . $relative;
        break;
    }
}

if ($file === '' && (strpos($image, 'http://') === 0 || strpos($image, 'https://') === 0)) {
    $base_path = parse_url(BASE_URL, PHP_URL_PATH);
    $pos = strpos($image, $base_path);
    if ($pos !== false) {
        $relative = ltrim(substr($image, $pos + strlen($base_path)), '/');
        $file = $crm_root . '/' . $relative;
    }
}

if ($file !== '' && file_exists($file)) {
    unlink($file);
}
