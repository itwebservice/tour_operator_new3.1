<?php
include "../../../../model/model.php";
global $currency;
$emp_id = $_SESSION['emp_id'];
$role = $_SESSION['role'];
$branch_admin_id = $_SESSION['branch_admin_id'];
$branch_status = $_POST['branch_status'];
$customer_id = isset($_POST['customer_id']) ? $_POST['customer_id'] : '';
$travel_type = $_POST['travel_type'];
$from_date = $_POST['from_date'];
$to_date = $_POST['to_date'];
$cust_type = $_POST['cust_type'];
$company_name = isset($_POST['company_name']) ? $_POST['company_name'] : '';
$booker_id = $_POST['booker_id'];
$branch_id = $_POST['branch_id'];

$array_s = array();
$temp_arr = array();
$count = 0;

// Initialize totals
$global_total_cancel = 0;
$global_total_sale = 0;
$global_total_paid = 0;
$global_total_balance = 0;

// Function to get customer info
function get_customer_info($customer_id) {
	global $encrypt_decrypt, $secret_key;
	$sq_customer_info = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$customer_id'"));
	$contact_no = $encrypt_decrypt->fnDecrypt($sq_customer_info['contact_no'], $secret_key);
	$email_id = $encrypt_decrypt->fnDecrypt($sq_customer_info['email_id'], $secret_key);
	if ($sq_customer_info['type'] == 'Corporate' || $sq_customer_info['type'] == 'B2B') {
		$customer_name = $sq_customer_info['company_name'];
	} else {
		$customer_name = $sq_customer_info['first_name'] . ' ' . $sq_customer_info['last_name'];
	}
	return array('name' => $customer_name, 'contact' => $contact_no, 'email' => $email_id);
}

// Function to get employee and branch info
function get_emp_branch_info($emp_id) {
	$sq_emp = mysqli_fetch_assoc(mysqlQuery("select * from emp_master where emp_id='$emp_id'"));
	if ($sq_emp['first_name'] == '') {
		$emp_name = 'Admin';
	} else {
		$emp_name = $sq_emp['first_name'] . ' ' . $sq_emp['last_name'];
	}
	$sq_branch = mysqli_fetch_assoc(mysqlQuery("select * from branches where branch_id='$sq_emp[branch_id]'"));
	$branch_name = $sq_branch['branch_name'] == '' ? 'NA' : $sq_branch['branch_name'];
	return array('emp_name' => $emp_name, 'branch_name' => $branch_name);
}

// Apply common filters function
function apply_common_filters($query, $customer_id, $from_date, $to_date, $cust_type, $company_name, $booker_id, $branch_id, $date_field = 'created_at') {
	if ($customer_id != "") {
		$query .= " and customer_id='$customer_id'";
	}
	if ($from_date != "" && $to_date != "") {
		$from_date = date('Y-m-d', strtotime($from_date));
		$to_date = date('Y-m-d', strtotime($to_date));
		$query .= " and $date_field between '$from_date' and '$to_date'";
	}
	// else{

	// 	// Default: today and tomorrow
    // $from_date = date('Y-m-d'); // today
    // $to_date = date('Y-m-d', strtotime('+1 day')); // tomorrow
    // $query .= " and $date_field between '$from_date' and '$to_date'";
	// }
	if ($cust_type != "") {
		$query .= " and customer_id in (select customer_id from customer_master where type = '$cust_type')";
	}
	if ($company_name != "") {
		$query .= " and customer_id in (select customer_id from customer_master where company_name = '$company_name')";
	}
	if ($booker_id != "") {
		$query .= " and emp_id='$booker_id'";
	}
	if ($branch_id != "") {
		$query .= " and emp_id in(select emp_id from emp_master where branch_id = '$branch_id')";
	}
	return $query;
}

// Include branchwise filteration
include "../../../../model/app_settings/branchwise_filteration.php";

// ===== PACKAGE TOUR =====
if ($travel_type == "" || $travel_type == "Package Tour") {
	$query = "select * from package_tour_booking_master where 1 and delete_status='0' ";
	$query = apply_common_filters($query, $customer_id, $from_date, $to_date, $cust_type, $company_name, $booker_id, $branch_id, 'booking_date');

	$sq_package = mysqlQuery($query);
	while ($row_package = mysqli_fetch_assoc($sq_package)) {
		$count++;


$pass_count = mysqli_num_rows(mysqlQuery("select * from package_travelers_details where booking_id='$row_package[booking_id]'"));
	$cancle_count = mysqli_num_rows(mysqlQuery("select * from package_travelers_details where booking_id='$row_package[booking_id]' and status='Cancel'"));
	if ($pass_count == $cancle_count) {
		$bg = "danger";
	} else {
		$bg = "#fff";
	}

		// Customer info
		$cust_info = get_customer_info($row_package['customer_id']);
		$customer_name = $cust_info['name'];
		$contact_no = $cust_info['contact'];
		$email_id = $cust_info['email'];

		// Employee and branch info
		$emp_branch = get_emp_branch_info($row_package['emp_id']);
		$emp_name = $emp_branch['emp_name'];
		$branch_name = $emp_branch['branch_name'];

		// Pax count
		$sq_total_member = mysqli_num_rows(mysqlQuery("select booking_id from package_travelers_details where booking_id = '$row_package[booking_id]'"));

		// Payment calculations
		$sq_paid_amount = mysqli_fetch_assoc(mysqlQuery("SELECT sum(amount) as sum,sum(credit_charges) as sumc from package_payment_master where booking_id='$row_package[booking_id]' and clearance_status!='Pending' and  clearance_status!='Cancelled'"));
		$credit_card_charges = $sq_paid_amount['sumc'];
		$total_paid = $sq_paid_amount['sum'];
		if ($total_paid == '') $total_paid = 0;

		// Sale and cancel amounts
		$tour_fee = $row_package['net_total'];
		$q1 = "SELECT * from package_refund_traveler_estimate where booking_id='$row_package[booking_id]'";
		$row_esti = mysqli_fetch_assoc(mysqlQuery($q1));
		$tour_esti = isset($row_esti['cancel_amount']) ? $row_esti['cancel_amount'] : 0;
		$total_amount = $tour_fee - $tour_esti;

		// Balance calculation
		$pass_count = mysqli_num_rows(mysqlQuery("select * from package_travelers_details where booking_id='$row_package[booking_id]'"));
		$cancle_count = mysqli_num_rows(mysqlQuery("select * from package_travelers_details where booking_id='$row_package[booking_id]' and status='Cancel'"));
		if ($pass_count == $cancle_count) {
			if ($total_paid > 0) {
				if ($tour_esti > 0) {
					if ($total_paid > $tour_esti) {
						$total_balance = 0;
					} else {
						$total_balance = $tour_esti - $total_paid;
					}
				} else {
					$total_balance = 0;
				}
			} else {
				$total_balance = $tour_esti;
			}
		} else {
			$total_balance = $total_amount - $total_paid;
		}


		// Purchase calculations
		$total_purchase = 0;
		$sq_purchase = mysqlQuery("select * from vendor_estimate where status!='Cancel' and estimate_type='Package Tour' and estimate_type_id='$row_package[booking_id]' and delete_status='0'");
		while ($row_purchase = mysqli_fetch_assoc($sq_purchase)) {
			if ($row_purchase['purchase_return'] == 0) {
				$total_purchase += $row_purchase['net_total'];
			} else if ($row_purchase['purchase_return'] == 2) {
				$cancel_estimate = json_decode($row_purchase['cancel_estimate']);
				$p_purchase = ($row_purchase['net_total'] - (float)($cancel_estimate[0]->net_total));
				$total_purchase += $p_purchase;
			}
		}
		$vendor_name = 'NA';
		if ($total_purchase > 0) {
			$sq_purchase1 = mysqli_fetch_assoc(mysqlQuery("select * from vendor_estimate where status!='Cancel' and estimate_type='Package Tour' and estimate_type_id='$row_package[booking_id]' and delete_status='0'"));
			$vendor_type = $sq_purchase1['vendor_type'];
			$vendor_type_id = $sq_purchase1['vendor_type_id'];
			$vendor_name = get_vendor_name_report($vendor_type, $vendor_type_id);
		}

		// purchase currency conversion
		$currency_amount_3 = '';
		if($total_purchase > 0){
			$row_purchase1 = mysqli_fetch_assoc(mysqlQuery("select * from vendor_estimate where status!='Cancel' and estimate_type='Package Tour' and estimate_type_id='$row_package[booking_id]' and delete_status='0'"));
			if($row_purchase1['currency_code'] !='0' && $currency != $row_purchase1['currency_code']){
				$currency_amount3 = currency_conversion($currency,$row_purchase1['currency_code'],$total_purchase);
				$currency_amount_3 = ' ('.$currency_amount3.')';
			}
		}

		// Incentive
		$sq_incentive = mysqli_fetch_assoc(mysqlQuery("select * from booker_sales_incentive where booking_id='$row_package[booking_id]' and service_type='Package Tour'"));
		$incentive_amount = isset($sq_incentive['incentive_amount']) ? $sq_incentive['incentive_amount'] : 0;

		// Tax calculations
		$service_tax_amount = 0;
		if ($row_package['tour_service_tax_subtotal'] !== 0.00 && ($row_package['tour_service_tax_subtotal']) !== '') {
			$service_tax_subtotal1 = explode(',', $row_package['tour_service_tax_subtotal']);
			for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
				$service_tax = explode(':', $service_tax_subtotal1[$i]);
				$service_tax_amount +=  $service_tax[2];
			}
		}

		// Tour date
		$tour_date = get_date_user($row_package['tour_from_date']) . ' To ' . get_date_user($row_package['tour_to_date']);

		// Tour name
		$tour_name = 'Package Tour';

		// Build data array
		$temp_arr = array("data" => array(
			$count,
			get_package_booking_id($row_package['booking_id'], $year),
			$customer_name,
			$contact_no,
			$email_id,
		$sq_total_member,
		get_date_user($row_package['booking_date']),
		'<button class="btn btn-info btn-sm" onclick="package_view_modal(' . $row_package['booking_id'] . ')"><i class="fa fa-eye"></i></button>',
			$row_package['tour_type'],
			$row_package['tour_name'],
			$tour_date,
			number_format($row_package['basic_amount'], 2),
			number_format($row_package['service_charge'], 2),
			number_format($service_tax_amount, 2),
			number_format($row_package['tcs_per'], 2),
			number_format($row_package['tds'], 2),
			number_format((float)($credit_card_charges), 2),
			number_format($tour_fee, 2),
			number_format((float)($tour_esti), 2),
			number_format($total_amount, 2),
			number_format($total_paid, 2),
			'<button class="btn btn-info btn-sm" id="paymentv_btn-' . $row_package['booking_id'] . '" onclick="package_payment_view_modal(' . $row_package['booking_id'] . ')" data-toggle="tooltip" title="View Details"><i class="fa fa-eye"></i></button>',
			number_format((float)($total_balance), 2),
			($row_package['due_date'] == '1970-01-01') ? get_date_user($row_package['booking_date']) : get_date_user($row_package['due_date']),
			number_format((float)($total_purchase), 2).$currency_amount_3,
			'<button class="btn btn-info btn-sm" id="supplierv_btn-' . $row_package['booking_id'] . '" onclick="package_supplier_view_modal(' . $row_package['booking_id'] . ')" data-toggle="tooltip" title="View Details"><i class="fa fa-eye"></i></button>',
			$branch_name,
			$emp_name,
			number_format((float)($incentive_amount), 2)
		), "bg" => $bg);
		array_push($array_s, $temp_arr);

		// Update totals
		$global_total_cancel += $tour_esti;
		$global_total_sale += $total_amount;
		$global_total_paid += $total_paid;
		$global_total_balance += $total_balance;
	}
}

