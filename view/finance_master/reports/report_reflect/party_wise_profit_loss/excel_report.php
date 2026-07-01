<?php
include "../../../../../model/model.php";
global $currency;

/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/London');

if (PHP_SAPI == 'cli')
  die('This example should only be run from a Web Browser');

/** Include PHPExcel */
require_once '../../../../../classes/PHPExcel-1.8/Classes/PHPExcel.php';

//This function generates the background color
function cellColor($cells,$color){
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
    ));
$table_header_style_Array = array(
    'font'  => array(
        'bold'  => false,
        'color' => array('rgb' => '000000'),
        'size'  => 11,
        'name'  => 'Verdana'
    ));
$content_style_Array = array(
    'font'  => array(
        'bold'  => false,
        'color' => array('rgb' => '000000'),
        'size'  => 9,
        'name'  => 'Verdana'
    ));

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
$objPHPExcel->getProperties()->setCreator("Tour Operator")
->setLastModifiedBy("Tour Operator")
->setTitle("Party Wise Profit & Loss Report")
->setSubject("Party Wise Profit & Loss Report")
->setDescription("Party Wise Profit & Loss Report generated using PHP classes.")
->setKeywords("office 2007 openxml php")
->setCategory("Report result file");

//////////////////////////****************Content start**************////////////////////////////////

$customer_id = $_GET['customer_id'];
$from_date = $_GET['from_date'];
$to_date = $_GET['to_date'];
$branch_status = $_GET['branch_status'];
$branch_admin_id = $_GET['branch_admin_id'];
$role = $_GET['role'];

// Get customer name
$sq_customer = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$customer_id'"));
if($sq_customer['type'] == 'Corporate'||$sq_customer['type']=='B2B'){
	$customer_name = $sq_customer['company_name'];
}else{
	$customer_name = $sq_customer['first_name'].' '.$sq_customer['last_name'];
}

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

// Add some data
$objPHPExcel->setActiveSheetIndex(0)
	->setCellValue('B2', 'Report Name')
	->setCellValue('C2', 'Party Wise Profit & Loss Report')
	->setCellValue('B3', 'Customer Name')
	->setCellValue('C3', $customer_name);

if($from_date != '' && $to_date != ''){
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('B4', 'From Date')
		->setCellValue('C4', $from_date)
		->setCellValue('B5', 'To Date')
		->setCellValue('C5', $to_date);
	$objPHPExcel->getActiveSheet()->getStyle('B4:C4')->applyFromArray($header_style_Array);
	$objPHPExcel->getActiveSheet()->getStyle('B4:C4')->applyFromArray($borderArray);
	$objPHPExcel->getActiveSheet()->getStyle('B5:C5')->applyFromArray($header_style_Array);
	$objPHPExcel->getActiveSheet()->getStyle('B5:C5')->applyFromArray($borderArray);
	$row_count = 7;
} else {
	$row_count = 5;
}

$objPHPExcel->getActiveSheet()->getStyle('B2:C2')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B2:C2')->applyFromArray($borderArray);
$objPHPExcel->getActiveSheet()->getStyle('B3:C3')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B3:C3')->applyFromArray($borderArray);

// Table Headers
$objPHPExcel->setActiveSheetIndex(0)
	->setCellValue('B'.$row_count, 'Sr. No.')
	->setCellValue('C'.$row_count, 'Service Name')
	->setCellValue('D'.$row_count, 'Booking ID')
	->setCellValue('E'.$row_count, 'Total Sale (Without Tax)')
	->setCellValue('F'.$row_count, 'Total Purchase (Without Tax)')
	->setCellValue('G'.$row_count, 'Profit/Loss');

$objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':G'.$row_count)->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':G'.$row_count)->applyFromArray($borderArray);
$row_count++;

