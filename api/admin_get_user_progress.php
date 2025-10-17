<?php
// api/admin_get_user_progress.php
// Returns progress data for a specific user (admin view)

header('Content-Type: application/json');

require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/Auth.php';

$auth = Auth::getInstance();
if (!$auth->isAdmin()) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$db = Database::getInstance()->getConnection();
$userId = $_GET['id'] ?? null;
if (!$userId) {
    echo json_encode(['success' => false, 'error' => 'User ID required']);
    exit;
}

// Example: Fetch progress stats (customize as needed)
try {
    // Example: completed tutorials, quizzes, challenges, total XP, etc.
    $stmt = $db->prepare('SELECT completed_tutorials, completed_quizzes, completed_challenges, total_xp FROM user_progress WHERE user_id = ?');
    $stmt->execute([$userId]);
    $progress = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$progress) {
        $progress = [
            'completed_tutorials' => 0,
            'completed_quizzes' => 0,
            'completed_challenges' => 0,
            'total_xp' => 0
        ];
    }
    echo json_encode(['success' => true, 'data' => $progress]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
