<?php
include "../../../model/model.php";
include "../../../model/other_masters/airport_master.php";

header('Content-Type: application/json');

$city_id = isset($_POST['city_id']) ? $_POST['city_id'] : '';
$airport_name = isset($_POST['airport_name']) ? $_POST['airport_name'] : '';
$airport_code = isset($_POST['airport_code']) ? $_POST['airport_code'] : '';

$airport_master = new airport_master();
$airport_master->airport_quick_save($city_id, $airport_name, $airport_code);
