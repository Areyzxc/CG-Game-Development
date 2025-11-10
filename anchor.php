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
  <title>Code Game | Gamified Programming Platform</title>
  
  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="/CodeGaming/assets/images/diffeasy.ico">
  <link rel="icon" type="image/png" sizes="32x32" href="/CodeGaming/assets/images/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/CodeGaming/assets/images/favicon-16x16.png">
  <link rel="apple-touch-icon" sizes="180x180" href="/CodeGaming/assets/images/apple-touch-icon.png">
  <link rel="manifest" href="/CodeGaming/site.webmanifest">
  
  <!-- SEO Meta Tags -->
  <meta name="description" content="Code Game is a gamified web-based educational platform for learning programming through interactive challenges, quizzes, tutorials, and mini-games. Track your progress and compete on leaderboards!">
  <meta name="keywords" content="coding, programming, game, challenges, quizzes, tutorials, leaderboard, education, learn to code">
  <meta name="author" content="Code Gaming Team">
  <!-- Open Graph Meta Tags -->
  <meta property="og:title" content="Code Game | Gamified Programming Platform">
  <meta property="og:description" content="Learn programming with interactive challenges, quizzes, tutorials, and mini-games. Track your progress and rise on the leaderboard!">
  <meta property="og:image" content="https://codegaming.example.com/images/PTC.png">
  <meta property="og:url" content="https://codegaming.example.com/">
  <meta property="og:type" content="website">
  <!-- Twitter Card Meta Tags -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="Code Game | Gamified Programming Platform">
  <meta name="twitter:description" content="Learn programming with interactive challenges, quizzes, tutorials, and mini-games. Track your progress and rise on the leaderboard!">
  <meta name="twitter:image" content="https://codegaming.example.com/images/PTC.png">

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
  <a href="https://www.paterostechnologicalcollege.edu.ph/" target="_blank" rel="noopener noreferrer" aria-label="Visit Pateros Technological College website">
    <div class="school-logo" tabindex="0" aria-label="School logo">
      <img src="assets/images/PTC.png" alt="Pateros Technological College Logo" class="logo-img">
    </div>
  </a>

  <!-- Three.js Background Container -->
  <div id="three-container"></div>

  <!-- GitHub Icon in Header -->
  <div class="github-corner">
    <a href="https://github.com/Areyzxc/Game-Development" target="_blank" rel="noopener noreferrer" aria-label="View source on GitHub">
      <i class="fab fa-github"></i>
    </a>
  </div>

  <!-- Main Content -->
  <main class="main-content">
    <div class="container">
      <div class="content-wrapper">
        <div class="title-container">
          <section class="showcase">
            <div class="video-text-container">
              <div class="video-text">
                <video autoplay loop muted playsinline class="video-bg">
                  <source src="videos/code-bg-1.mp4" type="video/mp4">
                </video>
                <span>CODE GAMING</span>
              </div>
            </div>
          </section>
          <span id="typed-text" class="typed-text"></span>
          <div class="title-decoration"></div>
          
          <!-- Code Editor Container -->
          <div class="code-editor-container mt-4">
            <div class="code-editor">
              <div class="code-header">
                <span class="code-dot red"></span>
                <span class="code-dot yellow"></span>
                <span class="code-dot green"></span>
                <span class="code-title">code.js</span>
              </div>
              <div class="code-body">
                <pre><code id="typing-code"></code></pre>
              </div>
            </div>
          </div>
        </div>
    <div class="auth-buttons" role="group" aria-label="Authentication options">
      <?php if ($auth->isLoggedIn()): ?>
      <?php
        $profileLink = $auth->isAdmin() ? 'admin_dashboard.php' : 'profile.php';
        $welcomeText = "Welcome, " . htmlspecialchars($currentUser['username']);
      ?>
      <a href="<?php echo $profileLink; ?>" class="welcome-pixel-button" title="Go to your profile" tabindex="0" aria-label="Go to your profile">
        <?php if (!empty($currentUser['profile_picture'])): ?>
          <img src="<?php echo htmlspecialchars($currentUser['profile_picture']); ?>" alt="Profile Picture" class="profile-picture">
        <?php else: ?>
          <i class="fas fa-user-circle profile-icon" aria-hidden="true"></i>
        <?php endif; ?>
        <span class="welcome-text" aria-live="polite"><?php echo $welcomeText; ?></span>
        <div class="pixel-cursor" aria-hidden="true"></div>
      </a>
      <a href="logout.php" class="btn btn-outline-secondary" tabindex="0" aria-label="Logout">
        <i class="fa fa-sign-out-alt me-2" aria-hidden="true"></i>Logout
      </a>
      <?php if (!$auth->isAdmin()): ?>
        <a href="home_page.php" class="btn btn-success btn-lg" tabindex="0" aria-label="Play the game">
          <i class="fa fa-gamepad me-2" aria-hidden="true"></i>Let's Play
        </a>
      <?php endif; ?>
      <?php else: ?>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#signInModal" aria-label="Sign Up" tabindex="0">
          <i class="fa fa-user-plus me-2" aria-hidden="true"></i>Sign Up
        </button>
        <button class="btn btn-outline-light" data-bs-toggle="modal" data-bs-target="#loginModal" aria-label="Log In" tabindex="0">
          <i class="fa fa-sign-in-alt me-2" aria-hidden="true"></i>Log In
        </button>
        <a href="home_page.php" class="btn btn-success btn-lg" tabindex="0" aria-label="Play the game">
          <i class="fa fa-gamepad me-2" aria-hidden="true"></i>Let's Play
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
          <strong>Coding Game</strong> is a web-based gamified educational platform designed to teach programming fundamentals through interactive challenges, real-time feedback, and engaging visuals. Whether you're a beginner or a curious learner, embark on coding adventures where logic becomes your superpower.
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
            <p class="small">Practice, take quizzes, mini-games, and challenge yourself—all gamified to boost your coding skills.</p>
          </div>
          <div class="col-md-4">
            <i class="fas fa-chart-line fa-2x mb-2 text-warning"></i>
            <h5>Track Your Progress</h5>
            <p class="small">Advance through obstacles, earn points, and rise on the leaderboard!</p>
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
  <div id="gameModesCarousel" class="carousel slide" data-bs-ride="carousel" aria-label="Game modes preview carousel">
      <!-- Carousel Indicators -->
      <div class="carousel-indicators" role="tablist">
        <button type="button" data-bs-target="#gameModesCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Challenge Mode" tabindex="0"></button>
        <button type="button" data-bs-target="#gameModesCarousel" data-bs-slide-to="1" aria-label="Quiz Mode" tabindex="0"></button>
        <button type="button" data-bs-target="#gameModesCarousel" data-bs-slide-to="2" aria-label="Practice Mode" tabindex="0"></button>
        <button type="button" data-bs-target="#gameModesCarousel" data-bs-slide-to="3" aria-label="Mini-Game Mode" tabindex="0"></button>
      </div>

      <!-- Carousel Items -->
      <div class="carousel-inner">
        <!-- Challenge Mode -->
        <div class="carousel-item active">
          <div class="row align-items-center">
            <div class="col-md-6">
              <div class="video-container">
                <video class="game-mode-video" muted loop controls
                      aria-label="Challenge Mode Demo Video"
                      aria-describedby="challenge-video-desc"
                      id="challengeVideo">
                  <source src="videos/Challenges.mp4" type="video/mp4">
                  <track kind="captions" label="English" srclang="en" src="videos/captions/challenge-mode.vtt" default>
                  <track kind="descriptions" label="English Descriptions" srclang="en" src="videos/descriptions/challenge-mode.vtt">
                  Your browser does not support the video tag.
                  <p id="challenge-video-desc" class="visually-hidden">
                    A demonstration of Challenge Mode showing a coding challenge interface with a timer and code editor.
                  </p>
                </video>
                <div class="play-overlay" role="button" tabindex="0" aria-label="Play or pause Challenge Mode video" aria-pressed="false" onclick="document.getElementById('challengeVideo').play(); this.style.display='none';">
                  <i class="fas fa-play-circle" aria-hidden="true"></i>
                  <span class="visually-hidden">Play or pause Challenge Mode video</span>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="game-mode-content">
                <h3><i class="fas fa-trophy text-warning"></i> Challenge Mode</h3>
                <p class="lead">Test your skills with real coding challenges</p>
                <ul class="feature-list">
                  <li><i class="fas fa-check-circle text-success"></i> Progressive difficult questions with case scenarios</li>
                  <li><i class="fas fa-check-circle text-success"></i> Real-time code execution</li>
                  <li><i class="fas fa-check-circle text-success"></i> Timer and instant feedback</li>
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
                <video class="game-mode-video" muted loop controls
                      aria-label="Quiz Mode Demo Video"
                      aria-describedby="quiz-video-desc"
                      id="quizVideo">
                  <source src="videos/quiz-mode.mp4" type="video/mp4">
                  <track kind="captions" label="English" srclang="en" src="videos/captions/quiz-mode.vtt" default>
                  <track kind="descriptions" label="English Descriptions" srclang="en" src="videos/descriptions/quiz-mode.vtt">
                  Your browser does not support the video tag.
                  <p id="quiz-video-desc" class="visually-hidden">
                    A demonstration of Quiz Mode showing multiple-choice questions about programming concepts.
                  </p>
                </video>
                <div class="play-overlay" role="button" tabindex="0" aria-label="Play or pause Quiz Mode video" aria-pressed="false" onclick="document.getElementById('quizVideo').play(); this.style.display='none';">
                  <i class="fas fa-play-circle" aria-hidden="true"></i>
                  <span class="visually-hidden">Play or pause Quiz Mode video</span>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="game-mode-content">
                <h3><i class="fas fa-question-circle text-info"></i> Quiz Mode</h3>
                <p class="lead">Test your knowledge with interactive quizzes</p>
                <ul class="feature-list">
                  <li><i class="fas fa-check-circle text-success"></i> Multiple choice questions</li>
                  <li><i class="fas fa-check-circle text-success"></i> Three different difficulties to choose from</li>
                  <li><i class="fas fa-check-circle text-success"></i> Score tracking</li>
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
                <video class="game-mode-video" muted loop controls
                      aria-label="Tutorial Mode Demo Video"
                      aria-describedby="tutorial-video-desc"
                      id="tutorialVideo">
                  <source src="videos/tutorial-mode.mp4" type="video/mp4">
                  <track kind="captions" label="English" srclang="en" src="videos/captions/tutorial-mode.vtt" default>
                  <track kind="descriptions" label="English Descriptions" srclang="en" src="videos/descriptions/tutorial-mode.vtt">
                  Your browser does not support the video tag.
                  <p id="tutorial-video-desc" class="visually-hidden">
                    A demonstration of Tutorial Mode showing step-by-step coding lessons with interactive examples.
                  </p>
                </video>
                <div class="play-overlay" role="button" tabindex="0" aria-label="Play or pause Tutorial Mode video" aria-pressed="false" onclick="document.getElementById('tutorialVideo').play(); this.style.display='none';">
                  <i class="fas fa-play-circle" aria-hidden="true"></i>
                  <span class="visually-hidden">Play or pause Tutorial Mode video</span>
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
                  <li><i class="fas fa-check-circle text-success"></i> Different topics to choose from</li>
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
                <video class="game-mode-video" muted loop controls
                      aria-label="Mini-Game Mode Demo Video"
                      aria-describedby="minigame-video-desc"
                      id="minigameVideo">
                  <source src="videos/mini-game-mode.mp4" type="video/mp4">
                  <track kind="captions" label="English" srclang="en" src="videos/captions/mini-game-mode.vtt" default>
                  <track kind="descriptions" label="English Descriptions" srclang="en" src="videos/descriptions/mini-game-mode.vtt">
                  Your browser does not support the video tag.
                  <p id="minigame-video-desc" class="visually-hidden">
                    A demonstration of Mini-Game Mode showing various coding mini-games that teach programming concepts through gameplay.
                  </p>
                </video>
                <div class="play-overlay" role="button" tabindex="0" aria-label="Play or pause Mini-Game Mode video" aria-pressed="false" onclick="document.getElementById('minigameVideo').play(); this.style.display='none';">
                  <i class="fas fa-play-circle" aria-hidden="true"></i>
                  <span class="visually-hidden">Play or pause Mini-Game Mode video</span>
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
    
    <!-- CTA Button -->
    <div class="text-center mt-5">
      <a href="home_page.php" class="cta-button btn btn-primary btn-lg px-5 py-3">
        <i class="fas fa-play-circle me-2"></i> Ready to Code? Let's Play!
      </a>
    </div>
  </div>