// ===== GROUP TOUR =====
if ($travel_type == "" || $travel_type == "Group Tour") {
	$query = "select * from tourwise_traveler_details where 1 ";
	$query = apply_common_filters($query, $customer_id, $from_date, $to_date, $cust_type, $company_name, $booker_id, $branch_id, 'form_date');

	$sq_group = mysqlQuery($query);
	while ($row_group = mysqli_fetch_assoc($sq_group)) {
		$count++;

	$bg = "";
	$pass_count = mysqli_num_rows(mysqlQuery("select * from  travelers_details where traveler_group_id='$row_group[traveler_group_id]'"));
	$cancelpass_count = mysqli_num_rows(mysqlQuery("select * from  travelers_details where traveler_group_id='$row_group[traveler_group_id]' and status='Cancel'"));
	if ($row_booking['tour_group_status'] == "Cancel") {
		$bg = "danger";
		$sq_total_member = 0;
	} else {
		if ($pass_count == $cancelpass_count) {
			$bg = "danger";
		} else
			$bg = "#fff";
	}


$sq_group1 = mysqli_fetch_assoc(mysqlQuery("select * from tour_groups where group_id='$row_group[tour_group_id]'"));
	$tour = $sq_tour['tour_name'];
	$group = get_date_user($sq_group1['from_date']) . ' To ' . get_date_user($sq_group1['to_date']);

		// Get tour master info
		$sq_tour_master = mysqli_fetch_assoc(mysqlQuery("select * from tour_master where tour_id='$row_group[tour_id]'"));

		// Customer info
		$cust_info = get_customer_info($row_group['customer_id']);
		$customer_name = $cust_info['name'];
		$contact_no = $cust_info['contact'];
		$email_id = $cust_info['email'];

		// Employee and branch info
		$emp_branch = get_emp_branch_info($row_group['emp_id']);
		$emp_name = $emp_branch['emp_name'];
		$branch_name = $emp_branch['branch_name'];

		// Pax count
		$sq_total_member = mysqli_num_rows(mysqlQuery("select traveler_id from travelers_details where traveler_group_id = '$row_group[traveler_group_id]'"));

		// Payment calculations
		$sq_paid_amount = mysqli_fetch_assoc(mysqlQuery("SELECT sum(amount) as sum,sum(credit_charges) as sumc from payment_master where tourwise_traveler_id='$row_group[id]' and clearance_status!='Pending' and  clearance_status!='Cancelled'"));
		$credit_card_charges = $sq_paid_amount['sumc'];
		$total_paid = $sq_paid_amount['sum'];
		if ($total_paid == '') $total_paid = 0;

		// Sale and cancel amounts
		$tour_fee = $row_group['total_tour_fee'] + $row_group['total_travel_expense'];
		$cancel_amount = $row_group['total_refund_amount'];
		$total_amount = $tour_fee - $cancel_amount;

		// Balance calculation
		$total_balance = $total_amount - $total_paid;

		// Purchase calculations
		$total_purchase = 0;
		$sq_purchase = mysqlQuery("select * from vendor_estimate where status!='Cancel' and estimate_type='Group Tour' and estimate_type_id='$row_group[traveler_group_id]' and delete_status='0'");
		while ($row_purchase = mysqli_fetch_assoc($sq_purchase)) {
			if ($row_purchase['purchase_return'] == 0) {
				$total_purchase += $row_purchase['net_total'];
			} else if ($row_purchase['purchase_return'] == 2) {
				$cancel_estimate = json_decode($row_purchase['cancel_estimate']);
				$p_purchase = ($row_purchase['net_total'] - (float)($cancel_estimate[0]->net_total));
				$total_purchase += $p_purchase;
			}
		}
		$vendor_name = 'NA';
		if ($total_purchase > 0) {
			$sq_purchase1 = mysqli_fetch_assoc(mysqlQuery("select * from vendor_estimate where status!='Cancel' and estimate_type='Group Tour' and estimate_type_id='$row_group[traveler_group_id]' and delete_status='0'"));
			$vendor_type = $sq_purchase1['vendor_type'];
			$vendor_type_id = $sq_purchase1['vendor_type_id'];
			$vendor_name = get_vendor_name_report($vendor_type, $vendor_type_id);
		}

		// purchase currency conversion
		$currency_amount_3 = '';
		if($total_purchase > 0){
			$row_purchase1 = mysqli_fetch_assoc(mysqlQuery("select * from vendor_estimate where status!='Cancel' and estimate_type='Group Tour' and estimate_type_id='$row_group[traveler_group_id]' and delete_status='0'"));
			if($row_purchase1['currency_code'] !='0' && $currency != $row_purchase1['currency_code']){
				$currency_amount3 = currency_conversion($currency,$row_purchase1['currency_code'],$total_purchase);
				$currency_amount_3 = ' ('.$currency_amount3.')';
			}
		}

		// Incentive (placeholder)
		$incentive_amount = 0;

		// Tax calculations (simplified)
		$service_tax_amount = 0;
		$tcs_amount = 0;
		$tds_amount = 0;

		// Tour date
		$tour_date = get_date_user($sq_tour_master['from_date']) . ' To ' . get_date_user($sq_tour_master['to_date']);

		// Tour name
		$tour_name = 'Group Tour';

		// Amount calculations for Group Tour
		$basic_amount = $row_group['basic_amount'];
		$service_charge = 0;
		if ($row_group['service_tax'] !== 0.00 && ($row_group['service_tax']) !== '') {
			$service_tax_subtotal1 = explode(',', $row_group['service_tax']);
			for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
				$service_tax = explode(':', $service_tax_subtotal1[$i]);
				$service_charge += $service_tax[2];
			}
		}
		$tax_amount = $service_charge;
		$tcs_amount = $row_group['tcs_tax'];
		$sale_amount = $row_group['net_total'] - $cancel_amount;
		$total_amount = $sale_amount;

		$total_balance=$total_amount-$total_paid;
		// Build data array
		$temp_arr = array("data" => array(
			$count,
			get_group_booking_id($row_group['id'], $year),
			$customer_name,
			$contact_no,
			$email_id,
		$sq_total_member,
		get_date_user($row_group['form_date']),
		'<button class="btn btn-info btn-sm" onclick="group_view_modal(' . $row_group['id'] . ')"><i class="fa fa-eye"></i></button>',
			$sq_tour_master['tour_type'],
			$sq_tour_master['tour_name'],
			$group,
			number_format($basic_amount, 2),
			number_format($service_charge, 2),
			number_format($tax_amount, 2),
			number_format($tcs_amount, 2),
			number_format($tds_amount, 2),
			number_format((float)($credit_card_charges), 2),
			number_format($sale_amount, 2),
			number_format((float)($cancel_amount), 2),
			number_format($total_amount, 2),
			number_format($total_paid, 2),
			'<button class="btn btn-info btn-sm" id="paymentv_btn-' . $row_group['id'] . '" onclick="group_payment_view_modal(' . $row_group['id'] . ')" data-toggle="tooltip" title="View Details"><i class="fa fa-eye"></i></button>',
			number_format((float)($total_balance), 2),
			get_date_user($row_group['form_date']),
			number_format((float)($total_purchase), 2).$currency_amount_3,
			'<button class="btn btn-info btn-sm" id="supplierv_btn-' . $row_group['id'] . '" onclick="group_supplier_view_modal(' . $row_group['id'] . ')" data-toggle="tooltip" title="View Details"><i class="fa fa-eye"></i></button>',
			$branch_name,
			$emp_name,
			number_format((float)($incentive_amount), 2)
		), "bg" => $bg);
		array_push($array_s, $temp_arr);

		// Update totals
		$global_total_cancel += $cancel_amount;
		$global_total_sale += $total_amount;
		$global_total_paid += $total_paid;
		$global_total_balance += $total_balance;
	}
}

