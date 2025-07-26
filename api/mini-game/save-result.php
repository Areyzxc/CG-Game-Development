<?php
/**
 * File: api/mini-game/save-result.php
 * Purpose: API endpoint to save results of mini-games played by users.
 * Features:
 *   - Accepts POST requests with game type, language, score, and time taken.
 *   - Validates input and checks for required fields.
 *   - Saves results to mini_game_results table.
 *   - Returns success or error response in JSON format.
 * 
 * Usage:
 *   - Called by the frontend after a mini-game is completed.
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
    echo json_encode(['error' => 'User must be logged in to save results']);
    exit;
}

// Get current user
$currentUser = $auth->getCurrentUser();
$userId = $currentUser['user_id'];

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

// Validate required fields
if (!isset($data['gameType']) || !isset($data['language']) || !isset($data['score'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

// Validate game type
$validGameTypes = ['guess', 'typing'];
if (!in_array($data['gameType'], $validGameTypes)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid game type']);
    exit;
}

// Validate language
$validLanguages = ['html', 'css', 'javascript', 'bootstrap', 'java', 'python', 'cpp'];
if (!in_array($data['language'], $validLanguages)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid language']);
    exit;
}

try {
    $db = Database::getInstance();
    
    // Prepare the SQL statement
    $sql = "INSERT INTO mini_game_results (user_id, game_type, score, time_taken, details) 
            VALUES (?, ?, ?, ?, ?)";
    
    // Create details JSON
    $details = json_encode([
        'language' => $data['language'],
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
    // Execute the query
    $stmt = $db->prepare($sql);
    $stmt->execute([
        $userId,
        $data['gameType'],
        $data['score'],
        $data['timeTaken'] ?? null,
        $details
    ]);
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Result saved successfully',
        'resultId' => $db->lastInsertId()
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error',
        'message' => $e->getMessage()
    ]);
} 