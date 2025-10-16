<?php
/**
 * File: includes/XSSProtection.php
 * Purpose: XSS (Cross-Site Scripting) protection utility for CodeGaming.
 * Features:
 *   - Sanitizes user input to prevent XSS attacks
 *   - Provides safe output methods
 *   - Validates and cleans HTML content
 *   - Handles different content types (HTML, text, JSON)
 * Usage:
 *   - Use before displaying user input
 *   - Sanitize form data before processing
 *   - Clean content before database storage
 * Included Files/Dependencies:
 *   - PHP built-in functions
 * Author: CodeGaming Team
 * Last Updated: July 26, 2025
 */

class XSSProtection {
    private static $instance = null;
    
    // Allowed HTML tags for rich content
    private $allowedTags = '<p><br><strong><b><em><i><u><ul><ol><li><h1><h2><h3><h4><h5><h6><blockquote><code><pre><a><img>';
    
    // Allowed attributes for HTML tags
    private $allowedAttributes = [
        'a' => ['href', 'title', 'target'],
        'img' => ['src', 'alt', 'title', 'width', 'height'],
        'blockquote' => ['cite'],
        'code' => ['class'],
        'pre' => ['class']
    ];
    
    private function __construct() {}
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Sanitize text input (removes all HTML)
     */
    public function sanitizeText($input) {
        if (is_null($input)) {
            return null;
        }
        
        // Remove all HTML tags
        $cleaned = strip_tags($input);
        
        // Decode HTML entities
        $cleaned = html_entity_decode($cleaned, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // Remove any remaining HTML entities
        $cleaned = htmlspecialchars($cleaned, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        return trim($cleaned);
    }
    
    /**
     * Sanitize HTML input (allows safe HTML tags)
     */
    public function sanitizeHtml($input) {
        if (is_null($input)) {
            return null;
        }
        
        // Strip dangerous tags and attributes
        $cleaned = strip_tags($input, $this->allowedTags);
        
        // Remove dangerous attributes
        $cleaned = $this->removeDangerousAttributes($cleaned);
        
        // Decode HTML entities
        $cleaned = html_entity_decode($cleaned, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // Re-encode to prevent XSS
        $cleaned = htmlspecialchars($cleaned, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        return trim($cleaned);
    }
    
    /**
     * Sanitize for JSON output
     */
    public function sanitizeJson($input) {
        if (is_null($input)) {
            return null;
        }
        
        // Convert to string if not already
        $input = (string) $input;
        
        // Remove null bytes
        $input = str_replace("\0", '', $input);
        
        // Remove control characters except newlines and tabs
        $input = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $input);
        
        return trim($input);
    }
    
    /**
     * Sanitize filename for uploads
     */
    public function sanitizeFilename($filename) {
        if (is_null($filename)) {
            return null;
        }
        
        // Remove path traversal attempts
        $filename = basename($filename);
        
        // Remove dangerous characters
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
        
        // Remove multiple dots
        $filename = preg_replace('/\.{2,}/', '.', $filename);
        
        // Ensure it doesn't start with a dot
        $filename = ltrim($filename, '.');
        
        return $filename;
    }
    
    /**
     * Sanitize URL
     */
    public function sanitizeUrl($url) {
        if (is_null($url)) {
            return null;
        }
        
        // Remove dangerous protocols
        $url = preg_replace('/^(javascript|data|vbscript):/i', '', $url);
        
        // Validate URL format
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }
        
        // Only allow http and https protocols
        $parsed = parse_url($url);
        if (!in_array($parsed['scheme'] ?? '', ['http', 'https'])) {
            return null;
        }
        
        return $url;
    }
    
    /**
     * Remove dangerous attributes from HTML
     */
    private function removeDangerousAttributes($html) {
        // Remove script and event attributes
        $html = preg_replace('/\s*on\w+\s*=\s*["\'][^"\']*["\']/', '', $html);
        $html = preg_replace('/\s*on\w+\s*=\s*[^>\s]+/', '', $html);
        
        // Remove javascript: and data: URLs
        $html = preg_replace('/href\s*=\s*["\']javascript:[^"\']*["\']/', 'href="#"', $html);
        $html = preg_replace('/src\s*=\s*["\']data:[^"\']*["\']/', 'src=""', $html);
        
        return $html;
    }
    
    /**
     * Validate and sanitize array of data
     */
    public function sanitizeArray($data, $type = 'text') {
        if (!is_array($data)) {
            return $this->sanitizeText($data);
        }
        
        $sanitized = [];
        foreach ($data as $key => $value) {
            $cleanKey = $this->sanitizeText($key);
            if (is_array($value)) {
                $sanitized[$cleanKey] = $this->sanitizeArray($value, $type);
            } else {
                switch ($type) {
                    case 'html':
                        $sanitized[$cleanKey] = $this->sanitizeHtml($value);
                        break;
                    case 'json':
                        $sanitized[$cleanKey] = $this->sanitizeJson($value);
                        break;
                    case 'url':
                        $sanitized[$cleanKey] = $this->sanitizeUrl($value);
                        break;
                    case 'filename':
                        $sanitized[$cleanKey] = $this->sanitizeFilename($value);
                        break;
                    default:
                        $sanitized[$cleanKey] = $this->sanitizeText($value);
                        break;
                }
            }
        }
        
        return $sanitized;
    }
    
    /**
     * Safe output for HTML (escapes special characters)
     */
    public function outputHtml($input) {
        return htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
    
    /**
     * Safe output for JSON
     */
    public function outputJson($input) {
        return json_encode($input, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
    }
    
    /**
     * Check if input contains potential XSS
     */
    public function detectXSS($input) {
        if (is_null($input)) {
            return false;
        }
        
        $patterns = [
            '/<script[^>]*>.*?<\/script>/is',
            '/javascript:/i',
            '/vbscript:/i',
            '/data:text\/html/i',
            '/on\w+\s*=/i',
            '/<iframe[^>]*>.*?<\/iframe>/is',
            '/<object[^>]*>.*?<\/object>/is',
            '/<embed[^>]*>.*?<\/embed>/is',
            '/<link[^>]*>.*?<\/link>/is',
            '/<meta[^>]*>.*?<\/meta>/is'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Log potential XSS attempt
     */
    public function logXSSAttempt($input, $context = '') {
        $logMessage = sprintf(
            "[%s] Potential XSS attempt detected in %s: %s",
            date('Y-m-d H:i:s'),
            $context ?: 'unknown context',
            substr($input, 0, 200)
        );
        
        error_log($logMessage);
        
        // Log to database if available
        try {
            $db = Database::getInstance();
            $db->logError('Potential XSS attempt', [
                'context' => $context,
                'input' => substr($input, 0, 500),
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ]);
        } catch (Exception $e) {
            // If database logging fails, just log to error log
            error_log("Failed to log XSS attempt to database: " . $e->getMessage());
        }
    }
}
?>
