<?php
/**
 * ==========================================================
 * File: challenges.php
 * 
 * Description:
 *   - Expert Challenge page for Code Gaming platform
 *   - Features:
 *       • Interactive challenge mode with 20 expert-level questions
 *       • Multiple question types: fill-in-the-blank, output prediction, case study, code writing
 *       • Timer, score, and lives system
 *       • Dynamic feedback, instructions, and leaderboard modals
 *       • Guest nickname input and validation
 *       • Responsive, retro-inspired UI with Bootstrap and custom styles
 * 
 * Usage:
 *   - Accessible to all users and guests
 *   - Allows users to test programming skills and compete for high scores
 * 
 * Files Included:
 *   - assets/css/challenge-style.css
 *   - assets/js/challenge.js
 *   - includes/header.php, includes/footer.php
 *   - External: Bootstrap, Font Awesome, Google Fonts
 * 
 * Author: [Santiago]
 * Last Updated: [July 22, 2025]
 * -- Code Gaming Team --
 * ==========================================================
 */

// session_start(); // Removed to let Auth handle session
require_once 'includes/Database.php';
require_once 'includes/Auth.php';

// Check if user is logged in or guest
$isLoggedIn = isset($_SESSION['user_id']);
$userId = $isLoggedIn ? $_SESSION['user_id'] : null;
$username = $isLoggedIn ? $_SESSION['username'] : null;

