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

$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")

->setLastModifiedBy("Maarten Balliauw")

->setTitle("Office 2007 XLSX Test Document")

->setSubject("Office 2007 XLSX Test Document")

->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")

->setKeywords("office 2007 openxml php")

->setCategory("Test result file");


//////////////////////////****************Content start**************////////////////////////////////

$from_date = $_GET['from_date'];
$to_date = $_GET['to_date'];
$sale_type = $_GET['sale_type'];
$branch_status = $_GET['branch_status'];
$branch_admin_id = $_GET['branch_admin_id'];
$role = $_GET['role'];
$financial_year_id = $_SESSION['financial_year_id'];

if($from_date != '' && $to_date != ''){
  $from_date1 = get_date_user($from_date);
  $to_date1 = get_date_user($to_date);
  $date_string = $from_date1.' To '.$to_date1; 
}else{
  $date_string = '';
}

if($sale_type=='Excursion Booking')
$sale_type1 = 'Activity Booking';
else if($sale_type=='Air Ticket Booking')
$sale_type1 = 'Flight Booking';
else if($sale_type=='Train Ticket Booking')
$sale_type1 = 'Train Booking';
else
$sale_type1 = $sale_type;
// Add some data

$objPHPExcel->setActiveSheetIndex(0)

            ->setCellValue('B2', 'Report Name')

            ->setCellValue('C2', 'Sales Register')

            ->setCellValue('B3', 'Sale Type')

            ->setCellValue('C3', $sale_type1)

            ->setCellValue('B4', 'Date')

            ->setCellValue('C4', $date_string);


$objPHPExcel->getActiveSheet()->getStyle('B2:C2')->applyFromArray($header_style_Array);

$objPHPExcel->getActiveSheet()->getStyle('B2:C2')->applyFromArray($borderArray);    

$objPHPExcel->getActiveSheet()->getStyle('B3:C3')->applyFromArray($header_style_Array);

$objPHPExcel->getActiveSheet()->getStyle('B3:C3')->applyFromArray($borderArray); 

$objPHPExcel->getActiveSheet()->getStyle('B4:C4')->applyFromArray($header_style_Array);

$objPHPExcel->getActiveSheet()->getStyle('B4:C4')->applyFromArray($borderArray);     

$count = 0;
$total_amount = 0;
$query = "SELECT * FROM `ledger_master` WHERE 1 and `group_sub_id` in('20')"; 
$sq_query = mysqlQuery($query);

$finDate = mysqli_fetch_assoc(mysqlQuery("SELECT `from_date`, `to_date` FROM `financial_year` WHERE `financial_year_id` = '$financial_year_id'"));
$row_count = 6;

$objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('B'.$row_count, "Sr. No")
        ->setCellValue('C'.$row_count, "Booking Type")
        ->setCellValue('D'.$row_count, "Customer Name")
        ->setCellValue('E'.$row_count, "Booking ID")
        ->setCellValue('F'.$row_count, "Amount");

$objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':F'.$row_count)->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':F'.$row_count)->applyFromArray($borderArray);    

$row_count++;


