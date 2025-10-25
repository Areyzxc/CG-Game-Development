<?php
/**
 * Contact Page - Code Gaming
 * 
 * Description:
 *   - Contact form and information page for users to reach out to the Code Gaming team
 *   - Includes a contact form, location map, and contact information
 * 
 * Author: [Santiago]
 * Last Updated: [October 24, 2025]
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include visitor tracking if file exists
if (file_exists(__DIR__ . '/includes/track_visitor.php')) {
    require_once __DIR__ . '/includes/track_visitor.php';
}

// Include CSRF protection if file exists
$csrf = null;
if (file_exists(__DIR__ . '/includes/CSRFProtection.php')) {
    require_once __DIR__ . '/includes/CSRFProtection.php';
    $csrf = CSRFProtection::getInstance();
}

// Check if auth class exists and initialize it
$auth = null;
if (file_exists(__DIR__ . '/includes/Auth.php')) {
    require_once __DIR__ . '/includes/Auth.php';
    if (class_exists('Auth') && method_exists('Auth', 'getInstance')) {
        $auth = Auth::getInstance();
    }
}

// Set page title for header
$pageTitle = 'Contact Us';

// Include header
include 'includes/header.php';
?>

<!-- Main Content -->
<main class="container py-5">
    <!-- Page Header -->
    <div class="text-center mb-5">
        <h1 class="display-4 fw-bold text-gradient">Get In Touch</h1>
        <p class="lead text-muted">We'd love to hear from you! Send us a message and we'll respond as soon as possible.</p>
    </div>

    <div class="row g-5">
        <!-- Contact Form -->
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body p-4 p-md-5">
                    <h2 class="h4 mb-4">Send us a Message</h2>
                    
                    <form id="contactForm" action="/contact/submit" method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf->getToken(); ?>">
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Your Name" required>
                                    <label for="name">Your Name</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Your Email" required>
                                    <label for="email">Email Address</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="subject" name="subject" placeholder="Subject" required>
                                    <label for="subject">Subject</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control" id="message" name="message" placeholder="Your Message" style="height: 150px;" required></textarea>
                                    <label for="message">Your Message</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="privacyPolicy" name="privacyPolicy" required>
                                    <label class="form-check-label small text-muted" for="privacyPolicy">
                                        I agree to the <a href="/privacy" class="text-decoration-none">Privacy Policy</a> and <a href="/terms" class="text-decoration-none">Terms of Service</a>
                                    </label>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-lg px-4">
                                    <i class="fas fa-paper-plane me-2"></i> Send Message
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-body p-4">
                    <h2 class="h4 mb-4">Contact Information</h2>
                    
                    <div class="d-flex mb-4">
                        <div class="flex-shrink-0 bg-primary bg-opacity-10 p-3 rounded-3 me-3">
                            <i class="fas fa-map-marker-alt text-primary"></i>
                        </div>
                        <div>
                            <h3 class="h6 mb-1">Our Location</h3>
                            <p class="mb-0 text-muted">College St.<br>Pateros, Metro Manila</p>
                        </div>
                    </div>

                    <div class="d-flex mb-4">
                        <div class="flex-shrink-0 bg-primary bg-opacity-10 p-3 rounded-3 me-3">
                            <i class="fas fa-envelope text-primary"></i>
                        </div>
                        <div>
                            <h3 class="h6 mb-1">Email Us</h3>
                            <p class="mb-0">
                                <a href="mailto:contact@codegaming.com" class="text-decoration-none">contact@codegaming.com</a>
                            </p>
                        </div>
                    </div>

                    <div class="d-flex">
                        <div class="flex-shrink-0 bg-primary bg-opacity-10 p-3 rounded-3 me-3">
                            <i class="fas fa-phone-alt text-primary"></i>
                        </div>
                        <div>
                            <h3 class="h6 mb-1">Call Us</h3>
                            <p class="mb-0">
                                <a href="tel:+639605876574" class="text-decoration-none">+63 9605876574</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Map -->
            <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                <div class="ratio ratio-16x9">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3022.215209179128!2d121.0656666!3d14.5656666!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c259a9b3117469%3A0xd134e199a405a163!2sEmpire%20State%20Building!5e0!3m2!1sen!2sus!4v1623456789012!5m2!1sen!2sus" 
                        width="600" 
                        height="450" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy">
                    </iframe>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Include Footer -->
<?php require_once __DIR__ . '/includes/footer.php'; ?>

<style>
/* Custom styles for contact page */
.text-gradient {
    background: linear-gradient(90deg, #4f46e5, #7c3aed);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    display: inline-block;
}

.card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: none;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1) !important;
}

.form-control, .form-select {
    padding: 1rem 1rem;
    border-radius: 0.5rem;
    border: 1px solid #e2e8f0;
}

.form-control:focus, .form-select:focus {
    border-color: #818cf8;
    box-shadow: 0 0 0 0.25rem rgba(99, 102, 241, 0.25);
}

.btn-primary {
    background-color: #4f46e5;
    border: none;
    padding: 0.75rem 1.5rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background-color: #4338ca;
    transform: translateY(-2px);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .card-body {
        padding: 1.5rem !important;
    }
    
    .btn-lg {
        width: 100%;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contactForm');
    
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = new FormData(contactForm);
            
            // Add CSRF token
            formData.append('csrf_token', '<?php echo $csrf->getToken(); ?>');
            
            // Show loading state
            const submitBtn = contactForm.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Sending...';
            
            // Simulate form submission (replace with actual AJAX call)
            setTimeout(() => {
                // Show success message
                const alert = document.createElement('div');
                alert.className = 'alert alert-success mt-3';
                alert.innerHTML = '<i class="fas fa-check-circle me-2"></i> Your message has been sent successfully!';
                contactForm.appendChild(alert);
                
                // Reset form
                contactForm.reset();
                
                // Reset button
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
                
                // Remove success message after 5 seconds
                setTimeout(() => {
                    alert.remove();
                }, 5000);
            }, 1500);
        });
    }
});
</script>
