-- Create database if it doesn't exist
CREATE DATABASE IF NOT EXISTS fishing_master;

-- Use the database
USE fishing_master;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    is_admin BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create learning paths table
CREATE TABLE IF NOT EXISTS learning_paths (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    difficulty_level ENUM('beginner', 'intermediate', 'advanced') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create modules table
CREATE TABLE IF NOT EXISTS modules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    path_id INT,
    title VARCHAR(100) NOT NULL,
    content TEXT,
    order_number INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (path_id) REFERENCES learning_paths(id)
);

-- Create user progress table
CREATE TABLE IF NOT EXISTS user_progress (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    module_id INT,
    completed BOOLEAN DEFAULT FALSE,
    completed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (module_id) REFERENCES modules(id)
);

-- Insert sample learning paths
INSERT INTO learning_paths (title, description, difficulty_level) VALUES
('Fishing Basics', 'Learn the fundamentals of fishing including equipment, techniques, and safety.', 'beginner'),
('Intermediate Angling', 'Advanced techniques and strategies for experienced fishers.', 'intermediate'),
('Expert Fishing Mastery', 'Professional-level fishing techniques and advanced strategies.', 'advanced');

-- Insert modules for Fishing Basics
INSERT INTO modules (path_id, title, content, order_number) VALUES
(1, 'Introduction to Fishing', 'Basic introduction to fishing equipment and terminology.', 1),
(1, 'Fishing Safety', 'Essential safety guidelines for fishing.', 2),
(1, 'Basic Fishing Techniques', 'Learn fundamental fishing techniques and methods.', 3);

-- Insert modules for Intermediate Angling
INSERT INTO modules (path_id, title, content, order_number) VALUES
(2, 'Advanced Equipment Usage', 'Detailed guide on using advanced fishing equipment.', 1),
(2, 'Weather and Fish Behavior', 'Understanding how weather affects fish behavior.', 2),
(2, 'Specialized Fishing Methods', 'Learn specialized fishing techniques for different scenarios.', 3);

-- Insert modules for Expert Fishing Mastery
INSERT INTO modules (path_id, title, content, order_number) VALUES
(3, 'Professional Techniques', 'Master professional-level fishing techniques.', 1),
(3, 'Advanced Fish Finding', 'Advanced methods for locating specific fish species.', 2),
(3, 'Competition Strategies', 'Strategies used in professional fishing competitions.', 3); 