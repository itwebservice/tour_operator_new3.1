<?php
include '../config.php';
global $app_email_id_send;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data safely
    $name    = htmlspecialchars(trim($_POST['name']));
    $email   = htmlspecialchars(trim($_POST['email']));
    $phone   = htmlspecialchars(trim($_POST['phone']));
    $message = htmlspecialchars(trim($_POST['message']));
    
    if (empty($name) || empty($email) || empty($message)) {
        die("All fields are required.");
    }

    // Set recipient email
    $to = $app_email_id_send; // Replace with your actual email

    // Subject
    $subject = "New Contact Form Message from $name";

    // Email body
    $body = "Name: $name\n";
    $body .= "Email: $email\n\n";
    $body .= "Phone No: $phone\n\n";
    $body .= "Message:\n$message\n";

    // Headers
    $headers = "From: $email" . "\r\n" .
        "Reply-To: $email" . "\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";


    // Send email
    if (mail($to, $subject, $body, $headers)) {
        echo "Thank you! Your message has been sent.";
    } else {
        echo "Sorry, your message could not be sent.";
    }
} else {
    echo "Invalid request.";
}
