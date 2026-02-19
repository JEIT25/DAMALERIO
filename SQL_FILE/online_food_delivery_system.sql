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
('0001-0001', 'Admin', 'User', '', '', 'male', '1995-01-15', 31, 'Purok 1', 'Barangay 1', 'Cabadbaran City', 'Agusan Del Norte', '8605', 'Philippines', 'admin123', 'admin@foodgrab.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1. Who is your bestfriend in elementary? *', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2. What is the name of your pet? *', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3. Who is your favorite teacher in highschool? *', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 0),
('0001-0002', 'Super', 'Admin', '', '', 'male', '1990-06-20', 35, 'Purok 2', 'Barangay 2', 'Cabadbaran City', 'Agusan Del Norte', '8605', 'Philippines', 'superadmin123', 'superadmin@foodgrab.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1. Who is your bestfriend in elementary? *', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2. What is the name of your pet? *', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3. Who is your favorite teacher in highschool? *', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'superadmin', 0),
('0001-0003', 'Juan', 'Dela Cruz', 'A', '', 'male', '2000-03-10', 25, 'Purok 3', 'Barangay 3', 'Cabadbaran City', 'Agusan Del Norte', '8605', 'Philippines', 'consumer1', 'consumer1@foodgrab.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1. Who is your bestfriend in elementary? *', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2. What is the name of your pet? *', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3. Who is your favorite teacher in highschool? *', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'consumer', 0),
('0001-0004', 'Maria', 'Santos', 'B', '', 'female', '2001-07-22', 24, 'Purok 4', 'Barangay 4', 'Cabadbaran City', 'Agusan Del Norte', '8605', 'Philippines', 'consumer2', 'consumer2@foodgrab.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1. Who is your bestfriend in elementary? *', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2. What is the name of your pet? *', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3. Who is your favorite teacher in highschool? *', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'consumer', 0),
('0001-0005', 'Pedro', 'Reyes', 'C', '', 'male', '2002-11-05', 23, 'Purok 5', 'Barangay 5', 'Cabadbaran City', 'Agusan Del Norte', '8605', 'Philippines', 'consumer3', 'consumer3@foodgrab.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1. Who is your bestfriend in elementary? *', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2. What is the name of your pet? *', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3. Who is your favorite teacher in highschool? *', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'consumer', 0);

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

--
-- Dumping data for table `restaurants`
--

