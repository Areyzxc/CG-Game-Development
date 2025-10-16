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
require_once '../includes/CSRFProtection.php';

try {
    $db = Database::getInstance();
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validate CSRF token for POST requests
        $csrf = CSRFProtection::getInstance();
        $csrfToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!$csrf->validateToken($csrfToken)) {
            throw new Exception('Invalid CSRF token');
        }
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
    
    // Extract and validate data
    $userId = isset($input['user_id']) && is_numeric($input['user_id']) ? (int)$input['user_id'] : null;
    $guestSessionId = isset($input['guest_session_id']) && is_numeric($input['guest_session_id']) ? (int)$input['guest_session_id'] : null;
    $nickname = isset($input['nickname']) ? trim($input['nickname']) : '';
    $totalScore = isset($input['total_score']) && is_numeric($input['total_score']) ? (int)$input['total_score'] : 0;
    $totalTime = isset($input['total_time']) && is_numeric($input['total_time']) ? (float)$input['total_time'] : 0;
    $questionsAttempted = isset($input['questions_attempted']) && is_numeric($input['questions_attempted']) ? (int)$input['questions_attempted'] : 0;
    $questionsCorrect = isset($input['questions_correct']) && is_numeric($input['questions_correct']) ? (int)$input['questions_correct'] : 0;
    
    // Validate required fields
    if (!$userId && !$guestSessionId) {
        throw new Exception('User ID or Guest Session ID is required');
    }
    
    if (empty($nickname)) {
        throw new Exception('Nickname is required');
    }
    
    // Validate nickname length and characters
    if (strlen($nickname) < 2 || strlen($nickname) > 50) {
        throw new Exception('Nickname must be between 2 and 50 characters');
    }
    
    // Sanitize nickname
    $nickname = htmlspecialchars($nickname, ENT_QUOTES, 'UTF-8');
    
    // Validate score and time ranges
    if ($totalScore < 0 || $totalScore > 10000) {
        throw new Exception('Invalid total score');
    }
    
    if ($totalTime < 0 || $totalTime > 10000) {
        throw new Exception('Invalid total time');
    }
    
    if ($questionsAttempted < 0 || $questionsAttempted > 100) {
        throw new Exception('Invalid questions attempted count');
    }
    
    if ($questionsCorrect < 0 || $questionsCorrect > $questionsAttempted) {
        throw new Exception('Invalid questions correct count');
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
