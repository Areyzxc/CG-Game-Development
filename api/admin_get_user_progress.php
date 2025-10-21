<?php
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

try {
    // Get tutorials progress
    $tutorialStmt = $db->prepare('
        SELECT COUNT(*) as completed_tutorials 
        FROM user_progress 
        WHERE user_id = ? AND status = "done_reading"
    ');
    $tutorialStmt->execute([$userId]);
    $completedTutorials = $tutorialStmt->fetch(PDO::FETCH_ASSOC)['completed_tutorials'];

    // Get quiz progress
    $quizStmt = $db->prepare('
        SELECT COUNT(DISTINCT question_id) as completed_quizzes 
        FROM user_quiz_attempts 
        WHERE user_id = ? AND is_correct = 1
    ');
    $quizStmt->execute([$userId]);
    $completedQuizzes = $quizStmt->fetch(PDO::FETCH_ASSOC)['completed_quizzes'];

    // Get challenge progress
    $challengeStmt = $db->prepare('
        SELECT COUNT(DISTINCT question_id) as completed_challenges 
        FROM user_challenge_attempts 
        WHERE user_id = ? AND status = "passed"
    ');
    $challengeStmt->execute([$userId]);
    $completedChallenges = $challengeStmt->fetch(PDO::FETCH_ASSOC)['completed_challenges'];

    // Get mini-games progress (Guess and Typing games)
    $miniGamesStmt = $db->prepare('
        SELECT COUNT(DISTINCT game_type) as completed_mini_games
        FROM mini_game_results 
        WHERE user_id = ? AND game_type IN ("guess", "typing")
    ');
    $miniGamesStmt->execute([$userId]);
    $completedMiniGames = $miniGamesStmt->fetch(PDO::FETCH_ASSOC)['completed_mini_games'];

    // Get total XP
    $xpStmt = $db->prepare('
        SELECT COALESCE(SUM(points_earned), 0) as total_xp
        FROM (
            SELECT points_earned FROM user_quiz_attempts WHERE user_id = ?
            UNION ALL
            SELECT points_earned FROM user_challenge_attempts WHERE user_id = ?
            UNION ALL
            SELECT points_earned FROM mini_game_results WHERE user_id = ?
        ) as points
    ');
    $xpStmt->execute([$userId, $userId, $userId]);
    $totalXP = $xpStmt->fetch(PDO::FETCH_ASSOC)['total_xp'];

    echo json_encode([
        'success' => true,
        'data' => [
            'completed_tutorials' => (int)$completedTutorials,
            'completed_quizzes' => (int)$completedQuizzes,
            'completed_challenges' => (int)$completedChallenges,
            'completed_mini_games' => (int)$completedMiniGames,
            'total_xp' => (int)$totalXP
        ]
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false, 
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
