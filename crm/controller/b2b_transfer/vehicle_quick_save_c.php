<?php
include_once('../../model/model.php');
include_once('../../model/generic_vehicle_save.php');

$vehicle_name = isset($_POST['vehicle_name']) ? $_POST['vehicle_name'] : '';
$vehicle_type = isset($_POST['vehicle_type']) ? $_POST['vehicle_type'] : 'Private Car';
$seating_capacity = isset($_POST['seating_capacity']) ? $_POST['seating_capacity'] : '4';
$status = isset($_POST['status']) ? $_POST['status'] : 'Active';

$vehicle = new vehicle_master();
$vehicle->vehicle_quick_save($vehicle_name, $vehicle_type, $seating_capacity, $status);
?>
