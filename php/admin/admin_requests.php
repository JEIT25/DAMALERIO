<?php
/**
 * Admin Creation Requests Management
 * View and approve/reject admin account creation requests
 */
session_start();
require_once '../database/db_connect.php';
require_once '../includes/auth.php';

// Only superadmin can access this page
requireRole('superadmin');

$error = '';
$success = '';

// Handle approval/rejection
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $request_id = (int)$_POST['request_id'];
    $action = $_POST['action']; // approve or reject
    $review_notes = trim($_POST['review_notes']);

    if ($action === 'approve') {
        // Get request details
        $stmt = $conn->prepare("SELECT * FROM admin_creation_requests WHERE id = ? AND status = 'pending'");
        $stmt->bind_param('i', $request_id);
        $stmt->execute();
        $request = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($request) {
            // Generate temporary password
            $temp_password = bin2hex(random_bytes(4)); // 8-character temp password
            $hashed_password = password_hash($temp_password, PASSWORD_DEFAULT);

            // Create user account
            $user_id = 'ADMIN-' . date('Y') . '-' . sprintf('%04d', mt_rand(1, 9999));

            $stmt = $conn->prepare("INSERT INTO users
                                   (id, firstName, lastName, username, email, password, role)
                                   VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('ssssss', $user_id, $request['target_firstName'],
                $request['target_lastName'], $request['target_username'],
                $request['target_email'], $hashed_password, $request['target_role']);

            if ($stmt->execute()) {
                // Update request status
                $reviewed_by = $_SESSION['user']['id'];
                $stmt = $conn->prepare("UPDATE admin_creation_requests
                                       SET status = 'approved', reviewed_by = ?, reviewed_at = NOW(), review_notes = ?
                                       WHERE id = ?");
                $stmt->bind_param('ssi', $reviewed_by, $review_notes, $request_id);
                $stmt->execute();
                $stmt->close();

                // Send welcome email with temporary password
                if (sendOTPEmail($request['target_email'], "Your temporary password is: {$temp_password}")) {
                    $success = 'Admin account created successfully! Temporary password sent to email.';
                }
                else {
                    $success = 'Admin account created! (Email sending failed, password: ' . $temp_password . ')';
                }
            }
            else {
                $error = 'Failed to create admin account';
            }
        }
    }
    elseif ($action === 'reject') {
        // Update request status
        $reviewed_by = $_SESSION['user']['id'];
        $stmt = $conn->prepare("UPDATE admin_creation_requests
                               SET status = 'rejected', reviewed_by = ?, reviewed_at = NOW(), review_notes = ?
                               WHERE id = ?");
        $stmt->bind_param('ssi', $reviewed_by, $review_notes, $request_id);

        if ($stmt->execute()) {
            $success = 'Request rejected successfully';
        }
        else {
            $error = 'Failed to reject request';
        }
        $stmt->close();
    }
}

// Get all requests
$stmt = $conn->prepare("SELECTacr.*, u.firstName as requester_name
                        FROM admin_creation_requests acr
                        LEFT JOIN users u ON acr.requested_by = u.id
                        ORDER BY acr.created_at DESC");
$stmt->execute();
$requests = $stmt->get_result();
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Requests - FoodGrab</title>
    <link rel="stylesheet" href="../../css/design-system.css">
    <link rel="stylesheet" href="../../css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .admin-table { width: 100%; border-collapse: collapse; background: var(--bg-card); border-radius: 8px; overflow: hidden; }
        .admin-table th, .admin-table td { padding: 12px; text-align: left; border-bottom: 1px solid var(--border-color); }
        .admin-table th { background: var(--bg-body); font-weight: 600; }
        .action-form { display: inline-block; margin: 2px; }
        .small-input { width: 120px; margin-right: 5px; padding: 6px; border: 1px solid var(--border-color); border-radius: 4px; }
        .btn-small { padding: 4px 8px; font-size: 0.8em; border-radius: 4px; cursor: pointer; border: none; color: white; }
        .btn-success { background-color: #10b981; }
        .btn-danger { background-color: #ef4444; }
        .badge { padding: 4px 8px; border-radius: 12px; font-size: 0.8em; font-weight: 500; color: white; }
        .badge-admin { background-color: #3b82f6; }
        .badge-superadmin { background-color: #dc2626; }
        .badge-pending { background-color: #f59e0b; }
        .badge-approved { background-color: #10b981; }
        .badge-rejected { background-color: #6b7280; }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/layout/navbar.php'; ?>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <?php $currentPage = 'admin_requests';
include __DIR__ . '/../includes/layout/sidebar.php'; ?>

        <main class="dashboard-main">
        <div class="admin-container">
            <h1>Admin Account Creation Requests</h1>

            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php
endif; ?>

            <?php if ($success): ?>
                <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
            <?php
endif; ?>

            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Target User</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Requested By</th>
                            <th>Created</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($request = $requests->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $request['id']; ?></td>
                                <td>
                                    <?php echo htmlspecialchars($request['target_firstName'] . ' ' . $request['target_lastName']); ?>
                                    <br>
                                    <small>@<?php echo htmlspecialchars($request['target_username']); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($request['target_email']); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $request['target_role']; ?>">
                                        <?php echo ucfirst($request['target_role']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($request['requester_name'] ?? 'Unknown'); ?></td>
                                <td><?php echo date('M j, Y g:i A', strtotime($request['created_at'])); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $request['status']; ?>">
                                        <?php echo ucfirst($request['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($request['status'] === 'pending'): ?>
                                        <form method="POST" class="action-form" onsubmit="return confirm('Approve this request?')">
                                            <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                            <input type="hidden" name="action" value="approve">
                                            <input type="text" name="review_notes" placeholder="Notes (optional)" class="small-input">
                                            <button type="submit" class="btn-small btn-success">Approve</button>
                                        </form>
                                        <form method="POST" class="action-form" onsubmit="return confirm('Reject this request?')">
                                            <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                            <input type="hidden" name="action" value="reject">
                                            <input type="text" name="review_notes" placeholder="Reason" class="small-input" required>
                                            <button type="submit" class="btn-small btn-danger">Reject</button>
                                        </form>
                                    <?php
    else: ?>
                                        <?php if ($request['review_notes']): ?>
                                            <small><?php echo htmlspecialchars($request['review_notes']); ?></small>
                                        <?php
        endif; ?>
                                    <?php
    endif; ?>
                                </td>
                            </tr>
                        <?php
endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        </main>
        <?php include __DIR__ . '/../includes/layout/footer.php'; ?>
    </div>

    <style>
        .action-form {
            display: inline-block;
            margin: 2px;
        }
        .small-input {
            width: 120px;
            margin-right: 5px;
            padding: 4px;
        }
        .badge-admin { background-color: #3b82f6; }
        .badge-superadmin { background-color: #dc2626; }
        .badge-pending { background-color: #f59e0b; }
        .badge-approved { background-color: #10b981; }
        .badge-rejected { background-color: #6b7280; }
    </style>
</body>
</html>
