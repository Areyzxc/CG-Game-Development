<?php
/**
 * File: api/admin_ban_user.php
 * Purpose: API endpoint for banning users or admins in CodeGaming.
 * Features:
 *   - Accepts POST requests with user/admin ID and type (user/admin).
 *   - Requires admin authentication.
 *   - Logs the ban action in admin_actions_log.
 *   - Returns JSON response indicating success or error.
 * 
 * Request (POST):
 *   - id: int (user or admin ID to ban)
 *   - type: string (either 'user' or 'admin')
 * Response:
 *   - success: bool
 *   - error: string (if any)
 *   - message: string (success message)
 * Usage:
 *   - Called by admin interface to ban users or admins.
 *   - Requires Database.php and Auth.php for DB access and authentication.
 * 
 * Included Files/Dependencies:
 *   - includes/Database.php
 *   - includes/Auth.php
 * 
 * Author: CodeGaming Team
 * Last Updated: July 26, 2025
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
        $stmt = $conn->prepare('UPDATE users SET is_banned=1 WHERE id=?');
        $stmt->execute([$id]);
    } else {
        $stmt = $conn->prepare('UPDATE admin_users SET is_banned=1 WHERE admin_id=?');
        $stmt->execute([$id]);
    }
    // Log action
    $adminId = $auth->getCurrentUser()['admin_id'] ?? null;
    $stmt = $conn->prepare('INSERT INTO admin_actions_log (admin_id, action_type, target_type, target_id, details) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$adminId, 'ban', $type, $id, null]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
} 