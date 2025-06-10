<?php
$conn = new mysqli('localhost', 'root', '', 'fishing_application');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Database connected successfully!";
    
    $result = $conn->query("SHOW TABLES LIKE 'users'");
    echo $result->num_rows > 0 
        ? "<br>Users table exists" 
        : "<br>Users table DOES NOT exist";
}