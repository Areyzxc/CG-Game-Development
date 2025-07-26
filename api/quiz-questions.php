<?php
/**
 * File: api/quiz-questions.php
 * Purpose: API endpoint for retrieving 40 random quiz questions and choices for a given difficulty in CodeGaming.
 * Features:
 *   - Validates difficulty parameter (beginner, intermediate, and expert).
 *   - Fetches 40 random questions and their choices from the database.
 *   - Returns question data with multiple choices/true or false and correct answer flags in JSON format.
 * Usage:
 *   - Called via AJAX from quiz.js to load questions for the quiz game.
 *   - Requires Database.php for DB access.
 * Included Files/Dependencies:
 *   - includes/Database.php
 * Author: CodeGaming Team
 * Last Updated: July 24, 2025
 */

// api/quiz-questions.php
require_once '../includes/Database.php';
header('Content-Type: application/json');

if (!isset($_GET['difficulty']) || !in_array($_GET['difficulty'], ['beginner','intermediate','expert'])) {
    echo json_encode(['success'=>false, 'error'=>'Invalid difficulty']);
    exit;
}
$difficulty = $_GET['difficulty'];

try {
    $db = Database::getInstance()->getConnection();
    // Get 40 random questions for the difficulty
    $stmt = $db->prepare("SELECT id, question, question_type FROM quiz_questions WHERE difficulty = :difficulty ORDER BY RAND() LIMIT 40");
    $stmt->execute(['difficulty'=>$difficulty]);
    $questions = [];
    while ($q = $stmt->fetch()) {
        // Get choices for this question
        $choicesStmt = $db->prepare("SELECT id, answer, is_correct FROM quiz_answers WHERE question_id = :qid");
        $choicesStmt->execute(['qid'=>$q['id']]);
        $choices = [];
        while ($c = $choicesStmt->fetch()) {
            $choices[] = [ 
                'id' => $c['id'], 
                'text' => $c['answer'],
                'is_correct' => (int)$c['is_correct'] // Include is_correct field
            ];
        }
        $questions[] = [
            'id' => $q['id'],
            'type' => $q['question_type'],
            'question' => $q['question'],
            'choices' => $choices
        ];
    }
    echo json_encode(['success'=>true, 'questions'=>$questions]);
} catch (Exception $e) {
    echo json_encode(['success'=>false, 'error'=>'Server error']);
}