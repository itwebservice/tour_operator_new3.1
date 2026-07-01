<?php
include "../../../../../model/model.php";

echo "<h2>Check Package Program Data</h2>";

$package_id = 29; // The package ID from your test

echo "<h3>Checking package ID: $package_id</h3>";

// Check if package exists
$package_check = mysqlQuery("SELECT package_name FROM custom_package_master WHERE package_id = '$package_id'");
if (mysqli_num_rows($package_check) > 0) {
    $package_info = mysqli_fetch_assoc($package_check);
    echo "✓ Package exists: " . $package_info['package_name'] . "<br>";
} else {
    echo "✗ Package does not exist<br>";
    exit;
}

// Check program entries
$program_check = mysqlQuery("SELECT COUNT(*) as count FROM custom_package_program WHERE package_id = '$package_id'");
$program_count = mysqli_fetch_assoc($program_check);
echo "Program entries for package $package_id: " . $program_count['count'] . "<br>";

if ($program_count['count'] > 0) {
    echo "<h3>Program Entries:</h3>";
    $program_query = mysqlQuery("SELECT * FROM custom_package_program WHERE package_id = '$package_id'");
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Package ID</th><th>Attraction</th><th>Day Program</th><th>Stay</th><th>Meal Plan</th><th>Day Count</th></tr>";
    
    while ($row = mysqli_fetch_assoc($program_query)) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['package_id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['attraction']) . "</td>";
        echo "<td>" . htmlspecialchars(substr($row['day_wise_program'], 0, 50)) . "...</td>";
        echo "<td>" . htmlspecialchars($row['stay']) . "</td>";
        echo "<td>" . htmlspecialchars($row['meal_plan']) . "</td>";
        echo "<td>" . $row['day_count'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<h3>No program entries found!</h3>";
    echo "<p>This is why the itinerary table is empty. The package needs to have program entries in the <code>custom_package_program</code> table.</p>";
    
    echo "<h3>Available packages with program entries:</h3>";
    $available_query = mysqlQuery("SELECT DISTINCT p.package_id, p.package_name, COUNT(pr.id) as program_count 
                                   FROM custom_package_master p 
                                   LEFT JOIN custom_package_program pr ON p.package_id = pr.package_id 
                                   WHERE pr.id IS NOT NULL 
                                   GROUP BY p.package_id, p.package_name 
                                   ORDER BY program_count DESC 
                                   LIMIT 10");
    
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Package ID</th><th>Package Name</th><th>Program Count</th></tr>";
    
    while ($row = mysqli_fetch_assoc($available_query)) {
        echo "<tr>";
        echo "<td>" . $row['package_id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['package_name']) . "</td>";
        echo "<td>" . $row['program_count'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<p><a href='javascript:history.back()'>Go Back</a></p>";
?>
