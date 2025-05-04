<?php
// db_connection.php

// 1. Database credentials
$DB_HOST = 'localhost:3327';
$DB_PORT = 3327;            // XAMPP default port for MySQL is 3327; change if you’ve set a different port
$DB_NAME = 'code_kombat_db'; 
$DB_USER = 'root';          // default for XAMPP; change if you’ve set a password
$DB_PASS = '';              // default is empty

// 2. Create connection
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $DB_PORT);

// 3. Check for errors
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// 4. Set charset for proper encoding
$conn->set_charset("utf8mb4");
