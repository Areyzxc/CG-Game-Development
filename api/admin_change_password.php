<?php
/**
 * File: api/admin_change_password.php
 * Purpose: Change admin password
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

$currentPassword = $_POST['current_password'] ?? '';
$newPassword = $_POST['new_password'] ?? '';

if (!$currentPassword || !$newPassword) {
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit;
}

// Validate new password length
if (strlen($newPassword) < 8) {
    echo json_encode(['success' => false, 'error' => 'New password must be at least 8 characters']);
    exit;
}

$db = Database::getInstance();
$currentUser = $auth->getCurrentUser();
$adminId = $currentUser['id'];

// Verify current password
$stmt = $db->prepare("SELECT password FROM admin_users WHERE admin_id = ?");
$stmt->execute([$adminId]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$admin) {
    echo json_encode(['success' => false, 'error' => 'Admin not found']);
    exit;
}

if (!password_verify($currentPassword, $admin['password'])) {
    echo json_encode(['success' => false, 'error' => 'Current password is incorrect']);
    exit;
}

// Hash new password
$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

// Update password
$stmt = $db->prepare("UPDATE admin_users SET password = ? WHERE admin_id = ?");
if ($stmt->execute([$hashedPassword, $adminId])) {
    // Log the action
    $logStmt = $db->prepare("INSERT INTO admin_actions_log (admin_id, action_type, target_type, target_id, details) VALUES (?, ?, ?, ?, ?)");
    $logStmt->execute([
        $adminId,
        'change_password',
        'admin',
        $adminId,
        json_encode(['action' => 'Changed own password'])
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Password changed successfully'
    ]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to update password']);
}
