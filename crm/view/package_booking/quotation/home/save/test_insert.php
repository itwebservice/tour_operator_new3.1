<?php
include "../../../../../model/model.php";

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Test Insert into package_quotation_program</h2>";

// Test a simple insert
$test_quotation_id = 'test_' . time();
$test_package_id = '1';
$test_attraction = 'Test Attraction';
$test_program = 'Test Program';
$test_stay = 'Test Stay';
$test_meal_plan = 'Test Meal Plan';
$test_day_count = 1;

$insert_query = "INSERT INTO package_quotation_program (quotation_id, package_id, attraction, day_wise_program, stay, meal_plan, day_count) 
                 VALUES ('$test_quotation_id', '$test_package_id', '$test_attraction', '$test_program', '$test_stay', '$test_meal_plan', '$test_day_count')";

echo "<p>Testing query: " . htmlspecialchars($insert_query) . "</p>";

$result = mysqlQuery($insert_query);

if ($result) {
    echo "<p style='color: green;'>✓ Test insert successful!</p>";
    
    // Get the inserted record
    $select_query = "SELECT * FROM package_quotation_program WHERE quotation_id = '$test_quotation_id'";
    $select_result = mysqlQuery($select_query);
    
    if ($select_result && mysqli_num_rows($select_result) > 0) {
        $row = mysqli_fetch_assoc($select_result);
        echo "<p>Inserted record:</p>";
        echo "<pre>" . print_r($row, true) . "</pre>";
    }
    
    // Clean up test data
    $delete_query = "DELETE FROM package_quotation_program WHERE quotation_id = '$test_quotation_id'";
    mysqlQuery($delete_query);
    echo "<p>Test data cleaned up.</p>";
    
} else {
    echo "<p style='color: red;'>✗ Test insert failed!</p>";
    echo "<p>Error: " . mysqli_error($con) . "</p>";
}

// Check table structure
echo "<h3>Table Structure:</h3>";
$structure_query = "DESCRIBE package_quotation_program";
$structure_result = mysqlQuery($structure_query);

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

echo "<p><a href='javascript:history.back()'>Go Back</a></p>";
?>
