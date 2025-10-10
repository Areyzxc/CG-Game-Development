<?php
/**
 * API Endpoint: Save User Preferences
 * Purpose: Save user preferences for mini-game (like welcome modal settings)
 * Method: POST
 * Returns: JSON response
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../../includes/Database.php';
require_once '../../includes/Auth.php';

try {
    // Check if user is logged in
    $auth = Auth::getInstance();
    if (!$auth->isLoggedIn()) {
        echo json_encode([
            'success' => true,
            'message' => 'Preferences saved locally (guest user)'
        ]);
        exit;
    }
    
    $user = $auth->getCurrentUser();
    $userId = $user['id'];
    
    // Get POST data
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Invalid input data');
    }
    
    $db = Database::getInstance();
    
    // Check if preferences already exist
    $stmt = $db->prepare("SELECT id FROM user_mini_game_preferences WHERE user_id = ?");
    $stmt->execute([$userId]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        // Update existing preferences
        $stmt = $db->prepare("
            UPDATE user_mini_game_preferences 
            SET 
                show_welcome_modal = ?,
                preferred_language = ?,
                preferred_difficulty = ?,
                updated_at = CURRENT_TIMESTAMP
            WHERE user_id = ?
        ");
        
        $stmt->execute([
            $input['show_welcome_modal'] ?? true,
            $input['preferred_language'] ?? 'javascript',
            $input['preferred_difficulty'] ?? 'beginner',
            $userId
        ]);
    } else {
        // Insert new preferences
        $stmt = $db->prepare("
            INSERT INTO user_mini_game_preferences 
            (user_id, show_welcome_modal, preferred_language, preferred_difficulty) 
            VALUES (?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $userId,
            $input['show_welcome_modal'] ?? true,
            $input['preferred_language'] ?? 'javascript',
            $input['preferred_difficulty'] ?? 'beginner'
        ]);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Preferences saved successfully'
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to save preferences',
        'message' => $e->getMessage()
    ]);
}
?>
