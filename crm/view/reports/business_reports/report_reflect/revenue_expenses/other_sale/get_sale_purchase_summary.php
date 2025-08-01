<?php
include "../../../../../../model/model.php";
include_once('sale_type_generic_function.php');
$sale_type = $_POST['sale_type'];


global $currency, $$modify_entries_switch;
$emp_id = $_SESSION['emp_id'];
$role = $_SESSION['role'];
$role_id = $_SESSION['role_id'];
$branch_admin_id = $_SESSION['branch_admin_id'];
// $financial_year_id = $_POST['financial_year_id'];
// $branch_status = $_POST['branch_status'];

$booking_id = $_POST['booking_id'];

$from_date = $_POST['from_date'];
$to_date = $_POST['to_date'];
$tour_id = $_POST['tour_id'];
$group_id = $_POST['group_id'];


$sale_purchase_data = get_sale_purchase($sale_type);
$total_sale = $sale_purchase_data['total_sale'];
$total_purchase1 = $sale_purchase_data['total_purchase'];
$total_expense = isset($sale_purchase_data['total_expense']) ? $sale_purchase_data['total_expense'] : 0;
$array_s = array();
$temp_arr = array();
//Add other Expense
$total_purchase1 += $total_expense;

if ($total_sale > $total_purchase1) {
	$var = 'Total Profit';
} else {
	$var = 'Total Loss';
}
$profit_loss = $total_sale - $total_purchase1;

$profit_loss_per = 0;

