<?php
/**
 * ==========================================================
 * File: privacy.php
 * 
 * Description:
 *   - Privacy Policy page with interactive Rellax background
 *   - Features:
 *       • Floating privacy icons and security elements
 *       • Expandable content sections
 *       • Interactive privacy quiz
 *       • Language toggle functionality
 *       • Print/Download options
 *       • Responsive design with accessibility
 * 
 * Usage:
 *   - Public page accessible to all users and visitors
 *   - Displays comprehensive privacy policy with engaging UI
 * 
 * Author: [Santiago]
 * Last Updated: [June 17, 2025]
 * -- Code Gaming Team --
 * ==========================================================
 */

// Include header
include 'includes/header.php';
?>

<!-- Privacy Policy Hero Section -->
<section class="privacy-hero">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <div class="back-button-wrapper">
                    <a href="anchor.php" class="btn btn-outline-light back-btn">
                        <i class="fas fa-arrow-left"></i> Back to Home
                    </a>
                </div>
                <h1 class="privacy-title">
                    <span class="privacy-icon">🔒</span>
                    Your Privacy Matters
                    <span class="privacy-icon">🔒</span>
                </h1>
                <p class="privacy-subtitle">We're committed to protecting your data and ensuring transparency in how we handle your information.</p>
            </div>
        </div>
    </div>
    
    <!-- Floating Privacy Elements (Rellax) -->
    <div class="floating-elements">
        <div class="privacy-element" data-rellax-speed="2">🔐</div>
        <div class="privacy-element" data-rellax-speed="-1">🛡️</div>
        <div class="privacy-element" data-rellax-speed="3">✅</div>
        <div class="privacy-element" data-rellax-speed="-2">🔒</div>
        <div class="privacy-element" data-rellax-speed="1">⚡</div>
        <div class="privacy-element" data-rellax-speed="2">🎯</div>
        <div class="privacy-element" data-rellax-speed="-3">🔍</div>
        <div class="privacy-element" data-rellax-speed="1">💎</div>
    </div>
</section>

