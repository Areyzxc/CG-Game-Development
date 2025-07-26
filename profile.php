<?php
/**
 * ==========================================================
 * File: profile.php
 * 
 * Description:
 *   - User Profile page for Code Gaming platform
 *   - Features:
 *       • View and edit user profile details (username, email, avatar)
 *       • Upload and validate profile picture (JPG, PNG, GIF, max 2MB)
 *       • Display player stats (points, rank, challenges, quizzes)
 *       • Show recent activity and achievements
 *       • Modal-based profile editing with Bootstrap
 *       • Responsive and modern UI
 *       • Note: Further enhancements can be made to improve user experience
 * 
 * Usage:
 *   - Accessible to logged-in, non-admin users
 *   - Allows users to manage their account and view progress
 * 
 * Author: [Santiago]
 * Last Updated: [July 22, 2025]
 * -- Code Gaming Team --
 * ==========================================================
 */

// Include required files
require_once 'includes/Database.php';
require_once 'includes/Auth.php';

// Initialize core components
$db = Database::getInstance();
$auth = Auth::getInstance();

if (!$auth->isLoggedIn() || $auth->isAdmin()) {
    header('Location: login.php');
    exit;
}

$currentUser = $auth->getCurrentUser();
$userId = $currentUser['id'];

$pageTitle = "My Profile";
$additionalStyles = '<link rel="stylesheet" href="assets/css/profile-style.css">'; // You might need to create this file

// Handle Profile Update
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    // Basic validation
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    
    // Check if username or email is taken by another user
    // ... (add validation logic here) ...

    // Handle file upload
    $profilePicPath = $currentUser['profile_picture'];
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $targetDir = "uploads/avatars/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        $fileName = uniqid() . '-' . basename($_FILES["profile_picture"]["name"]);
        $targetFile = $targetDir . $fileName;
        
        // Basic image validation (type, size)
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        if (in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif']) && $_FILES['profile_picture']['size'] < 2000000) {
            if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $targetFile)) {
                $profilePicPath = $targetFile;
            } else {
                $message = "Error uploading file.";
            }
        } else {
            $message = "Invalid file type or size (Max 2MB: jpg, png, jpeg, gif).";
        }
    }

    if (empty($message)) {
        $stmt = $db->prepare("UPDATE users SET username = :username, email = :email, profile_picture = :pic WHERE id = :id");
        if ($stmt->execute(['username' => $username, 'email' => $email, 'pic' => $profilePicPath, 'id' => $userId])) {
            $message = "Profile updated successfully!";
            // Refresh user data in session
            $auth->refreshUserSession();
            $currentUser = $auth->getCurrentUser();
        } else {
            $message = "Failed to update profile.";
        }
    }
}

// Fetch user stats (placeholders)
$stats = [
    'points' => 1250,
    'rank' => '#42',
    'challenges_completed' => 15,
    'quizzes_passed' => 28
];
?>

<?php include 'includes/header.php'; ?>

<main class="container py-5 profile-page">
    <?php if ($message): ?>
        <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <div class="row">
        <!-- Left Column: Profile Card -->
        <div class="col-lg-4">
            <div class="card profile-card text-center">
                <div class="card-body">
                    <img src="<?php echo !empty($currentUser['profile_picture']) ? htmlspecialchars($currentUser['profile_picture']) : 'images/PTC.png'; ?>" 
                         alt="Profile Picture" class="profile-avatar mb-3">
                    <h4 class="card-title"><?php echo htmlspecialchars($currentUser['username']); ?></h4>
                    <p class="text-muted"><?php echo htmlspecialchars($currentUser['email']); ?></p>
                    <p class="text-muted small">Joined: <?php echo date('M d, Y', strtotime($currentUser['created_at'])); ?></p>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                        <i class="fas fa-edit me-2"></i>Edit Profile
                    </button>
                </div>
            </div>
        </div>

        <!-- Right Column: Stats and Activity -->
        <div class="col-lg-8">
            <!-- Stats -->
            <div class="card stats-card mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">Player Stats</h5>
                    <div class="row text-center">
                        <div class="col-md-3 col-6 mb-3">
                            <i class="fas fa-star fa-2x text-warning mb-2"></i>
                            <h5><?php echo $stats['points']; ?></h5>
                            <p class="text-muted mb-0">Points</p>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <i class="fas fa-trophy fa-2x text-success mb-2"></i>
                            <h5><?php echo $stats['rank']; ?></h5>
                            <p class="text-muted mb-0">Rank</p>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <i class="fas fa-flag-checkered fa-2x text-info mb-2"></i>
                            <h5><?php echo $stats['challenges_completed']; ?></h5>
                            <p class="text-muted mb-0">Challenges</p>
                        </div>
                         <div class="col-md-3 col-6 mb-3">
                            <i class="fas fa-check-circle fa-2x text-primary mb-2"></i>
                            <h5><?php echo $stats['quizzes_passed']; ?></h5>
                            <p class="text-muted mb-0">Quizzes</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity (Placeholder) -->
            <div class="card activity-card">
                 <div class="card-body">
                    <h5 class="card-title mb-3">Recent Activity</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Completed Challenge: "Array Basics"
                            <span class="badge bg-success">1 day ago</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Passed Quiz: "HTML Fundamentals"
                            <span class="badge bg-primary">2 days ago</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Unlocked Achievement: "First Code"
                            <span class="badge bg-warning">4 days ago</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-light">
            <form action="profile.php" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">Edit Your Profile</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($currentUser['username']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($currentUser['email']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="profile_picture" class="form-label">Profile Picture (Optional)</label>
                        <input class="form-control" type="file" id="profile_picture" name="profile_picture">
                        <small class="form-text text-muted">Max 2MB. JPG, PNG, GIF.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="update_profile" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>