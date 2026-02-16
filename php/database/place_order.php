<?php
/**
 * Place order: expects JSON or POST with items[], delivery_address, notes, payment_method_id (optional).
 * Requires logged-in consumer.
 */
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/db_connect.php';
require_once __DIR__ . '/../includes/auth.php';

requireRole('consumer');
$user_id = $_SESSION['user']['id'];

$input = $_POST;
if (empty($input) && !empty(file_get_contents('php://input'))) {
    $input = json_decode(file_get_contents('php://input'), true) ?: [];
}

$items = $input['items'] ?? [];
$restaurant_id = isset($input['restaurant_id']) ? (int) $input['restaurant_id'] : 0;
$delivery_address = trim($input['delivery_address'] ?? '');
$notes = trim($input['notes'] ?? '');
$payment_method_id = isset($input['payment_method_id']) && $input['payment_method_id'] !== '' && $input['payment_method_id'] !== null
    ? (int) $input['payment_method_id'] : null;
if ($payment_method_id === 0) $payment_method_id = null;

if (empty($items) || $restaurant_id <= 0 || $delivery_address === '') {
    echo json_encode(['success' => false, 'error' => 'Missing items, restaurant, or delivery address']);
    exit;
}

$conn->begin_transaction();
try {
    $total = 0;
    foreach ($items as $item) {
        $qty = (int) ($item['quantity'] ?? 0);
        $price = (float) ($item['unit_price'] ?? 0);
        $total += $qty * $price;
    }

    $stmt = $conn->prepare("INSERT INTO orders (user_id, restaurant_id, status, total_amount, delivery_address, notes, payment_method_id) VALUES (?, ?, 'pending', ?, ?, ?, ?)");
    $stmt->bind_param('sisdsi', $user_id, $restaurant_id, $total, $delivery_address, $notes, $payment_method_id);
    $stmt->execute();
    $order_id = $conn->insert_id;
    $stmt->close();

    $ins = $conn->prepare("INSERT INTO order_items (order_id, menu_item_id, quantity, unit_price, subtotal) VALUES (?, ?, ?, ?, ?)");
    foreach ($items as $item) {
        $menu_item_id = (int) ($item['menu_item_id'] ?? 0);
        $qty = (int) ($item['quantity'] ?? 0);
        $unit_price = (float) ($item['unit_price'] ?? 0);
        $subtotal = $qty * $unit_price;
        $ins->bind_param('iiidd', $order_id, $menu_item_id, $qty, $unit_price, $subtotal);
        $ins->execute();
    }
    $ins->close();

    $conn->commit();
    echo json_encode(['success' => true, 'order_id' => $order_id]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'error' => 'Failed to place order']);
}
$conn->close();
