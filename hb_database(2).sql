-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 23, 2025 at 08:55 AM
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
  `project_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donations`
--

INSERT INTO `donations` (`id`, `user_id`, `organization_id`, `amount`, `date_created`, `receipt_path`, `status`, `project_id`) VALUES
(1, 2, 1, 200.00, '2025-03-15 18:14:24', 'receipts/receipt1.jpg', 'Completed', NULL),
(2, 3, 2, 50.00, '2025-03-15 18:14:24', 'receipts/receipt2.jpg', 'Completed', NULL),
(3, 4, 1, 75.50, '2025-03-15 18:14:24', 'receipts/receipt3.jpg', 'Completed', NULL),
(4, 2, 2, 200.00, '2025-03-15 18:14:24', 'receipts/receipt4.jpg', 'Failed', NULL);

--
-- Triggers `donations`
--
DELIMITER $$
CREATE TRIGGER `before_fund_update` BEFORE UPDATE ON `donations` FOR EACH ROW BEGIN
    INSERT INTO funds_raised_table (organization_id, previous_fund_raised, recorded_at)
    VALUES (OLD.organization_id, OLD.amount, NOW());
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `funds_raised_history`
--

CREATE TABLE `funds_raised_history` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `funds_raised` decimal(10,2) NOT NULL,
  `recorded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `organization_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `funds_raised_table`
--

CREATE TABLE `funds_raised_table` (
  `id` int(11) NOT NULL,
  `organization_id` int(11) NOT NULL,
  `previous_fund_raised` decimal(10,2) NOT NULL,
  `recorded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `funds_raised_table`
--

INSERT INTO `funds_raised_table` (`id`, `organization_id`, `previous_fund_raised`, `recorded_at`) VALUES
(1, 1, 100.00, '2025-03-22 22:07:00'),
(2, 1, 100.00, '2025-03-22 22:07:29'),
(3, 1, 200.00, '2025-03-22 22:07:43'),
(4, 2, 50.00, '2025-03-22 22:25:43');

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
(5, 'Save the Children', 'https://www.savethechildren.org', 'https://donate.savethechildren.org', 'Children, Health', 'Advocates for children’s rights and welfare', '2025-03-01 04:00:00'),
(6, 'HopeBridge', 'https://www.hopebridge.org', 'https://donate.hopebridge.org', 'Charity, Emergency', 'Bridges people when disaster strikes', '2025-03-17 14:54:00');

-- --------------------------------------------------------

--
-- Table structure for table `org_user_table`
--

CREATE TABLE `org_user_table` (
  `id` int(11) NOT NULL,
  `organization_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` text NOT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `role` enum('Organization','User') DEFAULT 'Organization',
  `status` enum('Active','Blocked') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `verification_status` enum('Unverified','Pending','Verified','Rejected') NOT NULL DEFAULT 'Unverified',
  `verification_reason` text DEFAULT NULL,
  `verification_document` varchar(255) DEFAULT NULL,
  `organization_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `org_user_table`
--

INSERT INTO `org_user_table` (`id`, `organization_name`, `email`, `password`, `contact_number`, `address`, `role`, `status`, `created_at`, `is_verified`, `verification_status`, `verification_reason`, `verification_document`, `organization_id`) VALUES
(1, 'ORGTEST', 'org@gmail.com', 'qwerty123', NULL, NULL, 'Organization', 'Active', '2025-03-20 06:15:22', 0, 'Unverified', NULL, NULL, NULL),
(3, 'ORGTEST1', 'qwe@gmail.com', 'qwerty123', NULL, NULL, 'Organization', 'Active', '2025-03-21 01:19:03', 0, 'Rejected', 'L + ratio + no bitches', NULL, NULL);

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
(1, 2, 'Clean Water Initiative', 'Providing clean water to rural areas.', 'images/clean_water.jpg', 5000.00, 1200.00, '2025-03-20 02:46:31', '2');

-- --------------------------------------------------------

--
-- Table structure for table `user_table`
--

CREATE TABLE `user_table` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
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
(3, 'testuser2', 'test2@example.com', '$2y$10$abcdefghijk1234567890', 'User', 'Active', '2025-03-15 07:49:51', 1, 'Verified', NULL, NULL),
(4, 'testuser3', 'test3@example.com', '$2y$10$abcdefghijk1234567890', 'User', 'Active', '2025-03-15 07:49:51', 1, 'Verified', NULL, NULL),
(5, 'testuser5', 'test5@example.com', '$2y$10$abcdefghijk1234567890', 'User', 'Active', '2025-03-14 23:49:51', 1, 'Verified', NULL, NULL),
(6, 'luther', 'test@gmail.com', 'qwerty123', 'User', 'Active', '2025-03-17 08:36:39', 1, 'Verified', NULL, NULL),
(7, 'luther1', 'test@gmail.com1', 'qwerty123', 'User', 'Active', '2025-03-17 08:37:40', 1, 'Verified', NULL, NULL);

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
(5, 2, 'Pending', '2025-03-11 06:45:00', '2025-03-19 02:30:25', 'uploads/docs/user2_doc.pdf', NULL),
(6, 3, 'Pending', '2025-03-12 00:20:00', '2025-03-18 03:25:31', 'uploads/docs/user3_doc.pdf', 'Document not clear'),
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
  ADD KEY `organization_id` (`organization_id`),
  ADD KEY `fk_donations_project` (`project_id`);

--
-- Indexes for table `funds_raised_history`
--
ALTER TABLE `funds_raised_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `fk_funds_raised_history_donations` (`organization_id`);

--
-- Indexes for table `funds_raised_table`
--
ALTER TABLE `funds_raised_table`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `organizations`
--
ALTER TABLE `organizations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `org_user_table`
--
ALTER TABLE `org_user_table`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `organization_name` (`organization_name`),
  ADD UNIQUE KEY `email` (`email`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `funds_raised_history`
--
ALTER TABLE `funds_raised_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `funds_raised_table`
--
ALTER TABLE `funds_raised_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `organizations`
--
ALTER TABLE `organizations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `org_user_table`
--
ALTER TABLE `org_user_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `project_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_table`
--
ALTER TABLE `user_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
  ADD CONSTRAINT `donations_ibfk_2` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_donations_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE SET NULL;

--
-- Constraints for table `funds_raised_history`
--
ALTER TABLE `funds_raised_history`
  ADD CONSTRAINT `fk_funds_raised_history_donations` FOREIGN KEY (`organization_id`) REFERENCES `donations` (`organization_id`) ON DELETE CASCADE;

--
-- Constraints for table `verification_requests`
--
ALTER TABLE `verification_requests`
  ADD CONSTRAINT `verification_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_table` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
