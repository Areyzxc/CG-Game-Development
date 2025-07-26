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
 *   - Relies on API endpoints: api/admin_get_users.php, api/admin_get_user_details.php, api/admin_ban_user.php, api/admin_unban_user.php, api/admin_get_actions_log.php.
 * Included Files/Dependencies:
 *   - FontAwesome (icons)
 *   - Bootstrap (optional for modal styling)
 * Author: CodeGaming Team
 * Last Updated: July 22, 2025
 */

// admin_users.js

document.addEventListener('DOMContentLoaded', () => {
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
    const usersTableBody = document.getElementById('usersTableBody');
    const adminsTableBody = document.getElementById('adminsTableBody');
    
    // Modal elements
    const modalOverlay = document.getElementById('retroModalOverlay');
    const modalCloseButton = document.getElementById('modalCloseButton');
    const modalBackButton = document.getElementById('modalBackButton');

    // --- 0. Bulk Actions & Selection ---
    function updateBulkActionsPanel() {
        const userCheckboxes = document.querySelectorAll('.user-select-checkbox:checked');
        const adminCheckboxes = document.querySelectorAll('.admin-select-checkbox:checked');
        const anySelected = userCheckboxes.length > 0 || adminCheckboxes.length > 0;
        const panel = document.getElementById('adminBulkActionsPanel');
        panel.style.display = anySelected ? 'flex' : 'none';
        document.getElementById('bulkBanBtn').disabled = !anySelected;
        document.getElementById('bulkUnbanBtn').disabled = !anySelected;
        document.getElementById('bulkBadgeBtn').disabled = !anySelected;
    }
    document.getElementById('selectAllUsers').addEventListener('change', function(e) {
        document.querySelectorAll('.user-select-checkbox').forEach(cb => cb.checked = e.target.checked);
        updateBulkActionsPanel();
    });
    document.getElementById('selectAllAdmins').addEventListener('change', function(e) {
        document.querySelectorAll('.admin-select-checkbox').forEach(cb => cb.checked = e.target.checked);
        updateBulkActionsPanel();
    });
    // --- 1. Fetch and Display All Users ---
    function loadUsers() {
        fetch('api/admin_get_users.php')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    populateTable(usersTableBody, data.users, 'user');
                    populateTable(adminsTableBody, data.admins, 'admin');
                } else {
                    console.error('Failed to load users:', data.error);
                }
            })
            .catch(err => console.error('Error fetching users:', err));
    }
    function populateTable(tbody, data, type) {
        tbody.innerHTML = ''; // Clear existing rows
        if (data.length === 0) {
            const colSpan = (type === 'user') ? 7 : 8;
            tbody.innerHTML = `<tr><td colspan="${colSpan}" class="text-center">No users found.</td></tr>`;
            return;
        }
        // Get base path for project (e.g., /CodeGaming)
        const basePath = window.location.pathname.split('/').slice(0, -1).join('/');
        data.forEach(user => {
            const row = document.createElement('tr');
            const joinedDate = new Date(user.created_at).toLocaleDateString();
            let profilePic = 'images/PTC.png';
            if (user.profile_picture && user.profile_picture !== 'NULL') {
                profilePic = basePath + '/uploads/avatars/' + user.profile_picture;
            }
            const isBanned = user.is_banned == 1 || user.is_banned === '1';
            let statusBadge = isBanned
                ? '<span class="badge bg-danger ms-2">Banned</span>'
                : '<span class="badge bg-success ms-2">Active</span>';
            let statusClass = user.status === 'Online' ? 'status-active' : 'status-inactive' + (isBanned ? ' status-banned' : '');
            let rowHtml = `
                <td class="text-center"><input type="checkbox" class="${type}-select-checkbox"></td>
                <td class="text-center">
                    <img src="${profilePic}" alt="${user.username}'s avatar" class="table-avatar">
                </td>
                <td>${user.id || user.admin_id}</td>
                <td>${user.username}</td>
                <td>${user.email}</td>
                ${type === 'admin' ? `<td>${user.role}</td>` : ''}
                <td>${joinedDate}</td>
                <td><span class="${statusClass}">${user.status}</span></td>
                <td>${user.last_seen ? user.last_seen : 'Never'}</td>
                <td>
                    <button class="btn btn-sm btn-primary view-details-btn" data-id="${user.id || user.admin_id}" data-type="${type}">
                        View Details
                    </button>
                    ${statusBadge}
                </td>
            `;
            row.innerHTML = rowHtml;
            tbody.appendChild(row);
        });
        // Add event listeners for checkboxes
        tbody.querySelectorAll('input[type="checkbox"]').forEach(cb => {
            cb.addEventListener('change', updateBulkActionsPanel);
        });
    }
    // --- 2. Modal Handling ---
    function showModal(userId, userType) {
        fetch(`api/admin_get_user_details.php?id=${userId}&type=${userType}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    populateModal(data.data);
                    modalOverlay.classList.add('active');
                } else {
                    alert('Could not fetch user details.');
                }
            });
    }
    function hideModal() {
        modalOverlay.classList.remove('active');
    }
    function populateModal(user) {
        document.getElementById('modalTitle').textContent = `@${user.username} // ${user.role}'s Page`;
        // Get base path for project (e.g., /CodeGaming)
        const basePath = window.location.pathname.split('/').slice(0, -1).join('/');
        let modalPic = 'images/PTC.png';
        if (user.profile_picture && user.profile_picture !== 'NULL') {
            modalPic = basePath + '/uploads/avatars/' + user.profile_picture;
        }
        document.getElementById('modalProfilePic').src = modalPic;
        document.getElementById('modalLocation').textContent = `${user.role} / Joined ${new Date(user.created_at).toLocaleDateString()}`;
        document.getElementById('modalUsername').textContent = user.username;
        document.getElementById('modalEmail').textContent = user.email;
        document.getElementById('modalLastSeen').textContent = user.last_seen ? user.last_seen : 'Never';
        const statusEl = document.getElementById('modalStatus');
        statusEl.textContent = user.status;
        statusEl.className = user.status === 'Online' ? 'status-active' : 'status-inactive';
        document.getElementById('modalBadge').textContent = `${user.username} and Proud!`;
        // Set up edit fields
        const usernameInput = document.getElementById('modalUsernameInput');
        usernameInput.value = user.username;
        usernameInput.classList.add('d-none');
        document.getElementById('modalUsername').classList.remove('d-none');
        // Hide profile pic input
        document.getElementById('modalProfilePicInput').classList.add('d-none');
        // Hide Save/Cancel, show Edit
        document.getElementById('modalEditButton').classList.remove('d-none');
        document.getElementById('modalSaveButton').classList.add('d-none');
        document.getElementById('modalCancelButton').classList.add('d-none');
        // Store user id/type for save
        modalOverlay.dataset.userid = user.id || user.admin_id;
        modalOverlay.dataset.usertype = user.role && user.role.toLowerCase().includes('admin') ? 'admin' : 'user';
    }

    // --- Modal Edit/Save/Cancel Logic ---
    const modalEditButton = document.getElementById('modalEditButton');
    const modalSaveButton = document.getElementById('modalSaveButton');
    const modalCancelButton = document.getElementById('modalCancelButton');
    const modalProfilePicInput = document.getElementById('modalProfilePicInput');
    const modalProfilePic = document.getElementById('modalProfilePic');
    const modalUsername = document.getElementById('modalUsername');
    const modalUsernameInput = document.getElementById('modalUsernameInput');

    modalEditButton.addEventListener('click', function() {
        // Show input fields
        modalUsername.classList.add('d-none');
        modalUsernameInput.classList.remove('d-none');
        modalProfilePicInput.classList.remove('d-none');
        // Show Save/Cancel, hide Edit
        modalEditButton.classList.add('d-none');
        modalSaveButton.classList.remove('d-none');
        modalCancelButton.classList.remove('d-none');
    });

    modalCancelButton.addEventListener('click', function() {
        // Hide input fields, revert values
        modalUsernameInput.classList.add('d-none');
        modalUsername.classList.remove('d-none');
        modalProfilePicInput.value = '';
        modalProfilePicInput.classList.add('d-none');
        // Hide Save/Cancel, show Edit
        modalEditButton.classList.remove('d-none');
        modalSaveButton.classList.add('d-none');
        modalCancelButton.classList.add('d-none');
        // Reset preview
        modalProfilePic.src = modalProfilePic.dataset.original || modalProfilePic.src;
        modalUsernameInput.value = modalUsername.textContent;
    });

    // Preview profile picture on file select
    modalProfilePicInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(ev) {
                modalProfilePic.src = ev.target.result;
            };
            reader.readAsDataURL(file);
        }
    });

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
        // AJAX call to backend for saving user edits
        fetch('api/admin_edit_user.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Dynamically update avatar and username in the table (use same path logic as modal)
                const tableId = userType === 'admin' ? 'adminsTableBody' : 'usersTableBody';
                const tableBody = document.getElementById(tableId);
                const userIdStr = String(userId);
                // Get base path for project (e.g., /CodeGaming)
                const basePath = window.location.pathname.split('/').slice(0, -1).join('/');
                const newPic = data.data.profile_picture ? (basePath + '/uploads/avatars/' + data.data.profile_picture) : 'images/PTC.png';
                const newUsername = data.data.username;
                const rows = tableBody.querySelectorAll('tr');
                rows.forEach(row => {
                    // id is in the 3rd cell (index 2)
                    const idCell = row.querySelector('td:nth-child(3)');
                    if (idCell && idCell.textContent.trim() === userIdStr) {
                        // Avatar is in 2nd cell (index 1)
                        const avatarImg = row.querySelector('td:nth-child(2) img');
                        if (avatarImg) {
                            avatarImg.src = newPic;
                        }
                        // Username is in 4th cell (index 3)
                        const usernameCell = row.querySelector('td:nth-child(4)');
                        if (usernameCell && newUsername) {
                            usernameCell.textContent = newUsername;
                        }
                    }
                });
                showToast('User updated successfully!', 'success');
                hideModal();
                // No immediate loadUsers(); keep instant update
            } else {
                showToast('Failed to update user: ' + data.error, 'error');
            }
        });
    });
    // --- 3. Event Listeners ---
    document.body.addEventListener('click', event => {
        if (event.target.classList.contains('view-details-btn')) {
            const userId = event.target.dataset.id;
            const userType = event.target.dataset.type;
            showModal(userId, userType);
        }
    });
    modalCloseButton.addEventListener('click', hideModal);
    modalBackButton.addEventListener('click', hideModal);
    modalOverlay.addEventListener('click', event => {
        if (event.target === modalOverlay) {
            hideModal();
        }
    });
    // --- Bulk Actions ---
    document.getElementById('bulkBanBtn').addEventListener('click', function() {
        handleBulkAction('ban');
    });
    document.getElementById('bulkUnbanBtn').addEventListener('click', function() {
        handleBulkAction('unban');
    });
    document.getElementById('bulkBadgeBtn').addEventListener('click', function() {
        // Implement assign badge logic here (AJAX call)
        alert('Assign badge to selected users/admins (to be implemented)');
    });
    // --- End Modal Edit/Save/Cancel Logic ---
    function handleBulkAction(action) {
        const userIds = Array.from(document.querySelectorAll('.user-select-checkbox:checked')).map(cb => cb.closest('tr').querySelector('td:nth-child(3)').textContent);
        const adminIds = Array.from(document.querySelectorAll('.admin-select-checkbox:checked')).map(cb => cb.closest('tr').querySelector('td:nth-child(3)').textContent);
        const promises = [];
        userIds.forEach(id => {
            promises.push(fetch('api/admin_' + (action === 'ban' ? 'ban' : 'unban') + '_user.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id, type: 'user' })
            }));
        });
        adminIds.forEach(id => {
            promises.push(fetch('api/admin_' + (action === 'ban' ? 'ban' : 'unban') + '_user.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id, type: 'admin' })
            }));
        });
        Promise.all(promises).then(() => {
            loadUsers();
            loadAdminActionsLog();
            updateBulkActionsPanel();
        });
    }
    // --- Quick Search ---
    const searchInput = document.getElementById('adminQuickSearchInput');
    const searchForm = document.getElementById('adminQuickSearchForm');
    const clearBtn = document.createElement('button');
    clearBtn.type = 'button';
    clearBtn.className = 'btn btn-outline-secondary';
    clearBtn.innerHTML = '<i class="fas fa-times"></i>';
    clearBtn.style.marginLeft = '4px';
    searchForm.appendChild(clearBtn);
    clearBtn.addEventListener('click', function() {
        searchInput.value = '';
        document.getElementById('adminQuickSearchResults').innerHTML = '';
        searchInput.focus();
    });

    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const query = searchInput.value.trim();
        if (!query) return;
        fetch('api/admin_get_users.php?q=' + encodeURIComponent(query))
            .then(res => res.json())
            .then(data => {
                const resultsDiv = document.getElementById('adminQuickSearchResults');
                if (data.success) {
                    let html = '';
                    if ((data.users && data.users.length) || (data.admins && data.admins.length)) {
                        html += '<ul class="list-group">';
                        if (data.users && data.users.length) {
                            html += '<li class="list-group-item fw-bold">Users</li>';
                            data.users.forEach(user => {
                                const isBanned = user.is_banned == 1 || user.is_banned === '1';
                                html += `<li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class='fas fa-user me-2'></i>${user.username} <span class='text-muted small'>(${user.email})</span></span>
                                    <span>${isBanned ? '<span class=\'badge bg-danger\'>Banned</span>' : '<span class=\'badge bg-success\'>Active</span>'}</span>
                                </li>`;
                            });
                        }
                        if (data.admins && data.admins.length) {
                            html += '<li class="list-group-item fw-bold">Admins</li>';
                            data.admins.forEach(admin => {
                                const isBanned = admin.is_banned == 1 || admin.is_banned === '1';
                                html += `<li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class='fas fa-user-shield me-2'></i>${admin.username} <span class='text-muted small'>(${admin.email})</span> <span class='badge bg-secondary ms-2'>${admin.role}</span></span>
                                    <span>${isBanned ? '<span class=\'badge bg-danger\'>Banned</span>' : '<span class=\'badge bg-success\'>Active</span>'}</span>
                                </li>`;
                            });
                        }
                        html += '</ul>';
                    } else {
                        html = '<div class="text-muted">No results found.</div>';
                    }
                    resultsDiv.innerHTML = html;
                } else {
                    resultsDiv.innerHTML = '<div class="text-danger">Search failed. Please try again.</div>';
                }
            });
    });
    // --- 4. Admin Actions Log Placeholder ---
    function loadAdminActionsLog() {
        fetch('api/admin_get_actions_log.php')
            .then(res => res.json())
            .then(data => {
                const log = document.getElementById('adminActionsLog');
                if (data.success && Array.isArray(data.actions)) {
                    log.innerHTML = '';
                    data.actions.forEach(act => {
                        const li = document.createElement('li');
                        li.className = 'list-group-item';
                        let actionVerb = act.action_type;
                        if (actionVerb === 'ban') actionVerb = 'banned';
                        else if (actionVerb === 'unban') actionVerb = 'unbanned';
                        else if (actionVerb === 'assign_badge') actionVerb = 'assigned badge to';
                        else actionVerb += 'ed';
                        li.innerHTML = `<b>${act.admin_username || 'Unknown Admin'}</b> ${actionVerb} <b>${act.target_type}</b> #${act.target_id} <span class='text-muted small'>${new Date(act.created_at).toLocaleString()}</span>`;
                        log.appendChild(li);
                    });
                } else {
                    log.innerHTML = '<li class="list-group-item text-muted">No recent actions.</li>';
                }
            });
    }
    loadAdminActionsLog();
    setInterval(loadAdminActionsLog, 15000);
    // --- Initial Load ---
    loadUsers();
});