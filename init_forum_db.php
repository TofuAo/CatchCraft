<?php
require_once 'Database Connection.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Create forum_posts table
    $conn->exec("CREATE TABLE IF NOT EXISTS forum_posts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        username VARCHAR(50),
        content TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        is_admin_post BOOLEAN DEFAULT FALSE,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )");

    // Create forum_likes table
    $conn->exec("CREATE TABLE IF NOT EXISTS forum_likes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        post_id INT NOT NULL,
        user_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (post_id) REFERENCES forum_posts(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id),
        UNIQUE KEY unique_like (post_id, user_id)
    )");

    // Create forum_comments table
    $conn->exec("CREATE TABLE IF NOT EXISTS forum_comments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        post_id INT NOT NULL,
        user_id INT NOT NULL,
        username VARCHAR(50),
        content TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (post_id) REFERENCES forum_posts(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )");

    // Add is_admin_post column if it doesn't exist
    try {
        $conn->query("SELECT is_admin_post FROM forum_posts LIMIT 1");
    } catch (Exception $e) {
        $conn->exec("ALTER TABLE forum_posts ADD COLUMN is_admin_post BOOLEAN DEFAULT FALSE");
    }

    echo "Forum tables created successfully";

} catch (Exception $e) {
    echo "Error creating forum tables: " . $e->getMessage();
}
?> 