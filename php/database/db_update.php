<?php
require_once __DIR__ . '/db_connect.php';

echo "<h2>Applying Database Updates...</h2>";

// 1. Add is_blocked to users table if not exists
$res = $conn->query("SHOW COLUMNS FROM users LIKE 'is_blocked'");
if ($res->num_rows == 0) {
    if ($conn->query("ALTER TABLE users ADD COLUMN is_blocked TINYINT(1) DEFAULT 0")) {
        echo "Added 'is_blocked' column to users table.<br>";
    }
    else {
        echo "Error adding 'is_blocked': " . $conn->error . "<br>";
    }
}
else {
    echo "'is_blocked' column already exists.<br>";
}

// 2. Create login_logs table
$sql = "CREATE TABLE IF NOT EXISTS `login_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(11) NOT NULL,
  `action` ENUM('login', 'logout') NOT NULL,
  `log_time` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_logs_user` (`user_id`),
  CONSTRAINT `fk_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
if ($conn->query($sql))
    echo "Table 'login_logs' ready.<br>";
else
    echo "Error creating 'login_logs': " . $conn->error . "<br>";

// 3. Create user_block_requests table
$sql = "CREATE TABLE IF NOT EXISTS `user_block_requests` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
if ($conn->query($sql))
    echo "Table 'user_block_requests' ready.<br>";
else
    echo "Error creating 'user_block_requests': " . $conn->error . "<br>";

// 4. Create cart table
$sql = "CREATE TABLE IF NOT EXISTS `cart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_cart_user` (`user_id`),
  CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
if ($conn->query($sql))
    echo "Table 'cart' ready.<br>";
else
    echo "Error creating 'cart': " . $conn->error . "<br>";

// 5. Create cart_items table
$sql = "CREATE TABLE IF NOT EXISTS `cart_items` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
if ($conn->query($sql))
    echo "Table 'cart_items' ready.<br>";
else
    echo "Error creating 'cart_items': " . $conn->error . "<br>";

echo "<h3>Done! You can close this page.</h3>";
?>