// ===== HOTEL BOOKING =====
if ($travel_type == "" || $travel_type == "Hotel") {
	$query = "select * from hotel_booking_master where 1 and delete_status='0' ";
	$query = apply_common_filters($query, $customer_id, $from_date, $to_date, $cust_type, $company_name, $booker_id, $branch_id);

	$hotel_result = mysqlQuery($query);
	while ($row_hotel = mysqli_fetch_assoc($hotel_result)) {
		$count++;

	$pass_count = mysqli_num_rows(mysqlQuery("select * from hotel_booking_entries where booking_id='$row_hotel[booking_id]'"));
	$cancel_count = mysqli_num_rows(mysqlQuery("select * from hotel_booking_entries where booking_id='$row_hotel[booking_id]' and status='Cancel'"));
	if ($pass_count == $cancel_count) {
		$bg = "danger";
	} else {
		$bg = "#fff";
	}

		// Customer info
		$cust_info = get_customer_info($row_hotel['customer_id']);
		$customer_name = $cust_info['name'];
		$contact_no = $cust_info['contact'];
		$email_id = $cust_info['email'];

		// Employee and branch info
		$emp_branch = get_emp_branch_info($row_hotel['emp_id']);
		$emp_name = $emp_branch['emp_name'];
		$branch_name = $emp_branch['branch_name'];

		// Pax count
		$sq_total_member = mysqli_num_rows(mysqlQuery("select booking_id from hotel_booking_entries where booking_id = '$row_hotel[booking_id]'"));

		// Payment calculations
		$sq_paid_amount = mysqli_fetch_assoc(mysqlQuery("SELECT sum(payment_amount) as sum ,sum(credit_charges) as sumc from hotel_booking_payment where booking_id='$row_hotel[booking_id]' and clearance_status!='Pending' and clearance_status!='Cancelled'"));
		$credit_card_charges = $sq_paid_amount['sumc'];
		$total_paid = $sq_paid_amount['sum'];
		if ($total_paid == '') $total_paid = 0;

		// Sale and cancel amounts
		$sale_amount = $row_hotel['total_fee'] - $row_hotel['cancel_amount'];
		$cancel_amount = $row_hotel['cancel_amount'];
		$total_amount = $sale_amount;

		// Balance calculation
		$total_balance = $total_amount - $total_paid;

		// Purchase calculations
		$total_purchase = 0;
		$sq_purchase = mysqlQuery("select * from vendor_estimate where status!='Cancel' and estimate_type='Hotel' and estimate_type_id='$row_hotel[booking_id]' and delete_status='0'");
		while ($row_purchase = mysqli_fetch_assoc($sq_purchase)) {
			if ($row_purchase['purchase_return'] == 0) {
				$total_purchase += $row_purchase['net_total'];
			} else if ($row_purchase['purchase_return'] == 2) {
				$cancel_estimate = json_decode($row_purchase['cancel_estimate']);
				$p_purchase = ($row_purchase['net_total'] - (float)($cancel_estimate[0]->net_total));
				$total_purchase += $p_purchase;
			}
		}
		$vendor_name = 'NA';
		if ($total_purchase > 0) {
			$sq_purchase1 = mysqli_fetch_assoc(mysqlQuery("select * from vendor_estimate where status!='Cancel' and estimate_type='Hotel' and estimate_type_id='$row_hotel[booking_id]' and delete_status='0'"));
			$vendor_type = $sq_purchase1['vendor_type'];
			$vendor_type_id = $sq_purchase1['vendor_type_id'];
			$vendor_name = get_vendor_name_report($vendor_type, $vendor_type_id);
		}

		// purchase currency conversion
		$currency_amount_3 = '';
		if($total_purchase > 0){
			$row_purchase1 = mysqli_fetch_assoc(mysqlQuery("select * from vendor_estimate where status!='Cancel' and estimate_type='Hotel' and estimate_type_id='$row_hotel[booking_id]' and delete_status='0'"));
			if($row_purchase1['currency_code'] !='0' && $currency != $row_purchase1['currency_code']){
				$currency_amount3 = currency_conversion($currency,$row_purchase1['currency_code'],$total_purchase);
				$currency_amount_3 = ' ('.$currency_amount3.')';
			}
		}

		// Incentive
		$incentive_amount = 0;

		// Tax calculations (simplified)
		$service_tax_amount = 0;
		$tcs_amount = 0;
		$tds_amount = 0;

		// Tour date
		$tour_date = 'NA';

		// Tour name
		$tour_name = 'NA';

		// Amount calculations for Hotel
		$basic_amount = $row_hotel['sub_total'];
		$service_charge = $row_hotel['service_charge'] + $row_hotel['markup'];

		// Tax calculation
		$tax_amount = 0;
		if ($row_hotel['service_tax_subtotal'] !== 0.00 && ($row_hotel['service_tax_subtotal']) !== '') {
			$service_tax_subtotal1 = explode(',', $row_hotel['service_tax_subtotal']);
			for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
				$service_tax = explode(':', $service_tax_subtotal1[$i]);
				$tax_amount += $service_tax[2];
			}
		}
		$markupservice_tax_amount = 0;
		if ($row_hotel['markup_tax'] !== 0.00 && $row_hotel['markup_tax'] !== "") {
			$service_tax_markup1 = explode(',', $row_hotel['markup_tax']);
			for ($i = 0; $i < sizeof($service_tax_markup1); $i++) {
				$service_tax = explode(':', $service_tax_markup1[$i]);
				$markupservice_tax_amount += $service_tax[2];
			}
		}
		$tax_amount = $tax_amount + $markupservice_tax_amount;

		$tcs_amount = $row_hotel['tcs_tax'];
		$sale_amount = $row_hotel['total_fee'] - $row_hotel['cancel_amount'];
		$total_amount = $sale_amount;

		// Build data array
		$temp_arr = array("data" => array(
			$count,
			get_hotel_booking_id($row_hotel['booking_id'], $year),
			$customer_name,
			$contact_no,
			$email_id,
		$sq_total_member,
		get_date_user($row_hotel['created_at']),
		'<button class="btn btn-info btn-sm" onclick="hotel_view_modal(' . $row_hotel['booking_id'] . ')"><i class="fa fa-eye"></i></button>',
			'NA',
			$tour_name,
			$tour_date,
			number_format($basic_amount, 2),
			number_format($service_charge, 2),
			number_format($tax_amount, 2),
			number_format($tcs_amount, 2),
			number_format($tds_amount, 2),
			number_format((float)($credit_card_charges), 2),
			number_format($sale_amount, 2),
			number_format((float)($cancel_amount), 2),
			number_format($total_amount, 2),
			number_format($total_paid, 2),
			'<button class="btn btn-info btn-sm" id="paymentv_btn-' . $row_hotel['booking_id'] . '" onclick="hotel_payment_view_modal(' . $row_hotel['booking_id'] . ')" data-toggle="tooltip" title="View Details"><i class="fa fa-eye"></i></button>',
			number_format((float)($total_balance), 2),
			get_date_user($row_hotel['created_at']),
			number_format((float)($total_purchase), 2).$currency_amount_3,
			'<button class="btn btn-info btn-sm" id="supplierv_btn-' . $row_hotel['booking_id'] . '" onclick="hotel_supplier_view_modal(' . $row_hotel['booking_id'] . ')" data-toggle="tooltip" title="View Details"><i class="fa fa-eye"></i></button>',
			$branch_name,
			$emp_name,
			number_format((float)($incentive_amount), 2)
		), "bg" => $bg);
		array_push($array_s, $temp_arr);

		// Update totals
		$global_total_cancel += $cancel_amount;
		$global_total_sale += $total_amount;
		$global_total_paid += $total_paid;
		$global_total_balance += $total_balance;
	}
}

