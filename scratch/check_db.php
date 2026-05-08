<?php
require 'php/database/db_connect.php';
$res = $conn->query("SELECT TABLE_NAME, COLUMN_NAME, CONSTRAINT_NAME 
                    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                    WHERE REFERENCED_TABLE_NAME = 'users' 
                    AND REFERENCED_TABLE_SCHEMA = '$dbname'");
while($row = $res->fetch_assoc()) {
    print_r($row);
}
