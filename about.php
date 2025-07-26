<?php
/**
 * ==========================================================
 * File: about.php
 * 
 * Description:
 *   - About page for Code Gaming platform
 *   - Features:
 *       • Team member showcase with interactive cards and modal
 *       • Project journey carousel and coding playlist
 *       • FAQ/info boxes for project goals, technologies, and team spirit
 *       • Feedback form and wall with pagination and like system
 *       • Credits section and floating feedback button
 *       • Responsive, modern UI with custom styles
 * 
 * Usage:
 *   - Public page for all users and visitors
 *   - Provides information about the team, project, and contact/feedback options
 * 
 * Files Included:
 *   - assets/css/about-style.css
 *   - assets/js/about-function.js
 *   - images/background.png, images/PTC.png, audio/*.mp3
 *   - includes/header.php, includes/footer.php
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
$pageTitle = "About Us";
?>

<?php include 'includes/header.php'; ?>
<!-- Project Name Banner -->
<div class="about-project-banner">
  <h1 class="project-title">CODE GAMING</h1>
</div>
<link rel="stylesheet" href="assets/css/about-style.css">
<main class="about-main-bg">
  <!-- Proponents Row -->
  <section class="about-proponents-row">
    <h2 class="section-title">Meet the Team</h2>
    <div class="proponents-row">
      <div class="proponent-card" data-proponent="1" style="background-image: url('images/background.png');">
        <div class="proponent-overlay">
          <div class="proponent-name-box">Belza, John Jaylyn I.</div>
        </div>
      </div>
      <div class="proponent-card" data-proponent="2" style="background-image: url('images/background.png');">
        <div class="proponent-overlay">
          <div class="proponent-name-box">Constantino, Alvin Jr. B.</div>
        </div>
      </div>
      <div class="proponent-card" data-proponent="3" style="background-image: url('images/background.png');">
        <div class="proponent-overlay">
          <div class="proponent-name-box">Sabangan, Ybo T.</div>
        </div>
      </div>
      <div class="proponent-card" data-proponent="4" style="background-image: url('images/background.png');">
        <div class="proponent-overlay">
          <div class="proponent-name-box">Santiago, James Aries G.</div>
        </div>
      </div>
      <div class="proponent-card" data-proponent="5" style="background-image: url('images/background.png');">
        <div class="proponent-overlay">
          <div class="proponent-name-box">Silvestre, Jesse Lei C.</div>
        </div>
      </div>
      <div class="proponent-card" data-proponent="6" style="background-image: url('images/background.png');">
        <div class="proponent-overlay">
          <div class="proponent-name-box">Valencia, Paul Dexter</div>
        </div>
      </div>
    </div>
  </section>
  <!-- Proponent Modal (Blade Runner style) -->
  <div id="proponent-modal" class="proponent-modal" style="display:none;">
    <div class="modal-content">
      <button class="modal-close futuristic-x">✕</button>
      <div class="modal-main-grid">
        <!-- Photo Section -->
        <div class="modal-photo-box">
          <img src="" alt="Proponent Photo" id="modal-photo-img">
        </div>
        <!-- Vertical Divider -->
        <div class="modal-divider-vertical"></div>
        <!-- Info Section -->
        <div class="modal-info-box">
          <div class="modal-info-header-row">
            <span class="modal-label">ROLE</span>
            <span class="modal-role" id="modal-role">Role</span>
            <span class="modal-access-label">ACCESS</span>
            <span class="modal-access-bar">▓▓▓▓▓▓▓▓▓░</span>
            <button class="modal-close-x futuristic-x">✕</button>
          </div>
          <div class="modal-info-fields">
            <div><span class="modal-label">NAME</span> <span class="modal-name" id="modal-name">Name</span></div>
            <div><span class="modal-label">AGE</span> <span class="modal-age" id="modal-age">--</span></div>
            <div><span class="modal-label">EMAIL</span> <span class="modal-email" id="modal-email">--</span></div>
            <div><span class="modal-label">CODE</span> <span class="modal-code" id="modal-code">--</span></div>
            <div class="modal-socials" id="modal-socials"></div>
            <div class="modal-funfact" id="modal-funfact">Fun fact or team spirit goes here.</div>
          </div>
        </div>
      </div>
      <!-- Horizontal Divider -->
      <div class="modal-divider-horizontal"></div>
      <!-- Authorization Section -->
      <div class="modal-mission-box">
        <div class="modal-mission" id="modal-mission">AUTHORIZATION: Default mission statement for the proponent.</div>
      </div>
      <!-- Modal Footer Bar -->
      <div class="modal-footer-bar">
        <span>PROPERTY OF CODE GAMING TEAM</span>
      </div>
    </div>
  </div>
  <!-- Main Content Grid: Carousel & Playlist -->
  <div class="about-main-grid">
    <section class="about-carousel-section">
      <h2 class="section-title">Project Journey</h2>
      <div class="about-carousel">
        <!-- Existing carousel code here -->
        <div id="carousel-docs" class="carousel slide" data-bs-ride="carousel">
          <div class="carousel-inner">
            <div class="carousel-item active">
              <img src="images/background-1.jpg" class="d-block w-100" alt="Meeting">
              <div class="carousel-caption">Initial Meeting</div>
            </div>
            <div class="carousel-item">
              <img src="images/background-2.jpg" class="d-block w-100" alt="Prototype">
              <div class="carousel-caption">Prototype Demo</div>
            </div>
          </div>
          <button class="carousel-control-prev" type="button" data-bs-target="#carousel-docs" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#carousel-docs" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
          </button>
        </div>
      </div>
    </section>
    <aside class="about-playlist-section">
      <h2 class="section-title">Coding Playlist</h2>
      <ul class="playlist-list">
        <li>
          <span class="song-title"><a href="https://artist-link.com/andromeda" target="_blank">Andromeda Sunsets</a></span>
          <audio controls src="audio/Andromeda_Sunsets.mp3"></audio>
        </li>
        <li>
          <span class="song-title"><a href="https://artist-link.com/apricot" target="_blank">Apricot</a></span>
          <audio controls src="audio/apricot.mp3"></audio>
        </li>
        <li>
          <span class="song-title"><a href="https://artist-link.com/binary" target="_blank">Binary</a></span>
          <audio controls src="audio/binary.mp3"></audio>
        </li>
        <!-- Add more songs as needed -->
      </ul>
    </aside>
  </div>
  <!-- FAQ / Info Boxes -->
  <section class="about-faq-section">
    <h2 class="section-title">Project Info</h2>
    <div class="faq-grid">
      <div class="faq-card">
        <h4>Project Goals</h4>
        <p>To create an engaging, educational, and fun platform for learning programming through games, quizzes, and challenges.</p>
      </div>
      <div class="faq-card">
        <h4>Technologies Used</h4>
        <p>PHP, MySQL, JavaScript, HTML5, CSS3, Bootstrap, and more.</p>
      </div>
      <div class="faq-card">
        <h4>Team Spirit</h4>
        <p>"Fueled by coffee, memes, and late-night coding marathons!"</p>
      </div>
    </div>
  </section>
  <!-- Feedback Form & Wall (container updated) -->
  <section class="about-feedback-section">
    <h2 class="section-title">Contact & Feedback</h2>
    <div class="feedback-container">
      <!-- Existing feedback form and wall code here -->
      <form id="feedback-form" action="api/send-feedback.php" method="POST">
        <div class="mb-3">
          <label for="feedback-name" class="form-label">Name</label>
          <input type="text" class="form-control" id="feedback-name" name="name" required>
        </div>
        <div class="mb-3">
          <label for="feedback-email" class="form-label">Email</label>
          <input type="email" class="form-control" id="feedback-email" name="email" required>
        </div>
        <div class="mb-3">
          <label for="feedback-proponent" class="form-label">To</label>
          <select class="form-select" id="feedback-proponent" name="proponent">
            <option value="jibelza@paterostechnologicalcollege.edu.ph">Belza, John Jaylyn I.</option>
            <option value="ajbconstantino@paterostechnologicalcollege.edu.ph">Constantino, Alvin Jr. B.</option>
            <option value="ytsabangan@paterostechnologicalcollege.edu.ph">Sabangan, Ybo T.</option>
            <option value="jgsantiago@paterostechnologicalcollege.edu.ph">Santiago, James Aries G.</option>
            <option value="jcsilvestre@paterostechnologicalcollege.edu.ph">Silvestre, Jesse Lei C.</option>
            <option value="psvalencia@paterostechnologicalcollege.edu.ph">Valencia, Paul Dexter</option>
          </select>
        </div>
        <div class="mb-3">
          <label for="feedback-message" class="form-label">Message</label>
          <textarea class="form-control" id="feedback-message" name="message" rows="3" required></textarea>
        </div>
        <button type="submit" class="btn btn-retro">Send Feedback</button>
      </form>
      <div class="feedback-wall mt-4">
        <h6>Recent Feedback</h6>
        <ul id="feedback-list">
          <?php
          // Display the latest 5 feedback messages from the database
          $conn = Database::getInstance()->getConnection();
          // Pagination logic
          $page = isset($_GET['feedback_page']) ? max(1, intval($_GET['feedback_page'])) : 1;
          $perPage = 5;
          $offset = ($page - 1) * $perPage;
          // Count total feedbacks
          $totalResult = $conn->query("SELECT COUNT(*) as total FROM feedback_messages");
          $totalFeedback = $totalResult ? $totalResult->fetch()['total'] : 0;
          $totalPages = ceil($totalFeedback / $perPage);
          // Fetch feedbacks for current page
          $feedbackResult = $conn->query("SELECT id, sender_name, sender_email, message, sent_at, likes FROM feedback_messages ORDER BY sent_at DESC LIMIT $perPage OFFSET $offset");
          $hasFeedback = false;
          if (
            isset(
              $feedbackResult
            ) && $feedbackResult->rowCount() > 0) {
            while ($row = $feedbackResult->fetch()) {
              $hasFeedback = true;
              $id = (int)$row['id'];
              $name = htmlspecialchars($row['sender_name']);
              $email = htmlspecialchars($row['sender_email']);
              $msg = htmlspecialchars($row['message']);
              $time = date('Y-m-d H:i', strtotime($row['sent_at']));
              $likes = (int)$row['likes'];
              echo "<li data-feedback-id='{$id}'><strong>{$name}</strong> (<span class='feedback-email'>{$email}</span>) <span class='feedback-time'>[{$time}]</span>: {$msg} <br><button class='like-btn' data-id='{$id}'><i class='fas fa-thumbs-up'></i> <span class='like-count'>{$likes}</span></button></li>";
            }
          }
          if (!$hasFeedback) {
            echo '<li>No feedback yet. Be the first to send one!</li>';
          }
          // Pagination controls
          if ($totalPages > 1) {
            echo "<div class='feedback-pagination'>";
            if ($page > 1) {
              echo "<button class='feedback-page-btn' data-page='" . ($page - 1) . "'>&laquo; Prev</button> ";
            }
            for ($p = 1; $p <= $totalPages; $p++) {
              $active = $p === $page ? 'active' : '';
              echo "<button class='feedback-page-btn $active' data-page='$p'>$p</button> ";
            }
            if ($page < $totalPages) {
              echo "<button class='feedback-page-btn' data-page='" . ($page + 1) . "'>Next &raquo;</button>";
            }
            echo "</div>";
          }
          ?>
        </ul>
      </div>
    </div>
  </section>
  <!-- Credits Section -->
  <section class="about-credits-section">
    <div class="credits-content">
      <img src="images/PTC.png" alt="School Logo" class="school-logo">
      <span class="credits-text">Built with <span class="heart">❤️</span> by the Code Gaming Team</span>
    </div>
  </section>
  <!-- Floating Feedback Button -->
  <button id="floating-feedback-btn" class="floating-feedback-btn">
    <i class="fas fa-comment-dots"></i> Send Feedback
  </button>
</main>
<script src="assets/js/about-function.js"></script>
<?php include 'includes/footer.php'; ?> 