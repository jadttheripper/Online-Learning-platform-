-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 19, 2025 at 08:58 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `skillswap`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` char(72) NOT NULL,
  `admin_reset_token_hash` varchar(255) DEFAULT NULL,
  `admin_token_expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `name`, `email`, `password`, `admin_reset_token_hash`, `admin_token_expires_at`) VALUES
(1, 'moe Kaadan', 'jadsoubra05@gmail.com', '$2y$10$k.cRKKPANQjruWogFUe9Nu7U4/dbX86VyfjCLjLAh/6neslj.kObq', NULL, NULL),
(2, 'Jixy67', '12031634@students.liu.edu.lb', '$2y$10$z7EAKfAUMCJHEG2JMVlCROvNAVy9LaRX4JYmXerQ5W0OAEN7/q1au', NULL, NULL),
(3, 'jad soubra', 'jadsoubra04@gmail.com', '$2y$10$ZQ1U971Opdk5il36sTkDB.3xurOySf1AmIiK69RGmVe.pSWTUB7sm', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `admin_logs`
--

CREATE TABLE `admin_logs` (
  `log_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `change_type` enum('insert','update','delete') NOT NULL,
  `page_name` varchar(100) NOT NULL,
  `change_time` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_logs`
--

INSERT INTO `admin_logs` (`log_id`, `admin_id`, `change_type`, `page_name`, `change_time`) VALUES
(1, 2, 'insert', 'manage skill', '2025-05-17 15:36:02'),
(2, 2, 'delete', 'manage skill', '2025-05-17 15:54:54'),
(3, 2, 'delete', 'manage user', '2025-05-17 15:55:34'),
(4, 2, 'update', 'manage admin', '2025-05-17 16:05:09'),
(5, 2, 'update', 'manage admin', '2025-05-17 16:05:22'),
(6, 2, 'update', 'manage admin', '2025-05-17 16:06:00'),
(7, 2, 'update', 'manage user', '2025-05-17 16:07:19'),
(8, 2, 'delete', 'manage user_ skill', '2025-05-17 16:08:59'),
(9, 2, 'update', 'manage course', '2025-05-17 16:28:43'),
(10, 3, 'delete', 'manage message', '2025-05-18 20:52:54'),
(11, 3, 'delete', 'manage message', '2025-05-18 21:18:46'),
(12, 3, 'update', 'manage reviews', '2025-05-18 21:55:11'),
(13, 3, 'delete', 'manage reviews', '2025-05-18 21:59:21'),
(14, 2, 'delete', 'manage user_ skill', '2025-05-19 09:29:50'),
(15, 2, 'delete', 'manage user_ skill', '2025-05-19 09:31:44');

-- --------------------------------------------------------

--
-- Table structure for table `contact`
--

CREATE TABLE `contact` (
  `message_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

CREATE TABLE `course` (
  `c_id` int(11) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `user_skill_id` int(11) DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `version` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course`
--

INSERT INTO `course` (`c_id`, `title`, `description`, `user_skill_id`, `last_updated`, `version`) VALUES
(10, 'intro to cryptography', 'start with the AES encryption algorithm', 31, '2025-05-17 12:28:43', 1),
(15, 'web dev with php', 'fullstack web', 38, '2025-05-19 06:40:52', 1);

-- --------------------------------------------------------

--
-- Table structure for table `course_progress`
--

CREATE TABLE `course_progress` (
  `progress_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `c_id` int(11) NOT NULL,
  `started_at` datetime DEFAULT current_timestamp(),
  `last_accessed_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `course_version` int(11) DEFAULT 1,
  `last_synced` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_progress`
--

INSERT INTO `course_progress` (`progress_id`, `user_id`, `c_id`, `started_at`, `last_accessed_at`, `course_version`, `last_synced`) VALUES
(12, 4, 15, '2025-05-19 09:42:40', '2025-05-19 09:43:44', 1, '2025-05-19 06:42:40');

-- --------------------------------------------------------

--
-- Table structure for table `lesson`
--

CREATE TABLE `lesson` (
  `lesson_id` int(11) NOT NULL,
  `c_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text DEFAULT NULL,
  `video_url` varchar(1024) DEFAULT NULL,
  `video_source_type` enum('url','file') DEFAULT 'url',
  `position` int(11) DEFAULT 1,
  `lesson_type` enum('video','reading') NOT NULL DEFAULT 'reading',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lesson`
--

INSERT INTO `lesson` (`lesson_id`, `c_id`, `title`, `content`, `video_url`, `video_source_type`, `position`, `lesson_type`, `created_at`) VALUES
(15, 10, 'what is aes', 'aes is a cryptography algorithm', '', 'url', 1, 'reading', '2025-05-16 20:14:47'),
(16, 10, 'afafd', 'adfdsf', '', 'url', 1, 'reading', '2025-05-16 20:16:14'),
(20, 15, 'fdsfdsf', 'ejfhjskdhvdsldlsfs', '', 'url', 1, 'reading', '2025-05-19 06:41:26'),
(21, 15, 'kifk', 'csdihdldvjhxlkvx,n.v', '', 'url', 1, 'reading', '2025-05-19 06:43:10'),
(22, 15, 'xzcvxzvcxzcxzczxczxc', 'xcxzcxzcxzcxzczxc', '', 'url', 1, 'reading', '2025-05-19 06:44:19');

-- --------------------------------------------------------

--
-- Table structure for table `lesson_progress`
--

CREATE TABLE `lesson_progress` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `c_id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `is_completed` tinyint(1) DEFAULT 0,
  `completed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lesson_progress`
--

INSERT INTO `lesson_progress` (`id`, `user_id`, `c_id`, `lesson_id`, `is_completed`, `completed_at`) VALUES
(16, 4, 15, 20, 1, '2025-05-19 09:43:31'),
(18, 4, 15, 21, 1, '2025-05-19 09:43:44');

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE `message` (
  `message_id` int(11) NOT NULL,
  `sender_id` int(11) DEFAULT NULL,
  `receiver_id` int(11) DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `time_stamp` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `message`
--

INSERT INTO `message` (`message_id`, `sender_id`, `receiver_id`, `content`, `time_stamp`) VALUES
(51, 4, 2, '\r\n<div class=\'course-card\' style=\'border:1px solid #ccc; padding:16px; border-radius:8px; background:#f9f9f9;\'>\r\n    <h4 style=\'margin: 0 0 10px; font-size: 16px;\'>📘 intro to linear algebra</h4>\r\n    <p style=\'margin-bottom: 12px; color:#555;\'>A course has been shared with you.</p>\r\n    <a href=\'create_progress.php?course_id=9\' style=\'display:inline-block; padding:8px 12px; background:#007bff; color:#fff; text-decoration:none; border-radius:4px;\'>Start Course</a>\r\n</div>\r\n', '2025-05-16 18:04:39'),
(52, 4, 2, '\r\n<div class=\'course-card\' style=\'border:1px solid #ccc; padding:16px; border-radius:8px; background:#f9f9f9;\'>\r\n    <h4 style=\'margin: 0 0 10px; font-size: 16px;\'>📘 intro to linear algebra</h4>\r\n    <p style=\'margin-bottom: 12px; color:#555;\'>A course has been shared with you.</p>\r\n    <a href=\'create_progress.php?c_id=9\' style=\'display:inline-block; padding:8px 12px; background:#007bff; color:#fff; text-decoration:none; border-radius:4px;\'>Start Course</a>\r\n</div>\r\n', '2025-05-16 18:09:29'),
(53, 4, 2, '\r\n<div class=\'course-card\' style=\'border:1px solid #ccc; padding:16px; border-radius:8px; background:#f9f9f9;\'>\r\n    <h4 style=\'margin: 0 0 10px; font-size: 16px;\'>📘 intro to linear algebra</h4>\r\n    <p style=\'margin-bottom: 12px; color:#555;\'>A course has been shared with you.</p>\r\n    <a href=\'create_progress.php?c_id=9\' style=\'display:inline-block; padding:8px 12px; background:#007bff; color:#fff; text-decoration:none; border-radius:4px;\'>Start Course</a>\r\n</div>\r\n', '2025-05-16 18:10:58'),
(56, 4, 2, '\r\n<div class=\'course-card\' style=\'border:1px solid #ccc; padding:16px; border-radius:8px; background:#f9f9f9; max-width:320px;\'>\r\n    <h4 style=\'margin:0 0 8px; font-size:18px; color:#1d4ed8;\'>📘 intro to linear algebra</h4>\r\n    <p style=\'margin:0 0 12px; color:#555; font-size:14px;\'>learn the basics of determinants matrices operations, etc....</p>\r\n    <a href=\'create_progress.php?c_id=9\' style=\'display:inline-block; padding:10px 16px; background:#2563eb; color:#fff; text-decoration:none; border-radius:5px; font-weight:600;\'>Start Course</a>\r\n</div>\r\n', '2025-05-16 18:33:58'),
(57, 4, 2, '\r\n<div class=\'course-card\' style=\'border:1px solid #ccc; padding:16px; border-radius:8px; background:#f9f9f9; max-width:300px;\'>\r\n    <h4 style=\'margin:0 0 8px; font-size:18px; color:#1d4ed8;\'>📘 intro to linear algebra</h4>\r\n    <p style=\'margin:0 0 12px; color:#555; font-size:14px;\'>learn the basics of determinants matrices operations, etc....</p>\r\n    <a href=\'create_progress.php?c_id=9\' style=\'display:inline-block; padding:10px 16px; background:#2563eb; color:#fff; text-decoration:none; border-radius:5px; font-weight:600;\'>Start Course</a>\r\n</div>\r\n', '2025-05-16 18:51:13'),
(60, 4, 2, '\r\n<div class=\'course-card\' style=\'border:1px solid #ccc; padding:16px; border-radius:8px; background:#f9f9f9; max-width:300px;\'>\r\n    <h4 style=\'margin:0 0 8px; font-size:18px; color:#1d4ed8;\'>📘 intro to linear algebra</h4>\r\n    <p style=\'margin:0 0 12px; color:#555; font-size:14px;\'>learn the basics of determinants matrices operations, etc....</p>\r\n    <a href=\'create_course_progress.php?c_id=9\' style=\'display:inline-block; padding:10px 16px; background:#2563eb; color:#fff; text-decoration:none; border-radius:5px; font-weight:600;\'>Start Course</a>\r\n</div>\r\n', '2025-05-16 20:11:48'),
(67, 4, 2, 'hii kifkkk', '2025-05-18 21:13:12'),
(68, 4, 2, '\r\n<div class=\'course-card\' style=\'border:1px solid #ccc; padding:16px; border-radius:8px; background:#f9f9f9; max-width:300px;\'>\r\n    <h4 style=\'margin:0 0 8px; font-size:18px; color:#1d4ed8;\'>📘 intro to cryptography</h4>\r\n    <p style=\'margin:0 0 12px; color:#555; font-size:14px;\'>start with the AES encryption algorithm</p>\r\n    <a href=\'create_course_progress.php?c_id=10\' style=\'display:inline-block; padding:10px 16px; background:#2563eb; color:#fff; text-decoration:none; border-radius:5px; font-weight:600;\'>Start Course</a>\r\n</div>\r\n', '2025-05-18 21:41:08'),
(69, 2, 4, '\r\n<div class=\'course-card\' style=\'border:1px solid #ccc; padding:16px; border-radius:8px; background:#f9f9f9; max-width:300px;\'>\r\n    <h4 style=\'margin:0 0 8px; font-size:18px; color:#1d4ed8;\'>📘 web dev with php</h4>\r\n    <p style=\'margin:0 0 12px; color:#555; font-size:14px;\'>fullstack web</p>\r\n    <a href=\'create_course_progress.php?c_id=15\' style=\'display:inline-block; padding:10px 16px; background:#2563eb; color:#fff; text-decoration:none; border-radius:5px; font-weight:600;\'>Start Course</a>\r\n</div>\r\n', '2025-05-19 09:41:53');

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `review_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `rating` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `skill`
--

CREATE TABLE `skill` (
  `skill_id` int(11) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `skill_category` text NOT NULL,
  `image_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `skill`
--

INSERT INTO `skill` (`skill_id`, `title`, `description`, `skill_category`, `image_url`) VALUES
(17, 'Linear algebra', 'math', 'Math_Science', 'image/Math_Science.png'),
(20, 'Guitar', 'learn to play the electric guitar', 'Music', 'image/Music.png'),
(21, 'finance', '', 'Finance_Accounting', 'image/Finance_Accounting.png'),
(47, 'crypto', 'learn cryptograpghy', 'Technology_Programming', 'image/Technology_Programming.png'),
(49, 'violin', 'a music instrument', 'Music', 'image/Music.png'),
(50, 'motsik part', 'a2wa 150 cc ma3 tawshe wshbeshil', 'Design_Creativity', 'image/Design_Creativity.png'),
(51, 'php', 'a web development skill', 'Technology_Programming', 'image/Technology_Programming.png');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `pass` char(72) NOT NULL,
  `education_institute` varchar(300) DEFAULT NULL,
  `language_preference` varchar(300) DEFAULT NULL,
  `reset_token_hash` varchar(64) DEFAULT NULL,
  `reset_token_expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `name`, `email`, `profile_pic`, `pass`, `education_institute`, `language_preference`, `reset_token_hash`, `reset_token_expires_at`) VALUES
(2, 'moe jz', 'tralalero54@gmail.com', 'image/681cf0fd53d4f_Screenshot 2024-11-17 181239.png', '$2y$10$0rhdp28DJGzppR0G5.0ymuaxfAifEJT68edjXhRTw8ja/qB5xGPEi', 'Notre Dame University – Louaize (NDU)', 'Arabic', NULL, NULL),
(4, 'jad soubra', 'jadsoubra05@gmail.com', NULL, '$2y$10$sMQxnqULqt6bRz6H.ASE4OKxl7OyHIXeBB6Omd8sf0l9bTP7kfPC2', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_skill`
--

CREATE TABLE `user_skill` (
  `user_skill_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `skill_id` int(11) DEFAULT NULL,
  `user_skill_description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_skill`
--

INSERT INTO `user_skill` (`user_skill_id`, `user_id`, `skill_id`, `user_skill_description`) VALUES
(31, 4, 47, NULL),
(38, 2, 51, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `contact`
--
ALTER TABLE `contact`
  ADD PRIMARY KEY (`message_id`);

--
-- Indexes for table `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`c_id`),
  ADD KEY `userskill_id` (`user_skill_id`);

--
-- Indexes for table `course_progress`
--
ALTER TABLE `course_progress`
  ADD PRIMARY KEY (`progress_id`),
  ADD UNIQUE KEY `unique_user_course` (`user_id`,`c_id`),
  ADD KEY `fk_course_progress_course` (`c_id`);

--
-- Indexes for table `lesson`
--
ALTER TABLE `lesson`
  ADD PRIMARY KEY (`lesson_id`),
  ADD KEY `c_id` (`c_id`);

--
-- Indexes for table `lesson_progress`
--
ALTER TABLE `lesson_progress`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_progress` (`user_id`,`lesson_id`),
  ADD KEY `c_id` (`c_id`),
  ADD KEY `lesson_id` (`lesson_id`);

--
-- Indexes for table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `skill`
--
ALTER TABLE `skill`
  ADD PRIMARY KEY (`skill_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `reset_token_hash` (`reset_token_hash`);

--
-- Indexes for table `user_skill`
--
ALTER TABLE `user_skill`
  ADD PRIMARY KEY (`user_skill_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `skill_id` (`skill_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `admin_logs`
--
ALTER TABLE `admin_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `contact`
--
ALTER TABLE `contact`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `course`
--
ALTER TABLE `course`
  MODIFY `c_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `course_progress`
--
ALTER TABLE `course_progress`
  MODIFY `progress_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `lesson`
--
ALTER TABLE `lesson`
  MODIFY `lesson_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `lesson_progress`
--
ALTER TABLE `lesson_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `message`
--
ALTER TABLE `message`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `skill`
--
ALTER TABLE `skill`
  MODIFY `skill_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user_skill`
--
ALTER TABLE `user_skill`
  MODIFY `user_skill_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD CONSTRAINT `admin_logs_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`admin_id`) ON DELETE CASCADE;

--
-- Constraints for table `course`
--
ALTER TABLE `course`
  ADD CONSTRAINT `fk_course_user_skill` FOREIGN KEY (`user_skill_id`) REFERENCES `user_skill` (`user_skill_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_course_userskill` FOREIGN KEY (`user_skill_id`) REFERENCES `user_skill` (`user_skill_id`) ON DELETE CASCADE;

--
-- Constraints for table `course_progress`
--
ALTER TABLE `course_progress`
  ADD CONSTRAINT `fk_course_progress_course` FOREIGN KEY (`c_id`) REFERENCES `course` (`c_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_course_progress_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `lesson`
--
ALTER TABLE `lesson`
  ADD CONSTRAINT `lesson_ibfk_1` FOREIGN KEY (`c_id`) REFERENCES `course` (`c_id`) ON DELETE CASCADE;

--
-- Constraints for table `lesson_progress`
--
ALTER TABLE `lesson_progress`
  ADD CONSTRAINT `lesson_progress_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lesson_progress_ibfk_2` FOREIGN KEY (`c_id`) REFERENCES `course` (`c_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lesson_progress_ibfk_3` FOREIGN KEY (`lesson_id`) REFERENCES `lesson` (`lesson_id`) ON DELETE CASCADE;

--
-- Constraints for table `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `message_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `message_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `review_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `user_skill`
--
ALTER TABLE `user_skill`
  ADD CONSTRAINT `user_skill_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `user_skill_ibfk_2` FOREIGN KEY (`skill_id`) REFERENCES `skill` (`skill_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
