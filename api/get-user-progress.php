<?php
// api/get-user-progress.php
// Returns user progress data for the dashboard

header('Content-Type: application/json');

require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/Auth.php';
require_once __DIR__ . '/../includes/CSRFProtection.php';

try {
    $auth = Auth::getInstance();
    $db = Database::getInstance()->getConnection();
    $csrf = CSRFProtection::getInstance();

    // Validate CSRF header
    $csrfHeader = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!$csrf->validateToken($csrfHeader)) {
        http_response_code(403);
        echo json_encode(['error' => 'Invalid CSRF token']);
        exit;
    }

    if (!$auth->isLoggedIn()) {
        http_response_code(401);
        echo json_encode(['error' => 'Not authenticated']);
        exit;
    }

    $currentUser = $auth->getCurrentUser();
    $userId = $currentUser['id'] ?? null;

    if (!$userId) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid user']);
        exit;
    }

    // Get user profile data including profile picture and banner
    $stmt = $db->prepare("SELECT profile_picture, header_banner FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Default profile picture if not set
    $defaultProfilePic = '/assets/images/default-avatar.png';
    $profilePicture = !empty($userData['profile_picture']) ? 
        $userData['profile_picture'] : $defaultProfilePic;
    
    // Process profile picture path
    if ($profilePicture !== $defaultProfilePic) {
        // Convert backslashes to forward slashes and ensure it's a valid path
        $profilePicture = str_replace('\\', '/', $profilePicture);
        $filename = basename($profilePicture);
        
        // Define the base URL for the website
        $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
        
        // Define the full server path to the avatars directory
        $avatarDir = $_SERVER['DOCUMENT_ROOT'] . '/CodeGaming/uploads/avatars/';
        
        // Try to find the file with exact match first
        $exactPath = $avatarDir . $filename;
        
        if (file_exists($exactPath)) {
            $profilePicture = $baseUrl . '/CodeGaming/uploads/avatars/' . $filename;
        } else {
            // If exact match not found, try case-insensitive search
            $found = false;
            if (is_dir($avatarDir) && $handle = opendir($avatarDir)) {
                while (($file = readdir($handle)) !== false) {
                    if (strtolower($file) === strtolower($filename)) {
                        $profilePicture = $baseUrl . '/CodeGaming/uploads/avatars/' . $file;
                        $found = true;
                        break;
                    }
                }
                closedir($handle);
                
                if (!$found) {
                    $profilePicture = $baseUrl . '/CodeGaming' . $defaultProfilePic;
                    error_log("Profile picture not found: " . $filename);
                }
            } else {
                $profilePicture = $baseUrl . '/CodeGaming' . $defaultProfilePic;
                error_log("Avatar directory not accessible: " . $avatarDir);
            }
        }
    }
    
    // Default banner if not set
    $defaultBanner = '/assets/images/default-banner.jpg';
    $bannerUrl = !empty($userData['header_banner']) ? $userData['header_banner'] : $defaultBanner;
    
    // Process banner path
    if ($bannerUrl !== $defaultBanner) {
        // Convert backslashes to forward slashes and ensure it's a valid path
        $bannerUrl = str_replace('\\', '/', $bannerUrl);
        $filename = basename($bannerUrl);
        
        // Define the base URL for the website
        $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
        
        // Define the full server path to the banners directory
        $bannerDir = $_SERVER['DOCUMENT_ROOT'] . '/CodeGaming/uploads/banners/';
        
        // Try to find the file with exact match first
        $exactPath = $bannerDir . $filename;
        
        if (file_exists($exactPath)) {
            $bannerUrl = $baseUrl . '/CodeGaming/uploads/banners/' . $filename;
        } else {
            // If exact match not found, try case-insensitive search
            $found = false;
            if (is_dir($bannerDir) && $handle = opendir($bannerDir)) {
                while (($file = readdir($handle)) !== false) {
                    if (strtolower($file) === strtolower($filename)) {
                        $bannerUrl = $baseUrl . '/CodeGaming/uploads/banners/' . $file;
                        $found = true;
                        break;
                    }
                }
                closedir($handle);
                
                if (!$found) {
                    $bannerUrl = $baseUrl . '/CodeGaming' . $defaultBanner;
                    error_log("Banner not found: " . $filename);
                }
            } else {
                $bannerUrl = $baseUrl . '/CodeGaming' . $defaultBanner;
                error_log("Banner directory not accessible: " . $bannerDir);
            }
        }
    }

    $progressData = [
        'user' => [
            'profile_picture' => $profilePicture,
            'header_banner' => $bannerUrl,
            'username' => $currentUser['username'] ?? 'User'
        ]
    ];

    // Get tutorial progress
    $stmt = $db->prepare("
        SELECT COUNT(*) as total_topics, 
               SUM(CASE WHEN status = 'done_reading' THEN 1 ELSE 0 END) as completed_topics
        FROM user_progress 
        WHERE user_id = ?
    ");
    $stmt->execute([$userId]);
    $tutorialProgress = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $totalTutorials = max(1, (int)$tutorialProgress['total_topics']);
    $completedTutorials = (int)$tutorialProgress['completed_topics'];
    $tutorialPercentage = round(($completedTutorials / $totalTutorials) * 100);
    
    $progressData['tutorials'] = [
        'completed' => $completedTutorials,
        'total' => $totalTutorials,
        'percentage' => $tutorialPercentage
    ];

    // Get achievements count
    $stmt = $db->prepare("
        SELECT COUNT(*) as total_achievements
        FROM user_achievements 
        WHERE user_id = ?
    ");
    $stmt->execute([$userId]);
    $achievementData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $progressData['achievements'] = [
        'earned' => (int)$achievementData['total_achievements'],
        'total' => 20 // Assuming 20 total possible achievements
    ];

    // Calculate profile completeness
    $profileFields = [
        'username' => !empty($currentUser['username']),
        'email' => !empty($currentUser['email']),
        'profile_picture' => !empty($currentUser['profile_picture']),
        'bio' => !empty($currentUser['bio']),
        'first_name' => !empty($currentUser['first_name']),
        'last_name' => !empty($currentUser['last_name'])
    ];
    
    $completedFields = array_sum($profileFields);
    $totalFields = count($profileFields);
    $profilePercentage = round(($completedFields / $totalFields) * 100);
    
    $progressData['profile'] = [
        'completeness' => $profilePercentage,
        'completed_fields' => $completedFields,
        'total_fields' => $totalFields
    ];

    // Get recent activity/stats
    $stmt = $db->prepare("
        SELECT 
            (SELECT COUNT(*) FROM user_quiz_attempts WHERE user_id = ?) as quiz_attempts,
            (SELECT COUNT(*) FROM user_challenge_attempts WHERE user_id = ?) as challenge_attempts,
            (SELECT COUNT(*) FROM user_mini_game_attempts WHERE user_id = ?) as minigame_attempts
    ");
    $stmt->execute([$userId, $userId, $userId]);
    $activityData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $progressData['activity'] = [
        'quiz_attempts' => (int)$activityData['quiz_attempts'],
        'challenge_attempts' => (int)$activityData['challenge_attempts'],
        'minigame_attempts' => (int)$activityData['minigame_attempts']
    ];

    echo json_encode(['success' => true, 'data' => $progressData]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error', 'details' => $e->getMessage()]);
}
