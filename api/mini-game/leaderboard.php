<?php
/**
 * ==========================================================
 * Endpoint:  api/mini-game/leaderboard.php
 * Description:
 *   - Fetches the leaderboard for mini-games.
 *   - Returns top 10 results for each game type.
 *   - Supports filtering by game type and language.
 * 
 * Usage:
 *   - Called by the frontend to display mini-game leaderboards.
 *   - Requires Database.php for DB access.
 * 
 * Included Files/Dependencies:
 *   - includes/Database.php
 * 
 * Author: CodeGaming Team
 * Last Updated: July 26, 2025
 */
require_once '../../includes/Database.php';

header('Content-Type: application/json');

try {
    $db = Database::getInstance();
    
    // Get top 10 results for each game type
    $sql = "SELECT 
                m.user_id,
                u.username,
                m.game_type,
                m.score,
                m.time_taken,
                m.played_at,
                JSON_UNQUOTE(JSON_EXTRACT(m.details, '$.language')) as language
            FROM mini_game_results m
            JOIN users u ON m.user_id = u.user_id
            WHERE m.id IN (
                SELECT id
                FROM (
                    SELECT id,
                        ROW_NUMBER() OVER (PARTITION BY game_type ORDER BY score DESC) as rn
                    FROM mini_game_results
                ) ranked
                WHERE rn <= 10
            )
            ORDER BY m.game_type, m.score DESC";
    
    $stmt = $db->query($sql);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format the results
    $leaderboard = [];
    foreach ($results as $result) {
        $leaderboard[] = [
            'username' => $result['username'],
            'gameType' => $result['game_type'],
            'score' => (int)$result['score'],
            'timeTaken' => $result['time_taken'] ? (float)$result['time_taken'] : null,
            'language' => $result['language'],
            'playedAt' => $result['played_at']
        ];
    }
    
    echo json_encode($leaderboard);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error',
        'message' => $e->getMessage()
    ]);
} 