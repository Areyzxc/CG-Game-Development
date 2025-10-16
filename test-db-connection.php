<?php
// test_connection.php
require_once __DIR__ . '/includes/Database.php';

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    echo "Database connection successful!";
    
    // Test query
    $stmt = $conn->query("SELECT DATABASE() as dbname");
    $result = $stmt->fetch();
    echo "<br>Connected to database: " . $result['dbname'];
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    echo "<br>Check your database configuration in config/database.php";
    echo "<br>Make sure MySQL is running in XAMPP";
}