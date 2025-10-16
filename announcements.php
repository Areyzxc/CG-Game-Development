<?php
/**
 * ==========================================================
 * File: announcements.php
 * 
 * Description:
 *   - Announcements page for Code Gaming platform
 *   - Features:
 *       • Displays all active announcements (latest and pinned first)
 *       • Shows author, date, and announcement details
 *       • Pagination for large announcement lists
 *       • Responsive, themed UI
 * 
 * Usage:
 *   - Public page for all users and visitors
 *   - Keeps users updated on news, features, and events
 * 
 * Files Included:
 *   - assets/css/announcements.css
 *   - assets/js/announcements.js
 *   - includes/header.php, includes/footer.php
 * 
 * Author: [Santiago]
 * Last Updated: [July 22, 2025]
 * -- Code Gaming Team --
 * ==========================================================
 */

require_once 'includes/Database.php';
require_once 'includes/Auth.php';

$db = Database::getInstance();
$auth = Auth::getInstance();

// Fetch all active announcements, most recent first
$conn = $db->getConnection();
$stmt = $conn->prepare('SELECT a.*, au.username as author FROM announcements a LEFT JOIN admin_users au ON a.created_by = au.admin_id WHERE a.is_active = 1 ORDER BY a.created_at DESC');
$stmt->execute();
$announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Set page title for the header
$pageTitle = "Announcements";
?>

<?php include 'includes/header.php'; ?>

<body class="announcements-theme">
  <main>
    <div class="announcements-container" id="announcementsContainer">
      <h1 style="text-align:center;margin-bottom:2rem;">Latest Announcements</h1>
      <ul class="announcement-list" id="announcementList"></ul>
      <div class="announcements-pagination" id="announcementsPagination"></div>
    </div>
  </main>
  <?php include 'includes/footer.php'; ?>
  <link rel="stylesheet" href="assets/css/announcements.css">
  <script src="assets/js/announcements.js"></script>
</body>
