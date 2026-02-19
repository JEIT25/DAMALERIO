<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/db_connect.php';
require_once __DIR__ . '/../includes/auth.php';

requireRole('superadmin');

$user_id = trim($_POST['user_id'] ?? '');
$action = trim($_POST['action'] ?? ''); // 'block' or 'unblock'

if (!$user_id || !in_array($action, ['block', 'unblock'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

// Prevent blocking self
if ($user_id === $_SESSION['user']['id']) {
    echo json_encode(['success' => false, 'error' => 'Cannot block yourself']);
    exit;
}

$is_blocked = ($action === 'block') ? 1 : 0;

$stmt = $conn->prepare("UPDATE users SET is_blocked = ? WHERE id = ?");
$stmt->bind_param('is', $is_blocked, $user_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
}
else {
    echo json_encode(['success' => false, 'error' => 'Database error']);
}
$stmt->close();
$conn->close();
?>
