-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 11, 2024 at 05:49 AM
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
-- Database: `casino_management_v2`
--

-- --------------------------------------------------------

--
-- Table structure for table `cashouts`
--

CREATE TABLE `cashouts` (
  `id` int(11) NOT NULL,
  `fb_name` varchar(255) NOT NULL,
  `cashout_amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deposit_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cashouts`
--

INSERT INTO `cashouts` (`id`, `fb_name`, `cashout_amount`, `created_at`, `deposit_id`) VALUES
(26, 'david grace ', 100.00, '2024-10-23 06:57:15', 23);

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `file` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chat_messages`
--

INSERT INTO `chat_messages` (`id`, `username`, `message`, `file`, `created_at`) VALUES
(30, 'admin', 'hlo', NULL, '2024-10-23 09:49:42'),
(31, 'ok', 'hjr', NULL, '2024-10-23 09:49:50'),
(32, 'ok', 'hi', NULL, '2024-10-23 09:52:18'),
(33, 'ok', 'OK', NULL, '2024-10-23 09:53:12'),
(34, 'ok', 'ok', NULL, '2024-10-23 09:54:56'),
(35, 'ok', 'hoina maila', NULL, '2024-10-23 09:55:05');

-- --------------------------------------------------------

--
-- Table structure for table `deposits`
--

CREATE TABLE `deposits` (
  `id` int(11) NOT NULL,
  `fb_name` varchar(255) NOT NULL,
  `deposit_amount` decimal(10,2) NOT NULL,
  `bonus_amount` decimal(10,2) NOT NULL,
  `game` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `deposits`
--

INSERT INTO `deposits` (`id`, `fb_name`, `deposit_amount`, `bonus_amount`, `game`, `created_at`) VALUES
(23, 'david grace ', 10.00, 2.00, 'gv', '2024-10-23 06:56:57');

-- --------------------------------------------------------

--
-- Table structure for table `freeplay_deposits`
--

CREATE TABLE `freeplay_deposits` (
  `id` int(11) NOT NULL,
  `fb_name` varchar(255) NOT NULL,
  `freeplay_amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `freeplay_deposits`
--

INSERT INTO `freeplay_deposits` (`id`, `fb_name`, `freeplay_amount`, `created_at`) VALUES
(17, 'david grace ', 5.00, '2024-10-23 06:57:05');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `date_column` datetime NOT NULL,
  `deposit_amount` decimal(10,2) DEFAULT 0.00,
  `cashout_amount` decimal(10,2) DEFAULT 0.00,
  `bonus_amount` decimal(10,2) DEFAULT 0.00,
  `freeplay_amount` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `is_deleted` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `is_deleted`) VALUES
(1, 'admin', '$2y$10$B7m41rwZ4/Bmp49xCibxWOYnxHOUw3Jh0cELzAhMSoK9G68OIuS0W', 'admin', 0),
(53, 'a', '$2y$10$kHgxqCDPTTxs4Z7HZnVPcOVyAi.XMhWIhptUyKJasTE0P5rrBc.pW', 'user', 1),
(54, 'games', '$2y$10$Rm/Gk6/OBmWcJPSsBiPb7O/SNPj0eMZ07gSV4mVxmD67jbAdynqxK', 'user', 0),
(55, 'ok', '$2y$10$R3gwNfNgDgXvFDaEKojqt.Q32iURbMGxQ5fhJeFXs4u3yyIjclYvq', 'user', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cashouts`
--
ALTER TABLE `cashouts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `deposits`
--
ALTER TABLE `deposits`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `freeplay_deposits`
--
ALTER TABLE `freeplay_deposits`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
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
-- AUTO_INCREMENT for table `cashouts`
--
ALTER TABLE `cashouts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `deposits`
--
ALTER TABLE `deposits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `freeplay_deposits`
--
ALTER TABLE `freeplay_deposits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
