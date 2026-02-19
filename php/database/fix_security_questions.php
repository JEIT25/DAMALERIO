<?php
require 'db_connect.php';

echo "Fixing missing security questions for testing...\n";

// Default values
$q1 = "What is your favorite color?";
$q2 = "What is your pet's name?";
$q3 = "What is your hometown?";

// Default answer for ALL questions: "test"
$a1_plain = "test";
$a2_plain = "test";
$a3_plain = "test";

$a1_hash = password_hash($a1_plain, PASSWORD_DEFAULT);
$a2_hash = password_hash($a2_plain, PASSWORD_DEFAULT);
$a3_hash = password_hash($a3_plain, PASSWORD_DEFAULT);

// Update users where questions are missing
$sql = "UPDATE users SET
        secure_question = ?, secure_answer = ?,
        secure_question2 = ?, secure_answer2 = ?,
        secure_question3 = ?, secure_answer3 = ?
        WHERE secure_question IS NULL OR secure_question = ''";

$stmt = $conn->prepare($sql);
$stmt->bind_param('ssssss', $q1, $a1_hash, $q2, $a2_hash, $q3, $a3_hash);

if ($stmt->execute()) {
    echo "Successfully updated " . $stmt->affected_rows . " users.\n";
    echo "Default Security Questions set.\n";
    echo "Correct Answer for ALL questions is: 'test'\n";
}
else {
    echo "Error updating records: " . $conn->error . "\n";
}

$stmt->close();
$conn->close();
?>
