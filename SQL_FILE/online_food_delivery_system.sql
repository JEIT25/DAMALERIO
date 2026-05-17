-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 17, 2026 at 04:56 AM
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
-- Database: `online_food_delivery_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_creation_requests`
--

CREATE TABLE `admin_creation_requests` (
  `id` int(11) NOT NULL,
  `requested_by` varchar(11) NOT NULL COMMENT 'User ID of superadmin requesting',
  `target_username` varchar(50) NOT NULL,
  `target_email` varchar(100) NOT NULL,
  `target_role` enum('admin','superadmin') NOT NULL,
  `target_firstName` varchar(50) NOT NULL,
  `target_lastName` varchar(50) NOT NULL,
  `reason` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `reviewed_by` varchar(11) DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `review_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` varchar(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL,
  `cart_id` int(11) NOT NULL,
  `menu_item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login_logs`
--

CREATE TABLE `login_logs` (
  `id` int(11) NOT NULL,
  `user_id` varchar(11) NOT NULL,
  `action` enum('login','logout') NOT NULL,
  `log_time` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_logs`
--

INSERT INTO `login_logs` (`id`, `user_id`, `action`, `log_time`) VALUES
(1, '0000-0001', 'logout', '2026-02-19 17:06:41'),
(2, '0000-0001', 'login', '2026-02-19 17:07:01'),
(3, '0000-0001', 'logout', '2026-02-19 17:08:20'),
(4, '0000-0001', 'login', '2026-02-19 17:08:28'),
(5, '0000-0001', 'login', '2026-02-19 17:09:49'),
(6, '0000-0001', 'logout', '2026-02-19 17:12:03'),
(7, '0000-0001', 'login', '2026-02-19 17:12:12'),
(8, '0000-0001', 'logout', '2026-02-19 17:13:39'),
(9, '0000-0001', 'login', '2026-02-19 17:13:51'),
(10, '0000-0001', 'login', '2026-02-19 17:14:49'),
(11, '0000-0001', 'login', '2026-02-19 17:14:57'),
(12, '0000-0001', 'login', '2026-02-19 17:15:17'),
(13, '0000-0001', 'logout', '2026-02-19 17:18:36'),
(14, '0000-0001', 'login', '2026-02-19 17:18:38'),
(15, '0000-0001', 'login', '2026-02-19 17:34:23'),
(16, '0000-0001', 'login', '2026-02-19 17:46:35'),
(17, '0000-0001', 'logout', '2026-02-19 20:21:29'),
(18, '0000-0001', 'login', '2026-02-19 20:21:31'),
(19, '0000-0001', 'login', '2026-02-19 21:07:45'),
(20, '0000-0001', 'login', '2026-02-19 21:30:08'),
(21, '0000-0001', 'login', '2026-02-19 21:30:14'),
(22, '0000-0001', 'logout', '2026-02-19 22:01:29'),
(23, '0000-0001', 'login', '2026-02-19 22:01:31'),
(24, '0000-0001', 'logout', '2026-02-19 22:20:49'),
(25, '0000-0001', 'login', '2026-02-19 22:20:51'),
(26, '0000-0001', 'logout', '2026-02-19 22:23:33'),
(27, '0000-0001', 'login', '2026-02-19 22:24:36'),
(28, '0000-0001', 'login', '2026-02-19 22:28:56'),
(29, '0000-0001', 'logout', '2026-02-19 22:57:54'),
(30, '0000-0001', 'login', '2026-02-19 22:59:38'),
(31, '0000-0001', 'logout', '2026-02-19 23:10:46'),
(32, '0000-0001', 'login', '2026-02-19 23:10:49'),
(33, '0000-0001', 'logout', '2026-02-19 23:11:33'),
(34, '0000-0001', 'login', '2026-02-19 23:11:35'),
(35, '0000-0001', 'logout', '2026-02-19 23:11:58'),
(36, '0000-0001', 'login', '2026-02-19 23:12:00'),
(37, '0000-0001', 'logout', '2026-02-19 23:13:45'),
(38, '0000-0001', 'login', '2026-02-19 23:13:48'),
(39, '0000-0001', 'login', '2026-02-19 23:18:39'),
(40, '0000-0001', 'login', '2026-02-19 23:21:36'),
(41, '0000-0001', 'logout', '2026-02-19 23:23:36'),
(42, '0000-0001', 'login', '2026-02-19 23:23:40'),
(43, '0000-0001', 'logout', '2026-02-19 23:51:48'),
(44, '0000-0001', 'login', '2026-02-19 23:52:04'),
(45, '0000-0001', 'logout', '2026-02-19 23:56:56'),
(46, '0000-0001', 'login', '2026-02-20 00:27:38'),
(47, '0000-0001', 'logout', '2026-02-20 08:49:23'),
(51, '0000-0001', 'login', '2026-02-24 08:37:23'),
(52, '0000-0001', 'login', '2026-02-24 08:37:41'),
(53, '0000-0001', 'login', '2026-02-24 08:42:51'),
(54, '0000-0001', 'logout', '2026-02-24 08:50:16'),
(56, '0000-0001', 'login', '2026-02-24 09:10:24'),
(57, '0000-0001', 'logout', '2026-02-24 09:11:48'),
(59, '0000-0001', 'login', '2026-02-24 09:13:28'),
(60, '0000-0001', 'login', '2026-02-24 09:14:01'),
(62, '0000-0001', 'login', '2026-02-24 09:15:33'),
(64, '0000-0001', 'login', '2026-02-24 09:22:01'),
(66, '0000-0001', 'login', '2026-02-24 09:24:51'),
(67, '0000-0001', 'login', '2026-02-24 09:25:15'),
(68, '0000-0001', 'login', '2026-02-24 09:28:17'),
(69, '0000-0001', 'login', '2026-02-24 09:30:22'),
(71, '0000-0001', 'login', '2026-02-24 09:31:11'),
(72, '0000-0001', 'login', '2026-02-24 09:34:58'),
(73, '0000-0001', 'login', '2026-02-24 09:35:13'),
(74, '0000-0001', 'login', '2026-02-24 09:35:55'),
(75, '0000-0001', 'login', '2026-02-24 09:36:12'),
(76, '0000-0001', 'logout', '2026-02-24 09:44:41'),
(77, '0000-0001', 'login', '2026-02-24 09:44:44'),
(78, '0000-0001', 'logout', '2026-02-24 09:44:56'),
(79, '0000-0001', 'login', '2026-02-24 09:45:05'),
(80, '0000-0001', 'logout', '2026-02-24 09:46:48'),
(83, '0000-0001', 'login', '2026-02-24 09:48:41'),
(84, '0000-0001', 'logout', '2026-02-24 10:00:52'),
(85, '0000-0001', 'logout', '2026-02-24 10:28:46'),
(87, '0000-0001', 'logout', '2026-05-08 18:15:56'),
(88, '0000-0001', 'login', '2026-05-08 18:16:59'),
(89, '0000-0001', 'logout', '2026-05-08 18:19:44'),
(90, '0000-0002', 'login', '2026-05-08 18:19:49'),
(91, '0000-0002', 'logout', '2026-05-08 18:21:46'),
(92, '0000-0001', 'login', '2026-05-08 18:28:53'),
(93, '0000-0001', 'logout', '2026-05-08 18:29:05'),
(94, '0000-0002', 'login', '2026-05-08 18:29:10'),
(95, '0000-0002', 'logout', '2026-05-08 18:30:55'),
(96, '0000-0002', 'login', '2026-05-08 18:34:30'),
(97, '0000-0002', 'logout', '2026-05-08 19:23:59'),
(100, '0000-0002', 'login', '2026-05-08 19:44:13'),
(101, '0000-0002', 'logout', '2026-05-08 19:46:42'),
(104, '0000-0002', 'login', '2026-05-08 19:46:56'),
(105, '0000-0002', 'logout', '2026-05-08 19:54:43'),
(106, '0000-0001', 'login', '2026-05-08 19:54:55'),
(107, '0000-0001', 'logout', '2026-05-08 19:54:56'),
(108, '0000-0002', 'login', '2026-05-08 19:55:00'),
(109, '0000-0002', 'logout', '2026-05-08 19:55:05'),
(110, '0000-0001', 'login', '2026-05-08 19:55:08'),
(111, '0000-0001', 'login', '2026-05-08 20:17:45'),
(112, '0000-0001', 'logout', '2026-05-08 20:17:48'),
(113, '0000-0002', 'login', '2026-05-08 20:18:18'),
(114, '0000-0002', 'logout', '2026-05-08 20:21:17'),
(117, '0000-0002', 'login', '2026-05-08 20:21:45'),
(118, '0000-0001', 'logout', '2026-05-08 20:41:58'),
(119, '0000-0001', 'login', '2026-05-08 20:42:04'),
(120, '0000-0002', 'logout', '2026-05-08 20:42:26'),
(122, '0000-0001', 'logout', '2026-05-08 20:45:09'),
(124, '0000-0002', 'login', '2026-05-08 20:46:14'),
(125, '0000-0001', 'login', '2026-05-08 20:47:21'),
(126, '0000-0002', 'logout', '2026-05-08 20:56:04'),
(127, '2022-0909', 'login', '2026-05-08 22:00:08'),
(128, '2022-0909', 'logout', '2026-05-08 22:00:12'),
(131, '0000-0002', 'login', '2026-05-11 22:49:54'),
(132, '0000-0002', 'logout', '2026-05-11 22:49:56'),
(133, '0000-0001', 'login', '2026-05-11 22:50:00'),
(134, '0000-0001', 'logout', '2026-05-11 22:50:27'),
(135, '0000-0002', 'login', '2026-05-11 22:50:32'),
(136, '0000-0002', 'logout', '2026-05-11 22:56:18'),
(137, '0000-0002', 'login', '2026-05-12 09:34:30'),
(138, '0000-0002', 'logout', '2026-05-12 09:34:44'),
(139, '0000-0001', 'login', '2026-05-17 10:52:06'),
(140, '0000-0001', 'logout', '2026-05-17 10:52:59'),
(143, '0000-0001', 'login', '2026-05-17 10:53:47'),
(144, '0000-0001', 'logout', '2026-05-17 10:55:40'),
(147, '0000-0001', 'login', '2026-05-17 10:56:02');

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL,
  `restaurant_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `restaurant_id`, `name`, `description`, `price`, `image_path`, `is_available`, `created_at`) VALUES
(1, 1, 'Chicken Burger', 'Crispy chicken with lettuce and mayo', 89.00, NULL, 1, '2026-02-19 09:06:08'),
(2, 1, 'Beef Burger', 'Classic beef patty with cheese', 99.00, NULL, 1, '2026-02-19 09:06:08'),
(3, 1, 'Pancit Canton', 'Stir-fried noodles with vegetables', 65.00, NULL, 1, '2026-02-19 09:06:08'),
(4, 1, 'Halo-Halo', 'Mixed shaved ice dessert', 75.00, NULL, 1, '2026-02-19 09:06:08'),
(5, 2, 'Fish Ball (10 pcs)', 'Street-style fish balls with sauce', 35.00, NULL, 1, '2026-02-19 09:06:08'),
(6, 2, 'Isaw (5 sticks)', 'Grilled chicken intestines', 50.00, NULL, 1, '2026-02-19 09:06:08'),
(7, 2, 'Turon (3 pcs)', 'Fried banana spring rolls', 45.00, NULL, 1, '2026-02-19 09:06:08'),
(8, 3, 'Chao fan', '', 99.00, NULL, 0, '2026-02-19 23:50:47');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` varchar(11) NOT NULL,
  `restaurant_id` int(11) NOT NULL,
  `status` enum('pending','confirmed','preparing','out_for_delivery','delivered','cancelled') NOT NULL DEFAULT 'pending',
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `delivery_address` text NOT NULL,
  `notes` text DEFAULT NULL,
  `payment_method_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `restaurant_id`, `status`, `total_amount`, `delivery_address`, `notes`, `payment_method_id`, `created_at`, `updated_at`) VALUES
(1, '0000-0001', 2, 'pending', 90.00, 'Purok 5, Baranggay 6, Cabadbaran City, Agusan Del Norte 8605, Philippines', '', NULL, '2026-02-19 23:18:21', '2026-02-19 23:18:21'),
(2, '0000-0001', 1, 'delivered', 290.00, 'Purok 5, Baranggay 6, Cabadbaran City, Agusan Del Norte 8605, Philippines', '', NULL, '2026-02-19 23:18:21', '2026-02-20 01:20:38');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `menu_item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unit_price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `quantity`, `unit_price`, `subtotal`) VALUES
(1, 1, 7, 2, 45.00, 90.00),
(2, 2, 4, 3, 75.00, 225.00),
(3, 2, 3, 1, 65.00, 65.00);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_otp`
--

CREATE TABLE `password_reset_otp` (
  `id` int(11) NOT NULL,
  `user_id` varchar(11) NOT NULL,
  `otp_code` varchar(6) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_methods`
--

CREATE TABLE `payment_methods` (
  `id` int(11) NOT NULL,
  `user_id` varchar(11) NOT NULL,
  `type` enum('cash_on_delivery','gcash','card','bank') NOT NULL DEFAULT 'cash_on_delivery',
  `label` varchar(50) NOT NULL COMMENT 'e.g. GCash 09xx',
  `details` varchar(255) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `restaurants`
--

CREATE TABLE `restaurants` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `restaurants`
--

INSERT INTO `restaurants` (`id`, `name`, `description`, `address`, `image_path`, `is_active`, `created_at`) VALUES
(1, 'FoodGrab Kitchen', 'Fresh local flavors and quick bites.', 'Purok 4, Barangay 9, Cabadbaran City', NULL, 1, '2026-02-19 09:06:08'),
(2, 'Street Bites', 'Street food favorites delivered.', 'Downtown Cabadbaran', NULL, 1, '2026-02-19 09:06:08'),
(3, 'Chow King', '', '', '', 0, '2026-02-19 23:50:30');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` varchar(11) NOT NULL,
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `middleInitial` varchar(1) DEFAULT NULL,
  `extension` varchar(10) DEFAULT NULL,
  `sex` enum('male','female') NOT NULL,
  `birthdate` date NOT NULL,
  `age` int(11) NOT NULL,
  `purok` varchar(50) NOT NULL,
  `barangay` varchar(50) NOT NULL,
  `city` varchar(50) NOT NULL,
  `province` varchar(50) NOT NULL,
  `zipCode` varchar(10) NOT NULL,
  `country` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `secure_question` varchar(100) DEFAULT NULL,
  `secure_answer` varchar(255) DEFAULT NULL,
  `secure_question2` varchar(100) DEFAULT NULL,
  `secure_answer2` varchar(255) DEFAULT NULL,
  `secure_question3` varchar(100) DEFAULT NULL,
  `secure_answer3` varchar(255) DEFAULT NULL,
  `role` enum('consumer','admin','superadmin') NOT NULL DEFAULT 'consumer',
  `is_blocked` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('pending','active','rejected') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstName`, `lastName`, `middleInitial`, `extension`, `sex`, `birthdate`, `age`, `purok`, `barangay`, `city`, `province`, `zipCode`, `country`, `username`, `email`, `password`, `secure_question`, `secure_answer`, `secure_question2`, `secure_answer2`, `secure_question3`, `secure_answer3`, `role`, `is_blocked`, `status`) VALUES
('0000-0001', 'Fanny', 'Lightborn', '', '', 'male', '2000-02-02', 26, 'Purok 5', 'Baranggay 6', 'Cabadbaran City', 'Agusan Del Norte', '8605', 'Philippines', 'fanny21', 'fanny@gmail.com', '$2y$10$kegL1i4v3DBT7GYkEqoAKufipuMoc3tDESp6tCAI/z2b3CMkhCWgC', '1. Who is your bestfriend in elementary? *', '$2y$10$oAxzxNREO5zqOOqdlv64MOrSILWtPDXTBAI9zKBaRexUP9/3mPBp2', '2. What is the name of your pet? *', '$2y$10$qe8S/KD9Iff/3q5Ea.J5duSHhZS6mCZG70/YbPoCqfuTwJA8VrM06', '3. Who is your favorite teacher in highschool? *', '$2y$10$9IwiBrtHZBdldBDT.gYVnuALtbRNyEYEsHKWXmerwlpDvaHb2/iTe', 'superadmin', 0, 'active'),
('0000-0002', 'Kagura', 'Nyks', '', '', 'male', '2000-02-02', 26, 'Purok 5', 'Baranggay 6', 'Cabadbaran City', 'Agusan Del Norte', '8605', 'Philippines', 'kagura21', 'kagura@gmail.com', '$2y$10$KEHj4Eb2RTS4qaTUbtnEsugoJOujqcvxiI8J/S0.T/PIDjYNLGEzq', '1. Who is your bestfriend in elementary? *', '$2y$10$MvcpF9RZfcRxnba0bjOVmeIA2FkmTIS/5t4Pn3IlWZJNJVCBmrdH6', '2. What is the name of your pet? *', '$2y$10$gtUup7ukshnxnhNUko11Veou3FaPJI8tdvwstmG4FG5pvayPyjJ0y', '3. Who is your favorite teacher in highschool? *', '$2y$10$FaOdDbvwhRZTbqC4.yXu8eFtYMkr2/kfg5wxL9Kr7kT.zZUxDc4pu', 'admin', 0, 'active'),
('2022-0909', 'Maria', 'Cruz', '', '', 'female', '2003-02-02', 23, 'Purok 5', 'Baranggay 6', 'City of Cabadbaran', 'Agusan Del Norte', '8605', 'Philippines', 'maria21', 'maria@gmail.com', '$2y$10$Q.vSW.uIu4JA4kU7mNauf.o0./fwkyHjBCtlSh.J9m7jt.bvb4LBy', 'Who is your bestfriend in elementary?', '$2y$10$/FNv6W81EvU1x0Tdg7fAPOyW9TEf2J/2Wtv0ZjzoaAg5uo1Y1zyCS', 'What elementary school did you attend?', '$2y$10$NyzO.19X5yz6kLsPS.anq.8hQRsDs8RrzLESMV7TSUs9WsVyGzBmu', 'What street did you grow up on?', '$2y$10$mQitZHHHrBleeAkTaGdODu27nriLKBd4jLnVRXUl4kQMD1QxRp4k2', 'consumer', 0, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `user_block_requests`
--

CREATE TABLE `user_block_requests` (
  `id` int(11) NOT NULL,
  `requester_id` varchar(11) NOT NULL,
  `target_id` varchar(11) NOT NULL,
  `request_type` enum('block','unblock','registration') DEFAULT 'block',
  `reason` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_block_requests`
--

INSERT INTO `user_block_requests` (`id`, `requester_id`, `target_id`, `request_type`, `reason`, `status`, `created_at`, `updated_at`) VALUES
(10, '0000-0002', '2022-0909', 'block', 'Need blocking.', 'approved', '2026-05-08 20:20:32', '2026-05-08 20:21:14'),
(12, '0000-0002', '2022-0909', 'unblock', 'needs to unblocked', 'approved', '2026-05-08 20:40:24', '2026-05-08 20:40:30');

-- --------------------------------------------------------

--
-- Table structure for table `user_favorites`
--

CREATE TABLE `user_favorites` (
  `id` int(11) NOT NULL,
  `user_id` varchar(11) NOT NULL,
  `menu_item_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_creation_requests`
--
ALTER TABLE `admin_creation_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `requested_by` (`requested_by`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_cart_user` (`user_id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_cart_item_unique` (`cart_id`,`menu_item_id`),
  ADD KEY `fk_cartitem_menu` (`menu_item_id`);

--
-- Indexes for table `login_logs`
--
ALTER TABLE `login_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_logs_user` (`user_id`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_menu_restaurant` (`restaurant_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_orders_user` (`user_id`),
  ADD KEY `fk_orders_restaurant` (`restaurant_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_orderitems_order` (`order_id`),
  ADD KEY `fk_orderitems_menu` (`menu_item_id`);

--
-- Indexes for table `password_reset_otp`
--
ALTER TABLE `password_reset_otp`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_payment_user` (`user_id`);

--
-- Indexes for table `restaurants`
--
ALTER TABLE `restaurants`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `user_block_requests`
--
ALTER TABLE `user_block_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_block_requester` (`requester_id`),
  ADD KEY `fk_block_target` (`target_id`);

--
-- Indexes for table `user_favorites`
--
ALTER TABLE `user_favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_menu_unique` (`user_id`,`menu_item_id`),
  ADD KEY `fk_fav_user` (`user_id`),
  ADD KEY `fk_fav_menu` (`menu_item_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_creation_requests`
--
ALTER TABLE `admin_creation_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `login_logs`
--
ALTER TABLE `login_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=148;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `password_reset_otp`
--
ALTER TABLE `password_reset_otp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `restaurants`
--
ALTER TABLE `restaurants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_block_requests`
--
ALTER TABLE `user_block_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `user_favorites`
--
ALTER TABLE `user_favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_creation_requests`
--
ALTER TABLE `admin_creation_requests`
  ADD CONSTRAINT `admin_creation_requested_by_fk` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `fk_cartitem_cart` FOREIGN KEY (`cart_id`) REFERENCES `cart` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cartitem_menu` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `login_logs`
--
ALTER TABLE `login_logs`
  ADD CONSTRAINT `fk_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD CONSTRAINT `fk_menu_restaurant` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_restaurant` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_orderitems_menu` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_orderitems_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `password_reset_otp`
--
ALTER TABLE `password_reset_otp`
  ADD CONSTRAINT `password_reset_otp_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD CONSTRAINT `fk_payment_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_block_requests`
--
ALTER TABLE `user_block_requests`
  ADD CONSTRAINT `fk_block_requester` FOREIGN KEY (`requester_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_block_target` FOREIGN KEY (`target_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_favorites`
--
ALTER TABLE `user_favorites`
  ADD CONSTRAINT `fk_fav_menu` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_fav_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
