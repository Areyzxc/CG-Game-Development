<?php
// Include visitor tracking
require_once 'includes/track_visitor.php';

/**
 * ==========================================================
 * File: home_page.php
 * 
 * Description:
 *   - Main landing page for Code Gaming platform
 *   - Features:
 *       • Animated hero section with parallax and floating code icons
 *       • Latest announcements carousel
 *       • Quick access cards for profile, tutorials, games, and more
 *       • Quiz, mini-game, and challenge analytics & leaderboards
 *       • Progress dashboard and recent achievements feed
 *       • Responsive, modern UI with interactive backgrounds
 * 
 * Usage:
 *   - Public page for all users and visitors
 *   - Entry point to all major features and game modes
 * 
 * Files Included:
 *   - assets/css/stylehome.css
 *   - assets/js/functionhome.js
 *   - assets/js/chart.js
 *   - images/background-1.jpg, images/icon-tutorial.png, etc.
 *   - External: Bootstrap, Font Awesome, Anime.js, Typed.js, Rellax, ScrollReveal, Chart.js
 * 
 * Author: [Santiago]
 * Last Updated: [July 22, 2025]
 * -- Code Gaming Team --
 * ==========================================================
 */

// Include required files
require_once 'includes/Database.php';
require_once 'includes/Auth.php';
require_once 'includes/ErrorHandler.php';
require_once 'includes/CSRFProtection.php';
require_once 'includes/XSSProtection.php';

// Initialize core components
$db = Database::getInstance();
$auth = Auth::getInstance();
$errorHandler = ErrorHandler::getInstance();
$csrf = CSRFProtection::getInstance();
$xss = XSSProtection::getInstance();

// Note: Do NOT redirect admins immediately. We show a welcome modal first
// and redirect to the dashboard after the modal is closed via JS.

// Get database connection
$conn = $db->getConnection();

// Get current user data if logged in
$currentUser = null;
$currentRole = null;

if ($auth->isLoggedIn()) {
    $currentUser = $auth->getCurrentUser();
    $currentRole = $auth->getCurrentRole();
    
    // Debugging: Check if user data was retrieved
    if (!$currentUser) {
        error_log('User is logged in but user data could not be retrieved. User ID: ' . $_SESSION['user_id']);
    } else {
        error_log('User data retrieved successfully. User ID: ' . $currentUser['id']);
    }
}

