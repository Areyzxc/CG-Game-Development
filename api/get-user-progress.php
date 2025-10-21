<?php
// api/get-user-progress.php
// Returns user progress data for the dashboard

// Error reporting configuration
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors to the user
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../../logs/php_errors.log');

// Set JSON content type
header('Content-Type: application/json');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include required files
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/Auth.php';
require_once __DIR__ . '/../includes/CSRFProtection.php';

/**
 * Sends a JSON response and terminates the script
 * 
 * @param mixed $data The data to encode as JSON
 * @param int $statusCode HTTP status code (default: 200)
 */
function sendJsonResponse($data, $statusCode = 200) {
    $responseData = [
        'success' => $statusCode < 400,
        'data' => $data
    ];
    
    if ($statusCode >= 400) {
        if (is_string($data)) {
            $responseData['error'] = $data;
            unset($responseData['data']);
        } else if (is_array($data) && isset($data['error'])) {
            $responseData['error'] = $data['error'];
        }
    }
    
    $jsonResponse = json_encode($responseData, JSON_PRETTY_PRINT);
    if ($jsonResponse === false) {
        $jsonError = json_last_error_msg();
        error_log("JSON encode error: " . $jsonError);
        // Fallback to simple response if JSON encoding fails
        http_response_code(500);
        header('Content-Type: application/json');
        echo '{"success":false,"error":"Error encoding response"}';
        exit;
    }
    
    error_log("Sending response: " . $jsonResponse);
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo $jsonResponse;
    exit;
}

/**
 * Logs debug messages to a file
 * 
 * @param string $message The message to log
 * @param bool $includeBacktrace Whether to include a backtrace in the log
 */
function debug_log($message, $includeBacktrace = false) {
    $logFile = __DIR__ . '/../../logs/debug_user_progress.log';
    $logDir = dirname($logFile);
    
    // Create logs directory if it doesn't exist
    if (!file_exists($logDir)) {
        @mkdir($logDir, 0777, true);
    }
    
    $logMessage = '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;
    if ($includeBacktrace) {
        $logMessage .= 'Backtrace: ' . print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true) . PHP_EOL;
    }
    
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

// Alias for backward compatibility
function safeLog($message, $data = null) {
    debug_log($message, $data !== null);
    if ($data !== null) {
        debug_log('Data: ' . print_r($data, true));
    }
}

