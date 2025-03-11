-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 11, 2025 at 01:10 PM
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
-- Database: `hopebridge_database`
--

-- --------------------------------------------------------

--
-- Table structure for table `organizations_table`
--

CREATE TABLE `organizations_table` (
  `org_id` int(11) NOT NULL,
  `org_name` varchar(255) NOT NULL,
  `org_type` varchar(255) NOT NULL,
  `website_link` varchar(2048) DEFAULT NULL,
  `donation_link` varchar(2048) DEFAULT NULL,
  `donation_type` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `organizations_table`
--

INSERT INTO `organizations_table` (`org_id`, `org_name`, `org_type`, `website_link`, `donation_link`, `donation_type`, `description`) VALUES
(2, 'test2', '', 'https://www.youtube.com', 'https://www.youtube.com', '', 'test descript');

-- --------------------------------------------------------

--
-- Table structure for table `organization_tags`
--

CREATE TABLE `organization_tags` (
  `id` int(11) NOT NULL,
  `org_id` int(11) NOT NULL,
  `tag` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `organization_tags`
--

INSERT INTO `organization_tags` (`id`, `org_id`, `tag`) VALUES
(1, 2, 'health'),
(2, 2, 'flood'),
(3, 2, 'rescue'),
(4, 2, 'charity');

-- --------------------------------------------------------

--
-- Table structure for table `user_table`
--

CREATE TABLE `user_table` (
  `id` int(11) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `verified` tinyint(1) DEFAULT 0,
  `verification_code` varchar(255) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `role` enum('Admin','User','Verified') NOT NULL DEFAULT 'User',
  `status` enum('Active','Blocked') NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_table`
--

INSERT INTO `user_table` (`id`, `username`, `password`, `verified`, `verification_code`, `reset_token`, `token_expiry`, `email`, `role`, `status`) VALUES
(30, 'admin', '$2y$10$0EJ6NpzIIdySSK0vXe/VoODxHMSDF.SL9z1V3C8ekFf7qHiJc7k1y', 0, NULL, NULL, NULL, 'admin@gmail.com', 'Admin', 'Active'),
(36, 'asdasd', 'asdasd', 0, NULL, NULL, NULL, 'asdasd@gmail.com', 'User', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `verification_requests`
--

CREATE TABLE `verification_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `organizations_table`
--
ALTER TABLE `organizations_table`
  ADD PRIMARY KEY (`org_id`);

--
-- Indexes for table `organization_tags`
--
ALTER TABLE `organization_tags`
  ADD PRIMARY KEY (`id`),
  ADD KEY `org_id` (`org_id`);

--
-- Indexes for table `user_table`
--
ALTER TABLE `user_table`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `UNIQUEEMAIL` (`username`);

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
-- AUTO_INCREMENT for table `organizations_table`
--
ALTER TABLE `organizations_table`
  MODIFY `org_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `organization_tags`
--
ALTER TABLE `organization_tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `user_table`
--
ALTER TABLE `user_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `verification_requests`
--
ALTER TABLE `verification_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `organization_tags`
--
ALTER TABLE `organization_tags`
  ADD CONSTRAINT `organization_tags_ibfk_1` FOREIGN KEY (`org_id`) REFERENCES `organizations_table` (`org_id`) ON DELETE CASCADE;

--
-- Constraints for table `verification_requests`
--
ALTER TABLE `verification_requests`
  ADD CONSTRAINT `verification_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_table` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
