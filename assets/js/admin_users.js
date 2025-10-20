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
 * Last Updated: October 2, 2025
 */

// admin_users.js
// Add this helper function at the top of your file
function updateUserAvatar(userId, userType, newPic) {
    // Update avatar in the table
    const tableId = userType === 'admin' ? 'adminsTableBody' : 'usersTableBody';
    const tableBody = document.getElementById(tableId);
    const userIdStr = String(userId);
    const basePath = window.location.origin + window.location.pathname.split('/').slice(0, -1).join('/');
    const timestamp = new Date().getTime();
    
    // Handle both full paths and filenames
    const avatarFilename = newPic ? newPic.split('/').pop() : 'PTC.png';
    const newAvatarPath = newPic ? `uploads/avatars/${avatarFilename}` : 'assets/images/PTC.png';
    const newAvatarFullPath = `${basePath}/${newAvatarPath}?t=${timestamp}`;

    // Update avatars in the table
    const rows = tableBody.querySelectorAll('tr');
    rows.forEach(row => {
        const idCell = row.querySelector('td:nth-child(3)');
        if (idCell && idCell.textContent.trim() === userIdStr) {
            const avatarImg = row.querySelector('td:nth-child(2) img');
            if (avatarImg) {
                // Update the image source with timestamp to prevent caching
                avatarImg.src = newAvatarFullPath;
            }
        }
    });

    // Update any other instances in the UI
    const fullAvatarPath = newPic ? `${basePath}/uploads/avatars/${avatarFilename}?t=${timestamp}` : `${basePath}/assets/images/PTC.png?t=${timestamp}`;
    
    // Update all matching user avatars in the page
    document.querySelectorAll(`img[data-user-id="${userId}"][data-user-type="${userType}"]`).forEach(img => {
        img.src = fullAvatarPath;
    });
    
    // Update all admin profile pictures in the header that match this user
    const adminProfilePics = document.querySelectorAll(`.admin-profile-pic[data-user-id="${userId}"]`);
    if (adminProfilePics.length > 0) {
        adminProfilePics.forEach(img => {
            // Only update if the image source is different to prevent flickering
            if (img.src !== fullAvatarPath) {
                // Add fade effect
                img.style.transition = 'opacity 0.3s ease';
                img.style.opacity = '0.7';
                
                // Update the source after a small delay for the fade effect
                setTimeout(() => {
                    img.src = fullAvatarPath;
                    // Force a reflow to ensure the new image is loaded
                    void img.offsetWidth;
                    img.style.opacity = '1';
                }, 100);
            }
        });
    }
}

