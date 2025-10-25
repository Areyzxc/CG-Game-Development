<?php
/**
 * Test endpoint to debug leaderboard issues
 */

// Enable error reporting for debugging
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Set headers first to prevent any output issues
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

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

// Start output buffering
ob_start();

try {
    // Include required files
    require_once '../../includes/Database.php';
    
    // Test database connection first
    try {
        $db = Database::getInstance();
        $pdo = $db->getConnection();
        
        // Test the connection
        $pdo->query("SELECT 1");
    } catch (Exception $e) {
        error_log('Database connection failed: ' . $e->getMessage());
        throw new Exception('Failed to connect to database: ' . $e->getMessage());
    }
    
    // Test 1: Check if mini_game_results table exists
    try {
        $stmt = $pdo->prepare("SHOW TABLES LIKE 'mini_game_results'");
        $stmt->execute();
        $tableExists = $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        error_log('Error checking for mini_game_results table: ' . $e->getMessage());
        throw new Exception('Error checking for mini_game_results table. Make sure it exists and you have proper permissions.');
    }
    
    $result = [
        'table_exists' => $tableExists,
        'total_records' => 0,
        'sample_records' => [],
        'join_test' => []
    ];
    
    if ($tableExists) {
        // Test 2: Count total records
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM mini_game_results");
        $stmt->execute();
        $result['total_records'] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Test 3: Get sample records (only if there are records)
        if ($result['total_records'] > 0) {
            $stmt = $pdo->prepare("SELECT * FROM mini_game_results ORDER BY played_at DESC LIMIT 5");
            $stmt->execute();
            $result['sample_records'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Test 4: Check users table join (only if there are records)
            $stmt = $pdo->prepare("
                SELECT m.*, u.username 
                FROM mini_game_results m 
                LEFT JOIN users u ON m.user_id = u.id 
                ORDER BY m.played_at DESC 
                LIMIT 3
            ");
            $stmt->execute();
            $result['join_test'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    
    sendJsonResponse([
        'success' => true,
        'tests' => $result
    ]);
    
} catch (PDOException $e) {
    error_log('Database error in test-leaderboard.php: ' . $e->getMessage());
    sendError('Database error occurred', 500, [
        'code' => $e->getCode(),
        'message' => $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log('Error in test-leaderboard.php: ' . $e->getMessage());
    sendError('An error occurred while testing the leaderboard', 500, [
        'code' => $e->getCode(),
        'message' => $e->getMessage()
    ]);
}
?>
