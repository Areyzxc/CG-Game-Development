<?php
// api/update-first-visit.php
// Sets first_visit = 0 for the current user/admin

header('Content-Type: application/json');

require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/Auth.php';
require_once __DIR__ . '/../includes/CSRFProtection.php';

try {
    $auth = Auth::getInstance();
    $db = Database::getInstance()->getConnection();
    $csrf = CSRFProtection::getInstance();

    // Validate CSRF header
    $csrfHeader = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!$csrf->validateToken($csrfHeader)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
        exit;
    }

    if (!$auth->isLoggedIn()) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Not authenticated']);
        exit;
    }

    $isAdmin = $auth->isAdmin();
    $current = $auth->getCurrentUser();

    if ($isAdmin) {
        $stmt = $db->prepare('UPDATE admin_users SET first_visit = 0 WHERE admin_id = ?');
        $stmt->execute([$current['admin_id'] ?? $current['id'] ?? null]);
    } else {
        $stmt = $db->prepare('UPDATE users SET first_visit = 0 WHERE id = ?');
        $stmt->execute([$current['id'] ?? null]);
    }

    echo json_encode(['success' => true]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error', 'details' => $e->getMessage()]);
}
