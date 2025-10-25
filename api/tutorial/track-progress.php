<?php
// Enable all error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/tutorial_errors.log');

// Set JSON content type and CORS headers
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-Requested-With, X-CSRF-Token");

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Initialize response array
$response = [
    'success' => false,
    'message' => 'An error occurred',
    'data' => []
];

try {
    // Set the base directory
    $baseDir = dirname(dirname(dirname(__FILE__)));
    
    // Include required files using absolute paths
    require_once $baseDir . '/includes/Database.php';
    require_once $baseDir . '/includes/Auth.php';
    require_once $baseDir . '/includes/CSRFProtection.php';
    // Verify CSRF token for non-GET requests
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $csrf = CSRFProtection::getInstance();
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        
        if (!$token || !$csrf->validateToken($token)) {
            throw new Exception('Invalid or expired CSRF token');
        }
    }

    // Get database connection
    $db = Database::getInstance();
    $auth = Auth::getInstance();
    $user = $auth->isLoggedIn() ? $auth->getCurrentUser() : null;
    $userId = $user ? $user['id'] : null;
    
    // For guest users, we'll use session storage
    if (!$userId) {
        if (!session_id()) {
            session_start();
        }
        
        // Initialize guest progress in session if it doesn't exist
        if (!isset($_SESSION['guest_tutorial_progress'])) {
            $_SESSION['guest_tutorial_progress'] = [];
        }
    }
    
    // Handle different request methods
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'POST':
            // Get JSON input
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON input');
            }
            
            // Validate required fields
            if (empty($input['topic_id']) || !isset($input['status'])) {
                throw new Exception('Missing required fields');
            }
            
            $topicId = $input['topic_id'];
            // In the POST handler, update the status mapping:
$statusMap = [
    'not_started' => 'pending',
    'in_progress' => 'currently_reading',
    'completed' => 'done_reading',
    'done_reading' => 'done_reading' // Add this line to handle direct 'done_reading' status
];
$status = $statusMap[$input['status']] ?? 'pending';

// Add debug logging
error_log("Received status update - User ID: $userId, Topic ID: $topicId, Status: " . $input['status'] . ", Mapped Status: $status");

// For logged-in users, save to database
if ($userId) {
    try {
        // Check if progress already exists
        $stmt = $db->prepare(
            "SELECT id, status FROM user_progress 
            WHERE user_id = ? AND topic_id = ?"
        );
        $stmt->execute([$userId, $topicId]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existing) {
            // Update existing progress
            $stmt = $db->prepare(
                "UPDATE user_progress 
                SET status = ?, 
                    last_accessed = NOW(),
                    completed_at = CASE WHEN ? = 'done_reading' THEN NOW() ELSE completed_at END
                WHERE user_id = ? AND topic_id = ?"
            );
            $result = $stmt->execute([$status, $status, $userId, $topicId]);
            error_log("Update query executed. Rows affected: " . $stmt->rowCount());
        } else {
            // Insert new progress
            $stmt = $db->prepare(
                "INSERT INTO user_progress 
                (user_id, topic_id, status, last_accessed, completed_at) 
                VALUES (?, ?, ?, NOW(), ?)"
            );
            $completedAt = ($status === 'done_reading') ? date('Y-m-d H:i:s') : null;
            $result = $stmt->execute([$userId, $topicId, $status, $completedAt]);
            error_log("Insert query executed. Rows affected: " . $stmt->rowCount());
        }
        
        if ($result) {
            $response['success'] = true;
            $response['message'] = 'Progress saved successfully';
            $response['data'] = [
                'topic_id' => $topicId,
                'status' => $status
            ];
        } else {
            throw new Exception('Failed to save progress');
        }
    } catch (Exception $e) {
        error_log("Database error: " . $e->getMessage());
        throw new Exception('Database error: ' . $e->getMessage());
    }
}
            
        case 'GET':
            // Get progress for all topics or a specific topic
            $topicId = $_GET['topic_id'] ?? null;
            
            if ($userId) {
                // For logged-in users, get from database
                if ($topicId) {
                    $stmt = $db->prepare(
                        "SELECT status FROM user_progress 
                        WHERE user_id = ? AND topic_id = ?"
                    );
                    $stmt->execute([$userId, $topicId]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    $response['success'] = true;
                    $response['data'] = $result ?: ['status' => 'not_started'];
                } else {
                    // Get all progress for the user
                    $stmt = $db->prepare(
                        "SELECT topic_id, status FROM user_progress 
                        WHERE user_id = ?"
                    );
                    $stmt->execute([$userId]);
                    $progress = [];
                    
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $progress[$row['topic_id']] = $row['status'];
                    }
                    
                    $response['success'] = true;
                    $response['data'] = $progress;
                }
            } else {
                // For guests, get from session
                if (!session_id()) {
                    session_start();
                }
                
                if ($topicId) {
                    $status = $_SESSION['guest_tutorial_progress'][$topicId]['status'] ?? 'not_started';
                    $response['success'] = true;
                    $response['data'] = ['status' => $status];
                } else {
                    $response['success'] = true;
                    $response['data'] = $_SESSION['guest_tutorial_progress'] ?? [];
                }
            }
            break;
            
        default:
            throw new Exception('Method not allowed');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    $response = [
        'success' => false,
        'message' => 'An error occurred while processing your request.',
        'error' => [
            'code' => $e->getCode(),
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]
    ];
    
    // Log detailed error
    $logMessage = sprintf(
        "[%s] Error %s: %s in %s on line %d\nStack Trace:\n%s\n\n",
        date('Y-m-d H:i:s'),
        $e->getCode(),
        $e->getMessage(),
        $e->getFile(),
        $e->getLine(),
        $e->getTraceAsString()
    );
    error_log($logMessage, 3, __DIR__ . '/tutorial_errors.log');
}

// Ensure we're outputting valid JSON
if (!headers_sent()) {
    header_remove('X-Powered-By');
    header_remove('Set-Cookie');
    header_remove('Expires');
    header_remove('Cache-Control');
    header('Content-Type: application/json');
}

try {
    $jsonResponse = json_encode($response, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    if ($jsonResponse === false) {
        throw new Exception('Failed to encode JSON: ' . json_last_error_msg());
    }
    echo $jsonResponse;
} catch (Exception $e) {
    // If JSON encoding fails, send a simple error response
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to encode response',
        'error' => [
            'code' => $e->getCode(),
            'message' => $e->getMessage()
        ]
    ]);
}
