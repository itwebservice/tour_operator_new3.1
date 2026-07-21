<?php
include '../../../config.php';
header('Content-Type: application/json');
session_start();
// Get the input data
$input = json_decode(file_get_contents("php://input"), true);

$paymentId = $input['paymentId'];
$bookingId = $input['bookingId'];
$orderId = $input['orderId'];
$amount = $input['amount'];
$bookingData = [
    "bookingId" => $bookingId,
    "paymentInfos" => [
        [
            "amount" => $amount
        ]
    ]
];
//print_r($bookingData);

$confirmbook = callAPI('POST', 'https://apitest.tripjack.com/oms/v1/air/confirm-book', json_encode($bookingData));
$confirmbookresult=json_decode($confirmbook,true);
if (isset($confirmbookresult['status']['success']) && 
$confirmbookresult['status']['httpStatus'] == 200 && 
$confirmbookresult['status']['success'] == true)
{
$flight_review_data=$_SESSION['flight_review_data'];
$search_data=$_SESSION['search_data'];

$query = "SELECT `financial_year_id` FROM `financial_year` WHERE CURDATE() BETWEEN `from_date` AND `to_date`";
$result = mysqli_query($connection, $query);
$financial_year_id=0;
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $financial_year_id = $row['financial_year_id'];
    // Use $financial_year_id as needed
}
            $bookingData=["bookingId" => $bookingId,"requirePaxPricing" => true];
            $bookingdetails = callAPI('POST', 'https://apitest.tripjack.com/oms/v1/booking-details', json_encode($bookingData));
            
            $bookingDetailsResult = json_decode($bookingdetails, true);
    
                // Check order status
                $orderStatus = $bookingDetailsResult['order']['status'] ?? 'UNKNOWN';
                switch ($orderStatus) {
                    case 'SUCCESS':
                        $message = 'Order has been successfully confirmed.';
                        $ticket_status='Confirmed';
                        break;
        
                    case 'ON_HOLD':
                        $message = 'Order is currently on hold. Further action might be required.';
                        $ticket_status='ON_HOLD';
                        break;
        
                    case 'CANCELLED':
                        $message = 'Order has been cancelled.';
                        $ticket_status='CANCELLED';
                        break;
        
                    case 'FAILED':
                        $message = 'Order has failed due to unexpected reasons.';
                        $ticket_status='FAILED';
                        break;
        
                    case 'PENDING':
                        $message = 'Order is in a pending state. Please check again later.';
                        $ticket_status='PENDING';
                        break;
        
                    case 'ABORTED':
                        $message = 'Order has been aborted.';
                        $ticket_status='ABORTED';
                        break;
        
                    default:
                        $message = 'Order status is unknown.';
                        $ticket_status='unknown';
                }
                $customer_id=$_SESSION['customer_id'];
                $customersql = "SELECT * FROM customer_master WHERE customer_id = $customer_id";
                $customerresult = $conn->query($customersql);
                if ($customerresult && $customerresult->num_rows > 0) {
                  // Fetch user data from the database
                    $customer = $customerresult->fetch_assoc();
                }
               // Initialize totals for traveler categories
                $travelerSummary = [
                    "ADULT" => ["count" => 0, "baseFare" => 0, "tax" => 0, "totalFare" => 0],
                    "CHILD" => ["count" => 0, "baseFare" => 0, "tax" => 0, "totalFare" => 0],
                    "INFANT" => ["count" => 0, "baseFare" => 0, "tax" => 0, "totalFare" => 0]
                ];
                
                // Loop through tripInfos to extract traveler information
                if(isset($bookingDetailsResult['itemInfos']['AIR']['tripInfos']))
                {
                foreach ($bookingDetailsResult['itemInfos']['AIR']['tripInfos'] as $tripInfoindex => $tripInfo) {
                    foreach ($tripInfo['sI'] as $segmentInfoindex => $segment) {
                        if (isset($segment['bI']['tI'])) {
                            foreach ($segment['bI']['tI'] as $traveler) {
                                $type = $traveler['pt']; // Traveler type: ADULT, CHILD, INFANT
                                if(isset($traveler['fd']['fC']['BF']))
                                {
                                $baseFare = $traveler['fd']['fC']['BF'];
                                }
                                if(isset($traveler['fd']['fC']['TAF']))
                                {
                                $tax = $traveler['fd']['fC']['TAF'];
                                }
                                $totalFare = $traveler['fd']['fC']['TF'];
                                
                                // Update traveler summary
                                if($tripInfoindex==0 && $segmentInfoindex==0)
                                {
                                    $travelerSummary[$type]['count']++;
                                }
                                
                                $travelerSummary[$type]['baseFare'] += $baseFare;
                                $travelerSummary[$type]['tax'] += $tax;
                                $travelerSummary[$type]['totalFare'] += $totalFare;
                            }
                        }
                    }
                }
                }
                $adults = [];
                $childrens = [];
                $infant = [];
                
                // Display traveler summary and store in arrays
                foreach ($travelerSummary as $type => $details) {
                    // Prepare the formatted data
                    $formattedDetails = [
                        "Count" => $details['count'],
                        "Base Fare" => number_format($details['baseFare'], 2),
                        "Tax" => number_format($details['tax'], 2),
                        "Total Fare" => number_format($details['totalFare'], 2),
                    ];
                    
                    // Store in respective arrays based on the type
                    if ($type === "ADULT") {
                        $adults = $formattedDetails;
                    } elseif ($type === "CHILD") {
                        $childrens = $formattedDetails;
                    } elseif ($type === "INFANT") {
                        $infant = $formattedDetails;
                    }
                }
                // Access totalFareDetail->fC
                if(isset($bookingDetailsResult['itemInfos']['AIR']['totalPriceInfo']['totalFareDetail']['fC']))
                {
                    $fC = $bookingDetailsResult['itemInfos']['AIR']['totalPriceInfo']['totalFareDetail']['fC'];
                    // Assign each fC field to variables
                    $netFare = $fC['NF']; // Net Fare
                    // $igst = $fC['IGST'];  // Integrated GST
                    $baseFare = $fC['BF']; // Base Fare
                    $totalFare = $fC['TF']; // Total Fare
                    $totalTaxesAndFees = $fC['TAF']; // Total Additional Fees
                }   
                // Variables
                $bookingDate=date('Y-m-d');
                // Define reflections and bsm_values
                $reflections = json_encode([
                    [
                        "flight_sc" => "",
                        "flight_markup" => "",
                        "flight_taxes" => "21, 119",
                        "flight_markup_taxes" => "21, 119",
                        "flight_tds" => "",
                        "tax_apply_on" => "2",
                        "tax_value" => "CGST:(9.00%):(21)+SGST:(9.00%):(119)",
                        "markup_tax_value" => "CGST:(9.00%):(21)+SGST:(9.00%):(119)"
                    ]
                ]);
                
                $bsm_values = json_encode([
                    [
                        "basic" => "",
                        "service" => "",
                        "markup" => "",
                        "discount" => ""
                    ]
                ]);
            // Assuming $adults, $childrens, and $infant might be arrays or integers
        // Handling adults count and fare
        $adults_count = is_array($adults) ? $adults['Count'] : $adults;
        $adults_fare = is_array($adults) ? str_replace(',', '', $adults['Total Fare']) : str_replace(',', '', $adults);
        
        // Handling childrens count and fare
        $childrens_count = is_array($childrens) ? $childrens['Count'] : $childrens;
        $childrens_fare = is_array($childrens) ? str_replace(',', '', $childrens['Total Fare']) : str_replace(',', '', $childrens);
        
        // Handling infant count and fare
        $infant_count = is_array($infant) ? $infant['Count'] : $infant;
        $infant_fare = is_array($infant) ? str_replace(',', '', $infant['Total Fare']) : str_replace(',', '', $infant);
        $ticket_reissue = 'NULL';
        $customer_id = $customer_id;
        $branch_admin_id = 1;
        $financial_year_id = $financial_year_id;
        $tour_type = 'Domestic';
        $due_date = $bookingDate;
        $adults = $adults_count;
        $childrens = $childrens_count;
        $infant = $infant_count;
        $adult_fair = $adults_fare;
        $children_fair = $childrens_fare;
        $infant_fair = $infant_fare;
        $basic_cost = $baseFare; // Assuming $baseFare is already defined
        $markup_show = 'NULL';
        $markup = 'NULL';
        $basic_cost_discount = 'NULL';
        $yq_tax = 'NULL';
        $other_taxes = 'NULL';
        $service_show = 'NULL';
        $service_charge = 10;
        $service_tax_subtotal = $totalTaxesAndFees; // Assuming $totalTaxesAndFees is defined
        $service_tax_markup = 'NULL';
        $tds = 'NULL';
        $ticket_total_cost = $totalFare; // Assuming $totalFare is already defined
        $cancel_amount = 'NULL';
        $total_refund_amount = 'NULL';
        $created_at = $bookingDate;
        $emp_id = 1;
        $reflections = $reflections; // Assuming $reflections is defined
        $bsm_values = $bsm_values; // Assuming $bsm_values is defined
        $roundoff = 0;
        $canc_policy = 'NULL';
        $guest_name = "Guest Name";
        $cancel_flag = 'NULL';
        $delete_status = 0;
        $cancel_type = 'NULL';
        $cancel_estimate = 'NULL';
        $tax_value = 0;
        $tax_amount = 'NULL';
        $cancel_amount_exc = 'NULL';
        
        
        // Insert Query
        $ticket_master_sql = "INSERT INTO ticket_master (ticket_reissue,customer_id,branch_admin_id, financial_year_id,tour_type, due_date, adults, childrens, infant, adult_fair, children_fair,infant_fair, basic_cost, markup_show, markup, basic_cost_discount, yq_tax,other_taxes, service_show,service_charge,service_tax_subtotal,service_tax_markup, tds, ticket_total_cost,cancel_amount,total_refund_amount, created_at, emp_id, reflections,bsm_values,roundoff, canc_policy, guest_name, cancel_flag, delete_status,
            cancel_type, cancel_estimate, tax_value, tax_amount, cancel_amount_exc) VALUES ($ticket_reissue,$customer_id,$branch_admin_id,$financial_year_id,'$tour_type','$due_date',$adults,$childrens,$infant,$adult_fair,$children_fair,$infant_fair,$basic_cost,$markup_show,$markup,$basic_cost_discount,$yq_tax,$other_taxes,$service_show,$service_charge,$service_tax_subtotal,$service_tax_markup,$tds,$ticket_total_cost,$cancel_amount,$total_refund_amount,'$created_at',$emp_id,'$reflections','$bsm_values',$roundoff,$canc_policy,'$guest_name',$cancel_flag,$delete_status,$cancel_type,$cancel_estimate,$tax_value,$tax_amount,$cancel_amount_exc)";

 if (mysqli_query($connection, $ticket_master_sql)) 
 {
     $ticket_id = mysqli_insert_id($conn);
     $flightIndex=0;
     foreach ($bookingDetailsResult['itemInfos']['AIR']['tripInfos'] as $tripInfoindex => $tripInfo) 
     {
        foreach ($tripInfo['sI'] as $flight) 
        {
            
                 $airline_id=$flight['fD']['aI']['code'];
                 $departure_datetime=str_replace('T', ' ', $flight['dt']);
                 $arrival_datetime=str_replace('T', ' ', $flight['at']);
                 $airlines_name=$flight['fD']['aI']['name'];
                 $class=isset($flight['bI']['tI'][0]['fd']['cc']) ? $flight['bI']['tI'][0]['fd']['cc'] : 'NULL';
                 $flight_class=isset($flight['bI']['tI'][0]['fd']['cc']) ? $flight['bI']['tI'][0]['fd']['cc'] : 'NULL';
                 $flight_no=$flight['fD']['fN'];
                 $airline_pnr="";
                 $from_city=$flight['da']['city'];
                 $to_city=$flight['aa']['city'];
                 $departure_city=$flight['da']['city'];
                 $arrival_city=$flight['aa']['city'];
                 $luggage='NULL';
                 $special_note='NULL';
                 $arrival_terminal = isset($flight['da']['terminal']) ? $flight['da']['terminal'] : 'NULL';
                 $departure_terminal = isset($flight['aa']['terminal']) ? $flight['aa']['terminal'] : 'NULL';
                 $sub_category='NULL';
                 $no_of_pieces='NULL';
                 $aircraft_type='NULL';
                 $operating_carrier='NULL';
                 $frequent_flyer='NULL';
                 
                 $basic_fare=$baseFare;
                 $flight_duration=$flight['duration'];
                 $layover_time=isset($flight['cT']) ? $flight['cT'] : 'NULL';
                 $refund_type=isset($flight['bI']['tI'][0]['fd']['rT']) ? $flight['bI']['tI'][0]['fd']['rT'] : 0;
                 if ($refund_type === 0) {
                    $refund_status = 'Non Refundable';
                } elseif ($refund_type === 1) {
                    $refund_status = 'Refundable';
                } elseif ($refund_type === 2) {
                    $refund_status = 'Partial Refundable';
                } else {
                    $refund_status = 'Unknown Refund Type'; // Handle unexpected values
                }
                $meal_plan = isset($flight['bI']['tI'][0]['fd']['mI']) ? $flight['bI']['tI'][0]['fd']['mI'] : false;
                if ($meal_plan) {
                    $meal_status = 'Free Meal';
                } else {
                    $meal_status = 'Paid Meal';
                }
                 $status='Active';
                 $travellerInfosIndex=$flight['da']['code'].'-'.$flight['aa']['code'];
                 $ticket_trip_entries_sql = "INSERT INTO ticket_trip_entries 
                        (ticket_id, airline_id, departure_datetime, arrival_datetime, airlines_name, class, flight_class, flight_no, 
                        airline_pnr, from_city, to_city, departure_city, arrival_city, meal_plan, luggage, special_note, arrival_terminal, 
                        departure_terminal, sub_category, no_of_pieces, aircraft_type, operating_carrier, frequent_flyer, ticket_status, 
                        basic_fare, flight_duration, layover_time, refund_type, status)
                        VALUES('$ticket_id', '$airline_id', '$departure_datetime', '$arrival_datetime', '$airlines_name', '$class', '$flight_class', 
                        '$flight_no', '$airline_pnr', '$from_city', '$to_city', '$departure_city', '$arrival_city', '$meal_status', '$luggage', 
                        '$special_note', '$arrival_terminal', '$departure_terminal', '$sub_category', '$no_of_pieces', '$aircraft_type', 
                        '$operating_carrier', '$frequent_flyer', '$ticket_status', '$basic_fare', '$flight_duration', 
                        '$layover_time', '$refund_status', '$status')";
                        
                if (mysqli_query($connection, $ticket_trip_entries_sql)) 
                {
                    $entry_id = mysqli_insert_id($conn);
                    $passenger_id = $entry_id;
                    // Update the passenger_id with the newly inserted entry_id
                    $update_passenger_sql = "UPDATE ticket_trip_entries 
                                             SET passenger_id = '$entry_id' 
                                             WHERE entry_id = $entry_id";
                
                    if (mysqli_query($connection, $update_passenger_sql)) 
                    {
                                $travellerInfos=$bookingDetailsResult['itemInfos']['AIR']['travellerInfos'][0];
                                if (isset($flight['bI']['tI'])) {
                                    foreach ($flight['bI']['tI'] as $traveler) {
                                        $ticket_id = $ticket_id;
                                        $ti = $traveler['ti'];
                                        $first_name = $traveler['fN'];
                                        $middle_name = '';
                                        $last_name = $traveler['lN'];
                                        $birth_date = 'NULL';
                                        $adolescence = $traveler['pt'];
                                        if (isset($travellerInfos['ticketNumberDetails'][$travellerInfosIndex])) {
                                            $ticket_no = $travellerInfos['ticketNumberDetails'][$travellerInfosIndex];
                                        } else {
                                            $ticket_no = 'NULL'; // or assign a default value as needed
                                        }
                                        $baggage_info = json_encode($traveler['fd']['bI']);
                                        $gds_pnr = $travellerInfos['pnrDetails'][$travellerInfosIndex];
                                        $main_ticket = 'NULL';
                                        $passport_no = 'NULL';
                                        $passport_issue_date = 'NULL';
                                        $passport_expiry_date = 'NULL';
                                        $id_proof_url = 'NULL';
                                        $pan_card_url = 'NULL';
                                        $status = "SUCCESS";
                                        $seat_no = 'NULL';
                                        $meal_status=$meal_status;
                                        $type_of_tour = 'NULL';
                                        $pan_card_url3 = 'NULL';
                                        $pan_card_url4 = 'NULL';
                                        $operating_carrier = 'NULL';
                                        $frequent_flyer = 'NULL';
                                        $basic_fare = $basic_fare;
                                        $flight_duration = $flight_duration;
                                        $layover_time = $layover_time;
                                        $refund_status = $refund_status;
                                        $status = 'SUCCESS'; // Duplicate key
                                       $ticket_master_entries_query = "
                                            INSERT INTO ticket_master_entries (
                                                ticket_id, first_name, middle_name, last_name, birth_date,
                                                adolescence, ticket_no, baggage_info, gds_pnr, main_ticket, passport_no,
                                                passport_issue_date, passport_expiry_date, id_proof_url, pan_card_url, 
                                                status, seat_no, meal_plan, type_of_tour, pan_card_url3, 
                                                pan_card_url4, operating_carrier, frequent_flyer, ticket_status, basic_fare,
                                                passenger_id, flight_duration, layover_time, refund_type
                                            ) VALUES (
                                                '$ticket_id','$first_name', '$middle_name', '$last_name', $birth_date,
                                                '$adolescence', '$ticket_no', '$baggage_info', '$gds_pnr', $main_ticket, 
                                                $passport_no, $passport_issue_date, $passport_expiry_date, $id_proof_url, 
                                                $pan_card_url, '$status', $seat_no,'$meal_status', $type_of_tour, 
                                                $pan_card_url3, $pan_card_url4, $operating_carrier, $frequent_flyer, 
                                                '$ticket_status', $basic_fare, $passenger_id, $flight_duration, $layover_time, 
                                                '$refund_status'
                                            )";
                                            
                                        if (mysqli_query($connection, $ticket_master_entries_query)) 
                                        {
                                           
                                        }
                                        else
                                        {
                                            $queryError=mysqli_error($conn);
                                            $deleteFlightDataSQL = "DELETE FROM ticket_master WHERE ticket_id = $ticket_id";
                                            // mysqli_query($connection, $deleteFlightDataSQL);
                                            $deleteFlightDataSQL = "DELETE FROM ticket_trip_entries WHERE ticket_id = $ticket_id";
                                            // mysqli_query($connection, $deleteFlightDataSQL);
                                            
                                            $returndata=array(
                                                 "status"=>"error",
                                                 "message"=>$queryError
                                            );
                                            echo json_encode($returndata);
                                        }
                                    }
                                }
                    } else {
                        $queryError=mysqli_error($conn);
                        $deleteFlightDataSQL = "DELETE FROM ticket_master WHERE ticket_id = $ticket_id";
                        // mysqli_query($connection, $deleteFlightDataSQL);
                        $deleteFlightDataSQL = "DELETE FROM ticket_trip_entries WHERE ticket_id = $ticket_id";
                        // mysqli_query($connection, $deleteFlightDataSQL);
                        $returndata=array(
                             "status"=>"error",
                             "message"=>$queryError
                        );
                        echo json_encode($returndata);
                    }
                }
                else
                {
                    $queryError=mysqli_error($conn);
                    $deleteFlightDataSQL = "DELETE FROM ticket_master WHERE ticket_id = $ticket_id";
                    // mysqli_query($connection, $deleteFlightDataSQL);
                    
                    $returndata=array(
                         "status"=>"error",
                         "message"=>$queryError
                    );
                    echo json_encode($returndata);
                }
                
        $flightIndex++;                
        }
     }
     
    $branch_admin_id = 1;
    $ticket_id = $ticket_id;
    $financial_year_id = $financial_year_id;
    $payment_date = $bookingDate;
    $payment_amount = $amount;
    $payment_mode = 'razorpay';
    $bank_name = 'NULL';
    $transaction_id = $paymentId;
    $bank_id = 'NULL';
    $clearance_status = 'Cleared';
    $credit_charges = 'NULL';
    $credit_card_details = 'NULL';
    $status = 'SUCCESS';
    $delete_status = 'NULL';
    $ticket_payment_master_query = "INSERT INTO ticket_payment_master (branch_admin_id, ticket_id, financial_year_id, payment_date, payment_amount, payment_mode, bank_name, transaction_id, bank_id, clearance_status, credit_charges, credit_card_details, status, delete_status) VALUES ($branch_admin_id, '$ticket_id', '$financial_year_id', '$payment_date', $payment_amount, '$payment_mode', '$bank_name', '$transaction_id', '$bank_id', '$clearance_status', '$credit_charges', '$credit_card_details', '$status', $delete_status)";

    if (mysqli_query($connection, $ticket_payment_master_query)) 
    {
        // // Define PHP variables corresponding to the fields
        // $financial_year_id = $financial_year_id; // Example financial year ID
        // $branch_admin_id = 1; // Example branch admin ID
        // $module_name = 'Air Ticket Booking,Air Ticket Booking Payment'; // Example module name
        // $module_entry_id = 'NULL'; // Example module entry ID (could be from a related table)
        // $gl_id = 'NULL'; // Example general ledger ID
        // $payment_amount = $payment_amount; // Example payment amount
        // $payment_date = $payment_date; // Example payment date (current date)
        // $payment_particular = 'Flight Booking Payment'; // Example payment particular
        // $transaction_id = $paymentId; // Example transaction ID
        // $payment_side = 'customer_master ledger payment side credit, bank debit side'; // Payment side (Debit or Credit)
        // $clearance_status = 'Cleared'; // Example clearance status
        // $row_specification = 'sales'; // Row specification (if applicable)
        // $ledger_particular = 'To Flight Ticket Sales ,By Cash/Bank'; // Ledger description
        // $created_at = date('Y-m-d H:i:s'); // Date and time of creation
        // $type = 'INVOICE'; // Type of transaction (e.g., Credit or Debit)
        // $tax_type = 1; // Example tax type (e.g., GST or VAT)
        
        // $finance_transaction_master_query = "INSERT INTO finance_transaction_master 
        //         (financial_year_id, branch_admin_id, module_name, module_entry_id, gl_id, 
        //         payment_amount, payment_date, payment_particular, transaction_id, 
        //         payment_side, clearance_status, row_specification, ledger_particular, created_at, 
        //         type, tax_type)
        //         VALUES 
        //         ($financial_year_id, $branch_admin_id, '$module_name', $module_entry_id, $gl_id, 
        //         $payment_amount, '$payment_date', '$payment_particular', '$transaction_id', 
        //         '$payment_side', '$clearance_status', '$row_specification', '$ledger_particular', '$created_at', 
        //         '$type', $tax_type)";
        // if (mysqli_query($connection, $finance_transaction_master_query)) 
        // {
            
            
        // }
        // else
        // {
        //     $queryError=mysqli_error($conn);
        //     $deleteFlightDataSQL = "DELETE FROM ticket_master WHERE ticket_id = $ticket_id";
        //     // mysqli_query($connection, $deleteFlightDataSQL);
        //     $deleteFlightDataSQL = "DELETE FROM ticket_trip_entries WHERE ticket_id = $ticket_id";
        //     // mysqli_query($connection, $deleteFlightDataSQL);
        //     $deleteFlightDataSQL = "DELETE FROM ticket_master_entries WHERE ticket_id = $ticket_id";
        //     // mysqli_query($connection, $deleteFlightDataSQL);
        //     $deleteFlightDataSQL = "DELETE FROM ticket_payment_master WHERE ticket_id = $ticket_id";
        //     // mysqli_query($connection, $deleteFlightDataSQL);
        //     $returndata=array(
        //          "status"=>"error",
        //          "message"=>$queryError
        //     );
        //     echo json_encode($returndata);
        // }
        $returndata=array(
             "status"=>"success",
             "ticket_id"=>$ticket_id,
             "bookingId" => $bookingId,
             "paymentId" => $paymentId,
             "orderId" => $orderId,
             "message"=>"Flight Booking data Successfully Insert."
        );
        echo json_encode($returndata);
    
        
    }
    else
    {
        $queryError=mysqli_error($conn);
        $deleteFlightDataSQL = "DELETE FROM ticket_master WHERE ticket_id = $ticket_id";
        // mysqli_query($connection, $deleteFlightDataSQL);
        $deleteFlightDataSQL = "DELETE FROM ticket_trip_entries WHERE ticket_id = $ticket_id";
        // mysqli_query($connection, $deleteFlightDataSQL);
        $deleteFlightDataSQL = "DELETE FROM ticket_master_entries WHERE ticket_id = $ticket_id";
        // mysqli_query($connection, $deleteFlightDataSQL);
        $returndata=array(
             "status"=>"error",
             "message"=>$queryError
        );
        echo json_encode($returndata);
    }
 } 
 else
 {
        $queryError=mysqli_error($conn);
        $returndata=array(
             "status"=>"error",
             "message"=>mysqli_error($conn)
        );
        echo json_encode($returndata);

 }
}
else
{
    if (isset($farevalidateresult['errors']) && is_array($farevalidateresult['errors']) && isset($farevalidateresult['errors'][0]['message'])) {
        // Respond with an error message in JSON format
        echo json_encode([
            'status' => 'error',
            'message' => $confirmbookresult['errors'][0]['message']
        ]);
        } else {
        // Handle cases where 'errors' key does not exist or is not an array
        echo json_encode([
            'status' => 'error',
            'message' => 'An unexpected error occurred. No error message provided.'
        ]);
    }
}
?>