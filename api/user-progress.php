<?php
/**
 * File: api/user-progress.php
 * Purpose: API endpoint for retrieving user progress, achievements, and personalized data for CodeGaming.
 * Features:
 *   - Fetches user progress across all game modes (quiz, challenge, mini-game, tutorials)
 *   - Returns user achievements and statistics
 *   - Provides personalized data for home page display
 *   - Handles both logged-in users and guest sessions
 * Usage:
 *   - Called via AJAX from home page and profile pages
 *   - Requires Database.php for DB access
 * Included Files/Dependencies:
 *   - includes/Database.php
 *   - includes/Auth.php
 * Author: CodeGaming Team
 * Last Updated: July 26, 2025
 */

require_once '../includes/Database.php';
require_once '../includes/Auth.php';
require_once '../includes/UserProgressManager.php';
header('Content-Type: application/json');

try {
    $auth = Auth::getInstance();
    $progressManager = new UserProgressManager();
    
    $userId = null;
    $guestSessionId = null;
    $username = null;
    $nickname = null;
    
    // Determine if user is logged in or guest
    if ($auth->isLoggedIn()) {
        $user = $auth->getCurrentUser();
        $userId = $user['id'];
        $username = $user['username'];
    } else {
        // Check for guest session
        $guestSessionId = isset($_GET['guest_session_id']) ? intval($_GET['guest_session_id']) : null;
        $nickname = isset($_GET['nickname']) ? $_GET['nickname'] : null;
    }
    
    $response = [
        'success' => true,
        'user_stats' => [],
        'achievements' => [],
        'progress' => [],
        'personalization' => []
    ];
    
    if ($userId) {
        // Get user progress across all modes using the utility class
        $response['user_stats'] = $progressManager->getUserStats($userId);
        $response['achievements'] = $progressManager->getUserAchievements($userId);
        $response['progress'] = $progressManager->getUserProgress($userId);
        $response['personalization'] = $progressManager->getPersonalizationData($userId, $username);
    } else if ($guestSessionId) {
        // Get guest progress using the utility class
        $response['user_stats'] = $progressManager->getGuestStats($guestSessionId);
        $response['achievements'] = [];
        $response['progress'] = [];
        $response['personalization'] = $progressManager->getGuestPersonalizationData($nickname);
    }
    
    echo json_encode($response);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch user progress: ' . $e->getMessage()
    ]);
}
?>
