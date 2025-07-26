<?php
/**
 * ==========================================================
 * Endpoint:  api/admin_edit_user.php
 *
 * Description:
 *   - Allows admins to edit a user's or admin's username and profile picture.
 *   - Accepts multipart/form-data POST requests.
 *   - Only accessible to authenticated admins.
 *   - Validates input and checks for existing usernames.
 *
 * Request (POST):
 *   - id: int (user/admin id)
 *   - type: 'user' or 'admin'
 *   - username: string
 *   - profile_picture: file (optional)
 *
 * Response (JSON):
 *   - success: bool
 *   - error: string (if any)
 *   - data: updated user info (optional)
 *
 * Author: CodeGaming Team
 * Last Updated: July 24, 2025
 * ==========================================================
 */
require_once '../includes/Auth.php';
require_once '../includes/Database.php';

header('Content-Type: application/json');

$auth = Auth::getInstance();
if (!$auth->isAdmin()) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
    exit;
}

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$type = isset($_POST['type']) ? $_POST['type'] : '';
$username = isset($_POST['username']) ? trim($_POST['username']) : '';

if (!$id || !in_array($type, ['user', 'admin']) || !$username) {
    echo json_encode(['success' => false, 'error' => 'Missing or invalid parameters.']);
    exit;
}

$db = Database::getInstance();
$table = $type === 'admin' ? 'admins' : 'users';
$idField = $type === 'admin' ? 'admin_id' : 'id';

// Check for username uniqueness (excluding current user)
$stmt = $db->prepare("SELECT COUNT(*) FROM $table WHERE username = ? AND $idField != ?");
$stmt->execute([$username, $id]);
if ($stmt->fetchColumn() > 0) {
    echo json_encode(['success' => false, 'error' => 'Username already taken.']);
    exit;
}

// Handle profile picture upload
$profilePicPath = null;
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $fileType = mime_content_type($_FILES['profile_picture']['tmp_name']);
    if (!in_array($fileType, $allowedTypes)) {
        echo json_encode(['success' => false, 'error' => 'Invalid image type.']);
        exit;
    }
    $ext = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
    $filename = $type . '_' . $id . '_' . time() . '.' . $ext;
    $uploadDir = '../uploads/avatars/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    // Delete old avatar if present and not default
    $stmtOld = $db->prepare("SELECT profile_picture FROM $table WHERE $idField = ?");
    $stmtOld->execute([$id]);
    $oldPic = $stmtOld->fetchColumn();
    if ($oldPic && $oldPic !== 'NULL' && $oldPic !== '' && $oldPic !== null) {
        $oldPath = '../uploads/avatars/' . ltrim($oldPic, '/\\uploads/avatars/');
        if (file_exists($oldPath)) {
            @unlink($oldPath);
        }
    }
    $destPath = $uploadDir . $filename;
    if (!move_uploaded_file($_FILES['profile_picture']['tmp_name'], $destPath)) {
        echo json_encode(['success' => false, 'error' => 'Failed to save profile picture.']);
        exit;
    }
    // Store only the filename in the DB
    $profilePicPath = $filename;
}

// Build update query
$fields = ['username = ?'];
$params = [$username];
if ($profilePicPath) {
    $fields[] = 'profile_picture = ?';
    $params[] = $profilePicPath;
}
$params[] = $id;
$sql = "UPDATE $table SET " . implode(', ', $fields) . " WHERE $idField = ?";
$stmt = $db->prepare($sql);
if (!$stmt->execute($params)) {
    echo json_encode(['success' => false, 'error' => 'Database update failed.']);
    exit;
}

// Return updated user info

$stmt = $db->prepare("SELECT $idField AS id, username, email, profile_picture, created_at FROM $table WHERE $idField = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    echo json_encode(['success' => true, 'data' => $user]);
} else {
    echo json_encode(['success' => false, 'error' => 'User not found after update.']);
}
