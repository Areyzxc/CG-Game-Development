/**
 * File: admin_users.js
 * Purpose: Handles admin user management logic for CodeGaming
 * Last Updated: October 2, 2025
 */

// Update user avatar in the UI
function updateUserAvatar(userId, userType, newPic) {
    const tableId = userType === 'admin' ? 'adminsTableBody' : 'usersTableBody';
    const tableBody = document.getElementById(tableId);
    if (!tableBody) return;
    
    const userIdStr = String(userId);
    const basePath = window.location.pathname.split('/').slice(0, -1).join('/');
    const timestamp = new Date().getTime();
    const newPicWithTimestamp = newPic ? 
        `${basePath}/uploads/avatars/${newPic}?t=${timestamp}` : 
        'assets/images/PTC.png';

    // Update avatar in the table
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

// Add event listener for save button
document.addEventListener('click', function(e) {
    if (e.target && e.target.id === 'modalSaveButton') {
        const modalOverlay = document.getElementById('retroModalOverlay');
        if (!modalOverlay) return;

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
                updateUserAvatar(userId, userType, profilePic);
                
                // Update the modal display
                const modalUsername = document.getElementById('modalUsername');
                if (modalUsername) modalUsername.textContent = newUsername;
                
                const modalProfilePic = document.getElementById('modalProfilePic');
                if (file && data.data.profile_picture && modalProfilePic) {
                    const timestamp = new Date().getTime();
                    const basePath = window.location.pathname.split('/').slice(0, -1).join('/');
                    const newPicUrl = `${basePath}/uploads/avatars/${data.data.profile_picture}?t=${timestamp}`;
                    modalProfilePic.src = newPicUrl;
                }
                
                // Show success message
                showToast('Profile updated successfully!', 'success');
                
                // Reload the admin actions log to show the update
                if (typeof loadAdminActionsLog === 'function') {
                    loadAdminActionsLog();
                }
                
                // Close the edit mode if open
                const editMode = document.querySelector('.edit-mode');
                if (editMode) {
                    editMode.classList.remove('edit-mode');
                    const viewMode = document.querySelector('.view-mode');
                    if (viewMode) viewMode.style.display = '';
                    editMode.style.display = 'none';
                }
                
                // Close the modal
                const hideModal = window.hideModal || (() => {
                    if (modalOverlay) modalOverlay.style.display = 'none';
                });
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
            if (saveButton) {
                saveButton.disabled = false;
                saveButton.innerHTML = originalButtonText;
            }
        });
    }
});

// Toast notification helper
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
    toast.className = `cg-toast cg-toast-${type}`;
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
    
    // Auto-remove toast after 5 seconds
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 400);
    }, 5000);
}
