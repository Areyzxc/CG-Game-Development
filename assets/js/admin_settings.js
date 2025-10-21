/**
 * File: admin_settings.js
 * Purpose: Handles admin settings page functionality
 * Author: CodeGaming Team
 * Last Updated: October 21, 2025
 */

document.addEventListener('DOMContentLoaded', function() {
    // Settings navigation
    const settingsNav = document.querySelectorAll('[data-section]');
    const settingsSections = document.querySelectorAll('.settings-section-content');

    settingsNav.forEach(nav => {
        nav.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all nav items
            settingsNav.forEach(item => item.classList.remove('active'));
            
            // Add active class to clicked item
            this.classList.add('active');
            
            // Hide all sections
            settingsSections.forEach(section => section.classList.add('d-none'));
            
            // Show selected section
            const sectionId = this.dataset.section + '-section';
            const targetSection = document.getElementById(sectionId);
            if (targetSection) {
                targetSection.classList.remove('d-none');
            }
        });
    });

    // Load settings from localStorage
    loadSettings();

    // Save Notification Settings
    const saveNotificationBtn = document.getElementById('saveNotificationSettings');
    if (saveNotificationBtn) {
        saveNotificationBtn.addEventListener('click', function() {
            const settings = {
                emailNewUsers: document.getElementById('emailNewUsers').checked,
                emailReports: document.getElementById('emailReports').checked,
                emailAlerts: document.getElementById('emailAlerts').checked,
                desktopNotif: document.getElementById('desktopNotif').checked,
                soundAlerts: document.getElementById('soundAlerts').checked
            };

            saveSettingsToStorage('notifications', settings);
            showToast('Notification preferences saved!', 'success');
        });
    }

    // Save Dashboard Settings
    const saveDashboardBtn = document.getElementById('saveDashboardSettings');
    if (saveDashboardBtn) {
        saveDashboardBtn.addEventListener('click', function() {
            const settings = {
                autoRefresh: document.getElementById('autoRefresh').checked,
                showVisitorStats: document.getElementById('showVisitorStats').checked,
                compactView: document.getElementById('compactView').checked
            };

            saveSettingsToStorage('dashboard', settings);
            showToast('Dashboard settings saved!', 'success');
            
            // Apply compact view if enabled
            if (settings.compactView) {
                document.body.classList.add('compact-view');
            } else {
                document.body.classList.remove('compact-view');
            }
        });
    }

    // Save Privacy Settings
    const savePrivacyBtn = document.getElementById('savePrivacySettings');
    if (savePrivacyBtn) {
        savePrivacyBtn.addEventListener('click', function() {
            const settings = {
                sessionTimeout: document.getElementById('sessionTimeout').value,
                logActions: document.getElementById('logActions').checked
            };

            saveSettingsToStorage('privacy', settings);
            showToast('Privacy settings saved!', 'success');
        });
    }

    // Save Appearance Settings
    const saveAppearanceBtn = document.getElementById('saveAppearanceSettings');
    if (saveAppearanceBtn) {
        saveAppearanceBtn.addEventListener('click', function() {
            const settings = {
                colorScheme: document.getElementById('colorScheme').value,
                accentColor: document.getElementById('accentColor').value
            };

            saveSettingsToStorage('appearance', settings);
            applyTheme(settings.colorScheme);
            applyAccentColor(settings.accentColor);
            showToast('Appearance settings saved!', 'success');
        });
    }

    // Enable 2FA button
    const enable2FABtn = document.getElementById('enable2FA');
    if (enable2FABtn) {
        enable2FABtn.addEventListener('click', function() {
            showToast('Two-factor authentication setup coming soon!', 'info');
        });
    }

    // Desktop notifications permission
    const desktopNotifToggle = document.getElementById('desktopNotif');
    if (desktopNotifToggle) {
        desktopNotifToggle.addEventListener('change', function() {
            if (this.checked) {
                requestNotificationPermission();
            }
        });
    }
});

