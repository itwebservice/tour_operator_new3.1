<?php
include_once("../../../model/model.php");

$entry_id = $_POST['entry_id'];
$image_url = $_POST['image_url'];

if ($image_url != '') {
    // Convert the relative path to an absolute path
    $image_path = str_replace(BASE_URL, '../', $image_url);

    if (file_exists($image_path)) {
        unlink($image_path); // Delete the image file
    }

    // Remove image reference from the database
    $query = mysqlQuery("UPDATE b2c_team_details SET image='' WHERE entry_id='$entry_id'");

    if ($query) {
        echo "success--Image deleted successfully!";
    } else {
        echo "error--Failed to update the database!";
    }
} else {
    echo "error--No image found!";
}
?>
