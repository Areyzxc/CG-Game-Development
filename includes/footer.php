<?php
/**
 * ==========================================================
 * File: includes/footer.php
 * 
 * Description:
 *   - Global footer for Code Game web application for users and guests.
 *   - Provides consistent footer, audio player, and modal functionality across the site.
 *   - Contains:
 *       • Footer content (links, social icons, copyright)
 *       • Global audio player toolbar (with visualizer, volume, track selection)
 *       • Back-to-top button
 *       • Footer-specific and audio player CSS styles
 *       • Modal dialogs for Sign Up, Login, and Forgot Password
 *       • Global JS includes (audio.js)
 * 
 * Usage:
 *   - Included at the bottom of all main pages for consistent footer, audio, and modal functionality.
 *   - All global scripts and styles for the footer/audio/modal are managed here.
 * 
 * Author: [Santiago]
 * Last Updated: [June 13, 2025]
 * ==========================================================
 */
?>

<!-- Footer Main Content -->
<footer class="site-footer">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4 mb-md-0">
                <h5>Code Gaming</h5>
                <p class="mb-0">Master coding through interactive gameplay and challenges.</p>
            </div>
            <div class="col-md-4 mb-4 mb-md-0">
                <h5>Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="about.php" class="footer-link">About Us</a></li>
                    <li><a href="privacy.php" class="footer-link">Privacy Policy</a></li>
                    <li><a href="terms.php" class="footer-link">Terms of Service</a></li>
                    <li><a href="contact.php" class="footer-link">Contact Us</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h5>Connect With Us</h5>
                <div class="footer-social">
                    <a href="https://github.com/Areyzxc/Game-Development" target="_blank" title="GitHub" class="social-icon">
                        <i class="fab fa-github"></i>
                    </a>
                    <a href="https://reddit.com/r/codegaming_ptc" target="_blank" title="Reddit" class="social-icon">
                        <i class="fa-brands fa-reddit"></i>
                    </a>
                    <a href="https://discord.gg/codegame" target="_blank" title="Discord" class="social-icon">
                        <i class="fab fa-discord"></i>
                    </a>
                </div>
                <p class="mt-3 mb-0 footer-copy">&copy; 2025 Code Game. All rights reserved.</p>
            </div>
        </div>
    </div>

    <!-- Back to Top Button -->
    <button id="backToTop" class="btn btn-primary btn-sm position-fixed" title="Back to top">
        <i class="fas fa-chevron-up"></i>
    </button>
</footer>

    <!-- ===== Audio Player ===== -->
    <div class="audio-toolbar fixed-bottom bg-dark text-light">
        <div class="container-fluid">
            <div class="row align-items-center py-2">
                <!-- Playback Controls -->
                <div class="col-md-4 d-flex align-items-center">
                    <button id="audioPlayPause" class="btn btn-link text-light">
                        <i id="audioPlayIcon" class="fas fa-pause fa-lg"></i>
                    </button>
                    <span id="audioTitle" class="ms-3 text-truncate">Track Name</span>
                </div>

                <!-- Visualizer -->
                <div class="col-md-4 text-center">
                    <canvas id="audioVisualizer" width="200" height="40"></canvas>
                </div>

                <!-- Volume & Track Selection -->
                <div class="col-md-4 d-flex align-items-center justify-content-end">
                    <input type="range" id="volumeSlider" class="form-range me-3" min="0" max="1" step="0.01">
                    <select id="trackSelect" class="form-select form-select-sm track-select-scroll">
                        <option value="audio/vhs.mp3">VHS Dreams</option>
                        <option value="audio/flying.m4a">Flying'n'Stuff</option>
                        <option value="audio/gateway.m4a">Gateway</option>
                        <option value="audio/h4.mp3">H4</option>
                        <option value="audio/apricot.mp3">Apricot</option>
                        <option value="audio/just.m4a">Just</option>
                        <option value="audio/binary.mp3">Binary</option>
                        <option value="audio/SleeperMKUltra4.mp3">Sleeper MK Ultra 4</option>
                        <option value="audio/Virtuality.mp3">Virtuality</option>
                        <option value="audio/come_to_life.mp3">Come to Life</option>
                        <option value="audio/Andromeda_Sunsets.mp3">Andromeda Sunsets</option>
                        <option value="audio/rainsdef.mp3">Rainsdef</option>
                        <option value="audio/crystalsettings.mp3">Crystal Settings</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

<style>
/*===================================
  Enhanced Footer Styling
===================================*/
.site-footer {
    background: var(--nav-bg);
    border-top: 1px solid var(--nav-border);
    color: var(--text-secondary);
    padding: 2rem 0;
    margin-top: auto;
    position: relative;
    z-index: 100;
}

