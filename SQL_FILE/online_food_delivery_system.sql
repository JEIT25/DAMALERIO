-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 18, 2026 at 12:56 PM
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
  `is_blocked` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstName`, `lastName`, `middleInitial`, `extension`, `sex`, `birthdate`, `age`, `purok`, `barangay`, `city`, `province`, `zipCode`, `country`, `username`, `email`, `password`, `secure_question`, `secure_answer`, `secure_question2`, `secure_answer2`, `secure_question3`, `secure_answer3`, `role`, `is_blocked`) VALUES
('1234-5671', 'Misisipiq', 'Garageq', '', '', 'male', '2005-02-02', 21, 'Purok 5', 'Baranggay 6', 'Cabadbaran City', 'Agusan Del Norte', '8605', 'Philippines', 'test1230', 'instructor@csucc.edu.ph0', '$2y$10$y1FO2dNYFV6JKT/UEJktL.LdvDBEufFpT51baui9.Db9Ar0BIeKtC', NULL, NULL, NULL, NULL, NULL, NULL, 'admin', 0),
('1234-5673', 'Misisipia', 'Garagea', '', '', 'male', '2003-02-02', 23, 'Purok 5', 'Baranggay 6', 'Cabadbaran City', 'Agusan Del Norte', '8605', 'Philippines', 'c', 'jeroldash.amora@gmail.coms', '$2y$10$fwsRY75nk5MRBtxv9F3p2eQw8DbfV/oPpx4C1NR37RijXRHpyPiVi', NULL, NULL, NULL, NULL, NULL, NULL, 'admin', 0),
('1234-5678', 'Misisipi', 'Garage', '', '', 'male', '2000-02-02', 26, 'Purok 5', 'Baranggay 6', 'Cabadbaran City', 'Agusan Del Norte', '8605', 'Philippines', 'test123', 'jeroldash.amora@gmail.com', '$2y$10$WYLdnkGFU1MVhqtZZGRbXupYQfP8eKtj//t5W6DmSVzRfm0ZMQGXy', '1. Who is your bestfriend in elementary? *', '$2y$10$oAxzxNREO5zqOOqdlv64MOrSILWtPDXTBAI9zKBaRexUP9/3mPBp2', '2. What is the name of your pet? *', '$2y$10$qe8S/KD9Iff/3q5Ea.J5duSHhZS6mCZG70/YbPoCqfuTwJA8VrM06', '3. Who is your favorite teacher in highschool? *', '$2y$10$9IwiBrtHZBdldBDT.gYVnuALtbRNyEYEsHKWXmerwlpDvaHb2/iTe', 'superadmin', 0),
('1234-7893', 'Misisipis', 'Garages', '', '', 'male', '2000-02-02', 26, 'Purok 5', 'Baranggay 6', 'Cabadbaran City', 'Agusan Del Norte', '8605', 'Philippines', 'test1234', 'instructors@csucc.edu.ph', '$2y$10$CMT77dHtHBWYQWhwzkNPce0YUDqmM5Ol3NA6S5vksxz81nyq//1KO', '1. Who is your bestfriend in elementary? *', '$2y$10$MvcpF9RZfcRxnba0bjOVmeIA2FkmTIS/5t4Pn3IlWZJNJVCBmrdH6', '2. What is the name of your pet? *', '$2y$10$gtUup7ukshnxnhNUko11Veou3FaPJI8tdvwstmG4FG5pvayPyjJ0y', '3. Who is your favorite teacher in highschool? *', '$2y$10$FaOdDbvwhRZTbqC4.yXu8eFtYMkr2/kfg5wxL9Kr7kT.zZUxDc4pu', 'admin', 0),
('2021-0909', 'Jeit', 'Hero', '', '', 'male', '2003-02-24', 22, 'Purok 4', 'Baranggay 9', 'Cabadbaran City', 'Agusan Del Norte', '8605', 'Philippines', 'jeit25', 'jeit@gmail.com', '$2y$10$8DbhGATwd2hb/4z/20eDfedQpqWzY2rAJsuzQpPO1NwGncwgOmDJK', '1. Who is your bestfriend in elementary? *', '$2y$10$jzfLVe1KCOKEh.B36Ld6TuiLbPVbmxNrP1qFOJTrDGTSbN7P2SyCa', '2. What is the name of your pet? *', '$2y$10$jzfLVe1KCOKEh.B36Ld6TuiLbPVbmxNrP1qFOJTrDGTSbN7P2SyCa', '3. Who is your favorite teacher in highschool? *', '$2y$10$jzfLVe1KCOKEh.B36Ld6TuiLbPVbmxNrP1qFOJTrDGTSbN7P2SyCa', 'consumer', 1),
('2123-4324', 'Misisipise', 'Garagef', '', '', 'male', '2000-02-02', 26, 'Purok 5', 'Baranggay 6', 'Cabadbaran City', 'Agusan Del Norte', '8605', 'Philippines', 'test123449', 'instructor@csucc.edu.phse', '$2y$10$u0jEWDNSHQGQ3DH5WfoEwOOF8kY8/bvBHBzBk/DSA.93ckNGVYPE2', '1. Who is your bestfriend in elementary? *', '$2y$10$ON2Feay1QS4EeWQy6VvCOefoG/d1xCObDevFOfKZecSwQsmWrP7UO', '2. What is the name of your pet? *', '$2y$10$f/I0naWji.nyV0pJYORJg.dnez1PnYeh1Xg7k9abhRRSO1CKMBZrS', '3. Who is your favorite teacher in highschool? *', '$2y$10$DWEHIiVLDal.X5DJVRw7d.cazyT6wKi4qdv8qx2hQaycWDqdysHjC', 'admin', 0),
('5675-6767', 'Misisipiss', 'Garagesd', '', '', 'male', '2003-02-02', 23, 'Purok 5', 'Baranggay 6', 'Cabadbaran City', 'Agusan Del Norte', '8605', 'Philippines', 'test123447', 'instructor@csucc.edu.phs', '$2y$10$a4GU/yYhKfQ/s/e7mHB7r.I8aw0S14XXKPQSusFXVEA6KiJkqKAOu', '1. Who is your bestfriend in elementary? *', '$2y$10$GUquMpzyEnK/j2UtEH6xE.EzN.9AGxVOVRu.a7hiWe7iXgrJWCMO6', '2. What is the name of your pet? *', '$2y$10$iNGku3TpBTktzyXwiTKPjev4B3oVkroZyteYcvpwTxJhb9Ay9lFVW', '3. Who is your favorite teacher in highschool? *', '$2y$10$YEOsUrVLuX7WyNzt3wqjzeb88cWP8wRQQRUAi6CJVNshezh6Fd3zW', 'admin', 0);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_otp`
--

