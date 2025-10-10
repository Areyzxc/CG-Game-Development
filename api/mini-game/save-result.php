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
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Prevent any output before JSON
ob_start();

// Check if user is logged in (allow guests)
$auth = Auth::getInstance();
$userId = null;

if ($auth->isLoggedIn()) {
    $currentUser = $auth->getCurrentUser();
    $userId = $currentUser['id']; // Fixed: use 'id' not 'user_id'
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

// Validate required fields (support both formats)
$gameType = $data['game_type'] ?? $data['gameType'] ?? null;
$language = $data['language'] ?? null;
$score = $data['score'] ?? null;

if (!$gameType || !$language || !isset($score)) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields: game_type, language, score']);
    exit;
}

// Validate game type
$validGameTypes = ['guess', 'typing'];
if (!in_array($gameType, $validGameTypes)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid game type']);
    exit;
}

// Validate language
$validLanguages = ['html', 'css', 'javascript', 'bootstrap', 'java', 'python', 'cpp'];
if (!in_array($language, $validLanguages)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid language']);
    exit;
}

try {
    $db = Database::getInstance();
    
    // Prepare the SQL statement
    $sql = "INSERT INTO mini_game_results (user_id, game_type, score, time_taken, details) 
            VALUES (?, ?, ?, ?, ?)";
    
    // Create details JSON with all submitted data
    $details = json_encode([
        'language' => $language,
        'difficulty' => $data['difficulty'] ?? 'beginner',
        'timestamp' => date('Y-m-d H:i:s'),
        'details' => $data['details'] ?? []
    ]);
    
    // Execute the query
    $stmt = $db->prepare($sql);
    $stmt->execute([
        $userId,
        $gameType,
        $score,
        $data['time_taken'] ?? null,
        $details
    ]);
    
    // Clean output buffer and return success response
    ob_clean();
    echo json_encode([
        'success' => true,
        'message' => 'Result saved successfully',
        'resultId' => $db->lastInsertId()
    ]);
    
} catch (PDOException $e) {
    ob_clean();
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error',
        'message' => $e->getMessage()
    ]);
} catch (Exception $e) {
    ob_clean();
    http_response_code(500);
    echo json_encode([
        'error' => 'Server error',
        'message' => $e->getMessage()
    ]);
}

// Ensure clean exit
exit;
