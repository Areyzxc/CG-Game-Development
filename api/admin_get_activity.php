<?php
/**
 * File: api/admin_get_activity.php
 * Purpose: API endpoint for fetching recent admin activity in CodeGaming.
 * Features:
 *   - Returns recent activity from login logs, quiz attempts, and code submissions.
 *   - Formats timestamps to show relative time (e.g., "5 min ago").
 *   - Requires admin authentication.
 * Usage:
 *   - Called by admin dashboard to display recent activity.
 *   - Requires Auth.php and Database.php for authentication and DB access.
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

// Check admin authentication
$auth = Auth::getInstance();
if (!$auth->isAdmin()) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit;
}

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    // Get recent activity from activity_log table
    $stmt = $conn->prepare("
        SELECT 
            username as user,
            action,
            action_details,
            MAX(created_at) as time,
            status,
            CASE 
                WHEN status = 'success' THEN 'success'
                WHEN status = 'failed' THEN 'danger'
                ELSE 'warning'
            END as status_color
        FROM activity_log
        WHERE created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)
        GROUP BY username, action, action_details, status
        ORDER BY time DESC
        LIMIT 15
    ");
    $stmt->execute();
    
    $activity = [];
    while ($row = $stmt->fetch()) {
        $date = new DateTime($row['time']);
        $now = new DateTime();
        $diff = $now->diff($date);
        
        if ($diff->days == 0) {
            if ($diff->h == 0) {
                if ($diff->i == 0) {
                    $timeAgo = $diff->s . ' second' . ($diff->s != 1 ? 's' : '') . ' ago';
                } else {
                    $timeAgo = $diff->i . ' minute' . ($diff->i != 1 ? 's' : '') . ' ago';
                }
            } else {
                $timeAgo = $diff->h . ' hour' . ($diff->h != 1 ? 's' : '') . ' ago';
            }
        } else {
            $timeAgo = $diff->days . ' day' . ($diff->days != 1 ? 's' : '') . ' ago';
        }
        
        // Format action text
        $actionText = $row['action'];
        switch ($row['action']) {
            case 'logged_in':
                $actionText = 'Logged in' . ($row['action_details'] ? ' from ' . substr($row['action_details'], strrpos($row['action_details'], ' ') + 1) : '');
                break;
            case 'logged_out':
                $actionText = 'Logged out';
                break;
            case 'login_failed':
                $actionText = 'Failed login attempt';
                break;
            case 'registered':
                $actionText = 'Registered new account';
                break;
            case 'profile_updated':
                $actionText = 'Updated profile';
                break;
            case 'password_changed':
                $actionText = 'Changed password';
                break;
            default:
                $actionText = ucfirst(str_replace('_', ' ', $row['action']));
        }
        
        $activity[] = [
            'user' => $row['user'] ?? 'Unknown User',
            'action' => $actionText,
            'time' => $timeAgo,
            'status' => ucfirst($row['status']),
            'status_color' => $row['status_color']
        ];
    }
    
    // If no real activity, provide some default data
    if (empty($activity)) {
        $activity = [
            [
                'user' => 'System',
                'action' => 'No recent activity',
                'time' => 'Just now',
                'status' => 'Info',
                'status_color' => 'info'
            ]
        ];
    }
    
    echo json_encode([
        'success' => true,
        'activity' => $activity
    ]);
    
} catch (Exception $e) {
    error_log("Admin activity error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch activity'
    ]);
} 
