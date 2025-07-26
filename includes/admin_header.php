<?php
/**
 * File: includes/admin_header.php
 * 
 * This file contains the header for the admin panel.
 * 
 * It includes:
 *   - Bootstrap for styling
 *   - Font Awesome for icons
 *   - Provides a consistent header layout for the admin dashboard.
 *   - Handles user authentication and displays user-specific information.
 *   - Clock functionality for the admin dashboard.
 * Purpose:
 *   - Display copyright information
 * Usage:
 *   - Included in admin pages to provide a consistent header.
 * 
 * Dependencies:
 *   - Auth.php for authentication  
 *   - Database.php for database access
 * 
 * Author: CodeGaming Team
 * Last Updated: July 26, 2025
 */
require_once 'Auth.php';

$auth = Auth::getInstance();
$currentUser = $auth->getCurrentUser();
$currentRole = $auth->getCurrentRole();
$username = $currentUser['username'] ?? $_SESSION['username'] ?? 'Admin';
$profilePicture = !empty($currentUser['profile_picture']) ? htmlspecialchars($currentUser['profile_picture']) : 'images/PTC.png';
?>

<!-- Main Navigation & Sidebar -->
<header class="admin-header">
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <!-- Logo -->
            <a class="navbar-brand" href="admin_dashboard.php"><i class="fas fa-code text-accent"></i> Admin Panel</a>
            
            <!-- Mobile Sidebar Toggler -->
            <button class="navbar-toggler" type="button" id="sidebarToggler">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Desktop Navigation -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin_announcements.php"></i>Announcements</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin_users.php">Users</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Content</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Analytics</a></li>
                </ul>
                <div class="d-flex align-items-center ms-3">
                    <span class="vr mx-3 d-none d-lg-block" style="height:32px; opacity:0.2;"></span>
                    <!-- Digital Clock with Date (Header) -->
                    <div id="admin-digital-clock-container" class="me-3 text-end">
                        <div id="admin-digital-clock" style="font-size:1.1rem; font-weight:bold; color:#222;"></div>
                        <div id="admin-digital-date" style="font-size:0.95rem; color:#555;"></div>
                    </div>
                    <span class="vr mx-2 d-none d-lg-block" style="height:32px; opacity:0.2;"></span>
                    <a href="#" class="nav-link nav-icon px-2">
                        <i class="fas fa-bell"></i>
                    </a>
                    <span class="vr mx-2 d-none d-lg-block" style="height:32px; opacity:0.2;"></span>
                    <div class="dropdown">
                        <a href="#" class="nav-link nav-icon d-flex align-items-center" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="<?php echo htmlspecialchars($profilePicture); ?>" alt="User" class="rounded-circle" width="30" height="30">
                            <span class="ms-2 fw-bold d-none d-lg-inline"><?php echo htmlspecialchars($username); ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end p-0" aria-labelledby="userDropdown" style="min-width:220px;">
                            <li class="bg-dark text-light p-3 text-center" style="border-bottom:1px solid #222;">
                                <img src="<?php echo htmlspecialchars($profilePicture); ?>" alt="Profile" class="rounded-circle mb-2" width="56" height="56">
                                <div class="fw-bold" style="font-size:1.1rem;"><?php echo htmlspecialchars($username); ?></div>
                                <div class="text-muted small"><?php echo htmlspecialchars($currentUser['email'] ?? ''); ?></div>
                            </li>
                            <li><a class="dropdown-item d-flex align-items-center" href="admin_profile.php"><i class="fas fa-user me-2"></i>My Profile</a></li>
                            <li><a class="dropdown-item d-flex align-items-center" href="admin_settings.php"><i class="fas fa-cog me-2"></i>Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item d-flex align-items-center text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>

<!-- Mobile Sidebar -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>
<div class="sidebar-container" id="adminSidebar">
    <div class="d-flex justify-content-end p-2">
        <button class="btn-close" id="closeSidebar"></button>
    </div>
    <!-- Digital Clock with Date (Sidebar) -->
    <div id="admin-digital-clock-sidebar" class="mb-3" style="text-align:center;">
        <div id="admin-digital-clock-sb" style="font-size:1.1rem; font-weight:bold; color:#222;"></div>
        <div id="admin-digital-date-sb" style="font-size:0.95rem; color:#555;"></div>
    </div>
    <!-- Admin Profile in Sidebar -->
    <div class="sidebar-profile mb-3 text-center">
        <img src="<?php echo htmlspecialchars($profilePicture); ?>" alt="Profile" class="rounded-circle mb-2" width="56" height="56">
        <div class="fw-bold" style="font-size:1.1rem; color:#222;"><?php echo htmlspecialchars($username); ?></div>
        <div class="text-muted small"><?php echo htmlspecialchars($currentUser['email'] ?? ''); ?></div>
    </div>
    <ul class="list-unstyled">
        <li><a href="admin_dashboard.php"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
        <li><a href="admin_announcements.php"><i class="fas fa-bullhorn me-2"></i>Announcements</a></li>
        <li><a href="admin_users.php"><i class="fas fa-users me-2"></i>Users</a></li>
        <li><a href="admin_content.php"><i class="fas fa-pencil-alt me-2"></i>Content</a></li>
        <li><a href="admin_analytics.php"><i class="fas fa-chart-bar me-2"></i>Analytics</a></li>
        <li><hr></li>
        <li><a href="profile.php"><i class="fas fa-user-circle me-2"></i>Profile</a></li>
        <li><a href="logout.php" class="text-danger"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
    </ul>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('adminSidebar');
    const overlay = document.querySelector('.sidebar-overlay');

    if (sidebarToggle && sidebar && overlay) {
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        });

        overlay.addEventListener('click', () => {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        });
    }
});

// Digital Clock Drawing
function updateAdminDigitalClock() {
    const now = new Date();
    let hours = now.getHours();
    const minutes = now.getMinutes().toString().padStart(2, '0');
    const seconds = now.getSeconds().toString().padStart(2, '0');
    const ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12; // the hour '0' should be '12'
    const timeStr = `${hours}:${minutes}:${seconds} ${ampm}`;
    const dateStr = now.toLocaleDateString(undefined, { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' });
    // Header
    const clock = document.getElementById('admin-digital-clock');
    const date = document.getElementById('admin-digital-date');
    if (clock) clock.textContent = timeStr;
    if (date) date.textContent = dateStr;
    // Sidebar
    const clockSb = document.getElementById('admin-digital-clock-sb');
    const dateSb = document.getElementById('admin-digital-date-sb');
    if (clockSb) clockSb.textContent = timeStr;
    if (dateSb) dateSb.textContent = dateStr;
}
setInterval(updateAdminDigitalClock, 1000);
document.addEventListener('DOMContentLoaded', updateAdminDigitalClock);
</script> 