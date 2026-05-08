<?php
require 'php/database/db_connect.php';

$tables_to_fix = [
    'user_block_requests' => [
        ['name' => 'fk_block_requester', 'col' => 'requester_id'],
        ['name' => 'fk_block_target', 'col' => 'target_id']
    ],
    'user_favorites' => [
        ['name' => 'fk_fav_user', 'col' => 'user_id']
    ],
    'orders' => [
        ['name' => 'fk_orders_user', 'col' => 'user_id']
    ],
    'cart' => [
        ['name' => 'fk_cart_user', 'col' => 'user_id']
    ],
    'payment_methods' => [
        ['name' => 'fk_payment_user', 'col' => 'user_id']
    ],
    'login_logs' => [
        ['name' => 'fk_logs_user', 'col' => 'user_id']
    ],
    'admin_creation_requests' => [
        ['name' => 'admin_creation_requested_by_fk', 'col' => 'requested_by']
    ],
    'password_reset_otp' => [
        ['name' => 'password_reset_otp_ibfk_1', 'col' => 'user_id']
    ]
];

foreach ($tables_to_fix as $table => $constraints) {
    foreach ($constraints as $c) {
        $name = $c['name'];
        $col = $c['col'];
        
        echo "Fixing $table ($name)... ";
        try {
            // Drop existing
            $conn->query("ALTER TABLE `$table` DROP FOREIGN KEY `$name` ");
            // Add new with ON UPDATE CASCADE
            $conn->query("ALTER TABLE `$table` ADD CONSTRAINT `$name` FOREIGN KEY (`$col`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE");
            echo "Success\n";
        } catch (Exception $e) {
            echo "Failed: " . $e->getMessage() . "\n";
        }
    }
}
?>
