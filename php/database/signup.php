<?php
session_start();
require '../database/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get input values
    $id = $_POST['id'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $middleInitial = $_POST['middleInitial'];
    $extension = $_POST['extension'];
    $purok = $_POST['purok'];
    $barangay = $_POST['barangay'];
    $city = $_POST['city'];
    $province = $_POST['province'];
    $zipCode = $_POST['zipCode'];
    $country = $_POST['country'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $birthdate = $_POST['birthdate'];
    $age = date_diff(date_create($birthdate), date_create('today'))->y;
    $secure_question = $_POST['secure_question'];
    $secure_answerRaw = $_POST['secure_answer']; // Raw answer

    // --- THIS IS THE FIX ---
    // Hash password AND the security answer
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $secure_answerHashed = password_hash($secure_answerRaw, PASSWORD_DEFAULT); // Hashing is re-enabled

    // --- CHECK FOR DUPLICATES (Server-side safety check) ---
    // 1. Check ID
    $stmt = $conn->prepare("SELECT 1 FROM users WHERE id = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        echo "This ID is already registered.";
        $stmt->close();
        $conn->close();
        exit;
    }
    $stmt->close();

    // 2. Check Username
    $stmt = $conn->prepare("SELECT 1 FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        echo "This username is already taken.";
        $stmt->close();
        $conn->close();
        exit;
    }
    $stmt->close();

    // 3. Check Email
    $stmt = $conn->prepare("SELECT 1 FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        echo "This email is already registered.";
        $stmt->close();
        $conn->close();
        exit;
    }
    $stmt->close();
    
    // --- END DUPLICATE CHECK ---

    // New users are always consumers; admin/superadmin are set manually in DB
    $role = 'consumer';

    // Prepare SQL statement (includes role)
    $sql = "INSERT INTO users (
                id, firstName, lastName, middleInitial, extension,
                purok, barangay, city, province, zipCode, country, 
                username, email, password, birthdate, age, 
                secure_question, secure_answer, role
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param(
            'sssssssssssssssssss', // 19 params
            $id,
            $firstName,
            $lastName,
            $middleInitial,
            $extension,
            $purok,
            $barangay,
            $city,
            $province,
            $zipCode,
            $country,
            $username,
            $email,
            $hashedPassword,
            $birthdate,
            $age,
            $secure_question,
            $secure_answerHashed,
            $role
        );

        if ($stmt->execute()) {
            echo "User successfully registered!";
        } else {
            if ($conn->errno == 1062) {
                echo "An error occurred: Duplicate entry for a unique field.";
            } else {
                echo "Signup failed. Please try again. Error: " . $stmt->error;
            }
        }

        $stmt->close();
    } else {
        die("Database Error: " . $conn->error);
    }

    $conn->close();
}
?>