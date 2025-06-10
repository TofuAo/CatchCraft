DROP TABLE IF EXISTS community_posts;

CREATE TABLE `community_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `likes` int(11) DEFAULT 0,
  `comments` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `community_posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



DROP TABLE IF EXISTS learning_modules;

CREATE TABLE `learning_modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pathway_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `content_type` enum('video','text','quiz') NOT NULL,
  `content` text DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `order_number` int(11) NOT NULL,
  `estimated_duration` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `pathway_id` (`pathway_id`),
  CONSTRAINT `learning_modules_ibfk_1` FOREIGN KEY (`pathway_id`) REFERENCES `learning_path` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO learning_modules VALUES("1","1","Introduction to Fishing","Learn the basic concepts and terminology of fishing","text","<h3>Welcome to Fishing!</h3><p>In this lesson, you\'ll learn the fundamental concepts of fishing and why it\'s such a rewarding hobby.</p><h4>Key Topics:</h4><ul><li>What is fishing?</li><li>Different types of fishing</li><li>Basic terminology</li><li>Safety considerations</li></ul><p>Fishing is both an art and a science. It requires patience, skill, and understanding of nature.</p>","","1","10","2025-05-27 19:48:05");
INSERT INTO learning_modules VALUES("2","1","Essential Equipment","Discover the basic gear needed to start fishing","text","<h3>Fishing Equipment Basics</h3><p>Learn about the essential gear you need to start fishing.</p><h4>Essential Items:</h4><ul><li>Fishing rods and reels</li><li>Fishing line types</li><li>Hooks and sinkers</li><li>Bait and lures</li></ul><p>Having the right equipment is crucial for a successful fishing experience.</p>","","2","15","2025-05-27 19:48:05");
INSERT INTO learning_modules VALUES("3","1","Basic Techniques","Master the fundamental fishing techniques","text","<h3>Basic Fishing Techniques</h3><p>Learn the essential techniques every angler should know.</p><h4>You will learn:</h4><ul><li>Proper casting techniques</li><li>Knot tying</li><li>Bait presentation</li><li>Fish handling</li></ul>","","3","20","2025-05-27 19:48:05");


DROP TABLE IF EXISTS learning_paths;

CREATE TABLE `learning_paths` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `difficulty_level` enum('beginner','intermediate','advanced') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO learning_paths VALUES("1","Fishing Basics","Learn the fundamentals of fishing including equipment, techniques, and safety.","beginner","2025-05-27 19:07:08",1);
INSERT INTO learning_paths VALUES("2","Intermediate Angling","Advanced techniques and strategies for experienced fishers.","intermediate","2025-05-27 19:07:08",1);
INSERT INTO learning_paths VALUES("3","Expert Fishing Mastery","Professional-level fishing techniques and advanced strategies.","advanced","2025-05-27 19:07:08",1);


DROP TABLE IF EXISTS learning_pathways;

CREATE TABLE `learning_pathways` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `difficulty_level` enum('Beginner','Intermediate','Expert') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO learning_pathways VALUES("1","Fishing Basics","Learn the fundamentals of fishing and essential techniques","Beginner","2025-05-27 19:48:05");


DROP TABLE IF EXISTS modules;

CREATE TABLE `modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `path_id` int(11) DEFAULT NULL,
  `title` varchar(100) NOT NULL,
  `content` text DEFAULT NULL,
  `order_number` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `path_id` (`path_id`),
  CONSTRAINT `modules_ibfk_1` FOREIGN KEY (`path_id`) REFERENCES `learning_paths` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO modules VALUES("1","1","Introduction to Fishing","Basic introduction to fishing equipment and terminology.","1","2025-05-27 19:07:08");
INSERT INTO modules VALUES("2","1","Fishing Safety","Essential safety guidelines for fishing.","2","2025-05-27 19:07:08");
INSERT INTO modules VALUES("3","1","Basic Fishing Techniques","Learn fundamental fishing techniques and methods.","3","2025-05-27 19:07:08");
INSERT INTO modules VALUES("4","2","Advanced Equipment Usage","Detailed guide on using advanced fishing equipment.","1","2025-05-27 19:07:08");
INSERT INTO modules VALUES("5","2","Weather and Fish Behavior","Understanding how weather affects fish behavior.","2","2025-05-27 19:07:08");
INSERT INTO modules VALUES("6","2","Specialized Fishing Methods","Learn specialized fishing techniques for different scenarios.","3","2025-05-27 19:07:08");
INSERT INTO modules VALUES("7","3","Professional Techniques","Master professional-level fishing techniques.","1","2025-05-27 19:07:08");
INSERT INTO modules VALUES("8","3","Advanced Fish Finding","Advanced methods for locating specific fish species.","2","2025-05-27 19:07:08");
INSERT INTO modules VALUES("9","3","Competition Strategies","Strategies used in professional fishing competitions.","3","2025-05-27 19:07:08");


DROP TABLE IF EXISTS post_comments;

CREATE TABLE `post_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `post_comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`),
  CONSTRAINT `post_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



DROP TABLE IF EXISTS post_likes;

