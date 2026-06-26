<?php
include_once('../../../../../model/model.php');
$package_id_arr = $_POST['package_id_arr'];
$chain_ts = quotation_chain_parse_dmY($_POST['from_date']);
$travel_start_date = $chain_ts ? date('d-m-Y', $chain_ts) : date('d-m-Y');
$from_date = $travel_start_date;

function quotation_chain_parse_dmY($date_str) {
	$date_str = trim(explode(' ', $date_str)[0]);
	$parts = explode('-', $date_str);
	if (sizeof($parts) !== 3) {
		return false;
	}
	return mktime(0, 0, 0, (int)$parts[1], (int)$parts[0], (int)$parts[2]);
}

function quotation_chain_add_days_dmY($date_str, $days) {
	$ts = quotation_chain_parse_dmY($date_str);
	if (!$ts) {
		return $date_str;
	}
	return date('d-m-Y', strtotime('+' . intval($days) . ' days', $ts));
}

$train_info_arr = array();
for($i=0; $i<sizeof($package_id_arr); $i++){

	$from_date = $travel_start_date;

	$sq_package = mysqli_fetch_assoc(mysqlQuery("select * from custom_package_master where package_id='$package_id_arr[$i]'"));		
	$sq_count = mysqli_num_rows(mysqlQuery("select * from custom_package_hotels where package_id = '$package_id_arr[$i]'"));
	if($sq_count==0){

		for($j=0;$j<3;$j++){
			$arr = array(
				'city_id' => '',
				'hotel_id1' => '',
				'city_name' => '',
				'hotel_name' => '',
				'hotel_type' => '',
				'total_days' => '',			
				'package_name' => $sq_package['package_name'],
				'hotel_cost' => '',
				'extra_bed_cost' => '',
				'package_id' => $sq_package['package_id']
			);
			array_push($train_info_arr, $arr);
		}
	}
	else{
		
		$sq_train = mysqlQuery("select * from custom_package_hotels where package_id='$package_id_arr[$i]'");
		while($row_train = mysqli_fetch_assoc($sq_train)){
			
			$total_nights = $row_train['total_days'];
			$sq_hotel_id = mysqli_fetch_assoc(mysqlQuery("select * from hotel_master where hotel_id = '$row_train[hotel_name]'"));
			$hotel_name1 = $sq_hotel_id['hotel_name'];
			$sq_city_id = mysqli_fetch_assoc(mysqlQuery("select * from city_master where city_id = '$row_train[city_name]'"));
			$city_name1 = $sq_city_id['city_name'];

			$check_in_date = $from_date;
			$check_out_date = quotation_chain_add_days_dmY($check_in_date, $total_nights);

			$arr = array(
				'city_id' => $row_train['city_name'],
				'hotel_id1' => $row_train['hotel_name'],
				'city_name' => $city_name1,
				'hotel_name' => $hotel_name1,
				'hotel_type' => $row_train['hotel_type'],
				'total_days' => $row_train['total_days'],
				'package_name' => $sq_package['package_name'],
				'check_in_date' => $check_in_date,
				'check_out_date' => $check_out_date,
				'hotel_cost' => 0,
				'extra_bed_cost' => 0,
				'package_id' => $sq_package['package_id']
			);
			$from_date = $check_out_date;
		array_push($train_info_arr, $arr);
		}
	}
}
echo json_encode($train_info_arr);
?>