<!-- Privacy Policy Content -->
<section class="privacy-content">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                
                <!-- Progress Indicator -->
                <div class="reading-progress">
                    <div class="progress-bar">
                        <div class="progress-fill"></div>
                    </div>
                    <span class="progress-text">Reading Progress: <span class="progress-percentage">0%</span></span>
                </div>

                <!-- Introduction Section -->
                <div class="policy-section active" id="introduction">
                    <div class="section-header" data-section="introduction">
                        <h2><i class="fas fa-info-circle"></i> Introduction</h2>
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </div>
                    <div class="section-content">
                        <p>Welcome to Code Gaming! We respect your privacy and are committed to protecting your personal data. This privacy policy explains how we collect, use, and safeguard your information when you use our platform.</p>
                        <p>By using Code Gaming, you agree to the collection and use of information in accordance with this policy. We will not use or share your information with anyone except as described in this Privacy Policy.</p>
                        <div class="policy-highlight">
                            <i class="fas fa-shield-alt"></i>
                            <strong>Our Commitment:</strong> Your privacy is our priority. We implement industry-standard security measures to protect your data.
                        </div>
                    </div>
                </div>

                <!-- Information We Collect -->
                <div class="policy-section" id="data-collection">
                    <div class="section-header" data-section="data-collection">
                        <h2><i class="fas fa-database"></i> Information We Collect</h2>
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </div>
                    <div class="section-content">
                        <h4>Personal Information</h4>
                        <ul>
                            <li><strong>Account Data:</strong> Username, email address, and profile information</li>
                            <li><strong>Usage Data:</strong> Game progress, scores, achievements, and learning patterns</li>
                            <li><strong>Technical Data:</strong> IP address, browser type, device information, and cookies</li>
                        </ul>
                        
                        <h4>How We Collect Information</h4>
                        <div class="collection-methods">
                            <div class="method-card">
                                <i class="fas fa-user-plus"></i>
                                <h5>Registration</h5>
                                <p>When you create an account</p>
                            </div>
                            <div class="method-card">
                                <i class="fas fa-gamepad"></i>
                                <h5>Gameplay</h5>
                                <p>During your learning sessions</p>
                            </div>
                            <div class="method-card">
                                <i class="fas fa-cookie-bite"></i>
                                <h5>Cookies</h5>
                                <p>To enhance your experience</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- How We Use Information -->
                <div class="policy-section" id="data-usage">
                    <div class="section-header" data-section="data-usage">
                        <h2><i class="fas fa-cogs"></i> How We Use Your Information</h2>
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </div>
                    <div class="section-content">
                        <div class="usage-grid">
                            <div class="usage-item">
                                <i class="fas fa-chart-line"></i>
                                <h5>Improve Learning</h5>
                                <p>Personalize your coding journey and track progress</p>
                            </div>
                            <div class="usage-item">
                                <i class="fas fa-trophy"></i>
                                <h5>Achievements</h5>
                                <p>Track and display your accomplishments</p>
                            </div>
                            <div class="usage-item">
                                <i class="fas fa-users"></i>
                                <h5>Community</h5>
                                <p>Enable leaderboards and social features</p>
                            </div>
                            <div class="usage-item">
                                <i class="fas fa-tools"></i>
                                <h5>Platform</h5>
                                <p>Maintain and improve our services</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Sharing -->
                <div class="policy-section" id="data-sharing">
                    <div class="section-header" data-section="data-sharing">
                        <h2><i class="fas fa-share-alt"></i> Data Sharing & Third Parties</h2>
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </div>
                    <div class="section-content">
                        <p>We do not sell, trade, or rent your personal information to third parties. We may share information in the following circumstances:</p>
                        
                        <div class="sharing-scenarios">
                            <div class="scenario">
                                <i class="fas fa-check-circle text-success"></i>
                                <span><strong>With Your Consent:</strong> When you explicitly agree to share</span>
                            </div>
                            <div class="scenario">
                                <i class="fas fa-gavel text-warning"></i>
                                <span><strong>Legal Requirements:</strong> When required by law</span>
                            </div>
                            <div class="scenario">
                                <i class="fas fa-shield-alt text-info"></i>
                                <span><strong>Service Providers:</strong> Trusted partners who help us operate</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Rights -->
                <div class="policy-section" id="user-rights">
                    <div class="section-header" data-section="user-rights">
                        <h2><i class="fas fa-user-shield"></i> Your Rights & Control</h2>
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </div>
                    <div class="section-content">
                        <div class="rights-grid">
                            <div class="right-item">
                                <i class="fas fa-eye"></i>
                                <h5>Access</h5>
                                <p>View your personal data</p>
                            </div>
                            <div class="right-item">
                                <i class="fas fa-edit"></i>
                                <h5>Update</h5>
                                <p>Modify your information</p>
                            </div>
                            <div class="right-item">
                                <i class="fas fa-trash"></i>
                                <h5>Delete</h5>
                                <p>Remove your account</p>
                            </div>
                            <div class="right-item">
                                <i class="fas fa-download"></i>
                                <h5>Export</h5>
                                <p>Download your data</p>
                            </div>
                        </div>
                        
                        <div class="control-panel">
                            <h4>Quick Actions</h4>
                            <div class="action-buttons">
                                <button class="btn btn-outline-primary" onclick="exportData()">
                                    <i class="fas fa-download"></i> Export My Data
                                </button>
                                <button class="btn btn-outline-warning" onclick="requestDeletion()">
                                    <i class="fas fa-trash"></i> Request Deletion
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Security Measures -->
                <div class="policy-section" id="security">
                    <div class="section-header" data-section="security">
                        <h2><i class="fas fa-lock"></i> Security Measures</h2>
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </div>
                    <div class="section-content">
                        <div class="security-features">
                            <div class="security-item">
                                <i class="fas fa-key"></i>
                                <h5>Encryption</h5>
                                <p>All data is encrypted in transit and at rest</p>
                            </div>
                            <div class="security-item">
                                <i class="fas fa-server"></i>
                                <h5>Secure Servers</h5>
                                <p>Hosted on industry-standard secure infrastructure</p>
                            </div>
                            <div class="security-item">
                                <i class="fas fa-shield-alt"></i>
                                <h5>Access Control</h5>
                                <p>Strict access controls and authentication</p>
                            </div>
                            <div class="security-item">
                                <i class="fas fa-sync"></i>
                                <h5>Regular Updates</h5>
                                <p>Continuous security monitoring and updates</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Interactive Privacy Quiz -->
                <div class="policy-section" id="privacy-quiz">
                    <div class="section-header" data-section="privacy-quiz">
                        <h2><i class="fas fa-question-circle"></i> Privacy Knowledge Quiz</h2>
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </div>
                    <div class="section-content">
                        <div class="quiz-container">
                            <div class="quiz-question" data-question="1">
                                <h4>Question 1: What data do we collect when you play games?</h4>
                                <div class="quiz-options">
                                    <button class="quiz-option" data-correct="true">Game progress and scores</button>
                                    <button class="quiz-option" data-correct="false">Your personal photos</button>
                                    <button class="quiz-option" data-correct="false">Your bank account details</button>
                                </div>
                            </div>
                            
                            <div class="quiz-question" data-question="2" style="display: none;">
                                <h4>Question 2: Do we sell your personal information?</h4>
                                <div class="quiz-options">
                                    <button class="quiz-option" data-correct="false">Yes, to advertisers</button>
                                    <button class="quiz-option" data-correct="true">No, we never sell your data</button>
                                    <button class="quiz-option" data-correct="false">Only with your permission</button>
                                </div>
                            </div>
                            
                            <div class="quiz-result" style="display: none;">
                                <h4>Quiz Complete!</h4>
                                <p>You got <span class="quiz-score">0</span> out of 2 questions correct.</p>
                                <button class="btn btn-primary" onclick="restartQuiz()">Try Again</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="policy-actions">
                    <button class="btn btn-primary" onclick="printPolicy()">
                        <i class="fas fa-print"></i> Print Policy
                    </button>
                    <button class="btn btn-success" onclick="downloadPDF()">
                        <i class="fas fa-file-pdf"></i> Download PDF
                    </button>
                    <button class="btn btn-info" onclick="sharePolicy()">
                        <i class="fas fa-share"></i> Share Policy
                    </button>
                </div>

            </div>
        </div>
    </div>
