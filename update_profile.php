<?php
require_once 'Database Connection.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$email = $_POST['email'] ?? '';
$fullname = $_POST['fullname'] ?? '';
$language = $_POST['language'] ?? 'en';

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

    // Check if email is already used by another user
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->execute([$email, $_SESSION['user_id']]);
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => false, 'message' => 'Email is already in use']);
        exit;
    }

    // Update user profile
    $stmt = $conn->prepare("UPDATE users SET email = ?, fullname = ?, language = ? WHERE id = ?");
    $stmt->execute([$email, $fullname, $language, $_SESSION['user_id']]);

    echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?> 