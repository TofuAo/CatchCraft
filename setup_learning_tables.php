<?php
require_once 'Database Connection.php';

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create learning_paths table
    $conn->exec("CREATE TABLE IF NOT EXISTS learning_paths (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(100) NOT NULL,
        description TEXT,
        difficulty_level ENUM('beginner', 'intermediate', 'advanced') NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Create modules table
    $conn->exec("CREATE TABLE IF NOT EXISTS modules (
        id INT AUTO_INCREMENT PRIMARY KEY,
        path_id INT,
        title VARCHAR(100) NOT NULL,
        content TEXT,
        order_number INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (path_id) REFERENCES learning_paths(id)
    )");
    
    // Create user_progress table
    $conn->exec("CREATE TABLE IF NOT EXISTS user_progress (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        module_id INT,
        completed BOOLEAN DEFAULT FALSE,
        completed_at TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (module_id) REFERENCES modules(id)
    )");
    
    echo json_encode([
        'success' => true,
        'message' => 'Learning tables created successfully'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 