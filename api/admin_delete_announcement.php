<?php
/**
 * File: api/admin_delete_announcement.php
 * Purpose: API endpoint for deleting announcements in CodeGaming.
 * Features:
 *   - Accepts DELETE requests with announcement ID.
 *   - Checks if the user is an admin.
 *   - Deletes the specified announcement from the database.
 *   - Returns JSON response indicating success or error.
 * 
 * Usage:
 *   - Called via AJAX from admin interface for managing announcements.
 *   - Requires Database.php and Auth.php for DB access.
 * 
 * Included Files/Dependencies:
 *   - includes/Database.php
 *   - includes/Auth.php
 * 
 * Author: CodeGaming Team
 * Last Updated: July 26, 2025
 */
require_once '../includes/Database.php';
require_once '../includes/Auth.php';
header('Content-Type: application/json');

$auth = Auth::getInstance();
if (!$auth->isAdmin()) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['id']) || !$data['id']) {
    echo json_encode(['success' => false, 'error' => 'ID required.']);
    exit;
}

$db = Database::getInstance();
$conn = $db->getConnection();
$stmt = $conn->prepare('DELETE FROM announcements WHERE id = ?');
if ($stmt->execute([$data['id']])) {
    echo json_encode(['success' => true, 'message' => 'Announcement deleted.']);
} else {
    echo json_encode(['success' => false, 'error' => 'Database error.']);
} 