<?php
/**
 * ==========================================================
 * File: admin_announcements.php
 * 
 * Description:
 *   - Admin Announcements management page for Code Gaming platform
 *   - Features:
 *       • Add, edit, filter, and search announcements
 *       • Category and status filters (system, event, update; published, draft)
 *       • Announcement cards list and pagination
 *       • Sidebar with recent, stats, and pinned/featured announcements
 *       • Modal for add/edit, pin limit, and back-to-top button
 *       • Responsive, modern UI with Bootstrap and custom styles
 * 
 * Usage:
 *   - Accessible only to logged-in admins
 *   - Used to manage platform announcements and featured posts
 * 
 * Files Included:
 *   - assets/css/admin_dashboard.css
 *   - assets/css/admin_announcements.css
 *   - assets/js/admin_global.js
 *   - assets/js/admin_announcements.js
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
$pageTitle = 'Admin Announcements';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.4/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/admin_dashboard.css">
    <link rel="stylesheet" href="assets/css/admin_announcements.css">
</head>
<body class="admin-theme">
<?php include 'includes/admin_header.php'; ?>
<main class="admin-announcements-main container-fluid py-4">
    <div class="row g-4">
        <!-- Main Content -->
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="magazine-title mb-0">ANNOUNCEMENTS</h1>
                <button class="btn btn-warning btn-lg" id="addAnnouncementBtn"><i class="fas fa-plus me-2"></i>Add Announcement</button>
            </div>
            <!-- Filters/Search -->
            <div class="d-flex flex-wrap gap-2 mb-3 align-items-center">
                <input type="text" class="form-control" id="announcementSearch" placeholder="Search announcements..." style="max-width:260px;">
                <select class="form-select" id="announcementStatusFilter" style="max-width:180px;">
                    <option value="">All Statuses</option>
                    <option value="published">Published</option>
                    <option value="draft">Draft</option>
                </select>
                <select class="form-select" id="announcementCategoryFilter" style="max-width:180px;">
                    <option value="">All Categories</option>
                    <option value="system">System</option>
                    <option value="event">Event</option>
                    <option value="update">Update</option>
                </select>
            </div>
            <!-- Announcement Cards List -->
            <div id="announcementCardsList"></div>
            <!-- Pagination -->
            <nav id="announcementPagination" class="mt-4"></nav>
        </div>
        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="magazine-sidebar">
                <div class="sidebar-section mb-4">
                    <h5 class="sidebar-title"><i class="fas fa-bolt me-2"></i>Recent Announcements</h5>
                    <ul class="list-group" id="recentAnnouncementsList"></ul>
                </div>
                <div class="sidebar-section mb-4">
                    <h5 class="sidebar-title"><i class="fas fa-chart-bar me-2"></i>Stats</h5>
                    <div id="announcementStats"></div>
                </div>
                <div class="sidebar-section mb-4">
                    <h5 class="sidebar-title"><i class="fas fa-thumbtack me-2"></i>Pinned/Featured</h5>
                    <div id="featuredAnnouncement"></div>
                </div>
                <div class="sidebar-section">
                    <button class="btn btn-success w-100" id="shareOnTwitterBtn"><i class="fab fa-twitter me-2"></i>Share on Twitter</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Add/Edit Announcement Modal (hidden by default) -->
    <div class="modal fade" id="announcementModal" tabindex="-1" aria-labelledby="announcementModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="announcementModalLabel">Add/Edit Announcement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="announcementForm">
                        <input type="hidden" id="announcementId">
                        <div class="mb-3">
                            <label for="announcementTitle" class="form-label">Title</label>
                            <input type="text" class="form-control" id="announcementTitle" required>
                        </div>
                        <div class="mb-3">
                            <label for="announcementContent" class="form-label">Content</label>
                            <textarea class="form-control" id="announcementContent" rows="5" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="announcementCategory" class="form-label">Category</label>
                            <select class="form-select" id="announcementCategory">
                                <option value="system">System</option>
                                <option value="event">Event</option>
                                <option value="update">Update</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="announcementStatus" class="form-label">Status</label>
                            <select class="form-select" id="announcementStatus">
                                <option value="published">Published</option>
                                <option value="draft">Draft</option>
                            </select>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Max Pins Modal -->
    <div class="modal fade" id="maxPinsModal" tabindex="-1" aria-labelledby="maxPinsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="maxPinsModalLabel"><i class="fas fa-thumbtack text-warning me-2"></i>Pin Limit Reached</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>You can only have <b>3 pinned announcements</b> at a time.<br>Unpin another announcement before pinning a new one.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Understood</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Back to Top Button -->
    <button id="backToTopBtn" class="btn btn-dark rounded-circle" style="position:fixed;bottom:32px;right:32px;display:none;z-index:9999;"><i class="fas fa-arrow-up"></i></button>
</main>
<?php include 'includes/admin_footer.php'; ?>
<script src="assets/js/admin_global.js"></script>
<script src="assets/js/admin_announcements.js"></script>
</body>
</html> 
