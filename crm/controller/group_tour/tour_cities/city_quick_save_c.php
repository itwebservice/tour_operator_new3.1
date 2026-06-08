<?php
include "../../../model/model.php";
include "../../../model/group_tour/tour_cities/city_master.php";

$city_name = isset($_POST['city_name']) ? $_POST['city_name'] : '';
$active_flag = isset($_POST['active_flag']) ? $_POST['active_flag'] : 'Active';

$city_master = new city_master();
$city_master->city_quick_save($city_name, $active_flag);
?>
