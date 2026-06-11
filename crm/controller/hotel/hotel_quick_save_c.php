<?php
include "../../model/model.php";
include "../../model/hotel/hotel_master.php";
include "../../model/vendor_login/vendor_login_master.php";

header('Content-Type: application/json');

$city_id = isset($_POST['city_id']) ? $_POST['city_id'] : '';
$hotel_name = isset($_POST['hotel_name']) ? $_POST['hotel_name'] : '';
$state_id = isset($_POST['state_id']) ? $_POST['state_id'] : '';
$rating_star = isset($_POST['rating_star']) ? $_POST['rating_star'] : '';

$hotel_master = new hotel_master();
$hotel_master->hotel_quick_save($city_id, $hotel_name, $state_id, $rating_star);
?>
