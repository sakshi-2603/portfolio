<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

// Load .env variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Sanitize inputs
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$subject = filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_SPECIAL_CHARS);
$message = nl2br(filter_input(INPUT_POST, 'message', FILTER_SANITIZE_SPECIAL_CHARS));

if (!$name || !$email || !$subject || !$message) {
    echo json_encode(['success' => false, 'message' => 'Invalid input.']);
    exit;
}

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = $_ENV['MAIL_HOST'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $_ENV['MAIL_USERNAME'];
    $mail->Password   = $_ENV['MAIL_PASSWORD'];
    $mail->SMTPSecure = $_ENV['MAIL_ENCRYPTION'];
    $mail->Port       = $_ENV['MAIL_PORT'];

    $mail->setFrom($email, $name);
    $mail->addAddress($_ENV['MAIL_TO']);
    $mail->Subject = $subject;
    $mail->Body    = $message;
    $mail->isHTML(true);

    $mail->send();

    echo  'Message sent successfully!';
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Mailer Error: ' . $mail->ErrorInfo]);
}
?>
