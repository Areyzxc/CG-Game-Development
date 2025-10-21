<?php
/**
 * File: api/admin_mark_notification_read.php
 * Purpose: Mark a notification as read
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

$input = json_decode(file_get_contents('php://input'), true);
$notificationId = $input['id'] ?? null;

if (!$notificationId) {
    echo json_encode(['success' => false, 'error' => 'Notification ID required']);
    exit;
}

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("UPDATE system_notifications SET is_read = TRUE, read_at = NOW() WHERE id = ?");
    $stmt->execute([$notificationId]);
    
    echo json_encode(['success' => true]);
    
} catch (Exception $e) {
    error_log("Mark notification read error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Failed to mark notification as read']);
}
