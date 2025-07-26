<?php
/**
 * File: api/get_announcements.php
 * Purpose: API endpoint for retrieving paginated user announcements with author info for CodeGaming.
 * Features:
 *   - Fetches announcements with author name and avatar, sorted by pinned status and date.
 *   - Supports pagination and returns total count and pages.
 *   - Returns JSON response for success or error.
 * Usage:
 *   - Called via AJAX from announcements.js and user-facing pages for displaying announcements.
 *   - Requires Database.php for DB access.
 * Included Files/Dependencies:
 *   - includes/Database.php
 * Author: CodeGaming Team
 * Last Updated: July 24, 2025
 */
require_once '../includes/Database.php';
header('Content-Type: application/json');

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    // Pagination
    $page = max(1, intval($_GET['page'] ?? 1));
    $limit = 10;
    $offset = ($page - 1) * $limit;
    
    // Fetch announcements with author info
    $sql = "SELECT a.id, a.title, a.content, a.is_pinned, a.created_at, au.username AS author_name, au.profile_picture AS author_avatar
            FROM announcements a
            LEFT JOIN admin_users au ON a.created_by = au.admin_id
            WHERE a.is_active = 1
            ORDER BY a.is_pinned DESC, a.created_at DESC
            LIMIT :limit OFFSET :offset";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $announcements = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $announcements[] = [
            'id' => $row['id'],
            'title' => $row['title'],
            'content' => $row['content'],
            'is_pinned' => $row['is_pinned'],
            'created_at' => date('F j, Y, g:i a', strtotime($row['created_at'])),
            'author_name' => $row['author_name'] ?? 'Admin',
            'author_avatar' => !empty($row['author_avatar']) ? $row['author_avatar'] : 'images/PTC.png'
        ];
    }
    // Total count for pagination
    $total = $conn->query('SELECT COUNT(*) FROM announcements WHERE is_active=1')->fetchColumn();
    $total_pages = ceil($total / $limit);
    echo json_encode([
        'success' => true,
        'announcements' => $announcements,
        'total' => intval($total),
        'total_pages' => $total_pages
    ]);
} catch (Exception $e) {
    error_log('User announcements error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch announcements.'
    ]);
}