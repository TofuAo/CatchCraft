USE fishing_master;

-- Create learning paths table if not exists
CREATE TABLE IF NOT EXISTS learning_paths (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    difficulty ENUM('beginner', 'intermediate', 'advanced') NOT NULL,
    type VARCHAR(50) NOT NULL,
    estimated_time VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create learning modules table if not exists
CREATE TABLE IF NOT EXISTS learning_modules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    path_id INT,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    type VARCHAR(50) NOT NULL,
    content_url TEXT,
    order_index INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (path_id) REFERENCES learning_paths(id)
);

-- Create user progress table if not exists
CREATE TABLE IF NOT EXISTS user_progress (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    module_id INT,
    completed BOOLEAN DEFAULT FALSE,
    completed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (module_id) REFERENCES learning_modules(id)
);

-- Clear existing data to avoid duplicates
DELETE FROM user_progress;
DELETE FROM learning_modules;
DELETE FROM learning_paths;

-- Insert learning paths
INSERT INTO learning_paths (title, description, difficulty, type, estimated_time) VALUES
('Fishing Basics', 'Learn the fundamentals of fishing including equipment, techniques, and safety. Perfect for beginners who want to start their fishing journey.', 'beginner', 'Course', '2-3 weeks'),
('Intermediate Angling', 'Advanced techniques and strategies for experienced fishers. Take your fishing skills to the next level.', 'intermediate', 'Course', '4-6 weeks'),
('Expert Fishing Mastery', 'Professional-level fishing techniques and advanced strategies. Master the art of fishing.', 'advanced', 'Course', '8-10 weeks');

-- Insert modules for each path
INSERT INTO learning_modules (path_id, title, description, type, content_url, order_index) VALUES
(1, 'Introduction to Fishing', 'Learn about basic fishing equipment and fundamentals', 'Video', 'content/fishing-basics-intro.mp4', 1),
(1, 'Fishing Safety', 'Essential safety guidelines and best practices', 'Article', 'content/fishing-safety.html', 2),
(1, 'Basic Techniques', 'Learn fundamental fishing techniques', 'Interactive', 'content/basic-techniques.html', 3),

(2, 'Advanced Equipment', 'Master specialized fishing equipment', 'Video', 'content/advanced-equipment.mp4', 1),
(2, 'Weather and Fish Behavior', 'Understanding weather patterns and fish behavior', 'Article', 'content/weather-patterns.html', 2),
(2, 'Specialized Methods', 'Learn specialized fishing techniques', 'Interactive', 'content/specialized-methods.html', 3),

(3, 'Professional Techniques', 'Expert-level fishing techniques', 'Video', 'content/pro-techniques.mp4', 1),
(3, 'Advanced Fish Finding', 'Master the art of finding fish', 'Article', 'content/fish-finding.html', 2),
(3, 'Competition Strategies', 'Learn competitive fishing strategies', 'Interactive', 'content/competition.html', 3); 