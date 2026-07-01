<?php
include "../../../../../model/model.php";
global $currency;
$customer_id = $_POST['customer_id'];
$from_date = $_POST['from_date'];
$to_date = $_POST['to_date'];
$branch_status = $_POST['branch_status'];
$branch_admin_id = $_POST['branch_admin_id'];
$role = $_POST['role'];

$array_s = array();
$temp_arr = array();
$count = 0;

// Totals
$total_sale_without_tax = 0;
$total_purchase_without_tax = 0;
$total_profit_loss = 0;

// Include branchwise filteration
include "../../../../../model/app_settings/branchwise_filteration.php";

// Function to calculate tax amount from tax subtotal string
function calculate_tax_amount($tax_subtotal) {
	$tax_amount = 0;
	if ($tax_subtotal !== 0.00 && $tax_subtotal !== '') {
		$service_tax_subtotal1 = explode(',', $tax_subtotal);
		for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
			$service_tax = explode(':', $service_tax_subtotal1[$i]);
			if(isset($service_tax[2])){
				$tax_amount += $service_tax[2];
			}
		}
	}
	return $tax_amount;
}

// Function to get purchase amount from vendor_estimate
function get_purchase_amount($estimate_type, $estimate_type_id) {
	global $currency;
	$total_purchase = 0;
	$sq_purchase = mysqlQuery("select * from vendor_estimate where status!='Cancel' and estimate_type='$estimate_type' and estimate_type_id='$estimate_type_id' and delete_status='0'");
	while ($row_purchase = mysqli_fetch_assoc($sq_purchase)) {
		if ($row_purchase['purchase_return'] == 0) {
			$purchase_amount = $row_purchase['net_total'];
			// Currency conversion if needed
			if($row_purchase['currency_code'] !='0' && $currency != $row_purchase['currency_code']){
				$purchase_amount = currency_conversion($currency, $row_purchase['currency_code'], $purchase_amount);
			}
			$total_purchase += $purchase_amount;
		} else if ($row_purchase['purchase_return'] == 2) {
			$cancel_estimate = json_decode($row_purchase['cancel_estimate']);
			$p_purchase = ($row_purchase['net_total'] - (float)($cancel_estimate[0]->net_total));
			// Currency conversion if needed
			if($row_purchase['currency_code'] !='0' && $currency != $row_purchase['currency_code']){
				$p_purchase = currency_conversion($currency, $row_purchase['currency_code'], $p_purchase);
			}
			$total_purchase += $p_purchase;
		}
	}
	return $total_purchase;
}

// ===== PACKAGE TOUR =====
$query = "select * from package_tour_booking_master where customer_id='$customer_id' and delete_status='0' ";
if ($from_date != "" && $to_date != "") {
	$from_date_db = date('Y-m-d', strtotime($from_date));
	$to_date_db = date('Y-m-d', strtotime($to_date));
	$query .= " and booking_date between '$from_date_db' and '$to_date_db'";
}
$sq_package = mysqlQuery($query);
while ($row_package = mysqli_fetch_assoc($sq_package)) {
	$count++;
	
	// Sale amount calculation (without tax and TCS)
	$tour_fee = $row_package['net_total'];
	$q1 = "SELECT * from package_refund_traveler_estimate where booking_id='$row_package[booking_id]'";
	$row_esti = mysqli_fetch_assoc(mysqlQuery($q1));
	$tour_esti = isset($row_esti['cancel_amount']) ? $row_esti['cancel_amount'] : 0;
	$total_sale = $tour_fee - $tour_esti;
	
	// Calculate tax amount and TCS
	$service_tax_amount = calculate_tax_amount($row_package['tour_service_tax_subtotal']);
	$tcs_amount = $row_package['tcs_per'];
	$sale_without_tax = $total_sale - $service_tax_amount - $tcs_amount;
	
	// Purchase amount
	$total_purchase = get_purchase_amount('Package Tour', $row_package['booking_id']);
	
	// Profit/Loss calculation
	$profit_loss = $sale_without_tax - $total_purchase;
	$profit_loss_percentage = ($sale_without_tax > 0) ? (($profit_loss / $sale_without_tax) * 100) : 0;
	$profit_loss_text = ($profit_loss >= 0) ? 'Profit' : 'Loss';
	$profit_loss_display = number_format($profit_loss, 2) . ' (' . number_format($profit_loss_percentage, 2) . '%) ' . $profit_loss_text;
	
	// Booking ID
	$year = explode("-", $row_package['booking_date']);
	$year = $year[0];
	$booking_id = get_package_booking_id($row_package['booking_id'], $year);
	
	$temp_arr = array("data" => array(
		$count,
		'Package Tour',
		$booking_id,
		number_format($sale_without_tax, 2),
		number_format($total_purchase, 2),
		$profit_loss_display
	));
	array_push($array_s, $temp_arr);
	
	// Add to totals
	$total_sale_without_tax += $sale_without_tax;
	$total_purchase_without_tax += $total_purchase;
	$total_profit_loss += $profit_loss;
}

