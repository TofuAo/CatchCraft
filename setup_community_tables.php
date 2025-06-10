<?php
require_once 'Database Connection.php';

try {
    // Create community posts table
    $query = "CREATE TABLE IF NOT EXISTS community_posts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        title VARCHAR(200) NOT NULL,
        content TEXT NOT NULL,
        image_url VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        likes INT DEFAULT 0,
        comments INT DEFAULT 0,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )";
    
    if (!mysqli_query($conn, $query)) {
        throw new Exception("Error creating community_posts table: " . mysqli_error($conn));
    }

    // Create post likes table
    $query = "CREATE TABLE IF NOT EXISTS post_likes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        post_id INT NOT NULL,
        user_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (post_id) REFERENCES community_posts(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id),
        UNIQUE KEY unique_like (post_id, user_id)
    )";
    
    if (!mysqli_query($conn, $query)) {
        throw new Exception("Error creating post_likes table: " . mysqli_error($conn));
    }

    // Create post comments table
    $query = "CREATE TABLE IF NOT EXISTS post_comments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        post_id INT NOT NULL,
        user_id INT NOT NULL,
        content TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (post_id) REFERENCES community_posts(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )";
    
    if (!mysqli_query($conn, $query)) {
        throw new Exception("Error creating post_comments table: " . mysqli_error($conn));
    }

    // Insert sample post if table is empty
    $query = "SELECT COUNT(*) as count FROM community_posts";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    
    if ($row['count'] == 0) {
        // Get first admin user
        $query = "SELECT id FROM users WHERE is_admin = 1 LIMIT 1";
        $result = mysqli_query($conn, $query);
        $admin = mysqli_fetch_assoc($result);
        
        if ($admin) {
            $query = "INSERT INTO community_posts (user_id, title, content) VALUES 
                (?, 'Welcome to the Fishing Community!', 'Share your fishing experiences, ask questions, and connect with fellow anglers!')";
            
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "i", $admin['id']);
            mysqli_stmt_execute($stmt);
        }
    }

    echo json_encode(['success' => true, 'message' => 'Community tables created successfully']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 