CREATE TABLE IF NOT EXISTS `password_reset_otp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(11) NOT NULL,
  `otp_code` varchar(6) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `password_reset_otp_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Restaurants
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `restaurants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Menu items (per restaurant)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `menu_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `restaurant_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_menu_restaurant` (`restaurant_id`),
  CONSTRAINT `fk_menu_restaurant` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Order status enum and orders table
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(11) NOT NULL,
  `restaurant_id` int(11) NOT NULL,
  `status` ENUM('pending','confirmed','preparing','out_for_delivery','delivered','cancelled') NOT NULL DEFAULT 'pending',
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `delivery_address` text NOT NULL,
  `notes` text DEFAULT NULL,
  `payment_method_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_orders_user` (`user_id`),
  KEY `fk_orders_restaurant` (`restaurant_id`),
  CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_orders_restaurant` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Order items (line items per order)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `menu_item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unit_price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_orderitems_order` (`order_id`),
  KEY `fk_orderitems_menu` (`menu_item_id`),
  CONSTRAINT `fk_orderitems_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_orderitems_menu` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- User payment methods (consumer)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `payment_methods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(11) NOT NULL,
  `type` ENUM('cash_on_delivery','gcash','card','bank') NOT NULL DEFAULT 'cash_on_delivery',
  `label` varchar(50) NOT NULL COMMENT 'e.g. GCash 09xx',
  `details` varchar(255) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_payment_user` (`user_id`),
  CONSTRAINT `fk_payment_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- User favorites (menu items)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `user_favorites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(11) NOT NULL,
  `menu_item_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_menu_unique` (`user_id`,`menu_item_id`),
  KEY `fk_fav_user` (`user_id`),
  KEY `fk_fav_menu` (`menu_item_id`),
  CONSTRAINT `fk_fav_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_fav_menu` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Seed sample restaurant and menu (optional)
-- --------------------------------------------------------
INSERT INTO `restaurants` (`name`, `description`, `address`, `is_active`) VALUES
('FoodGrab Kitchen', 'Fresh local flavors and quick bites.', 'Purok 4, Barangay 9, Cabadbaran City', 1),
('Street Bites', 'Street food favorites delivered.', 'Downtown Cabadbaran', 1);

SET @r1 = (SELECT id FROM restaurants WHERE name = 'FoodGrab Kitchen' LIMIT 1);
SET @r2 = (SELECT id FROM restaurants WHERE name = 'Street Bites' LIMIT 1);

INSERT INTO `menu_items` (`restaurant_id`, `name`, `description`, `price`, `is_available`) VALUES
(@r1, 'Chicken Burger', 'Crispy chicken with lettuce and mayo', 89.00, 1),
(@r1, 'Beef Burger', 'Classic beef patty with cheese', 99.00, 1),
(@r1, 'Pancit Canton', 'Stir-fried noodles with vegetables', 65.00, 1),
(@r1, 'Halo-Halo', 'Mixed shaved ice dessert', 75.00, 1),
(@r2, 'Fish Ball (10 pcs)', 'Street-style fish balls with sauce', 35.00, 1),
(@r2, 'Isaw (5 sticks)', 'Grilled chicken intestines', 50.00, 1),
(@r2, 'Turon (3 pcs)', 'Fried banana spring rolls', 45.00, 1);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/* ... existing content ... */

-- --------------------------------------------------------
-- Login Logs
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `login_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(11) NOT NULL,
  `action` ENUM('login', 'logout') NOT NULL,
  `log_time` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_logs_user` (`user_id`),
  CONSTRAINT `fk_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- User Block Requests (Admin requests to block Consumer)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `user_block_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `requester_id` varchar(11) NOT NULL,
  `target_id` varchar(11) NOT NULL,
  `reason` text DEFAULT NULL,
  `status` ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_block_requester` (`requester_id`),
  KEY `fk_block_target` (`target_id`),
  CONSTRAINT `fk_block_requester` FOREIGN KEY (`requester_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_block_target` FOREIGN KEY (`target_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Shopping Cart
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_cart_user` (`user_id`),
  CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Cart Items
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cart_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cart_id` int(11) NOT NULL,
  `menu_item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_cart_item_unique` (`cart_id`, `menu_item_id`),
  KEY `fk_cartitem_menu` (`menu_item_id`),
  CONSTRAINT `fk_cartitem_cart` FOREIGN KEY (`cart_id`) REFERENCES `cart` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cartitem_menu` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