// ===== GROUP TOUR =====
$query = "select * from tourwise_traveler_details where customer_id='$customer_id' ";
if ($from_date != "" && $to_date != "") {
	$from_date_db = date('Y-m-d', strtotime($from_date));
	$to_date_db = date('Y-m-d', strtotime($to_date));
	$query .= " and form_date between '$from_date_db' and '$to_date_db'";
}
$sq_group = mysqlQuery($query);
while ($row_group = mysqli_fetch_assoc($sq_group)) {
	$count++;
	
	// Sale amount calculation (without tax and TCS)
	$tour_fee = $row_group['total_tour_fee'] + $row_group['total_travel_expense'];
	$cancel_amount = $row_group['cancel_amount'];
	$total_sale = $tour_fee - $cancel_amount;
	
	// Calculate tax amount and TCS
	$service_tax_amount = calculate_tax_amount($row_group['service_tax_subtotal']);
	$tcs_amount = isset($row_group['tcs']) ? $row_group['tcs'] : 0;
	$sale_without_tax = $total_sale - $service_tax_amount - $tcs_amount;
	
	// Purchase amount
	$total_purchase = get_purchase_amount('Group Tour', $row_group['traveler_group_id']);
	
	// Profit/Loss calculation
	$profit_loss = $sale_without_tax - $total_purchase;
	$profit_loss_percentage = ($sale_without_tax > 0) ? (($profit_loss / $sale_without_tax) * 100) : 0;
	$profit_loss_text = ($profit_loss >= 0) ? 'Profit' : 'Loss';
	$profit_loss_display = number_format($profit_loss, 2) . ' (' . number_format($profit_loss_percentage, 2) . '%) ' . $profit_loss_text;
	
	// Booking ID
	$year = explode("-", $row_group['form_date']);
	$year = $year[0];
	$booking_id = get_group_booking_id($row_group['traveler_group_id'], $year);
	
	$temp_arr = array("data" => array(
		$count,
		'Group Tour',
		$booking_id,
		number_format($sale_without_tax, 2),
		number_format($total_purchase, 2),
		$profit_loss_display
	));
	array_push($array_s, $temp_arr);
	
	// Add to totals
	$total_sale_without_tax += $sale_without_tax;
	$total_purchase_without_tax += $total_purchase;
	$total_profit_loss += $profit_loss;
}

