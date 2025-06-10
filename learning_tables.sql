-- Create learning pathways table
CREATE TABLE IF NOT EXISTS learning_pathways (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    difficulty_level ENUM('Beginner', 'Intermediate', 'Expert') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create learning modules table
CREATE TABLE IF NOT EXISTS learning_modules (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pathway_id INT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    content_type ENUM('video', 'text', 'quiz') NOT NULL,
    content TEXT,
    video_url VARCHAR(255),
    order_number INT NOT NULL,
    estimated_duration INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pathway_id) REFERENCES learning_pathways(id)
);

-- Create quiz questions table
CREATE TABLE IF NOT EXISTS quiz_questions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    module_id INT,
    question TEXT NOT NULL,
    question_type ENUM('multiple_choice', 'true_false', 'open_ended') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (module_id) REFERENCES learning_modules(id)
);

-- Create quiz answers table
CREATE TABLE IF NOT EXISTS quiz_answers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    question_id INT,
    answer_text TEXT NOT NULL,
    is_correct BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (question_id) REFERENCES quiz_questions(id)
);

-- Create user progress table
CREATE TABLE IF NOT EXISTS user_progress (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    module_id INT,
    status ENUM('not_started', 'in_progress', 'completed') DEFAULT 'not_started',
    score INT,
    completed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (module_id) REFERENCES learning_modules(id),
    UNIQUE KEY user_module (user_id, module_id)
);

-- Insert sample learning pathway
INSERT INTO learning_pathways (title, description, difficulty_level) VALUES
('Fishing Basics', 'Learn the fundamentals of fishing and essential techniques', 'Beginner');

-- Insert sample modules
INSERT INTO learning_modules (pathway_id, title, description, content_type, content, order_number, estimated_duration) VALUES
(1, 'Introduction to Fishing', 'Learn the basic concepts and terminology of fishing', 'text', 
'<h3>Welcome to Fishing!</h3><p>In this lesson, you''ll learn the fundamental concepts of fishing and why it''s such a rewarding hobby.</p><h4>Key Topics:</h4><ul><li>What is fishing?</li><li>Different types of fishing</li><li>Basic terminology</li><li>Safety considerations</li></ul><p>Fishing is both an art and a science. It requires patience, skill, and understanding of nature.</p>', 1, 10),

(1, 'Essential Equipment', 'Discover the basic gear needed to start fishing', 'text',
'<h3>Fishing Equipment Basics</h3><p>Learn about the essential gear you need to start fishing.</p><h4>Essential Items:</h4><ul><li>Fishing rods and reels</li><li>Fishing line types</li><li>Hooks and sinkers</li><li>Bait and lures</li></ul><p>Having the right equipment is crucial for a successful fishing experience.</p>', 2, 15),

(1, 'Basic Techniques', 'Master the fundamental fishing techniques', 'text',
'<h3>Basic Fishing Techniques</h3><p>Learn the essential techniques every angler should know.</p><h4>You will learn:</h4><ul><li>Proper casting techniques</li><li>Knot tying</li><li>Bait presentation</li><li>Fish handling</li></ul>', 3, 20);

-- Insert sample quiz
INSERT INTO quiz_questions (module_id, question, question_type) VALUES
(1, 'What is the main purpose of a fishing rod?', 'multiple_choice'),
(1, 'Safety equipment is optional when fishing.', 'true_false');

-- Insert sample quiz answers
INSERT INTO quiz_answers (question_id, answer_text, is_correct) VALUES
(1, 'To cast and control the fishing line', TRUE),
(1, 'To store fishing line', FALSE),
(1, 'To attract fish', FALSE),
(1, 'To measure fish length', FALSE),
(2, 'false', TRUE),
(2, 'true', FALSE); 