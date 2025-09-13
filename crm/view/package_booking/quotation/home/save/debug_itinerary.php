<?php
include "../../../../../model/model.php";

echo "<h2>Debug: Package Quotation Program Data</h2>";

// Get all records from package_quotation_program table
$query = "SELECT * FROM package_quotation_program ORDER BY id DESC LIMIT 20";
$result = mysqlQuery($query);

echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>ID</th><th>Quotation ID</th><th>Package ID</th><th>Attraction</th><th>Day Program</th><th>Stay</th><th>Meal Plan</th><th>Day Count</th></tr>";

while ($row = mysqli_fetch_assoc($result)) {
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

// Get distinct quotation IDs
echo "<h3>Distinct Quotation IDs in the table:</h3>";
$query2 = "SELECT DISTINCT quotation_id FROM package_quotation_program ORDER BY quotation_id DESC";
$result2 = mysqlQuery($query2);

echo "<ul>";
while ($row2 = mysqli_fetch_assoc($result2)) {
    echo "<li>" . $row2['quotation_id'] . "</li>";
}
echo "</ul>";

// Check for temporary quotation IDs
echo "<h3>Temporary Quotation IDs (starting with 'temp_'):</h3>";
$query3 = "SELECT DISTINCT quotation_id FROM package_quotation_program WHERE quotation_id LIKE 'temp_%' ORDER BY quotation_id DESC";
$result3 = mysqlQuery($query3);

echo "<ul>";
while ($row3 = mysqli_fetch_assoc($result3)) {
    echo "<li>" . $row3['quotation_id'] . "</li>";
}
echo "</ul>";

echo "<p><a href='test_db.php' target='_blank'>Test Database Connection</a> | <a href='test_insert.php' target='_blank'>Test Insert</a> | <a href='javascript:history.back()'>Go Back</a></p>";
?>
