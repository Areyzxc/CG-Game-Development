<?php
// Admin Footer
/**
 * Admin Footer for CodeGaming Admin Panel
 * Provides footer section with links and copyright information.
 * 
 * Included in all admin pages to maintain consistent footer design.
 * 
 * Dependencies:
 *   - Bootstrap for styling
 *   - Font Awesome for icons
 * 
 * Purpose:
 *   - Display copyright information 
 * * Usage:
 *   - Included in admin pages to provide a consistent footer.
 * 
 * Author: CodeGaming Team
 * Last Updated: July 24, 2025
 * 
 */
?>
<!-- Admin Footer -->
<footer class="admin-footer mt-5">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-6">
        <p class="mb-0">
          <i class="fas fa-code me-2"></i>
          Code Gaming Admin Panel &copy; <?php echo date('Y'); ?>
        </p>
      </div>
      <div class="col-md-6 text-end">
        <a href="admin_dashboard.php" class="me-3">Dashboard</a>
        <a href="admin_users.php" class="me-3">Users</a>
        <a href="admin_profile.php" class="me-3">Profile</a>
        <a href="admin_settings.php">Settings</a>
      </div>
    </div>
  </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.4/dist/js/bootstrap.bundle.min.js"></script>

<!-- Chart.js for charts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- ApexCharts for advanced charts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<!-- Font Awesome JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>

<!-- Admin Notifications -->
<script src="assets/js/admin_notifications.js"></script>
