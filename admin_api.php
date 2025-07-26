<?php
/**
 * ==========================================================
 * File: admin_api.php
 * 
 * Description:
 *   - Admin API endpoint for Code Gaming platform
 *   - Features:
 *       • Handles dashboard stats, user management, and announcements via RESTful actions
 *       • Supports GET, POST, PUT, DELETE methods with CORS headers
 *       • Authenticates admin users for all actions except login
 *       • Returns JSON responses for all API calls
 *       • Includes helper functions for database operations
 * 
 * Usage:
 *   - Accessible only to authenticated admins
 *   - Used by admin dashboard and management tools for AJAX/API requests
 * 
 * Files Included:
 *   - db_connection.php
 * 
 * Author: [Santiago]
 * Last Updated: [July 22, 2025]
 * -- Code Gaming Team --
 * ==========================================================
 */
session_start();
require_once 'db_connection.php';

// Check if user is logged in and is an admin
function checkAdminAuth() {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized access']);
        exit;
    }
}

// Handle CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Get the action from the request
$action = $_GET['action'] ?? '';

// Check authentication for all actions except login
if ($action !== 'login') {
    checkAdminAuth();
}

try {
    switch ($action) {
        case 'dashboard':
            // Fetch dashboard statistics
            $stats = [
                'totalUsers' => getTotalUsers(),
                'activeUsers' => getActiveUsers(),
                'totalContent' => getTotalContent(),
                'recentActivity' => getRecentActivity(),
                'notifications' => getSystemNotifications()
            ];
            echo json_encode($stats);
            break;

        case 'createAnnouncement':
            // Create new announcement
            $title = $_POST['title'] ?? '';
            $content = $_POST['content'] ?? '';
            $priority = $_POST['priority'] ?? 'medium';
            
            if (empty($title) || empty($content)) {
                throw new Exception('Title and content are required');
            }
            
            $result = createAnnouncement($title, $content, $priority);
            echo json_encode(['success' => true, 'message' => 'Announcement created successfully']);
            break;

        case 'getUsers':
            // Get paginated user list
            $page = $_GET['page'] ?? 1;
            $limit = $_GET['limit'] ?? 10;
            $users = getUsers($page, $limit);
            echo json_encode($users);
            break;

        case 'updateUser':
            // Update user details
            $userId = $_POST['user_id'] ?? null;
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$userId) {
                throw new Exception('User ID is required');
            }
            
            $result = updateUser($userId, $data);
            echo json_encode(['success' => true, 'message' => 'User updated successfully']);
            break;

        case 'deleteUser':
            // Delete user
            $userId = $_POST['user_id'] ?? null;
            
            if (!$userId) {
                throw new Exception('User ID is required');
            }
            
            $result = deleteUser($userId);
            echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
            break;

        default:
            throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}

// Database Functions
function getTotalUsers() {
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM users");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total'];
}

function getActiveUsers() {
    global $conn;
    $stmt = $conn->prepare("
        SELECT COUNT(*) as total 
        FROM users 
        WHERE last_activity > DATE_SUB(NOW(), INTERVAL 5 MINUTE)
    ");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total'];
}

function getTotalContent() {
    global $conn;
    $stmt = $conn->prepare("
        SELECT 
            (SELECT COUNT(*) FROM tutorials) +
            (SELECT COUNT(*) FROM quizzes) +
            (SELECT COUNT(*) FROM challenges) as total
    ");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total'];
}

function getRecentActivity() {
    global $conn;
    $stmt = $conn->prepare("
        SELECT 
            u.username as userName,
            u.avatar as userAvatar,
            a.action,
            a.timestamp,
            a.status
        FROM user_activity a
        JOIN users u ON a.user_id = u.id
        ORDER BY a.timestamp DESC
        LIMIT 10
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getSystemNotifications() {
    global $conn;
    $stmt = $conn->prepare("
        SELECT 
            title,
            message,
            type,
            priority,
            created_at as timestamp
        FROM system_notifications
        ORDER BY created_at DESC
        LIMIT 5
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function createAnnouncement($title, $content, $priority) {
    global $conn;
    $stmt = $conn->prepare("
        INSERT INTO announcements (title, content, priority, created_by)
        VALUES (?, ?, ?, ?)
    ");
    return $stmt->execute([$title, $content, $priority, $_SESSION['user_id']]);
}

function getUsers($page, $limit) {
    global $conn;
    $offset = ($page - 1) * $limit;
    
    $stmt = $conn->prepare("
        SELECT 
            id,
            username,
            email,
            created_at,
            last_login,
            status
        FROM users
        ORDER BY created_at DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->execute([$limit, $offset]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function updateUser($userId, $data) {
    global $conn;
    $allowedFields = ['username', 'email', 'status', 'role'];
    $updates = [];
    $values = [];
    
    foreach ($data as $field => $value) {
        if (in_array($field, $allowedFields)) {
            $updates[] = "$field = ?";
            $values[] = $value;
        }
    }
    
    if (empty($updates)) {
        throw new Exception('No valid fields to update');
    }
    
    $values[] = $userId;
    $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?";
    $stmt = $conn->prepare($sql);
    return $stmt->execute($values);
}

function deleteUser($userId) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    return $stmt->execute([$userId]);
}
?> 