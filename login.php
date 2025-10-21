<?php
/**
 * ==========================================================
 * File: login.php
 * 
 * Description:
 *   - Handles user and admin login for Code Gaming platform
 *   - Features:
 *       • Accepts both JSON and form POST requests
 *       • Validates username/email and password
 *       • Supports login for players and admins (separate tables)
 *       • Checks for banned accounts and provides error feedback
 *       • Sets session variables for authenticated users
 *       • Returns JSON responses for all outcomes
 * 
 * Usage:
 *   - Called via AJAX or form submission for login
 *   - Used by both frontend and API clients
 * 
 * Author: [Santiago]
 * Last Updated: [July 22, 2025]
 * -- Code Gaming Team --
 * ==========================================================
 */

// login.php

include 'db_connection.php';  // $conn = new mysqli(...);
require_once 'includes/Database.php';
require_once 'includes/ActivityLogger.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');

// 1. Only POST allowed
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
    exit;
}

// 2. Get input data (handle both JSON and form data)
$input = [];
if ($_SERVER['CONTENT_TYPE'] === 'application/json') {
    $input = json_decode(file_get_contents('php://input'), true);
} else {
    $input = $_POST;
}

$username = trim($input['username'] ?? '');
$password = $input['password'] ?? '';

if (empty($username)) {
    echo json_encode(['success' => false, 'error' => 'Username or email is required.']);
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

// 3. Attempt Player login (check both username and email)
$stmt = $conn->prepare("SELECT id, password_hash, username, role, is_banned FROM users WHERE email = ? OR username = ?");
$stmt->bind_param("ss", $username, $username);
$stmt->execute();
$res = $stmt->get_result();
if ($row = $res->fetch_assoc()) {
    // Verify password
    if (password_verify($password, $row['password_hash'])) {
        if (!empty($row['is_banned']) && $row['is_banned']) {
            respond(false, ['error' => 'Your account has been banned. Please contact support.']);
        }
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['role']    = $row['role'];
        $_SESSION['username'] = $row['username'];
        // Update last_seen for user
        $updateStmt = $conn->prepare("UPDATE users SET last_seen = NOW() WHERE id = ?");
        $updateStmt->bind_param("i", $row['id']);
        $updateStmt->execute();
        $updateStmt->close();
        
        // Log successful login
        $logger = ActivityLogger::getInstance();
        $logger->logActivity([
            'user_id' => $row['id'],
            'username' => $row['username'],
            'user_type' => 'user',
            'action' => 'logged_in',
            'action_details' => 'Logged in from ' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'),
            'status' => 'success'
        ]);
        
        respond(true, ['role' => $row['role']]);
    } else {
        // Log failed login attempt
        $logger = ActivityLogger::getInstance();
        $logger->logActivity([
            'user_id' => null,
            'username' => $username,
            'user_type' => 'user',
            'action' => 'login_failed',
            'action_details' => 'Failed login attempt - incorrect password',
            'status' => 'failed'
        ]);
        respond(false, ['error' => 'Incorrect password.']);
    }
}
$stmt->close();

// 4. Attempt Admin login (check both username and email)
$stmt = $conn->prepare("SELECT admin_id AS id, password_hash, role, username, is_banned FROM admin_users WHERE email = ? OR username = ?");
$stmt->bind_param("ss", $username, $username);
$stmt->execute();
$res = $stmt->get_result();
if ($row = $res->fetch_assoc()) {
    // Verify password
    if (password_verify($password, $row['password_hash'])) {
        if (!empty($row['is_banned']) && $row['is_banned']) {
            respond(false, ['error' => 'This admin account has been banned.']);
        }
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['role']    = $row['role'] ?: 'admin';
        $_SESSION['username'] = $row['username'];
        // Update last_seen for admin
        $updateStmt = $conn->prepare("UPDATE admin_users SET last_seen = NOW() WHERE admin_id = ?");
        $updateStmt->bind_param("i", $row['id']);
        $updateStmt->execute();
        $updateStmt->close();
        
        // Log successful admin login
        $logger = ActivityLogger::getInstance();
        $logger->logActivity([
            'admin_id' => $row['id'],
            'username' => $row['username'],
            'user_type' => 'admin',
            'action' => 'logged_in',
            'action_details' => 'Admin logged in from ' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'),
            'status' => 'success'
        ]);
        
        respond(true, ['role' => $_SESSION['role']]);
    } else {
        // Log failed admin login attempt
        $logger = ActivityLogger::getInstance();
        $logger->logActivity([
            'admin_id' => null,
            'username' => $username,
            'user_type' => 'admin',
            'action' => 'login_failed',
            'action_details' => 'Failed admin login - incorrect password',
            'status' => 'failed'
        ]);
        respond(false, ['error' => 'Incorrect password.']);
    }
}
$stmt->close();

// 5. No account found - log failed attempt
$logger = ActivityLogger::getInstance();
$logger->logActivity([
    'user_id' => null,
    'username' => $username,
    'user_type' => 'user',
    'action' => 'login_failed',
    'action_details' => 'Failed login - account not found',
    'status' => 'failed'
]);
respond(false, ['error' => 'No account found with this username or email.']);
