<?php
/**
 * ==========================================================
 * File: terms.php
 * 
 * Description:
 *   - Terms of Service page with interactive Rellax background
 *   - Features:
 *       • Floating legal icons and compliance elements
 *       • Expandable content sections
 *       • Interactive acceptance flow
 *       • Terms knowledge quiz
 *       • Version history timeline
 *       • Print/Download options
 *       • Responsive design with accessibility
 * 
 * Usage:
 *   - Public page accessible to all users and visitors
 *       • Displays comprehensive terms of service with engaging UI
 * 
 * Author: [Santiago]
 * Last Updated: [June 17, 2025]
 * -- Code Gaming Team --
 * ==========================================================
 */

// Include header
include 'includes/header.php';
?>

<!-- Terms of Service Hero Section -->
<section class="terms-hero">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <div class="back-button-wrapper">
                    <a href="anchor.php" class="btn btn-outline-light back-btn">
                        <i class="fas fa-arrow-left"></i> Back to Home
                    </a>
                </div>
                <h1 class="terms-title">
                    <span class="terms-icon">⚖️</span>
                    Terms of Service
                    <span class="terms-icon">⚖️</span>
                </h1>
                <p class="terms-subtitle">Please read these terms carefully before using Code Gaming. By using our platform, you agree to these terms.</p>
                
                <!-- Last Updated Info -->
                <div class="last-updated">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Last Updated: June 17, 2025</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Floating Legal Elements (Rellax) -->
    <div class="floating-elements">
        <div class="legal-element" data-rellax-speed="2">📄</div>
        <div class="legal-element" data-rellax-speed="-1">⚖️</div>
        <div class="legal-element" data-rellax-speed="3">✅</div>
        <div class="legal-element" data-rellax-speed="-2">🔒</div>
        <div class="legal-element" data-rellax-speed="1">📋</div>
        <div class="legal-element" data-rellax-speed="2">🛡️</div>
        <div class="legal-element" data-rellax-speed="-3">📝</div>
        <div class="legal-element" data-rellax-speed="1">🎯</div>
    </div>
</section>

