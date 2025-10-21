
/**
 * File: admin_dashboard.js
 * Purpose: Handles admin dashboard logic for CodeGaming, including stats, announcements, activity, charts, and notifications.
 * Features:
 *   - Fetches and displays dashboard stats, recent activity, and announcements.
 *   - Supports add/edit/delete/pin/unpin for announcements with modals and confirmation.
 *   - Renders charts for user activity and content distribution using ApexCharts.
 *   - Displays login notifications and auto-refreshes dashboard data.
 *   - Provides utility functions for notifications and announcement management.
 * Usage:
 *   - Included on the admin dashboard page for site management and moderation.
 *   - Requires HTML elements for stats, announcements, activity table, modals, and charts.
 *   - Relies on API endpoints: api/admin_get_stats.php, api/admin_get_activity.php, api/admin_get_announcements.php, api/admin_post_announcement.php, api/admin_edit_announcement.php, api/admin_delete_announcement.php, api/admin_get_chart_data.php.
 * Included Files/Dependencies:
 *   - Bootstrap (modals, alerts)
 *   - ApexCharts.js (charts)
 * Author: CodeGaming Team
 * Last Updated: July 22, 2025
 */

// Admin Dashboard JavaScript

document.addEventListener('DOMContentLoaded', function() {
  // --- Handle Login Notification ---
  const loginNotification = document.getElementById('loginNotification');
  if (loginNotification) {
      // Clean the URL to prevent notification on reload
      const cleanUrl = window.location.href.split('?')[0];
      window.history.replaceState({}, document.title, cleanUrl);

      const closeBtn = document.getElementById('closeLoginNotification');

      const dismissNotification = () => {
          loginNotification.classList.add('dismissed');
          // Optional: remove from DOM after transition
          setTimeout(() => {
              if (loginNotification) loginNotification.remove();
          }, 500); 
      };

      // Auto-dismiss after 6 seconds
      const autoDismissTimer = setTimeout(dismissNotification, 6000);

      // Manual dismiss on click
      if(closeBtn) {
        closeBtn.addEventListener('click', () => {
            clearTimeout(autoDismissTimer);
            dismissNotification();
        });
      }
  }

  // --- Elements ---
  const announcementsList = document.getElementById('dashboardAnnouncementsList');
  const activityTable = document.querySelector('.admin-card table tbody');
  const announcementModal = document.getElementById('newAnnouncementModal');
  const announcementForm = document.getElementById('announcementForm');
  const announcementIdInput = document.getElementById('announcementId');
  const announcementTitleInput = document.getElementById('announcementTitle');
  const announcementContentInput = document.getElementById('announcementContent');
  const publishAnnouncementBtn = document.getElementById('publishAnnouncementBtn');
  // --- Fetch Dashboard Stats ---
  function loadStats() {
    fetch('api/admin_get_stats.php')
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          const totalUsers = document.getElementById('totalUsersStat');
          const activeUsers = document.getElementById('activeUsersStat');
          const totalContent = document.getElementById('totalContentStat');
          const systemStatus = document.getElementById('systemStatusStat');
          const totalAnnouncements = document.getElementById('totalAnnouncementsStat');
          const publishedAnnouncements = document.getElementById('publishedAnnouncementsStat');
          const draftAnnouncements = document.getElementById('draftAnnouncementsStat');
          
          if (totalUsers) totalUsers.textContent = data.total_users;
          if (activeUsers) activeUsers.textContent = data.active_users;
          if (totalContent) totalContent.textContent = data.total_content;
          if (systemStatus) systemStatus.textContent = data.system_status;
          if (totalAnnouncements) totalAnnouncements.textContent = data.total_announcements;
          if (publishedAnnouncements) publishedAnnouncements.textContent = data.published_announcements;
          if (draftAnnouncements) draftAnnouncements.textContent = data.draft_announcements;
          console.log('Stats API response:', data);
        } else {
          console.error('Failed to load stats:', data.error);
        }
      })
      .catch(error => {
        console.error('Error fetching stats:', error);
      });
  }
  // Initial load
  loadStats();

  // --- Fetch Chart Data ---
  fetch('api/admin_get_chart_data.php')
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        initializeCharts(data);
      } else {
        console.error('Failed to fetch chart data:', data.error);
      }
    })
    .catch(error => {
      console.error('Error fetching chart data:', error);
    });

  // --- Fetch Announcements (Pinned first, limit 5) ---
  function loadAnnouncements() {
    fetch('api/admin_get_announcements.php')
      .then(res => res.json())
      .then(data => {
        if (data.success && Array.isArray(data.announcements)) {
          announcementsList.innerHTML = '';
          // Sort: pinned first, then by date desc
          const sorted = [...data.announcements].sort((a, b) => {
            if (b.is_pinned - a.is_pinned !== 0) return b.is_pinned - a.is_pinned;
            return new Date(b.date) - new Date(a.date);
          }).slice(0, 5);
          sorted.forEach(a => {
            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-center';
            if (a.is_pinned) li.classList.add('dashboard-announcement-pinned');
            li.innerHTML = `
              <div style="position:relative;">
                ${a.is_pinned ? '<i class="fas fa-thumbtack"></i>' : ''}
                <strong>${a.title}</strong><br>
                <span class="text-muted small">${a.content}</span><br>
                <span class="badge bg-info">${a.date}</span>
                <small class="text-muted ms-2">by ${a.created_by}</small>
              </div>
              <div class="d-flex align-items-center">
                <button class="pin-btn${a.is_pinned ? ' pinned' : ''}" title="${a.is_pinned ? 'Unpin' : 'Pin'}" data-id="${a.id}" data-pinned="${a.is_pinned}">
                  <i class="fas fa-thumbtack"></i>
                </button>
                <button class="btn btn-sm btn-outline-primary me-1 edit-announcement" data-id="${a.id}" data-title="${encodeURIComponent(a.title)}" data-content="${encodeURIComponent(a.content)}">
                  <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger delete-announcement" data-id="${a.id}">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            `;
            announcementsList.appendChild(li);
          });
        } else {
          announcementsList.innerHTML = '<li class="list-group-item text-muted">No announcements found.</li>';
        }
      })
      .catch(error => {
        console.error('Error fetching announcements:', error);
        announcementsList.innerHTML = '<li class="list-group-item text-danger">Failed to load announcements.</li>';
      });
  }
  loadAnnouncements();

  // --- Fetch Recent Activity ---
  function loadActivity() {
    fetch('api/admin_get_activity.php')
      .then(res => res.json())
      .then(data => {
        if (data.success && Array.isArray(data.activity)) {
          activityTable.innerHTML = '';
          data.activity.forEach(act => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
              <td><i class="fas fa-user-circle text-primary"></i> ${act.user}</td>
              <td>${act.action}</td>
              <td>${act.time}</td>
              <td><span class="badge bg-${act.status_color}">${act.status}</span></td>
            `;
            activityTable.appendChild(tr);
          });
        } else {
          console.error('Failed to fetch activity:', data.error);
        }
      })
      .catch(error => {
        console.error('Error fetching activity:', error);
      });
  }
  loadActivity();

  // --- Fetch System Notifications ---
  function loadNotifications() {
    fetch('api/admin_get_notifications.php?limit=5')
      .then(res => res.json())
      .then(data => {
        const notificationsList = document.getElementById('systemNotificationsList');
        if (data.success && Array.isArray(data.notifications)) {
          notificationsList.innerHTML = '';
          if (data.notifications.length === 0) {
            notificationsList.innerHTML = '<li class="list-group-item text-muted">No notifications</li>';
          } else {
            data.notifications.forEach(notif => {
              const li = document.createElement('li');
              li.className = 'list-group-item';
              
              let iconClass = 'fa-info-circle text-info';
              if (notif.type === 'error') iconClass = 'fa-exclamation-circle text-danger';
              else if (notif.type === 'warning') iconClass = 'fa-exclamation-triangle text-warning';
              else if (notif.type === 'success') iconClass = 'fa-check-circle text-success';
              
              li.innerHTML = `<i class="fas ${iconClass} me-2"></i>${notif.message}`;
              notificationsList.appendChild(li);
            });
          }
        } else {
          notificationsList.innerHTML = '<li class="list-group-item text-danger">Failed to load notifications</li>';
        }
      })
      .catch(error => {
        console.error('Error fetching notifications:', error);
        const notificationsList = document.getElementById('systemNotificationsList');
        if (notificationsList) {
          notificationsList.innerHTML = '<li class="list-group-item text-danger">Error loading notifications</li>';
        }
      });
  }
  loadNotifications();

  // Refresh notifications button
  const refreshNotificationsBtn = document.getElementById('refreshNotificationsBtn');
  if (refreshNotificationsBtn) {
    refreshNotificationsBtn.addEventListener('click', loadNotifications);
  }

  // --- Initialize Charts ---
  function initializeCharts(chartData) {
    // User Activity Chart
    if (window.ApexCharts && document.querySelector('#userActivityChart')) {
      const userActivityOptions = {
        chart: { 
          type: 'bar', 
          height: 180, 
          toolbar: { show: false },
          background: 'transparent'
        },
        series: [{ 
          name: 'Logins', 
          data: chartData.user_activity.data || [30, 40, 35, 50, 49, 60, 70] 
        }],
        xaxis: { 
          categories: chartData.user_activity.labels || ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
          labels: { style: { colors: '#7f8c8d' } }
        },
        yaxis: { labels: { style: { colors: '#7f8c8d' } } },
        colors: ['#3498db'],
        grid: { borderColor: '#e0e4ea' },
        plotOptions: {
          bar: {
            borderRadius: 4,
            dataLabels: { position: 'top' }
          }
        }
      };
      new ApexCharts(document.querySelector('#userActivityChart'), userActivityOptions).render();
    }

    // Content Distribution Chart
    if (window.ApexCharts && document.querySelector('#contentPieChart')) {
      const labels = chartData.content_distribution.map(item => item.label) || ['Tutorials', 'Quizzes', 'Challenges'];
      const values = chartData.content_distribution.map(item => item.value) || [44, 33, 23];
      
      const contentPieOptions = {
        chart: { 
          type: 'donut', 
          height: 180,
          background: 'transparent'
        },
        series: values,
        labels: labels,
        colors: ['#3498db', '#f1c40f', '#2ecc71'],
        legend: { 
          show: true, 
          position: 'bottom',
          labels: { colors: '#7f8c8d' }
        },
        plotOptions: {
          pie: {
            donut: {
              size: '60%'
            }
          }
        }
      };
      new ApexCharts(document.querySelector('#contentPieChart'), contentPieOptions).render();
    }
  }

  // --- Add/Edit Announcement ---
  document.querySelectorAll('[data-bs-target="#newAnnouncementModal"]').forEach(btn => {
    btn.addEventListener('click', function() {
      isEditingAnnouncement = false;
      announcementIdInput.value = '';
      announcementTitleInput.value = '';
      announcementContentInput.value = '';
      document.getElementById('newAnnouncementModalLabel').textContent = 'New Announcement';
      publishAnnouncementBtn.textContent = 'Publish';
    });
  });

  // Edit button handler (open modal, pre-fill)
  announcementsList.addEventListener('click', function(e) {
    if (e.target.closest('.edit-announcement')) {
      const btn = e.target.closest('.edit-announcement');
      const id = btn.dataset.id;
      const title = decodeURIComponent(btn.dataset.title || '');
      const content = decodeURIComponent(btn.dataset.content || '');
      isEditingAnnouncement = true;
      announcementIdInput.value = id;
      announcementTitleInput.value = title;
      announcementContentInput.value = content;
      document.getElementById('newAnnouncementModalLabel').textContent = 'Edit Announcement';
      publishAnnouncementBtn.textContent = 'Update';
      const modal = bootstrap.Modal.getOrCreateInstance(announcementModal);
      modal.show();
    }
  });

  // Publish/Update Announcement
  publishAnnouncementBtn.addEventListener('click', function() {
    const id = announcementIdInput.value;
    const title = announcementTitleInput.value.trim();
    const content = announcementContentInput.value.trim();
    if (!title || !content) {
      showNotification('Title and content are required', 'error');
      return;
    }
    const url = isEditingAnnouncement ? 'api/admin_edit_announcement.php' : 'api/admin_post_announcement.php';
    const payload = isEditingAnnouncement ? { id, title, content } : { title, content };
    fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        afterAnnouncementChange();
        bootstrap.Modal.getOrCreateInstance(announcementModal).hide();
        showNotification(isEditingAnnouncement ? 'Announcement updated!' : 'Announcement posted!', 'success');
      } else {
        showNotification(data.error || 'Failed to save announcement.', 'error');
      }
    })
    .catch(error => {
      console.error('Error saving announcement:', error);
      showNotification('Failed to save announcement.', 'error');
    });
  });

  // --- Pin/Unpin Announcement ---
  let pendingPinUnpin = { id: null, isPinned: null };
  announcementsList.addEventListener('click', function(e) {
    if (e.target.closest('.pin-btn')) {
      const btn = e.target.closest('.pin-btn');
      const id = btn.dataset.id;
      const isPinned = btn.dataset.pinned == '1' || btn.dataset.pinned === 'true';
      // Show confirmation modal
      pendingPinUnpin.id = id;
      pendingPinUnpin.isPinned = isPinned;
      const modalTitle = document.getElementById('pinUnpinModalTitle');
      const modalBody = document.getElementById('pinUnpinModalBody');
      if (!isPinned) {
        modalTitle.textContent = 'Pin Announcement';
        modalBody.textContent = 'Are you sure you want to pin this announcement as featured?';
      } else {
        modalTitle.textContent = 'Unpin Announcement';
        modalBody.textContent = 'Are you sure you want to unpin this post?';
      }
      const pinUnpinModal = new bootstrap.Modal(document.getElementById('pinUnpinModal'));
      pinUnpinModal.show();
    }
  });

  document.getElementById('confirmPinUnpinBtn').addEventListener('click', function() {
    const id = pendingPinUnpin.id;
    const isPinned = pendingPinUnpin.isPinned;
    const pinUnpinModal = bootstrap.Modal.getInstance(document.getElementById('pinUnpinModal'));
    pinUnpinModal.hide();
    // Only check pin limit if pinning (not unpinning)
    if (!isPinned) {
      fetch('api/admin_get_announcements.php')
        .then(res => res.json())
        .then(data => {
          if (data.success && Array.isArray(data.announcements)) {
            const pinCount = data.announcements.filter(a => a.is_pinned).length;
            if (pinCount >= 3) {
              // Show modal and do not proceed
              const maxPinsModal = new bootstrap.Modal(document.getElementById('maxPinsModal'));
              maxPinsModal.show();
              return;
            } else {
              // Proceed to pin
              doPinUnpin(id, 1);
            }
          } else {
            showNotification('Could not check pin limit.', 'error');
          }
        })
        .catch(() => {
          showNotification('Could not check pin limit.', 'error');
        });
    } else {
      // Unpin directly
      doPinUnpin(id, 0);
    }
  });

  function doPinUnpin(id, isPinned) {
    fetch('api/admin_edit_announcement.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id, is_pinned: isPinned })
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        afterAnnouncementChange();
        showNotification(isPinned ? 'Announcement pinned!' : 'Announcement unpinned.', 'success');
      } else {
        showNotification(data.error || 'Failed to update pin.', 'error');
      }
    })
    .catch(error => {
      console.error('Error pinning/unpinning:', error);
      showNotification('Failed to update pin.', 'error');
    });
  }

  // --- Delete Announcement ---
  announcementsList.addEventListener('click', function(e) {
    if (e.target.closest('.delete-announcement')) {
      const id = e.target.closest('.delete-announcement').dataset.id;
      if (!confirm('Are you sure you want to delete this announcement?')) return;
      fetch('api/admin_delete_announcement.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          afterAnnouncementChange();
          showNotification('Announcement deleted successfully!', 'success');
        } else {
          showNotification(data.error || 'Failed to delete announcement.', 'error');
        }
      })
      .catch(error => {
        console.error('Error deleting announcement:', error);
        showNotification('Failed to delete announcement.', 'error');
      });
    }
  });

  // --- Utility Functions ---
  function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 100px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
      ${message}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
      if (notification.parentNode) {
        notification.remove();
      }
    }, 5000);
  }

  // --- Auto-refresh data every 30 seconds ---
  setInterval(() => {
    loadAnnouncements();
    loadActivity();
    loadNotifications();
  }, 30000);

  // After any announcement change, also call loadStats()
  function afterAnnouncementChange() {
    loadAnnouncements();
    loadStats();
  }
});