// ===== HOTEL BOOKING =====
$query = "select * from hotel_booking_master where customer_id='$customer_id' and delete_status='0' ";
if ($from_date != "" && $to_date != "") {
	$from_date_db = date('Y-m-d', strtotime($from_date));
	$to_date_db = date('Y-m-d', strtotime($to_date));
	$query .= " and created_at between '$from_date_db' and '$to_date_db'";
}
$hotel_result = mysqlQuery($query);
while ($row_hotel = mysqli_fetch_assoc($hotel_result)) {
	$count++;
	
	// Sale amount calculation (without tax and TCS)
	$sale_amount = $row_hotel['total_fee'] - $row_hotel['cancel_amount'];
	
	// Calculate tax amount and TCS
	$tax_amount = calculate_tax_amount($row_hotel['service_tax_subtotal']);
	$markupservice_tax_amount = calculate_tax_amount($row_hotel['markup_tax']);
	$total_tax = $tax_amount + $markupservice_tax_amount;
	$tcs_amount = isset($row_hotel['tcs_tax']) ? $row_hotel['tcs_tax'] : 0;
	$sale_without_tax = $sale_amount - $total_tax - $tcs_amount;
	
	// Purchase amount
	$total_purchase = get_purchase_amount('Hotel', $row_hotel['booking_id']);
	
	// Profit/Loss calculation
	$profit_loss = $sale_without_tax - $total_purchase;
	$profit_loss_percentage = ($sale_without_tax > 0) ? (($profit_loss / $sale_without_tax) * 100) : 0;
	$profit_loss_text = ($profit_loss >= 0) ? 'Profit' : 'Loss';
	$profit_loss_display = number_format($profit_loss, 2) . ' (' . number_format($profit_loss_percentage, 2) . '%) ' . $profit_loss_text;
	
	// Booking ID
	$year = explode("-", $row_hotel['created_at']);
	$year = $year[0];
	$booking_id = get_hotel_booking_id($row_hotel['booking_id'], $year);
	
	$temp_arr = array("data" => array(
		$count,
		'Hotel Booking',
		$booking_id,
		number_format($sale_without_tax, 2),
		number_format($total_purchase, 2),
		$profit_loss_display
	));
	array_push($array_s, $temp_arr);
	
	// Add to totals
	$total_sale_without_tax += $sale_without_tax;
	$total_purchase_without_tax += $total_purchase;
	$total_profit_loss += $profit_loss;
}

// ===== CAR RENTAL BOOKING =====
$query = "select * from car_rental_booking where customer_id='$customer_id' and delete_status='0' ";
if ($from_date != "" && $to_date != "") {
	$from_date_db = date('Y-m-d', strtotime($from_date));
	$to_date_db = date('Y-m-d', strtotime($to_date));
	$query .= " and created_at between '$from_date_db' and '$to_date_db'";
}
$sq_car = mysqlQuery($query);
while ($row_car = mysqli_fetch_assoc($sq_car)) {
	$count++;
	
	// Sale amount calculation (without tax and TCS)
	$total_sale = $row_car['total_fees'] - $row_car['cancel_amount'];
	
	// Calculate tax amount and TCS
	$tax_amount = calculate_tax_amount($row_car['service_tax_subtotal']);
	$markupservice_tax_amount = calculate_tax_amount($row_car['markup_cost_subtotal']);
	$total_tax = $tax_amount + $markupservice_tax_amount;
	$tcs_amount = isset($row_car['tcs_tax']) ? $row_car['tcs_tax'] : 0;
	$sale_without_tax = $total_sale - $total_tax - $tcs_amount;
	
	// Purchase amount
	$total_purchase = get_purchase_amount('Car Rental', $row_car['booking_id']);
	
	// Profit/Loss calculation
	$profit_loss = $sale_without_tax - $total_purchase;
	$profit_loss_percentage = ($sale_without_tax > 0) ? (($profit_loss / $sale_without_tax) * 100) : 0;
	$profit_loss_text = ($profit_loss >= 0) ? 'Profit' : 'Loss';
	$profit_loss_display = number_format($profit_loss, 2) . ' (' . number_format($profit_loss_percentage, 2) . '%) ' . $profit_loss_text;
	
	// Booking ID
	$year = explode("-", $row_car['created_at']);
	$year = $year[0];
	$booking_id = get_car_rental_booking_id($row_car['booking_id'], $year);
	
	$temp_arr = array("data" => array(
		$count,
		'Car Rental Booking',
		$booking_id,
		number_format($sale_without_tax, 2),
		number_format($total_purchase, 2),
		$profit_loss_display
	));
	array_push($array_s, $temp_arr);
	
	// Add to totals
	$total_sale_without_tax += $sale_without_tax;
	$total_purchase_without_tax += $total_purchase;
	$total_profit_loss += $profit_loss;
}

