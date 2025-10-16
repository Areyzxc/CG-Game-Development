<?php
/**
 * File: includes/Auth.php
 * Purpose: Authentication and session management for CodeGaming.
 * Features:
 *   - Handles user login, registration, and logout.
 *   - Manages user sessions with secure parameters.
 *   - Provides methods for checking user roles (admin/player).
 *   - Implements CSRF protection.
 * Usage:
 *   - Used across various API endpoints and web pages for user authentication.
 * Included Files/Dependencies:
 *   - includes/Database.php
 *   - Uses PHP sessions for managing user state.
 *   - Requires secure session handling to prevent session hijacking.
 * Included in:
 *   - api/admin_unban_user.php
 *   - api/check-guest-nickname.php
 *   - api/guest-session.php
 *   - api/track-visitor.php
 *   - api/topics.php
 * 
 * 
 * Author: CodeGaming Team
 * Last Updated: July 24, 2025
 */
require_once 'Database.php';

class Auth {
    private static $instance = null;
    private $db;
    private $sessionStarted = false;

    // Update last_seen for authenticated users/admins
    private function updateLastSeen($userId, $role) {
        $conn = $this->db->getConnection();
        if ($role === 'admin' || $role === 'super_admin') {
            $stmt = $conn->prepare("UPDATE admin_users SET last_seen = NOW() WHERE admin_id = ?");
        } else {
            $stmt = $conn->prepare("UPDATE users SET last_seen = NOW() WHERE id = ?");
        }
        $stmt->execute([$userId]);
    }

    private function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->initSession();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function initSession() {
        if (!$this->sessionStarted) {
            if (session_status() === PHP_SESSION_NONE) {
                // Set secure session parameters
                $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
                $httponly = true;
                $samesite = 'lax';
                
                session_set_cookie_params([
                    'lifetime' => 0,
                    'path' => '/',
                    'domain' => $_SERVER['HTTP_HOST'],
                    'secure' => $secure,
                    'httponly' => $httponly,
                    'samesite' => $samesite
                ]);
                
                session_start();
                $this->sessionStarted = true;
                if (!isset($_SESSION['last_regeneration']) || 
                    time() - $_SESSION['last_regeneration'] > 1800) { // 30 minutes
                    session_regenerate_id(true);
                    $_SESSION['last_regeneration'] = time();
                }
            }
        }
    }

    public function getUserByEmail($email) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting user by email: " . $e->getMessage());
            return null;
        }
    }

            // Try admin login
            public function getAdminByEmail($email) {
                try {
                    $stmt = $this->db->prepare("SELECT * FROM admin_users WHERE email = ? LIMIT 1");
                    $stmt->execute([$email]);
                    return $stmt->fetch(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    error_log("Error getting admin by email: " . $e->getMessage());
                    return null;
                }
            }

    public function register($username, $email, $password) {
        try {
            // Validate input
            if (empty($username) || empty($email) || empty($password)) {
                return ['success' => false, 'error' => 'All fields are required'];
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return ['success' => false, 'error' => 'Invalid email format'];
            }

            if (strlen($password) < 6) {
                return ['success' => false, 'error' => 'Password must be at least 6 characters'];
            }
            
            // Check for existing user
            if ($this->getUserByEmail($email)) {
                return ['success' => false, 'error' => 'Email already registered'];
            }

            // Create user
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
            $stmt->execute([$username, $email, $passwordHash]);

            return ['success' => true];
        } catch (Exception $e) {
            error_log("Registration failed: username=$username, email=$email, error=" . $e->getMessage());
            return ['success' => false, 'error' => 'Registration failed. Please try again later.'];
        }
    }
    

    public function logout() {
        if (isset($_SESSION['user_id'])) {
            // Log the logout
            $this->db->logLogin(
                $_SESSION['user_id'],
                $_SESSION['role'] ?? 'unknown',
                $_SERVER['REMOTE_ADDR'],
                session_id()
            );
        }

        // Clear session data
        $_SESSION = array();

        // Destroy session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        // Destroy session
        session_destroy();
    }

     public function isLoggedIn() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    public function isAdmin() {
        return isset($_SESSION['role']) && 
            ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'super_admin');
    }

    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }

        $userId = $_SESSION['user_id'];
        $role = $_SESSION['role'] ?? 'player';
        
        try {
            $conn = $this->db;
         // Handle admin users
         if ($role === 'admin' || $role === 'super_admin') {
            $stmt = $conn->prepare("SELECT * FROM admin_users WHERE admin_id = ?");
            $stmt->execute([$userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        // Handle regular users
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Error getting user data: " . $e->getMessage());
        return null;
    }
}

    public function getCurrentRole() {
        return $_SESSION['role'] ?? null;
    }

    private function setUserSession($userId, $role, $username) {
        $_SESSION['user_id'] = $userId;
        $_SESSION['role'] = $role;
        $_SESSION['username'] = $username;
        $_SESSION['last_activity'] = time();
        $_SESSION['last_regeneration'] = time();

        // Log the login
        $this->db->logLogin(
            $userId,
            $role,
            $_SERVER['REMOTE_ADDR'],
            session_id()
        );
    }

    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            header('Location: login.php');
            exit;
        }
    }

    public function requireAdmin() {
        if (!$this->isAdmin()) {
            header('Location: index.php');
            exit;
        }
    }

    public function getCsrfToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public function validateCsrfToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
} 
