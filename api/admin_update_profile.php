<?php
/**
 * File: api/admin_update_profile.php
 * Purpose: Update admin profile information
 * Author: CodeGaming Team
 * Last Updated: October 21, 2025
 */

session_start();
require_once '../includes/Auth.php';
require_once '../includes/Database.php';

header('Content-Type: application/json');

$auth = Auth::getInstance();
if (!$auth->isAdmin()) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

$adminId = $_POST['admin_id'] ?? null;
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');

if (!$adminId || !$username || !$email) {
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit;
}

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'error' => 'Invalid email address']);
    exit;
}

$db = Database::getInstance();

// Check if username is already taken by another admin
$stmt = $db->prepare("SELECT admin_id FROM admin_users WHERE username = ? AND admin_id != ?");
$stmt->execute([$username, $adminId]);
if ($stmt->fetch()) {
    echo json_encode(['success' => false, 'error' => 'Username already taken']);
    exit;
}

// Check if email is already taken by another admin
$stmt = $db->prepare("SELECT admin_id FROM admin_users WHERE email = ? AND admin_id != ?");
$stmt->execute([$email, $adminId]);
if ($stmt->fetch()) {
    echo json_encode(['success' => false, 'error' => 'Email already in use']);
    exit;
}

// Update profile
$stmt = $db->prepare("UPDATE admin_users SET username = ?, email = ? WHERE admin_id = ?");
if ($stmt->execute([$username, $email, $adminId])) {
    // Update session
    $_SESSION['username'] = $username;
    
    // Get updated data
    $stmt = $db->prepare("SELECT admin_id, username, email, role, profile_picture FROM admin_users WHERE admin_id = ?");
    $stmt->execute([$adminId]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'message' => 'Profile updated successfully',
        'data' => $admin
    ]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to update profile']);
}
