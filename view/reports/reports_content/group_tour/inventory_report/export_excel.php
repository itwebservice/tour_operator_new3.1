<?php
include "../../../../../model/model.php";
include_once('../../../../../classes/tour_booked_seats.php');

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
$objPHPExcel->getProperties()->setCreator("iTours")
    ->setLastModifiedBy("iTours")
    ->setTitle("Inventory Report")
    ->setSubject("Inventory Report")
    ->setDescription("Group Tour Inventory Report showing tour capacity, booked seats, and available seats.")
    ->setKeywords("inventory report group tour")
    ->setCategory("Report");

//////////////////////////****************Content start**************////////////////////////////////

//mainQuery
$tour_id = $_GET['tourName'];
$group_id = $_GET['tourDate'];
$status = $_GET['status'];

$query = "select tg.*, tm.tour_name, tm.dest_id from tour_groups tg
          INNER JOIN tour_master tm ON tg.tour_id = tm.tour_id 
          WHERE 1";

if($tour_id!="")
{
	$query .= " and tg.tour_id = '$tour_id'";
}
if($group_id!="")
{
	$query .= " and tg.group_id = '$group_id'";
}
if($status!="")
{
	$query .= " and tg.status = '$status'";
}

$query .= " ORDER BY tg.from_date ASC";

$sq_groups = mysqlQuery($query);

//mainQuery

// Fetch tour name and tour date if provided
$tour_id_single_data = null;
if (!empty($tour_id)) {
    $tour_id_single_data = mysqli_fetch_assoc(mysqlQuery("select tour_id,tour_name from tour_master where tour_id='$tour_id'"));
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
    ->setCellValue('C2', 'Inventory Report')
    ->setCellValue('B3', 'Tour Name')
    ->setCellValue('C3', ($tour_id_single_data != null ? $tour_id_single_data['tour_name'] : 'All Tours'))
    ->setCellValue('B4', 'Tour Date')
    ->setCellValue('C4', ($group_single_from_to_date != null ? $group_single_from_to_date : 'All Dates'))
    ->setCellValue('B5', 'Status')
    ->setCellValue('C5', (!empty($status) ? $status : 'All Status'));

$objPHPExcel->getActiveSheet()->getStyle('B2:C2')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B2:C2')->applyFromArray($borderArray);

$objPHPExcel->getActiveSheet()->getStyle('B3:C3')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B3:C3')->applyFromArray($borderArray);

$objPHPExcel->getActiveSheet()->getStyle('B4:C4')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B4:C4')->applyFromArray($borderArray);

$objPHPExcel->getActiveSheet()->getStyle('B5:C5')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B5:C5')->applyFromArray($borderArray);

$count = 0;
$total_capacity = 0;
$total_booked = 0;
$total_available = 0;
$row_count = 8;

$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('B' . $row_count, "Sr.No")
    ->setCellValue('C' . $row_count, "Destination")
    ->setCellValue('D' . $row_count, "From Date")
    ->setCellValue('E' . $row_count, "To Date")
    ->setCellValue('F' . $row_count, "Total Capacity")
    ->setCellValue('G' . $row_count, "Booked Seats")
    ->setCellValue('H' . $row_count, "Available Seats");

$objPHPExcel->getActiveSheet()->getStyle('B' . $row_count . ':H' . $row_count)->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B' . $row_count . ':H' . $row_count)->applyFromArray($borderArray);
// cellColor('B' . $row_count . ':H' . $row_count, 'E0E0E0');

$row_count++;

while($row_groups = mysqli_fetch_assoc($sq_groups))
{
    $count++;
    
    $tour_id_loop = $row_groups['tour_id'];
    $tour_group_id = $row_groups['group_id'];
    $tour_name = $row_groups['tour_name'];
    
    // Get destination name
    $destination = $tour_name; // Default to tour name
    if($row_groups['dest_id'] != '' && $row_groups['dest_id'] != '0'){
        $sq_dest = mysqli_fetch_assoc(mysqlQuery("select dest_name from destination_master where dest_id='".$row_groups['dest_id']."'"));
        if($sq_dest){
            $destination = $sq_dest['dest_name'];
        }
    }
    
    $from_date_display = date("d-m-Y", strtotime($row_groups['from_date']));
    $to_date_display = date("d-m-Y", strtotime($row_groups['to_date']));
    $capacity = $row_groups['capacity'];
    
    // Calculate booked seats using the existing class
    $booked_seats = $bk_seats->booked_seats($tour_id_loop, $tour_group_id);
    
    // Calculate available seats
    $available_seats = $capacity - $booked_seats;
    
    // Add to totals
    $total_capacity += $capacity;
    $total_booked += $booked_seats;
    $total_available += $available_seats;

    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('B' . $row_count, $count)
        ->setCellValue('C' . $row_count, $destination)
        ->setCellValue('D' . $row_count, $from_date_display)
        ->setCellValue('E' . $row_count, $to_date_display)
        ->setCellValue('F' . $row_count, $capacity)
        ->setCellValue('G' . $row_count, $booked_seats)
        ->setCellValue('H' . $row_count, $available_seats);

    $objPHPExcel->getActiveSheet()->getStyle('B' . $row_count . ':H' . $row_count)->applyFromArray($content_style_Array);
    $objPHPExcel->getActiveSheet()->getStyle('B' . $row_count . ':H' . $row_count)->applyFromArray($borderArray);

    // Color coding based on availability
    // if($available_seats <= 0){
    //     cellColor('B' . $row_count . ':H' . $row_count, 'FFCCCC'); // Light Red - Fully booked
    // }
    // else if($available_seats <= 5){
    //     cellColor('B' . $row_count . ':H' . $row_count, 'FFE5CC'); // Light Orange - Almost full
    // }

    $row_count++;
}

// Add total row
if($count > 0){
    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('E' . $row_count, 'TOTAL')
        ->setCellValue('F' . $row_count, $total_capacity)
        ->setCellValue('G' . $row_count, $total_booked)
        ->setCellValue('H' . $row_count, $total_available);

    $objPHPExcel->getActiveSheet()->getStyle('B' . $row_count . ':H' . $row_count)->applyFromArray($header_style_Array);
    $objPHPExcel->getActiveSheet()->getStyle('B' . $row_count . ':H' . $row_count)->applyFromArray($borderArray);
    // cellColor('B' . $row_count . ':H' . $row_count, 'D3D3D3');
}

//////////////////////////****************Content End**************////////////////////////////////

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Inventory Report');
for ($col = 'A'; $col !== 'K'; $col++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
}

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client's web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Inventory Report(' . date('d-m-Y H:i') . ').xls"');
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

