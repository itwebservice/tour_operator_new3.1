<?php
include "../../../model/model.php"; 
include "../../../model/package_tour/quotation/quotation_email_send.php"; 

$quotation_id = $_POST['quotation_id'];
$email_id = $_POST['email_id'];
$email_option = $_POST['email_option'];
$options = isset($_POST['options']) && !empty($_POST['options']) ? $_POST['options'] : array();

// Debug: Log what we received
error_log("Controller - Received POST options: " . print_r($_POST['options'], true));
error_log("Controller - Processed options: " . print_r($options, true));

$quotation_email_send = new quotation_email_send;
if($email_option=='By HTML'){
    $quotation_email_send->quotation_email_individual($quotation_id, $email_id);	
}else{
    // print_r($options);die;
    $quotation_email_send->quotation_email_body_individual($quotation_id, $email_id, $options);
}
?>
