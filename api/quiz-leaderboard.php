<?php
/**
 * File: api/quiz-leaderboard.php
 * Purpose: API endpoint for retrieving quiz leaderboard and user/guest stats for CodeGaming quizzes.
 * Features:
 *   - Fetches top 10 users and guests by correct answers for a given difficulty and scope (alltime/weekly).
 *   - Combines, sorts, and returns leaderboard data in JSON format.
 *   - Provides user/guest stats for stat cards (best score, last played, rank, percentage).
 *   - Identifies top player for display.
 * Usage:
 *   - Called via AJAX from quiz.js and home page analytics for leaderboard and stats display.
 *   - Requires Database.php for DB access.
 * Included Files/Dependencies:
 *   - includes/Database.php
 * Author: CodeGaming Team
 * Last Updated: July 24, 2025
 */

// api/quiz-leaderboard.php
require_once '../includes/Database.php';
header('Content-Type: application/json');

$difficulty = isset($_GET['difficulty']) ? $_GET['difficulty'] : null;
$scope = isset($_GET['scope']) ? $_GET['scope'] : 'alltime';
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;
$guest_session_id = isset($_GET['guest_session_id']) ? intval($_GET['guest_session_id']) : null;
if (!in_array($difficulty, ['beginner','intermediate','expert'])) {
    echo json_encode(['success'=>false, 'error'=>'Invalid difficulty']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    $date_limit = '';
    if ($scope === 'weekly') {
        $date_limit = "AND attempted_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
    }
    
    // Get top 10 users by correct answers for this difficulty
    $sql = "SELECT u.username, COUNT(*) as score, MAX(a.attempted_at) as played_at
            FROM user_quiz_attempts a
            JOIN quiz_questions q ON a.question_id = q.id
            JOIN users u ON a.user_id = u.id
            WHERE a.is_correct = 1 AND q.difficulty = :difficulty $date_limit
            GROUP BY a.user_id
            ORDER BY score DESC, played_at DESC
            LIMIT 10";
    $stmt = $db->prepare($sql);
    $stmt->execute(['difficulty'=>$difficulty]);
    $userRows = $stmt->fetchAll();
    
    // Get top 10 guests by correct answers for this difficulty
    $sql = "SELECT g.nickname as username, COUNT(*) as score, MAX(a.attempted_at) as played_at
            FROM guest_quiz_attempts a
            JOIN quiz_questions q ON a.question_id = q.id
            JOIN guest_sessions g ON a.guest_session_id = g.id
            WHERE a.is_correct = 1 AND q.difficulty = :difficulty $date_limit
            GROUP BY a.guest_session_id
            ORDER BY score DESC, played_at DESC
            LIMIT 10";
    $stmt = $db->prepare($sql);
    $stmt->execute(['difficulty'=>$difficulty]);
    $guestRows = $stmt->fetchAll();
    
    // Combine and sort all results
    $allResults = array_merge($userRows, $guestRows);
    usort($allResults, function($a, $b) {
        if ($a['score'] != $b['score']) {
            return $b['score'] - $a['score']; // Higher score first
        }
        return strtotime($b['played_at']) - strtotime($a['played_at']); // More recent first
    });
    
    // Take top 10 overall
    $leaderboard = array_slice($allResults, 0, 10);

    // --- User/Guest Stats for Stat Cards ---
    $user_stats = null;
    if ($user_id) {
        // Best score (most correct in a single quiz session)
        $sql = "SELECT COUNT(*) as best_score, MAX(attempted_at) as last_played FROM user_quiz_attempts a JOIN quiz_questions q ON a.question_id = q.id WHERE a.user_id = :uid AND a.is_correct = 1 AND q.difficulty = :difficulty $date_limit";
        $stmt = $db->prepare($sql);
        $stmt->execute(['uid'=>$user_id, 'difficulty'=>$difficulty]);
        $row = $stmt->fetch();
        $user_stats = [
            'best_score' => $row ? intval($row['best_score']) : 0,
            'last_played' => $row ? $row['last_played'] : null,
            'rank' => null,
            'total_questions' => 40,
            'best_percentage' => $row && $row['best_score'] ? round($row['best_score']/40*100) : 0
        ];
        // Find rank
        foreach ($leaderboard as $i => $entry) {
            if (isset($entry['username']) && $entry['username'] === $_GET['username']) {
                $user_stats['rank'] = $i+1;
                break;
            }
        }
    } else if ($guest_session_id) {
        $sql = "SELECT COUNT(*) as best_score, MAX(attempted_at) as last_played FROM guest_quiz_attempts a JOIN quiz_questions q ON a.question_id = q.id WHERE a.guest_session_id = :gsid AND a.is_correct = 1 AND q.difficulty = :difficulty $date_limit";
        $stmt = $db->prepare($sql);
        $stmt->execute(['gsid'=>$guest_session_id, 'difficulty'=>$difficulty]);
        $row = $stmt->fetch();
        $user_stats = [
            'best_score' => $row ? intval($row['best_score']) : 0,
            'last_played' => $row ? $row['last_played'] : null,
            'rank' => null,
            'total_questions' => 40,
            'best_percentage' => $row && $row['best_score'] ? round($row['best_score']/40*100) : 0
        ];
        // Find rank
        foreach ($leaderboard as $i => $entry) {
            if (isset($entry['username']) && $entry['username'] === $_GET['nickname']) {
                $user_stats['rank'] = $i+1;
                break;
            }
        }
    }
    // Top player (first in leaderboard)
    $top_player = isset($leaderboard[0]) ? [
        'username' => $leaderboard[0]['username'],
        'score' => $leaderboard[0]['score']
    ] : null;

    echo json_encode(['success'=>true, 'leaderboard'=>$leaderboard, 'user_stats'=>$user_stats, 'top_player'=>$top_player]);
} catch (Exception $e) {
    echo json_encode(['success'=>false, 'error'=>'Server error']);
}