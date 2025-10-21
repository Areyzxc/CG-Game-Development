/**
 * File: admin_notifications.js
 * Purpose: Handles notification bell in admin header
 * Author: CodeGaming Team
 * Last Updated: October 21, 2025
 */

// Load notifications for header bell
function loadHeaderNotifications() {
    fetch('api/admin_get_notifications.php?limit=5')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                updateNotificationBell(data.unread_count);
                displayNotifications(data.notifications);
            }
        })
        .catch(error => {
            console.error('Error loading notifications:', error);
        });
}

// Update notification badge
function updateNotificationBell(count) {
    const badge = document.getElementById('notificationBadge');
    if (badge) {
        if (count > 0) {
            badge.textContent = count > 9 ? '9+' : count;
            badge.style.display = 'block';
        } else {
            badge.style.display = 'none';
        }
    }
}

// Display notifications in dropdown
function displayNotifications(notifications) {
    const notificationList = document.getElementById('notificationList');
    if (!notificationList) return;
    
    notificationList.innerHTML = '';
    
    if (notifications.length === 0) {
        notificationList.innerHTML = `
            <li class="dropdown-item text-muted text-center py-3">
                <i class="fas fa-check-circle me-2"></i>No new notifications
            </li>
        `;
        return;
    }
    
    notifications.forEach(notif => {
        const li = document.createElement('li');
        li.className = `notification-item ${notif.is_read ? '' : 'unread'}`;
        li.dataset.notifId = notif.id;
        
        let iconClass = 'fa-info-circle';
        if (notif.type === 'error') iconClass = 'fa-exclamation-circle';
        else if (notif.type === 'warning') iconClass = 'fa-exclamation-triangle';
        else if (notif.type === 'success') iconClass = 'fa-check-circle';
        
        li.innerHTML = `
            <div class="notification-icon ${notif.type}">
                <i class="fas ${iconClass}"></i>
            </div>
            <div class="notification-content">
                <div class="notification-title">${notif.title}</div>
                <div class="notification-message">${notif.message}</div>
                <div class="notification-time">${notif.time_ago}</div>
            </div>
        `;
        
        // Mark as read on click
        li.addEventListener('click', function() {
            markNotificationRead(notif.id);
            this.classList.remove('unread');
        });
        
        notificationList.appendChild(li);
    });
}

// Mark notification as read
function markNotificationRead(notificationId) {
    fetch('api/admin_mark_notification_read.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: notificationId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            loadHeaderNotifications(); // Refresh to update badge count
        }
    })
    .catch(error => {
        console.error('Error marking notification as read:', error);
    });
}

// Mark all as read
document.addEventListener('DOMContentLoaded', function() {
    const markAllReadBtn = document.getElementById('markAllReadBtn');
    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            fetch('api/admin_mark_all_notifications_read.php', {
                method: 'POST'
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    loadHeaderNotifications();
                }
            })
            .catch(error => {
                console.error('Error marking all as read:', error);
            });
        });
    }
    
    // Load notifications on page load
    loadHeaderNotifications();
    
    // Auto-refresh every 60 seconds
    setInterval(loadHeaderNotifications, 60000);
});
