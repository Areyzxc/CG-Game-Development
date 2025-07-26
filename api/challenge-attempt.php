<?php
/**
 * File: api/challenge-attempt.php
 * Purpose: API endpoint for submitting and validating challenge question attempts for CodeGaming.
 * Features:
 *   - Accepts user/guest challenge attempts via POST request.
 *   - Validates submitted answers against challenge_answers or code_challenges tables.
 *   - Records attempts for both users and guests, including time taken and points earned.
 *   - Returns correctness, points, and explanation for frontend feedback.
 * Usage:
 *   - Called via AJAX from challenge.js when a user/guest submits an answer.
 *   - Requires Database.php for DB access.
 * Included Files/Dependencies:
 *   - includes/Database.php
 * Author: CodeGaming Team
 * Last Updated: July 24, 2025
 */
header('Content-Type: application/json');
require_once '../includes/Database.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Invalid input data');
    }
    
    $db = Database::getInstance();
    
    // Extract data
    $userId = $input['user_id'] ?? null;
    $guestSessionId = $input['guest_session_id'] ?? null;
    $questionId = $input['question_id'] ?? null;
    $submittedAnswer = $input['submitted_answer'] ?? '';
    $timeTaken = $input['time_taken'] ?? 0;
    
    // Validate required fields
    if (!$questionId) {
        throw new Exception('Question ID is required');
    }
    
    if (!$userId && !$guestSessionId) {
        throw new Exception('User ID or Guest Session ID is required');
    }
    
    // Check if answer is correct
    $matchedExplanation = null;
    $isCorrect = checkAnswer($db, $questionId, $submittedAnswer, $matchedExplanation);
    $pointsEarned = $isCorrect ? 30 : 0;
    
    // Insert attempt
    if ($userId) {
        // User attempt
        $stmt = $db->prepare("
            INSERT INTO user_challenge_attempts 
            (user_id, question_id, submitted_answer, is_correct, points_earned, time_taken)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$userId, $questionId, $submittedAnswer, $isCorrect, $pointsEarned, $timeTaken]);
    } else {
        // Guest attempt
        $stmt = $db->prepare("
            INSERT INTO guest_challenge_attempts 
            (guest_session_id, question_id, submitted_answer, is_correct, points_earned, time_taken)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$guestSessionId, $questionId, $submittedAnswer, $isCorrect, $pointsEarned, $timeTaken]);
    }
    
    echo json_encode([
        'success' => true,
        'correct' => $isCorrect,
        'points_earned' => $pointsEarned,
        'explanation' => $matchedExplanation
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

function checkAnswer($db, $questionId, $submittedAnswer, &$matchedExplanation = null) {
    // Get all correct and incorrect answers from the database
    $stmt = $db->prepare("
        SELECT ca.answer_text, ca.is_correct, ca.explanation, cq.type, cq.expected_output
        FROM challenge_answers ca
        JOIN challenge_questions cq ON ca.question_id = cq.id
        WHERE ca.question_id = ?
    ");
    $stmt->execute([$questionId]);
    $allAnswers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$allAnswers || count($allAnswers) === 0) {
        // Fallback to code_challenges table
        $stmt = $db->prepare("
            SELECT test_cases, starter_code
            FROM code_challenges
            WHERE id = ?
        ");
        $stmt->execute([$questionId]);
        $challenge = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($challenge) {
            $isCorrect = strtolower(trim($submittedAnswer)) === strtolower(trim($challenge['starter_code'] ?? ''));
            $matchedExplanation = $isCorrect ? 'Correct code!' : 'Check your code logic.';
            return $isCorrect;
        }
        $matchedExplanation = null;
        return false;
    }

    $submittedAnswerNorm = strtolower(trim($submittedAnswer));
    foreach ($allAnswers as $row) {
        $answerNorm = strtolower(trim($row['answer_text']));
        if ($submittedAnswerNorm === $answerNorm) {
            $matchedExplanation = $row['explanation'] ?? null;
            return (int)$row['is_correct'] === 1;
        }
    }
    // If not matched, optionally return explanation for a generic wrong answer
    $matchedExplanation = null;
    return false;
}
?> 