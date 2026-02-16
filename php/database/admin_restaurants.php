<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/db_connect.php';
require_once __DIR__ . '/../includes/auth.php';

requireRole(['admin', 'superadmin']);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = $conn->query("SELECT id, name, description, address, is_active FROM restaurants ORDER BY name");
    $list = [];
    while ($row = $result->fetch_assoc()) $list[] = $row;
    echo json_encode(['success' => true, 'restaurants' => $list]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'save') {
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $is_active = isset($_POST['is_active']) ? (int) $_POST['is_active'] : 1;
        if ($name === '') {
            echo json_encode(['success' => false, 'error' => 'Name required']);
            exit;
        }
        if ($id > 0) {
            $stmt = $conn->prepare("UPDATE restaurants SET name = ?, description = ?, address = ?, is_active = ? WHERE id = ?");
            $stmt->bind_param('sssii', $name, $description, $address, $is_active, $id);
        } else {
            $stmt = $conn->prepare("INSERT INTO restaurants (name, description, address, is_active) VALUES (?, ?, ?, ?)");
            $stmt->bind_param('sssi', $name, $description, $address, $is_active);
        }
        $stmt->execute();
        $newId = $id > 0 ? $id : $conn->insert_id;
        $stmt->close();
        echo json_encode(['success' => true, 'id' => $newId]);
    } elseif ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) { echo json_encode(['success' => false]); exit; }
        $stmt = $conn->prepare("DELETE FROM restaurants WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
}
$conn->close();
