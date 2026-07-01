<?php 
class car_transport_service_voucher{

	public function transport_voucher_save()
	{
		$booking_id = $_POST['booking_id'];
		$vehicle_name_array = $_POST['vehicle_name_array'];
		$driver_name_array = $_POST['driver_name_array'];
		$driver_mobile_no_array = $_POST['driver_mobile_no_array'];
$type_array = $_POST['type_array'];
        $vehicle_no_array=$_POST['vehicle_no_array'];


				$voucher_entry = mysqli_num_rows( mysqlQuery("select * from car_rental_transport_voucher_entries where booking_id=$booking_id") );
			if($voucher_entry>0){

				

				$sq11 = mysqlQuery("UPDATE car_rental_transport_voucher_entries SET driver_name='$driver_name_array', mobile_no='$driver_mobile_no_array', type_array='$type_array', vehicle_no='$vehicle_no_array' WHERE booking_id = $booking_id");

			}else{
				$entry_id = mysqli_fetch_assoc( mysqlQuery("select max(entry_id) as max from car_rental_transport_voucher_entries") );
				$entry_id1 = $entry_id['max'] + 1;

				$sq11 = mysqlQuery("INSERT INTO `car_rental_transport_voucher_entries`(`entry_id`, `booking_id`,  `driver_name`, `mobile_no`,`type_array`,`vehicle_no`) VALUES ('$entry_id1', '$booking_id', '$driver_name_array','$driver_mobile_no_array','$type_array','$vehicle_no_array')");
			 }
			if($sq11){
				echo "Service voucher information saved successfully.";
				exit;
			}
           
		else{
			echo "error--Service voucher can not be generated.";
			exit;
		}
	}
}
?>