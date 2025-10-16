<?php
// Include visitor tracking
require_once 'includes/track_visitor.php';

/**
 * ==========================================================
 * File: mini-game.php
 * 
 * Description:
 *   - Enhanced Mini-Game page for Code Gaming platform
 *   - Features:
 *       • Simple screen-based game flow (following challenges.js pattern)
 *       • Interactive card-based mode selection with direct start buttons
 *       • Language and difficulty selection
 *       • Real-time feedback and performance stats
 *       • Leaderboard sidebar for all game types
 *       • AJAX-based challenge loading and result submission
 *       • Responsive, modern UI with enhanced animations
 * 
 * Usage:
 *   - Accessible to all users and guests
 *   - Allows users to play interactive coding mini-games
 * 
 * Author: [Santiago] - Enhanced by Cascade AI
 * Last Updated: [September 29, 2025]
 * -- Code Gaming Team --
 * ==========================================================
 */

require_once 'includes/Database.php';
require_once 'includes/Auth.php';
require_once 'includes/ErrorHandler.php';
require_once 'includes/CSRFProtection.php';
require_once 'includes/XSSProtection.php';

$db = Database::getInstance();
$auth = Auth::getInstance();
$isLoggedIn = $auth->isLoggedIn();
$currentUser = $isLoggedIn ? $auth->getCurrentUser() : null;

// Define available languages
$languages = [
    'html' => 'HTML',
    'css' => 'CSS',
    'javascript' => 'JavaScript',
    'bootstrap' => 'Bootstrap',
    'java' => 'Java',
    'python' => 'Python',
    'cpp' => 'C++'
];

// Fetch game modes from database
try {
    $stmt = $db->prepare("SELECT * FROM mini_game_modes WHERE is_active = 1 ORDER BY id");
    $stmt->execute();
    $gameTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Fallback to hardcoded game types if database fails
    $gameTypes = [
        [
            'mode_key' => 'guess', 
            'name' => 'Guess the Output', 
            'description' => 'Test your code comprehension by predicting outputs', 
            'icon' => 'fas fa-search',
            'instructions' => '["1. A code snippet will be displayed", "2. Analyze the code carefully", "3. Type what you think the output will be", "4. Submit your answer to see if you\'re correct", "5. Learn from explanations for each answer"]'
        ],
        [
            'mode_key' => 'typing', 
            'name' => 'Fast Code Typing', 
            'description' => 'Improve your coding speed and accuracy', 
            'icon' => 'fas fa-keyboard',
            'instructions' => '["1. A code snippet will appear on screen", "2. Click \'Start Challenge\' to begin", "3. Type the code exactly as shown", "4. Complete before time runs out", "5. Achieve high WPM (Words Per Minute) scores"]'
        ]
    ];
}

// Define difficulty levels
$difficulties = [
    'beginner' => 'Beginner',
    'intermediate' => 'Intermediate',
    'expert' => 'Expert'
];

// Set page-specific variables for header
$pageTitle = 'Mini-Game Arena';
$additionalStyles = '<link href="assets/css/mini-game.css" rel="stylesheet">';

