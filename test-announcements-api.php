<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start output buffering
ob_start();

// Set content type
header('Content-Type: text/html; charset=utf-8');

echo "<h2>Announcements API Test</h2>";

try {
    // Test direct database connection first
    echo "<h3>1. Testing Database Connection</h3>";
    require_once __DIR__ . '/includes/Database.php';
    $db = Database::getInstance()->getConnection();
    echo "<p style='color: green;'>✓ Database connection successful!</p>";
    
    // Test announcements query
    echo "<h3>2. Testing Announcements Query</h3>";
    $query = "SELECT * FROM announcements WHERE is_active = 1 AND status = 'published' ORDER BY is_pinned DESC, created_at DESC LIMIT 3";
    $stmt = $db->query($query);
    $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($announcements) > 0) {
        echo "<p style='color: green;'>✓ Found " . count($announcements) . " announcements:</p>";
        echo "<pre>" . print_r($announcements, true) . "</pre>";
        
        // Test the API endpoint
        echo "<h3>3. Testing API Endpoint</h3>";
        $apiUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/CodeGaming/api/get-announcements.php?guest=1';
        echo "<p>Testing API URL: <code>" . htmlspecialchars($apiUrl) . "</code></p>";
        
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        echo "<p>HTTP Status Code: <strong>" . $httpCode . "</strong></p>";
        
        if ($response === false) {
            echo "<p style='color: red;'>✗ cURL Error: " . curl_error($ch) . "</p>";
        } else {
            $data = json_decode($response, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                echo "<p style='color: green;'>✓ Valid JSON Response:</p>";
                echo "<pre>" . json_encode($data, JSON_PRETTY_PRINT) . "</pre>";
            } else {
                echo "<p style='color: orange;'>Response (not JSON):</p>";
                echo "<pre>" . htmlspecialchars($response) . "</pre>";
            }
        }
        curl_close($ch);
    } else {
        echo "<p style='color: orange;'>No active announcements found in the database.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

// Get and clean the output
$output = ob_get_clean();

// Output the results
echo "<!DOCTYPE html>
<html>
<head>
    <title>Announcements API Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
        pre { background: #f4f4f4; padding: 10px; border-radius: 5px; overflow-x: auto; }
        h3 { margin-top: 30px; border-bottom: 1px solid #eee; padding-bottom: 5px; }
        code { background: #f4f4f4; padding: 2px 5px; border-radius: 3px; }
    </style>
</head>
<body>
    <h1>Announcements API Test</h1>
    $output
</body>
</html>";
?>
