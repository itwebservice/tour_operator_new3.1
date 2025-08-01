<?php
include '../../../config.php';

require('razorpay-php-2.9.0/Razorpay.php');

use Razorpay\Api\Api;

$api = new Razorpay\Api\Api($apiKey, $apiSecret);

$input = json_decode(file_get_contents("php://input"), true);

$paymentId = $input['paymentId'];
$orderId = $input['orderId'];
$signature = $input['signature'];
$bookingId = $input['bookingId'];
$payment_amount=$input['amount'];
try {
    $attributes = [
        'razorpay_order_id' => $orderId,
        'razorpay_payment_id' => $paymentId,
        'razorpay_signature' => $signature
    ];
    $api->utility->verifyPaymentSignature($attributes);
    $query = "SELECT `financial_year_id` FROM `financial_year` WHERE CURDATE() BETWEEN `from_date` AND `to_date`";
    $result = mysqli_query($connection, $query);
    $financial_year_id=0;
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $financial_year_id = $row['financial_year_id'];
        // Use $financial_year_id as needed
    }
        $payment_date=date('Y-m-d');
        $financial_year_id = $financial_year_id; // Example financial year ID
        $branch_admin_id = 1; // Example branch admin ID
        $module_name = 'Air Ticket Booking,Air Ticket Booking Payment'; // Example module name
        $module_entry_id = 'NULL'; // Example module entry ID (could be from a related table)
        $gl_id = 'NULL'; // Example general ledger ID
        $payment_amount = $payment_amount; // Example payment amount
        $payment_date = $payment_date; // Example payment date (current date)
        $payment_particular = 'Flight Booking Payment'; // Example payment particular
        $transaction_id = $paymentId; // Example transaction ID
        $payment_side = 'customer_master ledger payment side credit, bank debit side'; // Payment side (Debit or Credit)
        $clearance_status = 'Cleared'; // Example clearance status
        $row_specification = 'sales'; // Row specification (if applicable)
        $ledger_particular = 'To Flight Ticket Sales ,By Cash/Bank'; // Ledger description
        $created_at = date('Y-m-d H:i:s'); // Date and time of creation
        $type = 'INVOICE'; // Type of transaction (e.g., Credit or Debit)
        $tax_type = 1; // Example tax type (e.g., GST or VAT)
        $finance_transaction_master_query = "INSERT INTO finance_transaction_master 
                (financial_year_id, branch_admin_id, module_name, module_entry_id, gl_id, 
                payment_amount, payment_date, payment_particular, transaction_id, 
                payment_side, clearance_status, row_specification, ledger_particular, created_at, 
                type, tax_type)
                VALUES 
                ($financial_year_id, $branch_admin_id, '$module_name', $module_entry_id, $gl_id, 
                $payment_amount, '$payment_date', '$payment_particular', '$transaction_id', 
                '$payment_side', '$clearance_status', '$row_specification', '$ledger_particular', '$created_at', 
                '$type', $tax_type)";
                if (mysqli_query($connection, $finance_transaction_master_query)) 
                {
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Payment verified successfully.'
                    ]);
                }
                else
                {
                    $returndata=array(
                         "status"=>"error",
                         "message"=>$queryError
                    );
                    echo json_encode($returndata);
                }

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Payment verification failed: ' . $e->getMessage()
    ]);
}
?>
