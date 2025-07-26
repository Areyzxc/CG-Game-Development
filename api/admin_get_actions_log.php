<?php
/**
 * File: api/admin_get_actions_log.php
 * Purpose: API endpoint for retrieving the admin actions log in CodeGaming.
 * Features:
 *   - Returns the last 20 admin actions with timestamps and admin usernames.
 *   - Requires authentication and admin privileges.
 *   - Returns JSON response with action details.
 *   - Handles database connection and query execution.
 * 
 * Usage:
 *   - Called by admin dashboard to display recent actions.
 *   - Requires Auth.php and Database.php for authentication and DB access.
 * 
 * Included Files/Dependencies:
 *   - includes/Auth.php
 *   - includes/Database.php
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
$db = Database::getInstance();
$conn = $db->getConnection();
try {
    $stmt = $conn->prepare('SELECT l.*, a.username as admin_username FROM admin_actions_log l LEFT JOIN admin_users a ON l.admin_id=a.admin_id ORDER BY l.created_at DESC LIMIT 20');
    $stmt->execute();
    $actions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'actions' => $actions]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
} 