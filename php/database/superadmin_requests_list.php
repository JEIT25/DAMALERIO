<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/db_connect.php';
require_once __DIR__ . '/../includes/auth.php';

requireRole('superadmin');

// Parameters
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = isset($_GET['limit']) ? max(1, (int)$_GET['limit']) : 10;
$offset = ($page - 1) * $limit;

$type = $_GET['type'] ?? '';
$status = $_GET['status'] ?? '';
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';

$where = [];
$params = [];
$types = "";

if ($type) { $where[] = "r.request_type = ?"; $params[] = $type; $types .= "s"; }
if ($status) { $where[] = "r.status = ?"; $params[] = $status; $types .= "s"; }
if ($start_date) { $where[] = "DATE(r.created_at) >= ?"; $params[] = $start_date; $types .= "s"; }
if ($end_date) { $where[] = "DATE(r.created_at) <= ?"; $params[] = $end_date; $types .= "s"; }

$where_clause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

// Get total count
$count_query = "SELECT COUNT(*) as total FROM user_block_requests r JOIN users u2 ON r.target_id = u2.id $where_clause";
$stmt_count = $conn->prepare($count_query);
if (!empty($params)) {
    $stmt_count->bind_param($types, ...$params);
}
$stmt_count->execute();
$total_result = $stmt_count->get_result()->fetch_assoc();
$total_records = $total_result['total'];
$total_pages = ceil($total_records / $limit);
$stmt_count->close();

$sql = "SELECT r.id, r.reason, r.status, r.created_at, r.request_type,
               u1.firstName as r_first, u1.lastName as r_last, u1.role as r_role,
               u2.firstName as t_first, u2.lastName as t_last, u2.username as t_username, u2.id as t_id, u2.role as t_role
        FROM user_block_requests r
        JOIN users u1 ON r.requester_id = u1.id
        JOIN users u2 ON r.target_id = u2.id
        $where_clause
        ORDER BY r.created_at DESC
        LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);
$bind_params = [...$params, $limit, $offset];
$bind_types = $types . "ii";
$stmt->bind_param($bind_types, ...$bind_params);
$stmt->execute();
$result = $stmt->get_result();

$list = [];
while ($row = $result->fetch_assoc()) {
    $list[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode([
    'success' => true, 
    'requests' => $list,
    'pagination' => [
        'current_page' => $page,
        'total_pages' => $total_pages,
        'total_requests' => $total_records,
        'limit' => $limit
    ]
]);
?>