if ($sale_type == 'Visa') {
	$count = 1;
	$sq_query = "select * from visa_master where delete_status='0'";

	if ($booking_id != "") {
		$sq_query .= " and visa_id='$booking_id'";
	}
	if ($from_date != "" && $to_date != "") {
		$from_date = get_date_db($from_date);
		$to_date = get_date_db($to_date);
		$sq_query .= " and date(created_at) between '$from_date' and '$to_date'";
	}
	$sq_query .= "order by visa_id desc";
	$query = mysqlQuery($sq_query);

	while ($row_visa = mysqli_fetch_assoc($query)) {

		$sq_customer = mysqli_fetch_assoc(mysqlQuery("select type,company_name,first_name,last_name from customer_master where customer_id='$row_visa[customer_id]'"));
		$customer_name = ($sq_customer['type'] == 'Corporate' || $sq_customer['type'] == 'B2B') ? $sq_customer['company_name'] : $sq_customer['first_name'] . ' ' . $sq_customer['last_name'];
		$vendor_type = '';
		$vendor_name = '';
		$total_purchase = 0;
		$sq_visa_entry = mysqli_num_rows(mysqlQuery("select * from visa_master_entries where visa_id='$row_visa[visa_id]'"));
		$sq_visa_cancel = mysqli_num_rows(mysqlQuery("select * from visa_master_entries where visa_id='$row_visa[visa_id]' and status = 'Cancel'"));

		$date = $row_visa['created_at'];
		$yr = explode("-", $date);
		$year = $yr[0];
		$sq_emp = mysqli_fetch_assoc(mysqlQuery("select * from emp_master where emp_id='$row_visa[emp_id]'"));
		$emp = ($row_visa['emp_id'] == 0) ? 'Admin' : $sq_emp['first_name'] . ' ' . $sq_emp['last_name'];

		$sq_paid_amount = mysqli_fetch_assoc(mysqlQuery("SELECT sum(credit_charges) as sumc from visa_payment_master where visa_id='$row_visa[visa_id]' and clearance_status!='Pending' and clearance_status!='Cancelled'"));
		$credit_charges = $sq_paid_amount['sumc'];

		//Service Tax and Markup Tax
		$service_tax_amount = 0;
		if ($row_visa['service_tax_subtotal'] !== 0.00 && ($row_visa['service_tax_subtotal']) !== '') {
			$service_tax_subtotal1 = explode(',', $row_visa['service_tax_subtotal']);
			for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
				$service_tax = explode(':', $service_tax_subtotal1[$i]);
				$service_tax_amount +=  $service_tax[2];
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

		$total_sale = $row_visa['visa_total_cost'] - $service_tax_amount - $markupservice_tax_amount + $credit_charges;

		//Purchase
		$sq_purchase = mysqlQuery("select * from vendor_estimate where estimate_type='Visa' and estimate_type_id ='$row_visa[visa_id]' and delete_status='0' and status !='Cancel'");
		while ($sq_pquery = mysqli_fetch_assoc($sq_purchase)) {
			if ($sq_pquery['purchase_return'] == 0 || $sq_pquery['purchase_return'] == 1) {
				$total_purchase += $sq_pquery['net_total'];
			} else if ($sq_pquery['purchase_return'] == 2) {
				$cancel_estimate = json_decode($sq_pquery['cancel_estimate']);
				$p_purchase = ($sq_pquery['net_total'] - (float)($cancel_estimate[0]->net_total) - (float)($cancel_estimate[0]->service_tax_subtotal));
				$total_purchase += $p_purchase;
			}
			//Service Tax 
			$service_tax_amount = 0;
			if ($sq_pquery['service_tax_subtotal'] !== 0.00 && ($sq_pquery['service_tax_subtotal']) !== '') {
				$service_tax_subtotal1 = explode(',', $sq_pquery['service_tax_subtotal']);
				for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
					$service_tax = explode(':', $service_tax_subtotal1[$i]);
					$service_tax_amount +=  $service_tax[2];
				}
			}
			$total_purchase -= $service_tax_amount;
			$vendor_type = $sq_pquery['vendor_type'];
			$vendor_name = get_vendor_name_report($sq_pquery['vendor_type'], $sq_pquery['vendor_type_id']);
		}

		$bg = '';
		if ($sq_visa_cancel == $sq_visa_entry) {
			$bg = 'danger';
			$total_sale = 0;
		}

		$profit_amount = $total_sale - $total_purchase;
		$profit_loss_per = ($total_sale > 0) ? ($profit_amount / $total_sale) * 100 : 0;
		$profit_loss_per = round($profit_loss_per, 2);
		$var = ($total_sale > $total_purchase) ? 'Profit' : 'Loss';

		$btn = 'NA';

		$temp_arr = array("data" => array(
			(int)($count++),
			get_visa_booking_id($row_visa['visa_id'], $year),
			get_date_user($row_visa['created_at']),
			$customer_name,
			number_format($total_sale, 2),
			// ($vendor_type !='')?$vendor_type:'NA',
			// ($vendor_name !='')?$vendor_name:'NA',
			'<button data-toggle="tooltip" class="btn btn-info btn-sm" onclick="purchases_display_modal(\'' . 'Visa' . '\',' . $row_visa['visa_id'] . ')" title="" data-original-title="View Details" id="supplierv_btn-' . $row_visa['visa_id'] . '"><i class="fa fa-eye"></i></button>',
			number_format($total_purchase, 2),
			$btn,
			'<b>' . number_format($profit_amount, 2) . '</b>' . ' (' . $profit_loss_per . '% ' . $var . ')',
			$emp
		), "bg" => $bg);
		array_push($array_s, $temp_arr);

	}
}
if ($sale_type == 'Excursion') {
	$count = 1;
	$sq_query = "select * from excursion_master where delete_status='0'";

	// Add booking ID condition if provided
	if (!empty($booking_id)) {
		$sq_query .= " AND exc_id='$booking_id'";
	}

	// Add date range condition if both dates are provided
	if (!empty($from_date) && !empty($to_date)) {
		$from_date_db = get_date_db($from_date);
		$to_date_db = get_date_db($to_date);
		$sq_query .= " AND DATE(created_at) BETWEEN '$from_date_db' AND '$to_date_db'";
	}

	// Add ordering clause
	$sq_query .= " ORDER BY exc_id DESC";
	$query = mysqlQuery($sq_query);

	while ($row_passport = mysqli_fetch_assoc($query)) {

		$sq_customer = mysqli_fetch_assoc(mysqlQuery("select type,company_name,first_name,last_name from customer_master where customer_id='$row_passport[customer_id]'"));
		$customer_name = ($sq_customer['type'] == 'Corporate' || $sq_customer['type'] == 'B2B') ? $sq_customer['company_name'] : $sq_customer['first_name'] . ' ' . $sq_customer['last_name'];
		$vendor_type = '';
		$vendor_name = '';
		$total_purchase = 0;
		$date = $row_passport['created_at'];
		$yr = explode("-", $date);
		$year = $yr[0];

		$sq_exc_entry = mysqli_num_rows(mysqlQuery("select * from excursion_master_entries where exc_id='$row_passport[exc_id]'"));
		$sq_exc_cancel = mysqli_num_rows(mysqlQuery("select * from excursion_master_entries where exc_id='$row_passport[exc_id]' and status = 'Cancel'"));
		$sq_emp = mysqli_fetch_assoc(mysqlQuery("select * from emp_master where emp_id='$row_passport[emp_id]'"));
		$emp = ($row_passport['emp_id'] == 0) ? 'Admin' : $sq_emp['first_name'] . ' ' . $sq_emp['last_name'];
		$sq_paid_amount = mysqli_fetch_assoc(mysqlQuery("SELECT sum(credit_charges) as sumc from exc_payment_master where exc_id='$row_passport[exc_id]' and clearance_status!='Pending' and clearance_status!='Cancelled'"));
		$credit_charges = $sq_paid_amount['sumc'];
		//// Calculate Service Tax//////
		$service_tax_amount = 0;
		if ($row_passport['service_tax_subtotal'] !== 0.00 && ($row_passport['service_tax_subtotal']) !== '') {
			$service_tax_subtotal1 = explode(',', $row_passport['service_tax_subtotal']);
			for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
				$service_tax = explode(':', $service_tax_subtotal1[$i]);
				$service_tax_amount +=  $service_tax[2];
			}
		}

		//// Calculate Markup Tax//////
		$markupservice_tax_amount = 0;
		if ($row_passport['service_tax_markup'] !== 0.00 && $row_passport['service_tax_markup'] !== "") {
			$service_tax_markup1 = explode(',', $row_passport['service_tax_markup']);
			for ($i = 0; $i < sizeof($service_tax_markup1); $i++) {
				$service_tax = explode(':', $service_tax_markup1[$i]);
				$markupservice_tax_amount += $service_tax[2];
			}
		}
		$total_sale = $row_passport['exc_total_cost'] - $service_tax_amount - $markupservice_tax_amount + $credit_charges;

		//Purchase
		$sq_purchase = mysqlQuery("select * from vendor_estimate where estimate_type='Activity' and estimate_type_id ='$row_passport[exc_id]' and delete_status='0' and status !='Cancel'");
		while ($sq_pquery = mysqli_fetch_assoc($sq_purchase)) {
			if ($sq_pquery['purchase_return'] == 0 || $sq_pquery['purchase_return'] == 1) {
				$total_purchase += $sq_pquery['net_total'];
			} else if ($sq_pquery['purchase_return'] == 2) {
				$cancel_estimate = json_decode($sq_pquery['cancel_estimate']);
				$p_purchase = ($sq_pquery['net_total'] - (float)($cancel_estimate[0]->net_total) - (float)($cancel_estimate[0]->service_tax_subtotal));
				$total_purchase += $p_purchase;
			}
			//Service Tax 
			$service_tax_amount = 0;
			if ($sq_pquery['service_tax_subtotal'] !== 0.00 && ($sq_pquery['service_tax_subtotal']) !== '') {
				$service_tax_subtotal1 = explode(',', $sq_pquery['service_tax_subtotal']);
				for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
					$service_tax = explode(':', $service_tax_subtotal1[$i]);
					$service_tax_amount +=  $service_tax[2];
				}
			}
			$total_purchase -= $service_tax_amount;
			$vendor_type = $sq_pquery['vendor_type'];
			$vendor_name = get_vendor_name_report($sq_pquery['vendor_type'], $sq_pquery['vendor_type_id']);
		}


		$bg = '';
		if ($sq_exc_cancel == $sq_exc_entry) {
			$bg = 'danger';
			$total_sale = 0;
		}

		$profit_amount = $total_sale - $total_purchase;
		$profit_loss_per = ($total_sale > 0) ? ($profit_amount / $total_sale) * 100 : 0;
		$profit_loss_per = round($profit_loss_per, 2);
		$var = ($total_sale > $total_purchase) ? 'Profit' : 'Loss';


		$btn = 'NA';
		$temp_arr = array("data" => array(
			(int)($count++),
			get_exc_booking_id($row_passport['exc_id'], $year),
			get_date_user($row_passport['created_at']),
			$customer_name,
			number_format($total_sale, 2),
			// ($vendor_type !='')?$vendor_type:'NA',
			// ($vendor_name !='')?$vendor_name:'NA',
			'<button data-toggle="tooltip" class="btn btn-info btn-sm" onclick="purchases_display_modal(\'' . 'Activity' . '\',' . $row_passport['exc_id'] . ')" title="" data-original-title="View Details" id="supplierv_btn-' . $row_passport['exc_id'] . '"><i class="fa fa-eye"></i></button>',
			number_format($total_purchase, 2),
			$btn,
			'<b>' . number_format($profit_amount, 2) . '</b>' . ' (' . $profit_loss_per . '% ' . $var . ')',
			$emp
		), "bg" => $bg);
		array_push($array_s, $temp_arr);
	}
}
if ($sale_type == 'Bus') {

	$count = 1;
	$sq_query = "select * from bus_booking_master where 1 and delete_status='0'";


	// Add booking ID condition if provided
	if (!empty($booking_id)) {
		$sq_query .= " AND booking_id='$booking_id'";
	}

	// Add date range condition if both dates are provided
	if (!empty($from_date) && !empty($to_date)) {
		$from_date_db = get_date_db($from_date);
		$to_date_db = get_date_db($to_date);
		$sq_query .= " AND DATE(created_at) BETWEEN '$from_date_db' AND '$to_date_db'";
	}

	// Add ordering clause
	$sq_query .= " ORDER BY booking_id DESC";

	// Execute the query
	$query = mysqlQuery($sq_query);


	while ($row_passport = mysqli_fetch_assoc($query)) {

		$sq_customer = mysqli_fetch_assoc(mysqlQuery("select type,company_name,first_name,last_name from customer_master where customer_id='$row_passport[customer_id]'"));
		$customer_name = ($sq_customer['type'] == 'Corporate' || $sq_customer['type'] == 'B2B') ? $sq_customer['company_name'] : $sq_customer['first_name'] . ' ' . $sq_customer['last_name'];
		$vendor_type = '';
		$vendor_name = '';
		$total_purchase = 0;
		$date = $row_passport['created_at'];
		$yr = explode("-", $date);
		$year = $yr[0];
		$sq_exc_entry = mysqli_num_rows(mysqlQuery("select * from bus_booking_entries where booking_id='$row_passport[booking_id]'"));
		$sq_exc_cancel = mysqli_num_rows(mysqlQuery("select * from bus_booking_entries where booking_id='$row_passport[booking_id]' and status = 'Cancel'"));

		$sq_emp = mysqli_fetch_assoc(mysqlQuery("select * from emp_master where emp_id='$row_passport[emp_id]'"));
		$emp = ($row_passport['emp_id'] == 0) ? 'Admin' : $sq_emp['first_name'] . ' ' . $sq_emp['last_name'];

		$sq_paid_amount = mysqli_fetch_assoc(mysqlQuery("SELECT sum(credit_charges) as sumc from bus_booking_payment_master where booking_id='$row_passport[booking_id]' and clearance_status!='Pending' and clearance_status!='Cancelled'"));
		$credit_charges = $sq_paid_amount['sumc'];

		//Service Tax and Markup Tax
		$service_tax_amount = 0;
		if ($row_passport['service_tax_subtotal'] !== 0.00 && ($row_passport['service_tax_subtotal']) !== '') {
			$service_tax_subtotal1 = explode(',', $row_passport['service_tax_subtotal']);
			for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
				$service_tax = explode(':', $service_tax_subtotal1[$i]);
				$service_tax_amount +=  $service_tax[2];
			}
		}
		$markupservice_tax_amount = 0;
		if ($row_passport['markup_tax'] !== 0.00 && $row_passport['markup_tax'] !== "") {
			$service_tax_markup1 = explode(',', $row_passport['markup_tax']);
			for ($i = 0; $i < sizeof($service_tax_markup1); $i++) {
				$service_tax = explode(':', $service_tax_markup1[$i]);
				$markupservice_tax_amount += $service_tax[2];
			}
		}
		$total_sale = $row_passport['net_total'] - $service_tax_amount - $markupservice_tax_amount + $credit_charges;

		//Purchase
		$sq_purchase = mysqlQuery("select * from vendor_estimate where estimate_type='Bus' and estimate_type_id ='$row_passport[booking_id]' and delete_status='0' and status !='Cancel'");
		while ($sq_pquery = mysqli_fetch_assoc($sq_purchase)) {
			if ($sq_pquery['purchase_return'] == 0 || $sq_pquery['purchase_return'] == 1) {
				$total_purchase += $sq_pquery['net_total'];
			} else if ($sq_pquery['purchase_return'] == 2) {
				$cancel_estimate = json_decode($sq_pquery['cancel_estimate']);
				$p_purchase = ($sq_pquery['net_total'] - (float)($cancel_estimate[0]->net_total) - (float)($cancel_estimate[0]->service_tax_subtotal));
				$total_purchase += $p_purchase;
			}
			//Service Tax 
			$service_tax_amount = 0;
			if ($sq_pquery['service_tax_subtotal'] !== 0.00 && ($sq_pquery['service_tax_subtotal']) !== '') {
				$service_tax_subtotal1 = explode(',', $sq_pquery['service_tax_subtotal']);
				for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
					$service_tax = explode(':', $service_tax_subtotal1[$i]);
					$service_tax_amount +=  $service_tax[2];
				}
			}
			$total_purchase -= $service_tax_amount;
			$vendor_type = $sq_pquery['vendor_type'];
			$vendor_name = get_vendor_name_report($sq_pquery['vendor_type'], $sq_pquery['vendor_type_id']);
		}


		$bg = '';
		if ($sq_exc_entry == $sq_exc_cancel) {
			$bg = 'danger';
			$total_sale = 0;
		}



		$profit_amount = $total_sale - $total_purchase;
		$profit_loss_per = ($total_sale > 0) ? ($profit_amount / $total_sale) * 100 : 0;
		$profit_loss_per = round($profit_loss_per, 2);
		$var = ($total_sale > $total_purchase) ? 'Profit' : 'Loss';


		$btn = 'NA';

		$temp_arr = array("data" => array(
			(int)($count++),
			get_bus_booking_id($row_passport['booking_id'], $year),
			get_date_user($row_passport['created_at']),
			$customer_name,
			number_format($total_sale, 2),
			// ($vendor_type !='')?$vendor_type:'NA',
			// ($vendor_name !='')?$vendor_name:'NA',
			'<button data-toggle="tooltip" class="btn btn-info btn-sm" onclick="purchases_display_modal(\'' . 'Bus' . '\',' . $row_passport['booking_id'] . ')" title="" data-original-title="View Details" id="supplierv_btn-' . $row_passport['booking_id'] . '"><i class="fa fa-eye"></i></button>',
			number_format($total_purchase, 2),
			$btn,
			'<b>' . number_format($profit_amount, 2) . '</b>' . ' (' . $profit_loss_per . '% ' . $var . ')',
			$emp
		), "bg" => $bg);
		array_push($array_s, $temp_arr);
	}
}
if ($sale_type == 'Hotel') {

	$count = 1;
	// 	$sq_query = "select * from hotel_booking_master where delete_status='0' order by booking_id desc";

	// 	if($booking_id!=""){
	// 		$sq_query .=" and booking_id='$booking_id'";
	// 	}
	// 	if($from_date!="" && $to_date!=""){
	// 		$from_date = get_date_db($from_date);
	// 		$to_date = get_date_db($to_date);
	// 		$sq_query .= " and date(created_at) between '$from_date' and '$to_date'";
	// 	}
	// $query=mysqlQuery($sq_query);



	$sq_query = "SELECT * FROM hotel_booking_master WHERE delete_status='0'";

	// Add booking ID condition if provided
	if (!empty($booking_id)) {
		$sq_query .= " AND booking_id='$booking_id'";
	}

	// Add date range condition if both dates are provided
	if (!empty($from_date) && !empty($to_date)) {
		$from_date_db = get_date_db($from_date);
		$to_date_db = get_date_db($to_date);
		$sq_query .= " AND DATE(created_at) BETWEEN '$from_date_db' AND '$to_date_db'";
	}

	// Add ordering clause
	$sq_query .= " ORDER BY booking_id DESC";

	// Execute the query
	$query = mysqlQuery($sq_query);

	while ($row_passport = mysqli_fetch_assoc($query)) {

		$sq_customer = mysqli_fetch_assoc(mysqlQuery("select type,company_name,first_name,last_name from customer_master where customer_id='$row_passport[customer_id]'"));
		$customer_name = ($sq_customer['type'] == 'Corporate' || $sq_customer['type'] == 'B2B') ? $sq_customer['company_name'] : $sq_customer['first_name'] . ' ' . $sq_customer['last_name'];
		$vendor_type = '';
		$vendor_name = '';
		$total_purchase = 0;
		$date = $row_passport['created_at'];
		$yr = explode("-", $date);
		$year = $yr[0];

		$sq_emp = mysqli_fetch_assoc(mysqlQuery("select * from emp_master where emp_id='$row_passport[emp_id]'"));
		$emp = ($row_passport['emp_id'] == 0) ? 'Admin' : $sq_emp['first_name'] . ' ' . $sq_emp['last_name'];
		$sq_paid_amount = mysqli_fetch_assoc(mysqlQuery("SELECT sum(credit_charges) as sumc from hotel_booking_payment where booking_id='$row_passport[booking_id]' and clearance_status!='Pending' and clearance_status!='Cancelled'"));
		$credit_charges = $sq_paid_amount['sumc'];
		//// Calculate Service Tax//////
		$service_tax_amount = 0;
		if ($row_passport['service_tax_subtotal'] !== 0.00 && ($row_passport['service_tax_subtotal']) !== '') {
			$service_tax_subtotal1 = explode(',', $row_passport['service_tax_subtotal']);
			for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
				$service_tax = explode(':', $service_tax_subtotal1[$i]);
				$service_tax_amount +=  $service_tax[2];
			}
		}
		//// Calculate Markup Tax//////
		$markupservice_tax_amount = 0;
		if ($row_passport['markup_tax'] !== 0.00 && $row_passport['markup_tax'] !== "") {
			$service_tax_markup1 = explode(',', $row_passport['markup_tax']);
			for ($i = 0; $i < sizeof($service_tax_markup1); $i++) {
				$service_tax = explode(':', $service_tax_markup1[$i]);
				$markupservice_tax_amount += $service_tax[2];
			}
		}
		$total_sale = $row_passport['total_fee'] - $service_tax_amount - $markupservice_tax_amount + $credit_charges;

		//Purchase
		$sq_purchase = mysqlQuery("select * from vendor_estimate where estimate_type='Hotel' and estimate_type_id ='$row_passport[booking_id]' and delete_status='0' and status !='Cancel'");
		while ($sq_pquery = mysqli_fetch_assoc($sq_purchase)) {
			if ($sq_pquery['purchase_return'] == 0 || $sq_pquery['purchase_return'] == 1) {
				$total_purchase += $sq_pquery['net_total'];
			} else if ($sq_pquery['purchase_return'] == 2) {
				$cancel_estimate = json_decode($sq_pquery['cancel_estimate']);
				$p_purchase = ($sq_pquery['net_total'] - (float)($cancel_estimate[0]->net_total) - (float)($cancel_estimate[0]->service_tax_subtotal));
				$total_purchase += $p_purchase;
			}
			//Service Tax 
			$service_tax_amount = 0;
			if ($sq_pquery['service_tax_subtotal'] !== 0.00 && ($sq_pquery['service_tax_subtotal']) !== '') {
				$service_tax_subtotal1 = explode(',', $sq_pquery['service_tax_subtotal']);
				for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
					$service_tax = explode(':', $service_tax_subtotal1[$i]);
					$service_tax_amount +=  $service_tax[2];
				}
			}
			$total_purchase -= $service_tax_amount;
			$vendor_type = $sq_pquery['vendor_type'];
			$vendor_name = get_vendor_name_report($sq_pquery['vendor_type'], $sq_pquery['vendor_type_id']);
		}


		$sq_exc_entry = mysqli_num_rows(mysqlQuery("select * from hotel_booking_entries where booking_id='$row_passport[booking_id]'"));
		$sq_exc_cancel = mysqli_num_rows(mysqlQuery("select * from hotel_booking_entries where booking_id='$row_passport[booking_id]' and status = 'Cancel'"));
		$bg = '';
		if ($sq_exc_entry == $sq_exc_cancel) {
			$bg = 'danger';
			$total_sale = 0;
		}


		$profit_amount = $total_sale - $total_purchase;
		$profit_loss_per = ($total_sale > 0) ? ($profit_amount / $total_sale) * 100 : 0;
		$profit_loss_per = round($profit_loss_per, 2);
		$var = ($total_sale > $total_purchase) ? 'Profit' : 'Loss';


		$btn = 'NA';
		$temp_arr = array("data" => array(
			(int)($count++),
			get_hotel_booking_id($row_passport['booking_id'], $year),
			get_date_user($row_passport['created_at']),
			$customer_name,
			number_format($total_sale, 2),
			// ($vendor_type !='')?$vendor_type:'NA',
			// ($vendor_name !='') ? $vendor_name : 'NA',
			'<button data-toggle="tooltip" class="btn btn-info btn-sm" onclick="purchases_display_modal(\'' . 'Hotel' . '\',' . $row_passport['booking_id'] . ')" title="" data-original-title="View Details" id="supplierv_btn-' . $row_passport['booking_id'] . '"><i class="fa fa-eye"></i></button>',
			number_format($total_purchase, 2),
			$btn,
			'<b>' . number_format($profit_amount, 2) . '</b>' . ' (' . $profit_loss_per . '% ' . $var . ')',
			$emp
		), "bg" => $bg);
		array_push($array_s, $temp_arr);
	}
}
if ($sale_type == 'Car Rental') {

	$count = 1;
	$sq_query = "select * from car_rental_booking where delete_status='0'";

	if ($booking_id != "") {
		$sq_query .= " and booking_id='$booking_id'";
	}
	if ($from_date != "" && $to_date != "") {
		$from_date = get_date_db($from_date);
		$to_date = get_date_db($to_date);
		$sq_query .= " and date(created_at) between '$from_date' and '$to_date'";
	}
	$sq_query .= "order by booking_id desc";
	$query = mysqlQuery($sq_query);

	while ($row_passport = mysqli_fetch_assoc($query)) {

		$sq_customer = mysqli_fetch_assoc(mysqlQuery("select type,company_name,first_name,last_name from customer_master where customer_id='$row_passport[customer_id]'"));
		$customer_name = ($sq_customer['type'] == 'Corporate' || $sq_customer['type'] == 'B2B') ? $sq_customer['company_name'] : $sq_customer['first_name'] . ' ' . $sq_customer['last_name'];
		$vendor_type = '';
		$vendor_name = '';
		$total_purchase = 0;
		$date = $row_passport['created_at'];
		$yr = explode("-", $date);
		$year = $yr[0];

		$sq_emp = mysqli_fetch_assoc(mysqlQuery("select * from emp_master where emp_id='$row_passport[emp_id]'"));
		$emp = ($row_passport['emp_id'] == 0) ? 'Admin' : $sq_emp['first_name'] . ' ' . $sq_emp['last_name'];

		$sq_paid_amount = mysqli_fetch_assoc(mysqlQuery("SELECT sum(payment_amount) as sum ,sum(`credit_charges`) as sumc from car_rental_payment where booking_id='$row_passport[booking_id]' and clearance_status!='Pending' and clearance_status!='Cancelled'"));
		$credit_charges = $sq_paid_amount['sumc'];

		//Service Tax and Markup Tax
		$service_tax_amount = 0;
		if ($row_passport['service_tax_subtotal'] !== 0.00 && ($row_passport['service_tax_subtotal']) !== '') {
			$service_tax_subtotal1 = explode(',', $row_passport['service_tax_subtotal']);
			for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
				$service_tax = explode(':', $service_tax_subtotal1[$i]);
				$service_tax_amount +=  $service_tax[2];
			}
		}
		$markupservice_tax_amount = 0;
		if ($row_passport['markup_cost_subtotal'] !== 0.00 && $row_passport['markup_cost_subtotal'] !== "") {
			$service_tax_markup1 = explode(',', $row_passport['markup_cost_subtotal']);
			for ($i = 0; $i < sizeof($service_tax_markup1); $i++) {
				$service_tax = explode(':', $service_tax_markup1[$i]);
				$markupservice_tax_amount += $service_tax[2];
			}
		}
		$total_sale = $row_passport['total_fees'] - $service_tax_amount - $markupservice_tax_amount + $credit_charges;

		//Purchase
		$sq_purchase = mysqlQuery("select * from vendor_estimate where estimate_type='Car Rental' and estimate_type_id ='$row_passport[booking_id]' and delete_status='0' and status !='Cancel'");
		while ($sq_pquery = mysqli_fetch_assoc($sq_purchase)) {
			if ($sq_pquery['purchase_return'] == 0 || $sq_pquery['purchase_return'] == 1) {
				$total_purchase += $sq_pquery['net_total'];
			} else if ($sq_pquery['purchase_return'] == 2) {
				$cancel_estimate = json_decode($sq_pquery['cancel_estimate']);
				$p_purchase = ($sq_pquery['net_total'] - (float)($cancel_estimate[0]->net_total) - (float)($cancel_estimate[0]->service_tax_subtotal));
				$total_purchase += $p_purchase;
			}
			//Service Tax 
			$service_tax_amount = 0;
			if ($sq_pquery['service_tax_subtotal'] !== 0.00 && ($sq_pquery['service_tax_subtotal']) !== '') {
				$service_tax_subtotal1 = explode(',', $sq_pquery['service_tax_subtotal']);
				for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
					$service_tax = explode(':', $service_tax_subtotal1[$i]);
					$service_tax_amount +=  $service_tax[2];
				}
			}
			$total_purchase -= $service_tax_amount;
			$vendor_type = $sq_pquery['vendor_type'];
			$vendor_name = get_vendor_name_report($sq_pquery['vendor_type'], $sq_pquery['vendor_type_id']);
		}


		$bg = '';
		if ($row_passport['status'] == 'Cancel') {
			$bg = 'danger';
			$total_sale = 0;
		}

		$profit_amount = $total_sale - $total_purchase;
		$profit_loss_per = ($total_sale > 0) ? ($profit_amount / $total_sale) * 100 : 0;
		$profit_loss_per = round($profit_loss_per, 2);
		$var = ($total_sale > $total_purchase) ? 'Profit' : 'Loss';


		$btn = 'NA';
		$temp_arr = array("data" => array(
			(int)($count++),
			get_car_rental_booking_id($row_passport['booking_id'], $year),
			get_date_user($row_passport['created_at']),
			$customer_name,
			number_format($total_sale, 2),
			// ($vendor_type !='')?$vendor_type:'NA',
			// ($vendor_name !='')?$vendor_name:'NA',
			'<button data-toggle="tooltip" class="btn btn-info btn-sm" onclick="purchases_display_modal(\'' . 'Car Rental' . '\',' . $row_passport['booking_id'] . ')" title="" data-original-title="View Details" id="supplierv_btn-' . $row_passport['booking_id'] . '"><i class="fa fa-eye"></i></button>',
			number_format($total_purchase, 2),
			$btn,
			'<b>' . number_format($profit_amount, 2) . '</b>' . ' (' . $profit_loss_per . '% ' . $var . ')',
			$emp
		), "bg" => $bg);
		array_push($array_s, $temp_arr);
	}
}

