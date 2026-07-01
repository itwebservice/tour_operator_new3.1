<?php
include "../../../model/model.php";
$settings = mysqli_fetch_array(mysqlQuery("select client_id from app_settings"));


// Assuming you have the $client_id and API URL defined
$client_id = !empty($settings['client_id']) ? $settings['client_id'] : 0;
$apiurl = "https://itourssupport.in/";

// Prepare the full URL with the client_id as a query parameter (you can adjust this depending on your API endpoint)
$request_url = $apiurl . "view/onboarding_flow/api_crm_status.php"; // Replace with your actual endpoint
$request_url .= "?client_id=" . urlencode($client_id); // Appending client_id to the URL

// Initialize cURL session
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $request_url); // Set the request URL
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Set a timeout for the request (in seconds)

// Optionally, you can set headers if needed (e.g., for authentication or content type)
$headers = [
    "Content-Type: application/json",
    // "Authorization: Bearer YOUR_ACCESS_TOKEN"  // Uncomment this if the API requires an authorization token
];
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

// Execute the cURL request
 $response = curl_exec($ch);

// Check for errors in the request
if ($response === false) {
    echo "cURL Error: " . curl_error($ch);
} else {
    // Handle the response
    $response_data = json_decode($response, true); // Assuming the API returns JSON data
    if ($response_data) {
        // Process the data here
        echo $response;  // Print the response data for debugging
    } else {
        echo "Error: Unable to parse the response.";
    }
}

// Close cURL session
curl_close($ch);
?>
