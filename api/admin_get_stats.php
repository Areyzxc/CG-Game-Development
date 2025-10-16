<?php
/**
 * File: api/admin_get_stats.php
 * Purpose: Provides admin statistics for CodeGaming, including user counts, content statistics, and system status.
 * Features:
 *   - Returns total users, active users, total content, and system status.
 *   - Fetches total announcements and their statuses.
 * Usage:
 *   - Called via AJAX from the admin panel to display statistics.
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
    
    // Get total users
    $stmt = $conn->query("SELECT COUNT(*) as total FROM users");
    $totalUsers = $stmt->fetch()['total'];
    
    // Get active users (users with activity in last 5 minutes)
    $stmt = $conn->query("SELECT COUNT(*) as total FROM users WHERE last_activity > DATE_SUB(NOW(), INTERVAL 5 MINUTE)");
    $activeUsers = $stmt->fetch()['total'];
    
    // Get total content (tutorials + quizzes + challenges + mini-games)
    $stmt = $conn->query("
        SELECT 
            (SELECT COUNT(*) FROM programming_languages) +
            (SELECT COUNT(*) FROM quiz_questions) +
            (SELECT COUNT(*) FROM code_challenges) +
            (SELECT COUNT(*) FROM mini_game_results) as total
    ");
    $totalContent = $stmt->fetch()['total'];
    
    // Get total announcements, published, and drafts (only is_active=1)
    $stmt = $conn->query("SELECT COUNT(*) as total FROM announcements WHERE is_active=1");
    $totalAnnouncements = $stmt->fetch()['total'];
    $stmt = $conn->query("SELECT COUNT(*) as published FROM announcements WHERE is_active=1 AND status='published'");
    $publishedAnnouncements = $stmt->fetch()['published'];
    $stmt = $conn->query("SELECT COUNT(*) as drafts FROM announcements WHERE is_active=1 AND status='draft'");
    $draftAnnouncements = $stmt->fetch()['drafts'];
    
    // Simple system status check
    $systemStatus = 'Online';
    
    echo json_encode([
        'success' => true,
        'total_users' => $totalUsers,
        'active_users' => $activeUsers,
        'total_content' => $totalContent,
        'system_status' => $systemStatus,
        'total_announcements' => $totalAnnouncements,
        'published_announcements' => $publishedAnnouncements,
        'draft_announcements' => $draftAnnouncements
    ]);
    
} catch (Exception $e) {
    error_log("Admin stats error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch statistics: ' . $e->getMessage()
    ]);
} 
