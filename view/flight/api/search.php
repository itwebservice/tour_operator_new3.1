<?php 
error_reporting(E_ALL);
ini_set('display_errors', TRUE);

function callAPI($method, $url, $data){
    $curl = curl_init();

    switch($method){
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);
            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
        default:
            if ($data)
                $url = sprintf("%s?%s", $url, http_build_query($data));
    }

    // Set cURL options
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'apikey:1123423b81fdfd-4eb9-40a2-9c97-3a736e306b7e',
        'Content-Type: application/json'
    )); 
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    // Execute cURL request and get the response
    $result = curl_exec($curl);

    // Check for errors
    if(!$result) {
        // Output the cURL error if request fails
        die("Connection Failure: " . curl_error($curl));
    }

    curl_close($curl);
    return $result;
}

// Extract incoming POST data (assumed to be sent via AJAX)
$searchType = isset($_POST['searchType']) ? $_POST['searchType'] : 'oneway';
$order = isset($_POST['order']) ? $_POST['order'] : 'price-asc';
$travelClass = isset($_POST['travelClass']) ? $_POST['travelClass'] : 'Economy';
$adult = isset($_POST['adult']) ? $_POST['adult'] : 1;
$child = isset($_POST['child']) ? $_POST['child'] : 0;
$infant = isset($_POST['infant']) ? $_POST['infant'] : 0;
$from = isset($_POST['from']) ? $_POST['from'] : null;
$to = isset($_POST['to']) ? $_POST['to'] : null;
$departureDate = isset($_POST['departureDate']) ? $_POST['departureDate'] : null;
$returnDate = isset($_POST['returnDate']) ? $_POST['returnDate'] : null;


// Initialize an empty array for routes
$route = [];

// Check if from, to, and departureDate are all provided
if ($from && $to && $departureDate) {
    // Create the route array with the data
    if($searchType=="oneway" || $searchType=="round")
    {
        if ($departureDate) {
            // Create a DateTime object from the input date
            $date = DateTime::createFromFormat('d/m/Y', $departureDate);
        
            // If the date is valid, format it to 'Y-m-d' (e.g., 2024-01-01)
            if ($date) {
                $formattedDate = $date->format('Y-m-d');
            } else {
                // Handle invalid date format (optional)
                echo "Invalid date format.";
                $formattedDate = null;
            }
        } else {
            $formattedDate = null; // Default if no date is provided
        }
        if ($returnDate) {
            // Create a DateTime object from the input date
            $date = DateTime::createFromFormat('d/m/Y', $returnDate);
        
            // If the date is valid, format it to 'Y-m-d' (e.g., 2024-01-01)
            if ($date) {
                $formattedreturnDate = $date->format('Y-m-d');
            } else {
                // Handle invalid date format (optional)
                echo "Invalid date format.";
                $formattedreturnDate = null;
            }
        } else {
            $formattedreturnDate = null; // Default if no date is provided
        }
        $route[] = [
            'from' => $from,
            'to' => $to,
            'date' => $formattedDate 
        ];
    }
    if($searchType=="round")
    {
        if ($returnDate) {
            // Create a DateTime object from the input date
            $date = DateTime::createFromFormat('d/m/Y', $returnDate);
        
            // If the date is valid, format it to 'Y-m-d' (e.g., 2024-01-01)
            if ($date) {
                $formattedreturnDate = $date->format('Y-m-d');
            } else {
                // Handle invalid date format (optional)
                echo "Invalid date format.";
                $formattedreturnDate = null;
            }
        } else {
            $formattedreturnDate = null; // Default if no date is provided
        }
        
          
            $route[] = [
                'from' => $to,
                'to' => $from,
                'date' => $formattedreturnDate 
            ];
            
        
    }
    if($searchType=="multicity")
    {
        $departureDate_arr=explode(",",$departureDate);
        $from_arr=explode(",",$from);
        $to_arr=explode(",",$to);
        foreach($departureDate_arr as $index => $departureDate)
        {
            if ($departureDate) {
                // Create a DateTime object from the input date
                $date = DateTime::createFromFormat('d/m/Y', $departureDate);
            
                // If the date is valid, format it to 'Y-m-d' (e.g., 2024-01-01)
                if ($date) {
                    $formattedDate = $date->format('Y-m-d');
                } else {
                    // Handle invalid date format (optional)
                    echo "Invalid date format.";
                    $formattedDate = null;
                }
            } else {
                $formattedDate = null; // Default if no date is provided
            }
            $route[] = [
                'from' => $from_arr[$index],
                'to' => $to_arr[$index],
                'date' => $formattedDate 
            ];
        }
    }
}
$allroute=$route;