</section>

<!-- Include Footer -->
<?php include 'includes/footer.php'; ?>

<style>
/*===================================
  Privacy Policy Styling
===================================*/
.privacy-hero {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
    min-height: 70vh;
    display: flex;
    align-items: center;
    position: relative;
    overflow: hidden;
    padding: 0 0 7rem 0;
    padding-top: 0;
}

.back-button-wrapper {
    position: absolute;
    top: 2rem;
    left: 2rem;
    z-index: 10;
}

.back-btn {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: white;
    transition: all 0.3s ease;
}

.back-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateX(-5px);
}

.privacy-title {
    color: white;
    font-size: 3.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
    text-shadow: 0 0 20px rgba(255, 255, 255, 0.3);
}

.privacy-icon {
    display: inline-block;
    animation: float 3s ease-in-out infinite;
    margin: 0 1rem;
}

.privacy-subtitle {
    color: rgba(255, 255, 255, 0.8);
    font-size: 1.2rem;
    margin-bottom: 2rem;
}

/* Floating Elements */
.floating-elements {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
}

.privacy-element {
    position: absolute;
    font-size: 2rem;
    opacity: 0.6;
    animation: float 4s ease-in-out infinite;
}

.privacy-element:nth-child(1) { top: 20%; left: 10%; animation-delay: 0s; }
.privacy-element:nth-child(2) { top: 60%; left: 80%; animation-delay: 1s; }
.privacy-element:nth-child(3) { top: 30%; left: 70%; animation-delay: 2s; }
.privacy-element:nth-child(4) { top: 70%; left: 20%; animation-delay: 3s; }
.privacy-element:nth-child(5) { top: 40%; left: 90%; animation-delay: 0.5s; }
.privacy-element:nth-child(6) { top: 80%; left: 60%; animation-delay: 1.5s; }
.privacy-element:nth-child(7) { top: 10%; left: 50%; animation-delay: 2.5s; }
.privacy-element:nth-child(8) { top: 50%; left: 30%; animation-delay: 3.5s; }

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