// ===== FLIGHT BOOKING =====
$query = "select * from ticket_master where customer_id='$customer_id' and delete_status='0' ";
if ($from_date != "" && $to_date != "") {
	$from_date_db = date('Y-m-d', strtotime($from_date));
	$to_date_db = date('Y-m-d', strtotime($to_date));
	$query .= " and created_at between '$from_date_db' and '$to_date_db'";
}
$sq_flight = mysqlQuery($query);
while ($row_flight = mysqli_fetch_assoc($sq_flight)) {
	$count++;
	
	// Sale amount calculation (without tax and TCS)
	$total_sale = $row_flight['ticket_total_cost'] - $row_flight['cancel_amount'];
	
	// Calculate tax amount and TCS (note: flight uses 'tds' field for TCS)
	$tax_amount = calculate_tax_amount($row_flight['service_tax_subtotal']);
	$markupservice_tax_amount = calculate_tax_amount($row_flight['service_tax_markup']);
	$total_tax = $tax_amount + $markupservice_tax_amount;
	$tcs_amount = isset($row_flight['tds']) ? $row_flight['tds'] : 0;
	$sale_without_tax = $total_sale - $total_tax - $tcs_amount;
	
	// Purchase amount
	$total_purchase = get_purchase_amount('Flight', $row_flight['ticket_id']);
	
	// Profit/Loss calculation
	$profit_loss = $sale_without_tax - $total_purchase;
	$profit_loss_percentage = ($sale_without_tax > 0) ? (($profit_loss / $sale_without_tax) * 100) : 0;
	$profit_loss_text = ($profit_loss >= 0) ? 'Profit' : 'Loss';
	$profit_loss_display = number_format($profit_loss, 2) . ' (' . number_format($profit_loss_percentage, 2) . '%) ' . $profit_loss_text;
	
	// Booking ID
	$year = explode("-", $row_flight['created_at']);
	$year = $year[0];
	$booking_id = get_ticket_booking_id($row_flight['ticket_id'], $year);
	
	$temp_arr = array("data" => array(
		$count,
		'Flight Booking',
		$booking_id,
		number_format($sale_without_tax, 2),
		number_format($total_purchase, 2),
		$profit_loss_display
	));
	array_push($array_s, $temp_arr);
	
	// Add to totals
	$total_sale_without_tax += $sale_without_tax;
	$total_purchase_without_tax += $total_purchase;
	$total_profit_loss += $profit_loss;
}

// ===== ACTIVITY/EXCURSION BOOKING =====
$query = "select * from excursion_master where customer_id='$customer_id' and delete_status='0' ";
if ($from_date != "" && $to_date != "") {
	$from_date_db = date('Y-m-d', strtotime($from_date));
	$to_date_db = date('Y-m-d', strtotime($to_date));
	$query .= " and created_at between '$from_date_db' and '$to_date_db'";
}
$sq_activity = mysqlQuery($query);
while ($row_activity = mysqli_fetch_assoc($sq_activity)) {
	$count++;
	
	// Sale amount calculation (without tax and TCS)
	$total_sale = $row_activity['exc_total_cost'] - $row_activity['cancel_amount'];
	
	// Calculate tax amount and TCS
	$tax_amount = calculate_tax_amount($row_activity['service_tax_subtotal']);
	$markupservice_tax_amount = calculate_tax_amount($row_activity['service_tax_markup']);
	$total_tax = $tax_amount + $markupservice_tax_amount;
	$tcs_amount = isset($row_activity['tcs']) ? $row_activity['tcs'] : 0;
	$sale_without_tax = $total_sale - $total_tax - $tcs_amount;
	
	// Purchase amount
	$total_purchase = get_purchase_amount('Activity', $row_activity['exc_id']);
	
	// Profit/Loss calculation
	$profit_loss = $sale_without_tax - $total_purchase;
	$profit_loss_percentage = ($sale_without_tax > 0) ? (($profit_loss / $sale_without_tax) * 100) : 0;
	$profit_loss_text = ($profit_loss >= 0) ? 'Profit' : 'Loss';
	$profit_loss_display = number_format($profit_loss, 2) . ' (' . number_format($profit_loss_percentage, 2) . '%) ' . $profit_loss_text;
	
	// Booking ID
	$year = explode("-", $row_activity['created_at']);
	$year = $year[0];
	$booking_id = get_exc_booking_id($row_activity['exc_id'], $year);
	
	$temp_arr = array("data" => array(
		$count,
		'Activity Booking',
		$booking_id,
		number_format($sale_without_tax, 2),
		number_format($total_purchase, 2),
		$profit_loss_display
	));
	array_push($array_s, $temp_arr);
	
	// Add to totals
	$total_sale_without_tax += $sale_without_tax;
	$total_purchase_without_tax += $total_purchase;
	$total_profit_loss += $profit_loss;
}

