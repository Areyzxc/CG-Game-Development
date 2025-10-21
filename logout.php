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
require_once 'includes/ActivityLogger.php';

$auth = Auth::getInstance();

// Log the logout action before clearing session
if ($auth->isLoggedIn()) {
    $userId = $_SESSION['user_id'] ?? null;
    $username = $_SESSION['username'] ?? 'Unknown';
    $role = $_SESSION['role'] ?? 'visitor';
    
    // Log logout activity
    $logger = ActivityLogger::getInstance();
    $isAdmin = ($role === 'admin' || $role === 'super_admin');
    
    $logger->logActivity([
        'user_id' => $isAdmin ? null : $userId,
        'admin_id' => $isAdmin ? $userId : null,
        'username' => $username,
        'user_type' => $isAdmin ? 'admin' : 'user',
        'action' => 'logged_out',
        'action_details' => 'User logged out',
        'status' => 'success'
    ]);
}

// Use the Auth class to handle logout properly
$auth->logout();

// Redirect to anchor page
header('Location: anchor.php');
exit;
