<?php
/**
 * File: api/admin_get_announcements.php
 * Purpose: Fetches announcements for admin dashboard, with search functionality.
 * Features:
 *   - Admin authentication check.
 *   - Fetches all announcements or filtered by search query.
 *   - Returns JSON response with announcement data.
 * 
 * Usage:
 *   - Called via AJAX from the admin panel to display announcements.
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
    
    // Main list (paginated, 10 per page)
    $page = max(1, intval($_GET['page'] ?? 1));
    $limit = 10;
    $offset = ($page - 1) * $limit;
    $where = 'WHERE is_active=1';
    $params = [];
    if (!empty($_GET['q'])) {
        $where .= ' AND (title LIKE ? OR content LIKE ?)';
        $params[] = '%' . $_GET['q'] . '%';
        $params[] = '%' . $_GET['q'] . '%';
    }
    if (!empty($_GET['status'])) {
        $where .= ' AND status=?';
        $params[] = $_GET['status'];
    }
    if (!empty($_GET['category'])) {
        $where .= ' AND category=?';
        $params[] = $_GET['category'];
    }
    $sql = "SELECT a.*, au.username as created_by FROM announcements a LEFT JOIN admin_users au ON a.created_by = au.admin_id $where ORDER BY is_pinned DESC, created_at DESC LIMIT $limit OFFSET $offset";
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $announcements = [];
    while ($row = $stmt->fetch()) {
        $date = new DateTime($row['created_at']);
        $now = new DateTime();
        $diff = $now->diff($date);
        
        if ($diff->days == 0) {
            $timeAgo = 'Today';
        } elseif ($diff->days == 1) {
            $timeAgo = 'Yesterday';
        } else {
            $timeAgo = $diff->days . ' days ago';
        }
        
        $announcements[] = [
            'id' => $row['id'],
            'title' => $row['title'],
            'content' => $row['content'],
            'category' => $row['category'],
            'status' => $row['status'],
            'is_pinned' => $row['is_pinned'],
            'date' => $timeAgo,
            'created_by' => $row['created_by'] ?? 'System'
        ];
    }
    
    // Recent (last 5)
    $recent = [];
    $stmt = $conn->query("SELECT id, title FROM announcements WHERE is_active=1 ORDER BY created_at DESC LIMIT 5");
    while ($row = $stmt->fetch()) {
        $recent[] = [ 'id' => $row['id'], 'title' => $row['title'] ];
    }
    
    // Stats
    $stats = [
        'total' => $conn->query('SELECT COUNT(*) FROM announcements WHERE is_active=1')->fetchColumn(),
        'drafts' => $conn->query("SELECT COUNT(*) FROM announcements WHERE is_active=1 AND status='draft'")->fetchColumn()
    ];
    
    // Featured
    $featured = null;
    $stmt = $conn->query("SELECT id, title FROM announcements WHERE is_active=1 AND is_pinned=1 LIMIT 1");
    if ($row = $stmt->fetch()) {
        $featured = [ 'id' => $row['id'], 'title' => $row['title'] ];
    }
    
    echo json_encode([
        'success' => true,
        'announcements' => $announcements,
        'recent' => $recent,
        'stats' => $stats,
        'featured' => $featured,
        'total_pages' => ceil($stats['total'] / $limit)
    ]);
    
} catch (Exception $e) {
    error_log("Admin announcements error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch announcements'
    ]);
} 