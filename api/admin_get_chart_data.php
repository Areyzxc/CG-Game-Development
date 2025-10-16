<?php
/**
 * File: api/admin_get_chart_data.php
 * Purpose: Fetches chart data for admin dashboard including user activity, content distribution, and visitor data.
 * Features:
 *   - Returns user activity over the last 7 days.
 *   - Provides content distribution statistics.
 *   - Retrieves recent visitor data.
 * 
 * Usage:
 *   - Called via AJAX from the admin panel to display charts.
 *   - Requires Auth.php and Database.php for authentication and DB access.
 * 
 * Included Files/Dependencies:
 *   - includes/Auth.php
 *   - includes/Database.php
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
    
    // Get user activity data for the last 7 days
    $stmt = $conn->query("
        SELECT 
            DATE(ll.login_time) as date,
            COUNT(*) as count
        FROM login_logs ll
        WHERE ll.login_time >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        GROUP BY DATE(ll.login_time)
        ORDER BY date
    ");
    
    $userActivity = [];
    $dates = [];
    $counts = [];
    
    while ($row = $stmt->fetch()) {
        $dates[] = date('D', strtotime($row['date'])); // Mon, Tue, etc.
        $counts[] = (int)$row['count'];
    }
    
    // Fill in missing days with 0
    $last7Days = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $dayName = date('D', strtotime($date));
        $last7Days[] = $dayName;
    }
    
    $userActivity = [
        'labels' => $last7Days,
        'data' => $counts
    ];
    
    // Get content distribution data
    $stmt = $conn->query("
        SELECT 
            'Programming Languages' as category,
            COUNT(*) as count
        FROM programming_languages
        UNION ALL
        SELECT 
            'Quiz Questions' as category,
            COUNT(*) as count
        FROM quiz_questions
        UNION ALL
        SELECT 
            'Code Challenges' as category,
            COUNT(*) as count
        FROM code_challenges
    ");
    
    $contentDistribution = [];
    while ($row = $stmt->fetch()) {
        $contentDistribution[] = [
            'label' => $row['category'],
            'value' => (int)$row['count']
        ];
    }
    
    // Get recent visitor data
    $stmt = $conn->query("
        SELECT 
            DATE(visit_time) as date,
            COUNT(*) as visitors
        FROM visitor_logs
        WHERE visit_time >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        GROUP BY DATE(visit_time)
        ORDER BY date
    ");
    
    $visitorData = [];
    while ($row = $stmt->fetch()) {
        $visitorData[] = [
            'date' => $row['date'],
            'visitors' => (int)$row['visitors']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'user_activity' => $userActivity,
        'content_distribution' => $contentDistribution,
        'visitor_data' => $visitorData
    ]);
    
} catch (Exception $e) {
    error_log("Admin chart data error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch chart data'
    ]);
} 
