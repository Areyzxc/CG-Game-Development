<?php
/**
 * File: api/quiz-attempt.php
 * Purpose: API endpoint for recording quiz answer attempts for CodeGaming users and guests.
 * Features:
 *   - Accepts POSTed data for user/guest, question, selected choice, correctness, and time taken.
 *   - Inserts attempt into user_quiz_attempts or guest_quiz_attempts table.
 *   - Returns JSON response for success or error.
 * Usage:
 *   - Called via AJAX from quiz.js when a user/guest answers a quiz question.
 *   - Requires Database.php for DB access.
 * Included Files/Dependencies:
 *   - includes/Database.php
 * Author: CodeGaming Team
 * Last Updated: July 24, 2025
 */

// api/quiz-attempt.php
require_once '../includes/Database.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) $data = $_POST;

$user_id = isset($data['user_id']) ? intval($data['user_id']) : null;
$guest_session_id = isset($data['guest_session_id']) ? intval($data['guest_session_id']) : null;
$question_id = isset($data['question_id']) ? intval($data['question_id']) : null;
$selected_choice_id = isset($data['selected_choice_id']) ? intval($data['selected_choice_id']) : null;
$is_correct = isset($data['is_correct']) ? (bool)$data['is_correct'] : null;
$time_taken = isset($data['time_taken']) ? floatval($data['time_taken']) : null;

if (!$question_id || !$selected_choice_id || $is_correct === null) {
    echo json_encode(['success'=>false, 'error'=>'Missing data']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    if ($user_id) {
        // Insert attempt for logged-in user
        $stmt = $db->prepare("INSERT INTO user_quiz_attempts (user_id, question_id, selected_answer_id, is_correct, points_earned, attempted_at) VALUES (:uid, :qid, :aid, :isc, :pts, NOW())");
        $stmt->execute([
            'uid' => $user_id,
            'qid' => $question_id,
            'aid' => $selected_choice_id,
            'isc' => $is_correct ? 1 : 0,
            'pts' => $is_correct ? 1 : 0
        ]);
        echo json_encode(['success'=>true]);
    } else if ($guest_session_id) {
        // Insert attempt for guest
        $stmt = $db->prepare("INSERT INTO guest_quiz_attempts (guest_session_id, question_id, selected_answer_id, is_correct, points_earned, attempted_at) VALUES (:gsid, :qid, :aid, :isc, :pts, NOW())");
        $stmt->execute([
            'gsid' => $guest_session_id,
            'qid' => $question_id,
            'aid' => $selected_choice_id,
            'isc' => $is_correct ? 1 : 0,
            'pts' => $is_correct ? 1 : 0
        ]);
        echo json_encode(['success'=>true]);
    } else {
        echo json_encode(['success'=>false, 'error'=>'No user or guest session provided']);
    }
} catch (Exception $e) {
    echo json_encode(['success'=>false, 'error'=>'Server error']);
}
