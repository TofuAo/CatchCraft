<?php
require_once 'Database Connection.php';

$token = $_GET['token'] ?? '';
$error = '';
$success = '';

if (empty($token)) {
    $error = 'Invalid reset token';
} else {
    try {
        $database = new Database();
        $conn = $database->getConnection();

        // Check if token exists and is valid
        $stmt = $conn->prepare("
            SELECT pr.user_id, u.username 
            FROM password_resets pr 
            JOIN users u ON pr.user_id = u.id 
            WHERE pr.token = ? AND pr.expires_at > NOW()
            LIMIT 1
        ");
        $stmt->execute([$token]);
        $reset = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$reset) {
            $error = 'Invalid or expired reset token';
        }
    } catch(PDOException $e) {
        $error = 'Database error';
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($token)) {
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($password) || empty($confirm_password)) {
        $error = 'Both password fields are required';
    } else if ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } else if (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long';
    } else {
        try {
            // Update password
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt->execute([$hashed_password, $reset['user_id']]);

            // Delete used token
            $stmt = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
            $stmt->execute([$token]);

            $success = 'Password has been reset successfully. You can now login with your new password.';
        } catch(PDOException $e) {
            $error = 'Error resetting password';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Master Fisher</title>
    <link rel="stylesheet" href="ptastyle.css">
    <style>
        .reset-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 30px;
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }
        .reset-container h2 {
            color: #0077cc;
            margin-bottom: 20px;
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #666;
        }
        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        .reset-btn {
            width: 100%;
            padding: 12px;
            background-color: #0077cc;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }
        .error-message {
            color: #dc3545;
            margin: 10px 0;
            text-align: center;
        }
        .success-message {
            color: #28a745;
            margin: 10px 0;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <h2>Reset Password</h2>
        
        <?php if ($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success-message">
                <?php echo htmlspecialchars($success); ?>
                <p><a href="ptalogin.html">Return to Login</a></p>
            </div>
        <?php else: ?>
            <?php if (!$error): ?>
                <form method="POST">
                    <div class="form-group">
                        <label for="password">New Password</label>
                        <input type="password" id="password" name="password" required 
                               minlength="8" placeholder="Enter new password">
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" 
                               required minlength="8" placeholder="Confirm new password">
                    </div>
                    <button type="submit" class="reset-btn">Reset Password</button>
                </form>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html> 