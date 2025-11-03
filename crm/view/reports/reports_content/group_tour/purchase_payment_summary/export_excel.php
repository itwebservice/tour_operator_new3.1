<?php
include "../../../../../model/model.php";
//include_once('../../../../vendor/inc/vendor_generic_functions.php');


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



//mainQery

$tour_id = $_GET['tourName'];
$group_id = $_GET['tourDate'];
$role = $_SESSION['role'];
$branch_admin_id = $_SESSION['branch_admin_id'];
$branch_status = $_GET['branch_status'];
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

//mainQuery

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
    ->setCellValue('C2', 'Purchase Payment Summary Report')
    ->setCellValue('B3', 'Tour Name')
    ->setCellValue('C3', $tour_id_single_data['tour_name'])
   
    ->setCellValue('B4', 'Tour Date')
    ->setCellValue('C4', $group_single_from_to_date);

$objPHPExcel->getActiveSheet()->getStyle('B2:C2')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B2:C2')->applyFromArray($borderArray);

$objPHPExcel->getActiveSheet()->getStyle('B3:C3')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B3:C3')->applyFromArray($borderArray);

$objPHPExcel->getActiveSheet()->getStyle('B4:C4')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B4:C4')->applyFromArray($borderArray);

$count = 0;
$row_count = 8;

$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('B' . $row_count, "S.No")
    ->setCellValue('C' . $row_count, "Vendor Type")
    ->setCellValue('D' . $row_count, "Vendor Name")
    // ->setCellValue('E' . $row_count, "Total Rooms")
    // ->setCellValue('F' . $row_count, "Cost per Room")
    // ->setCellValue('E' . $row_count, "Purchase ID")
     ->setCellValue('E' . $row_count, "Total Amount")
    ->setCellValue('F' . $row_count, "Paid Amount")
    ->setCellValue('G' . $row_count, "Balance Amount");

$objPHPExcel->getActiveSheet()->getStyle('B' . $row_count . ':G' . $row_count)->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B' . $row_count . ':G' . $row_count)->applyFromArray($borderArray);

$row_count++;

while ($row_estimate = mysqli_fetch_assoc($sq_vendor_estimate)) {
	$count++;
	
	// Get vendor type and supplier name
	$vendor_type = $row_estimate['vendor_type'];

    //$estimate_type_val = get_estimate_type_name($row_estimate['estimate_type'], $row_estimate['estimate_type_id']);
	
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



    $objPHPExcel->setActiveSheetIndex(0)

    ->setCellValue('B' . $row_count, $count)
    ->setCellValue('C' . $row_count, $vendor_type)
    ->setCellValue('D' . $row_count, $supplier_name)
    // ->setCellValue('E' . $row_count, $total_rooms)
    // ->setCellValue('F' . $row_count, $cost_per_room)
    // ->setCellValue('E' . $row_count, $estimate_type_val)
    ->setCellValue('E' . $row_count, $total_amount)
    ->setCellValue('F' . $row_count, $advance_paid_formatted)
    ->setCellValue('G' . $row_count, $balance_amount_formatted);

    $objPHPExcel->getActiveSheet()->getStyle('B' . $row_count . ':G' . $row_count)->applyFromArray($content_style_Array);
    $objPHPExcel->getActiveSheet()->getStyle('B' . $row_count . ':G' . $row_count)->applyFromArray($borderArray);


    $row_count++;
}


//////////////////////////****************Content End**************////////////////////////////////

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Sheet 1');
for ($col = 'A'; $col !== 'N'; $col++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
}

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client's web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Purchase Payment Summary Report(' . date('d-m-Y H:i') . ').xls"');
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

