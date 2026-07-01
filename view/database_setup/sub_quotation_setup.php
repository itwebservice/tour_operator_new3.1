<?php
include "../../../model/model.php";
include "../../../model/database_setup/sub_quotation_setup.php";

$sub_quotation_setup = new sub_quotation_setup();
$message = '';
$message_type = 'info';

if (isset($_POST['manual_setup'])) {
    try {
        $result = $sub_quotation_setup->setup_sub_quotation_database();
        if ($result) {
            $message = "Sub-quotation database setup completed successfully!";
            $message_type = 'success';
        } else {
            $message = "Sub-quotation database setup failed. Check error logs for details.";
            $message_type = 'danger';
        }
    } catch (Exception $e) {
        $message = "Error during setup: " . $e->getMessage();
        $message_type = 'danger';
    }
}

// Redirect back to status page with message
header("Location: sub_quotation_status.php?message=" . urlencode($message) . "&type=" . urlencode($message_type));
exit();
?>