$count = 0;
$total_sale = 0;
$total_purchase = 0;
$total_profit_loss = 0;

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
	
	$tour_fee = $row_package['net_total'];
	$q1 = "SELECT * from package_refund_traveler_estimate where booking_id='$row_package[booking_id]'";
	$row_esti = mysqli_fetch_assoc(mysqlQuery($q1));
	$tour_esti = isset($row_esti['cancel_amount']) ? $row_esti['cancel_amount'] : 0;
	$total_sale_amount = $tour_fee - $tour_esti;
	
	$service_tax_amount = calculate_tax_amount($row_package['tour_service_tax_subtotal']);
	$tcs_amount = $row_package['tcs_per'];
	$sale_without_tax = $total_sale_amount - $service_tax_amount - $tcs_amount;
	
	$total_purchase_amount = get_purchase_amount('Package Tour', $row_package['booking_id']);
	$profit_loss = $sale_without_tax - $total_purchase_amount;
	$profit_loss_percentage = ($sale_without_tax > 0) ? (($profit_loss / $sale_without_tax) * 100) : 0;
	$profit_loss_text = ($profit_loss >= 0) ? 'Profit' : 'Loss';
	$profit_loss_display = number_format($profit_loss, 2) . ' (' . number_format($profit_loss_percentage, 2) . '%) ' . $profit_loss_text;
	
	$year = explode("-", $row_package['booking_date']);
	$year = $year[0];
	$booking_id = get_package_booking_id($row_package['booking_id'], $year);
	
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('B'.$row_count, $count)
		->setCellValue('C'.$row_count, 'Package Tour')
		->setCellValue('D'.$row_count, $booking_id)
		->setCellValue('E'.$row_count, number_format($sale_without_tax, 2))
		->setCellValue('F'.$row_count, number_format($total_purchase_amount, 2))
		->setCellValue('G'.$row_count, $profit_loss_display);
	
	$objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':G'.$row_count)->applyFromArray($content_style_Array);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':G'.$row_count)->applyFromArray($borderArray);
	$row_count++;
	
	$total_sale += $sale_without_tax;
	$total_purchase += $total_purchase_amount;
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
	
	$tour_fee = $row_group['total_tour_fee'] + $row_group['total_travel_expense'];
	$cancel_amount = $row_group['cancel_amount'];
	$total_sale_amount = $tour_fee - $cancel_amount;
	
	$service_tax_amount = calculate_tax_amount($row_group['service_tax_subtotal']);
	$tcs_amount = isset($row_group['tcs']) ? $row_group['tcs'] : 0;
	$sale_without_tax = $total_sale_amount - $service_tax_amount - $tcs_amount;
	
	$total_purchase_amount = get_purchase_amount('Group Tour', $row_group['traveler_group_id']);
	$profit_loss = $sale_without_tax - $total_purchase_amount;
	$profit_loss_percentage = ($sale_without_tax > 0) ? (($profit_loss / $sale_without_tax) * 100) : 0;
	$profit_loss_text = ($profit_loss >= 0) ? 'Profit' : 'Loss';
	$profit_loss_display = number_format($profit_loss, 2) . ' (' . number_format($profit_loss_percentage, 2) . '%) ' . $profit_loss_text;
	
	$year = explode("-", $row_group['form_date']);
	$year = $year[0];
	$booking_id = get_group_booking_id($row_group['traveler_group_id'], $year);
	
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('B'.$row_count, $count)
		->setCellValue('C'.$row_count, 'Group Tour')
		->setCellValue('D'.$row_count, $booking_id)
		->setCellValue('E'.$row_count, number_format($sale_without_tax, 2))
		->setCellValue('F'.$row_count, number_format($total_purchase_amount, 2))
		->setCellValue('G'.$row_count, $profit_loss_display);
	
	$objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':G'.$row_count)->applyFromArray($content_style_Array);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':G'.$row_count)->applyFromArray($borderArray);
	$row_count++;
	
	$total_sale += $sale_without_tax;
	$total_purchase += $total_purchase_amount;
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
	
	$sale_amount = $row_hotel['total_fee'] - $row_hotel['cancel_amount'];
	$tax_amount = calculate_tax_amount($row_hotel['service_tax_subtotal']);
	$markupservice_tax_amount = calculate_tax_amount($row_hotel['markup_tax']);
	$total_tax = $tax_amount + $markupservice_tax_amount;
	$tcs_amount = isset($row_hotel['tcs_tax']) ? $row_hotel['tcs_tax'] : 0;
	$sale_without_tax = $sale_amount - $total_tax - $tcs_amount;
	
	$total_purchase_amount = get_purchase_amount('Hotel', $row_hotel['booking_id']);
	$profit_loss = $sale_without_tax - $total_purchase_amount;
	$profit_loss_percentage = ($sale_without_tax > 0) ? (($profit_loss / $sale_without_tax) * 100) : 0;
	$profit_loss_text = ($profit_loss >= 0) ? 'Profit' : 'Loss';
	$profit_loss_display = number_format($profit_loss, 2) . ' (' . number_format($profit_loss_percentage, 2) . '%) ' . $profit_loss_text;
	
	$year = explode("-", $row_hotel['created_at']);
	$year = $year[0];
	$booking_id = get_hotel_booking_id($row_hotel['booking_id'], $year);
	
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('B'.$row_count, $count)
		->setCellValue('C'.$row_count, 'Hotel Booking')
		->setCellValue('D'.$row_count, $booking_id)
		->setCellValue('E'.$row_count, number_format($sale_without_tax, 2))
		->setCellValue('F'.$row_count, number_format($total_purchase_amount, 2))
		->setCellValue('G'.$row_count, $profit_loss_display);
	
	$objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':G'.$row_count)->applyFromArray($content_style_Array);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':G'.$row_count)->applyFromArray($borderArray);
	$row_count++;
	
	$total_sale += $sale_without_tax;
	$total_purchase += $total_purchase_amount;
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
	
	$total_sale_amount = $row_car['total_fees'] - $row_car['cancel_amount'];
	$tax_amount = calculate_tax_amount($row_car['service_tax_subtotal']);
	$markupservice_tax_amount = calculate_tax_amount($row_car['markup_cost_subtotal']);
	$total_tax = $tax_amount + $markupservice_tax_amount;
	$tcs_amount = isset($row_car['tcs_tax']) ? $row_car['tcs_tax'] : 0;
	$sale_without_tax = $total_sale_amount - $total_tax - $tcs_amount;
	
	$total_purchase_amount = get_purchase_amount('Car Rental', $row_car['booking_id']);
	$profit_loss = $sale_without_tax - $total_purchase_amount;
	$profit_loss_percentage = ($sale_without_tax > 0) ? (($profit_loss / $sale_without_tax) * 100) : 0;
	$profit_loss_text = ($profit_loss >= 0) ? 'Profit' : 'Loss';
	$profit_loss_display = number_format($profit_loss, 2) . ' (' . number_format($profit_loss_percentage, 2) . '%) ' . $profit_loss_text;
	
	$year = explode("-", $row_car['created_at']);
	$year = $year[0];
	$booking_id = get_car_rental_booking_id($row_car['booking_id'], $year);
	
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('B'.$row_count, $count)
		->setCellValue('C'.$row_count, 'Car Rental Booking')
		->setCellValue('D'.$row_count, $booking_id)
		->setCellValue('E'.$row_count, number_format($sale_without_tax, 2))
		->setCellValue('F'.$row_count, number_format($total_purchase_amount, 2))
		->setCellValue('G'.$row_count, $profit_loss_display);
	
	$objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':G'.$row_count)->applyFromArray($content_style_Array);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':G'.$row_count)->applyFromArray($borderArray);
	$row_count++;
	
	$total_sale += $sale_without_tax;
	$total_purchase += $total_purchase_amount;
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
	
	$total_sale_amount = $row_flight['ticket_total_cost'] - $row_flight['cancel_amount'];
	$tax_amount = calculate_tax_amount($row_flight['service_tax_subtotal']);
	$markupservice_tax_amount = calculate_tax_amount($row_flight['service_tax_markup']);
	$total_tax = $tax_amount + $markupservice_tax_amount;
	$tcs_amount = isset($row_flight['tds']) ? $row_flight['tds'] : 0;
	$sale_without_tax = $total_sale_amount - $total_tax - $tcs_amount;
	
	$total_purchase_amount = get_purchase_amount('Flight', $row_flight['ticket_id']);
	$profit_loss = $sale_without_tax - $total_purchase_amount;
	$profit_loss_percentage = ($sale_without_tax > 0) ? (($profit_loss / $sale_without_tax) * 100) : 0;
	$profit_loss_text = ($profit_loss >= 0) ? 'Profit' : 'Loss';
	$profit_loss_display = number_format($profit_loss, 2) . ' (' . number_format($profit_loss_percentage, 2) . '%) ' . $profit_loss_text;
	
	$year = explode("-", $row_flight['created_at']);
	$year = $year[0];
	$booking_id = get_ticket_booking_id($row_flight['ticket_id'], $year);
	
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('B'.$row_count, $count)
		->setCellValue('C'.$row_count, 'Flight Booking')
		->setCellValue('D'.$row_count, $booking_id)
		->setCellValue('E'.$row_count, number_format($sale_without_tax, 2))
		->setCellValue('F'.$row_count, number_format($total_purchase_amount, 2))
		->setCellValue('G'.$row_count, $profit_loss_display);
	
	$objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':G'.$row_count)->applyFromArray($content_style_Array);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':G'.$row_count)->applyFromArray($borderArray);
	$row_count++;
	
	$total_sale += $sale_without_tax;
	$total_purchase += $total_purchase_amount;
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
	
	$total_sale_amount = $row_activity['exc_total_cost'] - $row_activity['cancel_amount'];
	$tax_amount = calculate_tax_amount($row_activity['service_tax_subtotal']);
	$markupservice_tax_amount = calculate_tax_amount($row_activity['service_tax_markup']);
	$total_tax = $tax_amount + $markupservice_tax_amount;
	$tcs_amount = isset($row_activity['tcs']) ? $row_activity['tcs'] : 0;
	$sale_without_tax = $total_sale_amount - $total_tax - $tcs_amount;
	
	$total_purchase_amount = get_purchase_amount('Activity', $row_activity['exc_id']);
	$profit_loss = $sale_without_tax - $total_purchase_amount;
	$profit_loss_percentage = ($sale_without_tax > 0) ? (($profit_loss / $sale_without_tax) * 100) : 0;
	$profit_loss_text = ($profit_loss >= 0) ? 'Profit' : 'Loss';
	$profit_loss_display = number_format($profit_loss, 2) . ' (' . number_format($profit_loss_percentage, 2) . '%) ' . $profit_loss_text;
	
	$year = explode("-", $row_activity['created_at']);
	$year = $year[0];
	$booking_id = get_exc_booking_id($row_activity['exc_id'], $year);
	
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('B'.$row_count, $count)
		->setCellValue('C'.$row_count, 'Activity Booking')
		->setCellValue('D'.$row_count, $booking_id)
		->setCellValue('E'.$row_count, number_format($sale_without_tax, 2))
		->setCellValue('F'.$row_count, number_format($total_purchase_amount, 2))
		->setCellValue('G'.$row_count, $profit_loss_display);
	
	$objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':G'.$row_count)->applyFromArray($content_style_Array);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':G'.$row_count)->applyFromArray($borderArray);
	$row_count++;
	
	$total_sale += $sale_without_tax;
	$total_purchase += $total_purchase_amount;
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
	
	$sale_amount = $row_visa['visa_total_cost'] - $row_visa['cancel_amount'];
	$tax_amount = calculate_tax_amount($row_visa['service_tax_subtotal']);
	$markupservice_tax_amount = calculate_tax_amount($row_visa['markup_tax']);
	$total_tax = $tax_amount + $markupservice_tax_amount;
	$tcs_amount = isset($row_visa['tcs']) ? $row_visa['tcs'] : 0;
	$sale_without_tax = $sale_amount - $total_tax - $tcs_amount;
	
	$total_purchase_amount = get_purchase_amount('Visa', $row_visa['visa_id']);
	$profit_loss = $sale_without_tax - $total_purchase_amount;
	$profit_loss_percentage = ($sale_without_tax > 0) ? (($profit_loss / $sale_without_tax) * 100) : 0;
	$profit_loss_text = ($profit_loss >= 0) ? 'Profit' : 'Loss';
	$profit_loss_display = number_format($profit_loss, 2) . ' (' . number_format($profit_loss_percentage, 2) . '%) ' . $profit_loss_text;
	
	$year = explode("-", $row_visa['created_at']);
	$year = $year[0];
	$booking_id = get_visa_booking_id($row_visa['visa_id'], $year);
	
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('B'.$row_count, $count)
		->setCellValue('C'.$row_count, 'Visa Booking')
		->setCellValue('D'.$row_count, $booking_id)
		->setCellValue('E'.$row_count, number_format($sale_without_tax, 2))
		->setCellValue('F'.$row_count, number_format($total_purchase_amount, 2))
		->setCellValue('G'.$row_count, $profit_loss_display);
	
	$objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':G'.$row_count)->applyFromArray($content_style_Array);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':G'.$row_count)->applyFromArray($borderArray);
	$row_count++;
	
	$total_sale += $sale_without_tax;
	$total_purchase += $total_purchase_amount;
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
	
	$total_sale_amount = $row_train['net_total'] - $row_train['cancel_amount'];
	$tax_amount = calculate_tax_amount($row_train['service_tax_subtotal']);
	$markupservice_tax_amount = calculate_tax_amount($row_train['service_tax_markup']);
	$total_tax = $tax_amount + $markupservice_tax_amount;
	$tcs_amount = isset($row_train['tcs']) ? $row_train['tcs'] : 0;
	$sale_without_tax = $total_sale_amount - $total_tax - $tcs_amount;
	
	$total_purchase_amount = get_purchase_amount('Train', $row_train['train_ticket_id']);
	$profit_loss = $sale_without_tax - $total_purchase_amount;
	$profit_loss_percentage = ($sale_without_tax > 0) ? (($profit_loss / $sale_without_tax) * 100) : 0;
	$profit_loss_text = ($profit_loss >= 0) ? 'Profit' : 'Loss';
	$profit_loss_display = number_format($profit_loss, 2) . ' (' . number_format($profit_loss_percentage, 2) . '%) ' . $profit_loss_text;
	
	$year = explode("-", $row_train['created_at']);
	$year = $year[0];
	$booking_id = get_train_ticket_booking_id($row_train['train_ticket_id'], $year);
	
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('B'.$row_count, $count)
		->setCellValue('C'.$row_count, 'Train Booking')
		->setCellValue('D'.$row_count, $booking_id)
		->setCellValue('E'.$row_count, number_format($sale_without_tax, 2))
		->setCellValue('F'.$row_count, number_format($total_purchase_amount, 2))
		->setCellValue('G'.$row_count, $profit_loss_display);
	
	$objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':G'.$row_count)->applyFromArray($content_style_Array);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':G'.$row_count)->applyFromArray($borderArray);
	$row_count++;
	
	$total_sale += $sale_without_tax;
	$total_purchase += $total_purchase_amount;
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
	
	$total_sale_amount = $row_bus['net_total'] - $row_bus['cancel_amount'];
	$tax_amount = calculate_tax_amount($row_bus['service_tax_subtotal']);
	$markupservice_tax_amount = calculate_tax_amount($row_bus['service_tax_markup']);
	$total_tax = $tax_amount + $markupservice_tax_amount;
	$tcs_amount = isset($row_bus['tcs']) ? $row_bus['tcs'] : 0;
	$sale_without_tax = $total_sale_amount - $total_tax - $tcs_amount;
	
	$total_purchase_amount = get_purchase_amount('Bus', $row_bus['booking_id']);
	$profit_loss = $sale_without_tax - $total_purchase_amount;
	$profit_loss_percentage = ($sale_without_tax > 0) ? (($profit_loss / $sale_without_tax) * 100) : 0;
	$profit_loss_text = ($profit_loss >= 0) ? 'Profit' : 'Loss';
	$profit_loss_display = number_format($profit_loss, 2) . ' (' . number_format($profit_loss_percentage, 2) . '%) ' . $profit_loss_text;
	
	$year = explode("-", $row_bus['created_at']);
	$year = $year[0];
	$booking_id = get_bus_booking_id($row_bus['booking_id'], $year);
	
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('B'.$row_count, $count)
		->setCellValue('C'.$row_count, 'Bus Booking')
		->setCellValue('D'.$row_count, $booking_id)
		->setCellValue('E'.$row_count, number_format($sale_without_tax, 2))
		->setCellValue('F'.$row_count, number_format($total_purchase_amount, 2))
		->setCellValue('G'.$row_count, $profit_loss_display);
	
	$objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':G'.$row_count)->applyFromArray($content_style_Array);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':G'.$row_count)->applyFromArray($borderArray);
	$row_count++;
	
	$total_sale += $sale_without_tax;
	$total_purchase += $total_purchase_amount;
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
	
	$total_sale_amount = $row_misc['misc_total_cost'] - $row_misc['cancel_amount'];
	$tax_amount = calculate_tax_amount($row_misc['service_tax_subtotal']);
	$markupservice_tax_amount = calculate_tax_amount($row_misc['service_tax_markup']);
	$total_tax = $tax_amount + $markupservice_tax_amount;
	$tcs_amount = isset($row_misc['tcs']) ? $row_misc['tcs'] : 0;
	$sale_without_tax = $total_sale_amount - $total_tax - $tcs_amount;
	
	$total_purchase_amount = get_purchase_amount('Miscellaneous', $row_misc['misc_id']);
	$profit_loss = $sale_without_tax - $total_purchase_amount;
	$profit_loss_percentage = ($sale_without_tax > 0) ? (($profit_loss / $sale_without_tax) * 100) : 0;
	$profit_loss_text = ($profit_loss >= 0) ? 'Profit' : 'Loss';
	$profit_loss_display = number_format($profit_loss, 2) . ' (' . number_format($profit_loss_percentage, 2) . '%) ' . $profit_loss_text;
	
	$year = explode("-", $row_misc['created_at']);
	$year = $year[0];
	$booking_id = get_misc_booking_id($row_misc['misc_id'], $year);
	
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('B'.$row_count, $count)
		->setCellValue('C'.$row_count, 'Miscellaneous Booking')
		->setCellValue('D'.$row_count, $booking_id)
		->setCellValue('E'.$row_count, number_format($sale_without_tax, 2))
		->setCellValue('F'.$row_count, number_format($total_purchase_amount, 2))
		->setCellValue('G'.$row_count, $profit_loss_display);
	
	$objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':G'.$row_count)->applyFromArray($content_style_Array);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':G'.$row_count)->applyFromArray($borderArray);
	$row_count++;
	
	$total_sale += $sale_without_tax;
	$total_purchase += $total_purchase_amount;
	$total_profit_loss += $profit_loss;
}

