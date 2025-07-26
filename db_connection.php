<?php
/**
 * ==========================================================
 * File: db_connection.php
 * 
 * Description:
 *   - Database connection and utility functions for Code Gaming platform
 *   - Features:
 *       • Connects to MySQL database using mysqli
 *       • Sets charset to utf8mb4 for full Unicode support
 *       • Provides input sanitization and error logging helpers
 *       • Session management and cookie configuration
 *       • Helper functions for user authentication and role checks
 *       • Error reporting enabled for development
 * 
 * 
 * Usage:
 *   - Included in backend scripts requiring database access
 *   - Used for session and user role management
 * 
 * 
 * Author: [Santiago]
 * Last Updated: [July 22, 2025]
 * -- Code Gaming Team --
 * ==========================================================
 */

// Database configuration
define('DB_HOST', 'localhost:3327');
define('DB_USER', 'root');  // Change in production
define('DB_PASS', '');      // Change in production
define('DB_NAME', 'coding_game');

// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    die(json_encode([
        'success' => false,
        'error' => 'Database connection failed. Please try again later.'
    ]));
}

// Set charset to utf8mb4
if (!$conn->set_charset("utf8mb4")) {
    error_log("Error setting charset: " . $conn->error);
}

// Helper function to sanitize input
function sanitize_input($data) {
    global $conn;
    return $conn->real_escape_string(trim($data));
}

// Helper function to log errors
function log_error($message, $context = []) {
    $log_message = date('Y-m-d H:i:s') . " - " . $message;
    if (!empty($context)) {
        $log_message .= " - Context: " . json_encode($context);
    }
    error_log($log_message);
}

// Helper function to check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Helper function to check if user is admin
function is_admin() {
    return isset($_SESSION['role']) && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'super_admin');
}

// Helper function to get current user ID
function get_current_user_id() {
    return $_SESSION['user_id'] ?? null;
}

// Helper function to get current user role
function get_current_user_role() {
    return $_SESSION['role'] ?? null;
}

// Set session cookie parameters
session_set_cookie_params([
    'lifetime' => 86400, // 24 hours
    'path' => '/',
    'secure' => isset($_SERVER['HTTPS']), // Use secure cookies in HTTPS
    'httponly' => true,
    'samesite' => 'Lax'
]);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
