<?php
$flag = true;
class cheque_clearance{
    public function status_update(){
        $register_id = $_POST['register_id'];
        $status = isset($_POST['status']) ? $_POST['status'] : '';
        $status_date = isset($_POST['status_date']) ? $_POST['status_date'] : '';
        $module_name = $_POST['module_name'];
        $module_entry_id = $_POST['module_entry_id'];
        $transaction_id = isset($_POST['transaction_id']) ? $_POST['transaction_id'] : '';
        $payment_amount = $_POST['payment_amount'];
        $particular = $_POST['particular'];
        $type = isset($_POST['type1']) ? $_POST['type1'] : '';
        
        // Payment date formatting
        $payment_date = get_date_db($status_date);
        $sq_bank_cash_book_info = mysqli_fetch_assoc(mysqlQuery("select * from bank_cash_book_master where register_id='$register_id'"));
        
        begin_t();
        $q = "update bank_cash_book_master set clearance_status='$status', payment_date='$payment_date' where register_id='$register_id'";
        $sq_bank_cash_book = mysqlQuery($q);
		
if($type=='cheque'){


	if ($status == 'Cancelled') {
		// Fetch the last two entries with payment_side = Debit and Credit
		$latest_id_query = "SELECT * FROM finance_transaction_master WHERE payment_side IN ('Debit', 'Credit') ORDER BY finance_transaction_id DESC LIMIT 2";
		$result = mysqlQuery($latest_id_query);
		$row = mysqli_fetch_all($result, MYSQLI_ASSOC);
		
		// Check if two entries were fetched
		if(count($row) == 2) {
			// Last entry - Credit, second last entry - Debit
			$latest_entry = $row[0]; // Last entry (Credit)
			$second_last_entry = $row[1]; // Second last entry (Debit)

			// Get the new finance_transaction_id
			$new_id = $latest_entry['finance_transaction_id'] + 1;



			// Get the required data for new entries
			$gl_id_credit = $latest_entry['gl_id']; // GL ID for Credit entry (latest entry)
			$gl_id_debit = $second_last_entry['gl_id'];

			// Get the required data for new entries
			// $gl_id = $latest_entry['gl_id'];
			$financial_year_id = $latest_entry['financial_year_id'];
			$branch_admin_id = $latest_entry['branch_admin_id'];
			$payment_date = $latest_entry['payment_date'];
			$row_specification = $latest_entry['row_specification'];
			$ledger_particular = $latest_entry['ledger_particular'];
			$created_at = $latest_entry['created_at'];
			$type1=$latest_entry['type'];
			$payment_particular=$latest_entry['payment_particular'];

			
			// First Insert - Credit (for new first entry)
			$q2 = "INSERT INTO finance_transaction_master 
					(finance_transaction_id, module_name, transaction_id, payment_amount, clearance_status, payment_side, module_entry_id, payment_particular, type, gl_id, financial_year_id, branch_admin_id, payment_date, row_specification, ledger_particular, created_at) 
					VALUES 
					('$new_id', '$module_name', '$transaction_id', '$payment_amount', '$status', 'Credit', '$module_entry_id', '$payment_particular', '$type1', '$gl_id_debit', '$financial_year_id', '$branch_admin_id', '$payment_date', '$row_specification', '$ledger_particular', '$created_at')";
			$sq_fin_insert1 = mysqlQuery($q2);

			// Increment the finance_transaction_id for the second entry
			$new_id++;

			// Second Insert - Debit (for new second entry)
			$q3 = "INSERT INTO finance_transaction_master 
					(finance_transaction_id, module_name, transaction_id, payment_amount, clearance_status, payment_side, module_entry_id, payment_particular, type, gl_id, financial_year_id, branch_admin_id, payment_date, row_specification, ledger_particular, created_at) 
					VALUES 
					('$new_id', '$module_name', '$transaction_id', '$payment_amount', '$status', 'Debit', '$module_entry_id', '$payment_particular', '$type1', '$gl_id_credit', '$financial_year_id', '$branch_admin_id', '$payment_date', '$row_specification', '$ledger_particular', '$created_at')";
			$sq_fin_insert2 = mysqlQuery($q3);

			if ($sq_fin_insert1 && $sq_fin_insert2) {
				// echo "Two records inserted successfully: First Credit, Second Debit.";
			} else {
				echo "Error inserting records: " . mysqli_error($conn);
			}
		} else {
			echo "Error: Could not fetch two entries with Debit and Credit payment side.";
		}
	} else {
		// Update clearance_status in finance_transaction_master table
		$q1 = "UPDATE finance_transaction_master 
				SET clearance_status='$status' 
				WHERE module_name='$module_name' 
				AND transaction_id='$transaction_id' 
				AND payment_amount='$payment_amount'";
		$sq_fin_update = mysqlQuery($q1);

		if ($sq_fin_update) {
			echo "Record updated successfully.";
		} else {
			echo "Error updating record: " . mysqli_error($conn);
		}
	}


}else{

	if ($status == 'Cancelled') {
		// Fetch the last two entries with payment_side = Debit and Credit
		$latest_id_query = "SELECT * FROM finance_transaction_master WHERE payment_side IN ('Debit', 'Credit') ORDER BY finance_transaction_id DESC LIMIT 5";
		$result = mysqlQuery($latest_id_query);
		$row = mysqli_fetch_all($result, MYSQLI_ASSOC);
		
		// Check if two entries were fetched
		if(count($row) == 5) {
			// Last entry - Credit, second last entry - Debit
			$latest_entry = $row[0]; // Last entry (Credit)
			$second_last_entry = $row[1]; // Second last entry (Debit)
			$third_last_entry=$row[2];
			$fourth_last_entry=$row[3];

			// Get the new finance_transaction_id
			$new_id = $latest_entry['finance_transaction_id'] + 1;



			// Get the required data for new entries
			$gl_id_credit = $latest_entry['gl_id']; // GL ID for Credit entry (latest entry)
			$gl_id_debit = $second_last_entry['gl_id'];
			$gl_id_debit_third=$third_last_entry['gl_id'];
			$gl_id_credit_fourth=$fourth_last_entry['gl_id'];

			// Get the required data for new entries
			// $gl_id = $latest_entry['gl_id'];
			$financial_year_id = $latest_entry['financial_year_id'];
			$branch_admin_id = $latest_entry['branch_admin_id'];
			$payment_date = $latest_entry['payment_date'];
			$row_specification = $latest_entry['row_specification'];
			$ledger_particular = $latest_entry['ledger_particular'];
			$created_at = $latest_entry['created_at'];
			$type1=$latest_entry['type'];
			$payment_particular=$latest_entry['payment_particular'];
			$payment_amount1=$latest_entry['payment_amount'];


			// Second first entry Insert - Debit (for new second entry)
			$q3 = "INSERT INTO finance_transaction_master 
					(finance_transaction_id, module_name, transaction_id, payment_amount, clearance_status, payment_side, module_entry_id, payment_particular, type, gl_id, financial_year_id, branch_admin_id, payment_date, row_specification, ledger_particular, created_at) 
					VALUES 
					('$new_id', '$module_name', '$transaction_id', '$payment_amount1', '$status', 'Debit', '$module_entry_id', '$payment_particular', '$type1', '$gl_id_credit', '$financial_year_id', '$branch_admin_id', '$payment_date', '$row_specification', '$ledger_particular', '$created_at')";
			$sq_fin_insert3 = mysqlQuery($q3);


			$new_id++;



			// Second entry



			$financial_year_id = $second_last_entry['financial_year_id'];
			$branch_admin_id = $second_last_entry['branch_admin_id'];
			$payment_date = $second_last_entry['payment_date'];
			$row_specification = $second_last_entry['row_specification'];
			$ledger_particular = $second_last_entry['ledger_particular'];
			$created_at = $second_last_entry['created_at'];
			$type1=$second_last_entry['type'];
			$payment_particular=$second_last_entry['payment_particular'];
			$payment_amount1=$second_last_entry['payment_amount'];


			$q4 = "INSERT INTO finance_transaction_master 
					(finance_transaction_id, module_name, transaction_id, payment_amount, clearance_status, payment_side, module_entry_id, payment_particular, type, gl_id, financial_year_id, branch_admin_id, payment_date, row_specification, ledger_particular, created_at) 
					VALUES 
					('$new_id', '$module_name', '$transaction_id', '$payment_amount1', '$status', 'Credit', '$module_entry_id', '$payment_particular', '$type1', '$gl_id_debit', '$financial_year_id', '$branch_admin_id', '$payment_date', '$row_specification', '$ledger_particular', '$created_at')";
			$sq_fin_insert4 = mysqlQuery($q4);

			$new_id++;


			// third entry

			$financial_year_id = $third_last_entry['financial_year_id'];
			$branch_admin_id = $third_last_entry['branch_admin_id'];
			$payment_date = $third_last_entry['payment_date'];
			$row_specification = $third_last_entry['row_specification'];
			$ledger_particular = $third_last_entry['ledger_particular'];
			$created_at = $third_last_entry['created_at'];
			$type1=$third_last_entry['type'];
			$payment_particular=$third_last_entry['payment_particular'];
			$payment_amount1=$third_last_entry['payment_amount'];


			$q5 = "INSERT INTO finance_transaction_master 
					(finance_transaction_id, module_name, transaction_id, payment_amount, clearance_status, payment_side, module_entry_id, payment_particular, type, gl_id, financial_year_id, branch_admin_id, payment_date, row_specification, ledger_particular, created_at) 
					VALUES 
					('$new_id', '$module_name', '$transaction_id', '$payment_amount1', '$status', 'Credit', '$module_entry_id', '$payment_particular', '$type1', '$gl_id_debit_third', '$financial_year_id', '$branch_admin_id', '$payment_date', '$row_specification', '$ledger_particular', '$created_at')";
			$sq_fin_insert5 = mysqlQuery($q5);

			// fourth entry


			$new_id++;


			$financial_year_id = $fourth_last_entry['financial_year_id'];
			$branch_admin_id = $fourth_last_entry['branch_admin_id'];
			$payment_date =$fourth_last_entry['payment_date'];
			$row_specification = $fourth_last_entry['row_specification'];
			$ledger_particular = $fourth_last_entry['ledger_particular'];
			$created_at = $fourth_last_entry['created_at'];
			$type1=$fourth_last_entry['type'];
			$payment_particular=$fourth_last_entry['payment_particular'];
			$payment_amount1=$fourth_last_entry['payment_amount'];




			$q6 = "INSERT INTO finance_transaction_master 
					(finance_transaction_id, module_name, transaction_id, payment_amount, clearance_status, payment_side, module_entry_id, payment_particular, type, gl_id, financial_year_id, branch_admin_id, payment_date, row_specification, ledger_particular, created_at) 
					VALUES 
					('$new_id', '$module_name', '$transaction_id', '$payment_amount1', '$status', 'Debit', '$module_entry_id', '$payment_particular', '$type1', '$gl_id_credit_fourth', '$financial_year_id', '$branch_admin_id', '$payment_date', '$row_specification', '$ledger_particular', '$created_at')";
			$sq_fin_insert6 = mysqlQuery($q6);


			if ( $sq_fin_insert3 && $sq_fin_insert4 && $sq_fin_insert5 && $sq_fin_insert6) {
				// echo "Two records inserted successfully: First Credit, Second Debit.";
			} else {
				echo "Error inserting records: " . mysqli_error($conn);
			}
		} else {
			echo "Error: Could not fetch two entries with Debit and Credit payment side.";
		}
	} else {
		// Update clearance_status in finance_transaction_master table
		$q1 = "UPDATE finance_transaction_master 
				SET clearance_status='$status' 
				WHERE module_name='$module_name' 
				AND transaction_id='$transaction_id' 
				AND payment_amount='$payment_amount'";
		$sq_fin_update = mysqlQuery($q1);

		if ($sq_fin_update) {
			echo "Record updated successfully.";
		} else {
			echo "Error updating record: " . mysqli_error($conn);
		}
	}


}
       





	// $q1 = "update finance_transaction_master set clearance_status='$status' where module_name='$module_name' and transaction_id='$transaction_id' and payment_amount='$payment_amount'";
	// $sq_fin = mysqlQuery($q1);

	if($sq_bank_cash_book){

		$module_name = $sq_bank_cash_book_info['module_name'];
		$module_entry_id = $sq_bank_cash_book_info['module_entry_id'];
		$transaction_id = $sq_bank_cash_book_info['transaction_id'];

		//B2B Deposit
		if($module_name=="B2b Deposit"){ 
			$table_name = 'b2b_registration';
			$id_name = 'register_id';
			$date_field = 'payment_date';
		}

		//B2B Booking
		if($module_name=="B2B Booking"){ 
			$table_name = 'b2b_payment_master';
			$id_name = 'payment_id';
			$date_field = 'payment_date';
		}

		//B2B Refund
		if($module_name=="B2B Booking Refund Paid"){ 
			$table_name = 'b2b_booking_refund_master';
			$id_name = 'refund_id';
			$date_field = 'refund_date';
		}

		//B2C Booking
		if($module_name=="B2C Booking"){ 
			$table_name = 'b2c_payment_master';
			$id_name = 'payment_id';
			$date_field = 'payment_date';
		}

		//B2C Refund
		if($module_name=="B2C Booking Refund Paid"){ 
			$table_name = 'b2c_booking_refund_master';
			$id_name = 'refund_id';
			$date_field = 'refund_date';
		}

		//Visa Booking
		if($module_name=="Visa Booking Payment"){ 
			$table_name = 'visa_payment_master';
			$id_name = 'payment_id';
			$date_field = 'payment_date';
		}

		if($module_name=="Visa Booking Refund Paid"){ 
			$table_name = 'visa_refund_master';
			$id_name = 'refund_id';
			$date_field = 'refund_date';
		}
		//miscelleneous Booking
		if($module_name=="Miscellaneous Booking Payment"){ 
			$table_name = 'miscellaneous_payment_master';
			$id_name = 'payment_id';
			$date_field = 'payment_date';
		}

		if($module_name=="Miscellaneous Booking Refund Paid"){ 
			$table_name = 'miscellaneous_refund_master';
			$id_name = 'refund_id';
			$date_field = 'refund_date';
		}

		//Air Ticket Booking
		if($module_name=="Air Ticket Booking Payment"){ 
			$table_name = 'ticket_payment_master';
			$id_name = 'payment_id';
			$date_field = 'payment_date';

		}
		if($module_name=="Air Ticket Booking Refund Paid"){ 
			$table_name = 'ticket_refund_master';
			$id_name = 'refund_id';
			$date_field = 'refund_date';
		}
		//Train Ticket Booking
		if($module_name=="Train Ticket Booking Payment"){ 
			$table_name = 'train_ticket_payment_master';
			$id_name = 'payment_id';
			$date_field = 'payment_date';
		}

		if($module_name=="Train Ticket Booking Refund Paid"){ 
			$table_name = 'train_ticket_refund_master';
			$id_name = 'refund_id';
			$date_field = 'refund_date';
		}

		//Hotel Booking
		if($module_name=="Hotel Booking Payment"){ 
			$table_name = 'hotel_booking_payment';
			$id_name = 'payment_id';
			$date_field = 'payment_date';
		}
		if($module_name=="Hotel Booking Refund Paid"){ 
			$table_name = 'hotel_booking_refund_master';
			$id_name = 'refund_id';
			$date_field = 'refund_date';
		}
		//Car Rental Booking
		if($module_name=="Car Rental Booking Payment"){ 
			$table_name = 'car_rental_payment';
			$id_name = 'payment_id';
			$date_field = 'payment_date';
		}
		if($module_name=="Car Rental Booking Refund Paid"){ 
			$table_name = 'car_rental_refund_master';
			$id_name = 'refund_id';
			$date_field = 'refund_date';
		}

		//Group Booking
		if($module_name=="Group Booking Payment"){ 
			$table_name = 'payment_master';
			$id_name = 'payment_id';
			$date_field = 'date';
		}

		if($module_name=="Group Booking Refund Paid"){ 
			$table_name = 'refund_tour_cancelation';
			$id_name = 'refund_id';
			$date_field = 'refund_date';
		}

		if($module_name=="Group Booking Traveller Refund Paid"){ 
			$table_name = 'refund_traveler_cancelation';
			$id_name = 'refund_id';
			$date_field = 'refund_date';
		}

		//Package Booking
		if($module_name=="Package Booking Payment"){ 
			$table_name = 'package_payment_master';
			$id_name = 'payment_id';
			$date_field = 'date';
		}	

		if($module_name=="Package Booking Traveller Refund Paid"){ 
			$table_name = 'package_refund_traveler_cancelation';
			$id_name = 'refund_id';
			$date_field = 'refund_date';
		}

		//Employee Salary
		if($module_name=="Employee Salary"){ 
			$table_name = 'employee_salary_master';
			$id_name = 'salary_id';
			$date_field = 'payment_date';
		}

		//Office Expense
		if($module_name=="Other Expense Booking Payment" || $module_name=="Other Expense Booking"){ 
			$table_name = 'other_expense_payment_master';
			$id_name = 'payment_id';
			$date_field = 'payment_date';
		}

		//Other Income
		if($module_name=="Other Income Payment"){ 
			$table_name = 'other_income_payment_master';
			$id_name = 'payment_id';
			$date_field = 'payment_date';
		}


		if($module_name=="Booker Incentive Payment"){ 
			$table_name = 'booker_incentive_payment_master';
			$id_name = 'payment_id';
			$date_field = 'payment_date';
		}
		if($module_name=="Customer Advance"){ 
			$table_name = 'corporate_advance_master';
			$id_name = 'advance_id';
			$date_field = 'payment_date';
		}
		if($module_name=="Airline Supplier Payment"){ 
			$table_name = 'flight_supplier_payment';
			$id_name = 'id';
			$date_field = 'payment_date';
		}
		if($module_name=="Visa Supplier Payment"){ 
			$table_name = 'visa_supplier_payment';
			$id_name = 'id';
			$date_field = 'payment_date';
		}
		//Vendor Payment
		if($module_name=="Vendor Payment"){ 
			$table_name = 'vendor_payment_master';
			$id_name = 'payment_id';
			$date_field = 'payment_date';
		}
		if($module_name=="Hotel Vendor"){ 
			$table_name = 'vendor_payment_master';
			$id_name = 'payment_id';
			$date_field = 'payment_date';
		}
		if($module_name=="Transport Vendor"){ 

			$table_name = 'vendor_payment_master';

			$id_name = 'payment_id';

			$date_field = 'payment_date';

		}
		if($module_name=="DMC Vendor"){ 

			$table_name = 'vendor_payment_master';

			$id_name = 'payment_id';

			$date_field = 'payment_date';

		}
		if($module_name=="Car Rental Vendor"){ 

			$table_name = 'vendor_payment_master';

			$id_name = 'payment_id';

			$date_field = 'payment_date';

		}
		if($module_name=="Visa Vendor"){ 

			$table_name = 'vendor_payment_master';

			$id_name = 'payment_id';

			$date_field = 'payment_date';

		}
		if($module_name=="Ticket Vendor"){ 

			$table_name = 'vendor_payment_master';

			$id_name = 'payment_id';

			$date_field = 'payment_date';

		}
		if($module_name=="Excursion Vendor"){ 

			$table_name = 'vendor_payment_master';

			$id_name = 'payment_id';

			$date_field = 'payment_date';

		}
		if($module_name=="DMC Vendor"){ 

			$table_name = 'vendor_payment_master';

			$id_name = 'payment_id';

			$date_field = 'payment_date';

		}
		if($module_name=="Cruise Vendor"){ 

			$table_name = 'vendor_payment_master';

			$id_name = 'payment_id';

			$date_field = 'payment_date';

		}
		if($module_name=="Train Ticket Vendor"){ 

			$table_name = 'vendor_payment_master';

			$id_name = 'payment_id';

			$date_field = 'payment_date';

		}
		if($module_name=="Passport Vendor"){ 

			$table_name = 'vendor_payment_master';

			$id_name = 'payment_id';

			$date_field = 'payment_date';

		}
		if($module_name=="Insurance Vendor"){ 

			$table_name = 'vendor_payment_master';

			$id_name = 'payment_id';

			$date_field = 'payment_date';

		}
		if($module_name=="Other Vendor"){ 

			$table_name = 'vendor_payment_master';

			$id_name = 'payment_id';

			$date_field = 'payment_date';

		}

		if($module_name=="Vendor Advance Payment"){ 

			$table_name = 'vendor_advance_master';

			$id_name = 'payment_id';

			$date_field = 'payment_date';

		}

		if($module_name=="Vendor Refund Paid"){ 

			$table_name = 'vendor_refund_master';

			$id_name = 'refund_id';

			$date_field = 'payment_date';

		}

		//TDS
		if($module_name=="TDS Payment"){ 

			$table_name = 'tds_entry_master';

			$id_name = 'payment_id';

			$date_field = 'payment_date';

		}

		//Miscellaneous
		if($module_name=="Miscellaneous Booking Payment"){ 
			$table_name = 'miscellaneous_payment_master';
			$id_name = 'payment_id';
			$date_field = 'payment_date';
		}

		if($module_name=="Miscellaneous Booking Refund"){ 
			$table_name = 'miscellaneous_refund_master';
			$id_name = 'refund_id';
			$date_field = 'refund_date';
		}



		//Bus
		if($module_name=="Bus Booking Payment"){ 
			$table_name = 'bus_booking_payment_master';
			$id_name = 'payment_id';
			$date_field = 'payment_date';
		}

		if($module_name=="Bus Booking Refund Paid"){ 
			$table_name = 'bus_booking_refund_master';
			$id_name = 'refund_id';
			$date_field = 'refund_date';
		}

		//GST Paid

		if($module_name=="GST Monthly Payment"){ 
			$table_name = 'gst_payable_master';
			$id_name = 'id';
			$date_field = 'payment_date';
		}

		//Excursion Booking
		if($module_name=="Excursion Booking Payment"){ 
			$table_name = 'exc_payment_master';
			$id_name = 'payment_id';
			$date_field = 'payment_date';
		}

		if($module_name=="Excursion Booking Refund Paid"){ 
			$table_name = 'exc_refund_master';
			$id_name = 'refund_id';
			$date_field = 'refund_date';
		}
		
		if($module_name=="B2B Booking"){
			$sq_payment = mysqli_fetch_assoc(mysqlQuery("select * from b2b_payment_master where entry_id='$sq_bank_cash_book_info[module_entry_id]'"));
			$payment_id = $sq_payment['payment_id'];
			$q = "update $table_name set clearance_status='$status', $date_field='$payment_date' where $id_name='$payment_id'";
			$id_name = 'entry_id';
		}
		else if($module_name=="B2C Booking"){
			$sq_payment = mysqli_fetch_assoc(mysqlQuery("select * from b2c_payment_master where entry_id='$sq_bank_cash_book_info[module_entry_id]'"));
			$payment_id = $sq_payment['payment_id'];
			$q = "update $table_name set clearance_status='$status', $date_field='$payment_date' where $id_name='$payment_id'";
			$id_name = 'entry_id';
		}
		else{
			$q = "update $table_name set clearance_status='$status', $date_field='$payment_date' where $id_name='$sq_bank_cash_book_info[module_entry_id]'";
		}
		$sq_payment = mysqlQuery($q);

		if(!$sq_payment){
			$GLOBALS['flag'] = false;
		}

		if($GLOBALS['flag']){

			if($type === 'card' && $status == 'Cleared'){
				if($module_name != "Customer Advance"){
					$this->finance_Save($module_name,$module_entry_id,$payment_amount,$payment_date,$table_name,$id_name,$particular);
				}
			}
			$type1 = ($type  === 'card') ? 'Credit Card' : 'Cheque';
			echo $type1." payment has been successfully ".$status;
			commit_t();
			exit;
		}

	}
	else{
		rollback_t();
		echo "error--Sorry,Cheque status not updated!";
		exit;
	}
}

function finance_Save($module_name,$module_entry_id,$payment_amount,$payment_date,$table_name,$id_name,$particular){

	global $transaction_master;
	$branch_admin_id = $_SESSION['branch_admin_id'];
	$sq_credit = mysqli_fetch_assoc(mysqlQuery("select credit_charges,credit_card_details from $table_name where $id_name='$module_entry_id'"));
	$credit_card_details = $sq_credit['credit_card_details'];
	$credit_card_details = explode('-',$credit_card_details);
	$entry_id = $credit_card_details[0];
	//Credit card company ledger
	$sq_cust1 = mysqli_fetch_assoc(mysqlQuery("select * from ledger_master where customer_id='$entry_id' and user_type='credit company'"));
	$company_gl = $sq_cust1['ledger_id'];

	$sq_credit_charges = mysqli_fetch_assoc(mysqlQuery("select * from credit_card_company where entry_id ='$entry_id'"));
	//////company's credit card charges
	$company_card_charges = ($sq_credit_charges['charges_in'] =='Flat') ? $sq_credit_charges['credit_card_charges'] : ($payment_amount * ($sq_credit_charges['credit_card_charges']/100));
	//////company's tax on credit card charges
	$tax_charges = ($sq_credit_charges['tax_charges_in'] =='Flat') ? $sq_credit_charges['tax_on_credit_card_charges'] : ($company_card_charges * ($sq_credit_charges['tax_on_credit_card_charges']/100));
	$finance_charges = $company_card_charges + $tax_charges;
	$finance_charges = number_format($finance_charges,2);
	$credit_company_amount = $payment_amount - $finance_charges;

	$sq_bank = mysqli_fetch_assoc(mysqlQuery("select * from ledger_master where customer_id='$sq_credit_charges[bank_id]' and user_type='bank'"));
	$pay_gl = $sq_bank['ledger_id'];

	//Bank
	$module_name = $module_name;
	$module_entry_id = $module_entry_id;
	$transaction_id = '';
	$payment_amount = $credit_company_amount;
	$payment_date = $payment_date;
	$payment_particular = $particular;
	$ledger_particular = get_ledger_particular('By','Cash/Bank');
	$gl_id = $pay_gl;
	$payment_side = "Debit";
	$clearance_status = "Cleared";
	$transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '',$payment_side, $clearance_status, 'sales',$branch_admin_id,$ledger_particular,'BANK RECEIPT');

	//Credit company
	$module_name = $module_name;
	$module_entry_id = $module_entry_id;
	$transaction_id = '';
	$payment_amount = $credit_company_amount;
	$payment_date = $payment_date;
	$payment_particular = $particular;
	$ledger_particular = get_ledger_particular('By','Cash/Bank');
	$gl_id = $company_gl;
	$payment_side = "Credit";
	$clearance_status = "Cleared";
	$transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '',$payment_side, $clearance_status, 'sales',$branch_admin_id,$ledger_particular,'BANK RECEIPT');

}

}

?>