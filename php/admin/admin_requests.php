<?php
/**
 * My Requests (Admin)
 * View status of block requests submitted by this admin.
 */
session_start();
require_once '../database/db_connect.php';
require_once '../includes/auth.php';
requireRole('admin');

$admin_id = $_SESSION['user']['id'];

// Fetch my block requests
$stmt = $conn->prepare("SELECT ubr.*, u.firstName, u.lastName, u.username, u.email
                        FROM user_block_requests ubr
                        JOIN users u ON ubr.target_id = u.id
                        WHERE ubr.requester_id = ?
                        ORDER BY ubr.created_at DESC");
$stmt->bind_param('s', $admin_id);
$stmt->execute();
$requests = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Requests - FoodGrab</title>
    <link rel="stylesheet" href="../../css/design-system.css">
    <link rel="stylesheet" href="../../css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .data-table { width: 100%; border-collapse: collapse; background: var(--bg-card); border-radius: 8px; overflow: hidden; }
        .data-table th, .data-table td { padding: 12px; text-align: left; border-bottom: 1px solid var(--border-color); }
        .data-table th { background: var(--bg-body); font-weight: 600; }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/layout/navbar.php'; ?>
    <div class="dashboard-container">
        <?php $currentPage = 'admin_requests';
include __DIR__ . '/../includes/layout/sidebar.php'; ?>
        <main class="dashboard-main">
            <h1 class="page-title">My Requests</h1>
            <p class="page-subtitle">Track status of your consumer block requests.</p>

            <div class="table-container">
                <?php if ($requests->num_rows > 0): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Target User</th>
                                <th>Reason</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($r = $requests->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <div style="font-weight:600"><?php echo htmlspecialchars($r['firstName'] . ' ' . $r['lastName']); ?></div>
                                        <div class="muted small">@<?php echo htmlspecialchars($r['username']); ?></div>
                                    </td>
                                    <td><?php echo htmlspecialchars($r['reason']); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($r['created_at'])); ?></td>
                                    <td>
                                        <?php
        $statusClass = 'status-pending';
        if ($r['status'] === 'approved')
            $statusClass = 'status-ok';
        if ($r['status'] === 'rejected')
            $statusClass = 'status-trash';
?>
                                        <span class="status-badge no-dot <?php echo $statusClass; ?>"><?php echo ucfirst($r['status']); ?></span>
                                    </td>
                                    <td class="muted small"><?php echo htmlspecialchars($r['review_notes'] ?? '-'); ?></td>
                                </tr>
                            <?php
    endwhile; ?>
                        </tbody>
                    </table>
                <?php
else: ?>
                    <div class="empty-state" style="padding: 2rem; text-align: center; border: 2px dashed var(--border-light); border-radius: var(--radius-lg);">
                        <p style="color: var(--text-muted); margin: 0;">No requests found.</p>
                    </div>
                <?php
endif; ?>
            </div>
        </main>
        <?php include __DIR__ . '/../includes/layout/footer.php'; ?>
    </div>
</body>
</html>