<!-- Terms of Service Content -->
<section class="terms-content">
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

                <!-- Acceptance Flow -->
                <div class="acceptance-flow">
                    <div class="acceptance-step active" data-step="1">
                        <h3><i class="fas fa-user-check"></i> Step 1: Read the Terms</h3>
                        <p>Please read through all sections below to understand our terms of service.</p>
                    </div>
                    <div class="acceptance-step" data-step="2">
                        <h3><i class="fas fa-question-circle"></i> Step 2: Take the Quiz</h3>
                        <p>Complete a quick quiz to ensure you understand the key terms.</p>
                    </div>
                    <div class="acceptance-step" data-step="3">
                        <h3><i class="fas fa-check-double"></i> Step 3: Accept Terms</h3>
                        <p>Confirm your acceptance of the terms of service.</p>
                    </div>
                </div>

                <!-- Introduction Section -->
                <div class="terms-section active" id="introduction">
                    <div class="section-header" data-section="introduction">
                        <h2><i class="fas fa-info-circle"></i> Acceptance of Terms</h2>
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </div>
                    <div class="section-content">
                        <p>Welcome to Code Gaming! These Terms of Service ("Terms") govern your use of our platform and services. By accessing or using Code Gaming, you agree to be bound by these Terms.</p>
                        <p>If you do not agree to these Terms, please do not use our platform. We reserve the right to modify these Terms at any time, and such modifications will be effective immediately upon posting.</p>
                        
                        <div class="terms-highlight">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Important:</strong> By using Code Gaming, you confirm that you are at least 18 years old and have the legal capacity to enter into these Terms.
                        </div>
                        
                        <div class="key-points">
                            <h4>Key Points:</h4>
                            <ul>
                                <li>You must be 18+ years old to use Code Gaming</li>
                                <li>You are responsible for maintaining account security</li>
                                <li>You agree not to violate any applicable laws</li>
                                <li>We may terminate accounts for violations</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- User Accounts Section -->
                <div class="terms-section" id="user-accounts">
                    <div class="section-header" data-section="user-accounts">
                        <h2><i class="fas fa-user-circle"></i> User Accounts & Registration</h2>
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </div>
                    <div class="section-content">
                        <h4>Account Creation</h4>
                        <p>To access certain features of Code Gaming, you must create an account. You agree to provide accurate, current, and complete information during registration.</p>
                        
                        <div class="account-requirements">
                            <div class="requirement-card">
                                <i class="fas fa-user-plus"></i>
                                <h5>Registration</h5>
                                <p>Provide valid email and username</p>
                            </div>
                            <div class="requirement-card">
                                <i class="fas fa-shield-alt"></i>
                                <h5>Security</h5>
                                <p>Keep credentials secure</p>
                            </div>
                            <div class="requirement-card">
                                <i class="fas fa-edit"></i>
                                <h5>Updates</h5>
                                <p>Keep information current</p>
                            </div>
                        </div>
                        
                        <h4>Account Responsibilities</h4>
                        <ul>
                            <li>You are responsible for all activities under your account</li>
                            <li>You must notify us immediately of any unauthorized use</li>
                            <li>You may not share your account credentials with others</li>
                            <li>You may not create multiple accounts for malicious purposes</li>
                        </ul>
                    </div>
                </div>

                <!-- Acceptable Use Section -->
                <div class="terms-section" id="acceptable-use">
                    <div class="section-header" data-section="acceptable-use">
                        <h2><i class="fas fa-handshake"></i> Acceptable Use Policy</h2>
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </div>
                    <div class="section-content">
                        <div class="use-policy-grid">
                            <div class="policy-item allowed">
                                <i class="fas fa-check-circle"></i>
                                <h5>Allowed Activities</h5>
                                <ul>
                                    <li>Learning and practicing coding</li>
                                    <li>Participating in challenges</li>
                                    <li>Sharing knowledge respectfully</li>
                                    <li>Building a positive community</li>
                                </ul>
                            </div>
                            <div class="policy-item prohibited">
                                <i class="fas fa-times-circle"></i>
                                <h5>Prohibited Activities</h5>
                                <ul>
                                    <li>Cheating or using unauthorized tools</li>
                                    <li>Harassing other users</li>
                                    <li>Sharing inappropriate content</li>
                                    <li>Attempting to hack or disrupt services</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="consequences">
                            <h4>Consequences of Violations</h4>
                            <div class="consequence-levels">
                                <div class="level">
                                    <span class="level-badge warning">Warning</span>
                                    <p>First-time minor violations</p>
                                </div>
                                <div class="level">
                                    <span class="level-badge suspension">Suspension</span>
                                    <p>Temporary account suspension</p>
                                </div>
                                <div class="level">
                                    <span class="level-badge termination">Termination</span>
                                    <p>Permanent account ban</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Intellectual Property Section -->
                <div class="terms-section" id="intellectual-property">
                    <div class="section-header" data-section="intellectual-property">
                        <h2><i class="fas fa-copyright"></i> Intellectual Property Rights</h2>
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </div>
                    <div class="section-content">
                        <div class="ip-rights">
                            <div class="ip-item">
                                <i class="fas fa-code"></i>
                                <h5>Our Content</h5>
                                <p>Code Gaming owns all platform content, code, and design elements</p>
                            </div>
                            <div class="ip-item">
                                <i class="fas fa-user-edit"></i>
                                <h5>Your Content</h5>
                                <p>You retain rights to content you create and share</p>
                            </div>
                            <div class="ip-item">
                                <i class="fas fa-share-alt"></i>
                                <h5>Licensing</h5>
                                <p>You grant us license to display your shared content</p>
                            </div>
                        </div>
                        
                        <div class="copyright-notice">
                            <h4>Copyright Notice</h4>
                            <p>All content on Code Gaming is protected by copyright laws. You may not reproduce, distribute, or create derivative works without permission.</p>
                        </div>
                    </div>
                </div>

                <!-- Privacy & Data Section -->
                <div class="terms-section" id="privacy-data">
                    <div class="section-header" data-section="privacy-data">
                        <h2><i class="fas fa-user-shield"></i> Privacy & Data Protection</h2>
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </div>
                    <div class="section-content">
                        <p>Your privacy is important to us. Our collection and use of your personal information is governed by our <a href="privacy.php" class="terms-link">Privacy Policy</a>.</p>
                        
                        <div class="data-usage">
                            <h4>How We Use Your Data</h4>
                            <div class="usage-categories">
                                <div class="category">
                                    <i class="fas fa-chart-line"></i>
                                    <span>Improve Services</span>
                                </div>
                                <div class="category">
                                    <i class="fas fa-bell"></i>
                                    <span>Communications</span>
                                </div>
                                <div class="category">
                                    <i class="fas fa-shield-alt"></i>
                                    <span>Security</span>
                                </div>
                                <div class="category">
                                    <i class="fas fa-cog"></i>
                                    <span>Platform Operations</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Disclaimers Section -->
                <div class="terms-section" id="disclaimers">
                    <div class="section-header" data-section="disclaimers">
                        <h2><i class="fas fa-exclamation-triangle"></i> Disclaimers & Limitations</h2>
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </div>
                    <div class="section-content">
                        <div class="disclaimer-grid">
                            <div class="disclaimer-item">
                                <i class="fas fa-server"></i>
                                <h5>Service Availability</h5>
                                <p>We strive for 99.9% uptime but cannot guarantee uninterrupted service</p>
                            </div>
                            <div class="disclaimer-item">
                                <i class="fas fa-graduation-cap"></i>
                                <h5>Educational Content</h5>
                                <p>While we provide quality content, we cannot guarantee specific learning outcomes</p>
                            </div>
                            <div class="disclaimer-item">
                                <i class="fas fa-shield-alt"></i>
                                <h5>Security</h5>
                                <p>We implement security measures but cannot guarantee complete protection</p>
                            </div>
                        </div>
                        
                        <div class="limitation-notice">
                            <h4>Limitation of Liability</h4>
                            <p>Code Gaming shall not be liable for any indirect, incidental, special, consequential, or punitive damages arising from your use of our platform.</p>
                        </div>
                    </div>
                </div>

                <!-- Termination Section -->
                <div class="terms-section" id="termination">
                    <div class="section-header" data-section="termination">
                        <h2><i class="fas fa-user-times"></i> Termination & Suspension</h2>
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </div>
                    <div class="section-content">
                        <div class="termination-scenarios">
                            <div class="scenario">
                                <i class="fas fa-user-edit text-info"></i>
                                <span><strong>By You:</strong> You may terminate your account at any time</span>
                            </div>
                            <div class="scenario">
                                <i class="fas fa-gavel text-warning"></i>
                                <span><strong>By Us:</strong> We may terminate for Terms violations</span>
                            </div>
                            <div class="scenario">
                                <i class="fas fa-clock text-secondary"></i>
                                <span><strong>Inactivity:</strong> Accounts inactive for 12+ months may be removed</span>
                            </div>
                        </div>
                        
                        <div class="termination-effects">
                            <h4>Effects of Termination</h4>
                            <ul>
                                <li>Immediate loss of access to your account</li>
                                <li>Deletion of personal data (subject to privacy policy)</li>
                                <li>Loss of progress, achievements, and content</li>
                                <li>No refund of any paid services</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Governing Law Section -->
                <div class="terms-section" id="governing-law">
                    <div class="section-header" data-section="governing-law">
                        <h2><i class="fas fa-balance-scale"></i> Governing Law & Disputes</h2>
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </div>
                    <div class="section-content">
                        <div class="legal-framework">
                            <div class="framework-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <h5>Governing Law</h5>
                                <p>These Terms are governed by the laws of [Jurisdiction]</p>
                            </div>
                            <div class="framework-item">
                                <i class="fas fa-gavel"></i>
                                <h5>Dispute Resolution</h5>
                                <p>Disputes will be resolved through binding arbitration</p>
                            </div>
                            <div class="framework-item">
                                <i class="fas fa-globe"></i>
                                <h5>International Users</h5>
                                <p>You agree to comply with local laws and regulations</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Interactive Terms Quiz -->
                <div class="terms-section" id="terms-quiz">
                    <div class="section-header" data-section="terms-quiz">
                        <h2><i class="fas fa-question-circle"></i> Terms Knowledge Quiz</h2>
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </div>
                    <div class="section-content">
                        <div class="quiz-container">
                            <div class="quiz-question" data-question="1">
                                <h4>Question 1: What is the minimum age to use Code Gaming?</h4>
                                <div class="quiz-options">
                                    <button class="quiz-option" data-correct="true">13 years old</button>
                                    <button class="quiz-option" data-correct="false">18 years old</button>
                                    <button class="quiz-option" data-correct="false">16 years old</button>
                                </div>
                            </div>
                            
                            <div class="quiz-question" data-question="2" style="display: none;">
                                <h4>Question 2: What happens if you violate the Terms of Service?</h4>
                                <div class="quiz-options">
                                    <button class="quiz-option" data-correct="false">Nothing, it's just a warning</button>
                                    <button class="quiz-option" data-correct="true">Your account may be suspended or terminated</button>
                                    <button class="quiz-option" data-correct="false">You get a free pass</button>
                                </div>
                            </div>
                            
                            <div class="quiz-question" data-question="3" style="display: none;">
                                <h4>Question 3: Who is responsible for keeping your account secure?</h4>
                                <div class="quiz-options">
                                    <button class="quiz-option" data-correct="false">Code Gaming team</button>
                                    <button class="quiz-option" data-correct="true">You are responsible</button>
                                    <button class="quiz-option" data-correct="false">Your internet provider</button>
                                </div>
                            </div>
                            
                            <div class="quiz-result" style="display: none;">
                                <h4>Quiz Complete!</h4>
                                <p>You got <span class="quiz-score">0</span> out of 3 questions correct.</p>
                                <button class="btn btn-primary" onclick="restartQuiz()">Try Again</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Version History -->
                <div class="terms-section" id="version-history">
                    <div class="section-header" data-section="version-history">
                        <h2><i class="fas fa-history"></i> Version History</h2>
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </div>
                    <div class="section-content">
                        <div class="version-timeline">
                            <div class="version-item current">
                                <div class="version-date">June 17, 2025</div>
                                <div class="version-content">
                                    <h5>Current Version (v2.1)</h5>
                                    <ul>
                                        <li>Updated privacy and data protection terms</li>
                                        <li>Added new acceptable use policies</li>
                                        <li>Enhanced user rights and control sections</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="version-item">
                                <div class="version-date">April 15, 2025</div>
                                <div class="version-content">
                                    <h5>Version 2.0</h5>
                                    <ul>
                                        <li>Major restructuring of terms</li>
                                        <li>Added intellectual property section</li>
                                        <li>Updated termination policies</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="version-item">
                                <div class="version-date">March  1, 2025</div>
                                <div class="version-content">
                                    <h5>Version 1.0</h5>
                                    <ul>
                                        <li>Initial terms of service</li>
                                        <li>Basic user agreement</li>
                                        <li>Privacy policy integration</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Final Acceptance -->
                <div class="final-acceptance">
                    <div class="acceptance-box">
                        <div class="checkbox-wrapper">
                            <input type="checkbox" id="acceptTerms" class="accept-checkbox">
                            <label for="acceptTerms" class="accept-label">
                                <i class="fas fa-check"></i>
                                <span>I have read, understood, and agree to the Terms of Service</span>
                            </label>
                        </div>
                        <div class="acceptance-actions">
                            <button class="btn btn-success" id="acceptButton" disabled onclick="acceptTerms()">
                                <i class="fas fa-check-double"></i> Accept Terms
                            </button>
                            <button class="btn btn-outline-secondary" onclick="declineTerms()">
                                <i class="fas fa-times"></i> Decline
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="terms-actions">
                    <button class="btn btn-primary" onclick="printTerms()">
                        <i class="fas fa-print"></i> Print Terms
                    </button>
                    <button class="btn btn-success" onclick="downloadPDF()">
                        <i class="fas fa-file-pdf"></i> Download PDF
                    </button>
                    <button class="btn btn-info" onclick="shareTerms()">
                        <i class="fas fa-share"></i> Share Terms
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
  Terms of Service Styling
