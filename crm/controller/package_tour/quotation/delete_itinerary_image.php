<?php
include "../../../model/model.php";

$response = array();

try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        
        $quotation_id = isset($_POST['quotation_id']) ? $_POST['quotation_id'] : '';
        $package_id = isset($_POST['package_id']) ? $_POST['package_id'] : '';
        $day_number = isset($_POST['day_number']) ? $_POST['day_number'] : '';
        $image_url = isset($_POST['image_url']) ? $_POST['image_url'] : '';
        
        // Validate required fields
        if (empty($quotation_id) || empty($package_id) || empty($day_number) || empty($image_url)) {
            throw new Exception("Missing required parameters");
        }
        
        // Delete from database
        $delete_query = "DELETE FROM package_tour_quotation_images 
                        WHERE quotation_id = '$quotation_id' 
                        AND package_id = '$package_id' 
                        AND image_url = '$image_url'";
        
        $result = mysqlQuery($delete_query);
        
        if ($result) {
            // Delete physical file
            $file_path = "../../../" . $image_url;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
            
            $response['success'] = true;
            $response['message'] = "Image deleted successfully";
        } else {
            throw new Exception("Failed to delete image from database");
        }
        
    } else {
        throw new Exception("Invalid request method");
    }
    
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
    error_log("Delete itinerary image error: " . $e->getMessage());
}

header('Content-Type: application/json');
echo json_encode($response);
?>
