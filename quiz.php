<?php
/**
 * ==========================================================
 * File: quiz.php
 * 
 * Description:
 *   - Interactive Quiz page for Code Gaming platform
 *   - Features:
 *       • Welcome and difficulty selection screens with instructions
 *       • Multiple-choice questions with pixel-art styled UI
 *       • 3 difficulty levels (Beginner, Intermediate, Expert)
 *       • 7 lives system and expert timer mode
 *       • 40 programming questions (HTML, CSS, JS, Python, Bootstrap, C++, Java)
 *       • Pixel-art styled UI and animated elements
 *       • Leaderboard, feedback, and end-of-quiz modals
 *       • Guest nickname input and validation
 *       • Responsive design and sound/pixel art credits
 * 
 * Usage:
 *   - Accessible to all users and guests
 *   - Allows users to test programming knowledge and compete for high scores
 * 
 * Author: [Santiago]
 * Last Updated: [July 22, 2025]
 * -- Code Gaming Team --
 * ==========================================================
 */

// Include required files
require_once 'includes/Database.php';
require_once 'includes/Auth.php';

// Initialize core components
$db = Database::getInstance();
$auth = Auth::getInstance();

// Set page title for the header
$pageTitle = "Quiz";
?>

<?php include 'includes/header.php'; ?>
<link rel="stylesheet" href="assets/css/quiz-style.css">
<main class="quiz-main-bg">
  <!-- Pixel Font Title Banner -->
  <div class="quiz-title-banner">
    <h1 class="quiz-title">QUIZ CODE GAMING</h1>
    <div class="quiz-subtitle">Test Your Programming Knowledge!</div>
  </div>

  <!-- Quiz Container -->
  <section class="quiz-container">
    <!-- Welcome Intro Screen -->
    <div class="quiz-welcome-screen">
      <div class="welcome-content">
        <div class="welcome-icon">🚀</div>
        <h2 class="welcome-title">Ready to Code?</h2>
        <p class="welcome-message">
          Embark on an epic journey through the world of programming! 
          Choose your difficulty, test your skills, and climb the leaderboard.
        </p>
        <div class="welcome-features">
          <div class="feature-item">
            <span class="feature-icon">🎯</span>
            <span class="feature-text">3 Difficulty Levels</span>
          </div>
          <div class="feature-item">
            <span class="feature-icon">💖</span>
            <span class="feature-text">7 Lives System</span>
          </div>
          <div class="feature-item">
            <span class="feature-icon">⏰</span>
            <span class="feature-text">Expert Timer Mode</span>
          </div>
          <div class="feature-item">
            <span class="feature-icon">🏆</span>
            <span class="feature-text">Leaderboards</span>
          </div>
        </div>
        <button class="btn-pixel welcome-start-btn">Begin Adventure</button>
      </div>
    </div>

    <!-- Start Screen -->
    <div class="quiz-start-screen" style="display:none;">
      <h2 class="quiz-section-title">Select Your Challenge</h2>
      
      <div class="difficulty-options">
        <button class="btn-pixel difficulty-btn" data-difficulty="beginner">
          <div class="difficulty-icon">🎯</div>
          <div class="difficulty-info">
            <div class="difficulty-name">Beginner</div>
            <div class="difficulty-desc">Perfect for newcomers</div>
          </div>
        </button>
        <button class="btn-pixel difficulty-btn" data-difficulty="intermediate">
          <div class="difficulty-icon">⚡</div>
          <div class="difficulty-info">
            <div class="difficulty-name">Intermediate</div>
            <div class="difficulty-desc">For experienced coders</div>
          </div>
        </button>
        <button class="btn-pixel difficulty-btn" data-difficulty="expert">
          <div class="difficulty-icon">🚀</div>
          <div class="difficulty-info">
            <div class="difficulty-name">Expert</div>
            <div class="difficulty-desc">Ultimate challenge</div>
          </div>
        </button>
      </div>
      
      <div class="quiz-intro">
        <p>Ready to test your programming knowledge? Choose your difficulty level and start the challenge!</p>
        <p>Each quiz contains 40 questions covering HTML, CSS, JavaScript, Python, Bootstrap, C++, and Java.</p>
      </div>
      
      <?php if (!$auth->isLoggedIn()): ?>
      <div class="nickname-input">
        <label for="guest-nickname">Enter Your Nickname:</label>
        <input type="text" id="guest-nickname" maxlength="20" placeholder="Choose a cool nickname" />
        <span id="nickname-status" class="nickname-status"></span>
      </div>
      <?php endif; ?>
      
      <div class="start-controls">
        <button class="btn-pixel instructions-btn" title="View Instructions">❓</button>
        <button class="btn-pixel start-quiz-btn">Start Quiz</button>
      </div>
    </div>

    <!-- Quiz In-Progress (hidden until quiz starts) -->
    <div class="quiz-in-progress" style="display:none;">
      <div class="quiz-status-bar">
        <div class="quiz-hearts"><!-- ♥♥♥♥♥♥♥ --></div>
        <div class="quiz-progress">Q1/40</div>
        <div class="quiz-timer" style="display:none;">00:30</div>
      </div>
      
      <div class="quiz-game-area">
        <div class="quiz-spaceship">
          <!-- Spaceship sprite/animation placeholder -->
        </div>
        <div class="quiz-question-box">
          <!-- Question text (pixel-art styled) -->
        </div>
        <div class="quiz-choices">
          <!-- Multiple-choice or True/False buttons -->
        </div>
      </div>
    </div>
  </section>

  <!-- Feedback Modal (Correct/Wrong) -->
  <div id="quiz-feedback-modal" class="quiz-modal" style="display:none;">
    <div class="modal-content">
      <!-- Enhanced feedback content -->
    </div>
  </div>

  <!-- End of Quiz Modal -->
  <div id="quiz-end-modal" class="quiz-modal" style="display:none;">
    <div class="modal-content">
      <!-- Enhanced end screen content -->
    </div>
  </div>

  <!-- Leaderboard Modal/Sidebar -->
  <div id="quiz-leaderboard-modal" class="quiz-modal" style="display:none;">
    <div class="modal-content">
      <!-- Enhanced leaderboard content -->
    </div>
  </div>

  <!-- Pixel Art & Sound Credits Placeholder -->
  <section class="quiz-credits-section">
    <div class="credits-content">
      <span>Pixel art & sound credits will be listed here.</span>
    </div>
  </section>
</main>

 <!-- ===== Footer ===== -->
 <?php include 'includes/footer.php'; ?>
 
<script>
<?php if ($auth->isLoggedIn()): ?>
window.CG_USER_ID = <?php echo json_encode($auth->getUserId()); ?>;
window.CG_USERNAME = <?php echo json_encode($auth->getUsername()); ?>;
<?php endif; ?>
</script>
<script src="assets/js/quiz.js"></script>
<?php include 'includes/footer.php'; ?>