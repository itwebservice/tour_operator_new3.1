<?php
include_once('../../../model/model.php');

$quotation_id = isset($_POST['quotation_id']) ? $_POST['quotation_id'] : 0;
$info = array();
$transport_info_arr = array();

if ($quotation_id == '' || $quotation_id == '0') {
	echo json_encode(array('status' => 'empty'));
	exit;
}

$sq_quotation = mysqli_fetch_assoc(mysqlQuery("select * from group_tour_quotation_master where quotation_id='$quotation_id'"));
if (!$sq_quotation) {
	echo json_encode(array('status' => 'error', 'message' => 'Quotation not found'));
	exit;
}

$tour_id = $sq_quotation['tour_group_id'];
$tour_group = $sq_quotation['tour_group'];
$sq_tour = mysqli_fetch_assoc(mysqlQuery("select tour_id, tour_name from tour_master where tour_id='$tour_id'"));

$total_adult = (float)$sq_quotation['total_adult'];
$total_infant = (float)$sq_quotation['total_infant'];
$children_with_bed = (float)$sq_quotation['children_with_bed'];
$children_without_bed = (float)$sq_quotation['children_without_bed'];
$single_person = (float)$sq_quotation['single_person'];

$adult_cost = (float)$sq_quotation['adult_cost'];
$with_bed_cost = (float)$sq_quotation['with_bed_cost'];
$children_cost = (float)$sq_quotation['children_cost'];
$infant_cost = (float)$sq_quotation['infant_cost'];
$single_person_cost = (float)$sq_quotation['single_person_cost'];

// Fallback per-person rates from tour master when quotation category count is 0
$row_cost = mysqli_fetch_assoc(mysqlQuery("select adult_cost, child_with_cost, child_without_cost, infant_cost, single_person_cost from tour_master where tour_id='$tour_id'"));
$tm_adult = isset($row_cost['adult_cost']) ? (float)$row_cost['adult_cost'] : 0;
$tm_cwb = isset($row_cost['child_with_cost']) ? (float)$row_cost['child_with_cost'] : 0;
$tm_cwob = isset($row_cost['child_without_cost']) ? (float)$row_cost['child_without_cost'] : 0;
$tm_infant = isset($row_cost['infant_cost']) ? (float)$row_cost['infant_cost'] : 0;
$tm_single = isset($row_cost['single_person_cost']) ? (float)$row_cost['single_person_cost'] : 0;

$info['status'] = 'ok';
$info['quotation_id'] = $quotation_id;
$info['tour_id'] = $tour_id;
$info['tour_name'] = isset($sq_tour['tour_name']) ? $sq_tour['tour_name'] : $sq_quotation['tour_name'];
$info['tour_group'] = $tour_group;
$info['booking_type'] = $sq_quotation['booking_type'];
$info['customer_name'] = $sq_quotation['customer_name'];
$info['email_id'] = $sq_quotation['email_id'];
$info['mobile_number'] = $sq_quotation['mobile_number'];
$info['enquiry_id'] = $sq_quotation['enquiry_id'];

$info['total_adult'] = $total_adult;
$info['total_infant'] = $total_infant;
$info['children_with_bed'] = $children_with_bed;
$info['children_without_bed'] = $children_without_bed;
$info['single_person'] = $single_person;
$info['total_passangers'] = $sq_quotation['total_passangers'];

$info['adult_cost'] = $adult_cost;
$info['with_bed_cost'] = $with_bed_cost;
$info['children_cost'] = $children_cost;
$info['infant_cost'] = $infant_cost;
$info['single_person_cost'] = $single_person_cost;
$info['tour_cost'] = (float)$sq_quotation['tour_cost'];
$info['service_charge'] = (float)$sq_quotation['service_charge'];
$info['service_tax_subtotal'] = $sq_quotation['service_tax_subtotal'];
$info['quotation_cost'] = (float)$sq_quotation['quotation_cost'];
$info['currency_code'] = $sq_quotation['currency_code'];

// Per-person rates derived from quotation totals (for sale seat-based recalculation)
$info['adult_rate'] = ($total_adult > 0) ? ($adult_cost / $total_adult) : $tm_adult;
$info['with_bed_rate'] = ($children_with_bed > 0) ? ($with_bed_cost / $children_with_bed) : $tm_cwb;
$info['without_bed_rate'] = ($children_without_bed > 0) ? ($children_cost / $children_without_bed) : $tm_cwob;
$info['infant_rate'] = ($total_infant > 0) ? ($infant_cost / $total_infant) : $tm_infant;
$info['single_person_rate'] = ($single_person > 0) ? ($single_person_cost / $single_person) : $tm_single;

$bsmValues = json_decode($sq_quotation['bsm_values']);
$info['tax_apply_on'] = (isset($bsmValues[0]->tax_apply_on)) ? $bsmValues[0]->tax_apply_on : '';
$info['tax_value'] = (isset($bsmValues[0]->tax_value)) ? $bsmValues[0]->tax_value : '';
$info['tcsper'] = (isset($bsmValues[0]->tcsper)) ? $bsmValues[0]->tcsper : '0';
$info['tcsvalue'] = (isset($bsmValues[0]->tcsvalue)) ? $bsmValues[0]->tcsvalue : 0;