if ($sale_type == 'Flight Ticket') {

	$count = 1;
	$sq_query = "select * from ticket_master where delete_status='0' ";


	if ($booking_id != "") {
		$sq_query .= " and ticket_id='$booking_id'";
	}
	if ($from_date != "" && $to_date != "") {
		$from_date = get_date_db($from_date);
		$to_date = get_date_db($to_date);
		$sq_query .= " and date(created_at) between '$from_date' and '$to_date'";
	}
	$sq_query .= "order by ticket_id desc";
	$query = mysqlQuery($sq_query);

	while ($row_passport = mysqli_fetch_assoc($query)) {

		$sq_customer = mysqli_fetch_assoc(mysqlQuery("select type,company_name,first_name,last_name from customer_master where customer_id='$row_passport[customer_id]'"));
		$customer_name = ($sq_customer['type'] == 'Corporate' || $sq_customer['type'] == 'B2B') ? $sq_customer['company_name'] : $sq_customer['first_name'] . ' ' . $sq_customer['last_name'];
		$vendor_type = '';
		$vendor_name = '';
		$total_purchase = 0;
		$date = $row_passport['created_at'];
		$yr = explode("-", $date);
		$year = $yr[0];
		$sq_exc_entry = mysqli_num_rows(mysqlQuery("select * from ticket_master_entries where ticket_id='$row_passport[ticket_id]'"));
		$sq_exc_cancel = mysqli_num_rows(mysqlQuery("select * from ticket_master_entries where ticket_id='$row_passport[ticket_id]' and status = 'Cancel'"));

		$sq_emp = mysqli_fetch_assoc(mysqlQuery("select * from emp_master where emp_id='$row_passport[emp_id]'"));
		$emp = ($row_passport['emp_id'] == 0) ? 'Admin' : $sq_emp['first_name'] . ' ' . $sq_emp['last_name'];

		//Service Tax and Markup Tax
		$service_tax_amount = 0;
		if ($row_passport['service_tax_subtotal'] !== 0.00 && ($row_passport['service_tax_subtotal']) !== '') {
			$service_tax_subtotal1 = explode(',', $row_passport['service_tax_subtotal']);
			for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
				$service_tax = explode(':', $service_tax_subtotal1[$i]);
				$service_tax_amount +=  $service_tax[2];
			}
		}
		$markupservice_tax_amount = 0;
		if ($row_passport['service_tax_markup'] !== 0.00 && $row_passport['service_tax_markup'] !== "") {
			$service_tax_markup1 = explode(',', $row_passport['service_tax_markup']);
			for ($i = 0; $i < sizeof($service_tax_markup1); $i++) {
				$service_tax = explode(':', $service_tax_markup1[$i]);
				$markupservice_tax_amount += $service_tax[2];
			}
		}

		$sq_paid_amount = mysqli_fetch_assoc(mysqlQuery("SELECT sum(credit_charges) as sumc from ticket_payment_master where ticket_id='$row_passport[ticket_id]' and clearance_status!='Pending' and clearance_status!='Cancelled'"));
		$credit_card_charges = $sq_paid_amount['sumc'];

		if ($row_passport['cancel_type'] == '2' || $row_passport['cancel_type'] == '3') {
			$cancel_estimate_data = json_decode($row_passport['cancel_estimate']);
			$cancel_estimate = (!isset($cancel_estimate_data)) ? 0 : $cancel_estimate_data[0]->ticket_total_cost;
			$flight_service_tax = (!isset($cancel_estimate_data)) ? 0 : $cancel_estimate_data[0]->service_tax_subtotal;
			$flight_markup_tax = (!isset($cancel_estimate_data)) ? 0 : $cancel_estimate_data[0]->service_tax_markup;
			$sale_amount = ($row_passport['ticket_total_cost'] - (float)($cancel_estimate) - (float)($flight_service_tax) - (float)($flight_markup_tax));
		} else {
			$sale_amount = ($row_passport['ticket_total_cost']);
		}
		$total_sale = $sale_amount - $service_tax_amount - $markupservice_tax_amount + $credit_card_charges;

		//Purchase
		$sq_purchase = mysqlQuery("select * from vendor_estimate where estimate_type='Flight' and estimate_type_id ='$row_passport[ticket_id]' and delete_status='0' and status !='Cancel'");
		while ($sq_pquery = mysqli_fetch_assoc($sq_purchase)) {
			if ($sq_pquery['purchase_return'] == 0 || $sq_pquery['purchase_return'] == 1) {
				$total_purchase += $sq_pquery['net_total'];
			} else if ($sq_pquery['purchase_return'] == 2) {
				$cancel_estimate = json_decode($sq_pquery['cancel_estimate']);
				$p_purchase = ($sq_pquery['net_total'] - (float)($cancel_estimate[0]->net_total) - (float)($cancel_estimate[0]->service_tax_subtotal));
				$total_purchase += $p_purchase;
			}
			//Service Tax 
			$service_tax_amount = 0;
			if ($sq_pquery['service_tax_subtotal'] !== 0.00 && ($sq_pquery['service_tax_subtotal']) !== '') {
				$service_tax_subtotal1 = explode(',', $sq_pquery['service_tax_subtotal']);
				for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
					$service_tax = explode(':', $service_tax_subtotal1[$i]);
					$service_tax_amount +=  $service_tax[2];
				}
			}
			$total_purchase -= $service_tax_amount;
			$vendor_type = $sq_pquery['vendor_type'];
			$vendor_name = get_vendor_name_report($sq_pquery['vendor_type'], $sq_pquery['vendor_type_id']);
		}

		$bg = '';
		if ($sq_exc_cancel == $sq_exc_entry) {
			$bg = 'danger';
			$total_sale = 0;
		} else if ($row_passport['cancel_type'] == 2 || $row_passport['cancel_type'] == 3) {
			$bg = "warning";
		}

		$profit_amount = $total_sale - $total_purchase;
		$profit_loss_per = ($total_sale > 0) ? ($profit_amount / $total_sale) * 100 : 0;
		$profit_loss_per = round($profit_loss_per, 2);
		$var = ($total_sale > $total_purchase) ? 'Profit' : 'Loss';

		$btn = 'NA';

		$temp_arr = array("data" => array(
			(int)($count++),
			get_ticket_booking_id($row_passport['ticket_id'], $year),
			get_date_user($row_passport['created_at']),
			$customer_name,
			number_format($total_sale, 2),
			// ($vendor_type !='')?$vendor_type:'NA',
			// ($vendor_name !='')?$vendor_name:'NA',
			'<button data-toggle="tooltip" class="btn btn-info btn-sm" onclick="purchases_display_modal(\'' . 'Flight' . '\',' . $row_passport['ticket_id'] . ')" title="" data-original-title="View Details" id="supplierv_btn-' . $row_passport['ticket_id'] . '"><i class="fa fa-eye"></i></button>',
			number_format($total_purchase, 2),
			$btn,
			'<b>' . number_format($profit_amount, 2) . '</b>' . ' (' . $profit_loss_per . '% ' . $var . ')',
			$emp
		), "bg" => $bg);
		array_push($array_s, $temp_arr);
	}
}
if ($sale_type == 'Train Ticket') {

	$count = 1;
	$sq_query = "select * from train_ticket_master where delete_status='0'";

	// Add booking ID condition if provided
	if (!empty($booking_id)) {
		$sq_query .= " AND train_ticket_id='$booking_id'";
	}

	// Add date range condition if both dates are provided
	if (!empty($from_date) && !empty($to_date)) {
		$from_date_db = get_date_db($from_date);
		$to_date_db = get_date_db($to_date);
		$sq_query .= " AND DATE(created_at) BETWEEN '$from_date_db' AND '$to_date_db'";
	}

	// Add ordering clause
	$sq_query .= " ORDER BY train_ticket_id DESC";
	$query = mysqlQuery($sq_query);

	while ($row_passport = mysqli_fetch_assoc($query)) {

		$sq_customer = mysqli_fetch_assoc(mysqlQuery("select type,company_name,first_name,last_name from customer_master where customer_id='$row_passport[customer_id]'"));
		$customer_name = ($sq_customer['type'] == 'Corporate' || $sq_customer['type'] == 'B2B') ? $sq_customer['company_name'] : $sq_customer['first_name'] . ' ' . $sq_customer['last_name'];
		$vendor_type = '';
		$vendor_name = '';
		$total_purchase = 0;
		$date = $row_passport['created_at'];
		$yr = explode("-", $date);
		$year = $yr[0];

		$sq_emp = mysqli_fetch_assoc(mysqlQuery("select * from emp_master where emp_id='$row_passport[emp_id]'"));
		$emp = ($row_passport['emp_id'] == 0) ? 'Admin' : $sq_emp['first_name'] . ' ' . $sq_emp['last_name'];

		$sq_paid_amount = mysqli_fetch_assoc(mysqlQuery("SELECT sum(payment_amount) as sum,sum(credit_charges) as sumc from train_ticket_payment_master where train_ticket_id='$row_passport[train_ticket_id]' and clearance_status!='Pending' and clearance_status!='Cancelled'"));
		$credit_card_charges = $sq_paid_amount['sumc'];

		//Service Tax and Markup Tax
		$service_tax_amount = 0;
		if ($row_passport['service_tax_subtotal'] !== 0.00 && ($row_passport['service_tax_subtotal']) !== '') {
			$service_tax_subtotal1 = explode(',', $row_passport['service_tax_subtotal']);
			for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
				$service_tax = explode(':', $service_tax_subtotal1[$i]);
				$service_tax_amount +=  $service_tax[2];
			}
		}
		$total_sale = $row_passport['net_total'] - $service_tax_amount + $credit_card_charges;

		//Purchase
		$sq_purchase = mysqlQuery("select * from vendor_estimate where estimate_type='Train' and estimate_type_id ='$row_passport[train_ticket_id]' and delete_status='0' and status !='Cancel'");
		while ($sq_pquery = mysqli_fetch_assoc($sq_purchase)) {
			if ($sq_pquery['purchase_return'] == 0 || $sq_pquery['purchase_return'] == 1) {
				$total_purchase += $sq_pquery['net_total'];
			} else if ($sq_pquery['purchase_return'] == 2) {
				$cancel_estimate = json_decode($sq_pquery['cancel_estimate']);
				$p_purchase = ($sq_pquery['net_total'] - (float)($cancel_estimate[0]->net_total) - (float)($cancel_estimate[0]->service_tax_subtotal));
				$total_purchase += $p_purchase;
			}
			//Service Tax 
			$service_tax_amount = 0;
			if ($sq_pquery['service_tax_subtotal'] !== 0.00 && ($sq_pquery['service_tax_subtotal']) !== '') {
				$service_tax_subtotal1 = explode(',', $sq_pquery['service_tax_subtotal']);
				for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
					$service_tax = explode(':', $service_tax_subtotal1[$i]);
					$service_tax_amount +=  $service_tax[2];
				}
			}
			$total_purchase -= $service_tax_amount;
			$vendor_type = $sq_pquery['vendor_type'];
			$vendor_name = get_vendor_name_report($sq_pquery['vendor_type'], $sq_pquery['vendor_type_id']);
		}


		$sq_exc_entry = mysqli_num_rows(mysqlQuery("select * from train_ticket_master_entries where train_ticket_id='$row_passport[train_ticket_id]'"));
		$sq_exc_cancel = mysqli_num_rows(mysqlQuery("select * from train_ticket_master_entries where train_ticket_id='$row_passport[train_ticket_id]' and status = 'Cancel'"));
		$bg = '';
		if ($sq_exc_cancel == $sq_exc_entry) {
			$bg = 'danger';
			$total_sale = 0;
		}

		$profit_amount = $total_sale - $total_purchase;
		$profit_loss_per = ($total_sale > 0) ? ($profit_amount / $total_sale) * 100 : 0;
		$profit_loss_per = round($profit_loss_per, 2);
		$var = ($total_sale > $total_purchase) ? 'Profit' : 'Loss';


		$btn = 'NA';

		$temp_arr = array("data" => array(
			(int)($count++),
			get_train_ticket_booking_id($row_passport['train_ticket_id'], $year),
			get_date_user($row_passport['created_at']),
			$customer_name,
			number_format($total_sale, 2),
			// ($vendor_type !='')?$vendor_type:'NA',
			// ($vendor_name !='')?$vendor_name:'NA',
			'<button data-toggle="tooltip" class="btn btn-info btn-sm" onclick="purchases_display_modal(\'' . 'Train' . '\',' . $row_passport['train_ticket_id'] . ')" title="" data-original-title="View Details" id="supplierv_btn-' . $row_passport['train_ticket_id'] . '"><i class="fa fa-eye"></i></button>',
			number_format($total_purchase, 2),
			$btn,
			'<b>' . number_format($profit_amount, 2) . '</b>' . ' (' . $profit_loss_per . '% ' . $var . ')',
			$emp
		), "bg" => $bg);
		array_push($array_s, $temp_arr);
	}
}
if ($sale_type == 'Miscellaneous') {

	$count = 1;
	$sq_query = "select * from miscellaneous_master where 1 and delete_status='0'";


	// Add booking ID condition if provided
	if (!empty($booking_id)) {
		$sq_query .= " AND misc_id='$booking_id'";
	}

	// Add date range condition if both dates are provided
	if (!empty($from_date) && !empty($to_date)) {
		$from_date_db = get_date_db($from_date);
		$to_date_db = get_date_db($to_date);
		$sq_query .= " AND DATE(created_at) BETWEEN '$from_date_db' AND '$to_date_db'";
	}

	// Add ordering clause
	$sq_query .= "order by misc_id desc";
	$query = mysqlQuery($sq_query);

	while ($row_visa = mysqli_fetch_assoc($query)) {

		$sq_customer = mysqli_fetch_assoc(mysqlQuery("select type,company_name,first_name,last_name from customer_master where customer_id='$row_visa[customer_id]'"));
		$customer_name = ($sq_customer['type'] == 'Corporate' || $sq_customer['type'] == 'B2B') ? $sq_customer['company_name'] : $sq_customer['first_name'] . ' ' . $sq_customer['last_name'];
		$vendor_type = '';
		$vendor_name = '';
		$total_purchase = 0;
		$date = $row_visa['created_at'];
		$yr = explode("-", $date);
		$year = $yr[0];
		$sq_paid_amount1 = mysqli_fetch_assoc(mysqlQuery("SELECT sum(credit_charges) as sumc from miscellaneous_payment_master where misc_id='$row_visa[misc_id]' and clearance_status!='Pending' and clearance_status!='Cancelled'"));
		$credit_card_charges = $sq_paid_amount1['sumc'];

		$sq_emp = mysqli_fetch_assoc(mysqlQuery("select * from emp_master where emp_id='$row_visa[emp_id]'"));
		$emp = ($row_visa['emp_id'] == 0) ? 'Admin' : $sq_emp['first_name'] . ' ' . $sq_emp['last_name'];

		//Service Tax and Markup Tax
		$service_tax_amount = 0;
		if ($row_visa['service_tax_subtotal'] !== 0.00 && ($row_visa['service_tax_subtotal']) !== '') {
			$service_tax_subtotal1 = explode(',', $row_visa['service_tax_subtotal']);
			for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
				$service_tax = explode(':', $service_tax_subtotal1[$i]);
				$service_tax_amount +=  $service_tax[2];
			}
		}
		$markupservice_tax_amount = 0;
		if ($row_visa['service_tax_markup'] !== 0.00 && $row_visa['service_tax_markup'] !== "") {
			$service_tax_markup1 = explode(',', $row_visa['service_tax_markup']);
			for ($i = 0; $i < sizeof($service_tax_markup1); $i++) {
				$service_tax = explode(':', $service_tax_markup1[$i]);
				$markupservice_tax_amount += $service_tax[2];
			}
		}
		$total_sale = $row_visa['misc_total_cost'] - $service_tax_amount - $markupservice_tax_amount + $credit_card_charges;

		//Purchase
		$sq_purchase = mysqlQuery("select * from vendor_estimate where estimate_type='Miscellaneous' and estimate_type_id ='$row_visa[misc_id]' and delete_status='0' and status !='Cancel'");
		while ($sq_pquery = mysqli_fetch_assoc($sq_purchase)) {
			if ($sq_pquery['purchase_return'] == 0 || $sq_pquery['purchase_return'] == 1) {
				$total_purchase += $sq_pquery['net_total'];
			} else if ($sq_pquery['purchase_return'] == 2) {
				$cancel_estimate = json_decode($sq_pquery['cancel_estimate']);
				$p_purchase = ($sq_pquery['net_total'] - (float)($cancel_estimate[0]->net_total) - (float)($cancel_estimate[0]->service_tax_subtotal));
				$total_purchase += $p_purchase;
			}
			//Service Tax 
			$service_tax_amount = 0;
			if ($sq_pquery['service_tax_subtotal'] !== 0.00 && ($sq_pquery['service_tax_subtotal']) !== '') {
				$service_tax_subtotal1 = explode(',', $sq_pquery['service_tax_subtotal']);
				for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
					$service_tax = explode(':', $service_tax_subtotal1[$i]);
					$service_tax_amount +=  $service_tax[2];
				}
			}
			$total_purchase -= $service_tax_amount;
			$vendor_type = $sq_pquery['vendor_type'];
			$vendor_name = get_vendor_name_report($sq_pquery['vendor_type'], $sq_pquery['vendor_type_id']);
		}


		$bg = '';
		$sq_visa_entry = mysqli_num_rows(mysqlQuery("select * from miscellaneous_master_entries where misc_id='$row_visa[misc_id]'"));
		$sq_visa_cancel = mysqli_num_rows(mysqlQuery("select * from miscellaneous_master_entries where misc_id='$row_visa[misc_id]' and status = 'Cancel'"));
		if ($sq_visa_cancel == $sq_visa_entry) {
			$bg = 'danger';
			$total_sale = 0;
		}

		$profit_amount = $total_sale - $total_purchase;
		$profit_loss_per = ($total_sale > 0) ? ($profit_amount / $total_sale) * 100 : 0;
		$profit_loss_per = round($profit_loss_per, 2);
		$var = ($total_sale > $total_purchase) ? 'Profit' : 'Loss';


		$btn = 'NA';
		$temp_arr = array("data" => array(
			(int)($count++),
			get_misc_booking_id($row_visa['misc_id'], $year),
			get_date_user($row_visa['created_at']),
			$customer_name,
			number_format($total_sale, 2),
			// ($vendor_type !='')?$vendor_type:'NA',
			// ($vendor_name !='') ? $vendor_name : 'NA',
			'<button data-toggle="tooltip" class="btn btn-info btn-sm" onclick="purchases_display_modal(\'' . 'Miscellaneous' . '\',' . $row_visa['misc_id'] . ')" title="" data-original-title="View Details" id="supplierv_btn-' . $row_visa['misc_id'] . '"><i class="fa fa-eye"></i></button>',
			number_format($total_purchase, 2),
			$btn,
			'<b>' . number_format($profit_amount, 2) . '</b>' . ' (' . $profit_loss_per . '% ' . $var . ')',
			$emp
		), "bg" => $bg);
		array_push($array_s, $temp_arr);
	}
}



