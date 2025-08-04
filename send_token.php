<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Adjust path if needed

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $token = $_POST['token'] ?? '';

    // Validate email address
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['message'] = "Invalid email address.";
        header('Location: token_dashboard.php');
        exit;
    }

    // Check if token is provided
    if (empty($token)) {
        $_SESSION['message'] = "Token is missing.";
        header('Location: token_dashboard.php');
        exit;
    }

    $mail = new PHPMailer(true);

    try {
        // SMTP server configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username   = 'dreamteams666666@gmail.com';  // Your Gmail address
$mail->Password   = 'lxhc fdmv lzhj sykt';          // Gmail App Password (NOT your actual Gmail password)

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Email sender and recipient
        $mail->setFrom('dreamteams666666@gmail.com', 'Your Team Name');
        $mail->addAddress($email);

        // Email content
        $mail->isHTML(false);
        $mail->Subject = 'Your Signup Token';
        $mail->Body    = "Hello,\n\nYour signup token is: $token\nPlease use this token to complete your registration.\n\nThank you.";

        $mail->send();

        $_SESSION['message'] = "Token sent successfully to $email";
    } catch (Exception $e) {
        $_SESSION['message'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }

    header('Location: token_dashboard.php');
    exit;
} else {
    http_response_code(405);
    echo "Method Not Allowed";
}