.site-footer h5 {
    color: var(--text-primary);
    margin-bottom: 1rem;
    font-weight: 600;
    text-shadow: 0 0 10px var(--shadow-color);
}

.site-footer p {
    color: var(--text-secondary);
    line-height: 1.6;
}

/* Footer Links */
.footer-link {
    color: var(--text-secondary);
    text-decoration: none;
    transition: color var(--transition-speed) var(--transition-timing);
    display: inline-block;
    margin-bottom: 0.5rem;
}

.footer-link:hover {
    color: var(--accent-primary);
    transform: translateX(5px);
}

/* Social Icons */
.footer-social {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
}

.social-icon {
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid var(--nav-border);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-secondary);
    text-decoration: none;
    transition: all var(--transition-speed) var(--transition-timing);
    box-shadow: 0 2px 8px var(--shadow-color);
}

.social-icon:hover {
    background: var(--accent-primary);
    color: var(--bg-primary);
    transform: translateY(-3px) scale(1.1);
    box-shadow: 0 4px 15px var(--shadow-color);
}

/* Footer Copy */
.footer-copy {
    color: rgba(255, 255, 255, 0.6);
    font-size: 0.9rem;
    font-family: "Times New Roman", Times, serif;
}

/* Back to Top Button */
#backToTop {
    position: fixed;
    bottom: 6rem;
    right: 2rem;
    display: none;
    opacity: 0.8;
    transition: opacity var(--transition-speed) var(--transition-timing);
    z-index: 1000;
    background: var(--gradient-primary);
    border: none;
    box-shadow: 0 4px 15px var(--shadow-color);
    width: 45px;
    height: 45px;
    border-radius: 50%;
    font-size: 1.2rem;
}

#backToTop:hover {
    opacity: 1;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px var(--shadow-color);
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .site-footer {
        padding: 1.5rem 0;
    }
    
    .footer-social {
        justify-content: center;
    }
    
    #backToTop {
        bottom: 5rem;
        right: 1rem;
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }
}

@media (max-width: 576px) {
    .site-footer h5 {
        font-size: 1.1rem;
    }
    
    .social-icon {
        width: 35px;
        height: 35px;
    }
}

.track-select-scroll {
  max-height: 50px;
  overflow-y: auto;
}
</style>

<script>
// Back to Top functionality
document.addEventListener('DOMContentLoaded', function() {
    const backToTopBtn = document.getElementById('backToTop');
    
    // Show/hide button based on scroll position
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            backToTopBtn.style.display = 'block';
        } else {
            backToTopBtn.style.display = 'none';
        }
    });
    
    // Smooth scroll to top
    backToTopBtn.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
});
</script>


<!-- Custom JS -->
<script src="assets/js/script.js"></script>
<script src="assets/js/audio.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/howler/2.2.3/howler.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@glidejs/glide"></script>
</body>
</html>

