<?php
/**
 * One-time migration: move B2C CMS images from crm/uploads to crm/images
 * and update database paths. Also migrates quotation testimonial photos to
 * crm/images/quotational_customer_testimonials. Run once via browser or CLI,
 * then remove/restrict access.
 */
include "model/model.php";

$crmRoot = dirname(__FILE__);
$stats = ['moved' => 0, 'skipped' => 0, 'errors' => 0, 'messages' => []];

function b2c_db_path_to_file($crmRoot, $dbPath)
{
    $dbPath = str_replace('\\', '/', $dbPath);
    if (strpos($dbPath, 'http://') === 0 || strpos($dbPath, 'https://') === 0) {
        if (preg_match('#/crm/(.+)$#', $dbPath, $matches)) {
            $dbPath = $matches[1];
        }
    }
    $relative = str_replace('../', '', preg_replace('/(\/+)/', '/', $dbPath));
    return $crmRoot . '/' . ltrim($relative, '/');
}

function b2c_move_image($crmRoot, $oldDbPath, $newDbPath, $force = false)
{
    global $stats;

    if (!$force) {
        if (strpos($oldDbPath, 'images/quotational_customer_testimonials/') !== false) {
            $stats['skipped']++;
            return $oldDbPath;
        }

        if (strpos($oldDbPath, 'images/') !== false && strpos($oldDbPath, 'uploads/') === false) {
            $stats['skipped']++;
            return $oldDbPath;
        }
    }

    $oldFile = b2c_db_path_to_file($crmRoot, $oldDbPath);
    $newFile = b2c_db_path_to_file($crmRoot, $newDbPath);

    if (!file_exists($oldFile)) {
        $stats['errors']++;
        $stats['messages'][] = "Source not found: $oldFile (DB: $oldDbPath)";
        return $oldDbPath;
    }

    $newDir = dirname($newFile);
    if (!is_dir($newDir)) {
        mkdir($newDir, 0777, true);
    }

    if (@rename($oldFile, $newFile)) {
        $stats['moved']++;
        return $newDbPath;
    }

    $stats['errors']++;
    $stats['messages'][] = "Failed to move: $oldFile -> $newFile";
    return $oldDbPath;
}

// --- Banners (b2c_settings.banner_images JSON) ---
$bannerRow = mysqli_fetch_assoc(mysqlQuery("SELECT banner_images FROM b2c_settings WHERE setting_id='1'"));
if (!empty($bannerRow['banner_images']) && $bannerRow['banner_images'] != 'null') {
    $banners = json_decode($bannerRow['banner_images']);
    $updated = false;
    if (is_array($banners)) {
        foreach ($banners as $banner) {
            if (!empty($banner->image_url) && strpos($banner->image_url, 'uploads/b2c_banner_images') !== false) {
                $newPath = str_replace('uploads/b2c_banner_images', 'images/banner', $banner->image_url);
                $banner->image_url = b2c_move_image($crmRoot, $banner->image_url, $newPath);
                $updated = true;
            }
        }
    }
    if ($updated) {
        $json = addslashes(json_encode($banners));
        mysqlQuery("UPDATE b2c_settings SET banner_images='$json' WHERE setting_id='1'");
        $stats['messages'][] = 'Updated b2c_settings.banner_images';
    }
}

// --- Testimonials ---
$testimonialQuery = mysqlQuery("SELECT entry_id, image FROM b2c_testimonials WHERE image LIKE '%uploads/testimonials%'");
while ($row = mysqli_fetch_assoc($testimonialQuery)) {
    $newPath = str_replace('uploads/testimonials', 'images/testimonial', $row['image']);
    $newPath = b2c_move_image($crmRoot, $row['image'], $newPath);
    $newPath = addslashes($newPath);
    mysqlQuery("UPDATE b2c_testimonials SET image='$newPath' WHERE entry_id='{$row['entry_id']}'");
}

// --- Blogs ---
$blogQuery = mysqlQuery("SELECT entry_id, image FROM b2c_blogs WHERE image LIKE '%uploads/call_to_action%'");
while ($row = mysqli_fetch_assoc($blogQuery)) {
    $newPath = str_replace('uploads/call_to_action', 'images/call_to_action', $row['image']);
    $newPath = b2c_move_image($crmRoot, $row['image'], $newPath);
    $newPath = addslashes($newPath);
    mysqlQuery("UPDATE b2c_blogs SET image='$newPath' WHERE entry_id='{$row['entry_id']}'");
}

// --- Team ---
$teamQuery = mysqlQuery("SELECT entry_id, image FROM b2c_team_details WHERE image LIKE '%uploads/testimonials%'");
while ($row = mysqli_fetch_assoc($teamQuery)) {
    $newPath = str_replace('uploads/testimonials', 'images/team', $row['image']);
    $newPath = b2c_move_image($crmRoot, $row['image'], $newPath);
    $newPath = addslashes($newPath);
    mysqlQuery("UPDATE b2c_team_details SET image='$newPath' WHERE entry_id='{$row['entry_id']}'");
}

// --- Quotation testimonials (quotation_testimonial.photo) ---
$quotTestimonialQuery = mysqlQuery("SELECT testimonial_id, photo FROM quotation_testimonial WHERE photo != ''");
while ($row = mysqli_fetch_assoc($quotTestimonialQuery)) {
    $oldPath = $row['photo'];
    $normalized = str_replace('\\', '/', $oldPath);

    if (strpos($normalized, 'http://') === 0 || strpos($normalized, 'https://') === 0) {
        if (preg_match('#/crm/(.+)$#', $normalized, $matches)) {
            $normalized = $matches[1];
        }
    }

    $normalized = preg_replace('#^(\.\./)+#', '', $normalized);
    $normalized = ltrim($normalized, '/');

    if (strpos($normalized, 'images/quotational_customer_testimonials/') === 0) {
        continue;
    }

    $filename = basename($normalized);
    $newPath = 'images/quotational_customer_testimonials/' . $filename;
    $newPath = b2c_move_image($crmRoot, $oldPath, $newPath, true);
    $newPath = addslashes($newPath);
    mysqlQuery("UPDATE quotation_testimonial SET photo='$newPath' WHERE testimonial_id='{$row['testimonial_id']}'");
}

echo "<h2>B2C CMS Image Migration Complete</h2>";
echo "<p>Moved: {$stats['moved']} | Skipped (already migrated): {$stats['skipped']} | Errors: {$stats['errors']}</p>";
if (!empty($stats['messages'])) {
    echo "<ul>";
    foreach ($stats['messages'] as $msg) {
        echo "<li>" . htmlspecialchars($msg) . "</li>";
    }
    echo "</ul>";
}
echo "<p>Re-generate B2C cache from CRM settings after verifying images.</p>";
