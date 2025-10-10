<?php
/**
 * File: api/get-feedback.php
 * Purpose: Fetches feedback messages for display on the About page feedback wall
 * Features:
 *   - Retrieves recent feedback messages with like counts
 *   - Supports pagination and filtering
 *   - Returns formatted data for the feedback wall display
 * Usage:
 *   - Called via GET/POST from the About page to populate feedback wall
 *   - Optional parameters: limit, offset, filter
 * Included Files/Dependencies:
 *   - includes/Database.php
 * Author: CodeGaming Team
 * Last Updated: September 29, 2025
 */
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/Database.php';

try {
    $conn = Database::getInstance()->getConnection();
    
    // Get parameters
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
    $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
    $limit = min($limit, 50); // Max 50 items per request
    
    // Fetch feedback messages with like counts
    $stmt = $conn->prepare("
        SELECT 
            fm.id,
            fm.sender_name,
            fm.sender_email,
            fm.message,
            fm.created_at,
            COUNT(fl.id) as like_count
        FROM feedback_messages fm
        LEFT JOIN feedback_likes fl ON fm.id = fl.feedback_id
        WHERE fm.proponent_email = 'about-page-feedback' OR fm.proponent_email IS NULL
        GROUP BY fm.id
        ORDER BY fm.created_at DESC
        LIMIT ? OFFSET ?
    ");
    
    $stmt->execute([$limit, $offset]);
    $feedback = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format the data for frontend consumption
    $formattedFeedback = [];
    foreach ($feedback as $item) {
        $formattedFeedback[] = [
            'id' => $item['id'],
            'name' => htmlspecialchars($item['sender_name']),
            'message' => htmlspecialchars($item['message']),
            'likes' => intval($item['like_count']),
            'created_at' => $item['created_at'],
            'time_ago' => timeAgo($item['created_at'])
        ];
    }
    
    // Get total count for pagination
    $countStmt = $conn->prepare("
        SELECT COUNT(*) 
        FROM feedback_messages 
        WHERE proponent_email = 'about-page-feedback' OR proponent_email IS NULL
    ");
    $countStmt->execute();
    $totalCount = $countStmt->fetchColumn();
    
    echo json_encode([
        'success' => true,
        'feedback' => $formattedFeedback,
        'total' => intval($totalCount),
        'limit' => $limit,
        'offset' => $offset,
        'has_more' => ($offset + $limit) < $totalCount
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}

/**
 * Helper function to convert timestamp to human-readable time ago format
 */
function timeAgo($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'Just now';
    if ($time < 3600) return floor($time/60) . ' minutes ago';
    if ($time < 86400) return floor($time/3600) . ' hours ago';
    if ($time < 2592000) return floor($time/86400) . ' days ago';
    if ($time < 31536000) return floor($time/2592000) . ' months ago';
    
    return floor($time/31536000) . ' years ago';
}
?>
