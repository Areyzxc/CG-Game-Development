<?php
/**
 * File: api/award-achievement.php
 * Purpose: API endpoint for awarding achievements to users in CodeGaming.
 * Features:
 *   - Awards achievements based on challenge performance
 *   - Prevents duplicate achievement awards
 *   - Returns achievement details for frontend display
 * Usage:
 *   - Called via AJAX from challenge.js when achievements are earned
 *   - Requires Database.php and Auth.php for user validation
 * Included Files/Dependencies:
 *   - includes/Database.php
 *   - includes/Auth.php
 * Author: CodeGaming Team
 * Last Updated: July 24, 2025
 */

header('Content-Type: application/json');
require_once '../includes/Database.php';
require_once '../includes/Auth.php';
require_once '../includes/CSRFProtection.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Invalid input data');
    }
    
    // Validate CSRF token
    $csrf = CSRFProtection::getInstance();
    $csrfToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!$csrf->validateToken($csrfToken)) {
        throw new Exception('Invalid CSRF token');
    }
    
    $auth = Auth::getInstance();
    if (!$auth->isLoggedIn()) {
        throw new Exception('User must be logged in to earn achievements');
    }
    
    $user = $auth->getCurrentUser();
    $userId = $user['id'];
    
    $achievementId = isset($input['achievement_id']) ? trim($input['achievement_id']) : '';
    $challengeScore = isset($input['challenge_score']) && is_numeric($input['challenge_score']) ? (int)$input['challenge_score'] : 0;
    $questionsCorrect = isset($input['questions_correct']) && is_numeric($input['questions_correct']) ? (int)$input['questions_correct'] : 0;
    $totalQuestions = isset($input['total_questions']) && is_numeric($input['total_questions']) ? (int)$input['total_questions'] : 20;
    
    if (empty($achievementId)) {
        throw new Exception('Achievement ID is required');
    }
    
    $db = Database::getInstance();
    
    // Check if user already has this achievement
    $stmt = $db->prepare("SELECT id FROM user_achievements WHERE user_id = ? AND achievement_id = ?");
    $stmt->execute([$userId, $achievementId]);
    
    if ($stmt->fetch()) {
        echo json_encode([
            'success' => true,
            'already_earned' => true,
            'message' => 'Achievement already earned'
        ]);
        exit;
    }
    
    // Award the achievement
    $stmt = $db->prepare("INSERT INTO user_achievements (user_id, achievement_id) VALUES (?, ?)");
    $stmt->execute([$userId, $achievementId]);
    
    // Get achievement details
    $achievementDetails = getAchievementDetails($achievementId, $challengeScore, $questionsCorrect, $totalQuestions);
    
    echo json_encode([
        'success' => true,
        'achievement' => $achievementDetails,
        'message' => 'Achievement earned!'
    ]);
    
} catch (Exception $e) {
    // Log the error for debugging
    error_log("Award achievement error: " . $e->getMessage());
    
    // Return user-friendly error message
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'debug' => getenv('ENVIRONMENT') !== 'production' ? $e->getMessage() : null
    ]);
}

function getAchievementDetails($achievementId, $score, $correct, $total) {
    $achievements = [
        'challenge_master' => [
            'id' => 'challenge_master',
            'name' => 'Challenge Master',
            'description' => 'Achieved a perfect score in the Expert Challenge',
            'icon' => '🏆',
            'color' => '#FFD700',
            'condition' => $score >= ($total * 30)
        ],
        'speed_demon' => [
            'id' => 'speed_demon',
            'name' => 'Speed Demon',
            'description' => 'Completed the challenge in under 1 minute',
            'icon' => '⚡',
            'color' => '#FF6B6B',
            'condition' => false // This would need time data
        ],
        'persistent_coder' => [
            'id' => 'persistent_coder',
            'name' => 'Persistent Coder',
            'description' => 'Completed 10 challenge attempts',
            'icon' => '💪',
            'color' => '#4ECDC4',
            'condition' => false // This would need attempt count
        ],
        'expert_level' => [
            'id' => 'expert_level',
            'name' => 'Expert Level',
            'description' => 'Scored 80% or higher in the Expert Challenge',
            'icon' => '🎯',
            'color' => '#45B7D1',
            'condition' => ($correct / $total) >= 0.8
        ],
        'first_challenge' => [
            'id' => 'first_challenge',
            'name' => 'First Challenge',
            'description' => 'Completed your first Expert Challenge',
            'icon' => '🌟',
            'color' => '#96CEB4',
            'condition' => true // Always award on first completion
        ],
        'near_perfect' => [
            'id' => 'near_perfect',
            'name' => 'Near Perfect',
            'description' => 'Scored 90% or higher in the Expert Challenge',
            'icon' => '✨',
            'color' => '#FECA57',
            'condition' => ($correct / $total) >= 0.9
        ]
    ];
    
    return $achievements[$achievementId] ?? [
        'id' => $achievementId,
        'name' => 'Unknown Achievement',
        'description' => 'An achievement was earned',
        'icon' => '🏅',
        'color' => '#95A5A6'
    ];
}
?>