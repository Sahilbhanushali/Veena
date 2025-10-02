<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // if using Composer
// or require 'PHPMailer/src/PHPMailer.php'; and others if manual

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $tel = trim($_POST["tel"]);
    $budget = trim($_POST["budget"]);
    $message = trim($_POST["message"]);

    if(empty($name) || empty($email) || empty($tel) || empty($budget) || empty($message)) {
        echo json_encode(["success" => false, "message" => "All fields are required"]);
        exit;
    }

    $mail = new PHPMailer(true);

    try {
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host       = 'smtp.hostinger.com'; 
        $mail->SMTPAuth   = true;
        $mail->Username   = 'veena@niilkanth.com'; // your Hostinger email
        $mail->Password   = 'Niilkanth@123'; // email password
        $mail->SMTPSecure = 'ssl'; // or 'tls'
        $mail->Port       = 465;

        // Sender & recipient
        $mail->setFrom('veena@niilkanth.com', $name);
        $mail->addAddress('veena@niilkanth.com'); 

        // Content
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