// ===== FLIGHT BOOKING =====
if ($travel_type == "" || $travel_type == "Flight") {
	$query = "select * from ticket_master where 1 and delete_status='0' ";
	$query = apply_common_filters($query, $customer_id, $from_date, $to_date, $cust_type, $company_name, $booker_id, $branch_id);

	$sq_flight = mysqlQuery($query);
	while ($row_flight = mysqli_fetch_assoc($sq_flight)) {
		$count++;


		$pass_count = mysqli_num_rows(mysqlQuery("select * from ticket_master_entries where ticket_id='$row_flight[ticket_id]'"));
	$cancel_count = mysqli_num_rows(mysqlQuery("select * from ticket_master_entries where ticket_id='$row_flight[ticket_id]' and status='Cancel'"));

	if ($pass_count == $cancel_count) {
		$bg = "danger";
	} else {
		$bg = "#fff";
	}
		// Customer info
		$cust_info = get_customer_info($row_flight['customer_id']);
		$customer_name = $cust_info['name'];
		$contact_no = $cust_info['contact'];
		$email_id = $cust_info['email'];

		// Employee and branch info
		$emp_branch = get_emp_branch_info($row_flight['emp_id']);
		$emp_name = $emp_branch['emp_name'];
		$branch_name = $emp_branch['branch_name'];

		// Pax count
		$sq_total_member = mysqli_num_rows(mysqlQuery("select ticket_id from ticket_master_entries where ticket_id = '$row_flight[ticket_id]'"));

		// Payment calculations
		$sq_paid_amount = mysqli_fetch_assoc(mysqlQuery("SELECT sum(payment_amount) as sum, sum(credit_charges) as sumc from ticket_payment_master where ticket_id='$row_flight[ticket_id]' and clearance_status!='Pending' and clearance_status!='Cancelled'"));
		$credit_card_charges = $sq_paid_amount['sumc'];
		$total_paid = $sq_paid_amount['sum'];
		if ($total_paid == '') $total_paid = 0;

		// Sale and cancel amounts
		$total_sale = $row_flight['ticket_total_cost'];
		$cancel_amount = $row_flight['cancel_amount'];
		$total_amount = $total_sale - $cancel_amount;

		// Balance calculation
		$total_balance = $total_amount - $total_paid;

		// Purchase calculations
		$total_purchase = 0;
		$sq_purchase = mysqlQuery("select * from vendor_estimate where status!='Cancel' and estimate_type='Flight' and estimate_type_id='$row_flight[ticket_id]' and delete_status='0'");
		while ($row_purchase = mysqli_fetch_assoc($sq_purchase)) {
			if ($row_purchase['purchase_return'] == 0) {
				$total_purchase += $row_purchase['net_total'];
			} else if ($row_purchase['purchase_return'] == 2) {
				$cancel_estimate = json_decode($row_purchase['cancel_estimate']);
				$p_purchase = ($row_purchase['net_total'] - (float)($cancel_estimate[0]->net_total));
				$total_purchase += $p_purchase;
			}
		}
		$vendor_name = 'NA';
		if ($total_purchase > 0) {
			$sq_purchase1 = mysqli_fetch_assoc(mysqlQuery("select * from vendor_estimate where status!='Cancel' and estimate_type='Flight' and estimate_type_id='$row_flight[ticket_id]' and delete_status='0'"));
			$vendor_type = $sq_purchase1['vendor_type'];
			$vendor_type_id = $sq_purchase1['vendor_type_id'];
			$vendor_name = get_vendor_name_report($vendor_type, $vendor_type_id);
		}

		// purchase currency conversion
		$currency_amount_3 = '';
		if($total_purchase > 0){
			$row_purchase1 = mysqli_fetch_assoc(mysqlQuery("select * from vendor_estimate where status!='Cancel' and estimate_type='Flight' and estimate_type_id='$row_flight[ticket_id]' and delete_status='0'"));
			if($row_purchase1['currency_code'] !='0' && $currency != $row_purchase1['currency_code']){
				$currency_amount3 = currency_conversion($currency,$row_purchase1['currency_code'],$total_purchase);
				$currency_amount_3 = ' ('.$currency_amount3.')';
			}
		}

		// Incentive
		$incentive_amount = 0;

		// Tax calculations (simplified)
		$service_tax_amount = 0;
		$tcs_amount = 0;
		$tds_amount = 0;

		// Tour date
		$tour_date = 'NA';

		// Tour name
		$tour_name = 'NA';

		// Amount calculations for Flight
		$basic_amount = $row_flight['basic_cost'];

		// Service charge calculation
		$other_charges = $row_flight['other_charges'] ?? 0;
		$service_charge = $row_flight['service_charge'] + $row_flight['markup'];

		// Tax calculation
		$tax_amount = 0;
		if ($row_flight['service_tax_subtotal'] !== 0.00 && ($row_flight['service_tax_subtotal']) !== '') {
			$service_tax_subtotal1 = explode(',', $row_flight['service_tax_subtotal']);
			for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
				$service_tax = explode(':', $service_tax_subtotal1[$i]);
				$tax_amount += $service_tax[2];
			}
		}
		$markupservice_tax_amount = 0;
		if ($row_flight['service_tax_markup'] !== 0.00 && $row_flight['service_tax_markup'] !== "") {
			$service_tax_markup1 = explode(',', $row_flight['service_tax_markup']);
			for ($i = 0; $i < sizeof($service_tax_markup1); $i++) {
				$service_tax = explode(':', $service_tax_markup1[$i]);
				$markupservice_tax_amount += $service_tax[2];
			}
		}
		$tax_amount = $tax_amount + $markupservice_tax_amount;

		$tcs_amount = $row_flight['tds']; // This is actually TDS, not TCS
		$sale_amount = $row_flight['ticket_total_cost'] - $cancel_amount;
		$total_amount = $sale_amount;

		// Build data array
		$temp_arr = array("data" => array(
			$count,
			get_ticket_booking_id($row_flight['ticket_id'], $year),
			$customer_name,
			$contact_no,
			$email_id,
		$sq_total_member,
		get_date_user($row_flight['created_at']),
		'<button class="btn btn-info btn-sm" onclick="ticket_view_modal(' . $row_flight['ticket_id'] . ')"><i class="fa fa-eye"></i></button>',
			$row_flight['tour_type'],
			$tour_name,
			$tour_date,
			number_format($basic_amount, 2),
			number_format($service_charge, 2),
			number_format($tax_amount, 2),
			number_format($tcs_amount, 2),
			number_format($tds_amount, 2),
			number_format((float)($credit_card_charges), 2),
			number_format($sale_amount, 2),
			number_format((float)($cancel_amount), 2),
			number_format($total_amount, 2),
			number_format($total_paid, 2),
			'<button class="btn btn-info btn-sm" id="paymentv_btn-' . $row_flight['ticket_id'] . '" onclick="ticket_payment_view_modal(' . $row_flight['ticket_id'] . ')" data-toggle="tooltip" title="View Details"><i class="fa fa-eye"></i></button>',
			number_format((float)($total_balance), 2),
			get_date_user($row_flight['created_at']),
			number_format((float)($total_purchase), 2).$currency_amount_3,
			'<button class="btn btn-info btn-sm" id="supplierv_btn-' . $row_flight['ticket_id'] . '" onclick="ticket_supplier_view_modal(' . $row_flight['ticket_id'] . ')" data-toggle="tooltip" title="View Details"><i class="fa fa-eye"></i></button>',
			$branch_name,
			$emp_name,
			number_format((float)($incentive_amount), 2)
		), "bg" => $bg);
		array_push($array_s, $temp_arr);

		// Update totals
		$global_total_cancel += $cancel_amount;
		$global_total_sale += $total_amount;
		$global_total_paid += $total_paid;
		$global_total_balance += $total_balance;
	}
}

// ===== VISA BOOKING =====
if ($travel_type == "" || $travel_type == "Visa") {
	$query = "select * from visa_master where 1 and delete_status='0' ";
	$query = apply_common_filters($query, $customer_id, $from_date, $to_date, $cust_type, $company_name, $booker_id, $branch_id);

	$sq_visa = mysqlQuery($query);
	while ($row_visa = mysqli_fetch_assoc($sq_visa)) {
		$count++;

		$pass_count = mysqli_num_rows(mysqlQuery("select * from  visa_master_entries where visa_id='$row_visa[visa_id]'"));
	$cancel_count = mysqli_num_rows(mysqlQuery("select * from  visa_master_entries where visa_id='$row_visa[visa_id]' and status='Cancel'"));
	$bg = "";
	if ($pass_count == $cancel_count) {
		$bg = "danger";
	} else {
		$bg = "#fff";
	}

		// Customer info
		$cust_info = get_customer_info($row_visa['customer_id']);
		$customer_name = $cust_info['name'];
		$contact_no = $cust_info['contact'];
		$email_id = $cust_info['email'];

		// Employee and branch info
		$emp_branch = get_emp_branch_info($row_visa['emp_id']);
		$emp_name = $emp_branch['emp_name'];
		$branch_name = $emp_branch['branch_name'];

		// Pax count
		$sq_total_member = mysqli_num_rows(mysqlQuery("select visa_id from visa_master_entries where visa_id = '$row_visa[visa_id]'"));

		// Payment calculations
		$sq_paid_amount = mysqli_fetch_assoc(mysqlQuery("SELECT sum(payment_amount) as sum, sum(credit_charges) as sumc from visa_payment_master where visa_id='$row_visa[visa_id]' and clearance_status!='Pending' and clearance_status!='Cancelled'"));
		$credit_card_charges = $sq_paid_amount['sumc'];
		$total_paid = $sq_paid_amount['sum'];
		if ($total_paid == '') $total_paid = 0;

		// Sale and cancel amounts
		$total_sale = $row_visa['visa_total_cost'];
		$cancel_amount = $row_visa['cancel_amount'];
		$total_amount = $total_sale - $cancel_amount;

		// Balance calculation
		$total_balance = $total_amount - $total_paid;

		// Purchase calculations
		$total_purchase = 0;
		$sq_purchase = mysqlQuery("select * from vendor_estimate where status!='Cancel' and estimate_type='Visa' and estimate_type_id='$row_visa[visa_id]' and delete_status='0'");
		while ($row_purchase = mysqli_fetch_assoc($sq_purchase)) {
			if ($row_purchase['purchase_return'] == 0) {
				$total_purchase += $row_purchase['net_total'];
			} else if ($row_purchase['purchase_return'] == 2) {
				$cancel_estimate = json_decode($row_purchase['cancel_estimate']);
				$p_purchase = ($row_purchase['net_total'] - (float)($cancel_estimate[0]->net_total));
				$total_purchase += $p_purchase;
			}
		}
		$vendor_name = 'NA';
		if ($total_purchase > 0) {
			$sq_purchase1 = mysqli_fetch_assoc(mysqlQuery("select * from vendor_estimate where status!='Cancel' and estimate_type='Visa' and estimate_type_id='$row_visa[visa_id]' and delete_status='0'"));
			$vendor_type = $sq_purchase1['vendor_type'];
			$vendor_type_id = $sq_purchase1['vendor_type_id'];
			$vendor_name = get_vendor_name_report($vendor_type, $vendor_type_id);
		}

		// purchase currency conversion
		$currency_amount_3 = '';
		if($total_purchase > 0){
			$row_purchase1 = mysqli_fetch_assoc(mysqlQuery("select * from vendor_estimate where status!='Cancel' and estimate_type='Visa' and estimate_type_id='$row_visa[visa_id]' and delete_status='0'"));
			if($row_purchase1['currency_code'] !='0' && $currency != $row_purchase1['currency_code']){
				$currency_amount3 = currency_conversion($currency,$row_purchase1['currency_code'],$total_purchase);
				$currency_amount_3 = ' ('.$currency_amount3.')';
			}
		}

		// Incentive
		$incentive_amount = 0;

		// Tax calculations (simplified)
		$service_tax_amount = 0;
		$tcs_amount = 0;
		$tds_amount = 0;

		// Tour date
		$tour_date = 'NA';

		// Tour name
		$tour_name = 'NA';

		// Amount calculations for Visa
		$basic_amount = $row_visa['visa_issue_amount'];
		$service_charge = $row_visa['service_charge'] + $row_visa['markup'];

		// Tax calculation
		$tax_amount = 0;
		if ($row_visa['service_tax_subtotal'] !== 0.00 && ($row_visa['service_tax_subtotal']) !== '') {
			$service_tax_subtotal1 = explode(',', $row_visa['service_tax_subtotal']);
			for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
				$service_tax = explode(':', $service_tax_subtotal1[$i]);
				$tax_amount += $service_tax[2];
			}
		}
		$markupservice_tax_amount = 0;
		if ($row_visa['markup_tax'] !== 0.00 && $row_visa['markup_tax'] !== "") {
			$service_tax_markup1 = explode(',', $row_visa['markup_tax']);
			for ($i = 0; $i < sizeof($service_tax_markup1); $i++) {
				$service_tax = explode(':', $service_tax_markup1[$i]);
				$markupservice_tax_amount += $service_tax[2];
			}
		}
		$tax_amount = $tax_amount + $markupservice_tax_amount;

		$sale_amount = $row_visa['visa_total_cost'] - $cancel_amount;
		$total_amount = $sale_amount;

		// Build data array
		$temp_arr = array("data" => array(
			$count,
			get_visa_booking_id($row_visa['visa_id'], $year),
			$customer_name,
			$contact_no,
			$email_id,
		$sq_total_member,
		get_date_user($row_visa['created_at']),
		'<button class="btn btn-info btn-sm" onclick="visa_view_modal(' . $row_visa['visa_id'] . ')"><i class="fa fa-eye"></i></button>',
			'NA',
			$tour_name,
			$tour_date,
			number_format($basic_amount, 2),
			number_format($service_charge, 2),
			number_format($tax_amount, 2),
			number_format($tcs_amount, 2),
			number_format($tds_amount, 2),
			number_format((float)($credit_card_charges), 2),
			number_format($sale_amount, 2),
			number_format((float)($cancel_amount), 2),
			number_format($total_amount, 2),
			number_format($total_paid, 2),
			'<button class="btn btn-info btn-sm" id="paymentv_btn-' . $row_visa['visa_id'] . '" onclick="visa_payment_view_modal(' . $row_visa['visa_id'] . ')" data-toggle="tooltip" title="View Details"><i class="fa fa-eye"></i></button>',
			number_format((float)($total_balance), 2),
			get_date_user($row_visa['created_at']),
			number_format((float)($total_purchase), 2).$currency_amount_3,
			'<button class="btn btn-info btn-sm" id="supplierv_btn-' . $row_visa['visa_id'] . '" onclick="visa_supplier_view_modal(' . $row_visa['visa_id'] . ')" data-toggle="tooltip" title="View Details"><i class="fa fa-eye"></i></button>',
			$branch_name,
			$emp_name,
			number_format((float)($incentive_amount), 2)
		), "bg" => $bg);
		array_push($array_s, $temp_arr);

		// Update totals
		$global_total_cancel += $cancel_amount;
		$global_total_sale += $total_amount;
		$global_total_paid += $total_paid;
		$global_total_balance += $total_balance;
	}
}