if ($sale_type == 'Package Tour') {

	$count = 1;
	$sq_query = "select * from package_tour_booking_master where delete_status='0'";

	if ($booking_id != "") {
		$sq_query .= " and booking_id='$booking_id'";
	}
	if ($from_date != "" && $to_date != "") {
		$from_date = get_date_db($from_date);
		$to_date = get_date_db($to_date);
		$sq_query .= " and date(booking_date) between '$from_date' and '$to_date'";
	}
	$sq_query .= "order by booking_id desc";
	$query = mysqlQuery($sq_query);

	while ($row_visa = mysqli_fetch_assoc($query)) {

		$sq_customer = mysqli_fetch_assoc(mysqlQuery("select type,company_name,first_name,last_name from customer_master where customer_id='$row_visa[customer_id]'"));
		$customer_name = ($sq_customer['type'] == 'Corporate' || $sq_customer['type'] == 'B2B') ? $sq_customer['company_name'] : $sq_customer['first_name'] . ' ' . $sq_customer['last_name'];
		$vendor_type = '';
		$vendor_name = '';
		$total_purchase = 0;
		$date = $row_visa['created_at'];
		$yr = explode("-", $date);
		$year = $yr[0];
		$sq_paid_amount1 = mysqli_fetch_assoc(mysqlQuery("SELECT sum(credit_charges) as sumc from package_payment_master where booking_id='$row_visa[booking_id]' and clearance_status!='Pending' and clearance_status!='Cancelled'"));
		$credit_card_charges = $sq_paid_amount1['sumc'];

		$sq_emp = mysqli_fetch_assoc(mysqlQuery("select * from emp_master where emp_id='$row_visa[emp_id]'"));
		$emp = ($row_visa['emp_id'] == 0) ? 'Admin' : $sq_emp['first_name'] . ' ' . $sq_emp['last_name'];

		//Service Tax and Markup Tax
		$service_tax_amount = 0;
		if ($row_visa['service_tax_subtotal'] !== 0.00 && ($row_visa['service_tax_subtotal']) !== '') {
			$service_tax_subtotal1 = explode(',', $row_visa['service_tax_subtotal']);
			for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
				$service_tax = explode(':', $service_tax_subtotal1[$i]);
				$service_tax_amount +=  $service_tax[2];
			}
		}
		$markupservice_tax_amount = 0;
		if ($row_visa['service_tax_markup'] !== 0.00 && $row_visa['service_tax_markup'] !== "") {
			$service_tax_markup1 = explode(',', $row_visa['service_tax_markup']);
			for ($i = 0; $i < sizeof($service_tax_markup1); $i++) {
				$service_tax = explode(':', $service_tax_markup1[$i]);
				$markupservice_tax_amount += $service_tax[2];
			}
		}
		$total_sale = $row_visa['net_total'] - $service_tax_amount - $markupservice_tax_amount + $credit_card_charges;

		//Purchase
		$sq_purchase = mysqlQuery("select * from vendor_estimate where estimate_type='Package Tour' and estimate_type_id ='$row_visa[booking_id]' and delete_status='0' and status !='Cancel'");
		while ($sq_pquery = mysqli_fetch_assoc($sq_purchase)) {
			if ($sq_pquery['purchase_return'] == 0 || $sq_pquery['purchase_return'] == 1) {
				$total_purchase += $sq_pquery['net_total'];
			} else if ($sq_pquery['purchase_return'] == 2) {
				$cancel_estimate = json_decode($sq_pquery['cancel_estimate']);
				$p_purchase = ($sq_pquery['net_total'] - (float)($cancel_estimate[0]->net_total) - (float)($cancel_estimate[0]->service_tax_subtotal));
				$total_purchase += $p_purchase;
			}
			//Service Tax 
			$service_tax_amount = 0;
			if ($sq_pquery['service_tax_subtotal'] !== 0.00 && ($sq_pquery['service_tax_subtotal']) !== '') {
				$service_tax_subtotal1 = explode(',', $sq_pquery['service_tax_subtotal']);
				for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
					$service_tax = explode(':', $service_tax_subtotal1[$i]);
					$service_tax_amount +=  $service_tax[2];
				}
			}
			$total_purchase -= $service_tax_amount;
			$vendor_type = $sq_pquery['vendor_type'];
			$vendor_name = get_vendor_name_report($sq_pquery['vendor_type'], $sq_pquery['vendor_type_id']);
		}

		// other expense

		$sq_expense = mysqli_fetch_assoc(mysqlQuery("
		SELECT SUM(amount) AS total_amount 
		FROM package_tour_estimate_expense 
		WHERE booking_id = '$row_visa[booking_id]'
	"));

		$total_purchase += $sq_expense['total_amount'] ?? 0;



		$bg = '';
		$sq_visa_entry = mysqli_num_rows(mysqlQuery("select * from package_travelers_details where booking_id='$row_visa[booking_id]'"));
		$sq_visa_cancel = mysqli_num_rows(mysqlQuery("select * from package_travelers_details where booking_id='$row_visa[booking_id]' and status = 'Cancel'"));




		if ($sq_visa_cancel == $sq_visa_entry) {
			$bg = 'danger';
			$total_sale = 0;
		}


		$profit_amount = $total_sale - $total_purchase;
		$profit_loss_per = ($total_sale > 0) ? ($profit_amount / $total_sale) * 100 : 0;
		$profit_loss_per = round($profit_loss_per, 2);
		$var = ($total_sale > $total_purchase) ? 'Profit' : 'Loss';


		$btn = ($bg == '') ? '<button class="btn btn-info btn-sm" id="suppliere_btn-' . $row_visa['booking_id'] . '" onclick="package_other_expnse_modal(' . $row_visa['booking_id'] . ')" title="Add Other expense amount"><i class="fa fa-plus"></i></button>' : 'NA';

		$temp_arr = array("data" => array(
			(int)($count++),
			get_package_booking_id($row_visa['booking_id'], $year),
			get_date_user($row_visa['booking_date']),
			$customer_name,
			number_format($total_sale, 2),
			// ($vendor_type !='')?$vendor_type:'NA',
			// ($vendor_name !='') ? $vendor_name : 'NA',
			'<button data-toggle="tooltip" class="btn btn-info btn-sm" onclick="purchases_display_modal_pkg(\'' . 'Package Tour' . '\',' . $row_visa['booking_id'] . ')" title="" data-original-title="View Details" id="supplierv_btn-' . $row_visa['booking_id'] . '"><i class="fa fa-eye"></i></button>',
			number_format($total_purchase, 2),
			$btn,
			'<b>' . number_format($profit_amount, 2) . '</b>' . ' (' . $profit_loss_per . '% ' . $var . ')',
			$emp
		), "bg" => $bg);
		array_push($array_s, $temp_arr);
	}
}


