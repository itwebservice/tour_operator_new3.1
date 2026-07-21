<?php
include '../../../config.php';
$datareview=[];
// Check if the selectedValues data is sent via POST
if (isset($_POST['selectedValues'])) {
    $searchType = isset($_GET['searchType']) ? $_GET['searchType'] : 'oneway';
    $order = isset($_GET['order']) ? $_GET['order'] : 'price-asc';
    $travelClass = isset($_GET['travelClass']) ? $_GET['travelClass'] : 'Economy';
    $adult = isset($_GET['adult']) ? $_GET['adult'] : 1;
    $child = isset($_GET['child']) ? $_GET['child'] : 0;
    $infant = isset($_GET['infant']) ? $_GET['infant'] : 0;
    $from = isset($_GET['from']) ? $_GET['from'] : null;
    $to = isset($_GET['to']) ? $_GET['to'] : null;
    $departureDate = isset($_GET['departureDate']) ? $_GET['departureDate'] : null;
    $returnDate = isset($_GET['returnDate']) ? $_GET['returnDate'] : null;
    // Decode the JSON data sent from JavaScript
    $selectedValues = $_POST['selectedValues'];

    // You can now process the $selectedValues array
    // For example, log it or handle it as needed
    foreach ($selectedValues as $group => $value) 
    {
        $datareview['priceIds'][]=$value;
    }
    
    $resultreview = callAPI('POST', 'https://apitest.tripjack.com/fms/v1/review', json_encode($datareview));
    $resultreviewArr = json_decode($resultreview, true);
    
    
    if( isset($resultreviewArr['status']['success'] ) && $resultreviewArr['status']['httpStatus']=200 && $resultreviewArr['status']['success']==true) 
    {
        $allFlighData=array();
        $totalPayAmount = 0;
        foreach($resultreviewArr['tripInfos'] as $tripInfossI)
        {
            $flighAmount=0;
            // print_r($tripInfossI['sI'][0]['fD']['aI']['code']);
            // die;
            $dtdate = new DateTime($tripInfossI['sI'][0]['dt']);
            $atdate = new DateTime($tripInfossI['sI'][0]['at']);
            $flightData=array(
                "flight_code"=>$tripInfossI['sI'][0]['fD']['aI']['code'],
                "flight_name"=>$tripInfossI['sI'][0]['fD']['aI']['name'],
                "flight_no"=>$tripInfossI['sI'][0]['fD']['fN'],
                "da_city"=>$tripInfossI['sI'][0]['da']['city'],
                "da_code"=>$tripInfossI['sI'][0]['da']['code'],
                "aa_city"=>$tripInfossI['sI'][0]['aa']['city'],
                "aa_code"=>$tripInfossI['sI'][0]['aa']['code'],
                "dtdate"=>$dtdate->format('d-m-Y, h:i A'),
                "atdate"=>$atdate->format('d-m-Y, h:i A'),
                "dttime"=>explode('T', $tripInfossI['sI'][0]['dt'])[1],
                "attime"=>explode('T', $tripInfossI['sI'][0]['at'])[1],
                "cabinClass"=>$resultreviewArr['searchQuery']['cabinClass']
            );
            if (isset($tripInfossI['totalPriceList']) && is_array($tripInfossI['totalPriceList'])) {
                foreach ($tripInfossI['totalPriceList'] as $tripInfostotalPriceList) {
                    if (isset($tripInfostotalPriceList['fd']) && is_array($tripInfostotalPriceList['fd'])) {
                        $fairDetails = $tripInfostotalPriceList['fd'];
                        //print_r($fairDetails);
                        
                        foreach ($fairDetails as $fdindex => $fd) {
                            // Make sure $fd contains the necessary keys
                            if (!isset($fd['fC']['TF'])) {
                                continue; // Skip if 'TF' is missing
                            }
        
                            // Determine the passenger count
                            if ($fdindex == "CHILD") {
                                $prcount = $child ?? 0; // Default to 0 if $child is not defined
                            } elseif ($fdindex == "INFANT") {
                                $prcount = $infant ?? 0; // Default to 0 if $infant is not defined
                            } else {
                                $prcount = $adult ?? 0; // Default to 0 if $adult is not defined
                            }
                            
                            // Add to the total payable amount
                            $flighAmount += $fd['fC']['TF'] * $prcount;
                            
                        }
                    }
                }
            }
            $flightData['flight_amount']=$flighAmount;
            $totalPayAmount+=$flighAmount;
            $allFlighData['flightData'][]=$flightData;
            
        } 
        // 
        $allFlighData['total_amount']=$totalPayAmount;
        $allFlighData['status']=array(
          "success"=>true  
        );
        echo json_encode($allFlighData);
             // Initialize total payable amount
           
    }
    else
    {
       echo $resultreview;
    }

} 



?>