<?php
$host = 'localhost';
$username = 'root';
$password = '';

try {
    // Create connection without database
    $conn = new PDO("mysql:host=$host", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create database if it doesn't exist
    $sql = "CREATE DATABASE IF NOT EXISTS fishing_master";
    $conn->exec($sql);
    echo "Database created successfully or already exists\n";

    // Select the database
    $conn->exec("USE fishing_master");

    // Create users table if it doesn't exist
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        is_admin BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->exec($sql);
    echo "Users table created successfully\n";

    // Create forum_posts table
    $sql = "CREATE TABLE IF NOT EXISTS forum_posts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        username VARCHAR(50),
        content TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        is_admin_post BOOLEAN DEFAULT FALSE,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )";
    $conn->exec($sql);
    echo "Forum posts table created successfully\n";

    // Create forum_likes table
    $sql = "CREATE TABLE IF NOT EXISTS forum_likes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        post_id INT NOT NULL,
        user_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (post_id) REFERENCES forum_posts(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id),
        UNIQUE KEY unique_like (post_id, user_id)
    )";
    $conn->exec($sql);
    echo "Forum likes table created successfully\n";

    // Create forum_comments table
    $sql = "CREATE TABLE IF NOT EXISTS forum_comments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        post_id INT NOT NULL,
        user_id INT NOT NULL,
        username VARCHAR(50),
        content TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (post_id) REFERENCES forum_posts(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )";
    $conn->exec($sql);
    echo "Forum comments table created successfully\n";

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?> 