<!-- Sign‑Up Modal -->
<div class="modal fade" id="signInModal" tabindex="-1" aria-labelledby="signInModalLabel" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content retro-modal">
            <div class="modal-title-bar">
                <span class="title-text">C:\\DOS\\SIGNUP.EXE</span>
                <button type="button" class="modal-close-btn" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="signInForm" method="post" novalidate>
                    <div class="mb-3">
                        <label for="signUsername" class="form-label">Username</label>
                        <input type="text" id="signUsername" name="username" class="form-control" placeholder="Enter your callsign..." required>
                        <div class="invalid-feedback" id="nameError"></div>
                    </div>
                    <div class="mb-3">
                        <label for="signEmail" class="form-label">Email address</label>
                        <input type="email" id="signEmail" name="email" class="form-control" placeholder="Enter your address..." required>
                        <div class="invalid-feedback" id="emailError"></div>
                    </div>
                    <div class="mb-3">
                        <label for="signPassword" class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" id="signPassword" name="password" class="form-control" placeholder="Enter your password..." required>
                            <button type="button" class="btn btn-outline-secondary retro-eye-btn" tabindex="-1" style="border-radius: 0 6px 6px 0; background: #222; color: #0ff; border-left: 0;" onclick="togglePasswordVisibility('signPassword', this)">
                                <i class="fas fa-eye-slash"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback" id="passwordError"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Register as:</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="role" id="roleUser" value="user" checked>
                            <label class="form-check-label" for="roleUser">Player</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="role" id="roleAdmin" value="admin">
                            <label class="form-check-label" for="roleAdmin">Admin</label>
                        </div>
                    </div>
                    <div id="adminAcceptanceContainer" class="mb-3 d-none">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="adminAcceptance" name="admin_acceptance">
                            <label class="form-check-label" for="adminAcceptance">I accept my fate as an admin.</label>
                            <div class="invalid-feedback" id="adminAcceptanceError"></div>
                        </div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-success">EXECUTE</button>
                    </div>
                    <div id="loadingIndicator" class="text-center mt-3"></div>
                    <script>
                    function togglePasswordVisibility(inputId, btn) {
                        const input = document.getElementById(inputId);
                        const icon = btn.querySelector('i');
                        if (input.type === 'password') {
                            input.type = 'text';
                            icon.classList.remove('fa-eye-slash');
                            icon.classList.add('fa-eye');
                        } else {
                            input.type = 'password';
                            icon.classList.remove('fa-eye');
                            icon.classList.add('fa-eye-slash');
                        }
                    }
                    </script>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content retro-modal">
            <div class="modal-title-bar">
                <span class="title-text">C:\\DOS\\LOGIN.EXE</span>
                <button type="button" class="modal-close-btn" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="loginForm" method="POST" novalidate>
                    <div class="mb-3">
                        <label for="loginEmail" class="form-label">Username or Email</label>
                        <input type="text" class="form-control" id="loginEmail" name="username" placeholder="Enter your callsign..." required>
                        <div class="invalid-feedback" id="loginEmailError"></div>
                    </div>
                    <div class="mb-3">
                        <label for="loginPassword" class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="loginPassword" name="password" placeholder="Enter your password..." required>
                            <button type="button" class="btn btn-outline-secondary retro-eye-btn" tabindex="-1" style="border-radius: 0 6px 6px 0; background: #222; color: #0ff; border-left: 0;" onclick="togglePasswordVisibility('loginPassword', this)">
                                <i class="fas fa-eye-slash"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback" id="loginPasswordError"></div>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="rememberMe" name="remember">
                        <label class="form-check-label" for="rememberMe">Remember session</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">EXECUTE</button>
                    <div id="loginLoadingIndicator" class="text-center mt-3"></div>
                </form>
                <div class="text-center mt-3">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal" data-bs-dismiss="modal">Password recovery protocol?</a>
                </div>
                <div class="text-center mt-2">
                    <a href="#" id="showSignUpFromLogin">Not yet registered? Sign up here.</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Forgot Password Modal -->
<div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content retro-modal">
            <div class="modal-title-bar">
                 <span class="title-text">C:\\DOS\\RECOVERY.EXE</span>
                <button type="button" class="modal-close-btn" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="forgotPasswordForm" method="post" novalidate>
                    <div class="mb-3">
                        <label for="resetUsername" class="form-label">Username</label>
                        <input type="text" id="resetUsername" name="username" class="form-control" placeholder="Enter your callsign..." required>
                        <div class="invalid-feedback" id="resetUsernameError">Please enter your username.</div>
                    </div>
                    <div class="mb-3">
                        <label for="newPassword" class="form-label">New Password</label>
                        <div class="input-group">
                            <input type="password" id="newPassword" name="new_password" class="form-control" placeholder="Enter new password..." required>
                            <button type="button" class="btn btn-outline-secondary retro-eye-btn" tabindex="-1" style="border-radius: 0 6px 6px 0; background: #222; color: #0ff; border-left: 0;" onclick="togglePasswordVisibility('newPassword', this)">
                                <i class="fas fa-eye-slash"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback" id="newPasswordError">Please enter a new password.</div>
                    </div>
                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Confirm Password</label>
                        <div class="input-group">
                            <input type="password" id="confirmPassword" name="confirm_password" class="form-control" placeholder="Confirm new password..." required>
                            <button type="button" class="btn btn-outline-secondary retro-eye-btn" tabindex="-1" style="border-radius: 0 6px 6px 0; background: #222; color: #0ff; border-left: 0;" onclick="togglePasswordVisibility('confirmPassword', this)">
                                <i class="fas fa-eye-slash"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback" id="confirmPasswordError">Passwords do not match.</div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">EXECUTE</button>
                    </div>
                    <div id="resetLoadingIndicator" class="text-center mt-3"></div>
                    <script>
                    function togglePasswordVisibility(inputId, btn) {
                        const input = document.getElementById(inputId);
                        const icon = btn.querySelector('i');
                        if (input.type === 'password') {
                            input.type = 'text';
                            icon.classList.remove('fa-eye-slash');
                            icon.classList.add('fa-eye');
                        } else {
                            input.type = 'password';
                            icon.classList.remove('fa-eye');
                            icon.classList.add('fa-eye-slash');
                        }
                    }
                    </script>
                </form>
            </div>
        </div>
    </div>
</div>
