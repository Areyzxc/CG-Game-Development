// Profile page client logic: AJAX update, avatar preview, and feedback
(function() {
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('#editProfileModal form');
        if (!form) return;

            // Social Media Modal close on save
            const socialForm = document.getElementById('socialMediaForm');
            const socialModalEl = document.getElementById('socialMediaModal');
            if (socialForm && socialModalEl) {
                socialForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const submitBtn = socialForm.querySelector('button[type="submit"]');
                    if (submitBtn) submitBtn.disabled = true;
                    try {
                        // Simulate AJAX save (replace with real API call if needed)
                        await new Promise(res => setTimeout(res, 500));
                        bootstrap.Modal.getOrCreateInstance(socialModalEl).hide();
                    } catch (err) {
                        alert('Failed to save social media links.');
                    } finally {
                        if (submitBtn) submitBtn.disabled = false;
                    }
                });
            }

        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';
        console.log('CSRF Token loaded:', csrfToken ? 'Yes (length: ' + csrfToken.length + ')' : 'No');
        console.log('CSRF Token value:', csrfToken);
        
        if (!csrfToken) {
            console.error('CRITICAL: No CSRF token found! Check if meta tag exists in page header.');
            alert('Error: CSRF token not found. Please refresh the page.');
        }

        const avatarImg = document.querySelector('.profile-avatar');
        const navbarAvatar = document.getElementById('userAvatar');
        const usernameDisplay = document.getElementById('usernameDisplay');

        const usernameInput = document.getElementById('username');
        const emailInput = document.getElementById('email');
        const fileInput = document.getElementById('profile_picture');

        // Load real stats and recent activity
        fetch('api/profile-stats.php', { credentials: 'same-origin' })
            .then(r => r.json()).then(j => {
                if (j && j.success && j.data) {
                    const s = j.data;
                    const pointsEl = document.querySelector('.stats-card h5:nth-of-type(1)');
                    const rankEl = document.querySelector('.stats-card h5:nth-of-type(2)');
                    const chalEl = document.querySelector('.stats-card h5:nth-of-type(3)');
                    const quizEl = document.querySelector('.stats-card h5:nth-of-type(4)');
                    if (pointsEl) pointsEl.textContent = s.points;
                    if (rankEl) rankEl.textContent = s.rank;
                    if (chalEl) chalEl.textContent = s.challenges_completed;
                    if (quizEl) quizEl.textContent = s.quizzes_passed;
                    computeCompleteness();
                }
            }).catch(() => {});

        fetch('api/recent-activity.php', { credentials: 'same-origin' })
            .then(r => r.json()).then(j => {
                if (!j || !j.success) return;
                const list = document.querySelector('.activity-card ul');
                if (!list) return;
                list.innerHTML = '';
                function truncate(text, n=80) {
                    if (!text) return text;
                    const s = String(text);
                    return s.length > n ? s.slice(0, n - 1) + '…' : s;
                }
                j.data.forEach(item => {
                    const li = document.createElement('li');
                    li.className = 'list-group-item d-flex justify-content-between align-items-center';
                    let name = 'Activity';
                    if (item.type === 'quiz') name = 'Quiz';
                    if (item.type === 'challenge') name = 'Challenge';
                    if (item.type === 'achievement') name = 'Achievement';
                    if (item.type === 'tutorial') name = 'Tutorial Completed';
                    if (item.type === 'mini_game') name = `Mini-Game`;
                    const success = item.success ? 'success' : 'secondary';
                    const right = document.createElement('span');
                    right.className = `badge bg-${success}`;
                    right.textContent = new Date(item.at).toLocaleDateString();
                    const rawLabel = item.label ? item.label : item.ref_id;
                    const label = rawLabel ? ` • ${truncate(rawLabel, 80)}` : '';
                    const left = document.createElement('div');
                    left.className = 'd-flex align-items-center gap-2';
                    const icon = document.createElement('i');
                    // Boxicons icon per type
                    if (item.type === 'quiz') icon.className = 'bx bx-help-circle text-info';
                    else if (item.type === 'challenge') icon.className = 'bx bx-trophy text-warning';
                    else if (item.type === 'achievement') icon.className = 'bx bx-medal text-success';
                    else if (item.type === 'tutorial') icon.className = 'bx bx-book text-primary';
                    else if (item.type === 'mini_game') icon.className = 'bx bx-joystick text-danger';
                    else icon.className = 'bx bx-time';
                    const text = document.createElement('span');
                    text.textContent = `${name}${label}`;
                    left.appendChild(icon);
                    left.appendChild(text);
                    li.innerHTML = '';
                    li.appendChild(left);
                    li.appendChild(right);
                    list.appendChild(li);
                });
            }).catch(() => {});

        // Live preview for avatar
        if (fileInput) {
            fileInput.addEventListener('change', function() {
                const file = this.files && this.files[0];
                if (!file) return;
                const url = URL.createObjectURL(file);
                if (avatarImg) avatarImg.src = url;
                if (navbarAvatar) navbarAvatar.src = url;
            });
        }

        function showAlert(message, type = 'info') {
            // type: 'success' | 'danger' | 'warning' | 'info'
            const container = document.querySelector('.profile-page .container') || document.body;
            const alert = document.createElement('div');
            alert.className = `alert alert-${type} alert-dismissible fade show`;
            alert.setAttribute('role', 'alert');
            alert.setAttribute('aria-live', 'polite');
            alert.innerHTML = `${message}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>`;
            container.prepend(alert);
            setTimeout(() => {
                try { new bootstrap.Alert(alert).close(); } catch (_) {}
            }, 5000);
        }

        form.addEventListener('submit', async function(event) {
            // Use AJAX; keep default as fallback if fetch fails early
            event.preventDefault();
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerText = 'Saving...';
            }

            try {
                const formData = new FormData(form);
                // Add CSRF token to FormData if not already present
                if (!formData.has('csrf_token') && csrfToken) {
                    formData.append('csrf_token', csrfToken);
                }
                
                console.log('Submitting profile update with CSRF token:', csrfToken.substring(0, 10) + '...');
                console.log('FormData has csrf_token:', formData.has('csrf_token'));
                
                // Ensure CSRF header present for API
                const response = await fetch('api/profile-update.php', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-Token': csrfToken
                    },
                    body: formData
                });

                let result;
                try {
                    const text = await response.text();
                    console.log('Raw server response:', text); // Debug the raw response
                    
                    // Check if response starts with <?php, <br>, or other HTML
                    if (text.trim().startsWith('<')) {
                        console.error('Server returned HTML instead of JSON:', text);
                        showAlert('Server configuration error. Please contact support.', 'danger');
                        return;
                    }
                    
                    result = JSON.parse(text);
                } catch (err) {
                    console.error('Response parsing error:', err);
                    console.error('Raw response was:', text); // Show the problematic response
                    showAlert('Server response error. Please try again.', 'danger');
                    return;
                }

                if (!response.ok || !result.success) {
                    showAlert(result.error || 'Failed to update profile', 'danger');
                    return;
                }

                // Update UI (name/email/avatar)
                if (usernameDisplay && usernameInput) {
                    usernameDisplay.textContent = usernameInput.value;
                }
                if (result.profile_picture) {
                    const newSrc = result.profile_picture;
                    if (avatarImg) avatarImg.src = newSrc + `?t=${Date.now()}`;
                    if (navbarAvatar) navbarAvatar.src = newSrc + `?t=${Date.now()}`;
                }

                showAlert(result.message || 'Profile updated', 'success');

                // Close modal and reload page to show all updates
                const modalEl = document.getElementById('editProfileModal');
                if (modalEl) {
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) {
                        // Wait for modal to finish hiding before reloading
                        modalEl.addEventListener('hidden.bs.modal', function() {
                            setTimeout(() => window.location.reload(), 100);
                        }, { once: true });
                        modal.hide();
                    } else {
                        // Fallback if modal instance not found
                        window.location.reload();
                    }
                }
            } catch (err) {
                showAlert('Network error. Please try again.', 'danger');
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerText = 'Save changes';
                }
            }
        });

        // Profile completeness ring/checklist
        function computeCompleteness() {
            const checklist = {
                setup: true, // logged in
                photo: !!(avatarImg && avatarImg.src && !avatarImg.src.includes('PTC.png')),
                personal: !!(usernameInput && usernameInput.value && emailInput && emailInput.value),
                location: !!document.getElementById('location') && document.getElementById('location').value,
                bio: !!document.getElementById('bio') && document.getElementById('bio').value,
                notifications: true // placeholder
            };
            const weights = { setup: 0.1, photo: 0.05, personal: 0.1, location: 0.2, bio: 0.15, notifications: 0.1, bank: 0.3 };
            const percent = Math.round((checklist.setup*weights.setup + checklist.photo*weights.photo + checklist.personal*weights.personal + (checklist.location?1:0)*weights.location + (checklist.bio?1:0)*weights.bio + checklist.notifications*weights.notifications) * 100);
            const ring = document.getElementById('profileProgress');
            const label = document.getElementById('profileProgressLabel');
            if (ring) {
                const circumference = 283; // ~2πr for r=45
                const offset = circumference - (percent / 100) * circumference;
                ring.style.strokeDasharray = `${circumference}`;
                ring.style.strokeDashoffset = `${offset}`;
            }
            if (label) label.textContent = percent + '%';
            const list = document.getElementById('profileChecklist');
            if (list) {
                list.querySelector('[data-item="photo"]').classList.toggle('text-success', checklist.photo);
                list.querySelector('[data-item="personal"]').classList.toggle('text-success', checklist.personal);
                if (list.querySelector('[data-item="location"]')) list.querySelector('[data-item="location"]').classList.toggle('text-success', !!checklist.location);
                if (list.querySelector('[data-item="bio"]')) list.querySelector('[data-item="bio"]').classList.toggle('text-success', !!checklist.bio);
            }
        }

        computeCompleteness();

        // Optional: password change handler if form exists
        const pwdForm = document.getElementById('changePasswordForm');
        if (pwdForm) {
            pwdForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                const submit = pwdForm.querySelector('button[type="submit"]');
                if (submit) { submit.disabled = true; submit.innerText = 'Updating...'; }
                try {
                    const data = new FormData(pwdForm);
                    const res = await fetch('api/change-password.php', {
                        method: 'POST',
                        headers: { 'X-CSRF-Token': csrfToken },
                        body: data
                    });
                    const json = await res.json();
                    if (!res.ok || !json.success) {
                        showAlert(json.error || 'Password change failed', 'danger');
                    } else {
                        showAlert('Password updated successfully', 'success');
                        const modalEl = document.getElementById('changePasswordModal');
                        if (modalEl) bootstrap.Modal.getOrCreateInstance(modalEl).hide();
                        pwdForm.reset();
                    }
                } catch (_) {
                    showAlert('Network error. Please try again.', 'danger');
                } finally {
                    if (submit) { submit.disabled = false; submit.innerText = 'Update Password'; }
                }
            });
        }

        // Account deactivate/delete wiring
        const actionModalEl = document.getElementById('accountActionModal');
        const actionForm = document.getElementById('accountActionForm');
        const actionInput = document.getElementById('accountActionInput');
        const actionText = document.getElementById('accountActionText');
        const btnDeactivate = document.getElementById('btnDeactivate');
        const btnDelete = document.getElementById('btnDelete');
        const csrfTokenHeader = csrfToken;

        function openAction(action, text) {
            actionInput.value = action;
            actionText.textContent = text;
            bootstrap.Modal.getOrCreateInstance(actionModalEl).show();
        }

        if (btnDeactivate) btnDeactivate.addEventListener('click', () => openAction('deactivate', 'Deactivate your account? You can reactivate by logging in later.'));
        if (btnDelete) btnDelete.addEventListener('click', () => openAction('delete', 'Permanently delete your account? This action cannot be undone.'));

        if (actionForm) {
            actionForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                const submit = document.getElementById('accountActionSubmit');
                if (submit) { submit.disabled = true; submit.textContent = 'Processing...'; }
                try {
                    const res = await fetch('api/account-action.php', {
                        method: 'POST',
                        headers: { 'X-CSRF-Token': csrfTokenHeader },
                        body: new FormData(actionForm)
                    });
                    const json = await res.json();
                    if (!res.ok || !json.success) {
                        showAlert(json.error || 'Failed to process request', 'danger');
                    } else {
                        showAlert(json.message || 'Done', 'success');
                        bootstrap.Modal.getOrCreateInstance(actionModalEl).hide();
                        if (actionInput.value === 'delete') {
                            setTimeout(() => { window.location.href = 'logout.php'; }, 1500);
                        }
                    }
                } catch (_) {
                    showAlert('Network error. Please try again.', 'danger');
                } finally {
                    if (submit) { submit.disabled = false; submit.textContent = 'Confirm'; }
                }
            });
        }
    });
})();

