<?php
/**
 * Database Configuration
 * 
 * This file contains the database connection settings for the application.
 * Update these values to match your database configuration.
 */
// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

return [
    'host' => 'localhost',     // Database host
    'port' => 1456,            // Default MySQL port (change to 3327 if that's what you're using or default 3306)
    'dbname' => 'coding_game',  // Database name
    'username' => 'root',      // Database username
    'password' => '',          // Empty password (common XAMPP default)
    'charset' => 'utf8mb4',    // Database charset
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ],
];
