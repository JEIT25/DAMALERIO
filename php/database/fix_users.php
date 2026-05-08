<?php
// Database Repair Script for DAMALERIO
// Standardizes user roles and ensures data consistency
require_once __DIR__ . '/db_connect.php';

echo "Starting database cleanup...\n";

// 1. Repair empty or null roles
$stmt = $conn->prepare("UPDATE users SET role = 'consumer' WHERE role = '' OR role IS NULL");
$stmt->execute();
$affected = $stmt->affected_rows;
echo "- Fixed $affected users with missing roles (set to 'consumer').\n";
$stmt->close();

// 2. Ensure role names are lowercase (for consistency with auth.php)
$stmt = $conn->prepare("UPDATE users SET role = LOWER(role)");
$stmt->execute();
$affected = $stmt->affected_rows;
echo "- Standardized $affected user roles to lowercase.\n";
$stmt->close();

// 3. Optional: Recalculate ages if any are 0 or null
$stmt = $conn->prepare("SELECT id, birthdate FROM users WHERE birthdate IS NOT NULL AND (age IS NULL OR age = 0)");
$stmt->execute();
$res = $stmt->get_result();
$count = 0;
while ($u = $res->fetch_assoc()) {
    $age = date_diff(date_create($u['birthdate']), date_create('today'))->y;
    $upd = $conn->prepare("UPDATE users SET age = ? WHERE id = ?");
    $upd->bind_param('is', $age, $u['id']);
    $upd->execute();
    $upd->close();
    $count++;
}
echo "- Recalculated age for $count users.\n";
$stmt->close();

echo "Cleanup complete!\n";
$conn->close();
?>