$additionalFilters = isset($_POST['additionalFilters']) ? $_POST['additionalFilters'] : [
    'airlines' => [],
    'prices' => [],
    'stops' => [],
    'departure_times' => [],
    'arrival_times' => []
];
//print_r($additionalFilters);

$_REQUEST = [
    'searchType' => $searchType,
    'order' => 'price-asc',
    'travelClass' => $travelClass,
    'adult' => $adult,
    'child' => $child,
    'infant' => $infant,
    'route' => $route,
    'additionalFilters' => [
        'airlines' => [

        ],
        'prices' => [

        ],
        'stops' => [

        ],
        'departure_times' => [

        ],
        'arrival_times' => [

        ],
    ],
]; 

function getReq($key, $fallback = null) {
    return isset($_REQUEST[$key]) ? $_REQUEST[$key] : $fallback;
}

function timeCategory($hm){
    $timeParts = explode(':', $hm);

    $hours = (int)$timeParts[0];
    $minutes = (int)$timeParts[1];

    if ($hours < 6) {
        $timeCategory = '0-6';
    } elseif ($hours < 12) {
        $timeCategory = '6-12';
    } elseif ($hours < 18) {
        $timeCategory = '12-18';
    } else {
        $timeCategory = '18-24';
    }

    return $timeCategory;
}

function minutesToTime($minutes) {
    $hours = floor($minutes / 60); 
    $minutes = $minutes % 60;

    return sprintf('%02d:%02d', $hours, $minutes);
}

$travelClasses = [
    'Economy' => 'ECONOMY',
    'First' => 'FIRST',
    'Business' => 'BUSINESS',
    'Premium Economy' => 'PREMIUM_ECONOMY'
];

// Prepare the data based on the incoming request
$data = [
    'searchQuery' => [
        'cabinClass' => $travelClasses[ getReq('travelClass', 'Economy') ],
        'paxInfo' => [
            "ADULT" => getReq('adult', 1),
            "CHILD" => getReq('child', 0),
            "INFANT" => getReq('infant', 0)
        ],
       
        "searchModifiers" => [
            "pft" => "REGULAR",
            "isDirectFlight" => true,
            "isConnectingFlight" => true
        ]
    ]
];

foreach(getReq('route') as $route) {
    $data['searchQuery']['routeInfos'][] = 
         [
            'fromCityOrAirport' => [ 'code' => $route['from'] ],
            'toCityOrAirport' => [ 'code' => $route['to'] ],
            'travelDate' => $route['date'], // YYYY-MM-DD
        ];
}

// Define a cache file path
// Initialize an empty array to hold the flattened data
$flattenedRoute = [];
foreach ($allroute as $segment) {
    // Ensure $segment is an array
    if (is_array($segment)) {
        // Flatten each sub-array to string format
        $flattenedRoute[] = implode("_", $segment);
    } else {
        // If it's not an array, handle it accordingly (optional)
        $flattenedRoute[] = $segment;
    }
}

// Combine all segments into a single string with "_" separator
$route_name = str_replace("-", "",str_replace("_", "",implode("_", $flattenedRoute)));

// Dynamically create the cache file name
$cacheFile = 'cache/'.$route_name.'.json';
session_start();
// Save $cacheFile into the session
$_SESSION['cacheFile'] = $cacheFile;

$cacheTime = 3600; // Cache expiration time in seconds (e.g., 1 hour)
function cleanExpiredCache($cacheDir, $cacheTime) {
    if (is_dir($cacheDir)) {
        $files = scandir($cacheDir);
        foreach ($files as $file) {
            $filePath = $cacheDir . '/' . $file;
            if (is_file($filePath) && (time() - filemtime($filePath)) >= $cacheTime) {
                unlink($filePath); // Delete expired cache file
            }
        }
    }
}

