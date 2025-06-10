<?php
require_once 'Database Connection.php';
require_once 'email_config.php';

// Include PHPMailer classes manually
require_once 'vendor/phpmailer/phpmailer/src/Exception.php';
require_once 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once 'vendor/phpmailer/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

$database = new Database();
$conn = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';

    if (empty($email)) {
        echo json_encode(['success' => false, 'message' => 'Email is required']);
        exit;
    }

    try {
        // Check if email exists
        $stmt = $conn->prepare("SELECT id, username FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            // Don't reveal if email exists or not for security
            echo json_encode(['success' => true, 'message' => 'If your email is registered, you will receive reset instructions shortly.']);
            exit;
        }

        // Generate unique token
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Store reset token in database
        $stmt = $conn->prepare("
            INSERT INTO password_resets (user_id, token, expires_at) 
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$user['id'], $token, $expires]);

        // Create reset link
        $resetLink = "http://" . $_SERVER['HTTP_HOST'] . "/reset-password.php?token=" . $token;

        // Configure PHPMailer
        $mail = new PHPMailer(true);
        
        try {
            // Server settings
            $mail->SMTPDebug = 0;                      // Disable debug output
            $mail->isSMTP();                           // Send using SMTP
            $mail->Host       = SMTP_HOST;             // SMTP server
            $mail->SMTPAuth   = true;                  // Enable SMTP authentication
            $mail->Username   = SMTP_USERNAME;          // SMTP username
            $mail->Password   = SMTP_PASSWORD;          // SMTP password
            $mail->SMTPSecure = SMTP_SECURE;           // Enable TLS encryption
            $mail->Port       = SMTP_PORT;             // TCP port to connect to

            // Recipients
            $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
            $mail->addAddress($email);                 // Add recipient

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request - Master Fisher';
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                    <h2 style='color: #005f73;'>Password Reset Request</h2>
                    <p>Hello {$user['username']},</p>
                    <p>We received a request to reset your password. Click the button below to set a new password:</p>
                    <p style='text-align: center; margin: 30px 0;'>
                        <a href='{$resetLink}' 
                           style='background-color: #ee9b00; 
                                  color: white; 
                                  padding: 12px 30px; 
                                  text-decoration: none; 
                                  border-radius: 5px; 
                                  display: inline-block;'>
                            Reset Password
                        </a>
                    </p>
                    <p>Or copy and paste this link in your browser:</p>
                    <p style='background-color: #f5f5f5; padding: 10px; border-radius: 5px;'>
                        {$resetLink}
                    </p>
                    <p><strong>This link will expire in 1 hour.</strong></p>
                    <p>If you didn't request this reset, please ignore this email.</p>
                    <br>
                    <p style='color: #666; font-size: 14px;'>
                        Best regards,<br>
                        Master Fisher Team
                    </p>
                </div>
            ";
            $mail->AltBody = "
                Hello {$user['username']},
                
                We received a request to reset your password. Click the link below to set a new password:
                
                {$resetLink}
                
                This link will expire in 1 hour.
                
                If you didn't request this reset, please ignore this email.
                
                Best regards,
                Master Fisher Team
            ";

            $mail->send();
            echo json_encode(['success' => true, 'message' => 'If your email is registered, you will receive reset instructions shortly.']);

        } catch (Exception $e) {
            error_log("Email sending failed: " . $mail->ErrorInfo);
            echo json_encode(['success' => false, 'message' => 'Failed to send reset instructions. Please try again later.']);
        }

    } catch (Exception $e) {
        error_log("Password reset error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again later.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
