<?php
/**
 * API Endpoint: Get Guest Progress
 * Returns default progress data for guest users
 */

// Set headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

// Default guest progress data
$guestProgress = [
    'success' => true,
    'data' => [
        'tutorial' => [
            'total_topics' => 30,
            'completed_topics' => 0,
            'progress' => 0,
            'next_topic' => 'Getting Started with HTML',
            'next_url' => 'tutorial.php?lang=html&topic=getting-started'
        ],
        'quiz' => [
            'high_score' => 0,
            'total_attempts' => 0,
            'avg_score' => 0,
            'best_category' => 'None',
            'last_attempt' => null
        ],
        'mini_game' => [
            'high_score' => 0,
            'total_plays' => 0,
            'favorite_mode' => 'None',
            'last_played' => null
        ],
        'achievements' => [
            'total' => 15,
            'unlocked' => 0,
            'recent' => []
        ],
        'is_guest' => true
    ]
];

// Return the guest progress data
echo json_encode($guestProgress);
?>
