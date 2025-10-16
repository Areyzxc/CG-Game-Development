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
require_once '../includes/CSRFProtection.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Invalid input data');
    }
    
    // Validate CSRF token
    $csrf = CSRFProtection::getInstance();
    $csrfToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!$csrf->validateToken($csrfToken)) {
        throw new Exception('Invalid CSRF token');
    }
    
    $db = Database::getInstance();
    
    // Extract and validate data
    $userId = isset($input['user_id']) && is_numeric($input['user_id']) ? (int)$input['user_id'] : null;
    $guestSessionId = isset($input['guest_session_id']) && is_numeric($input['guest_session_id']) ? (int)$input['guest_session_id'] : null;
    $questionId = isset($input['question_id']) && is_numeric($input['question_id']) ? (int)$input['question_id'] : null;
    $submittedAnswer = isset($input['submitted_answer']) ? trim($input['submitted_answer']) : '';
    $timeTaken = isset($input['time_taken']) && is_numeric($input['time_taken']) ? (float)$input['time_taken'] : 0;
    
    // Validate required fields
    if (!$questionId) {
        throw new Exception('Question ID is required');
    }
    
    if (!$userId && !$guestSessionId) {
        throw new Exception('User ID or Guest Session ID is required');
    }
    
    if (empty($submittedAnswer)) {
        throw new Exception('Answer cannot be empty');
    }
    
    // Validate answer length (prevent extremely long answers)
    if (strlen($submittedAnswer) > 10000) {
        throw new Exception('Answer is too long');
    }
    
    // Validate time taken range
    if ($timeTaken < 0 || $timeTaken > 10000) {
        throw new Exception('Invalid time taken value');
    }
    
    // Sanitize submitted answer
    $submittedAnswer = htmlspecialchars($submittedAnswer, ENT_QUOTES, 'UTF-8');
    
    // Check if answer is correct
    $matchedExplanation = null;
    $isCorrect = checkAnswer($db, $questionId, $submittedAnswer, $matchedExplanation);
    $pointsEarned = $isCorrect ? 30 : 0;
    
    // Insert attempt with error handling
    try {
        if ($userId) {
            // User attempt
            $stmt = $db->prepare("
                INSERT INTO user_challenge_attempts 
                (user_id, question_id, submitted_answer, is_correct, points_earned, time_taken)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            if (!$stmt) {
                throw new Exception('Failed to prepare user attempt statement');
            }
            $stmt->execute([$userId, $questionId, $submittedAnswer, $isCorrect, $pointsEarned, $timeTaken]);
        } else {
            // Guest attempt
            $stmt = $db->prepare("
                INSERT INTO guest_challenge_attempts 
                (guest_session_id, question_id, submitted_answer, is_correct, points_earned, time_taken)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            if (!$stmt) {
                throw new Exception('Failed to prepare guest attempt statement');
            }
            $stmt->execute([$guestSessionId, $questionId, $submittedAnswer, $isCorrect, $pointsEarned, $timeTaken]);
        }
    } catch (Exception $e) {
        error_log("Failed to insert challenge attempt: " . $e->getMessage());
        throw new Exception('Failed to record attempt. Please try again.');
    }
    
    echo json_encode([
        'success' => true,
        'correct' => $isCorrect,
        'points_earned' => $pointsEarned,
        'explanation' => $matchedExplanation
    ]);
    
} catch (Exception $e) {
    // Log the error for debugging
    error_log("Challenge attempt error: " . $e->getMessage());
    
    // Return user-friendly error message
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'debug' => getenv('ENVIRONMENT') !== 'production' ? $e->getMessage() : null
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
