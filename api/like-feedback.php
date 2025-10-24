<?php
/**
 * File: api/like-feedback.php
 * Purpose: Handles AJAX requests to like feedback messages on the CodeGaming About page.
 * Features:
 *   - Uses user_feedback_likes table for tracking likes by user_id
 *   - Prevents multiple likes per user for the same feedback
 *   - Returns updated like count and success/error status in JSON format
 *   - Only allows logged-in users to like feedback
 * Usage:
 *   - Called via POST from the About page feedback wall like button.
 *   - Requires Database.php for DB access.
 * Included Files/Dependencies:
 *   - includes/Database.php
 * Author: CodeGaming Team
 * Last Updated: October 25, 2025
 */
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/../includes/Database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Please log in to like feedback']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['feedback_id'])) {
    $feedbackId = intval($_POST['feedback_id']);
    $userId = intval($_SESSION['user_id']);
    
    try {
        $conn = Database::getInstance()->getConnection();
        
        // Check if this user has already liked this feedback
        $checkStmt = $conn->prepare('SELECT id FROM user_feedback_likes WHERE feedback_id = ? AND user_id = ?');
        $checkStmt->execute([$feedbackId, $userId]);
        
        if ($checkStmt->fetch()) {
            echo json_encode(['success' => false, 'error' => 'You have already liked this feedback!']);
            exit;
        }
        
        // Start transaction
        $conn->beginTransaction();
        
        try {
            // Insert like record
            $insertStmt = $conn->prepare('INSERT INTO user_feedback_likes (feedback_id, user_id) VALUES (?, ?)');
            $insertStmt->execute([$feedbackId, $userId]);
            
            // Update like count in feedback_messages table
            $updateStmt = $conn->prepare('UPDATE feedback_messages SET likes = likes + 1 WHERE id = ?');
            $updateStmt->execute([$feedbackId]);
            
            // Commit transaction
            $conn->commit();
            
            // Get updated like count
            $countStmt = $conn->prepare('SELECT likes FROM feedback_messages WHERE id = ?');
            $countStmt->execute([$feedbackId]);
            $likes = $countStmt->fetchColumn();
            
            echo json_encode(['success' => true, 'likes' => $likes]);
            
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollBack();
            throw $e;
        }
        
    } catch (Exception $e) {
        error_log('Error in like-feedback.php: ' . $e->getMessage());
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    }
    exit;
}

echo json_encode(['success' => false, 'error' => 'Invalid request']);
