<?php
/**
 * File: api/check-guest-nickname.php
 * Purpose: API endpoint for checking guest nickname availability in CodeGaming.
 * Features:
 *   - Accepts GET requests with nickname parameter.
 *   - Checks guest_sessions table for nickname existence.
 *   - Returns JSON response indicating availability (true/false).
 * Usage:
 *   - Called via AJAX from quiz.js and challenge.js for guest nickname validation.
 *   - Requires Database.php for DB access.
 * Included Files/Dependencies:
 *   - includes/Database.php
 * Author: CodeGaming Team
 * Last Updated: July 24, 2025
 */
require_once '../includes/Database.php';
header('Content-Type: application/json');

$nickname = isset($_GET['nickname']) ? trim($_GET['nickname']) : '';
if ($nickname === '') {
    echo json_encode(['available' => false]);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare('SELECT COUNT(*) FROM guest_sessions WHERE nickname = :nickname');
    $stmt->execute(['nickname' => $nickname]);
    $count = $stmt->fetchColumn();
    echo json_encode(['available' => $count == 0]);
} catch (Exception $e) {
    echo json_encode(['available' => false]);
}