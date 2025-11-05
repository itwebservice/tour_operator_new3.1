<?php
include "../../../../../model/model.php";

/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/London');

if (PHP_SAPI == 'cli')
    die('This example should only be run from a Web Browser');

/** Include PHPExcel */
require_once  '../../../../../classes/PHPExcel-1.8/Classes/PHPExcel.php';

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

//This array sets the font attributes
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

//mainQuery
$tour_id = $_GET['tourName'];
$group_id = $_GET['tourDate'];
$role = $_SESSION['role'];
$branch_admin_id = $_SESSION['branch_admin_id'];
$branch_status = $_GET['branch_status'];
$count=0;

$query = "select * from tourwise_traveler_details where 1 and delete_status='0'";

if($tour_id!="")
{
	$query .= " and tour_id = '$tour_id'";
}
if($group_id!="")
{
	$query .= " and tour_group_id = '$group_id'";
}
if($branch_status=='yes' && $role=='Branch Admin'){
    $query .= " and  branch_admin_id = '$branch_admin_id'";
}
$query .= " order by id desc";
 
$sq_tourwise_det = mysqlQuery($query);

//mainQuery

// Fetch tour name and tour date if provided
$tour_id_single_data = null;
if (!empty($tour_id)) {
    $tour_id_single_data = mysqli_fetch_assoc(mysqlQuery("select tour_id,tour_name from tour_master where active_flag='Active' and tour_id='$tour_id'"));
}
$group_single_from_to_date = null;

if(!empty($group_id))
{
    $group_id_single_data = mysqli_fetch_assoc(mysqlQuery("select * from tour_groups where group_id='$group_id'"));
    $from_date=$group_id_single_data['from_date'];
    $to_date=$group_id_single_data['to_date'];
    $group_single_from_date=date("d-m-Y", strtotime($from_date));  
    $group_single_to_date=date("d-m-Y", strtotime($to_date)); 
    $group_single_from_to_date = $group_single_from_date.' to '.$group_single_to_date;
}

// Add some data
$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('B2', 'Report Name')
    ->setCellValue('C2', 'Booking Wise Total Selling Report')
    ->setCellValue('B3', 'Tour Name')
    ->setCellValue('C3', ($tour_id_single_data != null ? $tour_id_single_data['tour_name'] : 'All Tours'))
   
    ->setCellValue('B4', 'Tour Date')
    ->setCellValue('C4', ($group_single_from_to_date != null ? $group_single_from_to_date : 'All Dates'));

$objPHPExcel->getActiveSheet()->getStyle('B2:C2')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B2:C2')->applyFromArray($borderArray);

$objPHPExcel->getActiveSheet()->getStyle('B3:C3')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B3:C3')->applyFromArray($borderArray);

$objPHPExcel->getActiveSheet()->getStyle('B4:C4')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B4:C4')->applyFromArray($borderArray);

$count = 0;
$total_selling = 0;
$total_paid = 0;
$total_balance = 0;
$row_count = 8;

$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('B' . $row_count, "Sr.No")
    ->setCellValue('C' . $row_count, "Booking ID")
    ->setCellValue('D' . $row_count, "Customer Name")
    ->setCellValue('E' . $row_count, "Selling Amount")
    ->setCellValue('F' . $row_count, "Paid Amount")
    ->setCellValue('G' . $row_count, "Balance Amount");

$objPHPExcel->getActiveSheet()->getStyle('B' . $row_count . ':G' . $row_count)->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B' . $row_count . ':G' . $row_count)->applyFromArray($borderArray);

$row_count++;

while ($row_tourwise_det = mysqli_fetch_assoc($sq_tourwise_det)) {

    $count++;
	$date = $row_tourwise_det['form_date'];
	$yr = explode("-", $date);
	$year =$yr[0];

	// Get customer details
	$sq_customer = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$row_tourwise_det[customer_id]'"));
	if ($sq_customer['type'] == 'Corporate' || $sq_customer['type'] == 'B2B') {
		$customer_name = $sq_customer['company_name'];
	} else {
		$customer_name = $sq_customer['first_name'] . ' ' . $sq_customer['last_name'];
	}

	// Get booking ID
	$booking_id = get_group_booking_id($row_tourwise_det['id'],$year);

	// Calculate amounts
	$selling_amount = $row_tourwise_det['net_total'];
	
	// Get total paid amount
	$sq_paid = mysqli_fetch_assoc(mysqlQuery("select sum(amount) as sum, sum(credit_charges) as sumc from payment_master where tourwise_traveler_id='$row_tourwise_det[id]' and clearance_status!='Pending' and clearance_status!='Cancelled'"));
	$payment_sum = ($sq_paid['sum'] != '' && $sq_paid['sum'] != null) ? $sq_paid['sum'] : 0;
	$credit_sum = ($sq_paid['sumc'] != '' && $sq_paid['sumc'] != null) ? $sq_paid['sumc'] : 0;
	$paid_amount = $payment_sum + $credit_sum;
	
	// Calculate balance (including credit charges in selling amount)
	$selling_amount_total = $selling_amount + $credit_sum;
	$balance_amount = $selling_amount_total - $paid_amount;

	// Add to totals
	$total_selling += $selling_amount_total;
	$total_paid += $paid_amount;
	$total_balance += $balance_amount;

    $objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('B' . $row_count, $count)
    ->setCellValue('C' . $row_count, $booking_id)
    ->setCellValue('D' . $row_count, $customer_name)
    ->setCellValue('E' . $row_count, number_format($selling_amount_total, 2))
    ->setCellValue('F' . $row_count, number_format($paid_amount, 2))
    ->setCellValue('G' . $row_count, number_format($balance_amount, 2));

    $objPHPExcel->getActiveSheet()->getStyle('B' . $row_count . ':G' . $row_count)->applyFromArray($content_style_Array);
    $objPHPExcel->getActiveSheet()->getStyle('B' . $row_count . ':G' . $row_count)->applyFromArray($borderArray);

    $row_count++;
}

// Add total row
if($count > 0){
    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('D' . $row_count, 'TOTAL')
        ->setCellValue('E' . $row_count, number_format($total_selling, 2))
        ->setCellValue('F' . $row_count, number_format($total_paid, 2))
        ->setCellValue('G' . $row_count, number_format($total_balance, 2));

    $objPHPExcel->getActiveSheet()->getStyle('B' . $row_count . ':G' . $row_count)->applyFromArray($header_style_Array);
    $objPHPExcel->getActiveSheet()->getStyle('B' . $row_count . ':G' . $row_count)->applyFromArray($borderArray);
}

//////////////////////////****************Content End**************////////////////////////////////

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Sheet 1');
for ($col = 'A'; $col !== 'K'; $col++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
}

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client's web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Booking Wise Total Selling Report(' . date('d-m-Y H:i') . ').xls"');
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
