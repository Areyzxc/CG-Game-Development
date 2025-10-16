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
    
    // Get recent activity from multiple sources
    $stmt = $conn->query("
        (SELECT 
            u.username as user,
            CONCAT('Logged in from ', ll.ip_address) as action,
            ll.login_time as time,
            'Success' as status,
            'success' as status_color
        FROM login_logs ll
        LEFT JOIN users u ON ll.user_id = u.id
        WHERE ll.login_time > DATE_SUB(NOW(), INTERVAL 24 HOUR)
        ORDER BY ll.login_time DESC
        LIMIT 5)
        
        UNION ALL
        
        (SELECT 
            u.username as user,
            CONCAT('Completed quiz: ', qq.question) as action,
            uqa.attempted_at as time,
            CASE WHEN uqa.is_correct THEN 'Success' ELSE 'Failed' END as status,
            CASE WHEN uqa.is_correct THEN 'success' ELSE 'danger' END as status_color
        FROM user_quiz_attempts uqa
        LEFT JOIN users u ON uqa.user_id = u.id
        LEFT JOIN quiz_questions qq ON uqa.question_id = qq.id
        WHERE uqa.attempted_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)
        ORDER BY uqa.attempted_at DESC
        LIMIT 5)
        
        UNION ALL
        
        (SELECT 
            u.username as user,
            CONCAT('Submitted code for challenge: ', cc.title) as action,
            ucs.submitted_at as time,
            ucs.status as status,
            CASE 
                WHEN ucs.status = 'passed' THEN 'success'
                WHEN ucs.status = 'failed' THEN 'danger'
                ELSE 'warning'
            END as status_color
        FROM user_code_submissions ucs
        LEFT JOIN users u ON ucs.user_id = u.id
        LEFT JOIN code_challenges cc ON ucs.challenge_id = cc.id
        WHERE ucs.submitted_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)
        ORDER BY ucs.submitted_at DESC
        LIMIT 5)
        
        ORDER BY time DESC
        LIMIT 10
    ");
    
    $activity = [];
    while ($row = $stmt->fetch()) {
        $date = new DateTime($row['time']);
        $now = new DateTime();
        $diff = $now->diff($date);
        
        if ($diff->days == 0) {
            if ($diff->h == 0) {
                $timeAgo = $diff->i . ' min ago';
            } else {
                $timeAgo = $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
            }
        } else {
            $timeAgo = $diff->days . ' day' . ($diff->days > 1 ? 's' : '') . ' ago';
        }
        
        $activity[] = [
            'user' => $row['user'] ?? 'Unknown User',
            'action' => $row['action'],
            'time' => $timeAgo,
            'status' => $row['status'],
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
