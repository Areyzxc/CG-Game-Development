<?php
/**
 * File: api/admin_mark_all_notifications_read.php
 * Purpose: Mark all notifications as read
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

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("UPDATE system_notifications SET is_read = TRUE, read_at = NOW() WHERE is_read = FALSE");
    $stmt->execute();
    
    echo json_encode(['success' => true, 'message' => 'All notifications marked as read']);
    
} catch (Exception $e) {
    error_log("Mark all notifications read error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Failed to mark notifications as read']);
}
