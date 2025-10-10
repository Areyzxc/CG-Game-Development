// Profile page specific JavaScript

// Show toast notification function
function showToast(message, type = 'success') {
    console.log('showToast called with:', { message, type });
    
    const toastEl = document.getElementById('successToast');
    const toastMessage = document.getElementById('toastMessage');
    
    if (toastEl && toastMessage) {
        toastMessage.textContent = message;
        
        // Set toast class based on type
        toastEl.className = 'toast align-items-center text-white bg-' + 
            (type === 'success' ? 'success' : 'danger') + 
            ' border-0 position-fixed bottom-0 end-0 m-3';
        
        const toast = new bootstrap.Toast(toastEl, {
            autohide: true,
            delay: 5000
        });
        
        toast.show();
    } else {
        console.error('Toast elements not found');
    }
}

// Initialize profile page functionality
function initializeProfilePage() {
    // Image preview for profile picture upload
    const profilePicInput = document.getElementById('profile_picture');
    if (profilePicInput) {
        profilePicInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const profileImg = document.getElementById('profileImagePreview');
                    const avatarImg = document.getElementById('avatarPreview');
                    if (profileImg) profileImg.src = event.target.result;
                    if (avatarImg) avatarImg.src = event.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Toggle read more/less for bio
    const readMoreBtn = document.getElementById('readMoreBtn');
    const userBio = document.getElementById('userBio');

    if (readMoreBtn && userBio) {
        const fullText = userBio.textContent;
        const maxLength = 200;
        
        if (fullText.length > maxLength) {
            const shortText = fullText.substring(0, maxLength) + '...';
            userBio.textContent = shortText;
            readMoreBtn.style.display = 'inline-block';
            
            let isExpanded = false;
            readMoreBtn.addEventListener('click', function(e) {
                e.preventDefault();
                isExpanded = !isExpanded;
                userBio.textContent = isExpanded ? fullText : shortText;
                readMoreBtn.textContent = isExpanded ? 'Read Less' : 'Read More';
            });
        } else {
            readMoreBtn.style.display = 'none';
        }
    }

    // Banner upload elements
    const bannerInput = document.getElementById('headerBanner') || document.getElementById('banner');
    const bannerPreview = document.getElementById('bannerPreview');
    const bannerPreviewImg = document.getElementById('bannerPreviewImg');
    const uploadBtn = document.getElementById('uploadBannerBtn');

    // Account action elements
    const btnDeactivate = document.getElementById('btnDeactivate');
    const btnDelete = document.getElementById('btnDelete');
    const accountActionModal = new bootstrap.Modal(document.getElementById('accountActionModal'));
    const accountActionForm = document.getElementById('accountActionForm');
    const accountActionText = document.getElementById('accountActionText');
    const accountActionInput = document.getElementById('accountActionInput');

    // Banner preview functionality
    if (bannerInput && bannerPreview && bannerPreviewImg) {
        bannerInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    bannerPreviewImg.src = e.target.result;
                    bannerPreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                bannerPreview.style.display = 'none';
            }
        });
    }

    // Banner upload form handling
    const bannerForm = document.getElementById('bannerUploadForm');
    if (bannerForm) {
        bannerForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            console.log('Banner upload form submitted');

            const uploadBtn = document.getElementById('uploadBannerBtn');
            const uploadText = uploadBtn?.querySelector('.upload-text');
            const spinner = uploadBtn?.querySelector('.spinner-border');

            try {
                // Show loading state
                if (uploadBtn && uploadText && spinner) {
                    uploadBtn.disabled = true;
                    uploadText.textContent = 'Uploading...';
                    spinner.classList.remove('d-none');
                }

                const formData = new FormData(this);
                console.log('FormData:', formData);

                const response = await fetch('profile.php', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const responseText = await response.text();
                console.log('Raw response:', responseText);

                // Try to parse JSON
                let data;
                try {
                    data = JSON.parse(responseText);
                } catch (e) {
                    console.error('Failed to parse JSON response');
                    throw new Error('Invalid server response');
                }

                console.log('Parsed response:', data);

                if (data.success) {
                    // Show success message
                    showToast(data.message || 'Banner uploaded successfully!');
                    
                    // Close the modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('bannerUploadModal'));
                    if (modal) {
                        modal.hide();
                        // Reload the page after modal is hidden
                        modal._element.addEventListener('hidden.bs.modal', function() {
                            window.location.reload();
                        }, { once: true });
                    } else {
                        window.location.reload();
                    }
                } else {
                    throw new Error(data.message || 'Error uploading banner');
                }
            } catch (error) {
                console.error('Upload error:', error);
                showToast('Error: ' + (error.message || 'Failed to update profile. Please try again.'), 'error');
            } finally {
                // Reset button state
                if (uploadBtn && uploadText && spinner) {
                    uploadBtn.disabled = false;
                    uploadText.textContent = 'Upload Banner';
                    spinner.classList.add('d-none');
                }
            }
        });
    }

    // Account action handlers
    function handleAccountAction(action, message) {
        return function(event) {
            event.preventDefault();
            accountActionText.textContent = message;
            accountActionInput.value = action;
            const modal = new bootstrap.Modal(document.getElementById('accountActionModal'));
            modal.show();
        };
    }

    if (btnDeactivate) {
        btnDeactivate.addEventListener('click', handleAccountAction('deactivate', 'Are you sure you want to deactivate your account?'));
    }

    if (btnDelete) {
        btnDelete.addEventListener('click', handleAccountAction('delete', 'Are you sure you want to delete your account? This action cannot be undone.'));
    }

    // Account action form submission
    if (accountActionForm) {
        accountActionForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const action = accountActionInput.value;
            const password = document.getElementById('verifyPassword').value;
            
            // Here you would typically make an AJAX call to handle the action
            console.log(`Performing ${action} with password`);
            
            // Close the modal and reset the form
            accountActionModal.hide();
            accountActionForm.reset();
        });
    }
}

