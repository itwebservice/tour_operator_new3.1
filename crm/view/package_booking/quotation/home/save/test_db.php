<?php
include "../../../../../model/model.php";

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Database Connection Test</h2>";

// Test basic database connection
$test_query = "SELECT 1 as test";
$result = mysqlQuery($test_query);

if ($result) {
    echo "<p style='color: green;'>✓ Database connection is working</p>";
    
    // Test if the table exists
    $table_check = "SHOW TABLES LIKE 'package_quotation_program'";
    $table_result = mysqlQuery($table_check);
    
    if (mysqli_num_rows($table_result) > 0) {
        echo "<p style='color: green;'>✓ Table 'package_quotation_program' exists</p>";
        
        // Test table structure
        $structure_query = "DESCRIBE package_quotation_program";
        $structure_result = mysqlQuery($structure_query);
        
        echo "<h3>Table Structure:</h3>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        
        while ($row = mysqli_fetch_assoc($structure_result)) {
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . $row['Default'] . "</td>";
            echo "<td>" . $row['Extra'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
    } else {
        echo "<p style='color: red;'>✗ Table 'package_quotation_program' does not exist</p>";
    }
    
} else {
    echo "<p style='color: red;'>✗ Database connection failed</p>";
}

echo "<p><a href='javascript:history.back()'>Go Back</a></p>";
?>
