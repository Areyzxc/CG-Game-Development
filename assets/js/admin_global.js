/**
 * File: admin_global.js
 * Purpose: Contains shared JavaScript for the CodeGaming admin panel, including sidebar navigation and mobile UI controls.
 * Features:
 *   - Handles sidebar toggle logic for mobile and desktop views.
 *   - Manages sidebar overlay and close button interactions.
 * Usage:
 *   - Included on all admin panel pages for consistent sidebar navigation.
 *   - Requires HTML elements for sidebar, toggler, overlay, and close button.
 * Included Files/Dependencies:
 *   - Bootstrap (optional for UI)
 * Author: CodeGaming Team
 * Last Updated: July 22, 2025
 */

// admin_global.js
// Contains shared JavaScript for the admin panel, like sidebar navigation.

document.addEventListener('DOMContentLoaded', function() {
  // --- Sidebar Toggle for Mobile ---
  const sidebarToggler = document.getElementById('sidebarToggler');
  const sidebar = document.getElementById('adminSidebar');
  const sidebarOverlay = document.getElementById('sidebarOverlay');
  const closeSidebarBtn = document.getElementById('closeSidebar');

  const toggleSidebar = () => {
    if (sidebar && sidebarOverlay) {
        sidebar.classList.toggle('active');
        sidebarOverlay.classList.toggle('active');
    }
  };

  if (sidebarToggler) {
    sidebarToggler.addEventListener('click', toggleSidebar);
  }
  if (sidebarOverlay) {
    sidebarOverlay.addEventListener('click', toggleSidebar);
  }
  if (closeSidebarBtn) {
    closeSidebarBtn.addEventListener('click', toggleSidebar);
  }
});