$sq_transport = mysqlQuery("select * from group_tour_quotation_transport_entries where quotation_id='$quotation_id'");
while ($row_transport = mysqli_fetch_assoc($sq_transport)) {
	$sq_vehicle = mysqli_fetch_assoc(mysqlQuery("select vehicle_name from b2b_transfer_master where entry_id='" . $row_transport['vehicle_name'] . "'"));
	$vehicle_name = isset($sq_vehicle['vehicle_name']) ? $sq_vehicle['vehicle_name'] : '';

	$pickup_location = '';
	$pickup_value = '';
	if ($row_transport['pickup_type'] == 'city') {
		$row = mysqli_fetch_assoc(mysqlQuery("select city_id,city_name from city_master where city_id='" . $row_transport['pickup'] . "'"));
		if ($row) {
			$pickup_location = $row['city_name'];
			$pickup_value = 'city-' . $row['city_id'];
		}
	} else if ($row_transport['pickup_type'] == 'hotel') {
		$row = mysqli_fetch_assoc(mysqlQuery("select hotel_id,hotel_name from hotel_master where hotel_id='" . $row_transport['pickup'] . "'"));
		if ($row) {
			$pickup_location = $row['hotel_name'];
			$pickup_value = 'hotel-' . $row['hotel_id'];
		}
	} else if ($row_transport['pickup_type'] == 'airport') {
		$row = mysqli_fetch_assoc(mysqlQuery("select airport_id,airport_name, airport_code from airport_master where airport_id='" . $row_transport['pickup'] . "'"));
		if ($row) {
			$pickup_location = $row['airport_name'] . " (" . $row['airport_code'] . ")";
			$pickup_value = 'airport-' . $row['airport_id'];
		}
	}

	$drop_location = '';
	$drop_value = '';
	if ($row_transport['drop_type'] == 'city') {
		$row = mysqli_fetch_assoc(mysqlQuery("select city_id,city_name from city_master where city_id='" . $row_transport['drop_location'] . "'"));
		if ($row) {
			$drop_location = $row['city_name'];
			$drop_value = 'city-' . $row['city_id'];
		}
	} else if ($row_transport['drop_type'] == 'hotel') {
		$row = mysqli_fetch_assoc(mysqlQuery("select hotel_id,hotel_name from hotel_master where hotel_id='" . $row_transport['drop_location'] . "'"));
		if ($row) {
			$drop_location = $row['hotel_name'];
			$drop_value = 'hotel-' . $row['hotel_id'];
		}
	} else if ($row_transport['drop_type'] == 'airport') {
		$row = mysqli_fetch_assoc(mysqlQuery("select airport_id,airport_name, airport_code from airport_master where airport_id='" . $row_transport['drop_location'] . "'"));
		if ($row) {
			$drop_location = $row['airport_name'] . " (" . $row['airport_code'] . ")";
			$drop_value = 'airport-' . $row['airport_id'];
		}
	}

	$start_date = ($row_transport['start_date'] != '' && $row_transport['start_date'] != '0000-00-00')
		? get_date_user($row_transport['start_date']) : '';
	$end_date = ($row_transport['end_date'] != '' && $row_transport['end_date'] != '0000-00-00')
		? get_date_user($row_transport['end_date']) : '';

	$service_duration_raw = isset($row_transport['service_duration']) ? trim($row_transport['service_duration']) : '';
	$row1 = false;
	if ($service_duration_raw != '') {
		$service_duration_esc = mysqlREString($service_duration_raw);
		$row1 = mysqli_fetch_assoc(mysqlQuery("select entry_id from service_duration_master where duration='$service_duration_esc'"));
		if (!$row1) {
			$row1 = mysqli_fetch_assoc(mysqlQuery("select entry_id from service_duration_master where TRIM(duration)='$service_duration_esc' OR LOWER(TRIM(duration))=LOWER('$service_duration_esc') LIMIT 1"));
		}
	}
	$s_duration_id = ($row1 && isset($row1['entry_id'])) ? $row1['entry_id'] : '';

	$transport_info_arr[] = array(
		'vehicle_id' => $row_transport['vehicle_name'],
		'vehicle_name' => $vehicle_name,
		'start_date' => $start_date,
		'end_date' => $end_date,
		'pickup_location' => $pickup_location,
		'pickup_value' => $pickup_value,
		'pickup_type' => $row_transport['pickup_type'],
		'drop_location' => $drop_location,
		'drop_value' => $drop_value,
		'drop_type' => $row_transport['drop_type'],
		'service_duration' => $service_duration_raw,
		's_duration_id' => $s_duration_id,
		'vehicle_count' => $row_transport['vehicle_count']
	);
}

$info['transport_info_arr'] = $transport_info_arr;
echo json_encode($info);
?>
