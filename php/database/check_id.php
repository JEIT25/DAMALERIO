<?php
// Start Session immediately
session_start();

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Check if db_connect exists
if (!file_exists('db_connect.php')) {
    echo json_encode(['exists' => false, 'error' => 'db_connect.php not found']);
    exit;
}

include('db_connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? trim($_POST['id']) : '';

    if (empty($id)) {
        echo json_encode(['exists' => false, 'error' => 'ID is empty']);
        exit;
    }

    // Query to fetch username and secure_question by ID
    $query = "SELECT username, secure_question, status FROM users WHERE id = ?";

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($username, $secure_question, $status);
            $stmt->fetch();

            if ($status === 'pending') {
                echo json_encode(['exists' => false, 'error' => 'Your account is still pending approval. You cannot reset your password yet.']);
                $stmt->close();
                exit;
            } elseif ($status === 'rejected') {
                echo json_encode(['exists' => false, 'error' => 'Your registration request was rejected. Please contact support.']);
                $stmt->close();
                exit;
            }

            // SAVE ID TO SESSION (Crucial Step)
            $_SESSION['reset_user_id'] = $id;

            // Success
            echo json_encode([
                'exists' => true,
                'secure_question' => $secure_question,
                'username' => $username,
                'id' => $id
            ]);
        } else {
            // ID not found
            echo json_encode(['exists' => false, 'error' => 'User ID not found']);
        }
        $stmt->close();
    } else {
        echo json_encode(['exists' => false, 'error' => 'SQL Error: ' . $conn->error]);
    }
}
?>