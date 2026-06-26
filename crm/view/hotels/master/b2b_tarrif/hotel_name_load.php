<?php include "../../../../model/model.php"; ?>
<?php
$city_id = isset($_GET['city_id']) ? trim($_GET['city_id']) : '';

if ($city_id === '' || !ctype_digit((string) $city_id)) {
    echo '<option value="">Select Hotel</option>';
    exit;
}
$city_id = intval($city_id);
?>
<option value="">Select Hotel</option>
<?php
$sq_hotel = mysqlQuery("select hotel_id, hotel_name from hotel_master where city_id='$city_id' and active_flag='Active' order by hotel_name");
while($row_hotel = mysqli_fetch_assoc($sq_hotel))
{
?>
	<option value="<?php echo $row_hotel['hotel_id'] ?>"><?php echo $row_hotel['hotel_name'] ?></option>
<?php	
}

?>
