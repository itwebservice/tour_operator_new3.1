<?php 

class quotation_edit
{
    public function quotation_edit_data()
    {
        $quotation_id = $_POST['quotation_id'];
        
        // Get original quotation details
        $result = mysqlQuery("SELECT * FROM package_tour_quotation_master WHERE quotation_id='$quotation_id'");
        $quotation_data = mysqli_fetch_assoc($result);
        
        if (!$quotation_data) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Quotation not found.'
            ]);
            exit;
        }
        
        // Return the quotation data for editing
        echo json_encode([
            'status' => 'success',
            'message' => 'Quotation data loaded for editing',
            'quotation_data' => $quotation_data
        ]);
    }
}

?>