// Include header
include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <!-- Main Game Content -->
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="mb-0">Mini-Game Arena</h1>
                <button class="btn btn-outline-light btn-sm" onclick="showNotification('Welcome to Mini-Game Arena! Choose a game mode below to get started.', 'info')">
                    <i class="fas fa-info-circle me-2"></i>Help
                </button>
            </div>
            
            <!-- Mini-Game Welcome Screen -->
            <div class="mini-game-screen active" id="mini-game-welcome">
                <div class="welcome-content text-center">
                    <div class="welcome-header mb-4">
                        <h2>🎮 Choose Your Challenge</h2>
                        <p class="text-muted">Test your coding skills with interactive mini-games</p>
                    </div>
                    
                    <!-- Game Mode Selection -->
                    <div class="row mb-4">
                        <?php foreach ($gameTypes as $gameType): ?>
                        <div class="col-md-6 mb-3">
                            <div class="game-mode-card h-100" 
                                 data-mode="<?php echo htmlspecialchars($gameType['mode_key']); ?>"
                                 data-name="<?php echo htmlspecialchars($gameType['name']); ?>"
                                 data-description="<?php echo htmlspecialchars($gameType['description']); ?>">
                                <div class="card-body text-center">
                                    <div class="game-mode-icon mb-3">
                                        <i class="<?php echo htmlspecialchars($gameType['icon']); ?> fa-3x"></i>
                                    </div>
                                    <h5 class="card-title"><?php echo htmlspecialchars($gameType['name']); ?></h5>
                                    <p class="card-text text-light"><?php echo htmlspecialchars($gameType['description']); ?></p>
                                    <button class="btn btn-primary btn-start-mode" 
                                            data-mode="<?php echo htmlspecialchars($gameType['mode_key']); ?>">
                                        <span class="btn-text">START <?php echo strtoupper($gameType['name']); ?></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Settings -->
                    <div class="game-settings mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="languageSelect" class="form-label">Language:</label>
                                <select class="form-select" id="languageSelect">
                                    <option value="javascript">JavaScript</option>
                                    <option value="python">Python</option>
                                    <option value="java">Java</option>
                                    <option value="cpp">C++</option>
                                    <option value="css">CSS</option>
                                    <option value="html">HTML</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="difficultySelect" class="form-label">Difficulty:</label>
                                <select class="form-select" id="difficultySelect">
                                    <option value="beginner">Beginner</option>
                                    <option value="intermediate">Intermediate</option>
                                    <option value="expert">Expert</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Mini-Game Progress Screen -->
            <div class="mini-game-screen" id="mini-game-progress">
                <div class="game-header d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 id="current-game-title">Game Mode</h4>
                        <div class="game-stats">
                            <span class="badge bg-primary me-2">Score: <span id="current-score">0</span></span>
                            <span class="badge bg-success me-2">Streak: <span id="current-streak">0</span></span>
                            <span class="badge bg-info">Language: <span id="current-language">JavaScript</span></span>
                        </div>
                    </div>
                    <button class="btn btn-outline-light btn-sm btn-back-menu">
                        <i class="fas fa-arrow-left me-2"></i>Back to Menu
                    </button>
                </div>
                
                <!-- Guess the Output Game -->
                <div class="game-container d-none" id="guess-content">
                    <div class="card bg-secondary text-light">
                        <div class="card-body">
                            <div id="guessCodeDisplay" class="code-display"></div>
                            <div class="mb-3">
                                <label for="guessAnswer" class="form-label">What will be the output?</label>
                                <input type="text" class="form-control bg-dark text-light" id="guessAnswer" placeholder="Enter your answer">
                            </div>
                            <div class="d-flex justify-content-end">
                                <button class="btn btn-primary" id="checkGuessBtn">Check Answer</button>
                            </div>
                            <div id="guessFeedback" class="result-feedback mt-3" style="display: none;">
                                <div class="feedback-content">
                                    <div class="feedback-message"></div>
                                    <div class="correct-answer" style="display: none;">
                                        <strong>Correct Answer:</strong>
                                        <pre class="mt-2"></pre>
                                    </div>
                                </div>
                                <button class="dismiss-btn" title="Dismiss feedback">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Typing Speed Game -->
                <div class="game-container d-none" id="typing-content">
                    <div class="card bg-secondary text-light">
                        <div class="card-body">
                            <div id="typingCodeDisplay" class="code-display"></div>
                            <div class="mb-3">
                                <label for="typingInput" class="form-label">Type the code above:</label>
                                <textarea class="form-control bg-dark text-light typing-input" 
                                        id="typingInput" 
                                        rows="6" 
                                        placeholder="Start typing here..."></textarea>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="game-stats">
                                    <span class="me-3">Time: <span id="typingTimer">0</span>s</span>
                                    <span class="me-3">WPM: <span id="typingWPM">0</span></span>
                                    <span>Accuracy: <span id="typingAccuracy">100</span>%</span>
                                </div>
                                <div class="btn-group">
                                    <button class="btn btn-primary" id="startTypingBtn">Start Challenge</button>
                                    <button class="btn btn-success" id="submitTypingBtn" style="display: none;">
                                        <i class="fas fa-check me-1"></i>Submit
                                    </button>
                                </div>
                            </div>
                            <div id="typingFeedback" class="result-feedback mt-3" style="display: none;">
                                <div class="feedback-content">
                                    <div class="feedback-message"></div>
                                    <div class="correct-answer" style="display: none;">
                                        <strong>Your Performance:</strong>
                                        <div class="performance-stats mt-2"></div>
                                    </div>
                                </div>
                                <button class="dismiss-btn" title="Dismiss feedback">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Leaderboard Sidebar -->
        <div class="col-lg-4">
            <div class="leaderboard-container">
                <div class="card bg-secondary text-light leaderboard-card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Leaderboard</h5>
                            <button class="btn btn-outline-light btn-sm" id="reloadLeaderboardBtn" title="Refresh Leaderboard">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                        <div class="game-mode-selectors mt-2">
                            <button class="game-mode-selector active" data-game-type="guess">Guess</button>
                            <button class="game-mode-selector" data-game-type="typing">Typing</button>
                        </div>
                        <div class="time-scope-selectors mt-2">
                            <button class="time-scope-selector active" data-scope="alltime">All-Time</button>
                            <button class="time-scope-selector" data-scope="weekly">Weekly</button>
                            <button class="time-scope-selector" data-scope="monthly">Monthly</button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <ul class="leaderboard-list" id="leaderboardList">
                            <!-- Leaderboard items will be populated by JavaScript -->
                        </ul>
                    </div>
                    <div class="card-footer bg-transparent border-secondary">
                        <div class="leaderboard-pagination" id="leaderboardPagination">
                            <!-- Pagination will be populated by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Exit Confirmation Modal -->
