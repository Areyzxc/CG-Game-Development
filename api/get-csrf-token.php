<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../includes/CSRFProtection.php';

header('Content-Type: application/json');

// Get CSRF instance and generate a new token
$csrf = CSRFProtection::getInstance();
$token = $csrf->getToken();

// Output the token in a JSON response
echo json_encode([
    'success' => true,
    'token' => $token,
    'session_id' => session_id(),
    'timestamp' => time()
]);
