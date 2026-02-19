-- ============================================================
-- FoodGrab: Enhanced Security Features
-- Adds 3 security questions, login logs, and improved OTP system
-- Run after existing schema files
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- Add 2 additional security questions to users table
DROP PROCEDURE IF EXISTS add_security_questions;
DELIMITER //
CREATE PROCEDURE add_security_questions()
BEGIN
  -- Add second security question if not exists
  IF NOT EXISTS (
    SELECT 1 FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'secure_question2'
  ) THEN
    ALTER TABLE `users`
      ADD COLUMN `secure_question2` varchar(100) DEFAULT NULL AFTER `secure_answer`,
      ADD COLUMN `secure_answer2` varchar(255) DEFAULT NULL AFTER `secure_question2`,
      ADD COLUMN `secure_question3` varchar(100) DEFAULT NULL AFTER `secure_answer2`,
      ADD COLUMN `secure_answer3` varchar(255) DEFAULT NULL AFTER `secure_question3`;
  END IF;
END//
DELIMITER ;
CALL add_security_questions();
DROP PROCEDURE IF EXISTS add_security_questions;

-- Create login_logs table for superadmin viewing
CREATE TABLE IF NOT EXISTS `login_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `user_role` enum('consumer','admin','superadmin') NOT NULL,
  `login_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `login_status` enum('success','failed') NOT NULL DEFAULT 'success',
  `failure_reason` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_login_time` (`login_time`),
  KEY `idx_user_role` (`user_role`),
  CONSTRAINT `login_logs_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Enhance password_reset_otp table with resend tracking and better expiration
DROP PROCEDURE IF EXISTS enhance_otp_table;
DELIMITER //
CREATE PROCEDURE enhance_otp_table()
BEGIN
  -- Add resend tracking columns if not exist
  IF NOT EXISTS (
    SELECT 1 FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'password_reset_otp' AND COLUMN_NAME = 'resend_count'
  ) THEN
    ALTER TABLE `password_reset_otp`
      ADD COLUMN `resend_count` int(11) NOT NULL DEFAULT 0 AFTER `used`,
      ADD COLUMN `last_resend_at` timestamp NULL DEFAULT NULL AFTER `resend_count`,
      ADD COLUMN `ip_address` varchar(45) DEFAULT NULL AFTER `expires_at`,
      MODIFY COLUMN `expires_at` datetime NOT NULL DEFAULT (DATE_ADD(NOW(), INTERVAL 15 MINUTE));
  END IF;
END//
DELIMITER ;
CALL enhance_otp_table();
DROP PROCEDURE IF EXISTS enhance_otp_table;

-- Create admin account creation requests table
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

-- Add indexes for better performance
CREATE INDEX IF NOT EXISTS idx_users_role ON users(role);
CREATE INDEX IF NOT EXISTS idx_orders_user_created ON orders(user_id, created_at);
CREATE INDEX IF NOT EXISTS idx_menu_items_restaurant_available ON menu_items(restaurant_id, is_available);

COMMIT;
