/**
 * File: admin_profile.js
 * Purpose: Handles admin profile management functionality
 * Author: CodeGaming Team
 * Last Updated: October 21, 2025
 */

document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const editProfileBtn = document.getElementById('editProfileBtn');
    const cancelEditBtn = document.getElementById('cancelEditBtn');
    const profileForm = document.getElementById('profileForm');
    const passwordForm = document.getElementById('passwordForm');
    const changeAvatarBtn = document.getElementById('changeAvatarBtn');
    const avatarInput = document.getElementById('avatarInput');
    const profileAvatar = document.getElementById('profileAvatar');
    const saveButtonContainer = document.getElementById('saveButtonContainer');
    
    const usernameInput = document.getElementById('username');
    const emailInput = document.getElementById('email');

    // Edit Profile
    if (editProfileBtn) {
        editProfileBtn.addEventListener('click', function() {
            usernameInput.disabled = false;
            emailInput.disabled = false;
            saveButtonContainer.classList.remove('d-none');
            editProfileBtn.classList.add('d-none');
        });
    }

    // Cancel Edit
    if (cancelEditBtn) {
        cancelEditBtn.addEventListener('click', function() {
            usernameInput.disabled = true;
            emailInput.disabled = true;
            saveButtonContainer.classList.add('d-none');
            editProfileBtn.classList.remove('d-none');
            profileForm.reset();
        });
    }

    // Save Profile
    if (profileForm) {
        profileForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(profileForm);
            
            fetch('api/admin_update_profile.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Profile updated successfully!', 'success');
                    usernameInput.disabled = true;
                    emailInput.disabled = true;
                    saveButtonContainer.classList.add('d-none');
                    editProfileBtn.classList.remove('d-none');
                    
                    // Update header if username changed
                    if (typeof updateUserHeader === 'function') {
                        updateUserHeader(data.data);
                    }
                } else {
                    showToast(data.error || 'Failed to update profile', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('An error occurred while updating profile', 'error');
            });
        });
    }

    // Change Avatar
    if (changeAvatarBtn && avatarInput) {
        changeAvatarBtn.addEventListener('click', function() {
            avatarInput.click();
        });

        avatarInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            // Validate file type
            if (!file.type.match('image.*')) {
                showToast('Please select an image file', 'error');
                return;
            }

            // Validate file size (max 5MB)
            if (file.size > 5 * 1024 * 1024) {
                showToast('Image size must be less than 5MB', 'error');
                return;
            }

            // Preview image
            const reader = new FileReader();
            reader.onload = function(event) {
                profileAvatar.src = event.target.result;
            };
            reader.readAsDataURL(file);

            // Upload image
            const formData = new FormData();
            formData.append('profile_picture', file);
            formData.append('admin_id', document.querySelector('input[name="admin_id"]').value);

            fetch('api/admin_update_avatar.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Profile picture updated successfully!', 'success');
                    
                    // Update header avatar
                    const headerAvatar = document.querySelector('.admin-profile-pic');
                    if (headerAvatar && data.data.profile_picture) {
                        const timestamp = new Date().getTime();
                        headerAvatar.src = `uploads/avatars/${data.data.profile_picture}?t=${timestamp}`;
                    }
                } else {
                    showToast(data.error || 'Failed to update profile picture', 'error');
                    // Revert preview on error
                    profileAvatar.src = profileAvatar.dataset.original || 'assets/images/PTC.png';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('An error occurred while uploading image', 'error');
                profileAvatar.src = profileAvatar.dataset.original || 'assets/images/PTC.png';
            });
        });
    }

    // Change Password
    if (passwordForm) {
        passwordForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const currentPassword = document.getElementById('currentPassword').value;
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;

            // Validate passwords
            if (newPassword.length < 8) {
                showToast('New password must be at least 8 characters', 'error');
                return;
            }

            if (newPassword !== confirmPassword) {
                showToast('Passwords do not match', 'error');
                return;
            }

            const formData = new FormData();
            formData.append('current_password', currentPassword);
            formData.append('new_password', newPassword);

            fetch('api/admin_change_password.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Password changed successfully!', 'success');
                    passwordForm.reset();
                } else {
                    showToast(data.error || 'Failed to change password', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('An error occurred while changing password', 'error');
            });
        });
    }
});

// Toast notification function
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
    }, 3000);
}