// Main execution
try {
    // Initialize required classes
    $auth = Auth::getInstance();
    $db = Database::getInstance()->getConnection();
    $csrf = CSRFProtection::getInstance();

    // Initialize debug logging
    file_put_contents(__DIR__ . '/../../logs/debug_user_progress.log', "");
    debug_log("=== Starting request ===");
    debug_log("Request URI: " . ($_SERVER['REQUEST_URI'] ?? 'N/A'));
    debug_log("Request method: " . ($_SERVER['REQUEST_METHOD'] ?? 'N/A'));

    // Verify CSRF token if this is a POST request
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!$csrf->validateToken()) {
            throw new Exception('Invalid CSRF token', 403);
        }
    }
    
    // Check if user is logged in
    if (!$auth->isLoggedIn()) {
        throw new Exception('Authentication required', 401);
    }

    $currentUser = $auth->getCurrentUser();
    $currentUserId = $currentUser['id'] ?? null;
    $isAdmin = $auth->isAdmin();
    
    // Get the target user ID from the request, default to current user if not provided or not admin
    $userId = $currentUserId;
    if ($isAdmin && !empty($_GET['user_id'])) {
        $userId = (int)$_GET['user_id'];
    }

    if (!$userId) {
        throw new Exception('Invalid user ID', 400);
    }

    // Get user progress data
    $stmt = $db->prepare("SELECT * FROM user_progress WHERE user_id = ?");
    $stmt->execute([$userId]);
    $progress = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Process progress data
    foreach ($progress as &$item) {
        if (isset($item['status'])) {
            if ($item['status'] === 'done_reading' || $item['status'] === 'completed') {
                $item['progress_percent'] = 100;
            } elseif ($item['status'] === 'in_progress') {
                $item['progress_percent'] = 50;
            } else {
                $item['progress_percent'] = 0;
            }
        }
    }
    
    // Get user's completed tutorials count
    $tutorialsQuery = "SELECT COUNT(DISTINCT topic_id) FROM user_progress 
                      WHERE user_id = ? AND status IN ('completed', 'done_reading')";
    $tutorialsStmt = $db->prepare($tutorialsQuery);
    $tutorialsStmt->execute([$userId]);
    $tutorialsCompleted = (int)$tutorialsStmt->fetchColumn();
    
    // Get total available tutorials
    $totalTutorialsQuery = "SELECT COUNT(DISTINCT id) FROM tutorial_topics WHERE is_active = 1";
    $totalTutorials = (int)$db->query($totalTutorialsQuery)->fetchColumn();
    
    // Get user's profile picture
    $defaultProfilePic = '/assets/images/default-avatar.png';
    $profilePicture = $currentUser['profile_picture'] ?? $defaultProfilePic;
    
    // Process profile picture URL
    if ($profilePicture !== $defaultProfilePic) {
        $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
        $avatarPath = '/CodeGaming/uploads/avatars/' . basename($profilePicture);
        $profilePicture = $baseUrl . $avatarPath;
    }
    
    // Get user's banner
    $defaultBanner = '/assets/images/default-banner.jpg';
    $bannerUrl = $currentUser['header_banner'] ?? $defaultBanner;
    
    // Process banner URL
    if ($bannerUrl !== $defaultBanner) {
        $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
        $bannerPath = '/CodeGaming/uploads/banners/' . basename($bannerUrl);
        $bannerUrl = $baseUrl . $bannerPath;
    }
    
    // Get achievements count
    $stmt = $db->prepare("SELECT COUNT(*) as total FROM user_achievements WHERE user_id = ?");
    $stmt->execute([$userId]);
    $achievements = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Calculate progress metrics
    $totalTopics = count($progress);
    $completedTopics = count(array_filter($progress, function($p) { 
        return $p['status'] === 'done_reading'; 
    }));
    $inProgressTopics = count(array_filter($progress, function($p) { 
        return $p['status'] === 'currently_reading'; 
    }));
    
    // Prepare response
    $response = [
        'success' => true,
        'data' => [
            'user' => [
                'id' => $userId,
                'username' => $currentUser['username'] ?? 'User',
                'profile_picture' => $profilePicture,
                'header_banner' => $bannerUrl,
                'is_admin' => $isAdmin
            ],
            'progress' => $progress,
            'stats' => [
                'total_topics' => $totalTopics,
                'completed_topics' => $completedTopics,
                'in_progress_topics' => $inProgressTopics,
                'achievements_earned' => (int)($achievements['total'] ?? 0),
                'achievements_total' => 20 // Assuming 20 total possible achievements
            ]
        ]
    ];
    
    sendJsonResponse($response);
    
} catch (Exception $e) {
    error_log('Error in get-user-progress.php: ' . $e->getMessage() . '\n' . $e->getTraceAsString());
    sendJsonResponse([
        'success' => false, 
        'error' => 'An error occurred while fetching user progress',
        'details' => $e->getMessage()
    ], 500);
}

// Set error handling before any output
function handleShutdown() {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => 'A fatal error occurred',
            'details' => $error['message'] . ' in ' . $error['file'] . ' on line ' . $error['line']
        ]);
    }
}

// Initialize shutdown handler
register_shutdown_function('handleShutdown');

// Check for user_progress table with exact case
$db = Database::getInstance()->getConnection();
$tableExists = $db->query("SHOW TABLES LIKE 'user_progress'")->rowCount() > 0;
debug_log("Table 'user_progress' exists: " . ($tableExists ? 'Yes' : 'No'));

