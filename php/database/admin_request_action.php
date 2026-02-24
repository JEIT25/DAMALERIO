<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/db_connect.php';
require_once __DIR__ . '/../includes/auth.php';

requireRole('admin');

$request_id = trim($_POST['request_id'] ?? '');
$action = trim($_POST['action'] ?? ''); // 'approve' or 'reject'

if (!$request_id || !in_array($action, ['approve', 'reject'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

// Transaction
$conn->begin_transaction();

try {
    // Get request details specifically for registration - Admins can only approve registrations
    $stmt = $conn->prepare("SELECT target_id, request_type FROM user_block_requests WHERE id = ?");
    $stmt->bind_param('i', $request_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $request = $res->fetch_assoc();
    $stmt->close();

    if (!$request || $request['request_type'] !== 'registration') {
        throw new Exception("Admins can only process registration requests.");
    }

    $target_id = $request['target_id'];
    $status = ($action === 'approve') ? 'approved' : 'rejected';

    // Update request status
    $stmt = $conn->prepare("UPDATE user_block_requests SET status = ? WHERE id = ?");
    $stmt->bind_param('si', $status, $request_id);
    $stmt->execute();
    $stmt->close();

    // Update user status
    $user_status = ($action === 'approve') ? 'active' : 'rejected';
    $stmt2 = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
    $stmt2->bind_param('ss', $user_status, $target_id);
    $stmt2->execute();
    $stmt2->close();

    $conn->commit();
    echo json_encode(['success' => true]);

}
catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

$conn->close();
?>
