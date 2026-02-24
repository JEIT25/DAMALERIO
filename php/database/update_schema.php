<?php
require_once 'db_connect.php';
// Add request_type column if it doesn't exist
$conn->query("ALTER TABLE user_block_requests ADD COLUMN request_type ENUM('block', 'unblock') DEFAULT 'block' AFTER target_id");
echo "Column added or already exists.";
?>
