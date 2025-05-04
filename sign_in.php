<?php
// sign_up.php

include 'db_connection.php';  // establishes $conn (MySQLi)
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
    exit;
}

// 1. Sanitize & validate inputs
$username = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING));
$email    = trim(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL));
$password = $_POST['password'] ?? '';

if (!$username) {
    echo json_encode(['success' => false, 'error' => 'Username is required.']);
    exit;
}
if (!$email) {
    echo json_encode(['success' => false, 'error' => 'A valid email is required.']);
    exit;
}
if (strlen($password) < 6) {
    echo json_encode(['success' => false, 'error' => 'Password must be at least 6 characters.']);
    exit;
}

// 2. Check for existing email
$chk = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
$chk->bind_param("s", $email);
$chk->execute();
$chk->store_result();
if ($chk->num_rows > 0) {
    echo json_encode(['success' => false, 'error' => 'Email already registered.']);
    $chk->close();
    exit;
}
$chk->close();

// 3. Hash the password
$hashed = password_hash($password, PASSWORD_DEFAULT);

// 4. Insert new user
$stmt = $conn->prepare("
    INSERT INTO users (username, email, password_hash, created_at)
    VALUES (?, ?, ?, NOW())
");
if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Prepare failed.']);
    exit;
}

$stmt->bind_param("sss", $username, $email, $hashed);
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Database error: could not create user.']);
}
$stmt->close();
$conn->close();
