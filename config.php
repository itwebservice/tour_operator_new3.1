<?php
ini_set("session.gc_maxlifetime", 3 * 60 * 60);
ini_set('session.gc_maxlifetime', 3 * 60 * 60);

session_start();
date_default_timezone_set('Asia/Kolkata');

set_error_handler("myErrorHandler");
function myErrorHandler($errno, $errstr, $errfile, $errline)
{
    // echo  "<br><br>".$errno."<br>".$errstr."<br>".$errfile."<br>".$errline;
}
$localIP = getHostByName(getHostName());
include 'crm/model/app_settings/dropdown_master.php';

// Create connection
$servername = "localhost";
$username = "root";
$password = "";
$db_name = "tour_operator_new_2_2";

// $db_name = "tour_operator_theme";
global $connection;
$connection = new mysqli($servername, $username, $password, $db_name);
$conn = $connection;

// Check connection
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}


define('BASE_URL', 'http://localhost/itoursdemo/crm/');
define('BASE_URL_B2C', 'http://localhost/itoursdemo/');
mysqli_query($connection, "SET SESSION sql_mode = ''");


// Razorpay API credentials
$apiKey = "rzp_test_adssXOsKMD3X0B";     // Replace with your Razorpay Key
$apiSecret = "6f5zZs6XkbeOgnr0HFDXzZtC"; // Replace with your Razorpay Secret

//**********Global Variables start**************//
global $admin_logo_url, $circle_logo_url, $report_logo_small_url, $app_email_id_send, $app_smtp_host, $app_smtp_port, $app_smtp_password, $app_smtp_method, $app_smtp_status, $app_name, $app_contact_no, $currency_logo, $currency_code;

$admin_logo_url = BASE_URL . 'images/Admin-Area-Logo.png';
$circle_logo_url = BASE_URL . 'images/logo-circle.png';
$report_logo_small_url = BASE_URL . 'images/Receips-Logo-Small.jpg';

global $secret_key, $encrypt_decrypt, $currency, $text_primary_color, $text_secondary_color, $button_color;
$secret_key = "secret_key_for_iTours";

$sq_app_setting = mysqli_fetch_assoc(mysqli_query($connection, "select * from app_settings"));
$app_name = $sq_app_setting['app_name'];
$app_contact_no = $sq_app_setting['app_contact_no'];
$app_smtp_status = $sq_app_setting['app_smtp_status'];
$app_email_id_send = $sq_app_setting['app_email_id'];
$app_smtp_host = $sq_app_setting['app_smtp_host'];
$app_smtp_port = $sq_app_setting['app_smtp_port'];
$app_smtp_password = $sq_app_setting['app_smtp_password'];
$app_smtp_password = $sq_app_setting['app_smtp_password'];
$app_smtp_method = $sq_app_setting['app_smtp_method'];
$currency =  ($_SESSION['session_currency_id']) ? $_SESSION['session_currency_id'] : $sq_app_setting['currency'];
$app_address = $sq_app_setting['app_address'];
$headerMenu = $sq_app_setting['menu_option'];

$sq_color_scheme = mysqli_fetch_assoc(mysqli_query($connection, "select * from b2c_color_scheme where 1 "));
$text_primary_color = $sq_color_scheme['text_primary_color'];
$text_secondary_color = $sq_color_scheme['text_secondary_color'];
$button_color = $sq_color_scheme['button_color'];
$currency_logo_d = mysqli_fetch_assoc(mysqli_query($connection, "SELECT `default_currency`,`currency_code` FROM `currency_name_master` WHERE id=" . $currency));
$currency_code = $currency_logo_d['currency_code'];
$currency_logo = ($currency_logo_d['default_currency']);


include_once __DIR__ . "/classes/themedata.php";
/**
 * @var  ThemeData
 */
$themeData = new ThemeData($connection, BASE_URL_B2C, BASE_URL, $currency);

