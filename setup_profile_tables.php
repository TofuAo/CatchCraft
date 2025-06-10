<?php
require_once 'Database Connection.php';

try {
    // Create user profiles table
    $query = "CREATE TABLE IF NOT EXISTS user_profiles (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        bio TEXT,
        location VARCHAR(100),
        experience ENUM('beginner', 'intermediate', 'advanced', 'expert') DEFAULT 'beginner',
        avatar_url VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id),
        UNIQUE KEY unique_user_profile (user_id)
    )";
    
    if (!mysqli_query($conn, $query)) {
        throw new Exception("Error creating user_profiles table: " . mysqli_error($conn));
    }

    // Create user achievements table
    $query = "CREATE TABLE IF NOT EXISTS user_achievements (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        type ENUM('fish', 'learning', 'social', 'master') NOT NULL,
        title VARCHAR(100) NOT NULL,
        description TEXT NOT NULL,
        achieved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )";
    
    if (!mysqli_query($conn, $query)) {
        throw new Exception("Error creating user_achievements table: " . mysqli_error($conn));
    }

    // Insert sample achievements if table is empty
    $query = "SELECT COUNT(*) as count FROM user_achievements";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    
    if ($row['count'] == 0) {
        // Get first user
        $query = "SELECT id FROM users LIMIT 1";
        $result = mysqli_query($conn, $query);
        $user = mysqli_fetch_assoc($result);
        
        if ($user) {
            $achievements = [
                ['type' => 'fish', 'title' => 'First Catch', 'description' => 'Caught your first fish!'],
                ['type' => 'learning', 'title' => 'Knowledge Seeker', 'description' => 'Completed your first learning module'],
                ['type' => 'social', 'title' => 'Community Member', 'description' => 'Made your first post in the community']
            ];
            
            foreach ($achievements as $achievement) {
                $query = "INSERT INTO user_achievements (user_id, type, title, description) VALUES (?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "isss", $user['id'], $achievement['type'], $achievement['title'], $achievement['description']);
                mysqli_stmt_execute($stmt);
            }
        }
    }

    echo json_encode(['success' => true, 'message' => 'Profile tables created successfully']);

} 
catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 