/**
 * File: admin_announcements.js
 * Purpose: Handles admin announcements management for CodeGaming, including fetching, rendering, filtering, pagination, pin/unpin, add/edit/delete, and sidebar updates.
 * Features:
 *   - Fetches and displays announcements with pagination, search, status, and category filters.
 *   - Supports add, edit, delete, and pin/unpin actions with modals and confirmation.
 *   - Updates sidebar with recent, stats, and featured announcements from API response.
 *   - Handles back-to-top button and responsive UI.
 * Usage:
 *   - Included on admin announcements management pages for site updates and moderation.
 *   - Requires HTML elements for cards list, pagination, filters, modals, and sidebar.
 *   - Relies on API endpoints: api/admin_get_announcements.php, api/admin_post_announcement.php, api/admin_edit_announcement.php, api/admin_delete_announcement.php.
 * Included Files/Dependencies:
 *   - Bootstrap (modals)
 *   - FontAwesome (icons)
 * Author: CodeGaming Team
 * Last Updated: July 22, 2025
 */
document.addEventListener('DOMContentLoaded', function() {
    const cardsList = document.getElementById('announcementCardsList');
    const pagination = document.getElementById('announcementPagination');
    const searchInput = document.getElementById('announcementSearch');
    const statusFilter = document.getElementById('announcementStatusFilter');
    const categoryFilter = document.getElementById('announcementCategoryFilter');
    const addBtn = document.getElementById('addAnnouncementBtn');
    const modal = new bootstrap.Modal(document.getElementById('announcementModal'));
    const form = document.getElementById('announcementForm');
    const backToTopBtn = document.getElementById('backToTopBtn');
    let currentPage = 1;
    let totalPages = 1;
    let announcements = [];

    // Fetch and render announcements
    function fetchAnnouncements(page = 1) {
        const params = new URLSearchParams({
            page,
            q: searchInput.value.trim(),
            status: statusFilter.value,
            category: categoryFilter.value
        });
        fetch('api/admin_get_announcements.php?' + params)
            .then(res => res.json())
            .then(data => {
                announcements = data.announcements || [];
                totalPages = data.total_pages || 1;
                renderAnnouncements();
                renderPagination();
                updateSidebar(data);
            });
    }

    function renderAnnouncements() {
        if (!announcements.length) {
            cardsList.innerHTML = '<div class="text-muted text-center py-5">No announcements found.</div>';
            return;
        }
        cardsList.innerHTML = announcements.map(a => {
            const isPinned = a.is_pinned == 1 || a.is_pinned === '1';
            return `
            <div class="announcement-card">
                <div class="announcement-actions">
                    <button class="btn btn-outline-primary btn-sm edit-announcement" data-id="${a.id}" title="Edit"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-outline-danger btn-sm delete-announcement" data-id="${a.id}" title="Delete"><i class="fas fa-trash"></i></button>
                    <button class="btn btn-outline-warning btn-sm pin-announcement${isPinned ? ' pinned' : ''}" data-id="${a.id}" title="${isPinned ? 'Unpin' : 'Pin'}"><i class="fas fa-thumbtack${isPinned ? ' text-warning' : ''}"></i></button>
                </div>
                <div class="announcement-title">${a.title}</div>
                <div class="announcement-meta">
                    <span class="announcement-badge">${a.category || 'General'}</span>
                    <span>${a.status || 'Published'}</span> &bull; ${a.date || ''} &bull; <i class="fas fa-user-circle"></i> ${a.created_by || 'Admin'}
                </div>
                <div class="announcement-content">${a.content.length > 180 ? a.content.slice(0, 180) + '...' : a.content}</div>
            </div>
            `;
        }).join('');
    }

    function renderPagination() {
        if (totalPages <= 1) {
            pagination.innerHTML = '';
            return;
        }
        let html = '<ul class="pagination justify-content-center">';
        for (let i = 1; i <= totalPages; i++) {
            html += `<li class="page-item${i === currentPage ? ' active' : ''}"><a class="page-link" href="#">${i}</a></li>`;
        }
        html += '</ul>';
        pagination.innerHTML = html;
        pagination.querySelectorAll('.page-link').forEach((link, idx) => {
            link.addEventListener('click', e => {
                e.preventDefault();
                currentPage = idx + 1;
                fetchAnnouncements(currentPage);
            });
        });
    }

    // Pin/Unpin Announcement
    let pinAnnouncementId = null;
    cardsList.addEventListener('click', function(e) {
        if (e.target.closest('.pin-announcement')) {
            pinAnnouncementId = e.target.closest('.pin-announcement').dataset.id;
            const isPinned = e.target.closest('.pin-announcement').classList.contains('pinned');
            // Show confirmation modal
            const pinModalHtml = `
                <div class="modal fade" id="pinModal" tabindex="-1" aria-labelledby="pinModalLabel" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header bg-warning-subtle">
                        <h5 class="modal-title" id="pinModalLabel"><i class="fas fa-thumbtack me-2 text-warning"></i>${isPinned ? 'Unpin Announcement' : 'Pin Announcement'}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        ${isPinned ? 'Are you sure you want to unpin this post?' : 'Are you sure you want to pin this announcement as featured?'}
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Dismiss</button>
                        <button type="button" class="btn btn-warning" id="confirmPinBtn">Confirm</button>
                      </div>
                    </div>
                  </div>
                </div>`;
            document.body.insertAdjacentHTML('beforeend', pinModalHtml);
            const pinModal = new bootstrap.Modal(document.getElementById('pinModal'));
            pinModal.show();
            document.getElementById('confirmPinBtn').onclick = function() {
                if (!isPinned) {
                    // Check pin limit before pinning
                    fetch('api/admin_get_announcements.php')
                        .then(res => res.json())
                        .then(data => {
                            const pinCount = (data.announcements || []).filter(a => a.is_pinned == 1 || a.is_pinned === '1').length;
                            if (pinCount >= 3) {
                                pinModal.hide();
                                document.getElementById('pinModal').remove();
                                const maxPinsModal = new bootstrap.Modal(document.getElementById('maxPinsModal'));
                                maxPinsModal.show();
                                return;
                            } else {
                                doPinUnpin(pinAnnouncementId, 1);
                                pinModal.hide();
                                document.getElementById('pinModal').remove();
                            }
                        });
                } else {
                    doPinUnpin(pinAnnouncementId, 0);
                    pinModal.hide();
                    document.getElementById('pinModal').remove();
                }
            };
            document.getElementById('pinModal').addEventListener('hidden.bs.modal', function() {
                document.getElementById('pinModal').remove();
            });
        } else if (e.target.closest('.edit-announcement')) {
            const id = e.target.closest('.edit-announcement').dataset.id;
            const a = announcements.find(x => x.id == id);
            if (a) {
                document.getElementById('announcementId').value = a.id;
                document.getElementById('announcementTitle').value = a.title;
                document.getElementById('announcementContent').value = a.content;
                document.getElementById('announcementCategory').value = a.category || 'system';
                document.getElementById('announcementStatus').value = a.status || 'published';
                modal.show();
            }
        } else if (e.target.closest('.delete-announcement')) {
            const id = e.target.closest('.delete-announcement').dataset.id;
            if (confirm('Delete this announcement?')) {
                fetch('api/admin_delete_announcement.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id })
                }).then(() => fetchAnnouncements(currentPage));
            }
        }
    });
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('announcementId').value;
        const title = document.getElementById('announcementTitle').value.trim();
        const content = document.getElementById('announcementContent').value.trim();
        const category = document.getElementById('announcementCategory').value;
        const status = document.getElementById('announcementStatus').value;
        fetch(id ? 'api/admin_edit_announcement.php' : 'api/admin_post_announcement.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id, title, content, category, status })
        })
        .then(() => {
            modal.hide();
            fetchAnnouncements(currentPage);
        });
    });
    // Filters/Search
    [searchInput, statusFilter, categoryFilter].forEach(el => {
        el.addEventListener('input', () => fetchAnnouncements(1));
    });
    // Back to Top
    window.addEventListener('scroll', function() {
        if (window.scrollY > 400) {
            backToTopBtn.style.display = 'block';
        } else {
            backToTopBtn.style.display = 'none';
        }
    });
    backToTopBtn.addEventListener('click', function() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
    // Initial load
    fetchAnnouncements(1);

    // Update Sidebar: fetch recent, stats, and featured from API response
    function updateSidebar(data) {
        // Recent Announcements
        const recentList = document.getElementById('recentAnnouncementsList');
        if (data.recent && data.recent.length) {
            recentList.innerHTML = data.recent.map(a => `<li class="list-group-item">${a.title}</li>`).join('');
        } else {
            recentList.innerHTML = '<li class="list-group-item text-muted">No recent announcements.</li>';
        }
        // Stats
        const statsDiv = document.getElementById('announcementStats');
        statsDiv.innerHTML = `<div>Total: <b>${data.stats?.total || 0}</b></div><div>Drafts: <b>${data.stats?.drafts || 0}</b></div>`;
        // Featured
        const featuredDiv = document.getElementById('featuredAnnouncement');
        if (data.featured) {
            featuredDiv.innerHTML = `<div class="announcement-card">${data.featured.title}</div>`;
        } else {
            featuredDiv.innerHTML = '<div class="text-muted">No featured announcement.</div>';
        }
    }

    // Add/Edit Announcement
    addBtn.addEventListener('click', () => {
        form.reset();
        document.getElementById('announcementId').value = '';
        modal.show();
    });

    function doPinUnpin(id, isPinned) {
        fetch('api/admin_edit_announcement.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id, is_pinned: isPinned })
        }).then(() => fetchAnnouncements(currentPage));
    }
});