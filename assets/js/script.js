/**
 * File: script.js
 * Purpose: Handles core UI interactions, form validation, AJAX submissions, video controls, scroll animations, and modal logic for CodeGaming.
 * Features:
 *   - Validates and submits sign up, login, and password reset forms via AJAX.
 *   - Displays feedback, loading indicators, and handles redirects.
 *   - Manages role selection and admin acceptance for sign up.
 *   - Controls video playback overlays and carousel video pausing (anchor page).
 *   - Implements scroll-triggered animations using anime.js.
 *   - Provides back-to-top button functionality.
 *   - Adds password visibility toggle for login form.
 *   - Handles modal transitions between login and sign up.
 * Usage:
 *   - Included on main pages requiring form handling, video controls, and UI enhancements.
 *   - Requires Bootstrap, anime.js, and specific HTML structure for forms, modals, and videos.
 * Included Files/Dependencies:
 *   - Bootstrap (modals)
 *   - anime.js (animations)
 *   - sign_in.php, login.php, api/reset-password.php (AJAX endpoints)
 * Author: CodeGaming Team
 * Last Updated: July 22, 2025
 */

document.addEventListener('DOMContentLoaded', () => {
   // ---------------------------------------------------
  // 1. Form Validation & Submission
  // ---------------------------------------------------
  const validateForm = (form, fields) => {
    let isValid = true;
    fields.forEach(field => {
      const input = form.querySelector(`#${field.id}`);
      const errorEl = form.querySelector(`#${field.errorId}`);
      
      // Reset previous errors
      input.classList.remove('is-invalid');
      errorEl.textContent = '';
      
      // Required field validation
      if (field.required && !input.value.trim()) {
        input.classList.add('is-invalid');
        errorEl.textContent = `${field.label} is required.`;
        isValid = false;
        return;
      }
      
      // Email validation
      if (field.type === 'email' && input.value.trim()) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(input.value.trim())) {
          input.classList.add('is-invalid');
          errorEl.textContent = 'Please enter a valid email address.';
          isValid = false;
          return;
        }
      }
      
      // Password validation
      if (field.type === 'password' && input.value.trim()) {
        if (input.value.length < 6) {
          input.classList.add('is-invalid');
          errorEl.textContent = 'Password must be at least 6 characters.';
          isValid = false;
          return;
        }
      }
    });
    return isValid;
  };

  const handleFormSubmit = async (form, config) => {
    const submitBtn = form.querySelector('button[type="submit"]');
    const loadingEl = form.querySelector(`#${config.loadingId}`);
    
    try {
      // Disable submit button and show loading
      submitBtn.disabled = true;
      loadingEl.innerHTML = '<div class="spinner-border text-primary" role="status"></div>';
      
      // Validate form
      if (!validateForm(form, config.fields)) {
        throw new Error('Please fix the form errors.');
      }
      
      // Prepare form data
      const formData = new FormData(form);
      const data = {};
      formData.forEach((value, key) => data[key] = value);
      
      // Send request
      const response = await fetch(config.url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
      });
      
      const result = await response.json();
      
      if (!result.success) {
        throw new Error(result.error || 'An error occurred. Please try again.');
      }
      
      // Handle success
      loadingEl.innerHTML = '<div class="alert alert-success">Success!</div>';
      
      // Handle redirects
      if (config.redirect) {
        setTimeout(() => {
          window.location.href = config.redirect(result);
        }, 1000);
      } else if (config.onSuccess) {
        config.onSuccess(result);
      }
      
    } catch (error) {
      loadingEl.innerHTML = `<div class="alert alert-danger">${error.message}</div>`;
    } finally {
      submitBtn.disabled = false;
    }
  };

  // Sign Up Form
  const signUpForm = document.getElementById('signInForm');
  if (signUpForm) {
    const loadingIndicator = document.getElementById('loadingIndicator');
    const feedbackMsg = document.createElement('div');
    feedbackMsg.id = 'signUpFeedbackMsg';
    feedbackMsg.className = 'text-center mt-3';
    signUpForm.appendChild(feedbackMsg);

    // Role selection logic
    const roleUser = document.getElementById('roleUser');
    const roleAdmin = document.getElementById('roleAdmin');
    const adminAcceptanceContainer = document.getElementById('adminAcceptanceContainer');
    const adminAcceptance = document.getElementById('adminAcceptance');
    const adminAcceptanceError = document.getElementById('adminAcceptanceError');

    function updateAdminAcceptance() {
      if (roleAdmin.checked) {
        adminAcceptanceContainer.classList.remove('d-none');
      } else {
        adminAcceptanceContainer.classList.add('d-none');
        adminAcceptance.checked = false;
        adminAcceptance.classList.remove('is-invalid');
        adminAcceptanceError.textContent = '';
      }
    }
    roleUser.addEventListener('change', updateAdminAcceptance);
    roleAdmin.addEventListener('change', updateAdminAcceptance);
    updateAdminAcceptance();

    signUpForm.addEventListener('submit', (e) => {
      e.preventDefault();
      feedbackMsg.textContent = '';
      loadingIndicator.innerHTML = '<div class="spinner-border text-success" role="status"><span class="visually-hidden">Loading...</span></div>';

      // Validate admin acceptance if admin selected
      if (roleAdmin.checked && !adminAcceptance.checked) {
        adminAcceptance.classList.add('is-invalid');
        adminAcceptanceError.textContent = 'Admin acceptance is required.';
        loadingIndicator.innerHTML = '';
        return;
      } else {
        adminAcceptance.classList.remove('is-invalid');
        adminAcceptanceError.textContent = '';
      }

      // Gather form data
      const formData = {
        username: document.getElementById('signUsername').value.trim(),
        email: document.getElementById('signEmail').value.trim(),
        password: document.getElementById('signPassword').value,
        role: roleAdmin.checked ? 'admin' : 'user',
        admin_acceptance: adminAcceptance.checked ? '1' : ''
      };

      // Send AJAX request
      fetch('sign_in.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(formData)
      })
      .then(res => res.json())
      .then(result => {
        loadingIndicator.innerHTML = '';
        feedbackMsg.innerHTML = '';
        if (result.success) {
          feedbackMsg.innerHTML = '<div class="alert alert-success">' + (result.message || 'Sign up successful!') + '</div>';
          setTimeout(() => {
            bootstrap.Modal.getInstance(document.getElementById('signInModal')).hide();
            feedbackMsg.textContent = '';
            document.getElementById('signInForm').reset();
            setTimeout(() => {
              document.querySelector('[data-bs-target=\"#loginModal\"]').click();
            }, 500);
          }, 1200);
        } else {
          feedbackMsg.innerHTML = '<div class="alert alert-danger">' + (result.error || 'Sign up failed.') + '</div>';
        }
      })
      .catch(() => {
        loadingIndicator.innerHTML = '';
        feedbackMsg.innerHTML = '';
        feedbackMsg.innerHTML = '<div class="alert alert-danger">Sign up failed.</div>';
      });
    });
  }

  // Login Form
  const loginForm = document.getElementById('loginForm');
  if (loginForm) {
    const loadingIndicator = document.getElementById('loginLoadingIndicator');
    const feedbackMsg = document.createElement('div');
    feedbackMsg.id = 'loginFeedbackMsg';
    feedbackMsg.className = 'text-center mt-3';
    loginForm.appendChild(feedbackMsg);

    loginForm.addEventListener('submit', (e) => {
      e.preventDefault();
      feedbackMsg.textContent = '';
      loadingIndicator.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';
      handleFormSubmit(loginForm, {
        url: 'login.php',
        loadingId: 'loginLoadingIndicator',
        fields: [
          { id: 'loginEmail', errorId: 'loginEmailError', label: 'Username or Email', required: true },
          { id: 'loginPassword', errorId: 'loginPasswordError', label: 'Password', type: 'password', required: true }
        ],
        redirect: (result) => {
          loadingIndicator.innerHTML = '';
          feedbackMsg.innerHTML = '<div class="alert alert-success">Login successful! Redirecting...</div>';
          if (result.role === 'admin' || result.role === 'super_admin') {
            return 'admin_dashboard.php?login=success';
          }
          return 'home_page.php?login=success';
        },
        onError: (error) => {
          loadingIndicator.innerHTML = '';
          feedbackMsg.innerHTML = '<div class="alert alert-danger">' + (error || 'Login failed.') + '</div>';
        }
      });
    });
  }

  // Forgot Password Form
  const forgotPasswordForm = document.getElementById('forgotPasswordForm');
  if (forgotPasswordForm) {
    forgotPasswordForm.addEventListener('submit', (e) => {
      e.preventDefault();
      e.stopPropagation();

      // Clear previous errors
      const errorElements = forgotPasswordForm.querySelectorAll('.invalid-feedback');
      const inputs = forgotPasswordForm.querySelectorAll('.form-control');
      errorElements.forEach(el => el.textContent = '');
      inputs.forEach(input => input.classList.remove('is-invalid'));

      // Manual validation for empty fields
      const username = forgotPasswordForm.querySelector('#resetUsername').value.trim();
      const newPassword = forgotPasswordForm.querySelector('#newPassword').value.trim();
      const confirmPassword = forgotPasswordForm.querySelector('#confirmPassword').value.trim();

      let hasErrors = false;

      if (!username) {
        forgotPasswordForm.querySelector('#resetUsername').classList.add('is-invalid');
        forgotPasswordForm.querySelector('#resetUsernameError').textContent = 'Please enter your username.';
        hasErrors = true;
      } else {
        forgotPasswordForm.querySelector('#resetUsername').classList.remove('is-invalid');
        forgotPasswordForm.querySelector('#resetUsernameError').textContent = '';
      }

      if (!newPassword) {
        forgotPasswordForm.querySelector('#newPassword').classList.add('is-invalid');
        forgotPasswordForm.querySelector('#newPasswordError').textContent = 'Please enter a new password.';
        hasErrors = true;
      } else {
        forgotPasswordForm.querySelector('#newPassword').classList.remove('is-invalid');
        forgotPasswordForm.querySelector('#newPasswordError').textContent = '';
      }

      if (!confirmPassword) {
        forgotPasswordForm.querySelector('#confirmPassword').classList.add('is-invalid');
        forgotPasswordForm.querySelector('#confirmPasswordError').textContent = 'Please confirm your password.';
        hasErrors = true;
      } else {
        forgotPasswordForm.querySelector('#confirmPassword').classList.remove('is-invalid');
        forgotPasswordForm.querySelector('#confirmPasswordError').textContent = '';
      }

      // Check if passwords match
      if (!hasErrors && newPassword !== confirmPassword) {
        forgotPasswordForm.querySelector('#confirmPassword').classList.add('is-invalid');
        forgotPasswordForm.querySelector('#confirmPasswordError').textContent = 'Passwords do not match.';
        hasErrors = true;
      } else if (!hasErrors) {
        forgotPasswordForm.querySelector('#confirmPassword').classList.remove('is-invalid');
        forgotPasswordForm.querySelector('#confirmPasswordError').textContent = '';
      }

      if (hasErrors) {
        return false; // Stop form submission
      }

      // If validation passes, proceed with form submission
      handleFormSubmit(forgotPasswordForm, {
        url: 'api/reset-password.php',     
        loadingId: 'resetLoadingIndicator',
        fields: [
          { id: 'resetUsername', errorId: 'resetUsernameError', label: 'Username', required: true },
          { id: 'newPassword', errorId: 'newPasswordError', label: 'New Password', type: 'password', required: true },
          { id: 'confirmPassword', errorId: 'confirmPasswordError', label: 'Confirm Password', type: 'password', required: true }
        ],
        onSuccess: () => {
          // Close modal and show login
          bootstrap.Modal.getInstance(document.getElementById('forgotPasswordModal')).hide();
          setTimeout(() => {
            document.querySelector('[data-bs-target="#loginModal"]').click();
          }, 1000);
        }
      });
    });
  }

  // ---------------------------------------------------
  // 2. Back to Top Button Functionality
  // ---------------------------------------------------
  
  const backToTopBtn = document.getElementById('backToTop');
  
  if (backToTopBtn) {
    // Show/hide button based on scroll position
    window.addEventListener('scroll', function() {
      if (window.scrollY > 300) {
        backToTopBtn.style.display = 'block';
      } else {
        backToTopBtn.style.display = 'none';
      }
    });
    
    // Smooth scroll to top when button is clicked
    backToTopBtn.addEventListener('click', function() {
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    });
  }

  // ---------------------------------------------------
  // 3. Video Controls (Anchor Page Specific)
  // ---------------------------------------------------
  const videoContainers = document.querySelectorAll('.video-container');
  
  if (videoContainers.length > 0) {
    videoContainers.forEach(container => {
      const video = container.querySelector('video');
      const overlay = container.querySelector('.play-overlay');
      
      if (video && overlay) {
        overlay.addEventListener('click', () => {
          if (video.paused) {
            video.play();
            overlay.style.display = 'none';
          } else {
            video.pause();
            overlay.style.display = 'flex';
          }
        });
        
        video.addEventListener('ended', () => {
          overlay.style.display = 'flex';
        });
      }
    });
    
    // Pause all videos when carousel slides
    const carousel = document.getElementById('gameModesCarousel');
    if (carousel) {
      carousel.addEventListener('slide.bs.carousel', () => {
        document.querySelectorAll('.game-mode-video').forEach(video => {
          video.pause();
          const overlay = video.parentElement.querySelector('.play-overlay');
          if (overlay) {
            overlay.style.display = 'flex';
          }
        });
      });
    }
  }

  // ---------------------------------------------------
  // 4. Scroll Animations
  // ---------------------------------------------------
  const animateOnScroll = () => {
    const elements = document.querySelectorAll('.animate-on-scroll');
    elements.forEach(element => {
      const rect = element.getBoundingClientRect();
      const isVisible = (rect.top <= window.innerHeight * 0.8);
      
      if (isVisible && !element.classList.contains('animated')) {
        element.classList.add('animated');
        anime({
          targets: element,
          opacity: [0, 1],
          translateY: [20, 0],
          easing: 'easeOutExpo',
          duration: 1000
        });
      }
    });
  };

  // Initial check for elements in view
  animateOnScroll();
  
  // Check on scroll
  window.addEventListener('scroll', animateOnScroll);

  // Login Form - Add peeking eye icon logic
  const loginPassword = document.getElementById('loginPassword');
  if (loginPassword) {
    const inputGroup = loginPassword.parentElement;
    if (inputGroup && !inputGroup.querySelector('.retro-eye-btn')) {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'btn btn-outline-secondary retro-eye-btn';
      btn.tabIndex = -1;
      btn.style = 'border-radius: 0 6px 6px 0; background: #222; color: #0ff; border-left: 0;';
      btn.innerHTML = '<i class="fas fa-eye-slash"></i>';
      btn.onclick = function() {
        const icon = btn.querySelector('i');
        if (loginPassword.type === 'password') {
          loginPassword.type = 'text';
          icon.classList.remove('fa-eye-slash');
          icon.classList.add('fa-eye');
        } else {
          loginPassword.type = 'password';
          icon.classList.remove('fa-eye');
          icon.classList.add('fa-eye-slash');
        }
      };
      inputGroup.appendChild(btn);
      inputGroup.classList.add('input-group');
    }
  }

  // Show Sign Up from Login Modal
  const showSignUpFromLogin = document.getElementById('showSignUpFromLogin');
  if (showSignUpFromLogin) {
    showSignUpFromLogin.addEventListener('click', function(e) {
      e.preventDefault();
      // Hide login modal, then show signup modal
      const loginModal = bootstrap.Modal.getInstance(document.getElementById('loginModal'));
      if (loginModal) loginModal.hide();
      setTimeout(() => {
        const signUpModal = new bootstrap.Modal(document.getElementById('signInModal'));
        signUpModal.show();
      }, 400);
    });
  }
});