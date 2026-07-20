<?php
include "model/model.php";

echo "<h2>Fixing Image Paths in package_quotation_program Table</h2>";

// Find all records with images in itinerary_images folder
$query = "SELECT id, quotation_id, package_id, day_image, day_count 
          FROM package_quotation_program 
          WHERE day_image LIKE 'uploads/itinerary_images/%' 
          AND day_image != '' 
          AND day_image != 'NULL'";

$result = mysqlQuery($query);
$total_records = mysqli_num_rows($result);

echo "<p>Found $total_records records with images in itinerary_images folder</p>";

if ($total_records > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Quotation ID</th><th>Package ID</th><th>Day Count</th><th>Old Path</th><th>New Path</th><th>Status</th></tr>";
    
    $fixed_count = 0;
    $error_count = 0;
    
    while ($row = mysqli_fetch_assoc($result)) {
        $id = $row['id'];
        $quotation_id = $row['quotation_id'];
        $package_id = $row['package_id'];
        $day_count = $row['day_count'];
        $old_path = $row['day_image'];
        
        // Create new filename
        $filename = basename($old_path);
        $new_filename = "quotation_" . $quotation_id . "_" . $filename;
        $new_path = "uploads/quotation_images/" . $new_filename;
        
        // Source and destination file paths
        $source_file = "../../../" . $old_path;
        $dest_file = "../../../" . $new_path;
        
        echo "<tr>";
        echo "<td>$id</td>";
        echo "<td>$quotation_id</td>";
        echo "<td>$package_id</td>";
        echo "<td>$day_count</td>";
        echo "<td>$old_path</td>";
        echo "<td>$new_path</td>";
        
        // Check if source file exists
        if (file_exists($source_file)) {
            // Create quotation_images directory if it doesn't exist
            $quotation_images_dir = "../../../uploads/quotation_images/";
            if (!file_exists($quotation_images_dir)) {
                mkdir($quotation_images_dir, 0777, true);
            }
            
            // Copy the file
            if (copy($source_file, $dest_file)) {
                // Update database with new path
                $update_query = "UPDATE package_quotation_program 
                                SET day_image = '$new_path' 
                                WHERE id = '$id'";
                
                if (mysqlQuery($update_query)) {
                    echo "<td style='color: green;'>✅ FIXED</td>";
                    $fixed_count++;
                } else {
                    echo "<td style='color: red;'>❌ DB UPDATE FAILED</td>";
                    $error_count++;
                }
            } else {
                echo "<td style='color: red;'>❌ FILE COPY FAILED</td>";
                $error_count++;
            }
        } else {
            echo "<td style='color: orange;'>⚠️ SOURCE FILE NOT FOUND</td>";
            $error_count++;
        }
        
        echo "</tr>";
    }
    
    echo "</table>";
    
    echo "<h3>Summary:</h3>";
    echo "<p style='color: green;'>✅ Fixed: $fixed_count records</p>";
    echo "<p style='color: red;'>❌ Errors: $error_count records</p>";
    
} else {
    echo "<p style='color: green;'>✅ No records found with images in itinerary_images folder. All images are already in the correct location!</p>";
}

echo "<br><p><strong>Note:</strong> This script fixes existing data. New quotations will automatically use the correct folder structure.</p>";
?>