// ===== CAR RENTAL BOOKING =====
if ($travel_type == "" || $travel_type == "Car Rental") {
	$query = "select * from car_rental_booking where 1 and delete_status='0' ";
	$query = apply_common_filters($query, $customer_id, $from_date, $to_date, $cust_type, $company_name, $booker_id, $branch_id);

	$sq_car = mysqlQuery($query);
	while ($row_car = mysqli_fetch_assoc($sq_car)) {
		$count++;


		$bg = "";
	
	($row_car['status'] == 'Cancel') ? $bg = 'danger' : $bg = 'fff';

		// Customer info
		$cust_info = get_customer_info($row_car['customer_id']);
		$customer_name = $cust_info['name'];
		$contact_no = $cust_info['contact'];
		$email_id = $cust_info['email'];

		// Employee and branch info
		$emp_branch = get_emp_branch_info($row_car['emp_id']);
		$emp_name = $emp_branch['emp_name'];
		$branch_name = $emp_branch['branch_name'];

		// Pax count (simplified for car rental)
		$sq_total_member = 1;

		// Payment calculations
		$sq_paid_amount = mysqli_fetch_assoc(mysqlQuery("select sum(payment_amount) as sum,sum(credit_charges) as sumc from car_rental_payment where booking_id='$row_car[booking_id]' and clearance_status!='Pending' and clearance_status!='Cancelled'"));
		$credit_card_charges = $sq_paid_amount['sumc'];
		$total_paid = $sq_paid_amount['sum'];
		if ($total_paid == '') $total_paid = 0;

		// Sale and cancel amounts
		$total_sale = $row_car['total_fees'];
		$cancel_amount = $row_car['cancel_amount'];
		$total_amount = $total_sale - $cancel_amount;

		// Balance calculation
		$total_balance = $total_amount - $total_paid;

		// Purchase calculations
		$total_purchase = 0;
		$sq_purchase = mysqlQuery("select * from vendor_estimate where status!='Cancel' and estimate_type='Car Rental' and estimate_type_id='$row_car[booking_id]' and delete_status='0'");
		while ($row_purchase = mysqli_fetch_assoc($sq_purchase)) {
			if ($row_purchase['purchase_return'] == 0) {
				$total_purchase += $row_purchase['net_total'];
			} else if ($row_purchase['purchase_return'] == 2) {
				$cancel_estimate = json_decode($row_purchase['cancel_estimate']);
				$p_purchase = ($row_purchase['net_total'] - (float)($cancel_estimate[0]->net_total));
				$total_purchase += $p_purchase;
			}
		}
		$vendor_name = 'NA';
		if ($total_purchase > 0) {
			$sq_purchase1 = mysqli_fetch_assoc(mysqlQuery("select * from vendor_estimate where status!='Cancel' and estimate_type='Car Rental' and estimate_type_id='$row_car[booking_id]' and delete_status='0'"));
			$vendor_type = $sq_purchase1['vendor_type'];
			$vendor_type_id = $sq_purchase1['vendor_type_id'];
			$vendor_name = get_vendor_name_report($vendor_type, $vendor_type_id);
		}

		// purchase currency conversion
		$currency_amount_3 = '';
		if($total_purchase > 0){
			$row_purchase1 = mysqli_fetch_assoc(mysqlQuery("select * from vendor_estimate where status!='Cancel' and estimate_type='Car Rental' and estimate_type_id='$row_car[booking_id]' and delete_status='0'"));
			if($row_purchase1['currency_code'] !='0' && $currency != $row_purchase1['currency_code']){
				$currency_amount3 = currency_conversion($currency,$row_purchase1['currency_code'],$total_purchase);
				$currency_amount_3 = ' ('.$currency_amount3.')';
			}
		}

		// Incentive
		$incentive_amount = 0;

		// Tax calculations (simplified)
		$service_tax_amount = 0;
		$tcs_amount = 0;
		$tds_amount = 0;

		// Tour date
		$tour_date = 'NA';

		// Tour name
		$tour_name = 'NA';

		// Amount calculations for Car Rental
		$basic_amount = $row_car['basic_amount'];
		$service_charge = $row_car['service_charge'] + $row_car['markup_cost'];

		// Tax calculation
		$tax_amount = 0;
		if ($row_car['service_tax_subtotal'] !== 0.00 && ($row_car['service_tax_subtotal']) !== '') {
			$service_tax_subtotal1 = explode(',', $row_car['service_tax_subtotal']);
			for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
				$service_tax = explode(':', $service_tax_subtotal1[$i]);
				$tax_amount += $service_tax[2];
			}
		}
		$markupservice_tax_amount = 0;
		if ($row_car['markup_cost_subtotal'] !== 0.00 && $row_car['markup_cost_subtotal'] !== "") {
			$service_tax_markup1 = explode(',', $row_car['markup_cost_subtotal']);
			for ($i = 0; $i < sizeof($service_tax_markup1); $i++) {
				$service_tax = explode(':', $service_tax_markup1[$i]);
				$markupservice_tax_amount += $service_tax[2];
			}
		}
		$tax_amount = $tax_amount + $markupservice_tax_amount;

		$sale_amount = $row_car['total_fees'] - $cancel_amount;
		$total_amount = $sale_amount;

		// Build data array
		$temp_arr = array("data" => array(
			$count,
			get_car_rental_booking_id($row_car['booking_id'], $year),
			$customer_name,
			$contact_no,
			$email_id,
		$sq_total_member,
		get_date_user($row_car['created_at']),
		'<button class="btn btn-info btn-sm" onclick="car_view_modal(' . $row_car['booking_id'] . ')"><i class="fa fa-eye"></i></button>',
			'NA',
			$tour_name,
			$tour_date,
			number_format($basic_amount, 2),
			number_format($service_charge, 2),
			number_format($tax_amount, 2),
			number_format($tcs_amount, 2),
			number_format($tds_amount, 2),
			number_format((float)($credit_card_charges), 2),
			number_format($sale_amount, 2),
			number_format((float)($cancel_amount), 2),
			number_format($total_amount, 2),
			number_format($total_paid, 2),
			'<button class="btn btn-info btn-sm" id="paymentv_btn-' . $row_car['booking_id'] . '" onclick="car_payment_view_modal(' . $row_car['booking_id'] . ')" data-toggle="tooltip" title="View Details"><i class="fa fa-eye"></i></button>',
			number_format((float)($total_balance), 2),
			get_date_user($row_car['created_at']),
			number_format((float)($total_purchase), 2).$currency_amount_3,
			'<button class="btn btn-info btn-sm" id="supplierv_btn-' . $row_car['booking_id'] . '" onclick="car_supplier_view_modal(' . $row_car['booking_id'] . ')" data-toggle="tooltip" title="View Details"><i class="fa fa-eye"></i></button>',
			$branch_name,
			$emp_name,
			number_format((float)($incentive_amount), 2)
		), "bg" => $bg);
		array_push($array_s, $temp_arr);

		// Update totals
		$global_total_cancel += $cancel_amount;
		$global_total_sale += $total_amount;
		$global_total_paid += $total_paid;
		$global_total_balance += $total_balance;
	}
}

