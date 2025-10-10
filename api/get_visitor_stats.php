<?php
/**
 * Get Visitor Statistics API
 * 
 * Returns visitor statistics in JSON format for the admin dashboard.
 * 
 * @package CodeGaming
 * @subpackage API
 * @version 1.0.0
 * @author CodeGaming Team
 */

// Set content type to JSON
header('Content-Type: application/json');

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include required files
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/VisitorTracker.php';

// Initialize database connection
$db = Database::getInstance();

// Start or resume session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Debug: Log session data
error_log('Session data: ' . print_r($_SESSION, true));

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'error' => 'Unauthorized access',
        'session' => $_SESSION,
        'is_logged_in' => isset($_SESSION['user_id']),
        'is_admin' => ($_SESSION['user_role'] ?? null) === 'admin'
    ]);
    exit;
}

try {
    // Get days parameter (default: 30 days)
    $days = isset($_GET['days']) ? (int)$_GET['days'] : 30;
    $days = max(1, min(365, $days)); // Limit between 1 and 365 days
    
    // Get visitor statistics
    $tracker = new VisitorTracker();
    $stats = $tracker->getStats($days);
    
    // Return the statistics
    echo json_encode([
        'success' => true,
        'data' => $stats
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch visitor statistics',
        'message' => $e->getMessage()
    ]);
}
?>
