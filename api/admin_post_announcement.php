<?php
/**
 * File: api/admin_post_announcement.php
 * Purpose: API endpoint for posting new announcements by admins in CodeGaming.
 * Features:
 *   - Validates admin authentication and permissions.
 *   - Accepts title, content, category, status, and pin status via POST request.
 *   - Inserts new announcement into the announcements table.
 *   - Returns the created announcement data in JSON format.
 * Usage:
 *   - Called via AJAX from admin panel to post announcements.
 *   - Requires Auth.php and Database.php for authentication and DB access.
 * Included Files/Dependencies:
 *   - includes/Auth.php
 *   - includes/Database.php
 * Author: CodeGaming Team
 * Last Updated: July 24, 2025
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
if (!isset($data['title']) || !isset($data['content']) || !$data['title'] || !$data['content']) {
    echo json_encode(['success' => false, 'error' => 'Title and content required.']);
    exit;
}
$title = $data['title'];
$content = $data['content'];
$category = $data['category'] ?? 'general';
$status = $data['status'] ?? 'published';
$is_pinned = !empty($data['is_pinned']) ? 1 : 0;
$db = Database::getInstance();
$conn = $db->getConnection();
$stmt = $conn->prepare('INSERT INTO announcements (title, content, category, status, is_pinned, created_by) VALUES (?, ?, ?, ?, ?, ?)');
$adminId = $_SESSION['user_id'] ?? null;
if ($stmt->execute([$title, $content, $category, $status, $is_pinned, $adminId])) {
    $id = $conn->lastInsertId();
    echo json_encode(['success' => true, 'announcement' => [
        'id' => $id,
        'title' => $title,
        'content' => $content,
        'category' => $category,
        'status' => $status,
        'is_pinned' => $is_pinned,
        'created_by' => $adminId
    ]]);
} else {
    echo json_encode(['success' => false, 'error' => 'Database error.']);
} 