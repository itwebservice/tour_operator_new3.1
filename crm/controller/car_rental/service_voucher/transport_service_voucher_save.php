<?php 
include_once('../../../model/model.php');
include_once('../../../model/car_rental/service_voucher/transport_service_voucher.php');

$transport_service_voucher = new car_transport_service_voucher;
$transport_service_voucher->transport_voucher_save();
?>