<div class="modal fade" id="exitConfirmModal" tabindex="-1" aria-labelledby="exitConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark text-light">
            <div class="modal-header border-secondary">
                <h5 class="modal-title" id="exitConfirmModalLabel">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                    Confirm Exit
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">Are you sure you want to exit?</p>
                <div class="alert alert-info">
                    <small><i class="fas fa-info-circle me-1"></i>Your session progress will be saved to the leaderboard before exiting, you can continue playing if you want.</small>
                </div>
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>No, Continue Playing
                </button>
                <button type="button" class="btn btn-success" id="confirmExitBtn" style="background-color: #1e7e34; border-color: #1e7e34;">
                    <i class="fas fa-save me-1"></i>Yes, save progress and exit the game
                </button>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include 'includes/footer.php';
?>

<!-- Mini-Game Specific Scripts -->
<script>
    // Test basic JavaScript
    console.log('🧪 Basic JavaScript working in mini-game.php');
    
    // Global variables for JavaScript
    window.CG_USER_ID = <?php echo $auth->isLoggedIn() ? $auth->getCurrentUser()['id'] : 'null'; ?>;
    window.CG_USERNAME = <?php echo $auth->isLoggedIn() ? json_encode($auth->getCurrentUser()['username']) : 'null'; ?>;
    window.CG_IS_LOGGED_IN = <?php echo $auth->isLoggedIn() ? 'true' : 'false'; ?>;
    window.CG_NICKNAME = <?php echo isset($_SESSION['guest_nickname']) ? json_encode($_SESSION['guest_nickname']) : 'null'; ?>;
    
    console.log('🔍 User variables set:', {
        CG_USER_ID: window.CG_USER_ID,
        CG_USERNAME: window.CG_USERNAME,
        CG_IS_LOGGED_IN: window.CG_IS_LOGGED_IN
    });
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/highlight.min.js"></script>
<script src="assets/js/mini-game-simple.js"></script>
