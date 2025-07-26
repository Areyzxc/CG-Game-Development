<?php
/**
 * ==========================================================
 * File: anchor.php
 * 
 * Description:
 *   - Welcome/landing page for Code Gaming platform
 *   - Features:
 *       • School logo and branding
 *       • Three.js animated background and GSAP/Typed.js effects
 *       • Auth buttons for login, signup, and play
 *       • Game modes preview carousel with video demos
 *       • Section explaining platform features and benefits
 *       • Responsive, modern UI with Bootstrap and custom styles
 * 
 * Usage:
 *   - Public entry page for all users and visitors
 *   - Gateway to login, registration, and main game modes
 * 
 * Files Included:
 *   - assets/css/style.css
 *   - assets/css/anchor-style.css
 *   - assets/js/script.js
 *   - assets/js/three-background.js
 *   - includes/footer.php
 *   - External: Bootstrap, Font Awesome, Three.js, GSAP, Typed.js
 * 
 * Author: [Santiago]
 * Last Updated: [July 22, 2025]
 * -- Code Gaming Team --
 * ==========================================================
 */
require_once 'includes/ErrorHandler.php';
require_once 'includes/Auth.php';
require_once 'includes/Database.php';

ErrorHandler::getInstance();
$auth = Auth::getInstance();
$db = Database::getInstance();

$currentUser = $auth->isLoggedIn() ? $auth->getCurrentUser() : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <!-- Ensure proper scaling on mobile devices -->
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Code Game</title>

  <!-- Bootstrap 5 CSS -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
    rel="stylesheet"
    integrity="sha384-…"
    crossorigin="anonymous"
  >

  <!-- Font Awesome for icons -->
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
  >

  <!-- Global styles for variables and footer -->
  <link rel="stylesheet" href="assets/css/style.css">

  <!-- Your custom styles -->
  <link rel="stylesheet" href="assets/css/anchor-style.css">

  <!-- Three.js -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
  <!-- GSAP for smooth animations -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
  <!-- Three.js Font Loader -->
  <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/FontLoader.js"></script>
  <!-- Three.js Text Geometry -->
  <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/geometries/TextGeometry.js"></script>
  <!-- Typed.js for typing effect -->
  <script src="https://unpkg.com/typed.js@2.1.0/dist/typed.umd.js"></script>

</head>
<body data-bs-spy="scroll" data-bs-target="#mainNavbar" data-bs-offset="70" tabindex="0">
  <?php include 'api/track-visitor.php'; ?>
  
  <!-- School Logo -->
  <a href="https://www.paterostechnologicalcollege.edu.ph/" target="_blank" rel="noopener noreferrer">
    <div class="school-logo">
      <img src="images/PTC.png" alt="PTC Logo" class="logo-img">
    </div>
  </a>

  <!-- Three.js Background Container -->
  <div id="three-container"></div>

  <!-- Main Content -->
  <main class="main-content">
    <div class="container">
      <div class="content-wrapper">
        <div class="title-container">
          <h1 class="game-title">CODE GAME</h1>
          <span id="typed-text" class="typed-text"></span>
          <div class="title-decoration"></div>
        </div>
        <div class="auth-buttons">
          <?php if ($auth->isLoggedIn()): ?>
            <?php
                // Welcome button for both user and admin
                $profileLink = $auth->isAdmin() ? 'admin_dashboard.php' : 'profile.php';
                $welcomeText = "Welcome, " . htmlspecialchars($currentUser['username']);
            ?>
            <a href="<?php echo $profileLink; ?>" class="welcome-pixel-button" title="Go to your profile">
                <?php if (!empty($currentUser['profile_picture'])): ?>
                    <img src="<?php echo htmlspecialchars($currentUser['profile_picture']); ?>" alt="Profile Picture" class="profile-picture">
                <?php else: ?>
                    <i class="fas fa-user-circle profile-icon"></i>
                <?php endif; ?>
                <span class="welcome-text"><?php echo $welcomeText; ?></span>
                <div class="pixel-cursor"></div>
            </a>
            <a href="logout.php" class="btn btn-outline-secondary">
                <i class="fa fa-sign-out-alt me-2"></i>Logout
            </a>
            
            <?php if (!$auth->isAdmin()): // Only show "Let's Play" for non-admins (i.e., 'user' role) ?>
                <a href="home_page.php" class="btn btn-success btn-lg">
                    <i class="fa fa-gamepad me-2"></i>Let's Play
                </a>
            <?php endif; ?>

          <?php else: // Visitor ?>
              <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#signInModal">
                  <i class="fa fa-user-plus me-2"></i>Sign Up
              </button>
              <button class="btn btn-outline-light" data-bs-toggle="modal" data-bs-target="#loginModal">
                  <i class="fa fa-sign-in-alt me-2"></i>Log In
              </button>
              <a href="home_page.php" class="btn btn-success btn-lg">
                  <i class="fa fa-gamepad me-2"></i>Let's Play
              </a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </main>

  <!-- Anchor Page Scroll-Triggered Section -->
