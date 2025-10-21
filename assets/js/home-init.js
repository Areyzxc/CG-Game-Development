/**
 * Home Page Initialization
 * - Handles first visit check
 * - Initializes page components
 * - Manages welcome modal
 */

document.addEventListener('DOMContentLoaded', async function() {
    // Initialize API helper
    if (typeof APIHelper === 'undefined') {
        console.error('APIHelper is not loaded');
        return;
    }

    // Check if it's the user's first visit
    await checkFirstVisit();
    
    // Initialize other components
    initializePageComponents();
});

/**
 * Check if it's the user's first visit
 */
async function checkFirstVisit() {
    try {
        // For guests, we'll use localStorage
        const guestKey = 'codegaming_guest_first_visit';
        const isGuest = !window.CG_USER_ID;
        
        if (isGuest) {
            const hasVisitedBefore = localStorage.getItem(guestKey);
            if (!hasVisitedBefore) {
                localStorage.setItem(guestKey, '1');
                showWelcomeModal();
            }
            return;
        }
        
        // For logged-in users, check with the server
        const data = await APIHelper.fetchWithAuth('api/check-first-visit.php');
        
        if (data && data.first_visit) {
            showWelcomeModal();
            
            // Handle "Don't show again" checkbox
            const dontShowAgainCheck = document.getElementById('dontShowAgainCheck');
            const welcomeModal = document.getElementById('welcomeModal');
            
            if (dontShowAgainCheck && welcomeModal) {
                dontShowAgainCheck.addEventListener('change', async function() {
                    if (this.checked) {
                        try {
                            await APIHelper.fetchWithAuth('api/update-user-preferences.php', {
                                method: 'POST',
                                body: JSON.stringify({
                                    preference: 'welcome_dont_show',
                                    value: '1'
                                })
                            });
                        } catch (error) {
                            console.error('Failed to update user preferences:', error);
                        }
                    }
                });
                
                // Show the modal
                const modal = new bootstrap.Modal(welcomeModal);
                modal.show();
            }
        }
    } catch (error) {
        console.error('Error checking first visit:', error);
    }
}

/**
 * Show welcome modal
 */
function showWelcomeModal() {
    const welcomeModal = document.getElementById('welcomeModal');
    if (!welcomeModal) return;
    
    // Ensure modal is in the correct position in the DOM
    if (welcomeModal.parentNode !== document.body) {
        document.body.appendChild(welcomeModal);
    }
    
    // Position the modal properly
    const modalDialog = welcomeModal.querySelector('.modal-dialog');
    if (modalDialog) {
        modalDialog.style.margin = '1.75rem auto';
    }
    
    // Initialize and show the modal
    const modal = new bootstrap.Modal(welcomeModal, {
        backdrop: 'static',
        keyboard: false,
        focus: true
    });
    
    // Force repositioning
    modal._handleUpdate = function() {
        this._adjustDialog();
    };
    
    modal.show();
}

/**
 * Initialize page components
 */
function initializePageComponents() {
    // Initialize any other components here
    if (typeof initializeHomeEnhancements === 'function') {
        initializeHomeEnhancements();
    }
    
    // Initialize any other components here
    if (typeof initializeQuoteSpotlight === 'function') {
        initializeQuoteSpotlight();
    }
    
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

// Make functions available globally if needed
window.checkFirstVisit = checkFirstVisit;
