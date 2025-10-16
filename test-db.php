<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// Test database connection
try {
    require_once 'includes/Database.php';
    
    $db = Database::getInstance()->getConnection();
    
    // Test connection
    $db->query('SELECT 1');
    
    // Get database info
    $database = $db->query('SELECT DATABASE() as dbname')->fetch(PDO::FETCH_ASSOC)['dbname'];
    $tables = $db->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    
    // Check if announcements table exists
    $announcementsTable = in_array('announcements', $tables);
    $announcementsData = [];
    
    if ($announcementsTable) {
        // Get table structure
        $structure = $db->query('DESCRIBE announcements')->fetchAll(PDO::FETCH_ASSOC);
        
        // Get some sample data
        $announcementsData = [
            'structure' => $structure,
            'count' => $db->query('SELECT COUNT(*) as count FROM announcements')->fetch(PDO::FETCH_ASSOC)['count'],
            'sample' => $db->query('SELECT * FROM announcements ORDER BY created_at DESC LIMIT 3')->fetchAll(PDO::FETCH_ASSOC)
        ];
    }
    
    // Check admin_users table
    $adminUsersTable = in_array('admin_users', $tables);
    $adminUsersData = [];
    
    if ($adminUsersTable) {
        $adminUsersData = [
            'count' => $db->query('SELECT COUNT(*) as count FROM admin_users')->fetch(PDO::FETCH_ASSOC)['count']
        ];
    }
    
    // Return results
    echo json_encode([
        'success' => true,
        'database' => $database,
        'tables' => $tables,
        'announcements' => [
            'exists' => $announcementsTable,
            'data' => $announcementsData
        ],
        'admin_users' => [
            'exists' => $adminUsersTable,
            'data' => $adminUsersData
        ]
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ], JSON_PRETTY_PRINT);
}
