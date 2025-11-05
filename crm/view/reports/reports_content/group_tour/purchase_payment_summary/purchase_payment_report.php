<?php
include "../../../../../model/model.php";
include_once('../../../../vendor/inc/vendor_generic_functions.php');
$array_s = array();
$temp_arr = array();
$tour_id= $_POST['tour_id'];
$group_id= $_POST['group_id'];
$role = $_SESSION['role'];
$branch_admin_id = $_SESSION['branch_admin_id'];
$branch_status = $_POST['branch_status'];

$count=0;

// Query to get vendor estimates directly with tour_group_id filter
$query = "select * from vendor_estimate where estimate_type='Group Tour' and delete_status='0'";

// Add filter based on tour_group_id from tourwise_traveler_details
if($tour_id!="" || $group_id!="")
{
	$query .= " and estimate_type_id IN (
	            select DISTINCT tour_group_id from tourwise_traveler_details 
	            where delete_status='0'";
	
	if($tour_id!="")
	{
		$query .= " and tour_id = '$tour_id'";
	}
	if($group_id!="")
	{
		$query .= " and tour_group_id = '$group_id'";
	}
	$query .= ")";
}

if($branch_status=='yes' && $role=='Branch Admin'){
    $query .= " and branch_admin_id = '$branch_admin_id'";
}
$query .= " order by estimate_id";

