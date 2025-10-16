<?php
/**
 * File: api/admin_edit_announcement.php
 * Purpose: Allows admins to edit announcements in CodeGaming.
 * Features:
 *   - Accepts POST requests with announcement data.
 *   - Validates input and checks for existing announcements.
 *   - Updates announcement details in the database.
 *   - Returns JSON response for success or error.
 *   - Supports pinning/unpinning announcements.
 * 
 * Usage:
 *   - Called via AJAX from admin interface for editing announcements.
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
$id = intval($data['id'] ?? 0);
$title = trim($data['title'] ?? '');
$content = trim($data['content'] ?? '');
$category = $data['category'] ?? null;
$status = $data['status'] ?? null;
$is_pinned = isset($data['is_pinned']) ? intval($data['is_pinned']) : null;
if ($id <= 0) {
    echo json_encode(['success' => false, 'error' => 'ID required.']);
    exit;
}
$db = Database::getInstance();
$conn = $db->getConnection();
if ($is_pinned !== null) {
    if ($is_pinned === 1) {
        // Check how many are already pinned
        $count = $conn->query('SELECT COUNT(*) FROM announcements WHERE is_pinned=1')->fetchColumn();
        if ($count >= 3) {
            echo json_encode(['success' => false, 'error' => 'Pin limit reached.']);
            exit;
        }
        $stmt = $conn->prepare('UPDATE announcements SET is_pinned=1 WHERE id=?');
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
        exit;
    } else {
        // Unpin
        $stmt = $conn->prepare('UPDATE announcements SET is_pinned=0 WHERE id=?');
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
        exit;
    }
}
$fields = [];
$params = [];
if ($title !== '') { $fields[] = 'title=?'; $params[] = $title; }
if ($content !== '') { $fields[] = 'content=?'; $params[] = $content; }
if ($category !== null) { $fields[] = 'category=?'; $params[] = $category; }
if ($status !== null) { $fields[] = 'status=?'; $params[] = $status; }
if (!$fields) {
    echo json_encode(['success' => false, 'error' => 'No fields to update.']);
    exit;
}
$params[] = $id;
$stmt = $conn->prepare('UPDATE announcements SET ' . implode(', ', $fields) . ' WHERE id=?');
if ($stmt->execute($params)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Database error.']);
} 
