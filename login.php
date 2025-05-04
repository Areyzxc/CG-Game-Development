<?php
// login.php

include 'db_connection.php';  // $conn = new mysqli(...);
session_start();
header('Content-Type: application/json');

// 1. Only POST allowed
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
    exit;
}

// 2. Sanitize & validate inputs
$email    = trim(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL));
$password = $_POST['password'] ?? '';

if (!$email) {
    echo json_encode(['success' => false, 'error' => 'A valid email is required.']);
    exit;
}
if (empty($password)) {
    echo json_encode(['success' => false, 'error' => 'Password is required.']);
    exit;
}

// Helper to close and respond
function respond($success, $data = []) {
    echo json_encode(array_merge(['success' => $success], $data));
    exit;
}

// 3. Attempt Player login
if ($stmt = $conn->prepare("SELECT user_id AS id, password_hash FROM users WHERE email = ?")) {
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        // Verify password
        if (password_verify($password, $row['password_hash'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['role']    = 'player';
            respond(true, ['role' => 'player']);
        } else {
            respond(false, ['error' => 'Incorrect password.']);
        }
    }
    $stmt->close();
}

// 4. Attempt Admin login
if ($stmt = $conn->prepare("SELECT admin_id AS id, password_hash, role FROM admin_users WHERE email = ?")) {
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        // Verify password
        if (password_verify($password, $row['password_hash'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['role']    = $row['role'] ?: 'admin';
            respond(true, ['role' => $_SESSION['role']]);
        } else {
            respond(false, ['error' => 'Incorrect password.']);
        }
    }
    $stmt->close();
}

// 5. No account found
respond(false, ['error' => 'No account found with this email.']);

