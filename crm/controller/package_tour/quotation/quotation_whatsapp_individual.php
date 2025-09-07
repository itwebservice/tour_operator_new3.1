<?php 
include "../../../model/model.php"; 
include "../../../model/package_tour/quotation/quotation_email_send.php"; 

$quotation_id = $_POST['quotation_id'];
$mobile_no = $_POST['mobile_no'];
$options = isset($_POST['options']) && !empty($_POST['options']) ? $_POST['options'] : array();

$quotation_whatsapp = new quotation_email_send;
$quotation_whatsapp->quotation_whatsapp_individual($quotation_id, $mobile_no, $options);
?>
