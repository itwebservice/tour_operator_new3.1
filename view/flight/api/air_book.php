<?php
include '../../../config.php';
// api/air_book.php
require('razorpay-php-2.9.0/Razorpay.php'); // Include the Razorpay SDK

use Razorpay\Api\Api;



// Initialize Razorpay API
$api = new Razorpay\Api\Api($apiKey, $apiSecret);


// Check if the form data is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the form data from $_POST array
    $bookingId = $_POST['bookingId'];
    $totalFare = $_POST['totalFare'];
    
    $_SESSION['post_data'] = $_POST;
    // Retrieve arrays of travelers' data
    $titles = $_POST['ti']; // Array of titles (Mr, Mrs, etc.)
    $firstNames = $_POST['fN']; // Array of first names
    $lastNames = $_POST['lN']; // Array of last names
    $datesOfBirth = $_POST['dob']; // Array of dates of birth

    // Retrieve contact info
    $contactCountryCode = $_POST['contact_country_code'];
    $contactMobile = $_POST['contact_mobile'];
    $contactEmail = $_POST['contact_email'];
    $contactName = $_POST['contact_name'];

    // Assume you're getting the values from a form (e.g., $_POST)
    /*$gstInfo = array(
        'gstNumber' => $_POST['gstNumber'], // GST Number
        'email' => $_POST['gstEmail'], // GST Email
        'registeredName' => $_POST['gstRegisteredName'], // GST Registered Name
        'mobile' => $_POST['gstMobile'], // GST Mobile Number
        'address' => $_POST['gstAddress'] // GST Address
    );*/
    $deliveryEmails=array();
    $deliveryEmails[]=$_POST['contact_email'];
    $deliveryContacts=array();
    $deliveryContacts[]=$_POST['contact_country_code'].$_POST['contact_mobile'];
    $deliveryInfo = array(
        'emails' => isset($_POST['deliveryEmails']) ? $_POST['deliveryEmails'] : $deliveryEmails,
        'contacts' => isset($_POST['deliveryContacts']) ? $_POST['deliveryContacts'] : $deliveryContacts
    );
    
    // Retrieve arrays of travelers' data
    $titles = $_POST['ti']; // Array of titles (Mr, Mrs, etc.)
    $pts = $_POST['pt'];
    $firstNames = $_POST['fN']; // Array of first names
    $lastNames = $_POST['lN']; // Array of last names
    $datesOfBirth = isset($_POST['dob']) ? $_POST['dob'] : array(); // Array of dates of birth (optional)
    
    // Initialize the traveler info array
    $travellerInfo = array();
    
    // Loop through the arrays to construct the travellerInfo array
    foreach ($titles as $index => $title) {
        // Make sure the other fields exist for the given index
        if (isset($firstNames[$index]) && isset($lastNames[$index])) {
            // Add each traveller's info to the travellerInfo array
            $travellerData = array(
                'ti' => $title, // Title (Mr, Mrs, etc.)
                'fN' => $firstNames[$index], // First name
                'lN' => $lastNames[$index], // Last name
                'pt' => strtoupper($pts[$index]) // Type (Adult, Child, or Infant)
            );
            
            // Add date of birth only for INFANT
            if ($travellerData['pt'] === 'INFANT' && isset($datesOfBirth[$index])) {
                $travellerData['dob'] = $datesOfBirth[$index]; // Add Date of Birth only for INFANT
            }
            
            // Add the traveller data to the travellerInfo array
            $travellerInfo[] = $travellerData;
        }
    }
    
    // Final structured array
    
    $airBookData = array(
        'bookingId' => $_POST['bookingId'], // Booking ID
        'travellerInfo' => $travellerInfo, // Traveler info
        //'gstInfo' => $gstInfo, // GST info
        'deliveryInfo' => $deliveryInfo // Delivery info
    );
    

    $airBookresult = callAPI('POST', 'https://apitest.tripjack.com/oms/v1/air/book', json_encode($airBookData));
  
    $airBookresult=json_decode($airBookresult,true);
    if (isset($airBookresult['status']['success']) && 
        $airBookresult['status']['httpStatus'] == 200 && 
        $airBookresult['status']['success'] == true) {
            
        $bookingId=$airBookresult['bookingId'];   
        $bookingData=array();
        $bookingData['bookingId']=$bookingId;

        $farevalidate = callAPI('POST', 'https://apitest.tripjack.com/oms/v1/air/fare-validate', json_encode($bookingData));
       
        $farevalidateresult=json_decode($farevalidate,true);
        
        if (isset($farevalidateresult['status']['success']) && 
        $farevalidateresult['status']['httpStatus'] == 200 && 
        $farevalidateresult['status']['success'] == true)
        {
            
            $bookingId = $farevalidateresult['bookingId'];   
            $totalAmount = $totalFare; // Total fare amount from API response
            $currency = "INR"; // Razorpay supports INR by default
            
            try {
                // Create an order in Razorpay
                $razorpayOrder = $api->order->create([
                    'receipt' => $bookingId, 
                    'amount' => $totalAmount * 100, // Razorpay expects amount in paise
                    'currency' => $currency
                ]);
                
                $orderId = $razorpayOrder['id']; // Get Razorpay order ID
        
                // Send orderId and other details to the client for payment
                echo json_encode([
                    'status' => 'success',
                    'orderId' => $orderId,
                    'amount' => $totalAmount,
                    'currency' => $currency,
                    'bookingId' => $bookingId,
                    'contact_country_code' => $_POST['contact_country_code'],
                    'contact_mobile' => $_POST['contact_mobile'],
                    'contact_email' => $_POST['contact_email'],
                    'contact_name' => $_POST['contact_name'],
                ]);
        
            } catch (Exception $e) {
                echo json_encode([
                    'status' => 'error',
                    'message' => $e->getMessage(),
                ]);
                
            }
        }
        else
        {
            
             if (isset($farevalidateresult['errors']) && is_array($farevalidateresult['errors']) && isset($farevalidateresult['errors'][0]['message'])) {
                // Respond with an error message in JSON format
                echo json_encode([
                    'status' => 'error',
                    'message' => $farevalidateresult['errors'][0]['message']
                ]);
            } else {
                // Handle cases where 'errors' key does not exist or is not an array
                echo json_encode([
                    'status' => 'error',
                    'message' => 'An unexpected error occurred. No error message provided.'
                ]);
            }
        }
        


    } else {
        
        // Check if 'errors' key exists and is an array
        if (isset($airBookresult['errors']) && is_array($airBookresult['errors']) && isset($airBookresult['errors'][0]['message'])) {
            // Respond with an error message in JSON format
            if($airBookresult['errors'][0]['errCode']==2502)
            {
                $bookingId=$_POST['bookingId'];   
                $bookingData=array();
                $bookingData['bookingId']=$bookingId;
        
                $farevalidate = callAPI('POST', 'https://apitest.tripjack.com/oms/v1/air/fare-validate', json_encode($bookingData));
                $farevalidateresult=json_decode($farevalidate,true);
                
                if (isset($farevalidateresult['status']['success']) && 
                $farevalidateresult['status']['httpStatus'] == 200 && 
                $farevalidateresult['status']['success'] == true)
                {
                    
                    $bookingId = $farevalidateresult['bookingId'];   
                    $totalAmount = $totalFare; // Total fare amount from API response
                    $currency = "INR"; // Razorpay supports INR by default
                    
                    try {
                        // Create an order in Razorpay
                        $razorpayOrder = $api->order->create([
                            'receipt' => $bookingId, 
                            'amount' => $totalAmount * 100, // Razorpay expects amount in paise
                            'currency' => $currency
                        ]);
                        
                        $orderId = $razorpayOrder['id']; // Get Razorpay order ID
                
                        // Send orderId and other details to the client for payment
                        echo json_encode([
                            'status' => 'success',
                            'orderId' => $orderId,
                            'amount' => $totalAmount,
                            'currency' => $currency,
                            'bookingId' => $bookingId,
                            'contact_country_code' => $_POST['contact_country_code'],
                            'contact_mobile' => $_POST['contact_mobile'],
                            'contact_email' => $_POST['contact_email'],
                            'contact_name' => $_POST['contact_name'],
                        ]);
                
                    } catch (Exception $e) {
                        echo json_encode([
                            'status' => 'error',
                            'message' => $e->getMessage(),
                        ]);
                        
                    }
        }
                else
                {
                    
                    
                     if (isset($farevalidateresult['errors']) && is_array($farevalidateresult['errors']) && isset($farevalidateresult['errors'][0]['message'])) {
                        // Respond with an error message in JSON format
                        echo json_encode([
                            'status' => 'error',
                            'message' => $farevalidateresult['errors'][0]['message']
                        ]);
                    } else {
                        // Handle cases where 'errors' key does not exist or is not an array
                        echo json_encode([
                            'status' => 'error',
                            'message' => 'An unexpected error occurred. No error message provided.'
                        ]);
                    }
        }
            }
            else
            {
                echo json_encode([
                    'status' => 'error',
                    'errorCode'=>$airBookresult['errors'][0]['errCode'],
                    'message' => $airBookresult['errors'][0]['message']
                ]);
            }
            
        } else {
            // Handle cases where 'errors' key does not exist or is not an array
            echo json_encode([
                'status' => 'error',
                'message' => 'An unexpected error occurred. No error message provided.'
            ]);
        }
    }

}

?>