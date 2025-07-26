<?php
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

// Initialize core components
$db = Database::getInstance();
$auth = Auth::getInstance();
$errorHandler = ErrorHandler::getInstance();

// Redirect admin users to the admin dashboard immediately
if ($auth->isAdmin()) {
    header('Location: admin_dashboard.php');
    exit;
}

// Get database connection
$conn = $db->getConnection();

// Get current user data if logged in
$currentUser = $auth->isLoggedIn() ? $auth->getCurrentUser() : null;
$currentRole = $auth->isLoggedIn() ? $auth->getCurrentRole() : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="Code Game - Learn coding through interactive gameplay and challenges" />
    <title>Code Game • Home</title>

    <!-- ===== External CSS Dependencies ===== -->
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.4/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
    <!-- Custom CSS -->
    <link href="assets/css/stylehome.css" rel="stylesheet" />

    <!-- Animation Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/animejs@4.0.0/lib/anime.iife.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/typed.js@2.0.12"></script>
    <script src="https://cdn.jsdelivr.net/npm/rellax@1.12.1/rellax.min.js"></script>
    <script src="https://unpkg.com/scrollreveal"></script>
</head>

<body>
    <!-- ===== Navigation Bar ===== -->
    <?php include 'includes/header.php'; ?>

    <?php
    // Check for the login success flag to show a notification
    $showLoginNotification = isset($_GET['login']) && $_GET['login'] === 'success';
    if ($showLoginNotification && $currentUser):
    ?>
    <div class="login-notification-home" id="loginNotificationHome">
        <span>Welcome back, <strong><?php echo htmlspecialchars($currentUser['username']); ?></strong>!</span>
        <button type="button" class="btn-close" id="closeLoginNotificationHome" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <!-- ===== Main Content ===== -->
    <main class="pt-5 mt-4">
        <!-- Dynamic Background (particles, etc.) -->
        <div id="dynamicBackground" class="dynamic-bg">
            <canvas id="bgCanvas"></canvas>
            <div class="particles-container"></div>
        </div>

        <!-- Welcome Hero Section -->
        <section class="hero-section position-relative overflow-hidden py-5 text-white">
            <!-- Parallax Background -->
            <div class="rellax-bg" data-rellax-speed="-2"></div>
            <div class="container position-relative">
                <div class="hero-content">
                    <h1 class="display-4 fw-bold mb-3 glitch-text">
                        <span id="welcomeTyped"></span>
                    </h1>
                    <p class="lead mb-4 animated-underline">
                        Embark on your next coding adventure and level up your skills.
                    </p>
                    <div class="cta-container">
                        <button id="startGameBtn" class="btn btn-primary btn-lg pulse-effect">
                            <i class="fas fa-play me-2"></i>Jump into Game Mode
                        </button>
                        <div class="cta-decoration"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== Announcements Section ===== -->
        <section id="announcements" class="container py-5">
            <h4 class="fw-bold mb-4 text-center">Latest Updates</h4>
            <?php
            // Fetch recent announcements
            $stmt = $conn->prepare('
                SELECT a.*, au.username as author 
                FROM announcements a 
                LEFT JOIN admin_users au ON a.created_by = au.admin_id 
                WHERE a.is_active = 1 
                ORDER BY a.created_at DESC 
                LIMIT 3
            ');
            $stmt->execute();
            $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>

            <?php if (empty($announcements)): ?>
                <div class="alert alert-info">
                    No announcements yet. 
                    <a href="announcements.php" class="alert-link">Check back later</a>
                </div>
            <?php else: ?>
                <div id="announcementCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
                    <!-- Carousel Indicators -->
                    <div class="carousel-indicators">
                        <?php foreach ($announcements as $i => $a): ?>
                            <button type="button" 
                                    data-bs-target="#announcementCarousel" 
                                    data-bs-slide-to="<?php echo $i; ?>" 
                                    <?php echo $i === 0 ? 'class="active"' : ''; ?>>
                            </button>
                        <?php endforeach; ?>
                    </div>

                    <!-- Carousel Items -->
                    <div class="carousel-inner">
                        <?php foreach ($announcements as $i => $a): ?>
                            <div class="carousel-item<?php echo $i === 0 ? ' active' : ''; ?>">
                                <div class="announcement-card">
                                    <div class="announcement-icon">
                                        <i class="fas <?php echo getAnnouncementIcon($a['type'] ?? null); ?>"></i>
                                    </div>
                                    <div class="announcement-content">
                                        <h5><?php echo htmlspecialchars($a['title']); ?></h5>
                                        <p><?php echo nl2br(htmlspecialchars($a['content'])); ?></p>
                                        <small class="text-muted">
                                            By <?php echo htmlspecialchars($a['author'] ?? 'Admin'); ?> • 
                                            <?php echo date('F j, Y', strtotime($a['created_at'])); ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Carousel Controls -->
                    <button class="carousel-control-prev" type="button" data-bs-target="#announcementCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#announcementCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            <?php endif; ?>
        </section>

        <!-- ===== Quick Access Cards ===== -->
        <section class="quick-cards container py-5" aria-label="Quick Access Navigation">
            <div class="row g-4 justify-content-center">
                <?php
                $quickCards = [
                    [
                        'title' => 'Profile',
                        'icon' => 'fa-user',
                        'image' => 'images/background-1.jpg',
                        'progress' => '25%',
                        'text' => 'Complete your profile',
                        'link' => 'profile.php'
                    ],
                    [
                        'title' => 'Tutorials',
                        'icon' => 'fa-book',
                        'image' => 'images/icon-tutorial.png',
                        'progress' => '3 of 10',
                        'text' => 'Next: Variables 101',
                        'link' => 'tutorial.php'
                    ],
                    [
                        'title' => 'Mini-Game',
                        'icon' => 'fa-gamepad',
                        'image' => 'images/icon-mini-game.png',
                        'progress' => '5 of 20',
                        'text' => 'Rank: Beginner',
                        'link' => 'mini-game.php'
                    ],
                    [
                        'title' => 'Quiz',
                        'icon' => 'fa-question-circle',
                        'image' => 'images/icon-quiz.png',
                        'progress' => '12/15 Correct',
                        'text' => 'Rank: Advanced',
                        'link' => 'quiz.php'
                    ],
                    [
                        'title' => 'Challenge',
                        'icon' => 'fa-trophy',
                        'image' => 'images/icon-challenge.png',
                        'progress' => '2 of 5',
                        'text' => 'Current: Algorithm Master',
                        'link' => 'challenges.php'
                    ],
                    [
                        'title' => 'About',
                        'icon' => 'fa-info-circle',
                        'image' => 'images/icon-about.png',
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
        <section class="container py-5" id="home-quiz-analytics">
          <div class="retro-analytics-window-bg">
            <!-- Overlapping Stat Cards -->
            <div class="stat-card stat-card-best">
              <div class="stat-card-title">Best Score <span class="stat-x">&#10005;</span></div>
              <div class="stat-card-value">38/40</div>
              <div class="stat-card-desc">Your all-time best</div>
            </div>
            <div class="stat-card stat-card-recent">
              <div class="stat-card-title">Recent Game <span class="stat-x">&#10005;</span></div>
              <div class="stat-card-value">32/40</div>
              <div class="stat-card-desc">Played 2 days ago</div>
            </div>
            <div class="stat-card stat-card-top">
              <div class="stat-card-title">Top Player <span class="stat-x">&#10005;</span></div>
              <div class="stat-card-value">Areyzxc</div>
              <div class="stat-card-desc">#1 this month</div>
            </div>
            <!-- Main Window -->
            <div class="retro-analytics-window">
              <div class="window-title-bar">
                <div class="window-controls">
                  <span class="window-dot red"></span>
                  <span class="window-dot yellow"></span>
                  <span class="window-dot green"></span>
                </div>
                <span class="window-title">// QUIZ ANALYTICS & LEADERBOARD</span>
                <span class="window-x">&#10005;</span>
              </div>
              <div class="window-content">
                <div class="analytics-header">
                  <div class="analytics-tabs">
                    <button class="analytics-tab active" data-scope="alltime">All-Time</button>
                    <button class="analytics-tab" data-scope="weekly">Weekly</button>
                    <button class="analytics-tab" data-scope="monthly">Monthly</button>
                  </div>
                </div>
                <div class="analytics-body">
                  <div class="difficulty-tabs">
                    <button class="difficulty-tab active" data-difficulty="beginner">Beginner</button>
                    <button class="difficulty-tab" data-difficulty="intermediate">Intermediate</button>
                    <button class="difficulty-tab" data-difficulty="expert">Expert</button>
                  </div>
                  <div class="user-quiz-stats"></div>
                  <div class="quiz-leaderboard-list"></div>
                  <div class="play-now-section">
                    <button class="btn-play-now" onclick="window.location.href='quiz.php'">
                      <span class="btn-text">🎯 PLAY QUIZ NOW</span>
                    </button>
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
              <div class="stat-card-value">1200</div>
              <div class="stat-card-desc">Your all-time best</div>
            </div>
            <div class="stat-card stat-card-recent minigame-theme">
              <div class="stat-card-title">Recent Game <span class="stat-x">&#10005;</span></div>
              <div class="stat-card-value">950</div>
              <div class="stat-card-desc">Played 1 day ago</div>
            </div>
            <div class="stat-card stat-card-top minigame-theme">
              <div class="stat-card-title">Top Player <span class="stat-x">&#10005;</span></div>
              <div class="stat-card-value">BlueAce</div>
              <div class="stat-card-desc">#1 this week</div>
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
              <div class="stat-card-value">800</div>
              <div class="stat-card-desc">Your all-time best</div>
            </div>
            <div class="stat-card stat-card-recent challenge-theme">
              <div class="stat-card-title">Recent Game <span class="stat-x">&#10005;</span></div>
              <div class="stat-card-value">700</div>
              <div class="stat-card-desc">Played 3 days ago</div>
            </div>
            <div class="stat-card stat-card-top challenge-theme">
              <div class="stat-card-title">Top Player <span class="stat-x">&#10005;</span></div>
              <div class="stat-card-value">GoldStar</div>
              <div class="stat-card-desc">#1 this month</div>
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

        <!-- ===== Progress Dashboard ===== -->
        <section class="dashboard-widget container py-5">
            <div class="row g-4 align-items-center">
                <!-- Progress Chart -->
                <div class="col-lg-6 text-center">
                    <h4 class="fw-bold mb-3">Overall Progress</h4>
                    <canvas id="completionChart" width="200" height="200"></canvas>
                    <p class="mt-2">75% of all challenges completed</p>
                </div>

                <!-- Achievements Feed -->
                <div class="col-lg-6">
                    <h4 class="fw-bold mb-3">Recent Achievements</h4>
                    <div class="achievements-list p-3 bg-dark rounded">
                        <?php
                        $achievements = [
                            ['icon' => 'medal', 'color' => 'warning', 'text' => 'First Tutorial Completed'],
                            ['icon' => 'trophy', 'color' => 'info', 'text' => 'Quiz Mode — 90%'],
                            ['icon' => 'bolt', 'color' => 'danger', 'text' => 'Challenge Conquered: Beginner Blitz']
                        ];

                        foreach ($achievements as $achievement): ?>
                            <div class="achievement-item mb-3">
                                <i class="fas fa-<?php echo $achievement['icon']; ?> text-<?php echo $achievement['color']; ?> me-2"></i>
                                <strong><?php echo $achievement['text']; ?></strong>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- ===== Footer ===== -->
    <?php include 'includes/footer.php'; ?>

    <!-- ===== Custom Scripts ===== -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.5.1/dist/chart.min.js"></script>
    <script src="assets/js/functionhome.js"></script>
    <script src="assets/js/chart.js"></script>

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
</body>
</html>