// Load settings from localStorage
function loadSettings() {
    // Load notification settings
    const notifSettings = getSettingsFromStorage('notifications');
    if (notifSettings) {
        if (document.getElementById('emailNewUsers')) document.getElementById('emailNewUsers').checked = notifSettings.emailNewUsers ?? true;
        if (document.getElementById('emailReports')) document.getElementById('emailReports').checked = notifSettings.emailReports ?? true;
        if (document.getElementById('emailAlerts')) document.getElementById('emailAlerts').checked = notifSettings.emailAlerts ?? true;
        if (document.getElementById('desktopNotif')) document.getElementById('desktopNotif').checked = notifSettings.desktopNotif ?? false;
        if (document.getElementById('soundAlerts')) document.getElementById('soundAlerts').checked = notifSettings.soundAlerts ?? false;
    }

    // Load dashboard settings
    const dashSettings = getSettingsFromStorage('dashboard');
    if (dashSettings) {
        if (document.getElementById('autoRefresh')) document.getElementById('autoRefresh').checked = dashSettings.autoRefresh ?? true;
        if (document.getElementById('showVisitorStats')) document.getElementById('showVisitorStats').checked = dashSettings.showVisitorStats ?? true;
        if (document.getElementById('compactView')) document.getElementById('compactView').checked = dashSettings.compactView ?? false;
        
        if (dashSettings.compactView) {
            document.body.classList.add('compact-view');
        }
    }

    // Load privacy settings
    const privacySettings = getSettingsFromStorage('privacy');
    if (privacySettings) {
        if (document.getElementById('sessionTimeout')) document.getElementById('sessionTimeout').value = privacySettings.sessionTimeout ?? '30';
    }

    // Load appearance settings
    const appearanceSettings = getSettingsFromStorage('appearance');
    if (appearanceSettings) {
        if (document.getElementById('colorScheme')) document.getElementById('colorScheme').value = appearanceSettings.colorScheme ?? 'light';
        if (document.getElementById('accentColor')) document.getElementById('accentColor').value = appearanceSettings.accentColor ?? '#667eea';
        
        applyTheme(appearanceSettings.colorScheme);
        applyAccentColor(appearanceSettings.accentColor);
    }
}

// Save settings to localStorage
function saveSettingsToStorage(category, settings) {
    const key = `admin_settings_${category}`;
    localStorage.setItem(key, JSON.stringify(settings));
}

// Get settings from localStorage
function getSettingsFromStorage(category) {
    const key = `admin_settings_${category}`;
    const data = localStorage.getItem(key);
    return data ? JSON.parse(data) : null;
}

// Apply theme
function applyTheme(scheme) {
    if (scheme === 'dark') {
        document.body.classList.add('dark-theme');
    } else if (scheme === 'auto') {
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        if (prefersDark) {
            document.body.classList.add('dark-theme');
        } else {
            document.body.classList.remove('dark-theme');
        }
    } else {
        document.body.classList.remove('dark-theme');
    }
}

// Apply accent color
function applyAccentColor(color) {
    document.documentElement.style.setProperty('--accent-color', color);
}

// Request notification permission
function requestNotificationPermission() {
    if ('Notification' in window) {
        Notification.requestPermission().then(permission => {
            if (permission === 'granted') {
                showToast('Desktop notifications enabled!', 'success');
                new Notification('CodeGaming Admin', {
                    body: 'You will now receive desktop notifications',
                    icon: 'assets/images/PTC.png'
                });
            } else {
                showToast('Notification permission denied', 'error');
                document.getElementById('desktopNotif').checked = false;
            }
        });
    } else {
        showToast('Your browser does not support notifications', 'error');
        document.getElementById('desktopNotif').checked = false;
    }
}

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
    
    let bgColor = '#28a745';
    if (type === 'error') bgColor = '#dc3545';
    if (type === 'info') bgColor = '#17a2b8';
    if (type === 'warning') bgColor = '#ffc107';
    
    toast.style.background = bgColor;
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
