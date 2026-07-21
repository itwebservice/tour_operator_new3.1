<?php
function callAPI($method, $url, $data){
    $curl = curl_init();
     
    // Initialize cURL session
$curl = curl_init($url);

// Set cURL options
curl_setopt($curl, CURLOPT_POST, 1); // Use POST method
curl_setopt($curl, CURLOPT_POSTFIELDS, $data);  // Attach JSON data
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // Return the response as a string
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification (use carefully)

// Set the request headers
curl_setopt($curl, CURLOPT_HTTPHEADER, [
    'apikey: 1123423b81fdfd-4eb9-40a2-9c97-3a736e306b7e',  // Your API key
    'Content-Type: application/json'  // Specify that we are sending JSON
]);

// Execute cURL request and capture the result
$result = curl_exec($curl);

// Check for errors
if (!$result) {
    die("Connection Failure: " . curl_error($curl)); // If request fails
}

// Close the cURL session
curl_close($curl);

// Output the result
echo $result;
}

$data1 = [
    "searchQuery" => [
        "cabinClass" => "ECONOMY",
        "paxInfo" => [
            "ADULT" => "1",
            "CHILD" => "0",
            "INFANT" => "0"
        ],
        "searchModifiers" => [
            "pft" => "REGULAR",
            "isDirectFlight" => true,
            "isConnectingFlight" => true
        ],
        "routeInfos" => [
            [
                "fromCityOrAirport" => ["code" => "DEL"],
                "toCityOrAirport" => ["code" => "BOM"],
                "travelDate" => "2025-03-26"
            ]
        ]
    ]
];

// Convert PHP array to JSON
$jsonData = json_encode($data1);
  $result = callAPI('POST', 'https://apitest.tripjack.com/fms/v1/air-search-all', $jsonData);
        $resultArr = json_decode($result, true);
      
?>