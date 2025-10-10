<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/Auth.php';

header('Content-Type: application/json');

try {
    $db = Database::getInstance();
    $auth = Auth::getInstance();

    if (!$auth->isLoggedIn() || $auth->isAdmin()) {
        throw new Exception('Unauthorized: User not logged in or is admin');
    }

$userId = $auth->getCurrentUser()['id'];

    $userId = $auth->getCurrentUser()['id'];
    $conn = $db->getConnection();
    
    // First, let's check if the required tables exist
    $tables = ['user_quiz_attempts', 'quiz_questions', 'user_code_submissions', 'code_challenges', 'user_achievements', 'mini_game_results'];
    foreach ($tables as $table) {
        $check = $conn->query("SHOW TABLES LIKE '$table'");
        if ($check->rowCount() === 0) {
            throw new Exception("Required table '$table' does not exist");
        }
    }

    // Combine activity with labels where available (latest 10)
    $sql = '(
        SELECT "quiz" AS type, uqa.question_id AS ref_id, uqa.is_correct AS success, uqa.points_earned AS points, uqa.attempted_at AS at,
            CASE WHEN CHAR_LENGTH(qq.question) > 80 THEN CONCAT(LEFT(qq.question, 77), "...") ELSE qq.question END AS label
        FROM user_quiz_attempts uqa
        JOIN quiz_questions qq ON qq.id = uqa.question_id
        WHERE uqa.user_id = ?
    ) UNION ALL (
        SELECT "challenge" AS type, ucs.challenge_id AS ref_id, (ucs.status = "passed") AS success, ucs.points_earned AS points, ucs.submitted_at AS at,
            CASE WHEN CHAR_LENGTH(cc.title) > 80 THEN CONCAT(LEFT(cc.title, 77), "...") ELSE cc.title END AS label
        FROM user_code_submissions ucs
        JOIN code_challenges cc ON cc.id = ucs.challenge_id
        WHERE ucs.user_id = ?
    ) UNION ALL (
        SELECT "achievement" AS type, achievement_id AS ref_id, 1 AS success, NULL AS points, awarded_at AS at, NULL AS label
        FROM user_achievements WHERE user_id = ?
    ) UNION ALL (
        SELECT "mini_game" AS type, mgr.game_type AS ref_id, 1 AS success, mgr.score AS points, mgr.played_at AS at,
            CONCAT(mgr.game_type, COALESCE(CONCAT(" • ", JSON_UNQUOTE(JSON_EXTRACT(mgr.details, "$.language"))), "")) AS label
        FROM mini_game_results mgr WHERE mgr.user_id = ?
    ) ORDER BY at DESC LIMIT 10';

    $stmt = $conn->prepare($sql);
    $stmt->execute([$userId, $userId, $userId, $userId]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true, 
        'data' => $rows,
        'debug' => [
            'userId' => $userId,
            'query' => $sql,
            'params' => [$userId, $userId, $userId, $userId, $userId]
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false, 
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}
?>

