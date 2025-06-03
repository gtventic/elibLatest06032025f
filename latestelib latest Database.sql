-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 03, 2025 at 06:58 AM
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
-- Database: `latestelib`
--

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `department_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `department_name`) VALUES
(1, 'ICTD'),
(2, 'BDD'),
(3, 'ACCOUNTING'),
(4, 'BRANCH'),
(5, 'TREASURY'),
(6, 'AMLD'),
(7, 'CREDIT'),
(8, 'INTERNAL AUDIT DEPARTMENT'),
(10, 'GSO');

-- --------------------------------------------------------

--
-- Table structure for table `pdf_files`
--

CREATE TABLE `pdf_files` (
  `id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_by` int(11) DEFAULT NULL,
  `archived` tinyint(1) DEFAULT 0,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `department_id` int(11) DEFAULT NULL,
  `target_users` text DEFAULT NULL,
  `category` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pdf_files`
--

INSERT INTO `pdf_files` (`id`, `file_name`, `file_path`, `uploaded_by`, `archived`, `uploaded_at`, `department_id`, `target_users`, `category`) VALUES
(9, 'sample policy.pdf', '../uploads/POLICY/sample policy.pdf', 5, 0, '2025-06-03 00:01:39', 1, '2', 'POLICY'),
(12, 'sample IOM.pdf', '../uploads/IOM/sample IOM.pdf', 5, 0, '2025-06-03 00:24:44', 4, NULL, 'IOM');

-- --------------------------------------------------------

--
-- Table structure for table `pdf_log`
--

CREATE TABLE `pdf_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `pdf_file_id` int(11) DEFAULT NULL,
  `view_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `action` varchar(50) NOT NULL DEFAULT 'view',
  `ip_address` varchar(50) DEFAULT NULL,
  `pdf_file_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pdf_log`
--

INSERT INTO `pdf_log` (`id`, `user_id`, `pdf_file_id`, `view_time`, `action`, `ip_address`, `pdf_file_name`) VALUES
(21, 3, 3, '2025-05-30 08:10:47', 'view', NULL, NULL),
(22, 3, 3, '2025-05-30 08:10:48', 'view', NULL, NULL),
(23, 3, 3, '2025-05-30 08:10:48', 'view', NULL, NULL),
(24, 3, 3, '2025-05-30 08:10:48', 'view', NULL, NULL),
(25, 3, 3, '2025-05-30 08:10:53', 'view', NULL, NULL),
(26, 3, 3, '2025-05-30 08:11:05', 'view', NULL, NULL),
(27, 3, 3, '2025-05-30 08:13:12', 'view', NULL, NULL),
(28, 3, 3, '2025-05-30 08:14:50', 'view', NULL, NULL),
(39, 6, 6, '2025-06-03 00:26:12', 'view', NULL, NULL),
(40, 6, 12, '2025-06-03 00:26:17', 'view', NULL, NULL),
(41, 5, 11, '2025-06-03 00:58:59', 'delete', NULL, NULL),
(42, 5, 10, '2025-06-03 00:59:04', 'delete', NULL, NULL),
(43, 5, 2, '2025-06-03 01:14:46', 'delete', '26.187.132.17', NULL),
(44, 5, 6, '2025-06-03 01:38:56', 'delete', '26.187.132.17', 'Annex-A-EDITED.pdf'),
(45, 3, 12, '2025-06-03 02:46:31', 'view', NULL, NULL),
(46, 3, 3, '2025-06-03 02:46:40', 'view', NULL, NULL),
(47, 3, 12, '2025-06-03 02:46:49', 'view', NULL, NULL),
(48, 5, 8, '2025-06-03 03:03:28', 'delete', '26.187.132.17', 'Amended BL.pdf'),
(49, 5, 7, '2025-06-03 03:03:33', 'delete', '26.187.132.17', '4.21.2025 Aurora Branch Floor PLan.pdf'),
(50, 5, 3, '2025-06-03 03:03:40', 'delete', '26.187.132.17', 'sample.pdf'),
(51, 3, 12, '2025-06-03 04:43:20', 'view', '26.187.132.17', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin','super_admin') DEFAULT 'user',
  `department_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1,
  `password_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `department_id`, `created_at`, `is_active`, `password_expiry`) VALUES
(1, 'ictd', '$2y$10$XYeLJBVPdheJ0A1ckat59OuJWnJ5XGOgEScW5emT/iOaOojjXI5EK', 'super_admin', 1, '2025-05-31 01:08:39', 1, '2025-12-27 10:08:33'),
(2, 'bdd', '$2y$10$o1fYhx586c/ypy0KL2.bsezC.dyHqsBrq7DaR2KMnDlfiP.NAE0gO', 'user', 2, '2025-05-30 01:12:18', 1, '2025-06-30 10:09:06'),
(3, 'alicia', '$2y$10$x4uQtZh.P1QwbTmCrBXnMOaAefn/pbuMvw2FbMdnt2wD2yB9PnWQK', 'user', 4, '2025-05-30 02:16:04', 1, '2025-08-28 04:16:04'),
(4, 'aparri', '$2y$10$JuTCcLcrJUqpDQNhooxK6.RiKEdBgV6hz7/QqfXnOzMSFVKOA7tZy', 'user', 4, '2025-05-30 03:08:49', 1, '2025-08-28 05:08:49'),
(5, 'bdd_systemadmin', '$2y$10$zs/dXfKEb9D7yWc9ni2ucOWwgkX0fiftEHRZSt3hzme2cpBV653TK', 'admin', 1, '2025-05-30 03:15:53', 1, '2025-08-28 05:15:53'),
(6, 'abulug', '$2y$10$lakE3baDpwdlvDKT2QdqFOGKXAikxt9phWp1SLjLc9GpOFT8qJ.e6', 'user', 4, '2025-06-02 07:24:53', 1, '2025-11-29 09:24:53');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pdf_files`
--
ALTER TABLE `pdf_files`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pdf_log`
--
ALTER TABLE `pdf_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `pdf_files`
--
ALTER TABLE `pdf_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `pdf_log`
--
ALTER TABLE `pdf_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
