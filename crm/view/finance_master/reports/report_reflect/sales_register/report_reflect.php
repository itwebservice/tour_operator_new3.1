<?php
include "../../../../../model/model.php"; 
$sale_type = $_POST['sale_type'];
$from_date = $_POST['from_date'];
$to_date = $_POST['to_date'];
$branch_status = $_POST['branch_status'];
$branch_admin_id = $_POST['branch_admin_id'];
$role = $_POST['role'];
$financial_year_id = $_SESSION['financial_year_id'];

$array_s = array();
$temp_arr = array();
$count = 1;
$total_amount = 0;

$finDate = mysqli_fetch_assoc(mysqlQuery("SELECT `from_date`, `to_date` FROM `financial_year` WHERE `financial_year_id` = '$financial_year_id'"));

$query = "SELECT * FROM `ledger_master` WHERE 1 and `group_sub_id` in('20')"; 
$sq_query = mysqlQuery($query);
while($row_query = mysqli_fetch_assoc($sq_query)){

	$f_query = "select * from finance_transaction_master where gl_id='$row_query[ledger_id]' and type in('INVOICE','REFUND') and module_name!='Journal Entry'";
	if($from_date != '' && $to_date != ''){
		$from_date = get_date_db($from_date);
		$to_date = get_date_db($to_date);
		$f_query .= " and payment_date between '$from_date' and '$to_date'";
	}else{
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
	
		// $roundoff = $sq_pkg['roundoff'];


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
		


		// $currency_amount1 = currency_conversion($currency, $sq_pkg['currency_code'], $payment_amount );

		
		// if($sq_pkg['currency_code'] !='0' && $currency != $sq_pkg['currency_code']){
		// 	$currency_amount = '('.$currency_amount1.')';
			
		// }else{
		// 	$currency_amount = '';
		// }
	

		
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
		
			$temp_arr = array( "data" => array(
				(int)($count++),
				$module_name,
				$customer_name,
				$booking_id,
				$payment_amount.$currency_amount,
				), "bg" =>$bg);
				array_push($array_s,$temp_arr);
		}
	}
}
$footer_data = array("footer_data" => array(
	'total_footers' => 2,
	
	'foot0' => "Total",
	'col0' => 4,
	'class0' =>"text-right",

	'foot1' => number_format($total_amount,2),
	'col1' => 1,
	'class1' =>"text-right success"
));

array_push($array_s, $footer_data);
echo json_encode($array_s);
?>