<?php
/**
 * File: admin_get_user_details.php
 * Purpose: API endpoint for retrieving detailed information about a user or admin for CodeGaming admin panel.
 * Features:
 *   - Validates admin authentication and permissions.
 *   - Accepts user/admin ID and type via GET request.
 *   - Returns profile, status, and ban info for users and admins.
 *   - Placeholder for future data (last seen, status).
 * Usage:
 *   - Called via AJAX from admin panel to view user/admin details.
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

$userId = $_GET['id'] ?? null;
$userType = $_GET['type'] ?? null;

if (!$userId || !$userType) {
    echo json_encode(['success' => false, 'error' => 'User ID and type are required.']);
    exit;
}

try {
    $data = null;
    if ($userType === 'user') {
        $stmt = $conn->prepare("SELECT id, username, email, profile_picture, created_at, is_banned, last_seen, 'user' as role FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
    } elseif ($userType === 'admin') {
        $stmt = $conn->prepare("SELECT admin_id as id, username, email, profile_picture, role, created_at, is_banned, last_seen FROM admin_users WHERE admin_id = ?");
        $stmt->execute([$userId]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    if ($data) {
        // Calculate online/offline status based on last_seen
        $data['last_seen'] = $data['last_seen'] ?? null;
        if ($data['last_seen']) {
            $now = time();
            $lastSeenTs = strtotime($data['last_seen']);
            $interval = $now - $lastSeenTs;
            $data['status'] = ($interval < 300) ? 'Online' : 'Offline'; // 5 min threshold
        } else {
            $data['status'] = 'Offline';
        }
        echo json_encode(['success' => true, 'data' => $data]);
    } else {
        echo json_encode(['success' => false, 'error' => 'User not found.']);
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
