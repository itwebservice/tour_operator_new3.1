<?php

include "constants.php";
session_start();

$data = $_GET['data'] ?? '';
if (!$data) die("Missing data");

$decoded = json_decode(base64_decode($data), true);
$access_token = $decoded['access_token'];
$pages = $decoded['pages'];

if (!isset($_SESSION['login_id'])) {
    die("❌ FB login failed.");
}

// check current login user
$login_id = $_SESSION['login_id'];

$userConnect = $conn->prepare("SELECT * FROM roles WHERE id = ?");
$userConnect->bind_param("s", $login_id);
$userConnect->execute();
$result = $userConnect->get_result();

if ($result->num_rows == 0) {
    die("❌ Login user error");
}

// get user id 
$user = $result->fetch_assoc();
$user_id = $user['id'];

// loop through decoded pages
foreach ($pages as $page) {
    $page_id = $page['page_id'];
    $page_name = $page['page_name'];
    $page_token = $page['page_token'];
    $forms = $page['forms'];

    if (!empty($forms)) {
        foreach ($forms as $form) {
            $form_id = $form['id'];
            $formName = $form['name'];
            // Save page + form in facebook_pages table
            $fbPageQuery = $conn->prepare("INSERT INTO facebook_pages (
                user_id,
                page_id,
                page_name,
                form_id,
                form_name
                ) 
                VALUES (?, ?, ?, ?, ?) 
                ON DUPLICATE KEY UPDATE 
                page_name = VALUES(page_name),
                form_name = VALUES(form_name),
                updated_at = CURRENT_TIMESTAMP");

            $fbPageQuery->bind_param(
                "issss",
                $user_id,
                $page_id,
                $page_name,
                $form_id,
                $formName
            );

            $fbPageQuery->execute();
        }

        // Save the latest page access token
        $stmt4 = $conn->prepare("UPDATE roles SET fb_access_token = ? WHERE id = ?");
        $stmt4->bind_param("si", $page_token, $user_id);
        $stmt4->execute();
    }
}

header("Location: " . BASEURL . 'view/online_leads/index.php');
exit;
