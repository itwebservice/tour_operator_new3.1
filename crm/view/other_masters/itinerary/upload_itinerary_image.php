<?php
// Returns either: "error--<message>" or a relative path like "uploads/itinerary_images/<filename>"

header('Content-Type: text/plain; charset=utf-8');

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

$allowedExts = array('jpg','jpeg','png','webp');
$maxSize     = 5 * 1024 * 1024;

$ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
if (!in_array($ext, $allowedExts, true)) {
    echo "error--Only JPG, JPEG, PNG, WEBP files are allowed";
    exit;
}

if ($sizeBytes <= 0 || $sizeBytes > $maxSize) {
    echo "error--File size must be between 1 byte and 5MB";
    exit;
}

$relativeDir = 'uploads/itinerary_images/';
$candidateRoots = array();
$projectRoot = dirname(__DIR__, 4);
$crmRoot = dirname(__DIR__, 3);
if ($projectRoot) {
    $candidateRoots[] = rtrim(str_replace('\\', '/', $projectRoot), '/');
}
if ($crmRoot) {
    $candidateRoots[] = rtrim(str_replace('\\', '/', $crmRoot), '/');
}
$candidateRoots = array_values(array_unique($candidateRoots));

$targetDir = '';
foreach ($candidateRoots as $root) {
    $dir = $root . '/' . $relativeDir;
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
    if (is_dir($dir) && is_writable($dir)) {
        $targetDir = $dir;
        break;
    }
}

if ($targetDir === '') {
    echo "error--Failed to create upload directory";
    exit;
}

$safeBase   = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
$uniqueName = date('Ymd_His').'_'.bin2hex(random_bytes(4)).'_'.$safeBase.'.'.$ext;
$destPath   = $targetDir.$uniqueName;

if (!move_uploaded_file($tmpPath, $destPath)) {
    echo "error--Failed to move uploaded file";
    exit;
}

echo $relativeDir.$uniqueName;
exit;
?>