// ===== ACTIVITY/EXCURSION BOOKING =====
if ($travel_type == "" || $travel_type == "Activity") {
	$query = "select * from excursion_master where 1 and delete_status='0' ";
	$query = apply_common_filters($query, $customer_id, $from_date, $to_date, $cust_type, $company_name, $booker_id, $branch_id);

	$sq_activity = mysqlQuery($query);
	while ($row_activity = mysqli_fetch_assoc($sq_activity)) {
		$count++;


		$pass_count = mysqli_num_rows(mysqlQuery("select * from excursion_master_entries where exc_id='$row_activity[exc_id]'"));
	$cancel_count = mysqli_num_rows(mysqlQuery("select * from excursion_master_entries where exc_id='$row_activity[exc_id]' and status='Cancel'"));
	if ($pass_count == $cancel_count) {
		$bg = "danger";
	} else {
		$bg = "#fff";
	}

		// Customer info
		$cust_info = get_customer_info($row_activity['customer_id']);
		$customer_name = $cust_info['name'];
		$contact_no = $cust_info['contact'];
		$email_id = $cust_info['email'];

		// Employee and branch info
		$emp_branch = get_emp_branch_info($row_activity['emp_id']);
		$emp_name = $emp_branch['emp_name'];
		$branch_name = $emp_branch['branch_name'];

		// Pax count
		$sq_total_member = mysqli_num_rows(mysqlQuery("select exc_id from excursion_master_entries where exc_id = '$row_activity[exc_id]'"));

		// Payment calculations
		$sq_paid_amount = mysqli_fetch_assoc(mysqlQuery("SELECT sum(payment_amount) as sum, sum(credit_charges) as sumc from exc_payment_master where exc_id='$row_activity[exc_id]' and clearance_status!='Pending' and clearance_status!='Cancelled'"));
		$credit_card_charges = $sq_paid_amount['sumc'];
		$total_paid = $sq_paid_amount['sum'];
		if ($total_paid == '') $total_paid = 0;

		// Sale and cancel amounts
		$total_sale = $row_activity['exc_total_cost'];
		$cancel_amount = $row_activity['cancel_amount'];
		$total_amount = $total_sale - $cancel_amount;

		// Balance calculation
		$total_balance = $total_amount - $total_paid;

		// Purchase calculations
		$total_purchase = 0;
		$sq_purchase = mysqlQuery("select * from vendor_estimate where status!='Cancel' and estimate_type='Activity' and estimate_type_id='$row_activity[exc_id]' and delete_status='0'");
		while ($row_purchase = mysqli_fetch_assoc($sq_purchase)) {
			if ($row_purchase['purchase_return'] == 0) {
				$total_purchase += $row_purchase['net_total'];
			} else if ($row_purchase['purchase_return'] == 2) {
				$cancel_estimate = json_decode($row_purchase['cancel_estimate']);
				$p_purchase = ($row_purchase['net_total'] - (float)($cancel_estimate[0]->net_total));
				$total_purchase += $p_purchase;
			}
		}
		$vendor_name = 'NA';
		if ($total_purchase > 0) {
			$sq_purchase1 = mysqli_fetch_assoc(mysqlQuery("select * from vendor_estimate where status!='Cancel' and estimate_type='Activity' and estimate_type_id='$row_activity[exc_id]' and delete_status='0'"));
			$vendor_type = $sq_purchase1['vendor_type'];
			$vendor_type_id = $sq_purchase1['vendor_type_id'];
			$vendor_name = get_vendor_name_report($vendor_type, $vendor_type_id);
		}

		// purchase currency conversion
		$currency_amount_3 = '';
		if($total_purchase > 0){
			$row_purchase1 = mysqli_fetch_assoc(mysqlQuery("select * from vendor_estimate where status!='Cancel' and estimate_type='Activity' and estimate_type_id='$row_activity[exc_id]' and delete_status='0'"));
			if($row_purchase1['currency_code'] !='0' && $currency != $row_purchase1['currency_code']){
				$currency_amount3 = currency_conversion($currency,$row_purchase1['currency_code'],$total_purchase);
				$currency_amount_3 = ' ('.$currency_amount3.')';
			}
		}

		// Incentive
		$incentive_amount = 0;

		// Tax calculations (simplified)
		$service_tax_amount = 0;
		$tcs_amount = 0;
		$tds_amount = 0;

		// Tour date
		$tour_date = 'NA';

		// Tour name
		$tour_name = 'NA';

		// Amount calculations for Activity
		$basic_amount = $row_activity['exc_issue_amount'] ?? 0; // Use exc_issue_amount if available, else 0
		$service_charge = $row_activity['service_charge'] + $row_activity['markup'];

		// Tax calculation
		$tax_amount = 0;
		if ($row_activity['service_tax_subtotal'] !== 0.00 && ($row_activity['service_tax_subtotal']) !== '') {
			$service_tax_subtotal1 = explode(',', $row_activity['service_tax_subtotal']);
			for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
				$service_tax = explode(':', $service_tax_subtotal1[$i]);
				$tax_amount += $service_tax[2];
			}
		}
		$markupservice_tax_amount = 0;
		if ($row_activity['service_tax_markup'] !== 0.00 && $row_activity['service_tax_markup'] !== "") {
			$service_tax_markup1 = explode(',', $row_activity['service_tax_markup']);
			for ($i = 0; $i < sizeof($service_tax_markup1); $i++) {
				$service_tax = explode(':', $service_tax_markup1[$i]);
				$markupservice_tax_amount += $service_tax[2];
			}
		}
		$tax_amount = $tax_amount + $markupservice_tax_amount;

		$sale_amount = $row_activity['exc_total_cost'] - $cancel_amount;
		$total_amount = $sale_amount;

		// Build data array
		$temp_arr = array("data" => array(
			$count,
			get_exc_booking_id($row_activity['exc_id'], $year),
			$customer_name,
			$contact_no,
			$email_id,
		$sq_total_member,
		get_date_user($row_activity['created_at']),
		'<button class="btn btn-info btn-sm" onclick="exc_view_modal(' . $row_activity['exc_id'] . ')"><i class="fa fa-eye"></i></button>',
			'NA',
			$tour_name,
			$tour_date,
			number_format($basic_amount, 2),
			number_format($service_charge, 2),
			number_format($tax_amount, 2),
			number_format($tcs_amount, 2),
			number_format($tds_amount, 2),
			number_format((float)($credit_card_charges), 2),
			number_format($sale_amount, 2),
			number_format((float)($cancel_amount), 2),
			number_format($total_amount, 2),
			number_format($total_paid, 2),
			'<button class="btn btn-info btn-sm" id="paymentv_btn-' . $row_activity['exc_id'] . '" onclick="exc_payment_view_modal(' . $row_activity['exc_id'] . ')" data-toggle="tooltip" title="View Details"><i class="fa fa-eye"></i></button>',
			number_format((float)($total_balance), 2),
			get_date_user($row_activity['created_at']),
			number_format((float)($total_purchase), 2).$currency_amount_3,
			'<button class="btn btn-info btn-sm" id="supplierv_btn-' . $row_activity['exc_id'] . '" onclick="exc_supplier_view_modal(' . $row_activity['exc_id'] . ')" data-toggle="tooltip" title="View Details"><i class="fa fa-eye"></i></button>',
			$branch_name,
			$emp_name,
			number_format((float)($incentive_amount), 2)
		), "bg" => $bg);
		array_push($array_s, $temp_arr);

		// Update totals
		$global_total_cancel += $cancel_amount;
		$global_total_sale += $total_amount;
		$global_total_paid += $total_paid;
		$global_total_balance += $total_balance;
	}
}

