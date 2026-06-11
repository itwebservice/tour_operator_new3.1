<?php
include "../../../model/model.php";
include "../../../model/other_masters/airline_master.php";

$airline_input = isset($_POST['airline_input']) ? $_POST['airline_input'] : '';
$active_flag = isset($_POST['active_flag']) ? $_POST['active_flag'] : 'Active';

$airline_master = new airline_master();
$airline_master->airline_quick_save($airline_input, $active_flag);
?>
