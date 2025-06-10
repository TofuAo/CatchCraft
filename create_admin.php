<?php
require_once 'Database Connection.php';

try {
    $database = new Database();
    $conn = $database->getConnection();

    // Check if admin user already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute(['admin']);
    
    if ($stmt->rowCount() > 0) {
        echo "Admin user already exists!";
        exit;
    }

    // Create admin user
    $username = 'admin';
    $password = 'Khalid005S!';
    $email = 'admin@masterfisher.com';
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Insert admin user with is_admin flag
    $stmt = $conn->prepare("
        INSERT INTO users (username, email, password, is_admin) 
        VALUES (?, ?, ?, 1)
    ");
    
    if ($stmt->execute([$username, $email, $password_hash])) {
        echo "Admin user created successfully!\n";
        echo "Username: admin\n";
        echo "Password: Khalid005S!\n";
    } else {
        echo "Error creating admin user.";
    }

} catch(PDOException $e) {
    // If the error is about missing is_admin column, add it
    if (strpos($e->getMessage(), "Unknown column 'is_admin'") !== false) {
        try {
            $conn->exec("ALTER TABLE users ADD COLUMN is_admin BOOLEAN DEFAULT FALSE");
            echo "Added is_admin column to users table.\n";
            
            // Try creating admin user again
            $stmt = $conn->prepare("
                INSERT INTO users (username, email, password, is_admin) 
                VALUES (?, ?, ?, 1)
            ");
            
            if ($stmt->execute([$username, $email, $password_hash])) {
                echo "Admin user created successfully!\n";
                echo "Username: admin\n";
                echo "Password: Khalid005S!\n";
            } else {
                echo "Error creating admin user.";
            }
        } catch(PDOException $e2) {
            echo "Database error: " . $e2->getMessage();
        }
    } else {
        echo "Database error: " . $e->getMessage();
    }
}
?> 