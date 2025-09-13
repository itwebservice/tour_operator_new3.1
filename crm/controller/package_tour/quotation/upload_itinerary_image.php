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
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Generate unique filename
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        $uniqueFileName = "quotation_" . $quotation_id . "_package_" . $package_id . "_day_" . $day_number . "_" . time() . "." . $fileExtension;
        $uploadPath = $uploadDir . $uniqueFileName;
        
        // Move uploaded file
        if (move_uploaded_file($fileTmpName, $uploadPath)) {
            
            // Save to database
            $image_url = "uploads/quotation_images/" . $uniqueFileName;
            
            // Insert new record (following existing table structure)
            $sq_max = mysqli_fetch_assoc(mysqlQuery("SELECT MAX(id) as max FROM package_tour_quotation_images"));
            $image_id = $sq_max['max'] + 1;
            
            // Include day number in the image URL path for identification
            $image_url_with_day = "uploads/quotation_images/day_" . $day_number . "_" . $uniqueFileName;
            
            // Move the file to include day in filename
            $final_upload_path = $uploadDir . "day_" . $day_number . "_" . $uniqueFileName;
            if (file_exists($uploadPath)) {
                rename($uploadPath, $final_upload_path);
            }
            
            $insert_query = "INSERT INTO package_tour_quotation_images 
                           (id, quotation_id, package_id, image_url) 
                           VALUES ('$image_id', '$quotation_id', '$package_id', '$image_url_with_day')";
            $result = mysqlQuery($insert_query);
            
            if ($result) {
                $response['success'] = true;
                $response['message'] = "Image uploaded successfully";
                $response['image_url'] = BASE_URL . $image_url_with_day;
                $response['image_id'] = $image_id;
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
