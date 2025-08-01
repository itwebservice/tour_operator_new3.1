<?php

session_start();

//  Facebook Variables--------------------
define('BASEURL', 'https://itoursdemo.co.in/demo-6/crm/');

define('FB_APP_ID', '1136255048306440');

define('FB_APP_SECRET', 'a6829580fca16d45db03ca45ab4c6ebb');

// Callback URL after login with FB
define('FB_REDIRECT_URI', BASEURL . 'view/online_leads/facebook/callback.php');

// Graph API version
define('FB_GRAPH_VERSION', 'v19.0');

// Webhook Verify Token (for security)
define('FB_VERIFY_TOKEN', 'crmhubby_secret_verify_token');

// Database token expiry threshold (optional)
define('ACCESS_TOKEN_EXPIRY_MINUTES', 60);

//  Facebook Variables--------------------
$host = "localhost";
$username = "itourjh2_demo6_u";
$password = "eNPmMwgQ#IVk";
$db_name = "itourjh2_demo6";

$conn = new mysqli($host, $username, $password, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}