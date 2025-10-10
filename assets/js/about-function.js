/**
 * File: about-function.js
 * Purpose: Enhanced interactive logic for the Northsidefestival 2013 inspired About page
 * Features:
 *   - ScrollReveal animations for enhanced UX
 *   - Team modal functionality with Bootstrap 5
 *   - FAQ search functionality
 *   - AJAX feedback form submission and like system
 *   - Floating feedback button interactions
 *   - Timeline carousel controls
 *   - Responsive interactions and mobile optimizations
 * Design: Northsidefestival 2013 inspired with modern interactions
 * Dependencies:
 *   - Bootstrap 5 (modals, carousel, accordion)
 *   - ScrollReveal.js (animations)
 *   - FontAwesome (icons)
 * Author: Code Gaming Team
 * Last Updated: September 28, 2025
 */

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
  
  // Initialize ScrollReveal animations
  if (typeof ScrollReveal !== 'undefined') {
    const sr = ScrollReveal({
      origin: 'bottom',
      distance: '60px',
      duration: 1000,
      delay: 100,
      easing: 'ease-out',
      mobile: true,
      reset: false
    });

    // Animate elements with data-reveal attributes
    sr.reveal('[data-reveal="fade-up"]', {
      origin: 'bottom',
      delay: 200
    });
    
    sr.reveal('[data-reveal="fade-left"]', {
      origin: 'right',
      delay: 300
    });
    
    sr.reveal('[data-reveal="fade-right"]', {
      origin: 'left',
      delay: 300
    });
    
    // Animate team cards with stagger effect
    sr.reveal('.team-card', {
      interval: 200
    });
    
    // Animate timeline cards
    sr.reveal('.timeline-card', {
      interval: 150
    });
    
    // Animate stat cards
    sr.reveal('.stat-card', {
      interval: 100
    });
  }

  // FAQ Search functionality
  const faqSearch = document.getElementById('faqSearch');
  const faqHeaderSearch = document.getElementById('faq-search');
  
  function setupFAQSearch(searchInput) {
    if (searchInput) {
      searchInput.addEventListener('keyup', function() {
        const value = this.value.toLowerCase();
        const faqItems = document.querySelectorAll('.faq-item, .accordion-item');
        
        faqItems.forEach(function(item) {
          const text = item.textContent.toLowerCase();
          if (text.indexOf(value) > -1) {
            item.style.display = '';
          } else {
            item.style.display = 'none';
          }
        });
      });
    }
  }
  
  // Setup search for both FAQ search inputs
  setupFAQSearch(faqSearch);
  setupFAQSearch(faqHeaderSearch);

  // Team Modal functionality
  const teamModal = document.getElementById('teamModal');
  if (teamModal) {
    teamModal.addEventListener('show.bs.modal', function(event) {
      const button = event.relatedTarget;
      const name = button.getAttribute('data-name');
      const role = button.getAttribute('data-role');
      const bio = button.getAttribute('data-bio');
      const email = button.getAttribute('data-email');
      const photo = button.getAttribute('data-photo');
      const funfact = button.getAttribute('data-funfact');
      const mission = button.getAttribute('data-mission');
      const facebook = button.getAttribute('data-facebook');
      const instagram = button.getAttribute('data-instagram');
      const github = button.getAttribute('data-github');
      
      // Update modal content
      document.getElementById('modal-member-name').textContent = name;
      document.getElementById('modal-member-role').textContent = role;
      document.getElementById('modal-member-bio').textContent = bio;
      document.getElementById('modal-member-funfact').textContent = funfact;
      document.getElementById('modal-member-mission').textContent = mission;
      document.getElementById('modal-member-photo').src = photo;
      
      // Update email if element exists
      const emailElement = document.getElementById('modal-member-email');
      if (emailElement && email) {
        emailElement.innerHTML = `<a href="mailto:${email}">${email}</a>`;
      }
      
      // Update social links
      const socialContainer = document.getElementById('modal-member-social');
      socialContainer.innerHTML = '';
      
      if (facebook && facebook !== '#') {
        socialContainer.innerHTML += `<a href="${facebook}" target="_blank"><i class="fab fa-facebook"></i></a>`;
      }
      if (instagram && instagram !== '#') {
        socialContainer.innerHTML += `<a href="${instagram}" target="_blank"><i class="fab fa-instagram"></i></a>`;
      }
      if (github && github !== '#') {
        socialContainer.innerHTML += `<a href="${github}" target="_blank"><i class="fab fa-github"></i></a>`;
      }
    });
  }

  // Floating feedback button functionality
  const floatingBtn = document.getElementById('floating-feedback-btn');
  const feedbackSection = document.getElementById('feedback');
  
  if (floatingBtn && feedbackSection) {
    floatingBtn.addEventListener('click', function() {
      feedbackSection.scrollIntoView({ 
        behavior: 'smooth',
        block: 'start'
      });
    });
  }

  // Add loading states for AJAX requests
  function showLoading(element) {
    element.style.opacity = '0.6';
    element.style.pointerEvents = 'none';
  }

  function hideLoading(element) {
    element.style.opacity = '1';
    element.style.pointerEvents = 'auto';
  }

  // AJAX Feedback form submission
  const feedbackForm = document.getElementById('feedback-form');
  const feedbackList = document.getElementById('feedback-list');
  const feedbackStatus = document.getElementById('feedback-status');
  const cancelBtn = document.getElementById('cancel-feedback-btn');
  
  // Cancel button functionality
  if (cancelBtn) {
    cancelBtn.addEventListener('click', function() {
      feedbackForm.reset();
      // Clear validation classes
      feedbackForm.querySelectorAll('.is-valid, .is-invalid').forEach(field => {
        field.classList.remove('is-valid', 'is-invalid');
      });
      // Hide status message
      if (feedbackStatus) {
        feedbackStatus.style.display = 'none';
      }
    });
  }
  
  // Show status message
  function showStatus(message, isSuccess = true) {
    if (feedbackStatus) {
      feedbackStatus.textContent = message;
      feedbackStatus.className = isSuccess ? 'success' : 'error';
      feedbackStatus.style.display = 'block';
      
      // Auto-hide after 5 seconds
      setTimeout(() => {
        feedbackStatus.style.display = 'none';
      }, 5000);
    }
  }
  
  if (feedbackForm) {
    feedbackForm.addEventListener('submit', function(e) {
      e.preventDefault();
      const formData = new FormData(feedbackForm);
      const submitBtn = document.getElementById('submit-feedback-btn');
      
      // Show loading state
      showLoading(feedbackForm);
      if (submitBtn) {
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';
        submitBtn.disabled = true;
      }
      
      fetch('api/send-feedback.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          // Add to feedback wall
          const name = formData.get('name');
          const message = formData.get('message');
          if (feedbackList && name && message) {
            const feedbackItem = document.createElement('div');
            feedbackItem.className = 'feedback-item';
            feedbackItem.setAttribute('data-feedback-id', data.feedback_id || 0);
            feedbackItem.innerHTML = `
              <div class="feedback-header">
                <strong>${escapeHtml(name)}</strong>
                <small>Just now</small>
              </div>
              <p>${escapeHtml(message)}</p>
              <div class="feedback-actions">
                <button class="like-btn btn btn-sm btn-outline-danger" data-feedback-id="${data.feedback_id || 0}">
                  <i class="fas fa-heart"></i> 
                  <span class="like-count">0</span>
                </button>
              </div>
            `;
            feedbackList.prepend(feedbackItem);
            // Re-bind like buttons for new feedback
            bindLikeButtons();
          }
          
          // Show success message
          showStatus('Thank you for your feedback! It has been added to the wall.', true);
          feedbackForm.reset();
          // Clear validation classes
          feedbackForm.querySelectorAll('.is-valid, .is-invalid').forEach(field => {
            field.classList.remove('is-valid', 'is-invalid');
          });
        } else {
          showStatus(data.error || 'An error occurred while sending feedback.', false);
        }
      })
      .catch(() => {
        showStatus('An error occurred while sending feedback. Please try again.', false);
      })
      .finally(() => {
        // Hide loading state
        hideLoading(feedbackForm);
        if (submitBtn) {
          submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Send Feedback';
          submitBtn.disabled = false;
        }
      });
    });
  }
  
  // Helper function to escape HTML
  function escapeHtml(text) {
    const map = {
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
  }

  // Like button AJAX functionality
  function bindLikeButtons() {
    document.querySelectorAll('.like-btn').forEach(btn => {
      // Remove existing event listeners to prevent duplicates
      btn.replaceWith(btn.cloneNode(true));
    });
    
    document.querySelectorAll('.like-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        const feedbackId = this.getAttribute('data-feedback-id');
        const likeCountSpan = this.querySelector('.like-count');
        const originalText = this.innerHTML;
        
        if (!feedbackId || this.disabled) return;
        
        // Show loading state
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>...</span>';
        this.disabled = true;
        
        fetch('api/like-feedback.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: 'feedback_id=' + encodeURIComponent(feedbackId)
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            this.innerHTML = `<i class="fas fa-heart"></i> <span class="like-count">${data.likes}</span>`;
            this.classList.add('liked');
            this.disabled = true; // Keep disabled after successful like
          } else if (data.error === 'Already liked') {
            this.innerHTML = originalText;
            this.classList.add('liked');
            this.disabled = true;
            showStatus('You have already liked this feedback!', false);
          } else {
            this.innerHTML = originalText;
            this.disabled = false;
            showStatus(data.error || 'Failed to like feedback', false);
          }
        })
        .catch(err => {
          console.error('Error liking feedback:', err);
          this.innerHTML = originalText;
          this.disabled = false;
          showStatus('Network error. Please try again.', false);
        });
      });
    });
  }
  
  // Initialize like buttons
  bindLikeButtons();

  // Additional smooth scrolling for internal links
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      e.preventDefault();
      const target = document.querySelector(this.getAttribute('href'));
      if (target) {
        target.scrollIntoView({
          behavior: 'smooth',
          block: 'start'
        });
      }
    });
  });

  // Initialize audio controls for playlist
  const audioElements = document.querySelectorAll('audio');
  audioElements.forEach(audio => {
    audio.addEventListener('play', function() {
      // Pause other audio elements when one starts playing
      audioElements.forEach(otherAudio => {
        if (otherAudio !== this) {
          otherAudio.pause();
        }
      });
    });
  });

  // Enhanced form validation
  const forms = document.querySelectorAll('form');
  forms.forEach(form => {
    const inputs = form.querySelectorAll('input, textarea, select');
    inputs.forEach(input => {
      input.addEventListener('blur', function() {
        validateField(this);
      });
    });
  });

  function validateField(field) {
    const fieldValue = field.value.trim();
    const isValid = field.checkValidity() && fieldValue.length > 0;
    
    if (!isValid) {
      field.classList.add('is-invalid');
      field.classList.remove('is-valid');
    } else {
      field.classList.add('is-valid');
      field.classList.remove('is-invalid');
    }
  }

  console.log('About page enhanced functionality loaded successfully!');
});