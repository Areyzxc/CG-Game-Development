<?php
/**
 * File: api/admin_get_users.php
 * Purpose: API endpoint for retrieving user and admin account lists for CodeGaming admin panel.
 * Features:
 *   - Validates admin authentication and permissions.
 *   - Supports search queries for users and admins by username or email.
 *   - Returns lists of regular users and admin users, including ban status and creation date.
 * Usage:
 *   - Called via AJAX from admin panel to display/manage user and admin accounts.
 *   - Requires Auth.php and Database.php for authentication and DB access.
 * Included Files/Dependencies:
 *   - includes/Auth.php
 *   - includes/Database.php
 * Author: CodeGaming Team
 * Last Updated: July 24, 2025
 */
require_once '../includes/Auth.php';
require_once '../includes/Database.php';

header('Content-Type: application/json');

$auth = Auth::getInstance();
if (!$auth->isAdmin()) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$db = Database::getInstance();
$conn = $db->getConnection();

$q = isset($_GET['q']) ? trim($_GET['q']) : '';

if ($q !== '') {
    // Search users
    $stmt_users = $conn->prepare("SELECT id, username, email, created_at, is_banned, last_seen FROM users WHERE role = 'user' AND (username LIKE ? OR email LIKE ?) ORDER BY created_at DESC");
    $likeQ = "%$q%";
    $stmt_users->execute([$likeQ, $likeQ]);
    $users = $stmt_users->fetchAll(PDO::FETCH_ASSOC);
    // Add status field
    foreach ($users as &$user) {
        $user['last_seen'] = $user['last_seen'] ?? null;
        if ($user['last_seen']) {
            $now = time();
            $lastSeenTs = strtotime($user['last_seen']);
            $interval = $now - $lastSeenTs;
            $user['status'] = ($interval < 300) ? 'Online' : 'Offline';
        } else {
            $user['status'] = 'Offline';
        }
    }
    unset($user);

    // Search admins
    $stmt_admins = $conn->prepare("SELECT admin_id as id, username, email, role, created_at, is_banned, last_seen FROM admin_users WHERE username LIKE ? OR email LIKE ? ORDER BY created_at DESC");
    $stmt_admins->execute([$likeQ, $likeQ]);
    $admins = $stmt_admins->fetchAll(PDO::FETCH_ASSOC);
    foreach ($admins as &$admin) {
        $admin['last_seen'] = $admin['last_seen'] ?? null;
        if ($admin['last_seen']) {
            $now = time();
            $lastSeenTs = strtotime($admin['last_seen']);
            $interval = $now - $lastSeenTs;
            $admin['status'] = ($interval < 300) ? 'Online' : 'Offline';
        } else {
            $admin['status'] = 'Offline';
        }
    }
    unset($admin);
} else {
    // Fetch regular users
    $stmt_users = $conn->prepare("SELECT id, username, email, created_at, is_banned, last_seen FROM users WHERE role = 'user' ORDER BY created_at DESC");
    $stmt_users->execute();
    $users = $stmt_users->fetchAll(PDO::FETCH_ASSOC);
    foreach ($users as &$user) {
        $user['last_seen'] = $user['last_seen'] ?? null;
        if ($user['last_seen']) {
            $now = time();
            $lastSeenTs = strtotime($user['last_seen']);
            $interval = $now - $lastSeenTs;
            $user['status'] = ($interval < 300) ? 'Online' : 'Offline';
        } else {
            $user['status'] = 'Offline';
        }
    }
    unset($user);

    // Fetch admin users
    $stmt_admins = $conn->prepare("SELECT admin_id as id, username, email, role, created_at, is_banned, last_seen FROM admin_users ORDER BY created_at DESC");
    $stmt_admins->execute();
    $admins = $stmt_admins->fetchAll(PDO::FETCH_ASSOC);
    foreach ($admins as &$admin) {
        $admin['last_seen'] = $admin['last_seen'] ?? null;
        if ($admin['last_seen']) {
            $now = time();
            $lastSeenTs = strtotime($admin['last_seen']);
            $interval = $now - $lastSeenTs;
            $admin['status'] = ($interval < 300) ? 'Online' : 'Offline';
        } else {
            $admin['status'] = 'Offline';
        }
    }
    unset($admin);
}

echo json_encode([
    'success' => true,
    'users' => $users,
    'admins' => $admins
]);

try {
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
