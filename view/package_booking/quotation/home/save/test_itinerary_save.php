<?php
include "../../../../../model/model.php";

echo "<h2>Test Itinerary Save Functionality</h2>";

// Test database connection
echo "<h3>1. Database Connection Test</h3>";
if ($conn) {
    echo "✓ Database connection successful<br>";
    echo "Database: " . $conn->database . "<br>";
} else {
    echo "✗ Database connection failed<br>";
    exit;
}

// Check if table exists
echo "<h3>2. Table Existence Check</h3>";
$table_check = mysqlQuery("SHOW TABLES LIKE 'package_quotation_program'");
if (mysqli_num_rows($table_check) > 0) {
    echo "✓ Table 'package_quotation_program' exists<br>";
} else {
    echo "✗ Table 'package_quotation_program' does not exist<br>";
    exit;
}

// Check current records
echo "<h3>3. Current Records in Table</h3>";
$count_query = mysqlQuery("SELECT COUNT(*) as total FROM package_quotation_program");
$count_result = mysqli_fetch_assoc($count_query);
echo "Total records: " . $count_result['total'] . "<br>";

// Show recent records
echo "<h3>4. Recent Records (Last 5)</h3>";
$recent_query = mysqlQuery("SELECT * FROM package_quotation_program ORDER BY id DESC LIMIT 5");
echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>ID</th><th>Quotation ID</th><th>Package ID</th><th>Attraction</th><th>Day Program</th><th>Stay</th><th>Meal Plan</th><th>Day Count</th></tr>";

while ($row = mysqli_fetch_assoc($recent_query)) {
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . $row['quotation_id'] . "</td>";
    echo "<td>" . $row['package_id'] . "</td>";
    echo "<td>" . htmlspecialchars($row['attraction']) . "</td>";
    echo "<td>" . htmlspecialchars(substr($row['day_wise_program'], 0, 50)) . "...</td>";
    echo "<td>" . htmlspecialchars($row['stay']) . "</td>";
    echo "<td>" . htmlspecialchars($row['meal_plan']) . "</td>";
    echo "<td>" . $row['day_count'] . "</td>";
    echo "</tr>";
}
echo "</table>";

// Test insert functionality
echo "<h3>5. Test Insert Functionality</h3>";
$test_quotation_id = 'test_' . time();
$test_package_id = '1';
$test_attraction = 'Test Attraction';
$test_program = 'Test Program';
$test_stay = 'Test Stay';
$test_meal_plan = 'Test Meal Plan';
$test_day_count = 1;

$insert_query = "INSERT INTO package_quotation_program (quotation_id, package_id, attraction, day_wise_program, stay, meal_plan, day_count) 
                 VALUES ('$test_quotation_id', '$test_package_id', '$test_attraction', '$test_program', '$test_stay', '$test_meal_plan', '$test_day_count')";

echo "Testing insert query: " . htmlspecialchars($insert_query) . "<br>";

$insert_result = mysqlQuery($insert_query);

if ($insert_result) {
    echo "✓ Test insert successful!<br>";
    
    // Verify the insert
    $verify_query = mysqlQuery("SELECT * FROM package_quotation_program WHERE quotation_id = '$test_quotation_id'");
    if (mysqli_num_rows($verify_query) > 0) {
        echo "✓ Record verified in database<br>";
        
        // Clean up test data
        $delete_query = mysqlQuery("DELETE FROM package_quotation_program WHERE quotation_id = '$test_quotation_id'");
        if ($delete_query) {
            echo "✓ Test data cleaned up<br>";
        }
    } else {
        echo "✗ Record not found after insert<br>";
    }
} else {
    echo "✗ Test insert failed!<br>";
    echo "Error: " . mysqli_error($conn) . "<br>";
}

echo "<h3>6. Session Storage Test</h3>";
echo "Check browser console for sessionStorage data when creating a quotation.<br>";
echo "Look for 'itinerary_data' in sessionStorage.<br>";

echo "<p><a href='javascript:history.back()'>Go Back</a></p>";
?>
