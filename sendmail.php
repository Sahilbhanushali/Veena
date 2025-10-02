<?php
header('Content-Type: application/json');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"] ?? '');
    $email = trim($_POST["email"] ?? '');
    $tel = trim($_POST["tel"] ?? '');
    $budget = trim($_POST["budget"] ?? '');
    $message = trim($_POST["message"] ?? '');

    if(empty($name) || empty($email) || empty($tel) || empty($budget) || empty($message)) {
        echo json_encode(["success" => false, "message" => "All fields are required"]);
        exit;
    }

    if(!preg_match('/^\d{10,15}$/', $tel)) {
        echo json_encode(["success" => false, "message" => "Phone must be proper only."]);
        exit;
    }

    if(!preg_match('/^\d+$/', $budget)) {
        echo json_encode(["success" => false, "message" => "Budget must be numbers only."]);
        exit;
    }

    if(strlen($message) < 10) {
        echo json_encode(["success" => false, "message" => "Message must be at least 10 characters."]);
        exit;
    }

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.hostinger.com'; 
        $mail->SMTPAuth   = true;
        $mail->Username   = 'veena@niilkanth.com'; // your Hostinger email
        $mail->Password   = 'Niilkanth@123';       // email password
        $mail->SMTPSecure = 'ssl';
        $mail->Port       = 465;

        $mail->setFrom('veena@niilkanth.com', $name);
        $mail->addAddress('veena@niilkanth.com');

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
        echo json_encode(["success" => false, "message" => "Mailer Error: {$mail->ErrorInfo}"]);
    }
}
?>
