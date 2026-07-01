<?php
include "../../../model/model.php";
include "../../../model/other_masters/destination_master.php";

$dest_name = isset($_POST['dest_name']) ? $_POST['dest_name'] : '';
$status = isset($_POST['status']) ? $_POST['status'] : 'Active';

$destination_master = new destination_master();
$destination_master->destination_quick_save($dest_name, $status);
?>
