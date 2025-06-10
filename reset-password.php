<?php
require_once 'Database Connection.php';

$database = new Database();
$conn = $database->getConnection();

$token = $_GET['token'] ?? '';
$error = '';
$success = '';

if (empty($token)) {
    $error = 'Invalid reset token';
} else {
    try {
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #005f73, #001219);
            color: white;
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            background: rgba(255, 255, 255, 0.1);
            padding: 2rem;
            border-radius: 10px;
            backdrop-filter: blur(10px);
            max-width: 400px;
            width: 90%;
        }
        h1 {
            text-align: center;
            color: #94d2bd;
            margin-bottom: 2rem;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #e9d8a6;
        }
        input {
            width: 100%;
            padding: 0.8rem;
            border: none;
            border-radius: 5px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            margin-bottom: 1rem;
        }
        button {
            width: 100%;
            padding: 1rem;
            border: none;
            border-radius: 5px;
            background: #ee9b00;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background: #ca6702;
        }
        .error {
            color: #ff4444;
            text-align: center;
            margin-bottom: 1rem;
        }
        .success {
            color: #00C851;
            text-align: center;
            margin-bottom: 1rem;
        }
        .back-to-login {
            text-align: center;
            margin-top: 1rem;
        }
        .back-to-login a {
            color: #94d2bd;
            text-decoration: none;
        }
        .back-to-login a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Reset Password</h1>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php else: ?>
            <?php if (!$error || $error !== 'Invalid reset token'): ?>
                <form method="POST">
                    <div class="form-group">
                        <label for="password">New Password</label>
                        <input type="password" id="password" name="password" required minlength="8">
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required minlength="8">
                    </div>
                    <button type="submit">Reset Password</button>
                </form>
            <?php endif; ?>
        <?php endif; ?>

        <div class="back-to-login">
            <a href="ptalogin.html">Back to Login</a>
        </div>
    </div>
</body>
</html> 