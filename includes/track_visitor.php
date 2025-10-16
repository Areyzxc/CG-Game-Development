<?php
/**
 * Visitor Tracker
 * 
 * Tracks visitors and records their information in the database.
 * This file should be included at the top of every page where you want to track visitors.
 * 
 * @package CodeGaming
 * @subpackage Core
 * @version 1.0.0
 * @author CodeGaming Team
 */

// Only track if not in admin area to avoid inflating stats
$isAdminArea = strpos($_SERVER['REQUEST_URI'], '/admin/') !== false;

if (!$isAdminArea) {
    // Include the VisitorTracker class
    require_once __DIR__ . '/VisitorTracker.php';
    
    // Initialize and track the visitor
    try {
        $tracker = new VisitorTracker();
        $tracker->track();
    } catch (Exception $e) {
        // Log error but don't break the page
        error_log('Visitor tracking error: ' . $e->getMessage());
    }
}
?>
