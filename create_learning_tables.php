<?php
require_once 'Database Connection.php';

$database = new Database();
$conn = $database->getConnection();

try {
    // Create learning paths table
    $sql = "CREATE TABLE IF NOT EXISTS learning_paths (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        difficulty_level ENUM('beginner', 'intermediate', 'advanced') DEFAULT 'beginner',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->exec($sql);

    // Create modules table
    $sql = "CREATE TABLE IF NOT EXISTS modules (
        id INT AUTO_INCREMENT PRIMARY KEY,
        path_id INT,
        title VARCHAR(255) NOT NULL,
        content TEXT,
        content_type ENUM('text', 'video', 'quiz') DEFAULT 'text',
        video_url VARCHAR(255),
        order_number INT NOT NULL,
        estimated_duration INT DEFAULT 30,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (path_id) REFERENCES learning_paths(id) ON DELETE CASCADE
    )";
    $conn->exec($sql);

    // Create user progress table
    $sql = "CREATE TABLE IF NOT EXISTS user_progress (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        module_id INT,
        status ENUM('not_started', 'in_progress', 'completed') DEFAULT 'not_started',
        score INT,
        completed_at TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE
    )";
    $conn->exec($sql);

    echo json_encode([
        'success' => true,
        'message' => 'Learning tables created successfully'
    ]);

} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error creating tables: ' . $e->getMessage()
    ]);
}
?> 