// Group tour

if ($sale_type == 'Group Tour') {

	$count = 1;
	$sq_query = "select * from tourwise_traveler_details where delete_status='0'";



	if ($tour_id != "") {
		// $from_date = get_date_db($from_date);
		// $to_date = get_date_db($to_date);
		// $sq_query .= " and date(booking_date) between '$from_date' and '$to_date'";

		$sq_query .= " and tour_id='$tour_id' ";
	}

	if ($group_id != "") {
		$sq_query .= " and tour_group_id='$group_id'";
	}

	$sq_query .= "order by traveler_group_id desc";

	$query = mysqlQuery($sq_query);

	while ($row_visa = mysqli_fetch_assoc($query)) {

		$sq_customer = mysqli_fetch_assoc(mysqlQuery("select type,company_name,first_name,last_name from customer_master where customer_id='$row_visa[customer_id]'"));
		$customer_name = ($sq_customer['type'] == 'Corporate' || $sq_customer['type'] == 'B2B') ? $sq_customer['company_name'] : $sq_customer['first_name'] . ' ' . $sq_customer['last_name'];
		$vendor_type = '';
		$vendor_name = '';
		$total_purchase = 0;
		$date = $row_visa['form_date'];
		$yr = explode("-", $date);
		$year = $yr[0];
		// $sq_paid_amount1 = mysqli_fetch_assoc(mysqlQuery("SELECT sum(credit_charges) as sumc from package_payment_master where booking_id='$row_visa[booking_id]' and clearance_status!='Pending' and clearance_status!='Cancelled'"));
		// $credit_card_charges = $sq_paid_amount1['sumc'];

		$sq_emp = mysqli_fetch_assoc(mysqlQuery("select * from emp_master where emp_id='$row_visa[emp_id]'"));
		$emp = ($row_visa['emp_id'] == 0) ? 'Admin' : $sq_emp['first_name'] . ' ' . $sq_emp['last_name'];

		//Service Tax and Markup Tax
		$service_tax_amount = 0;
		if ($row_visa['service_tax_subtotal'] !== 0.00 && ($row_visa['service_tax_subtotal']) !== '') {
			$service_tax_subtotal1 = explode(',', $row_visa['service_tax_subtotal']);
			for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
				$service_tax = explode(':', $service_tax_subtotal1[$i]);
				$service_tax_amount +=  $service_tax[2];
			}
		}
		$markupservice_tax_amount = 0;
		if ($row_visa['service_tax_markup'] !== 0.00 && $row_visa['service_tax_markup'] !== "") {
			$service_tax_markup1 = explode(',', $row_visa['service_tax_markup']);
			for ($i = 0; $i < sizeof($service_tax_markup1); $i++) {
				$service_tax = explode(':', $service_tax_markup1[$i]);
				$markupservice_tax_amount += $service_tax[2];
			}
		}
		$total_sale = $row_visa['net_total'] - $service_tax_amount - $markupservice_tax_amount + $credit_card_charges;

		//Purchase
		$sq_purchase = mysqlQuery("select * from vendor_estimate where estimate_type='Group Tour' and estimate_type_id ='$row_visa[tour_group_id]' and delete_status='0' and status!='Cancel'");
		while ($sq_pquery = mysqli_fetch_assoc($sq_purchase)) {
			if ($sq_pquery['purchase_return'] == 0 || $sq_pquery['purchase_return'] == 1) {
				$total_purchase += $sq_pquery['net_total'];
			} else if ($sq_pquery['purchase_return'] == 2) {
				$cancel_estimate = json_decode($sq_pquery['cancel_estimate']);
				$p_purchase = ($sq_pquery['net_total'] - (float)($cancel_estimate[0]->net_total) - (float)($cancel_estimate[0]->service_tax_subtotal));
				$total_purchase += $p_purchase;
			}
			//Service Tax 
			$service_tax_amount = 0;
			if ($sq_pquery['service_tax_subtotal'] !== 0.00 && ($sq_pquery['service_tax_subtotal']) !== '') {
				$service_tax_subtotal1 = explode(',', $sq_pquery['service_tax_subtotal']);
				for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
					$service_tax = explode(':', $service_tax_subtotal1[$i]);
					$service_tax_amount +=  $service_tax[2];
				}
			}
			$total_purchase -= $service_tax_amount;
			$vendor_type = $sq_pquery['vendor_type'];
			$vendor_name = get_vendor_name_report($sq_pquery['vendor_type'], $sq_pquery['vendor_type_id']);
		}


		// Other Expense

		$sq_expense = mysqli_fetch_assoc(mysqlQuery("
		SELECT SUM(amount) AS total_amount 
		FROM group_tour_estimate_expense 
		WHERE tour_id = '$row_visa[tour_id]'
	"));

		$total_purchase += $sq_expense['total_amount'] ?? 0;


		$bg = '';
		$sq_visa_entry = mysqli_num_rows(mysqlQuery("select *  from  travelers_details where traveler_group_id='$row_visa[traveler_group_id]'"));
		$sq_visa_cancel = mysqli_num_rows(mysqlQuery("select *  from  travelers_details where traveler_group_id='$row_visa[traveler_group_id]' and status = 'Cancel'"));

		// complete group tour cancel
		if ($row_visa['tour_group_status'] == 'Cancel') {
			$bg = 'danger';
			$total_sale = 0;
		}


		if ($sq_visa_cancel == $sq_visa_entry) {

			$bg = 'danger';
			$total_sale = 0;
		}

		$profit_amount = $total_sale - $total_purchase;
		$profit_loss_per = ($total_sale > 0) ? ($profit_amount / $total_sale) * 100 : 0;
		$profit_loss_per = round($profit_loss_per, 2);
		$var = ($total_sale > $total_purchase) ? 'Profit' : 'Loss';



		$temp_arr = array("data" => array(
			(int)($count++),
			get_group_booking_id($row_visa['traveler_group_id'], $year),
			get_date_user($date),
			$customer_name,
			number_format($total_sale, 2),
			// ($vendor_type !='')?$vendor_type:'NA',
			// ($vendor_name !='') ? $vendor_name : 'NA',
			'<button data-toggle="tooltip" class="btn btn-info btn-sm" onclick="purchases_display_modal(\'' . 'Group Tour' . '\',' . $row_visa['tour_group_id'] . ',' . $row_visa['tour_id'] . ')" title="" data-original-title="View Details" id="supplierv_btn-' . $row_visa['tour_group_id'] . '"><i class="fa fa-eye"></i></button>',
			number_format($total_purchase, 2),
			'<button class="btn btn-info btn-sm" id="suppliere_btn-' . $row_visa['tour_group_id'] . $row_visa['tour_id'] . $row_visa['tour_group_id'] . '" onclick="other_expnse_modal(' . $row_visa['tour_id'] . ',' . $row_visa['tour_group_id'] . ',' . $row_visa['tour_group_id'] . ')" data-toggle="tooltip" title="Add Other expense amount"><i class="fa fa-plus"></i></button>',
			'<b>' . number_format($profit_amount, 2) . '</b>' . ' (' . $profit_loss_per . '% ' . $var . ')',
			$emp
		), "bg" => $bg);
		array_push($array_s, $temp_arr);
	}
}
echo json_encode($array_s);
