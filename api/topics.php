<?php
/**
 * File: api/topics.php
 * Purpose: API endpoint for retrieving, filtering, and updating programming topics and progress for CodeGaming users and visitors.
 * Features:
 *   - Returns topics by language, with progress for users and visitors.
 *   - Supports topic filtering by search, difficulty, progress, and language.
 *   - Updates topic progress and awards achievements for users.
 *   - Handles both GET (fetch topics) and POST (update progress) requests.
 * Usage:
 *   - Called by frontend to display topics, track progress, and award achievements.
 *   - Requires Database.php, Auth.php, and ErrorHandler.php for DB, authentication, and error handling.
 * Included Files/Dependencies:
 *   - includes/Database.php
 *   - includes/Auth.php
 *   - includes/ErrorHandler.php
 * Author: CodeGaming Team
 * Last Updated: July 24, 2025
 */

require_once '../includes/Database.php';
require_once '../includes/Auth.php';
require_once '../includes/ErrorHandler.php';

// Set JSON response headers
header('Content-Type: application/json');

$db = Database::getInstance();
$auth = Auth::getInstance();

// Get user ID or visitor ID
$user = $auth->getCurrentUser();
$userId = ($auth->isLoggedIn() && isset($user['id'])) ? $user['id'] : null;
$visitorId = isset($_COOKIE['visitor_id']) ? $_COOKIE['visitor_id'] : null;

if (!$userId && !$visitorId) {
    // Generate a new visitor ID if none exists
    $visitorId = uniqid('visitor_', true);
    setcookie('visitor_id', $visitorId, time() + (86400 * 30), path: '/'); // 30 days
}

// Handle different request methods
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Get topics for a language
        if (isset($_GET['language'])) {
            $languageId = $_GET['language'];
            $topics = $db->getTopicsByLanguage($languageId);
            
            // Add progress information for each topic
            foreach ($topics as &$topic) {
                if ($userId) {
                    $progress = $db->getUserTopicProgress($userId, $topic['id']);
                    $topic['progress'] = $progress;
                } elseif ($visitorId) {
                    $progress = $db->getVisitorTopicProgress($visitorId, $topic['id']);
                    $topic['progress'] = $progress;
                }
            }
            
            echo json_encode(['success' => true, 'data' => $topics]);
        }
        // Get filtered topics
        elseif (isset($_GET['search']) || isset($_GET['difficulty']) || isset($_GET['progress'])) {
            $filters = [
                'search' => $_GET['search'] ?? '',
                'difficulty' => $_GET['difficulty'] ?? '',
                'progress' => $_GET['progress'] ?? '',
                'language' => $_GET['language_filter'] ?? ''
            ];
            
            $topics = $db->getFilteredTopics($filters, $userId ?? $visitorId);
            echo json_encode(['success' => true, 'data' => $topics]);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['topic_id']) || !isset($data['status'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            exit;
        }

        $topicId = $data['topic_id'];
        $status = $data['status'];
        
        // Update progress
        if ($userId) {
            $success = $db->updateUserTopicProgress($userId, $topicId, $status);
        } else {
            $success = $db->updateVisitorTopicProgress($visitorId, $topicId, $status);
        }

        if ($success) {
            // Check and award achievements
            if ($userId) {
                $achievements = checkAndAwardAchievements($userId, $db);
                echo json_encode([
                    'success' => true,
                    'message' => 'Progress updated successfully',
                    'achievements' => $achievements
                ]);
            } else {
                echo json_encode([
                    'success' => true,
                    'message' => 'Progress updated successfully'
                ]);
            }
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to update progress'
            ]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        break;
}

/**
 * Check and award achievements based on user progress
 */
function checkAndAwardAchievements($userId, $db) {
    $newAchievements = [];
    
    // Get user's progress statistics
    $stats = $db->getUserProgressStats($userId);
    
    // Define achievement criteria
    $achievements = [
        'beginner' => [
            'title' => 'Getting Started',
            'description' => 'Complete your first topic',
            'criteria' => ['completed_topics' => 1]
        ],
        'intermediate' => [
            'title' => 'Making Progress',
            'description' => 'Complete 5 topics',
            'criteria' => ['completed_topics' => 5]
        ],
        'advanced' => [
            'title' => 'Advanced Learner',
            'description' => 'Complete 10 topics',
            'criteria' => ['completed_topics' => 10]
        ],
        'language_master' => [
            'title' => 'Language Master',
            'description' => 'Complete all topics in a language',
            'criteria' => ['language_completion' => 100]
        ]
    ];
    
    // Check each achievement
    foreach ($achievements as $id => $achievement) {
        if (!$db->hasAchievement($userId, $id)) {
            $earned = true;
            foreach ($achievement['criteria'] as $metric => $required) {
                if ($stats[$metric] < $required) {
                    $earned = false;
                    break;
                }
            }
            
            if ($earned) {
                $db->awardAchievement($userId, $id);
                $newAchievements[] = [
                    'id' => $id,
                    'title' => $achievement['title'],
                    'description' => $achievement['description']
                ];
            }
        }
    }
    
    return $newAchievements;
}