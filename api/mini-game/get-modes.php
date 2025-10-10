<?php
/**
 * API Endpoint: Get Game Modes
 * Purpose: Fetch available mini-game modes from database
 * Method: GET
 * Returns: JSON array of game modes
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../../includes/Database.php';

try {
    $db = Database::getInstance();
    
    // Fetch active game modes
    $stmt = $db->prepare("
        SELECT 
            mode_key,
            name,
            description,
            instructions,
            icon,
            difficulty_levels,
            supported_languages,
            created_at
        FROM mini_game_modes 
        WHERE is_active = 1 
        ORDER BY id ASC
    ");
    
    $stmt->execute();
    $modes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Parse JSON fields
    foreach ($modes as &$mode) {
        $mode['instructions'] = json_decode($mode['instructions'], true);
        $mode['difficulty_levels'] = json_decode($mode['difficulty_levels'], true);
        $mode['supported_languages'] = json_decode($mode['supported_languages'], true);
    }
    
    echo json_encode([
        'success' => true,
        'modes' => $modes,
        'count' => count($modes)
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch game modes',
        'message' => $e->getMessage()
    ]);
}
?>
