<?php
/**
 * ==========================================================
 * File: mini-game.php
 * 
 * Description:
 *   - Mini-Game page for Code Gaming platform
 *   - Features:
 *       • Two game modes: Guess the Output & Typing Speed Challenge (other game modes can be added)
 *       • Language and difficulty selection (HTML, CSS, JS, Python, Java, C++, and Bootstrap)
 *       • Interactive code display and input areas
 *       • Real-time feedback and performance stats
 *       • Leaderboard sidebar for both game types
 *       • Responsive, modern UI with pixel-art and highlight.js
 *       • Note: Further enhancements can be made to improve user experience
 * 
 * Usage:
 *   - Accessible to all users and guests
 *   - Allows users to play quick coding mini-games and compete for high scores
 * 
 * Author: [Santiago]
 * Last Updated: [July 22, 2025]
 * -- Code Gaming Team --
 * ==========================================================
 */

require_once 'includes/Database.php';
require_once 'includes/Auth.php';

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

// Define game types
$gameTypes = [
    'guess' => 'Guess the Output',
    'typing' => 'Typing Speed'
];

// Define difficulty levels
$difficulties = [
    'beginner' => 'Beginner',
    'intermediate' => 'Intermediate',
    'expert' => 'Expert'
];

// Set page-specific variables for header
$pageTitle = 'Mini-Game';
$includeAnnouncementBanner = true;
$additionalStyles = '<link href="assets/css/mini-game.css" rel="stylesheet">';

// Include header
include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <!-- Main Game Content -->
        <div class="col-lg-8">
            <h1 class="mb-4">Mini-Game</h1>
            
            <!-- Game Type Selection -->
            <ul class="nav nav-tabs mb-4" id="gameTabs" role="tablist">
                <?php foreach ($gameTypes as $type => $name): ?>
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?php echo $type === 'guess' ? 'active' : ''; ?>" 
                            id="<?php echo $type; ?>-tab" 
                            data-bs-toggle="tab" 
                            data-bs-target="#<?php echo $type; ?>-content" 
                            type="button" 
                            role="tab"
                            data-game-type="<?php echo $type; ?>">
                        <?php echo htmlspecialchars($name); ?>
                    </button>
                </li>
                <?php endforeach; ?>
            </ul>

            <!-- Language and Difficulty Selection -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="languageSelect" class="form-label">Select Language:</label>
                    <select class="form-select bg-dark text-light" id="languageSelect">
                        <?php foreach ($languages as $code => $name): ?>
                        <option value="<?php echo $code; ?>"><?php echo htmlspecialchars($name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="difficultySelect" class="form-label">Select Difficulty:</label>
                    <select class="form-select bg-dark text-light" id="difficultySelect">
                        <?php foreach ($difficulties as $code => $name): ?>
                        <option value="<?php echo $code; ?>"><?php echo htmlspecialchars($name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Game Content -->
            <div class="tab-content" id="gameTabContent">
                <!-- Guess the Output Game -->
                <div class="tab-pane fade show active" id="guess-content" role="tabpanel">
                    <div class="card bg-secondary text-light game-container">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Guess the Output</h5>
                            <div id="guessCodeDisplay" class="code-display"></div>
                            <div class="mb-3">
                                <label for="guessAnswer" class="form-label">What will be the output?</label>
                                <input type="text" class="form-control bg-dark text-light" id="guessAnswer" placeholder="Enter your answer">
                            </div>
                            <button class="btn btn-primary" id="checkGuessBtn">Check Answer</button>
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
                <div class="tab-pane fade" id="typing-content" role="tabpanel">
                    <div class="card bg-secondary text-light game-container">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Typing Speed Challenge</h5>
                            <div id="typingCodeDisplay" class="code-display"></div>
                            <div class="mb-3">
                                <label for="typingInput" class="form-label">Type the code above:</label>
                                <textarea class="form-control bg-dark text-light typing-input" 
                                        id="typingInput" 
                                        rows="6" 
                                        placeholder="Start typing here..."></textarea>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="me-3">Time: <span id="typingTimer">0</span>s</span>
                                    <span>WPM: <span id="typingWPM">0</span></span>
                                </div>
                                <button class="btn btn-primary" id="startTypingBtn">Start Challenge</button>
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
                        <h5 class="mb-0">Leaderboard</h5>
                        <div class="game-mode-selectors">
                            <button class="game-mode-selector active" data-game-type="guess">Guess</button>
                            <button class="game-mode-selector" data-game-type="typing">Typing</button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <ul class="leaderboard-list" id="leaderboardList">
                            <!-- Leaderboard items will be populated by JavaScript -->
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Set page-specific scripts
$additionalScripts = '
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/highlight.min.js"></script>
<script src="assets/js/mini-game.js"></script>';

// Include footer
include 'includes/footer.php';
?>