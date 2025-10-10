<?php
/**
 * Test endpoint to debug leaderboard issues
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../../includes/Database.php';

try {
    $db = Database::getInstance();
    
    // Test 1: Check if mini_game_results table exists
    $stmt = $db->query("SHOW TABLES LIKE 'mini_game_results'");
    $tableExists = $stmt->rowCount() > 0;
    
    // Test 2: Count total records
    $stmt = $db->query("SELECT COUNT(*) as total FROM mini_game_results");
    $totalRecords = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Test 3: Get sample records
    $stmt = $db->query("SELECT * FROM mini_game_results LIMIT 5");
    $sampleRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Test 4: Check users table join
    $stmt = $db->query("
        SELECT m.*, u.username 
        FROM mini_game_results m 
        LEFT JOIN users u ON m.user_id = u.id 
        LIMIT 3
    ");
    $joinTest = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'tests' => [
            'table_exists' => $tableExists,
            'total_records' => $totalRecords,
            'sample_records' => $sampleRecords,
            'join_test' => $joinTest
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}
?>
