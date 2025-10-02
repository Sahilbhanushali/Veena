<?php
header('Content-Type: application/json');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$config = require 'config.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
    exit;
}

// Honeypot check
if (!empty($_POST['website'] ?? '')) {
    echo json_encode(["success" => false, "message" => "Spam detected."]);
    exit;
}

// Collect & sanitize inputs
$name = htmlspecialchars(trim($_POST["name"] ?? ''));
$email = htmlspecialchars(trim($_POST["email"] ?? ''));
$tel = htmlspecialchars(trim($_POST["tel"] ?? ''));
$budget = htmlspecialchars(trim($_POST["budget"] ?? ''));
$message = htmlspecialchars(trim($_POST["message"] ?? ''));

// Validation
if (!$name || !$email || !$tel || !$budget || !$message) {
    echo json_encode(["success" => false, "message" => "All fields are required."]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["success" => false, "message" => "Invalid email address."]);
    exit;
}

if (!preg_match('/^\d{10,15}$/', $tel)) {
    echo json_encode(["success" => false, "message" => "Phone must be 10-15 digits."]);
    exit;
}

if (!preg_match('/^\d+$/', $budget)) {
    echo json_encode(["success" => false, "message" => "Budget must be numbers only."]);
    exit;
}

if (strlen($message) < 10) {
    echo json_encode(["success" => false, "message" => "Message must be at least 10 characters."]);
    exit;
}

// Send email
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = $config['smtp_host'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $config['smtp_user'];
    $mail->Password   = $config['smtp_pass'];
    $mail->SMTPSecure = 'tls';
    $mail->Port       = $config['smtp_port'];

    $mail->setFrom($config['from_email'], $config['from_name']);
    $mail->addReplyTo($email, $name);
    $mail->addAddress($config['from_email']); // recipient

    $mail->isHTML(true);
    $mail->Subject = "New Contact Form Submission - $name";
    $mail->Body    = "
        <strong>Name:</strong> $name <br>
        <strong>Email:</strong> $email <br>
        <strong>Phone:</strong> $tel <br>
        <strong>Budget:</strong> $budget <br>
        <strong>Message:</strong> $message
    ";

    $mail->send();
    echo json_encode(["success" => true, "message" => "Message sent successfully!"]);

} catch (Exception $e) {
    error_log("Mailer Error: {$mail->ErrorInfo}"); // log for debugging
    echo json_encode(["success" => false, "message" => "Failed to send message. Please try again later."]);
}