// ===== VISA BOOKING =====
$query = "select * from visa_master where customer_id='$customer_id' and delete_status='0' ";
if ($from_date != "" && $to_date != "") {
	$from_date_db = date('Y-m-d', strtotime($from_date));
	$to_date_db = date('Y-m-d', strtotime($to_date));
	$query .= " and created_at between '$from_date_db' and '$to_date_db'";
}
$sq_visa = mysqlQuery($query);
while ($row_visa = mysqli_fetch_assoc($sq_visa)) {
	$count++;
	
	// Sale amount calculation (without tax and TCS)
	$sale_amount = $row_visa['visa_total_cost'] - $row_visa['cancel_amount'];
	
	// Calculate tax amount and TCS
	$tax_amount = calculate_tax_amount($row_visa['service_tax_subtotal']);
	$markupservice_tax_amount = calculate_tax_amount($row_visa['markup_tax']);
	$total_tax = $tax_amount + $markupservice_tax_amount;
	$tcs_amount = isset($row_visa['tcs']) ? $row_visa['tcs'] : 0;
	$sale_without_tax = $sale_amount - $total_tax - $tcs_amount;
	
	// Purchase amount
	$total_purchase = get_purchase_amount('Visa', $row_visa['visa_id']);
	
	// Profit/Loss calculation
	$profit_loss = $sale_without_tax - $total_purchase;
	$profit_loss_percentage = ($sale_without_tax > 0) ? (($profit_loss / $sale_without_tax) * 100) : 0;
	$profit_loss_text = ($profit_loss >= 0) ? 'Profit' : 'Loss';
	$profit_loss_display = number_format($profit_loss, 2) . ' (' . number_format($profit_loss_percentage, 2) . '%) ' . $profit_loss_text;
	
	// Booking ID
	$year = explode("-", $row_visa['created_at']);
	$year = $year[0];
	$booking_id = get_visa_booking_id($row_visa['visa_id'], $year);
	
	$temp_arr = array("data" => array(
		$count,
		'Visa Booking',
		$booking_id,
		number_format($sale_without_tax, 2),
		number_format($total_purchase, 2),
		$profit_loss_display
	));
	array_push($array_s, $temp_arr);
	
	// Add to totals
	$total_sale_without_tax += $sale_without_tax;
	$total_purchase_without_tax += $total_purchase;
	$total_profit_loss += $profit_loss;
}

