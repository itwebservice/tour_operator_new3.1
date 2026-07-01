<?php
include '../../../model/model.php';

$quotation_id = $_POST['quotation_id'];

$itinerary_data = array();

// Fetch ALL itinerary rows from car_rental_quotation_program table for this quotation
$sq_itinerary = mysqlQuery("SELECT * FROM car_rental_quotation_program WHERE quotation_id='$quotation_id' ORDER BY id ASC");

$row_count = mysqli_num_rows($sq_itinerary);

while($row = mysqli_fetch_assoc($sq_itinerary)){
    $itinerary_data[] = array(
        'attraction' => $row['attraction'],
        'day_wise_program' => $row['day_wise_program'],
        'stay' => $row['stay'],
        'meal_plan' => $row['meal_plan']
    );
}

// Return all itinerary rows as JSON
echo json_encode($itinerary_data);
?>

