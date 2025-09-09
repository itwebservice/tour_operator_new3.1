<?php
// Database update script for quotation_display_id column
// Run this script once to add the new column and populate existing data

include 'config.php';

try {
    // Add quotation_display_id column
    $sql1 = "ALTER TABLE package_tour_quotation_master 
             ADD COLUMN quotation_display_id VARCHAR(50) DEFAULT NULL AFTER quotation_id";
    
    if (mysqlQuery($sql1)) {
        echo "✅ Added quotation_display_id column successfully\n";
    } else {
        echo "❌ Error adding quotation_display_id column\n";
    }
    
    // Add index for better performance
    $sql2 = "ALTER TABLE package_tour_quotation_master 
             ADD INDEX idx_quotation_display_id (quotation_display_id)";
    
    if (mysqlQuery($sql2)) {
        echo "✅ Added index for quotation_display_id successfully\n";
    } else {
        echo "❌ Error adding index (may already exist)\n";
    }
    
    // Update existing quotations to have their display IDs
    $sql3 = "UPDATE package_tour_quotation_master 
             SET quotation_display_id = CONCAT('QTN/', YEAR(quotation_date), '/', quotation_id)
             WHERE quotation_display_id IS NULL";
    
    $result = mysqlQuery($sql3);
    if ($result) {
        $affected_rows = mysqli_affected_rows($GLOBALS['con']);
        echo "✅ Updated $affected_rows existing quotations with display IDs\n";
    } else {
        echo "❌ Error updating existing quotations\n";
    }
    
    echo "\n🎉 Database update completed successfully!\n";
    echo "You can now use the sub-quotation feature with proper display IDs.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
