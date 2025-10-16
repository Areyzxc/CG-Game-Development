<?php
/**
 * File: api/check-challenge-answer.php
 * Purpose: API endpoint for validating challenge answers in CodeGaming, supporting multiple question types.
 * Features:
 *   - Accepts POSTed data for question ID and submitted answer.
 *   - Checks answer correctness for code, fill_blank, output, and case_study types.
 *   - Returns JSON response with correctness and points earned.
 * Usage:
 *   - Called via AJAX from challenge.js for answer validation.
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
    
    $questionId = $input['question_id'] ?? null;
    $submittedAnswer = $input['answer'] ?? '';
    
    if (!$questionId) {
        throw new Exception('Question ID is required');
    }
    
    // Check if answer is correct
    $isCorrect = checkAnswer($db, $questionId, $submittedAnswer);
    
    echo json_encode([
        'success' => true,
        'correct' => $isCorrect,
        'points_earned' => $isCorrect ? 30 : 0
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

function checkAnswer($db, $questionId, $submittedAnswer) {
    // Get the correct answer from the database
    $stmt = $db->prepare("
        SELECT ca.answer_text, cq.type, cq.expected_output
        FROM challenge_answers ca
        JOIN challenge_questions cq ON ca.question_id = cq.id
        WHERE ca.question_id = ? AND ca.is_correct = 1
    ");
    $stmt->execute([$questionId]);
    $correctAnswer = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$correctAnswer) {
        // Fallback to code_challenges table
        $stmt = $db->prepare("
            SELECT test_cases, starter_code, description
            FROM code_challenges
            WHERE id = ?
        ");
        $stmt->execute([$questionId]);
        $challenge = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($challenge) {
            // For code challenges, we'll do a simple comparison
            // In a real implementation, you'd want to execute the code
            return strtolower(trim($submittedAnswer)) === strtolower(trim($challenge['starter_code'] ?? ''));
        }
        
        return false;
    }
    
    $submittedAnswer = trim(strtolower($submittedAnswer));
    $correctAnswerText = trim(strtolower($correctAnswer['answer_text']));
    
    // For different question types, we might need different comparison logic
    switch ($correctAnswer['type']) {
        case 'code':
            // For code questions, we might want to execute the code
            // For now, do a simple string comparison
            return $submittedAnswer === $correctAnswerText;
            
        case 'fill_blank':
        case 'output':
        case 'case_study':
            // For these types, do exact string comparison
            return $submittedAnswer === $correctAnswerText;
            
        default:
            return $submittedAnswer === $correctAnswerText;
    }
}
?>