// Clean up expired cache files in the 'cache' directory
cleanExpiredCache('cache', $cacheTime);
    // Check if the cache file exists and is not expired
    if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTime  && $_POST['callBy']!='') {
        // Read from cache
        $resultArr = json_decode(file_get_contents($cacheFile), true);

    } else {
        // Make the API call and store the result in cache
        $result = callAPI('POST', 'https://apitest.tripjack.com/fms/v1/air-search-all', json_encode($data));
        $resultArr = json_decode($result, true);
        //print_r($result);
        
        // Save to cache
        if (!is_dir('cache')) {
            mkdir('cache', 0777, true); // Create the cache directory if it doesn't exist
        }
        file_put_contents($cacheFile, json_encode($resultArr));
    }
     //echo $result;
     //die;
    //print_r($result)
// if(isset($resultArr['errors']))
// {
// print_r($result);
// die;
// }
//$result = callAPI('POST','https://api.tripjack.com/fms/v1/air-search-all',json_encode($data));

$filtered = [];
$filters = getReq('additionalFilters');
$filters=array();
//$resultArr = json_decode($result, true);
$airlineCounts=array();
$airlineName="";
$arrivalTimeCount=array();
$arrivalTime="";
$departureTimeCount=array();
$departureTime="";
$stopCount=array();
$stopNo='';

if(isset($_POST['stops']))
{
    $filters['stops']=$_POST['stops'];
}

if(isset($_POST['airlines']))
{
    $filters['airlines']=$_POST['airlines'];
}

if(isset($_POST['departureTime']))
{
    $filters['departure_times']=$_POST['departureTime'];
}

if(isset($_POST['arrivalTime']))
{
    $filters['arrival_times']=$_POST['arrivalTime'];
}

if(isset($_POST['pricefrom']))
{
    $filters['pricefrom']=$_POST['pricefrom'];
}

if(isset($_POST['priceto']))
{
    $filters['priceto']=$_POST['priceto'];
}