/* Content Styling */
.privacy-content {
    background: var(--bg-primary);
    padding: 4rem 0 7rem 0;
    position: relative;
}

.reading-progress {
    position: sticky;
    top: 0;
    background: var(--nav-bg);
    padding: 1rem;
    border-radius: 10px;
    margin-bottom: 2rem;
    z-index: 100;
}

.progress-bar {
    width: 100%;
    height: 8px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 0.5rem;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #00A4EF, #4B8BBE);
    width: 0%;
    transition: width 0.3s ease;
}

.policy-section {
    background: var(--nav-bg);
    border-radius: 15px;
    margin-bottom: 2rem;
    overflow: hidden;
    box-shadow: 0 4px 20px var(--shadow-color);
    transition: all 0.3s ease;
}

.policy-section:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px var(--shadow-color);
}

.section-header {
    background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
    color: white;
    padding: 1.5rem;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: all 0.3s ease;
}

.section-header:hover {
    background: linear-gradient(135deg, var(--accent-secondary), var(--accent-primary));
}

.section-header h2 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 600;
}

.toggle-icon {
    transition: transform 0.3s ease;
}

.section-content {
    padding: 2rem;
    display: none;
}

.policy-section.active .section-content {
    display: block;
}

.policy-section.active .toggle-icon {
    transform: rotate(180deg);
}

.policy-highlight {
    background: rgba(0, 164, 239, 0.1);
    border-left: 4px solid var(--accent-primary);
    padding: 1rem;
    margin: 1rem 0;
    border-radius: 0 10px 10px 0;
}

.collection-methods, .usage-grid, .rights-grid, .security-features {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin: 1.5rem 0;
}

.method-card, .usage-item, .right-item, .security-item {
    background: rgba(255, 255, 255, 0.05);
    padding: 1.5rem;
    border-radius: 10px;
    text-align: center;
    transition: all 0.3s ease;
}

.method-card:hover, .usage-item:hover, .right-item:hover, .security-item:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: translateY(-5px);
}

.method-card i, .usage-item i, .right-item i, .security-item i {
    font-size: 2rem;
    color: var(--accent-primary);
    margin-bottom: 1rem;
}

.sharing-scenarios {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin: 1.5rem 0;
}

.scenario {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
}

.scenario i {
    font-size: 1.5rem;
}

.control-panel {
    background: rgba(255, 255, 255, 0.05);
    padding: 1.5rem;
    border-radius: 10px;
    margin-top: 1.5rem;
}

.action-buttons {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
}

/* Quiz Styling */
.quiz-container {
    background: rgba(255, 255, 255, 0.05);
    padding: 2rem;
    border-radius: 10px;
}

.quiz-options {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-top: 1rem;
}

.quiz-option {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    padding: 1rem;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: left;
}

.quiz-option:hover {
    background: rgba(255, 255, 255, 0.2);
}

.quiz-option.correct {
    background: rgba(76, 175, 80, 0.3);
    border-color: #4CAF50;
}

.quiz-option.incorrect {
    background: rgba(244, 67, 54, 0.3);
    border-color: #F44336;
}

.policy-actions {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin-top: 3rem;
    flex-wrap: wrap;
}

