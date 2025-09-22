<?php
include "../../../model/model.php";

$response = array();

// Enable error logging
error_log("=== Image Upload Debug Start ===");
error_log("POST data: " . print_r($_POST, true));
error_log("FILES data: " . print_r($_FILES, true));

try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
        
        $quotation_id = isset($_POST['quotation_id']) ? $_POST['quotation_id'] : '';
        $package_id = isset($_POST['package_id']) ? $_POST['package_id'] : '';
        $day_number = isset($_POST['day_number']) ? $_POST['day_number'] : '';
        
        // Validate required fields
        if (empty($quotation_id) || empty($package_id) || empty($day_number)) {
            throw new Exception("Missing required parameters");
        }
        
        // Handle temporary quotation IDs - we'll update them later when actual quotation is created
        $is_temp_quotation = (strpos($quotation_id, 'temp_') === 0);
        
        $file = $_FILES['image'];
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileError = $file['error'];
        $fileType = $file['type'];
        
        // Check for upload errors
        if ($fileError !== UPLOAD_ERR_OK) {
            throw new Exception("File upload error: " . $fileError);
        }
        
        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($fileType, $allowedTypes)) {
            throw new Exception("Invalid file type. Only JPEG, PNG, GIF, and WebP images are allowed.");
        }
        
        // Validate file size (max 5MB)
        $maxSize = 5 * 1024 * 1024; // 5MB
        if ($fileSize > $maxSize) {
            throw new Exception("File size too large. Maximum size is 5MB.");
        }
        
        // Create upload directory if it doesn't exist
        $uploadDir = "../../../uploads/quotation_images/";
        error_log("DEBUG: Upload directory path: " . $uploadDir);
        error_log("DEBUG: Current working directory: " . getcwd());
        error_log("DEBUG: Directory exists: " . (file_exists($uploadDir) ? 'YES' : 'NO'));
        error_log("DEBUG: Directory is writable: " . (is_writable($uploadDir) ? 'YES' : 'NO'));
        
        if (!file_exists($uploadDir)) {
            error_log("DEBUG: Creating directory: " . $uploadDir);
            if (!mkdir($uploadDir, 0777, true)) {
                throw new Exception("Failed to create upload directory");
            }
        }
        
        // Generate unique filename
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        $uniqueFileName = "quotation_" . $quotation_id . "_package_" . $package_id . "_day_" . $day_number . "_" . time() . "." . $fileExtension;
        $uploadPath = $uploadDir . $uniqueFileName;
        
        // Move uploaded file
        error_log("DEBUG: Attempting to move file from: " . $fileTmpName . " to: " . $uploadPath);
        error_log("DEBUG: Source file exists: " . (file_exists($fileTmpName) ? 'YES' : 'NO'));
        error_log("DEBUG: Target directory writable: " . (is_writable($uploadDir) ? 'YES' : 'NO'));
        
        if (move_uploaded_file($fileTmpName, $uploadPath)) {
            error_log("DEBUG: File moved successfully to: " . $uploadPath);
            
            // Save to database - Update package_quotation_program table
            $image_url = "uploads/quotation_images/" . $uniqueFileName;
            
            // Include day number in the image URL path for identification
            $image_url_with_day = "uploads/quotation_images/day_" . $day_number . "_" . $uniqueFileName;
            
            // Move the file to include day in filename
            $final_upload_path = $uploadDir . "day_" . $day_number . "_" . $uniqueFileName;
            if (file_exists($uploadPath)) {
                rename($uploadPath, $final_upload_path);
            }
            
            // Update the package_quotation_program table with the image URL
            $update_query = "UPDATE package_quotation_program 
                            SET day_image = '$image_url_with_day' 
                            WHERE quotation_id = '$quotation_id' 
                            AND package_id = '$package_id' 
                            AND day_count = '$day_number'";
            
            error_log("DEBUG: Updating package_quotation_program with query: " . $update_query);
            $result = mysqlQuery($update_query);
            error_log("DEBUG: Database update result: " . ($result ? 'SUCCESS' : 'FAILED'));
            
            if ($result) {
                $response['success'] = true;
                $response['message'] = "Image uploaded successfully";
                $response['image_url'] = BASE_URL . $image_url_with_day;
                $response['day_number'] = $day_number;
            } else {
                throw new Exception("Failed to save image to database");
            }
            
        } else {
            throw new Exception("Failed to move uploaded file");
        }
        
    } else {
        throw new Exception("No image file received");
    }
    
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
    error_log("Itinerary image upload error: " . $e->getMessage());
}

header('Content-Type: application/json');
echo json_encode($response);
?>
