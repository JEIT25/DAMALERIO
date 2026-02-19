<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/db_connect.php';
require_once __DIR__ . '/../includes/auth.php';

requireRole('superadmin');

$result = $conn->query("SELECT id, firstName, lastName, username, email, role, is_blocked FROM users ORDER BY role, username");
$list = [];
while ($row = $result->fetch_assoc()) {
    $list[] = $row;
}
$conn->close();
echo json_encode(['success' => true, 'users' => $list]);
