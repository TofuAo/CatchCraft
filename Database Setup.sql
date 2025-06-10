-- Run these commands in your MySQL admin (phpMyAdmin, MySQL Workbench, or command line)

CREATE DATABASE fishing_master;

USE fishing_master;

-- Users table for login/registration
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Fish species table
CREATE TABLE fish_species (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    scientific_name VARCHAR(100),
    habitat TEXT,
    description TEXT,
    image_path VARCHAR(255)
);

-- Fishing tips table
CREATE TABLE fishing_tips (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    difficulty ENUM('beginner', 'intermediate', 'expert') DEFAULT 'beginner',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Learning pathways table
CREATE TABLE learning_pathways (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    difficulty_level ENUM('beginner', 'intermediate', 'expert') DEFAULT 'beginner',
    prerequisites TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Learning modules table
CREATE TABLE learning_modules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pathway_id INT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    content_type ENUM('video', 'text', 'quiz', 'interactive') NOT NULL,
    content TEXT,
    video_url VARCHAR(255),
    order_number INT NOT NULL,
    estimated_duration INT, -- in minutes
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pathway_id) REFERENCES learning_pathways(id) ON DELETE CASCADE
);

-- User progress table
CREATE TABLE user_progress (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    module_id INT,
    status ENUM('not_started', 'in_progress', 'completed') DEFAULT 'not_started',
    score INT,
    completed_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (module_id) REFERENCES learning_modules(id) ON DELETE CASCADE
);

-- Quiz questions table
CREATE TABLE quiz_questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    module_id INT,
    question TEXT NOT NULL,
    question_type ENUM('multiple_choice', 'true_false', 'open_ended') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (module_id) REFERENCES learning_modules(id) ON DELETE CASCADE
);

-- Quiz answers table
CREATE TABLE quiz_answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT,
    answer_text TEXT NOT NULL,
    is_correct BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (question_id) REFERENCES quiz_questions(id) ON DELETE CASCADE
);

-- Insert sample learning pathway data
INSERT INTO learning_pathways (title, description, difficulty_level) VALUES
('Fishing Fundamentals', 'Master the basics of fishing with this comprehensive beginner''s pathway', 'beginner'),
('Advanced Casting Techniques', 'Learn professional casting methods for different fishing situations', 'intermediate'),
('Fish Species Mastery', 'Become an expert in identifying and catching different fish species', 'intermediate'),
('Pro Angler Skills', 'Advanced techniques and strategies for competitive fishing', 'expert');

-- Insert sample learning modules for Fishing Fundamentals pathway
INSERT INTO learning_modules (pathway_id, title, description, content_type, content, order_number, estimated_duration) VALUES
(1, 'Understanding Fishing Equipment', 'Learn about essential fishing gear and their proper use', 'text', 'In this module, we will cover the basic equipment needed for fishing...', 1, 30),
(1, 'Basic Knots and Rigging', 'Master fundamental fishing knots and rigging techniques', 'video', 'https://example.com/videos/basic-knots', 2, 45),
(1, 'Safety First', 'Essential safety practices for fishing', 'text', 'Safety is paramount in fishing. In this module...', 3, 20),
(1, 'Basic Casting Techniques', 'Learn the fundamentals of casting', 'video', 'https://example.com/videos/basic-casting', 4, 60),
(1, 'Understanding Fish Behavior', 'Learn how fish think and behave', 'text', 'Understanding fish behavior is crucial for successful fishing...', 5, 40),
(1, 'Fundamentals Quiz', 'Test your knowledge of fishing basics', 'quiz', NULL, 6, 15);

-- Insert sample quiz questions
INSERT INTO quiz_questions (module_id, question, question_type) VALUES
(6, 'What is the primary purpose of a fishing rod''s action?', 'multiple_choice'),
(6, 'True or False: You should always store fishing rods in direct sunlight.', 'true_false'),
(6, 'Name three essential items in a basic fishing tackle box.', 'open_ended');

-- Insert sample quiz answers
INSERT INTO quiz_answers (question_id, answer_text, is_correct) VALUES
(1, 'To determine how much the rod bends under pressure', TRUE),
(1, 'To make the rod look cool', FALSE),
(1, 'To increase the price of the rod', FALSE),
(2, 'False', TRUE),
(2, 'True', FALSE);