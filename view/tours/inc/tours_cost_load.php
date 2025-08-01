<?php
include '../../../config.php';
$package_id = $_POST['package_id'];
$travel_date = $_POST['travel_date'] ?? null;
$adult_count = $_POST['adult_count'];
$child_wobed = $_POST['child_wobed'];
$child_wibed = $_POST['child_wibed'];
$extra_bed_count = $_POST['extra_bed_c'];
$infant_count1 = $_POST['infant_c'];
$all_costs_array = array();
$currency = $_SESSION['session_currency_id'];

$sq_curr_symbol = mysqli_fetch_assoc(mysqlQuery("select default_currency from currency_name_master where id='$currency'"));
$curr_symbol = $sq_curr_symbol['default_currency'];

$sq_to = mysqli_fetch_assoc(mysqlQuery("select currency_rate from roe_master where currency_id='$currency'"));
$to_currency_rate = $sq_to['currency_rate'];

$total_cost = 0;
if ($travel_date) {
	$date1 = date("Y-m-d", strtotime($travel_date));
	$costing_pax = intval($adult_count) + intval($child_wobed) + intval($child_wibed) + intval($extra_bed_count);

	$query = mysqli_fetch_assoc(mysqlQuery("select currency_id from custom_package_master where package_id='$package_id' "));
	$sq_from = mysqli_fetch_assoc(mysqlQuery("select currency_rate from roe_master where currency_id='$query[currency_id]'"));
	$from_currency_rate = $sq_from['currency_rate'];


	//Costing fetch
	$qq = "select * from custom_package_tariff where (`from_date` <= '$date1' and `to_date` >= '$date1') and (`min_pax` <= '$costing_pax' and `max_pax` >= '$costing_pax') and `package_id`='$package_id'";
	$sq_tariff = mysqlQuery($qq);
	while ($row_tariff = mysqli_fetch_assoc($sq_tariff)) {

		$total_cost1 = ($adult_count * (float)($row_tariff['cadult'])) + ($child_wobed * (float)($row_tariff['ccwob'])) + ($child_wibed * (float)($row_tariff['ccwb'])) + ($extra_bed_count * (float)($row_tariff['cextra'])) + ($infant_count1 * (float)($row_tariff['cinfant']));
		$c_amount = ($to_currency_rate != '') ? $to_currency_rate * $total_cost1 : 0;
		array_push($all_costs_array, array(
			'type' => $row_tariff['hotel_type'],
			'cost' => $curr_symbol . ' ' . number_format($c_amount, 2)
		));
	}
	$all_costs_array = ($all_costs_array == NULL) ? [] : $all_costs_array;
}
$all_costs_array = json_encode($all_costs_array);
echo $all_costs_array;
