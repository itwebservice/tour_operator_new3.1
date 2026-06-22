<?php
include "../../../../model/model.php";
$quotation_id = $_POST['quotation_id'];
$package_type = $_POST['package_type'];
$quot_info_arr = array();
$hotel_info_arr = array();

$sq_quotation = mysqli_fetch_assoc(mysqlQuery("select * from package_tour_quotation_master where quotation_id='$quotation_id'"));
if (!$sq_quotation) {
	echo json_encode(array('hotel_info_arr' => array(), 'tour_cost' => 0, 'service_charge' => 0, 'discount_in' => 'Percentage', 'discount' => 0, 'tax_apply_on' => '', 'tax_value' => ''));
	exit;
}

$sq_costing = mysqli_fetch_assoc(mysqlQuery("select * from package_tour_quotation_costing_entries where quotation_id = '$quotation_id' and package_type='$package_type'"));
if (!$sq_costing) {
	$sq_costing = mysqli_fetch_assoc(mysqlQuery("select * from package_tour_quotation_costing_entries where quotation_id = '$quotation_id' limit 1"));
}

if ($sq_costing) {
	$quot_info_arr['tour_cost'] = $sq_costing['tour_cost'] + $sq_costing['transport_cost'] + $sq_costing['excursion_cost'] + $sq_quotation['guide_cost'] + $sq_quotation['misc_cost'] + $sq_quotation['visa_cost'];
	$quot_info_arr['service_charge'] = $sq_costing['service_charge'];
	$quot_info_arr['tax_type'] =  '';
	$quot_info_arr['tax_in_percentage'] = '';
	$quot_info_arr['discount_in'] = $sq_costing['discount_in'];
	$quot_info_arr['discount'] = $sq_costing['discount'];
	$bsm_values = json_decode($sq_costing['bsmValues']);
	$quot_info_arr['tax_apply_on'] = isset($bsm_values[0]->tax_apply_on) ? $bsm_values[0]->tax_apply_on : '';
	$tax_app_value = '';
	if(isset($bsm_values[0]->tax_value) && $bsm_values[0]->tax_value == 1){
		$tax_app_value = 'Basic Amount';
	}
	else if(isset($bsm_values[0]->tax_value) && $bsm_values[0]->tax_value == 2){
		$tax_app_value = 'Service Charge';
	}
	else if(isset($bsm_values[0]->tax_value) && $bsm_values[0]->tax_value == 3){
		$tax_app_value = 'Total';
	}
	$quot_info_arr['tax_value'] = isset($bsm_values[0]->tax_value) ? $bsm_values[0]->tax_value : '';
	$quot_info_arr['tax_app_value'] = $tax_app_value;
	$quot_info_arr['service_tax_subtotal'] = $sq_costing['service_tax_subtotal'];
	$quot_info_arr['total_tour_cost'] = $sq_costing['total_tour_cost'] + $sq_quotation['guide_cost']+ $sq_quotation['misc_cost'];
} else {
	$quot_info_arr['tour_cost'] = 0;
	$quot_info_arr['service_charge'] = 0;
	$quot_info_arr['tax_type'] = '';
	$quot_info_arr['tax_in_percentage'] = '';
	$quot_info_arr['discount_in'] = 'Percentage';
	$quot_info_arr['discount'] = 0;
	$quot_info_arr['tax_apply_on'] = '';
	$quot_info_arr['tax_value'] = '';
	$quot_info_arr['tax_app_value'] = '';
	$quot_info_arr['service_tax_subtotal'] = '';
	$quot_info_arr['total_tour_cost'] = 0;
}

$hotel_query = "select * from package_tour_quotation_hotel_entries where quotation_id='$quotation_id'";
if (!empty($package_type)) {
	$hotel_query .= " and package_type='$package_type'";
}
$sq_hotel = mysqlQuery($hotel_query);
if (mysqli_num_rows($sq_hotel) == 0 && !empty($package_type)) {
	$sq_hotel = mysqlQuery("select * from package_tour_quotation_hotel_entries where quotation_id='$quotation_id'");
}
while($row_hotel = mysqli_fetch_assoc($sq_hotel)){

	$sq_hotel_id = mysqli_fetch_assoc(mysqlQuery("select * from hotel_master where hotel_id = '$row_hotel[hotel_name]'"));
	$hotel_name1 = isset($sq_hotel_id['hotel_name']) ? $sq_hotel_id['hotel_name'] : '';
	$sq_city_id = mysqli_fetch_assoc(mysqlQuery("select * from city_master where city_id = '$row_hotel[city_name]'"));
	$city_name1 = isset($sq_city_id['city_name']) ? $sq_city_id['city_name'] : '';
	$meal_plan= $row_hotel['meal_plan'];
	$check_in_val = $row_hotel['check_in'];
	$check_out_val = $row_hotel['check_out'];
	if($check_in_val != '' && $check_in_val != '0000-00-00' && strpos($check_in_val, ':') === false){
		$check_in_val = get_date_user($check_in_val) . ' 00:00';
	} else {
		$check_in_val = get_datetime_user($check_in_val);
	}
	if($check_out_val != '' && $check_out_val != '0000-00-00' && strpos($check_out_val, ':') === false){
		$check_out_val = get_date_user($check_out_val) . ' 00:00';
	} else {
		$check_out_val = get_datetime_user($check_out_val);
	}

	$arr2 = array(
		'city_id' => $row_hotel['city_name'],
		'city_name' => $city_name1,
		'from_date' => $sq_quotation['from_date'],
		'to_date' => $sq_quotation['to_date'],
		'hotel_id1' => $row_hotel['hotel_name'],
		'hotel_name1' => $hotel_name1,
		'total_rooms' => $row_hotel['total_rooms'],
		'check_in' => $check_in_val,
		'check_out' => $check_out_val,
		'room_category' => $row_hotel['room_category'],
		'extra_bed' => $row_hotel['extra_bed'],
		'meal_plan'=>$meal_plan
	);
	array_push($hotel_info_arr, $arr2);
}
$quot_info_arr['hotel_info_arr'] = $hotel_info_arr;

echo json_encode($quot_info_arr);
?>
