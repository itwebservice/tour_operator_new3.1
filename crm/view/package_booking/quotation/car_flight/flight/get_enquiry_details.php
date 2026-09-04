<?php
include "../../../../../model/model.php";
header('Content-Type: application/json; charset=utf-8');

$enquiry_id = isset($_REQUEST['enquiry_id']) ? $_REQUEST['enquiry_id'] : '';
$sq_enq = mysqli_fetch_assoc(mysqlQuery("select * from enquiry_master where enquiry_id='$enquiry_id'"));
if (!$sq_enq) {
	echo json_encode(array('name' => '', 'email_id' => '', 'landline_no' => '', 'country_code' => '', 'flights' => array()));
	exit;
}

$enquiry_content_arr = isset($sq_enq['enquiry_content']) ? json_decode($sq_enq['enquiry_content'], true) : array();
if (!is_array($enquiry_content_arr)) {
	$enquiry_content_arr = array();
}

$flights = array();
foreach ($enquiry_content_arr as $row) {
	if (!is_array($row)) {
		continue;
	}
	// Flight enquiry rows are objects with sector_from / travel_datetime (not name/value pairs)
	if (!isset($row['sector_from']) && !isset($row['travel_datetime']) && !isset($row['sector_to'])) {
		continue;
	}
	$airline_id = isset($row['preffered_airline']) ? trim((string) $row['preffered_airline']) : '';
	$airline_label = '';
	if ($airline_id !== '' && ctype_digit($airline_id)) {
		$sq_air = mysqli_fetch_assoc(mysqlQuery("select airline_id, airline_name, airline_code from airline_master where airline_id='$airline_id'"));
		if ($sq_air) {
			$airline_label = $sq_air['airline_name'] . ' (' . $sq_air['airline_code'] . ')';
		}
	}
	$travel_dt = isset($row['travel_datetime']) ? trim((string) $row['travel_datetime']) : '';
	if ($travel_dt !== '' && preg_match('/^\d{4}-\d{2}-\d{2}/', $travel_dt) && function_exists('get_datetime_user')) {
		$travel_dt = get_datetime_user($travel_dt);
	}
	$flights[] = array(
		'travel_datetime' => $travel_dt,
		'sector_from' => isset($row['sector_from']) ? trim((string) $row['sector_from']) : '',
		'sector_to' => isset($row['sector_to']) ? trim((string) $row['sector_to']) : '',
		'preffered_airline' => $airline_id,
		'airline_label' => $airline_label,
		'class_type' => isset($row['class_type']) ? trim((string) $row['class_type']) : '',
		'total_adults_flight' => isset($row['total_adults_flight']) ? $row['total_adults_flight'] : '',
		'total_child_flight' => isset($row['total_child_flight']) ? $row['total_child_flight'] : '',
		'total_infant_flight' => isset($row['total_infant_flight']) ? $row['total_infant_flight'] : '',
		'from_city_id_flight' => isset($row['from_city_id_flight']) ? $row['from_city_id_flight'] : '',
		'to_city_id_flight' => isset($row['to_city_id_flight']) ? $row['to_city_id_flight'] : '',
	);
}

echo json_encode(array(
	'name' => isset($sq_enq['name']) ? $sq_enq['name'] : '',
	'email_id' => isset($sq_enq['email_id']) ? $sq_enq['email_id'] : '',
	'landline_no' => isset($sq_enq['landline_no']) ? $sq_enq['landline_no'] : '',
	'mobile_no' => isset($sq_enq['mobile_no']) ? $sq_enq['mobile_no'] : '',
	'country_code' => isset($sq_enq['country_code']) ? $sq_enq['country_code'] : '',
	'enquiry_content' => $sq_enq['enquiry_content'],
	'flights' => $flights,
));
exit;
?>
