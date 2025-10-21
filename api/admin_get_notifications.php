<?php
/**
 * File: api/admin_get_notifications.php
 * Purpose: Fetch system notifications for admin dashboard
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

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    
    // Get recent notifications
    $stmt = $conn->prepare("
        SELECT 
            id,
            type,
            title,
            message,
            icon,
            is_read,
            created_at
        FROM system_notifications
        ORDER BY created_at DESC
        LIMIT ?
    ");
    $stmt->execute([$limit]);
    
    $notifications = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Calculate time ago
        $date = new DateTime($row['created_at']);
        $now = new DateTime();
        $diff = $now->diff($date);
        
        if ($diff->days == 0) {
            if ($diff->h == 0) {
                if ($diff->i == 0) {
                    $timeAgo = 'Just now';
                } else {
                    $timeAgo = $diff->i . ' min ago';
                }
            } else {
                $timeAgo = $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
            }
        } else {
            $timeAgo = $diff->days . ' day' . ($diff->days > 1 ? 's' : '') . ' ago';
        }
        
        $notifications[] = [
            'id' => $row['id'],
            'type' => $row['type'],
            'title' => $row['title'],
            'message' => $row['message'],
            'icon' => $row['icon'],
            'is_read' => (bool)$row['is_read'],
            'time_ago' => $timeAgo,
            'created_at' => $row['created_at']
        ];
    }
    
    // Get unread count
    $stmt = $conn->query("SELECT COUNT(*) as count FROM system_notifications WHERE is_read = FALSE");
    $unreadCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    echo json_encode([
        'success' => true,
        'notifications' => $notifications,
        'unread_count' => $unreadCount
    ]);
    
} catch (Exception $e) {
    error_log("Notifications error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch notifications'
    ]);
}