$sq_vendor_estimate = mysqlQuery($query);
while($row_estimate = mysqli_fetch_assoc($sq_vendor_estimate))
{
	$count++;
	
	// Get vendor type and supplier name
	$vendor_type = $row_estimate['vendor_type'];

	$estimate_type_val = get_estimate_type_name($row_estimate['estimate_type'], $row_estimate['estimate_type_id']);
	
	// Get supplier name based on vendor type
	$supplier_name = '';
	if($vendor_type == 'Hotel Vendor') {
		$sq_hotel = mysqli_fetch_assoc(mysqlQuery("select hotel_name from hotel_master where hotel_id='$row_estimate[vendor_type_id]'"));
		$supplier_name = isset($sq_hotel['hotel_name']) ? $sq_hotel['hotel_name'] : 'N/A';
	}
	else if($vendor_type == 'Transport Vendor') {
		$sq_transport = mysqli_fetch_assoc(mysqlQuery("select transport_agency_name from transport_agency_master where transport_agency_id='$row_estimate[vendor_type_id]'"));
		$supplier_name = isset($sq_transport['transport_agency_name']) ? $sq_transport['transport_agency_name'] : 'N/A';
	}
	else if($vendor_type == 'Visa Vendor') {
		$sq_visa = mysqli_fetch_assoc(mysqlQuery("select vendor_name from visa_vendor where vendor_id='$row_estimate[vendor_type_id]'"));
		$supplier_name = isset($sq_visa['vendor_name']) ? $sq_visa['vendor_name'] : 'N/A';
	}
	else if($vendor_type == 'Excursion Vendor') {
		$sq_excursion = mysqli_fetch_assoc(mysqlQuery("select vendor_name from site_seeing_vendor where vendor_id='$row_estimate[vendor_type_id]'"));
		$supplier_name = isset($sq_excursion['vendor_name']) ? $sq_excursion['vendor_name'] : 'N/A';
	}
	
	else if($vendor_type == 'Car Rental Vendor') {
		$sq_car_rental = mysqli_fetch_assoc(mysqlQuery("select vendor_name from car_rental_vendor where vendor_id='$row_estimate[vendor_type_id]'"));
		$supplier_name = isset($sq_car_rental['vendor_name']) ? $sq_car_rental['vendor_name'] : 'N/A';
	}
	else if($vendor_type == 'Cruise Vendor') {
		$sq_cruise = mysqli_fetch_assoc(mysqlQuery("select company_name from cruise_master where cruise_id='$row_estimate[vendor_type_id]'"));
		$supplier_name = isset($sq_cruise['company_name']) ? $sq_cruise['company_name'] : 'N/A';
	}
	else if($vendor_type == 'DMC Vendor') {
		$sq_dmc = mysqli_fetch_assoc(mysqlQuery("select company_name from dmc_master where dmc_id='$row_estimate[vendor_type_id]'"));
		$supplier_name = isset($sq_dmc['company_name']) ? $sq_dmc['company_name'] : 'N/A';
	}
	else if($vendor_type == 'Insurance Vendor') {
		$sq_insurance = mysqli_fetch_assoc(mysqlQuery("select vendor_name from insuarance_vendor where vendor_id='$row_estimate[vendor_type_id]'"));
		$supplier_name = isset($sq_insurance['vendor_name']) ? $sq_insurance['vendor_name'] : 'N/A';
	}
	else if($vendor_type == 'Other Vendor') {
		$sq_other = mysqli_fetch_assoc(mysqlQuery("select vendor_name from other_vendors where vendor_id='$row_estimate[vendor_type_id]'"));
		$supplier_name = isset($sq_other['vendor_name']) ? $sq_other['vendor_name'] : 'N/A';
	}
	else if($vendor_type == 'Ticket Vendor') {
		$sq_ticket = mysqli_fetch_assoc(mysqlQuery("select vendor_name from ticket_vendor where vendor_id='$row_estimate[vendor_type_id]'"));
		$supplier_name = isset($sq_ticket['vendor_name']) ? $sq_ticket['vendor_name'] : 'N/A';
	}
	else if($vendor_type == 'Train Ticket Vendor') {
		$sq_train_ticket = mysqli_fetch_assoc(mysqlQuery("select vendor_name from train_ticket_vendor where vendor_id='$row_estimate[vendor_type_id]'"));
		$supplier_name = isset($sq_train_ticket['vendor_name']) ? $sq_train_ticket	['vendor_name'] : 'N/A';
	}
	else {
		$supplier_name = 'N/A';
	}
	
	// Calculate total rooms (s_single_bed_room + s_double_bed_room)
	$single_rooms = $row_estimate['s_single_bed_room'] ? $row_estimate['s_single_bed_room'] : 0;
	$double_rooms = $row_estimate['s_double_bed_room'] ? $row_estimate['s_double_bed_room'] : 0;
	$total_rooms = $single_rooms + $double_rooms;
	
	// Calculate cost per room
	$cost_per_room = 0;
	if($total_rooms > 0) {
		$cost_per_room = number_format(($row_estimate['net_total'] / $total_rooms), 2);
	} else {
		$cost_per_room = number_format($row_estimate['net_total'], 2);
	}
	
	// Get total amount from net_total
	$total_amount = number_format($row_estimate['net_total'], 2);
	
	// Get advance paid amount from vendor_payment_master
	$sq_payment = mysqlQuery("select sum(payment_amount) as total_paid from vendor_payment_master 
	                          where estimate_id='$row_estimate[estimate_id]' and clearance_status!='Cancelled'");
	$row_payment = mysqli_fetch_assoc($sq_payment);
	$advance_paid = isset($row_payment['total_paid']) && $row_payment['total_paid'] ? $row_payment['total_paid'] : 0;
	$advance_paid_formatted = number_format($advance_paid, 2);
	
	// Calculate balance amount
	$balance_amount = $row_estimate['net_total'] - $advance_paid;
	$balance_amount_formatted = number_format($balance_amount, 2);
	
	// Set background color for cancelled or fully refunded
	$bg = "";
	if($row_estimate['status'] == 'Cancel') {
		$bg = "danger";
	}
	
	$temp_arr = array( "data" => array(
		(int)($count),
		$vendor_type,
		$supplier_name,
		$estimate_type_val,
		// $total_rooms,
		// $cost_per_room,
		$total_amount,
		$advance_paid_formatted,
		$balance_amount_formatted
	), "bg" => $bg);
	
	array_push($array_s, $temp_arr);
}

echo json_encode($array_s);
?>

