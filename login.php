<?php
require_once 'session_config.php';
require_once __DIR__ . '/DatabaseConnection.php';

header('Content-Type: application/json');

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$database = new Database();
$conn = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get input data from either POST or JSON
    if (!empty($_POST)) {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $loginType = $_POST['loginType'] ?? 'user';
    } else {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true) ?? [];
        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';
        $loginType = $data['loginType'] ?? 'user';
    }

    if (empty($username) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Username and password are required']);
        exit;
    }

    try {
        $stmt = $conn->prepare("SELECT id, username, password, is_admin FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && password_verify($password, $row['password'])) {
            // Clear any existing session data
            session_unset();
            session_regenerate_id(true);

            // Set session variables
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['is_admin'] = $row['is_admin'] ? true : false;
            $_SESSION['login_time'] = time();
            
            // Debug session data
            error_log("Session after login: " . print_r($_SESSION, true));
            
            // Determine redirect URL based on login type and user role
            $redirect_url = 'ptahome.html';
            if ($row['is_admin'] && $loginType === 'admin') {
                $redirect_url = 'admin_dashboard.html';
            }
            
            // Log successful login
            error_log("User logged in - ID: {$row['id']}, Username: {$row['username']}, Is Admin: " . ($row['is_admin'] ? 'true' : 'false'));
            
            echo json_encode([
                'success' => true, 
                'message' => 'Login successful',
                'is_admin' => $row['is_admin'],
                'redirect_url' => $redirect_url,
                'session_id' => session_id() // Include session ID for debugging
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
        }
    } catch(PDOException $e) {
        error_log("Login error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error occurred']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

if (isset($error)) {
    error_log('LOGIN ERROR: ' . $error);
}
?>