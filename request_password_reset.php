<?php
require_once 'Database Connection.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$email = $data['email'] ?? '';

if (empty($email)) {
    echo json_encode(['success' => false, 'message' => 'Email is required']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}

try {
    $database = new Database();
    $conn = $database->getConnection();

    // Check if email exists
    $stmt = $conn->prepare("SELECT id, username FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        // For security, don't reveal if email exists or not
        echo json_encode(['success' => true, 'message' => 'If the email exists, password reset instructions will be sent']);
        exit;
    }

    // Generate reset token
    $token = bin2hex(random_bytes(32));
    $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

    // Create password_resets table if it doesn't exist
    $conn->exec("CREATE TABLE IF NOT EXISTS password_resets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        token VARCHAR(64) NOT NULL,
        expires_at DATETIME NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    // Insert reset token
    $stmt = $conn->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)");
    $stmt->execute([$user['id'], $token, $expiry]);

    // Get site settings for email
    $stmt = $conn->query("SELECT setting_value FROM site_settings WHERE setting_key = 'support_email'");
    $supportEmail = $stmt->fetch(PDO::FETCH_ASSOC)['setting_value'];

    // Send reset email
    $resetLink = "http://{$_SERVER['HTTP_HOST']}/reset_password.php?token=" . $token;
    $to = $email;
    $subject = "Password Reset Request - Master Fisher";
    $headers = "From: $supportEmail\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    $message = "
        <html>
        <head>
            <title>Password Reset Request</title>
        </head>
        <body>
            <h2>Password Reset Request</h2>
            <p>Hello {$user['username']},</p>
            <p>We received a request to reset your password. Click the link below to reset it:</p>
            <p><a href='$resetLink'>Reset Password</a></p>
            <p>This link will expire in 1 hour.</p>
            <p>If you didn't request this, please ignore this email.</p>
            <p>Best regards,<br>Master Fisher Team</p>
        </body>
        </html>
    ";

    mail($to, $subject, $message, $headers);

    echo json_encode(['success' => true, 'message' => 'Password reset instructions have been sent to your email']);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
}
?> 