<section id="synopsis" class="extra-section py-5 bg-dark text-light">
  <div class="container">
    <div class="row justify-content-center text-center">
      <div class="col-md-8">
        <h2 class="mb-3">What is a <span class="text-warning">Coding Game</span>?</h2>
        <p class="lead">
          <strong>Coding Game</strong> is a gamified web-based educational platform designed to teach programming fundamentals through interactive challenges, real-time feedback, and engaging visuals. Whether you're a beginner or a curious learner, embark on coding adventures where logic becomes your superpower.
        </p>
        <hr class="border-light my-4">
        <div class="row">
          <div class="col-md-4">
            <i class="fas fa-code fa-2x mb-2 text-info"></i>
            <h5>Interactive Challenges</h5>
            <p class="small">Write, run, and solve programming puzzles with built-in feedback and guidance.</p>
          </div>
          <div class="col-md-4">
            <i class="fas fa-gamepad fa-2x mb-2 text-success"></i>
            <h5>Game Modes</h5>
            <p class="small">Practice, take quizzes, and challenge yourself—all gamified to boost your coding skills.</p>
          </div>
          <div class="col-md-4">
            <i class="fas fa-chart-line fa-2x mb-2 text-warning"></i>
            <h5>Track Your Progress</h5>
            <p class="small">Advance through levels, earn points, and rise on the leaderboard!</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Game Modes Preview Section -->
