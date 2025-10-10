<?php
// api/check-first-visit.php
// Returns { first_visit: boolean }

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
        echo json_encode(['error' => 'Invalid CSRF token']);
        exit;
    }

    if (!$auth->isLoggedIn()) {
        // Guests handled with localStorage; do not show from API
        echo json_encode(['first_visit' => false]);
        exit;
    }

    $isAdmin = $auth->isAdmin();
    $current = $auth->getCurrentUser();

    if ($isAdmin) {
        // Admin user
        $stmt = $db->prepare('SELECT first_visit, welcome_dont_show FROM admin_users WHERE admin_id = ? LIMIT 1');
        $stmt->execute([$current['admin_id'] ?? $current['id'] ?? null]);
    } else {
        // Normal user
        $stmt = $db->prepare('SELECT first_visit, welcome_dont_show FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$current['id'] ?? null]);
    }

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $first = isset($row['first_visit']) ? (bool)$row['first_visit'] : false;
    $dontShow = isset($row['welcome_dont_show']) ? (bool)$row['welcome_dont_show'] : false;

    // Show modal only if it's first visit AND user hasn't opted out
    $shouldShow = $first && !$dontShow;

    echo json_encode(['first_visit' => $shouldShow]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error', 'details' => $e->getMessage()]);
}