include_once __DIR__ . "/classes/moduledata.php";
/**
 * @var ModuleData
 */
$moduleData = new ModuleData($connection, BASE_URL_B2C, BASE_URL);

$encrypt_decrypt = new encrypt_decrypt;
class encrypt_decrypt
{
    function fnEncrypt($plaintext, $key)
    {
        // Store the cipher method
        $ciphering = "AES-128-CTR";

        // Use OpenSSl Encryption method
        $iv_length = openssl_cipher_iv_length($ciphering);
        $options = 0;

        // Non-NULL Initialization Vector for encryption
        $encryption_iv = '1234567891011121';

        // Use openssl_encrypt() function to encrypt the data
        $encryption = openssl_encrypt(
            $plaintext,
            $ciphering,
            $key,
            $options,
            $encryption_iv
        );
        return $encryption;
    }
    function fnDecrypt($encryption, $key)
    {
        // Store the cipher method
        $ciphering = "AES-128-CTR";

        // Non-NULL Initialization Vector for decryption
        $decryption_iv = '1234567891011121';
        $options = 0;

        // Use openssl_decrypt() function to decrypt the data
        $decryption = openssl_decrypt(
            $encryption,
            $ciphering,
            $key,
            $options,
            $decryption_iv
        );
        return $decryption;
    }
}

// Userdefined function for php-8 mysqli-query
function mysqlQuery($query)
{

    global $connection;
    return mysqli_query($connection, $query);
}
// Userdefined function for php-8 mysqli_real_escape_string
function mysqlREString($string)
{

    global $connection;
    return mysqli_real_escape_string($connection, $string);
}
function clean($string)
{

    return preg_replace('/[^A-Za-z0-9\-]/', ' ', $string); // Removes special chars.
}
function callAPI($method, $url, $data)
{
    $curl = curl_init();

    switch ($method) {
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
        'apikey: 112342b116e718-0106-4e59-89fd-5cc5c9674300',
        'Content-Type: application/json'
    ));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    // Execute cURL request and get the response
    $result = curl_exec($curl);

    // Check for errors
    if (!$result) {
        // Output the cURL error if request fails
        die("Connection Failure: " . curl_error($curl));
    }

    curl_close($curl);
    return $result;
}

function getReq($key, $fallback = null)
{
    return isset($_REQUEST[$key]) ? $_REQUEST[$key] : $fallback;
}

function timeCategory($hm)
{
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

function minutesToTime($minutes)
{
    $hours = floor($minutes / 60);
    $minutes = $minutes % 60;

    return sprintf('%02d:%02d', $hours, $minutes);
}

function get_cities_dropdown_sugg()
{
    $final_array = array();
    $sq_city = mysqlQuery("select city_name from city_master where active_flag!='Inactive' order by REPLACE(city_name, ' ', '') asc");
    while ($row_city = mysqli_fetch_assoc($sq_city)) {
        array_push($final_array, $row_city['city_name']);
    }
    echo json_encode($final_array);
}

// Define a cache file path
$cacheFile = '/api/cache/airportsDataCache.json';
$cacheTime = 72000; // Cache expiration time in seconds (e.g., 20 hours)

// Check if the cache file exists and is not expired
if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTime) {
    // Read from cache
    $airportsResultdata = json_decode(file_get_contents($cacheFile), true); // Decode JSON to an array

} else {
    // Make the API call (query the database)
    $airportsResult = mysqli_query($connection, "SELECT * FROM airports ");

    // Fetch all rows as an associative array
    $airportsResultdata = []; // Initialize the array to store results
    while ($row = mysqli_fetch_assoc($airportsResult)) {
        $airportsResultdata[] = $row; // Add each row to the results array
    }

    // Save to cache
    if (!is_dir('cache')) {
        mkdir('cache', 0777, true); // Create the cache directory if it doesn't exist
    }

    // Store the data in the cache as a JSON string
    file_put_contents($cacheFile, json_encode($airportsResultdata, true));
}
