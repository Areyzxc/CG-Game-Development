/**
 * File: welcome-modal.js
 * Purpose: Handles the welcome modal functionality for first-time visitors
 * Features:
 *   - Detects first visit for both guests and logged-in users
 *   - Shows personalized welcome modal with confetti animation
 *   - Updates database/localStorage to track modal completion
 *   - Redirects admins to dashboard after modal close
 * Usage:
 *   - Include this file on the home page
 *   - Requires Bootstrap modal functionality
 * Author: CodeGaming Team
 * Last Updated: September 27, 2025
 */

// Only initialize if not already initialized
if (!window.welcomeModalInitialized) {
  document.addEventListener('DOMContentLoaded', () => {
    initWelcomeModal();
  });

  window.welcomeModalInitialized = true;
}

// ─────────────────────────────────────────────────────────
// Welcome Modal Functionality
// ─────────────────────────────────────────────────────────
function initWelcomeModal() {
  const welcomeModal = document.getElementById('welcomeModal');
  if (!welcomeModal) return;

  // Position the modal properly
  const modalDialog = welcomeModal.querySelector('.modal-dialog');
  if (modalDialog) {
    modalDialog.style.margin = '1.75rem auto';
    modalDialog.style.maxWidth = '600px';
  }

  // Ensure modal is in the correct position in the DOM
  if (welcomeModal.parentNode !== document.body) {
    document.body.appendChild(welcomeModal);
  }

  // Add event delegation for close buttons
  document.addEventListener('click', (e) => {
    if (e.target.matches('[data-bs-dismiss="modal"], [data-bs-dismiss="modal"] *')) {
      const modal = bootstrap.Modal.getInstance(welcomeModal);
      if (modal) {
        modal.hide();
      }
    }
  });

  // Check if user should see the welcome modal
  checkFirstVisit().then(shouldShow => {
    if (shouldShow) {
      // Add confetti animation
      createConfetti();
      
      // Show modal with delay for better UX
      setTimeout(() => {
        const modal = new bootstrap.Modal(welcomeModal, {
          backdrop: true, // Changed from 'static' to allow closing by clicking outside
          keyboard: true, // Changed to allow closing with ESC key
          focus: true
        });
        
        // Add proper event listeners
        welcomeModal.addEventListener('hide.bs.modal', () => {
          removeConfetti();
          handleModalClose();
        });
        
        // Show the modal
        modal.show();
      }, 1000);

      // Handle modal close event
      welcomeModal.addEventListener('hidden.bs.modal', () => {
        handleModalClose();
        removeConfetti();
        
        // Redirect admin to dashboard if they are admin
        if (window.CG_USER_ID && isAdmin()) {
          setTimeout(() => {
            window.location.href = 'admin_dashboard.php';
          }, 500);
        }
      });

      // Add tracking for accordion clicks
      setupAccordionTracking();
    }
  });
}

// Check if this is the user's first visit
async function checkFirstVisit() {
  try {
    // For guests, use enhanced localStorage checking
    if (!window.CG_USER_ID) {
      return checkGuestPreferences();
    }

    // For logged-in users, check database
    const response = await fetch('api/check-first-visit.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': window.CSRF_TOKEN || ''
      },
      body: JSON.stringify({
        user_id: window.CG_USER_ID
      })
    });

    if (response.ok) {
      const data = await response.json();
      return data.first_visit === true;
    }
  } catch (error) {
    console.error('Error checking first visit:', error);
  }
  
  // Default to not showing modal if there's an error
  return false;
}

// Mark first visit as complete
async function markFirstVisitComplete() {
  try {
    // For guests, use localStorage
    if (!window.CG_USER_ID) {
      localStorage.setItem('cg_welcome_modal_seen', 'true');
      return;
    }

    // For logged-in users, update database
    await fetch('api/update-first-visit.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': window.CSRF_TOKEN || ''
      },
      body: JSON.stringify({
        user_id: window.CG_USER_ID
      })
    });
  } catch (error) {
    console.error('Error marking first visit complete:', error);
  }
}

// Check if current user is admin
function isAdmin() {
  // This would need to be set from PHP
  return window.CG_USER_ROLE === 'admin' || window.CG_IS_ADMIN === true;
}

// Create confetti animation
function createConfetti() {
  const modalContent = document.querySelector('.welcome-modal-content');
  if (!modalContent) return;

  for (let i = 0; i < 50; i++) {
    const confetti = document.createElement('div');
    confetti.className = 'confetti';
    confetti.style.left = Math.random() * 100 + '%';
    confetti.style.animationDelay = Math.random() * 3 + 's';
    confetti.style.backgroundColor = getRandomColor();
    modalContent.appendChild(confetti);
  }
}

// Remove confetti animation
function removeConfetti() {
  const confettiElements = document.querySelectorAll('.confetti');
  confettiElements.forEach(el => el.remove());
}

// Get random color for confetti
function getRandomColor() {
  const colors = ['#e74c3c', '#3498db', '#2ecc71', '#f39c12', '#9b59b6', '#e67e22', '#1abc9c'];
  return colors[Math.floor(Math.random() * colors.length)];
}

// Handle modal close with preference checking
async function handleModalClose() {
  const dontShowAgainCheck = document.getElementById('dontShowAgainCheck');
  const dontShowAgain = dontShowAgainCheck && dontShowAgainCheck.checked;

  // Mark first visit as complete
  await markFirstVisitComplete();

  // Track modal completion
  await trackPreference('modal_completed');

  // Handle "don't show again" preference
  if (dontShowAgain) {
    await handleDontShowAgain();
  }
}

// Handle "don't show again" preference
async function handleDontShowAgain() {
  try {
    if (!window.CG_USER_ID) {
      // For guests, set a more permanent localStorage flag
      localStorage.setItem('cg_welcome_modal_dont_show', 'true');
      return;
    }

    // For logged-in users, update database
    await trackPreference('set_dont_show_again', null, true);
  } catch (error) {
    console.error('Error setting dont show again preference:', error);
  }
}

// Setup tracking for accordion button clicks
function setupAccordionTracking() {
  const accordionButtons = document.querySelectorAll('.welcome-accordion .accordion-button[data-section]');
  
  accordionButtons.forEach(button => {
    button.addEventListener('click', () => {
      const section = button.getAttribute('data-section');
      if (section) {
        trackPreference('track_section_click', section);
      }
    });
  });
}

// Track user preferences and interactions
async function trackPreference(action, section = null, dontShowAgain = false) {
  try {
    const payload = {
      action: action,
      section: section,
      dont_show_again: dontShowAgain
    };

    await fetch('api/track-welcome-preferences.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': window.CSRF_TOKEN || ''
      },
      body: JSON.stringify(payload)
    });
  } catch (error) {
    console.error('Error tracking preference:', error);
  }
}

// Enhanced guest check that includes "don't show again" preference
function checkGuestPreferences() {
  const hasSeenModal = localStorage.getItem('cg_welcome_modal_seen');
  const dontShowAgain = localStorage.getItem('cg_welcome_modal_dont_show');
  
  // Don't show if they've seen it or explicitly opted out
  return !(hasSeenModal || dontShowAgain);
}
