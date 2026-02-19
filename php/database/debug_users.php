<?php
require 'db_connect.php';

echo "User Data Debug (Security Questions)\n";
echo "ID | Username | Q1 | Q2 | Q3 | A1 Len | A2 Len | A3 Len\n";
echo "---------------------------------------------------------\n";

$result = $conn->query("SELECT id, username, secure_question, secure_question2, secure_question3, secure_answer, secure_answer2, secure_answer3 FROM users LIMIT 10");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo $row['id'] . " | " .
            $row['username'] . " | " .
            ($row['secure_question'] ?? 'NULL') . " | " .
            ($row['secure_question2'] ?? 'NULL') . " | " .
            ($row['secure_question3'] ?? 'NULL') . " | " .
            (isset($row['secure_answer']) ? strlen($row['secure_answer']) : 'NULL') . " | " .
            (isset($row['secure_answer2']) ? strlen($row['secure_answer2']) : 'NULL') . " | " .
            (isset($row['secure_answer3']) ? strlen($row['secure_answer3']) : 'NULL') . "\n";
    }
}
else {
    echo "Error: " . $conn->error . "\n";
}
?>
