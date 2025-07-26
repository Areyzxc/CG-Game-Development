<?php
/**
 * File: api/guest-session.php
 * Purpose: API endpoint for creating guest sessions in CodeGaming, storing nickname, session, and device info.
 * Features:
 *   - Accepts POSTed data for nickname, session ID, IP address, and user agent.
 *   - Inserts new guest session into guest_sessions table and returns session ID.
 *   - Returns JSON response for success or error.
 * Usage:
 *   - Called via AJAX from quiz.js and challenge.js for guest session creation.
 *   - Requires Database.php for DB access.
 * Included Files/Dependencies:
 *   - includes/Database.php
 * Author: CodeGaming Team
 * Last Updated: July 24, 2025
 */

// api/guest-session.php
require_once '../includes/Database.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) $data = $_POST;

$nickname = isset($data['nickname']) ? trim($data['nickname']) : '';
$session_id = isset($data['session_id']) ? trim($data['session_id']) : '';
$ip_address = isset($data['ip_address']) ? trim($data['ip_address']) : '';
$user_agent = isset($data['user_agent']) ? trim($data['user_agent']) : '';

if (!$nickname || !$session_id || !$ip_address) {
    echo json_encode(['success'=>false, 'error'=>'Missing data']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("INSERT INTO guest_sessions (session_id, ip_address, user_agent, nickname) VALUES (:sid, :ip, :ua, :nick)");
    $stmt->execute([
        'sid' => $session_id,
        'ip' => $ip_address,
        'ua' => $user_agent,
        'nick' => $nickname
    ]);
    $guest_session_id = $db->lastInsertId();
    echo json_encode(['success'=>true, 'guest_session_id'=>$guest_session_id]);
} catch (Exception $e) {
    echo json_encode(['success'=>false, 'error'=>'Server error']);
}