// ===== TRAIN BOOKING =====
$query = "select * from train_ticket_master where customer_id='$customer_id' and delete_status='0' ";
if ($from_date != "" && $to_date != "") {
	$from_date_db = date('Y-m-d', strtotime($from_date));
	$to_date_db = date('Y-m-d', strtotime($to_date));
	$query .= " and created_at between '$from_date_db' and '$to_date_db'";
}
$sq_train = mysqlQuery($query);
while ($row_train = mysqli_fetch_assoc($sq_train)) {
	$count++;
	
	// Sale amount calculation (without tax and TCS)
	$total_sale = $row_train['net_total'] - $row_train['cancel_amount'];
	
	// Calculate tax amount and TCS
	$tax_amount = calculate_tax_amount($row_train['service_tax_subtotal']);
	$markupservice_tax_amount = calculate_tax_amount($row_train['service_tax_markup']);
	$total_tax = $tax_amount + $markupservice_tax_amount;
	$tcs_amount = isset($row_train['tcs']) ? $row_train['tcs'] : 0;
	$sale_without_tax = $total_sale - $total_tax - $tcs_amount;
	
	// Purchase amount
	$total_purchase = get_purchase_amount('Train', $row_train['train_ticket_id']);
	
	// Profit/Loss calculation
	$profit_loss = $sale_without_tax - $total_purchase;
	$profit_loss_percentage = ($sale_without_tax > 0) ? (($profit_loss / $sale_without_tax) * 100) : 0;
	$profit_loss_text = ($profit_loss >= 0) ? 'Profit' : 'Loss';
	$profit_loss_display = number_format($profit_loss, 2) . ' (' . number_format($profit_loss_percentage, 2) . '%) ' . $profit_loss_text;
	
	// Booking ID
	$year = explode("-", $row_train['created_at']);
	$year = $year[0];
	$booking_id = get_train_ticket_booking_id($row_train['train_ticket_id'], $year);
	
	$temp_arr = array("data" => array(
		$count,
		'Train Booking',
		$booking_id,
		number_format($sale_without_tax, 2),
		number_format($total_purchase, 2),
		$profit_loss_display
	));
	array_push($array_s, $temp_arr);
	
	// Add to totals
	$total_sale_without_tax += $sale_without_tax;
	$total_purchase_without_tax += $total_purchase;
	$total_profit_loss += $profit_loss;
}

// ===== BUS BOOKING =====
$query = "select * from bus_booking_master where customer_id='$customer_id' and delete_status='0' ";
if ($from_date != "" && $to_date != "") {
	$from_date_db = date('Y-m-d', strtotime($from_date));
	$to_date_db = date('Y-m-d', strtotime($to_date));
	$query .= " and created_at between '$from_date_db' and '$to_date_db'";
}
$sq_bus = mysqlQuery($query);
while ($row_bus = mysqli_fetch_assoc($sq_bus)) {
	$count++;
	
	// Sale amount calculation (without tax and TCS)
	$total_sale = $row_bus['net_total'] - $row_bus['cancel_amount'];
	
	// Calculate tax amount and TCS
	$tax_amount = calculate_tax_amount($row_bus['service_tax_subtotal']);
	$markupservice_tax_amount = calculate_tax_amount($row_bus['service_tax_markup']);
	$total_tax = $tax_amount + $markupservice_tax_amount;
	$tcs_amount = isset($row_bus['tcs']) ? $row_bus['tcs'] : 0;
	$sale_without_tax = $total_sale - $total_tax - $tcs_amount;
	
	// Purchase amount
	$total_purchase = get_purchase_amount('Bus', $row_bus['booking_id']);
	
	// Profit/Loss calculation
	$profit_loss = $sale_without_tax - $total_purchase;
	$profit_loss_percentage = ($sale_without_tax > 0) ? (($profit_loss / $sale_without_tax) * 100) : 0;
	$profit_loss_text = ($profit_loss >= 0) ? 'Profit' : 'Loss';
	$profit_loss_display = number_format($profit_loss, 2) . ' (' . number_format($profit_loss_percentage, 2) . '%) ' . $profit_loss_text;
	
	// Booking ID
	$year = explode("-", $row_bus['created_at']);
	$year = $year[0];
	$booking_id = get_bus_booking_id($row_bus['booking_id'], $year);
	
	$temp_arr = array("data" => array(
		$count,
		'Bus Booking',
		$booking_id,
		number_format($sale_without_tax, 2),
		number_format($total_purchase, 2),
		$profit_loss_display
	));
	array_push($array_s, $temp_arr);
	
	// Add to totals
	$total_sale_without_tax += $sale_without_tax;
	$total_purchase_without_tax += $total_purchase;
	$total_profit_loss += $profit_loss;
}