// Total Row
$total_profit_loss_percentage = ($total_sale > 0) ? (($total_profit_loss / $total_sale) * 100) : 0;
$total_profit_loss_text = ($total_profit_loss >= 0) ? 'Profit' : 'Loss';
$total_profit_loss_display = number_format($total_profit_loss, 2) . ' (' . number_format($total_profit_loss_percentage, 2) . '%) ' . $total_profit_loss_text;

$objPHPExcel->setActiveSheetIndex(0)
	->setCellValue('B'.$row_count, '')
	->setCellValue('C'.$row_count, '')
	->setCellValue('D'.$row_count, 'Total')
	->setCellValue('E'.$row_count, number_format($total_sale, 2))
	->setCellValue('F'.$row_count, number_format($total_purchase, 2))
	->setCellValue('G'.$row_count, $total_profit_loss_display);

$objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':G'.$row_count)->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':G'.$row_count)->applyFromArray($borderArray);

//////////////////////////****************Content End**************////////////////////////////////

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Simple');

// Auto size columns
for($col = 'A'; $col !== 'H'; $col++){
	$objPHPExcel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
}

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client's web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Party Wise Profit & Loss Report('.date('d-m-Y H:i').').xls"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');
// If you're serving to IE over SSL, then the following may be needed
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: '.gmdate('D, d M Y H:i').' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;
?>

