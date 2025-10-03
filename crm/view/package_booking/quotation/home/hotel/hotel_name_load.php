<?php 
include "../../../../../model/model.php"; 
$city_id = $_GET['city_id'];

// Debug logging
error_log("Hotel Name Load - City ID: " . $city_id);

// Check if city_id is valid
if(empty($city_id) || !is_numeric($city_id)) {
    error_log("Hotel Name Load - Invalid city_id: " . $city_id);
    echo '<option value="">Invalid City</option>';
    exit;
}
?>
<option value="">Select Hotel</option>
<?php
$sq_hotel = mysqlQuery("select * from hotel_master where city_id='$city_id' and active_flag='Active'");
$hotel_count = 0;

while($row_hotel = mysqli_fetch_assoc($sq_hotel))
{
    $hotel_count++;
?>
	<option value="<?php echo $row_hotel['hotel_id'] ?>"><?php echo $row_hotel['hotel_name'] ?></option>
<?php	
}

// Debug logging
error_log("Hotel Name Load - Found " . $hotel_count . " hotels for city_id: " . $city_id);

// If no hotels found, add a debug option
if($hotel_count == 0) {
    echo '<option value="">No hotels found for this city</option>';
}
?>