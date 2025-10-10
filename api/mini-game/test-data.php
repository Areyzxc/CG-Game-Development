<?php
/**
 * Test endpoint to verify mini-game data and database connection
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../../includes/Database.php';

try {
    $db = Database::getInstance();
    
    // Test 1: Check if tables exist
    $tables = [];
    $stmt = $db->query("SHOW TABLES LIKE 'mini_game%'");
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        $tables[] = $row[0];
    }
    
    // Test 2: Check mini_game_modes data
    $modes = [];
    if (in_array('mini_game_modes', $tables)) {
        $stmt = $db->query("SELECT * FROM mini_game_modes");
        $modes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Test 3: Check mini_game_results data
    $results = [];
    if (in_array('mini_game_results', $tables)) {
        $stmt = $db->query("SELECT * FROM mini_game_results LIMIT 5");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Test 4: Test leaderboard query
    $leaderboard = [];
    if (in_array('mini_game_results', $tables)) {
        $stmt = $db->query("
            SELECT 
                m.user_id,
                u.username,
                m.game_type,
                m.score,
                m.played_at
            FROM mini_game_results m
            LEFT JOIN users u ON m.user_id = u.user_id
            ORDER BY m.score DESC
            LIMIT 5
        ");
        $leaderboard = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    echo json_encode([
        'success' => true,
        'database_connection' => 'OK',
        'tables_found' => $tables,
        'modes_count' => count($modes),
        'modes_data' => $modes,
        'results_count' => count($results),
        'results_sample' => $results,
        'leaderboard_sample' => $leaderboard,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
?>
