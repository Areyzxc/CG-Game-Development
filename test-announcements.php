<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the database connection
require_once __DIR__ . '/includes/Database.php';

// Create a logs directory if it doesn't exist
if (!is_dir(__DIR__ . '/logs')) {
    mkdir(__DIR__ . '/logs', 0755, true);
}

// Set error log file
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/test_errors.log');

function logMessage($message) {
    $logFile = __DIR__ . '/logs/test_log.log';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message" . PHP_EOL, FILE_APPEND);
}

logMessage("Starting test script");

try {
    // Test database connection
    logMessage("Attempting to get database connection...");
    $db = Database::getInstance()->getConnection();
    logMessage("Database connection successful");
    
    // Test query
    $query = "SHOW TABLES";
    $tables = $db->query($query)->fetchAll(PDO::FETCH_COLUMN);
    logMessage("Available tables: " . implode(', ', $tables));
    
    // Check if announcements table exists
    if (!in_array('announcements', $tables)) {
        throw new Exception("Announcements table does not exist");
    }
    
    // Test announcements query
    $query = "
        SELECT a.id, a.title, a.content, a.category as type, a.created_at, 
               COALESCE(au.username, 'Admin') as author,
               a.is_active,
               a.is_pinned
        FROM announcements a 
        LEFT JOIN admin_users au ON a.created_by = au.admin_id 
        WHERE a.is_active = 1 
        AND a.status = 'published'
        ORDER BY a.is_pinned DESC, a.created_at DESC 
        LIMIT 3";
    
    logMessage("Executing query: " . preg_replace('/\s+/', ' ', trim($query)));
    
    $stmt = $db->query($query);
    $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    logMessage("Found " . count($announcements) . " announcements");
    
    // Output results
    echo "<h2>Test Results</h2>";
    echo "<pre>" . print_r($announcements, true) . "</pre>";
    
} catch (Exception $e) {
    $error = "Error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine();
    logMessage($error);
    echo "<div style='color:red;'><h2>Error</h2><pre>$error</pre></div>";
}

// Show log file
$logFile = __DIR__ . '/logs/test_log.log';
if (file_exists($logFile)) {
    echo "<h3>Log File Contents:</h3>";
    echo "<pre>" . htmlspecialchars(file_get_contents($logFile)) . "</pre>";
}
?>
