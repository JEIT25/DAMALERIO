<?php
require_once 'db_connect.php';

echo "Applying security enhancements to database...\n";

// Read and execute the SQL file
$sql_file = __DIR__ . '/../SQL_FILE/schema_security_enhancements_fixed.sql';
$sql = file_get_contents($sql_file);

if ($sql === false) {
    die("Error reading SQL file");
}

// Split SQL into individual statements
$statements = array_filter(array_map('trim', explode(';', $sql)));

foreach ($statements as $statement) {
    if (!empty($statement) && !preg_match('/^--/', $statement)) {
        try {
            if ($conn->query($statement)) {
                echo "✓ Executed: " . substr($statement, 0, 50) . "...\n";
            } else {
                echo "✗ Error: " . $conn->error . "\n";
                echo "Statement: " . $statement . "\n";
            }
        } catch (Exception $e) {
            echo "✗ Exception: " . $e->getMessage() . "\n";
        }
    }
}

echo "\nDatabase security enhancements completed!\n";
$conn->close();
?>