<section id="game-modes-preview" class="game-modes-section py-5 bg-dark text-light">
  <div class="container">
    <div class="row justify-content-center text-center mb-5">
      <div class="col-md-8">
        <h2 class="mb-3">Explore Our <span class="text-warning">Game Modes</span></h2>
        <p class="lead">Experience different ways to learn coding through our interactive game modes</p>
      </div>
    </div>

    <!-- Video Carousel -->
    <div id="gameModesCarousel" class="carousel slide" data-bs-ride="carousel">
      <!-- Carousel Indicators -->
      <div class="carousel-indicators">
        <button type="button" data-bs-target="#gameModesCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Challenge Mode"></button>
        <button type="button" data-bs-target="#gameModesCarousel" data-bs-slide-to="1" aria-label="Quiz Mode"></button>
        <button type="button" data-bs-target="#gameModesCarousel" data-bs-slide-to="2" aria-label="Practice Mode"></button>
        <button type="button" data-bs-target="#gameModesCarousel" data-bs-slide-to="3" aria-label="Mini-Game Mode"></button>
      </div>

      <!-- Carousel Items -->
      <div class="carousel-inner">
        <!-- Challenge Mode -->
        <div class="carousel-item active">
          <div class="row align-items-center">
            <div class="col-md-6">
              <div class="video-container">
                <video class="game-mode-video" muted loop>
                  <source src="videos/challenge-mode.mp4" type="video/mp4">
                  Your browser does not support the video tag.
                </video>
                <div class="play-overlay">
                  <i class="fas fa-play-circle"></i>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="game-mode-content">
                <h3><i class="fas fa-trophy text-warning"></i> Challenge Mode</h3>
                <p class="lead">Test your skills with real coding challenges</p>
                <ul class="feature-list">
                  <li><i class="fas fa-check-circle text-success"></i> Progressive difficulty levels</li>
                  <li><i class="fas fa-check-circle text-success"></i> Real-time code execution</li>
                  <li><i class="fas fa-check-circle text-success"></i> Instant feedback and hints</li>
                </ul>
                <a href="challenges.php" class="btn btn-primary mt-3">Try Challenge Mode</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Quiz Mode -->
        <div class="carousel-item">
          <div class="row align-items-center">
            <div class="col-md-6">
              <div class="video-container">
                <video class="game-mode-video" muted loop>
                  <source src="videos/quiz-mode.mp4" type="video/mp4">
                  Your browser does not support the video tag.
                </video>
                <div class="play-overlay">
                  <i class="fas fa-play-circle"></i>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="game-mode-content">
                <h3><i class="fas fa-question-circle text-info"></i> Quiz Mode</h3>
                <p class="lead">Test your knowledge with interactive quizzes</p>
                <ul class="feature-list">
                  <li><i class="fas fa-check-circle text-success"></i> Multiple choice questions</li>
                  <li><i class="fas fa-check-circle text-success"></i> Code snippet analysis</li>
                  <li><i class="fas fa-check-circle text-success"></i> Performance tracking</li>
                </ul>
                <a href="quiz.php" class="btn btn-primary mt-3">Try Quiz Mode</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Tutorial Mode -->
        <div class="carousel-item">
          <div class="row align-items-center">
            <div class="col-md-6">
              <div class="video-container">
                <video class="game-mode-video" muted loop>
                  <source src="videos/tutorial-mode.mp4" type="video/mp4">
                  Your browser does not support the video tag.
                </video>
                <div class="play-overlay">
                  <i class="fas fa-play-circle"></i>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="game-mode-content">
                <h3><i class="fas fa-code text-success"></i> Tutorial Mode</h3>
                <p class="lead">Master coding concepts at your own pace</p>
                <ul class="feature-list">
                  <li><i class="fas fa-check-circle text-success"></i> Step-by-step tutorials</li>
                  <li><i class="fas fa-check-circle text-success"></i> Interactive code examples</li>
                  <li><i class="fas fa-check-circle text-success"></i> Tracks your progress</li>
                </ul>
                <a href="tutorial.php" class="btn btn-primary mt-3">Try Tutorial Mode</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Mini-Game Mode -->
        <div class="carousel-item">
          <div class="row align-items-center">
            <div class="col-md-6">
              <div class="video-container">
                <video class="game-mode-video" muted loop>
                  <source src="videos/mini-game-mode.mp4" type="video/mp4">
                  Your browser does not support the video tag.
                </video>
                <div class="play-overlay">
                  <i class="fas fa-play-circle"></i>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="game-mode-content">
                <h3><i class="fas fa-gamepad text-danger"></i> Mini-Game Mode</h3>
                <p class="lead">Learn coding through fun mini-games</p>
                <ul class="feature-list">
                  <li><i class="fas fa-check-circle text-success"></i> Engaging gameplay</li>
                  <li><i class="fas fa-check-circle text-success"></i> Code-based puzzles</li>
                  <li><i class="fas fa-check-circle text-success"></i> Achievement system</li>
                </ul>
                <a href="mini-game.php" class="btn btn-primary mt-3">Try Mini-Game Mode</a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Carousel Controls -->
      <button class="carousel-control-prev" type="button" data-bs-target="#gameModesCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#gameModesCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
      </button>
    </div>
  </div>
</section>

  <!-- Bootstrap Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.4/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Custom JS -->
  <script src="assets/js/script.js"></script>
  <script src="assets/js/three-background.js"></script>
  <script>
    // Typed.js Initialization
    const typed = new Typed('#typed-text', {
      strings: ["Let's learn coding skills and programming language."],
      typeSpeed: 50,
      backSpeed: 25,
      loop: true,
      showCursor: true,
      cursorChar: '_',
    });

    // Load font before initializing Three.js background
    const fontLoader = new THREE.FontLoader();
    fontLoader.load('https://threejs.org/examples/fonts/helvetiker_regular.typeface.json', function(font) {
        window.codeGameFont = font;
        new ThreeBackground();
    });
  </script>

  <?php include 'includes/footer.php'; ?>

</body>
</html>
