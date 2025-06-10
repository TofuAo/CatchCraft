<?php
function validateRegistrationData($data) {
    $errors = [];
    
    // Username validation
    if (empty($data['username'])) {
        $errors['username'] = 'Username is required';
    } elseif (strlen($data['username']) < 4) {
        $errors['username'] = 'Username must be at least 4 characters';
    }
    
    // Email validation
    if (empty($data['email'])) {
        $errors['email'] = 'Email is required';
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format';
    }
    
    // Password validation
    if (empty($data['password'])) {
        $errors['password'] = 'Password is required';
    } elseif (strlen($data['password']) < 8) {
        $errors['password'] = 'Password must be at least 8 characters';
    } elseif ($data['password'] !== $data['confirm_password']) {
        $errors['confirm_password'] = 'Passwords do not match';
    }
    
    return $errors;
}

function isUsernameTaken($conn, $username) {
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    return $stmt->fetch() !== false;
}

function isEmailTaken($conn, $email) {
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetch() !== false;
}
?>