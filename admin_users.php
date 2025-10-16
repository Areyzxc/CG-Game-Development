<?php
/**
 * ==========================================================
 * File: admin_users.php
 * 
 * Description:
 *   - Admin User Management page for Code Gaming platform
 *   - Features:
 *       • Quick search for users, admins, and content
 *       • Bulk actions: ban, unban, assign badge
 *       • Tables for registered users and administrators
 *       • Retro modal for user details, editing, password reset, activity, and progress
 *       • Recent admin actions log
 *       • Responsive, modern UI with Bootstrap and custom styles
 * 
 * Usage:
 *   - Accessible only to logged-in admins
 *   - Used to manage users, admins, and perform bulk actions
 * 
 * Files Included:
 *   - assets/css/admin_dashboard.css
 *   - assets/css/admin_users.css
 *   - assets/js/admin_global.js
 *   - assets/js/admin_users.js
 *   - includes/admin_header.php, includes/admin_footer.php
 * 
 * Author: [Santiago]
 * Last Updated: [July 22, 2025]
 * -- Code Gaming Team --
 * ==========================================================
 */
require_once 'includes/Auth.php';

$auth = Auth::getInstance();
if (!$auth->isAdmin()) {
    header('Location: home_page.php');
    exit;
}

$currentUser = $auth->getCurrentUser();
$currentRole = $auth->getCurrentRole();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.4/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/admin_dashboard.css">
    <link rel="stylesheet" href="assets/css/admin_users.css">
    <script src="assets/js/admin_users_fixed.js" defer></script>
    
</head>
<body class="admin-theme">

<?php include 'includes/admin_header.php'; ?>

<main class="admin-main-content">
    <div class="container-fluid">
        <h1 class="mb-4">User Management</h1>
        <!-- Quick Search Bar -->
        <div class="admin-quick-search mb-4">
            <form id="adminQuickSearchForm" class="d-flex flex-wrap align-items-center gap-2">
                <input type="text" class="form-control" id="adminQuickSearchInput" placeholder="Search users, admins, or content by username, email, or title..." style="max-width:340px;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search me-1"></i>Search</button>
            </form>
            <div id="adminQuickSearchResults" class="mt-2"></div>
        </div>

        <!-- Bulk Actions Panel -->
        <div class="admin-bulk-actions mb-3 d-flex flex-wrap align-items-center gap-2" id="adminBulkActionsPanel" style="display:none;">
            <span class="fw-bold">Bulk Actions:</span>
            <button class="btn btn-danger btn-sm" id="bulkBanBtn" disabled><i class="fas fa-user-slash me-1"></i>Ban</button>
            <button class="btn btn-success btn-sm" id="bulkUnbanBtn" disabled><i class="fas fa-user-check me-1"></i>Unban</button>
            <button class="btn btn-warning btn-sm" id="bulkBadgeBtn" disabled><i class="fas fa-certificate me-1"></i>Assign Badge</button>
        </div>

        <!-- Players Table -->
        <section class="admin-card mb-5">
            <div class="card-body">
                <h5 class="card-title">Registered Users (Players)</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th class="text-center"><input type="checkbox" id="selectAllUsers"></th>
                                <th class="text-center">Avatar</th>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Joined On</th>
                                <th>Status</th>
                                <th>Last Seen</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody">
                            <!-- User rows will be injected by JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Admins Table -->
        <section class="admin-card">
            <div class="card-body">
                <h5 class="card-title">Administrators</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th class="text-center"><input type="checkbox" id="selectAllAdmins"></th>
                                <th class="text-center">Avatar</th>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Joined On</th>
                                <th>Status</th>
                                <th>Last Seen</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="adminsTableBody">
                            <!-- Admin rows will be injected by JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Recent Admin Actions Log -->
        <section class="admin-card mt-5">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-history me-2 text-secondary"></i>Recent Admin Actions</h5>
                <ul class="list-group list-group-flush" id="adminActionsLog">
                    <li class="list-group-item text-muted">Loading recent actions...</li>
                </ul>
            </div>
        </section>
    </div>
</main>

<!-- Retro User Details Modal -->
<div class="retro-modal-overlay" id="retroModalOverlay">
    <div class="retro-modal" id="retroModal">
        <div class="retro-modal-title-bar">
            <div class="header-left">
                <span class="dot" id="modalCloseButton"></span>
                <span class="dot"></span>
                <span class="dot"></span>
            </div>
            <div class="header-center" id="modalHeaderCenter">
                <span id="modalTitle">@username // file.txt</span>
                <i class="fas fa-edit edit-user-icon" title="Edit User"></i>
            </div>
            <div class="header-right"></div>
        </div>
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
                <img src="assets/images/PTC.png" alt="Profile Picture" id="modalProfilePic">
                <!-- Profile picture upload, hidden unless in edit mode -->
                <input type="file" accept="image/*" id="modalProfilePicInput" class="form-control d-none mt-2" style="max-width:180px;">
            </div>
            <div class="file-details-container">
                <div class="file-info-box">
                    <span>File Name:</span>
                    <p id="modalFileName">user_profile.dat</p>
                </div>
                <div class="file-info-box">
                    <span>Location:</span>
                    <p id="modalLocation">Role / Joined Date</p>
                </div>
                <div class="details-section">
                    <p class="section-title">File Details</p>
                    <p>name: 
                        <span id="modalUsername">Lennon</span>
                        <input type="text" id="modalUsernameInput" class="form-control d-none" value="" maxlength="32" style="width:180px;display:inline-block;vertical-align:middle;">
                    </p>
                    <p>email: <span id="modalEmail">user@example.com</span></p>
                    <p>last seen: <span id="modalLastSeen">Online</span></p>
                    <p>status: <span id="modalStatus" class="status-active">Active</span></p>
                </div>
            </div>
        </div>
        <div class="retro-modal-status-bar">
            <button class="back-button" id="modalBackButton">< Back</button>
            <div class="time-display" id="modalTime">Time: 01:12 PM</div>
            <div class="status-badge" id="modalBadge">User and Proud!!!</div>
        </div>
    </div>
</div>


<?php include 'includes/admin_footer.php'; ?>
<script src="assets/js/admin_global.js"></script>
    <!-- Main admin users functionality is now in admin_users_fixed.js -->
    <script src="assets/js/admin_users.js" defer></script>
</body>
</html>