// ===== BUS BOOKING =====
if ($travel_type == "" || $travel_type == "Bus") {
	$query = "select * from bus_booking_master where 1 and delete_status='0' ";
	$query = apply_common_filters($query, $customer_id, $from_date, $to_date, $cust_type, $company_name, $booker_id, $branch_id);

	$sq_bus = mysqlQuery($query);
	while ($row_bus = mysqli_fetch_assoc($sq_bus)) {
		$count++;


		$pass_count = mysqli_num_rows(mysqlQuery("select * from bus_booking_entries where booking_id='$row_bus[booking_id]'"));
	$cancel_count = mysqli_num_rows(mysqlQuery("select * from bus_booking_entries where booking_id='$row_bus[booking_id]' and status='Cancel'"));
	if ($pass_count == $cancel_count) {
		$bg = "danger";
	} else {
		$bg = "#fff";
	}
		// Customer info
		$cust_info = get_customer_info($row_bus['customer_id']);
		$customer_name = $cust_info['name'];
		$contact_no = $cust_info['contact'];
		$email_id = $cust_info['email'];

		// Employee and branch info
		$emp_branch = get_emp_branch_info($row_bus['emp_id']);
		$emp_name = $emp_branch['emp_name'];
		$branch_name = $emp_branch['branch_name'];

		// Pax count
		$sq_total_member = mysqli_num_rows(mysqlQuery("select booking_id from bus_booking_entries where booking_id = '$row_bus[booking_id]'"));

		// Payment calculations
		$sq_paid_amount = mysqli_fetch_assoc(mysqlQuery("SELECT sum(payment_amount) as sum, sum(credit_charges) as sumc from bus_booking_payment_master where booking_id='$row_bus[booking_id]' and clearance_status!='Pending' and clearance_status!='Cancelled'"));
		$credit_card_charges = $sq_paid_amount['sumc'];
		$total_paid = $sq_paid_amount['sum'];
		if ($total_paid == '') $total_paid = 0;

		// Sale and cancel amounts
		$total_sale = $row_bus['net_total'];
		$cancel_amount = $row_bus['cancel_amount'];
		$total_amount = $total_sale - $cancel_amount;

		// Balance calculation
		$total_balance = $total_amount - $total_paid;

		// Purchase calculations
		$total_purchase = 0;
		$sq_purchase = mysqlQuery("select * from vendor_estimate where status!='Cancel' and estimate_type='Bus' and estimate_type_id='$row_bus[booking_id]' and delete_status='0'");
		while ($row_purchase = mysqli_fetch_assoc($sq_purchase)) {
			if ($row_purchase['purchase_return'] == 0) {
				$total_purchase += $row_purchase['net_total'];
			} else if ($row_purchase['purchase_return'] == 2) {
				$cancel_estimate = json_decode($row_purchase['cancel_estimate']);
				$p_purchase = ($row_purchase['net_total'] - (float)($cancel_estimate[0]->net_total));
				$total_purchase += $p_purchase;
			}
		}
		$vendor_name = 'NA';
		if ($total_purchase > 0) {
			$sq_purchase1 = mysqli_fetch_assoc(mysqlQuery("select * from vendor_estimate where status!='Cancel' and estimate_type='Bus' and estimate_type_id='$row_bus[booking_id]' and delete_status='0'"));
			$vendor_type = $sq_purchase1['vendor_type'];
			$vendor_type_id = $sq_purchase1['vendor_type_id'];
			$vendor_name = get_vendor_name_report($vendor_type, $vendor_type_id);
		}

		// purchase currency conversion
		$currency_amount_3 = '';
		if($total_purchase > 0){
			$row_purchase1 = mysqli_fetch_assoc(mysqlQuery("select * from vendor_estimate where status!='Cancel' and estimate_type='Bus' and estimate_type_id='$row_bus[booking_id]' and delete_status='0'"));
			if($row_purchase1['currency_code'] !='0' && $currency != $row_purchase1['currency_code']){
				$currency_amount3 = currency_conversion($currency,$row_purchase1['currency_code'],$total_purchase);
				$currency_amount_3 = ' ('.$currency_amount3.')';
			}
		}

		// Incentive
		$incentive_amount = 0;

		// Tax calculations (simplified)
		$service_tax_amount = 0;
		$tcs_amount = 0;
		$tds_amount = 0;

		// Tour date
		$tour_date = 'NA';

		// Tour name
		$tour_name = 'NA';

		// Amount calculations for Bus
		$basic_amount = $row_bus['basic_cost'];
		$service_charge = $row_bus['service_charge'] + $row_bus['markup'];

		// Tax calculation
		$tax_amount = 0;
		if ($row_bus['service_tax_subtotal'] !== 0.00 && ($row_bus['service_tax_subtotal']) !== '') {
			$service_tax_subtotal1 = explode(',', $row_bus['service_tax_subtotal']);
			for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
				$service_tax = explode(':', $service_tax_subtotal1[$i]);
				$tax_amount += $service_tax[2];
			}
		}
		$markupservice_tax_amount = 0;
		if ($row_bus['markup_tax'] !== 0.00 && $row_bus['markup_tax'] !== "") {
			$service_tax_markup1 = explode(',', $row_bus['markup_tax']);
			for ($i = 0; $i < sizeof($service_tax_markup1); $i++) {
				$service_tax = explode(':', $service_tax_markup1[$i]);
				$markupservice_tax_amount += $service_tax[2];
			}
		}
		$tax_amount = $tax_amount + $markupservice_tax_amount;

		$sale_amount = $row_bus['net_total'] - $cancel_amount;
		$total_amount = $sale_amount;

		// Build data array
		$temp_arr = array("data" => array(
			$count,
			get_bus_booking_id($row_bus['booking_id'], $year),
			$customer_name,
			$contact_no,
			$email_id,
		$sq_total_member,
		get_date_user($row_bus['created_at']),
		'<button class="btn btn-info btn-sm" onclick="bus_view_modal(' . $row_bus['booking_id'] . ')"><i class="fa fa-eye"></i></button>',
			'NA',
			$tour_name,
			$tour_date,
			number_format($basic_amount, 2),
			number_format($service_charge, 2),
			number_format($tax_amount, 2),
			number_format($tcs_amount, 2),
			number_format($tds_amount, 2),
			number_format((float)($credit_card_charges), 2),
			number_format($sale_amount, 2),
			number_format((float)($cancel_amount), 2),
			number_format($total_amount, 2),
			number_format($total_paid, 2),
			'<button class="btn btn-info btn-sm" id="paymentv_btn-' . $row_bus['booking_id'] . '" onclick="bus_payment_view_modal(' . $row_bus['booking_id'] . ')" data-toggle="tooltip" title="View Details"><i class="fa fa-eye"></i></button>',
			number_format((float)($total_balance), 2),
			get_date_user($row_bus['created_at']),
			number_format((float)($total_purchase), 2).$currency_amount_3,
			'<button class="btn btn-info btn-sm" id="supplierv_btn-' . $row_bus['booking_id'] . '" onclick="bus_supplier_view_modal(' . $row_bus['booking_id'] . ')" data-toggle="tooltip" title="View Details"><i class="fa fa-eye"></i></button>',
			$branch_name,
			$emp_name,
			number_format((float)($incentive_amount), 2)
		), "bg" => $bg);
		array_push($array_s, $temp_arr);

		// Update totals
		$global_total_cancel += $cancel_amount;
		$global_total_sale += $total_amount;
		$global_total_paid += $total_paid;
		$global_total_balance += $total_balance;
	}
}

// ===== TRAIN BOOKING =====
if ($travel_type == "" || $travel_type == "Train") {
	$query = "select * from train_ticket_master where 1 and delete_status='0' ";
	$query = apply_common_filters($query, $customer_id, $from_date, $to_date, $cust_type, $company_name, $booker_id, $branch_id);

	$sq_train = mysqlQuery($query);
	while ($row_train = mysqli_fetch_assoc($sq_train)) {
		$count++;


		$pass_count = mysqli_num_rows(mysqlQuery("select * from  train_ticket_master_entries where train_ticket_id='$row_train[train_ticket_id]'"));
	$cancel_count = mysqli_num_rows(mysqlQuery("select * from  train_ticket_master_entries where train_ticket_id='$row_train[train_ticket_id]' and status='Cancel'"));
	if ($pass_count == $cancel_count) {
		$bg = "danger";
	} else {
		$bg = "#fff";
	}
		// Customer info
		$cust_info = get_customer_info($row_train['customer_id']);
		$customer_name = $cust_info['name'];
		$contact_no = $cust_info['contact'];
		$email_id = $cust_info['email'];

		// Employee and branch info
		$emp_branch = get_emp_branch_info($row_train['emp_id']);
		$emp_name = $emp_branch['emp_name'];
		$branch_name = $emp_branch['branch_name'];

		// Pax count
		$sq_total_member = mysqli_num_rows(mysqlQuery("select train_ticket_id from train_ticket_master_entries where train_ticket_id = '$row_train[train_ticket_id]'"));

		// Payment calculations
		$sq_paid_amount = mysqli_fetch_assoc(mysqlQuery("SELECT sum(payment_amount) as sum, sum(credit_charges) as sumc from train_ticket_payment_master where train_ticket_id='$row_train[train_ticket_id]' and clearance_status!='Pending' and clearance_status!='Cancelled'"));
		$credit_card_charges = $sq_paid_amount['sumc'];
		$total_paid = $sq_paid_amount['sum'];
		if ($total_paid == '') $total_paid = 0;

		// Sale and cancel amounts
		$total_sale = $row_train['net_total'];
		$cancel_amount = $row_train['cancel_amount'];
		$total_amount = $total_sale - $cancel_amount;

		// Balance calculation
		$total_balance = $total_amount - $total_paid;

		// Purchase calculations
		$total_purchase = 0;
		$sq_purchase = mysqlQuery("select * from vendor_estimate where status!='Cancel' and estimate_type='Train' and estimate_type_id='$row_train[train_ticket_id]' and delete_status='0'");
		while ($row_purchase = mysqli_fetch_assoc($sq_purchase)) {
			if ($row_purchase['purchase_return'] == 0) {
				$total_purchase += $row_purchase['net_total'];
			} else if ($row_purchase['purchase_return'] == 2) {
				$cancel_estimate = json_decode($row_purchase['cancel_estimate']);
				$p_purchase = ($row_purchase['net_total'] - (float)($cancel_estimate[0]->net_total));
				$total_purchase += $p_purchase;
			}
		}
		$vendor_name = 'NA';
		if ($total_purchase > 0) {
			$sq_purchase1 = mysqli_fetch_assoc(mysqlQuery("select * from vendor_estimate where status!='Cancel' and estimate_type='Train' and estimate_type_id='$row_train[train_ticket_id]' and delete_status='0'"));
			$vendor_type = $sq_purchase1['vendor_type'];
			$vendor_type_id = $sq_purchase1['vendor_type_id'];
			$vendor_name = get_vendor_name_report($vendor_type, $vendor_type_id);
		}

		// purchase currency conversion
		$currency_amount_3 = '';
		if($total_purchase > 0){
			$row_purchase1 = mysqli_fetch_assoc(mysqlQuery("select * from vendor_estimate where status!='Cancel' and estimate_type='Train' and estimate_type_id='$row_train[train_ticket_id]' and delete_status='0'"));
			if($row_purchase1['currency_code'] !='0' && $currency != $row_purchase1['currency_code']){
				$currency_amount3 = currency_conversion($currency,$row_purchase1['currency_code'],$total_purchase);
				$currency_amount_3 = ' ('.$currency_amount3.')';
			}
		}

		// Incentive
		$incentive_amount = 0;

		// Tax calculations (simplified)
		$service_tax_amount = 0;
		$tcs_amount = 0;
		$tds_amount = 0;

		// Tour date
		$tour_date = 'NA';

		// Tour name
		$tour_name = 'NA';

		// Amount calculations for Train
		$basic_amount = $row_train['basic_fair'];
		$service_charge = $row_train['service_charge'];

		// Tax calculation
		$tax_amount = 0;
		if ($row_train['service_tax_subtotal'] !== 0.00 && ($row_train['service_tax_subtotal']) !== '') {
			$service_tax_subtotal1 = explode(',', $row_train['service_tax_subtotal']);
			for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
				$service_tax = explode(':', $service_tax_subtotal1[$i]);
				$tax_amount += $service_tax[2];
			}
		}

		$sale_amount = $row_train['net_total'] - $cancel_amount;
		$total_amount = $sale_amount;

		// Build data array
		$temp_arr = array("data" => array(
			$count,
			get_train_ticket_booking_id($row_train['train_ticket_id'], $year),
			$customer_name,
			$contact_no,
			$email_id,
		$sq_total_member,
		get_date_user($row_train['created_at']),
		'<button class="btn btn-info btn-sm" onclick="train_view_modal(' . $row_train['train_ticket_id'] . ')"><i class="fa fa-eye"></i></button>',
			'NA',
			$tour_name,
			$tour_date,
			number_format($basic_amount, 2),
			number_format($service_charge, 2),
			number_format($tax_amount, 2),
			number_format($tcs_amount, 2),
			number_format($tds_amount, 2),
			number_format((float)($credit_card_charges), 2),
			number_format($sale_amount, 2),
			number_format((float)($cancel_amount), 2),
			number_format($total_amount, 2),
			number_format($total_paid, 2),
			'<button class="btn btn-info btn-sm" id="paymentv_btn-' . $row_train['train_ticket_id'] . '" onclick="train_payment_view_modal(' . $row_train['train_ticket_id'] . ')" data-toggle="tooltip" title="View Details"><i class="fa fa-eye"></i></button>',
			number_format((float)($total_balance), 2),
			get_date_user($row_train['created_at']),
			number_format((float)($total_purchase), 2).$currency_amount_3,
			'<button class="btn btn-info btn-sm" id="supplierv_btn-' . $row_train['train_ticket_id'] . '" onclick="train_supplier_view_modal(' . $row_train['train_ticket_id'] . ')" data-toggle="tooltip" title="View Details"><i class="fa fa-eye"></i></button>',
			$branch_name,
			$emp_name,
			number_format((float)($incentive_amount), 2)
		), "bg" => $bg);
		array_push($array_s, $temp_arr);

		// Update totals
		$global_total_cancel += $cancel_amount;
		$global_total_sale += $total_amount;
		$global_total_paid += $total_paid;
		$global_total_balance += $total_balance;
	}
}