//print_r($resultArr['searchResult']['tripInfos']);
if( isset($resultArr['searchResult']['tripInfos'] ) ) 
{
    if(isset($resultArr['searchResult']['tripInfos']['ONWARD']))
    {
        foreach($resultArr['searchResult']['tripInfos']['ONWARD'] as $index => $result) {
        $segments = [];

            foreach($result['sI'] as $segmentindex => $row) {
                $row['stopsAlt'] = $row['stops'] > 1 ? '1+' : $row['stops'];
                $row['arrivalTimeAlt'] = timeCategory(explode('T', $row['at'])[1]);
                $row['departureTimeAlt'] = timeCategory(explode('T', $row['dt'])[1]);
                $row['durationAlt'] = minutesToTime($row['duration']);
                if($segmentindex==0)
                {
                    if( isset($filters['airlines']) && count($filters['airlines']) > 0 && ! in_array($row['fD']['aI']['name'], $filters['airlines']) ) {
                        continue 2;
                    }
            
        
                    if( isset($filters['departure_times']) && count($filters['departure_times']) > 0 && ! in_array($row['departureTimeAlt'], $filters['departure_times'])  ) {
                        continue 2;
                    }
                }
                if(count($result['sI'])-1==$segmentindex)
                {
                    if( isset($filters['arrival_times']) && count($filters['arrival_times']) > 0 && ! in_array($row['arrivalTimeAlt'], $filters['arrival_times'])  ) {
                        continue 2;
                    }
                }
        
                $row['da']['at'] = explode('T', $row['dt']);
                $row['aa']['at'] = explode('T', $row['at']);
        
                $row['da']['at'] = [
                    'date' => $row['da']['at'][0],
                    'time' => $row['da']['at'][1]
                ];
        
                $row['aa']['at'] = [
                    'date' => $row['aa']['at'][0],
                    'time' => $row['aa']['at'][1]
                ];
                
                if($segmentindex==0)
                {
                    $airlineName =  $row['fD']['aI']['name'];
                    $departureTime =  $row['departureTimeAlt'];
                }
                if(count($result['sI'])-1==$segmentindex)
                {
                    $arrivalTime =  $row['arrivalTimeAlt'];
                }
    
                $segments[] = [
                    'segment_id' => $row['id'],
                    'airline_code' => $row['fD']['aI']['code'],
                    'airline_name' => $row['fD']['aI']['name'],
                    'flight_number' => $row['fD']['fN'] ?? '',
                    'equipment_type' => $row['eT'] ?? '',
                    'stops' => $row['stops'],
                    'duration' => $row['duration'],
                    'durationAlt' => $row['durationAlt'],
                    'departure' => $row['da'],
                    'arrival' => $row['aa'],
                ];
            }
            if(count($segments)==0)
            {
                $stopNo=count($segments);
            }
            else
            {
                $stopNo=count($segments)-1;
            }
            
            if( isset($filters['stops']) && count($filters['stops']) > 0 && ! in_array($stopNo, $filters['stops'])  ) {
                continue;
            }
            
            if(count($segments)>=1)
            {
                $resultPrices = [];
                if($stopNo>=3)
                {
                    $stopNo=3;
                }
                            
                            
        
                foreach($result['totalPriceList'] as $priceIndex => $price) 
                {
                   
                    $total_amount=0;
                    if($priceIndex==0)
                    {
                        $first_total_amount=0;
                        $flightidprice=$price['id'];
                    }
                    $resultPrice = [
                        'id' => $price['id'],
                        'fareIdentifier' => $price['fareIdentifier'],
                        'variants' => [],
                        'sri'=> $price['sri'] ?? '',
                        'msri'=> $price['msri'] ?? '',
                    ];
                    
        
                    if( isset($price['fD']) ){
                        $price['fd'] = $price['fD'];
                    }
                   
                    foreach($price['fd'] as $variant => $value) {
                       
                        $resultPrice['variants'][ strtolower($variant) ] = [
                            'base_price' => $value['fC']['BF'],
                            'taxes' => $value['fC']['TAF'] ?? 0,
                            'total_price' => $value['fC']['TF'],
                            'baggage_checking' => $value['bI']['iB'] ?? 0,
                            'baggage_cabin' => $value['bI']['cB'] ?? 0,
                            'refundable' => $value['rT'] ?? false,
                            'free_meal' => $value['mI'] ?? false,
                            'class' => $value['cc']
                        ];
                        
                        if($variant=="CHILD")
                        {
                            $prcount=$child;
                        }
                        elseif($variant=="INFANT")
                        {
                            $prcount=$infant;
                        }
                        else
                        {
                            $prcount=$adult;
                        }
                        
                        $total_amount+=$value['fC']['TF']*$prcount;
                        if($priceIndex==0)
                        {
                            $first_total_amount+=$value['fC']['TF']*$prcount;
                        }
                    }
                    $resultPrice['total_amount']=$total_amount;
                    $resultPrices[] = $resultPrice;
                    
                }
                
                if( isset($filters['pricefrom']) && isset($filters['priceto'])) 
                {
                    if($filters['pricefrom'] != '' && $filters['priceto'] != '' && $filters['priceto']>0)
                    {
                        if($first_total_amount < $filters['pricefrom'] || $first_total_amount > $filters['priceto'])
                        {
                            continue;
                        }
                    }
                }
                
                            if($stopNo!="" || $stopNo==0)
                            {
                            // Increment the count for each airline name
                                if (!isset($stopCount[$stopNo])) 
                                {
                                    $stopCount[$stopNo] = 0;
                                }
                                $stopCount[$stopNo]++;
                            }
                            if($departureTime!='')
                            {
                                // Increment the count for each airline name
                                if (!isset($departureTimeCount[$departureTime])) {
                                    $departureTimeCount[$departureTime] = 0;
                                }
                                $departureTimeCount[$departureTime]++;
                            }
                            
                            
                            if($arrivalTime!='')
                            {
                            // Increment the count for each airline name
                                if (!isset($arrivalTimeCount[$arrivalTime])) {
                                    $arrivalTimeCount[$arrivalTime] = 0;
                                }
                                $arrivalTimeCount[$arrivalTime]++;
                            }
                            
                            
                            if($airlineName!=='')
                            {
                                // Increment the count for each airline name
                                if (!isset($airlineCounts[$airlineName])) {
                                    $airlineCounts[$airlineName] = 0;
                                }
                                $airlineCounts[$airlineName]++;
                            }
                $filtered['ONWARD'][] = [
                    'index' => $index,
                    'flightidprice'=>$flightidprice,
                    'segments' => $segments,
                    'prices' => $resultPrices
                ];
            }
        }
    }
    
    if(isset($resultArr['searchResult']['tripInfos']['RETURN']))
    {
        foreach($resultArr['searchResult']['tripInfos']['RETURN'] as $index => $result) {
        $segments = [];

            foreach($result['sI'] as $segmentindex => $row) {
                $row['stopsAlt'] = $row['stops'] > 1 ? '1+' : $row['stops'];
                $row['arrivalTimeAlt'] = timeCategory(explode('T', $row['at'])[1]);
                $row['departureTimeAlt'] = timeCategory(explode('T', $row['dt'])[1]);
                $row['durationAlt'] = minutesToTime($row['duration']);
                if($segmentindex==0)
                {
                    if( isset($filters['airlines']) && count($filters['airlines']) > 0 && ! in_array($row['fD']['aI']['name'], $filters['airlines']) ) {
                        continue 2;
                    }
            
        
                    if( isset($filters['departure_times']) && count($filters['departure_times']) > 0 && ! in_array($row['departureTimeAlt'], $filters['departure_times'])  ) {
                        continue 2;
                    }
                }
                if(count($result['sI'])-1==$segmentindex)
                {
                if( isset($filters['arrival_times']) && count($filters['arrival_times']) > 0 && ! in_array($row['arrivalTimeAlt'], $filters['arrival_times'])  ) {
                    continue 2;
                }
                }
        
                $row['da']['at'] = explode('T', $row['dt']);
                $row['aa']['at'] = explode('T', $row['at']);
        
                $row['da']['at'] = [
                    'date' => $row['da']['at'][0],
                    'time' => $row['da']['at'][1]
                ];
        
                $row['aa']['at'] = [
                    'date' => $row['aa']['at'][0],
                    'time' => $row['aa']['at'][1]
                ];
                
                if($segmentindex==0)
                {
                    $airlineName =  $row['fD']['aI']['name'];
                    $departureTime =  $row['departureTimeAlt'];
                }
                if(count($result['sI'])-1==$segmentindex)
                {
                $arrivalTime =  $row['arrivalTimeAlt'];
                }
    
                $segments[] = [
                    'segment_id' => $row['id'],
                    'airline_code' => $row['fD']['aI']['code'],
                    'airline_name' => $row['fD']['aI']['name'],
                    'flight_number' => $row['fD']['fN'] ?? '',
                    'equipment_type' => $row['eT'] ?? '',
                    'stops' => $row['stops'],
                    'duration' => $row['duration'],
                    'durationAlt' => $row['durationAlt'],
                    'departure' => $row['da'],
                    'arrival' => $row['aa'],
                ];
            }
            if(count($segments)==0)
            {
                $stopNo=count($segments);
            }
            else
            {
                $stopNo=count($segments)-1;
            }
            
            if( isset($filters['stops']) && count($filters['stops']) > 0 && ! in_array($stopNo, $filters['stops'])  ) {
                continue;
            }
            if(count($segments)>=1)
            {
                $resultPrices = [];
                if($stopNo>=3)
                {
                    $stopNo=3;
                }
                            
                            
        
                foreach($result['totalPriceList'] as $priceIndex => $price) 
                {
                    $total_amount=0;
                    if($priceIndex==0)
                    {
                        $first_total_amount=0;
                        $flightidprice=$price['id'];
                    }
                    $resultPrice = [
                        'id' => $price['id'],
                        'fareIdentifier' => $price['fareIdentifier'],
                        'variants' => [],
                        'sri'=> $price['sri'] ?? '',
                        'msri'=> $price['msri'] ?? '',
                    ];
        
                    if( isset($price['fD']) ){
                        $price['fd'] = $price['fD'];
                    }
                   
                    foreach($price['fd'] as $variant => $value) {
                       
                        $resultPrice['variants'][ strtolower($variant) ] = [
                            'base_price' => $value['fC']['BF'],
                            'taxes' => $value['fC']['TAF'] ?? 0,
                            'total_price' => $value['fC']['TF'],
                            'baggage_checking' => $value['bI']['iB'] ?? 0,
                            'baggage_cabin' => $value['bI']['cB'] ?? 0,
                            'refundable' => $value['rT'] ?? false,
                            'free_meal' => $value['mI'] ?? false,
                            'class' => $value['cc']
                        ];
                        if($variant=="CHILD")
                        {
                            $prcount=$child;
                        }
                        elseif($variant=="INFANT")
                        {
                            $prcount=$infant;
                        }
                        else
                        {
                            $prcount=$adult;
                        }
                        
                        $total_amount+=$value['fC']['TF']*$prcount;
                        if($priceIndex==0)
                        {
                            $first_total_amount+=$value['fC']['TF']*$prcount;
                        }
                    }
                    $resultPrice['total_amount']=$total_amount;
                    $resultPrices[] = $resultPrice;
                    
                }
                
                if( isset($filters['pricefrom']) && isset($filters['priceto'])) 
                {
                    if($filters['pricefrom'] != '' && $filters['priceto'] != '' && $filters['priceto']>0)
                    {
                        if($first_total_amount < $filters['pricefrom'] || $first_total_amount > $filters['priceto'])
                        {
                            continue;
                        }
                    }
                }
                            if($stopNo!="" || $stopNo==0)
                            {
                            // Increment the count for each airline name
                                if (!isset($stopCount[$stopNo])) {
                                    $stopCount[$stopNo] = 0;
                                }
                                $stopCount[$stopNo]++;
                            }
                            if($departureTime!='')
                            {
                                // Increment the count for each airline name
                                if (!isset($departureTimeCount[$departureTime])) {
                                    $departureTimeCount[$departureTime] = 0;
                                }
                                $departureTimeCount[$departureTime]++;
                            }
                            
                            
                            if($arrivalTime!='')
                            {
                            // Increment the count for each airline name
                                if (!isset($arrivalTimeCount[$arrivalTime])) {
                                    $arrivalTimeCount[$arrivalTime] = 0;
                                }
                                $arrivalTimeCount[$arrivalTime]++;
                            }
                            
                            
                            if($airlineName!=='')
                            {
                                // Increment the count for each airline name
                                if (!isset($airlineCounts[$airlineName])) {
                                    $airlineCounts[$airlineName] = 0;
                                }
                                $airlineCounts[$airlineName]++;
                            }
                $filtered['RETURN'][] = [
                    'index' => $index,
                    'flightidprice'=>$flightidprice,
                    'segments' => $segments,
                    'prices' => $resultPrices
                ];
            }
        }
    }
    
    if($searchType=='multicity')
    {
        if(isset($resultArr['searchResult']['tripInfos']))
        {
            foreach($resultArr['searchResult']['tripInfos'] as $Cityindex => $resultCity) 
            {
                foreach($resultCity as $index => $result) {
                        $segments = [];

                        foreach($result['sI'] as $segmentindex => $row) {
                            $row['stopsAlt'] = $row['stops'] > 1 ? '1+' : $row['stops'];
                            $row['arrivalTimeAlt'] = timeCategory(explode('T', $row['at'])[1]);
                            $row['departureTimeAlt'] = timeCategory(explode('T', $row['dt'])[1]);
                            $row['durationAlt'] = minutesToTime($row['duration']);
                            if($segmentindex==0)
                            {
                                if( isset($filters['airlines']) && count($filters['airlines']) > 0 && ! in_array($row['fD']['aI']['name'], $filters['airlines']) ) {
                                    continue 2;
                                }
    
                                if( isset($filters['departure_times']) && count($filters['departure_times']) > 0 && ! in_array($row['departureTimeAlt'], $filters['departure_times'])  ) {
                                    continue 2;
                                }
                            }
                            if(count($result['sI'])-1==$segmentindex)
                            {
                                if( isset($filters['arrival_times']) && count($filters['arrival_times']) > 0 && ! in_array($row['arrivalTimeAlt'], $filters['arrival_times'])  ) {
                                    continue 2;
                                }
                            }
                    
                            $row['da']['at'] = explode('T', $row['dt']);
                            $row['aa']['at'] = explode('T', $row['at']);
                    
                            $row['da']['at'] = [
                                'date' => $row['da']['at'][0],
                                'time' => $row['da']['at'][1]
                            ];
                    
                            $row['aa']['at'] = [
                                'date' => $row['aa']['at'][0],
                                'time' => $row['aa']['at'][1]
                            ];
                            
                            if($segmentindex==0)
                            {
                                $airlineName =  $row['fD']['aI']['name'];
                                $departureTime =  $row['departureTimeAlt'];
                            }
                            if(count($result['sI'])-1==$segmentindex)
                            {
                                $arrivalTime =  $row['arrivalTimeAlt'];
                            }
                            
                
                            $segments[] = [
                                'segment_id' => $row['id'],
                                'airline_code' => $row['fD']['aI']['code'],
                                'airline_name' => $row['fD']['aI']['name'],
                                'flight_number' => $row['fD']['fN'] ?? '',
                                'equipment_type' => $row['eT'] ?? '',
                                'stops' => $row['stops'],
                                'duration' => $row['duration'],
                                'durationAlt' => $row['durationAlt'],
                                'departure' => $row['da'],
                                'arrival' => $row['aa'],
                            ];
                            
                    }
                            if(count($segments)==0)
                            {
                                $stopNo=count($segments);
                            }
                            else
                            {
                                $stopNo=count($segments)-1;
                            }
                            
                            if( isset($filters['stops']) && count($filters['stops']) > 0 && ! in_array($stopNo, $filters['stops'])  ) {
                                $segments = [];
                            }
                            
                        if(count($segments)>=1)
                        {
                            $resultPrices = [];
                            
                            if($stopNo>=3)
                            {
                                $stopNo=3;
                            }

                            foreach($result['totalPriceList'] as $priceIndex => $price) {
                                $total_amount=0;
                                
                                if($priceIndex==0)
                                {
                                    $first_total_amount=0;
                                    $flightidprice=$price['id'];
                                }
                                $resultPrice = [
                                    'id' => $price['id'],
                                    'fareIdentifier' => $price['fareIdentifier'],
                                    'variants' => [],
                                    'sri'=> $price['sri'] ?? '',
                                    'msri'=> $price['msri'] ?? '',
                                ];
                    
                                if( isset($price['fD']) ){
                                    $price['fd'] = $price['fD'];
                                }
                               
                                foreach($price['fd'] as $variant => $value) {
                                   
                                    $resultPrice['variants'][ strtolower($variant) ] = [
                                        'base_price' => $value['fC']['BF'],
                                        'taxes' => $value['fC']['TAF'] ?? 0,
                                        'total_price' => $value['fC']['TF'],
                                        'baggage_checking' => $value['bI']['iB'] ?? 0,
                                        'baggage_cabin' => $value['bI']['cB'] ?? 0,
                                        'refundable' => $value['rT'] ?? false,
                                        'free_meal' => $value['mI'] ?? false,
                                        'class' => $value['cc'] ?? '',
                                    ];
                                    if($variant=="CHILD")
                                    {
                                        $prcount=$child;
                                    }
                                    elseif($variant=="INFANT")
                                    {
                                        $prcount=$infant;
                                    }
                                    else
                                    {
                                        $prcount=$adult;
                                    }
                                    
                                    $total_amount+=$value['fC']['TF']*$prcount;
                                    if($priceIndex==0)
                                    {
                                        $first_total_amount+=$value['fC']['TF']*$prcount;
                                    }
                                }
                                $resultPrice['total_amount']=$total_amount;
                                $resultPrices[] = $resultPrice;
                                
                        }
                            if( isset($filters['pricefrom']) && isset($filters['priceto'])) 
                            {
                                if($filters['pricefrom'] != '' && $filters['priceto'] != '' && $filters['priceto']>0)
                                {
                                    if($first_total_amount < $filters['pricefrom'] || $first_total_amount > $filters['priceto'])
                                    {
                                        continue;
                                    }
                                }
                            }
                            if($stopNo!="" || $stopNo==0)
                            {
                            // Increment the count for each airline name
                                if (!isset($stopCount[$stopNo])) {
                                    $stopCount[$stopNo] = 0;
                                }
                                $stopCount[$stopNo]++;
                            }
                            if($departureTime!='')
                            {
                                // Increment the count for each airline name
                                if (!isset($departureTimeCount[$departureTime])) {
                                    $departureTimeCount[$departureTime] = 0;
                                }
                                $departureTimeCount[$departureTime]++;
                            }
                            
                            
                            if($arrivalTime!='')
                            {
                            // Increment the count for each airline name
                                if (!isset($arrivalTimeCount[$arrivalTime])) {
                                    $arrivalTimeCount[$arrivalTime] = 0;
                                }
                                $arrivalTimeCount[$arrivalTime]++;
                            }
                            
                            
                            if($airlineName!=='')
                            {
                                // Increment the count for each airline name
                                if (!isset($airlineCounts[$airlineName])) {
                                    $airlineCounts[$airlineName] = 0;
                                }
                                $airlineCounts[$airlineName]++;
                            }
                            

                            $multicityIndex=$Cityindex.$from_arr[$Cityindex].$to_arr[$Cityindex];
                            $filtered[$multicityIndex][] = [
                                'index' => $index,
                                'flightidprice'=>$flightidprice,
                                'segments' => $segments,
                                'prices' => $resultPrices
                            ];
                        }
                }
            }
        }
    }
}

$filtered['stops']=$stopCount;
$filtered['departureTime']=$departureTimeCount;
$filtered['arrivalTime']=$arrivalTimeCount;
$filtered['airline']=$airlineCounts;


echo json_encode($filtered, JSON_PRETTY_PRINT);
