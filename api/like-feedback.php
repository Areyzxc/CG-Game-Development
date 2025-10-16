<?php
/**
 * File: api/like-feedback.php
 * Purpose: Handles AJAX requests to like feedback messages on the CodeGaming About page.
 * Features:
 *   - Uses feedback_likes table for proper tracking with IP addresses
 *   - Prevents multiple likes per IP address for the same feedback
 *   - Returns updated like count and success/error status in JSON format
 *   - Supports both logged-in users and anonymous visitors
 * Usage:
 *   - Called via POST from the About page feedback wall like button.
 *   - Requires Database.php for DB access.
 * Included Files/Dependencies:
 *   - includes/Database.php
 * Author: CodeGaming Team
 * Last Updated: September 29, 2025
 */
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/../includes/Database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['feedback_id'])) {
    $feedbackId = intval($_POST['feedback_id']);
    $userId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;
    $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    
    try {
        $conn = Database::getInstance()->getConnection();
        
        // Check if this IP has already liked this feedback
        $checkStmt = $conn->prepare('SELECT id FROM feedback_likes WHERE feedback_id = ? AND ip_address = ?');
        $checkStmt->execute([$feedbackId, $ipAddress]);
        
        if ($checkStmt->fetch()) {
            echo json_encode(['success' => false, 'error' => 'Already liked']);
            exit;
        }
        
        // Insert like record
        $insertStmt = $conn->prepare('INSERT INTO feedback_likes (feedback_id, user_id, ip_address) VALUES (?, ?, ?)');
        $insertStmt->execute([$feedbackId, $userId, $ipAddress]);
        
        // Get updated like count
        $countStmt = $conn->prepare('SELECT COUNT(*) FROM feedback_likes WHERE feedback_id = ?');
        $countStmt->execute([$feedbackId]);
        $likes = $countStmt->fetchColumn();
        
        echo json_encode(['success' => true, 'likes' => $likes]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    }
    exit;
}

echo json_encode(['success' => false, 'error' => 'Invalid request']);
