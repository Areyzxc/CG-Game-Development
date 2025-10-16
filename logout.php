<?php
/**
 * ==========================================================
 * File: logout.php
 * 
 * Description:
 *   - Handles user logout for Code Gaming platform
 *   - Features:
 *       • Logs logout action to login_logs table (user, role, IP, session)
 *       • Uses Auth class for secure session termination
 *       • Redirects user to anchor page after logout
 * 
 * Usage:
 *   - Called when a user chooses to log out
 *   - Ensures session is properly cleared and activity is logged
 * 
 * Author: [Santiago]
 * Last Updated: [July 22, 2025]
 * -- Code Gaming Team --
 * ==========================================================
 */

require_once 'includes/Auth.php';

$auth = Auth::getInstance();

// Log the logout action before clearing session
if ($auth->isLoggedIn()) {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    // Log the logout
    $stmt = $conn->prepare("
        INSERT INTO login_logs (user_id, role, ip_address, session_id, login_time)
        VALUES (?, ?, ?, ?, NOW())
    ");
    
    $userId = $_SESSION['user_id'] ?? null;
    $role = $_SESSION['role'] ?? 'visitor';
    $ip = $_SERVER['REMOTE_ADDR'];
    $sessionId = session_id();
    
    $stmt->execute([$userId, $role, $ip, $sessionId]);
}

// Use the Auth class to handle logout properly
$auth->logout();

// Redirect to anchor page
header('Location: anchor.php');
exit;
