<?php
// sign_in.php - User Registration
require_once 'includes/Database.php';
require_once 'includes/Auth.php';
require_once 'includes/ActivityLogger.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
    exit;
}

// Get input data (handle both JSON and form data)
$input = [];
if ($_SERVER['CONTENT_TYPE'] === 'application/json') {
    $input = json_decode(file_get_contents('php://input'), true);
} else {
    $input = $_POST;
}

// Sanitize & validate inputs
$username = trim($input['username'] ?? '');
$email = trim($input['email'] ?? '');
$password = $input['password'] ?? '';
$role = $input['role'] ?? 'user';
$admin_acceptance = $input['admin_acceptance'] ?? '';

if (!$username) {
    echo json_encode(['success' => false, 'error' => 'Username is required.']);
    exit;
}
if (!$email) {
    echo json_encode(['success' => false, 'error' => 'A valid email is required.']);
    exit;
}
if (strlen($password) < 6) {
    echo json_encode(['success' => false, 'error' => 'Password must be at least 6 characters.']);
    exit;
}

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    // Debug logging
    error_log("Registration attempt - Role: $role, Username: $username, Email: $email");
    
    // Check for existing email (case-insensitive)
    $stmt = $conn->prepare("SELECT id FROM users WHERE LOWER(email) = LOWER(?)");
    $stmt->execute([$email]);
    $existingUser = $stmt->fetch();
    if ($existingUser) {
        error_log("Email $email already exists in users table (ID: {$existingUser['id']})");
        echo json_encode(['success' => false, 'error' => 'Email already registered.']);
        exit;
    }
    
    $stmt = $conn->prepare("SELECT admin_id FROM admin_users WHERE LOWER(email) = LOWER(?)");
    $stmt->execute([$email]);
    $existingAdmin = $stmt->fetch();
    if ($existingAdmin) {
        error_log("Email $email already exists in admin_users table (ID: {$existingAdmin['admin_id']})");
        echo json_encode(['success' => false, 'error' => 'Email already registered as admin.']);
        exit;
    }
    
    error_log("Email $email is available for registration");
    
    // Check for existing username (case-insensitive)
    $stmt = $conn->prepare("SELECT id FROM users WHERE LOWER(username) = LOWER(?)");
    $stmt->execute([$username]);
    $existingUserByUsername = $stmt->fetch();
    if ($existingUserByUsername) {
        error_log("Username $username already exists in users table");
        echo json_encode(['success' => false, 'error' => 'Username already taken.']);
        exit;
    }
    
    $stmt = $conn->prepare("SELECT admin_id FROM admin_users WHERE LOWER(username) = LOWER(?)");
    $stmt->execute([$username]);
    $existingAdminByUsername = $stmt->fetch();
    if ($existingAdminByUsername) {
        error_log("Username $username already exists in admin_users table");
        echo json_encode(['success' => false, 'error' => 'Username already taken as admin.']);
        exit;
    }
    
    error_log("Username $username is available for registration");
    
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    
    if ($role === 'admin') {
        // Require admin acceptance
        if (!$admin_acceptance) {
            error_log("Admin registration failed: Admin acceptance not provided");
            echo json_encode(['success' => false, 'error' => 'Admin acceptance is required.']);
            exit;
        }
        
        error_log("Attempting to insert admin user: $username, $email");
        
        // Insert into admin_users
        $stmt = $conn->prepare("INSERT INTO admin_users (username, email, password_hash, role, created_at) VALUES (?, ?, ?, 'admin', NOW())");
        $executeResult = $stmt->execute([$username, $email, $passwordHash]);
        
        if (!$executeResult) {
            $errorInfo = $stmt->errorInfo();
            error_log("Failed to insert admin user. Error: " . print_r($errorInfo, true));
            echo json_encode(['success' => false, 'error' => 'Failed to create admin account. Database error.']);
            exit;
        }
        
        $adminId = $conn->lastInsertId();
        error_log("Admin user created successfully with ID: $adminId");
        
        // Log admin registration
        $logger = ActivityLogger::getInstance();
        $logger->logActivity([
            'admin_id' => $adminId,
            'username' => $username,
            'user_type' => 'admin',
            'action' => 'registered',
            'action_details' => 'New admin account created',
            'status' => 'success'
        ]);
        
        // Create system notification
        $logger->createNotification([
            'type' => 'info',
            'title' => 'New Admin Registration',
            'message' => "New admin registered: {$username}",
            'icon' => 'fa-user-shield',
            'related_admin_id' => $adminId
        ]);
        
        echo json_encode(['success' => true, 'message' => 'Admin account created! You can now log in as admin.']);
    } else {
        // Insert into users
        $auth = Auth::getInstance();
        $result = $auth->register($username, $email, $password);
        if ($result['success']) {
            // Get the newly created user ID
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $userId = $stmt->fetchColumn();
            
            // Log user registration
            $logger = ActivityLogger::getInstance();
            $logger->logActivity([
                'user_id' => $userId,
                'username' => $username,
                'user_type' => 'user',
                'action' => 'registered',
                'action_details' => 'New user account created',
                'status' => 'success'
            ]);
            
            // Create system notification
            $logger->createNotification([
                'type' => 'info',
                'title' => 'New User Registration',
                'message' => "New user registered: {$username}",
                'icon' => 'fa-user-plus',
                'related_user_id' => $userId
            ]);
            
            echo json_encode(['success' => true, 'message' => 'Account created successfully!']);
        } else {
            echo json_encode(['success' => false, 'error' => $result['error']]);
        }
    }
    
} catch (Exception $e) {
    error_log("Registration error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Registration failed. Please try again later.']);
}
