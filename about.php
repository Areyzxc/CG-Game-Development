<?php
// Include visitor tracking
require_once 'includes/track_visitor.php';

/**
 * ==========================================================
 * File: about.php
 * 
 * Description:
 *   - Enhanced About page for Code Gaming platform
 *   - Northside festival 2013 inspired design with pastel teal background
 *   - Features:
 *       • Hero banner with project overview
 *       • Team member showcase with interactive cards and modal
 *       • Project journey timeline with milestones
 *       • FAQ section with search functionality
 *       • Coding playlist with audio controls
 *       • Feedback form and wall with AJAX
 *       • Tech stack showcase and project statistics
 *       • Animated footer with silhouette design
 *       • Full responsive design with ScrollReveal animations
 * 
 * Design Inspiration: Northside festival 2013
 *   - Pastel teal/green background with subtle texture
 *   - Orange/red accents for headers and buttons
 *   - Clean grid layout with white cards
 *   - Bold typography with large sans-serif fonts
 *   - Interactive elements and social feeds
 * 
 * Author: Code Gaming Team
 * Last Updated: September 28, 2025
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
$conn = $db->getConnection();

// Set page title for the header
$pageTitle = "About Us";
?>

<?php include 'includes/header.php'; ?>
<link rel="stylesheet" href="assets/css/about-style.css">
<link rel="stylesheet" href="https://unpkg.com/scrollreveal@4.0.9/dist/scrollreveal.min.js">

<!-- Northside Festival Inspired Header -->
<header class="northside-header">
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-md-8">
        <h1 class="page-title">About Us</h1>
        <div class="search-container">
          <input type="text" id="faq-search" class="search-input" placeholder="Søg i FAQ...">
          <button class="search-btn"><i class="fas fa-search"></i></button>
        </div>
      </div>
      <div class="col-md-4 text-end">
        <a href="home_page.php" class="home-btn"><i class="fas fa-home"></i></a>
        <img src="assets/images/PTC.png" alt="PTC Logo" class="header-logo">
      </div>
    </div>
  </div>
</header>

<!-- Hero Banner Section -->
<section class="hero-section">
    <div class="row">
      <div class="col-md-6">
        <div class="hero-content">
          <h1 class="hero-title">Code Gaming</h1>
          <p class="hero-description">Our journey in creating a web-based gamified system for coding skills through interactive mini-games, tutorials, quizzes, and challenges.</p>
              <div class="hero-stats">
                <?php
                try {
                    // Fetch project statistics
                    $statsQuery = $conn->prepare("SELECT stat_name, stat_value, stat_label, icon FROM project_statistics WHERE is_active = 1 ORDER BY display_order LIMIT 3");
                    $statsQuery->execute();
                    while ($stat = $statsQuery->fetch()) {
                        echo "<div class='stat-item'>";
                        echo "<i class='{$stat['icon']}'></i>";
                        echo "<span class='stat-number'>{$stat['stat_value']}+</span>";
                        echo "<span class='stat-label'>{$stat['stat_label']}</span>";
                        echo "</div>";
                    }
                } catch (PDOException $e) {
                    // Fallback stats if table doesn't exist
                    echo "<div class='stat-item'>";
                    echo "<i class='fas fa-users'></i>";
                    echo "<span class='stat-number'>6+</span>";
                    echo "<span class='stat-label'>Team Members</span>";
                    echo "</div>";
                    echo "<div class='stat-item'>";
                    echo "<i class='fas fa-code'></i>";
                    echo "<span class='stat-number'>100+</span>";
                    echo "<span class='stat-label'>Hours Coded</span>";
                    echo "</div>";
                    echo "<div class='stat-item'>";
                    echo "<i class='fas fa-trophy'></i>";
                    echo "<span class='stat-number'>1+</span>";
                    echo "<span class='stat-label'>Project</span>";
                    echo "</div>";
                }
                ?>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="hero-image">
              <img src="assets/images/anchor.png" alt="Code Gaming Hero" class="img-fluid">
            </div>
          </div>
        </div>
      </div>
</section>

<main class="main-content">
  <!-- Team Showcase Section (Inspired by 'Seneste Kunstnere') -->
  <section class="team-section" id="team">
    <div class="container">
      <h2 class="section-title">Our Team</h2>
      <div class="row team-grid">
        <?php
        try {
            // Fetch team members from database
            $teamQuery = $conn->prepare("SELECT * FROM team_members WHERE is_active = 1 ORDER BY display_order");
            $teamQuery->execute();
            $hasMembers = false;
            while ($member = $teamQuery->fetch()) {
                $hasMembers = true;
            echo "
            <div class='col-lg-4 col-md-6 mb-4' data-reveal='fade-up'>
              <div class='team-card' data-bs-toggle='modal' data-bs-target='#teamModal' 
                   data-name='{$member['name']}' 
                   data-role='{$member['role']}'
                   data-bio='{$member['bio']}'
                   data-email='{$member['email']}'
                   data-photo='{$member['photo']}'
                   data-funfact='{$member['fun_fact']}'
                   data-mission='{$member['mission_statement']}'
                   data-facebook='{$member['facebook_url']}'
                   data-instagram='{$member['instagram_url']}'
                   data-github='{$member['github_url']}'>
                <div class='team-image'>
                  <img src='{$member['photo']}' class='img-fluid' alt='{$member['name']}'>
                  <div class='team-overlay'>
                    <div class='team-social'>
                      " . ($member['facebook_url'] ? "<a href='{$member['facebook_url']}' target='_blank'><i class='fab fa-facebook'></i></a>" : "") . "
                      " . ($member['instagram_url'] ? "<a href='{$member['instagram_url']}' target='_blank'><i class='fab fa-instagram'></i></a>" : "") . "
                      " . ($member['github_url'] ? "<a href='{$member['github_url']}' target='_blank'><i class='fab fa-github'></i></a>" : "") . "
                    </div>
                  </div>
                </div>
                <div class='team-info'>
                  <h4>{$member['name']}</h4>
                  <p class='team-role'>{$member['role']}</p>
                  <p class='team-bio'>" . substr($member['bio'], 0, 100) . "...</p>
                </div>
              </div>
            </div>";
            }
            
            // If no members found, show fallback
            if (!$hasMembers) {
                // Fallback team members
                $fallbackMembers = [
                    ['name' => 'Belza, John Jaylyn I.', 'role' => 'Lead Developer', 'bio' => 'Passionate about creating innovative solutions and leading the development team.'],
                    ['name' => 'Constantino, Alvin Jr. B.', 'role' => 'UI/UX Designer', 'bio' => 'Focused on creating beautiful and user-friendly interfaces.'],
                    ['name' => 'Sabangan, Ybo T.', 'role' => 'Backend Developer', 'bio' => 'Expert in server-side development and database management.'],
                    ['name' => 'Santiago, James Aries G.', 'role' => 'Frontend Developer', 'bio' => 'Specializes in creating interactive and responsive web experiences.'],
                    ['name' => 'Silvestre, Jesse Lei C.', 'role' => 'QA & Tester', 'bio' => 'Ensures quality and reliability of all project features.'],
                    ['name' => 'Valencia, Paul Dexter', 'role' => 'Documentation & Support', 'bio' => 'Maintains comprehensive documentation and user support.']
                ];
                
                foreach ($fallbackMembers as $member) {
                    echo "
                    <div class='col-lg-4 col-md-6 mb-4' data-reveal='fade-up'>
                      <div class='team-card'>
                        <div class='team-image'>
                          <img src='assets/images/background.png' class='img-fluid' alt='{$member['name']}'>
                        </div>
                        <div class='team-info'>
                          <h4>{$member['name']}</h4>
                          <p class='team-role'>{$member['role']}</p>
                          <p class='team-bio'>{$member['bio']}</p>
                        </div>
                      </div>
                    </div>";
                }
            }
        } catch (PDOException $e) {
            // Fallback team members if database error
            $fallbackMembers = [
                ['name' => 'Belza, John Jaylyn I.', 'role' => 'Lead Developer', 'bio' => 'Passionate about creating innovative solutions and leading the development team.'],
                ['name' => 'Constantino, Alvin Jr. B.', 'role' => 'UI/UX Designer', 'bio' => 'Focused on creating beautiful and user-friendly interfaces.'],
                ['name' => 'Sabangan, Ybo T.', 'role' => 'Backend Developer', 'bio' => 'Expert in server-side development and database management.'],
                ['name' => 'Santiago, James Aries G.', 'role' => 'Frontend Developer', 'bio' => 'Specializes in creating interactive and responsive web experiences.'],
                ['name' => 'Silvestre, Jesse Lei C.', 'role' => 'QA & Tester', 'bio' => 'Ensures quality and reliability of all project features.'],
                ['name' => 'Valencia, Paul Dexter', 'role' => 'Documentation & Support', 'bio' => 'Maintains comprehensive documentation and user support.']
            ];
            
            foreach ($fallbackMembers as $member) {
                echo "
                <div class='col-lg-4 col-md-6 mb-4' data-reveal='fade-up'>
                  <div class='team-card'>
                    <div class='team-image'>
                      <img src='assets/images/background.png' class='img-fluid' alt='{$member['name']}'>
                    </div>
                    <div class='team-info'>
                      <h4>{$member['name']}</h4>
                      <p class='team-role'>{$member['role']}</p>
                      <p class='team-bio'>{$member['bio']}</p>
                    </div>
                  </div>
                </div>";
            }
        }
        ?>
      </div>
    </div>
  </section>
  <!-- Team Modal -->
  <div class="modal fade" id="teamModal" tabindex="-1" aria-labelledby="teamModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="teamModalLabel">Team Member</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-4">
              <img id="modal-member-photo" src="" alt="Team Member" class="img-fluid rounded">
            </div>
            <div class="col-md-8">
              <h4 id="modal-member-name"></h4>
              <p class="text-orange" id="modal-member-role"></p>
              <p id="modal-member-bio"></p>
              <p><strong>Fun Fact:</strong> <span id="modal-member-funfact"></span></p>
              <p><strong>Mission:</strong> <span id="modal-member-mission"></span></p>
              <div class="social-links" id="modal-member-social"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Project Journey Timeline -->
  <section class="timeline-section" id="journey">
    <div class="container">
      <h2 class="section-title">Project Journey</h2>
      <div class="timeline-carousel">
        <div id="timelineCarousel" class="carousel slide" data-bs-ride="carousel">
          <div class="carousel-inner">
            <?php
            // Fetch timeline events
            $timelineQuery = $conn->prepare("SELECT * FROM timeline_events ORDER BY event_date ASC");
            $timelineQuery->execute();
            $events = $timelineQuery->fetchAll();
            $chunks = array_chunk($events, 3); // Show 3 events per slide
            $first = true;
            foreach ($chunks as $chunk) {
                echo "<div class='carousel-item " . ($first ? 'active' : '') . "'>";
                echo "<div class='row'>";
                foreach ($chunk as $event) {
                    echo "
                    <div class='col-md-4'>
                      <div class='timeline-card' data-reveal='fade-up'>
                        <div class='timeline-icon'>
                          <i class='{$event['icon']}'></i>
                        </div>
                        <h5>{$event['title']}</h5>
                        <p class='timeline-date'>" . date('M Y', strtotime($event['event_date'])) . "</p>
                        <p>{$event['description']}</p>
                      </div>
                    </div>";
                }
                echo "</div></div>";
                $first = false;
            }
            ?>
          </div>
          <button class="carousel-control-prev" type="button" data-bs-target="#timelineCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#timelineCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
          </button>
        </div>
      </div>
    </div>
  </section>

  <!-- Tech Stack & Playlist Grid -->
  <section class="content-grid-section">
    <div class="container">
      <div class="row">
        <!-- Tech Stack -->
        <div class="col-lg-8">
          <div class="tech-stack-card" data-reveal="fade-right">
            <h3>Technologies We Use</h3>
            <div class="tech-icons">
              <div class="tech-item"><i class="fab fa-html5"></i><span>HTML5</span></div>
              <div class="tech-item"><i class="fab fa-css3-alt"></i><span>CSS3</span></div>
              <div class="tech-item"><i class="fab fa-js-square"></i><span>JavaScript</span></div>
              <div class="tech-item"><i class="fab fa-php"></i><span>PHP</span></div>
              <div class="tech-item"><i class="fas fa-database"></i><span>MySQL</span></div>
              <div class="tech-item"><i class="fab fa-bootstrap"></i><span>Bootstrap</span></div>
              <div class="tech-item"><i class="fab fa-threejs"></i><span>Three.js</span></div>
            </div>
          </div>
        </div>
        <!-- Coding Playlist -->
        <div class="col-lg-4">
          <div class="playlist-card" data-reveal="fade-left">
            <h3>Coding Playlist</h3>
            <div class="playlist-items">
              <?php
              $playlistQuery = $conn->prepare("SELECT * FROM coding_playlist ORDER BY display_order LIMIT 5");
              $playlistQuery->execute();
              while ($song = $playlistQuery->fetch()) {
                  echo "
                  <div class='playlist-item'>
                    <div class='song-info'>
                      <h6>{$song['title']}</h6>
                      <small>{$song['artist']}</small>
                    </div>
                    <audio controls>
                      <source src='{$song['file_path']}' type='audio/mpeg'>
                    </audio>
                  </div>";
              }
              ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- FAQ Section with Search -->
  <section class="faq-section" id="faq">
    <div class="container">
      <h2 class="section-title">Frequently Asked Questions</h2>
      <div class="faq-search-container">
        <input type="text" id="faqSearch" class="faq-search-input" placeholder="Search FAQ...">
        <i class="fas fa-search"></i>
      </div>
      <div class="accordion" id="faqAccordion">
        <?php
        $faqQuery = $conn->prepare("SELECT * FROM faq_items WHERE is_featured = 1 ORDER BY display_order");
        $faqQuery->execute();
        $index = 0;
        while ($faq = $faqQuery->fetch()) {
            $index++;
            echo "
            <div class='accordion-item faq-item' data-reveal='fade-up'>
              <h2 class='accordion-header' id='heading{$index}'>
                <button class='accordion-button collapsed' type='button' data-bs-toggle='collapse' 
                        data-bs-target='#collapse{$index}' aria-expanded='false' aria-controls='collapse{$index}'>
                  {$faq['question']}
                </button>
              </h2>
              <div id='collapse{$index}' class='accordion-collapse collapse' 
                   aria-labelledby='heading{$index}' data-bs-parent='#faqAccordion'>
                <div class='accordion-body'>
                  {$faq['answer']}
                </div>
              </div>
            </div>";
        }
        ?>
      </div>
    </div>
  </section>

  <!-- Project Statistics -->
  <section class="stats-section">
    <div class="container">
      <h2 class="section-title">Project Impact</h2>
      <div class="row stats-grid">
        <?php
        $statsQuery = $conn->prepare("SELECT * FROM project_statistics WHERE is_active = 1 ORDER BY display_order");
        $statsQuery->execute();
        while ($stat = $statsQuery->fetch()) {
            echo "
            <div class='col-lg-2 col-md-4 col-6 mb-4' data-reveal='fade-up'>
              <div class='stat-card'>
                <div class='stat-icon'>
                  <i class='{$stat['icon']}'></i>
                </div>
                <div class='stat-number'>{$stat['stat_value']}</div>
                <div class='stat-label'>{$stat['stat_label']}</div>
              </div>
            </div>";
        }
        ?>
      </div>
    </div>
  </section>
  <!-- Feedback Section -->
  <section class="feedback-section" id="feedback">
    <div class="container">
      <div class="row">
        <div class="col-lg-6">
          <div class="feedback-form-card" data-reveal="fade-right">
            <h3>Send Us Feedback</h3>
            <form id="feedback-form" action="api/send-feedback.php" method="POST">
              <div class="mb-3">
                <label for="feedback-name" class="form-label text-black">Name:</label>
                <input type="text" class="form-control" id="feedback-name" name="name" required>
              </div>
              <div class="mb-3">
                <label for="feedback-email" class="form-label text-black">Email:</label>
                <input type="email" class="form-control" id="feedback-email" name="email" required>
              </div>
              <div class="mb-3">
                <label for="feedback-message" class="form-label text-black">Message:</label>
                <textarea class="form-control" id="feedback-message" name="message" rows="4" required placeholder="Share your thoughts about our project..."></textarea>
              </div>
              <div class="form-actions">
                <button type="submit" class="btn btn-primary" id="submit-feedback-btn">
                  <i class="fas fa-paper-plane me-2"></i>Send Feedback
                </button>
                <button type="button" class="btn btn-secondary ms-2" id="cancel-feedback-btn">
                  <i class="fas fa-times me-2"></i>Cancel
                </button>
              </div>
              <div id="feedback-status" class="mt-3" style="display: none;"></div>
            </form>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="feedback-wall-card" data-reveal="fade-left">
            <h3>Recent Feedback</h3>
            <div class="feedback-wall">
              <div id="feedback-list">
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
                
                // Display feedback messages with proper like counts from feedback_likes table
                if ($feedbackResult && $feedbackResult->rowCount() > 0) {
                    while ($row = $feedbackResult->fetch()) {
                        $id = (int)$row['id'];
                        $name = htmlspecialchars($row['sender_name']);
                        $msg = htmlspecialchars($row['message']);
                        $time = date('M j, Y', strtotime($row['sent_at']));
                        
                        // Get actual like count from feedback_likes table
                        $likeQuery = $conn->prepare("SELECT COUNT(*) as like_count FROM feedback_likes WHERE feedback_id = ?");
                        $likeQuery->execute([$id]);
                        $likeResult = $likeQuery->fetch();
                        $likes = (int)$likeResult['like_count'];
                        
                        echo "
                        <div class='feedback-item' data-feedback-id='{$id}'>
                          <div class='feedback-header'>
                            <strong>{$name}</strong>
                            <small>{$time}</small>
                          </div>
                          <p>{$msg}</p>
                          <div class='feedback-actions'>
                            <button class='like-btn btn btn-sm btn-outline-danger' data-feedback-id='{$id}'>
                              <i class='fas fa-heart'></i> 
                              <span class='like-count'>{$likes}</span>
                            </button>
                          </div>
                        </div>";
                    }
                } else {
                    echo '<p class="no-feedback">No feedback yet. Be the first to share your thoughts!</p>';
                }
                ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

<!-- Animated Footer with Silhouette Design -->
<footer class="northside-footer">
  <div class="footer-silhouette">
    <svg viewBox="0 0 1200 200" class="silhouette-svg">
      <path d="M0,200 L0,100 L50,90 L100,95 L150,85 L200,90 L250,80 L300,85 L350,75 L400,80 L450,70 L500,75 L550,65 L600,70 L650,60 L700,65 L750,55 L800,60 L850,50 L900,55 L950,45 L1000,50 L1050,40 L1100,45 L1150,35 L1200,40 L1200,200 Z" fill="currentColor"/>
    </svg>
  </div>
  <div class="container">
    <div class="row footer-content">
      <div class="col-md-6">
        <div class="footer-info">
          <img src="assets/images/PTC.png" alt="PTC Logo" class="footer-logo">
          <h4>Code Gaming</h4>
          <p>Built with <span class="heart">❤️</span> by the Code Gaming Team</p>
          <p>Pateros Technological College</p>
        </div>
      </div>
      <div class="col-md-6">
        <div class="footer-links">
          <h5>Connect With Us</h5>
          <div class="social-links">
            <a href="#" class="social-link"><i class="fab fa-facebook"></i></a>
            <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
            <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
            <a href="#" class="social-link"><i class="fab fa-github"></i></a>
          </div>
          <div class="footer-credits">
            <p>&copy; 2025 Code Gaming Team. All rights reserved.</p>
            <p>Inspired by Northside festival 2013 design</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</footer>

<!-- Floating Feedback Button -->
<button id="floating-feedback-btn" class="floating-feedback-btn">
  <i class="fas fa-plus"></i>
</button>

<!-- Scripts -->
<script src="https://unpkg.com/scrollreveal@4.0.9/dist/scrollreveal.min.js"></script>
<script src="assets/js/about-function.js"></script>
<?php include 'includes/footer.php'; ?>
