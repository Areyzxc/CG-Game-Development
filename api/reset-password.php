<?php
/**
 * File: api/reset-password.php
 * Purpose: Handles password reset requests for CodeGaming users and admins, updating credentials securely.
 * Features:
 *   - Accepts POST requests with username and new password.
 *   - Validates password length and required parameters.
 *   - Updates password for users and admins, logs password reset events.
 *   - Returns JSON response for success or error.
 * Usage:
 *   - Called via AJAX from the password reset form.
 *   - Requires Database.php for DB access.
 * Included Files/Dependencies:
 *   - includes/Database.php
 * Author: CodeGaming Team
 * Last Updated: July 24, 2025
 */
require_once '../includes/Database.php';
header('Content-Type: application/json');

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['username']) || !isset($input['new_password'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
    exit;
}

$username = trim($input['username']);
$newPassword = $input['new_password'];

// Validate password
if (strlen($newPassword) < 6) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Password must be at least 6 characters long']);
    exit;
}

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $ipAddress = $_SERVER['REMOTE_ADDR'];
    $now = date('Y-m-d H:i:s');

    // Try to update in users table
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if ($user) {
        $stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        $stmt->execute([$hashedPassword, $user['id']]);
        // Log the password change
        $stmt = $conn->prepare("INSERT INTO login_logs (user_id, role, ip_address, login_time) VALUES (?, 'password_reset', ?, ?)");
        $stmt->execute([$user['id'], $ipAddress, $now]);
        echo json_encode(['success' => true, 'message' => 'Password updated for user.']);
        exit;
    }

    // Try to update in admin_users table
    $stmt = $conn->prepare("SELECT admin_id FROM admin_users WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();
    if ($admin) {
        $stmt = $conn->prepare("UPDATE admin_users SET password_hash = ? WHERE admin_id = ?");
        $stmt->execute([$hashedPassword, $admin['admin_id']]);
        // Log the password change (optional: you can add a separate admin log table if desired)
        echo json_encode(['success' => true, 'message' => 'Password updated for admin.']);
        exit;
    }

    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Username not found.']);
    exit;

} catch (Exception $e) {
    http_response_code(500);
    error_log('Password reset error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Server error. Please try again later.']);
}