// ===== MISCELLANEOUS BOOKING =====
$query = "select * from miscellaneous_master where customer_id='$customer_id' and delete_status='0' ";
if ($from_date != "" && $to_date != "") {
	$from_date_db = date('Y-m-d', strtotime($from_date));
	$to_date_db = date('Y-m-d', strtotime($to_date));
	$query .= " and created_at between '$from_date_db' and '$to_date_db'";
}
$sq_misc = mysqlQuery($query);
while ($row_misc = mysqli_fetch_assoc($sq_misc)) {
	$count++;
	
	// Sale amount calculation (without tax and TCS)
	$total_sale = $row_misc['misc_total_cost'] - $row_misc['cancel_amount'];
	
	// Calculate tax amount and TCS
	$tax_amount = calculate_tax_amount($row_misc['service_tax_subtotal']);
	$markupservice_tax_amount = calculate_tax_amount($row_misc['service_tax_markup']);
	$total_tax = $tax_amount + $markupservice_tax_amount;
	$tcs_amount = isset($row_misc['tcs']) ? $row_misc['tcs'] : 0;
	$sale_without_tax = $total_sale - $total_tax - $tcs_amount;
	
	// Purchase amount
	$total_purchase = get_purchase_amount('Miscellaneous', $row_misc['misc_id']);
	
	// Profit/Loss calculation
	$profit_loss = $sale_without_tax - $total_purchase;
	$profit_loss_percentage = ($sale_without_tax > 0) ? (($profit_loss / $sale_without_tax) * 100) : 0;
	$profit_loss_text = ($profit_loss >= 0) ? 'Profit' : 'Loss';
	$profit_loss_display = number_format($profit_loss, 2) . ' (' . number_format($profit_loss_percentage, 2) . '%) ' . $profit_loss_text;
	
	// Booking ID
	$year = explode("-", $row_misc['created_at']);
	$year = $year[0];
	$booking_id = get_misc_booking_id($row_misc['misc_id'], $year);
	
	$temp_arr = array("data" => array(
		$count,
		'Miscellaneous Booking',
		$booking_id,
		number_format($sale_without_tax, 2),
		number_format($total_purchase, 2),
		$profit_loss_display
	));
	array_push($array_s, $temp_arr);
	
	// Add to totals
	$total_sale_without_tax += $sale_without_tax;
	$total_purchase_without_tax += $total_purchase;
	$total_profit_loss += $profit_loss;
}

// Calculate total profit/loss percentage
$total_profit_loss_percentage = ($total_sale_without_tax > 0) ? (($total_profit_loss / $total_sale_without_tax) * 100) : 0;
$total_profit_loss_text = ($total_profit_loss >= 0) ? 'Profit' : 'Loss';
$total_profit_loss_display = number_format($total_profit_loss, 2) . ' (' . number_format($total_profit_loss_percentage, 2) . '%) ' . $total_profit_loss_text;

// Add footer data for totals
$footer_data = array(
	"footer_data" => array(
		'total_footers' => 4,
		'foot0' => "",
		'namecol0' => "Total",
		'col0' => 3,
		'class0' => "text-right bold",
		'foot1' => number_format($total_sale_without_tax, 2),
		'namecol1' => "",
		'col1' => 1,
		'class1' => "info text-right bold",
		'foot2' => number_format($total_purchase_without_tax, 2),
		'namecol2' => "",
		'col2' => 1,
		'class2' => "warning text-right bold",
		'foot3' => $total_profit_loss_display,
		'namecol3' => "",
		'col3' => 1,
		'class3' => "success text-right bold"
	)
);
array_push($array_s, $footer_data);

echo json_encode($array_s);
?>

