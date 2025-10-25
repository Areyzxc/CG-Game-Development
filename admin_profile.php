<?php
/**
 * File: admin_profile.php
 * Purpose: Admin profile management page for CodeGaming
 * Features:
 *   - View and edit admin profile information
 *   - Update profile picture
 *   - Change password
 *   - View account statistics
 * Author: CodeGaming Team
 * Last Updated: October 21, 2025
 */

session_start();
require_once 'includes/Auth.php';
require_once 'includes/Database.php';

$auth = Auth::getInstance();
if (!$auth->isAdmin()) {
    header('Location: login.php');
    exit;
}
// Set page title for the header
$pageTitle = "Admin Profile";

$db = Database::getInstance();
$currentUser = $auth->getCurrentUser();
$userId = $currentUser['admin_id'] ?? $currentUser['id'];

// Fetch admin details
$stmt = $db->prepare("SELECT admin_id, username, email, role, profile_picture, created_at, last_seen FROM admin_users WHERE admin_id = ?");
$stmt->execute([$userId]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$admin) {
    header('Location: admin_dashboard.php');
    exit;
}

// Get admin statistics
$stmt = $db->prepare("SELECT COUNT(*) as total_actions FROM admin_actions_log WHERE admin_id = ?");
$stmt->execute([$userId]);
$stats = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Admin</title>
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
            <h1><i class="fas fa-user-circle me-2"></i>My Profile</h1>
            <a href="admin_dashboard.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
            </a>
        </div>

        <div class="row">
            <!-- Profile Card -->
            <div class="col-lg-4 mb-4">
                <div class="profile-card">
                    <div class="profile-header">
                        <div class="profile-avatar-container">
                            <img id="profileAvatar" 
                                 src="<?php echo $admin['profile_picture'] ? 'uploads/avatars/' . htmlspecialchars($admin['profile_picture']) : 'assets/images/PTC.png'; ?>" 
                                 alt="Profile Picture" 
                                 class="profile-avatar">
                            <button class="avatar-edit-btn" id="changeAvatarBtn">
                                <i class="fas fa-camera"></i>
                            </button>
                            <input type="file" id="avatarInput" accept="image/*" style="display: none;">
                        </div>
                        <h3 class="profile-name"><?php echo htmlspecialchars($admin['username']); ?></h3>
                        <p class="profile-role">
                            <span class="badge bg-primary"><?php echo htmlspecialchars($admin['role']); ?></span>
                        </p>
                    </div>
                    
                    <div class="profile-stats">
                        <div class="stat-item">
                            <i class="fas fa-calendar-alt"></i>
                            <div>
                                <div class="stat-label">Member Since</div>
                                <div class="stat-value"><?php echo date('M d, Y', strtotime($admin['created_at'])); ?></div>
                            </div>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-clock"></i>
                            <div>
                                <div class="stat-label">Last Seen</div>
                                <div class="stat-value"><?php echo $admin['last_seen'] ? date('M d, Y H:i', strtotime($admin['last_seen'])) : 'Never'; ?></div>
                            </div>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-tasks"></i>
                            <div>
                                <div class="stat-label">Total Actions</div>
                                <div class="stat-value"><?php echo number_format($stats['total_actions'] ?? 0); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Information -->
            <div class="col-lg-8 mb-4">
                <div class="admin-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Profile Information</h5>
                        <button class="btn btn-sm btn-primary" id="editProfileBtn">
                            <i class="fas fa-edit me-1"></i>Edit Profile
                        </button>
                    </div>
                    <div class="card-body">
                        <form id="profileForm">
                            <input type="hidden" name="admin_id" value="<?php echo $admin['admin_id']; ?>">
                            
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Username</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="username" name="username" 
                                           value="<?php echo htmlspecialchars($admin['username']); ?>" disabled>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Email</label>
                                <div class="col-sm-9">
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($admin['email']); ?>" disabled>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Role</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($admin['role']); ?>" disabled>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Admin ID</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" value="#<?php echo $admin['admin_id']; ?>" disabled>
                                </div>
                            </div>

                            <div class="d-none" id="saveButtonContainer">
                                <hr>
                                <div class="d-flex gap-2 justify-content-end">
                                    <button type="button" class="btn btn-secondary" id="cancelEditBtn">Cancel</button>
                                    <button type="submit" class="btn btn-primary" id="saveProfileBtn">
                                        <i class="fas fa-save me-1"></i>Save Changes
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Change Password Card -->
                <div class="admin-card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-lock me-2"></i>Change Password</h5>
                    </div>
                    <div class="card-body">
                        <form id="passwordForm">
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Current Password</label>
                                <div class="col-sm-9">
                                    <input type="password" class="form-control" id="currentPassword" name="current_password" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">New Password</label>
                                <div class="col-sm-9">
                                    <input type="password" class="form-control" id="newPassword" name="new_password" required>
                                    <small class="text-muted">Minimum 8 characters</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Confirm Password</label>
                                <div class="col-sm-9">
                                    <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-key me-1"></i>Update Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/admin_footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.4/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/admin_global.js"></script>
<script src="assets/js/admin_profile.js"></script>

</body>
</html>