</section>

<!-- FAQ Section -->
<section id="faq" class="py-5 bg-dark text-light">
  <div class="container">
    <h2 class="text-center mb-5">Frequently Asked Questions</h2>
    <div class="accordion" id="faqAccordion">
      <!-- FAQ Item 1 -->
      <div class="accordion-item bg-transparent border-light">
        <h3 class="accordion-header" id="faq1">
          <button class="accordion-button bg-dark text-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
            Is this available for beginners?
          </button>
        </h3>
        <div id="collapse1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
          <div class="accordion-body text-light">
            Yes, this platform is designed to be accessible for beginners. We offer a range of modules and challenges that are tailored to different skill levels, ensuring that everyone can learn at their own pace and progress at their own speed.
          </div>
        </div>
      </div>

      <!-- FAQ Item 2 -->
      <div class="accordion-item bg-transparent border-light">
        <h3 class="accordion-header" id="faq2">
          <button class="accordion-button collapsed bg-dark text-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
            How do I sign up?
          </button>
        </h3>
        <div id="collapse2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
          <div class="accordion-body text-light">
            Signing up is easy! Just click the 'Sign Up' button on the above screen, fill in your details, and you'll be ready to start your coding journey in no time. It's completely free!
          </div>
        </div>
      </div>

      <!-- FAQ Item 3 -->
      <div class="accordion-item bg-transparent border-light">
        <h3 class="accordion-header" id="faq3">
          <button class="accordion-button collapsed bg-dark text-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3" aria-expanded="false" aria-controls="collapse3">
            Can guests play without signing up?
          </button>
        </h3>
        <div id="collapse3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
          <div class="accordion-body text-light">
            Yes, you can explore some features as a guest, but we recommend signing up to save your progress, unlock achievements, and appear on the leaderboards using your username. Your game data will be saved across devices when you're signed in.
          </div>
        </div>
      </div>

      <!-- FAQ Item 4 -->
      <div class="accordion-item bg-transparent border-light">
        <h3 class="accordion-header" id="faq4">
          <button class="accordion-button collapsed bg-dark text-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4" aria-expanded="false" aria-controls="collapse4">
            What programming languages are covered?
          </button>
        </h3>
        <div id="collapse4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
          <div class="accordion-body text-light">
            We currently cover JavaScript, Python, HTML, Bootstrap, Java, C++, and CSS with a total of 70 modules across different difficulty levels. We're constantly adding new languages and content based on user feedback soon!
          </div>
        </div>
      </div>

      <!-- FAQ Item 5 -->
      <div class="accordion-item bg-transparent border-light">
        <h3 class="accordion-header" id="faq5">
          <button class="accordion-button collapsed bg-dark text-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapse5" aria-expanded="false" aria-controls="collapse5">
            Is the platform mobile-friendly?
          </button>
        </h3>
        <div id="collapse5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
          <div class="accordion-body text-light">
            Absolutely! Code Game is fully responsive and works great on all devices including smartphones, tablets, and desktops. Practice coding anytime, anywhere!
          </div>
        </div>
      </div>

      <!-- FAQ Item 6 -->
      <div class="accordion-item bg-transparent border-light">
        <h3 class="accordion-header" id="faq6">
          <button class="accordion-button collapsed bg-dark text-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapse6" aria-expanded="false" aria-controls="collapse6">
            How are scores and progress tracked?
          </button>
        </h3>
        <div id="collapse6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
          <div class="accordion-body text-light">
            Your progress is automatically saved to our secure database as you complete challenges. You can track your scores, badges, and ranking on your profile page. Share your progress with friends and climb the leaderboards!
          </div>
        </div>
      </div>

      <!-- FAQ Item 7 -->
      <div class="accordion-item bg-transparent border-light">
        <h3 class="accordion-header" id="faq7">
          <button class="accordion-button collapsed bg-dark text-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapse7" aria-expanded="false" aria-controls="collapse7">
            Who can I contact for support or to report issues?
          </button>
        </h3>
        <div id="collapse7" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
          <div class="accordion-body text-light">
            We'd love to hear from you! For support, bug reports, or feature requests, please email us at <a href="mailto:support@codegaming.ptc.edu.ph" class="text-info">support@codegaming.ptc.edu.ph</a> or open an issue on our <a href="https://github.com/Areyzxc/Game-Development" target="_blank" class="text-info">GitHub repository</a>. We typically respond within 24-48 hours.
          </div>
        </div>
      </div>

      <!-- FAQ Item 8 -->
      <div class="accordion-item bg-transparent border-light">
        <h3 class="accordion-header" id="faq8">
          <button class="accordion-button collapsed bg-dark text-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapse8" aria-expanded="false" aria-controls="collapse8">
            Is this available for experts?
          </button>
        </h3>
        <div id="collapse8" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
          <div class="accordion-body text-light">
            Yes, this platform is designed to be accessible for experts. The challenge page is for experts to test their skills and knowledge. We'll wait for you there.
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

  <!-- Bootstrap Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.4/dist/js/bootstrap.bundle.min.js"></script>
  <!-- rellax.js for smooth scrolling effects -->
  <script src="https://cdn.jsdelivr.net/npm/rellax@1.12.1/rellax.min.js"></script>
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
    // Initialize tooltips and other DOM-dependent code when document is ready
    document.addEventListener('DOMContentLoaded', function() {
      // Get the code element
      const codeElement = document.getElementById('typing-code');
      
      // Initialize tooltips
      var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
      var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl, {
          trigger: 'hover'
        });
      });

      // Initialize rellax for smooth scrolling effects if elements exist
      if (document.querySelectorAll('.rellax').length > 0) {
        var rellax = new Rellax('.rellax', {
          // Global settings
          speed: -2, // Speed of the parallax effect
          center: true, // Center the parallax elements
          round: true, // Round values for better performance
          breakpoints: [576, 768, 1024] // Breakpoints for responsive behavior
        });
      }

    // Sample code to be typed
    const codeSnippets = [
        `// Calculate Fibonacci sequence
function fibonacci(n) {
  if (n <= 1) return n;
  return fibonacci(n - 1) + fibonacci(n - 2);
}

// Find max number in array
const findMax = (arr) => {
  return Math.max(...arr);
};`,
        `// Simple React component
import React, { useState } from 'react';

const Counter = () => {
  const [count, setCount] = useState(0);
  
  return (
    <div className="counter">
      <p>Count: {count}</p>
      <button onClick={() => setCount(count + 1)}>
        Increment
      </button>
    </div>
  );
};`,
        `// Async function example
async function fetchData() {
  try {
    const response = await fetch('https://api.example.com/data');
    const data = await response.json();
    return data;
  } catch (error) {
    console.error('Error:', error);
    throw error;
  }
}`,
        `// Java Syntax
public class Main {
    public static void main(String[] args) {
        System.out.println("Hello, World!");
    }
}`,
        `// JavaScript Syntax Event Handler
function handleClick() {
    alert('Button clicked!');
}`,
        `// Python Snake Game
import pygame

# Initialize Pygame
pygame.init()

# Set up the display
screen = pygame.display.set_mode((640, 480))

# Game loop
running = True
while running:
    for event in pygame.event.get():
        if event.type == pygame.QUIT:
            running = False

# Quit Pygame
pygame.quit()`,
        `// C++ Syntax
#include <iostream>

int main() {
    std::cout << "Hello, World!" << std::endl;
    return 0;
}`,
        `// CSS Syntax Coloring
body {
    background-color: #f0f0f0;
    color: #333;
    font-family: Arial, sans-serif;
}`,
        `// HTML Syntax Coloring
<!DOCTYPE html>
<html>
<head>
    <title>Code Gaming</title>
</head>
<body>
    <h1>Welcome To Code Gaming!</h1>
    <p>Enjoy Your Day</p>
</body>
</html>`,
        `// Java Grade Calculator
public class GradeCalculator {
    public static void main(String[] args) {
        int score = 85;
        char grade;
        
        if (score >= 90) {
            grade = 'A';
        } else if (score >= 80) {
            grade = 'B';
        } else if (score >= 70) {
            grade = 'C';
        } else if (score >= 60) {
            grade = 'D';
        } else {
            grade = 'F';
        }
        
        System.out.println("Grade: " + grade);
    }
}`,
        `// JavaScript Mathematics
function calculateArea(radius) {
    return Math.PI * radius * radius;
}`,
        `// JavaScript Division
function divide(a, b) {
    if (b === 0) {
        throw new Error('Cannot divide by zero');
    }
    return a / b;
}`,
        `// C++ Addition
int add(int a, int b) {
    return a + b;
}`,
        `// Python Library
import math

# Calculate square root
result = math.sqrt(16)
print(result)`,
        `// C++ Subtraction
int subtract(int a, int b) {
    return a - b;
}`,
        `// Python Tic-Tac-Toe Game
int subtract(int a, int b) {
    return a - b;
}`,
        `// Python Name Calling
print("Welcome to Code Gaming!")`,
        `// Bootstrap Column
<div class="row">
    <div class="col-md-6">Column 1</div>
    <div class="col-md-6">Column 2</div>
</div>`,
        `// Bootstrap Row
<div class="row">
    <div class="col-md-6">Column 3</div>
    <div class="col-md-6">Column 4</div>
</div>`,
        `// Bootstrap Modal
<div class="modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modal title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Modal body text goes here.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>`,
];

      // Function to add syntax highlighting
      function highlightCode(code) {
        // Keywords to highlight
        const keywords = [
          'function', 'return', 'if', 'else', 'for', 'while', 'const', 'let', 'var',
          'async', 'await', 'try', 'catch', 'import', 'export', 'default', 'class',
          'new', 'this', 'true', 'false', 'null', 'undefined'
        ];
        
        // Patterns for syntax highlighting
        const patterns = [
          {
            regex: new RegExp(`\\b(${keywords.join('|')})\\b`, 'g'),
            replace: '<span class="keyword">$&</span>'
          },
          {
            regex: /(['"])(?:\\.|[^\\])*?\1/g,
            replace: '<span class="string">$&</span>'
          },
          {
            regex: /\b(\d+)\b/g,
            replace: '<span class="number">$&</span>'
          },
          {
            regex: /(\/\/.*$)/gm,
            replace: '<span class="comment">$&</span>'
          },
          {
            regex: /(\/\*[\s\S]*?\*\/)/g,
            replace: '<span class="comment">$&</span>'
          },
          {
            regex: /\b(\w+)(?=\s*\()/g,
            replace: '<span class="function">$&</span>'
          },
          {
            regex: /([{}()\[\];,.])/g,
            replace: '<span class="punctuation">$&</span>'
          },
          {
            regex: /([=+\-*\/%<>!&|^~?:]+)/g,
            replace: '<span class="operator">$&</span>'
          }
        ];

        let highlighted = code;
        patterns.forEach(pattern => {
          highlighted = highlighted.replace(pattern.regex, pattern.replace);
        });

        // Add line numbers and wrap in spans
        return highlighted.split('\n').map((line, i) => {
          return `<span class="line">${line || ' '}</span>`;
        }).join('\n');
      }

      // Function to type out the code
      async function typeCode(code, element) {
        const lines = code.split('\n');
        let currentLine = 0;
        
        while (currentLine < lines.length) {
          const line = lines[currentLine];
          let charIndex = 0;
          
          // Add the current line to the display
          if (currentLine > 0) {
            element.innerHTML += '\n';
          }
          
          // Type each character with a small delay
          while (charIndex < line.length) {
            const char = line[charIndex];
            let charElement = document.createElement('span');
            charElement.textContent = char;
            element.appendChild(charElement);
            
            // Scroll to bottom
            element.scrollTop = element.scrollHeight;
            
            // Add a small delay between characters (faster for spaces)
            await new Promise(resolve => setTimeout(resolve, char === ' ' ? 20 : 30));
            charIndex++;
          }
          
          // Add line break and cursor for next line
          if (currentLine < lines.length - 1) {
            element.innerHTML += '\n';
          }
          
          currentLine++;
          
          // Add a slightly longer delay between lines
          if (currentLine < lines.length) {
            await new Promise(resolve => setTimeout(resolve, 100));
          }
        }
        
        // Add blinking cursor at the end
        element.innerHTML += '<span class="cursor blink">|</span>';
      }

      // Function to simulate typing effect
      async function typeCode(code, element) {
        element.innerHTML = '';
        const codeArray = code.split('');
        
        for (let i = 0; i < codeArray.length; i++) {
          element.innerHTML = code.substring(0, i + 1);
          // Add a small delay between characters for typing effect
          await new Promise(resolve => setTimeout(resolve, 10));
        }
      }

      // Start the typing animation
      let currentSnippet = 0;
      
      async function startTyping() {
        while (true) {
          const highlightedCode = highlightCode(codeSnippets[currentSnippet]);
          codeElement.innerHTML = '';
          await typeCode(codeSnippets[currentSnippet], codeElement);
          
          // Wait a bit before moving to the next snippet
          await new Promise(resolve => setTimeout(resolve, 3000));
          
          // Move to next snippet
          currentSnippet = (currentSnippet + 1) % codeSnippets.length;
        }
      }
      
      // Start the animation after a short delay
      setTimeout(startTyping, 1000);
    }); // End of DOMContentLoaded
  </script>

  <?php include 'includes/footer.php'; ?>
  <script src="assets/js/video-text-effect.js"></script>
</body>
</html>

