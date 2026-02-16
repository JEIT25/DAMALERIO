-- ============================================================
-- FoodGrab: Roles, Restaurants, Menu, Orders, Payments
-- Run this after online_food_delivery_system.sql
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- Add role to users (consumer | admin | superadmin) — only if column does not exist
DROP PROCEDURE IF EXISTS add_role_if_missing;
DELIMITER //
CREATE PROCEDURE add_role_if_missing()
BEGIN
  IF NOT EXISTS (
    SELECT 1 FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'role'
  ) THEN
    ALTER TABLE `users`
      ADD COLUMN `role` ENUM('consumer','admin','superadmin') NOT NULL DEFAULT 'consumer' AFTER `secure_answer`;
  END IF;
END//
DELIMITER ;
CALL add_role_if_missing();
DROP PROCEDURE IF EXISTS add_role_if_missing;

-- Set one existing user as superadmin (run after first deploy; replace 'jeit25' with your admin username)
-- UPDATE `users` SET `role` = 'superadmin' WHERE `username` = 'jeit25' LIMIT 1;

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
