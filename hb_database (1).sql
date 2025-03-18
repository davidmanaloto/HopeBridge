-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 18, 2025 at 08:16 AM
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
-- Database: `hb_database`
--

-- --------------------------------------------------------

--
-- Table structure for table `donations`
--

CREATE TABLE `donations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `organization_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `date_created` datetime DEFAULT current_timestamp(),
  `receipt_path` varchar(255) DEFAULT NULL,
  `status` enum('Pending','Completed','Failed') DEFAULT 'Pending',
  `username` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donations`
--

INSERT INTO `donations` (`id`, `user_id`, `organization_id`, `amount`, `date_created`, `receipt_path`, `status`, `username`) VALUES
(1, 2, 1, 100.00, '2025-03-15 18:14:24', 'receipts/receipt1.jpg', 'Pending', ''),
(2, 3, 2, 50.00, '2025-03-15 18:14:24', 'receipts/receipt2.jpg', 'Pending', ''),
(3, 4, 1, 75.50, '2025-03-15 18:14:24', 'receipts/receipt3.jpg', 'Completed', ''),
(4, 2, 2, 200.00, '2025-03-15 18:14:24', 'receipts/receipt4.jpg', 'Failed', ''),
(10, 6, 1, 2.00, '2025-03-17 19:53:25', NULL, 'Pending', ''),
(22, 6, 4, 2.00, '2025-03-17 22:18:11', NULL, 'Completed', ''),
(23, 6, 4, 2.00, '2025-03-17 22:18:41', NULL, 'Completed', ''),
(24, 8, 3, 11.00, '2025-03-17 22:24:39', NULL, 'Completed', ''),
(25, 9, 2, 12.00, '2025-03-17 22:29:27', NULL, 'Completed', ''),
(26, 9, 2, 10.00, '2025-03-18 12:24:19', NULL, 'Completed', ''),
(27, 9, 2, 10.00, '2025-03-18 12:35:47', NULL, 'Pending', ''),
(28, 9, 2, 2.00, '2025-03-18 12:45:37', NULL, 'Pending', ''),
(55, 14, 2, 11.00, '2025-03-18 14:15:24', NULL, 'Completed', 'eeqwe'),
(56, 14, 1, 12.00, '2025-03-18 14:15:40', NULL, 'Completed', 'eeqwe'),
(57, 8, 1, 12.00, '2025-03-18 14:16:20', NULL, 'Completed', 'vergil'),
(58, 10, 2, 10.00, '2025-03-18 14:26:35', NULL, 'Pending', 'cartel'),
(59, 9, 2, 10.00, '2025-03-18 14:26:45', NULL, 'Pending', 'mwking'),
(60, 10, 2, 10.00, '2025-03-18 14:27:05', NULL, 'Pending', 'cartel'),
(61, 6, 2, 10.00, '2025-03-18 14:27:16', NULL, 'Pending', 'luther11'),
(62, 1, 2, 10.00, '2025-03-18 14:27:21', NULL, 'Pending', 'admin'),
(63, 1, 5, 10.00, '2025-03-18 14:27:55', NULL, 'Pending', 'admin'),
(64, 11, 5, 10.00, '2025-03-18 14:28:02', NULL, 'Pending', 'gus'),
(65, 8, 1, 12.00, '2025-03-18 15:08:08', NULL, 'Pending', 'vergil');

-- --------------------------------------------------------

--
-- Table structure for table `organizations`
--

CREATE TABLE `organizations` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `website` varchar(255) DEFAULT NULL,
  `donation_link` varchar(255) DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `organizations`
--

INSERT INTO `organizations` (`id`, `name`, `website`, `donation_link`, `tags`, `description`, `date_created`) VALUES
(1, 'Red Cross', 'https://www.redcross.org', 'https://donate.redcross.org', 'Health, Emergency', 'International humanitarian organization', '2025-02-25 00:30:00'),
(2, 'UNICEF', 'https://www.unicef.org', 'https://donate.unicef.org', 'Children, Education', 'Supports child health and education globally', '2025-02-26 02:45:00'),
(3, 'WWF', 'https://www.worldwildlife.org', 'https://donate.wwf.org', 'Environment, Wildlife', 'Protects wildlife and nature conservation', '2025-02-27 06:20:00'),
(4, 'Doctors Without Borders', 'https://www.doctorswithoutborders.org', 'https://donate.doctorswithoutborders.org', 'Medical, Emergency', 'Provides medical aid in conflict zones', '2025-02-28 01:15:00'),
(5, 'Save the Children', 'https://www.savethechildren.org', 'https://donate.savethechildren.org', 'Children, Health', 'Advocates for children’s rights and welfare', '2025-03-01 04:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `project_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `project_name` varchar(255) NOT NULL,
  `project_summary` text NOT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `donation_goal` decimal(10,2) NOT NULL,
  `funds_raised` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `organization_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`project_id`, `user_id`, `project_name`, `project_summary`, `image_url`, `donation_goal`, `funds_raised`, `created_at`, `organization_id`) VALUES
(13, 6, 'Yolanda', 'qwertyuiopasdfghjklzxcvbnm,1234567890', 'content://com.google.android.apps.photos.contentprovider/-1/1/content%3A%2F%2Fmedia%2Fexternal%2Fimages%2Fmedia%2F58/ORIGINAL/NONE/image%2Fjpeg/201413581', 1000.00, 30.00, '2025-03-17 11:45:18', NULL),
(31, 10, '3222232wqe', 'qweqweqwe', 'content://com.google.android.apps.photos.contentprovider/-1/1/content%3A%2F%2Fmedia%2Fexternal%2Fimages%2Fmedia%2F58/ORIGINAL/NONE/image%2Fjpeg/1936246082', 1211.00, 429.00, '2025-03-18 04:49:34', '5'),
(32, 9, 'asdasdsadasd', 'qweqwewqeasdz', 'content://com.google.android.apps.photos.contentprovider/-1/1/content%3A%2F%2Fmedia%2Fexternal%2Fimages%2Fmedia%2F58/ORIGINAL/NONE/image%2Fjpeg/1092659583', 1000.00, 162.00, '2025-03-18 05:19:59', '2'),
(33, 8, '331313', '13131313', 'content://com.google.android.apps.photos.contentprovider/-1/1/content%3A%2F%2Fmedia%2Fexternal%2Fimages%2Fmedia%2F58/ORIGINAL/NONE/image%2Fjpeg/2069404959', 13113.00, 36.00, '2025-03-18 05:42:56', '1');

-- --------------------------------------------------------

--
-- Table structure for table `user_table`
--

CREATE TABLE `user_table` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` text NOT NULL,
  `role` enum('User','Admin') DEFAULT 'User',
  `status` enum('Active','Blocked') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `verification_status` enum('Unverified','Pending','Verified','Rejected') NOT NULL DEFAULT 'Unverified',
  `verification_reason` text DEFAULT NULL,
  `verification_document` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_table`
--

INSERT INTO `user_table` (`id`, `username`, `email`, `password`, `role`, `status`, `created_at`, `is_verified`, `verification_status`, `verification_reason`, `verification_document`) VALUES
(1, 'admin', 'admin@example.com', '$2y$10$btraEi4Zb6eGBdIb4GbBoeS2/ezb58bLZloHKDbXhGsruWs5uDVAu', 'Admin', '', '2025-03-14 05:20:22', 0, 'Unverified', NULL, NULL),
(2, 'testuser1', 'test1@example.com', '$2y$10$abcdefghijk1234567890', 'User', 'Active', '2025-03-15 07:49:51', 1, 'Verified', NULL, NULL),
(3, 'testuser2', 'test2@example.com', '$2y$10$abcdefghijk1234567890', 'User', 'Blocked', '2025-03-15 07:49:51', 0, 'Unverified', NULL, NULL),
(4, 'testuser3', 'test3@example.com', '$2y$10$abcdefghijk1234567890', 'User', 'Active', '2025-03-15 07:49:51', 1, 'Pending', NULL, NULL),
(6, 'luther11', 'test@gmail.com11', 'qwerty123', 'User', 'Active', '2025-03-17 08:40:18', 0, 'Unverified', NULL, NULL),
(8, 'vergil', 'test@gmail.com', 'qwerty123', 'User', 'Active', '2025-03-17 14:07:05', 0, 'Unverified', NULL, NULL),
(9, 'mwking', 'dante@gmail.com', 'qwerty123', 'User', 'Active', '2025-03-17 14:27:20', 0, 'Unverified', NULL, NULL),
(10, 'cartel', 'iona@gmail.com', 'qwerty123', 'User', 'Active', '2025-03-18 04:35:19', 0, 'Unverified', NULL, NULL),
(11, 'gus', 'fil@gmail.com', 'qwerty123', 'User', 'Active', '2025-03-18 05:15:56', 0, 'Unverified', NULL, NULL),
(12, 'ert', 'lol@gmail.com', 'qwerty', 'User', 'Active', '2025-03-18 05:40:51', 0, 'Unverified', NULL, NULL),
(13, 'ertyu', 'ert@gmail.com', 'qwerty', 'User', 'Active', '2025-03-18 05:41:35', 0, 'Unverified', NULL, NULL),
(14, 'eeqwe', '123@gmail.com', 'qwerty123', 'User', 'Active', '2025-03-18 06:03:02', 0, 'Unverified', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `verification_requests`
--

CREATE TABLE `verification_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('Pending','Verified','Rejected') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `document_path` varchar(255) NOT NULL,
  `reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `verification_requests`
--

INSERT INTO `verification_requests` (`id`, `user_id`, `status`, `created_at`, `updated_at`, `document_path`, `reason`) VALUES
(5, 2, 'Verified', '2025-03-11 06:45:00', '2025-03-12 01:15:00', 'uploads/docs/user2_doc.pdf', NULL),
(6, 3, 'Rejected', '2025-03-12 00:20:00', '2025-03-13 02:00:00', 'uploads/docs/user3_doc.pdf', 'Document not clear'),
(7, 4, 'Pending', '2025-03-13 07:10:00', '2025-03-13 07:10:00', 'uploads/docs/user4_doc.pdf', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `donations`
--
ALTER TABLE `donations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `organization_id` (`organization_id`);

--
-- Indexes for table `organizations`
--
ALTER TABLE `organizations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`project_id`),
  ADD UNIQUE KEY `project_name` (`project_name`),
  ADD UNIQUE KEY `organization_id` (`organization_id`);

--
-- Indexes for table `user_table`
--
ALTER TABLE `user_table`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `verification_requests`
--
ALTER TABLE `verification_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `donations`
--
ALTER TABLE `donations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `organizations`
--
ALTER TABLE `organizations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `project_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `user_table`
--
ALTER TABLE `user_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `verification_requests`
--
ALTER TABLE `verification_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `donations`
--
ALTER TABLE `donations`
  ADD CONSTRAINT `donations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_table` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `donations_ibfk_2` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `verification_requests`
--
ALTER TABLE `verification_requests`
  ADD CONSTRAINT `verification_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_table` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
