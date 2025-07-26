<?php
/**
 * File: api/challenge-leaderboard.php
 * Purpose: API endpoint for submitting and retrieving challenge leaderboard scores for CodeGaming.
 * Features:
 *   - Handles POST requests to submit or update user/guest challenge scores.
 *   - Handles GET requests to fetch leaderboard data (all-time, weekly, monthly scopes).
 *   - Calculates accuracy and formats leaderboard for frontend display.
 * Usage:
 *   - Called via AJAX from challenge.js to submit scores and display leaderboard.
 *   - Requires Database.php for DB access.
 * Included Files/Dependencies:
 *   - includes/Database.php
 * Author: CodeGaming Team
 * Last Updated: July 24, 2025
 */
header('Content-Type: application/json');
require_once '../includes/Database.php';

try {
    $db = Database::getInstance();
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Handle POST request - submit final score
        handlePostRequest($db);
    } else {
        // Handle GET request - fetch leaderboard
        handleGetRequest($db);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

function handlePostRequest($db) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Invalid input data');
    }
    
    $userId = $input['user_id'] ?? null;
    $guestSessionId = $input['guest_session_id'] ?? null;
    $nickname = $input['nickname'] ?? '';
    $totalScore = $input['total_score'] ?? 0;
    $totalTime = $input['total_time'] ?? 0;
    $questionsAttempted = $input['questions_attempted'] ?? 0;
    $questionsCorrect = $input['questions_correct'] ?? 0;
    
    // Validate required fields
    if (!$userId && !$guestSessionId) {
        throw new Exception('User ID or Guest Session ID is required');
    }
    
    if (!$nickname) {
        throw new Exception('Nickname is required');
    }
    
    // Insert or update leaderboard entry
    $stmt = $db->prepare("
        INSERT INTO challenge_leaderboard 
        (user_id, guest_session_id, nickname, total_score, total_time, questions_attempted, questions_correct)
        VALUES (?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
        total_score = GREATEST(total_score, VALUES(total_score)),
        total_time = CASE 
            WHEN VALUES(total_score) > total_score THEN VALUES(total_time)
            ELSE total_time
        END,
        questions_attempted = VALUES(questions_attempted),
        questions_correct = VALUES(questions_correct),
        completed_at = CURRENT_TIMESTAMP
    ");
    
    $stmt->execute([
        $userId, 
        $guestSessionId, 
        $nickname, 
        $totalScore, 
        $totalTime, 
        $questionsAttempted, 
        $questionsCorrect
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Score submitted successfully'
    ]);
}

function handleGetRequest($db) {
    $scope = $_GET['scope'] ?? 'alltime';
    
    // Build the query based on scope
    $whereClause = '';
    $params = [];
    
    switch ($scope) {
        case 'weekly':
            $whereClause = 'WHERE completed_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)';
            break;
        case 'monthly':
            $whereClause = 'WHERE completed_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)';
            break;
        default: // alltime
            $whereClause = '';
            break;
    }
    
    // Fetch leaderboard data
    $stmt = $db->prepare("
        SELECT 
            cl.id,
            cl.user_id,
            cl.guest_session_id,
            COALESCE(cl.nickname, u.username) as display_name,
            cl.total_score,
            cl.total_time,
            cl.questions_attempted,
            cl.questions_correct,
            cl.completed_at
        FROM challenge_leaderboard cl
        LEFT JOIN users u ON cl.user_id = u.id
        $whereClause
        ORDER BY cl.total_score DESC, cl.total_time ASC
        LIMIT 50
    ");
    
    $stmt->execute($params);
    $leaderboard = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format the data
    $formattedLeaderboard = array_map(function($row) {
        return [
            'id' => $row['id'],
            'username' => $row['display_name'],
            'score' => $row['total_score'],
            'time' => $row['total_time'],
            'accuracy' => $row['questions_attempted'] > 0 ? 
                round(($row['questions_correct'] / $row['questions_attempted']) * 100, 1) : 0,
            'played_at' => $row['completed_at']
        ];
    }, $leaderboard);
    
    echo json_encode([
        'success' => true,
        'leaderboard' => $formattedLeaderboard,
        'scope' => $scope
    ]);
}
?> 