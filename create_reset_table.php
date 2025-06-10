<?php
require_once 'Database Connection.php';

try {
    $database = new Database();
    $conn = $database->getConnection();

    // Create password_resets table
    $sql = "CREATE TABLE IF NOT EXISTS password_resets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        token VARCHAR(64) NOT NULL,
        expires_at DATETIME NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX (token),
        INDEX (expires_at)
    )";

    $conn->exec($sql);
    echo "Password resets table created successfully!";

} catch(PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
?> 