// ===== MISCELLANEOUS BOOKING =====
if ($travel_type == "" || $travel_type == "Miscellaneous") {
	$query = "select * from miscellaneous_master where 1 and delete_status='0' ";
	$query = apply_common_filters($query, $customer_id, $from_date, $to_date, $cust_type, $company_name, $booker_id, $branch_id);

	$sq_misc = mysqlQuery($query);
	while ($row_misc = mysqli_fetch_assoc($sq_misc)) {
		$count++;


		$pass_count = mysqli_num_rows(mysqlQuery("select * from  miscellaneous_master_entries where misc_id='$row_misc[misc_id]'"));
	$cancel_count = mysqli_num_rows(mysqlQuery("select * from  miscellaneous_master_entries where misc_id='$row_misc[misc_id]' and status='Cancel'"));
	$bg = "";
	if ($pass_count == $cancel_count) {
		$bg = "danger";
	} else {
		$bg = "#fff";
	}
		// Customer info
		$cust_info = get_customer_info($row_misc['customer_id']);
		$customer_name = $cust_info['name'];
		$contact_no = $cust_info['contact'];
		$email_id = $cust_info['email'];

		// Employee and branch info
		$emp_branch = get_emp_branch_info($row_misc['emp_id']);
		$emp_name = $emp_branch['emp_name'];
		$branch_name = $emp_branch['branch_name'];

		// Pax count (simplified)
		$sq_total_member = 1;

		// Payment calculations
		$sq_paid_amount = mysqli_fetch_assoc(mysqlQuery("SELECT sum(payment_amount) as sum, sum(credit_charges) as sumc from miscellaneous_payment_master where misc_id='$row_misc[misc_id]' and clearance_status!='Pending' and clearance_status!='Cancelled'"));
		$credit_card_charges = $sq_paid_amount['sumc'];
		$total_paid = $sq_paid_amount['sum'];
		if ($total_paid == '') $total_paid = 0;

		// Sale and cancel amounts
		$total_sale = $row_misc['misc_total_cost'];
		$cancel_amount = $row_misc['cancel_amount'];
		$total_amount = $total_sale - $cancel_amount;

		// Balance calculation
		$total_balance = $total_amount - $total_paid;

		// Purchase calculations
		$total_purchase = 0;
		$sq_purchase = mysqlQuery("select * from vendor_estimate where status!='Cancel' and estimate_type='Miscellaneous' and estimate_type_id='$row_misc[misc_id]' and delete_status='0'");
		while ($row_purchase = mysqli_fetch_assoc($sq_purchase)) {
			if ($row_purchase['purchase_return'] == 0) {
				$total_purchase += $row_purchase['net_total'];
			} else if ($row_purchase['purchase_return'] == 2) {
				$cancel_estimate = json_decode($row_purchase['cancel_estimate']);
				$p_purchase = ($row_purchase['net_total'] - (float)($cancel_estimate[0]->net_total));
				$total_purchase += $p_purchase;
			}
		}
		$vendor_name = 'NA';
		if ($total_purchase > 0) {
			$sq_purchase1 = mysqli_fetch_assoc(mysqlQuery("select * from vendor_estimate where status!='Cancel' and estimate_type='Miscellaneous' and estimate_type_id='$row_misc[misc_id]' and delete_status='0'"));
			$vendor_type = $sq_purchase1['vendor_type'];
			$vendor_type_id = $sq_purchase1['vendor_type_id'];
			$vendor_name = get_vendor_name_report($vendor_type, $vendor_type_id);
		}

		// purchase currency conversion
		$currency_amount_3 = '';
		if($total_purchase > 0){
			$row_purchase1 = mysqli_fetch_assoc(mysqlQuery("select * from vendor_estimate where status!='Cancel' and estimate_type='Miscellaneous' and estimate_type_id='$row_misc[misc_id]' and delete_status='0'"));
			if($row_purchase1['currency_code'] !='0' && $currency != $row_purchase1['currency_code']){
				$currency_amount3 = currency_conversion($currency,$row_purchase1['currency_code'],$total_purchase);
				$currency_amount_3 = ' ('.$currency_amount3.')';
			}
		}

		// Incentive
		$incentive_amount = 0;

		// Tax calculations (simplified)
		$service_tax_amount = 0;
		$tcs_amount = 0;
		$tds_amount = 0;

		// Tour date
		$tour_date = 'NA';

		// Tour name
		$tour_name = 'NA';

		// Amount calculations for Miscellaneous
		$basic_amount = $row_misc['misc_issue_amount'];
		$service_charge = $row_misc['service_charge'] + $row_misc['markup'];

		// Tax calculation
		$tax_amount = 0;
		if ($row_misc['service_tax_subtotal'] !== 0.00 && ($row_misc['service_tax_subtotal']) !== '') {
			$service_tax_subtotal1 = explode(',', $row_misc['service_tax_subtotal']);
			for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
				$service_tax = explode(':', $service_tax_subtotal1[$i]);
				$tax_amount += $service_tax[2];
			}
		}
		$markupservice_tax_amount = 0;
		if ($row_misc['service_tax_markup'] !== 0.00 && $row_misc['service_tax_markup'] !== "") {
			$service_tax_markup1 = explode(',', $row_misc['service_tax_markup']);
			for ($i = 0; $i < sizeof($service_tax_markup1); $i++) {
				$service_tax = explode(':', $service_tax_markup1[$i]);
				$markupservice_tax_amount += $service_tax[2];
			}
		}
		$tax_amount = $tax_amount + $markupservice_tax_amount;

		$sale_amount = $row_misc['misc_total_cost'] - $cancel_amount;
		$total_amount = $sale_amount;

		// Build data array
		$temp_arr = array("data" => array(
			$count,
			get_misc_booking_id($row_misc['misc_id'], $year),
			$customer_name,
			$contact_no,
			$email_id,
		$sq_total_member,
		get_date_user($row_misc['created_at']),
		'<button class="btn btn-info btn-sm" onclick="misc_view_modal(' . $row_misc['misc_id'] . ')"><i class="fa fa-eye"></i></button>',
			'NA',
			$tour_name,
			$tour_date,
			number_format($basic_amount, 2),
			number_format($service_charge, 2),
			number_format($tax_amount, 2),
			number_format($tcs_amount, 2),
			number_format($tds_amount, 2),
			number_format((float)($credit_card_charges), 2),
			number_format($sale_amount, 2),
			number_format((float)($cancel_amount), 2),
			number_format($total_amount, 2),
			number_format($total_paid, 2),
			'<button class="btn btn-info btn-sm" id="paymentv_btn-' . $row_misc['misc_id'] . '" onclick="misc_payment_view_modal(' . $row_misc['misc_id'] . ')" data-toggle="tooltip" title="View Details"><i class="fa fa-eye"></i></button>',
			number_format((float)($total_balance), 2),
			get_date_user($row_misc['created_at']),
			number_format((float)($total_purchase), 2).$currency_amount_3,
			'<button class="btn btn-info btn-sm" id="supplierv_btn-' . $row_misc['misc_id'] . '" onclick="misc_supplier_view_modal(' . $row_misc['misc_id'] . ')" data-toggle="tooltip" title="View Details"><i class="fa fa-eye"></i></button>',
			$branch_name,
			$emp_name,
			number_format((float)($incentive_amount), 2)
		), "bg" => $bg);
		array_push($array_s, $temp_arr);

		// Update totals
		$global_total_cancel += $cancel_amount;
		$global_total_sale += $total_amount;
		$global_total_paid += $total_paid;
		$global_total_balance += $total_balance;
	}
}

// Footer with totals
$footer_data = array(
	"footer_data" => array(
		'total_footers' => 6,
		'foot0' => "",
		'col0' => 12,
		'class0' => "",

		'foot1' => "TOTAL CANCEL : " . number_format($global_total_cancel, 2),
		'col1' => 2,
		'class1' => "danger text-right",

		'foot2' => "TOTAL SALE : " . number_format($global_total_sale, 2),
		'col2' => 2,
		'class2' => "info text-right",

		'foot3' => "TOTAL PAID : " . number_format($global_total_paid, 2),
		'col3' => 3,
		'class3' => "success text-right",

		'foot4' => "TOTAL BALANCE : " . number_format($global_total_balance, 2),
		'col4' => 3,
		'class4' => "warning text-right",

		'foot5' => "",
		'col5' => 11,
		'class5' => ""
	)
);
array_push($array_s, $footer_data);

echo json_encode($array_s);
?>