// Track visitor for analytics (only for non-logged-in users)
if (!$currentUser) {
    try {
        $visitorIP = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        
        // Insert visitor log
        $stmt = $conn->prepare('
            INSERT INTO visitor_logs (ip_address, user_agent, visit_time) 
            VALUES (?, ?, NOW())
        ');
        $stmt->execute([$visitorIP, $userAgent]);
    } catch (Exception $e) {
        // Silently fail visitor tracking to not break the page
        error_log('Visitor tracking failed: ' . $e->getMessage());
    }
}

// Set additional styles for header.php
$additionalStyles = '
    <link rel="stylesheet" href="assets/css/stylehome.css">
    <link rel="stylesheet" href="assets/css/home-profile.css">
    <script src="https://cdn.jsdelivr.net/npm/animejs@4.0.0/lib/anime.iife.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/typed.js@2.0.12"></script>
    <script src="https://cdn.jsdelivr.net/npm/rellax@1.12.1/rellax.min.js"></script>
    <script src="https://unpkg.com/scrollreveal"></script>
';

// Set page title for header
$pageTitle = 'Home Page';
// Include header which contains the full HTML structure
include 'includes/header.php';

// Check for the login success flag to show a notification
$showLoginNotification = isset($_GET['login']) && $_GET['login'] === 'success';
if ($showLoginNotification && $currentUser):
?>
     <div class="login-notification-home" id="loginNotificationHome">
        <span>Welcome back, <strong><?php echo htmlspecialchars($currentUser['username']); ?></strong>!</span>
        <button type="button" class="btn-close" id="closeLoginNotificationHome" aria-label="Close"></button>
    <?php endif; ?>
    <!-- ===== Welcome Modal ===== -->
    <div class="modal fade" id="welcomeModal" tabindex="-1" aria-labelledby="welcomeModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta name="csrf-token" content="<?php echo $csrf->getToken(); ?>">
                <div class="modal-header welcome-modal-header">
                    <div class="welcome-icon-container">
                        <i class="bx bx-magic-wand welcome-icon"></i>
                    </div>
                    <div class="welcome-title-container">
                        <h4 class="modal-title welcome-title" id="welcomeModalLabel">
                            Welcome to Code Game, 
                            <?php 
                            if ($auth->isAdmin()) {
                                echo htmlspecialchars($currentUser['username']) . '!';
                            } elseif ($currentUser) {
                                echo htmlspecialchars($currentUser['username']) . '!';
                            } else {
                                echo 'Guest!';
                            }
                            ?>
                        </h4>
                        <p class="welcome-subtitle">Let's explore your coding adventure!</p>
                    </div>
                    <button type="button" class="btn-close welcome-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body welcome-modal-body">
                    <div class="wizard-container">
                        <div class="wizard-character">
                            <i class="bx bx-code-alt wizard-avatar"></i>
                        </div>
                        <div class="welcome-content">
                            <?php if (!$auth->isAdmin()): ?>
                                <!-- User/Guest Welcome Content -->
                                <p class="welcome-intro">Ready to level up your coding skills? Here's what awaits you:</p>
                                <div class="accordion welcome-accordion" id="userWelcomeAccordion">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#homeDesc" aria-expanded="true" data-section="home">
                                                <i class="bx bx-home me-2"></i>Home
                                            </button>
                                        </h2>
                                        <div id="homeDesc" class="accordion-collapse collapse show" data-bs-parent="#userWelcomeAccordion">
                                            <div class="accordion-body">
                                                Your central hub for tracking progress with interactive pie charts, quick access to all game modes, and your personal achievement dashboard.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#tutorialDesc" data-section="tutorials">
                                                <i class="bx bx-book-open me-2"></i>Tutorial/Lessons
                                            </button>
                                        </h2>
                                        <div id="tutorialDesc" class="accordion-collapse collapse" data-bs-parent="#userWelcomeAccordion">
                                            <div class="accordion-body">
                                                Step-by-step guides through programming languages like Python, JavaScript, and more. Track your completion progress in your profile.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#gameModesDesc" data-section="games">
                                                <i class="bx bx-joystick me-2"></i>Game Modes
                                            </button>
                                        </h2>
                                        <div id="gameModesDesc" class="accordion-collapse collapse" data-bs-parent="#userWelcomeAccordion">
                                            <div class="accordion-body">
                                                <strong>Mini-Game:</strong> Fun coding activities like speed typing and guess-the-output challenges.<br>
                                                <strong>Quiz:</strong> Test your knowledge with multiple-choice questions and earn points.<br>
                                                <strong>Challenge:</strong> Expert-level problems for serious coders seeking high scores and glory!
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#announcementsDesc" data-section="announcements">
                                                <i class="bx bx-megaphone me-2"></i>Announcements
                                            </button>
                                        </h2>
                                        <div id="announcementsDesc" class="accordion-collapse collapse" data-bs-parent="#userWelcomeAccordion">
                                            <div class="accordion-body">
                                                Stay updated with the latest platform news, feature releases, and community events. Click on any announcement to read the full details.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#profileDesc" data-section="profile">
                                                <i class="bx bx-user me-2"></i>Profile
                                            </button>
                                        </h2>
                                        <div id="profileDesc" class="accordion-collapse collapse" data-bs-parent="#userWelcomeAccordion">
                                            <div class="accordion-body">
                                                <?php if ($currentUser): ?>
                                                    Customize your username, upload a profile picture, write your bio, and view your achievements, scores, and detailed progress tables.
                                                <?php else: ?>
                                                    Sign up to save your progress, unlock achievements, compete on leaderboards, and customize your coding journey!
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer welcome-modal-footer">
                    <div class="footer-left">
                        <div class="wizard-tip">
                            <i class="bx bx-bulb me-2"></i>
                            <span>Tip: You can always access help from the navigation menu!</span>
                        </div>
                        <div class="dont-show-again-container">
                            <label class="form-check-label dont-show-again-label">
                                <input type="checkbox" class="form-check-input me-2" id="dontShowAgainCheck">
                                <i class="bx bx-hide me-1"></i>Don't show this welcome tour again
                            </label>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary welcome-btn" data-bs-dismiss="modal">
                        <i class="bx bx-rocket me-2"></i>Let's Start Coding!
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Background Slideshow -->
    <div id="bgSlideshow" class="bg-slideshow">
        <div class="slideshow-overlay"></div>
    </div>

    <!-- ===== Main Content ===== -->
    <main class="pt-5 mt-4">
        <!-- Dynamic Background (particles, etc.) -->
        <div id="dynamicBackground" class="dynamic-bg">
            <canvas id="bgCanvas"></canvas>
            <div class="particles-container"></div>
        </div>

        <!-- Quote Spotlight Section -->
        <section class="quote-spotlight-section position-relative py-5 text-white" role="banner" aria-labelledby="quote-spotlight">
            <div class="container position-relative text-center">
                    <!-- Welcome Message with Typed.js Effect -->
                    <?php if ($auth->isLoggedIn() && isset($currentUser['username'])): ?>
                        <div class="welcome-message mb-4">
                            <h2 class="display-5 fw-bold mb-3">
                                <span id="welcomeTyped" class="glitch-text"></span>
                            </h2>
                            <p class="lead">Continue your coding journey today!</p>
                        </div>
                    <?php endif; ?>
                    
                    <div class="quote-content">
                        <div id="quoteSpotlight" class="quote-spotlight mb-4">
                            <blockquote class="quote-text">
                                <i class="bx bx-code-alt quote-icon"></i>
                                <span id="currentQuote">Loading inspirational quote...</span>
                                <i class="bx bx-code-alt quote-icon"></i>
                            </blockquote>
                            <cite id="quoteAuthor" class="quote-author">— Loading...</cite>
                        </div>
                        </div>
                    </div>
                </div>
        </section>

        <!-- User Progress Dashboard (Dynamic for logged-in users) -->
        <?php if ($auth->isLoggedIn()): ?>
        <section class="progress-dashboard container py-5" role="region" aria-labelledby="progress-heading">
            <!-- User Profile Header -->
            <div class="user-journey-header mb-4">
                <div class="user-banner" id="userBanner" style="background-image: url('assets/images/default-banner.jpg');">
                </div>
                <div class="user-info text-center mt-4">
                    <h3 class="username" id="userDisplayName">Welcome Back, <?php echo htmlspecialchars($currentUser['username'] ?? 'Coder'); ?>!</h3>
                    <p class="text-muted" id="userLevel">
                        <?php 
                        $level = $currentUser['level'] ?? 1;
                        $title = match(true) {
                            $level >= 50 => 'Coding Master',
                            $level >= 30 => 'Senior Developer',
                            $level >= 20 => 'Mid-level Developer',
                            $level >= 10 => 'Junior Developer',
                            default => 'Coding Enthusiast'
                        };
                        echo htmlspecialchars($title);
                        ?>
                    </p>
                </div>
            </div>
            
            <h4 id="progress-heading" class="fw-bold mb-4 text-center">Your Coding Journey</h4>
            <div class="row g-4" id="progressContainer">
                <!-- Progress cards will be loaded via AJAX -->
                <div class="col-12 text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading your progress...</span>
                    </div>
                </div>
                
                <!-- Fallback content in case JavaScript is disabled -->
                <noscript>
                    <div class="col-12">
                        <div class="alert alert-warning">
                            <i class="bx bx-error-circle me-2"></i>
                            Please enable JavaScript to view your progress dashboard.
                        </div>
                    </div>
                </noscript>
            </div>
        </section>
        <?php else: ?>
        <!-- Guest Progress Placeholders -->
        <section class="progress-dashboard container py-5" role="region" aria-labelledby="guest-progress-heading">
            <h4 id="guest-progress-heading" class="fw-bold mb-4 text-center">Start Your Coding Journey</h4>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="progress-card" id="tutorial-progress-card">
                    <div class="progress-icon text-primary">
                        <i class="fas fa-graduation-cap fa-2x"></i>
                    </div>
                    <div class="progress-details">
                        <h5 class="mb-3">Tutorial Progress</h5>
                        <div class="progress mb-2" style="height: 8px;">
                            <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated" 
                                 id="tutorial-progress-bar" 
                                 role="progressbar" 
                                 style="width: 0%" 
                                 aria-valuenow="0" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="progress-percentage fw-bold" id="tutorial-progress-percentage">0%</span>
                            <span class="progress-text small" id="tutorial-progress-text">
                                <?php echo ($currentUser ? 'Loading...' : 'Sign in to track progress'); ?>
                            </span>
                        </div>
                    </div>
                </div>
                </div>
                </div>
                <div class="col-md-4">
                    <div class="progress-card h-100" id="profile-progress-card">
                        <div class="progress-icon text-success">
                            <i class="fas fa-user-circle fa-2x"></i>
                        </div>
                        <div class="progress-details">
                            <h5 class="mb-3">Profile Status</h5>
                            <div class="progress mb-2" style="height: 8px;">
                                <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" 
                                     id="profile-progress-bar" 
                                     role="progressbar" 
                                     style="width: 0%" 
                                     aria-valuenow="0" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100"></div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="progress-percentage fw-bold" id="profile-progress-percentage">0%</span>
                                <span class="progress-text small" id="profile-progress-text">
                                    <?php echo ($currentUser ? 'Loading profile...' : 'Sign in to complete profile'); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <!-- ===== Dynamic Announcements Section ===== -->
        <section id="announcements" class="container py-5" role="region" aria-labelledby="announcements-heading">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 id="announcements-heading" class="fw-bold mb-0">Latest Updates</h4>
                <a href="announcements.php" class="btn btn-outline-primary btn-sm">
                    <i class="bx bx-list-ul me-2"></i>View All
                </a>
            </div>
            <div id="announcementsContainer">
                <!-- Announcements will be loaded via AJAX for real-time updates -->
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading announcements...</span>
                    </div>
                </div>
            </div>
        </section>


        <!-- ===== Quick Access Cards ===== -->
        <section class="quick-cards container py-5" aria-label="Quick Access Navigation">
            <div class="row g-4 justify-content-center">
                <?php
                // Get user progress data for quick cards
                $userProgress = [
                    'tutorial' => ['total_topics' => 0, 'completed_topics' => 0],
                    'quiz' => ['total_attempts' => 0, 'correct_answers' => 0],
                    'challenge' => ['total_attempts' => 0, 'correct_answers' => 0],
                    'minigame' => ['total_games' => 0, 'best_score' => 0]
                ];
                
                if ($currentUser) {
                    try {
                        // Get tutorial progress
                        $stmt = $conn->prepare("
                            SELECT COUNT(*) as total_topics, 
                                SUM(CASE WHEN status = 'done_reading' THEN 1 ELSE 0 END) as completed_topics
                            FROM user_progress 
                            WHERE user_id = ?
                        ");
                        $stmt->execute([$currentUser['id']]);
                        $tutorialProgress = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        // Get quiz stats
                        $stmt = $conn->prepare("
                            SELECT COUNT(*) as total_attempts,
                                SUM(CASE WHEN is_correct = 1 THEN 1 ELSE 0 END) as correct_answers
                            FROM user_quiz_attempts 
                            WHERE user_id = ?
                        ");
                        $stmt->execute([$currentUser['id']]);
                        $quizProgress = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        // Get challenge stats
                        $stmt = $conn->prepare("
                            SELECT COUNT(*) as total_attempts,
                                SUM(CASE WHEN is_correct = 1 THEN 1 ELSE 0 END) as correct_answers
                            FROM user_challenge_attempts 
                            WHERE user_id = ?
                        ");
                        $stmt->execute([$currentUser['id']]);
                        $challengeProgress = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        // Get mini-game stats
                        $stmt = $conn->prepare("
                            SELECT COUNT(*) as total_games,
                                MAX(score) as best_score
                            FROM mini_game_results 
                            WHERE user_id = ?
                        ");
                        $stmt->execute([$currentUser['id']]);
                        $minigameProgress = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        $userProgress = [
                            'tutorial' => $tutorialProgress ?: ['total_topics' => 0, 'completed_topics' => 0],
                            'quiz' => $quizProgress ?: ['total_attempts' => 0, 'correct_answers' => 0],
                            'challenge' => $challengeProgress ?: ['total_attempts' => 0, 'correct_answers' => 0],
                            'minigame' => $minigameProgress ?: ['total_games' => 0, 'best_score' => 0]
                        ];
                    } catch (Exception $e) {
                        // Fallback to default progress
                        $userProgress = [
                            'tutorial' => ['total_topics' => 0, 'completed_topics' => 0],
                            'quiz' => ['total_attempts' => 0, 'correct_answers' => 0],
                            'challenge' => ['total_attempts' => 0, 'correct_answers' => 0],
                            'minigame' => ['total_games' => 0, 'best_score' => 0]
                        ];
                    }
                }
                
                $quickCards = [
                    [
                        'title' => 'Profile',
                        'icon' => 'fa-user',
                        'image' => 'assets/images/Profile.png',
                        'progress' => $currentUser ? 'Level ' . min(floor((($userProgress['challenge']['correct_answers'] ?? 0) * 30) / 100) + 1, 50) : 'Guest',
                        'text' => $currentUser ? 'Manage your account' : 'Sign up to save progress',
                        'link' => $currentUser ? 'profile.php' : 'sign_in.php'
                    ],
                    [
                        'title' => 'Tutorials',
                        'icon' => 'fa-book',
                        'image' => 'assets/images/Tutorial.png',
                        'progress' => ($userProgress['tutorial']['completed_topics'] ?? 0) . ' of ' . max($userProgress['tutorial']['total_topics'] ?? 1, 1),
                        'text' => ($userProgress['tutorial']['completed_topics'] ?? 0) > 0 ? 
                            'Keep learning!' : 'Start your journey',
                        'link' => 'tutorial.php'
                    ],
                    [
                        'title' => 'Mini-Game',
                        'icon' => 'fa-gamepad',
                        'image' => 'assets/images/icon-mini-game.png',
                        'progress' => ($userProgress['minigame']['total_games'] ?? 0) . ' games played',
                        'text' => ($userProgress['minigame']['best_score'] ?? 0) > 0 ? 
                            'Best: ' . ($userProgress['minigame']['best_score'] ?? 0) : 'New high score awaits',
                        'link' => 'mini-game.php'
                    ],
                    [
                        'title' => 'Quiz',
                        'icon' => 'fa-question-circle',
                        'image' => 'assets/images/icon-quiz.png',
                        'progress' => ($userProgress['quiz']['correct_answers'] ?? 0) . '/40 correct',
                        'text' => ($userProgress['quiz']['correct_answers'] ?? 0) > 0 ? 
                            'Improve your score!' : 'Test your knowledge',
                        'link' => 'quiz.php'
                    ],
                    [
                        'title' => 'Challenge',
                        'icon' => 'fa-trophy',
                        'image' => 'assets/images/icon-challenge.png',
                        'progress' => ($userProgress['challenge']['correct_answers'] ?? 0) . ' solved',
                        'text' => ($userProgress['challenge']['correct_answers'] ?? 0) > 0 ? 
                            'Expert level unlocked!' : 'Master the challenges',
                        'link' => 'challenges.php'
                    ],
                    [
                        'title' => 'About',
                        'icon' => 'fa-info-circle',
                        'image' => 'assets/images/about-us.png',
                        'text' => 'Learn about Code Game',
                        'link' => 'about.php'
                    ]
                ];

                foreach ($quickCards as $card): 
                    $title = htmlspecialchars($card['title']);
                    $image = file_exists($card['image']) ? $card['image'] : 'images/default-card.png';
                    $progress = isset($card['progress']) ? htmlspecialchars($card['progress']) : '';
                    $text = isset($card['text']) ? htmlspecialchars($card['text']) : '';
                    $link = htmlspecialchars($card['link']);
                    $icon = htmlspecialchars($card['icon']);
                ?>
                    <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                        <div class="card quick-card drift" role="article">
                            <img src="<?php echo $image; ?>" 
                                class="card-img-top" 
                                alt="<?php echo $title; ?>"
                                onerror="this.src='images/default-card.png'">
                            <div class="card-body text-center">
                                <h6 class="card-title">
                                    <i class="fas <?php echo $icon; ?> me-2" aria-hidden="true"></i>
                                    <?php echo $title; ?>
                                </h6>
                                <?php if ($progress): ?>
                                    <p class="progress-text" aria-label="Progress: <?php echo $progress; ?>">
                                        <?php echo $progress; ?>
                                    </p>
                                <?php endif; ?>
                                <?php if ($text): ?>
                                    <p class="small text-light"><?php echo $text; ?></p>
                                <?php endif; ?>
                                <a href="<?php echo $link; ?>" 
                                class="btn btn-outline-light btn-sm"
                                aria-label="Navigate to <?php echo $title; ?>">
                                    Go to <?php echo $title; ?>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- ===== Quiz Analytics & Leaderboard Section ===== -->
        <section class="container py-5" id="home-quiz-analytics" role="region" aria-labelledby="quiz-analytics-heading">
            <div class="retro-analytics-window-bg">
            <!-- Overlapping Stat Cards -->
            <div class="stat-card stat-card-best" role="img" aria-label="Best quiz score">
              <div class="stat-card-title">Best Score <span class="stat-x" aria-hidden="true">&#10005;</span></div>
              <div class="stat-card-value" id="quiz-best-score" aria-live="polite">--</div>
              <div class="stat-card-desc">Your all-time best</div>
            </div>
            <div class="stat-card stat-card-recent" role="img" aria-label="Recent quiz game">
              <div class="stat-card-title">Recent Game <span class="stat-x" aria-hidden="true">&#10005;</span></div>
              <div class="stat-card-value" id="quiz-recent-score" aria-live="polite">--</div>
              <div class="stat-card-desc" id="quiz-recent-time">No recent game</div>
            </div>
            <div class="stat-card stat-card-top" role="img" aria-label="Top quiz player">
              <div class="stat-card-title">Top Player <span class="stat-x" aria-hidden="true">&#10005;</span></div>
              <div class="stat-card-value" id="quiz-top-player" aria-live="polite">--</div>
              <div class="stat-card-desc" id="quiz-top-player-desc">No top player</div>
            </div>
            <!-- Main Window -->
            <div class="retro-analytics-window">
              <div class="window-title-bar">
                <div class="window-controls" aria-hidden="true">
                  <span class="window-dot red"></span>
                  <span class="window-dot yellow"></span>
                  <span class="window-dot green"></span>
                </div>
                <span class="window-title">// QUIZ ANALYTICS & LEADERBOARD</span>
                <span class="window-x" aria-hidden="true">&#10005;</span>
              </div>
              <div class="window-content">
                <div class="analytics-header">
                  <div class="analytics-tabs" role="tablist" aria-label="Quiz analytics time period">
                    <button class="analytics-tab active" data-scope="alltime" role="tab" aria-selected="true" aria-controls="quiz-content">All-Time</button>
                    <button class="analytics-tab" data-scope="weekly" role="tab" aria-selected="false" aria-controls="quiz-content">Weekly</button>
                    <button class="analytics-tab" data-scope="monthly" role="tab" aria-selected="false" aria-controls="quiz-content">Monthly</button>
                  </div>
                </div>
                <div class="analytics-body" id="quiz-content" role="tabpanel">
                  <div class="difficulty-tabs" role="tablist" aria-label="Quiz difficulty level">
                    <button class="difficulty-tab active" data-difficulty="beginner" role="tab" aria-selected="true" aria-controls="quiz-difficulty-content">Beginner</button>
                    <button class="difficulty-tab" data-difficulty="intermediate" role="tab" aria-selected="false" aria-controls="quiz-difficulty-content">Intermediate</button>
                    <button class="difficulty-tab" data-difficulty="expert" role="tab" aria-selected="false" aria-controls="quiz-difficulty-content">Expert</button>
                  </div>
                  <div class="user-quiz-stats" aria-live="polite"></div>
                  <div class="quiz-leaderboard-list" aria-live="polite"></div>
                  <div class="play-now-section">
                    <button class="btn-play-now" onclick="window.location.href='quiz.php'" aria-describedby="quiz-play-description">
                      <span class="btn-text">🎯 PLAY QUIZ NOW</span>
                    </button>
                    <div id="quiz-play-description" class="visually-hidden">
                      Start a new quiz to test your coding knowledge and compete on the leaderboard
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- ===== Mini-Game Leaderboard Section ===== -->
        <div class="retro-bg-container" style="margin-top: 2.5rem;">
        <section class="container py-5" id="home-minigame-analytics">
          <div class="retro-analytics-window-bg minigame-theme">
            <!-- Overlapping Stat Cards -->
            <div class="stat-card stat-card-best minigame-theme">
              <div class="stat-card-title">Best Score <span class="stat-x">&#10005;</span></div>
              <div class="stat-card-value" id="minigame-best-score">--</div>
              <div class="stat-card-desc">Your all-time best</div>
            </div>
            <div class="stat-card stat-card-recent minigame-theme">
              <div class="stat-card-title">Recent Game <span class="stat-x">&#10005;</span></div>
              <div class="stat-card-value" id="minigame-recent-score">--</div>
              <div class="stat-card-desc" id="minigame-recent-time">No recent game</div>
            </div>
            <div class="stat-card stat-card-top minigame-theme">
              <div class="stat-card-title">Top Player <span class="stat-x">&#10005;</span></div>
              <div class="stat-card-value" id="minigame-top-player">--</div>
              <div class="stat-card-desc" id="minigame-top-player-desc">No top player</div>
            </div>
            <div class="retro-analytics-window minigame-theme">
              <div class="window-title-bar minigame-theme">
                <span class="window-controls">
                  <span class="window-dot blue"></span>
                  <span class="window-dot blue"></span>
                  <span class="window-dot green"></span>
                </span>
                <span class="window-title">// MINI-GAME LEADERBOARD</span>
                <span class="window-refresh" title="Refresh">⟳</span>
                <span class="window-x">&#10005;</span>
              </div>
              <div class="window-content">
                <div class="analytics-tabs">
                  <span class="analytics-tab active" data-scope="alltime">All-Time</span>
                  <span class="analytics-tab" data-scope="weekly">Weekly</span>
                  <span class="analytics-tab" data-scope="monthly">Monthly</span>
                </div>
                <div class="user-quiz-stats"></div>
                <div class="quiz-leaderboard-list"></div>
                <div class="play-now-section">
                  <button class="btn-play-now minigame-theme" onclick="window.location.href='mini-game.php'">
                    <span class="btn-text">🎮 PLAY MINI-GAME NOW</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </section>
        </div>

        <!-- ===== Challenge Leaderboard Section ===== -->
        <div class="retro-bg-container" style="margin-top: 2.5rem;">
        <section class="container py-5" id="home-challenge-analytics">
          <div class="retro-analytics-window-bg challenge-theme">
            <!-- Overlapping Stat Cards -->
            <div class="stat-card stat-card-best challenge-theme">
              <div class="stat-card-title">Best Score <span class="stat-x">&#10005;</span></div>
              <div class="stat-card-value" id="challenge-best-score">--</div>
              <div class="stat-card-desc">Your all-time best</div>
            </div>
            <div class="stat-card stat-card-recent challenge-theme">
              <div class="stat-card-title">Recent Game <span class="stat-x">&#10005;</span></div>
              <div class="stat-card-value" id="challenge-recent-score">--</div>
              <div class="stat-card-desc" id="challenge-recent-time">No recent game</div>
            </div>
            <div class="stat-card stat-card-top challenge-theme">
              <div class="stat-card-title">Top Player <span class="stat-x">&#10005;</span></div>
              <div class="stat-card-value" id="challenge-top-player">--</div>
              <div class="stat-card-desc" id="challenge-top-player-desc">No top player</div>
            </div>
            <div class="retro-analytics-window challenge-theme">
              <div class="window-title-bar challenge-theme">
                <span class="window-controls">
                  <span class="window-dot gold"></span>
                  <span class="window-dot orange"></span>
                  <span class="window-dot green"></span>
                </span>
                <span class="window-title">// CHALLENGE LEADERBOARD (EXPERT ONLY)</span>
                <span class="window-refresh" title="Refresh">⟳</span>
                <span class="window-x">&#10005;</span>
              </div>
              <div class="window-content">
                <div class="analytics-tabs">
                  <span class="analytics-tab active" data-scope="alltime">All-Time</span>
                  <span class="analytics-tab" data-scope="weekly">Weekly</span>
                  <span class="analytics-tab" data-scope="monthly">Monthly</span>
                </div>
                <div class="user-quiz-stats"></div>
                <div class="quiz-leaderboard-list"></div>
                <div class="play-now-section">
                  <button class="btn-play-now challenge-theme" onclick="window.location.href='challenges.php'">
                    <span class="btn-text">🚀 PLAY CHALLENGE NOW</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </section>
        </div>
    </main>

    <!-- ===== Footer ===== -->
    <?php include 'includes/footer.php'; ?>

    <!-- ===== Custom Scripts ===== -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.5.1/dist/chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
    <!-- Home Page Specific Scripts -->
    <script>
        // Global variables for JavaScript
        window.CG_USER_ID = <?php echo $currentUser ? $currentUser['id'] : 'null'; ?>;
        window.CG_USERNAME = <?php echo $currentUser ? json_encode($currentUser['username']) : 'null'; ?>;
        window.CG_NICKNAME = <?php echo isset($_SESSION['guest_nickname']) ? json_encode($_SESSION['guest_nickname']) : 'null'; ?>;
        window.CG_USER_ROLE = <?php echo $currentRole ? json_encode($currentRole) : 'null'; ?>;
        window.CG_IS_ADMIN = <?php echo $auth->isAdmin() ? 'true' : 'false'; ?>;
        window.CSRF_TOKEN = <?php echo json_encode($csrf->getToken()); ?>;
    </script>
    <!-- Home Page JavaScript Files -->
    <script>
        // Make sure APIHelper is only loaded once
        if (typeof window.APIHelper === 'undefined') {
            document.write('<script src="assets/js/api-helper.js"><\/script>');
        }
    </script>
    <script src="assets/js/functionhome.js"></script>
    <script src="assets/js/chart.js"></script>
    <script src="assets/js/welcome-modal.js"></script>
    <script src="assets/js/home-enhancements.js"></script>
    <script>
        
        // Function to update quick cards with progress
        function updateQuickCardsProgress(progress) {
            const tutorialCard = document.querySelector('.quick-card[data-card-type="tutorial"]');
            if (tutorialCard) {
                const progressEl = tutorialCard.querySelector('.card-progress');
                if (progressEl) {
                    progressEl.textContent = `${progress.completed_topics} of ${progress.total_topics}`;
                }
                const textEl = tutorialCard.querySelector('.card-text');
                if (textEl) {
                    textEl.textContent = progress.completed_topics > 0 ? 'Keep learning!' : 'Start your journey';
                }
            }
        }
        
        // Global error display function
        window.showGlobalError = function(message) {
            console.error('Global error:', message);
            
            // Try to find an existing error container or create one
            let errorContainer = document.getElementById('globalErrorContainer');
            
            if (!errorContainer) {
                errorContainer = document.createElement('div');
                errorContainer.id = 'globalErrorContainer';
                errorContainer.className = 'container mt-3';
                
                // Insert at the beginning of the main content
                const mainContent = document.querySelector('main') || document.body;
                if (mainContent.firstChild) {
                    mainContent.insertBefore(errorContainer, mainContent.firstChild);
                } else {
                    mainContent.appendChild(errorContainer);
                }
            }
            
            // Set the error message
            errorContainer.innerHTML = `
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bx bx-error-circle me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>`;
        };

        // Set up global variables for the page
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM fully loaded');
            
            // Set user data
            window.CG_USER_ID = '<?php echo $currentUser ? $currentUser['id'] : ''; ?>';
            
            window.CG_IS_LOGGED_IN = <?php echo $auth->isLoggedIn() ? 'true' : 'false'; ?>;
            
            console.log('User ID:', window.CG_USER_ID, 'Is Logged In:', window.CG_IS_LOGGED_IN);
            
            // Initialize components
            try {
                // Initialize all components if the function exists
                if (window.initializeAll && typeof window.initializeAll === 'function') {
                    console.log('Initializing all components...');
                    window.initializeAll();
                } else {
                    console.warn('initializeAll function not found');
                    showGlobalError('Some page features may not work correctly. Please refresh the page.');
                }
                
                // Load user progress if function exists and user is logged in
                if (window.CG_IS_LOGGED_IN) {
                    if (window.loadUserProgress && typeof window.loadUserProgress === 'function') {
                        console.log('Loading user progress...');
                        window.loadUserProgress().catch(error => {
                            console.error('Error in loadUserProgress:', error);
                            showGlobalError('Failed to load progress data. ' + (error.message || ''));
                        });
                    } else {
                        console.warn('loadUserProgress function not found');
                        const progressContainer = document.getElementById('progressContainer');
                        if (progressContainer) {
                            progressContainer.innerHTML = `
                                <div class="col-12">
                                    <div class="alert alert-warning">
                                        <i class="bx bx-error-circle me-2"></i>
                                        Unable to load progress. Please refresh the page.
                                    </div>
                                </div>`;
                        }
                    }
                } else {
                    console.log('User not logged in, showing guest message');
                    const progressContainer = document.getElementById('progressContainer');
                    if (progressContainer) {
                        progressContainer.innerHTML = `
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="bx bx-log-in-circle me-2"></i>
                                    Please <a href="login.php" class="alert-link">log in</a> to track your coding journey
                                </div>
                            </div>`;
                    }
                }
                
                // Load announcements if function exists
                if (window.loadAnnouncements && typeof window.loadAnnouncements === 'function') {
                    console.log('Loading announcements...');
                    window.loadAnnouncements().catch(error => {
                        console.error('Error in loadAnnouncements:', error);
                        const announcementsContainer = document.getElementById('announcementsContainer');
                        if (announcementsContainer) {
                            announcementsContainer.innerHTML = `
                                <div class="alert alert-warning">
                                    <i class="bx bx-error-circle me-2"></i>
                                    Failed to load announcements. Please refresh the page.
                                </div>`;
                        }
                    });
                } else {
                    console.warn('loadAnnouncements function not found');
                    const announcementsContainer = document.getElementById('announcementsContainer');
                    if (announcementsContainer) {
                        announcementsContainer.innerHTML = `
                            <div class="alert alert-warning">
                                <i class="bx bx-error-circle me-2"></i>
                                Unable to load announcements. Please refresh the page.
                            </div>`;
                    }
                }
                
                // Close login notification if exists
                const closeNotificationBtn = document.getElementById('closeLoginNotificationHome');
                if (closeNotificationBtn) {
                    closeNotificationBtn.addEventListener('click', function() {
                        const notification = document.getElementById('loginNotificationHome');
                        if (notification) {
                            notification.style.display = 'none';
                        }
                    });
                }
            } catch (error) {
                console.error('Error during initialization:', error);
                showGlobalError('An error occurred while initializing the page. ' + (error.message || ''));
            }
        });
    </script>
    <script>
        // Global error display function
        function showGlobalError(message) {
            console.error('Global error:', message);
            
            // Try to find an existing error container or create one
            let errorContainer = document.getElementById('globalErrorContainer');
            
            if (!errorContainer) {
                errorContainer = document.createElement('div');
                errorContainer.id = 'globalErrorContainer';
                errorContainer.className = 'container mt-3';
                
                // Insert at the beginning of the main content
                const mainContent = document.querySelector('main') || document.body;
                if (mainContent.firstChild) {
                    mainContent.insertBefore(errorContainer, mainContent.firstChild);
                } else {
                    mainContent.appendChild(errorContainer);
                }
            }
            
            // Set the error message
            errorContainer.innerHTML = `
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bx bx-error-circle me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>`;
        }
    </script>
    <?php
    // Helper function for announcement icons
    function getAnnouncementIcon($type) {
        $icons = [
            'update' => 'fa-star',
            'maintenance' => 'fa-tools',
            'tutorial' => 'fa-book',
            'default' => 'fa-bullhorn'
        ];
        return $icons[$type] ?? $icons['default'];
    }
    ?>
