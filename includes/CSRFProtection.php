<?php
/**
 * File: includes/CSRFProtection.php
 * Purpose: CSRF (Cross-Site Request Forgery) protection utility for CodeGaming.
 * Features:
 *   - Generates and validates CSRF tokens
 *   - Stores tokens in session with expiration
 *   - Provides methods for form protection
 *   - Handles token regeneration for security
 * Usage:
 *   - Include in forms and API endpoints that modify data
 *   - Validate tokens on POST/PUT/DELETE requests
 * Included Files/Dependencies:
 *   - PHP sessions
 * Author: CodeGaming Team
 * Last Updated: July 26, 2025
 */

class CSRFProtection {
    private static $instance = null;
    private $tokenName = 'csrf_token';
    private $tokenExpiry = 3600; // 1 hour
    
    private function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Generate a new CSRF token
     */
    public function generateToken() {
        $token = bin2hex(random_bytes(32));
        // Store token data in session
        $_SESSION[$this->tokenName] = [
            'token' => $token,
            'expires' => time() + $this->tokenExpiry
        ];
        // Return just the token string for use in forms/headers
        return $token;
    }
    
    /**
     * Get the current CSRF token (generate if not exists or invalid)
     */
    public function getToken() {
        // If no token exists or is invalid, generate a new one
        if (empty($_SESSION[$this->tokenName]) || 
            !is_array($_SESSION[$this->tokenName]) || 
            !isset($_SESSION[$this->tokenName]['token']) ||
            !isset($_SESSION[$this->tokenName]['expires']) ||
            time() > $_SESSION[$this->tokenName]['expires']) {
            
            error_log('Generating new CSRF token');
            return $this->generateToken();
        }
        
        return $_SESSION[$this->tokenName]['token'];
    }
    
    /**
     * Check if current token is valid
     */
    public function hasValidToken() {
        if (!isset($_SESSION[$this->tokenName])) {
            error_log('No CSRF token in session');
            return false;
        }
        
        $tokenData = $_SESSION[$this->tokenName];
        
        // Check if token data is valid
        if (!is_array($tokenData) || !isset($tokenData['token']) || !isset($tokenData['expires'])) {
            error_log('Invalid token format in session');
            $this->clearToken();
            return false;
        }
        
        // Check if token has expired
        if (time() > $tokenData['expires']) {
            error_log('CSRF token has expired');
            $this->clearToken();
            return false;
        }
        
        return true;
    }
    
    /**
     * Regenerate CSRF token
     */
    public function regenerateToken() {
        $this->clearToken();
        return $this->generateToken();
    }
    
    /**
     * Get CSRF token meta tag for HTML head
     */
    public function getTokenMeta() {
        $token = $this->getToken();
        error_log('Generated CSRF token for meta tag: ' . substr($token, 0, 10) . '...');
        return '<meta name="csrf-token" content="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }
    
    /**
     * Clear the current CSRF token
     */
    private function clearToken() {
        unset($_SESSION[$this->tokenName]);
    }
    
    /**
     * Check if request method requires CSRF protection
     */
    public function requiresProtection($method = null) {
        $method = $method ?: $_SERVER['REQUEST_METHOD'];
        return in_array(strtoupper($method), ['POST', 'PUT', 'DELETE', 'PATCH']);
    }

    /**
     * Validate a CSRF token
     */
    public function validateToken($token) {
        error_log('CSRF Validation Attempt - Token: ' . substr($token, 0, 10) . '...');
        
        // If no token provided
        if (empty($token)) {
            error_log('CSRF Validation Failed: No token provided');
            return false;
        }
        
        // If no token in session
        if (!isset($_SESSION[$this->tokenName])) {
            error_log('CSRF Validation Failed: No token in session');
            return false;
        }
        
        // Get token data from session
        $storedToken = $_SESSION[$this->tokenName];
        
        // Check if token data is valid
        if (!is_array($storedToken) || !isset($storedToken['token']) || !isset($storedToken['expires'])) {
            error_log('CSRF Validation Failed: Invalid token format in session');
            $this->clearToken();
            return false;
        }
        
        // Check if token has expired
        if (time() > $storedToken['expires']) {
            error_log('CSRF Validation Failed: Token expired');
            $this->clearToken();
            return false;
        }
        
        // Get token to compare
        $tokenToCompare = $storedToken['token'];
        
        // Compare tokens
        if (!hash_equals($tokenToCompare, $token)) {
            error_log('CSRF Validation Failed: Token mismatch');
            return false;
        }
        
        return true;
    }
    
    /**
     * Validate token from POST data
     */
    private function validatePostToken() {
        $token = $_POST['csrf_token'] ?? $_POST[$this->tokenName] ?? null;
        error_log('validatePostToken - Token from POST: ' . ($token ? substr($token, 0, 10) . '...' : 'NULL'));
        if ($token) {
            return $this->validateToken($token);
        }
        return false;
    }
    
    /**
     * Validate token from GET data
     */
    private function validateGetToken() {
        $token = $_GET['csrf_token'] ?? $_GET[$this->tokenName] ?? null;
        if ($token) {
            return $this->validateToken($token);
        }
        return false;
    }
    
    /**
     * Validate token from HTTP headers
     */
    private function validateHeaderToken() {
        // Try to get token from various header formats
        $token = null;
        
        // Check $_SERVER for the header (Apache/Nginx format)
        if (isset($_SERVER['HTTP_X_CSRF_TOKEN'])) {
            $token = $_SERVER['HTTP_X_CSRF_TOKEN'];
        }
        
        // Fallback to getallheaders if available
        if (!$token && function_exists('getallheaders')) {
            $headers = getallheaders();
            $token = $headers['X-CSRF-Token'] ?? $headers['X-Csrf-Token'] ?? null;
        }
        
        if ($token) {
            return $this->validateToken($token);
        }
        return false;
    }
    
    /**
     * Validate CSRF token for current request
     */
    public function validateRequest() {
        if (!$this->requiresProtection()) {
            return true;
        }
        
        // Try different methods to get the token
        if ($this->validatePostToken()) {
            return true;
        }
        
        if ($this->validateGetToken()) {
            return true;
        }
        
        if ($this->validateHeaderToken()) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Throw exception if CSRF validation fails
     */
    public function requireValidToken() {
        if (!$this->validateRequest()) {
            throw new Exception('CSRF token validation failed');
        }
    }
}
?>
