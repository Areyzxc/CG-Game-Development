<?php
// api/track-welcome-preferences.php
// Tracks user interactions with the welcome modal for personalization

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

    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid JSON input']);
        exit;
    }

    $action = $input['action'] ?? '';
    $section = $input['section'] ?? '';
    $dontShowAgain = isset($input['dont_show_again']) ? (bool)$input['dont_show_again'] : false;

    // For guests, we'll just return success (they use localStorage)
    if (!$auth->isLoggedIn()) {
        echo json_encode(['success' => true, 'message' => 'Guest preferences handled client-side']);
        exit;
    }

    $currentUser = $auth->getCurrentUser();
    $userId = $currentUser['id'] ?? null;
    $isAdmin = $auth->isAdmin();

    if (!$userId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid user']);
        exit;
    }

    // Handle different actions
    switch ($action) {
        case 'track_section_click':
            // Track which section the user clicked on
            if ($section) {
                $stmt = $db->prepare('
                    INSERT INTO user_welcome_tracking (user_id, section_clicked, clicked_at, is_admin) 
                    VALUES (?, ?, NOW(), ?)
                    ON DUPLICATE KEY UPDATE 
                    click_count = click_count + 1, 
                    last_clicked_at = NOW()
                ');
                $stmt->execute([$userId, $section, $isAdmin ? 1 : 0]);
            }
            break;

        case 'set_dont_show_again':
            // Update the user's preference to not show the modal again
            if ($isAdmin) {
                $stmt = $db->prepare('UPDATE admin_users SET welcome_dont_show = 1 WHERE admin_id = ?');
                $stmt->execute([$userId]);
            } else {
                $stmt = $db->prepare('UPDATE users SET welcome_dont_show = 1 WHERE id = ?');
                $stmt->execute([$userId]);
            }
            break;

        case 'modal_completed':
            // Track that the user completed viewing the modal
            $stmt = $db->prepare('
                INSERT INTO user_welcome_tracking (user_id, section_clicked, clicked_at, is_admin, action_type) 
                VALUES (?, "modal_completed", NOW(), ?, "completed")
            ');
            $stmt->execute([$userId, $isAdmin ? 1 : 0]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid action']);
            exit;
    }

    echo json_encode(['success' => true, 'message' => 'Preference tracked successfully']);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error', 'details' => $e->getMessage()]);
}
