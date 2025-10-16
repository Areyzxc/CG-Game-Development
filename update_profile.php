<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include required files
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/Database.php';
require_once __DIR__ . '/includes/CSRFProtection.php';
require_once __DIR__ . '/includes/Auth.php';

// Initialize Auth using singleton pattern
$auth = Auth::getInstance();
if (!$auth->isLoggedIn()) {
    header('Content-Type: application/json');
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

// Get current user
$currentUser = $auth->getCurrentUser();

// Set content type
header('Content-Type: application/json');

// Debug: Log all POST data
error_log('POST data: ' . print_r($_POST, true));

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method. Please use POST.'
    ]);
    exit;
}

// Initialize CSRF
$csrf = CSRFProtection::getInstance();

try {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !$csrf->validateToken($_POST['csrf_token'])) {
        error_log('CSRF token validation failed');
        error_log('Posted token: ' . ($_POST['csrf_token'] ?? 'not set'));
        error_log('Session token: ' . ($_SESSION['csrf_token'] ?? 'not set'));
        
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid CSRF token. Please refresh the page and try again.'
        ]);
        exit;
    }
    
    // Sanitize inputs
    $social_instagram = isset($_POST['social_instagram']) ? trim($_POST['social_instagram']) : '';
    $social_facebook = isset($_POST['social_facebook']) ? trim($_POST['social_facebook']) : '';
    $social_twitter = isset($_POST['social_twitter']) ? trim($_POST['social_twitter']) : '';
    $social_pinterest = isset($_POST['social_pinterest']) ? trim($_POST['social_pinterest']) : '';

    // Get database connection
    $db = Database::getInstance()->getConnection();
    
    // Update database
    $stmt = $db->prepare('
        UPDATE users 
        SET social_instagram = ?,
            social_facebook = ?,
            social_twitter = ?,
            social_pinterest = ?,
            updated_at = NOW()
        WHERE id = ?
    ');
    
    $success = $stmt->execute([
        $social_instagram,
        $social_facebook,
        $social_twitter,
        $social_pinterest,
        $currentUser['id']
    ]);
    
    if ($success) {
        // Update session
        $_SESSION['user']['social_instagram'] = $social_instagram;
        $_SESSION['user']['social_facebook'] = $social_facebook;
        $_SESSION['user']['social_twitter'] = $social_twitter;
        $_SESSION['user']['social_pinterest'] = $social_pinterest;
        
        echo json_encode([
            'success' => true,
            'message' => 'Social media links updated successfully!'
        ]);
    } else {
        $errorInfo = $stmt->errorInfo();
        error_log("Database error: " . print_r($errorInfo, true));
        throw new Exception('Failed to update social media links. Please try again.');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    error_log("Error in update_profile.php: " . $e->getMessage());
}

exit;