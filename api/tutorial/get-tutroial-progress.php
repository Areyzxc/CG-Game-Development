<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../../includes/Database.php';
require_once '../../includes/Auth.php';

$response = [
    'success' => false,
    'data' => [
        'total_topics' => 0,
        'completed_topics' => 0,
        'in_progress_topics' => 0,
        'progress_percentage' => 0
    ]
];

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    $auth = Auth::getInstance();
    $user = $auth->isLoggedIn() ? $auth->getCurrentUser() : null;
    $userId = $user ? $user['id'] : null;

    // Get total number of topics (you may need to adjust this query based on your topics table)
    $stmt = $conn->query("SELECT COUNT(*) as total FROM topics WHERE is_active = 1");
    $totalTopics = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    if ($userId) {
        // For logged-in users, get progress from database
        $stmt = $conn->prepare("
            SELECT 
                COUNT(*) as completed_topics,
                SUM(CASE WHEN status = 'currently_reading' THEN 1 ELSE 0 END) as in_progress
            FROM user_progress 
            WHERE user_id = ? AND status IN ('currently_reading', 'done_reading')
        ");
        $stmt->execute([$userId]);
        $progress = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $response['data'] = [
            'total_topics' => (int)$totalTopics,
            'completed_topics' => (int)($progress['completed_topics'] ?? 0),
            'in_progress_topics' => (int)($progress['in_progress'] ?? 0),
            'progress_percentage' => $totalTopics > 0 ? 
                round((($progress['completed_topics'] ?? 0) / $totalTopics) * 100) : 0
        ];
    } else {
        // For guests, get from session
        session_start();
        $guestProgress = $_SESSION['guest_tutorial_progress'] ?? [];
        $completed = 0;
        $inProgress = 0;
        
        foreach ($guestProgress as $topic) {
            if ($topic['status'] === 'done_reading') {
                $completed++;
            } elseif ($topic['status'] === 'currently_reading') {
                $inProgress++;
            }
        }
        
        $response['data'] = [
            'total_topics' => (int)$totalTopics,
            'completed_topics' => $completed,
            'in_progress_topics' => $inProgress,
            'progress_percentage' => $totalTopics > 0 ? 
                round(($completed / $totalTopics) * 100) : 0
        ];
    }
    
    $response['success'] = true;
    
} catch (Exception $e) {
    http_response_code(500);
    $response['error'] = $e->getMessage();
}

echo json_encode($response);