while($row_query = mysqli_fetch_assoc($sq_query))
{
  $f_query = "select * from finance_transaction_master where gl_id='$row_query[ledger_id]' and type in('INVOICE','REFUND') and module_name!='Journal Entry'";
	if($from_date != '' && $to_date != ''){
		$from_date = get_date_db($from_date);
		$to_date = get_date_db($to_date);
		$f_query .= " and payment_date between '$from_date' and '$to_date'";
	}	else{
		$from_date = $finDate['from_date'];
		$to_date = $finDate['to_date'];
		$f_query .= " and payment_date between '$from_date' and '$to_date'";
	}
	if($sale_type != ''){
		$f_query .= " and module_name = '$sale_type'";
	}
	if($branch_status == 'yes'){
		if($role == 'Branch Admin' || $role == 'Accountant'){
			$f_query .= " and branch_admin_id='$branch_admin_id'";
		}
	}
  $sq_finance = mysqlQuery($f_query);
  while($row_finance = mysqli_fetch_assoc($sq_finance)){

		if($row_finance['payment_side'] == 'Credit'){
			$total_amount -= $row_finance['payment_amount'];
			$payment_amount = '-'.$row_finance['payment_amount'];
			$bg = 'danger';
		}else{

			$total_amount += $row_finance['payment_amount'];
			$payment_amount = $row_finance['payment_amount'];
			$bg = '';
		}
		$booking_data = get_customer_name($row_finance['module_name'],$row_finance['module_entry_id']);
		$booking_data = explode('=',$booking_data);
		$customer_name = $booking_data[0];
		$booking_id = $booking_data[1];

		if($row_finance['module_name']=='Excursion Booking')
			$module_name = 'Activity Booking';
		else if($row_finance['module_name']=='Air Ticket Booking')
			$module_name = 'Flight Booking';
		else if($row_finance['module_name']=='Train Ticket Booking')
			$module_name = 'Train Booking';
		else
			$module_name = $row_finance['module_name'];

      if($row_finance['module_name']=='Package Booking'){
				$sq_pkg=mysqli_fetch_assoc(mysqlQuery("select * from package_tour_booking_master where booking_id ='$row_finance[module_entry_id]' "));
				
	
		// Currency conversion
		// $currency_amount1 = currency_conversion($currency,$sq_pkg['currency_code'],$total_amount);
		// $roundoff_1 = abs($sq_pkg['roundoff']);

		// $payment_amount = $sq_pkg['net_total'] + $roundoff_1 ;


		
		$roundoff = $sq_pkg['roundoff'];


		if($roundoff < 0){
			// Only add roundoff if it's negative
			$payment_amount = $sq_pkg['net_total']+ abs($roundoff);
	
			
		} else {
			$payment_amount = $sq_pkg['net_total'] - abs($roundoff);
		}


		$currency_amount1 = currency_conversion($currency, $sq_pkg['currency_code'], $payment_amount);


		if($sq_pkg['currency_code'] !='0' && $currency != $sq_pkg['currency_code']){
			$currency_amount = '('.$currency_amount1.')';
		}else{
			$currency_amount = '';
		}

		
		if($payment_amount ==0){
			$currency_amount='';
		}
	
			}else if($row_finance['module_name']=='Hotel Booking'){
				$sq_pkg=mysqli_fetch_assoc(mysqlQuery("select * from hotel_booking_master where booking_id ='$row_finance[module_entry_id]' "));
				
	
				$sq_credit = mysqli_fetch_assoc(mysqlQuery("SELECT sum(credit_charges) as sumc FROM hotel_booking_payment WHERE booking_id = '$row_finance[module_entry_id]' and clearance_status != 'Pending' and clearance_status !='Cancelled'"));
	$credit_card_charges = $sq_credit['sumc'];
		// Currency conversion
		
		// $roundoff_1 = abs($sq_pkg['roundoff']);

		// $payment_amount = $sq_pkg['total_fee'] + $roundoff_1 +$credit_card_charges;


		
		$roundoff = $sq_pkg['roundoff'];


		if($roundoff < 0){
			// Only add roundoff if it's negative
			$payment_amount = $sq_pkg['total_fee']  +$credit_card_charges+ abs($roundoff);
	
			
		} else {
			$payment_amount = $sq_pkg['total_fee']  +$credit_card_charges -abs($roundoff);
		}



		$currency_amount1 = currency_conversion($currency, $sq_pkg['currency_code'], $payment_amount );


		if($sq_pkg['currency_code'] !='0' && $currency != $sq_pkg['currency_code']){
			$currency_amount = '('.$currency_amount1.')';
			
		}else{
			$currency_amount = '';
		}
	

		
		if($payment_amount ==0){
			$currency_amount='';
		}

			}
			else if($row_finance['module_name']=='Visa Booking'){
				$sq_pkg=mysqli_fetch_assoc(mysqlQuery("select * from visa_master where visa_id ='$row_finance[module_entry_id]' "));
				
	
				
		// Currency conversion
	
		$roundoff = $sq_pkg['roundoff'];


		if($roundoff < 0){
			// Only add roundoff if it's negative
			$payment_amount = $sq_pkg['visa_total_cost'] + abs($roundoff);
	
			
		} else {
			$payment_amount = $sq_pkg['visa_total_cost'] -abs($roundoff);
		}



		$currency_amount1 = currency_conversion($currency, $sq_pkg['currency_code'], $payment_amount );


		if($sq_pkg['currency_code'] !='0' && $currency != $sq_pkg['currency_code']){
			$currency_amount = '('.$currency_amount1.')';
			
		}else{
			$currency_amount = '';
		}
	

		
		if($payment_amount ==0){
			$currency_amount='';
		}

			}
			else if($row_finance['module_name']=='Excursion Booking'){
				$sq_pkg=mysqli_fetch_assoc(mysqlQuery("select * from excursion_master where exc_id ='$row_finance[module_entry_id]' "));
				
	
				
		// Currency conversion
	
		$roundoff = $sq_pkg['roundoff'];


		if($roundoff < 0){
			// Only add roundoff if it's negative
			$payment_amount = $sq_pkg['exc_total_cost'] + abs($roundoff);
	
			
		} else {
			$payment_amount = $sq_pkg['exc_total_cost'] -abs($roundoff);
		}



		$currency_amount1 = currency_conversion($currency, $sq_pkg['currency_code'], $payment_amount );


		if($sq_pkg['currency_code'] !='0' && $currency != $sq_pkg['currency_code']){
			$currency_amount = '('.$currency_amount1.')';
			
		}else{
			$currency_amount = '';
		}
	

		
		if($payment_amount ==0){
			$currency_amount='';
		}

			}

			// car rental 

			else if($row_finance['module_name']=='Car Rental Booking'){
				$sq_pkg=mysqli_fetch_assoc(mysqlQuery("select * from car_rental_booking where booking_id ='$row_finance[module_entry_id]' "));
				
	
				$sq_paid_amount = mysqli_fetch_assoc(mysqlQuery("SELECT sum(payment_amount) as sum ,sum(`credit_charges`) as sumc from car_rental_payment where booking_id='$row_finance[module_entry_id]' and clearance_status!='Pending' and clearance_status!='Cancelled'"));
$paid_amount = $sq_paid_amount['sum'];
$credit_card_charges = $sq_paid_amount['sumc'];
				
		// Currency conversion
	
		$roundoff = $sq_pkg['roundoff'];


		if($roundoff < 0){
			// Only add roundoff if it's negative
			$payment_amount = $sq_pkg['total_fees']+$credit_card_charges + abs($roundoff);
	
			
		} else {
			$payment_amount = $sq_pkg['total_fees'] -abs($roundoff);
		}



		$currency_amount1 = currency_conversion($currency, $sq_pkg['currency_code'], $payment_amount );

		
		if($sq_pkg['currency_code'] !='0' && $currency != $sq_pkg['currency_code']){
			$currency_amount = '('.$currency_amount1.')';
			
		}else{
			$currency_amount = '';
		}
	

		
		if($payment_amount ==0){
			$currency_amount='';
		}

			}else if($row_finance['module_name']=='Group Booking'){
				$sq_pkg=mysqli_fetch_assoc(mysqlQuery("select * from tourwise_traveler_details where traveler_group_id ='$row_finance[module_entry_id]'"));
				
	
				$sq_paid_amount = mysqli_fetch_assoc(mysqlQuery("SELECT sum(amount) as sum ,sum(`credit_charges`) as sumc from payment_master where tourwise_traveler_id='$row_finance[module_entry_id]' and clearance_status!='Pending' and clearance_status!='Cancelled'"));
$paid_amount = $sq_paid_amount['sum'];
$credit_card_charges = $sq_paid_amount['sumc'];
				
		// Currency conversion
	
		// $roundoff = $sq_pkg['roundoff'];


		// if($roundoff < 0){
			// Only add roundoff if it's negative
			$payment_amount = $sq_pkg['net_total']+$credit_card_charges ;
			// + abs($roundoff);
	
			
		// } else {
		// 	$payment_amount = $sq_pkg['total_fees'] -abs($roundoff);
		// }



		$currency_amount1 = currency_conversion($currency, $sq_pkg['currency_code'], $payment_amount );

		
		if($sq_pkg['currency_code'] !='0' && $currency != $sq_pkg['currency_code']){
			$currency_amount = '('.$currency_amount1.')';
			
		}else{
			$currency_amount = '';
		}
	

		
		if($payment_amount ==0){
			$currency_amount='';
		}

			}else if($row_finance['module_name']=='Air Ticket Booking'){
				$sq_pkg=mysqli_fetch_assoc(mysqlQuery("select * from ticket_master where ticket_id ='$row_finance[module_entry_id]'"));
				
	
				$sq_paid_amount = mysqli_fetch_assoc(mysqlQuery("SELECT sum(payment_amount) as sum ,sum(`credit_charges`) as sumc from ticket_payment_master where ticket_id='$row_finance[module_entry_id]' and clearance_status!='Pending' and clearance_status!='Cancelled'"));
$paid_amount = $sq_paid_amount['sum'];
$credit_card_charges = $sq_paid_amount['sumc'];
				
		// Currency conversion
	
		// $roundoff = $sq_pkg['roundoff'];


		// if($roundoff < 0){
			// Only add roundoff if it's negative
			$payment_amount = $sq_pkg['ticket_total_cost']+$credit_card_charges ;
			// + abs($roundoff);
	
			
		// } else {
		// 	$payment_amount = $sq_pkg['total_fees'] -abs($roundoff);
		// }



		$currency_amount1 = currency_conversion($currency, $sq_pkg['currency_code'], $payment_amount );

		
		if($sq_pkg['currency_code'] !='0' && $currency != $sq_pkg['currency_code']){
			$currency_amount = '('.$currency_amount1.')';
			
		}else{
			$currency_amount = '';
		}
	

		
		if($payment_amount ==0){
			$currency_amount='';
		}

			}else if($row_finance['module_name']=='Bus Booking'){
				$sq_pkg=mysqli_fetch_assoc(mysqlQuery("select * from bus_booking_master where booking_id ='$row_finance[module_entry_id]'"));
				
	
				$sq_paid_amount = mysqli_fetch_assoc(mysqlQuery("SELECT sum(payment_amount) as sum ,sum(`credit_charges`) as sumc from bus_booking_payment_master where booking_id='$row_finance[module_entry_id]' and clearance_status!='Pending' and clearance_status!='Cancelled'"));
$paid_amount = $sq_paid_amount['sum'];
$credit_card_charges = $sq_paid_amount['sumc'];
				
		
			$payment_amount = $sq_pkg['net_total']+$credit_card_charges ;
		


		$currency_amount1 = currency_conversion($currency, $sq_pkg['currency_code'], $payment_amount );

		
		if($sq_pkg['currency_code'] !='0' && $currency != $sq_pkg['currency_code']){
			$currency_amount = '('.$currency_amount1.')';
			
		}else{
			$currency_amount = '';
		}
	

		
		if($payment_amount ==0){
			$currency_amount='';
		}

			}else if($row_finance['module_name']=='Miscellaneous Booking'){
				$sq_pkg=mysqli_fetch_assoc(mysqlQuery("select * from miscellaneous_master where misc_id ='$row_finance[module_entry_id]'"));
				
	
				$sq_paid_amount = mysqli_fetch_assoc(mysqlQuery("SELECT sum(payment_amount) as sum ,sum(`credit_charges`) as sumc from miscellaneous_payment_master where misc_id='$row_finance[module_entry_id]' and clearance_status!='Pending' and clearance_status!='Cancelled'"));
$paid_amount = $sq_paid_amount['sum'];
$credit_card_charges = $sq_paid_amount['sumc'];
				
		
			$payment_amount = $sq_pkg['misc_total_cost']+$credit_card_charges ;
		


		$currency_amount1 = currency_conversion($currency, $sq_pkg['currency_code'], $payment_amount );

		
		if($sq_pkg['currency_code'] !='0' && $currency != $sq_pkg['currency_code']){
			$currency_amount = '('.$currency_amount1.')';
			
		}else{
			$currency_amount = '';
		}
	

		
		if($payment_amount ==0){
			$currency_amount='';
		}

			}
			

      else{
				$currency_amount='';

			}

    if($row_finance['payment_amount']!=0){
      
      $objPHPExcel->setActiveSheetIndex(0)

      ->setCellValue('B'.$row_count, ++$count)
      ->setCellValue('C'.$row_count, $module_name)
      ->setCellValue('D'.$row_count, $customer_name)
      ->setCellValue('E'.$row_count, $booking_id)
      ->setCellValue('F'.$row_count, number_format($payment_amount,2).$currency_amount);

      $objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':F'.$row_count)->applyFromArray($content_style_Array);
      $objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':F'.$row_count)->applyFromArray($borderArray);    
      $row_count++;
    }
}
}

  $objPHPExcel->setActiveSheetIndex(0)

    ->setCellValue('B'.$row_count, '')
    ->setCellValue('C'.$row_count, '')
    ->setCellValue('D'.$row_count, '')
    ->setCellValue('E'.$row_count, 'Total')
    ->setCellValue('F'.$row_count, number_format($total_amount,2));

  
  $objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':F'.$row_count)->applyFromArray($header_style_Array);
  $objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':F'.$row_count)->applyFromArray($borderArray);    
//////////////////////////****************Content End**************////////////////////////////////


// Rename worksheet

$objPHPExcel->getActiveSheet()->setTitle('Simple');

for($col = 'A'; $col !== 'N'; $col++) {

    $objPHPExcel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);

}





// Set active sheet index to the first sheet, so Excel opens this as the first sheet

$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel5)

header('Content-Type: application/vnd.ms-excel');

header('Content-Disposition: attachment;filename="Sales_Register('.date('d-m-Y H:i').').xls"');

header('Cache-Control: max-age=0');

// If you're serving to IE 9, then the following may be needed

header('Cache-Control: max-age=1');


// If you're serving to IE over SSL, then the following may be needed

header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past

header ('Last-Modified: '.gmdate('D, d M Y H:i').' GMT'); // always modified

header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1

header ('Pragma: public'); // HTTP/1.0



$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

$objWriter->save('php://output');

exit;

