<?php
/**
 * File: api/admin_update_avatar.php
 * Purpose: Update admin profile picture
 * Author: CodeGaming Team
 * Last Updated: October 21, 2025
 */

session_start();
require_once '../includes/Auth.php';
require_once '../includes/Database.php';

header('Content-Type: application/json');

$auth = Auth::getInstance();
if (!$auth->isAdmin()) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

$adminId = $_POST['admin_id'] ?? null;

if (!$adminId || !isset($_FILES['profile_picture'])) {
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit;
}

$file = $_FILES['profile_picture'];

// Validate file
if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'error' => 'File upload error']);
    exit;
}

$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$fileType = mime_content_type($file['tmp_name']);

if (!in_array($fileType, $allowedTypes)) {
    echo json_encode(['success' => false, 'error' => 'Invalid file type. Only JPG, PNG, GIF, and WEBP allowed']);
    exit;
}

// Check file size (max 5MB)
if ($file['size'] > 5 * 1024 * 1024) {
    echo json_encode(['success' => false, 'error' => 'File size must be less than 5MB']);
    exit;
}

$db = Database::getInstance();

// Get old profile picture
$stmt = $db->prepare("SELECT profile_picture FROM admin_users WHERE admin_id = ?");
$stmt->execute([$adminId]);
$oldPic = $stmt->fetchColumn();

// Generate unique filename
$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = 'admin_' . $adminId . '_' . time() . '.' . $ext;
$uploadDir = '../uploads/avatars/';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$destPath = $uploadDir . $filename;

// Delete old avatar if exists
if ($oldPic && $oldPic !== 'NULL' && file_exists($uploadDir . $oldPic)) {
    @unlink($uploadDir . $oldPic);
}

// Upload new avatar
if (move_uploaded_file($file['tmp_name'], $destPath)) {
    // Update database
    $stmt = $db->prepare("UPDATE admin_users SET profile_picture = ? WHERE admin_id = ?");
    if ($stmt->execute([$filename, $adminId])) {
        // Update session
        $_SESSION['profile_picture'] = $filename;
        
        echo json_encode([
            'success' => true,
            'message' => 'Profile picture updated successfully',
            'data' => [
                'profile_picture' => $filename
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to update database']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to upload file']);
}
