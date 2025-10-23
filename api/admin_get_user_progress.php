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
    // Get user's points from quizzes and challenges
    $pointsQuery = $db->prepare("
        SELECT (
            SELECT COALESCE(SUM(points_earned), 0) 
            FROM user_quiz_attempts 
            WHERE user_id = :user_id1
        ) + (
            SELECT COALESCE(SUM(points_earned), 0)
            FROM user_challenge_attempts 
            WHERE user_id = :user_id2
        ) as total_points
    ");
    $pointsQuery->execute([
        ':user_id1' => $userId,
        ':user_id2' => $userId
    ]);
    $points = $pointsQuery->fetch(PDO::FETCH_COLUMN) ?: 0;

    // Get challenges completed (distinct correct attempts)
    $challengesQuery = $db->prepare("
        SELECT COUNT(DISTINCT question_id) 
        FROM user_challenge_attempts 
        WHERE user_id = :user_id AND is_correct = 1
    ");
    $challengesQuery->execute([':user_id' => $userId]);
    $challengesCompleted = (int)$challengesQuery->fetch(PDO::FETCH_COLUMN);

    // Get total available challenges
    $totalChallengesQuery = $db->prepare("
        SELECT COUNT(*) 
        FROM code_challenges 
        WHERE difficulty = 'expert'
    ");
    $totalChallengesQuery->execute();
    $totalChallenges = (int)$totalChallengesQuery->fetch(PDO::FETCH_COLUMN);

    // Get quizzes passed (distinct correct attempts)
    $quizzesQuery = $db->prepare("
        SELECT COUNT(DISTINCT question_id) 
        FROM user_quiz_attempts 
        WHERE user_id = :user_id AND is_correct = 1
    ");
    $quizzesQuery->execute([':user_id' => $userId]);
    $quizzesPassed = (int)$quizzesQuery->fetch(PDO::FETCH_COLUMN);

    // Get total available quiz questions
    $totalQuizzesQuery = $db->prepare("SELECT COUNT(*) FROM quiz_questions");
    $totalQuizzesQuery->execute();
    $totalQuizzes = (int)$totalQuizzesQuery->fetch(PDO::FETCH_COLUMN);

    // Get mini-games completed (distinct game types with correct attempts)
    $gamesQuery = $db->prepare("
        SELECT COUNT(DISTINCT mode_key) 
        FROM user_mini_game_attempts 
        WHERE user_id = :user_id AND is_correct = 1
    ");
    $gamesQuery->execute([':user_id' => $userId]);
    $miniGamesCompleted = (int)$gamesQuery->fetch(PDO::FETCH_COLUMN);

    // Get total available mini-games
    $totalGamesQuery = $db->prepare("SELECT COUNT(*) FROM mini_game_modes WHERE is_active = 1");
    $totalGamesQuery->execute();
    $totalGames = (int)$totalGamesQuery->fetch(PDO::FETCH_COLUMN);
    if ($totalGames === 0) $totalGames = 2; // Default to 2 if none found

    // Get tutorials progress
    $tutorialsQuery = $db->prepare("
        SELECT 
            COUNT(DISTINCT topic_id) as total_topics,
            SUM(CASE WHEN status = 'done_reading' THEN 1 ELSE 0 END) as completed_topics,
            SUM(CASE WHEN status = 'currently_reading' THEN 1 ELSE 0 END) as in_progress_topics
        FROM user_progress 
        WHERE user_id = :user_id
    ");
    $tutorialsQuery->execute([':user_id' => $userId]);
    $tutorialProgress = $tutorialsQuery->fetch(PDO::FETCH_ASSOC);

    // If no progress found, set default values
    if (!$tutorialProgress || $tutorialProgress['total_topics'] === null) {
        $tutorialProgress = [
            'total_topics' => 0,
            'completed_topics' => 0,
            'in_progress_topics' => 0
        ];
    }

    $tutorialsCompleted = (int)$tutorialProgress['completed_topics'];
    $tutorialsInProgress = (int)$tutorialProgress['in_progress_topics'];
    $totalTopics = (int)$tutorialProgress['total_topics'];
    $tutorialProgressPercentage = $totalTopics > 0 ? 
        round(($tutorialsCompleted / $totalTopics) * 100) : 0;

    // Calculate overall progress (same formula as profile.php)
    $overallProgress = min(100, 
        ($challengesCompleted * 5) + 
        ($quizzesPassed * 2) + 
        ($miniGamesCompleted * 10) + 
        ($tutorialProgressPercentage * 0.3)
    );
    $lastWeekProgress = min(100, $overallProgress * 0.8);
    $lastMonthProgress = min(100, $overallProgress * 0.9);

    echo json_encode([
        'success' => true,
        'data' => [
            'points' => (int)$points,
            'challenges_completed' => $challengesCompleted,
            'total_challenges' => $totalChallenges,
            'quizzes_passed' => $quizzesPassed,
            'total_quizzes' => $totalQuizzes,
            'mini_games_completed' => $miniGamesCompleted,
            'total_mini_games' => $totalGames,
            'tutorials_completed' => $tutorialsCompleted,
            'tutorials_in_progress' => $tutorialsInProgress,
            'total_topics' => $totalTopics,
            'tutorial_progress_percentage' => $tutorialProgressPercentage,
            'overall_progress' => round($overallProgress),
            'last_week_progress' => round($lastWeekProgress),
            'last_month_progress' => round($lastMonthProgress)
        ]
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false, 
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
