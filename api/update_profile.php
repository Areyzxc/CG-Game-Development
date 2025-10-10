<?php
require_once '../includes/Auth.php';
require_once '../includes/Database.php';

header('Content-Type: application/json');

$auth = Auth::getInstance();
if (!$auth->isAdmin()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$type = isset($_POST['type']) ? $_POST['type'] : '';
$username = isset($_POST['username']) ? trim($_POST['username']) : '';

if (!$id || !in_array($type, ['user', 'admin']) || !$username) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing or invalid parameters']);
    exit;
}

$db = Database::getInstance();
$table = $type === 'admin' ? 'admin_users' : 'users';
$idField = $type === 'admin' ? 'admin_id' : 'id';

try {
    // Check for username uniqueness (excluding current user)
    $stmt = $db->prepare("SELECT COUNT(*) FROM $table WHERE username = ? AND $idField != ?");
    $stmt->execute([$username, $id]);
    if ($stmt->fetchColumn() > 0) {
        throw new Exception('Username already taken');
    }

    // Handle profile picture upload
    $profilePicPath = null;
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $fileType = mime_content_type($_FILES['profile_picture']['tmp_name']);
        if (!in_array($fileType, $allowedTypes)) {
            throw new Exception('Invalid image type');
        }
        
        $ext = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
        $filename = $type . '_' . $id . '_' . time() . '.' . $ext;
        $uploadDir = '../uploads/avatars/';
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Delete old avatar if exists
        $stmtOld = $db->prepare("SELECT profile_picture FROM $table WHERE $idField = ?");
        $stmtOld->execute([$id]);
        $oldPic = $stmtOld->fetchColumn();
        
        if ($oldPic && $oldPic !== 'NULL' && $oldPic !== '' && $oldPic !== null) {
            $oldPath = $uploadDir . ltrim($oldPic, '/\\');
            if (file_exists($oldPath)) {
                @unlink($oldPath);
            }
        }
        
        $destPath = $uploadDir . $filename;
        if (!move_uploaded_file($_FILES['profile_picture']['tmp_name'], $destPath)) {
            throw new Exception('Failed to save profile picture');
        }
        
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
        throw new Exception('Database update failed');
    }

    // Get updated user info
    $stmt = $db->prepare("SELECT $idField AS id, username, email, profile_picture, created_at FROM $table WHERE $idField = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception('User not found after update');
    }

    // Ensure profile_picture is always a string
    $user['profile_picture'] = $user['profile_picture'] ?? '';

    // Update session if the updated user is the current user
    if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $id) {
        $_SESSION['username'] = $user['username'];
        $_SESSION['profile_picture'] = $user['profile_picture'];
    }

    echo json_encode([
        'success' => true,
        'data' => $user,
        'message' => 'Profile updated successfully'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}