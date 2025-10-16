<?php
/**
 * File: includes/announcement_banner.php
 * 
 * This file displays the latest announcement banner on the site.
 * 
 * It includes:
 *   - Database.php for database access
 *   - Retrieves the latest active announcement from the database.
 * * Purpose:
 *   - Display the latest announcement to users.
 *   - Allows users to read more about the announcement.
 * * Usage:
 *   - Included in the main layout to show announcements on all pages.
 * 
 * Dependencies:
 *   - Database.php for database access
 * 
 * Author: CodeGaming Team
 * Last Updated: July 26, 2025
 */
require_once __DIR__ . '/Database.php';
$db = Database::getInstance();
$conn = $db->getConnection();
$stmt = $conn->prepare('SELECT a.title, a.content FROM announcements a WHERE a.is_active = 1 ORDER BY a.created_at DESC LIMIT 1');
$stmt->execute();
$latest = $stmt->fetch(PDO::FETCH_ASSOC);
if ($latest): ?>
<div id="announcementBanner" class="alert alert-warning text-dark rounded-0 mb-0 d-flex align-items-center justify-content-between" style="border-bottom:2px solid #ffc107;">
  <div>
    <strong><?php echo htmlspecialchars($latest['title']); ?>:</strong>
    <?php echo nl2br(htmlspecialchars(mb_strimwidth($latest['content'], 0, 100, '...'))); ?>
    <a href="/announcements.php" class="ms-2 fw-bold">Read more</a>
  </div>
  <button type="button" class="btn-close ms-3" aria-label="Close" onclick="dismissAnnouncementBanner()"></button>
</div>
<script>
function dismissAnnouncementBanner() {
  document.getElementById('announcementBanner').style.display = 'none';
  sessionStorage.setItem('announcementBannerDismissed', '1');
}
if (sessionStorage.getItem('announcementBannerDismissed') === '1') {
  document.addEventListener('DOMContentLoaded', function() {
    var banner = document.getElementById('announcementBanner');
    if (banner) banner.style.display = 'none';
  });
}
</script>
<?php endif; ?> 
