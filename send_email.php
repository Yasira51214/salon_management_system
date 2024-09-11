<?php
ini_set('SMTP', 'smtp.yourisp.com');
ini_set('smtp_port', 25);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input data
    $to_email = filter_var($_POST["to_email"], FILTER_SANITIZE_EMAIL);
    $subject = filter_var($_POST["subject"], FILTER_SANITIZE_STRING);
    $message = filter_var($_POST["message"], FILTER_SANITIZE_STRING);

    // Validate email address
    if (!filter_var($to_email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid recipient email address.");
    }

    // Set headers
    $headers = "From: your-email@example.com\r\n";
    $headers .= "Reply-To: your-email@example.com\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    // Send email
    if (mail($to_email, $subject, $message, $headers)) {
        echo "Email sent successfully!";
    } else {
        echo "Failed to send email. Please try again later.";
    }
}
?>