===================================*/
.terms-hero {
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 50%, #2c3e50 100%);
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

.terms-title {
    color: white;
    font-size: 3.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
    text-shadow: 0 0 20px rgba(255, 255, 255, 0.3);
}

.terms-icon {
    display: inline-block;
    animation: float 3s ease-in-out infinite;
    margin: 0 1rem;
}

.terms-subtitle {
    color: rgba(255, 255, 255, 0.8);
    font-size: 1.2rem;
    margin-bottom: 2rem;
}

.last-updated {
    background: rgba(255, 255, 255, 0.1);
    padding: 0.5rem 1rem;
    border-radius: 25px;
    display: inline-block;
    color: rgba(255, 255, 255, 0.9);
    font-size: 0.9rem;
}

.last-updated i {
    margin-right: 0.5rem;
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

.legal-element {
    position: absolute;
    font-size: 2rem;
    opacity: 0.6;
    animation: float 4s ease-in-out infinite;
}

.legal-element:nth-child(1) { top: 20%; left: 10%; animation-delay: 0s; }
.legal-element:nth-child(2) { top: 60%; left: 80%; animation-delay: 1s; }
.legal-element:nth-child(3) { top: 30%; left: 70%; animation-delay: 2s; }
.legal-element:nth-child(4) { top: 70%; left: 20%; animation-delay: 3s; }
.legal-element:nth-child(5) { top: 40%; left: 90%; animation-delay: 0.5s; }
.legal-element:nth-child(6) { top: 80%; left: 60%; animation-delay: 1.5s; }
.legal-element:nth-child(7) { top: 10%; left: 50%; animation-delay: 2.5s; }
.legal-element:nth-child(8) { top: 50%; left: 30%; animation-delay: 3.5s; }

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

/* Content Styling */
.terms-content {
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
    background: linear-gradient(90deg, #2c3e50, #34495e);
    width: 0%;
    transition: width 0.3s ease;
}

/* Acceptance Flow */
.acceptance-flow {
    background: var(--nav-bg);
    border-radius: 15px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 20px var(--shadow-color);
}

.acceptance-step {
    display: none;
    text-align: center;
}

.acceptance-step.active {
    display: block;
}

.acceptance-step h3 {
    color: var(--accent-primary);
    margin-bottom: 1rem;
}

.acceptance-step i {
    font-size: 2rem;
    margin-bottom: 1rem;
    display: block;
}

/* Terms Sections */
.terms-section {
    background: var(--nav-bg);
    border-radius: 15px;
    margin-bottom: 2rem;
    overflow: hidden;
    box-shadow: 0 4px 20px var(--shadow-color);
    transition: all 0.3s ease;
}

.terms-section:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px var(--shadow-color);
}

.section-header {
    background: linear-gradient(135deg, #2c3e50, #34495e);
    color: white;
    padding: 1.5rem;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: all 0.3s ease;
}

.section-header:hover {
    background: linear-gradient(135deg, #34495e, #2c3e50);
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

.terms-section.active .section-content {
    display: block;
}

.terms-section.active .toggle-icon {
    transform: rotate(180deg);
}

.terms-highlight {
    background: rgba(44, 62, 80, 0.1);
    border-left: 4px solid #2c3e50;
    padding: 1rem;
    margin: 1rem 0;
    border-radius: 0 10px 10px 0;
}

.key-points ul {
    list-style: none;
    padding: 0;
}

.key-points li {
    padding: 0.5rem 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.key-points li:before {
    content: "•";
    color: var(--accent-primary);
    font-weight: bold;
    margin-right: 0.5rem;
}

/* Grid Layouts */
.account-requirements, .use-policy-grid, .ip-rights, .disclaimer-grid, .legal-framework {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin: 1.5rem 0;
}

.requirement-card, .policy-item, .ip-item, .disclaimer-item, .framework-item {
    background: rgba(255, 255, 255, 0.05);
    padding: 1.5rem;
    border-radius: 10px;
    text-align: center;
    transition: all 0.3s ease;
}

.requirement-card:hover, .ip-item:hover, .disclaimer-item:hover, .framework-item:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: translateY(-5px);
}

.policy-item.allowed {
    border-left: 4px solid #27ae60;
}

.policy-item.prohibited {
    border-left: 4px solid #e74c3c;
}

.requirement-card i, .policy-item i, .ip-item i, .disclaimer-item i, .framework-item i {
    font-size: 2rem;
    color: var(--accent-primary);
    margin-bottom: 1rem;
}

/* Consequence Levels */
.consequence-levels {
    display: flex;
    gap: 1rem;
    margin: 1rem 0;
}

.level {
    flex: 1;
    text-align: center;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
}

.level-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.level-badge.warning {
    background: #f39c12;
    color: white;
}

.level-badge.suspension {
    background: #e67e22;
    color: white;
}

.level-badge.termination {
    background: #e74c3c;
    color: white;
}

/* Usage Categories */
.usage-categories {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin: 1rem 0;
}

.category {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 8px;
}

.category i {
    color: var(--accent-primary);
    font-size: 1.2rem;
}

/* Scenarios */
.termination-scenarios, .sharing-scenarios {
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

/* Version Timeline */
.version-timeline {
    position: relative;
    padding-left: 2rem;
}

.version-timeline::before {
    content: '';
    position: absolute;
    left: 1rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background: var(--accent-primary);
}

.version-item {
    position: relative;
    margin-bottom: 2rem;
    padding-left: 2rem;
}

.version-item::before {
    content: '';
    position: absolute;
    left: -1.5rem;
    top: 0.5rem;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: var(--accent-primary);
}

.version-item.current::before {
    background: #27ae60;
    box-shadow: 0 0 10px #27ae60;
}

.version-date {
    font-weight: bold;
    color: var(--accent-primary);
    margin-bottom: 0.5rem;
}

.version-content h5 {
    margin-bottom: 0.5rem;
}

.version-content ul {
    margin: 0;
    padding-left: 1rem;
}

/* Final Acceptance */
.final-acceptance {
    background: var(--nav-bg);
    border-radius: 15px;
    padding: 2rem;
    margin: 3rem 0;
    text-align: center;
    box-shadow: 0 4px 20px var(--shadow-color);
}

.acceptance-box {
    max-width: 600px;
    margin: 0 auto;
}

.checkbox-wrapper {
    margin-bottom: 2rem;
}

.accept-checkbox {
    display: none;
}

.accept-label {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.accept-label:hover {
    background: rgba(255, 255, 255, 0.1);
}

.accept-label i {
    font-size: 1.5rem;
    color: #ccc;
    transition: all 0.3s ease;
}

.accept-checkbox:checked + .accept-label {
    background: rgba(39, 174, 96, 0.1);
    border: 2px solid #27ae60;
}

.accept-checkbox:checked + .accept-label i {
    color: #27ae60;
    transform: scale(1.2);
}

.acceptance-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
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
    background: rgba(39, 174, 96, 0.3);
    border-color: #27ae60;
}

.quiz-option.incorrect {
    background: rgba(231, 76, 60, 0.3);
    border-color: #e74c3c;
}

.terms-actions {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin-top: 3rem;
    flex-wrap: wrap;
}

/* Links */
.terms-link {
    color: var(--accent-primary);
    text-decoration: none;
    transition: color 0.3s ease;
}

.terms-link:hover {
    color: var(--accent-secondary);
    text-decoration: underline;
}

/* Responsive Design */
@media (max-width: 768px) {
    .terms-title {
        font-size: 2.5rem;
    }
    
    .back-button-wrapper {
        position: relative;
        top: auto;
        left: auto;
        margin-bottom: 2rem;
    }
    
    .account-requirements, .use-policy-grid, .ip-rights, .disclaimer-grid, .legal-framework {
        grid-template-columns: 1fr;
    }
    
    .terms-actions {
        flex-direction: column;
        align-items: center;
    }
    
    .acceptance-actions {
        flex-direction: column;
    }
    
    .consequence-levels {
        flex-direction: column;
    }
}
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/rellax/1.12.1/rellax.min.js"></script>
<script>
// Initialize Rellax
document.addEventListener('DOMContentLoaded', function() {
    new Rellax('.legal-element', {
        speed: -2,
        vertical: true,
        horizontal: false
    });
    
    // Initialize terms functionality
    initializeTerms();
});

function initializeTerms() {
    // Section toggles
    document.querySelectorAll('.section-header').forEach(header => {
        header.addEventListener('click', function() {
            const section = this.closest('.terms-section');
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
    
    // Acceptance checkbox
    document.getElementById('acceptTerms').addEventListener('change', function() {
        document.getElementById('acceptButton').disabled = !this.checked;
    });
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
                if (currentQuestion < 3) {
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

// Acceptance functions
function acceptTerms() {
    if (document.getElementById('acceptTerms').checked) {
        // Show success animation
        const button = document.getElementById('acceptButton');
        button.innerHTML = '<i class="fas fa-check"></i> Terms Accepted!';
        button.classList.remove('btn-success');
        button.classList.add('btn-secondary');
        button.disabled = true;
        
        // Store acceptance in localStorage
        localStorage.setItem('termsAccepted', new Date().toISOString());
        
        // Show success message
        setTimeout(() => {
            alert('Thank you for accepting our Terms of Service! You can now fully access Code Gaming.');
        }, 500);
    }
}

function declineTerms() {
    if (confirm('Declining the Terms of Service will limit your access to Code Gaming. Are you sure?')) {
        alert('You have declined the Terms of Service. Some features may be limited.');
    }
}

// Action functions
function printTerms() {
    window.print();
}

function downloadPDF() {
    alert('PDF download feature will be implemented soon!');
}

function shareTerms() {
    if (navigator.share) {
        navigator.share({
            title: 'Code Gaming Terms of Service',
            url: window.location.href
        });
    } else {
        // Fallback for browsers that don't support Web Share API
        navigator.clipboard.writeText(window.location.href);
        alert('Link copied to clipboard!');
    }
}
</script> 