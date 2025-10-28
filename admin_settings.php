<?php
/**
 * File: admin_settings.php
 * Purpose: Admin settings and preferences page for CodeGaming
 * Features:
 *   - Manage notification preferences
 *   - Configure dashboard settings
 *   - Set privacy options
 *   - Manage session settings
 * Author: CodeGaming Team
 * Last Updated: October 21, 2025
 */

session_start();
require_once 'includes/Auth.php';
require_once 'includes/Database.php';

// Set page title for the header
$pageTitle = "Admin Settings";

$auth = Auth::getInstance();
if (!$auth->isAdmin()) {
    header('Location: login.php');
    exit;
}

$db = Database::getInstance();
$currentUser = $auth->getCurrentUser();
$userId = $currentUser['admin_id'] ?? $currentUser['id'];

// Fetch admin settings (you can create a settings table or use JSON in admin_users)
$stmt = $db->prepare("SELECT username, email, role FROM admin_users WHERE admin_id = ?");
$stmt->execute([$userId]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.4/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/admin_dashboard.css">
    <link rel="stylesheet" href="assets/css/admin_profile.css">
</head>
<body class="admin-theme">

<?php include 'includes/admin_header.php'; ?>

<main class="admin-main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-cog me-2"></i>Settings</h1>
            <a href="admin_dashboard.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
            </a>
        </div>

        <div class="row">
            <div class="col-lg-3 mb-4">
                <!-- Settings Navigation -->
                <div class="settings-card">
                    <div class="list-group list-group-flush">
                        <a href="#notifications" class="list-group-item list-group-item-action active" data-section="notifications">
                            <i class="fas fa-bell me-2"></i>Notifications
                        </a>
                        <a href="#dashboard" class="list-group-item list-group-item-action" data-section="dashboard">
                            <i class="fas fa-th-large me-2"></i>Dashboard
                        </a>
                        <a href="#privacy" class="list-group-item list-group-item-action" data-section="privacy">
                            <i class="fas fa-shield-alt me-2"></i>Privacy & Security
                        </a>
                        <a href="#appearance" class="list-group-item list-group-item-action" data-section="appearance">
                            <i class="fas fa-palette me-2"></i>Appearance
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-9">
                <!-- Notifications Settings -->
                <div class="settings-card settings-section-content" id="notifications-section">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-bell me-2"></i>Notification Preferences</h5>
                    </div>
                    <div class="card-body">
                        <div class="settings-section">
                            <h6>Email Notifications</h6>
                            
                            <div class="setting-item">
                                <div class="setting-label">
                                    <div class="label-title">New User Registrations</div>
                                    <div class="label-description">Receive email when a new user registers</div>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="emailNewUsers" checked>
                                </div>
                            </div>

                            <div class="setting-item">
                                <div class="setting-label">
                                    <div class="label-title">User Reports</div>
                                    <div class="label-description">Get notified about user-submitted reports</div>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="emailReports" checked>
                                </div>
                            </div>

                            <div class="setting-item">
                                <div class="setting-label">
                                    <div class="label-title">System Alerts</div>
                                    <div class="label-description">Critical system notifications and errors</div>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="emailAlerts" checked>
                                </div>
                            </div>
                        </div>

                        <div class="settings-section">
                            <h6>In-App Notifications</h6>
                            
                            <div class="setting-item">
                                <div class="setting-label">
                                    <div class="label-title">Desktop Notifications</div>
                                    <div class="label-description">Show browser notifications for important events</div>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="desktopNotif">
                                </div>
                            </div>

                            <div class="setting-item">
                                <div class="setting-label">
                                    <div class="label-title">Sound Alerts</div>
                                    <div class="label-description">Play sound for new notifications</div>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="soundAlerts">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            <button class="btn btn-primary" id="saveNotificationSettings">
                                <i class="fas fa-save me-1"></i>Save Preferences
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Dashboard Settings -->
                <div class="settings-card settings-section-content d-none" id="dashboard-section">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-th-large me-2"></i>Dashboard Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="settings-section">
                            <h6>Display Options</h6>
                            
                            <div class="setting-item">
                                <div class="setting-label">
                                    <div class="label-title">Auto-refresh Dashboard</div>
                                    <div class="label-description">Automatically refresh stats every 5 minutes</div>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="autoRefresh" checked>
                                </div>
                            </div>

                            <div class="setting-item">
                                <div class="setting-label">
                                    <div class="label-title">Show Visitor Stats</div>
                                    <div class="label-description">Display visitor statistics on dashboard</div>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="showVisitorStats" checked>
                                </div>
                            </div>

                            <div class="setting-item">
                                <div class="setting-label">
                                    <div class="label-title">Compact View</div>
                                    <div class="label-description">Use compact layout for tables and cards</div>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="compactView">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            <button class="btn btn-primary" id="saveDashboardSettings">
                                <i class="fas fa-save me-1"></i>Save Settings
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Privacy & Security Settings -->
                <div class="settings-card settings-section-content d-none" id="privacy-section">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-shield-alt me-2"></i>Privacy & Security</h5>
                    </div>
                    <div class="card-body">
                        <div class="settings-section">
                            <h6>Session Management</h6>
                            
                            <div class="setting-item">
                                <div class="setting-label">
                                    <div class="label-title">Session Timeout</div>
                                    <div class="label-description">Automatically log out after inactivity</div>
                                </div>
                                <select class="form-select" style="max-width: 200px;" id="sessionTimeout">
                                    <option value="15">15 minutes</option>
                                    <option value="30" selected>30 minutes</option>
                                    <option value="60">1 hour</option>
                                    <option value="120">2 hours</option>
                                    <option value="0">Never</option>
                                </select>
                            </div>

                            <div class="setting-item">
                                <div class="setting-label">
                                    <div class="label-title">Two-Factor Authentication</div>
                                    <div class="label-description">Add extra security to your account</div>
                                </div>
                                <button class="btn btn-sm btn-outline-primary" id="enable2FA">
                                    <i class="fas fa-lock me-1"></i>Enable 2FA
                                </button>
                            </div>
                        </div>

                        <div class="settings-section">
                            <h6>Activity Log</h6>
                            
                            <div class="setting-item">
                                <div class="setting-label">
                                    <div class="label-title">Log Admin Actions</div>
                                    <div class="label-description">Track all administrative actions</div>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="logActions" checked disabled>
                                </div>
                            </div>

                            <div class="setting-item">
                                <div class="setting-label">
                                    <div class="label-title">View Activity Log</div>
                                    <div class="label-description">See your recent admin activities</div>
                                </div>
                                <a href="admin_activity_log.php" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-history me-1"></i>View Log
                                </a>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            <button class="btn btn-primary" id="savePrivacySettings">
                                <i class="fas fa-save me-1"></i>Save Settings
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Appearance Settings -->
                <div class="settings-card settings-section-content d-none" id="appearance-section">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-palette me-2"></i>Appearance</h5>
                    </div>
                    <div class="card-body">
                        <div class="settings-section">
                            <h6>Theme</h6>
                            
                            <div class="setting-item">
                                <div class="setting-label">
                                    <div class="label-title">Color Scheme</div>
                                    <div class="label-description">Choose your preferred theme</div>
                                </div>
                                <select class="form-select" style="max-width: 200px;" id="colorScheme">
                                    <option value="light" selected>Light</option>
                                    <option value="dark">Dark</option>
                                    <option value="auto">Auto (System)</option>
                                </select>
                            </div>

                            <div class="setting-item">
                                <div class="setting-label">
                                    <div class="label-title">Accent Color</div>
                                    <div class="label-description">Primary color for buttons and links</div>
                                </div>
                                <input type="color" class="form-control form-control-color" id="accentColor" value="#667eea" style="width: 60px;">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            <button class="btn btn-primary" id="saveAppearanceSettings">
                                <i class="fas fa-save me-1"></i>Save Settings
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/admin_footer.php'; ?>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.4/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JS -->
<script>
// Initialize Bootstrap components
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all dropdowns
    var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
    var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
        return new bootstrap.Dropdown(dropdownToggleEl, {
            autoClose: true,
            boundary: 'clippingParents'
        });
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.matches('.dropdown-toggle') && !event.target.closest('.dropdown-menu')) {
            dropdownList.forEach(function(dropdown) {
                if (dropdown._menu.classList.contains('show')) {
                    dropdown.hide();
                }
            });
        }
    });
});
</script>
<script src="assets/js/admin_global.js"></script>
<script src="assets/js/admin_settings.js" defer></script>

</body>
</html>
