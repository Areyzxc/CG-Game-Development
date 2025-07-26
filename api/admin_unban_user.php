<?php
/**
 * File: api/admin_unban_user.php
 * Purpose: API endpoint for unbanning users or admins in CodeGaming by an authorized admin.
 * Features:
 *   - Validates admin authentication and permissions.
 *   - Accepts user/admin ID and type via POST request.
 *   - Updates ban status in users or admin_users table.
 *   - Logs the unban action in admin_actions_log for auditing.
 * Usage:
 *   - Called via AJAX from admin panel to unban users or admins.
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
$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'] ?? null;
$type = $data['type'] ?? null;
if (!$id || !$type || !in_array($type, ['user', 'admin'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid parameters']);
    exit;
}
$db = Database::getInstance();
$conn = $db->getConnection();
try {
    if ($type === 'user') {
        $stmt = $conn->prepare('UPDATE users SET is_banned=0 WHERE id=?');
        $stmt->execute([$id]);
    } else {
        $stmt = $conn->prepare('UPDATE admin_users SET is_banned=0 WHERE admin_id=?');
        $stmt->execute([$id]);
    }
    // Log action
    $adminId = $auth->getCurrentUser()['admin_id'] ?? null;
    $stmt = $conn->prepare('INSERT INTO admin_actions_log (admin_id, action_type, target_type, target_id, details) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$adminId, 'unban', $type, $id, null]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
} 