INSERT INTO `restaurants` (`id`, `name`, `description`, `address`, `image_path`, `is_active`, `created_at`) VALUES
(1, 'Jollibee', 'Home of the famous Chickenjoy — crispy, juicy, and loved by every Filipino.', 'P. Burgos St, Cabadbaran City, Agusan Del Norte', NULL, 1, '2026-01-01 08:00:00'),
(2, 'Chowking', 'Chinese-Filipino fast food — chao fan, siomai, lauriat meals, and halo-halo.', 'National Highway, Cabadbaran City, Agusan Del Norte', NULL, 1, '2026-01-01 08:00:00'),
(3, 'Mang Inasal', 'The home of chicken inasal with unlimited rice. Grilled to perfection.', 'J.C. Aquino Ave, Butuan City, Agusan Del Norte', NULL, 1, '2026-01-01 08:00:00'),
(4, 'Greenwich', 'Pizza, pasta, and more — the go-to place for barkada hangouts.', 'Montilla Blvd, Butuan City, Agusan Del Norte', NULL, 1, '2026-01-01 08:00:00'),
(5, 'Goldilocks', 'Filipino bakeshop and restaurant known for cakes, mamon, and classic Pinoy meals.', 'Langihan Rd, Butuan City, Agusan Del Norte', NULL, 1, '2026-01-01 08:00:00');

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

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `restaurant_id`, `name`, `description`, `price`, `image_path`, `is_available`) VALUES
-- Jollibee (restaurant_id = 1)
(1, 1, '1-pc Chickenjoy', 'Crispy fried chicken served with rice and gravy.', 89.00, NULL, 1),
(2, 1, 'Jolly Spaghetti', 'Sweet-style spaghetti with hotdog slices, ground meat, and cheese.', 65.00, NULL, 1),
(3, 1, 'Yumburger', 'Classic Jollibee hamburger with special dressing.', 45.00, NULL, 1),
(4, 1, 'Peach Mango Pie', 'Crispy flaky pie filled with peaches and mangoes.', 46.00, NULL, 1),
(5, 1, 'Palabok Fiesta', 'Rice noodles in shrimp sauce topped with chicharon and egg.', 138.00, NULL, 1),
-- Chowking (restaurant_id = 2)
(6, 2, 'Siomai Chao Fan', 'Savory fried rice topped with steamed pork siomai.', 95.00, NULL, 1),
(7, 2, 'Chinese-Style Fried Chicken', 'Crispy fried chicken with Chinese five-spice flavor.', 99.00, NULL, 1),
(8, 2, 'Sweet & Sour Pork Lauriat', 'Crispy pork in tangy sweet-and-sour sauce with rice and drink.', 215.00, NULL, 1),
(9, 2, 'Halo-Halo SuperSangkap', 'Crushed ice with sweet beans, fruits, leche flan, and ube ice cream.', 175.00, NULL, 1),
(10, 2, 'Beef Wonton Mami', 'Hot noodle soup with beef slices and wontons.', 115.00, NULL, 1),
-- Mang Inasal (restaurant_id = 3)
(11, 3, 'Chicken Inasal Paa', 'Grilled chicken leg marinated in local spices, served with unlimited rice.', 149.00, NULL, 1),
(12, 3, 'Chicken Inasal Pecho', 'Grilled chicken breast, juicy and flavorful with unlimited rice.', 159.00, NULL, 1),
(13, 3, 'Pork BBQ (2 pcs)', 'Two sticks of smoky sweet grilled pork skewers with rice.', 112.00, NULL, 1),
(14, 3, 'Extra Creamy Halo-Halo', 'Refreshing shaved ice dessert with sweet toppings and creamy milk.', 76.00, NULL, 1),
(15, 3, 'Palabok', 'Filipino-style rice noodles in rich savory sauce with toppings.', 99.00, NULL, 1),
-- Greenwich (restaurant_id = 4)
(16, 4, 'Hawaiian Overload Pizza', 'Loaded pizza with ham, pineapple, and extra cheese.', 162.00, NULL, 1),
(17, 4, 'Lasagna Supreme', 'Layers of pasta, rich meat sauce, and melted cheese.', 99.00, NULL, 1),
(18, 4, 'All-In Overload Pizza', 'Fully loaded pizza with pepperoni, ham, beef, and veggies.', 174.00, NULL, 1),
(19, 4, 'Winner Wings (4 pcs)', 'Crispy fried chicken wings in your choice of flavor.', 212.00, NULL, 1),
(20, 4, 'Ham & Cheese Pizzawrap', 'Toasted wrap filled with ham, cheese, and pizza sauce.', 66.00, NULL, 1),
-- Goldilocks (restaurant_id = 5)
(21, 5, 'Classic Mamon', 'Soft and fluffy Filipino sponge cake, lightly sweetened.', 35.00, NULL, 1),
(22, 5, 'Pork Adobo Meal', 'Tender pork braised in soy sauce and vinegar with rice.', 120.00, NULL, 1),
(23, 5, 'Pancit Bihon Bilao', 'Stir-fried thin rice noodles with vegetables and meat. Serves 5-6.', 350.00, NULL, 1),
(24, 5, 'Mocha Roll', 'Moist chocolate sponge cake rolled with mocha cream filling.', 280.00, NULL, 1),
(25, 5, 'Pinoy Spaghetti', 'Sweet Filipino-style spaghetti with hotdogs and cheese.', 85.00, NULL, 1);

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

-- --------------------------------------------------------
-- Approvals (Admin requests actions; Superadmin reviews)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `approvals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `requested_by` varchar(11) NOT NULL COMMENT 'User ID of admin requesting',
  `action_type` enum('delete_user','delete_restaurant','delete_menu_item','other') NOT NULL DEFAULT 'delete_user',
  `target_id` varchar(32) NOT NULL COMMENT 'ID of user / restaurant / menu_item',
  `target_type` enum('user','restaurant','menu_item') NOT NULL DEFAULT 'user',
  `reason` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `reviewed_by` varchar(11) DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `review_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `requested_by` (`requested_by`),
  KEY `status` (`status`),
  KEY `created_at` (`created_at`),
  CONSTRAINT `approvals_requested_by_fk` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `approvals_reviewed_by_fk` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Admin Creation Requests
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `admin_creation_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `requested_by` (`requested_by`),
  KEY `status` (`status`),
  CONSTRAINT `admin_creation_requested_by_fk` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `admin_creation_reviewed_by_fk` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
