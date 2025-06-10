USE fishing_master;

-- Drop existing tables in correct order to avoid foreign key constraints
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS post_comments;
DROP TABLE IF EXISTS post_likes;
DROP TABLE IF EXISTS posts;
SET FOREIGN_KEY_CHECKS = 1;

-- Create posts table
CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Create likes table
CREATE TABLE IF NOT EXISTS post_likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id),
    UNIQUE KEY unique_like (post_id, user_id)
);

-- Create comments table
CREATE TABLE IF NOT EXISTS post_comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Insert sample posts (we'll use user_id 1 for samples, make sure this user exists)
INSERT INTO posts (user_id, title, content) VALUES
(1, 'Best Fishing Spots in Local Lakes', 'I recently discovered some amazing fishing spots at Lake Wilson. The north shore early in the morning has been particularly good for bass fishing. Here are my top recommendations...'),
(1, 'Essential Gear for Beginners', 'If you''re just starting out, here''s the essential gear you need:\n\n1. A reliable rod and reel combo\n2. Various hooks and sinkers\n3. Basic tackle box\n4. Fishing line (10-12 lb test)\n5. Pliers and scissors'),
(1, 'My First Tournament Experience', 'Just participated in my first fishing tournament! The competition was intense but I learned so much. Here are some key takeaways from my experience...'),
(1, 'Seasonal Fishing Tips: Summer Edition', 'As we enter summer, here are some crucial tips for successful fishing:\n\n- Fish early morning or late evening\n- Look for shaded areas\n- Use lighter tackle\n- Focus on deeper waters\n- Keep bait fresh'),
(1, 'Conservation Tips for Responsible Fishing', 'Let''s discuss the importance of conservation in fishing. Here are some practices I follow:\n\n- Proper catch and release techniques\n- Using appropriate gear\n- Following local regulations\n- Protecting spawning areas');

-- Insert sample likes
INSERT INTO post_likes (post_id, user_id) VALUES
(1, 1), -- First post has 1 like
(2, 1), -- Second post has 1 like
(3, 1), -- Third post has 1 like
(4, 1), -- Fourth post has 1 like
(5, 1); -- Fifth post has 1 like

-- Insert sample comments
INSERT INTO post_comments (post_id, user_id, content) VALUES
(1, 1, 'Great spot! I''ve had success there too.'),
(2, 1, 'This list is super helpful for beginners!'),
(3, 1, 'Congratulations on your first tournament!'),
(4, 1, 'These summer tips are spot-on.'),
(5, 1, 'Conservation is so important for sustainable fishing.'); 