// Add event listener for save button
document.addEventListener('click', function(e) {
    if (e.target && e.target.id === 'modalSaveButton') {
        const modalOverlay = document.getElementById('retroModalOverlay');
        const userId = modalOverlay.dataset.userid;
        const userType = modalOverlay.dataset.usertype;
        const newUsername = document.getElementById('editUsernameInput')?.value.trim() || 
                              document.getElementById('modalUsername')?.textContent.trim();
        const file = document.getElementById('modalProfilePicInput')?.files[0];

        if (!newUsername) {
            showToast('Username cannot be empty', 'error');
            return;
        }

        const formData = new FormData();
        formData.append('id', userId);
        formData.append('type', userType);
        formData.append('username', newUsername);
        if (file) formData.append('profile_picture', file);

        // Show loading state
        const saveButton = e.target;
        const originalButtonText = saveButton.innerHTML;
        saveButton.disabled = true;
        saveButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';

        fetch('api/admin_edit_user.php', {
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
                // Update the UI with new data
                const profilePic = data.data.profile_picture || '';
                updateUserInUI(userId, userType, newUsername, profilePic);
                
                // Update the modal display
                const modalUsername = document.getElementById('modalUsername');
                if (modalUsername) modalUsername.textContent = newUsername;
                
                const modalProfilePic = document.getElementById('modalProfilePic');
                if (file && data.data.profile_picture && modalProfilePic) {
                    const timestamp = new Date().getTime();
                    const basePath = window.location.origin + window.location.pathname.split('/').slice(0, -1).join('/');
                    const newPicUrl = `${basePath}/uploads/avatars/${data.data.profile_picture}?t=${timestamp}`;
                    modalProfilePic.src = newPicUrl;
                }
                
                // Show success message
                showToast('Profile updated successfully!', 'success');
                
                // Reload the admin actions log to show the update
                loadAdminActionsLog();
                
                // Close the edit mode if open
                const editMode = document.querySelector('.edit-mode');
                if (editMode) {
                    editMode.classList.remove('edit-mode');
                    const viewMode = document.querySelector('.view-mode');
                    if (viewMode) viewMode.style.display = '';
                    editMode.style.display = 'none';
                }
                
                // Close the modal
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
            showToast(error.message || 'Failed to update profile', 'error');
        })
        .catch(error => {
            console.error('Error updating profile:', error);
            showToast('Error: ' + (error.message || 'Failed to update profile. Please try again.'), 'error');
        })
        .finally(() => {
            // Reset button state
            if (saveButton) {
                saveButton.disabled = false;
                saveButton.innerHTML = originalButtonText;
            }
        });
    }
});
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
            let profilePic = 'assets/images/PTC.png';
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
        console.log('showModal called with:', { userId, userType });
        
        // Get or create modal overlay
        let modalOverlay = document.getElementById('retroModalOverlay');
        if (!modalOverlay) {
            console.error('Modal overlay element not found');
            alert('Error: Modal overlay not found');
            return;
        }
        
        // Show loading state
        modalOverlay.classList.add('loading');
        modalOverlay.style.display = 'flex';
        
        // Set loading content
        const loadingHtml = `
            <div class="text-center p-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading user data...</p>
            </div>`;
            
        // Set up the modal structure if it doesn't exist
        const modalHtml = `
            <div class="retro-modal">
                <div class="retro-modal-title-bar">
                    <span id="modalTitle">Loading...</span>
                    <div class="title-bar-buttons">
                        <span id="modalCloseButton">✕</span>
                    </div>
                </div>
                <div class="retro-modal-content">
                    ${loadingHtml}
                </div>
                <div class="retro-modal-status-bar">
                    <button id="modalBackButton" class="back-button">← Back</button>
                    <div class="status-badge">User Profile</div>
                    <div class="time-display">${new Date().toLocaleTimeString()}</div>
                </div>
            </div>`;
            
        // Update or create the modal content
        modalOverlay.innerHTML = modalHtml;
        
        // Set up event listeners for the new elements
        const closeButton = document.getElementById('modalCloseButton');
        const backButton = document.getElementById('modalBackButton');
        
        if (closeButton) {
            closeButton.addEventListener('click', hideModal);
        }
        if (backButton) {
            backButton.addEventListener('click', hideModal);
        }
        
        // Always fetch fresh data when opening the modal with cache-busting
        const url = `api/admin_get_user_details.php?id=${userId}&type=${userType}&_=${new Date().getTime()}`;
        console.log('Fetching user details from:', url);
        
        fetch(url)
            .then(async response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Error response:', errorText);
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const text = await response.text();
                console.log('Raw response text:', text);
                try {
                    const json = text ? JSON.parse(text) : {};
                    console.log('Parsed response:', json);
                    return json;
                } catch (e) {
                    console.error('Failed to parse JSON response. Text was:', text);
                    throw new Error('Invalid server response: ' + e.message);
                }
            })
            .then(data => {
                console.log('Data received:', data);
                if (data && data.success) {
                    console.log('Data is valid, populating modal...');
                    // Remove loading state before populating
                    modalOverlay.classList.remove('loading');
                    try {
                        populateModal(data.data);
                        modalOverlay.classList.add('active');
                        console.log('Modal populated successfully');
                    } catch (e) {
                        console.error('Error in populateModal:', e);
                        throw new Error('Failed to populate modal: ' + e.message);
                    }
                } else {
                    const errorMsg = data?.error || 'Could not fetch user details';
                    console.error('API returned error:', errorMsg);
                    throw new Error(errorMsg);
                }
            })
            .catch(error => {
                console.error('Error in showModal:', {
                    error: error,
                    message: error.message,
                    stack: error.stack
                });
                modalOverlay.classList.remove('loading');
                showToast('Error loading user details: ' + (error.message || 'Unknown error'), 'error');
                hideModal();
            });
    }
    function hideModal() {
        const modalOverlay = document.getElementById('retroModalOverlay');
        if (modalOverlay) {
            modalOverlay.classList.remove('active', 'loading');
            modalOverlay.style.display = 'none';
            
            // Clear any existing content
            const modalContent = modalOverlay.querySelector('.retro-modal-content');
            if (modalContent) {
                modalContent.innerHTML = '';
            }
        }
    }
    function populateModal(user) {
        console.log('populateModal called with user:', user);
        
        if (!user) {
            console.error('No user data provided to populateModal');
            throw new Error('No user data available');
        }
        
        const modalOverlay = document.getElementById('retroModalOverlay');
        if (!modalOverlay) {
            console.error('Modal overlay not found');
            return false;
        }
        
        // Create the main modal content
        const basePath = window.location.pathname.split('/').slice(0, -1).join('/');
        const timestamp = new Date().getTime();
        const picUrl = user.profile_picture 
            ? `${basePath}/uploads/avatars/${user.profile_picture}?t=${timestamp}`
            : `${basePath}/assets/images/PTC.png`;
            
            const modalContent = `
            <div class="retro-modal">
                <div class="retro-modal-title-bar">
                    <span id="modalTitle">@${user.username || 'Unknown'} // ${user.role || 'User'}'s Page</span>
                    <div class="title-bar-buttons">
                        <span id="modalCloseButton">✕</span>
                    </div>
                </div>
                <!-- Add the menu bar here -->
                <div class="retro-modal-menu-bar">
                    <button class="menu-button" id="modalEditButton">Edit</button>
                    <button class="menu-button d-none" id="modalSaveButton">Save</button>
                    <button class="menu-button d-none" id="modalCancelButton">Cancel</button>
                    <button class="menu-button" id="modalPasswordButton">Reset Pass</button>
                    <button class="menu-button" id="modalActivityButton">Activity</button>
                    <button class="menu-button" id="modalProgressButton">Progress</button>
                </div>
                <div class="retro-modal-content">
                    <div class="profile-picture-container">
                        <img id="modalProfilePic" 
                             src="${picUrl}" 
                             alt="${user.username || 'User'}'s profile"
                             class="user-avatar"
                             onerror="this.src='${basePath}/assets/images/PTC.png'">
                        <input type="file" id="modalProfilePicInput" accept="image/*" class="d-none">
                    </div>
                    <div class="file-details-container">
                        <div class="file-info-box">
                            <p><span>Username:</span> <span id="modalUsername">${user.username || 'Unknown'}</span></p>
                            <p><span>Role:</span> ${user.role || 'User'}</p>
                            <p><span>Status:</span> <span class="status-${user.status || 'inactive'}">${user.status || 'Inactive'}</span></p>
                            <p><span>Last Active:</span> ${user.last_seen || 'Unknown'}</p>
                        </div>
                        <div class="details-section">
                            <div class="section-title">Account Details</div>
                            <p><span>User ID:</span> ${user.id || 'N/A'}</p>
                            <p><span>Email:</span> ${user.email || 'N/A'}</p>
                            <p><span>Joined:</span> ${new Date(user.created_at).toLocaleDateString() || 'Unknown'}</p>
                        </div>
                    </div>
                </div>
                <!-- Add Progress Content -->
                <div class="progress-content" id="progressContent" style="display: none;">
                    <div class="progress-ring-wrapper">
                        <div class="progress-ring">
                            <svg class="progress-ring__circle" width="160" height="160">
                                <circle class="progress-ring__circle-bg" r="70" cx="80" cy="80"/>
                                <circle class="progress-ring__circle-fill" r="70" cx="80" cy="80"/>
                            </svg>
                            <div class="progress-ring__content">
                                <div class="progress-ring__percent">0%</div>
                                <div class="progress-ring__label">Total Progress</div>
                            </div>
                        </div>
                        <div class="stats-grid">
                            <div class="stat-card">
                                <div class="stat-value" id="completedTutorials">0</div>
                                <div class="stat-label">Tutorials</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-value" id="completedQuizzes">0</div>
                                <div class="stat-label">Quizzes</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-value" id="completedChallenges">0</div>
                                <div class="stat-label">Challenges</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-value" id="completedMiniGames">0/2</div>
                                <div class="stat-label">Mini-Games</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-value" id="totalXP">0</div>
                                <div class="stat-label">Total XP</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="retro-modal-status-bar">
                    <button id="modalBackButton" class="back-button">← Back</button>
                    <div class="status-badge">${user.role === 'admin' ? 'Admin' : 'User'} Profile</div>
                    <div class="time-display">${new Date().toLocaleTimeString()}</div>
                </div>
            </div>`;
        
       // Update the modal content
    modalOverlay.innerHTML = modalContent;


    // Add this to admin_users.js after the modal content is created
function setupModalButtons(user) {
    // Edit Button
    document.getElementById('modalEditButton').addEventListener('click', function() {
        // Show edit mode
        enableEditMode();
    });

    // Reset Pass Button
    document.getElementById('modalPasswordButton').addEventListener('click', function() {
        // Implement password reset functionality
        fetch(`api/admin_reset_password.php?id=${user.id}&type=${user.role}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Password reset email sent to user', 'success');
                } else {
                    showToast('Failed to reset password: ' + data.error, 'error');
                }
            })
            .catch(error => {
                showToast('Error resetting password', 'error');
                console.error('Password reset error:', error);
            });
    });

    // Activity Button
    document.getElementById('modalActivityButton').addEventListener('click', function() {
        // Hide other content sections
        document.querySelectorAll('.retro-modal-content > div, .progress-content').forEach(div => {
            div.style.display = 'none';
        });
        
        // Show activity content
        const activityContent = document.createElement('div');
        activityContent.className = 'activity-content';
        activityContent.innerHTML = '<div class="loading">Loading activity...</div>';
        document.querySelector('.retro-modal-content').appendChild(activityContent);
        
        // Fetch user activity
        fetch(`api/admin_get_user_activity.php?id=${user.id}&type=${user.role}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    activityContent.innerHTML = `
                        <div class="activity-list">
                            ${data.activities.map(activity => `
                                <div class="activity-item">
                                    <div class="activity-time">${new Date(activity.timestamp).toLocaleString()}</div>
                                    <div class="activity-description">${activity.description}</div>
                                </div>
                            `).join('')}
                        </div>
                    `;
                } else {
                    activityContent.innerHTML = '<div class="error">Failed to load activity</div>';
                }
            })
            .catch(error => {
                activityContent.innerHTML = '<div class="error">Error loading activity</div>';
            });
    });

    // Progress Button (already implemented in your code)
    document.getElementById('modalProgressButton').addEventListener('click', () => {
        // Hide other content sections
        document.querySelectorAll('.retro-modal-content > div').forEach(div => {
            div.style.display = 'none';
        });
        
        // Show progress content
        document.getElementById('progressContent').style.display = 'block';
        
        // Load progress data
        loadUserProgress(user.id);
    });
}

    // Remove the setupEditHandlers() call since we'll handle it differently
    // Instead, call our new setupModalButtons function
    setupModalButtons(user);
    
    // Set up event listeners for close and back buttons
    const closeButton = document.getElementById('modalCloseButton');
    const backButton = document.getElementById('modalBackButton');
    
    if (closeButton) closeButton.addEventListener('click', hideModal);
    if (backButton) backButton.addEventListener('click', hideModal);

    return true;
    }
        
        function setupEditHandlers() {
            const editButton = document.createElement('button');
            editButton.className = 'btn btn-sm btn-outline-primary';
            editButton.innerHTML = '<i class="fas fa-edit"></i> Edit';
            editButton.addEventListener('click', enableEditMode);
            
            const titleBar = document.querySelector('.retro-modal-title-bar');
            if (titleBar) {
                titleBar.appendChild(editButton);
            }
            
            function enableEditMode() {
                console.log('Edit mode enabled');
                
                // Get the necessary elements
                const usernameSpan = document.getElementById('modalUsername');
                const profilePic = document.getElementById('modalProfilePic');
                const profilePicContainer = document.querySelector('.profile-picture-container');
                const fileInput = document.getElementById('modalProfilePicInput');
                
                if (!usernameSpan || !profilePic || !profilePicContainer) {
                    console.error('Required elements not found for edit mode');
                    return;
                }
                
                // Replace username with an input field
                const currentUsername = usernameSpan.textContent;
                const usernameInput = document.createElement('input');
                usernameInput.type = 'text';
                usernameInput.id = 'editUsernameInput';
                usernameInput.className = 'form-control';
                usernameInput.value = currentUsername;
                usernameSpan.replaceWith(usernameInput);
                
                // Add change picture button
                const changePicBtn = document.createElement('button');
                changePicBtn.className = 'btn btn-sm btn-outline-secondary mt-2';
                changePicBtn.innerHTML = '<i class="fas fa-camera"></i> Change Picture';
                changePicBtn.onclick = () => fileInput.click();
                
                // Add file input change handler
                fileInput.onchange = (e) => {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (event) => {
                            profilePic.src = event.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                };
                
                // Add buttons container
                const buttonsContainer = document.createElement('div');
                buttonsContainer.className = 'd-flex gap-2 mt-3';
                
                // Add save button
                const saveBtn = document.createElement('button');
                saveBtn.className = 'btn btn-primary btn-sm';
                saveBtn.innerHTML = '<i class="fas fa-save"></i> Save';
                saveBtn.onclick = saveChanges;
                
                // Add cancel button
                const cancelBtn = document.createElement('button');
                cancelBtn.className = 'btn btn-outline-secondary btn-sm';
                cancelBtn.textContent = 'Cancel';
                cancelBtn.onclick = cancelEdit;
                
                // Add buttons to container
                buttonsContainer.appendChild(saveBtn);
                buttonsContainer.appendChild(cancelBtn);
                
                // Add elements to profile container
                profilePicContainer.appendChild(changePicBtn);
                profilePicContainer.appendChild(buttonsContainer);
                
                // Save changes function
                async function saveChanges() {
                    const newUsername = usernameInput.value.trim();
                    if (!newUsername) {
                        showToast('Username cannot be empty', 'error');
                        return;
                    }
                    
                    // Show loading state
                    saveBtn.disabled = true;
                    saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';
                    
                    const formData = new FormData();
                    formData.append('id', user.id);
                    formData.append('type', user.role === 'admin' ? 'admin' : 'user');
                    formData.append('username', newUsername);
                    
                    // Add file if selected
                    const file = fileInput.files[0];
                    if (file) {
                        formData.append('profile_picture', file);
                    }
                    
                    try {
                        const response = await fetch('api/update_profile.php', {
                            method: 'POST',
                            body: formData
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            showToast('Profile updated successfully', 'success');
                            // Update the displayed username
                            usernameInput.replaceWith(usernameSpan);
                            usernameSpan.textContent = newUsername;
                            
                            // Update the user object
                            user.username = newUsername;
                            if (data.profile_picture) {
                                user.profile_picture = data.profile_picture;
                                profilePic.src = `${basePath}/uploads/avatars/${data.profile_picture}?t=${new Date().getTime()}`;
                            }
                            
                            // Exit edit mode
                            cancelEdit();
                        } else {
                            throw new Error(data.message || 'Failed to update profile');
                        }
                    } catch (error) {
                        console.error('Error updating profile:', error);
                        showToast(error.message || 'Failed to update profile', 'error');
                    } finally {
                        saveBtn.disabled = false;
                        saveBtn.innerHTML = '<i class="fas fa-save"></i> Save';
                    }
                }
                
                // Cancel edit function
                function cancelEdit() {
                    // Restore original username
                    usernameInput.replaceWith(usernameSpan);
                    usernameSpan.textContent = currentUsername;
                    
                    // Remove edit mode elements
                    changePicBtn.remove();
                    buttonsContainer.remove();
                    
                    // Reset file input
                    fileInput.value = '';
                    
                    // Restore original image if changed
                    if (fileInput.files.length > 0) {
                        profilePic.src = picUrl;
                    }
                }
            }
        }
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
            showToast('Username cannot be empty.', 'error');
            return;
        }
        
        // Show loading state
        const saveButton = this;
        const originalButtonText = saveButton.innerHTML;
        saveButton.disabled = true;
        saveButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';
        
        const formData = new FormData();
        formData.append('id', userId);
        formData.append('type', userType);
        formData.append('username', newUsername);
        if (file) formData.append('profile_picture', file);
        
        // Add CSRF token if available
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        if (csrfToken) {
            formData.append('csrf_token', csrfToken);
        }
        
        // AJAX call to backend for saving user edits
        fetch('api/admin_edit_user.php', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'  // Helps identify AJAX requests on the server
            }
        })
        .then(async response => {
            const text = await response.text();
            console.log('Raw server response:', { status: response.status, statusText: response.statusText, text });
            let data;
            try {
                if (!text.trim()) {
                    throw new Error('Server returned an empty response');
                }
                data = JSON.parse(text);
                console.log('Parsed response data:', data);
                if (!response.ok) {
                    const errorMsg = data.error || data.message || 'Failed to update user';
                    console.error('Server returned error:', errorMsg, 'Status:', response.status);
                    throw new Error(errorMsg);
                }
                return data;
            } catch (e) {
                console.error('Error parsing response. Text was:', text, 'Error:', e);
                if (response.ok) {
                    // If the response is 200 but we couldn't parse it, it might be a success
                    return { success: true, message: 'User updated successfully' };
                }
                throw new Error('Invalid server response: ' + (e.message || 'Could not parse response'));
            }
        })
        .then(data => {
            console.log('Update response:', data);
            if (data && data.success) {
                const profilePic = (data.data && data.data.profile_picture) ? data.data.profile_picture : null;
                
                // Update the UI first
                updateUserInUI(userId, userType, newUsername, profilePic);
                
                // Store the updated data in the modal's dataset
                if (modalOverlay) {
                    modalOverlay.dataset.username = newUsername;
                    if (profilePic) {
                        modalProfilePic.src = `${window.location.pathname.split('/').slice(0, -1).join('/')}/uploads/avatars/${profilePic}`;
                    }
                }
                
                // Show success message
                showToast(data.message || 'User updated successfully!', 'success');
                
                // Close the modal after a short delay to show the success message
                setTimeout(() => {
                    hideModal();
                    // Reset the modal state
                    if (modalUsername && modalUsernameInput) {
                        modalUsername.textContent = newUsername;
                        modalUsernameInput.value = newUsername;
                    }
                }, 800);
                
                // If this is the current user's profile, update the session data
                if (typeof updateUserHeader === 'function') {
                    updateUserHeader({
                        username: newUsername,
                        profile_picture: profilePic,
                        id: userId
                    });
                }
            } else {
                throw new Error(data?.error || data?.message || 'Failed to update user');
            }
        })
        .catch(error => {
            console.error('Update error details:', {
                error: error,
                message: error.message,
                stack: error.stack
            });
            showToast('Error: ' + (error.message || 'Failed to update user. Please try again.'), 'error');
        })
        .finally(() => {
            // Reset button state
            saveButton.disabled = false;
            saveButton.innerHTML = originalButtonText;
        });
    });
    
    // Helper function to update user in the UI
    function updateUserInUI(userId, userType, newUsername, profilePic) {
        const tableId = userType === 'admin' ? 'adminsTableBody' : 'usersTableBody';
        const tableBody = document.getElementById(tableId);
        if (!tableBody) return;
        
        const rows = tableBody.querySelectorAll('tr');
        const basePath = window.location.pathname.split('/').slice(0, -1).join('/');
        
        rows.forEach(row => {
            const idCell = row.querySelector('td:nth-child(3)');
            if (idCell && idCell.textContent.trim() === userId) {
                // Update username (4th column)
                const usernameCell = row.querySelector('td:nth-child(4)');
                if (usernameCell) {
                    usernameCell.textContent = newUsername;
                }
                
                // Update avatar if changed
                if (profilePic) {
                    const avatarImg = row.querySelector('td:nth-child(2) img');
                    if (avatarImg) {
                        const timestamp = new Date().getTime();
                        avatarImg.src = `${basePath}/uploads/avatars/${profilePic}?t=${timestamp}`;
                    }
                }
            }
        });
    }
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
            .then(async response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const text = await response.text();
                try {
                    return text ? JSON.parse(text) : {};
                } catch (e) {
                    console.error('Failed to parse actions log JSON:', text);
                    throw new Error('Invalid server response');
                }
            })
            .then(data => {
                const log = document.getElementById('adminActionsLog');
                if (data && data.success && Array.isArray(data.actions)) {
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
            })
            .catch(error => {
                console.error('Error loading admin actions log:', error);
                const log = document.getElementById('adminActionsLog');
                if (log) {
                    log.innerHTML = '<li class="list-group-item text-danger">Error loading actions log.</li>';
                }
            });
    }
    
    // Initialize the admin interface
    loadAdminActionsLog();
    setInterval(loadAdminActionsLog, 15000);
    loadUsers();
});
