<?php
$year = date("Y");
$month = date("M");
$day = date("d");
$timestamp = date('U');

function b2c_check_dir($current_dir, $type)
{
    if (!is_dir($current_dir . "/" . $type)) {
        mkdir($current_dir . "/" . $type, 0777, true);
    }
    return $current_dir . "/" . $type . "/";
}

$crm_root = dirname(__DIR__, 3);
$storage_dir = b2c_check_dir($crm_root . '/images', 'banner');
$storage_dir = b2c_check_dir(rtrim($storage_dir, '/'), $year);
$storage_dir = b2c_check_dir(rtrim($storage_dir, '/'), $month);
$storage_dir = b2c_check_dir(rtrim($storage_dir, '/'), $day);
$storage_dir = b2c_check_dir(rtrim($storage_dir, '/'), $timestamp);

$filename = basename($_FILES['uploadfile']['name']);
$absolute_file = $storage_dir . $filename;
$db_path = '../../../images/banner/' . $year . '/' . $month . '/' . $day . '/' . $timestamp . '/' . $filename;

if ($_FILES['uploadfile']['size'] < 1 * 1048576) {
    if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], $absolute_file)) {
        echo $db_path;
    } else {
        echo "error--File is not uploaded.";
    }
} else {
    echo "error--File Size Limit Exceeded.";
}
