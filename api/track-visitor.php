<?php
/**
 * File: api/track-visitor.php
 * Purpose: Tracks anonymous visitors to CodeGaming, logging IP and user agent for analytics and security.
 * Features:
 *   - Logs visitor IP and user agent if not logged in.
 *   - Records visit in login_logs table for tracking.
 *   - Handles errors silently and logs them for review.
 * Usage:
 *   - Called automatically on page load for non-authenticated users.
 *   - Requires Database.php and Auth.php for DB and authentication logic.
 * Included Files/Dependencies:
 *   - includes/Database.php
 *   - includes/Auth.php
 * Author: CodeGaming Team
 * Last Updated: July 24, 2025
 */

require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/Auth.php';

$auth = Auth::getInstance();
$db = Database::getInstance();

// Only track if no user is logged in
if (!$auth->isLoggedIn()) {
    try {
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        
        // Log the visitor
        $visitorId = $db->logVisitor($ipAddress, $userAgent);
        
        if ($visitorId) {
            // Log the visit in login_logs
            $db->logLogin($visitorId, 'visitor', $ipAddress, session_id());
        }
        
    } catch (Exception $e) {
        // Log error but don't expose it to user
        error_log("Visitor tracking error: " . $e->getMessage());
    }
}