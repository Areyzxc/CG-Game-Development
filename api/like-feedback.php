<?php
/**
 * File: api/like-feedback.php
 * Purpose: Handles AJAX requests to like feedback messages on the CodeGaming About page.
 * Features:
 *   - Increments like count for a feedback message in the database.
 *   - Prevents multiple likes per session for the same feedback.
 *   - Returns updated like count and success/error status in JSON format.
 * Usage:
 *   - Called via POST from the About page feedback wall like button.
 *   - Requires Database.php for DB access.
 * Included Files/Dependencies:
 *   - includes/Database.php
 * Author: CodeGaming Team
 * Last Updated: July 24, 2025
 */
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/../includes/Database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['feedback_id'])) {
    $feedbackId = intval($_POST['feedback_id']);
    if (!isset($_SESSION['liked_feedback'])) {
        $_SESSION['liked_feedback'] = [];
    }
    if (in_array($feedbackId, $_SESSION['liked_feedback'])) {
        echo json_encode(['success' => false, 'error' => 'Already liked', 'likes' => null]);
        exit;
    }
    $conn = Database::getInstance()->getConnection();
    // Increment likes
    $stmt = $conn->prepare('UPDATE feedback_messages SET likes = likes + 1 WHERE id = ?');
    $stmt->execute([$feedbackId]);
    // Get new like count
    $stmt = $conn->prepare('SELECT likes FROM feedback_messages WHERE id = ?');
    $stmt->execute([$feedbackId]);
    $likes = $stmt->fetchColumn();
    $_SESSION['liked_feedback'][] = $feedbackId;
    echo json_encode(['success' => true, 'likes' => $likes]);
    exit;
}
echo json_encode(['success' => false, 'error' => 'Invalid request']);