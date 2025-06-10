<?php
require_once 'vendor/phpmailer/phpmailer/src/Exception.php';
require_once 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once 'vendor/phpmailer/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

try {
    $mail = new PHPMailer(true);

    // Server settings
    $mail->SMTPDebug = 2;                      // Enable verbose debug output
    $mail->isSMTP();                           // Send using SMTP
    $mail->Host       = 'smtp.gmail.com';      // Gmail SMTP server
    $mail->SMTPAuth   = true;                  // Enable SMTP authentication
    $mail->Username   = 'khalidabdulkadir005@gmail.com'; // Your Gmail address
    $mail->Password   = 'yvxe aqxm yvxw aqxm';  // Your Gmail App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  // Enable TLS encryption
    $mail->Port       = 587;                   // TCP port to connect to

    // Recipients
    $mail->setFrom('khalidabdulkadir005@gmail.com', 'Master Fisher Support');
    $mail->addAddress('khalidabdulkadir005@gmail.com'); // Add a recipient

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Test Email from Master Fisher';
    $mail->Body    = '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <h2 style="color: #005f73;">Test Email</h2>
            <p>This is a test email from the Master Fisher password reset system.</p>
            <p>If you received this email, the email system is working correctly!</p>
            <br>
            <p style="color: #666; font-size: 14px;">
                Best regards,<br>
                Master Fisher Team
            </p>
        </div>
    ';
    $mail->AltBody = 'This is a test email from the Master Fisher password reset system. If you received this email, the email system is working correctly!';

    $mail->send();
    echo "Test email sent successfully!";
} catch (Exception $e) {
    echo "Error sending test email: {$mail->ErrorInfo}";
}
?> 