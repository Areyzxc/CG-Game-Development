<?php
/**
 * File: api/mini-game/user-results.php
 * Purpose: API endpoint to retrieve the last 10 results of a user in mini-games.
 * 
 * Features:
 *   - Returns game type, score, time taken, and language for each result.
 *   - Requires user to be logged in and can be played by both users and guests.
 *   - Handles JSON response format.
 * 
 * Usage:
 *   - Called by the frontend to display user results in their profile.
 *   - Requires Database.php and Auth.php for DB access and user authentication.
 * 
 * Included Files/Dependencies:
 *   - includes/Database.php
 *   - includes/Auth.php
 * 
 * Author: CodeGaming Team
 * Last Updated: July 26, 2025
 */
require_once '../../includes/Database.php';
require_once '../../includes/Auth.php';

header('Content-Type: application/json');

// Check if user is logged in
$auth = Auth::getInstance();
if (!$auth->isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'User must be logged in to view results']);
    exit;
}

// Get current user
$currentUser = $auth->getCurrentUser();
$userId = $currentUser['user_id'];

try {
    $db = Database::getInstance();
    
    // Get user's 10 most recent results
    $sql = "SELECT 
                game_type,
                score,
                time_taken,
                played_at,
                JSON_UNQUOTE(JSON_EXTRACT(details, '$.language')) as language
            FROM mini_game_results
            WHERE user_id = ?
            ORDER BY played_at DESC
            LIMIT 10";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([$userId]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format the results
    $userResults = [];
    foreach ($results as $result) {
        $userResults[] = [
            'gameType' => $result['game_type'],
            'score' => (int)$result['score'],
            'timeTaken' => $result['time_taken'] ? (float)$result['time_taken'] : null,
            'language' => $result['language'],
            'playedAt' => $result['played_at']
        ];
    }
    
    echo json_encode($userResults);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error',
        'message' => $e->getMessage()
    ]);
} 