<?php
include "../../../../model/model.php";

/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/London');

if (PHP_SAPI == 'cli')
  die('This example should only be run from a Web Browser');

/** Include PHPExcel */
require_once '../../../../classes/PHPExcel-1.8/Classes/PHPExcel.php';

//This function generates the background color
function cellColor($cells, $color)
{
  global $objPHPExcel;

  $objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()->applyFromArray(array(
    'type' => PHPExcel_Style_Fill::FILL_SOLID,
    'startcolor' => array(
      'rgb' => $color
    )
  ));
}

//This array sets the font atrributes
$header_style_Array = array(
  'font'  => array(
    'bold'  => true,
    'color' => array('rgb' => '000000'),
    'size'  => 12,
    'name'  => 'Verdana'
  )
);

$table_header_style_Array = array(
  'font'  => array(
    'bold'  => false,
    'color' => array('rgb' => '000000'),
    'size'  => 11,
    'name'  => 'Verdana'
  )
);

$content_style_Array = array(
  'font'  => array(
    'bold'  => false,
    'color' => array('rgb' => '000000'),
    'size'  => 9,
    'name'  => 'Verdana'
  )
);

//This is border array
$borderArray = array(
  'borders' => array(
    'allborders' => array(
      'style' => PHPExcel_Style_Border::BORDER_THIN
    )
  )
);

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
  ->setLastModifiedBy("Maarten Balliauw")
  ->setTitle("Office 2007 XLSX Test Document")
  ->setSubject("Office 2007 XLSX Test Document")
  ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
  ->setKeywords("office 2007 openxml php")
  ->setCategory("Test result file");

//////////////////////////****************Content start**************////////////////////////////////
global $currency;
$customer_id = isset($_GET['customer_id']) ? $_GET['customer_id'] : '';
$travel_type = isset($_GET['travel_type']) ? $_GET['travel_type'] : '';
$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : '';
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : '';
$cust_type = isset($_GET['cust_type']) ? $_GET['cust_type'] : '';
$company_name = isset($_GET['company_name']) ? $_GET['company_name'] : '';
$booker_id = isset($_GET['booker_id']) ? $_GET['booker_id'] : '';
$branch_id = isset($_GET['branch_id']) ? $_GET['branch_id'] : '';
$branch_status = isset($_GET['branch_status']) ? $_GET['branch_status'] : '';

$year = date('Y');

// Add some data
if ($from_date != "" && $to_date != "") {
  $date_str = $from_date . ' to ' . $to_date;
} else {
  $date_str = "";
}

if ($company_name == 'undefined') {
  $company_name = '';
}

if ($cust_type != "") {
  $cust_type_str = $cust_type;
} else {
  $cust_type_str = "";
}

if ($travel_type != "") {
  $travel_type_str = $travel_type;
} else {
  $travel_type_str = "All";
}

if ($booker_id != "") {
  $sq_emp = mysqli_fetch_assoc(mysqlQuery("select * from emp_master where emp_id='$booker_id'"));
  $booker_name = ($sq_emp['first_name'] == '') ? 'Admin' : $sq_emp['first_name'] . ' ' . $sq_emp['last_name'];
} else {
  $booker_name = "";
}

if ($branch_id != "") {
  $sq_branch = mysqli_fetch_assoc(mysqlQuery("select * from branches where branch_id='$branch_id'"));
  $branch_name_filter = $sq_branch['branch_name'];
} else {
  $branch_name_filter = "";
}

if ($customer_id != "") {
  $sq_customer_info = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$customer_id'"));
  if ($sq_customer_info['type'] == 'Corporate' || $sq_customer_info['type'] == 'B2B') {
    $cust_name = $sq_customer_info['company_name'];
  } else {
    $cust_name = $sq_customer_info['first_name'] . ' ' . $sq_customer_info['last_name'];
  }
} else {
  $cust_name = "";
}

$objPHPExcel->setActiveSheetIndex(0)
  ->setCellValue('B2', 'Report Name')
  ->setCellValue('C2', 'Consolidated Sales Report')
  ->setCellValue('B3', 'Customer Name')
  ->setCellValue('C3', $cust_name)
  ->setCellValue('B4', 'Travel Type')
  ->setCellValue('C4', $travel_type_str)
  ->setCellValue('B5', 'From-To-Date')
  ->setCellValue('C5', $date_str)
  ->setCellValue('B6', 'Customer Type')
  ->setCellValue('C6', $cust_type_str)
  ->setCellValue('B7', 'Company Name')
  ->setCellValue('C7', $company_name)
  ->setCellValue('B8', 'Booker Name')
  ->setCellValue('C8', $booker_name)
  ->setCellValue('B9', 'Branch Name')
  ->setCellValue('C9', $branch_name_filter);

$objPHPExcel->getActiveSheet()->getStyle('B2:C2')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B2:C2')->applyFromArray($borderArray);
$objPHPExcel->getActiveSheet()->getStyle('B3:C3')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B3:C3')->applyFromArray($borderArray);
$objPHPExcel->getActiveSheet()->getStyle('B4:C4')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B4:C4')->applyFromArray($borderArray);
$objPHPExcel->getActiveSheet()->getStyle('B5:C5')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B5:C5')->applyFromArray($borderArray);
$objPHPExcel->getActiveSheet()->getStyle('B6:C6')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B6:C6')->applyFromArray($borderArray);
$objPHPExcel->getActiveSheet()->getStyle('B7:C7')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B7:C7')->applyFromArray($borderArray);
$objPHPExcel->getActiveSheet()->getStyle('B8:C8')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B8:C8')->applyFromArray($borderArray);
$objPHPExcel->getActiveSheet()->getStyle('B9:C9')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B9:C9')->applyFromArray($borderArray);

