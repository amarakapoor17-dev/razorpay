<?php
// Show all errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "Step 1: Starting...<br>";

require 'config.php';
echo "Step 2: Config loaded...<br>";

if (file_exists('vendor/autoload.php')) {
    require 'vendor/autoload.php';
    echo "Step 3: Vendor loaded...<br>";
} else {
    die("<span style='color:red;font-size:20px;'>❌ VENDOR FOLDER MISSING! Run: composer require phpmailer/phpmailer mpdf/mpdf --ignore-platform-reqs</span>");
}

use PHPMailer\PHPMailer\PHPMailer;

$mail = new PHPMailer(true);
echo "Step 4: PHPMailer ready...<br>";

try {
    $mail->isSMTP();
    $mail->Host       = SMTP_HOST;
    $mail->Port       = SMTP_PORT;
    $mail->SMTPAuth   = true;
    $mail->Username   = SMTP_USERNAME;
    $mail->Password   = SMTP_PASSWORD;
    $mail->SMTPSecure = SMTP_SECURE;
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
    $mail->addAddress('sambahdur23@gmail.com', 'Test User'); // CHANGE THIS

    $mail->Subject = 'Test Email';
    $mail->Body    = 'Email working!';

    $mail->send();
    echo "<h1 style='color:green;'>✅ SUCCESS!</h1>";

} catch (Exception $e) {
    echo "<h1 style='color:red;'>❌ FAILED!</h1>";
    echo "<p>" . $mail->ErrorInfo . "</p>";
}
?>