// Test database connection
    debug_log("Testing database connection...");
    $dbName = $db->query('SELECT DATABASE()')->fetchColumn();
    debug_log("Current database: " . $dbName);
    
    // Get all tables
    debug_log("Fetching all tables...");
    $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    debug_log("All tables in database: " . print_r($tables, true));
    
    // Check for user_progress table with exact case
    $tableExists = $db->query("SHOW TABLES LIKE 'user_progress'")->rowCount() > 0;
    debug_log("Table 'user_progress' exists: " . ($tableExists ? 'Yes' : 'No'));
    
    // Find all progress-related tables
    $progressTables = [];
    foreach ($tables as $table) {
        if (stripos($table, 'progress') !== false) {
            $progressTables[] = $table;
        }
    }
    // Log the tables we found
    error_log("Tables containing 'progress': " . print_r($progressTables, true));
    
    // If we found matching tables, log their structure
    foreach ($progressTables as $table) {
        try {
            $columns = $db->query("SHOW COLUMNS FROM `$table`")->fetchAll(PDO::FETCH_COLUMN);
            error_log("Columns in $table: " . print_r($columns, true));
        } catch (Exception $e) {
            error_log("Error getting columns for $table: " . $e->getMessage());
        }
    }

    // Get user ID from session
    $userId = $_SESSION['user_id'] ?? null;
    
    if (!$userId) {
        throw new Exception('User not authenticated');
    }
    
    // Your existing code to fetch user progress would go here
    // For example:
    $stmt = $db->prepare('SELECT * FROM user_progress WHERE user_id = ?');
    $stmt->execute([$userId]);
    $progressData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Send the response
    sendJsonResponse([
        'success' => true,
        'data' => $progressData
    ]);
    
    // Try to get token from headers (case-insensitive)
    foreach ($headers as $key => $value) {
        if (strtolower($key) === 'x-csrf-token') {
            $csrfToken = $value;
            safeLog('Found CSRF token in header: ' . substr($csrfToken, 0, 5) . '...');
            break;
        }
    }
    
    // Fall back to GET/POST parameters if not in header
    if (empty($csrfToken)) {
        $csrfToken = $_GET['csrf_token'] ?? $_POST['csrf_token'] ?? '';
        if ($csrfToken) {
            safeLog('Found CSRF token in request parameters: ' . substr($csrfToken, 0, 5) . '...');
        }
    }
    
    if (empty($csrfToken)) {
        safeLog('No CSRF token found in request');
        http_response_code(403);
        echo json_encode([
            'error' => 'CSRF token is required',
            'token_received' => 'no',
            'session_id' => session_id(),
            'request_headers' => array_keys($headers)
        ]);
        exit;
    }
    
    // Validate the token
    $isValid = $csrf->validateToken($csrfToken);
    safeLog('CSRF Validation Result: ' . ($isValid ? 'VALID' : 'INVALID'));
    
    if (!$isValid) {
        $errorDetails = [
            'error' => 'Invalid CSRF token',
            'token_received' => 'yes',
            'token_preview' => substr($csrfToken, 0, 5) . '...',
            'session_id' => session_id(),
            'request_method' => $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN',
            'session_active' => session_status() === PHP_SESSION_ACTIVE ? 'yes' : 'no'
        ];
        
        safeLog('CSRF Validation Failed', $errorDetails);
        
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode($errorDetails);
        exit;
    }
    
    safeLog('CSRF token validated successfully');

    if (!$auth->isLoggedIn()) {
        http_response_code(401);
        echo json_encode(['error' => 'Not authenticated']);
        exit;
    }

    $currentUser = $auth->getCurrentUser();
    $currentUserId = $currentUser['id'] ?? null;
    $isAdmin = $auth->isAdmin();
    
    // Get the target user ID from the request, default to current user if not provided or not admin
    $requestedUserId = $_GET['user_id'] ?? null;
    
    // If no user ID is provided and the current user is not an admin, use their own ID
    if (empty($requestedUserId) && !$isAdmin) {
        $userId = $currentUserId;
    } 
    // If the current user is an admin and a user ID is provided, use that
    else if ($isAdmin && !empty($requestedUserId)) {
        $userId = $requestedUserId;
    }
    // Otherwise, use the current user's ID
    else {
        $userId = $currentUserId;
    }

    if (!$userId) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid user']);
        exit;
    }

    // Get user progress from database
    $stmt = $db->prepare("SELECT 
        p.*, 
        CASE 
            WHEN p.status = 'done_reading' THEN 100
            WHEN p.status = 'currently_reading' THEN 50
            ELSE p.progress 
        END as calculated_progress
    FROM user_progress p 
    WHERE p.user_id = ?");
    $stmt->execute([$userId]);
    $progress = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
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

    // Initialize tutorials data with default values
    $tutorialsCompleted = 0;
    $totalTutorials = 0;
    $tutorialProgressPercentage = 0;

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

    // Get user's points from quizzes and challenges
    $pointsQuery = $db->prepare("
        SELECT (
            SELECT COALESCE(SUM(points_earned), 0) 
            FROM user_quiz_attempts 
            WHERE user_id = :user_id1
        ) + (
            SELECT COALESCE(SUM(points_earned), 0)
            FROM user_challenge_attempts 
            WHERE user_id = :user_id2
        ) as total_points
    ");
    $pointsQuery->execute([
        ':user_id1' => $userId,
        ':user_id2' => $userId
    ]);
    $points = $pointsQuery->fetch(PDO::FETCH_COLUMN) ?: 0;

    // Get challenges completed (distinct correct attempts)
    $challengesQuery = $db->prepare("
        SELECT COUNT(DISTINCT question_id) 
        FROM user_challenge_attempts 
        WHERE user_id = :user_id AND is_correct = 1
    ");
    $challengesQuery->execute([':user_id' => $userId]);
    $challengesCompleted = (int)$challengesQuery->fetch(PDO::FETCH_COLUMN);

    // Get total available challenges
    $totalChallengesQuery = $db->prepare("
        SELECT COUNT(*) 
        FROM code_challenges 
        WHERE difficulty = 'expert'
    ");
    $totalChallengesQuery->execute();
    $totalChallenges = (int)$totalChallengesQuery->fetch(PDO::FETCH_COLUMN);

    // Get quizzes passed (distinct correct attempts)
    $quizzesQuery = $db->prepare("
        SELECT COUNT(DISTINCT question_id) 
        FROM user_quiz_attempts 
        WHERE user_id = :user_id AND is_correct = 1
    ");
    $quizzesQuery->execute([':user_id' => $userId]);
    $quizzesPassed = (int)$quizzesQuery->fetch(PDO::FETCH_COLUMN);

    // Get total available quiz questions
    $totalQuizzesQuery = $db->prepare("SELECT COUNT(*) FROM quiz_questions WHERE is_active = 1");
    $totalQuizzesQuery->execute();
    $totalQuizzes = (int)$totalQuizzesQuery->fetch(PDO::FETCH_COLUMN);

    // Get mini-games completed (distinct game types with correct attempts)
    $gamesQuery = $db->prepare("
        SELECT COUNT(DISTINCT mode_key) 
        FROM user_mini_game_attempts 
        WHERE user_id = :user_id AND is_correct = 1
    ");
    $gamesQuery->execute([':user_id' => $userId]);
    $miniGamesCompleted = (int)$gamesQuery->fetch(PDO::FETCH_COLUMN);

    // Get total available mini-games
    $totalGamesQuery = $db->prepare("SELECT COUNT(*) FROM mini_game_modes WHERE is_active = 1");
    $totalGamesQuery->execute();
    $totalGames = (int)$totalGamesQuery->fetch(PDO::FETCH_COLUMN);

    // Get total available quiz questions
    $totalQuizzesQuery = $db->prepare("SELECT COUNT(*) FROM quiz_questions WHERE is_active = 1");
    $totalQuizzesQuery->execute();
    $totalQuizzes = (int)$totalQuizzesQuery->fetch(PDO::FETCH_COLUMN);

// Get mini-games completed (distinct game types with correct attempts)
try {
    $sql = "SELECT COUNT(DISTINCT mode_key) FROM user_mini_game_attempts WHERE user_id = :user_id AND is_correct = 1";
    debug_log("Executing mini-games query: " . $sql);
    debug_log("With params: " . print_r([':user_id' => $userId], true));
    
    $gamesQuery = $db->prepare($sql);
    if ($gamesQuery->execute([':user_id' => $userId])) {
        $miniGamesCompleted = (int)$gamesQuery->fetch(PDO::FETCH_COLUMN);
        debug_log("Mini-games completed: " . $miniGamesCompleted);
    } else {
        $error = $gamesQuery->errorInfo();
        debug_log("Error in mini-games query: " . print_r($error, true));
        $miniGamesCompleted = 0;
    }
} catch (Exception $e) {
    debug_log("Exception in mini-games query: " . $e->getMessage());
    $miniGamesCompleted = 0;
}

// Initialize variables
$tutorialsCompleted = 0;
$totalTutorials = 1; // Default to 1 to avoid division by zero

// Get total number of unique tutorial topics
try {
    $sql = "SELECT COUNT(DISTINCT `topic_id`) as total FROM `user_progress`";
    debug_log("Executing total tutorials query: " . $sql);
    
    $totalStmt = $db->prepare($sql);
    if ($totalStmt->execute()) {
        $totalResult = $totalStmt->fetch(PDO::FETCH_ASSOC);
        $totalTutorials = max(1, (int)($totalResult['total'] ?? 1));
        debug_log("Total tutorials: " . $totalTutorials);
    } else {
        $error = $totalStmt->errorInfo();
        debug_log("Error in total tutorials query: " . print_r($error, true));
    }
} catch (Exception $e) {
    error_log("Exception in total tutorials query: " . $e->getMessage());
} catch (Exception $e) {
    error_log('Unhandled exception in get-user-progress.php: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'An internal server error occurred']);
    exit;
}

// Get completed tutorials for the user
    // Get database connection
    $db = Database::getInstance()->getConnection();
    
    // First, list all tables in the database
    $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    error_log("Available tables: " . print_r($tables, true));
    
    // Look for tables that might contain progress data
    $possibleTables = [
        'user_progress',
        'userprogress',
        'progress',
        'user_progresses',
        'tutorial_progress',
        'user_tutorials'
    ];
    
    $foundTable = null;
    $statusColumn = null;
    $columns = [];
    
    // Check each possible table
    foreach ($possibleTables as $table) {
        if (in_array($table, $tables)) {
            try {
                $checkColumns = $db->query("SHOW COLUMNS FROM `$table`");
                $cols = $checkColumns->fetchAll(PDO::FETCH_ASSOC);
                $columnNames = array_column($cols, 'Field');
                
                // Look for any column that might indicate status
                $possibleStatusColumns = ['status', 'progress_status', 'state', 'completion_status', 'is_completed'];
                
                foreach ($cols as $col) {
                    $colName = strtolower($col['Field']);
                    
                    // Check if this column name contains any of our status indicators
                    foreach ($possibleStatusColumns as $possible) {
                        if (strpos($colName, $possible) !== false) {
                            $foundTable = $table;
                            $statusColumn = $col['Field']; // Preserve original case
                            $statusType = $col['Type'];
                            $columns = $columnNames;
                            
                            // Log what we found
                            error_log("Found potential status column: $statusColumn ($statusType) in table: $foundTable");
                            
                            // If it's an ENUM, log the possible values
                            if (strpos(strtolower($statusType), 'enum') === 0) {
                                error_log("Column $statusColumn is an ENUM with possible values: $statusType");
                            }
                            
                            break 3; // Exit all loops
                        }
                    }
                }
            } catch (Exception $e) {
                // Table might not exist, continue to next
                continue;
            }
        }
    }
    
    if (!$foundTable || !$statusColumn) {
        throw new Exception("Could not find a valid progress table. Available tables: " . implode(', ', $tables));
    }
    
    error_log("Using table: $foundTable, status column: $statusColumn");
    error_log("Columns in $foundTable: " . print_r($columns, true));
    
    // First, try to find out what values are in the status column
    $sampleQuery = "SELECT DISTINCT `$statusColumn` as status_value, COUNT(*) as count 
                   FROM `$foundTable` 
                   GROUP BY `$statusColumn` 
                   LIMIT 10";
    
    try {
        $sampleResult = $db->query($sampleQuery)->fetchAll(PDO::FETCH_ASSOC);
        error_log("Sample status values in $foundTable.$statusColumn: " . print_r($sampleResult, true));
    } catch (Exception $e) {
        error_log("Could not get sample status values: " . $e->getMessage());
    }
    
    // Build the query with the found table and column names
    // Try different possible status values that might indicate completion
    $possibleCompleteValues = ['done', 'complete', 'completed', 'finished', '1', 'true'];
    $statusConditions = [];
    
    foreach ($possibleCompleteValues as $value) {
        $statusConditions[] = "`$statusColumn` = '" . addslashes($value) . "'";
    }
    
    // Also check for numeric/boolean values
    $statusConditions[] = "`$statusColumn` = 1";
    $statusConditions[] = "`$statusColumn` = true";
    
    $statusCondition = "(" . implode(" OR ", $statusConditions) . ")";
    
    $sql = "SELECT COUNT(DISTINCT `topic_id`) as completed 
            FROM `$foundTable` 
            WHERE $statusCondition AND `user_id` = ?";
    
    error_log("Executing query: " . $sql);
    
    try {
        // Execute the query
        $stmt = $db->prepare($sql);
        if (!$stmt->execute([$userId])) {
            $error = $stmt->errorInfo();
            throw new Exception("Query failed: " . ($error[2] ?? 'Unknown error'));
        }
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $tutorialsCompleted = (int)($result['completed'] ?? 0);
        
        // Log the result
        error_log("User $userId has completed $tutorialsCompleted tutorials");
        
        // Verify the result makes sense
        if ($tutorialsCompleted < 0) {
            $tutorialsCompleted = 0;
        }
    } catch (Exception $e) {
        error_log("Error in tutorial progress query: " . $e->getMessage());
        // Set to 0 if there's an error to prevent breaking the page
        $tutorialsCompleted = 0;
    }
// Calculate tutorial progress percentage
$tutorialProgressPercentage = $totalTutorials > 0 ? 
    round(($tutorialsCompleted / $totalTutorials) * 100) : 0;

    // Calculate overall progress (weighted average)
    $overallProgress = min(100,
        ($quizzesPassed * 2) + 
        ($miniGamesCompleted * 10) + 
        ($tutorialProgressPercentage * 0.3)
    );

// Prepare the response in a format that matches the profile page
$responseData = [
    // Overall stats
    'points' => $points,
    'challenges_completed' => $challengesCompleted,
    'quizzes_passed' => $quizzesPassed,
    'mini_games_completed' => $miniGamesCompleted,
    'tutorials_completed' => $tutorialsCompleted,
    'total_topics' => $totalTutorials,
    'tutorial_progress_percentage' => $tutorialProgressPercentage,
    'overall_progress' => $overallProgress,
    'last_week_progress' => min(100, $overallProgress * 0.8), // Example calculation
    'last_month_progress' => min(100, $overallProgress * 0.9), // Example calculation
    
    // Detailed progress by category (for the admin panel)
    'progress_by_category' => [
        'tutorials' => [
            'completed' => $tutorialsCompleted,
            'total' => $totalTutorials,
            'percentage' => $tutorialProgressPercentage
        ],
        'quizzes' => [
            'completed' => $quizzesPassed,
            'total' => $totalQuizzes,
            'percentage' => $totalQuizzes > 0 ? min(100, round(($quizzesPassed / $totalQuizzes) * 100)) : 0
        ],
        'challenges' => [
            'completed' => $challengesCompleted,
            'total' => $totalChallenges,
            'percentage' => $totalChallenges > 0 ? min(100, round(($challengesCompleted / $totalChallenges) * 100)) : 0
        ],
        'miniGames' => [
            'completed' => $miniGamesCompleted,
            'total' => $totalGames > 0 ? $totalGames : 2,
            'percentage' => $totalGames > 0 ? min(100, round(($miniGamesCompleted / $totalGames) * 100)) : 0
        ]
    ]
];

    // Add debugging info to response
    $responseData['debug'] = [
        'user_id' => $userId,
        'tutorials_completed' => $tutorialsCompleted,
        'total_tutorials' => $totalTutorials,
        'tutorial_progress' => $tutorialProgressPercentage,
        'overall_progress' => $overallProgress,
        'last_week_progress' => min(100, $overallProgress * 0.8),
        'last_month_progress' => min(100, $overallProgress * 0.9),
        'server_time' => date('Y-m-d H:i:s')
    ];

    try {
        // Encode the response data
        $response = json_encode([
            'success' => true,
            'data' => $responseData
        ]);

        if ($response === false) {
            throw new Exception('Failed to encode response data: ' . json_last_error_msg());
        }

        // Output the response
        if (!headers_sent()) {
            header('Content-Type: application/json');
        }
        echo $response;
    } catch (Exception $e) {
        // Log the error
        error_log('Error in get-user-progress.php: ' . $e->getMessage());
        
        // Send error response
        if (!headers_sent()) {
            http_response_code(500);
            header('Content-Type: application/json');
        }
        
        $errorResponse = [
            'success' => false,
            'error' => 'An error occurred while processing your request',
            'message' => $e->getMessage()
        ];
        
        if (ini_get('display_errors')) {
            $errorResponse['trace'] = $e->getTraceAsString();
        }
        
        echo json_encode($errorResponse);
    exit;
}?>