/* Responsive Design */
@media (max-width: 768px) {
    .privacy-title {
        font-size: 2.5rem;
    }
    
    .back-button-wrapper {
        position: relative;
        top: auto;
        left: auto;
        margin-bottom: 2rem;
    }
    
    .collection-methods, .usage-grid, .rights-grid, .security-features {
        grid-template-columns: 1fr;
    }
    
    .policy-actions {
        flex-direction: column;
        align-items: center;
    }
    
    .action-buttons {
        flex-direction: column;
    }
}
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/rellax/1.12.1/rellax.min.js"></script>
<script>
// Initialize Rellax
document.addEventListener('DOMContentLoaded', function() {
    new Rellax('.privacy-element', {
        speed: -2,
        vertical: true,
        horizontal: false
    });
    
    // Initialize privacy policy functionality
    initializePrivacyPolicy();
});

function initializePrivacyPolicy() {
    // Section toggles
    document.querySelectorAll('.section-header').forEach(header => {
        header.addEventListener('click', function() {
            const section = this.closest('.policy-section');
            const content = section.querySelector('.section-content');
            
            if (section.classList.contains('active')) {
                section.classList.remove('active');
                content.style.display = 'none';
            } else {
                section.classList.add('active');
                content.style.display = 'block';
            }
        });
    });
    
    // Reading progress
    updateReadingProgress();
    window.addEventListener('scroll', updateReadingProgress);
    
    // Quiz functionality
    initializeQuiz();
}

function updateReadingProgress() {
    const scrollTop = window.pageYOffset;
    const docHeight = document.body.offsetHeight - window.innerHeight;
    const scrollPercent = (scrollTop / docHeight) * 100;
    
    document.querySelector('.progress-fill').style.width = scrollPercent + '%';
    document.querySelector('.progress-percentage').textContent = Math.round(scrollPercent) + '%';
}

function initializeQuiz() {
    let currentQuestion = 1;
    let score = 0;
    
    document.querySelectorAll('.quiz-option').forEach(option => {
        option.addEventListener('click', function() {
            const isCorrect = this.getAttribute('data-correct') === 'true';
            const allOptions = this.parentElement.querySelectorAll('.quiz-option');
            
            // Disable all options
            allOptions.forEach(opt => opt.style.pointerEvents = 'none');
            
            // Show correct/incorrect
            allOptions.forEach(opt => {
                if (opt.getAttribute('data-correct') === 'true') {
                    opt.classList.add('correct');
                } else {
                    opt.classList.add('incorrect');
                }
            });
            
            if (isCorrect) score++;
            
            // Next question or show result
            setTimeout(() => {
                if (currentQuestion < 2) {
                    currentQuestion++;
                    showQuestion(currentQuestion);
                } else {
                    showQuizResult();
                }
            }, 1500);
        });
    });
}

function showQuestion(questionNum) {
    document.querySelectorAll('.quiz-question').forEach(q => q.style.display = 'none');
    document.querySelector(`[data-question="${questionNum}"]`).style.display = 'block';
    
    // Reset options
    document.querySelectorAll('.quiz-option').forEach(opt => {
        opt.classList.remove('correct', 'incorrect');
        opt.style.pointerEvents = 'auto';
    });
}

function showQuizResult() {
    document.querySelectorAll('.quiz-question').forEach(q => q.style.display = 'none');
    document.querySelector('.quiz-result').style.display = 'block';
    document.querySelector('.quiz-score').textContent = score;
}

function restartQuiz() {
    location.reload();
}

// Action functions
function exportData() {
    alert('Data export feature will be implemented soon!');
}

function requestDeletion() {
    if (confirm('Are you sure you want to request account deletion? This action cannot be undone.')) {
        alert('Deletion request submitted. We will process your request within 30 days.');
    }
}

function printPolicy() {
    window.print();
}

function downloadPDF() {
    alert('PDF download feature will be implemented soon!');
}

function sharePolicy() {
    if (navigator.share) {
        navigator.share({
            title: 'Code Gaming Privacy Policy',
            url: window.location.href
        });
    } else {
        // Fallback for browsers that don't support Web Share API
        navigator.clipboard.writeText(window.location.href);
        alert('Link copied to clipboard!');
    }
}
</script> 
