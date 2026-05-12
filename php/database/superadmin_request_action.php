<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/db_connect.php';
require_once __DIR__ . '/../includes/auth.php';

requireRole('superadmin');

$request_id = trim($_POST['request_id'] ?? '');
$action = trim($_POST['action'] ?? ''); // 'approve' or 'reject'

if (!$request_id || !in_array($action, ['approve', 'reject'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

// Transaction
$conn->begin_transaction();

try {
    // Update request status
    $stmt = $conn->prepare("UPDATE user_block_requests SET status = ? WHERE id = ?");
    $status = ($action === 'approve') ? 'approved' : 'rejected';
    $stmt->bind_param('si', $status, $request_id);
    $stmt->execute();
    $stmt->close();

    // Get request details
    $stmt = $conn->prepare("SELECT target_id, request_type FROM user_block_requests WHERE id = ?");
    $stmt->bind_param('i', $request_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $target_id = $row['target_id'];
        $type = $row['request_type'];

        if ($type === 'registration') {
            // Update user status for registration (registered if approved, rejected if rejected)
            $user_status = ($action === 'approve') ? 'registered' : 'rejected';
            $stmt2 = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
            $stmt2->bind_param('ss', $user_status, $target_id);
            $stmt2->execute();
            $stmt2->close();
        }
        elseif ($action === 'approve') {
            // Block or Unblock user (only if approved)
            $is_blocked = ($type === 'block') ? 1 : 0;
            $stmt2 = $conn->prepare("UPDATE users SET is_blocked = ? WHERE id = ?");
            $stmt2->bind_param('is', $is_blocked, $target_id);
            $stmt2->execute();
            $stmt2->close();
        }
    }
    $stmt->close();

    $conn->commit();
    echo json_encode(['success' => true]);

}
catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

$conn->close();
?>