// Function to get customer info
function get_customer_info_excel($customer_id) {
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
function get_emp_branch_info_excel($emp_id) {
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
function apply_common_filters_excel($query, $customer_id, $from_date, $to_date, $cust_type, $company_name, $booker_id, $branch_id, $date_field = 'created_at') {
	if ($customer_id != "") {
		$query .= " and customer_id='$customer_id'";
	}
	if ($from_date != "" && $to_date != "") {
		$from_date = date('Y-m-d', strtotime($from_date));
		$to_date = date('Y-m-d', strtotime($to_date));
		$query .= " and $date_field between '$from_date' and '$to_date'";
	}
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

$count = 0;
// Initialize totals
$global_total_cancel = 0;
$global_total_sale = 0;
$global_total_paid = 0;
$global_total_balance = 0;

$row_count = 11;

$objPHPExcel->setActiveSheetIndex(0)
  ->setCellValue('B' . $row_count, "S_no")
  ->setCellValue('C' . $row_count, "Booking_id")
  ->setCellValue('D' . $row_count, "Customer_name")
  ->setCellValue('E' . $row_count, "Mobile")
  ->setCellValue('F' . $row_count, "Email_id")
  ->setCellValue('G' . $row_count, "No_of_pax")
  ->setCellValue('H' . $row_count, "Booking_date")
//   ->setCellValue('I' . $row_count, "View")
  ->setCellValue('I' . $row_count, "Travel_type")
  ->setCellValue('J' . $row_count, "Tour_name")
  ->setCellValue('K' . $row_count, "Tour_date")
  ->setCellValue('L' . $row_count, "Basic_amount")
  ->setCellValue('M' . $row_count, "Service_charge")
  ->setCellValue('N' . $row_count, "Tax")
  ->setCellValue('O' . $row_count, "Tcs")
  ->setCellValue('P' . $row_count, "Tds")
  ->setCellValue('Q' . $row_count, "Credit_card_charges")
  ->setCellValue('R' . $row_count, "Sale")
  ->setCellValue('S' . $row_count, "Cancel")
  ->setCellValue('T' . $row_count, "Total")
  ->setCellValue('U' . $row_count, "Paid")
//   ->setCellValue('W' . $row_count, "View")
  ->setCellValue('V' . $row_count, "Outstanding_balance")
  ->setCellValue('W' . $row_count, "Due_date")
  ->setCellValue('X' . $row_count, "Purchase")
//   ->setCellValue('AA' . $row_count, "Purchase_form")
  ->setCellValue('Y' . $row_count, "Branch")
  ->setCellValue('Z' . $row_count, "Booked_by")
  ->setCellValue('AA' . $row_count, "Incentive");

$objPHPExcel->getActiveSheet()->getStyle('B' . $row_count . ':AA' . $row_count)->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B' . $row_count . ':AA' . $row_count)->applyFromArray($borderArray);

$row_count++;

// ===== PACKAGE TOUR =====
if ($travel_type == "" || $travel_type == "Package Tour") {
	$query = "select * from package_tour_booking_master where 1 and delete_status='0' ";
	$query = apply_common_filters_excel($query, $customer_id, $from_date, $to_date, $cust_type, $company_name, $booker_id, $branch_id, 'booking_date');

	$sq_package = mysqlQuery($query);
	while ($row_package = mysqli_fetch_assoc($sq_package)) {
		$count++;

		// Customer info
		$cust_info = get_customer_info_excel($row_package['customer_id']);
		$customer_name = $cust_info['name'];
		$contact_no = $cust_info['contact'];
		$email_id = $cust_info['email'];

		// Employee and branch info
		$emp_branch = get_emp_branch_info_excel($row_package['emp_id']);
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

		// Write data to Excel
		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('B' . $row_count, $count)
			->setCellValue('C' . $row_count, get_package_booking_id($row_package['booking_id'], $year))
			->setCellValue('D' . $row_count, $customer_name)
			->setCellValue('E' . $row_count, $contact_no)
			->setCellValue('F' . $row_count, $email_id)
			->setCellValue('G' . $row_count, $sq_total_member)
			->setCellValue('H' . $row_count, get_date_user($row_package['booking_date']))
			// ->setCellValue('I' . $row_count, '') // View button placeholder
			->setCellValue('I' . $row_count, $row_package['tour_type'])
			->setCellValue('J' . $row_count, $row_package['tour_name'])
			->setCellValue('K' . $row_count, $tour_date)
			->setCellValue('L' . $row_count, number_format($row_package['basic_amount'], 2))
			->setCellValue('M' . $row_count, number_format($row_package['service_charge'], 2))
			->setCellValue('N' . $row_count, number_format($service_tax_amount, 2))
			->setCellValue('O' . $row_count, number_format($row_package['tcs_per'], 2))
			->setCellValue('P' . $row_count, number_format($row_package['tds'], 2))
			->setCellValue('Q' . $row_count, number_format((float)($credit_card_charges), 2))
			->setCellValue('R' . $row_count, number_format($tour_fee, 2))
			->setCellValue('S' . $row_count, number_format((float)($tour_esti), 2))
			->setCellValue('T' . $row_count, number_format($total_amount, 2))
			->setCellValue('U' . $row_count, number_format($total_paid, 2))
			// ->setCellValue('W' . $row_count, '') // Payment view button placeholder
			->setCellValue('V' . $row_count, number_format((float)($total_balance), 2))
			->setCellValue('W' . $row_count, ($row_package['due_date'] == '1970-01-01') ? get_date_user($row_package['booking_date']) : get_date_user($row_package['due_date']))
			->setCellValue('X' . $row_count, number_format((float)($total_purchase), 2).$currency_amount_3)
			// ->setCellValue('AA' . $row_count, '') // Supplier view button placeholder
			->setCellValue('Y' . $row_count, $branch_name)
			->setCellValue('Z' . $row_count, $emp_name)
			->setCellValue('AA' . $row_count, number_format((float)($incentive_amount), 2));

		$objPHPExcel->getActiveSheet()->getStyle('B' . $row_count . ':AA' . $row_count)->applyFromArray($content_style_Array);
		$objPHPExcel->getActiveSheet()->getStyle('B' . $row_count . ':AA' . $row_count)->applyFromArray($borderArray);
		$row_count++;

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
	$query = apply_common_filters_excel($query, $customer_id, $from_date, $to_date, $cust_type, $company_name, $booker_id, $branch_id, 'form_date');

	$sq_group = mysqlQuery($query);
	while ($row_group = mysqli_fetch_assoc($sq_group)) {
		$count++;

		$sq_group1 = mysqli_fetch_assoc(mysqlQuery("select * from tour_groups where group_id='$row_group[tour_group_id]'"));
		$tour = $sq_tour['tour_name'];
		$group = get_date_user($sq_group1['from_date']) . ' To ' . get_date_user($sq_group1['to_date']);

		// Get tour master info
		$sq_tour_master = mysqli_fetch_assoc(mysqlQuery("select * from tour_master where tour_id='$row_group[tour_id]'"));

		// Customer info
		$cust_info = get_customer_info_excel($row_group['customer_id']);
		$customer_name = $cust_info['name'];
		$contact_no = $cust_info['contact'];
		$email_id = $cust_info['email'];

		// Employee and branch info
		$emp_branch = get_emp_branch_info_excel($row_group['emp_id']);
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
		// Write data to Excel
		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('B' . $row_count, $count)
			->setCellValue('C' . $row_count, get_group_booking_id($row_group['id'], $year))
			->setCellValue('D' . $row_count, $customer_name)
			->setCellValue('E' . $row_count, $contact_no)
			->setCellValue('F' . $row_count, $email_id)
			->setCellValue('G' . $row_count, $sq_total_member)
			->setCellValue('H' . $row_count, get_date_user($row_group['form_date']))
			// ->setCellValue('I' . $row_count, '') // View button placeholder
			->setCellValue('I' . $row_count, $sq_tour_master['tour_type'])
			->setCellValue('J' . $row_count, $sq_tour_master['tour_name'])
			->setCellValue('K' . $row_count, $group)
			->setCellValue('L' . $row_count, number_format($basic_amount, 2))
			->setCellValue('M' . $row_count, number_format($service_charge, 2))
			->setCellValue('N' . $row_count, number_format($tax_amount, 2))
			->setCellValue('O' . $row_count, number_format($tcs_amount, 2))
			->setCellValue('P' . $row_count, number_format($tds_amount, 2))
			->setCellValue('Q' . $row_count, number_format((float)($credit_card_charges), 2))
			->setCellValue('R' . $row_count, number_format($sale_amount, 2))
			->setCellValue('S' . $row_count, number_format((float)($cancel_amount), 2))
			->setCellValue('T' . $row_count, number_format($total_amount, 2))
			->setCellValue('U' . $row_count, number_format($total_paid, 2))
			// ->setCellValue('W' . $row_count, '') // Payment view button placeholder
			->setCellValue('V' . $row_count, number_format((float)($total_balance), 2))
			->setCellValue('W' . $row_count, get_date_user($row_group['form_date']))
			->setCellValue('X' . $row_count, number_format((float)($total_purchase), 2).$currency_amount_3)
			// ->setCellValue('AA' . $row_count, '') // Supplier view button placeholder
			->setCellValue('Y' . $row_count, $branch_name)
			->setCellValue('Z' . $row_count, $emp_name)
			->setCellValue('AA' . $row_count, number_format((float)($incentive_amount), 2));

		$objPHPExcel->getActiveSheet()->getStyle('B' . $row_count . ':AA' . $row_count)->applyFromArray($content_style_Array);
		$objPHPExcel->getActiveSheet()->getStyle('B' . $row_count . ':AA' . $row_count)->applyFromArray($borderArray);
		$row_count++;

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
	$query = apply_common_filters_excel($query, $customer_id, $from_date, $to_date, $cust_type, $company_name, $booker_id, $branch_id);

	$hotel_result = mysqlQuery($query);
	while ($row_hotel = mysqli_fetch_assoc($hotel_result)) {
		$count++;

		// Customer info
		$cust_info = get_customer_info_excel($row_hotel['customer_id']);
		$customer_name = $cust_info['name'];
		$contact_no = $cust_info['contact'];
		$email_id = $cust_info['email'];

		// Employee and branch info
		$emp_branch = get_emp_branch_info_excel($row_hotel['emp_id']);
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

		// Write data to Excel
		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('B' . $row_count, $count)
			->setCellValue('C' . $row_count, get_hotel_booking_id($row_hotel['booking_id'], $year))
			->setCellValue('D' . $row_count, $customer_name)
			->setCellValue('E' . $row_count, $contact_no)
			->setCellValue('F' . $row_count, $email_id)
			->setCellValue('G' . $row_count, $sq_total_member)
			->setCellValue('H' . $row_count, get_date_user($row_hotel['created_at']))
			// ->setCellValue('I' . $row_count, '') // View button placeholder
			->setCellValue('I' . $row_count, 'NA')
			->setCellValue('J' . $row_count, $tour_name)
			->setCellValue('K' . $row_count, $tour_date)
			->setCellValue('L' . $row_count, number_format($basic_amount, 2))
			->setCellValue('M' . $row_count, number_format($service_charge, 2))
			->setCellValue('N' . $row_count, number_format($tax_amount, 2))
			->setCellValue('O' . $row_count, number_format($tcs_amount, 2))
			->setCellValue('P' . $row_count, number_format($tds_amount, 2))
			->setCellValue('Q' . $row_count, number_format((float)($credit_card_charges), 2))
			->setCellValue('R' . $row_count, number_format($sale_amount, 2))
			->setCellValue('S' . $row_count, number_format((float)($cancel_amount), 2))
			->setCellValue('T' . $row_count, number_format($total_amount, 2))
			->setCellValue('U' . $row_count, number_format($total_paid, 2))
			// ->setCellValue('W' . $row_count, '') // Payment view button placeholder
			->setCellValue('V' . $row_count, number_format((float)($total_balance), 2))
			->setCellValue('W' . $row_count, get_date_user($row_hotel['created_at']))
			->setCellValue('X' . $row_count, number_format((float)($total_purchase), 2).$currency_amount_3)
			// ->setCellValue('AA' . $row_count, '') // Supplier view button placeholder
			->setCellValue('Y' . $row_count, $branch_name)
			->setCellValue('Z' . $row_count, $emp_name)
			->setCellValue('AA' . $row_count, number_format((float)($incentive_amount), 2));

		$objPHPExcel->getActiveSheet()->getStyle('B' . $row_count . ':AA' . $row_count)->applyFromArray($content_style_Array);
		$objPHPExcel->getActiveSheet()->getStyle('B' . $row_count . ':AA' . $row_count)->applyFromArray($borderArray);
		$row_count++;

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
	$query = apply_common_filters_excel($query, $customer_id, $from_date, $to_date, $cust_type, $company_name, $booker_id, $branch_id);

	$sq_flight = mysqlQuery($query);
	while ($row_flight = mysqli_fetch_assoc($sq_flight)) {
		$count++;

		// Customer info
		$cust_info = get_customer_info_excel($row_flight['customer_id']);
		$customer_name = $cust_info['name'];
		$contact_no = $cust_info['contact'];
		$email_id = $cust_info['email'];

		// Employee and branch info
		$emp_branch = get_emp_branch_info_excel($row_flight['emp_id']);
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

		// Write data to Excel
		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('B' . $row_count, $count)
			->setCellValue('C' . $row_count, get_ticket_booking_id($row_flight['ticket_id'], $year))
			->setCellValue('D' . $row_count, $customer_name)
			->setCellValue('E' . $row_count, $contact_no)
			->setCellValue('F' . $row_count, $email_id)
			->setCellValue('G' . $row_count, $sq_total_member)
			->setCellValue('H' . $row_count, get_date_user($row_flight['created_at']))
			// ->setCellValue('I' . $row_count, '') // View button placeholder
			->setCellValue('I' . $row_count, $row_flight['tour_type'])
			->setCellValue('J' . $row_count, $tour_name)
			->setCellValue('K' . $row_count, $tour_date)
			->setCellValue('L' . $row_count, number_format($basic_amount, 2))
			->setCellValue('M' . $row_count, number_format($service_charge, 2))
			->setCellValue('N' . $row_count, number_format($tax_amount, 2))
			->setCellValue('O' . $row_count, number_format($tcs_amount, 2))
			->setCellValue('P' . $row_count, number_format($tds_amount, 2))
			->setCellValue('Q' . $row_count, number_format((float)($credit_card_charges), 2))
			->setCellValue('R' . $row_count, number_format($sale_amount, 2))
			->setCellValue('S' . $row_count, number_format((float)($cancel_amount), 2))
			->setCellValue('T' . $row_count, number_format($total_amount, 2))
			->setCellValue('U' . $row_count, number_format($total_paid, 2))
			// ->setCellValue('W' . $row_count, '') // Payment view button placeholder
			->setCellValue('V' . $row_count, number_format((float)($total_balance), 2))
			->setCellValue('W' . $row_count, get_date_user($row_flight['created_at']))
			->setCellValue('X' . $row_count, number_format((float)($total_purchase), 2).$currency_amount_3)
			// ->setCellValue('AA' . $row_count, '') // Supplier view button placeholder
			->setCellValue('Y' . $row_count, $branch_name)
			->setCellValue('Z' . $row_count, $emp_name)
			->setCellValue('AA' . $row_count, number_format((float)($incentive_amount), 2));

		$objPHPExcel->getActiveSheet()->getStyle('B' . $row_count . ':AA' . $row_count)->applyFromArray($content_style_Array);
		$objPHPExcel->getActiveSheet()->getStyle('B' . $row_count . ':AA' . $row_count)->applyFromArray($borderArray);
		$row_count++;

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
	$query = apply_common_filters_excel($query, $customer_id, $from_date, $to_date, $cust_type, $company_name, $booker_id, $branch_id);

	$sq_visa = mysqlQuery($query);
	while ($row_visa = mysqli_fetch_assoc($sq_visa)) {
		$count++;

		// Customer info
		$cust_info = get_customer_info_excel($row_visa['customer_id']);
		$customer_name = $cust_info['name'];
		$contact_no = $cust_info['contact'];
		$email_id = $cust_info['email'];

		// Employee and branch info
		$emp_branch = get_emp_branch_info_excel($row_visa['emp_id']);
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

		// Write data to Excel
		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('B' . $row_count, $count)
			->setCellValue('C' . $row_count, get_visa_booking_id($row_visa['visa_id'], $year))
			->setCellValue('D' . $row_count, $customer_name)
			->setCellValue('E' . $row_count, $contact_no)
			->setCellValue('F' . $row_count, $email_id)
			->setCellValue('G' . $row_count, $sq_total_member)
			->setCellValue('H' . $row_count, get_date_user($row_visa['created_at']))
			// ->setCellValue('I' . $row_count, '') // View button placeholder
			->setCellValue('I' . $row_count, 'NA')
			->setCellValue('J' . $row_count, $tour_name)
			->setCellValue('K' . $row_count, $tour_date)
			->setCellValue('L' . $row_count, number_format($basic_amount, 2))
			->setCellValue('M' . $row_count, number_format($service_charge, 2))
			->setCellValue('N' . $row_count, number_format($tax_amount, 2))
			->setCellValue('O' . $row_count, number_format($tcs_amount, 2))
			->setCellValue('P' . $row_count, number_format($tds_amount, 2))
			->setCellValue('Q' . $row_count, number_format((float)($credit_card_charges), 2))
			->setCellValue('R' . $row_count, number_format($sale_amount, 2))
			->setCellValue('S' . $row_count, number_format((float)($cancel_amount), 2))
			->setCellValue('T' . $row_count, number_format($total_amount, 2))
			->setCellValue('U' . $row_count, number_format($total_paid, 2))
			// ->setCellValue('W' . $row_count, '') // Payment view button placeholder
			->setCellValue('V' . $row_count, number_format((float)($total_balance), 2))
			->setCellValue('W' . $row_count, get_date_user($row_visa['created_at']))
			->setCellValue('X' . $row_count, number_format((float)($total_purchase), 2).$currency_amount_3)
			// ->setCellValue('AA' . $row_count, '') // Supplier view button placeholder
			->setCellValue('Y' . $row_count, $branch_name)
			->setCellValue('Z' . $row_count, $emp_name)
			->setCellValue('AA' . $row_count, number_format((float)($incentive_amount), 2));

		$objPHPExcel->getActiveSheet()->getStyle('B' . $row_count . ':AA' . $row_count)->applyFromArray($content_style_Array);
		$objPHPExcel->getActiveSheet()->getStyle('B' . $row_count . ':AA' . $row_count)->applyFromArray($borderArray);
		$row_count++;

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
	$query = apply_common_filters_excel($query, $customer_id, $from_date, $to_date, $cust_type, $company_name, $booker_id, $branch_id);

	$sq_car = mysqlQuery($query);
	while ($row_car = mysqli_fetch_assoc($sq_car)) {
		$count++;

		// Customer info
		$cust_info = get_customer_info_excel($row_car['customer_id']);
		$customer_name = $cust_info['name'];
		$contact_no = $cust_info['contact'];
		$email_id = $cust_info['email'];

		// Employee and branch info
		$emp_branch = get_emp_branch_info_excel($row_car['emp_id']);
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

		// Write data to Excel
		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('B' . $row_count, $count)
			->setCellValue('C' . $row_count, get_car_rental_booking_id($row_car['booking_id'], $year))
			->setCellValue('D' . $row_count, $customer_name)
			->setCellValue('E' . $row_count, $contact_no)
			->setCellValue('F' . $row_count, $email_id)
			->setCellValue('G' . $row_count, $sq_total_member)
			->setCellValue('H' . $row_count, get_date_user($row_car['created_at']))
			// ->setCellValue('I' . $row_count, '') // View button placeholder
			->setCellValue('I' . $row_count, 'NA')
			->setCellValue('J' . $row_count, $tour_name)
			->setCellValue('K' . $row_count, $tour_date)
			->setCellValue('L' . $row_count, number_format($basic_amount, 2))
			->setCellValue('M' . $row_count, number_format($service_charge, 2))
			->setCellValue('N' . $row_count, number_format($tax_amount, 2))
			->setCellValue('O' . $row_count, number_format($tcs_amount, 2))
			->setCellValue('P' . $row_count, number_format($tds_amount, 2))
			->setCellValue('Q' . $row_count, number_format((float)($credit_card_charges), 2))
			->setCellValue('R' . $row_count, number_format($sale_amount, 2))
			->setCellValue('S' . $row_count, number_format((float)($cancel_amount), 2))
			->setCellValue('T' . $row_count, number_format($total_amount, 2))
			->setCellValue('U' . $row_count, number_format($total_paid, 2))
			// ->setCellValue('W' . $row_count, '') // Payment view button placeholder
			->setCellValue('V' . $row_count, number_format((float)($total_balance), 2))
			->setCellValue('W' . $row_count, get_date_user($row_car['created_at']))
			->setCellValue('X' . $row_count, number_format((float)($total_purchase), 2).$currency_amount_3)
			// ->setCellValue('AA' . $row_count, '') // Supplier view button placeholder
			->setCellValue('Y' . $row_count, $branch_name)
			->setCellValue('Z' . $row_count, $emp_name)
			->setCellValue('AA' . $row_count, number_format((float)($incentive_amount), 2));

		$objPHPExcel->getActiveSheet()->getStyle('B' . $row_count . ':AA' . $row_count)->applyFromArray($content_style_Array);
		$objPHPExcel->getActiveSheet()->getStyle('B' . $row_count . ':AA' . $row_count)->applyFromArray($borderArray);
		$row_count++;

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
	$query = apply_common_filters_excel($query, $customer_id, $from_date, $to_date, $cust_type, $company_name, $booker_id, $branch_id);

	$sq_activity = mysqlQuery($query);
	while ($row_activity = mysqli_fetch_assoc($sq_activity)) {
		$count++;

		// Customer info
		$cust_info = get_customer_info_excel($row_activity['customer_id']);
		$customer_name = $cust_info['name'];
		$contact_no = $cust_info['contact'];
		$email_id = $cust_info['email'];

		// Employee and branch info
		$emp_branch = get_emp_branch_info_excel($row_activity['emp_id']);
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

		// Write data to Excel
		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('B' . $row_count, $count)
			->setCellValue('C' . $row_count, get_exc_booking_id($row_activity['exc_id'], $year))
			->setCellValue('D' . $row_count, $customer_name)
			->setCellValue('E' . $row_count, $contact_no)
			->setCellValue('F' . $row_count, $email_id)
			->setCellValue('G' . $row_count, $sq_total_member)
			->setCellValue('H' . $row_count, get_date_user($row_activity['created_at']))
			// ->setCellValue('I' . $row_count, '') // View button placeholder
			->setCellValue('I' . $row_count, 'NA')
			->setCellValue('J' . $row_count, $tour_name)
			->setCellValue('K' . $row_count, $tour_date)
			->setCellValue('L' . $row_count, number_format($basic_amount, 2))
			->setCellValue('M' . $row_count, number_format($service_charge, 2))
			->setCellValue('N' . $row_count, number_format($tax_amount, 2))
			->setCellValue('O' . $row_count, number_format($tcs_amount, 2))
			->setCellValue('P' . $row_count, number_format($tds_amount, 2))
			->setCellValue('Q' . $row_count, number_format((float)($credit_card_charges), 2))
			->setCellValue('R' . $row_count, number_format($sale_amount, 2))
			->setCellValue('S' . $row_count, number_format((float)($cancel_amount), 2))
			->setCellValue('T' . $row_count, number_format($total_amount, 2))
			->setCellValue('U' . $row_count, number_format($total_paid, 2))
			// ->setCellValue('W' . $row_count, '') // Payment view button placeholder
			->setCellValue('V' . $row_count, number_format((float)($total_balance), 2))
			->setCellValue('W' . $row_count, get_date_user($row_activity['created_at']))
			->setCellValue('X' . $row_count, number_format((float)($total_purchase), 2).$currency_amount_3)
			// ->setCellValue('AA' . $row_count, '') // Supplier view button placeholder
			->setCellValue('Y' . $row_count, $branch_name)
			->setCellValue('Z' . $row_count, $emp_name)
			->setCellValue('AA' . $row_count, number_format((float)($incentive_amount), 2));

		$objPHPExcel->getActiveSheet()->getStyle('B' . $row_count . ':AA' . $row_count)->applyFromArray($content_style_Array);
		$objPHPExcel->getActiveSheet()->getStyle('B' . $row_count . ':AA' . $row_count)->applyFromArray($borderArray);
		$row_count++;

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
	$query = apply_common_filters_excel($query, $customer_id, $from_date, $to_date, $cust_type, $company_name, $booker_id, $branch_id);

	$sq_bus = mysqlQuery($query);
	while ($row_bus = mysqli_fetch_assoc($sq_bus)) {
		$count++;

		// Customer info
		$cust_info = get_customer_info_excel($row_bus['customer_id']);
		$customer_name = $cust_info['name'];
		$contact_no = $cust_info['contact'];
		$email_id = $cust_info['email'];

		// Employee and branch info
		$emp_branch = get_emp_branch_info_excel($row_bus['emp_id']);
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

		// Write data to Excel
		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('B' . $row_count, $count)
			->setCellValue('C' . $row_count, get_bus_booking_id($row_bus['booking_id'], $year))
			->setCellValue('D' . $row_count, $customer_name)
			->setCellValue('E' . $row_count, $contact_no)
			->setCellValue('F' . $row_count, $email_id)
			->setCellValue('G' . $row_count, $sq_total_member)
			->setCellValue('H' . $row_count, get_date_user($row_bus['created_at']))
			// ->setCellValue('I' . $row_count, '') // View button placeholder
			->setCellValue('I' . $row_count, 'NA')
			->setCellValue('J' . $row_count, $tour_name)
			->setCellValue('K' . $row_count, $tour_date)
			->setCellValue('L' . $row_count, number_format($basic_amount, 2))
			->setCellValue('M' . $row_count, number_format($service_charge, 2))
			->setCellValue('N' . $row_count, number_format($tax_amount, 2))
			->setCellValue('O' . $row_count, number_format($tcs_amount, 2))
			->setCellValue('P' . $row_count, number_format($tds_amount, 2))
			->setCellValue('Q' . $row_count, number_format((float)($credit_card_charges), 2))
			->setCellValue('R' . $row_count, number_format($sale_amount, 2))
			->setCellValue('S' . $row_count, number_format((float)($cancel_amount), 2))
			->setCellValue('T' . $row_count, number_format($total_amount, 2))
			->setCellValue('u' . $row_count, number_format($total_paid, 2))
			// ->setCellValue('W' . $row_count, '') // Payment view button placeholder
			->setCellValue('V' . $row_count, number_format((float)($total_balance), 2))
			->setCellValue('W' . $row_count, get_date_user($row_bus['created_at']))
			->setCellValue('X' . $row_count, number_format((float)($total_purchase), 2).$currency_amount_3)
			// ->setCellValue('AA' . $row_count, '') // Supplier view button placeholder
			->setCellValue('Y' . $row_count, $branch_name)
			->setCellValue('Z' . $row_count, $emp_name)
			->setCellValue('AA' . $row_count, number_format((float)($incentive_amount), 2));

		$objPHPExcel->getActiveSheet()->getStyle('B' . $row_count . ':AA' . $row_count)->applyFromArray($content_style_Array);
		$objPHPExcel->getActiveSheet()->getStyle('B' . $row_count . ':AA' . $row_count)->applyFromArray($borderArray);
		$row_count++;

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
	$query = apply_common_filters_excel($query, $customer_id, $from_date, $to_date, $cust_type, $company_name, $booker_id, $branch_id);

	$sq_train = mysqlQuery($query);
	while ($row_train = mysqli_fetch_assoc($sq_train)) {
		$count++;

		// Customer info
		$cust_info = get_customer_info_excel($row_train['customer_id']);
		$customer_name = $cust_info['name'];
		$contact_no = $cust_info['contact'];
		$email_id = $cust_info['email'];

		// Employee and branch info
		$emp_branch = get_emp_branch_info_excel($row_train['emp_id']);
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

		// Write data to Excel
		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('B' . $row_count, $count)
			->setCellValue('C' . $row_count, get_train_ticket_booking_id($row_train['train_ticket_id'], $year))
			->setCellValue('D' . $row_count, $customer_name)
			->setCellValue('E' . $row_count, $contact_no)
			->setCellValue('F' . $row_count, $email_id)
			->setCellValue('G' . $row_count, $sq_total_member)
			->setCellValue('H' . $row_count, get_date_user($row_train['created_at']))
			// ->setCellValue('I' . $row_count, '') // View button placeholder
			->setCellValue('I' . $row_count, 'NA')
			->setCellValue('J' . $row_count, $tour_name)
			->setCellValue('K' . $row_count, $tour_date)
			->setCellValue('L' . $row_count, number_format($basic_amount, 2))
			->setCellValue('M' . $row_count, number_format($service_charge, 2))
			->setCellValue('N' . $row_count, number_format($tax_amount, 2))
			->setCellValue('O' . $row_count, number_format($tcs_amount, 2))
			->setCellValue('P' . $row_count, number_format($tds_amount, 2))
			->setCellValue('Q' . $row_count, number_format((float)($credit_card_charges), 2))
			->setCellValue('R' . $row_count, number_format($sale_amount, 2))
			->setCellValue('S' . $row_count, number_format((float)($cancel_amount), 2))
			->setCellValue('T' . $row_count, number_format($total_amount, 2))
			->setCellValue('U' . $row_count, number_format($total_paid, 2))
			// ->setCellValue('W' . $row_count, '') // Payment view button placeholder
			->setCellValue('V' . $row_count, number_format((float)($total_balance), 2))
			->setCellValue('W' . $row_count, get_date_user($row_train['created_at']))
			->setCellValue('X' . $row_count, number_format((float)($total_purchase), 2).$currency_amount_3)
			// ->setCellValue('AA' . $row_count, '') // Supplier view button placeholder
			->setCellValue('Y' . $row_count, $branch_name)
			->setCellValue('Z' . $row_count, $emp_name)
			->setCellValue('AA' . $row_count, number_format((float)($incentive_amount), 2));

		$objPHPExcel->getActiveSheet()->getStyle('B' . $row_count . ':AA' . $row_count)->applyFromArray($content_style_Array);
		$objPHPExcel->getActiveSheet()->getStyle('B' . $row_count . ':AA' . $row_count)->applyFromArray($borderArray);
		$row_count++;

		// Update totals
		$global_total_cancel += $cancel_amount;
		$global_total_sale += $total_amount;
		$global_total_paid += $sq_paid_amount['sum'];
		$global_total_balance += $total_balance;
	}
}

// ===== MISCELLANEOUS BOOKING =====
if ($travel_type == "" || $travel_type == "Miscellaneous") {
	$query = "select * from miscellaneous_master where 1 and delete_status='0' ";
	$query = apply_common_filters_excel($query, $customer_id, $from_date, $to_date, $cust_type, $company_name, $booker_id, $branch_id);

	$sq_misc = mysqlQuery($query);
	while ($row_misc = mysqli_fetch_assoc($sq_misc)) {
		$count++;

		// Customer info
		$cust_info = get_customer_info_excel($row_misc['customer_id']);
		$customer_name = $cust_info['name'];
		$contact_no = $cust_info['contact'];
		$email_id = $cust_info['email'];

		// Employee and branch info
		$emp_branch = get_emp_branch_info_excel($row_misc['emp_id']);
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

		// Write data to Excel
		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('B' . $row_count, $count)
			->setCellValue('C' . $row_count, get_misc_booking_id($row_misc['misc_id'], $year))
			->setCellValue('D' . $row_count, $customer_name)
			->setCellValue('E' . $row_count, $contact_no)
			->setCellValue('F' . $row_count, $email_id)
			->setCellValue('G' . $row_count, $sq_total_member)
			->setCellValue('H' . $row_count, get_date_user($row_misc['created_at']))
			// ->setCellValue('I' . $row_count, '') // View button placeholder
			->setCellValue('I' . $row_count, 'NA')
			->setCellValue('J' . $row_count, $tour_name)
			->setCellValue('K' . $row_count, $tour_date)
			->setCellValue('L' . $row_count, number_format($basic_amount, 2))
			->setCellValue('M' . $row_count, number_format($service_charge, 2))
			->setCellValue('N' . $row_count, number_format($tax_amount, 2))
			->setCellValue('O' . $row_count, number_format($tcs_amount, 2))
			->setCellValue('P' . $row_count, number_format($tds_amount, 2))
			->setCellValue('Q' . $row_count, number_format((float)($credit_card_charges), 2))
			->setCellValue('R' . $row_count, number_format($sale_amount, 2))
			->setCellValue('S' . $row_count, number_format((float)($cancel_amount), 2))
			->setCellValue('T' . $row_count, number_format($total_amount, 2))
			->setCellValue('U' . $row_count, number_format($total_paid, 2))
			// ->setCellValue('W' . $row_count, '') // Payment view button placeholder
			->setCellValue('V' . $row_count, number_format((float)($total_balance), 2))
			->setCellValue('W' . $row_count, get_date_user($row_misc['created_at']))
			->setCellValue('X' . $row_count, number_format((float)($total_purchase), 2).$currency_amount_3)
			// ->setCellValue('AA' . $row_count, '') // Supplier view button placeholder
			->setCellValue('Y' . $row_count, $branch_name)
			->setCellValue('Z' . $row_count, $emp_name)
			->setCellValue('AA' . $row_count, number_format((float)($incentive_amount), 2));

		$objPHPExcel->getActiveSheet()->getStyle('B' . $row_count . ':AA' . $row_count)->applyFromArray($content_style_Array);
		$objPHPExcel->getActiveSheet()->getStyle('B' . $row_count . ':AA' . $row_count)->applyFromArray($borderArray);
		$row_count++;

		// Update totals
		$global_total_cancel += $cancel_amount;
		$global_total_sale += $total_amount;
		$global_total_paid += $total_paid;
		$global_total_balance += $total_balance;
	}
}

//////////////////////////****************Content End**************////////////////////////////////

// Add totals row
$row_count++;
$objPHPExcel->setActiveSheetIndex(0)
  ->setCellValue('B' . $row_count, '')
  ->setCellValue('C' . $row_count, '')
  ->setCellValue('D' . $row_count, '')
  ->setCellValue('E' . $row_count, '')
  ->setCellValue('F' . $row_count, '')
  ->setCellValue('G' . $row_count, '')
  ->setCellValue('H' . $row_count, '')
//   ->setCellValue('I' . $row_count, '')
  ->setCellValue('I' . $row_count, '')
  ->setCellValue('J' . $row_count, '')
  ->setCellValue('K' . $row_count, '')
  ->setCellValue('L' . $row_count, '')
  ->setCellValue('M' . $row_count, '')
  ->setCellValue('N' . $row_count, '')
  ->setCellValue('O' . $row_count, '')
  ->setCellValue('P' . $row_count, '')
  ->setCellValue('Q' . $row_count, '')
  ->setCellValue('R' . $row_count, 'Total')
  ->setCellValue('S' . $row_count,  number_format($global_total_cancel, 2))
  ->setCellValue('T' . $row_count,  number_format($global_total_sale, 2))
  ->setCellValue('U' . $row_count, number_format($global_total_paid, 2))
//   ->setCellValue('W' . $row_count, '')
  ->setCellValue('V' . $row_count, number_format($global_total_balance, 2))
  ->setCellValue('W' . $row_count, '')
  ->setCellValue('X' . $row_count, '')
//   ->setCellValue('AA' . $row_count, '')
  ->setCellValue('Y' . $row_count, '')
  ->setCellValue('Z' . $row_count, '')
  ->setCellValue('AA' . $row_count, '');

$objPHPExcel->getActiveSheet()->getStyle('B' . $row_count . ':AA' . $row_count)->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B' . $row_count . ':AA' . $row_count)->applyFromArray($borderArray);

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Consolidated Sales Report');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Auto size columns
for ($col = 'A'; $col !== 'AE'; $col++) {
  $objPHPExcel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
}

// Redirect output to a client's web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Consolidated_Sales_Report(' . date('d-m-Y H:i') . ').xls"');
header('Cache-Control: max-age=0');

// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');

exit;
?>
