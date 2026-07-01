<?php
include "../../../model/model.php";
include "../../../model/other_masters/room_category_master.php";

$room_category = isset($_POST['room_category']) ? $_POST['room_category'] : '';
$active_status = isset($_POST['active_status']) ? $_POST['active_status'] : 'Active';

$room_category_master = new room_category_master();
$room_category_master->category_quick_save($room_category, $active_status);
?>
