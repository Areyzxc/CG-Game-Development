<?php
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/Auth.php';
require_once __DIR__ . '/../includes/CSRFProtection.php';

header('Content-Type: application/json');

$db = Database::getInstance();
$auth = Auth::getInstance();

if (!$auth->isLoggedIn() || $auth->isAdmin()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$user = $auth->getCurrentUser();
$userId = $user['id'];

try {
    $conn = $db->getConnection();

    // Points aggregate
    $pointsStmt = $conn->prepare('SELECT COALESCE(SUM(points_earned),0) AS points FROM (
        SELECT points_earned FROM user_quiz_attempts WHERE user_id = ?
        UNION ALL
        SELECT points_earned FROM user_code_submissions WHERE user_id = ?
    ) AS p');
    $pointsStmt->execute([$userId, $userId]);
    $points = (int) ($pointsStmt->fetch()['points'] ?? 0);

    // Rank by points among users
    $rankStmt = $conn->prepare('SELECT 1 + COUNT(*) AS rank FROM (
        SELECT u.id, COALESCE(pq.points,0) + COALESCE(pc.points,0) AS total
        FROM users u
        LEFT JOIN (
            SELECT user_id, SUM(points_earned) AS points
            FROM user_quiz_attempts GROUP BY user_id
        ) pq ON pq.user_id = u.id
        LEFT JOIN (
            SELECT user_id, SUM(points_earned) AS points
            FROM user_code_submissions GROUP BY user_id
        ) pc ON pc.user_id = u.id
    ) totals WHERE total > (
        SELECT COALESCE(pq.points,0) + COALESCE(pc.points,0) FROM (
            SELECT SUM(points_earned) AS points FROM user_quiz_attempts WHERE user_id = ?
        ) pq, (
            SELECT SUM(points_earned) AS points FROM user_code_submissions WHERE user_id = ?
        ) pc
    )');
    $rankStmt->execute([$userId, $userId]);
    $rankRow = $rankStmt->fetch();
    $rank = '#' . (int) ($rankRow['rank'] ?? 1);

    // Completed counts
    $chalStmt = $conn->prepare('SELECT COUNT(*) AS cnt FROM user_code_submissions WHERE user_id = ? AND status = "passed"');
    $chalStmt->execute([$userId]);
    $challenges = (int) ($chalStmt->fetch()['cnt'] ?? 0);

    $quizStmt = $conn->prepare('SELECT COUNT(DISTINCT question_id) AS cnt FROM user_quiz_attempts WHERE user_id = ? AND is_correct = 1');
    $quizStmt->execute([$userId]);
    $quizzes = (int) ($quizStmt->fetch()['cnt'] ?? 0);

    echo json_encode([
        'success' => true,
        'data' => [
            'points' => $points,
            'rank' => $rank,
            'challenges_completed' => $challenges,
            'quizzes_passed' => $quizzes
        ]
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to load stats']);
}
?>


