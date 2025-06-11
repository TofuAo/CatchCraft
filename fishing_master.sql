-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 11, 2025 at 09:08 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fishing_master`
--

-- --------------------------------------------------------

--
-- Table structure for table `community_posts`
--

CREATE TABLE `community_posts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT '',
  `content` text NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `likes` int(11) DEFAULT 0,
  `comments` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `community_posts`
--

INSERT INTO `community_posts` (`id`, `user_id`, `title`, `content`, `image_url`, `created_at`, `updated_at`, `likes`, `comments`) VALUES
(6, 1, 'Test Post', 'This is a test post for the admin dashboard.', NULL, '2025-06-07 12:57:19', '2025-06-07 12:57:19', 0, 0),
(7, 1, 'Second Post', 'This is another example post.', NULL, '2025-06-07 12:59:55', '2025-06-07 12:59:55', 0, 0),
(8, 1, 'Fishing Tips', 'Always check your line for knots before casting.', NULL, '2025-06-07 12:59:55', '2025-06-07 12:59:55', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `fish`
--

CREATE TABLE `fish` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `forum_comments`
--

CREATE TABLE `forum_comments` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `forum_comments`
--

INSERT INTO `forum_comments` (`id`, `post_id`, `user_id`, `username`, `content`, `created_at`) VALUES
(1, 3, 7, NULL, 'hi', '2025-06-07 14:11:09');

-- --------------------------------------------------------

--
-- Table structure for table `forum_likes`
--

CREATE TABLE `forum_likes` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `forum_likes`
--

INSERT INTO `forum_likes` (`id`, `post_id`, `user_id`, `created_at`) VALUES
(1, 2, 7, '2025-06-02 14:31:29'),
(2, 3, 7, '2025-06-02 14:36:02'),
(4, 4, 7, '2025-06-07 14:13:49'),
(5, 1, 7, '2025-06-07 14:13:52');

-- --------------------------------------------------------

--
-- Table structure for table `forum_posts`
--

CREATE TABLE `forum_posts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_admin_post` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `forum_posts`
--

INSERT INTO `forum_posts` (`id`, `user_id`, `username`, `content`, `created_at`, `is_admin_post`) VALUES
(1, 7, 'sss', 'AS', '2025-06-02 14:31:15', 1),
(2, 7, 'sss', 'HELLO WORLD\r\n', '2025-06-02 14:31:27', 1),
(3, 7, 'sss', 'hello\r\n', '2025-06-02 14:35:51', 1),
(4, 7, 'sss', 'hello', '2025-06-02 14:36:29', 1);

-- --------------------------------------------------------

--
-- Table structure for table `learning_modules`
--

CREATE TABLE `learning_modules` (
  `id` int(11) NOT NULL,
  `pathway_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `content_type` enum('video','text','quiz') NOT NULL,
  `content` text DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `order_number` int(11) NOT NULL,
  `estimated_duration` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `path_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `learning_modules`
--

INSERT INTO `learning_modules` (`id`, `pathway_id`, `title`, `description`, `content_type`, `content`, `video_url`, `order_number`, `estimated_duration`, `created_at`, `path_id`) VALUES
(1, 1, 'Introduction to Fishing', 'Learn the basic concepts and terminology of fishing', 'text', '<h3>Welcome to Fishing!</h3><p>In this lesson, you\'ll learn the fundamental concepts of fishing and why it\'s such a rewarding hobby.</p><h4>Key Topics:</h4><ul><li>What is fishing?</li><li>Different types of fishing</li><li>Basic terminology</li><li>Safety considerations</li></ul><p>Fishing is both an art and a science. It requires patience, skill, and understanding of nature.</p>', NULL, 1, 10, '2025-05-27 11:48:05', NULL),
(2, 1, 'Essential Equipment', 'Discover the basic gear needed to start fishing', 'text', '<h3>Fishing Equipment Basics</h3><p>Learn about the essential gear you need to start fishing.</p><h4>Essential Items:</h4><ul><li>Fishing rods and reels</li><li>Fishing line types</li><li>Hooks and sinkers</li><li>Bait and lures</li></ul><p>Having the right equipment is crucial for a successful fishing experience.</p>', NULL, 2, 15, '2025-05-27 11:48:05', NULL),
(3, 1, 'Basic Techniques', 'Master the fundamental fishing techniques', 'text', '<h3>Basic Fishing Techniques</h3><p>Learn the essential techniques every angler should know.</p><h4>You will learn:</h4><ul><li>Proper casting techniques</li><li>Knot tying</li><li>Bait presentation</li><li>Fish handling</li></ul>', NULL, 3, 20, '2025-05-27 11:48:05', NULL),
(4, NULL, 'Intro to Fishing', 'Learn the basics', '', NULL, NULL, 1, NULL, '2025-06-03 07:01:49', 1),
(5, NULL, 'Casting Techniques', 'How to cast', 'video', NULL, NULL, 2, NULL, '2025-06-03 07:01:49', 1),
(6, NULL, 'Advanced Lures', 'Using advanced lures', '', NULL, NULL, 1, NULL, '2025-06-03 07:01:49', 3);

-- --------------------------------------------------------

--
-- Table structure for table `learning_paths`
--

CREATE TABLE `learning_paths` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `difficulty_level` enum('beginner','intermediate','advanced') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `learning_paths`
--

INSERT INTO `learning_paths` (`id`, `title`, `description`, `difficulty_level`, `created_at`, `is_active`) VALUES
(1, 'Fishing Basics', 'Learn the fundamentals of fishing including equipment, techniques, and safety.', 'beginner', '2025-05-27 11:07:08', 1),
(2, 'Intermediate Angling', 'Advanced techniques and strategies for experienced fishers.', 'intermediate', '2025-05-27 11:07:08', 1),
(3, 'Expert Fishing Mastery', 'Professional-level fishing techniques and advanced strategies.', 'advanced', '2025-05-27 11:07:08', 1),
(9, 'Fishing Basics', 'Learn the fundamentals of fishing including equipm...', 'beginner', '2025-06-02 14:32:54', 1);

-- --------------------------------------------------------

--
-- Table structure for table `learning_pathways`
--

CREATE TABLE `learning_pathways` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `difficulty_level` enum('Beginner','Intermediate','Expert') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `learning_pathways`
--

INSERT INTO `learning_pathways` (`id`, `title`, `description`, `difficulty_level`, `created_at`) VALUES
(1, 'Fishing Basics', 'Learn the fundamentals of fishing and essential techniques', 'Beginner', '2025-05-27 11:48:05');

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE `modules` (
  `id` int(11) NOT NULL,
  `path_id` int(11) DEFAULT NULL,
  `title` varchar(100) NOT NULL,
  `content` text DEFAULT NULL,
  `order_number` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `modules`
--

INSERT INTO `modules` (`id`, `path_id`, `title`, `content`, `order_number`, `created_at`) VALUES
(1, 1, 'Introduction to Fishing', 'Basic introduction to fishing equipment and terminology.', 1, '2025-05-27 11:07:08'),
(2, 1, 'Fishing Safety', 'Essential safety guidelines for fishing.', 2, '2025-05-27 11:07:08'),
(3, 1, 'Basic Fishing Techniques', 'Learn fundamental fishing techniques and methods.', 3, '2025-05-27 11:07:08'),
(4, 2, 'Advanced Equipment Usage', 'Detailed guide on using advanced fishing equipment.', 1, '2025-05-27 11:07:08'),
(5, 2, 'Weather and Fish Behavior', 'Understanding how weather affects fish behavior.', 2, '2025-05-27 11:07:08'),
(6, 2, 'Specialized Fishing Methods', 'Learn specialized fishing techniques for different scenarios.', 3, '2025-05-27 11:07:08'),
(7, 3, 'Professional Techniques', 'Master professional-level fishing techniques.', 1, '2025-05-27 11:07:08'),
(8, 3, 'Advanced Fish Finding', 'Advanced methods for locating specific fish species.', 2, '2025-05-27 11:07:08'),
(9, 3, 'Competition Strategies', 'Strategies used in professional fishing competitions.', 3, '2025-05-27 11:07:08');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_id`, `token`, `expires_at`, `created_at`) VALUES
(1, 7, '458b9a5c4acbc2be511d60058d6870aff357988fc03bc75cd1629a2d9e4c6063', '2025-06-03 10:40:41', '2025-06-03 07:40:41'),
(2, 7, '91ea896c11c31d33f1e51dc73df2a18aef1a5e6f1d99ed785ea65d8fe3b3c096', '2025-06-03 10:40:42', '2025-06-03 07:40:42'),
(3, 7, '39dab67a029d9e496fd26f7d8aa4b97ed8c567b22671cfad840265381aaa7fef', '2025-06-03 10:40:43', '2025-06-03 07:40:43'),
(4, 7, 'ddf02db458786854c5ba4b5c6dd2612610efefe41385192653bea1656772d106', '2025-06-03 10:40:45', '2025-06-03 07:40:45');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `content` text NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `user_id`, `title`, `content`, `image_url`, `created_at`, `updated_at`) VALUES
(1, 1, 'My First Catch!', 'Just caught my first bass! Here are some tips that helped me...', NULL, '2025-05-27 07:07:22', '2025-05-27 07:07:22'),
(2, 1, 'Best Fishing Spots in the Area', 'I\'ve discovered some amazing fishing spots that I\'d like to share...', NULL, '2025-05-27 07:07:22', '2025-05-27 07:07:22'),
(3, 1, 'Fishing Equipment Review', 'Here\'s my honest review of the new fishing rod I just bought...', NULL, '2025-05-27 07:07:22', '2025-05-27 07:07:22'),
(4, 7, 'cat is scarying people', 'sda', NULL, '2025-06-07 12:09:43', '2025-06-07 12:09:43'),
(5, 7, 'dsa', 'dasd', NULL, '2025-06-07 12:10:16', '2025-06-07 12:10:16'),
(6, 7, 'hello', 'handsome', NULL, '2025-06-07 12:24:23', '2025-06-07 12:24:23'),
(7, 7, 'cat is scarying people', 'dsad', NULL, '2025-06-07 12:27:15', '2025-06-07 12:27:15');

-- --------------------------------------------------------

--
-- Table structure for table `post_comments`
--

CREATE TABLE `post_comments` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `post_likes`
--

CREATE TABLE `post_likes` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `post_likes`
--

INSERT INTO `post_likes` (`id`, `post_id`, `user_id`, `created_at`) VALUES
(1, 1, 1, '2025-05-27 07:07:22'),
(2, 2, 1, '2025-05-27 07:07:22'),
(3, 3, 1, '2025-05-27 07:07:22');

-- --------------------------------------------------------

--
-- Table structure for table `quiz_answers`
--

CREATE TABLE `quiz_answers` (
  `id` int(11) NOT NULL,
  `question_id` int(11) DEFAULT NULL,
  `answer_text` text NOT NULL,
  `is_correct` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz_answers`
--

INSERT INTO `quiz_answers` (`id`, `question_id`, `answer_text`, `is_correct`, `created_at`) VALUES
(1, 1, 'To cast and control the fishing line', 1, '2025-05-27 11:48:05'),
(2, 1, 'To store fishing line', 0, '2025-05-27 11:48:05'),
(3, 1, 'To attract fish', 0, '2025-05-27 11:48:05'),
(4, 1, 'To measure fish length', 0, '2025-05-27 11:48:05'),
(5, 2, 'false', 1, '2025-05-27 11:48:05'),
(6, 2, 'true', 0, '2025-05-27 11:48:05');

-- --------------------------------------------------------

--
-- Table structure for table `quiz_questions`
--

CREATE TABLE `quiz_questions` (
  `id` int(11) NOT NULL,
  `module_id` int(11) DEFAULT NULL,
  `question` text NOT NULL,
  `question_type` enum('multiple_choice','true_false','open_ended') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz_questions`
--

INSERT INTO `quiz_questions` (`id`, `module_id`, `question`, `question_type`, `created_at`) VALUES
(1, 1, 'What is the main purpose of a fishing rod?', 'multiple_choice', '2025-05-27 11:48:05'),
(2, 1, 'Safety equipment is optional when fishing.', 'true_false', '2025-05-27 11:48:05');

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`id`, `setting_key`, `setting_value`, `created_at`, `updated_at`) VALUES
(1, 'support_whatsapp', '+1234567890', '2025-05-27 11:24:47', '2025-05-27 11:24:47'),
(2, 'support_email', 'support@fishingmaster.com', '2025-05-27 11:24:47', '2025-05-27 11:24:47'),
(3, 'site_name', 'Master Fisher', '2025-05-27 11:24:47', '2025-05-27 11:24:47'),
(4, 'maintenance_mode', 'false', '2025-05-27 11:24:47', '2025-05-27 11:24:47'),
(5, 'max_login_attempts', '5', '2025-05-27 11:24:47', '2025-05-27 11:24:47');

-- --------------------------------------------------------

--
-- Table structure for table `tools`
--

CREATE TABLE `tools` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_admin` tinyint(1) DEFAULT 0,
  `language` varchar(5) DEFAULT 'en',
  `fullname` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`, `is_admin`, `language`, `fullname`) VALUES
(1, 'testuser', 'test@test.com', 'hashed_password', '2025-04-23 03:24:05', 0, 'en', NULL),
(5, 'naufal', 'naufal@gmail.com', '$2y$10$opvXzIWo4uM5uESW6bI/7evBv67KBaRGAvd8CVVe7IPaRwV/Y/oWG', '2025-05-07 10:47:32', 0, 'en', NULL),
(6, 'RC23138', 'rc23138@student.umpsa.edu.my', '$2y$10$DG5fbyZoRdRa5aVs8C.5U.AHrGEbgLq/djJ3NbNuB.GmVjNMZeJCy', '2025-05-07 10:58:46', 0, 'en', NULL),
(7, 'sss', 'rc23139@student.umpsa.edu.my', '$2y$10$a/rHa1ocVHem/M0UubVQA.oSc0EQJEkgCqdN2ZklvHre7r95Yy2cS', '2025-05-14 10:26:54', 1, 'en', ''),
(8, 'TofuAo', 'ducksayyes005S@gmail.com', '$2y$10$4BH2tZx0TbmvNcI/vD0EEuPWunHeSb9vQsgUOl7jmHy7PWB9ung5.', '2025-05-27 06:03:09', 0, 'en', NULL),
(9, 'admin', 'admin@fishingmaster.com', '$2y$10$8KzcO.v5e9zw5RqDB0Nqxe6AYEXrIdAzGHJ9P9q3L8JUlPHcN4n4W', '2025-05-27 11:24:47', 1, 'en', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_achievements`
--

CREATE TABLE `user_achievements` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('fish','learning','social','master') NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `achieved_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_profiles`
--

CREATE TABLE `user_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `bio` text DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `experience` enum('beginner','intermediate','advanced','expert') DEFAULT 'beginner',
  `avatar_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_progress`
--

CREATE TABLE `user_progress` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `module_id` int(11) DEFAULT NULL,
  `status` enum('not_started','in_progress','completed') DEFAULT 'not_started',
  `completed` tinyint(1) DEFAULT 0,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `community_posts`
--
ALTER TABLE `community_posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `fish`
--
ALTER TABLE `fish`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `forum_comments`
--
ALTER TABLE `forum_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `forum_likes`
--
ALTER TABLE `forum_likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_like` (`post_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `forum_posts`
--
ALTER TABLE `forum_posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `learning_modules`
--
ALTER TABLE `learning_modules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pathway_id` (`pathway_id`),
  ADD KEY `fk_path_id` (`path_id`);

--
-- Indexes for table `learning_paths`
--
ALTER TABLE `learning_paths`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `learning_pathways`
--
ALTER TABLE `learning_pathways`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `path_id` (`path_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `token` (`token`),
  ADD KEY `expires_at` (`expires_at`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `post_comments`
--
ALTER TABLE `post_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `post_likes`
--
ALTER TABLE `post_likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_like` (`post_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `quiz_answers`
--
ALTER TABLE `quiz_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `module_id` (`module_id`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `tools`
--
ALTER TABLE `tools`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_achievements`
--
ALTER TABLE `user_achievements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_profile` (`user_id`);

--
-- Indexes for table `user_progress`
--
ALTER TABLE `user_progress`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `module_id` (`module_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `community_posts`
--
ALTER TABLE `community_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `fish`
--
ALTER TABLE `fish`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `forum_comments`
--
ALTER TABLE `forum_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `forum_likes`
--
ALTER TABLE `forum_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `forum_posts`
--
ALTER TABLE `forum_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `learning_modules`
--
ALTER TABLE `learning_modules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `learning_paths`
--
ALTER TABLE `learning_paths`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `learning_pathways`
--
ALTER TABLE `learning_pathways`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `modules`
--
ALTER TABLE `modules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `post_comments`
--
ALTER TABLE `post_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `post_likes`
--
ALTER TABLE `post_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `quiz_answers`
--
ALTER TABLE `quiz_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tools`
--
ALTER TABLE `tools`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `user_achievements`
--
ALTER TABLE `user_achievements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_profiles`
--
ALTER TABLE `user_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_progress`
--
ALTER TABLE `user_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `community_posts`
--
ALTER TABLE `community_posts`
  ADD CONSTRAINT `community_posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `forum_comments`
--
ALTER TABLE `forum_comments`
  ADD CONSTRAINT `forum_comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `forum_posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `forum_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `forum_likes`
--
ALTER TABLE `forum_likes`
  ADD CONSTRAINT `forum_likes_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `forum_posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `forum_likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `forum_posts`
--
ALTER TABLE `forum_posts`
  ADD CONSTRAINT `forum_posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `learning_modules`
--
ALTER TABLE `learning_modules`
  ADD CONSTRAINT `fk_path_id` FOREIGN KEY (`path_id`) REFERENCES `learning_paths` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `learning_modules_ibfk_1` FOREIGN KEY (`pathway_id`) REFERENCES `learning_pathways` (`id`);

--
-- Constraints for table `modules`
--
ALTER TABLE `modules`
  ADD CONSTRAINT `modules_ibfk_1` FOREIGN KEY (`path_id`) REFERENCES `learning_paths` (`id`);

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `post_comments`
--
ALTER TABLE `post_comments`
  ADD CONSTRAINT `post_comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`),
  ADD CONSTRAINT `post_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `post_likes`
--
ALTER TABLE `post_likes`
  ADD CONSTRAINT `post_likes_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`),
  ADD CONSTRAINT `post_likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `quiz_answers`
--
ALTER TABLE `quiz_answers`
  ADD CONSTRAINT `quiz_answers_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `quiz_questions` (`id`);

--
-- Constraints for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  ADD CONSTRAINT `quiz_questions_ibfk_1` FOREIGN KEY (`module_id`) REFERENCES `learning_modules` (`id`);

--
-- Constraints for table `user_achievements`
--
ALTER TABLE `user_achievements`
  ADD CONSTRAINT `user_achievements_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD CONSTRAINT `user_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `user_progress`
--
ALTER TABLE `user_progress`
  ADD CONSTRAINT `user_progress_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `user_progress_ibfk_2` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
