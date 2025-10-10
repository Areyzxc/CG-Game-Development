<?php
/**
 * ==========================================================
 * Endpoint:  api/mini-game/leaderboard.php
 * Description:
 *   - Enhanced leaderboard for mini-games with user stats
 *   - Returns top results with scope filtering (alltime, weekly, monthly)
 *   - Supports user identification and guest sessions
 * 
 * Usage:
 *   - Called by frontend and home page for leaderboard display
 *   - Supports query parameters: scope, user_id, guest_session_id, nickname
 * 
 * Author: CodeGaming Team
 * Last Updated: September 29, 2025
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../../includes/Database.php';
require_once '../../includes/Auth.php';

try {
    $db = Database::getInstance();
    
    // Get parameters
    $scope = $_GET['scope'] ?? 'alltime';
    $userId = $_GET['user_id'] ?? null;
    $guestSessionId = $_GET['guest_session_id'] ?? null;
    $nickname = $_GET['nickname'] ?? null;
    $gameType = $_GET['game_type'] ?? null;
    $page = max(1, intval($_GET['page'] ?? 1));
    $limit = 15; // Items per page
    $offset = ($page - 1) * $limit;
    
    // Debug logging
    error_log("Leaderboard API called with: scope=$scope, userId=$userId, gameType=$gameType, page=$page");
    
    // Build date filter based on scope
    $dateFilter = '';
    switch ($scope) {
        case 'weekly':
            $dateFilter = "AND m.played_at >= DATE_SUB(NOW(), INTERVAL 1 WEEK)";
            break;
        case 'monthly':
            $dateFilter = "AND m.played_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
            break;
        case 'alltime':
        default:
            $dateFilter = '';
            break;
    }
    
    // Build game type filter
    $gameTypeFilter = '';
    if ($gameType) {
        $gameTypeFilter = "AND m.game_type = '$gameType'";
    }
    
    // Get user stats if user is identified
    $userStats = null;
    if ($userId) {
        $userStatsQuery = "
            SELECT 
                COUNT(*) as total_games,
                MAX(score) as best_score,
                AVG(score) as avg_score,
                MAX(CASE WHEN game_type = 'typing' THEN score END) as best_wpm,
                MAX(played_at) as last_played,
                (SELECT score FROM mini_game_results WHERE user_id = ? ORDER BY played_at DESC LIMIT 1) as recent_score
            FROM mini_game_results 
            WHERE user_id = ? $dateFilter
        ";
        $stmt = $db->prepare($userStatsQuery);
        $stmt->execute([$userId, $userId]);
        $userStats = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($userStats) {
            $userStats['avg_score'] = $userStats['avg_score'] ? round($userStats['avg_score'], 1) : null;
        }
    }
    
    // Get total count for pagination
    $countQuery = "
        SELECT COUNT(*) as total
        FROM mini_game_results m
        WHERE 1=1 $dateFilter $gameTypeFilter
    ";
    $stmt = $db->prepare($countQuery);
    $stmt->execute();
    $totalResults = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = ceil($totalResults / $limit);
    
    // Get leaderboard
    $leaderboardQuery = "
        SELECT 
            m.user_id,
            u.username,
            m.game_type,
            m.score,
            m.time_taken,
            m.played_at,
            JSON_UNQUOTE(JSON_EXTRACT(m.details, '$.language')) as language,
            " . ($userId ? "CASE WHEN m.user_id = ? THEN 1 ELSE 0 END as is_me" : "0 as is_me") . "
        FROM mini_game_results m
        LEFT JOIN users u ON m.user_id = u.id
        WHERE 1=1 $dateFilter $gameTypeFilter
        ORDER BY m.score DESC, m.played_at DESC
        LIMIT $limit OFFSET $offset
    ";
    
    $params = $userId ? [$userId] : [];
    $stmt = $db->prepare($leaderboardQuery);
    $stmt->execute($params);
    $leaderboardResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Debug logging
    error_log("Leaderboard query returned " . count($leaderboardResults) . " results");
    
    // Get top player
    $topPlayer = null;
    if (!empty($leaderboardResults)) {
        $topPlayer = [
            'username' => $leaderboardResults[0]['username'],
            'nickname' => null, // Can be extended for guest support
            'score' => (int)$leaderboardResults[0]['score']
        ];
    }
    
    // Format leaderboard
    $leaderboard = [];
    foreach ($leaderboardResults as $result) {
        $leaderboard[] = [
            'username' => $result['username'] ?: 'Guest Player',
            'nickname' => null, // Can be extended for guest support
            'game_type' => $result['game_type'],
            'score' => (int)$result['score'],
            'time_taken' => $result['time_taken'] ? (float)$result['time_taken'] : null,
            'language' => $result['language'],
            'played_at' => $result['played_at'],
            'completed_at' => $result['played_at'], // Alias for compatibility
            'created_at' => $result['played_at'], // Alias for compatibility
            'is_me' => (bool)$result['is_me']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'leaderboard' => $leaderboard,
        'user_stats' => $userStats,
        'top_player' => $topPlayer,
        'scope' => $scope,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_results' => $totalResults,
            'per_page' => $limit,
            'has_next' => $page < $totalPages,
            'has_prev' => $page > 1
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to load leaderboard',
        'message' => $e->getMessage()
    ]);
}
