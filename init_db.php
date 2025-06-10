<?php
require_once 'Database Connection.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Create users table
    $conn->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        fullname VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Create user_profiles table
    $conn->exec("CREATE TABLE IF NOT EXISTS user_profiles (
        user_id INT PRIMARY KEY,
        bio TEXT,
        location VARCHAR(100),
        experience ENUM('beginner', 'intermediate', 'advanced', 'expert') DEFAULT 'beginner',
        avatar_url VARCHAR(255),
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    // Create community_posts table
    $conn->exec("CREATE TABLE IF NOT EXISTS community_posts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        title VARCHAR(100) NOT NULL DEFAULT '',
        content TEXT NOT NULL,
        image_url VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    // Create post_comments table
    $conn->exec("CREATE TABLE IF NOT EXISTS post_comments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        post_id INT,
        user_id INT,
        content TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (post_id) REFERENCES community_posts(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    // Create post_likes table
    $conn->exec("CREATE TABLE IF NOT EXISTS post_likes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        post_id INT,
        user_id INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (post_id) REFERENCES community_posts(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        UNIQUE KEY unique_like (post_id, user_id)
    )");

    // Create learning_modules table
    $conn->exec("CREATE TABLE IF NOT EXISTS learning_modules (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(100) NOT NULL,
        description TEXT,
        difficulty ENUM('beginner', 'intermediate', 'advanced', 'expert') DEFAULT 'beginner',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Create user_progress table
    $conn->exec("CREATE TABLE IF NOT EXISTS user_progress (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        module_id INT,
        status ENUM('started', 'in_progress', 'completed') DEFAULT 'started',
        progress INT DEFAULT 0,
        started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        completed_at TIMESTAMP NULL,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (module_id) REFERENCES learning_modules(id) ON DELETE CASCADE,
        UNIQUE KEY unique_progress (user_id, module_id)
    )");

    // Create user_achievements table
    $conn->exec("CREATE TABLE IF NOT EXISTS user_achievements (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        type ENUM('fish', 'learning', 'social', 'master') NOT NULL,
        title VARCHAR(100) NOT NULL,
        description TEXT,
        achieved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    echo "Database tables created successfully";
} catch(PDOException $e) {
    die("Error creating tables: " . $e->getMessage());
}
?> 