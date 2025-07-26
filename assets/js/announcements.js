/**
 * File: announcements.js
 * Purpose: Handles user announcements page logic for CodeGaming, including fetching, rendering, pagination, and modal display.
 * Features:
 *   - Fetches announcements from API and displays them in a paginated list.
 *   - Renders announcement items with avatars, badges, and meta info.
 *   - Supports keyboard accessibility and modal popups for full announcement details.
 *   - Handles errors and empty states gracefully.
 * Usage:
 *   - Included on the announcements page for user-facing updates and news.
 *   - Requires HTML elements for container, list, and pagination.
 *   - Relies on API endpoint: api/get_announcements.php.
 * Included Files/Dependencies:
 *   - FontAwesome (icons)
 *   - Bootstrap (optional for modal styling)
 * Author: CodeGaming Team
 * Last Updated: July 22, 2025
 */

// announcements.js - User Announcements Page Logic

document.addEventListener('DOMContentLoaded', function() {
  const container = document.getElementById('announcementsContainer');
  const listEl = document.getElementById('announcementList');
  const paginationEl = document.getElementById('announcementsPagination');
  let currentPage = 1;
  const pageSize = 10;
  let totalPages = 1;

  // --- Fetch Announcements ---
  function fetchAnnouncements(page = 1) {
    fetch(`api/get_announcements.php?page=${page}`)
      .then(res => res.json())
      .then(data => {
        if (data.success && Array.isArray(data.announcements)) {
          renderAnnouncements(data.announcements);
          totalPages = data.total_pages || 1;
          renderPagination(page, totalPages);
        } else {
          listEl.innerHTML = '<li class="announcement-item text-muted">No announcements found.</li>';
          paginationEl.innerHTML = '';
        }
      })
      .catch(() => {
        listEl.innerHTML = '<li class="announcement-item text-danger">Failed to load announcements.</li>';
        paginationEl.innerHTML = '';
      });
  }

  // --- Render Announcements List ---
  function renderAnnouncements(announcements) {
    listEl.innerHTML = '';
    announcements.forEach(a => {
      const li = document.createElement('li');
      li.className = 'announcement-item';
      li.tabIndex = 0;
      li.setAttribute('role', 'button');
      li.setAttribute('aria-label', a.title);
      // Snippet: first 100 chars, no HTML tags
      const snippet = a.content.replace(/<[^>]+>/g, '').slice(0, 100) + (a.content.length > 100 ? '...' : '');
      // Avatar fallback
      const avatar = a.author_avatar || 'images/PTC.png';
      // Date
      const date = a.created_at || '';
      // Badge
      let badge = '';
      if (a.is_pinned) badge = '<span class="announcement-badge featured">Pinned</span>';
      li.innerHTML = `
        <img src="${avatar}" alt="${a.author_name || 'Admin'}'s avatar" class="announcement-avatar">
        <div class="announcement-content">
          <div class="announcement-title">${badge}${a.title}</div>
          <div class="announcement-snippet">${snippet}</div>
          <div class="announcement-meta">
            <span><i class="fa fa-user"></i> ${a.author_name || 'Admin'}</span>
            <span><i class="fa fa-calendar"></i> ${date}</span>
          </div>
        </div>
      `;
      li.addEventListener('click', () => openModal(a));
      li.addEventListener('keypress', e => { if (e.key === 'Enter') openModal(a); });
      listEl.appendChild(li);
    });
  }

  // --- Pagination ---
  function renderPagination(page, total) {
    paginationEl.innerHTML = '';
    if (total <= 1) return;
    for (let i = 1; i <= total; i++) {
      const btn = document.createElement('button');
      btn.textContent = i;
      btn.className = (i === page ? 'active' : '');
      btn.disabled = (i === page);
      btn.addEventListener('click', () => {
        currentPage = i;
        fetchAnnouncements(i);
        window.scrollTo({ top: container.offsetTop - 30, behavior: 'smooth' });
      });
      paginationEl.appendChild(btn);
    }
  }

  // --- Modal ---
  function openModal(a) {
    // Remove any existing modal
    const old = document.getElementById('pixelModalOverlay');
    if (old) old.remove();
    // Modal HTML
    const overlay = document.createElement('div');
    overlay.className = 'pixel-modal-overlay';
    overlay.id = 'pixelModalOverlay';
    overlay.innerHTML = `
      <div class="pixel-modal" role="dialog" aria-modal="true">
        <button class="modal-close" aria-label="Close">X</button>
        <div class="modal-title">${a.is_pinned ? '<span class=\'modal-badge\'>Pinned</span>' : ''}${a.title}</div>
        <div class="modal-meta">
          <img src="${a.author_avatar || 'images/PTC.png'}" class="modal-avatar" alt="${a.author_name || 'Admin'}'s avatar">
          <span><i class="fa fa-user"></i> ${a.author_name || 'Admin'}</span>
          <span><i class="fa fa-calendar"></i> ${a.created_at || ''}</span>
        </div>
        <div class="modal-content">${a.content.replace(/\n/g, '<br>')}</div>
      </div>
    `;
    document.body.appendChild(overlay);
    // Close logic
    overlay.querySelector('.modal-close').addEventListener('click', closeModal);
    overlay.addEventListener('click', e => { if (e.target === overlay) closeModal(); });
    document.addEventListener('keydown', escListener);
    function escListener(e) { if (e.key === 'Escape') closeModal(); }
    function closeModal() {
      overlay.remove();
      document.removeEventListener('keydown', escListener);
    }
  }

  // --- Initial Load ---
  fetchAnnouncements(currentPage);
});