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
 * Last Updated: October 25, 2025
 */

// Enable error reporting for debugging
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Set headers first to prevent any output issues
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Prevent any output before JSON
ob_start();

// Function to send JSON response and exit
function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    ob_clean();
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit();
}

// Function to send error response
function sendError($message, $statusCode = 400, $details = []) {
    $response = [
        'success' => false,
        'error' => $message,
        'message' => $message
    ];
    
    if (!empty($details)) {
        $response['details'] = $details;
    }
    
    sendJsonResponse($response, $statusCode);
}

// Include required files
try {
    require_once '../../includes/Database.php';
    require_once '../../includes/Auth.php';
} catch (Exception $e) {
    sendError('Server configuration error', 500, ['details' => $e->getMessage()]);
}

try {
    // Check if user is logged in (allow guests)
    $auth = Auth::getInstance();
    $userId = null;

    if ($auth->isLoggedIn()) {
        $currentUser = $auth->getCurrentUser();
        $userId = $currentUser['id'] ?? null;
    }

    // Get POST data
    $json = file_get_contents('php://input');
    if (empty($json)) {
        sendError('No data received', 400);
    }

    $data = json_decode($json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        sendError('Invalid JSON data: ' . json_last_error_msg(), 400);
    }

    // Validate required fields (support both formats)
    $gameType = $data['game_type'] ?? $data['gameType'] ?? null;
    $language = $data['language'] ?? null;
    $score = $data['score'] ?? null;

    if (!$gameType || !$language || !isset($score)) {
        sendError('Missing required fields: game_type, language, score', 400, [
            'received' => array_keys($data),
            'required' => ['game_type', 'language', 'score']
        ]);
    }

    // Validate game type
    $validGameTypes = ['guess', 'typing', 'memory', 'quiz'];
    if (!in_array($gameType, $validGameTypes)) {
        sendError('Invalid game type. Must be one of: ' . implode(', ', $validGameTypes), 400);
    }

    // Validate language
    $validLanguages = ['html', 'css', 'javascript', 'bootstrap', 'java', 'python', 'cpp', 'general'];
    if (!in_array(strtolower($language), $validLanguages)) {
        sendError('Invalid language. Must be one of: ' . implode(', ', $validLanguages), 400);
    }

    // Get database connection
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    // Prepare details with all submitted data
    $details = [
        'language' => strtolower($language),
        'difficulty' => $data['difficulty'] ?? ($data['details']['difficulty'] ?? 'beginner'),
        'timestamp' => date('Y-m-d H:i:s'),
        'details' => $data['details'] ?? []
    ];
    
    // Add user agent and IP for analytics (if needed)
    $details['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? null;
    $details['ip_address'] = $_SERVER['REMOTE_ADDR'] ?? null;
    
    $detailsJson = json_encode($details, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    
    // Prepare the SQL statement with correct number of placeholders
    $sql = "INSERT INTO mini_game_results 
    (user_id, game_type, score, time_taken, details, played_at) 
    VALUES (?, ?, ?, ?, ?, NOW())";
    
    // Execute the query with correct number of parameters
    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute([
        $userId,
        $gameType,
        (float) $score,
        isset($data['time_taken']) ? (float) $data['time_taken'] : null,
        $detailsJson
    ]);
    
    if (!$success) {
        $errorInfo = $stmt->errorInfo();
        throw new PDOException($errorInfo[2] ?? 'Unknown database error');
    }
    
    // Return success response
    sendJsonResponse([
        'success' => true,
        'message' => 'Result saved successfully',
        'resultId' => $pdo->lastInsertId(),
        'timestamp' => date('c')
    ]);
    
} catch (PDOException $e) {
    error_log('Database error in save-result.php: ' . $e->getMessage());
    sendError('Database error occurred', 500, [
        'code' => $e->getCode(),
        'message' => $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log('Error in save-result.php: ' . $e->getMessage());
    sendError('An error occurred while saving your result', 500, [
        'code' => $e->getCode(),
        'message' => $e->getMessage()
    ]);
}

// Ensure clean exit
exit;
