-- Create community posts table
CREATE TABLE IF NOT EXISTS community_posts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    likes INT DEFAULT 0,
    comments INT DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Create post comments table
CREATE TABLE IF NOT EXISTS post_comments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES community_posts(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Create post likes table
CREATE TABLE IF NOT EXISTS post_likes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES community_posts(id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    UNIQUE KEY unique_like (post_id, user_id)
);

-- Insert sample posts
INSERT INTO community_posts (user_id, content) VALUES
(1, 'Just caught my first bass! Any tips for a beginner?'),
(2, 'Beautiful day for fishing at Lake Michigan. The water is perfect!'),
(1, 'Looking for fishing buddies in the Chicago area. Anyone interested?');

-- Insert sample comments
INSERT INTO post_comments (post_id, user_id, content) VALUES
(1, 2, 'Congratulations! Try using soft plastic worms next time.'),
(1, 3, 'Make sure to fish early morning or late evening for best results.'),
(2, 1, 'Looks amazing! What bait are you using?');

-- Insert sample likes
INSERT INTO post_likes (post_id, user_id) VALUES
(1, 2),
(1, 3),
(2, 1),
(2, 3),
(3, 2); 