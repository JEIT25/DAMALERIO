<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/db_connect.php';
require_once __DIR__ . '/../includes/auth.php';

requireRole('admin');

// Fetch only consumers
$result = $conn->query("SELECT id, firstName, lastName, username, email, is_blocked FROM users WHERE role = 'consumer' ORDER BY username");
$list = [];
while ($row = $result->fetch_assoc()) {
    $list[] = $row;
}
$conn->close();
echo json_encode(['success' => true, 'consumers' => $list]);
?>