// Main DOMContentLoaded event listener
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOMContentLoaded: Initializing profile page...');
    
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Update progress ring animation
    const progressRing = document.querySelector('.progress-ring__circle-fill');
    if (progressRing) {
        const radius = progressRing.r.baseVal.value;
        const circumference = 2 * Math.PI * radius;
        progressRing.style.strokeDasharray = `${circumference} ${circumference}`;
        progressRing.style.transition = 'stroke-dashoffset 1s ease-in-out';
    }

    // Call the initializeProfilePage function
    if (typeof initializeProfilePage === 'function') {
        initializeProfilePage();
    }

    // Social media form submission
    const socialMediaForm = document.getElementById('socialMediaForm');
    if (socialMediaForm) {
        socialMediaForm.onsubmit = async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            
            try {
                // Show loading state
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';
                
                // Add action to form data
                formData.append('action', 'update_social_media');
                
                console.log('Sending form data:', Object.fromEntries(formData));
                
                const response = await fetch('update_profile.php', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();
                
                if (data.success) {
                    // Update the UI with new social data
                    if (data.social) {
                        Object.entries(data.social).forEach(([key, value]) => {
                            const input = document.querySelector(`[name="${key}"]`);
                            if (input) input.value = value;
                        });
                        
                        // Show success message
                        const messageEl = document.getElementById('socialMediaMessage');
                        if (messageEl) {
                            messageEl.textContent = 'Social media links updated successfully!';
                            messageEl.style.display = 'block';
                            
                            // Hide the message after 3 seconds
                            setTimeout(() => {
                                messageEl.style.opacity = '0';
                                setTimeout(() => {
                                    messageEl.style.display = 'none';
                                    messageEl.style.opacity = '1';
                                }, 500);
                            }, 3000);
                        }
                    }
                } else {
                    throw new Error(data.message || 'Failed to update social media links');
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('Error: ' + (error.message || 'Failed to update social media links. Please try again.'), 'error');
            } finally {
                // Reset button state
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                }
            }
        };
    }
});