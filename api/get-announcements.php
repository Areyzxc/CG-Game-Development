<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    // Include database connection
    require_once __DIR__ . '/../includes/Database.php';
    $db = Database::getInstance();
    $pdo = $db->getConnection();

    // Simple query to get active announcements
    $query = "SELECT id, title, content, category as type, created_at, 
              'Admin' as author, is_pinned
              FROM announcements 
              WHERE is_active = 1 
              AND status = 'published'
              ORDER BY is_pinned DESC, created_at DESC 
              LIMIT 3";

    $stmt = $pdo->query($query);
    $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Add relative time and icon to each announcement
    foreach ($announcements as &$announcement) {
        $announcement['relative_time'] = getRelativeTime($announcement['created_at']);
        $announcement['icon'] = getAnnouncementIcon($announcement['type']);
    }

    // Return the response
    echo json_encode([
        'success' => true,
        'data' => $announcements
    ]);

} catch (Exception $e) {
    // Return error response
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch announcements: ' . $e->getMessage()
    ]);
}

// Helper function to get relative time
function getRelativeTime($datetime) {
    $time = time() - strtotime($datetime);
    if ($time < 60) return 'just now';
    if ($time < 3600) return floor($time/60) . ' minutes ago';
    if ($time < 86400) return floor($time/3600) . ' hours ago';
    if ($time < 2592000) return floor($time/86400) . ' days ago';
    return date('M j, Y', strtotime($datetime));
}

// Helper function for announcement icons
function getAnnouncementIcon($type) {
    $icons = [
        'update' => 'bx-star',
        'maintenance' => 'bx-wrench',
        'event' => 'bx-calendar-event',
        'feature' => 'bx-rocket',
        'bug_fix' => 'bx-bug',
        'general' => 'bx-info-circle'
    ];
    return $icons[strtolower($type)] ?? 'bx-info-circle';
}