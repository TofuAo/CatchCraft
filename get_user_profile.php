<?php
require_once 'session_config.php';
require_once 'Database Connection.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

try {
    $database = new Database();
    $conn = $database->getConnection();

    // Add language column if it doesn't exist
    try {
        $conn->query("SELECT language FROM users LIMIT 1");
    } catch(PDOException $e) {
        $conn->exec("ALTER TABLE users ADD COLUMN language VARCHAR(5) DEFAULT 'en'");
    }

    // Add fullname column if it doesn't exist
    try {
        $conn->query("SELECT fullname FROM users LIMIT 1");
    } catch(PDOException $e) {
        $conn->exec("ALTER TABLE users ADD COLUMN fullname VARCHAR(100)");
    }

    $stmt = $conn->prepare("SELECT username, email, fullname, language FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo json_encode([
            'success' => true,
            'username' => $user['username'],
            'email' => $user['email'],
            'fullname' => $user['fullname'],
            'language' => $user['language'] ?? 'en'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'User not found']);
    }
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?> 