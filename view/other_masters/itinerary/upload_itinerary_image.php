<?php
// Minimal upload handler for itinerary images
// Returns either: "error--<message>" or a relative path like "uploads/itinerary_images/<filename>"

// Allow only POST with file
if (!isset($_FILES['uploadfile'])) {
    echo "error--No file uploaded";
    exit;
}

$file      = $_FILES['uploadfile'];
$errorCode = isset($file['error']) ? (int)$file['error'] : UPLOAD_ERR_NO_FILE;

if ($errorCode !== UPLOAD_ERR_OK) {
    echo "error--Upload error code: " . $errorCode;
    exit;
}

$originalName = $file['name'];
$tmpPath      = $file['tmp_name'];
$sizeBytes    = (int)$file['size'];

// Basic validations
$allowedExts = array('jpg','jpeg','png','webp');
$maxSize     = 5 * 1024 * 1024; // 5MB

$ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
if (!in_array($ext, $allowedExts, true)) {
    echo "error--Only JPG, JPEG, PNG, WEBP files are allowed";
    exit;
}

if ($sizeBytes <= 0 || $sizeBytes > $maxSize) {
    echo "error--File size must be between 1 byte and 5MB";
    exit;
}

// Destination paths
$projectRoot = dirname(__DIR__, 4); // /var/www/html/itoursdemo
$relativeDir = 'uploads/itinerary_images/';
$targetDir   = rtrim($projectRoot, '/').'/'.$relativeDir;

if (!is_dir($targetDir)) {
    if (!mkdir($targetDir, 0755, true)) {
        echo "error--Failed to create upload directory";
        exit;
    }
}

if (!is_writable($targetDir)) {
    echo "error--Upload directory is not writable";
    exit;
}

// Generate unique filename
$safeBase   = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
$uniqueName = date('Ymd_His').'_'.bin2hex(random_bytes(4)).'_'.$safeBase.'.'.$ext;
$destPath   = $targetDir.$uniqueName;

if (!move_uploaded_file($tmpPath, $destPath)) {
    echo "error--Failed to move uploaded file";
    exit;
}

// Success: return relative path to be stored in DB
echo $relativeDir.$uniqueName;
exit;
?>


