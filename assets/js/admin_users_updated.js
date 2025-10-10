/**
 * File: admin_users.js
 * Purpose: Handles admin user management logic for CodeGaming, including user/admin listing, bulk actions, modals, search, and actions log.
 * Features:
 *   - Fetches and displays users/admins in tables with status badges and avatars.
 *   - Supports bulk ban/unban/badge assignment actions with selection controls.
 *   - Displays user/admin details in modal overlays.
 *   - Implements quick search for users/admins with live results.
 *   - Loads and updates admin actions log with periodic refresh.
 * Usage:
 *   - Included on admin user management pages for user/admin control and moderation.
 *   - Requires HTML elements for tables, modals, bulk actions panel, search, and actions log.
 *   - Relies on API endpoints: api/admin_get_users.php, api/admin_get_user_details.php, api/update_profile.php, api/admin_ban_user.php, api/admin_unban_user.php, api/admin_get_actions_log.php.
 * Included Files/Dependencies:
 *   - FontAwesome (icons)
 *   - Bootstrap (optional for modal styling)
 * Author: CodeGaming Team
 * Last Updated: October 3, 2025
 */

// Create a self-executing function to handle modal functionality
const ModalManager = (function() {
    let modalOverlay = null;
    
    function init() {
        modalOverlay = document.getElementById('retroModalOverlay');
        if (!modalOverlay) {
            console.error('Modal overlay element not found');
            return false;
        }
        
        // Initialize the time display
        updateModalTime();
        setInterval(updateModalTime, 60000);
        
        return true;
    }
    
    // Function to update the modal time display
    function updateModalTime() {
        if (!modalOverlay) return;
        const timeDisplay = modalOverlay.querySelector('#modalTime');
        if (timeDisplay) {
            const now = new Date();
            timeDisplay.textContent = `Time: ${now.toLocaleTimeString('en-US', {hour: '2-digit', minute:'2-digit'})}`;
        }
    }
    
    function show(userId, userType) {
        // Initialize if not already done
        if (!modalOverlay && !init()) {
            console.error('Modal system not initialized');
            if (typeof showToast === 'function') {
                showToast('Error: Modal system not initialized', 'error');
            }
            return;
        }
        
        fetch(`api/admin_get_user_details.php?id=${userId}&type=${userType}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    populateModal(data.user);
                    modalOverlay.dataset.userid = userId;
                    modalOverlay.dataset.usertype = userType;
                    modalOverlay.style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                } else {
                    throw new Error(data.error || 'Failed to load user data');
                }
            })
            .catch(error => {
                console.error('Error loading user details:', error);
                showToast('Error: ' + (error.message || 'Failed to load user details'), 'error');
            });
    }
    
    function hide() {
        if (modalOverlay) {
            modalOverlay.style.display = 'none';
            document.body.style.overflow = '';
        }
    }
    
    return {
        init: init,
        show: show,
        hide: hide
    };
})();

// Global function to show modal
function showModal(userId, userType) {
    ModalManager.show(userId, userType);
}

// Initialize modal overlay when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    // Initialize modal manager
    ModalManager.init();
    // --- Helper Functions ---
    function updateUserAvatar(userId, userType, newPic) {
        // Update avatar in the table
        const tableId = userType === 'admin' ? 'adminsTableBody' : 'usersTableBody';
        const tableBody = document.getElementById(tableId);
        if (!tableBody) return;
        
        const userIdStr = String(userId);
        const basePath = window.location.pathname.split('/').slice(0, -1).join('/');
        const timestamp = new Date().getTime();
        const newPicWithTimestamp = newPic ? `${basePath}/uploads/avatars/${newPic}?t=${timestamp}` : 'assets/images/PTC.png';

        // Find and update all instances of this user's avatar in the table
        const rows = tableBody.querySelectorAll('tr');
        rows.forEach(row => {
            const idCell = row.querySelector('td:nth-child(3)');
            if (idCell && idCell.textContent.trim() === userIdStr) {
                const avatarImg = row.querySelector('td:nth-child(2) img');
                if (avatarImg) {
                    avatarImg.src = newPicWithTimestamp;
                }
            }
        });

        // Update any other instances in the UI
        document.querySelectorAll(`img[data-user-id="${userId}"][data-user-type="${userType}"]`).forEach(img => {
            img.src = newPicWithTimestamp;
        });
    }

    // --- Toast Notification Helper ---
    function showToast(message, type = 'success') {
        let toastContainer = document.getElementById('cgToastContainer');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'cgToastContainer';
            toastContainer.style.position = 'fixed';
            toastContainer.style.top = '24px';
            toastContainer.style.right = '24px';
            toastContainer.style.zIndex = '9999';
            toastContainer.style.display = 'flex';
            toastContainer.style.flexDirection = 'column';
            toastContainer.style.gap = '8px';
            document.body.appendChild(toastContainer);
        }
        const toast = document.createElement('div');
        toast.className = 'cg-toast cg-toast-' + type;
        toast.style.background = type === 'success' ? '#28a745' : '#dc3545';
        toast.style.color = '#fff';
        toast.style.padding = '12px 24px';
        toast.style.borderRadius = '6px';
        toast.style.boxShadow = '0 2px 8px rgba(0,0,0,0.15)';
        toast.style.fontWeight = 'bold';
        toast.style.fontSize = '1rem';
        toast.style.opacity = '0.95';
        toast.style.transition = 'opacity 0.3s';
        toast.textContent = message;
        toastContainer.appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 400);
        }, 2200);
    }

    // --- DOM Elements ---
    const usersTableBody = document.getElementById('usersTableBody');
    const adminsTableBody = document.getElementById('adminsTableBody');
    const modalOverlay = document.getElementById('retroModalOverlay');
    const modalCloseButton = document.getElementById('modalCloseButton');
    const modalBackButton = document.getElementById('modalBackButton');
    const modalEditButton = document.getElementById('modalEditButton');
    const modalSaveButton = document.getElementById('modalSaveButton');
    const modalCancelButton = document.getElementById('modalCancelButton');
    const modalUsernameInput = document.getElementById('modalUsernameInput');
    const modalProfilePicInput = document.getElementById('modalProfilePicInput');
    const modalProfilePic = document.getElementById('modalProfilePic');

    // --- Event Listeners ---
    if (modalSaveButton) {
        modalSaveButton.addEventListener('click', function() {
            // Prepare form data
            const userId = modalOverlay.dataset.userid;
            const userType = modalOverlay.dataset.usertype;
            const newUsername = modalUsernameInput.value.trim();
            const file = modalProfilePicInput.files[0];
            
            if (!newUsername) {
                alert('Username cannot be empty.');
                return;
            }

            const formData = new FormData();
            formData.append('id', userId);
            formData.append('type', userType);
            formData.append('username', newUsername);
            if (file) formData.append('profile_picture', file);

            // Show loading state
            const originalButtonText = this.innerHTML;
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';

            // AJAX call to backend for saving user edits
            fetch('api/update_profile.php', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(async response => {
                const text = await response.text();
                let data;
                try {
                    data = text ? JSON.parse(text) : {};
                } catch (e) {
                    console.error('Failed to parse JSON response:', text);
                    throw new Error('Invalid server response');
                }
                
                if (!response.ok) {
                    const error = data.error || 'Failed to update profile';
                    throw new Error(error);
                }
                return data;
            })
            .then(data => {
                if (data.success) {
                    // Update the UI using our helper function
                    updateUserAvatar(userId, userType, data.data.profile_picture);
                    
                    // Update username in the table
                    const tableId = userType === 'admin' ? 'adminsTableBody' : 'usersTableBody';
                    const tableBody = document.getElementById(tableId);
                    const userIdStr = String(userId);
                    
                    const rows = tableBody.querySelectorAll('tr');
                    rows.forEach(row => {
                        const idCell = row.querySelector('td:nth-child(3)');
                        if (idCell && idCell.textContent.trim() === userIdStr) {
                            const usernameCell = row.querySelector('td:nth-child(4)');
                            if (usernameCell) {
                                usernameCell.textContent = data.data.username;
                            }
                        }
                    });

                    showToast(data.message || 'Profile updated successfully!', 'success');
                    hideModal();
                    
                    // Update the current user's header if they updated their own profile
                    if (typeof updateUserHeader === 'function' && data.data) {
                        updateUserHeader(data.data);
                    }
                } else {
                    throw new Error(data.error || 'Failed to update profile');
                }
            })
            .catch(error => {
                console.error('Error updating profile:', error);
                showToast('Error: ' + (error.message || 'Failed to update profile. Please try again.'), 'error');
            })
            .finally(() => {
                // Reset button state
                modalSaveButton.disabled = false;
                modalSaveButton.innerHTML = originalButtonText;
            });
        });
    }

    // --- Modal Functions ---
    // showModal function has been moved to global scope

    function hideModal() {
        modalOverlay.style.display = 'none';
        document.body.style.overflow = '';
        // Reset form
        if (modalSaveButton && modalCancelButton) {
            modalSaveButton.classList.add('d-none');
            modalCancelButton.classList.add('d-none');
            modalEditButton.classList.remove('d-none');
        }
    }

    function populateModal(user) {
        if (!user) return;

        // Set modal title and basic info
        document.getElementById('modalTitle').textContent = `@${user.username} // user_profile.dat`;
        document.getElementById('modalUsername').textContent = user.username;
        document.getElementById('modalUsernameInput').value = user.username;
        document.getElementById('modalEmail').textContent = user.email || 'N/A';
        
        // Set avatar with cache busting
        const timestamp = new Date().getTime();
        const avatarPath = user.avatar 
            ? `uploads/avatars/${user.avatar}?t=${timestamp}` 
            : 'assets/images/PTC.png';
        modalProfilePic.src = avatarPath;
        
        // Set status
        const statusElement = document.getElementById('modalStatus');
        if (statusElement) {
            statusElement.textContent = user.is_banned ? 'Banned' : 'Active';
            statusElement.className = user.is_banned ? 'status-banned' : 'status-active';
        }

        // Set last seen
        const lastSeenElement = document.getElementById('modalLastSeen');
        if (lastSeenElement) {
            lastSeenElement.textContent = user.last_seen || 'Never';
        }
    }

    // --- Event Listeners ---
    if (modalCloseButton) {
        modalCloseButton.addEventListener('click', hideModal);
    }

    if (modalBackButton) {
        modalBackButton.addEventListener('click', hideModal);
    }

    if (modalEditButton) {
        modalEditButton.addEventListener('click', function() {
            modalEditButton.classList.add('d-none');
            modalSaveButton.classList.remove('d-none');
            modalCancelButton.classList.remove('d-none');
            modalUsernameInput.classList.remove('d-none');
            modalUsernameInput.focus();
            modalProfilePicInput.classList.remove('d-none');
        });
    }

    if (modalCancelButton) {
        modalCancelButton.addEventListener('click', function() {
            // Reset form
            const userId = modalOverlay.dataset.userid;
            const userType = modalOverlay.dataset.usertype;
            if (userId && userType) {
                fetch(`api/admin_get_user_details.php?id=${userId}&type=${userType}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            populateModal(data.user);
                        }
                    });
            }
            
            modalSaveButton.classList.add('d-none');
            modalCancelButton.classList.add('d-none');
            modalEditButton.classList.remove('d-none');
            modalUsernameInput.classList.add('d-none');
            modalProfilePicInput.classList.add('d-none');
        });
    }

    // Profile picture preview
    if (modalProfilePicInput) {
        modalProfilePicInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(e) {
                modalProfilePic.src = e.target.result;
            };
            reader.readAsDataURL(file);
        });
    }

    // Close modal when clicking outside content
    modalOverlay.addEventListener('click', function(e) {
        if (e.target === modalOverlay) {
            hideModal();
        }
    });

    // --- Bulk Actions ---
    function updateBulkActionsPanel() {
        const userCheckboxes = document.querySelectorAll('.user-select-checkbox:checked');
        const adminCheckboxes = document.querySelectorAll('.admin-select-checkbox:checked');
        const anySelected = userCheckboxes.length > 0 || adminCheckboxes.length > 0;
        const panel = document.getElementById('adminBulkActionsPanel');
        if (panel) {
            panel.style.display = anySelected ? 'flex' : 'none';
            const bulkBanBtn = document.getElementById('bulkBanBtn');
            const bulkUnbanBtn = document.getElementById('bulkUnbanBtn');
            const bulkBadgeBtn = document.getElementById('bulkBadgeBtn');
            
            if (bulkBanBtn) bulkBanBtn.disabled = !anySelected;
            if (bulkUnbanBtn) bulkUnbanBtn.disabled = !anySelected;
            if (bulkBadgeBtn) bulkBadgeBtn.disabled = !anySelected;
        }
    }

    // Select All checkboxes
    const selectAllUsers = document.getElementById('selectAllUsers');
    const selectAllAdmins = document.getElementById('selectAllAdmins');

    if (selectAllUsers) {
        selectAllUsers.addEventListener('change', function(e) {
            document.querySelectorAll('.user-select-checkbox').forEach(cb => cb.checked = e.target.checked);
            updateBulkActionsPanel();
        });
    }

    if (selectAllAdmins) {
        selectAllAdmins.addEventListener('change', function(e) {
            document.querySelectorAll('.admin-select-checkbox').forEach(cb => cb.checked = e.target.checked);
            updateBulkActionsPanel();
        });
    }

    // Individual checkbox changes
    document.addEventListener('change', function(e) {
        if (e.target.matches('.user-select-checkbox, .admin-select-checkbox')) {
            updateBulkActionsPanel();
        }
    });

    // Bulk action buttons
    const bulkActionButtons = ['ban', 'unban', 'badge'];
    bulkActionButtons.forEach(action => {
        const btn = document.getElementById(`bulk${action.charAt(0).toUpperCase() + action.slice(1)}Btn`);
        if (btn) {
            btn.addEventListener('click', () => handleBulkAction(action));
        }
    });

    function handleBulkAction(action) {
        const selectedUsers = [];
        const selectedAdmins = [];
        
        document.querySelectorAll('.user-select-checkbox:checked').forEach(cb => {
            selectedUsers.push(cb.value);
        });
        
        document.querySelectorAll('.admin-select-checkbox:checked').forEach(cb => {
            selectedAdmins.push(cb.value);
        });

        if (selectedUsers.length === 0 && selectedAdmins.length === 0) {
            showToast('No users selected', 'error');
            return;
        }

        // Show confirmation dialog
        const confirmMessage = `Are you sure you want to ${action} ${selectedUsers.length + selectedAdmins.length} selected user(s)?`;
        if (!confirm(confirmMessage)) return;

        // Prepare data
        const data = {
            users: selectedUsers,
            admins: selectedAdmins,
            action: action
        };

        // Show loading state
        const originalButtonText = document.getElementById(`bulk${action.charAt(0).toUpperCase() + action.slice(1)}Btn`).innerHTML;
        document.getElementById(`bulk${action.charAt(0).toUpperCase() + action.slice(1)}Btn`).innerHTML = 
            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';

        // Send request
        fetch('api/admin_bulk_actions.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message || 'Action completed successfully', 'success');
                // Reload the page to reflect changes
                window.location.reload();
            } else {
                throw new Error(data.error || 'Failed to complete action');
            }
        })
        .catch(error => {
            console.error('Error performing bulk action:', error);
            showToast('Error: ' + (error.message || 'Failed to complete action'), 'error');
        })
        .finally(() => {
            // Reset button state
            const btn = document.getElementById(`bulk${action.charAt(0).toUpperCase() + action.slice(1)}Btn`);
            if (btn) {
                btn.innerHTML = originalButtonText;
            }
        });
    }

    // --- Quick Search ---
    const searchInput = document.getElementById('adminQuickSearchInput');
    const searchForm = document.getElementById('adminQuickSearchForm');
    const searchResults = document.getElementById('adminQuickSearchResults');

    if (searchForm && searchInput && searchResults) {
        let searchTimeout;
        
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(performSearch, 300);
        });

        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            clearTimeout(searchTimeout);
            performSearch();
        });

        function performSearch() {
            const query = searchInput.value.trim();
            if (query.length < 2) {
                searchResults.style.display = 'none';
                return;
            }

            fetch(`api/admin_search.php?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.results.length > 0) {
                        const html = data.results.map(result => `
                            <div class="search-result-item" data-id="${result.id}" data-type="${result.type}">
                                <img src="${result.avatar || 'assets/images/PTC.png'}" alt="${result.username}" class="search-result-avatar">
                                <div class="search-result-info">
                                    <div class="search-result-username">${result.username}</div>
                                    <div class="search-result-email">${result.email}</div>
                                    <div class="search-result-type badge ${result.type === 'admin' ? 'bg-warning' : 'bg-primary'}">
                                        ${result.type === 'admin' ? 'Admin' : 'User'}
                                    </div>
                                </div>
                            </div>
                        `).join('');
                        
                        searchResults.innerHTML = html;
                        searchResults.style.display = 'block';

                        // Add click handlers to search results
                        document.querySelectorAll('.search-result-item').forEach(item => {
                            item.addEventListener('click', function() {
                                const userId = this.dataset.id;
                                const userType = this.dataset.type;
                                showModal(userId, userType);
                                searchResults.style.display = 'none';
                                searchInput.value = '';
                            });
                        });
                    } else {
                        searchResults.innerHTML = '<div class="search-no-results">No results found</div>';
                        searchResults.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Search error:', error);
                    searchResults.innerHTML = '<div class="search-error">Error performing search</div>';
                    searchResults.style.display = 'block';
                });
        }

        // Hide search results when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchForm.contains(e.target)) {
                searchResults.style.display = 'none';
            }
        });
    }

    // --- Admin Actions Log ---
    function loadAdminActionsLog() {
        const actionsLog = document.getElementById('adminActionsLog');
        if (!actionsLog) return;

        fetch('api/admin_get_actions_log.php')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.actions.length > 0) {
                    actionsLog.innerHTML = data.actions.map(action => `
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-bold">${action.username}</span>
                                <span class="text-muted ms-2">${action.action}</span>
                            </div>
                            <small class="text-muted" title="${new Date(action.timestamp).toLocaleString()}">
                                ${timeAgo(new Date(action.timestamp))}
                            </small>
                        </li>
                    `).join('');
                } else {
                    actionsLog.innerHTML = '<li class="list-group-item text-muted">No recent actions</li>';
                }
            })
            .catch(error => {
                console.error('Error loading admin actions:', error);
                actionsLog.innerHTML = '<li class="list-group-item text-danger">Error loading actions log</li>';
            });
    }

    // Helper function for relative time
    function timeAgo(date) {
        const seconds = Math.floor((new Date() - date) / 1000);
        let interval = Math.floor(seconds / 31536000);
        
        if (interval >= 1) return interval + ' year' + (interval === 1 ? '' : 's') + ' ago';
        interval = Math.floor(seconds / 2592000);
        if (interval >= 1) return interval + ' month' + (interval === 1 ? '' : 's') + ' ago';
        interval = Math.floor(seconds / 86400);
        if (interval >= 1) return interval + ' day' + (interval === 1 ? '' : 's') + ' ago';
        interval = Math.floor(seconds / 3600);
        if (interval >= 1) return interval + ' hour' + (interval === 1 ? '' : 's') + ' ago';
        interval = Math.floor(seconds / 60);
        if (interval >= 1) return interval + ' minute' + (interval === 1 ? '' : 's') + ' ago';
        return 'just now';
    }

    // --- Initial Load ---
    // Load users and admins
    function loadUsers() {
        // Show loading state
        const usersTableBody = document.getElementById('usersTableBody');
        const adminsTableBody = document.getElementById('adminsTableBody');
        
        if (usersTableBody) {
            usersTableBody.innerHTML = '<tr><td colspan="9" class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>';
        }
        if (adminsTableBody) {
            adminsTableBody.innerHTML = '<tr><td colspan="10" class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>';
        }
    
        // Fetch users and admins in a single request
        fetch('api/admin_get_users.php?type=all')
            .then(res => res.json())
            .then(data => {
                if (!data || !data.success) {
                    throw new Error(data?.error || 'Failed to load user data');
                }
    
                // Handle users
                if (usersTableBody) {
                    const users = data.users || [];
                    usersTableBody.innerHTML = users.length > 0 
                        ? populateTable(usersTableBody, users, 'user')
                        : '<tr><td colspan="9" class="text-center py-4">No users found</td></tr>';
                }
    
                // Handle admins
                if (adminsTableBody) {
                    const admins = data.admins || [];
                    adminsTableBody.innerHTML = admins.length > 0
                        ? populateTable(adminsTableBody, admins, 'admin')
                        : '<tr><td colspan="10" class="text-center py-4">No admins found</td></tr>';
                }
            })
            .catch(error => {
                console.error('Error loading users/admins:', error);
                showToast(error.message || 'Error loading user data. Please refresh the page.', 'error');
                
                // Show error in tables
                if (usersTableBody) {
                    usersTableBody.innerHTML = '<tr><td colspan="9" class="text-center text-danger py-4">Error loading users. Please refresh the page.</td></tr>';
                }
                if (adminsTableBody) {
                    adminsTableBody.innerHTML = '<tr><td colspan="10" class="text-center text-danger py-4">Error loading admins. Please refresh the page.</td></tr>';
                }
            });
    }

    // Function to populate table with user data
    function populateTable(tbody, users, type) {
        if (!tbody || !users || !Array.isArray(users)) return;
        
        if (users.length === 0) {
            const colSpan = type === 'admin' ? 10 : 9;
            tbody.innerHTML = `<tr><td colspan="${colSpan}" class="text-center py-4">No ${type}s found</td></tr>`;
            return;
        }

        tbody.innerHTML = users.map(user => {
            // Get the correct ID field based on user type
            const userId = type === 'admin' ? user.admin_id : user.id;
            const isBanned = user.is_banned === '1' || user.is_banned === 1 || user.is_banned === true;
            const isOnline = user.status === 'Online';
            const statusClass = isBanned ? 'status-banned' : (isOnline ? 'status-active' : 'status-offline');
            const statusText = isBanned ? 'Banned' : (isOnline ? 'Online' : 'Offline');
            const lastSeen = isOnline ? 'Now' : (user.last_seen ? formatLastSeen(user.last_seen) : 'Never');
            const joinedDate = user.created_at ? new Date(user.created_at).toLocaleDateString() : 'N/A';
            
            // Handle avatar URL
            const basePath = window.location.pathname.split('/').slice(0, -1).join('/');
            let avatarUrl = 'assets/images/default-avatar.png';
            if (user.profile_picture && user.profile_picture !== 'NULL') {
                avatarUrl = basePath + '/uploads/avatars/' + user.profile_picture;
            }

            // Generate row HTML
            let rowHtml = `
                <tr>
                    <td class="text-center">
                        <input type="checkbox" class="${type}-select-checkbox" value="${userId}" onchange="updateBulkActionsPanel()">
                    </td>
                    <td class="text-center">
                        <img src="${avatarUrl}" alt="${user.username}" class="user-avatar" 
                             data-user-id="${userId}" data-user-type="${type}">
                    </td>
                    <td>${userId}</td>
                    <td>${user.username || 'N/A'}</td>
                    <td>${user.email || 'N/A'}</td>
                    ${type === 'admin' ? `<td>${user.role || 'Admin'}</td>` : ''}
                    <td>${joinedDate}</td>
                    <td><span class="status-badge ${statusClass}">${statusText}</span></td>
                    <td>${lastSeen}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary edit-user-btn" 
                                data-user-id="${userId}" 
                                data-user-type="${type}">
                            <i class="fas fa-edit"></i>
                        </button>
                    </td>
                </tr>
            `;
            
            return rowHtml;
        }).join('');
    }
    // Helper function to escape HTML
    function escapeHtml(unsafe) {
        if (!unsafe) return '';
        return unsafe
            .toString()
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // Format last seen timestamp into a human-readable format
    function formatLastSeen(timestamp) {
        if (!timestamp) return 'Never';
        
        const now = new Date();
        const lastSeen = new Date(timestamp);
        const diffInSeconds = Math.floor((now - lastSeen) / 1000);
        
        if (diffInSeconds < 60) {
            return 'Just now';
        }
        
        const diffInMinutes = Math.floor(diffInSeconds / 60);
        if (diffInMinutes < 60) {
            return `${diffInMinutes} min${diffInMinutes === 1 ? '' : 's'} ago`;
        }
        
        const diffInHours = Math.floor(diffInMinutes / 60);
        if (diffInHours < 24) {
            return `${diffInHours} hour${diffInHours === 1 ? '' : 's'} ago`;
        }
        
        const diffInDays = Math.floor(diffInHours / 24);
        if (diffInDays < 7) {
            return `${diffInDays} day${diffInDays === 1 ? '' : 's'} ago`;
        }
        
        return lastSeen.toLocaleDateString();
    }

    // Event delegation for edit buttons - now using ModalManager

    // Event delegation for edit buttons - now using ModalManager
    document.addEventListener('click', function(e) {
        const editBtn = e.target.closest('.edit-user-btn');
        if (editBtn) {
            const userId = editBtn.dataset.userId;
            const userType = editBtn.dataset.userType;
            if (userId && userType) {
                // Use the ModalManager to show the modal
                ModalManager.show(userId, userType);
            }
        }
    });

    // Load initial data
    loadUsers();
    loadAdminActionsLog();
    
    // Refresh admin actions log every 30 seconds
    setInterval(loadAdminActionsLog, 30000);
});
