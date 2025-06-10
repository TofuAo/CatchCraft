<?php
require_once 'Database Connection.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'User must be logged in']);
    exit;
}

// Set headers for JSON response
header('Content-Type: application/json');

// Handle file upload
function handleImageUpload() {
    if (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    $file = $_FILES['image'];
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];
    
    // Get file extension
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    // Allowed extensions
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    
    // Validate file
    if ($fileError !== 0) {
        throw new Exception('Error uploading file');
    }
    
    if (!in_array($fileExt, $allowed)) {
        throw new Exception('Invalid file type. Allowed types: ' . implode(', ', $allowed));
    }
    
    if ($fileSize > 5000000) { // 5MB max
        throw new Exception('File size too large. Maximum size: 5MB');
    }
    
    // Generate unique filename
    $newFileName = uniqid('post_', true) . '.' . $fileExt;
    $uploadPath = 'uploads/forum/' . $newFileName;
    
    // Create directory if it doesn't exist
    if (!file_exists('uploads/forum')) {
        mkdir('uploads/forum', 0777, true);
    }
    
    // Move uploaded file
    if (!move_uploaded_file($fileTmpName, $uploadPath)) {
        throw new Exception('Failed to save image');
    }
    
    return $uploadPath;
}

try {
    // Validate post content
    if (!isset($_POST['content']) || empty(trim($_POST['content']))) {
        throw new Exception('Post content is required');
    }

    $content = trim($_POST['content']);
    $userId = $_SESSION['user_id'];
    $imageUrl = null;

    // Handle image upload if present
    if (isset($_FILES['image'])) {
        $imageUrl = handleImageUpload();
    }

    // Insert post into database
    $stmt = $conn->prepare("
        INSERT INTO community_posts (user_id, content, image_url)
        VALUES (?, ?, ?)
    ");
    
    $stmt->execute([$userId, $content, $imageUrl]);
    $postId = $conn->lastInsertId();

    // Get the newly created post with user info
    $stmt = $conn->prepare("
        SELECT 
            p.*,
            u.username,
            u.fullname,
            0 as like_count,
            0 as comment_count,
            0 as user_liked
        FROM community_posts p
        JOIN users u ON p.user_id = u.id
        WHERE p.id = ?
    ");
    
    $stmt->execute([$postId]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    // Format the response
    $response = [
        'success' => true,
        'message' => 'Post created successfully',
        'post' => [
            'id' => $post['id'],
            'content' => $post['content'],
            'image_url' => $post['image_url'],
            'created_at' => $post['created_at'],
            'username' => $post['username'],
            'fullname' => $post['fullname'],
            'likes' => 0,
            'comments' => 0,
            'user_liked' => false
        ]
    ];

    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 