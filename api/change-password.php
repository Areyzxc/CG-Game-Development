<?php
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/Auth.php';
require_once __DIR__ . '/../includes/CSRFProtection.php';

header('Content-Type: application/json');

// Simple per-user rate limit: max 5 password changes per hour
session_start();
$bucketKey = 'rate_change_pwd';
if (!isset($_SESSION[$bucketKey])) { $_SESSION[$bucketKey] = []; }
$_SESSION[$bucketKey] = array_values(array_filter($_SESSION[$bucketKey], function($t){ return $t > time()-3600; }));
if (count($_SESSION[$bucketKey]) >= 5) {
    http_response_code(429);
    echo json_encode(['success' => false, 'error' => 'Too many attempts. Try again later.']);
    exit;
}
$_SESSION[$bucketKey][] = time();

$db = Database::getInstance();
$auth = Auth::getInstance();
$csrf = CSRFProtection::getInstance();

if (!$auth->isLoggedIn() || $auth->isAdmin()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if (!$csrf->validateRequest()) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
    exit;
}

$user = $auth->getCurrentUser();
$userId = $user['id'];

$current = $_POST['current_password'] ?? '';
$new = $_POST['new_password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

if (strlen($new) < 8) {
    echo json_encode(['success' => false, 'error' => 'Password must be at least 8 characters']);
    exit;
}
if ($new !== $confirm) {
    echo json_encode(['success' => false, 'error' => 'Passwords do not match']);
    exit;
}

// Fetch current hash
$conn = $db->getConnection();
$stmt = $conn->prepare('SELECT password_hash FROM users WHERE id = ?');
$stmt->execute([$userId]);
$row = $stmt->fetch();
if (!$row || !password_verify($current, $row['password_hash'])) {
    echo json_encode(['success' => false, 'error' => 'Current password is incorrect']);
    exit;
}

$newHash = password_hash($new, PASSWORD_DEFAULT);
$stmt = $conn->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
if ($stmt->execute([$newHash, $userId])) {
    echo json_encode(['success' => true, 'message' => 'Password updated successfully']);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to update password']);
}

?>