// Ensure $auth is available for the header
$auth = Auth::getInstance();
$currentUser = $auth->getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expert Challenge - Code Gaming</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=VT323&family=Fira+Mono:wght@400;500&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/challenge-style.css">
</head>
<body class="challenge-body">
<?php include 'includes/header.php'; ?>
<main class="challenge-main">
    <div class="retro-window challenge-window">
        <!-- Title Bar -->
        <div class="window-title-bar">
            <div class="title-bar-left">
                <span class="apple-menu">🍎</span>
                <span class="menu-item">Challenge</span>
                <span class="menu-item">Leaderboard</span>
                <span class="menu-item">Help</span>
            </div>
            <div class="title-bar-center">
                <span class="window-title">Expert Challenge Mode</span>
            </div>
            <div class="title-bar-right">
                <span class="window-controls">
                    <span class="control close"></span>
                    <span class="control minimize"></span>
                    <span class="control maximize"></span>
                </span>
            </div>
        </div>
        <!-- Main Content Area -->
        <div class="window-content">
            <!-- Welcome Screen -->
            <div id="challenge-welcome" class="challenge-screen active">
                <div class="welcome-content">
                    <div class="welcome-icon">🚀</div>
                    <h1 class="welcome-title">EXPERT CHALLENGE</h1>
                    <p class="welcome-subtitle">The Ultimate Programming Test</p>
                    <div class="welcome-stats">
                        <div class="stat-item">
                            <span class="stat-label">Questions:</span>
                            <span class="stat-value">20</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Time Limit:</span>
                            <span class="stat-value">2:30</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Points per Question:</span>
                            <span class="stat-value">30</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Difficulty:</span>
                            <span class="stat-value">EXPERT ONLY</span>
                        </div>
                    </div>
                    <?php if (!$isLoggedIn): ?>
                    <div class="guest-input-section">
                        <label for="guest-nickname" class="input-label">Enter Your Nickname:</label>
                        <input type="text" id="guest-nickname" class="retro-input" placeholder="Your nickname here..." maxlength="20">
                        <span id="nickname-status" class="nickname-status"></span>
                    </div>
                    <?php endif; ?>
                    <div class="welcome-buttons">
                        <button class="btn-retro btn-start">
                            <span class="btn-text">START CHALLENGE</span>
                        </button>
                        <button class="btn-retro btn-instructions">
                            <span class="btn-text">📖 INSTRUCTIONS</span>
                        </button>
                        <button class="btn-retro btn-leaderboard">
                            <span class="btn-text">🏆 LEADERBOARD</span>
                        </button>
                    </div>
                </div>
            </div>
            <!-- Challenge In Progress Screen -->
            <div id="challenge-progress" class="challenge-screen">
                <!-- Header Stats -->
                <div class="challenge-header">
                    <div class="header-left">
                        <div class="progress-info">
                            <span class="progress-text">Question <span id="current-question">1</span> of 20</span>
                        </div>
                        <div class="score-info">
                            <span class="score-text">Score: <span id="current-score">0</span></span>
                        </div>
                    </div>
                    <div class="header-center">
                        <div class="timer-container">
                            <span class="timer-label">TIME:</span>
                            <span id="challenge-timer" class="timer-display">02:30</span>
                        </div>
                    </div>
                    <div class="header-right">
                        <div class="hearts-container">
                            <span class="hearts-label">LIVES:</span>
                            <span id="challenge-hearts" class="hearts-display">♥♥♥</span>
                        </div>
                    </div>
                </div>
                <!-- Question Content -->
                <div class="question-container">
                    <div class="question-header">
                        <span id="question-type" class="question-type">FILL IN THE BLANK</span>
                        <span id="question-points" class="question-points">30 pts</span>
                    </div>
                    <div class="question-content">
                        <h3 id="question-title" class="question-title">Question Title</h3>
                        <p id="question-description" class="question-description">Question description goes here...</p>
                        <!-- Code Editor (for code questions) -->
                        <div id="code-editor-container" class="code-editor-container" style="display: none;">
                            <div class="editor-header">
                                <span class="editor-title">Code Editor</span>
                                <button class="btn-retro btn-run" onclick="runCode()">▶ RUN</button>
                            </div>
                            <textarea id="code-editor" class="code-editor" placeholder="Write your code here..."></textarea>
                            <div id="code-output" class="code-output"></div>
                        </div>
                        <!-- Text Input (for fill-in-blank, output, case study) -->
                        <div id="text-input-container" class="text-input-container">
                            <label for="answer-input" class="input-label">Your Answer:</label>
                            <input type="text" id="answer-input" class="retro-input" placeholder="Type your answer...">
                        </div>
                    </div>
                    <!-- Action Buttons -->
                    <div class="question-actions">
                        <button class="btn-retro btn-submit">
                            <span class="btn-text">SUBMIT ANSWER</span>
                        </button>
                        <button class="btn-retro btn-skip">
                            <span class="btn-text">SKIP</span>
                        </button>
                    </div>
                </div>
            </div>
            <!-- End Screen -->
            <div id="challenge-end" class="challenge-screen">
                <div class="end-content">
                    <div class="end-header">
                        <h2 id="end-title" class="end-title">CHALLENGE COMPLETE!</h2>
                        <div id="end-achievement" class="end-achievement">🏆 MASTER CODER!</div>
                    </div>
                    <div class="end-stats">
                        <div class="stat-row">
                            <span class="stat-label">Final Score:</span>
                            <span id="final-score" class="stat-value">0</span>
                        </div>
                        <div class="stat-row">
                            <span class="stat-label">Questions Correct:</span>
                            <span id="questions-correct" class="stat-value">0/20</span>
                        </div>
                        <div class="stat-row">
                            <span class="stat-label">Time Taken:</span>
                            <span id="time-taken" class="stat-value">0:00</span>
                        </div>
                        <div class="stat-row">
                            <span class="stat-label">Accuracy:</span>
                            <span id="accuracy" class="stat-value">0%</span>
                        </div>
                    </div>
                    <div class="end-actions">
                        <button class="btn-retro btn-play-again">
                            <span class="btn-text">PLAY AGAIN</span>
                        </button>
                        <button class="btn-retro btn-leaderboard">
                            <span class="btn-text">LEADERBOARD</span>
                        </button>
                        <button class="btn-retro btn-exit">
                            <span class="btn-text">EXIT</span>
                        </button>
                    </div>
                    <!-- Leaderboard Preview -->
                    <div id="end-leaderboard" class="end-leaderboard">
                        <h3>Top Scores</h3>
                        <div id="leaderboard-content" class="leaderboard-list">
                            <!-- Populated by AJAX -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Status Bar -->
        <div class="window-status-bar">
            <div class="status-left">
                <span id="status-text">Ready to challenge your programming skills?</span>
            </div>
            <div class="status-right">
                <span id="status-time" class="status-time"><?php echo date('H:i'); ?></span>
            </div>
        </div>
    </div>
    <!-- Feedback Modal -->
    <div id="feedback-modal" class="retro-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="feedback-title">Feedback</h3>
                <button class="modal-close" onclick="closeFeedback()">✕</button>
            </div>
            <div class="modal-body">
                <div id="feedback-icon" class="feedback-icon">🎯</div>
                <p id="feedback-message">Feedback message here...</p>
                <p id="feedback-explanation" class="feedback-explanation" style="margin-top:0.5em;color:#ffe066;"></p>
                <div id="feedback-stats" class="feedback-stats">
                    <span>Score: <span id="feedback-score">0</span></span>
                    <span>Lives: <span id="feedback-lives">♥♥♥</span></span>
                </div>
            </div>
        </div>
    </div>
    <!-- Instructions Modal -->
    <div id="instructions-modal" class="retro-modal">
        <div class="modal-content instructions-modal">
            <div class="modal-header">
                <h3>📚 Challenge Instructions</h3>
                <button class="modal-close" onclick="closeInstructions()">✕</button>
            </div>
            <div class="modal-body">
                <div class="instruction-section">
                    <h4>🎯 Challenge Overview</h4>
                    <p>Test your expert programming skills with 20 challenging questions across different formats.</p>
                </div>
                <div class="instruction-section">
                    <h4>📝 Question Types</h4>
                    <ul>
                        <li><strong>Fill in the Blank:</strong> Complete missing code snippets</li>
                        <li><strong>Output Prediction:</strong> Predict what code will output</li>
                        <li><strong>Case Study:</strong> Solve real-world programming problems</li>
                        <li><strong>Code Writing:</strong> Write complete code solutions</li>
                    </ul>
                </div>
                <div class="instruction-section">
                    <h4>⏱️ Time & Scoring</h4>
                    <ul>
                        <li>2 minutes 30 seconds total time</li>
                        <li>30 points per correct answer</li>
                        <li>3 lives (hearts) - lose one for each wrong answer</li>
                        <li>No penalty for skipping questions</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- Leaderboard Modal -->
    <div id="leaderboard-modal" class="retro-modal">
        <div class="modal-content leaderboard-modal">
            <div class="modal-header">
                <h3>🏆 Challenge Leaderboard</h3>
                <button class="modal-close" onclick="closeLeaderboard()">✕</button>
            </div>
            <div class="modal-body">
                <div class="leaderboard-controls">
                    <button class="btn-retro btn-refresh" id="btn-refresh-leaderboard">
                        <span class="btn-text">🔄 REFRESH</span>
                    </button>
                    <div class="scope-tabs">
                        <button class="tab-btn active" data-scope="alltime">All Time</button>
                        <button class="tab-btn" data-scope="weekly">This Week</button>
                        <button class="tab-btn" data-scope="monthly">This Month</button>
                    </div>
                </div>
                <div id="leaderboard-list" class="leaderboard-list">
                    <!-- Populated by AJAX -->
                </div>
            </div>
        </div>
    </div>
</main>
<?php include 'includes/footer.php'; ?>
<!-- Scripts -->
<script>
    // Global variables for JavaScript
    window.CG_USER_ID = <?php echo $currentUser && isset($currentUser['id']) ? (int)$currentUser['id'] : 'null'; ?>;
    window.CG_USERNAME = <?php echo $currentUser && isset($currentUser['username']) ? json_encode($currentUser['username']) : 'null'; ?>;
    window.CG_IS_LOGGED_IN = <?php echo $auth->isLoggedIn() ? 'true' : 'false'; ?>;
</script>
<script src="assets/js/challenge.js"></script>
</body>
</html>