CREATE TABLE `post_likes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_like` (`post_id`,`user_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `post_likes_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`),
  CONSTRAINT `post_likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO post_likes VALUES("1","1","1","2025-05-27 15:07:22");
INSERT INTO post_likes VALUES("2","2","1","2025-05-27 15:07:22");
INSERT INTO post_likes VALUES("3","3","1","2025-05-27 15:07:22");


DROP TABLE IF EXISTS posts;

CREATE TABLE `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `content` text NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO posts VALUES("1","1","My First Catch!","Just caught my first bass! Here are some tips that helped me...","","2025-05-27 15:07:22","2025-05-27 15:07:22");
INSERT INTO posts VALUES("2","1","Best Fishing Spots in the Area","I\'ve discovered some amazing fishing spots that I\'d like to share...","","2025-05-27 15:07:22","2025-05-27 15:07:22");
INSERT INTO posts VALUES("3","1","Fishing Equipment Review","Here\'s my honest review of the new fishing rod I just bought...","","2025-05-27 15:07:22","2025-05-27 15:07:22");


DROP TABLE IF EXISTS quiz_answers;

CREATE TABLE `quiz_answers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) DEFAULT NULL,
  `answer_text` text NOT NULL,
  `is_correct` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `question_id` (`question_id`),
  CONSTRAINT `quiz_answers_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `quiz_questions` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO quiz_answers VALUES("1","1","To cast and control the fishing line","1","2025-05-27 19:48:05");
INSERT INTO quiz_answers VALUES("2","1","To store fishing line","0","2025-05-27 19:48:05");
INSERT INTO quiz_answers VALUES("3","1","To attract fish","0","2025-05-27 19:48:05");
INSERT INTO quiz_answers VALUES("4","1","To measure fish length","0","2025-05-27 19:48:05");
INSERT INTO quiz_answers VALUES("5","2","false","1","2025-05-27 19:48:05");
INSERT INTO quiz_answers VALUES("6","2","true","0","2025-05-27 19:48:05");


DROP TABLE IF EXISTS quiz_questions;

CREATE TABLE `quiz_questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module_id` int(11) DEFAULT NULL,
  `question` text NOT NULL,
  `question_type` enum('multiple_choice','true_false','open_ended') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `module_id` (`module_id`),
  CONSTRAINT `quiz_questions_ibfk_1` FOREIGN KEY (`module_id`) REFERENCES `learning_modules` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO quiz_questions VALUES("1","1","What is the main purpose of a fishing rod?","multiple_choice","2025-05-27 19:48:05");
INSERT INTO quiz_questions VALUES("2","1","Safety equipment is optional when fishing.","true_false","2025-05-27 19:48:05");


DROP TABLE IF EXISTS site_settings;

CREATE TABLE `site_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO site_settings VALUES("1","support_whatsapp","+1234567890","2025-05-27 19:24:47","2025-05-27 19:24:47");
INSERT INTO site_settings VALUES("2","support_email","support@fishingmaster.com","2025-05-27 19:24:47","2025-05-27 19:24:47");
INSERT INTO site_settings VALUES("3","site_name","Master Fisher","2025-05-27 19:24:47","2025-05-27 19:24:47");
INSERT INTO site_settings VALUES("4","maintenance_mode","false","2025-05-27 19:24:47","2025-05-27 19:24:47");
INSERT INTO site_settings VALUES("5","max_login_attempts","5","2025-05-27 19:24:47","2025-05-27 19:24:47");


DROP TABLE IF EXISTS user_progress;

CREATE TABLE `user_progress` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `module_id` int(11) DEFAULT NULL,
  `completed` tinyint(1) DEFAULT 0,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `module_id` (`module_id`),
  CONSTRAINT `user_progress_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `user_progress_ibfk_2` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



DROP TABLE IF EXISTS users;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_admin` tinyint(1) DEFAULT 0,
  `language` varchar(5) DEFAULT 'en',
  `fullname` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO users VALUES("1","testuser","test@test.com","hashed_password","2025-04-23 11:24:05","0","en","");
INSERT INTO users VALUES("4","khalid","khalidkingez@gmail.com","$2y$10$oHto1W8ZSjivg5iwTuNTLe9Sk8Wf/HTrhHRUNv2a9h38vg5Wh1Wji","2025-04-28 14:18:30","0","en","");
INSERT INTO users VALUES("5","naufal","naufal@gmail.com","$2y$10$opvXzIWo4uM5uESW6bI/7evBv67KBaRGAvd8CVVe7IPaRwV/Y/oWG","2025-05-07 18:47:32","0","en","");
INSERT INTO users VALUES("6","RC23138","rc23138@student.umpsa.edu.my","$2y$10$DG5fbyZoRdRa5aVs8C.5U.AHrGEbgLq/djJ3NbNuB.GmVjNMZeJCy","2025-05-07 18:58:46","0","en","");
INSERT INTO users VALUES("7","sss","rc23139@student.umpsa.edu.my","$2y$10$a/rHa1ocVHem/M0UubVQA.oSc0EQJEkgCqdN2ZklvHre7r95Yy2cS","2025-05-14 18:26:54","1","en","");
INSERT INTO users VALUES("8","TofuAo","ducksayyes005S@gmail.com","$2y$10$4BH2tZx0TbmvNcI/vD0EEuPWunHeSb9vQsgUOl7jmHy7PWB9ung5.","2025-05-27 14:03:09","0","en","");
INSERT INTO users VALUES("9","admin","admin@fishingmaster.com","$2y$10$8KzcO.v5e9zw5RqDB0Nqxe6AYEXrIdAzGHJ9P9q3L8JUlPHcN4n4W","2025-05-27 19:24:47","1","en","");


