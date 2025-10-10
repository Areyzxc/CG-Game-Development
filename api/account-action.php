<?php
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/Auth.php';
require_once __DIR__ . '/../includes/CSRFProtection.php';

header('Content-Type: application/json');

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

$action = $_POST['action'] ?? '';
$password = $_POST['password'] ?? '';
$user = $auth->getCurrentUser();
$userId = $user['id'];

try {
    $conn = $db->getConnection();
    // Re-auth check for sensitive actions
    $stmt = $conn->prepare('SELECT password_hash FROM users WHERE id = ?');
    $stmt->execute([$userId]);
    $row = $stmt->fetch();
    if (!$row || !password_verify($password, $row['password_hash'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Password verification failed']);
        exit;
    }

    if ($action === 'deactivate') {
        $conn->prepare('UPDATE users SET is_active = 0 WHERE id = ?')->execute([$userId]);
        echo json_encode(['success' => true, 'message' => 'Account deactivated']);
    } elseif ($action === 'delete') {
        // Soft delete: mark deleted_at and deactivate
        $conn->prepare('UPDATE users SET is_active = 0, deleted_at = NOW() WHERE id = ?')->execute([$userId]);
        echo json_encode(['success' => true, 'message' => 'Account scheduled for deletion']);
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Unknown action']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to process request']);
}
?>


