<?php
include_once('../../../../../model/model.php');

//Get selected currency rate
global $currency;
$sq_to = mysqli_fetch_assoc(mysqlQuery("select currency_rate from roe_master where currency_id='$currency'"));
$to_currency_rate = $sq_to['currency_rate'];

$package_id_arr = $_POST['package_id_arr'];
$total_adult = $_POST['total_adult'];

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

$chain_ts = quotation_chain_parse_dmY($_POST['from_date']);
$travel_start_date = $chain_ts ? date('d-m-Y', $chain_ts) : date('d-m-Y');
$chain_date = $travel_start_date;
$transport_info_arr = array();
$total_cost = 0;
for($i=0; $i<sizeof($package_id_arr); $i++){
	
	$chain_date = $travel_start_date;

	$sq_package = mysqli_fetch_assoc(mysqlQuery("select * from custom_package_master where package_id='$package_id_arr[$i]'"));
	$sq_transport = mysqlQuery("select * from custom_package_transport where package_id='$package_id_arr[$i]'");

	$hotel_segments = array();
	$sq_hotels = mysqlQuery("select total_days from custom_package_hotels where package_id='$package_id_arr[$i]' order by entry_id");
	while($row_hotel = mysqli_fetch_assoc($sq_hotels)){
		$check_in_date = $chain_date;
		$check_out_date = quotation_chain_add_days_dmY($check_in_date, $row_hotel['total_days']);
		$hotel_segments[] = array(
			'check_in_date' => $check_in_date,
			'check_out_date' => $check_out_date
		);
		$chain_date = $check_out_date;
	}
	if(sizeof($hotel_segments) == 0){
		$package_nights = intval($sq_package['total_nights']);
		if($package_nights <= 0){
			$package_nights = intval($sq_package['total_days']);
		}
		$package_start = $chain_date;
		$package_end = quotation_chain_add_days_dmY($package_start, $package_nights);
		$hotel_segments[] = array(
			'check_in_date' => $package_start,
			'check_out_date' => $package_end
		);
		$chain_date = $package_end;
	}

	$transport_index = 0;
	while($row_transport = mysqli_fetch_assoc($sq_transport)){

		$segment_index = $transport_index;
		if($segment_index >= sizeof($hotel_segments)){
			$segment_index = sizeof($hotel_segments) - 1;
		}
		$start_date = $hotel_segments[$segment_index]['check_in_date'];
		$end_date = $hotel_segments[$segment_index]['check_out_date'];
		$tariff_lookup_date = get_date_db($start_date);

		$service_duration = '';
		$row_tariff_master1 = mysqlQuery("select * from b2b_transfer_tariff where 1 and vehicle_id='$row_transport[vehicle_name]' order by tariff_id desc");
		while($row_tariff_master = mysqli_fetch_assoc($row_tariff_master1)){

			$currency_id = $row_tariff_master['currency_id'];
			$sq_from = mysqli_fetch_assoc(mysqlQuery("select currency_rate from roe_master where currency_id='$currency_id'"));
			$from_currency_rate = $sq_from['currency_rate'];
			$tariff_count = mysqli_num_rows(mysqlQuery("select * from b2b_transfer_tariff_entries where tariff_id='$row_tariff_master[tariff_id]' and pickup_type = '$row_transport[pickup_type]' and drop_type = '$row_transport[drop_type]' and pickup_location = '$row_transport[pickup]' and drop_location = '$row_transport[drop]' and (from_date <='$tariff_lookup_date' and to_date>='$tariff_lookup_date')"));
			if($tariff_count != 0){
				$sq_tariff = mysqli_fetch_assoc(mysqlQuery("select * from b2b_transfer_tariff_entries where tariff_id='$row_tariff_master[tariff_id]' and pickup_type = '$row_transport[pickup_type]' and drop_type = '$row_transport[drop_type]' and pickup_location = '$row_transport[pickup]' and drop_location = '$row_transport[drop]' and (from_date <='$tariff_lookup_date' and to_date>='$tariff_lookup_date')"));
				$row1 = mysqli_fetch_assoc(mysqlQuery("select duration from service_duration_master where entry_id='$sq_tariff[service_duration]'"));
				$service_duration = $row1['duration'];
				$tariff_data = json_decode($sq_tariff['tariff_data']);
				$total_cost = $tariff_data[0]->total_cost;
				break;
			}else{
				$total_cost = 0;
				break;
			}
		}
		$q_transport = mysqli_fetch_assoc(mysqlQuery("select * from b2b_transfer_master where entry_id='$row_transport[vehicle_name]'"));
		$seating_capacity = $q_transport['seating_capacity'];
		$total_vehicles = ($seating_capacity > 0 ) ? ceil(intval($total_adult) / intval($seating_capacity)) : 0;
		// Pickup
		if($row_transport['pickup_type'] == 'city'){
			$row = mysqli_fetch_assoc(mysqlQuery("select city_id,city_name from city_master where city_id='$row_transport[pickup]'"));
			$pickup = $row['city_name'];
			$pickup_id = $row['city_id'];
		}
		else if($row_transport['pickup_type'] == 'hotel'){
			$row = mysqli_fetch_assoc(mysqlQuery("select hotel_id,hotel_name from hotel_master where hotel_id='$row_transport[pickup]'"));
			$pickup_id = $row['hotel_id'];
			$pickup = $row['hotel_name'];
		}
		else{
			$row = mysqli_fetch_assoc(mysqlQuery("select airport_name, airport_code, airport_id from airport_master where airport_id='$row_transport[pickup]'"));
			$airport_nam = clean($row['airport_name']);
			$airport_code = clean($row['airport_code']);
			$pickup = $airport_nam." (".$airport_code.")";
			$pickup = $pickup;
			$pickup_id = $row['airport_id'];
		}
		// Drop
		if($row_transport['drop_type'] == 'city'){
			$row = mysqli_fetch_assoc(mysqlQuery("select city_id,city_name from city_master where city_id='$row_transport[drop]'"));
			$drop = $row['city_name'];
			$drop_id = $row['city_id'];
		}
		else if($row_transport['drop_type'] == 'hotel'){
			$row = mysqli_fetch_assoc(mysqlQuery("select hotel_id,hotel_name from hotel_master where hotel_id='$row_transport[drop]'"));
			$drop = $row['hotel_name'];
			$drop_id = $row['hotel_id'];
		}
		else{
			$row = mysqli_fetch_assoc(mysqlQuery("select airport_name, airport_code, airport_id from airport_master where airport_id='$row_transport[drop]'"));
			$airport_nam = clean($row['airport_name']);
			$airport_code = clean($row['airport_code']);
			$drop = $airport_nam." (".$airport_code.")";
			$drop = $drop;
			$drop_id = $row['airport_id'];
		}

		$arr1 = array(
			'bus_name' => $q_transport['vehicle_name'],
			'bus_id' => $q_transport['entry_id'],
			'package_name' => $sq_package['package_name'],
			'package_id' => $sq_package['package_id'],
			'pickup' => $pickup,
			'pickup_id' => $pickup_id,
			'drop'=> $drop,
			'drop_id'=> $drop_id,
			'total_cost'=>$total_cost,
			'pickup_type' => $row_transport['pickup_type'],
			'drop_type'=> $row_transport['drop_type'],
			'total_vehicles'=>$total_vehicles,
			'duration'=> $service_duration,
			'start_date' => $start_date,
			'end_date' => $end_date
		);	
		array_push($transport_info_arr, $arr1);
		$transport_index++;
	}
}
echo json_encode($transport_info_arr);
?>