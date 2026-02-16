-- ============================================================
-- FoodGrab: Approvals table (AMORA-style)
-- Admin requests delete; Superadmin reviews
-- Run after schema_roles_and_ordering.sql
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- Approvals: delete_user, delete_restaurant, delete_menu_item
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
