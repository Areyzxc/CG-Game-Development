<?php
// Ensure no errors are output
ini_set('display_errors', 0);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../php_errors.log');

// Buffer output to prevent any unwanted output before headers
ob_start();

require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/Auth.php';
require_once __DIR__ . '/../includes/CSRFProtection.php';
require_once __DIR__ . '/../includes/XSSProtection.php';

// Start session first
session_start();

header('Content-Type: application/json');

// Log incoming request for debugging
error_log('Profile update request received');
error_log('POST data: ' . print_r($_POST, true));
error_log('FILES data: ' . print_r($_FILES, true));

// Simple per-user rate limit: max 10 updates per 10 minutes
$bucketKey = 'rate_profile_update';
if (!isset($_SESSION[$bucketKey])) { $_SESSION[$bucketKey] = []; }
$_SESSION[$bucketKey] = array_values(array_filter($_SESSION[$bucketKey], function($t){ return $t > time()-600; }));
if (count($_SESSION[$bucketKey]) >= 10) {
    http_response_code(429);
    echo json_encode(['success' => false, 'error' => 'Too many requests, please try later']);
    exit;
}
$_SESSION[$bucketKey][] = time();

$db = Database::getInstance();
$auth = Auth::getInstance();
$csrf = CSRFProtection::getInstance();
$xss = XSSProtection::getInstance();

if (!$auth->isLoggedIn() || $auth->isAdmin()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if (!$csrf->validateRequest()) {
    error_log('CSRF validation failed for profile update');
    error_log('Session CSRF token: ' . print_r($_SESSION['csrf_token'] ?? 'NOT SET', true));
    error_log('POST csrf_token: ' . ($_POST['csrf_token'] ?? 'NOT SET'));
    error_log('Header X-CSRF-Token: ' . ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? 'NOT SET'));
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid CSRF token. Please refresh the page and try again.']);
    exit;
}

$user = $auth->getCurrentUser();
$userId = $user['id'];

$username = $xss->sanitizeText($_POST['username'] ?? '');
$emailRaw = $_POST['email'] ?? '';
$email = filter_var($emailRaw, FILTER_VALIDATE_EMAIL) ? trim($emailRaw) : '';
$location = $xss->sanitizeText($_POST['location'] ?? '');
$bio = $xss->sanitizeText($_POST['bio'] ?? '');

if (empty($username) || empty($email)) {
    echo json_encode(['success' => false, 'error' => 'Username and valid email are required']);
    exit;
}

// Uniqueness checks
$stmt = $db->prepare('SELECT id FROM users WHERE username = :u AND id <> :id LIMIT 1');
$stmt->execute(['u' => $username, 'id' => $userId]);
if ($stmt->fetch()) {
    echo json_encode(['success' => false, 'error' => 'Username already taken']);
    exit;
}

$stmt = $db->prepare('SELECT id FROM users WHERE email = :e AND id <> :id LIMIT 1');
$stmt->execute(['e' => $email, 'id' => $userId]);
if ($stmt->fetch()) {
    echo json_encode(['success' => false, 'error' => 'Email already in use']);
    exit;
}

$profilePicPath = $user['profile_picture'];

if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
    $targetDir = __DIR__ . '/../uploads/avatars/';
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    $originalName = $xss->sanitizeFilename($_FILES['profile_picture']['name']);
    $fileName = uniqid('', true) . '-' . $originalName;
    $targetFile = $targetDir . $fileName;

    $maxSize = 2 * 1024 * 1024; // 2MB
    $tmpPath = $_FILES['profile_picture']['tmp_name'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = $finfo ? finfo_file($finfo, $tmpPath) : null;
    if ($finfo) { finfo_close($finfo); }
    $allowedMimes = ['image/jpeg', 'image/png', 'image/gif'];
    $imageInfo = @getimagesize($tmpPath);
    if (!in_array($mime, $allowedMimes) || !$imageInfo) {
        echo json_encode(['success' => false, 'error' => 'Invalid image file']);
        exit;
    }
    if ($_FILES['profile_picture']['size'] > $maxSize) {
        echo json_encode(['success' => false, 'error' => 'Image exceeds 2MB limit']);
        exit;
    }
    // Optional: auto-resize to max 512x512 to save space
    if ($imageInfo && function_exists('imagecreatetruecolor')) {
        [$w, $h] = $imageInfo;
        $max = 512;
        $scale = min(1, $max / max($w, $h));
        if ($scale < 1) {
            $newW = (int) floor($w * $scale);
            $newH = (int) floor($h * $scale);
            $src = null; $ext = $mime;
            if ($ext === 'image/jpeg') $src = imagecreatefromjpeg($tmpPath);
            if ($ext === 'image/png') $src = imagecreatefrompng($tmpPath);
            if ($ext === 'image/gif') $src = imagecreatefromgif($tmpPath);
            if ($src) {
                $dst = imagecreatetruecolor($newW, $newH);
                imagealphablending($dst, false);
                imagesavealpha($dst, true);
                imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $w, $h);
                if ($ext === 'image/jpeg') imagejpeg($dst, $tmpPath, 90);
                if ($ext === 'image/png') imagepng($dst, $tmpPath, 6);
                if ($ext === 'image/gif') imagegif($dst, $tmpPath);
                imagedestroy($dst);
                imagedestroy($src);
            }
        }
    }

    if (!move_uploaded_file($tmpPath, $targetFile)) {
        echo json_encode(['success' => false, 'error' => 'Upload failed']);
        exit;
    }

    // Delete old avatar if exists (path relative from project root)
    $old = $user['profile_picture'] ?? '';
    if (!empty($old)) {
        $oldPath = __DIR__ . '/../' . ltrim($old, '/');
        if (file_exists($oldPath)) { @unlink($oldPath); }
    }
    // Save relative path
    $profilePicPath = 'uploads/avatars/' . $fileName;
}

$stmt = $db->prepare('UPDATE users SET username = :u, email = :e, profile_picture = :p, location = :l, bio = :b WHERE id = :id');
if ($stmt->execute(['u' => $username, 'e' => $email, 'p' => $profilePicPath, 'l' => $location, 'b' => $bio, 'id' => $userId])) {
    // Update session with new data
    $_SESSION['user']['username'] = $username;
    $_SESSION['user']['email'] = $email;
    $_SESSION['user']['profile_picture'] = $profilePicPath;
    $_SESSION['user']['location'] = $location;
    $_SESSION['user']['bio'] = $bio;
    
    // Update session data only, no need to update Auth instance
    $_SESSION['user']['username'] = $username;
    $_SESSION['user']['email'] = $email;
    $_SESSION['user']['profile_picture'] = $profilePicPath;
    $_SESSION['user']['location'] = $location;
    $_SESSION['user']['bio'] = $bio;
    
    echo json_encode(['success' => true, 'message' => 'Profile updated', 'profile_picture' => $profilePicPath]);
} else {
    echo json_encode(['success' => false, 'error' => 'Update failed']);
}

?>
